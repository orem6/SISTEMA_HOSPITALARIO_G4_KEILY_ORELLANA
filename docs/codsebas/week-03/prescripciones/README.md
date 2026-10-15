# ASII-15 — Arquitectura de Software, Vistas y Patrones (Semana 3)

## Resumen Ejecutivo y Alcance
Entrega de diseño arquitectónico y patrones para el módulo **ASII-15: Prescripciones Electrónicas con Validación de Alergias** (Semana 3 / Asignación Odoo #3945 / Issue GitHub #19) en el Sistema Hospitalario Integrado (HIS).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`) | **Rama**: `feature/asii-15-codsebas-week-3`
- **Fuente teórica**: Cátedra de Diseño de Software (`https://mvpcluster.com/diseno-de-software-2/`)

## Vistas Arquitectónicas y Patrones Aplicados
- **Vista Lógica**: Separación en capas (Dominio, Aplicación, Infraestructura) con validación desacoplada.
- **Vista de Componentes y Despliegue**: Micro-HIS empaquetado como servicio web con PostgreSQL, Redis y bus de eventos.
- **Aislamiento Multi-Tenant**: Control perimetral estricto mediante cabecera `X-Tenant-ID` en API Gateway y repositorios.
- **Strategy Pattern**: Familia de algoritmos intercambiables (`DirectAllergyRuleStrategy`, `CrossReactivityStrategy`).
- **Observer Pattern**: Emisión de eventos clínicos (`PrescriptionIssuedEvent`, `AllergyAlertTriggeredEvent`) para auditoría y alertas.
- **Repository Pattern**: Persistencia clínica desacoplada mediante `PrescriptionRepositoryInterface` y `AllergyRepositoryInterface`.

## Matriz de Trazabilidad Arquitectónica
| Requerimiento Clínico | Vista Arquitectónica | Patrón de Diseño | Componente / Clase |
|---|---|---|---|
| RF-01 Aislamiento de Sede | Despliegue / Lógica | Multi-Tenant Header | `TenantContextMiddleware` / `X-Tenant-ID` |
| RF-02/03 Evaluación Alergias | Lógica / Componentes | Strategy Pattern | `DirectAllergyRuleStrategy`, `CrossReactivityStrategy` |
| RF-04/05 Bloqueo y Override | Lógica de Negocio | Observer / Domain Events | `AllergyAlertTriggeredEvent`, `PrescriptionService` |
| RF-06 Auditoría Inmutable | Componentes | Observer Pattern | `AuditLogSubscriber` |
| RF-07 Emisión de Receta | Lógica / Despliegue | Observer Pattern | `PrescriptionIssuedEvent`, `PharmacyNotificationSubscriber` |
| RF-08 Persistencia Clínica | Lógica / Infraestructura | Repository Pattern | `PrescriptionRepositoryInterface`, `AllergyRepositoryInterface` |

## Instrucciones de Validación
Ejecutar el validador automatizado (suite de tests unitarios, estándares y presupuesto <= 400 líneas):
```bash
php docs/codsebas/week-03/prescripciones/scripts/run-validation.php
```

## Índice de Artefactos
- [Informe Técnico](INFORME.md) | [Defensa Oral](DEFENSA_ORAL.md) | [Declaración IA](DECLARACION_IA.md)
- [01. Vistas Arquitectónicas](docs/01-architectural-views.md) | [02. Patrones de Diseño](docs/02-design-patterns.md)
- [Script Validador](scripts/run-validation.php)
