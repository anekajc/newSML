> ## ⚠️ SUPERSEDED
> Use **[new-design-all-report-guide.md](new-design-all-report-guide.md)** instead — it is the entry point, and
> routes to [new-slider-table-guide.md](new-slider-table-guide.md),
> [new-filter-modal-ui-guide.md](new-filter-modal-ui-guide.md) and
> [new-cust-supp-modal-guide.md](new-cust-supp-modal-guide.md).
>
> This file is kept as an archive. Its content is still broadly correct, but it is report-module
> specific, its picker include counts are inflated (they counted dated backup folders), and it
> predates the ask-first protocol. Don't edit it; edit the new guides.

# Report Table Guide — interactive header, Filter modal, and entity picker

How to give any `report*` page the same behaviour as **Kas Harian**
(`reportaccountingkasharian.blade.php`) and **Marketing SO** (`reportmarketingso.blade.php`):

1. **Interactive table** — drag column headings to reorder, per-column gear menu
   (hide / decimals / show total), and a bar above the table listing hidden columns plus an
   optional "Tampilan" (view) switcher.
2. **Filter modal skin** (`#modalFilter.rt-filter`) — the redesigned "Filter Laporan" dialog.
3. **Entity picker skin** (`#formSelect.rt-picker-v2`) — click-the-row select modal with no
   Actions column.

All three are already loaded globally for every page that extends `report.masterreport2`.
**You do not add any CSS or JS file** — you only write page markup and call the API.

> **Upgrading a page that already has a working table?** Use
> [report-table-upgrade-steps.md](report-table-upgrade-steps.md) instead — it's the short
> do-this-then-that checklist for adding the interactive header to an existing report page.
> This file is the full reference (new pages, the `gcart_header` contract, the modal skins).

> **Naming note.** These used to live in `report-table-v2.{css,js}`. They were merged into
> `public/css/report-table.css` and `public/js/report-table.js`; the v2 files remain on disk as
> an archived copy but are **not loaded**. The JS API is `window.ReportTable` (not
> `ReportTableV2`). The `.rt-picker-v2` class and `window.g_pickerV2` flag deliberately keep
> their "v2" names because `modalMarketingSO.blade.php` is shared by ~35 pages.

---

## 0. What the layout already gives you

| Provided by | You get |
|---|---|
| `report/newmaster2.blade.php` | `report-table.css` (all `.tb-report` styling + the three skins above), `customize-table.css`, Bootstrap Icons (`bi-gear` etc.), and the global helpers `format_date`, `format_number`, `currencyNormalizer`, `nullToEmpty` |
| `report/masterreport2.blade.php` | `report-table.js` (`window.ReportTable`, `loadingHtml`, `fmtRp`, voucher drill), the "Atur Kolom" modal (`#formCustomizeTable`), the "Filter Data" modal (`#formFilterData`), and the header-persistence functions |

Sections a child page fills: `@section('header2')` for markup, `@section('jsreport')` for scripts,
`@section('css')` for page-local styles.

---

## 1. The `gcart_header` contract

Everything — the rendered `<thead>`, the gear menu, the "Atur Kolom" modal, and what gets saved
to `DBSIMPANHEADER` — is driven by one global array. Each column is a 6-element array:

```js
// [ 0: field,  1: label,  2: visible,  3: type,  4: total,  5: decimals ]
['NDPPRPZX', 'DPP IDR',  1, 'float', 1, 2]
```

| Idx | Meaning | Values |
|---|---|---|
| 0 | Field name **exactly as returned by the stored procedure** | e.g. `NoBukti` |
| 1 | Column heading shown to the user | e.g. `No. Bukti` |
| 2 | Visible | `1` = shown, `0` = hidden (appears in the "kolom tersembunyi" bar) |
| 3 | Data type | `varchar` \| `date` \| `float` \| `int` \| `bool` |
| 4 | Include in Subtotal / Grand Total | `1` / `0` — only meaningful for `float`/`int` |
| 5 | Decimal places | `0`–`4` |

Two companion globals control the total rows:

```js
gsum_issubtotal   = 1;   // show Subtotal rows at each group change
gsum_isgrandtotal = 1;   // show one GRAND TOTAL row at the end
```

