---
name: laravel-expert
description: Convenciones de Laravel para el Sistema Hospitalario Integrado ASII 2026 G4 (Laravel 12, PHP 8.2+, API JWT + Vue 3/Pinia, PostgreSQL federado CENTRAL/HOSPITAL, Spatie Permission, Stancl Tenancy). Úsala SIEMPRE que crees o modifiques controllers, rutas API, middleware, migraciones, modelos, seeders, tests o código Vue de este repo.
---

# Laravel en el Sistema Hospitalario Integrado

Laravel `^12`, PHP `^8.2`, API REST bajo `/api/v1` consumida por un SPA Vue 3 (Vite, Pinia, Vue Router, Axios). Autenticación con `tymon/jwt-auth`, roles con `spatie/laravel-permission`, tenant por header `X-Tenant-ID`. No hay Blade, Fortify, SQLite ni MySQL.

Antes de crear algo, busca el equivalente existente e imítalo: `app/Http/Controllers/Api/V1/AuthController.php`, `app/Http/Middleware/{JwtAuth,TenantMiddleware}.php`, `routes/api.php`.

## Base de datos (PostgreSQL federado, dos conexiones)
- `central`: red, identidad, MPI, consentimientos, auditoría, sincronización. Migraciones en `database/migrations/central`.
- `hospital`: esquema clínico de un hospital. Migraciones en `database/migrations/hospital`. Requiere extensión `btree_gist`.
- `database/migrations/framework`: sessions, cache, jobs (van en CENTRAL).
- Cada migración declara su conexión (`Schema::connection('central'|'hospital')`) y nunca referencia FKs entre bases: lo de CENTRAL se enlaza por UUID sin constraint.
- Claves UUID, `timestamptz`, constraints/EXCLUDE en SQL cuando el dominio lo exige. Toda migración nueva lleva `down()` y un test de integridad en `tests/Database/{Central,Hospital}`.
- Comandos: `composer migrate:central|hospital|framework`. Modelos Eloquent deben fijar `$connection` explícito.

## API
- Rutas solo en `routes/api.php`, prefijo `/api/v1`, agrupadas por middleware: `tenant` → `jwt.auth` → permisos Spatie (`can:`/`permission:`).
- Controllers en `app/Http/Controllers/Api/V1/<Modulo>/`, delgados: validar con Form Request, delegar lógica a Services en `app/Services`, responder con API Resources (`JsonResource`).
- Respuestas JSON consistentes, códigos HTTP correctos (201 crear, 204 borrar, 422 validación, 403 permiso).
- Datos clínicos sensibles: autorizar siempre por rol/permiso y registrar acceso en la auditoría de CENTRAL; nunca loguear PII.
- Estado actual: login/registro dan 500 hasta que existan los modelos `User`/`Tenant` nuevos; no dependas de ellos sin crearlos.

## Frontend
- Por módulo en `resources/js/modules/<modulo>/{pages,components}`; compartido en `resources/js/shared`.
- Estado en Pinia (`resources/js/stores`), llamadas por la instancia de `resources/js/plugins/axios.js` (ya añade token y `X-Tenant-ID`), rutas en `router/index.js` con guards en `router/guards.js`.
- Composition API con `<script setup>`; alias `@` = `resources/js`.

## Pruebas y calidad
- `composer test` / `php artisan test`. Los tests de BD usan bases desechables de `phpunit.xml` (puertos 55431/55433); en Docker local ya existen `his_central_test` y `his_hospital_test`.
- TDD: test rojo primero, luego implementación. Feature tests para endpoints, `tests/Database` para integridad.
- Formato: `vendor/bin/pint`. Antes de PR: `php artisan route:list --path=api`, `npm run build`, `php artisan test`.

## Flujo de trabajo
- Ramas y PR por módulo vertical (ver `docs/worktree-guide.md`); commits convencionales (`feat(db): ...`). `main` solo lo integra el líder del grupo.
- Cada módulo incluye: contrato API, permisos, pruebas y documentación.
