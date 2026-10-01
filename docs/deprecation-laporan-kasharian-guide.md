# Guide — Fixing pre-Laravel-6 API usage in Report controllers

This app was upgraded to **Laravel 13 / PHP 8.3** (`composer.json: "laravel/framework": "^13.8"`), but
most of `app/Http/Controllers/Report/` was written against a pre-Laravel-6 codebase and never migrated.
Several of the old calls aren't merely "deprecated" — they are **hard fatal errors** the moment their
route is hit. This guide is the general playbook for bringing one of these controllers up to date. It
was written while fixing `LaporanAccountingKasHarianController` and confirmed on a second, independent
controller (`LaporanAccountingPiutangPelunasanController`) before being generalized here — both are kept
below as worked examples.

---

## 1. The known fatal/broken patterns

Grep for these first. All counts below are a snapshot from **2026-08-27** — re-run the commands in §5 to
get current numbers, don't trust the checklist counts as still accurate.

| # | Pattern | Why it breaks | Fix |
|---|---------|----------------|-----|
| 1 | `array_add(...)` | Global helper **removed** in Laravel 6.0 → `Error: Call to undefined function array_add()` | See §2.1 — **not** a 1:1 swap to `Arr::add()` |
| 2 | `DB::connection('MGL')` | `MGL` connection is **not defined** in `config/database.php`/`.env`/`.env.example` → `InvalidArgumentException` | See §2.2 |
| 3 | Queries against `VwREPORTHISPO` (or similar view names) | View **does not exist** in any database on the server | See §2.3 — verify before assuming a rename fixes it |
| 4 | `auth()->user()->someProperty` with no null check, on a route with no `auth` middleware | `Attempt to read property on null` for a guest hitting an AJAX endpoint | Guard + `401` JSON, see §2.4 |
| 5 | `$req->get('field')` | Still works, but not idiomatic for a `Route::get` — cosmetic only | Optional: replace with `$req->query('field')` |

**False positive to know about:** `str_contains($haystack, $needle)` used with two arguments is **native
PHP 8** (built into the language since PHP 8.0), not the old Laravel `Str::contains()`/`str_contains()`
helper. It shows up in `HeaderTableController.php` and `PembelianPermintaanNonAgenController.php` — leave
it alone, it is not part of this cleanup.

---

## 2. How to fix each pattern

### 2.1 `array_add()`

```php
// the old pattern, e.g.:
$res = array_add($res, $i+$j, $row[$j]);
```

**Do not** do a 1:1 swap to `Arr::add()`. `Arr::add()` only writes when the key is **absent**, while an
index like `$i+$j` collides across loop iterations (`i=0,j=1` and `i=1,j=0` both produce key `1`) — rows
get silently dropped. The correct replacement is almost always a plain append:

```php
$res[] = $row[$j];
```

`Arr::add()` itself remains fine for genuine keyed-map cases (see `app/Traits/AksesTrait.php` for an
example already using it correctly).

### 2.2 `DB::connection('MGL')`

`MGL` is a legacy connection name, referenced across the codebase (see checklist in §6) but **never
registered** anywhere. There is a database on the server called `DBMGL2` that's the likely original
target, but before "fixing" this by pointing at it:

1. Check whether the method is even called from its Blade view (search the view's `<script>` block for
   the route name). A large fraction of `MGL` usages turn out to be **dead code** — copy-pasted from
   another report and never wired up to any UI control.
2. If it is dead, delete the method and its route entirely rather than repairing the connection.
3. If it is genuinely used, verify the target table/view exists in the intended database before pointing
   `MGL` at it (see §2.3) — swapping the connection name alone just trades one fatal error for another
   (`Invalid object name '...'`).
4. If `MGL` needs to be registered as a real connection one day: **do not** copy the `SML` block's pattern
   in `config/database.php`, which bakes the host and password in as `env()` defaults (credentials
   hardcoded into the repo). Use `env('MGL_...')` with **no** default value.

### 2.3 Queries against a view/table that doesn't exist

