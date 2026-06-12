<?php

require_once __DIR__ . '/../helpers/Logger.php';
require_once __DIR__ . '/../helpers/FlashMessage.php';

/**
 * Controller for managing exams
 * Handles CRUD operations for exam records
 */
class ExamController
{
    private $examModel;

    /**
     * Constructor - initializes the exam model
     */
    public function __construct()
    {
        $this->examModel = new ExamModel();
    }

    /**
     * Display list of all exams
     * @return void
     */
    public function list()
    {
        $this->ensureAuth();
        try {
            $statusFilter = null;
            $statusDisplay = null;
            $allowedStatuses = [
                'FINALIZADO' => 'Apto',
                'RECHAZADO' => 'No apto',
                'PENDIENTE' => 'Pendiente',
                'EN_CURSO' => 'En curso',
                'SIN_RESULTADO' => 'Sin resultado',
            ];

            if (!empty($_GET['status'])) {
                $requestedStatus = strtoupper(trim($_GET['status']));
                if (array_key_exists($requestedStatus, $allowedStatuses)) {
                    $statusFilter = $requestedStatus;
                    $statusDisplay = $allowedStatuses[$requestedStatus];
                }
            }

            $exams = $this->examModel->all($statusFilter);
            Logger::info('Exams list displayed', ['count' => count($exams), 'status_filter' => $statusFilter]);
            include __DIR__ . '/../views/exams/list.php';
        } catch (Exception $e) {
            Logger::error('Error displaying exams list', ['error' => $e->getMessage()]);
            FlashMessage::set('error', 'Error al cargar la lista de exámenes.');
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }
    }

    /**
     * Display form to add new exam
     * @return void
     */
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
        $companyModel = new CompanyModel();
        $companies = $companyModel->all();
        $examTypes = $this->examModel->getExamTypes();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $companyInput = trim($_POST['company_id'] ?? '');
            $exam_type_id = intval($_POST['exam_type_id'] ?? 0);
            $order_number = trim($_POST['order_number'] ?? '');

            // Solo tratar como ID cuando el valor es un número entero puro.
            if (preg_match('/^[0-9]+$/', $companyInput)) {
                $company_id = intval($companyInput);
            } else {
                $company_id = 0;
            }

            if (!$company_id && $companyInput !== '') {
                $company_id = $companyModel->addIfNotExists($companyInput);
            }

            $candidateText = trim($_POST['candidate_text'] ?? '');
            $candidates = [];

            if (isset($_FILES['exam_file']) && !empty($_FILES['exam_file']['name'])) {
                if ($_FILES['exam_file']['error'] === UPLOAD_ERR_OK) {
                    $parsed = $this->parseExamFile($_FILES['exam_file']['tmp_name'], $_FILES['exam_file']['name']);
                    if (isset($parsed['error'])) {
                        $error = $parsed['error'];
                    } else {
                        $candidates = $parsed['candidates'];
                    }
                } else {
                    $error = 'Error al subir el archivo. Verifica que se haya cargado correctamente.';
                }
            }

            if (empty($candidates) && $candidateText !== '') {
                $candidates = $this->extractCandidates($candidateText);
            }

            // Validations
            $errors = [];

            if (empty($company_id)) {
                $errors[] = 'Debe seleccionar o ingresar una empresa válida.';
            }

            if (empty($exam_type_id)) {
                $errors[] = 'Debe seleccionar un tipo de examen.';
            }

