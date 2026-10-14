<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$passed = 0; $failed = 0;

function assertTest(string $desc, bool $cond): void {
    global $passed, $failed;
    $cond ? $passed++ : $failed++;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $desc . "\n";
}

echo "=== ASII-15 — Validación Semana 2 (codsebas) ===\n";
$required = [
    'README.md', 'INFORME.md', 'DEFENSA_ORAL.md', 'DECLARACION_IA.md',
    'docs/01-requirements-and-acceptance-criteria.md',
    'docs/02-solid-dependency-inversion.md', 'scripts/run-validation.php'
];

$formatOk = true; $totalLines = 0;
foreach ($required as $f) {
    $p = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    assertTest("Archivo presente: {$f}", file_exists($p) && filesize($p) > 0);
    $c = file_get_contents($p);
    if (str_starts_with($c, "\xEF\xBB\xBF") || str_contains($c, "\r")) $formatOk = false;
    foreach (explode("\n", $c) as $l) {
        if (preg_match('/[ \t]+$/', $l)) $formatOk = false;
    }
    $totalLines += count(file($p));
}
assertTest("Formato UTF-8 no-BOM, LF y sin trailing spaces", $formatOk);

$reqDoc = file_get_contents($baseDir . '/docs/01-requirements-and-acceptance-criteria.md');
$readmeDoc = file_get_contents($baseDir . '/README.md');

for ($i = 1; $i <= 10; $i++) {
    $id = sprintf('RF-%02d', $i);
    assertTest("Requerimiento {$id} presente", str_contains($reqDoc, $id) && str_contains($readmeDoc, $id));
}
for ($j = 1; $j <= 6; $j++) {
    $id = sprintf('RNF-%02d', $j);
    assertTest("Requerimiento {$id} presente", str_contains($reqDoc, $id) && str_contains($readmeDoc, $id));
}
assertTest("Criterios de aceptación formalizados en Gherkin", str_contains($reqDoc, 'Característica:') && str_contains($reqDoc, 'Dado que') && str_contains($reqDoc, 'Cuando') && str_contains($reqDoc, 'Entonces'));

$dipDoc = file_get_contents($baseDir . '/docs/02-solid-dependency-inversion.md');
assertTest("Referencia a fuente teórica (mvpcluster.com)", str_contains($dipDoc, 'mvpcluster.com/diseno-de-software-2/'));
assertTest("Contrato AllergyVerifierInterface", str_contains($dipDoc, 'interface AllergyVerifierInterface'));
assertTest("Contrato PrescriptionStorageInterface", str_contains($dipDoc, 'interface PrescriptionStorageInterface'));
assertTest("Servicio desacoplado PrescriptionService", str_contains($dipDoc, 'class PrescriptionService'));
assertTest("Adaptador mock InMemoryAllergyVerifierMock", str_contains($dipDoc, 'class InMemoryAllergyVerifierMock'));
assertTest("Contraste Diseño Antes vs Después", str_contains($dipDoc, 'Diseño "Antes"') && str_contains($dipDoc, 'Diseño "Después"'));

assertTest("Presupuesto estricto <= 400 líneas ({$totalLines} líneas)", $totalLines <= 400);

echo "\nResultados: {$passed} pasadas, {$failed} fallidas. Total líneas: {$totalLines} / 400\n";
exit($failed === 0 ? 0 : 1);
