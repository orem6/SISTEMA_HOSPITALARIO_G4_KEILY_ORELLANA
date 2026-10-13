# ASII-15 — Actividad, Secuencia y Trazabilidad

## 1. Propósito y Alcance
Este documento formaliza el comportamiento dinámico del proceso de creación de prescripciones médicas con validación de alergias (ASII-15), detallando las bifurcaciones de control, la mensajería entre participantes del dominio y la matriz de trazabilidad cruzada con los casos de uso.

## 2. Flujo de Actividad y Decisiones Clínicas
El diagrama completo está disponible en [activity-diagram.puml](diagrams/activity-diagram.puml).
- **Preparación y Captura**: `ACT-PRE-01` inicia el contexto de prescripción médica; `ACT-PRE-02` captura el paciente, episodio de atención, fármaco y posología.
- **Verificación de Alergias**: `ACT-CHK-01` invoca el motor de comprobación; `ACT-CHK-02` inspecciona registros en el repositorio alérgico; `ACT-CHK-03` evalúa si el paciente cuenta con historial alérgico previo.
- **Detección y Alerta de Riesgo**: `ACT-ALT-01` dispara la alerta visual de contraindicación alérgica ante coincidencia activa.
- **Decisión Médica (Override vs Anulación)**:
  - `ACT-OVR-01` anula el fármaco o solicita selección de sustituto terapéutico ante riesgo no asumible.
  - `ACT-OVR-02` captura el motivo clínico justificado obligatorio de override cuando el beneficio supera el riesgo.
- **Persistencia y Confirmación**: `ACT-SAV-01` almacena la prescripción autorizada; `ACT-SAV-02` confirma la emisión al facultativo.
- **Auditoría Clínica**: `ACT-AUD-01` registra la traza inmutable del resultado o justificación de override en `prescription_allergy_checks`.
- **Excepciones de Control**: `ACT-EX-01` gestiona la caída del servicio de alergias; `ACT-EX-02` habilita protocolo de contingencia auditada; `ACT-EX-03` interrumpe la prescripción ante cancelación médica.

## 3. Secuencia y Participantes
El diagrama de secuencia se encuentra en [sequence-diagram.puml](diagrams/sequence-diagram.puml).
- **Participantes**: `Médico` (facultativo), `UI_Prescripciones` (interfaz clínica), `PrescriptionController` (orquestador), `AllergyCheckService` (reglas de contraindicación), `AllergyRepository` (persistencia de alergias), `PrescriptionRepository` (persistencia de recetas) y `AuditService` (bitácora clínica).
- **Mensajería**:
  - Solicitud: `SEQ-PRE-01` envía solicitud desde UI; `SEQ-PRE-02` transfiere datos al controlador.
  - Verificación: `SEQ-CHK-01` invoca servicio; `SEQ-CHK-02` consulta repositorio; `SEQ-CHK-03` entrega alergias activas; `SEQ-CHK-04` reporta estado alérgico.
  - Alerta: `SEQ-ALT-01` presenta alerta modal al médico; `SEQ-ALT-02` expone severidad y reacción conocida.
  - Resolución: `SEQ-OVR-01` solicita anulación o justificación; `SEQ-OVR-02` remite justificación de override; `SEQ-OVR-03` valida justificación.
  - Persistencia: `SEQ-SAV-01` ordena guardado; `SEQ-SAV-02` registra en base de datos; `SEQ-SAV-03` retorna confirmación de prescripción emitida.
  - Auditoría: `SEQ-AUD-01` registra evento en `prescription_allergy_checks`; `SEQ-AUD-02` confirma persistencia de auditoría.
  - Excepciones: `SEQ-EX-01` captura indisponibilidad del servicio; `SEQ-EX-02` emite advertencia por paciente no evaluado; `SEQ-EX-03` finaliza por anulación médica.

## 4. Matriz de Trazabilidad Cruzada
| Caso de Uso | Actor Principal | Actividades Relacionadas | Mensajes de Secuencia | Resultado Trazable |
|---|---|---|---|---|
| UC-01 Iniciar prescripción | `Médico` | `ACT-PRE-01` | `SEQ-PRE-01` | Formulario clínico inicializado |
| UC-02 Seleccionar fármaco | `Médico` | `ACT-PRE-02` | `SEQ-PRE-02` | Fármaco y posología capturados |
| UC-03 Verificar alergias | `AllergyCheckService` | `ACT-CHK-01..02` | `SEQ-CHK-01..04` | Evaluación de reactividad cruzada |
| UC-04 Notificar alerta | `UI_Prescripciones` | `ACT-ALT-01` | `SEQ-ALT-01..02` | Notificación visual de riesgo |
| UC-05 Anular o modificar | `Médico` | `ACT-OVR-01`, `ACT-EX-03` | `SEQ-OVR-01`, `SEQ-EX-03` | Prescripción cancelada o sustituida |
| UC-06 Override justificado | `Médico` | `ACT-OVR-02` | `SEQ-OVR-02..03` | Justificación registrada para auditoría |
| UC-07 Paciente no evaluado | `PrescriptionController` | `ACT-CHK-03` | `SEQ-EX-02` | Advertencia y confirmación facultativa |
| UC-08 Excepción de servicio | `PrescriptionController` | `ACT-EX-01..02` | `SEQ-EX-01` | Fail-safe activado y contingencia |
| UC-09 Emitir prescripción | `PrescriptionRepository` | `ACT-SAV-01..02` | `SEQ-SAV-01..03` | Prescripción activa persistida |
| UC-10 Auditar verificación | `AuditService` | `ACT-AUD-01` | `SEQ-AUD-01..02` | Registro en prescription_allergy_checks |

## 5. Verificación de Consistencia e Invariantes
1. **Unicidad de Identificadores**: Todo caso de uso (`UC-01` a `UC-10`) cuenta con nodos activos correspondientes en los modelos visuales.
2. **Invariante de Seguridad Clínica**: Ninguna prescripción alcanza `ACT-SAV-01` o `SEQ-SAV-01` sin haber atravesado verificación exitosa o un override con motivo explícito registrado en `prescription_allergy_checks`.
3. **Resiliencia Operativa**: Las fallas externas se canalizan mediante excepciones controladas (`ACT-EX-*`, `SEQ-EX-*`) previniendo estados inconsistentes o prescripciones no auditadas.
