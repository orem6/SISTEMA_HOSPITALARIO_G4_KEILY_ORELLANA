# Guion de Defensa Oral — Centro de Documentación y Manuales por Rol

## Preguntas Frecuentes y Respuestas Técnicas

### 1. ¿Cómo se relaciona este módulo con la asignación oficial de "Contratos API"?
**Respuesta**: El módulo oficial es *Contratos API: OpenAPI/Postman y documentación técnica*. Para la Semana 1, se adaptó académicamente al caso de negocio *Publicación y consulta de documentación y manuales por rol*. Esto permite modelar procesos reales del HIS (roles clínicos y administrativos) manteniendo el enfoque en documentación técnica y contratos que se formalizarán en semanas posteriores.

### 2. ¿Qué componentes ya existen en el repositorio y cuáles son propuestos?
**Respuesta**: En el repositorio base ya existen la autenticación JWT (`/api/v1/auth`), el middleware de inquilinos (`TenantMiddleware` con cabecera `X-Tenant-ID`) y los roles semilla de Spatie (`Admin`, `Médico`, `Enfermera`, `TecnicoLab`, `Recepcionista`). Lo propuesto para ASII-24 es la lógica documental: publicación, asignación de visibilidad, versionado, archivado y filtrado por rol y tenant.

### 3. ¿Por qué solo el rol `Admin` administra documentos y no se creó un nuevo rol?
**Respuesta**: Siguiendo el principio de diseño conservador y los roles existentes del HIS, no se introdujeron roles ficticios como "GestorDocumental". `Admin` es el único actor autorizado para publicar, versionar y archivar documentos dentro del tenant activo, preservando la coherencia con los seeders existentes.

### 4. ¿Cómo se garantiza el aislamiento multi-tenant?
**Respuesta**: Toda solicitud requiere la cabecera `X-Tenant-ID`. El middleware valida que el usuario pertenezca a dicho tenant. En la lógica documental, cada consulta o descarga verifica que `documento.tenant_id == tenant_activo`. Las solicitudes cruzadas son rechazadas inmediatamente (`ACT-EX-02`, `SEQ-EX-02`), evitando fuga de información entre clínicas o sedes.

### 5. ¿Cómo se demuestra la trazabilidad entre diagramas?
**Respuesta**: Cada caso de uso (`UC-01` a `UC-10`) cuenta con un mapeo biunívoco a nodos de actividad (`ACT-*`) y mensajes de secuencia (`SEQ-*`). Por ejemplo, la publicación (`UC-05`) involucra las actividades `ACT-PUB-01..07` y los mensajes `SEQ-PUB-01..06`, cubriendo tanto el camino feliz como el bucle de validación (`ACT-EX-04`) y fallos de persistencia (`SEQ-EX-06`).

### 6. ¿Por qué no se implementaron controladores, rutas ni migraciones de base de datos?
**Respuesta**: La Semana 1 se enfoca estrictamente en análisis, delimitación y modelado conceptual UML. Escribir código prematuro de backend sin haber validado el flujo de negocio y los casos de uso violaría las fases de ingeniería de software. Las especificaciones OpenAPI y la implementación se desarrollarán a partir de la Semana 2.
