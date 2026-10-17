# Casos de uso y actores

## Actores
| Actor | Responsabilidad |
|---|---|
| Técnico de laboratorio | Consulta pendientes, captura y corrige valores. |
| Bioquímico | Consulta para validación posterior, fuera de ASII-19. |
| Control de acceso HIS | Verifica permiso, sesión y tenant. |
| Auditoría clínica | Registra captura y corrección de forma trazable. |

## Reglas del diseño
1. Captura solo para orden del tenant, muestra recibida y estado `pendiente`.
2. Tipo, unidad, campos y rango configurado para captura deben ser válidos; fuera de rango se rechaza (pendiente de confirmar con equipo).
3. Corrección de `capturado`: requiere permiso, motivo y datos válidos; conserva la versión anterior.
4. Guardado y auditoría son una transacción atómica: si uno falla, no se confirma nada.
5. Enviado a validación: ASII-19 no permite corregir; deriva al flujo autorizado.

## Catálogo
| ID | Caso de uso | Actor | Resultado o excepción |
|---|---|---|---|
| UC-01 | Consultar pendientes | Técnico | Lista del tenant o vacía. |
| UC-02 | Capturar resultado | Técnico | Datos recibidos o incompletos. |
| UC-03 | Validar datos | Sistema | Válidos o EX-03. |
| UC-04 | Guardar resultado | Técnico | `capturado` auditado o rollback. |
| UC-05 | Corregir resultado | Técnico | Nueva versión y auditoría o rechazo. |
| UC-06 | Auditar operación | Auditoría clínica | Evento o fallo que revierte la operación. |
| UC-07 | Consultar para validación | Bioquímico | Lectura autorizada, sin validación clínica. |

[Diagrama de casos de uso](diagrams/use-case-diagram.puml). UC-04 incluye captura, validación, autorización y auditoría; UC-05 exige los mismos controles para corrección.
