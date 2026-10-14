/* ============================================================================
 * master-list.js
 *
 * Tabel daftar menu Master (resources/views/master/**) di layout newmasterTest -
 * menggantikan public/js/masterTable.js (yang tetap dipakai halaman berkas).
 *
 * 1) Toolbar: kotak cari #tabel_filter_visual dan dropdown "Tampilkan"
 *    #tabel_length_visual (lihat master/partials/toolbarMaster.blade.php) diikat ke
 *    tabel yang sedang aktif (MasterList.aktif, bawaan '#tabel').
 *
 * 2) MasterList.opsi(extra)  -> opsi DataTables standar (pola dom .po-table-wrap +
 *    info + pager, disalin dari gudang/ubahkemasanbarang & accounting/pengajuandph).
 *    MasterList.selesai(sel)  -> dipanggil setelah $(sel).DataTable({...}): pasang ulang
 *    kata cari, pindahkan #rtBar ke atas tabel, atur tinggi kotak scroll.
 *
 * 3) MasterList.kolom({...}) -> fitur geser & sembunyikan kolom (window.ReportTable,
 *    public/js/report-table.js) KHUSUS tabel yang kolom datanya lebih dari 5. Susunan
 *    kolom disimpan per user lewat globalfunctions_doLoadHeader/doSimpanHeader
 *    (DBSIMPANHEADER) - pola yang sama dengan accounting/pengajuandph.blade.php, TIDAK
 *    memakai HeaderTableController.
 *
 * jQuery + DataTables wajib sudah termuat (disediakan layout newmasterTest).
 * ========================================================================== */
