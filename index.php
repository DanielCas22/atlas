<?php
session_start();

// Evitar cache para que el botón "atrás" no vuelva a mostrar páginas autenticadas
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0
header('Expires: 0'); // Proxies

// Cargar autoload de Composer
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/ExamModel.php';
require_once __DIR__ . '/models/CompanyModel.php';

$userModel = new UserModel();
$userModel->ensureDefaultUsers();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/ExamController.php';
require_once __DIR__ . '/controllers/CompanyController.php';

$controller = $_GET['c'] ?? 'auth';
$action = $_GET['a'] ?? 'login';

switch ($controller) {
    case 'auth':
        $ctrl = new AuthController();
        break;
    case 'dashboard':
        $ctrl = new DashboardController();
        break;
    case 'exam':
        $ctrl = new ExamController();
        break;
    case 'company':
        $ctrl = new CompanyController();
        break;
    default:
        http_response_code(404);
        echo 'Página no encontrada';
        exit;
}

if (!method_exists($ctrl, $action)) {
    http_response_code(404);
    echo 'Acción no encontrada';
    exit;
}

$ctrl->{$action}();
