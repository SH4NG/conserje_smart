<?php

require_once __DIR__ . '/comun_admin.php';

header('Referrer-Policy: no-referrer');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    responderAdmin(false, 'Método no permitido.', 405);
}

try {
    $bd = abrirBaseAdmin();
    $fila = consultarInvitacionAdmin($bd, textoAdmin('token', 64, true));
    responderAdmin(true, 'Invitación válida. El condominio fue asignado por su administrador.', 200, [
        'id_condominio' => (int) $fila['id_condominio_fk'],
        'nombre_condominio' => $fila['nombre_condominio'],
        'vence_en' => $fila['vence_en']
    ]);
} catch (Throwable $error) {
    error_log('Validar invitación: ' . $error->getMessage());
    responderAdmin(false, 'No se pudo validar la invitación. Revisa la conexión e inténtalo nuevamente.', 503);
}
