<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Agregar examen</h1>
<p><a class="btn" href="index.php?c=dashboard&a=index">Volver al Dashboard</a></p>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="index.php?c=exam&a=add" class="form-grid">
    <label>Empresa</label>
    <select name="company_id" required>
        <option value="">-- Seleccionar empresa --</option>
        <?php foreach ($companies as $company): ?>
            <option value="<?= $company['id'] ?>"><?= htmlspecialchars($company['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Tipo de examen</label>
    <select name="exam_type_id" required>
        <option value="">-- Seleccionar tipo --</option>
        <?php foreach ($examTypes as $type): ?>
            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Nombre del candidato</label>
    <input type="text" name="candidate_name" required>

    <button type="submit">Crear examen</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>