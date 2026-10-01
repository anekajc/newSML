# Handoff — Removing the Detail/Rekap picker + adding the "Order By" switcher

Status as of **2026-09-09**. Written for a fresh Claude session picking up either of these two
jobs on a `resources/views/report/*.blade.php` page.

Two separate jobs live in this doc because they keep landing together on the same pages:

- **Part A** — delete the Detail/Rekap dropdown from the Filter modal and hard-pin the page to
  Detail (`0`).
- **Part B** — give the page an **Order By** switcher above the table so the user picks the
  grouping/sort themselves.

> **Part B has a hard STOP in it.** You must ask the user which orderings they want and get them
> to confirm the values are accepted by the stored procedure **before you write any code**.
> See §B0. Do not skip it, do not guess the values.

---

## TL;DR

| | Part A (Detail/Rekap) | Part B (Order By) |
|---|---|---|
| Safe to do unprompted? | **No** — run the §A0 gate first | **No** — run the §B0 ask first |
| Can you verify it from the repo alone? | Yes, fully | **No** — the SP source is not in this repo |
| Reference implementation | `reportpengadaanreturpembelianacc.blade.php` | same file, `ReportTable.init({ views })` |

---

## Background: why "Rekap" is usually — but not always — dead

Most of these report pages descend from a common ancestor that had a `Report: Detail / Rekap`
dropdown wired to a `DetOrRekap` / `globalReportMode` pair, which fed both `gcart_header`
selection and an `inputDetOrRekap` query param.

On **most** pages that wiring rotted in one of two ways:

1. The controller never reads `inputDetOrRekap` at all — it calls one stored procedure no matter
   what, so the param is sent and silently dropped.
2. The `gcart_header` arrays for the Detail and Rekap branches drifted into being **byte-for-byte
   identical**, so the toggle changes nothing on the client either.

When *both* are true, the toggle is pure dead UI and should be removed.

But on **some** pages Rekap is completely real. `LaporanRegisterPembelianController::doReport()`
branches to two different procs with different signatures:

```php
if ($DetOrRekap == '0') {
  $res = DB::connection('SML')->select('exec Sp_reportBeliAccDet ?,?,?,?,?,?,?,?,?,?,?', $values);  // 11 params
} else if ($DetOrRekap == '1') {
  $res = DB::connection('SML')->select('exec Sp_reportBeliAccRek ?,?,?,?,?,?', $values);            // 6 params
}
```

Removing the picker there would make the Rekap report unreachable. **Always run the gate.**

Also: do not trust the in-file comments about this. The comment block that used to sit in
`reportpengadaanreturpembelianacc.blade.php` claimed Rekap columns were "a real subset of Detail"
and that the Invoice Pembelian page used two different procs — **both claims were false** by the
time they were read. Verify against the controller and the arrays, never against a comment.

---

# Part A — Removing the Detail/Rekap picker

## §A0 The gate (do this before touching anything)

Three checks. All three must come back "dead" before you remove.

**1. Does the controller read the param?**

Find the page's `doReport` route in `routes/report.php`, open that controller, and grep it:

```bash
grep -rn "inputDetOrRekap" app/Http/Controllers/Report/
```

If the controller reads it and branches to a different `exec Sp_...` — **STOP.** Rekap is real.
Report back to the user that this page can't have its picker removed without also dropping a
whole report variant, and let them decide.

**2. Are the `gcart_header` branches actually different?**

In the blade, compare every branch of `setDefaultHeader()` (Detail vs Rekap, across every Order By
slot). If Rekap's column list is a genuinely different/shorter set, removing the toggle removes a
feature — surface that to the user rather than deciding for them.

**3. Does anything else key off `DetOrRekap`?**

```bash
grep -n "DetOrRekap\|globalReportMode\|modalReport" resources/views/report/<page>.blade.php
```

Watch for row-rendering or subtotal logic gated on `DetOrRekap === 0` (the Register Pembelian page
has these at several points).

## §A1 Removal checklist

Once the gate says "dead", remove all of it — do not leave the variable pinned to `0` with the
branches still standing. Dead branches referencing deleted mode constants are worse than the
toggle was.

1. **Modal markup** — delete the `Report` `<select id="modalReport">` and its wrapper. If it was
   the only field in its `rt-section`, delete the whole section (including its
   `<div class="rt-group-label">Pengaturan Laporan</div>`) so you don't ship an empty box.
   Leave a short `{{-- ... --}}` note saying it's pinned to Detail and why.
