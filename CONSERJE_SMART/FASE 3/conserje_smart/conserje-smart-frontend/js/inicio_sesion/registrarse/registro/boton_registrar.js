// TITULO 1 PREPARAR EL ENVIO DEL FORMULARIO
const botonRegistrar = document.getElementById('boton_registrar');
let enviandoRegistro = false;
let registroCompletado = false;
document.getElementById('registro')?.addEventListener('submit', evento => {
    evento.preventDefault();
    abrirValidacionTemporal();
});

// TITULO 2 VALIDAR LOS DATOS ANTES DE SOLICITAR LA CUENTA
function validarDatosRegistro() {
    return validarNombreRegistrar() && validarApellidoRegistrar() && validarRutRegistrar() &&
        validarTelefonoRegistrar() && validarRolRegistrar() && validarCondominioRegistrar() && validarContraseñaRegistrar();
}
function abrirValidacionTemporal() {
    if (enviandoRegistro || registroCompletado || !validarDatosRegistro()) return;
    if (obtenerRolRegistrar() === 'conserje') return registrarUsuario();
    const dialogo = document.getElementById('ventana_emergente');
    const mensaje = document.getElementById('mensaje_validacion_temporal');
    if (mensaje) mensaje.hidden = true;
    dialogo?.showModal();
}

// TITULO 3 GENERAR EL CORREO SIN MODIFICAR LOS NOMBRES PERSONALES
function crearCorreoRegistrar(nombre, apellido, rol) {
    const limpiar = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().replace(/[^a-z0-9]/g, '');
    const dominios = { conserje: '@conserje.smart.cl', admin: '@admin.conserje.smart.cl', residente: '@residente.conserje.smart.cl' };
    return limpiar(nombre) + limpiar(apellido).slice(0, rol === 'residente' ? 1 : 2) + dominios[rol];
}

// TITULO 4 PRESENTAR RESPUESTAS DEL SERVIDOR SIN ALERTAS DEL NAVEGADOR
function mensajeEnvioRegistro(texto, tipo = 'error') {
    const dialogo = document.getElementById('ventana_emergente');
    const mensaje = document.getElementById(dialogo?.open ? 'mensaje_validacion_temporal' : 'mensaje_registro');
    if (!mensaje) return;
    mensaje.textContent = texto;
    mensaje.dataset.estado = tipo;
    mensaje.hidden = !texto;
    if (tipo === 'error') mensaje.focus();
}

// TITULO 5 ENVIAR LOS DATOS Y PROTEGER LA SOLICITUD DE ENVIOS DUPLICADOS
async function registrarUsuario() {
    if (enviandoRegistro || registroCompletado || !validarDatosRegistro()) return;
    const nombre = obtenerNombreRegistrar(), apellido = obtenerApellidoRegistrar(), rol = obtenerRolRegistrar();
    const correo = crearCorreoRegistrar(nombre, apellido, rol);
    const datos = new FormData();
    const valores = { rut: obtenerRutRegistrar(), nombre, apellido, telefono: obtenerTelefonoRegistrar(),
        correo, rol, contraseña: obtenerContraseñaRegistrar(), condominio: obtenerCondominioRegistrar() };
    for (const [campo, valor] of Object.entries(valores)) datos.set(campo, valor);
    if (rol === 'conserje') datos.set('token_invitacion', obtenerInvitacionConserjeRegistrar());
    else {
        datos.set('rut_admin', document.getElementById('rut_admin_validacion_temporal')?.value || '');
        datos.set('codigo_auth_admin', document.getElementById('codigo_auth_admin_validacion_temporal')?.value || '');
    }
    const formulario = document.getElementById('registro');
    const controles = [botonRegistrar, document.getElementById('boton_validar_validacion_temporal'), document.getElementById('boton_cancelar_validacion_temporal')].filter(Boolean);
    enviandoRegistro = true;
    controles.forEach(boton => { boton.disabled = true; });
    formulario.setAttribute('aria-busy', 'true');
    document.getElementById('texto_boton_registrar').textContent = 'Enviando solicitud...';
    mensajeEnvioRegistro('Enviando tus datos...', 'info');
    try {
        const respuesta = await fetch('/conserje_smart/conserje-smart-backend/php/inicio_sesion/registrarse/seguridad/registrar_usuario.php',
            { method: 'POST', body: datos, cache: 'no-store', headers: { Accept: 'application/json' } });
        let resultado;
        try { resultado = await respuesta.json(); }
        catch { throw new Error('El servidor no devolvió una respuesta válida. Inténtalo nuevamente.'); }
        if (!respuesta.ok || resultado.exito !== true) {
            const campo = resultado.datos?.campo;
            if (['nombre', 'apellido', 'rut', 'telefono', 'contraseña', 'condominio', 'rol'].includes(campo)) {
                document.getElementById('ventana_emergente')?.close();
                mostrarErrorCampoRegistro(campo + '_registrar', resultado.mensaje);
            }
            mensajeEnvioRegistro(resultado.mensaje || 'No se pudo enviar la solicitud.');
            return;
        }
        registroCompletado = true;
        document.getElementById('ventana_emergente')?.close();
        if (rol === 'conserje') {
            try { sessionStorage.removeItem('conserje.registro.invitacion'); } catch { /* el resultado se conserva en esta pantalla */ }
        }
        formulario.classList.add('registro_enviado');
        formulario.querySelectorAll('input, select, button').forEach(control => { control.disabled = true; });
        mensajeEnvioRegistro(resultado.mensaje + ' Tu correo de acceso: ' + (resultado.datos?.correo || correo), 'exito');
        document.getElementById('mensaje_registro').focus();
        document.getElementById('acceso_login_registro').textContent = 'Ir al inicio de sesión';
    } catch (error) {
        mensajeEnvioRegistro(error.message || 'No se pudo comunicar con el servidor. Revisa tu conexión.');
    } finally {
        enviandoRegistro = false;
        formulario.removeAttribute('aria-busy');
        if (!registroCompletado) controles.forEach(boton => { boton.disabled = false; });
        document.getElementById('texto_boton_registrar').textContent = registroCompletado ? 'Solicitud enviada' : 'Crear cuenta';
    }
}
