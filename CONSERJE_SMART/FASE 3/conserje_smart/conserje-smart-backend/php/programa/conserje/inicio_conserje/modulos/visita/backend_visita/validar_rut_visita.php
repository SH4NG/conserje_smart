<?php
// TITULO 1 LIMPIAR Y VALIDAR EL FORMATO DEL RUT
// comparte la validación entre registro y modificación sin consultar la base de datos
function normalizarRutVisita(string $valor): ?string
{
    $rut = preg_replace('/[.\s-]/', '', strtoupper(trim($valor)));
    if (!is_string($rut) || !preg_match('/^([0-9]{1,8})([0-9K])$/D', $rut, $partes)) {
        return null;
    }
    $cuerpo = $partes[1];
    if ((int) $cuerpo === 0) {
        return null;
    }
    // TITULO 2 CALCULAR EL DIGITO VERIFICADOR
    $suma = 0;
    $multiplicador = 2;
    for ($indice = strlen($cuerpo) - 1; $indice >= 0; $indice--) {
        $suma += (int) $cuerpo[$indice] * $multiplicador;
        $multiplicador = $multiplicador === 7 ? 2 : $multiplicador + 1;
    }
    // TITULO 3 DEVOLVER EL RUT NORMALIZADO
    $resto = 11 - ($suma % 11);
    $digito = $resto === 11 ? '0' : ($resto === 10 ? 'K' : (string) $resto);
    return $partes[2] === $digito ? number_format((int) $cuerpo, 0, '', '.') . '-' . $digito : null;
}
