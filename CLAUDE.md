# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

PROXIS — a project/activity monitoring web app (thesis project, BPS context) built on Laravel 12 (PHP 8.2+), Blade + Alpine.js + Tailwind CSS v4 via Vite, SweetAlert2 for dialogs. Domain language, table/column names, UI text and code comments are in **Indonesian**; keep new code consistent with that.

## Commands

```bash
composer setup            # install deps, copy .env, key:generate, migrate, npm install + build
composer dev              # runs artisan serve + queue:listen + pail (logs) + vite concurrently
php artisan migrate --seed  # seeds roles, peran_proyek, and admin account (admin@bps.go.id / password123)
npm run build             # production asset build
composer test             # clears config, runs php artisan test
php artisan test --filter=SomeTest   # single test
vendor/bin/pint           # code style (Laravel Pint)
```

Local `.env` uses MySQL; `.env.example` and tests (`phpunit.xml`) use SQLite (tests: in-memory). Session, cache and queue all use the `database` driver. The test suite currently only contains Laravel's example tests. The local PHP has no `pdo_sqlite`, so DB-backed tests must point at a separate MySQL database via env vars (e.g. `DB_CONNECTION=mysql DB_DATABASE=<test_db> php artisan test`) — never the real `manajemenproyek` database.

## Architecture

**Auth model is `Pengguna`, not `User`.** `config/auth.php` points the provider at `App\Models\Pengguna` (table `pengguna`, PK `id_pengguna`). `App\Models\User` is unused skeleton code. All models use custom Indonesian table names and `id_<entity>` primary keys — always pass explicit foreign/local keys in relationships.

**Roles** (`role.nama_role`): `Admin`, `Direktur`, `Ketua Tim`, `Anggota`. Role is looked up by name string throughout controllers. Post-login/guest redirect per role is defined in `bootstrap/app.php` (`redirectUsersTo`) and mirrored in `AuthController`. Routes in `routes/web.php` are grouped by role prefix (`admin.`, `direktur.`, `ketuatim.`, `anggota.`), each backed by one controller and gated by `auth` plus the `role:<nama_role>` middleware (`CheckRole`, aliased in `bootstrap/app.php`), which redirects other roles to their own home page. Ketua Tim actions additionally check that the project belongs to the team they lead (`KetuaTimController::proyekMilikTimSaya`).

**Account status:** `Pengguna.status_akun` is `pending` | `aktif` | `nonaktif`. Self-registration creates `pending` users that an Admin must activate. `CheckActiveUser` middleware (appended to the `web` group) logs out any non-`aktif` user on every request.

**Teams vs. projects:** `TimKerja` has a Ketua Tim (`id_ketua_tim`) and members via `AnggotaTim` (active membership = `tanggal_keluar IS NULL`). `Proyek` belongs to a team, has an `id_ketua_proyek`, and members via `AnggotaProyek` with a `peran_proyek` (Ketua Proyek / Anggota). Activities (`AktivitasProyek`) belong to a project and have `ProgressAktivitas` reports, `KendalaAktivitas` (obstacles), and `DokumenPendukung` (uploads stored on the `public` disk under `dokumen_progress/`).

**Derived status/progress (important when touching models):**
- `AktivitasProyek.target` is the activity's progress percentage (clamped 0–100). Its `status_aktivitas` is recomputed in a `saving` hook via `hitungStatusOtomatis()` (`belum_dimulai` / `berjalan` / `selesai` / `terlambat`) from dates + progress.
- `Proyek.persen_progress` = average of its activities' `target`; `status_proyek` is likewise auto-computed on save. `AktivitasProyek::syncProgressProyek()` pushes activity changes up to the project.
- Because status depends on today's date, controllers call `Proyek::sinkronkanSemuaStatus()` at the top of dashboard/list actions. It is throttled by cache keys `proyek_status_synced_recent` / `aktivitas_status_synced_recent` (10 min), which model save/delete hooks invalidate. Writes inside the sync use `updateQuietly` to avoid recursion.

**Notifications:** in-app bell only. Controllers call `$pengguna->notify(new GeneralNotification($title, $message))` (database channel, `notifications` table). The bell component polls `notifications.check`.

**Views:** pages are organized per role under `resources/views/{admin,direktur,ketuatim,anggota}/`, with CRUD forms as Alpine-driven modals in each `modals/` subfolder. Shared layout and UI are anonymous Blade components in `resources/views/components/` (`<x-layoututama>`, `<x-sidebar>`, `<x-datatable>`, `<x-toast>`, …). Live search/filter is done by `fetch`ing the same page URL with `X-Requested-With`, parsing the returned HTML and swapping a wrapper element by id (then `Alpine.initTree`) — so controllers return the full view, and those wrapper ids must be preserved. Direktur charts load JSON from the `direktur.chart.*` endpoints. `AppServiceProvider` sets Carbon locale `id`, Tailwind pagination, and shares a `$sapaanWaktu` greeting (Asia/Jakarta time) with every view.

**Scheduled reminder:** `php artisan proxis:ingatkan-progress` (`App\Console\Commands\IngatkanProgress`) notifies the responsible member of any started, unfinished activity with no progress report for 7 days (once per window; `--hari=N` overrides). It is registered in `routes/console.php` for weekdays 08:00 WIB and needs a server cron entry running `php artisan schedule:run` every minute. Tests: `tests/Feature/IngatkanProgressTest.php` (needs a separate MySQL test database).

`scratch/` holds one-off helper scripts, not application code.
