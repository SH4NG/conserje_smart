<?php
// TITULO 1 DEFINIR LAS RUTAS DE LOS RECURSOS JS
// cada importación utiliza la versión del contenido actual, incluidas sus dependencias
$rutaJsVisita = '/conserje_smart/conserje-smart-frontend/js/programa/conserje/inicio_conserje/modulos/visita/';
$carpetaJsVisita = __DIR__ . '/../../../../../../js/programa/conserje/inicio_conserje/modulos/visita/';
// TITULO 2 VERSIONAR LAS IMPORTACIONES DEL MODULO
$importacionesVisita = [];
foreach ([
    'visita.js', 'rut_visita.js', 'escanerQR_visita/control_escanerQR_visita.js', 'tablero_visita.js',
    'Backend_visita/api_visita.js', 'Backend_visita/listar_visita.js',
    'Backend_visita/registrar_visita.js', 'Backend_visita/modificar_visita.js',
    'Backend_visita/registrar_salida_visita.js', 'escanerQR_visita/appMovil_visita/appMovil_visita.js',
    'escanerQR_visita/vincular_movil_visita.js', 'escanerQR_visita/appMovil_visita/vinculo_appMovil_visita.js'
] as $archivoJsVisita) {
    $urlJsVisita = $rutaJsVisita . $archivoJsVisita;
    $importacionesVisita[$urlJsVisita] = $urlJsVisita . '?v=' . substr(hash_file('sha256', $carpetaJsVisita . $archivoJsVisita), 0, 12);
}
?>
<!-- TITULO MAPA DE IMPORTACIONES VISITA -->
<script type="importmap"><?= json_encode(['imports' => $importacionesVisita], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
