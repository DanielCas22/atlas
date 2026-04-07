<?php

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'fullname' => $user['fullname'],
                    'role' => $user['role_name'],
                ];
                header('Location: index.php?c=dashboard&a=index');
                exit;
            }

            $error = 'Usuario o contraseña inválido';
        }

        include __DIR__ . '/../views/auth/login.php';
    }

    public function logout()
    {
        // Limpiar datos de sesión y cookies
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        session_destroy();

        // Redireccionar y deshabilitar cache en el navegador
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Location: index.php');
        exit;
    }
}
