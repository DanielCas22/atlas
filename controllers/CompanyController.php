<?php

class CompanyController
{
    private $companyModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
    }

    public function list()
    {
        $this->ensureAuth();

        $search = trim($_GET['search'] ?? '');
        if (!empty($search)) {
            $companies = $this->companyModel->searchByName($search);
        } else {
            $companies = $this->companyModel->allWithExamCounts();
        }

        include __DIR__ . '/../views/companies/list.php';
    }

    public function add()
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $contact = trim($_POST['contact'] ?? '');

            if (!empty($name)) {
                $this->companyModel->add($name, $contact);
                header('Location: index.php?c=company&a=list');
                exit;
            }

            $error = 'El nombre de la empresa es requerido';
        }

        include __DIR__ . '/../views/companies/add.php';
    }

    public function edit()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        $company = $this->companyModel->find($id);

        if (!$company) {
            header('Location: index.php?c=company&a=list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $contact = trim($_POST['contact'] ?? '');

            if (!empty($name)) {
                $this->companyModel->update($id, $name, $contact);
                header('Location: index.php?c=company&a=list');
                exit;
            }

            $error = 'El nombre de la empresa es requerido';
        }

        include __DIR__ . '/../views/companies/edit.php';
    }

    public function delete()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);

        if ($id) {
            try {
                $deleted = $this->companyModel->delete($id);
                if ($deleted) {
                    header('Location: index.php?c=company&a=list&message=' . urlencode('Empresa eliminada correctamente.'));
                } else {
                    header('Location: index.php?c=company&a=list&error=' . urlencode('No se pudo eliminar la empresa.'));
                }
            } catch (Exception $e) {
                header('Location: index.php?c=company&a=list&error=' . urlencode('Error al eliminar la empresa: ' . $e->getMessage()));
            }
        } else {
            header('Location: index.php?c=company&a=list&error=' . urlencode('Empresa inválida.'));
        }

        exit;
    }

    public function view()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        $company = $this->companyModel->find($id);

        if (!$company) {
            header('Location: index.php?c=company&a=list');
            exit;
        }

        $orderNumber = trim($_GET['order'] ?? '');
        $message = trim($_GET['message'] ?? '');

        $examModel = new ExamModel();
        $orderGroups = $examModel->getOrderGroupsByCompany($id);
        $exams = [];

        if (!empty($orderNumber)) {
            $exams = $examModel->findByCompanyId($id, $orderNumber);
        }

        include __DIR__ . '/../views/companies/view.php';
    }

    public function clearOrder()
    {
        $this->ensureAuth();

        $id = intval($_GET['id'] ?? 0);
        $orderNumber = trim($_GET['order'] ?? '');

        if (!$id || $orderNumber === '') {
            header('Location: index.php?c=company&a=list');
            exit;
        }

        $examModel = new ExamModel();
        $deleted = $examModel->deleteByCompanyAndOrder($id, $orderNumber);

        $message = $deleted ? 'Carpeta depurada correctamente.' : 'No se pudo depurar la carpeta.';

        header('Location: index.php?c=company&a=view&id=' . $id . '&message=' . urlencode($message));
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
