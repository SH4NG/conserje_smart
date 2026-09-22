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
                    </div>

                <!-- TITULO 8 CONTENEDOR ATAJOS CONTENIDO -->

                    <!-- contenedor que recibe atajos_contenido.php -->
                    <div class="contenedor_atajo_contenido" id="contenedor_atajo_contenido">

                        <!-- TITULO 9 CONTENEDOR ACCESO RAPIDO -->

                            <!-- contenedor que recibe acceso_rapido.php -->
                            <div class="contenedor_acceso_rapido" id="contenedor_acceso_rapido">
                            </div>

                        <!-- TITULO 10 CONTENEDOR CRONOGRAMA -->

                            <!-- contenedor que recibe cronograma.php -->
                            <div class="contenedor_cronograma" id="contenedor_cronograma">
                            </div>

                    </div>

            </main>