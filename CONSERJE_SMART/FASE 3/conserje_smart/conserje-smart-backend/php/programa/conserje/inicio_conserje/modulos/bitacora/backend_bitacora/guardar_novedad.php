<?php

// TITULO 1 CONFIGURAR RESPUESTA JSON
// establece el formato de respuesta de este endpoint
header('Content-Type: application/json; charset=utf-8');

// TITULO 2 RESPONDER SOLICITUD
// devuelve una respuesta JSON uniforme y finaliza el endpoint
function responderGuardado(bool $exito, string $mensaje, int $codigoHttp = 200, ?array $datos = null): void
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
// restringe el guardado a conserjes autenticados
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderGuardado(false, 'Método de solicitud no permitido.', 405);
}

session_start();

$idUsuario = filter_var($_SESSION['id_usuario'] ?? null, FILTER_VALIDATE_INT);
$idCondominio = filter_var($_SESSION['id_condominio_fk'] ?? null, FILTER_VALIDATE_INT);

if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje' ||
    $idUsuario === false ||
    $idUsuario < 1 ||
    $idCondominio === false ||
    $idCondominio < 1
) {
    responderGuardado(false, 'Se requiere una sesión de conserje válida.', 401);
}

// TITULO 4 RECIBIR Y VALIDAR DATOS
// recibe el objeto de novedad generado y validado durante el análisis
$cuerpoSolicitud = file_get_contents('php://input');

try {
    $solicitud = json_decode($cuerpoSolicitud, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $error) {
    responderGuardado(false, 'La solicitud no contiene un JSON válido.', 400);
}

if (!is_array($solicitud)) {
    responderGuardado(false, 'La solicitud debe contener los datos de la novedad.', 400);
}

$camposObligatorios = [
    'titulo',
    'descripcion',
    'categoria',
    'tipo_lugar',
    'lugar',
    'entidad_relacionada',
    'fecha_evento',
    'hora_evento',
    'etiquetas',
    'campos_faltantes',
    'observaciones',
    'fecha_novedad',
    'hora_novedad'
];

foreach ($camposObligatorios as $campo) {
    if (!array_key_exists($campo, $solicitud)) {
        responderGuardado(false, 'Faltan datos requeridos para guardar la novedad.', 400);
    }
}

foreach (['titulo', 'descripcion', 'fecha_novedad', 'hora_novedad'] as $campo) {
    if (!is_string($solicitud[$campo]) || trim($solicitud[$campo]) === '') {
        responderGuardado(false, 'Uno o más campos obligatorios tienen un formato inválido.', 400);
    }
}

$camposTextoOpcionales = [
    'categoria' => 40,
    'tipo_lugar' => 40,
    'lugar' => 150,
    'fecha_evento' => null,
    'hora_evento' => null,
    'observaciones' => null
];

foreach ($camposTextoOpcionales as $campo => $longitudMaxima) {
    if ($solicitud[$campo] !== null && !is_string($solicitud[$campo])) {
        responderGuardado(false, 'Uno o más campos opcionales tienen un formato inválido.', 400);
    }

    if (
        $longitudMaxima !== null &&
        is_string($solicitud[$campo]) &&
        mb_strlen($solicitud[$campo], 'UTF-8') > $longitudMaxima
    ) {
        responderGuardado(false, 'Uno o más campos superan la longitud permitida.', 400);
    }
}

if (
    mb_strlen($solicitud['titulo'], 'UTF-8') > 150 ||
    mb_strlen($solicitud['descripcion'], 'UTF-8') > 4000
) {
    responderGuardado(false, 'El título o la descripción superan la longitud permitida.', 400);
}

foreach (['entidad_relacionada', 'etiquetas', 'campos_faltantes'] as $campo) {
    if (!is_array($solicitud[$campo])) {
        responderGuardado(false, 'Las entidades, etiquetas y campos faltantes deben ser listas.', 400);
    }

    foreach ($solicitud[$campo] as $valor) {
        if (!is_string($valor)) {
            responderGuardado(false, 'Las listas de la novedad contienen valores inválidos.', 400);
        }
    }
}

// comprueba fecha y hora sin aceptar formatos que MariaDB no pueda almacenar
$fechaValida = static function (?string $fecha): bool {
    if ($fecha === null) {
        return true;
    }

    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $partes)) {
        return false;
    }

    return checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1]);
};

