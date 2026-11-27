<!-- TITULO SECCION AUTORIZACIONES ADMIN -->
<section class="seccion_autorizaciones_admin tarjeta_admin" data-panel-admin="autorizaciones" <?= $seccion !== 'autorizaciones' ? 'hidden' : ''; ?>>
    
    <!-- TITULO ENCABEZADO SECCION ADMIN -->
    <div class="encabezado_seccion_admin">
        <div class="titulos_seccion_admin">
            <span class="etiqueta_admin">Administración</span>
            <h2 id="titulo_autorizaciones_admin">Autorizaciones</h2>
            <p>Aprueba o rechaza las solicitudes de nuevas cuentas de conserje.</p>
        </div>
        <button class="boton_admin secundario_admin" type="button" id="actualizar_autorizaciones_admin">Actualizar</button>
    </div>

    <!-- TITULO INVITACION CONSERJE ADMIN -->
    <section class="invitacion_conserje_admin">
        <h3>Invitar un conserje</h3>
        <p class="nota_admin">La invitación fija el condominio de tu sesión. El conserje solicita su cuenta; tú revisas sus datos y confirmas la aprobación con tu código personal.</p>
        <form class="filtros_admin" id="formulario_invitar_conserje_admin">
            <label class="campo_admin"><span>Vigencia del enlace</span><select name="horas"><option value="1">1 hora</option><option value="24" selected>24 horas</option><option value="72">72 horas</option></select></label>
            <button type="submit" class="boton_admin principal_admin">Crear invitación</button>
        </form>
        <p class="mensaje_admin" id="mensaje_crear_invitacion_admin" role="status" aria-live="polite"></p>
        <!-- TITULO ENLACE INVITACION ADMIN -->
        <div class="enlace_invitacion_admin" id="enlace_invitacion_admin" hidden>
            <label class="campo_admin"><span>Enlace de registro para compartir</span><textarea id="url_invitacion_admin" rows="3" readonly></textarea></label>
            <button type="button" class="boton_admin secundario_admin" id="copiar_invitacion_admin">Copiar enlace</button>
            <p class="nota_admin">El enlace se muestra solo al crearlo y permite una solicitud. Si lo pierdes, revócalo y genera uno nuevo.</p>
        </div>
    </section>

    <!-- TITULO ENCABEZADO SOLICITUDES ADMIN -->
    <h3 class="subtitulo_autorizaciones_admin">Solicitudes por revisar e historial</h3>
    
    <!-- TITULO FILTROS AUTORIZACIONES ADMIN -->
    <form class="filtros_admin" id="filtros_autorizaciones_admin">
        <label class="campo_admin"><span>Buscar</span><input type="search" name="buscar" maxlength="100" placeholder="Nombre, RUT o correo"></label>
        <label class="campo_admin"><span>Estado</span><select name="estado"><option value="pendiente">Pendientes</option><option value="aprobada">Aprobadas</option><option value="rechazada">Rechazadas</option><option value="todas">Todas</option></select></label>
        <button class="boton_admin secundario_admin" type="submit">Buscar</button>
    </form>

    <!-- TITULO MENSAJE AUTORIZACIONES ADMIN -->
    <p class="mensaje_admin" id="mensaje_autorizaciones_admin" role="status" aria-live="polite"></p>
    
    <!-- TITULO TABLA DESPLAZABLE ADMIN -->
    <div class="tabla_desplazable_admin" role="region" aria-labelledby="titulo_autorizaciones_admin" tabindex="0">
        <table class="tabla_admin">
            <thead><tr><th scope="col">Solicitud / estado</th><th scope="col">Conserje</th><th scope="col">Contacto</th><th scope="col">Fecha / resolución</th><th scope="col">Acciones</th></tr></thead>
            <tbody id="filas_autorizaciones_admin"><tr><td colspan="5">Cargando registros...</td></tr></tbody>
        </table>
    </div>

    <!-- TITULO PAGINACION AUTORIZACIONES ADMIN -->
    <nav class="paginacion_admin" aria-label="Páginas de autorizaciones">
        <button class="boton_admin secundario_admin" type="button" id="anterior_autorizaciones_admin" disabled>Anterior</button>
        <span id="pagina_autorizaciones_admin" aria-live="polite">Página 1</span>
        <button class="boton_admin secundario_admin" type="button" id="siguiente_autorizaciones_admin" disabled>Siguiente</button>
    </nav>

    <!-- TITULO HISTORIAL INVITACIONES ADMIN -->
    <section class="historial_invitaciones_admin" aria-labelledby="titulo_invitaciones_admin">
        <div class="encabezado_seccion_admin">
            <h3 id="titulo_invitaciones_admin">Invitaciones generadas</h3>
            <button class="boton_admin secundario_admin" type="button" id="actualizar_invitaciones_admin">Actualizar</button>
        </div>
        <!-- TITULO FILTROS INVITACIONES ADMIN -->
        <form class="filtros_admin" id="filtros_invitaciones_admin" hidden>
            
        </form>
        <p class="mensaje_admin" id="mensaje_invitaciones_admin" role="status" aria-live="polite"></p>
        <!-- TITULO TABLA DESPLAZABLE ADMIN -->
        <div class="tabla_desplazable_admin" role="region" aria-labelledby="titulo_invitaciones_admin" tabindex="0">
            <table class="tabla_admin">
                <thead><tr><th scope="col">Invitación / estado</th><th scope="col">Creación / vencimiento</th><th scope="col">Creada por / uso</th><th scope="col">Acciones</th></tr></thead>
                <tbody id="filas_invitaciones_admin"><tr><td colspan="4">Cargando registros...</td></tr></tbody>
            </table>
        </div>
        <!-- TITULO PAGINACION INVITACIONES ADMIN -->
        <nav class="paginacion_admin" aria-label="Páginas de invitaciones">
            <button class="boton_admin secundario_admin" type="button" id="anterior_invitaciones_admin" disabled>Anterior</button>
            <span id="pagina_invitaciones_admin" aria-live="polite">Página 1</span>
            <button class="boton_admin secundario_admin" type="button" id="siguiente_invitaciones_admin" disabled>Siguiente</button>
        </nav>
    </section>

    <!-- TITULO CONSERJES ASIGNADOS ADMIN -->
    <section class="conserjes_asignados_admin" aria-labelledby="titulo_conserjes_admin">
        <div class="encabezado_seccion_admin">
            <h3 id="titulo_conserjes_admin">Conserjes asignados a tu condominio</h3>
            <button class="boton_admin secundario_admin" type="button" id="actualizar_conserjes_admin">Actualizar</button>
        </div>
        <!-- TITULO FILTROS CONSERJES ADMIN -->
        <form class="filtros_admin" id="filtros_conserjes_admin">
            <label class="campo_admin"><span>Buscar conserje</span><input type="search" name="buscar" maxlength="100" placeholder="Nombre, RUT o correo"></label><button class="boton_admin secundario_admin" type="submit">Buscar</button>
        </form>
        <p class="mensaje_admin" id="mensaje_conserjes_admin" role="status" aria-live="polite"></p>
        <!-- TITULO TABLA DESPLAZABLE ADMIN -->
        <div class="tabla_desplazable_admin" role="region" aria-labelledby="titulo_conserjes_admin" tabindex="0">
            <table class="tabla_admin">
                <thead><tr><th scope="col">Cuenta / estado</th><th scope="col">Conserje</th><th scope="col">Contacto</th><th scope="col">Solicitud / resolución</th></tr></thead>
                <tbody id="filas_conserjes_admin"><tr><td colspan="4">Cargando registros...</td></tr></tbody>
            </table>
        </div>
        <!-- TITULO PAGINACION CONSERJES ADMIN -->
        <nav class="paginacion_admin" aria-label="Páginas de conserjes">
            <button class="boton_admin secundario_admin" type="button" id="anterior_conserjes_admin" disabled>Anterior</button>
            <span id="pagina_conserjes_admin" aria-live="polite">Página 1</span>
            <button class="boton_admin secundario_admin" type="button" id="siguiente_conserjes_admin" disabled>Siguiente</button>
        </nav>
    </section>
