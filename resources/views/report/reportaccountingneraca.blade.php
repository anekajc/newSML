@extends('report.masterreport2')

{{-- Table styling lives in public/css/report-table.css (loaded via report/newmaster2.blade.php).
     Laporan Neraca (balance sheet): styled .tb-report dengan header DUA TINGKAT — dua band
     "AKTIVA" & "PASIVA" (masing-masing 3 kolom: Uraian/Bulan Ini/Bulan Lalu), tiap kolom tetap
     punya menu roda gigi (sembunyikan/desimal/total) lewat buildGroupedThead(), sama seperti
     reportaccountinglabarugi.blade.php/reportaccountingaktiva.blade.php. Halaman ini sebelumnya
     @extends('report.masterreportNeraca') — layout khusus (bukan masterreport2/masterreport4)
     dengan engine render() bespoke sendiri (dua sumber data digabung jadi satu tabel dua-panel).
     Diganti total ke masterreport2 + report-table.js supaya gear-nya identik dengan laporan lain,
     TAPI badan tabel (render()) tetap ditulis manual karena bukan daftar baris flat: Aktiva &
     Pasiva adalah DUA SUMBER TERPISAH (res1/res2 dari doReport), masing-masing dikelompokkan per
     rentang kode (grupAP2/grupAP2Pasiva → getGroupTitle()) dan dirender berdampingan per baris
     (lihat render()), dengan Subtotal per grup + baris "JUMLAH ASET"/"JUMLAH KEWAJIBAN DAN
     EKUITAS" mengikuti toggle Customize Table (gsum_issubtotal/gsum_isgrandtotal) — TIDAK ada
     UI lain untuk toggle itu, makanya tombol "Customize Table" tetap ada di toolbar (pola sama
     dengan reportaccountingaktiva.blade.php).
     Mode "Valas" (Debet/Kredit jurnal) dan filter Tolakan/Perkiraan yang lama SUDAH DIHAPUS —
     semuanya berupa dropdown `hidden` yang tidak pernah bisa dicapai user, tidak ada kode yang
     meng-unhide-nya. Fetch "saldo awal" (dulu di doMakeTableSaldoAwal) juga dihapus karena nilainya
     tidak pernah dipakai oleh footer mode Rp (window.resSaldoAwal diambil tapi tidak pernah dibaca
     di setRowFooterRp yang lama) — itu hanya dipakai footer mode Valas yang sudah tidak ada.
     Filter: Bulan/Tahun tetap di toolbar; Divisi dipindah ke modal "Filter Laporan" (dropdown
     polos, bukan entity-picker modalAccountingJurnal lagi — endpoint loadDivisi controller ini
     sendiri, sebelumnya tidak pernah di-routing).
     TIDAK ada search box: filter sisi-klien akan membuat sisi Aktiva & Pasiva ter-filter berbeda
     (teks yang cocok belum tentu sama di kedua sisi), sehingga "JUMLAH ASET" bisa berhenti sama
     dengan "JUMLAH KEWAJIBAN DAN EKUITAS" padahal datanya memang seimbang — halaman lama juga
     tidak punya search. Export: Excel (dump HTML tabel apa adanya, sama seperti sebelumnya, lewat
     doExportTableToExcel() bawaan masterreport2) + Print. Tanpa CSV — struktur dua-panel
     berkelompok tidak punya representasi CSV yang jujur tanpa kerja tambahan yang tidak diminta.
     Sumber: sp_ReportNeracaAktiva (res1) + sp_ReportNeracaPasivaWeb (res2) :divisi,:bulan,:tahun.
     Data hanya dimuat setelah klik "Tampilkan". --}}

