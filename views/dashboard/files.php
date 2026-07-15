<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="bi bi-folder-fill me-2 text-primary"></i>
                    Archivos Clasificados
                </h2>
                <a href="index.php?c=dashboard&a=index" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>
            <p class="text-white-50 mt-2">Explora los archivos generados por empresa</p>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-dark border-dark text-white"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar empresa, carpeta o archivo...">
                <button type="button" id="clearSearchBtn" class="btn btn-outline-secondary" title="Borrar búsqueda">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div id="noResultsMessage" class="alert alert-warning mt-3 d-none" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>No se encontraron resultados para la búsqueda.
            </div>
        </div>
        <div class="col-md-6 text-end">
            <form method="post" action="index.php?c=dashboard&a=cleanClassifiedCompanies" style="display:inline; margin-right: .5rem;">
                <button type="submit" class="btn btn-info me-2">
                    <i class="bi bi-brush me-1"></i>Limpiar nombres
                </button>
            </form>
            <form method="post" action="index.php?c=dashboard&a=deleteAllClassifiedFiles" style="display:inline; margin-right: .5rem;" onsubmit="return confirm('¿Eliminar todos los archivos clasificados? Esta acción no se puede deshacer.');">
                <button type="submit" class="btn btn-danger me-2">
                    <i class="bi bi-trash-fill me-1"></i>Eliminar Todo
                </button>
            </form>
            <button type="button" id="selectAllBtn" class="btn btn-secondary me-2">
                <i class="bi bi-check-square me-1"></i>Seleccionar Todo
            </button>
            <button type="button" id="deleteSelectedBtn" class="btn btn-danger" disabled>
                <i class="bi bi-trash me-1"></i>Eliminar Seleccionados
            </button>
        </div>
    </div>

    <form id="deleteForm" method="POST" action="index.php?c=dashboard&a=deleteFiles">

    <?php if (empty($companies)): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No se encontraron archivos clasificados. Sube un archivo Excel para generar reportes.
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php
            $totalCompanies = count($companies);
            $totalFiles = 0;
            foreach ($companies as $companyData) {
                foreach ($companyData['company_dirs'] as $companyDirData) {
                    foreach ($companyDirData['folders'] as $folderData) {
                        $totalFiles += count($folderData['files']);
                    }
                }
            }
        ?>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase text-white-50">Empresas</small>
                                <h3 class="mb-0"><?php echo $totalCompanies; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white rounded-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="bi bi-file-earmark-text-fill fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase text-white-50">Archivos</small>
                                <h3 class="mb-0"><?php echo $totalFiles; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-3 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="bi bi-award fs-4"></i>
                            </div>
                            <div>
                                <small class="text-uppercase text-white-50">Presentación</small>
                                <p class="mb-0 text-white-50">Vista en tarjetas más clara y ordenada.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion" id="companiesAccordion">
            <?php $index = 0; foreach ($companies as $companyKey => $companyData): ?>
                <?php
                    $companyFiles = 0;
                    foreach ($companyData['company_dirs'] as $companyDirData) {
                        foreach ($companyDirData['folders'] as $folderData) {
                            $companyFiles += count($folderData['files']);
                        }
                    }
                ?>
                <div class="accordion-item company-card" data-company="<?php echo htmlspecialchars(strtolower($companyData['display_name'])); ?>">
                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-building-fill text-primary fs-3 me-3"></i>
                                    <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($companyData['display_name']); ?></div>
                                        <small class="text-white-50"><?php echo count($companyData['company_dirs']); ?> carpeta<?php echo count($companyData['company_dirs']) !== 1 ? 's' : ''; ?></small>
                                    </div>
                                </div>
                                <span class="badge bg-secondary py-2 px-3"><?php echo $companyFiles; ?> archivo<?php echo $companyFiles !== 1 ? 's' : ''; ?></span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#companiesAccordion">
                        <div class="accordion-body">
                            <?php 
                                $hasFolders = false;
                                foreach ($companyData['company_dirs'] as $companyDirData) {
                                    if (!empty($companyDirData['folders'])) {
                                        $hasFolders = true;
                                        break;
                                    }
                                }
                            ?>
                            <?php if (!$hasFolders): ?>
                                <div class="alert alert-secondary mb-0">No hay carpetas en esta empresa.</div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($companyData['company_dirs'] as $companyDir => $companyDirData): ?>
                                        <?php foreach ($companyDirData['folders'] as $folderName => $folderData): ?>
                                            <?php $prettyFolder = trim(preg_replace('/\s+/', ' ', str_replace('_', ' ', $folderName))); ?>
                                            <div class="col-12 col-md-6">
                                                <div class="card shadow-sm border-0 h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <div>
                                                                <h6 class="mb-1"><?php echo htmlspecialchars($prettyFolder); ?></h6>
                                                                <small class="text-white-50"><?php echo count($folderData['files']); ?> archivo<?php echo count($folderData['files']) !== 1 ? 's' : ''; ?></small>
                                                            </div>
                                                            <i class="bi bi-folder-fill text-warning fs-4"></i>
                                                        </div>
                                                        <?php if (empty($folderData['files'])): ?>
                                                            <p class="text-white-50 mb-0">Sin archivos</p>
                                                        <?php else: ?>
                                                            <div class="list-group list-group-flush">
                                                                <?php foreach ($folderData['files'] as $file): ?>
                                                                    <div class="list-group-item px-0 border-0 py-2">
                                                                        <div class="row align-items-center gx-2">
                                                                            <div class="col">
                                                                                <div class="fw-semibold text-truncate"><?php echo htmlspecialchars($file['name']); ?></div>
                                                                                <small class="text-white-50 d-block">
                                                                                    <?php 
                                                                                    $size = $file['size'];
                                                                                    if ($size >= 1048576) {
                                                                                        echo round($size / 1048576, 2) . ' MB';
                                                                                    } elseif ($size >= 1024) {
                                                                                        echo round($size / 1024, 2) . ' KB';
                                                                                    } else {
                                                                                        echo $size . ' bytes';
                                                                                    }
                                                                                    ?> • Modificado: <?php echo $file['modified']; ?>
                                                                                </small>
                                                                                <?php if (!empty($file['order_number']) || !empty($file['order_capacity'])): ?>
                                                                                    <small class="text-white-50 d-block mt-1">
                                                                                        <?php if (!empty($file['order_number'])): ?>
                                                                                            <span class="badge bg-info text-dark me-1">Orden: <?php echo htmlspecialchars($file['order_number']); ?></span>
                                                                                        <?php endif; ?>
                                                                                        <?php if (!empty($file['order_capacity'])): ?>
                                                                                            <span class="badge bg-success text-dark">Cupos: <?php echo htmlspecialchars($file['order_capacity']); ?></span>
                                                                                        <?php endif; ?>
                                                                                    </small>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="col-auto text-end">
                                                                                <a href="index.php?c=dashboard&a=download&company=<?php echo urlencode($companyDir); ?>&folder=<?php echo urlencode($folderName); ?>&file=<?php echo urlencode($file['name']); ?>" class="btn btn-sm btn-primary mb-2">
                                                                                    <i class="bi bi-download me-1"></i>Descargar
                                                                                </a>
                                                                                <button type="button" class="btn btn-sm btn-secondary mb-2" onclick="toggleMetadataForm(this, '<?php echo htmlspecialchars($companyDir); ?>', '<?php echo htmlspecialchars($folderName); ?>', '<?php echo htmlspecialchars($file['name']); ?>')">
                                                                                    <i class="bi bi-pencil-square me-1"></i>Editar metadata
                                                                                </button>
                                                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('<?php echo htmlspecialchars($companyDir); ?>', '<?php echo htmlspecialchars($folderName); ?>', '<?php echo htmlspecialchars($file['name']); ?>')">
                                                                                    <i class="bi bi-trash me-1"></i>Eliminar
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php $index++; endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que quieres eliminar el archivo <strong id="fileName"></strong>? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a id="deleteLink" href="#" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(company, folder, file) {
    document.getElementById('fileName').textContent = file;
    document.getElementById('deleteLink').href = `index.php?c=dashboard&a=deleteFile&company=${encodeURIComponent(company)}&folder=${encodeURIComponent(folder)}&file=${encodeURIComponent(file)}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleMetadataForm(button, company, folder, file) {
    let formId = `metadata-form-${company}-${folder}-${file}`.replace(/[^a-zA-Z0-9-_]/g, '_');
    let form = document.getElementById(formId);
    if (!form) {
        form = document.createElement('form');
        form.id = formId;
        form.method = 'POST';
        form.action = 'index.php?c=dashboard&a=updateClassifiedFileMetadata';
        form.className = 'metadata-form';
        form.innerHTML = `
            <input type="hidden" name="company" value="${company}">
            <input type="hidden" name="folder" value="${folder}">
            <input type="hidden" name="file" value="${file}">
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Número de Orden</label>
                    <input type="text" name="order_number" class="form-control" placeholder="Número de orden">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cupos</label>
                    <input type="text" name="order_capacity" class="form-control" placeholder="Cupos disponibles">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success btn-sm">Guardar metadata</button>
                </div>
            </div>
        `;
        let container = document.createElement('div');
        container.className = 'metadata-form-container mt-3';
        container.appendChild(form);
        const fileCard = button.closest('.list-group-item');
        fileCard.appendChild(container);
    } else {
        form.remove();
    }
}

// Función de búsqueda
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearchBtn');
const noResultsMessage = document.getElementById('noResultsMessage');

function filterCompanyCards() {
    const searchTerm = searchInput.value.trim().toLowerCase();
    const companyCards = document.querySelectorAll('.company-card');
    let visibleCount = 0;

    companyCards.forEach(card => {
        const companyName = card.getAttribute('data-company');
        const content = card.textContent.toLowerCase();
        const isMatch = searchTerm === '' || companyName.includes(searchTerm) || content.includes(searchTerm);

        card.style.display = isMatch ? '' : 'none';
        if (isMatch) visibleCount++;
    });

    noResultsMessage.classList.toggle('d-none', visibleCount > 0);
}

searchInput.addEventListener('input', filterCompanyCards);
clearSearchBtn.addEventListener('click', function() {
    searchInput.value = '';
    filterCompanyCards();
    searchInput.focus();
});

// Seleccionar/deseleccionar todo
document.getElementById('selectAllBtn').addEventListener('click', function() {
    const allCheckboxes = document.querySelectorAll('.file-checkbox, .company-checkbox');
    const isChecked = this.textContent.includes('Seleccionar');
    
    allCheckboxes.forEach(cb => {
        cb.checked = isChecked;
    });
    
    this.innerHTML = isChecked ? '<i class="bi bi-square me-1"></i>Deseleccionar Todo' : '<i class="bi bi-check-square me-1"></i>Seleccionar Todo';
    updateDeleteButton();
});

// Actualizar estado del botón eliminar
function updateDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.file-checkbox:checked, .company-checkbox:checked');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    deleteBtn.disabled = checkedBoxes.length === 0;
}

// Manejar checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('file-checkbox') || e.target.classList.contains('company-checkbox')) {
        updateDeleteButton();
    }
    
    // Si se marca una empresa, marcar todos sus archivos
    if (e.target.classList.contains('company-checkbox')) {
        const companyItem = e.target.closest('.accordion-item');
        const fileCheckboxes = companyItem.querySelectorAll('.file-checkbox');
        fileCheckboxes.forEach(cb => cb.checked = e.target.checked);
    }
});

// Eliminar seleccionados
document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.file-checkbox:checked, .company-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    let message = `¿Estás seguro de que quieres eliminar ${checkedBoxes.length} elemento(s)? Esta acción no se puede deshacer.`;
    if (confirm(message)) {
        document.getElementById('deleteForm').submit();
    }
});

updateDeleteButton();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>