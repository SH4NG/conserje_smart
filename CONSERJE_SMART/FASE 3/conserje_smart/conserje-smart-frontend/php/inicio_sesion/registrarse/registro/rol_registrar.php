    <!-- TITULO ROL REGISTRAR -->
    <!-- permite seleccionar el rol del usuario -->
    <label for="rol_registrar">Perfil de acceso</label>
    <select aria-describedby="error_rol_registrar" required class="rol_registrar" id="rol_registrar" name="rol">
        <!-- opcion inicial sin rol seleccionado -->
        <option value="">Seleccionar rol</option>
        <!-- roles disponibles para el registro -->
        <option value="admin">Administrador</option>
        <option value="conserje">Conserje</option>
        <option value="residente">Residente</option>
    </select>

    <p class="error_campo_registro" id="error_rol_registrar" hidden></p>
    <!-- TITULO FUNCIONES ROL REGISTRAR -->
    <script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/rol_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/rol_registrar.js'); ?>"></script>
