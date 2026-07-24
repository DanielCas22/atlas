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
                <a href="index.php?c=dashboard&a=index" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-house-fill me-2"></i>Ir al Inicio
                </a>
            </div>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-header bg-black text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>Cargar Archivo Excel
                    </h5>
                </div>
                <div class="card-body p-4 text-white">
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

                    <?php if (isset($syncSummary)): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            Empresas sincronizadas: <?php echo intval($syncSummary['added']); ?> añadidas, <?php echo intval($syncSummary['existing']); ?> ya existentes<?php echo !empty($syncSummary['failed']) ? ', ' . intval($syncSummary['failed']) . ' con error' : ''; ?>.
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
                                Formatos soportados: Excel (.xlsx, .xls) o CSV
                            </small>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-semibold mb-3">Especifica las columnas del archivo:</h6>

                        <div class="mb-4">
                            <label for="companyColumn" class="form-label fw-semibold">
                                <i class="bi bi-building me-2"></i>Columna de Empresa *
                            </label>
                            <input type="text" class="form-control" id="companyColumn" name="company_column" placeholder="Ej: AC, Empresa, Company" value="AC" required>
                        </div>

                        <div class="mb-4">
                            <label for="nameColumn" class="form-label fw-semibold">
                                <i class="bi bi-person me-2"></i>Columna de Nombre *
                            </label>
                            <input type="text" class="form-control" id="nameColumn" name="name_column" placeholder="Ej: E, Nombre" value="E">
                        </div>

                        <div class="mb-4">
                            <label for="documentColumn" class="form-label fw-semibold">
                                <i class="bi bi-card-text me-2"></i>Columna de Documento
                            </label>
                            <input type="text" class="form-control" id="documentColumn" name="document_column" placeholder="Ej: D, Cedula" value="D">
                        </div>

                        <div class="mb-4">
                            <label for="phoneColumn" class="form-label fw-semibold">
                                <i class="bi bi-telephone me-2"></i>Columna de Teléfono
                            </label>
                            <input type="text" class="form-control" id="phoneColumn" name="phone_column" placeholder="Ej: H, Telefono" value="H">
                        </div>

                        <div class="mb-4">
                            <label for="genderColumn" class="form-label fw-semibold">
                                <i class="bi bi-person-check me-2"></i>Columna de Género
                            </label>
                            <input type="text" class="form-control" id="genderColumn" name="gender_column" placeholder="Ej: I, Genero" value="I">
                        </div>

                        <div class="mb-4">
                            <label for="birthColumn" class="form-label fw-semibold">
                                <i class="bi bi-calendar-event me-2"></i>Columna de Fecha Nacimiento
                            </label>
                            <input type="text" class="form-control" id="birthColumn" name="birth_column" placeholder="Ej: K, Fecha_Nacimiento" value="K">
                        </div>

                        <div class="mb-4">
                            <label for="examDateColumn" class="form-label fw-semibold">
                                <i class="bi bi-calendar2-event me-2"></i>Columna de Fecha de Examen
                            </label>
                            <input type="text" class="form-control" id="examDateColumn" name="exam_date_column" placeholder="Ej: X, Fecha_Examen" value="X">
                        </div>

                        <div class="mb-4">
                            <label for="resultColumn" class="form-label fw-semibold">
                                <i class="bi bi-check2-circle me-2"></i>Columna de Resultado
                            </label>
                            <input type="text" class="form-control" id="resultColumn" name="result_column" placeholder="Ej: V, Resultado, Estado" value="V">
                        </div>


                        <div class="mb-4" id="customColumnsContainer"></div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-info" id="addColumnBtn">
                                <i class="bi bi-plus-circle me-1"></i>Agregar Columna Nueva
                            </button>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-cloud-upload me-1"></i>Procesar Archivo
                        </button>
                        <a href="index.php?c=dashboard&a=index" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                    </form>

                    <form method="post" action="index.php?c=dashboard&a=syncClassifiedCompanies" class="mt-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-arrow-repeat me-2"></i>Sincronizar empresas clasificadas a "Empresas"
                        </button>
                    </form>

                    <script>
                        let customColumnCount = 0;

                        document.getElementById('addColumnBtn').addEventListener('click', function(e) {
                            e.preventDefault();
                            customColumnCount++;
                            
                            const columnId = 'custom_' + customColumnCount;
                            const columnDiv = document.createElement('div');
                            columnDiv.className = 'mb-3 p-3 border rounded bg-light';
                            columnDiv.id = 'column-' + columnId;
                            
                            columnDiv.innerHTML = `
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">
                                            <i class="bi bi-columns me-2"></i>Columna Personalizada
                                        </label>
                                        <input type="text" class="form-control" name="custom_column_${customColumnCount}" placeholder="Ej: G, Columna_Personalizada">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-column-btn" data-column-id="${columnId}">
                                            <i class="bi bi-trash me-1"></i>Eliminar
                                        </button>
                                    </div>
                                </div>
                            `;
                            
                            document.getElementById('customColumnsContainer').appendChild(columnDiv);
                            
                            columnDiv.querySelector('.remove-column-btn').addEventListener('click', function() {
                                columnDiv.remove();
                            });
                        });
                    </script>
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
                        <li class="mb-2">Prepara tu archivo Excel con columnas de datos de candidatos</li>
                        <li class="mb-2">Especifica qué columna contiene el nombre de la empresa</li>
                        <li class="mb-2">Especifica las columnas de datos personales (nombre, documento, etc.)</li>
                        <li class="mb-2">Sube el archivo para procesar automáticamente</li>
                        <li class="mb-2">El sistema clasificará pacientes por empresa</li>
                        <li class="mb-2">Crea carpetas con reportes en la carpeta "REPORTE GUARDA"</li>
                        <li class="mb-2">Los reportes llevan la fecha del procesamiento</li>
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
                                            <span class="badge badge-primary bg-primary text-white"><?php echo $count; ?></span>
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
                                        <span class="badge badge-success bg-success text-white"><?php echo array_sum($results); ?></span>
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
