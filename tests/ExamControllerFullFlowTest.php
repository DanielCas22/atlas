<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/CompanyModel.php';
require_once __DIR__ . '/../models/ExamModel.php';
require_once __DIR__ . '/../controllers/ExamController.php';
require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../helpers/FlashMessage.php';

function assertOrFail($condition, $message)
{
    if (!$condition) {
        echo "FAIL: $message\n";
        exit(1);
    }
}

$_SESSION['user'] = 'test_flow_user';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$companyName = 'Atlas Flow Test ' . time() . rand(1000, 9999);
$orderNumber = 'FLOW' . time() . rand(100, 999);

$controller = new ExamController();
$companyModel = new CompanyModel();
$examModel = new ExamModel();

$examTypes = $examModel->getExamTypes();
assertOrFail(!empty($examTypes), 'Debe existir al menos un tipo de examen en la base de datos.');
$examTypeId = intval($examTypes[0]['id']);

$candidateText = implode("\t", [
    '80749268',
    'OSCAR JAVIER GUTIERREZ PARRAGA',
    'LA RESURECCION',
    '',
    '3133198592',
    'M',
    '11/11/1984',
    '14/05/2026'
]);

$reflection = new ReflectionClass(ExamController::class);
$extractMethod = $reflection->getMethod('extractCandidates');
$extractMethod->setAccessible(true);

$candidates = $extractMethod->invoke($controller, $candidateText);
assertOrFail(is_array($candidates) && count($candidates) === 1, 'Debe extraer 1 candidato del texto de prueba.');
assertOrFail($candidates[0]['document_number'] === '80749268', 'Documento extraído incorrecto.');
assertOrFail($candidates[0]['name'] === 'OSCAR JAVIER GUTIERREZ PARRAGA', 'El nombre debe excluir el barrio.');
assertOrFail($candidates[0]['phone'] === '3133198592', 'Teléfono extraído incorrecto.');
assertOrFail($candidates[0]['exam_date'] === '2026-05-14' || $candidates[0]['exam_date'] === '14/05/2026', 'Fecha de examen extraída incorrecta.');

$companyId = $companyModel->addIfNotExists($companyName);
assertOrFail($companyId, 'No se pudo crear la empresa de prueba.');

$insertedExamIds = [];
try {
    foreach ($candidates as $candidate) {
        $success = $examModel->add(
            $companyId,
            $examTypeId,
            $candidate['name'],
            $candidate['document_number'],
            $candidate['phone'],
            $candidate['gender'],
            $candidate['birth_date'],
            $candidate['exam_date'],
            $orderNumber,
            $candidate['status'] ?? 'PENDIENTE'
        );

        assertOrFail($success, 'No se pudo insertar el examen en la base de datos.');
    }

    $inserted = $examModel->findByOrderNumber($orderNumber);
    assertOrFail(count($inserted) === count($candidates), 'El número de exámenes insertados no coincide.');
    assertOrFail($inserted[0]['candidate_name'] === 'OSCAR JAVIER GUTIERREZ PARRAGA', 'El nombre insertado en la DB es incorrecto.');
    assertOrFail($inserted[0]['phone'] === '3133198592', 'El teléfono insertado en la DB es incorrecto.');
    assertOrFail($inserted[0]['order_number'] === $orderNumber, 'El número de orden insertado en la DB es incorrecto.');

    echo "FULL FLOW TEST PASSED\n";
} finally {
    if (!empty($orderNumber)) {
        $examModel->deleteByCompanyAndOrder($companyId, $orderNumber);
    }
    if (!empty($companyId)) {
        $companyModel->delete($companyId);
    }
}
