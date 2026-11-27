// TITULO 1 PREPARAR RUT REGISTRAR
const entradaRutRegistrar = document.getElementById('rut_registrar');

// TITULO 2 SEPARAR EL RUT SIN ALTERAR SU DIGITO VERIFICADOR
function partesRutRegistrar() {
    const texto = (entradaRutRegistrar?.value || '').trim();
    if (!/^[\dKk.\s-]+$/u.test(texto)) return null;
    const limpio = texto.replace(/[.\s-]/gu, '').toUpperCase();
    if (!/^\d{1,8}[0-9K]$/.test(limpio)) return null;
    const cuerpo = limpio.slice(0, -1).replace(/^0+(?=\d)/, '');
    return Number(cuerpo) > 0 ? { cuerpo, digito: limpio.slice(-1) } : null;
}

// TITULO 3 CALCULAR EL DIGITO VERIFICADOR CHILENO
function validarRutRegistrar(enfocar = true) {
    const partes = partesRutRegistrar();
    let suma = 0, factor = 2;
    if (partes) {
        for (let i = partes.cuerpo.length - 1; i >= 0; i--) {
            suma += Number(partes.cuerpo[i]) * factor;
            factor = factor === 7 ? 2 : factor + 1;
        }
        const resto = 11 - suma % 11;
        const esperado = resto === 11 ? '0' : resto === 10 ? 'K' : String(resto);
        if (partes.digito === esperado) {
            confirmarCampoRegistro('rut_registrar');
            return true;
        }
    }
    return mostrarErrorCampoRegistro('rut_registrar', 'Revisa el RUT y su dígito verificador. Puedes escribirlo con puntos y guion; la K es válida.', enfocar);
}

// TITULO 4 ENTREGAR Y MOSTRAR EL RUT CON FORMATO
function obtenerRutRegistrar() {
    const partes = partesRutRegistrar();
    if (!partes) return '';
    return partes.cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + partes.digito;
}
entradaRutRegistrar?.addEventListener('blur', () => {
    if (entradaRutRegistrar.value.trim() && validarRutRegistrar(false)) entradaRutRegistrar.value = obtenerRutRegistrar();
});

// TITULO 5 DAR FORMATO AL RUT SIN CAMBIAR EL DIGITO VERIFICADOR
function aplicarFormatoRutRegistro(campo) {
    const texto = campo.value;
    const limpio = texto.replace(/[.\s-]/gu, '').toUpperCase();
    if (!/^[0-9K]{0,9}$/.test(limpio)) return;
    const inicio = campo.selectionStart ?? texto.length;
    const fin = campo.selectionEnd ?? inicio;
    const cantidadAntes = posicion => texto.slice(0, posicion).replace(/[^0-9K]/gi, '').length;
    const antesInicio = cantidadAntes(inicio), antesFin = cantidadAntes(fin);
    const cuerpo = limpio.slice(0, -1);
    const formateado = limpio.length < 2 ? limpio :
        cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + limpio.slice(-1);
    if (texto === formateado) return;
    campo.value = formateado;
    const posicionNueva = cantidad => {
        if (cantidad === 0) return 0;
        let encontrados = 0;
        for (let i = 0; i < formateado.length; i++) {
            if (/[0-9K]/.test(formateado[i]) && ++encontrados === cantidad) return i + 1;
        }
        return formateado.length;
    };
    campo.setSelectionRange(posicionNueva(antesInicio), posicionNueva(antesFin));
}

// TITULO 6 PERMITIR ESCRIBIR, PEGAR Y BORRAR CON EL FORMATO AUTOMATICO
function prepararFormatoRutRegistro(campo) {
    if (!campo || campo.dataset.formatoRut === 'activo') return;
    campo.dataset.formatoRut = 'activo';
    campo.addEventListener('input', evento => {
        if (!evento.isComposing) aplicarFormatoRutRegistro(campo);
    });
    campo.addEventListener('compositionend', () => aplicarFormatoRutRegistro(campo));
    campo.addEventListener('beforeinput', evento => {
        const inicio = campo.selectionStart, fin = campo.selectionEnd;
        if (evento.isComposing || inicio !== fin || !['deleteContentBackward', 'deleteContentForward'].includes(evento.inputType)) return;
        const retroceder = evento.inputType === 'deleteContentBackward';
        let borrar = retroceder ? inicio - 1 : inicio;
        if (!/[.-]/.test(campo.value[borrar] || '')) return;
        while (borrar >= 0 && borrar < campo.value.length && /[.-]/.test(campo.value[borrar])) borrar += retroceder ? -1 : 1;
        if (borrar < 0 || borrar >= campo.value.length) return;
        evento.preventDefault();
        campo.value = campo.value.slice(0, borrar) + campo.value.slice(borrar + 1);
        const cursor = retroceder ? borrar : inicio;
        campo.setSelectionRange(cursor, cursor);
        campo.dispatchEvent(new Event('input', { bubbles: true }));
    });
    campo.addEventListener('paste', evento => {
        const pegado = evento.clipboardData?.getData('text');
        if (pegado == null) return;
        if (!/^[0-9Kk.\s-]+$/u.test(pegado)) {
            evento.preventDefault();
            const aviso = 'Pega solamente el RUT, con números, puntos, guion y dígito K si corresponde.';
            if (campo.id === 'rut_registrar') mostrarErrorCampoRegistro(campo.id, aviso);
            else mensajeEnvioRegistro(aviso);
            return;
        }
        const actual = campo.value.replace(/[.\s-]/gu, '').toUpperCase();
        if (!/^[0-9K]*$/.test(actual)) return;
        const posicion = indice => campo.value.slice(0, indice).replace(/[^0-9K]/gi, '').length;
        const inicio = posicion(campo.selectionStart ?? 0);
        const fin = posicion(campo.selectionEnd ?? 0);
        const insertar = pegado.replace(/[.\s-]/gu, '').toUpperCase();
        const propuesto = actual.slice(0, inicio) + insertar + actual.slice(fin);
        evento.preventDefault();
        if (propuesto.length > 9) {
            const aviso = 'El RUT admite hasta 8 dígitos y su dígito verificador. Revisa el texto que pegaste.';
            if (campo.id === 'rut_registrar') mostrarErrorCampoRegistro(campo.id, aviso);
            else mensajeEnvioRegistro(aviso);
            return;
        }
        campo.value = propuesto;
        campo.setSelectionRange(inicio + insertar.length, inicio + insertar.length);
        campo.dispatchEvent(new Event('input', { bubbles: true }));
    });
    aplicarFormatoRutRegistro(campo);
}
prepararFormatoRutRegistro(entradaRutRegistrar);
