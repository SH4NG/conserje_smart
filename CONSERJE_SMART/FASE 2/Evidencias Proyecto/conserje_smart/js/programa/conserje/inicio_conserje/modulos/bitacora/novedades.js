// TITULO 1 INICIALIZAR LISTADO DE NOVEDADES
(() => {
    const contenedor = document.getElementById('novedades');
    const estado = document.getElementById('estado_lista_novedades');

    if (!contenedor || !estado) {
        console.error('No se pudo iniciar el listado: faltan elementos de novedades.php.');
        return;
    }

    // TITULO 2 CREAR CAMPO DE DETALLE
    const crearCampo = (etiqueta, valor) => {
        const campo = document.createElement('div');
        campo.className = 'detalle_tarjeta_novedad';

        const etiquetaElemento = document.createElement('span');
        etiquetaElemento.className = 'etiqueta_detalle_tarjeta';
        etiquetaElemento.textContent = etiqueta;

        const valorElemento = document.createElement('span');
        valorElemento.className = 'valor_detalle_tarjeta';
        valorElemento.textContent = valor;

        campo.append(etiquetaElemento, valorElemento);
        return campo;
    };

    // TITULO 3 CREAR TARJETA DE NOVEDAD
    const crearTarjeta = (datos) => {
        const tarjeta = document.createElement('article');
        tarjeta.className = 'tarjeta_novedad';
        tarjeta.dataset.idBitacora = String(datos.id_bitacora);

        const encabezado = document.createElement('div');
        encabezado.className = 'encabezado_tarjeta_novedad';

        const contenido = document.createElement('div');
        contenido.className = 'contenido_principal_tarjeta';

        const titulo = document.createElement('h2');
        titulo.className = 'titulo_tarjeta_novedad';
        titulo.textContent = datos.titulo;

        const descripcion = document.createElement('div');
        descripcion.className = 'descripcion_tarjeta_novedad';
        descripcion.textContent = datos.descripcion;
        contenido.append(titulo, descripcion);

        const autor = document.createElement('div');
        autor.className = 'autor_tarjeta_novedad';

        const icono = document.createElement('img');
        icono.className = 'icono_autor_tarjeta_novedad';
        icono.src = '/conserje_smart/imagenes/programa/conserje/inicio_conserje/modulos/bitacora/icono_conserje.png';
        icono.alt = '';
        icono.setAttribute('aria-hidden', 'true');

        const nombreAutor = document.createElement('span');
        nombreAutor.textContent = datos.nombre_conserje;

        const fechaHora = document.createElement('div');
        fechaHora.className = 'fecha_hora_tarjeta_novedad';

        const fecha = document.createElement('time');
        fecha.dateTime = datos.fecha_novedad;
        fecha.textContent = formatearFecha(datos.fecha_novedad);

        const hora = document.createElement('time');
        hora.textContent = formatearHora(datos.hora_novedad);
        fechaHora.append(fecha, hora);
        autor.append(icono, nombreAutor, fechaHora);
        encabezado.append(contenido, autor);

        const detalles = document.createElement('div');
        detalles.className = 'detalles_tarjeta_novedad';

        [
            ['Categoría', datos.categoria],
            ['Tipo de lugar', datos.tipo_lugar],
            ['Lugar', datos.lugar],
            ['Fecha del evento', datos.fecha_evento],
            ['Hora del evento', datos.hora_evento],
            ['Entidades relacionadas', formatearLista(datos.entidad_relacionada)],
            ['Etiquetas', formatearLista(datos.etiquetas)],
            ['Campos faltantes', formatearLista(datos.campos_faltantes)],
            ['Observaciones', datos.observaciones]
        ].forEach(([etiqueta, valor]) => {
            detalles.appendChild(crearCampo(etiqueta, valor ?? 'No especificado'));
        });

        tarjeta.append(encabezado, detalles);
        return tarjeta;
    };

    // TITULO 4 FORMATEAR DATOS DE NOVEDAD
    const formatearLista = (valores) => (
        Array.isArray(valores) && valores.length > 0 ? valores.join(', ') : 'Ninguno'
    );

    const formatearFecha = (fecha) => {
        if (typeof fecha !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
            return 'No especificada';
        }

        const [anio, mes, dia] = fecha.split('-');
        return `${dia}/${mes}/${anio}`;
    };

    const formatearHora = (hora) => (
        typeof hora === 'string' && /^\d{2}:\d{2}/.test(hora)
            ? hora.slice(0, 5)
            : 'No especificada'
    );

    // TITULO 5 CARGAR NOVEDADES DEL CONDOMINIO
    const cargarNovedades = async () => {
        estado.textContent = 'Cargando novedades...';

        try {
            const respuesta = await fetch(
                '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/bitacora/backend_bitacora/listar_bitacora.php',
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

            contenedor.replaceChildren();

            if (resultado.datos.length === 0) {
                const vacio = document.createElement('p');
                vacio.className = 'lista_novedades_vacia';
                vacio.textContent = 'Este condominio aún no tiene novedades guardadas.';
                contenedor.appendChild(vacio);
                estado.textContent = '';
                return;
            }

            resultado.datos.forEach((novedad) => {
                contenedor.appendChild(crearTarjeta(novedad));
            });
            estado.textContent = `Se cargaron ${resultado.datos.length} novedades del condominio.`;
        } catch (error) {
            estado.textContent = error.message || 'No se pudieron cargar las novedades.';
            console.error('Error al cargar las novedades del condominio:', error);
        }
    };

    cargarNovedades();
})();