<?php

class CompanyModel extends BaseModel
{
    public function all()
    {
        $stmt = $this->db->query('SELECT id, name, contact FROM security_companies ORDER BY name');
        return $stmt->fetchAll();
    }

    public function allWithExamCounts()
    {
        $sql = 'SELECT sc.id, sc.name, COUNT(e.id) as exam_count
                FROM security_companies sc
                LEFT JOIN exams e ON sc.id = e.company_id
                GROUP BY sc.id, sc.name
                ORDER BY sc.name';
        return $this->db->query($sql)->fetchAll();
    }

    public function searchByName($searchTerm)
    {
        $sql = 'SELECT sc.id, sc.name, COUNT(e.id) as exam_count
                FROM security_companies sc
                LEFT JOIN exams e ON sc.id = e.company_id
                WHERE sc.name LIKE ?
                GROUP BY sc.id, sc.name
                ORDER BY sc.name';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['%' . $searchTerm . '%']);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT id, name, contact FROM security_companies WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function add($name, $contact = null)
    {
        $stmt = $this->db->prepare('INSERT INTO security_companies (name, contact) VALUES (?, ?)');
        return $stmt->execute([$name, $contact]);
    }

    public function update($id, $name, $contact = null)
    {
        $stmt = $this->db->prepare('UPDATE security_companies SET name = ?, contact = ? WHERE id = ?');
        return $stmt->execute([$name, $contact, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM security_companies WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
