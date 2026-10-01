@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Purchase Order Non Stock')

@endsection
{{-- Tampilan halaman ini disamakan dengan purchasing/purchaseOrder.blade.php dan
     purchasing/closingPurchaseOrder.blade.php: layout newmasterx, header tabel interaktif
     (drag kolom + roda gigi + bar kolom tersembunyi lewat report-table.js), tab pil biru,
     toolbar seragam (search + dropdown "Tampilkan" + tombol Filter), dan tombol aksi bulat
     yang baru muncul saat barisnya di-hover. --}}
  @section('css')
<style>
  /* Form Add/Edit/Detail/Otorisasi dibungkus div#formBsGrid supaya input memakai gaya
     #formBsGrid di public/css/newmaster.css (tinggi 38px, sudut 8px, border halus, abu-abu saat
     disabled). Dua pengecualian tinggi:
     - textarea tetap setinggi aslinya (atribut rows), supaya isi beberapa baris tetap terbaca;
     - input di dalam tabel item ikut gaya baru tapi tingginya kembali ke ukuran aslinya
       (termasuk form-control-sm), supaya baris tabel tidak ikut lebih tinggi. */
  #formBsGrid textarea.form-control,
  #formBsGrid table .form-control {
    height: auto;
  }

  /* Tombol browse (kaca pembesar) di dalam #formBsGrid disamakan tingginya dengan input.
     Banyak tombol masih membawa inline style="height:32px" dari tata letak lama, sedangkan
     input di sini 38px - karena itu !important. Tombol yang berada di .input-group dilepas
     tingginya supaya meregang (align-items: stretch) mengikuti input di sebelahnya: 38px di
     form, dan tetap setinggi input di dalam sel tabel. Tombol yang berdiri sendiri (bukan di
     .input-group) dipaku 38px. */
  #formBsGrid .btn:has(> .bi-search) {
    height: 38px !important;
  }

  /* Padding atas-bawah dinolkan: dengan padding 8px tombol setinggi 35,5px, lebih tinggi dari
     input (34,25px) - stretch hanya bisa memanjangkan, jadi tombol menonjol ke bawah. Tanpa
     padding vertikal tinggi tombol sepenuhnya mengikuti input di sebelahnya. */
  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
    padding-top: 0;
    padding-bottom: 0;
  }
</style>
    <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
  /* Isi tabel di semua tab dibuat sebaris (tidak turun ke bawah). Kolom yang panjang
     cukup digeser lewat scroll horizontal .po-table-wrap (overflow:auto). */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }
</style>
<style>
  /* Tombol browse (kaca pembesar) tetap menempel ke input, tapi sudutnya membulat
     (bukan kotak). !important untuk menimpa inline
     style="border-radius:0" dan aturan .input-group bawaan Bootstrap. */
  .btn-chip-biru:has(> .bi-search),
  .btn-browsing:has(> .bi-search) {
    border-radius: 6px !important;
  }
  /* Input di kiri tombol browse ikut membulat di sisi kanannya (lewati input hidden). */
  .form-control:has(+ .btn > .bi-search),
  .form-control:has(+ :is([type=hidden], [hidden]) + .btn > .bi-search),
  .form-control:has(+ .input-group-append > .btn > .bi-search) {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
  }
</style>
<style>
  /* Dropdown "Tampilkan" (jumlah data) di modal browsing - kontrol length bawaan DataTables
     (lengthChange + lengthMenu 10/25/50/100/Semua), digaya seperti .po-len-wrap di newpo.
     Letaknya kiri atas, sejajar tepi kiri tabel; kotak pencarian tetap di kanan pada baris
     yang sama. Hanya baris toolbar yang memang berisi dropdown ini yang diatur (:has), jadi
     browsing barang yang lengthChange-nya false tidak ikut berubah. !important di flex/max-width
     untuk menimpa aturan "#form .dataTables_wrapper > .row:first-child > div { flex: 0 0 100% }"
     yang memaksa tiap kolom toolbar selebar tabel. */
  .modal .dataTables_wrapper > .row:first-child:has(.dataTables_length) {
    align-items: center;
    margin-bottom: 8px;
  }

  .modal .dataTables_wrapper > .row:first-child:has(.dataTables_length) > div {
    flex: 1 1 auto !important;
    max-width: none !important;
    width: auto;
  }

  .modal .dataTables_wrapper > .row:first-child:has(.dataTables_length) .dataTables_filter {
    margin-bottom: 0 !important;
  }

  .modal .dataTables_wrapper .dataTables_length label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    background: var(--rt-card, #FFFFFF);
    border: 1.5px solid var(--rt-border, #E7E8F0);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft, #6B7180);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .modal .dataTables_wrapper .dataTables_length select {
    width: auto;
    height: auto;
    margin: 0;
    border: none !important;
    border-radius: 0;
    box-shadow: none !important;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink, #1D2130);
    text-transform: none;
    letter-spacing: normal;
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0 !important;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: transparent url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>") no-repeat right center !important;
  }
</style>
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
{{-- Modal pemilih (Perkiraan/Barang/Supplier/Customer/dst.) disamakan persis dengan modal pemilih menu Kas - lihat public/css/picker-kas.css. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
  <style>
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }
  .rodokNdukurTitik{
    margin-top:-12px;
  }

  /* Halaman ini dirancang mengisi tinggi layar (lihat ponsAturTinggiTabel()), jadi
     padding atas #content layout dikecilkan supaya tab tidak menggantung jauh dari header. */
  #content { padding-top: 12px; }

  /* layout newmasterx punya rule .card global (align-items:center) yang override ini */
  .tab-card {
    display: block !important;
    align-items: flex-start !important;
    padding: 0 !important;
    border: none !important;
    margin-bottom: 6px !important;
  }

  .tab-card .card-body {
    padding: 5px 10px !important;
  }

  /* Tab pil biru, disamakan persis dengan purchaseOrder.blade.php / closingPurchaseOrder.blade.php. */
  .custom-tabs {
    display: inline-flex;
    justify-content: flex-start;
    align-items: center;
    gap: 2px;
    background-color: #f1f3f5;
    border-radius: 20px;
    padding: 3px;
  }

  .custom-tabs .nav-link {
    display: inline-block !important;
    padding: 5px 16px !important;
    font-size: 0.75rem !important;
    border: none;
    border-radius: 17px;
    color: #495057;
    background: transparent;
    font-weight: 600;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
  }

  .custom-tabs .nav-link:hover {
    background: transparent;
    color: #007bff;
  }

  .custom-tabs .nav-link.active {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
  }

  /* Rule .card global di layout newmasterx untuk kartu menu dashboard (flex + align-items:center
     + efek melayang saat hover) merusak card berisi tabel - card-body jadi mengikuti lebar
     tabel, bukan lebar halaman. */
  #page1 .card {
    display: block !important;
    align-items: stretch !important;
    padding: 0 !important;
    text-align: left !important;
    cursor: default !important;
  }

  #page1 .card:hover {
    transform: none !important;
    box-shadow: none !important;
    border-color: var(--border) !important;
  }

  /* Tabel bisa punya banyak kolom - kolom Bootstrap adalah flex item yang min-width
     bawaannya "auto", jadi tabel lebar memaksa card dan halaman ikut melebar. min-width:0
     membuat tabel discroll di dalam card, tidak melewati batas card. */
  #page1 .tab-content .col-md-12 {
    min-width: 0;
    max-width: 100%;
  }

  /* ---------- Kolom Action tabel Purchase Order (#tabel2) - tombol bulat kecil ---------- */
  /* :not(.dataTables_empty) - DataTables menaruh baris "No data available in table" sebagai
     <td> anak pertama juga (colspan penuh). display:flex di situ membuang colspan-nya dan
     bikin teksnya menciut ke kiri, bukan rata tengah membentang selebar tabel.
     JANGAN pakai display:flex pada <td>: sel yang di-flex berhenti jadi sel tabel, tingginya
     cuma setinggi tombol dan tidak ikut vertical-align:middle baris - tombolnya jadi kelihatan
     naik dibanding teks kolom lain. Cukup text-align + vertical-align; font-size:0 membuang
     spasi antar tombol (tombolnya sendiri punya font-size 13px) supaya jaraknya pas 4px. */
  #tabel2 tbody td:first-child:not(.dataTables_empty) {
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    font-size: 0;
  }

  #tabel2 td:first-child .btn + .btn {
    margin-left: 4px;
  }

  /* #tabel (Outstanding PR) TIDAK ikut di grup ini - kolom Actions-nya dimatikan (informasi
     saja, tanpa tombol tambah), jadi sel pertamanya sekarang kolom data biasa. */
  #tabel2 td:first-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    vertical-align: middle;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    font-size: 13px;
    border: 1px solid transparent;
    box-shadow: none;
    transition: all .12s ease;
  }

  #tabel2 td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel2 td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel2 td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  /* ---------- Kolom Action tabel item (#tabel_add) - tombol bulat kecil, sama seperti
     #tabel2 di atas, disamakan dengan menu Purchase Order (stock). Di #tabel_add kolom
     Actions ada di paling kanan, bukan paling kiri. */
  #tabel_add td:last-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    font-size: 13px;
    border: 1px solid transparent;
    box-shadow: none;
    transition: all .12s ease;
  }

  #tabel_add td:last-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel_add td:last-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel_add td:last-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel_add td:last-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel_add td:last-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel_add td:last-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  /* ---------- Header & baris tabel - bersih, uppercase abu-abu ---------- */
  #tabel thead th,
  #tabel2 thead th,
  #tabel_add thead th,
  #tabel_detail thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
  }

  #tabel tbody tr:nth-of-type(odd),
  #tabel2 tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #tabel tbody tr:hover,
  #tabel2 tbody tr:hover {
    background-color: #f5f3ff;
  }

  /* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
     style pada <table>, mengalahkan `.data-table { width: 100% }`. Dipakai min-width, BUKAN
     width, di-scope lewat ID (bukan class). */
  #tabel, #tabel2 {
    min-width: 100%;
  }

  /* Tombol di kolom Action baru muncul saat barisnya di-hover. visibility (bukan display)
     supaya lebar kolomnya tetap dipesan - tabel tidak melompat saat tombol muncul/hilang. Sengaja TIDAK memakai :focus-within: klik mouse membuat tombol tetap fokus sehingga tidak ikut hilang saat kursor sudah pindah. */
  table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transition: opacity .12s ease;
  }
  table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar kedua tab. Bentuknya meniru
     .po-filter-wrap milik public/css/po-table-header.css tapi ditulis di sini supaya
     perubahan ini cukup mengunggah file blade-nya saja. */
  .po-len-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--rt-card);
    border: 1.5px solid var(--rt-border);
    border-radius: 8px;
    padding: 5px 12px;
  }

  .po-len-wrap label {
    margin: 0;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .po-len-inp {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink);
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
  }

  /* ---- Lapisan "sedang memuat" kedua tabel ----
     Elemennya adalah .dataTables_processing bawaan DataTables, tampilannya ditulis ulang
     di sini. */
  #tabel_wrapper,
  #tabel2_wrapper {
    position: relative;
  }

  #tabel_wrapper > .dataTables_processing,
  #tabel2_wrapper > .dataTables_processing {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: auto;
    margin: 0;
    padding: 0;
    border: 0;
    background: rgba(255, 255, 255, .62);
    z-index: 40;
    animation: ponsMunculLoading .34s ease-out both;
  }

  @keyframes ponsMunculLoading {
    0%, 45% { opacity: 0; }
    100% { opacity: 1; }
  }

  .po-loading-chip {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: inline-flex;
    align-items: center;
    gap: 9px;
    white-space: nowrap;
    padding: 9px 18px;
    border-radius: 999px;
    background: rgba(31, 36, 48, .92);
    color: #fff;
    font-size: 12.5px;
    font-weight: 600;
    box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
  }

  .po-loading-spin {
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255, 255, 255, .35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: ponsPutarLoading .6s linear infinite;
  }

  @keyframes ponsPutarLoading {
    to { transform: rotate(360deg); }
  }

  /* Tabel Harga Terakhir: struktur header sama persis dengan #tabel/#tabel2 di atas
     (uppercase, 12px, garis bawah tipis), hanya warnanya hijau - mengikuti tombol
     Histori Harga yang membukanya. Sama seperti purchaseOrder.blade.php. */
  #tabel_add_harga_terakhir thead th {
    background: #e7f7ed !important;
    color: #16a34a !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #cdebd7;
    border-top: none;
  }

  #tabel_add_harga_terakhir tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #tabel_add_harga_terakhir tbody tr:hover {
    background-color: #f2fbf5;
  }

  /* ---------- Chip biru: Show/Hide Header (ikon truk), + Tambah Item, Simpan Data ----------
     Satu kelas dipakai bertiga supaya warnanya tidak jalan sendiri-sendiri. Sama seperti
     purchaseOrder.blade.php. */
  .btn-chip-biru {
    background-color: #e8edff;
    border-color: #cfdcff;
    color: #2563eb;
  }

  .btn-chip-biru:hover,
  .btn-chip-biru:focus {
    background-color: #dce6ff;
    border-color: #b9c9ff;
    color: #1d4ed8;
  }

  .btn-chip-biru:active {
    background-color: #cfdcff !important;
    border-color: #a8bdff !important;
    color: #1d4ed8 !important;
  }

  /* ---------- Tombol Histori Harga / Batal ----------
     Gaya chip: latar tint muda + teks berwarna. Warna Histori Harga sengaja sama persis
     dengan header #tabel_add_harga_terakhir di atas, sehingga tombol dan tabelnya terbaca
     sebagai satu pasangan. */
  .btn-histori-harga {
    background-color: #e7f7ed;
    border-color: #cdebd7;
    color: #16a34a;
  }

  .btn-histori-harga:hover,
  .btn-histori-harga:focus {
    background-color: #d8f0e2;
    border-color: #b6e0c6;
    color: #15803d;
  }

  .btn-histori-harga:active {
    background-color: #c8e9d5 !important;
    border-color: #a5d8b8 !important;
    color: #15803d !important;
  }

  /* Batal = aksi sekunder, jadi abu-abu muda dengan teks gelap (bukan solid gelap). */
  .btn-batal-add {
    background-color: #f1f3f5;
    border-color: #dee2e6;
    color: #495057;
  }

  .btn-batal-add:hover,
  .btn-batal-add:focus {
    background-color: #e9ecef;
    border-color: #ced4da;
    color: #343a40;
  }

  .btn-batal-add:active {
    background-color: #dee2e6 !important;
    border-color: #ced4da !important;
    color: #343a40 !important;
  }
  </style>

{{-- tampilan search bar modal add pelanggan --}}
  <style>
    #tabel_add_list_pelanggan_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;

    }
    #tabel_add_list_pelanggan_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search bar modal add pelanggan --}}

{{-- tampilan search sales --}}
  <style>
    #tabel_add_list_sales_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sales_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sales --}}

{{-- tampilan search modal barang all --}}
  <style>
    #input_search_barang_all {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
    }
    .search-label {
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: 155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }
  </style>
{{-- end tampilan search modal barang all --}}

{{-- Kartu ringkasan (Jumlah Supplier / Total DPP / Outstanding PR) - gaya sama dengan
     purchaseOrder.blade.php, tapi 3 kolom (tanpa Outstanding SO, menu ini tidak
     punya SO). --}}
