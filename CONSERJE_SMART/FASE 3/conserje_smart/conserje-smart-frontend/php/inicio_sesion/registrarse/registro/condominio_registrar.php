    <!-- TITULO CONDOMINIO REGISTRAR -->
    <!-- muestra los condominios cargados desde la base de datos -->
    <label for="condominio_registrar">Condominio</label>
    <select aria-describedby="estado_invitacion_registro error_condominio_registrar" required class="condominio_registrar" id="condominio_registrar" name="condominio">
        <!-- opcion inicial sin condominio seleccionado -->
        <option value="">Seleccionar condominio</option>
    </select>

    <!-- TITULO ESTADO INVITACION REGISTRO -->
    <p class="estado_invitacion_registro" id="estado_invitacion_registro" role="status" aria-live="polite" hidden></p>

    <p class="error_campo_registro" id="error_condominio_registrar" hidden></p>
    <!-- TITULO FUNCIONES CONDOMINIO REGISTRAR -->
    <script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/condominio_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/condominio_registrar.js'); ?>"></script>
