<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    [, $buscar] = paginacionAdmin();
    $estado = $_GET['estado'] ?? 'activas';
    if (!in_array($estado, ['activas', 'finalizadas', 'todas'], true)) {
        responderAdmin(false, 'Estado no válido.', 400);
    }
    $filtroEstado = $estado === 'activas'
        ? ' AND v.fecha_hora_salida IS NULL'
        : ($estado === 'finalizadas' ? ' AND v.fecha_hora_salida IS NOT NULL' : '');
    listadoAdmin(
        $bd,
        "FROM visita v
         INNER JOIN departamento d ON d.id_departamento = v.id_departamento_fk
            AND d.id_condominio_fk = v.id_condominio_fk
         LEFT JOIN usuario u ON u.id_usuario = v.id_usuario_fk AND u.id_condominio_fk = v.id_condominio_fk
         WHERE v.id_condominio_fk = ?
            AND (v.nombre_visitante ILIKE ? OR v.rut_visitante ILIKE ? OR d.numero_departamento ILIKE ?)$filtroEstado",
        "v.id_visita, v.nombre_visitante, v.rut_visitante, v.motivo_visita,
         v.observaciones_visita, v.fecha_hora_ingreso, v.fecha_hora_salida,
         d.numero_departamento, d.torre_departamento,
         CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_conserje",
        'v.fecha_hora_ingreso DESC, v.id_visita DESC',
        [$condominio, $buscar, $buscar, $buscar]
    );
});
