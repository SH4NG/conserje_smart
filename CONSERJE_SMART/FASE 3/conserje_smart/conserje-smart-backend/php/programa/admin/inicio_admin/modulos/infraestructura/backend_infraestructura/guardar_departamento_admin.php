<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio): void {
    $idTexto = textoAdmin('id_departamento', 20);
    $id = $idTexto === '' ? null : enteroAdmin($idTexto);
    $idTorre = enteroAdmin($_POST['id_torre'] ?? null);
    $numero = textoAdmin('numero_departamento', 10, true);
    if (
        ($idTexto !== '' && !$id) ||
        !$idTorre ||
        !preg_match('/^[\p{L}\p{N}_.\/ -]{1,10}$/uD', $numero)
    ) {
        responderAdmin(false, 'Selecciona una torre y un número de departamento válido (máximo 10 caracteres).', 400);
    }

    $bd->beginTransaction();
    $torre = torreInfraAdmin($bd, $idTorre, $condominio, true);
    $piso = pisoInfraAdmin($torre, $_POST['piso_departamento'] ?? null);
    if ($id) {
        $anterior = consultarAdmin(
            $bd,
            'SELECT * FROM departamento
             WHERE id_departamento = ? AND id_condominio_fk = ? FOR UPDATE',
            [$id, $condominio]
        )->fetch();
        if (!$anterior) {
            $bd->rollBack();
            responderAdmin(false, 'El departamento no pertenece a tu condominio.', 404);
        }
        if (!hash_equals(versionInfraAdmin($anterior), textoAdmin('version', 64, true))) {
            $bd->rollBack();
            responderAdmin(false, 'El departamento cambió. Actualiza antes de editarlo.', 409);
        }
        consultarAdmin(
            $bd,
            'UPDATE departamento SET id_torre_fk = ?, torre_departamento = ?,
                numero_departamento = ?, piso_departamento = ?
             WHERE id_departamento = ? AND id_condominio_fk = ?',
            [$idTorre, $torre['nombre'], $numero, $piso, $id, $condominio]
        );
    } else {
        $id = (int) consultarAdmin(
            $bd,
            'INSERT INTO departamento
                (id_condominio_fk, id_torre_fk, torre_departamento, numero_departamento, piso_departamento)
             VALUES (?, ?, ?, ?, ?) RETURNING id_departamento',
            [$condominio, $idTorre, $torre['nombre'], $numero, $piso]
        )->fetchColumn();
    }
    $bd->commit();
    responderAdmin(true, 'Departamento guardado. Su condominio y torre se validaron en el servidor.', 200, [
        'id_departamento' => $id
    ]);
});
