# ASII-15 — Alcance, Actores y Casos de Uso

## 1. Identificación de la Actividad
- **Estudiante**: Albino Sebastián Rosales Ruano | **GitHub**: `codsebas`
- **Módulo**: ASII-15: Prescripciones Electrónicas con Validación de Alergias | **Semana**: 1
- **Proceso de negocio**: Creación de prescripción médica con verificación previa de alergias
- **Tipo de evidencia**: Análisis, delimitación y modelado conceptual UML

## 2. Contexto Clínico del Repositorio HIS
| Componente Base | Estado en el HIS | Rol en ASII-15 |
|---|---|---|
| `allergies` | Implementado en esquema hospitalario | Fuente primaria de alérgenos, tipos y severidades del paciente |
| `care_episodes` | Implementado en esquema hospitalario | Vincula la prescripción al episodio clínico de atención activo |
| `doctor_profiles` | Implementado en esquema hospitalario | Valida que el usuario tenga acreditación médica para prescribir |
| `medications` | Catálogo hospitalario | Provee principios activos, códigos y formulaciones farmacológicas |
| `prescription_allergy_checks` | Propuesta de persistencia auditable | Bitácora de verificaciones, alertas y justificaciones de override |

## 3. Problema y Objetivo Clínico
La administración accidental de fármacos a pacientes alérgicos representa uno de los eventos adversos prevenibles más graves en un hospital. El objetivo de ASII-15 es modelar un mecanismo de validación proactiva y sincrónica que contraste el medicamento prescrito contra las alergias activas del paciente, alertando al médico y bloqueando la emisión indebida salvo override justificado.

## 4. Límite y Alcance
- **Límite**: Intercepción del flujo de prescripción clínica entre la selección de posología y la persistencia final.
- **Incluido**: Catálogo de casos de uso (UC-01 a UC-10), diagrama de casos de uso, diagrama de actividades con excepciones y diagrama de secuencia.
- **Fuera de alcance (Semana 1)**: Interfaces gráficas web finales, contratos OpenAPI, migraciones DDL ejecutables y código de controladores backend.

## 5. Actores
| Actor | Tipo | Responsabilidad Clínica |
|---|---|---|
| `Médico` | Primario clínico | Seleccionar paciente, indicar fármaco/posología y resolver alertas |
| `Farmacéutico` | Secundario clínico | Consultar prescripciones activas validadas para dispensación |
| `Enfermera` | Secundario clínico | Consultar prescripción autorizada para administración en sala |
| `Auditor Clínico` | Auditoría médica | Supervisar overrides justificados y cumplimiento de protocolos |
| `AllergyCheckService` | Servicio del dominio | Comparar principios activos contra historial alérgico activo |
| `AuditService` | Servicio de soporte | Registrar eventos inmutables en `prescription_allergy_checks` |

## 6. Reglas de Negocio
1. **RN-01 (Verificación Previa Obligatoria)**: Toda prescripción debe ser evaluada contra el historial alérgico del paciente antes de poder guardarse en estado activo.
2. **RN-02 (Bloqueo Preventivo)**: Si existe coincidencia con una alergia de severidad moderada, severa o mortal, el sistema suspende la emisión automática.
3. **RN-03 (Override con Justificación Obligatoria)**: El médico puede forzar la prescripción riesgosa únicamente si ingresa un motivo clínico explícito, el cual se audita con su firma/id.
4. **RN-04 (Advertencia por Falta de Historial)**: Si el paciente no posee registros en `allergies`, el sistema emite una advertencia de "Paciente no evaluado", requiriendo confirmación antes de emitir.

## 7. Catálogo de Casos de Uso
| ID | Caso de Uso | Actor Principal | Resumen y Excepción |
|---|---|---|---|
| UC-01 | Iniciar creación de prescripción médica | `Médico` | Abre el formulario clínico vinculado al episodio de atención. Ex: Episodio inactivo o cerrado. |
| UC-02 | Seleccionar medicamento y posología | `Médico` | Escoge fármaco, dosis, vía de administración y frecuencia. Ex: Dosis fuera de rango terapéutico. |
| UC-03 | Verificar alergias e interacciones | `AllergyCheckService` | Ejecuta motor de comparación contra alergias activas. Ex: Servicio temporalmente no disponible. |
| UC-04 | Notificar alerta de alergia coincidente | `UI_Prescripciones` | Muestra alerta modal con nivel de severidad y reacción conocida. Ex: Desconocimiento de alérgeno. |
| UC-05 | Anular o modificar prescripción por riesgo | `Médico` | Cancela el fármaco conflictivo o selecciona sustituto terapéutico. Ex: Falta de sustituto disponible. |
| UC-06 | Registrar justificación de override clínico | `Médico` | Ingresa justificación clínica obligatoria para forzar prescripción. Ex: Justificación vacía o ambigua. |
| UC-07 | Gestionar alerta de paciente no evaluado | `PrescriptionController` | Advierte falta de historial alérgico previo y solicita confirmación. Ex: Cancelación por el prescriptor. |
| UC-08 | Manejar excepción por fallo de servicio | `PrescriptionController` | Aplica política de fail-safe con contingencia auditada si cae el servicio. Ex: Interrupción total del sistema. |
| UC-09 | Emitir y persistir prescripción electrónica | `PrescriptionRepository` | Guarda la prescripción definitiva en la base de datos hospitalaria. Ex: Error de concurrencia o rollback. |
| UC-10 | Auditar verificación y overrides alérgicos | `AuditService` | Registra el resultado de la validación u override en bitácora inmutable. Ex: Fallo en almacenamiento de auditoría. |

## 8. Decisiones del Diagrama de Casos de Uso
- El diagrama formal se encuentra en [use-case-diagram.puml](diagrams/use-case-diagram.puml).
- `UC-01` a `UC-09` modelan el ciclo de vida de la prescripción bajo el actor `Médico`.
- `UC-03` y `UC-10` son incluidos (`<<include>>`) en el flujo de emisión para asegurar cumplimiento preventivo y auditoría clínica.
- `UC-06` extiende (`<<extend>>`) a `UC-04` ante la necesidad médica de realizar un override.
