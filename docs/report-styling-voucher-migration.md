# Report Migration Guide — Styled `.tb-report` + Voucher Opening

How to bring an accounting report page onto the new **styled `.tb-report`** design (the
look shared by the already-updated reports) and, where the data has a **No Nota** or
**No Bukti** column, make those cells **open the source voucher** in the bottom panel.

This is the pattern extracted from the pages already migrated. Copy from one of the
**reference implementations** and adapt.

---

## 1. What "migrated" means

A migrated report:
- Extends **`report.masterreport2`** (which loads `public/css/report-table.css` **and**
  `public/js/report-table.js`, both via `report/newmaster2.blade.php`).
- Renders a **styled `.tb-report`** table **client-side** from `gcart_header` + the raw
  rows returned by `doReport` (no more `doMakeTable`/`#tabel` server rendering).
- Groups rows per entity (supplier / customer) with **Subtotal + Grand Total** driven by
  the Customize Table toggles.
- Has a styled toolbar (Periode, optional Valas, Perkiraan, entity range, Order By…),
  client-side **search**, **Export** (XLSX/CSV/Print), a **loading spinner**, and the
  **Customize Table** modal.
- If it has a **No Nota** (`nofaktur`/`NoFaktur`/`Nofaktur`) and/or **No Bukti**
  (`nobukti`/`NoBukti`) column, those cells are **clickable** and open the voucher via
  the shared `report-table.js` bottom panel.

## 2. Reference implementations (copy from these)

| Report | Notes |
|---|---|
| `reportaccountingpiutangkartu.blade.php` | Valas mode, Perkiraan **dropdown**, entity modal, No Nota + No Bukti clickable, **Pelanggan** labels |
| `reportaccountinghutangpelunasan.blade.php` | No LPB + No Bukti Bayar clickable, **Supplier** labels |
| `reportaccountingpiutangoutstandingJT.blade.php` | extra filters (PIC, Lokasi, Group By), single date, no Valas |
| `reportaccountinghutangoutstandingnota.blade.php` | single clickable column (No Nota only) |
| `reportaccountingjurnalpenerimaankas.blade.php` | **two-level header** (Debet/Kredit), **Divisi dropdown**, group by `NoBukti`, No Bukti (BKM) → Bukti Kas |
| `reportaccountingpiutanglppto.blade.php` | **flat table** (one row per entity, no grouping), Grand Total only, Valas feeds SP |

Controllers pull shared endpoints from **`App\Traits\ReportVoucherTrait`**; the bottom
panel + helpers live in **`public/js/report-table.js`**.

---

## 3. Migration steps (per page)

### 3.1 Controller — add the voucher endpoints
```php
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;   // <-- add

class LaporanAccounting<X>Controller extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;            // <-- doKasharian / doInvoice / doLpb / doBp
  // index() + doReport() stay per-controller (each calls its own stored proc).
}
```
No collision risk: the trait's `doKasharian/doInvoice/doLpb/doBp` (+ `doLedger`,
`doFilter`, `doReportFilter`, `loadDivisi`) don't clash with the typical
`index/doReport/loadPerkiraan/loadValas/loadSuppAwal` a report defines.

### 3.2 Routes — add four endpoints next to `_doReport`
```php
Route::get('/<route>_doKasharian', 'LaporanAccounting<X>Controller@doKasharian');
Route::get('/<route>_doInvoice',   'LaporanAccounting<X>Controller@doInvoice');
Route::get('/<route>_doLpb',       'LaporanAccounting<X>Controller@doLpb');
Route::get('/<route>_doBp',        'LaporanAccounting<X>Controller@doBp');
```

### 3.3 Blade — structure
Replace `@extends('report.masterreport5')` → `@extends('report.masterreport2')`, then
replace the old header/table markup and `jsreport` script with the styled version copied
from a reference page. Keep the page's **own** filter set, endpoint names, columns, and
group field. Key pieces:

- **`<style>`**: only the small borderless-button rules + `.tb-report .table-wrap
  { min-height:10vh }`. The table/toolbar CSS comes from `report-table.css`.
