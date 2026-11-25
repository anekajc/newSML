@extends('report.masterreportGudang')
{{-- @include('report.modalbrowsemaster') dihapus: sudah tidak dipakai, halaman ini punya
     picker sendiri (.modal-picker / openPickMaster()) yang menggantikan popup #formBrowseMaster
     bawaan -- lihat komentar di dalam <script> jsreport. --}}

{{-- @section('reportname')
      <h3>Report Saldo Stock</h3>
@endsection --}}

@section('header2')
<style>
  #tabel thead th {
    font-size: 13px !important;
    font-weight: 700 !important;
    padding: 12px 14px !important;
    white-space: nowrap;
  }
  #tabel tbody td {
    font-size: 13.5px !important;
    padding: 11px 14px !important;
    line-height: 1.5 !important;
  }
  #tabel tbody tr td {
    border-bottom: 1px solid #EEF1F8 !important;
  }

  /* Samakan baris Grand Total dengan .tb-report .grand-total di report-table.css. */
  #tabel #gtrow td {
    border: none !important;
    border-top: 2px solid #4F46E5 !important;
    background: linear-gradient(135deg, #EEF2FF, #F5F3FF) !important;
    font-weight: 800 !important;
    font-size: 14.5px !important;
    padding: 13px 14px !important;
  }
  #tabel #gtrow td.gt {
    text-align: left !important;
  }
  #tabel #gtrow td:not(.gt) {
    color: #4F46E5 !important;
  }

  /* Popup "Pilih Data" (Gudang/Grup/Kategori/SubKategori/Merk) dari dalam
     modal Filter. Dibuat manual */
  .modal-picker-backdrop {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0, 0, 0, .5);
    z-index: 1071;
  }
  .modal-picker-backdrop.show { display: block; }
  .modal-picker {
    display: none;
    position: fixed; inset: 0;
    z-index: 1072;
    overflow-x: hidden; overflow-y: auto;
    outline: 0;
  }
  .modal-picker.show { display: block; }
  .modal-picker .modal-dialog {
    margin: 1.75rem auto;
  }

  /* Dropdown + kotak cari khusus field Gudang di modal Filter. Menu MENGAMBANG (absolute
     terhadap .rt-combo yang sudah position:relative) tepat di bawah .rt-combo-input, jadi
     tidak mendorong field lain ke bawah. Aman dari pemotongan karena .modal-body tidak
     punya overflow dan Gudang field pertama (ruang di bawahnya masih banyak). */
  #modalFilter .rt-dd-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 20;
    margin-top: 4px;
    background: #fff;
    border: 1px solid #E2E8F4;
    border-radius: 9px;
    padding: 8px;
    box-shadow: 0 12px 28px -8px rgba(20, 30, 60, .25);
  }
  #modalFilter .rt-dd-search {
    width: 100%;
    height: 36px;
    padding: 0 10px;
    border: 1px solid #E2E8F4;
    border-radius: 8px;
    font-size: 13.5px;
    outline: none;
  }
  #modalFilter .rt-dd-search:focus {
    border-color: #2F6FED;
    box-shadow: 0 0 0 3px #EAF1FF;
  }
  #modalFilter .rt-dd-list {
    max-height: 220px;
    overflow-y: auto;
    margin-top: 6px;
  }
  #modalFilter .rt-dd-opt {
    padding: 8px 10px;
    font-size: 13.5px;
    border-radius: 7px;
    cursor: pointer;
  }
  #modalFilter .rt-dd-opt:hover { background: #F8F9FF; }
  #modalFilter .rt-dd-opt.is-selected {
    background: #EAF1FF;
    color: #2F6FED;
    font-weight: 600;
  }
  #modalFilter .rt-dd-empty {
    padding: 10px;
    text-align: center;
    color: #94a3b8;
    font-size: 12.5px;
  }
</style>