(function () {
  'use strict';

  var DOM = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";

  var ML = {
    aktif: '#tabel',
    // URL diisi dari data-* #masterListCfg (toolbarMaster.blade.php) saat dipakai.
    urlLoadHeader: '',
    urlSimpanHeader: ''
  };

  function panjangHalaman() {
    var n = Number($('#tabel_length_visual').val());
    return (n === -1 || n > 0) ? n : 10;
  }

  function kataCari() {
    return String($('#tabel_filter_visual').val() || '');
  }

  function adaTabel(sel) {
    return !!(sel && $.fn.DataTable && $.fn.DataTable.isDataTable(sel));
  }

  ML.panjang = panjangHalaman;

  // Dipakai halaman yang menyusun opsi DataTables sendiri (mis. server-side masterbarang).
  ML.dom = DOM;
  ML.bahasa = {
    emptyTable: 'Tidak ada data',
    zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
  };

  ML.opsi = function (extra) {
    return $.extend({
      lengthChange: false,
      paging: true,
      searching: true,
      pageLength: panjangHalaman(),
      order: [],
      columnDefs: [{ targets: [0], orderable: false }],
      dom: DOM,
      language: ML.bahasa
    }, extra || {});
  };

  // #rtBar (bar kolom tersembunyi milik ReportTable) diletakkan tepat sebelum wrapper
  // DataTables - DataTables membungkus ulang tabel tiap init.
  function pindahBar(sel) {
    var bar = document.getElementById('rtBar');
    var tabel = document.querySelector(sel);
    if (!bar || !tabel) { return; }

    var acuan = tabel;
    if (adaTabel(sel) && tabel.id) {
      acuan = document.getElementById(tabel.id + '_wrapper') || tabel;
    }
    if (acuan.previousElementSibling !== bar) {
      acuan.parentNode.insertBefore(bar, acuan);
    }
  }

  // Kotak scroll tabel setinggi sisa ruang #content, supaya yang discroll hanya isi tabel.
  // Sama pola dengan aturTinggiTabel() di gudang/ubahkemasanbarang.blade.php.
  function aturTinggi(sel) {
    var tabel = document.querySelector(sel);
    if (!tabel) { return; }
    var page = tabel.closest('.po-list-page');
    var area = document.getElementById('content');
    var wrap = tabel.closest('.po-table-wrap');
    if (!page || !area || !wrap || page.offsetParent === null) { return; }

    wrap.style.maxHeight = 'none';

    var padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
    var batasBawah = area.getBoundingClientRect().bottom - padBawah;
    var kotak = wrap.getBoundingClientRect();
    var bawah = page.getBoundingClientRect().bottom - kotak.bottom;

    var sisa = batasBawah - kotak.top - bawah - 4;
    wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
  }

  ML.aturTinggi = function (sel) { aturTinggi(sel || ML.aktif); };

  ML.selesai = function (sel) {
    sel = sel || ML.aktif;
    if (!adaTabel(sel)) { return; }
    var dt = $(sel).DataTable();
    var kata = kataCari();
    // Kata cari dipasang lagi setelah tabel dibuat ulang (mis. sesudah simpan/hapus).
    if (kata && dt.search() !== kata) { dt.search(kata).draw(); }
    pindahBar(sel);
    aturTinggi(sel);
  };

  // Ganti tabel yang dikendalikan toolbar (halaman dengan tab, mis. mastergiro).
  ML.pakai = function (sel) {
    ML.aktif = sel;
    if (adaTabel(sel)) {
      var dt = $(sel).DataTable();
      var kata = kataCari();
      if (dt.search() !== kata) { dt.search(kata); }
      if (dt.page.len() !== panjangHalaman()) { dt.page.len(panjangHalaman()); }
      dt.draw(false);
    }
    pindahBar(sel);
    aturTinggi(sel);
  };

  /* ---------- Toolbar ---------- */
  var tundaCari = null;
  $(document).on('input', '#tabel_filter_visual', function () {
    var kata = this.value;
    clearTimeout(tundaCari);
    // Ditunda sebentar: tabel server-side (masterbarang, dst) memanggil server tiap draw.
    tundaCari = setTimeout(function () {
      if (adaTabel(ML.aktif)) { $(ML.aktif).DataTable().search(kata).draw(); }
    }, 300);
  });

  $(document).on('change', '#tabel_length_visual', function () {
    if (adaTabel(ML.aktif)) { $(ML.aktif).DataTable().page.len(panjangHalaman()).draw(); }
    aturTinggi(ML.aktif);
  });

  $(window).on('resize', function () { aturTinggi(ML.aktif); });

  /* ---------- Kolom geser & sembunyi (tabel > 5 kolom) ----------
   * opt = {
   *   href     : 'masterbarang'  (kunci simpan per menu),
   *   kolom    : [[field, label, tampil(1/0), 'varchar'|'float'|'date', 0, desimal], ...],
   *   onChange : fungsi render ulang tabel halaman,
   *   table    : selektor tabel (bawaan '#tabel'),
   *   mode     : nomor susunan di DBSIMPANHEADER (bawaan '1'; halaman dua tabel memakai 1 & 2)
   * }
   * Bisa dipanggil sekali per tabel (mis. mastergiro: Giro Dibuka & Giro Diterima). Tabel yang
   * sedang dipakai ReportTable diaktifkan lewat onActivate (window.gcart_header / g_href /
   * g_modeReport / doSimpanHeader / doSetHeader ditukar ke milik tabel itu).
   * Susunan tersimpan yang field-nya sudah tidak ada di bawaan dibuang, dan kolom bawaan
   * yang belum ada di susunan tersimpan ditambahkan di ujung - supaya perubahan daftar
   * kolom di kemudian hari tidak membuat tabel rusak. Label selalu diambil dari bawaan. */
  var kolomInst = {};
  var kolomTerakhir = null;

  function salinBawaan(inst) {
    return inst.opt.kolom.map(function (c) { return c.slice(); });
  }

  function gabungTersimpan(inst, teks) {
    var bawaan = salinBawaan(inst);
    var peta = {};
    bawaan.forEach(function (c) { peta[c[0]] = c; });

    var hasil = [];
    String(teks).split('||').forEach(function (bagian) {
      var p = bagian.split(';;');
      var b = peta[p[0]];
      if (!b || b._dipakai) { return; }
      b._dipakai = true;
      var c = b.slice();
      c[2] = Number(p[2]) === 1 ? 1 : 0;
      var des = Number(p[5]);
      if (!isNaN(des)) { c[5] = des; }
      hasil.push(c);
    });
    bawaan.forEach(function (b) { if (!b._dipakai) { hasil.push(b.slice()); } });
    return hasil;
  }

  function simpanHeader(inst) {
    var teks = (inst.cart || []).map(function (c) {
      // Label tidak disimpan (selalu diambil dari bawaan) supaya muat di DBSIMPANHEADER.header varchar(1000).
      return [c[0], '', c[2], c[3], c[4], c[5]].join(';;');
    }).join('||');

    $.ajax({
      url: ML.urlSimpanHeader,
      type: 'get',
      async: false,
      data: {
        href: inst.opt.href,
        mode: inst.mode,
        header: teks,
        issubtotal: 0,
        isgrandtotal: 0
      },
      error: function (err) {
        console.log(err);
        if (window.alertify) { alertify.warning('Gagal menyimpan pengaturan kolom'); }
      }
    });
  }

  function muatHeader(inst) {
    var teks = '';
    $.ajax({
      url: ML.urlLoadHeader,
      type: 'get',
      async: false,
      data: { href: inst.opt.href, mode: inst.mode },
      success: function (res) {
        teks = (res && res.length > 0) ? (res[0].header || '') : '';
      }
    });
    return teks;
  }

  // Jadikan tabel `inst` yang dilayani report-table.js (variabel global miliknya).
  function aktifkan(inst) {
    kolomTerakhir = inst;
    window.g_href = inst.opt.href;
    window.g_modeReport = inst.mode;
    window.gsum_issubtotal = 0;
    window.gsum_isgrandtotal = 0;
    window.gcart_header = inst.cart;
    window.doSimpanHeader = function () {
      // report-table.js memutasi window.gcart_header; array yang sama dengan inst.cart.
      inst.cart = window.gcart_header;
      simpanHeader(inst);
    };
    // Dipanggil tombol "Reset kolom" di bar (reset = true).
    window.doSetHeader = function (mode, reset) {
      var teks = reset ? '' : muatHeader(inst);
      if (teks) {
        inst.cart = gabungTersimpan(inst, teks);
      } else {
        inst.cart = salinBawaan(inst);
        simpanHeader(inst);
      }
      window.gcart_header = inst.cart;
    };
  }

  ML.kolom = function (opt) {
    var cfgEl = document.getElementById('masterListCfg');
    if (cfgEl) {
      ML.urlLoadHeader = ML.urlLoadHeader || cfgEl.getAttribute('data-load-header');
      ML.urlSimpanHeader = ML.urlSimpanHeader || cfgEl.getAttribute('data-simpan-header');
    }

    var sel = opt.table || '#tabel';
    var inst = { opt: opt, sel: sel, mode: String(opt.mode || '1'), cart: [] };
    kolomInst[sel] = inst;

    aktifkan(inst);
    window.doSetHeader(inst.mode, false);

    if (typeof ReportTable === 'undefined') { return; }

    ReportTable.init({
      table: sel,
      bar: opt.bar || '#rtBar',
      onChange: opt.onChange,
      onActivate: function () { aktifkan(inst); }
    });

    // DataTables memasang sort di tiap <th>, sedangkan roda gigi/pegangan geser milik
    // ReportTable didelegasikan di <thead>. Klik pada keduanya dihentikan di fase capture
    // lalu ditembakkan ulang langsung ke <thead> - sama seperti memorialkoreksi/newpo.
    var thead = document.querySelector(sel + ' thead');
    if (thead && !thead.dataset.mlGuard) {
      thead.dataset.mlGuard = '1';
      var ulangi = false;
      thead.addEventListener('click', function (e) {
        if (ulangi) { return; }
        var el = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip');
        if (!el) { return; }
        e.stopPropagation();
        e.preventDefault();
        ulangi = true;
        var ev = new MouseEvent('click', { bubbles: false, cancelable: true, view: window });
        Object.defineProperty(ev, 'target', { value: el, configurable: true });
        thead.dispatchEvent(ev);
        ulangi = false;
      }, true);
    }
  };

  // Aktifkan tabel `sel` untuk ReportTable (halaman dengan lebih dari satu tabel).
  function pakaiKolom(sel) {
    var inst = sel ? kolomInst[sel] : kolomTerakhir;
    if (!inst) { return null; }
    aktifkan(inst);
    if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use(inst.sel); }
    return inst;
  }

  // Kolom yang tampil - WAJIB hasil filter() dari cart (referensi yang sama), karena
  // ReportTable.headHtml() mencari index global lewat indexOf().
  ML.kolomTampil = function (sel) {
    var inst = pakaiKolom(sel);
    var cart = inst ? inst.cart : (window.gcart_header || []);
    return cart.filter(function (c) { return Number(c[2]) === 1; });
  };

  // <tr> header: kolom Actions (tetap, tidak bisa digeser) + kolom dari cart.
  ML.headHtml = function (cols, sel) {
    if (sel) { pakaiKolom(sel); }
    var html;
    if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
      html = ReportTable.headHtml(cols);
    } else {
      console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan.');
      html = '<tr>' + cols.map(function (c) { return '<th scope="col">' + c[1] + '</th>'; }).join('') + '</tr>';
    }
    return html.replace('<tr>', '<tr><th style="padding: 4px 12px;" scope="col">Actions</th>');
  };

  // Tab berganti: bar kolom tersembunyi ikut menampilkan milik tabel yang aktif.
  ML.pakaiKolom = function (sel) {
    if (pakaiKolom(sel) && typeof ReportTable !== 'undefined' && ReportTable.refresh) { ReportTable.refresh(); }
  };

  /* ---------- Render sel untuk tabel berkolom dinamis ---------- */
  function angka(v, des) {
    var n = Number(String(v === null || v === undefined || v === '' ? 0 : v).split(',').join(''));
    if (isNaN(n)) { return v; }
    var d = Number(des);
    if (isNaN(d) || d < 0) { d = 0; }
    return n.toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d });
  }

  function tanggal(v) {
    if (!v) { return ''; }
    var t = String(v).substring(0, 10).split('-');
    return t.length === 3 ? t[2] + '/' + t[1] + '/' + t[0] : v;
  }

  // Desimal < 1 dari SQL Server datang tanpa nol di depan (".00", "-.50") - jadikan "0.00", "-0.50".
  function nolDepan(v) {
    if (v === null || v === undefined) { return ''; }
    return String(v).replace(/^(-?)\.(\d)/, '$10.$2');
  }

  ML.angka = angka;
  ML.tanggal = tanggal;
  ML.nolDepan = nolDepan;

  // Satu <td> untuk kolom c milik baris item. `khusus` = { FIELD: function (item) { return '<td>..</td>' } }
  // untuk kolom yang tampilannya tidak sekadar teks (badge, centang, dsb).
  ML.sel = function (item, c, khusus) {
    if (khusus && typeof khusus[c[0]] === 'function') { return khusus[c[0]](item); }
    var v = item[c[0]];
    if (v === null || v === undefined) { v = ''; }
    if (c[3] === 'float') { return '<td class="text-right">' + angka(v, c[5]) + '</td>'; }
    if (c[3] === 'date') { return '<td>' + tanggal(v) + '</td>'; }
    return '<td>' + nolDepan(v) + '</td>';
  };

  // Satu <tr> utuh: sel Actions + sel tiap kolom yang tampil.
  ML.baris = function (item, cols, aksiHtml, khusus, atributTr) {
    return '<tr' + (atributTr ? ' ' + atributTr : '') + '><td class="text-center">' + aksiHtml + '</td>'
      + cols.map(function (c) { return ML.sel(item, c, khusus); }).join('') + '</tr>';
  };

  // DataTables server-side: definisi `columns` mengikuti kolom yang tampil.
  // `khusus` = { FIELD: { render: fn, orderable: false, className: '...' } }
  ML.kolomServer = function (cols, aksiRender, khusus) {
    var hasil = [{ data: null, render: aksiRender, orderable: false, searchable: false, className: 'text-center' }];
    cols.forEach(function (c) {
      var def = { data: c[0], defaultContent: '' };
      if (c[3] === 'float') {
        def.className = 'text-right';
        def.render = function (d) { return angka(d, c[5]); };
      } else if (c[3] === 'date') {
        def.render = function (d) { return tanggal(d); };
      }
      if (khusus && khusus[c[0]]) { def = $.extend(def, khusus[c[0]]); }
      hasil.push(def);
    });
    return hasil;
  };

  /* ---------- Tombol Filter toolbar (#modalFilterMaster, toolbarMaster.blade.php) ----------
     Pola sama dengan #modalFilterPO di purchasing/purchaseOrder.blade.php: pilihan di modal
     baru berlaku setelah Terapkan, Batal mengembalikan pilihan terakhir yang diterapkan.
     Dropdown di dalam modal memakai id lama halaman (mis. #perkiraanCustomer), jadi loadAll()
     halaman tetap membaca nilainya seperti dulu. "Aktif" = nilai berbeda dari opsi pertama. */
  var filter = { nilai: {} };

  function filterSelect() {
    return $('#modalFilterMaster select');
  }

  function filterTampil() {
    var jml = 0;
    var teks = [];
    filterSelect().each(function () {
      var pertama = $(this).find('option').first().val();
      if ($(this).val() !== pertama) { jml++; }
      var label = $('label[for="' + this.id + '"]').first().text();
      teks.push((label ? label + ': ' : '') + $(this).find('option:selected').text());
    });
    $('#masterFilterBadge').text(jml + ' aktif');
    $('#masterFilterAktif').text(teks.join(' · '));
  }

  function filterSimpan() {
    filter.nilai = {};
    filterSelect().each(function () { filter.nilai[this.id] = $(this).val(); });
  }

  function filterKembalikan() {
    filterSelect().each(function () {
      if (filter.nilai.hasOwnProperty(this.id)) { $(this).val(filter.nilai[this.id]); }
    });
  }

  filter.terapkan = function () {
    filterSimpan();
    filterTampil();
    $('#modalFilterMaster').modal('hide');
    if (typeof window.loadAll === 'function') { window.loadAll(); }
  };

  filter.reset = function () {
    filterSelect().each(function () {
      $(this).val($(this).find('option').first().val());
    });
    filter.terapkan();
  };

  $(function () {
    var modal = $('#modalFilterMaster');
    if (!modal.length) { return; }
    // Modal dipindah ke <body> supaya tidak terkurung stacking context .card.
    modal.appendTo('body');
    filterSimpan();
    filterTampil();
    modal.on('show.bs.modal', filterKembalikan);
    modal.on('hidden.bs.modal', filterKembalikan);
  });

  ML.filter = filter;

  window.MasterList = ML;
})();
