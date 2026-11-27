<!-- TITULO TELEFONO REGISTRAR -->
<label for="telefono_registrar">Teléfono</label>
<input class="telefono_registrar" id="telefono_registrar" name="telefono" type="tel" autocomplete="tel" maxlength="24" placeholder="Ej.: +56 9 6669 1139" aria-describedby="ayuda_telefono_registrar error_telefono_registrar" required>
<small class="ayuda_campo_registro" id="ayuda_telefono_registrar">Acepta +56, espacios y guiones, o el número nacional de 9 dígitos.</small>
<p class="error_campo_registro" id="error_telefono_registrar" hidden></p>
<!-- TITULO FUNCIONES TELEFONO REGISTRAR -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/telefono_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/telefono_registrar.js'); ?>"></script>
