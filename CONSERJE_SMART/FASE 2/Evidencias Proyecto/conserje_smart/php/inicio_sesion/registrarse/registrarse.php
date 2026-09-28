<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/registrarse/registrarse.css">
    
</head>
<body>
    

     <!-- TITULO CABECERA -->

            <!-- se llama a la cabecera mediante include -->
            <?php include __DIR__ . '/../cabecera/cabecera.php'; ?>

        <!-- TITULO REGISTRO -->
            <div class=contenedor_registro >
                <!-- se llama al autentificacion mediante include -->
                <?php
                include __DIR__ . '/registro/registro.php';
                ?> 
            </div>

        <!-- TITULO PIE PAGINA -->

            <!-- se llama al pie de pagina mediante include -->
            <?php
            include __DIR__ . '/../pie_pagina/pie_pagina.php';
            ?>


</body>
</html>