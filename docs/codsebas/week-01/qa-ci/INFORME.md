# Informe Arquitectónico — QA, Pruebas E2E, CI y Gestión de Versiones

## 1. Delimitación y Gobernanza de Calidad
El presente informe formaliza el diseño arquitectónico de la Semana 1 para el módulo **ASII-25: QA, Pruebas E2E, CI y Gestión de Versiones**, enfocado en la gobernanza de calidad, automatización del pipeline y reproducibilidad para el Sistema Hospitalario Integrado (HIS).

### Separación de Responsabilidades y Contexto HIS
- **Contexto de Arquitectura**: El HIS opera bajo un modelo multi-tenant con esquemas federados en PostgreSQL (`central` y `hospital`). Cualquier cambio en migraciones o controladores puede comprometer datos clínicos críticos si no se valida en un entorno fiel e idempotente.
- **Límites de ASII-25**: El módulo no reemplaza las pruebas funcionales de cada microservicio; provee el marco estandarizado de integración continua, ejecución en arneses aislados, imposición de quality gates y custodia inmutable de evidencias previas a la liberación de versiones.

## 2. Políticas de Quality Gates
El pipeline impone compuertas de calidad innegociables para autorizar la integración a ramas protegidas (`develop`, `main`):
1. **Linting y Estándares Estrictos**: 0 errores y 0 warnings no permitidos (Laravel Pint, PHPStan nivel 8+, ESLint). Violaciones abortan el pipeline de inmediato (`ACT-EX-01`).
2. **Cobertura Mínima de Código**: Umbral mínimo del **80%** en lógica de dominio, servicios clínicos y controladores.
3. **Cero Defectos Críticos**: 0 fallos tolerados en pruebas unitarias, aserciones de esquemas de BD y suites de regresión.
4. **Validación E2E Clínica**: 100% de éxito en flujos de usuario completos (admisión, agenda, prescripción, consentimiento de datos).

## 3. Conservación de Evidencias y Versionado
- **Evidencias Inmutables**: Cada ejecución empaqueta artefactos estandarizados (reportes JUnit XML, cobertura LCOV/Clover, trazas y videos de pruebas E2E). Los artefactos se sellan con sumas de verificación **SHA-256** asociadas al `commit SHA`, impidiendo manipulaciones posteriores.
- **Gestión de Versiones Semánticas (SemVer)**: La liberación a producción (`main`) exige la firma del `Release Manager / Lead`, aplicando la convención `vMAJOR.MINOR.PATCH` mediante tags git inmutables y changelogs trazables.

## 4. Síntesis de Modelado UML
1. **Casos de Uso ([use-case-diagram.puml](docs/diagrams/use-case-diagram.puml))**: Modela 10 casos de uso (`UC-01..10`), estructurando la colaboración entre `Desarrollador`, `QA Engineer`, `CI Runner` y `Release Lead`.
2. **Flujo de Actividades ([activity-diagram.puml](docs/diagrams/activity-diagram.puml))**: Modela la ejecución escalonada con detección temprana de anomalías (`ACT-EX-01..05`), garantizando que builds defectuosos bloqueen el PR tempranamente.
3. **Flujo de Secuencia ([sequence-diagram.puml](docs/diagrams/sequence-diagram.puml))**: Define 9 participantes desacoplados interactuando con el harness PostgreSQL y el almacén de artefactos, reflejando el protocolo síncrono/asíncrono de CI/CD.

## 5. Conclusión
El modelado garantiza 100% de trazabilidad entre requisitos de calidad, validaciones automatizadas y control de versiones, sentando una arquitectura sólida para implementar los workflows de GitHub Actions y arneses de prueba en la Semana 2.
