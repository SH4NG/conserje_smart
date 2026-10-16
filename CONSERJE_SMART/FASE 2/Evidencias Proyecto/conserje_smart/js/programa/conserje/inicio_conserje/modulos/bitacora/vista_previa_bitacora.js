// TITULO 1 INICIALIZAR ELEMENTOS DE VISTA PREVIA
// actualiza fecha y turno, y prepara la lista de novedades analizadas
(() => {
    const previewContainer = document.getElementById('contenedor_vista_previa_bitacora');
    const dateElement = document.getElementById('fecha_vista_previa_bitacora');
    const shiftNameElement = document.getElementById('nombre_turno_vista_previa_bitacora');
    const shiftHoursElement = document.getElementById('horario_turno_vista_previa_bitacora');
    const timeZone = previewContainer?.dataset.timeZone;
    const conserjeName = previewContainer?.dataset.nombreConserje || 'Conserje';
    const novedadesContainer = document.getElementById('contenido_vista_previa');
    const estadoCarga = document.createElement('p');
    estadoCarga.className = 'estado_carga_bitacora';
    estadoCarga.textContent = 'Cargando novedades guardadas...';

    if (!dateElement || !shiftNameElement || !shiftHoursElement || !timeZone || !novedadesContainer) {
        console.error('No se pudo actualizar la fecha y el turno: faltan elementos de la vista previa de bitácora.');
        return;
    }

    // TITULO 2 CONFIGURAR FORMATEADORES LOCALES
    const dateFormatter = new Intl.DateTimeFormat('es-CL', { 
        timeZone,
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    const dateTimeFormatter = new Intl.DateTimeFormat('en-CA', {
        timeZone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
    const timeFormatter = new Intl.DateTimeFormat('en-GB', {
        timeZone,
        hour: '2-digit',
        minute: '2-digit',
        hourCycle: 'h23'
    });

    // TITULO 3 ACTUALIZAR FECHA Y TURNO
    const updatePreview = () => {
        const now = new Date();
        const dateParts = Object.fromEntries(
            dateTimeFormatter.formatToParts(now).map(({ type, value }) => [type, value])
        );
        const timeParts = Object.fromEntries(
            timeFormatter.formatToParts(now).map(({ type, value }) => [type, value])
        );
        const minutesInChile = Number(timeParts.hour) * 60 + Number(timeParts.minute);

        dateElement.textContent = dateFormatter.format(now);
        dateElement.dateTime = `${dateParts.year}-${dateParts.month}-${dateParts.day}`;

        if (minutesInChile >= 8 * 60 && minutesInChile < 16 * 60) {
            shiftNameElement.textContent = 'Turno Mañana';
            shiftHoursElement.textContent = '08:00 - 16:00';
        } else if (minutesInChile >= 16 * 60) {
            shiftNameElement.textContent = 'Turno Tarde';
            shiftHoursElement.textContent = '16:00 - 00:00';
        } else {
            shiftNameElement.textContent = 'Turno Noche';
            shiftHoursElement.textContent = '00:00 - 08:00';
        }
    };

    // TITULO 4 CREAR CONTENIDO SEGURO PARA UNA NOVEDAD
    const crearCampo = (etiqueta, valor) => {
        const campo = document.createElement('div');
        campo.className = 'detalle_novedad';

        const nombre = document.createElement('span');
        nombre.className = 'etiqueta_detalle_novedad';
        nombre.textContent = etiqueta;

        const contenido = document.createElement('span');
        contenido.className = 'valor_detalle_novedad';
        contenido.textContent = valor;

        campo.append(nombre, contenido);
        return campo;
    };

    // TITULO 5 AGREGAR TARJETA DE NOVEDAD
    const agregarNovedad = (datos) => {
        if (!datos || typeof datos !== 'object') {
            console.error('No se puede mostrar la novedad: el resultado recibido no es válido.');
            return;
        }

        const tarjeta = document.createElement('div');
        tarjeta.className = 'novedad';
        const idGuardado = Number(datos.id_bitacora);
        const esGuardada = Number.isSafeInteger(idGuardado) && idGuardado > 0;
        if (esGuardada && novedadesContainer.querySelector(`[data-id-bitacora="${idGuardado}"]`)) {
            return;
        }

        tarjeta.id = esGuardada
            ? `novedad-bitacora-${idGuardado}`
            : `novedad-previa-${novedadesContainer.querySelectorAll('.novedad').length + 1}`;
        tarjeta.dataset.fechaNovedad = typeof datos.fecha_novedad === 'string' ? datos.fecha_novedad : '';
        tarjeta.dataset.horaNovedad = typeof datos.hora_novedad === 'string' ? datos.hora_novedad : '';
        if (esGuardada) {
            tarjeta.dataset.idBitacora = String(idGuardado);
        }

        const encabezado = document.createElement('div');
        encabezado.className = 'encabezado_novedad';

        const contenidoPrincipal = document.createElement('div');
        contenidoPrincipal.className = 'contenido_principal_novedad';

        const titulo = document.createElement('div');
        titulo.className = 'titulo_novedad';
        titulo.textContent = typeof datos.titulo === 'string' ? datos.titulo : 'Novedad sin título';

        const descripcion = document.createElement('div');
        descripcion.className = 'descripcion_novedad';
        descripcion.textContent = typeof datos.descripcion === 'string' ? datos.descripcion : '';

        contenidoPrincipal.append(titulo, descripcion);

        const autor = document.createElement('div');
        autor.className = 'autor_novedad';
        const nombreConserje = typeof datos.nombre_conserje === 'string'
            ? datos.nombre_conserje
            : conserjeName;
        autor.setAttribute('aria-label', `Registrada por ${nombreConserje}`);

        const identidadConserje = document.createElement('div');
        identidadConserje.className = 'identidad_conserje_novedad';

        const icono = document.createElement('img');
        icono.setAttribute('class', 'icono_conserje_novedad');
        icono.src = '/conserje_smart/imagenes/programa/conserje/inicio_conserje/modulos/bitacora/icono_conserje.png';
        icono.alt = '';
        icono.setAttribute('aria-hidden', 'true');

        const nombreAutor = document.createElement('span');
        nombreAutor.className = 'nombre_conserje_novedad';
        nombreAutor.textContent = nombreConserje;
        identidadConserje.append(icono, nombreAutor);

        const fechaHora = document.createElement('div');
        fechaHora.className = 'fecha_hora_novedad';

        const fechaNovedad = document.createElement('div');
        fechaNovedad.className = 'fecha_novedad';
        fechaNovedad.textContent = formatearFechaNovedad(datos.fecha_novedad);

        const horaNovedad = document.createElement('div');
        horaNovedad.className = 'hora_novedad';
        horaNovedad.textContent = formatearHoraNovedad(datos.hora_novedad);

        fechaHora.append(fechaNovedad, horaNovedad);
        autor.append(identidadConserje, fechaHora);
        encabezado.append(contenidoPrincipal, autor);

        const detalles = document.createElement('div');
        detalles.className = 'detalles_novedad';

        const camposTexto = [
            ['Categoría', datos.categoria],
            ['Tipo de lugar', datos.tipo_lugar],
            ['Lugar', datos.lugar],
            ['Fecha', datos.fecha_evento],
            ['Hora', datos.hora_evento],
            ['Observaciones', datos.observaciones]
        ];

        camposTexto.forEach(([etiqueta, valor]) => {
            detalles.appendChild(crearCampo(etiqueta, valor ?? 'No especificado'));
        });

        const camposLista = [
            ['Entidades relacionadas', datos.entidad_relacionada],
            ['Etiquetas', datos.etiquetas],
            ['Campos faltantes', datos.campos_faltantes]
        ];

        camposLista.forEach(([etiqueta, valores]) => {
            const texto = Array.isArray(valores) && valores.length > 0
                ? valores.join(', ')
                : 'Ninguno';
            detalles.appendChild(crearCampo(etiqueta, texto));
        });

        const acciones = document.createElement('div');
        acciones.className = 'acciones_novedad';

        const estadoGuardado = document.createElement('span');
        estadoGuardado.className = 'estado_guardado_novedad';
        estadoGuardado.setAttribute('role', 'status');
        estadoGuardado.setAttribute('aria-live', 'polite');

        const botonGuardar = document.createElement('button');
        botonGuardar.className = 'boton_guardar_novedad';
        botonGuardar.type = 'button';
        botonGuardar.textContent = esGuardada ? 'Guardada' : 'Guardar';
        botonGuardar.disabled = esGuardada;
        if (esGuardada) {
            estadoGuardado.textContent = 'Novedad guardada en bitácora.';
        } else {
            botonGuardar.addEventListener('click', () => guardarNovedad(tarjeta, datos, botonGuardar, estadoGuardado));
        }

        acciones.append(estadoGuardado, botonGuardar);
        tarjeta.append(encabezado, detalles, acciones);
        novedadesContainer.appendChild(tarjeta);
    };

    // TITULO 6 GUARDAR NOVEDAD EN LA BASE DE DATOS
    const guardarNovedad = async (tarjeta, datos, boton, estado) => {
        if (boton.disabled || tarjeta.dataset.idBitacora) {
            return;
        }

        boton.disabled = true;
        boton.textContent = 'Guardando...';
        estado.textContent = '';

        try {
            const respuesta = await fetch(
                '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/bitacora/backend_bitacora/guardar_novedad.php',
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(datos)
                }
            );
            const resultado = await respuesta.json();

            if (!respuesta.ok || resultado.exito !== true || !resultado.datos?.id_bitacora) {
                throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status}`);
            }

            tarjeta.dataset.idBitacora = String(resultado.datos.id_bitacora);
            boton.textContent = 'Guardada';
            estado.textContent = 'Novedad guardada correctamente.';
        } catch (error) {
            boton.disabled = false;
            boton.textContent = 'Guardar';
            estado.textContent = error.message || 'No se pudo guardar la novedad.';
            console.error('Error al guardar la novedad:', error);
        }
    };

    // TITULO 7 CARGAR NOVEDADES GUARDADAS
    const cargarNovedadesGuardadas = async () => {
        novedadesContainer.appendChild(estadoCarga);

        try {
            const respuesta = await fetch(
                '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/bitacora/backend_bitacora/listar_bitacora_conserje.php',
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                }
            );
            const resultado = await respuesta.json();

            if (!respuesta.ok || resultado.exito !== true || !Array.isArray(resultado.datos)) {
                throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status}`);
            }

            resultado.datos.forEach(agregarNovedad);
            estadoCarga.remove();
        } catch (error) {
            estadoCarga.textContent = error.message || 'No se pudieron recuperar las novedades guardadas.';
            console.error('Error al recuperar las novedades guardadas:', error);
        }
    };

    // TITULO 8 FORMATEAR FECHA Y HORA DE CREACION
    const formatearFechaNovedad = (fecha) => {
        if (typeof fecha !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
            return 'Fecha no disponible';
        }

        const [anio, mes, dia] = fecha.split('-');
        return `${dia}/${mes}/${anio.slice(-2)}`;
    };

    const formatearHoraNovedad = (hora) => {
        if (typeof hora !== 'string' || !/^\d{2}:\d{2}/.test(hora)) {
            return 'Hora no disponible';
        }

        return hora.slice(0, 5);
    };

    // TITULO 9 ESCUCHAR RESULTADOS DEL ANALISIS
    window.addEventListener('bitacora:novedad-analizada', (evento) => {
        agregarNovedad(evento.detail);
    });

    // TITULO 10 INICIAR ACTUALIZACION PERIODICA
    updatePreview();
    window.setInterval(updatePreview, 30_000);
    cargarNovedadesGuardadas();
})();