Verify directly against the database (`DBSMLNEW` primarily; also checked `DBMGL2`, `DBSML`, `DBSML2`,
`DBETM` when chasing `MGL` leftovers) before assuming the name just needs a connection fix. If the target
genuinely doesn't exist anywhere:

- Check whether a **similarly-named view exists** that the broken code was likely copied from — e.g.
  `VwREPORTHISPO` (PO) doesn't exist, but `VWREPORTHISSO` (SO) does, with the exact four columns
  (`NOBUKTI`, `TANGGAL`, `KODEBRG`, `NAMABRG`) the broken PO version selected. That's strong evidence the
  method was copy-pasted from an SO report and renamed without ever creating the PO view — useful context
  to leave in a comment/commit message, not something to "fix" by creating the missing view yourself.
- If nothing calls the method (see §2.2 step 1), delete it and its route instead of trying to repair the
  query.

`VwREPORTHISPO` specifically appears in **13 files** as of 2026-08-27 (`grep -rl "VwREPORTHISPO\|VWREPORTHISPO" app/Http/Controllers`) — treat each as a candidate for the same dead-`doFilter`/`doReportFilter` deletion, but verify per-file that it's actually unused before deleting.

### 2.4 Unauthenticated `auth()->user()->...`

```php
// before
$userid = auth()->user()->username;

// after
$user = auth()->user();
if (! $user) { return response()->json([], 401); }
$userid = $user->username;
```

Applies to routes with **no** `->middleware('auth')` that are still hit only via authenticated pages in
practice, but crash instead of failing gracefully if hit directly by a guest. Return `401` + JSON (not an
HTML redirect) when the route is an AJAX endpoint (`$.ajax` call from a Blade `<script>` block) — an HTML
redirect response gets silently treated as garbage JSON on the client.

**Do not** add `->middleware('auth')` to these routes instead — see §7, it's not currently safe repo-wide.

### 2.5 `$req->get()` → `$req->query()`

Purely cosmetic/idiomatic — `$req->get()` still works. Only worth doing on `Route::get` endpoints where
you're already touching the method for one of the fatal fixes above; not worth a dedicated pass on its
own.

---

## 3. Step-by-step workflow for one controller

1. Open the controller and its route file (`routes/<domain>.php`) side by side.
2. Grep the file for the patterns in §1.
3. For each `array_add()` hit: check what it's building and replace with a plain append (§2.1).
4. For each `MGL`/missing-view hit: find the corresponding Blade view, search its `<script>` block for the
   route name to determine if it's actually called (§2.2, §2.3). Delete dead methods + their routes
   together; only repair genuinely-used ones, and only after confirming the real target exists.
5. For each unauthenticated `auth()->user()` chain: check whether the route has `->middleware('auth')`. If
   not, add the null guard (§2.4).
6. Leave everything else — including commented-out dead code you're not directly resolving, and the
   controller's existing indentation/brace style — alone. Don't reformat the whole file; this codebase
   doesn't follow the Pint preset anywhere (see §5).
7. Verify per §5.
8. Report back which methods/routes were changed vs. deleted, and why (see the two examples in §8).

---

## 4. Note on `auth` middleware (repo-wide blocker)

Adding `->middleware('auth')` to a fatal-crash-prone route is **not currently safe**: there is no route
named `login` in `routes/`, and `bootstrap/app.php` does not set `redirectGuestsTo`. A guest hitting a
protected route would get `RouteNotFoundException: Route [login] not defined` instead of a redirect. Fix
via §2.4's null-guard pattern instead, until someone adds `->redirectGuestsTo('/')` to `bootstrap/app.php`
as a separate, deliberate, global change.

---

## 5. Verification

```bash
# no removed APIs remain in the file you touched
grep -n "array_add\|connection('MGL')" app/Http/Controllers/Report/<TheController>.php

# lint with PHP 8.3 (Laragon's default on PATH may be an older PHP — check the version first)
/c/laragon/bin/php/php-8.3.26-Win32-vs16-x64/php.exe -l app/Http/Controllers/Report/<TheController>.php
/c/laragon/bin/php/php-8.3.26-Win32-vs16-x64/php.exe -l routes/report.php
```

Browser smoke test — page behaviour must be **identical** to before the fix, minus the crash:

- Open the report page → any dropdown-populating AJAX call (e.g. `_loadperkiraan`) succeeds.
- Run the report with a normal filter → table + footer render as before.
- Log out, then hit the guarded AJAX endpoint directly → `401` + `[]`/`{}`, not a `500`.

**Note on Pint.** `vendor/bin/pint --test` will report `fail` on almost any file in this domain — it fails
with the same fixer list on completely untouched files (2-space indent, same-line braces is the house
style here, not the Pint preset). This is pre-existing everywhere, not something introduced by your fix;
match the surrounding style rather than reformatting.

Regenerate the tracking checklist counts in §6:

```bash
grep -rl "array_add("              app/Http/Controllers | wc -l
grep -rl "connection('MGL')"       app/Http/Controllers | wc -l
grep -rl "VwREPORTHISPO\|VWREPORTHISPO" app/Http/Controllers | wc -l
```

---

## 6. Repo-wide tracking checklist

Snapshot from **2026-08-27**. Tags: `array_add` = has `array_add()`, `MGL` = has `DB::connection('MGL')`,
`HISPO` = queries `VwREPORTHISPO`. Paths are relative to `app/Http/Controllers/`. Check a box only after
verifying with the grep in §5 that the pattern is actually gone from that file — don't check it off just
because a related file was fixed.

