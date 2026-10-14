---
name: qa-ci-quality-gates
description: Directrices de aseguramiento de calidad (QA), pipelines de CI/CD, harness de pruebas PostgreSQL federado, suites E2E y evaluación de Quality Gates para el Sistema Hospitalario Integrado ASII 2026 G4. Úsala al escribir tests, pipelines de GitHub Actions o preparar releases.
---

# QA, Pruebas E2E, CI y Gestión de Versiones (ASII-25)

Directrices para el marco de calidad, integración continua y compuertas de calidad (*Quality Gates*) del Sistema Hospitalario Integrado (SHI 2026). Garantiza la detección temprana de regresiones y la inmutabilidad de la evidencia de pruebas.

## 1. Pipeline de Integración Continua (CI)
Toda integración hacia `develop` debe cumplir el flujo secuencial de validación:
1. **Análisis Estático (Lint):** Formateo estricto con `vendor/bin/pint --test` y análisis estático con `phpstan`.
2. **Pruebas de Integridad de BD:** Ejecución de suites en `tests/Database/` verificando constraints, triggers y aislamiento de esquemas.
3. **Pruebas Unitarias y de Feature:** `php artisan test` cubriendo controladores, servicios de dominio y políticas de acceso.
4. **Pruebas End-to-End (E2E):** Suites automatizadas con Playwright cubriendo flujos clínicos transversales.
5. **Evaluación de Quality Gates:** Aprobación automatizada de umbrales antes de autorizar el merge.

## 2. Harness de Pruebas con PostgreSQL Federado
- **Cero SQLite / Cero MySQL:** Los tests corren obligatoriamente contra instancias reales de PostgreSQL 15+ con `btree_gist`.
- **Bases desechables de prueba:** `his_central_test` (puerto 55431) y `his_hospital_test` (puerto 55433), aisladas en Docker.
- **Rollback controlado:** Toda migración debe ser reversible mediante `down()` probado en suite de migración/reversión completa.

## 3. Pruebas Funcionales y E2E Críticas
Flujos clínicos que requieren validación E2E obligatoria:
- Autenticación multitenant con JWT y cambio de sesión.
- Admisión hospitalaria, ocupación de cama y alta médica.
- Prescripción médica con alerta y bloqueo de alergias conocidas.
- Solicitud, toma de muestra y validación de resultados de laboratorio.

## 4. Políticas de Quality Gates (Criterios de Aceptación)
Ningún Pull Request podrá integrarse si no cumple con:
- **Zero Errors de Lint:** 100% de archivos en conformidad con Pint.
- **Zero Regresiones de Base de Datos:** Todas las pruebas de integridad en verde sin excepciones.
- **Presupuesto de Líneas:** Todo PR debe mantenerse en $\le 800$ líneas agregadas (regla estricta del bot *ASII PR Guard*).
- **Cobertura de Código:** Cobertura de pruebas $\ge 80\%$ en lógica de servicios de dominio (`app/Services/`).

## 5. Gestión de Evidencia y Versionado Semántico
- **Evidencia inmutable:** Almacenamiento de reportes JUnit, logs de ejecución y digest SHA-256 en almacenamiento seguro.
- **Versionado Semántico (SemVer):** Etiquetas de release inmutables `vMAJOR.MINOR.PATCH` asociadas al commit integrado en `main`.
