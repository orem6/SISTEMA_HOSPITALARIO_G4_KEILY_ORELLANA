# Vistas Arquitectónicas del Motor de Quality Gates (ASII-25)
**Estudiante:** Albino Sebastián Rosales Ruano (`codsebas`) | **Asignación Odoo:** #3975 | **Issue GitHub:** #21

## 1. Vista de Procesos (Pipeline CI Fail-Fast)
La Vista de Procesos modela el flujo secuencial de validación orquestado en cada Pull Request. Aplica una política estricta de corte inmediato (*fail-fast*):

```
[PR Trigger] -> [Laravel Pint (Linteo)] ---> [FALLO] -> [Abortar CI / PR Bloqueado]
                     | (Éxito)
             [Aprovisionar PostgreSQL 16+ efímero (TestHarnessBuilder)]
                     | (Éxito)
             [Migraciones DB (central/hospital)] ---> [FALLO] -> [Abortar CI / PR Bloqueado]
                     | (Éxito)
             [PHPUnit Suite (Unit/Feature)] -------> [FALLO] -> [Abortar CI / PR Bloqueado]
                     | (Éxito)
             [Playwright E2E (Flujos Clínicos)] ---> [FALLO] -> [Abortar CI / PR Bloqueado]
                     | (Éxito)
             [Sellado SHA-256 & Métricas (Observer)] -> [Dictamen APPROVED / Merge Autorizado]
```
- **Principio Fail-Fast**: Detiene la ejecución en la compuerta exacta que falle, impidiendo etapas costosas posteriores (como suites E2E) y optimizando el tiempo de cómputo en CI (< 10 minutos).

## 2. Vista de Componentes
Estructura modular desacoplada mediante patrones de diseño orientados a objetos:
- `QualityGateChainHandler` (Abstracción CoR): Define el contrato del eslabón y la propagación fail-fast (`handle`, `setNext`).
- **Eslabones Concretos**: `PintQualityHandler`, `DatabaseMigrationHandler`, `PhpUnitQualityHandler` y `PlaywrightQualityHandler`.
- `TestHarnessBuilder` (Builder): Construye la configuración y dependencias del arnés PostgreSQL 16+ de forma determinista.
- `QualityEventDispatcher` & `QualityObserverInterface` (Observer): Publican eventos de estado desacoplados hacia `EvidenceSealObserver` (sellado SHA-256) y `QualityMetricsObserver` (telemetría).

## 3. Vista de Despliegue (PostgreSQL 16+ Efímero)
La topología de ejecución garantiza aislamiento total y reproducibilidad en integración continua:
- **Runner CI**: Contenedor Ubuntu Linux ejecutando runtime PHP 8.2-CLI con extensiones `pdo_pgsql`, Composer y Playwright headless Chromium.
- **Contenedor PostgreSQL 16+ Efímero**: Servicio aislado instanciado con red privada `bridge`, memoria en RAM (`tmpfs`), extensiones `uuid-ossp` y `citext`, y esquemas federados `central` y `hospital`.
- **Descarte de Motores In-Memory**: Se descarta formalmente SQLite y MySQL. El HIS requiere integridad relacional multi-tenant y tipos nativos PostgreSQL; simular la BD en motores simplificados introduce falsos positivos inadmisibles en software hospitalario.
