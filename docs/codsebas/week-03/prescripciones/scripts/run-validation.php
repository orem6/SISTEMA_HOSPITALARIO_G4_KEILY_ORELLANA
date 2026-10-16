<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$passed = 0; $failed = 0;

function assertTest(string $desc, bool $cond): void {
    global $passed, $failed;
    $cond ? $passed++ : $failed++;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $desc . "\n";
}

echo "=== ASII-15 — Validación Arquitectura y Patrones Semana 3 (codsebas) ===\n";
$required = [
    'README.md', 'INFORME.md', 'DEFENSA_ORAL.md', 'DECLARACION_IA.md',
    'docs/01-architectural-views.md', 'docs/02-design-patterns.md',
    'scripts/run-validation.php'
];

$formatOk = true; $totalLines = 0;
foreach ($required as $f) {
    $p = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    assertTest("Archivo presente: {$f}", file_exists($p) && filesize($p) > 0);
    $c = (string)file_get_contents($p);
    if (str_starts_with($c, "\xEF\xBB\xBF") || str_contains($c, "\r")) $formatOk = false;
    foreach (explode("\n", $c) as $l) {
        if (preg_match('/[ \t]+$/', $l)) $formatOk = false;
    }
    $totalLines += count(file($p));
}
assertTest("Formato UTF-8 no-BOM, LF y sin trailing spaces", $formatOk);

$viewsDoc = (string)file_get_contents($baseDir . '/docs/01-architectural-views.md');
assertTest("Vista Lógica documentada", str_contains($viewsDoc, 'Vista Lógica'));
assertTest("Vista de Componentes y Despliegue documentada", str_contains($viewsDoc, 'Vista de Componentes y Despliegue'));
assertTest("Aislamiento multi-tenant por X-Tenant-ID", str_contains($viewsDoc, 'X-Tenant-ID'));

$patternsDoc = (string)file_get_contents($baseDir . '/docs/02-design-patterns.md');
assertTest("Fuente teórica obligatoria mvpcluster.com", str_contains($patternsDoc, 'mvpcluster.com/diseno-de-software-2/'));
assertTest("Estrategia DirectAllergyRuleStrategy", str_contains($patternsDoc, 'class DirectAllergyRuleStrategy'));
assertTest("Estrategia CrossReactivityStrategy", str_contains($patternsDoc, 'class CrossReactivityStrategy'));
assertTest("Evento PrescriptionIssuedEvent", str_contains($patternsDoc, 'class PrescriptionIssuedEvent'));
assertTest("Evento AllergyAlertTriggeredEvent", str_contains($patternsDoc, 'class AllergyAlertTriggeredEvent'));
assertTest("Despachador de eventos Observer", str_contains($patternsDoc, 'class PrescriptionEventDispatcher'));
assertTest("Contratos Repository Pattern", str_contains($patternsDoc, 'PrescriptionRepositoryInterface') && str_contains($patternsDoc, 'AllergyRepositoryInterface'));
assertTest("Orquestador PrescriptionService", str_contains($patternsDoc, 'class PrescriptionService'));

preg_match('/```php\s*(<\?php.*?)```/s', $patternsDoc, $m);
$phpOk = false;
if (isset($m[1])) {
    $tmp = tempnam(sys_get_temp_dir(), 'php_');
    file_put_contents($tmp, $m[1]);
    exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $out, $ret);
    @unlink($tmp);
    $phpOk = ($ret === 0);
}
assertTest("Sintaxis PHP 8.2+ en código de patrones (php -l)", $phpOk);

exec('php -l ' . escapeshellarg(__FILE__) . ' 2>&1', $outSelf, $retSelf);
assertTest("Sintaxis PHP 8.2+ en run-validation.php", $retSelf === 0);

assertTest("Presupuesto estricto <= 320 líneas ({$totalLines} líneas)", $totalLines <= 320);
assertTest("Presupuesto de seguridad cátedra <= 400 líneas", $totalLines <= 400);

echo "\nResultados: {$passed} pasadas, {$failed} fallidas. Total líneas: {$totalLines} / 320 (límite cátedra 400)\n";
exit($failed === 0 ? 0 : 1);
