@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Perintah Retur Beli')

@endsection
{{-- tampilan search bar 1 --}}
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
  {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + modal
       filter), disamakan dengan newpo.blade.php / uangmukabeli.blade.php. --}}
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
  #content { padding-top: 12px; }

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

  /* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
     style pada <table>, yang mengalahkan `.data-table { width: 100% }`. Dipakai min-width,
     BUKAN width, dan di-scope lewat ID (bukan class) - sama seperti uangmukabeli.blade.php. */
  #tabel, #tabel2 {
    min-width: 100%;
  }

  #tabel td:first-child:not([colspan]),
  #tabel2 td:first-child:not([colspan]) {
    vertical-align: middle;
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #tabel td:first-child .btn,
  #tabel2 td:first-child .btn {
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

  #tabel td:first-child .btn:hover,
  #tabel2 td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success,  #tabel2 td:first-child .btn-success  { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel td:first-child .btn-warning,  #tabel2 td:first-child .btn-warning  { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel td:first-child .btn-primary,  #tabel2 td:first-child .btn-primary  { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel td:first-child .btn-danger,   #tabel2 td:first-child .btn-danger   { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel td:first-child .btn-info,     #tabel2 td:first-child .btn-info     { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  /* ---------- #tabel_add (tabel item di form Perintah Retur Beli, dalam #page2) - kolom
     Actions ada di kolom PALING KANAN (last-child; disembunyikan di mode detail karena
     mode itu tidak punya kolom aksi), header abu-abu uppercase + zebra + hover + tombol
     aksi pastel bulat, disamakan dengan
     resources/views/purchasing/pembelianpermintaannonagen.blade.php. ---------- */
  #tabel_add td:last-child:not([colspan]) {
    vertical-align: middle;
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

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

  #tabel_add td:last-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel_add td:last-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel_add td:last-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel_add td:last-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

  #tabel_add thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
  }

  #tabel_add tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
  #tabel_add tbody tr:hover { background-color: #f5f3ff; }

  /* Qty & Sat rata tengah, header dan isi, supaya lurus segaris. */
  #tabel_add thead th:nth-child(3),
  #tabel_add thead th:nth-child(4),
  #tabel_add tbody td:nth-child(3):not([colspan]),
  #tabel_add tbody td:nth-child(4):not([colspan]) {
    text-align: center;
  }

  table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transition: opacity .12s ease;
  }
  table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar - tidak ada di
     po-table-header.css, ditulis di sini supaya perubahan cukup mengunggah file blade-nya saja. */
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

  /* ---------- Tombol chip (latar tint muda + teks berwarna) untuk tombol Tambah Item,
     Submit Add/Edit di form #page2 - disalin dari pembelianpermintaannonagen.blade.php. ---------- */
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

  /* Batal = aksi sekunder, jadi abu-abu muda dengan teks gelap (bukan solid merah). */
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

  <style>
  .rodokNdukurTitik{
    margin-top:-12px;
  }
  </style>

  <style>
    .dataTables_wrapper {
    overflow-x: auto;
  }

  .dataTables_scrollBody {
      border: none !important;
  }

  /* Remove conflicting borders */
  .table-responsive {
      border: none !important;
  }

    </style>

  <style>
  #tabel_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;  
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }
  </style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
  <style>
  #tabel2_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel2_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel2_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabel2_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar 3 --}}
  <style>
  #tabel3_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel3_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel3_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabel3_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 3 --}}

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
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid">
    <!-- <div id="qrcode"></div> -->
    <div class="row" style="margin-bottom: 14px;">
      <div class="col-12 text-left">
        <!-- <h2>Perintah Retur Beli</h2> -->
      </div>
    </div>

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

    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
             aria-controls="nav-home" aria-selected="true">
            Perintah Retur Beli
          </a>
          <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
             aria-controls="nav-profile" aria-selected="false">
            List Retur Jual
          </a>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" class="po-filter-inp" id="prbTglAwal1" value="{!! $prbTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="prbTglAkhir1" value="{!! $prbTglAkhir !!}">
                    </div>
                    <input type="search" id="prbSearch1" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="prbLen1">Tampilkan</label>
                      <select id="prbLen1" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                    <button class="po-btn-filter" type="button" id="prbBtnFilter" onclick="$('#modalFilterPRB').modal('show')">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="po-toolbar-act">
                      <button class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
                    </div>
                  </div>

                  {{-- #rtBar diisi lewat JS oleh ReportTable.init() - satu elemen dipakai
                       bersama #tabel dan #tabel2, dipindah lewat JS (prbPindahBar) saat tab
                       berganti - lihat prbInitReportTableSekali(). --}}
                  <div id="rtBar"></div>
                  <table id="tabel" class="data-table po-aksi-hover">
                    <thead id="tabel_header" class="text-center">
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="tabel_data" class="text-left"></tbody>
                  </table>
                  <div class="po-rt-hint">
                    <i class="bi bi-info-circle"></i>
                    Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12">
                <div class="container-fluid" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" class="po-filter-inp" id="prbTglAwal2" value="{!! $prbTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="prbTglAkhir2" value="{!! $prbTglAkhir !!}">
                    </div>
                    <input type="search" id="prbSearch2" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="prbLen2">Tampilkan</label>
                      <select id="prbLen2" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>

                  {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat prbPindahBar(). --}}
                  <table id="tabel2" class="data-table po-aksi-hover">
                    <thead id="tabel2_header" class="text-center">
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Nomor Retur</th>
                      </tr>
                    </thead>
                    <tbody id="tabel2_data" class="text-left"></tbody>
                  </table>
                  <div class="po-rt-hint">
                    <i class="bi bi-info-circle"></i>
                    Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>

<div id="page2" class="container-fluid" style="display: none" >
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left">
      <h2></h2>
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
    <div class="modal-body">
      <div class="row">

            <input type="hidden" class="form-control" id="input_nourut">

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" readonly>
                    <input type="text" class="form-control text-left" id="input_add_nourut" placeholder="nourut" hidden>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" readonly>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group">
                    <label>Pembayaran</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select id="input_add_tipebayar" class="form-control form-select-lg mb-3 text-left" aria-label=".form-select-lg example">
                      <option value=0 selected >Tunai</option>
                      <option value=1>Kredit</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-1">
                  <div class="form-group">
                    <label>Keterangan</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <textarea style="width: 100%; resize: none" rows="2" placeholder="Keterangan" class="form-control text-left " id="input_add_keterangan" onblur="onChangeCatatan()"></textarea>
                  </div>
                </div>
              </div>
            </div>

          </div>

          {{-- <div class="row" style='margin-top:5px'>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-primary btn-lg" style="
                height: 30px; 
                margin-top: -35px;
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="buttonShowHideHeader()" class="btn btn-secondary"><b>Show/Hide Header</b></button>
            </div>
          </div> --}}
            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none;">
              <div class="row">

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodeAlamatKirim" readonly >
                      <button onclick="buttonAddListGudang()" id="buttonAddListGudang"  style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
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
                    <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodeEkspedisi" value ='-'readonly>
                      <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" style="height:32px;" value = '-' class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
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
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" disabled></textarea>
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
                            <label>No SO</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_noso" value='-' readonly>
                            <button onclick="buttonAddListNoSO()" id="buttonAddListNoSo" style="height:32px;" class="btn btn-primary btn-sm text-right">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>No. PO Cust</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_nopocust" value ='-' readonly>
                          </div>
                        </div>
                      </div>
                    </div>
  
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Tgl Kirim</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="date" class="form-control text-center" id="input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
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
            <table id="tabel_add" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">No. Retur Jual</th>
                  <th style="padding: 4px 12px;" scope="col">No. Beli</th>
                  <th style="padding: 4px 12px;" scope="col" id="th_action_add">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
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
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>

          <div class="row" style='margin-top:-25px;'>
            <div class="col-md-12">
              <div class="row">

                <!-- START OF ITEM No. R. Jual, No Beli, & Supplier -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <input type="text" class="form-control" id="inputitem_urut" hidden>

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>No. R. Jual</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_norjual" readonly>
                            <input type="text" class="form-control" id="input_add_add_urutRJual" hidden>
                            <button class="btn btn-primary btn-sm text-right" onclick='buttonNoRJual()' hidden>
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>No. Beli</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_nobeli" onchange='lockSupplier()'>
                            <input type="text" class="form-control" id="input_add_add_urutPbl" hidden>
                            <button id="buttonBrowseNoBeli" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick='buttonNoBeli()'>
                              <i class="bi bi-search"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Supplier</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <div class="input-group form-group">
                              <input type="text" class="form-control" id="input_add_add_supplier" readonly>
                              <button class="btn btn-primary btn-sm text-right" id="buttonSupplier" onclick='buttonSupplier()' hidden>
                                <i class="bi bi-plus"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM No. R. Jual, No Beli, & Supplier -->


                <!-- START OF ITEM Gudang, Kode Barang, Nama Barang -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Kode Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_kodebrg" >
                            <button id="buttonBrowseBarang" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick='buttonBarang()'>
                              <i class="bi bi-search"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Nama Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_add_namabrg" readonly>
                          </div>
                        </div>
                      </div>

                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Quantity</label>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="input-group form-group">
                            <input type="number" class="form-control" id="input_add_add_quantity">
                          </div>
                        </div>

                        <div class="col-2">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>
                        <div class="col-3">
                          <div class="input-group form-group">
                            <select id="input_add_add_satuan" class="form-control form-select-lg text-center" aria-label=".form-select-lg example" disabled>
                            </select>
                          </div>
                          
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Gudang, Kode Barang, Nama Barang -->


                <!-- START OF ITEM Quantity, Satuan, Keterangan -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-3">
                          <div class="form-group">
                            <label>Gudang</label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="input-group form-group">
                            <!-- Gudang dipilih lewat dropdown (diisi loadOptionGudang() dari prblistgudang), menggantikan browse modal -->
                            <select id="input_add_add_gudang" class="form-control form-select-lg text-left" aria-label=".form-select-lg example">
                              <option value="">Pilih Gudang</option>
                            </select>
                            <!-- NONAKTIF: tombol browse gudang, diganti dropdown di atas
                            <button id="buttonBrowseGudang" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" onclick='buttonGudang()'>
                              <i class="bi bi-search"></i>
                            </button>
                            -->
                          </div>
                        </div>
                      </div>

                      <div class="row" style='margin-top:-12px;'>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Keterangan</label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="form-group">
                            <textarea style="width: 100%; resize: none" rows="3" placeholder="Keterangan" class="form-control text-center" id="input_add_add_keterangan"></textarea>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Quantity, Satuan, Keterangan -->

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

              <button type="button" id="submitAddEdit" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddEdit()">Simpan</button>
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
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>

  <div class="container-fluid" style="margin-top: -10px;">
  {{-- <div class="row">
    
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
        <input type="number" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
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

  </div> --}}
</div>

</div>{{-- /#formBsGrid --}}
</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
<div id="formBsGrid">
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail SO</h2>
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
            <input type="text" class="form-control text-left" id="input_detail_nobukti" placeholder="" disabled>
          </div>
        </div>

      <div class="col-md-4" style="margin-top:-12px;">
        <div class="form-group">
          <label>Tanggal</label>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:-12px;">
        <div class="form-group">
          <input type="date" class="form-control text-left" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
      </div>


      <div class="col-md-4" style="margin-top:-10px;">
        <div class="form-group">
          <label>Pelanggan</label>
        </div>
      </div>


    <div class="col-md-8" style="margin-top:-10px;">
      <div class="input-group form-group">
        <input type="text" class="form-control text-left" id="input_detail_kodepelanggan" disabled>
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
            <input type="text" class="form-control text-left" id="input_detail_namapelanggan"  disabled>
          </div>
        </div>
        <!-- </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="row"> -->


        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-left" id="input_detail_alamatpelanggan" disabled></textarea>
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
          <select  id="input_detail_pembayaran" disabled class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Non-Kredit</option>
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
                <input type="date" class="form-control text-center" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
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

@include('purchasing/modals/modalPRBAdd')

<!-- modal filter status otorisasi Perintah Retur Beli -->
<div class="modal fade rt-filter" id="modalFilterPRB">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Perintah Retur Beli
          <span class="rt-active-badge" id="prbFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPRB').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="prbModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="prbModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="prbResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterPRB').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="prbTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@section('js')
{{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + tombol
     "Reset kolom"), disamakan dengan newpo.blade.php / uangmukabeli.blade.php. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

    window.onload = function(){
      loadOptionGudang();
      loadAll();
    };
    
let dataTableAdd = []
let dataTableEdit = []

let dataRefresh = []

let dataAddAddListItem = []

let listAlamatKirim = []

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let nosatTemp = 0
let isi1Temp = 0
let isi2Temp = 0
let urutTemp = 0

let tipeform = ''
let tipeformitem = ''

function buttonOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('prbupdateotorisasi') !!}",
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
        alertify.warning('Gagal Otorisasi')
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

  alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }        
let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('prbupdatebatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

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
	alertify.error("Action cancelled");
    });



}

