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
            <?php if (!empty($_GET['message']) || !empty($_GET['error'])): ?>
                <div class="mb-3">
                    <?php if (!empty($_GET['message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_GET['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="c" value="company">
                        <input type="hidden" name="a" value="list">

                        <div class="col-md-8">
                            <label for="searchInput" class="form-label fw-semibold">
                                Buscar Empresa
                            </label>
                            <div class="input-group search-input-group rounded-4 overflow-hidden">
                                <input type="text" class="form-control" id="searchInput" name="search"
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                       placeholder="Ingresa el nombre de la empresa...">
                                <button type="submit" class="btn btn-primary">
                                    Buscar
                                </button>
                                <?php if (!empty($_GET['search'])): ?>
                                    <a href="index.php?c=company&a=list" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4 d-none">
                            <!-- Espacio vacío para mantener el layout si es necesario -->
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
                            <span class="badge bg-primary text-white ms-2"><?= count($companies) ?></span>
                        </h5>
                        <small class="text-muted">
                            <?php if (!empty($_GET['search'])): ?>
                                Resultados para: "<?= htmlspecialchars($_GET['search']) ?>"
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Toolbar de acciones masivas -->
                    <div class="bg-light border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="selectedCount">0</span> empresa(s) seleccionada(s)
                        </div>
                        <form id="deleteMultipleForm" method="POST" action="index.php?c=company&a=deleteMultiple" style="display: inline;">
                            <button type="button" id="deleteSelectedBtn" class="btn btn-danger btn-sm" disabled>
                                <i class="bi bi-trash me-1"></i>Eliminar Seleccionadas
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                        </div>
                                    </th>
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
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input company-checkbox" type="checkbox" 
                                                       name="company_ids[]" value="<?= $company['id'] ?>">
                                            </div>
                                        </td>
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
                                            <span class="badge bg-info text-white fs-6 px-3 py-2">
                                                <i class="bi bi-clipboard-check me-1"></i>
                                                <?= $company['exam_count'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-primary" href="index.php?c=company&a=view&id=<?= $company['id'] ?>"
                                                   data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                                <a class="btn btn-sm btn-warning" href="index.php?c=company&a=edit&id=<?= $company['id'] ?>"
                                                   data-bs-toggle="tooltip" title="Editar empresa">
                                                    <i class="bi bi-pencil me-1"></i>Editar
                                                </a>
                                                <a class="btn btn-sm btn-danger" href="index.php?c=company&a=delete&id=<?= $company['id'] ?>"
                                                   onclick="return confirm('¿Está seguro de eliminar esta empresa?');"
                                                   data-bs-toggle="tooltip" title="Eliminar empresa">
                                                    <i class="bi bi-trash me-1"></i>Eliminar
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

<!-- JavaScript para selección múltiple -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const companyCheckboxes = document.querySelectorAll('.company-checkbox');
    const selectedCountEl = document.getElementById('selectedCount');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const deleteMultipleForm = document.getElementById('deleteMultipleForm');

    // Función para actualizar el contador y estado del botón
    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('.company-checkbox:checked');
        const count = checkedBoxes.length;
        selectedCountEl.textContent = count;
        deleteSelectedBtn.disabled = count === 0;
        
        // Cambiar texto del botón según cantidad
        if (count === 1) {
            deleteSelectedBtn.innerHTML = '<i class="bi bi-trash me-1"></i>Eliminar Seleccionada';
        } else if (count > 1) {
            deleteSelectedBtn.innerHTML = '<i class="bi bi-trash me-1"></i>Eliminar Seleccionadas';
        }
    }

    // Seleccionar/deseleccionar todos
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            companyCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateSelectionState();
        });
    }

    // Manejar cambio en checkboxes individuales
    companyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Verificar si todos están marcados
            const allChecked = Array.from(companyCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(companyCheckboxes).some(cb => cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
            
            updateSelectionState();
        });
    });

    // Manejar clic en botón eliminar
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.company-checkbox:checked');
            const count = checkedBoxes.length;
            
            if (count === 0) {
                return;
            }

            const confirmMessage = count === 1 
                ? '¿Está seguro de eliminar esta empresa?' 
                : '¿Está seguro de eliminar estas ' + count + ' empresas?';
            
            if (confirm(confirmMessage)) {
                // Crear inputs ocultos con los IDs seleccionados
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'company_ids[]';
                    input.value = checkbox.value;
                    deleteMultipleForm.appendChild(input);
                });
                
                // Enviar el formulario
                deleteMultipleForm.submit();
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