### Where it comes from

You define `setDefaultHeader()`; the layout's `doSetHeader(g_modeReport)` either loads the
user's saved layout for this page+mode, or (first time) calls your `setDefaultHeader()` and
saves it. **`setDefaultHeader()` must exist** or nothing is seeded.

```js
var modereport_detail = 1, modereport_rekap = 2;
g_modeReport = modereport_detail;          // must be set BEFORE doSetHeader()

function setDefaultHeader() {
  if (g_modeReport == modereport_detail) {
    gcart_header = [
      ['tanggal',     'Tanggal',     1, 'date',    0, 0],
      ['nobukti',     'No Bukti',    1, 'varchar', 0, 0],
      ['keterangan',  'Uraian',      1, 'varchar', 0, 0],
      ['Debet',       'Penerimaan',  1, 'float',   1, 2],
      ['kredit',      'Pengeluaran', 1, 'float',   1, 2],
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 0;
  } else {
    gcart_header = [ /* … columns for the other mode … */ ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 0;
  }
}
```

If your page has only one column layout, ignore `g_modeReport` and just assign one array.

---

## 2. Complete page skeleton

Paste this, rename the placeholders (`PAGE_TITLE`, `ROUTE_NAME`, `EXPORT_PREFIX`), and replace
`setDefaultHeader()` with your columns.

