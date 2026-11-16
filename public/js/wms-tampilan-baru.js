/* ============================================================================
 * wms-tampilan-baru.js - tabel daftar ajcwms bergaya newSML (pola menu newpo).
 *
 * Dimuat layout newmaster setelah datatables.min.js, sebelum @yield('js') halaman.
 *
 * Cara kerja: $.fn.dataTable dibungkus. Setiap kali halaman membuat DataTable pada
 * TABEL UTAMA (punya id, berada di area konten, BUKAN di dalam .modal), opsi halaman
 * tetap dipakai apa adanya lalu hanya ditambah opsi tampilan:
 *   - toolbar "Cari data" + "Tampilkan 10/25/50/100/Semua" di atas tabel;
 *   - kotak scroll .po-table-wrap (header menempel) + info & nomor halaman di bawah;
 *   - pesan kosong "Tidak ada data".
 * Isi data, urutan, columnDefs, event, dan fungsi halaman tidak diubah.
 *
 * Pengecualian:
 *   - tabel di dalam modal (pemilih, tabel item) tidak disentuh;
 *   - opsi `dom` halaman yang sudah memakai po-table-wrap (mis. newpo) -> dilewati;
 *   - <table data-wms-asli> -> dilewati sepenuhnya;
 *   - <table data-wms-paging="false"> -> tanpa halaman/"Tampilkan" (semua baris tetap di DOM).
 *
 * Jumlah baris awal: pageLength/lengthMenu halaman kalau ada (mis. "All" di master
 * gudang), selain itu 10 seperti newSML. Pilihan Tampilkan & kata kunci pencarian
 * dipertahankan saat halaman menggambar ulang tabel (destroy + init di loadAll()).
 * ========================================================================== */
