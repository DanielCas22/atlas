<?php
// Script para crear archivo Excel de prueba

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$headers = ['Empresa', 'Nombre', 'Documento', 'Teléfono', 'Género', 'Fecha Nacimiento', 'Tipo Examen'];
$sheet->fromArray([$headers], null, 'A1');

// Datos de prueba
$testData = [
    ['Empresa A', 'Juan Pérez', '12345678', '3001234567', 'M', '1990-01-15', 'Psicofísico'],
    ['Empresa A', 'María García', '87654321', '3019876543', 'F', '1992-05-20', 'Psicofísico'],
    ['Empresa A', 'Carlos López', '11111111', '3005555555', 'M', '1988-03-10', 'Psicológico'],
    ['Empresa B', 'Ana Martínez', '22222222', '3006666666', 'F', '1995-07-25', 'Psicofísico'],
    ['Empresa B', 'Pedro Rodríguez', '33333333', '3007777777', 'M', '1991-11-30', 'Médico'],
    ['Empresa B', 'Sandra Torres', '44444444', '3008888888', 'F', '1993-02-14', 'Psicológico'],
    ['Empresa C', 'Luis Fernández', '55555555', '3009999999', 'M', '1989-09-05', 'Psicofísico'],
    ['Empresa C', 'Rosa Díaz', '66666666', '3000001111', 'F', '1994-12-20', 'Médico'],
];

$sheet->fromArray($testData, null, 'A2');

// Ajustar ancho de columnas
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Guardar archivo
$writer = new Xlsx($spreadsheet);
$writer->save('archivos_prueba.xlsx');

echo "Archivo de prueba creado: archivos_prueba.xlsx\n";
?>
