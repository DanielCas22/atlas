<?php

class DashboardController
{
    public function index()
    {
        $this->ensureAuth();

        $user = $_SESSION['user'];
        $examModel = new ExamModel();
        $companyModel = new CompanyModel();
        $userModel = new UserModel();

        $exams = $examModel->all();
        $companyCount = $companyModel->countAll();
        $examCount = $examModel->countAll();
        $userCount = $userModel->countAll();

        $classifiedCompanies = 0;
        $reportFiles = 0;
        $baseDir = __DIR__ . '/../empresas_clasificadas';
        if (is_dir($baseDir)) {
            foreach (scandir($baseDir) as $companyDir) {
                if ($companyDir === '.' || $companyDir === '..') {
                    continue;
                }

                $companyPath = $baseDir . '/' . $companyDir;
                if (!is_dir($companyPath)) {
                    continue;
                }

                $classifiedCompanies++;
                $reportDir = $companyPath . '/REPORTE GUARDA';
                if (is_dir($reportDir)) {
                    foreach (scandir($reportDir) as $file) {
                        if ($file === '.' || $file === '..') {
                            continue;
                        }
                        $filePath = $reportDir . '/' . $file;
                        if (is_file($filePath)) {
                            $reportFiles++;
                        }
                    }
                }
            }
        }

        include __DIR__ . '/../views/dashboard/index.php';
    }

    public function users()
    {
        $this->ensureAuth();

        $userModel = new UserModel();
        $users = $userModel->all();

        include __DIR__ . '/../views/dashboard/users.php';
    }

    public function estadisticas()
    {
        $this->ensureAuth();

        $examModel = new ExamModel();
        $companyModel = new CompanyModel();

        $totalExams = $examModel->countAll();
        $registeredCompanies = $companyModel->countAll();
        $activeCompanies = $companyModel->countCompaniesWithExams();
        $resultSummary = $examModel->getResultSummary();
        $statusSummary = $examModel->getStatusSummary();
        $topCompanies = $examModel->getTopCompaniesByExams(5);
        $topExamTypes = $examModel->getTopExamTypes(5);

        $statusLabels = [
            'FINALIZADO' => 'Finalizado',
            'RECHAZADO' => 'Rechazado',
        ];

        $statusTotals = array_column($statusSummary, 'total', 'status');
        $statusSummary = [];
        foreach ($statusLabels as $statusCode => $statusLabel) {
            $statusSummary[] = [
                'status' => $statusCode,
                'label' => $statusLabel,
                'total' => intval($statusTotals[$statusCode] ?? 0),
            ];
        }

        include __DIR__ . '/../views/dashboard/estadisticas.php';
    }

    public function estadisticasData()
    {
        $this->ensureAuth();

        $examModel = new ExamModel();
        $companyModel = new CompanyModel();

        $totalExams = $examModel->countAll();
        $registeredCompanies = $companyModel->countAll();
        $activeCompanies = $companyModel->countCompaniesWithExams();
        $resultSummary = $examModel->getResultSummary();
        $statusSummaryRaw = $examModel->getStatusSummary();
        $topCompanies = $examModel->getTopCompaniesByExams(5);

        $statusLabels = [
            'FINALIZADO' => 'Finalizado',
            'RECHAZADO' => 'Rechazado',
        ];

        $statusTotals = array_column($statusSummaryRaw, 'total', 'status');
        $statusSummary = [];
        foreach ($statusLabels as $statusCode => $statusLabel) {
            $statusSummary[] = [
                'status' => $statusCode,
                'label' => $statusLabel,
                'total' => intval($statusTotals[$statusCode] ?? 0),
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'totalExams' => intval($totalExams),
            'registeredCompanies' => intval($registeredCompanies),
            'activeCompanies' => intval($activeCompanies),
            'resultSummary' => [
                'aptos' => intval($resultSummary['aptos'] ?? 0),
                'no_aptos' => intval($resultSummary['no_aptos'] ?? 0),
                'pendientes' => intval($resultSummary['pendientes'] ?? 0),
                'en_curso' => intval($resultSummary['en_curso'] ?? 0),
                'sin_resultado' => intval($resultSummary['sin_resultado'] ?? 0),
            ],
            'statusSummary' => $statusSummary,
            'topCompanies' => $topCompanies,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function getRoleOptions()
    {
        return [
            1 => 'Administrador',
            2 => 'Supervisor',
            4 => 'Coordinador de área',
            3 => 'Trabajador',
        ];
    }

    public function editUser()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: index.php?c=dashboard&a=users');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findById($id);
        if (!$user) {
            header('Location: index.php?c=dashboard&a=users');
            exit;
        }

        $roleOptions = $this->getRoleOptions();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'fullname' => trim($_POST['nombres'] ?? '') . ' ' . trim($_POST['apellidos'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role_id' => intval($_POST['role_id'] ?? 0),
            ];

            if ($data['fullname'] && $data['email'] && $data['username'] && $data['role_id'] && isset($roleOptions[$data['role_id']])) {
                if (!$data['password']) {
                    unset($data['password']);
                }

                $userModel->update($id, $data);
                header('Location: index.php?c=dashboard&a=users');
                exit;
            }

            $error = 'Complete todos los campos obligatorios';
            $user = array_merge($user, $_POST); // keep submitted values
        }

        include __DIR__ . '/../views/dashboard/edit_user.php';
    }

    public function deleteUser()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $userModel = new UserModel();
            $userModel->delete($id);
        }

        header('Location: index.php?c=dashboard&a=users');
        exit;
    }

