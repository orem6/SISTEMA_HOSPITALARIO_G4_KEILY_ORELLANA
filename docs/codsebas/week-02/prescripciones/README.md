# ASII-15 — Requerimientos Clínicos y Diseño SOLID DIP (Semana 2)

## Resumen Ejecutivo
Entrega técnica de especificación formal y diseño arquitectónico para el módulo **ASII-15: Prescripciones Electrónicas con Validación de Alergias** (Semana 2 / Asignación Odoo #3944 / Issue GitHub #12), en el Sistema Hospitalario Integrado (HIS).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-15-codsebas-week-2`
- **Fuente teórica**: Principios SOLID (`https://mvpcluster.com/diseno-de-software-2/`)

## Catálogo de Requerimientos (Semana 2)
- **RF-01**: Validación de contexto e identidad JWT / `X-Tenant-ID`.
- **RF-02**: Consulta de alergias activas del paciente.
- **RF-03**: Detección de contraindicación medicamentosa y reactividad cruzada.
- **RF-04**: Bloqueo preventivo de prescripción.
- **RF-05**: Soporte de override clínico justificado (`override_reason`).
- **RF-06**: Registro en auditoría inmutable (`prescription_allergy_checks`).
- **RF-07**: Emisión de prescripción médica en estado ACTIVA.
- **RF-08**: Consulta de prescripciones vigentes por paciente o episodio.
- **RF-09**: Cancelación de prescripción con justificación médica.
- **RF-10**: Notificación a farmacia y enfermería ante emisión o cancelación.
- **RNF-01**: Seguridad clínica e integridad RBAC.
- **RNF-02**: Aislamiento multi-tenant estricto por `X-Tenant-ID`.
- **RNF-03**: Tiempo de respuesta en evaluación de alergias p95 < 500 ms.
- **RNF-04**: Trazabilidad y no repudio bajo estándar RFC 7807.
- **RNF-05**: Desacoplamiento de persistencia mediante SOLID DIP.
- **RNF-06**: Disponibilidad del servicio >= 99.9% con fail-safe.

## Matriz de Trazabilidad Cruzada
| Caso de Uso (Sem 1) | Requerimiento (Sem 2) | RNF | Componente DIP |
|---|---|---|---|
| UC-01 Iniciar prescripción | RF-01 Validación contexto | RNF-01, RNF-02 | `PrescriptionService` |
| UC-03 Verificar alergias | RF-02, RF-03 Consulta y detección | RNF-03, RNF-05 | `AllergyVerifierInterface` |
| UC-04 Notificar alerta | RF-04 Bloqueo preventivo | RNF-04, RNF-06 | `VerificationResult` |
| UC-06 Override justificado | RF-05 Override justificado | RNF-01, RNF-04 | `PrescriptionService::createPrescription` |
| UC-09 Emitir prescripción | RF-07, RF-08 Emisión y consulta | RNF-02, RNF-05 | `PrescriptionStorageInterface` |
| UC-10 Auditar verificación | RF-06 Auditoría inmutable | RNF-01, RNF-04 | `auditCheck` |

## Instrucciones de Validación
Ejecutar validador automatizado (límite estricto <= 400 líneas):
```bash
php scripts/run-validation.php
```
- [Informe Técnico](INFORME.md) | [Defensa Oral](DEFENSA_ORAL.md) | [Declaración IA](DECLARACION_IA.md)
- [01. Requerimientos](docs/01-requirements-and-acceptance-criteria.md) | [02. SOLID DIP](docs/02-solid-dependency-inversion.md)