Already fixed (kept here for reference, not because they're still open):
- [x] `Report/LaporanAccountingKasHarianController.php` — was array_add, MGL, HISPO (see §8.1)
- [x] `Report/LaporanAccountingPiutangPelunasanController.php` — was array_add-adjacent pattern (see §8.2; `doFilter`/`doReportFilter` were already commented out here, not live)

Two filenames are almost certainly **dead, unrouted files** — confirmed by grepping `routes/report.php`
for their class name and finding no match. Don't spend time fixing these; if anything, flag for deletion:
- `Report/LaporanClosingTransferCabang.php` (only `LaporanClosingTransferCabangController.php` is routed)
- `Report/LaporanMarketingSOController(old).php`, `Report/LaporanMarketingSPBController(old).php` (literal `(old)` filenames; only the non-`(old)` versions are routed)

Remaining (110 files as of the snapshot; regenerate with the §5 commands before relying on this list):

- [ ] `GlobalFunctionsController.php` (root, not `Report/`) — MGL
- [ ] `Report/FunctionBrowseController.php` — MGL
- [ ] `Report/FunctionGlobalController.php` — MGL
- [ ] `Report/GlobalFunctionsController.php` — MGL
- [ ] `Report/LaporanAccountingAktivaController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingBiayaController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingBiayaPenyusutanController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingCostingController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingHPPController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingHutangLHPJTController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingHutangLPHController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingHutangLPHTOController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingHutangOutstandingNotaController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingHutangPelunasanController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingHutangUmurController.php` — array_add, MGL, HISPO
- [ ] `Report/LaporanAccountingJurnalComputerController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalKoreksiController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalMemorialController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalPenerimaanBankController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalPenerimaanKasController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalPengeluaranBankController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalPengeluaranKasController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingJurnalPenutupController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingLabaRugiController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingLabaRugiTahunanController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingLaporanArusController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingNeracaController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingNeracaLajurController.php` — MGL
- [ ] `Report/LaporanAccountingNeracaPenunjangController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingPiutangLPPController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingPiutangLPPTOController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingPiutangSPJTController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingPiutangUmurController.php` — array_add, MGL
- [ ] `Report/LaporanAccountingSKBController.php` — array_add, MGL
- [ ] `Report/LaporanClosingOpnameController.php` — array_add, MGL
- [ ] `Report/LaporanClosingPermintaanTransferController.php` — array_add, MGL
- [ ] `Report/LaporanClosingTransferCabangController.php` — array_add, MGL
- [ ] `Report/LaporanGudangKonsinController.php` — array_add, MGL
- [ ] `Report/LaporanGudangOutKonsinsController.php` — array_add, MGL
- [ ] `Report/LaporanGudangOutPRKonsinController.php` — array_add, MGL
- [ ] `Report/LaporanGudangOutstandingSoPoController.php` — array_add, MGL
- [ ] `Report/LaporanGudangPembebananSampleController.php` — MGL
- [ ] `Report/LaporanGudangPermintaanKonsinyasiController.php` — array_add, MGL
- [ ] `Report/LaporanGudangReturSampleController.php` — array_add, MGL
- [ ] `Report/LaporanHisPoController.php` — array_add, MGL
- [ ] `Report/LaporanHistoriPenyerahanSampleController.php` — array_add, MGL
- [ ] `Report/LaporanKoreksiStockController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingAnalisaKotorController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingEvalSoLunasController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingHistoryOutSOController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingHistorySOController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingInvoiceController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingLaporanOutSoController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingOutGudangGTCController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingOutSPBHrgSoController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingOutSPPBController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingPOSController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingRegSaleInvController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingRegUangMukaController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingReturPenjualanController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingSOController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingSPBACCController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingSPBController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingSPBHrgSOController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingSPBLTController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingSalesAnalisaController.php` — array_add, MGL
- [ ] `Report/LaporanMarketingUangMukaOutController.php` — array_add, MGL
- [ ] `Report/LaporanOpnameController.php` — array_add, MGL
- [ ] `Report/LaporanOutstandingPermintaanSampleController.php` — array_add, MGL
- [ ] `Report/LaporanOutstandingTransferController.php` — array_add, MGL
- [ ] `Report/LaporanPemakaianController.php` — array_add, MGL
- [ ] `Report/LaporanPenerimaanGudangController.php` — array_add, MGL
- [ ] `Report/LaporanPenerimaanGudangOSPOController.php` — array_add, MGL
- [ ] `Report/LaporanPenerimaanTransferController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanClosingPRController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanDebetNoteController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanInvoicePembelianController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanOutStandingUM2Controller.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanOutStandingUMBeliController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanPRController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanRegisterRekapOSInvoiceController.php` — array_add, MGL
- [ ] `Report/LaporanPengadaanRekapInvoicePembelianController.php` — array_add, MGL
- [ ] `Report/LaporanPenyerahanSampleController.php` — array_add, MGL
- [ ] `Report/LaporanPerintahOpnameController.php` — array_add, MGL
- [ ] `Report/LaporanPermintaanPemakaianController.php` — array_add, MGL
- [ ] `Report/LaporanPermintaanSampleController.php` — array_add, MGL
- [ ] `Report/LaporanPermintaanTransferController.php` — array_add, MGL
- [ ] `Report/LaporanPurchaseOrderClosePOController.php` — array_add, MGL
- [ ] `Report/LaporanPurchaseOrderOSPController.php` — array_add, MGL
- [ ] `Report/LaporanPurchaseOrderPOController.php` — array_add, MGL
- [ ] `Report/LaporanRegisterPembelianController.php` — array_add, MGL
- [ ] `Report/LaporanReturPembelianACCController.php` — array_add, MGL
- [ ] `Report/LaporanReturPembelianGDGController.php` — array_add, MGL
- [ ] `Report/LaporanSelisihPerintahOpnameController.php` — array_add, MGL
- [ ] `Report/LaporanStockFastSlowDeadMovingController.php` — MGL
- [ ] `Report/LaporanStockKartuStockController.php` — MGL
- [ ] `Report/LaporanStockMutasiStockController.php` — MGL
- [ ] `Report/LaporanStockMutasiStockHarianController.php` — MGL
- [ ] `Report/LaporanStockMutasiStockPerMerkController.php` — MGL
- [ ] `Report/LaporanStockSaldoStockController.php` — MGL
- [ ] `Report/LaporanStockStockFisikGudangController.php` — MGL
- [ ] `Report/LaporanStockStockKartuDanOpnameController.php` — MGL
- [ ] `Report/LaporanSuratJalanController.php` — array_add, MGL
- [ ] `Report/LaporanTransferController.php` — array_add, MGL
- [ ] `Report/LaporanTransferKeCabangBlmDiterimaController.php` — array_add, MGL
- [ ] `Report/LaporanUbahKemasanController.php` — array_add, MGL
- [ ] `Report/ReportTesController.php` — MGL

---

## 7. Outstanding / not yet decided

- **`auth` middleware repo-wide.** See §4 — blocked on a `bootstrap/app.php` change (`redirectGuestsTo`)
  that's outside the scope of any single controller fix.
- **`MGL` connection registration.** Still undecided whether `MGL` should ever be registered for real
  (pointing at `DBMGL2`) or whether every reference to it is dead code destined for deletion. Decide this
  per-file per §2.2 rather than assuming one global answer.
- **`(old)`-suffixed and unrouted duplicate files** (§6) — candidates for outright deletion, not fixing,
  but confirm via `routes/` grep per file before removing anything.

---

## 8. Worked examples

### 8.1 `LaporanAccountingKasHarianController` (first case, full writeup)

Page: **Laporan Accounting → Kas Harian**, view `resources/views/report/reportaccountingkasharian.blade.php`.

**`doReport()` / `doReportSaldoAwal()`** — `$req->get(...)` → `$req->query(...)` for `date1`, `date2`,
`inputPerkiraan`, `detOrRekap`. Stored-procedure calls (`sp_LapKasHarian`, `sp_LapSaldoAwal`) were verified
to exist and were left untouched — argument order confirmed correct against the database.

**`doFilter()` & `doReportFilter()` — deleted**, along with their routes in `routes/report.php`. Three
independent reasons: (1) never called — the Blade view only hits `_doReport`, `_saldoawal`,
`_loadperkiraan`; (2) both queried `VwREPORTHISPO`, a *Purchase Order* history view unrelated to a daily
*cash* report — copy-paste residue from a marketing report; (3) `VwREPORTHISPO` doesn't exist on the
server at all (checked `DBSMLNEW`, `DBMGL2`, `DBSML`, `DBSML2`, `DBETM`). Repairing `MGL` → `SML` was
considered and rejected — it only swaps one fatal error for another (`Invalid object name`), since there's
no target that makes the endpoint work. Note left for future reference: `VWREPORTHISSO` (Sales Order)
exists in `DBSMLNEW` with the exact four columns (`NOBUKTI`, `TANGGAL`, `KODEBRG`, `NAMABRG`) the deleted
`doFilter` selected — strong evidence of a copy-paste-and-rename-gone-wrong from an SO report.

**`loadPerkiraan()`** — added the null-guard from §2.4 (this route has no `auth` middleware). Tables
`DBPOSTHUTPIUT`, `DBPERKIRAAN`, `DBAKSESPERKIRAANR` were verified to exist.

**`index()` — left unchanged.** Its `if ($akses['userLoggedOut'])` guard is dead code (that flag is only
ever set inside a commented-out block in `AksesTrait`), but the same pattern appears in 100+ other
controllers — changing it here alone would create an inconsistency for no gain. Leave this kind of
repo-wide-pattern dead code alone unless you're doing a repo-wide pass on it specifically.

### 8.2 `LaporanAccountingPiutangPelunasanController` (second case, confirms the pattern generalizes)

Page: **Laporan Accounting → Piutang → Pelunasan**. This controller confirmed the same playbook applies
cleanly to an independent controller, with two differences worth noting:

- Its `doFilter()`/`doReportFilter()` equivalent (`VwREPORTHISPO`-querying block) was **already commented
  out**, not live — so no route deletion was needed, just leaving the comment as-is (or removing the dead
  comment block, at the fixer's discretion).
- `loadPerkiraan()` got the identical §2.4 null-guard fix (`auth()->user()->username` → guarded), i.e. this
  isn't a one-off — expect the same unauthenticated-AJAX-endpoint shape in most `Laporan*Controller`s that
  have a `loadPerkiraan`/`loadSupp*`/similar dropdown-populating method.

Also, in the course of unrelated work on this controller's `ReportVoucherTrait` usage, a `doKasharian` /
`doInvoice` / `doLpb` set of routes were added — unrelated to this deprecation cleanup, mentioned here only
so it isn't mistaken for part of the pattern above.