    public function createUser()
    {
        $this->ensureAuth();

        $roleOptions = $this->getRoleOptions();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombres' => trim($_POST['nombres'] ?? ''),
                'apellidos' => trim($_POST['apellidos'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? ''),
                'numero_documento' => trim($_POST['numero_documento'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'role_id' => intval($_POST['role_id'] ?? 0),
            ];

            if ($data['nombres'] && $data['apellidos'] && $data['tipo_documento'] && $data['numero_documento'] && $data['telefono'] && $data['email'] && $data['username'] && $data['password'] && $data['role_id'] && isset($roleOptions[$data['role_id']])) {
                // Asegurar que el rol existe en roles.
                $pdo = Database::getInstance()->getConnection();
                $stmtRole = $pdo->prepare('SELECT id FROM roles WHERE id = ?');
                $stmtRole->execute([$data['role_id']]);

                if (!$stmtRole->fetch()) {
                    $stmtInsertRole = $pdo->prepare('INSERT INTO roles (id, name, description) VALUES (?, ?, ?)');
                    $stmtInsertRole->execute([$data['role_id'], $roleOptions[$data['role_id']], 'Rol creado automáticamente']);
                }

                // Verificar si el usuario ya existe
                $stmtUserExists = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $stmtUserExists->execute([$data['username']]);
                if ($stmtUserExists->fetch()) {
                    $error = 'El nombre de usuario ya existe. Elige otro nombre de usuario.';
                } else {
                    try {
                        $stmt = $pdo->prepare('INSERT INTO users (role_id, username, password, fullname, email) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute([
                            $data['role_id'],
                            $data['username'],
                            password_hash($data['password'], PASSWORD_BCRYPT),
                            $data['nombres'] . ' ' . $data['apellidos'],
                            $data['email'],
                        ]);
                    } catch (PDOException $e) {
                        $errorInfo = $e->errorInfo[1] ?? null;
                        if (in_array($errorInfo, [1062, 19]) || stripos($e->getMessage(), 'duplicate') !== false) {
                            $error = 'El nombre de usuario ya existe. Elige otro nombre de usuario.';
                        } else {
                            throw $e;
                        }
                    }

                    if (empty($error)) {
                        $to = $data['email'];
                        $subject = 'Bienvenido a Atlas - Registro exitoso';
                        $message = "Hola {$data['nombres']} {$data['apellidos']},\n\n" .
                                   "Tu usuario ha sido creado correctamente en la plataforma Atlas.\n" .
                                   "Datos de acceso:\n" .
                                   "Usuario: {$data['username']}\n" .
                                   "Rol: {$roleOptions[$data['role_id']]}\n" .
                                   "(Conserva tu contraseña en un lugar seguro).\n\n" .
                                   "Gracias por registrarte.\n" .
                                   "Equipo Atlas\n";
                        $headers = "From: atlas@tu-dominio.com\r\n" .
                                   "Reply-To: atlas@tu-dominio.com\r\n" .
                                   "Content-Type: text/plain; charset=UTF-8\r\n";

                        if (!mail($to, $subject, $message, $headers)) {
                            $error = 'Usuario creado, pero no se pudo enviar el correo de confirmación. Verifique la configuración de email.';
                        }

                        if (empty($error)) {
                            header('Location: index.php?c=dashboard&a=users');
                            exit;
                        }
                    }
                }
            } else {
                $error = 'Complete todos los campos correctamente.';
            }
        }

        include __DIR__ . '/../views/dashboard/create_user.php';
    }

