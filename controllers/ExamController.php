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

        $companies = $this->examModel->getCompanies();
        $examTypes = $this->examModel->getExamTypes();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $company_id = intval($_POST['company_id'] ?? 0);
            $exam_type_id = intval($_POST['exam_type_id'] ?? 0);
            $candidate_name = trim($_POST['candidate_name'] ?? '');

            if ($company_id && $exam_type_id && $candidate_name) {
                $this->examModel->add($company_id, $exam_type_id, $candidate_name);
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

    private function ensureAuth()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
    }
}
