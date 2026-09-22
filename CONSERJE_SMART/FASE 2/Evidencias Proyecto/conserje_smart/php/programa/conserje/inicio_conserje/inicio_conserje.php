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
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/inicio_conserje.css">


    <title>ConserjeSmart - Inicio Conserje</title>
    
</head>
<body>

        <!-- TITULO CABECERA -->
        <div class="cabecera" id="cabecera">

        </div>


        <!-- TITULO CONTENEDOR CONTENIDO -->
        <div class="contenedor_contenido" id="contenedor_contenido">

            <div class="contenedor_barra_lateral" id="contenedor_barra_lateral">
                <?php include __DIR__ . '/barra_lateral/barra_lateral.php'; ?>
            </div>

            <div class="contenedor_inicio_contenido" id="contenedor_inicio_contenido">
                <?php include __DIR__ . '/contenedor_inicio_contenido/contenedor_inicio_contenido.php'; ?>
            </div>

        </div>

    <!-- TITULO CONTENEDOR PIE PAGINA -->
    <?php include __DIR__ . '/pie_pagina/pie_pagina.php'; ?>



</body>
</html>