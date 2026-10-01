# New Design — Gudang-Style Pages Guide

Sibling of **[new-design-all-report-guide.md](new-design-all-report-guide.md)**, which covers
`report/masterreport2` pages. **This file covers everything else** — bringing the same look
(draggable/hideable columns, the "Tampilkan" page-size control, the Filter modal, the chip/pill
buttons, the round action-icon column) to a page that does **not** extend a `report/*` layout.

Reference implementations, in order of how much they show:

| Page | Has |
|---|---|
| `resources/views/gudang/permintaanpemakaian.blade.php` + `app/Http/Controllers/Gudang/PermintaanPemakaianController.php` | Everything in this guide: draggable columns, hand-rolled Tampilkan+pager, Filter modal, search-icon picker (single entity), periode date-range toolbar |
| `resources/views/gudang/pembebananpemakaian.blade.php` + `...PembebananPemakaianController.php` | Same, plus a **multi-entity** search-icon picker (Perkiraan → Costing → Sub Costing, parent-dependent) and the pattern for keeping a single list's Actions column different per row state (Edit vs Print) |
| `resources/views/gudang/pemakaianbarang.blade.php` | An **older** sibling: two tabs (genuinely different datasets, not a state filter), DataTables-driven paging instead of the hand-rolled pager, no draggable columns, no search-icon picker (plain "+" opens a picker modal outright). Documented here so you don't copy patterns from it by accident. |

---

## 0. Is this actually the guide you want?

Three unrelated "modern table" systems exist in this codebase. Confusing them wastes real time.

| If the page is on... | Use |
|---|---|
| `report/masterreport2` (or `masterreport2x/3/4/5/Gudang/Neraca`) | [new-design-all-report-guide.md](new-design-all-report-guide.md) — full engine provided by the layout, nothing to port |
| `gudang/newmasterx` or `gudang/newmaster` already | **This guide** — the assets are already loaded, skip straight to §3 |
| Any other layout (`marketing/*`, `purchasing/*`, most of `accounting/*`) | **Check first**: does it already load `po-table-header.css` (or `so-table-header.css` / `sj-table-header.css`)? Those are a *parallel port* of this same visual language (toolbar, search, filter modal, pill buttons) — 39 live pages, no draggable columns. If the page's module already has that, matching your module's own convention is usually right. **This guide is for when you specifically want the draggable-column engine and the gudang button set** on a page that doesn't have either system yet. |

`grep -n "report-table.css\|po-table-header.css\|gcart_header" resources/views/<module>/<layout>.blade.php`
tells you which camp a layout is in.

---

## 1. What the three assets give you, and where they live

| File | Provides |
|---|---|
| `public/css/report-table.css` | `.tb-report` and everything scoped under it: `.toolbar`, `.filter-wrap`, `.search-inp`, `.btn-load`, `.table-outer`/`.table-wrap`/`.table-footer`, `.tb` table skin, `.pg` pager buttons, the draggable/gear column header (`.rt-th`, `.rt-bar`), and the `.rt-filter` modal skin. **Every one of these class rules is written as `.tb-report .xxx`** — none work unscoped. |
| `public/js/report-table.js` | `window.ReportTable` — `init()`, `headHtml()`, the drag/gear/bar interaction. See [new-slider-table-guide.md](new-slider-table-guide.md) for the full API; §5 below covers the one thing that guide assumes you have and you don't. |
| `public/css/tableMaster2.css` | Gudang-specific extras layered on top of `.tb-report`: `.action-buttons` + `.btn-action-sm` (+ 5 color variants), `.sp-badge`, `.tab-toggle` (pill tab strip), `.btn-pill-primary`/`.btn-pill-secondary` (pill-shaped Close/Submit buttons), `.aksi-hover` (row-hover-reveal for action icons) |
| `public/css/newmaster.css` | `.btn-chip-biru` — the light-blue "chip" button used for toolbar-level actions like "+ Tambah" |