2. **State** — delete `let DetOrRekap = 0;` and `let globalReportMode = "0";`.
3. **`show.bs.modal` handler** — drop the `$("#modalReport").val(globalReportMode);` line.
4. **`updateFilterBadge()`** — the Report field was never counted toward the badge, so there is no
   logic change; just delete any now-stale comment explaining why it wasn't counted.
5. **`resetAllFilters()`** — drop any `$("#modalReport").val("0")`.
6. **`applyModalFilter()`** — drop the `globalReportMode = ...; DetOrRekap = Number(...)` lines.
7. **Mode constants** — delete the `modereport_rekap*` constants. Rename `modereport_detail*` →
   `modereport_*` (they are now just Order By slots, not Detail-vs-Rekap slots).
8. **`setDefaultHeader()`** — collapse to whatever branching genuinely remains. If every branch
   held the same array, it becomes one flat assignment.
9. **`makeTable()`** — replace `(DetOrRekap === 0) ? modereport_detailX : modereport_rekapX`
   with the bare `modereport_X`.
10. **AJAX payload** — drop `inputDetOrRekap` from the `data` object (the controller never read it,
    per the gate).

## §A2 Worked example — `reportpengadaanreturpembelianacc.blade.php`

Done 2026-09-09, all three gate checks came back dead:

- `LaporanReturPembelianACCController::doReport()` never reads `inputDetOrRekap`; it always calls
  `exec Sp_reportRBeliGDGDet ?,?,?,?,?,?,?,?`.
- All **six** branches of `setDefaultHeader()` (Detail/Rekap × No Bukti/Barang/Supplier) held
  identical column arrays.
- Nothing else referenced `DetOrRekap`.

Result: picker gone, `modereport_rekap*` gone, `setDefaultHeader()` collapsed from six branches to
one flat array, three Order By slots kept (`modereport_nobukti/barang/customer`).

## §A3 Verify before reporting done

```bash
grep -n "DetOrRekap\|globalReportMode\|modalReport\|modereport_rekap" resources/views/report/<page>.blade.php
php -l resources/views/report/<page>.blade.php
```

Only prose comments explaining the removal should survive. Then tell the user which files changed.

---

# Part B — The "Order By" switcher above the table

The switcher renders into `#rtBar` (the bar above the table, next to "kolom tersembunyi" /
"Reset kolom"), driven by the optional `views` block of `ReportTable.init()` in
`public/js/report-table.js`. If `views.options` is empty or missing, **nothing renders** — that's
why some pages have no switcher today.

Picking an option does two things: it sets `globalOrderBy`, and it re-runs `makeTable('REPORT')`
so the value goes back to the SP as `inputOrd`. It is **not** a client-side re-sort.

## §B0 MANDATORY — stop and ask the user first

The stored procedures live in SQL Server (`DBSMLNEW`). **Their source is not in this repo** —
there are no `.sql` files here, so you cannot read what `Ordr` values a proc accepts. Do not query
the database to find out, and do not infer the values from other pages.

**Ask the user these three things and wait for answers before writing code:**

1. **Which orderings do you want on this page?** (e.g. No Bukti, Barang, Supplier, Customer,
   Tanggal…) Don't assume the usual three.
2. **What should each one group by?** You need the exact field name **as it appears in the SP's
   result rows** — this becomes `groupby`, which drives where Subtotal rows break. Getting this
   wrong doesn't error; it silently produces subtotals in the wrong places, or one giant group.
3. **Confirm the SP accepts the value each option sends.** Show the user the literal values you
   intend to send (`'N'`, `'B'`, `'S'`, …) and the proc name from the controller, and ask them to
   confirm against the procedure in SSMS. An unaccepted `Ordr` typically comes back as an empty
   result or an unchanged order — it does **not** raise an error you'd notice.

Only after all three are answered do you write the `views` block.

## §B1 Canonical implementation

From `reportpengadaanreturpembelianacc.blade.php` — copy the shape, not the values:

```js
ReportTable.init({
    table: '#mainTable',
    bar: '#rtBar',
    onChange: function() { applyFilters(); },
    views: {
        label: 'Order By',
        options: [
            { value: 'N', label: 'No Bukti',  desc: 'Dikelompokkan per No Bukti' },
            { value: 'B', label: 'Barang',    desc: 'Dikelompokkan per Nama Barang' },
            { value: 'S', label: 'Supplier',  desc: 'Dikelompokkan per Nama Supplier' }
        ],
        get: function() { return globalOrderBy; },
        set: function(v) {
            setOrderBy(String(v));
            if (lastRows.length) { makeTable('REPORT'); }   // re-fetch: inputOrd is a SP param
        }
    }
});
```

The bar button renders as `Order By: <label of current option>`; `desc` is the small grey line
under each item in the dropdown.

## §B2 Wiring checklist

1. `let globalOrderBy = "N";` (default) + a `setOrderBy(val)` setter.
2. Call `setOrderBy(globalOrderBy)` once in `$(document).ready()` before `ReportTable.init()`.
3. In `makeTable()`, map the current value to **both** a `g_modeReport` slot and a `groupby` field:

```js
if (input_order == "N")      { g_modeReport = modereport_nobukti;  groupby = 'NoBukti'; }
else if (input_order == "B") { g_modeReport = modereport_barang;   groupby = 'NamaBrg'; }
else                         { g_modeReport = modereport_customer; groupby = 'NAMACUSTSUPP'; }
```

4. Send it: `inputOrd: input_order` in the AJAX `data` object.
5. Keep `currentGroupby = groupby` on success, so client-side search re-renders with the same
   grouping.

## §B3 Gotchas

- **One mode slot per ordering.** Even when every ordering shows identical columns, give each its
  own `modereport_*` number. `doSetHeader(g_modeReport)` persists the user's column
  show/hide/reorder per mode in `DBSIMPANHEADER`; sharing one slot makes customisations bleed
  across orderings.
- **`groupby` must match the SP's field name exactly**, including case as it arrives in JSON
  (`NoBukti` vs `Nobukti` vs `kodebrg` differ between procs — the PR page genuinely uses
  lowercase `kodebrg` for its Outstanding proc).
- **Newly added columns won't appear for existing users** until they hit "Reset kolom", because
  `doSetHeader()` loads the saved layout. Don't remove that button; do mention it to the user if
  they report a missing column.
- **Two modes on one page** (e.g. an Outstanding toggle) need distinct slot ranges — the PR/PO
  pages offset the second mode by `OUT_MODE_OFFSET = 20` so saved layouts don't collide under the
  same href.
- If the two modes need different option lists, swap `viewsCfg.options` then call
  `ReportTable.refresh()` — **do not** call `ReportTable.init()` a second time.

---

## Page inventory (verified 2026-09-09)

Pages still carrying `id="modalReport"`. Each still needs its own §A0 gate — this table is a
starting point, not a verdict.

| Page | Gate finding so far |
|---|---|
| `reportpengadaanregisterpembelian.blade.php` | **Rekap is REAL.** Controller branches to `Sp_reportBeliAccDet` (11 params) vs `Sp_reportBeliAccRek` (6 params), and the blade has several `if (DetOrRekap === 0)` render branches. **Do not remove without asking the user.** |
| `reportmarketingso.blade.php` | Likely dead, but messy — the Report block is partly commented out already and the JS defensively checks `if ($("#modalReport").length)` before touching it. The **routed** controller (`App\Http\Controllers\Report\LaporanMarketingSOController`) does **not** read `inputDetOrRekap`. The only file that does is `LaporanMarketingSOController(old).php`, which declares `namespace App\Http\Controllers;` (root, not `\Report`) while `routes/report.php` imports the `\Report` one — i.e. it is a dead duplicate, per the "read the namespace, not the folder" rule in `CLAUDE.md`. |
| `reportmarketinglaporanoutso.blade.php` | Has `DetOrRekap`; controller not yet checked. |

Re-run the inventory with:

```bash
grep -rln 'id="modalReport"' resources/views/report/
grep -rn "inputDetOrRekap" app/Http/Controllers/Report/
```

## Reference implementations

| Page | What to copy from it |
|---|---|
| `reportpengadaanreturpembelianacc.blade.php` | Both parts — cleanest example of the end state |
| `reportpengadaanpr.blade.php` | Order By where each ordering **rearranges columns** and flips subtotal/grand-total per mode |
| `reportpengadaanpopo.blade.php` | Order By shared across two report modes via `OUT_MODE_OFFSET` |
