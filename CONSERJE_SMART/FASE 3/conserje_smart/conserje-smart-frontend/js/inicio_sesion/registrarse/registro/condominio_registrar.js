// TITULO 1 PREPARAR EL CONDOMINIO Y LA INVITACION DEL REGISTRO
const entradaCondominioRegistrar = document.getElementById('condominio_registrar');
let invitacionConserjeRegistro = null;
let validandoInvitacionRegistro = false;
let errorInvitacionRegistro = '';
const claveInvitacionRegistro = 'conserje.registro.invitacion';

// TITULO 2 EXPLICAR QUIEN ASIGNA EL CONDOMINIO
function estadoInvitacionRegistro(texto, tipo = 'info') {
    const mensaje = document.getElementById('estado_invitacion_registro');
    if (!mensaje) return;
    mensaje.hidden = !texto;
    mensaje.textContent = texto;
    mensaje.dataset.estado = tipo;
    document.dispatchEvent(new CustomEvent('registro:invitacion'));
}
function sincronizarCondominioRegistro() {
    const rol = document.getElementById('rol_registrar');
    if (!rol || !entradaCondominioRegistrar) return;
    if (invitacionConserjeRegistro) {
        rol.value = 'conserje'; rol.disabled = true;
        entradaCondominioRegistrar.disabled = true;
        estadoInvitacionRegistro('Condominio asignado por invitación: ' + invitacionConserjeRegistro.nombre_condominio +
            '. Tu cuenta quedará pendiente de aprobación.', 'ok');
    } else if (validandoInvitacionRegistro) {
        rol.disabled = true; entradaCondominioRegistrar.disabled = true;
        estadoInvitacionRegistro('Comprobando la invitación del administrador...');
    } else {
        rol.disabled = false;
        entradaCondominioRegistrar.disabled = rol.value === 'conserje';
        estadoInvitacionRegistro(rol.value === 'conserje'
            ? (errorInvitacionRegistro || 'Para solicitar una cuenta de conserje, abre el enlace de invitación que te entregue el administrador.')
            : '', rol.value === 'conserje' ? 'error' : 'info');
    }
}

// TITULO 3 CARGAR CONDOMINIOS PARA LOS OTROS PERFILES
async function cargarCondominiosRegistrar() {
    if (!entradaCondominioRegistrar) return;
    try {
        const respuesta = await fetch('/conserje_smart/conserje-smart-backend/php/inicio_sesion/registrarse/seguridad/listar_condominios.php', { cache: 'no-store' });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.exito !== true || !Array.isArray(resultado.condominios)) throw new Error('No se pudieron cargar los condominios.');
        if (invitacionConserjeRegistro || validandoInvitacionRegistro) return;
        entradaCondominioRegistrar.replaceChildren(new Option('Seleccionar condominio', ''));
        for (const condominio of resultado.condominios) entradaCondominioRegistrar.add(new Option(condominio.nombre_condominio, condominio.id_condominio));
        sincronizarCondominioRegistro();
    } catch (error) { estadoInvitacionRegistro(error.message, 'error'); }
}

// TITULO 4 RETIRAR EL TOKEN DEL ENLACE Y VALIDARLO EN EL SERVIDOR
async function prepararInvitacionRegistro() {
    const parametros = new URLSearchParams(location.hash.slice(1));
    let token = parametros.get('invitacion');
    if (token !== null) {
        history.replaceState(null, '', location.pathname + location.search);
        try { sessionStorage.setItem(claveInvitacionRegistro, token); } catch {}
    } else {
        try { token = sessionStorage.getItem(claveInvitacionRegistro); } catch { token = null; }
    }
    if (!token) { await cargarCondominiosRegistrar(); return; }
    validandoInvitacionRegistro = true;
    document.getElementById('rol_registrar').value = 'conserje';
    sincronizarCondominioRegistro();
    try {
        if (!/^[a-f0-9]{64}$/.test(token)) throw new Error('La invitación no es válida. Solicita un enlace nuevo al administrador.');
        const datos = new FormData(); datos.set('token', token);
        const respuesta = await fetch('/conserje_smart/conserje-smart-backend/php/programa/admin/inicio_admin/modulos/autorizaciones/backend_autorizaciones/validar_invitacion_conserje.php',
            { method: 'POST', body: datos, cache: 'no-store', headers: { Accept: 'application/json' } });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.exito !== true) throw new Error(resultado.mensaje || 'No se pudo validar la invitación.');
        invitacionConserjeRegistro = { ...resultado.datos, token };
        entradaCondominioRegistrar.replaceChildren(new Option(resultado.datos.nombre_condominio, resultado.datos.id_condominio));
    } catch (error) {
        invitacionConserjeRegistro = null; errorInvitacionRegistro = error.message;
        try { sessionStorage.removeItem(claveInvitacionRegistro); } catch {}
    } finally {
        validandoInvitacionRegistro = false; sincronizarCondominioRegistro();
        if (!invitacionConserjeRegistro) await cargarCondominiosRegistrar();
    }
}

// TITULO 5 ENTREGAR LOS DATOS DEL FORMULARIO SIN PERMITIR CAMBIAR LA ASIGNACION
function obtenerCondominioRegistrar() { return entradaCondominioRegistrar?.value || ''; }
function obtenerInvitacionConserjeRegistrar() { return invitacionConserjeRegistro?.token || ''; }
function validarCondominioRegistrar() {
    if (validandoInvitacionRegistro) { return mostrarErrorCampoRegistro('condominio_registrar', 'Espera a que se compruebe la invitación.'); }
    const rol = document.getElementById('rol_registrar')?.value;
    if (rol === 'conserje' && (!invitacionConserjeRegistro ||
        String(invitacionConserjeRegistro.id_condominio) !== obtenerCondominioRegistrar())) {
        return mostrarErrorCampoRegistro('condominio_registrar', errorInvitacionRegistro || 'Necesitas una invitación del administrador con el condominio asignado.');
    }
    if (!/^[1-9]\d*$/.test(obtenerCondominioRegistrar())) { return mostrarErrorCampoRegistro('condominio_registrar', 'Selecciona un condominio.'); }
    return true;
}

// TITULO 6 INICIALIZAR LA ASIGNACION Y REACCIONAR AL CAMBIO DE PERFIL
document.getElementById('rol_registrar')?.addEventListener('change', sincronizarCondominioRegistro);
prepararInvitacionRegistro();
