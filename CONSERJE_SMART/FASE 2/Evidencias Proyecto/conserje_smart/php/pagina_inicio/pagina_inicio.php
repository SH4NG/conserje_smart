<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/pagina_inicio.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/inicio/inicio.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/inicio/quienes_somos/quienes_somos.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/inicio/nuestro_objetivo/nuestro_objetivo.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/inicio/nuestra_vision/nuestra_vision.css">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/inicio/consultas/consultas.css">
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
    <title> INICIO </title>
</head>
    <body>
        
        <!-- Contenido de la cabecera -->

            <!-- se llama a la cabecera mediante include -->
            <?php include __DIR__ . '/cabecera/cabecera.php'; ?>

        <!-- Contenido pagina inicio -->

            <!-- se llama al al contenido de inicio mediante include -->
            <?php include __DIR__ . '/inicio/inicio.php'; ?>

        <!-- Contenido del pie de pagina -->

            <!-- se llama al pie de pagina mediante include -->
            <?php include __DIR__ . '/pie_pagina/pie_pagina.php'; ?>
    
    <script src="/conserje_smart/js/pagina_inicio/navegacion_secciones.js" defer></script>
    </body>
</html>