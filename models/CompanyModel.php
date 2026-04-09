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

    public function normalizeCompanyName(string $name)
    {
        $clean = preg_replace('/^LISTADO[ _-]*/iu', '', $name);
        return trim($clean);
    }

    public function sanitizeCompanyFolderName(string $name)
    {
        $folder = trim($name);
        $folder = preg_replace('/[\\\/]+/', '_', $folder);
        $folder = preg_replace('/\s+/', ' ', $folder);
        $folder = trim($folder);
        return $folder;
    }

    public function syncClassifiedCompanies()
    {
        $baseDir = __DIR__ . '/../empresas_clasificadas';
        $summary = ['added' => 0, 'existing' => 0, 'failed' => 0, 'folders_created' => 0];

        if (!is_dir($baseDir)) {
            return $summary;
        }

        $classifiedFolders = [];
        foreach (scandir($baseDir) as $companyDir) {
            if ($companyDir === '.' || $companyDir === '..') {
                continue;
            }

            $companyPath = $baseDir . '/' . $companyDir;
            if (!is_dir($companyPath)) {
                continue;
            }

            $companyName = $this->normalizeCompanyName($companyDir);
            if ($companyName === '') {
                continue;
            }

            $classifiedFolders[$companyName] = $companyDir;
        }

        foreach ($classifiedFolders as $companyName => $companyDir) {
            try {
                if ($this->findByName($companyName)) {
                    $summary['existing']++;
                } else {
                    $this->add($companyName);
                    $summary['added']++;
                }
            } catch (Exception $e) {
                error_log('Error sincronizando empresa desde archivos clasificados: ' . $e->getMessage());
                $summary['failed']++;
            }
        }

        $dbCompanies = $this->all();
        foreach ($dbCompanies as $company) {
            $companyName = trim($company['name']);
            if ($companyName === '') {
                continue;
            }

            if (isset($classifiedFolders[$companyName])) {
                continue;
            }

            $folderName = $this->sanitizeCompanyFolderName($companyName);
            $folderPath = $baseDir . '/' . $folderName;

            if (!is_dir($folderPath)) {
                if (@mkdir($folderPath, 0777, true)) {
                    $summary['folders_created']++;
                }
            }
        }

        return $summary;
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

    public function findByName($name)
    {
        $stmt = $this->db->prepare('SELECT id, name, contact FROM security_companies WHERE name = ? LIMIT 1');
        $stmt->execute([$name]);
        return $stmt->fetch();
    }

    public function addIfNotExists($name, $contact = null)
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $company = $this->findByName($name);
        if ($company) {
            return $company['id'];
        }

        $this->add($name, $contact);
        return $this->db->lastInsertId();
    }

    public function delete($id)
    {
        if (empty($id)) {
            return false;
        }

        if (!class_exists('ExamModel')) {
            require_once __DIR__ . '/ExamModel.php';
        }

        $examModel = new ExamModel();
        $examModel->deleteByCompanyId($id);

        $stmt = $this->db->prepare('DELETE FROM security_companies WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
