    </main>

    <footer class="bg-light border-top mt-auto">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Atlas Seguridad - Sistema de Gestión de Exámenes
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">© 2026 - Versión 1.0</small>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted d-block">Creado por Daniel Bernal Castellanos</small>
                    <small class="text-muted d-block">Contacto: +57 321 955 8545</small>
                    <small class="text-muted d-block">Correo: angeldb20052@gmail.com</small>
                    <small class="text-muted d-block">Certificado e implementado</small>
                </div>
            </div>
        </div>
    </footer>

    <button id="scrollTopBtn" type="button" class="btn btn-primary scroll-top-btn" aria-label="Ir arriba">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var scrollBtn = document.getElementById('scrollTopBtn');
            if (!scrollBtn) return;

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 250) {
                    scrollBtn.classList.add('show');
                } else {
                    scrollBtn.classList.remove('show');
                }
            });

            scrollBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="public/js/app.js"></script>
</body>
</html>
