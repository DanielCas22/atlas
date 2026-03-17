<?php
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/ExamModel.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/ExamController.php';

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
