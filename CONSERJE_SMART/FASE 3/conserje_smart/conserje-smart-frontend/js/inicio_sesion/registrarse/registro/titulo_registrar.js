// TITULO 1 ACTUALIZAR LAS INSTRUCCIONES SEGUN EL PERFIL
function actualizarTituloRegistro() {
    const rol = document.getElementById('rol_registrar')?.value;
    const descripcion = document.getElementById('descripcion_registrar');
    if (!descripcion) return;
    descripcion.textContent = rol === 'conserje'
        ? 'Completa tus datos. El administrador revisará tu solicitud antes de habilitar el acceso.'
        : 'Completa tus datos para solicitar una cuenta en tu condominio.';
}

// TITULO 2 SINCRONIZAR EL TITULO CON EL FORMULARIO Y LA INVITACION
document.addEventListener('DOMContentLoaded', () => {
    actualizarTituloRegistro();
    document.getElementById('rol_registrar')?.addEventListener('change', actualizarTituloRegistro);
    document.addEventListener('registro:invitacion', actualizarTituloRegistro);
});
