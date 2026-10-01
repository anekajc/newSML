# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 13 (PHP 8.3) internal ERP / back-office web app for an Indonesian trading business (module names and DB columns are in Indonesian: `Barang`=Goods, `Gudang`=Warehouse, `Piutang`/`Hutang`=Receivables/Payables, `SO`/`PO`=Sales/Purchase Order, `Periode`=accounting period). It is largely a thin Laravel wrapper around a **pre-existing SQL Server database** rather than an Eloquent-first app — see Architecture below.

## Commands

```bash
# Install
composer install
npm install

# Local dev (serves app + queue listener + log tail + vite, concurrently)
composer run dev

# Just the asset pipeline
npm run dev      # vite dev server
npm run build    # production build

# Tests (Laravel's default sqlite-backed PHPUnit suite — does NOT touch the SML SQL Server DB)
composer run test
# or directly:
php artisan test
php artisan test --filter=SomeTestName
php artisan test tests/Feature/ExampleTest.php

# Lint/format (Laravel Pint, installed but no custom pint.json — uses Laravel preset)
vendor/bin/pint
vendor/bin/pint --test   # check only, no changes
```

There is no JS test runner or JS linter configured — `npm run dev`/`build` only run Vite.

## Architecture

### Two databases, two very different roles

- `sqlite` (default `DB_CONNECTION`) — Laravel's own bookkeeping: sessions, cache, queue, and the `users`/auth tables. This is what `php artisan migrate` and the test suite target.
- `SML` (`config/database.php`) — a `sqlsrv` connection to the real business database (`DBSMLNEW`). Nearly all business data (`DBHDGROUP`, `DBBARANG`, `DBSubGroup`, `DBPERIODE`, `DBPERUSAHAAN`, etc.) lives here and is queried with **raw parameterized SQL**, not Eloquent:
  ```php
  DB::connection('SML')->select('SELECT * FROM DBHDGROUP WHERE KODEHDGRP = :kode', ['kode' => $req->kode]);
  DB::connection('SML')->update('UPDATE DBHDGROUP SET NAMAHDGRP = :nama WHERE KODEHDGRP = :kode', [...]);
  ```
  Only a handful of Eloquent models exist (`app/Models/`: `User`, `UserReal`, `NewMenu`, `NewMenuReport`, `NewPeriode`, `VWPerkiraan`, `VwPPL`) — most of them map to SQL Server views/tables via the `SML` connection too. When adding a new data operation, match the existing convention for that controller (raw SQL on `SML`) rather than introducing new Eloquent models unless the surrounding code already does.
- `config/database.php` has **hardcoded fallback credentials** for the `SML` connection (host/password baked into the `env()` defaults). Don't copy that pattern for new connections, and don't assume `.env` is the only place secrets live when auditing this repo.

### Module layout (controllers, routes, views all mirror each other)

Business domains: `Master`, `Marketing`, `Purchasing`, `Accounting`, `Report`, `Berkas` (+ a `Gudang`/warehouse route file that's currently commented out in `routes/web.php`). For each domain:
- Controllers live in `app/Http/Controllers/<Domain>/` (e.g. `app/Http/Controllers/Master/MasterHeadGroupController.php`), namespaced `App\Http\Controllers\<Domain>`.
- Routes live in `routes/<domain>.php` (lowercase), all `require`d from `routes/web.php`.
- Views live in `resources/views/<domain>/`, extending the shared `newmaster` Blade layout.

**CRUD endpoint naming convention** — controllers expose a consistent set of actions per entity, called via jQuery `$.ajax` from inline `<script>` blocks in the Blade view (no SPA framework, no Vite-managed JS per view — Vite only builds `resources/js/app.js`/`resources/css/app.css`; page-specific JS is inline Blade, legacy static assets are served straight from `public/js` and `public/css`):
- `index` — renders the Blade page
- `loadAll` — returns the full list (JSON) for the initial table load
- `spAdd` / `spEdit` / `spDelete` / `spDetail` — create/update/delete/read-one (the `sp` prefix is a holdover from when these were SQL Server stored procedures; they're now inline parameterized SQL)
- Nested entities on the same page follow the same pattern with a suffix, e.g. `spAddSubGroup`, `spEditSubKategori`.

Most routes are gated with `->middleware('auth')`.

### Duplicate/dead controller files — read the namespace, not the folder

`app/Http/Controllers/Master/` contains several files that **share a filename with a top-level controller** (`AuthController.php`, `HomeController.php`, `GlobalController.php`, `CetakController.php`, `KoreksiStockController.php`, `KoreksiStockMySqlController.php`, `LaporanModelController.php`, `ModelController.php`, `NewMenuController.php`, `PeriodeController.php`, `ReportController.php`, `SearchController.php`, `TesController.php`, `UsersController.php`, `Controller.php`) but still declare `namespace App\Http\Controllers;` (not `...\Master`) — i.e. they collide with, rather than extend, the real top-level classes. No route or code anywhere references `App\Http\Controllers\Master\HomeController` etc. — the active, routed classes are always the ones directly under `app/Http/Controllers/` (root). Treat the `Master/`-folder copies of these specific filenames as dead/legacy and do not edit them expecting effect; if a task touches auth, home, global helpers, "cetak" (print), koreksi stock, laporan model, menu, periode, report, search, tes, or users logic, edit the root-level file. Genuine `Master`-namespaced controllers (`MasterXxxController`, `MasterSetPosting/*`, etc.) don't have this problem.

### Auth

Standard Laravel session auth (`Auth::attempt`) against the `users` table (sqlite/local), in `app/Http/Controllers/AuthController.php` — not the `Master/` duplicate. On successful login it also writes the current month/year to `dbperiode` on the `SML` connection, so login has a side effect on the SQL Server side.

### Traits

Shared cross-controller helpers live in `app/Traits/` and are pulled into controllers as needed (not a base class):
- `GlobalTrait` — date/time helpers, numeric formatting on raw query results, temp-table helpers, `DBNOMORPK` insert/delete for document-number allocation.
- `AksesTrait` — builds the `$akses` (access/permissions) array passed to report views, keyed off `DBFLMENUREPORT`/`DBMENUREPORT` and the logged-in user.
- `ActivityTrait`, `ReportTrait`, `ReportVoucherTrait`, `TerbilangTrait` (`terbilang` = number-to-Indonesian-words, used for printed documents).

## Notes & gotchas
- Do not make any changes until you have 95% confidence in what you need to build. Ask me follow-up questions until you reach that confidence.
- After all edits, always tell me what files have been changed.