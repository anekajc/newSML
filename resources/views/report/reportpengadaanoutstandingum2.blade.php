@extends('report.masterreport2')

<style>
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
  <div class="tb-report main">
      <div class="content">
        {{-- <div class="page-title" style="margin-bottom:8px;">Outstanding Uang Muka Beli 2</div> --}}

        <!-- TOOLBAR -->
        <div class="toolbar">

          {{-- Cuma satu tanggal ("per tanggal"), bukan rentang: doReport() controller hanya
               membaca date2 (dipakai sebagai SPReportOutUMN's satu-satunya parameter) -- ini
               genuinely laporan snapshot outstanding per tanggal tertentu, bukan bug. Tidak ada
               Otorisasi ataupun Order By: controller tidak pernah membaca inputOto/inputOrd sama
               sekali, jadi tidak ada apa pun untuk difilter/di-switch -- tombol Filter juga
               sengaja tidak diikutsertakan. --}}
          <div class="filter-wrap">
            <label>Per Tanggal</label>
            <input type="date" class="filter-inp" id="inputDate2" value="{!! date('Y-m-d') !!}">
          </div>

          {{-- Search --}}
          <div>
            <input class="search-inp" type="text" id="searchBox2" placeholder="Cari data..." oninput="applyFilters()" style="width:180px">
          </div>

          <!-- Actions: tampilkan + export -->
          <div class="action-group">
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
                  <th style="min-width:90px">Tgl BBK</th>
                  <th style="min-width:130px">No BBK</th>
                  <th style="min-width:110px">No UM</th>
                  <th style="min-width:150px">Supplier</th>
                  <th style="min-width:110px">No. PO</th>
                  <th class="num" style="min-width:10px">Rp. UM</th>
                  <th class="num" style="min-width:10px">Bayar</th>
                  <th class="num" style="min-width:10px">Sisa</th>
                </tr>
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
          Seret judul kolom untuk mengurutkan. Klik <i class="bi bi-gear"></i> untuk sembunyikan kolom atau atur total.
        </div>

      </div><!-- /content -->

      <!-- TOAST -->
      <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>
    </div><!-- /tb-report -->

@endsection

@section('jsreport')
<script type="text/javascript">
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
      // tersembunyi"/"Reset kolom"). Tidak ada "Tampilan" switcher di halaman ini -- tidak ada
      // Order By yang genuinely dikonsumsi controller.
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () { applyFilters(); }
      });
  });

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  g_modeReport = 0;

  function setDefaultHeader() {
    gcart_header = [
      ['tanggal', 'Tgl BBK', 1, 'date', 0, 0],
      ['NoBukti', 'No BBK', 1, 'varchar', 0, 0],
      ['NoFaktur', 'No UM', 1, 'varchar', 0, 0],
      ['NAMACUSTSUPP', 'Supplier', 1, 'varchar', 0, 0],
      ['NOSO', 'No. PO', 1, 'varchar', 0, 0],
      ['dpp', 'Rp. UM', 1, 'float', 1, 2],
      ['bayar', 'Bayar', 1, 'float', 1, 2],
      ['sisa', 'Sisa', 1, 'float', 1, 2]
    ];
    gsum_issubtotal = 0; gsum_isgrandtotal = 1;
  }

  const reportUrl = "{{ url('laporanpengadaanoutstandingum2_doReport') }}"

  // Bottom voucher panel endpoint (report-table.js, loaded via masterreport2). No BBK
  // (NoBukti) selalu bertipe Bukti Kas/Bank (BBK/BKK/dst -- CetakKasharian), jadi hanya
  // kasUrl yang dibutuhkan; invoiceUrl/lpbUrl/bpUrl tidak pernah dipakai halaman ini.
  window.ReportTableConfig = {
    kasUrl: "{{ url('laporanpengadaanoutstandingum2_doKasharian') }}"
  };

  function makeTable(_mode) {
    let groupby = 'NoBukti';
    let _date2 = $("#inputDate2").val();

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let data = {
      date2: _date2,
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

  // No BBK cell: klikable hanya untuk nomor voucher betulan (ada '/', bukan baris
  // pembuka "Saldo Awal"). Hanya diterapkan ke kolom NoBukti (No BBK) -- No UM
  // (NoFaktur) TIDAK dibuat klikable, sesuai permintaan.
  function str(v) { return (v == null ? '' : String(v)).trim(); }
  function isVoucherNo(v) {
    const s = str(v);
    if (!s || s.indexOf('/') === -1) return false;
    return s.toUpperCase().indexOf('SALDO AWAL') === -1;
  }
  // Kolom No BBK hanya PETUNJUK visual (garis bawah biru); klik dilakukan pada SELURUH
  // baris (lihat voucherRowOpen) karena hanya ada satu kolom yang bisa diklik.
  function voucherCell(v) {
    const s = str(v);
    if (!isVoucherNo(s)) return '<td>' + nullToEmpty(v) + '</td>';
    return '<td class="kas-clickable" style="color:#0d6efd;text-decoration:underline">' + nullToEmpty(v) + '</td>';
  }
  // Tag <tr> pembuka baris data: bila NoBukti memuat nomor voucher betulan, SELURUH
  // baris bisa diklik untuk membuka voucher (report-table.js).
  function voucherRowOpen(v, cls) {
    const s = str(v);
    if (!isVoucherNo(s)) return '<tr class="' + cls + '">';
    const jn  = (typeof jenisFromNo === 'function') ? jenisFromNo(s) : '';
    const ttl = (typeof jenisTitle === 'function') ? jenisTitle(jn) : 'Voucher';
    const esc = s.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    const jsc = String(jn).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    return '<tr class="' + cls + '" title="Klik untuk lihat ' + ttl + ' ' + s + '" ' +
           'onclick="openVoucher(\'' + esc + '\',\'' + jsc + '\')">';
  }

  // === RENDER KE TABEL STYLED (.tb-report #mainTable) ===
  // Kolom dibangun DINAMIS dari gcart_header (hanya kolom yang terlihat /
  // item[2]===1, sesuai urutan simpanan). Jadi hasil "Customize Table"
  // (show/hide + urutan kolom) langsung tampil. <thead> ditulis ulang tiap
  // render lewat ReportTable.headHtml() (drag-reorder + gear per kolom).
  // Subtotal (nonaktif default) / Grand Total = jumlah kolom Rp. UM/Bayar/Sisa,
  // dikelompokkan per NoBukti.
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const qntVisible = cols.some(c => c[0] === 'dpp');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom Rp. UM ada.
    const showSub   = qntVisible && (gsum_issubtotal === 1);
    const showGrand = qntVisible && (gsum_isgrandtotal === 1);

    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { dpp: 0, bayar: 0, sisa: 0 }, grand = { dpp: 0, bayar: 0, sisa: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
        sub = { dpp: 0, bayar: 0, sisa: 0 };
      }

      sub.dpp   += currencyNormalizer(r.dpp);
      sub.bayar += currencyNormalizer(r.bayar);
      sub.sisa  += currencyNormalizer(r.sisa);

      grand.dpp   += currencyNormalizer(r.dpp);
      grand.bayar += currencyNormalizer(r.bayar);
      grand.sisa  += currencyNormalizer(r.sisa);

      // satu sel per kolom terlihat, format menurut tipe (item[3]) & desimal (item[5]).
      // Baris ini klikable-voucher jika NoBukti adalah nomor voucher betulan (lihat
      // voucherRowOpen/voucherCell di atas) -- satu-satunya kolom klikable di halaman ini.
      html += voucherRowOpen(r.NoBukti, 'data-row') + cols.map(function (c) {
        const key = c[0], type = c[3];
        if (key === 'NoBukti') return voucherCell(r[key]);
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
    const totalKeys = ['dpp', 'bayar', 'sisa'];
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
