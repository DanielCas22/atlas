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

    <button type="submit" class="btn">Guardar</button>
    <a class="btn" href="index.php?c=company&a=list">Cancelar</a>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
