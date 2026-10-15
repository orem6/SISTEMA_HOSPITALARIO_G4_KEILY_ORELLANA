# ASII-24 — Informe Técnico de Arquitectura y Patrones de Diseño

## 1. Alcance y Objetivos de la Arquitectura
El módulo **ASII-24** (Semana 3 / Asignación Odoo #3930 / Issue #20) formaliza la arquitectura del «Centro de Documentación y Manuales por Rol» en el Hospital Information System (HIS). Se definen vistas arquitectónicas estandarizadas y patrones estructurales para desacoplar el acceso a documentación y blindar la seguridad multi-tenant.

## 2. Modelo de Vistas Arquitectónicas
- **Vista Lógica**: Organizada en 4 capas estrictas (Presentación REST, Aplicación, Dominio e Infraestructura). La capa de aplicación encapsula la lógica de orquestación, mientras que el dominio permanece agnóstico a la persistencia.
- **Vista de Componentes y Despliegue**: Modela contenedores PHP 8.2-FPM bajo Kubernetes/Docker, con proxy inverso Nginx Ingress, base de datos PostgreSQL multi-tenant y almacenamiento S3 para documentos binarios.
- **Aislamiento Multi-Tenant y RBAC**: Cada consulta valida la cabecera `X-Tenant-ID` y el contexto de rol Spatie (`Admin`, `Médico`, `Enfermera`, `TecnicoLab`), garantizando que un tenant jamás acceda a documentos de otro.

## 3. Patrones de Diseño de Software Implementados
- **Facade Pattern (`DocumentationCenterFacade`)**: Ofrece una interfaz de alto nivel simplificada para los clientes frontend (SPA), abstrayendo la complejidad de validaciones, proxy y repositorios.
- **Proxy Pattern (`SecureDocumentAccessProxy`)**: Intercepta de forma transparente las operaciones sobre `DocumentServiceInterface` para aplicar validaciones previas de tenant y permisos de rol sin contaminar el servicio base.
- **Repository Pattern (`DocumentMetadataRepositoryInterface`)**: Desacopla la consulta y almacenamiento de metadatos del motor de persistencia relacional.

## 4. Fundamentación Teórica de la Cátedra
Basado en *MVP Cluster: Principios básicos del diseño de software 2* (Luis Miguel Rodríguez González, `https://mvpcluster.com/diseno-de-software-2/`), los patrones Facade y Proxy proporcionan una solución canónica al desacoplamiento estructural: Facade minimiza dependencias del cliente hacia subsistemas complejos, mientras que Proxy introduce control transversal de acceso y seguridad respetando el Principio de Responsabilidad Única (SRP) y de Abierto/Cerrado (OCP).