<style>
  .po-kpi-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 16px;
  }
  @media (max-width: 900px) {
    .po-kpi-strip { grid-template-columns: 1fr; }
  }
  .po-kpi-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    display: flex;
    align-items: flex-start;
    gap: 14px;
  }
  .po-kpi-ic {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }
  .po-kpi-label { font-size: 13px; color: #64748b; margin-bottom: 4px; }
  .po-kpi-val { font-size: 22px; font-weight: 700; color: #1e293b; }
</style>
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid">
  <div id="contentContainer" class="">
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />
    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" id="level" value="{!! $level !!}" />

    <div class="po-kpi-strip" id="poKpiStrip"></div>

    <!-- UNTUK TAB -->
    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">

          <a class="nav-item nav-link active"
            id="nav-profile-tab"
            data-toggle="tab"
            href="#profile"
            role="tab"
            aria-controls="profile"
            aria-selected="true">
              Purchase Order
          </a>

          <a class="nav-item nav-link"
            id="nav-home-tab"
            data-toggle="tab"
            href="#home"
            role="tab"
            aria-controls="home"
            aria-selected="false">
              Outstanding PR
          </a>

        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">

          {{-- Tab "Outstanding PR" - informasi saja, tanpa kolom Actions maupun tombol
               tambah item. Seluruh penambahan item PO Non Stock dilakukan dari form
               Purchase Order (lihat memory tab-outstanding-pr-hanya-informasi - kebijakan
               yang sama berlaku untuk semua tab "outstanding"). --}}
          <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <input type="search" id="ponsSearch1" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Nilai -1 = tampilkan semua data; angka itu
                         dikirim apa adanya ke ponsdataoutstandingpr, yang memperlakukannya
                         sebagai "tanpa batas" - lihat PONonStockController@dataOutstandingPR. --}}
                    <div class="po-len-wrap">
                      <label for="ponsLen1">Tampilkan</label>
                      <select id="ponsLen1" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  {{-- Bar kolom tersembunyi + tombol "Reset kolom" (diisi report-table.js).
                       Satu elemen dipakai bersama #tabel dan #tabel2, dipindah lewat JS
                       (ponsPindahBar) saat tab berganti. --}}
                  <div id="rtBar"></div>
                  <table id="tabel" class="data-table">
                    <thead id="tabel_header" class="text-center"></thead>
                    <tbody id="tabel_data" class="text-left"></tbody>
                  </table>
                  <div class="po-rt-hint">
                    <i class="bi bi-info-circle"></i>
                    Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                    untuk menyembunyikan kolom atau mengatur jumlah desimal.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" class="po-filter-inp" id="ponsTglAwal" value="{!! $ponsTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="ponsTglAkhir" value="{!! $ponsTglAkhir !!}">
                    </div>
                    <input type="search" id="ponsSearch2" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Sama seperti #ponsLen1 milik tab Outstanding
                         PR - lihat ponsIkatPanjangHalaman(). --}}
                    <div class="po-len-wrap">
                      <label for="ponsLen2">Tampilkan</label>
                      <select id="ponsLen2" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                    <button class="po-btn-filter" type="button" id="ponsBtnFilter" onclick="$('#modalFilterPONS').modal('show')">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="po-toolbar-act">
                      <button class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
                    </div>
                  </div>
                  {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat ponsPindahBar(). --}}
                  <table id="tabel2" class="data-table po-aksi-hover">
                    <thead id="tabel2_header" class="text-center"></thead>
                    <tbody id="tabel2_data" class="text-left"></tbody>
                  </table>
                  <div class="po-rt-hint">
                    <i class="bi bi-info-circle"></i>
                    Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                    untuk menyembunyikan kolom atau mengatur jumlah desimal.
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- start modal filter status Purchase Order Non Stock -->
    <div class="modal fade rt-filter" id="modalFilterPONS">
      <div class="modal-dialog modal-md">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-funnel"></i>
              Filter Purchase Order
              <span class="rt-active-badge" id="ponsFilterBadge">0 aktif</span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPONS').modal('hide')">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="rt-section">
              <div class="rt-group-label">Penyaringan Data</div>
              <div class="rt-grid-2">
                <div>
                  <label class="rt-field-label" for="ponsModalStatus">Status</label>
                  <select class="rt-native" id="ponsModalStatus">
                    <option value="SEMUA">Semua</option>
                    <option value="Sudah">Sudah</option>
                    <option value="Belum">Belum</option>
                    <option value="Sebagian">Sebagian</option>
                    <option value="Batal">Batal</option>
                  </select>
                </div>
                <div>
                  <label class="rt-field-label" for="ponsModalOtorisasi">Otorisasi</label>
                  <select class="rt-native" id="ponsModalOtorisasi">
                    <option value="SEMUA">Semua</option>
                    <option value="Sudah">Sudah</option>
                    <option value="Belum">Belum</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="rt-reset-link" onclick="ponsResetFilter()">Reset semua</button>
            <div class="rt-footer-buttons">
              <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                onclick="$('#modalFilterPONS').modal('hide')">Batal</button>
              <button type="button" class="rt-btn rt-btn-primary" onclick="ponsTerapkanFilter()">Terapkan</button>
            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- end modal filter status Purchase Order Non Stock -->
  </div>
</div>

