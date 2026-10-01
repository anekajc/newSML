@extends('report.masterreport2')

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
        {{-- <div>
          <div class="page-title">Pemakaian</div>
        </div> --}}

        <!-- date range-->
        <div class="filter-wrap">
          <label>Periode</label>
          <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
          <span class="filter-sep">s/d</span>
          <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>

        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()">

        <div class="action-group">
          {{-- Dibuka lewat plugin jQuery (Bootstrap 4), BUKAN data-bs-toggle (Bootstrap 5).
               Halaman ini memuat dua Bootstrap; jQuery dimuat SESUDAH bundle BS5, jadi
               $.fn.modal dipegang BS4. applyModalFilter() menutup modal ini dengan
               $('#modalFilter').modal('hide'), jadi pembukanya harus API yang sama. --}}
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

      <!-- Bar kolom tersembunyi (diisi oleh report-table.js / ReportTable) -->
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

  {{-- Modal DILETAKKAN DI LUAR .tb-report supaya reset `.tb-report *{margin:0;padding:0}`
   di report-table.css tidak merusak padding/margin modal Bootstrap. --}}

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
          {{-- data-dismiss (BS4) = yang benar-benar menutup, karena modal ini dibuka lewat
               $.fn.modal milik BS4. data-bs-dismiss dibiarkan untuk jaga-jaga. --}}
          <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
              onclick="$('#modalFilter').modal('hide')"></button>
        </div>

        <div class="modal-body">

          <div class="rt-section">
            {{-- <div class="rt-group-label">Pengaturan Laporan</div> --}}
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
  let globalOrderBy = "N";  // default: No Bukti
  let globalReportMode = "0";
  var jenisreport = 0;
  let lastRows = [];
  let currentGroupby = 'NoBukti';

  // Kolom (gcart_header) TIDAK berubah per Order By (lihat setDefaultHeader()) -- hanya
  // groupby/subtotal & parameter Ordr yang berubah. Tetap satu slot kolom tersimpan per
  // ordering walau isinya sama, supaya kustomisasi kolom user (DBSIMPANHEADER) tidak bleed
  // antar ordering. modereport_nobukti mempertahankan angka slot lama (modereport_detail=3)
  // supaya layout tersimpan milik user yang sudah ada tidak hilang.
  var modereport_nobukti = 3,
      modereport_barang  = 4;
  g_modeReport = modereport_nobukti;

  // Order By: N (No Bukti) / B (Barang) -- parameter nyata (Ordr) yang dikirim ke proc &
  // dikonfirmasi diterima proc tsb.
  const ORDER_OPTIONS = [
    { value: 'N', label: 'No Bukti', desc: 'Dikelompokkan per No Bukti' },
    { value: 'B', label: 'Barang', desc: 'Dikelompokkan per Kode Barang' },
  ];
  let viewsCfg = {
    label: 'Order By',
    options: ORDER_OPTIONS,
    get: function() { return globalOrderBy; },
    set: function(v) {
      setOrderBy(String(v));
      if (lastRows.length) { makeTable('REPORT'); } // re-fetch: inputOrd adalah parameter SP
    }
  };

  const reportUrl = "{{ url('laporanpemakaian_doReport') }}";

  $(document).ready(function() {
    // Tabel bergaya lama dari engine masterreport2 (#showTableReport) tidak pernah dipakai
    // halaman ini (kita punya #mainTable/.tb-report sendiri, tidak pernah panggil doMakeTable
    // versi lama) -- dikosongkan & disembunyikan, sama seperti reportaccountingkasharian.blade.php.
    $("#showTableReport").empty().hide();

    setDefaultHeader();
    setOrderBy(globalOrderBy);

    // Header tabel interaktif: drag-reorder + gear (sembunyikan/desimal/total) + bar
    // "Reset kolom"/kolom tersembunyi + switcher "Order By". Tidak ada "Tampilan" switcher --
    // jenisreport tidak pernah punya kontrol UI di halaman ini (selalu 0), sama seperti
    // sebelum migrasi.
    ReportTable.init({
      table: '#mainTable',
      bar: '#rtBar',
      onChange: function() {
        if (lastRows.length) { applyFilters(); } else { renderRows([], currentGroupby); }
      },
      views: viewsCfg
    });

    // setTimeout(() => {
    //   makeTable('REPORT');
    // }, 100);
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
    if ("{!! $mode_menu !!}" == "QTY") {
      if (jenisreport === 1) {
        gcart_header = [
          ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
          ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
          ['KodePerkiraan', 'No. PRK', 1, 'varchar', 0, 0],
          ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
          ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
          ['Qnt', 'Qty', 1, 'float', 1, 2],
          ['HPP', 'HPP', 1, 'float', 1, 2],
          ['NilaiHPP', 'HPP X Qty', 1, 'float', 1, 2]
        ];
      } else {
        gcart_header = [
          ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
          ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
          ['KodePerkiraan', 'No. PRK', 1, 'varchar', 0, 0],
          ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
          ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
          ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
          ['Qnt', 'Qty', 1, 'float', 1, 2],
          ['HPP', 'HPP', 1, 'float', 1, 2],
          ['NilaiHPP', 'HPP X Qty', 1, 'float', 1, 2],
          ['StatusOto', 'Otorisasi', 1, 'status', 0, 0]
        ];
      }
    } else {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['KodePerkiraan', 'No. PRK', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['Qnt', 'Qty', 1, 'float', 1, 2],
        ['HPP', 'HPP', 1, 'float', 1, 2],
        ['NilaiHPP', 'HPP X Qty', 1, 'float', 1, 2],
        ['StatusOto', 'Otorisasi', 1, 'status', 0, 0]
      ];
    }
    gsum_issubtotal = 1;
    gsum_isgrandtotal = 1;

  }

  // Field aslinya (dari laporanpemakaian_doReport): "NeedOtorisasi".
  let _otoFieldWarned = false;
  function getOtoStatusLabel(r) {
    const need = pickCI(r, 'NeedOtorisasi');
    if (need !== undefined && need !== null && need !== '') {
      const s = String(need).trim().toUpperCase();
      const needTruthy = (need === 1 || need === '1' || need === true || s === 'Y' || s === 'YES' || s === 'TRUE');
      return needTruthy
        ? { text: 'Belum', color: '#B91C1C', bg: '#FEE2E2' }
        : { text: 'Sudah', color: '#15803D', bg: '#DCFCE7' }
    }

    const candidates = ['Otorisasi', 'StatusOtorisasi', 'IsOtorisasi', 'FlagOtorisasi', 'Oto', 'StatusOto', 'Approve', 'Approved', 'IsApproved', 'Disetujui', 'StatusApproval', 'Approval', 'Auth', 'IsAuth', 'Authorized', 'Acc', 'ACC'];
    let raw;
    for (const c of candidates) {
      const v = pickCI(r, c);
      if (v !== undefined && v !== null && v !== '') { raw = v; break; }
    }
    if (raw === undefined) {
      if (!_otoFieldWarned) {
        _otoFieldWarned = true;
        console.warn('[Status Otorisasi] Nama field-nya gak ketemu dari daftar kandidat. Cek nama kolom asli di sini:', Object.keys(r));
        console.warn('[Status Otorisasi] Contoh isi 1 baris data:', r);
      }
      return { text: '(field?)', color: '#B45309', bg: '#FEF3C7' };
    }

    const s = String(raw).trim().toUpperCase();
    const truthy = (raw === 1 || raw === '1' || raw === true || s === 'Y' || s === 'YES' || s === 'OTORISASI' || s === 'SUDAH' || s === 'TRUE');
    return truthy
      ? { text: 'Belum', color: '#B91C1C', bg: '#FEE2E2' }
      : { text: 'Sudah', color: '#15803D', bg: '#DCFCE7' };
  }

  function renderOtoStatusCell(r) {
    const st = getOtoStatusLabel(r);
    return '<td><span style="display:inline-flex; align-items:center; gap:6px; padding:2px 10px; border-radius:999px; font-size:11.5px; font-weight:700; color:' + st.color + '; background:' + st.bg + ';"><span style="width:6px; height:6px; border-radius:50%; background:' + st.color + '; flex-shrink:0;"></span>' + st.text + '</span></td>';
  }

  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalOtorisasi").val(globalOtorisasi);
    updateFilterBadge();
  });

  function applyModalFilter() {
    setOtorisasi($("#modalOtorisasi").val());

    $('#modalFilter').modal('hide');
  }

  function setOtorisasi(val) { globalOtorisasi = val; }

  // order by: menentukan groupby subtotal + slot kolom tersimpan (lewat g_modeReport), bukan
  // susunan kolom itu sendiri -- lihat setDefaultHeader().
  function setOrderBy(val) {
    globalOrderBy = val;
    if (val === 'B') {
      currentGroupby = 'KodeBrg';
      g_modeReport = modereport_barang;
    } else {
      currentGroupby = 'NoBukti';
      g_modeReport = modereport_nobukti;
    }
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }
    if (typeof doShowCustomize === 'function') { doShowCustomize(); }
  }


  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();

    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }
    setDefaultHeader();
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
        lastRows = Array.isArray(res) ? res : ((res && res.res1) ? res.res1 : []);
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

    // HEADER dinamis — dibangun report-table.js (ReportTable) supaya kolom bisa diseret
    // untuk diurutkan & punya menu roda gigi (sembunyikan / desimal / total). Juga
    // menyegarkan #rtBar (daftar kolom tersembunyi).
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
        if (type === 'status') return renderOtoStatusCell(r);
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
      if (c[3] === 'status') return getOtoStatusLabel(r).text;
      const v = r[c[0]];
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  function doExport(fmt) { if (fmt === 'Print') { window.print(); } }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
</script>
@endsection
