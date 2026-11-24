<?php
// TITULO 1 COMPROBAR EL METODO DE CARGA DEL SERVICE WORKER
if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'HEAD'], true)) {
    header('Allow: GET, HEAD');
    http_response_code(405);
    exit;
}

// TITULO 2 PERMITIR EL CONTROL DE LA RUTA DE VISITA
header('Content-Type: text/javascript; charset=utf-8');
header('Service-Worker-Allowed: /conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/visita/');
header('Cache-Control: no-cache');

// TITULO 3 SERVIR EL ARCHIVO JS ORIGINAL DE LA PWA
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    readfile(dirname(__DIR__, 8) . '/conserje-smart-frontend/js/programa/conserje/inicio_conserje/modulos/visita/visita_appMovil/visita_appMovil_serviceWorker.js');
}
