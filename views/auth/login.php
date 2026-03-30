<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">
                <div class="card-body p-5">
                    <!-- Logo/Brand Section -->
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 80px; height: 80px; background: linear-gradient(135deg, #0d6efd, #0056b3) !important;">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                        <h2 class="h3 fw-bold text-primary mb-1">Atlas Seguridad</h2>
                        <p class="text-muted small mb-0">Sistema de Gestión de Exámenes</p>
                    </div>

                    <!-- Login Form -->
                    <form method="post" action="index.php?c=auth&a=login">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger border-0 shadow-sm" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <label for="username" class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="bi bi-person me-1"></i>Usuario
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="bi bi-person text-muted"></i>
                                </span>
                                <input type="text" id="username" name="username"
                                       class="form-control border-start-0 ps-0"
                                       placeholder="Ingresa tu usuario" required
                                       style="padding-left: 0;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="bi bi-lock me-1"></i>Contraseña
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-light">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" id="password-field" name="password"
                                       class="form-control border-start-0 ps-0"
                                       placeholder="Ingresa tu contraseña" required
                                       style="padding-left: 0;">
                                <button type="button" class="btn btn-outline-secondary border-start-0"
                                        id="toggle-password" aria-label="Mostrar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label text-muted small" for="remember">
                                    Recordarme
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none small text-muted">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="text-center">
                        <p class="text-muted small mb-0">
                            ¿No tienes cuenta?
                            <a href="#" class="text-primary text-decoration-none fw-semibold">
                                Contacta al administrador
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="text-center mt-4">
                <p class="text-white-50 small mb-0">
                    <i class="bi bi-shield-lock me-1"></i>
                    Acceso seguro y confidencial
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>