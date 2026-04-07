<?php

class DashboardController
{
    public function index()
    {
        $this->ensureAuth();

        $user = $_SESSION['user'];
        $examModel = new ExamModel();
        $exams = $examModel->all();

        include __DIR__ . '/../views/dashboard/index.php';
    }

    public function users()
    {
        $this->ensureAuth();

        $userModel = new UserModel();
        $users = $userModel->all();

        include __DIR__ . '/../views/dashboard/users.php';
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
                // Asegurar que el rol existe en roles (si no, insertamos coordinador como ejemplo).
                $pdo = Database::getInstance()->getConnection();
                $stmtRole = $pdo->prepare('SELECT id FROM roles WHERE id = ?');
                $stmtRole->execute([$data['role_id']]);

                if (!$stmtRole->fetch()) {
                    $stmtInsertRole = $pdo->prepare('INSERT INTO roles (id, name, description) VALUES (?, ?, ?)');
                    $stmtInsertRole->execute([$data['role_id'], $roleOptions[$data['role_id']], 'Rol creado automáticamente']);
                }

                // Guarda en la tabla users con el rol elegido
                $stmt = $pdo->prepare('INSERT INTO users (role_id, username, password, fullname, email) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([
                    $data['role_id'],
                    $data['username'],
                    password_hash($data['password'], PASSWORD_BCRYPT),
                    $data['nombres'] . ' ' . $data['apellidos'],
                    $data['email'],
                ]);

                // Enviar correo de confirmación:
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

