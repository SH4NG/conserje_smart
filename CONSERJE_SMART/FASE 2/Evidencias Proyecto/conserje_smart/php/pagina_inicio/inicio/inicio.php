    <!-- TITULO PAGINA_INICIO  -->

     <!-- estilo pagina inicio provisorio, sacar despues de agregar algo  -->

        <!-- TITULO CONTENIDO_PAGINA_INICIO -->

        <div class=titulo_inicio>

            TE DAMOS LA BIENVENIDA A CONSERJE SMART

        </div>

        <!-- TITULO QUIENES SOMOS  -->

            <!-- se llama al quienes_somos mediante include -->

        <div class="contenedor_contenindo_inicio" id="contenedor_contenindo_inicio">
            <div class="contenedor_quienes_somos" id="contenedor_quienes_somos">
                
                <?php include __DIR__ . '/quienes_somos/quienes_somos.php'; ?>

            </div>

            <div class="contenedor_nuestro_objetivo" id="contenedor_nuestro_objetivo">
                
                <?php include __DIR__ . '/nuestro_objetivo/nuestro_objetivo.php'; ?>

            </div>

            <div class="contenedor_nuestra_vision" id="contenedor_nuestra_vision">

                <?php include __DIR__ . '/nuestra_vision/nuestra_vision.php'; ?>

            </div>

            <div class="contenedor_consultas" id="contenedor_consultas">

                <?php include __DIR__ . '/consultas/consultas.php'; ?>

            </div>

        </div>