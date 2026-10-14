# ASII-15 — Prescripciones Electrónicas con Validación de Alergias

## Resumen Ejecutivo
Entrega de análisis y diseño arquitectónico (Semana 1) para el módulo **ASII-15: Prescripciones Electrónicas con Validación de Alergias**, modelando el proceso de negocio «creación de prescripción con verificación previa de alergias» en el Sistema Hospitalario Integrado (HIS).
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Rama activa**: `feature/asii-15-codsebas-week-1`

## Alcance del Módulo
- **Incluido**: Catálogo de casos de uso (UC-01 a UC-10), diagrama de casos de uso, diagrama de actividades con decisiones clínicas y excepciones (`ACT-*`), diagrama de secuencia con participantes desacoplados (`SEQ-*`), y matriz de trazabilidad cruzada formal.
- **Límites y Fuera de Alcance**: Reutilización estricta de las entidades clínicas base (`allergies`, `medications`, `prescriptions`, `care_episodes`). En Semana 1 no se incluyen endpoints HTTP, migraciones ni código ejecutable de backend/frontend.

## Actores y Componentes del Sistema
- **Clínico Primario**: `Médico` (facultativo que prescribe fármacos al paciente).
- **Interesados Secundarios**: `Farmacéutico` (dispensación), `Enfermera` (administración) y `Auditor Clínico` (revisión de overrides).
- **Componentes y Servicios**: `UI_Prescripciones`, `PrescriptionController`, `AllergyCheckService`, `AllergyRepository`, `PrescriptionRepository` y `AuditService`.

## Matriz de Trazabilidad Resumida
| Caso de Uso | Actor Principal | Actividad | Secuencia | Resultado Trazable |
|---|---|---|---|---|
| UC-01 Iniciar prescripción | `Médico` | `ACT-PRE-01` | `SEQ-PRE-01` | Contexto de prescripción abierto |
| UC-02 Seleccionar fármaco | `Médico` | `ACT-PRE-02` | `SEQ-PRE-02` | Medicamento y posología definidos |
| UC-03 Verificar alergias | `AllergyCheckService` | `ACT-CHK-01..02` | `SEQ-CHK-01..04` | Evaluación de reactividad cruzada |
| UC-04 Notificar alerta | `UI_Prescripciones` | `ACT-ALT-01` | `SEQ-ALT-01..02` | Alerta visual de contraindicación |
| UC-05 Anular o modificar | `Médico` | `ACT-OVR-01` | `SEQ-OVR-01` | Fármaco descartado o sustituido |
| UC-06 Override justificado | `Médico` | `ACT-OVR-02` | `SEQ-OVR-02..03` | Justificación clínica registrada |
| UC-07 Paciente no evaluado | `PrescriptionController` | `ACT-CHK-03` | `SEQ-EX-02` | Advertencia y confirmación médica |
| UC-08 Excepción de servicio | `PrescriptionController` | `ACT-EX-01..02` | `SEQ-EX-01` | Bloqueo o contingencia auditada |
| UC-09 Emitir prescripción | `PrescriptionRepository` | `ACT-SAV-01..02` | `SEQ-SAV-01..03` | Prescripción guardada y activa |
| UC-10 Auditar verificación | `AuditService` | `ACT-AUD-01` | `SEQ-AUD-01..02` | Registro en prescription_allergy_checks |

## Instrucciones de Validación
Para verificar la integridad de todos los artefactos, sintaxis PlantUML y trazabilidad:
```bash
php scripts/run-validation.php
```

## Estructura de Entregables
- [Informe Arquitectónico y Técnico](INFORME.md)
- [Guion de Defensa Oral](DEFENSA_ORAL.md)
- [Declaración de Asistencia de IA](DECLARACION_IA.md)
- [01. Alcance, Actores y Casos de Uso](docs/01-scope-actors-use-cases.md)
- [02. Actividad, Secuencia y Trazabilidad](docs/02-activity-sequence-traceability.md)
- [Diagramas PlantUML](docs/diagrams/)