All four are loaded once, globally, by `gudang/newmasterx.blade.php` (and `gudang/newmaster.blade.php`).
A page on a different layout does not get them for free.

---

## 2. Getting the assets onto a page whose layout doesn't have them

### 2a. Bootstrap check first — don't skip this

Everything here assumes **Bootstrap 4** (`data-dismiss`, `data-toggle`, `$.fn.modal` owned by
BS4). Before adding anything:

```
grep -nE "bootstrap|popper|data-bs-" resources/views/<module>/<layout>.blade.php
```

- **Layout is BS4 already** (`canvas/bootstrap.css` + `bootstrap.min.js`/`bootstrap.bundle-4.6.2.min.js`) →
  proceed to §2b.
- **Layout is BS5-only or mixes BS5 CDN tags with BS4** → do **not** invent a new migration and do
  **not** edit the shared layout's Bootstrap tags as a side effect of a styling task. Follow
  [bootstrap4-version-alignment-guide.md](bootstrap4-version-alignment-guide.md)'s rules: never
  overwrite a shared JS file in place, migrate one target at a time. Often the cheaper move is
  **retargeting the page to a sibling layout that's already BS4** (check `@extends` on other pages
  in the same module first — a module often already has one) rather than stripping BS5 out of a
  layout shared by dozens of unrelated pages. Either way: **ask first**, state the blast radius
  (how many pages the candidate layout serves).

### 2b. Add the three files from the page itself — no shared-layout edit needed

If the target layout has a `@yield('css')` inside `<head>` and a `@yield('js')` near the end of
`<body>` (true for every layout checked so far — `gudang/newmasterx`, `newmasterTest`,
`accounting/newmaster`, root `newmaster`), a single page can pull in the assets through its own
sections without touching the shared layout file at all:

```blade
@section('css')
<link rel="stylesheet"
    href="{!! URL::asset('css/report-table.css') !!}?v={{ @filemtime(base_path('public/css/report-table.css')) ?: '1' }}">
<link rel="stylesheet"
    href="{!! URL::asset('css/tableMaster2.css') !!}?v={{ @filemtime(base_path('public/css/tableMaster2.css')) ?: '1' }}">
{{-- newmaster.css only if the layout doesn't already load it — check first, most do --}}
@endsection
```

```blade
@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">
  // page JS goes here, after ReportTable is defined
</script>
@endsection
```

`@yield('js')` must fire **after** jQuery and Bootstrap load — confirm this with the same grep from
§2a (check the line numbers).

This is still worth a heads-up to whoever owns the page/module even though it's additive-only:
`report-table.css` carries the `.tb-report * { margin:0; padding:0 }` reset (see §3) and a few
unscoped `:root` variables that can collide by name with Bootstrap's own (documented in
[bootstrap4-version-alignment-guide.md](bootstrap4-version-alignment-guide.md)'s "Known latent
traps"). Low risk, not zero.

---

## 3. Page structure: `.tb-report` wraps the list, nothing else

**The single most important rule in this guide.** `report-table.css` resets
`.tb-report * { margin:0; padding:0 }` on every descendant. That's fine for a toolbar + table — it's
disastrous on a Bootstrap grid form (`.row` / `.col-md-*` depend on padding for their gutters).

Every reference page solves this the same way: **one `.tb-report` block for the list view, and
everything else — Add form, Detail view, Koreksi form, modals — stays outside it as plain Bootstrap
grid markup**, shown/hidden with a simple `showPage()`/`.mainpage` toggle (not Bootstrap tabs, not
nested inside the report styling at all).

```blade
<div id="page1" class="container-fluid mainpage">   {{-- the list --}}
  <div class="tb-report">
    <div class="content">
      <div class="toolbar"> ... </div>
      <div id="rtBar"></div>
      <div class="table-outer"> ... </div>
    </div>
  </div>
</div>

<div id="page2" style="display:none" class="mainpage container-fluid">   {{-- Add form --}}
  <div class="row"> <div class="col-md-4">...</div> </div>  {{-- plain Bootstrap grid, untouched --}}
</div>
```

