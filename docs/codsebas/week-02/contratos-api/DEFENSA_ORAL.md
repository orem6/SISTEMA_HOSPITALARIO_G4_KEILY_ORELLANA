# Guion de Defensa Oral — Requerimientos y Diseño SOLID ISP (ASII-24)

## Preguntas Críticas y Argumentación Técnica

### 1. ¿Cómo se relacionan los RF/RNF de la Semana 2 con los casos de uso definidos en la Semana 1?
**Respuesta**: Los 10 casos de uso de la Semana 1 (`UC-01` a `UC-10`) delimitaron el comportamiento general del centro documental. En la Semana 2, cada caso de uso se desglosa formalmente en requerimientos funcionales (`RF-01` a `RF-10`) con precondiciones, entradas, salidas y excepciones, respaldados por requerimientos no funcionales (`RNF-01` a `RNF-08`) que fijan restricciones de seguridad, aislamiento multi-tenant y rendimiento. Esta correspondencia biunívoca garantiza que no haya funcionalidades huérfanas ni omisiones en el alcance del sistema.

### 2. ¿Por qué es crítico el principio ISP en este módulo y qué problema resuelve la segregación de interfaces?
**Respuesta**: Según la teoría de la cátedra (MVP Cluster, Luis M. Rodríguez González) y Robert C. Martin, un cliente no debe depender de métodos que no utiliza. En el diseño previo, una interfaz monolítica obligaba al visor clínico a depender de métodos administrativos (`publishDocument`, `archiveDocument`). La segregación en `DocumentReaderInterface` y `DocumentAdminInterface` elimina dependencias innecesarias, respeta el principio de menor privilegio y asegura que cambios o adiciones en la administración no fuercen modificaciones ni revalidaciones en los clientes clínicos de solo lectura.

### 3. ¿Cómo se garantiza la seguridad y el aislamiento multi-tenant en los contratos de interfaz?
**Respuesta**: La seguridad se modela explícitamente a través de tipos y contextos en las firmas de los contratos (`TenantContext` y `RoleContext`). Todo método de lectura exige validar que el recurso pertenezca al `tenant_id` de la petición y que los roles del usuario intersecten con los roles aprobados del documento. Solicitudes de otros inquilinos o accesos a recursos inexistentes devuelven rechazos controlados sin confirmar la existencia del archivo, previniendo fuga de metadatos clínicos.

### 4. ¿Por qué se utilizó el formato Gherkin (Dado-Cuando-Entonces) para los criterios de aceptación?
**Respuesta**: El formato Gherkin (BDD) proporciona especificaciones ejecutables e inequívocas compartidas entre desarrolladores, analistas y evaluadores. Al formalizar precondiciones (**Dado** un usuario clínico con tenant válido), acciones (**Cuando** consulta la documentación) y aserciones (**Entonces** solo recibe documentos asignados a su rol), se crea una base directa para la automatización de pruebas de aceptación y se valida el cumplimiento de las reglas de negocio antes de codificar la persistencia.

### 5. ¿Qué implicaciones tiene el RNF-04 sobre RFC 7807 (Problem Details) en el diseño de contratos?
**Respuesta**: El estándar RFC 7807 (`application/problem+json`) unifica las respuestas ante errores y excepciones (`type`, `title`, `status`, `detail`, `instance`). Su adopción garantiza que denegaciones de acceso por rol, ausencias de tenant o fallos de validación documental se comuniquen de forma estructurada a las capas cliente sin exponer trazas de depuración, consultas SQL ni información sensible de la infraestructura hospitalaria.

### 6. ¿Por qué el código PHP 8.2+ se presenta como diseño de contratos desacoplados y no como controladores de Laravel?
**Respuesta**: En la fase de ingeniería de requerimientos y diseño de contratos (Semana 2), acoplarse prematuramente al framework o a controladores HTTP generaría dependencias accidentales y deuda técnica. Modelar contratos con PHP 8.2 vanilla, tipos estrictos y DTOs inmutables permite validar la arquitectura de objetos pura y el principio ISP de forma agnóstica, facilitando la creación de dobles de prueba (mocks) y una integración limpia en etapas posteriores.
