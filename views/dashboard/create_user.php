<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Crear usuario</h1>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="index.php?c=dashboard&a=createUser" class="form-grid">
    <label>Nombres<span>*</span></label>
    <input type="text" name="nombres" required>

    <label>Apellidos<span>*</span></label>
    <input type="text" name="apellidos" required>

    <label>Tipo de documento<span>*</span></label>
    <select name="tipo_documento" required>
        <option value="">-- Seleccionar --</option>
        <option value="T.I">T.I</option>
        <option value="C.C">C.C</option>
        <option value="PPT">PPT</option>
        <option value="C.E">C.E</option>
        <option value="PAS">PAS</option>
    </select>

    <label>Número de documento<span>*</span></label>
    <input type="text" name="numero_documento" required>

    <label>Teléfono<span>*</span></label>
    <input type="text" name="telefono" required>

    <label>Correo electrónico<span>*</span></label>
    <input type="email" name="email" required>

    <label>Usuario<span>*</span></label>
    <input type="text" name="username" required>

    <label>Rol<span>*</span></label>
    <select name="role_id" required>
        <option value="">-- Seleccionar --</option>
        <option value="1">Administrador</option>
        <option value="2">Supervisor</option>
        <option value="4">Coordinador de área</option>
        <option value="3">Trabajador</option>
    </select>

    <label>Contraseña<span>*</span></label>
    <input type="password" name="password" required>

    <button type="submit" class="btn btn-success me-2">
        <i class="bi bi-check-circle me-1"></i>Guardar
    </button>
</form>

<p><a class="btn btn-outline-secondary" href="index.php?c=dashboard&a=users">
    <i class="bi bi-arrow-left me-1"></i>Volver a Usuarios
</a></p>

<?php include __DIR__ . '/../layouts/footer.php'; ?>