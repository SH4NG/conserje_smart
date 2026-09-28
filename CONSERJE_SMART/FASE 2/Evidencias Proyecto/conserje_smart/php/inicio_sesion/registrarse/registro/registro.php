        <!-- TITULO REGISTRO -->
        <div class="registro" id="registro">

            <!-- CONTENEDOR TITULO REGISTRAR -->
            <div class="contenedor_titulo_registrar" id="contenedor_titulo_registrar">
                <?php include __DIR__ . '/titulo_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR NOMBRE REGISTRAR -->
            <div class="contenedor_nombre_registrar" id="contenedor_nombre_registrar">
                <?php include __DIR__ . '/nombre_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR APELLIDO REGISTRAR -->
            <div class="contenedor_apellido_registrar" id="contenedor_apellido_registrar">
                <?php include __DIR__ . '/apellido_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR RUT REGISTRAR -->
            <div class="contenedor_rut_registrar" id="contenedor_rut_registrar">
                <?php include __DIR__ . '/rut_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR TELEFONO REGISTRAR -->
            <div class="contenedor_telefono_registrar" id="contenedor_telefono_registrar">
                <?php include __DIR__ . '/telefono_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR ROL REGISTRAR -->
            <div class="contenedor_rol_registrar" id="contenedor_rol_registrar">
                <?php include __DIR__ . '/rol_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR CONDOMINIO REGISTRAR -->
            <div class="contenedor_condominio_registrar" id="contenedor_condominio_registrar">
                <?php include __DIR__ . '/condominio_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR CONTRASEÑA REGISTRAR -->
            <div class="contenedor_contraseña_registrar" id="contenedor_contraseña_registrar">
                <?php include __DIR__ . '/contraseña_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR BOTON REGISTRAR -->
            <div class="contenedor_boton_registrar" id="contenedor_boton_registrar">
                <?php include __DIR__ . '/boton_registrar.php'; ?>
            </div>

            <!-- CONTENEDOR VENTANA EMERGENTE -->

            <dialog class="ventana_emergente" id="ventana_emergente">
                <?php include __DIR__ . '/validacion_temporal.php'; ?>
            </dialog>
        
        </div>

        <script src="/conserje_smart/js/inicio_sesion/registrarse/registro/registro.js"></script>
