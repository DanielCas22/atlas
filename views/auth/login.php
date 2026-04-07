<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-card">
                <h2>Atlas Seguridad</h2>
                <p class="text-center mb-4" style="color: rgba(255,255,255,.75); font-size: 0.95rem; line-height: 1.4;">
                    Sistema de Gestión de Exámenes
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?c=auth&a=login">
                    <div class="input-group username">
                        <span class="input-icon"><i class="bi bi-person"></i></span>
                        <input type="text" id="username" name="username"
                               class="form-control"
                               placeholder="Usuario" required autocomplete="username"
                               style="width: 100%; margin-bottom: 1rem; padding-left: 2.6rem;">
                    </div>

                    <div class="input-group password">
                        <span class="input-icon"><i class="bi bi-lock"></i></span>
                        <div class="password-wrapper" style="width: 100%;">
                            <input type="password" id="password-field" name="password"
                                   class="form-control"
                                   placeholder="Contraseña" required autocomplete="current-password"
                                   style="width: 100%; margin-bottom: 1rem; padding-left: 2.6rem;">
                            <button type="button" class="password-toggle" id="toggle-password" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="login-extras">
                        <label class="form-check-label" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" style="width: auto; margin: 0;">
                            Recordarme
                        </label>
                        <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="primary-btn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>
                </form>

                <p class="signup">
                    ¿No tienes cuenta? <strong>Contacta al administrador</strong>
                </p>
            </div>

            <div class="text-center mt-4">
                <p class="text-white-50 small mb-0">
                    <i class="bi bi-shield-lock me-1"></i>
                    Acceso seguro y confidencial
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password-field');
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>