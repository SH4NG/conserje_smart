<!-- TITULO RUT REGISTRAR -->
<label for="rut_registrar">RUT</label>
<input class="rut_registrar" id="rut_registrar" name="rut" type="text" autocomplete="off" maxlength="12" spellcheck="false" autocapitalize="characters" placeholder="Ej.: 20.492.799-5" aria-describedby="ayuda_rut_registrar error_rut_registrar" required>
<small class="ayuda_campo_registro" id="ayuda_rut_registrar">Escribe los números y el dígito verificador. Los puntos y el guion se agregan automáticamente; también admite K.</small>
<p class="error_campo_registro" id="error_rut_registrar" hidden></p>
<!-- TITULO FUNCIONES RUT REGISTRAR -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/rut_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/rut_registrar.js'); ?>"></script>
