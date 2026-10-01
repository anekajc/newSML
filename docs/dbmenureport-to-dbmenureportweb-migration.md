# DBMENUREPORT → dbmenureportweb migration (AksesTrait)

Date: 2026-08-29
Branch: `noelganteng`

## What changed

`AksesTrait::cekAkses()` — called by all ~119 `Report/*Controller.php` `index()`
methods — used to source three things from the old `DBMENUREPORT` /
`DBFLMENUREPORT` tables:

1. **Per-user access flag** (`$akses['akses']->Access`), via
   `AksesTrait::getAksesMenu($href)` joining `DBFLMENUREPORT.L1 =
   DBMENUREPORT.KODEMENU`.
2. **Page title** (`$akses['namamenu']`), via `NewMenuReport::where('href',
   $href)->first()->Keterangan` (`NewMenuReport` model → table
   `DBMENUREPORT`).
3. **Report sidebar tree** (`$akses['menul0']`), via
   `NewMenuController::getMenuL0Report()`, same `DBFLMENUREPORT` ⋈
   `DBMENUREPORT` join, 4 fixed levels.

This broke for any report whose href only exists in the newer
`dbmenureportweb` table (the one the client-rendered sidebar in
`resources/views/newmaster.blade.php` and `HomeController::getMenuReport()`
already use) — e.g. `laporanopname`.

### Root cause (confirmed live against the SML database)

- `DBFLMENUREPORT.L1` (343-row `DBMENUREPORT`'s KODEMENU scheme, e.g. `04080`
  = "Laporan Opname Barang") and `dbmenureportweb.KODEMENU` (134 rows,
  renumbered independently, e.g. `04030` = "Opname & Koreksi" for
  `href=laporanopname`) **do not correlate** — neither by code nor reliably
  by href. `DBMENUREPORT` has no row with `href='laporanopname'` at all.
- No other table maps per-user permissions to `dbmenureportweb`'s scheme.
  `new_aksesmenureport` / `new_menureport` looked promising by name but are a
  separate, unrelated 14-row report menu (just stock reports: Kartu Stok,
  Stok Lokasi, etc.), keyed by a **numeric** `USERID` instead of username.
- Of the 118 real hrefs passed to `cekAkses()` across the codebase, only 79
  exist in `dbmenureportweb` today; 39 don't (checked live). Any fix has to
  tolerate that gap rather than assume full coverage.

### What replaced it

- `getAksesMenu()` is gone. `$akses['akses']` is now a stub
  `new Fluent(['Access' => true, 'IsDesign' => true, 'Isexport' => true])` —
  every authenticated user has access, since no real per-user permission
  table exists for `dbmenureportweb`'s scheme. **This is the actual behavior
  change**, not an implementation detail: report-level access control is
  currently a no-op app-wide.
- `namamenu` now comes from a direct `dbmenureportweb` lookup by href, falling
  back to the raw href string if the row isn't there (covers the 39 missing).
- `menul0` now comes from `HomeController::getReportMenuTreeArray()` (new
  method — same tree `HomeController::getMenuReport()` already serves to the
  JS sidebar, just without the JSON response wrapper), recursively wrapped in
  `Illuminate\Support\Fluent` via a new private `toFluentMenuTree()` helper.
  The `Fluent` wrapping exists because `report/newmaster2.blade.php` and
  `newmaster2x.blade.php` access tree nodes both ways (`$menu0->href` *and*
  `$menu0['Keterangan']`) — that only worked before because `NewMenuReport`
  rows were Eloquent models (dual-access natively); plain arrays only support
  `['key']`.

## Files changed

- `app/Traits/AksesTrait.php` — `cekAkses()` rewritten as above;
  `getAksesMenu()` removed; new private `toFluentMenuTree()`; import swapped
  `App\Models\NewMenuReport` → `Illuminate\Support\Fluent`.
- `app/Http/Controllers/HomeController.php` — added `getReportMenuTreeArray()`
  (purely additive, wraps the existing private `buildReportTree()`).
