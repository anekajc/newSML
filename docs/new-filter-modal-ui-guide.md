# New Design — Filter Modal UI Guide

Sub-guide of **[new-design-all-report-guide.md](new-design-all-report-guide.md)** — read that first. It holds the
pre-flight checks, the ask-first protocol, and the cross-cutting constraints. This file only covers
the "Filter Laporan" modal.

**What this produces:** a redesigned filter dialog with grouped sections, styled native selects,
click-to-open picker boxes that show a removable tag, an active-filter count badge, and a
Reset / Batal / Terapkan footer.

Reference implementations: `resources/views/report/reportmarketingso.blade.php`,
`resources/views/report/reportmarketinghistoryoutso.blade.php`.

---

## 1. Prerequisites

The skin activates purely from the `rt-filter` marker class, and all of it lives in
`public/css/report-table.css`. So the only requirement is that the page loads that stylesheet —
true for anything extending `report.masterreport2`. Any other layout: see the all-guide's
pre-flight section first.

> ⚠️ **Placement rule.** The modal must sit **outside** `<div class="tb-report">`.
> `report-table.css` resets `.tb-report * { margin:0; padding:0 }`, which destroys Bootstrap's
> modal spacing. Put it after the closing `.tb-report` div, still inside `@section('header2')`.

---

## 2. Class reference

| Class | Use on |
|---|---|
| `rt-filter` | the `.modal` element — switches the whole skin on |
| `rt-section` | one titled block of fields |
| `rt-group-label` / `rt-group-hint` | that block's heading and its muted suffix |
| `rt-grid-1` / `rt-grid-2` | one- or two-column field row (2 collapses to 1 under 520px) |
| `rt-field-label` | small uppercase label above a control |
| `rt-native` | on a `<select>` — styled dropdown with custom chevron |
| `rt-combo` + `rt-combo-input` | the click-to-open picker box |
| `rt-combo-tag` / `rt-combo-placeholder` / `rt-combo-chevron` | its filled / empty / arrow states |
| `rt-active-badge` | the "N aktif" pill in the title |
| `rt-reset-link` | the "Reset semua" text button (footer left) |
| `rt-btn` + `rt-btn-ghost` / `rt-btn-primary` | footer buttons |
| `rt-footer-buttons` | wrapper that right-aligns them |

---

## 3. Markup

```blade
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i> Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        {{-- Atribut dismiss: lihat aturan Bootstrap di new-design-all-report-guide.md --}}
        <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="rt-section">
          <div class="rt-group-label">Pengaturan Laporan</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="modalReport">Report</label>
              <select class="rt-native" id="modalReport">
                <option value="0">Detail</option>
                <option value="1">Rekap</option>
              </select>
            </div>
            <div>
              <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="modalOtorisasi">
                <option value="2">Semua</option>
                <option value="0">Belum</option>
                <option value="1">Sudah</option>
              </select>
            </div>
          </div>
        </div>

        <div class="rt-section">
          <div class="rt-group-label">Filter Data
            <span class="rt-group-hint">&mdash; klik untuk memilih</span>
          </div>
          <div class="rt-grid-2" id="pickFields"></div>

          {{-- Nilai sebenarnya (dibaca makeTable() & ditulis buttonPilih()) --}}
          <input type="hidden" id="inputCustomer" value="-">
          <input type="hidden" id="inputLokasi" value="-">
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="applyModalFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
```

The trigger button in the toolbar — see the all-guide for **why** it uses jQuery rather than
`data-bs-toggle`:

```blade
<button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
    <i class="fas fa-filter"></i> Filter
</button>
```

---

## 4. The `.rt-combo` picker fields

Each combo is only a *display* over a hidden input. The hidden input holds the real value; `-`
means "not set". `makeTable()` reads the hidden input, and the picker modal's `buttonPilih()`
writes to it — neither knows the combo exists.

