# ASII-25 — Alcance, Actores y Casos de Uso

## 1. Identificación de la Actividad
- **Estudiante**: Albino Sebastián Rosales Ruano | **GitHub**: `codsebas`
- **Módulo oficial**: QA, Pruebas E2E, CI y Gestión de Versiones (ASII-25)
- **Actividad**: Modelado de ciclo de pruebas, quality gates y evidencias | **Semana**: 1
- **Tipo de evidencia**: Análisis, delimitación conceptual y modelado UML

## 2. Contexto de Ingeniería del HIS
| Componente HIS | Estado en Repositorio | Relación con ASII-25 |
|---|---|---|
| PostgreSQL Federado | Esquemas `central` y `hospital` | Objeto de verificación en arnés efímero (`TestHarness_PostgreSQL`) |
| Control de Ramas | `develop` y `main` protegidas | Imposición de status checks y bloqueo ante fallos de calidad |
| Autenticación JWT / RBAC | Módulos de identidad y roles | Sujetos de pruebas de regresión e integración continua |
| Pipeline QA / CI / E2E | En modelado (ASII-25) | Marco de aseguramiento y compuertas de calidad |

## 3. Problema y Objetivo
El Sistema Hospitalario Integrado (HIS) maneja información clínica sensible y requiere alta disponibilidad. Modificaciones no verificadas pueden provocar fallos en esquemas de base de datos o flujos asistenciales. El objetivo es modelar un marco de ingeniería de calidad que garantice ejecución automatizada de pruebas, verificación estricta de quality gates, preservación inmutable de evidencias y control de versiones semánticas.

## 4. Límite y Alcance
- **Límite**: Ciclo de vida de validación desde la apertura del PR hasta el release tagging inmutable.
- **Incluido**: Catálogo de casos de uso (UC-01 a UC-10), reglas de gobernanza, actores, flujos de actividad con excepciones y secuencias de CI/CD.
- **Fuera de alcance (Semana 1)**: Archivos `.github/workflows/*.yml` ejecutables, runners configurados en nube, scripts de pruebas en código fuente y tags git remotos.

## 5. Actores del Sistema
| Actor | Tipo | Responsabilidad en el Ciclo |
|---|---|---|
| Desarrollador (`Developer`) | Primario humano | Sube código, abre PR y atiende fallos reportados por el pipeline |
| QA Engineer (`QA_Engineer`) | Primario humano | Define planes de prueba, matrices de aceptación y audita evidencias |
| CI Runner (`GitHub_Actions_Runner`) | Sistema automatizado | Ejecuta linters, orquesta arneses de BD, corre suites y evalúa gates |
| Release Manager / Lead (`Release_Lead`) | Primario humano | Dictamina aprobaciones finales, autoriza merges y emite release tags |

## 6. Reglas de Gobernanza y Quality Gates
1. **RG-01 (Compuerta Innegociable)**: Cualquier fallo en linting, pruebas unitarias, BD o E2E bloquea inmediatamente la integración (`ACT-EX-05`).
2. **RG-02 (Umbral de Cobertura)**: Se exige un mínimo del 80% de cobertura en lógica de dominio clínico y controladores.
3. **RG-03 (Evidencias Inmutables)**: Toda ejecución debe persistir sus reportes (JUnit XML, LCOV, videos E2E) con firma hash SHA-256 inmutable.
4. **RG-04 (Release Semántico)**: Ningún tag de versión (`vMAJOR.MINOR.PATCH`) se emite sin previo pase del Quality Gate y dictamen del Release Lead.

## 7. Catálogo de Casos de Uso
| ID | Caso de Uso | Actor | Resumen y Excepción |
|---|---|---|---|
| UC-01 | Planificar matriz de pruebas | QA Engineer | Define escenarios, criterios de aceptación y tipos de prueba. Ex: Criterios incompletos. |
| UC-02 | Ejecutar validación estática y linting | CI Runner | Verifica código con Pint/PHPStan/ESLint. Ex: Violaciones de formato o tipos. |
| UC-03 | Ejecutar pruebas unitarias y BD | CI Runner | Ejecuta pruebas sobre arnés PostgreSQL efímero. Ex: Fallo en aserción o migración. |
| UC-04 | Ejecutar suite de pruebas E2E | CI Runner / QA | Simula flujos clínicos de usuario final. Ex: Regresión visual o funcional en UI/API. |
| UC-05 | Evaluar umbrales de Quality Gates | CI Runner | Contrasta métricas contra RG-01/RG-02. Ex: Cobertura < 80% o defectos críticos > 0. |
| UC-06 | Preservar evidencia inmutable | CI Runner | Genera y almacena paquete con digest SHA-256. Ex: Fallo de red en repositorio de artefactos. |
| UC-07 | Bloquear PR y reportar fallos | CI Runner / Dev | Marca check fallido en GitHub y bloquea merge. Ex: Notificación no entregada. |
| UC-08 | Dictaminar aprobación de PR | Release Lead | Revisa dictamen de Quality Gate y autoriza merge. Ex: PR rechazado por criterio humano. |
| UC-09 | Generar Release Tagging semántico | Release Lead | Emite etiqueta inmutable `vX.Y.Z` en rama protegida. Ex: Colisión de versión. |
| UC-10 | Auditar trazabilidad histórica | QA / Release Lead | Consulta evidencias históricas y métricas de calidad. Ex: Evidencia no encontrada. |

## 8. Diagrama UML y Decisiones de Modelado
El diagrama de casos de uso reside en [use-case-diagram.puml](diagrams/use-case-diagram.puml). Se modelan relaciones `<<include>>` obligatorias para la evaluación de Quality Gates (`UC-05` incluye `UC-02`, `UC-03`, `UC-04` y `UC-06`) y `<<extend>>` para el bloqueo preventivo (`UC-07`).
