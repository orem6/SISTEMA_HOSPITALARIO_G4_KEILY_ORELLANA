# Declaración de Asistencia de Inteligencia Artificial

## 1. Identificación y Propósito
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Herramienta de IA**: Antigravity / Claude 3.7 Sonnet / Gemini
- **Propósito**: Asistencia en la formulación rigurosa de los requerimientos funcionales (RF-01 a RF-10) y no funcionales (RNF-01 a RNF-06), redacción de criterios de aceptación en formato Gherkin, modelado de refactorización según el Principio de Inversión de Dependencias (DIP) y codificación del script automatizado de validación para la Semana 2 del módulo ASII-15.

## 2. Prompts Clave Utilizados
1. *Especificación de Requerimientos y Criterios Gherkin*:
   > "Redacta la especificación formal de RF-01 a RF-10 y RNF-01 a RNF-06 para el módulo de prescripciones con validación de alergias (ASII-15), incluyendo criterios de aceptación en formato Gherkin (Dado-Cuando-Entonces) para camino feliz, bloqueo severo, paciente no evaluado y override auditado."
2. *Diseño Orientado a Objetos y Principio SOLID DIP*:
   > "Modela la refactorización arquitectónica de PrescriptionService aplicando el Principio de Inversión de Dependencias (DIP) conforme a la cátedra de diseño de software (mvpcluster.com/diseno-de-software-2/). Genera código ilustrativo PHP 8.2 vanilla comparando el diseño 'Antes' (acoplado a repositorios PostgreSQL concretos) con el diseño 'Después' (desacoplado mediante interfaces AllergyVerifierInterface y PrescriptionStorageInterface, con adaptadores para BD y mocks en memoria)."
3. *Script de Validación y Control de Presupuesto*:
   > "Implementa un script en PHP 8.2 vanilla que verifique estándares de formato (UTF-8 sin BOM, LF, sin espacios residuales), existencia de los 7 artefactos de la semana 2, cobertura de requerimientos y límite estricto de <= 800 líneas totales."

## 3. Criterio y Validación Humana
- **Validación de Reglas Clínicas**: Se verificó que el bloqueo preventivo por alergia moderada o severa no pueda ser eludido por omisión, exigiendo siempre justificación clínica explícita para el override.
- **Alineación con el Principio DIP**: Se verificó que las interfaces pertenezcan conceptualmente al dominio de la aplicación y que las implementaciones de infraestructura dependan de ellas, y no al revés.
- **Calidad de Código PHP 8.2+**: Se comprobó que el código de ejemplo utilice tipado fuerte, readonly properties y sintaxis moderna sin dependencias de frameworks externos.
- **Responsabilidad Académica**: El estudiante `codsebas` asume la autoría intelectual, comprensión técnica y responsabilidad integral sobre los artefactos presentados.
