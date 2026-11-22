// TITULO 1 IMPORTAR LAS PETICIONES DE VINCULACION
import { solicitarVisita, mostrarMensajeVisita } from '../../Backend_visita/api_visita.js';

// TITULO 2 INICIALIZAR EL ACCESO PROPIO DEL TELEFONO
export async function inicializarVinculoAppMovilVisita() {
    const panel = document.getElementById('estado_acceso_movil_visita');
    if (!panel) return;
    const mensaje = document.getElementById('mensaje_acceso_movil_visita');
    const codigo = document.getElementById('codigo_acceso_movil_visita');
    const identidad = document.getElementById('identidad_acceso_movil_visita');
    const contenido = document.getElementById('contenido_autorizado_movil_visita');
    const cerrar = document.getElementById('cerrar_acceso_movil_visita');
    const reintentar = document.getElementById('reintentar_acceso_movil_visita');
    const meta = document.querySelector('meta[name="csrf-visita"]');
    let temporizador;
    let ocupado = false;
    let activo = false;
    let iniciado = false;
    let sesionCompleta = false;
    let ultimaActividad = 0;

    // TITULO 3 RETIRAR EL SECRETO DE LA BARRA DE DIRECCIONES
    function extraerToken() {
        const token = new URLSearchParams(location.hash.slice(1)).get('vinculo');
        if (token) history.replaceState(null, '', location.pathname + location.search);
        return token;
    }
    let tokenPendiente = extraerToken();
    function operar(accion, valores = {}) {
        const datos = new FormData();
        Object.entries(valores).forEach(([clave, valor]) => datos.set(clave, valor));
        return solicitarVisita('vinculo_movil_visita.php', { parametros: { accion }, datos });
    }
    function ocultarFormulario() {
        activo = false;
        contenido.hidden = true;
        contenido.inert = true;
        identidad.hidden = true;
        codigo.hidden = true;
        document.dispatchEvent(new CustomEvent('visita:sesion-cerrada'));
    }

    // TITULO 4 MOSTRAR EL ESTADO AUTORIZADO Y PREPARAR EL REGISTRO
    function presentar(datos) {
        if (datos.csrf) meta.content = datos.csrf;
        codigo.hidden = datos.estado !== 'solicitado';
        cerrar.hidden = datos.estado === 'sin_vinculo';
        if (datos.estado === 'activo') {
            // al cambiar de vínculo se recarga para descartar departamentos anteriores
            if (iniciado && !activo) { location.reload(); return; }
            activo = true;
            sesionCompleta = datos.sesion_completa === true;
            cerrar.textContent = sesionCompleta ? 'Cerrar sesión' : 'Desvincular teléfono';
            identidad.textContent = `${datos.nombre_conserje} · ${datos.nombre_condominio}`;
            identidad.hidden = false;
            contenido.hidden = false;
            contenido.inert = false;
            mostrarMensajeVisita(mensaje, 'Acceso autorizado. Escanea el carnet, completa los datos y registra la visita.', 'exito');
            if (!iniciado) {
                iniciado = true;
                ultimaActividad = Date.now();
                document.dispatchEvent(new CustomEvent('visita:movil-autorizado'));
            }
        } else {
            ocultarFormulario();
            if (datos.estado === 'solicitado') {
                codigo.textContent = (datos.codigo || '').replace(/(...)(...)/, '$1 $2');
                codigo.hidden = false;
                mostrarMensajeVisita(mensaje, 'Comprueba que este código coincida con el del computador. Allí debes pulsar Autorizar teléfono.');
            } else {
                mostrarMensajeVisita(mensaje, 'Escanea con la cámara de tu teléfono el QR que muestra Visita en el computador del conserje.');
            }
        }
    }

    // TITULO 5 SOLICITAR EL VINCULO Y ESPERAR LA APROBACION
    async function comprobar() {
        clearTimeout(temporizador);
        if (ocupado || document.hidden) return;
        ocupado = true;
        let espera = 30000;
        try {
            let resultado = await solicitarVisita('vinculo_movil_visita.php', { parametros: { accion: 'estado' } });
            if (resultado.datos.csrf) meta.content = resultado.datos.csrf;
            if (tokenPendiente) {
                const token = tokenPendiente;
                tokenPendiente = null;
                ocultarFormulario();
                resultado = await operar('solicitar', { token });
            }
            if (resultado.datos.estado === 'aprobado') resultado = await operar('activar');
            presentar(resultado.datos);
            if (resultado.datos.estado === 'solicitado') espera = 2000;
            if (resultado.datos.estado === 'sin_vinculo') espera = 0;
        } catch (error) {
            if ([401, 403, 409, 410].includes(error.codigoHttp)) {
                ocultarFormulario();
                cerrar.hidden = true;
                espera = 0;
            }
            mostrarMensajeVisita(mensaje, error.message + ' Puedes comprobar la conexión o generar otro QR desde el computador.', 'error');
        } finally {
            ocupado = false;
            if (espera) temporizador = setTimeout(comprobar, espera);
        }
    }

    // TITULO 6 DESVINCULAR SIN COPIAR LA SESION DEL COMPUTADOR
    cerrar.addEventListener('click', async () => {
        if (ocupado) return;
        if (sesionCompleta) {
            location.assign('/conserje_smart/php/inicio_sesion/autentificacion/seguridad/perfil_usuarios/cerrar_sesion.php');
            return;
        }
        ocupado = true;
        cerrar.disabled = true;
        try {
            await operar('revocar');
            clearTimeout(temporizador);
            ocultarFormulario();
            cerrar.hidden = true;
            mostrarMensajeVisita(mensaje, 'Teléfono desvinculado. Para volver a registrar, escanea un QR nuevo del computador.');
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            ocupado = false;
            cerrar.disabled = false;
        }
    });

    // TITULO 7 RENOVAR SOLO CUANDO EL CONSERJE UTILIZA LA APP
    async function registrarActividad() {
        if (!activo || sesionCompleta || ocupado || Date.now() - ultimaActividad < 30000) return;
        ultimaActividad = Date.now();
        try {
            await operar('mantener');
        } catch (error) {
            if (error.codigoHttp === 401) {
                ocultarFormulario();
                mostrarMensajeVisita(mensaje, error.message, 'error');
            }
        }
    }
    for (const evento of ['pointerdown', 'keydown', 'input']) {
        contenido.addEventListener(evento, registrarActividad, { passive: true });
    }
    document.addEventListener('visita:acceso-revocado', comprobar);
    reintentar.addEventListener('click', comprobar);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) comprobar(); });
    window.addEventListener('hashchange', () => { tokenPendiente = extraerToken(); comprobar(); });
    window.addEventListener('pagehide', () => clearTimeout(temporizador));
    await comprobar();
}
