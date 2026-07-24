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

        // Sincronizar empresas que ya existen en la carpeta de archivos clasificados
        $this->companyModel->syncClassifiedCompanies();

        $search = trim($_GET['search'] ?? '');
        $order = trim($_GET['order'] ?? '');

        // Priorizar búsqueda por número de orden si se proporciona
        if ($order !== '') {
            $companies = $this->companyModel->searchByOrder($order);
        } elseif (!empty($search)) {
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
                $this->companyModel->addIfNotExists($name, $contact);
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

    /**
     * Eliminar múltiples empresas seleccionadas
     */
    public function deleteMultiple()
    {
        $this->ensureAuth();

        $ids = $_POST['company_ids'] ?? $_GET['ids'] ?? [];
        
        // Convertir a array si viene como string
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        // Filtrar y convertir a enteros
        $ids = array_filter($ids, function($id) {
            return is_numeric($id) && intval($id) > 0;
        });
        $ids = array_map('intval', $ids);

        if (empty($ids)) {
            header('Location: index.php?c=company&a=list&error=' . urlencode('No se seleccionaron empresas para eliminar.'));
            exit;
        }

        try {
            $result = $this->companyModel->deleteMultiple($ids);
            if ($result['success']) {
                $count = $result['deleted_count'];
                $message = $count === 1 
                    ? 'Empresa eliminada correctamente.' 
                    : "$count empresas eliminadas correctamente.";
                header('Location: index.php?c=company&a=list&message=' . urlencode($message));
            } else {
                header('Location: index.php?c=company&a=list&error=' . urlencode('Error al eliminar empresas: ' . ($result['error'] ?? 'Error desconocido.')));
            }
        } catch (Exception $e) {
            header('Location: index.php?c=company&a=list&error=' . urlencode('Error al eliminar empresas: ' . $e->getMessage()));
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
