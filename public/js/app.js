// Archivo de JavaScript base para funcionalidades futuras
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Agregar animación de entrada a la página
    document.body.classList.add('fade-in');

    const themeToggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('atlas-theme') || 'light';

    const applyTheme = (theme) => {
        document.body.classList.remove('light-theme', 'dark-theme');
        document.body.classList.add(`${theme}-theme`);
        localStorage.setItem('atlas-theme', theme);
        if (themeToggle) {
            themeToggle.setAttribute('aria-label', theme === 'dark' ? 'Activar modo claro' : 'Activar modo oscuro');
        }
    };

    if (savedTheme) {
        applyTheme(savedTheme);
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const current = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        });
    }

    const passwordField = document.getElementById('password-field');
    const togglePassword = document.getElementById('toggle-password');

    if (passwordField && togglePassword) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordField.getAttribute('type') === 'password';
            passwordField.setAttribute('type', isPassword ? 'text' : 'password');
            this.textContent = isPassword ? '🙈' : '👁';
            this.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    // Funcionalidad de búsqueda mejorada
    const searchInputs = document.querySelectorAll('input[name="search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = this.closest('.container-fluid').querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    });

    // Mejorar UX de botones de confirmación
    const confirmButtons = document.querySelectorAll('a[onclick*="confirm"]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('onclick').match(/confirm\('([^']+)'\)/);
            if (message && !confirm(message[1])) {
                e.preventDefault();
            }
        });
    });
});