                header('Location: index.php?c=dashboard&a=users');
                exit;
            }

            $error = 'Complete todos los campos correctamente.';
        }

        include __DIR__ . '/../views/dashboard/create_user.php';
    }

    public function clasificacion_empresas()
    {
        $this->ensureAuth();

        $results = null;
        $error = null;
        $success = null;

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

    public function files()
    {
        $this->ensureAuth();

        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $companies = [];

        if (is_dir($baseDir)) {
            $companyDirs = scandir($baseDir);
            foreach ($companyDirs as $companyDir) {
                if ($companyDir === '.' || $companyDir === '..') continue;

                $companyPath = $baseDir . '/' . $companyDir;
                if (is_dir($companyPath)) {
                    $companies[$companyDir] = [
                        'folders' => []
                    ];

                    $subDirs = scandir($companyPath);
                    foreach ($subDirs as $subDir) {
                        if ($subDir === '.' || $subDir === '..') continue;

                        $subPath = $companyPath . '/' . $subDir;
                        if (is_dir($subPath)) {
                            $companies[$companyDir]['folders'][$subDir] = [
                                'files' => []
                            ];

                            $files = scandir($subPath);
                            foreach ($files as $file) {
                                if ($file === '.' || $file === '..') continue;

                                $filePath = $subPath . '/' . $file;
                                if (is_file($filePath)) {
                                    $companies[$companyDir]['folders'][$subDir]['files'][] = [
                                        'name' => $file,
                                        'size' => filesize($filePath),
                                        'modified' => date('d/m/Y H:i', filemtime($filePath))
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        include __DIR__ . '/../views/dashboard/files.php';
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
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($filePath));
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

    private function processExcelFile($filePath, $columns, $fileExt)
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
                
                $highestRow = $worksheet->getHighestRow();
                for ($rowNum = 2; $rowNum <= $highestRow; $rowNum++) {
                    if ($this->rowHasCellFill($worksheet, $rowNum, $colIndices)) {
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
                        'name' => $this->getCellValue($worksheet, $colIndices['name'], $rowNum),
                        'document' => $this->getCellValue($worksheet, $colIndices['document'], $rowNum),
                        'phone' => $this->getCellValue($worksheet, $colIndices['phone'], $rowNum),
                        'gender' => $this->getCellValue($worksheet, $colIndices['gender'], $rowNum),
                        'birth' => $this->getCellValue($worksheet, $colIndices['birth'], $rowNum),
                        'exam_date' => $this->getCellValue($worksheet, $colIndices['exam_date'], $rowNum),
                        'result' => $this->getCellValue($worksheet, $colIndices['result'], $rowNum),
                        'exam' => 'Psicofisico',
                    ];
                    
                    $companiesData[$company][] = $patientData;
                }
            }

            // Crear carpetas y archivos Excel
            if (!empty($results)) {
                $this->createCompanyFolders($companiesData);
            }

            asort($results);
            return $results;

        } catch (Exception $e) {
            throw $e;
        }
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
            return trim($value);
        }

        return trim((string) ($value ?? ''));
    }

    private function rowHasCellFill($worksheet, $rowNum, $colIndices)
    {
        foreach ($colIndices as $colIndex) {
            if ($colIndex === false || $colIndex === null) {
                continue;
            }

            $cellAddress = $this->indexToColumnLetter($colIndex) . $rowNum;
            $fill = $worksheet->getStyle($cellAddress)->getFill();
            $fillType = $fill->getFillType();

            if (!empty($fillType) && $fillType !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE) {
                return true;
            }
        }

        return false;
    }

    private function createCompanyFolders($companiesData)
    {
        $baseDir = __DIR__ . '/../empresas_clasificadas';
        
        // Crear directorio base si no existe
        if (!is_dir($baseDir)) {
            if (!@mkdir($baseDir, 0777, true)) {
                throw new Exception('No se puede crear el directorio base. Verifica los permisos.');
            }
        }
        
        // Fecha actual para el archivo
        $fechaHoy = date('d_m_Y');
        
        // Crear carpeta para cada empresa
        foreach ($companiesData as $company => $patients) {
            $safeName = $this->sanitizeFileName($company);
            $companyDir = $baseDir . '/' . $safeName;
            $reportDir = $companyDir . '/REPORTE GUARDA';
            
            // Crear directorios
            if (!is_dir($companyDir)) {
                if (!@mkdir($companyDir, 0777, true)) {
                    throw new Exception("No se puede crear la carpeta de empresa: $company");
                }
            }
            
            if (!is_dir($reportDir)) {
                if (!@mkdir($reportDir, 0777, true)) {
                    throw new Exception("No se puede crear la carpeta REPORTE GUARDA para: $company");
                }
            }
            
            // Generar nombre del archivo con fecha
            $fileName = 'REPORTE_' . $fechaHoy . '.xlsx';
            $filePath = $reportDir . '/' . $fileName;
            
            // Generar archivo Excel con los pacientes
            try {
                $this->generatePatientExcelFile($patients, $filePath, $company);
            } catch (Exception $e) {
                throw new Exception("Error generar reporte para $company: " . $e->getMessage());
            }
        }
    }

    private function generatePatientExcelFile($patients, $filePath, $empresa = '')
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Reporte');
            
            // Información del reporte en las primeras filas
            if (!empty($empresa)) {
                $sheet->setCellValue('A1', 'EMPRESA: ' . $empresa);
                $sheet->setCellValue('A2', 'FECHA: ' . date('d/m/Y H:i:s'));
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $startRow = 4;
            } else {
                $startRow = 1;
            }
            
            // Encabezados
            $headers = ['Nombre', 'Documento', 'Teléfono', 'Género', 'Fecha Nacimiento', 'Fecha Examen', 'Resultado', 'Tipo Examen'];
            $sheet->fromArray([$headers], null, 'A' . $startRow);
            
            // Aplicar estilo a encabezados
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            
            for ($col = 'A'; $col <= 'H'; $col++) {
                $sheet->getStyle($col . $startRow)->applyFromArray($headerStyle);
            }
            
            // Datos de pacientes
            if (!empty($patients)) {
                $rowNum = $startRow + 1;
                foreach ($patients as $patient) {
                    $sheet->setCellValue('A' . $rowNum, $patient['name']);
                    $sheet->setCellValue('B' . $rowNum, $patient['document']);
                    $sheet->setCellValue('C' . $rowNum, $patient['phone']);
                    $sheet->setCellValue('D' . $rowNum, $patient['gender']);
                    $sheet->setCellValue('E' . $rowNum, $patient['birth']);
                    $sheet->setCellValue('F' . $rowNum, $patient['exam_date']);
                    $sheet->setCellValue('G' . $rowNum, $patient['result']);
                    $sheet->setCellValue('H' . $rowNum, $patient['exam']);
                    $rowNum++;
                }
            }
            
            // Ajustar ancho de columnas
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Guardar archivo
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

        // Obtener índices de columnas
        $colIndices = [];
        $rowIndex = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;

            if ($rowIndex === 1) {
                // Procesar encabezados
                foreach ($columns as $key => $colRef) {
                    if (is_numeric($colRef)) {
                        $colIndices[$key] = intval($colRef) - 1;
                    } else {
                        // Buscar por nombre
                        $colIndices[$key] = array_search(strtolower(trim($colRef)), array_map('strtolower', $row));
                        if ($colIndices[$key] === false) {
                            $colIndices[$key] = null;
                        }
                    }
                }
                continue;
            }

            $company = $row[$colIndices['company'] ?? 0] ?? '';
            $company = trim($company);
            
            if (empty($company)) continue;
            
            if (!isset($companiesData[$company])) {
                $companiesData[$company] = [];
            }
            
            $patientData = [
                'name' => $row[$colIndices['name'] ?? 1] ?? '',
                'document' => $row[$colIndices['document'] ?? 2] ?? '',
                'phone' => $row[$colIndices['phone'] ?? 3] ?? '',
                'gender' => $row[$colIndices['gender'] ?? 4] ?? '',
                'birth' => $row[$colIndices['birth'] ?? 5] ?? '',
                'exam_date' => $row[$colIndices['exam_date'] ?? 6] ?? '',
                'result' => $row[$colIndices['result'] ?? 7] ?? '',
                'exam' => 'Psicofisico',
            ];
            
            $companiesData[$company][] = $patientData;
        }

        fclose($handle);
        return $companiesData;
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
