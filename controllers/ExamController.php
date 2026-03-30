<?php

class ExamController
{
    private $examModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
    }

    public function list()
    {
        $this->ensureAuth();
        $exams = $this->examModel->all();
        include __DIR__ . '/../views/exams/list.php';
    }

    public function add()
    {
        $this->ensureAuth();

        $desiredExamTypes = [
            'examen psicofisico',
            'examen psicosensometrico',
            'examen ocupacional de ingreso',
            'examen ocupacional de retiro',
            'examen ocupacional periodico'
        ];
        $this->examModel->syncExamTypes($desiredExamTypes);

        // Obtener empresas directamente de la base de datos en lugar de sincronizar lista predefinida
        $companies = $this->examModel->getCompanies();
        $examTypes = $this->examModel->getExamTypes();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $companyInput = trim($_POST['company_id'] ?? '');
            $exam_type_id = intval($_POST['exam_type_id'] ?? 0);
            $order_number = trim($_POST['order_number'] ?? '');

            $company_id = intval($companyInput);
            if (!$company_id) {
                $company = $this->examModel->findCompanyByName($companyInput);
                $company_id = $company['id'] ?? 0;
            }

            $candidateText = trim($_POST['candidate_text'] ?? '');
            $candidates = $this->extractCandidates($candidateText);

            if ($company_id && $exam_type_id && !empty($candidates) && !empty($order_number)) {
                foreach ($candidates as $candidate) {
                    $status = $candidate['status'] ?? 'PENDIENTE';
                    $this->examModel->add($company_id, $exam_type_id, $candidate['name'], $candidate['document_number'], $candidate['phone'], $candidate['gender'], $candidate['birth_date'], $candidate['exam_date'], $order_number, $status);
                }
                header('Location: index.php?c=dashboard&a=index');
                exit;
            }

            $error = 'Complete todos los campos';
        }

        include __DIR__ . '/../views/exams/add.php';
    }

    public function status()
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            if ($id && in_array($status, ['PENDIENTE','EN_CURSO','FINALIZADO','RECHAZADO'])) {
                $this->examModel->updateStatus($id, $status);
            }
        }

        header('Location: index.php?c=dashboard&a=index');
        exit;
    }

    public function delete()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $this->examModel->delete($id);
        }

        header('Location: index.php?c=exam&a=list');
        exit;
    }

    public function edit()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        $exam = $this->examModel->findById($id);

        if (!$exam) {
            header('Location: index.php?c=exam&a=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $candidate_name = trim($_POST['candidate_name'] ?? '');
            $document_number = trim($_POST['document_number'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $gender = strtoupper(trim($_POST['gender'] ?? ''));
            $birth_date = trim($_POST['birth_date'] ?? '');
            $exam_date = trim($_POST['exam_date'] ?? '');
            $order_number = trim($_POST['order_number'] ?? '');
            $status = trim($_POST['status'] ?? 'PENDIENTE');

            if ($candidate_name !== '' && $document_number !== '') {
                $this->examModel->update($id, $candidate_name, $document_number, $phone, $gender, $birth_date, $exam_date, $order_number, $status);
                header('Location: index.php?c=exam&a=list');
                exit;
            }

            $error = 'Nombre y documento son obligatorios';
        }

        include __DIR__ . '/../views/exams/edit.php';
    }

    private function performOCR(string $tmpFile)
    {
        // Requiere Tesseract instalado en el servidor: tesseract <img> stdout
        $text = '';
        $escaped = escapeshellarg($tmpFile);
        $cmd = "tesseract $escaped stdout 2>&1";
        $result = shell_exec($cmd);
        if ($result !== null) {
            $text = trim($result);
        }

        return $text;
    }

    private function extractCandidates(string $text)
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        $candidates = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Probar diferentes separadores: tabulador, punto y coma, coma
            $separators = ["\t", ";", ","];
            $parts = [];

            foreach ($separators as $sep) {
                if (strpos($line, $sep) !== false) {
                    $parts = explode($sep, $line);
                    break;
                }
            }

            if (empty($parts)) {
                // Espacios como separador
                $parts = preg_split('/\s+/', $line);
            }

            $parts = array_map('trim', $parts);
            $parts = array_filter($parts, fn($p) => $p !== '');
            $parts = array_values($parts);

            if (count($parts) < 2) {
                continue;
            }

            $document_number = null;
            $name = null;
            $phone = null;
            $gender = null;
            $birth_date = null;
            $exam_date = null;
            $status = null;
            $order_number = null;

            // 1) Documento (ID): primer número largo (>= 5 dígitos)
            $documentIndex = null;
            foreach ($parts as $idx => $part) {
                $clean = preg_replace('/\D/', '', $part);
                if (!$document_number && preg_match('/^\d{5,}$/', $clean)) {
                    $document_number = $clean;
                    $documentIndex = $idx;
                    unset($parts[$idx]);
                    break;
                }
            }

            // 1.5) Nombre: tokens desde inicio hasta primer token claramente no nombre
            $nameParts = [];
            foreach ($parts as $idx => $part) {
                $clean = preg_replace('/\D/', '', $part);
                $isEmail = filter_var($part, FILTER_VALIDATE_EMAIL);
                $isDate = $this->isValidDate($part);
                $isGender = preg_match('/^[MF]$/i', $part);
                $isPhone = preg_match('/^(3\d{7,9}|\d{7,13})$/', $clean);
                $isStatus = preg_match('/\b(apto|no\s+apto|reprobado|finalizado|completado|aplazado)\b/i', $part);
                $isOrder = preg_match('/^\d{3,}$/', $clean);
                $isSkipToken = preg_match('/\b(colombia|bogota|d\.c|activo|inactivo|usuarios?)\b/i', $part);

                if ($isEmail || $isDate || $isGender || $isPhone || $isStatus || $isOrder || $isSkipToken || ($clean !== '' && preg_match('/^\d+$/', $part) && strlen($part) <= 3)) {
                    break;
                }

                if ($part !== '') {
                    $nameParts[] = $part;
                    unset($parts[$idx]);
                }
            }

            if (!empty($nameParts)) {
                $name = implode(' ', $nameParts);
            }

            // 2) Fechas (nacimiento + examen), ordenar cronológicamente
            $dateCandidates = [];
            foreach ($parts as $idx => $part) {
                if ($this->isValidDate($part)) {
                    $date = $this->parseDate($part);
                    if ($date) {
                        $dateCandidates[] = ['idx' => $idx, 'date' => $date];
                    }
                }
            }
            usort($dateCandidates, fn($a, $b) => $a['date'] <=> $b['date']);
            if (!empty($dateCandidates)) {
                $birth_date = $dateCandidates[0]['date']->format('Y-m-d');
                unset($parts[$dateCandidates[0]['idx']]);
            }
            if (count($dateCandidates) > 1) {
                $exam_date = $dateCandidates[1]['date']->format('Y-m-d');
                unset($parts[$dateCandidates[1]['idx']]);
            }

            // 3) Género (M/F)
            foreach ($parts as $idx => $part) {
                if (!$gender && preg_match('/^[MF]$/i', $part)) {
                    $gender = strtoupper($part);
                    unset($parts[$idx]);
                    break;
                }
            }

            // 4) Resultado (tomar el último encontrado)
            $foundStatus = null;
            foreach ($parts as $idx => $part) {
                $low = mb_strtolower($part);
                if (preg_match('/\b(apto|no\s+apto|reprobado|finalizado|completado|aplazado)\b/i', $part)) {
                    if (stripos($low, 'no apto') !== false || stripos($low, 'reprobado') !== false) {
                        $foundStatus = 'RECHAZADO';
                    } elseif (stripos($low, 'aplazado') !== false) {
                        $foundStatus = 'EN_CURSO';
                    } elseif (stripos($low, 'apto') !== false || stripos($low, 'finalizado') !== false || stripos($low, 'completado') !== false) {
                        $foundStatus = 'FINALIZADO';
                    } else {
                        $foundStatus = 'PENDIENTE';
                    }
                    unset($parts[$idx]);
                }
            }
            $status = $foundStatus;

            // 5) Orden numérico al final (>= 3 dígitos y distinto de documento)
            foreach ($parts as $idx => $part) {
                $clean = preg_replace('/\D/', '', $part);
                if (!$order_number && preg_match('/^\d{3,}$/', $clean) && $clean !== $document_number) {
                    $order_number = $clean;
                    unset($parts[$idx]);
                    break;
                }
            }

            // 6) Teléfono
            foreach ($parts as $idx => $part) {
                $clean = preg_replace('/\D/', '', $part);
                if (!$phone && preg_match('/^(3\d{7,9}|\d{7,10})$/', $clean)) {
                    $phone = $clean;
                    unset($parts[$idx]);
                    break;
                }
            }

            // 7) Nombre: concatenar resto de tokens no reservados
            if (!$name) {
                $nameParts = [];
                foreach ($parts as $part) {
                    $check = preg_replace('/\D/', '', $part);
                    if (!preg_match('/^\d+$/', $check)) {
                        $nameParts[] = $part;
                    }
                }
                if (!empty($nameParts)) {
                    $name = implode(' ', $nameParts);
                }
            }

            $candidates[] = [
                'document_number' => $document_number,
                'name' => $name,
                'phone' => $phone,
                'gender' => $gender,
                'birth_date' => $birth_date,
                'exam_date' => $exam_date,
                'status' => $status,
                'order_number' => $order_number
            ];
        }

        $unique = [];
        foreach ($candidates as $candidate) {
            $key = $candidate['document_number'] ?: $candidate['name'];
            if ($key && !isset($unique[$key])) {
                $unique[$key] = $candidate;
            }
        }

        return array_values($unique);
    }

    private function isValidDate($str)
    {
        // Detecta patrones de fecha con múltiples separadores: /, -, . o espacios
        // Formatos: DD/MM/YYYY, DD-MM-YYYY, DD.MM.YYYY, YYYY-MM-DD, etc.
        $str = trim($str);
        // Busca patrón: número.separador.número.separador.número (4 dígitos)
        return preg_match('/^\d{1,2}[-\/.]\d{1,2}[-\/.]\d{4}$|^\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}$/', $str);
    }

    private function parseDate($str)
    {
        $str = trim($str);
        
        // Normalizar separadores: convertir punto o guión a barra
        $normalized = str_replace(['.', '-'], '/', $str);
        
        // Intentar múltiples formatos
        $formats = [
            'd/m/Y',
            'Y/m/d',
            'd/m/y',
            'Y/m/d',
            'm/d/Y',
            'd/m/Y H:i:s',
            'Y/m/d H:i:s',
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $normalized);
            if ($date && $date->format('Y-m-d')) {
                // QUITAR restricción de años - aceptar cualquier fecha válida
                return $date;
            }
        }
        
        // Si nada funcionó, intentar con formato original (en caso de tener guiones en formato ISO)
        $date = \DateTime::createFromFormat('Y-m-d', trim(str_replace('/', '-', $str)));
        if ($date) {
            return $date;
        }
        
        return null;
    }

    public function exportCandidates()
    {
        $this->ensureAuth();

        require_once __DIR__ . '/../vendor/autoload.php'; // Asumir PhpSpreadsheet instalado

        // Crear un mock simple de PSR Simple Cache para evitar la dependencia faltante
        if (!interface_exists('Psr\SimpleCache\CacheInterface')) {
            // Crear interfaz PSR Simple Cache básica
            eval('
                namespace Psr\SimpleCache;
                interface CacheInterface {
                    public function get($key, $default = null);
                    public function set($key, $value, $ttl = null);
                    public function delete($key);
                    public function clear();
                    public function getMultiple($keys, $default = null);
                    public function setMultiple($values, $ttl = null);
                    public function deleteMultiple($keys);
                    public function has($key);
                }
            ');

            // Crear implementación mock
            eval('
                namespace PhpOffice\PhpSpreadsheet\Collection\Memory;
                class SimpleCache implements \Psr\SimpleCache\CacheInterface {
                    private $data = [];
                    public function get($key, $default = null) { return $this->data[$key] ?? $default; }
                    public function set($key, $value, $ttl = null) { $this->data[$key] = $value; return true; }
                    public function delete($key) { unset($this->data[$key]); return true; }
                    public function clear() { $this->data = []; return true; }
                    public function getMultiple($keys, $default = null) { $result = []; foreach($keys as $key) { $result[$key] = $this->get($key, $default); } return $result; }
                    public function setMultiple($values, $ttl = null) { foreach($values as $key => $value) { $this->set($key, $value, $ttl); } return true; }
                    public function deleteMultiple($keys) { foreach($keys as $key) { $this->delete($key); } return true; }
                    public function has($key) { return isset($this->data[$key]); }
                }
            ');

            // Configurar PhpSpreadsheet para usar el mock cache
            \PhpOffice\PhpSpreadsheet\Settings::setCache(new \PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache());
        }

        $orderNumber = trim($_GET['order'] ?? '');
        if (empty($orderNumber)) {
            // Si no se especifica orden, exportar todo
            $exams = $this->examModel->allWithDetails();
            $filename = 'atlas_export_' . date('Ymd_His') . '.xlsx';
        } else {
            // Si se especifica orden, exportar solo esa orden
            $exams = $this->examModel->findByOrderNumber($orderNumber);
            $filename = 'Listado de asistencia OS ' . $orderNumber . '.xlsx';
        }

        // Cargar plantilla de Excel
        $templatePath = __DIR__ . '/../plantilla de organizacion/plantilla general.xlsx';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException('No se encontró la plantilla: ' . $templatePath);
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($templatePath);

        // Eliminar posibles rangos con nombre corruptos/antiguos heredados de plantilla
        foreach ($spreadsheet->getNamedRanges() as $namedRange) {
            $spreadsheet->removeNamedRange($namedRange->getName());
        }

        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados fijos según requerimiento, SIN CAMBIAR en la plantilla
        foreach (range('B', 'J') as $col) {
            $sheet->setCellValue($col . '1', 'LISTADO DE ASISTENCIA');
            $sheet->setCellValue($col . '2', 'PSICOFISICAS-ARMAS');
        }
        $fixedHeaders = [
            'B' => 'N',
            'C' => 'Identificación',
            'D' => 'Nombre',
            'E' => 'Teléfono',
            'F' => 'Fecha de nacimiento',
            'G' => 'Género',
            'H' => 'Fecha de examen',
            'I' => 'Resultado',
            'J' => 'Orden',
        ];
        foreach ($fixedHeaders as $cell => $label) {
            $sheet->setCellValue($cell . '3', $label);
        }

        // Los datos exportados comienzan en fila 4 (manteniendo B1:J3 inalterables como se pidió)
        $row = 4;
        $sequence = 1;
        foreach ($exams as $exam) {
            $sheet->setCellValue('B' . $row, $sequence);
            $sheet->setCellValue('C' . $row, $exam['document_number']);
            $sheet->setCellValue('D' . $row, $exam['candidate_name']);
            $sheet->setCellValue('E' . $row, $exam['phone']);
            $sheet->setCellValue('F' . $row, $exam['birth_date']);
            $sheet->setCellValue('G' . $row, $exam['gender']);
            $sheet->setCellValue('H' . $row, $exam['exam_date']);
            // Resultado amigable
            $status = strtoupper(trim($exam['status'] ?? ''));
            if ($status === 'FINALIZADO') {
                $resultado = 'APTO';
            } elseif ($status === 'RECHAZADO') {
                $resultado = 'NO APTO';
            } elseif ($status === 'EN_CURSO') {
                $resultado = 'APLazADO';
            } else {
                $resultado = $exam['status'];
            }
            $sheet->setCellValue('I' . $row, $resultado);
            $sheet->setCellValue('J' . $row, $exam['order_number']);
            $row++;
            $sequence++;
        }

        // Ajustar columnas
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        if (ob_get_length()) {
            @ob_end_clean();
        }

        // Refrescar la salida para evitar contenido extra que pueda corromper el ZIP
        if (function_exists('ob_start')) {
            @ob_start();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        header('Expires: 0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    private function ensureAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
    }
}
