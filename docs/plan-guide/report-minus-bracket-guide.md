# Report Number Format — Negative Brackets + Indonesian Separators

**What this changes (report pages only):**

| | Today | Target |
|---|---|---|
| Negative number | `-1,234.50` (or `-1.234,50` on pages that use `toLocaleString`) | **`(1.234,50)`** — brackets, **no minus sign** |
| Thousand / decimal separators | mostly `1,234.50` (`format_number`), a few pages already `1.234,50` | **`1.234,50`** everywhere (thousand = `.`, decimal = `,`) |

Both changes go through the **same few formatter functions**, so they are planned together and done in one pass.

**How to use this file:** a session asked to "apply the report number format" reads this file, does the phases in §5 in order,
and ticks the verification boxes in Appendix A. The page inventory comes from
[docs/file_list/report-files-list.md](../file_list/report-files-list.md) (the 98 report pages that are actually in use).

> Read [CLAUDE.md](../../CLAUDE.md) first: ask the user follow-up questions until 95% confident, and finish by listing every
> file you changed. Do not ask about, or remind the user of, the `DBUSERFILTER` table (it exists; unrelated to this task).

---

## 1. Decisions (already made — do not re-litigate)

| Topic | Decision |
|---|---|
| Negative format | **`(123.000)`** — brackets only, no minus sign inside. |
| Separators | **`123.000,00`** — `.` for thousands, `,` for decimals. Integers: `1.234`. |
| Which numbers | **Every number in a report table** (amounts, quantities, subtotal and grand-total rows), not only money. |
| KPI cards and charts | Switch to the Indonesian separators **too** (`1,2 jt`, not `1.2 jt`). Brackets for negatives there: see open decision D1. |
| Rollout | **Central first, then the leftovers**: fix the shared formatters once (fixes most pages), then the few pages with their own formatter. |
| Excel `.xlsx` export | Cells stay **real numbers**; an Excel number format shows negatives in brackets. |
| CSV export | **Real negative number** (`-123,00`). CSV has no formatting layer; bracket text would turn the number into text. It already uses `;` and a decimal comma — no change. |
| Server-side print/PDF | In scope, **but no live caller exists today** (§4.6) — helper + replacement list are prepared, execution waits for a confirmed caller. |
| Out of scope | Transaction / master / dashboard pages and every input field; `numberWithCommas()` / `numberNoCommas()`; `public/js/ajc-func-core.js`. |

### Open decisions (a default is given — change only if the user says so)

| # | Question | Default used in this guide |
|---|---|---|
| D1 | Should negative numbers in KPI cards / chart ticks / tooltips (`fmtShort`) also get brackets? (The user asked for the separators there; brackets were not discussed for them.) | **Yes** — otherwise one page shows `(1.234,50)` in the table and `-1,2 jt` in a card. |
| D2 | Is there a live server-side print/PDF path for reports that this survey missed? | **No** (none found, §4.6). |
| D3 | Closing-bracket alignment: positives have no `)`, so digits are off by one character in a right-aligned column. Add CSS padding to positive cells? | **No** for now; cosmetic, can follow later. |

---

## 2. Target format spec

Rule for a displayed amount `n` with `d` decimals:

1. Round first: `s = n.toFixed(d)`. A value that **rounds to zero is zero** — no brackets, no minus (`-0.004`, 2 → `0,00`).
2. Negative (after rounding): `(` + grouped absolute value + `)`. The bracket wraps **everything displayed**, including a prefix: `(Rp 1.234,00)`.
3. Grouping: `.` every three integer digits; `,` before decimals.
4. **Raw mode is untouched:** `format_number(n, d, false)` (third argument `false`) returns plain `n.toFixed(d)` — dot decimal, no grouping, no brackets. That is for values fed to inputs or calculations.

| Call | Output |
|---|---|
| `format_number(1234567.891, 2)` | `1.234.567,89` |
| `format_number(-1234567.891, 2)` | `(1.234.567,89)` |
| `format_number(-0.004, 2)` | `0,00` |
| `format_number(1234, 0)` | `1.234` |
| `format_number(-1234, 0)` | `(1.234)` |
| `format_number(0.5, 2)` | `0,50` |
| `format_number(-1234.5, 2, false)` | `-1234.50` (raw, unchanged) |
| `fmtN(-3500000)` | `(3.500.000)` |
| `fmtRp(-1234.5)` | `(1.234,50)` |
| `fmtShort(-1500000)` | `(1,5 jt)` |
| `fmtShort(2500)` | `3 rb` (existing rounding, unchanged) |

