document.addEventListener('DOMContentLoaded', () => {
    const selectDepartamento = document.getElementById('select_departamento_visita');
    const resumenDepartamento = document.getElementById('resumen_departamento_visita');
    const mensajeFormulario = document.getElementById('mensaje_formulario_visita');
    const formulario = document.getElementById('formulario_visita');
    const botonRegistrar = document.getElementById('boton_registrar_visita');

    const mostrarMensaje = (mensaje, tipo) => {
        if (!mensajeFormulario) {
            return;
        }

        mensajeFormulario.textContent = mensaje;
        mensajeFormulario.classList.remove('error', 'exito');
        if (tipo) {
            mensajeFormulario.classList.add(tipo);
        }
    };

    const cargarDepartamentos = async () => {
        if (!selectDepartamento) {
            return;
        }

        try {
            const respuesta = await fetch(
                '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/visita/backend_visita/listar_departamento_visita.php',
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                }
            );

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.exito || !Array.isArray(resultado.datos)) {
                throw new Error(resultado.mensaje || 'No se pudieron cargar los departamentos.');
            }

            selectDepartamento.innerHTML = '<option value="">Seleccione un departamento</option>';

            if (resultado.datos.length === 0) {
                selectDepartamento.innerHTML = '<option value="">No hay departamentos registrados</option>';
                mostrarMensaje('Este condominio aún no tiene departamentos. Agrega registros a la tabla departamento para probar el formulario.', 'error');
                return;
            }

            resultado.datos.forEach((departamento) => {
                const etiqueta = [
                    departamento.torre_departamento ? `Torre ${departamento.torre_departamento}` : '',
                    `Depto. ${departamento.numero_departamento}`,
                    departamento.piso_departamento !== null ? `Piso ${departamento.piso_departamento}` : ''
                ].filter(Boolean).join(' · ');

                const option = document.createElement('option');
                option.value = String(departamento.id_departamento);
                option.textContent = etiqueta;
                selectDepartamento.appendChild(option);
            });
            mostrarMensaje('', null);
        } catch (error) {
            selectDepartamento.innerHTML = '<option value="">No disponible</option>';
            mostrarMensaje(error.message || 'Error al cargar los departamentos.', 'error');
            console.error('Error al cargar departamentos:', error);
        }
    };

    if (selectDepartamento) {
        selectDepartamento.addEventListener('change', () => {
            const opcionSeleccionada = selectDepartamento.options[selectDepartamento.selectedIndex];
            const texto = opcionSeleccionada && opcionSeleccionada.value ? opcionSeleccionada.textContent : 'Sin selección';

            if (resumenDepartamento) {
                resumenDepartamento.textContent = texto;
            }

            mostrarMensaje('', null);
        });
    }

    if (formulario) {
        formulario.addEventListener('reset', () => {
            window.setTimeout(() => {
                if (resumenDepartamento) {
                    resumenDepartamento.textContent = 'Sin selección';
                }
                mostrarMensaje('', null);
            });
        });

        formulario.addEventListener('submit', async (evento) => {
            evento.preventDefault();

            const datosFormulario = new FormData(formulario);
            const departamento = String(datosFormulario.get('id_departamento') || '');
            const nombre = String(datosFormulario.get('nombre_visitante') || '').trim();
            const rut = String(datosFormulario.get('rut_visitante') || '').trim();
            const motivo = String(datosFormulario.get('motivo_visita') || '').trim();

            if (!departamento || !nombre || !rut || !motivo) {
                mostrarMensaje('Completa departamento, nombre, RUT y motivo antes de registrar.', 'error');
                return;
            }

            if (botonRegistrar) {
                botonRegistrar.disabled = true;
                botonRegistrar.textContent = 'Registrando...';
            }

            mostrarMensaje('Guardando el ingreso de visita...', null);

            try {
                const respuesta = await fetch(
                    '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/visita/backend_visita/registrar_visita.php',
                    {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json' },
                        body: datosFormulario
                    }
                );
                const resultado = await respuesta.json();

                if (!respuesta.ok || resultado.exito !== true) {
                    throw new Error(resultado.mensaje || `No se pudo registrar la visita (HTTP ${respuesta.status}).`);
                }

                formulario.reset();
                const numeroRegistro = resultado.datos?.id_visita
                    ? ` Número de registro: #${resultado.datos.id_visita}.`
                    : '';
                mostrarMensaje(`${resultado.mensaje || 'Visita registrada correctamente.'}${numeroRegistro}`, 'exito');
            } catch (error) {
                mostrarMensaje(error.message || 'No se pudo registrar la visita.', 'error');
                console.error('Error al registrar la visita:', error);
            } finally {
                if (botonRegistrar) {
                    botonRegistrar.disabled = false;
                    botonRegistrar.textContent = 'Registrar ingreso';
                }
            }
        });
    }

    cargarDepartamentos();
});
