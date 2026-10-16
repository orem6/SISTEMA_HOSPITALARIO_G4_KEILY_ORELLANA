# Guion de Defensa Oral — Arquitectura y Patrones de Diseño (ASII-24)

## Preguntas Críticas y Argumentación Técnica

### 1. ¿Cuál es el propósito del modelo de vistas arquitectónicas en este módulo?
**Respuesta**: Permite separar las preocupaciones de diseño en tres perspectivas complementarias: la Vista Lógica estructura las responsabilidades de dominio y aplicación sin acoplamiento a infraestructura; la Vista de Componentes define las fronteras e interfaces de comunicación (`Facade`, `Proxy`, `Repository`); y la Vista de Despliegue define la topología de contenedores PHP 8.2, persistencia segregada en PostgreSQL y almacenamiento S3.

### 2. ¿Qué problema resuelve el patrón Facade (`DocumentationCenterFacade`) para los clientes frontend?
**Respuesta**: El frontend requiere un punto de entrada intuitivo y unificado para consultar y leer manuales sin conocer la complejidad interna del subsistema (repositorios, verificaciones de rol o adaptadores S3). La fachada ofrece métodos cohesivos de alto nivel (`getAuthorizedManuals`, `openManual`) reduciendo el acoplamiento y facilitando la evolución interna sin alterar el contrato público.

### 3. ¿Por qué se eligió el patrón Proxy (`SecureDocumentAccessProxy`) para la seguridad multi-tenant y RBAC?
**Respuesta**: Conforme a la teoría de MVP Cluster, el Proxy actúa como intermediario transparente sobre la misma interfaz del servicio (`DocumentServiceInterface`), interceptando llamadas para validar la presencia de `X-Tenant-ID` y los roles Spatie antes de delegar en el servicio real. Esto evita duplicar validaciones en controladores y preserva el Principio de Responsabilidad Única (SRP) en `RealDocumentService`.

### 4. ¿Cómo interactúa el Repository Pattern con el Proxy y la Facade?
**Respuesta**: La fachada delega la solicitud al proxy de seguridad. El proxy valida el inquilino y los roles permitidos; si la validación es exitosa, delega en el servicio real, el cual invoca a `DocumentMetadataRepositoryInterface` para recuperar la entidad documental. Esto garantiza que la capa de persistencia nunca sea alcanzada si la petición carece de autorización.

### 5. ¿De qué manera la cabecera X-Tenant-ID garantiza el aislamiento en las vistas de componentes y despliegue?
**Respuesta**: La cabecera `X-Tenant-ID` es validada en el middleware HTTP, inyectada en el contexto de ejecución y requerida por el Proxy en cada consulta. A nivel de despliegue y persistencia, asegura que las consultas a PostgreSQL y las rutas de almacenamiento de S3 (`/tenants/{tenant_id}/docs/`) estén estrictamente acotadas al inquilino, previniendo fuga de información clínica transversal.

### 6. ¿Cómo se fundamenta esta arquitectura en los principios de diseño de software de la cátedra (MVP Cluster)?
**Respuesta**: La cátedra (Luis Miguel Rodríguez González, *MVP Cluster*) enfatiza la modularidad, el desacoplamiento y la reusabilidad mediante patrones GoF bien delimitados. Combinar Facade (reducción de acoplamiento externo) con Proxy (control de acceso ortogonal e intermediación) cumple estrictamente los principios SOLID, manteniendo clases pequeñas, cohesivas y extensibles.
