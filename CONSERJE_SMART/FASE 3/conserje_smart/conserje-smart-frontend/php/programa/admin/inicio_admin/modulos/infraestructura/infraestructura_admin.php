<!-- TITULO SECCION INFRAESTRUCTURA ADMIN -->
<section class="seccion_infraestructura_admin tarjeta_admin" data-panel-admin="infraestructura" <?= $seccion !== 'infraestructura' ? 'hidden' : ''; ?>>
    <!-- TITULO ENCABEZADO SECCION ADMIN -->
    <div class="encabezado_seccion_admin">
        <div class="titulos_seccion_admin">
            <span class="etiqueta_admin">Configuración del condominio</span>
            <h2 id="titulo_infraestructura_admin">Torres y departamentos</h2>
            <p>Define los pisos y la numeración que utilizarán los conserjes.</p>
        </div>
        <button type="button" class="boton_admin secundario_admin" id="actualizar_infraestructura_admin">Actualizar</button>
    </div>
    <!-- TITULO RESUMEN INFRAESTRUCTURA ADMIN -->
    <div class="resumen_infraestructura_admin" id="resumen_infraestructura_admin" aria-live="polite">Consultando la estructura...</div>
    <p class="mensaje_admin" id="mensaje_infraestructura_admin" role="status" aria-live="polite"></p>
    <!-- TITULO ENCABEZADO TORRES ADMIN -->
    <div class="encabezado_seccion_admin">
        <h3>Torres</h3>
        <button type="button" class="boton_admin principal_admin" id="nueva_torre_admin">Agregar torre</button>
    </div>
    <!-- TITULO LISTA TORRES ADMIN -->
    <div class="lista_torres_admin" id="lista_torres_admin"></div>
    <p class="nota_admin">Los pisos de las torres importadas deben ser confirmados. Los departamentos sin torre se conservan para asignarlos manualmente; no se fusionan ni eliminan registros.</p>
    <!-- TITULO ENCABEZADO DEPARTAMENTOS ADMIN -->
    <div class="encabezado_seccion_admin encabezado_departamentos_admin">
        <h3>Departamentos</h3>
        <div class="acciones_infraestructura_admin">
            <button type="button" class="boton_admin secundario_admin" id="nuevo_departamento_admin" disabled>Agregar departamento</button>
            <button type="button" class="boton_admin principal_admin" id="generar_departamentos_admin" disabled>Generar por pisos</button>
        </div>
    </div>
    <!-- TITULO FILTROS INFRAESTRUCTURA ADMIN -->
    <form class="filtros_admin" id="filtros_infraestructura_admin">
        <label class="campo_admin"><span>Buscar</span><input type="search" name="buscar" maxlength="100" placeholder="Número de departamento o torre"></label>
        <label class="campo_admin"><span>Torre</span><select name="torre"><option value="">Todas</option><option value="sin_torre">Sin torre asignada</option></select></label>
        <button class="boton_admin secundario_admin" type="submit">Buscar</button>
    </form>
    <!-- TITULO TABLA DESPLAZABLE ADMIN -->
    <div class="tabla_desplazable_admin" role="region" aria-label="Departamentos del condominio" tabindex="0">
        <table class="tabla_admin">
            <thead><tr><th scope="col">Registro</th><th scope="col">Torre</th><th scope="col">Departamento</th><th scope="col">Piso</th><th scope="col">Acciones</th></tr></thead>
            <tbody id="filas_infraestructura_admin"><tr><td colspan="5">Cargando departamentos...</td></tr></tbody>
        </table>
    </div>
    <!-- TITULO PAGINACION INFRAESTRUCTURA ADMIN -->
    <nav class="paginacion_admin" aria-label="Páginas de departamentos">
        <button type="button" class="boton_admin secundario_admin" id="anterior_infraestructura_admin" disabled>Anterior</button>
        <span id="pagina_infraestructura_admin">Página 1</span>
        <button type="button" class="boton_admin secundario_admin" id="siguiente_infraestructura_admin" disabled>Siguiente</button>
    </nav>
