// TITULO 1 PREPARAR NOMBRE REGISTRAR
const entradaNombreRegistrar = document.getElementById('nombre_registrar');

// TITULO 2 CONSERVAR MAYUSCULAS Y ACENTOS
function obtenerNombreRegistrar() {
    return (entradaNombreRegistrar?.value || '').normalize('NFC').trim().replace(/\s+/gu, ' ');
}

// TITULO 3 VALIDAR NOMBRE SIN CAMBIAR SU ESCRITURA
function validarNombreRegistrar(enfocar = true) {
    const valor = obtenerNombreRegistrar();
    if (!valor || [...valor].length > 100 || !/^[\p{L}\p{M}]+(?:[ '\u2019-][\p{L}\p{M}]+)*$/u.test(valor)) {
        return mostrarErrorCampoRegistro('nombre_registrar', 'Ingresa tu nombre con letras, espacios, apóstrofes o guiones (máximo 100 caracteres).', enfocar);
    }
    confirmarCampoRegistro('nombre_registrar');
    return true;
}

// TITULO 4 REVISAR EL CAMPO AL TERMINAR DE ESCRIBIR
entradaNombreRegistrar?.addEventListener('blur', () => {
    if (entradaNombreRegistrar.value.trim() && validarNombreRegistrar(false)) {
        entradaNombreRegistrar.value = obtenerNombreRegistrar();
    }
});
