/*
 * tabel-memuat.js - lapisan "Memuat data..." untuk tabel daftar (#page1) di semua menu
 * Purchasing & Marketing, di semua tab. Di-include lewat resources/views/partials/tabel-memuat.blade.php
 * di awal @section('js') tiap halaman (sesudah jQuery & DataTables dimuat layout).
 *
 * Masalah utamanya: hampir semua halaman memuat datanya dengan $.ajax({ async:false }).
 * Selama request sinkron berjalan browser TIDAK menggambar apa pun, jadi spinner yang
 * dipasang tepat sebelum request tidak pernah terlihat. Solusinya: lapisan dipasang
 * DULU, browser diberi kesempatan menggambar satu frame, baru kode halaman dijalankan.
 * Animasi spinner & fade-in memakai transform/opacity, jadi tetap berjalan di thread
 * compositor walau thread utama sedang terblokir request sinkron.
 *
 * Titik yang ditangani (tanpa mengubah logika halaman):
 *  1. Saat halaman dibuka  - handler $(document).ready ditahan (jQuery.holdReady) dan event
 *                            window 'load' ditunda sampai lapisan sempat tergambar.
 *  2. Pindah tab           - klik link tab ditunda 1 frame lalu dikirim ulang.
 *  3. Filter / tombol      - 'change' pada input/select dan klik tombol di #page1, serta
 *                            tombol dialog alertify (mis. konfirmasi Otorisasi), ditunda
 *                            1 frame lalu dikirim ulang.
 *  4. Request async        - $.ajax async yang jalan saat daftar tampil.
 *  5. DataTables ajax      - event processing.dt (server-side: cari, sortir, pindah halaman).
 * Lapisan dilepas begitu tidak ada lagi request jQuery yang berjalan ($.active === 0).
 * Halaman yang sudah punya lapisan memuat sendiri (.dataTables_processing / .po-loading-chip)
 * tetap memakai miliknya - lapisan ini otomatis disembunyikan supaya tidak dobel.
 *
 * Elemen yang tidak ingin ditunda bisa diberi atribut data-tm-lewati.
 */
