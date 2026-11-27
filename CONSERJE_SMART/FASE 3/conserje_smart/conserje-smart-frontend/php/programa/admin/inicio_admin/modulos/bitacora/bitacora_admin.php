<!-- TITULO SECCION BITACORA ADMIN -->
<section class="seccion_bitacora_admin tarjeta_admin" data-panel-admin="bitacora" <?= $seccion !== 'bitacora' ? 'hidden' : ''; ?>>
    
    <!-- TITULO ENCABEZADO SECCION ADMIN -->
    <div class="encabezado_seccion_admin">
        <div class="titulos_seccion_admin">
            <span class="etiqueta_admin">Administración</span>
            <h2 id="titulo_bitacora_admin">Bitácora</h2>
            <p>Consulta y edita las novedades registradas por los conserjes.</p>
        </div>
        <button class="boton_admin secundario_admin" type="button" id="actualizar_bitacora_admin">Actualizar</button>
    </div>

    <!-- TITULO FILTROS BITACORA ADMIN -->
    <form class="filtros_admin" id="filtros_bitacora_admin">
        <label class="campo_admin"><span>Buscar</span><input type="search" name="buscar" maxlength="100" placeholder="Título, descripción o lugar"></label>
        
        <button class="boton_admin secundario_admin" type="submit">Buscar</button>
    </form>

    <!-- TITULO MENSAJE BITACORA ADMIN -->
    <p class="mensaje_admin" id="mensaje_bitacora_admin" role="status" aria-live="polite"></p>
    
    <!-- TITULO TABLA DESPLAZABLE ADMIN -->
    <div class="tabla_desplazable_admin" role="region" aria-labelledby="titulo_bitacora_admin" tabindex="0">
        <table class="tabla_admin">
            <thead><tr><th scope="col">Registro / fecha</th><th scope="col">Novedad</th><th scope="col">Clasificación / lugar</th><th scope="col">Conserje</th><th scope="col">Acciones</th></tr></thead>
            <tbody id="filas_bitacora_admin"><tr><td colspan="5">Cargando registros...</td></tr></tbody>
        </table>
    </div>

    <!-- TITULO PAGINACION BITACORA ADMIN -->
    <nav class="paginacion_admin" aria-label="Páginas de bitácora">
        <button class="boton_admin secundario_admin" type="button" id="anterior_bitacora_admin" disabled>Anterior</button>
        <span id="pagina_bitacora_admin" aria-live="polite">Página 1</span>
        <button class="boton_admin secundario_admin" type="button" id="siguiente_bitacora_admin" disabled>Siguiente</button>
    </nav>
</section>

<!-- TITULO DIALOGO BITACORA ADMIN -->
<dialog class="dialogo_admin dialogo_bitacora_admin" id="dialogo_bitacora_admin" aria-labelledby="titulo_edicion_bitacora_admin">
    
    <!-- TITULO FORMULARIO BITACORA ADMIN -->
    <form class="formulario_admin" id="formulario_bitacora_admin">
        <div class="encabezado_seccion_admin">
            <div class="titulos_seccion_admin"><span class="etiqueta_admin">Revisión de novedades</span><h2 id="titulo_edicion_bitacora_admin">Editar novedad</h2></div>
            <button type="button" class="boton_admin secundario_admin" data-cerrar-dialogo aria-label="Cerrar edición de bitácora">Cerrar</button>
        </div>
        <p class="nota_admin">Se conservan el autor, la fecha de registro y los metadatos del análisis original.</p>
        <input type="hidden" name="id_bitacora"><input type="hidden" name="version">
        <label class="campo_admin"><span>Título</span><input name="titulo" maxlength="150" required></label>
        <label class="campo_admin"><span>Descripción</span><textarea name="descripcion" maxlength="10000" rows="4" required></textarea></label>
        <div class="grupo_campos_admin">
            <label class="campo_admin"><span>Categoría</span><input name="categoria" maxlength="40"></label>
            <label class="campo_admin"><span>Tipo de lugar</span><input name="tipo_lugar" maxlength="40"></label>
            <label class="campo_admin"><span>Lugar</span><input name="lugar" maxlength="150"></label>
            <label class="campo_admin"><span>Fecha del evento</span><input type="date" name="fecha_evento"></label>
            <label class="campo_admin"><span>Hora del evento</span><input type="time" step="1" name="hora_evento"></label>
        </div>
        <label class="campo_admin"><span>Observaciones</span><textarea name="observaciones" maxlength="5000" rows="3"></textarea></label>
        <p class="mensaje_admin" id="mensaje_edicion_bitacora_admin" role="status" aria-live="polite"></p>
        <div class="acciones_formulario_admin"><button type="button" class="boton_admin secundario_admin" data-cerrar-dialogo>Cancelar</button><button type="submit" class="boton_admin principal_admin">Guardar cambios</button></div>
    </form>
</dialog>
