// TITULO 1 IMPORTAR LAS FUNCIONES DE AUTORIZACION
import { crearListadoAdmin, celdaAdmin, fechaAdmin, estadoAdmin, botonAdmin, prepararDialogoAdmin, pedirAdmin, mensajeAdmin, notificarAdmin } from './backend_autorizaciones/api_admin.js';

// TITULO 2 MOSTRAR EL RUT CON PUNTOS Y GUION
function formatoRutAutorizacionesAdmin(valor) {
    const original = String(valor || '');
    const limpio = original.replace(/[.\s-]/gu, '').toUpperCase();
    if (!/^[0-9]{1,8}[0-9K]$/.test(limpio)) return original;
    return limpio.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + limpio.slice(-1);
}

// TITULO 3 CONSULTAR LAS SOLICITUDES Y SU HISTORIAL
export function iniciarAutorizacionesAdmin() {
    iniciarInvitacionesYConserjesAdmin();
    const modal = prepararDialogoAdmin('dialogo_autorizaciones_admin');
    const formulario = document.getElementById('formulario_autorizaciones_admin');
    const mensaje = document.getElementById('mensaje_decision_admin');
    const confirmacion = document.getElementById('confirmacion_aprobacion_admin');
    const rutAdministrador = document.getElementById('rut_admin_decision');
    const codigoAdministrador = document.getElementById('codigo_auth_decision_admin');
    const confirmar = document.getElementById('confirmar_decision_admin');
    let consultaIdentidad;
    let identidadDisponible = false;
    const listado = crearListadoAdmin('autorizaciones', 'listar_solicitudes_admin.php', 5, (fila, dato) => {
        const registro = celdaAdmin(fila, '');
        estadoAdmin(registro, dato.estado[0].toUpperCase() + dato.estado.slice(1), dato.estado + '_admin');
        const numero = document.createElement('small');
        numero.textContent = '#' + dato.id_solicitud;
        registro.append(numero);
        const nombre = dato.nombre_usuario + ' ' + dato.apellido_usuario;
        celdaAdmin(fila, nombre, formatoRutAutorizacionesAdmin(dato.rut_usuario));
        celdaAdmin(fila, dato.correo, dato.telefono_usuario);
        celdaAdmin(fila, fechaAdmin(dato.fecha_solicitud), dato.fecha_resolucion ?
            fechaAdmin(dato.fecha_resolucion) + ' · ' + (dato.nombre_resolutor || 'Administrador') : 'Esperando revisión');
        const acciones = celdaAdmin(fila, '');
        if (dato.estado === 'pendiente') {
            acciones.append(botonAdmin('Aprobar', 'aprobar', dato.id_solicitud, nombre), botonAdmin('Rechazar', 'rechazar', dato.id_solicitud, nombre));
        } else acciones.textContent = dato.motivo_resolucion || 'Solicitud resuelta';
    });

    // TITULO 3 PEDIR UNA CONFIRMACION CON LA IDENTIDAD DE LA SOLICITUD
    listado.cuerpo.addEventListener('click', async evento => {
        const boton = evento.target.closest('button[data-accion]');
        if (!boton || modal.ocupado) return;
        const dato = listado.buscar(boton.dataset.id);
        if (!dato || dato.estado !== 'pendiente') return;
        const aprobar = boton.dataset.accion === 'aprobar';
        consultaIdentidad?.abort();
        identidadDisponible = false;
        formulario.reset();
        formulario.removeAttribute('aria-busy');
        codigoAdministrador.removeAttribute('aria-invalid');
        const consulta = new AbortController();
        consultaIdentidad = consulta;
        confirmacion.hidden = !aprobar;
        codigoAdministrador.required = aprobar;
        codigoAdministrador.disabled = true;
        codigoAdministrador.value = '';
        rutAdministrador.value = '';
        confirmar.disabled = aprobar;
        formulario.elements.id_solicitud.value = dato.id_solicitud;
        formulario.elements.accion.value = boton.dataset.accion;
        formulario.elements.motivo.required = !aprobar;
        document.getElementById('titulo_decision_admin').textContent = aprobar ? 'Aprobar cuenta de conserje' : 'Rechazar solicitud';
        document.getElementById('identidad_solicitud_admin').textContent = dato.nombre_usuario + ' ' + dato.apellido_usuario + ' · ' + formatoRutAutorizacionesAdmin(dato.rut_usuario) + ' · Condominio: ' + (dato.nombre_condominio || document.getElementById('nombre_condominio_admin').textContent);
        document.getElementById('ayuda_decision_admin').textContent = aprobar ?
            'Revisa los datos y confirma con tu código personal para habilitar el acceso de esta persona a tu condominio.' : 'La cuenta permanecerá sin acceso. Indica el motivo de la decisión.';
        document.getElementById('etiqueta_motivo_admin').textContent = aprobar ? 'Observaciones (opcional)' : 'Motivo del rechazo';
        document.getElementById('confirmar_decision_admin').textContent = aprobar ? 'Sí, aprobar cuenta' : 'Confirmar rechazo';
        mensajeAdmin(mensaje);
        modal.dialogo.showModal();
        if (!aprobar) return;

        // TITULO 4 CONSULTAR EL RUT DESDE LA CUENTA DEL ADMINISTRADOR
        document.getElementById('nombre_admin_decision').textContent = 'Consultando tu cuenta...';
        formulario.setAttribute('aria-busy', 'true');
        mensajeAdmin(mensaje, 'Comprobando la identidad de tu sesión...');
        try {
            const resultado = await pedirAdmin('autorizaciones', 'resumen_admin.php', { signal: consulta.signal });
            if (consulta.signal.aborted || !modal.dialogo.open) return;
            const identidad = resultado.datos.administrador;
            if (!identidad || typeof identidad.rut !== 'string' || !identidad.rut.trim()) {
                throw new Error('No se pudo identificar el RUT de tu cuenta. Cierra la ventana y vuelve a abrir la solicitud.');
            }
            rutAdministrador.value = formatoRutAutorizacionesAdmin(identidad.rut);
            document.getElementById('nombre_admin_decision').textContent = identidad.nombre || 'Administrador de la sesión';
            identidadDisponible = true;
            codigoAdministrador.disabled = false;
            confirmar.disabled = false;
            mensajeAdmin(mensaje);
            codigoAdministrador.focus();
        } catch (error) {
            if (error.name !== 'AbortError') {
                document.getElementById('nombre_admin_decision').textContent = 'Identidad sin confirmar';
                mensajeAdmin(mensaje, error.message, 'error');
            }
        } finally {
            if (consultaIdentidad === consulta) formulario.removeAttribute('aria-busy');
        }
    });

    // TITULO 5 RETIRAR EL CODIGO CUANDO SE CIERRA LA CONFIRMACION
    modal.dialogo.addEventListener('close', () => {
        consultaIdentidad?.abort();
        codigoAdministrador.value = '';
        codigoAdministrador.removeAttribute('aria-invalid');
        identidadDisponible = false;
    });

    codigoAdministrador.addEventListener('input', () => {
        if (codigoAdministrador.hasAttribute('aria-invalid')) {
            codigoAdministrador.removeAttribute('aria-invalid');
            mensajeAdmin(mensaje);
        }
    });

    // TITULO 6 RESOLVER LA SOLICITUD CON LA AUTORIZACION DEL ADMINISTRADOR
    formulario.addEventListener('submit', async evento => {
        evento.preventDefault();
        if (modal.ocupado) return;
        if (formulario.elements.accion.value === 'aprobar' && !identidadDisponible) {
            mensajeAdmin(mensaje, 'Espera a que se confirme la identidad de tu sesión. Si falló la consulta, vuelve a abrir la solicitud.', 'error');
            return;
        }
        if (!formulario.reportValidity()) return;
        const datos = new FormData(formulario);
        codigoAdministrador.removeAttribute('aria-invalid');
        let revisarCodigo = false;
        modal.bloquear(true);
        mensajeAdmin(mensaje, 'Guardando decisión...');
        try {
            const resultado = await pedirAdmin('autorizaciones', 'resolver_solicitud_admin.php', { datos });
            modal.dialogo.close();
            await listado.cargar();
            mensajeAdmin(listado.mensaje, resultado.mensaje, 'exito');
            notificarAdmin('autorizaciones');
        } catch (error) {
            mensajeAdmin(mensaje, error.message, 'error');
            revisarCodigo = formulario.elements.accion.value === 'aprobar' && [400, 403].includes(error.codigoHttp);
            if (revisarCodigo) codigoAdministrador.setAttribute('aria-invalid', 'true');
        } finally {
            codigoAdministrador.value = '';
            datos.delete('codigo_auth_admin');
            modal.bloquear(false);
            if (revisarCodigo && modal.dialogo.open) codigoAdministrador.focus();
        }
    });
}

