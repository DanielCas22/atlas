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

    private function ensureAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
    }
}
