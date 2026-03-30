<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold text-primary mb-1">
                        <i class="bi bi-building me-2"></i>Gestión de Empresas
                    </h1>
                    <p class="text-muted mb-0">Administra las empresas y sus exámenes médicos</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="index.php?c=dashboard&a=index" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                    <a href="index.php?c=company&a=add" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Empresa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="c" value="company">
                        <input type="hidden" name="a" value="list">

                        <div class="col-md-8">
                            <label for="searchInput" class="form-label fw-semibold">
                                <i class="bi bi-search me-1"></i>Buscar Empresa
                            </label>
                            <input type="text" class="form-control" id="searchInput" name="search"
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                   placeholder="Ingresa el nombre de la empresa...">
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Buscar
                                </button>
                                <?php if (!empty($_GET['search'])): ?>
                                    <a href="index.php?c=company&a=list" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <?php if (!empty($companies)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-list-ul me-2"></i>
                            Empresas Registradas
                            <span class="badge bg-primary ms-2"><?= count($companies) ?></span>
                        </h5>
                        <small class="text-muted">
                            <?php if (!empty($_GET['search'])): ?>
                                Resultados para: "<?= htmlspecialchars($_GET['search']) ?>"
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold">
                                        <i class="bi bi-building me-1"></i>Nombre de Empresa
                                    </th>
                                    <th class="border-0 fw-semibold text-center">
                                        <i class="bi bi-clipboard-data me-1"></i>Exámenes
                                    </th>
                                    <th class="border-0 fw-semibold text-center" style="width: 200px;">
                                        <i class="bi bi-gear me-1"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($companies as $company): ?>
                                    <tr>
                                        <td class="fw-semibold">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold company-name"><?= htmlspecialchars($company['name']) ?></div>
                                                    <small class="text-muted">ID: <?= $company['id'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6 px-3 py-2">
                                                <i class="bi bi-clipboard-check me-1"></i>
                                                <?= $company['exam_count'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-outline-primary" href="index.php?c=company&a=view&id=<?= $company['id'] ?>"
                                                   data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-warning" href="index.php?c=company&a=edit&id=<?= $company['id'] ?>"
                                                   data-bs-toggle="tooltip" title="Editar empresa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-danger" href="index.php?c=company&a=delete&id=<?= $company['id'] ?>"
                                                   onclick="return confirm('¿Está seguro de eliminar esta empresa?');"
                                                   data-bs-toggle="tooltip" title="Eliminar empresa">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Empty State -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="text-muted mb-4">
                        <i class="bi bi-building display-1 opacity-25"></i>
                    </div>
                    <h4 class="text-muted mb-3">
                        <?php if (!empty($_GET['search'])): ?>
                            No se encontraron empresas
                        <?php else: ?>
                            No hay empresas registradas
                        <?php endif; ?>
                    </h4>
                    <p class="text-muted mb-4">
                        <?php if (!empty($_GET['search'])): ?>
                            No se encontraron empresas que coincidan con "<strong><?= htmlspecialchars($_GET['search']) ?></strong>".
                        <?php else: ?>
                            Comienza registrando tu primera empresa para gestionar exámenes médicos.
                        <?php endif; ?>
                    </p>
                    <?php if (empty($_GET['search'])): ?>
                        <a href="index.php?c=company&a=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Agregar Primera Empresa
                        </a>
                    <?php else: ?>
                        <a href="index.php?c=company&a=list" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Volver al listado
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
