# Declaración Ética de Asistencia de Inteligencia Artificial

## 1. Identificación y Contexto
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Módulo**: ASII-25 — QA, Pruebas E2E, CI y Gestión de Versiones
- **Ciclo y Asignatura**: Octavo Ciclo, Análisis de Sistemas II (2026)
- **Asignación**: Semana 2 — Issue #13 / Asignación Odoo #3974

## 2. Herramientas de IA y Modelos Empleados
- **Modelos de Lenguaje**: Antigravity AI Assistant (Google DeepMind Gemini 2.5 Pro / Flash).
- **Herramientas de Soporte**: Extensión MCP de inspección de documentación, CLI local de Git y runtime PHP 8.2+ para análisis estático y formateo.

## 3. Registro de Interacciones y Prompts Principales
1. **Modelado de Requerimientos y Criterios BDD**: Consulta sobre estructuración formal de requerimientos funcionales y no funcionales para pipelines hospitalarios y escenarios Gherkin para compuertas de calidad.
2. **Aplicación del Principio SOLID DIP**: Análisis comparativo de diseño ("Antes" acoplado vs "Después" desacoplado mediante interfaces) según la fuente de la cátedra (*MVP Cluster* por Luis Miguel Rodríguez González).
3. **Generación de Validadores Automatizados**: Construcción del script de comprobación determinista en PHP 8.2 vanilla para auditar codificación UTF-8, LF, trazabilidad y presupuesto de líneas.

## 4. Supervisión y Validación Humana (`codsebas`)
- **Autoría Intelectual y Decisión de Diseño**: La delimitación del pipeline en PostgreSQL exclusivo, el descarte de SQLite, las políticas de umbral de cobertura (80%) y la jerarquía de interfaces fueron concebidas y validadas directamente por el estudiante.
- **Auditoría de Código y Calidad**: Se revisó minuciosamente que el código PHP ilustrativo no contuviera construcciones sintácticas inválidas, cumpliera tipado estricto (`declare(strict_types=1);`), y satisficiera la regla de presupuesto de líneas (<= 800 líneas agregadas).
- **Compromiso Ético**: La inteligencia artificial se utilizó estrictamente como herramienta de aceleración analítica y estructuración documental, asumiendo el estudiante plena responsabilidad técnica y ética sobre los entregables.
