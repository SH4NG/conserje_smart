<?php

// TITULO 1 RESPUESTA JSON
// establece el formato de respuesta de este endpoint
header('Content-Type: application/json; charset=utf-8');

// TITULO 2 RESPONDER SOLICITUD
// devuelve un resultado JSON uniforme y finaliza el endpoint
function responderAnalisis(bool $exito, string $mensaje, int $codigoHttp = 200, ?array $datos = null): void
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
// restringe el endpoint a conserjes con una sesion iniciada
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responderAnalisis(false, 'Método de solicitud no permitido.', 405);
}

session_start();

if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje'
) {
    responderAnalisis(false, 'Se requiere una sesión de conserje iniciada.', 401);
}

// TITULO 4 RECIBIR Y VALIDAR NOVEDAD
// acepta exclusivamente JSON con una novedad de texto
$cuerpoSolicitud = file_get_contents('php://input');
$solicitud = json_decode($cuerpoSolicitud, true);

if (!is_array($solicitud) || !isset($solicitud['novedad']) || !is_string($solicitud['novedad'])) {
    responderAnalisis(false, 'La solicitud debe incluir la novedad como texto.', 400);
}

$novedad = trim($solicitud['novedad']);

if ($novedad === '') {
    responderAnalisis(false, 'Escribe una novedad antes de solicitar el análisis.', 400);
}

if (mb_strlen($novedad, 'UTF-8') > 4000) {
    responderAnalisis(false, 'La novedad supera el máximo permitido de 4000 caracteres.', 413);
}

// TITULO 5 CAPTURAR FECHA Y HORA DE CREACION
// registra el momento en que PHP recibe la novedad antes del análisis de IA
$fechaHoraNovedad = new DateTimeImmutable('now', new DateTimeZone('America/Santiago'));
$fechaNovedad = $fechaHoraNovedad->format('Y-m-d');
$horaNovedad = $fechaHoraNovedad->format('H:i:s');

// TITULO 6 CONFIGURAR LLAMADA A N8N
// obtiene la Production URL desde el entorno de Apache y nunca desde el navegador
$urlWebhookN8n = getenv('N8N_BITACORA_WEBHOOK_URL');

if ($urlWebhookN8n === false || trim($urlWebhookN8n) === '') {
    responderAnalisis(false, 'El servicio de análisis no está configurado.', 503);
}

if (
    !filter_var($urlWebhookN8n, FILTER_VALIDATE_URL) ||
    !in_array(parse_url($urlWebhookN8n, PHP_URL_SCHEME), ['http', 'https'], true)
) {
    error_log('N8N_BITACORA_WEBHOOK_URL debe ser una URL HTTP o HTTPS válida.');
    responderAnalisis(false, 'La configuración del servicio de análisis no es válida.', 500);
}

if (!function_exists('curl_init')) {
    error_log('La extensión cURL de PHP no está habilitada.');
    responderAnalisis(false, 'El servidor no tiene habilitada la conexión requerida para el análisis.', 500);
}

$cuerpoN8n = json_encode(
    ['novedad' => $novedad],
    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
);

$encabezados = [
    'Content-Type: application/json; charset=utf-8',
    'Accept: application/json'
];

// añade autenticación si el webhook de n8n está configurado para exigir un token
$tokenWebhook = getenv('N8N_BITACORA_WEBHOOK_TOKEN');
if ($tokenWebhook !== false && $tokenWebhook !== '') {
    $encabezados[] = 'X-Bitacora-Webhook-Token: ' . $tokenWebhook;
}

// TITULO 7 SOLICITAR ANALISIS A N8N
// mantiene la solicitud abierta mientras Ollama genera la respuesta
$curl = curl_init($urlWebhookN8n);
curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $cuerpoN8n,
    CURLOPT_HTTPHEADER => $encabezados,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 120
]);

$respuestaN8n = curl_exec($curl);
$errorCurl = curl_error($curl);
$codigoHttpN8n = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($respuestaN8n === false) {
    error_log('No se pudo conectar con n8n: ' . $errorCurl);
    responderAnalisis(false, 'No se pudo conectar con el servicio de análisis.', 502);
}