(function ($) {
  if (!$ || !$.fn || !$.fn.dataTable || $.fn.dataTable.wmsTerbungkus) { return }

  var DOM_TABEL = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
  var PILIHAN_LEN = [10, 25, 50, 100, -1]
  var keadaan = {} // per id tabel: { len, search }

  function tabelUtama ($t) {
    if ($t.length !== 1 || !$t.is('table') || !$t[0].id) { return false }
    if ($t.is('[data-wms-asli]')) { return false }
    if ($t.closest('.modal').length) { return false }
    return $t.closest('#content-blade').length > 0
  }

  function panjangAwal (o) {
    var n = o.pageLength !== undefined ? o.pageLength : o.iDisplayLength
    if (n === undefined && o.lengthMenu) {
      n = $.isArray(o.lengthMenu[0]) ? o.lengthMenu[0][0] : o.lengthMenu[0]
    }
    n = Number(n)
    return (n === -1 || n > 0) ? n : 10
  }

  function api (id) {
    return $('#' + id).DataTable()
  }

  function pasangToolbar ($t, o, st) {
    var id = $t[0].id
    var bar = $('.po-toolbar[data-wms-tabel="' + id + '"]')
    if (bar.length) { return }

    var cari = o.searching === false || o.bFilter === false
    var paging = st.paging

    bar = $('<div class="po-toolbar wms-toolbar"></div>').attr('data-wms-tabel', id)
    if (!cari) {
      $('<input type="search" class="po-search-inp" placeholder="Cari data">')
        .val(st.search)
        .on('input', function () {
          st.search = this.value
          if ($.fn.dataTable.isDataTable('#' + id)) { api(id).search(this.value).draw() }
        })
        .appendTo(bar)
    }
    if (paging) {
      var pilihan = PILIHAN_LEN.slice()
      if (pilihan.indexOf(st.len) === -1) { pilihan.splice(pilihan.length - 1, 0, st.len) }
      var sel = $('<select class="po-len-inp"></select>')
      pilihan.forEach(function (n) {
        $('<option></option>').val(n).text(n === -1 ? 'Semua' : n).appendTo(sel)
      })
      sel.val(String(st.len)).on('change', function () {
        var n = Number(this.value)
        st.len = (n === -1 || n > 0) ? n : 10
        if ($.fn.dataTable.isDataTable('#' + id)) { api(id).page.len(st.len).draw() }
      })
      $('<div class="po-len-wrap"></div>')
        .append('<label>Tampilkan</label>')
        .append(sel)
        .appendTo(bar)
    }
    // Tombol halaman yang ditandai data-wms-toolbar="<id tabel>" (mis. "Add User") dipindah
    // ke kanan toolbar - elemennya sama (onclick/id tetap), hanya posisinya.
    var tombol = $('[data-wms-toolbar="' + id + '"]')
    if (tombol.length) {
      $('<div class="po-toolbar-act"></div>').append(tombol).appendTo(bar)
    }
    if (bar.children().length) { $t.before(bar) }
  }

  function gabungOpsi ($t, opsiHalaman) {
    var o = $.extend(true, {}, opsiHalaman || {})
    var id = $t[0].id
    var st = keadaan[id]
    if (!st) {
      st = keadaan[id] = {
        len: panjangAwal(o),
        search: '',
        paging: $t.attr('data-wms-paging') !== 'false'
      }
    }

    pasangToolbar($t, o, st)

    o.dom = DOM_TABEL
    if (st.paging) {
      o.paging = true
      delete o.bPaginate
      o.lengthChange = false
      delete o.bLengthChange
      o.pageLength = st.len
      delete o.iDisplayLength
    } else {
      o.paging = false
    }
    if (st.search) { o.search = $.extend({}, o.search, { search: st.search }) }
    o.language = $.extend({
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }, o.language)

    var drawHalaman = o.drawCallback || o.fnDrawCallback
    delete o.fnDrawCallback
    o.drawCallback = function () {
      if (drawHalaman) { drawHalaman.apply(this, arguments) }
      setTimeout(wmsAturTinggiTabel, 0)
    }
    return o
  }

  var asli = $.fn.dataTable
  var bungkus = function (opsi) {
    var $t = this
    var sudah = $t.length === 1 && $.fn.dataTable.isDataTable($t[0])
    var dilewati = opsi && typeof opsi.dom === 'string' && opsi.dom.indexOf('po-table-wrap') !== -1
    if (!sudah && !dilewati && (opsi === undefined || $.isPlainObject(opsi)) && tabelUtama($t)) {
      $t.addClass('data-table')
      arguments[0] = gabungOpsi($t, opsi)
    }
    return asli.apply(this, arguments)
  }
  $.extend(bungkus, asli)
  bungkus.prototype = asli.prototype
  bungkus.wmsTerbungkus = true
  $.fn.dataTable = bungkus

  // Kotak scroll tabel setinggi sisa layar di #content (yang discroll hanya isi tabel),
  // hanya bila di area konten cuma ada SATU tabel daftar yang terlihat - halaman yang
  // menampilkan beberapa tabel sekaligus tetap memakai tinggi bawaan (65vh).
  function wmsAturTinggiTabel () {
    var area = document.getElementById('content')
    var blade = document.getElementById('content-blade')
    if (!area || !blade) { return }
    var wraps = $(blade).find('.po-table-wrap').filter(function () {
      return this.offsetParent !== null && !$(this).closest('.modal').length
    })
    if (wraps.length !== 1) { return }
    var wrap = wraps[0]
    wrap.style.maxHeight = 'none'
    var padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
    var batasBawah = area.getBoundingClientRect().bottom - padBawah
    var kotak = wrap.getBoundingClientRect()
    var dt = $(wrap).closest('.dataTables_wrapper')[0]
    var bawah = dt ? dt.getBoundingClientRect().bottom - kotak.bottom : 0
    var sisa = batasBawah - kotak.top - bawah - 4
    wrap.style.maxHeight = Math.max(240, Math.floor(sisa)) + 'px'
  }
  window.wmsAturTinggiTabel = wmsAturTinggiTabel

  // Tab berganti: lebar kolom tabel yang tadinya tersembunyi dihitung ulang.
  $(document).on('shown.bs.tab', function () {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust()
    wmsAturTinggiTabel()
  })

  var timerResize = null
  $(window).on('resize', function () {
    if (timerResize) { clearTimeout(timerResize) }
    timerResize = setTimeout(wmsAturTinggiTabel, 150)
  })

  // Export to Excel halaman report membaca isi <table> di DOM. Dengan halaman (paging)
  // hanya baris halaman aktif yang ada di DOM, jadi selama ekspor semua baris (yang lolos
  // pencarian, sama seperti sebelumnya) ditampilkan dulu lalu dikembalikan.
  $(window).on('load', function () {
    if (typeof window.exportTableToExcel !== 'function' || window.exportTableToExcel.wmsTerbungkus) { return }
    var eksporAsli = window.exportTableToExcel
    var eksporBungkus = function (tableID) {
      var sel = '#' + tableID
      if (!tableID || !$(sel).length || !$.fn.dataTable.isDataTable(sel)) {
        return eksporAsli.apply(this, arguments)
      }
      var t = $(sel).DataTable()
      var len = t.page.len()
      var hal = t.page()
      t.page.len(-1).draw(false)
      try {
        return eksporAsli.apply(this, arguments)
      } finally {
        t.page.len(len).draw(false)
        t.page(hal).draw(false)
      }
    }
    eksporBungkus.wmsTerbungkus = true
    window.exportTableToExcel = eksporBungkus
  })

  // Animasi "Memuat data..." seperti newSML (purchaseOrder: poTampilMemuatAwal / poProses).
  // wmsMemuat(true|false, target?) - lapisan di atas area tab/tabel (default #myTabContent,
  //   kalau tidak ada .wms-kartu pertama) untuk pemuatan AJAX async.
  // wmsProses(teks, kerja) - lapisan seluruh layar untuk pekerjaan SINKRON (async:false):
  //   lapisan digambar dulu, baru kerja() dijalankan, supaya animasinya sempat tampil.
  window.wmsMemuat = function (tampil, target) {
    var $wadah = target ? $(target) : $('#myTabContent')
    if (!$wadah.length) { $wadah = $('.wms-kartu').first() }
    if (!$wadah.length) { return }
    $wadah.children('.wms-memuat').remove()
    if (!tampil) { return }
    $wadah.addClass('wms-memuat-wadah').append(
      '<div class="wms-memuat"><span class="wms-loading-chip"><span class="wms-loading-spin"></span>Memuat data...</span></div>'
    )
  }

  // Ditambahkan 2026-10-23 (menu Report): isi sel tabel yang digambar ulang dari AJAX.
  // null/undefined -> '' (sama dengan tampilan awal dari Blade, dulu tertulis "null"), dan karakter
  // HTML di-escape seperti {{ }} Blade.
  window.wmsTeks = function (v) {
    if (v === null || v === undefined) { return '' }
    return String(v).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
    })
  }

  // Ditambahkan 2026-10-23 (menu Report): unduh isi tabel sebagai file .xls lewat Blob.
  // Dulu lewat data-URI: karakter "#" (ada di 73 nama barang) memotong isi file, dan tabel besar
  // bisa gagal diunduh. Isi file (HTML tabel) tetap sama.
  window.wmsUnduhXls = function (tableHTML, filename) {
    var blob = new Blob(['﻿', tableHTML], { type: 'application/vnd.ms-excel' })
    if (navigator.msSaveOrOpenBlob) { return navigator.msSaveOrOpenBlob(blob, filename) }
    var a = document.createElement('a')
    a.href = URL.createObjectURL(blob)
    a.download = filename
    document.body.appendChild(a)
    a.click()
    setTimeout(function () { URL.revokeObjectURL(a.href); a.remove() }, 1000)
  }

  var wmsSibuk = 0
  window.wmsProses = function (teks, kerja) {
    if (wmsSibuk) { return kerja() }
    wmsSibuk++
    var lapisan = document.getElementById('wmsProsesOverlay')
    if (!lapisan) {
      lapisan = document.createElement('div')
      lapisan.id = 'wmsProsesOverlay'
      lapisan.className = 'wms-proses-overlay'
      lapisan.innerHTML = '<span class="wms-loading-chip"><span class="wms-loading-spin"></span><span class="wms-proses-teks"></span></span>'
      document.body.appendChild(lapisan)
    }
    lapisan.querySelector('.wms-proses-teks').textContent = teks || 'Memproses...'
    lapisan.style.display = 'block'
    requestAnimationFrame(function () {
      setTimeout(function () {
        try {
          kerja()
        } finally {
          wmsSibuk = Math.max(0, wmsSibuk - 1)
          if (!wmsSibuk) { lapisan.style.display = 'none' }
        }
      }, 0)
    })
  }
})(window.jQuery)
