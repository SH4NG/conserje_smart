// TITULO 1 PREPARAR APELLIDO REGISTRAR
const entradaApellidoRegistrar = document.getElementById('apellido_registrar');

// TITULO 2 CONSERVAR MAYUSCULAS Y ACENTOS
function obtenerApellidoRegistrar() {
    return (entradaApellidoRegistrar?.value || '').normalize('NFC').trim().replace(/\s+/gu, ' ');
}

// TITULO 3 VALIDAR APELLIDO SIN CAMBIAR SU ESCRITURA
function validarApellidoRegistrar(enfocar = true) {
    const valor = obtenerApellidoRegistrar();
    if (!valor || [...valor].length > 100 || !/^[\p{L}\p{M}]+(?:[ '\u2019-][\p{L}\p{M}]+)*$/u.test(valor)) {
        return mostrarErrorCampoRegistro('apellido_registrar', 'Ingresa tus apellidos con letras, espacios, apóstrofes o guiones (máximo 100 caracteres).', enfocar);
    }
    confirmarCampoRegistro('apellido_registrar');
    return true;
}

// TITULO 4 REVISAR EL CAMPO AL TERMINAR DE ESCRIBIR
entradaApellidoRegistrar?.addEventListener('blur', () => {
    if (entradaApellidoRegistrar.value.trim() && validarApellidoRegistrar(false)) {
        entradaApellidoRegistrar.value = obtenerApellidoRegistrar();
    }
});
