<!-- TITULO REGISTRO -->
<form class="registro" id="registro" method="post" action="/conserje_smart/conserje-smart-backend/php/inicio_sesion/registrarse/seguridad/registrar_usuario.php" novalidate aria-labelledby="titulo_registrar">
    <!-- TITULO CONTENEDOR TITULO REGISTRAR -->
    <div class="contenedor_titulo_registrar" id="contenedor_titulo_registrar">
        <?php include __DIR__ . '/titulo_registrar.php'; ?>
    </div>
    <!-- TITULO PROGRESO REGISTRO -->
    <div class="progreso_registro">
        <span id="detalle_progreso_registro">0 de 5 campos personales con datos</span>
        <progress id="progreso_registro" max="5" value="0" aria-labelledby="detalle_progreso_registro"></progress>
    </div>
    <!-- TITULO CONTENEDOR NOMBRE REGISTRAR -->
    <div class="contenedor_nombre_registrar" id="contenedor_nombre_registrar">
        <?php include __DIR__ . '/nombre_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR APELLIDO REGISTRAR -->
    <div class="contenedor_apellido_registrar" id="contenedor_apellido_registrar">
        <?php include __DIR__ . '/apellido_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR RUT REGISTRAR -->
    <div class="contenedor_rut_registrar" id="contenedor_rut_registrar">
        <?php include __DIR__ . '/rut_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR TELEFONO REGISTRAR -->
    <div class="contenedor_telefono_registrar" id="contenedor_telefono_registrar">
        <?php include __DIR__ . '/telefono_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR ROL REGISTRAR -->
    <div class="contenedor_rol_registrar" id="contenedor_rol_registrar">
        <?php include __DIR__ . '/rol_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR CONDOMINIO REGISTRAR -->
    <div class="contenedor_condominio_registrar" id="contenedor_condominio_registrar">
        <?php include __DIR__ . '/condominio_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR CONTRASEÑA REGISTRAR -->
    <div class="contenedor_contraseña_registrar" id="contenedor_contraseña_registrar">
        <?php include __DIR__ . '/contraseña_registrar.php'; ?>
    </div>
    <!-- TITULO CONTENEDOR BOTON REGISTRAR -->
    <div class="contenedor_boton_registrar" id="contenedor_boton_registrar">
        <?php include __DIR__ . '/boton_registrar.php'; ?>
    </div>
</form>
<!-- TITULO VENTANA EMERGENTE -->
<dialog class="ventana_emergente" id="ventana_emergente" aria-labelledby="titulo_validacion_temporal">
    <?php include __DIR__ . '/validacion_temporal.php'; ?>
</dialog>
<!-- TITULO FUNCIONES REGISTRO -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/registro.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/registro.js'); ?>"></script>

