# ASII-24 — Arquitectura y Patrones en Centro de Documentación por Rol (Semana 3)

## Resumen Ejecutivo
Entrega técnica del diseño de arquitectura de software para el módulo **ASII-24: Contratos API y Centro de Documentación por Rol** (Semana 3 / Asignación Odoo #3930 / Issue GitHub #20).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-24-codsebas-week-3`
- **Fuente teórica**: Patrones de Diseño (`https://mvpcluster.com/diseno-de-software-2/`)

## Vistas Arquitectónicas del Sistema
- **Vista Lógica**: Arquitectura en capas desacopladas (Presentación REST, Aplicación, Dominio e Infraestructura).
- **Vista de Componentes**: Desacoplamiento mediante interfaces, Facade unificada y Proxy de seguridad.
- **Vista de Despliegue**: Servicios PHP 8.2-FPM contenerizados con PostgreSQL multi-tenant y almacenamiento S3.
- **Aislamiento Multi-Tenant y RBAC**: Validación estricta por cabecera `X-Tenant-ID` y roles Spatie (`Admin`, `Médico`, `Enfermera`, `TecnicoLab`).

## Patrones de Diseño Estructurales
- **Facade (`DocumentationCenterFacade`)**: Unifica y simplifica el consumo de manuales y documentos para el frontend.
- **Proxy (`SecureDocumentAccessProxy`)**: Intercepta consultas para validar contexto de tenant y permisos RBAC antes de consultar el repositorio.
- **Repository (`DocumentMetadataRepository`)**: Desacopla la persistencia de metadatos de las reglas de dominio.

## Matriz de Trazabilidad Cruzada
| Requerimiento (Sem 2) | Componente Arquitectónico | Patrón / Implementación |
|---|---|---|
| RF-01, RF-02 (Consultar) | Capa Aplicación / API | `DocumentationCenterFacade::getAuthorizedManuals` |
| RF-04, RF-05 (Visualizar) | Capa Seguridad / Dominio | `SecureDocumentAccessProxy::viewDocument` |
| RF-06, RF-08 (Metadatos) | Capa Persistencia | `DocumentMetadataRepositoryInterface` |

## Instrucciones de Validación
Ejecutar validador automatizado (límite estricto <= 400 líneas):
```bash
php scripts/run-validation.php
```
- [Informe](INFORME.md) | [Defensa Oral](DEFENSA_ORAL.md) | [Declaración IA](DECLARACION_IA.md)
- [01. Vistas Arquitectónicas](docs/01-architectural-views.md) | [02. Patrones Facade y Proxy](docs/02-design-patterns.md)
