<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/ExamController.php';

function expect($condition, $message)
{
    if (!$condition) {
        echo "FAIL: $message\n";
        exit(1);
    }
}

$reflection = new ReflectionClass(ExamController::class);
$controller = $reflection->newInstanceWithoutConstructor();

$extractMethod = $reflection->getMethod('extractCandidates');
$extractMethod->setAccessible(true);

$parseMethod = $reflection->getMethod('parseExamFile');
$parseMethod->setAccessible(true);

// Datos de prueba construidos a partir del ejemplo provisto.
$textData = implode("\n", [
    "80749268\tOSCAR JAVIER GUTIERREZ PARRAGA\tLA RESURECCION\t\t3133198592\tM\t+\t11/11/1984\tCOLOMBIA\tBOGOTA\tBOGOTA D.C\tInactivo\tFinalizado\tCompletado\tCompletado\tCompletado\tCompletado\tCompletado\tApto\t989076\t14/05/2026",
    "1118120141\tESTEBAN DAVID MENDOZA CORDOBA\tCALLE 25 A SUR\t\t3222952746\tM\t+\t2/07/2004\tCOLOMBIA\tBOGOTA\tBOGOTA D.C\tActivo\tFinalizado\tCompletado\tCompletado\tCompletado\tCompletado\tCompletado\tApto\t999110\t16/05/2026"
]);

$candidates = $extractMethod->invoke($controller, $textData);
expect(count($candidates) === 2, 'Debe extraer 2 candidatos del texto de prueba.');
expect($candidates[0]['document_number'] === '80749268', 'Primer documento esperado 80749268.');
expect($candidates[0]['name'] === 'OSCAR JAVIER GUTIERREZ PARRAGA', 'El nombre no debe contener el barrio.');
expect($candidates[0]['phone'] === '3133198592', 'Debe extraer el teléfono 3133198592.');
expect($candidates[1]['name'] === 'ESTEBAN DAVID MENDOZA CORDOBA', 'El segundo nombre debe extraerse correctamente.');
expect($candidates[1]['phone'] === '3222952746', 'Debe extraer el teléfono 3222952746.');

// Generar CSV de prueba con encabezados reales del ejemplo.
$csvHeaders = [
    'NUMERO_DOCUMENTO_ASPIRANTE',
    'NOMBRE_ASPIRANTE',
    'DIRECCION_ASPIRANTE',
    'CAMPO_VACIO',
    'CELULAR_ASPIRANTE',
    'GENERO_ASPIRANTE',
    'RH_ASPIRANTE',
    'FECHA_NACIMIENTO_ASPIRANTE',
    'PAIS_ASPIRANTE',
    'DEPARTAMENTO_ASPIRANTE',
    'CIUDAD_ASPIRANTE',
    'ESTADO_ASPIRANTE',
    'ESTADO_RECONOCIMIENTO',
    'ESTADO_HCFONOAUDIOLOGIA',
    'ESTADO_HCOPTOMETRIA',
    'ESTADO_HCPSICOLOGIA',
    'ESTADO_HCMEDICINAGENERAL',
    'ESTADO_CERTIFICADO',
    'RESULTADO',
    'CONSECUTIVO_BOLSA',
    'FECHA_CREACION'
];

$csvRows = [
    [
        '80749268',
        'OSCAR JAVIER GUTIERREZ PARRAGA',
        'LA RESURECCION',
        '',
        '3133198592',
        'M',
        '+',
        '11/11/1984',
        'COLOMBIA',
        'BOGOTA',
        'BOGOTA D.C',
        'Inactivo',
        'Finalizado',
        'Completado',
        'Completado',
        'Completado',
        'Completado',
        'Completado',
        'Apto',
        '989076',
        '14/05/2026'
    ],
    [
        '1118120141',
        'ESTEBAN DAVID MENDOZA CORDOBA',
        'CALLE 25 A SUR',
        '',
        '3222952746',
        'M',
        '+',
        '2/07/2004',
        'COLOMBIA',
        'BOGOTA',
        'BOGOTA D.C',
        'Activo',
        'Finalizado',
        'Completado',
        'Completado',
        'Completado',
        'Completado',
        'Completado',
        'Apto',
        '999110',
        '16/05/2026'
    ]
];

$tempCsv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'atlas_exam_parser_test.csv';
$fp = fopen($tempCsv, 'w');
fputcsv($fp, $csvHeaders);
foreach ($csvRows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);

$result = $parseMethod->invoke($controller, $tempCsv, 'test.csv');
expect(is_array($result), 'El resultado del parseo de CSV debe ser un arreglo.');
expect(isset($result['candidates']), 'El parseo de CSV debe devolver la clave candidates.');
$candidatesCsv = $result['candidates'];
expect(count($candidatesCsv) === 2, 'Debe extraer 2 candidatos del CSV de prueba.');
expect($candidatesCsv[0]['name'] === 'OSCAR JAVIER GUTIERREZ PARRAGA', 'CSV: nombre sin barrio.');
expect($candidatesCsv[0]['phone'] === '3133198592', 'CSV: teléfono correcto.');
expect($candidatesCsv[0]['order_number'] === '989076', 'CSV: número de orden correcto.');
expect($candidatesCsv[0]['exam_date'] === '2026-05-14', 'CSV: fecha de examen correcta.');
expect($candidatesCsv[1]['name'] === 'ESTEBAN DAVID MENDOZA CORDOBA', 'CSV: segundo nombre correcto.');
expect($candidatesCsv[1]['phone'] === '3222952746', 'CSV: segundo teléfono correcto.');
expect($candidatesCsv[1]['order_number'] === '999110', 'CSV: segunda orden correcta.');
expect($candidatesCsv[1]['exam_date'] === '2026-05-16', 'CSV: segunda fecha de examen correcta.');

unlink($tempCsv);

echo "ALL TESTS PASSED\n";
