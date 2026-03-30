<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Bienvenido a ATLAS</h1>

<section class="modules-card">
    <h2>Usuarios</h2>
    <div class="modules-grid">
        <a class="module-item" href="index.php?c=dashboard&a=users"><i class="bi bi-people"></i> Ver detalle</a>
    </div>
</section>

<section class="modules-card">
    <h2>Empresas</h2>
    <div class="modules-grid">
        <a class="module-item" href="index.php?c=company&a=list"><i class="bi bi-building"></i> Gestionar</a>
    </div>
</section>

<section class="summary">
    <a class="btn btn-primary" href="index.php?c=exam&a=add"><i class="bi bi-plus-circle"></i> Agregar examen</a>
    <a class="btn btn-secondary" href="index.php?c=exam&a=list"><i class="bi bi-list"></i> Ver lista general</a>

    <div style="display: inline-block; margin-left: 10px;">
        <form method="GET" action="index.php" style="display: flex; gap: 5px; align-items: center;">
            <input type="hidden" name="c" value="exam">
            <input type="hidden" name="a" value="exportCandidates">
            <input type="text" name="order" placeholder="N° Orden" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100px;">
            <button type="submit" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</button>
        </form>
        <a class="btn btn-info" href="index.php?c=exam&a=exportCandidates" style="margin-left: 10px;"><i class="bi bi-download"></i> Exportar Todo</a>
    </div>
</section>

<!-- Tabla principal removida a pedido -->

<?php include __DIR__ . '/../layouts/footer.php'; ?>