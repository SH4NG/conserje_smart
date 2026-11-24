<?php
function obtenerDatosPaqueteria(PDO $pdo, int $idCondominio, string $filtroMes, string $filtroAnio): array
{
    $stmtPendientes = $pdo->prepare("
        SELECT p.*, d.numero_departamento, d.torre_departamento, u.nombre_usuario
        FROM paquete p
        JOIN departamento d ON p.id_departamento_fk = d.id_departamento
        LEFT JOIN usuario u ON p.id_usuario_fk = u.id_usuario
        WHERE p.id_condominio_fk = ? AND p.estado_paquete = 'Pendiente'
        ORDER BY p.fecha_recepcion ASC
    ");
    $stmtPendientes->execute([$idCondominio]);

    $stmtRetirados = $pdo->prepare("
        SELECT p.*, d.numero_departamento, d.torre_departamento, u.nombre_usuario
        FROM paquete p
        JOIN departamento d ON p.id_departamento_fk = d.id_departamento
        LEFT JOIN usuario u ON p.id_usuario_fk = u.id_usuario
        WHERE p.id_condominio_fk = ? AND p.estado_paquete = 'Retirado' AND p.fecha_eliminacion IS NULL
        ORDER BY p.fecha_retiro DESC
    ");
    $stmtRetirados->execute([$idCondominio]);

    $queryHistorial = "
        SELECT p.*, d.numero_departamento, d.torre_departamento, u.nombre_usuario
        FROM paquete p
        JOIN departamento d ON p.id_departamento_fk = d.id_departamento
        LEFT JOIN usuario u ON p.id_usuario_fk = u.id_usuario
        WHERE p.id_condominio_fk = ? AND p.estado_paquete = 'Retirado'
    ";
    $parametrosHistorial = [$idCondominio];

    if ($filtroMes !== '' && $filtroAnio !== '') {
        $queryHistorial .= ' AND MONTH(p.fecha_retiro) = ? AND YEAR(p.fecha_retiro) = ?';
        $parametrosHistorial[] = $filtroMes;
        $parametrosHistorial[] = $filtroAnio;
    } else {
        $queryHistorial .= ' AND p.fecha_eliminacion IS NOT NULL';
    }

    $queryHistorial .= ' ORDER BY p.fecha_retiro DESC';
    $stmtHistorial = $pdo->prepare($queryHistorial);
    $stmtHistorial->execute($parametrosHistorial);

    $stmtDepartamentos = $pdo->prepare("
        SELECT id_departamento, numero_departamento, torre_departamento
        FROM departamento
        WHERE id_condominio_fk = ?
        ORDER BY torre_departamento, numero_departamento
    ");
    $stmtDepartamentos->execute([$idCondominio]);

    return [
        'paquetes_pendientes' => $stmtPendientes->fetchAll(),
        'paquetes_retirados' => $stmtRetirados->fetchAll(),
        'paquetes_historial' => $stmtHistorial->fetchAll(),
        'departamentos' => $stmtDepartamentos->fetchAll()
    ];
}
