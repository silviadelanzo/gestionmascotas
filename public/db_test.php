<?php
/**
 * db_test.php - Test completo de conexión y estructura de DB
 * Version: 2.0 con análisis de columnas tipo/rubro
 * NOTA: Excluir de deploy en producción (.cpanel.yml)
 */

// Cargar config
if (!file_exists(__DIR__ . '/includes/config.php')) {
    die("❌ ERROR: No existe includes/config.php");
}

require_once __DIR__ . '/includes/config.php';

echo "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='utf-8'>\n<title>DB Test - " . htmlspecialchars(DB_NAME) . "</title>\n<style>body{font-family:system-ui;margin:2rem;max-width:1200px;} h1{color:#333;} h2{color:#666;margin-top:2rem;} .ok{color:#28a745;} .error{color:#dc3545;} table{border-collapse:collapse;margin:1rem 0;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;font-size:0.9em;} th{background:#f8f9fa;font-weight:600;} .timestamp{color:#6c757d;font-size:0.9em;} .highlight{background:#ffffcc;}</style>\n</head>\n<body>\n";

echo "<h1>🔍 Test de Conexión y Estructura DB</h1>\n";
echo "<p class='timestamp'><strong>Archivo:</strong> " . __FILE__ . "<br><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

// Info de configuración
echo "<h2>📋 Configuración</h2>\n";
echo "<table>\n";
echo "<tr><th>Constante</th><th>Valor</th></tr>\n";
echo "<tr><td>APP_URL</td><td>" . htmlspecialchars(defined('APP_URL') ? APP_URL : '(no definida)') . "</td></tr>\n";
echo "<tr><td>DB_HOST</td><td>" . htmlspecialchars(DB_HOST) . "</td></tr>\n";
echo "<tr><td>DB_NAME</td><td>" . htmlspecialchars(DB_NAME) . "</td></tr>\n";
echo "<tr><td>DB_USER</td><td>" . htmlspecialchars(DB_USER) . "</td></tr>\n";
echo "<tr><td>DB_PASS</td><td>" . (DB_PASS ? '***' . substr(DB_PASS, -4) : '(vacío)') . "</td></tr>\n";
echo "</table>\n";

