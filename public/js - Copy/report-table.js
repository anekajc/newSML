/* ============================================================================
 * report-table.js — reusable voucher drill + interactive table header for
 * report* pages
 *
 * Two concerns live in this file:
 *
 * (1) The bottom "voucher" panel that opens when a report row (carrying a
 * No Bukti + Jenis) is clicked:
 *   - B* types (BBK/BBM/BKK/BKM/BMM/BJK) -> Bukti Kas/Bank/Memorial/Jurnal
 *     (EXEC dbo.CetakKasharian), differing only by title.
 *   - INVC                               -> sales Invoice
 *     (EXEC dbo.CetakInvoicePenjualan), with the invoice layout.
 *   - BPL                                -> Faktur Pembelian (purchase receipt)
 *     (EXEC dbo.CetakPenerimaanACC), with the purchase-invoice layout.
 *   - BP                                 -> Bukti Pemakaian Internal ACC
 *     (EXEC dbo.CetakPemakaianBahanACC), with the internal-usage layout.
 *
 * Pairs with public/css/report-table.css for styling.
 *
 * --- How to reuse on another report page ---------------------------------
 *   1. <link> public/css/report-table.css
 *   2. Before this file, declare the endpoints:
 *        <script>
 *          window.ReportTableConfig = {
 *            kasUrl    : "{{ url('<route>_doKasharian') }}",
 *            invoiceUrl: "{{ url('<route>_doInvoice') }}",
 *            lpbUrl    : "{{ url('<route>_doLpb') }}"
 *          };
 *        </script>
 *   3. <script src="public/js/report-table.js">
 *   4. From each clickable row:  onclick="openVoucher(noBukti, jenis)"
 *
 * The #kasPanel markup is created automatically if the page doesn't have it,
 * so steps 1-4 are all a new page needs. jQuery must be present (used for ajax).
 * Globals exposed: openVoucher, openKasharian, openInvoice, closeKasharian,
 * jenisTitle, jenisFromNo, loadingHtml, and the helpers num / fmtRp / invDate / fmtDMY.
 *
 * (2) window.ReportTable — an interactive <thead> for report pages that render
 * columns from gcart_header (see report/masterreport2.blade.php): drag judul
 * kolom untuk mengurutkan, plus menu roda gigi per kolom (sembunyikan / desimal
 * / tampilkan total), and a bar above the table listing hidden columns + an
 * optional "Tampilan" view switcher. Merged in from the former
 * public/js/report-table-v2.js (still on disk as an archived copy, no longer
 * loaded). Paired with the CSS block of the same origin in report-table.css.
 *
 * Pemakaian di halaman report:
 *
 *   ReportTable.init({
 *     table    : '#mainTable',
 *     bar      : '#rtBar',
 *     onChange : render,                       // fungsi render tabel halaman
 *     views    : {                             // opsional (Detail/Rekap saja)
 *       label   : 'Tampilan',
 *       options : [{ value: '0', label: 'Detail', desc: 'Rincian per baris' }],
 *       get     : function () { return globalReportMode; },
 *       set     : function (v) { ... }
 *     }
 *   });
 *
 *   // di dalam render() halaman, ganti pembuatan <thead> menjadi:
 *   thead.innerHTML = ReportTable.headHtml(cols);
 * ==========================================================================*/
