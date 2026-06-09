<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .management-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        background-color: #1f355d !important;
        border: 1px solid #182b4a !important;
        color: #ffffff !important;
        text-decoration: none !important;
        border-radius: 1rem !important;
        box-shadow: 0 12px 30px rgba(16, 28, 71, 0.18);
        transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .management-action:hover,
    .management-action:focus {
        background-color: #273f70 !important;
        border-color: #20345d !important;
        transform: translateY(-2px);
        box-shadow: 0 18px 35px rgba(16, 28, 71, 0.24);
        color: #ffffff !important;
    }
    .management-action .bi {
        color: #ffffff !important;
    }
    .management-action span,
    .management-action .fw-semibold {
        color: #ffffff !important;
    }
    .management-action.btn {
        padding: 1.75rem 1rem !important;
    }
    .management-action:active {
        transform: translateY(0);
        box-shadow: 0 10px 20px rgba(16, 28, 71, 0.18);
    }
</style>

<div class="container-fluid py-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="bi bi-shield-check me-3"></i>Atlas Seguridad
                </h1>
                <p class="lead fw-semibold mb-4 text-white">Atlas - Gestión y Clasificación de Exámenes Médicos</p>
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
                            <a href="index.php?c=exam&a=add" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-plus-circle fs-1 mb-2"></i>
                                <span class="fw-semibold">Nuevo Examen</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=exam&a=list" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-list-ul fs-1 mb-2"></i>
                                <span class="fw-semibold">Ver Exámenes</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=company&a=list" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-building fs-1 mb-2"></i>
                                <span class="fw-semibold">Empresas</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=dashboard&a=users" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <span class="fw-semibold">Usuarios</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=dashboard&a=clasificacion_empresas" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-diagram-3 fs-1 mb-2"></i>
                                <span class="fw-semibold">Clasificación por Empresas</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=dashboard&a=estadisticas" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-bar-chart-line fs-1 mb-2"></i>
                                <span class="fw-semibold">Datos y Estadísticas</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?c=dashboard&a=files" class="btn management-action w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4">
                                <i class="bi bi-folder-fill fs-1 mb-2"></i>
                                <span class="fw-semibold">Archivos Clasificados</span>
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
                            <div class="d-flex gap-2 flex-wrap">
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

    <!-- Dashboard Chart -->
    <style>
        .dashboard-summary-card {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            background-color: rgba(15, 23, 42, 0.98);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .dashboard-summary-card .card-body {
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-height: 420px;
        }
        .dashboard-summary-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }
        .dashboard-summary-header .title-block {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .dashboard-summary-chart {
            width: 100%;
            flex: 1;
            min-height: 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dashboard-summary-chart canvas {
            width: 100% !important;
            height: 100% !important;
            max-height: 320px;
        }
        .dashboard-summary-card .card-title {
            font-size: 1rem;
            margin-bottom: 0.15rem;
        }
        .dashboard-summary-card .text-muted {
            font-size: 0.82rem;
        }
        .dashboard-summary-card .badge {
            font-size: 0.75rem;
            padding: 0.45rem 0.9rem;
            white-space: nowrap;
        }
    </style>
    <div class="row mb-3 justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-6 col-sm-8">
            <div class="card shadow-sm border-0 dashboard-summary-card">
                <div class="card-body">
                    <div class="dashboard-summary-header">
                        <div class="title-block">
                            <h5 class="card-title">Resumen Visual</h5>
                            <p class="text-muted mb-0">Actividad del sistema y resultados clave.</p>
                        </div>
                        <span class="badge bg-outline-primary text-primary">Actualizado al momento</span>
                    </div>
                    <div class="dashboard-summary-chart">
                        <canvas id="dashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-primary mb-3">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?php echo intval($companyCount); ?></h3>
                    <p class="fw-semibold mb-0 text-white">Empresas Registradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-success mb-3">
                        <i class="bi bi-clipboard-check fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?php echo intval($examCount); ?></h3>
                    <p class="fw-semibold mb-0 text-white">Exámenes Realizados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-info mb-3">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?php echo intval($userCount); ?></h3>
                    <p class="fw-semibold mb-0 text-white">Usuarios Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="text-secondary mb-3">
                        <i class="bi bi-file-earmark-text fs-1"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?php echo intval($reportFiles); ?></h3>
                    <p class="fw-semibold mb-0 text-white">Reportes Generados</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const dashboardCtx = document.getElementById('dashboardChart');
    if (dashboardCtx) {
        const dashboardChart = new Chart(dashboardCtx, {
            type: 'doughnut',
            data: {
                labels: ['Empresas', 'Exámenes', 'Usuarios', 'Reportes'],
                datasets: [{
                    data: [<?php echo intval($companyCount); ?>, <?php echo intval($examCount); ?>, <?php echo intval($userCount); ?>, <?php echo intval($reportFiles); ?>],
                    backgroundColor: ['#0d6efd', '#198754', '#0dcaf0', '#ffc107'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2,
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            color: '#212529'
                        }
                    }
                },
                layout: {
                    padding: 16
                }
            }
        });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>