```js
function showPage(id) { $('.mainpage').hide(); $('#' + id).show(); }
```

Modals go **outside** `.tb-report` too, for the same reset reason (already covered in
[new-filter-modal-ui-guide.md](new-filter-modal-ui-guide.md) §1).

If the page you're porting has its list AND its form on the same scroll (no page-switching), you
still need this boundary — wrap only the table/toolbar block in `.tb-report`, not the form section,
even if that means `.tb-report` doesn't wrap the whole `<div id="content">`.

---

## 4. Toolbar

```blade
<div class="toolbar">
  <div class="filter-wrap">
    <label>Periode</label>
    <input type="date" class="filter-inp" id="inputDate1" value="{!! $date1 !!}" onchange="reloadData()">
    <span class="filter-sep">s/d</span>
    <input type="date" class="filter-inp" id="inputDate2" value="{!! $date2 !!}" onchange="reloadData()">
  </div>

  <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
      oninput="renderTabel()" style="width:200px">

  <div class="len-wrap">   {{-- see §7 — not part of report-table.css, copy the CSS too --}}
    <label for="tabelLen2">Tampilkan</label>
    <select id="tabelLen2" class="len-inp" onchange="onLenChange2()">
      <option value="10">10</option><option value="25">25</option>
      <option value="50">50</option><option value="100">100</option>
      <option value="-1">Semua</option>
    </select>
  </div>

  <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
    <i class="bi bi-funnel"></i> Filter
  </button>

  <div class="action-group">
    <button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
  </div>
</div>
```

The periode range is a convention, not a requirement — only include it if the list is naturally
date-scoped. `filter-wrap`/`filter-inp`/`filter-sep` are already scoped in `report-table.css`, no
extra CSS needed.

---

## 5. The table + the "slider" (draggable / hideable columns)

This is the [new-slider-table-guide.md](new-slider-table-guide.md) `gcart_header` contract —
**read that file for the full API** (the column-array shape, drag, gear menu, decimals, totals,
"Reset kolom" persistence behavior, all of it applies unchanged). What that guide doesn't cover,
because it assumes `report/masterreport2`, is where the *PHP-side* half of the engine comes from
when your layout isn't `masterreport2`.

**The fix: copy the wrapper functions page-locally.** They're thin — each one just calls a
**global** route that isn't gated to any layout:

```js
var g_href = 'yourpagename';   // unique key — this is what DBSIMPANHEADER is keyed on
var g_modeReport = '1';
var gcart_header = [];
var gsum_issubtotal = 0, gsum_isgrandtotal = 0, gct_desimal_max = 4;

function setDefaultHeader() {
  gcart_header = [
    // [ field, label, visible, type, total, decimals ]
    ['NOBUKTI', 'No Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL', 'Tanggal',  1, 'date',    0, 0],
  ];
}

function doSetHeader(_mode, _isReset = false) {
  let _str = (!_isReset) ? doLoadHeader(g_href, _mode) : "";
  if (_str != "") { gcart_header = doGetHeader(_str); }
  else { setDefaultHeader(); doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal); }
}

function doLoadHeader(_href, _mode) {
  let _header = "";
  $.ajax({ url: "{!! url('globalfunctions_doLoadHeader') !!}", type: "get", async: false,
    data: { href: _href, mode: _mode },
    success: r => { _header = (r.length > 0) ? r[0].header : ""; if (r.length) { gsum_issubtotal = Number(r[0].issubtotal); gsum_isgrandtotal = Number(r[0].isgrandtotal); } } });
  return _header;
}

function doGetHeader(_str) {
  return _str.split("||").map(item => {
    const p = item.split(";;");
    return [p[0], p[1], Number(p[2]), p[3], Number(p[4]), Number(p[5])];
  });
}

function doSimpanHeader(_href, _mode, _cart, _sub, _grand) {
  const _str = _cart.map(i => i.join(';;')).join('||');
  $.ajax({ url: "{!! url('globalfunctions_doSimpanHeader') !!}", type: "get", async: false,
    data: { href: _href, mode: _mode, header: _str, issubtotal: _sub, isgrandtotal: _grand } });
}

function doMoveHeader(from, to) { /* splice from -> to, then doSimpanHeader(...) */ }
function doButtonVisibility(i)  { gcart_header[i][2] ^= 1; doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal); }
function doSetDesimal(i, step)  { /* clamp 0-4, then doSimpanHeader(...) */ }
function doButtonTotal(i)       { gcart_header[i][4] ^= 1; doSimpanHeader(g_href, g_modeReport, gcart_header, gsum_issubtotal, gsum_isgrandtotal); }
```

