// Esperamos a que el documento cargue
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('overlay');

    btn.addEventListener('click', () => {
        // toggle añade la clase si no está, y la quita si ya está
        sidebar.classList.toggle('active');
    });
});