<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-card">
                <h2>Recuperar Contraseña</h2>
                <p class="text-center mb-4" style="color: rgba(255,255,255,.75); font-size: 0.95rem; line-height: 1.4;">
                    Ingresa tu email para recuperar tu contraseña
                </p>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?c=auth&a=forgot">
                    <div class="input-group email">
                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email"
                               class="form-control"
                               placeholder="Tu email" required autocomplete="email"
                               style="width: 100%; margin-bottom: 1rem; padding-left: 2.6rem;">
                    </div>

                    <button type="submit" class="primary-btn">
                        <i class="bi bi-send me-2"></i>Enviar enlace de recuperación
                    </button>
                </form>

                <p class="signup" style="margin-top: 1.5rem;">
                    ¿Recordaste tu contraseña? <a href="index.php?c=auth&a=login" style="color: #fff; text-decoration: underline;">Volver al login</a>
                </p>
            </div>

            <div class="text-center mt-4">
                <p class="text-white-50 small mb-0">
                    <i class="bi bi-shield-lock me-1"></i>
                    Recuperación segura
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
