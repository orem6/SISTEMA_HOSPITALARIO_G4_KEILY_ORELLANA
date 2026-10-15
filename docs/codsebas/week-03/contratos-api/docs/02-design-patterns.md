# ASII-24 — Patrones de Diseño Estructurales: Facade y Proxy

## 1. Fundamentación Teórica de la Cátedra
Según *MVP Cluster: Principios básicos del diseño de software 2* (Luis Miguel Rodríguez González, `https://mvpcluster.com/diseno-de-software-2/`), los patrones estructurales permiten ensamblar objetos y clases en estructuras mayores manteniendo la flexibilidad y eficiencia:
- **Facade**: Proporciona una interfaz unificada y de alto nivel a un conjunto de interfaces de un subsistema, desacoplando clientes de detalles internos.
- **Proxy**: Proporciona un sustituto o marcador de posición para controlar el acceso al objeto original, habilitando autorización, validación o almacenamiento en caché sin modificar el servicio real.

## 2. Implementación de Referencia en PHP 8.2+ Vanilla

```php
<?php
declare(strict_types=1);

namespace Hospital\Documentation;

readonly class Document {
    public function __construct(
        public string $id,
        public string $title,
        public string $tenantId,
        public array $allowedRoles,
        public string $contentUri
    ) {}
}

interface DocumentMetadataRepositoryInterface {
    public function findById(string $id, string $tenantId): ?Document;
    /** @return Document[] */
    public function findByRole(string $role, string $tenantId): array;
}

interface DocumentServiceInterface {
    public function viewDocument(string $docId, string $tenantId, string $userRole): Document;
    /** @return Document[] */
    public function listDocuments(string $tenantId, string $userRole): array;
}

class RealDocumentService implements DocumentServiceInterface {
    public function __construct(private DocumentMetadataRepositoryInterface $repo) {}

    public function viewDocument(string $docId, string $tenantId, string $userRole): Document {
        $doc = $this->repo->findById($docId, $tenantId);
        if (!$doc) throw new \RuntimeException("Documento no encontrado");
        return $doc;
    }

    public function listDocuments(string $tenantId, string $userRole): array {
        return $this->repo->findByRole($userRole, $tenantId);
    }
}

class SecureDocumentAccessProxy implements DocumentServiceInterface {
    public function __construct(private DocumentServiceInterface $realService) {}

    public function viewDocument(string $docId, string $tenantId, string $userRole): Document {
        if (trim($tenantId) === '') throw new \InvalidArgumentException("X-Tenant-ID requerido");
        $doc = $this->realService->viewDocument($docId, $tenantId, $userRole);
        if ($doc->tenantId !== $tenantId || !in_array($userRole, $doc->allowedRoles, true)) {
            throw new \DomainException("Acceso denegado: aislamiento tenant o rol insuficiente");
        }
        return $doc;
    }

    public function listDocuments(string $tenantId, string $userRole): array {
        if (trim($tenantId) === '') throw new \InvalidArgumentException("X-Tenant-ID requerido");
        return $this->realService->listDocuments($tenantId, $userRole);
    }
}

class DocumentationCenterFacade {
    public function __construct(private DocumentServiceInterface $securityProxy) {}

    public function getAuthorizedManuals(string $tenantId, string $userRole): array {
        return $this->securityProxy->listDocuments($tenantId, $userRole);
    }

    public function openManual(string $docId, string $tenantId, string $userRole): Document {
        return $this->securityProxy->viewDocument($docId, $tenantId, $userRole);
    }
}
```