// Test de conexión
echo "<h2>🔌 Test de Conexión</h2>\n";
try {
    require_once __DIR__ . '/includes/db.php';
    $pdo = db();
    echo "<p class='ok'>✅ Conexión exitosa a " . htmlspecialchars(DB_NAME) . "</p>\n";
    
    // MySQL NOW()
    $stmt = $pdo->query("SELECT NOW() AS now, DATABASE() AS db");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>MySQL NOW():</strong> " . htmlspecialchars($row['now']) . "</p>\n";
    echo "<p><strong>DATABASE():</strong> " . htmlspecialchars($row['db']) . "</p>\n";
    
    // Verificar tabla servicios
    echo "<h2>📊 Estructura de Tabla 'servicios'</h2>\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM servicios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>\n<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>\n";
    $hasRubro = false;
    $hasTipo = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'rubro') $hasRubro = true;
        if ($col['Field'] === 'tipo') $hasTipo = true;
        $highlight = (in_array($col['Field'], ['rubro', 'tipo'])) ? ' class="highlight"' : '';
        echo "<tr$highlight>";
        echo "<td><strong>" . htmlspecialchars($col['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    echo "<p><strong>Columna 'rubro':</strong> " . ($hasRubro ? "<span class='ok'>✅ Existe</span>" : "<span class='error'>❌ No existe</span>") . "</p>\n";
    echo "<p><strong>Columna 'tipo':</strong> " . ($hasTipo ? "<span class='ok'>✅ Existe</span>" : "<span class='error'>❌ No existe</span>") . "</p>\n";
    
    // Contar registros
    $count = $pdo->query("SELECT COUNT(*) FROM servicios")->fetchColumn();
    echo "<p><strong>Total registros:</strong> " . (int)$count . "</p>\n";
    
    if ($count > 0) {
        // Estadísticas de tipos
        echo "<h2>📈 Estadísticas de Columnas Tipo/Rubro</h2>\n";
        echo "<table>\n<tr><th>Columna</th><th>Con Valor</th><th>Vacío/NULL</th><th>% Lleno</th></tr>\n";
        
        if ($hasRubro) {
            $stmt = $pdo->query("SELECT COUNT(CASE WHEN rubro IS NOT NULL AND rubro != '' THEN 1 END) as con_valor, COUNT(CASE WHEN rubro IS NULL OR rubro = '' THEN 1 END) as sin_valor FROM servicios");
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $pct = round(($stats['con_valor'] / $count) * 100, 1);
            echo "<tr><td><strong>rubro</strong></td><td>" . $stats['con_valor'] . "</td><td>" . $stats['sin_valor'] . "</td><td>" . $pct . "%</td></tr>\n";
        }
        
        if ($hasTipo) {
            $stmt = $pdo->query("SELECT COUNT(CASE WHEN tipo IS NOT NULL AND tipo != '' THEN 1 END) as con_valor, COUNT(CASE WHEN tipo IS NULL OR tipo = '' THEN 1 END) as sin_valor FROM servicios");
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            $pct = round(($stats['con_valor'] / $count) * 100, 1);
            echo "<tr><td><strong>tipo</strong></td><td>" . $stats['con_valor'] . "</td><td>" . $stats['sin_valor'] . "</td><td>" . $pct . "%</td></tr>\n";
        }
        echo "</table>\n";
        
        // Tipos únicos
        echo "<h2>🏷️ Valores Únicos de Tipo</h2>\n";
        if ($hasRubro && $hasTipo) {
            $stmt = $pdo->query("(SELECT DISTINCT rubro AS tipo FROM servicios WHERE rubro IS NOT NULL AND rubro != '') UNION (SELECT DISTINCT tipo AS tipo FROM servicios WHERE tipo IS NOT NULL AND tipo != '') ORDER BY tipo");
            $tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Tipos únicos (UNION de rubro + tipo):</strong></p>\n";
            echo "<p style='background:#e7f3ff;padding:1rem;border-radius:4px;'>" . implode(', ', array_map('htmlspecialchars', $tipos)) . "</p>\n";
        } elseif ($hasRubro) {
            $stmt = $pdo->query("SELECT DISTINCT rubro FROM servicios WHERE rubro IS NOT NULL AND rubro != '' ORDER BY rubro");
            $tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Rubros únicos:</strong> " . implode(', ', array_map('htmlspecialchars', $tipos)) . "</p>\n";
        } elseif ($hasTipo) {
            $stmt = $pdo->query("SELECT DISTINCT tipo FROM servicios WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo");
            $tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p><strong>Tipos únicos:</strong> " . implode(', ', array_map('htmlspecialchars', $tipos)) . "</p>\n";
        }
        
        // Muestra de datos con tipo calculado
        echo "<h2>📝 Muestra de Datos (últimos 10 registros)</h2>\n";
        
        // Determinar expresión para tipo (igual que en servicios.php)
        if ($hasRubro && $hasTipo) {
            $tipoExpr = "COALESCE(NULLIF(rubro,''), NULLIF(tipo,'')) AS tipo_calc";
        } elseif ($hasRubro) {
            $tipoExpr = "rubro AS tipo_calc";
        } elseif ($hasTipo) {
            $tipoExpr = "tipo AS tipo_calc";
        } else {
            $tipoExpr = "NULL AS tipo_calc";
        }
        
        $sql = "SELECT id, nombre, $tipoExpr";
        if ($hasRubro) $sql .= ", rubro";
        if ($hasTipo) $sql .= ", tipo";
        $sql .= ", ciudad, provincia FROM servicios ORDER BY id DESC LIMIT 10";
        
        $stmt = $pdo->query($sql);
        $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>\n<tr><th>ID</th><th>Nombre</th><th class='highlight'>TIPO CALC</th>";
        if ($hasRubro) echo "<th>rubro</th>";
        if ($hasTipo) echo "<th>tipo</th>";
        echo "<th>Ciudad</th><th>Provincia</th></tr>\n";
        
        foreach ($sample as $row) {
            $tipoEmpty = empty($row['tipo_calc']);
            echo "<tr" . ($tipoEmpty ? " style='background:#ffe0e0;'" : "") . ">";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td class='highlight' style='font-weight:600;'>" . ($tipoEmpty ? "⚠️ (vacío)" : htmlspecialchars($row['tipo_calc'])) . "</td>";
            if ($hasRubro) echo "<td>" . (empty($row['rubro']) ? "<em style='color:#999;'>vacío</em>" : htmlspecialchars($row['rubro'])) . "</td>";
            if ($hasTipo) echo "<td>" . (empty($row['tipo']) ? "<em style='color:#999;'>vacío</em>" : htmlspecialchars($row['tipo'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['ciudad']) . "</td>";
            echo "<td>" . htmlspecialchars($row['provincia']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "<p class='timestamp'><em>TIPO CALC usa: COALESCE(NULLIF(rubro,''), NULLIF(tipo,'')) — misma lógica que servicios.php</em></p>\n";
    }
    
    echo "<h2>✅ Conclusión</h2>\n";
    echo "<div style='background:#d4edda;padding:1rem;border-radius:4px;border-left:4px solid #28a745;'>\n";
    echo "<p><strong>La conexión funciona correctamente.</strong></p>\n";
    echo "<p>Si tipos NO aparecen en servicios.php, verifica:</p>\n";
    echo "<ul>\n";
    echo "<li>✓ Que servicios.php esté actualizado con código COALESCE (revisa timestamp en File Manager)</li>\n";
    echo "<li>✓ Que el deploy haya copiado el archivo actualizado al servidor</li>\n";
    echo "<li>✓ Que no haya errores PHP en servicios.php (revisa error_log)</li>\n";
    echo "<li>✓ Cache del navegador (Ctrl+F5 para recargar sin caché)</li>\n";
    echo "</ul>\n";
    echo "</div>\n";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error de conexión:</p>\n";
    echo "<pre style='background:#f8d7da;padding:1rem;border-radius:4px;border:1px solid #dc3545;'>" . htmlspecialchars($e->getMessage()) . "</pre>\n";
    echo "<h3>Verifica:</h3>\n";
    echo "<ul>\n";
    echo "<li>DB_HOST es correcto ('localhost' o '127.0.0.1')</li>\n";
    echo "<li>DB_NAME, DB_USER y DB_PASS son correctos</li>\n";
    echo "<li>El usuario tiene ALL PRIVILEGES en la base de datos</li>\n";
    echo "<li>MySQL está corriendo</li>\n";
    echo "</ul>\n";
}

echo "<hr style='margin:3rem 0;'>\n";
echo "<p class='timestamp'><strong>⚠️ IMPORTANTE:</strong> Este archivo debe excluirse del deploy en producción (ver .cpanel.yml)</p>\n";
echo "</body>\n</html>";