Copy these **verbatim** from `pembebananpemakaian.blade.php`'s `@section('js')` rather than
retyping — the snippet above is trimmed for readability. Then wire up exactly as the sub-guide
describes:

```js
$(document).ready(function() {
  doSetHeader(g_modeReport);
  ReportTable.init({ table: '#mainTable', bar: '#rtBar', onChange: renderTabel });
  renderTabel();
});
```

Everything else — `ReportTable.headHtml(cols)`, the `cols = gcart_header.filter(...)` trap, "Reset
kolom" not reaching existing users when you edit `setDefaultHeader()` later — is identical to the
sub-guide. Don't re-derive it here.

---

## 6. Row actions — the Aksi column

```blade
<td class="text-center">
  <div class="action-buttons">
    <button type="button" class="btn-action-sm btn-action-warning" data-toggle="tooltip" title="Detail"
        onclick="buttonDetail('...')"><i class="bi bi-info"></i></button>
    <button type="button" class="btn-action-sm btn-action-primary" data-toggle="tooltip" title="Otorisasi"
        onclick="buttonOtorisasi('...')"><i class="bi bi-key"></i></button>
  </div>
</td>
```

| Class | Color | Convention (not enforced — just what every reference page uses) |
|---|---|---|
| `btn-action-warning` | amber | Detail (`bi-info`) |
| `btn-action-primary` | blue | Otorisasi (`bi-key`) |
| `btn-action-success` | green | Edit (`bi-pencil-fill`) |
| `btn-action-danger` | red | Batal Otorisasi (`bi-key-fill`) / destructive actions |
| `btn-action-info` | cyan | Print (`bi-printer`) |

Round, 30×30px, `border-radius:7px`. `.action-buttons` is the flex row wrapper. Add `class="tb
aksi-hover"` to the `<table>` to make the icons only appear on row hover (opt-in — most reference
pages use it, `pemakaianbarang`'s older tables don't).

**Tooltip gotcha**, hit in every reference page's render function: dispose old tooltip instances
*before* replacing row HTML, and re-init with `container:'body', boundary:'window'` *after* —
otherwise stale tooltips from destroyed buttons stack up and can visually block the new ones:

```js
$(tbody).find('[data-toggle="tooltip"]').tooltip('dispose');
tbody.innerHTML = html;
$('[data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' });
```

**One list, action buttons that change per row state** (rather than two tabs — see §11): decide the
buttons inside the row-render function based on the row's own field, same pattern for both the
Blade-side first paint and the JS re-render:

```js
function aksiButtonsHtml(r) {
  const detailBtn = '...bi-info...';
  if (Number(r.IsOtorisasi1) === 1) {
    return detailBtn + '...Batal Otorisasi...' + '...Print...';
  }
  return detailBtn + '...Otorisasi...' + '...Edit...';
}
```

---

## 7. Buttons outside the table: chip vs. pill

Two different button "families," don't mix them up:

