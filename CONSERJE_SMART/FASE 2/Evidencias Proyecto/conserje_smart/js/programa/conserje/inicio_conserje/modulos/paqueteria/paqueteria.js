document.addEventListener('DOMContentLoaded', function() {
    // --- Lógica del Modal ---
    const modal = document.getElementById('modalNuevoPaquete');
    const btnAbrir = document.getElementById('btnAbrirModal');
    const btnCerrar = document.getElementById('btnCerrarModal');
    const btnCancelar = document.getElementById('btnCancelarModal');

    if(btnAbrir) {
        btnAbrir.addEventListener('click', function() {
            modal.classList.add('activo'); 
        });
    }

    function cerrarModal() {
        modal.classList.remove('activo'); 
    }

    if(btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
    if(btnCancelar) btnCancelar.addEventListener('click', cerrarModal);

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            cerrarModal();
        }
    });

    // --- Confirmación de retiro ---
    const formulariosRetirar = document.querySelectorAll('.form-retirar');
    formulariosRetirar.forEach(form => {
        form.addEventListener('submit', function(e) {
            if(!confirm('¿Confirmas que el residente ha retirado el paquete?')) {
                e.preventDefault();
            }
        });
    });

    // --- Abrir pestaña automáticamente si viene desde un retiro ---
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'retirados') {
        const btnRetirados = document.querySelectorAll('.tab-btn')[1]; // El botón de la segunda pestaña
        if(btnRetirados) cambiarTab('retirados', btnRetirados);
    }
});

// Función global para cambiar de pestaña
function cambiarTab(tabId, btnElement) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    document.getElementById('tab-' + tabId).classList.add('active');
    btnElement.classList.add('active');
}