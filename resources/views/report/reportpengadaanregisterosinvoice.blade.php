@extends('report.masterreport2')

<style>
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
  <div class="tb-report main">
      <div class="content">
        {{-- <div class="page-title" style="margin-bottom:8px;">Register Outstanding Invoice</div> --}}

        <!-- TOOLBAR -->
        <div class="toolbar">

          <!-- Periode (date range) -->
          <div class="filter-wrap">
            <label>Periode</label>
            <input type="date" class="filter-inp" id="inputDate1" value="{!! date('Y-m-d') !!}">
            <span class="filter-sep">s/d</span>
            <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
          </div>

          {{-- Search --}}
          <div>
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
          </div>

          {{-- Tidak ada Order By di halaman ini: controller doReport() men-hardcode Ordr="N"
               (tidak pernah membaca inputOrd sama sekali), jadi tidak ada apa pun untuk
               di-switch. Otorisasi juga tidak difilter -- NeedOto di-hardcode ke 2 (Semua). --}}

          <!-- Actions: filter modal + tampilkan + export -->
          <div class="action-group">
            <button class="btn-load" type="button" data-bs-toggle="modal" data-bs-target="#modalFilter" title="Filter Laporan">
              <i class="fas fa-filter"></i> Filter
            </button>
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

        <!-- Bar kolom tersembunyi (diisi oleh report-table.js / ReportTable) -->
        <div id="rtBar"></div>

        <!-- TABLE — header satu tingkat (tanpa band), dibangun oleh ReportTable.headHtml() di
             renderRows() (drag-reorder + gear aktif seperti biasa). -->
        <div class="table-outer">
          <div class="table-wrap">
            <table class="tb" id="mainTable">
              <thead>
                <tr>
                  <th style="min-width:130px">No. Bukti</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:110px">No. PO</th>
                  <th style="min-width:150px">Nama Cust Supp</th>
                  <th style="min-width:110px">No. FPJ</th>
                  <th style="min-width:90px">Tgl. FPJ</th>
                  <th class="num" style="min-width:10px">DPP</th>
                  <th class="num" style="min-width:10px">PPN</th>
                  <th class="num" style="min-width:10px">Net</th>
                  <th class="num" style="min-width:10px">Outstanding</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr class="empty-row"><td colspan="10">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
              </tbody>
            </table>
          </div>
          <div class="table-footer">
            <span id="footerLabel">Belum ada data dimuat</span>
          </div>
        </div>

        <div class="rt-hint">
          <i class="bi bi-info-circle"></i>
          Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> untuk sembunyikan kolom atau atur total.
        </div>

      </div><!-- /content -->

      <!-- TOAST -->
      <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

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
          <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="rt-section">
            <div class="rt-group-label">Filter Data</div>
            <div class="rt-grid-1">
              {{-- "Report" (Detail/Rekap) DIHAPUS: file lama meng-copy toggle ini dari halaman
                   Rekap Invoice Pembelian, tapi doReport() controller halaman ini (Sp_OUTreportInvoicedet)
                   tidak pernah membaca inputDetOrRekap sama sekali -- toggle tidak mengubah apa pun.
                   PPN dihidupkan sebagai gantinya: controller genuinely membaca inputPpn, tapi
                   sebelumnya tidak ada UI ataupun payload untuk itu sama sekali. --}}
              <div class="mb-3">
                <label class="rt-field-label">PPN</label>
                <select class="rt-native" id="modalPPN">
                  <option value="2">Semua</option>
                  <option value="1">Non PPN</option>
                  <option value="0">PPN</option>
                </select>
              </div>
            </div>
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
  <!-- modal filter -->

@endsection

