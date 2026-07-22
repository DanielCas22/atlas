<?php

class CompanyModel extends BaseModel
{
    public function all()
    {
        $stmt = $this->db->query('SELECT id, name, contact FROM security_companies ORDER BY name');
        $companies = $stmt->fetchAll();
        foreach ($companies as &$company) {
            $company['name'] = $this->normalizeCompanyName($company['name']);
        }
        return $companies;
    }

    public function allWithExamCounts()
    {
        $sql = 'SELECT sc.id, sc.name, COUNT(e.id) as exam_count
                FROM security_companies sc
                LEFT JOIN exams e ON sc.id = e.company_id
                GROUP BY sc.id, sc.name
                ORDER BY sc.name';
        $companies = $this->db->query($sql)->fetchAll();
        foreach ($companies as &$company) {
            $company['name'] = $this->normalizeCompanyName($company['name']);
        }
        return $companies;
    }

    public function countAll()
    {
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM security_companies');
        $row = $stmt->fetch();
        return intval($row['total'] ?? 0);
    }

    public function countCompaniesWithExams()
    {
        $stmt = $this->db->query('SELECT COUNT(DISTINCT company_id) as total FROM exams');
        $row = $stmt->fetch();
        return intval($row['total'] ?? 0);
    }

    public function normalizeCompanyName(string $name)
    {
        // Eliminar prefijos como LISTADO
        $clean = preg_replace('/^LISTADO[ _-]*/iu', '', $name);
        
        // Reemplazar múltiples guiones bajos por espacio
        $clean = preg_replace('/_+/', ' ', $clean);
        
        // Corregir errores de encoding de COMPAÑÍA
        $clean = str_replace('COMPA_IA', 'COMPAÑÍA', $clean);
        $clean = str_replace('COMPA IA', 'COMPAÑÍA', $clean);
        $clean = str_replace('COMPA__A', 'COMPAÑÍA', $clean);
        $clean = str_replace('COMPA A', 'COMPAÑÍA', $clean);
        
        // Corregir errores de AMÉRICA
        $clean = str_replace('AM_RICA', 'AMÉRICA', $clean);
        $clean = str_replace('AM RICA', 'AMÉRICA', $clean);
        
        // Eliminar guiones triples o más
        $clean = preg_replace('/-{3,}/', ' - ', $clean);
        
        // Limpiar espacios múltiples
        $clean = preg_replace('/\s+/', ' ', $clean);
        
        return trim($clean);
    }

    public function sanitizeCompanyFolderName(string $name)
    {
        $folder = trim($name);
        
        // Reemplazar caracteres especiales prohibidos en Windows/Linux
        // < > : " / \ | ? * y algunos otros caracteres problemáticos
        $folder = str_replace(
            ['<', '>', ':', '"', '/', '\\', '|', '?', '*', '\0'],
            '_',
            $folder
        );
        
        // Reemplazar caracteres acentuados y especiales que puedan causar problemas
        $folder = preg_replace('/[^a-zA-Z0-9\s\-_()]/u', '', $folder);
        
        // Reemplazar espacios múltiples con un solo espacio
        $folder = preg_replace('/\s+/', ' ', $folder);
        
        // Limitar longitud a 200 caracteres (Windows permite 255 pero dejamos margen)
        $folder = substr(trim($folder), 0, 200);
        
        // Asegurar que no esté vacío
        if (empty(trim($folder))) {
            $folder = 'Empresa_Sin_Nombre';
        }
        
        return trim($folder);
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
                $id = $this->addIfNotExists($companyName);
                if ($id) {
                    // Si ya existía, addIfNotExists retorna su id; si fue agregada, retorna nuevo id
                    $summary['added']++;
                } else {
                    $summary['existing']++;
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
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        // Normalizar nombre antes de insertar para evitar variantes que provoquen duplicados
        $name = $this->normalizeCompanyName($name);

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
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        // Intento 1: búsqueda exacta rápida
        $stmt = $this->db->prepare('SELECT id, name, contact FROM security_companies WHERE name = ? LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }

        // Intento 2: comparar por nombre normalizado en PHP (maneja variantes/encoding)
        $normalizedTarget = $this->normalizeCompanyName($name);
        $stmt = $this->db->query('SELECT id, name, contact FROM security_companies');
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) {
            if ($this->normalizeCompanyName($r['name']) === $normalizedTarget) {
                return $r;
            }
        }

        return false;
    }

    public function addIfNotExists($name, $contact = null)
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        // Normalizar antes de buscar/crear
        $normalized = $this->normalizeCompanyName($name);

        $company = $this->findByName($normalized);
        if ($company) {
            return intval($company['id']);
        }

        $this->add($normalized, $contact);
        return intval($this->db->lastInsertId());
    }

