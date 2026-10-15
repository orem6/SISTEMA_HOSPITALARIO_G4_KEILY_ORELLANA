# ASII-15 — Patrones de Diseño: Strategy, Observer y Repository (Semana 3)

## 1. Fundamentación Teórica
Conforme a la cátedra de Diseño de Software (`https://mvpcluster.com/diseno-de-software-2/`), los patrones de diseño resuelven problemas de acoplamiento y rigidez. Se modelan en PHP 8.2+ vanilla:
- **Strategy**: Desacopla las reglas de validación alérgica en estrategias independientes intercambiables.
- **Observer**: Notifica eventos clínicos de dominio desacoplando efectos secundarios (auditoría y alertas).
- **Repository**: Abstrae el acceso a datos separando la lógica de negocio de la infraestructura SQL.

## 2. Implementación de Patrones en PHP 8.2+ Vanilla
```php
<?php
declare(strict_types=1);
namespace App\ClinicalArchitecture;

readonly class AllergyRisk {
    public function __construct(public bool $hasConflict, public string $severity, public string $reason) {}
}

interface AllergyEvaluationStrategyInterface {
    public function evaluate(string $tenantId, string $patientId, string $medId): ?AllergyRisk;
}

class DirectAllergyRuleStrategy implements AllergyEvaluationStrategyInterface {
    public function __construct(private readonly AllergyRepositoryInterface $allergyRepo) {}
    public function evaluate(string $tenantId, string $patientId, string $medId): ?AllergyRisk {
        $allergies = $this->allergyRepo->findActiveAllergies($tenantId, $patientId);
        return in_array($medId, $allergies, true)
            ? new AllergyRisk(true, 'CRITICAL', "Alergia directa registrada al medicamento {$medId}") : null;
    }
}

class CrossReactivityStrategy implements AllergyEvaluationStrategyInterface {
    private const CROSS_REACTIVITY_MAP = ['penicillin' => ['amoxicillin', 'ampicillin', 'cephalexin']];
    public function __construct(private readonly AllergyRepositoryInterface $allergyRepo) {}
    public function evaluate(string $tenantId, string $patientId, string $medId): ?AllergyRisk {
        $allergies = $this->allergyRepo->findActiveAllergies($tenantId, $patientId);
        foreach ($allergies as $allergen) {
            if (isset(self::CROSS_REACTIVITY_MAP[$allergen]) && in_array($medId, self::CROSS_REACTIVITY_MAP[$allergen], true)) {
                return new AllergyRisk(true, 'HIGH', "Reactividad cruzada detectada: {$medId} con alérgeno {$allergen}");
            }
        }
        return null;
    }
}

interface DomainEventInterface { public function name(): string; }

readonly class PrescriptionIssuedEvent implements DomainEventInterface {
    public function __construct(public string $tenantId, public string $rxId, public string $patientId) {}
    public function name(): string { return 'PrescriptionIssuedEvent'; }
}

readonly class AllergyAlertTriggeredEvent implements DomainEventInterface {
    public function __construct(public string $tenantId, public string $patientId, public string $medId, public string $severity, public ?string $reason) {}
    public function name(): string { return 'AllergyAlertTriggeredEvent'; }
}

class PrescriptionEventDispatcher {
    private array $listeners = [];
    public function subscribe(string $eventClass, callable $listener): void { $this->listeners[$eventClass][] = $listener; }
    public function dispatch(DomainEventInterface $event): void {
        foreach ($this->listeners[$event::class] ?? [] as $listener) { $listener($event); }
    }
}

interface PrescriptionRepositoryInterface {
    public function save(string $tenantId, string $patientId, string $medId, string $status): string;
}

interface AllergyRepositoryInterface {
    public function findActiveAllergies(string $tenantId, string $patientId): array;
}

class PrescriptionService {
    /** @param AllergyEvaluationStrategyInterface[] $strategies */
    public function __construct(
        private readonly array $strategies,
        private readonly PrescriptionRepositoryInterface $rxRepo,
        private readonly PrescriptionEventDispatcher $dispatcher
    ) {}

    public function issue(string $tenantId, string $patientId, string $medId, ?string $override = null): array {
        foreach ($this->strategies as $strategy) {
            if ($risk = $strategy->evaluate($tenantId, $patientId, $medId)) {
                $this->dispatcher->dispatch(new AllergyAlertTriggeredEvent($tenantId, $patientId, $medId, $risk->severity, $override));
                if ($override === null || strlen(trim($override)) < 15) {
                    return ['success' => false, 'status' => 'BLOCKED', 'reason' => $risk->reason];
                }
                break;
            }
        }
        $rxId = $this->rxRepo->save($tenantId, $patientId, $medId, 'ACTIVE');
        $this->dispatcher->dispatch(new PrescriptionIssuedEvent($tenantId, $rxId, $patientId));
        return ['success' => true, 'rx_id' => $rxId, 'status' => 'ACTIVE'];
    }
}
```

## 3. Beneficios Clínicos y Arquitectónicos
1. **Extensibilidad**: Inclusión de nuevas estrategias (p. ej. interacciones fármaco-fármaco) sin modificar `PrescriptionService`.
2. **Auditoría y Desacoplamiento**: El patrón Observer asegura bitácoras médico-legales y alertas a farmacia sin acoplar canales.
