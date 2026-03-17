<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Dashboard</h1>

<section class="summary">
    <p>Total de exámenes: <?= count($exams) ?></p>
    <a class="btn" href="index.php?c=exam&a=add">Agregar examen</a>
    <a class="btn" href="index.php?c=exam&a=list">Ver lista general</a>
</section>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Empresa</th>
            <th>Tipo examen</th>
            <th>Candidato</th>
            <th>Estado</th>
            <th>Creado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($exams as $exam): ?>
            <tr>
                <td><?= $exam['id'] ?></td>
                <td><?= htmlspecialchars($exam['company']) ?></td>
                <td><?= htmlspecialchars($exam['exam_type']) ?></td>
                <td><?= htmlspecialchars($exam['candidate_name']) ?></td>
                <td><?= $exam['status'] ?></td>
                <td><?= $exam['created_at'] ?></td>
                <td>
                    <form method="post" action="index.php?c=exam&a=status" class="inline-form">
                        <input type="hidden" name="id" value="<?= $exam['id'] ?>">
                        <select name="status">
                            <?php foreach (['PENDIENTE','EN_CURSO','FINALIZADO','RECHAZADO'] as $status): ?>
                                <option value="<?= $status ?>" <?= ($status === $exam['status']) ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layouts/footer.php'; ?>