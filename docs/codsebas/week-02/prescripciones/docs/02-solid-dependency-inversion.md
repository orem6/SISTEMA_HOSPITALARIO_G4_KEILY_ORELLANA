# ASII-15 — Diseño SOLID: Dependency Inversion Principle (DIP)

## 1. Marco Teórico y Diagnóstico
Conforme a la cátedra de Diseño de Software (`https://mvpcluster.com/diseno-de-software-2/`), el **Principio de Inversión de Dependencias (DIP)** establece que los módulos de alto nivel no deben depender de los de bajo nivel, sino ambos de abstracciones. Las reglas clínicas de prescripción médica no deben depender de PostgreSQL directo ni infraestructura SQL.

## 2. Antipatrón: Diseño "Antes" (Violación DIP)
En el diseño tradicional, `PrescriptionService` instancia directamente repositorios concretos mediante `new`, impidiendo pruebas unitarias aisladas y violando OCP/DIP:
```php
<?php
namespace App\Legacy;
class PostgreSqlAllergyRepository {
    public function findActive(string $t, string $p): array { return ['penicillin']; }
}
class PostgreSqlPrescriptionRepository {
    public function save(string $t, array $d): string { return 'rx-999'; }
}
class PrescriptionService {
    private PostgreSqlAllergyRepository $allergyRepo;
    private PostgreSqlPrescriptionRepository $rxRepo;
    public function __construct() {
        $this->allergyRepo = new PostgreSqlAllergyRepository(); // Violación DIP
        $this->rxRepo = new PostgreSqlPrescriptionRepository(); // Violación DIP
    }
    public function issue(string $t, string $p, string $med, ?string $override): string {
        if (in_array($med, $this->allergyRepo->findActive($t, $p)) && empty($override)) {
            throw new \RuntimeException("Contraindicación sin justificar");
        }
        return $this->rxRepo->save($t, ['patient' => $p, 'med' => $med]);
    }
}
```

## 3. Arquitectura Refactorizada: Diseño "Después" (Cumplimiento DIP)
Se desacopla la lógica inyectando abstracciones (`AllergyVerifierInterface` y `PrescriptionStorageInterface`) en el constructor:

```php
<?php
declare(strict_types=1);
namespace App\Clinical;

readonly class PrescriptionDraft {
    public function __construct(
        public string $tenantId, public string $patientId,
        public string $medicationId, public ?string $overrideReason = null
    ) {}
}

readonly class VerificationResult {
    public function __construct(
        public bool $isSafe, public bool $isUnassessed,
        public string $severity, public ?string $allergenName
    ) {}
}

interface AllergyVerifierInterface {
    public function verify(string $tenantId, string $patientId, string $medicationId): VerificationResult;
}

interface PrescriptionStorageInterface {
    public function persistPrescription(PrescriptionDraft $draft, string $status): string;
    public function auditCheck(string $tenantId, string $patientId, string $medicationId, string $verdict, ?string $reason): void;
}

class PrescriptionService {
    public function __construct(
        private readonly AllergyVerifierInterface $allergyVerifier,
        private readonly PrescriptionStorageInterface $prescriptionStorage
    ) {}

    public function createPrescription(PrescriptionDraft $draft): array {
        $verif = $this->allergyVerifier->verify($draft->tenantId, $draft->patientId, $draft->medicationId);
        if (!$verif->isSafe) {
            if ($draft->overrideReason === null || strlen(trim($draft->overrideReason)) < 15) {
                $this->prescriptionStorage->auditCheck($draft->tenantId, $draft->patientId, $draft->medicationId, 'BLOCKED', $draft->overrideReason);
                return ['success' => false, 'status' => 'BLOCKED', 'message' => "Alerta: alérgeno {$verif->allergenName}"];
            }
            $this->prescriptionStorage->auditCheck($draft->tenantId, $draft->patientId, $draft->medicationId, 'OVERRIDDEN', $draft->overrideReason);
        }
        $id = $this->prescriptionStorage->persistPrescription($draft, 'ACTIVE');
        return ['success' => true, 'id' => $id, 'status' => 'ACTIVE'];
    }
}

class InMemoryAllergyVerifierMock implements AllergyVerifierInterface {
    public function __construct(private array $allergies = []) {}
    public function verify(string $tenantId, string $patientId, string $medicationId): VerificationResult {
        if (!isset($this->allergies[$patientId])) return new VerificationResult(true, true, 'unassessed', null);
        if (isset($this->allergies[$patientId][$medicationId])) return new VerificationResult(false, false, 'severe', $medicationId);
        return new VerificationResult(true, false, 'none', null);
    }
}
```

## 4. Beneficios Técnicos y Clínicos
1. **Pruebas Unitarias Deterministas**: Evaluación completa de alergias en memoria sin base de datos ni Docker.
2. **Alta Cohesión**: La lógica médica se concentra en el protocolo asistencial, independiente de la capa SQL.