<div class="tb-report main" style="font-family: 'Segoe UI', sans-serif;">
  <div class="content" style="padding: 20px 24px 0;">

    <div hidden>
      <button type="button" id="buttonMode0" onclick="doReportMode(0)"></button>
      <button type="button" id="buttonMode1" onclick="doReportMode(1)"></button>
    </div>

    <!-- TOOLBAR -->
    <div class="toolbar">

      <!-- Periode -->
      <div class="filter-wrap">
        <label>Periode</label>
        <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
      </div>

      {{-- Search --}}
      <div>
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
      </div>

      <!-- filter + filter data + tampilkan + export -->
      <div class="action-group">
        <button
          class="btn-load"
          type="button"
          onclick="$('#modalFilter').modal('show')">
          <i class="fas fa-filter"></i> Filter
        </button>
        <button class="btn-load" onclick="doShowFormFilterData()" title="Filter Data"><i class="fas fa-magnifying-glass"></i> Filter Data</button>
        {{-- <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table"><i class="fas fa-cog"></i> Customize Table</button> --}}
        <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i class="fas fa-check"></i> Tampilkan</button>
        <div class="export-wrap" id="exportWrap">
          <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
          <div class="export-drop" id="exportDrop">
            <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span></div>
            <div class="export-opt" onclick="doExport('CSV')"><i class="bi bi-clipboard"></i> Ekspor ke <span class="ext">CSV</span></div>
            <div class="export-opt" onclick="doExport('Print')"><i class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bar kolom tersembunyi + Reset kolom (diisi report-table.js / ReportTable). -->
    <div id="rtBar"></div>

    <div class="table-outer">
      <div class="table-wrap" id="showTableReport">
        <table class="tb" id="tabel">
          <thead id="tabel_header">
            <tr>
              <th>KODE</th>
              <th>NAMA BARANG</th>
              <th>PART NUMBER</th>
              <th>KODE MERK</th>
              <th>MERK</th>
              <th>GUDANG</th>
              <th>SALDO</th>
              <th>SAT</th>
            </tr>
          </thead>
          <tbody id="tabel_data">
            <tr class="empty-row">
              <td colspan="100%">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer Tabel (Belum ada data dimuat) -->
      <div class="table-footer">
        <span id="footerLabel">Belum ada data dimuat</span>
      </div>

    </div>

    <div class="rt-hint">
      <i class="bi bi-info-circle"></i>
      Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
      sembunyikan kolom atau atur total.
    </div>

  </div>
</div>

<!-- modal filter -->
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i>
          Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>

        <button
          type="button"
          class="btn-close"
          aria-label="Close"
          data-bs-dismiss="modal">
        </button>
      </div>

      <div class="modal-body">

        <div class="rt-section">
          <div class="rt-group-label">Filter Data
            <span class="rt-group-hint">&mdash; klik untuk memilih</span>
          </div>
          {{-- Combo diisi renderPickFields() dari PICK_FIELDS -- lihat
               docs/new-filter-modal-ui-guide.md #4. Combo hanya tampilan di atas input
               hidden di bawah; yang menyimpan nilai asli tetap input hidden-nya, jadi
               makeTable() dan pickMasterSelect() tidak perlu tahu combo ini ada. --}}
          <div class="rt-grid-2" id="pickFields"></div>

          <input type="hidden" id="inputGudang" value="-">
          <input type="hidden" id="inputGrup" value="-">
          <input type="hidden" id="inputKategori" value="-">
          <input type="hidden" id="inputSubKategori" value="-">
          <input type="hidden" id="inputMerk" value="-">
        </div>

        <div class="rt-section">
          <div class="rt-group-label">Pengaturan Lain</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="inputIsi">No Satuan</label>
              <input type="number" id="inputIsi" class="form-control" value="1">
            </div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" data-bs-dismiss="modal">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- modal filter -->

<!-- modal pilih data master (Gudang/Grup/Kategori/SubKategori/Merk) -->
{{-- Picker gaya baru (docs/new-cust-supp-modal-guide.md): tanpa kolom Actions / tombol
     Select, seluruh baris bisa diklik. Skin-nya class .rt-picker-v2 -- class generik,
     bukan id-scoped (lihat catatan di public/css/report-table.css sekitar baris 1552),
     jadi id #modalPickMaster boleh tetap dipakai, sama seperti #formFilterData di
     masterreportGudang. Modal ini milik halaman ini sendiri, bukan blade shared, jadi
     tidak perlu digating window.g_pickerV2. --}}
<div class="modal-picker-backdrop" id="modalPickMasterBackdrop" onclick="closePickMaster()"></div>
<div class="modal-picker rt-picker-v2" id="modalPickMaster" tabindex="-1" role="dialog" aria-labelledby="modalPickMasterLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPickMasterLabel">Pilih Data</h5>
        <button type="button" class="btn-close" aria-label="Close" onclick="closePickMaster()"></button>
      </div>
      <div class="modal-body">
        <table id="tabelPickMaster" class="table table-bordered table-striped">
          <thead id="tabelPickMaster_header" class="text-center"></thead>
          <tbody id="tabelPickMaster_data" class="text-left"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closePickMaster()">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- modal pilih data master -->

@endsection


@section('jsreport')
<script type="text/javascript">
  g_modeReport = 0;

  const reportTitle = "LAPORAN STOK AKHIR";
  const reportGroupBy = "KodeBrg";

  if (typeof loadingHtml !== 'function') {
    window.loadingHtml = function (msg) {
      return '<span style="display:inline-flex;align-items:center;gap:6px;color:#5A6A85;">' +
             '<i class="fas fa-spinner fa-spin"></i> ' + (msg || 'Memuat...') + '</span>';
    };
  }

  // Daftar field picker di modal Filter. Combo (.rt-combo) cuma tampilan; nilai aslinya
  // disimpan di input hidden ber-id sama -- lihat docs/new-filter-modal-ui-guide.md #4.
  const PICK_FIELDS = [
    // type 'dropdown' -> menu + kotak cari di dalam modal Filter (lihat renderGudangList());
    // field lain tetap membuka popup "Pilih Data" lewat openPickMaster().
    { id: 'inputGudang',      label: 'Gudang',       url: '{!! $gudang !!}',      title: 'Pilih Gudang', type: 'dropdown' },
    { id: 'inputGrup',        label: 'Grup',         url: '{!! $grup !!}',        title: 'Pilih Grup' },
    { id: 'inputKategori',    label: 'Kategori',     url: '{!! $kategori !!}',    title: 'Pilih Kategori' },
    { id: 'inputSubKategori', label: 'Sub Kategori', url: '{!! $subkategori !!}', title: 'Pilih Sub Kategori' },
    { id: 'inputMerk',        label: 'Merk',         url: '{!! $merk !!}',        title: 'Pilih Merk' }
  ];

  // Dropdown Gudang: daftar diambil sekali dari endpoint Gudang (format {kolom, table} yang
  // sama dengan popup picker) lalu di-cache; mengetik di kotak cari hanya menyaring cache
  // ini di sisi klien, tanpa AJAX lagi.
  let gudangRows = null;        // null = belum dimuat
  let gudangLoading = false;
  let gudangOpen = false;

  function gudangKode(r) { return String(r.KODEGDG == null ? '' : r.KODEGDG).trim(); }
  function gudangNama(r) { return String(r.NAMA == null ? '' : r.NAMA).trim(); }
  function gudangLabel(r) { return gudangNama(r) ? gudangKode(r) + ' - ' + gudangNama(r) : gudangKode(r); }

  // htmlEscape() tidak meng-escape petik dua, padahal nilainya dipakai di dalam atribut
  // data-kode="..." -- jadi petik dua perlu di-escape juga.
  function attrEscape(_val) { return htmlEscape(_val).replace(/"/g, '&quot;'); }

  function loadGudangRows(url) {
    if (gudangRows !== null || gudangLoading) { return; }
    gudangLoading = true;
    $.ajax({
      url: url,
      type: 'get',
      success: function (res) {
        gudangRows = (res && res.table) || [];
        gudangLoading = false;
        // Menu sedang terbuka (user sudah mengetik di kotak cari) -> cukup segarkan daftarnya
        // saja; render ulang penuh akan membuang teks yang sedang diketik.
        if (gudangOpen) { renderGudangList(); } else { renderPickFields(); }
      },
      error: function () {
        gudangLoading = false;
        gudangRows = [];
        if (gudangOpen) { renderGudangList(); }
      }
    });
  }

  function renderPickFields() {
    let html = '';
    PICK_FIELDS.forEach(function (f) {
      const val = $('#' + f.id).val() || '-';
      const isSet = (val !== '-' && val !== '');
      const isDropdown = (f.type === 'dropdown');

      // Tag menampilkan "KODE - NAMA" begitu daftar Gudang sudah termuat; sebelum itu
      // cukup kodenya saja.
      let tagText = htmlEscape(val);
      if (isDropdown && isSet && gudangRows) {
        const hit = gudangRows.find(function (r) { return gudangKode(r) === String(val).trim(); });
        if (hit) { tagText = htmlEscape(gudangLabel(hit)); }
      }

      html += '<div>';
      html += '<label class="rt-field-label">' + f.label + '</label>';
      html += '<div class="rt-combo"' + (isDropdown ? ' id="gudangCombo"' : '') + '>';
      html += '<div class="rt-combo-input" onclick="' + (isDropdown
        ? 'toggleGudangMenu()'
        : 'openPickMaster(\'' + f.id + '\')') + '">';
      if (isSet) {
        html += '<span class="rt-combo-tag">' + tagText +
                '<button type="button" onclick="event.stopPropagation(); clearPickField(\'' + f.id +
                '\')">&times;</button></span>';
      } else {
        html += '<span class="rt-combo-placeholder">Pilih ' + f.label.toLowerCase() + '...</span>';
      }
      html += '<span class="rt-combo-chevron">' +
              '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>' +
              '</span>';
      html += '</div>';
      if (isDropdown && gudangOpen) {
        html += '<div class="rt-dd-menu">' +
          '<input type="text" class="rt-dd-search" id="gudangSearch" placeholder="Cari gudang..." autocomplete="off"' +
          ' oninput="renderGudangList()" onkeydown="gudangSearchKey(event)">' +
          '<div class="rt-dd-list" id="gudangList"></div></div>';
      }
      html += '</div></div>';

      if (isDropdown) { loadGudangRows(f.url); }
    });
    $('#pickFields').html(html);

    if (gudangOpen) {
      renderGudangList();
      $('#gudangSearch').trigger('focus');
    }
  }

  function filteredGudang() {
    const term = ($('#gudangSearch').val() || '').trim().toLowerCase();
    return (gudangRows || []).filter(function (r) {
      return !term || gudangLabel(r).toLowerCase().indexOf(term) !== -1;
    });
  }

  function renderGudangList() {
    const el = document.getElementById('gudangList');
    if (!el) { return; }
    if (gudangRows === null) {
      el.innerHTML = '<div class="rt-dd-empty">' + loadingHtml('Memuat data...') + '</div>';
      return;
    }
    const current = String($('#inputGudang').val() || '').trim();
    const rows = filteredGudang();
    if (!rows.length) {
      el.innerHTML = '<div class="rt-dd-empty">Tidak ada data ditemukan</div>';
      return;
    }
    el.innerHTML = rows.map(function (r) {
      const kode = gudangKode(r);
      return '<div class="rt-dd-opt' + (kode === current ? ' is-selected' : '') +
        '" data-kode="' + attrEscape(kode) + '">' + htmlEscape(gudangLabel(r)) + '</div>';
    }).join('');
  }

  function toggleGudangMenu() {
    gudangOpen = !gudangOpen;
    renderPickFields();
  }

  function closeGudangMenu() {
    if (!gudangOpen) { return; }
    gudangOpen = false;
    renderPickFields();
  }

  function gudangSearchKey(e) {
    if (e.key === 'Escape') {
      // stopPropagation supaya Esc cuma menutup menu, bukan ikut menutup modal Filter.
      e.stopPropagation();
      closeGudangMenu();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      const rows = filteredGudang();
      if (rows.length) { selectGudang(gudangKode(rows[0])); }
    }
  }

  function selectGudang(kode) {
    $('#inputGudang').val(kode);
    gudangOpen = false;
    renderPickFields();
    updateFilterBadge();
  }

  // Opsi dibangun lewat innerHTML tiap render, jadi klik didelegasikan (data-kode, bukan
  // onclick inline) -- sekaligus menghindari masalah tanda kutip di dalam kode gudang.
  $(document).on('click', '#pickFields .rt-dd-opt', function () {
    selectGudang($(this).attr('data-kode'));
  });

  // Klik di luar combo Gudang menutup menu. Penjaga document.contains() penting: klik pada
  // combo/opsi me-render ulang #pickFields, sehingga e.target sudah terlepas dari DOM saat
  // handler ini jalan -- tanpa penjaga, closest() gagal dan menu langsung tertutup lagi.
  $(document).on('click', function (e) {
    if (!gudangOpen || !document.contains(e.target)) { return; }
    if (!$(e.target).closest('#gudangCombo').length) { closeGudangMenu(); }
  });

  function clearPickField(id) {
    $('#' + id).val('-');
    gudangOpen = false;
    renderPickFields();
    updateFilterBadge();
  }

  /* modal "Pilih Data" (Gudang/Grup/Kategori/SubKategori/Merk)
     Menggantikan popup shared #formBrowseMaster (search + Submit). Gaya picker baru:
     tanpa kolom Actions / tombol Select, seluruh baris diklik --
     lihat docs/new-cust-supp-modal-guide.md. */

  // Picker gaya baru: TANPA kolom Actions / tombol Select, baris langsung diklik.
  function pickerHeadHtml(cols) {
    return '<tr>' + cols.map(c => '<th>' + c + '</th>').join('') + '</tr>';
  }

  function pickerRowHtml(idPart, kode, cellsHtml) {
    return '<tr class="pick-row" onclick="pickMasterSelect(\'' + jsQuote(idPart) + '\',\'' + jsQuote(kode) + '\')">' +
      cellsHtml + '</tr>';
  }

  // kode master bisa mengandung tanda kutip / backslash. Nilainya masuk ke dalam string
  // JS yang ada DI DALAM atribut onclick="...", jadi harus di-escape dua kali: dulu
  // untuk JS (backslash + petik satu), lalu untuk atribut HTML (& dan petik dua).
  function jsQuote(_val) {
    return String(_val)
      .replace(/\\/g, '\\\\')
      .replace(/'/g, "\\'")
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;');
  }

  function htmlEscape(_val) {
    return String(_val)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  let pickerTargetInput = "";

  function openPickMaster(targetInputId) {
    const f = PICK_FIELDS.find(function (x) { return x.id === targetInputId; });
    if (!f) { return; }

    pickerTargetInput = targetInputId;
    const url = f.url;
    $("#modalPickMasterLabel").text(f.title);

    try {
      if ($.fn.DataTable.isDataTable('#tabelPickMaster')) {
        $('#tabelPickMaster').DataTable().destroy();
      }
    } catch (e) {
      console.error('openPickMaster: gagal destroy DataTable sebelumnya', e);
    }

    $("#tabelPickMaster_header").html("");
    $("#tabelPickMaster_data").html('<tr><td>' + loadingHtml('Memuat data...') + '</td></tr>');

    $('#modalPickMasterBackdrop').addClass('show');
    $('#modalPickMaster').addClass('show').attr('aria-hidden', 'false');

    $.ajax({
      url: url,
      type: 'get',
      success: function (res) { renderPickMaster(res); },
      error: function () {
        $("#tabelPickMaster_data").html('<tr><td class="text-center">Gagal memuat data.</td></tr>');
      }
    });
  }

  function closePickMaster() {
    $('#modalPickMaster').removeClass('show').attr('aria-hidden', 'true');
    $('#modalPickMasterBackdrop').removeClass('show');
  }

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalPickMaster').hasClass('show')) {
      closePickMaster();
    }
  });

  function renderPickMaster(res) {
    const kolom = (res && res.kolom) || [];
    const rows = (res && res.table) || [];

    $("#tabelPickMaster_header").html(pickerHeadHtml(kolom.map(function (k) { return k[1]; })));

    let bodyHtml = '';
    if (rows.length) {
      rows.forEach(function (item) {
        let cellsHtml = '';
        kolom.forEach(function (k) {
          let val;
          if (k[2] === 'date') { val = format_date(item[k[0]]); }
          else if (k[2] === 'float') { val = format_number(currencyNormalizer(item[k[0]]), k[3]); }
          else { val = nullToEmpty(item[k[0]]); }
          cellsHtml += '<td>' + val + '</td>';
        });
        const kode = kolom.length ? item[kolom[0][0]] : '';
        bodyHtml += pickerRowHtml(pickerTargetInput, kode, cellsHtml);
      });
    } else {
      bodyHtml = '<tr><td colspan="' + kolom.length + '" class="text-center">Tidak ada data ditemukan</td></tr>';
    }
    $("#tabelPickMaster_data").html(bodyHtml);

    try {
      rtPickerInit('tabelPickMaster');
    } catch (e) {
      console.error('renderPickMaster: gagal inisialisasi DataTable', e);
    }
  }

  // padanan buttonPilih(idPart, kode) di docs/new-cust-supp-modal-guide.md: menulis nilai
  // terpilih ke input hidden milik field itu, lalu menutup picker.
  function pickMasterSelect(idPart, kode) {
    if (idPart) { $('#' + idPart).val(kode); }
    closePickMaster();
    renderPickFields();
    updateFilterBadge();
  }

  // Badge "N aktif" di judul modal Filter. Field dengan nilai netral ("-" untuk picker)
  // tidak dihitung -- sama seperti aturan di new-filter-modal-ui-guide.md #5.
  // No Satuan dianggap netral di "1" (artinya tidak ada konversi satuan).
  function updateFilterBadge() {
    let count = 0;
    PICK_FIELDS.forEach(function (f) {
      const val = $('#' + f.id).val();
      if (val && val !== '-') { count++; }
    });
    if (($('#inputIsi').val() || '1') !== '1') { count++; }
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    PICK_FIELDS.forEach(function (f) { $('#' + f.id).val('-'); });
    $('#inputIsi').val('1');
    gudangOpen = false;
    renderPickFields();
    updateFilterBadge();
  }

  $('#modalFilter').on('show.bs.modal', function () { gudangOpen = false; renderPickFields(); updateFilterBadge(); });
  $('#modalFilter').on('hide.bs.modal', function () { gudangOpen = false; });
  $('#modalFilter').on('input', '#inputIsi', updateFilterBadge);

  $(document).ready(function() {
    $("#gButtonCustomizeTable").hide();

    renderPickFields();

    $("#btnFilterData").on("click", function() {
      if (typeof doShowFormFilterData === "function") doShowFormFilterData();
      else alert(" Fungsi doShowFormFilterData belum tersedia.");
    });

    $("#btnCustomizeTable").on("click", function() {
      if (typeof doShowFormCustomizeTable === "function") doShowFormCustomizeTable();
      else alert(" Fungsi doShowFormCustomizeTable belum tersedia.");
    });

    $("#btnSubmitReport").on("click", function() {
      makeTable('REPORT');
    });

    // Header interaktif (drag / gigi / sembunyikan kolom) -- lihat
    // docs/new-slider-table-guide.md.
    ReportTable.init({
      table: '#tabel',
      bar: '#rtBar',
      onChange: renderCachedReport
    });
  });

  // Render ulang tabel dari data yang sudah dimuat (gcart_res), dipakai sebagai
  // onChange oleh ReportTable saat kolom di-drag/disembunyikan/direset.
  function renderCachedReport() {
    if (!gcart_res || !gcart_res.length) { return; }
    doShowReport(gcart_res, reportTitle, reportGroupBy, $("#inputDate1").val());
    doRenameGrandTotal("Total Item : " + gcart_res.length, galign_left);
  }

  function setDefaultHeader() {
    gcart_header = [
      ['KodeBrg', 'Kode', 1, 'varchar', 0, 0],
      ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
      ['PartNumber', 'Part Number', 1, 'varchar', 0, 0],
      ['KodeMerk', 'Kode Merk', 1, 'varchar', 0, 0],
      ['NAMAMERK', 'Merk', 1, 'varchar', 0, 0],
      ['NamaGdg', 'Gudang', 1, 'varchar', 0, 0],
      ['SALDOQNT', 'Saldo', 1, 'float', 1, 2],
      ['Sat1', 'Sat', 1, 'varchar', 0, 0]
    ];

    gsum_issubtotal = 0; gsum_isgrandtotal = 1;
  }

  function setRowHeader(_rowHeader) {
    // Header interaktif -- lihat docs/new-slider-table-guide.md.
    // cols HARUS berasal dari gcart_header.filter(...), bukan .map()/copy, supaya
    // ReportTable.headHtml() bisa memetakan tiap kolom balik ke index globalnya.
    const cols = gcart_header.filter(c => c[2] === 1);
    return _rowHeader + ReportTable.headHtml(cols);
  }

  function getRowFooter(_col) {
    let _sum = gcart_res.reduce((sum, item) => sum + currencyNormalizer(item[_col]), 0);
    return '  <td class="cellcompact-right" style="border: 1px solid black; white-space:nowrap;">' + format_number(_sum, 2) + '</td>';
  }

  function makeTable(_mode, _callback) {
    // nilai groupby adalah nama kolom (sesuai database) untuk pengelompokan subtotal
    let groupby = reportGroupBy;
    let _date1  = $("#inputDate1").val();

    let data = {
      date1            : _date1,
      inputGudang      : $("#inputGudang").val(),
      inputIsi         : $("#inputIsi").val(),
      inputGrup        : $("#inputGrup").val(),
      inputKategori    : $("#inputKategori").val(),
      inputSubKategori : $("#inputSubKategori").val(),
      inputMerk        : $("#inputMerk").val(),
    };

    // Mode FILTER dipanggil doShowFormFilterData() di layout, yang sekarang membaca
    // gcart_filter lewat _callback (doMakeTable() sudah async -- lihat catatan di
    // masterreportGudang.blade.php), jadi teruskan saja _callback apa adanya.
    if (_mode !== 'REPORT') {
      doMakeTable(_mode, groupby, data, reportTitle, _date1, null, _callback);
      return;
    }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    // doMakeTable() sekarang async (dulu async:false yang membekukan tab sampai response
    // datang tanpa sempat menggambar "Memuat data..." di atas) -- jadi bagian yang
    // membaca gcart_res harus jalan di callback ini, bukan lagi lewat trik nested rAF.
    doMakeTable(_mode, groupby, data, reportTitle, _date1, null, function () {
      doRenameGrandTotal("Total Item : " + gcart_res.length, galign_left);

      let footerMsg = (gcart_res && gcart_res.length > 0)
        ? "Menampilkan " + gcart_res.length + " baris"
        : "Belum ada data dimuat";
      document.getElementById('footerLabel').textContent = footerMsg;

      if (typeof _callback === 'function') { _callback(); }
    });
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // berapa pun bisa asal dalam bentuk array
    return ['KodeBrg', 'NamaBrg'];
  }

  // Pencarian cepat di sisi client atas data yang sudah dimuat (gcart_res).
  function applyFilters() {
    if (typeof gcart_res === 'undefined' || !gcart_res.length) { return; }
    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    const cols = gcart_header.filter(c => c[2] === 1);
    const rows = !term
      ? gcart_res
      : gcart_res.filter(r => cols.map(c => String(r[c[0]] || '')).join(' ').toLowerCase().indexOf(term) !== -1);

    doShowReport(rows, reportTitle, reportGroupBy, $("#inputDate1").val());
    doRenameGrandTotal("Total Item : " + rows.length, galign_left);
    document.getElementById('footerLabel').textContent =
      rows.length > 0 ? "Menampilkan " + rows.length + " baris" : "Tidak ada data ditemukan";
  }

  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); return; }
    if (typeof gcart_res !== 'undefined') {
      const cols = gcart_header.filter(c => c[2] === 1);
      const header = cols.map(c => c[1]);
      const body = gcart_res.map(r => cols.map(c => c[3] === 'float' || c[3] === 'int' ? currencyNormalizer(r[c[0]]) : (r[c[0]] || '')));
      ReportExport.fromRows([header].concat(body), fmt, { customName: 'LaporanStokAkhir' });
    }
  }
</script>

@endsection
