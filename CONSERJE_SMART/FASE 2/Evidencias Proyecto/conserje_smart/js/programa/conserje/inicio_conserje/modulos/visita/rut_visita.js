// TITULO 1 LIMPIAR EL FORMATO DEL RUT
// conserva los caracteres inválidos para que la validación pueda rechazarlos
export function limpiarRut(valor) {
    return String(valor).trim().toUpperCase().replace(/[.\s-]/g, '');
}

// TITULO 2 VALIDAR EL DIGITO VERIFICADOR DEL RUT
export function validarRut(valor) {
    const rut = limpiarRut(valor);
    if (!/^\d{1,8}[0-9K]$/.test(rut)) {
        return false;
    }
    const cuerpo = rut.slice(0, -1);
    if (Number(cuerpo) === 0) {
        return false;
    }
    let suma = 0;
    let multiplicador = 2;
    for (let indice = cuerpo.length - 1; indice >= 0; indice--) {
        suma += Number(cuerpo[indice]) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }
    const resto = 11 - (suma % 11);
    const digito = resto === 11 ? '0' : resto === 10 ? 'K' : String(resto);
    return rut.slice(-1) === digito;
}

// TITULO 3 FORMATEAR EL RUT CON PUNTOS Y GUION
export function formatearRut(valor) {
    const rut = limpiarRut(valor);
    if (!/^\d{1,8}[0-9K]$/.test(rut)) {
        return String(valor).trim();
    }
    return `${String(Number(rut.slice(0, -1))).replace(/\B(?=(\d{3})+(?!\d))/g, '.')}-${rut.slice(-1)}`;
}

// TITULO 4 INICIALIZAR EL CAMPO RUT DE VISITA
export function inicializarRutVisita() {
    const campo = document.getElementById('rut_visitante');
    if (!campo) {
        return;
    }

    // TITULO 5 LIMPIAR EL ESTADO DE VALIDACION AL ESCRIBIR
    campo.addEventListener('input', () => {
        campo.setCustomValidity('');
        campo.removeAttribute('aria-invalid');
    });
    // TITULO 6 VALIDAR Y FORMATEAR AL SALIR DEL CAMPO
    campo.addEventListener('blur', () => {
        const valido = !campo.value.trim() || validarRut(campo.value);
        campo.setCustomValidity(valido ? '' : 'Ingresa un RUT válido.');
        campo.setAttribute('aria-invalid', String(!valido));
        if (valido && campo.value.trim()) {
            campo.value = formatearRut(campo.value);
        }
    });
}
