<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades</title>
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
    <link rel="stylesheet" href="/conserje_smart/css/programa/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/creado_por_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/bitacora/novedades.css">
</head>
<body>

    <!-- TITULO 1 CABECERA DE SESION -->
    <?php include __DIR__ . '/../../../../cabecera/cabecera.php'; ?>

    <!-- TITULO 2 CONTENIDO DE NOVEDADES -->
    <main class="pagina_novedades" id="pagina_novedades">
        <div class="encabezado_novedades">
            <h1>Bitácora del condominio</h1>
            <a class="volver_bitacora" href="/conserje_smart/php/programa/conserje/inicio_conserje/modulos/bitacora/bitacora.php">
                Volver a ingresar novedad
            </a>
        </div>

        <p class="estado_lista_novedades" id="estado_lista_novedades" role="status" aria-live="polite">
            Cargando novedades...
        </p>
        <div class="novedades" id="novedades" aria-label="Novedades del condominio"></div>
    </main>

    <!-- TITULO 3 PIE DE PAGINA -->
    <?php include __DIR__ . '/../../pie_pagina/pie_pagina.php'; ?>

    <script src="/conserje_smart/js/programa/conserje/inicio_conserje/modulos/bitacora/novedades.js"></script>
</body>
</html>