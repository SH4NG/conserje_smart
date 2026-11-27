<!-- TITULO SECCION PAQUETERIA ADMIN -->
<section class="seccion_paqueteria_admin tarjeta_admin" data-panel-admin="paqueteria" <?= $seccion !== 'paqueteria' ? 'hidden' : ''; ?>>
    
    <!-- TITULO ENCABEZADO SECCION ADMIN -->
    <div class="encabezado_seccion_admin">
        <div class="titulos_seccion_admin">
            <span class="etiqueta_admin">Administración</span>
            <h2 id="titulo_paqueteria_admin">Paquetería</h2>
            <p>Consulta los paquetes recibidos, retirados y archivados.</p>
        </div>
        <button class="boton_admin secundario_admin" type="button" id="actualizar_paqueteria_admin">Actualizar</button>
    </div>

    <!-- TITULO FILTROS PAQUETERIA ADMIN -->
    <form class="filtros_admin" id="filtros_paqueteria_admin">
        <label class="campo_admin"><span>Buscar</span><input type="search" name="buscar" maxlength="100" placeholder="Empresa, código o departamento"></label>
        <label class="campo_admin"><span>Estado</span><select name="estado"><option value="Pendiente">Pendientes</option><option value="Retirado">Retirados</option><option value="todos">Todos</option></select></label>
        <button class="boton_admin secundario_admin" type="submit">Buscar</button>
    </form>

    <!-- TITULO MENSAJE PAQUETERIA ADMIN -->
    <p class="mensaje_admin" id="mensaje_paqueteria_admin" role="status" aria-live="polite"></p>
    
    <!-- TITULO TABLA DESPLAZABLE ADMIN -->
    <div class="tabla_desplazable_admin" role="region" aria-labelledby="titulo_paqueteria_admin" tabindex="0">
        <table class="tabla_admin">
            <thead><tr><th scope="col">Registro / estado</th><th scope="col">Departamento</th><th scope="col">Envío</th><th scope="col">Recepción / retiro</th><th scope="col">Conserje</th></tr></thead>
            <tbody id="filas_paqueteria_admin"><tr><td colspan="5">Cargando registros...</td></tr></tbody>
        </table>
    </div>
    
    <!-- TITULO PAGINACION PAQUETERIA ADMIN -->
    <nav class="paginacion_admin" aria-label="Páginas de paquetería">
        <button class="boton_admin secundario_admin" type="button" id="anterior_paqueteria_admin" disabled>Anterior</button>
        <span id="pagina_paqueteria_admin" aria-live="polite">Página 1</span>
        <button class="boton_admin secundario_admin" type="button" id="siguiente_paqueteria_admin" disabled>Siguiente</button>
    </nav>
</section>
