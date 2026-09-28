<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> INICIO DE SESION </title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/inicio_sesion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/logo_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/titulo_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/campo_correo_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/campo_contraseña_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/recordar_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/recuperar_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/boton_iniciar_sesion_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/autentificacion/registrar_autentificacion.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/creado_por_pie_pagina.css">
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
</head>

    <body>
        
        <!-- TITULO CABECERA -->

            <!-- se llama a la cabecera mediante include -->
            <?php include __DIR__ . '/cabecera/cabecera.php'; ?>

        <!-- TITULO AUTENTIFICACION -->

            <!-- se llama al autentificacion mediante include -->
            <?php include __DIR__ . '/autentificacion/autentificacion.php'; ?>

        <!-- TITULO PIE PAGINA -->

            <!-- se llama al pie de pagina mediante include -->
            <?php include __DIR__ . '/pie_pagina/pie_pagina.php'; ?>
    
    </body>
    
</html>