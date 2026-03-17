<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Listado de Exámenes</h1>
<p><a class="btn" href="index.php?c=dashboard&a=index">Volver al Dashboard</a></p>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Empresa</th>
            <th>Tipo</th>
            <th>Candidato</th>
            <th>Estado</th>
            <th>Creado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ((new ExamModel())->all() as $exam): ?>
            <tr>
                <td><?= $exam['id'] ?></td>
                <td><?= htmlspecialchars($exam['company']) ?></td>
                <td><?= htmlspecialchars($exam['exam_type']) ?></td>
                <td><?= htmlspecialchars($exam['candidate_name']) ?></td>
                <td><?= $exam['status'] ?></td>
                <td><?= $exam['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../layouts/footer.php'; ?>