<?php

// TITULO 1 RESPUESTA JSON
// define el formato de salida para el listado de condominios
header('Content-Type: application/json; charset=utf-8');

// TITULO 2 CONEXION BASE DE DATOS
// establece la conexion local con la base de datos del proyecto
$mysqli = new mysqli('localhost', 'root', '', 'conserje_smart_bd');

// verifica si la conexion produjo un error
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No se pudo conectar con la base de datos.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// configura la codificacion para recibir correctamente caracteres especiales
$mysqli->set_charset('utf8mb4');

// TITULO 3 CONSULTAR CONDOMINIOS
// obtiene el id y el nombre ordenados alfabeticamente
$resultado = $mysqli->query(
    'SELECT id_condominio, nombre_condominio
     FROM condominio
     ORDER BY nombre_condominio ASC'
);

if ($resultado === false) {
    $mysqli->close();
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No se pudieron cargar los condominios.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// TITULO 4 PREPARAR RESPUESTA
// crea la lista que sera enviada al archivo JavaScript
$condominios = [];

// recorre cada resultado y conserva solo los datos necesarios
while ($condominio = $resultado->fetch_assoc()) {
    $condominios[] = [
        'id_condominio' => (int) $condominio['id_condominio'],
        'nombre_condominio' => $condominio['nombre_condominio']
    ];
}

// libera el resultado y cierra la conexion
$resultado->free();
$mysqli->close();

// TITULO 5 DEVOLVER CONDOMINIOS
// responde con el listado en formato JSON
echo json_encode([
    'exito' => true,
    'condominios' => $condominios
], JSON_UNESCAPED_UNICODE);

?>
