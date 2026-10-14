# Guion de Defensa Oral — Requerimientos Clínicos y Diseño SOLID DIP

## Preguntas Frecuentes y Respuestas Técnicas

### 1. ¿Cómo se alinean los RF-01 a RF-10 y RNF-01 a RNF-06 con la seguridad del paciente y los casos de uso de la Semana 1?
**Respuesta**: Los requerimientos traducen formalmente los casos de uso de la Semana 1 (`UC-01` a `UC-10`) a especificaciones técnicas verificables. `RF-01` a `RF-06` aseguran que ninguna prescripción sea emitida sin autenticar al prescriptor, aislar el tenant y contrastar el fármaco contra las alergias activas del paciente. Los requerimientos no funcionales (`RNF-01` a `RNF-06`) imponen restricciones críticas de latencia (< 500 ms en evaluación), integridad transaccional y disponibilidad con fail-safe, evitando que fallos de red deriven en prescripciones silenciosas o dañinas para el paciente.

### 2. ¿Por qué y cómo se aplica el Principio de Inversión de Dependencias (DIP) en `PrescriptionService`?
**Respuesta**: DIP establece que los módulos de alto nivel no deben depender de módulos de bajo nivel; ambos deben depender de abstracciones. En el diseño inicial ("Antes"), `PrescriptionService` instanciaba directamente clases concretas de PostgreSQL con el operador `new`. En el diseño refactorizado ("Después"), `PrescriptionService` recibe sus dependencias mediante inyección en el constructor a través de las interfaces `AllergyVerifierInterface` y `PrescriptionStorageInterface`. Esto desacopla las reglas de negocio clínicas del motor de persistencia relacional.

### 3. ¿Cómo impacta el uso de `AllergyVerifierInterface` y `PrescriptionStorageInterface` en la capacidad de pruebas automatizadas?
**Respuesta**: Elimina la dependencia de una base de datos PostgreSQL activa para ejecutar pruebas de negocio. Permite implementar un adaptador de pruebas en memoria (`InMemoryAllergyVerifierMock` e `InMemoryPrescriptionStorage`) con el cual es posible simular escenarios clínicos deterministas (pacientes con alergia severa a penicilinas, pacientes sin historial o fallos de conexión) en microsegundos dentro de la suite de CI/CD, sin efectos colaterales en bases de datos de desarrollo.

### 4. ¿Cómo garantiza la arquitectura el aislamiento multi-tenant y la trazabilidad clínica ante un override justificado?
**Respuesta**: Todo request HTTP exige el encabezado `X-Tenant-ID` (validado contra el token JWT en `RF-01` y `RNF-02`), inyectando el identificador del tenant en todas las consultas y escrituras para impedir filtración entre sedes hospitalarias. Cuando el médico decide forzar una prescripción riesgosa mediante un override clínico (`RF-05`), el sistema valida que el campo `override_reason` sea provisto y persiste de forma transaccional e inmutable la justificación en `prescription_allergy_checks` (`RF-06`), garantizando el no repudio y trazabilidad médico-legal.

### 5. ¿Cuál es el tratamiento del error y la estructura de respuesta cuando se detecta una contraindicación alérgica bloqueante (RFC 7807)?
**Respuesta**: El sistema rechaza la solicitud retornando un código HTTP 422 (Unprocessable Content) o 409 (Conflict) bajo la especificación RFC 7807 (`application/problem+json`). El cuerpo de la respuesta incluye el tipo de problema (`urn:his:allergy-contraindication`), título explicativo, severidad del alérgeno detectado (`severe` o `life_threatening`), principio activo en conflicto y enlace al protocolo de solicitud de override justificado, evitando respuestas ambiguas en la interfaz del prescriptor.

### 6. ¿Qué valor aporta la fundamentación de software de la cátedra (`mvpcluster.com/diseno-de-software-2/`) al diseño de sistemas de misión crítica médica?
**Respuesta**: La cátedra enfatiza que los principios SOLID no son meras recomendaciones estilísticas, sino salvaguardas de calidad que reducen la rigidez, fragilidad e inmovilidad del software. En un sistema clínico hospitalario de misión crítica, el desacoplamiento mediante DIP permite reemplazar o actualizar componentes de persistencia, integrar servicios externos de farmacovigilancia o incorporar motores de reglas clínicas sin riesgo de romper el flujo central de prescripción ni comprometer la vida de los pacientes.
