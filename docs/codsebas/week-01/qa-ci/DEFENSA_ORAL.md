# Guion de Defensa Oral — QA, Pruebas E2E, CI y Gestión de Versiones

## Preguntas Críticas de Evaluación y Respuestas Técnicas

### 1. ¿Cuál es el propósito central del módulo ASII-25 y cómo se delimita en la Semana 1?
**Respuesta**: Su propósito es diseñar y gobernar el ciclo de vida de aseguramiento de calidad del HIS: planificación de pruebas, ejecución automatizada en CI, suites E2E, imposición de quality gates y versionado inmutable. En la Semana 1, se delimita al análisis arquitectónico, catálogo de casos de uso (UC-01 a UC-10), modelado UML y definición formal de políticas, sin implementar pipelines YAML ejecutables ni código de pruebas prematuro.

### 2. ¿Qué actores intervienen y cómo se distribuyen sus responsabilidades?
**Respuesta**: Intervienen cuatro actores:
- `Desarrollador`: Publica código y pruebas en pull requests.
- `QA Engineer`: Planifica matrices de prueba, criterios de aceptación y audita el cumplimiento de calidad.
- `CI Runner`: Orquesta autónomamente el pipeline (linting, arnés PostgreSQL, suites de pruebas y cálculo de métricas).
- `Release Manager / Lead`: Evalúa el dictamen de los quality gates, aprueba o rechaza la integración a ramas protegidas y genera el release tag semántico.

### 3. ¿Cuáles son las políticas y umbrales definidos para los Quality Gates?
**Respuesta**: Se establecen cuatro políticas obligatorias:
1. Linting estricto: 0 errores y 0 desviaciones de formato.
2. Cobertura de código: mínimo del 80% en lógica de negocio y controladores clínicos.
3. Defectos críticos: 0 fallos en pruebas unitarias e integración de base de datos.
4. Suites E2E: 100% de escenarios clínicos críticos aprobados sin regresiones.

### 4. ¿Cómo se manejan las excepciones en el pipeline de CI y qué impacto tienen en el PR?
**Respuesta**: Cualquier anomalía detectada (fallo de linting `ACT-EX-01`, fallo de base de datos `ACT-EX-02`, fallo E2E `ACT-EX-03` o incumplimiento de métricas `ACT-EX-04`) detiene inmediatamente las fases posteriores, emite una notificación con logs consolidados y aplica la acción `ACT-EX-05`: marcar el check de GitHub como fallido y bloquear el PR para impedir el merge en ramas protegidas.

### 5. ¿De qué manera se garantiza la inmutabilidad y trazabilidad de las evidencias de prueba?
**Respuesta**: Los artefactos de prueba (reportes JUnit XML, mapas de cobertura LCOV, grabaciones de video de flujos E2E) son empaquetados por el CI Runner y almacenados en un repositorio inmutable (`Artifact_Storage`). Cada paquete genera un hash criptográfico SHA-256 ligado al `commit SHA` del PR, impidiendo cualquier modificación o sustitución no auditada.

### 6. ¿Cómo se estructura la relación entre pruebas de integración y la base de datos federada PostgreSQL?
**Respuesta**: El CI Runner aprovisiona un arnés efímero de PostgreSQL (`TestHarness_PostgreSQL`) que replica con fidelidad los esquemas federados `central` y `hospital`. Las pruebas unitarias y de integración ejecutan migraciones completas, validan restricciones de integridad y prueban aislamiento multi-tenant en un contenedor desechable e idempotente.
