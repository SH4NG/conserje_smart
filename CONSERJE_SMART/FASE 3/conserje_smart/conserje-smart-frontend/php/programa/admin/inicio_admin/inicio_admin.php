<?php
// TITULO 1 PREPARAR LA SESION Y COMPROBAR EL PERFIL ADMINISTRADOR
$raizFrontend = dirname(__DIR__, 4);
$raizProyecto = dirname($raizFrontend);
require_once $raizProyecto . '/conserje-smart-backend/php/programa/admin/inicio_admin/modulos/autorizaciones/backend_autorizaciones/comun_admin.php';
iniciarSesionAdmin();
if (($_SESSION['estado'] ?? '') !== 'sesion_iniciada') {
    header('Location: /conserje_smart/conserje-smart-frontend/php/inicio_sesion/inicio_sesion.php');
    exit;
}
if (($_SESSION['rol_usuario'] ?? '') !== 'admin' || !enteroAdmin($_SESSION['id_usuario'] ?? null)
    || !enteroAdmin($_SESSION['id_condominio_fk'] ?? null)) {
    http_response_code(403);
    exit('Acceso exclusivo del administrador.');
}
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
$seccion = $_GET['seccion'] ?? 'resumen';
$secciones = ['resumen' => 'Inicio', 'bitacora' => 'Bitácora', 'paqueteria' => 'Paquetería',
    'visita' => 'Visitas', 'infraestructura' => 'Infraestructura', 'autorizaciones' => 'Autorizaciones'];
if (!is_string($seccion) || !isset($secciones[$seccion])) $seccion = 'resumen';

