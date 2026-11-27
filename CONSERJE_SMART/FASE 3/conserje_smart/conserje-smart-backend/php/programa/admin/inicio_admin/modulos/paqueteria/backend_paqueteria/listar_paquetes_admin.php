<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    [, $buscar] = paginacionAdmin();
    $estado = $_GET['estado'] ?? 'Pendiente';
    if (!in_array($estado, ['Pendiente', 'Retirado', 'todos'], true)) {
        responderAdmin(false, 'Estado no válido.', 400);
    }
    $filtroEstado = $estado === 'todos' ? '' : ' AND p.estado_paquete = ?';
    $valores = [$condominio, $buscar, $buscar, $buscar];
    if ($filtroEstado !== '') {
        $valores[] = $estado;
    }
    listadoAdmin(
        $bd,
        "FROM paquete p
         INNER JOIN departamento d ON d.id_departamento = p.id_departamento_fk
            AND d.id_condominio_fk = p.id_condominio_fk
         LEFT JOIN usuario u ON u.id_usuario = p.id_usuario_fk AND u.id_condominio_fk = p.id_condominio_fk
         WHERE p.id_condominio_fk = ?
            AND (COALESCE(p.empresa_envio, '') ILIKE ? OR COALESCE(p.codigo_seguimiento, '') ILIKE ?
                 OR d.numero_departamento ILIKE ?)$filtroEstado",
        "p.id_paquete, p.empresa_envio, p.codigo_seguimiento, p.descripcion_paquete,
         p.fecha_recepcion, p.fecha_retiro, p.estado_paquete, p.fecha_eliminacion,
         d.numero_departamento, d.torre_departamento,
         CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS nombre_conserje",
        'p.fecha_recepcion DESC, p.id_paquete DESC',
        $valores
    );
});
