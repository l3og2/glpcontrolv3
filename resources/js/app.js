import './bootstrap';
import * as bootstrap from 'bootstrap';

// Registrar Bootstrap globalmente (opcional, útil para usar en HTML inline)
window.bootstrap = bootstrap;

// Activar tooltips automáticamente
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
});
