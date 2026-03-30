<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-primary mb-2">
                        <i class="bi bi-diagram-3 me-3"></i>Clasificación por Empresas
                    </h1>
                    <p class="lead mb-0" style="color:#fff;">Carga un archivo Excel para registrar y clasificar candidatos por empresa</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Cargar Archivo Excel
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-x-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="excelFile" class="form-label fw-semibold">
                                <i class="bi bi-upload me-2"></i>Selecciona archivo Excel
                            </label>
                            <input type="file" class="form-control form-control-lg" id="excelFile" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted d-block mt-2">
                                Formatos soportados: Excel (.xlsx, .xls) o CSV. 
                                <br>El archivo debe contener columnas: Empresa, Nombre, Apellido (u otro identificador)
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="companyColumn" class="form-label fw-semibold">
                                <i class="bi bi-building me-2"></i>Columna de Empresa
                            </label>
                            <input type="text" class="form-control" id="companyColumn" name="company_column" placeholder="Ej: A, Empresa, Company" value="A" required>
                            <small class="text-muted">Número o nombre de la columna que contiene el nombre de la empresa</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-cloud-upload me-2"></i>Procesar Archivo
                            </button>
                            <a href="index.php?c=dashboard&a=index" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body p-4">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Instrucciones
                    </h6>
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Descarga el archivo Excel con los datos de candidatos</li>
                        <li class="mb-2">Asegúrate de que contenga una columna con el nombre de la empresa</li>
                        <li class="mb-2">Sube el archivo para procesar automáticamente</li>
                        <li class="mb-2">El sistema contará candidatos por empresa</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <?php if (isset($results) && !empty($results)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i>Resultados del Procesamiento
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">
                                        <i class="bi bi-building me-2"></i>Empresa
                                    </th>
                                    <th class="fw-semibold text-center">
                                        <i class="bi bi-people me-2"></i>Cantidad de Personas
                                    </th>
                                    <th class="fw-semibold text-center">
                                        <i class="bi bi-percent me-2"></i>Porcentaje
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $company => $count): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($company); ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-primary bg-primary"><?php echo $count; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $total = array_sum($results);
                                                $percentage = ($total > 0) ? round(($count / $total) * 100, 2) : 0;
                                                echo $percentage . '%';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-light fw-bold">
                                    <td>TOTAL</td>
                                    <td class="text-center">
                                        <span class="badge badge-success bg-success"><?php echo array_sum($results); ?></span>
                                    </td>
                                    <td class="text-center">100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
