# Informe Técnico — Requerimientos Clínicos y Diseño SOLID DIP

## 1. Delimitación y Justificación del Módulo
El presente informe consolida la especificación formal y el diseño orientado a objetos de la Semana 2 para el módulo **ASII-15: Prescripciones Electrónicas con Validación de Alergias** (Issue #12 / Asignación Odoo #3944), en el marco del Sistema Hospitalario Integrado (HIS).
- **Problema Clínico**: La administración inadvertida de fármacos contraindicados por antecedentes alérgicos constituye una de las causas principales de morbimortalidad evitable en entornos hospitalarios.
- **Límites de ASII-15**: Intercepta la solicitud de prescripción médica antes de su persistencia definitiva, ejecutando una verificación sincrónica contra las alergias activas del paciente. Reutiliza las entidades preexistentes del HIS (`allergies`, `medications`, `prescriptions`, `care_episodes`, `doctor_profiles`) e incorpora la bitácora auditable `prescription_allergy_checks`.

## 2. Especificación de Requerimientos y Criterios de Aceptación
- **Requerimientos Funcionales (RF-01 a RF-10)**: Cubren el ciclo completo: autenticación JWT y tenant `X-Tenant-ID` (RF-01), consulta de alérgenos activos (RF-02), evaluación de reactividad cruzada (RF-03), bloqueo preventivo automático ante riesgo moderado/severo (RF-04), soporte de override clínico con justificación obligatoria (RF-05), registro inmutable en auditoría (RF-06), emisión de receta en estado ACTIVA (RF-07), consulta de prescripciones vigentes (RF-08), cancelación por el prescriptor (RF-09) y notificación a farmacia y enfermería (RF-10).
- **Requerimientos No Funcionales (RNF-01 a RNF-06)**: Garantizan seguridad e integridad RBAC (RNF-01), particionamiento multi-tenant estricto (RNF-02), latencia de evaluación p95 < 500 ms (RNF-03), respuestas estructuradas bajo RFC 7807 (RNF-04), desacoplamiento arquitectónico mediante DIP (RNF-05) y disponibilidad de 99.9% con contingencia fail-safe (RNF-06).
- **Criterios de Aceptación**: Formalizados en Gherkin (`Dado-Cuando-Entonces`) para cuatro escenarios clínicos críticos: camino feliz sin alergias, bloqueo preventivo por alergia severa, manejo de paciente no evaluado previamente y excepción clínica con override justificado y auditado.

## 3. Análisis Técnico del Principio SOLID DIP (Dependency Inversion Principle)
Basado en los lineamientos de diseño de software de la cátedra (`https://mvpcluster.com/diseno-de-software-2/`):
- **Antipatrón Detectado (Diseño Antes)**: La clase de dominio `PrescriptionService` instancia de forma directa repositorios de bajo nivel (`new PostgreSqlAllergyRepository()`, `new PostgreSqlPrescriptionRepository()`). Este acoplamiento rígido impide ejecutar pruebas unitarias automatizadas sin una base de datos PostgreSQL activa, viola el principio Abierto/Cerrado (OCP) y traslada fragilidades de infraestructura al núcleo de la lógica médica.
- **Diseño Refactorizado (Diseño Después)**: `PrescriptionService` depende exclusivamente de interfaces abstractas (`AllergyVerifierInterface` y `PrescriptionStorageInterface`), suministradas mediante inyección de dependencias por constructor. Esta separación permite desacoplar la regla de negocio de la tecnología de persistencia y utilizar implementaciones de prueba deterministas (`InMemoryAllergyVerifierMock`, `InMemoryPrescriptionStorage`) que se ejecutan en microsegundos sin red ni base de datos.

## 4. Decisiones de Arquitectura y Seguridad Clínica
1. **Aislamiento Multi-Tenant**: Inclusión y validación del header `X-Tenant-ID` en toda operación para evitar filtración cruzada de datos clínicos entre entidades hospitalarias.
2. **Estandarización de Errores RFC 7807**: Cuando ocurre una contraindicación alérgica, el sistema responde con `application/problem+json`, detallando el tipo de alerta, severidad del alérgeno y código clínico, facilitando el manejo determinista en el cliente.
3. **Auditoría Inmutable de Seguridad**: Ningún fármaco con alerta de alergia puede emitirse sin dejar una traza inmutable en `prescription_allergy_checks`, registrando la tupla `(prescription_id, allergy_id, doctor_id, justification, timestamp, override_flag)`.

## 5. Conclusión
La aplicación rigurosa de los requerimientos clínicos y del principio DIP asegura que el módulo ASII-15 opere como una barrera de contención robusta, desacoplada, auditable y fácilmente comprobable mediante pruebas automatizadas.
