<?php
// Script para importar empresas desde carpetas "LISTADO NOMBRE"
// Ubica este archivo en la raíz del proyecto y ejecútalo una sola vez

echo "=== IMPORTADOR AUTOMÁTICO DE EMPRESAS ATLAS ===\n\n";

// Función para buscar carpetas LISTADO recursivamente
function buscarCarpetasListadoRecursivo($directorio, $maxDepth = 3, $currentDepth = 0) {
    $carpetas = [];

    if (!is_dir($directorio) || $currentDepth > $maxDepth) {
        return $carpetas;
    }

    $archivos = scandir($directorio);
    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;

        $rutaCompleta = $directorio . DIRECTORY_SEPARATOR . $archivo;

        if (is_dir($rutaCompleta)) {
            if (strpos($archivo, 'LISTADO ') === 0) {
                $carpetas[] = $archivo;
            }

            // Buscar recursivamente en subdirectorios
            $subCarpetas = buscarCarpetasListadoRecursivo($rutaCompleta, $maxDepth, $currentDepth + 1);
            $carpetas = array_merge($carpetas, $subCarpetas);
        }
    }

    return $carpetas;
}

// Directorios donde buscar automáticamente
$directorios_busqueda = [
    __DIR__, // Directorio del proyecto
    dirname(__DIR__), // Un nivel arriba
    'C:/xampp/htdocs', // Otros proyectos en xampp
];

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // En Windows, buscar en ubicaciones comunes
    $userName = getenv('USERNAME');
    $directorios_busqueda = array_merge($directorios_busqueda, [
        "C:/Users/$userName/Desktop",
        "C:/Users/$userName/Documents",
        "C:/Users/$userName/Downloads",
        "D:/", // Si existe unidad D
        "E:/", // Si existe unidad E
    ]);
}

$carpetas = [];
$directorios_revisados = [];

echo "🔍 Buscando carpetas LISTADO en todo el sistema...\n\n";

foreach ($directorios_busqueda as $directorio) {
    if (is_dir($directorio)) {
        echo "Buscando en: $directorio\n";
        $encontradas = buscarCarpetasListadoRecursivo($directorio);
        if (!empty($encontradas)) {
            $carpetas = array_merge($carpetas, $encontradas);
            $directorios_revisados[] = $directorio;
            echo "  ✅ Encontradas: " . count($encontradas) . " carpetas\n";
        } else {
            echo "  ❌ No se encontraron carpetas LISTADO\n";
        }
    } else {
        echo "Directorio no existe: $directorio\n";
    }
    echo "\n";
}

// Eliminar duplicados
$carpetas = array_unique($carpetas);

