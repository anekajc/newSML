> ## ⚠️ SUPERSEDED
> Use **[new-design-all-report-guide.md](new-design-all-report-guide.md)** instead, which routes to
> [new-slider-table-guide.md](new-slider-table-guide.md) for this material.
>
> This file is kept as an archive. Don't edit it; edit the new guides.

# Upgrading an existing report page's main table — step by step

A short, do-this-then-that checklist for taking a report page that already renders a
`.tb-report` table and giving it the **switchable / interactive header**:

- drag a column heading to reorder columns
- gear menu per column → hide, decimals, show total
- a bar above the table listing hidden columns (click to restore)
- an optional **"Tampilan"** switcher (Detail ⇄ Rekap, Rp ⇄ Valas, …)

For the full reference — the `gcart_header` contract, the Filter modal skin, the picker skin —
see [report-table-guide.md](report-table-guide.md). **This file is only the migration steps.**

Worked example: `resources/views/report/reportmarketinghistoryso.blade.php`
(done on 2026-08-10; every snippet below is from that page).

---

## Before you start — does the page qualify?

Check these three. If any is missing, fix it first or use the full guide instead.

| Check | How |
|---|---|
| Extends the right layout | Line 1 is `@extends('report.masterreport2')` |
| Has a styled table | Markup contains `<div class="tb-report main">` … `<table class="tb" id="mainTable">` |
| Builds columns from `gcart_header` | The page defines `setDefaultHeader()` and its render function starts with `gcart_header.filter(c => c[2] === 1)` |

If the page instead prints a hardcoded `<thead><tr><th>…</th></tr></thead>` and hardcoded `<td>`s,
it needs the bigger conversion in the full guide first — the interactive header only works on
top of `gcart_header`.

You do **not** need to add any CSS or JS file. `report-table.css` and `report-table.js`
(`window.ReportTable`) are already loaded for every page extending `masterreport2`.

---

## Step 1 — Add the bar container

Put an empty `<div id="rtBar">` **between the toolbar and `.table-outer`**. This is where the
"N kolom tersembunyi" button and the "Tampilan" switcher get drawn.

```blade
<!-- Bar kolom tersembunyi + Tampilan (diisi oleh report-table.js / ReportTable) -->
<div id="rtBar"></div>

<!-- TABLE -->
<div class="table-outer">
    <div class="table-wrap">
        <table class="tb" id="mainTable">
```

> The id is free-form — it just has to match the `bar:` option in Step 3. Use `rtBar` to stay
> consistent with the other pages.

---

## Step 2 — Add the hint line (optional but recommended)

Right after `.table-outer` closes, so users know the header is interactive:

```blade
<div class="rt-hint">
    <i class="bi bi-info-circle"></i>
    Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
    sembunyikan kolom atau atur desimal &amp; total.
</div>
```

---

## Step 3 — Initialise `ReportTable` in `$(document).ready`

Add this **after** the page's existing setup calls (`setDefaultHeader()`, `setReportMode()`, …),
so `gcart_header` is already populated:

```js
ReportTable.init({
    table: '#mainTable',     // the <table class="tb">
    bar: '#rtBar',           // the div from Step 1
    onChange: render         // the page's OWN render function — name varies!
});
```

`onChange` is called after every hide / restore / reorder / decimal / total change. Point it at
whatever repaints the table on that page:

| Page | Its render function |
|---|---|
| `reportmarketinghistoryso` | `render` |
| `reportmarketingso` | `render` |
| `reportaccountingkasharian` | a wrapper — see the "re-render needs arguments" note below |

**If your render function takes arguments** (e.g. `renderRows(rows, groupby)`), wrap it:

```js
onChange: function () {
    if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
}
```

---

## Step 4 — Render the header through `headHtml()`

This is the step that actually turns the header interactive. Find where the render function
builds `<thead>` by hand and replace the whole block.

**Before** (the hand-built version — what most pages have):

```js
// HEADER dinamis dari gcart_header
thead.innerHTML = '<tr>' + cols.map(function(c) {
    const isNum = (c[3] === 'float' || c[3] === 'int');
    return '<th' + (isNum ? ' class="num"' : '') + '>' + c[1] + '</th>';
}).join('') + '</tr>';
```

**After:**

```js
// HEADER dinamis dari gcart_header — dibangun report-table.js (ReportTable) supaya
// kolom bisa diseret untuk diurutkan & punya menu roda gigi (sembunyikan / desimal
// / total). Juga menyegarkan #rtBar (daftar kolom tersembunyi + Tampilan).
thead.innerHTML = ReportTable.headHtml(cols);
```

`headHtml()` adds the `num` class for numeric columns itself, and refreshes `#rtBar` as a side
effect — so you don't call anything else.

