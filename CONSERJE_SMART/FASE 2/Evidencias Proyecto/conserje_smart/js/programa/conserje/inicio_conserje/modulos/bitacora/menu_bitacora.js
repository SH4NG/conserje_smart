// TITULO 1 INICIALIZAR CONTROLES DE MENU
(() => {
    const botonAnalizar = document.getElementById('boton_estructurar_ia');
    const campoNovedad = document.getElementById('input_novedad');
    const estadoAnalisis = document.getElementById('estado_analisis_novedad');

    if (!botonAnalizar || !campoNovedad || !estadoAnalisis) {
        console.error('No se pudo inicializar el análisis: faltan elementos del menú de bitácora.');
        return;
    }

    // TITULO 2 VALIDAR Y SOLICITAR ANALISIS
    const analizar = async () => {
        if (botonAnalizar.getAttribute('aria-busy') === 'true') {
            return;
        }

        const novedad = campoNovedad.value.trim();
        if (novedad === '') {
            estadoAnalisis.textContent = 'Escribe una novedad antes de estructurarla.';
            campoNovedad.focus();
            return;
        }

        botonAnalizar.setAttribute('aria-busy', 'true');
        estadoAnalisis.textContent = 'Analizando la novedad. Esto puede tardar unos segundos...';

        try {
            // TITULO 3 MOSTRAR RESULTADO O ERROR
            const respuesta = await fetch(
                '/conserje_smart/php/programa/conserje/inicio_conserje/modulos/bitacora/backend_bitacora/analizar_novedad.php',
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ novedad })
                }
            );
            const resultado = await respuesta.json();

            if (!respuesta.ok || resultado.exito !== true || !resultado.datos) {
                throw new Error(resultado.mensaje || `Error HTTP ${respuesta.status}`);
            }

            window.dispatchEvent(new CustomEvent('bitacora:novedad-analizada', {
                detail: resultado.datos
            }));
            estadoAnalisis.textContent = 'Análisis listo. Revisa el resultado antes de continuar.';
        } catch (error) {
            estadoAnalisis.textContent = error.message || 'No se pudo analizar la novedad.';
            console.error('Error al solicitar el análisis de la novedad:', error);
        } finally {
            botonAnalizar.removeAttribute('aria-busy');
        }
    };

    // TITULO 4 CONECTAR EVENTOS DEL BOTON
    botonAnalizar.addEventListener('click', analizar);
    botonAnalizar.addEventListener('keydown', (evento) => {
        if (evento.key === 'Enter' || evento.key === ' ') {
            evento.preventDefault();
            analizar();
        }
    });
})();