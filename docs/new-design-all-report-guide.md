# New Design — Report Pages Main Guide

**This is the entry point. Read this file first, every time, before touching any page.**
It holds the pre-flight checks, the ask-first protocol, and the constraints that apply to every
job. The feature detail lives in three sub-guides:

| Want to add… | Sub-guide |
|---|---|
| Draggable/reorderable column headers, gear menu, hidden-column bar, "Tampilan" switcher | [new-slider-table-guide.md](new-slider-table-guide.md) |
| The redesigned "Filter Laporan" dialog (sections, combo pickers, badge, reset) | [new-filter-modal-ui-guide.md](new-filter-modal-ui-guide.md) |
| The entity picker modal (`#formSelect`) with click-the-row selection | [new-cust-supp-modal-guide.md](new-cust-supp-modal-guide.md) |

These are **page-type agnostic**. They were built from the `report` module, but the same designs
can be applied to marketing / master / purchasing / gudang / accounting pages — with the extra
prerequisites in §3.

**Porting this whole system to a page that isn't `report/masterreport2`?** Go straight to
[new-design-gudang-style-guide.md](new-design-gudang-style-guide.md) instead of this file — it's
the worked, already-solved version of §3 below, built from the gudang module's own port.

> Supersedes `report-table-guide.md` and `report-table-upgrade-steps.md`. Those remain on disk as
> an archive but are no longer the source of truth.

---

## 1. Workflow

Follow in order. Do not skip step 2.

1. **Read this file** and the relevant sub-guide(s).
2. **Run the pre-flight** (§2). It tells you what the page already has.
3. **Apply the ask-first protocol** (§4). If any trigger fires, ask the user *before* editing.
4. **Make the change**, obeying the constraints in §5.
5. **Verify** (§6) and report exactly which files changed.

---

## 2. Pre-flight — find out what you're dealing with

Run these before editing. The answers decide everything downstream.

```bash
# 1. Which layout does the page extend?
head -1 resources/views/<path>/<page>.blade.php

# 2. Does that layout load the shared assets + which Bootstrap?
grep -n "report-table.css\|report-table.js\|bootstrap@5\|js/bootstrap.min.js" \
     resources/views/<module>/<layout>.blade.php

# 3. Does the page build its table from gcart_header?
grep -n "gcart_header\|setDefaultHeader\|doSetHeader" resources/views/<path>/<page>.blade.php

# 4. Which picker modal (if any) does it include, and how many pages share it?
grep -n "@include('report.modal" resources/views/<path>/<page>.blade.php
grep -rl "@include('report.<modalName>')" --include=*.blade.php resources/views/ \
  | grep -v "(070526)\|(180526)\|(Old)\|report1705\|backup" | wc -l
```

### Page classes

**Class A — `@extends('report.masterreport2')`**
Has everything: `report-table.css`, `report-table.js` (`window.ReportTable`), Bootstrap 5 **and**
Bootstrap 4, the `gcart_header` engine, the "Atur Kolom" and "Filter Data" modals, and the shared
formatters. Sub-guides apply directly.

**Class B — any other layout** (`marketing/newmaster`, `master/newmaster`,
`purchasing/newmaster`, `accounting/newmaster`, …)
Verified: these load **Bootstrap 4 only** and **none** of `report-table.css`, `report-table.js`,
or the `gcart_header` engine. ? go to §3, and **ask before proceeding** (§4 trigger B).

---

## 3. Prerequisites for Class B (non-report) pages

| Need | How | Note |
|---|---|---|
| The CSS | add `<link ... public/css/report-table.css>` to that module's layout `<head>` | affects every page on that layout ? shared-file edit |
| `window.ReportTable` + `loadingHtml`/`fmtRp` | add `<script ... public/js/report-table.js>` after jQuery | same |
| Formatters `format_date`, `format_number`, `currencyNormalizer`, `nullToEmpty` | present in `report/newmaster2` only — check the target layout, provide equivalents if absent | |
| Bootstrap Icons (`bi-gear`, `bi-info-circle`) | check the layout; otherwise icons render blank | |
| Modal close attribute | **`data-dismiss` (BS4)** on Class B — *not* `data-bs-dismiss` | opposite of Class A, see §5.1 |
| `gcart_header` engine (`doSetHeader`, `doSimpanHeader`, `doButtonVisibility`, `doSetDesimal`, `doButtonTotal`, `doMoveHeader`) | **only exists in `masterreport2`** as a layout-provided helper — see the note below | |

