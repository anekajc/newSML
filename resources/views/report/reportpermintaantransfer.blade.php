@extends('report.masterreport2')

{{-- Laporan Permintaan Transfer (2026-11-25): tampilan disamakan dengan Laporan Permintaan
     Pemakaian (reportpermintaanpemakaian.blade.php), tanpa switcher Jenis. Data dari
     Sp_ReportTransferDetPR (view VwreporttransferPR) lewat laporanpermintaantransfer_doReport.
     Filter: Otorisasi. Order By No Bukti / Barang (parameter Ordr SP). --}}

<style>
    .tb-report .table-wrap {
        min-height: 10vh;
    }
</style>

@section('header2')
  <div class="tb-report main">
    <div class="content">

      <!-- toolbar-->
      <div class="toolbar">
        <!-- date range-->
        <div class="filter-wrap">
          <label>Periode</label>
          <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-01') !!}">
          <span class="filter-sep">s/d</span>
          <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>

        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()">

        <div class="action-group">
          {{-- Dibuka lewat plugin jQuery (Bootstrap 4), sama seperti report lain. --}}
          <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
            <i class="fas fa-filter"></i> Filter
          </button>
          <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i class="fas fa-check"></i> Tampilkan</button>
          <div class="export-wrap" id="exportWrap">
            <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
            <div class="export-drop" id="exportDrop">
              <div class="export-opt" onclick="doExport('Excel')"> Ekspor ke <span class="ext">XLSX</span></div>
              <div class="export-opt" onclick="doExport('Print')"> Cetak Laporan</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bar kolom tersembunyi + switcher Order By (report-table.js / ReportTable) -->
      <div id="rtBar"></div>

      <!-- tabel-->
      <div class="table-outer">
        <div class="table-wrap">
          <table class="tb" id="mainTable">
            <thead>
            </thead>
            <tbody id="tableBody">
              <tr class="empty-row"><td colspan="8">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="table-footer">
          <span id="footerLabel">Belum ada data dimuat</span>
        </div>
      </div>

      <div class="rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> pada judul kolom untuk
        sembunyikan kolom atau atur desimal &amp; total.
      </div>
    </div>

    <!-- toast-->
    <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
  </div>

  {{-- Modal di luar .tb-report supaya reset CSS report-table.css tidak merusak modal Bootstrap. --}}

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
          <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
              onclick="$('#modalFilter').modal('hide')"></button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-grid-2">
              <div>
                <label class="rt-field-label" for="modalOtorisasi">Otorisasi</label>
                <select class="rt-native" id="modalOtorisasi">
                  <option value="2">Semua</option>
                  <option value="1">Belum</option>
                  <option value="0">Sudah</option>
                </select>
              </div>
            </div>
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
  <!-- modal filter -->

@endsection

@section('jsreport')

