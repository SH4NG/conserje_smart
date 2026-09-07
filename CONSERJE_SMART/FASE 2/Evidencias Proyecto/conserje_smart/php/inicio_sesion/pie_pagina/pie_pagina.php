<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="/conserje_smart/css/inicio_sesion/pie_pagina/pie_pagina.css">
</head>
<body>

    <!-- TITULO 1 PIE PAGINA -->

    <div class="pie_pagina" id="pie_pagina">

        <!-- TITULO PARTE SUPERIOR PIE PAGINA -->

        <div class="parte_superior_pie_pagina" id="parte_superior_pie_pagina">

            <!-- TITULO CONTENEDOR NAVEGACION PIE PAGINA -->

            <div class="contenedor_navegacion_pie_pagina" id="contenedor_navegacion_pie_pagina">
                <?php include __DIR__ . '/navegacion_pie_pagina.php'; ?>
            </div>
            
            <!-- TITULO CONTENEDOR LOGO PIE PAGINA -->

            <div class="contenedor_logo_pie_pagina" id="contenedor_logo_pie_pagina">
                <?php include __DIR__ . '/logo_pie_pagina.php'; ?>
            </div>

            <!-- TITULO CONTENEDOR CONTACTOS PIE PAGINA -->

            <div class="contenedor_contactos_pie_pagina" id="contenedor_contactos_pie_pagina">
                <?php include __DIR__ . '/contactos_pie_pagina.php'; ?>
            </div>

        </div>

        <!-- TITULO PARTE INFERIOR PIE DE PAGINA -->

        <div class="parte_inferior_pie_pagina" id="parte_inferior_pie_pagina">

            <div class="contenedor_creado_por_pie_pagina" id="contenedor_creado_por_pie_pagina">
                <?php include __DIR__ . '/creado_por_pie_pagina.php'; ?>
            </div>

        </div>
    </div>

    <script><?php include __DIR__ . '/../../../js/inicio_sesion/pie_pagina/pie_pagina.js'; ?></script>
</body>
    
</html>