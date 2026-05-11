<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1><?= htmlspecialchars($company['name']) ?></h1>
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
    <a class="btn" href="index.php?c=company&a=list">← Volver a Empresas</a>
</div>

<div class="order-info" style="margin-bottom: 1.5rem; padding: 1.5rem; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <p style="margin: 0; font-size: 0.95rem;"><strong style="color: #4973A8;">Contacto:</strong> <span style="color: #333;"><?= htmlspecialchars($company['contact'] ?? 'No especificado') ?></span></p>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success" style="padding: 1rem 1.2rem; background: linear-gradient(135deg, #e6ffed, #f0fff4); border: 1px solid #96d79c; color: #0f6a1c; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-weight: 500;">
        ✓ <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<h2 style="margin-top: 2rem;">Órdenes de Examen</h2>

<?php if (empty($orderGroups)): ?>
    <p style="color: #999; font-style: italic;">No hay órdenes asignadas a esta empresa.</p>
<?php else: ?>
    <?php if (empty($orderNumber)): ?>
        <p style="margin-bottom: 1.5rem; color: #555; font-size: 0.95rem;">Seleccione una orden para ver los pacientes correspondientes:</p>
        <div class="order-cards-wrapper" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <?php foreach ($orderGroups as $group): ?>
                <div class="order-card">
                    <strong style="font-size: 1.1rem; color: #2c3e50;">Orden: <?= htmlspecialchars($group['order_number'] ?? 'N/A') ?></strong>
                    <p style="margin: 0.3rem 0 0 0; font-size: 0.9rem; color: #7f8c8d;"><strong><?= intval($group['total']) ?></strong> pacientes</p>
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.8rem;">
                        <a href="index.php?c=company&a=view&id=<?= $company['id'] ?>&order=<?= urlencode($group['order_number']) ?>" class="btn" style="flex: 1; text-align: center; font-size: 0.9rem; padding: 0.55rem 0.9rem; margin-right: 0;">Abrir</a>
                        <a href="index.php?c=company&a=clearOrder&id=<?= $company['id'] ?>&order=<?= urlencode($group['order_number']) ?>" class="btn btn-danger" style="background:#d98880;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;font-size: 0.85rem; margin-right: 0;" onclick="return confirm('¿Eliminar todos los <?= intval($group['total']) ?> pacientes de la orden <?= htmlspecialchars($group['order_number']) ?>?');"><i class="bi bi-trash"></i> <u>Depurar</u></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: #f5f7fa; padding: 1.2rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #e0e6ed;">
            <p style="margin: 0 0 1rem 0; font-size: 0.95rem; color: #555;">
                Orden: <strong style="color: #4973A8; font-size: 1.05rem;"><?= htmlspecialchars($orderNumber) ?></strong> 
                <span style="color: #999; font-size: 0.9rem;">— <?= count($exams) ?> pacientes</span>
            </p>
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
                <a class="btn" href="index.php?c=company&a=view&id=<?= $company['id'] ?>" style="font-size: 0.9rem;">← Volver a órdenes</a>
                <a class="btn btn-success" href="index.php?c=exam&a=exportCandidates&order=<?= urlencode($orderNumber) ?>" style="font-size: 0.9rem;">📥 Exportar XLSX</a>
                <a class="btn btn-danger" href="index.php?c=company&a=clearOrder&id=<?= $company['id'] ?>&order=<?= urlencode($orderNumber) ?>" style="background:#d98880;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;font-size: 0.9rem;" onclick="return confirm('¿Eliminar todos los <?= count($exams) ?> pacientes de la orden <?= htmlspecialchars($orderNumber) ?>?');"><i class="bi bi-trash"></i> <u>Depurar carpeta</u></a>
            </div>
        </div>

        <?php if (empty($exams)): ?>
            <p style="color: #999; font-style: italic; text-align: center; padding: 2rem;">No hay pacientes registrados para la orden seleccionada.</p>
        <?php else: ?>
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
                    <?php $sequence = 1; ?>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?= $sequence++ ?></td>
                            <td><?= htmlspecialchars($exam['document_number'] ?? '') ?></td>
                            <td><?= htmlspecialchars($exam['candidate_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($exam['phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($exam['birth_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($exam['gender'] ?? '') ?></td>
                            <td><?= htmlspecialchars($exam['exam_date'] ?? '') ?></td>
                            <td><?php
                                $statusMap = [
                                    'FINALIZADO' => 'Apto',
                                    'RECHAZADO' => 'No Apto',
                                    'EN_CURSO' => 'Aplazado',
                                    'SIN_RESULTADO' => 'Sin resultado',
                                    'PENDIENTE' => 'Pendiente'
                                ];
                                echo htmlspecialchars($statusMap[$exam['status']] ?? 'Pendiente');
                            ?></td>
                            <td><?= htmlspecialchars($exam['order_number'] ?? '') ?></td>
                            <td>
                                <a class="btn btn-sm btn-warning me-2" href="index.php?c=exam&a=edit&id=<?= $exam['id'] ?>">
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </a>
                                <a class="btn btn-sm btn-danger" href="index.php?c=exam&a=delete&id=<?= $exam['id'] ?>" onclick="return confirm('¿Eliminar paciente?');">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
