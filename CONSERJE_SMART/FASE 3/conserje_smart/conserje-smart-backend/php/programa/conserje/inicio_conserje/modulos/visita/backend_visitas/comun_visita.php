<?php
// TITULO 1 IMPORTAR LA VALIDACION DE RUT
require_once __DIR__ . '/validar_rut_visita.php';

// TITULO 2 ENVIAR LAS RESPUESTAS JSON DEL BACKEND
function responderVisita(bool $exito, string $mensaje, int $codigo = 200, array $datos = [], array $extra = []): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(array_merge([
        'exito' => $exito, 'mensaje' => $mensaje, 'datos' => $datos
    ], $extra), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// TITULO 3 VALIDAR LOS IDENTIFICADORES NUMERICOS
function enteroPositivoVisita($valor): ?int
{
    $numero = filter_var($valor, FILTER_VALIDATE_INT);
    return $numero !== false && $numero > 0 ? $numero : null;
}

// TITULO 4 LEER LOS CAMPOS DE TEXTO DEL FORMULARIO
function textoVisita(string $campo): string
{
    $valor = $_POST[$campo] ?? '';
    if (!is_string($valor)) {
        responderVisita(false, 'El formato de los campos no es válido.', 400);
    }
    return trim($valor);
}

// TITULO 5 VALIDAR LOS DATOS OBLIGATORIOS Y SUS LONGITUDES
function datosFormularioVisita(): array
{
    $idDepartamento = enteroPositivoVisita($_POST['id_departamento'] ?? null);
    $nombre = textoVisita('nombre_visitante');
    $rut = normalizarRutVisita(textoVisita('rut_visitante'));
    $motivo = textoVisita('motivo_visita');
    $observaciones = textoVisita('observaciones_visita');
    if (!$idDepartamento || $nombre === '' || $motivo === '') {
        responderVisita(false, 'Completa departamento, nombre, RUT y motivo con datos válidos.', 400);
    }
    if ($rut === null) {
        responderVisita(false, 'El RUT ingresado no es válido.', 400);
    }
    if (mb_strlen($nombre, 'UTF-8') > 120 || mb_strlen($motivo, 'UTF-8') > 180 || mb_strlen($observaciones, 'UTF-8') > 1000) {
        responderVisita(false, 'Uno o más campos superan la longitud permitida.', 400);
    }
    return [$idDepartamento, $nombre, $rut, $motivo, $observaciones];
}

// TITULO 6 COMPROBAR QUE EL DEPARTAMENTO PERTENECE AL CONDOMINIO
function validarDepartamentoVisita(mysqli $bd, int $idDepartamento, int $idCondominio): void
{
    $consulta = $bd->prepare('SELECT id_departamento FROM departamento WHERE id_departamento = ? AND id_condominio_fk = ? LIMIT 1');
    $consulta->bind_param('ii', $idDepartamento, $idCondominio);
    $consulta->execute();
    $valido = $consulta->get_result()->num_rows === 1;
    $consulta->close();
    if (!$valido) {
        responderVisita(false, 'El departamento seleccionado no pertenece a tu condominio.', 400);
    }
}

// TITULO 7 OBTENER LA FECHA Y HORA DE SANTIAGO
function fechaActualVisita(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('America/Santiago')))->format('Y-m-d H:i:s');
}

// TITULO 8 VALIDAR EL METODO DE LA SOLICITUD
// el usuario y el condominio se obtienen exclusivamente de la sesión del servidor
function ejecutarVisita(string $metodo, callable $operacion): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== $metodo) {
        header('Allow: ' . $metodo);
        responderVisita(false, 'Método no permitido.', 405);
    }
    // TITULO 9 VALIDAR LA SESION DEL CONSERJE
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $idUsuario = enteroPositivoVisita($_SESSION['id_usuario'] ?? null);
    $idCondominio = enteroPositivoVisita($_SESSION['id_condominio_fk'] ?? null);
    if (($_SESSION['estado'] ?? '') !== 'sesion_iniciada' || ($_SESSION['rol_usuario'] ?? '') !== 'conserje' || !$idUsuario || !$idCondominio) {
        responderVisita(false, 'Se requiere una sesión de conserje válida.', 401);
    }
    // TITULO 10 VALIDAR EL TOKEN DEL FORMULARIO
    if ($metodo === 'POST') {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($token) || empty($_SESSION['csrf_visita']) || !hash_equals($_SESSION['csrf_visita'], $token)) {
            responderVisita(false, 'La sesión del formulario venció. Recarga la página e inténtalo nuevamente.', 403);
        }
    }
    // TITULO 11 CONECTAR LA BASE DE DATOS Y EJECUTAR LA OPERACION
    session_write_close();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $bd = new mysqli('localhost', 'root', '', getenv('CONSERJE_DB_NOMBRE') ?: 'conserje_smart_bd');
        $bd->set_charset('utf8mb4');
        $operacion($bd, $idCondominio, $idUsuario);
    } catch (Throwable $error) {
        error_log('Error en visitas: ' . $error->getMessage());
        responderVisita(false, 'No se pudo completar la operación con la base de datos. Inténtalo nuevamente.', 500);
    }
}
