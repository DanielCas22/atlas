<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Listado de Exámenes</h1>
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
    <a class="btn" href="index.php?c=dashboard&a=index">← Volver al Dashboard</a>
    <a class="btn btn-success" href="index.php?c=exam&a=add" style="margin-right: 0;">+ Importar Pacientes</a>
</div>

<div style="overflow-x: auto; border-radius: 8px;">
<table class="table">
    <thead>
        <tr>
            <th>N°</th>
            <th>IDENTIFICACION</th>
            <th>NOMBRE</th>
            <th>TELEFONO</th>
            <th>FECHA DE NACIMIENTO</th>
            <th>GENERO</th>
            <th>FECHA DE EXAMEN</th>
            <th>RESULTADO</th>
            <th>ORDEN</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ((new ExamModel())->allWithDetails() as $exam): ?>
            <tr>
                <td><?= $exam['id'] ?></td>
                <td><?= htmlspecialchars($exam['document_number'] ?? '') ?></td>
                <td><?= htmlspecialchars($exam['candidate_name']) ?></td>
                <td><?= htmlspecialchars($exam['phone'] ?? '') ?></td>
                <td><?= htmlspecialchars($exam['birth_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($exam['gender'] ?? '') ?></td>
                <td><?= htmlspecialchars($exam['exam_date'] ?? '') ?></td>
                <td><?php
                    $statusMap = [
                        'FINALIZADO' => 'Apto',
                        'RECHAZADO' => 'No Apto',
                        'EN_CURSO' => 'Aplazado',
                        'PENDIENTE' => 'Pendiente'
                    ];
                    echo htmlspecialchars($statusMap[$exam['status']] ?? $exam['status']);
                ?></td>
                <td><?= htmlspecialchars($exam['order_number'] ?? '') ?></td>
                <td>
                    <a class="btn btn-warning" href="index.php?c=exam&a=edit&id=<?= $exam['id'] ?>">Editar</a>
                    <a class="btn btn-danger" href="index.php?c=exam&a=delete&id=<?= $exam['id'] ?>" onclick="return confirm('¿Eliminar paciente?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>