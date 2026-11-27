<!-- TITULO CONTRASEÑA REGISTRAR -->
<label for="contraseña_registrar">Contraseña</label>
<!-- TITULO ENTRADA CONTRASEÑA REGISTRO -->
<div class="entrada_contraseña_registro">
    <input class="contraseña_registrar" id="contraseña_registrar" name="contraseña" type="password" autocomplete="new-password" maxlength="72" aria-describedby="requisitos_contraseña_registrar error_contraseña_registrar" required>
    <button type="button" id="mostrar_contraseña_registrar" aria-controls="contraseña_registrar" aria-pressed="false">Mostrar</button>
</div>
<!-- TITULO REQUISITOS CONTRASEÑA REGISTRAR -->
<ul class="requisitos_contraseña_registrar" id="requisitos_contraseña_registrar">
    <li data-requisito-registro="longitud"><span data-estado-requisito class="texto_lector_registro">Pendiente: </span>8 o más caracteres</li>
    <li data-requisito-registro="mayuscula"><span data-estado-requisito class="texto_lector_registro">Pendiente: </span>Una mayúscula</li>
    <li data-requisito-registro="minuscula"><span data-estado-requisito class="texto_lector_registro">Pendiente: </span>Una minúscula</li>
    <li data-requisito-registro="numero"><span data-estado-requisito class="texto_lector_registro">Pendiente: </span>Un número</li>
</ul>
<p class="error_campo_registro" id="error_contraseña_registrar" hidden></p>
<!-- TITULO FUNCIONES CONTRASEÑA REGISTRAR -->
<script src="/conserje_smart/conserje-smart-frontend/js/inicio_sesion/registrarse/registro/contraseña_registrar.js?v=<?= filemtime(dirname(__DIR__, 4) . '/js/inicio_sesion/registrarse/registro/contraseña_registrar.js'); ?>"></script>