<script type="text/javascript">
  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";
  let globalOtorisasi = "2";
  let globalOrderBy = "N";  // N = No Bukti, B = Barang (parameter Ordr Sp_ReportTransferDetPR)
  let lastRows = [];
  let currentGroupby = 'nobukti';

  // Slot kolom tersimpan (DBSIMPANHEADER.reportmode). 11/12 dipakai supaya layout tersimpan
  // dari halaman lama (kolomnya lain) tidak ikut terbaca.
  const MODE_SLOT = { N: 11, B: 12 };
  g_modeReport = MODE_SLOT.N;

  const ORDER_OPTIONS = [
    { value: 'N', label: 'No Bukti' },
    { value: 'B', label: 'Barang' },
  ];
  let viewsCfg = {
    label: 'Order By',
    options: ORDER_OPTIONS,
    get: function() { return globalOrderBy; },
    set: function(v) {
      globalOrderBy = String(v);
      applyMode();
      if (lastRows.length) { makeTable('REPORT'); } // re-fetch: inputOrd adalah parameter SP
    }
  };

  const reportUrl = "{{ url('laporanpermintaantransfer_doReport') }}";

  $(document).ready(function() {
    $("#showTableReport").empty().hide();

    setDefaultHeader();
    applyMode();

    // pulihkan Otorisasi terakhir yang di-Terapkan user ini (DBUSERFILTER).
    UserFilter.restore('#modalFilter', function (v) {
      if (v.modalOtorisasi !== undefined) { setOtorisasi(v.modalOtorisasi); }
    });

    ReportTable.init({
      table: '#mainTable',
      bar: '#rtBar',
      onChange: function() {
        if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
      },
      views: viewsCfg
    });
  });

  /* -- FILTER MODAL -- */
  function updateFilterBadge() {
    let count = 0;
    if ($('#modalOtorisasi').val() !== '2') { count++; }
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    $('#modalOtorisasi').val('2');
    updateFilterBadge();
  }

  $('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

  function setDefaultHeader() {
    gcart_header = [
      ['nobukti', 'No. Bukti', 1, 'varchar', 0, 0],
      ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
      ['KODEBRG', 'Kode Barang', 1, 'varchar', 0, 0],
      ['NAMABRG', 'Nama Barang', 1, 'varchar', 0, 0],
      ['NamagdgAsal', 'Gudang Asal', 1, 'varchar', 0, 0],
      ['NamagdgTujuan', 'Gudang Tujuan', 1, 'varchar', 0, 0],
      ['SAT_1', 'Satuan', 1, 'varchar', 0, 0],
      ['QNT', 'Qty', 1, 'float', 1, 2]
    ];
    gsum_issubtotal = 1;
    gsum_isgrandtotal = 1;
  }

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalOtorisasi").val(globalOtorisasi);
    updateFilterBadge();
  });

  function applyModalFilter() {
    setOtorisasi($("#modalOtorisasi").val());
    UserFilter.save('#modalFilter');
    $('#modalFilter').modal('hide');
  }

  function setOtorisasi(val) { globalOtorisasi = val; }

  // Order By menentukan groupby subtotal dan slot kolom tersimpan (g_modeReport).
  function applyMode() {
    currentGroupby = (globalOrderBy === 'B') ? 'KODEBRG' : 'nobukti';
    g_modeReport = MODE_SLOT[globalOrderBy] || MODE_SLOT.N;
    setDefaultHeader();
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }
    if (typeof doShowCustomize === 'function') { doShowCustomize(); }
    if (!lastRows.length && window.ReportTable && document.querySelector('#rtBar.rt-bar')) {
      renderRows([], currentGroupby);
    }
  }

  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();

    setDefaultHeader();
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }
    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let filterData = {
      date1: globalDate1,
      date2: globalDate2,
      inputOto: globalOtorisasi,
      inputOrd: globalOrderBy
    };

    $.ajax({
      url: reportUrl,
      type: 'get',
      data: filterData,
      success: function (res) {
        lastRows = Array.isArray(res) ? res : [];
        $('#searchBox2').val('');
        renderRows(lastRows, currentGroupby);
      },
      error: function () {
        lastRows = [];
        renderRows([], currentGroupby);
      }
    });
  }

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
        const v = currencyNormalizer(pickCI(r, k));
        sub[k] += v; grand[k] += v;
      });

      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        const v = pickCI(r, key);
        if (type === 'date') return '<td>' + format_date(v) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(v), c[5]) + '</td>';
        return '<td>' + nullToEmpty(v) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    if (showSub)   html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    if (showGrand) html += totalRow('GRAND TOTAL', grand, cols, totalKeys, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  function totalRow(label, sums, cols, totalKeys, cls) {
    const labelIdx = cols.findIndex(c => totalKeys.indexOf(c[0]) === -1);
    const tds = cols.map(function (c, idx) {
      if (totalKeys.indexOf(c[0]) !== -1) return '<td class="num">' + format_number(sums[c[0]], c[5]) + '</td>';
      if (idx === labelIdx) return '<td>' + label + '</td>';
      return '<td></td>';
    });
    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }

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
      const v = pickCI(r, c[0]);
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); return; }
    if (!document.querySelector('#tableBody tr.data-row')) {
      const msg = 'Tidak ada data untuk di-export. Klik Tampilkan terlebih dahulu.';
      if (window.alertify && typeof alertify.alert === 'function') { alertify.alert(msg); } else { alert(msg); }
      return;
    }
    doExportTableToExcel('mainTable');
  }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
</script>
@endsection