> ⚠️ **The one thing that silently breaks everything.** `cols` must come from
> `gcart_header.filter(...)`, never `.map()` or a copy. `headHtml()` uses `indexOf()` to map each
> column back to its index in `gcart_header`; with copied objects every lookup returns `-1`, and
> the gear menu and drag-reorder stop working with no error in the console.
>
> ```js
> const cols = gcart_header.filter(c => c[2] === 1);   // ✅ same object references
> const cols = gcart_header.filter(...).map(c => [...c]);   // ❌ breaks silently
> ```

At this point the page already has drag-reorder, the gear menu, and the hidden-columns bar.
**Stop here if the page has no Detail/Rekap-style mode.**

---

## Step 5 (optional) — Add the "Tampilan" switcher

Only if the page already has a mode that swaps the column layout. This **mirrors** existing
state; it does not create new state.

Add a `views` block to the `ReportTable.init()` call from Step 3:

```js
ReportTable.init({
    table: '#mainTable',
    bar: '#rtBar',
    onChange: render,
    views: {
        label: 'Tampilan',
        options: [
            { value: '0', label: 'Detail', desc: 'Rincian per baris' },
            { value: '1', label: 'Rekap',  desc: 'Ringkasan per grup' }
        ],
        get: function() { return globalReportMode; },     // current value, AS A STRING
        set: function(v) {
            setReportMode(String(v));                     // the page's own mode setter
            if (lastRows.length) { render(); }
        }
    }
});
```

Four rules:

1. **`options` is mandatory.** Without it the switcher silently doesn't render — `views` with
   only `get`/`set` draws nothing. (This is exactly what was wrong on `reportmarketinghistoryso`
   before the upgrade: `init()` was there, but no `options`, no `#rtBar`, and a hand-built
   `thead` — so none of it appeared.)
2. **`value` must be a string** and must match what `get()` returns — comparison is
   `String(o.value) === String(get())`.
3. **In `set()`, decide whether to re-query.** If the modes only rearrange columns of the same
   data → `render()`. If each mode needs different fields from the SP → `makeTable('REPORT')`.
   (`reportmarketingso` and `reportmarketinghistoryso` re-render; `reportaccountingkasharian`
   re-queries because Rp and Valas return different columns.)
4. **Keep other controls in sync.** If the same mode also exists as a `<select>` in a Filter
   modal, set it inside `set()`: `$('#modalReport').val(String(v));`

---

## Step 6 — Test

Reload the page (both assets carry a `?v=filemtime` cache-buster, so a normal refresh is enough)
and check, in order:

1. The bar appears above the table and says "Semua kolom tampil".
2. Drag a column heading onto another → columns swap.
3. Hover a heading → grip dots + gear appear; click the gear → menu opens **below the heading and
   outside the scroll box**, with "Sembunyikan kolom" and, on numeric columns, the Desimal
   stepper and "Tampilkan total" switch.
4. Hide a column → the bar count goes up → open the bar → click the column → it comes back.
5. **Reload the page** — every change above must survive (it is saved to `DBSIMPANHEADER`).
6. Open "Customize Table" — it must show the same state as the gear menu.
7. If you did Step 5: switch Tampilan and confirm the columns change and the label updates.

---

## Troubleshooting

| Symptom | Cause |
|---|---|
| Nothing appears above the table | `#rtBar` missing (Step 1) or `bar:` selector doesn't match its id |
| Header looks normal, no grip/gear | Step 4 not done — still building `<thead>` by hand |
| Gear opens but clicking does nothing; drag does nothing | `cols` isn't from `gcart_header.filter(...)` — see the warning in Step 4 |
| "Tampilan" button never shows | `views.options` missing or empty (Step 5, rule 1) |
| Tampilan shows "-" as the current value | `get()` returns a number but `options[].value` are strings (or vice-versa) |
| Gear menu gets clipped inside the table | Don't restyle `.rt-colmenu` — it is `position:fixed` on `<body>` on purpose, because `.table-wrap` is `overflow:auto` |
| Bar dropdown renders *under* the table | Something changed the z-index layering: toolbar 50, `.rt-bar` 45, sticky `thead` 20 |
| Changes don't survive reload | `g_modeReport` not set before `doSetHeader()`, or `setDefaultHeader()` missing |
| `ReportTable is not defined` | Page doesn't extend `report.masterreport2` |

---

## Quick checklist

- [ ] `<div id="rtBar"></div>` between toolbar and `.table-outer`
- [ ] `.rt-hint` line under the table (optional)
- [ ] `ReportTable.init({ table, bar, onChange })` in `$(document).ready`, after the setup calls
- [ ] `thead.innerHTML = ReportTable.headHtml(cols)` replacing the hand-built header
- [ ] `cols` comes from `gcart_header.filter(...)` — not `.map()`
- [ ] (optional) `views` with a non-empty `options` array, string values, correct `get`/`set`
- [ ] Tested drag, hide, restore, decimals, total — **and that they survive a reload**