(function (window, document) {
  'use strict'

  var $ = window.jQuery
  if (!$ || window.TabelMemuat) { return }

  var BATAS_SESI_MS = 45000     // pengaman: lapisan tidak pernah tertinggal selamanya
  var TINGGI_MIN = 170          // tabel yang masih kosong tetap dapat lapisan yang cukup tinggi
  var TUNGGU_LOAD_MS = 400      // lama lapisan awal menunggu event window 'load'

  /* ---------------- Tampilan ---------------- */
  var css =
    '.tm-lapisan{position:fixed;z-index:1020;display:none;background:rgba(255,255,255,.62);cursor:progress;}' +
    '.tm-lapisan.tm-aktif{display:block;animation:tmMuncul .42s ease-out both;}' +
    '.tm-lapisan.tm-langsung{animation:none;opacity:1;}' +
    // ~250ms pertama masih transparan: proses yang cepat (mis. pindah tab tanpa memuat apa pun,
    // ~160ms termasuk animasi fade tab) selesai sebelum lapisan terlihat - tidak berkedip.
    '@keyframes tmMuncul{0%,60%{opacity:0}100%{opacity:1}}' +
    '.tm-chip{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);display:inline-flex;' +
    'align-items:center;gap:9px;white-space:nowrap;padding:9px 18px;border-radius:999px;' +
    'background:rgba(31,36,48,.92);color:#fff;font-size:12.5px;font-weight:600;line-height:1.2;' +
    'box-shadow:0 8px 22px rgba(0,0,0,.18);}' +
    '.tm-spin{width:14px;height:14px;flex:0 0 14px;box-sizing:border-box;border:2px solid rgba(255,255,255,.35);' +
    'border-top-color:#fff;border-radius:50%;animation:tmPutar .6s linear infinite;}' +
    '@keyframes tmPutar{to{transform:rotate(360deg)}}'

  var gaya = document.createElement('style')
  gaya.setAttribute('data-tabel-memuat', '')
  gaya.appendChild(document.createTextNode(css))
  ;(document.head || document.documentElement).appendChild(gaya)

  var lapisan = document.createElement('div')
  lapisan.className = 'tm-lapisan'
  lapisan.setAttribute('aria-live', 'polite')
  lapisan.innerHTML = '<span class="tm-chip"><span class="tm-spin"></span><span class="tm-teks">Memuat data...</span></span>'
  var lapisanTerpasang = false

  function pasangLapisan () {
    if (lapisanTerpasang || !document.body) { return }
    document.body.appendChild(lapisan)
    lapisanTerpasang = true
  }

  /* ---------------- Utilitas ---------------- */
  function terlihat (el) {
    if (!el || !el.isConnected || !el.getClientRects().length) { return false }
    return window.getComputedStyle(el).visibility !== 'hidden'
  }

  function page1 () { return document.getElementById('page1') }

  function daftarTampil () { return terlihat(page1()) }

  // Jalankan fn setelah browser sempat menggambar satu frame. Cadangan setTimeout dipakai
  // karena requestAnimationFrame tidak berjalan di tab browser yang sedang di latar.
  function setelahGambar (fn) {
    var sudah = false
    function jalan () {
      if (sudah) { return }
      sudah = true
      fn()
    }
    window.requestAnimationFrame(function () { setTimeout(jalan, 0) })
    setTimeout(jalan, 150)
  }

  // Lapisan memuat milik halaman sendiri sedang tampil? (purchaseOrder, so, cetaktandaterima, dst.)
  function adaLapisanHalaman (root) {
    var el = root.querySelectorAll('.dataTables_processing, .po-memuat-awal, .po-loading-chip')
    for (var i = 0; i < el.length; i++) {
      if (terlihat(el[i]) && window.getComputedStyle(el[i]).display !== 'none') { return true }
    }
    return false
  }

  // Kotak yang ditutup: gabungan semua tabel daftar yang sedang tampil di #page1 (tab aktif),
  // dipotong oleh area #content supaya tidak menutupi header/sidebar.
  function kotakTarget () {
    var root = page1()
    if (!terlihat(root) || adaLapisanHalaman(root)) { return null }

    var kandidat = []
    var semua = root.querySelectorAll('.dataTables_wrapper, table')
    for (var i = 0; i < semua.length; i++) {
      var el = semua[i]
      if (el.closest('.modal') || !terlihat(el)) { continue }
      kandidat.push(el)
    }
    // Buang yang berada di dalam kandidat lain (tabel di dalam wrapper DataTables, tabel bersarang).
    var utama = kandidat.filter(function (el) {
      for (var j = 0; j < kandidat.length; j++) {
        if (kandidat[j] !== el && kandidat[j].contains(el)) { return false }
      }
      return true
    })
    if (!utama.length) { utama = [root] }

    var r = { top: Infinity, left: Infinity, right: -Infinity, bottom: -Infinity }
    utama.forEach(function (el) {
      var b = el.getBoundingClientRect()
      if (!b.width && !b.height) { return }
      r.top = Math.min(r.top, b.top)
      r.left = Math.min(r.left, b.left)
      r.right = Math.max(r.right, b.right)
      r.bottom = Math.max(r.bottom, b.bottom)
    })
    if (r.top === Infinity) { return null }

    // Tabel yang belum dibungkus DataTables (masih kosong) bisa hanya selebar isi thead-nya.
    // Lebar lapisan disamakan dengan tab aktif / #page1 supaya selalu menutup area daftar.
    var wadah = null
    var pane = root.querySelectorAll('.tab-pane.active')
    for (var p = 0; p < pane.length; p++) {
      if (terlihat(pane[p]) && pane[p].contains(utama[0])) { wadah = pane[p] }
    }
    var bw = (wadah || root).getBoundingClientRect()
    if (bw.width) {
      r.left = Math.min(r.left, bw.left)
      r.right = Math.max(r.right, bw.right)
    }

    var area = document.getElementById('content')
    var batas = area ? area.getBoundingClientRect() : { top: 0, left: 0, right: window.innerWidth, bottom: window.innerHeight }
    var atas = Math.max(0, batas.top)
    var kiri = Math.max(0, batas.left)
    var kanan = Math.min(window.innerWidth, batas.right)
    var bawah = Math.min(window.innerHeight, batas.bottom)

    if (r.bottom - r.top < TINGGI_MIN) { r.bottom = r.top + TINGGI_MIN }
    var k = {
      top: Math.max(r.top, atas),
      left: Math.max(r.left, kiri),
      right: Math.min(r.right, kanan),
      bottom: Math.min(r.bottom, bawah)
    }
    if (k.right - k.left < 40 || k.bottom - k.top < 40) { return null }
    return k
  }

  /* ---------------- Sesi memuat ---------------- */
  var sesi = {}
  var nomorSesi = 0
  var idLoop = 0
  var modeLangsung = false

  function adaSesi () {
    var ada = false
    var kini = Date.now()
    Object.keys(sesi).forEach(function (k) {
      if (kini - sesi[k] > BATAS_SESI_MS) { delete sesi[k] } else { ada = true }
    })
    return ada
  }

  function posisikan () {
    pasangLapisan()
    var k = adaSesi() ? kotakTarget() : null
    if (!k) {
      lapisan.classList.remove('tm-aktif', 'tm-langsung')
      if (!adaSesi()) { modeLangsung = false }
      return
    }
    lapisan.style.top = k.top + 'px'
    lapisan.style.left = k.left + 'px'
    lapisan.style.width = (k.right - k.left) + 'px'
    lapisan.style.height = (k.bottom - k.top) + 'px'
    lapisan.classList.toggle('tm-langsung', modeLangsung)
    lapisan.classList.add('tm-aktif')
  }

  function loop () {
    idLoop = 0
    posisikan()
    if (adaSesi()) { idLoop = window.requestAnimationFrame(loop) }
  }

  function mulai (kunci, langsung) {
    kunci = kunci || ('s' + (++nomorSesi))
    sesi[kunci] = Date.now()
    if (langsung) { modeLangsung = true }
    // Posisikan seketika supaya frame berikutnya sudah menggambar lapisan di tempat yang benar.
    posisikan()
    if (!idLoop) { idLoop = window.requestAnimationFrame(loop) }
    return kunci
  }

  function selesai (kunci) {
    delete sesi[kunci]
    if (!idLoop) { idLoop = window.requestAnimationFrame(loop) }
    // Cadangan: requestAnimationFrame tidak berjalan di tab latar, lapisan tetap harus lepas.
    setTimeout(function () { if (!adaSesi()) { posisikan() } }, 80)
  }

  // Jalankan fn setelah semua request jQuery yang sedang berjalan beres.
  function setelahTenang (fn) {
    setTimeout(function cek () {
      if ($.active > 0) {
        $(document).one('ajaxStop', function () { setTimeout(cek, 0) })
        return
      }
      fn()
    }, 0)
  }

  function selesaiSetelahTenang (kunci) {
    setelahTenang(function () { selesai(kunci) })
  }

  /* ---------------- 1. Saat halaman dibuka ---------------- */
  if (document.readyState === 'loading') {
    $.holdReady(true)

    var kunciAwal = mulai('awal', true)
    var siapSelesai = false
    var loadTertahan = false

    var kirimLoad = function () {
      if (!siapSelesai || !loadTertahan) { return }
      loadTertahan = false
      // Sesi 'load' dimulai sebelum sesi awal dilepas, jadi lapisannya tidak sempat hilang.
      var kunciLoad = mulai('load', true)
      selesai(kunciAwal)
      setelahGambar(function () {
        var ev = new Event('load')
        ev.__tm = true
        try { window.dispatchEvent(ev) } finally { selesaiSetelahTenang(kunciLoad) }
      })
    }

    // Capture di window berjalan lebih dulu daripada window.onload / $(window).on('load')
    // milik halaman. Event aslinya dihentikan lalu dikirim ulang setelah semua handler
    // ready selesai dan lapisan sempat tergambar - urutan ready -> load tetap terjaga.
    window.addEventListener('load', function (e) {
      if (e.__tm) { return }
      e.stopImmediatePropagation()
      loadTertahan = true
      kirimLoad()
    }, true)

    document.addEventListener('DOMContentLoaded', function () {
      mulai('awal', true)
      setelahGambar(function () {
        // Didaftarkan terakhir, jadi berjalan sesudah semua handler ready halaman.
        $(function () {
          setTimeout(function () {
            siapSelesai = true
            // Event load sudah datang & tertahan: sesi 'load' mengambil alih (lihat kirimLoad).
            kirimLoad()
            // Halaman yang memuat datanya di ready: tunggu request-nya beres. Event load yang
            // belum datang ditunggu sebentar supaya lapisan tidak hilang lalu muncul lagi
            // pada halaman yang memuat datanya di window.onload.
            setelahTenang(function () {
              setTimeout(function () { selesai(kunciAwal) }, TUNGGU_LOAD_MS)
            })
          }, 0)
        })
        $.holdReady(false)
      })
    })
  }

  /* ---------------- 2 & 3. Klik tab / tombol, ubah filter ---------------- */
  var SEL_TAB = 'a[data-toggle="tab"], a[data-toggle="pill"], [role="tab"][href^="#"]'
  var SEL_TOMBOL = 'button, .btn, input[type="button"], input[type="submit"]'

  function pemicuKlik (e) {
    var t = e.target
    if (!(t instanceof Element) || e.button !== 0 || !daftarTampil()) { return null }
    if (t.closest('[data-tm-lewati]')) { return null }

    // Tombol dialog alertify (konfirmasi Otorisasi / Hapus, dll.) - dialognya di <body>.
    var aj = t.closest('.ajs-button')
    if (aj) { return aj }

    if (t.closest('.modal')) { return null }

    var tab = t.closest(SEL_TAB)
    if (tab) {
      if (tab.classList.contains('active') || tab.classList.contains('disabled')) { return null }
      return tab
    }

    var root = page1()
    var btn = t.closest(SEL_TOMBOL)
    if (!btn || !root.contains(btn) || btn.disabled) { return null }
    if (btn.matches('[data-toggle="dropdown"], [data-dismiss]')) { return null }
    return btn
  }

  function pemicuUbah (e) {
    var t = e.target
    if (!(t instanceof Element) || !daftarTampil()) { return null }
    if (!t.matches('select, input') || t.type === 'file') { return null }
    var root = page1()
    if (!root.contains(t) || t.closest('.modal, table, [data-tm-lewati]')) { return null }
    return t
  }

  function tunda (e, buatUlang, tab) {
    e.preventDefault()
    e.stopImmediatePropagation()
    var target = e.target
    var kunci = mulai()
    var akhiri = function () { selesaiSetelahTenang(kunci) }
    setelahGambar(function () {
      var tungguTab = false
      try {
        if (target.isConnected) {
          var ev = buatUlang()
          ev.__tm = true
          target.dispatchEvent(ev)
        }
        // Tab ber-animasi fade: handler shown.bs.tab (tempat banyak halaman menggambar
        // tabel tab itu) baru jalan ~150ms kemudian - tunggu event-nya.
        if (tab && $(tab).hasClass('active')) {
          tungguTab = true
          var sudah = false
          var lanjut = function () {
            if (sudah) { return }
            sudah = true
            $(tab).off('shown.bs.tab', lanjut)
            akhiri()
          }
          $(tab).on('shown.bs.tab', lanjut)
          setTimeout(lanjut, 600)
        }
      } finally {
        if (!tungguTab) { akhiri() }
      }
    })
  }

  window.addEventListener('click', function (e) {
    if (e.__tm || !e.isTrusted) { return }
    var pemicu = pemicuKlik(e)
    if (!pemicu) { return }
    var tab = pemicu.matches(SEL_TAB) ? pemicu : null
    tunda(e, function () {
      return new MouseEvent('click', {
        bubbles: true,
        cancelable: true,
        composed: true,
        view: window,
        detail: e.detail,
        screenX: e.screenX,
        screenY: e.screenY,
        clientX: e.clientX,
        clientY: e.clientY,
        ctrlKey: e.ctrlKey,
        shiftKey: e.shiftKey,
        altKey: e.altKey,
        metaKey: e.metaKey,
        button: e.button,
        buttons: e.buttons
      })
    }, tab)
  }, true)

  window.addEventListener('change', function (e) {
    if (e.__tm || !e.isTrusted || !pemicuUbah(e)) { return }
    tunda(e, function () { return new Event('change', { bubbles: true }) })
  }, true)

  /* ---------------- 4. Request async saat daftar tampil ---------------- */
  $(document).on('ajaxSend', function (e, xhr, opt) {
    if (!opt || opt.async === false || !daftarTampil()) { return }
    if (document.querySelector('.modal.show')) { return }
    var kunci = mulai()
    xhr.always(function () { setTimeout(function () { selesai(kunci) }, 0) })
  })

  /* ---------------- 5. DataTables ajax / server-side ---------------- */
  $(document).on('processing.dt', function (e, settings, tampil) {
    if (!settings || !settings.nTable || !$(settings.nTable).closest('#page1').length) { return }
    var kunci = 'dt-' + (settings.sTableId || settings.sInstance)
    if (tampil) { mulai(kunci) } else { selesai(kunci) }
  })

  $(document).on('shown.bs.tab', function () { if (adaSesi()) { posisikan() } })

  /* ---------------- API manual ---------------- */
  window.TabelMemuat = {
    tampil: function (kunci) { return mulai(kunci || 'manual') },
    sembunyi: function (kunci) { selesai(kunci || 'manual') },
    // Jalankan fn (boleh berisi $.ajax async:false) setelah lapisan sempat tergambar.
    jalankan: function (fn) {
      var kunci = mulai()
      setelahGambar(function () {
        try { fn() } finally { selesaiSetelahTenang(kunci) }
      })
    }
  }
})(window, document)
