<?php
// compare_server.php
// Compara estructura local vs servidor sin modificar nada

$localBase  = 'D:\\xampp\\htdocs\\gestionmascotas';
$serverBase = '/home/dnsmaest/public_html/gestionmascotas';
$logFile    = __DIR__ . '/compare_server_' . date('Ymd_His') . '.html';

$ignore = ['node_modules', 'vendor', '.git', '.vscode', 'cgi-bin', 'logs', 'tmp'];

function shouldIgnore($path, $ignore) {
    foreach ($ignore as $i) if (stripos($path, $i) !== false) return true;
    return false;
}

function scanFiles($base, $ignore) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        $rel = str_replace($base . DIRECTORY_SEPARATOR, '', $item->getPathname());
        if (shouldIgnore($rel, $ignore)) continue;
        $files[$rel] = [
            'type'  => $item->isDir() ? 'dir' : 'file',
            'size'  => $item->isFile() ? $item->getSize() : 0,
            'mtime' => $item->getMTime(),
        ];
    }
    return $files;
}

// --- MAIN ---
echo "Analizando estructuras...\n";

$localFiles  = scanFiles($localBase, $ignore);
$serverFiles = scanFiles($serverBase, $ignore);

$onlyLocal   = array_diff_key($localFiles, $serverFiles);
$onlyServer  = array_diff_key($serverFiles, $localFiles);
$common      = array_intersect_key($localFiles, $serverFiles);
$diffs = [];
$equals = 0;

foreach ($common as $path => $meta) {
    $l = $localFiles[$path];
    $s = $serverFiles[$path];
    if ($l['size'] != $s['size'] || abs($l['mtime'] - $s['mtime']) > 5) {
        $diffs[$path] = [
            'local_size' => $l['size'], 'server_size' => $s['size'],
            'local_time' => date('Y-m-d H:i', $l['mtime']),
            'server_time' => date('Y-m-d H:i', $s['mtime']),
        ];
    } else $equals++;
}

$summary = [
    'total_local'  => count($localFiles),
    'total_server' => count($serverFiles),
    'only_local'   => count($onlyLocal),
    'only_server'  => count($onlyServer),
    'different'    => count($diffs),
    'identical'    => $equals,
];

// --- MODO CLI ---
if (php_sapi_name() === 'cli') {
    echo "\n=== COMPARACIÓN LOCAL VS SERVIDOR ===\n";
    echo "Total local: "  . $summary['total_local']  . "\n";
    echo "Total servidor: " . $summary['total_server'] . "\n";
    echo "Solo en local: "  . $summary['only_local']  . "\n";
    echo "Solo en servidor: " . $summary['only_server'] . "\n";
    echo "Diferentes: "      . $summary['different']   . "\n";
    echo "Iguales: "         . $summary['identical']   . "\n\n";

    if ($summary['different'] > 0) {
        echo "Archivos diferentes:\n";
        foreach ($diffs as $p => $d) echo " - $p\n";
    }
    exit;
}

// --- MODO WEB ---
$html = '<html><head><meta charset="utf-8"><title>Comparación Local vs Servidor</title>';
$html .= '<style>body{font-family:Arial;margin:20px;}h2{color:#663300;}table{width:100%;border-collapse:collapse;}td,th{border:1px solid #ccc;padding:6px;}th{background:#eee;}</style></head><body>';
$html .= '<h1>Comparación Local vs Servidor</h1>';
$html .= '<h2>Resumen</h2><pre>' . htmlspecialchars(json_encode($summary, JSON_PRETTY_PRINT)) . '</pre>';

$html .= '<h2>Solo en local (' . count($onlyLocal) . ')</h2><table><tr><th>Ruta</th></tr>';
foreach ($onlyLocal as $path => $_) $html .= '<tr><td>' . htmlspecialchars($path) . '</td></tr>';
$html .= '</table>';

$html .= '<h2>Solo en servidor (' . count($onlyServer) . ')</h2><table><tr><th>Ruta</th></tr>';
foreach ($onlyServer as $path => $_) $html .= '<tr><td>' . htmlspecialchars($path) . '</td></tr>';
$html .= '</table>';

$html .= '<h2>Diferencias (' . count($diffs) . ')</h2><table><tr><th>Archivo</th><th>Tamaño Local</th><th>Tamaño Servidor</th><th>Fecha Local</th><th>Fecha Servidor</th></tr>';
foreach ($diffs as $p => $d) {
    $html .= '<tr><td>' . htmlspecialchars($p) . '</td><td>' . $d['local_size'] . '</td><td>' . $d['server_size'] . '</td><td>' . $d['local_time'] . '</td><td>' . $d['server_time'] . '</td></tr>';
}
$html .= '</table>';

$html .= '<h3>Idénticos: ' . $equals . '</h3>';
$html .= '<p>Archivo generado: ' . basename($logFile) . '</p>';
$html .= '</body></html>';

file_put_contents($logFile, $html);
echo "<p>Informe generado: <a href='" . basename($logFile) . "'>Abrir informe</a></p>";
?>
