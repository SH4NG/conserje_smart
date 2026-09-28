<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRARSE</title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registrarse.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/creado_por_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/registro.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/titulo_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/nombre_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/apellido_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/rut_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/telefono_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/rol_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/condominio_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/contraseña_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/boton_registrar.css">
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registro/validacion_temporal.css">
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
    
</head>
<body>
    

     <!-- TITULO CABECERA -->

            <!-- se llama a la cabecera mediante include -->
            <?php include __DIR__ . '/../cabecera/cabecera.php'; ?>

        <!-- TITULO REGISTRO -->
            <main class="contenedor_registro">
                <!-- se llama al autentificacion mediante include -->
                <?php
                include __DIR__ . '/registro/registro.php';
                ?> 
            </main>

        <!-- TITULO PIE PAGINA -->

            <!-- se llama al pie de pagina mediante include -->
            <?php
            include __DIR__ . '/../pie_pagina/pie_pagina.php';
            ?>


</body>
</html>