```blade
@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php). --}}

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div><div class="page-title">PAGE_TITLE</div></div>

      <div class="filter-wrap">
        <label>Periode</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
        <span class="filter-sep">s/d</span>
        <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
      </div>

      <div class="action-group">
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..."
               oninput="applyFilters()" style="width:180px">
        <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table">
          <i class="fas fa-cog"></i> Customize Table</button>
        <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan">
          <i class="fas fa-check"></i> Tampilkan</button>
        <div class="export-wrap" id="exportWrap">
          <button class="export-btn" onclick="toggleExport()">
            <i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
          <div class="export-drop" id="exportDrop">
            <div class="export-opt" onclick="doExport('Excel')">
              <i class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span></div>
            <div class="export-opt" onclick="doExport('CSV')">
              <i class="bi bi-clipboard"></i> Ekspor ke <span class="ext">CSV</span></div>
            <div class="export-opt" onclick="doExport('Print')">
              <i class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bar kolom tersembunyi + Tampilan (filled by ReportTable) -->
    <div id="rtBar"></div>

    <!-- TABLE: thead is rendered dynamically from gcart_header -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead><tr><th>&nbsp;</th></tr></thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer"><span id="footerLabel">Belum ada data dimuat</span></div>
    </div>

    <div class="rt-hint">
      <i class="bi bi-info-circle"></i>
      Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
      sembunyikan kolom atau atur desimal &amp; total.
    </div>

  </div><!-- /content -->

  <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
</div><!-- /tb-report -->
@endsection


@section('jsreport')
<script type="text/javascript">
  let lastRows = [];                 // last fetch (used by render / search / export)
  let currentGroupby = 'NoBukti';    // field whose change triggers a Subtotal row

  var modereport_detail = 1;
  g_modeReport = modereport_detail;

  const reportUrl = "{{ url('ROUTE_NAME_doReport') }}";

  $(document).ready(function () {
    doSetHeader(g_modeReport);       // load saved layout, or seed from setDefaultHeader()

    ReportTable.init({
      table: '#mainTable',
      bar: '#rtBar',
      onChange: render              // re-render after hide / reorder / decimals / total
    });
  });

  function setDefaultHeader() {
    gcart_header = [
      ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
      ['TANGGAL', 'Tanggal',   1, 'date',    0, 0],
      ['NNETRPZX','Total IDR', 1, 'float',   1, 2],
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  /* ── LOAD ── */
  function makeTable(_mode) {
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }
    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    $.ajax({
      url: reportUrl, type: 'get',
      data: { date1: $('#inputDate1').val(), date2: $('#inputDate2').val() },
      success: function (res) {
        lastRows = res || [];
        $('#searchBox2').val('');
        render();
      },
      error: function () { lastRows = []; render(); }
    });
  }

  /* ── RENDER ──
     Columns are built dynamically from gcart_header (visible only, in saved order),
     so hiding/reordering/decimals from either the gear menu or "Customize Table"
     shows up immediately. */
  function render() {
    const cols  = gcart_header.filter(c => c[2] === 1);          // MUST be .filter on the original
    const keys  = cols.filter(c => (c[3] === 'float' || c[3] === 'int') && c[4] === 1).map(c => c[0]);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const showSub   = keys.length > 0 && (gsum_issubtotal === 1);
    const showGrand = keys.length > 0 && (gsum_isgrandtotal === 1);

    // interactive header (drag + gear); also refreshes the #rtBar
    thead.innerHTML = ReportTable.headHtml(cols);

    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    const rows = !term ? (lastRows || [])
                       : (lastRows || []).filter(r => rowSearchText(r, cols).indexOf(term) !== -1);

    if (!rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length +
                        '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = {}, grand = {};
    keys.forEach(k => { sub[k] = 0; grand[k] = 0; });

    rows.forEach(function (r, i) {
      const now = pickCI(r, currentGroupby);

      if (showSub && i !== 0 && prev !== now) {
        html += totalRow('Subtotal', sub, cols, keys, 'subtotal-row');
        keys.forEach(k => sub[k] = 0);
      }

      keys.forEach(function (k) {
        const v = currencyNormalizer(pickCI(r, k));
        sub[k] += v; grand[k] += v;
      });

      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(pickCI(r, key)) + '</td>';
        if (type === 'float' || type === 'int')
          return '<td class="num">' + format_number(currencyNormalizer(pickCI(r, key)), c[5]) + '</td>';
        return '<td>' + nullToEmpty(pickCI(r, key)) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    if (showSub)   html += totalRow('Subtotal', sub, cols, keys, 'subtotal-row');
    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, keys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Total row: value in every totalled numeric column, label in the first non-totalled one.
  function totalRow(label, sums, cols, keys, cls) {
    const labelIdx = cols.findIndex(c => keys.indexOf(c[0]) === -1);
    const tds = cols.map(function (c, idx) {
      if (keys.indexOf(c[0]) !== -1) return '<td class="num">' + format_number(sums[c[0]], c[5]) + '</td>';
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });
    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  // Stored procs mix casing (Debet / kredit / Nobukti) — read fields case-insensitively.
  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

  function rowSearchText(r, cols) {
    return cols.map(function (c) {
      const v = pickCI(r, c[0]);
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  function applyFilters() { if (lastRows.length) { render(); } }

  /* ── EXPORT ── */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); return; }
    const cols = gcart_header.filter(c => c[2] === 1);
    const body = (lastRows || []).map(r => cols.map(function (c) {
      const v = pickCI(r, c[0]);
      if (c[3] === 'date') return format_date(v);
      if (c[3] === 'float' || c[3] === 'int') return currencyNormalizer(v);
      return (v == null ? '' : v);
    }));
    const rows = [cols.map(c => c[1])].concat(body);
    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'EXPORT_PREFIX_' + $('#inputDate1').val() + '_' + $('#inputDate2').val() +
                 '.' + (fmt === 'Excel' ? 'xls' : 'csv');
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    showToast('📄', 'Data diekspor sebagai ' + fmt);
  }

  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  // Columns offered by the layout's "Filter Data" modal. Values MUST match gcart_header[i][0].
  function getKolomFilter() { return ['NoBukti', 'TANGGAL']; }
</script>
@endsection
```

---

## 3. `ReportTable` API

### `ReportTable.init(options)` — call once, in `$(document).ready`

| Option | Required | Meaning |
|---|---|---|
| `table` | yes | Selector of the `<table class="tb">`, e.g. `'#mainTable'` |
| `bar` | yes | Selector of the empty `<div>` above the table, e.g. `'#rtBar'` |
| `onChange` | yes | Your render function. Called after hide / show / reorder / decimals / total |
| `views` | no | Adds a "Tampilan" dropdown to the bar (see below) |

### `ReportTable.headHtml(cols)` — call inside your render

```js
thead.innerHTML = ReportTable.headHtml(cols);
```

Returns the full `<tr>` of `<th class="rt-th">` cells with drag handles and gear buttons, and
refreshes the `#rtBar` as a side effect.