- **Toolbar** (`@section('header2')` inside `<div class="tb-report main"><div class="content">`):
  filter chips (`.filter-wrap`), a **search** input, **Customize Table** + **Tampilkan**
  buttons, and the **Export** dropdown.
  - **Do NOT add the "Dicetak oleh" subtitle.** Omit the
    `<div class="page-sub">Dicetak oleh: {{ $akses['user'] }} … <span id="printTime"></span></div>`
    line entirely, **and** drop the matching `document.getElementById('printTime').textContent = …`
    line in `$(document).ready`. The toolbar header keeps only the `.page-title`. (Pages migrated
    before this note still have it — leave them; the user edits those.)
- **Table shell**: `<table class="tb" id="mainTable">` with an empty `<thead>`/`<tbody id="tableBody">`
  and a `.table-footer` with `#footerLabel`.
- **Toast**: `<div class="toast" id="toast">…`.
- **Modals** for entity/valas pickers go **outside** `.tb-report` (so the CSS reset
  doesn't break Bootstrap modal spacing).

### 3.4 Blade — JS (the engine)
Copy the script block and adapt. Core functions:
- `setDefaultHeader()` — define `gcart_header` (one row per column:
  `[key, label, visible, type, isTotal, decimals]`). Mark money columns `isTotal=1`.
  Set `gsum_issubtotal = 1; gsum_isgrandtotal = 1;`. **Do not** include the group field
  (e.g. `nama`) as a column — it becomes the group header.
- `g_modeReport` **must be an integer** (see [Gotchas](#5-gotchas)); pick a distinct int
  for detail vs rekap.
- `setModeReport()` → sets `g_modeReport`, calls `doSetHeader(g_modeReport)` + `doShowCustomize()`.
- `makeTable()` — read filters, `document.getElementById('footerLabel').innerHTML =
  loadingHtml('Memuat data...')`, AJAX GET `reportUrl` with the same params the SP expects,
  set `lastRows`, call `render()`.
- `render()` — build `<thead>` from visible cols, group `lastRows` by the group field,
  emit `group-row` / `data-row` / `subtotal-row` / `grand-total`, honor client search.
- `pickCI(r,key)` (case-insensitive read), `num`, `str`, `applyFilters`, `rowSearchText`,
  `exportDelimited`, `toggleExport`, `showToast`.
- Perkiraan: prefer the **dropdown** pattern (`loadPerkiraanDropdown()` + `setPerkiraan()`
  + `#dropdownPerkiraan .perkiraan-item` click handler). Entity pickers (Supplier/Pelanggan)
  and Valas stay **modals** (large lists) — guard `DataTable().destroy()` with
  `if ($.fn.DataTable.isDataTable('#id')) { … }`.
- **Divisi** (jurnal reports): same **dropdown** pattern as Perkiraan, fed by the
  controller's `loadDivisi` (`Devisi`, `NamaDevisi` from `DBDEVISI`). Keep a hidden
  `#inputDivisi` (value the SP reads), a `#dropdownDivisi` list, and an
  `applyDivisi()`/`setDivisi()` pair (`setDivisi` re-runs `makeTable`). **Default to the
  first division** — do **not** add a "Semua Divisi" (`-`) item (per feedback; the SP is
  usually run per-division). This replaces the old `modalAccountingJurnal` +
  `buttonSelect('selectDivisi')` modal, so drop that `@include`.

### 3.5 Blade — voucher wiring (only if there's a No Nota / No Bukti column)
1. Config (report-table.js is already loaded by masterreport2 — just set endpoints):
   ```js
   window.ReportTableConfig = {
     kasUrl    : "{{ url('<route>_doKasharian') }}",
     invoiceUrl: "{{ url('<route>_doInvoice') }}",
     lpbUrl    : "{{ url('<route>_doLpb') }}",
     bpUrl     : "{{ url('<route>_doBp') }}"
     // optional: noJenisAlias: { PREFIX: 'JENIS' }  // extend LPB/PBJ→BPL etc.
   };
   ```
2. Helpers:
   ```js
   function isVoucherNo(v){ const s=str(v); if(!s||s.indexOf('/')===-1) return false;
     return s.toUpperCase().indexOf('SALDO AWAL')===-1; }
   function voucherCell(v){
     const s=str(v); if(!isVoucherNo(s)) return '<td>'+nullToEmpty(v)+'</td>';
     const jn=(typeof jenisFromNo==='function')?jenisFromNo(s):'';
     const ttl=(typeof jenisTitle==='function')?jenisTitle(jn):'Voucher';
     const esc=s.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
     const jsc=String(jn).replace(/\\/g,'\\\\').replace(/'/g,"\\'");
     return '<td class="kas-clickable" style="cursor:pointer;color:#0d6efd;text-decoration:underline" '+
            'title="Klik untuk lihat '+ttl+' '+s+'" '+
            'onclick="openVoucher(\''+esc+'\',\''+jsc+'\')">'+nullToEmpty(v)+'</td>';
   }
   ```
3. In `render()`'s cell loop, before the default `<td>`:
   ```js
   const kl = String(key).toLowerCase();
   if (kl === 'nofaktur' || kl === 'nobukti') return voucherCell(v);
   ```
   (Use only the keys that exist on the page — e.g. `nofaktur` for a No-Nota-only report.)

That's all — `report-table.js` builds/opens the `#kasPanel`, dispatches by Jenis, and
renders the right layout.

**Single clickable column → click the whole row.** When a page has exactly **one**
clickable voucher column (e.g. No Nota only, or a jurnal's No Bukti only), make the
**entire data row** open the voucher instead of just that cell — clicking anywhere on the
row is friendlier. Keep `voucherCell()` as a **hint only** (blue underline, no `onclick`)
and add a `voucherRowOpen(v, cls)` that returns the `<tr>` open tag carrying the
`onclick="openVoucher(...)"` when `isVoucherNo(v)`; in `render()` replace
`'<tr class="data-row">'` with `voucherRowOpen(pickCI(r, '<key>'), 'data-row')`. `.data-row`
already has `cursor:pointer` + a hover highlight in `report-table.css`, so no extra CSS is
needed. **Two-column pages keep per-cell `onclick`** (a row click can't tell which voucher
to open). Row-click pages: `piutangspjt`, `piutangoutstandingJT`, `hutangoutstandingnota`,
`hutanglhpjt`, `jurnalpenerimaankas`, `jurnalpengeluarankas`.

> The panel's "click-outside-to-close" handler in `report-table.js` ignores any
> `.kas-clickable` **or** any `[onclick*="openVoucher"]` element — so a whole clickable row
> opens/switches the voucher instead of instantly closing the panel. If a row-click seems to
> "do nothing", it's usually this handler closing the just-opened panel — make sure the row's
> opener is reachable via `closest('[onclick*="openVoucher"]')`.

---

## 4. Voucher type reference

`jenisFromNo(no)` parses the **2nd `/`-segment** of the number and applies aliases; the
result is passed to `openVoucher(no, jenis)`, which dispatches:

| Jenis (from number) | Opens | Stored proc | Typical column |
|---|---|---|---|
| `INVC` | Sales Invoice | `CetakInvoicePenjualan` | Piutang **No Nota** |
| `LPB` / `PBJ` → `BPL` | Faktur Pembelian | `CetakPenerimaanACC` | Hutang **No Nota / No LPB** |
| `BP` | Bukti Pemakaian Internal | `CetakPemakaianBahanACC` | — |
| `BBK`/`BBM`/`BKK`/`BKM`/`BMM`/`BJK`, else | Bukti Kas/Bank/Memorial/Jurnal | `CetakKasharian` | **No Bukti** (payment/receipt) |

Rule of thumb:
- **Hutang** reports: No Nota / No LPB → purchase receipt (`LPB`/`PBJ`→`BPL`);
  No Bukti (Bayar) → payment (`BBK`/`BKK`).
- **Piutang** reports: No Nota → sales Invoice (`INVC`); No Bukti → receipt (`BKM`/`BBM`).

If a page's number prefix maps to a different Jenis, add it to
`ReportTableConfig.noJenisAlias` (page-local) or `NO_JENIS_ALIAS` in `report-table.js`
(global). `jenisTitle` overrides go in `ReportTableConfig.jenisTitle`.

---

## 5. Gotchas

- **`report-table.js` / `report-table.css` are already loaded** by `masterreport2` → do
  **not** add your own `<script src>`/`<link>` for them; just set `window.ReportTableConfig`.
- **`g_modeReport` must be an integer.** `DBSIMPANHEADER.reportmode` is an `int`, so a
  string mode (e.g. `'IDR'`) makes the Customize Table layout **fail to persist**. Use
  distinct ints for detail vs rekap. (Reportmode is scoped per page/`href`, so reusing the
  same int on another page is fine.) See memory `report-mode-must-be-numeric`.
- **Use `innerHTML`, not `textContent`, for the loading spinner** (`loadingHtml()` returns
  markup). Result/error messages that are plain text can stay `textContent`.
- **Only real voucher numbers are clickable** — `isVoucherNo` skips blanks, values without
  `/`, and `SALDO AWAL`/opening rows.
- **Labels**: use **Pelanggan/Customer** for piutang, **Supplier** for hutang. Keep the
  input **IDs** (`inputSuppAwal`/`inputSuppAkhir`) and route names as the controller/SP
  expects — only relabel the visible text.
- **Group field is not a data column** — it's the group-row header; drop it from `gcart_header`.
- Guard `DataTable().destroy()` with `$.fn.DataTable.isDataTable('#id')` to avoid the
  first-open error the old code had.
- `CetakKasharian` returns 0 rows for an `INVC`; the dispatch (via `jenisFromNo`) must be
  correct or the panel shows "Tidak ada data".
- **Two-level header reports** (jurnal Debet/Kredit): write the grouped `<thead>` **static**
  in the markup (`rowspan`/`colspan`, `class="th-group"` on the spanning cells) and have
  `render()` fill only `<tbody>` — do **not** rebuild the header from `gcart_header`, and
  **omit the Customize Table** button (column-toggle can't express a spanning header).
  `setDefaultHeader()` still defines a flat `gcart_header` so the engine stays initialised.
  These group by `NoBukti` (kept as a data column, not a group-header row) with a Subtotal
  per voucher; jenis is `BKM`/`BKK` → `CetakKasharian`.

---

## 6. Remaining accounting reports (to migrate)

Still on the old `masterreport5` engine:

| Page / route | Controller | Has No Nota? | Has No Bukti? | Voucher wiring |
|---|---|---|---|---|
| `reportaccountinghutanglph` | `LaporanAccountingHutangLPHController` | verify | verify | if columns exist |
| `reportaccountinghutangumur` | `LaporanAccountingHutangUmurController` | **Yes** (`NoFaktur` = LPB/Nota) | verify | No Nota → `BPL` |
| `reportaccountingpiutanglpp` | `LaporanAccountingPiutangLPPController` | verify | verify | if columns exist |
| `reportaccountingpiutangspjt` | `LaporanAccountingPiutangSPJTController` | **Yes** (`Nofaktur` = No Nota) | verify | No Nota → `INVC` |
| `reportaccountingpiutangumur` | `LaporanAccountingPiutangUmurController` | **Yes** (`NoFaktur` = Nota) | verify | No Nota → `INVC` |

> "verify" = open the blade's `setDefaultHeader()` / the SP result to confirm the exact
> column keys before wiring. Wire only the columns that actually exist, keyed by their
> lowercased name (`nofaktur` / `nobukti`).

Already migrated + voucher-wired (use as references): neraca lajur, trial balance, buku
besar, hutang kartu, hutang pelunasan, hutang outstanding nota, hutang LHPJT, piutang
kartu, piutang pelunasan, piutang outstanding JT.

---

## 7. Verify each migrated page

1. Open the report — styled table renders; toolbar filters, search, Customize Table,
   Export all work; loading spinner shows while fetching.
2. Rows group per supplier/customer with correct Subtotal + Grand Total.
3. Perkiraan dropdown populates (auto-selects first account); changing it re-filters the
   entity picker.
4. **No Nota / No Bukti** cells render as links (except opening "Saldo Awal" rows) and
   clicking opens the correct voucher layout at the bottom (Invoice vs Faktur Pembelian vs
   Bukti Kas/Bank).
5. Numbers/totals match the pre-migration report for the same filters.
