<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('GET', function (PDO $bd, int $condominio): void {
    listadoAdmin(
        $bd,
        "FROM invitacion_conserje i
         INNER JOIN usuario u ON u.id_usuario = i.id_admin_fk
            AND u.id_condominio_fk = i.id_condominio_fk
         LEFT JOIN usuario c ON c.id_usuario = i.id_usuario_fk
            AND c.id_condominio_fk = i.id_condominio_fk
         WHERE i.id_condominio_fk = ?",
        "i.id_invitacion,
         CASE WHEN i.estado = 'activa' AND i.vence_en <= timezone('UTC', now())
              THEN 'vencida' ELSE i.estado END AS estado,
         i.creado_en, i.vence_en, i.usada_en,
         CONCAT(u.nombre_usuario, ' ', u.apellido_usuario) AS creado_por,
         CONCAT(c.nombre_usuario, ' ', c.apellido_usuario) AS usado_por",
        'i.creado_en DESC, i.id_invitacion DESC',
        [$condominio]
    );
});
