<div class="contenedor_formulario_visita" id="contenedor_formulario_visita">
    <div class="encabezado_formulario_visita">
        <div>
            <span class="etiqueta_seccion">Control de acceso</span>
            <h2>Registrar visita</h2>
        </div>
        <span class="badge_estado_visita" id="badge_estado_visita">Ingreso de visita</span>
    </div>

    <form class="formulario_visita" id="formulario_visita" novalidate>
        <div class="grupo_campos_visita">
            <label class="campo_visita">
                <span>Departamento</span>
                <select id="select_departamento_visita" name="id_departamento" required>
                    <option value="">Cargando departamentos...</option>
                </select>
            </label>

            <label class="campo_visita">
                <span>Nombre del visitante</span>
                <input type="text" id="nombre_visitante" name="nombre_visitante" maxlength="120" autocomplete="name" placeholder="Ej: María González" required>
            </label>
        </div>

        <div class="grupo_campos_visita">
            <label class="campo_visita">
                <span>RUT</span>
                <input type="text" id="rut_visitante" name="rut_visitante" maxlength="12" autocomplete="off" placeholder="Ej: 12.345.678-9" required>
            </label>

            <label class="campo_visita">
                <span>Motivo</span>
                <input type="text" id="motivo_visita" name="motivo_visita" maxlength="180" placeholder="Ej: entrega de paquete" required>
            </label>
        </div>

        <div class="grupo_campos_visita">
            <label class="campo_visita campo_visita_amplio">
                <span>Observaciones</span>
                <textarea id="observaciones_visita" name="observaciones_visita" maxlength="1000" rows="4" placeholder="Información adicional para el conserje..."></textarea>
            </label>
        </div>

        <div class="acciones_formulario_visita">
            <button type="reset" class="boton_secundario_visita">Limpiar</button>
            <button type="submit" class="boton_principal_visita" id="boton_registrar_visita">Registrar ingreso</button>
        </div>

        <p class="mensaje_formulario_visita" id="mensaje_formulario_visita" aria-live="polite"></p>
    </form>
</div>
