
<?php

class ExamModel extends BaseModel
{
    public function all(string $status = null)
    {
        $sql = 'SELECT e.id, sc.name as company_name, et.name as exam_type, e.candidate_name, e.document_number, e.phone, e.gender, e.birth_date, e.exam_date, e.order_number, e.status, e.created_at, e.updated_at
                FROM exams e
                JOIN security_companies sc ON e.company_id = sc.id
                JOIN exam_types et ON e.exam_type_id = et.id';
        $params = [];

        if ($status !== null) {
            $sql .= ' WHERE e.status = ?';
            $params[] = $status;
        }

        $sql .= ' ORDER BY e.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM exams');
        $row = $stmt->fetch();
        return intval($row['total'] ?? 0);
    }

    public function getStatusSummary()
    {
        $sql = 'SELECT status, COUNT(*) as total FROM exams GROUP BY status';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getResultSummary()
    {
        $sql = 'SELECT
                    SUM(status = "FINALIZADO") as aptos,
                    SUM(status = "RECHAZADO") as no_aptos,
                    SUM(status = "PENDIENTE") as pendientes,
                    SUM(status = "EN_CURSO") as en_curso,
                    SUM(status = "SIN_RESULTADO") as sin_resultado
                FROM exams';
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }

    public function getTopCompaniesByExams($limit = 5)
    {
        $sql = 'SELECT sc.name as company_name, COUNT(*) as exams_count
                FROM exams e
                JOIN security_companies sc ON e.company_id = sc.id
                GROUP BY sc.id, sc.name
                ORDER BY exams_count DESC
                LIMIT ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getTopExamTypes($limit = 5)
    {
        $sql = 'SELECT et.name as exam_type_name, COUNT(*) as exams_count
                FROM exams e
                JOIN exam_types et ON e.exam_type_id = et.id
                GROUP BY et.id, et.name
                ORDER BY exams_count DESC
                LIMIT ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function allWithDetails()
    {
        $sql = 'SELECT e.id, sc.name as company_name, et.name as exam_type_name, e.candidate_name, e.document_number, e.phone, e.gender, e.birth_date, e.exam_date, e.order_number, e.status, e.created_at, e.updated_at
                FROM exams e
                JOIN security_companies sc ON e.company_id = sc.id
                JOIN exam_types et ON e.exam_type_id = et.id
                ORDER BY e.exam_date ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function findByCompanyId($company_id, $orderNumber = null)
    {
        $sql = 'SELECT e.id, et.name as exam_type_name, e.candidate_name, e.document_number, e.phone, e.gender, e.birth_date, e.exam_date, e.order_number, e.status, e.created_at
                FROM exams e
                JOIN exam_types et ON e.exam_type_id = et.id
                WHERE e.company_id = ?';
        $params = [$company_id];

        if (!empty($orderNumber)) {
            $sql .= ' AND e.order_number = ?';
            $params[] = $orderNumber;
        }

        $sql .= ' ORDER BY e.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getOrderGroupsByCompany($company_id)
    {
        $sql = 'SELECT e.order_number, COUNT(*) as total
                FROM exams e
                WHERE e.company_id = ?
                GROUP BY e.order_number
                ORDER BY e.order_number ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$company_id]);
        return $stmt->fetchAll();
    }

    public function findByOrderNumber($orderNumber)
    {
        $sql = 'SELECT e.id, sc.name as company_name, et.name as exam_type_name, e.candidate_name, e.document_number, e.phone, e.gender, e.birth_date, e.exam_date, e.order_number, e.status, e.created_at, e.updated_at
                FROM exams e
                JOIN security_companies sc ON e.company_id = sc.id
                JOIN exam_types et ON e.exam_type_id = et.id
                WHERE e.order_number = ?
                ORDER BY e.exam_date ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderNumber]);
        return $stmt->fetchAll();
    }

    public function add($company_id, $exam_type_id, $candidate_name, $document_number = null, $phone = null, $gender = null, $birth_date = null, $exam_date = null, $order_number = null, $status = 'PENDIENTE')
    {
        $stmt = $this->db->prepare('INSERT INTO exams (company_id, exam_type_id, candidate_name, document_number, phone, gender, birth_date, exam_date, order_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$company_id, $exam_type_id, $candidate_name, $document_number, $phone, $gender, $birth_date, $exam_date, $order_number, $status]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE exams SET status = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function update($id, $candidate_name, $document_number, $phone, $gender, $birth_date, $exam_date, $order_number, $status)
    {
        $stmt = $this->db->prepare('UPDATE exams SET candidate_name = ?, document_number = ?, phone = ?, gender = ?, birth_date = ?, exam_date = ?, order_number = ?, status = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$candidate_name, $document_number, $phone, $gender, $birth_date, $exam_date, $order_number, $status, $id]);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM exams WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM exams WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function deleteByCompanyAndOrder($company_id, $order_number)
    {
        if (empty($company_id) || $order_number === '') {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM exams WHERE company_id = ? AND order_number = ?');
        return $stmt->execute([$company_id, $order_number]);
    }

    public function deleteByCompanyId($company_id)
    {
        if (empty($company_id)) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM exams WHERE company_id = ?');
        return $stmt->execute([$company_id]);
    }

    public function getCompanies()
    {
        $stmt = $this->db->query('SELECT id, name FROM security_companies ORDER BY name');
        return $stmt->fetchAll();
    }

    public function findCompanyByName(string $name)
    {
        // Delegar a CompanyModel para aprovechar normalización y búsqueda robusta
        if (!class_exists('CompanyModel')) {
            require_once __DIR__ . '/CompanyModel.php';
        }
        $cm = new CompanyModel();
        return $cm->findByName($name);
    }

    public function getExamTypes()
    {
        $stmt = $this->db->query('SELECT id, name FROM exam_types ORDER BY name');
        return $stmt->fetchAll();
    }

    public function syncExamTypes(array $types)
    {
        $stmtSelect = $this->db->prepare('SELECT id FROM exam_types WHERE name = ?');
        $stmtInsert = $this->db->prepare('INSERT INTO exam_types (name, description) VALUES (?, ?)');

        foreach ($types as $type) {
            $stmtSelect->execute([$type]);
            if (!$stmtSelect->fetch()) {
                $stmtInsert->execute([$type, 'Tipo generado automáticamente']);
            }
        }
    }

    public function syncCompanies(array $companyNames)
    {
        $companyNames = array_unique(array_map('trim', $companyNames));
        if (!class_exists('CompanyModel')) {
            require_once __DIR__ . '/CompanyModel.php';
        }
        $cm = new CompanyModel();

        foreach ($companyNames as $company) {
            if ($company === '' || strcasecmp($company, 'laboratorios') === 0) {
                continue;
            }

            // addIfNotExists normaliza y evita duplicados
            $cm->addIfNotExists($company);
        }
    }
}
