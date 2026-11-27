// TITULO 1 IMPORTAR LAS FUNCIONES COMPARTIDAS DEL ADMINISTRADOR
import { crearListadoAdmin, celdaAdmin, fechaAdmin, botonAdmin, pedirAdmin, mensajeAdmin, prepararDialogoAdmin, notificarAdmin } from '../autorizaciones/backend_autorizaciones/api_admin.js';

// TITULO 2 MOSTRAR Y EDITAR LAS NOVEDADES DEL CONDOMINIO
export function iniciarBitacoraAdmin() {
    const modal = prepararDialogoAdmin('dialogo_bitacora_admin');
    const formulario = document.getElementById('formulario_bitacora_admin');
    const mensaje = document.getElementById('mensaje_edicion_bitacora_admin');
    const listado = crearListadoAdmin('bitacora', 'listar_bitacora_admin.php', 5, (fila, dato) => {
        celdaAdmin(fila, '#' + dato.id_bitacora, fechaAdmin(dato.fecha_novedad) + ' · ' + dato.hora_novedad);
        celdaAdmin(fila, dato.titulo, dato.descripcion);
        celdaAdmin(fila, dato.categoria, [dato.tipo_lugar, dato.lugar].filter(Boolean).join(' · '));
        celdaAdmin(fila, dato.nombre_conserje);
        celdaAdmin(fila, '').append(botonAdmin('Ver / editar', 'editar', dato.id_bitacora, dato.titulo));
    });

    // TITULO 3 CARGAR LA VERSION QUE SE DESEA REVISAR
    listado.cuerpo.addEventListener('click', evento => {
        const boton = evento.target.closest('button[data-accion="editar"]');
        if (!boton || modal.ocupado) return;
        const dato = listado.buscar(boton.dataset.id);
        if (!dato) return;
        formulario.reset();
        for (const elemento of formulario.elements) {
            if (elemento.name && Object.hasOwn(dato, elemento.name)) elemento.value = dato[elemento.name] ?? '';
        }
        document.getElementById('titulo_edicion_bitacora_admin').textContent = 'Editar novedad #' + dato.id_bitacora;
        mensajeAdmin(mensaje);
        modal.dialogo.showModal();
    });

    // TITULO 4 GUARDAR SIN SOBRESCRIBIR CAMBIOS AJENOS
    formulario.addEventListener('submit', async evento => {
        evento.preventDefault();
        if (modal.ocupado || !formulario.reportValidity()) return;
        const datos = new FormData(formulario);
        modal.bloquear(true);
        mensajeAdmin(mensaje, 'Guardando cambios...');
        try {
            const resultado = await pedirAdmin('bitacora', 'modificar_bitacora_admin.php', { datos });
            modal.dialogo.close();
            await listado.cargar();
            mensajeAdmin(listado.mensaje, resultado.mensaje, 'exito');
            notificarAdmin('bitacora');
        } catch (error) {
            mensajeAdmin(mensaje, error.message, 'error');
        } finally { modal.bloquear(false); }
    });
}