if ($codigoHttpN8n < 200 || $codigoHttpN8n >= 300) {
    error_log('n8n respondió con el código HTTP ' . $codigoHttpN8n . '.');
    responderAnalisis(false, 'El servicio de análisis rechazó la solicitud.', 502);
}

// TITULO 8 INTERPRETAR RESPUESTA DE N8N
// admite el objeto final directo o un único elemento en un arreglo JSON
$resultadoN8n = json_decode($respuestaN8n, true);

if (
    is_array($resultadoN8n) &&
    isset($resultadoN8n[0]) &&
    is_array($resultadoN8n[0]) &&
    count($resultadoN8n) === 1
) {
    $resultadoN8n = $resultadoN8n[0];
}

if (is_array($resultadoN8n) && isset($resultadoN8n['output']) && is_array($resultadoN8n['output'])) {
    $resultadoN8n = $resultadoN8n['output'];
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
    'observaciones'
];

if (!is_array($resultadoN8n)) {
    error_log('n8n devolvió una respuesta que no es un objeto JSON.');
    responderAnalisis(false, 'El servicio de análisis devolvió una respuesta inválida.', 502);
}

foreach ($camposObligatorios as $campo) {
    if (!array_key_exists($campo, $resultadoN8n)) {
        error_log('La respuesta de n8n no contiene el campo requerido: ' . $campo . '.');
        responderAnalisis(false, 'El servicio de análisis devolvió una respuesta incompleta.', 502);
    }
}

$camposTextoObligatorios = ['titulo', 'descripcion'];
$camposTextoOpcionales = [
    'categoria',
    'tipo_lugar',
    'lugar',
    'fecha_evento',
    'hora_evento',
    'observaciones'
];

foreach ($camposTextoObligatorios as $campo) {
    if (!is_string($resultadoN8n[$campo])) {
        responderAnalisis(false, 'El servicio de análisis devolvió campos con tipos incorrectos.', 502);
    }
}

foreach ($camposTextoOpcionales as $campo) {
    if ($resultadoN8n[$campo] !== null && !is_string($resultadoN8n[$campo])) {
        responderAnalisis(false, 'El servicio de análisis devolvió campos con tipos incorrectos.', 502);
    }
}

foreach (['entidad_relacionada', 'etiquetas', 'campos_faltantes'] as $campo) {
    if (!is_array($resultadoN8n[$campo])) {
        responderAnalisis(false, 'El servicio de análisis devolvió campos con tipos incorrectos.', 502);
    }

    foreach ($resultadoN8n[$campo] as $valor) {
        if (!is_string($valor)) {
            responderAnalisis(false, 'El servicio de análisis devolvió campos con tipos incorrectos.', 502);
        }
    }
}

if (
    ($resultadoN8n['fecha_evento'] !== null &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $resultadoN8n['fecha_evento'])) ||
    ($resultadoN8n['hora_evento'] !== null &&
        !preg_match('/^\d{2}:\d{2}$/', $resultadoN8n['hora_evento']))
) {
    responderAnalisis(false, 'El servicio de análisis devolvió una fecha u hora con formato incorrecto.', 502);
}

// TITULO 9 NORMALIZAR RESULTADO
// conserva el texto original y agrega los datos de creación calculados por el servidor
$resultadoN8n['descripcion'] = $novedad;
$resultadoN8n['fecha_novedad'] = $fechaNovedad;
$resultadoN8n['hora_novedad'] = $horaNovedad;
$resultadoN8n['campos_faltantes'] = [];

foreach (['categoria', 'tipo_lugar', 'lugar', 'fecha_evento', 'hora_evento'] as $campo) {
    if ($resultadoN8n[$campo] === null) {
        $resultadoN8n['campos_faltantes'][] = $campo;
    }
}

responderAnalisis(true, 'Novedad analizada correctamente.', 200, $resultadoN8n);