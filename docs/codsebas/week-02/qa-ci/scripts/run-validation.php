<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$passed = 0; $failed = 0;

function assertTest(string $desc, bool $cond): void {
    global $passed, $failed;
    $cond ? $passed++ : $failed++;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $desc . "\n";
}

echo "=== ASII-25 — Validación Semana 2 (codsebas) ===\n";
$required = [
    'README.md', 'INFORME.md', 'DEFENSA_ORAL.md', 'DECLARACION_IA.md',
    'docs/01-requirements-and-acceptance-criteria.md',
    'docs/02-solid-dependency-inversion.md', 'scripts/run-validation.php'
];

$formatOk = true; $totalLines = 0;
foreach ($required as $f) {
    $p = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    assertTest("Archivo presente y no vacío: {$f}", file_exists($p) && filesize($p) > 0);
    $c = file_get_contents($p);
    if (str_starts_with($c, "\xEF\xBB\xBF") || str_contains($c, "\r")) $formatOk = false;
    foreach (explode("\n", $c) as $l) {
        if (preg_match('/[ \t]+$/', $l)) $formatOk = false;
    }
    $totalLines += count(file($p));
}
assertTest("Todos los archivos cumplen codificación UTF-8 sin BOM", $formatOk);
assertTest("Todos los archivos usan saltos de línea LF (Unix)", $formatOk);
assertTest("Cero espacios en blanco residuales al final de línea", $formatOk);

$reqPath = $baseDir . '/docs/01-requirements-and-acceptance-criteria.md';
$reqContent = file_exists($reqPath) ? file_get_contents($reqPath) : '';

for ($i = 1; $i <= 10; $i++) {
    $rfId = sprintf('RF-%02d', $i);
    assertTest("Requerimiento Funcional {$rfId} definido formalmente", str_contains($reqContent, $rfId));
}
for ($i = 1; $i <= 6; $i++) {
    $rnfId = sprintf('RNF-%02d', $i);
    assertTest("Requerimiento No Funcional {$rnfId} definido formalmente", str_contains($reqContent, $rnfId));
}
assertTest("Criterios de aceptación estructurados en formato BDD Gherkin (Dado-Cuando-Entonces)", str_contains($reqContent, 'Dado') && str_contains($reqContent, 'Cuando') && str_contains($reqContent, 'Entonces') && str_contains($reqContent, 'Escenario:'));

$solidPath = $baseDir . '/docs/02-solid-dependency-inversion.md';
$solidContent = file_exists($solidPath) ? file_get_contents($solidPath) : '';
assertTest("Cita y enlace formal a fuente teórica obligatoria (MVP Cluster)", str_contains($solidContent, 'https://mvpcluster.com/diseno-de-software-2/'));
assertTest("Referencia al autor de la cátedra (Luis Miguel Rodríguez González)", str_contains($solidContent, 'Luis Miguel Rodríguez González'));
assertTest("Definición de interfaz abstracta QualityGateRuleInterface", str_contains($solidContent, 'interface QualityGateRuleInterface'));
assertTest("Definición de interfaz abstracta EvidenceCollectorInterface", str_contains($solidContent, 'interface EvidenceCollectorInterface'));
assertTest("Implementación polimórfica LintQualityRule", str_contains($solidContent, 'class LintQualityRule'));
assertTest("Implementación polimórfica TestCoverageQualityRule", str_contains($solidContent, 'class TestCoverageQualityRule'));
assertTest("Implementación polimórfica E2EQualityRule", str_contains($solidContent, 'class E2EQualityRule'));
assertTest("Inyección de dependencias en constructor de QualityGateEngine", str_contains($solidContent, 'public function __construct'));

$syntax = @shell_exec('php -l "' . __FILE__ . '" 2>&1');
assertTest("Sintaxis de script de validación impecable (PHP 8.2+)", $syntax !== null && str_contains($syntax, 'No syntax errors detected'));
assertTest("Total de líneas agregadas en módulo ({$totalLines}) <= 400", $totalLines <= 400);

echo "\nResultados: {$passed} pasadas, {$failed} fallidas. Total líneas: {$totalLines} / 400\n";
exit($failed === 0 ? 0 : 1);
