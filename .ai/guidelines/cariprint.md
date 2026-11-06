# CariPrint — Project Guidelines

These rules apply to every task in this repository. When a rule here conflicts with a generic suggestion, this file wins. When something is ambiguous or contradicts `docs/proposal.pdf`, ask the user instead of assuming.

## Product

- Website for university students around CitraLand Surabaya to find and compare print shops: location, opening hours, open-now status, services, price list, WhatsApp contact.
- CariPrint only connects students and shops. There are NO orders and NO payments on the site.
- Scope source of truth: `docs/proposal.pdf`. Task list and status: `docs/ROADMAP.md`.
- Target users: students on their phones, often in a hurry before class. Core info must be visible without login, fast, and mobile-first.
- UI copy is Indonesian. Code, identifiers, and commit messages are English.

## Features (priority order)

1. **Printing Shop Directory** (core): list and map sorted by distance from the user; shop detail page with hours, open status, services, prices, WhatsApp, "verified" vs "estimated" price badge, last-updated date.
2. **AI Print Agent**: free-text request (+ optional file) → recommended shops, cost estimate per shop, ready-to-send WhatsApp message.
3. **Smart File Check & Color Optimization**: count color vs B/W pages, AI judges whether color is needed per page, suggest B/W pages and show savings. The user always makes the final choice.
4. **Report & Admin Panel**: logged-in users report wrong data; admins review reports and edit shop/price data. Only admins can change shop and price data.

## Access levels

- **Guest**: all main features, AI quota from `config('cariprint.ai.quota.guest')` (3/day).
- **User**: Google login via Socialite, AI quota `config('cariprint.ai.quota.user')` (15/day), saved file-check history, can submit reports.
- **Admin**: email + password via built-in auth, manages shops, prices, and reports. Admins are created by seeder or artisan command only. There is no public registration.

## Stack

Do not add packages beyond this list without asking the user first.

- Laravel (version in `composer.json`), Inertia, React 19, TypeScript, Tailwind CSS v4, shadcn/ui (official React starter kit), Wayfinder for typed routes, Pest for tests.
- Database: SQLite until the Supabase migration (ROADMAP Phase 2), then Supabase Postgres via the `pgsql` connection. Until that switch, migrations and queries must stay database-agnostic: no Postgres-only column types, extensions, or raw SQL. Laravel migrations are the ONLY way to change the schema. Never create or alter tables from a database dashboard.
- File storage: Supabase Storage through a Laravel `s3` disk, private bucket (after the Supabase migration).
- AI: `laravel/ai` with Gemini Flash, always with structured output.
- Admin panel: Filament, mounted at `/admin`.
- Maps: `@vis.gl/react-google-maps` on the frontend. Google Places and Routes APIs are called from backend services only.
- PDF: pdf.js in the browser.

## Core rules (non-negotiable)

1. AI never produces prices, distances, or open status. AI only (a) turns a user request into a typed print spec and (b) judges whether a colored page really needs color. All money, distance, and open-status logic lives in PHP services that read the database and are covered by unit tests.
2. From Google APIs, persist only `place_id`. Coordinates and opening hours stored in the database come from the team's own survey.
3. The user's location is never stored or logged.
4. Uploaded files go to a private bucket and are deleted automatically after 24 hours.
5. Secrets live only in `.env`. Never commit `.env`, and never print secret values in output, logs, or commit messages.
6. Money is stored as integer Rupiah. No floats for prices.

## Architecture

### Backend

- Controllers stay thin: Form Request validation → service call → Inertia response.
- Domain logic lives in `app/Services/<Domain>/`, e.g. `Pricing/PriceCalculator`, `Shops/OpeningStatus`, `Location/DistanceService`, `Google/PlacesClient`.
- AI agents follow `laravel/ai` conventions (`php artisan make:agent`).
- Fixed value sets are PHP enums in `app/Enums` (role, paper size, color mode, binding, report status).
- Authorization uses policies and middleware. No role checks scattered inline.
- Tunable values (quotas, area bounds, file TTL) live in `config/cariprint.php`.

### Frontend

- Pages: `resources/js/pages/<feature>/`.
- Feature components: `resources/js/components/<feature>/`. Shared components: `resources/js/components/`. shadcn primitives stay in `resources/js/components/ui/`; only modify them when there is no other way.
- Hooks in `resources/js/hooks`, pure helpers in `resources/js/lib`, shared types in `resources/js/types`.
- Use Wayfinder-generated route helpers. Never hardcode URLs.
- Public pages use the public layout, not the starter kit's dashboard layout.
- Mobile-first. Every page must work at 360px width.

## Design

- Who it is for: students comparing print shops quickly on a phone. Price, open status, and distance get the strongest visual emphasis. Everything else supports them.
- Design tokens (colors, radius, fonts, spacing) are defined once in the Tailwind theme in `resources/css/app.css`. No ad-hoc hex values or one-off styles in components.
- Reuse existing components before creating new ones, and keep one consistent visual language across all pages.
- When proposing UI, include a short rationale: what is emphasized and why.

## Code standards

- Clean, reusable, and readable. No duplicated logic. No unused code, imports, files, or dependencies.
- Keep files small. Split a file when it passes roughly 250 lines or starts doing more than one job.
- No per-line comments. Use only section headers (`// --- Section Name ---`) and docblocks where the intent is not obvious from the code.
- Check sibling files and follow existing conventions before inventing new patterns.
- Every backend feature ships with Pest feature tests. Every pricing, opening-status, and distance service ships with unit tests.
- Before calling a task done, these must pass: `vendor/bin/pint --dirty`, the lint and type-check scripts in `package.json`, and `php artisan test --compact`.

## Git workflow (team of 3)

- `main` must always be deployable.
- One branch per task: `feat/<area>-<short-desc>`, `fix/<area>-<short-desc>`, `chore/...`, `docs/...`. Areas: `core`, `directory`, `agent`, `filecheck`, `admin`, `report`.
- Conventional Commits in English, e.g. `feat(directory): sort shops by distance`. Small, focused commits.
- Never commit `.env`, `vendor/`, `node_modules/`, or `public/build/`.
- Merging (each member merges their own branch): merge the latest `main` into the branch, run all checks, then `git merge --no-ff` into `main`, push, and delete the branch.
- Never edit a migration that is already on `main`. Create a new migration instead.
- No AI attribution anywhere: no `Co-Authored-By` trailers, no "Generated with" lines, no session links in commits, pull requests, or code.
- AI agents propose the commit message and wait for the user's OK before committing. Never push, merge, force-push, or rewrite history without an explicit instruction.

## Working with the user

- At the start of a session, read `docs/ROADMAP.md` and work on one task at a time.
- For non-trivial tasks, present a plan first (files to touch, design decisions, open questions) and wait for approval.
- When unsure about scope, data, a design choice, or anything that is hard to undo, ask the user first instead of guessing. List the options and recommend one.
- Explain what the user must do manually (accounts, dashboards, server steps) as numbered steps, and wait for confirmation before continuing.
- After each task, tick its checkbox in `docs/ROADMAP.md`, update the progress table in `README.md` (it is submitted to the lecturer as the team progress report), and summarize what changed and how it was tested.

## Deployment constraints

- Hosted on the UC server with CloudPanel (Nginx + PHP-FPM) at `cariprint.lius.my.id`. See `docs/DEPLOY.md`.
- There is no permanent queue worker. Scheduled work runs through a cron `schedule:run` every minute, and queued jobs are drained by the scheduler. Nothing user-facing may depend on instant queue processing. AI calls run synchronously with timeouts.
