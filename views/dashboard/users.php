<?php include __DIR__ . '/../layouts/header.php'; ?>

<h1>Usuarios</h1>
<p>Este módulo muestra el panel de gestión de usuarios.</p>

<p>
    <a class="btn" href="index.php?c=dashboard&a=createUser">Crear usuario</a>
    <a class="btn" href="index.php?c=dashboard&a=index">Volver</a>
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
                            <a class="btn" href="index.php?c=dashboard&a=editUser&id=<?= $userItem['id'] ?>">Editar</a>
                            <a class="btn" href="index.php?c=dashboard&a=deleteUser&id=<?= $userItem['id'] ?>" onclick="return confirm('¿Eliminar usuario?');">Eliminar</a>
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