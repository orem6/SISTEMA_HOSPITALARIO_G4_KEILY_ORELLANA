# ASII-24 — Principio SOLID: Segregación de Interfaces (ISP)

## 1. Marco Teórico
Conforme a la cátedra de Diseño de Software (`https://mvpcluster.com/diseno-de-software-2/`), el **Principio de Segregación de Interfaces (ISP)** establece que los clientes no deben verse forzados a depender de interfaces o métodos que no utilizan. Es preferible diseñar interfaces pequeñas y cohesivas en lugar de contratos monolíticos.

## 2. Antipatrón: Diseño "Antes" (Violación ISP)
Una interfaz "fat" obliga a consumidores de solo lectura a acoplarse a operaciones administrativas:
```php
<?php
namespace App\Modules\Documentation\Before;

interface DocumentationManagerInterface {
    public function getAvailableDocuments(string $tenantId, string $role): array;
    public function viewDocument(int $docId, string $tenantId, string $role): ?array;
    public function publishDocument(string $tenantId, array $data): int;
    public function archiveDocument(int $docId, string $tenantId): bool;
}

class ClinicalDocumentViewerService {
    public function __construct(private DocumentationManagerInterface $docManager) {}
    public function render(string $t, string $r): array {
        return $this->docManager->getAvailableDocuments($t, $r);
    }
}
```

## 3. Arquitectura Refactorizada: Diseño "Después" (Cumplimiento ISP)
Segregamos la interfaz en contratos independientes según el rol de consumo:

```php
<?php
declare(strict_types=1);
namespace App\Modules\Documentation\After;

interface DocumentReaderInterface {
    public function getAvailableDocuments(string $tenantId, string $role): array;
    public function viewDocument(int $docId, string $tenantId, string $role): ?array;
}

interface DocumentAdminInterface {
    public function publishDocument(string $tenantId, array $data): int;
    public function archiveDocument(int $docId, string $tenantId): bool;
}

class ClinicalDocumentViewerService {
    public function __construct(private readonly DocumentReaderInterface $reader) {}
    public function renderClinicalManuals(string $tenantId, string $role): array {
        return $this->reader->getAvailableDocuments($tenantId, $role);
    }
}

class HospitalDocumentationService implements DocumentReaderInterface, DocumentAdminInterface {
    public function getAvailableDocuments(string $tenantId, string $role): array {
        return [['id' => 1, 'title' => 'Guía Clínica', 'role' => $role]];
    }
    public function viewDocument(int $docId, string $tenantId, string $role): ?array {
        return ['id' => $docId, 'content' => 'Protocolo asistencial'];
    }
    public function publishDocument(string $tenantId, array $data): int { return 101; }
    public function archiveDocument(int $docId, string $tenantId): bool { return true; }
}
```

## 4. Beneficios Técnicos
1. **Seguridad y Privilegio Mínimo**: El módulo clínico jamás tiene acceso accidental a métodos destructivos de publicación o borrado.
2. **Bajo Acoplamiento**: Cambios en la firma administrativa no impactan a las estaciones médicas.
