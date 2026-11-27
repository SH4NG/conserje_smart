<!-- TITULO CONTENEDOR FORMULARIO VISITA -->
<div class="contenedor_formulario_visita" id="contenedor_formulario_visita">
    <!-- TITULO ENCABEZADO FORMULARIO VISITA -->
    <div class="encabezado_formulario_visita">
        <!-- TITULO TITULOS FORMULARIO VISITA -->
        <div class="titulos_formulario_visita">
            <span class="etiqueta_seccion">Control de acceso</span>
            <h2 id="titulo_formulario_visita">Registrar visita</h2>
        </div>
        <!-- TITULO BADGE ESTADO VISITA -->
        <span class="badge_estado_visita" id="badge_estado_visita">Ingreso de visita</span>
    </div>

    <!-- TITULO AYUDA FORMULARIO VISITA -->
    <p class="ayuda_formulario_visita">Revisa la identidad, indica el destino y confirma el ingreso.</p>

    <!-- TITULO FORMULARIO VISITA -->
    <form class="formulario_visita" id="formulario_visita" novalidate>
        <!-- TITULO GRUPO CAMPOS VISITA DEPARTAMENTO Y NOMBRE -->
        <div class="grupo_campos_visita">
            <!-- TITULO CAMPO VISITA DEPARTAMENTO -->
            <label class="campo_visita">
                <span>Departamento</span>
                <select id="select_departamento_visita" name="id_departamento" required>
                    <option value="">Cargando departamentos...</option>
                </select>
            </label>

            <!-- TITULO CAMPO VISITA NOMBRE DEL VISITANTE -->
            <label class="campo_visita">
                <span>Nombre del visitante</span>
                <input type="text" aria-describedby="origen_nombre_visitante" id="nombre_visitante" name="nombre_visitante" maxlength="120" autocomplete="name" placeholder="Ej: María González" required>
                <!-- TITULO ORIGEN NOMBRE VISITANTE -->
                <small class="origen_nombre_visitante" id="origen_nombre_visitante" hidden></small>
                <button type="button" class="boton_secundario_visita" id="corregir_nombre_visitante" hidden>Corregir nombre</button>
            </label>
        </div>

        <!-- TITULO GRUPO CAMPOS VISITA RUT Y MOTIVO -->
        <div class="grupo_campos_visita">
            <!-- TITULO CAMPO VISITA RUT -->
            <label class="campo_visita">
                <span>RUT</span>
                <input type="text" id="rut_visitante" name="rut_visitante" maxlength="12" autocomplete="off" placeholder="Ej: 12.345.678-9" required>
            </label>

            <!-- TITULO CAMPO VISITA MOTIVO -->
            <label class="campo_visita">
                <span>Motivo</span>
                <input type="text" id="motivo_visita" name="motivo_visita" maxlength="180" placeholder="Ej: entrega de paquete" required>
            </label>
        </div>

        <!-- TITULO GRUPO CAMPOS VISITA OBSERVACIONES -->
        <div class="grupo_campos_visita">
            <!-- TITULO CAMPO VISITA AMPLIO -->
            <label class="campo_visita campo_visita_amplio">
                <span>Observaciones <small>Opcional</small></span>
                <textarea id="observaciones_visita" name="observaciones_visita" maxlength="1000" rows="4" placeholder="Información adicional para el conserje..."></textarea>
            </label>
        </div>

        <!-- TITULO ACCIONES FORMULARIO VISITA -->
        <div class="acciones_formulario_visita">
            <button type="reset" class="boton_secundario_visita">Limpiar</button>
            <button type="submit" class="boton_principal_visita" id="boton_registrar_visita">Registrar visita</button>
        </div>

        <!-- TITULO MENSAJE FORMULARIO VISITA -->
        <p class="mensaje_formulario_visita" id="mensaje_formulario_visita" aria-live="polite"></p>
    </form>
</div>

