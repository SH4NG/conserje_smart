// TITULO 1 IMPORTAR LAS PETICIONES COMPARTIDAS
import { solicitarVisita, mostrarMensajeVisita } from '../Backend_visita/api_visita.js';

// TITULO 2 GENERAR EL QR EN EL COMPUTADOR SIN SERVICIOS EXTERNOS
let bibliotecaQr;
async function dibujarQrMovilVisita(contenedor, texto) {
    if (!window.qrcodegen) {
        bibliotecaQr ??= new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = new URL('./qr_vincular_movil_visita.js', import.meta.url).href;
            script.onload = () => resolve();
            script.onerror = () => { bibliotecaQr = null; script.remove(); reject(new Error('No se pudo cargar el generador QR.')); };
            document.head.appendChild(script);
        });
        await bibliotecaQr;
    }
    const qr = window.qrcodegen.QrCode.encodeText(texto, window.qrcodegen.QrCode.Ecc.MEDIUM);
    const espacio = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(espacio, 'svg');
    svg.setAttribute('viewBox', '0 0 ' + (qr.size + 8) + ' ' + (qr.size + 8));
    svg.setAttribute('role', 'img');
    svg.setAttribute('aria-label', 'QR temporal para vincular el teléfono');
    svg.setAttribute('shape-rendering', 'crispEdges');
    const fondo = document.createElementNS(espacio, 'rect');
    fondo.setAttribute('width', '100%');
    fondo.setAttribute('height', '100%');
    fondo.setAttribute('fill', '#fff');
    const puntos = document.createElementNS(espacio, 'path');
    let dibujo = '';
    for (let y = 0; y < qr.size; y++) {
        for (let x = 0; x < qr.size; x++) {
            if (qr.getModule(x, y)) dibujo += 'M' + (x + 4) + ',' + (y + 4) + 'h1v1h-1z';
        }
    }
    puntos.setAttribute('d', dibujo);
    puntos.setAttribute('fill', '#000');
    svg.append(fondo, puntos);
    contenedor.replaceChildren(svg);
}

