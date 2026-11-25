# New Design â€” Entity Picker Modal Guide (Cust/Supp and friends)

Sub-guide of **[new-design-all-report-guide.md](new-design-all-report-guide.md)** â€” read that first. It holds the
pre-flight checks, the ask-first protocol, and the cross-cutting constraints. This file only covers
the `#formSelect` picker modal.

Despite the filename, this applies to **every entity** the picker is used for â€” Customer,
Supplier, Lokasi/Kebun, PIC, Kategori, Sub-Kategori, Merk, Perkiraan, Divisi, Sales, Group.
Cust/Supp is just the most common case.

**What this produces:** a picker where the whole row is clickable â€” **no Actions column, no green
Select button** â€” plus a modern modal shell and skinned DataTables controls.

---

## 1. How the picker is structured

Every picker modal blade follows the same shape:

| Element | id | Role |
|---|---|---|
| `.modal` | `formSelect` | the dialog; also carries the `rt-picker-v2` skin class |
| `<h5 class="modal-title">` | `exampleModalLabel` | title, set per entity ("Select Customer") |
| `<thead>` | `tabelHeader` | filled by `pickerHeadHtml()` |
| `<tbody>` | `tabel_dataSelect` | filled by `pickerRowHtml()` |
| `<table>` | `tabelSelect` | DataTables target |

