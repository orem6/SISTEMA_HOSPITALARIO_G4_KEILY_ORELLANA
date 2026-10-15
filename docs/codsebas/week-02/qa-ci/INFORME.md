# Informe Técnico — Requerimientos y Diseño SOLID DIP en Quality Gates

## 1. Delimitación y Alcance del Módulo (Semana 2)
El módulo **ASII-25: QA, Pruebas E2E, CI y Gestión de Versiones** evoluciona en la Semana 2 del modelado de casos de uso hacia la especificación formal de requerimientos de software y el diseño orientado a objetos desacoplado para el Sistema Hospitalario Integrado (HIS).
- **Alcance Funcional**: Orquestación automatizada de etapas de integración continua (linteo con Laravel Pint, harness de base de datos PostgreSQL aislado, suites unitarias/feature con PHPUnit, pruebas E2E con Playwright), evaluación de compuertas de calidad, custodia de evidencias inmutables, bloqueo estricto de Pull Requests y etiquetado SemVer.
- **Exclusión de Motores en Memoria**: Se descarta de forma categórica el uso de SQLite y MySQL en el pipeline. El HIS requiere esquemas federados multi-tenant (`central` y `hospital`) con extensiones PostgreSQL (`uuid-ossp`, `citext`), haciendo inviable el uso de motores simplificados que ocultan errores de concurrencia y restricciones relacionales.

## 2. Catálogo de Requerimientos y Criterios de Aceptación
Se definieron formalmente 10 Requerimientos Funcionales (`RF-01` a `RF-10`) y 6 Requerimientos No Funcionales (`RNF-01` a `RNF-06`):
- **Funcionales clave**: Detección de disparadores CI (`RF-01`), linteo de código (`RF-02`), aprovisionamiento de PostgreSQL efímero (`RF-03`), ejecución de pruebas unitarias (`RF-04`) y E2E clínicas (`RF-05`), evaluación de Quality Gates (`RF-06`), almacenamiento inmutable con hash SHA-256 (`RF-07`), bloqueo estricto de PR (`RF-08`), dictamen humano del Release Lead (`RF-09`) y etiquetado SemVer (`RF-10`).
- **No Funcionales**: Determinismo en BD (`RNF-01`), latencia de pipeline < 10 minutos (`RNF-02`), inmutabilidad y no repudio (`RNF-03`), desacoplamiento arquitectónico mediante DIP (`RNF-04`), tolerancia a fallos en persistencia de evidencias (`RNF-05`) y seguridad de secretos (`RNF-06`).
- **Criterios de Aceptación**: Formalizados en sintaxis Gherkin (Dado-Cuando-Entonces) cubriendo: pase limpio con quality gates aprobados, bloqueo preventivo por infracción de linteo, bloqueo por falla en pruebas o esquemas de BD, y bloqueo por cobertura insuficiente (< 80%).

## 3. Fundamentación Teórica del Principio SOLID DIP
De acuerdo con la fuente teórica de la cátedra (*Principios básicos del diseño de software*, MVP Cluster, por Luis Miguel Rodríguez González, `https://mvpcluster.com/diseno-de-software-2/`):
> *"Las clases de alto nivel no tienen que depender de otras de bajo nivel, sino que ambas dependan de abstracciones, así como que las abstracciones no deben depender de los detalles, sino al contrario."*

### Análisis Técnico: Antes vs Después
- **Diseño Antes (Violación DIP y OCP)**: La clase de alto nivel `QualityGateEngine` dependía directamente de clases concretas de bajo nivel (`PintLinterRunner`, `PhpUnitTestRunner`, `PlaywrightE2ERunner`). Cada nueva herramienta de validación (ej. PHPStan o Mutation Testing) obligaba a modificar el código fuente de `QualityGateEngine`, generando fragilidad, rigidez y acoplamiento severo.
- **Diseño Después (Solución con DIP)**: Se introducen las abstracciones `QualityGateRuleInterface` y `EvidenceCollectorInterface`. `QualityGateEngine` depende exclusivamente de estas abstracciones e interactúa con reglas polimórficas (`LintQualityRule`, `TestCoverageQualityRule`, `E2EQualityRule`) inyectadas vía constructor. Los detalles concretos dependen de la abstracción, logrando extensibilidad sin modificación.

## 4. Decisiones Técnicas y Gobernanza
1. **Determinismo Idempotente**: Las pruebas de base de datos se ejecutan contra un contenedor efímero de PostgreSQL real inicializado con migraciones y semillas clínicas, garantizando que el pipeline sea reproducible y libre de efectos secundarios.
2. **Custodia Criptográfica**: Todo artefacto de prueba (reportes JUnit, cobertura LCOV, trazas de Playwright) se empaqueta y asocia a un hash SHA-256 inmutable vinculado al `commit SHA`, satisfaciendo la auditoría técnica requerida para software médico.
