# Informe Técnico — Arquitectura de Software, Vistas y Patrones (Semana 3)

## 1. Síntesis Arquitectónica y Delimitación del Módulo
El módulo **ASII-15: Prescripciones Electrónicas con Validación de Alergias** (Issue #19 / Odoo #3945) se estructura bajo una arquitectura en capas desacoplada y orientada al dominio dentro del ecosistema Micro-HIS. Garantiza la intercepción en tiempo real de prescripciones médicas, evaluando interacciones contra alérgenos registrados antes de persistir o autorizar la administración de fármacos.

## 2. Modelo de Vistas Arquitectónicas
- **Vista Lógica**: Organizada en Dominio (entidades, value objects, contratos de estrategia y eventos), Aplicación (orquestación en `PrescriptionService` y manejadores) e Infraestructura (repositorios concretos, persistencia transaccional y clientes HTTP).
- **Vista de Componentes y Despliegue**: Servicios contenerizados donde el API Gateway valida identidad y enruta con `X-Tenant-ID`. El servicio clínico interactúa con PostgreSQL particionado por tenant, Redis para caché de contraindicaciones y un bus de eventos en memoria/RabbitMQ para alertas asíncronas.
- **Aislamiento Multi-Tenant y Flujo Asistencial**: Cada petición HTTP propaga `X-Tenant-ID`. La capa de aplicación confina las consultas y mutaciones al esquema o clave de partición del tenant, blindando la privacidad entre hospitales sin degradar el tiempo de respuesta (p95 < 500 ms).

## 3. Patrones de Diseño de Software y Fundamentación Teórica
Conforme a la fundamentación teórica de la cátedra (`https://mvpcluster.com/diseno-de-software-2/`), los patrones adoptados resuelven acoplamiento y rigidez:
- **Strategy Pattern**: Encapsula las reglas clínicas en `AllergyEvaluationStrategyInterface`. Permite alternar y componer `DirectAllergyRuleStrategy` (alergia directa al fármaco) y `CrossReactivityStrategy` (alergias cruzadas por familia farmacológica como betalactámicos) sin alterar el orquestador principal (cumpliendo OCP).
- **Observer Pattern**: Desacopla la emisión de recetas de los efectos secundarios colaterales. Mediante `PrescriptionEventDispatcher`, se publican `AllergyAlertTriggeredEvent` y `PrescriptionIssuedEvent`, procesados independientemente por auditores inmutables (`AuditLogSubscriber`) y notificaciones a farmacia/enfermería (`PharmacyNotificationSubscriber`).
- **Repository Pattern**: `PrescriptionRepositoryInterface` y `AllergyRepositoryInterface` aíslan la lógica asistencial de la capa de acceso a datos, facilitando pruebas con mocks y adaptadores.

## 4. Conclusiones y Seguridad Asistencial
El modelo arquitectónico asegura alta cohesión, extensibilidad de reglas farmacológicas sin riesgo de regresión clínica, y un registro auditable e inmutable ante eventos adversos u overrides facultativos.