> **Critical:** `cols` must be `gcart_header.filter(...)` — the array elements must be the *same
> object references* as in `gcart_header`, because `headHtml` uses `indexOf()` to map each column
> back to its global index. A `.map()` copy silently breaks the gear menu and drag-reorder.

### Optional `views` — the "Tampilan" switcher

Use when the page already has a Detail/Rekap (or similar) mode. It mirrors an existing control,
it does not create new state:

```js
views: {
  label: 'Tampilan',
  options: [
    { value: '1', label: 'Rp',    desc: 'Detail rupiah' },
    { value: '2', label: 'Valas', desc: 'Rekap valas' }
  ],
  get: function () { return globalReportMode; },       // current value, as a string
  set: function (v) {
    setReportMode(String(v));                          // your own mode setter
    if (lastRows.length) { makeTable('REPORT'); }      // re-query if columns AND data differ…
    // …or just render() if only the column layout changes
  }
}
```

- **Kas Harian** re-queries in `set` (Rp vs Valas returns different fields).
- **Marketing SO** only calls `render()` (Detail vs Rekap is a column-layout change) and also
  syncs the matching `<select>` in its Filter modal: `$('#modalReport').val(String(v));`.

### Other methods

| Method | Use |
|---|---|
| `ReportTable.refresh()` | Redraw just the bar (rarely needed — `headHtml` already does it) |
| `ReportTable.close()` | Force-close the gear menu and bar dropdowns |

### How changes persist

The gear menu and bar delegate to the layout's functions, so the interactive header, the "Atur
Kolom" modal, and the saved layout never diverge:

| User action | Calls | Result |
|---|---|---|
| Hide column / show from bar | `doButtonVisibility(i)` | flips `gcart_header[i][2]`, saves |
| Drag a heading | `doMoveHeader(from, to)` | reorders `gcart_header`, saves |
| Decimal `+` / `−` | `doSetDesimal(i, ±1)` | adjusts `[5]` (clamped 0–4), saves |
| "Tampilkan total" toggle | `doButtonTotal(i)` | flips `[4]`, saves |

Each of those calls `doSimpanHeader(...)` → `globalfunctions_doSimpanHeader` → `DBSIMPANHEADER`,
keyed by page href **and** `g_modeReport`. So each report mode remembers its own layout.

---

## 4. Filter modal (`#modalFilter.rt-filter`)

Optional — only for pages with enough filters to justify a dialog. The skin activates on the
marker class `rt-filter`.

> **Placement rule:** put the modal **outside** `<div class="tb-report">`. `report-table.css`
> resets `.tb-report * { margin:0; padding:0 }`, which destroys Bootstrap modal spacing.
> Keep it after `@endsection`'s closing `.tb-report` div, still inside `@section('header2')`.

### Trigger button (in the toolbar)

```blade
<button class="btn-load" data-bs-toggle="modal" data-bs-target="#modalFilter">
  <i class="fas fa-filter"></i> Filter
</button>
```

### Markup

```blade
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i> Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

          {{-- real values; read by makeTable(), written by buttonPilih() in the picker modal --}}
          <input type="hidden" id="inputCustomer" value="-">
          <input type="hidden" id="inputGroup" value="-">
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

### Class reference

| Class | Use on |
|---|---|
| `rt-filter` | the `.modal` element — turns the whole skin on |
| `rt-section` / `rt-group-label` / `rt-group-hint` | a titled block of fields |
| `rt-grid-2` | two-column field row (collapses to 1 column under 520px) |
| `rt-field-label` | small uppercase label above a control |
| `rt-native` | on a `<select>` — styled dropdown with custom chevron |
| `rt-combo` / `rt-combo-input` / `rt-combo-tag` / `rt-combo-placeholder` / `rt-combo-chevron` | the click-to-open picker box |
| `rt-active-badge` | the "N aktif" pill in the title |
| `rt-reset-link`, `rt-btn`, `rt-btn-ghost`, `rt-btn-primary` | footer controls |

### The `.rt-combo` fields

Each combo is only a *display* over a hidden input; the hidden input holds the real value
(`-` means "not set").

```js
const PICK_FIELDS = [
  { id: 'inputCustomer', label: 'Customer', modal: 'selectCustomer' },
  { id: 'inputGroup',    label: 'Group',    modal: 'selectGroup' },
];

