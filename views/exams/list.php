<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Listado de Exámenes</h1>
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
    <a class="btn" href="index.php?c=dashboard&a=index"><i class="bi bi-arrow-left"></i> Volver al Dashboard</a>
    <a class="btn btn-success" href="index.php?c=exam&a=add" style="margin-right: 0;"><i class="bi bi-upload"></i> Importar Pacientes</a>
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
                    <a class="btn-small" style="background:#e0e0e0;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;margin-right:2px;" href="index.php?c=exam&a=view&id=<?= $exam['id'] ?>"><i class="bi bi-eye"></i> <u>Ver</u></a>
                    <a class="btn-small" style="background:#e0e0e0;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;margin-right:2px;" href="index.php?c=exam&a=edit&id=<?= $exam['id'] ?>"><i class="bi bi-pencil"></i> <u>Editar</u></a>
                    <a class="btn-small btn-danger" style="background:#d98880;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;" href="index.php?c=exam&a=delete&id=<?= $exam['id'] ?>" onclick="return confirm('¿Eliminar paciente?');"><i class="bi bi-trash"></i> <u>Eliminar</u></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>