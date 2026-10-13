# Declaración de Asistencia de Inteligencia Artificial

## 1. Identificación y Propósito
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Herramienta de IA**: Antigravity / Claude 3.7 Sonnet / Gemini
- **Propósito**: Asistencia en la formulación de casos de uso clínicos, modelado formal PlantUML de actividades y secuencias, y codificación del script de validación automatizada para la Semana 1 de ASII-15.

## 2. Prompts Clave Utilizados
1. *Diseño y Delimitación Clínica*:
   > "Modela el proceso de creación de prescripción médica con verificación previa de alergias para ASII-15, incorporando bloqueo preventivo por riesgo alérgico, excepciones de paciente no evaluado y caída del servicio."
2. *Mecanismo de Override y Auditoría*:
   > "Estructura la alternativa de override justificado por el médico con captura obligatoria de motivo clínico y persistencia inmutable en prescription_allergy_checks."
3. *Optimización y Validación Automatizada*:
   > "Genera un script en PHP 8.2 vanilla que verifique estándares UTF-8 no-BOM, LF, sin espacios finales, balance de etiquetas PlantUML, trazabilidad cruzada y presupuesto estricto de <= 800 líneas."

## 3. Criterio y Validación Humana
- **Reglas Clínicas**: Se validó que el bloqueo preventivo no pueda ser ignorado sin justificación explícita ni registro en auditoría.
- **Alineación con el Esquema Base**: Se comprobó que el flujo no redefina tablas del HIS, sino que reutilice `allergies` y enlace con `prescriptions`.
- **Revisión de Sintaxis UML**: Se inspeccionó la consistencia gráfica y lógica de los tres diagramas antes de su integración.
- **Responsabilidad Académica**: El estudiante asume la autoría, comprensión y responsabilidad total de los artefactos presentados.
