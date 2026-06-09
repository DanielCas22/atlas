<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .estadisticas-white-text {
        color: #ffffff;
    }
    .estadisticas-white-text h1,
    .estadisticas-white-text h2,
    .estadisticas-white-text h5,
    .estadisticas-white-text h6,
    .estadisticas-white-text p,
    .estadisticas-white-text span,
    .estadisticas-white-text th,
    .estadisticas-white-text td,
    .estadisticas-white-text .badge,
    .estadisticas-white-text .list-group-item {
        color: #ffffff !important;
    }
    .estadisticas-white-text .text-muted {
        color: rgba(255,255,255,0.72) !important;
    }
    .estadisticas-white-text .card {
        background-color: rgba(15, 23, 42, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .estadisticas-white-text .card-body {
        background: transparent;
    }
    .estadisticas-white-text .badge {
        background-color: rgba(255,255,255,0.08) !important;
        border: 1px solid rgba(255,255,255,0.14) !important;
        color: #ffffff !important;
    }
    .estadisticas-white-text .table-responsive {
        border-radius: 1rem;
        overflow: hidden;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
    }
    .estadisticas-white-text .table {
        color: #ffffff;
        margin-bottom: 0;
    }
    .estadisticas-white-text .table thead th {
        border-bottom: 1px solid rgba(255,255,255,0.16);
        background-color: rgba(255,255,255,0.05);
        color: #f8f9fa;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        font-size: 0.86rem;
    }
    .estadisticas-white-text .table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,0.08);
        transition: background-color 0.2s ease;
    }
    .estadisticas-white-text .table tbody tr:hover {
        background-color: rgba(255,255,255,0.06);
    }
    .estadisticas-white-text .table td,
    .estadisticas-white-text .table th {
        border-top: none;
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    .estadisticas-white-text .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(255,255,255,0.03);
    }
    .estadisticas-white-text .list-group-item {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .estadisticas-white-text .list-group-item:last-child {
        border-bottom: none;
    }
    .estadisticas-white-text .card-title {
        color: #ffffff;
    }
</style>

<div class="container-fluid py-4 estadisticas-white-text">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div>
                            <h1 class="display-6 fw-bold mb-2">Datos y Estadísticas</h1>
                            <p class="text-muted mb-0">Visión integral de los exámenes, la aptitud de candidatos y las empresas que más confían en nuestro servicio.</p>
                        </div>
                        <span class="badge bg-primary py-2 px-3">Actualizado en tiempo real</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-primary fs-3">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <span class="badge bg-light text-primary">Total</span>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo number_format(intval($totalExams)); ?></h2>
                    <p class="text-muted mb-0">Exámenes registrados</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-success fs-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <span class="badge bg-light text-success">Aptos</span>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo number_format(intval($resultSummary['aptos'] ?? 0)); ?></h2>
                    <p class="text-muted mb-0">Candidatos aptos</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-danger fs-3">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <span class="badge bg-light text-danger">No aptos</span>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo number_format(intval($resultSummary['no_aptos'] ?? 0)); ?></h2>
                    <p class="text-muted mb-0">Candidatos no aptos</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-warning fs-3">
                            <i class="bi bi-clock"></i>
                        </div>
                        <span class="badge bg-light text-warning">Pendientes</span>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo number_format(intval($resultSummary['pendientes'] ?? 0)); ?></h2>
                    <p class="text-muted mb-0">Exámenes pendientes o en proceso</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-info fs-3">
                            <i class="bi bi-building"></i>
                        </div>
                        <span class="badge bg-light text-info">Registradas</span>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo number_format(intval($registeredCompanies)); ?></h2>
                    <p class="text-muted mb-0">Empresas registradas en el sistema</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-success fs-3">
                            <i class="bi bi-building-check"></i>
                        </div>
                        <span class="badge bg-light text-success">Activas</span>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo number_format(intval($activeCompanies)); ?></h2>
                    <p class="text-muted mb-0">Empresas que usan el servicio de exámenes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="card-title mb-1">Distribución por estado</h5>
                            <p class="text-muted mb-0">Estado de los exámenes registrados en el sistema.</p>
                        </div>
                        <span class="text-muted small">Top 5 segmentos</span>
                    </div>
                    <div class="row gx-3">
                        <div class="col-md-6">
                            <canvas id="statusChart" height="300"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="list-group list-group-flush">
                                <?php foreach ($statusSummary as $statusItem): ?>
                                    <div class="list-group-item border-0 px-0 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1 text-capitalize"><?php echo htmlspecialchars(str_replace('_', ' ', strtolower($statusItem['status']))); ?></h6>
                                                <small class="text-muted">Proporción del total</small>
                                            </div>
                                            <span class="fw-semibold"><?php echo number_format(intval($statusItem['total'])); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="card-title mb-1">Podio de empresas</h5>
                            <p class="text-muted mb-0">Las empresas que más utilizan nuestros exámenes.</p>
                        </div>
                        <span class="badge bg-secondary">Top <?php echo count($topCompanies); ?></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-muted">Posición</th>
                                    <th class="text-muted">Empresa</th>
                                    <th class="text-end text-muted">Exámenes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCompanies as $index => $company): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($company['company_name']); ?></td>
                                        <td class="text-end fw-semibold"><?php echo number_format(intval($company['exams_count'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($topCompanies)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay datos disponibles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="card-title mb-1">Tipos de examen más solicitados</h5>
                            <p class="text-muted mb-0">Recuento por tipo de examen.</p>
                        </div>
                        <span class="badge bg-primary">Demanda</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipo de examen</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topExamTypes as $index => $examType): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($examType['exam_type_name']); ?></td>
                                        <td class="text-end fw-semibold"><?php echo number_format(intval($examType['exams_count'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($topExamTypes)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay tipos de examen registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    (function() {
        const statusChart = document.getElementById('statusChart');
        if (!statusChart) {
            return;
        }

        const statusData = {
            labels: [
                <?php foreach ($statusSummary as $item): ?>
                    '<?php echo htmlspecialchars(str_replace('_', ' ', ucfirst(strtolower($item['status'])))); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [<?php echo implode(',', array_map('intval', array_column($statusSummary, 'total'))); ?>],
                backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d'],
                hoverBackgroundColor: ['#0b5ed7', '#157347', '#f7c948', '#c82333', '#5c636a'],
                borderWidth: 1,
                borderColor: '#f8f9fa'
            }]
        };

        new Chart(statusChart, {
            type: 'doughnut',
            data: statusData,
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            boxWidth: 12
                        }
                    }
                },
                maintainAspectRatio: false,
                layout: {
                    padding: 12
                }
            }
        });
    })();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>