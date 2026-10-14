# ASII-24 — Contratos API y Centro de Documentación por Rol (Semana 2)

## Resumen Ejecutivo
Entrega técnica de especificación formal y diseño arquitectónico para el módulo **ASII-24: Contratos API y Centro de Documentación por Rol** (Semana 2 / Asignación Odoo #3929 / Issue GitHub #14).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-24-codsebas-week-2`
- **Fuente teórica**: Principios SOLID (`https://mvpcluster.com/diseno-de-software-2/`)

## Catálogo de Requerimientos (Semana 2)
- **RF-01**: Validar contexto de acceso JWT y `X-Tenant-ID`.
- **RF-02**: Consultar documentación autorizada por rol.
- **RF-03**: Buscar y filtrar manuales por rol y categoría.
- **RF-04**: Visualizar documento seguro revalidando permisos.
- **RF-05**: Descargar documento para usuarios clínicos acreditados.
- **RF-06**: Publicar documento o manual por usuario `Admin`.
- **RF-07**: Asignar visibilidad por rol lector.
- **RF-08**: Validar metadatos documentales y formato.
- **RF-09**: Actualizar versión manteniendo histórico inmutable.
- **RF-10**: Archivar documento lógicamente sin borrado físico.
- **RNF-01 a RNF-08**: Seguridad JWT, multi-tenant, RBAC, RFC 7807, latencia <2s, atomicidad, desacoplamiento y auditoría.

## Matriz de Trazabilidad Cruzada
| Caso de Uso (Sem 1) | Requerimiento (Sem 2) | Componente ISP |
|---|---|---|
| UC-01 Consultar manuales | RF-02, RF-03 | `DocumentReaderInterface::getAvailableDocuments` |
| UC-03 Visualizar manual | RF-04, RF-05 | `DocumentReaderInterface::viewDocument` |
| UC-05 Publicar manual | RF-06, RF-07 | `DocumentAdminInterface::publishDocument` |
| UC-07 Actualizar versión | RF-09, RF-10 | `DocumentAdminInterface::archiveDocument` |

## Instrucciones de Validación
Ejecutar validador automatizado (límite estricto <= 400 líneas):
```bash
php scripts/run-validation.php
```
- [Informe](INFORME.md) | [Defensa Oral](DEFENSA_ORAL.md) | [Declaración IA](DECLARACION_IA.md)
- [01. Requerimientos](docs/01-requirements-and-acceptance-criteria.md) | [02. SOLID ISP](docs/02-solid-interface-segregation.md)