$horaValida = static function (?string $hora): bool {
    if ($hora === null) {
        return true;
    }

    if (!preg_match('/^(\d{2}):(\d{2})(?::(\d{2}))?$/', $hora, $partes)) {
        return false;
    }

    return (int) $partes[1] <= 23 &&
        (int) $partes[2] <= 59 &&
        (!isset($partes[3]) || (int) $partes[3] <= 59);
};

if (
    !$fechaValida($solicitud['fecha_novedad']) ||
    !$horaValida($solicitud['hora_novedad']) ||
    !$fechaValida($solicitud['fecha_evento']) ||
    !$horaValida($solicitud['hora_evento'])
) {
    responderGuardado(false, 'La fecha u hora de la novedad no es válida.', 400);
}

$horaNovedad = strlen($solicitud['hora_novedad']) === 5
    ? $solicitud['hora_novedad'] . ':00'
    : $solicitud['hora_novedad'];
$horaEvento = $solicitud['hora_evento'] !== null && strlen($solicitud['hora_evento']) === 5
    ? $solicitud['hora_evento'] . ':00'
    : $solicitud['hora_evento'];

try {
    $entidadRelacionada = json_encode($solicitud['entidad_relacionada'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $etiquetas = json_encode($solicitud['etiquetas'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $camposFaltantes = json_encode($solicitud['campos_faltantes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (JsonException $error) {
    responderGuardado(false, 'No se pudieron preparar los datos de la novedad.', 400);
}

// TITULO 5 CONECTAR CON LA BASE DE DATOS
// utiliza la configuración local de MariaDB empleada por los demás endpoints
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

if ($mysqli->connect_error) {
    error_log('No se pudo conectar con la base de datos de bitácora: ' . $mysqli->connect_error);
    responderGuardado(false, 'No se pudo conectar con la base de datos.', 500);
}

if (!$mysqli->set_charset('utf8mb4')) {
    error_log('No se pudo establecer utf8mb4 para el guardado de bitácora: ' . $mysqli->error);
    $mysqli->close();
    responderGuardado(false, 'No se pudo preparar la conexión con la base de datos.', 500);
}

// TITULO 6 GUARDAR NOVEDAD
// asocia la fila al usuario y condominio de la sesión, no a valores enviados por el navegador
$consulta = $mysqli->prepare(
    'INSERT INTO bitacora (
        id_condominio_fk,
        id_usuario_fk,
        titulo,
        descripcion,
        categoria,
        tipo_lugar,
        lugar,
        entidad_relacionada,
        fecha_evento,
        hora_evento,
        etiquetas,
        campos_faltantes,
        fecha_novedad,
        hora_novedad,
        observaciones
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

if ($consulta === false) {
    error_log('No se pudo preparar la inserción en bitácora: ' . $mysqli->error);
    $mysqli->close();
    responderGuardado(false, 'No se pudo preparar el guardado de la novedad.', 500);
}

$consulta->bind_param(
    'iisssssssssssss',
    $idCondominio,
    $idUsuario,
    $solicitud['titulo'],
    $solicitud['descripcion'],
    $solicitud['categoria'],
    $solicitud['tipo_lugar'],
    $solicitud['lugar'],
    $entidadRelacionada,
    $solicitud['fecha_evento'],
    $horaEvento,
    $etiquetas,
    $camposFaltantes,
    $solicitud['fecha_novedad'],
    $horaNovedad,
    $solicitud['observaciones']
);

if (!$consulta->execute()) {
    error_log('Falló la inserción en bitácora: ' . $consulta->error);
    $consulta->close();
    $mysqli->close();
    responderGuardado(false, 'No se pudo guardar la novedad.', 500);
}

$idBitacora = $consulta->insert_id;
$consulta->close();
$mysqli->close();

responderGuardado(true, 'La novedad se guardó correctamente.', 201, [
    'id_bitacora' => $idBitacora
]);