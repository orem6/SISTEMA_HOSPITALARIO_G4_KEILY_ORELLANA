# Catálogo de Requerimientos y Criterios de Aceptación (ASII-25)
**Estudiante:** Albino Sebastián Rosales Ruano (`codsebas`) | **Asignación Odoo:** #3974 | **Issue GitHub:** #13

## 1. Requerimientos Funcionales (RF-01 a RF-10)
| ID | Nombre | Descripción Técnica |
|---|---|---|
| **RF-01** | Disparador CI | Intercepta eventos `pull_request` hacia ramas protegidas (`develop`, `main`). |
| **RF-02** | Linteo de Código | Ejecuta Laravel Pint exigiendo 0 desviaciones estilísticas PSR-12. |
| **RF-03** | PostgreSQL Aislado | Instancia contenedor efímero PostgreSQL 16+ con esquemas `central` y `hospital`. |
| **RF-04** | Pruebas PHPUnit | Ejecuta suite unitaria/feature contra el arnés PostgreSQL aislado. |
| **RF-05** | Pruebas E2E | Ejecuta pruebas Playwright headless en flujos clínicos críticos. |
| **RF-06** | Quality Gates | Evalúa umbrales: cobertura >=80%, 0 fallos y 0 faltas de estilo. |
| **RF-07** | Evidencias Inmutables | Sella artefactos con hash SHA-256 vinculados al `commit SHA`. |
| **RF-08** | Bloqueo Estricto PR | Registra `failure` e inhabilita merge si falla cualquier compuerta. |
| **RF-09** | Dictamen Aprobación | Emite dictamen `APPROVED` requiriendo autorización previa a integración. |
| **RF-10** | Etiquetado SemVer | Aplica tag Git inmutable (`vMAJOR.MINOR.PATCH`) tras merge a `main`. |

## 2. Requerimientos No Funcionales (RNF-01 a RNF-06)
| ID | Categoría | Criterio de Medición |
|---|---|---|
| **RNF-01** | Determinismo | Entorno PostgreSQL efímero e idempotente sin estado residual. |
| **RNF-02** | Tiempo Pipeline | Duración total del pipeline inferior a 10 minutos. |
| **RNF-03** | Trazabilidad | Evidencias con hash SHA-256 archivadas con trazabilidad estricta. |
| **RNF-04** | Desacoplamiento DIP | Motor desacoplado de runners concretos mediante abstracciones. |
| **RNF-05** | Resiliencia | Almacenamiento de artefactos con reintentos exponenciales. |
| **RNF-06** | Custodia Secretos | Secretos y credenciales cifrados sin exposición en logs. |

## 3. Criterios de Aceptación (Gherkin BDD)
```gherkin
Característica: Evaluación y aprobación de Quality Gates en Pull Request

  Escenario: Ejecución satisfactoria de todas las compuertas de calidad
    Dado que un desarrollador envía un PR hacia la rama "develop"
    Cuando se ejecutan Laravel Pint, PHPUnit contra PostgreSQL y Playwright
    Entonces el motor de calidad emite dictamen "APPROVED" y almacena evidencias SHA-256

  Escenario: Bloqueo de PR por caída de umbral de cobertura o pruebas fallidas
    Dado que una prueba unitaria falla o la cobertura cae por debajo del 80%
    Cuando el QualityGateEngine evalúa las compuertas de calidad
    Entonces bloquea el Pull Request emitiendo veredicto "REJECTED"
```
