<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    [, $buscar] = paginacionAdmin();
    $estado = $_GET['estado'] ?? 'pendiente';
    if (!in_array($estado, ['pendiente', 'aprobada', 'rechazada', 'todas'], true)) {
        responderAdmin(false, 'Estado no válido.', 400);
    }
    $filtroEstado = $estado === 'todas' ? '' : ' AND s.estado = ?';
    $valores = [$condominio, $buscar, $buscar, $buscar];
    if ($filtroEstado !== '') {
        $valores[] = $estado;
    }
    listadoAdmin(
        $bd,
        "FROM solicitud_registro_conserje s
         INNER JOIN condominio c ON c.id_condominio = s.id_condominio_fk
         INNER JOIN usuario u ON u.id_usuario = s.id_usuario_fk
            AND u.id_condominio_fk = s.id_condominio_fk
         LEFT JOIN usuario a ON a.id_usuario = s.id_admin_resolutor_fk
            AND a.id_condominio_fk = s.id_condominio_fk
         WHERE s.id_condominio_fk = ? AND u.rol_usuario = 'conserje'
           AND (CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) ILIKE ?
                OR u.rut_usuario ILIKE ? OR COALESCE(u.correo, '') ILIKE ?)$filtroEstado",
        "s.id_solicitud, s.id_invitacion_fk, c.nombre_condominio, s.estado,
         s.fecha_solicitud, s.fecha_resolucion, s.motivo_resolucion,
         u.nombre_usuario, u.apellido_usuario, u.rut_usuario, u.correo, u.telefono_usuario,
         CONCAT(a.nombre_usuario, ' ', a.apellido_usuario) AS nombre_resolutor",
        's.fecha_solicitud DESC, s.id_solicitud DESC',
        $valores
    );
});
