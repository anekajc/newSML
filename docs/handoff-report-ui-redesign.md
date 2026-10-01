# Handoff — Report UI redesign (Customize Table, interactive table header, SO filter/picker)

Status as of **2026-08-10**. Written for a fresh session picking this up.
Repo has **no git** (`fatal: not a git repository`), so there is no diff to read — this file
is the only record of what changed and why.

> **Update (2026-08-10, later same day):** `public/css/report-table-v2.css` and
> `public/js/report-table-v2.js` (described in §3 below) were merged into
> `public/css/report-table.css` and `public/js/report-table.js` respectively, and are no longer
> loaded by `newmaster2.blade.php` / `masterreport2.blade.php` — the v2 files stay on disk as an
> archived copy but are not the live source. The JS API was renamed `window.ReportTableV2` →
> `window.ReportTable` (no alias); the CSS custom properties `--rtv2-*` → `--rt-*`; and the bar
> `<div id="rtv2Bar">` → `<div id="rtBar">` in both pages that use it. `.rt-picker-v2` /
> `window.g_pickerV2` (shared by `modalMarketingSO.blade.php`) were intentionally left unchanged.
> Everywhere §3 below says `report-table-v2.js` / `ReportTableV2` / `#rtv2Bar`, read
> `report-table.js` / `ReportTable` / `#rtBar` instead.

---

## TL;DR

Four connected pieces of work landed, all in the `report` module:

1. **"Atur Kolom" modal** (`#formCustomizeTable`) fully redesigned in `masterreport2.blade.php`.
2. A **`toInteger()` crash** that this introduced was found and fixed.
3. **Interactive table headers** (drag to reorder columns + per-column gear menu) added as
   shared `report-table-v2.{css,js}`, wired into **2 pages so far**.
4. **SO page's Filter modal + entity picker** redesigned, plus a real bug fix for a missing
   `inputOrd` parameter.

All work is UI-layer. The persisted data contract (`DBSIMPANHEADER`, the `;;`/`||` header
string, `Sp_ReportSODet` params) was **not** changed.

---

## Reference mockups used

Under `references/` (design targets, not shipped code):

| File | Drove |
|---|---|
| `customize-table-redesign-v2.html` | The "Atur Kolom" modal |
| `table-no-modal-concept.html` | Drag headers + gear menu + "kolom tersembunyi"/"Tampilan" bar |
| `filter-laporan-redesign.html` | SO Filter modal + entity picker |

---

## New files

| File | Purpose |
|---|---|
| `public/css/customize-table.css` | Styling for the "Atur Kolom" modal. **Every selector scoped under `.ct-modal`** so the other `masterreport*` files (which still use the old Bootstrap-grid markup) are untouched. |
| `public/css/report-table-v2.css` | Two concerns: (a) interactive `<thead>` + the bar above the table, scoped under `.tb-report`; (b) SO Filter modal + picker, scoped under `#modalFilter.rt-filter` / `#formSelect.rt-picker-v2`. |
| `public/js/report-table-v2.js` | `window.ReportTableV2` — IIFE guarded by `if (window.ReportTableV2) { return; }`. |

All three are loaded **globally** from the shared layouts (see below) rather than per page,
because many report pages will eventually want them. Pages that never call
`ReportTableV2.init()` are completely unaffected.

---

## 1. "Atur Kolom" modal — `resources/views/report/masterreport2.blade.php`

Replaced the old Bootstrap-grid modal (eye button / name / up / down, plus a **separate**
`#formSettingTotal` modal that dimmed its parent to `opacity: 0.6`) with:

- header + subtitle, preview strip of numbered chips showing visible columns in order,
  search box, scrollable column list, subtotal/grand-total switches, footer.
- Each row: grip · visibility switch · name · tags (`N desimal`, `total`) · gear · up · down.
- **Gear opens an inline panel** (`.ct-panel`) instead of a second modal — decimal stepper
  (0–4) + "Tampilkan total" switch.
- **Drag to reorder** is wired, and the Up/Down buttons were deliberately **kept** as a fallback.
- Esc is two-stage: first collapses an open gear panel, then closes the modal.

**`#formSettingTotal` was deleted**, along with `doCloseFormSettingTotal`,
`doSimpanFormSettingTotal`, `gsettotal_*`, `gmodal_settingtotal`. Verified by grep that no
other file referenced them.

