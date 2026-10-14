# Declaración Ética de Asistencia de Inteligencia Artificial

## 1. Identificación y Alcance
- **Estudiante**: Albino Sebastián Rosales Ruano (`codsebas`)
- **Módulo**: ASII-24 — Contratos API / Centro de Documentación y Manuales por Rol
- **Semana de Entrega**: Semana 2 (Requerimientos, Criterios de Aceptación y Diseño SOLID ISP)
- **Herramientas de IA Utilizadas**: Antigravity / Claude 3.7 Sonnet / Gemini

## 2. Prompts Clave Empleados
1. *Formalización de Requerimientos y Criterios Gherkin*:
   > "Especifica formalmente los requerimientos RF-01 a RF-10 y RNF-01 a RNF-08 para el módulo ASII-24 derivándolos de los casos de uso de la Semana 1, redactando criterios de aceptación en formato Dado-Cuando-Entonces enfocados en roles clínicos, administración y aislamiento multi-tenant."
2. *Diseño Orientado a Objetos - SOLID ISP*:
   > "Modela en PHP 8.2+ vanilla la violación del principio de segregación de interfaces con un DocumentationManagerInterface inflado y su posterior refactorización en DocumentReaderInterface y DocumentAdminInterface, citando la teoría de MVP Cluster."
3. *Aseguramiento de Calidad y Presupuesto de Líneas*:
   > "Implementa un script en PHP 8.2 vanilla sin dependencias que valide la existencia de los 7 artefactos, estándares UTF-8 no-BOM, LF, ausencia de trailing spaces y verifique que el cambio total se mantenga estrictamente en <= 800 líneas."

## 3. Criterio y Validación Humana
- **Alineación con el Dominio**: Se supervisó que no se inventaran roles artificiales, restringiendo la administración de manuales exclusivamente al rol existente `Admin` y respetando el identificador técnico `TecnicoLab`.
- **Aislamiento Multi-Tenant**: Se verificó manualmente que todo requerimiento, criterio de aceptación y firma de contrato valide la cabecera `X-Tenant-ID`.
- **Auditoría Técnica y de Formato**: Se revisó la sintaxis estricta de PHP 8.2+, la consistencia de los criterios BDD y la ejecución local satisfactoria del script de validación.
- **Responsabilidad Académica**: El estudiante declara autoría intelectual, comprensión integral y supervisión directa de los artefactos generados.
