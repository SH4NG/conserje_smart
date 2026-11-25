<?php
// TITULO 1 IMPORTAR LAS VALIDACIONES COMPARTIDAS
require_once __DIR__ . '/comun_visita.php';

// TITULO 2 VALIDAR LOS DATOS DEL INGRESO DE VISITA
ejecutarVisita('POST', function (PDO $bd, int $idCondominio, int $idUsuario): void {
    [$idDepartamento, $nombre, $rut, $motivo, $observaciones] = datosFormularioVisita();
    validarDepartamentoVisita($bd, $idDepartamento, $idCondominio);
    $lectura = textoVisita('id_lectura_movil');
    $idVinculo = enteroPositivoVisita($_POST['id_vinculo_movil'] ?? null);
    $conLectura = $lectura !== '' || isset($_POST['id_vinculo_movil']);
    $bd->beginTransaction();

    // TITULO 3 COMPROBAR LA LECTURA Y EVITAR CONFIRMACIONES DUPLICADAS
    if ($conLectura) {
        if (!sesionEscritorioVisita() || !$idVinculo || !preg_match('/^[a-f0-9]{32}$/D', $lectura)) {
            $bd->rollBack();
            responderVisita(false, 'Confirma la lectura desde el computador autorizado.', 403);
        }
        $origen = origenSesionVisita();
        $consultar = $bd->prepare(
            "SELECT rut_lectura_movil, id_visita_lectura_fk,
                CASE WHEN fecha_lectura_movil > (timezone('UTC', now()) - INTERVAL '5 minutes') THEN 1 ELSE 0 END AS vigente
             FROM visita_vinculo_movil
             WHERE id_vinculo = ? AND id_lectura_movil = ? AND id_usuario_fk = ?
               AND id_condominio_fk = ? AND sesion_origen_hash = ? AND estado = 'activo'
               AND vence_en > timezone('UTC', now()) AND ultimo_uso > (timezone('UTC', now()) - INTERVAL '30 minutes')
             FOR UPDATE"
        );
        $consultar->execute([$idVinculo, $lectura, $idUsuario, $idCondominio, $origen]);
        $pendiente = $consultar->fetch();
        if (!$pendiente || $pendiente['rut_lectura_movil'] !== $rut) {
            $bd->rollBack();
            responderVisita(false, 'La lectura cambió, fue descartada o no pertenece a este computador.', 409);
        }
        if (!empty($pendiente['id_visita_lectura_fk'])) {
            $idVisita = (int) $pendiente['id_visita_lectura_fk'];
            $bd->commit();
            responderVisita(true, 'Esta lectura ya fue registrada.', 200, ['id_visita' => $idVisita, 'rut_visitante' => $rut]);
        }
        if (!$pendiente['vigente']) {
            $bd->rollBack();
            responderVisita(false, 'La lectura venció. Escanea de nuevo el carnet.', 409);
        }
    }

    // TITULO 4 GUARDAR EL INGRESO CON IDENTIDAD Y CONDOMINIO DEL SERVIDOR
    $fechaIngreso = fechaActualVisita();
    $consulta = $bd->prepare(
        'INSERT INTO visita (id_condominio_fk, id_usuario_fk, id_departamento_fk,
            nombre_visitante, rut_visitante, motivo_visita, observaciones_visita, fecha_hora_ingreso)
         VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, \'\'), ?)
         RETURNING id_visita'
    );
    $consulta->execute([$idCondominio, $idUsuario, $idDepartamento, $nombre, $rut, $motivo, $observaciones, $fechaIngreso]);
    $idVisita = (int) $consulta->fetchColumn();

    // TITULO 5 CONFIRMAR LA LECTURA Y EL REGISTRO EN UNA SOLA TRANSACCION
    if ($conLectura) {
        $confirmar = $bd->prepare('UPDATE visita_vinculo_movil SET id_visita_lectura_fk = ? WHERE id_vinculo = ? AND id_lectura_movil = ?');
        $confirmar->execute([$idVisita, $idVinculo, $lectura]);
    }
    $bd->commit();
    responderVisita(true, 'Visita registrada correctamente.', 201, [
        'id_visita' => $idVisita, 'fecha_hora_ingreso' => $fechaIngreso, 'rut_visitante' => $rut
    ]);
}, true);