Save semantics unchanged — every toggle still calls `doSimpanHeader` immediately.

### Deliberately NOT implemented (from the mockup)

- **Group labels** (Identitas/Nilai/Status) — no group field exists in `gcart_header`, and
  grouping conflicts with the flat global ordering index.
- **Pin / "sematkan"** — no slot in the 6-field `;;`-delimited persisted header string.
  Adding it means changing a format shared by ~80 report pages.

### Key contract to preserve

`doShowCustomize()` **must stay callable with no arguments** — ~80 report pages call it bare
(usually from their `doReportMode`/`setModeReport`). The search term is therefore read from
`#ct_search` *inside* the function, never passed as a parameter.

---

## 2. The `toInteger()` crash — `n.replace is not a function`

Worth knowing about, it is an easy trap to fall into again.

```js
// public/js/ajc-func-core.js:117
function toInteger(n) { return parseInt(n.replace(/,/g, "")); }
```

**It is string-only.** It calls `.replace()` directly on its argument, so passing a *number*
throws `n.replace is not a function`.

`gcart_header` flag fields are **already numbers** from both paths that populate them:
`setDefaultHeader()` uses numeric literals (`['tanggal','Tanggal', 1, 'date', 0, 0]`), and
`doGetHeader()` already ran `toInteger()` on the split strings. So wrapping them again crashes.

Every such call was changed to `Number(...)` (handles both numbers and numeric strings).
Also fixed a **pre-existing** instance in `doLoadHeader()`: `doLoadHeader` does `select *` from
`DBSIMPANHEADER`, whose `issubtotal`/`isgrandtotal` are `int` columns arriving as JSON numbers —
`toInteger(1)` threw there on every page that had a saved header.

`toInteger()` itself was **left alone** — it is used app-wide on comma-formatted input strings,
and loosening it is a far wider blast radius.

Remaining `toInteger()` calls in `masterreport2.blade.php` are correct: they operate on
`item.split(";;")[n]` and `row.getAttribute("data-idx")`, both strings.

---

## 3. Interactive table headers — `report-table-v2.{css,js}`

### How a page opts in

```js
ReportTableV2.init({
  table: '#mainTable',
  bar:   '#rtv2Bar',        // <div id="rtv2Bar"></div> placed above .table-outer
  onChange: render,         // the page's own table-render function
  views: {                  // OPTIONAL — only if the page has a rekap/detail filter
    label: 'Tampilan',
    options: [ { value:'0', label:'Detail', desc:'…' }, … ],
    get: function () { return globalReportMode; },
    set: function (v) { /* setReportMode(v) + reload/re-render */ }
  }
});
```

and in the page's render function, replace its hand-built `<thead>` with:

```js
thead.innerHTML = ReportTableV2.headHtml(cols);   // cols = gcart_header.filter(c => c[2] === 1)
```

### Design points a maintainer needs

- `cols` entries are **references** into `gcart_header`, so `indexOf(c)` recovers the true
  global index. That is what `data-gidx` on each `<th>` carries — so drag and the gear menu act
  on the right column even when some are hidden or a search filter is active.
- All mutations **delegate to the masterreport2 functions** (`doMoveHeader`,
  `doButtonVisibility`, `doSetDesimal`, `doButtonTotal`). Those already persist *and* call
  `doShowCustomize()`, so the header and the Atur Kolom modal stay in sync for free.
- **The gear menu renders into `<body>` as `position: fixed`.** `.table-wrap` is
  `overflow: auto`, so an absolutely-positioned popover inside a `<th>` gets clipped. It closes
  on outside-click, Esc, resize, and capture-phase scroll (so `.table-wrap` scrolling counts).
- The "N kolom tersembunyi" button opens a dropdown listing hidden columns; clicking one
  restores it.
- Visual approach: **kept** the existing `.tb-report .tb` look (sticky header, `subtotal-row`,
  `grand-total` — none of which the mockup has, and both pages depend on them) and layered only
  the new affordances on top.

### Wired into (2 pages so far)

- `resources/views/report/reportaccountingkasharian.blade.php` — Tampilan = **Rp / Valas**
  (its `modereport_detail`/`modereport_rekap`). Mode is sent to the proc as `detOrRekap`, so
  switching re-runs `makeTable('REPORT')` when data is already loaded.
