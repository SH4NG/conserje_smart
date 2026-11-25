<?php
// TITULO 1 IMPORTAR LAS VALIDACIONES COMPARTIDAS
require_once __DIR__ . '/comun_visita.php';

// TITULO 2 VALIDAR LA VISITA SELECCIONADA
ejecutarVisita('POST', function (PDO $bd, int $idCondominio): void {
    $idVisita = enteroPositivoVisita($_POST['id_visita'] ?? null);
    if (!$idVisita) {
        responderVisita(false, 'Selecciona una visita válida.', 400);
    }

    // TITULO 3 REGISTRAR LA SALIDA DE UNA VISITA ACTIVA
    $fechaSalida = fechaActualVisita();
    $consulta = $bd->prepare(
        'UPDATE visita SET fecha_hora_salida = ?
         WHERE id_visita = ? AND id_condominio_fk = ? AND fecha_hora_salida IS NULL'
    );
    $consulta->execute([$fechaSalida, $idVisita, $idCondominio]);
    $actualizada = $consulta->rowCount() === 1;
    // TITULO 4 CONFIRMAR EL RESULTADO DEL REGISTRO DE SALIDA
    if (!$actualizada) {
        responderVisita(false, 'La visita no está disponible o su salida ya fue registrada.', 409);
    }
    responderVisita(true, 'Salida registrada correctamente.', 200, [
        'id_visita' => $idVisita, 'fecha_hora_salida' => $fechaSalida
    ]);
});
