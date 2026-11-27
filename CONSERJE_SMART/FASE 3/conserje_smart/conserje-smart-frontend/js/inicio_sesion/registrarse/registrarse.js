// TITULO 1 RESTAURAR EL ESTADO VISUAL AL VOLVER AL FORMULARIO
window.addEventListener('pageshow', () => {
    actualizarRequisitosContraseñaRegistrar();
    actualizarTituloRegistro();
    document.dispatchEvent(new CustomEvent('registro:restaurar'));
});

// TITULO 2 OCULTAR LA CONTRASEÑA AL SALIR DE LA PAGINA
function ocultarContraseñaPaginaRegistro() {
    const campo = document.getElementById('contraseña_registrar');
    const boton = document.getElementById('mostrar_contraseña_registrar');
    if (campo) campo.type = 'password';
    if (boton) {
        boton.textContent = 'Mostrar';
        boton.setAttribute('aria-pressed', 'false');
    }
}
window.addEventListener('pagehide', ocultarContraseñaPaginaRegistro);
document.addEventListener('visibilitychange', () => {
    if (document.hidden) ocultarContraseñaPaginaRegistro();
});
