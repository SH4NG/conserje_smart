<?php
// TITULO 1 IMPORTAR LAS VALIDACIONES COMPARTIDAS
require_once __DIR__ . '/comun_visita.php';

// TITULO 2 VALIDAR LA VISITA Y EL DEPARTAMENTO DE DESTINO
ejecutarVisita('POST', function (PDO $bd, int $idCondominio): void {
    $idVisita = enteroPositivoVisita($_POST['id_visita'] ?? null);
    if (!$idVisita) {
        responderVisita(false, 'Selecciona una visita válida.', 400);
    }
    [$idDepartamento, $nombre, $rut, $motivo, $observaciones] = datosFormularioVisita();
    validarDepartamentoVisita($bd, $idDepartamento, $idCondominio);

    // TITULO 3 BLOQUEAR LA VISITA DURANTE LA MODIFICACION
    // evita modificar la fila mientras otra solicitud registra su salida
    $bd->beginTransaction();
    $actual = $bd->prepare('SELECT fecha_hora_salida FROM visita WHERE id_visita = ? AND id_condominio_fk = ? FOR UPDATE');
    $actual->execute([$idVisita, $idCondominio]);
    $visita = $actual->fetch();
    if (!$visita || $visita['fecha_hora_salida'] !== null) {
        $bd->rollBack();
        responderVisita(false, $visita ? 'La visita ya tiene una salida registrada y no se puede modificar.' : 'No se encontró la visita en tu condominio.', $visita ? 409 : 404);
    }
    // TITULO 4 GUARDAR LOS DATOS DE LA VISITA ACTIVA
    $consulta = $bd->prepare(
        'UPDATE visita SET id_departamento_fk = ?, nombre_visitante = ?, rut_visitante = ?,
            motivo_visita = ?, observaciones_visita = NULLIF(?, \'\')
         WHERE id_visita = ? AND id_condominio_fk = ? AND fecha_hora_salida IS NULL'
    );
    $consulta->execute([$idDepartamento, $nombre, $rut, $motivo, $observaciones, $idVisita, $idCondominio]);
    // TITULO 5 CONFIRMAR LA TRANSACCION Y ENVIAR LA RESPUESTA
    $bd->commit();
    responderVisita(true, 'Visita modificada correctamente.', 200, ['id_visita' => $idVisita]);
});
