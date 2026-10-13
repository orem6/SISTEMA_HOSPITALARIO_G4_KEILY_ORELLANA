# Declaración de Asistencia de Inteligencia Artificial

## 1. Identificación y Propósito
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Herramienta de IA**: Antigravity / Claude 3.7 Sonnet / Gemini
- **Propósito**: Asistencia en la estructuración de la arquitectura de calidad, redacción técnica sintetizada y script de validación automatizada para la entrega de la Semana 1.

## 2. Prompts Clave Utilizados
1. *Delimitación de Arquitectura de Calidad y Casos de Uso*:
   > "Modela la arquitectura de QA, Pruebas E2E, CI y Gestión de Versiones (ASII-25) para el HIS, definiendo actores (QA Engineer, Release Lead, Desarrollador, CI Runner) y casos de uso UC-01 a UC-10 orientados a quality gates y conservación de evidencias."
2. *Diseño de Flujos Dinámicos y Excepciones*:
   > "Genera diagramas PlantUML para el pipeline de CI/CD: actividad con decisiones de error tempranas y secuencia con participantes (CI Runner, Lint, PostgreSQL Harness, E2E, QualityGate, Artifact Storage), manteniendo familias formales de trazabilidad."
3. *Validación Automatizada y Control de Líneas*:
   > "Implementa un script en PHP 8.2 vanilla sin librerías externas que valide formato UTF-8 sin BOM, LF, sintaxis PlantUML, trazabilidad cruzada y presupuesto estricto de <= 800 líneas agregadas."

## 3. Criterio y Validación Humana
- **Gobernanza de Calidad**: Se definió el umbral de cobertura (>= 80%) y la política de 0 defectos críticos considerando la sensibilidad de los datos clínicos del HIS.
- **Topología de Pruebas**: Se verificó que el harness de PostgreSQL refleje fielmente los esquemas federados `central` y `hospital` existentes en el proyecto.
- **Auditoría Técnica y Trazabilidad**: Se revisó minuciosamente la correspondencia entre los identificadores `UC-*`, `ACT-*` y `SEQ-*` en los diagramas y documentos.
- **Ejecución y Verificación**: Se ejecutó localmente el script PHP comprobando el 100% de aserciones aprobadas antes del commit local. El estudiante asume plena autoría y comprensión de los artefactos.
