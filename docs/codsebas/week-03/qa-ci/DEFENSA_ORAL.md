# Guion de Defensa Oral — Arquitectura y Patrones en Quality Gates

## Preguntas Críticas y Respuestas Técnicas de Sustentación

### 1. ¿Cómo se articulan las Vistas de Procesos, Componentes y Despliegue en el módulo ASII-25?
**Respuesta**: La Vista de Procesos formaliza el ciclo temporal del pipeline CI con política fail-fast; la Vista de Componentes desacopla las responsabilidades mediante Chain of Responsibility (handlers), Builder (arnés) y Observer (sellado y métricas); y la Vista de Despliegue define la infraestructura aislada con contenedores efímeros PostgreSQL 16+, garantizando reproducibilidad sin interferencias.

### 2. ¿Por qué se implementó el patrón Chain of Responsibility para los Quality Gates?
**Respuesta**: Permite encadenar validadores heterogéneos (Pint, Database, PHPUnit, Playwright) con desacoplamiento total. Si una etapa falla, la cadena ejecuta corte inmediato (*fail-fast*), bloqueando el PR y evitando consumir recursos en etapas posteriores más pesadas como las pruebas E2E.

### 3. ¿Qué beneficio aporta el patrón Builder en el arnés de pruebas PostgreSQL?
**Respuesta**: El arnés hospitalario requiere configurar variables de entorno, crear esquemas federados (`central` y `hospital`), cargar extensiones nativas y sembrar datos clínicos. `TestHarnessBuilder` encapsula esta construcción paso a paso de forma determinista, evitando código espagueti de inicialización.

### 4. ¿Cuál es el rol del patrón Observer en la inmutabilidad y telemetría?
**Respuesta**: Separa la evaluación de calidad de la persistencia de evidencias y métricas. Al culminar cada compuerta, el despachador notifica a los observadores: `EvidenceSealObserver` genera el hash criptográfico SHA-256 del artefacto vinculado al `commit SHA`, y `QualityMetricsObserver` publica telemetría sin ensuciar los handlers.

### 5. ¿Cómo se sustenta teóricamente la arquitectura según la cátedra?
**Respuesta**: Conforme a Luis Miguel Rodríguez González en MVP Cluster (`https://mvpcluster.com/diseno-de-software-2/`), los patrones de diseño previenen el acoplamiento rígido y favorecen la cohesión. La combinación de CoR, Builder y Observer respeta OCP y DIP: es posible agregar nuevas compuertas u observadores sin modificar el motor central.

### 6. ¿Por qué es inadmisible usar SQLite o MySQL en la Vista de Despliegue?
**Respuesta**: El HIS exige esquemas federados multi-tenant (`central`/`hospital`), tipos `uuid`, `citext` y concurrencia relacional estricta. SQLite y MySQL no soportan dicha arquitectura y generarían falsos positivos en CI: pruebas verdes en local que colapsarían catastróficamente en el entorno hospitalario real.
