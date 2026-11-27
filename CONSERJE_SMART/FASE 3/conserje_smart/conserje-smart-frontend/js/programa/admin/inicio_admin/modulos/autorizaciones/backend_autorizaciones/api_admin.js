// TITULO 1 PREPARAR LAS PETICIONES DEL ADMINISTRADOR
const base = '/conserje_smart/conserje-smart-backend/php/programa/admin/inicio_admin/modulos/';
export async function pedirAdmin(modulo, archivo, { datos, parametros = {}, signal } = {}) {
    const url = new URL(base + modulo + '/backend_' + modulo + '/' + archivo, location.origin);
    url.search = new URLSearchParams(parametros).toString();
    const headers = { Accept: 'application/json' };
    if (datos) headers['X-CSRF-Token'] = document.querySelector('meta[name="csrf-admin"]')?.content || '';
    const respuesta = await fetch(url, { method: datos ? 'POST' : 'GET', credentials: 'same-origin', headers, body: datos, signal, cache: 'no-store' });
    let resultado;
    try { resultado = await respuesta.json(); }
    catch { throw new Error('El servidor no devolvió una respuesta válida. Comprueba tu sesión y vuelve a intentar.'); }
    if (!respuesta.ok || resultado.exito !== true) {
        const error = new Error(resultado.mensaje || 'No se pudo completar la operación.');
        error.codigoHttp = respuesta.status;
        throw error;
    }
    return resultado;
}

// TITULO 2 MOSTRAR MENSAJES SIN INTERPRETAR HTML
export function mensajeAdmin(elemento, texto = '', tipo = '') {
    elemento.textContent = texto;
    elemento.classList.toggle('error_admin', tipo === 'error');
    elemento.classList.toggle('exito_admin', tipo === 'exito');
}
export function celdaAdmin(fila, texto = '—', detalle) {
    const celda = document.createElement('td');
    celda.textContent = texto == null ? '—' : String(texto);
    if (detalle) {
        const secundario = document.createElement('small');
        secundario.textContent = detalle;
        celda.append(secundario);
    }
    fila.append(celda);
    return celda;
}
export function estadoAdmin(celda, texto, tipo = '') {
    celda.replaceChildren();
    const estado = document.createElement('span');
    estado.className = 'estado_registro_admin ' + tipo;
    estado.textContent = texto;
    celda.append(estado);
}
export function fechaAdmin(valor) {
    if (!valor) return '—';
    const partes = /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}))?/.exec(valor);
    return partes ? partes[3] + '/' + partes[2] + '/' + partes[1] + (partes[4] ? ' · ' + partes[4] + ':' + partes[5] : '') : valor;
}
export function departamentoAdmin(fila) {
    return (fila.torre_departamento ? 'Torre ' + fila.torre_departamento + ' · ' : '') + 'Depto. ' + fila.numero_departamento;
}
export function botonAdmin(texto, accion, id, nombre) {
    const boton = document.createElement('button');
    boton.type = 'button';
    boton.className = 'boton_admin secundario_admin';
    boton.dataset.accion = accion;
    boton.dataset.id = String(id);
    boton.textContent = texto;
    boton.setAttribute('aria-label', texto + ': ' + nombre);
    return boton;
}

