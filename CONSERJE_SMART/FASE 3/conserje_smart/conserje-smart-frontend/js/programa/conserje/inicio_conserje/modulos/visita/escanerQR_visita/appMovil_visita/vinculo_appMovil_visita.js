// TITULO 1 IMPORTAR LAS PETICIONES DE VINCULACION
import { solicitarVisita, mostrarMensajeVisita } from '../../backend_visita/api_visita.js';

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
    let comprobacionPendiente = false;
    let activo = false;
    let iniciado = false;
    let sesionCompleta = false;
    let ultimaActividad = 0;
    let lecturaEnviada = null;

    // TITULO 3 MOSTRAR LA ENTREGA Y LA CONFIRMACION DE LA LECTURA
    function presentarLectura(lectura) {
        const rut = document.getElementById('rut_enviado_movil_visita');
        const estado = document.getElementById('estado_lectura_movil_visita');
        if (!rut || !estado) return;
        const tarjeta = rut.closest('.lectura_enviada_movil_visita');
        if (tarjeta) tarjeta.dataset.estado = lectura?.estado || 'sin_lectura';
        if (!lectura) {
            if (lecturaEnviada) {
                estado.textContent = 'La lectura fue descartada desde el computador. Puedes escanear otro carnet.';
                lecturaEnviada = null;
                rut.textContent = 'Sin lectura pendiente';
            }
            return;
        }
        lecturaEnviada = lectura;
        rut.textContent = lectura.rut_visitante;
        estado.textContent = lectura.estado === 'registrada'
            ? 'Visita registrada desde el computador. Registro #' + lectura.id_visita + '. Puedes escanear otro carnet.'
            : lectura.estado === 'vencida'
                ? 'La lectura venció. Vuelve a escanear el carnet.'
                : 'RUT enviado. El conserje debe completar y confirmar la visita en el computador.';
    }

    // TITULO 4 RETIRAR EL SECRETO DE LA BARRA DE DIRECCIONES
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
        panel.dataset.estado = 'sin_vinculo';
        activo = false;
        contenido.hidden = true;
        contenido.inert = true;
        identidad.hidden = true;
        codigo.hidden = true;
        document.dispatchEvent(new CustomEvent('visita:sesion-cerrada'));
    }

    // TITULO 5 MOSTRAR EL ESTADO AUTORIZADO Y PREPARAR EL REGISTRO
    function presentar(datos) {
        panel.dataset.estado = datos.estado;
        const titulo = document.getElementById('titulo_acceso_movil_visita');
        if (titulo) titulo.textContent = datos.estado === 'activo' ? 'Conectado al computador' : 'Vincular con el computador';
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
            mostrarMensajeVisita(mensaje, 'Acceso autorizado. Escanea el carnet; el registro se completa y confirma en el computador.', 'exito');
            presentarLectura(datos.lectura || null);
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

    // TITULO 6 SOLICITAR EL VINCULO Y ESPERAR LA APROBACION
    async function comprobar() {
        clearTimeout(temporizador);
        if (document.hidden) return;
        if (ocupado) { comprobacionPendiente = true; return; }
        comprobacionPendiente = false;
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
            if (resultado.datos.estado === 'solicitado' || resultado.datos.lectura?.estado === 'pendiente') espera = 2000;
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
            if (comprobacionPendiente) temporizador = setTimeout(comprobar, 0);
            else if (espera) temporizador = setTimeout(comprobar, espera);
        }
    }

    // TITULO 7 DESVINCULAR SIN COPIAR LA SESION DEL COMPUTADOR
    cerrar.addEventListener('click', async () => {
        if (ocupado) return;
        if (sesionCompleta) {
            location.assign('/conserje_smart/conserje-smart-frontend/php/inicio_sesion/autentificacion/seguridad/perfil_usuarios/cerrar_sesion.php');
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

    // TITULO 8 RENOVAR SOLO CUANDO EL CONSERJE UTILIZA LA APP
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
    document.addEventListener('visita:lectura-enviada', ({ detail }) => {
        presentarLectura(detail);
        ultimaActividad = Date.now();
        comprobar();
    });
    document.addEventListener('visita:acceso-revocado', comprobar);
    reintentar.addEventListener('click', comprobar);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) comprobar(); });
    window.addEventListener('hashchange', () => { tokenPendiente = extraerToken(); comprobar(); });
    window.addEventListener('pagehide', () => clearTimeout(temporizador));
    await comprobar();
}

