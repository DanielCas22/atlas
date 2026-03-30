<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atlas Seguridad - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php?c=dashboard&a=index">
            <i class="bi bi-shield-check me-2"></i>
            <span>Atlas</span>
            <small class="text-muted ms-1 d-none d-sm-inline">Seguridad</small>
        </a>

        <span class="fw-semibold ms-3" style="color:#fff;">Atlas Seguridad - Sistema de Gestión de Exámenes</span>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (!empty($_SESSION['user'])): ?>
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=dashboard&a=index">
                        <i class="bi bi-house-door me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=dashboard&a=users">
                        <i class="bi bi-people me-1"></i>Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=company&a=list">
                        <i class="bi bi-building me-1"></i>Empresas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?c=exam&a=list">
                        <i class="bi bi-clipboard-data me-1"></i>Exámenes
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle border-0 bg-transparent" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><span class="dropdown-item-text small text-muted">Rol: <?= htmlspecialchars($_SESSION['user']['role']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?c=auth&a=logout">
                            <i class="bi bi-box-arrow-right me-2"></i>Salir
                        </a></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
