<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje'
) {
    session_destroy();
    header('Location: /conserje_smart/php/inicio_sesion/inicio_sesion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitas | Conserje Smart</title>
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
    <link rel="stylesheet" href="/conserje_smart/css/programa/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/visita/visita.css?v=20261020-1">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/creado_por_pie_pagina.css">
</head>
<body>
    <?php include __DIR__ . '/../../../../cabecera/cabecera.php'; ?>

    <main class="pagina_visita" id="pagina_visita">
        <section class="cabecera_visita" id="cabecera_visita">
            <div>
                <span class="etiqueta_seccion">Acceso residencial</span>
                <h1>Registro de visitas</h1>
            </div>
            <div class="resumen_visita" id="resumen_visita">
                <span class="resumen_visita_titulo">Departamento</span>
                <strong id="resumen_departamento_visita">Sin selección</strong>
            </div>
        </section>

        <?php include __DIR__ . '/formulario_visita.php'; ?>
    </main>

    <?php include __DIR__ . '/../../pie_pagina/pie_pagina.php'; ?>

    <script src="/conserje_smart/js/programa/conserje/inicio_conserje/modulos/visita/visita.js?v=20261020-1"></script>
</body>
</html>
