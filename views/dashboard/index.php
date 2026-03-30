<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="bi bi-shield-check me-3"></i>Atlas Seguridad
                </h1>
                <p class="lead text-muted mb-4">Sistema de Gestión y Clasificación de Exámenes Médicos</p>
                <hr class="w-25 mx-auto mb-4">
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-lightning-charge me-2 text-warning"></i>
                        Acciones Rápidas
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=exam&a=add" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-plus-circle fs-1 mb-2"></i>
                                <span class="fw-semibold">Nuevo Examen</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=exam&a=list" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-list-ul fs-1 mb-2"></i>
                                <span class="fw-semibold">Ver Exámenes</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=company&a=list" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-building fs-1 mb-2"></i>
                                <span class="fw-semibold">Empresas</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=dashboard&a=users" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <span class="fw-semibold">Usuarios</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                        Exportar Datos
                    </h5>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="orderNumber" class="form-label fw-semibold">Número de Orden (Opcional)</label>
                            <input type="text" class="form-control" id="orderNumber" name="order" placeholder="Ej: ORD-001" form="exportForm">
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex gap-2">
                                <button type="submit" form="exportForm" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Exportar Excel
                                </button>
                                <a href="index.php?c=exam&a=exportCandidates" class="btn btn-outline-success">
                                    <i class="bi bi-download me-2"></i>Exportar Todo
                                </a>
                            </div>
                        </div>
                    </div>
                    <form id="exportForm" method="GET" action="index.php" class="d-none">
                        <input type="hidden" name="c" value="exam">
                        <input type="hidden" name="a" value="exportCandidates">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-primary mb-3">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1">0</h3>
                    <p class="text-muted mb-0">Empresas Registradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-success mb-3">
                        <i class="bi bi-clipboard-check fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1">0</h3>
                    <p class="text-muted mb-0">Exámenes Realizados</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-info mb-3">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1">0</h3>
                    <p class="text-muted mb-0">Usuarios Activos</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>