function onChangeCatatan () {

  if (tipeform == 'detail') {
    return
  }

  let nobukti = $("#input_add_nobukti").val()
  if (!nobukti) {
    return
  }

  let value = $("#input_add_keterangan").val()
  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('prbonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field: 'KETERANGAN',
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update Keterangan berhasil')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}
function onChangeNoPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_noso").val()
    onChangeHeader('NoPesanan' , value)
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

function onChangeDP () {
  console.log('onChangeDP')
  if (tipeform == 'edit') {
    let value = $("#input_add_nopocust").val()
    console.log(value)
    onChangeHeader('DP' , value)
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
        let value = $("#input_add_discrp").val()
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

  console.log('submitAddAdd')

  let checkDate = new Date($("#input_add_tanggal").val())
  
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let Jmlrecord = 0 
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "I"

  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val() 
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_add_supplier").val()
  let KodeGdg = $("#input_add_add_gudang").val()

  let NoBeli = $("#input_add_add_nobeli").val()
  let Keterangan = $("#input_add_keterangan").val()
  // let FakturSupp = $("#input_add_lier").val() isi di controller
  // let Urut = $("#input_add_lier").val() isi di controller
  let KodeBrg = $("#input_add_add_kodebrg").val()
  let UrutPBL = $("#input_add_add_urutPbl").val()
  let Qnt = $("#input_add_add_quantity").val()
  let NoSat = nosatTemp
  let Satuan = $("#input_add_add_satuan").val()
  let Qnt1 = $("#input_add_add_quantity").val()
  let Qnt2 = $("#input_add_lier").val()
  let NORJual = $("#input_add_add_norjual").val()
  let UrutRJual = $("#input_add_add_urutRJual").val()
  let KETDET = $("#input_add_add_keterangan").val()

  console.log(tempAddAdd)

  let Isi = 0

  if (NoSat == 1) {
    Qnt2 = Qnt * isi1Temp
    Isi = isi1Temp
  }
  if (NoSat == 2) {
    Qnt2 = Qnt * isi2Temp
    Isi = isi2Temp
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log(
    "I",
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    '',
    0,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    0,
    '',
    NORJual,
    UrutRJual,
    Jmlrecord,
    KETDET,
    0
  )

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !KodeGdg) {
    alertify.warning("Data belum lengkap")
    return
  }

  // if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
  //   alertify.warning("Angka negatif")
  //   return
  // }

  $.ajax({
    url: "{!! url('prbspadd') !!}",
    type: "post",
    async: false,
    data: {

    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    NORJual,
    UrutRJual,
    Jmlrecord,
    KETDET

    },
    success: function(res) {
      
      if (res == 1) {

        loadAll()
        tipeform = 'edit'

        // $('#divhargaterakhir').hide();
        // $('#divStockProyeksi').hide();
        cleanFormAddAdd()

        refreshDataTableAdd(NoBukti)

        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
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

   let Jmlrecord = 0 
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "U"

  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val() 
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_add_supplier").val()
  let KodeGdg = $("#input_add_add_gudang").val()

  let NoBeli = $("#input_add_add_nobeli").val()
  let Keterangan = $("#input_add_keterangan").val()
  // let FakturSupp = $("#input_add_lier").val() isi di controller
  let KodeBrg = $("#input_add_add_kodebrg").val()
  let UrutPBL = $("#input_add_add_urutPbl").val()
  let Qnt = $("#input_add_add_quantity").val()
  let NoSat = nosatTemp
  let Satuan = $("#input_add_add_satuan").val()
  let Qnt1 = $("#input_add_add_quantity").val()
  let Qnt2 = $("#input_add_lier").val()
  let NORJual = $("#input_add_add_norjual").val()
  let UrutRJual = $("#input_add_add_urutRJual").val()
  let KETDET = $("#input_add_add_keterangan").val()

  console.log(tempAddAdd)

  let Isi = 0

  if (NoSat == 1) {
    Qnt2 = Qnt * isi1Temp
    Isi = isi1Temp
  }
  if (NoSat == 2) {
    Qnt2 = Qnt * isi2Temp
    Isi = isi2Temp
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log(
    "U",
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    '',
    0,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    0,
    '',
    NORJual,
    UrutRJual,
    Jmlrecord,
    urutTemp,
    KETDET,
    0
  )


  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !KodeGdg) {
    alertify.warning("Data belum lengkap")
    return
  }

  // if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
  //   alertify.warning("Angka negatif")
  //   return
  // }

  $.ajax({
    url: "{!! url('prbspadd') !!}",
    type: "post",
    async: false,
    data: {

    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    KodeSupp,
    KodeGdg,
    NoBeli,
    Keterangan,
    KodeBrg,
    UrutPBL,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Qnt1,
    Qnt2,
    NORJual,
    UrutRJual,
    Urut : urutTemp,
    Jmlrecord,
    KETDET

    },
    success: function(res) {
      console.log('resspsoaddedit', res)
      loadAll()

      // lockFormAdd()
      $('.showhide').hide();
      refreshDataTableAdd(NoBukti)

      alertify.success('Berhasil edit item')

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
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_disc").value = '0.00'
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

  document.getElementById('input_add_add_norjual').value = '-'
  document.getElementById('input_add_add_nobeli').value = '-'
  document.getElementById('input_add_add_supplier').value = ''
  document.getElementById('input_add_add_kodebrg').value = ''
  document.getElementById('input_add_add_namabrg').value = ''
  document.getElementById('input_add_add_quantity').value = ''
  document.getElementById('input_add_add_satuan').innerHTML = ''
  document.getElementById('input_add_add_gudang').value = ''
  document.getElementById('input_add_add_keterangan').value = ''

  document.getElementById('input_add_add_nobeli').disabled = false
  document.getElementById('input_add_add_kodebrg').disabled = false
  document.getElementById('input_add_add_gudang').disabled = false
  document.getElementById('buttonBrowseBarang').disabled = false
  // NONAKTIF: tombol browse gudang diganti dropdown
  // document.getElementById('buttonBrowseGudang').disabled = false
  document.getElementById('buttonBrowseNoBeli').disabled = false

  // cleanFormAddAdd()
  // document.getElementById("buttonAddAddListBarang").disabled = false
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  // document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function showTableHargaTerakhir () {
  if ( $("#divStockProyeksi").is(':visible')) 
  {
    $('#divStockProyeksi').hide();
  }

  if (!$("#divhargaterakhir").is(':visible')) 
  {
    $('#divhargaterakhir').show();
  } else 
  {
    $('#divhargaterakhir').hide();
  }
}

function showTableStockProyeksi () {
  if ($("#divhargaterakhir").is(':visible')) 
  {
    $('#divhargaterakhir').hide();
  }
  
  if (!$("#divStockProyeksi").is(':visible'))
  {
    $('#divStockProyeksi').show();
  } else 
  {
    $('#divStockProyeksi').hide();
  }
}

function buttonAddEditItem (i) {
  tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.showhide').hide();
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]

  let selectOption = ''
  if (tempAddEdit.SATUAN) {
    selectOption += `<option value=1 selected>${tempAddEdit.SATUAN}</option>`
  }

  if (tempAddEdit.NoPNW == ''){
    tempAddEdit.NoPNW = '-' 
  }

  urutTemp = tempAddEdit.URUT

  document.getElementById("input_add_add_norjual").value = tempAddEdit.NORJual
  document.getElementById("input_add_add_nobeli").value = tempAddEdit.nopbl
  document.getElementById("input_add_add_supplier").value = tempAddEdit.KODESUPP
  document.getElementById("input_add_add_kodebrg").value = tempAddEdit.KODEBRG
  document.getElementById("input_add_add_namabrg").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_quantity").value = tempAddEdit.QNT

  let satuanEditTemp = `<option value="${tempAddEdit.SATUAN}" selected>${tempAddEdit.SATUAN}</option>`
  document.getElementById("input_add_add_satuan").innerHTML = satuanEditTemp

  nosatTemp = tempAddEdit.NOSAT ?? nosatTemp
  isi1Temp = tempAddEdit.brgIsi1 ?? isi1Temp
  isi2Temp = tempAddEdit.brgIsi2 ?? isi2Temp

  setInputGudang(tempAddEdit.KodeGdg)
  document.getElementById("input_add_add_keterangan").value = tempAddEdit.ketdet ?? ''
  document.getElementById("input_add_add_urutPbl").value = tempAddEdit.URUTPBL
  document.getElementById("input_add_add_urutRJual").value = tempAddEdit.UrutRJual

  document.getElementById("input_add_add_nobeli").disabled = true
  document.getElementById("input_add_add_kodebrg").disabled = true
  document.getElementById("input_add_add_gudang").disabled = true
  document.getElementById("buttonBrowseBarang").disabled = true
  // NONAKTIF: tombol browse gudang diganti dropdown
  // document.getElementById("buttonBrowseGudang").disabled = true
  document.getElementById("buttonBrowseNoBeli").disabled = true

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

}

function closeShowHideAdd () {
  $('.showhide').hide();

}


function setNewNoBukti () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'PRB',
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

function buttonAddAddListPWO () {

  let _token = $("#_token").val();
  let noSo = $("#input_add_noso").val();

  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('polistpwo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noSo
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.no_bukti}</td>
          <td>${item.tanggal}</td>
          <td>${item.supplier}</td>
          <td>${item.kode}</td>
          <td>${item.NAMABRG}</td>
          <td>${formatAngka(item.qty)}</td>
          <td>${item.satuan}</td>
          <td>${item.harga}</td>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pwo").innerHTML = rowTable

      document.getElementById("namaHeaderTable").textContent = 'Nomor Penawaran PO'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPWO').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddAddListBarang () {

  let _token = $("#_token").val();
  let foc = $("#input_add_add_foc").val();
  let noSo = $("#input_add_noso").val();
  
  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  if (foc == 0 & noSo == '-') {
    if ( noBuktiUntukAdd != 0){

    $('#tabel_add_list_barang_nonfoc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosominus') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        noBukti : noBuktiUntukAdd
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOC(${i})" type="button" ><i class="bi bi-plus"></i>
              </button>
            </td>
            <td style="white-space:nowrap;">${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.PartNumber}</td>
            <td style="white-space:nowrap;">${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td style="white-space:nowrap;">${item.Sat}</td>
            <td style="white-space:nowrap;">${formatAngka(item.Qnt)}</td>
            <td style="white-space:nowrap;">${formatAngka(item.QntPO)}</td>
            <td style="white-space:nowrap;">${item.SisaPPL}</td>
            <td style="white-space:nowrap;">${item.NoBukti}</td>
            <td style="white-space:nowrap;">${item.NosoCust}</td>
          </tr>`
        });

        if(!res.length) {
          rowTable= ``
        }
        document.getElementById("tabel_data_add_list_barang_nonfoc").innerHTML = rowTable

        $("#tabel_add_list_barang_nonfoc").DataTable({
          "lengthChange": false,
            "paging": false ,
        });
        document.getElementById("namaHeaderTable").textContent = 'Barang Tanpa FOC'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    }) }
    else if ( noBuktiUntukAdd == 0){
      
    $('#tabel_add_list_barang_nonfoc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosominusallso') !!}",
      type: "post",
      async: false,
      data: {
        _token
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOC(${i})" type="button" ><i class="bi bi-plus"></i>
              </button>
            </td>
            <td style="white-space:nowrap;">${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.PartNumber}</td>
            <td style="white-space:nowrap;">${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td style="white-space:nowrap;">${item.Sat}</td>
            <td style="white-space:nowrap;">${formatAngka(item.Qnt)}</td>
            <td style="white-space:nowrap;">${formatAngka(item.QntPO)}</td>
            <td style="white-space:nowrap;">${item.SisaPPL}</td>
            <td style="white-space:nowrap;">${item.NoBukti}</td>
            <td style="white-space:nowrap;">${item.NosoCust}</td>
          </tr>`
        });

        if(!res.length) {
          rowTable= ``
        }
        document.getElementById("tabel_data_add_list_barang_nonfoc").innerHTML = rowTable
        document.getElementById("namaHeaderTable").textContent = 'Barang Tanpa FOC'
        $("#tabel_add_list_barang_nonfoc").DataTable({
          "lengthChange": false,
            "paging": false ,
        });

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
  } else if (foc == 1) {
    console.log(foc + "- FOC")

    $('#tabel_add_list_barang_foc').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangfoc') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td style="white-space:nowrap;" class="text-center">
              <button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button>
            </td>
            <td style="white-space:nowrap;">${item.Kodebrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td style="white-space:nowrap;">${item.partNumber}</td>
            <td style="white-space:nowrap;">${item.NamaMerk}</td>
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_foc").innerHTML = rowTable

        $("#tabel_add_list_barang_foc").DataTable({
          "lengthChange": false,
            "paging": true ,
        });
        document.getElementById("namaHeaderTable").textContent = 'Barang (FOC)'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  } else {
    console.log(foc + " - FOC " +" //// "+ noSo + " - NOSO")

    $('#tabel_add_list_barang_nonfocplus').DataTable().destroy();

    $.ajax({
      url: "{!! url('polistbarangnosoplus') !!}",
      type: "get",
      async: false,
      data: {
        noSo
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res
        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr>
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddPickBarangNonFOCPlus(${i})" type="button" ><i class="bi bi-plus"></i></button></td>
            <td>${item.KodeBrg}</td>
            <td style="white-space:nowrap;">${item.NamaBrg}</td>
            <td>${formatAngka(item.Qnt)}</td>
            <td>${formatAngka(item.Qnt2)}</td>
            <td>${item.Sat}</td>
            <td>${item.SisaPPL}</td>
            <td>${item.Sisa2PPL}</td>
            <td>${item.NoBukti}</td>
            <td>${item.PartNumber}</td>
            
          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_nonfocplus").innerHTML = rowTable

        $("#tabel_add_list_barang_nonfocplus").DataTable({
          "lengthChange": false,
            "paging": true ,
        });
        document.getElementById("namaHeaderTable").textContent = 'Barang'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOCPlus').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  }
}

function buttonAddListNoSO () {

  let _token = $("#_token").val();

  $('#tabel_add_list_noSo').DataTable().destroy();
  $.ajax({
    url: "{!! url('polistnoso') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('-' , '-')" type="button" ><i class="bi bi-plus"></i></button></td> 
          <td>-</td>
          <td>-</td>
          <td>-</td>
          </tr>`

      listNoSo = res

      listNoSo.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickNoSO('${item.NOBUKTI}' , '${item.NoPesanan}')" type="button" ><i class="bi bi-plus"></i></button></td>
        
          <td>${item.NOBUKTI}</td>
          <td>${item.Tanggal}</td>
          <td>${item.NoPesanan}</td></tr>`
      });

      document.getElementById("tabel_data_add_list_noSo").innerHTML = rowTable
      $("#tabel_add_list_noSo").DataTable({
        "lengthChange": false,
        "paging": true,
      });
      document.getElementById("namaHeaderTable").textContent = 'Nomor SO'
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoSo').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListGudang () {

  let _token = $("#_token").val();

  $('#tabel_add_list_alamatkirim').DataTable().destroy();
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
      });

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

  $('#tabel_add_list_lokasipenerima').DataTable().destroy();

  $.ajax({
    url: "{!! url('polistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `<tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('-' , '-' )" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>-</td>
        <td>-</td>
        
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      $("#tabel_add_list_lokasipenerima").DataTable({
        "lengthChange": false,
        "paging": true,
      });

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
  
  $('#tabel_add_list_valas').DataTable().destroy();
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
  $('#tabel_add_list_pelanggan').DataTable().destroy();

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
        <tr>
        <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:10px;" onclick="buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}','${item.HARI}', '${item.PPN}')" type="button" ><i class="bi bi-plus"></i></button></td>

        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>
        
        </tr>`
      });


      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable
      $("#tabel_add_list_pelanggan").DataTable({
        "lengthChange": false,
          "paging": true ,
      });

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
  $('#tabel_add_list_sales').DataTable().destroy();
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
      
/* ==========================================================================
 * Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi),
 * urut 1 = tabel PRB gabungan (#tabel), urut 2 = tabel List Retur Jual
 * (#tabel2) - sama pola dengan newpo.blade.php (satu #rtBar dipindah lewat
 * prbPindahBar()) digabung dengan modal filter otorisasi ala uangmukabeli.blade.php.
 * ========================================================================== */
const PRB_HREF = 'perintahreturbeli'
let prbCart = { 1: [], 2: [] }
let prbActiveUrut = 1
let prbPanjangHalaman = { 1: 10, 2: 10 }
let prbRtSudahInit = false
const PRB_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
// Tabel di tab yang TIDAK aktif tidak digambar saat loadAll() - cukup ditandai di sini,
// lalu digambar sungguhan saat tabnya dibuka (lihat handler shown.bs.tab). Sama pola
// dengan npoPerluGambar di newpo.blade.php.
let prbPerluGambar = { 1: false, 2: false }

let dataPRB = []
let dataRJual = []

const PRB_LABEL_1 = { NoBukti: 'No. Bukti', Tanggal: 'Tanggal' }
const PRB_LABEL_2 = { NomorRetur: 'Nomor Retur', KodeBrg: 'Kode Barang', NamaBrg: 'Nama Barang', Satuan: 'Satuan', Qty: 'Qty' }

// Kelas badge disamakan dengan kolom Status di purchaseorder: hijau=selesai,
// biru=belum, kuning=sebagian. Status datang jadi dari server (vwBrowsPrRBeli),
// jadi di sini tidak ada perhitungan qnt seperti poStatusPO().
const PRB_BADGE_STATUS = {
  'Sudah'    : 'is-active',
  'Belum'    : 'is-user',
  'Sebagian' : 'is-supervisor'
}

function prbBadgeStatus (item) {
  let status = item.Status || 'Belum'
  let kelas = PRB_BADGE_STATUS[status] || ''
  return `<span class="sp-badge ${kelas}">${status}</span>`
}

window.g_href = PRB_HREF
window.g_modeReport = 1
window.gcart_header = []

function prbBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered, labelMap) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let tipeNama = { 0: 'varchar', 1: 'float', 2: 'date' }
    let label = (labelMap && labelMap[h]) || h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias && aliasordered[i].alias !== h) {
      label = aliasordered[i].alias
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],        // 0 nama field di data
      label,                             // 1 judul kolom
      Number(isshowns[i]) === 1 ? 1 : 0, // 2 tampil
      tipeNama[tipe] || 'varchar',       // 3 tipe data
      0,                                 // 4 total (tidak dipakai halaman ini)
      isNaN(des) ? 0 : des,              // 5 jumlah desimal
      h,                                 // 6 header asli
      values[i],                         // 7 value asli
      tipe                               // 8 isnumeric asli
    ])
  });
  return cart
}

// Kolom yang tampil. WAJIB hasil filter() dari cart, bukan map/salinan - lihat catatan
// yang sama di uangmukabeli.blade.php / newpo.blade.php.
function prbKolomTampil (urut) {
  return (prbCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function prbKolomRender (c) {
  return { field: c[0], label: c[1], tipe: Number(c[8]), desimal: Number(c[5]) }
}

// formatAngka() selalu menempelkan '.' + bagian desimal - dipakai versi yang sadar jumlah
// desimal, sama seperti umbFormatAngkaDes() di uangmukabeli.blade.php.
function prbFormatAngkaDes (nilai, des) {
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

function prbRenderNilai (col, item) {
  let nilai = item ? item[col.field] : undefined
  if (col.tipe === 1) {
    return prbFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ''
  }
  return (nilai === null || nilai === undefined) ? '' : nilai
}

// Kalau public/js/report-table.js belum ikut terunggah, halaman tetap tampil dengan
// <th> biasa, hanya tanpa drag & roda gigi.
function prbHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => { html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>` });
  return html + '</tr>'
}

function prbUrutTabAktif () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

function prbAktifkanTabel (urut) {
  prbActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = prbCart[urut]
}

function prbOnChangeAktif () {
  if (prbActiveUrut === 2) {
    renderTabelRJual()
  } else {
    renderTabelPRB()
  }
}

// Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman - sama
// alasannya dengan newpo.blade.php / uangmukabeli.blade.php.
function prbInitReportTableSekali () {
  if (prbRtSudahInit || typeof ReportTable === 'undefined') { return }
  prbRtSudahInit = true

  let urutAktif = prbUrutTabAktif()
  let idTabel = { 1: '#tabel', 2: '#tabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], onChange: prbOnChangeAktif })
  });

  ReportTable.init({
    table: PRB_SELEKTOR_TABEL_AKTIF,
    bar: '#rtBar',
    onChange: prbOnChangeAktif
  })

  // DataTables memasang handler sort langsung di tiap <th>, sedangkan roda gigi/drag milik
  // report-table.js didelegasikan di <thead> - hentikan event aslinya di fase capture, lalu
  // tembakkan ulang satu event click baru langsung ke <thead>. Sama solusinya dengan
  // newpo.blade.php / uangmukabeli.blade.php.
  let prbGuardUlangKlik = false
  let idThead = ['tabel_header', 'tabel2_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (prbGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      prbGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      prbGuardUlangKlik = false
    }, true)
  });
}

// Pindahkan elemen #rtBar supaya duduk tepat sebelum tabel yang sedang aktif - sama
// catatan/bug-fix dengan npoPindahBar()/umbPindahBar().
function prbPindahBar (urut) {
  let bar = document.getElementById('rtBar')
  let id = urut === 2 ? 'tabel2' : 'tabel'
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

// Kotak scroll tabel dibuat setinggi sisa ruang di #content, sama seperti
// npoAturTinggiTabel() di newpo.blade.php.
function prbAturTinggiTabel () {
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

// Kotak search & dropdown "Tampilkan" - statis di blade, diikat sekali lewat
// dataset.rtBound karena renderTabelPRB()/renderTabelRJual() destroy+init tiap kali kolom
// digeser/disembunyikan.
function prbIkatSearch (urut) {
  let id = urut === 2 ? 'prbSearch2' : 'prbSearch1'
  let tabelId = urut === 2 ? '#tabel2' : '#tabel'
  let input = document.getElementById(id)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  input.addEventListener('input', function () {
    $(tabelId).DataTable().search(input.value).draw()
  })
}

function prbIkatPanjangHalaman (urut) {
  let id = urut === 2 ? 'prbLen2' : 'prbLen1'
  let tabelId = urut === 2 ? '#tabel2' : '#tabel'
  let sel = document.getElementById(id)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(prbPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    prbPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    $(tabelId).DataTable().page.len(prbPanjangHalaman[urut]).draw()
  })
}

// Ubah salah satu tanggal periode -> muat ulang data dari server. urut 1 = tab Perintah
// Retur Beli, urut 2 = tab List Retur Jual (difilter lewat tanggal SPR di dbSPBRJual).
function prbIkatPeriode (urut) {
  let idAwal  = urut === 2 ? 'prbTglAwal2'  : 'prbTglAwal1'
  let idAkhir = urut === 2 ? 'prbTglAkhir2' : 'prbTglAkhir1'
  let awal  = document.getElementById(idAwal)
  let akhir = document.getElementById(idAkhir)
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  // Saat tanggal diketik manual, browser mengirim 'change' tiap digit tahun (0002, 0020,
  // 0201...) - tahun setengah jadi jangan sampai memicu request. Lihat juga penjaga tahun
  // di PerintahReturBeliController@loadAll.
  let sahTanggal = function (v) {
    return /^\d{4}-\d{2}-\d{2}$/.test(v) && Number(v.slice(0, 4)) >= 1900
  }

  let onUbah = function () {
    if (!sahTanggal(awal.value) || !sahTanggal(akhir.value)) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

// 'SEMUA' = tidak menyaring. Disimpan di luar renderTabelPRB() supaya tetap berlaku saat
// tabel digambar ulang (sehabis simpan, otorisasi, dst).
let prbFilterOtorisasi = 'SEMUA'

function prbOtorisasiPRB (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

function prbUpdateFilterBadge () {
  let jml = (prbFilterOtorisasi !== 'SEMUA') ? 1 : 0
  let badge = document.getElementById('prbFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function prbTerapkanFilter () {
  prbFilterOtorisasi = $('#prbModalOtorisasi').val() || 'SEMUA'
  prbUpdateFilterBadge()
  $('#modalFilterPRB').modal('hide')
  renderTabelPRB()
}

function prbResetFilter () {
  prbFilterOtorisasi = 'SEMUA'
  $('#prbModalOtorisasi').val('SEMUA')
  prbUpdateFilterBadge()
  $('#modalFilterPRB').modal('hide')
  renderTabelPRB()
}

/* ---- Jembatan ke mesin penyimpan milik report-table.js ----
 * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
 * didefinisikan - report-table.js sudah punya fallback yang memutasi gcart_header sendiri
 * lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke doSimpanHeader di bawah. */
function prbUrutSah (mode) {
  return Number(mode) === 2 ? 2 : 1
}

window.doSimpanHeader = function (href, mode) {
  let urut = prbUrutSah(mode)
  let cart = prbCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url: "{!! url('saveheadertable') !!}",
    type: "post",
    async: false,
    data: {
      _token: $("#_token").val(),
      header: JSON.stringify(header),
      isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal),
      value: JSON.stringify(value),
      isshown: JSON.stringify(isshown),
      href: PRB_HREF,
      urut: urut
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Dipakai tombol "Reset kolom" di bar. Harus async:false karena report-table.js langsung
// menggambar ulang setelahnya.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }
  let urut = prbUrutSah(mode)

  $.ajax({
    url: "{!! url('getheadertable') !!}",
    type: "post",
    async: false,
    data: {
      _token: $("#_token").val(),
      href: PRB_HREF,
      urut: urut,
      reset: 1
    },
    success: function (res) {
      if (urut === 2) {
        prbCart[2] = prbBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, PRB_LABEL_2)
      } else {
        prbCart[1] = prbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, PRB_LABEL_1)
      }
      window.gcart_header = prbCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

// Tab "Perintah Retur Beli" (urut 1) - tabel PRB gabungan (dulu tab "Perintah Retur Beli" +
// "Sudah Otorisasi"). Tombol aksi ikut status otorisasi barisnya, sama seperti
// renderTabelUMB() di uangmukabeli.blade.php.
function renderTabelPRB () {
  prbAktifkanTabel(1)

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = prbKolomTampil(1)
  let kolomRender = cols.map(prbKolomRender)

  let thead = document.getElementById('tabel_header')
  thead.innerHTML = prbHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
      <th style="padding: 4px 12px;" scope="col">Status</th>
    `)
  }

  let dataTampil = dataPRB || []
  if (prbFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return prbOtorisasiPRB(item) === prbFilterOtorisasi })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let isOtorisasi = Number(item.IsOtorisasi1) || 0
    let nobukti = item.NoBukti || ''

    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${nobukti}')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${nobukti}')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${nobukti}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('${nobukti}')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${nobukti}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${prbRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${prbRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${isOtorisasi ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${item.OtoUser1 || ''}</td>
      <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
      <td class="text-center">${prbBadgeStatus(item)}</td>
    </tr>`;
  });

  // Baris "Tidak ada data" TIDAK ditulis manual di sini - baris manual hanya berisi 1 sel
  // sedangkan header punya banyak kolom, dan DataTables mencoba mengindeks sel-sel yang
  // tidak ada di situ lalu crash (_DT_CellIndex). Biarkan <tbody> kosong dan serahkan ke
  // opsi language.emptyTable di bawah.
  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel').DataTable({
    lengthChange: false,
    pageLength: prbPanjangHalaman[1],
    // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom Actions).
    // Data sudah datang terurut dari server (Tanggal/NoBukti terbaru dulu - lihat
    // PerintahReturBeliController@loadAll), jadi urutan DOM dipertahankan apa adanya.
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    },
    drawCallback: function () {
      setTimeout(prbAturTinggiTabel, 0)
    }
  })

  prbPindahBar(1)
  prbIkatSearch(1)
  prbIkatPanjangHalaman(1)
  prbIkatPeriode(1)
  let inputSearch = document.getElementById('prbSearch1')
  if (inputSearch && inputSearch.value) {
    $('#tabel').DataTable().search(inputSearch.value).draw()
  }
  prbAturTinggiTabel()
}

// Tab "List Retur Jual" (urut 2) - datanya tidak difilter tanggal & tidak punya tombol aksi,
// sama seperti tampilan lama.
function renderTabelRJual () {
  prbAktifkanTabel(2)

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  let cols = prbKolomTampil(2)
  let kolomRender = cols.map(prbKolomRender)

  let thead = document.getElementById('tabel2_header')
  thead.innerHTML = prbHeadHtml(cols)

  let rowTable = ''
  dataRJual.forEach((item) => {
    rowTable += '<tr>'
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${prbRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${prbRenderNilai(c, item)}</td>`
      }
    });
    rowTable += '</tr>'
  });

  document.getElementById('tabel2_data').innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: prbPanjangHalaman[2],
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    },
    drawCallback: function () {
      setTimeout(prbAturTinggiTabel, 0)
    }
  })

  prbPindahBar(2)
  prbIkatSearch(2)
  prbIkatPanjangHalaman(2)
  prbIkatPeriode(2)
  let inputSearch = document.getElementById('prbSearch2')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
  prbAturTinggiTabel()
}

function loadAll () {
  // Idempotent - hanya benar-benar mengikat sekali seumur halaman.
  prbInitReportTableSekali()

  let urutAktif = prbUrutTabAktif()
  prbPerluGambar[1] = (urutAktif !== 1)
  prbPerluGambar[2] = (urutAktif !== 2)

  $.ajax({
    url: "{!! url('prbloadall') !!}",
    type: "GET",
    data: {
      tglawal: $('#prbTglAwal1').val(),
      tglakhir: $('#prbTglAkhir1').val(),
      tglawal2: $('#prbTglAwal2').val(),
      tglakhir2: $('#prbTglAkhir2').val()
    },
    success: function (res) {
      prbCart[1] = prbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, PRB_LABEL_1)
      prbCart[2] = prbBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, PRB_LABEL_2)
      window.gcart_header = prbCart[prbActiveUrut]

      dataPRB = res.listPRB || []
      dataRJual = res.listRJual || []

      if (urutAktif === 2) {
        renderTabelRJual()
      } else {
        renderTabelPRB()
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

$('#nav-home-tab').on('shown.bs.tab', function () {
  prbAktifkanTabel(1)
  prbPindahBar(1)

  if (prbPerluGambar[1]) {
    prbPerluGambar[1] = false
    renderTabelPRB()
    return
  }

  if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
  prbAturTinggiTabel()
})

$('#nav-profile-tab').on('shown.bs.tab', function () {
  prbAktifkanTabel(2)
  prbPindahBar(2)

  if (prbPerluGambar[2]) {
    prbPerluGambar[2] = false
    renderTabelRJual()
    return
  }

  if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
  prbAturTinggiTabel()
})

// Layar diubah ukurannya - tinggi kotak tabel diukur ulang, didebounce.
let prbTimerResize = null
$(window).on('resize', function () {
  if (prbTimerResize) { clearTimeout(prbTimerResize) }
  prbTimerResize = setTimeout(prbAturTinggiTabel, 150)
})


// ========== Helper: Date Formatter ==========
function formatDate(dateString) {
  const date = new Date(dateString);
  if (isNaN(date)) return "";
  const day = ("0" + date.getDate()).slice(-2);
  const month = ("0" + (date.getMonth() + 1)).slice(-2);
  return `${date.getFullYear()}/${month}/${day}`;
}

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

function buttonAddAddPickBarangFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.Kodebrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0
  // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("input_add_add_discrp").value = '0.00'

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
      kodebarang : tempAddAdd.Kodebrg,
      nosat : 1
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

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].Xharga).toFixed(2)
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
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


let cekQntPR = 0
let cekQntPO = 0
let cekQntSisa = 0

function cekQntStock () {
  
  tempStockAdd = dataAddAddListItem[0]

  let currentQntPO = 0

  cekQntPR = tempStockAdd.Qnt
  
  cekQntPO = tempStockAdd.QntPO
  cekQntSisa = tempStockAdd.SisaPPL

  currentQntPO = parseInt(document.getElementById("input_add_add_qty").value) || 0

  console.log(currentQntPO + ' current qnt PO')

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
  document.getElementById("input_add_add_qty").value = tempAddAdd.SisaPPL
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'
  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1>1-${tempSatuanBarang[0].SAT1}</option>`
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

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].HARGA).toFixed(2)
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
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function lockSupplier (){
  let noBeliCek = document.getElementById('input_add_add_nobeli').value
  console.log(noBeliCek)
  if ( noBeliCek == '-'){
    document.getElementById('input_add_add_supplier').disabled = false
    document.getElementById('buttonSupplier').hidden = false
    document.getElementById('input_add_add_supplier').value = ''
  }
  else if ( noBeliCek != '-'){
    document.getElementById('input_add_add_supplier').disabled = true
    document.getElementById('buttonSupplier').hidden = true

  }

}

function buttonAddAddPickBarangNonFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = tempAddAdd.Qnt
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'

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

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = parseFloat(res[0].HARGA).toFixed(2)
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
  document.getElementById("input_add_kodealamatkirim").value = ''
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'

  if (hari == 0 ){
    selectTipeBayar = `<option value=0 selected>Non-Kredit</option>
    <option value=1>Kredit</option>`
  }
  else if (hari != 0){
    selectTipeBayar = `
    <option value=0 >Non-Kredit</option>
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

function buttonAddPickNoSO (kode, nama) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

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

function buttonAddPickPWO (kode, nama) {
  console.log('buttonAddPickPWO')
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

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

function cleanFormAddAdd () {
  document.getElementById("input_add_add_norjual").value = '-'
  document.getElementById("input_add_add_nobeli").value = '-'
  document.getElementById("input_add_add_supplier").value = ''
  document.getElementById("input_add_add_kodebrg").value = ''
  document.getElementById("input_add_add_namabrg").value = ''
  document.getElementById("input_add_add_quantity").value = ''
  document.getElementById("input_add_add_satuan").innerHTML = ''
  document.getElementById("input_add_add_gudang").value = ''
  document.getElementById("input_add_add_keterangan").value = ''

  document.getElementById("input_add_add_nobeli").disabled = false
  document.getElementById("input_add_add_kodebrg").disabled = false
  document.getElementById("input_add_add_gudang").disabled = false
  document.getElementById("buttonBrowseBarang").disabled = false
  // NONAKTIF: tombol browse gudang diganti dropdown
  // document.getElementById("buttonBrowseGudang").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_add_norjual").disabled = true
  document.getElementById("input_add_add_nobeli").disabled = true
  document.getElementById("input_add_add_supplier").disabled = true
  document.getElementById("input_add_add_kodebrg").disabled = true
  document.getElementById("input_add_add_namabrg").disabled = true
  document.getElementById("input_add_add_quantity").disabled = true
  document.getElementById("input_add_add_satuan").disabled = true
  document.getElementById("input_add_add_gudang").disabled = true
  document.getElementById("input_add_add_keterangan").disabled = true 
  
  document.getElementById("input_add_tipebayar").disabled = true
  document.getElementById("input_add_keterangan").disabled = true
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
  document.getElementById("input_add_add_norjual").disabled = false
  document.getElementById("input_add_add_nobeli").disabled = false
  document.getElementById("input_add_add_supplier").disabled = false
  document.getElementById("input_add_add_kodebrg").disabled = false
  document.getElementById("input_add_add_namabrg").disabled = false
  document.getElementById("input_add_add_quantity").disabled = false
  document.getElementById("input_add_add_satuan").disabled = false
  document.getElementById("input_add_add_gudang").disabled = false
  document.getElementById("input_add_add_keterangan").disabled = false

  // Pembayaran hanya boleh diisi saat Add; di Edit/Detail dokumen sudah ada, jadi terkunci.
  document.getElementById("input_add_tipebayar").disabled = (tipeform !== 'add')
  // Keterangan header boleh diedit di Add maupun Edit; hanya terkunci di Detail lewat lockFormAdd().
  document.getElementById("input_add_keterangan").disabled = false
}

function cleanFormAdd () {

  document.getElementById("input_add_tipebayar").value = '0'
  document.getElementById("input_add_keterangan").value = ''
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

  document.getElementById('buttonTambahItem').hidden = false;
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
    url: "{!! url('prbcekotorisasi') !!}",
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

  document.getElementById('buttonTambahItem').hidden = false;

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
  cleanFormAddAdd()
  unlockFormAdd()
  setNewNoBukti();

  refreshDataTableAdd()

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

  document.getElementById('buttonTambahItem').hidden = true;

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

  let thAction = document.getElementById("th_action_add")
  if (thAction) {
    thAction.style.display = (tipeform == 'detail') ? 'none' : ''
  }

  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="${tipeform == 'detail' ? 6 : 7}">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('prbgetdetail') !!}",
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
              <td>${item.KODEBRG}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.QNT ? formatAngka(Number(item.QNT).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td>${item.NORJual}</td>
              <td>${item.nopbl}</td>
              ${tipeform == 'detail' ? '' : `
              <td class="text-center">
                ${tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>`
              }
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="${tipeform == 'detail' ? 6 : 7}">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NOBUKTI
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.TANGGAL)
          document.getElementById("input_add_tipebayar").value = dataHeaderAdd.TIPEBAYAR
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.KETERANGAN

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

        let NoBukti = dataDelete.NOBUKTI
        let Urut = dataDelete.URUT

        $.ajax({
          url: "{!! url('prbspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            Choice,
            NoBukti,
            NoUrut : 0,
            Urut,
            Tanggal : '',
            KodeSupp : '',
            KodeGdg : '',
            NoBeli : '',
            Keterangan : '',
            KodeBrg : '',
            UrutPBL : 0,
            Qnt : 0,
            NoSat : 0,
            Satuan : '',
            Isi : 0,
            Qnt1 : 0,
            Qnt2 : 0,
            NORJual:'',
            UrutRJual : 0,
            Jmlrecord : 0,
            KETDET :''

          },
          success: function(res) {
            console.log('resspsoadd', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();

            refreshDataTableAdd(NoBukti)

            alertify.success('Berhasil menghapus item')

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

    $('#tabel_add_list_barangall').DataTable().destroy();

  }

}

function formatAngka (angkaString) {
  // console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

function calculateDiscRp() {
  let disc1 = document.getElementById('input_add_add_discpersen1').value
  let disc2 = document.getElementById('input_add_add_discpersen2').value
  let disc3 = document.getElementById('input_add_add_discpersen3').value
  
  let discRp = $('#input_add_add_harga').val()

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

  document.getElementById('input_add_add_discrp').value = totalDiscount
}

function LockFreeOfCharge(){
  let focState = document.getElementById('input_add_add_foc').value

  if (focState == 1){
    document.getElementById('input_add_add_harga').disabled = true;
    document.getElementById('input_add_add_discrp').disabled = true;
    document.getElementById('input_add_add_discpersen1').disabled = true;
    document.getElementById('input_add_add_discpersen2').disabled = true;
    document.getElementById('input_add_add_discpersen3').disabled = true;
    
    document.getElementById('input_add_add_harga').value = 0,00 ;
    document.getElementById('input_add_add_discrp').value = 0,00 ;
    document.getElementById('input_add_add_discpersen1').value = 0 ;
    document.getElementById('input_add_add_discpersen2').value = 0 ;
    document.getElementById('input_add_add_discpersen3').value = 0 ;
  } else {
    document.getElementById('input_add_add_harga').disabled = false;
    document.getElementById('input_add_add_discrp').disabled = false;
    document.getElementById('input_add_add_discpersen1').disabled = false;
    document.getElementById('input_add_add_discpersen2').disabled = false;
    document.getElementById('input_add_add_discpersen3').disabled = false;
  }

}

</script>
{{-- Warna tab aktif sekarang murni CSS (.custom-tabs .nav-link.active) + Bootstrap Tab
     (bs.tab), sama seperti newpo.blade.php - script pewarna tab manual (setActiveTab, yang
     dulu menunjuk juga ke #nav-profile1-tab yang sudah dihapus) tidak lagi diperlukan. --}}
  <script>
    function performSearch () {
      // input_add_add_kodebarang adalah sisa salinan dari modul lain - tidak ada di halaman
      // ini (id yang benar: input_add_add_kodebrg), jadi dijaga null di sini.
      const elKodeBrgCari = document.getElementById('input_add_add_kodebarang');
      const searchValue = elKodeBrgCari ? elKodeBrgCari.value.trim() : '';

      buttonAddAddListBarang();

      // Apply search to all DataTables
      $('#tabel_add_list_barang_nonfoc').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_nonfocplus').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_foc').DataTable().search(searchValue).draw();
    }

    // Keyboard event
    if (document.getElementById('input_add_add_kodebarang')) {
      document.getElementById('input_add_add_kodebarang').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
      });
    }


  function buttonNoRJual () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }
  $('#tabelModalOpen').removeClass('modalOpen-plain');
  $('#modalOpenCustomSearch').hide().find('input').val('');

  $.ajax({
    url: "{!! url('prblistnorjual') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectNoRJual('-','-')"><i class="bi bi-plus"></i></button>
      </td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
    </tr>`;
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectNoRJual('${item.NoBukti}','${item.Urut}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.NoBukti}</td>
      <td>${item.KodeCustSupp}</td>
      <td>${item.NamaCustSupp}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">No. Bukti</th>
    <th scope="col">Kode Customer</th>
    <th scope="col">Nama Customer</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Retur Jual'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectNoRJual(NoBukti, urut){
  document.getElementById('input_add_add_norjual').value = NoBukti;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonNoBeli () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('prblistnobeli') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      dataRefresh = res;
    },
  });

  let rowTable = `<tr class="pick-row" onclick="buttonSelectNoBeli('-','-','-')">
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
    </tr>`;
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectNoBeli('${item.NoBukti}','${item.KodeSupp}', '${item.Kodegdg}')">
      <td>${item.NoBukti}</td>
      <td>${String(item.Tanggal).split(' ')[0]}</td>
      <td>${item.KodeSupp}</td>
      <td>${item.namaCustSupp}</td>
      <td>${item.Kodegdg}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">No. Bukti</th>
    <th scope="col">Tanggal</th>
    <th scope="col">Kode Supp</th>
    <th scope="col">Nama Supp</th>
    <th scope="col">Kode Gudang</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Beli'

  // Dulu mode "plain" (semua baris + kotak cari sendiri yang menyembunyikan baris) - diganti
  // DataTables seperti buttonGudang() supaya ada dropdown "Tampilkan" jumlah data. Kotak
  // cari sendiri tidak dipakai lagi karena hanya bisa menyaring baris di halaman yang tampil.
  // order: [] supaya urutan baris tetap urutan dari server, sama seperti mode plain.
  // $('#tabelModalOpen').addClass('modalOpen-plain');
  // let $modalOpenSearch = $('#modalOpenCustomSearch').show().find('input').val('');
  // $modalOpenSearch.off('input').on('input', function () {
  //   let q = this.value.toLowerCase();
  //   $('#tabel_dataModalOpen tr').each(function () {
  //     $(this).toggle(this.textContent.toLowerCase().indexOf(q) > -1);
  //   });
  // });
  $('#tabelModalOpen').removeClass('modalOpen-plain');
  $('#modalOpenCustomSearch').hide().find('input').val('');

  pickerKasInit('tabelModalOpen', { "order": [] })

  $("#formModalOpen").modal('toggle')
}

function buttonSelectNoBeli(NoBukti, kodeSupp, kodegdg){
  document.getElementById('input_add_add_nobeli').value = NoBukti;
  document.getElementById('input_add_add_supplier').value = kodeSupp;
  setInputGudang(kodegdg);
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  lockSupplier()

  $("#formModalOpen").modal("hide");
}

function buttonSupplier () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }
  $('#tabelModalOpen').removeClass('modalOpen-plain');
  $('#modalOpenCustomSearch').hide().find('input').val('');

  $.ajax({
    url: "{!! url('prblistsupplier') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td class="text-center">
        <button class="btn btn-primary btn-sm" type="button" onclick="buttonSelectSupplier('${item.KodeCustSupp}')"><i class="bi bi-plus"></i></button>
      </td>
      <td>${item.KodeCustSupp}</td>
      <td>${item.NamaCustSupp}</td>
      <td>${item.Alamat}</td>
      <td>${item.namaKota}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Actions</th>
    <th scope="col">Kode</th>
    <th scope="col">Nama</th>
    <th scope="col">Alamat</th>
    <th scope="col">Kota</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'No. Beli'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectSupplier (NoBukti){
  document.getElementById('input_add_add_supplier').value = NoBukti;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function renderModalPickTable (headers, rows, callbacks) {
  let headerHtml = '<tr>' + headers.map(h => `<th scope="col">${h}</th>`).join('') + '</tr>'
  document.querySelector("#theadOpen").innerHTML = headerHtml

  // .pick-row: baris pilihan bergaya modal pemilih menu Kas (picker-kas.css).
  let bodyHtml = rows.map((r, i) => `<tr class="modalPickRow pick-row" data-idx="${i}" style="cursor:pointer">` + r.map(c => `<td>${c}</td>`).join('') + `</tr>`).join('')
  document.getElementById("tabel_dataModalOpen").innerHTML = bodyHtml

  window.modalPickCallbacks = callbacks

  $(document).off('click', '#tabel_dataModalOpen tr.modalPickRow').on('click', '#tabel_dataModalOpen tr.modalPickRow', function () {
    let idx = $(this).data('idx')
    if (window.modalPickCallbacks && window.modalPickCallbacks[idx]) {
      window.modalPickCallbacks[idx]()
    }
  })
}

function buttonGudang () {
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }
  $('#tabelModalOpen').removeClass('modalOpen-plain');
  $('#modalOpenCustomSearch').hide().find('input').val('');

  $.ajax({
    url: "{!! url('prblistgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      dataRefresh = res;
    },
  });

  renderModalPickTable(
    ['Kode', 'Nama', 'Alamat'],
    dataRefresh.map(item => [item.KodeGdg, item.Nama, item.Alamat]),
    dataRefresh.map(item => () => buttonSelectGudang(item.KodeGdg))
  );

  document.getElementById("namaModalOpen").innerHTML = 'Gudang'

  pickerKasInit('tabelModalOpen')

  $("#formModalOpen").modal('toggle')
}

function buttonSelectGudang (NoBukti){
  document.getElementById('input_add_add_gudang').value = NoBukti;
  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

// Isi dropdown gudang sekali saat halaman dibuka (gudang aktif dari prblistgudang).
function loadOptionGudang () {
  let select = document.getElementById('input_add_add_gudang')

  $.ajax({
    url: "{!! url('prblistgudang') !!}",
    type: "get",
    async: false,
    success: function (res) {
      (res || []).forEach((item) => {
        let kode = (item.KodeGdg ?? '').toString().trim()
        select.add(new Option(kode + ' ' + (item.Nama ?? ''), kode))
      })
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar gudang, silahkan refresh browser')
    }
  })
}

// Pilih gudang di dropdown. Kalau kodenya tidak ada di daftar (mis. gudang sudah tidak
// aktif tapi masih dipakai item lama), ditambahkan sebagai opsi supaya nilainya tidak
// hilang saat Edit disimpan.
function setInputGudang (kodegdg) {
  let select = document.getElementById('input_add_add_gudang')
  let kode = (kodegdg ?? '').toString().trim()
  let opsi = Array.from(select.options).find((o) => o.value.toUpperCase() === kode.toUpperCase())

  if (!opsi && kode) {
    opsi = new Option(kode, kode)
    select.add(opsi)
  }
  select.value = opsi ? opsi.value : ''
}


function buttonBarang () {
  let _token = $("#_token").val();

  const isEmpty = (value) => !value || value === '-';

  let kodeJual = document.getElementById('input_add_add_norjual').value
  let kodeBeli = document.getElementById('input_add_add_nobeli').value

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }
  $('#tabelModalOpen').removeClass('modalOpen-plain');
  $('#modalOpenCustomSearch').hide().find('input').val('');

if(isEmpty(kodeBeli)&&isEmpty(kodeJual)){
  alertify.warning('No Jual dan No Beli Kosong')
  return;
}

if (isEmpty(kodeBeli)){
  $.ajax({
    url: "{!! url('prblistbarangJualTanpaBeli') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeJual
    },
    success: function (res) {
      dataRefresh = res;

      renderModalPickTable(
        ['Kode Barang', 'Nama Barang'],
        dataRefresh.map(item => [item.Kodebrg, item.NamaBrg]),
        dataRefresh.map(item => () => buttonSelectBarang(item.Kodebrg, item.NamaBrg, item.Qnt, item.satuan, item.Nosat, item.ISI1, item.ISI2, '0', item.Urut))
      );

    },
  });
} else if (isEmpty(kodeJual)){
  $.ajax({
    url: "{!! url('prblistbarangBeliTanpaJual') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      noBeli : kodeBeli
    },
    success: function (res) {
      dataRefresh = res;

      renderModalPickTable(
        ['Kode Barang', 'Nama Barang'],
        dataRefresh.map(item => [item.KODEBRG, item.NAMABRG]),
        dataRefresh.map(item => () => buttonSelectBarang(item.KODEBRG, item.NAMABRG, item.Qnt, item.SATUAN, item.NOSAT, item.ISI1, item.ISI2, item.URUT, '0'))
      );
    },
  });

} else {
  $.ajax({
    url: "{!! url('prblistbarangJualDanBeli') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      noBeli : kodeBeli,
      kodeJual
    },
    success: function (res) {
      dataRefresh = res;

      renderModalPickTable(
        ['Kode Barang', 'Nama Barang'],
        dataRefresh.map(item => [item.KODEBRG, item.NAMABRG]),
        dataRefresh.map(item => () => buttonSelectBarang(item.KODEBRG, item.NAMABRG, item.QntTerima, item.SATUAN, item.NOSAT, item.ISI1, item.ISI2, item.URUT, '0'))
      );
    },
  });

}

  document.getElementById("namaModalOpen").innerHTML = 'Barang'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectBarang (kodebrg, namabrg, qnt, satuan, nosat, isi1, isi2, urut, urutJual){
  document.getElementById('input_add_add_kodebrg').value = kodebrg;
  document.getElementById('input_add_add_namabrg').value = namabrg;
  document.getElementById('input_add_add_urutPbl').value = urut;
  document.getElementById('input_add_add_urutRJual').value = urutJual;
  document.getElementById('input_add_add_quantity').value = qnt;

  let satuanTemp = `
    <option value="${satuan}" selected>${satuan}</option>
  `

  document.getElementById('input_add_add_satuan').innerHTML = satuanTemp

      nosatTemp = nosat ?? nosatTemp;
      isi1Temp = isi1 ?? isi1Temp;
      isi2Temp = isi2 ?? isi2Temp;

      console.log(nosatTemp, isi1Temp, isi2Temp)

  // document.getElementById('input_detailAkun_edit_hutPiut').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function submitPrint (nobukti) {

  let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('perintahreturbeliPrint') !!}",
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
    let tanggalOnly = dataPrint[0].TANGGAL.split(' ')[0];

    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    const tanggalCetak = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/')
    
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
                      <div class="pb-1" style="width: 20%">Reff No.</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOBUKTI}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${tanggalOnly}</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">BERITA ACARA RETUR</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Supplier</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NAMASUPPLIER}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 30%">BARANG</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">SUPPLIER</td>
                    <td class="text-center" style="width: 5%">PO BELI</td>
                    <td class="text-center" style="width: 5%">NOTA BELI</td>
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
      <td style='' class="no-border" style="width: 20%;">${z+1} ${itemSub.NAMABRG}</td>
      <td style='' class="no-border" style="width: 5%;">${formatAngka(itemSub.QNT)}  ${itemSub.SATUAN}</td>
      <td style='' class="no-border" style="width: 35%;">${itemSub.namaCustSupp}</td>
      <td style='' class="no-border" style="width: 20%;">${itemSub.POBELI}</td>
      <td style='' class="no-border" style="width: 20%;">${itemSub.NOBELI}</td>
    </tr>`;
  z++;
});
tempPrintStr += `
    <tr style="height: 24px;">
      <td  style='' class='no-border'>Kronologi:</td>
      <td  colspan=4 style='' class='no-border'>&nbsp;</td>
    </tr>`;
// Fill remaining empty rows � table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 6;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='' class='no-border'>&nbsp;</td>
      <td style='' class='no-border'>&nbsp;</td>
      <td style='' class='no-border'>&nbsp;</td>
      <td style='' class='no-border'>&nbsp;</td>
      <td style='' class='no-border'>&nbsp;</td>
    </tr>`;

    if (f == 3){
    tempPrintStr += `
    <tr style="height: 24px;">
      <td  style='' class='no-border'>Rekomendasi Barang yang Diretur:</td>
      <td  colspan=4 style='' class='no-border'>&nbsp;</td>
    </tr>`;
  }
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px; font-family: sans-serif; font-size: 10px;">

  <div style="width: 100%;">
    <div style="margin-bottom: 6px;">Banjarmasin, ${tanggalCetak}</div>

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 20px;">
      <tr>
        <td class="no-border text-center" style="width: 33%; font-size: 13px;">Di Buat Oleh,</td>
        <td class="no-border text-center" style="width: 33%; font-size: 13px;">Diketahui Oleh,</td>
        <td class="no-border text-center" style="width: 34%; font-size: 13px;">Disetujui Oleh,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size: 12px;">Nama :</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size: 12px;">Nama :</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size: 12px;">Nama :</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size: 12px;">Tanggal :</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size: 12px;">Tanggal :</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size: 12px;">Tanggal :</p>
        </td>
      </tr>
    </table>
  </div>

</div>`;


    tempPrintStr += `</div>`
  });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }


  </script>

@endsection