<div id="page2" class="container-fluid" style="display: none" >
<div id="formBsGrid">
  {{-- Margin negatif -80px/-120px yang dulu di sini disesuaikan untuk layout lama
       (purchasing.newmaster) - di newmasterx (padding atas #content lebih kecil,
       lihat @section('css')) itu membuat judul & tombol naik terlalu jauh. Disamakan
       dengan #page2 milik purchaseOrder.blade.php: tanpa margin negatif. --}}
  <div class="row">
    <div class="col-6 text-left">
      <!-- <h2>Form PO (Non-Stock)</h2> -->
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div id="modalBodyAddMain" class="">
    <div class="modal-body" style="">
      <div class="row">
        <input type="hidden" class="form-control" id="input_add_nourut">
        <div class="col-md-3">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Supplier</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" placeholder="Kode Supplier" id="input_add_kodesupplier">
                <button class="btn btn-chip-biru btn-sm" id="buttonAddListPelanggan" style="height:32px; border-radius:0;" onclick="performSearchSupplier()">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

          </div>
        </div>

        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Nama Supplier" id="input_add_namasupplier"  disabled>
              </div>
            </div>
            <div class="col-md-12" style="margin-top:-10px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Supplier" class="form-control text-left align-items-left" id="input_add_alamatsupplier"  disabled></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="row">
                <div class="col-9">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <select class="form-control" id="input_add_valas" onchange="onChangeValas()"></select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_kurs"  disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6" style="margin-top:-12px;">
              <div class="row">
                <div class="col-9">
                  <div class="form-group">
                    <label>Perkiraan</label>
                  </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6" style="margin-top:-12px;">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_add_perkiraan">
                    <button onclick="buttonAddListPerkiraan()" id="buttonAddListPerkiraan" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>PPH23</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_pph23" value='0.00'>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      <div class="col-md-3">
        <div class="row">

          <div class="col-md-12">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>PPN</label>
                </div>
              </div>
              <div class="col-md-6">
                <select onchange="onChangeTipePPN()" id="input_add_tipeppn" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected>None</option>
                  <option value=1 >Exclude</option>
                  <option value=2 >Include</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 rodokNdukurTitik">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <select id="input_add_pembayaran" onchange="onChangeInputAddPembayaran()" class="form-control form-select-lg mb-3 text-left" aria-label=".form-select-lg example">
                    <option value=0 selected >Tunai</option>
                    <option value=1 >Kredit</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-12px;">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>

              <div class="col-md-6">
                <input type="number" class="form-control text-right" id="input_add_hari" onblur="onChangeHari()" value=0 min=0 >
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label>PPH21</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_pph21" value='0.00'>
                  </div>
                </div>
              </div>
            </div>

        </div>
      </div>
    </div>

          <hr/>
          <div class="row" style='margin-top:5px'>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-lg btn-show-hide-header btn-chip-biru" style="
                height: 38px;
                width: 38px;
                margin-top: -35px;
                padding: 0;
                border-radius: 8px;
                font-size: 1.15rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="buttonShowHideHeader()" title="Show/Hide Header"><i class="bi bi-truck"></i></button>
            </div>
          </div>
            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none;">
              <div class="row" style='margin-top:-30px'>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <select class="form-control" id="input_add_kodealamatkirim" onchange="onChangeKodeAlamatKirim()">
                          <option value="GMPL">GMPL</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_alamatkirim"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Ekspedisi</label>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input class="form-control" id="input_add_kodeekspedisi" value="-" readonly>
                        <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" value="-" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_ekspedisi"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Keterangan</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_keterangan" onblur="onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Tgl Kirim</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="date" class="form-control text-left" id="input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

            <div class="col-md-3" hidden>
              <div class="row">

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Back Office</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-12">
                          <div class="input-group form-group">
                            <input type="hidden" class="form-control" id="input_add_kodebackoffice" >
                            <input type="text" class="form-control" id="input_add_namabackoffice"  disabled>
                            <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {{-- budi sementara 2 --}}
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input type="hidden" class="form-control" id="input_add_kodepic"  >
                        <input type="text" class="form-control" id="input_add_namapic"  disabled>
                        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Sales</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group form-group">
                    <input type="hidden" class="form-control" id="input_add_kodesales" >
                    <input type="text" class="form-control" id="input_add_namasales"  disabled>
                    <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-6" style="margin-top:-40px">
                  <div class="form-group">
                    <label>Draft PO</label>
                  </div>
                </div>

                <div class="col-md-6" style="margin-top:-40px">
                  <select onchange="onChangeDraftPO()" id="input_add_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1 >Ya</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

        </div>
            <hr/>
      </div>

    </div>

      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto; margin-top:-35px;">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                        <i class="bi bi-info-circle-fill"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                        <i class="bi bi-key-fill"></i>
                      </button>
                      <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id='buttonTambahItem' class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              transition: background-color 0.3s, box-shadow 0.3s;"
              onclick="buttonAddAddItem()">Tambah Item</button>
          </div>
        </div>

        <!-- ADD add -->
        <div id="addAddItem" class="container-fluid showhide">
          <hr/>

            <div class="row">
              <div class="col-12">
                <h4 id="h4AddAddItem">Add Item</h4>
                <h4 id="h4AddEditItem">Edit Item</h4>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12">

              {{-- PO Non Stock tidak lagi punya pilihan Jasa/FOC di UI - sumber barang
                   cukup lewat dropdown "+ Dari" di bawah. pFOC tetap dikirim ke sp_PO
                   (submitAddAdd/submitAddEdit), jadi field-nya dipertahankan sebagai
                   hidden bernilai tetap 0. pJasa sendiri dipaksa 1 di server
                   (PONonStockController@spAdd), tidak pernah dibaca dari form. --}}
              <input type="hidden" id="input_add_add_foc" value="0">

              <div class="row">

                {{-- Kolom kiri : identitas barang --}}
                <div class="col-md-6">

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">+ Dari</label>
                    <div class="col-md-4">
                      <select id="input_add_add_outstanding" class="form-control" onchange="onChangeOutstanding()">
                        <option value="PR" selected>PR Non Stock</option>
                        <option value="NONPR">Non PR</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Kode Barang</label>
                    <div class="col-md-4">
                      <div class="input-group">
                        <input type="text" class="form-control text-left" id="input_add_add_kodebarang">
                        <button class="btn btn-chip-biru btn-sm" id="buttonBrowseBarangItem" style="height:32px; border-radius:0;" onclick="performSearch()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <input type="text" class="form-control text-left" id="input_add_add_namabarangasli" placeholder="Nama Barang" readonly>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Nama Barang</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" id="input_add_add_namabarang">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Costing</label>
                    <div class="col-md-3">
                      <div class="input-group">
                        <input type="text" class="form-control" id="input_add_add_costing">
                        <button class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick="buttonAddListCosting()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                    <label class="col-md-3 mb-0">Sub-Costing</label>
                    <div class="col-md-3">
                      <div class="input-group">
                        <input type="text" class="form-control" id="input_add_add_subcosting">
                        <button class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick="buttonAddListSubCosting()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                </div>

                {{-- Kolom kanan : qty, harga, diskon --}}
                <div class="col-md-6">

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">QTY</label>
                    <div class="col-md-3">
                      <input type="text" id="input_add_add_qty" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onblur="cekQntStock()" tabindex="5">
                    </div>
                    <label class="col-md-3 mb-0">Satuan</label>
                    <div class="col-md-3">
                      <select id="input_add_add_nosat" class="form-control">
                        <option value=0 selected>Tidak</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Harga</label>
                    <div class="col-md-9">
                      <input type="text" id="input_add_add_harga" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onchange="onChangeInputAddAddHarga()" tabindex="6">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Disc(%)</label>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value=0 onChange='calculateDiscRp()' tabindex="8">
                    </div>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen2" value=0 onChange='calculateDiscRp()' tabindex="9">
                    </div>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen3" value=0 onChange='calculateDiscRp()' tabindex="10">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Disc RP</label>
                    <div class="col-md-3">
                      <input type="text" id="input_add_add_discrp" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onchange="reverseCalculateDiscPercent()" tabindex="7">
                    </div>
                    <label class="col-md-3 mb-0">Satuan Alias</label>
                    <div class="col-md-3">
                      <input type="text" class="form-control text-right" id="input_add_add_satuanAlias" tabindex="7">
                    </div>
                  </div>

                </div>

              </div>

              <div class="row" hidden>
                <div class="col-md-1">
                  <input type="text" min="1" max="100" class="form-control" id="input_add_add_noPPL" tabindex="1">
                </div>
                <div class="col-md-1">
                  <input type="text" min="1" max="100" class="form-control" id="input_add_add_urutPPL" tabindex="1">
                </div>
              </div>

            </div>

            <div class="col-md-12">
              <div id="divhargaterakhir">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_harga_terakhir" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Supplier</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Qty</th>
                            <th style="padding: 4px 12px;" scope="col">Satuan</th>
                            <th style="padding: 4px 12px;" scope="col">Valas</th>
                            <th style="padding: 4px 12px;" scope="col">Kurs</th>
                            <th style="padding: 4px 12px;" scope="col">Harga</th>
                            <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                            <th style="padding: 4px 12px;" scope="col">Hrg. Nett</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_harga_terakhir" class="text-left" >
                          <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right">

              <button type="button" class="btn btn-lg btn-batal-add" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeShowHideAdd()">Batal</button>

              <button type="button" class="btn btn-lg btn-histori-harga" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableHargaTerakhir()">Histori Harga</button>

              <button type="button" id="submitAddAdd" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd()">Simpan</button>

              <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddEdit()" class="btn btn-secondary">Simpan</button>
            </div>

          </div>

        </div>

        <!-- END ADD ADD -->

        <!-- ADD EDIT -->

        <div  id="addEditItem" class="container-fluid showhide">
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>
            <div class="row">

              <div class="col-md-12">

              <div class="row">

            <div class="col-md-4">

            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Ref PR</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus" ></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_refpr" value=""  disabled>
              </div>
            </div>

            </div>

            </div>


            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>No Penyerahan</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_nopenyerahan"  disabled>
              </div>
            </div>

            </div>

            </div>
            </div>
            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Barang</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang"  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_add_edit_kodebarang" >
                <input type="text" class="form-control" id="input_add_edit_namabarang"  disabled>
              </div>
            </div>

            </div>

            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Nama Produk</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_namaproduk" >
              </div>
            </div>

            </div>

            </div>

<div class="col-md-12">
  <div class="row">

    <div class="col-12">
      <div class="form-group">
        <label>Harga Terakhir</label>
      </div>
    </div>

    <div class="col-md-12 mb-4">
      <div class="form-group">
        <table id="tabel_edit_harga_terakhir" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Qty</th>
              <th scope="col">Satuan</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Harga</th>
              <th scope="col">Disc Rp</th>
              <th scope="col">Total Diskon</th>
            </tr>
          </thead>
          <tbody id="tabel_data_edit_harga_terakhir" class="text-left" >
            <tr>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

            <div class="col-md-12">
              <div class="row">


              <div class="col-md-2">
                <div class="row">

              <div class="col-md-12">
                <div class="form-group">
                  <label>Qty</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_edit_qty" value ="0.00" >
              </div>
            </div>

            </div>
          </div>

            <div class="col-md-2">
              <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>Satuan</label>
              </div>
            </div>


            <div class="col-md-12">
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" tabindex='2'>
                <option value=0 selected>Pilih Satuan</option>
              </select>
            </div>

          </div>
        </div>

        <div class="col-md-2 row">
          <div class="col-12">
            <div class="form-group">
              <label>Satuan Produk</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_edit_satuanproduk" >
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
              <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_edit_harga" value ="0.00" >
              </div>
            </div>
          </div>
        </div>


    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_disc" onChange="onChangeInputAddAddDisc()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc Rp</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_discrp" onChange="onChangeInputAddAddDiscRp()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

        </div>
        </div>
        <div class="col-md-12">
        <div class="row">


        </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Tambah ke PO</label>
              </div>
            </div>
            <div class="col-md-12">
              <select onchange="" id="input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih</option>
                <option value=1 >Tidak</option>
                <option value=2 >Ya</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Booking</label>
              </div>
            </div>
            <div class="col-md-12">
            <select onchange="" id="input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Urgent</label>
              </div>
            </div>
            <div class="col-md-12">
                <select onchange="" id="input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Tidak</option>
                <option value=1 >Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>



          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>

  <div class="container-fluid" style="margin-top: -10px;">
  <div class="row">

    <!-- Total = jumlah Subtotal seluruh item -->
    <div class="col">
      <div class="form-group">
        <label>Total</label>
        <input type="text" class="form-control text-right" id="input_add_total" value="0.00" disabled>
      </div>
    </div>

    <!-- Disc % -->
    <div class="col">
      <div class="form-group">
        <label>Disc %</label>
        <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
      </div>
    </div>

    <!-- DiscRp -->
    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="text" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value="0.00">
      </div>
    </div>

    <!-- DPP -->
    <div class="col">
      <div class="form-group">
        <label>DPP</label>
        <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
      </div>
    </div>

    <!-- PPN -->
    <div class="col">
      <div class="form-group">
        <label>PPN</label>
        <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
      </div>
    </div>

    <!-- Grand Total -->
    <div class="col">
      <div class="form-group">
        <label>Grand Total</label>
        <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
      </div>
    </div>

  </div>
</div>

</div>{{-- /#formBsGrid --}}
</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
<div id="formBsGrid">
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail PO (Non-Stock)</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

<div id="" class="">
  <div class="modal-body" >
<!-- <div class="container-fluid"> -->
  <div class="row">

    <input type="hidden" class="form-control" id="input_detail_nourut" >
    <div class="col-md-3">

      <div class="row">

        <div class="col-md-4" style="margin-top:-40px;">
          <div class="form-group">
            <label>No Bukti</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="" disabled>
          </div>
        </div>

      <div class="col-md-4" style="margin-top:-12px;">
        <div class="form-group">
          <label>Tanggal</label>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:-12px;">
        <div class="form-group">
          <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
      </div>


      <div class="col-md-4" style="margin-top:-10px;">
        <div class="form-group">
          <label>Pelanggan</label>
        </div>
      </div>


    <div class="col-md-8" style="margin-top:-10px;">
      <div class="input-group form-group">
        <input type="text" class="form-control text-center" id="input_detail_kodepelanggan" disabled>
      </div>
    </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="row">
        <!-- <div class="col-md-6">
          <div class="row">


        <div class="col-9">
          <div class="form-group">
            <label>Pelanggan</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button class="btn btn-primary btn-sm text-right" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div>
    </div>
    <div class="col-md-6">
    </div> -->


      <!-- <div class="col-md-6">
        <div class="row"> -->



        <div class="col-md-12" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_namapelanggan"  disabled>
          </div>
        </div>
        <!-- </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="row"> -->


        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-center" id="input_detail_alamatpelanggan" disabled></textarea>
          </div>
        </div>
        <!-- </div>
      </div> -->

      </div>
    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-6">

        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>Valas</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>


      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-40px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-center" id="input_detail_valas"  disabled>
            <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12" style="margin-top:-20px;">
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Kurs</label>
          </div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control text-center" id="input_detail_kurs"  disabled>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top:-12px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>TOP</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
            <input type="number" class="form-control text-center" id="input_detail_hari" disabled value=0 min=0 >
          </div>
        </div>
        </div>
    </div>

      </div>

    </div>



    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Pembayaran</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
          <select  id="input_detail_pembayaran" disabled class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Tunai</option>
            <option value=1 >Kredit</option>
          </select>
        </div>
        </div>
        </div>
        </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TGL KIRIM</label>
              </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control text-left" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-6">
              <select id="input_detail_tipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                <option value=0 selected>None</option>
                <option value=1 >Exclude</option>
                <option value=2 >Include</option>
              </select>
            </div>
          </div>
        </div>


      </div>

    </div>
  </div>

  <!-- </div> -->
  <!-- <hr/> -->
  <!-- <div class="row ">
    <div class="col-md-12 text-left">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
</div>
</div> -->
<hr/>
<div class="row ">
<div class="col-md-12 mt-2 text-left">
  <button type="button" class="btn btn-primary btn-lg" style="
  height: 30px;
  margin-top: -40px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  transition: background-color 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
  onclick="buttonShowHideHeaderDetail()" class="btn btn-secondary"><b>Show Hide Header</b></button>
</div>
</div>
  <div class="mt-4" id="modalBodyDetailMainHeader">

  <div class="row">
    <div class="col-md-3">
      <div class="row">

        <div class="col-md-6" style="margin-top:-20px;">
          <div class="form-group">
            <label>Alamat Kirim</label>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-15px;">
          <div class="form-group">
            <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
            <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
          </div>
        </div>

      </div>

    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-8" style="margin-top:-20px;">
          <div class="form-group">
            <label>Ekspedisi</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <!-- <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
        </div>

      </div>

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->
      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group">
          <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  value ='-'disabled></textarea>
        </div>
      </div>

      </div>

      <!-- <div class="row">
        <div class="col-9">
          <div class="form-group">
            <label>PIC</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div> -->
      <div class="row">

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->

      </div>

    </div>

    <div class="col-md-3">


      <div class="row">

        <div class="col-md-10" style="margin-top:-20px;">
          <label>Keterangan</label>
        </div>

      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group" style="margin-top: 14px">
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>
        </div>
      </div>

      <!-- <div class="col-md-12">

      </div> -->

      </div>

      <div class="row">

      <!-- <div class="row"> -->

      </div>

      <div class="row ">

  </div>

    </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6" style="margin-top:-20px;">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>

          <div class="col-md-6" style="margin-top:-20px;">
            <div class="form-group">
              <input type="number" class="form-control text-center" id="input_detail_dp" value='0.00' disabled>
            </div>
          </div>
          </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">

            <div class="form-group">
              <label>No PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input  type="text" class="form-control text-center" id="input_detail_nopo"  disabled>
          </div>
        </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">
            <div class="form-group">
              <label>Tgl PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>
        </div>
        </div>

      </div>

      <div class="row">

      <!-- <div class="col-md-12">

      </div> -->

      </div>

    </div>
    <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
    <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
</div> -->

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-6" style="margin-top:-10px;">
        <div class="form-group">
          <label>PIC</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodepic"  >
            <input type="text" class="form-control" id="input_detail_namapic"  disabled>
            <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-10" style="margin-top:-10px;">
        <div class="form-group">
          <label>Back Office</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>

    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="row">

          <!-- <div class="col-4">

          <div class="form-group">

          </div>

          </div> -->
          <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
            <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
            <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>

          </div>
          </div>
        <!-- </div> -->
        </div>
      </div>

    </div>

    <!-- <div class="row"> -->

    <!-- </div> -->

  </div>

</div>

<div class="col-md-3">
  <div class="row">

  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-8" style="margin-top:-10px;">
            <div class="form-group">
              <label>Sales</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>

        </div>
      </div>
      <div class="col-md-6" style="margin-top:-10px;">
        <div class="input-group form-group">
          <input type="hidden" class="form-control" id="input_detail_kodesales" >
          <input type="text" class="form-control" id="input_detail_namasales"  disabled>
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

        </div>
      </div>

    </div>

  </div>
  <!-- <div class="col-md-12">
    <div class="form-group">
      <input type="hidden" class="form-control" id="input_detail_kodesales" >
      <input type="text" class="form-control" id="input_detail_namasales"  disabled>
    </div>
  </div> -->
  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6" style="margin-top:-25px;">
      <div class="form-group">
        <label>Draft PO</label>
      </div>
    </div>

  <div class="col-md-6" style="margin-top:-25px;">
    <select  id="input_detail_draftpo" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
      <option value=0 selected>Tidak</option>
      <option value=1 >Ya</option>
    </select>
  </div>
  </div>

</div>

  </div>
  <hr/>

</div>

</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Qty</th>
          <th style="padding: 4px 12px;" scope="col">Sat</th>
          <th style="padding: 4px 12px;" scope="col">Harga</th>
          <th style="padding: 4px 12px;" scope="col">Diskon</th>
          <th style="padding: 4px 12px;" scope="col">NDPP</th>
          <!-- <th scope="col">Actions</th> -->

        </tr>
      </thead>

      <tbody id="tabel_data_detail" class="text-left" >

        <tr >

          <td></td>
          <td></td>

            <td class="text-center">
              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
              <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
              <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
            </td>
      </tr>
      </tbody>

    </table>
  </div>
    <!-- <button onclick="buttonSubKategori()">tes</button> -->
</div>

<div class="row ">
<div class="col-md-12 mt-2 text-right">
<!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
</div>
</div>

<hr/>
</div>

  <div class="container-fluid" style="margin-top: -10px;">
    <div class="row" >

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-top:-50px; margin-left:60px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_disc" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DiscRp</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-25px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_discrp" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DPP</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-50px;">
          <div class="form-group">
            <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-40px;">PPN</label>
          </div>
        </div>

        <div class="col-md-9" style="margin-left:-80px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label style="margin-left:-75px;">Grand Total</label>
          </div>
        </div>

        <div class="col-md-10" style="margin-left:45px; margin-top:-50px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    </div>

  </div>
</div>
</div>{{-- /#formBsGrid --}}
</div>

<!-- page3 end input_add -->

@include('purchasing/modals/modalPONonStockAdd')

@endsection

@section('js')
{{-- window.ReportTable: header tabel interaktif (drag kolom, roda gigi, bar kolom
     tersembunyi, tombol "Reset kolom"). File-nya berupa IIFE ber-guard, aman meski
     dimuat lebih dari sekali. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih (purchasing/modals/modalPONonStockAdd, #form) disamakan persis dengan modal
     pemilih menu Kas - class picker-kas dipasang di sini, sama seperti di purchaseOrder. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

let dataTableAdd = []
let dataTableEdit = []

let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

// Kartu ringkasan (Jumlah Supplier / Total DPP / Outstanding PR) di atas tab-content.
// Jumlah Supplier & Total DPP dihitung dari dataTampil2 (baris yang sedang tampil di tab
// Purchase Order, sudah kena filter periode + status/otorisasi). Outstanding PR
// diambil dari recordsTotal endpoint ponsdataoutstandingpr - satu baris di sana =
// satu kode barang, jadi 1 nobukti dengan 3 barang terhitung 3. Tidak ada
// Outstanding SO di sini - menu ini tidak punya SO.
let ponsKpiDPP = []
let ponsKpiOut = { 1 : null }

function renderKpiPONS () {
  let totalDPP = 0
  let suppSet = new Set()
  ;(ponsKpiDPP || []).forEach((r) => {
    totalDPP += Number(r.TotDPPRp) || 0
    // 1 supplier = 1 hitungan walau punya banyak PO. KodeSupp di-trim + upper supaya
    // kode yang sama tidak terhitung dobel hanya karena beda spasi/huruf.
    let kodeSupp = String(r.KodeSupp || '').trim().toUpperCase()
    if (kodeSupp) { suppSet.add(kodeSupp) }
  })

  let cards = [
    ['Jumlah Supplier', suppSet.size, '#dc2626', '#fee2e2', 'bi bi-people', false],
    ['Total DPP', totalDPP, '#4f46e5', '#ede9fe', 'bi bi-receipt', true],
    ['Outstanding PR', ponsKpiOut[1] === null ? '-' : ponsKpiOut[1], '#0891b2', '#cffafe', 'bi bi-clipboard-data', false]
  ]

  document.getElementById('poKpiStrip').innerHTML = cards.map((c) => `
    <div class="po-kpi-card">
      <div class="po-kpi-ic" style="background:${c[3]};color:${c[2]}">
        <i class="${c[4]}"></i>
      </div>
      <div>
        <div class="po-kpi-label">${c[0]}</div>
        <div class="po-kpi-val">${c[5] ? 'Rp ' + formatAngka(c[1]) : c[1]}</div>
      </div>
    </div>
  `).join('')
}

let dataRefreshPenerimaan = []

let listAlamatKirim = []

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let tipeform = ''
let tipeformitem = ''
buttonShowHideHeader()

// Qty/Harga/Disc RP di Tambah Item pakai pemisah ribuan (autoNumeric), disamakan
// dengan purchaseOrder.blade.php.
jQuery(function($) {
  $('.input-partial-number').autoNumeric('init',
    {
      minimumValue : '0',
    }
  );
});

$(document).ready(function(){
  muatDropdownAlamatKirim()
  muatDropdownValas()

  $("#tabel_add_list_barangall").DataTable({
    "lengthChange": false,
      "paging": false ,
      "searching" : false,
  });

  pickerKasInit('tabel_add_list_pelanggan')

  $("#tabel_add_list_sales").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

   // $("#tabel3").DataTable({
   //      "lengthChange": false,
   //        "paging": false ,
   //      });

});

function buttonOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

        let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('poupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi')
      loadAll()

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi SO ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('poupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });



}

function onChangeCatatan () {

  if (tipeform == 'edit') {
    let value  = $("#input_add_keterangan").val()
    onChangeHeader('Keterangan' , value)

  }

}
function onChangeTgglKirim () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tanggalkirim").val()
    onChangeHeader('TglKirim' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDraftPO () {
  console.log('onChangeDraftPO')
  if (tipeform == 'edit') {
    let value = $("#input_add_draftpo").val()
    console.log(value)
    onChangeHeader('PPO' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeHari () {
  console.log('onChangeHari')
  if (tipeform == 'edit')
  {
    let value = $("#input_add_hari").val()
    console.log(value)
    onChangeHeader('HARI' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }
}

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = formatAngkaVal($("#input_add_discrp").val())
        console.log(dataHeaderAdd)
        let x = Number(value) / Number(dataHeaderAdd.TOtSubtotalRP) * 100
        console.log(x)
        console.log(value)
        onChangeHeader('DISC' , x)
        refreshUpdateHeader()
        let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)
      }
}

function refreshUpdateHeader ()
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('pospupdatepo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeaderSP (field, value , field1 = null , value2 = null)
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('poonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function submitAddAdd () {


  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let TglJatuhTempo = new Date($("#input_add_tanggal").val())

  let hari = $("#input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "I"
  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_kodesupplier").val()
  //handling kosong
  let KodeExp = $("#input_add_kodeekspedisi").val()
  let Keterangan = $("#input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#input_add_valas").val()
  let Kurs = $("#input_add_kurs").val()
  let PPn = $("#input_add_tipeppn").val()
  let TipeBayar = $("#input_add_pembayaran").val()
  let Hari = $("#input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //discrp
  let Urut = 0
  let KodeBrg =  $("#input_add_add_kodebarang").val()
  let Qnt =  formatAngkaVal($("#input_add_add_qty").val())
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang
  let Harga = formatAngkaVal($("#input_add_add_harga").val())
  let DiscP = $("#input_add_add_discpersen1").val()
  let DiscTot = formatAngkaVal($("#input_add_add_discrp").val())
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  // UrutPPL WAJIB diambil dari field tersembunyi yang diisi buttonAddAddPickBarangNonFOC()
  // (baris PR yang dipilih). Dulu dibaca dari variabel global urutPPLTemp yang HANYA diisi
  // buttonAddAddPickBarangJasaPPL() - fungsi itu sudah tidak dipakai lagi sejak daftar
  // "Barang dari PR" pindah ke buttonAddAddPickBarangNonFOC(), jadi nilainya selalu 0.
  // Akibatnya dbPODet.UrutPPL=0, sp_RefreshOutPPL tidak menemukan pasangan
  // (NoPPL,UrutPPL) di dbPPLDet, QntPO PR tidak pernah bertambah, dan barangnya tetap
  // muncul di tab Outstanding PR maupun di daftar browse barang dari PR.
  let UrutPPL = $("#input_add_add_urutPPL").val()
  let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = $("#input_add_add_discpersen2").val()
  let Discpdet3 = $("#input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  // No SO & No. PO Cust dibuang dari form (PO Non Stock tidak memakainya) - dikirim
  // konstanta '-' apa adanya supaya tanda tangan parameter sp_PO tidak berubah.
  let Noso = '-'
  let NOPOCUST = '-'
  //iduser = $user->name
  //pJasa = 0
  let NPPH23 = $("#input_add_pph23").val()
  let PERKIRAAN = $("#input_add_perkiraan").val()
  let SatX = $("#input_add_add_satuanAlias").val()
  let COST = $("#input_add_add_costing").val()
  let SUBCOST = $("#input_add_add_subcosting").val()
  let TglKirim = $("#input_add_tanggalkirim").val()
  let PPH21 = $("#input_add_pph21").val()
  // No. PNW PO dibuang dari form (bergantung pada No SO yang juga sudah dibuang) -
  // dikirim konstanta apa adanya supaya tanda tangan parameter sp_PO tidak berubah.
  let NOPNw = '-'
  let UrutPNW = 0

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  console.log(tempAddAdd)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0

  // Master barang jasa (DBBARANG) di sini umumnya TIDAK mengisi SAT1/SAT2/SAT3, jadi tiga
  // cabang di bawah tidak pernah kena dan item tersimpan dengan Satuan '-' dan Isi 0.
  // Barang dari PR memakai satuan & isi baris PR-nya sendiri - pola yang sama dipakai
  // purchaseOrder.blade.php (cabang poSumberBarang() === 'PR').
  let masterSat = tempSatuanBarang.length ? tempSatuanBarang[0] : null

  if (masterSat && NoSat == 1) {
    qnt1 = Qnt * masterSat.ISI1
    Satuan = masterSat.SAT1
    Isi = masterSat.ISI1
  }
  if (masterSat && NoSat == 2) {
    qnt1 = Qnt * masterSat.ISI2
    Satuan = masterSat.SAT2
    Isi = masterSat.ISI2
  }
  if (masterSat && NoSat == 3) {
    qnt1 = Qnt * masterSat.ISI3
    Satuan = masterSat.SAT3
    Isi = masterSat.ISI3
  }

  if (!Satuan && tempAddAdd) {
    Satuan = tempAddAdd.Sat || tempAddAdd.Satuan || ''
    if (!Number(Isi)) { Isi = Number(tempAddAdd.Isi) || 0 }
  }

  if (Satuan == '') {
    Satuan = '-'
  }
  // Isi 0 tidak punya arti (dipakai konversi satuan di vwOutPPL/laporan) - baris PR non
  // stock selalu berisi 1.
  if (!Number(Isi)) {
    Isi = 1
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,

    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,

    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,

    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,

    // TipeDisc,
    // Disc,
    //discrp
    Urut,
    KodeBrg,

    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,

    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,

    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,

    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,

    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,

    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    NPPH23,

    PERKIRAAN,
    SatX,
    COST,
    SUBCOST,
    TglKirim,

    PPH21,
    NOPNw,
    UrutPNW
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    url: "{!! url('ponsspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      NoBukti,
      NoUrut,
      Tanggal,
      TglJatuhTempo,
      KodeSupp,
      // Handling,
      KodeExp,
      Keterangan,
      // FakturSupp,
      KodeVls,
      Kurs,
      PPn,
      TipeBayar,
      Hari,
      // TipeDisc,
      // Disc,
      // discrp
      Urut,
      KodeBrg,
      Qnt,
      NoSat,
      Satuan,
      Isi,
      Harga,
      DiscP,
      DiscTot,
      NoPPL,
      // IsClose,
      // IsCloseD,
      // Catatan,
      // IsExp,
      // Tolerate,
      UrutPPL,
      Kodegdg,
      Discpdet2,
      Discpdet3,
      // Discpdet4,
      // Discpdet5,
      // FlagTipe,
      NamaBrg,
      // IsJasa,
      // pFirst,
      pFOC,
      Noso,
      Jmlrecord,
      NOPOCUST,
      // IdUser,
      // pJasa,
      NPPH23,
      PERKIRAAN,
      SatX,
      COST,
      SUBCOST,
      TglKirim,
      PPH21,
      NOPNw,
      UrutPNW
    },
    success: function(res) {

      if (res == 1) {

        loadAll()
        tipeform = 'edit'
        document.getElementById("buttonAddListPelanggan").disabled = true
        document.getElementById("input_add_kodesupplier").disabled = true
        $('#divhargaterakhir').hide();
        cleanFormAddAdd()

        refreshDataTableAdd(NoBukti)

        alertify.success('Berhasil menambah item')
        ponsSegarkanOutstanding()
      }
      if(res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }
      // 3 & 4 = item DITOLAK database. Dulu kegagalan ini tidak kelihatan sama sekali:
      // sp_PO sudah terlanjur menulis header (dbPO) lalu INSERT dbPODET-nya gagal diam-diam,
      // sehingga dokumen tersimpan tanpa satu pun barang - di form barangnya terbaca null
      // dan di tab Purchase Order dokumennya hilang (TotTotalRp NULL).
      if (res == 3) {
        alertify.warning('Kode barang tidak ada di master barang - pilih barang lewat tombol browse')
      }
      if (res == 4) {
        alertify.warning('Item gagal disimpan (ditolak database) - cek kode barang & satuan')
        refreshDataTableAdd(NoBukti)
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function submitAddEdit () {

  console.log('submitAddEdits')

  let checkDate = new Date($("#input_add_tanggal").val())

  // let periode_bulan = document.getElementById("periode_bulan").value
  // let periode_tahun = document.getElementById("periode_tahun").value

  // if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
  //     alertify.warning("Tanggal tidak sesuai periode");
  //     return
  // }

  let TglJatuhTempo = new Date($("#input_add_tanggal").val())

  let hari = $("#input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "U"
  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_kodesupplier").val()
  //handling kosong
  let KodeExp = $("#input_add_kodeekspedisi").val()
  let Keterangan = $("#input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#input_add_valas").val()
  let Kurs = $("#input_add_kurs").val()
  let PPn = $("#input_add_tipeppn").val()
  let TipeBayar = $("#input_add_pembayaran").val()
  let Hari = $("#input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //DiscRp
  let Urut = tempAddEdit.Urut
  let KodeBrg =  $("#input_add_add_kodebarang").val()
  let Qnt =  formatAngkaVal($("#input_add_add_qty").val())
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang
  let Harga = formatAngkaVal($("#input_add_add_harga").val())
  let DiscP = $("#input_add_add_discpersen1").val()
  let DiscTot = formatAngkaVal($("#input_add_add_discrp").val())
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  let UrutPPL = $("#input_add_add_urutPPL").val()
  let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = $("#input_add_add_discpersen2").val()
  let Discpdet3 = $("#input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  // No SO & No. PO Cust dibuang dari form (PO Non Stock tidak memakainya) - dikirim
  // konstanta '-' apa adanya supaya tanda tangan parameter sp_PO tidak berubah.
  let Noso = '-'
  let NOPOCUST = '-'
  //iduser = $user->name
  //pJasa = 0
  let NPPH23 = $("#input_add_pph23").val()
  let PERKIRAAN = $("#input_add_perkiraan").val()
  let SatX = $("#input_add_add_satuanAlias").val()
  let COST = $("#input_add_add_costing").val()
  let SUBCOST = $("#input_add_add_subcosting").val()
  let TglKirim = $("#input_add_tanggalkirim").val()
  let PPH21 = $("#input_add_pph21").val()
  // No. PNW PO dibuang dari form (bergantung pada No SO yang juga sudah dibuang) -
  // dikirim konstanta apa adanya supaya tanda tangan parameter sp_PO tidak berubah.
  let NOPNw = '-'
  let UrutPNW = 0

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  // let tipediskon = 0
  // if (disc) {
  //   tipediskon = 1
  // }
  // if (discrp) {
  //   tipediskon = 1
  // }

  console.log(tempAddEdit)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0
  if (NoSat == 1) {
    qnt1 = Qnt * tempAddEdit.Isi
    Satuan = tempAddEdit.Satuan
    Isi = tempAddEdit.Isi
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,
    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,
    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,
    // TipeDisc,
    // Disc,
    // DiscRp,
    Urut,
    KodeBrg,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,
    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,
    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,
    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,
    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,
    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    NPPH23,
    PERKIRAAN,
    SatX,
    COST,
    SUBCOST,
    TglKirim,
    PPH21,
    NOPNw,
    UrutPNW
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) <= 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }

  $.ajax({
    // Diperbaiki: dulu menembak pospadd (POController@spAdd, milik PO stock), yang
    // menulis pJasa=0 dan mengabaikan NPPH23/PERKIRAAN/SatX/COST/SUBCOST/PPH21 - setiap
    // edit item PO Non Stock diam-diam membalik dokumen jadi PO stock (hilang dari
    // halaman ini) sekaligus mengosongkan field-field itu. ponsspadd
    // (PONonStockController@spAdd) menulis pJasa=1 dan meneruskan semuanya, sama seperti
    // jalur Add (submitAddAdd) yang sudah benar.
    url: "{!! url('ponsspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Choice,
      NoBukti,
      NoUrut,
      Tanggal,
      TglJatuhTempo,
      KodeSupp,
      // Handling,
      KodeExp,
      Keterangan,
      // FakturSupp,
      KodeVls,
      Kurs,
      PPn,
      TipeBayar,
      Hari,
      // TipeDisc,
      // Disc,
      // DiscRp,
      Urut,
      KodeBrg,
      Qnt,
      NoSat,
      Satuan,
      Isi,
      Harga,
      DiscP,
      DiscTot,
      NoPPL,
      // IsClose,
      // IsCloseD,
      // Catatan,
      // IsExp,
      // Tolerate,
      UrutPPL,
      Kodegdg,
      Discpdet2,
      Discpdet3,
      // Discpdet4,
      // Discpdet5,
      // FlagTipe,
      NamaBrg,
      // IsJasa,
      // pFirst,
      pFOC,
      Noso,
      Jmlrecord,
      NOPOCUST,
      // IdUser,
      // pJasa,
      NPPH23,
      PERKIRAAN,
      SatX,
      COST,
      SUBCOST,
      TglKirim,
      PPH21,
      NOPNw,
      UrutPNW

    },
    success: function(res) {
      console.log('resspsoaddedit', res)
      if (res == 3) {
        alertify.warning('Kode barang tidak ada di master barang - pilih barang lewat tombol browse')
        return
      }
      loadAll()

      // lockFormAdd()
      $('.showhide').hide();
      refreshDataTableAdd(NoBukti)

      alertify.success('Berhasil edit item')
      ponsSegarkanOutstanding()

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function onChangeInputAddPembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (dataTableAdd.length) {

    onChangeHeader('TIPEBAYAR' , check)
  }
  let nobukti = $("#input_add_nobukti").val()
  console.log('len',dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodesupplier = $("#input_add_kodesupplier").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodesupplier
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].hari) {
          document.getElementById("input_add_hari").value = res[0].hari

          if (dataTableAdd.length) {
            console.log('masokk')
            onChangeHeader('HARI' , res[0].hari)
            refreshUpdateHeader()
            // let nobukti = $("#input_add_nobukti").val()
            refreshDataTableAdd(nobukti)

          }
        }

      }})

  } else {
    document.getElementById("input_add_hari").value = 0
    // console.log('onChangeHari')
    if (tipeform == 'edit') {
      console.log('len', dataTableAdd.length)
      console.log(value)
      // onChangeHeader('TIPEBAYAR' , check)
      if (dataTableAdd.length) {
        console.log('masokk 2')
        onChangeHeader('HARI' , 0)
        refreshUpdateHeader()
        // let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)

      }
    }
  }
}

function onChangeInputAddAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = parseFloat(discRp).toFixed(2)

}

function onChangeInputAddAddHarga () {
  // input_add_add_disc tidak pernah ada di halaman ini (hanya discpersen1/2/3 dan
  // discrp) - dulu baris ini memakai id yang salah sehingga onchange Harga selalu
  // melempar TypeError dan berhenti di tengah jalan.
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_discpersen1").value = '0.00'
  document.getElementById("input_add_add_discpersen2").value = '0.00'
  document.getElementById("input_add_add_discpersen3").value = '0.00'
}

function onChangeInputAddEditHarga () {
  document.getElementById("input_add_edit_discrp").value = '0.00'
  document.getElementById("input_add_edit_disc").value = '0.00'
}

function onChangeInputAddAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = $("#input_add_add_harga").val();

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = $("#input_add_add_discrp").val();
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  $('#divhargaterakhir').hide();

  document.getElementById("input_add_add_outstanding").value = 'PR'
  cleanFormAddAdd()
  poKunciIdentitasBarang(false)
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function showTableHargaTerakhir () {
  if (!$("#divhargaterakhir").is(':visible'))
  {
    $('#divhargaterakhir').show();
  } else
  {
    $('#divhargaterakhir').hide();
  }
}

function buttonAddEditItem (i) {


  tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.showhide').hide();
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]

  if (tempAddEdit.NoPPL != null){
    noBuktiUntukAdd = tempAddEdit.NoPPL
  }

  if (tempAddEdit.NoPPL == null){
    noBuktiUntukAdd = 0
  }

  let selectOption = ''
  if (tempAddEdit.Satuan) {
    selectOption += `<option value=1 selected>${tempAddEdit.Satuan}</option>`
  }

  // Sumber barang saat edit ikut nilai NoPPL yang tersimpan - ada isinya berarti dulu
  // diambil dari PR, kosong berarti dari master barang (Non PR). Dropdown + kode barang
  // dikunci selama edit lewat poKunciIdentitasBarang(), sumber barang tidak bisa ditukar
  // di tengah edit item yang sudah tersimpan.
  document.getElementById("input_add_add_outstanding").value = tempAddEdit.NoPPL ? 'PR' : 'NONPR'
  poKunciIdentitasBarang(true)

  document.getElementById("input_add_add_foc").value = tempAddEdit.PFOC
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_discpersen1").value = Number(tempAddEdit.DiscP1) ?  tempAddEdit.DiscP1 : '0.00'
  document.getElementById("input_add_add_discpersen2").value = Number(tempAddEdit.Discp2) ?  tempAddEdit.Discp2 : '0.00'
  document.getElementById("input_add_add_discpersen3").value = Number(tempAddEdit.Discp3) ?  tempAddEdit.Discp3 : '0.00'
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddEdit.Qnt).toFixed(2))
  document.getElementById("input_add_add_nosat").innerHTML = selectOption
  document.getElementById("input_add_add_harga").value = Number(tempAddEdit.Harga) ? formatAngka(parseFloat(tempAddEdit.Harga).toFixed(2)) : '0.00'
  document.getElementById("input_add_add_discrp").value = Number(tempAddEdit.DISCTOT) ? formatAngka(parseFloat(tempAddEdit.DISCTOT).toFixed(2)) : '0.00'

  document.getElementById("input_add_add_noPPL").value = tempAddEdit.NoPPL
  document.getElementById("input_add_add_urutPPL").value = tempAddEdit.UrutPPL

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  $('#divhargaterakhir').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function closeShowHideAdd () {
  $('.showhide').hide();

}

function setNewNoBukti (tipePpn) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'POB',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})

}


function buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.kodepic}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPIC('${item.kodepic}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPIC').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

// Sumber barang untuk "+ Tambah Item": 'PR' (PR Non Stock, default) atau 'NONPR'
// (langsung dari master barang jasa, tanpa hubungan ke PR).
function poSumberBarang () {
  let el = document.getElementById('input_add_add_outstanding')
  return el ? el.value : 'PR'
}

function onChangeOutstanding () {
  cleanFormAddAdd()
}

// Mengunci/membuka identitas barang (sumber + kode + tombol browse) - dikunci saat
// mengedit item yang sudah tersimpan (tipeformitem == 'edit'), supaya barang yang sudah
// dipilih tidak diam-diam ditukar sumbernya. Sama seperti purchaseOrder.blade.php.
function poKunciIdentitasBarang (kunci) {
  document.getElementById('input_add_add_outstanding').disabled = kunci
  document.getElementById('input_add_add_kodebarang').disabled = kunci
  document.getElementById('buttonBrowseBarangItem').disabled = kunci
  document.getElementById('input_add_add_namabarang').disabled = kunci
}

// Cari indeks baris di dataAddAddListItem yang cocok dengan keyword yang diketik user di
// kolom kode barang. Dua tahap: kode barang PERSIS dulu, baru substring kode/nama -
// supaya kode lengkap yang kebetulan jadi bagian nama barang lain tidak membatalkan
// auto-pick.
function poCariIndeksBarang (keyword, fieldKode, fieldNama) {
  let k = String(keyword).trim().toLowerCase()
  if (!k) { return [] }

  let ambil = (item, f) => String((item[f] === null || item[f] === undefined) ? '' : item[f]).toLowerCase()
  let persis = []
  let mirip = []

  dataAddAddListItem.forEach((item, i) => {
    if (ambil(item, fieldKode) === k) { persis.push(i) }
    if (ambil(item, fieldKode).includes(k) || ambil(item, fieldNama).includes(k)) { mirip.push(i) }
  })

  return persis.length ? persis : mirip
}

// Dipakai setelah auto-pick (hasil pencarian persis satu barang): form item sudah terisi
// otomatis, jadi kursor langsung dilempar ke Qty - mengikuti pola buttonAddAddInsertItem
// di pembelianpermintaanagen/nonagen. Diberi jeda karena fungsi pick diakhiri
// scrollIntoView().
function poFokusQty () {
  setTimeout(() => {
    let el = document.getElementById("input_add_add_qty")
    if (el && !el.disabled) { el.focus(); el.select() }
  }, 300)
}

// opsi.keyword  : kata kunci dari kolom kode barang (kode maupun nama barang)
// opsi.autoPick : kalau hasil pencarian PERSIS satu baris, barang itu langsung dipilih
//                 dan modal tidak usah dibuka sama sekali. Hanya diaktifkan oleh Enter,
//                 bukan oleh tombol browse.
// Default {} membuat pemanggil lama berperilaku persis seperti sebelumnya.
function buttonAddAddListBarang (opsi = {}) {
  let _token = $("#_token").val();
  let sumber = poSumberBarang()
  let keyword = opsi.keyword ? String(opsi.keyword).trim() : ''
  let autoPick = !!opsi.autoPick

  if (sumber === 'NONPR') {

    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_jasa')) { $('#tabel_add_list_barang_jasa').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('polistbarangjasa') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res

        // Hasil pencarian persis satu barang -> langsung dipilih, modal tidak dibuka.
        if (autoPick && keyword) {
          let cocok = poCariIndeksBarang(keyword, 'KodeBrg', 'NamaBrg')
          if (cocok.length === 1) {
            buttonAddAddPickBarangJasa(cocok[0])
            poFokusQty()
            return
          }
        }

        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="buttonAddAddPickBarangJasa(${i})">
            <td style="white-space:nowrap;">${item.KodeBrg || ''}</td>
            <td style="white-space:nowrap;">${item.NamaBrg || ''}</td>
            <td style="white-space:nowrap;">${item.NamaMerk || ''}</td>
            <td style="white-space:nowrap;">${item.PartNumber || ''}</td>
            <td style="white-space:nowrap;">${item.Stock || ''}</td>
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_jasa").innerHTML = rowTable

        pickerKasInit('tabel_add_list_barang_jasa')

        // Kata kunci yang diketik user ikut terbawa ke kotak cari bawaan DataTables.
        if (keyword) { $("#tabel_add_list_barang_jasa").DataTable().search(keyword).draw() }

        document.getElementById("namaHeaderTable").textContent = 'Barang (Non PR)'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangJasa').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  } else {

    // Apa pun nilai noBuktiUntukAdd, selalu tampilkan SELURUH barang PR Non Stock yang
    // masih outstanding lewat ponslistbarangjasaall (PONonStockController@listBarangJasaAll,
    // sumbernya vwOutPPL - sama seperti data tab Outstanding PR), dirender ke panel
    // #modalBodyAddAddListBarangNonFOC yang kolomnya sudah cocok (Sat/Qnt PR/Qnt PO/Sisa
    // PR/No. PR/No. SO Cust), dengan buttonAddAddPickBarangNonFOC() yang mengisi
    // NoPPL/UrutPPL dari baris yang dipilih.
    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_nonfoc')) { $('#tabel_add_list_barang_nonfoc').DataTable().destroy(); }

    $.ajax({
      url: "{!! url('ponslistbarangjasaall') !!}",
      type: "post",
      async: false,
      data: {
        _token
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res

        // Auto-pick hanya kalau hasilnya benar-benar satu baris. Kalau satu kode barang
        // punya beberapa baris PR (beda No. PR / beda satuan), modal tetap dibuka supaya
        // user memilih sendiri No. PR mana yang dipakai.
        if (autoPick && keyword) {
          let cocok = poCariIndeksBarang(keyword, 'KodeBrg', 'NamaBrg')
          if (cocok.length === 1) {
            buttonAddAddPickBarangNonFOC(cocok[0])
            poFokusQty()
            return
          }
        }

        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="buttonAddAddPickBarangNonFOC(${i})">
            <td>${item.KodeBrg || ''}</td>
            <td style="white-space:nowrap;">${item.NamaBrg || ''}</td>
            <td>${item.PartNumber || ''}</td>
            <td>${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td>${item.Sat || ''}</td>
            <td>${item.Qnt ? formatAngka(item.Qnt) : ''}</td>
            <td>${item.QntPO ? formatAngka(item.QntPO) : ''}</td>
            <td>${item.SisaPPL || ''}</td>
            <td>${item.NoBukti || ''}</td>
            <td>${item.NosoCust ? item.NosoCust : ''}</td>
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_nonfoc").innerHTML = rowTable

        pickerKasInit('tabel_add_list_barang_nonfoc')

        if (keyword) { $("#tabel_add_list_barang_nonfoc").DataTable().search(keyword).draw() }

        document.getElementById("namaHeaderTable").textContent = 'Barang dari PR'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  }
}

// Valas jadi dropdown (bukan lagi picker + tombol +), disamakan dengan purchaseOrder.blade.php.
// Dimuat sekali saat halaman siap - lihat pemanggilan di $(document).ready().
let listValas = []

function muatDropdownValas () {
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      listValas = res
      let selectEl = document.getElementById("input_add_valas")
      let kodeTerpilih = selectEl.value
      selectEl.innerHTML = ''
      listValas.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.kodevls
        opt.textContent = `${item.kodevls} - ${item.namavls}`
        selectEl.appendChild(opt)
      });
      if (listValas.some(item => item.kodevls === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar valas')
    }
  })
}

function onChangeValas () {
  let kode = document.getElementById("input_add_valas").value
  let itemX = listValas.find(item => item.kodevls === kode)
  let kurs = itemX && itemX.kurs ? parseFloat(itemX.kurs).toFixed(2) : '0.00'
  document.getElementById("input_add_kurs").value = kurs
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS', kode)
    onChangeHeader('KURS', kurs)
  }
}

// Dikirim Ke jadi dropdown (bukan lagi picker + tombol +), sumbernya tetap DBGUDANG lewat
// polistgudang - lihat PONonStockController@index catatan yang sama di purchaseOrder.blade.php.
function muatDropdownAlamatKirim () {
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      listAlamatKirim = res
      let selectEl = document.getElementById("input_add_kodealamatkirim")
      let kodeTerpilih = selectEl.value
      selectEl.innerHTML = ''
      listAlamatKirim.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.KODEGDG
        opt.textContent = `${item.KODEGDG} - ${item.NAMA}`
        selectEl.appendChild(opt)
      });
      if (listAlamatKirim.some(item => item.KODEGDG === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
      onChangeKodeAlamatKirim()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar gudang')
    }
  })
}

function onChangeKodeAlamatKirim () {
  let kode = document.getElementById("input_add_kodealamatkirim").value
  let itemX = listAlamatKirim.find(item => item.KODEGDG === kode)
  let alamat = itemX ? itemX.Alamat : ''
  document.getElementById("input_add_alamatkirim").value = alamat
  if (tipeform == 'edit') {
    onChangeHeader('NoAlamatKirim', kode)
    onChangeHeader('AlamatKirim', alamat)
  }
}

function buttonAddListGudang () {

  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_alamatkirim')) { $('#tabel_add_list_alamatkirim').DataTable().destroy(); }
  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = ``

      listAlamatKirim = res

      listAlamatKirim.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KODEGDG}</td>
        <td>${item.NAMA}</td>
        <td>${item.Alamat}</td>
        </tr>`

        // '
        // <tr>
        // <td> '+ item.nomor + '</td>
        // <td> '+ item.nama + '</td>
        // <td>+ ' + item.alamat + '</td>
        // <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickAlamatKirim( `' + item.nomor + '` , `'+ item.nama + '` , `' + item.alamat +'` )" type="button" ><i class="bi bi-plus"></i></button></td>
        //
        // </tr>'
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable
      $("#tabel_add_list_alamatkirim").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Dikirim Ke'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListAlamatKirim').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_lokasipenerima')) { $('#tabel_add_list_lokasipenerima').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `<tr class="pick-row" onclick="buttonAddPickLokasiPenerima('-' , '-' )">
        <td>-</td>
        <td>-</td>
        </tr>`

      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )">
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      pickerKasInit('tabel_add_list_lokasipenerima')

      document.getElementById("namaHeaderTable").textContent = 'Ekspedisi'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListLokasiPenerima').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListValas () {

  if ($.fn.DataTable.isDataTable('#tabel_add_list_valas')) { $('#tabel_add_list_valas').DataTable().destroy(); }
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )" type="button" ><i class="bi bi-plus"></i></button></td>
          <td>${item.kodevls}</td>
          <td>${item.namavls}</td>
          <td>${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable
      $("#tabel_add_list_valas").DataTable({
        "lengthChange": false,
        "paging": true,
      });

      document.getElementById("namaHeaderTable").textContent = 'Valas'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPelanggan ()
{
  if ($.fn.DataTable.isDataTable('#tabel_add_list_pelanggan')) { $('#tabel_add_list_pelanggan').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistpelanggan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}','${item.HARI}', '${item.PPN}')">
        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable
      pickerKasInit('tabel_add_list_pelanggan')

      document.getElementById("namaHeaderTable").textContent = 'Supplier'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPelanggan').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

// Kotak Kode Supplier sekarang bisa diketik langsung (sama seperti purchaseOrder.blade.php)
// - tombol + / Enter membuka daftar supplier lalu menyaringnya dengan kata yang sudah diketik.
function performSearchSupplier () {
  const searchValue = document.getElementById('input_add_kodesupplier').value.trim();

  buttonAddListPelanggan();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_pelanggan')) { $('#tabel_add_list_pelanggan').DataTable().search(searchValue).draw(); }
}

document.getElementById('input_add_kodesupplier').addEventListener('keypress', function (event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    performSearchSupplier();
  }
});

function buttonAddListCosting ()
{

  let _token  = $("#_token").val()
  perkiraan = document.getElementById("input_add_perkiraan").value

  console.log(perkiraan)

  if ($.fn.DataTable.isDataTable('#tabel_add_list_costing')) { $('#tabel_add_list_costing').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistcosting') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCosting('${item.KodeCost}')">
        <td>${item.KodeCost}</td>
        <td>${item.NamaCost}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_costing").innerHTML = rowTable
      pickerKasInit('tabel_add_list_costing')

      document.getElementById("namaHeaderTable").textContent = 'Costing'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListCosting').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListSubCosting ()
{

  let _token  = $("#_token").val()
  kodeCost = document.getElementById("input_add_add_costing").value

  if ($.fn.DataTable.isDataTable('#tabel_add_list_subcosting')) { $('#tabel_add_list_subcosting').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistsubcosting') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCost
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickSubCosting('${item.KodeSubCost}')">
        <td>${item.KodeSubCost}</td>
        <td>${item.NamaSubCost}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_subcosting").innerHTML = rowTable
      pickerKasInit('tabel_add_list_subcosting')

      document.getElementById("namaHeaderTable").textContent = 'Sub-Costing'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSubCosting').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListPerkiraan ()
{
  if ($.fn.DataTable.isDataTable('#tabel_add_list_perkiraan')) { $('#tabel_add_list_perkiraan').DataTable().destroy(); }

  $.ajax({
    url: "{!! url('polistperkiraan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPerkiraan('${item.Perkiraan}')">
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable
      pickerKasInit('tabel_add_list_perkiraan')

      document.getElementById("namaHeaderTable").textContent = 'Perkiraan'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPerkiraan').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.fullname}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_backoffice").innerHTML = rowTable

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBackOffice').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListSales () {
  if ($.fn.DataTable.isDataTable('#tabel_add_list_sales')) { $('#tabel_add_list_sales').DataTable().destroy(); }
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr>
        <td>${item.keynik}</td>
        <td>${item.nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      $("#tabel_add_list_sales").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeInputAddAddNosat () {
  console.log('onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.TANGGAL}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td>${item.Xharga}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });


      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable

      if (tipeformitem == 'add') {
        console.log(tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = '0.00'
          }
        }
      } else {

      }

      // if (res.length && res[0].Xharga) {
      //   document.getElementById("input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (tempAddAdd.Hrg1_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (tempAddAdd.Hrg2_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (tempAddAdd.Hrg3_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Diporting dari purchasing/purchaseOrder.blade.php - dokumentasi lengkap pola ini ada
 * di sana, komentar di sini hanya menandai bagian yang berbeda. Halaman ini punya DUA
 * tabel: urut 1 = Outstanding PR (server-side paging, informasi saja - tanpa kolom
 * Actions, lihat memory tab-outstanding-pr-hanya-informasi), urut 2 = Purchase Order
 * (ditarik sekaligus per rentang periode, difilter Otorisasi di browser - kolom Status
 * sengaja belum dikerjakan). Tidak ada urut 3 (Outstanding SO) seperti di
 * purchaseOrder.blade.php.
 */

const PONS_HREF = 'pononstock'
const PONS_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date' }

const PONS_OUT = {
  1 : {
    tabel  : 'tabel',
    thead  : 'tabel_header',
    tbody  : 'tabel_data',
    search : 'ponsSearch1',
    len    : 'ponsLen1',
    url    : "{!! url('ponsdataoutstandingpr') !!}",
    nama   : 'Outstanding PR'
  }
}

let ponsCacheOut = { 1 : null }
let ponsPakaiCacheOut = { 1 : false }

let ponsCart = { 1 : [], 2 : [] }
let ponsActiveUrut = 0
let ponsPerluGambar = { 1 : false, 2 : false }
let ponsPanjangHalaman = { 1 : 10, 2 : 10 }

function ponsUrutTabAktif () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

const PONS_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'

function ponsBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      PONS_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function ponsKolomTampil (urut) {
  return (ponsCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function ponsKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function ponsAktifkanTabel (urut) {
  ponsActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = ponsCart[urut]
}

function ponsOnChangeAktif () {
  if (ponsActiveUrut === 2) {
    renderTabelPO()
  } else {
    initTabelOutstandingPons(ponsActiveUrut, true)
  }
}

function ponsHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let ponsRtSudahInit = false

function ponsInitReportTableSekali () {
  if (ponsRtSudahInit || typeof ReportTable === 'undefined') { return }
  ponsRtSudahInit = true

  let urutAktif = ponsUrutTabAktif()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({
      table    : idTabel[u],
      onChange : ponsOnChangeAktif
    })
  });

  ReportTable.init({
    table    : PONS_SELEKTOR_TABEL_AKTIF,
    bar      : '#rtBar',
    onChange : ponsOnChangeAktif
  })

  // Guard klik roda gigi vs sort DataTables - lihat catatan lengkap di
  // purchaseOrder.blade.php (poInitReportTableSekali).
  let ponsGuardUlangKlik = false
  let idThead = ['tabel_header', 'tabel2_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (ponsGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      ponsGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      ponsGuardUlangKlik = false
    }, true)
  });
}

function ponsPindahBar (urut) {
  let bar = document.getElementById('rtBar')
  let id = urut === 2 ? 'tabel2' : PONS_OUT[urut].tabel
  let tabel = document.getElementById(id)
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#' + id)) {
    acuan = document.getElementById(id + '_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

let ponsTimerSearch = { 1 : null }
function ponsIkatSearch (urut) {
  let input = document.getElementById(urut === 2 ? 'ponsSearch2' : PONS_OUT[urut].search)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let nilai = input.value
    let idTabel = urut === 2 ? 'tabel2' : PONS_OUT[urut].tabel
    if (urut === 2) {
      // Guard isDataTable() - tanpa ini, .DataTable() pada tabel yang baru saja
      // di-destroy() (mis. saat berpindah tab persis ketika event ini menyala) diam-diam
      // membuat instance DataTable baru dengan opsi bawaan, bukan error.
      if ($.fn.DataTable.isDataTable('#' + idTabel)) {
        $('#' + idTabel).DataTable().search(nilai).draw()
      }
      return
    }
    if (ponsTimerSearch[urut]) { clearTimeout(ponsTimerSearch[urut]) }
    ponsTimerSearch[urut] = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) {
        $('#' + idTabel).DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function ponsIkatPanjangHalaman (urut) {
  let sel = document.getElementById(urut === 2 ? 'ponsLen2' : PONS_OUT[urut].len)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(ponsPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    ponsPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    let idTabel = urut === 2 ? 'tabel2' : PONS_OUT[urut].tabel
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(ponsPanjangHalaman[urut]).draw()
    }
  })
}

function ponsIkatPeriode () {
  let awal  = document.getElementById('ponsTglAwal')
  let akhir = document.getElementById('ponsTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadTabelPO(true)
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function ponsAturTinggiTabel () {
  let area = document.getElementById('content')
  let pane = document.querySelector('#myTabContent .tab-pane.active')
  if (!area || !pane) { return }
  let wrap = pane.querySelector('.po-table-wrap')
  if (!wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

window.g_href = PONS_HREF
window.g_modeReport = 1
window.gcart_header = []

function ponsUrutSah (mode) {
  let urut = Number(mode)
  return urut === 2 ? 2 : 1
}

window.doSimpanHeader = function (href, mode) {
  let urut = ponsUrutSah(mode)
  let cart = ponsCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : PONS_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  if (!reset) { return }
  let urut = ponsUrutSah(mode)

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : PONS_HREF,
      urut   : urut,
      reset  : 1
    },
    success : function (res) {
      if (urut === 2) {
        ponsCart[2] = ponsBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2)
      } else {
        ponsCart[1] = ponsBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      }
      window.gcart_header = ponsCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function ponsFormatAngkaDes (nilai, des) {
  let d = Number(des)
  if (isNaN(d) || d < 0) { d = 0 }

  let mentah = (nilai === null || nilai === undefined || nilai === '') ? 0 : nilai
  let angka = Number(String(mentah).split(',').join(''))
  if (isNaN(angka)) {
    return (nilai === null || nilai === undefined) ? '' : nilai
  }

  let teks = angka.toFixed(d)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }

  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

function ponsRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return ponsFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Menggambar tabel "Outstanding PR" (urut 1) - server-side paging, tanpa kolom Actions.
function initTabelOutstandingPons (urut, pakaiCache) {
  let cfg = PONS_OUT[urut]
  if (!cfg) { return }
  let selTabel = '#' + cfg.tabel
  ponsAktifkanTabel(urut)

  let posisi = null
  if ($.fn.DataTable.isDataTable(selTabel)) {
    let dtLama = $(selTabel).DataTable()
    posisi = {
      start  : dtLama.page.info().start,
      search : dtLama.search(),
      order  : dtLama.order()
    }
    dtLama.destroy()
  }
  document.getElementById(cfg.tbody).innerHTML = ""

  let cols = ponsKolomTampil(urut)
  let kolomRender = cols.map(ponsKolomRender)

  let thead = document.getElementById(cfg.thead)
  thead.innerHTML = ponsHeadHtml(cols)

  let columns = []
  kolomRender.forEach((c) => {
    columns.push({
      data : null,
      className : c.tipe === 1 ? 'text-right' : '',
      render : function (data, type, row) {
        return ponsRenderNilai(c, row)
      }
    })
  });

  ponsPakaiCacheOut[urut] = !!(pakaiCache && ponsCacheOut[urut])

  if (!columns.length) {
    thead.innerHTML = '<tr><th style="padding: 4px 12px;" scope="col">' + cfg.nama + '</th></tr>'
    document.getElementById(cfg.tbody).innerHTML =
      '<tr><td class="text-center" style="padding: 14px;">Belum ada data untuk ditampilkan</td></tr>'
    return
  }

  let orderAman = posisi ? posisi.order.filter((o) => o[0] < columns.length) : []

  $(selTabel).DataTable({
    "processing" : true,
    "language" : {
      "processing" : '<span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>'
    },
    "serverSide" : true,
    "lengthChange" : false,
    "pageLength" : ponsPanjangHalaman[urut],
    "searchDelay" : 400,
    "dom" : "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "order" : orderAman,
    "displayStart" : posisi ? posisi.start : 0,
    "search" : posisi ? { "search" : posisi.search } : { "search" : "" },
    "columns" : columns,
    "ajax" : function (data, callback, settings) {
      if (ponsPakaiCacheOut[urut] && ponsCacheOut[urut]) {
        ponsPakaiCacheOut[urut] = false
        callback(Object.assign({}, ponsCacheOut[urut], { draw : data.draw }))
        return
      }

      let kolom = null
      let arah = 'asc'
      if (data.order && data.order.length) {
        let c = kolomRender[data.order[0].column]
        if (c) {
          kolom = c.field
          arah = data.order[0].dir
        }
      }

      $.ajax({
        url : cfg.url,
        type : "get",
        dataType : "json",
        data : {
          draw : data.draw,
          start : data.start,
          length : data.length,
          search : data.search ? data.search.value : '',
          orderCol : kolom,
          orderDir : arah
        },
        success : function (res) {
          // Kalau sesi login habis / server balas HTML (bukan JSON), "dataType: json"
          // di atas membuat jQuery melempar parsererror - masuk ke error: di bawah,
          // bukan diam-diam menutup overlay lewat callback(string). Tanpa itu,
          // callback(res) di sini akan melempar TypeError dan overlay "Memuat data..."
          // tidak pernah ditutup (lihat juga classlist card di bawah init DataTable).
          ponsCacheOut[urut] = res
          callback(res)
        },
        error : function (err) {
          console.log(cfg.url + ' gagal:', err.status, err.responseText)
          alertify.warning('Gagal memuat data ' + cfg.nama)
          callback({ draw : data.draw, data : [], recordsTotal : 0, recordsFiltered : 0 })
        }
      })
    },
    "drawCallback" : function () {
      setTimeout(ponsAturTinggiTabel, 0)
      ponsKpiOut[urut] = this.api().page.info().recordsTotal
      renderKpiPONS()
    }
  });

  // Integrasi Bootstrap-4 DataTables memberi elemen "Memuat data..." kelas tambahan
  // "card" (lihat public/js/datatables.min.js, sProcessing). Halaman ini punya aturan
  // "#page1 .card { display:block !important }" untuk mematikan gaya kartu dashboard -
  // itu MENGALAHKAN inline "display:none" yang dipasang DataTables saat proses selesai,
  // jadi overlay-nya kelihatan menyala terus walau datanya sudah tergambar. Membuang
  // kelas "card" di sini menghilangkan pertentangan itu (pola sama dengan
  // purchaseOrder.blade.php - poInitTabelOutstanding).
  let ponsElMemuat = document.querySelector('#' + cfg.tabel + '_wrapper > .dataTables_processing')
  if (ponsElMemuat) { ponsElMemuat.classList.remove('card') }

  ponsIkatSearch(urut)
  ponsIkatPanjangHalaman(urut)
  let inputSearch = document.getElementById(cfg.search)
  if (inputSearch) { inputSearch.value = posisi ? posisi.search : '' }
  ponsAturTinggiTabel()
}

// Filter Status & Otorisasi tab "Purchase Order" - client-side, mengikuti pola purchaseOrder.blade.php.
function ponsOtorisasiPO (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

function ponsAngka (v) {
  let n = Number(v)
  return isNaN(n) ? 0 : n
}

function ponsStatusPO (item) {
  let qnt   = ponsAngka(item.qnt)
  let qbeli = ponsAngka(item.qntbeli)
  if (qnt === 0)    { return 'Batal' }
  if (qbeli === 0)  { return 'Belum' }
  if (qbeli < qnt)  { return 'Sebagian' }
  return 'Sudah'
}

const PONS_BADGE_STATUS = {
  'Sudah'    : 'is-active',
  'Belum'    : 'is-user',
  'Sebagian' : 'is-supervisor',
  'Batal'    : 'is-inactive'
}

function ponsBadgeStatus (item) {
  let status = ponsStatusPO(item)
  let kelas = PONS_BADGE_STATUS[status] || ''
  return `<span class="sp-badge ${kelas}">${status}</span>`
}

let ponsFilterStatus = 'SEMUA'
let ponsFilterOtorisasi = 'SEMUA'

function ponsUpdateFilterBadge () {
  let jml = 0
  if (ponsFilterStatus !== 'SEMUA') { jml++ }
  if (ponsFilterOtorisasi !== 'SEMUA') { jml++ }
  let badge = document.getElementById('ponsFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function ponsTerapkanFilter () {
  ponsFilterStatus = $('#ponsModalStatus').val() || 'SEMUA'
  ponsFilterOtorisasi = $('#ponsModalOtorisasi').val() || 'SEMUA'
  ponsUpdateFilterBadge()
  $('#modalFilterPONS').modal('hide')
  renderTabelPO()
}

function ponsResetFilter () {
  ponsFilterStatus = 'SEMUA'
  ponsFilterOtorisasi = 'SEMUA'
  $('#ponsModalStatus').val('SEMUA')
  $('#ponsModalOtorisasi').val('SEMUA')
  ponsUpdateFilterBadge()
  $('#modalFilterPONS').modal('hide')
  renderTabelPO()
}

let ponsTab2Sudahdimuat = false

// Data tab "Purchase Order" ditarik sekaligus per rentang periode (Periode s/d), sama
// seperti purchaseOrder.blade.php - lihat PONonStockController@loadPurchaseOrder.
function loadTabelPO (paksa) {
  if (ponsTab2Sudahdimuat && !paksa) { return }
  $.ajax({
    url: "{!! url('ponsloadpurchaseorder') !!}",
    type: "get",
    data: {
      tglawal  : $('#ponsTglAwal').val(),
      tglakhir : $('#ponsTglAkhir').val()
    },
    success: function(res) {
      dataRefreshOutstanding2 = res.tempOutstanding3
      ponsTab2Sudahdimuat = true
      renderTabelPO()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data Purchase Order')
    }
  })
}

function renderTabelPO () {
  let level = $("#level").val()

  ponsAktifkanTabel(2)

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  let cols2 = ponsKolomTampil(2)
  let kolomRender2 = cols2.map(ponsKolomRender)

  let thead2 = document.getElementById('tabel2_header')

  let headerTable2 = ''
  headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized</th>
<th style="padding: 4px 12px;" scope="col">User Oto</th>
<th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
`

  if (level > 1) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized2</th>
    <th style="padding: 4px 12px;" scope="col">User Oto2</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto2</th>
  `
  }
  if (level > 2) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized3</th>
    <th style="padding: 4px 12px;" scope="col">User Oto3</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto3</th>
  `
  }
  if (level > 3) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized4</th>
    <th style="padding: 4px 12px;" scope="col">User Oto4</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto4</th>
  `
  }
  if (level > 4) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized5</th>
    <th style="padding: 4px 12px;" scope="col">User Oto5</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto5</th>
  `
  }

  headerTable2 += `<th style="padding: 4px 12px;" scope="col">Batal</th>
<th style="padding: 4px 12px;" scope="col">User Batal</th>
<th style="padding: 4px 12px;" scope="col">Tanggal Batal</th>
<th style="padding: 4px 12px;" scope="col">Status</th>`

  thead2.innerHTML = ponsHeadHtml(cols2)
  let baris2 = thead2.querySelector('tr')
  if (baris2) {
    baris2.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris2.insertAdjacentHTML('beforeend', headerTable2)
  }

  let rowTable2 = ""

  let dataTampil2 = (dataRefreshOutstanding2 && dataRefreshOutstanding2[0]) ? dataRefreshOutstanding2[0] : []
  if (ponsFilterStatus !== 'SEMUA') {
    dataTampil2 = dataTampil2.filter(function (r) { return ponsStatusPO(r) === ponsFilterStatus })
  }
  if (ponsFilterOtorisasi !== 'SEMUA') {
    dataTampil2 = dataTampil2.filter(function (r) { return ponsOtorisasiPO(r) === ponsFilterOtorisasi })
  }

  if (dataTampil2.length > 0) {
    dataTampil2.forEach((item, i) => {
      // Warna & ikon disamakan dengan pembelianpermintaanagen.blade.php.
      let tombolAksiPO = `<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Detail" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>`
      if (Number(item.IsOtorisasi1)) {
        tombolAksiPO += `
          <button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
          <button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Print" onclick="submitPrintcopy('${item.NoBukti}')"><i class="bi bi-printer"></i></button>`
      } else {
        tombolAksiPO += `
          <button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
          <button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Edit" onclick="buttonEdit('${item.NoBukti}')"><i class="bi bi-pen"></i></button>`
      }

      rowTable2 += `
      <tr>
        <td class="text-center" style=''>
          ${tombolAksiPO}
        </td>
      `
      kolomRender2.forEach((c) => {
        if (c.tipe === 1) {
          rowTable2 += `<td style="text-align: right;">${ponsRenderNilai(c, item)}</td>`
        } else {
          rowTable2 += `<td>${ponsRenderNilai(c, item)}</td>`
        }
      });

      rowTable2 += `
      ${Number(item.IsOtorisasi1) ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
        :
        '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
      }
      <td>${item.TglOto1 || ''}</td>
      <td>${item.OtoUser1 || ''}</td>
      `

      if (level > 1) {
        rowTable2 += `
        ${Number(item.IsOtorisasi2) ?
            '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
          :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
        }
        <td>${item.OtoUser2 || ''}</td>
        <td>${item.TglOto2 || ''}</td>
        `
        if (level > 2) {
          rowTable2 += `
          ${Number(item.IsOtorisasi3) ?
              '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
            :
            '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
          }
          <td>${item.TglOto3 || ''}</td>
          <td>${item.OtoUser3 || ''}</td>
          `
          if (level > 3) {
            rowTable2 += `
            ${Number(item.IsOtorisasi4) ?
                '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
              :
              '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
            }
            <td>${item.TglOto4 || ''}</td>
            <td>${item.OtoUser4 || ''}</td>
            `
            if (level > 4) {
              rowTable2 += `
              ${Number(item.IsOtorisasi5) ?
                  '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
                :
                '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
              }
              <td>${item.TglOto5 || ''}</td>
              <td>${item.OtoUser5 || ''}</td>
              `
            }
          }
        }
      }

      let tglBatalTampil = ''
      if (item.TglBatal) {
        let dTb = new Date(item.TglBatal)
        let dd = ("0" + dTb.getDate()).slice(-2)
        let mm = ("0" + (dTb.getMonth() + 1)).slice(-2)
        tglBatalTampil = dTb.getFullYear() + '/' + mm + '/' + dd
      }

      rowTable2 += `  ${item.Isbatal == 1 ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
        }
      <td>${item.UserBatal || ''}</td>
      <td>${tglBatalTampil}</td>
      <td class="text-center">${ponsBadgeStatus(item)}</td>
      </tr>
      `
    });
  }

  document.getElementById("tabel2_data").innerHTML = rowTable2

  $("#tabel2").DataTable({
    "lengthChange": false,
    "pageLength": ponsPanjangHalaman[2],
    "order": [],
    "dom": "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
  });

  ponsIkatSearch(2)
  ponsIkatPanjangHalaman(2)
  ponsIkatPeriode()
  let inputSearch2 = document.getElementById('ponsSearch2')
  if (inputSearch2 && inputSearch2.value) {
    $('#tabel2').DataTable().search(inputSearch2.value).draw()
  }
  ponsAturTinggiTabel()
  $('#tabel2_data [data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })

  ponsKpiDPP = dataTampil2
  renderKpiPONS()
}

function loadAll () {
  console.log('loadall')

  ponsInitReportTableSekali()

  let meta = null
  $.ajax({
    url: "{!! url('ponsloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      meta = res
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat konfigurasi tabel')
    }})

  if (!meta) {
    return
  }

  ponsCart[1] = ponsBuatCart(meta.headertableheader, meta.headertablevalue, meta.isnumeric, meta.isshown, meta.desimal, meta.aliasordered)
  ponsCart[2] = ponsBuatCart(meta.headertableheader2, meta.headertablevalue2, meta.isnumeric2, meta.isshown2, meta.desimal2, meta.aliasordered2)

  dataRefreshOutstanding2 = []
  ponsTab2Sudahdimuat = false

  let urutAktif = ponsUrutTabAktif()
  ponsPindahBar(urutAktif)

  ;[1, 2].forEach((u) => {
    ponsPerluGambar[u] = (u !== urutAktif)
  });

  if (urutAktif === 2) {
    loadTabelPO()
  } else {
    initTabelOutstandingPons(urutAktif)
  }
}

// Kartu KPI Outstanding PR tampil di semua tab, tapi tabelnya sendiri baru digambar
// saat tabnya dibuka (lazy - lihat ponsPerluGambar). Supaya kartunya tidak menunggu
// itu, total barisnya diambil sendiri di sini dengan payload minimal (length=1),
// terpisah dari initTabelOutstandingPons() - pola sama dengan purchaseOrder.blade.php
// (poMuatKpiOutstanding).
function ponsMuatKpiOutstanding () {
  $.ajax({
    url : PONS_OUT[1].url,
    type : "get",
    cache : false,
    data : { draw : 1, start : 0, length : 1, search : '' },
    success : function (res) {
      ponsKpiOut[1] = res.recordsTotal
      renderKpiPONS()
    },
    error : function (err) {
      console.log(PONS_OUT[1].url + ' gagal memuat KPI:', err.status, err.responseText)
    }
  })
}
ponsMuatKpiOutstanding()

// Dipanggil tiap kali PO berhasil disimpan (tambah/edit/hapus item) supaya barang
// yang baru saja diambil langsung hilang dari kartu Outstanding PR maupun tabelnya
// tanpa perlu F5. Cache-nya dikosongkan dulu (ponsCacheOut/ponsPakaiCacheOut) supaya
// draw berikutnya benar-benar menembak server - lihat initTabelOutstandingPons().
function ponsSegarkanOutstanding () {
  ponsCacheOut[1] = null
  ponsPakaiCacheOut[1] = false
  let selTabel = '#' + PONS_OUT[1].tabel
  if ($.fn.DataTable.isDataTable(selTabel)) {
    $(selTabel).DataTable().ajax.reload(null, false)
  }
  ponsMuatKpiOutstanding()
}

$(function () {
  $('#modalFilterPONS').on('show.bs.modal', function () {
    $('#ponsModalStatus').val(ponsFilterStatus)
    $('#ponsModalOtorisasi').val(ponsFilterOtorisasi)
    ponsUpdateFilterBadge()
  })

  $('#nav-profile-tab').on('shown.bs.tab', function () {
    ponsAktifkanTabel(2)
    ponsPindahBar(2)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (!ponsTab2Sudahdimuat) {
      ponsPerluGambar[2] = false
      loadTabelPO()
    } else if (ponsPerluGambar[2]) {
      ponsPerluGambar[2] = false
      loadTabelPO(true)
    } else {
      ponsAturTinggiTabel()
    }
  })

  $('#nav-home-tab').on('shown.bs.tab', function () {
    ponsAktifkanTabel(1)
    ponsPindahBar(1)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (ponsPerluGambar[1]) {
      ponsPerluGambar[1] = false
      initTabelOutstandingPons(1)
    } else {
      ponsAturTinggiTabel()
    }
  })

  let ponsTimerResize = null
  $(window).on('resize', function () {
    if (ponsTimerResize) { clearTimeout(ponsTimerResize) }
    ponsTimerResize = setTimeout(ponsAturTinggiTabel, 150)
  })
})

function buttonAddAddPickBarangAll (kodebrg) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('sodetailbarangall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg : kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)

      if (!res.barang.length) {

        alertify.warning("Terjadi kesalahan pada server")
        return
      }

      tempAddAdd = res.barang[0]
      document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
      document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_disc").value = '0.00'
      document.getElementById("input_add_add_discrp").value = '0.00'
      let selectOption = ''
      if (tempAddAdd.Sat1) {
        selectOption += `<option value=1 selected>${tempAddAdd.Sat1}</option>`
      }
      if (tempAddAdd.Sat2) {
        selectOption += `<option value=2>${tempAddAdd.Sat2}</option>`
      }
      if (tempAddAdd.Sat3) {
        selectOption += `<option value=3>${tempAddAdd.Sat3}</option>`
      }
      document.getElementById("input_add_add_nosat").innerHTML = selectOption

      console.log(res.harga)
      let rowTable = ``
      res.harga.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
        <td>${date1}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      if(!res.harga.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.harga.length && Number(res.harga[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res.harga[0].Xharga).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = parseFloat(tempAddAdd.Hrg1_1).toFixed(2)
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refres.hargah browser')
    }

  })

}


let cekQntPR = 0
let cekQntPO = 0
let cekQntSisa = 0


function cekQntStock () {
  // Batas Qnt Sisa hanya berlaku untuk barang dari PR - barang dari master (Non PR)
  // tidak punya kuantitas outstanding untuk dibandingkan. Dulu fungsi ini selalu memakai
  // dataAddAddListItem[0] (baris PERTAMA di daftar terakhir yang dimuat), bukan baris
  // yang benar-benar dipilih (tempAddAdd) - salah kalau baris yang dipilih bukan baris
  // pertama.
  if (poSumberBarang() !== 'PR' || !tempAddAdd) { return }

  cekQntPR = tempAddAdd.Qnt
  cekQntPO = tempAddAdd.QntPO
  cekQntSisa = tempAddAdd.SisaPPL

  let currentQntPO = Number((document.getElementById("input_add_add_qty").value || '0').replace(/,/g, '')) || 0

  if (currentQntPO > cekQntSisa) {
    alertify.warning('Qty PO Tidak boleh melebihi Qty Sisa')
    document.getElementById("input_add_add_qty").value = '0.00'
  }
}

function buttonAddAddPickBarangNonFOC (index , pEdit = 0) {

  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddAdd.SisaPPL).toFixed(2))
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'
  let selectOption = ''
  let masterSatPR = tempSatuanBarang.length ? tempSatuanBarang[0] : null
  if (masterSatPR && masterSatPR.SAT1) {
    selectOption += `<option value=1>1-${masterSatPR.SAT1}</option>`
  }
  if (masterSatPR && masterSatPR.SAT2) {
    selectOption += `<option value=2>2-${masterSatPR.SAT2}</option>`
  }
  if (masterSatPR && masterSatPR.SAT3) {
    selectOption += `<option value=3>3-${masterSatPR.SAT3}</option>`
  }
  // Barang jasa hampir selalu tidak punya SAT1..SAT3 di master, sehingga dropdown-nya
  // kosong dan $(...).val() mengembalikan null - Satuan terkirim '-' dan Isi 0. Kalau
  // master tidak memberi satuan, pakai satuan baris PR-nya sendiri.
  if (!selectOption) {
    selectOption = `<option value=${Number(tempAddAdd.NoSat) || 1} selected>${tempAddAdd.Sat || '-'}</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function buttonAddAddPickBarangJasa (index , pEdit = 0) {
  console.log('BARANG JASA PICK')
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  // Barang dari master (Non PR) tidak punya kuantitas outstanding seperti barang dari
  // PR - qty dimulai kosong, diisi manual oleh user.
  document.getElementById("input_add_add_qty").value = '0.00'

  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


urutPPLTemp = 0

function buttonAddAddPickBarangJasaPPL (index , pEdit = 0) {
  console.log('BARANG JASA PICK')
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddAdd.Qnt).toFixed(2))
  urutPPLTemp = tempAddAdd.Urut

  document.getElementById("input_add_add_urutPPL").value = 0
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  selectOption = `<option value=1 selected>1</option>`

  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=9>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

let tempSatuanBarang = []

function cekSatuanBarang (KodeBrg){
  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('ceksatuanbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      KodeBrg : KodeBrg
    },
    success: function(res) {
      tempSatuanBarang = res
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonAddPickPelanggan (kode, nama , alamat, hari, ppn) {
  console.log('buttonAddPickPelanggan')

  setNewNoBukti(ppn)

  document.getElementById("input_add_kodesupplier").value = kode
  document.getElementById("input_add_namasupplier").value = nama
  document.getElementById("input_add_alamatsupplier").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = hari
  document.getElementById("input_add_kodealamatkirim").value = 'GMPL'
  let itemGudangDefault = listAlamatKirim.find(item => item.KODEGDG === 'GMPL')
  document.getElementById("input_add_alamatkirim").value = itemGudangDefault ? itemGudangDefault.Alamat : ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'

  if (hari == 0 ){
    selectTipeBayar = `<option value=0 selected>Tunai</option>
    <option value=1>Kredit</option>`
  }
  else if (hari != 0){
    selectTipeBayar = `
    <option value=0 >Tunai</option>
    <option value=1 selected>Kredit</option>`
  }

  if (ppn == 1){
    selectTipePPN = `
    <option value=1>Exclude</option>
    <option value=2>Include</option>`
  } else if (ppn == 0) {
    selectTipePPN = `
    <option value=0>None</option>`
  }

  document.getElementById("input_add_pembayaran").innerHTML = selectTipeBayar
  document.getElementById("input_add_tipeppn").innerHTML = selectTipePPN

  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCosting (kodeCost) {
  document.getElementById("input_add_add_costing").value = kodeCost

  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickSubCosting (kodeSubCost) {
  document.getElementById("input_add_add_subcosting").value = kodeSubCost

  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickPerkiraan (kodePerkiraan) {
  console.log('buttonAddPickPerkiraan')

  document.getElementById("input_add_perkiraan").value = kodePerkiraan

  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickAlamatKirim (index) {

  let itemX = listAlamatKirim[index]
  console.log(itemX)
  // console.log(kode,nama,alamat)
  if (tipeform == 'edit') {
    onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    onChangeHeader('AlamatKirim' , itemX.Alamat)
  }

  document.getElementById("input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("input_add_alamatkirim").value = itemX.Alamat
  buttonAddListBatal()

}

function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_kodeekspedisi").value = kode
  document.getElementById("input_add_ekspedisi").value = nama

  buttonAddListBatal()
  document.getElementById("input_add_kodeekspedisi").scrollIntoView();
}

function buttonAddPickPIC (kode, nama ) {
  console.log('buttonAddPickPIC')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  buttonAddListBatal()
}

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_namasales").value = nama
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickBackOffice (kode, nama ) {
  console.log('buttonAddPickBackOffice')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('Boffice' , kode)

  }
  document.getElementById("input_add_kodebackoffice").value = kode
  document.getElementById("input_add_namabackoffice").value = nama
  buttonAddListBatal()

  document.getElementById("input_add_kodebackoffice").scrollIntoView();

}

// 'hide', bukan 'toggle'. Fungsi ini selalu berniat MENUTUP modal browse, dan sekarang
// dipanggil juga dari jalur auto-pick (buttonAddAddPickBarang* dijalankan tanpa modal
// pernah dibuka) - 'toggle' pada kondisi itu justru akan membuka modalnya.
function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('hide')
}

function cleanFormAddAdd () {
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_namabarang").value = ''
  document.getElementById("input_add_add_namabarangasli").value = ''
  document.getElementById("input_add_add_qty").value = '0.00'
  document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  document.getElementById("input_add_add_harga").value = '0.00'
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_discpersen1").value = '0.00'
  document.getElementById("input_add_add_discpersen2").value = '0.00'
  document.getElementById("input_add_add_discpersen3").value = '0.00'
  document.getElementById("input_add_add_costing").value = ''
  document.getElementById("input_add_add_subcosting").value = ''
  document.getElementById("input_add_add_satuanAlias").value = ''

  // Field referensi milik item yang tadi dipilih - kosongkan supaya memulai entri item
  // baru tidak menampilkan sisa asal barang dari item sebelumnya sebelum user memilih
  // barang baru lewat modal browse.
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0

  document.getElementById("tabel_data_add_harga_terakhir").innerHTML =
    '<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>'
}

function lockFormAdd () {
  document.getElementById("input_add_kodesupplier").disabled = true
  document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_pembayaran").disabled = true
  document.getElementById("input_add_keterangan").disabled = true
  document.getElementById("input_add_kodealamatkirim").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_draftpo").disabled = true
  document.getElementById("input_add_add_discpersen1").disabled = true
  document.getElementById("input_add_add_discpersen2").disabled = true
  document.getElementById("input_add_add_discpersen3").disabled = true
  document.getElementById("input_add_add_foc").disabled = true

  // Valas & Dikirim Ke sekarang dropdown langsung berinteraksi (bukan lagi input
  // disabled + tombol picker), jadi harus ikut dikunci di sini - lihat catatan yang
  // sama di purchaseOrder.blade.php (lockFormAdd/unlockFormAdd).
  document.getElementById("input_add_valas").disabled = true

  document.getElementById("buttonAddListPelanggan").hidden = true
  document.getElementById("buttonAddListSales").hidden = true
  document.getElementById("buttonAddListPIC").hidden = true
  document.getElementById("buttonAddListLokasiPenerima").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonTambahItem").hidden = true

  document.getElementById("input_add_disc").disabled = true
  document.getElementById("input_add_discrp").disabled = true

  document.getElementById("input_add_perkiraan").disabled = true
  document.getElementById("buttonAddListPerkiraan").hidden = true
  document.getElementById("input_add_pph23").disabled = true
  document.getElementById("input_add_pph21").disabled = true
}

function buttonShowHideHeader ()
{
  var modal = document.getElementById("modalBodyAddMainHeader");
  console.log($('#modalBodyAddMainHeader').css('display'))
  if($('#modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function buttonShowHideHeaderDetail () {
  var modal = document.getElementById("modalBodyDetailMainHeader");
  console.log($('#modalBodyDetailMainHeader').css('display'))
  if($('#modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function unlockFormAdd () {
  // Supplier hanya boleh dipilih saat bikin PO baru ("add"). Begitu tipeform == 'edit'
  // (baik karena membuka PO lama lewat buttonEdit(), maupun karena item pertama sudah
  // tersimpan - lihat submitAddAdd()), supplier dikunci supaya tidak diam-diam diganti
  // di tengah dokumen yang sudah ada itemnya. Sama seperti purchaseOrder.blade.php.
  let bolehPilihSupplier = (tipeform !== 'edit')
  document.getElementById("input_add_kodesupplier").disabled = !bolehPilihSupplier
  document.getElementById("buttonAddListPelanggan").disabled = !bolehPilihSupplier

  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  document.getElementById("input_add_kodealamatkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false
  document.getElementById("input_add_add_discpersen1").disabled = false
  document.getElementById("input_add_add_discpersen2").disabled = false
  document.getElementById("input_add_add_discpersen3").disabled = false
  document.getElementById("input_add_add_foc").disabled = false

  document.getElementById("input_add_valas").disabled = false

  document.getElementById("buttonAddListPelanggan").hidden = false
  document.getElementById("buttonAddListSales").hidden = false
  document.getElementById("buttonAddListPIC").hidden = false
  document.getElementById("buttonAddListLokasiPenerima").hidden = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonTambahItem").hidden = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false

  document.getElementById("input_add_perkiraan").disabled = false
  document.getElementById("buttonAddListPerkiraan").hidden = false
  document.getElementById("input_add_pph23").disabled = false
  document.getElementById("input_add_pph21").disabled = false
}

function cleanFormAdd () {
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_kodesupplier").value = ''
  document.getElementById("input_add_namasupplier").value = ''
  document.getElementById("input_add_alamatsupplier").value = ''
  document.getElementById("input_add_kodealamatkirim").value = 'GMPL'
  document.getElementById("input_add_alamatkirim").value = 'Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel.Simpang Pasir Mangkupalas, Samarinda Seberang. '
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_valas").value = ''
  document.getElementById("input_add_kurs").value = ''
  document.getElementById("input_add_kodebackoffice").value = ''
  document.getElementById("input_add_namabackoffice").value = ''
  document.getElementById("input_add_tipeppn").value = 0
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_kodesales").value = ''
  document.getElementById("input_add_namasales").value = ''
  document.getElementById("input_add_hari").value = 0
  document.getElementById("input_add_draftpo").value = 0

  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false

  document.getElementById("buttonAddListPelanggan").disabled = false
  document.getElementById("buttonAddListSales").disabled = false
  document.getElementById("buttonAddListPIC").disabled = false
  document.getElementById("buttonAddListLokasiPenerima").disabled = false
  document.getElementById("buttonAddListBackOffice").disabled = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false

  document.getElementById("input_add_total").value = '0.00'
  document.getElementById("input_add_disc").value = '0.00'
  document.getElementById("input_add_discrp").value = '0.00'
  document.getElementById("input_add_ppn").value = '0.00'
  document.getElementById("input_add_dpp").value = '0.00'
  document.getElementById("input_add_grandtotal").value = '0.00'
}

function buttonEdit (NOBUKTI) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  tipeform = 'edit'
  console.log('buttonEdit' , NOBUKTI)

  if (NOBUKTI != null){
  noBuktiUntukAdd = NOBUKTI
  }

  if (NOBUKTI == null){
    noBuktiUntukAdd = 0
  }

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  unlockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('pocekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

 let noBuktiUntukAdd = 0

function buttonAdd (noBukti) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  tipeform = 'add'

  if (noBukti != null){
  noBuktiUntukAdd = noBukti
  }

  if ( noBukti == null){
    noBuktiUntukAdd = 0
  }

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  dataTableAdd = []
  cleanFormAdd()
  unlockFormAdd()

  refreshDataTableAdd()
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_valas").value = 'IDR'
  document.getElementById("input_add_kurs").value = '1.00'
  document.getElementById('input_add_add_noPPL').value = noBuktiUntukAdd;

  $('#page1').hide();
  $('#page2').show();

}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page1').show();

}

function submitAdd ()
{

  let alamatpelanggan = $("#input_add_alamatsupplier").val();
  console.log(alamatpelanggan)
  let catatan = $("#input_add_keterangan").val();
  console.log(catatan)

}

function buttonAddMainHeader() {
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainHeader').show();
  // $('#buttonAddListPelanggan').hide();
}

function buttonAddMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainItems').show();
}

function buttonDetailMainHeader() {
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function buttonDetailMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}

function cekPoDet () {

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('cekPoDet') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res){
      console.log(res)
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonDetail (NOBUKTI) {
  tipeform = 'detail'
  console.log('button Detail' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  lockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableAdd(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function refreshDataTableAdd (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('pogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
              <td class="text-center">
                ${tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          if (dataHeaderAdd.NoPPL != null){
            noBuktiUntukAdd = dataHeaderAdd.NoPPL
          }

          if (dataHeaderAdd.NoPPL == null){
            noBuktiUntukAdd = 0
          }

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Alamat1
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Catatan
          document.getElementById("input_add_kodealamatkirim").value = dataHeaderAdd.Kodegdg
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.ALamatGdg
          document.getElementById("input_add_kodeekspedisi").value = dataHeaderAdd.KodeExp
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.NamaExp
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tanggalkirim").value = formatDate(dataHeaderAdd.TglKirim)
          document.getElementById("input_add_perkiraan").value = dataHeaderAdd.perkiraan

          // Total = jumlah Subtotal (Total) seluruh item.
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          document.getElementById("input_add_disc").value = dataHeaderAdd.Disc ? parseFloat(dataHeaderAdd.Disc).toFixed(2) : '0.00'
          document.getElementById("input_add_discrp").value = formatAngka(dataHeaderAdd.TotDiskon ? parseFloat(dataHeaderAdd.TotDiskon).toFixed(2) : '0.00')
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))

        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })
  }
}

function buttonAddDeleteItem (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(dataTableAdd[i])
  let dataDelete = dataTableAdd[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() {

        let _token = $("#_token").val();
        let Choice = "D"

        let NoBukti = dataDelete.NoBukti
        let Urut = dataDelete.Urut

        $.ajax({
          // Diperbaiki: dulu menembak pospadd (POController@spAdd, milik PO stock), yang
          // menulis pJasa=0 - menghapus item PO Non Stock diam-diam membalik dokumen jadi
          // PO stock. ponsspadd (PONonStockController@spAdd) menulis pJasa=1, sama seperti
          // jalur Add/Edit yang sudah benar - lihat submitAddAdd()/submitAddEdit().
          url: "{!! url('ponsspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            Choice,
            NoBukti,
            NoUrut:0,
            Tanggal: '',
            TglJatuhTempo: '',
            KodeSupp: '',
            // Handling,
            KodeExp: '',
            Keterangan: '',
            // FakturSupp,
            KodeVls: '',
            Kurs: 0,
            PPn: 0,
            TipeBayar: 0,
            Hari: 0,
            // TipeDisc,
            // Disc,
            DiscRp: 0,
            Urut,
            KodeBrg: 0,
            Qnt: 0,
            NoSat: 0,
            Satuan: '',
            Isi: 0,
            Harga: 0,
            DiscP: 0,
            // DiscTot,
            NoPPL: '',
            // IsClose,
            // IsCloseD,
            // Catatan,
            // IsExp,
            // Tolerate,
            UrutPPL: 0,
            Kodegdg: '',
            Discpdet2: 0,
            Discpdet3: 0,
            // Discpdet4,
            // Discpdet5,
            // FlagTipe,
            NamaBrg: '',
            // IsJasa,
            // pFirst,
            pFOC: 0,
            Noso: '',
            Jmlrecord: 0,
            NOPOCUST: '',
            // IdUser,
            // pJasa,
            NPPH23: 0,
            PERKIRAAN: '',
            SatX: '',
            COST: '',
            SUBCOST: '',
            TglKirim: '',
            PPH21: 0,
            NOPNw: '',
            UrutPNW: 0

          },
          success: function(res) {
            console.log('resspsoadd', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();

            refreshDataTableAdd(NoBukti)

            alertify.success('Berhasil menghapus item')
            ponsSegarkanOutstanding()

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });

}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}

function searchBarangAll (e) {
  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    if ($.fn.DataTable.isDataTable('#tabel_add_list_barangall')) { $('#tabel_add_list_barangall').DataTable().destroy(); }

  }

}

// Membuang pemisah ribuan dari nilai input-partial-number (autoNumeric) sebelum dipakai
// sebagai angka - tanpa ini parseFloat("1,234") berhenti di koma dan menghasilkan 1.
function formatAngkaVal (angka) {
  return Number(String(angka == null ? '' : angka).split(',').join('')) || 0
}

function formatAngka (angkaString) {
  if (angkaString === null || angkaString === undefined || angkaString === '') {
    return '0.00'
  }
  if (isNaN(Number(angkaString))) {
    return '0.00'
  }
  angkaString = parseFloat(angkaString).toFixed(2).toString()
  let tempAngka = angkaString.split('.')

  if (tempAngka[0][0] == '-') {
    let temp2 = ''
    let tempAngka1 = tempAngka[0].split('-')
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2
    }
    temp2 += '.' + tempAngka[1]
    temp2 = '-' + temp2
    return temp2
  }
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

function formatAngkaRupiah(angka) {
  if (angka == null || angka == '') return '0.00';

  return formatAngka(parseFloat(angka).toFixed(2));
}

function reverseCalculateDiscPercent() {
  let harga = formatAngkaVal($('#input_add_add_harga').val()) || 0;
  let discRp = formatAngkaVal(document.getElementById('input_add_add_discrp').value) || 0;

  // Clear all discount percentage fields first
  document.getElementById('input_add_add_discpersen1').value = 0;
  document.getElementById('input_add_add_discpersen2').value = 0;
  document.getElementById('input_add_add_discpersen3').value = 0;

  // If harga is 0, we can't calculate percentage
  if (harga === 0) {
    return;
  }

  // Calculate the discount percentage
  let discPercent = (discRp / harga) * 100;

  // Validate that discount doesn't exceed 100%
  if (discPercent > 100) {
    alert("Diskon tidak boleh melebihi harga");
    document.getElementById('input_add_add_discrp').value = '0.00';
    return;
  }

  // Set the first discount percentage field
  document.getElementById('input_add_add_discpersen1').value = discPercent.toFixed(2);
}

function calculateDiscRp() {
  let disc1 = document.getElementById('input_add_add_discpersen1').value
  let disc2 = document.getElementById('input_add_add_discpersen2').value
  let disc3 = document.getElementById('input_add_add_discpersen3').value

  let discRp = formatAngkaVal($('#input_add_add_harga').val())

  disc1 = parseFloat(disc1) || 0
  disc2 = parseFloat(disc2) || 0
  disc3 = parseFloat(disc3) || 0
  discRp = parseFloat(discRp) || 0

  if (disc1 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen1').value = ""
    return
  }
  if (disc2 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen2').value = ""
    return
  }
  if (disc3 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen3').value = ""
    return
  }

  let currentAmount = discRp
  let totalDiscount = 0

  if (disc1 > 0) {
    let afterDiskon1 = currentAmount * (disc1/100)
    currentAmount = currentAmount - afterDiskon1
    totalDiscount += afterDiskon1
  }

  if (disc2 > 0) {
    let afterDiskon2 = currentAmount * (disc2/100)
    currentAmount = currentAmount - afterDiskon2
    totalDiscount += afterDiskon2
  }

  if (disc3 > 0) {
    let afterDiskon3 = currentAmount * (disc3/100)
    currentAmount = currentAmount - afterDiskon3
    totalDiscount += afterDiskon3
  }

  document.getElementById('input_add_add_discrp').value = formatAngka(parseFloat(totalDiscount).toFixed(2))
}












function submitPrintcopy (nobukti) {

    let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('purchaseorderprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7)
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];
    let tanggalKirimOnly = dataPrint[0].tglkirim.split(' ')[0];


    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 18px;
        padding: 0px 4px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .border-bottom {
        border: bottom;
      }
      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 14cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%; font-size: 11px;">Jl. Ampera Pergudangan Mangkupalas Bisnis Centre Blok D No.18 RT.022, Simpang Pasir Palaran, Samarinda - Kalimantan Timur</div>
                        <div class="pb-1" style="width: 100%; font-size: 10px;">TELP : 0541 - 4104142, | FAX : 0541 - 4104195</div>
                        <div class="pb-1" style="width: 100%; font-size: 11px;">E-Mail : sml@indo.net.id</div>
                      </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">`+dataPrint[0].NAMA+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">`+dataPrint[0].ALAMAT1+`</div>
                    </div>
                  </div>

                  <div style="width: 40%; margin-left: 30px; margin-top: 15px;">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">PURCHASE ORDER</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">No</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].nobukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                    </div>
              <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Batas Tgl Kirim</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalKirimOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Pembayaran</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].HARI+` Hari</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Mata Uang</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].KODEVLS+`</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 95%; height: 100px; max-height: 100px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 35%">NAMA BARANG</td>
                    <td class="text-center" style="width: 15%">KODE BRG</td>
                    <td class="text-center" style="width: 10%">MERK</td>
                    <td class="text-center" style="width: 8%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
                    <td class="text-center" style="width: 15%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 2%; text-align: center;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NAMABRG}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.PartNumber}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 10%;">${itemSub.NAMAMERK ?? ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.QNT ? formatAngka(itemSub.QNT) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATX}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${formatAngka(parseFloat(itemSub.SUBTOTALRp).toFixed(2))}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows   table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 18px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 13px;">

  <div style="width: 38%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Ke:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">CV. SINAR MAHAKAM LESTARI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+dataPrint[0].AlamatGudang+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Via:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${dataPrint[0].Expedisi ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${dataPrint[0].almkirim ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Semua Dokumen Asli Dikirim Ke:</h3>
    </div>
    <div style="display: flex; flex-direction: column; width: 100%">
      <div class="pb-1">CV. SINAR MAHAKAM LESTARI</div>
      <div class="pb-1">Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel. Simpang Pasir Mangkupalas, Samarinda Seberang.</div>
      <div class="pb-1">Telp: +62 541-4104142 | UP. IBU ALVI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div>User :</div>
      <div>`+dataPrint[0].Iduser+`</div>
    </div>
  </div>`

  if(i == arrayDataPrint.length - 1){

    tempPrintStr += `
  <div style="width: 62%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
      <div style="width: 5%; text-align:left;"> JUMLAH </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].tsub).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 4px; position: relative;">
      <div style="width: 5%; text-align:left;"> DISKON </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Tdisc).toFixed(2))}</div>

      <div style="
      position: absolute;
      right: 0;
      bottom: 0;
      width: 35%;
      border-bottom: 1px solid #000;"></div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
      <div style="width: 5%; text-align:left;"> DPP </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Tndpprp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 6px; position: relative;">
      <div style="width: 5%; text-align:left;"> PPN </div>
      <div style="
        position: absolute;
        right: 0;
        bottom: 3px;
        width: 35%;
        border-bottom: 1px solid #000;">
      </div>

      <!-- garis bawah 2 -->
      <div style="
        position: absolute;
        right: 0;
        bottom: 0;
        width: 35%;
        border-bottom: 1px solid #000;">
      </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnppnRp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 8px; font-weight: bold;">
      <div style="width: 5%; text-align:left;"> TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnnetRp).toFixed(2))}</div>
    </div>`};

     tempPrintStr += `
      <div style="width:50%; margin-top:15px; margin-left:-25px; float:left; text-align:center; font-size:10px;">
       <div>Disetujui Oleh</div>

       <div style="height:60px;"></div>

       <div style="font-size:10px;">
        `+dataPrint[0].otouser+`
       </div>
     <div style="font-size:10px;">
        ELECTRONICALLY APPROVED
     </div>
      </div>

        <div style="width:50%; float:left; font-size:10px; margin-top:-10px; margin-left:-15px;">
        <table style="width:100%; border-collapse: collapse;">

        <!-- HEADER -->
        <tr style="height:20px;">
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Konfirmasi Supplier
          </td>
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Estimasi Tgl. Kirim
          </td>
        </tr>

        <!-- ROW NAMA -->
        <tr style="height:50px;">
          <td style="border:1px solid black; width:50%; font-size:10px; vertical-align: bottom;">
            Nama
          </td>

          <!-- KOLOM KANAN -->
          <td rowspan="2" style="border:1px solid black; height:60px; position:relative;">

            <div style="position:absolute; bottom:5px; left:6px; font-style:italic; font-size:10px; vertical-align: bottom;">
              *wajib isi
            </div>

          </td>
        </tr>

        <!-- ROW TANGGAL -->
        <tr style="height:20px;">
          <td style="border:1px solid black; font-size:10px;">
            Tanggal
          </td>
        </tr>

      </table>
    </div>

    <div style="clear: both;"></div>
  </div>

