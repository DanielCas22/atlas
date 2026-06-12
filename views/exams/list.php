<?php
include __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../helpers/FlashMessage.php';
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold text-primary mb-1">
                        <i class="bi bi-clipboard-data me-2"></i>Listado de Exámenes
                    </h1>
                    <p class="text-muted mb-0">Gestiona y administra los exámenes médicos realizados</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="index.php?c=exam&a=add" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Importar Pacientes
                    </a>
                    <a href="index.php?c=dashboard&a=index" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= FlashMessage::display() ?>

    <!-- Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-list-ul me-2"></i>
                        Exámenes Registrados
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($statusFilter)): ?>
                        <div class="alert alert-info rounded-0 mb-0 px-4 py-3">
                            <strong>Filtro activo:</strong> mostrando exámenes con estado
                            <span class="fw-semibold"><?= htmlspecialchars($statusDisplay ?? $statusFilter) ?></span>.
                            <a href="index.php?c=exam&a=list" class="alert-link">Ver todos</a>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold">#</th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-person-vcard me-1"></i>Identificación
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-building me-1"></i>Empresa
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-person me-1"></i>Nombre
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-telephone me-1"></i>Teléfono
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-calendar-event me-1"></i>Nacimiento
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-gender-ambiguous me-1"></i>Género
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-calendar-check me-1"></i>Examen
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-check-circle me-1"></i>Resultado
                                    </th>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-hash me-1"></i>Orden
                                    </th>
                                    <th class="border-0 fw-semibold text-center" style="width: 180px;">
                                        <i class="bi bi-gear me-1"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($exams)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">No hay exámenes para el filtro seleccionado.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($exams as $exam): ?>
                                        <tr>
                                            <td class="fw-semibold text-muted">#<?= $exam['id'] ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?= htmlspecialchars($exam['document_number'] ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td class="fw-semibold">
                                                <?= htmlspecialchars($exam['company_name'] ?? 'N/A') ?>
                                            </td>
                                            <td class="fw-semibold">
                                                <?= htmlspecialchars($exam['candidate_name']) ?>
                                            </td>
                                        <td>
                                            <i class="bi bi-telephone text-muted me-1"></i>
                                            <?= htmlspecialchars($exam['phone'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar-event text-muted me-1"></i>
                                            <?= htmlspecialchars($exam['birth_date'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $genderIcon = ($exam['gender'] === 'M') ? 'bi-gender-male text-primary' : 'bi-gender-female text-danger';
                                            ?>
                                            <i class="bi <?= $genderIcon ?> me-1"></i>
                                            <?= htmlspecialchars($exam['gender'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar-check text-muted me-1"></i>
                                            <?= htmlspecialchars($exam['exam_date'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusMap = [
                                                'FINALIZADO' => ['Apto', 'success'],
                                                'RECHAZADO' => ['No Apto', 'danger'],
                                                'EN_CURSO' => ['Aplazado', 'warning'],
                                                'SIN_RESULTADO' => ['Sin resultado', 'info'],
                                                'PENDIENTE' => ['Pendiente', 'secondary']
                                            ];
                                            $statusInfo = $statusMap[$exam['status']] ?? ['Pendiente', 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $statusInfo[1] ?> text-white">
                                                <?= htmlspecialchars($statusInfo[0]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white">
                                                <?= htmlspecialchars($exam['order_number'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-primary" href="index.php?c=exam&a=view&id=<?= $exam['id'] ?>"
                                                   data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                                <a class="btn btn-sm btn-warning" href="index.php?c=exam&a=edit&id=<?= $exam['id'] ?>"
                                                   data-bs-toggle="tooltip" title="Editar examen">
                                                    <i class="bi bi-pencil me-1"></i>Editar
                                                </a>
                                                <a class="btn btn-sm btn-danger" href="index.php?c=exam&a=delete&id=<?= $exam['id'] ?>"
                                                   onclick="return confirm('¿Está seguro de eliminar el examen de <?= htmlspecialchars($exam['candidate_name']) ?>? Esta acción no se puede deshacer.');"
                                                   data-bs-toggle="tooltip" title="Eliminar examen">
                                                    <i class="bi bi-trash me-1"></i>Eliminar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>