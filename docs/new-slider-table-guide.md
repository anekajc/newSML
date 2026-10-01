# New Design � Interactive Table Guide (draggable columns)

Sub-guide of **[new-design-all-report-guide.md](new-design-all-report-guide.md)** � read that first. It holds the
pre-flight checks, the ask-first protocol, and the cross-cutting constraints. This file only covers
the interactive table itself.

**What this adds to a page:**

- drag a column heading to reorder columns
- per-column gear menu: hide column, decimal places, show/hide total
- a bar above the table listing hidden columns (click to restore)
- an optional **"Tampilan"** switcher (Detail ? Rekap, Rp ? Valas, �)
- all of it persisted per user, per page, per report-mode

Reference implementations: `resources/views/report/reportaccountingkasharian.blade.php`,
`resources/views/report/reportmarketinghistoryoutso.blade.php`.

---

## 1. Prerequisites

| Requirement | Where it comes from | If missing |
|---|---|---|
| `public/css/report-table.css` | `report/newmaster2.blade.php` | add a `<link>` � **big change, ask first** |
| `public/js/report-table.js` (`window.ReportTable`) | `report/masterreport2.blade.php` | add a `<script>` � **big change, ask first** |
| `gcart_header` + `doSetHeader` / `doSimpanHeader` / `doButtonVisibility` / `doSetDesimal` / `doButtonTotal` / `doMoveHeader` | `report/masterreport2.blade.php` | **not portable** � this engine only exists in masterreport2. Stop and ask. |
| `format_date`, `format_number`, `currencyNormalizer`, `nullToEmpty` | `report/newmaster2.blade.php` | provide equivalents � ask first |
| Bootstrap Icons (`bi-gear`) | `report/newmaster2.blade.php` | gear icon renders blank |

A page that already does `@extends('report.masterreport2')` has **all** of this. Any other layout
does not � see the all-guide's pre-flight section before going further.

### Does the page qualify?

1. `@extends('report.masterreport2')`
2. Markup has `<div class="tb-report main">` � `<table class="tb" id="mainTable">`
3. The page defines `setDefaultHeader()` and its render function starts with
   `gcart_header.filter(c => c[2] === 1)`

If (3) is false � the page prints a hardcoded `<thead>` and hardcoded `<td>`s � the table must be
converted to `gcart_header` first. That is a **behaviour change**: stop and ask.

---

## 2. The `gcart_header` contract

Everything is driven by one global array. Each column is a 6-element array:

```js
// [ 0: field,  1: label,  2: visible,  3: type,  4: total,  5: decimals ]
['NDPPRPZX', 'DPP IDR',  1, 'float', 1, 2]
```

| Idx | Meaning | Values |
|---|---|---|
| 0 | Field name **exactly as the stored proc returns it** | `NoBukti` |
| 1 | Column heading shown to the user | `No. Bukti` |
| 2 | Visible | `1` shown, `0` hidden (appears in the bar) |
| 3 | Type | `varchar` \| `date` \| `float` \| `int` \| `bool` |
| 4 | Include in Subtotal / Grand Total | `1` / `0` (numeric columns only) |
| 5 | Decimal places | `0`�`4` |

Two companions control the total rows:

```js
gsum_issubtotal   = 1;   // Subtotal row at each group change
gsum_isgrandtotal = 1;   // one GRAND TOTAL row at the end
```

`doSetHeader(g_modeReport)` either loads the user's saved layout for this page+mode, or (first
run) calls your `setDefaultHeader()` and saves it. **`setDefaultHeader()` must exist**, and
`g_modeReport` must be assigned *before* `doSetHeader()` runs.

### ?? Editing `setDefaultHeader()` does NOT reach existing users

This is the single most surprising thing about this engine, and it will look like your change
"didn't work".

`doSetHeader()` prefers the **saved** layout from `DBSIMPANHEADER`, and that row is written
automatically the first time a user opens the page. So for anyone who has ever opened it:

| You do | They get |
|---|---|
| Add a new column to `setDefaultHeader()` | **nothing** � the new column never appears |
| Remove a column | it stays visible |
| Rename a label / change decimals / change the total flag | old value persists |

