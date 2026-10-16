# Declaración Ética de Asistencia de Inteligencia Artificial

## 1. Identificación y Contexto
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`) | **Asignación Odoo**: #3975 | **Issue GitHub**: #21
- **Módulo**: ASII-25 — QA, Pruebas E2E, CI y Gestión de Versiones | **Ciclo**: Octavo Ciclo, Análisis de Sistemas II (2026)

## 2. Herramientas de IA Empleadas
- Antigravity AI Assistant (Google DeepMind Gemini 2.5 Pro) con CLI de Git y runtime PHP 8.2+ para análisis y validación local.

## 3. Registro de Interacciones y Prompts Principales
1. **Modelado de Vistas Arquitectónicas**: Diseño conceptual de vistas de Procesos (pipeline fail-fast), Componentes y Despliegue en PostgreSQL 16+.
2. **Patrones de Diseño de Software**: Implementación limpia en PHP 8.2+ vanilla de Chain of Responsibility, Builder y Observer conforme a la cátedra de MVP Cluster (*Luis Miguel Rodríguez González*).
3. **Suite de Validación Determinista**: Generación del script de auditoría en PHP para validar UTF-8, LF, sintaxis y presupuesto <= 400 líneas.

## 4. Supervisión y Validación Humana (`codsebas`)
- **Autoría y Criterio Técnico**: La secuencia de compuertas (Pint -> Database -> PHPUnit -> Playwright), la exclusión de motores SQLite/MySQL, la parametrización del Builder y el sellado SHA-256 fueron determinados y auditados por el estudiante.
- **Calidad y Verificación**: Se comprobó que el código PHP cumpliera tipado estricto (`declare(strict_types=1);`), sintaxis 100% válida con `php -l` y presupuesto de líneas (<= 320 líneas agregadas).
- **Compromiso Ético**: La IA sirvió de catalizador analítico; la autoría y responsabilidad técnica corresponden enteramente al estudiante.
