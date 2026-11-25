const sectionNavigation = document.querySelector("#navegacion_pie_pagina");
const sectionButtons = sectionNavigation?.querySelectorAll("[data-section-target]");
const sectionContainers = document.querySelectorAll(
    ".contenedor_quienes_somos, .contenedor_nuestro_objetivo, " +
    ".contenedor_nuestra_vision, .contenedor_consultas"
);

sectionButtons?.forEach((button) => {
    button.addEventListener("click", () => {
        const targetId = button.dataset.sectionTarget;
        const selectedSection = targetId
            ? document.getElementById(targetId)
            : null;

        if (!selectedSection || !Array.from(sectionContainers).includes(selectedSection)) {
            console.error(`No se encontró una sección válida para el destino "${targetId}".`);
            return;
        }

        sectionContainers.forEach((section) => {
            section.hidden = section !== selectedSection;
        });

        sectionButtons.forEach((navigationButton) => {
            navigationButton.setAttribute(
                "aria-pressed",
                String(navigationButton === button)
            );
        });

        selectedSection.scrollIntoView({ behavior: "smooth", block: "start" });
    });
});
