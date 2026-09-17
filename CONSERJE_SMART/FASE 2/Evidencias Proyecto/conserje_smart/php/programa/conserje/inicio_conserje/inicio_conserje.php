<?php
// inicia o reanuda la sesion existente
session_start();

// comprueba si la variable de estado no existe o no esta iniciada
if (!isset($_SESSION['estado']) || $_SESSION['estado'] !== 'sesion_iniciada') {

    // destruye cualquier resto de sesion residual
    session_destroy();

    // redirige inmediatamente al formulario de login si intentan entrar a la fuerza
    header("Location: /conserje_smart/php/inicio_sesion/inicio_sesion.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConserjeSmart - Inicio Conserje</title>

    <link rel="stylesheet" href="/conserje_smart/css/programa/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/pie_pagina/creado_por_pie_pagina.css">

</head>
<body>

        <!-- TITULO CABECERA -->
        <div class="cabecera_programa" id="cabecera_programa">
            <?php include __DIR__ . '/../../cabecera/cabecera.php'; ?>
        </div>

        <!-- TITULO CONTENEDOR CONTENIDO -->
        <div class="contenedor_contenido" id="contenedor_contenido">

            <div class="contenedor_inicio_contenido" id="contenedor_inicio_contenido">
                <?php include __DIR__ . '/contenedor_inicio_contenido.php'; ?>
            </div>

            <div class="contenedor_barra_lateral" id="contenedor_barra_lateral">
                <?php include __DIR__ . '/contenedor_barra_lateral.php'; ?>
            </div>

        </div>

    </div>

    <!-- TITULO CONTENEDOR PIE PAGINA -->
    <div class="contenedor_pie_pagina" id="contenedor_pie_pagina">
        <?php include __DIR__ . '/../../pie_pagina/pie_pagina.php'; ?>
    </div>

</div>

</body>
</html>