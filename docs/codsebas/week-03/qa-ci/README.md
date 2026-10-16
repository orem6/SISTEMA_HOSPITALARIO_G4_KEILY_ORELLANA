# ASII-25 — QA, Pruebas E2E, CI y Gestión de Versiones (Semana 3)

## Resumen Ejecutivo
Diseño arquitectónico y patrones de software del motor de Quality Gates y orquestación de pruebas del Sistema Hospitalario Integrado (HIS).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`) | **Asignación Odoo**: #3975 | **Issue GitHub**: #21
- **Rama activa**: `feature/asii-25-codsebas-week-3` | **Fuente teórica**: `https://mvpcluster.com/diseno-de-software-2/`

## Vistas Arquitectónicas y Patrones Aplicados
- **Vista de Procesos**: Pipeline CI determinista con política *fail-fast* (Pint -> Database -> PHPUnit -> Playwright).
- **Vista de Componentes**: Desacoplamiento estructural con Chain of Responsibility, Builder y Observer.
- **Vista de Despliegue**: Contenedor efímero PostgreSQL 16+ con esquemas federados `central` y `hospital` (descarte de SQLite/MySQL).
- **Chain of Responsibility**: `QualityGateChainHandler` para ejecución secuencial con corte inmediato ante fallos (*fail-fast*).
- **Builder Pattern**: `TestHarnessBuilder` para aprovisionamiento reproducible e idempotente del arnés de pruebas PostgreSQL.
- **Observer Pattern**: `EvidenceSealObserver` y `QualityMetricsObserver` para métricas y sellado criptográfico SHA-256.

## Matriz de Trazabilidad Cruzada
| Requerimiento (Sem 2) | Componente / Patrón (Sem 3) | Vista Arquitectónica |
|---|---|---|
| RF-02 Linteo Pint | `PintQualityHandler` (CoR) | Procesos / Componentes |
| RF-03 PostgreSQL Aislado | `TestHarnessBuilder` (Builder) | Componentes / Despliegue |
| RF-04 Pruebas PHPUnit | `PhpUnitQualityHandler` (CoR) | Procesos / Componentes |
| RF-05 Pruebas E2E | `PlaywrightQualityHandler` (CoR) | Procesos / Componentes |
| RF-06 Quality Gates Fail-Fast | `QualityGateChainHandler` (CoR) | Procesos / Componentes |
| RF-07 Evidencias Inmutables | `EvidenceSealObserver` (Observer) | Componentes / Procesos |

## Instrucciones de Validación
Ejecutar el validador automatizado en PHP 8.2+ vanilla (presupuesto estricto <= 320 líneas, límite 400):
```bash
php docs/codsebas/week-03/qa-ci/scripts/run-validation.php
```
- [Informe](INFORME.md) | [Defensa Oral](DEFENSA_ORAL.md) | [Declaración IA](DECLARACION_IA.md)
- [01. Vistas Arquitectónicas](docs/01-architectural-views.md) | [02. Patrones de Diseño](docs/02-design-patterns.md)
