// TITULO 1 IMPORTAR PETICIONES Y VALIDACIONES DEL FORMULARIO
import { solicitarVisita, mostrarMensajeVisita, notificarCambioVisita } from './api_visita.js';
import { listarDepartamentos, etiquetaDepartamento } from './listar_visita.js';
import { modificarVisita } from './modificar_visita.js';
import { validarRut, formatearRut } from '../rut_visita.js';

// TITULO 2 ENVIAR EL REGISTRO DE LA VISITA AL BACKEND
export async function registrarVisita(datos) {
    const resultado = await solicitarVisita('registrar_visita.php', { datos });
    notificarCambioVisita();
    return resultado;
}

// TITULO 3 INICIALIZAR ELEMENTOS DEL FORMULARIO DE VISITA
export async function inicializarRegistroVisita() {
    const formulario = document.getElementById('formulario_visita');
    if (!formulario) {
        return;
    }
    const departamento = document.getElementById('select_departamento_visita');
    const resumen = document.getElementById('resumen_departamento_visita');
    const mensaje = document.getElementById('mensaje_formulario_visita');
    const boton = document.getElementById('boton_registrar_visita');
    const limpiar = formulario.querySelector('[type="reset"]');
    const titulo = document.getElementById('titulo_formulario_visita');
    const badge = document.getElementById('badge_estado_visita');
    const rut = document.getElementById('rut_visitante');
    let idEdicion = null;
    let guardando = false;
    let departamentosDisponibles = false;

    // TITULO 4 ACTUALIZAR EL RESUMEN DEL DEPARTAMENTO
    function actualizarResumen() {
        resumen.textContent = departamento.selectedOptions[0]?.value
            ? departamento.selectedOptions[0].textContent : 'Sin selección';
    }

    // TITULO 5 RESTABLECER EL MODO DE INGRESO
    function restablecer() {
        idEdicion = null;
        titulo.textContent = 'Registrar visita';
        badge.textContent = 'Ingreso de visita';
        boton.textContent = 'Registrar visita';
        limpiar.textContent = 'Limpiar';
        rut.setCustomValidity('');
        rut.removeAttribute('aria-invalid');
        actualizarResumen();
    }

    // TITULO 6 ESCUCHAR CAMBIOS Y LIMPIAR EL FORMULARIO
    departamento.addEventListener('change', actualizarResumen);
    formulario.addEventListener('reset', (evento) => {
        if (guardando) {
            evento.preventDefault();
            return;
        }
        queueMicrotask(() => {
            restablecer();
            mostrarMensajeVisita(mensaje);
        });
    });

    // TITULO 7 CARGAR LOS DATOS DE LA VISITA PARA EDITAR
    document.addEventListener('visita:editar', ({ detail: visita }) => {
        if (guardando) {
            return;
        }
        if (!departamentosDisponibles) {
            mostrarMensajeVisita(mensaje, 'No se puede editar hasta cargar los departamentos. Recarga la página.', 'error');
            return;
        }
        idEdicion = visita.id_visita;
        departamento.value = String(visita.id_departamento_fk);
        for (const nombre of ['nombre_visitante', 'rut_visitante', 'motivo_visita', 'observaciones_visita']) {
            formulario.elements.namedItem(nombre).value = visita[nombre] || '';
        }
        rut.setCustomValidity('');
        rut.removeAttribute('aria-invalid');
        actualizarResumen();
        titulo.textContent = `Modificar visita #${idEdicion}`;
        badge.textContent = 'Edición de visita';
        boton.textContent = 'Guardar cambios';
        limpiar.textContent = 'Cancelar edición';
        mostrarMensajeVisita(mensaje);
        formulario.scrollIntoView({ behavior: 'smooth', block: 'center' });
        formulario.elements.namedItem('nombre_visitante').focus({ preventScroll: true });
    });

    // TITULO 8 VALIDAR LOS CAMPOS ANTES DE ENVIAR
    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        if (guardando || !departamentosDisponibles) {
            return;
        }
        if (!validarRut(rut.value)) {
            rut.setCustomValidity('Ingresa un RUT válido.');
            rut.setAttribute('aria-invalid', 'true');
        } else {
            rut.setCustomValidity('');
            rut.removeAttribute('aria-invalid');
            rut.value = formatearRut(rut.value);
        }
        if (!formulario.reportValidity()) {
            return;
        }
        const datos = new FormData(formulario);
        for (const nombre of ['nombre_visitante', 'motivo_visita', 'observaciones_visita']) {
            datos.set(nombre, String(datos.get(nombre) || '').trim());
        }
        if (!datos.get('nombre_visitante') || !datos.get('motivo_visita')) {
            mostrarMensajeVisita(mensaje, 'Completa nombre y motivo antes de guardar.', 'error');
            return;
        }
        // TITULO 9 BLOQUEAR CONTROLES DURANTE EL ENVIO
        guardando = true;
        const textoBoton = boton.textContent;
        const controles = Array.from(formulario.elements);
        const estadosPrevios = controles.map((control) => control.disabled);
        controles.forEach((control) => {
            control.disabled = true;
        });
        boton.textContent = 'Guardando...';
        mostrarMensajeVisita(mensaje, 'Guardando los datos de la visita...');
        // TITULO 10 GUARDAR EL REGISTRO O LA MODIFICACION
        try {
            const resultado = idEdicion
                ? await modificarVisita(idEdicion, datos) : await registrarVisita(datos);
            guardando = false;
            formulario.reset();
            // El reset debe terminar antes de mostrar el resultado.
            await Promise.resolve();
            const numero = resultado.datos?.id_visita ? ` Registro #${resultado.datos.id_visita}.` : '';
            mostrarMensajeVisita(mensaje, resultado.mensaje + numero, 'exito');
        } catch (error) {
            boton.textContent = textoBoton;
            mostrarMensajeVisita(mensaje, error.message, 'error');
        } finally {
            guardando = false;
            controles.forEach((control, indice) => {
                control.disabled = estadosPrevios[indice];
            });
        }
    });

    // TITULO 11 CARGAR LOS DEPARTAMENTOS DEL CONDOMINIO
    boton.disabled = true;
    departamento.disabled = true;
    try {
        const departamentos = await listarDepartamentos();
        departamento.replaceChildren(new Option(
            departamentos.length ? 'Seleccione un departamento' : 'No hay departamentos registrados', ''
        ));
        departamentos.forEach((item) => {
            departamento.add(new Option(etiquetaDepartamento(item), String(item.id_departamento)));
        });
        departamentosDisponibles = departamentos.length > 0;
        if (!departamentosDisponibles) {
            mostrarMensajeVisita(mensaje, 'No hay departamentos registrados en este condominio.', 'error');
        }
    } catch (error) {
        departamento.replaceChildren(new Option('No disponible', ''));
        mostrarMensajeVisita(mensaje, error.message, 'error');
    } finally {
        departamento.disabled = !departamentosDisponibles;
        boton.disabled = !departamentosDisponibles;
    }
}
