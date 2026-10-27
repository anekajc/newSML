{{-- Pasangan JS dari marketing/partials/tampilan-purchasing-css.blade.php. Perilaku tampilan
     yang di purchaseOrder ditulis per tabel (poAturTinggiTabel, poRapikanBarisKosong, tooltip
     tombol aksi, modal pemilih picker-kas) dibuat generik di sini lewat event DataTables/Bootstrap,
     jadi tidak mengubah logika halaman. Di-include di awal @section('js'). --}}
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script>
(function ($) {
  if (!$ || !$.fn) { return }

  /* ---- Teks bawaan DataTables berbahasa Indonesia, sama seperti tabel purchasing ----
     Hanya mengisi nilai default; tabel yang sudah menulis language sendiri tetap memakai
     tulisannya. File ini dimuat sebelum script halaman, jadi berlaku untuk semua tabel. */
  if ($.fn.dataTable && $.fn.dataTable.defaults) {
    $.extend(true, $.fn.dataTable.defaults, {
      language : {
        emptyTable  : 'Tidak ada data',
        zeroRecords : 'Tidak ada data yang cocok dengan pencarian'
      }
    })
  }

  /* ---- Tinggi kotak tabel daftar ----
     Kotak scroll (.po-table-wrap) dibuat setinggi sisa ruang di #content supaya halaman
     TIDAK perlu scrollbar sendiri - yang discroll hanya isi tabel (scrollbar auto-hide).
     Sama dengan poAturTinggiTabel() di purchaseOrder. */
  function mktWrapAktif () {
    let page1 = document.getElementById('page1')
    if (!page1 || page1.offsetParent === null) { return null }
    let kandidat = page1.querySelectorAll('.po-table-wrap')
    for (let i = 0; i < kandidat.length; i++) {
      if (kandidat[i].offsetParent !== null) { return kandidat[i] }
    }
    return null
  }

  window.mktAturTinggiTabel = function () {
    let area = document.getElementById('content')
    let wrap = mktWrapAktif()
    if (!area || !wrap) { return }
    let pane = wrap.closest('.tab-pane') || document.getElementById('page1')

    let atas = wrap.scrollTop
    let kiri = wrap.scrollLeft
    wrap.style.maxHeight = 'none'

    let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
    let batasBawah = area.getBoundingClientRect().bottom - padBawah
    let kotak = wrap.getBoundingClientRect()
    let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

    let sisa = batasBawah - kotak.top - bawah - 4
    wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
    wrap.scrollTop = atas
    wrap.scrollLeft = kiri
  }

  let mktTimerTinggi = null
  function mktJadwalTinggi (jeda) {
    if (mktTimerTinggi) { clearTimeout(mktTimerTinggi) }
    mktTimerTinggi = setTimeout(window.mktAturTinggiTabel, jeda || 0)
  }

  /* ---- Pesan "Tidak ada data" ----
     Ditampilkan sebagai blok biasa di dalam .po-table-wrap (bukan <td colspan>), sama seperti
     poRapikanBarisKosong() di purchaseOrder. */
  function mktRapikanBarisKosong (api) {
    let wrap = $(api.table().container()).find('.po-table-wrap')[0]
    if (!wrap) { return }

    let pesan = wrap.querySelector('.po-empty-msg')
    if (!pesan) {
      pesan = document.createElement('div')
      pesan.className = 'po-empty-msg'
      wrap.appendChild(pesan)
    }

    let sel = api.table().body() ? api.table().body().querySelector('td.dataTables_empty') : null
    if (sel) {
      pesan.textContent = sel.textContent
      pesan.hidden = false
      sel.parentNode.style.display = 'none'
    } else {
      pesan.hidden = true
    }
  }

  /* ---- Tooltip tombol aksi tabel daftar ---- */
  function mktTooltipAksi (tabel) {
    let $btn = $(tabel).find('tbody .action-buttons-wrap .btn[title], tbody td .btn[data-toggle="tooltip"]')
    if (!$.fn.tooltip) { return }
    $btn.each(function () {
      if (!this.getAttribute('data-toggle')) { this.setAttribute('data-toggle', 'tooltip') }
    })
    $btn.tooltip('dispose')
    $btn.tooltip({ container: 'body', boundary: 'window' })
  }

  $(document).on('draw.dt', function (e, settings) {
    let tabel = settings.nTable
    if (!tabel || !$(tabel).closest('#page1').length) { return }
    let api = new $.fn.dataTable.Api(settings)
    mktRapikanBarisKosong(api)
    mktTooltipAksi(tabel)
    mktJadwalTinggi(0)
  })

  // Integrasi Bootstrap 4 DataTables memberi kelas "card" pada indikator memuat; aturan
  // `#page1 .card { display:block !important }` membuatnya tidak pernah bisa disembunyikan.
  $(document).on('preInit.dt', function (e, settings) {
    if (!settings.nTable || !$(settings.nTable).closest('#page1').length) { return }
    setTimeout(function () {
      $(settings.nTableWrapper).children('.dataTables_processing').removeClass('card')
    }, 0)
  })

  $(document).on('shown.bs.tab', function () { mktJadwalTinggi(0) })
  $(window).on('resize', function () { mktJadwalTinggi(150) })

  $(function () {
    // #page1 berganti tampil/sembunyi saat form Add/Edit/Detail dibuka-tutup - ukur ulang
    // tinggi kotak tabel begitu #page1 tampil lagi.
    let page1 = document.getElementById('page1')
    if (page1 && window.ResizeObserver) {
      new ResizeObserver(function () { mktJadwalTinggi(60) }).observe(page1)
    }
    mktJadwalTinggi(0)
  })

  /* ---- Modal pemilih (picker) ----
     Modal yang berisi baris pilihan (tr.pick-row) atau tabel daftar pemilih
     (id tabel_add_list_* / tabel_list_*) diberi gaya modal pemilih menu Kas (picker-kas),
     dan tabelnya diinisialisasi ulang lewat pickerKasInit() (dropdown "Tampilkan",
     kotak Search, pagination) - sama seperti purchaseOrder. */
  const MKT_SEL_TABEL_PICKER = 'table[id^="tabel_add_list"], table[id^="tabel_list"], table:has(tr.pick-row)'

  function mktTabelPicker (tabel) {
    return !!tabel && !!tabel.id && $(tabel).is(MKT_SEL_TABEL_PICKER) && $(tabel).closest('.modal').length > 0
  }

  function mktUpgradePicker (tabel) {
    if (!mktTabelPicker(tabel) || typeof pickerKasInit !== 'function') { return }
    let sel = '#' + tabel.id
    let cari = ''
    if ($.fn.DataTable.isDataTable(sel)) {
      let st = $(sel).DataTable().settings()[0]
      if (st.oInit && st.oInit._pk) { return }
      // Tabel server-side tidak boleh diinisialisasi ulang sebagai tabel biasa.
      if (st.oFeatures.bServerSide) { return }
      cari = $(sel).DataTable().search()
      // destroy() mengembalikan SEMUA baris ke <tbody>, jadi pickerKasInit() di bawah
      // menemukan tabel biasa dengan isi lengkap.
      $(sel).DataTable().destroy()
    } else if (!$(tabel).find('tbody tr.pick-row').length) {
      return
    }
    // data-pk-search="false": tabel punya kotak cari sendiri (cari ke server, mis. barang
    // di SO), jadi kotak Search DataTables tidak ditambahkan supaya tidak dobel.
    let pakaiSearch = $(tabel).data('pkSearch') !== false
    pickerKasInit(tabel.id, { _pk: true, searching: pakaiSearch, search: { search: pakaiSearch ? (cari || '') : '' } })
  }

  $(document).on('init.dt', function (e, settings) {
    let tabel = settings.nTable
    if (!mktTabelPicker(tabel) || (settings.oInit && settings.oInit._pk)) { return }
    setTimeout(function () { mktUpgradePicker(tabel) }, 0)
  })

  $(document).on('show.bs.modal', '.modal', function (e) {
    if (e.target !== this) { return }
    let $modal = $(this)
    if ($modal.hasClass('rt-filter')) { return }
    let $tabel = $modal.find(MKT_SEL_TABEL_PICKER)
    if (!$tabel.length) { return }

    $modal.addClass('picker-kas')
    $modal.find('.modal-footer .btn').filter(function () {
      return $.trim($(this).text()) === 'Batal'
    }).addClass('picker-kas-batal')

    $tabel.each(function () { mktUpgradePicker(this) })
  })
})(window.jQuery)
</script>
