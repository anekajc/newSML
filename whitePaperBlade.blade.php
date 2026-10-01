@extends('report.masterreport4')

  {{-- Table styling moved to public/css/report-table.css (loaded via report/newmaster2.blade.php) --}}

@include('report.modalAccountingJurnal')

@section('header2')
    <div class="tb-report main">
      <div class="content">

        <!-- TOOLBAR ROW 1 -->
        <div class="toolbar">
          <div>
            <div class="page-title">Report Neraca Lajur</div>
            <div class="page-sub" id="pageSubLine">Dicetak oleh:  {{ $akses['user'] }} &nbsp;<i class="bi bi-dot"></i>&nbsp; <span id="printTime"></span></div>
          </div>

          <!-- Period selector (populated dynamically by populatePeriodSelectors) -->
          <div class="period-select-wrap">
            <label>Periode</label>
            <select class="period-select" id="periodBulan" onchange="changePeriodParts()"></select>
            <select class="period-select" id="periodTahun" onchange="changePeriodParts()"></select>
          </div>

          <!-- Search -->
          <input class="search-inp" type="text" placeholder="Cari akun..." oninput="applyFilters()">

          <!-- Zero toggle -->
          <button class="tgl-btn" id="zeroToggle" onclick="toggleZero()">
            <span class="tgl-dot"></span> Sembunyikan nol
          </button>

          <!-- Export -->
          <div class="export-wrap" id="exportWrap">
            <button class="export-btn" onclick="toggleExport()"><i class="bi bi-arrow-down"></i> Export <i class="bi bi-caret-down-fill"></i></button>
            <div class="export-drop" id="exportDrop">
              <div class="export-opt" onclick="doExport('Excel')"><i class="bi bi-journals text-success"></i> Ekspor ke <span class="ext">XLSX</span></div>
              <div class="export-opt" onclick="doExport('PDF')"><i class="bi bi-file-earmark-pdf text-danger"></i> Ekspor ke <span class="ext">PDF</span></div>
              <div class="export-opt" onclick="doExport('CSV')"><i class="bi bi-clipboard"></i> Ekspor ke <span class="ext">CSV</span></div>
              <div class="export-opt" onclick="doExport('Print')"><i class="bi bi-printer-fill text-warning "></i> Cetak Laporan</div>
            </div>
          </div>
        </div>

        <!-- TOOLBAR ROW 2: type filters -->
        <div class="toolbar" style="margin-bottom:10px">
          <div class="type-filters">
            <button class="tf all on" onclick="setTypeFilter('all',this)">Semua</button>
            <button class="tf asset" onclick="setTypeFilter('asset',this)"><i class="bi bi-circle-fill text-primary"></i> Aset</button>
            <button class="tf liab" onclick="setTypeFilter('liab',this)"><i class="bi bi-circle-fill text-purple"></i> Kewajiban</button>
            <button class="tf equity" onclick="setTypeFilter('equity',this)"><i class="bi bi-circle-fill text-success"></i> Ekuitas</button>
            <button class="tf rev" onclick="setTypeFilter('rev',this)"><i class="bi bi-circle-fill text-teal"></i> Pendapatan</button>
            <button class="tf exp" onclick="setTypeFilter('exp',this)"><i class="bi bi-circle-fill text-warning"></i> Beban</button>
          </div>
          <span style="font-size:12px;color:var(--muted);margin-left:6px">
            <i class="bi bi-lightbulb-fill text-warning"></i> Klik baris untuk melihat detail transaksi
          </span>
        </div>

        <!-- KPI STRIP -->
        <div class="kpi-strip" id="kpiStrip"></div>

        <!-- TABLE -->
        <div class="table-outer">
          <div class="table-wrap">
            <table class="tb" id="mainTable">
              <thead>
                <tr>
                  <th rowspan="2" style="width:90px">Perkiraan</th>
                  <th rowspan="2">Keterangan</th>
                  <th rowspan="2" class="num" style="min-width:140px">Saldo Awal</th>
                  <th colspan="2" class="th-group" style="min-width:240px">Mutasi</th>
                  <th colspan="2" class="th-group" style="min-width:240px">Penyesuaian</th>
                  <th colspan="2" class="th-group" style="min-width:240px">Rugi / Laba</th>
                  <th rowspan="2" class="num" style="min-width:140px">Saldo Akhir</th>
                </tr>
                <tr>
                  <th class="num" style="min-width:120px">Debet</th>
                  <th class="num" style="min-width:120px">Kredit</th>
                  <th class="num" style="min-width:120px">Debet</th>
                  <th class="num" style="min-width:120px">Kredit</th>
                  <th class="num" style="min-width:120px">Debet</th>
                  <th class="num" style="min-width:120px">Kredit</th>
                </tr>
              </thead>
              <tbody id="tableBody"></tbody>
            </table>
          </div>
          <div class="table-footer">
            <span id="footerLabel">Menampilkan semua akun</span>
          </div>
        </div>

      </div><!-- /content -->

    <!-- DRILL OVERLAY -->
  <div class="drill-overlay" id="drillOverlay" onclick="closeDrill()"></div>

  <!-- DRILL PANEL -->
  <div class="drill-panel" id="drillPanel">
    <div class="dp-header">
      <div>
        <div class="dp-title" id="dpTitle">-</div>
        <div class="dp-sub" id="dpSub">-</div>
      </div>
      <div class="dp-close" onclick="closeDrill()"><i class="bi bi-x"></i></div>
    </div>
    <div class="dp-meta" id="dpMeta"></div>
    <div class="dp-body" id="dpBody"></div>
  </div>

  <!-- TOAST -->
  <div class="toast" id="toast"><span id="ti"></span><span id="tm"></span></div>

  <!-- KAS HARIAN PANEL (slides up from the bottom, full width, 50vh) -->
  <div class="kas-panel" id="kasPanel">
    <div class="kas-head">
      <div class="kas-title" id="kasTitle">Bukti Kas</div>
      <div class="dp-close" onclick="closeKasharian()"><i class="bi bi-x"></i></div>
    </div>
    <div class="kas-body" id="kasBody"></div>
  </div>
  </div><!-- /tb-report -->