</section>

<!-- TITULO DIALOGO AUTORIZACIONES ADMIN -->
<dialog class="dialogo_admin dialogo_autorizaciones_admin" id="dialogo_autorizaciones_admin" aria-labelledby="titulo_decision_admin">
    
<!-- TITULO FORMULARIO AUTORIZACIONES ADMIN -->
    <form class="formulario_admin" id="formulario_autorizaciones_admin" method="post" action="/conserje_smart/conserje-smart-backend/php/programa/admin/inicio_admin/modulos/autorizaciones/backend_autorizaciones/resolver_solicitud_admin.php">
        <span class="etiqueta_admin">Decisión del administrador</span>
        <h2 id="titulo_decision_admin">Resolver solicitud</h2>
        <p id="identidad_solicitud_admin"></p>
        <p class="nota_admin" id="ayuda_decision_admin"></p>
        <input type="hidden" name="id_solicitud"><input type="hidden" name="accion">
        <label class="campo_admin"><span id="etiqueta_motivo_admin">Observaciones</span><textarea name="motivo" maxlength="500" rows="3"></textarea></label>
        <!-- TITULO CONFIRMACION APROBACION ADMIN -->
        <section class="confirmacion_aprobacion_admin" id="confirmacion_aprobacion_admin" aria-labelledby="titulo_confirmacion_admin" hidden>
            <h3 id="titulo_confirmacion_admin">Confirma con tu cuenta</h3>
            <p class="nota_admin" id="nombre_admin_decision"></p>
            <label class="campo_admin" for="rut_admin_decision"><span>RUT del administrador autenticado</span>
                <input id="rut_admin_decision" type="text" readonly aria-describedby="ayuda_codigo_decision_admin">
            </label>
            <label class="campo_admin" for="codigo_auth_decision_admin"><span>Código personal de autorización</span>
                <input id="codigo_auth_decision_admin" name="codigo_auth_admin" type="password" maxlength="255" autocomplete="off" spellcheck="false" autocapitalize="none" aria-describedby="ayuda_codigo_decision_admin" disabled>
            </label>
            <p class="nota_admin" id="ayuda_codigo_decision_admin">Escribe el código de autorización de tu cuenta. El conserje no necesita conocerlo.</p>
        </section>
        <!-- TITULO MENSAJE DECISION ADMIN -->
        <p class="mensaje_admin" id="mensaje_decision_admin" role="status" aria-live="polite"></p>
        <div class="acciones_formulario_admin"><button type="button" class="boton_admin secundario_admin" data-cerrar-dialogo>Cancelar</button><button type="submit" class="boton_admin principal_admin" id="confirmar_decision_admin">Confirmar decisión</button></div>
    </form>
</dialog>
