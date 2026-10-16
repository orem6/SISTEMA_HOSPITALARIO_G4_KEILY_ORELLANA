# ASII-15 — Modelo de Vistas Arquitectónicas (Semana 3)

## 1. Vista Lógica
La vista lógica organiza el módulo en tres capas concéntricas desacopladas:
- **Capa de Dominio**: Entidades (`Prescription`, `Allergen`), interfaces de estrategia (`AllergyEvaluationStrategyInterface`), eventos de dominio (`PrescriptionIssuedEvent`, `AllergyAlertTriggeredEvent`) y contratos de repositorio (`PrescriptionRepositoryInterface`, `AllergyRepositoryInterface`).
- **Capa de Aplicación**: Casos de uso coordinados por `PrescriptionService`, inyector de estrategias clínicas y despachador de eventos `PrescriptionEventDispatcher`.
- **Capa de Infraestructura**: Adaptadores de persistencia en PostgreSQL (`PostgresPrescriptionRepository`), caché Redis de alérgenos y clientes de mensajería.

```text
[ HTTP Request + X-Tenant-ID ] --> [ PrescriptionController / Middleware ]
                                                  │
                                     [ PrescriptionService ] (Aplicación)
                       ┌──────────────────────────┼──────────────────────────┐
                       ▼                          ▼                          ▼
             [ Strategy Registry ]      [ Domain Entities ]       [ Event Dispatcher ]
             - DirectAllergyRule        - Prescription            - PrescriptionIssued
             - CrossReactivity          - AllergyCheckResult      - AllergyAlertTriggered
                       │                                                     │
             [ AllergyRepository ]                                [ Audit & Alert Handlers ]
```

## 2. Vista de Componentes y Despliegue
- **API Gateway / Ingress**: Valida JWT, extrae y autentica el encabezado `X-Tenant-ID`, aplicando políticas de Rate Limiting y CORS.
- **Micro-HIS Prescripciones (Contenedor PHP 8.2-FPM / Nginx)**: Servicio sin estado (stateless) que aloja la lógica asistencial, orquestación de patrones y conexión transaccional.
- **PostgreSQL 16 (Base de Datos Relacional)**: Persistencia de prescripciones, alergias e historial de auditoría con políticas de seguridad RLS (Row-Level Security) filtradas por `tenant_id`.
- **Redis 7 (Caché de Alérgenos)**: Almacén volátil para consulta ultrarrápida de catálogos y reactividad cruzada con SLA p95 < 15 ms.
- **Bus de Eventos (RabbitMQ / Internal Dispatcher)**: Canal asíncrono para propagar eventos hacia módulos de farmacia, enfermería y bitácora forense.

```text
[ Cliente Web / EHR ] ──HTTPS──> [ API Gateway (Valida JWT + X-Tenant-ID) ]
                                               │
                        ┌──────────────────────┴──────────────────────┐
                        ▼                                             ▼
          [ App Pod: PHP 8.2 Micro-HIS ]                 [ Redis 7 Cluster (Caché) ]
            - Strategy Engine                              - Active Allergies
            - Event Dispatcher                             - Cross-Reactivity Graph
                 │                     │
                 ▼                     ▼
     [ PostgreSQL 16 (RLS) ]   [ Event Bus (RabbitMQ) ] ──> [ Farmacia / Auditoría ]
```

## 3. Aislamiento Multi-Tenant y Flujo Asistencial
1. **Captura Perimetral**: El middleware extrae `X-Tenant-ID` del encabezado HTTP y lo asocia al hilo de ejecución en `TenantContext`.
2. **Evaluación de Alergias**: `PrescriptionService` invoca en cadena las estrategias (`DirectAllergyRuleStrategy` y `CrossReactivityStrategy`) pasando el `tenant_id` y el `patient_id`.
3. **Bloqueo / Override**: Si se detecta riesgo severo sin justificación médica (`override_reason`), se publica `AllergyAlertTriggeredEvent` y se aborta con HTTP 422.
4. **Emisión y Notificación**: Con evaluación limpia o justificación válida, la receta se persiste mediante `PrescriptionRepositoryInterface`, y se publica `PrescriptionIssuedEvent` para consumo de farmacia.