- `resources/views/report/reportmarketingso.blade.php` — Tampilan = **Detail / Rekap**,
  mirroring `#modalReport`. Mode only changes the column set (not the query), so it just
  re-renders from `lastRows`.

Both keep their original mode controls in place; everything routes through `setReportMode()`
so the two stay in sync rather than competing.

---

## 4. SO Filter modal + entity picker

### `resources/views/report/reportmarketingso.blade.php` — `#modalFilter`

Rebuilt per `filter-laporan-redesign.html`: `class="modal fade rt-filter"`, sections with
uppercase group labels, `N aktif` badge, `.rt-native` selects, and `.rt-combo` boxes for the six
lookup fields, footer = `Reset semua` / `Batal` / `Terapkan`.

**Critical:** the six lookup fields became `<input type="hidden" id="inputCustomer" value="-">`
etc. `makeTable()` reads those ids and `buttonPilih()` writes to them — the visible `.rt-combo`
box is only a rendered view over the hidden input. New helpers: `renderPickFields()`,
`clearPickField()`, `updateFilterBadge()`, `resetAllFilters()`.

### `resources/views/report/modalMarketingSO.blade.php` — `#formSelect`

This file is `@include`d by **35 live report pages**, so the picker redesign is **opt-in**:

```js
window.g_pickerV2 = true;   // set ONLY by reportmarketingso.blade.php
```

Two helpers, `pickerHeadHtml(cols)` and `pickerRowHtml(idPart, kode, cellsHtml)`, branch on that
flag. When it is falsy the emitted markup is **character-for-character identical to before**
(trailing `Actions` column + green `Select` button) — that is what protects the other 34 pages.
When true: no Actions column, `<tr class="pick-row">` clickable, hover darkens.
`buttonSelect()` toggles `.rt-picker-v2` on `#formSelect` for the CSS.

All six loaders were refactored onto those helpers. DataTables init left untouched (its search /
info / pagination are skinned via CSS instead of replaced).

Also fixed a stray unbalanced `</button>` in the modal header.

### Constraint that must not break

`reportmarketingso.blade.php` hides `#modalFilter`, opens `#formSelect`, and reopens the filter
on `hidden.bs.modal` (`g_reopenFilter`). **Keep the id `#formSelect` and keep that event firing**
or the Filter modal never comes back after picking.

---

## 5. The `inputOrd` bug (last thing worked on)

Symptom the user hit — network payload showed an empty 2nd param:

```
exec Sp_ReportSODet T,,2026-06-01,2026-08-09,,2,,,C024,-,-,-,-,-,2
                     ^ should be N
```

Two independent causes:

**Client (FIXED).** `applyModalFilter()` called `setOrderBy($("#modalOrder").val())`, but the
Order By block is commented out in the DOM. `.val()` on a non-existent element returns
`undefined`, permanently corrupting `globalOrderBy` on the first **Terapkan** click. Two
consequences: jQuery `.ajax()` **drops keys whose value is `undefined`**, so `inputOrd` stopped
being sent at all; and `setModeReport()`'s `if/else if` chain stopped matching, so `groupby` in
`makeTable()` silently fell back to `''`, breaking subtotal grouping too. Now guarded with
`if ($("#modalOrder").length) { … }`.

**Server (NOT fixed — reverted by the user).**
`app/Http/Controllers/Report/LaporanMarketingSOController.php:45` originally read:

```php
$Ordr = $req->get('inputOrd') or '-';   // ← operator-precedence trap
```

PHP's `or` binds looser than `=`, so this parses as
`($Ordr = $req->get('inputOrd')) or ('-');` — the `'-'` fallback is dead code and `$Ordr`
becomes `null` when the key is absent. It was changed to `?: '-'`, but **the user reverted it**;
line 45 is currently:

```php
$Ordr    = $req->get('inputOrd');
```

> **Open question for the next session:** confirm whether the user wants that server-side
> fallback at all. With the client fix in place `inputOrd` should always arrive as `"N"`, so the
> revert is harmless today — but there is now no safety net if any other caller omits it.
> Do not re-apply it without asking.

---

## Files touched

**New**
- `public/css/customize-table.css`
- `public/css/report-table-v2.css`
- `public/js/report-table-v2.js`

**Modified**
- `resources/views/report/newmaster2.blade.php` — 2 added `<link>` tags (customize-table,
  report-table-v2), each with the existing `filemtime` cache-buster pattern.
