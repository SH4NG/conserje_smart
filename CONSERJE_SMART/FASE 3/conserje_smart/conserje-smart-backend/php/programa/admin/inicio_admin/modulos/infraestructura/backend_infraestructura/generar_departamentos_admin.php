<?php

require_once __DIR__ . '/../../autorizaciones/backend_autorizaciones/comun_admin.php';

ejecutarAdmin('POST', function (PDO $bd, int $condominio): void {
    $accion = textoAdmin('accion', 20, true);
    $idTorre = enteroAdmin($_POST['id_torre'] ?? null);
    $desde = filter_var($_POST['piso_desde'] ?? null, FILTER_VALIDATE_INT);
    $hasta = filter_var($_POST['piso_hasta'] ?? null, FILTER_VALIDATE_INT);
    $unidades = enteroAdmin($_POST['unidades_piso'] ?? null);
    $numero = filter_var($_POST['numero_inicial'] ?? null, FILTER_VALIDATE_INT);
    $salto = enteroAdmin($_POST['salto_piso'] ?? null);
    $prefijo = textoAdmin('prefijo', 3);
    if (
        !in_array($accion, ['previsualizar', 'guardar'], true) ||
        !$idTorre ||
        $desde === false ||
        $desde === null ||
        $hasta === false ||
        $hasta === null ||
        $hasta < $desde ||
        !$unidades ||
        $unidades > 100 ||
        $numero === false ||
        $numero === null ||
        $numero < 0 ||
        $numero > 999999 ||
        !$salto ||
        $salto > 10000 ||
        ($hasta - $desde + 1) * $unidades > 500 ||
        !preg_match('/^[\p{L}\p{N}-]{0,3}$/uD', $prefijo)
    ) {
        responderAdmin(false, 'Revisa los pisos y la numeración. Se permiten hasta 500 departamentos por operación.', 400);
    }

    $bd->beginTransaction();
    $torre = torreInfraAdmin($bd, $idTorre, $condominio, true);
    pisoInfraAdmin($torre, $desde);
    pisoInfraAdmin($torre, $hasta);

    $propuestos = [];
    $numeros = [];
    for ($piso = $desde; $piso <= $hasta; $piso++) {
        for ($unidad = 0; $unidad < $unidades; $unidad++) {
            $etiqueta = $prefijo . (string) ($numero + ($piso - $desde) * $salto + $unidad);
            if (mb_strlen($etiqueta, 'UTF-8') > 10 || isset($numeros['n:' . $etiqueta])) {
                $bd->rollBack();
                responderAdmin(false, 'La numeración propuesta se repite o supera los 10 caracteres. Ajusta el salto entre pisos.', 400);
            }
            $numeros['n:' . $etiqueta] = true;
            $propuestos[] = ['piso' => $piso, 'numero' => $etiqueta];
        }
    }

    $existentes = consultarAdmin(
        $bd,
        'SELECT numero_departamento FROM departamento
         WHERE id_condominio_fk = ? AND id_torre_fk = ?',
        [$condominio, $idTorre]
    )->fetchAll(PDO::FETCH_COLUMN);
    foreach ($existentes as $existente) {
        if (isset($numeros['n:' . $existente])) {
            $bd->rollBack();
            responderAdmin(false, 'Ya existe el departamento ' . $existente . ' en esta torre. No se reemplazó ningún registro.', 409);
        }
    }

    $firma = hash('sha256', json_encode([$idTorre, versionInfraAdmin($torre), $propuestos], JSON_UNESCAPED_UNICODE));
    if ($accion === 'previsualizar') {
        $bd->commit();
        responderAdmin(true, 'Revisa la numeración antes de guardar.', 200, [
            'departamentos' => $propuestos,
            'cantidad' => count($propuestos),
            'firma' => $firma
        ]);
    }

    if (!hash_equals($firma, textoAdmin('firma_previa', 64, true))) {
        $bd->rollBack();
        responderAdmin(false, 'La configuración cambió o falta revisar la vista previa. Genérala de nuevo.', 409);
    }

    $insertar = $bd->prepare(
        'INSERT INTO departamento
            (id_condominio_fk, id_torre_fk, torre_departamento, numero_departamento, piso_departamento)
         VALUES (?, ?, ?, ?, ?)'
    );
    foreach ($propuestos as $dato) {
        $insertar->execute([$condominio, $idTorre, $torre['nombre'], $dato['numero'], $dato['piso']]);
    }
    $bd->commit();
    responderAdmin(true, count($propuestos) . ' departamentos creados. No se modificaron los anteriores.', 201, [
        'creados' => count($propuestos)
    ]);
});
