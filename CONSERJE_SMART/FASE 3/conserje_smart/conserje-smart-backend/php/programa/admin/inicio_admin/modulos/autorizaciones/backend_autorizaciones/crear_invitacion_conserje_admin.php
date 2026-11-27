<?php

require_once __DIR__ . '/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio, int $administrador): void {
    $horas = enteroAdmin($_POST['horas'] ?? 24);
    if (!in_array($horas, [1, 24, 72], true)) {
        responderAdmin(false, 'Selecciona una vigencia válida.', 400);
    }
    $url = urlRegistroInvitacionAdmin();
    if (!$url) {
        responderAdmin(false, 'Configura CONSERJE_URL_PUBLICA con la dirección HTTPS del frontend para generar invitaciones.', 503);
    }

    $token = bin2hex(random_bytes(32));
    $consulta = consultarAdmin(
        $bd,
        "INSERT INTO invitacion_conserje
            (id_condominio_fk, id_admin_fk, token_hash, creado_en, vence_en)
         VALUES (?, ?, ?, timezone('UTC', now()), timezone('UTC', now()) + make_interval(hours => CAST(? AS integer)))
         RETURNING id_invitacion",
        [$condominio, $administrador, hash('sha256', $token), $horas]
    );
    $id = (int) $consulta->fetchColumn();
    responderAdmin(
        true,
        'Invitación creada. Comparte este enlace con el conserje; después deberás aprobar su solicitud.',
        201,
        ['id_invitacion' => $id, 'url' => $url . '#invitacion=' . $token, 'horas' => $horas]
    );
});