```js
const PICK_FIELDS = [
    { id: 'inputCustomer', label: 'Cust/Supp', modal: 'selectCustomer' },
    { id: 'inputLokasi',   label: 'Lokasi',    modal: 'selectLokasi' },
];

function renderPickFields() {
    let html = '';
    PICK_FIELDS.forEach(function (f) {
        const val = $('#' + f.id).val() || '-';
        const isSet = (val !== '-' && val !== '');
        html += '<div>';
        html += '<label class="rt-field-label">' + f.label + '</label>';
        html += '<div class="rt-combo">';
        html += '<div class="rt-combo-input" onclick="pickFromModal(\'' + f.modal + '\')">';
        if (isSet) {
            html += '<span class="rt-combo-tag">' + val +
                '<button type="button" onclick="event.stopPropagation(); clearPickField(\'' + f.id +
                '\')">&times;</button></span>';
        } else {
            html += '<span class="rt-combo-placeholder">Pilih ' + f.label.toLowerCase() + '...</span>';
        }
        html += '<span class="rt-combo-chevron">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>' +
            '</span>';
        html += '</div></div></div>';
    });
    $('#pickFields').html(html);
}

function clearPickField(id) {
    $('#' + id).val('-');
    renderPickFields();
    updateFilterBadge();
}
```

> `event.stopPropagation()` on the tag's × is required — without it, clearing the field also
> triggers the parent's `onclick` and immediately reopens the picker.

---

## 5. The badge count

**Rule — decide per select, not by a fixed list:**

- A select **with a genuine neutral option** ("Semua", or the Detail default) **counts** when it's
  changed away from that neutral value.
- A select that is a **forced choice with no neutral option counts never** — it's a required
  report-mode switch, not a filter being turned on.

Worked examples in the codebase:

| Page | Select | Counts? | Why |
|---|---|---|---|
| `reportmarketingso` | Report `0=Detail` | ✅ when ≠ `'0'` | `0` is the neutral default |
| `reportmarketingso` | Otorisasi `2=Semua` | ✅ when ≠ `'2'` | has "Semua" |
| `reportmarketinghistoryoutso` | Outstanding `1/0` | ❌ never | no neutral — must pick one |
| `reportmarketinglaporanoutso` | Order By `N/B/C/…` | ❌ never | no neutral — must pick one |

```js
function updateFilterBadge() {
    let count = 0;
    PICK_FIELDS.forEach(function (f) {
        const val = $('#' + f.id).val();
        if (val && val !== '-') { count++; }
    });
    if ($('#modalOtorisasi').val() !== '2') { count++; }   // has a neutral -> counts
    // Outstanding / Order By: forced choice -> intentionally NOT counted
    $('#filterBadge').text(count + ' aktif');
}

function resetAllFilters() {
    $('#modalOtorisasi').val('2');
    PICK_FIELDS.forEach(function (f) { $('#' + f.id).val('-'); });
    renderPickFields();
    updateFilterBadge();
}
```

---

## 6. Wiring

```js
// repaint on open so the boxes reflect current values
$('#modalFilter').on('show.bs.modal', function () {
    $('#modalOtorisasi').val(globalOtorisasi);
    renderPickFields();
    updateFilterBadge();
});

// live badge update when a select changes
$('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

function applyModalFilter() {
    setOtorisasi($('#modalOtorisasi').val());
    $('#modalFilter').modal('hide');
}
```

### Opening a picker from inside the Filter modal

Bootstrap does not stack modals cleanly, so hide the Filter modal first and reopen it when the
picker closes:

```js
let g_reopenFilter = false;

function pickFromModal(idModal) {
    g_reopenFilter = true;
    $('#modalFilter').modal('hide');
    buttonSelect(idModal);              // opens #formSelect
}

$(document).on('hidden.bs.modal', '#formSelect', function () {
    if (g_reopenFilter) {
        g_reopenFilter = false;
        $('#modalFilter').modal('show');
        // show.bs.modal also calls these, but call again here in case it doesn't
        // re-fire while the modal is still transitioning in.
        renderPickFields();
        updateFilterBadge();
    }
});
```

