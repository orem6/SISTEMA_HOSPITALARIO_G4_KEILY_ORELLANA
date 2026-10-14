---
name: api-contracts-openapi
description: Estándares de contratos API en OpenAPI 3.0, colecciones Postman, respuestas RFC 7807 (Problem Details) y documentación diferenciada por rol para el Sistema Hospitalario Integrado ASII 2026 G4. Úsala al documentar, exponer o integrar APIs REST bajo /api/v1.
---

# Contratos API y Documentación Técnica (ASII-24)

Directrices de diseño de contratos canónicos, manejo de errores y documentación técnica para el Sistema Hospitalario Integrado (SHI 2026). Garantiza interoperabilidad, trazabilidad y control de acceso por rol y tenant.

## 1. Especificación OpenAPI 3.0.3
- Todo contrato debe definirse en OpenAPI 3.0.3 dentro de `contracts/` o en las especificaciones del módulo.
- **Servidor base:** `/api/v1` con soporte multitenant.
- **Headers obligatorios:**
  - `X-Tenant-ID`: UUID del tenant/hospital activo (`in: header`, `required: true`).
  - `Authorization`: `Bearer <token>` emitido por JWT en rutas protegidas.
- **Nomenclatura REST:** Recursos en plural en minúsculas (`/medications`, `/prescriptions`, `/patients`).
- **Esquemas:** Tipos de datos explícitos (`uuid`, `date-time`, `string`, `integer`, `boolean`), validación de rangos y enums declarados.

## 2. Respuestas de Error bajo RFC 7807 (Problem Details)
Toda respuesta de error debe retornar `Content-Type: application/problem+json` con la estructura uniforme:
```json
{
  "type": "https://his.umg.edu.gt/errors/validation-error",
  "title": "Unprocessable Entity",
  "status": 422,
  "detail": "El paciente presenta antecedentes de alergia al fármaco prescrito.",
  "instance": "/api/v1/prescriptions/activate",
  "code": "ALLERGY_CONTRAINDICATION_BLOCKED",
  "invalid_params": [
    { "name": "medication_id", "reason": "Principio activo incompatible con alergia activa." }
  ]
}
```
- **Matriz de códigos HTTP:**
  - `400 Bad Request`: Payload malformado o JSON no parseable.
  - `401 Unauthorized`: Token ausente, expirado o firma inválida.
  - `403 Forbidden`: Rol o permiso Spatie insuficiente para la acción.
  - `404 Not Found`: Recurso no encontrado en el tenant actual.
  - `409 Conflict`: Conflicto de estado (ej. cama ocupada, cita solapada, receta no cancelable).
  - `422 Unprocessable Entity`: Fallo en validación de reglas de negocio o datos.

## 3. Colecciones de Integración Postman (v2.1)
- Agrupadas por recurso y caso de uso (camino feliz, validaciones, excepciones de rol y tenant).
- Variables de entorno obligatorias: `{{base_url}}`, `{{token}}`, `{{tenant_id}}`.
- Scripts de prueba automatizados verificando código HTTP y conformidad del schema JSON.

## 4. Documentación y Manuales por Rol
- **Gobierno de visibilidad:** El centro documental filtra guías y endpoints según roles Spatie (`Admin`, `Médico`, `Enfermero`, `Bioquímico`, `Auditor`).
- **Aislamiento:** Ningún usuario puede visualizar manuales ni especificaciones técnicas de un tenant diferente al autorizado en su token.
- **Versionado:** Control de versiones inmutable (`v1.0.0`, `v1.1.0`) con historial de cambios y notas de release.
