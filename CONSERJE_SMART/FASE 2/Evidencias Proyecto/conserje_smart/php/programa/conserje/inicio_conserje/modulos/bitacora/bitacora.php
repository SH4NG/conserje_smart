<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- TITULO 1 METADATOS Y ESTILOS DE BITACORA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitacora</title>
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
    <link rel="stylesheet" href="/conserje_smart/css/programa/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/bitacora/bitacora.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/creado_por_pie_pagina.css">
</head>
<body>

    <!-- TITULO 2 CABECERA DE SESION -->
    <?php include __DIR__ . '/../../../../cabecera/cabecera.php'; ?>

    <!-- TITULO 3 CONTENIDO PRINCIPAL DE BITACORA -->
    <main class="contenido_bitacora" id="contenido_bitacora">
        <!-- TITULO 4 VISTA PREVIA DE TURNO Y RESULTADO -->
        <section class="panel_vista_previa_bitacora" id="panel_vista_previa_bitacora">
            <?php include __DIR__ . '/vista_previa_bitacora.php'; ?>
        </section>

        <!-- TITULO 5 MENU DE INGRESO DE NOVEDAD -->
        <section class="contenedor_menu_bitacora" id="contenedor_menu_bitacora">
            <?php include __DIR__ . '/menu_bitacora.php'; ?>
        </section>
    </main>

    <!-- TITULO 6 PIE DE PAGINA -->
    <?php include __DIR__ . '/../../pie_pagina/pie_pagina.php'; ?>

</body>
</html>