| | `btn-chip-biru` | `btn-pill-primary` / `btn-pill-secondary` |
|---|---|---|
| Shape | Whatever the base `.btn` gives (rectangular) | Pill: `height:30px`, `border-radius:20px`, uppercase, drop shadow |
| Color | Fixed light-blue tint, own class | **None** — pair with a Bootstrap color class (`btn-primary`, `btn-danger`, `btn-secondary`) |
| Used for | A toolbar-level action ("+ Tambah", "+ Tambah Item") | Close / Submit / Batal buttons on a full-page form or inside a modal |
| Defined in | `newmaster.css` | `tableMaster2.css`, **scoped** to `#contentContainer .btn.btn-pill-*` and `.modal .btn.btn-pill-*` only |

⚠️ The pill classes silently do nothing outside those two containers — if a pill button looks like
a plain rectangle, check it's actually inside `#contentContainer` or `.modal`.

```blade
<button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>

<button type="button" class="btn btn-danger btn-action-danger btn-pill-primary" onclick="buttonCloseForm()">CLOSE</button>
<button type="button" class="btn btn-primary btn-action-primary btn-pill-primary" onclick="submitAdd()">Submit</button>
<button type="button" class="btn btn-secondary btn-pill-secondary" onclick="buttonBatal()">Batal</button>
```

(The `btn-action-{color}` class contributes only its tint/border here, not its 30×30 sizing — that
sizing rule lives on `.btn-action-sm`, which isn't present on these buttons. Redundant-looking, but
that's the pattern every reference page uses — don't "clean it up" mid-task.)

---

## 8. "Tampilkan" (page-size) dropdown + pager

**Not part of `report-table.css`.** Every reference page defines `.len-wrap`/`.len-inp` locally in
its own `@section('css')`, on purpose, so this doesn't leak into other pages sharing the stylesheet.
Copy this block whenever you add the dropdown:

```css
.len-wrap { display:flex; align-items:center; gap:8px; background:var(--white); border:1.5px solid var(--border); border-radius:8px; padding:5px 12px; }
.len-wrap label { margin:0; font-size:11.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
.len-inp { border:none; background:transparent; font-size:13px; font-weight:700; color:#1D2130; outline:none; cursor:pointer; padding:2px 20px 2px 0; appearance:none; background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>"); background-repeat:no-repeat; background-position:right center; }
.tb-report .pg.disabled { opacity:.4; cursor:not-allowed; pointer-events:none; }
```

**Two implementations exist. Pick the hand-rolled one for new pages:**

| | DataTables-driven (`pemakaianbarang.blade.php`) | Hand-rolled client array (`permintaanpemakaian`/`pembebananpemakaian`) |
|---|---|---|
| Paging engine | Real DataTables `page.len()` | Plain JS `.slice()` on the in-memory row array |
| Works with §5's draggable columns? | Not combined in that page (no `gcart_header`) | Yes — this is what all newer pages actually use |
| Pager markup | Custom-rendered from `.page.info()`, DataTables' own pager hidden via `dom:'rt'` | Custom-rendered from `Math.ceil(rows.length / pageLen)` |

Hand-rolled version — `renderTabel()` filters/searches/slices `lastRows`, then calls `renderPager2()`:

```js
let tabelLen2 = 10, tabelPage2 = 1;

function onLenChange2() {
  const v = Number(document.getElementById('tabelLen2').value);
  tabelLen2 = (v === -1 || v > 0) ? v : 10;
  renderTabel();
}
function gotoPage2(p) { tabelPage2 = p; renderTabel(false); }

function renderPager2(page, totalPages) {
  const el = document.getElementById('pagerBtns2');
  if (!totalPages || totalPages <= 1) { el.innerHTML = ''; return; }
  // ...5-button window + « / » — copy verbatim from pembebananpemakaian.blade.php
}
```

