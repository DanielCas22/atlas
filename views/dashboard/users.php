<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Usuarios</h1>
<p>Este módulo muestra el panel de gestión de usuarios.</p>

<p>
    <a class="btn btn-success me-2" href="index.php?c=dashboard&a=createUser">
        <i class="bi bi-plus-circle me-1"></i>Crear usuario
    </a>
    <a class="btn btn-outline-secondary" href="index.php?c=dashboard&a=index">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</p>

<?php if (!empty($users)): ?>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $userItem): ?>
                    <tr>
                        <td><?= htmlspecialchars($userItem['id']) ?></td>
                        <td><?= htmlspecialchars($userItem['username']) ?></td>
                        <td><?= htmlspecialchars($userItem['fullname']) ?></td>
                        <td><?= htmlspecialchars($userItem['email']) ?></td>
                        <td><?= htmlspecialchars($userItem['role_name']) ?></td>
                        <td>
                            <a class="btn btn-sm btn-warning me-2" href="index.php?c=dashboard&a=editUser&id=<?= $userItem['id'] ?>">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </a>
                            <a class="btn btn-sm btn-danger" href="index.php?c=dashboard&a=deleteUser&id=<?= $userItem['id'] ?>" onclick="return confirm('¿Eliminar usuario?');">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No hay usuarios registrados aún.</p>
<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>