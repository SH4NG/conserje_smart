<!-- TITULO APELLIDO REGISTRAR -->
<label for="apellido_registrar">Apellidos</label>
<input class="apellido_registrar" id="apellido_registrar" name="apellido" type="text" autocomplete="family-name" maxlength="100" placeholder="Ej.: Hernández Pérez" aria-describedby="ayuda_apellido_registrar error_apellido_registrar" required>
<small class="ayuda_campo_registro" id="ayuda_apellido_registrar">Puedes escribir apellidos compuestos, con guiones o apóstrofes.</small>
<p class="error_campo_registro" id="error_apellido_registrar" hidden></p>
<!-- TITULO FUNCIONES APELLIDO REGISTRAR -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/apellido_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/apellido_registrar.js'); ?>"></script>
