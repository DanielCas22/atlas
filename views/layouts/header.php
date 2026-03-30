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
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><strong>Atlas</strong> - Clasificación Exámenes</a>
        <?php if (!empty($_SESSION['user'])): ?>
        <div class="d-flex">
            <button id="theme-toggle" class="theme-switch me-3" aria-label="Cambiar tema">
                <span class="theme-switch__track"></span>
                <span class="theme-switch__thumb"></span>
                <span class="theme-switch__icon theme-switch__icon--light" aria-hidden="true">☀</span>
                <span class="theme-switch__icon theme-switch__icon--dark" aria-hidden="true">🌙</span>
            </button>
            <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['user']['fullname']) ?> (<?= htmlspecialchars($_SESSION['user']['role']) ?>)</span>
            <a href="index.php?c=auth&a=logout" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
        <?php endif; ?>
    </div>
</nav>
<main class="container mt-4">