// TITULO 3 INICIALIZAR LOS ELEMENTOS DEL VINCULO
export async function inicializarVincularMovilVisita() {
    const contenedor = document.getElementById('contenedor_vincular_movil_visita');
    if (!contenedor) return;
    const generar = document.getElementById('generar_qr_movil_visita');
    const qr = document.getElementById('qr_vincular_movil_visita');
    const confirmacion = document.getElementById('confirmacion_vincular_movil_visita');
    const codigo = document.getElementById('codigo_vincular_movil_visita');
    const aprobar = document.getElementById('aprobar_movil_visita');
    const revocar = document.getElementById('revocar_movil_visita');
    const mensaje = document.getElementById('mensaje_vincular_movil_visita');
    let id = Number(sessionStorage.getItem('visita.vinculo.id')) || null;
    let codigoActual = '';
    let temporizador;
    let consultando = false;
    let operando = false;
    let disponible = false;

    async function operar(accion, valores = {}) {
        const datos = new FormData();
        Object.entries(valores).forEach(([clave, valor]) => datos.set(clave, String(valor)));
        return solicitarVisita('vinculo_movil_visita.php', { parametros: { accion }, datos });
    }

    // TITULO 4 CONSULTAR SOLO EL ESTADO DE ESTE COMPUTADOR
    async function consultar() {
        window.clearTimeout(temporizador);
        if (!id || consultando || document.hidden) return;
        consultando = true;
        let intervalo = 2000;
        try {
            const resultado = await solicitarVisita('vinculo_movil_visita.php', { parametros: { accion: 'estado', id_vinculo: id } });
            const estado = resultado.datos.estado;
            qr.hidden = estado !== 'pendiente';
            confirmacion.hidden = estado !== 'solicitado';
            codigoActual = resultado.datos.codigo || '';
            codigo.textContent = codigoActual.replace(/(...)(...)/, '$1 $2');
            if (estado !== 'pendiente') qr.replaceChildren();
            if (estado === 'pendiente' && !qr.firstElementChild) {
                generar.disabled = !disponible;
                mostrarMensajeVisita(mensaje, 'El QR anterior no se conserva al recargar. Cancela la solicitud o genera un QR nuevo.');
            } else if (estado === 'solicitado') {
                mostrarMensajeVisita(mensaje, 'Un teléfono solicita acceso. Autoriza solo si los códigos coinciden.');
            } else if (estado === 'aprobado') {
                mostrarMensajeVisita(mensaje, 'Autorizado. Esperando que el teléfono termine la conexión.');
            } else if (estado === 'activo') {
                intervalo = 5000;
                revocar.textContent = 'Desvincular teléfono';
                generar.disabled = true;
                mostrarMensajeVisita(mensaje, 'Teléfono vinculado. Sus registros aparecerán en el tablero.', 'exito');
                document.dispatchEvent(new CustomEvent('visita:actualizada'));
            } else if (estado === 'vencido' || estado === 'revocado') {
                finalizar();
                mostrarMensajeVisita(mensaje, 'La vinculación terminó. Puedes generar un QR nuevo.');
            }
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
            if (error.codigoHttp === 404 || error.codigoHttp === 401) finalizar();
        } finally {
            consultando = false;
            if (id) temporizador = window.setTimeout(consultar, intervalo);
        }
    }

    // TITULO 5 RETIRAR EL QR Y SUS ESTADOS AL TERMINAR
    function finalizar() {
        id = null;
        sessionStorage.removeItem('visita.vinculo.id');
        window.clearTimeout(temporizador);
        qr.replaceChildren();
        qr.hidden = true;
        confirmacion.hidden = true;
        revocar.hidden = true;
        generar.disabled = !disponible;
    }

    // TITULO 6 CREAR UN VINCULO NUEVO Y MOSTRAR SU QR
    generar.addEventListener('click', async () => {
        if (operando) return;
        operando = true;
        generar.disabled = true;
        try {
            const resultado = await operar('crear');
            id = resultado.datos.id_vinculo;
            sessionStorage.setItem('visita.vinculo.id', String(id));
            await dibujarQrMovilVisita(qr, resultado.datos.url);
            qr.hidden = false;
            revocar.hidden = false;
            revocar.textContent = 'Cancelar vinculación';
            mostrarMensajeVisita(mensaje, resultado.mensaje);
            consultar();
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
            generar.disabled = !disponible;
        } finally {
            operando = false;
        }
    });

    // TITULO 7 CONFIRMAR EL CODIGO DEL TELEFONO
    aprobar.addEventListener('click', async () => {
        if (operando || !id) return;
        operando = true;
        aprobar.disabled = true;
        try {
            await operar('aprobar', { id_vinculo: id, codigo: codigoActual });
            confirmacion.hidden = true;
            await consultar();
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            operando = false;
            aprobar.disabled = false;
        }
    });

    // TITULO 8 CANCELAR O REVOCAR EL ACCESO DEL TELEFONO
    revocar.addEventListener('click', async () => {
        if (operando || !id) return;
        operando = true;
        revocar.disabled = true;
        try {
            await operar('revocar', { id_vinculo: id });
            finalizar();
            mostrarMensajeVisita(mensaje, 'Teléfono desvinculado.');
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            operando = false;
            revocar.disabled = false;
        }
    });

    // TITULO 9 COMPROBAR LA DIRECCION DEL SERVIDOR
    try {
        const resultado = await solicitarVisita('vinculo_movil_visita.php', { parametros: { accion: 'configuracion' } });
        disponible = resultado.datos.disponible;
        generar.disabled = !disponible || Boolean(id);
        mostrarMensajeVisita(mensaje, resultado.datos.mensaje, disponible ? '' : 'error');
        if (id) {
            revocar.hidden = false;
            consultar();
        }
    } catch (error) {
        mostrarMensajeVisita(mensaje, error.message, 'error');
    }
    document.addEventListener('visibilitychange', () => { if (!document.hidden) consultar(); });
    window.addEventListener('pagehide', () => window.clearTimeout(temporizador));
}
