// TITULO 1 IMPORTAR LAS OPERACIONES DEL TABLERO
import { listarVisitas, etiquetaDepartamento } from './backend_visitas/listar_visita.js';
import { registrarSalidaVisita } from './backend_visitas/registrar_salida_visita.js';
import { prepararEdicionVisita } from './backend_visitas/modificar_visita.js';
import { mostrarMensajeVisita } from './backend_visitas/api_visita.js';

// TITULO 2 FORMATEAR FECHAS Y HORAS DE VISITA
// muestra la hora local de Santiago guardada por el backend
export function formatearFechaVisita(valor) {
    if (!valor) {
        return '—';
    }
    const partes = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/.exec(valor);
    return partes ? `${partes[3]}/${partes[2]}/${partes[1]} ${partes[4]}:${partes[5]}` : valor;
}

// TITULO 3 INICIALIZAR ELEMENTOS Y ESTADO DEL TABLERO
export function inicializarTableroVisita() {
    const cuerpo = document.getElementById('filas_tablero_visita');
    if (!cuerpo) {
        return;
    }
    const estado = document.getElementById('filtro_estado_visita');
    const mensaje = document.getElementById('mensaje_tablero_visita');
    const actualizar = document.getElementById('actualizar_tablero_visita');
    const anterior = document.getElementById('pagina_anterior_visita');
    const siguiente = document.getElementById('pagina_siguiente_visita');
    const indicador = document.getElementById('pagina_actual_visita');
    let pagina = 1;
    let visitas = [];
    let solicitud = null;
    let procesandoSalida = false;

    // TITULO 4 CREAR CELDAS CON CONTENIDO DE TEXTO
    function agregarCelda(fila, texto) {
        const celda = document.createElement('td');
        celda.textContent = texto;
        fila.appendChild(celda);
        return celda;
    }

    // TITULO 5 MOSTRAR LAS VISITAS Y SUS ACCIONES
    function dibujar() {
        cuerpo.replaceChildren();
        if (!visitas.length) {
            const fila = document.createElement('tr');
            agregarCelda(fila, 'No hay visitas para este estado.').colSpan = 8;
            cuerpo.appendChild(fila);
        }
        for (const visita of visitas) {
            const fila = document.createElement('tr');
            agregarCelda(fila, `#${visita.id_visita}`);
            const visitante = agregarCelda(fila, visita.nombre_visitante);
            const rut = document.createElement('small');
            rut.textContent = visita.rut_visitante;
            visitante.appendChild(rut);
            agregarCelda(fila, etiquetaDepartamento(visita));
            const motivo = agregarCelda(fila, visita.motivo_visita);
            if (visita.observaciones_visita) {
                const observaciones = document.createElement('small');
                observaciones.textContent = visita.observaciones_visita;
                motivo.appendChild(observaciones);
            }
            agregarCelda(fila, formatearFechaVisita(visita.fecha_hora_ingreso));
            agregarCelda(fila, formatearFechaVisita(visita.fecha_hora_salida));
            agregarCelda(fila, visita.fecha_hora_salida ? 'Finalizada' : 'En el condominio');
            const acciones = agregarCelda(fila, '');
            if (!visita.fecha_hora_salida) {
                for (const [accion, texto] of [['editar', 'Modificar'], ['salida', 'Registrar salida']]) {
                    const boton = document.createElement('button');
                    boton.type = 'button';
                    boton.className = 'boton_secundario_visita';
                    boton.dataset.accion = accion;
                    boton.dataset.id = String(visita.id_visita);
                    boton.textContent = texto;
                    boton.setAttribute('aria-label', `${texto}: ${visita.nombre_visitante}, registro ${visita.id_visita}`);
                    acciones.appendChild(boton);
                }
            } else {
                acciones.textContent = '—';
            }
            cuerpo.appendChild(fila);
        }
    }

    // TITULO 6 CARGAR LA PAGINA Y EL ESTADO SELECCIONADOS
    async function cargar({ conservarMensaje = false } = {}) {
        solicitud?.abort();
        const actual = new AbortController();
        solicitud = actual;
        actualizar.disabled = true;
        anterior.disabled = true;
        siguiente.disabled = true;
        cuerpo.setAttribute('aria-busy', 'true');
        if (!conservarMensaje) {
            mostrarMensajeVisita(mensaje, 'Cargando visitas...');
        }
        try {
            const resultado = await listarVisitas({ estado: estado.value, pagina, signal: actual.signal });
            if (actual.signal.aborted) {
                return;
            }
            // La última fila de una página puede desaparecer al registrar su salida.
            if (!resultado.datos.length && pagina > 1) {
                pagina--;
                return cargar({ conservarMensaje });
            }
            visitas = resultado.datos;
            dibujar();
            indicador.textContent = `Página ${pagina} · ${resultado.total} visitas`;
            anterior.disabled = pagina <= 1;
            siguiente.disabled = !resultado.hay_mas;
            if (!conservarMensaje) {
                mostrarMensajeVisita(mensaje);
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            visitas = [];
            cuerpo.replaceChildren();
            const fila = document.createElement('tr');
            agregarCelda(fila, 'No se pudo cargar el tablero. Usa Actualizar para reintentar.').colSpan = 8;
            cuerpo.appendChild(fila);
            indicador.textContent = 'Sin datos';
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            if (solicitud === actual) {
                actualizar.disabled = false;
                cuerpo.removeAttribute('aria-busy');
            }
        }
    }

    // TITULO 7 ATENDER LA EDICION Y EL REGISTRO DE SALIDA
    cuerpo.addEventListener('click', async (evento) => {
        const boton = evento.target.closest('button[data-accion]');
        if (!boton || procesandoSalida) {
            return;
        }
        const visita = visitas.find((item) => String(item.id_visita) === boton.dataset.id);
        if (!visita) {
            return;
        }
        if (boton.dataset.accion === 'editar') {
            prepararEdicionVisita(visita);
            return;
        }
        if (!window.confirm(`¿Registrar la salida de ${visita.nombre_visitante}, visita #${visita.id_visita}?`)) {
            return;
        }
        procesandoSalida = true;
        boton.disabled = true;
        try {
            const resultado = await registrarSalidaVisita(visita.id_visita);
            mostrarMensajeVisita(mensaje, resultado.mensaje, 'exito');
        } catch (error) {
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            procesandoSalida = false;
            boton.disabled = false;
        }
    });

    // TITULO 8 ESCUCHAR FILTROS Y CAMBIOS DE PAGINA
    estado.addEventListener('change', () => {
        pagina = 1;
        cargar();
    });
    actualizar.addEventListener('click', () => cargar());
    anterior.addEventListener('click', () => {
        if (pagina > 1) {
            pagina--;
            cargar();
        }
    });
    siguiente.addEventListener('click', () => {
        pagina++;
        cargar();
    });

    // TITULO 9 ACTUALIZAR EL TABLERO DESPUES DE GUARDAR
    document.addEventListener('visita:actualizada', () => cargar({ conservarMensaje: true }));
    cargar();
}
