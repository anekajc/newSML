@extends('report.masterreport2')

<style>
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
  <div class="tb-report main">
      <div class="content">
        {{-- <div class="page-title" style="margin-bottom:8px;">Debet Note</div> --}}

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

          {{-- Order By (No Bukti/Supplier) jadi "Tampilan" switcher di bar tabel (diisi
               ReportTable.init({ views: ... })) -- dropdown lama di sini (sudah di-comment
               total, dan sebelumnya TIDAK ada penggantinya sama sekali) dihidupkan lagi di situ,
               bukan dihapus: inputOrd genuinely dikonsumsi Sp_reportDebetnoteDet. --}}

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

        <!-- Bar kolom tersembunyi + Tampilan (Order By) (diisi oleh report-table.js / ReportTable) -->
        <div id="rtBar"></div>

        <!-- TABLE — header satu tingkat (tanpa band), dibangun oleh ReportTable.headHtml() di
             renderRows() (drag-reorder + gear aktif seperti biasa). -->
        <div class="table-outer">
          <div class="table-wrap">
            <table class="tb" id="mainTable">
              <thead>
                <tr>
                  <th style="min-width:130px">No. Bukti</th>
                  <th style="min-width:130px">No. Invoice</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:110px">Kode Supplier</th>
                  <th style="min-width:130px">Nama Supplier</th>
                  <th class="num" style="min-width:10px">Nilai</th>
                  <th style="min-width:70px">VLS</th>
                  <th style="min-width:70px">Kurs</th>
                  <th class="num" style="min-width:10px">DPP</th>
                  <th style="min-width:100px">Otorisasi</th>
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
            {{-- <div class="rt-group-label">Filter Data</div> --}}
            <div class="rt-grid-1">
              <div class="mb-3">
                <label class="rt-field-label">Otorisasi</label>
                <select class="rt-native" id="modalOtorisasi">
                  <option value="2">Semua</option>
                  <option value="1">Belum Otorisasi</option>
                  <option value="0">Sudah Otorisasi</option>
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
  let globalOtorisasi = "2"; // default: Semua
  let globalOrderBy = "N";   // default: Nomor Bukti
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      setOrderBy(globalOrderBy);

      // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
      // tersembunyi"/"Reset kolom"), plus "Tampilan" switcher untuk Order By.
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () { applyFilters(); },
        views: {
          label: 'Order By',
          options: [
            { value: 'N', label: 'No Bukti',  desc: 'Dikelompokkan per No Bukti' },
            { value: 'S', label: 'Supplier',  desc: 'Dikelompokkan per Nama Supplier' }
          ],
          get: function () { return globalOrderBy; },
          set: function (v) {
            setOrderBy(String(v));
            if (lastRows.length) { makeTable('REPORT'); }
          }
        }
      });
  });

  /* -- FILTER MODAL -- */
  $('#modalFilter').on('show.bs.modal', function () {
    $("#modalOtorisasi").val(globalOtorisasi);
    updateFilterBadge();
  });

  // Otorisasi punya "Semua" (value "2") sebagai nilai netral, jadi ikut dihitung ke badge saat
  // diubah dari situ.
  function updateFilterBadge() {
    let count = 0;
    if ($("#modalOtorisasi").val() !== "2") count++;
    $('#filterBadge').text(count + ' aktif');
  }

  function resetAllFilters() {
    $("#modalOtorisasi").val("2");
    updateFilterBadge();
  }

  function applyModalFilter() {
    globalOtorisasi = $("#modalOtorisasi").val();
    $('#modalFilter').modal('hide');
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  // order by (nilai sebenarnya -- UI-nya "Tampilan" switcher di #rtBar, lihat ReportTable.init() di atas)
  function setOrderBy(val) {
    globalOrderBy = val;
  }

  g_modeReport = 0;

  function setDefaultHeader() {
    // File lama punya cabang "modereport_detail"/"modereport_rekap" yang 100% identik kolomnya
    // DAN tidak pernah dipicu UI apa pun (tidak ada toggle Detail/Rekap sama sekali di halaman
    // ini) -- disederhanakan jadi satu definisi. Order (N/S) hanya mengubah urutan/grouping
    // data dari proc, bukan kolom yang ditampilkan.
    gcart_header = [
      ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
      ['NoInv', 'No. Invoice', 1, 'varchar', 0, 0],
      ['tanggal', 'Tanggal', 1, 'date', 0, 0],
      ['kodecustsupp', 'Kode Supplier', 1, 'varchar', 0, 0],
      ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
      ['NDPP', 'NilaiLS', 1, 'float', 1, 2],
      ['KodeVLS', 'VLS', 1, 'varchar', 0, 0],
      ['Kurs', 'Kurs', 1, 'varchar', 0, 0],
      ['NDPPRP', 'DPP', 1, 'float', 1, 2],
      ['NeedOtorisasi', 'Otorisasi', 1, 'varchar', 0, 0]
    ];
    gsum_issubtotal = 0; gsum_isgrandtotal = 1;
  }

  const reportUrl = "{{ url('laporanpengadaandebetnote_doReport') }}"
  function makeTable(_mode) {
    let groupby = '';
    let _date1 = $("#inputDate1").val();
    let _date2 = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      groupby = 'NoBukti';
    } else {
      // Sebelumnya cabang ini memakai variabel g_modeReport ('modereport_customer') yang TIDAK
      // PERNAH dideklarasikan di mana pun -- ReferenceError setiap kali Order By = Supplier
      // dipilih lalu Tampilkan diklik, jadi mode ini selalu gagal total. Groupby juga masih
      // 'NoBukti' (salin-tempel dari cabang No Bukti); dikoreksi ke NAMACUSTSUPP supaya subtotal
      // (jika diaktifkan) pecah per Supplier, sesuai urutan yang dipilih.
      groupby = 'NAMACUSTSUPP';
    }

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let data = {
      date1: _date1,
      date2: _date2,
      inputOto: globalOtorisasi,
      inputOrd: input_order,
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
  // Subtotal/Grand Total = jumlah kolom Nilai VLS/DPP, dikelompokkan per `groupby`.
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const qntVisible = cols.some(c => c[0] === 'NDPP');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom Nilai VLS ada.
    const showSub   = qntVisible && (gsum_issubtotal === 1);
    const showGrand = qntVisible && (gsum_isgrandtotal === 1);

    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { NDPP: 0, NDPPRP: 0 }, grand = { NDPP: 0, NDPPRP: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
        sub = { NDPP: 0, NDPPRP: 0 };
      }

      sub.NDPP   += currencyNormalizer(r.NDPP);
      sub.NDPPRP += currencyNormalizer(r.NDPPRP);

      grand.NDPP   += currencyNormalizer(r.NDPP);
      grand.NDPPRP += currencyNormalizer(r.NDPPRP);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5])
      html += '<tr class="data-row">' + cols.map(function (c) {
        const key = c[0], type = c[3];
        // Status Otorisasi
        if (key === 'NeedOtorisasi') {
          return `<td> ${r.NeedOtorisasi == 1 ? '<span class="sp-badge is-inactive">Belum</span>' : '<span class="sp-badge is-active">Sudah</span>'} </td>`;
        }

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
    const totalKeys = ['NDPP', 'NDPPRP'];
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