function renderPickFields() {
  let html = '';
  PICK_FIELDS.forEach(function (f) {
    const val = $('#' + f.id).val() || '-';
    const isSet = (val !== '-' && val !== '');
    html += '<div><label class="rt-field-label">' + f.label + '</label>' +
            '<div class="rt-combo"><div class="rt-combo-input" onclick="pickFromModal(\'' + f.modal + '\')">';
    html += isSet
      ? '<span class="rt-combo-tag">' + val +
        '<button type="button" onclick="event.stopPropagation(); clearPickField(\'' + f.id + '\')">&times;</button></span>'
      : '<span class="rt-combo-placeholder">Pilih ' + f.label.toLowerCase() + '...</span>';
    html += '<span class="rt-combo-chevron"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" ' +
            'stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></span>';
    html += '</div></div></div>';
  });
  $('#pickFields').html(html);
}

function clearPickField(id) { $('#' + id).val('-'); renderPickFields(); updateFilterBadge(); }

function updateFilterBadge() {
  let count = 0;
  PICK_FIELDS.forEach(f => { const v = $('#' + f.id).val(); if (v && v !== '-') count++; });
  if ($('#modalOtorisasi').val() !== '2') count++;
  if ($('#modalReport').val()    !== '0') count++;
  $('#filterBadge').text(count + ' aktif');
}

function resetAllFilters() {
  $('#modalReport').val('0'); $('#modalOtorisasi').val('2');
  PICK_FIELDS.forEach(f => $('#' + f.id).val('-'));
  renderPickFields(); updateFilterBadge();
}

// repaint on open so the boxes reflect current values
$('#modalFilter').on('show.bs.modal', function () {
  $('#modalReport').val(globalReportMode);
  $('#modalOtorisasi').val(globalOtorisasi);
  renderPickFields(); updateFilterBadge();
});
$('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

function applyModalFilter() {
  setReportMode($('#modalReport').val());
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
  buttonSelect(idModal);                 // opens #formSelect
}

$(document).on('hidden.bs.modal', '#formSelect', function () {
  if (g_reopenFilter) {
    g_reopenFilter = false;
    $('#modalFilter').modal('show');
    renderPickFields();                  // repaint even if show.bs.modal doesn't re-fire
    updateFilterBadge();
  }
});
```

---

## 5. Entity picker (`#formSelect.rt-picker-v2`)

The picker skin turns the select dialog into clickable rows: **no Actions column, no green
Select button** — clicking anywhere on a row picks it.

### 5a. If your page already includes `modalMarketingSO.blade.php`

One line, before anything opens a picker:

```js
window.g_pickerV2 = true;     // e.g. at the top of @section('jsreport')
```

`modalMarketingSO` reads the flag in `pickerHeadHtml()` / `pickerRowHtml()` and in
`buttonSelect()`, which does `$("#formSelect").toggleClass('rt-picker-v2', !!window.g_pickerV2)`.
When the flag is falsy the markup is byte-identical to the old version — that is what keeps the
~34 other pages including this same modal unchanged.

> The flag is **per page, global**: turning it on restyles *every* picker on that page.

### 5b. Porting the skin to another select modal

The other select modals are structurally identical to `modalMarketingSO`: same ids
(`#formSelect`, `#tabelSelect`, `#tabelHeader`, `#tabel_dataSelect`, `#exampleModalLabel`), same
function names (`buttonSelect`, `buttonPilih`, `loadSelectXxx`), same DataTables init, and the
same hardcoded "Actions column + green Select button" markup. Because they all use
`id="formSelect"`, **the CSS needs no change at all** — only the JS below.

#### Blast radius — check before you edit

Each modal blade is shared. Editing one touches every page that includes it:

| Modal blade | `@include`d by | Status |
|---|---|---|
| `modalMarketingSO.blade.php` | 33 pages | ported, **gated** (reference implementation) |
| `modalAccountingJurnal.blade.php` | 27 pages | ported, **ungated** — always click-row (see below) |
| `modalMarketingHistorySO.blade.php` | 4 pages | ported, **gated** |
| `modalMarketingInvoice.blade.php` | 2 pages | ported, **gated** (8 loaders) |
| `modalMarketingRegUangMuka.blade.php` | 2 pages | ported, **gated** |
| `modalStock.blade.php` | **0 pages** | dead code — not included anywhere; don't bother porting it |

#### Two porting styles

**Gated (default, steps 1–4 below).** Everything sits behind `window.g_pickerV2`; with the flag
falsy the emitted markup is unchanged, so pages that don't opt in are untouched. Use this when
the modal is shared and you only want the new look on specific pages.

**Ungated.** No flag at all: the Actions column and its button are deleted outright, every row is
clickable, and `buttonSelect()` does `addClass('rt-picker-v2')` instead of `toggleClass(...)`.
Every page including that modal gets the new picker at once. `modalAccountingJurnal.blade.php`
was deliberately converted this way.

> If you go ungated, you **must** add the class unconditionally too. `cursor: pointer` and the
> hover highlight live inside the `#formSelect.rt-picker-v2 …` selectors — without the class you
> get rows that react to clicks with no visual affordance at all.

Either way, load one page from that modal's set afterwards to confirm.

#### The four steps

**Step 1** — add the two helpers to that modal blade's `<script>` (copy verbatim from
`modalMarketingSO.blade.php`):

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