Copy `renderTabel()`/`renderPager2()` verbatim from `pembebananpemakaian.blade.php` — the logic
(filter → search → slice → render → footer label → pager) is identical for any page's list.

---

## 9. The Filter modal

Nothing gudang-specific here — [new-filter-modal-ui-guide.md](new-filter-modal-ui-guide.md) applies
exactly as written; the skin activates purely off the `rt-filter` class, no `masterreport2`
dependency. The gudang reference pages use the simplest version (one `rt-native` select, no
`.rt-combo` entity pickers), but the full modal — sections, combos, badge — works the same way.

---

## 10. Entity pickers — the search-icon pattern

**This is not** [new-cust-supp-modal-guide.md](new-cust-supp-modal-guide.md)'s `#formSelect` /
DataTables picker (that one is a separate, report-module-specific system: shared modal blade, its
own click-row skin, its own gated `window.g_pickerV2` flag). Gudang pages use a lighter,
self-contained pattern: one text input + a magnifying-glass button, no shared modal blade.

```blade
<div class="input-group">
  <input id="KoreksiEditPerkiraan" type="text" class="form-control text-left" placeholder="Perkiraan"
      onkeypress="onKeyPressPicker(event,'perkiraan')">
  <button type="button" onclick="openPicker('perkiraan')" class="btn btn-chip-biru"><i class="bi bi-search"></i></button>
</div>
```

Behavior: type a code and press Enter (or click the magnifying glass with the field empty) →

- **Exact match** (case-insensitive, trimmed) → fills the field directly, **no modal opens**.
- **Partial match, multiple results, or empty** → opens a `.rt-picker-v2`-skinned modal with the
  typed text already in its search box; clicking any row fills the field and closes the modal.

