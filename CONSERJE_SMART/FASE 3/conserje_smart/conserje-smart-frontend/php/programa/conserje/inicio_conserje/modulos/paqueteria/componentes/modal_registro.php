<?php
if (!defined('VISTA_PAQUETERIA_ACTIVA')) {
    http_response_code(404);
    exit;
}
$departamentos = $departamentos ?? [];
?>
<div class="modal-fondo" id="modalNuevoPaquete">
    <div class="modal-contenido">
        <div class="modal-header">
            <h3>Registrar Paquete</h3>
            <button class="btn-cerrar" id="btnCerrarModal">&times;</button>
        </div>
        
        <!-- RUTA ABSOLUTA AL CONTROLADOR -->
        <form action="/conserje_smart/conserje-smart-backend/php/programa/conserje/inicio_conserje/modulos/paqueteria/controlador_paquete.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="accion" value="registrar">
                
                <div class="form-group">
                    <label>Departamento Destino</label>
                    <select class="form-control" name="id_departamento" required>
                        <option value="">Seleccione departamento...</option>
                        <?php foreach($departamentos as $d): ?>
                            <option value="<?= $d['id_departamento'] ?>">
                                <?= $d['torre_departamento'] ? 'Torre '.$d['torre_departamento'].' - ' : '' ?>Depto <?= $d['numero_departamento'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Empresa de Envío</label>
                    <input type="text" class="form-control" name="empresa_envio" placeholder="Ej: Chilexpress, Starken" required>
                </div>
                
                <div class="form-group">
                    <label>N° Seguimiento (Opcional)</label>
                    <input type="text" class="form-control" name="codigo_seguimiento">
                </div>
                
                <div class="form-group">
                    <label>Descripción (Opcional)</label>
                    <textarea class="form-control" name="descripcion_paquete" rows="3"></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" id="btnCancelarModal">Cancelar</button>
                <button type="submit" class="btn-amarillo">Guardar Paquete</button>
            </div>
        </form>
    </div>
</div>