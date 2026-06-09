<?php
$isLoginPage = isset($_GET['c'], $_GET['a']) && $_GET['c'] === 'auth' && $_GET['a'] === 'login';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atlas Seguridad - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .dropdown-menu-lg {
            min-width: 500px !important;
        }
        
        @media (max-width: 768px) {
            .dropdown-menu-lg {
                min-width: 90vw !important;
                max-width: 90vw;
                left: -0.5rem !important;
            }
            
            .navbar-brand span:not(.text-muted) {
                font-size: 1.2rem;
            }
            
            .navbar {
                padding: 0.5rem 0;
            }
            
            .dropdown-item {
                font-size: 0.95rem;
                padding: 0.6rem 0.3rem !important;
                word-wrap: break-word;
            }
            
            .dropdown-item span {
                display: inline-block;
                max-width: calc(100% - 30px);
            }
            
            .dropdown-header {
                font-size: 0.95rem;
                padding: 0.5rem 0.3rem !important;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .dropdown-menu-lg {
                min-width: 85vw !important;
                max-width: 85vw;
                padding: 0.5rem !important;
            }
            
            .dropdown-item {
                font-size: 0.9rem;
                padding: 0.5rem 0.25rem !important;
            }
            
            .dropdown-item i {
                font-size: 1rem;
                margin-right: 0.5rem !important;
            }
            
            .dropdown-header i {
                font-size: 0.95rem;
                margin-right: 0.5rem !important;
            }
            
            .mb-3.pb-3 {
                margin-bottom: 0.75rem !important;
                padding-bottom: 0.75rem !important;
            }
            
            .dropdown-header {
                font-size: 0.9rem;
                padding: 0.4rem 0.25rem !important;
            }
        }

        @media (max-width: 360px) {
            .dropdown-menu-lg {
                min-width: 80vw !important;
                max-width: 80vw;
            }
            
            .dropdown-item span {
                font-size: 0.85rem;
            }
            
            .navbar-brand small {
                display: none;
            }
        }

        .scroll-top-btn {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0.75rem 1.5rem rgba(13, 110, 253, 0.2);
            transition: opacity 0.2s ease, transform 0.2s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 1050;
        }

        .scroll-top-btn.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .scroll-top-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<?php if (!$isLoginPage && !empty($_SESSION['user'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php?c=dashboard&a=index">
            <i class="bi bi-shield-check me-2"></i>
            <span>Atlas</span>
            <small class="text-muted ms-1 d-none d-sm-inline">Seguridad</small>
        </a>

        <span class="fw-semibold ms-3 d-none d-lg-inline" style="color:#fff;">Atlas Seguridad - Sistema de Gestión de Exámenes</span>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="gestionsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-seam me-1"></i><span class="d-none d-sm-inline">Gestiones</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg p-4" aria-labelledby="gestionsDropdown">
                        <!-- Exámenes -->
                        <div class="mb-3 pb-3 border-bottom">
                            <h6 class="dropdown-header fw-bold text-primary px-0">
                                <i class="bi bi-clipboard-check me-2"></i>Exámenes
                            </h6>
                            <a class="dropdown-item py-2" href="index.php?c=exam&a=add">
                                <i class="bi bi-plus-circle text-primary me-2"></i>
                                <span>Crear Nuevo Examen</span>
                            </a>
                            <a class="dropdown-item py-2" href="index.php?c=exam&a=list">
                                <i class="bi bi-list-ul text-primary me-2"></i>
                                <span>Ver Listado de Exámenes</span>
                            </a>
                            <a class="dropdown-item py-2" href="index.php?c=exam&a=exportCandidates">
                                <i class="bi bi-download text-primary me-2"></i>
                                <span>Exportar Candidatos</span>
                            </a>
                        </div>
                        
                        <!-- Empresas -->
                        <div class="mb-3 pb-3 border-bottom">
                            <h6 class="dropdown-header fw-bold text-danger px-0">
                                <i class="bi bi-building me-2"></i>Empresas
                            </h6>
                            <a class="dropdown-item py-2" href="index.php?c=company&a=list">
                                <i class="bi bi-list-check text-danger me-2"></i>
                                <span>Listar Empresas</span>
                            </a>
                            <a class="dropdown-item py-2" href="index.php?c=dashboard&a=clasificacion_empresas">
                                <i class="bi bi-diagram-3 text-danger me-2"></i>
                                <span>Clasificar por Empresa</span>
                            </a>
                            <a class="dropdown-item py-2" href="index.php?c=dashboard&a=estadisticas">
                                <i class="bi bi-bar-chart-line text-danger me-2"></i>
                                <span>Datos y Estadísticas</span>
                            </a>
                        </div>

                        <!-- Usuarios -->
                        <div class="mb-3 pb-3 border-bottom">
                            <h6 class="dropdown-header fw-bold text-info px-0">
                                <i class="bi bi-people me-2"></i>Usuarios
                            </h6>
                            <a class="dropdown-item py-2" href="index.php?c=dashboard&a=users">
                                <i class="bi bi-person-gear text-info me-2"></i>
                                <span>Gestionar Usuarios</span>
                            </a>
                        </div>

                        <!-- Archivos -->
                        <div class="mb-0">
                            <h6 class="dropdown-header fw-bold text-success px-0">
                                <i class="bi bi-folder-fill me-2"></i>Archivos
                            </h6>
                            <a class="dropdown-item py-2" href="index.php?c=dashboard&a=files">
                                <i class="bi bi-file-earmark-check text-success me-2"></i>
                                <span>Archivos Clasificados</span>
                            </a>
                        </div>
                    </div>
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
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="flex-grow-1">
