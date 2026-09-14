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

     <!-- estilo pagina inicio provisorio, sacar despues de agregar algo  -->

        <!-- TITULO CONTENIDO_PAGINA_INICIO -->

        <div class=titulo_inicio>

            TE DAMOS LA BIENVENIDA A CONSERJE SMART

        </div>

        <!-- TITULO QUIENES SOMOS  -->

            <!-- se llama al quienes_somos mediante include -->

        <div class="contenedor_contenindo_inicio" id="contenedor_contenindo_inicio">
            <div class="contenedor_quienes_somos" id="contenedor_quienes_somos">
                
                <?php include 'quienes_somos/quienes_somos.php';?>
            </div>
        </div>

    <script><?php include __DIR__ . '/../../../js/pagina_inicio/inicio/inicio.js'; ?></script>

</body>
</html>