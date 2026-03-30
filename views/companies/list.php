<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Gestión de Empresas</h1>

<div style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="c" value="company">
        <input type="hidden" name="a" value="list">
        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Buscar empresa por nombre..." style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        <button type="submit" class="btn">Buscar</button>
        <?php if (!empty($_GET['search'])): ?>
            <a href="index.php?c=company&a=list" class="btn">Limpiar</a>
        <?php endif; ?>
    </form>
</div>

<p><a class="btn" href="index.php?c=company&a=add">Agregar Empresa</a></p>
<p><a class="btn" href="index.php?c=dashboard&a=index">Volver al Dashboard</a></p>

<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Cantidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($companies as $company): ?>
            <tr>
                <td><?= htmlspecialchars($company['name']) ?></td>
                <td><?= $company['exam_count'] ?></td>
                <td>
                    <a class="btn-small" href="index.php?c=company&a=view&id=<?= $company['id'] ?>">Ver</a>
                    <a class="btn-small" href="index.php?c=company&a=edit&id=<?= $company['id'] ?>">Editar</a>
                    <a class="btn-small btn-danger" href="index.php?c=company&a=delete&id=<?= $company['id'] ?>" onclick="return confirm('¿Está seguro?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (empty($companies)): ?>
    <p style="text-align: center; color: #666; margin-top: 20px;">
        <?php if (!empty($_GET['search'])): ?>
            No se encontraron empresas que coincidan con "<?= htmlspecialchars($_GET['search']) ?>"
        <?php else: ?>
            No hay empresas registradas
        <?php endif; ?>
    </p>
<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
