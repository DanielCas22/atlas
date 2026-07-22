<?php
// Script para detectar y (opcionalmente) fusionar empresas duplicadas.
// Uso:
//  php tools/dedupe_companies.php        -> dry-run (recomienda acciones)
//  php tools/dedupe_companies.php --apply -> aplica los cambios (destructivo)

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/CompanyModel.php';
require_once __DIR__ . '/../models/ExamModel.php';

$apply = in_array('--apply', $argv, true);

$companyModel = new CompanyModel();
$db = Database::getInstance()->getConnection();

echo "Dedupe companies script. Mode: " . ($apply ? "APPLY" : "DRY-RUN") . "\n\n";

// Obtener todas las empresas (id + name originales)
$stmt = $db->query('SELECT id, name FROM security_companies');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groups = [];
foreach ($rows as $r) {
    $norm = $companyModel->normalizeCompanyName($r['name']);
    if ($norm === '') {
        $norm = '___EMPTY___' . $r['id'];
    }
    $groups[$norm][] = $r;
}

// Función para contar exámenes
function countExamsForCompany($db, $companyId) {
    $s = $db->prepare('SELECT COUNT(*) as c FROM exams WHERE company_id = ?');
    $s->execute([$companyId]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    return intval($r['c'] ?? 0);
}

$actions = [];
foreach ($groups as $norm => $list) {
    if (count($list) <= 1) continue;

    // Elegir canonical: el que tenga más exámenes, si empate, el de menor id
    $best = null;
    $bestCount = -1;
    foreach ($list as $l) {
        $c = countExamsForCompany($db, $l['id']);
        if ($c > $bestCount || ($c === $bestCount && ($best === null || $l['id'] < $best['id']))) {
            $best = ['id' => intval($l['id']), 'name' => $l['name'], 'exam_count' => $c];
            $bestCount = $c;
        }
    }

    $duplicates = array_filter($list, function($x) use ($best) { return intval($x['id']) !== intval($best['id']); });
    $actions[] = ['normalized' => $norm, 'canonical' => $best, 'duplicates' => array_values($duplicates)];
}

if (empty($actions)) {
    echo "No se encontraron grupos con duplicados según la normalización actual.\n";
    exit(0);
}

// Mostrar resumen
$totalDup = 0;
foreach ($actions as $act) {
    echo "Grupo: " . $act['normalized'] . "\n";
    echo "  Canonical => [{$act['canonical']['id']}] {$act['canonical']['name']} (exams: {$act['canonical']['exam_count']})\n";
    foreach ($act['duplicates'] as $d) {
        $count = countExamsForCompany($db, $d['id']);
        echo "    Duplicate => [{$d['id']}] {$d['name']} (exams: {$count})\n";
        $totalDup++;
    }
    echo "\n";
}

echo "Acciones propuestas: fusionar $totalDup empresas (re-asignar exámenes y eliminar filas duplicadas).\n";

if (!$apply) {
    echo "\nDRY-RUN: No se realizarán cambios. Para aplicar, ejecutar: php tools/dedupe_companies.php --apply\n";
    exit(0);
}

// Aplicar cambios
echo "\nAPLICANDO cambios...\n";

$baseDir = __DIR__ . '/../empresas_clasificadas';

try {
    $db->beginTransaction();

    foreach ($actions as $act) {
        $canonId = intval($act['canonical']['id']);
        $canonicalName = $act['canonical']['name'];

        // Asegurar carpeta canonical
        $canonicalFolderName = $companyModel->sanitizeCompanyFolderName($act['normalized']);
        $canonicalFolderPath = $baseDir . '/' . $canonicalFolderName;
        if (!is_dir($canonicalFolderPath)) {
            @mkdir($canonicalFolderPath, 0777, true);
        }

        foreach ($act['duplicates'] as $dup) {
            $dupId = intval($dup['id']);
            $dupName = $dup['name'];

            // Reasignar exámenes
            $u = $db->prepare('UPDATE exams SET company_id = ? WHERE company_id = ?');
            $u->execute([$canonId, $dupId]);
            $movedExams = $u->rowCount();

            // Eliminar la empresa duplicada
            $d = $db->prepare('DELETE FROM security_companies WHERE id = ?');
            $d->execute([$dupId]);

            // Mover/combinar carpetas en empresas_clasificadas: buscar carpetas que normalicen igual al grupo
            if (is_dir($baseDir)) {
                $folders = scandir($baseDir);
                foreach ($folders as $folder) {
                    if ($folder === '.' || $folder === '..') continue;
                    $path = $baseDir . '/' . $folder;
                    if (!is_dir($path)) continue;

                    if ($companyModel->normalizeCompanyName($folder) === $act['normalized']) {
                        // Si es la carpeta del canonical, saltar
                        $targetPath = $canonicalFolderPath;
                        if ($path === $targetPath) continue;

                        // Mover contenido de $path a $targetPath
                        $items = scandir($path);
                        foreach ($items as $it) {
                            if ($it === '.' || $it === '..') continue;
                            $src = $path . '/' . $it;
                            $dst = $targetPath . '/' . $it;
                            // Si existe archivo con mismo nombre, renombrar con sufijo
                            if (file_exists($dst)) {
                                $dst = $targetPath . '/' . time() . '_' . $it;
                            }
                            @rename($src, $dst);
                        }

                        // Eliminar carpeta duplicada vacía
                        @rmdir($path);
                    }
                }
            }

            echo "Fused duplicate [{$dupId}] {$dupName} -> canonical [{$canonId}] {$canonicalName} (exams moved: {$movedExams})\n";
        }
    }

    $db->commit();
    echo "\nOperación completada con éxito. Revisa el sistema y realiza backup si es necesario.\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error aplicando cambios: " . $e->getMessage() . "\n";
    exit(1);
}