</section>
<!-- TITULO DIALOGO TORRE ADMIN -->
<dialog class="dialogo_admin" id="dialogo_torre_admin" aria-labelledby="titulo_torre_admin">
    <form class="formulario_admin" id="formulario_torre_admin">
        <h2 id="titulo_torre_admin">Configurar torre</h2>
        <p class="nota_admin">El rango debe incluir los departamentos existentes. Cambiar el nombre actualiza la etiqueta de esta misma torre en sus departamentos, conservando sus IDs.</p>
        <input type="hidden" name="id_torre"><input type="hidden" name="version">
        <label class="campo_admin"><span>Nombre de la torre</span><input name="nombre" maxlength="20" placeholder="Ej.: A o Edificio principal" required></label>
        <div class="grupo_campos_admin">
            <label class="campo_admin"><span>Primer piso</span><input type="number" name="piso_inicial" value="1" min="-10" max="200" required></label>
            <label class="campo_admin"><span>Cantidad de pisos</span><input type="number" name="cantidad_pisos" min="1" max="200" required></label>
        </div>
        <p class="mensaje_admin" id="mensaje_torre_admin" role="status" aria-live="polite"></p>
        <div class="acciones_formulario_admin"><button type="button" class="boton_admin secundario_admin" data-cerrar-dialogo>Cancelar</button><button type="submit" class="boton_admin principal_admin">Guardar torre</button></div>
    </form>
</dialog>
<!-- TITULO DIALOGO DEPARTAMENTO ADMIN -->
<dialog class="dialogo_admin" id="dialogo_departamento_admin" aria-labelledby="titulo_departamento_admin">
    <form class="formulario_admin" id="formulario_departamento_admin">
        <h2 id="titulo_departamento_admin">Configurar departamento</h2>
        <p class="nota_admin">Al editar se conserva el identificador utilizado por visitas y paquetes. La torre debe tener sus pisos configurados.</p>
        <input type="hidden" name="id_departamento"><input type="hidden" name="version">
        <label class="campo_admin"><span>Torre</span><select name="id_torre" data-torres-infra required></select></label>
        <div class="grupo_campos_admin">
            <label class="campo_admin"><span>Número / identificación</span><input name="numero_departamento" maxlength="10" placeholder="Ej.: 101 o A-101" required></label>
            <label class="campo_admin"><span>Piso</span><input type="number" name="piso_departamento" required></label>
        </div>
        <p class="mensaje_admin" id="mensaje_departamento_admin" role="status" aria-live="polite"></p>
        <div class="acciones_formulario_admin"><button type="button" class="boton_admin secundario_admin" data-cerrar-dialogo>Cancelar</button><button type="submit" class="boton_admin principal_admin">Guardar departamento</button></div>
    </form>
</dialog>
<!-- TITULO DIALOGO GENERACION ADMIN -->
<dialog class="dialogo_admin dialogo_generacion_admin" id="dialogo_generacion_admin" aria-labelledby="titulo_generacion_admin">
    <form class="formulario_admin" id="formulario_generacion_admin">
        <h2 id="titulo_generacion_admin">Generar departamentos por pisos</h2>
        <p class="nota_admin">Define la numeración y revisa la vista previa. Ejemplo: número inicial 101 y salto 100 producen 101, 102… en el primer piso y 201, 202… en el siguiente. Máximo 500 por operación.</p>
        <label class="campo_admin"><span>Torre</span><select name="id_torre" data-torres-infra required></select></label>
        <div class="grupo_campos_admin">
            <label class="campo_admin"><span>Desde el piso</span><input type="number" name="piso_desde" required></label>
            <label class="campo_admin"><span>Hasta el piso</span><input type="number" name="piso_hasta" required></label>
            <label class="campo_admin"><span>Departamentos por piso</span><input type="number" name="unidades_piso" value="4" min="1" max="100" required></label>
            <label class="campo_admin"><span>Número inicial</span><input type="number" name="numero_inicial" value="101" min="0" max="999999" required></label>
            <label class="campo_admin"><span>Salto entre pisos</span><input type="number" name="salto_piso" value="100" min="1" max="10000" required></label>
            <label class="campo_admin"><span>Prefijo opcional</span><input name="prefijo" maxlength="3" placeholder="Ej.: D-"></label>
        </div>
        <p class="mensaje_admin" id="mensaje_generacion_admin" role="status" aria-live="polite"></p>
        <!-- TITULO VISTA PREVIA GENERACION ADMIN -->
        <div class="vista_previa_generacion_admin" id="vista_previa_generacion_admin" hidden>
            <strong id="cantidad_generacion_admin"></strong>
            <div class="tabla_desplazable_admin">
                <table class="tabla_admin"><thead><tr><th scope="col">Piso</th><th scope="col">Departamento</th></tr></thead><tbody id="filas_generacion_admin"></tbody></table>
            </div>
        </div>
        <div class="acciones_formulario_admin">
            <button type="button" class="boton_admin secundario_admin" data-cerrar-dialogo>Cancelar</button>
            <button type="submit" class="boton_admin secundario_admin">Revisar numeración</button>
            <button type="button" class="boton_admin principal_admin" id="confirmar_generacion_admin" disabled>Confirmar y crear</button>
        </div>
    </form>
</dialog>
