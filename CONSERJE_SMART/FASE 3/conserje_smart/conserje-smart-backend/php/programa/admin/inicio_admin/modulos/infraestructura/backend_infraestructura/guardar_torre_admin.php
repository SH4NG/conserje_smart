<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio): void {
    $idTexto = textoAdmin('id_torre', 20);
    $id = $idTexto === '' ? null : enteroAdmin($idTexto);
    $nombre = textoAdmin('nombre', 20, true);
    $inicio = filter_var($_POST['piso_inicial'] ?? null, FILTER_VALIDATE_INT);
    $cantidad = enteroAdmin($_POST['cantidad_pisos'] ?? null);
    if (
        ($idTexto !== '' && !$id) ||
        $inicio === false ||
        $inicio === null ||
        $inicio < -10 ||
        !$cantidad ||
        $cantidad > 200 ||
        $inicio + $cantidad - 1 > 200
    ) {
        responderAdmin(false, 'Revisa la torre y su rango de pisos (-10 a 200).', 400);
    }

    $bd->beginTransaction();
    if ($id) {
        $anterior = torreInfraAdmin($bd, $id, $condominio, true);
        if (!hash_equals(versionInfraAdmin($anterior), textoAdmin('version', 64, true))) {
            $bd->rollBack();
            responderAdmin(false, 'La torre cambió desde que la abriste. Actualiza para editarla.', 409);
        }
        $fuera = (int) consultarAdmin(
            $bd,
            'SELECT COUNT(*) FROM departamento
             WHERE id_condominio_fk = ? AND id_torre_fk = ? AND piso_departamento IS NOT NULL
                AND (piso_departamento < ? OR piso_departamento >= ?)',
            [$condominio, $id, $inicio, $inicio + $cantidad]
        )->fetchColumn();
        if ($fuera > 0) {
            $bd->rollBack();
            responderAdmin(false, 'El rango dejaría departamentos existentes fuera de la torre. Revisa sus pisos antes de reducirlo.', 409);
        }
        consultarAdmin(
            $bd,
            'UPDATE torre SET nombre = ?, piso_inicial = ?, cantidad_pisos = ?,
                fecha_actualizacion = timezone(\'UTC\', now())
             WHERE id_torre = ? AND id_condominio_fk = ?',
            [$nombre, $inicio, $cantidad, $id, $condominio]
        );
        consultarAdmin(
            $bd,
            'UPDATE departamento SET torre_departamento = ?
             WHERE id_torre_fk = ? AND id_condominio_fk = ?',
            [$nombre, $id, $condominio]
        );
    } else {
        $id = (int) consultarAdmin(
            $bd,
            'INSERT INTO torre (id_condominio_fk, nombre, piso_inicial, cantidad_pisos)
             VALUES (?, ?, ?, ?) RETURNING id_torre',
            [$condominio, $nombre, $inicio, $cantidad]
        )->fetchColumn();
    }
    $bd->commit();
    responderAdmin(true, 'Torre configurada. Los departamentos existentes conservaron sus identificadores.', 200, [
        'id_torre' => $id
    ]);
});
