# Actividad, secuencia y trazabilidad

Diagramas: [actividad](diagrams/activity-diagram.puml) y [secuencia](diagrams/sequence-diagram.puml).

## Flujo de captura
1. Consultar pendientes del tenant; técnico introduce valor, unidad y observación.
2. Verificar autorización, orden, muestra, estado y validez de tipo, unidad y rango.
3. Guardar resultado `capturado` y registrar auditoría en una única transacción; confirmar solo si ambos pasos tienen éxito.

## Flujo de corrección
1. Técnico solicita corregir un resultado `capturado`, aportando motivo y nuevos datos.
2. Verificar autorización, tenant, estado y validez del motivo, valor, unidad y rango.
3. Crear versión sin sobrescribir la anterior y auditar atómicamente; confirmar solo tras éxito.

## Alternos y excepciones
| ID | Condición | Respuesta |
|---|---|---|
| FA-01 | Sin pendientes | Lista vacía; sin cambios. |
| EX-01 | Permiso o tenant inválido | Denegar sin datos clínicos. |
| EX-02 | Orden o muestra no elegible | Bloquear captura. |
| EX-03 | Faltan datos, unidad/tipo inválidos o fuera de rango | Rechazar y solicitar corrección de datos; sin persistencia. |
| EX-04 | Resultado enviado a validación | Bloquear corrección y derivar al flujo autorizado. |
| EX-05 | Falla de guardado o auditoría | Revertir toda la transacción; no confirmar. |

## Trazabilidad
| UC | Actividad | Secuencia | Resultado |
|---|---|---|---|
| UC-01 | ACT-01 | SEQ-01 a 03 | Lista filtrada o vacía. |
| UC-02 | ACT-02 | SEQ-04 | Entrada de datos. |
| UC-03 | ACT-04, ACT-08 | SEQ-06, 14 | Datos válidos o EX-03. |
| UC-04 | ACT-03 a 05 | SEQ-05 a 10 | Captura auditada o reversión. |
| UC-05 | ACT-06 a 09 | SEQ-11 a 18 | Versión corregida o rechazo. |
| UC-06 | ACT-05, ACT-09 | SEQ-08, 16 | Auditoría en transacción. |
| UC-07 | ACT-10 | SEQ-19, 20 | Consulta autorizada, sin validación clínica. |

**Regla de alcance adoptada para el diseño:** un dato fuera del rango *configurado para captura* se rechaza; no se permite confirmarlo con advertencia. Esta interpretación de la regla 2 debe confirmarse con el equipo, pues no representa necesariamente los valores de referencia clínicos: estos pueden quedar bajo evaluación posterior de ASII-20.

**Invariante:** sin autorización, validación y auditoría exitosas no existe captura ni corrección confirmada; la versión anterior permanece.
