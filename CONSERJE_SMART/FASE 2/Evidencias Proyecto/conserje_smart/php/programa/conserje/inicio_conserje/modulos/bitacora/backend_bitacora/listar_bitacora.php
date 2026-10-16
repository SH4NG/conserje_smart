<?php

// TITULO 1 CONFIGURAR RESPUESTA JSON
// establece el formato de salida de este endpoint
header('Content-Type: application/json; charset=utf-8');

// TITULO 2 RESPONDER SOLICITUD
// devuelve una respuesta uniforme y finaliza la ejecucion
function responderListadoBitacora(bool $exito, string $mensaje, int $codigoHttp = 200, ?array $datos = null): void
{
    http_response_code($codigoHttp);

    $respuesta = [
        'exito' => $exito,
        'mensaje' => $mensaje
    ];

    if ($datos !== null) {
        $respuesta['datos'] = $datos;
    }

    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// TITULO 3 VALIDAR METODO Y SESION
// permite listar unicamente a conserjes con un condominio asociado
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    responderListadoBitacora(false, 'Método de solicitud no permitido.', 405);
}

session_start();

$idCondominio = filter_var($_SESSION['id_condominio_fk'] ?? null, FILTER_VALIDATE_INT);

if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje' ||
    $idCondominio === false ||
    $idCondominio < 1
) {
    responderListadoBitacora(false, 'Se requiere una sesión de conserje válida.', 401);
}

// TITULO 4 CONECTAR CON LA BASE DE DATOS
// utiliza la conexión MariaDB configurada para el proyecto
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

if ($mysqli->connect_error) {
    error_log('No se pudo conectar con la base de datos de bitácora: ' . $mysqli->connect_error);
    responderListadoBitacora(false, 'No se pudo conectar con la base de datos.', 500);
}

if (!$mysqli->set_charset('utf8mb4')) {
    error_log('No se pudo establecer utf8mb4 para listar bitácora: ' . $mysqli->error);
    $mysqli->close();
    responderListadoBitacora(false, 'No se pudo preparar la conexión con la base de datos.', 500);
}

// TITULO 5 LISTAR NOVEDADES DEL CONDOMINIO
// filtra en el servidor por el condominio de la sesión y muestra autores de cualquier conserje de ese condominio
$consulta = $mysqli->prepare(
    'SELECT
        b.id_bitacora,
        b.id_usuario_fk,
        b.titulo,
        b.descripcion,
        b.categoria,
        b.tipo_lugar,
        b.lugar,
        b.entidad_relacionada,
        b.fecha_evento,
        b.hora_evento,
        b.etiquetas,
        b.campos_faltantes,
        b.fecha_novedad,
        b.hora_novedad,
        b.observaciones,
        u.nombre_usuario,
        u.apellido_usuario
    FROM bitacora AS b
    INNER JOIN usuario AS u
        ON u.id_usuario = b.id_usuario_fk
        AND u.id_condominio_fk = b.id_condominio_fk
    WHERE b.id_condominio_fk = ?
    ORDER BY b.fecha_novedad DESC, b.hora_novedad DESC, b.id_bitacora DESC'
);

if ($consulta === false) {
    error_log('No se pudo preparar el listado de bitácora: ' . $mysqli->error);
    $mysqli->close();
    responderListadoBitacora(false, 'No se pudo preparar el listado de novedades.', 500);
}

$consulta->bind_param('i', $idCondominio);

if (!$consulta->execute()) {
    error_log('Falló la consulta del listado de bitácora: ' . $consulta->error);
    $consulta->close();
    $mysqli->close();
    responderListadoBitacora(false, 'No se pudieron cargar las novedades.', 500);
}

$resultado = $consulta->get_result();
if ($resultado === false) {
    error_log('No se pudo obtener el resultado del listado de bitácora: ' . $consulta->error);
    $consulta->close();
    $mysqli->close();
    responderListadoBitacora(false, 'No se pudieron leer las novedades.', 500);
}

$novedades = [];

while ($fila = $resultado->fetch_assoc()) {
    try {
        $entidadRelacionada = json_decode($fila['entidad_relacionada'], true, 512, JSON_THROW_ON_ERROR);
        $etiquetas = json_decode($fila['etiquetas'], true, 512, JSON_THROW_ON_ERROR);
        $camposFaltantes = json_decode($fila['campos_faltantes'], true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $error) {
        error_log('Una fila de bitácora contiene listas JSON inválidas.');
        $consulta->close();
        $mysqli->close();
        responderListadoBitacora(false, 'Una novedad guardada contiene datos inválidos.', 500);
    }

    foreach ([$entidadRelacionada, $etiquetas, $camposFaltantes] as $lista) {
        if (!is_array($lista)) {
            error_log('Una fila de bitácora contiene una lista JSON con formato incorrecto.');
            $consulta->close();
            $mysqli->close();
            responderListadoBitacora(false, 'Una novedad guardada contiene datos inválidos.', 500);
        }

        foreach ($lista as $valor) {
            if (!is_string($valor)) {
                error_log('Una fila de bitácora contiene elementos que no son texto.');
                $consulta->close();
                $mysqli->close();
                responderListadoBitacora(false, 'Una novedad guardada contiene datos inválidos.', 500);
            }
        }
    }

    $novedades[] = [
        'id_bitacora' => (int) $fila['id_bitacora'],
        'id_usuario_fk' => (int) $fila['id_usuario_fk'],
        'titulo' => $fila['titulo'],
        'descripcion' => $fila['descripcion'],
        'categoria' => $fila['categoria'],
        'tipo_lugar' => $fila['tipo_lugar'],
        'lugar' => $fila['lugar'],
        'entidad_relacionada' => $entidadRelacionada,
        'fecha_evento' => $fila['fecha_evento'],
        'hora_evento' => $fila['hora_evento'],
        'etiquetas' => $etiquetas,
        'campos_faltantes' => $camposFaltantes,
        'fecha_novedad' => $fila['fecha_novedad'],
        'hora_novedad' => $fila['hora_novedad'],
        'observaciones' => $fila['observaciones'],
        'nombre_conserje' => trim($fila['nombre_usuario'] . ' ' . $fila['apellido_usuario'])
    ];
}

$consulta->close();
$mysqli->close();

responderListadoBitacora(true, 'Novedades cargadas correctamente.', 200, $novedades);