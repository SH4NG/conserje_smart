// TITULO 1 IMPORTAR LA VALIDACION DE RUT Y LOS MENSAJES
import { validarRut, formatearRut } from '../rut_visita.js';
import { mostrarMensajeVisita } from '../Backend_visita/api_visita.js';

// TITULO 2 CARGAR LA BIBLIOTECA LOCAL DEL LECTOR QR
// el archivo conserva el código original y la API de la biblioteca jsQR
let cargaLector;
function cargarLectorQR() {
    if (window.jsQR) {
        return Promise.resolve(window.jsQR);
    }
    if (!cargaLector) {
        cargaLector = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = new URL('./escanerQR_visita.js', import.meta.url).href;
            script.onload = () => {
                if (window.jsQR) {
                    resolve(window.jsQR);
                } else {
                    script.remove();
                    reject(new Error('El lector QR no está disponible.'));
                }
            };
            script.onerror = () => {
                script.remove();
                reject(new Error('No se pudo cargar el lector QR. Inténtalo nuevamente.'));
            };
            document.head.appendChild(script);
        }).catch((error) => {
            cargaLector = null;
            throw error;
        });
    }
    return cargaLector;
}

// TITULO 3 PREPARAR EL LECTOR NATIVO CUANDO EL NAVEGADOR LO ADMITE
async function crearDetectorNativoQR() {
    if (typeof window.BarcodeDetector !== 'function') {
        return null;
    }
    try {
        const formatos = await window.BarcodeDetector.getSupportedFormats();
        return formatos.includes('qr_code') ? new window.BarcodeDetector({ formats: ['qr_code'] }) : null;
    } catch {
        return null;
    }
}

// TITULO 4 EXTRAER EL RUN O RUT DEL CONTENIDO DEL QR
export function extraerRutCarnet(contenido) {
    const texto = String(contenido).trim();
    if (validarRut(texto)) {
        return formatearRut(texto);
    }
    // procesa el enlace localmente; no consulta la URL contenida en el carnet
    try {
        const url = new URL(texto);
        if (!['https:', 'http:'].includes(url.protocol)) {
            return null;
        }
        for (const [clave, valor] of url.searchParams) {
            if (['RUN', 'RUT'].includes(clave.toUpperCase()) && validarRut(valor)) {
                return formatearRut(valor);
            }
        }
    } catch {
        // un QR sin URL ni RUT reconocible no completa el formulario
    }
    return null;
}