// TITULO 7 GESTIONAR LAS INVITACIONES SIN EXPONER TOKENS ANTERIORES
function iniciarInvitacionesYConserjesAdmin() {
    const formulario = document.getElementById('formulario_invitar_conserje_admin');
    const mensaje = document.getElementById('mensaje_crear_invitacion_admin');
    const salida = document.getElementById('enlace_invitacion_admin');
    const enlace = document.getElementById('url_invitacion_admin');
    let creando = false;
    const fechaUtc = valor => {
        if (!valor) return '—';
        const fecha = new Date(valor.replace(' ', 'T') + 'Z');
        return Number.isNaN(fecha.getTime()) ? '—' : new Intl.DateTimeFormat('es-CL', {
            timeZone: 'America/Santiago', dateStyle: 'short', timeStyle: 'short'
        }).format(fecha);
    };
    const invitaciones = crearListadoAdmin('invitaciones', 'listar_invitaciones_admin.php', 4, (fila, dato) => {
        const id = celdaAdmin(fila, '');
        estadoAdmin(id, '#' + dato.id_invitacion + ' · ' + dato.estado, dato.estado === 'activa' ? 'aprobada_admin' : 'neutral_admin');
        celdaAdmin(fila, fechaUtc(dato.creado_en), 'Vence: ' + fechaUtc(dato.vence_en));
        celdaAdmin(fila, dato.creado_por, dato.usado_por ? 'Utilizada por ' + dato.usado_por : '');
        const acciones = celdaAdmin(fila, '');
        if (dato.estado === 'activa') acciones.append(botonAdmin('Revocar', 'revocar', dato.id_invitacion, 'Invitación #' + dato.id_invitacion));
    }, 'autorizaciones');
    const cuentas = crearListadoAdmin('conserjes', 'listar_conserjes_admin.php', 4, (fila, dato) => {
        const id = celdaAdmin(fila, '');
        estadoAdmin(id, '#' + dato.id_usuario + ' · ' + (dato.estado === 'existente' ? 'Cuenta anterior' : dato.estado), dato.estado + '_admin');
        celdaAdmin(fila, dato.nombre_usuario + ' ' + dato.apellido_usuario, formatoRutAutorizacionesAdmin(dato.rut_usuario));
        celdaAdmin(fila, dato.correo);
        celdaAdmin(fila, dato.fecha_solicitud ? fechaAdmin(dato.fecha_solicitud) : 'Cuenta anterior a las solicitudes',
            dato.fecha_resolucion ? 'Resolución: ' + fechaAdmin(dato.fecha_resolucion) : '');
    }, 'autorizaciones');

    // TITULO 8 GENERAR Y COPIAR EL ENLACE QUE FIJA EL CONDOMINIO
    formulario.addEventListener('submit', async evento => {
        evento.preventDefault();
        if (creando || !formulario.reportValidity()) return;
        const datos = new FormData(formulario), boton = formulario.querySelector('button[type="submit"]');
        creando = true; boton.disabled = true; salida.hidden = true; enlace.value = '';
        formulario.setAttribute('aria-busy', 'true');
        boton.textContent = 'Creando invitación...';
        document.getElementById('copiar_invitacion_admin').textContent = 'Copiar enlace';
        document.getElementById('copiar_invitacion_admin').removeAttribute('data-copiado');
        mensajeAdmin(mensaje, 'Creando invitación...');
        try {
            const resultado = await pedirAdmin('autorizaciones', 'crear_invitacion_conserje_admin.php', { datos });
            enlace.value = resultado.datos.url; salida.hidden = false;
            mensajeAdmin(mensaje, resultado.mensaje, 'exito');
            await invitaciones.cargar();
        } catch (error) { mensajeAdmin(mensaje, error.message, 'error'); }
        finally {
            creando = false; boton.disabled = false;
            formulario.removeAttribute('aria-busy'); boton.textContent = 'Crear invitación';
        }
    });
    document.getElementById('copiar_invitacion_admin').addEventListener('click', async () => {
        if (!enlace.value) return;
        try {
            await navigator.clipboard.writeText(enlace.value);
            const boton = document.getElementById('copiar_invitacion_admin');
            boton.textContent = 'Enlace copiado'; boton.dataset.copiado = 'true';
            mensajeAdmin(mensaje, 'Enlace copiado. Compártelo con la persona que solicitará la cuenta.', 'exito');
        } catch {
            enlace.focus(); enlace.select();
            mensajeAdmin(mensaje, 'Selecciona el enlace y cópialo para compartirlo.');
        }
    });

    // TITULO 9 REVOCAR LA INVITACION CON CONFIRMACION DEL ADMINISTRADOR
    invitaciones.cuerpo.addEventListener('click', async evento => {
        const boton = evento.target.closest('button[data-accion="revocar"]');
        if (!boton || !confirm('¿Revocar esta invitación? El enlace dejará de permitir nuevas solicitudes.')) return;
        boton.disabled = true;
        try {
            const datos = new FormData(); datos.set('id_invitacion', boton.dataset.id);
            const resultado = await pedirAdmin('autorizaciones', 'revocar_invitacion_admin.php', { datos });
            await invitaciones.cargar(); mensajeAdmin(invitaciones.mensaje, resultado.mensaje, 'exito');
        } catch (error) { mensajeAdmin(invitaciones.mensaje, error.message, 'error'); boton.disabled = false; }
    });
    document.addEventListener('admin:seccion', ({ detail }) => {
        if (detail === 'autorizaciones') { invitaciones.cargar(); cuentas.cargar(); }
    });
    document.addEventListener('admin:actualizado', ({ detail }) => { if (detail === 'autorizaciones') cuentas.cargar(); });
}