The interactive table is the hard one: everything else is CSS + markup. The engine's PHP-side half
(the functions above) isn't portable as a shared helper — but it doesn't need to be: those
functions are thin wrappers around two **global** routes (`globalfunctions_doLoadHeader` /
`globalfunctions_doSimpanHeader`, not gated to any layout), so a page outside `masterreport2` can
copy the wrapper functions page-locally and call the same routes. This exact recipe — plus the
CSS/JS asset question above, the button/action-column conventions, the Tampilkan+pager pattern, and
a non-DataTables entity-picker pattern — is fully worked out in
[new-design-gudang-style-guide.md](new-design-gudang-style-guide.md), built from the gudang module's
own port of this system. Read that instead of re-deriving this from scratch; only ask the user if
your target page's situation doesn't match what it covers.

---

## 4. Ask-first protocol

### 4a. Different file structure ? ask **with options**

When the target page doesn't match the reference implementation — different layout, different
modal markup, extra/missing filter fields, a differently-shaped render function, an Actions column
in a different position, a different button style — **do not guess and do not "make it fit".**

Use `AskUserQuestion` with **2–4 concrete options you have actually researched** in the codebase.
Each option must state what will change and what the consequence is. The user can always type
their own answer via the built-in "Other" field.

> Real example: `modalAccountingJurnal` put its Actions column **first** in 3 of 6 loaders and used
> a `btn-primary` `+` button instead of `btn-success` "Select". Copying the reference helpers
> verbatim would have silently moved that column and restyled the button across 8 pages.

### 4b. Big change ? ask, and let the user state the expectation

**Stop and ask if any of these are true:**

| Trigger | Example |
|---|---|
| **Editing a shared file** | a `modalXxx.blade.php` included by many pages; `report-table.css` / `report-table.js`; a module layout |
| **Missing infrastructure** | Class B page — assets or the `gcart_header` engine are absent |
| **Behaviour change, not just styling** | removing the Actions column so rows become clickable; changing which parameters go to the stored proc; making a gated feature unconditional |
| **Deleting or replacing existing working UI** | dropping a filter field; swapping a control for a different one; removing a button users rely on |

Ask with `AskUserQuestion`, giving researched options **plus** room to type — state the blast
radius as a number wherever you can ("this modal is included by 8 pages"). Get the count from the
pre-flight command in §2, not from memory.

### 4c. Proceed without asking

Pure styling on a single, non-shared page that is Class A and already matches the reference
structure — swapping classes, adding `#rtBar`, adding the hint line. Still report what changed.

---

## 5. Constraints that apply to every job

### 5.1 Two Bootstraps — the close-button rule flips by page class

Class A pages load **both**: Bootstrap 4.0.0 (local, from `newmaster2`) and Bootstrap 5.3.3 (CDN,
from `masterreport2`). BS5 registers its jQuery plugin at `DOMContentLoaded`, i.e. *after* BS4's
synchronous registration — so **BS5 owns `$.fn.modal`** and the `data-bs-*` data-api.

| | Class A (masterreport2) | Class B (other layouts) |
|---|---|---|
| Loaded | BS4 + BS5 | BS4 only |
| Close button | `data-bs-dismiss="modal"` ? | `data-dismiss="modal"` ? |
| `$('#x').modal('show'/'hide')` | BS5 | BS4 |

**The real rule: whatever opens a modal must also close it.** The two libraries keep separate
instance registries — a modal opened by one and closed by the other silently no-ops, because the
foreign library builds a fresh instance whose `_isShown` is `false` and whose `hide()` returns
immediately. That is exactly why a close button can render fine and do nothing.

- Opened by `data-bs-toggle` ? close with `data-bs-dismiss`.
- Opened by `$('#modalFilter').modal('show')` ? close with `$('#modalFilter').modal('hide')` and/or
  the matching data-api for whichever library owns `$.fn.modal`.
- **Never put `data-toggle` and `data-bs-toggle` on the same element** — both data-apis fire and
  you get two instances and a doubled backdrop. (Both *dismiss* attributes together is harmless:
  one hides it, the other no-ops.)

### 5.2 Modals go outside `.tb-report`

`report-table.css` resets `.tb-report * { margin:0; padding:0 }`, which destroys Bootstrap modal
spacing. Place any modal after the closing `.tb-report` div, still inside the section.

### 5.3 Never edit the dated backup copies

The repo keeps manual snapshots beside live code. **Never edit these, and never count them:**

```
resources/views(070526)/   resources/views(180526)/   resources/views(Old)/
resources/views/report1705/   *backup*.blade.php   app/Http/Controllers(070526)/
app/Http/Controllers(180526)/   app/Http/ControllersOld/   Marketing - backup/
```

Per-file copies use `DDMMYY` or tag suffixes (`BankController2410.php`,
`marketing.php(0406)`, `kas2710.blade.php`). Always confirm you're in the file the live route
actually renders. When counting how many pages include something, exclude these paths — including
them inflates the number (e.g. `modalMarketingSO` is 19 live pages, not 33).

### 5.4 Don't read `.val()` on an element that may not exist