</div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`

    let w = window.open(' ');

    w.document.open();
    w.document.write(tempPrintStr);
    w.document.close();

    setTimeout(() => {
      w.focus();
      w.print();
      }, 300);
    }






function submitPrint (nobukti) {

    let _token = $('#_token').val()

  let namaTtdCetak = ''

  // const options = ['EVY YUSIA', 'JULIA', 'DESTI']

  // const overlay = document.createElement('div')
  // overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;'

  // overlay.innerHTML = `
  //   <div style="background:#fff;padding:24px;border-radius:8px;min-width:320px;font-family:sans-serif;font-size:14px;">
  //     <h3 style="margin:0 0 16px;">Cetak Purchase Order</h3>
  //     <label style="display:block;margin-bottom:6px;">Ditandatangani oleh :</label>
  //     <select id="selectNamaTtd" style="width:100%;padding:6px;font-size:14px;border:1px solid #ccc;border-radius:4px;">
  //       <option value="">-- Pilih --</option>
  //       ${options.map(n => `<option value="${n}">${n}</option>`).join('')}
  //     </select>
  //     <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;">
  //       <button id="btnBatalTtd" style="padding:6px 16px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;">Batal</button>
  //       <button id="btnLanjutTtd" style="padding:6px 16px;background:#333;color:#fff;border:none;border-radius:4px;cursor:pointer;">Cetak</button>
  //     </div>
  //   </div>
  // `

  // document.body.appendChild(overlay)

  // document.getElementById('btnBatalTtd').onclick = () => document.body.removeChild(overlay)

  // document.getElementById('btnLanjutTtd').onclick = () => {
  //   namaTtdCetak = document.getElementById('selectNamaTtd').value
  //   document.body.removeChild(overlay)


    $.ajax({
      url: "{!! url('purchaseorderprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)

      }
    })

    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7)
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];

    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 24px;
        padding: 1px 5px 0px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .border-bottom {
        border: bottom;
      }
      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 13.5cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%">JL. PRAMUKA NO. 63 RT. 11 BANJARMASIN 70249</div>
                        <div class="pb-1" style="width: 100%">TELP : 0511 - 3269593 | FAX : 0511 - 3272142</div>
                        <div class="pb-1" style="width: 100%">E-Mail : spl@indo.net</div>
                      </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">`+dataPrint[0].NAMA+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">`+dataPrint[0].ALAMAT1+`</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">PURCHASE ORDER</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+dataPrint[0].nobukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+tanggalOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Pembayaran</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+dataPrint[0].HARI+` Hari</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Mata Uang</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+dataPrint[0].KODEVLS+`</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td class="text-center" style="width: 5%">KODE BRG</td>
                    <td class="text-center" style="width: 5%">MERK</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 5%">HARGA</td>
                    <td class="text-center" style="width: 5%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NAMABRG}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.PartNumber}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.NAMAMERK}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${itemSub.QNT ? formatAngka(itemSub.QNT) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATUAN}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${formatAngka(parseFloat(itemSub.SUBTOTALRp).toFixed(2))}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows � table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 38%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Ke:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">CV. SARANA PRIMA LESTARI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+dataPrint[0].AlamatGudang+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Via:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+dataPrint[0].Expedisi+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+dataPrint[0].almkirim+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Semua Dokumen Asli Dikirim Ke:</h3>
    </div>
    <div style="display: flex; flex-direction: column; width: 100%">
      <div class="pb-1">CV. SARANA PRIMA LESTARI</div>
      <div class="pb-1">JL. PRAMUKA NO. 63 RT. 11, BANJARMASIN - 70249</div>
      <div class="pb-1">UP. IBU FITRI</div>
      <div class="pb-1">E-Mail: finance@saranaprimalestari.com</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 20%">USER : </div>
      <div class="pb-1" style="width: 80%">`+dataPrint[0].Iduser+`</div>
    </div>
  </div>

  <div style="width: 62%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> Jumlah </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].tsub).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> DISKON </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Tdisc).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> SUBTOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].ndpprp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> PPN 10% </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnppnRp).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 8px; font-weight: bold;">
      <div style="width: 60% margin-left:auto"> TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnnetRp).toFixed(2))}</div>
    </div>

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui Oleh</td>
        <td class="no-border text-center" style="width: 33%; font-size:13px;">Konfirmasi Supplier</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2 text-center">
          <p class="m-0" style='font-size:12px;'>`+dataPrint[0].otouser+`</p>
        </td>







        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tanggal</p>
        </td>
      </tr>
    </table>
  </div>

