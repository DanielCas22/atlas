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
            <p class="text-muted mt-2">Explora los archivos generados por empresa</p>
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
    </form>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar empresa...">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" id="selectAllBtn" class="btn btn-outline-primary me-2">
                <i class="bi bi-check-square me-1"></i>Seleccionar Todo
            </button>
            <button type="button" id="deleteSelectedBtn" class="btn btn-outline-danger" disabled>
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
        <div class="accordion" id="companiesAccordion">
            <?php $index = 0; foreach ($companies as $companyKey => $companyData): ?>
                <div class="accordion-item company-card" data-company="<?php echo htmlspecialchars(strtolower($companyData['display_name'])); ?>">
                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                            <div class="d-flex align-items-center w-100">
                                <input type="checkbox" class="form-check-input me-3 company-checkbox" value="<?php echo htmlspecialchars($companyData['display_name']); ?>">
                                <i class="bi bi-building me-2"></i>
                                <?php echo htmlspecialchars($companyData['display_name']); ?>
                                <span class="badge bg-secondary ms-2">
                                <?php 
                                $totalFiles = 0;
                                foreach ($companyData['company_dirs'] as $companyDirData) {
                                    foreach ($companyDirData['folders'] as $folder) {
                                        $totalFiles += count($folder['files']);
                                    }
                                }
                                echo $totalFiles . ' archivo' . ($totalFiles !== 1 ? 's' : '');
                                ?>
                            </span>
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
                                <p class="text-muted mb-0">No hay carpetas en esta empresa.</p>
                            <?php else: ?>
                                <?php foreach ($companyData['company_dirs'] as $companyDir => $companyDirData): ?>
                                    <?php foreach ($companyDirData['folders'] as $folderName => $folderData): ?>
                                        <div class="mb-4">
                                            <h6 class="text-secondary mb-3">
                                                <i class="bi bi-folder me-1"></i>
                                                <?php echo htmlspecialchars($folderName); ?>
                                            </h6>
                                            <?php if (empty($folderData['files'])): ?>
                                                <small class="text-muted">Sin archivos</small>
                                            <?php else: ?>
                                                <div class="row">
                                                    <?php foreach ($folderData['files'] as $file): ?>
                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                            <div class="card h-100">
                                                                <div class="card-body d-flex flex-column">
                                                                    <div class="d-flex align-items-start mb-2">
                                                                        <input type="checkbox" class="form-check-input me-2 file-checkbox" 
                                                                               name="files[]" 
                                                                               value="<?php echo htmlspecialchars($companyDir . '|' . $folderName . '|' . $file['name']); ?>">
                                                                        <i class="bi bi-file-earmark-excel text-success me-2 fs-5"></i>
                                                                        <div class="flex-grow-1">
                                                                            <h6 class="card-title mb-1" title="<?php echo htmlspecialchars($file['name']); ?>">
                                                                                <?php echo htmlspecialchars($file['name']); ?>
                                                                            </h6>
                                                                            <small class="text-muted">
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
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-auto">
                                                                        <div class="btn-group w-100" role="group">
                                                                            <a href="index.php?c=dashboard&a=download&company=<?php echo urlencode($companyDir); ?>&folder=<?php echo urlencode($folderName); ?>&file=<?php echo urlencode($file['name']); ?>"
                                                                               class="btn btn-outline-primary btn-sm">
                                                                                <i class="bi bi-download me-1"></i>Descargar
                                                                            </a>
                                                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                                    onclick="confirmDelete('<?php echo htmlspecialchars($companyDir); ?>', '<?php echo htmlspecialchars($folderName); ?>', '<?php echo htmlspecialchars($file['name']); ?>')">
                                                                                <i class="bi bi-trash me-1"></i>Eliminar
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
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

// Función de búsqueda
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const companyCards = document.querySelectorAll('.company-card');
    
    companyCards.forEach(card => {
        const companyName = card.getAttribute('data-company');
        if (companyName.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
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