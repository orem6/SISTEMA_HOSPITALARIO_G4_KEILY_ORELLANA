<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$passed = 0; $failed = 0;

function assertTest(string $desc, bool $cond): void {
    global $passed, $failed;
    $cond ? $passed++ : $failed++;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $desc . "\n";
}

echo "=== ASII-25 — Validación Semana 3 (codsebas) ===\n";
$required = [
    'README.md', 'INFORME.md', 'DEFENSA_ORAL.md', 'DECLARACION_IA.md',
    'docs/01-architectural-views.md', 'docs/02-design-patterns.md',
    'scripts/run-validation.php'
];

$formatOk = true; $totalLines = 0;
foreach ($required as $f) {
    $p = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    assertTest("Archivo presente y no vacío: {$f}", file_exists($p) && filesize($p) > 0);
    $c = (string) file_get_contents($p);
    if (str_starts_with($c, "\xEF\xBB\xBF") || str_contains($c, "\r")) $formatOk = false;
    foreach (explode("\n", $c) as $l) {
        if (preg_match('/[ \t]+$/', $l)) $formatOk = false;
    }
    $totalLines += count(file($p));
}
assertTest("Todos los archivos cumplen codificación UTF-8 sin BOM", $formatOk);
assertTest("Todos los archivos usan saltos de línea LF (Unix)", $formatOk);
assertTest("Cero espacios en blanco residuales al final de línea", $formatOk);

$vPath = $baseDir . '/docs/01-architectural-views.md';
$vContent = file_exists($vPath) ? (string) file_get_contents($vPath) : '';
assertTest("Vista de Procesos definida formalmente (fail-fast)", str_contains($vContent, 'Vista de Procesos') && str_contains($vContent, 'fail-fast'));
assertTest("Vista de Componentes definida formalmente", str_contains($vContent, 'Vista de Componentes'));
assertTest("Vista de Despliegue con contenedor PostgreSQL 16+ efímero", str_contains($vContent, 'Vista de Despliegue') && str_contains($vContent, 'PostgreSQL 16+'));

$pPath = $baseDir . '/docs/02-design-patterns.md';
$pContent = file_exists($pPath) ? (string) file_get_contents($pPath) : '';
assertTest("Cita y enlace formal a fuente teórica obligatoria (MVP Cluster)", str_contains($pContent, 'https://mvpcluster.com/diseno-de-software-2/'));
assertTest("Referencia al autor de la cátedra (Luis Miguel Rodríguez González)", str_contains($pContent, 'Luis Miguel Rodríguez González'));
assertTest("Patrón Chain of Responsibility (QualityGateChainHandler y secuencia)", str_contains($pContent, 'QualityGateChainHandler') && str_contains($pContent, 'PintQualityHandler') && str_contains($pContent, 'PlaywrightQualityHandler'));
assertTest("Patrón Builder (TestHarnessBuilder determinista)", str_contains($pContent, 'TestHarnessBuilder') && str_contains($pContent, 'build'));
assertTest("Patrón Observer (EvidenceSealObserver con sellado SHA-256)", str_contains($pContent, 'QualityObserverInterface') && str_contains($pContent, 'EvidenceSealObserver') && str_contains($pContent, 'sha256'));

preg_match_all('/```php\n(.*?)\n```/s', $pContent, $codeMatches);
$snippetsValid = count($codeMatches[1]) > 0;
foreach ($codeMatches[1] as $idx => $snippet) {
    $codeToLint = str_starts_with(trim($snippet), '<?php') ? $snippet : "<?php\n" . $snippet;
    $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "snippet_{$idx}.php";
    file_put_contents($tmpFile, $codeToLint);
    $res = @shell_exec('php -l "' . $tmpFile . '" 2>&1');
    @unlink($tmpFile);
    if ($res === null || !str_contains($res, 'No syntax errors detected')) $snippetsValid = false;
}
assertTest("Sintaxis de código PHP en patrones 100% válida (php -l)", $snippetsValid);

$syntax = @shell_exec('php -l "' . __FILE__ . '" 2>&1');
assertTest("Sintaxis de script de validación impecable (PHP 8.2+)", $syntax !== null && str_contains($syntax, 'No syntax errors detected'));
assertTest("Total de líneas agregadas en módulo ({$totalLines}) <= 350 (límite cátedra 400)", $totalLines <= 350);

echo "\nResultados: {$passed} pasadas, {$failed} fallidas. Total líneas: {$totalLines} / 320 presupuesto\n";
exit($failed === 0 ? 0 : 1);
