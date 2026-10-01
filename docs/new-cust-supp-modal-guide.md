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
| `modalMarketingSO.blade.php` | 19 pages | **gated** â€” needs `window.g_pickerV2 = true` |
| `modalAccountingJurnal.blade.php` | 8 pages | **ungated** â€” always click-row |
| `modalMarketingHistorySO.blade.php` | 2 pages | **ungated** â€” always click-row |
| `modalMarketingInvoice.blade.php` | 1 page | **gated** |
| `modalMarketingRegUangMuka.blade.php` | 1 page | **gated** |
| `modalStock.blade.php` | **0 pages** | dead code â€” don't bother |

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

Leave the `DataTable({...})` init alone â€” its search box, info line and pagination are skinned by
CSS, not replaced.

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
| Header/cell order mismatch | pre-existing in some loaders (e.g. Merk prints header `Nama, Kode Merk` but cells `KodeMerk, NamaMerk`). Preserve as-is unless asked; changing it affects every including page |

---

## 7. Checklist

- [ ] Read the target modal's existing markup **before** copying helpers from another modal
- [ ] Decided gated vs ungated â€” and if ungated, confirmed the blast radius with the user
- [ ] `pickerHeadHtml()` / `pickerRowHtml()` added, fallback branch matches the old markup byte-for-byte
- [ ] Every `loadSelectXxx()` refactored; `idPart` + value unchanged
- [ ] `buttonSelect()` applies `rt-picker-v2` (unconditionally if ungated)
- [ ] Gated: the page sets `window.g_pickerV2 = true`
- [ ] DataTables init untouched
- [ ] Page includes exactly **one** picker modal
- [ ] Tested: open each entity, click a row, confirm the right field is filled and the modal closes
