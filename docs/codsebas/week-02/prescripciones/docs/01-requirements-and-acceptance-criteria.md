# ASII-15 — Requerimientos y Criterios de Aceptación (Semana 2)
**Estudiante:** Albino Sebastián Rosales Ruano (`codsebas`) | **Asignación Odoo:** #3944 | **Issue GitHub:** #12

## 1. Requerimientos Funcionales (RF-01 a RF-10)
| ID | Nombre | Descripción Clínica |
|---|---|---|
| **RF-01** | Validación Contexto | Autentica JWT prescriptor y presencia obligatoria de cabecera `X-Tenant-ID`. |
| **RF-02** | Consulta Alergias | Recupera historial sincrónico de alérgenos activos del paciente en `allergies`. |
| **RF-03** | Detección Contraindicación | Contrasta principio activo y familia farmacológica evaluando reactividad cruzada. |
| **RF-04** | Bloqueo Preventivo | Bloquea emisión de receta ante contraindicación moderada, severa o mortal. |
| **RF-05** | Override Justificado | Permite override médico facultativo con parámetro `override_reason` (>=15 chars). |
| **RF-06** | Registro Auditoría | Persiste transaccionalmente el veredicto clínico en `prescription_allergy_checks`. |
| **RF-07** | Emisión Prescripción | Emite receta médica en estado `ACTIVE` con identificador UUID y posología. |
| **RF-08** | Consulta Vigentes | Permite a clínicos autorizados listar prescripciones vigentes del paciente. |
| **RF-09** | Cancelación Prescripción | Faculta suspensión médica con motivo clínico (`cancellation_reason`). |
| **RF-10** | Notificación Farmacia | Dispara eventos de dominio hacia farmacia dispensadora y enfermería en piso. |

## 2. Requerimientos No Funcionales (RNF-01 a RNF-06)
| ID | Categoría | Criterio Técnico |
|---|---|---|
| **RNF-01** | Seguridad y RBAC | Control de acceso por rol médico y firma digital en JWT. |
| **RNF-02** | Aislamiento Tenant | Particionamiento estricto por `X-Tenant-ID` en persistencia y lectura. |
| **RNF-03** | Tiempo de Respuesta | Verificación de contraindicaciones con latencia p95 < 500 ms. |
| **RNF-04** | Trazabilidad RFC 7807 | Respuestas de error estructuradas bajo `application/problem+json`. |
| **RNF-05** | Desacoplamiento DIP | Servicio de negocio acoplado a abstracciones sin dependencia de SQL concreto. |
| **RNF-06** | Disponibilidad | Operatividad >= 99.9% con protocolo fail-safe ante fallos de conectividad. |

## 3. Criterios de Aceptación (Gherkin)
```gherkin
Característica: Emisión exitosa de prescripción sin alergias (CA-01)
  Dado que el médico "DR-101" está autenticado con JWT y tenant "hospital-central"
  Cuando el médico envía prescripción para paciente sin contraindicaciones alérgicas
  Entonces emite la prescripción médica con estado "ACTIVE" y HTTP 201 en "prescription_allergy_checks"

Característica: Bloqueo preventivo ante alergia severa (CA-02)
  Dado que el paciente tiene alergia severa a penicilinas y no se provee "override_reason"
  Cuando el sistema ejecuta la verificación de contraindicaciones
  Entonces suspende la emisión, no guarda la receta y responde HTTP 422 bajo RFC 7807

Característica: Advertencia preventiva ante paciente sin historial alérgico (CA-03)
  Dado que el paciente no posee registros previos en "allergies"
  Cuando el médico intenta prescribir un medicamento
  Entonces retorna advertencia "PatientAllergyHistoryUnassessed" requiriendo confirmación explícita

Característica: Override clínico justificado ante bloqueo por alergia (CA-04)
  Dado que el sistema detectó contraindicación y el médico envía "override_reason" válido
  Cuando el médico confirma la prescripción
  Entonces autoriza receta en estado "ACTIVE", audita la justificación y notifica a farmacia
```
