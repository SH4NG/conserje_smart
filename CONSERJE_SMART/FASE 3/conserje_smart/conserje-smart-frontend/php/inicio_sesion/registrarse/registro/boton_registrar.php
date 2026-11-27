<!-- TITULO BOTON REGISTRAR -->
<button class="boton_registrar" id="boton_registrar" type="submit">
    <span id="texto_boton_registrar">Crear cuenta</span><span class="carga_boton_registrar" aria-hidden="true"></span>
</button>
<!-- TITULO MENSAJE REGISTRO -->
<p class="mensaje_registro" id="mensaje_registro" role="status" aria-live="polite" tabindex="-1" hidden></p>
<a class="acceso_login_registro" id="acceso_login_registro" href="/conserje_smart/conserje-smart-frontend/php/inicio_sesion/inicio_sesion.php">Ya tengo una cuenta. Iniciar sesión</a>
<!-- TITULO FUNCIONES BOTON REGISTRAR -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/boton_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/boton_registrar.js'); ?>"></script>
