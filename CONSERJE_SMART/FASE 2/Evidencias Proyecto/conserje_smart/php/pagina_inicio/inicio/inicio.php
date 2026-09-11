<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="/conserje_smart/css/pagina_inicio/inicio/inicio.css">
</head>
<body>
    
    <!-- TITULO PAGINA_INICIO  -->
    <div class="inicio" id="inicio">


        <!-- TITULO CONTENIDO_PAGINA_INICIO -->
        <div class=titulo_inicio>
            "TITULO DEL CONTENIDO DE INCIO"

        </div>
        
        <!-- TITULO QUIENES SOMOS  -->

            <!-- se llama al quienes_somos mediante include -->
        <div class="contenedor_quienes_somos" id="contenedor_quienes_somos">
            <?php include __DIR__ . '/quienes_somos.php'; ?>
    
        </div>

    </div>

    <script><?php include __DIR__ . '/../../../js/pagina_inicio/inicio/inicio.js'; ?></script>

</body>
</html>