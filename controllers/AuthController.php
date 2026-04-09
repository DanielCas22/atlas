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

        $adminEmail = $this->userModel->findAdminEmail();
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

    public function forgot()
    {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Por favor ingresa un email válido';
            } else {
                $user = $this->userModel->findByEmail($email);
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->savePasswordReset($user['id'], $token, 30);

                    // Intentar enviar email
                    require_once __DIR__ . '/../config/SMTPConfig.php';
                    require_once __DIR__ . '/../helpers/EmailHelper.php';

                    $emailHelper = new EmailHelper();
                    $emailSent = false;

                    if (SMTPConfig::isConfigured()) {
                        $emailSent = $emailHelper->sendPasswordResetEmail($email, $user['username'], $token);
                    }

                    if ($emailSent) {
                        $success = 'Se ha enviado un enlace de recuperación a tu email.';
                        $sentByEmail = true;
                    } else {
                        if ($user) {
                            error_log('Fallo al enviar email de recuperación a: ' . $email);
                            $error = 'No se pudo enviar el correo de recuperación. Revisa la configuración SMTP y prueba nuevamente.';
                        } else {
                            // Por seguridad, no revelar si el email existe
                            $success = 'Si ese email está registrado, recibirás un enlace de recuperación.';
                            $sentByEmail = true;
                        }
                    }
                } else {
                    // Por seguridad, no revelar si el email existe
                    $success = 'Si ese email está registrado, recibirás un enlace de recuperación.';
                    $sentByEmail = true;
                }
            }
        }

        include __DIR__ . '/../views/auth/forgot.php';
    }

    public function reset()
    {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?c=dashboard&a=index');
            exit;
        }

        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $error = 'Token inválido o no proporcionado';
            include __DIR__ . '/../views/auth/login.php';
            exit;
        }

        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            $error = 'El enlace ha expirado o es inválido';
            include __DIR__ . '/../views/auth/login.php';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } elseif ($password !== $confirmPassword) {
                $error = 'Las contraseñas no coinciden';
            } else {
                $this->userModel->update($user['id'], ['password' => $password]);
                $this->userModel->clearResetToken($user['id']);
                $success = 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión.';
                include __DIR__ . '/../views/auth/login.php';
                exit;
            }
        }

        include __DIR__ . '/../views/auth/reset.php';
    }
}