            if (empty($order_number)) {
                $errors[] = 'El número de orden es obligatorio.';
            } elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', $order_number)) {
                $errors[] = 'El número de orden contiene caracteres no válidos.';
            }

            if (empty($candidates)) {
                $errors[] = 'Debe ingresar candidatos como texto o subir un archivo Excel/CSV.';
            }

            if (empty($errors)) {
                try {
                    foreach ($candidates as $candidate) {
                        // Validate candidate data
                        if (empty($candidate['name']) || empty($candidate['document_number'])) {
                            throw new Exception('Datos de candidato incompletos: nombre y documento son obligatorios.');
                        }

                        // Usar siempre el número de orden ingresado manualmente para todos los candidatos
                        $candidate['order_number'] = $order_number;
                        $status = $candidate['status'] ?? 'PENDIENTE';

                        $this->examModel->add(
                            $company_id,
                            $exam_type_id,
                            $candidate['name'],
                            $candidate['document_number'],
                            $candidate['phone'],
                            $candidate['gender'],
                            $candidate['birth_date'],
                            $candidate['exam_date'],
                            $candidate['order_number'],
                            $status
                        );
                    }

                    Logger::audit('Exam added', $_SESSION['user'] ?? 'unknown', [
                        'company_id' => $company_id,
                        'exam_type_id' => $exam_type_id,
                        'candidates_count' => count($candidates),
                        'order_number' => $order_number
                    ]);

                    FlashMessage::set('success', 'Examen agregado exitosamente con ' . count($candidates) . ' candidato(s).');
                    header('Location: index.php?c=dashboard&a=index');
                    exit;
                } catch (Exception $e) {
                    Logger::error('Error adding exam', ['error' => $e->getMessage(), 'data' => $_POST]);
                    $errors[] = 'Error al guardar el examen: ' . $e->getMessage();
                }
            }

            if (!empty($errors)) {
                $error = implode('<br>', $errors);
            }
        }

        include __DIR__ . '/../views/exams/add.php';
    }

    /**
     * Update exam status
     * @return void
     */
    public function status()
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if ($id && in_array($status, ['PENDIENTE','EN_CURSO','FINALIZADO','RECHAZADO','SIN_RESULTADO'])) {
                try {
                    $this->examModel->updateStatus($id, $status);
                    Logger::audit('Exam status updated', $_SESSION['user'] ?? 'unknown', [
                        'exam_id' => $id,
                        'new_status' => $status
                    ]);
                    FlashMessage::set('success', 'Estado del examen actualizado correctamente.');
                } catch (Exception $e) {
                    Logger::error('Error updating exam status', ['exam_id' => $id, 'error' => $e->getMessage()]);
                    FlashMessage::set('error', 'Error al actualizar el estado del examen.');
                }
            } else {
                FlashMessage::set('error', 'Datos inválidos para actualizar el estado.');
            }
        }

        header('Location: index.php?c=dashboard&a=index');
        exit;
    }

    /**
     * Delete an exam
     * @return void
     */
    public function delete()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            try {
                $exam = $this->examModel->findById($id);
                if ($exam) {
                    $this->examModel->delete($id);
                    Logger::audit('Exam deleted', $_SESSION['user'] ?? 'unknown', [
                        'exam_id' => $id,
                        'candidate_name' => $exam['candidate_name']
                    ]);
                    FlashMessage::set('success', 'Examen eliminado correctamente.');
                } else {
                    FlashMessage::set('error', 'Examen no encontrado.');
                }
            } catch (Exception $e) {
                Logger::error('Error deleting exam', ['exam_id' => $id, 'error' => $e->getMessage()]);
                FlashMessage::set('error', 'Error al eliminar el examen.');
            }
        } else {
            FlashMessage::set('error', 'ID de examen inválido.');
        }

        header('Location: index.php?c=exam&a=list');
        exit;
    }

    /**
     * Edit an exam
     * @return void
     */
    public function edit()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        $exam = $this->examModel->findById($id);

        if (!$exam) {
            FlashMessage::set('error', 'Examen no encontrado.');
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

            // Validations
            $errors = [];
            if (empty($candidate_name)) {
                $errors[] = 'El nombre del candidato es obligatorio.';
            }
            if (empty($document_number)) {
                $errors[] = 'El número de documento es obligatorio.';
            }
            if (!empty($gender) && !in_array($gender, ['M', 'F', 'O'])) {
                $errors[] = 'El género debe ser M, F u O.';
            }
            if (!empty($birth_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_date)) {
                $errors[] = 'La fecha de nacimiento debe tener formato YYYY-MM-DD.';
            }
            if (!empty($exam_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $exam_date)) {
                $errors[] = 'La fecha del examen debe tener formato YYYY-MM-DD.';
            }

            if (empty($errors)) {
                try {
                    $this->examModel->update($id, $candidate_name, $document_number, $phone, $gender, $birth_date, $exam_date, $order_number, $status);
                    Logger::audit('Exam updated', $_SESSION['user'] ?? 'unknown', [
                        'exam_id' => $id,
                        'candidate_name' => $candidate_name
                    ]);
                    FlashMessage::set('success', 'Examen actualizado correctamente.');
                    header('Location: index.php?c=exam&a=list');
                    exit;
                } catch (Exception $e) {
                    Logger::error('Error updating exam', ['exam_id' => $id, 'error' => $e->getMessage()]);
                    $error = 'Error al actualizar el examen: ' . $e->getMessage();
                }
            } else {
                $error = implode('<br>', $errors);
            }
        }

        include __DIR__ . '/../views/exams/edit.php';
    }

    /**
     * Ensure user is authenticated
     * @throws Exception If user is not authenticated
     */
    private function ensureAuth()
    {
        if (!isset($_SESSION['user'])) {
            Logger::warning('Unauthorized access attempt', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
            header('Location: index.php?c=auth&a=login');
            exit;
        }
    }

    /**
     * View exam details
     * @return void
     */
    public function view()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        $exam = $this->examModel->findById($id);

        if (!$exam) {
            header('Location: index.php?c=exam&a=list');
            exit;
        }

        include __DIR__ . '/../views/exams/view.php';
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
            $separatorUsed = null;

            foreach ($separators as $sep) {
                if (strpos($line, $sep) !== false) {
                    $parts = explode($sep, $line);
                    $separatorUsed = $sep;
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

            // Si la línea usa un separador claro (tab, ;, ,) y el primer campo es documento,
            // tomar el segundo campo completo como nombre.
            if ($separatorUsed !== null && count($parts) >= 2) {
                $firstClean = preg_replace('/\D/', '', $parts[0]);
                if (preg_match('/^\d{5,}$/', $firstClean)) {
                    $maybeName = $parts[1];
                    if (preg_match('/[A-Za-zÁÉÍÓÚÑáéíóúñ]/u', $maybeName)) {
                        $document_number = $firstClean;
                        $name = $maybeName;
                        unset($parts[0], $parts[1]);
                        $parts = array_values($parts);
                    }
                }
            }

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
            // Solo hacer esto si todavía no tenemos nombre completo del segundo campo.
            if ($name === null) {
                $nameParts = [];
                foreach ($parts as $idx => $part) {
                    $clean = preg_replace('/\D/', '', $part);
                    $isEmail = filter_var($part, FILTER_VALIDATE_EMAIL);
                    $isDate = $this->isValidDate($part);
                    $isGender = preg_match('/^[MF]$/i', $part);
                    $isPhone = preg_match('/^(3\d{7,9}|\d{7,13})$/', $clean);
                    $isStatus = preg_match('/\b(sin\s+resultado|apto|no\s+apto|reprobado|finalizado|completado|aplazado)\b/i', $part);
                    $isOrder = preg_match('/^\d{3,}$/', $clean);
                    // Palabras comunes en direcciones - EXPANDIDO para capturar mejor los nombres de lugares
                    // Incluye artículos (LA, EL, LOS, LAS), palabras de dirección y barrios comunes
                    $isSkipToken = preg_match('/\b(la|el|los|las|colombia|bogota|d\.c|activo|inactivo|usuarios?|barrio|conjunto|calle|carrera|diagonal|transversal|avenida|manzana|lote|edificio|piso|villa|casa|pent|bloque|sector|vereda|corregimiento|municipio|provincia|localidad|zona|región|estado|país|ciudad|ap|pcia|depto|dept|decad|humano|suba|chapinero|usaquen|la\s+candelaria|san\s+cristobal|engativa|puente|aranda|teusaquillo|santa\s+fe|los\s+mártires|antonio|nariño|rafael|uribe|libertadores|jerusalen|kennedy|fontibón|bosa|tunjuelito)\b/i', $part);

                    // Limitar nombre a máximo 4 tokens para evitar capturar direcciones
                    if (count($nameParts) >= 4 || $isEmail || $isDate || $isGender || $isPhone || $isStatus || $isOrder || $isSkipToken || ($clean !== '' && preg_match('/^\d+$/', $part) && strlen($part) <= 3)) {
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
                if ($low === 'sin' && isset($parts[$idx + 1]) && mb_strtolower(trim($parts[$idx + 1])) === 'resultado') {
                    $foundStatus = 'SIN_RESULTADO';
                    unset($parts[$idx], $parts[$idx + 1]);
                    break;
                }
                if (preg_match('/\b(sin\s+resultado|apto|no\s+apto|reprobado|finalizado|completado|aplazado)\b/i', $part)) {
                    if (stripos($low, 'no apto') !== false || stripos($low, 'reprobado') !== false) {
                        $foundStatus = 'RECHAZADO';
                    } elseif (stripos($low, 'sin resultado') !== false || stripos($low, 'sinresultado') !== false) {
                        $foundStatus = 'SIN_RESULTADO';
                    } elseif (stripos($low, 'aplazado') !== false || stripos($low, 'en curso') !== false) {
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

            // 5) Teléfono (ANTES que orden para evitar que se capture como número de orden)
            foreach ($parts as $idx => $part) {
                $clean = preg_replace('/\D/', '', $part);
                if (!$phone && preg_match('/^(3\d{7,9}|\d{7,10})$/', $clean)) {
                    $phone = $clean;
                    unset($parts[$idx]);
                    break;
                }
            }

            // 6) Orden numérico al final (>= 3 dígitos y distinto de documento y teléfono)
            foreach ($parts as $idx => $part) {
                $clean = preg_replace('/\D/', '', $part);
                if (!$order_number && preg_match('/^\d{3,}$/', $clean) && $clean !== $document_number && $clean !== $phone) {
                    $order_number = $clean;
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

    private function parseExamFile(string $tmpFile, string $originalName)
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        try {
            if ($extension === 'csv') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setInputEncoding('UTF-8');
                $reader->setDelimiter($this->detectCsvDelimiter($tmpFile));
                $reader->setEnclosure('"');
                $spreadsheet = $reader->load($tmpFile);
            } else {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpFile);
                $spreadsheet = $reader->load($tmpFile);
            }
        } catch (\Exception $e) {
            return ['error' => 'No se pudo leer el archivo. Usa un archivo Excel (.xlsx, .xls) o CSV válido.'];
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        if ($highestRow < 2) {
            return ['candidates' => []];
        }

        $headers = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = trim((string) $worksheet->getCellByColumnAndRow($col, 1)->getValue());
            $headers[$col] = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $cellValue));
        }

        $mapping = [
            'name' => ['nombre', 'candidate', 'candidato', 'full_name', 'nombre_completo'],
            'document_number' => ['documento', 'cedula', 'id', 'identificacion'],
            'phone' => ['telefono', 'teléfono', 'phone', 'celular', 'mobile'],
            'gender' => ['genero', 'género', 'sexo', 'gender', 'sex'],
            'birth_date' => ['fecha_nacimiento', 'nacimiento', 'birth_date', 'birthdate'],
            'exam_date' => ['fecha_examen', 'fecha_de_examen', 'fecha_creacion', 'exam_date', 'fecha', 'date'],
            'status' => ['resultado', 'result', 'estado', 'status'],
            'order_number' => ['consecutivo_bolsa', 'consecutivo', 'orden', 'numero_orden', 'order_number', 'order']
        ];

        $colMap = [];
        foreach ($mapping as $key => $labels) {
            $bestScore = 0;
            $bestCol = null;
            foreach ($headers as $col => $header) {
                foreach ($labels as $label) {
                    if ($header === $label) {
                        $score = 300 + strlen($label);
                    } elseif (strpos($header, $label) !== false) {
                        $score = 100 + strlen($label);
                    } else {
                        continue;
                    }

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestCol = $col;
                    }
                }
            }
            if ($bestCol !== null) {
                $colMap[$key] = $bestCol;
            }
        }

        if (empty($colMap['name']) && $highestColumnIndex >= 1) {
            $colMap['name'] = 1;
        }
        if (empty($colMap['document_number']) && $highestColumnIndex >= 2) {
            $colMap['document_number'] = 2;
        }
        if (empty($colMap['phone']) && $highestColumnIndex >= 3) {
            $colMap['phone'] = 3;
        }
        if (empty($colMap['gender']) && $highestColumnIndex >= 4) {
            $colMap['gender'] = 4;
        }
        if (empty($colMap['birth_date']) && $highestColumnIndex >= 5) {
            $colMap['birth_date'] = 5;
        }
        if (empty($colMap['exam_date']) && $highestColumnIndex >= 6) {
            $colMap['exam_date'] = 6;
        }
        if (empty($colMap['status']) && $highestColumnIndex >= 7) {
            $colMap['status'] = 7;
        }
        if (empty($colMap['order_number']) && $highestColumnIndex >= 8) {
            $colMap['order_number'] = 8;
        }

        $candidates = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $candidate = [
                'name' => '',
                'document_number' => '',
                'phone' => '',
                'gender' => '',
                'birth_date' => '',
                'exam_date' => '',
                'status' => 'PENDIENTE',
                'order_number' => ''
            ];

            foreach ($candidate as $key => $value) {
                if (!empty($colMap[$key])) {
                    $cell = $worksheet->getCellByColumnAndRow($colMap[$key], $row);
                    if (in_array($key, ['birth_date', 'exam_date'], true)) {
                        $candidate[$key] = $this->normalizeSpreadsheetDate($cell);
                    } else {
                        $candidate[$key] = trim((string) $cell->getValue());
                    }
                }
            }

            if ($candidate['gender'] !== '') {
                $genderClean = strtoupper(substr(trim($candidate['gender']), 0, 1));
                if (!in_array($genderClean, ['M', 'F'], true)) {
                    $genderClean = '';
                }
                $candidate['gender'] = $genderClean;
            }

            $candidate['status'] = $this->normalizeStatus($candidate['status']);

            // Limpiar y extraer solo el nombre (evitar direcciones, emails, barrios)
            $candidate['name'] = $this->sanitizeCandidateName($candidate['name']);

            if ($candidate['name'] === '' && $candidate['document_number'] === '') {
                continue;
            }

            $candidates[] = $candidate;
        }

        return ['candidates' => $candidates];
    }

    private function normalizeSpreadsheetDate($cell)
    {
        $value = $cell->getValue();
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value) && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date ? $date->format('Y-m-d') : '';
            } catch (\Exception $e) {
                // continuar con texto
            }
        }

        return $this->parseDate(trim((string) $value))?->format('Y-m-d') ?? '';
    }

    private function normalizeStatus(string $status)
    {
        $clean = strtolower(trim($status));
        if ($clean === '') {
            return 'PENDIENTE';
        }
        if (str_contains($clean, 'no apto') || str_contains($clean, 'reprobado') || str_contains($clean, 'rechazado')) {
            return 'RECHAZADO';
        }
        if (str_contains($clean, 'sin resultado') || str_contains($clean, 'sinresultado')) {
            return 'SIN_RESULTADO';
        }
        if (str_contains($clean, 'aplaz') || str_contains($clean, 'en curso')) {
            return 'EN_CURSO';
        }
        if (str_contains($clean, 'apto') || str_contains($clean, 'finalizado') || str_contains($clean, 'completado')) {
            return 'FINALIZADO';
        }

        return strtoupper($clean);
    }

    private function detectCsvDelimiter(string $filePath, $sampleLines = 5)
    {
        $delimiters = ["\t", ';', ','];
        $counts = array_fill_keys($delimiters, 0);

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return ',';
        }

        $lineNum = 0;
        while (($line = fgets($handle)) !== false && $lineNum < $sampleLines) {
            $lineNum++;
            foreach ($delimiters as $delimiter) {
                $counts[$delimiter] += substr_count($line, $delimiter);
            }
        }

        fclose($handle);
        arsort($counts);
        $best = key($counts);
        return $best ?: ',';
    }

    private function sanitizeCandidateName($value)
    {
        $value = $value ?? '';
        $value = trim((string) $value);
        if ($value === '') return '';

        // Normalizar espacios y separadores
        $value = preg_replace('/[\r\n\t]+/', ' ', $value);
        $value = preg_replace('/\s{2,}/u', ' ', $value);
        $value = preg_replace('/\s+\+\s+/u', ' ', $value);

        // Eliminar correo electrónico si existe
        if (strpos($value, '@') !== false) {
            $parts = preg_split('/\s+/', $value);
            foreach ($parts as $i => $p) {
                if (strpos($p, '@') !== false) {
                    // mantener solo lo previo al email
                    $value = trim(implode(' ', array_slice($parts, 0, $i)));
                    break;
                }
            }
        }

        // Lista de palabras que indican inicio de dirección/ubicación
        $stopWords = [
            'BARRIO','BARIO','CONJUNTO','PORTAL','TORRE','APTO','MESA','LOCALIDAD','URB','URBANIZACION',
            'URB.','URBANO','CALLE','CLL','CARRERA','CRA','AVENIDA','AV','MANZANA','BLOQUE','SECTOR','COL',
            'COLOMBIA','CUNDINAMARCA','LA','EL','LOS','LAS','VILLA','CASA','EDIF','EDIFICIO'
        ];

        $tokens = preg_split('/\s+/u', $value);
        $nameParts = [];

        foreach ($tokens as $token) {
            $t = trim($token, ",.:;()[]\"'\x{2019}");
            if ($t === '') break;

            // Si contiene dígitos o símbolos que no son comunes en nombres, cortar
            if (preg_match('/\d/', $t)) break;
            if (strpos($t, '@') !== false) break;

            // Si token es una stopWord (insensible a mayúsculas), cortar
            if (in_array(mb_strtoupper($t, 'UTF-8'), $stopWords, true)) break;

            // Aceptar token si son letras, acentos, guiones o apóstrofes
            if (!preg_match('/^[\p{L}\.\-\'\u2019]+$/u', $t)) break;

            $nameParts[] = $t;

            // Limitar tokens del nombre a 5 por seguridad
            if (count($nameParts) >= 5) break;
        }

        if (!empty($nameParts)) {
            return trim(implode(' ', $nameParts));
        }

        // Fallback: truncar en la primera stopWord encontrada
        $minPos = null;
        foreach ($stopWords as $w) {
            $pos = stripos($value, $w);
            if ($pos !== false && ($minPos === null || $pos < $minPos)) {
                $minPos = $pos;
            }
        }
        if ($minPos !== null) {
            $candidate = trim(substr($value, 0, $minPos));
            return rtrim($candidate, ",.:;\-_/\\");
        }

        return $value;
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
                $resultado = 'APLAZADO';
            } elseif ($status === 'SIN_RESULTADO') {
                $resultado = 'SIN RESULTADO';
            } else {
                $resultado = $status ?: 'PENDIENTE';
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
}
