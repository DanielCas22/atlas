<?php
// Script de prueba para verificar generación de archivos

require 'vendor/autoload.php';
require 'config/Database.php';
require 'models/BaseModel.php';
require 'models/ExamModel.php';
require 'models/CompanyModel.php';
require 'models/UserModel.php';
require 'controllers/DashboardController.php';

// Crear instancia del controlador
$controller = new DashboardController();

// Datos de prueba simulados
$companiesData = [
    'Empresa A' => [
        ['name' => 'Juan Pérez', 'document' => '12345678', 'phone' => '3001234567', 'gender' => 'M', 'birth' => '1990-01-15', 'exam' => 'Psicofísico'],
        ['name' => 'María García', 'document' => '87654321', 'phone' => '3019876543', 'gender' => 'F', 'birth' => '1992-05-20', 'exam' => 'Psicofísico'],
    ],
    'Empresa B' => [
        ['name' => 'Ana Martínez', 'document' => '22222222', 'phone' => '3006666666', 'gender' => 'F', 'birth' => '1995-07-25', 'exam' => 'Psicofísico'],
        ['name' => 'Pedro Rodríguez', 'document' => '33333333', 'phone' => '3007777777', 'gender' => 'M', 'birth' => '1991-11-30', 'exam' => 'Médico'],
    ],
];

// Usar reflexión para acceder al método privado
$reflection = new ReflectionClass('DashboardController');
$method = $reflection->getMethod('createCompanyFolders');
$method->setAccessible(true);

try {
    $method->invoke($controller, $companiesData);
    echo "✓ Proceso completado exitosamente\n";
    echo "Revisa la carpeta: c:\\xampp\\htdocs\\atlas\\empresas_clasificadas\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
