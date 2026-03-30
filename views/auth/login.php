<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Login</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="index.php?c=auth&a=login">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text">👤</span>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text">🔒</span>
                        <input type="password" id="password-field" name="password" class="form-control" placeholder="Password" required>
                        <button type="button" class="btn btn-outline-secondary" id="toggle-password" aria-label="Mostrar contraseña">👁</button>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
            </form>
            <p class="text-center mt-3">¿Nuevo aquí? <strong>Regístrate</strong></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>