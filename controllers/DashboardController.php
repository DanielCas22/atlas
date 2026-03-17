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

    private function ensureAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
    }
}
