# ASII-25 — QA, Pruebas E2E, CI y Gestión de Versiones (Semana 2)

## Resumen Ejecutivo
Entrega técnica de especificación formal y diseño arquitectónico para el módulo **ASII-25: QA, Pruebas E2E, CI y Gestión de Versiones** (Semana 2 / Asignación Odoo #3974 / Issue GitHub #13).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-25-codsebas-week-2`
- **Fuente teórica**: Principios SOLID (`https://mvpcluster.com/diseno-de-software-2/`)

## Catálogo de Requerimientos (Semana 2)
- **RF-01 a RF-10**: Disparador CI, linteo Pint, arnés PostgreSQL efímero, PHPUnit, Playwright E2E, Quality Gates (cobertura >=80%), evidencias SHA-256 inmutables, bloqueo estricto, dictamen y etiquetado SemVer.
- **RNF-01 a RNF-06**: Determinismo, tiempo de pipeline <10 min, trazabilidad SHA-256, desacoplamiento DIP, resiliencia y custodia de secretos.

## Matriz de Trazabilidad Cruzada
| Caso de Uso (Sem 1) | Requerimiento (Sem 2) | Componente DIP |
|---|---|---|
| UC-01 Planificar pruebas | RF-01, RF-02 | `LintQualityRule` |
| UC-03 Ejecutar pruebas BD | RF-03, RF-04 | `TestCoverageQualityRule` |
| UC-05 Ejecutar E2E | RF-05, RF-06 | `E2EQualityRule` |
| UC-07 Calidad Quality Gates | RF-07, RF-08 | `QualityGateEngine::evaluatePipeline` |
| UC-09 Emitir dictamen | RF-09, RF-10 | `EvidenceCollectorInterface` |

## Instrucciones de Validación
Ejecutar validador automatizado (límite estricto <= 400 líneas):
```bash
php scripts/run-validation.php
```
- [Informe](INFORME.md) | [Defensa Oral](DEFENSA_ORAL.md) | [Declaración IA](DECLARACION_IA.md)
- [01. Requerimientos](docs/01-requirements-and-acceptance-criteria.md) | [02. SOLID DIP](docs/02-solid-dependency-inversion.md)
