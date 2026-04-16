# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 12 online shop.

- `app/` contains domain code: controllers, services, DTOs, models, middleware, and requests.
- Admin functionality is grouped under `app/Http/Controllers/Admin`, `app/Http/Requests/Admin`, and `app/DTO/Admin`.
- `resources/views/` contains Blade templates (`admin/` for back office, `products/`, `auth/`, etc.).
- `routes/web.php` defines web and admin routes.
- `database/migrations` and `database/seeders` manage schema and seed data.
- `tests/Feature` and `tests/Unit` contain PHPUnit tests.
- Frontend assets are built with Vite (`resources/`, `vite.config.js`).

## Build, Test, and Development Commands
Use these commands from the repository root:

- `composer setup` — full local bootstrap (`.env`, key, migrations, npm install, frontend build).
- `composer dev` — runs app server, queue listener, logs, and Vite in parallel.
- `php artisan serve` — backend only.
- `npm run dev` / `npm run build` — Vite dev server / production build.
- `composer test` or `php artisan test` — run test suite.
- `composer cschk` — code style check (`php-cs-fixer` dry run).
- `composer csfix` — auto-fix code style issues.

## Coding Style & Naming Conventions
- Follow PSR-12, 4-space indentation, and `declare(strict_types=1);` in PHP files where used.
- Prefer explicit typing for method params/returns in controllers and services.
- Keep namespaces aligned with paths (PSR-4), e.g. `App\\Http\\Controllers\\Admin\\ProductController`.
- Use descriptive names: `StoreRequest`, `UpdateRequest`, `*Service`, `*Dto`.

## Testing Guidelines
- Framework: PHPUnit via Laravel test runner.
- Add Feature tests for HTTP flows and middleware (especially admin role access).
- Add Unit tests for services/DTO transformations.
- Test file naming: `SomethingTest.php`; method names should describe behavior clearly.

## Commit & Pull Request Guidelines
- Current history favors short, task-focused commit subjects (often Russian), e.g. `панель администратора`.
- Use one logical change per commit and write an imperative subject.
- PRs should include:
  - concise summary of changes,
  - linked issue/task,
  - screenshots for UI changes (catalog/admin pages),
  - verification notes (commands run: tests/style checks).

## Security & Configuration Tips
- Do not commit secrets; keep credentials in `.env`.
- Before first run: copy `.env.example` to `.env` and run migrations.
- Validate and authorize admin requests through dedicated FormRequests and role middleware.
