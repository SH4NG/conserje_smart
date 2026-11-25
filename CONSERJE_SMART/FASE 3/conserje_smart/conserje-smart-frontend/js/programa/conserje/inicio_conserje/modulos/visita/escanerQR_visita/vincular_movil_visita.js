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
    let lecturaActual = null;
    let ultimaConfirmada = null;
    const panelLectura = document.getElementById('lectura_recibida_movil_visita');
    const usarLectura = document.getElementById('usar_lectura_movil_visita');
    const descartarLectura = document.getElementById('descartar_lectura_movil_visita');

    // TITULO 4 ACTUALIZAR EL INDICADOR Y LOS PASOS DEL VINCULO
    function presentarConexion(estado) {
        contenedor.dataset.estado = estado;
        const indicador = document.getElementById('estado_vinculo_visual_visita');
        const etiquetas = {
            listo: 'Listo para vincular', pendiente: 'QR listo para escanear',
            solicitado: 'Verifica el código', aprobado: 'Conectando teléfono',
            activo: 'Teléfono vinculado', error: 'Revisar conexión', sin_conexion: 'Acceso móvil no disponible'
        };
        if (indicador) indicador.textContent = etiquetas[estado] || 'Sin teléfono vinculado';
        contenedor.dataset.etapa = estado === 'activo' ? 'escanear' : 'vincular';
    }

    // TITULO 5 MOSTRAR LA LECTURA SOLO EN EL COMPUTADOR VINCULADO
    function presentarLectura(lectura) {
        if (!lectura) {
            if (lecturaActual) document.dispatchEvent(new CustomEvent('visita:lectura-descartada', { detail: lecturaActual }));
            lecturaActual = null;
            panelLectura.hidden = true;
            contenedor.dataset.etapa = contenedor.dataset.estado === 'activo' ? 'escanear' : 'vincular';
            return;
        }
        lecturaActual = lectura;
        panelLectura.dataset.estado = lectura.estado;
        contenedor.dataset.etapa = lectura.estado === 'pendiente' ? 'confirmar' : lectura.estado === 'registrada' ? 'registrada' : 'escanear';
        panelLectura.hidden = false;
        document.getElementById('rut_recibido_movil_visita').textContent = lectura.rut_visitante;
        document.getElementById('nombre_recibido_movil_visita').textContent = lectura.nombre_visitante || (lectura.estado === 'pendiente' ? 'Completar en la primera visita' : 'Revisado en el formulario');
        const texto = document.getElementById('estado_lectura_recibida_visita');
        usarLectura.hidden = lectura.estado !== 'pendiente';
        descartarLectura.hidden = lectura.estado === 'registrada';
        if (lectura.estado === 'pendiente') {
            texto.textContent = lectura.nombre_visitante
                ? 'Nombre recuperado del historial. Revisa los datos y completa departamento y motivo.'
                : 'Este RUT no tiene un nombre guardado en el condominio. Escríbelo una sola vez en el formulario.';
            document.dispatchEvent(new CustomEvent('visita:lectura-movil', { detail: { lectura, idVinculo: id } }));
        } else if (lectura.estado === 'registrada') {
            texto.textContent = 'Visita registrada correctamente. Registro #' + lectura.id_visita + '. El teléfono puede escanear otro carnet.';
            if (ultimaConfirmada !== lectura.id_lectura) {
                ultimaConfirmada = lectura.id_lectura;
                document.dispatchEvent(new CustomEvent('visita:actualizada'));
            }
        } else {
            texto.textContent = 'La lectura venció. Escanea de nuevo el carnet o descarta esta lectura.';
        }
    }

    async function operar(accion, valores = {}) {
        const datos = new FormData();
        Object.entries(valores).forEach(([clave, valor]) => datos.set(clave, String(valor)));
        return solicitarVisita('vinculo_movil_visita.php', { parametros: { accion }, datos });
    }

    // TITULO 6 CONSULTAR SOLO EL ESTADO DE ESTE COMPUTADOR
    async function consultar() {
        window.clearTimeout(temporizador);
        if (!id || consultando || document.hidden) return;
        consultando = true;
        let intervalo = 2000;
        try {
            const resultado = await solicitarVisita('vinculo_movil_visita.php', { parametros: { accion: 'estado', id_vinculo: id } });
            const estado = resultado.datos.estado;
            presentarConexion(estado);
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
                intervalo = 1500;
                revocar.textContent = 'Desvincular teléfono';
                generar.disabled = true;
                mostrarMensajeVisita(mensaje, 'Teléfono vinculado. Escanea el carnet en el móvil y confirma el ingreso desde este computador.', 'exito');
                presentarLectura(resultado.datos.lectura || null);
            } else if (estado === 'vencido' || estado === 'revocado') {
                finalizar();
                mostrarMensajeVisita(mensaje, 'La vinculación terminó. Puedes generar un QR nuevo.');
            }
        } catch (error) {
            presentarConexion('error');
            mostrarMensajeVisita(mensaje, error.message, 'error');
            if (error.codigoHttp === 404 || error.codigoHttp === 401) finalizar();
        } finally {
            consultando = false;
            if (id) temporizador = window.setTimeout(consultar, intervalo);
        }
    }

    // TITULO 7 RETIRAR EL QR Y SUS ESTADOS AL TERMINAR
    function finalizar() {
        id = null;
        presentarConexion(disponible ? 'listo' : 'sin_conexion');
        sessionStorage.removeItem('visita.vinculo.id');
        window.clearTimeout(temporizador);
        qr.replaceChildren();
        qr.hidden = true;
        confirmacion.hidden = true;
        revocar.hidden = true;
        generar.disabled = !disponible;
        presentarLectura(null);
    }

    // TITULO 8 CREAR UN VINCULO NUEVO Y MOSTRAR SU QR
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

    // TITULO 9 CONFIRMAR EL CODIGO DEL TELEFONO
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

    // TITULO 10 CANCELAR O REVOCAR EL ACCESO DEL TELEFONO
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

    // TITULO 11 USAR O DESCARTAR EL CARNET RECIBIDO
    usarLectura.addEventListener('click', () => {
        if (lecturaActual?.estado === 'pendiente') {
            document.dispatchEvent(new CustomEvent('visita:lectura-movil', {
                detail: { lectura: lecturaActual, idVinculo: id, forzar: true }
            }));
        }
    });
    descartarLectura.addEventListener('click', async () => {
        if (operando || !id || !lecturaActual) return;
        if (!confirm('¿Descartar este carnet sin registrar una visita?')) return;
        operando = true;
        descartarLectura.disabled = true;
        try {
            await operar('descartar_lectura', { id_vinculo: id, id_lectura: lecturaActual.id_lectura });
            presentarLectura(null);
            mostrarMensajeVisita(mensaje, 'Lectura descartada. Puedes escanear otro carnet.');
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            operando = false;
            descartarLectura.disabled = false;
        }
    });

    // TITULO 12 COMPROBAR LA DIRECCION DEL SERVIDOR
    try {
        const resultado = await solicitarVisita('vinculo_movil_visita.php', { parametros: { accion: 'configuracion' } });
        disponible = resultado.datos.disponible;
        presentarConexion(disponible ? 'listo' : 'sin_conexion');
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