- `resources/views/report/masterreport2.blade.php` — Atur Kolom modal rewrite; deleted
  `#formSettingTotal`; `toInteger`→`Number` fixes; added the `report-table-v2.js` `<script>`
  next to `report-table.js`, **before** `@yield('jsreport')` so `ReportTableV2` exists before
  page scripts run.
- `resources/views/report/reportaccountingkasharian.blade.php` — `#rtv2Bar`, hint line,
  `thead` swap, `ReportTableV2.init()`.
- `resources/views/report/reportmarketingso.blade.php` — same four, plus the whole Filter modal
  rewrite, `window.g_pickerV2 = true`, and the `#modalOrder` guard.
- `resources/views/report/modalMarketingSO.blade.php` — picker helpers + 6 loaders refactored.
- `app/Http/Controllers/Report/LaporanMarketingSOController.php` — change was reverted, see §5.

---

## Known open items / gotchas

1. **`#kasSummary` colspans.** `reportaccountingkasharian.blade.php` renders its saldo/signature
   block as a second `<tbody>` inside `#mainTable` with hardcoded `colspan="10"` / `"9"`. Hiding
   or reordering columns skews it. Pre-existing (the Customize Table modal could already hide
   columns), but the new header gear makes hiding *much* easier to reach, so it will be hit
   sooner. Fix = derive those colspans from the visible column count. **Was offered, not yet
   requested.**
2. **Five sibling picker modals** (`modalAccountingJurnal`, `modalMarketingHistorySO`,
   `modalMarketingRegUangMuka`, `modalMarketingInvoice`, `modalStock`) clone `#formSelect`,
   `#tabelSelect`, `buttonSelect`/`buttonPilih` verbatim. They were **not** restyled — the user
   explicitly scoped the redesign to SO only. Two can never coexist on one page.
3. **`loadSelectMerk` column swap** — headers print `Nama, Kode Merk` but cells emit `KodeMerk`
   then `NamaMerk`. Pre-existing, deliberately left alone (affects all 35 pages), commented
   in-place.
4. **Rollout.** `report-table-v2` is live on only 2 of ~80 report pages. Adding a page is:
   `<div id="rtv2Bar">`, swap the `thead` line, call `init()`. The `views:` block is optional —
   per the user's rule, only show "Tampilan" where the page actually has a rekap/detail filter.
5. **Bootstrap 4 + 5 both load.** `newmaster2` loads BS4 JS; `masterreport2` has stray BS5 CDN
   tags outside any `@section` which *do* still reach the browser. Net effect: `$().modal()` is
   BS4's jQuery plugin, while `data-bs-*` attributes are handled by BS5's data-api. Both work —
   existing code mixes them freely, so match whatever the surrounding file already does.
6. **No test suite** covers views (`tests/` has stubs only). Verification is manual in the browser.

---

## Verification checklist

Hard-refresh (Ctrl+F5) first — CSS is `filemtime`-busted but browsers still cache during rapid edits.

**Atur Kolom modal** (any page extending `masterreport2`)
- Opens with chips + search; toggling a switch updates chips and persists.
- Gear on a numeric column expands the inline panel; stepper and total switch persist.
- Drag a row and use Up/Down — both reorder and persist.
- Esc collapses the panel first, then closes the modal.

**Interactive header** (kasharian + SO)
- Drag a `<th>` to reorder; hidden-columns dropdown restores.
- Gear menu is not clipped when the table is scrolled sideways.
- Tampilan switches mode and the modal/toolbar control stays in sync.

**SO Filter + picker**
- Filter modal matches the reference; badge counts correctly; Reset semua clears everything.
- Clicking a combo hides the filter, opens the picker with **no Select button**, row hover
  darkens, clicking a row fills the combo **and the Filter modal reopens** (the `g_reopenFilter`
  path — this is the one most likely to regress).
- The `✕` on a tag clears to `-` and does **not** open the picker.
- Pick only Customer → Tampilkan → Network tab shows `T,N,…` (not `T,,…`) and the five unset
  lookups as `-`.

**Regression — the other 34 picker pages (most important)**
- Open e.g. `reportlaporansuratjalan` or `reportgudangkonsin`, open its picker: `Actions` column
  present, green `Select` button works. `window.g_pickerV2` is undefined there, so both helpers
  take the legacy branch.