// TITULO 5 INICIALIZAR LOS ELEMENTOS DEL ESCANER DE VISITA
export function inicializarEscanerVisita() {
    const iniciar = document.getElementById('iniciar_escaner_visita');
    if (!iniciar) {
        return;
    }
    const detener = document.getElementById('detener_escaner_visita');
    const video = document.getElementById('video_escaner_visita');
    const mensaje = document.getElementById('mensaje_escaner_visita');
    const controles = document.getElementById('controles_camara_visita');
    const controlZoom = document.getElementById('control_zoom_visita');
    const zoom = document.getElementById('zoom_camara_visita');
    const valorZoom = document.getElementById('valor_zoom_visita');
    const linterna = document.getElementById('linterna_camara_visita');
    const canvas = document.createElement('canvas');
    const contexto = canvas.getContext('2d', { willReadFrequently: true });
    let flujo = null;
    let pistaActiva = null;
    let detectorNativo = null;
    let temporizador = null;
    let intento = 0;
    let fotogramas = 0;
    let linternaEncendida = false;

    // TITULO 6 DETENER LA LECTURA Y LIBERAR LA CAMARA
    function cerrarCamara() {
        intento++;
        window.clearTimeout(temporizador);
        flujo?.getTracks().forEach((pista) => pista.stop());
        flujo = null;
        pistaActiva = null;
        detectorNativo = null;
        linternaEncendida = false;
        video.srcObject = null;
        video.hidden = true;
        iniciar.disabled = false;
        detener.disabled = true;
        if (controles) {
            controles.hidden = true;
        }
        if (zoom) {
            zoom.disabled = false;
        }
        if (linterna) {
            linterna.hidden = true;
            linterna.disabled = false;
            linterna.textContent = 'Encender luz';
            linterna.setAttribute('aria-pressed', 'false');
        }
        if (controlZoom) {
            controlZoom.hidden = true;
        }
    }

    // TITULO 7 CONFIGURAR EL ENFOQUE Y LOS CONTROLES DISPONIBLES
    async function configurarCamara(pista, idIntento) {
        if (!pista || typeof pista.applyConstraints !== 'function') {
            return;
        }
        let capacidades;
        try {
            capacidades = pista.getCapabilities?.() || {};
        } catch {
            return;
        }
        if (capacidades.focusMode?.includes('continuous')) {
            try {
                await pista.applyConstraints({ advanced: [{ focusMode: 'continuous' }] });
            } catch {
                // una función opcional de enfoque no debe impedir usar la cámara
            }
        }
        if (idIntento !== intento || pista !== pistaActiva) {
            return;
        }
        let mostrarControles = false;
        if (zoom && controlZoom && valorZoom && capacidades.zoom
            && Number.isFinite(capacidades.zoom.min) && Number.isFinite(capacidades.zoom.max)
            && capacidades.zoom.max > capacidades.zoom.min) {
            zoom.min = String(capacidades.zoom.min);
            zoom.max = String(Math.min(capacidades.zoom.max, Math.max(4, capacidades.zoom.min)));
            zoom.step = String(capacidades.zoom.step > 0 ? capacidades.zoom.step : 0.1);
            const actual = pista.getSettings?.().zoom ?? capacidades.zoom.min;
            zoom.value = String(Math.max(Number(zoom.min), Math.min(Number(zoom.max), actual)));
            valorZoom.textContent = `${Number(zoom.value).toFixed(1)}×`;
            controlZoom.hidden = false;
            mostrarControles = true;
        }
        if (linterna && capacidades.torch === true) {
            linterna.hidden = false;
            mostrarControles = true;
        }
        if (controles) {
            controles.hidden = !mostrarControles;
        }
    }

    // TITULO 8 CONSERVAR EL DETALLE DEL QR AL ANALIZAR LA IMAGEN
    function leerConBiblioteca(lector) {
        // alterna el centro sin reducirlo innecesariamente y una búsqueda de todo el cuadro
        const usarCentro = fotogramas % 3 !== 2;
        const lado = Math.floor(Math.min(video.videoWidth, video.videoHeight) * 0.8);
        const ancho = usarCentro ? lado : video.videoWidth;
        const alto = usarCentro ? lado : video.videoHeight;
        const origenX = usarCentro ? Math.floor((video.videoWidth - ancho) / 2) : 0;
        const origenY = usarCentro ? Math.floor((video.videoHeight - alto) / 2) : 0;
        const escala = Math.min(1, 1280 / Math.max(ancho, alto));
        canvas.width = Math.max(1, Math.round(ancho * escala));
        canvas.height = Math.max(1, Math.round(alto * escala));
        contexto.imageSmoothingEnabled = false;
        contexto.drawImage(video, origenX, origenY, ancho, alto, 0, 0, canvas.width, canvas.height);
        const imagen = contexto.getImageData(0, 0, canvas.width, canvas.height);
        return lector(imagen.data, imagen.width, imagen.height, {
            inversionAttempts: usarCentro ? 'dontInvert' : 'attemptBoth'
        })?.data || null;
    }

    // TITULO 9 BUSCAR EL QR SIN SUPERPONER EL ANALISIS DE FOTOGRAMAS
    async function leer(lector, idIntento) {
        if (!flujo || idIntento !== intento) {
            return;
        }
        if (video.readyState >= 2 && video.videoWidth && video.videoHeight) {
            let contenido = null;
            if (detectorNativo) {
                try {
                    const codigos = await detectorNativo.detect(video);
                    contenido = codigos.find((codigo) => extraerRutCarnet(codigo.rawValue))?.rawValue || null;
                } catch {
                    if (idIntento === intento) {
                        detectorNativo = null;
                    }
                }
            }
            if (idIntento !== intento || !flujo) {
                return;
            }
            if (!contenido && (!detectorNativo || fotogramas % 2 === 0)) {
                contenido = leerConBiblioteca(lector);
            }
            fotogramas++;
            if (contenido) {
                const rut = extraerRutCarnet(contenido);
                if (rut) {
                    const campo = document.getElementById('rut_visitante');
                    if (campo.disabled) {
                        cerrarCamara();
                        mostrarMensajeVisita(mensaje, 'Espera a que termine el registro antes de escanear otro carnet.', 'error');
                        return;
                    }
                    campo.value = rut;
                    campo.dispatchEvent(new Event('input', { bubbles: true }));
                    cerrarCamara();
                    mostrarMensajeVisita(mensaje, 'RUT leído. Completa nombre, departamento y motivo; luego pulsa Registrar visita.', 'exito');
                    document.getElementById('nombre_visitante').focus({ preventScroll: true });
                    document.getElementById('formulario_visita').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }
                mostrarMensajeVisita(mensaje, 'El QR no contiene un RUN/RUT reconocible. Prueba con el carnet o ingresa el RUT manualmente.', 'error');
            }
        }
        temporizador = window.setTimeout(() => ejecutarLectura(lector, idIntento), 100);
    }

    // TITULO 10 CONTROLAR ERRORES DEL ANALISIS DE IMAGEN
    async function ejecutarLectura(lector, idIntento) {
        try {
            await leer(lector, idIntento);
        } catch {
            if (idIntento === intento) {
                cerrarCamara();
                mostrarMensajeVisita(mensaje, 'No se pudo leer la imagen de la cámara. Vuelve a abrirla.', 'error');
            }
        }
    }

    // TITULO 11 ABRIR LA CAMARA TRASERA CON MAYOR DETALLE
    iniciar.addEventListener('click', async () => {
        if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
            mostrarMensajeVisita(mensaje, 'Para usar la cámara del móvil, abre el sistema mediante HTTPS y permite el acceso a la cámara.', 'error');
            return;
        }
        if (!contexto) {
            mostrarMensajeVisita(mensaje, 'Este navegador no permite procesar la imagen. Ingresa el RUT manualmente.', 'error');
            return;
        }
        iniciar.disabled = true;
        detener.disabled = false;
        fotogramas = 0;
        const idIntento = ++intento;
        mostrarMensajeVisita(mensaje, 'Abriendo la cámara...');
        try {
            const [lector, detector] = await Promise.all([cargarLectorQR(), crearDetectorNativoQR()]);
            if (idIntento !== intento) {
                return;
            }
            const nuevoFlujo = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1920 },
                    height: { ideal: 1080 },
                    frameRate: { ideal: 24, max: 30 }
                },
                audio: false
            });
            if (idIntento !== intento) {
                nuevoFlujo.getTracks().forEach((pista) => pista.stop());
                return;
            }
            flujo = nuevoFlujo;
            detectorNativo = detector;
            pistaActiva = flujo.getVideoTracks?.()[0] || flujo.getTracks()[0];
            video.srcObject = flujo;
            video.hidden = false;
            await video.play();
            await configurarCamara(pistaActiva, idIntento);
            if (idIntento !== intento) {
                return;
            }
            mostrarMensajeVisita(mensaje, 'Coloca el QR en el centro. Si está borroso, aleja un poco el carnet para que la cámara enfoque.');
            ejecutarLectura(lector, idIntento);
        } catch (error) {
            if (idIntento !== intento) {
                return;
            }
            cerrarCamara();
            const texto = error.name === 'NotAllowedError'
                ? 'Permite el acceso a la cámara en tu navegador para escanear el carnet.'
                : error.name === 'NotFoundError' ? 'No se encontró una cámara disponible.'
                    : error.name === 'NotReadableError' ? 'La cámara está ocupada o no se pudo acceder a ella. Cierra otras aplicaciones que la estén usando.'
                    : error.message?.includes('lector QR') ? error.message
                    : 'No se pudo iniciar el escáner. Revisa la cámara e inténtalo nuevamente.';
            mostrarMensajeVisita(mensaje, texto, 'error');
        }
    });

    // TITULO 12 AJUSTAR EL ACERCAMIENTO ADMITIDO POR LA CAMARA
    zoom?.addEventListener('input', () => {
        if (valorZoom) {
            valorZoom.textContent = `${Number(zoom.value).toFixed(1)}×`;
        }
    });
    zoom?.addEventListener('change', async () => {
        const pista = pistaActiva;
        const idIntento = intento;
        if (!pista) {
            return;
        }
        zoom.disabled = true;
        try {
            await pista.applyConstraints({ advanced: [{ zoom: Number(zoom.value) }] });
        } catch {
            if (idIntento === intento) {
                mostrarMensajeVisita(mensaje, 'No se pudo cambiar el acercamiento. Puedes seguir escaneando.', 'error');
            }
        } finally {
            if (idIntento === intento) {
                zoom.disabled = false;
            }
        }
    });

    // TITULO 13 ENCENDER O APAGAR LA LUZ CUANDO ESTE DISPONIBLE
    linterna?.addEventListener('click', async () => {
        const pista = pistaActiva;
        const idIntento = intento;
        if (!pista) {
            return;
        }
        linterna.disabled = true;
        try {
            const encender = !linternaEncendida;
            await pista.applyConstraints({ advanced: [{ torch: encender }] });
            if (idIntento === intento) {
                linternaEncendida = encender;
                linterna.textContent = encender ? 'Apagar luz' : 'Encender luz';
                linterna.setAttribute('aria-pressed', String(encender));
            }
        } catch {
            if (idIntento === intento) {
                mostrarMensajeVisita(mensaje, 'No se pudo cambiar la luz. Evita sombras y reflejos sobre el QR.', 'error');
            }
        } finally {
            if (idIntento === intento) {
                linterna.disabled = false;
            }
        }
    });

    // TITULO 14 ATENDER LA DETENCION MANUAL DE LA CAMARA
    detener.addEventListener('click', () => {
        cerrarCamara();
        mostrarMensajeVisita(mensaje, 'Cámara detenida.');
    });

    // TITULO 15 CERRAR LA CAMARA AL SALIR O ENVIAR EL FORMULARIO
    window.addEventListener('pagehide', cerrarCamara);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            cerrarCamara();
        }
    });
    document.addEventListener('visita:editar', cerrarCamara);
    document.addEventListener('visita:sesion-cerrada', cerrarCamara);
    document.getElementById('formulario_visita').addEventListener('submit', cerrarCamara);
}