jQuery returns `undefined`, which silently corrupts a global and can stop a parameter ever
reaching the stored proc. This really happened with `#modalOrder` in `reportmarketingso`.

```js
if ($('#modalOrder').length) { setOrderBy($('#modalOrder').val()); }
```

### 5.5 One picker modal per page

Two picker blades on one page = duplicate `#formSelect` id + identical function names, last
include wins. See [new-cust-supp-modal-guide.md](new-cust-supp-modal-guide.md) §5.

### 5.6 Changing table columns does not reach existing users

If you add, remove or rename anything in a page's `setDefaultHeader()`, **users who have opened
that page before will not see the change.** `doSetHeader()` prefers the saved layout in
`DBSIMPANHEADER`, which is written automatically on their first visit.

The only way to pick it up is the **"Reset kolom"** button in the table bar (or the
"Reset ke default" link in the Atur Kolom modal), which forces `setDefaultHeader()` to re-run.
It is per user **and** per report mode.

So whenever you touch `setDefaultHeader()`:

- say so explicitly in your report — the change is invisible until reset;
- confirm the page's bar actually shows **Reset kolom**;
- never "fix" it by making `doSetHeader()` always reload defaults — that wipes every user's
  deliberately customised layout on every page load.

Detail: [new-slider-table-guide.md](new-slider-table-guide.md) §2.

### 5.7 The "Atur Kolom" modal is being retired — but not yet

The plan is to drop the Customize Table modal now that the bar's gear menu and Reset button cover
it. **Do not comment out its markup yet:** ~90 live pages still show an active *Customize Table*
button and most of them do not use `ReportTable` at all, so that modal is still their only
column-config UI.

Its JavaScript must stay regardless of when the markup goes. `doShowCustomize()` is called by
`doButtonVisibility` / `doSetDesimal` / `doButtonTotal` / `doMoveHeader` — exactly the functions
the gear menu delegates to. Only the **markup** is ever safe to comment out, and only once the
remaining pages are migrated. Retiring it is a shared-file + behaviour change ? ask first (§4b).

### 5.8 Match the surrounding code

Indentation, Indonesian comment style, and the existing idiom of the file. These pages are raw
SQL + jQuery; don't introduce a new framework or pattern to make one change fit.

---

## 6. Verification

There is no automated test coverage (only stub PHPUnit examples), so verification is manual plus
static checks.

**Static — always:**

```bash
# no leftover old markup
grep -n "btn-select\|input-group-append\|form-select\|<th>Actions</th>" <file>

# JS syntax of the edited block (Blade tags stubbed out)
node -e "const fs=require('fs');const s=fs.readFileSync('<file>','utf8');
const m=s.match(/<script[^>]*>([\s\S]*?)<\/script>/);
let js=m[1].replace(/\{!![\s\S]*?!!\}/g,'STUB').replace(/\{\{[\s\S]*?\}\}/g,'STUB');
try{new Function(js);console.log('JS OK')}catch(e){console.log('ERR '+e.message)}"
```

**In the browser** — both assets carry a `?v=filemtime` cache-buster, so a normal reload picks up
changes; hard-reload if in doubt.

- Interactive table: drag a heading, hide a column, restore it from the bar, change decimals,
  toggle total — **then reload and confirm all of it survived**; check "Atur Kolom" agrees.
- Filter modal: open, change a filter, watch the badge, pick an entity, clear the tag ×,
  "Reset semua", confirm Terapkan closes it.
- Picker: open each entity, click a row, confirm the right field is filled and the modal closes.
- If a shared file was edited, load **one page you were not targeting** and confirm it's unchanged.

**Report honestly.** List every file changed. If something was verified statically but not clicked
through, say so.

---

## 7. Quick index of the moving parts

| Thing | Where |
|---|---|
| `.tb-report` table/toolbar styling, all `rt-*` classes, picker + filter skins | `public/css/report-table.css` |
| `window.ReportTable`, `loadingHtml`, `fmtRp`/`fmtN`, voucher drill | `public/js/report-table.js` |
| "Atur Kolom" modal styling | `public/css/customize-table.css` |
| `gcart_header` engine, "Atur Kolom" + "Filter Data" modals | `resources/views/report/masterreport2.blade.php` |
| CSS/JS `<link>`/`<script>` tags, `format_date` / `format_number` / `currencyNormalizer` / `nullToEmpty` | `resources/views/report/newmaster2.blade.php` |
| Reference page — interactive table + "Tampilan" that re-queries | `report/reportaccountingkasharian.blade.php` |
| Reference page — filter modal + combo pickers + badge | `report/reportmarketingso.blade.php` |
| Reference page — both, most recently updated | `report/reportmarketinghistoryoutso.blade.php` |
| Superseded archive | `docs/report-table-guide.md`, `docs/report-table-upgrade-steps.md` |
| History / rationale | `docs/handoff-report-ui-redesign.md` |
