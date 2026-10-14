# Informe Arquitectónico — Centro de Documentación y Manuales por Rol

## 1. Delimitación y Justificación del Módulo
El presente informe formaliza el diseño arquitectónico de la Semana 1 para el módulo **ASII-24: Contratos API y Documentación Técnica**, modelado bajo la necesidad funcional de un **Centro de Documentación y Manuales Diferenciados por Rol** en el Hospital Information System (HIS).

### Separación de Responsabilidades
- **Capacidades Base Reutilizadas**: La infraestructura existente del repositorio provee autenticación JWT (`/api/v1/auth`), contexto organizacional mediante cabecera `X-Tenant-ID` resuelto por `TenantMiddleware`, y autorización por roles mediante Spatie Permission (`RoleSeeder`).
- **Límite Propuesto (ASII-24)**: El centro documental no rediseña la autenticación ni el multi-tenant; consume este contexto de forma estricta para garantizar que el personal clínico solo visualice documentación correspondiente a su tenant y perfil funcional.

## 2. Síntesis de Modelado UML
1. **Casos de Uso ([use-case-diagram.puml](docs/diagrams/use-case-diagram.puml))**:
   - Delimita 10 casos de uso (`UC-01` a `UC-10`).
   - `Admin` asume el control del ciclo de vida documental (publicar `UC-05`, versionar `UC-07`, archivar `UC-08`).
   - Los roles lectores (`Médico`, `Enfermera`, `TecnicoLab`, `Recepcionista`) heredan de `Usuario autenticado del HIS` para consulta (`UC-01..04`).
   - `UC-09` encapsula la verificación transversal del HIS (`<<include>>`).
2. **Flujo de Actividades ([activity-diagram.puml](docs/diagrams/activity-diagram.puml))**:
   - Ramifica tempranamente la validación de JWT (`ACT-ACC-01`) y Tenant (`ACT-ACC-02`), abortando en excepciones controladas (`ACT-EX-01`, `ACT-EX-02`).
   - Separa el ciclo administrativo (publicación con bucle de corrección `ACT-EX-04`, persistencia `ACT-PUB-06`) del ciclo de consulta filtrada (`ACT-CON-01..08`).
3. **Flujo de Secuencia ([sequence-diagram.puml](docs/diagrams/sequence-diagram.puml))**:
   - Establece 7 participantes desacoplados: `User`, `UI`, `API`, `Access`, `Service`, `DB` y `Storage`.
   - Modela la validación de tokens y tenants en cada interacción, garantizando respuesta uniforme ante fallos (`SEQ-EX-01..06`).

## 3. Decisiones Técnicas y de Diseño
- **Principio de Mínimo Privilegio**: Solo `Admin` tiene potestad de publicación y archivado; no se introdujeron roles ficticios ni permisos no soportados.
- **Aislamiento Multi-Tenant Estricto**: Todo acceso verifica que `document.tenant_id == request.tenant_id`. Las solicitudes cruzadas devuelven rechazo inmediato sin revelar existencia de archivos.
- **Fidelidad Semántica**: Se preservó el identificador de base de datos `TecnicoLab` mientras que la presentación a nivel de interfaz de usuario utiliza la denominación en lenguaje natural *Técnico de laboratorio*.
- **Evolución Modular**: Durante la Semana 1 se evitó deliberadamente crear endpoints HTTP, esquemas de BD o migraciones prematuras, asegurando que la arquitectura conceptual esté validada antes de codificar contratos y controladores en semanas posteriores.

## 4. Conclusión
El modelado garantiza una trazabilidad del 100% entre requisitos funcionales, flujos de control y mensajes de interacción, estableciendo una base robusta y desacoplada para la posterior especificación de contratos OpenAPI y pruebas en Semana 2.
