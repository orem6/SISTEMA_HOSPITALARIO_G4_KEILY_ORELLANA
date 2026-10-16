# Guion de Defensa Oral — Arquitectura y Patrones Clínicos (Semana 3)

### 1. ¿Cómo se distribuyen las responsabilidades en la Vista Lógica del módulo ASII-15?
**R**: La Vista Lógica separa Dominio (entidades `Prescription`, estrategias `AllergyEvaluationStrategyInterface` y eventos), Aplicación (`PrescriptionService` como orquestador del flujo clínico) e Infraestructura (repositorios y adaptadores externos). Esta separación impide que detalles de base de datos o protocolos HTTP contaminen las decisiones de seguridad farmacológica.

### 2. ¿Cómo garantiza la arquitectura el aislamiento multi-tenant en despliegue con `X-Tenant-ID`?
**R**: El header `X-Tenant-ID` es verificado perimetralmente en el API Gateway y cargado en un `TenantContext` inmutable. Los repositorios inyectan este discriminador en cada consulta y mutación SQL, garantizando que un hospital no acceda ni modifique expedientes, alergias o recetas de otra institución en bases de datos compartidas.

### 3. ¿Por qué se utilizó el patrón Strategy para la validación de alergias y qué estrategias existen?
**R**: Evita sentencias condicionales complejas (`if/switch`) propensas a errores médicos y respeta el principio OCP. Implementamos `DirectAllergyRuleStrategy` para coincidencias exactas de fármaco y `CrossReactivityStrategy` para familias con reactividad cruzada (p. ej., penicilinas con cefalosporinas), permitiendo incorporar nuevas reglas sin modificar el servicio clínico.

### 4. ¿Qué papel cumple el patrón Observer ante eventos de prescripción y alertas de alergia?
**R**: Desacopla la lógica de negocio de los efectos secundarios colaterales. Cuando ocurre un bloqueo o override se publica `AllergyAlertTriggeredEvent` para auditoría médico-legal inmutable; al emitirse la receta se dispara `PrescriptionIssuedEvent` para alertar en tiempo real a farmacia y enfermería sin acoplar sus canales de comunicación al flujo transaccional.

### 5. ¿Cómo contribuye el patrón Repository a la calidad y testabilidad del Micro-HIS?
**R**: Define contratos abstractos (`PrescriptionRepositoryInterface`, `AllergyRepositoryInterface`) que encapsulan la persistencia. Permite sustituir la base de datos relacional por repositorios en memoria durante pruebas unitarias, ejecutando verificaciones clínicas deterministas en milisegundos sin levantar infraestructura externa.

### 6. ¿Cómo respalda la teoría de la cátedra (`mvpcluster.com/diseno-de-software-2/`) la robustez asistencial?
**R**: La cátedra demuestra que el diseño desacoplado mitiga la rigidez y fragilidad del código. En salud digital, la independencia de componentes y la extensibilidad mediante patrones garantizan que la evolución del sistema no introduzca fallos que comprometan la vida de los pacientes.
