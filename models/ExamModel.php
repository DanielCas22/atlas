<?php

class ExamModel extends BaseModel
{
    public function all()
    {
        $sql = 'SELECT e.id, sc.name as company, et.name as exam_type, e.candidate_name, e.status, e.created_at, e.updated_at
                FROM exams e
                JOIN security_companies sc ON e.company_id = sc.id
                JOIN exam_types et ON e.exam_type_id = et.id
                ORDER BY e.created_at DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function add($company_id, $exam_type_id, $candidate_name)
    {
        $stmt = $this->db->prepare('INSERT INTO exams (company_id, exam_type_id, candidate_name) VALUES (?, ?, ?)');
        return $stmt->execute([$company_id, $exam_type_id, $candidate_name]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE exams SET status = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function getCompanies()
    {
        $stmt = $this->db->query('SELECT id, name FROM security_companies ORDER BY name');
        return $stmt->fetchAll();
    }

    public function getExamTypes()
    {
        $stmt = $this->db->query('SELECT id, name FROM exam_types ORDER BY name');
        return $stmt->fetchAll();
    }
}
