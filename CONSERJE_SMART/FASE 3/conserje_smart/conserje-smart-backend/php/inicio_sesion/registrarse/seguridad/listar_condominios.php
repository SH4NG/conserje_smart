<?php

// TITULO 1 RESPUESTA JSON
// define el formato de salida para el listado de condominios
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__, 3) . '/base_datos/conexion_postgresql.php';

// TITULO 2 CONEXION BASE DE DATOS
// obtiene el id y el nombre ordenados alfabeticamente
try {
    $pdo = conectarBasePostgreSQL();
    $resultado = $pdo->query(
        'SELECT id_condominio, nombre_condominio
         FROM condominio
         ORDER BY nombre_condominio ASC'
    );
    $condominios = $resultado->fetchAll();
} catch (Throwable $error) {
    error_log('No se pudieron cargar los condominios: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No se pudieron cargar los condominios.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// TITULO 4 PREPARAR RESPUESTA
// crea la lista que sera enviada al archivo JavaScript
$condominios = array_map(static function (array $condominio): array {
    return [
        'id_condominio' => (int) $condominio['id_condominio'],
        'nombre_condominio' => $condominio['nombre_condominio']
    ];
}, $condominios);

// TITULO 5 DEVOLVER CONDOMINIOS
// responde con el listado en formato JSON
echo json_encode([
    'exito' => true,
    'condominios' => $condominios
], JSON_UNESCAPED_UNICODE);

?>