The saved layout only reloads from `setDefaultHeader()` when it is reset �
`doSetHeader(mode, true)` forces `_strHeader = ""`, which re-runs `setDefaultHeader()` and saves
the result. That is what the **"Reset kolom"** button in the bar does.

**So whenever you change `setDefaultHeader()`:**

1. Say so explicitly in your report to the user � the change is invisible until reset.
2. Make sure the page's bar shows **Reset kolom** (it appears automatically whenever
   `doSetHeader` exists � see �3 Step 1).
3. Tell them to click it once per report mode, since each `g_modeReport` stores its own layout.
4. Anyone else already using that page must click it too. It cannot be forced from code without
   wiping layouts everyone deliberately customised.

> Do not "fix" this by making `doSetHeader()` always reload the defaults � that would throw away
> every user's saved column layout on every page load.

---

## 3. Steps

### Step 1 � bar container

Between the toolbar and `.table-outer`:

```blade
<!-- Bar kolom tersembunyi + Tampilan (diisi oleh report-table.js / ReportTable) -->
<div id="rtBar"></div>
```

### Step 2 � hint line (optional)

After `.table-outer` closes:

```blade
<div class="rt-hint">
    <i class="bi bi-info-circle"></i>
    Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
    sembunyikan kolom atau atur desimal &amp; total.
</div>
```

### Step 3 � init

In `$(document).ready`, **after** the page's own setup calls (`setDefaultHeader()`,
`setReportMode()`, �) so `gcart_header` is populated:

```js
ReportTable.init({
    table: '#mainTable',   // the <table class="tb">
    bar: '#rtBar',         // the div from Step 1
    onChange: render       // the page's OWN render function � name varies
});
```

If the page's render function takes arguments, wrap it:

```js
onChange: function () {
    if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
}
```

### Step 4 � render the header through `headHtml()`

This is the step that actually makes the header interactive.

```js
// BEFORE � hand-built
thead.innerHTML = '<tr>' + cols.map(function(c) {
    const isNum = (c[3] === 'float' || c[3] === 'int');
    return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
}).join('') + '</tr>';

// AFTER
thead.innerHTML = ReportTable.headHtml(cols);
```

`headHtml()` adds the `num` class for numeric columns itself and refreshes `#rtBar` as a side
effect � nothing else to call.

> ?? **The trap that breaks everything silently.** `cols` must come from
> `gcart_header.filter(...)`, never `.map()` or any copy. `headHtml()` uses `indexOf()` to map
> each column back to its index in `gcart_header`; with copied objects every lookup returns `-1`,
> and the gear menu and drag-reorder stop working **with no console error**.
>
> ```js
> const cols = gcart_header.filter(c => c[2] === 1);          // ? same object references
> const cols = gcart_header.filter(...).map(c => [...c]);     // ? breaks silently
> ```

**Stop here if the page has no Detail/Rekap-style mode.**

### Step 5 (optional) � "Tampilan" switcher

Only when the page *already* has a mode that swaps the column layout. This mirrors existing
state; it does not create new state.

```js
views: {
    label: 'Tampilan',
    options: [
        { value: '0', label: 'Detail', desc: 'Rincian per baris' },
        { value: '1', label: 'Rekap',  desc: 'Ringkasan per grup' }
    ],
    get: function() { return globalReportMode; },   // current value, AS A STRING
    set: function(v) {
        setReportMode(String(v));                   // the page's own mode setter
        if (lastRows.length) { render(); }
    }
}
```

Four rules:

1. **`options` is mandatory.** Without it the switcher silently doesn't render � a `views` block
   with only `get`/`set` draws nothing.
2. **`value` must be a string**, matching what `get()` returns (compared via `String()`).
3. **Decide in `set()` whether to re-query.** Columns only rearranged ? `render()`. Each mode
   needs different fields from the SP ? `makeTable('REPORT')`.
4. **Keep sibling controls in sync**, e.g. `$('#modalReport').val(String(v));`

---

## 4. API reference

| Call | Use |
|---|---|
| `ReportTable.init(opts)` | once, in `$(document).ready` |
| `ReportTable.headHtml(cols)` | inside render, to build `<thead>` |
| `ReportTable.refresh()` | redraw just the bar (rarely needed) |
| `ReportTable.reset()` | reset columns to `setDefaultHeader()` � same as the bar's "Reset kolom" |
| `ReportTable.close()` | force-close gear menu + bar dropdowns |

