<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio): void {
    $id = enteroAdmin($_POST['id_invitacion'] ?? null);
    if (!$id) {
        responderAdmin(false, 'Selecciona una invitación válida.', 400);
    }
    $consulta = consultarAdmin(
        $bd,
        "UPDATE invitacion_conserje SET estado = 'revocada'
         WHERE id_invitacion = ? AND id_condominio_fk = ? AND estado = 'activa'",
        [$id, $condominio]
    );
    if ($consulta->rowCount() !== 1) {
        responderAdmin(false, 'La invitación no está disponible o ya fue utilizada. No se cambió ninguna cuenta.', 409);
    }
    responderAdmin(true, 'Invitación revocada. Ese enlace ya no permite solicitar una cuenta.');
});