(function () {
  'use strict';

  function cfg() { return window.ReportTableConfig || {}; }

  /* ── formatting helpers (exposed globally so report pages can reuse them) ── */
  if (typeof window.num !== 'function') {
    window.num = function (v) {
      if (v === null || v === undefined || v === '') return 0;
      var n = parseFloat(v);
      return isNaN(n) ? 0 : n;
    };
  }
  // Indonesian currency, e.g. 1000000 -> "1.000.000,00"
  if (typeof window.fmtRp !== 'function') {
    window.fmtRp = function (v) {
      return window.num(v).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
  }
  // Full Rupiah, no abbreviation: 3500000 -> "Rp 3.500.000". Zero -> "Rp 0";
  // null/undefined/'' -> "-". Used by report tables/KPIs (replaces the old
  // abbreviated fmtN that produced "jt"/"rb"/"M"). fmtFull is the same format.
  if (typeof window.fmtN !== 'function') {
    window.fmtN = function (v) {
      if (v === null || v === undefined || v === '') return '-';
      var n = window.num(v);
      return (n < 0 ? '-' : '')  + Math.abs(n).toLocaleString('id-ID');
    };
  }
  if (typeof window.fmtFull !== 'function') {
    window.fmtFull = window.fmtN;
  }
  // Parse a SQL datetime string ("2026-06-24 00:00:00.000") into a Date.
  if (typeof window.invDate !== 'function') {
    window.invDate = function (v) {
      if (v == null) return null;
      var p = String(v).slice(0, 10).split('-');
      if (p.length === 3) { var d = new Date(+p[0], +p[1] - 1, +p[2]); return isNaN(d) ? null : d; }
      var d2 = new Date(v); return isNaN(d2) ? null : d2;
    };
  }
  if (typeof window.fmtDMY !== 'function') {
    window.fmtDMY = function (d) {
      if (!d) return '';
      var dd = String(d.getDate()).padStart(2, '0');
      var mm = String(d.getMonth() + 1).padStart(2, '0');
      return dd + '/' + mm + '/' + d.getFullYear();
    };
  }
  // Inline "loading" label with a small Bootstrap spinner, e.g. for a report footer
  // while data is fetched. Assign via innerHTML (NOT textContent) so the spinner
  // markup renders:  el.innerHTML = loadingHtml('Memuat data…')
  if (typeof window.loadingHtml !== 'function') {
    window.loadingHtml = function (text) {
      var msg = (text != null && text !== '') ? text : 'Memuat data...';
      return msg + ' <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>';
    };
  }

  /* ── running "saldo berjalan" for kartu-style reports (Hutang/Piutang) ──
     Recomputes the Saldo Rp / Saldo $ columns as a per-group running balance,
     like the Trial Balance "Saldo Berjalan" drill: the first row of each group
     seeds from that column's existing value (Saldo Awal from the SP); each later
     row = previous + debet - kredit. Selisih Kurs is NOT applied. Computed over the
     full per-group sequence (independent of any search filter) and stamped onto each
     row as r._run so the table render and the export stay consistent.

     opts (all optional; defaults fit the Hutang/Piutang kartu reports):
       groupKey : row field to group by            (default 'nama'; pass null/false
                  for a single continuous sequence, e.g. a one-customer ledger)
       prop     : property stamped on each row      (default '_run')
       saldo    : { <saldoCol>: {debet, kredit} }   (default SaldoRp/SaldoD map) */
  var RUNNING_SALDO_DEFAULT = {
    SaldoRp: { debet: 'debet1',  kredit: 'kredit1'  },
    SaldoD:  { debet: 'debetd1', kredit: 'kreditd1' }
  };
  function rsNum(v) {
    return (typeof window.currencyNormalizer === 'function') ? window.currencyNormalizer(v) : window.num(v);
  }
  function rsPick(r, key) {
    if (r == null) return undefined;
    if (r[key] !== undefined) return r[key];
    var lk = String(key).toLowerCase();
    for (var k in r) { if (k.toLowerCase() === lk) return r[k]; }
    return undefined;
  }
  // True for a saldo-berjalan column (default 'SaldoRp' / 'SaldoD').
  if (typeof window.isRunningSaldo !== 'function') {
    window.isRunningSaldo = function (key, opts) {
      var saldo = (opts && opts.saldo) || RUNNING_SALDO_DEFAULT;
      return Object.prototype.hasOwnProperty.call(saldo, key);
    };
  }
  // Stamp each row with r._run = { <saldoCol>: runningBalance }. Returns rows.
  if (typeof window.assignRunningSaldo !== 'function') {
    window.assignRunningSaldo = function (rows, opts) {
      opts = opts || {};
      var saldo    = opts.saldo || RUNNING_SALDO_DEFAULT;
      var groupKey = ('groupKey' in opts) ? opts.groupKey : 'nama';   // null/false → one sequence
      var prop     = opts.prop  || '_run';
      var keys = Object.keys(saldo);
      var acc = {};   // group value -> { saldoCol: running balance }
      (rows || []).forEach(function (r) {
        var gv = groupKey ? rsPick(r, groupKey) : '';
        var g = String(gv == null ? '' : gv).trim();
        var first = !(g in acc);
        if (first) acc[g] = {};
        var a = acc[g], out = {};
        keys.forEach(function (col) {
          if (first) {
            a[col] = rsNum(rsPick(r, col));                                    // seed: Saldo Awal from SP
          } else {
            var c = saldo[col];
            a[col] += rsNum(rsPick(r, c.debet)) - rsNum(rsPick(r, c.kredit));  // + debet - kredit
          }
          out[col] = a[col];
        });
        r[prop] = out;
      });
      return rows;
    };
  }

  /* ── voucher title per transaction type (Jenis) ── */
  // Pages may override/extend via window.ReportTableConfig.jenisTitle.
  var JENIS_TITLE = {
    BBK: 'BUKTI BANK KELUAR',
    BBM: 'BUKTI BANK MASUK',
    BKK: 'BUKTI KAS KELUAR',
    BKM: 'BUKTI KAS MASUK',
    BMM: 'BARANG MEMORIAL MASUK',
    BJK: 'BUKTI JURNAL KOREKSI',
    INVC: 'INVOICE',
    BPL: 'LAPORAN PEMBELIAN',
    BP: 'BEBAN PEMBELIAN'
  };
  window.jenisTitle = function (j) {
    var map = Object.assign({}, JENIS_TITLE, (cfg().jenisTitle || {}));
    var key = (j != null ? String(j).trim().toUpperCase() : '');
    return map[key] || 'BUKTI KAS';
  };

  // Map a document-number prefix to the voucher Jenis code openVoucher dispatches on.
  // Most prefixes equal their Jenis (BBK, BKK, BKM, …); the purchase-receipt number
  // prefixes "LPB" and "PBJ" are both the "BPL" voucher type (Faktur Pembelian).
  // Pages may extend via window.ReportTableConfig.noJenisAlias = { PREFIX: 'JENIS' }.
  var NO_JENIS_ALIAS = { LPB: 'BPL', PBJ: 'BPL' };
  // Derive the voucher Jenis from a document number like "SML/LPB/00018/0426" (its
  // 2nd slash-segment), applying known aliases. Returns '' if not derivable — pass
  // the result to openVoucher(no, jenisFromNo(no)).
  if (typeof window.jenisFromNo !== 'function') {
    window.jenisFromNo = function (no) {
      var alias = Object.assign({}, NO_JENIS_ALIAS, (cfg().noJenisAlias || {}));
      var parts = String(no == null ? '' : no).split('/');
      var seg = (parts.length > 1 ? parts[1] : '').trim().toUpperCase();
      return alias[seg] || seg;
    };
  }

  /* ── panel plumbing ── */
  // Build the bottom panel if the page didn't ship its own #kasPanel markup.
  function ensurePanel() {
    var panel = document.getElementById('kasPanel');
    if (panel) return panel;
    panel = document.createElement('div');
    panel.className = 'kas-panel';
    panel.id = 'kasPanel';
    panel.innerHTML =
      '<div class="kas-head">' +
        '<div class="kas-title" id="kasTitle">Bukti Kas</div>' +
        '<div class="dp-close" onclick="closeKasharian()"><i class="bi bi-x"></i></div>' +
      '</div>' +
      '<div class="kas-body" id="kasBody"></div>';
    (document.querySelector('.tb-report') || document.body).appendChild(panel);
    return panel;
  }

  function showLoading(panel, title) {
    document.getElementById('kasTitle').textContent = title;
    document.getElementById('kasBody').innerHTML =
      '<div class="dp-section-title" style="padding:18px">Memuat ' + title + '...</div>';
    panel.classList.add('open');
  }
  function showError(msg) {
    document.getElementById('kasBody').innerHTML =
      '<div style="margin:18px;padding:12px;background:#FEF2F2;border:1px solid #FECACA;' +
      'border-radius:8px;color:#B91C1C;font-size:12.5px">' + msg + '</div>';
  }
  // Lowercase every key of a row (the procs mix casing: Debet/kredit/Nobukti…).
  // Tolerate a non-array payload (e.g. an error page / single object) so the
  // voucher degrades to "no data" instead of throwing ".map is not a function".
  function lc(rows) {
    if (!Array.isArray(rows)) rows = (rows && typeof rows === 'object') ? [rows] : [];
    return rows.map(function (r) {
      var o = {}; Object.keys(r).forEach(function (k) { o[k.toLowerCase()] = r[k]; }); return o;
    });
  }

  /* ── dispatch: INVC -> invoice, BPL -> faktur pembelian, BP -> bukti pemakaian,
        else -> Bukti Kas/Bank/etc. ── */
  window.openVoucher = function (nobukti, jenis) {
    var key = (jenis != null ? String(jenis).trim().toUpperCase() : '');
    if (key === 'INVC') { window.openInvoice(nobukti); }
    else if (key === 'BPL') { window.openLpb(nobukti); }
    else if (key === 'BP') { window.openBp(nobukti); }
    else { window.openKasharian(nobukti, jenis); }
  };

  window.openKasharian = function (nobukti, jenis) {
    var title = window.jenisTitle(jenis);
    var panel = ensurePanel();
    showLoading(panel, title + ' - ' + nobukti);
    $.ajax({
      url: cfg().kasUrl, type: 'get', data: { nobukti: nobukti },
      success: function (rows) { renderKasharian(nobukti, title, rows || []); },
      error: function () { showError('Gagal memuat ' + title + '.'); }
    });
  };

  function renderKasharian(nobukti, title, rows) {
    var data = lc(rows);
    var h = data[0] || {};
    var noBukti = h.nobukti != null ? h.nobukti : nobukti;
    var tanggal = h.tanggal != null ? String(h.tanggal).slice(0, 10) : '';   // strip time
    var kepada  = h.kepada  != null ? h.kepada  : '';
    var bank    = h.ketperk != null ? h.ketperk : '';

    var total = 0, items = '';
    data.forEach(function (e, i) {
      var jumlah = window.num(e.jumlahrp);
      total += jumlah;
      items += '<tr>' +
        '<td style="text-align:center">' + (i + 1) + '</td>' +
        '<td>' + (e.perper != null ? e.perper : '') + '</td>' +
        '<td>' + (e.ketper != null ? e.ketper : '') + '</td>' +
        '<td>' + (e.keterangan != null ? e.keterangan : (e.note != null ? e.note : '')) + '</td>' +
        '<td>' + (e.costskb != null ? e.costskb : '') + '</td>' +
        '<td class="num">' + (jumlah ? window.fmtRp(jumlah) : '') + '</td>' +
      '</tr>';
    });
    if (!data.length) {
      items = '<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:14px">Tidak ada data</td></tr>';
    }

    document.getElementById('kasBody').innerHTML =
      '<table class="kas-voucher"><tbody>' +
        '<tr>' +
          '<td class="kas-title-cell" colspan="4" rowspan="2"><div class="kas-doctitle">' + title + '</div></td>' +
          '<td class="kas-lbl">No. Bukti</td><td>' + noBukti + '</td>' +
        '</tr>' +
        '<tr><td class="kas-lbl">Tanggal</td><td>' + tanggal + '</td></tr>' +
        '<tr>' +
          '<td class="kas-lbl">Kepada</td><td colspan="3">' + kepada + '</td><td colspan="2">' + bank + '</td>' +
        '</tr>' +
        '<tr class="kas-colhead">' +
          '<th style="width:42px">No.</th>' +
          '<th style="width:90px">KODE</th>' +
          '<th style="width:200px">NAMA</th>' +
          '<th>KETERANGAN</th>' +
          '<th style="width:130px">SUB COST/SKB</th>' +
          '<th class="num" style="width:150px">JUMLAH</th>' +
        '</tr>' +
        items +
        '<tr class="kas-foot">' +
          '<td colspan="4">Cetak ke : </td>' +
          '<td class="kas-totlbl">Total :</td>' +
          '<td class="num">' + window.fmtRp(total) + '</td>' +
        '</tr>' +
      '</tbody></table>';
  }

  /* ── INVOICE PENJUALAN ── */
  window.openInvoice = function (nobukti) {
    var panel = ensurePanel();
    showLoading(panel, 'INVOICE - ' + nobukti);
    $.ajax({
      url: cfg().invoiceUrl, type: 'get', data: { nobukti: nobukti },
      success: function (rows) { renderInvoice(nobukti, rows || []); },
      error: function () { showError('Gagal memuat Invoice.'); }
    });
  };

  function renderInvoice(nobukti, rows) {
    var data = lc(rows);
    var h = data[0] || {};
    var noBukti = h.nobukti != null ? h.nobukti : nobukti;
    var tgl     = window.invDate(h.tanggal);
    var hari    = window.num(h.hari);
    // Jatuh Tempo = Tanggal + HARI days.
    var jatuh   = tgl ? new Date(tgl.getFullYear(), tgl.getMonth(), tgl.getDate() + hari) : null;
    var pono    = (h.pono != null && String(h.pono).trim() !== '') ? h.pono : '-';
    var pemb    = (h.hari != null && String(h.hari).trim() !== '') ? (hari + ' Hari') : '-';
    var cust    = h.namacustsupp != null ? h.namacustsupp : '';
    var alamat  = h.alamat != null ? h.alamat : '';

    var subtotal = 0, items = '';
    data.forEach(function (e, i) {
      var qty = window.num(e.qty), harga = window.num(e.harga), disc = window.num(e.disc);
      var jumlah = qty * harga * (1 - disc / 100);   // Qty x Harga, less DISC%
      subtotal += jumlah;
      items += '<tr>' +
        '<td style="text-align:center">' + (i + 1) + '</td>' +
        '<td>' + (e.nospb != null ? e.nospb : '') + '</td>' +
        '<td>' + (e.namabrg != null ? e.namabrg : '') + '</td>' +
        '<td style="text-align:center">' + (e.satuan != null ? e.satuan : '') + '</td>' +
        '<td style="text-align:center">' + (e.sattax != null ? e.sattax : '') + '</td>' +
        '<td class="num">' + (qty ? window.fmtRp(qty) : '') + '</td>' +
        '<td class="num">' + (harga ? window.fmtRp(harga) : '') + '</td>' +
        '<td class="num">' + window.fmtRp(disc) + '%</td>' +
        '<td class="num">' + (jumlah ? window.fmtRp(jumlah) : '') + '</td>' +
      '</tr>';
    });
    if (!data.length) {
      items = '<tr><td colspan="9" style="text-align:center;color:var(--muted);padding:14px">Tidak ada data</td></tr>';
    }

    // Totals: SUB TOTAL = sum(JUMLAH); DISKON/UANG MUKA/DPP come from the proc;
    // PPN 11 = SUB TOTAL x 0.11; TOTAL = SUB TOTAL + PPN 11.
    var diskon   = window.num(h.discp);
    // var uangMuka = window.num(h.nuangmuka);
    var dpp      = window.num(h.ndpprp);
    var nilaiPpn = window.num(h.nilaippn);
    var total    = window.num(h.nnetrp);
    var ppn       = nilaiPpn * total / 100;
    function totRow(label, val, bold) {
      return '<tr' + (bold ? ' style="font-weight:800"' : '') + '>' +
        '<td>' + label + '</td>' +
        '<td style="text-align:right;padding-left:40px">' + window.fmtRp(val) + '</td>' +
      '</tr>';
    }

    document.getElementById('kasBody').innerHTML =
      '<div class="inv-head" style="justify-content:flex-start">' +
        '<div class="inv-head-left">' +
          '<div class="inv-cust-lbl">Kepada YTH :</div>' +
          '<div class="inv-cust-name">' + cust + '</div>' +
          '<div class="inv-cust-addr">' + alamat + '</div>' +
        '</div>' +
        '<div class="inv-head-left">' +
          '<div class="inv-doctitle">INVOICE</div>' +
          '<table class="inv-meta"><tbody>' +
            '<tr><td>Nomor</td><td>:</td><td>' + noBukti + '</td></tr>' +
            '<tr><td>Tanggal</td><td>:</td><td>' + window.fmtDMY(tgl) + '</td></tr>' +
            '<tr><td>No PO</td><td>:</td><td>' + pono + '</td></tr>' +
            '<tr><td>Pembayaran</td><td>:</td><td>' + pemb + '</td></tr>' +
            '<tr><td>Jatuh Tempo</td><td>:</td><td>' + window.fmtDMY(jatuh) + '</td></tr>' +
          '</tbody></table>' +
        '</div>' +
      '</div>' +
      '<table class="kas-voucher inv-table inv-noheadborder">' +
        '<thead><tr class="kas-colhead">' +
          '<th style="width:42px">No.</th>' +
          '<th style="width:150px">NO SPB</th>' +
          '<th>NAMA BARANG</th>' +
          '<th style="width:60px">SAT</th>' +
          '<th style="width:80px">SAT TAX</th>' +
          '<th class="num" style="width:80px">QTY</th>' +
          '<th class="num" style="width:120px">HARGA</th>' +
          '<th class="num" style="width:80px">DISKON</th>' +
          '<th class="num" style="width:140px">JUMLAH</th>' +
        '</tr></thead>' +
        '<tbody>' + items + '</tbody>' +
        '<tfoot><tr class="kas-foot">' +
          '<td colspan="8" class="kas-totlbl" style="text-align:right">Total :</td>' +
          '<td class="num">' + window.fmtRp(subtotal) + '</td>' +
        '</tr></tfoot>' +
      '</table>' +
      '<div style="display:flex;justify-content:flex-end;margin-top:8px">' +
        '<table class="inv-meta"><tbody>' +
          totRow('SUB TOTAL', subtotal, false) +
          totRow('DISKON', diskon, false) +
        //   totRow('UANG MUKA', uangMuka, false) +
          totRow('DPP', dpp, false) +
          totRow('PPN 11', ppn, false) +
          totRow('TOTAL', total, true) +
        '</tbody></table>' +
      '</div>';
  }

  /* ── FAKTUR PEMBELIAN (BPL) ── */
  window.openLpb = function (nobukti) {
    var title = window.jenisTitle('BPL');
    var panel = ensurePanel();
    showLoading(panel, title + ' - ' + nobukti);
    $.ajax({
      url: cfg().lpbUrl, type: 'get', data: { nobukti: nobukti },
      success: function (rows) { renderLpb(nobukti, rows || []); },
      error: function () { showError('Gagal memuat ' + title + '.'); }
    });
  };

  function renderLpb(nobukti, rows) {
    var data = lc(rows);
    var h = data[0] || {};
    // value-or-dash for the header meta fields
    function v(x) { return (x != null && String(x).trim() !== '') ? x : '-'; }

    var noBukti  = h.nobukti != null ? h.nobukti : nobukti;
    var tgl      = window.fmtDMY(window.invDate(h.tanggal));

    var subtotal = 0, items = '';
    data.forEach(function (e, i) {
      var qty = window.num(e.qnt), harga = window.num(e.harga), disc = window.num(e.disc);
      var jumlah = harga * qty;   // JUMLAH = HARGA x QTY
      subtotal += jumlah;
      items += '<tr>' +
        '<td style="text-align:center">' + (i + 1) + '</td>' +
        '<td>' + (e.namabrg != null ? e.namabrg : '') + '</td>' +
        '<td>' + (e.kodebrg != null ? e.kodebrg : '') + '</td>' +
        '<td style="text-align:center">' + (e.satuan != null ? e.satuan : '') + '</td>' +
        '<td class="num">' + (qty ? window.fmtRp(qty) : '') + '</td>' +
        '<td class="num">' + (harga ? window.fmtRp(harga) : '') + '</td>' +
        '<td class="num">' + window.fmtRp(disc) + '%</td>' +
        '<td class="num">' + (jumlah ? window.fmtRp(jumlah) : '') + '</td>' +
      '</tr>';
    });
    if (!data.length) {
      items = '<tr><td colspan="8" style="text-align:center;color:var(--muted);padding:14px">Tidak ada data</td></tr>';
    }

    // Totals: SUB TOTAL = sum(JUMLAH); DISKON/UANG MUKA/DPP come from the proc;
    // PPN 11 = SUB TOTAL x 0.11; TOTAL = SUB TOTAL + PPN 11.
    var diskon   = window.num(h.discp);
    var uangMuka = window.num(h.nuangmuka);
    var dpp      = window.num(h.ndpprp);
    var ppn      = window.num(h.nppnrp);
    var total    = window.num(h.nnetrp);
    function totRow(label, val, bold) {
      return '<tr' + (bold ? ' style="font-weight:800"' : '') + '>' +
        '<td>' + label + '</td>' +
        '<td style="text-align:right;padding-left:40px">' + window.fmtRp(val) + '</td>' +
      '</tr>';
    }

    document.getElementById('kasBody').innerHTML =
      '<div class="inv-head">' +
        '<div class="inv-head-left">' +
          '<div class="inv-doctitle">FAKTUR PEMBELIAN</div>' +
          '<table class="inv-meta"><tbody>' +
            '<tr><td>No. FB</td><td>:</td><td>' + noBukti + '</td></tr>' +
            '<tr><td>No. PO</td><td>:</td><td>' + v(h.nopo) + '</td></tr>' +
            '<tr><td>NO. PR/SO</td><td>:</td><td>' + v(h.nosa) + '</td></tr>' +
            '<tr><td>No. PO Cust</td><td>:</td><td>' + v(h.nopocust) + '</td></tr>' +
          '</tbody></table>' +
        '</div>' +
        '<div class="inv-head-right"></div>' +
      '</div>' +
      '<table class="inv-meta" style="margin-bottom:14px"><tbody>' +
        '<tr><td>Tanggal</td><td>:</td><td>' + tgl + '</td></tr>' +
        '<tr><td>No. SJ</td><td>:</td><td>' + v(h.faktursupp) + '</td></tr>' +
        '<tr><td>Diterima Dari</td><td>:</td><td>' + v(h.namacustsupp) + '</td></tr>' +
        '<tr><td>Expedisi</td><td>:</td><td>' + v(h.namaexp) + '</td></tr>' +
      '</tbody></table>' +
      '<table class="kas-voucher inv-table">' +
        '<thead><tr class="kas-colhead">' +
          '<th style="width:42px">No.</th>' +
          '<th>NAMA BARANG</th>' +
          '<th style="width:170px">KODE BRG</th>' +
          '<th style="width:60px">SAT</th>' +
          '<th class="num" style="width:90px">QTY</th>' +
          '<th class="num" style="width:130px">HARGA</th>' +
          '<th class="num" style="width:90px">DISKON</th>' +
          '<th class="num" style="width:150px">JUMLAH</th>' +
        '</tr></thead>' +
        '<tbody>' + items + '</tbody>' +
      '</table>' +
      '<div style="display:flex;justify-content:flex-end;margin-top:8px">' +
        '<table class="inv-meta"><tbody>' +
          totRow('SUB TOTAL', subtotal, false) +
          totRow('DISKON', diskon, false) +
          totRow('UANG MUKA', uangMuka, false) +
          totRow('DPP', dpp, false) +
          totRow('PPN 11', ppn, false) +
          totRow('TOTAL', total, true) +
        '</tbody></table>' +
      '</div>';
  }

  /* ── BUKTI PEMAKAIAN INTERNAL ACC (BP) ── */
  window.openBp = function (nobukti) {
    var title = window.jenisTitle('BP');
    var panel = ensurePanel();
    showLoading(panel, title + ' - ' + nobukti);
    $.ajax({
      url: cfg().bpUrl, type: 'get', data: { nobukti: nobukti },
      success: function (rows) { renderBp(nobukti, rows || []); },
      error: function () { showError('Gagal memuat ' + title + '.'); }
    });
  };

  function renderBp(nobukti, rows) {
    var data = lc(rows);
    var h = data[0] || {};
    var noBukti = h.nobukti != null ? h.nobukti : nobukti;
    var tgl     = window.fmtDMY(window.invDate(h.tanggal));

    var grand = 0, items = '';
    data.forEach(function (e, i) {
      var qty = window.num(e.qnt), hargaSat = window.num(e.hpp);
      var total = qty * hargaSat;   // TOTAL = QTY x HARGA SAT
      grand += total;
      items += '<tr>' +
        '<td style="text-align:center">' + (i + 1) + '</td>' +
        '<td>' + (e.namabrg != null ? e.namabrg : '') + '</td>' +
        '<td style="text-align:center">' + (e.sat != null ? e.sat : '') + '</td>' +
        '<td class="num">' + (qty ? window.fmtRp(qty) : '') + '</td>' +
        '<td class="num">' + (hargaSat ? window.fmtRp(hargaSat) : '') + '</td>' +
        '<td class="num">' + (total ? window.fmtRp(total) : '') + '</td>' +
        '<td class="num">' + window.fmtRp(0) + '</td>' +
        '<td>' + (e.kodeperkiraan != null ? e.kodeperkiraan : '') + '</td>' +
        '<td>' + (e.namaperkiraan != null ? e.namaperkiraan : '') + '</td>' +
      '</tr>';
    });
    if (!data.length) {
      items = '<tr><td colspan="9" style="text-align:center;color:var(--muted);padding:14px">Tidak ada data</td></tr>';
    }

    document.getElementById('kasBody').innerHTML =
      '<div class="inv-head" style="justify-content:flex-start">' +
        '<div class="inv-head-left">' +
          '<div class="inv-doctitle">BUKTI PEMAKAIAN INTERNAL ACC</div>' +
          '<table class="inv-meta"><tbody>' +
            '<tr><td>No</td><td>:</td><td>' + noBukti + '</td></tr>' +
            '<tr><td>Tanggal</td><td>:</td><td>' + tgl + '</td></tr>' +
          '</tbody></table>' +
        '</div>' +
      '</div>' +
      '<table class="kas-voucher inv-table">' +
        '<thead><tr class="kas-colhead">' +
          '<th style="width:42px">No.</th>' +
          '<th>URAIAN BARANG</th>' +
          '<th style="width:70px">SATUAN</th>' +
          '<th class="num" style="width:70px">QTY</th>' +
          '<th class="num" style="width:120px">HARGA SAT</th>' +
          '<th class="num" style="width:130px">TOTAL</th>' +
          '<th class="num" style="width:90px">COST</th>' +
          '<th style="width:90px">COA</th>' +
          '<th style="width:160px">COA</th>' +
        '</tr></thead>' +
        '<tbody>' + items + '</tbody>' +
        '<tfoot><tr class="kas-foot">' +
          '<td colspan="5" class="kas-totlbl" style="text-align:right">Total :</td>' +
          '<td class="num">' + window.fmtRp(grand) + '</td>' +
          '<td colspan="3"></td>' +
        '</tr></tfoot>' +
      '</table>';
  }

  window.closeKasharian = function () {
    var panel = document.getElementById('kasPanel');
    if (panel) panel.classList.remove('open');
  };

  // Close the voucher when clicking outside it. Clicks on a voucher opener are
  // ignored so they switch the voucher instead of closing the panel — this covers
  // both a clickable cell (`.kas-clickable`) and a whole clickable row whose
  // onclick calls openVoucher (single-column pages make the entire <tr> clickable).
  document.addEventListener('click', function (e) {
    var panel = document.getElementById('kasPanel');
    if (!panel || !panel.classList.contains('open')) return;
    if (panel.contains(e.target)) return;
    if (e.target.closest &&
        (e.target.closest('.kas-clickable') || e.target.closest('[onclick*="openVoucher"]'))) return;
    window.closeKasharian();
  });
})();

/* ============================================================================
 * window.ReportTable — interactive table header (merged from the former
 * public/js/report-table-v2.js). See the file-top comment for usage.
 * ==========================================================================*/
(function () {
  "use strict";

  if (window.ReportTable) { return; }

  var cfg      = null;
  var openGidx = -1;   // index kolom (di gcart_header) yang menunya terbuka
  var dragGidx = -1;   // index kolom yang sedang diseret
  var menuEl   = null; // elemen .rt-colmenu di <body>

  /* ---------------- helper ---------------- */

  function cart() {
    return (typeof window.gcart_header !== "undefined" && window.gcart_header) ? window.gcart_header : [];
  }

  function isNumeric(col) {
    return (col && (col[3] === "float" || col[3] === "int"));
  }

  function closestEl(node, sel) {
    return (node && node.closest) ? node.closest(sel) : null;
  }

  function esc(val) {
    return String(val == null ? "" : val)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  function tableEl() {
    return (cfg && cfg.table) ? document.querySelector(cfg.table) : null;
  }

  function barEl() {
    return (cfg && cfg.bar) ? document.querySelector(cfg.bar) : null;
  }

  // Simpan langsung — hanya dipakai bila fungsi masterreport2 tidak tersedia.
  function saveHeader() {
    if (typeof window.doSimpanHeader !== "function" || typeof window.g_href === "undefined") { return; }
    window.doSimpanHeader(window.g_href, window.g_modeReport, cart(), window.gsum_issubtotal, window.gsum_isgrandtotal);
  }

  function fireChange() {
    if (cfg && typeof cfg.onChange === "function") { cfg.onChange(); }
  }

  /* ---------------- <thead> ---------------- */

  // cols = hasil gcart_header.filter(c => c[2] === 1) di halaman. Elemennya
  // adalah referensi ke array yang sama, jadi indexOf() memberi index global.
  function headHtml(cols) {
    var all  = cart();
    var html = "<tr>";

    (cols || []).forEach(function (c) {
      var g   = all.indexOf(c);
      var num = isNumeric(c);

      html += '<th class="rt-th' + (num ? " num" : "") + '" data-gidx="' + g + '">';
      html += '  <div class="th-inner" draggable="true">';
      html += '    <span class="th-grip"><i></i><i></i><i></i><i></i><i></i><i></i></span>';
      html += '    <span class="th-label">' + c[1] + "</span>";
      html += '    <button type="button" class="th-gear' + (openGidx === g ? " active" : "") +
              '" data-rtgear="' + g + '" title="Setting kolom"><i class="bi bi-gear"></i></button>';
      html += "  </div>";
      html += "</th>";
    });

    html += "</tr>";

    // bar ikut disegarkan tiap render supaya jumlah kolom tersembunyi akurat
    renderBar();

    return html;
  }

  function clearDragState() {
    var t = tableEl();
    if (!t) { return; }
    Array.prototype.forEach.call(t.querySelectorAll("thead .drag-over"), function (el) {
      el.classList.remove("drag-over");
    });
    Array.prototype.forEach.call(t.querySelectorAll("thead .th-inner.dragging"), function (el) {
      el.classList.remove("dragging");
    });
  }

  function bindHead(thead) {
    thead.addEventListener("dragstart", function (e) {
      var inner = closestEl(e.target, ".th-inner");
      if (!inner) { return; }
      var th = closestEl(inner, "th.rt-th");
      if (!th) { return; }

      dragGidx = Number(th.getAttribute("data-gidx"));
      inner.classList.add("dragging");
      closeMenu();

      if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = "move";
        try { e.dataTransfer.setData("text/plain", String(dragGidx)); } catch (err) { /* IE */ }
      }
    });

    thead.addEventListener("dragend", function () {
      dragGidx = -1;
      clearDragState();
    });

    thead.addEventListener("dragover", function (e) {
      var th = closestEl(e.target, "th.rt-th");
      if (!th || dragGidx < 0) { return; }
      e.preventDefault();
      if (dragGidx !== Number(th.getAttribute("data-gidx"))) { th.classList.add("drag-over"); }
    });

    thead.addEventListener("dragleave", function (e) {
      var th = closestEl(e.target, "th.rt-th");
      if (th && !th.contains(e.relatedTarget)) { th.classList.remove("drag-over"); }
    });

    thead.addEventListener("drop", function (e) {
      var th = closestEl(e.target, "th.rt-th");
      if (!th) { return; }
      e.preventDefault();

      var from = dragGidx;
      var to   = Number(th.getAttribute("data-gidx"));
      dragGidx = -1;
      clearDragState();
      moveColumn(from, to);
    });

    thead.addEventListener("click", function (e) {
      var gear = closestEl(e.target, "[data-rtgear]");
      if (!gear) { return; }
      e.preventDefault();
      e.stopPropagation();

      var g = Number(gear.getAttribute("data-rtgear"));
      if (openGidx === g) { closeMenu(); return; }
      openGidx = g;
      openMenu(gear, g);
    });
  }

  function moveColumn(from, to) {
    var all = cart();
    if (from < 0 || to < 0 || from === to) { return; }
    if (from >= all.length || to >= all.length) { return; }

    if (typeof window.doMoveHeader === "function") {
      window.doMoveHeader(from, to);
    } else {
      all.splice(to, 0, all.splice(from, 1)[0]);
      saveHeader();
    }

    closeMenu();
    fireChange();
  }

  /* ---------------- menu kolom ---------------- */

  function openMenu(anchor, g) {
    var col = cart()[g];
    if (!col) { return; }

    destroyMenu();

    var html = '<div class="rt-colmenu-item" data-rtact="hide"><span>Sembunyikan kolom</span></div>';

    if (isNumeric(col)) {
      html += '<div class="rt-colmenu-divider"></div>';
      html += '<div class="rt-colmenu-item is-static"><span>Desimal</span>' +
              '<span class="rt-stepper">' +
              '<button type="button" data-rtact="dec-minus">&minus;</button>' +
              "<b>" + Number(col[5]) + "</b>" +
              '<button type="button" data-rtact="dec-plus">+</button>' +
              "</span></div>";
      html += '<div class="rt-colmenu-item is-static"><span>Tampilkan total</span>' +
              '<button type="button" class="rt-miniswitch' + (Number(col[4]) === 1 ? " on" : "") +
              '" data-rtact="total"></button></div>';
    }

    menuEl = document.createElement("div");
    menuEl.className = "rt-colmenu";
    menuEl.innerHTML = html;
    document.body.appendChild(menuEl);

    positionMenu(anchor);
    anchor.classList.add("active");

    menuEl.addEventListener("click", function (e) {
      e.stopPropagation();
      var el = closestEl(e.target, "[data-rtact]");
      if (el) { menuAction(el.getAttribute("data-rtact"), g); }
    });
  }

  function positionMenu(anchor) {
    var r = anchor.getBoundingClientRect();
    var w = menuEl.offsetWidth || 216;
    var h = menuEl.offsetHeight || 120;

    var left = Math.min(r.left, window.innerWidth - w - 12);
    if (left < 8) { left = 8; }

    var top = r.bottom + 6;
    if (top + h > window.innerHeight - 8) { top = Math.max(8, r.top - h - 6); }

    menuEl.style.left = left + "px";
    menuEl.style.top  = top + "px";
  }

  function menuAction(act, g) {
    var all = cart();
    if (!all[g]) { return; }

    if (act === "hide") {
      if (typeof window.doButtonVisibility === "function") { window.doButtonVisibility(g); }
      else { all[g][2] = 0; saveHeader(); }
      closeMenu();
      fireChange();
      return;
    }

    if (act === "dec-minus" || act === "dec-plus") {
      var step = (act === "dec-plus") ? 1 : -1;
      if (typeof window.doSetDesimal === "function") {
        window.doSetDesimal(g, step);
      } else {
        var next = Number(all[g][5]) + step;
        if (next < 0 || next > 4) { return; }
        all[g][5] = next;
        saveHeader();
      }
    } else if (act === "total") {
      if (typeof window.doButtonTotal === "function") {
        window.doButtonTotal(g);
      } else {
        all[g][4] = (Number(all[g][4]) === 1) ? 0 : 1;
        saveHeader();
      }
    } else {
      return;
    }

    // render ulang tabel, lalu buka lagi menu pada kolom yang sama
    fireChange();
    reopenMenu(g);
  }

  function reopenMenu(g) {
    var t = tableEl();
    if (!t) { closeMenu(); return; }

    var gear = t.querySelector('thead [data-rtgear="' + g + '"]');
    if (!gear) { closeMenu(); return; }

    openGidx = g;
    openMenu(gear, g);
  }

  function destroyMenu() {
    if (menuEl && menuEl.parentNode) { menuEl.parentNode.removeChild(menuEl); }
    menuEl = null;
  }

  function closeMenu() {
    openGidx = -1;
    destroyMenu();

    var t = tableEl();
    if (!t) { return; }
    Array.prototype.forEach.call(t.querySelectorAll("thead .th-gear.active"), function (b) {
      b.classList.remove("active");
    });
  }

  /* ---------------- bar di atas tabel ---------------- */

  function renderBar() {
    var bar = barEl();
    if (!bar) { return; }

    var all    = cart();
    var hidden = [];
    all.forEach(function (c, i) { if (Number(c[2]) !== 1) { hidden.push({ col: c, idx: i }); } });

    var items = "";
    if (hidden.length) {
      hidden.forEach(function (h) {
        items += '<div class="rt-drop-item" data-rtshow="' + h.idx + '"><span>' + h.col[1] +
                 '</span><small>tampilkan</small></div>';
      });
    } else {
      items = '<div class="rt-drop-empty">Tidak ada kolom tersembunyi</div>';
    }

    var html = "";
    html += '<div class="rt-drop">';
    html += '  <button type="button" class="rt-hidden-btn" data-rtbar="hidden"' + (hidden.length ? "" : " disabled") + ">";
    html += '    <i class="bi bi-plus-lg"></i><span>' +
            (hidden.length ? hidden.length + " kolom tersembunyi" : "Semua kolom tampil") + "</span>";
    html += "  </button>";
    html += '  <div class="rt-drop-menu">' + items + "</div>";
    html += "</div>";

    if (cfg && cfg.views && cfg.views.options && cfg.views.options.length) { html += viewHtml(); }

    bar.classList.add("rt-bar");
    bar.innerHTML = html;
  }

  function viewHtml() {
    var v       = cfg.views;
    var current = String(v.get ? v.get() : "");
    var label   = "";
    var items   = "";

    v.options.forEach(function (o) {
      var on = (String(o.value) === current);
      if (on) { label = o.label; }
      items += '<div class="rt-drop-item' + (on ? " active" : "") + '" data-rtview="' + esc(o.value) + '">' +
               "<span>" + esc(o.label) + "</span>" +
               (o.desc ? "<small>" + esc(o.desc) + "</small>" : "") +
               "</div>";
    });

    return '<div class="rt-drop">' +
           '  <button type="button" class="rt-view-btn" data-rtbar="view">' +
           '    <i class="bi bi-list"></i><span>' + esc(v.label || "Tampilan") + ": " + esc(label || "-") + "</span>" +
           "  </button>" +
           '  <div class="rt-drop-menu">' + items + "</div>" +
           "</div>";
  }

  function closeBarMenus() {
    var bar = barEl();
    if (!bar) { return; }
    Array.prototype.forEach.call(bar.querySelectorAll(".rt-drop-menu.open"), function (m) {
      m.classList.remove("open");
    });
  }

  function bindBar(bar) {
    bar.addEventListener("click", function (e) {
      var btn = closestEl(e.target, "[data-rtbar]");
      if (btn) {
        e.stopPropagation();
        if (btn.disabled) { return; }
        var menu = btn.parentNode.querySelector(".rt-drop-menu");
        var open = menu && menu.classList.contains("open");
        closeBarMenus();
        closeMenu();
        if (menu && !open) { menu.classList.add("open"); }
        return;
      }

      var show = closestEl(e.target, "[data-rtshow]");
      if (show) {
        e.stopPropagation();
        closeBarMenus();
        var g = Number(show.getAttribute("data-rtshow"));
        if (typeof window.doButtonVisibility === "function") { window.doButtonVisibility(g); }
        else if (cart()[g]) { cart()[g][2] = 1; saveHeader(); }
        fireChange();
        return;
      }

      var view = closestEl(e.target, "[data-rtview]");
      if (view) {
        e.stopPropagation();
        closeBarMenus();
        if (cfg.views && typeof cfg.views.set === "function") { cfg.views.set(view.getAttribute("data-rtview")); }
        renderBar();
      }
    });
  }

  /* ---------------- init ---------------- */

  function init(options) {
    cfg = options || {};

    var t = tableEl();
    if (!t) { return; }

    var thead = t.querySelector("thead");
    if (thead) { bindHead(thead); }

    var bar = barEl();
    if (bar) { bindBar(bar); }

    document.addEventListener("click", function () { closeMenu(); closeBarMenus(); });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" || e.keyCode === 27) { closeMenu(); closeBarMenus(); }
    });

    // capture: ikut menangkap scroll di dalam .table-wrap (overflow:auto)
    window.addEventListener("scroll", function () { closeMenu(); }, true);
    window.addEventListener("resize", function () { closeMenu(); closeBarMenus(); });

    renderBar();
  }

  window.ReportTable = {
    init:     init,
    headHtml: headHtml,
    refresh:  renderBar,
    close:    function () { closeMenu(); closeBarMenus(); }
  };
})();
