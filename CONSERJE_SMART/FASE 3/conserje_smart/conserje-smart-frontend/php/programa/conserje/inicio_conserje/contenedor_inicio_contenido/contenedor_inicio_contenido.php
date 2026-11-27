<!-- TITULO 1 CONTENEDOR CONTENIDO INICIO -->

    <!-- contenedor contenido inicio -->
<?php
$nombreUsuario = trim((string) ($_SESSION['nombre_usuario'] ?? ''));
$nombreUsuario = $nombreUsuario !== '' ? $nombreUsuario : 'Usuario';
$fechaActual = date('d/m/Y');
?>

    <main class="contenedor_contenido_inicio" id="contenedor_contenido_inicio">

        <!-- TITULO 2 PARTE SUPERIOR CONTENIDO INICIO -->

            <!-- parte superior contenido inicio -->
            <section class="parte_superior_contenido_inicio" id="parte_superior_contenido_inicio">

                <!-- TITULO 3 CONTENEDOR TITULO CONTENIDO -->

                    <!-- contenedor que recibe titulo_contenido.php -->
                    <div class="contenedor_titulo_contenido" id="contenedor_titulo_contenido">
                        <span class="etiqueta_seccion">Panel de turno</span>
                        <h1>Bienvenido, <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8'); ?>!</h1>
                    </div>

                <!-- TITULO 4 CONTENEDOR FECHA CONTENIDO -->

                    <!-- contenedor que recibe fecha_contenido.php -->
                    <div class="contenedor_fecha_contenido" id="contenedor_fecha_contenido">
                        <?= htmlspecialchars($fechaActual, ENT_QUOTES, 'UTF-8'); ?>
                    </div>

                <!-- TITULO 5 CONTENEDOR SUBTITULO CONTENIDO -->

                    <!-- contenedor que recibe subtitulo_contenido.php -->
                    <div class="contenedor_subtitulo_contenido" id="contenedor_subtitulo_contenido">
                        Aquí encontrarás las novedades y tareas importantes de tu turno.
                    </div>

            </section>

        <!-- TITULO 6 PARTE INFERIOR CONTENIDO INICIO -->

            <!-- parte inferior contenido inicio -->
            <section class="parte_inferior_contenido_inicio" id="parte_inferior_contenido_inicio">

                <!-- TITULO 7 CONTENEDOR ACTIVIDADES CONTENIDO -->

                    <!-- contenedor que recibe actividades_contenido.php -->
                    <div class="contenedor_actividades_contenido" id="contenedor_actividades_contenido">
                        <!-- TITULO ENCABEZADO ACTIVIDADES INICIO -->
                        <div class="encabezado_actividades_inicio">
                            <!-- TITULO TITULOS ACTIVIDADES INICIO -->
                            <div class="titulos_actividades_inicio">
                                <span class="icono_actividades_inicio" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                                        <path d="m15 4 6-2v18l-6-3H5a3 3 0 0 1-3-3v-4a3 3 0 0 1 3-3h10V4ZM7 17l2 5h4l-2-5M15 7v10" />
                                    </svg>
                                </span>
                                <div class="descripcion_actividades_inicio">
                                    <h2 id="titulo_actividades_inicio">Novedades y Actividades</h2>
                                    <p>Últimos ingresos de visitas del condominio.</p>
                                </div>
                            </div>
                            <span class="contador_actividades_inicio" id="contador_actividades_inicio" hidden></span>
                        </div>

                        <!-- TITULO CONTROLES ACTIVIDADES INICIO -->
                        <div class="controles_actividades_inicio">
                            <p class="mensaje_actividades_inicio" id="mensaje_actividades_inicio" role="status" aria-live="polite">Cargando visitas recientes...</p>
                            <button class="actualizar_actividades_inicio" id="actualizar_actividades_inicio" type="button">Actualizar</button>
                        </div>

                        <!-- TITULO LISTA ACTIVIDADES -->
                        <ul class="lista_actividades" id="lista_actividades_inicio" aria-labelledby="titulo_actividades_inicio" aria-busy="true"></ul>

                        <!-- TITULO ENLACE VISITAS INICIO -->
                        <a class="enlace_visitas_inicio" href="/conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/visita/visita.php#contenedor_tablero_visita">Ver todas las visitas <span aria-hidden="true">→</span></a>
                        <noscript>Activa JavaScript para consultar las visitas recientes o utiliza el enlace al tablero.</noscript>
                    </div>

                <!-- TITULO 8 CONTENEDOR ATAJOS CONTENIDO -->

                    <!-- contenedor que recibe atajos_contenido.php -->
                    <div class="contenedor_atajo_contenido" id="contenedor_atajo_contenido">

                        <!-- TITULO 9 CONTENEDOR ACCESO RAPIDO -->

                            <!-- contenedor que recibe acceso_rapido.php -->
                            <div class="contenedor_acceso_rapido" id="contenedor_acceso_rapido">
                                <!-- TITULO ENCABEZADO ACCESO RAPIDO -->
                                <div class="encabezado_acceso_rapido">
                                    <svg class="icono_acceso_rapido" viewBox="0 0 24 24" fill="#e5ba31" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="M13 2 4 14h7l-1 8 10-13h-7l1-7Z" />
                                    </svg>
                                    <h2>Accesos rápidos</h2>
                                </div>

                                <!-- TITULO BOTON REGISTRAR VISITA INICIO -->
                                <a class="boton_registrar_visita_inicio" id="boton_registrar_visita_inicio" href="/conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/visita/visita.php#contenedor_formulario_visita">
                                    <svg class="icono_registrar_visita_inicio" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M2 21v-2a7 7 0 0 1 12-4M19 14v8M15 18h8" />
                                    </svg>
                                    <span class="texto_registrar_visita_inicio">Registrar visita</span>
                                    <svg class="flecha_registrar_visita_inicio" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="m9 5 7 7-7 7" />
                                    </svg>
                                </a>

                                <!-- TITULO 10 BOTON BITACORA INICIO -->
                                <a class="boton_bitacora_inicio" id="boton_bitacora_inicio" href="/conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/bitacora/bitacora.php">
                                    <svg class="icono_bitacora_inicio" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="M12 7v14M3 18V5a2 2 0 0 1 2-2h3a4 4 0 0 1 4 4 4 4 0 0 1 4-4h3a2 2 0 0 1 2 2v13h-5a4 4 0 0 0-4 2 4 4 0 0 0-4-2H3Z" />
                                    </svg>
                                    <span class="texto_bitacora_inicio">Bitácora</span>
                                    <svg class="flecha_bitacora_inicio" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="m9 5 7 7-7 7" />
                                    </svg>
                                </a>

                                <!-- TITULO 11 BOTON PAQUETERIA INICIO -->
                                <a class="boton_paqueteria_inicio" id="boton_paqueteria_inicio" href="/conserje_smart/conserje-smart-frontend/php/programa/conserje/inicio_conserje/modulos/paqueteria/paqueteria.php">
                                    <svg class="icono_paqueteria_inicio" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="m3 7 9-4 9 4v10l-9 4-9-4V7Z" />
                                        <path d="m3 7 9 4 9-4M12 11v10M7.5 5.2 16.5 9.4" />
                                    </svg>
                                    <span class="texto_paqueteria_inicio">Paquetería</span>
                                    <svg class="flecha_paqueteria_inicio" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                        <path d="m9 5 7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                        <!-- TITULO 12 CONTENEDOR CRONOGRAMA -->

                            <!-- contenedor que recibe cronograma.php -->
                            <div class="contenedor_cronograma" id="contenedor_cronograma">
                            </div>

                    </div>

            </main>