---

## 3. What exists today (survey, 2026-11-17)

**The 98 live report pages** (55 saved-filter pages + 1 skipped + 7 picker-only + 35 without a filter modal):

| How the page formats numbers | Pages | Action |
|---|---:|---|
| Only through the shared `format_number()` (or the layout's own table renderers, which also call it) | 82 | **Phase 1** covers them — nothing to edit in the page |
| `fmtN` / `fmtFull` from `report-table.js` (Neraca Lajur, Trial Balance, with a `.neg` CSS class) | 2 | **Phase 1** (`report-table.js`) |
| Their own numeric formatter (`toLocaleString`) | 3 | **Phase 2**: `reportaccountingkasharian`, `reportaccountingbankharian`, `reportaccountingbukubesar` |
| Their own KPI/chart abbreviation `fmtShort` (`1.2 jt`, `5 rb`) | 11 | **Phase 2** — 10 have Chart.js charts whose tooltips and axis ticks call `fmtShort` |

(`toLocaleString` hits on `new Date().toLocaleString(...)` are print timestamps, not numbers — ignore them.)

### 3.1 The central formatters

| Formatter | Where | Used by |
|---|---|---|
| `format_number(n, _decimal = 0, _comma = true)` → `numberWithCommas(n.toFixed(d))` (`1,234.50`) | defined **inline** in `resources/views/report/newmaster2x.blade.php` (~line 1415) and `report/newmaster2.blade.php` | `newmaster2x` is the parent of `masterreport2` / `masterreport4` / `masterreportGudang` ⇒ ≈ 104 report views; `newmaster2` serves `masterreportNeraca` ⇒ `reportaccountingneraca` |
| `window.fmtN`, `fmtFull` (alias), `fmtRp`, `num` | `public/js/report-table.js` lines ~80–105 (each guarded by `typeof window.x !== 'function'`, so a page that defines its own wins) | Neraca Lajur, Trial Balance (`fmtFull`) |
| `formatAngka(value)` (`toLocaleString('id-ID')`, 2 decimals) | `report/masterreport2.blade.php` ~line 1046 | **no live page calls it** — optional consistency edit |

Not to be touched, and why:
- `numberWithCommas()` / `numberNoCommas()` (same layouts) stay `1,234.50`: other code uses them to parse/fill inputs.
- `public/js/ajc-func-core.js` also defines `format_number` (identical old version). It is loaded only by 4 gudang/purchasing transaction pages and the unused `report/mastercore` layout — **no report layout loads it**, so leave it.
- Layout copies that are not used by any live page (`report/newmaster.blade.php`, `report/newmaster2backup.blade.php`): leave.

### 3.2 Safety findings (checked, so the central change is safe)

- **No live page parses formatted text back into a number**: no `numberNoCommas(`, no `parseFloat($(...).text())`, no `replace(/,/g, '')` on cell text, no check for a leading `-` in a formatted string. Totals and sums are computed from numbers.
- **No `format_number(..., false)` call** exists in report pages today (raw mode is kept anyway).
- **Only one non-report file calls `format_number`**: `gudang/ubahkemasanbarang.blade.php` (layout `newmasterTest`, uses the `ajc-func-core.js` version) — unaffected.
- **No report page formats numbers in Blade** (`number_format` count is 0 in all 98 pages).

---

## 4. Per-area plan (the exact edits)

### 4.1 `format_number` — both report layouts

In `resources/views/report/newmaster2x.blade.php` **and** `resources/views/report/newmaster2.blade.php`, replace the body (keep the signature and `numberWithCommas` untouched):

```js
// Angka untuk TAMPILAN laporan: 1.234.567,89 ; negatif di dalam kurung tanpa minus: (1.234.567,89).
// _comma = false -> nilai mentah n.toFixed(d) (titik desimal, tanpa pemisah/kurung) utk input & perhitungan.
function format_number(n, _decimal = 0, _comma = true) {
    var s = n.toFixed(_decimal);
    if (!_comma) { return s; }
    var neg = s.charAt(0) === '-' && /[1-9]/.test(s);      // "-0.00" itu nol, bukan negatif
    if (s.charAt(0) === '-') { s = s.slice(1); }
    var p = s.split('.');
    var out = p[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.') + (p[1] ? ',' + p[1] : '');
    return neg ? '(' + out + ')' : out;
}
```
Do not mask `undefined`/`NaN` (`n.toFixed` throws today and still does — that is how bad data shows up).

### 4.2 `public/js/report-table.js` — `fmtN`, `fmtRp`

```js
// fmtN: tanpa desimal (default toLocaleString), negatif -> (1.234). Komentar lama menyebut "Rp" tetapi kodenya tidak
// pernah menambahkannya -- jangan tambahkan.
window.fmtN = function (v) {
  if (v === null || v === undefined || v === '') return '-';
  var n = window.num(v);
  var s = Math.abs(n).toLocaleString('id-ID');
  return (n < 0 && s !== '0') ? '(' + s + ')' : s;
};
// fmtRp: 2 desimal, negatif -> (1.234,50)
window.fmtRp = function (v) {
  var n = window.num(v);
  var s = Math.abs(n).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  return (n < 0 && s !== '0,00') ? '(' + s + ')' : s;
};
```
`fmtFull` is the alias of `fmtN` and needs no edit. The layouts load this file with `?v=filemtime`, so browsers pick up the change.

### 4.3 Excel export — `public/js/report-export.js`

One function (line ~414) feeds both `toXlsx` and `fromRows`:

```js
function numFmt(decimals) {
  var pos = decimals > 0 ? '#,##0.' + new Array(decimals + 1).join('0') : '#,##0';
  return pos + ';(' + pos + ')';        // positif;negatif-dalam-kurung (nilai tetap angka negatif asli)
}
```
- The parser already understands brackets (`splitSign` strips `(...)` and marks the number negative) and both separator styles (it votes per table; a single dot followed by exactly three digits reads as thousands). **No parser change.**
- Excel shows the thousand/decimal characters according to the **viewer's Windows regional settings** — do not try to force them.
- **CSV**: no change (real negatives, `;`, BOM, decimal comma).
- Check that the `report-export.js` script tag in the layouts has a `?v=filemtime` query like `report-table.js` does; add it if missing.

### 4.4 Pages with their own formatter (Phase 2)

| Page | Edit |
|---|---|
| `reportaccountingkasharian`, `reportaccountingbankharian` | two identical lambdas each: `let f = (v) => Number(v).toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 });` → `let f = (v) => fmtRp(v);` (`fmtRp` now brackets negatives; same 2-decimal id-ID output). |
| `reportaccountingbukubesar` | two formatters of the form `(v < 0 ? '-' : '') + 'Rp ' + Math.abs(v).toLocaleString('id-ID', {...})` → `v < 0 ? '(Rp ' + s + ')' : 'Rp ' + s`, with `s` the formatted absolute value; keep the zero guard (a value that formats to zero gets no brackets). The `Rp` prefix goes **inside** the brackets. |
| the 11 `fmtShort` pages | replace the 6-line function (see below). |

`fmtShort` — 11 copies (10 identical, 1 differs in layout), used for KPI cards and by every Chart.js tooltip and axis `callback` on the 10 chart pages:

```js
function fmtShort(v) {
  v = Math.round(num(v)); const a = Math.abs(v);
  const id = (x) => String(x).replace('.', ',');              // desimal pakai koma
  let s;
  if (a >= 1e9) s = id((a / 1e9).toFixed(1).replace(/\.0$/, '')) + ' M';
  else if (a >= 1e6) s = id((a / 1e6).toFixed(1).replace(/\.0$/, '')) + ' jt';
  else if (a >= 1e3) s = (a / 1e3).toFixed(0) + ' rb';
  else s = String(a);
  return v < 0 ? '(' + s + ')' : s;                           // brackets: open decision D1
}
```
Pages: `reportaccountinghpp`, `hutanglph`, `hutangumur`, `labarugi`, `labarugitahunan`, `piutanglpp`, `piutangumur` (all `reportaccounting…`), `reportmarketinganalisakotor`, `reportmarketingso` (KPI cards only, no chart), `reportpengadaanregisterpembelian`, `reportstockmutasistock`. Do the edit with a script that asserts exactly one `function fmtShort` per file (as in the saved-filter work); the copy that differs must be read first.

Chart.js (loaded from `plugins/chart.js/chart.umd.min.js`, v4) also formats **its own** tick labels with the browser locale wherever a page has no `callback`. All 10 chart pages have `ticks.callback` and tooltip callbacks that call `fmtShort`, so nothing more is needed; after the change, confirm visually that no axis still shows `1,000`-style labels.

### 4.5 Optional consistency

`formatAngka` in `report/masterreport2.blade.php` (no live caller): apply the same bracket rule, or leave. Do not spend time on it unless asked.

### 4.6 Server-side print/PDF (Phase 4 — deferred)

- Server-side numbers use PHP `number_format($n, 2)` → `1,234.50`, negatives `-1,234.50`. They live in `app/Traits/ReportTrait.php` (18 calls; builds HTML for PDF/Excel via the `templatepdf` / `templateexcel` views) and `app/Http/Controllers/CetakController.php` (8 calls; Kas/Bank/Giro vouchers). The `Master/`, `Purchasing/`, `Report/` copies of these files are the legacy duplicates described in CLAUDE.md — edit the **root** files only.
- **No route reaches them today:** nothing in `routes/*.php` references `CetakController`, `ReportController` or `LaporanModelController` (the `ReportTrait` users), and none of the 98 live report pages formats numbers in PHP. The live report "print" button is the browser printing the on-screen table, which the Phase 1 edits already cover.
- **Do not edit these files until a live caller is confirmed (open decision D2).** When it is, add one helper (e.g. in `app/Traits/GlobalTrait.php`) and replace the calls:

```php
// Angka untuk dokumen cetak: 1.234,50 ; negatif (1.234,50) tanpa minus ; yang membulat ke nol tidak diberi kurung.
public function fmtReportNumber($n, $dec = 2)
{
    $s = number_format(abs((float) $n), $dec, ',', '.');
    return (round((float) $n, $dec) < 0) ? '(' . $s . ')' : $s;
}
```

---

## 5. Phases

| Phase | Work | Files | Done |
|---|---|---|---|
| 1 | Central formatters | `report/newmaster2x.blade.php`, `report/newmaster2.blade.php` (§4.1); `public/js/report-table.js` (§4.2); `public/js/report-export.js` (§4.3) | ☑ |
| 2 | Leftover pages | `reportaccountingkasharian`, `reportaccountingbankharian`, `reportaccountingbukubesar` (§4.4); the 11 `fmtShort` pages (§4.4) | ☑ |
| 3 | Verify (§6) and tick Appendix A | — | ☐ |
| 4 | Server-side print/PDF | only after open decision D2 is answered (§4.6) | ⏸ |

Phase 1 changes **all 98 pages** at once (82 + 2 are finished by it; the other 14 also need Phase 2). Do it in **one**
commit-sized step, then verify immediately (§6).

---

## 6. Verification

There is no JS test runner; the formatters are pure functions, so check them with Node, then check the screens.

**6.1 Formatter check (Node)** — paste the new `format_number`, `fmtN`, `fmtRp`, `fmtShort` into a scratch file with a stub `num = v => parseFloat(v) || 0` and compare with the table in §2. The cases that matter: negative with 2 and 0 decimals, `-0.004` → `0,00`, exactly `1000`, `999999`, `1234567.891`, `_comma=false`, and `0`.

**6.2 Screens** — pick the pages most likely to contain negatives and eyeball them with real data: Laba Rugi (+ Tahunan), Neraca (+ Penunjang, Neraca Lajur), Trial Balance, Kas/Bank Harian (saldo rows), Buku Besar, Hutang/Piutang Kartu / Umur / Pelunasan, Jurnal pages, Mutasi Stock, Koreksi Stock, Retur pages. Check: negatives bracketed with **no minus**, all separators `.`/`,`, subtotal / grand-total rows, KPI cards, chart tooltips and axis ticks, column "desimal" gear setting still honoured (0, 2, 4 decimals).

**6.3 Exports** — on one 2-decimal report with negatives and one integer-only report:
- `.xlsx`: cells are numbers (`ISNUMBER` true), negatives display in brackets, totals are right.
- `.csv`: negatives are `-123,00`, still numeric, `;` separated.
- Integer-only report: `1.234` in the table must export as 1234, not 1,234 (the parser reads a dot + exactly three digits as thousands).

**6.4 Regression** — Excel export of a page that was already `1.234,50` before (Kas/Bank Harian) is unchanged; the transaction pages (e.g. a Kas or PO form) still show the old `1,234.50` in their inputs.

---

## 7. Gotchas

- **Two copies of `format_number` must match** (`newmaster2x` and `newmaster2`); the old `ajc-func-core.js` copy is deliberately different and stays that way.
- **Do not touch `numberWithCommas`**: it is shared with input handling; `format_number` no longer calls it.
- **`fmtN`'s old comment says "Rp 3.500.000" but the code never adds "Rp"**; keep the code's behaviour.
- **A page that defines its own `fmtN`/`fmtRp`/`num` overrides the shared one** (the `typeof` guards in `report-table.js`); if you find one, edit that copy too.
- **Sums must come from numbers, never from displayed text.** The survey found none that do; if a new page does, fix the page, not the formatter.
- **Mixed styles are gone only for report pages.** Transaction/master/dashboard pages keep `1,234.50`; that is intended.
- **Excel decimal/thousand characters depend on the viewer's Windows locale**; the file itself only carries the number and its format.
- **`.neg` CSS class** (Neraca Lajur, Trial Balance) colours negatives; it keeps working because it is computed from the number, not from the text.
- **Re-check `git diff` after any scripted multi-file edit**: assert that each anchor matches exactly once before writing (as in the saved-filter batches).

---

## Appendix A — Page inventory and verification (98 live report pages)

Source: [docs/file_list/report-files-list.md](../file_list/report-files-list.md) (every page in use: saved-filter pages, the skipped one, the picker-only ones and the pages without a filter modal).
Tick a page after you have **looked at it with real data** (§6.2): negatives in brackets without a minus, separators `.`/`,`, totals, KPI cards and chart labels.
"Phase 1" = fixed by the shared formatters alone; "Phase 1 + 2" = also needs its own edit (§4.4). 📊 = has Chart.js charts.

Counts: 82 pages need Phase 1 only (excluding the 2 on `fmtN`/`fmtFull`), 2 use `fmtN`/`fmtFull`,
3 have their own `toLocaleString` formatter, 11 use `fmtShort` (10 with charts).

### Accounting (42)

- [ ] [reportaccountingaktiva](../../resources/views/report/reportaccountingaktiva.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingbankharian](../../resources/views/report/reportaccountingbankharian.blade.php) — own `toLocaleString` + `format_number` — Phase 1 + 2
- [ ] [reportaccountingbiaya](../../resources/views/report/reportaccountingbiaya.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingbiayapenyusutan](../../resources/views/report/reportaccountingbiayapenyusutan.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingbonsementara](../../resources/views/report/reportaccountingbonsementara.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingbukubesar](../../resources/views/report/reportaccountingbukubesar.blade.php) — own `toLocaleString` — Phase 1 + 2
- [ ] [reportaccountingcosting](../../resources/views/report/reportaccountingcosting.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghisbon](../../resources/views/report/reportaccountinghisbon.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghpp](../../resources/views/report/reportaccountinghpp.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountinghutangkartu](../../resources/views/report/reportaccountinghutangkartu.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghutanglhpjt](../../resources/views/report/reportaccountinghutanglhpjt.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghutanglph](../../resources/views/report/reportaccountinghutanglph.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountinghutanglphto](../../resources/views/report/reportaccountinghutanglphto.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghutangoutstandingJT](../../resources/views/report/reportaccountinghutangoutstandingJT.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghutangoutstandingnota](../../resources/views/report/reportaccountinghutangoutstandingnota.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghutangpelunasan](../../resources/views/report/reportaccountinghutangpelunasan.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountinghutangumur](../../resources/views/report/reportaccountinghutangumur.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountingjurnalcomputer](../../resources/views/report/reportaccountingjurnalcomputer.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportaccountingjurnalkoreksi](../../resources/views/report/reportaccountingjurnalkoreksi.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingjurnalmemorial](../../resources/views/report/reportaccountingjurnalmemorial.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingjurnalpenerimaanbank](../../resources/views/report/reportaccountingjurnalpenerimaanbank.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingjurnalpenerimaankas](../../resources/views/report/reportaccountingjurnalpenerimaankas.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingjurnalpengeluaranbank](../../resources/views/report/reportaccountingjurnalpengeluaranbank.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingjurnalpengeluarankas](../../resources/views/report/reportaccountingjurnalpengeluarankas.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingjurnalpenutup](../../resources/views/report/reportaccountingjurnalpenutup.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingkasharian](../../resources/views/report/reportaccountingkasharian.blade.php) — own `toLocaleString` + `format_number` — Phase 1 + 2
- [ ] [reportaccountinglabarugi](../../resources/views/report/reportaccountinglabarugi.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountinglabarugitahunan](../../resources/views/report/reportaccountinglabarugitahunan.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountinglaporanarus](../../resources/views/report/reportaccountinglaporanarus.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingneraca](../../resources/views/report/reportaccountingneraca.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingneracalajur](../../resources/views/report/reportaccountingneracalajur.blade.php) — `fmtN`/`fmtFull` + `format_number` — Phase 1
- [ ] [reportaccountingneracapenunjang](../../resources/views/report/reportaccountingneracapenunjang.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingpiutangkartu](../../resources/views/report/reportaccountingpiutangkartu.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingpiutanglpp](../../resources/views/report/reportaccountingpiutanglpp.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountingpiutanglppto](../../resources/views/report/reportaccountingpiutanglppto.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingpiutangoutstandingJT](../../resources/views/report/reportaccountingpiutangoutstandingJT.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingpiutangpelunasan](../../resources/views/report/reportaccountingpiutangpelunasan.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingpiutangspjt](../../resources/views/report/reportaccountingpiutangspjt.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingpiutangumur](../../resources/views/report/reportaccountingpiutangumur.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportaccountingposisibank](../../resources/views/report/reportaccountingposisibank.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingskb](../../resources/views/report/reportaccountingskb.blade.php) — `format_number` — Phase 1
- [ ] [reportaccountingtrialbalance](../../resources/views/report/reportaccountingtrialbalance.blade.php) — `fmtN`/`fmtFull` + `format_number` — Phase 1

### Marketing (20)

- [ ] [reportlaporanmarketingspb](../../resources/views/report/reportlaporanmarketingspb.blade.php) — `format_number` — Phase 1
- [ ] [reportlaporanmarketingspbacc](../../resources/views/report/reportlaporanmarketingspbacc.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportlaporanmarketingspbhrgso](../../resources/views/report/reportlaporanmarketingspbhrgso.blade.php) — `format_number` — Phase 1
- [ ] [reportlaporanmarketingspbLT](../../resources/views/report/reportlaporanmarketingspbLT.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketinganalisakotor](../../resources/views/report/reportmarketinganalisakotor.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportmarketingevalsolunas](../../resources/views/report/reportmarketingevalsolunas.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketinghistoryoutso](../../resources/views/report/reportmarketinghistoryoutso.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketinghistoryso](../../resources/views/report/reportmarketinghistoryso.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketinginvoice](../../resources/views/report/reportmarketinginvoice.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportmarketinglaporanoutso](../../resources/views/report/reportmarketinglaporanoutso.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingoutgudanggtc](../../resources/views/report/reportmarketingoutgudanggtc.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingoutspbhrgso](../../resources/views/report/reportmarketingoutspbhrgso.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingoutsppb](../../resources/views/report/reportmarketingoutsppb.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingpos](../../resources/views/report/reportmarketingpos.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportmarketingregsaleinv](../../resources/views/report/reportmarketingregsaleinv.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingreguangmuka](../../resources/views/report/reportmarketingreguangmuka.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportmarketingreturpenjualan](../../resources/views/report/reportmarketingreturpenjualan.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingsalesanalisa](../../resources/views/report/reportmarketingsalesanalisa.blade.php) — `format_number` — Phase 1
- [ ] [reportmarketingso](../../resources/views/report/reportmarketingso.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2
- [ ] [reportmarketinguangmukaout](../../resources/views/report/reportmarketinguangmukaout.blade.php) — `format_number` — Phase 1

### Pengadaan (Purchasing) (17)

- [ ] [reportpengadaanclosepo](../../resources/views/report/reportpengadaanclosepo.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanclosingpr](../../resources/views/report/reportpengadaanclosingpr.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportpengadaandebetnote](../../resources/views/report/reportpengadaandebetnote.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanhispo](../../resources/views/report/reportpengadaanhispo.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaaninvoicepembelian](../../resources/views/report/reportpengadaaninvoicepembelian.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanosp](../../resources/views/report/reportpengadaanosp.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanoutstandingum2](../../resources/views/report/reportpengadaanoutstandingum2.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanoutstandingumbeli](../../resources/views/report/reportpengadaanoutstandingumbeli.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportpengadaanpenerimaangudang](../../resources/views/report/reportpengadaanpenerimaangudang.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanpgoutstandingpo](../../resources/views/report/reportpengadaanpgoutstandingpo.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanpopo](../../resources/views/report/reportpengadaanpopo.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanpr](../../resources/views/report/reportpengadaanpr.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanregisterosinvoice](../../resources/views/report/reportpengadaanregisterosinvoice.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanregisterpembelian](../../resources/views/report/reportpengadaanregisterpembelian.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportpengadaanrekapinvoicepembelian](../../resources/views/report/reportpengadaanrekapinvoicepembelian.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanreturpembelianacc](../../resources/views/report/reportpengadaanreturpembelianacc.blade.php) — `format_number` — Phase 1
- [ ] [reportpengadaanreturpembeliangdg](../../resources/views/report/reportpengadaanreturpembeliangdg.blade.php) — `format_number` — Phase 1

### Stock (8)

- [ ] [reportstockfastslowdeadmoving](../../resources/views/report/reportstockfastslowdeadmoving.blade.php) — `format_number` — Phase 1
- [ ] [reportstockkartustock](../../resources/views/report/reportstockkartustock.blade.php) — `format_number` — Phase 1
- [ ] [reportstockmutasistock](../../resources/views/report/reportstockmutasistock.blade.php) — `fmtShort` + `format_number` — Phase 1 + 2 — 📊
- [ ] [reportstockmutasistockharian](../../resources/views/report/reportstockmutasistockharian.blade.php) — `format_number` — Phase 1
- [ ] [reportstockmutasistockpermerk](../../resources/views/report/reportstockmutasistockpermerk.blade.php) — `format_number` — Phase 1
- [ ] [reportstocksaldostock](../../resources/views/report/reportstocksaldostock.blade.php) — `format_number` — Phase 1
- [ ] [reportstockstockfisikgudang](../../resources/views/report/reportstockstockfisikgudang.blade.php) — `format_number` — Phase 1
- [ ] [reportstockstockkartudanopname](../../resources/views/report/reportstockstockkartudanopname.blade.php) — `format_number` — Phase 1

### Gudang & others (11)

- [ ] [reportclosingopname](../../resources/views/report/reportclosingopname.blade.php) — `format_number` — Phase 1
- [ ] [reportclosingtransfercabang](../../resources/views/report/reportclosingtransfercabang.blade.php) — `format_number` — Phase 1
- [ ] [reportgudangoutkonsin](../../resources/views/report/reportgudangoutkonsin.blade.php) — `format_number` — Phase 1
- [ ] [reporthistoripenyerahansample](../../resources/views/report/reporthistoripenyerahansample.blade.php) — `format_number` — Phase 1
- [ ] [reportkoreksistock](../../resources/views/report/reportkoreksistock.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportlaporansuratjalan](../../resources/views/report/reportlaporansuratjalan.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reportopname](../../resources/views/report/reportopname.blade.php) — `format_number` — Phase 1
- [ ] [reportpemakaian](../../resources/views/report/reportpemakaian.blade.php) — `format_number` — Phase 1
- [ ] [reportpermintaanpemakaian](../../resources/views/report/reportpermintaanpemakaian.blade.php) — layout table renderer (`format_number`) — Phase 1
- [ ] [reporttransferkecabangblmditerima](../../resources/views/report/reporttransferkecabangblmditerima.blade.php) — `format_number` — Phase 1
- [ ] [reportubahkemasan](../../resources/views/report/reportubahkemasan.blade.php) — `format_number` — Phase 1

