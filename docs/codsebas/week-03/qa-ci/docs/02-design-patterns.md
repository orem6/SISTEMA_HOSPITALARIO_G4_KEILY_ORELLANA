# Patrones de Diseño de Software en Quality Gates (ASII-25)

## 1. Fundamentación Teórica de la Cátedra
Según Luis Miguel Rodríguez González (*MVP Cluster*, `https://mvpcluster.com/diseno-de-software-2/`), los patrones de diseño proporcionan soluciones probadas que desacoplan responsabilidades y facilitan la extensibilidad sin modificar código existente (OCP y DIP).

## 2. Chain of Responsibility: Orquestación Fail-Fast
Permite evaluar secuencialmente cada compuerta con corte inmediato (*fail-fast*) ante fallos:

```php
<?php
declare(strict_types=1);
namespace App\Quality\Chain;

abstract class QualityGateChainHandler {
    private ?QualityGateChainHandler $next = null;
    public function setNext(QualityGateChainHandler $next): QualityGateChainHandler {
        $this->next = $next;
        return $next;
    }
    public function handle(string $sha): bool {
        if (!$this->evaluate($sha)) return false; // Corte fail-fast
        return $this->next?->handle($sha) ?? true;
    }
    abstract protected function evaluate(string $sha): bool;
    abstract public function getName(): string;
}

class PintQualityHandler extends QualityGateChainHandler {
    protected function evaluate(string $sha): bool { return true; }
    public function getName(): string { return 'Laravel Pint'; }
}
class DatabaseMigrationHandler extends QualityGateChainHandler {
    protected function evaluate(string $sha): bool { return true; }
    public function getName(): string { return 'Database Migrations'; }
}
class PhpUnitQualityHandler extends QualityGateChainHandler {
    protected function evaluate(string $sha): bool { return true; }
    public function getName(): string { return 'PHPUnit Suite'; }
}
class PlaywrightQualityHandler extends QualityGateChainHandler {
    protected function evaluate(string $sha): bool { return true; }
    public function getName(): string { return 'Playwright E2E'; }
}
```

## 3. Builder Pattern: Aprovisionamiento de Arnés de Pruebas
Encapsula la construcción determinista del entorno PostgreSQL 16+ efímero:

```php
namespace App\Quality\Builder;

final class TestHarness {
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $database,
        public readonly array $schemas,
        public readonly bool $seedData
    ) {}
}

class TestHarnessBuilder {
    private string $host = '127.0.0.1';
    private int $port = 5432;
    private string $database = 'his_test';
    private array $schemas = ['central', 'hospital'];
    private bool $seedData = false;

    public function withDatabase(string $db): self { $this->database = $db; return $this; }
    public function withSchemas(array $schemas): self { $this->schemas = $schemas; return $this; }
    public function withSeedData(bool $seed): self { $this->seedData = $seed; return $this; }
    public function build(): TestHarness {
        return new TestHarness($this->host, $this->port, $this->database, $this->schemas, $this->seedData);
    }
}
```

## 4. Observer Pattern: Métricas y Sellado Criptográfico SHA-256
Desacopla la auditoría inmutable y telemetría del ciclo de ejecución:

```php
namespace App\Quality\Observer;

interface QualityObserverInterface {
    public function onGateCompleted(string $gate, bool $success, string $sha): void;
}

class EvidenceSealObserver implements QualityObserverInterface {
    public function onGateCompleted(string $gate, bool $success, string $sha): void {
        $hash = hash('sha256', "{$gate}:{$sha}:" . ($success ? 'PASS' : 'FAIL'));
        // Registrar sello SHA-256 inmutable de la evidencia
    }
}

class QualityMetricsObserver implements QualityObserverInterface {
    public function onGateCompleted(string $gate, bool $success, string $sha): void {
        // Despachar métricas de latencia y cobertura hacia observabilidad
    }
}
```
