<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Editar usuario</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php
$fullnameParts = explode(' ', $user['fullname'] ?? '', 2);
$nombres = $fullnameParts[0] ?? '';
$apellidos = $fullnameParts[1] ?? '';
?>

<form method="post" action="index.php?c=dashboard&a=editUser&id=<?= htmlspecialchars($user['id']) ?>" class="form-grid">
    <label>Nombres<span>*</span></label>
    <input type="text" name="nombres" value="<?= htmlspecialchars($nombres) ?>" required>

    <label>Apellidos<span>*</span></label>
    <input type="text" name="apellidos" value="<?= htmlspecialchars($apellidos) ?>" required>

    <label>Usuario<span>*</span></label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>

    <label>Correo electrónico<span>*</span></label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

    <label>Rol<span>*</span></label>
    <select name="role_id" required>
        <option value="">-- Seleccionar --</option>
        <?php foreach ($roleOptions as $rid => $roleLabel): ?>
            <option value="<?= $rid ?>" <?= (isset($user['role_id']) && $user['role_id'] == $rid) ? 'selected' : '' ?>><?= htmlspecialchars($roleLabel) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Contraseña</label>
    <input type="password" name="password" placeholder="Dejar en blanco para no cambiarla">

    <button type="submit" class="btn">Actualizar</button>
</form>

<p><a class="btn" href="index.php?c=dashboard&a=users">Volver a Usuarios</a></p>

<?php include __DIR__ . '/../layouts/footer.php'; ?>