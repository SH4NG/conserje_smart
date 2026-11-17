<?php
// TITULO 1 INICIAR LA SESION DEL CONSERJE
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// TITULO 2 COMPROBAR EL ACCESO AL MODULO VISITA
if (
    ($_SESSION['estado'] ?? '') !== 'sesion_iniciada' ||
    ($_SESSION['rol_usuario'] ?? '') !== 'conserje'
) {
    session_destroy();
    header('Location: /conserje_smart/php/inicio_sesion/inicio_sesion.php');
    exit;
}

// TITULO 3 PREPARAR EL TOKEN DEL FORMULARIO
if (empty($_SESSION['csrf_visita'])) {
    $_SESSION['csrf_visita'] = bin2hex(random_bytes(32));
}
// TITULO 4 DEFINIR EL MODO DE PRESENTACION
$modoMovilVisita = $modoMovilVisita ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- TITULO CONFIGURACION DE PAGINA VISITA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-visita" content="<?= htmlspecialchars($_SESSION['csrf_visita'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#e5ba31">
    <title><?= $modoMovilVisita ? 'App de visitas' : 'Visitas'; ?> | Conserje Smart</title>

    <!-- TITULO ESTILOS DE PAGINA VISITA -->
    <link rel="icon" type="image/png" href="/conserje_smart/imagenes/favicon/icono_conserje_smart.png">
    <link rel="stylesheet" href="/conserje_smart/css/programa/cabecera/cabecera.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/visita/visita.css?v=20261115-1">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/visita/formulario_visita.css?v=20261115-1">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita.css?v=20261115-2">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/visita/tablero_visita.css?v=20261115-1">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/navegacion_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/logo_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/contactos_pie_pagina.css">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/pie_pagina/creado_por_pie_pagina.css">

    <!-- TITULO RECURSOS DE APLICACION MOVIL VISITA -->
    <?php if ($modoMovilVisita): ?>
        <link rel="manifest" href="/conserje_smart/js/programa/conserje/inicio_conserje/modulos/visita/visita_appMovil/visita_appMovil.webmanifest">
        <link rel="apple-touch-icon" href="/conserje_smart/imagenes/programa/conserje/inicio_conserje/modulos/visita/visita_appMovil/icono_visita_192.png">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="Visitas">
        <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/visita/visita_appMovil.css?v=20261115-1">
    <?php endif; ?>

    <!-- TITULO IMPORTACIONES JS DE VISITA -->
    <?php include __DIR__ . '/recursos_visita.php'; ?>
</head>
<body class="<?= $modoMovilVisita ? 'modo_movil_visita' : 'modo_escritorio_visita'; ?>">
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
            </div>
            <!-- TITULO RESUMEN VISITA -->
            <div class="resumen_visita" id="resumen_visita">
                <span class="resumen_visita_titulo">Departamento</span>
                <strong id="resumen_departamento_visita">Sin selección</strong>
            </div>
        </section>

        <!-- TITULO ENLACE MODO VISITA -->
        <p class="enlace_modo_visita">
            <?php if ($modoMovilVisita): ?>
                <a href="visita.php">Ver tablero de visitas</a>
            <?php else: ?>
                <a href="visita_appMovil.php">Abrir app móvil para escanear el carnet</a>
            <?php endif; ?>
        </p>

        <!-- TITULO CONTENEDOR INSTALACION VISITA -->
        <?php if ($modoMovilVisita) include __DIR__ . '/visita_appMovil_instalacion.php'; ?>

        <!-- TITULO CONTENEDOR ESCANER VISITA -->
        <?php include __DIR__ . '/escanerQR_visita.php'; ?>

        <!-- TITULO CONTENEDOR FORMULARIO VISITA -->
        <?php include __DIR__ . '/formulario_visita.php'; ?>

        <!-- TITULO CONTENEDOR TABLERO VISITA -->
        <?php if (!$modoMovilVisita) include __DIR__ . '/tablero_visita.php'; ?>
    </main>

    <!-- TITULO CONTENEDOR PIE PAGINA -->
    <?php if (!$modoMovilVisita) include __DIR__ . '/../../pie_pagina/pie_pagina.php'; ?>

    <!-- TITULO INICIALIZACION JS DE VISITA -->
    <script type="module" src="<?= htmlspecialchars($importacionesVisita[$rutaJsVisita . 'visita.js'], ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
