<!-- TITULO SECCION VISITA ADMIN -->
<section class="seccion_visita_admin tarjeta_admin" data-panel-admin="visita" <?= $seccion !== 'visita' ? 'hidden' : ''; ?>>
    
    <!-- TITULO ENCABEZADO SECCION ADMIN -->
    <div class="encabezado_seccion_admin">
        <div class="titulos_seccion_admin">
            <span class="etiqueta_admin">Administración</span>
            <h2 id="titulo_visita_admin">Visitas</h2>
            <p>Revisa el registro de ingresos y salidas de tu condominio.</p>
        </div>
        <button class="boton_admin secundario_admin" type="button" id="actualizar_visita_admin">Actualizar</button>
    </div>

    <!-- TITULO FILTROS VISITA ADMIN -->
    <form class="filtros_admin" id="filtros_visita_admin">
        <label class="campo_admin"><span>Buscar</span><input type="search" name="buscar" maxlength="100" placeholder="Nombre, RUT o departamento"></label>
        <label class="campo_admin"><span>Estado</span><select name="estado"><option value="activas">En el condominio</option><option value="finalizadas">Finalizadas</option><option value="todas">Todas</option></select></label>
        <button class="boton_admin secundario_admin" type="submit">Buscar</button>
    </form>

    <!-- TITULO MENSAJE VISITA ADMIN -->
    <p class="mensaje_admin" id="mensaje_visita_admin" role="status" aria-live="polite"></p>

    <!-- TITULO TABLA DESPLAZABLE ADMIN -->
    <div class="tabla_desplazable_admin" role="region" aria-labelledby="titulo_visita_admin" tabindex="0">
        <table class="tabla_admin">
            <thead><tr><th scope="col">Registro / estado</th><th scope="col">Visitante</th><th scope="col">Departamento</th><th scope="col">Motivo / observaciones</th><th scope="col">Ingreso / salida</th><th scope="col">Conserje</th></tr></thead>
            <tbody id="filas_visita_admin"><tr><td colspan="6">Cargando registros...</td></tr></tbody>
        </table>
    </div>

    <!-- TITULO PAGINACION VISITA ADMIN -->
    <nav class="paginacion_admin" aria-label="Páginas de visitas">
        <button class="boton_admin secundario_admin" type="button" id="anterior_visita_admin" disabled>Anterior</button>
        <span id="pagina_visita_admin" aria-live="polite">Página 1</span>
        <button class="boton_admin secundario_admin" type="button" id="siguiente_visita_admin" disabled>Siguiente</button>
    </nav>
</section>
