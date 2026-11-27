// TITULO 1 PREPARAR TELEFONO REGISTRAR
const entradaTelefonoRegistrar = document.getElementById('telefono_registrar');

// TITULO 2 NORMALIZAR EL NUMERO CHILENO SIN RECORTARLO
function obtenerTelefonoRegistrar() {
    const texto = (entradaTelefonoRegistrar?.value || '').trim();
    if (!/^[+\d\s().-]+$/u.test(texto) || (texto.includes('+') && !texto.startsWith('+')) || (texto.match(/\+/g) || []).length > 1) return '';
    let digitos = texto.replace(/\D/g, '');
    if (!texto.startsWith('+') && /^\d{8}$/.test(digitos)) digitos = '569' + digitos;
    else if (!texto.startsWith('+') && /^[2-9]\d{8}$/.test(digitos)) digitos = '56' + digitos;
    return /^56[2-9]\d{8}$/.test(digitos) ? '+' + digitos : '';
}

// TITULO 3 VALIDAR EL TELEFONO NACIONAL O CON PREFIJO
function validarTelefonoRegistrar(enfocar = true) {
    if (!obtenerTelefonoRegistrar()) {
        return mostrarErrorCampoRegistro('telefono_registrar', 'Ingresa un teléfono chileno válido, por ejemplo +56 9 6669 1139 o 966691139.', enfocar);
    }
    confirmarCampoRegistro('telefono_registrar');
    return true;
}

// TITULO 4 MOSTRAR EL PREFIJO DESPUES DE VALIDAR
entradaTelefonoRegistrar?.addEventListener('blur', () => {
    if (entradaTelefonoRegistrar.value.trim() && validarTelefonoRegistrar(false)) entradaTelefonoRegistrar.value = obtenerTelefonoRegistrar();
});