// TITULO 2 VERSIONAR LOS RECURSOS PROPIOS DEL ADMINISTRADOR
$rutaJs = '/conserje_smart/conserje-smart-frontend/js/programa/admin/inicio_admin/';
$rutaCss = '/conserje_smart/conserje-smart-frontend/css/programa/admin/inicio_admin/';
$importaciones = [];
foreach (['inicio_admin.js', 'modulos/autorizaciones/backend_autorizaciones/api_admin.js',
    'modulos/bitacora/bitacora_admin.js', 'modulos/paqueteria/paqueteria_admin.js',
    'modulos/infraestructura/infraestructura_admin.js', 'modulos/infraestructura/backend_infraestructura/infraestructura_api_admin.js', 'modulos/visita/visita_admin.js', 'modulos/autorizaciones/autorizaciones_admin.js'] as $archivo) {
    $importaciones[$rutaJs . $archivo] = $rutaJs . $archivo . '?v=' . substr(hash_file('sha256',
        $raizFrontend . '/js/programa/admin/inicio_admin/' . $archivo), 0, 12);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>

    <!-- TITULO CONFIGURACION DEL INICIO ADMIN -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-admin" content="<?= htmlspecialchars($_SESSION['csrf_admin'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#e5ba31">
    <title>Administración | Conserje Smart</title>
    <link rel="icon" href="/conserje_smart/conserje-smart-frontend/imagenes/favicon/icono_conserje_smart.png">

    <!-- TITULO ESTILOS DEL INICIO ADMIN -->
    <link rel="stylesheet" href="/conserje_smart/conserje-smart-frontend/css/programa/cabecera/cabecera.css">
    <?php foreach (['inicio_admin.css', 'modulos/bitacora/bitacora_admin.css', 'modulos/paqueteria/paqueteria_admin.css',
        'modulos/infraestructura/infraestructura_admin.css', 'modulos/visita/visita_admin.css', 'modulos/autorizaciones/autorizaciones_admin.css'] as $archivo): ?>
        <link rel="stylesheet" href="<?= $rutaCss . $archivo; ?>?v=<?= filemtime($raizFrontend . '/css/programa/admin/inicio_admin/' . $archivo); ?>">
    <?php endforeach; ?>

    <!-- TITULO IMPORTACIONES DEL INICIO ADMIN -->
    <script type="importmap"><?= json_encode(['imports' => $importaciones], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
</head>
<body class="modo_admin">

    <!-- TITULO CABECERA -->
    <?php include __DIR__ . '/../../cabecera/cabecera.php'; ?>

    <!-- TITULO PAGINA ADMIN -->
    <main class="pagina_admin" id="pagina_admin" data-seccion="<?= $seccion; ?>">

        <!-- TITULO CABECERA ADMIN -->
        <header class="cabecera_admin">

            <!-- TITULO TITULOS CABECERA ADMIN -->
            <div class="titulos_cabecera_admin">
                <span class="etiqueta_admin">Gestión del condominio</span>
                <h1>Panel de administración</h1>
                <p id="nombre_condominio_admin">Consultando tu comunidad...</p>
            </div>

            <!-- TITULO FECHA CABECERA ADMIN -->
            <div class="fecha_cabecera_admin">
                <span class="perfil_admin">Administrador</span>
                <time id="fecha_admin"></time>
            </div>
        </header>

        <!-- TITULO NAVEGACION ADMIN -->
        <nav class="navegacion_admin" aria-label="Secciones del administrador">
            <?php foreach ($secciones as $clave => $titulo): ?>
                <a href="?seccion=<?= $clave; ?>" data-seccion-admin="<?= $clave; ?>" <?= $seccion === $clave ? 'aria-current="page"' : ''; ?>>
                    <?= $titulo; ?>
                    <?php if ($clave === 'autorizaciones'): ?><span class="contador_admin" data-total-admin="autorizaciones" aria-label="Solicitudes pendientes">—</span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- TITULO MENSAJE GLOBAL ADMIN -->
        <p class="mensaje_admin" id="mensaje_global_admin" role="status" aria-live="polite"></p>

        <!-- TITULO RESUMEN ADMIN -->
        <section class="resumen_admin" data-panel-admin="resumen" <?= $seccion !== 'resumen' ? 'hidden' : ''; ?>>

            <!-- TITULO ACCESOS ADMIN -->
            <div class="accesos_admin">
                <?php
                $accesos = [
                    'bitacora' => ['Bitácora', 'Revisa y edita las novedades del turno.', 'Novedades registradas', 'M4 4h6a3 3 0 0 1 3 3v14a4 4 0 0 0-4-2H4z M13 7a3 3 0 0 1 3-3h5v15h-5a4 4 0 0 0-3 2'],
                    'paqueteria' => ['Paquetería', 'Consulta las entregas y los retiros.', 'Paquetes pendientes', 'M3 7l9-4 9 4v10l-9 4-9-4z M3 7l9 4 9-4 M12 11v10 M7 5l9 4'],
                    'infraestructura' => ['Infraestructura', 'Configura torres, pisos y numeración.', 'Departamentos registrados', 'M3 21h18 M5 21V3h14v18 M9 7h1 M14 7h1 M9 11h1 M14 11h1 M10 21v-5h4v5'],
                    'visita' => ['Visitas', 'Consulta ingresos, destinos y salidas.', 'Visitas en el condominio', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M17 8h5 M20 5l3 3-3 3']
                ];
                foreach ($accesos as $clave => [$titulo, $descripcion, $indicador, $dibujo]): ?>

                    <!-- TITULO TARJETA ACCESO ADMIN -->
                    <a class="tarjeta_acceso_admin" href="?seccion=<?= $clave; ?>" data-seccion-admin="<?= $clave; ?>">
                        <span class="icono_acceso_admin" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="<?= $dibujo; ?>"></path></svg></span>
                        <h2><?= $titulo; ?><span aria-hidden="true">↗</span></h2>
                        <p><?= $descripcion; ?></p>
                        <strong data-total-admin="<?= $clave; ?>">—</strong>
                        <span class="detalle_acceso_admin"><?= $indicador; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- TITULO BANDEJA RESUMEN ADMIN -->
            <section class="bandeja_resumen_admin tarjeta_admin">

                <!-- TITULO ENCABEZADO SECCION ADMIN -->
                <div class="encabezado_seccion_admin">
                    <div class="titulos_seccion_admin">
                        <span class="etiqueta_admin">Pendiente de tu revisión</span>
                        <h2>Solicitudes de conserjes</h2>
                        <p>Autoriza las nuevas cuentas de tu condominio.</p>
                    </div>
                    <a class="boton_admin secundario_admin" href="?seccion=autorizaciones" data-seccion-admin="autorizaciones">Abrir bandeja <span aria-hidden="true">→</span></a>
                </div>

                <!-- TITULO LISTA RESUMEN SOLICITUDES ADMIN -->
                <div class="lista_resumen_solicitudes_admin" id="lista_resumen_solicitudes_admin" aria-live="polite">
                    <p class="estado_vacio_admin">Cargando solicitudes...</p>
                </div>
                <p class="nota_admin">Las cuentas pendientes no pueden iniciar sesión. Cada decisión queda asociada al administrador que la realizó.</p>
            </section>
        </section>

        <!-- TITULO SECCIONES DEL ADMINISTRADOR -->
        <?php foreach (['bitacora', 'paqueteria', 'visita', 'infraestructura', 'autorizaciones'] as $modulo): ?>
            <?php include __DIR__ . '/modulos/' . $modulo . '/' . $modulo . '_admin.php'; ?>
        <?php endforeach; ?>

        <!-- TITULO PIE ADMIN -->
        <footer class="pie_admin">Conserje Smart <span>Administración de tu comunidad</span></footer>
    </main>
    
    <!-- TITULO INICIALIZACION DEL INICIO ADMIN -->
    <script type="module" src="<?= htmlspecialchars($importaciones[$rutaJs . 'inicio_admin.js'], ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
