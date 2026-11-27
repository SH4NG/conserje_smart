// TITULO 1 PREPARAR CONTRASEÑA REGISTRAR
const entradaContraseñaRegistrar = document.getElementById('contraseña_registrar');

// TITULO 2 COMPROBAR LAS MISMAS REGLAS DEL SERVIDOR
function obtenerContraseñaRegistrar() { return entradaContraseñaRegistrar?.value || ''; }
function requisitosContraseñaRegistrar() {
    const valor = obtenerContraseñaRegistrar();
    return {
        longitud: [...valor].length >= 8 && new TextEncoder().encode(valor).length <= 72 && !valor.includes('\0'),
        mayuscula: /\p{Lu}/u.test(valor),
        minuscula: /\p{Ll}/u.test(valor),
        numero: /[0-9]/.test(valor)
    };
}
function validarContraseñaRegistrar(enfocar = true) {
    if (!Object.values(requisitosContraseñaRegistrar()).every(Boolean)) {
        return mostrarErrorCampoRegistro('contraseña_registrar', 'Revisa los requisitos de la contraseña: al menos 8 caracteres, una mayúscula, una minúscula y un número. Si es muy larga, reduce su longitud.', enfocar);
    }
    confirmarCampoRegistro('contraseña_registrar');
    return true;
}

// TITULO 3 ACTUALIZAR LOS REQUISITOS MIENTRAS SE ESCRIBE
function actualizarRequisitosContraseñaRegistrar() {
    const reglas = requisitosContraseñaRegistrar();
    document.querySelectorAll('[data-requisito-registro]').forEach(elemento => {
        const cumple = reglas[elemento.dataset.requisitoRegistro];
        elemento.dataset.cumple = String(cumple);
        elemento.querySelector('[data-estado-requisito]').textContent = cumple ? 'Cumplido: ' : 'Pendiente: ';
    });
}
entradaContraseñaRegistrar?.addEventListener('input', actualizarRequisitosContraseñaRegistrar);
entradaContraseñaRegistrar?.addEventListener('blur', () => {
    if (entradaContraseñaRegistrar.value) validarContraseñaRegistrar(false);
});
document.getElementById('mostrar_contraseña_registrar')?.addEventListener('click', evento => {
    const visible = entradaContraseñaRegistrar.type === 'password';
    entradaContraseñaRegistrar.type = visible ? 'text' : 'password';
    evento.currentTarget.textContent = visible ? 'Ocultar' : 'Mostrar';
    evento.currentTarget.setAttribute('aria-pressed', String(visible));
});
actualizarRequisitosContraseñaRegistrar();
