<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atlas Seguridad - Dashboard</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<nav class="topbar">
    <div><strong>Atlas</strong> - Clasificación Exámenes</div>
    <?php if (!empty($_SESSION['user'])): ?>
        <div class="topbar-right">
            <span><?= htmlspecialchars($_SESSION['user']['fullname']) ?> (<?= htmlspecialchars($_SESSION['user']['role']) ?>)</span>
            <a href="index.php?c=auth&a=logout">Salir</a>
        </div>
    <?php endif; ?>
</nav>
<main class="container">
