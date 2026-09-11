<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/pagina_inicio.css">
    <title></title>
</head>
    <body>
        
        <!-- Contenido de la cabecera -->

            <!-- se llama a la cabecera mediante include -->
            <?php
            include 'cabecera/cabecera.php';
            ?>

        <!-- Contenido pagina inicio -->

            <!-- se llama al al contenido de inicio mediante include -->
            <?php
            include 'inicio/inicio.php';
            ?> 

        <!-- Contenido del pie de pagina -->

            <!-- se llama al pie de pagina mediante include -->
            <?php
            include 'pie_pagina/pie_pagina.php';
            ?>
    
        <script><?php include __DIR__ . '/../../../js/pagina_inicio/pie_pagina/pie_pagina.js'; ?></script>
    </body>
</html>