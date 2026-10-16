<?php
declare(strict_types=1);

$baseDir = dirname(__DIR__);
$passed = 0; $failed = 0;

function assertTest(string $desc, bool $cond): void {
    global $passed, $failed;
    $cond ? $passed++ : $failed++;
    echo ($cond ? "  [PASS] " : "  [FAIL] ") . $desc . "\n";
}

echo "=== ASII-24 — Validación Semana 3 (codsebas) ===\n";
$required = [
    'README.md', 'INFORME.md', 'DEFENSA_ORAL.md', 'DECLARACION_IA.md',
    'docs/01-architectural-views.md',
    'docs/02-design-patterns.md', 'scripts/run-validation.php'
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
assertTest("Vista Lógica presente y documentada", str_contains($viewsDoc, 'Vista Lógica'));
assertTest("Vista de Componentes presente y documentada", str_contains($viewsDoc, 'Vista de Componentes'));
assertTest("Vista de Despliegue presente y documentada", str_contains($viewsDoc, 'Vista de Despliegue'));
assertTest("Aislamiento multi-tenant por X-Tenant-ID documentado", str_contains($viewsDoc, 'X-Tenant-ID'));
assertTest("Control de acceso por rol Spatie RBAC documentado", str_contains($viewsDoc, 'RBAC'));

$patternsDoc = (string)file_get_contents($baseDir . '/docs/02-design-patterns.md');
assertTest("Patrón Facade (DocumentationCenterFacade) implementado", str_contains($patternsDoc, 'DocumentationCenterFacade'));
assertTest("Patrón Proxy (SecureDocumentAccessProxy) implementado", str_contains($patternsDoc, 'SecureDocumentAccessProxy'));
assertTest("Patrón Repository (DocumentMetadataRepository) implementado", str_contains($patternsDoc, 'DocumentMetadataRepository'));
assertTest("Fundamentación teórica referenciando la cátedra", str_contains($patternsDoc, 'mvpcluster.com'));

$syntaxOk = true;
if (preg_match_all('/```php\s*(<\?php.*?)\s*```/s', $patternsDoc, $matches)) {
    foreach ($matches[1] as $code) {
        $tmp = tempnam(sys_get_temp_dir(), 'pat_') . '.php';
        file_put_contents($tmp, $code);
        exec("php -l \"{$tmp}\" 2>&1", $out, $codeRet);
        unlink($tmp);
        if ($codeRet !== 0) $syntaxOk = false;
    }
}
assertTest("Sintaxis PHP 8.2+ válida en snippets y contratos", $syntaxOk);
assertTest("Presupuesto estricto <= 350 líneas ({$totalLines} líneas)", $totalLines <= 350);

echo "\nResultados: {$passed} pasadas, {$failed} fallidas. Total líneas: {$totalLines} / 350\n";
exit($failed === 0 ? 0 : 1);
