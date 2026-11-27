// TITULO 1 MOSTRAR ERRORES AL LADO DEL CAMPO
function mostrarErrorCampoRegistro(id, texto, enfocar = true) {
    const campo = document.getElementById(id);
    const mensaje = document.getElementById('error_' + id);
    campo?.setAttribute('aria-invalid', 'true');
    if (campo) delete campo.dataset.validado;
    if (mensaje) { mensaje.textContent = texto; mensaje.hidden = false; }
    document.dispatchEvent(new CustomEvent('registro:campo'));
    if (enfocar) campo?.focus();
    return false;
}
function limpiarErrorCampoRegistro(id) {
    const campo = document.getElementById(id);
    campo?.removeAttribute('aria-invalid');
    if (campo) delete campo.dataset.validado;
    const mensaje = document.getElementById('error_' + id);
    if (mensaje) { mensaje.textContent = ''; mensaje.hidden = true; }
}

// TITULO 2 IDENTIFICAR UN CAMPO REVISADO SIN GUARDAR SUS DATOS
function confirmarCampoRegistro(id) {
    limpiarErrorCampoRegistro(id);
    const campo = document.getElementById(id);
    if (campo) campo.dataset.validado = 'true';
    document.dispatchEvent(new CustomEvent('registro:campo'));
}

// TITULO 3 ACTUALIZAR LOS ERRORES Y EL PROGRESO DEL FORMULARIO
(() => {
    const formulario = document.getElementById('registro');
    if (!formulario) return;
    const campos = ['nombre_registrar', 'apellido_registrar', 'rut_registrar', 'telefono_registrar', 'contraseña_registrar'];
    function actualizarProgreso() {
        const completos = campos.filter(id => {
            const campo = document.getElementById(id);
            return campo?.value.trim() && campo.dataset.validado === 'true';
        }).length;
        const progreso = document.getElementById('progreso_registro');
        if (progreso) progreso.value = completos;
        document.getElementById('detalle_progreso_registro').textContent = completos + ' de ' + campos.length + ' datos personales revisados';
    }
    formulario.addEventListener('input', evento => {
        if (evento.target.id) limpiarErrorCampoRegistro(evento.target.id);
        actualizarProgreso();
    });
    formulario.addEventListener('change', evento => {
        if (evento.target.id) limpiarErrorCampoRegistro(evento.target.id);
        actualizarProgreso();
    });
    document.addEventListener('registro:restaurar', actualizarProgreso);
    document.addEventListener('registro:campo', actualizarProgreso);
    actualizarProgreso();
})();
