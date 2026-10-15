# Principio SOLID: Inversión de Dependencias (DIP) en Quality Gates

## 1. Fundamentación Teórica
Según la cátedra de Diseño de Software (*MVP Cluster*, Luis Miguel Rodríguez González, `https://mvpcluster.com/diseno-de-software-2/`), el **Principio de Inversión de Dependencias (DIP)** establece que las clases de alto nivel no deben depender de las de bajo nivel, sino de abstracciones. El motor `QualityGateEngine` no debe depender de ejecutores concretos de herramientas de CI.

## 2. Antipatrón: Diseño "Antes" (Violación DIP)
En el diseño acoplado inicial, `QualityGateEngine` crea instancias directas de linters y runners:
```php
<?php
namespace App\Quality\Legacy;
class PintLinterRunner { public function run(): bool { return true; } }
class PhpUnitTestRunner { public function run(): int { return 0; } }

class QualityGateEngine {
    private PintLinterRunner $linter;
    private PhpUnitTestRunner $tests;
    public function __construct() {
        $this->linter = new PintLinterRunner(); // Violación DIP
        $this->tests = new PhpUnitTestRunner();  // Violación DIP
    }
    public function evaluate(): string {
        return ($this->linter->run() && $this->tests->run() === 0) ? 'APPROVED' : 'REJECTED';
    }
}
```

## 3. Arquitectura Refactorizada: Diseño "Después" (Cumplimiento DIP)
El motor de compuertas depende de `QualityGateRuleInterface` y `EvidenceCollectorInterface`:

```php
<?php
declare(strict_types=1);
namespace App\Quality\After;

interface QualityGateRuleInterface {
    public function evaluate(string $commitSha): bool;
    public function getRuleName(): string;
}

interface EvidenceCollectorInterface {
    public function collectAndArchive(string $commitSha, array $results): string;
}

class LintQualityRule implements QualityGateRuleInterface {
    public function evaluate(string $commitSha): bool { return true; }
    public function getRuleName(): string { return 'PSR-12 / Pint'; }
}

class TestCoverageQualityRule implements QualityGateRuleInterface {
    public function __construct(private float $threshold = 80.0) {}
    public function evaluate(string $commitSha): bool { return true; }
    public function getRuleName(): string { return 'PHPUnit Coverage'; }
}

class E2EQualityRule implements QualityGateRuleInterface {
    public function evaluate(string $commitSha): bool { return true; }
    public function getRuleName(): string { return 'Playwright E2E'; }
}

class QualityGateEngine {
    /** @param QualityGateRuleInterface[] $rules */
    public function __construct(
        private readonly array $rules,
        private readonly EvidenceCollectorInterface $evidenceCollector
    ) {}

    public function evaluatePipeline(string $commitSha): string {
        $results = [];
        foreach ($this->rules as $rule) {
            $passed = $rule->evaluate($commitSha);
            $results[$rule->getRuleName()] = $passed;
            if (!$passed) return 'REJECTED';
        }
        $this->evidenceCollector->collectAndArchive($commitSha, $results);
        return 'APPROVED';
    }
}
```

## 4. Beneficios Técnicos
1. **Extensibilidad Abierta (OCP + DIP)**: Nuevas reglas (ej. PHPStan o Mutation Testing) se inyectan sin modificar `QualityGateEngine`.
2. **Pruebas Unitarias Aisladas**: El motor puede probarse con mocks de reglas sin necesidad de ejecutar herramientas externas.
