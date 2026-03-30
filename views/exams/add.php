<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Agregar examen</h1>
<p><a class="btn" href="index.php?c=dashboard&a=index">Volver</a></p>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="index.php?c=exam&a=add" class="form-grid">
    <label>Empresa</label>
    <input list="company-list" name="company_id" placeholder="Busca o selecciona una empresa" required>
    <datalist id="company-list">
        <?php foreach ($companies as $company): ?>
            <option value="<?= htmlspecialchars($company['name']) ?>"></option>
        <?php endforeach; ?>
    </datalist>

    <label>Tipo de examen</label>
    <select name="exam_type_id" required>
        <option value="">-- Seleccionar tipo --</option>
        <?php $allowedTypes = [
            'examen psicofisico',
            'examen psicosensometrico',
            'examen ocupacional de ingreso',
            'examen ocupacional de retiro',
            'examen ocupacional periodico'
        ]; ?>
        <?php foreach ($examTypes as $type): ?>
            <?php if (in_array(strtolower($type['name']), $allowedTypes, true)): ?>
                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <label>N° de Orden</label>
    <input type="text" name="order_number" placeholder="Número de orden para este examen" required>

    <label>Datos del candidato (texto libre, sin límite de caracteres)</label>
    <textarea name="candidate_text" rows="8" placeholder="Pega aquí los datos de una o varias personas." required></textarea>

    <button type="submit">Crear examen</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>