- 11 `Report/*Controller.php` files (`LaporanOpnameController`,
  `LaporanMarketingEvalSoLunasController`,
  `LaporanMarketingLaporanOutSoController`,
  `LaporanMarketingRegSaleInvController`,
  `LaporanMarketingReturPenjualanController`, `LaporanMarketingSPBController`,
  `LaporanMarketingSPBLTController`, `LaporanMarketingSalesAnalisaController`,
  `LaporanMarketingUangMukaOutController`, `LaporanPurchaseOrderPOController`,
  `LaporanUbahKemasanController`) — **no longer touched by this migration**;
  an earlier same-session bypass (`// TEMPORARY BYPASS` comments around the
  `if ($akses['akses']->Access)` gate) was added and then reverted back to
  the original `if (...) {...} else {...}` structure once the `Fluent` stub
  made the gate always-true again. Nothing to revert here for this migration
  specifically — see git history on those files if their *other* unrelated
  changes (e.g. `MGL` → `SML` connection swaps) ever need separate review.

## How to revert to DBMENUREPORT

Only `AksesTrait.php` and `HomeController.php` need to change. The cleanest
way, since both are tracked and this migration is the only pending change to
either file:

```bash
git checkout -- app/Traits/AksesTrait.php app/Http/Controllers/HomeController.php
```

This restores:
- `getAksesMenu()` and the `DBFLMENUREPORT ⋈ DBMENUREPORT` join.
- `NewMenuReport::where('href', $href)->first()` for `namamenu`.
- `NewMenuController::getMenuL0Report()` (unchanged this whole time — it was
  never called to switch away from) for `menul0`.
- Removes `HomeController::getReportMenuTreeArray()`.

**Reverting does not fix anything** — it puts back the exact join that was
broken to begin with (see Root cause above). It only makes sense if either:
(a) `DBFLMENUREPORT`/`DBMENUREPORT` turn out to still be correct for reports
*other* than the ones checked here and the `dbmenureportweb` swap caused a
regression somewhere unexpected, or (b) the team decides real per-user report
permissions matter more than working reports and wants the old (broken but
at least *fails closed*) behavior back while a proper fix is built.

### Manual revert (if `git checkout` isn't viable — e.g. other unrelated
changes have landed in these files since)

**`app/Traits/AksesTrait.php`** — restore the import:
```php
use App\Models\NewMenuReport;
```
(remove `use Illuminate\Support\Fluent;` if nothing else in the file needs
it), restore inside `cekAkses()`:
```php
$menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0Report(5);
```
and, inside the `if ($href != "Home")` branch:
```php
$menu = NewMenuReport::where('href' , $href)->first();
$aksesmenu = $this->getAksesMenu($href);
$akses = Arr::add($akses, 'akses', $aksesmenu[0]);
$akses = Arr::add($akses, 'namamenu', $menu->Keterangan);
```
and restore the `getAksesMenu()` method (deleted, was right after
`cekAkses()`'s closing brace):
```php
public function getAksesMenu($href) {
    if (!\Auth::check()) {
        return array();
    }

    return DB::connection('SML')->select('select fl.* from DBFLMENUREPORT fl left outer join DBMENUREPORT m on (fl.L1 = m.KODEMENU) where fl.UserID = :user and m.href = :href' , ['user' => \Auth::User()->username, 'href' => $href]);
}
```
Remove the `toFluentMenuTree()` private method (added by this migration,
nothing else uses it).

**`app/Http/Controllers/HomeController.php`** — remove the
`getReportMenuTreeArray()` method (it's additive-only; leaving it in place is
harmless if you don't want to touch this file, since `AksesTrait.php` alone
determines whether it's called).

## If you want per-user report permissions back for real (not just reverted)

Reverting does **not** get you working per-user permissions — it gets you the
same broken join this migration started from. A real fix needs a permission
table keyed on `dbmenureportweb.KODEMENU` (or equivalently on `href`) per
`UserID`/username, e.g. either:
- Rebuild `DBFLMENUREPORT` (or a new table) with `L1` values matching
  `dbmenureportweb.KODEMENU` instead of the old `DBMENUREPORT` scheme, or
- Add per-user grants directly against `dbmenureportweb.href`.

That's a data/backend task, not a code change to `AksesTrait.php`.
