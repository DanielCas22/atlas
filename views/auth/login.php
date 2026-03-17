<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="login-card">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?c=auth&a=login">
        <label>Usuario</label>
        <input type="text" name="username" required>
        <label>Contraseña</label>
        <input type="password" name="password" required>
        <button type="submit">Entrar</button>
    </form>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>