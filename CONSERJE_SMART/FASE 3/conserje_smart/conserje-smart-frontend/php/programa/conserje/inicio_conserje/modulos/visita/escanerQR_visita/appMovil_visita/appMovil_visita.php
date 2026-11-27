<?php
// TITULO 1 PREPARAR LA SESION PROPIA DEL TELEFONO
require_once dirname(__DIR__, 9) . '/conserje-smart-backend/php/programa/conserje/inicio_conserje/modulos/visita/backend_visita/comun_visita.php';
iniciarSesionVisita();
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');
// TITULO 2 DEFINIR LOS RECURSOS COMPARTIDOS DEL MODULO VISITA
$directorioModuloVisita = dirname(__DIR__, 2);
$rutaCssAppMovil = '/conserje_smart/conserje-smart-frontend/css/programa/conserje/inicio_conserje/modulos/visita/';
$directorioCssAppMovil = dirname($directorioModuloVisita, 6) . '/css/programa/conserje/inicio_conserje/modulos/visita/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- TITULO CONFIGURACION DE APLICACION MOVIL VISITA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-visita" content="<?= htmlspecialchars($_SESSION['csrf_visita'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#e5ba31">
    <title>App de visitas | Conserje Smart</title>
    <link rel="manifest" href="/conserje_smart/conserje-smart-frontend/js/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/appMovil_visita/appMovil_visita.webmanifest">
    <link rel="apple-touch-icon" href="/conserje_smart/conserje-smart-frontend/imagenes/programa/conserje/inicio_conserje/modulos/visita/escanerQR_visita/appMovil_visita/icono_visita_192.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Visitas">
    <!-- TITULO ESTILOS DE APLICACION MOVIL VISITA -->
    <?php foreach (['visita.css', 'formulario_visita.css', 'escanerQR_visita/escanerQR_visita.css', 'escanerQR_visita/appMovil_visita/appMovil_visita.css'] as $estilo): ?>
        <link rel="stylesheet" href="<?= $rutaCssAppMovil . $estilo; ?>?v=<?= filemtime($directorioCssAppMovil . $estilo); ?>">
    <?php endforeach; ?>
    <!-- TITULO IMPORTACIONES JS DE VISITA -->
    <?php include __DIR__ . '/../../recursos_visita.php'; ?>
</head>
<body class="modo_movil_visita">
    <!-- TITULO PAGINA VISITA -->
    <main class="pagina_visita" id="pagina_visita">
        <!-- TITULO CABECERA VISITA -->
        <section class="cabecera_visita">
            <!-- TITULO TITULOS CABECERA VISITA -->
            <div class="titulos_cabecera_visita">
                <span class="etiqueta_seccion">Conserje Smart · App móvil</span>
                <h1>Escanear carnet</h1>
                <p class="descripcion_cabecera_visita">Tu teléfono lee. El computador registra.</p>
            </div>
        </section>
        <!-- TITULO ESTADO ACCESO MOVIL VISITA -->
        <section class="estado_acceso_movil_visita" id="estado_acceso_movil_visita">
            <h2 id="titulo_acceso_movil_visita">Vincular con el computador</h2>
            <p id="mensaje_acceso_movil_visita" aria-live="polite">Comprobando el acceso...</p>
            <strong class="codigo_acceso_movil_visita" id="codigo_acceso_movil_visita" hidden></strong>
            <p id="identidad_acceso_movil_visita" hidden></p>
            <!-- TITULO ACCIONES ACCESO MOVIL VISITA -->
            <div class="acciones_acceso_movil_visita">
                <button class="boton_secundario_visita" id="reintentar_acceso_movil_visita" type="button">Comprobar conexión</button>
                <button class="boton_secundario_visita" id="cerrar_acceso_movil_visita" type="button" hidden>Desvincular teléfono</button>
            </div>
        </section>
        <!-- TITULO CONTENIDO AUTORIZADO MOVIL VISITA -->
        <section class="contenido_autorizado_movil_visita" id="contenido_autorizado_movil_visita" hidden inert>
            <!-- TITULO CONTENEDOR ESCANER VISITA -->
            <?php include __DIR__ . '/../escanerQR_visita.php'; ?>
            <!-- TITULO LECTURA ENVIADA MOVIL VISITA -->
            <section class="lectura_enviada_movil_visita contenedor_formulario_visita">
                <h2>Lectura del carnet</h2>
                <p>RUT: <strong id="rut_enviado_movil_visita">Sin lectura</strong></p>
                <p id="estado_lectura_movil_visita" role="status" aria-live="polite">Escanea el QR del carnet. El nombre, departamento y motivo se revisan en el computador.</p>
            </section>
        </section>
        <!-- TITULO CONTENEDOR INSTALACION VISITA -->
        <?php include __DIR__ . '/appMovil_visita_instalacion.php'; ?>
    </main>
    <!-- TITULO INICIALIZACION JS DE VISITA -->
    <script type="module" src="<?= htmlspecialchars($importacionesVisita[$rutaJsVisita . 'visita.js'], ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>

