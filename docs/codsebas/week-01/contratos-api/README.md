# ASII-24 — Centro de Documentación y Manuales por Rol

## Resumen Ejecutivo
Entrega de análisis y diseño arquitectónico (Semana 1) para el módulo de **Contratos API y Documentación Técnica**, adaptado funcionalmente como **Centro de documentación y manuales diferenciados por rol** para el Sistema Hospitalario Integrado (HIS).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-24-codsebas-week-1`

## Alcance del Módulo
- **Incluido**: Delimitación del centro documental, catálogo de casos de uso (UC-01 a UC-10), diagrama de casos de uso, diagrama de actividades con decisiones y excepciones (`ACT-*`), diagrama de secuencia con mensajería y validaciones (`SEQ-*`), y matriz de trazabilidad integral.
- **Límites y Fuera de Alcance**: Consumo estricto de las capacidades base del HIS (JWT, header `X-Tenant-ID` y roles Spatie). En la Semana 1 no se incluye implementación de endpoints, contratos OpenAPI/Postman, migraciones ni código ejecutable de backend/frontend.

## Actores del Sistema
- **General**: `Usuario autenticado del HIS` (base de consulta).
- **Administrador**: `Admin` (único autor y gestor de manuales).
- **Lectores**: `Médico`, `Enfermera`, `Recepcionista` y `Técnico de laboratorio (TecnicoLab)`.
- **Soporte Base**: `Control de acceso existente del HIS (JWT, tenant y RBAC)`.

## Matriz de Trazabilidad Resumida
| Caso de Uso | Actor | Actividad | Secuencia | Resultado Trazable |
|---|---|---|---|---|
| UC-01 Consultar docs | Usuario autenticado | `ACT-ACC-01..03`, `ACT-CON-01..03` | `SEQ-ACC-01..03`, `SEQ-CON-01..04` | Lista filtrada por tenant y rol |
| UC-02 Buscar y filtrar | Usuario autenticado | `ACT-CON-04` | `SEQ-CON-01`, `SEQ-CON-03..04` | Filtro por metadatos |
| UC-03 Visualizar manual | Usuario autenticado | `ACT-CON-05..08` | `SEQ-CON-05..08` | Visualización autorizada |
| UC-04 Descargar manual | Usuario autenticado | `ACT-CON-05..08` | `SEQ-CON-05..08` | Archivo descargado |
| UC-05 Publicar manual | `Admin` | `ACT-ACC-01..03`, `ACT-PUB-01..07` | `SEQ-ACC-01..03`, `SEQ-PUB-01..06` | Documento publicado con roles |
| UC-06 Asignar visibilidad | `Admin` | `ACT-PUB-03`, `ACT-PUB-06` | `SEQ-PUB-01`, `SEQ-PUB-05` | Roles asociados al documento |
| UC-07 Actualizar versión | `Admin` | `ACT-ACC-01..03`, `ACT-UPD-01..06` | `SEQ-ACC-01..03`, `SEQ-UPD-01..06` | Versión documental actualizada |
| UC-08 Archivar documento | `Admin` | `ACT-ACC-01..03`, `ACT-ARC-01..04` | `SEQ-ACC-01..03`, `SEQ-ARC-01..04` | Retirado de consulta activa |
| UC-09 Verificar acceso | Control acceso HIS | `ACT-ACC-01..03`, `ACT-EX-01..03` | `SEQ-ACC-01..03`, `SEQ-EX-01..03` | Verificación de seguridad |
| UC-10 Validar publicación | Módulo ASII-24 | `ACT-PUB-04`, `ACT-UPD-03`, `ACT-EX-04` | `SEQ-PUB-03`, `SEQ-UPD-03`, `SEQ-EX-04` | Validación de campos |

## Instrucciones de Validación
Para verificar la integridad de todos los artefactos, sintaxis PlantUML y trazabilidad:
```bash
php scripts/run-validation.php
```

## Estructura de Entregables
- [Informe Arquitectónico y Técnico](INFORME.md)
- [Guion de Defensa Oral](DEFENSA_ORAL.md)
- [Declaración de Asistencia de IA](DECLARACION_IA.md)
- [01. Alcance, Actores y Casos de Uso](docs/01-scope-actors-use-cases.md)
- [02. Actividad, Secuencia y Trazabilidad](docs/02-activity-sequence-traceability.md)
- [Diagramas PlantUML](docs/diagrams/)
