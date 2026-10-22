<?php
header('Content-Type: application/json; charset=utf-8');

function responderJson(bool $exito, string $mensaje, array $datos = [], int $codigoHttp = 200): void
{
    http_response_code($codigoHttp);
    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje,
        'datos' => $datos
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    responderJson(false, 'Método no permitido.', [], 405);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$idCondominio = filter_var($_SESSION['id_condominio_fk'] ?? null, FILTER_VALIDATE_INT);

if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje' ||
    $idCondominio === false ||
    $idCondominio === null ||
    $idCondominio < 1
) {
    responderJson(false, 'Se requiere una sesión de conserje válida.', [], 401);
}

mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

if ($mysqli->connect_error) {
    error_log('No se pudo conectar con la base de datos al listar departamentos: ' . $mysqli->connect_error);
    responderJson(false, 'No se pudo conectar con la base de datos.', [], 500);
}

if (!$mysqli->set_charset('utf8mb4')) {
    error_log('No se pudo establecer utf8mb4 al listar departamentos: ' . $mysqli->error);
    $mysqli->close();
    responderJson(false, 'No se pudo preparar la conexión con la base de datos.', [], 500);
}

$stmt = $mysqli->prepare(
    'SELECT id_departamento, torre_departamento, numero_departamento, piso_departamento
    FROM departamento
    WHERE id_condominio_fk = ?
    ORDER BY torre_departamento ASC, piso_departamento IS NULL, piso_departamento ASC, numero_departamento ASC'
);

if ($stmt === false) {
    error_log('No se pudo preparar la consulta de departamentos: ' . $mysqli->error);
    $mysqli->close();
    responderJson(false, 'No se pudo preparar la consulta de departamentos.', [], 500);
}

$stmt->bind_param('i', $idCondominio);

if (!$stmt->execute()) {
    error_log('No se pudieron consultar los departamentos: ' . $stmt->error);
    $stmt->close();
    $mysqli->close();
    responderJson(false, 'No se pudo consultar los departamentos disponibles.', [], 500);
}

$resultado = $stmt->get_result();
if ($resultado === false) {
    error_log('No se pudo leer el resultado de departamentos: ' . $stmt->error);
    $stmt->close();
    $mysqli->close();
    responderJson(false, 'No se pudo leer la lista de departamentos.', [], 500);
}

$departamentos = [];

while ($fila = $resultado->fetch_assoc()) {
    $departamentos[] = [
        'id_departamento' => (int) $fila['id_departamento'],
        'torre_departamento' => $fila['torre_departamento'] ?? '',
        'numero_departamento' => $fila['numero_departamento'] ?? '',
        'piso_departamento' => $fila['piso_departamento'] !== null ? (int) $fila['piso_departamento'] : null,
    ];
}

$stmt->close();
$mysqli->close();

responderJson(
    true,
    $departamentos === [] ? 'Este condominio todavía no tiene departamentos registrados.' : 'Departamentos cargados correctamente.',
    $departamentos
);
