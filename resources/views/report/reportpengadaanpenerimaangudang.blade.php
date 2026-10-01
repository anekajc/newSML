@extends('report.masterreport2')

<style>
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')
  <div class="tb-report main">
      <div class="content">
        {{-- <div class="page-title" style="margin-bottom:8px;">Penerimaan Gudang</div> --}}

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

          {{-- Order By (No Bukti/Barang/Supplier/Gudang) jadi "Tampilan" switcher di bar tabel
               (diisi ReportTable.init({ views: ... }), lihat docs/new-slider-table-guide.md
               §Step 5) -- dropdown lama di sini (sudah di-comment total) dihapus. inputOrd
               genuinely dikonsumsi Sp_reportBeliGudangdet (dicek di controller), jadi ini
               dihidupkan lagi, bukan dihapus. --}}

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
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:110px">No PO</th>
                  <th style="min-width:130px">Nama Supplier</th>
                  <th style="min-width:110px">Merk</th>
                  <th style="min-width:130px">Nama Barang</th>
                  <th style="min-width:110px">PO Customer</th>
                  <th style="min-width:130px">Nama Customer</th>
                  <th class="num" style="min-width:10px">QTY</th>
                  <th style="min-width:80px">Satuan</th>
                  <th style="min-width:110px">Lokasi</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <tr class="empty-row"><td colspan="11">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
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
  let globalOrderBy = "N";   // default: Nomor Bukti
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      setOrderBy(globalOrderBy);

      // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
      // tersembunyi"/"Reset kolom"), plus "Tampilan" switcher untuk Order By -- halaman ini
      // SUDAH punya mode yang menukar susunan kolom (g_modeReport/setDefaultHeader(), lihat
      // makeTable()); dropdown lamanya (sudah di-comment total di markup asli) sekarang jadi
      // switcher ini. Order By menukar urutan sortir dari SP (inputOrd, dipakai deteksi
      // pergantian grup di renderRows()) -- makanya set() di bawah memanggil makeTable('REPORT').
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () { applyFilters(); },
        views: {
          label: 'Order By',
          options: [
            { value: 'N', label: 'No Bukti',  desc: 'Dikelompokkan per No Bukti' },
            { value: 'B', label: 'Barang',    desc: 'Dikelompokkan per Kode Barang' },
            { value: 'S', label: 'Supplier',  desc: 'Dikelompokkan per Nama Supplier' },
            { value: 'G', label: 'Gudang',    desc: 'Dikelompokkan per Kode Gudang' }
          ],
          get: function () { return globalOrderBy; },
          set: function (v) {
            setOrderBy(String(v));
            if (lastRows.length) { makeTable('REPORT'); }
          }
        }
      });
  });

  // order by (nilai sebenarnya -- UI-nya "Tampilan" switcher di #rtBar, lihat ReportTable.init() di atas)
  function setOrderBy(val) {
    globalOrderBy = val;
  }

  /* -- EXPORT -- */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });

  var modereport_detailnobukti = 0, modereport_detailbarang = 1, modereport_detailcustomer = 2, modereport_detailgudang = 3;
  g_modeReport = modereport_detailnobukti;

  // VwReportBeliGudang (lewat Sp_reportBeliGudangdet) mengembalikan sepasang qty+satuan
  // (qnt/satuan, Qnt2/satuan2) + NOSAT -- NOSAT=1 -> pasangan pertama, 2 atau 3 -> kedua
  // (dikonfirmasi dari data nyata, sama pola dengan reportlaporanmarketingspb.blade.php,
  // reportmarketingso.blade.php & reportlaporanmarketingspbhrgso.blade.php). Beda dengan
  // ketiga halaman itu: qnt/Qnt2 di sini TETAP dijumlahkan di Subtotal/Grand Total (lihat
  // renderRows()) -- itu satu-satunya kolom angka di laporan ini, jadi baris Total akan
  // kosong kalau QTY tidak dijumlahkan. Dihitung sekali di sini (bukan di renderRows())
  // supaya render, subtotal, dan pencarian semua membaca field yang sama.
  function decorateRows(rows) {
    (rows || []).forEach(function (r) {
      const nosat = String(r.NOSAT);
      const pakaiKedua = (nosat === '2' || nosat === '3');
      r.QTY = pakaiKedua ? r.Qnt2 : r.qnt;
      r.Satuan = pakaiKedua ? r.satuan2 : r.satuan;
    });
    return rows || [];
  }

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['KodeGdg', 'Kode Gudang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QTY', 'QTY', 1, 'float', 1, 2],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else if (g_modeReport == modereport_detailbarang){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['KodeGdg', 'Kode Gudang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QTY', 'QTY', 1, 'float', 1, 2],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else if(g_modeReport == modereport_detailcustomer){
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['KodeGdg', 'Kode Gudang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QTY', 'QTY', 1, 'float', 1, 2],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;

    } else {
      gcart_header = [
        ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
        ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
        ['NoPO', 'No PO', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
        ['KodeBrg', 'Kode Barang', 1, 'varchar', 0, 0],
        ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
        ['KodeGdg', 'Kode Gudang', 1, 'varchar', 0, 0],
        ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
        ['QTY', 'QTY', 1, 'float', 1, 2],
        ['KODELOKASI', 'Lokasi', 1, 'varchar', 0, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 0;
    }
  }

  const reportUrl = "{{ url('laporanpenerimaangudang_doReport') }}"
  function makeTable(_mode) {
    let groupby = '';
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      g_modeReport = modereport_detailnobukti;
      groupby = 'NoBukti';
    } else if (input_order == "B") {
      g_modeReport = modereport_detailbarang;
      groupby = 'PartNumber';
    } else if (input_order == "S") {
      g_modeReport = modereport_detailcustomer;
      groupby = 'NAMACUSTSUPP';
    } else {
      // Gudang -- sebelumnya g_modeReport tidak pernah di-set di sini (bug: dropdown lama
      // sudah dihapus total sehingga cabang ini tidak pernah tersentuh), jadi kolom yang
      // tampil mengikuti mode terakhir yang aktif, bukan bentuk khusus Gudang. Dikelompokkan
      // per KodeGdg (Kode Gudang), BUKAN KODELOKASI (Lokasi) -- keduanya kolom berbeda; opsi
      // ini bernama "Gudang" jadi harus subtotal per gudang, bukan per lokasi di dalamnya.
      g_modeReport = modereport_detailgudang;
      groupby = 'KodeGdg';
    }

    setDefaultHeader();
    if (typeof doSetHeader === 'function') {
      doSetHeader(g_modeReport);
    }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    let data = {
      date1    : _date1,
      date2    : _date2,
      inputOrd : input_order,
    };

    // Ambil data SEKALI, lalu render langsung ke tabel styled baru (#tableBody).
    $.ajax({
      url    : reportUrl,
      type   : 'get',
      data   : data,
      success: function (res) {
        lastRows = decorateRows(res || []);
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
  // Subtotal/Grand Total = jumlah kolom QTY (hasil gabungan qnt/Qnt2 oleh
  // decorateRows(), lihat komentar di sana), dikelompokkan per `groupby`.
  // (Data sudah terurut dari proc sesuai inputOrd, jadi cukup deteksi
  // pergantian nilai grup. Jika kolom QTY disembunyikan, baris total tidak
  // ditampilkan.)
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const qntVisible = cols.some(c => c[0] === 'QTY');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila kolom QTY ada.
    const showSub   = qntVisible && (gsum_issubtotal === 1);
    const showGrand = qntVisible && (gsum_isgrandtotal === 1);

    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { QTY: 0 }, grand = { QTY: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
        sub = { QTY: 0 };
      }

      sub.QTY  += currencyNormalizer(r.QTY);
      grand.QTY += currencyNormalizer(r.QTY);

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

  // Baris total (QTY, satu-satunya kolom angka di laporan ini): nilai di kolomnya, label
  // di kolom pertama non-total, sel lain dikosongkan mengikuti urutan kolom terlihat saat ini.
  function totalRowTotal(label, total, cols, cls) {
    const totalKeys = ['QTY'];
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