// TITULO 3 CONTROLAR FILTROS Y PAGINACION DE CADA MODULO
export function crearListadoAdmin(modulo, archivo, columnas, dibujarFila, moduloPeticion = modulo) {
    const formulario = document.getElementById('filtros_' + modulo + '_admin');
    const cuerpo = document.getElementById('filas_' + modulo + '_admin');
    const mensaje = document.getElementById('mensaje_' + modulo + '_admin');
    const actualizar = document.getElementById('actualizar_' + modulo + '_admin');
    const anterior = document.getElementById('anterior_' + modulo + '_admin');
    const siguiente = document.getElementById('siguiente_' + modulo + '_admin');
    const indicador = document.getElementById('pagina_' + modulo + '_admin');
    let pagina = 1;
    let filas = [];
    let solicitud;
    async function cargar() {
        solicitud?.abort();
        const actual = new AbortController();
        solicitud = actual;
        actualizar.disabled = anterior.disabled = siguiente.disabled = true;
        cuerpo.setAttribute('aria-busy', 'true');
        mensajeAdmin(mensaje, 'Cargando registros...');
        try {
            const parametros = Object.fromEntries(new FormData(formulario));
            parametros.pagina = pagina;
            const resultado = await pedirAdmin(moduloPeticion, archivo, { parametros, signal: actual.signal });
            if (actual.signal.aborted) return;
            if (!Array.isArray(resultado.datos)) throw new Error('El listado no tiene el formato esperado.');
            if (!resultado.datos.length && pagina > 1) { pagina--; return cargar(); }
            filas = resultado.datos;
            cuerpo.replaceChildren();
            for (const datos of filas) {
                const fila = document.createElement('tr');
                dibujarFila(fila, datos);
                cuerpo.append(fila);
            }
            if (!filas.length) {
                const fila = document.createElement('tr');
                const celda = celdaAdmin(fila, 'No hay registros que coincidan con esta búsqueda.');
                celda.colSpan = columnas;
                cuerpo.append(fila);
            }
            indicador.textContent = 'Página ' + pagina + ' · ' + resultado.total + ' registros';
            anterior.disabled = pagina === 1;
            siguiente.disabled = !resultado.hay_mas;
            mensajeAdmin(mensaje);
        } catch (error) {
            if (error.name === 'AbortError') return;
            filas = [];
            cuerpo.replaceChildren();
            const fila = document.createElement('tr');
            celdaAdmin(fila, 'No se pudo cargar el listado. Usa Actualizar para reintentar.').colSpan = columnas;
            cuerpo.append(fila);
            indicador.textContent = 'Sin datos';
            mensajeAdmin(mensaje, error.message, 'error');
        } finally {
            if (solicitud === actual) { actualizar.disabled = false; cuerpo.removeAttribute('aria-busy'); }
        }
    }
    formulario.addEventListener('submit', evento => { evento.preventDefault(); pagina = 1; cargar(); });
    formulario.querySelector('select')?.addEventListener('change', () => { pagina = 1; cargar(); });
    actualizar.addEventListener('click', cargar);
    anterior.addEventListener('click', () => { if (pagina > 1) { pagina--; cargar(); } });
    siguiente.addEventListener('click', () => { pagina++; cargar(); });
    document.addEventListener('admin:seccion', ({ detail }) => { if (detail === modulo) cargar(); });
    cargar();
    return { cargar, cuerpo, mensaje, buscar: id => filas.find(fila => String(fila.id_bitacora ?? fila.id_solicitud) === id) };
}

// TITULO 4 PROTEGER LOS DIALOGOS DURANTE LAS OPERACIONES
export function prepararDialogoAdmin(id) {
    const dialogo = document.getElementById(id);
    let ocupado = false;
    let estados = [];
    dialogo.querySelectorAll('[data-cerrar-dialogo]').forEach(boton => boton.addEventListener('click', () => { if (!ocupado) dialogo.close(); }));
    dialogo.addEventListener('cancel', evento => { if (ocupado) evento.preventDefault(); });
    return {
        dialogo,
        get ocupado() { return ocupado; },
        bloquear(valor) {
            const controles = [...dialogo.querySelectorAll('input, textarea, select, button')];
            if (valor) { estados = controles.map(elemento => elemento.disabled); controles.forEach(elemento => { elemento.disabled = true; }); }
            else controles.forEach((elemento, i) => { elemento.disabled = estados[i]; });
            ocupado = valor;
        }
    };
}
export function notificarAdmin(modulo) {
    document.dispatchEvent(new CustomEvent('admin:actualizado', { detail: modulo }));
}
