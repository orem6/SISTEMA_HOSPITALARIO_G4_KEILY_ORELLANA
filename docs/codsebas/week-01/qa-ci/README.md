# ASII-25 — QA, Pruebas E2E, CI y Gestión de Versiones

## Resumen Ejecutivo
Entrega de análisis y diseño arquitectónico (Semana 1) para el módulo de **QA, Pruebas E2E, Integración Continua y Gestión de Versiones (ASII-25)** en el Sistema Hospitalario Integrado (HIS).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-25-codsebas-week-1`

## Alcance del Módulo
- **Incluido**: Delimitación del ciclo de calidad, catálogo de casos de uso (UC-01 a UC-10), diagrama de casos de uso, diagrama de actividades con excepciones y bloqueo preventivo (`ACT-*`), diagrama de secuencia del pipeline de CI/CD con harness PostgreSQL y evidencias (`SEQ-*`), y matriz de trazabilidad integral.
- **Límites y Fuera de Alcance (Semana 1)**: Modelado conceptual y reglas de gobernanza de calidad. No incluye scripts YAML de GitHub Actions ejecutables, configuración de runners de producción, tests E2E automatizados en código ni tags git remotos.

## Actores del Sistema
- **Desarrollador (`Developer`)**: Envía pull requests con código y pruebas unitarias/integración.
- **QA Engineer (`QA_Engineer`)**: Planifica matrices de prueba, criterios de aceptación y audita trazabilidad.
- **CI Runner (`GitHub_Actions_Runner`)**: Orquesta el pipeline automatizado (linters, harness DB, suites y gates).
- **Release Manager / Lead (`Release_Lead`)**: Dictamina aprobación final, autoriza merges y emite release tags inmutables.

## Matriz de Trazabilidad Resumida
| Caso de Uso | Actor | Actividad | Secuencia | Resultado Trazable |
|---|---|---|---|---|
| UC-01 Planificar pruebas | QA Engineer | `ACT-PLN-01` | `SEQ-PLN-01` | Matriz de prueba y criterios definidos |
| UC-02 Análisis estático y linting | CI Runner | `ACT-LNT-01`, `ACT-EX-01` | `SEQ-LNT-01..02`, `SEQ-EX-01` | Código validado contra estándares |
| UC-03 Pruebas unitarias y BD | CI Runner | `ACT-UNT-01..02`, `ACT-EX-02` | `SEQ-UNT-01..03`, `SEQ-EX-02` | Verificación en harness PostgreSQL |
| UC-04 Pruebas E2E clínicas | CI Runner / QA | `ACT-E2E-01..02`, `ACT-EX-03` | `SEQ-E2E-01..02`, `SEQ-EX-03` | Flujos clínicos críticos verificados |
| UC-05 Evaluar Quality Gates | CI Runner | `ACT-QG-01`, `ACT-EX-04` | `SEQ-QG-01..03`, `SEQ-EX-04` | Evaluación de umbrales (cobertura/fallos) |
| UC-06 Preservar evidencia inmutable | CI Runner | `ACT-ART-01..02` | `SEQ-ART-01..03` | Evidencias persistidas con hash SHA-256 |
| UC-07 Bloquear PR por fallo | CI Runner / Dev | `ACT-EX-05` | `SEQ-EX-05` | PR bloqueado con reporte de defectos |
| UC-08 Dictaminar aprobación de PR | Release Lead | `ACT-REL-01` | `SEQ-REL-01..02` | Veredicto de aprobación y merge |
| UC-09 Generar Release Tagging | Release Lead | `ACT-REL-02` | `SEQ-REL-03` | Versión semántica inmutable emitida |
| UC-10 Auditar trazabilidad histórica | QA / Release Lead | `ACT-PLN-01`, `ACT-ART-02` | `SEQ-PLN-01`, `SEQ-ART-03` | Pista de auditoría de calidad accesible |

## Instrucciones de Validación
Para verificar la integridad de los artefactos, estándares de formato, sintaxis PlantUML y presupuesto:
```bash
php scripts/run-validation.php
```

## Estructura de Entregables
- [Informe Arquitectónico y Gobernanza de Calidad](INFORME.md)
- [Guion de Defensa Oral (6 Preguntas Clave)](DEFENSA_ORAL.md)
- [Declaración de Asistencia de IA](DECLARACION_IA.md)
- [01. Alcance, Contexto y Casos de Uso](docs/01-scope-actors-use-cases.md)
- [02. Actividad, Secuencia y Trazabilidad](docs/02-activity-sequence-traceability.md)
- [Diagramas PlantUML](docs/diagrams/)
