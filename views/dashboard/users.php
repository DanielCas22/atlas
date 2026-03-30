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
                            <a class="btn" style="background:#e0e0e0;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;margin-right:2px;" href="index.php?c=dashboard&a=editUser&id=<?= $userItem['id'] ?>"><i class="bi bi-pencil"></i> <u>Editar</u></a>
                            <a class="btn" style="background:#d98880;color:#000;text-decoration:underline;padding:2px 8px;border-radius:4px;" href="index.php?c=dashboard&a=deleteUser&id=<?= $userItem['id'] ?>" onclick="return confirm('¿Eliminar usuario?');"><i class="bi bi-trash"></i> <u>Eliminar</u></a>
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