> ⚠️ **This automatic reopen also fires `show.bs.modal`** — the same event the repaint in §6 binds
> to. If that handler resyncs any `select.rt-native` from a committed/applied global (as in §6's
> `$('#modalOtorisasi').val(globalOtorisasi)`, or a page-specific "pending until Terapkan" field
> such as an account/Perkiraan select), that resync fires on the automatic reopen too — **silently
> discarding whatever the user just changed in that select before opening the picker.**
>
> Concretely: user picks a new Perkiraan (pending, not yet Terapkan) → opens the Supp Awal picker
> → picks a supplier → the Filter modal reopens → `show.bs.modal` fires → Perkiraan snaps back to
> whatever was last Terapkan'd. The Perkiraan choice is gone and nothing looked wrong on screen in
> between. This really happened in `reportaccountinghutangkartu.blade.php`, where Perkiraan/Urut/
> mode selects are pending-until-Terapkan (unlike the PICK_FIELDS combos, which commit to their
> hidden input immediately on pick and so aren't affected).
>
> **Fix — only if the page has a pending-until-Terapkan select:** track that the reopen is
> automatic, and skip the resync-from-committed-globals step only on that path. Still re-render the
> pick fields/badge either way — those reflect live DOM state, not the pending selects.
>
> ```js
> let g_reopeningFilter = false;   // true only while the modal is being reopened after a picker
>
> $('#modalFilter').on('show.bs.modal', function () {
>     if (!g_reopeningFilter) {
>         $('#modalOtorisasi').val(globalOtorisasi);   // ...and any other pending-until-Terapkan select
>     }
>     g_reopeningFilter = false;
>     renderPickFields();
>     updateFilterBadge();
> });
>
> $(document).on('hidden.bs.modal', '#formSelect', function () {
>     if (g_reopenFilter) {
>         g_reopenFilter = false;
>         g_reopeningFilter = true;
>         $('#modalFilter').modal('show');
>         renderPickFields();
>         updateFilterBadge();
>     }
> });
> ```
>
> A page with only PICK_FIELDS-style combos (immediate commit on pick, like the original
> `buttonPilih()`) doesn't need this — the bug only bites selects the page deliberately holds
> pending until "Terapkan".

---

## 7. Gotchas

| Symptom | Cause / fix |
|---|---|
| Modal padding looks crushed | Modal is inside `.tb-report` — move it out |
| Skin doesn't apply at all | `rt-filter` class missing from the `.modal` element |
| Clicking × on a tag reopens the picker | missing `event.stopPropagation()` |
| Combo boxes are empty after picking | `renderPickFields()` not called on `hidden.bs.modal` of `#formSelect` |
| Two modals stacked / backdrop stuck | `pickFromModal()` didn't hide the Filter modal first — check the open/close API matches (all-guide) |
| Badge never updates when a select changes | missing the `change` delegate on `select.rt-native` |
| A filter silently stops being sent to the SP | **Reading `.val()` on an element that doesn't exist returns `undefined` and corrupts the global.** This really happened with `#modalOrder` in `reportmarketingso.blade.php` when the Order By block was commented out. Guard it: `if ($('#modalOrder').length) { setOrderBy($('#modalOrder').val()); }` |
| A pending select (e.g. Perkiraan/account) snaps back to its old value after picking an entity | `show.bs.modal` resynced it from a committed global on every open, including the automatic reopen after a picker closes. Guard the resync with a `g_reopeningFilter` flag — see "Opening a picker from inside the Filter modal" above. This really happened in `reportaccountinghutangkartu.blade.php`. |

---

## 8. Checklist

- [ ] Modal is **outside** `.tb-report`
- [ ] `rt-filter` on the `.modal`, `rt-active-badge` in the title
- [ ] Selects use `rt-native` inside `rt-section` / `rt-grid-*`
- [ ] Picker fields replaced by `#pickFields` + hidden inputs
- [ ] `PICK_FIELDS`, `renderPickFields`, `clearPickField`, `updateFilterBadge`, `resetAllFilters`
- [ ] `show.bs.modal` repaint + `change` delegate on `select.rt-native`
- [ ] Badge counts only selects that have a neutral option
- [ ] Footer: Reset semua + Batal + Terapkan
- [ ] Every `$('#x').val()` read is guarded if `#x` might not exist
- [ ] If the page has a pending-until-Terapkan select (not just PICK_FIELDS combos), the
      `show.bs.modal` resync is guarded with `g_reopeningFilter` so it survives opening a picker
- [ ] Tested: open, change filters, badge count, pick an entity, tag ×, Reset semua, Terapkan closes
- [ ] Tested: change a pending select (e.g. Perkiraan), then pick an entity from a picker opened
      inside the modal — confirm the pending select's value is still there when the modal reopens
