<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Editar Candidato</h1>
<p><a class="btn" href="index.php?c=exam&a=list">Volver a listado</a></p>

<?php if (!empty($error)): ?>
    <div style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" action="index.php?c=exam&a=edit&id=<?= intval($exam['id']) ?>" class="form-grid">
    <label>Número de identificación</label>
    <input type="text" name="document_number" value="<?= htmlspecialchars($exam['document_number'] ?? '') ?>" required>

    <label>Nombre</label>
    <input type="text" name="candidate_name" value="<?= htmlspecialchars($exam['candidate_name'] ?? '') ?>" required>

    <label>Teléfono</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($exam['phone'] ?? '') ?>">

    <label>Género</label>
    <select name="gender">
        <option value="">-- Seleccionar --</option>
        <option value="M" <?= (isset($exam['gender']) && strtoupper($exam['gender']) === 'M') ? 'selected' : '' ?>>M</option>
        <option value="F" <?= (isset($exam['gender']) && strtoupper($exam['gender']) === 'F') ? 'selected' : '' ?>>F</option>
    </select>

    <label>Fecha de nacimiento</label>
    <input type="date" name="birth_date" value="<?= htmlspecialchars($exam['birth_date'] ?? '') ?>">

    <label>Fecha de examen</label>
    <input type="date" name="exam_date" value="<?= htmlspecialchars($exam['exam_date'] ?? '') ?>">

    <label>Orden</label>
    <input type="text" name="order_number" value="<?= htmlspecialchars($exam['order_number'] ?? '') ?>">

    <label>Resultado</label>
    <select name="status">
        <option value="PENDIENTE" <?= (isset($exam['status']) && $exam['status'] === 'PENDIENTE') ? 'selected' : '' ?>>Pendiente</option>
        <option value="FINALIZADO" <?= (isset($exam['status']) && $exam['status'] === 'FINALIZADO') ? 'selected' : '' ?>>Apto</option>
        <option value="RECHAZADO" <?= (isset($exam['status']) && $exam['status'] === 'RECHAZADO') ? 'selected' : '' ?>>No apto</option>
        <option value="EN_CURSO" <?= (isset($exam['status']) && $exam['status'] === 'EN_CURSO') ? 'selected' : '' ?>>Aplazado</option>
        <option value="SIN_RESULTADO" <?= (isset($exam['status']) && $exam['status'] === 'SIN_RESULTADO') ? 'selected' : '' ?>>Sin resultado</option>
    </select>

    <div></div>
    <button type="submit" class="btn btn-success">Guardar cambios</button>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>