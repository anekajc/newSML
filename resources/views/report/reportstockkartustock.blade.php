@extends('report.masterreportGudang')
{{-- @include('report.modalBrowseMaster') dihapus: (1) sudah tidak dipakai -- halaman ini
     punya picker sendiri (#modalPickMaster / openBarangPicker()), tidak ada satu pun
     pemanggilan doBrowseMaster; (2) nama filenya di disk modalbrowsemaster.blade.php
     (huruf kecil semua), jadi ejaan "modalBrowseMaster" cuma jalan di Windows dan akan
     gagal "View not found" di server Linux. --}}

@section('header2')
<style>
  /* popup "pilih data" dari dalam modal filter. ini dibuat manual pakai z-index di atas modal bootstrap */
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

  #tabelPickMaster,
  #tabelPickMaster th,
  #tabelPickMaster td {
    border: 0 !important;
  }
  #tabelPickMaster thead th {
    border-bottom: 2px solid var(--border) !important;
  }
  #tabelPickMaster tbody tr.pick-row td {
    border-bottom: 1px solid #F1F5F9 !important;
  }
</style>

  <div class="tb-report main">
    <div class="content">

      <!-- toolbar -->
      <div class="toolbar">
        {{-- <div>
          <div class="page-title">
            @if ($mode_menu == 'QTY')
              Kartu Stok (Qty)
            @else
              Kartu Stok (Qty + Rp)
            @endif
          </div>
        </div> --}}

        {{-- Periode: tanggal penuh. Sp_reportkartuStock cuma menerima bulan+tahun
             (controller memecah string ini dan hanya memakai bagian [0] tahun & [1]
             bulan), jadi query tetap mengambil satu bulan penuh; pemangkasan sampai
             level HARI dilakukan di sisi client lewat filterByDateRange(). --}}
        <div class="filter-wrap">
          <label>Periode</label>
          <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
          <span class="filter-sep">s/d</span>
          <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
        </div>

        <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()">

        <div class="action-group">
          <button
            class="btn-load"
            data-bs-toggle="modal"
            data-bs-target="#modalFilter">
            <i class="fas fa-filter"></i> Filter
          </button>
          <button class="btn-load" onclick="makeTable('REPORT')" title="Tampilkan laporan"><i class="fas fa-check"></i> Tampilkan</button>
          <div class="export-wrap" id="exportWrap">
            <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
            <div class="export-drop" id="exportDrop">
              <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span></div>
              <div class="export-opt" onclick="doExport('Print')"><i class="bi bi-printer-fill text-warning"></i> Cetak Laporan</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Bar kolom tersembunyi + Reset kolom (diisi report-table.js / ReportTable).
           Hanya aktif di mode QTY -- mode QTY+Rp memakai header grouping rowspan/colspan
           (buildTheadQtyRp) yang tidak bisa diwakili satu <th> per kolom, lihat
           docs/new-slider-table-guide.md #1. --}}
      <div id="rtBar"></div>

      <!-- tabel -->
      <div class="table-outer">
        <div class="table-wrap">
          <table class="tb" id="mainTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Bukti</th>
                    <th>Tipe</th>
                    <th>Keterangan</th>
                    <th>Sat</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody id="tableBody">
              <tr class="empty-row"><td id="emptyColspan" colspan="8">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
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
        sembunyikan kolom atau atur total.
      </div>
    </div>

    <!-- toast -->
    <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
  </div>

  <!-- modal filter: Gudang, Barang, No Satuan -->
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

            {{-- Combo Barang diisi renderPickFields() -- lihat
                 docs/new-filter-modal-ui-guide.md #4. Combo hanya tampilan di atas input
                 hidden di bawah; yang menyimpan nilai asli tetap input hidden-nya. --}}
            <div class="rt-grid-2" id="pickFields"></div>

            <input type="hidden" id="inputBarang" value="-">

            <div class="rt-grid-2">
              <div>
                {{-- Punya opsi netral ("-- Semua Gudang --"), jadi IKUT dihitung di badge
                     begitu diubah dari netral (new-filter-modal-ui-guide.md #5). --}}
                <label class="rt-field-label" for="inputGudang">Gudang</label>
                <select id="inputGudang" class="rt-native" onchange="updateFilterBadge()">
                  <option value="-" selected>-- Semua Gudang --</option>
                </select>
              </div>
            </div>
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

  <!-- modal pilih barang -->
  <div class="modal-picker-backdrop" id="modalPickMasterBackdrop" onclick="closeBarangPicker()"></div>
  <div class="modal-picker rt-picker-v2" id="modalPickMaster" tabindex="-1" role="dialog" aria-labelledby="modalPickMasterLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPickMasterLabel">Pilih Barang</h5>
          <button type="button" class="btn-close" aria-label="Close" onclick="closeBarangPicker()"></button>
        </div>
        <div class="modal-body">
          <table id="tabelPickMaster">
            <thead>
              <tr>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
              </tr>
            </thead>
            <tbody id="tabelPickMaster_data"></tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeBarangPicker()">Batal</button>
        </div>
      </div>
    </div>
  </div>
  <!-- modal pilih Barang -->

@endsection


@section('jsreport')
{{-- Dua <script> lama di sini dihapus:
     - ajc-browsemaster.js: tidak dipakai (picker halaman ini punya sendiri);
     - report-table.js: sudah dimuat masterreportGudang (baris 233) dan report-table.js
       memang menolak inisialisasi kedua (if (window.ReportTable) return).
     Keduanya juga salah path -- memakai URL::asset('public/js/...') padahal URL::asset
     sudah menunjuk ke folder public, sehingga hasilnya 404. --}}

<script type="text/javascript">
  var modereport_qty = 0, modereport_qtyrp = 1;
  g_modeReport = ("{!! $mode_menu !!}" == "QTY") ? modereport_qty : modereport_qtyrp;

  let globalDate1 = "{!! date('Y-m-d') !!}";
  let globalDate2 = "{!! date('Y-m-d') !!}";

  let lastRows = [];        // hasil query yang sudah dipangkas ke rentang tanggal
  let gRowsShown = null;    // hasil pencarian yang sedang tampil; null = tampil semua

  function rowsShown() { return gRowsShown || lastRows; }

  if (typeof loadingHtml !== 'function') {
    window.loadingHtml = function (msg) {
      return '<span style="display:inline-flex;align-items:center;gap:6px;color:#5A6A85;">' +
             '<i class="fas fa-spinner fa-spin"></i> ' + (msg || 'Memuat...') + '</span>';
    };
  }
  let currentGroupby = 'NoBukti';

  const reportUrl = "{{ url('laporanstockkartustock_doReport') }}";
  const urlListGudang = "{!! $gudang !!}";

  /* ---------- combo Barang di modal Filter (new-filter-modal-ui-guide.md #4) ---------- */

  function htmlEscape(_val) {
    return String(_val)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  function renderPickFields() {
    const val = $('#inputBarang').val() || '-';
    const isSet = (val !== '-' && val !== '');

    let html = '<div>';
    html += '<label class="rt-field-label">Barang</label>';
    html += '<div class="rt-combo">';
    html += '<div class="rt-combo-input" onclick="openBarangPicker(\'\')">';
    if (isSet) {
      html += '<span class="rt-combo-tag">' + htmlEscape(val) +
              '<button type="button" onclick="event.stopPropagation(); clearBarang()">&times;</button></span>';
    } else {
      html += '<span class="rt-combo-placeholder">Pilih barang...</span>';
    }
    html += '<span class="rt-combo-chevron">' +
            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>' +
            '</span>';
    html += '</div></div></div>';

    $('#pickFields').html(html);
  }

  function clearBarang() {
    $('#inputBarang').val('-');
    renderPickFields();
    updateFilterBadge();
  }

  // Badge "N aktif". Gudang punya opsi netral ("-"), jadi ikut dihitung saat diubah;
  // No Satuan dianggap netral di "1" (tidak ada konversi satuan).
  function updateFilterBadge() {
    let count = 0;
    if (($('#inputGudang').val() || '-') !== '-') { count++; }
    const _brg = $('#inputBarang').val();
    if (_brg && _brg !== '-') { count++; }
    if (($('#inputIsi').val() || '1') !== '1') { count++; }
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    $('#inputGudang').val('-');
    $('#inputBarang').val('-');
    $('#inputIsi').val('1');
    renderPickFields();
    updateFilterBadge();
  }

  $('#modalFilter').on('show.bs.modal', function () { renderPickFields(); updateFilterBadge(); });
  $('#modalFilter').on('input', '#inputIsi', updateFilterBadge);

  /* gudang (pakai dropdown) */
  function loadGudangDropdown() {
    $.ajax({
      url: urlListGudang,
      type: 'get',
      success: function (res) {
        const rows = Array.isArray(res) ? res : ((res && res.table) || []);
        let rowSelect = '<option value="-" selected>-- Semua Gudang --</option>';

        rows.forEach(function (item) {
          const kode = item.KODEGDG || item.KodeGdg || item.kode || '';
          const nama = item.NAMA || item.NAMAGDG || item.Namagdg || item.KETERANGAN || item.nama || '';
          if (!kode) return;
          rowSelect += '<option value="' + kode + '">' + kode + (nama ? (' - ' + nama) : '') + '</option>';
        });

        $('#inputGudang').html(rowSelect);
      },
      error: function () {
        console.error('loadGudangDropdown: gagal memuat daftar gudang');
      }
    });
  }

  let barangTableDT = null;
  let barangCacheAll = null;

  function normalizeBarangList(res) {
    if (Array.isArray(res)) {
      return res.map(function (item) {
        return {
          KODEBRG: item.KODEBRG || item.KodeBrg || item.kode || '',
          NAMABRG: item.NAMABRG || item.NamaBrg || item.nama || ''
        };
      });
    }

    const kolom = (res && res.kolom) || [];
    const rows = (res && res.table) || [];
    const codeKey = kolom.length ? kolom[0][0] : 'KODEBRG';
    const nameKey = kolom.length > 1 ? kolom[1][0] : 'NAMABRG';

    return rows.map(function (item) {
      return {
        KODEBRG: item[codeKey] || '',
        NAMABRG: item[nameKey] || ''
      };
    });
  }

  function fetchBarangList(callback) {
    $.ajax({
      url: '{!! $barang !!}',
      type: 'get',
      async: true,
      success: function (res) {
        const list = normalizeBarangList(res);
        if (callback) callback(list);
      },
      error: function (err) {
        console.error('fetchBarangList: gagal memuat daftar barang', err);
      }
    });
  }

  function initBarangTable(list, searchTerm) {
    if ($.fn.DataTable.isDataTable('#tabelPickMaster')) {
      $('#tabelPickMaster').DataTable().clear().destroy();
    }

    barangTableDT = $('#tabelPickMaster').DataTable({
      data: list,
      deferRender: true,
      paging: list.length > 10,
      lengthChange: false,
      searching: true,
      order: [],
      language: { emptyTable: 'Tidak ada data' },
      columns: [
        { data: 'KODEBRG' },
        { data: 'NAMABRG' }
      ],
      createdRow: function (row, data, dataIndex) {
        row.className = 'pick-row';
        row.style.cursor = 'pointer';
        row.setAttribute('onclick', 'buttonPickBarangInsert(' + dataIndex + ')');
      }
    });

    barangTableDT.search(searchTerm || '').draw();
  }

  function openBarangPicker(term) {
    // #modalFilter (modal Bootstrap 4 -- lihat public/js/bootstrap.min.js v4.0.0 yang
    // dimuat newmaster2x; tag CDN Bootstrap 5 di atas masterreportGudang.blade.php tidak
    // pernah ke-render karena ada di luar blok section Blade, jadi bukan itu yang dipakai) tetap
    // "shown" secara teknis di belakang picker ini. Bootstrap 4 punya focus-enforcement
    // (listener focusin.bs.modal di document) yang memaksa fokus balik ke #modalFilter
    // setiap ada elemen DI LUAR #modalFilter yang di-fokus -- termasuk search box
    // DataTables di picker ini, walau tampil di atas (z-index lebih tinggi). Akibatnya
    // klik/ketik di search box seperti tidak berefek. .modal('hide') melepas listener
    // itu secara sinkron; tidak terlihat karena backdrop picker ini sudah menutup modal
    // filter sepenuhnya.
    $('#modalFilter').modal('hide');

    $('#modalPickMasterBackdrop').addClass('show');
    $('#modalPickMaster').addClass('show').attr('aria-hidden', 'false');

    if (barangCacheAll) {
      requestAnimationFrame(function () { initBarangTable(barangCacheAll, term); });
      return;
    }

    $('#tabelPickMaster_data').html('<tr><td colspan="2">' + loadingHtml('Memuat data...') + '</td></tr>');

    fetchBarangList(function (list) {
      barangCacheAll = list;
      requestAnimationFrame(function () { initBarangTable(list, term); });
    });
  }

  function closeBarangPicker() {
    $('#modalPickMaster').removeClass('show').attr('aria-hidden', 'true');
    $('#modalPickMasterBackdrop').removeClass('show');

    // Kembalikan #modalFilter yang di-hide() saat picker dibuka (lihat openBarangPicker).
    $('#modalFilter').modal('show');
  }

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalPickMaster').hasClass('show')) {
      closeBarangPicker();
    }
  });

  // resolveBarang() / onKeyPressBarang() / buttonAddListBarang() dihapus bersama input
  // teks lamanya: field Barang sekarang berupa .rt-combo yang langsung membuka picker.
  // Pencarian dengan mengetik tidak hilang -- kotak search bawaan DataTables di dalam
  // picker mencari kode DAN nama barang.

  function applyBarangSelection(item) {
    $('#inputBarang').val(item.KODEBRG);
    renderPickFields();
    updateFilterBadge();
  }

  function buttonPickBarangInsert(index) {
    const item = barangTableDT ? barangTableDT.row(index).data() : null;
    closeBarangPicker();
    if (item) { applyBarangSelection(item); }
  }

  $(document).ready(function() {
    $("#showTableReport table").hide();
    $(".card-body table").hide();
    $(".btn-success, .btn-danger, #btnSubmitReport, .tombol-toggle").attr('style', 'display: none !important;').hide();

    setDefaultHeader();
    setEmptyColspan();
    loadGudangDropdown();
    renderPickFields();
    updateFilterBadge();

    // Header interaktif hanya untuk mode QTY. Mode QTY+Rp memakai header grouping
    // rowspan/colspan (buildTheadQtyRp) yang tidak bisa diwakili satu <th> per kolom,
    // jadi bar & hint-nya disembunyikan -- sama seperti reportstockmutasistock.
    if (g_modeReport == modereport_qty) {
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: renderCachedReport
      });
    } else {
      $('#rtBar').hide();
      $('.rt-hint').hide();
    }

    // setTimeout(() => {
    //   makeTable('REPORT');
    // }, 100);
  });

  function setEmptyColspan() {
    let n = (g_modeReport == modereport_qty) ? 8 : 12;
    let el = document.getElementById('emptyColspan');
    if (el) el.setAttribute('colspan', n);
  }

  function setDefaultHeader() {
    if (g_modeReport == modereport_qty) {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tipe', 'Tipe', 1, 'varchar', 0, 0],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Sat', 1, 'varchar', 0, 0],
        ['QntDB', 'Masuk', 1, 'float', 1, 2],
        ['QntCR', 'Keluar', 1, 'float', 1, 2],
        ['QntSaldo', 'Saldo', 1, 'sumfloat', 0, 2]
      ];
    } else {
      gcart_header = [
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tipe', 'Tipe', 1, 'varchar', 0, 0],
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
        ['SATUAN', 'Sat', 1, 'varchar', 0, 0],
        ['HPP', 'HPP', 1, 'float', 0, 2],
        ['QntDB', 'Masuk (Qty)', 1, 'float', 1, 2],
        ['HrgDebet', 'Masuk (Rp)', 1, 'float', 1, 2],
        ['QntCR', 'Keluar (Qty)', 1, 'float', 1, 2],
        ['HrgKredit', 'Keluar (Rp)', 1, 'float', 1, 2],
        ['QntSaldo', 'Saldo (Qty)', 1, 'sumfloat', 0, 2],
        ['HrgSaldo', 'Saldo (Rp)', 1, 'sumfloat', 0, 2]
      ];
    }

    gsum_issubtotal = 0;
    gsum_isgrandtotal = 1;
  }

  // applyModalFilter() dihapus: tombol Batal & Terapkan sekarang memakai data-bs-dismiss.
  // Modal ini dibuka lewat data-bs-toggle (Bootstrap 5), jadi menutupnya juga harus lewat
  // data-api Bootstrap 5 -- lihat docs/new-design-all-report-guide.md #5.1.

  // loadInfoHeader() dan buildInfoRows() dihapus: dua baris "Gudang : ..." /
  // "Barang : ... Lokasi : ..." di atas judul kolom sudah tidak dipakai lagi. Ikut hilang
  // pula dua request AJAX SINKRON (async:false) ke functionbrowse_doLoadBarang yang dulu
  // dijalankan tiap kali Tampilkan ditekan dan membekukan UI. Pilihan gudang/barang tetap
  // terlihat sebagai tag di combo modal Filter.

  function buildTheadQty(cols) {
    // Header interaktif (drag / gigi / sembunyikan kolom) -- docs/new-slider-table-guide.md.
    // cols datang dari gcart_header.filter(...) di renderRows(), bukan salinan, supaya
    // ReportTable.headHtml() bisa memetakan tiap kolom balik ke index globalnya.
    return ReportTable.headHtml(cols);
  }

  function buildTheadQtyRp(cols) {
    let _thopen = '<th rowspan="2" class="text-center">', _thclose = '</th>';
    let html = '';

    html += '<tr>';
    html += _thopen + 'Tanggal' + _thclose;
    html += _thopen + 'No. Bukti' + _thclose;
    html += _thopen + 'Tipe' + _thclose;
    html += _thopen + 'Keterangan' + _thclose;
    html += _thopen + 'Sat' + _thclose;
    html += _thopen + 'HPP' + _thclose;
    html += '<th colspan="2" class="text-center">Masuk</th>';
    html += '<th colspan="2" class="text-center">Keluar</th>';
    html += '<th colspan="2" class="text-center">Saldo</th>';
    html += '</tr>';

    html += '<tr>';
    html += '<th class="num">Quantity</th><th class="num">Rupiah</th>';
    html += '<th class="num">Quantity</th><th class="num">Rupiah</th>';
    html += '<th class="num">Quantity</th><th class="num">Rupiah</th>';
    html += '</tr>';

    return html;
  }

  // Ambil "YYYY-MM-DD" dari nilai tanggal apa pun yang dikirim SP. Bentuk ISO ditangani
  // lewat regex (bukan new Date()) supaya tidak tergeser zona waktu.
  function toYmd(_val) {
    if (!_val) { return ''; }
    const s = String(_val);
    const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (m) { return m[1] + '-' + m[2] + '-' + m[3]; }

    const d = new Date(s);
    if (isNaN(d.getTime())) { return ''; }
    return d.getFullYear() + '-' +
           String(d.getMonth() + 1).padStart(2, '0') + '-' +
           String(d.getDate()).padStart(2, '0');
  }

  // Sp_reportkartuStock cuma bisa menyaring per bulan, jadi hasilnya dipangkas ke rentang
  // HARI yang dipilih di sini. Perbandingan string aman karena formatnya YYYY-MM-DD.
  function filterByDateRange(rows) {
    const d1 = globalDate1, d2 = globalDate2;
    if (!d1 && !d2) { return rows; }

    return rows.filter(function (r) {
      const t = toYmd(pickCI(r, 'Tanggal'));
      if (!t) { return true; }            // baris tanpa tanggal tetap ditampilkan
      if (d1 && t < d1) { return false; }
      if (d2 && t > d2) { return false; }
      return true;
    });
  }

  function makeTable(_mode) {
    globalDate1 = $('#inputDate1').val();
    globalDate2 = $('#inputDate2').val();

    setDefaultHeader();
    setEmptyColspan();
    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let filterData = {
      date1       : globalDate1,
      date2       : globalDate2,
      inputGudang : $("#inputGudang").val(),
      inputBarang : $("#inputBarang").val(),
      inputIsi    : $("#inputIsi").val()
    };

    $.ajax({
      url: reportUrl,
      type: 'get',
      data: filterData,
      success: function (res) {
        const raw = Array.isArray(res) ? res : ((res && res.res1) ? res.res1 : []);
        lastRows = filterByDateRange(raw);
        gRowsShown = null;
        $('#searchBox2').val('');
        renderRows(lastRows, currentGroupby);
      },
      error: function () {
        lastRows = [];
        gRowsShown = null;
        renderRows([], currentGroupby);
      }
    });
  }

  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1);
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    const totalCols = cols.filter(c => (c[3] === 'float' || c[3] === 'int' || c[3] === 'sumfloat') && (c[4] === 1 || c[3] === 'sumfloat'));
    const totalKeys = totalCols.map(c => c[0]);
    const hasTotal  = totalCols.length > 0;
    const showSub   = hasTotal && (gsum_issubtotal === 1);
    const showGrand = hasTotal && (gsum_isgrandtotal === 1);

    thead.innerHTML = (g_modeReport == modereport_qty) ? buildTheadQty(cols) : buildTheadQtyRp(cols);

    // report-table.js hanya menandai kolom bertipe "float"/"int" sebagai .num (judul rata
    // kanan); kolom Saldo di sini bertipe "sumfloat", jadi ditandai sendiri. Dipakai
    // data-gidx (index global kolom) yang tidak berubah walau kolom di-drag.
    if (g_modeReport == modereport_qty) {
      gcart_header.forEach(function (c, i) {
        if (c[3] === 'sumfloat') {
          const th = thead.querySelector('th.rt-th[data-gidx="' + i + '"]');
          if (th) { th.classList.add('num'); }
        }
      });
    }

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
        if (type === 'float' || type === 'int' || type === 'sumfloat') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    if (showSub)   html += totalRow('Subtotal', sub, cols, totalKeys, 'subtotal-row');
    if (showGrand) html += totalRow('Total', grand, cols, totalKeys, 'grand-total');

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

    if (!term) {
      gRowsShown = null;
    } else {
      const cols = gcart_header.filter(c => c[2] === 1);
      gRowsShown = lastRows.filter(r => rowSearchText(r, cols).indexOf(term) !== -1);
    }

    renderRows(rowsShown(), currentGroupby);
  }

  // Dipakai sebagai onChange ReportTable saat kolom di-drag / disembunyikan / direset.
  // Hasil pencarian yang sedang aktif ikut dipertahankan.
  function renderCachedReport() {
    if (!lastRows.length) { return; }
    renderRows(rowsShown(), currentGroupby);
  }

  function rowSearchText(r, cols) {
    return cols.map(function (c) {
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

  function getKolomFilter() {
    return ['NoBukti', 'Tanggal'];
  }
</script>

@endsection
