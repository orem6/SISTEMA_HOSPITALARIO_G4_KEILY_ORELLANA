# ASII-24 — Requerimientos y Criterios de Aceptación (Semana 2)
**Estudiante:** Albino Sebastián Rosales Ruano (`codsebas`) | **Asignación Odoo:** #3929 | **Issue GitHub:** #14

## 1. Requerimientos Funcionales (RF-01 a RF-10)
| ID | Nombre | Descripción y Alcance |
|---|---|---|
| **RF-01** | Validar Contexto | Valida token JWT y cabecera `X-Tenant-ID` previa a cualquier operación. |
| **RF-02** | Consultar Documentos | Retorna catálogo activo del tenant filtrado por el rol del usuario. |
| **RF-03** | Buscar y Filtrar | Permite búsqueda facetada (título, rol) sobre el catálogo autorizado. |
| **RF-04** | Visualizar Manual | Despliega contenido renderizado revalidando tenant, rol y vigencia. |
| **RF-05** | Descargar Fichero | Entrega stream binario seguro para usuarios clínicos acreditados. |
| **RF-06** | Publicar Documento | Permite a `Admin` dar de alta manuales institucionales del tenant. |
| **RF-07** | Asignar Visibilidad | Asocia matriz de roles lectores (`Médico`, `Enfermera`) a cada manual. |
| **RF-08** | Validar Metadatos | Revisa consistencia de campos obligatorios y formato de archivo. |
| **RF-09** | Actualizar Versión | Permite a `Admin` registrar nueva versión preservando el histórico. |
| **RF-10** | Archivar Documento | Excluye lógicamente el manual de consultas activas sin borrado físico. |

## 2. Requerimientos No Funcionales (RNF-01 a RNF-08)
| ID | Categoría | Criterio de Medición |
|---|---|---|
| **RNF-01** | Autenticación JWT | Endpoints protegidos exigen token firmado válido. |
| **RNF-02** | Multi-Tenancy | Aislamiento estricto por `X-Tenant-ID` sin fuga inter-clínica. |
| **RNF-03** | Autorización RBAC | Mutaciones reservadas a `Admin`; roles clínicos en solo lectura. |
| **RNF-04** | Formato RFC 7807 | Respuestas de error estandarizadas en `application/problem+json`. |
| **RNF-05** | Latencia Consulta | 95% de consultas (<100 registros) responden en t < 2.0s. |
| **RNF-06** | Atomicidad | Fallos de almacenamiento impiden persistencia inconsistente. |
| **RNF-07** | Desacoplamiento | Persistencia documental desacoplada de la tecnología de guardado. |
| **RNF-08** | Trazabilidad | Registro inmutable de auditoría con fecha, tenant y autor `Admin`. |

## 3. Criterios de Aceptación (Gherkin BDD)
### CA-RF-01: Contexto de acceso y seguridad
- **Dado** un usuario autenticado con JWT vigente y cabecera `X-Tenant-ID` correspondiente;
- **Cuando** envía una solicitud a la API documental;
- **Entonces** el sistema valida el contexto organizacional y procesa la petición.

### CA-RF-02 a CA-RF-09: Consulta clínica por rol
- **Dado** un usuario con rol `Médico` o `Enfermera` en su tenant asignado;
- **Cuando** solicita el catálogo de manuales disponibles;
- **Entonces** retorna únicamente manuales activos autorizados para su perfil específico.

### CA-RF-10: Archivador lógico administrativo
- **Dado** un manual activo y un usuario con rol `Admin`;
- **Cuando** se solicita el archivado del documento;
- **Entonces** el sistema actualiza su estado a archivado excluyéndolo de consultas clínicas.