For a **single** entity, copy `permintaanpemakaian.blade.php`'s `resolveBarang()` /
`initBarangTable()` pair. For **multiple, parent-dependent** entities (Perkiraan → Costing → Sub
Costing, where Costing's options depend on which Perkiraan was picked), copy
`pembebananpemakaian.blade.php`'s generic `PICKERS` config object + `resolvePicker()`/`openPicker()`/
`applyPick()` — one engine drives every field, keyed by a config entry per field (`url`, `codeField`,
`nameField`, `columns`, `parent`, `clears`). Don't write a third, bespoke variant — extend the
config object instead.

---

## 11. One list with a state filter, vs. two genuinely different datasets

Decide with this rule, worded from `pemakaianbarang.blade.php`'s own comment on why it *doesn't*
merge its two tabs:

> Two tabs are correct when the two datasets are **genuinely different queries** (e.g. "outstanding
> requests not yet converted" vs. "documents already created from them") — merging them would mean
> switching between two different row shapes mid-table.
>
> One list + a Filter-modal select is correct when it's **the same query, split only by a status
> flag** (e.g. `IsOtorisasi1 = 0/1` on the same table) — that's a filter, not a different dataset.

`pembebananpemakaian` used to be the second case with two tabs; it's now one list (see its git
history) — use that as the template for merging a belum/sudah-style split.

---

## 12. Pre-flight — run before starting

```bash
# What does the target layout already have?
grep -nE "report-table\.css|report-table\.js|tableMaster2\.css|po-table-header\.css|gcart_header" \
    resources/views/<module>/<layout>.blade.php

# Bootstrap version
grep -nE "bootstrap|popper|data-bs-" resources/views/<module>/<layout>.blade.php

# Does the page already have its own picker pattern (don't silently replace it)?
grep -nE "formSelect|PICKERS|resolveBarang|onKeyPressPicker" resources/views/<path>/<page>.blade.php

# How many other pages share this exact layout (blast radius if you do end up editing it)?
grep -rl "@extends('<layout-view-name>')" resources/views --include=*.blade.php \
  | grep -v -E '\(070526\)|\(180526\)|\(Old\)|backup' | wc -l
```

## 13. Ask first — same spirit as the report guide's §4, adapted

| Trigger | Why |
|---|---|
| Target layout is BS5-only or mixed | Don't silently migrate a shared layout's Bootstrap version as a side effect — see §2a |
| Target layout is shared by many pages and lacks the 3 assets | Even additive CSS/JS is a shared-file edit — state the page count (§12's last command) |
| Page has a multi-section Bootstrap-grid form sharing the same `<div>` as the list | Confirm the `.tb-report` boundary (§3) before touching markup — get it wrong and the form's grid gutters silently vanish |
| Page already has its own picker/modal/pager pattern | Don't replace it with this guide's pattern without asking — same reasoning as the report guide's §4a |
| Removing an existing tab split in favor of one filtered list | Confirm the two tabs really are the same dataset (§11) — merging genuinely different queries loses data shape, not just a UI simplification |

---

## 14. Verification

**Static:**

```bash
grep -n "btn-select\|<th>Actions</th>\|data-bs-dismiss" <file>   # leftover old/BS5 markup

node -e "const fs=require('fs');const s=fs.readFileSync('<file>','utf8');
const m=s.match(/<script[^>]*>([\s\S]*?)<\/script>/);
let js=m[1].replace(/\{!![\s\S]*?!!\}/g,'STUB').replace(/\{\{[\s\S]*?\}\}/g,'STUB').replace(/@json\([^)]*\)/g,'[]');
try{new (require('vm').Script)(js);console.log('JS OK')}catch(e){console.log('ERR '+e.message)}"
```

**In the browser:**

- Drag a column heading, hide one via the gear, restore from the bar, **reload, confirm it stuck**.
- "Tampilkan" 10/25/Semua → row count and pager both update; pager hides entirely on "Semua" or a
  single page.
- Filter modal: badge count, Terapkan closes it and re-filters, Reset semua clears it.
- Picker: exact code + Enter fills with no modal; partial text opens the modal with it prefilled;
  click a row fills and closes.
- Row actions change correctly per row state (e.g. unauthorized → Edit, authorized → Print).
- Tooltips don't ghost after a re-render (hover a button that existed before the last refresh).
- Every modal on the page opens **and closes** — check which Bootstrap owns `$.fn.modal` on this
  layout and that open/close use matching APIs (see the report guide's §5.1 if the layout loads
  more than one Bootstrap).
- If a shared layout file was touched: load one page on that layout you were **not** targeting and
  confirm it's unchanged.

---

## 15. Quick reference

| Class / function | Lives in |
|---|---|
| `.tb-report`, `.toolbar`, `.filter-wrap`, `.search-inp`, `.btn-load`, `.table-outer/.table-wrap/.table-footer`, `.tb`, `.pg`, `.rt-th`/`.rt-bar` (drag+gear), `.rt-filter` modal skin | `public/css/report-table.css` |
| `window.ReportTable` (`init`, `headHtml`, drag/gear engine) | `public/js/report-table.js` |
| `.action-buttons`, `.btn-action-sm` + 5 color variants, `.sp-badge`, `.tab-toggle`, `.btn-pill-primary`/`.btn-pill-secondary`, `.aksi-hover` | `public/css/tableMaster2.css` |
| `.btn-chip-biru` | `public/css/newmaster.css` |
| `.len-wrap`/`.len-inp` (Tampilkan dropdown) | **Not shared** — copy into each page's own `@section('css')` |
| `doSetHeader`/`doLoadHeader`/`doGetHeader`/`doSimpanHeader`/`doMoveHeader`/`doButtonVisibility`/`doSetDesimal`/`doButtonTotal` | Provided free by `report/masterreport2.blade.php`; **copy page-locally** everywhere else (§5) |
| `globalfunctions_doLoadHeader` / `globalfunctions_doSimpanHeader` routes | `routes/report.php` → `GlobalFunctionsController` — **global**, not gated to any layout, safe to call from a page-local copy of the engine |