@endsection

@section('jsreport')

{{-- Reusable voucher drill (Bukti Kas/Bank + Invoice). Reads window.ReportTableConfig set below. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

<script type="text/javascript">
  let globalReportMode = "0"; // default: Detail
  let globalOrderBy = "N"; // default: Detail
  let defaultBulan = new Date().getMonth() + 1;  // +1 because getMonth() returns 0-11
  let defaultTahun = new Date().getFullYear();

  // Endpoints for the styled "main table" and its drill-down (Sp_NerajaLajur).
  const reportUrl  = "{{ url('laporanaccountingneracalajur_doReport') }}";
  const ledgerUrl  = "{{ url('laporanaccountingneracalajur_doLedger') }}";

  // Voucher drill (Bukti Kas/Bank/Invoice) lives in public/js/report-table.js.
  // It reads its endpoints from this config; titles come from its default
  // Jenis map (override here with jenisTitle: { CODE: 'TITLE' } if ever needed).
  window.ReportTableConfig = {
    kasUrl    : "{{ url('laporanaccountingneracalajur_doKasharian') }}",
    invoiceUrl: "{{ url('laporanaccountingneracalajur_doInvoice') }}",
    lpbUrl    : "{{ url('laporanaccountingneracalajur_doLpb') }}"
  };

  var jenisreport = 0; // ini untuk detail dan rekap

  $(document).ready(function() {
    // Keep the shared report engine's header state initialised (masterreport4
    // still references these), even though this report renders its own table.
    setReportMode(globalReportMode);
    setDefaultHeader();

    // The styled .tb-report table is the only table this report uses. Strip the
    // shared engine's old table markup from this page and keep it hidden (we
    // never call doMakeTable here). The wrapper element is kept empty so the
    // master's doGodown()/Ctrl+Up scroll target still resolves. The shared
    // masterreport4 template itself is untouched, so other reports are unaffected.
    $("#showTableReport").empty().hide();

    populatePeriodSelectors();

    setTimeout(() => {
      makeTable('REPORT');
    }, 100);
  });

  function setReportMode(val) {
    globalReportMode = val;
    jenisreport = Number(val);   // 0 = Detail, 1 = Rekap
    DetOrRekap = Number(val);    // samakan dengan variabel yang ada di setModeReport

    console.log(val)
    // hapus centang dulu
    $('#dropdownReportMode .dropdown-item').each(function() {
      let itemText = $(this).text().replace(' ?', '').trim();
      $(this).text(itemText);
    });

    // tambah centang di item terpilih
    $(`#dropdownReportMode .dropdown-item[data-value='${val}']`).each(function() {
      $(this).html(`${$(this).text()} <span class="checkmark-red">?</span>`);
    });

    // update g_modeReport sesuai pilihan order & detail/rekap
    // setModeReport() sudah mengatur g_modeReport berdasarkan $("#inputOrder").val() dan jenisreport/DetOrRekap
    setModeReport();
  }

  var modereport_detailnobukti = 0, modereport_rekapnobukti = 1;
  g_modeReport = modereport_detailnobukti;

  function setDefaultHeader() {
    if (g_modeReport == modereport_detailnobukti) {
      gcart_header = [
        ['perkiraan', 'No. ACC', 1, 'varchar', 0, 0, [1, 1, 2], false], // has data
        ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0, [1, 1, 2], false], // has data
        ['SaldoAwal', 'Saldo Awal', 1, 'float', 1, 0, [1, 1, 2], false], // has data
        ['', 'Mutasi', 1, 'group', 0, 0, [1, 2, 1], true], // header only
        ['MD', 'Debet', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['MK', 'Kredit', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['', 'Koreksi', 1, 'group', 0, 0, [1, 2, 1], true], // header only
        ['JPD', 'Debet', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['JPK', 'Kredit', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['', 'Laba/Rugi', 1, 'group', 0, 0, [1, 2, 1], true], // header only
        ['JPD', 'Debet', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['JPK', 'Kredit', 1, 'float', 1, 2, [2, 1, 1], false], // has data
        ['SaldoKAkhir', 'Saldo Akhir', 1, 'float', 1, 0, [1, 1, 2], false] // has data
      ];

      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    } else if (g_modeReport == modereport_rekapnobukti){
      gcart_header = [
        ['NoBukti', 'No. Bukti', 1, 'varchar', 0, 0],
        ['Tanggal', 'Tanggal', 1, 'date', 0, 0],
        ['NamaSls', 'Sales', 1, 'varchar', 0, 0],
        ['NAMACUSTSUPP', 'Nama Customer', 1, 'varchar', 0, 0],
        ['NOPOCUstomer', 'No. PO. Customer', 1, 'varchar', 0, 0],
        ['NoSo', 'No. SO', 1, 'varchar', 0, 0],
        ['TanggalSO', 'Tanggal SO', 1, 'date', 0, 0],
        ['NDPPRPZX', 'Total', 1, 'float', 1, 0]
      ];
      gsum_issubtotal = 1; gsum_isgrandtotal = 1;

    }

  }

  function makeTable (_mode) {
    let inputBulan = defaultBulan;
    let inputTahun = defaultTahun;
    let divisi     = '-';

    currentPeriod = inputBulan + '/' + inputTahun;
    document.getElementById('footerLabel').textContent = 'Memuat data…';

    $.ajax({
      url   : reportUrl,
      type  : 'get',
      data  : { inputBulan: inputBulan, inputTahun: inputTahun, divisi: divisi },
      success: function (res) {
        accountGroups = buildGroups(res || []);
        renderKpi();
        render();
      },
      error: function () {
        accountGroups = [];
        renderKpi();
        render();
        document.getElementById('footerLabel').textContent = 'Gagal memuat data laporan.';
      }
    });
  }

  function getKolomFilter() {
    // tentukan kolom (sesuai database & gcart_header) yang mau ditampilkan
    // mode report menentukan kolom yang dipakai
    // berapa pun bisa asal dalam bentuk array

    let data = [];
    if ($("#inputOrder").val() == "N")
    {
      data = ['NoBukti', 'TanggalSO'];
    }

    return data;
  }

  function setModeReport () {
    if (globalOrderBy == "N") {
      if (jenisreport === 0) {
        g_modeReport = modereport_detailnobukti;
      } else {
        g_modeReport = modereport_rekapnobukti;
      }
    }

    doSetHeader(g_modeReport);
    doShowCustomize();
  }

  /* -- DATA (populated from Sp_NerajaLajur via makeTable) -- */
    let accountGroups = [];

    const GROUP_META = {
      asset:  { key: 'asset',  label: 'ASET',       cls: 'g-asset',  tcls: 't-asset',  color: '#1D4ED8' },
      liab:   { key: 'liab',   label: 'KEWAJIBAN',  cls: 'g-liab',   tcls: 't-liab',   color: '#7C3AED' },
      equity: { key: 'equity', label: 'EKUITAS',    cls: 'g-equity', tcls: 't-equity', color: '#0F766E' },
      rev:    { key: 'rev',    label: 'PENDAPATAN', cls: 'g-rev',    tcls: 't-rev',    color: '#15803D' },
      exp:    { key: 'exp',    label: 'BEBAN',      cls: 'g-exp',    tcls: 't-exp',    color: '#B45309' },
      other:  { key: 'other',  label: 'LAINNYA',    cls: '',         tcls: '',         color: '#5A6A85' }
    };
    const GROUP_ORDER = ['asset', 'liab', 'equity', 'rev', 'exp', 'other'];

    // Classify by the first digit of the account code (standard COA):
    // 1=Aset, 2=Kewajiban, 3=Ekuitas, 4=Pendapatan, 5/6=Beban, else=Lainnya
    function classify(code) {
      switch (String(code || '').trim().charAt(0)) {
        case '1': return 'asset';
        case '2': return 'liab';
        case '3': return 'equity';
        case '4': return 'rev';
        case '5':
        case '6': return 'exp';
        default:  return 'other';
      }
    }

    function num(v) {
      if (v === null || v === undefined || v === '') return 0;
      const n = parseFloat(v);
      return isNaN(n) ? 0 : n;
    }

    // Read a property case-insensitively (the SP mixes cases: SaldoAwk vs SaldoAkK).
    function pick(lc, key) {
      return lc[key.toLowerCase()];
    }

    // Map flat Sp_NerajaLajur rows into the grouped structure render() expects.
    // SP columns: Perkiraan, keterangan, SaldoAwD, MD, MK, JPD, JPK,
    //             RLD, RLK, SaldoAkD  (SaldoAwk/SaldoAkK are unused and omitted)
    function buildGroups(rows) {
      const buckets = {};
      GROUP_ORDER.forEach(k => buckets[k] = []);

      rows.forEach(r => {
        // normalise keys to lowercase so casing differences don't matter
        const lc = {};
        Object.keys(r).forEach(k => { lc[k.toLowerCase()] = r[k]; });

        const code = (pick(lc, 'Perkiraan') != null ? pick(lc, 'Perkiraan') : '').toString().trim();
        buckets[classify(code)].push({
          code: code,
          name: (pick(lc, 'keterangan') != null ? pick(lc, 'keterangan') : '').toString().trim(),
          awD: num(pick(lc, 'SaldoAwD')),                             // Saldo Awal
          mD:  num(pick(lc, 'MD')),  mK:  num(pick(lc, 'MK')),        // Mutasi
          jpD: num(pick(lc, 'JPD')), jpK: num(pick(lc, 'JPK')),       // Penyesuaian
          rlD: num(pick(lc, 'RLD')), rlK: num(pick(lc, 'RLK')),       // Rugi / Laba
          akD: num(pick(lc, 'SaldoAkD'))                              // Saldo Akhir
        });
      });

      return GROUP_ORDER
        .filter(k => buckets[k].length)
        .map(k => Object.assign({}, GROUP_META[k], { accounts: buckets[k] }));
    }

    /* -- STATE -- */
    let hideZero = false, typeFilter = 'all', searchStr = '', currentPeriod = '6/2026';

    /* -- FORMAT -- */
    // fmtN / fmtFull (full Rupiah, e.g. "Rp 3.500.000") live in
    // public/js/report-table.js so all report* pages share one formatter.
    // Single Saldo Awal / Akhir values (Kredit columns are unused)
    function netAwal(a)  { return a.awD; }
    function netAkhir(a) { return a.akD; }
    function isZero(a) {
      return a.awD === 0 && a.mD === 0 && a.mK === 0 &&
             a.jpD === 0 && a.jpK === 0 && a.rlD === 0 && a.rlK === 0 &&
             a.akD === 0;
    }

    /* -- KPI -- */
    function renderKpi() {
      const el = document.getElementById('kpiStrip');
      el.innerHTML = accountGroups.map(g => {
        const total = g.accounts.reduce((s, a) => s + Math.abs(netAkhir(a)), 0);
        const count = g.accounts.length;
        return `<div class="kpi-card" style="border-left:3px solid ${g.color}">
      <div class="kpi-dot" style="background:${g.color}"></div>
      <div class="kpi-body">
        <div class="kpi-label">${g.label}</div>
        <div class="kpi-val" style="color:${g.color}">${fmtN(total)}</div>
        <div class="kpi-count">${count} akun</div>
      </div>
    </div>`;
      }).join('');
    }

  /* -- RENDER TABLE -- */
    function render() {
      const tbody = document.getElementById('tableBody');
      tbody.innerHTML = '';
      let visibleCount = 0, zeroHidden = 0;

      const search = document.querySelector('.search-inp')?.value?.toLowerCase() || '';

      accountGroups.forEach(g => {
        if (typeFilter !== 'all' && typeFilter !== g.key) return;

        const filteredAccs = g.accounts.filter(a => {
          if (hideZero && isZero(a)) { zeroHidden++; return false; }
          if (search && !a.code.includes(search) && !a.name.toLowerCase().includes(search)) return false;
          return true;
        });
        if (!filteredAccs.length) return;

        // group header row
        const gtr = document.createElement('tr');
        gtr.className = 'group-row ' + g.cls;
        gtr.innerHTML = `<td colspan="10">
      <span style="margin-right:8px">
        ${{ g_asset: '<i class="bi bi-circle-fill text-primary"></i>', g_liab: '<i class="bi bi-circle-fill text-purple"></i>', g_equity: '<i class="bi bi-circle-fill text-success">', g_rev: '<i class="bi bi-circle-fill text-teal">', g_exp: '<i class="bi bi-circle-fill text-warning">' }['g_' + g.key] || ''}
      </span>
      ${g.label}
      <span style="font-size:11px;font-weight:600;opacity:.7;margin-left:8px">(${filteredAccs.length} akun)</span>
    </td>`;
        tbody.appendChild(gtr);

        // running subtotals for the 8 numeric columns
        const sub = { awD: 0, mD: 0, mK: 0, jpD: 0, jpK: 0, rlD: 0, rlK: 0, akD: 0 };

        filteredAccs.forEach(a => {
          const zero = isZero(a);
          ['awD','mD','mK','jpD','jpK','rlD','rlK','akD'].forEach(k => sub[k] += a[k]);

          const tr = document.createElement('tr');
          tr.className = `data-row ${g.tcls}${zero ? ' zero-row' : ''}`;
          tr.title = `Klik untuk melihat detail transaksi ${a.code}`;
          tr.innerHTML = `
        <td class="code">${a.code}</td>
        <td class="name">${a.name}<span class="drill-hint"><i class="bi bi-arrow-right-short"></i> detail</span></td>
        <td class="num" style="font-weight:600">${fmtN(a.awD)}</td>
        <td class="num">${fmtN(a.mD)}</td>
        <td class="num">${fmtN(a.mK)}</td>
        <td class="num">${fmtN(a.jpD)}</td>
        <td class="num">${fmtN(a.jpK)}</td>
        <td class="num">${fmtN(a.rlD)}</td>
        <td class="num">${fmtN(a.rlK)}</td>
        <td class="num" style="font-weight:600">${fmtN(a.akD)}</td>
      `;
          tr.onclick = () => openDrill(a, g);
          tbody.appendChild(tr);
          visibleCount++;
        });

        // subtotal
        const str = document.createElement('tr');
        str.className = 'subtotal-row ' + g.cls;
        str.innerHTML = `
      <td colspan="2">Subtotal ${g.label}</td>
      <td class="num">${fmtN(sub.awD)}</td>
      <td class="num">${fmtN(sub.mD)}</td>
      <td class="num">${fmtN(sub.mK)}</td>
      <td class="num">${fmtN(sub.jpD)}</td>
      <td class="num">${fmtN(sub.jpK)}</td>
      <td class="num">${fmtN(sub.rlD)}</td>
      <td class="num">${fmtN(sub.rlK)}</td>
      <td class="num">${fmtN(sub.akD)}</td>
    `;
        tbody.appendChild(str);
      });

      // grand total
      if (typeFilter === 'all') {
        const allAccs = accountGroups.flatMap(g => g.accounts);
        const tot = { awD: 0, mD: 0, mK: 0, jpD: 0, jpK: 0, rlD: 0, rlK: 0, akD: 0 };
        allAccs.forEach(a => Object.keys(tot).forEach(k => tot[k] += a[k]));
        const gtr2 = document.createElement('tr');
        gtr2.className = 'grand-total';
        gtr2.innerHTML = `
      <td colspan="2" style="font-weight:800">TOTAL KESELURUHAN</td>
      <td class="num">${fmtN(tot.awD)}</td>
      <td class="num">${fmtN(tot.mD)}</td>
      <td class="num">${fmtN(tot.mK)}</td>
      <td class="num">${fmtN(tot.jpD)}</td>
      <td class="num">${fmtN(tot.jpK)}</td>
      <td class="num">${fmtN(tot.rlD)}</td>
      <td class="num">${fmtN(tot.rlK)}</td>
      <td class="num">${fmtN(tot.akD)}</td>
    `;
        tbody.appendChild(gtr2);
      }

      // footer
      const hidden = zeroHidden;
      let msg = `Menampilkan ${visibleCount} akun`;
      if (hidden > 0) msg += ` · ${hidden} akun nol disembunyikan`;
      document.getElementById('footerLabel').textContent = msg;
    }

    /* -- CONTROLS -- */
    function toggleZero() {
      hideZero = !hideZero;
      document.getElementById('zeroToggle').classList.toggle('on', hideZero);
      render();
    }
    function setTypeFilter(type, btn) {
      typeFilter = type;
      document.querySelectorAll('.tf').forEach(b => b.classList.remove('on'));
      if (btn) btn.classList.add('on');
      render();
    }
    function applyFilters() { render(); }

    /* -- PERIOD PICKER -- */
    const NAMA_BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Build the Bulan (1–12) and Tahun (current year and 6 prior) dropdowns,
    // selecting the current defaultBulan/defaultTahun.
    function populatePeriodSelectors() {
      const selB = document.getElementById('periodBulan');
      const selT = document.getElementById('periodTahun');
      if (!selB || !selT) return;

      selB.innerHTML = NAMA_BULAN.map((nama, i) =>
        `<option value="${i + 1}" ${(i + 1) == defaultBulan ? 'selected' : ''}>${nama}</option>`).join('');

      const thisYear = new Date().getFullYear();
      let years = '';
      for (let y = thisYear; y >= thisYear - 6; y--) {
        years += `<option value="${y}" ${y == defaultTahun ? 'selected' : ''}>${y}</option>`;
      }
      selT.innerHTML = years;
    }

    // Refetch when either dropdown changes.
    function changePeriodParts() {
      defaultBulan = parseInt(document.getElementById('periodBulan').value, 10);
      defaultTahun = parseInt(document.getElementById('periodTahun').value, 10);
      makeTable('REPORT');
    }

    /* -- EXPORT -- */
    function toggleExport() {
      document.getElementById('exportDrop').classList.toggle('open');
    }
    document.addEventListener('click', function (e) {
      const wrap = document.getElementById('exportWrap');
      if (wrap && !wrap.contains(e.target)) {
        document.getElementById('exportDrop').classList.remove('open');
      }
    });
    function doExport(fmt) {
      document.getElementById('exportDrop').classList.remove('open');
      if (fmt === 'Print') { window.print(); return; }
      exportDelimited(fmt);
    }
    function exportDelimited(fmt) {
      const rows = [['Perkiraan', 'Keterangan', 'Saldo Awal',
        'Mutasi Debet', 'Mutasi Kredit',
        'Penyesuaian Debet', 'Penyesuaian Kredit',
        'Rugi/Laba Debet', 'Rugi/Laba Kredit',
        'Saldo Akhir']];
      accountGroups.forEach(g => {
        if (typeFilter !== 'all' && typeFilter !== g.key) return;
        g.accounts.forEach(a => {
          if (hideZero && isZero(a)) return;
          rows.push([a.code, a.name, a.awD, a.mD, a.mK, a.jpD, a.jpK, a.rlD, a.rlK, a.akD]);
        });
      });
      const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
      const ext = (fmt === 'Excel') ? 'xls' : 'csv';
      const blob = new Blob(['?' + csv], { type: 'text/csv;charset=utf-8;' });
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = 'NeracaLajur_' + currentPeriod.replace('/', '-') + '.' + ext;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      showToast('??', 'Data diekspor sebagai ' + fmt);
    }

    /* -- DRILL DOWN (real ledger via Sp_LapJurnal) -- */
    function openDrill(acc, group) {
      const awal = netAwal(acc);
      const sa = netAkhir(acc);
      document.getElementById('dpTitle').textContent = acc.name || acc.code;
      document.getElementById('dpSub').textContent = 'Kode: ' + acc.code + ' - ' + group.label;
      document.getElementById('dpMeta').innerHTML = `
    <div class="dp-meta-item"><span class="dp-meta-label">Saldo Awal</span><span class="dp-meta-val ${awal < 0 ? 'neg' : ''}">${fmtFull(awal)}</span></div>
    <div class="dp-meta-item"><span class="dp-meta-label">Mutasi Debet</span><span class="dp-meta-val" style="color:#1D4ED8">${fmtFull(acc.mD)}</span></div>
    <div class="dp-meta-item"><span class="dp-meta-label">Mutasi Kredit</span><span class="dp-meta-val" style="color:#7C3AED">${fmtFull(acc.mK)}</span></div>
    <div class="dp-meta-item"><span class="dp-meta-label">Saldo Akhir</span><span class="dp-meta-val ${sa < 0 ? 'neg' : ''}" style="font-size:16px">${fmtFull(sa)}</span></div>
  `;
      document.getElementById('dpBody').innerHTML = '<div class="dp-section-title">Memuat rincian transaksi...</div>';
      document.getElementById('drillOverlay').classList.add('open');
      document.getElementById('drillPanel').classList.add('open');

      $.ajax({
        url   : ledgerUrl,
        type  : 'get',
        data  : { perkiraan: acc.code, inputBulan: defaultBulan, inputTahun: defaultTahun, divisi: '-' },
        success: function (rows) { renderDrillBody(acc, rows || []); },
        error  : function () {
          document.getElementById('dpBody').innerHTML =
            '<div style="padding:12px;background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;color:#B91C1C;font-size:12.5px">Gagal memuat rincian transaksi.</div>';
        }
      });
    }

    function renderDrillBody(acc, rows) {
      const sa = netAkhir(acc);
      let running = netAwal(acc);
      let totD = 0, totK = 0;
      let body = `<tr>
      <td colspan="3" style="font-style:italic;color:var(--muted)">Saldo Awal</td>
      <td class="num">-</td><td class="num">-</td>
      <td class="num ${running < 0 ? 'neg' : ''}" style="font-weight:600">${fmtFull(running)}</td>
    </tr>`;

      rows.forEach(e0 => {
        // Sp_ReportBukuTambahan mixes column casing (Debet, kredit, Nobukti…),
        // so normalise keys to lowercase before reading them.
        const e = {};
        Object.keys(e0).forEach(k => { e[k.toLowerCase()] = e0[k]; });

        const d = num(e.debet), k = num(e.kredit);
        totD += d; totK += k;
        running += d - k;
        const tgl = (typeof format_date === 'function') ? format_date(e.tanggal) : (e.tanggal || '');
        const nb = (e.nobukti != null ? String(e.nobukti) : '');
        const jn = (e.jenis != null ? String(e.jenis).trim() : '');
        const nbJs = nb.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        const jnJs = jn.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        body += `<tr class="kas-clickable" title="Klik untuk lihat ${jenisTitle(jn)} ${nb}" onclick="openVoucher('${nbJs}','${jnJs}')">
      <td style="white-space:nowrap">${tgl}</td>
      <td><span class="ref-badge">${nb}</span></td>
      <td>${e.keterangan != null ? e.keterangan : ''}</td>
      <td class="num">${d ? fmtFull(d) : '-'}</td>
      <td class="num">${k ? fmtFull(k) : '-'}</td>
      <td class="num ${running < 0 ? 'neg' : ''}" style="font-weight:600">${fmtFull(running)}</td>
    </tr>`;
      });

      if (!rows.length) {
        body += `<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:14px">Tidak ada transaksi pada periode ini</td></tr>`;
      }

      document.getElementById('dpBody').innerHTML = `
    <div class="dp-section-title">Rincian Transaksi - Periode ${currentPeriod}</div>
    <table class="ledger-table">
      <thead>
        <tr>
          <th>Tanggal</th><th>No Bukti</th><th>Keterangan</th>
          <th class="num">Debet</th><th class="num">Kredit</th><th class="num">Saldo Berjalan</th>
        </tr>
      </thead>
      <tbody>${body}</tbody>
      <tfoot>
        <tr class="ledger-total">
          <td colspan="3" style="font-weight:800">Total Periode ${currentPeriod}</td>
          <td class="num">${fmtFull(totD)}</td>
          <td class="num">${fmtFull(totK)}</td>
          <td class="num ${sa < 0 ? 'neg' : ''}">${fmtFull(sa)}</td>
        </tr>
      </tfoot>
    </table>`;
    }

    function closeDrill() {
      document.getElementById('drillOverlay').classList.remove('open');
      document.getElementById('drillPanel').classList.remove('open');
    }

    // The voucher drill (openVoucher / openKasharian / openInvoice / closeKasharian)
    // and the shared fmtRp / invDate / fmtDMY helpers now live in
    // public/js/report-table.js, configured via window.ReportTableConfig above.

    /* -- TOAST -- */
    function showToast(icon, msg) {
      const t = document.getElementById('toast');
      document.getElementById('ti').textContent = icon;
      document.getElementById('tm').textContent = msg;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 3000);
    }

    document.getElementById('printTime').textContent = new Date().toLocaleString('id-ID');

</script>

@endsection