**Step 2** — refactor each `loadSelectXxx()` to build its header and rows through them:

```js
// before
document.getElementById("tabelHeader").innerHTML =
  '<tr><th>Kode</th><th>Nama</th><th>Actions</th></tr>';
rowTable += '<tr><td>' + item.Kode + '</td><td>' + item.Nama + '</td>' +
            '<td><button class="btn btn-success btn-sm" onclick="buttonPilih(\'1\',\'' +
            item.Kode + '\')">Select</button></td></tr>';

// after
document.getElementById("tabelHeader").innerHTML = pickerHeadHtml(['Kode', 'Nama']);
rowTable += pickerRowHtml('1', item.Kode,
            `<td>${item.Kode}</td><td>${item.Nama}</td>`);
```

**Step 3** — add the class toggle as the first line of that modal's `buttonSelect()`:

```js
function buttonSelect(idModal) {
  $("#formSelect").toggleClass('rt-picker-v2', !!window.g_pickerV2);
  $("#formSelect").modal('toggle');
  // … existing dispatch …
}
```

**Step 4** — on the page that wants the new look: `window.g_pickerV2 = true;`

Because every branch is behind the flag, pages that don't set it keep the old picker exactly.

> If a modal uses an id other than `formSelect`, the CSS won't match. Either rename the id, or
> add that id to the `#formSelect.rt-picker-v2` selector group in `public/css/report-table.css`.
> DataTables' own controls (search box, info, pagination) are skinned by CSS, so leave the
> `DataTable({...})` init alone.

#### Bootstrap 4 vs 5 markup — harmless, but don't be surprised

`modalMarketingSO` is the only one written in Bootstrap 5 style (`data-bs-dismiss`,
`class="btn-close"`). The others still use Bootstrap 4 attributes (`data-dismiss`,
`class="close"` with a `<span>&times;</span>`); `modalAccountingJurnal` contains a mix of both.

This does **not** affect the skin. The picker CSS targets `.modal-content`, `.modal-header`,
`.modal-title`, `.modal-body`, `.modal-footer`, the `#tabelSelect` table and the DataTables
controls — it never styles the close button. So the redesign applies fine; the ✕ simply keeps
its native look on the BS4 modals. Any dismiss-button quirk there is pre-existing and unrelated
to this port — fix it separately if you care, don't fold it into the skin change.

#### Never include two of these modals on one page

Each of these blades renders a **complete** modal with `id="formSelect"` *and* declares the same
function names. Including two on the same page breaks the picker in two ways:

- **Duplicate DOM id** — two `<div id="formSelect">` exist. `$("#formSelect")` only ever matches
  the first, so you can end up toggling one modal while filling the other one's table.
- **Silently overwritten functions** — both declare `buttonSelect`, `buttonPilih`,
  `loadSelectCustomer`, … As plain `<script>` function declarations the last include wins, so a
  loader can fire the *other* modal's AJAX route and populate a table nobody can see.

