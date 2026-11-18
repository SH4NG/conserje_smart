<?php
$id_condominio = $_SESSION['id_condominio_fk'] ?? 1;

// 1. OBTENER PENDIENTES
$stmt_pendientes = $pdo->prepare("
    SELECT p.*, d.numero_departamento, d.torre_departamento, u.nombre_usuario 
    FROM paquete p 
    JOIN departamento d ON p.id_departamento_fk = d.id_departamento 
    LEFT JOIN usuario u ON p.id_usuario_fk = u.id_usuario
    WHERE p.id_condominio_fk = ? AND p.estado_paquete = 'Pendiente'
    ORDER BY p.fecha_recepcion ASC
");
$stmt_pendientes->execute([$id_condominio]);
$paquetes_pendientes = $stmt_pendientes->fetchAll();

// 2. OBTENER RETIRADOS (Activos, menos de 30 días)
$stmt_retirados = $pdo->prepare("
    SELECT p.*, d.numero_departamento, d.torre_departamento, u.nombre_usuario 
    FROM paquete p 
    JOIN departamento d ON p.id_departamento_fk = d.id_departamento 
    LEFT JOIN usuario u ON p.id_usuario_fk = u.id_usuario
    WHERE p.id_condominio_fk = ? AND p.estado_paquete = 'Retirado' AND p.fecha_eliminacion IS NULL
    ORDER BY p.fecha_retiro DESC
");
$stmt_retirados->execute([$id_condominio]);
$paquetes_retirados = $stmt_retirados->fetchAll();

// 3. OBTENER HISTORIAL CON FILTROS
$filtro_mes = $_GET['mes'] ?? '';
$filtro_anio = $_GET['anio'] ?? '';

$query_historial = "SELECT p.*, d.numero_departamento, d.torre_departamento, u.nombre_usuario 
    FROM paquete p 
    JOIN departamento d ON p.id_departamento_fk = d.id_departamento 
    LEFT JOIN usuario u ON p.id_usuario_fk = u.id_usuario
    WHERE p.id_condominio_fk = ? AND p.estado_paquete = 'Retirado'";

$params_historial = [$id_condominio];

if (!empty($filtro_mes) && !empty($filtro_anio)) {
    $query_historial .= " AND MONTH(p.fecha_retiro) = ? AND YEAR(p.fecha_retiro) = ?";
    $params_historial[] = $filtro_mes;
    $params_historial[] = $filtro_anio;
} else {
    // Por defecto mostramos solo los archivados (más de 30 días)
    $query_historial .= " AND p.fecha_eliminacion IS NOT NULL";
}

$query_historial .= " ORDER BY p.fecha_retiro DESC";
$stmt_historial = $pdo->prepare($query_historial);
$stmt_historial->execute($params_historial);
$paquetes_historial = $stmt_historial->fetchAll();
?>

<div class="card-tabla">
    
    <!-- PESTAÑAS ESTILO BANDEJA -->
    <div class="tabs-container">
        <button class="tab-btn active" onclick="cambiarTab('pendientes', this)">
            Bandeja Actual <span class="badge-count"><?= count($paquetes_pendientes) ?></span>
        </button>
        <button class="tab-btn" onclick="cambiarTab('retirados', this)">
            Retirados Recientemente <span class="badge-count"><?= count($paquetes_retirados) ?></span>
        </button>
        <button class="tab-btn" onclick="cambiarTab('historial', this)">
            Historial de Paquetes
        </button>
    </div>

    <!-- TAB 1: PENDIENTES -->
    <div id="tab-pendientes" class="tab-content active">
        <table class="tabla-paquetes">
            <thead>
                <tr><th>Depto</th><th>Empresa</th><th>Descripción</th><th>Fecha Recepción</th><th>Estado</th><th>Acción</th></tr>
            </thead>
            <tbody>
                <?php if(empty($paquetes_pendientes)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px 20px;"><div style="font-size: 40px; margin-bottom: 10px;">📦</div><h4 style="margin: 0; color: #1a1a1a;">Todo al día</h4><p style="margin: 5px 0 0 0; color: #777;">No hay paquetes pendientes.</p></td></tr>
                <?php else: ?>
                    <?php foreach($paquetes_pendientes as $p): ?>
                    <tr>
                        <td style="font-weight: 700; font-size: 16px;"><?= htmlspecialchars($p['torre_departamento'] ? 'Torre '.$p['torre_departamento'].' - ' : '') . htmlspecialchars($p['numero_departamento']) ?></td>
                        <td><?= htmlspecialchars($p['empresa_envio']) ?></td>
                        <td><?= htmlspecialchars($p['descripcion_paquete']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_recepcion'])) ?></td>
                        <td><span class="badge-pendiente">Pendiente</span></td>
                        <td>
                            <form action="/conserje_smart/php/programa/conserje/inicio_conserje/modulos/paqueteria/controlador_paquete.php" method="POST" class="form-retirar" style="margin: 0;">
                                <input type="hidden" name="accion" value="retirar">
                                <input type="hidden" name="id_paquete" value="<?= $p['id_paquete'] ?>">
                                <button type="submit" class="btn-outline-negro">Marcar Retirado</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TAB 2: RETIRADOS RECIENTES -->
    <div id="tab-retirados" class="tab-content">
        <table class="tabla-paquetes">
            <thead>
                <tr><th>Depto</th><th>Empresa</th><th>Recibido el</th><th>Retirado el</th><th>Recibido por</th><th>Estado</th></tr>
            </thead>
            <tbody>
                <?php if(empty($paquetes_retirados)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px 20px;"><div style="font-size: 40px; margin-bottom: 10px;">✅</div><h4 style="margin: 0; color: #1a1a1a;">Sin registros recientes</h4></td></tr>
                <?php else: ?>
                    <?php foreach($paquetes_retirados as $p): ?>
                    <tr>
                        <td style="font-weight: 700; font-size: 16px;"><?= htmlspecialchars($p['torre_departamento'] ? 'Torre '.$p['torre_departamento'].' - ' : '') . htmlspecialchars($p['numero_departamento']) ?></td>
                        <td><?= htmlspecialchars($p['empresa_envio']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_recepcion'])) ?></td>
                        <td style="font-weight: bold; color: #28a745;"><?= date('d/m/Y H:i', strtotime($p['fecha_retiro'])) ?></td>
                        <td><?= htmlspecialchars($p['nombre_usuario'] ?? 'Desconocido') ?></td>
                        <td><span class="badge-retirado">Entregado</span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TAB 3: HISTORIAL -->
    <div id="tab-historial" class="tab-content">
        
        <!-- FORMULARIO DE FILTRO -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 10px;">
            <h3 style="margin: 0; font-size: 18px;">Auditoría Histórica</h3>
            
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                <select name="mes" class="form-control" style="width: auto; padding: 8px; border-radius: 5px; border: 1px solid #ccc;" required>
                    <option value="">Mes...</option>
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= $i ?>" <?= ($filtro_mes == $i) ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,1,2000)) ?></option>
                    <?php endfor; ?>
                </select>
                <select name="anio" class="form-control" style="width: auto; padding: 8px; border-radius: 5px; border: 1px solid #ccc;" required>
                    <option value="">Año...</option>
                    <option value="2026" <?= ($filtro_anio == 2026) ? 'selected' : '' ?>>2026</option>
                    <option value="2027" <?= ($filtro_anio == 2027) ? 'selected' : '' ?>>2027</option>
                </select>
                <button type="submit" class="btn-amarillo" style="padding: 8px 15px; font-size: 14px; border-radius: 5px;">Filtrar</button>
                <a href="paqueteria.php" class="btn-outline-negro" style="text-decoration: none; padding: 8px 15px; font-size: 14px; border-radius: 5px;">Limpiar</a>
            </form>
        </div>

        <table class="tabla-paquetes">
            <thead>
                <tr><th>Depto</th><th>Empresa</th><th>Recibido el</th><th>Retirado el</th><th>Recibido por</th><th>Estado</th></tr>
            </thead>
            <tbody>
                <?php if(empty($paquetes_historial)): ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px 20px;"><div style="font-size: 40px; margin-bottom: 10px;">🗄️</div><h4 style="margin: 0; color: #1a1a1a;">No hay resultados</h4><p style="margin: 5px 0 0 0; color: #777;">No se encontraron paquetes archivados o con los filtros aplicados.</p></td></tr>
                <?php else: ?>
                    <?php foreach($paquetes_historial as $p): ?>
                    <tr>
                        <td style="font-weight: 700; font-size: 16px;"><?= htmlspecialchars($p['torre_departamento'] ? 'Torre '.$p['torre_departamento'].' - ' : '') . htmlspecialchars($p['numero_departamento']) ?></td>
                        <td><?= htmlspecialchars($p['empresa_envio']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['fecha_recepcion'])) ?></td>
                        <td style="font-weight: bold; color: #28a745;"><?= date('d/m/Y H:i', strtotime($p['fecha_retiro'])) ?></td>
                        <td><?= htmlspecialchars($p['nombre_usuario'] ?? 'Desconocido') ?></td>
                        <td><span class="badge-archivado">Archivado</span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>