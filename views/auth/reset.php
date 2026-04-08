<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-card">
                <h2>Establecer Nueva Contraseña</h2>
                <p class="text-center mb-4" style="color: rgba(255,255,255,.75); font-size: 0.95rem; line-height: 1.4;">
                    Para: <strong><?= htmlspecialchars($user['username'] ?? 'Usuario') ?></strong>
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?c=auth&a=reset&token=<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                    <div class="input-group password">
                        <span class="input-icon"><i class="bi bi-lock"></i></span>
                        <div class="password-wrapper" style="width: 100%;">
                            <input type="password" id="password-field" name="password"
                                   class="form-control"
                                   placeholder="Nueva contraseña" required 
                                   style="width: 100%; margin-bottom: 1rem; padding-left: 2.6rem;">
                            <button type="button" class="password-toggle" id="toggle-password" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group password" style="margin-top: 1rem;">
                        <span class="input-icon"><i class="bi bi-lock-check"></i></span>
                        <div class="password-wrapper" style="width: 100%;">
                            <input type="password" id="confirm-field" name="confirm_password"
                                   class="form-control"
                                   placeholder="Confirmar contraseña" required 
                                   style="width: 100%; margin-bottom: 1rem; padding-left: 2.6rem;">
                            <button type="button" class="password-toggle" id="toggle-confirm" aria-label="Mostrar contraseña">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <p style="font-size: 0.85rem; color: rgba(255,255,255,.7); margin-bottom: 1rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Mínimo 6 caracteres
                    </p>

                    <button type="submit" class="primary-btn">
                        <i class="bi bi-check-circle me-2"></i>Actualizar Contraseña
                    </button>
                </form>

                <p class="signup" style="margin-top: 1.5rem;">
                    <a href="index.php?c=auth&a=login" style="color: #fff; text-decoration: underline;">Volver al login</a>
                </p>
            </div>

            <div class="text-center mt-4">
                <p class="text-white-50 small mb-0">
                    <i class="bi bi-shield-lock me-1"></i>
                    Actualización segura
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

    const toggleConfirm = document.getElementById('toggle-confirm');
    const confirmField = document.getElementById('confirm-field');
    if (toggleConfirm && confirmField) {
        toggleConfirm.addEventListener('click', function () {
            const isPassword = confirmField.type === 'password';
            confirmField.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