### The "Reset kolom" button

Rendered automatically at the **left end of the bar** whenever `window.doSetHeader` exists, so a
Class A page gets it for free � no page-level markup needed. Clicking it:

1. `alertify.confirm` (falls back to resetting straight away if alertify isn't loaded)
2. `doSetHeader(g_modeReport, true)` ? re-runs `setDefaultHeader()` and saves
3. `onChange()` ? the page re-renders, so the change is visible immediately
4. `renderBar()` + an alertify success toast

This differs from masterreport2's `doResetHeader()` (the "Reset ke default" link in the Atur Kolom
modal), which resets and saves but does **not** re-render the main table � with that one the user
has to click *Tampilkan* again.

> **Keep this button when the "Atur Kolom" modal is eventually retired.** It is the only remaining
> way for a user to pick up columns added to `setDefaultHeader()` after their first visit.

### How changes persist

The gear menu and bar delegate to masterreport2's functions, so the interactive header, the
"Atur Kolom" modal and the saved layout never diverge:

| User action | Calls | Result |
|---|---|---|
| Hide column / restore from bar | `doButtonVisibility(i)` | flips `[2]`, saves |
| Drag a heading | `doMoveHeader(from, to)` | reorders array, saves |
| Decimal � | `doSetDesimal(i, �1)` | adjusts `[5]` (0�4), saves |
| "Tampilkan total" | `doButtonTotal(i)` | flips `[4]`, saves |

Each calls `doSimpanHeader(...)` ? `globalfunctions_doSimpanHeader` ? `DBSIMPANHEADER`, keyed by
page href **and** `g_modeReport`. Each report mode remembers its own layout.

---

## 5. Gotchas

| Symptom | Cause / fix |
|---|---|
| Nothing appears above the table | `#rtBar` missing, or `bar:` selector doesn't match its id |
| Header looks normal, no grip/gear | Step 4 not done � still building `<thead>` by hand |
| Gear opens but does nothing; drag does nothing | `cols` isn't from `gcart_header.filter(...)` � see Step 4 |
| "Tampilan" never shows | `views.options` missing or empty |
| Tampilan shows "-" | `get()` returns a number but `options[].value` are strings |
| Gear menu clipped inside the table | Don't restyle `.rt-colmenu` � it is `position:fixed` on `<body>` on purpose, because `.table-wrap` is `overflow:auto` |
| Bar dropdown renders under the table | z-index layering: toolbar 50, `.rt-bar` 45, sticky `thead` 20 |
| Changes don't survive reload | `g_modeReport` not set before `doSetHeader()`, or `setDefaultHeader()` missing |
| **A column you just added to `setDefaultHeader()` doesn't appear** | The user's saved layout in `DBSIMPANHEADER` wins � click **Reset kolom**. See �2 |
| "Reset kolom" button missing from the bar | `window.doSetHeader` doesn't exist � the page isn't on `masterreport2` |
| Numbers show `NaN` | field name doesn't match the proc's column, or `currencyNormalizer()` was skipped |
| Blank cells for fields that exist | Proc casing differs (`Debet` vs `debet`) � read via `pickCI(r, key)` |
| Subtotals never appear | `gsum_issubtotal !== 1`, no column has `[4] === 1`, or `currentGroupby` isn't a real field |
| "Filter Data" modal shows no columns | `getKolomFilter()` returns names not present in `gcart_header[i][0]` |
| `ReportTable is not defined` | page doesn't extend `report.masterreport2` |

---

## 6. Checklist

- [ ] `<div id="rtBar"></div>` between toolbar and `.table-outer`
- [ ] `.rt-hint` line (optional)
- [ ] `ReportTable.init({ table, bar, onChange })` after the setup calls
- [ ] `thead.innerHTML = ReportTable.headHtml(cols)`
- [ ] `cols` from `gcart_header.filter(...)` � **not** `.map()`
- [ ] optional `views` with non-empty `options`, string values
- [ ] Tested: drag, hide, restore, decimals, total � **and that they survive a reload**
- [ ] "Atur Kolom" modal shows the same state as the gear menu
