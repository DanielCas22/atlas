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
                        $companyColumn = trim($_POST['company_column'] ?? 'A');

                        if (empty($companyColumn)) {
                            $error = 'Especifica la columna de empresa.';
                        } else {
                            // Procesar el archivo
                            $results = $this->processExcelFile($filePath, $companyColumn, $fileExt);
                            
                            if ($results === false) {
                                $error = 'Error al procesar el archivo. Verifica que el formato sea correcto.';
                            } else {
                                $success = 'Archivo procesado correctamente. Se encontraron ' . count($results) . ' empresas.';
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

    private function processExcelFile($filePath, $companyColumn, $fileExt)
    {
        $results = [];

        try {
            if ($fileExt === 'csv') {
                // Procesar CSV
                $results = $this->processCsvFile($filePath, $companyColumn);
            } else {
                // Procesar Excel con PhpSpreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();

                // Convertir columna (A, B, C, etc) a número (1, 2, 3, etc)
                $columnIndex = $this->columnLetterToIndex($companyColumn);

                if ($columnIndex === false) {
                    // Podría ser un nombre de columna
                    $columnIndex = $this->findColumnByName($worksheet, $companyColumn);
                    if ($columnIndex === false) {
                        return false;
                    }
                }

                // Iterar sobre las filas
                foreach ($worksheet->getRowIterator(2) as $row) { // Comenzar desde fila 2 (omitir header)
                    $cell = $worksheet->getCellByColumnAndRow($columnIndex, $row->getRowIndex());
                    $company = trim($cell->getValue() ?? '');

                    if (!empty($company) && $company !== 'Empresa') {
                        if (!isset($results[$company])) {
                            $results[$company] = 0;
                        }
                        $results[$company]++;
                    }
                }
            }

            // Ordenar alfabéticamente
            asort($results);
            return $results;

        } catch (Exception $e) {
            return false;
        }
    }

    private function processCsvFile($filePath, $companyColumn)
    {
        $results = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return false;
        }

        $columnIndex = $this->columnLetterToIndex($companyColumn);
        $rowIndex = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;

            if ($rowIndex === 1) {
                // Saltar encabezado
                continue;
            }

            if ($columnIndex === false || !isset($row[$columnIndex - 1])) {
                continue;
            }

            $company = trim($row[$columnIndex - 1] ?? '');

            if (!empty($company)) {
                if (!isset($results[$company])) {
                    $results[$company] = 0;
                }
                $results[$company]++;
            }
        }

        fclose($handle);
        return $results;
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

        foreach ($worksheet->getRowIterator(1, 1) as $row) {
            $cellIterator = $row->getCellIterator();
            $colIndex = 1;

            foreach ($cellIterator as $cell) {
                if (strtolower(trim($cell->getValue() ?? '')) === $columnName) {
                    return $colIndex;
                }
                $colIndex++;
            }
        }

        return false;
    }

    private function ensureAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
    }
}
