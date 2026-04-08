<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold text-primary">Detalle del Examen</h1>
            <p class="text-muted">Revisa la información completa del paciente y su orden.</p>
            <a class="btn btn-outline-secondary" href="index.php?c=exam&a=list">← Volver al listado</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary">ID del examen</dt>
                        <dd class="col-sm-8">#<?= intval($exam['id']) ?></dd>

                        <dt class="col-sm-4 text-secondary">Número de orden</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['order_number'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Nombre del candidato</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['candidate_name'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Documento</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['document_number'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Teléfono</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['phone'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Género</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['gender'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Fecha de nacimiento</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['birth_date'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Fecha de examen</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['exam_date'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Estado</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['status'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Creado</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['created_at'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-4 text-secondary">Última actualización</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($exam['updated_at'] ?? 'N/A') ?></dd>
                    </dl>
                    <div class="mt-3">
                        <a class="btn btn-warning" href="index.php?c=exam&a=edit&id=<?= intval($exam['id']) ?>">Editar</a>
                        <a class="btn btn-danger" href="index.php?c=exam&a=delete&id=<?= intval($exam['id']) ?>" onclick="return confirm('¿Eliminar paciente?');">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>