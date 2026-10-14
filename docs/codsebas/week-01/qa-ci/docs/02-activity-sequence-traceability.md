# ASII-25 — Actividad, Secuencia y Trazabilidad

## 1. Propósito y Alcance Dinámico
Este documento formaliza la dinámica operativa del módulo **ASII-25: QA, Pruebas E2E, CI y Gestión de Versiones**, detallando el flujo de actividades, el protocolo de mensajes entre componentes del pipeline y la matriz de trazabilidad cruzada con los casos de uso.

## 2. Flujo de Actividad y Decisiones
El diagrama completo se encuentra en [activity-diagram.puml](diagrams/activity-diagram.puml).
- **Planificación y Disparo**:
  - `ACT-PLN-01`: QA Engineer formula matriz de pruebas y criterios de aceptación.
  - `ACT-PLN-02`: Apertura de PR por Desarrollador activa el runner de CI.
- **Validación Estática**:
  - `ACT-LNT-01`: Ejecución de análisis estático y linters. Falla hacia `ACT-EX-01`.
- **Pruebas Unitarias y Base de Datos**:
  - `ACT-UNT-01`: Aprovisionamiento de arnés PostgreSQL efímero (`central`/`hospital`).
  - `ACT-UNT-02`: Ejecución de pruebas unitarias y de integración. Falla hacia `ACT-EX-02`.
- **Pruebas End-to-End (E2E)**:
  - `ACT-E2E-01`: Despliegue de entorno efímero E2E.
  - `ACT-E2E-02`: Ejecución de escenarios clínicos completos. Falla hacia `ACT-EX-03`.
- **Quality Gates y Evidencias**:
  - `ACT-QG-01`: Evaluación de umbrales (cobertura >= 80%, 0 fallos críticos). Falla hacia `ACT-EX-04`.
  - `ACT-ART-01`: Consolidación de artefactos (JUnit XML, LCOV, grabaciones de video).
  - `ACT-ART-02`: Firma digital SHA-256 y almacenamiento inmutable de evidencias.
- **Dictamen y Release Tagging**:
  - `ACT-REL-01`: Release Lead dictamina aprobación del PR y habilita merge.
  - `ACT-REL-02`: Emisión de release tag semántico (`vX.Y.Z`) en rama protegida.
- **Ruta de Excepción**:
  - `ACT-EX-05`: Bloqueo preventivo de PR, reporte consolidado al desarrollador y terminación con fallo.

## 3. Secuencia y Participantes
El diagrama de secuencia se ubica en [sequence-diagram.puml](diagrams/sequence-diagram.puml).
- **Participantes**: `QA_Engineer`, `Developer`, `GitHub_Actions_Runner`, `Lint_Engine`, `TestHarness_PostgreSQL`, `E2E_Runner`, `QualityGate_Validator`, `Artifact_Storage` y `Release_Lead`.
- **Mensajería**:
  - `SEQ-PLN-01..02`: Definición de matrices y activación del pipeline por PR.
  - `SEQ-LNT-01..02`: Invocación de análisis estático y reporte de conformidad.
  - `SEQ-UNT-01..03`: Inicialización de harness PostgreSQL, ejecución unitaria y recolección de cobertura.
  - `SEQ-E2E-01..02`: Ejecución de flujos E2E clínicos y reporte de aserciones visuales/funcionales.
  - `SEQ-QG-01..03`: Envío de métricas consolidadas, validación de umbrales y veredicto de compuerta.
  - `SEQ-ART-01..03`: Empaquetado de reportes, generación de SHA-256 y confirmación de custodia inmutable.
  - `SEQ-REL-01..03`: Notificación de gate superado, dictamen de merge y etiquetado semántico.
  - `SEQ-EX-01..05`: Manejo de fallos en cada etapa y notificación de bloqueo de PR.

## 4. Matriz de Trazabilidad Cruzada
| Caso de Uso | Actor Principal | Nodos de Actividad | Mensajes de Secuencia | Resultado Trazable |
|---|---|---|---|---|
| UC-01 Planificar pruebas | QA Engineer | `ACT-PLN-01` | `SEQ-PLN-01` | Matriz de pruebas y criterios definida |
| UC-02 Análisis estático | CI Runner | `ACT-LNT-01`, `ACT-EX-01` | `SEQ-LNT-01..02`, `SEQ-EX-01` | Código validado contra estándares |
| UC-03 Pruebas unitarias/BD | CI Runner | `ACT-UNT-01..02`, `ACT-EX-02` | `SEQ-UNT-01..03`, `SEQ-EX-02` | Esquemas y lógica verificados en arnés |
| UC-04 Pruebas E2E | CI Runner / QA | `ACT-E2E-01..02`, `ACT-EX-03` | `SEQ-E2E-01..02`, `SEQ-EX-03` | Escenarios clínicos ejecutados y grabados |
| UC-05 Evaluar Quality Gate | CI Runner | `ACT-QG-01`, `ACT-EX-04` | `SEQ-QG-01..03`, `SEQ-EX-04` | Umbrales de calidad verificados |
| UC-06 Preservar evidencia | CI Runner | `ACT-ART-01..02` | `SEQ-ART-01..03` | Artefactos inmutables con hash SHA-256 |
| UC-07 Bloquear PR | CI Runner / Dev | `ACT-EX-05` | `SEQ-EX-05` | PR bloqueado con reporte de defectos |
| UC-08 Dictaminar PR | Release Lead | `ACT-REL-01` | `SEQ-REL-01..02` | Merge autorizado en rama protegida |
| UC-09 Generar Release Tag | Release Lead | `ACT-REL-02` | `SEQ-REL-03` | Tag semántico inmutable emitido |
| UC-10 Auditar trazabilidad | QA / Lead | `ACT-PLN-01`, `ACT-ART-02` | `SEQ-PLN-01`, `SEQ-ART-03` | Evidencias y métricas auditables |

## 5. Verificación de Consistencia e Idempotencia
- **Aislamiento de Entorno**: Cada ejecución de `TestHarness_PostgreSQL` corre en esquemas efímeros, garantizando idempotencia total.
- **Fail-Fast**: El pipeline aborta en la primera etapa con errores (`ACT-EX-*`), optimizando recursos de cómputo en CI.
- **Inmutabilidad Criptográfica**: Las evidencias vinculadas a SHA-256 aseguran que ningún reporte pueda modificarse a posteriori.
