<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$passed = 0; $failed = 0;

function assertTest(string $desc, bool $cond): void {
    global $passed, $failed;
    $cond ? $passed++ : $failed++;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $desc . "\n";
}

echo "=== ASII-24 — Validación Semana 2 (codsebas) ===\n";
$required = [
    'README.md', 'INFORME.md', 'DEFENSA_ORAL.md', 'DECLARACION_IA.md',
    'docs/01-requirements-and-acceptance-criteria.md',
    'docs/02-solid-interface-segregation.md', 'scripts/run-validation.php'
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

$reqDoc = (string)file_get_contents($baseDir . '/docs/01-requirements-and-acceptance-criteria.md');
for ($i = 1; $i <= 10; $i++) {
    $id = sprintf('RF-%02d', $i);
    assertTest("Requerimiento {$id} presente", str_contains($reqDoc, $id));
}
for ($j = 1; $j <= 8; $j++) {
    $id = sprintf('RNF-%02d', $j);
    assertTest("Requerimiento {$id} presente", str_contains($reqDoc, $id));
}
assertTest("Sintaxis BDD presente: Dado / Cuando / Entonces", str_contains($reqDoc, '**Dado**') && str_contains($reqDoc, '**Cuando**') && str_contains($reqDoc, '**Entonces**'));
assertTest("Criterios de aceptación CA-RF formalizados", str_contains($reqDoc, 'CA-RF-01') && str_contains($reqDoc, 'CA-RF-10'));

$solidDoc = (string)file_get_contents($baseDir . '/docs/02-solid-interface-segregation.md');
assertTest("Diseño 'Antes' documentado con violación ISP", str_contains($solidDoc, 'DocumentationManagerInterface'));
assertTest("Diseño 'Después' con interfaces segregadas", str_contains($solidDoc, 'DocumentReaderInterface') && str_contains($solidDoc, 'DocumentAdminInterface'));
assertTest("Cliente clínico desacoplado demostrado", str_contains($solidDoc, 'ClinicalDocumentViewerService'));
assertTest("Fundamentación teórica referenciando la cátedra", str_contains($solidDoc, 'mvpcluster.com'));

$syntaxOk = true;
if (preg_match_all('/```php\s*(<\?php.*?)\s*```/s', $solidDoc, $matches)) {
    foreach ($matches[1] as $code) {
        $tmp = tempnam(sys_get_temp_dir(), 'isp_') . '.php';
        file_put_contents($tmp, $code);
        exec("php -l \"{$tmp}\" 2>&1", $out, $codeRet);
        unlink($tmp);
        if ($codeRet !== 0) $syntaxOk = false;
    }
}
assertTest("Sintaxis PHP 8.2+ válida en snippets y contratos", $syntaxOk);
assertTest("Presupuesto estricto <= 400 líneas ({$totalLines} líneas)", $totalLines <= 400);

echo "\nResultados: {$passed} pasadas, {$failed} fallidas. Total líneas: {$totalLines} / 400\n";
exit($failed === 0 ? 0 : 1);
