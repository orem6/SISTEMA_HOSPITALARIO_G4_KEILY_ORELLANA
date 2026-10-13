# Guion de Defensa Oral — Prescripciones Electrónicas con Validación de Alergias

## Preguntas Frecuentes y Respuestas Técnicas

### 1. ¿Cuál es el propósito clínico y el alcance del módulo ASII-15 en el HIS?
**Respuesta**: Su objetivo es prevenir eventos adversos prevenibles por medicación mediante la verificación sincrónica obligatoria de contraindicaciones alérgicas antes de emitir una prescripción médica. Su alcance abarca la captura de posología, verificación cruzada contra alergias activas, emisión de alertas clínicas, gestión de anulaciones o overrides justificados, y registro inmutable en auditoría.

### 2. ¿Cómo interactúa el flujo con las entidades clínicas preexistentes del HIS?
**Respuesta**: Reutiliza sin duplicar las entidades existentes del DDL hospitalario: `local_user_refs` y `doctor_profiles` (identidad y credenciales del prescriptor), `care_episodes` (episodio clínico activo), `allergies` (registro histórico y activo de alérgenos y severidad) y el catálogo `medications`. Al validarse, la receta se registra en `prescriptions` con enlace a su episodio.

### 3. ¿En qué consiste el bloqueo preventivo ante la detección de una alergia activa coincidente?
**Respuesta**: Cuando `AllergyCheckService` detecta que el principio activo o compuesto del fármaco coincide con una alergia registrada (severidad moderada, severa o mortal), el sistema suspende de forma preventiva el flujo estándar de guardado y despliega una alerta visual crítica (`ACT-ALT-01`, `SEQ-ALT-01`), impidiendo que el fármaco se prescriba sin intervención explícita.

### 4. ¿Cuáles son los criterios clínicos para el "Override Justificado" y cómo se persiste en `prescription_allergy_checks`?
**Respuesta**: El médico puede requerir administrar el fármaco por necesidad vital (falta de alternativa terapéutica o protocolo de desensibilización). En ese caso, el sistema exige ingresar un motivo clínico justificado obligatorio (`ACT-OVR-02`, `SEQ-OVR-02`). La tupla `(prescription_id, allergy_id, doctor_id, justification, timestamp, override_flag)` se persiste de forma inmutable en la tabla de auditoría clínica `prescription_allergy_checks`.

### 5. ¿Cómo gestiona el sistema las excepciones de paciente no evaluado y caída del `AllergyCheckService`?
**Respuesta**: Si el paciente no posee alergias registradas (`ACT-CHK-03`), el sistema no bloquea pero emite una advertencia de "Paciente sin evaluación alérgica registrada", requiriendo confirmación médica. Si `AllergyCheckService` cae o no responde (`ACT-EX-01`, `SEQ-EX-01`), se aplica una política de fail-safe que bloquea la emisión silenciosa y activa un procedimiento de contingencia con auditoría obligatoria.

### 6. ¿Por qué la Semana 1 prioriza el modelado conceptual UML y cómo se garantiza la trazabilidad hacia la implementación?
**Respuesta**: Porque en sistemas hospitalarios críticos los errores de lógica provocan daño a pacientes; modelar formalmente los casos de uso (UC-01..10), actividades y secuencias asegura que todas las excepciones y decisiones estén blindadas antes de escribir código. La trazabilidad formal biunívoca garantiza que cada requerimiento tenga su método, servicio y prueba correspondiente a partir de la Semana 2.
