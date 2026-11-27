<?php
// TITULO 1 INICIAR LA SESION DEL CONSERJE
if (session_status() !== PHP_SESSION_ACTIVE) {
    require_once dirname(__DIR__, 7) . '/conserje-smart-backend/php/programa/conserje/inicio_conserje/modulos/visita/backend_visita/comun_visita.php';
    iniciarSesionVisita();
}

// TITULO 2 COMPROBAR EL ACCESO AL MODULO VISITA
if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje'
) {
    session_destroy();
    header('Location: /conserje_smart/conserje-smart-frontend/php/inicio_sesion/inicio_sesion.php');
    exit;
}

// TITULO 3 PREPARAR EL TOKEN DEL FORMULARIO
if (empty($_SESSION['csrf_visita'])) {
    $_SESSION['csrf_visita'] = bin2hex(random_bytes(32));
}
// TITULO 4 PREPARAR LOS ENCABEZADOS DE LA VISTA
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- TITULO CONFIGURACION DE PAGINA VISITA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-visita" content="<?= htmlspecialchars($_SESSION['csrf_visita'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#e5ba31">
    <title>Visitas | Conserje Smart</title>

    <!-- TITULO ESTILOS DE PAGINA VISITA -->
    <link rel="icon" type="image/png" href="/conserje_smart/conserje-smart-frontend/imagenes/favicon/icono_conserje_smart.png">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/visita/visita.css?v=<?= filemtime(__DIR__ . '/../../../../../../css/programa/conserje/inicio_conserje/modulos/visita/visita.css'); ?>">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/visita/formulario_visita.css?v=<?= filemtime(__DIR__ . '/../../../../../../css/programa/conserje/inicio_conserje/modulos/visita/formulario_visita.css'); ?>">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/vincular_movil_visita.css?v=<?= filemtime(__DIR__ . '/../../../../../../css/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/vincular_movil_visita.css'); ?>">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/visita/tablero_visita.css?v=<?= filemtime(__DIR__ . '/../../../../../../css/programa/conserje/inicio_conserje/modulos/visita/tablero_visita.css'); ?>">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/pie_pagina/creado_por_pie_pagina.css">

    <!-- TITULO IMPORTACIONES JS DE VISITA -->
    <?php include __DIR__ . '/recursos_visita.php'; ?>
</head>
<body class="modo_escritorio_visita">
    <!-- TITULO CABECERA -->
    <?php include __DIR__ . '/../../../../cabecera/cabecera.php'; ?>

    <!-- TITULO PAGINA VISITA -->
    <main class="pagina_visita" id="pagina_visita">
        <!-- TITULO CABECERA VISITA -->
        <section class="cabecera_visita" id="cabecera_visita">
            <!-- TITULO TITULOS CABECERA VISITA -->
            <div class="titulos_cabecera_visita">
                <span class="etiqueta_seccion">Acceso residencial</span>
                <h1>Registro de visitas</h1>
                <p class="descripcion_cabecera_visita">Escanea en el móvil. Confirma el ingreso aquí.</p>
            </div>
            <!-- TITULO RESUMEN VISITA -->
            <div class="resumen_visita" id="resumen_visita">
                <span class="resumen_visita_titulo">Departamento</span>
                <strong id="resumen_departamento_visita">Sin selección</strong>
            </div>
        </section>

        <!-- TITULO AREA REGISTRO VISITA -->
        <div class="area_registro_visita">
            <!-- TITULO PANEL LECTURA VISITA -->
            <div class="panel_lectura_visita">
                <!-- TITULO CONTENEDOR VINCULAR MOVIL VISITA -->
                <?php include __DIR__ . '/escanerQR_visita/vincular_movil_visita.php'; ?>

            </div>
            <!-- TITULO CONTENEDOR FORMULARIO VISITA -->
            <?php include __DIR__ . '/formulario_visita.php'; ?>
        </div>

        <!-- TITULO CONTENEDOR TABLERO VISITA -->
        <?php include __DIR__ . '/tablero_visita.php'; ?>
    </main>

    <!-- TITULO CONTENEDOR PIE PAGINA -->
    <?php include __DIR__ . '/../../pie_pagina/pie_pagina.php'; ?>

    <!-- TITULO INICIALIZACION JS DE VISITA -->
    <script type="module" src="<?= htmlspecialchars($importacionesVisita[$rutaJsVisita . 'visita.js'], ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
