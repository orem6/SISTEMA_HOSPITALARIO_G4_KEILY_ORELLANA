# ASII-24 — Alcance, Actores y Casos de Uso

## 1. Identificación de la Actividad
- **Estudiante**: Albino Sebastián Rosales Ruano | **GitHub**: `codsebas`
- **Módulo oficial**: Contratos API: OpenAPI/Postman y documentación técnica
- **Actividad adaptada**: Centro de documentación y manuales por rol | **Semana**: 1
- **Tipo de evidencia**: Análisis, delimitación y modelado conceptual UML

## 2. Contexto del Repositorio HIS
| Componente | Estado Verificado | Rol en ASII-24 |
|---|---|---|
| JWT Auth | Implementado (`/api/v1/auth`) | Autentica usuarios del HIS antes del acceso |
| Multi-tenant | Requerido (`X-Tenant-ID`) | Aísla los documentos dentro del tenant activo |
| RBAC Spatie | Roles semilla en base de datos | Determina permisos de lectura y administración |
| Centro Documental | No implementado (Propuesta) | Diseño conceptual propuesto para la Semana 1 |

## 3. Problema y Objetivo
El HIS requiere que el personal clínico y administrativo consulte manuales técnicos y protocolos según su perfil funcional, sin exponer documentación entre organizaciones (tenants). El objetivo es modelar un centro de documentación donde `Admin` publica y gestiona manuales, mientras los usuarios autorizados consultan y descargan material autorizado.

## 4. Límite y Alcance
- **Límite**: Publicación, versionado, archivado, filtrado y descarga de documentación técnica por tenant y rol.
- **Incluido**: Catálogo conceptual de documentos, visibilidad RBAC, aislamiento multi-tenant y trazabilidad UML.
- **Fuera de alcance (Semana 1)**: Endpoints HTTP, contratos OpenAPI/Postman, migraciones, base de datos y código ejecutable.

## 5. Actores
| Actor | Tipo | Responsabilidad |
|---|---|---|
| Usuario autenticado del HIS | General | Consultar, buscar, visualizar y descargar manuales autorizados |
| `Admin` | Primario administrativo | Publicar, asignar visibilidad por rol, versionar y archivar |
| `Médico`, `Enfermera`, `Recepcionista` | Lectores especializados | Consultar material clínico o administrativo de su rol |
| Técnico de laboratorio (`TecnicoLab`) | Lector especializado | Consultar guías técnicas de laboratorio según su rol |
| Control de acceso existente (JWT/RBAC) | Soporte de infraestructura | Validar credenciales, tenant activo y roles del usuario |

## 6. Reglas de Negocio
1. **RN-01 (Gestión Admin)**: Únicamente `Admin` puede publicar, actualizar versiones, asignar visibilidad y archivar documentos.
2. **RN-02 (Aislamiento de Tenant)**: Un documento pertenece estrictamente a un tenant; solicitudes entre tenants son rechazadas.
3. **RN-03 (Intersección RBAC)**: Un lector no-admin solo visualiza documentos vigentes (no archivados) donde sus roles coincidan con los asignados al documento.

## 7. Catálogo de Casos de Uso
| ID | Caso de Uso | Actor | Resumen y Excepción |
|---|---|---|---|
| UC-01 | Consultar documentación disponible | Usuario autenticado | Lista documentos autorizados del tenant. Ex: Sin documentos o credencial inválida. |
| UC-02 | Buscar y filtrar documentación | Usuario autenticado | Aplica filtros por categoría/título. Ex: Sin coincidencias accesibles. |
| UC-03 | Visualizar documento o manual | Usuario autenticado | Carga lectura en pantalla. Ex: Documento no autorizado o archivado. |
| UC-04 | Descargar documento o manual | Usuario autenticado | Entrega archivo descargable. Ex: Almacenamiento no disponible o acceso denegado. |
| UC-05 | Publicar documento o manual | `Admin` | Registra nuevo documento con roles autorizados. Ex: Validación de campos fallida. |
| UC-06 | Asignar visibilidad por rol | `Admin` | Define roles lectores autorizados (included por UC-05). Ex: Rol inexistente. |
| UC-07 | Actualizar versión del documento | `Admin` | Incrementa versión y sustituye archivo. Ex: Documento ajeno al tenant. |
| UC-08 | Archivar documento | `Admin` | Oculta documento de consultas activas sin destruirlo. Ex: Documento inexistente. |
| UC-09 | Verificar identidad, tenant y roles | Control de acceso HIS | Valida JWT, tenant header y roles Spatie. Ex: 401/403 de infraestructura. |
| UC-10 | Validar información de publicación | Módulo ASII-24 | Revisa campos obligatorios y metadatos. Ex: Error de datos incompletos. |

## 8. Diagrama UML y Decisiones
- El diagrama de casos de uso se ubica en [use-case-diagram.puml](diagrams/use-case-diagram.puml).
- Se preserva el rol técnico `TecnicoLab` con etiqueta humana *Técnico de laboratorio*.
- `Admin` especializa a `Usuario autenticado del HIS` y hereda capacidades de consulta.
