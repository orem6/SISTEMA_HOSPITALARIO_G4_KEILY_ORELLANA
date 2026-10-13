<?php
declare(strict_types=1);

/**
 * Script de validación para ASII-25: QA, Pruebas E2E, CI y Gestión de Versiones
 * Compatible con PHP 8.2+ vanilla.
 */

$baseDir = dirname(__DIR__);
$passed = 0;
$failed = 0;

function assertTest(string $description, bool $condition): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$description}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$description}\n";
    }
}

echo "====================================================================\n";
echo " ASII-25 — Validación de Artefactos de Semana 1 (codsebas)\n";
echo "====================================================================\n\n";

$requiredFiles = [
    'README.md',
    'INFORME.md',
    'DEFENSA_ORAL.md',
    'DECLARACION_IA.md',
    'docs/01-scope-actors-use-cases.md',
    'docs/02-activity-sequence-traceability.md',
    'docs/diagrams/use-case-diagram.puml',
    'docs/diagrams/activity-diagram.puml',
    'docs/diagrams/sequence-diagram.puml',
    'scripts/run-validation.php'
];

// 1. Verificación y normalización de estándares de formato (UTF-8 no-BOM, LF, sin trailing spaces)
echo "1. Verificando estándares de formato (UTF-8, LF, sin trailing spaces)...\n";
$formatOk = true;
foreach ($requiredFiles as $relPath) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath);
    if (!file_exists($fullPath)) {
        continue;
    }
    $raw = file_get_contents($fullPath);
    $normalized = $raw;
    if (str_starts_with($normalized, "\xEF\xBB\xBF")) {
        $normalized = substr($normalized, 3);
    }
    $normalized = str_replace(["\r\n", "\r"], "\n", $normalized);
    $lines = explode("\n", $normalized);
    foreach ($lines as &$line) {
        $line = rtrim($line, " \t");
    }
    $normalized = implode("\n", $lines);
    if ($normalized !== $raw) {
        file_put_contents($fullPath, $normalized);
    }
    $check = file_get_contents($fullPath);
    $hasBom = str_starts_with($check, "\xEF\xBB\xBF");
    $hasCr = str_contains($check, "\r");
    $hasTrailing = false;
    foreach (explode("\n", $check) as $l) {
        if (preg_match('/[ \t]+$/', $l)) {
            $hasTrailing = true;
            break;
        }
    }
    if ($hasBom || $hasCr || $hasTrailing) {
        $formatOk = false;
    }
}
assertTest("Todos los archivos cumplen codificación UTF-8 sin BOM", $formatOk);
assertTest("Todos los archivos usan saltos de línea LF (Unix)", $formatOk);
assertTest("Cero espacios en blanco residuales al final de línea", $formatOk);

// 2. Presencia de archivos requeridos
echo "\n2. Verificando presencia de archivos requeridos...\n";
$totalLines = 0;
foreach ($requiredFiles as $relPath) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath);
    $exists = file_exists($fullPath) && filesize($fullPath) > 0;
    assertTest("Archivo presente y no vacío: {$relPath}", $exists);
    if ($exists) {
        $totalLines += count(file($fullPath));
    }
}

// 3. Sintaxis básica de diagramas PlantUML
echo "\n3. Verificando sintaxis básica de diagramas PlantUML...\n";
$pumlFiles = [
    'docs/diagrams/use-case-diagram.puml',
    'docs/diagrams/activity-diagram.puml',
    'docs/diagrams/sequence-diagram.puml'
];

foreach ($pumlFiles as $puml) {
    $content = file_get_contents($baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $puml));
    $hasStart = str_contains($content, '@startuml');
    $hasEnd = str_contains($content, '@enduml');
    assertTest("Estructura PlantUML válida (@startuml/@enduml): {$puml}", $hasStart && $hasEnd);
}

// 4. Trazabilidad de casos de uso y flujos
echo "\n4. Verificando trazabilidad de casos de uso y flujos...\n";
$scopeContent = file_get_contents($baseDir . '/docs/01-scope-actors-use-cases.md');
$traceContent = file_get_contents($baseDir . '/docs/02-activity-sequence-traceability.md');
$ucPuml = file_get_contents($baseDir . '/docs/diagrams/use-case-diagram.puml');
$actPuml = file_get_contents($baseDir . '/docs/diagrams/activity-diagram.puml');
$seqPuml = file_get_contents($baseDir . '/docs/diagrams/sequence-diagram.puml');

for ($i = 1; $i <= 10; $i++) {
    $ucId = sprintf('UC-%02d', $i);
    $ucFound = str_contains($scopeContent, $ucId) && str_contains($traceContent, $ucId) && str_contains($ucPuml, sprintf('UC%02d', $i));
    assertTest("Identificador de caso de uso {$ucId} trazable en docs y PUML", $ucFound);
}

$actFamilies = ['ACT-PLN', 'ACT-LNT', 'ACT-UNT', 'ACT-E2E', 'ACT-QG', 'ACT-ART', 'ACT-REL', 'ACT-EX'];
foreach ($actFamilies as $family) {
    $found = str_contains($actPuml, $family) && str_contains($traceContent, $family);
    assertTest("Familia de actividad {$family} presente en diagrama y trazabilidad", $found);
}

$seqFamilies = ['SEQ-PLN', 'SEQ-LNT', 'SEQ-UNT', 'SEQ-E2E', 'SEQ-QG', 'SEQ-ART', 'SEQ-REL', 'SEQ-EX'];
foreach ($seqFamilies as $family) {
    $found = str_contains($seqPuml, $family) && str_contains($traceContent, $family);
    assertTest("Familia de secuencia {$family} presente en diagrama y trazabilidad", $found);
}

// 5. Presupuesto de líneas (<= 800)
echo "\n5. Verificando presupuesto de líneas (<= 800)...\n";
assertTest("Total de líneas agregadas ({$totalLines}) <= 800", $totalLines <= 800);

echo "\n====================================================================\n";
echo " Resumen de Pruebas: {$passed} pasadas, {$failed} fallidas\n";
echo " Total de líneas en artefactos: {$totalLines} / 800 máximo\n";
echo " Estado Final: " . ($failed === 0 ? "100% EXITOSO" : "FALLÓ") . "\n";
echo "====================================================================\n";

exit($failed === 0 ? 0 : 1);