</div>

         <div class="footer-print-date" style='margin-bottom:-100px;'>
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px ">
             <tr>
               <td class="no-border"></td>
               <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
             </tr>
           </table>
         </div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }
  // }

</script>
{{-- Dulu ada skrip manual "hover tab" (ubah inline style backgroundColor tiap tab
     diklik) untuk mewarnai 3 tab. Tabnya sekarang tinggal 2, dan warnanya sudah
     ditangani `.custom-tabs .nav-link.active` (@section('css')) lewat class yang
     dipasang otomatis oleh plugin tab Bootstrap - skrip manual itu dibuang, bukan
     cuma jadi tidak perlu tapi juga akan error (nav-profile1-tab sudah tidak ada). --}}
<script>
  // autoPick hanya true kalau dipanggil dari Enter. Menekan tombol browse berarti user
  // memang ingin melihat daftarnya, jadi modal selalu dibuka - tetap terfilter kalau
  // kolom kode barang sudah terisi.
  //
  // Penerapan filter ke DataTables sekarang dilakukan di dalam cabang masing-masing di
  // buttonAddAddListBarang(), yang tahu persis tabel mana miliknya sesuai pilihan
  // dropdown "+ Dari".
  function performSearch (autoPick = false) {
    const searchValue = document.getElementById('input_add_add_kodebarang').value.trim();

    if (autoPick && !searchValue) {
      alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.")
      return
    }

    buttonAddAddListBarang({ keyword: searchValue, autoPick: autoPick });
  }

  // Keyboard event
  document.getElementById('input_add_add_kodebarang').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        performSearch(true);
    }
  });

  window.onload = function(){
    loadAll();
  };

</script>

@endsection