<style>
  /* tinggi awal area tabel supaya dropdown tidak terpotong container pendek */
  .tb-report .table-wrap { min-height: 10vh; }

  /* Tidak ada drag-reorder di halaman ini (header dua tingkat/grouped tidak bisa
     menoleransi kolom pindah band) -- timpa cursor:grab bawaan .th-inner supaya
     tidak menyiratkan kolom bisa diseret. Gear (hide/desimal/total) tetap aktif. */
  .tb-report .tb thead th.rt-th .th-inner { cursor: default; }
</style>

@section('header2')
<div class="tb-report main">
  <div class="content">

    <!-- TOOLBAR -->
    <div class="toolbar">

      <!-- Period selector (populated dynamically by populatePeriodSelectors) -->
      <div class="period-select-wrap">
        <label>Periode</label>
        <select class="period-select" id="periodBulan" onchange="changePeriodParts()"></select>
        <select class="period-select" id="periodTahun" onchange="changePeriodParts()"></select>
      </div>

      {{-- Divisi pindah ke modal "Filter Laporan" -- lihat docs/new-filter-modal-ui-guide.md.
           Nilai sebenarnya: globalDivisi (var JS). --}}

      <!-- Actions: filter + customize table + tampilkan + export -->
      <div class="action-group">
        {{-- Dibuka lewat plugin jQuery (Bootstrap 4), BUKAN data-bs-toggle (Bootstrap 5) --
             lihat aturan dua-Bootstrap di new-design-all-report-guide.md §5.1. --}}
        <button class="btn-load" type="button" onclick="$('#modalFilter').modal('show')">
          <i class="fas fa-filter"></i> Filter
        </button>
        {{-- Satu-satunya UI untuk toggle Subtotal/Grand Total (gsum_issubtotal/gsum_isgrandtotal) --
             tidak ada switch lain untuk itu, sama seperti reportaccountingaktiva.blade.php. --}}
        {{-- <button class="btn-load" onclick="doShowFormCustomizeTable()" title="Customize Table"><i class="fas fa-cog"></i> Customize Table</button> --}}
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

    <!-- Bar kolom tersembunyi (diisi oleh report-table.js / ReportTable) -->
    <div id="rtBar"></div>

    <!-- TABLE — header dua tingkat (band AKTIVA/PASIVA) dibangun oleh buildGroupedThead();
         badan tabel (dua panel berkelompok) di-render oleh render() -->
    <div class="table-outer">
      <div class="table-wrap">
        <table class="tb" id="mainTable">
          <thead>
            <tr>
              <th colspan="3" class="th-group">AKTIVA</th>
              <th colspan="3" class="th-group">PASIVA</th>
            </tr>
            <tr>
              <th style="min-width:220px">Uraian</th>
              <th class="num" style="min-width:130px">Bulan Ini</th>
              <th class="num" style="min-width:130px">Bulan Lalu</th>
              <th style="min-width:220px">Uraian</th>
              <th class="num" style="min-width:130px">Bulan Ini</th>
              <th class="num" style="min-width:130px">Bulan Lalu</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            <tr class="empty-row"><td colspan="6">Atur filter lalu klik <b>Tampilkan</b> untuk memuat laporan.</td></tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span id="footerLabel">Belum ada data dimuat</span>
      </div>
    </div>

    <div class="rt-hint">
      <i class="bi bi-info-circle"></i>
      Klik <i class="bi bi-gear"></i> pada judul kolom untuk sembunyikan kolom atau atur desimal &amp; total.
      Subtotal/Grand Total diatur lewat <b>Customize Table</b>.
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
          <i class="fas fa-filter"></i> Filter Laporan
          <span class="rt-active-badge" id="filterBadge">0 aktif</span>
        </h5>
        {{-- data-dismiss (BS4) = yang benar-benar menutup, karena modal ini dibuka lewat
             $.fn.modal milik BS4 (jQuery baru dimuat SESUDAH bundle BS5 di masterreport2).
             data-bs-dismiss dibiarkan untuk jaga-jaga. --}}
        <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal" data-bs-dismiss="modal"
                onclick="$('#modalFilter').modal('hide')"></button>
      </div>

      <div class="modal-body">

        <div class="rt-section">
          <div class="rt-group-label">Pengaturan Laporan</div>
          <div class="rt-grid-1">
            <div>
              <label class="rt-field-label" for="modalDivisi">Divisi</label>
              {{-- Diisi dari laporanaccountingneraca_loaddivisi (loadDivisiDropdown()). Selalu
                   punya nilai (tidak ada opsi "Semua") -- pilihan wajib, bukan filter yang bisa
                   dimatikan, jadi TIDAK dihitung di badge (lihat updateFilterBadge()). --}}
              <select class="rt-native" id="modalDivisi"></select>
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
@endsection


