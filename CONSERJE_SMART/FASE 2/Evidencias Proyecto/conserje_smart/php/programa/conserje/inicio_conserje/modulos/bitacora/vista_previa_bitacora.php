<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/conserje_smart/css/programa/conserje/inicio_conserje/modulos/bitacora/vista_previa_bitacora.css">
    <title></title>
</head>
<body>
    <!-- TITULO VISTA PREVIA BITACORA -->
    <div class="contenedor_vista_previa_bitacora" id="contenedor_vista_previa_bitacora"
        data-time-zone="America/Santiago">
        <!-- TITULO FECHA VISTA PREVIA BITACORA -->
        <div class="fecha_vista_previa_bitacora">
            <img
                class="icono_fecha_vista_previa_bitacora"
                src="/conserje_smart/imagenes/programa/conserje/inicio_conserje/modulos/bitacora/Icono_Fecha_vista_previa_bitacora.png"
                alt=""
                aria-hidden="true">
            <time id="fecha_vista_previa_bitacora" aria-live="polite"></time>
        </div>
        <!-- TITULO DEFINICION TURNO VISTA PREVIA BITACORA -->
        <div class="definicion_turno_vista_previa_bitacora" id="definicion_turno_vista_previa_bitacora"
            aria-live="polite">
            <span class="nombre_turno_vista_previa_bitacora" id="nombre_turno_vista_previa_bitacora"></span>
            <span class="horario_turno_vista_previa_bitacora" id="horario_turno_vista_previa_bitacora"></span>
        </div>
        <!-- TITULO CONTENIDO VISTA PREVIA BITACORA -->
        <div class="contenido_vista_previa" id="contenido_vista_previa"
            aria-label="Espacio para futuras entradas de la bitácora"></div>

    </div>

    <script>
        (() => {
            const previewContainer = document.getElementById("contenedor_vista_previa_bitacora");
            const dateElement = document.getElementById("fecha_vista_previa_bitacora");
            const shiftNameElement = document.getElementById("nombre_turno_vista_previa_bitacora");
            const shiftHoursElement = document.getElementById("horario_turno_vista_previa_bitacora");
            const timeZone = previewContainer?.dataset.timeZone;

            if (!dateElement || !shiftNameElement || !shiftHoursElement || !timeZone) {
                console.error("No se pudo actualizar la fecha y el turno: faltan elementos de la vista previa de bitácora.");
                return;
            }

            const dateFormatter = new Intl.DateTimeFormat("es-CL", {
                timeZone,
                weekday: "long",
                day: "numeric",
                month: "long",
                year: "numeric"
            });
            const dateTimeFormatter = new Intl.DateTimeFormat("en-CA", {
                timeZone,
                year: "numeric",
                month: "2-digit",
                day: "2-digit"
            });
            const timeFormatter = new Intl.DateTimeFormat("en-GB", {
                timeZone,
                hour: "2-digit",
                minute: "2-digit",
                hourCycle: "h23"
            });

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
                    shiftNameElement.textContent = "Turno Mañana";
                    shiftHoursElement.textContent = "08:00 - 16:00";
                } else if (minutesInChile >= 16 * 60) {
                    shiftNameElement.textContent = "Turno Tarde";
                    shiftHoursElement.textContent = "16:00 - 00:00";
                } else {
                    shiftNameElement.textContent = "Turno Noche";
                    shiftHoursElement.textContent = "00:00 - 08:00";
                }
            };

            updatePreview();
            window.setInterval(updatePreview, 30_000);
        })();
    </script>
</body>
</html>