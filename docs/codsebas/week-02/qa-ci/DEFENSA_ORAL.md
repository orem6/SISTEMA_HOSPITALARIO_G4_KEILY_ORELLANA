# Guion de Defensa Oral — Requerimientos y Diseño SOLID DIP

## Preguntas Críticas de Evaluación y Respuestas Técnicas

### 1. ¿Cómo se articulan los casos de uso de la Semana 1 con los RF y RNF de la Semana 2?
**Respuesta**: Los casos de uso de la Semana 1 (`UC-01` a `UC-10`) definieron la interacción de los actores con el sistema de calidad. En la Semana 2, estos se traducen en especificaciones ejecutables: `UC-02` y `UC-03` se formalizan en `RF-02` (Laravel Pint), `RF-03` (arnés PostgreSQL) y `RF-04` (PHPUnit); `UC-05` y `UC-07` se materializan en `RF-06` (Quality Gates) y `RF-08` (bloqueo estricto del PR). A su vez, los RNF (`RNF-01` a `RNF-06`) imponen restricciones no negociables de determinismo, latencia máxima (< 10 min), inmutabilidad criptográfica y desacoplamiento mediante DIP.

### 2. ¿Por qué es imperativo descartar SQLite y exigir PostgreSQL aislado en CI?
**Respuesta**: El Sistema Hospitalario Integrado utiliza una arquitectura multi-tenant con esquemas federados `central` y `hospital`, extensiones nativas (`uuid-ossp`, `citext`), restricciones de clave foránea compuestas y funciones PL/pgSQL. SQLite no soporta estas características ni replica la semántica de bloqueos y transacciones de PostgreSQL. Utilizar SQLite en CI generaría falsos positivos masivos: pruebas que pasan en SQLite pero colapsan en producción con datos clínicos reales.

### 3. ¿Cómo se manifestaba la violación del principio DIP en el diseño inicial ("Antes")?
**Respuesta**: En el diseño inicial, la clase de alto nivel `QualityGateEngine` instanciaba directamente clases concretas de bajo nivel: `new PintLinterRunner()`, `new PhpUnitTestRunner()` y `new PlaywrightE2ERunner()`. Esto violaba directamente el principio DIP, ya que el motor dependía de implementaciones específicas y volátiles. Si se requería incorporar una herramienta adicional como PHPStan o pruebas de mutación, era forzoso modificar el código de `QualityGateEngine`, vulnerando a su vez el principio Open/Closed (OCP).

### 4. ¿Cómo resuelve la arquitectura el principio DIP según la fuente teórica de la cátedra?
**Respuesta**: Siguiendo a Luis Miguel Rodríguez González en MVP Cluster (`https://mvpcluster.com/diseno-de-software-2/`), invertimos la dirección de la dependencia haciendo que tanto el módulo de alto nivel como los de bajo nivel dependan de abstracciones. Se definieron `QualityGateRuleInterface` y `EvidenceCollectorInterface`. `QualityGateEngine` ahora recibe una colección polimórfica de reglas vía constructor. Cualquier nuevo validador simplemente implementa `QualityGateRuleInterface` y se inyecta sin tocar una sola línea del motor.

### 5. ¿De qué manera los Criterios de Aceptación en Gherkin garantizan el bloqueo del PR?
**Respuesta**: Los criterios BDD establecen reglas deterministas: ante cualquier salida distinta de cero en linteo (`CA-02`), fallo en aserciones de BD/PHPUnit (`CA-03`) o cobertura inferior al 80% (`CA-04`), el motor emite un dictamen `REJECTED`. El runner de CI intercepta este veredicto y actualiza el commit status a `failure` mediante la GitHub Status API, impidiendo que la rama protegida autorice el merge mientras persistan defectos.

### 6. ¿Cómo se garantiza la inmutabilidad de las evidencias y la tolerancia a fallos?
**Respuesta**: El recolector de evidencias empaqueta reportes JUnit, mapas de cobertura LCOV y grabaciones de Playwright, calculando un hash criptográfico SHA-256 ligado al `commit SHA` del PR (`RNF-03`). Para cumplir con `RNF-05` (tolerancia a fallos), `EvidenceCollectorInterface` implementa políticas de reintento exponencial y almacenamiento secundario de contingencia, asegurando que fallos transitorios de red nunca descarten la evidencia ni silencien una ejecución fallida.
