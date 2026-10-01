@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Lihat docs/new-design-all-report-guide.md + docs/new-slider-table-guide.md +
     docs/new-filter-modal-ui-guide.md + docs/new-cust-supp-modal-guide.md. --}}

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <!-- Periode (populated by populatePeriodSelectors) -->
      <div class="period-select-wrap">
        <label>Periode</label>
        <select class="period-select" id="periodBulan" onchange="changePeriodParts()"></select>
        <select class="period-select" id="periodTahun" onchange="changePeriodParts()"></select>
      </div>

      {{-- Costing & Sub Costing pindah ke modal Filter Laporan; mode Bulan/Tahun pindah ke
           ReportTable "Tampilan" switcher (#rtBar) -- lihat docs/new-filter-modal-ui-guide.md
           §3a: jangan duplikasi switcher yang sudah ada di #rtBar. --}}

      {{-- Search --}}
      <div>
        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
      </div>

      <!-- Actions -->
      <div class="action-group">
        {{-- Dibuka lewat plugin jQuery (Bootstrap 4/5, lihat catatan di modal Filter). --}}
        <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
          <i class="fas fa-filter"></i> Filter
        </button>
        {{-- <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table"><i class="fas fa-cog"></i> Customize Table</button> --}}
        <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i class="fas fa-check"></i> Tampilkan</button>
      </div>
    </div>

    <!-- Bar kolom tersembunyi + Tampilan (diisi oleh report-table.js / ReportTable) -->
    <div id="rtBar"></div>

    <!-- TABLE (header + rows dirender dinamis dari gcart_header) -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr><th>Tanggal</th></tr>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td>Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span id="footerLabel">Belum ada data dimuat</span>
      </div>
    </div>

    <div class="rt-hint">
      <i class="bi bi-info-circle"></i>
      Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan kolom atau atur desimal &amp; total.
    </div>

  </div><!-- /content -->

  <!-- TOAST -->
  <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>

</div><!-- /tb-report -->

{{-- Modal-modal di bawah DILETAKKAN DI LUAR .tb-report supaya reset
     `.tb-report *{margin:0;padding:0}` di report-table.css tidak merusak padding/margin
     modal Bootstrap. --}}

<!-- modal filter -->
<div class="modal fade rt-filter" id="modalFilter">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-filter"></i> Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        {{-- Kedua atribut dismiss + onclick eksplisit: lihat aturan Bootstrap di
             new-design-all-report-guide.md §5.1. --}}
        <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#modalFilter').modal('hide')"></button>
      </div>

      <div class="modal-body">

        <div class="rt-section">
          <div class="rt-group-label">Filter Data
            <span class="rt-group-hint">&mdash; klik untuk memilih</span>
          </div>
          <div class="rt-grid-2" id="pickFields"></div>

          {{-- Nilai sebenarnya (dibaca makeTable() & ditulis buttonPilihPerkiraan()/
               buttonPilihSubCosting()). "-" berarti belum dipilih / semua. --}}
          <input type="hidden" id="inputPerkiraan" value="-">
          <input type="hidden" id="inputSubCosting" value="-">
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="resetAllFilters()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" data-bs-dismiss="modal"
                  onclick="$('#modalFilter').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="applyModalFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- /modal filter -->

<!-- start modal select costing -->
<div class="modal fade rt-picker-v2" id="formSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelCosting" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabelCosting">Select Costing</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#formSelectPerkiraan').modal('hide')" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectPerkiraan" class="table table-bordered table-striped">
          <thead class="text-center">
            <tr>
              <th scope="col">Kode Costing</th>
              <th scope="col">Nama Costing</th>
            </tr>
          </thead>
          <tbody id="tabel_dataSelectPerkiraan" class="text-left"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#formSelectPerkiraan').modal('hide')">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- end modal select costing -->

<!-- start modal select subcosting -->
<div class="modal fade rt-picker-v2" id="formSelectSubCosting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelSubCosting" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1200px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabelSubCosting">Select Sub Costing</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#formSelectSubCosting').modal('hide')" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table id="tabelSelectSubCosting" class="table table-bordered table-striped">
          <thead class="text-center">
            <tr>
              <th scope="col">Kode Sub Costing</th>
              <th scope="col">Nama Sub Costing</th>
            </tr>
          </thead>
          <tbody id="tabel_dataSelectSubCosting" class="text-left"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#formSelectSubCosting').modal('hide')">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- end modal select subcosting -->
@endsection

@section('jsreport')
<script type="text/javascript">
  let defaultBulan = new Date().getMonth() + 1;  // 1–12
  let defaultTahun = new Date().getFullYear();

  let globalReportMode = "1"; // default: Bulan (1 = Bulan/detail, 2 = Tahun/rekap)
  let g_reportTitle = "";
  let g_inputPerkiraan = "-";
  let g_inputSubCosting = "-";

  let lastRows = [];               // hasil fetch terakhir (dipakai renderRows / search)
  let currentGroupby = 'tanggal';  // groupby aktif untuk render ulang saat search

  var modereport_detail = 1, modereport_rekap = 2;
  g_modeReport = modereport_detail;
  var jenisreport = 1;   // 1 = Bulan/detail, 2 = Tahun/rekap
  var DetOrRekap  = 1;

  // field yang dipakai modal Filter Laporan (§4 new-filter-modal-ui-guide.md)
  const PICK_FIELDS = [
    { id: 'inputPerkiraan',  label: 'Costing',     modal: 'costing' },
    { id: 'inputSubCosting', label: 'Sub Costing', modal: 'subcosting' },
  ];

  $(document).ready(function () {
    setReportMode(globalReportMode);   // memuat gcart_header untuk mode aktif
    populatePeriodSelectors();

    // Header tabel interaktif. "Tampilan" = mode report halaman ini (Bulan = detail,
    // Tahun = rekap), satu-satunya switcher mode sekarang (tidak diduplikasi di modal
    // Filter — lihat docs/new-filter-modal-ui-guide.md §3a).
    ReportTable.init({
      table: '#mainTable',
      bar: '#rtBar',
      onChange: function () {
        if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
      },
      views: {
        label: 'Tampilan',
        options: [
          { value: '1', label: 'Bulan', desc: 'Rincian per tanggal' },
          { value: '2', label: 'Tahun', desc: 'Rekap per bulan' }
        ],
        get: function () { return globalReportMode; },
        set: function (v) {
          setReportMode(String(v));
          // kolom & sumber data berbeda per mode, jadi muat ulang bila data sudah ada
          if (lastRows.length) { makeTable('REPORT'); }
        }
      }
    });

    renderPickFields();
    updateFilterBadge();

    // setTimeout(() => { makeTable("REPORT"); }, 100);
  });

  /* ── PERIODE (Bulan / Tahun) — sama seperti reportaccountinglabarugi.blade.php ── */
  const NAMA_BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  function populatePeriodSelectors() {
    const selB = document.getElementById('periodBulan');
    const selT = document.getElementById('periodTahun');
    selB.innerHTML = NAMA_BULAN.map((nama, i) =>
      `<option value="${i + 1}" ${(i + 1) == defaultBulan ? 'selected' : ''}>${nama}</option>`).join('');
    const thisYear = new Date().getFullYear();
    let years = '';
    for (let y = thisYear; y >= thisYear - 6; y--) {
      years += `<option value="${y}" ${y == defaultTahun ? 'selected' : ''}>${y}</option>`;
    }
    selT.innerHTML = years;
  }
  // Hanya perbarui nilai periode; laporan baru dimuat saat klik "Tampilkan".
  function changePeriodParts() {
    defaultBulan = parseInt(document.getElementById('periodBulan').value, 10);
    defaultTahun = parseInt(document.getElementById('periodTahun').value, 10);
  }

  /* ── kolom (gcart_header) per mode. Tabel dirender dari sini lewat renderRows(),
        jadi hasil drag/hide/desimal/total langsung ikut tampil. ── */
  function setDefaultHeader() {
    if (g_modeReport == modereport_detail) {
      gcart_header = [
        ['tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['KodePerk', 'Perkiraan', 1, 'varchar', 0, 0],
        ['Nama', 'Nama Perkiraan', 1, 'varchar', 0, 0],
        ['KETERANGAN', 'Keterangan', 1, 'varchar', 0, 0],
        ['Saldo', 'Jumlah', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    } else {
      gcart_header = [
        // NB: kolom ini sebelumnya bertipe 'date' (salah ketik) sehingga akan
        // diformat lewat format_date() dan tampil kosong/rusak di renderRows() —
        // dibetulkan ke 'varchar' (lihat catatan di laporan perubahan).
        ['KodePerk', 'Perkiraan', 1, 'varchar', 0, 0],
        ['KETERANGAN', 'Nama Perkiraan', 1, 'varchar', 0, 0],
        ['saldo1', 'Januari', 1, 'float', 1, 2],
        ['saldo2', 'Februari', 1, 'float', 1, 2],
        ['saldo3', 'Maret', 1, 'float', 1, 2],
        ['saldo4', 'April', 1, 'float', 1, 2],
        ['saldo5', 'Mei', 1, 'float', 1, 2],
        ['saldo6', 'Juni', 1, 'float', 1, 2],
        ['saldo7', 'Juli', 1, 'float', 1, 2],
        ['saldo8', 'Agustus', 1, 'float', 1, 2],
        ['saldo9', 'September', 1, 'float', 1, 2],
        ['saldo10', 'Oktober', 1, 'float', 1, 2],
        ['saldo11', 'November', 1, 'float', 1, 2],
        ['saldo12', 'Desember', 1, 'float', 1, 2],
        ['total', 'Total', 1, 'float', 1, 2]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    }
  }

  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 1 = Bulan, 2 = Tahun
    DetOrRekap = Number(val);

    setModeReport();
  }

  function setModeReport() {
    g_modeReport = (jenisreport === 1) ? modereport_detail : modereport_rekap;
    doSetHeader(g_modeReport);   // muat susunan kolom (default / hasil kustomisasi user)
    doShowCustomize();
  }

  /* ── LOAD DATA ──
        Bulan (DetOrRekap=1): Sp_ReportCosting -> baris per tanggal.
        Tahun (DetOrRekap=2): Sp_ReportCostingRekThn -> baris sudah dalam bentuk
        rekap (saldo1..saldo12/total), sesuai kolom gcart_header mode Tahun di atas. ── */
  function makeTable(_mode) {
    const groupby = (DetOrRekap === 1) ? 'tanggal' : 'KodePerk';

    // Controller (Sp_ReportCosting / Sp_ReportCostingRekThn) masih mengharapkan satu
    // string "YYYY-MM" di param date2 (di-explode('-') jadi Tahun/Bulan di server).
    const _date2 = defaultTahun + '-' + String(defaultBulan).padStart(2, '0');
    const _inputPerkiraan = $('#inputPerkiraan').val() || '-';
    const _inputSubCosting = $('#inputSubCosting').val() || '-';

    g_reportTitle = 'REPORT ACCOUNTING COSTING';
    g_inputPerkiraan = _inputPerkiraan;
    g_inputSubCosting = _inputSubCosting;

    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = {
      date2: _date2,
      inputPerkiraan: _inputPerkiraan,
      inputSubCosting: _inputSubCosting,
      detOrRekap: DetOrRekap
    };

    const url = (DetOrRekap === 1)
      ? "{{ url('/reportaccountingcosting_doReport') }}"
      : "{{ url('/reportaccountingcosting_saldoawal') }}";
    const rowsKey = (DetOrRekap === 1) ? 'res1' : 'res2';

    $.ajax({
      url: url,
      type: 'get',
      data: data,
      success: function (res) {
        lastRows = (res && res[rowsKey]) ? res[rowsKey] : [];
        currentGroupby = groupby;
        $('#searchBox2').val('');
        renderRows(lastRows, groupby);
      },
      error: function () {
        lastRows = [];
        currentGroupby = groupby;
        renderRows([], groupby);
      }
    });
  }

  /* ── RENDER KE TABEL STYLED (.tb-report #mainTable) ──
     Kolom dibangun DINAMIS dari gcart_header (hanya kolom terlihat / item[2]===1,
     sesuai urutan simpanan) — hasil drag/hide/desimal/total langsung tampil. ── */
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int') && c[4] === 1);
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showSub   = hasTotal && (gsum_issubtotal === 1);
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);

    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null;
    let sub = {}, grand = {};
    totalKeys.forEach(k => { sub[k] = 0; grand[k] = 0; });

    rows.forEach(function (r, i) {
      const now = pickCI(r, groupby);

      if (showSub && i !== 0 && prev !== now) {
        html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
        totalKeys.forEach(k => sub[k] = 0);
      }

      totalKeys.forEach(function (k) {
        const v = currencyNormalizer(r[k]);
        sub[k] += v; grand[k] += v;
      });

      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    if (showSub)   html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total: nilai di tiap kolom numerik yang ditotal; label di kolom pertama non-total.
  function totalRow(label, sums, cols, totalKeys, cls) {
    const labelIdx = cols.findIndex(c => totalKeys.indexOf(c[0]) === -1);
    const tds = cols.map(function (c, idx) {
      if (totalKeys.indexOf(c[0]) !== -1) return '<td class="num">' + format_number(sums[c[0]], c[5]) + '</td>';
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });
    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  // Ambil properti baris tanpa peduli besar/kecil huruf (proc mencampur casing).
  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

  /* ── PENCARIAN SISI-KLIEN: saring lastRows lalu render ulang tabel styled ── */
  function applyFilters() {
    if (!lastRows.length) return;
    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    if (!term) { renderRows(lastRows, currentGroupby); return; }

    const cols = gcart_header.filter(c => c[2] === 1);
    const filtered = lastRows.filter(r => rowSearchText(r, cols).indexOf(term) !== -1);
    renderRows(filtered, currentGroupby);
  }

  function rowSearchText(r, cols) {
    return cols.map(function (c) {
      const v = r[c[0]];
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  /* ── Filter Data engine (opsional, modal lama "Filter Data") ── */
  function getKolomFilter() {
    return ['nobukti', 'tanggal'];
  }

  /* ══════════════════ MODAL FILTER LAPORAN ══════════════════ */

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
    // Sub Costing tergantung Costing yang dipilih; kosongkan juga saat Costing dihapus
    // supaya tidak nyangkut ke Costing lama (menjaga konsistensi filter).
    if (id === 'inputPerkiraan') { $('#inputSubCosting').val('-'); }
    renderPickFields();
    updateFilterBadge();
  }

  function updateFilterBadge() {
    let count = 0;
    PICK_FIELDS.forEach(function (f) {
      const val = $('#' + f.id).val();
      if (val && val !== '-') { count++; }
    });
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    PICK_FIELDS.forEach(function (f) { $('#' + f.id).val('-'); });
    renderPickFields();
    updateFilterBadge();
  }

  $('#modalFilter').on('show.bs.modal', function () {
    renderPickFields();
    updateFilterBadge();
  });

  function applyModalFilter() {
    $('#modalFilter').modal('hide');
  }

  // Buka picker dari dalam modal Filter: Bootstrap tidak menumpuk modal dengan rapi,
  // jadi modal Filter disembunyikan dulu & dibuka lagi setelah picker ditutup.
  let g_reopenFilter = false;

  function pickFromModal(which) {
    g_reopenFilter = true;
    $('#modalFilter').modal('hide');
    if (which === 'costing') {
      buttonSelectPerkiraan();
    } else {
      buttonSelectSubCosting();
    }
  }

  $(document).on('hidden.bs.modal', '#formSelectPerkiraan, #formSelectSubCosting', function () {
    if (g_reopenFilter) {
      g_reopenFilter = false;
      $('#modalFilter').modal('show');
      renderPickFields();
      updateFilterBadge();
    }
  });

  /* ══════════════════ PICKER: SELECT COSTING ══════════════════
     Picker gaya baru (docs/new-cust-supp-modal-guide.md): TANPA kolom Actions /
     tombol Select, baris langsung diklik. Modal ini page-local (tidak di-@@include
     halaman lain), jadi konversi ini tidak berdampak ke halaman lain. ── */
  function buttonSelectPerkiraan() {
    loadSelectPerkiraan();
    $('#formSelectPerkiraan').modal('toggle');
  }

  function buttonPilihPerkiraan(selectedPerkiraan) {
    $('#inputPerkiraan').val(selectedPerkiraan);
    $('#formSelectPerkiraan').modal('hide');
  }

  function loadSelectPerkiraan() {
    let _token = $('#_token').val();

    $('#tabelSelectPerkiraan').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingcosting_loadperkiraan') !!}",
      type: 'get',
      async: false,
      data: { _token: _token },
      success: function (res) { dataRefresh = res; }
    });

    let rowTable = '';
    dataRefresh.forEach((item) => {
      rowTable += '<tr class="pick-row" onclick="buttonPilihPerkiraan(\'' + item.KodeCost + '\')">' +
        '<td>' + item.KodeCost + '</td>' +
        '<td>' + item.NamaCost + '</td>' +
        '</tr>';
    });

    document.getElementById('tabel_dataSelectPerkiraan').innerHTML = rowTable;
    $('#tabelSelectPerkiraan').DataTable({
      lengthChange: false,
      paging: true,
    });
  }

  /* ══════════════════ PICKER: SELECT SUB COSTING ══════════════════ */
  function buttonSelectSubCosting() {
    loadSelectSubCosting();
    $('#formSelectSubCosting').modal('toggle');
  }

  function buttonPilihSubCosting(selectedSubCosting) {
    $('#inputSubCosting').val(selectedSubCosting);
    $('#formSelectSubCosting').modal('hide');
  }

  function loadSelectSubCosting() {
    let _token = $('#_token').val();
    let noKira = $('#inputPerkiraan').val();

    if (!noKira || noKira === '-' || noKira.trim() === '') {
      alert('Pilih Cost terlebih dahulu');
      return;
    }

    $('#tabelSelectSubCosting').DataTable().destroy();

    $.ajax({
      url: "{!! url('reportaccountingcosting_loadsubcosting') !!}",
      type: 'get',
      async: false,
      data: {
        _token: _token,
        NoKira: noKira
      },
      success: function (res) { dataRefresh = res; }
    });

    let rowTable = '';
    dataRefresh.forEach((item) => {
      rowTable += '<tr class="pick-row" onclick="buttonPilihSubCosting(\'' + item.KodeSubCost + '\')">' +
        '<td>' + item.KodeSubCost + '</td>' +
        '<td>' + item.NamaSubCost + '</td>' +
        '</tr>';
    });

    $('#tabel_dataSelectSubCosting').html(rowTable);
    $('#tabelSelectSubCosting').DataTable({
      lengthChange: false,
      paging: true,
    });
  }
</script>

@endsection