    public function clasificacion_empresas()
    {
        $this->ensureAuth();

        $results = null;
        $error = null;
        $success = null;
        $syncSummary = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que se haya subido un archivo
            if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Por favor selecciona un archivo válido.';
            } else {
                try {
                    $filePath = $_FILES['excel_file']['tmp_name'];
                    $fileName = $_FILES['excel_file']['name'];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // Validar extensión
                    if (!in_array($fileExt, ['xlsx', 'xls', 'csv'])) {
                        $error = 'Formato de archivo no soportado. Usa Excel (.xlsx, .xls) o CSV.';
                    } else {
                        $columns = [
                            'company' => trim($_POST['company_column'] ?? 'A'),
                            'name' => trim($_POST['name_column'] ?? 'B'),
                            'document' => trim($_POST['document_column'] ?? ''),
                            'phone' => trim($_POST['phone_column'] ?? ''),
                            'gender' => trim($_POST['gender_column'] ?? ''),
                            'birth' => trim($_POST['birth_column'] ?? ''),
                            'exam_date' => trim($_POST['exam_date_column'] ?? ''),
                            'result' => trim($_POST['result_column'] ?? ''),
                        ];

                        if (empty($columns['company'])) {
                            $error = 'Especifica la columna de empresa.';
                        } else {
                            // Procesar el archivo
                            try {
                                $results = $this->processExcelFile($filePath, $columns, $fileExt);
                                if (is_array($results) && !empty($results)) {
                                    $syncSummary = $this->syncCompaniesFromList(array_keys($results));
                                    $success = 'Archivo procesado correctamente. Se encontraron ' . count($results) . ' empresas. Los pacientes han sido clasificados y guardados en carpetas.';
                                } else {
                                    $error = 'El archivo no contiene datos válidos.';
                                }
                            } catch (Exception $e) {
                                $error = $e->getMessage();
                            }
                        }
                    }
                } catch (Exception $e) {
                    $error = 'Error al procesar el archivo: ' . $e->getMessage();
                }
            }
        }

        include __DIR__ . '/../views/dashboard/clasificacion_empresas.php';
    }

    public function syncClassifiedCompanies()
    {
        $this->ensureAuth();

        $syncSummary = $this->syncCompaniesFromFolders();
        $success = 'Sincronización completada. Empresas importadas: ' . $syncSummary['added'] . ', existentes: ' . $syncSummary['existing'];
        if ($syncSummary['failed'] > 0) {
            $success .= ', errores: ' . $syncSummary['failed'];
        }

        $results = null;
        $error = null;

        include __DIR__ . '/../views/dashboard/clasificacion_empresas.php';
    }

    private function syncCompaniesFromList(array $companyNames)
    {
        $companyModel = new CompanyModel();
        $summary = [
            'added' => 0,
            'existing' => 0,
            'failed' => 0,
        ];

        foreach ($companyNames as $companyName) {
            $companyName = trim($companyName);
            if ($companyName === '') {
                continue;
            }

            try {
                if ($companyModel->findByName($companyName)) {
                    $summary['existing']++;
                } else {
                    $companyModel->add($companyName);
                    $summary['added']++;
                }
            } catch (Exception $e) {
                error_log('Error sincronizando empresa: ' . $e->getMessage());
                $summary['failed']++;
            }
        }

        return $summary;
    }

    private function syncCompaniesFromFolders()
    {
        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $companyNames = [];

        if (is_dir($baseDir)) {
            $companyDirs = scandir($baseDir);
            foreach ($companyDirs as $companyDir) {
                if ($companyDir === '.' || $companyDir === '..') {
                    continue;
                }

                $companyPath = $baseDir . '/' . $companyDir;
                if (is_dir($companyPath)) {
                    $companyNames[] = $companyDir;
                }
            }
        }

        return $this->syncCompaniesFromList($companyNames);
    }

    public function files()
    {
        $this->ensureAuth();

        $companyModel = new CompanyModel();
        $companyModel->syncClassifiedCompanies();

        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $companies = [];
        $processedDirs = []; // Rastrear directorios procesados

        if (is_dir($baseDir)) {
            $companyDirs = scandir($baseDir);
            foreach ($companyDirs as $companyDir) {
                if ($companyDir === '.' || $companyDir === '..') continue;

                $companyPath = $baseDir . '/' . $companyDir;
                if (!is_dir($companyPath)) {
                    continue;
                }

                // Evitar procesar la misma carpeta normalizada dos veces
                $normalizedDir = $companyModel->normalizeCompanyName($companyDir);
                if (in_array($normalizedDir, $processedDirs)) {
                    continue;
                }
                $processedDirs[] = $normalizedDir;

                $displayName = $this->normalizeClassifiedCompanyName($companyDir);
                if ($displayName === '') {
                    $displayName = $companyDir;
                }

                if (!isset($companies[$displayName])) {
                    $companies[$displayName] = [
                        'display_name' => $displayName,
                        'company_dirs' => []
                    ];
                }

                $companies[$displayName]['company_dirs'][$companyDir] = [
                    'folders' => []
                ];

                // Solo buscar carpeta REPORTE GUARDA
                $reportGuardaPath = $companyPath . '/REPORTE GUARDA';
                if (is_dir($reportGuardaPath)) {
                    $companies[$displayName]['company_dirs'][$companyDir]['folders']['REPORTE GUARDA'] = [
                        'files' => []
                    ];

                    $files = scandir($reportGuardaPath);
                    foreach ($files as $file) {
                        if ($file === '.' || $file === '..') continue;

                        $filePath = $reportGuardaPath . '/' . $file;
                        if (is_file($filePath)) {
                        $metadata = $this->getReportFileMetadata($filePath);
                        $companies[$displayName]['company_dirs'][$companyDir]['folders']['REPORTE GUARDA']['files'][] = [
                            'name' => $file,
                            'size' => filesize($filePath),
                            'modified' => date('d/m/Y H:i', filemtime($filePath)),
                            'order_number' => $metadata['order_number'] ?? '',
                            'order_capacity' => $metadata['order_capacity'] ?? '',
                        ];
                    }
                    }
                }
            }
        }

        include __DIR__ . '/../views/dashboard/files.php';
    }

    public function updateClassifiedFileMetadata()
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=dashboard&a=files&error=Solicitud inválida');
            exit;
        }

        $company = $_POST['company'] ?? '';
        $folder = $_POST['folder'] ?? '';
        $file = $_POST['file'] ?? '';
        $orderNumber = trim($_POST['order_number'] ?? '');
        $orderCapacity = trim($_POST['order_capacity'] ?? '');

        if (!$company || !$folder || !$file) {
            header('Location: index.php?c=dashboard&a=files&error=' . urlencode('Parámetros inválidos.'));
            exit;
        }

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $filePath = $baseDir . '/' . $company . '/' . $folder . '/' . $file;

        if (!file_exists($filePath) || !is_file($filePath)) {
            header('Location: index.php?c=dashboard&a=files&error=' . urlencode('Archivo no encontrado.'));
            exit;
        }

        try {
            $this->saveReportFileMetadata($filePath, $orderNumber, $orderCapacity);
            header('Location: index.php?c=dashboard&a=files&success=' . urlencode('Metadata de orden y cupos actualizada correctamente.'));
            exit;
        } catch (Exception $e) {
            header('Location: index.php?c=dashboard&a=files&error=' . urlencode('No se pudo actualizar la metadata: ' . $e->getMessage()));
            exit;
        }
    }

    private function getReportFileMetadata(string $filePath): array
    {
        $metadata = ['order_number' => '', 'order_capacity' => ''];

        if (!is_file($filePath)) {
            return $metadata;
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $metadataText = trim((string)$sheet->getCell('A3')->getValue());

            if (stripos($metadataText, 'ORDEN:') !== false) {
                if (preg_match('#ORDEN:\s*(\d+)\s*\/\s*CUPOS:\s*(\d+)#i', $metadataText, $matches)) {
                    $metadata['order_number'] = trim($matches[1]);
                    $metadata['order_capacity'] = trim($matches[2]);
                }
            }
        } catch (Exception $e) {
            // ignorar errores de lectura y devolver valores vacíos
        }

        return $metadata;
    }

    private function saveReportFileMetadata(string $filePath, string $orderNumber, string $orderCapacity)
    {
        if (!is_file($filePath)) {
            throw new Exception('Archivo no encontrado');
        }

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A3', 'ORDEN: ' . $orderNumber . ' / CUPOS: ' . $orderCapacity);
            $this->applyOrderNumberToReportRows($sheet, $orderNumber, (int)$orderCapacity);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);
        } catch (Exception $e) {
            throw new Exception('No se pudo guardar la metadata: ' . $e->getMessage());
        }
    }

    private function findEmptyOrderSlotRow($sheet, string $orderNumber, int $dataStartRow)
    {
        $highestRow = $sheet->getHighestRow();

        for ($rowNum = $dataStartRow; $rowNum <= $highestRow; $rowNum++) {
            $orderValue = trim((string)$sheet->getCell('A' . $rowNum)->getValue());
            $nameValue = trim((string)$sheet->getCell('B' . $rowNum)->getValue());

            if ($orderValue === $orderNumber && $nameValue === '') {
                return $rowNum;
            }
        }

        return null;
    }

    /**
     * Buscar la primera fila reservada para cualquier orden (A tiene valor y B está vacío)
     */
    private function findFirstReservedSlot($sheet, int $dataStartRow)
    {
        $highestRow = $sheet->getHighestRow();

        for ($rowNum = $dataStartRow; $rowNum <= $highestRow; $rowNum++) {
            $orderValue = trim((string)$sheet->getCell('A' . $rowNum)->getValue());
            $nameValue = trim((string)$sheet->getCell('B' . $rowNum)->getValue());

            if ($orderValue !== '' && $nameValue === '') {
                return $rowNum;
            }
        }

        return null;
    }

    /**
     * Contar cuántos pacientes ya están asignados (nombre no vacío) a una orden dada
     */
    private function countAssignedForOrder($sheet, string $orderNumber)
    {
        $count = 0;
        $highestRow = $sheet->getHighestRow();

        for ($rowNum = 5; $rowNum <= $highestRow; $rowNum++) {
            $orderValue = trim((string)$sheet->getCell('A' . $rowNum)->getValue());
            if ($orderValue !== $orderNumber) continue;
            $nameValue = trim((string)$sheet->getCell('B' . $rowNum)->getValue());
            if ($nameValue !== '') $count++;
        }

        return $count;
    }

    private function applyOrderNumberToReportRows($sheet, string $orderNumber, int $orderCapacity)
    {
        if (empty($orderNumber) || $orderCapacity <= 0) {
            return;
        }

        $highestRow = $sheet->getHighestRow();
        $assigned = 0;
        $dataStartRow = 5; // Encabezados en la fila 4

        for ($rowNum = $dataStartRow; $rowNum <= $highestRow; $rowNum++) {
            $orderValue = trim((string)$sheet->getCell('A' . $rowNum)->getValue());
            $rowHasData = false;
            foreach (range('B', 'I') as $colLetter) {
                $cellValue = trim((string)$sheet->getCell($colLetter . $rowNum)->getValue());
                if ($cellValue !== '') {
                    $rowHasData = true;
                    break;
                }
            }

            if (!$rowHasData) {
                continue;
            }

            if ($orderValue !== '' && $orderValue !== $orderNumber) {
                continue;
            }

            if ($orderValue === $orderNumber) {
                $assigned++;
                continue;
            }

            if ($assigned < $orderCapacity) {
                $sheet->setCellValue('A' . $rowNum, $orderNumber);
                $assigned++;
            }
        }

        if ($assigned < $orderCapacity) {
            $lastRowOrder = trim((string)$sheet->getCell('A' . $highestRow)->getValue());
            // Agregar una fila separadora SOLO si la última fila contiene una orden distinta
            if ($highestRow > 4 && $lastRowOrder !== '' && $lastRowOrder !== $orderNumber) {
                $highestRow++;
            }
        }

        while ($assigned < $orderCapacity) {
            $highestRow++;
            $sheet->setCellValue('A' . $highestRow, $orderNumber);
            foreach (range('B', 'I') as $colLetter) {
                $sheet->setCellValue($colLetter . $highestRow, '');
            }
            $assigned++;
        }
    }

    private function rowHasAnyValue($sheet, int $rowNum)
    {
        foreach (range('A', 'I') as $colLetter) {
            if (trim((string)$sheet->getCell($colLetter . $rowNum)->getValue()) !== '') {
                return true;
            }
        }

        return false;
    }

    private function cleanupCompanyReportFiles($reportDir, $company, $keepFilePath)
    {
        $baseName = $this->sanitizeFileName($company);
        $pattern = $reportDir . '/' . $baseName . '*.xlsx';
        foreach (glob($pattern) as $file) {
            if ($file !== $keepFilePath && is_file($file)) {
                @unlink($file);
            }
        }
    }

    private function normalizeClassifiedCompanyName(string $companyName)
    {
        $clean = preg_replace('/^LISTADO[ _-]*/iu', '', $companyName);
        $clean = str_replace('_', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return trim($clean);
    }

    public function cleanClassifiedCompanies()
    {
        $this->ensureAuth();

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        if (!is_dir($baseDir)) {
            header('Location: index.php?c=dashboard&a=files&error=' . urlencode('El directorio de empresas clasificadas no existe.'));
            exit;
        }

        $renamed = 0;
        $errors = [];
        $companyDirs = scandir($baseDir);

        foreach ($companyDirs as $companyDir) {
            if ($companyDir === '.' || $companyDir === '..') {
                continue;
            }

            $companyPath = $baseDir . '/' . $companyDir;
            if (!is_dir($companyPath)) {
                continue;
            }

            $cleanName = $this->normalizeClassifiedCompanyName($companyDir);
            if ($cleanName === '' || $cleanName === $companyDir) {
                continue;
            }

            $targetPath = $baseDir . '/' . $cleanName;
            if (is_dir($targetPath)) {
                $errors[] = "La carpeta ya existe: $cleanName";
                continue;
            }

            if (rename($companyPath, $targetPath)) {
                $renamed++;
            } else {
                $errors[] = "No se pudo renombrar: $companyDir";
            }
        }

        $message = "Nombres limpiados. Carpetas renombradas: $renamed.";
        if (!empty($errors)) {
            $message .= ' Errores: ' . implode(' / ', $errors);
        }

        header('Location: index.php?c=dashboard&a=files&success=' . urlencode($message));
        exit;
    }

    public function download()
    {
        $this->ensureAuth();

        $company = $_GET['company'] ?? '';
        $folder = $_GET['folder'] ?? '';
        $file = $_GET['file'] ?? '';

        if (!$company || !$folder || !$file) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Parámetros inválidos';
            exit;
        }

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $filePath = $baseDir . '/' . $company . '/' . $folder . '/' . $file;

        // Validar que el archivo existe y está dentro del directorio permitido
        if (!file_exists($filePath) || !is_file($filePath)) {
            header('HTTP/1.1 404 Not Found');
            echo 'Archivo no encontrado';
            exit;
        }

        // Verificar que la ruta está dentro de empresas_clasificadas
        $realPath = realpath($filePath);
        $realBaseDir = realpath($baseDir);
        if (strpos($realPath, $realBaseDir) !== 0) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Acceso denegado';
            exit;
        }

        // Enviar el archivo
        if (ob_get_level()) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        flush();
        readfile($filePath);
        exit;
    }

    public function deleteFile()
    {
        $this->ensureAuth();

        $company = $_GET['company'] ?? '';
        $folder = $_GET['folder'] ?? '';
        $file = $_GET['file'] ?? '';

        if (!$company || !$folder || !$file) {
            header('Location: index.php?c=dashboard&a=files&error=Parámetros inválidos');
            exit;
        }

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $filePath = $baseDir . '/' . $company . '/' . $folder . '/' . $file;

        // Validar que el archivo existe y está dentro del directorio permitido
        if (!file_exists($filePath) || !is_file($filePath)) {
            header('Location: index.php?c=dashboard&a=files&error=Archivo no encontrado: ' . htmlspecialchars($filePath));
            exit;
        }

        // Verificar que la ruta está dentro de empresas_clasificadas
        $realPath = realpath($filePath);
        $realBaseDir = realpath($baseDir);
        if (strpos($realPath, $realBaseDir) !== 0) {
            header('Location: index.php?c=dashboard&a=files&error=Acceso denegado a la ruta');
            exit;
        }

        // Verificar permisos de escritura en el directorio
        $dir = dirname($filePath);
        if (!is_writable($dir)) {
            header('Location: index.php?c=dashboard&a=files&error=El directorio no tiene permisos de escritura: ' . htmlspecialchars($dir));
            exit;
        }

        // Intentar eliminar el archivo
        if (unlink($filePath)) {
            header('Location: index.php?c=dashboard&a=files&success=Archivo eliminado correctamente');
        } else {
            $error = error_get_last();
            $errorMsg = isset($error['message']) ? $error['message'] : 'Error desconocido';
            header('Location: index.php?c=dashboard&a=files&error=Error al eliminar el archivo: ' . htmlspecialchars($errorMsg));
        }
        exit;
    }

    public function deleteFiles()
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['files'])) {
            header('Location: index.php?c=dashboard&a=files&error=Solicitud inválida');
            exit;
        }

        $files = $_POST['files'];
        if (!is_array($files)) {
            $files = [$files];
        }

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $deletedCount = 0;
        $errors = [];

        foreach ($files as $fileData) {
            $parts = explode('|', $fileData, 3);
            if (count($parts) !== 3) {
                $errors[] = "Formato inválido para: $fileData";
                continue;
            }

            list($company, $folder, $file) = $parts;
            $filePath = $baseDir . '/' . $company . '/' . $folder . '/' . $file;

            // Validar que el archivo existe
            if (!file_exists($filePath) || !is_file($filePath)) {
                $errors[] = "Archivo no encontrado: $file";
                continue;
            }

            // Verificar que la ruta está dentro de empresas_clasificadas
            $realPath = realpath($filePath);
            $realBaseDir = realpath($baseDir);
            if (strpos($realPath, $realBaseDir) !== 0) {
                $errors[] = "Acceso denegado para: $file";
                continue;
            }

            // Intentar eliminar
            if (unlink($filePath)) {
                $deletedCount++;
            } else {
                $error = error_get_last();
                $errorMsg = isset($error['message']) ? $error['message'] : 'Error desconocido';
                $errors[] = "Error eliminando $file: $errorMsg";
            }
        }

        if ($deletedCount > 0) {
            $message = "Se eliminaron $deletedCount archivo(s) correctamente.";
            if (!empty($errors)) {
                $message .= " Errores: " . implode(', ', $errors);
            }
            header('Location: index.php?c=dashboard&a=files&success=' . urlencode($message));
        } else {
            header('Location: index.php?c=dashboard&a=files&error=' . urlencode('No se pudo eliminar ningún archivo. ' . implode(', ', $errors)));
        }
        exit;
    }

    public function deleteAllClassifiedFiles()
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?c=dashboard&a=files&error=Solicitud inválida');
            exit;
        }

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        if (!is_dir($baseDir)) {
            header('Location: index.php?c=dashboard&a=files&error=' . urlencode('El directorio de archivos clasificados no existe.'));
            exit;
        }

        $deletedCount = 0;
        $errors = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $realPath = $item->getRealPath();
                if ($realPath === false) {
                    $errors[] = 'Archivo inaccesible';
                    continue;
                }

                // Verificar que esté dentro de la carpeta base para evitar rutas maliciosas
                $realBaseDir = realpath($baseDir);
                if (strpos($realPath, $realBaseDir) !== 0) {
                    $errors[] = 'Acceso denegado a ' . basename($realPath);
                    continue;
                }

                if (!is_writable($realPath)) {
                    $errors[] = 'No se puede escribir en ' . basename($realPath);
                    continue;
                }

                if (unlink($realPath)) {
                    $deletedCount++;
                } else {
                    $errors[] = 'Error eliminando ' . basename($realPath);
                }
            }
        }

        $message = "Se eliminaron $deletedCount archivo(s) correctamente.";
        if (!empty($errors)) {
            $message .= ' Errores: ' . implode(', ', $errors);
        }

        header('Location: index.php?c=dashboard&a=files&success=' . urlencode($message));
        exit;
    }

    private function processExcelFile($filePath, $columns, $fileExt, $orderNumber = '', $orderCapacity = 0)
    {
        $results = [];
        $companiesData = []; // Almacenar datos de pacientes por empresa

        try {
            if ($fileExt === 'csv') {
                $companiesData = $this->processCsvFileExtended($filePath, $columns);
            } else {
                // Procesar Excel con PhpSpreadsheet
                try {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    throw new Exception('No se pudo leer el archivo. Asegúrate de que sea un archivo Excel válido (.xlsx, .xls)');
                }
                
                $worksheet = $spreadsheet->getActiveSheet();

                // Obtener índices de columnas
                $colIndices = $this->getColumnIndices($worksheet, $columns);

                // Obtener índices de columnas
                
                $highestRow = $worksheet->getHighestRow();
                
                // Intentar auto-detectar columnas si la columna de empresa no tiene datos
                $foundCompanyData = false;
                $testColIndices = $colIndices;
                
                // Primero intentar con las columnas especificadas
                for ($rowNum = 2; $rowNum <= min($highestRow, 10); $rowNum++) {
                    $company = $this->getCellValue($worksheet, $testColIndices['company'], $rowNum);
                    if (!empty($company)) {
                        $foundCompanyData = true;
                        break;
                    }
                }
                
                // Si no encontró datos, intentar auto-detectar
                if (!$foundCompanyData) {
                    $testColIndices = $this->autoDetectColumns($worksheet);
                    if ($testColIndices['company'] !== null) {
                        $colIndices = $testColIndices;
                        for ($rowNum = 2; $rowNum <= min($highestRow, 10); $rowNum++) {
                            $company = $this->getCellValue($worksheet, $colIndices['company'], $rowNum);
                            if (!empty($company)) {
                                $foundCompanyData = true;
                                break;
                            }
                        }
                    }
                }
                
                for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
                    if ($this->rowHasCellFill($worksheet, $rowNum)) {
                        continue;
                    }

                    $company = $this->getCellValue($worksheet, $colIndices['company'], $rowNum);
                    
                    if (empty($company)) continue;
                    
                    if (!isset($results[$company])) {
                        $results[$company] = 0;
                        $companiesData[$company] = [];
                    }
                    
                    $results[$company]++;
                    
                    // Extraer datos del paciente
                    $patientData = [
                        'name' => $this->sanitizeCandidateName($this->getCellValue($worksheet, $colIndices['name'], $rowNum)),
                        'document' => $this->getCellValue($worksheet, $colIndices['document'], $rowNum),
                        'phone' => $this->getCellValue($worksheet, $colIndices['phone'], $rowNum),
                        'gender' => $this->getCellValue($worksheet, $colIndices['gender'], $rowNum),
                        'birth' => $this->getCellValue($worksheet, $colIndices['birth'], $rowNum),
                        'exam_date' => $this->getCellValue($worksheet, $colIndices['exam_date'], $rowNum),
                        'result' => $this->getCellValue($worksheet, $colIndices['result'], $rowNum),
                        'exam' => 'Psicofisico',
                        'order_number' => '',
                    ];
                    
                    $companiesData[$company][] = $patientData;
                }
            }

            // Aplicar orden y capacidad de orden si se configuró
// Crear carpetas y archivos Excel
            if (!empty($companiesData)) {
                $this->createCompanyFolders($companiesData);
            }

            asort($results);
            return $results;

        } catch (Exception $e) {
            throw $e;
        }
    }

    private function autoDetectColumns($worksheet)
    {
        $detected = [
            'company' => null,
            'name' => null,
            'document' => null,
            'phone' => null,
            'gender' => null,
            'birth' => null,
            'exam_date' => null,
            'result' => null,
        ];
        
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        // Palabras clave para detectar columnas
        $keywords = [
            'company' => ['empresa', 'company', 'client', 'cliente', 'organización', 'organizacion'],
            'name' => ['nombre', 'name', 'candidato', 'candidate', 'paciente', 'apellido', 'person'],
            'document' => ['documento', 'cedula', 'id', 'identificación', 'identificacion', 'cc', 'dni'],
            'phone' => ['telefono', 'teléfono', 'phone', 'celular', 'mobile', 'tel', 'movil', 'móvil'],
            'gender' => ['genero', 'género', 'sexo', 'gender', 'sex'],
            'birth' => ['nacimiento', 'fecha_nacimiento', 'birth', 'fecha nac', 'dob'],
            'exam_date' => ['fecha_examen', 'exam_date', 'fecha examen', 'fecha creacion', 'creation date', 'exam', 'fecha'],
            'result' => ['resultado', 'result', 'estado', 'status', 'apto', 'outcome'],
        ];
        
        // Escanear la primera fila para encabezados
        for ($colIndex = 1; $colIndex <= min($highestColumnIndex, 30); $colIndex++) {
            $columnLetter = $this->indexToColumnLetter($colIndex);
            $header = strtolower(trim((string) $worksheet->getCell($columnLetter . '1')->getValue()));
            
            if (empty($header)) continue;
            
            // Buscar coincidencias
            foreach ($keywords as $type => $words) {
                if ($detected[$type] === null) {
                    foreach ($words as $keyword) {
                        if (strpos($header, $keyword) !== false) {
                            $detected[$type] = $colIndex;
                            break 2; // Romper ambos loops
                        }
                    }
                }
            }
        }
        
        // Si no se detectó empresa, usar la primera columna no vacía
        if ($detected['company'] === null) {
            for ($colIndex = 1; $colIndex <= min($highestColumnIndex, 10); $colIndex++) {
                $columnLetter = $this->indexToColumnLetter($colIndex);
                $hasData = false;
                for ($rowNum = 2; $rowNum <= min($worksheet->getHighestRow(), 5); $rowNum++) {
                    if (!empty(trim((string) $worksheet->getCell($columnLetter . $rowNum)->getValue()))) {
                        $hasData = true;
                        break;
                    }
                }
                if ($hasData) {
                    $detected['company'] = $colIndex;
                    break;
                }
            }
        }
        
        return $detected;
    }

    private function getColumnIndices($worksheet, $columns)
    {
        $indices = [];
        
        foreach ($columns as $key => $colRef) {
            if (empty($colRef)) {
                $indices[$key] = null;
                continue;
            }
            
            $colIndex = $this->columnLetterToIndex($colRef);
            
            if ($colIndex === false) {
                // Podría ser un nombre de columna
                $colIndex = $this->findColumnByName($worksheet, $colRef);
            }
            
            $indices[$key] = $colIndex;
        }
        
        return $indices;
    }

    private function getCellValue($worksheet, $colIndex, $rowNum)
    {
        if ($colIndex === false || $colIndex === null) {
            return '';
        }
        
        $columnLetter = $this->indexToColumnLetter($colIndex);
        $cellAddress = $columnLetter . $rowNum;
        $cell = $worksheet->getCell($cellAddress);
        $value = $cell->getValue();

        return trim($this->normalizeWorksheetCellValue($cell, $value));
    }

    private function normalizeWorksheetCellValue($cell, $value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value) && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date instanceof \DateTime ? $date->format('Y-m-d') : trim((string) $value);
            } catch (\Exception $e) {
                // ignore and return raw value below
            }
        }

        if (is_string($value)) {
            // Reemplazar saltos y colapsar espacios
            $value = preg_replace('/[\r\n\t]+/', ' ', $value);
            $value = preg_replace('/\s{2,}/u', ' ', $value);
            // Remover separador visual ' + ' entre campos (preserva '+' en emails sin espacios)
            $value = preg_replace('/\s+\+\s+/u', ' ', $value);
            return trim($value);
        }

        return trim((string) ($value ?? ''));
    }

    private function rowHasCellFill($worksheet, $rowNum)
    {
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
            $cellAddress = $this->indexToColumnLetter($colIndex) . $rowNum;
            $fill = $worksheet->getStyle($cellAddress)->getFill();
            $fillType = $fill->getFillType();

            if (!empty($fillType) && $fillType !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE) {
                return true;
            }
        }

        return false;
    }

    private function findExistingCompanyDirectory(string $companyName, string $baseDir)
    {
        $companyModel = new CompanyModel();
        $normalizedTarget = $companyModel->normalizeCompanyName($companyName);

        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }

            $path = $baseDir . '/' . $folder;
            if (!is_dir($path) || $folder === 'REPORTE GUARDA') {
                continue;
            }

            $normalizedFolder = $companyModel->normalizeCompanyName($folder);
            if ($normalizedFolder === $normalizedTarget) {
                return $folder;
            }
        }

        return null;
    }

    private function createCompanyFolders($companiesData)
    {
        $baseDir = __DIR__ . '/../empresas_clasificadas';
        
        // Crear directorio base si no existe
        if (!is_dir($baseDir)) {
            if (!@mkdir($baseDir, 0777, true)) {
                throw new Exception('No se puede crear el directorio base. Verifica los permisos de la carpeta raíz.');
            }
            // Asegurar permisos de escritura
            if (!is_writable($baseDir)) {
                @chmod($baseDir, 0777);
            }
        }
        
        // Verificar que el directorio base sea escribible
        if (!is_writable($baseDir)) {
            throw new Exception('El directorio base no tiene permisos de escritura. Contacta al administrador.');
        }
        
        $companyModel = new CompanyModel();
        $fechaHoy = date('d_m_Y');
        
        // Crear carpeta para cada empresa
        foreach ($companiesData as $company => $patients) {
            try {
                $existingDir = $this->findExistingCompanyDirectory($company, $baseDir);
                if ($existingDir !== null) {
                    $companyDir = $baseDir . '/' . $existingDir;
                } else {
                    $folderName = $companyModel->sanitizeCompanyFolderName($company);
                    $companyDir = $baseDir . '/' . $folderName;
                    
                    if (!is_dir($companyDir)) {
                        // Intentar crear con permisos específicos
                        if (!@mkdir($companyDir, 0777, true)) {
                            // Si falla, proporcionar más detalles
                            $error = 'Carpeta: ' . $companyDir . ' | Empresa: ' . $company;
                            throw new Exception("No se puede crear la carpeta de empresa: {$error}");
                        }
                        // Asegurar permisos de escritura
                        @chmod($companyDir, 0777);
                    }
                }

                $reportDir = $companyDir . '/REPORTE GUARDA';
                if (!is_dir($reportDir)) {
                    if (!@mkdir($reportDir, 0777, true)) {
                        throw new Exception("No se puede crear la carpeta REPORTE GUARDA para: {$company}");
                    }
                    @chmod($reportDir, 0777);
                }

                // Generar un solo archivo por empresa y conservar los nuevos pacientes en el mismo archivo
                $fileName = $this->sanitizeFileName($company) . '.xlsx';
                $filePath = $reportDir . '/' . $fileName;
                $existingReportFile = $this->findExistingCompanyReportFile($reportDir, $company);
                if ($existingReportFile !== $filePath && file_exists($existingReportFile)) {
                    if (!file_exists($filePath)) {
                        rename($existingReportFile, $filePath);
                    } else {
                        @unlink($existingReportFile);
                    }
                }

                try {
                    $this->generatePatientExcelFile($patients, $filePath, $company);
                    $this->cleanupCompanyReportFiles($reportDir, $company, $filePath);
                } catch (Exception $e) {
                    throw new Exception("Error generar reporte para {$company}: " . $e->getMessage());
                }
            } catch (Exception $e) {
                // Registrar el error pero continuar con las demás empresas
                error_log('Error procesando empresa ' . $company . ': ' . $e->getMessage());
                throw $e; // Re-lanzar para notificar al usuario
            }
        }
    }

    private function generatePatientExcelFile($patients, $filePath, $empresa = '')
    {
        try {
            if (file_exists($filePath)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
                $spreadsheet = $reader->load($filePath);
            } else {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            }

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Reporte');

            // Leer metadata de A3 para obtener el número de orden y cupos asignados
            $metadataCell = (string)$sheet->getCell('A3')->getValue();
            $orderNumber = '';
            $orderCapacity = 0;
            $patientsAssignedCount = 0;
            
            if (!empty($metadataCell) && strpos($metadataCell, 'ORDEN:') !== false) {
                if (preg_match('/ORDEN:\s*(\d+)/', $metadataCell, $matches)) {
                    $orderNumber = $matches[1];
                }
                if (preg_match('/CUPOS:\s*(\d+)/', $metadataCell, $matches)) {
                    $orderCapacity = (int)$matches[1];
                }
            }

            $headers = ['Orden', 'Nombre', 'Documento', 'Teléfono', 'Género', 'Fecha Nacimiento', 'Fecha Examen', 'Resultado', 'Tipo Examen'];
            $existingHighestRow = max(1, $sheet->getHighestRow());

            if (!$this->isCompanyReportSheet($sheet, $empresa)) {
                if (!empty($empresa)) {
                    $sheet->setCellValue('A1', 'EMPRESA: ' . $empresa);
                    $sheet->setCellValue('A2', 'FECHA: ' . date('d/m/Y H:i:s'));
                    $sheet->getRowDimension(1)->setRowHeight(25);
                    $sheet->getRowDimension(2)->setRowHeight(25);
                    $startRow = 4;
                } else {
                    $startRow = 1;
                }

                $sheet->fromArray([$headers], null, 'A' . $startRow);
                $this->applyReportHeaderStyle($sheet, $startRow);
                $rowNum = $startRow + 1;
            } else {
                $sheet->setCellValue('A2', 'FECHA: ' . date('d/m/Y H:i:s'));
                if (trim((string)$sheet->getCell('A4')->getValue()) !== 'Nombre') {
                    $sheet->fromArray([$headers], null, 'A4');
                }
                $this->applyReportHeaderStyle($sheet, 4);
                $existingHighestRow = max(4, $sheet->getHighestRow());
                $rowNum = $existingHighestRow + 1;
                if ($existingHighestRow > 4 && $this->rowHasAnyValue($sheet, $existingHighestRow)) {
                    $rowNum++;
                }
            }

            if (!empty($patients)) {
                foreach ($patients as $patient) {
                    $assignedOrderNumber = $patient['order_number'] ?? '';
                    $targetRow = $rowNum;

                    if (empty($assignedOrderNumber)) {
                        // 1) Priorizar ocupar espacios reservados existentes (celdas A con orden y columna B vacía)
                        $reservedRow = $this->findFirstReservedSlot($sheet, 5);
                        if ($reservedRow !== null) {
                            $targetRow = $reservedRow;
                            $assignedOrderNumber = trim((string)$sheet->getCell('A' . $reservedRow)->getValue());
                        } else {
                            // 2) Si no hay espacios reservados, intentar asignar a la orden de metadata si aún tiene cupos
                            if (!empty($orderNumber) && $this->countAssignedForOrder($sheet, $orderNumber) < $orderCapacity) {
                                $assignedOrderNumber = $orderNumber;
                                $slotRow = $this->findEmptyOrderSlotRow($sheet, $assignedOrderNumber, 5);
                                if ($slotRow !== null) {
                                    $targetRow = $slotRow;
                                }
                            }
                        }
                    } else {
                        // Si el paciente ya trae número de orden, intentar ubicar un slot reservado para esa orden
                        $slotRow = $this->findEmptyOrderSlotRow($sheet, $assignedOrderNumber, 5);
                        if ($slotRow !== null) {
                            $targetRow = $slotRow;
                        }
                    }

                    $sheet->setCellValue('A' . $targetRow, $assignedOrderNumber);
                    $sheet->setCellValue('B' . $targetRow, $patient['name']);
                    $sheet->setCellValue('C' . $targetRow, $patient['document']);
                    $sheet->setCellValue('D' . $targetRow, $patient['phone']);
                    $sheet->setCellValue('E' . $targetRow, $patient['gender']);
                    $sheet->setCellValue('F' . $targetRow, $patient['birth']);
                    $sheet->setCellValue('G' . $targetRow, $patient['exam_date']);
                    $sheet->setCellValue('H' . $targetRow, $patient['result']);
                    $sheet->setCellValue('I' . $targetRow, $patient['exam']);

                    if ($targetRow === $rowNum) {
                        $rowNum++;
                    }
                }
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Validar que el directorio existe y es escribible
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                throw new Exception("El directorio no existe: $dir");
            }
            if (!is_writable($dir)) {
                throw new Exception("El directorio no tiene permisos de escritura: $dir");
            }

            $writer->save($filePath);

            if (!file_exists($filePath)) {
                throw new Exception("El archivo no se guardó correctamente: $filePath");
            }
        } catch (Exception $e) {
            throw new Exception("Error al generar el archivo Excel: " . $e->getMessage());
        }
    }

    private function isCompanyReportSheet($sheet, $company)
    {
        $companyCell = trim((string)$sheet->getCell('A1')->getValue());
        return $companyCell !== '' && strpos($companyCell, 'EMPRESA:') === 0;
    }

    private function applyOrderNumbersToCompanyData(array $companiesData, string $orderNumber, int $orderCapacity)
    {
        foreach ($companiesData as $company => &$patients) {
            $assigned = 0;
            $newPatients = [];
            foreach ($patients as $patient) {
                if ($assigned < $orderCapacity) {
                    $patient['order_number'] = $orderNumber;
                    $patient['order_capacity'] = $orderCapacity;
                    $assigned++;
                    $newPatients[] = $patient;
                    continue;
                }

                if ($assigned >= $orderCapacity && count($newPatients) === $orderCapacity) {
                    // Insertar una fila separadora una sola vez después de la primera tanda de orden si hay más pacientes.
                    $newPatients[] = [
                        'order_number' => '',
                        'name' => '',
                        'document' => '',
                        'phone' => '',
                        'gender' => '',
                        'birth' => '',
                        'exam_date' => '',
                        'result' => '',
                        'exam' => '',
                    ];
                    $assigned = -9999; // indicar que la fila separadora ya se agregó
                }

                $patient['order_number'] = '';
                $newPatients[] = $patient;
            }

            $patients = $newPatients;
        }
        unset($patients);

        return $companiesData;
    }

    private function applyReportHeaderStyle($sheet, $startRow)
    {
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        for ($col = 'A'; $col <= 'I'; $col++) {
            $sheet->getStyle($col . $startRow)->applyFromArray($headerStyle);
        }
    }

    private function findExistingCompanyReportFile($reportDir, $company)
    {
        $baseName = $this->sanitizeFileName($company);
        $canonicalPath = $reportDir . '/' . $baseName . '.xlsx';

        if (file_exists($canonicalPath)) {
            return $canonicalPath;
        }

        $pattern = $reportDir . '/' . $baseName . '_*.xlsx';
        $matches = glob($pattern);
        if (!empty($matches)) {
            usort($matches, function ($a, $b) {
                return filemtime($b) <=> filemtime($a);
            });
            return $matches[0];
        }

        return $canonicalPath;
    }

    private function sanitizeFileName($fileName)
    {
        // Eliminar caracteres especiales del nombre de archivo
        $fileName = preg_replace('/[^a-zA-Z0-9_\-]/u', '_', $fileName);
        return trim($fileName, '_');
    }

    private function processCsvFileExtended($filePath, $columns)
    {
        $companiesData = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new Exception('No se pudo abrir el archivo CSV');
        }

        // Detectar delimitador (coma, punto y coma, tab, barra vertical)
        $delimiter = $this->detectCsvDelimiter($filePath);

        // Obtener índices de columnas
        $colIndices = [];
        $rowIndex = 0;

        $sampleRows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowIndex++;

            // Normalizar cada campo del row para evitar saltos de línea y espacios extra
            $row = array_map([$this, 'sanitizeCsvField'], $row);

            if ($rowIndex === 1) {
                // Procesar encabezados
                foreach ($columns as $key => $colRef) {
                    if (is_numeric($colRef)) {
                        $colIndices[$key] = intval($colRef) - 1;
                    } else {
                        // Buscar por nombre (insensible a mayúsculas)
                        $colIndices[$key] = array_search(strtolower(trim($colRef)), array_map('strtolower', $row));
                        if ($colIndices[$key] === false) {
                            $colIndices[$key] = null;
                        }
                    }
                }
                // Guardar encabezado de ejemplo para logging
                $sampleRows[] = $row;
                continue;
            }

            $company = $row[$colIndices['company'] ?? 0] ?? '';
            $company = trim($company);

            if (empty($company)) continue;

            if (!isset($companiesData[$company])) {
                $companiesData[$company] = [];
            }

            $patientData = [
                'name' => $this->sanitizeCandidateName($row[$colIndices['name'] ?? 1] ?? ''),
                'document' => $row[$colIndices['document'] ?? 2] ?? '',
                'phone' => $row[$colIndices['phone'] ?? 3] ?? '',
                'gender' => $row[$colIndices['gender'] ?? 4] ?? '',
                'birth' => $row[$colIndices['birth'] ?? 5] ?? '',
                'exam_date' => $row[$colIndices['exam_date'] ?? 6] ?? '',
                'result' => $row[$colIndices['result'] ?? 7] ?? '',
                'exam' => 'Psicofisico',
                'order_number' => '',
            ];

            if (count($sampleRows) < 5) {
                $sampleRows[] = $row;
            }

            $companiesData[$company][] = $patientData;
        }

        fclose($handle);
        return $companiesData;
    }

    private function detectCsvDelimiter(string $filePath, $sampleLines = 5)
    {
        $delimiters = [',', ';', "\t", '|'];
        $counts = array_fill_keys($delimiters, 0);

        $handle = fopen($filePath, 'r');
        if ($handle === false) return ',';

        $lineNum = 0;
        while (($line = fgets($handle)) !== false && $lineNum < $sampleLines) {
            $lineNum++;
            foreach ($delimiters as $d) {
                $counts[$d] += substr_count($line, $d);
            }
        }

        fclose($handle);

        // Elegir el delimitador con mayor ocurrencia
        arsort($counts);
        $best = key($counts);

return $best ?: ',';
    }

    private function sanitizeCsvField($value)
    {
        // Normalizar tipo
        $value = $value ?? '';
        if (!is_string($value)) {
            $value = (string) $value;
        }

        // Convertir saltos de línea y tabs a espacios, colapsar múltiples espacios
        $value = preg_replace('/[\r\n\t]+/', ' ', $value);
        $value = preg_replace('/\s{2,}/u', ' ', $value);

        // Remover separador visual ' + ' que a veces aparece entre campos (mantener '+' dentro de emails/nombres cuando no tiene espacios)
        $value = preg_replace('/\s+\+\s+/u', ' ', $value);

        return trim($value);
    }

    private function sanitizeCandidateName($value)
    {
        $value = $this->sanitizeCsvField($value);
        if ($value === '') return '';

        // Palabras indicativas de dirección/ubicación que truncarán el nombre
        $stopWords = [
            'BARRIO','CONJUNTO','URB','URBANO','MANZANA','APTO','TORRE','EDIF','CASA',
            'CALLE','CLL','CRA','CR','TRANS','SECTOR','BLOQUE','MESA','EMAIL','CORREO',
            'LOCALIDAD'
        ];

        // Dividir en tokens y acumular mientras parezcan nombres (solo letras, guiones y apóstrofes)
        $tokens = preg_split('/\s+/u', $value);
        $nameParts = [];

        foreach ($tokens as $token) {
            $t = trim($token, ",.:;()[]\"'");
            if ($t === '') break;

            // Si token contiene email, números o símbolos, cortar
            if (strpos($t, '@') !== false) break;
            if (preg_match('/\d/', $t)) break;

            // Si el token es una de las stopWords -> cortar
            if (in_array(mb_strtoupper($t, 'UTF-8'), $stopWords, true)) break;

            // Si el token contiene caracteres no alfabéticos (excepto guion/apóstrofe/punto), cortar
            if (!preg_match('/^[\p{L}\.\-\'’]+$/u', $t)) {
                break;
            }

            $nameParts[] = $t;
        }

        if (!empty($nameParts)) {
            return trim(implode(' ', $nameParts));
        }

        // Fallback: si no se pudieron extraer partes "limpias", intentar truncar en la primera stopWord o email
        $minPos = null;
        foreach ($stopWords as $w) {
            $pos = stripos($value, $w);
            if ($pos !== false && ($minPos === null || $pos < $minPos)) {
                $minPos = $pos;
            }
        }
        $atPos = stripos($value, '@');
        if ($atPos !== false && ($minPos === null || $atPos < $minPos)) {
            $minPos = $atPos;
        }

        if ($minPos !== null) {
            $candidate = trim(substr($value, 0, $minPos));
            // Quitar puntuación final
            $candidate = rtrim($candidate, ",.:;\-_/\\");
            return $candidate;
        }

        return $value;
    }



    private function columnLetterToIndex($column)
    {
        // Convertir letras de columna (A, B, C, AA, etc) a índice numérico
        $column = strtoupper(trim($column));

        // Si es un número, devolverlo
        if (is_numeric($column)) {
            return intval($column);
        }

        // Convertir letra a número
        $index = 0;
        $len = strlen($column);

        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($column[$i]) - ord('A') + 1);
        }

        return $index ?: false;
    }

    private function findColumnByName($worksheet, $columnName)
    {
        // Buscar una columna por su nombre en la primera fila
        $columnName = strtolower(trim($columnName));
        
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = $this->columnLetterToIndex($highestColumn);
        
        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
            $columnLetter = $this->indexToColumnLetter($colIndex);
            $cellValue = $worksheet->getCell($columnLetter . '1')->getValue();
            
            if (strtolower(trim($cellValue ?? '')) === $columnName) {
                return $colIndex;
            }
        }

        return false;
    }

    private function indexToColumnLetter($index)
    {
        // Convertir índice numérico a letra de columna (1->A, 2->B, 27->AA, etc)
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intval($index / 26);
        }
        return $letter;
    }

    private function ensureAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
    }
}