    public function delete($id)
    {
        if (empty($id)) {
            return false;
        }

        // Obtener info de la empresa antes de eliminar
        $company = $this->find($id);
        $companyName = $company ? $company['name'] : null;

        if (!class_exists('ExamModel')) {
            require_once __DIR__ . '/ExamModel.php';
        }

        $examModel = new ExamModel();
        $examModel->deleteByCompanyId($id);

        $stmt = $this->db->prepare('DELETE FROM security_companies WHERE id = ?');
        $result = $stmt->execute([$id]);

        // Si se eliminó correctamente, eliminar también la carpeta en empresas_clasificadas
        if ($result && $companyName) {
            $this->deleteCompanyFolder($companyName);
        }

        return $result;
    }

    /**
     * Eliminar la carpeta de una empresa en empresas_clasificadas
     */
    private function deleteCompanyFolder($companyName)
    {
        $baseDir = __DIR__ . '/../empresas_clasificadas';
        
        if (!is_dir($baseDir)) {
            return false;
        }

        // Buscar carpeta que coincida con el nombre normalizado
        $normalizedName = $this->normalizeCompanyName($companyName);
        
        $folders = scandir($baseDir);
        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }
            
            $folderPath = $baseDir . '/' . $folder;
            if (!is_dir($folderPath)) {
                continue;
            }

            // Comparar nombres normalizados
            if ($this->normalizeCompanyName($folder) === $normalizedName) {
                // Eliminar carpeta y su contenido
                $this->deleteDirectory($folderPath);
                return true;
            }
        }

        return false;
    }

    /**
     * Eliminar directorio y todo su contenido
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        return @rmdir($dir);
    }

    /**
     * Eliminar múltiples empresas por sus IDs
     * @param array $ids Array de IDs de empresas a eliminar
     * @return array Resultado con 'success' y 'deleted_count'
     */
    public function deleteMultiple(array $ids)
    {
        if (empty($ids)) {
            return ['success' => false, 'deleted_count' => 0, 'error' => 'No se proporcionaron empresas a eliminar'];
        }

        // Validar que todos los IDs sean números enteros
        $ids = array_filter($ids, function($id) {
            return is_numeric($id) && intval($id) > 0;
        });
        $ids = array_map('intval', $ids);

        if (empty($ids)) {
            return ['success' => false, 'deleted_count' => 0, 'error' => 'IDs de empresas inválidos'];
        }

        $deletedCount = 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        try {
            // Obtener info de las empresas antes de eliminar
            $companiesToDelete = [];
            foreach ($ids as $id) {
                $company = $this->find($id);
                if ($company) {
                    $companiesToDelete[] = $company['name'];
                }
            }

            // Primero eliminar todos los exámenes asociados a estas empresas
            if (!class_exists('ExamModel')) {
                require_once __DIR__ . '/ExamModel.php';
            }
            $examModel = new ExamModel();
            
            foreach ($ids as $id) {
                $examModel->deleteByCompanyId($id);
            }

            // Eliminar las empresas
            $sql = "DELETE FROM security_companies WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($ids);
            $deletedCount = $stmt->rowCount();

            // Eliminar las carpetas correspondientes
            foreach ($companiesToDelete as $companyName) {
                $this->deleteCompanyFolder($companyName);
            }

            return ['success' => true, 'deleted_count' => $deletedCount, 'error' => null];
        } catch (Exception $e) {
            return ['success' => false, 'deleted_count' => $deletedCount, 'error' => $e->getMessage()];
        }
    }

    /**
     * Eliminar todas las empresas cuyo nombre consiste solo en dígitos.
     * Retorna un resumen con conteos y nombres eliminados.
     */
    public function deleteNumericNamedCompanies()
    {
        $summary = ['deleted' => 0, 'failed' => 0, 'names' => []];

        $stmt = $this->db->query("SELECT id, name FROM security_companies WHERE name REGEXP '^[0-9]+$'");
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            return $summary;
        }

        foreach ($rows as $row) {
            try {
                $id = intval($row['id']);
                $name = $row['name'];
                // Usar el método delete para reutilizar eliminación de exámenes y carpeta
                if ($this->delete($id)) {
                    $summary['deleted']++;
                    $summary['names'][] = $name;
                } else {
                    $summary['failed']++;
                }
            } catch (Exception $e) {
                $summary['failed']++;
            }
        }

        return $summary;
    }
}
