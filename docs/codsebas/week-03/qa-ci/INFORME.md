# Informe Técnico — Arquitectura, Vistas y Patrones en Quality Gates

## 1. Delimitación y Alcance Arquitectónico (Semana 3)
El módulo **ASII-25: QA, Pruebas E2E, CI y Gestión de Versiones** define la arquitectura del motor de Quality Gates para el Sistema Hospitalario Integrado (HIS). Se estructura bajo tres vistas: **Procesos** (pipeline CI secuencial fail-fast), **Componentes** (handlers de compuertas, builder de arnés y observadores de eventos) y **Despliegue** (contenedor efímero PostgreSQL 16+ con esquemas federados `central` y `hospital`). Se prohíbe el uso de SQLite y MySQL por carecer de soporte para extensiones (`uuid-ossp`, `citext`), esquemas federados y aislamiento transaccional estricto.

## 2. Fundamentación Teórica y Patrones de Diseño
Conforme a la cátedra de Diseño de Software (*MVP Cluster*, Luis Miguel Rodríguez González, `https://mvpcluster.com/diseno-de-software-2/`), los patrones estructuran soluciones desacopladas:
- **Chain of Responsibility**: `QualityGateChainHandler` orquesta la evaluación secuencial (Pint -> Database -> PHPUnit -> Playwright). Aplica corte inmediato (*fail-fast*): cualquier falla aborta el pipeline al instante, ahorrando minutos de cómputo en CI y garantizando feedback temprano.
- **Builder**: `TestHarnessBuilder` encapsula la construcción determinista e idempotente del arnés de prueba sobre PostgreSQL 16+, parametrizando base de datos, esquemas federados y semillas clínicas.
- **Observer**: `EvidenceSealObserver` y `QualityMetricsObserver` reaccionan a la finalización de compuertas sellando artefactos con hash SHA-256 inmutable y despachando métricas de calidad sin acoplar los evaluadores.

## 3. Decisiones Técnicas y Criterios de Aceptación
1. **Política Fail-Fast**: Un fallo en linteo (Pint) o migraciones detiene el pipeline antes de ejecutar PHPUnit o Playwright, evitando estados inconsistentes y consumo innecesario de recursos.
2. **Determinismo Idempotente**: Cada corrida de CI provisiona un entorno PostgreSQL limpio y aislado, eliminando dependencias de estado residual y garantizando paridad con producción.
3. **Custodia Criptográfica**: Todo reporte de prueba se sella con hash SHA-256 vinculado al `commit SHA` para auditoría y trazabilidad clínica inmutable.