This already happened once and was fixed by deleting one of the includes — the note is still in
the code at `resources/views/report/reportmarketinghistoryoutso.blade.php`:

```blade
{{-- modalMarketingHistorySO sudah menyediakan selectCustomer + selectLokasi (dan PIC/
     Kategori/SubKategori/Merk). @include('report.modalMarketingSO') dihapus: keduanya
     mendefinisikan modal ber-id #formSelect + nama fungsi yang sama (id ganda). --}}
```

No page does this today. Porting the helpers doesn't make it worse (they'd be identical in both
files), but if a page needs pickers that live in two different modals, **add the missing loader
to the modal it already includes** — don't include the second one.

---

## 6. Gotchas

| Problem | Cause / fix |
|---|---|
| Gear menu does nothing, drag doesn't reorder | `cols` passed to `headHtml()` isn't the original references. Use `gcart_header.filter(...)`, never `.map()` |
| Columns never appear / "Atur Kolom" is empty | `setDefaultHeader()` missing, or `g_modeReport` not set before `doSetHeader()` |
| Modal padding looks crushed | Modal is inside `.tb-report`. Move it outside — `.tb-report * { margin:0; padding:0 }` |
| Bar dropdown hidden behind the table | Keep the bar at `.tb-report .rt-bar` (z-index 45) above the sticky `thead` (20); the toolbar sits at 50 |
| Numbers show as `NaN` | Field name in `gcart_header[i][0]` doesn't match the proc's column, or you skipped `currencyNormalizer()` |
| Blank cells for fields you know exist | Proc casing differs (`Debet` vs `debet`). Read via `pickCI(r, key)` |
| Subtotals never appear | `gsum_issubtotal !== 1`, no column has `[4] === 1`, or `currentGroupby` isn't a real field |
| Reading `$('#someSelect').val()` when the element is commented out | Returns `undefined` and silently corrupts state — guard with `if ($('#x').length)`. This exact bug hit `#modalOrder` in `reportmarketingso.blade.php` |
| Filter Data modal shows no columns | `getKolomFilter()` returns names that don't match any `gcart_header[i][0]` |

---

## 7. Checklist for a new page

- [ ] `@extends('report.masterreport2')`
- [ ] Markup wrapped in `.tb-report` → `.content`, with `.toolbar`, `#rtBar`, `.table-outer` >
      `.table-wrap` > `<table class="tb" id="mainTable">`, `.table-footer`, `.rt-hint`
- [ ] `g_modeReport` assigned, `setDefaultHeader()` defined
- [ ] `doSetHeader(g_modeReport)` on ready, and again in `makeTable()`
- [ ] `ReportTable.init({ table, bar, onChange })` on ready
- [ ] `thead.innerHTML = ReportTable.headHtml(cols)` inside render, `cols` from `.filter()`
- [ ] `getKolomFilter()` returns valid field names
- [ ] Any modal placed **outside** `.tb-report`
- [ ] Customize button wired to `doShowFormCustomizeTable()`
- [ ] Verify: drag reorder, gear hide → column moves to the bar → restore from the bar,
      decimals ±, total toggle, and that all of it survives a page reload

---

## 8. Files to read when in doubt

| File | Why |
|---|---|
| `resources/views/report/reportaccountingkasharian.blade.php` | Reference for `views:` that re-queries, plus a second `<tbody>` footer (saldo/signature) |
| `resources/views/report/reportmarketingso.blade.php` | Reference for the Filter modal, `.rt-combo` fields, picker bridging, badge counting, client-side status filters |
| `public/js/report-table.js` | `window.ReportTable` implementation + voucher drill |
| `public/css/report-table.css` | Every class named in this guide |
| `resources/views/report/masterreport2.blade.php` | `doSetHeader`, `doSimpanHeader`, `doButtonVisibility`, `doSetDesimal`, `doButtonTotal`, `doMoveHeader`, "Atur Kolom" and "Filter Data" modals |
| `resources/views/report/newmaster2.blade.php` | `format_date`, `format_number`, `currencyNormalizer`, `nullToEmpty` |
| `docs/handoff-report-ui-redesign.md` | History and design rationale behind these components |
