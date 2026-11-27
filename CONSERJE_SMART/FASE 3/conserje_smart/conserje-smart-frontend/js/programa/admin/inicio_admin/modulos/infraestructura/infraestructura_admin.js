// TITULO 1 IMPORTAR LOS CONTROLES Y PETICIONES DE INFRAESTRUCTURA
import { celdaAdmin, botonAdmin, mensajeAdmin, prepararDialogoAdmin, notificarAdmin } from '../autorizaciones/backend_autorizaciones/api_admin.js';
import { listarInfraestructuraAdmin, guardarTorreAdmin, guardarDepartamentoAdmin, generarDepartamentosAdmin } from './backend_infraestructura/infraestructura_api_admin.js';

// TITULO 2 INICIALIZAR LA ESTRUCTURA DEL CONDOMINIO
export function iniciarInfraestructuraAdmin() {
    const filtros = document.getElementById('filtros_infraestructura_admin');
    const mensaje = document.getElementById('mensaje_infraestructura_admin');
    const cuerpo = document.getElementById('filas_infraestructura_admin');
    const anterior = document.getElementById('anterior_infraestructura_admin');
    const siguiente = document.getElementById('siguiente_infraestructura_admin');
    const actualizar = document.getElementById('actualizar_infraestructura_admin');
    const modalTorre = prepararDialogoAdmin('dialogo_torre_admin');
    const modalDepartamento = prepararDialogoAdmin('dialogo_departamento_admin');
    const modalGeneracion = prepararDialogoAdmin('dialogo_generacion_admin');
    const formTorre = document.getElementById('formulario_torre_admin');
    const formDepartamento = document.getElementById('formulario_departamento_admin');
    const formGeneracion = document.getElementById('formulario_generacion_admin');
    const confirmar = document.getElementById('confirmar_generacion_admin');
    const vistaPrevia = document.getElementById('vista_previa_generacion_admin');
    let pagina = 1, torres = [], departamentos = [], solicitud, firma = '';

    // TITULO 3 MOSTRAR LAS TORRES SIN SUPONER SUS CANTIDADES DE PISOS
    function dibujarTorres() {
        const lista = document.getElementById('lista_torres_admin');
        lista.replaceChildren();
        if (!torres.length) {
            const vacio = document.createElement('p');
            vacio.className = 'estado_vacio_admin';
            vacio.textContent = 'Todavía no hay torres configuradas. Agrega las que existen en tu condominio.';
            lista.append(vacio);
        }
        for (const torre of torres) {
            const tarjeta = document.createElement('article');
            tarjeta.className = 'torre_infra_admin';
            const nombre = document.createElement('strong');
            nombre.textContent = torre.nombre;
            const detalle = document.createElement('p');
            detalle.textContent = torre.cantidad_pisos == null ? 'Importada de la BD. Confirma sus pisos.' :
                torre.cantidad_pisos + ' pisos · desde ' + torre.piso_inicial + ' hasta ' + (Number(torre.piso_inicial) + Number(torre.cantidad_pisos) - 1);
            tarjeta.append(nombre, detalle, botonAdmin('Configurar', 'editar_torre', torre.id_torre, torre.nombre));
            lista.append(tarjeta);
        }
        const anteriorFiltro = filtros.elements.torre.value;
        filtros.elements.torre.replaceChildren(new Option('Todas', ''), new Option('Sin torre asignada', 'sin_torre'));
        for (const torre of torres) filtros.elements.torre.add(new Option(torre.nombre, torre.id_torre));
        filtros.elements.torre.value = [...filtros.elements.torre.options].some(op => op.value === anteriorFiltro) ? anteriorFiltro : '';
        document.querySelectorAll('[data-torres-infra]').forEach(select => {
            const valor = select.value;
            select.replaceChildren(new Option('Selecciona una torre', ''));
            for (const torre of torres) {
                const opcion = new Option(torre.nombre + (torre.cantidad_pisos == null ? ' · configura pisos' : ''), torre.id_torre);
                opcion.disabled = torre.cantidad_pisos == null;
                select.add(opcion);
            }
            select.value = valor;
        });
        const disponibles = torres.some(t => t.cantidad_pisos != null);
        document.getElementById('nuevo_departamento_admin').disabled = !disponibles;
        document.getElementById('generar_departamentos_admin').disabled = !disponibles;
    }

    // TITULO 4 CONSULTAR DEPARTAMENTOS Y PAGINACION
    async function cargar() {
        solicitud?.abort();
        const actual = new AbortController(); solicitud = actual;
        actualizar.disabled = anterior.disabled = siguiente.disabled = true;
        cuerpo.setAttribute('aria-busy', 'true');
        mensajeAdmin(mensaje, 'Consultando infraestructura...');
        try {
            const resultado = await listarInfraestructuraAdmin({ ...Object.fromEntries(new FormData(filtros)), pagina }, actual.signal);
            if (actual.signal.aborted) return;
            if (!resultado.datos.departamentos.length && pagina > 1) { pagina--; return cargar(); }
            torres = resultado.datos.torres; departamentos = resultado.datos.departamentos;
            dibujarTorres();
            const resumen = document.getElementById('resumen_infraestructura_admin');
            resumen.replaceChildren();
            for (const texto of [
                resultado.datos.totales.torres + ' torres',
                resultado.datos.totales.departamentos + ' departamentos',
                resultado.datos.totales.sin_torre + ' sin torre asignada',
                resultado.datos.totales.torres_sin_configurar + ' torres por revisar'
            ]) {
                const dato = document.createElement('span'); dato.textContent = texto; resumen.append(dato);
            }
            cuerpo.replaceChildren();
            for (const depto of departamentos) {
                const fila = document.createElement('tr');
                celdaAdmin(fila, '#' + depto.id_departamento);
                celdaAdmin(fila, depto.torre_departamento || 'Sin torre asignada');
                celdaAdmin(fila, depto.numero_departamento);
                celdaAdmin(fila, depto.piso_departamento);
                celdaAdmin(fila, '').append(botonAdmin('Editar / asignar', 'editar_departamento', depto.id_departamento, depto.numero_departamento));
                cuerpo.append(fila);
            }
            if (!departamentos.length) {
                const fila = document.createElement('tr');
                celdaAdmin(fila, 'No hay departamentos para esta búsqueda.').colSpan = 5; cuerpo.append(fila);
            }
            document.getElementById('pagina_infraestructura_admin').textContent = 'Página ' + pagina + ' · ' + resultado.total + ' departamentos';
            anterior.disabled = pagina === 1; siguiente.disabled = !resultado.hay_mas;
            mensajeAdmin(mensaje);
        } catch (error) {
            if (error.name !== 'AbortError') mensajeAdmin(mensaje, error.message, 'error');
        } finally {
            if (solicitud === actual) { actualizar.disabled = false; cuerpo.removeAttribute('aria-busy'); }
        }
    }
    filtros.addEventListener('submit', evento => { evento.preventDefault(); pagina = 1; cargar(); });
    filtros.elements.torre.addEventListener('change', () => { pagina = 1; cargar(); });
    actualizar.addEventListener('click', cargar);
    anterior.addEventListener('click', () => { if (pagina > 1) { pagina--; cargar(); } });
    siguiente.addEventListener('click', () => { pagina++; cargar(); });
    document.addEventListener('admin:seccion', ({ detail }) => { if (detail === 'infraestructura') cargar(); });

    // TITULO 5 ABRIR LA CONFIGURACION DE UNA TORRE
    document.getElementById('nueva_torre_admin').addEventListener('click', () => {
        formTorre.reset();
        document.getElementById('titulo_torre_admin').textContent = 'Agregar torre';
        mensajeAdmin(document.getElementById('mensaje_torre_admin'));
        modalTorre.dialogo.showModal();
    });
    document.getElementById('lista_torres_admin').addEventListener('click', evento => {
        const boton = evento.target.closest('button[data-accion="editar_torre"]');
        const torre = boton && torres.find(t => String(t.id_torre) === boton.dataset.id);
        if (!torre) return;
        formTorre.reset();
        for (const nombre of ['id_torre', 'version', 'nombre', 'piso_inicial', 'cantidad_pisos']) formTorre.elements.namedItem(nombre).value = torre[nombre] ?? '';
        document.getElementById('titulo_torre_admin').textContent = 'Configurar torre ' + torre.nombre;
        mensajeAdmin(document.getElementById('mensaje_torre_admin'));
        modalTorre.dialogo.showModal();
    });

    // TITULO 6 CONFIGURAR EL DEPARTAMENTO SIN CAMBIAR SU IDENTIFICADOR
    document.getElementById('nuevo_departamento_admin').addEventListener('click', () => {
        formDepartamento.reset();
        document.getElementById('titulo_departamento_admin').textContent = 'Agregar departamento';
        mensajeAdmin(document.getElementById('mensaje_departamento_admin'));
        modalDepartamento.dialogo.showModal();
    });
    cuerpo.addEventListener('click', evento => {
        const boton = evento.target.closest('button[data-accion="editar_departamento"]');
        const depto = boton && departamentos.find(d => String(d.id_departamento) === boton.dataset.id);
        if (!depto) return;
        formDepartamento.reset();
        for (const nombre of ['id_departamento', 'version', 'numero_departamento', 'piso_departamento']) formDepartamento.elements.namedItem(nombre).value = depto[nombre] ?? '';
        formDepartamento.elements.id_torre.value = depto.id_torre_fk ?? '';
        ajustarPisos(formDepartamento);
        document.getElementById('titulo_departamento_admin').textContent = 'Editar departamento #' + depto.id_departamento;
        mensajeAdmin(document.getElementById('mensaje_departamento_admin'));
        modalDepartamento.dialogo.showModal();
    });
    function ajustarPisos(formulario) {
        const torre = torres.find(t => String(t.id_torre) === formulario.elements.id_torre.value);
        for (const nombre of ['piso_departamento', 'piso_desde', 'piso_hasta']) {
            const campo = formulario.elements.namedItem(nombre);
            if (!campo) continue;
            if (torre?.cantidad_pisos != null) {
                campo.min = torre.piso_inicial; campo.max = Number(torre.piso_inicial) + Number(torre.cantidad_pisos) - 1;
            } else { campo.removeAttribute('min'); campo.removeAttribute('max'); }
        }
    }
    formDepartamento.elements.id_torre.addEventListener('change', () => ajustarPisos(formDepartamento));
    formGeneracion.elements.id_torre.addEventListener('change', () => ajustarPisos(formGeneracion));

    // TITULO 7 GUARDAR LOS CAMBIOS DE TORRES Y DEPARTAMENTOS
    for (const [formulario, modal, operacion, idMensaje] of [
        [formTorre, modalTorre, guardarTorreAdmin, 'mensaje_torre_admin'],
        [formDepartamento, modalDepartamento, guardarDepartamentoAdmin, 'mensaje_departamento_admin']
    ]) formulario.addEventListener('submit', async evento => {
        evento.preventDefault();
        if (modal.ocupado || !formulario.reportValidity()) return;
        const datos = new FormData(formulario), estado = document.getElementById(idMensaje);
        modal.bloquear(true); mensajeAdmin(estado, 'Guardando...');
        try {
            const resultado = await operacion(datos);
            modal.dialogo.close(); pagina = 1; await cargar();
            mensajeAdmin(mensaje, resultado.mensaje, 'exito'); notificarAdmin('infraestructura');
        } catch (error) { mensajeAdmin(estado, error.message, 'error'); }
        finally { modal.bloquear(false); }
    });

    // TITULO 8 EXIGIR UNA VISTA PREVIA PARA GENERAR DEPARTAMENTOS
    function limpiarPrevia() { firma = ''; vistaPrevia.hidden = true; confirmar.disabled = true; }
    document.getElementById('generar_departamentos_admin').addEventListener('click', () => {
        formGeneracion.reset(); limpiarPrevia();
        mensajeAdmin(document.getElementById('mensaje_generacion_admin'));
        modalGeneracion.dialogo.showModal();
    });
    formGeneracion.addEventListener('input', limpiarPrevia);
    formGeneracion.addEventListener('change', limpiarPrevia);
    async function generar(accion) {
        if (modalGeneracion.ocupado || !formGeneracion.reportValidity() || (accion === 'guardar' && !firma)) return;
        const datos = new FormData(formGeneracion), estado = document.getElementById('mensaje_generacion_admin');
        modalGeneracion.bloquear(true); mensajeAdmin(estado, accion === 'guardar' ? 'Creando departamentos...' : 'Preparando la vista previa...');
        try {
            const resultado = await generarDepartamentosAdmin(datos, accion, firma);
            if (accion === 'previsualizar') {
                firma = resultado.datos.firma;
                document.getElementById('cantidad_generacion_admin').textContent = resultado.datos.cantidad + ' departamentos nuevos. Revisa los números antes de confirmar.';
                const filas = document.getElementById('filas_generacion_admin'); filas.replaceChildren();
                for (const dato of resultado.datos.departamentos) {
                    const fila = document.createElement('tr'); celdaAdmin(fila, dato.piso); celdaAdmin(fila, dato.numero); filas.append(fila);
                }
                vistaPrevia.hidden = false; mensajeAdmin(estado, resultado.mensaje);
            } else {
                modalGeneracion.dialogo.close(); limpiarPrevia(); pagina = 1; await cargar();
                mensajeAdmin(mensaje, resultado.mensaje, 'exito'); notificarAdmin('infraestructura');
            }
        } catch (error) { limpiarPrevia(); mensajeAdmin(estado, error.message, 'error'); }
        finally { modalGeneracion.bloquear(false); confirmar.disabled = !firma; }
    }
    formGeneracion.addEventListener('submit', evento => { evento.preventDefault(); generar('previsualizar'); });
    confirmar.addEventListener('click', () => generar('guardar'));
    cargar();
}
