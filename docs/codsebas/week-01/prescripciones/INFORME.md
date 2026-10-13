# Informe Arquitectónico — Prescripciones Electrónicas con Validación de Alergias

## 1. Delimitación y Justificación del Módulo
El presente informe formaliza el diseño arquitectónico de la Semana 1 para el módulo **ASII-15: Prescripciones Electrónicas con Validación de Alergias**, enfocado en la prevención de eventos adversos prevenibles por medicamentos en el Hospital Information System (HIS).

### Separación de Responsabilidades y Reutilización
- **Capacidades Base Reutilizadas**: El módulo consume la identidad y roles clínicos del HIS (`local_user_refs`, `doctor_profiles`), episodios de atención (`care_episodes`), catálogo de fármacos (`medications`), registros alérgicos activos (`allergies`) e historial de signos y diagnósticos.
- **Límite Propuesto (ASII-15)**: Implementa la lógica de evaluación farmacológica previa, interceptando la prescripción antes de su persistencia definitiva mediante validación sincrónica de alérgenos y cálculo de reactividad cruzada.

## 2. Reglas Clínicas de Seguridad Farmacológica
1. **Bloqueo Preventivo por Alergia Coincidente**: Si el paciente presenta un alérgeno activo coincidente de severidad moderada, severa o mortal (`severe`, `life_threatening`), el sistema bloquea por defecto la prescripción inmediata para salvaguardar la vida del paciente.
2. **Override Justificado en `prescription_allergy_checks`**: Si el facultativo determina beneficio terapéutico crítico sobre el riesgo (o desensibilización previa), puede anular el bloqueo ingresando obligatoriamente una justificación clínica detallada. Toda decisión se persiste de forma inmutable en la tabla de auditoría clínica `prescription_allergy_checks`.
3. **Manejo de Paciente No Evaluado**: Si el paciente carece de historial alérgico registrado, el sistema no bloquea pero despliega una advertencia obligatoria exigiendo confirmación del médico antes de continuar.
4. **Resiliencia ante Fallo de Servicio**: Ante la indisponibilidad de `AllergyCheckService`, el sistema prohíbe la emisión silenciosa, activando un protocolo de contingencia auditado que registra la incidencia en bitácora.

## 3. Síntesis de Modelado UML
- **Casos de Uso ([use-case-diagram.puml](docs/diagrams/use-case-diagram.puml))**: Delimita 10 casos de uso (`UC-01` a `UC-10`) centrados en el rol `Médico`, con relaciones `<<include>>` hacia verificación alérgica y auditoría, y `<<extend>>` hacia la justificación de override.
- **Flujo de Actividades ([activity-diagram.puml](docs/diagrams/activity-diagram.puml))**: Modela la bifurcación de riesgo (`ACT-ALT-01`), permitiendo la anulación/sustitución del fármaco (`ACT-OVR-01`) o el override con captura de justificativo (`ACT-OVR-02`), concluyendo en persistencia auditada (`ACT-SAV-01`).
- **Flujo de Secuencia ([sequence-diagram.puml](docs/diagrams/sequence-diagram.puml))**: Desacopla 7 componentes clínicos (`Médico`, `UI_Prescripciones`, `PrescriptionController`, `AllergyCheckService`, `AllergyRepository`, `PrescriptionRepository` y `AuditService`), garantizando trazabilidad y aislamiento de responsabilidades.

## 4. Conclusión
El diseño propuesto establece una barrera preventiva contra errores de medicación sin comprometer la autonomía médica ante emergencias, garantizando una trazabilidad del 100% hacia las fases de especificación técnica y desarrollo.
