// TITULO 1 CERRAR LA AUTORIZACION SIN ENVIAR DATOS
const dialogoValidacionTemporal = document.getElementById('ventana_emergente');
document.getElementById('boton_cancelar_validacion_temporal')?.addEventListener('click', () => {
    if (!enviandoRegistro) dialogoValidacionTemporal?.close();
});
dialogoValidacionTemporal?.addEventListener('cancel', evento => {
    if (enviandoRegistro) evento.preventDefault();
});

// TITULO 2 VALIDAR LOS DATOS DEL ADMINISTRADOR
function confirmarValidacionTemporal() {
    if (enviandoRegistro) return;
    const rut = document.getElementById('rut_admin_validacion_temporal');
    const codigo = document.getElementById('codigo_auth_admin_validacion_temporal');
    if (!rut?.value.trim() || !codigo?.value.trim()) {
        mensajeEnvioRegistro('Ingresa el RUT y el código de autorización del administrador.');
        (!rut?.value.trim() ? rut : codigo)?.focus();
        return;
    }
    registrarUsuario();
}
document.getElementById('boton_validar_validacion_temporal')?.addEventListener('click', confirmarValidacionTemporal);
dialogoValidacionTemporal?.addEventListener('keydown', evento => {
    if (evento.key === 'Enter' && evento.target.matches('input')) {
        evento.preventDefault();
        confirmarValidacionTemporal();
    }
});

// TITULO 3 DAR FORMATO AL RUT DEL ADMINISTRADOR MIENTRAS SE ESCRIBE
prepararFormatoRutRegistro(document.getElementById('rut_admin_validacion_temporal'));
