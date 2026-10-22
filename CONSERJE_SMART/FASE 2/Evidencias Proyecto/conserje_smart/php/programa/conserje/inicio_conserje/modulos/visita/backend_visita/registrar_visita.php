<?php
header('Content-Type: application/json; charset=utf-8');

function responderRegistroVisita(bool $exito, string $mensaje, int $codigoHttp = 200, ?array $datos = null): void
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderRegistroVisita(false, 'Método de solicitud no permitido.', 405);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$idUsuario = filter_var($_SESSION['id_usuario'] ?? null, FILTER_VALIDATE_INT);
$idCondominio = filter_var($_SESSION['id_condominio_fk'] ?? null, FILTER_VALIDATE_INT);

if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje' ||
    $idUsuario === false ||
    $idUsuario === null ||
    $idUsuario < 1 ||
    $idCondominio === false ||
    $idCondominio === null ||
    $idCondominio < 1
) {
    responderRegistroVisita(false, 'Se requiere una sesión de conserje válida.', 401);
}

$idDepartamento = filter_var($_POST['id_departamento'] ?? null, FILTER_VALIDATE_INT);
$nombreVisitante = trim((string) ($_POST['nombre_visitante'] ?? ''));
$rutIngresado = strtoupper(trim((string) ($_POST['rut_visitante'] ?? '')));
$motivo = trim((string) ($_POST['motivo_visita'] ?? ''));
$observaciones = trim((string) ($_POST['observaciones_visita'] ?? ''));

if (
    $idDepartamento === false ||
    $idDepartamento === null ||
    $idDepartamento < 1 ||
    $nombreVisitante === '' ||
    $rutIngresado === '' ||
    $motivo === ''
) {
    responderRegistroVisita(false, 'Completa todos los campos obligatorios con datos válidos.', 400);
}

if (
    mb_strlen($nombreVisitante, 'UTF-8') > 120 ||
    mb_strlen($motivo, 'UTF-8') > 180 ||
    mb_strlen($observaciones, 'UTF-8') > 1000
) {
    responderRegistroVisita(false, 'Uno o más campos superan la longitud permitida.', 400);
}

$rutLimpio = preg_replace('/[^0-9K]/', '', $rutIngresado);
if (!is_string($rutLimpio) || strlen($rutLimpio) < 2 || strlen($rutLimpio) > 9) {
    responderRegistroVisita(false, 'El RUT ingresado no tiene un formato válido.', 400);
}

$cuerpoRut = substr($rutLimpio, 0, -1);
$digitoVerificador = substr($rutLimpio, -1);
if (!ctype_digit($cuerpoRut)) {
    responderRegistroVisita(false, 'El RUT ingresado no tiene un formato válido.', 400);
}

$suma = 0;
$multiplicador = 2;
for ($indice = strlen($cuerpoRut) - 1; $indice >= 0; $indice--) {
    $suma += (int) $cuerpoRut[$indice] * $multiplicador;
    $multiplicador = $multiplicador === 7 ? 2 : $multiplicador + 1;
}

$resto = 11 - ($suma % 11);
$digitoEsperado = $resto === 11 ? '0' : ($resto === 10 ? 'K' : (string) $resto);
if ($digitoVerificador !== $digitoEsperado) {
    responderRegistroVisita(false, 'El RUT ingresado no es válido.', 400);
}

$rutNormalizado = number_format((int) $cuerpoRut, 0, '', '.') . '-' . $digitoVerificador;

mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

if ($mysqli->connect_error) {
    error_log('No se pudo conectar con la base de datos al registrar una visita: ' . $mysqli->connect_error);
    responderRegistroVisita(false, 'No se pudo conectar con la base de datos.', 500);
}

if (!$mysqli->set_charset('utf8mb4')) {
    error_log('No se pudo establecer utf8mb4 al registrar una visita: ' . $mysqli->error);
    $mysqli->close();
    responderRegistroVisita(false, 'No se pudo preparar la conexión con la base de datos.', 500);
}

$consultaDepartamento = $mysqli->prepare(
    'SELECT id_departamento
    FROM departamento
    WHERE id_departamento = ? AND id_condominio_fk = ?
    LIMIT 1'
);

if ($consultaDepartamento === false) {
    error_log('No se pudo preparar la validación del departamento de visita: ' . $mysqli->error);
    $mysqli->close();
    responderRegistroVisita(false, 'No se pudo validar el departamento seleccionado.', 500);
}

$consultaDepartamento->bind_param('ii', $idDepartamento, $idCondominio);
if (!$consultaDepartamento->execute()) {
    error_log('Falló la validación del departamento de visita: ' . $consultaDepartamento->error);
    $consultaDepartamento->close();
    $mysqli->close();
    responderRegistroVisita(false, 'No se pudo validar el departamento seleccionado.', 500);
}

$resultadoDepartamento = $consultaDepartamento->get_result();
$departamentoValido = $resultadoDepartamento !== false && $resultadoDepartamento->num_rows === 1;
$consultaDepartamento->close();

if (!$departamentoValido) {
    $mysqli->close();
    responderRegistroVisita(false, 'El departamento seleccionado no pertenece a tu condominio.', 400);
}

$fechaHoraIngreso = (new DateTimeImmutable('now', new DateTimeZone('America/Santiago')))->format('Y-m-d H:i:s');
$consulta = $mysqli->prepare(
    'INSERT INTO visita (
        id_condominio_fk,
        id_usuario_fk,
        id_departamento_fk,
        nombre_visitante,
        rut_visitante,
        motivo_visita,
        observaciones_visita,
        fecha_hora_ingreso
    ) VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, \'\'), ?)'
);

if ($consulta === false) {
    error_log('No se pudo preparar el registro de visita: ' . $mysqli->error);
    $mysqli->close();
    responderRegistroVisita(false, 'No se pudo preparar el registro de la visita.', 500);
}

$consulta->bind_param(
    'iiisssss',
    $idCondominio,
    $idUsuario,
    $idDepartamento,
    $nombreVisitante,
    $rutNormalizado,
    $motivo,
    $observaciones,
    $fechaHoraIngreso
);

if (!$consulta->execute()) {
    error_log('Falló el registro de visita: ' . $consulta->error);
    $consulta->close();
    $mysqli->close();
    responderRegistroVisita(false, 'No se pudo guardar la visita.', 500);
}

$idVisita = $mysqli->insert_id;
$consulta->close();
$mysqli->close();

responderRegistroVisita(true, 'Visita registrada correctamente.', 201, [
    'id_visita' => $idVisita,
    'fecha_hora_ingreso' => $fechaHoraIngreso,
    'rut_visitante' => $rutNormalizado
]);