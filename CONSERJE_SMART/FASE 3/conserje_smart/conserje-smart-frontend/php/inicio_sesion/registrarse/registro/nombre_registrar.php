<!-- TITULO NOMBRE REGISTRAR -->
<label for="nombre_registrar">Nombre</label>
<input class="nombre_registrar" id="nombre_registrar" name="nombre" type="text" autocomplete="given-name" maxlength="100" placeholder="Ej.: Angeloti" aria-describedby="ayuda_nombre_registrar error_nombre_registrar" required>
<small class="ayuda_campo_registro" id="ayuda_nombre_registrar">Conservaremos las mayúsculas y los acentos que escribas.</small>
<p class="error_campo_registro" id="error_nombre_registrar" hidden></p>
<!-- TITULO FUNCIONES NOMBRE REGISTRAR -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/nombre_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/nombre_registrar.js'); ?>"></script>
