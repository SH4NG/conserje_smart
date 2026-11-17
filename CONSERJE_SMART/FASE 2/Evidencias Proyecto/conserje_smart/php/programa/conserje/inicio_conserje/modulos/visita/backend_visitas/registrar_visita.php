<?php
// TITULO 1 IMPORTAR LAS VALIDACIONES COMPARTIDAS
require_once __DIR__ . '/comun_visita.php';

// TITULO 2 VALIDAR LOS DATOS DEL INGRESO DE VISITA
ejecutarVisita('POST', function (mysqli $bd, int $idCondominio, int $idUsuario): void {
    [$idDepartamento, $nombre, $rut, $motivo, $observaciones] = datosFormularioVisita();
    validarDepartamentoVisita($bd, $idDepartamento, $idCondominio);
    // TITULO 3 GUARDAR LA VISITA Y SUS RELACIONES
    $fechaIngreso = fechaActualVisita();
    $consulta = $bd->prepare(
        'INSERT INTO visita (id_condominio_fk, id_usuario_fk, id_departamento_fk,
            nombre_visitante, rut_visitante, motivo_visita, observaciones_visita, fecha_hora_ingreso)
         VALUES (?, ?, ?, ?, ?, ?, NULLIF(?, \'\'), ?)'
    );
    $consulta->bind_param('iiisssss', $idCondominio, $idUsuario, $idDepartamento, $nombre, $rut, $motivo, $observaciones, $fechaIngreso);
    $consulta->execute();
    // TITULO 4 ENVIAR EL NUMERO DEL REGISTRO CREADO
    $idVisita = (int) $bd->insert_id;
    $consulta->close();
    $bd->close();
    responderVisita(true, 'Visita registrada correctamente.', 201, [
        'id_visita' => $idVisita, 'fecha_hora_ingreso' => $fechaIngreso, 'rut_visitante' => $rut
    ]);
});
