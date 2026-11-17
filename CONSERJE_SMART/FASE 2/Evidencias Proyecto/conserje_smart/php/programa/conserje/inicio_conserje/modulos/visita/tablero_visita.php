<!-- TITULO CONTENEDOR TABLERO VISITA -->
<section class="contenedor_tablero_visita" id="contenedor_tablero_visita" aria-labelledby="titulo_tablero_visita">
    <!-- TITULO ENCABEZADO TABLERO VISITA -->
    <div class="encabezado_tablero_visita">
        <h2 id="titulo_tablero_visita">Tablero de visitas</h2>
        <!-- TITULO CONTROLES TABLERO VISITA -->
        <div class="controles_tablero_visita">
            <label for="filtro_estado_visita">Estado</label>
            <select id="filtro_estado_visita">
                <option value="activas">En el condominio</option>
                <option value="finalizadas">Finalizadas</option>
                <option value="todas">Todas</option>
            </select>
            <button type="button" class="boton_secundario_visita" id="actualizar_tablero_visita">Actualizar</button>
        </div>
    </div>
    <!-- TITULO MENSAJE TABLERO VISITA -->
    <p id="mensaje_tablero_visita" class="mensaje_formulario_visita" role="status" aria-live="polite"></p>
    <!-- TITULO TABLA VISITA DESPLAZABLE -->
    <div class="tabla_visita_desplazable" role="region" aria-label="Registros de visitas" tabindex="0">
        <!-- TITULO TABLA VISITA -->
        <table class="tabla_visita">
            <thead>
                <tr>
                    <th scope="col">Registro</th>
                    <th scope="col">Visitante</th>
                    <th scope="col">Departamento</th>
                    <th scope="col">Motivo / observaciones</th>
                    <th scope="col">Ingreso</th>
                    <th scope="col">Salida</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody id="filas_tablero_visita">
                <tr>
                    <td colspan="8">Cargando visitas...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- TITULO PAGINACION VISITA -->
    <nav class="paginacion_visita" aria-label="Páginas de visitas">
        <button type="button" class="boton_secundario_visita" id="pagina_anterior_visita" disabled>Anterior</button>
        <span id="pagina_actual_visita" aria-live="polite">Página 1</span>
        <button type="button" class="boton_secundario_visita" id="pagina_siguiente_visita" disabled>Siguiente</button>
    </nav>
</section>