and the same three functions: `buttonSelect(idModal)` (opens + dispatches),
`loadSelectXxx()` (one per entity: AJAX â†’ build header + rows â†’ init DataTables), and
`buttonPilih(idPart, kode)` (writes the chosen value into the page's hidden input, then closes).

> The CSS is keyed on `#formSelect.rt-picker-v2`. A modal using a different id will not be
> skinned â€” either rename the id, or add that id to the selector group in
> `public/css/report-table.css`.

---

## 2. Current state of the picker modals

**Live include counts** (excluding the dated backup folders â€” see the all-guide):

| Modal blade | Included by | Picker style |
|---|---|---|
| `modalMarketingSO.blade.php` | 9 report pages | **ungated** â€” always click-row |
| `modalAccountingJurnal.blade.php` | 4 report pages (biaya, jurnalcomputer, neracalajur, trialbalance) | **ungated** â€” always click-row |
| `modalMarketingHistorySO.blade.php` | 2 pages | **ungated** â€” always click-row |
| `modalMarketingInvoice.blade.php` | 1 page | **ungated** â€” always click-row |
| `modalMarketingRegUangMuka.blade.php` | **not included anywhere** | gated (unchanged, not in use) |
| `modalStock.blade.php` | **0 pages** | dead code â€” don't bother |

> All four live shared modals (Jurnal, HistorySO, SO, Invoice) are now ungated. On report pages
> `window.g_pickerV2` no longer has any effect; report pickers are initialised with `rtPickerInit()`
> (see section 4b).

### Gated vs ungated

**Gated** â€” everything sits behind `window.g_pickerV2`. With the flag falsy the markup is
identical to the old version, so pages that don't opt in are untouched. Use when the modal is
shared and only some pages should get the new look.

**Ungated** â€” the Actions column and its button are deleted outright, every row is clickable, and
`buttonSelect()` calls `addClass('rt-picker-v2')` instead of `toggleClass(...)`. Every page
including that modal changes at once.

> Going ungated **requires** adding the class unconditionally. `cursor: pointer` and the hover
> highlight live inside the `#formSelect.rt-picker-v2 â€¦` selectors â€” without the class you get
> rows that react to clicks with no visual affordance at all.

Switching a shared modal from gated to ungated is a **behaviour change affecting every including
page** â†’ stop and ask (see the all-guide).

---

## 3. Turning it on for a page whose modal is already gated

One line, before anything opens a picker:

```js
window.g_pickerV2 = true;     // e.g. top of @section('jsreport')
```

The flag is **global per page**: it restyles *every* picker on that page.

---

## 4. Converting a modal to the new picker

### Step 1 â€” add the two helpers

**Ungated** (preferred when every including page should change):

```js
// Picker gaya baru: TANPA kolom Actions / tombol Select, baris langsung diklik.
function pickerHeadHtml(cols) {
    return '<tr>' + cols.map(c => '<th>' + c + '</th>').join('') + '</tr>';
}

function pickerRowHtml(idPart, kode, cellsHtml) {
    return '<tr class="pick-row" onclick="buttonPilih(\'' + idPart + '\',\'' + kode + '\')">' +
        cellsHtml + '</tr>';
}
```

**Gated** (when only some pages should change):

```js
function pickerHeadHtml(cols) {
    return '<tr>' + cols.map(c => '<th>' + c + '</th>').join('') +
        (window.g_pickerV2 ? '' : '<th>Actions</th>') + '</tr>';
}

function pickerRowHtml(idPart, kode, cellsHtml) {
    if (window.g_pickerV2) {
        return '<tr class="pick-row" onclick="buttonPilih(\'' + idPart + '\',\'' + kode + '\')">' +
            cellsHtml + '</tr>';
    }
    return '<tr>' + cellsHtml + '<td class="text-center">' +
        '<button class="btn btn-success btn-sm" type="button" onclick="buttonPilih(\'' + idPart +
        '\',\'' + kode + '\')">Select</button></td></tr>';
}
```

> **Match the old markup exactly in the fallback branch.** Not every modal used the same button.
> `modalAccountingJurnal` used `btn-primary` with a `+` label (not `btn-success`/"Select"), and
> put the Actions column **first** in three of its six loaders. A verbatim copy of the SO helpers
> would have silently moved that column and restyled the button for 8 pages. Read the file before
> copying â€” if the old markup varies, either add a parameter for it or go ungated.

### Step 2 â€” refactor each `loadSelectXxx()`

```js
// BEFORE
let headerTable = `<tr><th>Kode</th><th>Nama</th><th>Actions</th></tr>`;
document.getElementById("tabelHeader").innerHTML = headerTable;

let rowTable = "";
dataRefresh.forEach((item, i) => {
    let temp = "";
    rowTable += `<tr>
      <td>${item.Kode}</td>
      <td>${item.Nama}</td>
      <td class="text-center">
        <button class="btn btn-success btn-sm" type="button" onclick="buttonPilih('1','${item.Kode}')">Select</button>
      </td>
    </tr>`;
});

// AFTER
document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode', 'Nama']);

let rowTable = "";
dataRefresh.forEach((item, i) => {
    rowTable += pickerRowHtml('1', item.Kode,
        `<td>${item.Kode}</td><td>${item.Nama}</td>`);
});
```

Keep `idPart` and the value passed to `buttonPilih` **exactly** as they were â€” they map to the
page's hidden inputs. Drop the unused `let temp = "";`.

### Step 3 â€” the class toggle in `buttonSelect()`

```js
function buttonSelect(idModal) {
    $("#formSelect").addClass('rt-picker-v2');                          // ungated
    // $("#formSelect").toggleClass('rt-picker-v2', !!window.g_pickerV2);  // gated
    $("#formSelect").modal('toggle');
    // â€¦ existing dispatch â€¦
}
```

### Step 4 â€” (gated only) opt the page in

```js
window.g_pickerV2 = true;
```

On report pages, initialise the picker table with `rtPickerInit('<tableId>', {page-specific opts})`
instead of `.DataTable({...})` (see section 4b). Outside report pages, leave the `DataTable({...})`
init alone â€” its search box, info line and pagination are skinned by CSS, not replaced.

---

## 4b. `rtPickerInit()`: per-page dropdown + Kas look (report pages)

Report pages get the Kas picker look from a shared helper rather than from their own DataTables
options. `public/js/report-picker.js` defines `rtPickerInit(idTabel, opsi)` and `rtPickerSkin(idTabel)`,
and `public/css/picker-kas.css` supplies the styling. Both are loaded once globally from
`resources/views/report/newmaster2.blade.php` and `newmaster2x.blade.php`, so every report page that
uses those layouts (masterreport2, masterreport2x, masterreport4, Gudang, Neraca) has them.

What `rtPickerInit()` does:

- **Table behaviour** is copied from `kasInitPicker()` in `public/js/kas.js`: `lengthChange: true`,
  a "Tampilkan" dropdown with 10 / 25 / 50 / 100 / Semua, `pageLength: 10`, and Indonesian
  language strings.
- **Layout** uses `dom: 'lfr<"rt-picker-scroll"t><"rt-picker-bawah"ip>'`: only the table body
  scrolls, and the info and pager stay pinned below it.
- **Page options** are passed as the second argument and shallow-merged over the defaults.
  `language` is merged separately, so a page that sets only `language.emptyTable` still keeps the
  "Tampilkan" label.
- **Skin** is applied at init time: it adds `rt-picker-v2 picker-kas` to the closest
  `.modal, .modal-picker`, and `picker-kas-batal` to the footer button whose text is exactly
  "Batal". It runs at init rather than on `show.bs.modal` because pickers init right before they
  open, and the reportstock* `.modal-picker` overlays never fire Bootstrap events.
- **Re-init is safe.** If the table is already a DataTable, the helper destroys it while keeping
  the freshly built rows. A `colspan` placeholder row ("Belum ada data") is removed first, since it
  makes DataTables error.

Fill `<tbody>` first, then call `rtPickerInit()`:

```js
// BEFORE
$("#tabelSelectValas").DataTable({ "lengthChange": false, "paging": true });

// AFTER
rtPickerInit('tabelSelectValas');
```

Pass only the options the page really needs, for example `rtPickerInit('tabelSelect', { order: [] })`.

The shared CSS selectors use `:is(.modal, .modal-picker).picker-kas`, so custom overlays are skinned as
well. Specificity is unchanged.

> **Not used for the multi-select `#formFilterData` / `#tabelfilter` modal** in the masterreport*
> layouts. That one sets `paging: false` on purpose, because selection and drag need every row
> visible.

---

## 5. Hard rule â€” never include two picker modals on one page

Each of these blades renders a **complete** modal with `id="formSelect"` *and* declares the same
function names. Including two on one page breaks the picker two ways:

- **Duplicate DOM id** â€” two `<div id="formSelect">`. `$("#formSelect")` only matches the first,
  so you can toggle one modal while filling the other's table.
- **Silently overwritten functions** â€” both declare `buttonSelect`, `buttonPilih`,
  `loadSelectCustomer`, â€¦ As plain `<script>` declarations the last include wins, so a loader can
  fire the *other* modal's AJAX route and populate a table nobody can see.

This already happened and was fixed by deleting one include â€” the note is still in
`resources/views/report/reportmarketinghistoryoutso.blade.php`:

```blade
{{-- modalMarketingHistorySO sudah menyediakan selectCustomer + selectLokasi (dan PIC/
     Kategori/SubKategori/Merk). @include('report.modalMarketingSO') dihapus: keduanya
     mendefinisikan modal ber-id #formSelect + nama fungsi yang sama (id ganda). --}}
```

**If a page needs pickers that live in two different modals, add the missing `loadSelectXxx()` to
the modal it already includes** â€” don't include the second one. Adding a loader to a shared modal
is itself a shared-file edit â†’ ask first.

---

## 6. Gotchas

| Symptom | Cause / fix |
|---|---|
| Rows clickable but no pointer/hover | `rt-picker-v2` class not applied â€” check `buttonSelect()` |
| Skin doesn't apply | modal id isn't `formSelect`, or CSS not loaded on this layout |
| Column order changed after conversion | old markup had Actions **first** (e.g. `modalAccountingJurnal`) â€” see Step 1 |
| Button restyled unexpectedly | old markup used `btn-primary`/`+`, not `btn-success`/"Select" |
| Wrong field gets filled | `idPart` changed â€” it maps to a specific hidden input in `buttonPilih()` |
| Picker opens but table is empty / wrong data | two picker modals included â†’ duplicate `#formSelect` (Â§5) |
| Modal won't close | open/close going through different Bootstrap versions â€” see the all-guide |
| No Tampilkan dropdown / plain Bootstrap look on a report picker | table initialised with `.DataTable({...})` instead of `rtPickerInit()`, or the page doesn't extend a layout that loads report-picker.js |
| Header/cell order mismatch | pre-existing in some loaders (e.g. Merk prints header `Nama, Kode Merk` but cells `KodeMerk, NamaMerk`). Preserve as-is unless asked; changing it affects every including page |

---

## 7. Checklist

- [ ] Read the target modal's existing markup **before** copying helpers from another modal
- [ ] Decided gated vs ungated â€” and if ungated, confirmed the blast radius with the user
- [ ] `pickerHeadHtml()` / `pickerRowHtml()` added, fallback branch matches the old markup byte-for-byte
- [ ] Every `loadSelectXxx()` refactored; `idPart` + value unchanged
- [ ] `buttonSelect()` applies `rt-picker-v2` (unconditionally if ungated)
- [ ] Gated: the page sets `window.g_pickerV2 = true`
- [ ] Table initialised via `rtPickerInit()` (report pages)
- [ ] Page includes exactly **one** picker modal
- [ ] Tested: open each entity, click a row, confirm the right field is filled and the modal closes
