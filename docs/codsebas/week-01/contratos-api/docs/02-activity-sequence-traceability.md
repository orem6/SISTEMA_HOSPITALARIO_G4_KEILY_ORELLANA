# ASII-24 — Actividad, Secuencia y Trazabilidad

## 1. Propósito y Alcance
Este documento formaliza el comportamiento dinámico del Centro de Documentación y Manuales por Rol (ASII-24), detallando los flujos de control, validaciones entre participantes y la correspondencia cruzada con los casos de uso aprobados en la Semana 1.

## 2. Flujo de Actividad y Decisiones
El diagrama completo está disponible en [activity-diagram.puml](diagrams/activity-diagram.puml).
- **Acceso Inicial**: `ACT-ACC-01` valida JWT; `ACT-ACC-02` confirma tenant; `ACT-ACC-03` extrae roles Spatie.
- **Flujo Administrativo (`Admin`)**:
  - `ACT-PUB-01` verifica rol `Admin`.
  - Publicación: `ACT-PUB-02..04` capturan y validan metadatos; `ACT-PUB-05` almacena archivo; `ACT-PUB-06..07` registran y confirman.
  - Actualización: `ACT-UPD-01..03` seleccionan y validan versión; `ACT-UPD-04..06` almacenan y confirman.
  - Archivado: `ACT-ARC-01..02` solicitan confirmación; `ACT-ARC-03..04` marcan como archivado.
- **Flujo de Consulta (Lectores y Admin)**:
  - `ACT-CON-01..03` recuperan y filtran documentos por tenant y rol del solicitante.
  - `ACT-CON-04..06` gestionan búsqueda y revalidación de acceso; `ACT-CON-07..08` entregan visualización o descarga.
- **Excepciones**: `ACT-EX-01` (JWT inválido), `ACT-EX-02` (tenant erróneo), `ACT-EX-03` (no-admin denegado), `ACT-EX-04` (datos inválidos), `ACT-EX-05` (fallo persistencia/storage), `ACT-EX-06` (documento no autorizado/archivado).

## 3. Secuencia y Participantes
El diagrama de secuencia se encuentra en [sequence-diagram.puml](diagrams/sequence-diagram.puml).
- **Participantes**: `User` (actor), `UI` (frontend propuesto), `API` (gateway propuesto), `Access` (JWT/Tenant/RBAC base HIS), `Service` (lógica de negocio documental), `DB` (metadatos ASII-24), `Storage` (archivos físicos).
- **Mensajería**:
  - Autenticación: `SEQ-ACC-01` envía credenciales; `SEQ-ACC-02` valida con middleware base; `SEQ-ACC-03` retorna contexto.
  - Publicación/Versión: `SEQ-PUB-01..06`, `SEQ-UPD-01..06`, `SEQ-ARC-01..04`.
  - Consulta/Descarga: `SEQ-CON-01..04` (listado filtrado), `SEQ-CON-05..08` (entrega de contenido).
  - Respuestas de Excepción: `SEQ-EX-01..03` (seguridad), `SEQ-EX-04` (validación), `SEQ-EX-05` (no encontrado/no autorizado), `SEQ-EX-06` (fallo de infraestructura).

## 4. Matriz de Trazabilidad Cruzada
| Caso de Uso | Actor Principal | Actividades Relacionadas | Mensajes de Secuencia | Resultado Trazable |
|---|---|---|---|---|
| UC-01 Consultar lista | Usuario autenticado | `ACT-ACC-01..03`, `ACT-CON-01..03` | `SEQ-ACC-01..03`, `SEQ-CON-01..04` | Catálogo filtrado por tenant y rol |
| UC-02 Buscar y filtrar | Usuario autenticado | `ACT-CON-04` | `SEQ-CON-01`, `SEQ-CON-03..04` | Resultados coincidentes |
| UC-03 Visualizar manual | Usuario autenticado | `ACT-CON-05..08` | `SEQ-CON-05..08` | Lectura de documento autorizado |
| UC-04 Descargar manual | Usuario autenticado | `ACT-CON-05..08` | `SEQ-CON-05..08` | Descarga de archivo autorizado |
| UC-05 Publicar manual | `Admin` | `ACT-ACC-01..03`, `ACT-PUB-01..07` | `SEQ-ACC-01..03`, `SEQ-PUB-01..06` | Documento publicado con roles |
| UC-06 Asignar visibilidad | `Admin` | `ACT-PUB-03`, `ACT-PUB-06` | `SEQ-PUB-01`, `SEQ-PUB-05` | Permisos asignados al documento |
| UC-07 Actualizar versión | `Admin` | `ACT-ACC-01..03`, `ACT-UPD-01..06` | `SEQ-ACC-01..03`, `SEQ-UPD-01..06` | Nueva versión registrada |
| UC-08 Archivar documento | `Admin` | `ACT-ACC-01..03`, `ACT-ARC-01..04` | `SEQ-ACC-01..03`, `SEQ-ARC-01..04` | Estado archivado (excluido de lista) |
| UC-09 Verificar acceso | Control acceso HIS | `ACT-ACC-01..03`, `ACT-EX-01..03` | `SEQ-ACC-01..03`, `SEQ-EX-01..03` | Contexto de seguridad resuelto |
| UC-10 Validar publicación | Módulo ASII-24 | `ACT-PUB-04`, `ACT-UPD-03`, `ACT-EX-04` | `SEQ-PUB-03`, `SEQ-UPD-03`, `SEQ-EX-04` | Datos verificados o retroalimentados |

## 5. Verificación de Consistencia
1. **Identificadores Unificados**: Todos los IDs (`UC-*`, `ACT-*`, `SEQ-*`) concuerdan entre especificaciones y diagramas.
2. **Seguridad Integrada**: `UC-09` modela la dependencia obligatoria de la infraestructura existente sin alterarla.
3. **Manejo de Errores**: Cada excepción de actividad (`ACT-EX-*`) tiene su respuesta de secuencia correlativa (`SEQ-EX-*`).
