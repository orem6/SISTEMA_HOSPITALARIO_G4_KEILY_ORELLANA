# ASII-24 — Modelo de Vistas Arquitectónicas

## 1. Vista Lógica (Logical View)
Estructuración en capas ortogonales con responsabilidades aisladas:
- **Capa Presentación / API**: Controladores REST (`DocumentationApiController`), Middleware de autenticación JWT y extracción de `X-Tenant-ID`.
- **Capa Aplicación**: Fachada `DocumentationCenterFacade` que orquesta casos de uso para clientes frontend.
- **Capa Seguridad / Dominio**: Proxy `SecureDocumentAccessProxy`, entidad `Document`, contextos de validación por rol Spatie (`Admin`, `Médico`, `Enfermera`, `TecnicoLab`).
- **Capa Infraestructura / Persistencia**: Repositorio `DocumentMetadataRepository` y adaptador de almacenamiento para archivos estáticos (PDF / Markdown).

```
+-------------------------------------------------------------------------+
| [Frontend SPA] --> [REST Controllers / Middleware X-Tenant-ID]         |
|                             |                                           |
|                             v                                           |
|       [DocumentationCenterFacade] (Simplifica consumo API)              |
|                             |                                           |
|                             v                                           |
|       [SecureDocumentAccessProxy] (Valida Tenant & RBAC Spatie)         |
|                             |                                           |
|                             v                                           |
|  [RealDocumentService] ---> [DocumentMetadataRepository] (PostgreSQL)   |
|                        ---> [DocumentStorageAdapter]     (Object S3)    |
+-------------------------------------------------------------------------+
```

## 2. Vista de Componentes (Component View)
Define los bloques funcionales y contratos de interconexión:
- **`DocumentationController`**: Punto de entrada HTTP que valida el formato de entrada (RFC 7807) y delega a la fachada.
- **`DocumentationCenterFacade`**: Provee interfaz de alto nivel desacoplando los controladores de la seguridad y el almacenamiento.
- **`SecureDocumentAccessProxy`**: Implementa `DocumentServiceInterface`, validando pertenencia de `tenant_id` y compatibilidad de roles antes de delegar.
- **`DocumentMetadataRepository`**: Implementa `DocumentMetadataRepositoryInterface`, ejecutando consultas parametrizadas sobre PostgreSQL.
- **`DocumentStorageAdapter`**: Gestiona la lectura física de manuales desde S3/MinIO con URLs firmadas temporales.

## 3. Vista de Despliegue (Deployment View)
Topología de ejecución contenerizada y tolerante a fallos:
- **Edge / Ingress**: Nginx Ingress Controller con terminación TLS 1.3, rate-limiting e inyección de cabeceras de seguridad.
- **Application Tier**: Pods Kubernetes ejecutando PHP 8.2-FPM y Laravel, escalables horizontalmente (HPA).
- **Data Tier**: Cluster PostgreSQL 15 con segregación multi-tenant vía `tenant_id` indexado y esquemas independientes.
- **Storage Tier**: Almacenamiento S3 compatible con rutas segregadas `/tenants/{tenant_id}/docs/{document_id}.pdf`.

## 4. Aislamiento Multi-Tenant y Control por Rol
- **Aislamiento Multi-Tenant**: Cada petición exige la cabecera `X-Tenant-ID`. El proxy rechaza inmediatamente peticiones sin cabecera o cruzadas entre inquilinos.
- **Control por Rol (Spatie RBAC)**: La visibilidad y descarga de manuales se autoriza mediante la matriz de roles (`Admin` gestiona y publica; `Médico`, `Enfermera` y `TecnicoLab` leen manuales clínicos y operativos asignados).