if (empty($carpetas)) {
    echo "❌ No se encontraron carpetas LISTADO en ninguna ubicación.\n\n";
    echo "💡 SOLUCIONES:\n";
    echo "1. Asegúrate de que tus carpetas LISTADO existan en tu sistema\n";
    echo "2. Copia los nombres de tus carpetas y pégalos manualmente en el array \$carpetas_manual\n";
    echo "3. O especifica la ruta exacta editando la variable \$directorio_busqueda_personalizada\n\n";

    // Opción para especificar ruta personalizada
    $directorio_busqueda_personalizada = ''; // Cambia esto por tu ruta, ej: 'C:/Mis Documentos/'

    if (!empty($directorio_busqueda_personalizada) && is_dir($directorio_busqueda_personalizada)) {
        echo "Buscando en ruta personalizada: $directorio_busqueda_personalizada\n";
        $carpetas = buscarCarpetasListadoRecursivo($directorio_busqueda_personalizada);
    }

    // Si aún no hay carpetas, usar lista manual
    if (empty($carpetas)) {
        $carpetas_manual = [
                                    'SEVICOL',
                                    'SEVIN',
                                    'SIRIUS',
                                    'STAR ABRIL',
                                    'TECNOLOGIAS INTEGRALES - TECNIS',
                                    'THE ELITE FLOWERS',
                                    'TOPGUARD',
                                    'TRANSBANK',
                                    'TRANSPORTADORA DE VALORES',
                                    'UCOLBUS',
                                    'UNION TEMPORAL TAC',
                                    'VATCO',
                                    'VIGIAS COLOMBIA SRL',
                                    'VIGIASER',
                                    'VIGILANCIA 007',
                                    'VIGILANCIA DE COLOMBIA VIP',
                                    'VIGONSA',
                                    'VISE',
                                    'VISION',
                                    'ZONAMEDICA',
                        'G4S RISK MANAGER',
                        'G4S SECURE',
                        'GENDARME',
                        'GIMANEL',
                        'GM FINANCIAL',
                        'GRAN METROPOLIS',
                        'GUAJIRA',
                        'HOLDING',
                        'HORIZONTAL',
                        'HORUS',
                        'IMPLEMENTAR',
                        'IMPROCAN',
                        'INTER',
                        'INTERPOVIG',
                        'INVERPROGRESO',
                        'ISVI',
                        'KEY STELL',
                        'LAGUS',
                        'LATAMSEC',
                        'LIRA',
                        'LOGRO',
                        'MAGNUS',
                        'MASTIN',
                        'MONSERRATE',
                        'MOTO MART',
                        'MULTISERVICIOS',
                        'NAPALES',
                        'NASER',
                        'NATIVA',
                        'NUTRIX',
                        'OIL GAS',
                        'OCONNOR',
                        'OMNITEMPUS',
                        'ONCOR',
                        'OPEN VIAS',
                        'ORIENTAL',
                        'PITHSBURG',
                        'PPH',
                        'PROSEGUR',
                        'QAP',
                        'RAMSAN',
                        'READ COL',
                        'RISK',
                        'SECURITY AND PROTECTION',
                        'SEGURIDAD 2000',
                        'SEGURIDAD ANDES',
                        'SEGURIDAD CANINA',
                        'SEGURIDAD COSMOS',
                        'SEGURIDAD DE COLOMBIA',
                        'SEGURIDAD FLORIDA',
                        'SEGURIDAD IMPERIO',
                        'SEGURIDAD INTEGRAL',
                        'SEGURIDAD JANO',
                        'SEGURIDAD PENTA',
                        'SEGURIDAD PRIVADA TEXAS',
                        'SEGURIDAD SUPERIOR',
                        'SEPECOL',
                        'SER SEGURIDAD',
                        'SERVICONCEL',
                        'SERVICONI',
                        'SERVIGTEC',
                        'SERVILIN',
                        'SERVISION',
              // Empresas extraídas de la imagen proporcionada (sin 'LISTADO' y omitiendo 'LABORATORIOS')
              '7-24',
              'ACOSTA',
              'ADMEGNEGOCIOS',
              'ADPORT',
              'ALAMBRES',
              'ALLIANCE',
              'ALPHA',
              'AMCOVID',
              'APOLO',
              'ASEISA',
              'ASEP',
              'ATALAYA',
              'ATLANTA',
              'ATLANTIS',
              'ATMOSFERA',
              'BIAKO',
              'BOCHICA',
              'BOGOTANA',
              'BRINKS',
              'CASTELL Y CIA LTDA',
              'CAXAR',
              'CENTINELA',
              'CENTRAL',
              'CEMENTOS TEQUENDAMA',
              'CIPRES',
              'CLASICA',
              'COLVISEG',
              'COMNALMICROS',
              'CONTROLAR',
              'COOPRESERVIS',
              'COOSERVI',
              'COOSERVIUNIDOS',
              'COOVIPORFAC',
              'COOVISOCIAL',
              'CORPS',
              'COSERVCREA',
              'COSERVIPP',
              'COVISUR',
              'DEAS',
              'DECAPOLIS',
              'DETECCION',
              'DYNAMIC DE COLOMBIA',
              'EFECTIVE',
              'ELIAR',
              'EMBAJADA EU',
              'EMBAJADA GRAN BRETAÑA',
              'EXITO DE COLOMBIA',
              'EXPERTOS SEGURIDAD',
              'EXPLORER',
              'EXTRA SEGURIDAD',
              'FALCON',
              'FERAC',
              'FIDELITY',
              'FORMULA SECRETA',
              'FORTOX',
              // Puedes agregar más empresas aquí si aparecen más carpetas
        ];

        if (!empty($carpetas_manual)) {
            $carpetas = $carpetas_manual;
            echo "📝 Usando lista manual de carpetas...\n\n";
        } else {
            echo "No hay carpetas para procesar.\n";
            echo "Edita el array \$carpetas_manual en este archivo con tus carpetas LISTADO.\n";
            exit(1);
        }
    }
}

echo "📂 Carpetas LISTADO encontradas (" . count($carpetas) . "):\n";
foreach ($carpetas as $carpeta) {
    echo "  • $carpeta\n";
}
echo "\n";

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/models/CompanyModel.php';

$db = Database::getInstance()->getConnection();
$model = new CompanyModel();

$agregadas = 0;
$existentes = 0;
$errores = 0;

echo "🚀 Procesando empresas...\n\n";

foreach ($carpetas as $carpeta) {
    // Extraer el nombre después de "LISTADO "
    $nombre = trim(preg_replace('/^LISTADO[ _-]*/i', '', $carpeta));
    if (empty($nombre)) {
        echo "⚠️  Saltando carpeta sin nombre válido: $carpeta\n";
        continue;
    }

    // Verificar si ya existe
    $stmt = $db->prepare('SELECT id FROM security_companies WHERE name = ?');
    $stmt->execute([$nombre]);
    if (!$stmt->fetch()) {
        try {
            $model->add($nombre);
            echo "✅ Agregada: $nombre\n";
            $agregadas++;
        } catch (Exception $e) {
            echo "❌ Error al agregar '$nombre': " . $e->getMessage() . "\n";
            $errores++;
        }
    } else {
        echo "⭕ Ya existe: $nombre\n";
        $existentes++;
    }
}

echo "\n";
echo "📊 RESUMEN FINAL\n";
echo "═══════════════════════════════════════════\n";
echo "✅ Empresas agregadas: $agregadas\n";
echo "⭕ Empresas ya existentes: $existentes\n";
echo "❌ Errores: $errores\n";
echo "📂 Total procesadas: " . count($carpetas) . "\n";

if ($agregadas > 0) {
    echo "\n🎉 ¡IMPORTACIÓN EXITOSA!\n";
    echo "═══════════════════════════════════════════\n";
    echo "Las empresas han sido agregadas a la base de datos.\n";
    echo "📍 Ver en gestión: http://localhost/atlas/index.php?c=company&a=list\n";
    echo "📝 Aparecerán en: Formulario de agregar exámenes\n";
} elseif ($existentes > 0) {
    echo "\nℹ️  Todas las empresas ya existían en la base de datos.\n";
} else {
    echo "\n❌ No se pudo importar ninguna empresa.\n";
}

echo "\n💡 Si necesitas agregar más empresas, ejecuta este script nuevamente.\n";
?>
