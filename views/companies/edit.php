<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Editar Empresa</h1>
<p><a class="btn" href="index.php?c=company&a=list">Volver a Empresas</a></p>

<?php if (isset($error)): ?>
    <div style="color: red; padding: 10px; background: #ffebee; margin-bottom: 20px;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST" style="max-width: 500px;">
    <div>
        <label for="name">Nombre de la Empresa:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $company['name']) ?>" required>
    </div>

    <div>
        <label for="contact">Contacto:</label>
        <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($_POST['contact'] ?? $company['contact'] ?? '') ?>">
    </div>

    <button type="submit" class="btn btn-success me-2">
        <i class="bi bi-check-circle me-1"></i>Guardar cambios
    </button>
    <a class="btn btn-outline-secondary" href="index.php?c=company&a=list">
        <i class="bi bi-x-circle me-1"></i>Cancelar
    </a>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