@section('jsreport')
<script type="text/javascript">
  let defaultBulan = new Date().getMonth() + 1;  // 1–12
  let defaultTahun = new Date().getFullYear();

  let g_reportTitle = "";
  let lastAktiva = [];   // res1 (sp_ReportNeracaAktiva) hasil fetch terakhir
  let lastPasiva = [];   // res2 (sp_ReportNeracaPasivaWeb) hasil fetch terakhir

  let globalDivisi = "-";  // diisi loadDivisiDropdown() saat page load (selalu wajib diisi)

  // Report mode dipakai engine masterreport2 (doSetHeader) — cukup satu int, halaman ini
  // cuma satu mode (mode "Valas" yang lama sudah dihapus, tidak pernah bisa dicapai user).
  g_modeReport = 26;

  const reportUrl = "{{ url('laporanaccountingneraca_doReport') }}";

  // Susunan kolom tabel: dua band (AKTIVA/PASIVA), masing-masing 3 kolom (Uraian/Bulan Ini/
  // Bulan Lalu). Urutan mengikuti header dua tingkat di markup dan TETAP -- tidak ada drag di
  // halaman ini. `band` menandai sisi kolom (dipakai buildGroupedThead() DAN render() untuk
  // memilah kolom yang terlihat per sisi -- lihat bandCols()). Kolom Uraian tidak ditotal
  // (varchar); jumlah1/jumlah2 (kedua sisi) ditotal.
  const COLS = [
    { key: 'keterangan',        label: 'Aktiva - Uraian',      type: 'str', dec: 0, total: false, band: 'aktiva', short: 'Uraian' },
    { key: 'jumlah1',           label: 'Aktiva - Bulan Ini',   type: 'num', dec: 2, total: true,  band: 'aktiva', short: 'Bulan Ini' },
    { key: 'jumlah2',           label: 'Aktiva - Bulan Lalu',  type: 'num', dec: 2, total: true,  band: 'aktiva', short: 'Bulan Lalu' },
    { key: 'keteranganPasiva',  label: 'Pasiva - Uraian',      type: 'str', dec: 0, total: false, band: 'pasiva', short: 'Uraian' },
    { key: 'jumlah1Pasiva',     label: 'Pasiva - Bulan Ini',   type: 'num', dec: 2, total: true,  band: 'pasiva', short: 'Bulan Ini' },
    { key: 'jumlah2Pasiva',     label: 'Pasiva - Bulan Lalu',  type: 'num', dec: 2, total: true,  band: 'pasiva', short: 'Bulan Lalu' },
  ];

  function bandLabel(band) {
    return band === 'aktiva' ? 'AKTIVA' : 'PASIVA';
  }

  $(document).ready(function () {
    doSetHeader(g_modeReport);   // muat gcart_header + gsum flags (default / hasil Customize Table tersimpan)
    populatePeriodSelectors();
    loadDivisiDropdown();   // isi dropdown Divisi (default: divisi pertama)

    // Header tabel interaktif TANPA drag (lihat komentar di atas file): gear per kolom untuk
    // sembunyikan/desimal/total, + bar "Reset kolom"/kolom tersembunyi. Tidak ada "Tampilan"
    // switcher -- halaman ini cuma satu mode.
    ReportTable.init({
      table: '#mainTable',
      bar: '#rtBar',
      onChange: function () { render(); }
    });

    // Sengaja TIDAK memuat data saat halaman dibuka — laporan hanya dimuat setelah
    // pengguna klik tombol "Tampilkan".
  });

  // Header sederhana untuk menjaga engine masterreport2 tetap terinisialisasi
  // (doSetHeader memanggil ini bila belum ada header tersimpan). Tabel styled
  // di-render sendiri oleh render() dari COLS.
  function setDefaultHeader() {
    gcart_header = COLS.map(c => [c.key, c.label, 1, (c.type === 'num' ? 'float' : c.type), (c.total ? 1 : 0), c.dec]);
    gsum_issubtotal = 1; gsum_isgrandtotal = 1;
  }

  /* ── PERIODE (Bulan / Tahun) ── */
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
  // Hanya perbarui nilai periode; TIDAK memuat data (tunggu klik "Tampilkan").
  function changePeriodParts() {
    defaultBulan = parseInt(document.getElementById('periodBulan').value, 10);
    defaultTahun = parseInt(document.getElementById('periodTahun').value, 10);
  }

  /* ── EXPORT (Excel = dump HTML tabel apa adanya, sama seperti sebelumnya; Print = window.print).
     Tanpa CSV -- lihat catatan di atas file. doExportTableToExcel() disediakan oleh
     masterreport2.blade.php (dipakai $akses['xlsfilename'] sebagai nama file). ── */
  function toggleExport() { document.getElementById('exportDrop').classList.toggle('open'); }
  document.addEventListener('click', function (e) {
    const wrap = document.getElementById('exportWrap');
    if (wrap && !wrap.contains(e.target)) { document.getElementById('exportDrop').classList.remove('open'); }
  });
  function doExport(fmt) {
    document.getElementById('exportDrop').classList.remove('open');
    if (fmt === 'Print') { window.print(); return; }
    doExportTableToExcel('mainTable');
    showToast('📄', 'Data diekspor sebagai Excel');
  }

  /* ── LOAD DATA: sp_ReportNeracaAktiva (res1) + sp_ReportNeracaPasivaWeb (res2), digabung sisi
     klien jadi tabel dua-panel oleh render(). ── */
  function makeTable(_mode) {
    g_reportTitle = 'REPORT ACCOUNTING NERACA';
    let _divisi = globalDivisi || '-';

    // muat gcart_header + gsum flags (default / hasil Customize Table tersimpan)
    if (typeof doSetHeader === 'function') { doSetHeader(g_modeReport); }

    document.getElementById('footerLabel').innerHTML = loadingHtml('Memuat data...');

    const data = { inputBulan: defaultBulan, inputTahun: defaultTahun, divisi: _divisi };

    $.ajax({
      url: reportUrl, type: 'get', data: data,
      success: function (res) {
        lastAktiva = (res && res.res1) ? res.res1 : [];
        lastPasiva = (res && res.res2) ? res.res2 : [];
        render();
      },
      error: function () { lastAktiva = []; lastPasiva = []; render(); }
    });
  }

  /* ── helpers ── */
  function str(v) { return (v == null ? '' : String(v)).trim(); }
  function pickCI(r, key) {
    if (r[key] !== undefined) return r[key];
    const lk = String(key).toLowerCase();
    for (const k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }
  function groupBy(rows, keyFn) {
    const out = {};
    (rows || []).forEach(r => { const k = keyFn(r); (out[k] = out[k] || []).push(r); });
    return out;
  }
  // Judul grup dari rentang kode grupAP2/grupAP2Pasiva (A1/A2/A3 = Aset, P1/P2 = Kewajiban,
  // P3+ = Ekuitas) -- logika sama persis dengan engine lama (masterreportNeraca).
  function getGroupTitle(kode) {
    if (!kode) return '';
    if (kode >= 'A1' && kode < 'A2') return 'ASSET LANCAR';
    if (kode >= 'A2' && kode < 'A3') return 'ASSET TIDAK LANCAR';
    if (kode >= 'A3' && kode < 'P1') return 'ASSET LAIN-LAIN';
    if (kode >= 'P1' && kode < 'P2') return 'KEWAJIBAN JK PENDEK';
    if (kode >= 'P2' && kode < 'P3') return 'KEWAJIBAN JK PANJANG';
    if (kode >= 'P3') return 'EKUITAS';
    return '';
  }
  // HTML-escape teks bebas (nama divisi bisa diisi user).
  function esc(v) {
    return String(v == null ? '' : v)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  /* ── HEADER DUA TINGKAT (band AKTIVA/PASIVA), TANPA drag ──
     ReportTable.headHtml() cuma bisa satu baris flat, jadi header dibangun manual di sini --
     tapi tiap kolom tetap dapat tombol roda gigi (data-rtgear) yang didengarkan oleh listener
     yang sama dipasang ReportTable.init() (event delegation di elemen <thead>, lihat
     docs/new-slider-table-guide.md). Kolom TIDAK bisa diseret (tidak ada .th-inner[draggable]/
     .th-grip di markup) supaya band AKTIVA/PASIVA tidak pernah pecah. Index (idx) yang dipakai
     `data-rtgear`/`data-gidx` adalah posisi di gcart_header, yang urutannya SELALU sama dengan
     COLS karena tidak ada drag yang bisa mengubahnya. ── */
  function leafTh(idx, label, col) {
    const isNum = (col[3] === 'float' || col[3] === 'int');
    return '<th class="rt-th' + (isNum ? ' num' : '') + '" data-gidx="' + idx + '">' +
      '<div class="th-inner">' +
      '<span class="th-label">' + label + '</span>' +
      '<button type="button" class="th-gear" data-rtgear="' + idx + '" title="Setting kolom"><i class="bi bi-gear"></i></button>' +
      '</div></th>';
  }

  function buildGroupedThead() {
    let row1 = '', row2 = '';
    let i = 0;
    while (i < COLS.length) {
      // kumpulkan run kolom berurutan dalam band yang sama (band SELALU kontigu karena tidak ada drag)
      const band = COLS[i].band;
      let j = i, count = 0;
      while (j < COLS.length && COLS[j].band === band) {
        const bcol = gcart_header[j];
        if (Number(bcol[2]) === 1) { count++; row2 += leafTh(j, COLS[j].short || COLS[j].label, bcol); }
        j++;
      }
      if (count > 0) { row1 += '<th colspan="' + count + '" class="th-group">' + bandLabel(band) + '</th>'; }
      i = j;
    }
    return '<tr>' + row1 + '</tr><tr>' + row2 + '</tr>';
  }

  // Kolom yang terlihat (gcart_header[i][2]===1) untuk satu sisi (band), berurut sesuai COLS.
  function bandCols(band) {
    const out = [];
    COLS.forEach((c, i) => {
      if (c.band === band && Number(gcart_header[i][2]) === 1) { out.push({ def: c, col: gcart_header[i] }); }
    });
    return out;
  }

  // Sel data satu sisi untuk satu baris item (item null = sisi lain lebih panjang, isi kosong).
  function sideCells(item, visCols) {
    if (!visCols.length) { return ''; }
    if (!item) { return '<td colspan="' + visCols.length + '">&nbsp;</td>'; }
    return visCols.map(function (o) {
      const v = pickCI(item, o.def.key);
      if (o.def.type === 'num') {
        const n = currencyNormalizer(v);
        return '<td class="num">' + (n === 0 ? '' : format_number(n, o.def.dec)) + '</td>';
      }
      return '<td>' + nullToEmpty(v) + '</td>';
    }).join('');
  }

  // Sel judul grup satu sisi (colspan = jumlah kolom terlihat di sisi itu).
  function sideGroupHeader(title, visCols) {
    if (!visCols.length) { return ''; }
    return '<td colspan="' + visCols.length + '">' + (title || '&nbsp;') + '</td>';
  }

  // Sel subtotal/grand total satu sisi: kolom Uraian → label, kolom numerik bertotal → jumlah,
  // kolom lain → kosong.
  function sideTotalCells(visCols, sums, label) {
    if (!visCols.length) { return ''; }
    return visCols.map(function (o) {
      if (o.def.type === 'str') { return '<td>' + (label || '') + '</td>'; }
      if (o.def.total) { return '<td class="num">' + format_number(sums[o.def.key] || 0, o.def.dec) + '</td>'; }
      return '<td></td>';
    }).join('');
  }

  /* ── RENDER: Aktiva (res1) & Pasiva (res2) adalah DUA SUMBER TERPISAH -- masing-masing
     dikelompokkan per rentang kode (grupAP2/grupAP2Pasiva → getGroupTitle()), lalu dirender
     BERDAMPINGAN per baris (grup ke-N Aktiva sejajar dengan grup ke-N Pasiva, bukan dicocokkan
     berdasarkan isi). Subtotal per grup + baris "JUMLAH ASET"/"JUMLAH KEWAJIBAN DAN EKUITAS"
     mengikuti toggle Customize Table (gsum_issubtotal/gsum_isgrandtotal). Kolom terlihat & urutan
     dari gcart_header (item[2]===1) supaya show/hide lewat gear benar-benar berpengaruh per sisi
     (lihat bandCols()); urutan itu sendiri tetap sama dengan COLS karena tidak ada drag. ── */
  function render() {
    const thead = document.querySelector('#mainTable thead');
    const tbody = document.getElementById('tableBody');

    thead.innerHTML = buildGroupedThead();
    ReportTable.refresh();   // segarkan #rtBar (biasanya efek samping headHtml(), tapi di sini header dibangun manual)

    const visA = bandCols('aktiva');
    const visP = bandCols('pasiva');
    const showSub = (gsum_issubtotal === 1);
    const showGrand = (gsum_isgrandtotal === 1);

    if (!lastAktiva.length && !lastPasiva.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="' + Math.max(visA.length + visP.length, 1) + '">Tidak ada data ditemukan.</td></tr>';
      document.getElementById('footerLabel').textContent = 'Tidak ada data';
      return;
    }

    const groupsA = groupBy(lastAktiva, r => str(pickCI(r, 'grupAP2')) || str(pickCI(r, 'grupAP2Aktiva')));
    const groupsP = groupBy(lastPasiva, r => str(pickCI(r, 'grupAP2Pasiva')));
    const keysA = Object.keys(groupsA).sort();
    const keysP = Object.keys(groupsP).sort();
    const maxGroups = Math.max(keysA.length, keysP.length);

    const grandA = {}, grandP = {};
    visA.forEach(o => { if (o.def.total) { grandA[o.def.key] = 0; } });
    visP.forEach(o => { if (o.def.total) { grandP[o.def.key] = 0; } });

    let html = '';
    for (let g = 0; g < maxGroups; g++) {
      const kA = keysA[g] || null, kP = keysP[g] || null;
      const itemsA = kA ? groupsA[kA] : [];
      const itemsP = kP ? groupsP[kP] : [];
      const titleA = kA ? getGroupTitle(kA) : '';
      const titleP = kP ? getGroupTitle(kP) : '';

      html += '<tr class="group-row">' + sideGroupHeader(titleA, visA) + sideGroupHeader(titleP, visP) + '</tr>';

      const subA = {}, subP = {};
      visA.forEach(o => { if (o.def.total) { subA[o.def.key] = 0; } });
      visP.forEach(o => { if (o.def.total) { subP[o.def.key] = 0; } });

      const maxItems = Math.max(itemsA.length, itemsP.length);
      for (let i = 0; i < maxItems; i++) {
        const itemA = itemsA[i] || null;
        const itemP = itemsP[i] || null;

        if (itemA) { visA.forEach(o => { if (o.def.total) { const v = currencyNormalizer(pickCI(itemA, o.def.key)); subA[o.def.key] += v; grandA[o.def.key] += v; } }); }
        if (itemP) { visP.forEach(o => { if (o.def.total) { const v = currencyNormalizer(pickCI(itemP, o.def.key)); subP[o.def.key] += v; grandP[o.def.key] += v; } }); }

        html += '<tr class="data-row">' + sideCells(itemA, visA) + sideCells(itemP, visP) + '</tr>';
      }

      if (showSub) {
        html += '<tr class="subtotal-row">' +
          sideTotalCells(visA, subA, kA ? ('JML ' + titleA) : '') +
          sideTotalCells(visP, subP, kP ? ('JML ' + titleP) : '') +
          '</tr>';
      }
    }

    if (showGrand) {
      html += '<tr class="grand-total">' +
        sideTotalCells(visA, grandA, 'JUMLAH ASET') +
        sideTotalCells(visP, grandP, 'JUMLAH KEWAJIBAN DAN EKUITAS') +
        '</tr>';
    }

    tbody.innerHTML = html;
    document.getElementById('footerLabel').textContent =
      'Menampilkan ' + lastAktiva.length + ' baris Aktiva, ' + lastPasiva.length + ' baris Pasiva';
  }

  /* ── TOAST ── */
  function showToast(icon, msg) {
    const t = document.getElementById('toast');
    document.getElementById('ti').textContent = icon;
    document.getElementById('tm').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  function getKolomFilter() { return ['keterangan']; }

  /* ── SELECT DIVISI (modal Filter Laporan) ──
        Diisi sekali dari laporanaccountingneraca_loaddivisi saat page load. Memilih item hanya
        menyetel globalDivisi; laporan baru dimuat saat klik Tampilkan (konsisten dgn filter
        Periode). ── */
  function loadDivisiDropdown() {
    let list = [];
    $.ajax({
      url: "{!! url('laporanaccountingneraca_loaddivisi') !!}",
      type: "get", async: false,
      success: function (res) { list = res || []; }
    });

    let html = '';
    list.forEach((item) => {
      const nama = (item.NamaDevisi != null ? String(item.NamaDevisi) : '');
      html += '<option value="' + item.Devisi + '">' + item.Devisi + ' - ' + esc(nama) + '</option>';
    });
    $("#modalDivisi").html(html);

    // default: divisi pertama (tanpa memuat ulang — laporan dimuat saat klik "Tampilkan")
    if (list.length) { setDivisi(list[0].Devisi); }
  }

  function setDivisi(kode) {
    globalDivisi = kode;
    $("#modalDivisi").val(kode);
  }

  /* ── FILTER MODAL ──
        Satu-satunya field di sini adalah Divisi. Ia TIDAK ikut dihitung di badge karena
        tidak punya opsi "Semua" — wajib selalu diisi, jadi bukan "filter yang dinyalakan"
        (aturan sama seperti Divisi di reportaccountinglabarugi). ── */
  function updateFilterBadge() {
    $('#filterBadge').text('0 aktif');
  }

  function resetAllFilters() {
    if ($('#modalDivisi option').length) {
      $('#modalDivisi').prop('selectedIndex', 0);
    }
    updateFilterBadge();
  }

  $('#modalFilter').on('show.bs.modal', function () {
    $('#modalDivisi').val(globalDivisi);
    updateFilterBadge();
  });

  $('#modalFilter').on('change', 'select.rt-native', updateFilterBadge);

  function applyModalFilter() {
    setDivisi($('#modalDivisi').val());
    $('#modalFilter').modal('hide');
  }
</script>
@endsection
