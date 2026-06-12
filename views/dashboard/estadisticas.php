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
    .estadisticas-white-text .list-group {
        gap: 0.55rem;
        margin-top: 0.4rem;
    }
    .estadisticas-white-text .list-group-item {
        background: transparent;
        border: 1px solid transparent;
        border-radius: 16px;
        margin-bottom: 0;
        padding: 0.85rem 1.2rem;
        transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        text-decoration: none;
        width: calc(100% + 3px);
        margin-left: -1.5px;
    }
    .estadisticas-white-text .list-group-item:hover,
    .estadisticas-white-text .list-group-item-action:hover {
        background-color: rgba(255,255,255,0.10);
        border-color: rgba(255,255,255,0.16);
        text-decoration: none;
        transform: translateX(0);
    }
    .estadisticas-white-text .list-group-item h6 {
        color: #ffffff;
        margin-bottom: 0.15rem;
        font-size: 0.98rem;
        line-height: 1.2;
    }
    .estadisticas-white-text .list-group-item small {
        color: rgba(255,255,255,0.62);
        line-height: 1.25;
    }
    .estadisticas-white-text .list-group-item .fw-semibold {
        color: #ffffff;
        font-size: 1rem;
    }
    .estadisticas-white-text .list-group-item .d-flex {
        width: 100%;
    }
    .estadisticas-white-text .list-group-item:last-child {
        margin-bottom: 0;
    }
    .estadisticas-white-text .card {
        background: rgba(15, 23, 42, 0.94);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: none;
    }
    .estadisticas-white-text .card:hover {
        transform: translateY(0);
        box-shadow: none;
    }
    .estadisticas-white-text .card-body {
        padding: 1.4rem;
    }
    .estadisticas-white-text .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }
    .estadisticas-white-text .card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-radius: 24px;
        background: rgba(15, 23, 42, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }
    .estadisticas-white-text .card-body {
        border-radius: 24px;
        padding: 1.5rem;
    }
    .estadisticas-white-text .card-title {
        color: #ffffff;
    }
    .estadisticas-white-text .table-responsive {
        background: rgba(255,255,255,0.02);
    }
    .estadisticas-white-text .table thead th {
        color: rgba(255, 255, 255, 0.78);
    }
    .estadisticas-white-text .card .badge {
        opacity: 0.95;
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
                    <h2 class="fw-bold mb-1" id="totalExamsCount"><?php echo number_format(intval($totalExams)); ?></h2>
                    <p class="text-muted mb-0">Exámenes registrados</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="index.php?c=exam&a=list&status=FINALIZADO" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-success fs-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <span class="badge bg-light text-success">Aptos</span>
                        </div>
                        <h2 class="fw-bold mb-1" id="aptosCount"><?php echo number_format(intval($resultSummary['aptos'] ?? 0)); ?></h2>
                        <p class="text-muted mb-0">Candidatos aptos</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6">
            <a href="index.php?c=exam&a=list&status=RECHAZADO" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="text-danger fs-3">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <span class="badge bg-light text-danger">No aptos</span>
                        </div>
                        <h2 class="fw-bold mb-1" id="noAptosCount"><?php echo number_format(intval($resultSummary['no_aptos'] ?? 0)); ?></h2>
                        <p class="text-muted mb-0">Candidatos no aptos</p>
                    </div>
                </div>
            </a>
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
                    <h2 class="fw-bold mb-1" id="registeredCompaniesCount"><?php echo number_format(intval($registeredCompanies)); ?></h2>
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
                    <h2 class="fw-bold mb-1" id="activeCompaniesCount"><?php echo number_format(intval($activeCompanies)); ?></h2>
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
                                    <?php $statusValue = strtoupper(trim($statusItem['status'])); ?>
                                    <a href="index.php?c=exam&a=list&status=<?php echo urlencode($statusValue); ?>" class="list-group-item list-group-item-action border-0 px-0 py-3 text-white" data-status="<?php echo htmlspecialchars($statusValue); ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($statusItem['label']); ?></h6>
                                                <small class="text-muted">Proporción del total</small>
                                            </div>
                                            <span class="fw-semibold" id="statusCount-<?php echo htmlspecialchars($statusValue); ?>"><?php echo number_format(intval($statusItem['total'])); ?></span>
                                        </div>
                                    </a>
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
                            <tbody id="topCompaniesBody">
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
                    '<?php echo htmlspecialchars($item['label']); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [<?php echo implode(',', array_map('intval', array_column($statusSummary, 'total'))); ?>],
                backgroundColor: ['#0d6efd', '#dc3545', '#6c757d', '#ffc107', '#198754'],
                hoverBackgroundColor: ['#0b5ed7', '#c82333', '#5c636a', '#f7c948', '#157347'],
                borderWidth: 1,
                borderColor: '#f8f9fa'
            }]
        };

        const statusChartObject = new Chart(statusChart, {
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

        const formatNumber = value => Number(value).toLocaleString('es-CO');

        const updateDashboard = (data) => {
            document.getElementById('totalExamsCount').textContent = formatNumber(data.totalExams);
            document.getElementById('aptosCount').textContent = formatNumber(data.resultSummary.aptos);
            document.getElementById('noAptosCount').textContent = formatNumber(data.resultSummary.no_aptos);
            const registeredCompaniesCount = document.getElementById('registeredCompaniesCount');
            const activeCompaniesCount = document.getElementById('activeCompaniesCount');
            if (registeredCompaniesCount) {
                registeredCompaniesCount.textContent = formatNumber(data.registeredCompanies);
            }
            if (activeCompaniesCount) {
                activeCompaniesCount.textContent = formatNumber(data.activeCompanies);
            }

            const labels = data.statusSummary.map(item => item.label);
            const totals = data.statusSummary.map(item => item.total);
            statusChartObject.data.labels = labels;
            statusChartObject.data.datasets[0].data = totals;
            statusChartObject.update();

            data.statusSummary.forEach(item => {
                const element = document.getElementById('statusCount-' + item.status);
                if (element) {
                    element.textContent = formatNumber(item.total);
                }
            });

            const topCompaniesBody = document.getElementById('topCompaniesBody');
            if (topCompaniesBody) {
                topCompaniesBody.innerHTML = '';
                if (Array.isArray(data.topCompanies) && data.topCompanies.length > 0) {
                    data.topCompanies.forEach((company, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="fw-bold">${index + 1}</td>
                            <td>${company.company_name ? company.company_name.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</td>
                            <td class="text-end fw-semibold">${formatNumber(company.exams_count)}</td>
                        `;
                        topCompaniesBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="3" class="text-center text-muted">No hay datos disponibles.</td>';
                    topCompaniesBody.appendChild(row);
                }
            }
        };

        const refreshData = async () => {
            try {
                const response = await fetch('index.php?c=dashboard&a=estadisticasData');
                if (!response.ok) {
                    return;
                }
                const json = await response.json();
                updateDashboard(json);
            } catch (error) {
                console.error('Error actualizando estadísticas:', error);
            }
        };

        refreshData();
        setInterval(refreshData, 15000);
    })();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>