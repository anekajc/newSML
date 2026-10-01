@extends('report.masterreport2')

<style>
  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }
</style>

@section('header2')

<div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR -->
        <div class="toolbar">

          <!-- Periode (date range) -- dikonsumsi Sp_ReportPODetClose (tgl1/tgl2) -->
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

          {{-- Order By (No Bukti/Barang) jadi "Tampilan" switcher di bar tabel (diisi
               ReportTable.init({ views: ... }), lihat docs/new-slider-table-guide.md §Step 5) --
               dropdown lama di sini sudah dihapus. --}}

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
                  <th style="min-width:130px">No Bukti</th>
                  <th style="min-width:90px">Tanggal</th>
                  <th style="min-width:150px">Nama Supplier</th>
                  <th style="min-width:150px">Nama Barang</th>
                  <th style="min-width:80px">Satuan</th>
                  <th class="num" style="min-width:10px">Qnt</th>
                  <th class="num" style="min-width:10px">Qnt2</th>
                  <th style="min-width:150px">Keterangan batal</th>
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
  let globalOrderBy = "N";   // default: Nomor Bukti
  let lastRows = [];         // hasil fetch terakhir (dipakai renderRows / export / search)
  let currentGroupby = 'NoBukti'; // groupby aktif untuk render ulang saat search

  $(document).ready(function () {
      setOrderBy(globalOrderBy);

      // Header tabel interaktif standar (drag-reorder + gear per kolom + bar "kolom
      // tersembunyi"/"Reset kolom"), plus "Tampilan" switcher untuk Order By -- halaman ini
      // SUDAH punya mode yang menukar grouping (g_modeReport/setDefaultHeader(), lihat
      // makeTable()); dropdown lamanya sekarang jadi switcher ini. Order By menukar urutan
      // sortir dari SP (inputOrd, dipakai deteksi pergantian grup di renderRows()) -- makanya
      // set() di bawah memanggil makeTable('REPORT'), bukan render().
      ReportTable.init({
        table: '#mainTable',
        bar: '#rtBar',
        onChange: function () { applyFilters(); },
        views: {
          label: 'Order By',
          options: [
            { value: 'N', label: 'No Bukti', desc: 'Dikelompokkan per No Bukti' },
            { value: 'B', label: 'Barang',   desc: 'Dikelompokkan per Nama Barang' }
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

  var modereport_detail = 0, modereport_rekap = 1;
  g_modeReport = modereport_detail;

  function setDefaultHeader() {
    gcart_header = [
      ['NoBukti', 'No Bukti', 1, 'varchar', 0, 0],
      ['TANGGAL', 'Tanggal', 1, 'date', 0, 0],
      ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
      ['NamaBrg', 'Nama Barang', 1, 'varchar', 0, 0],
      ['Satuan', 'Satuan', 1, 'varchar', 0, 0],
      ['Qnt', 'Qnt', 1, 'float', 1, 2],
      ['QntBatal', 'Qnt2', 1, 'float', 1, 2],
      ['Ketbatal', 'Keterangan batal', 1, 'varchar', 0, 0]
    ];
    gsum_issubtotal = 1; gsum_isgrandtotal = 0;
  }

  const reportUrl = "{{ url('laporanpurchaseorderclosepo_doReport') }}"
  function makeTable(_mode) {
    let groupby = '';
    let _date1  = $("#inputDate1").val();
    let _date2  = $("#inputDate2").val();
    let input_order = globalOrderBy;

    if (input_order == "N") {
      g_modeReport = modereport_detail;
      groupby = 'NoBukti';
    } else if (input_order == "B") {
      g_modeReport = modereport_rekap;
      groupby = 'NamaBrg';
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
  // render. Subtotal/Grand Total = jumlah kolom Qnt & QntBatal, dikelompokkan
  // per `groupby`. (Data sudah terurut dari proc sesuai inputOrd, jadi cukup
  // deteksi pergantian nilai grup.)
  function renderRows(rows, groupby) {
    const cols  = gcart_header.filter(c => c[2] === 1); // kolom terlihat, terurut
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');
    const totalVisible = cols.some(c => c[0] === 'Qnt' || c[0] === 'QntBatal');
    // Baris Subtotal & Grand Total mengikuti toggle di modal Customize Table
    // (#buttonSubtotal -> gsum_issubtotal, #buttonGrandtotal -> gsum_isgrandtotal).
    // gsum_* dimuat oleh doSetHeader() saat klik Tampilkan, jadi pilihan user
    // (sudah tersimpan) langsung berlaku. Total hanya tampil bila Qnt/QntBatal ada.
    const showSub   = totalVisible && (gsum_issubtotal === 1);
    const showGrand = totalVisible && (gsum_isgrandtotal === 1);

    // HEADER dinamis dari gcart_header — ReportTable.headHtml() (drag-reorder + gear per
    // kolom), bukan lagi <th> polos manual.
    thead.innerHTML = ReportTable.headHtml(cols);

    if (!rows || !rows.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + cols.length + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    let html = '', prev = null, sub = { Qnt: 0, QntBatal: 0 }, grand = { Qnt: 0, QntBatal: 0 };

    rows.forEach(function (r, i) {
      const now = r[groupby];

      // subtotal saat nilai grup berganti (kalau toggle Subtotal aktif)
      if (showSub && i !== 0 && prev !== now) {
        html += totalRowTotal('Subtotal', sub, cols, 'subtotal-row');
        sub = { Qnt: 0, QntBatal: 0 };
      }

      sub.Qnt        += currencyNormalizer(r.Qnt);
      sub.QntBatal   += currencyNormalizer(r.QntBatal);

      grand.Qnt      += currencyNormalizer(r.Qnt);
      grand.QntBatal += currencyNormalizer(r.QntBatal);

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

  // Baris total (Qnt & QntBatal saja): nilai di kolomnya masing-masing, label di
  // kolom pertama non-total, sel lain dikosongkan   mengikuti urutan kolom terlihat saat ini.
  function totalRowTotal(label, total, cols, cls) {
    const labelIdx = cols.findIndex(c =>
        !['Qnt', 'QntBatal'].includes(c[0])
    );

    const tds = cols.map(function(c, idx) {
        if (c[0] === 'Qnt')
            return '<td class="num">' + format_number(total.Qnt, 2) + '</td>';
        if (c[0] === 'QntBatal')
            return '<td class="num">' + format_number(total.QntBatal, 2) + '</td>';
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