@section('jsreport')
<script type="text/javascript">
  let globalPPN = "2";       // default: Semua
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
      // tersembunyi"/"Reset kolom"). Tidak ada "Tampilan" switcher di halaman ini -- hanya satu
      // mode/urutan yang genuinely ada (lihat catatan di makeTable()).
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () { applyFilters(); }
      });
  });

  /* -- FILTER MODAL -- */
  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalPPN").val(globalPPN);
    updateFilterBadge();
  });

  // PPN punya "Semua" (value "2") sebagai nilai netral, jadi ikut dihitung ke badge saat
  // diubah dari situ.
  function updateFilterBadge() {
    let count = 0;
    if ($("#modalPPN").val() !== "2") count++;
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    $("#modalPPN").val("2");
    updateFilterBadge();
  }

  function applyModalFilter() {
    globalPPN = $("#modalPPN").val();
    $('#modalFilter').modal('hide');
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  g_modeReport = 0;

  function setDefaultHeader() {
    gcart_header = [
      ['NoBukti', 'Nobukti', 1, 'varchar', 0, 0],
      ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
      ['NOPO', 'No. PO', 1, 'varchar', 0, 0],
      ['NAMACUSTSUPP', 'Nama Cust Supp', 1, 'varchar', 0, 0],
      ['NoFakturPajak', 'No. FPJ', 1, 'varchar', 0, 0],
      ['TglFakturPajak', 'Tgl. FPJ', 1, 'date', 0, 0],
      ['NDPP', 'DPP', 1, 'float', 1, 2],
      ['NPPN', 'PPN', 1, 'float', 1, 2],
      ['NNET', 'Net', 1, 'float', 1, 2],
      // NAMA FIELD TEBAKAN -- Sp_OUTreportInvoicedet belum bisa diverifikasi dari kode (proc
      // ada di SQL Server, bukan repo ini). Setelah dites di browser, cocokkan 'OUTSTANDING' di
      // bawah ini dengan nama field yang benar-benar dikembalikan proc (lihat tab Network).
      ['OUTSTANDING', 'Outstanding', 1, 'float', 1, 2]
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  const reportUrl = "{{ url('laporanpengadaanregisteroutstandinginvoice_doReport') }}"
  function makeTable(_mode) {
    let groupby = 'NoBukti';
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let data = {
      date1: _date1,
      date2: _date2,
      inputPpn: globalPPN,
    };

    // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
    $.ajax({
      url    : reportUrl,
      type   : 'get',
      data   : data,
      success: function (res) {
        lastRows = res || [];
        currentGroupby = groupby;        // simpan utk render ulang saat search
        $('#searchBox2').val('');        // reset kotak cari tiap muat data baru
        renderRows(lastRows, groupby);   // <-- render ke .tb-report #tableBody
      },
      error  : function () {
        lastRows = [];
        currentGroupby = groupby;
        renderRows([], groupby);
      }
    });
  }

  // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
  // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
  // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
  // (show/hide + urutan kolom) langsung tampil. <thead> ditulis ulang tiap
  // render lewat ReportTable.headHtml() (drag-reorder + gear per kolom).
  // Subtotal/Grand Total = jumlah kolom DPP/PPN/Net/Outstanding, dikelompokkan per NoBukti.
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const qntVisible = cols.some(c => c[0] === 'NDPP');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku.
    const showSub   = qntVisible && (gsum_issubtotal === 1);
    const showGrand = qntVisible && (gsum_isgrandtotal === 1);

    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null,
        sub   = { NDPP: 0, NPPN: 0, NNET: 0, OUTSTANDING: 0 },
        grand = { NDPP: 0, NPPN: 0, NNET: 0, OUTSTANDING: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
        sub = { NDPP: 0, NPPN: 0, NNET: 0, OUTSTANDING: 0 };
      }

      sub.NDPP += currencyNormalizer(r.NDPP);
      sub.NPPN += currencyNormalizer(r.NPPN);
      sub.NNET += currencyNormalizer(r.NNET);
      sub.OUTSTANDING += currencyNormalizer(r.OUTSTANDING);

      grand.NDPP += currencyNormalizer(r.NDPP);
      grand.NPPN += currencyNormalizer(r.NPPN);
      grand.NNET += currencyNormalizer(r.NNET);
      grand.OUTSTANDING += currencyNormalizer(r.OUTSTANDING);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (type === 'date') return '<td>' + format_date(r[key]) + '</td>';
        if (type === 'float' || type === 'int') return '<td class="num">' + format_number(currencyNormalizer(r[key]), c[5]) + '</td>';
        return '<td>' + nullToEmpty(r[key]) + '</td>';
      }).join('') + '</tr>';

      prev = now;
    });

    // subtotal grup terakhir + grand total   mengikuti toggle di modal
    if (showSub)   html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
    if (showGrand) html += totalRowTotal('GRAND TOTAL', grand, cols, 'grand-total');

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent = 'Menampilkan ' + rows.length + ' baris';
  }

  // Baris total: nilai di kolomnya masing-masing, label di kolom pertama non-total, sel lain
  // dikosongkan   mengikuti urutan kolom terlihat saat ini.
  function totalRowTotal(label, total, cols, cls) {
    const totalKeys = ['NDPP', 'NPPN', 'NNET', 'OUTSTANDING'];
    const labelIdx = cols.findIndex(c => !totalKeys.includes(c[0]));

    const tds = cols.map(function(c, idx) {
        if (totalKeys.includes(c[0]))
            return '<td class="num">' + format_number(total[c[0]], 2) + '</td>';
        if (idx === labelIdx)
            return '<td>' + label + '</td>';
        return '<td></td>';
    });

    return '<tr class="' + cls + '">' + tds.join('') + '</tr>';
  }

  // === PENCARIAN SISI-KLIEN ===
  // Menyaring data yang SUDAH dimuat (lastRows) berdasarkan teks pencarian,
  // dicocokkan ke semua kolom yang sedang terlihat, lalu render ulang tabel
  // styled (renderRows menghitung ulang subtotal/grand total untuk hasil saring).
  function applyFilters() {
    if (!lastRows.length) return;        // belum ada data dimuat

    const term = ($('#searchBox2').val() || '').trim().toLowerCase();
    if (!term) { renderRows(lastRows, currentGroupby); return; }   // kosong -> tampilkan semua

    const cols = gcart_header.filter(c => c[2] === 1); // kolom yang terlihat
    const filtered = lastRows.filter(function (r) {
      return rowSearchText(r, cols).indexOf(term) !== -1;
    });

    renderRows(filtered, currentGroupby);
  }

  // Gabungan teks satu baris dari kolom terlihat (tanggal pakai format tampil
  // dd/mm/yyyy) supaya pencarian cocok dengan apa yang user lihat di tabel.
  function rowSearchText(r, cols) {
    return cols.map(function (c) {
      const v = r[c[0]];
      if (c[3] === 'date') return format_date(v);
      return (v == null ? '' : String(v));
    }).join(' ').toLowerCase();
  }

  // getKolomFilter() milik ENGINE LAMA (modal "Filter Data" / doShowFormFilterData()), yang
  // TIDAK dipakai lagi di halaman ini (tombolnya sudah dihapus dari toolbar). Stub ini cuma
  // jaga-jaga supaya base script masterreport2 tidak error kalau memanggilnya.
  function getKolomFilter() { return []; }
</script>

@endsection
