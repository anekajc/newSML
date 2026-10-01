@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Penerimaan Gudang')

@endsection
{{-- tampilan tampilan baru: tab custom, tabel .data-table, header interaktif --}}
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

  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
  }
</style>
    {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + tombol
       "Reset kolom"), disamakan dengan resources/views/purchasing/purchaseOrder.blade.php /
       uangmukabeli.blade.php. --}}
  <link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
  /* Isi tabel di semua tab dibuat sebaris (tidak turun ke bawah). Kolom yang panjang
     cukup digeser lewat scroll horizontal .po-table-wrap (overflow:auto). */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
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

  /* Halaman ini dirancang mengisi tinggi layar, jadi padding atas #content layout
     dikecilkan - sama seperti purchaseOrder.blade.php / uangmukabeli.blade.php. */
  #content { padding-top: 12px; }

  /* Rule .card global di layout newmasterx (flex + align-items:center + efek melayang
     saat hover) untuk kartu menu dashboard, bukan kartu berisi tabel. */
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

  /* ---------- Kolom Aksi tabel (#tabel/#tabel2 di depan, #editPembelianTable di
     belakang) - tombol bulat kecil, warna pastel, disalin dari purchaseOrder.blade.php
     (lihat pola #tabel_add td:last-child) / uangmukabeli.blade.php supaya seragam. ---------- */
  #tabel td:first-child:not([colspan]),
  #tabel2 td:first-child:not([colspan]) {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #tabel td:first-child .btn,
  #tabel2 td:first-child .btn,
  #editPembelianTable td:last-child .btn {
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
  #tabel2 td:first-child .btn:hover,
  #editPembelianTable td:last-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success,  #tabel2 td:first-child .btn-success,  #editPembelianTable td:last-child .btn-success  { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel td:first-child .btn-warning,  #tabel2 td:first-child .btn-warning,  #editPembelianTable td:last-child .btn-warning  { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel td:first-child .btn-primary,  #tabel2 td:first-child .btn-primary,  #editPembelianTable td:last-child .btn-primary  { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel td:first-child .btn-danger,   #tabel2 td:first-child .btn-danger,   #editPembelianTable td:last-child .btn-danger   { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel td:first-child .btn-info,     #tabel2 td:first-child .btn-info,     #editPembelianTable td:last-child .btn-info     { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  #editPembelianTable td:last-child {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  /* Tombol di kolom Action baru muncul saat barisnya di-hover - opt-in lewat kelas
     po-aksi-hover supaya tabel lain (mis. modal) tidak ikut terpengaruh. visibility
     (bukan display) supaya lebar kolomnya tetap dipesan. Sengaja TIDAK memakai :focus-within: klik mouse membuat tombol tetap fokus sehingga tidak ikut hilang saat kursor sudah pindah. */
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
     po-table-header.css, ditulis di sini supaya perubahan cukup mengunggah file
     blade-nya saja. */
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

  /* ---------- Form Add/Detail/Edit (modal) - kartu ber-section, menggantikan grid
     col-md-6 lama. ---------- */
  .form-card {
    background: var(--rt-card, #fff);
    border: 1px solid var(--rt-border, #e7e9ee);
    border-radius: 10px;
    padding: 16px 18px;
    margin-bottom: 14px;
  }

  .form-card-title {
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #6b7280;
    margin: 0 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eef0f3;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px 16px;
  }

  .form-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
  }

  .form-field .input-group {
    flex-wrap: nowrap;
  }

  /* ---------- Tombol chip (latar tint muda + teks berwarna) untuk tombol Submit dan
     Batal - disalin dari purchaseOrder.blade.php / uangmukabeli.blade.php. ---------- */
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

  /* Tombol close (x) di kanan atas semua modal - border kotak abu biar target klik jelas. */
  .modal-header {
    align-items: center;
  }

  .modal-header .close {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 0 0 auto;
    border: 1px solid #d5d9e0;
    border-radius: 6px;
    background: #f8f9fb;
    color: #6b7280;
    width: 30px;
    height: 30px;
    padding: 0;
    line-height: 1;
    opacity: 1;
    transition: background-color .12s, border-color .12s;
  }

  .modal-header .close:hover {
    background: #eceef1;
    border-color: #adb5bd;
  }

  /* Tabel di dalam modal (Pilih Barang, dst) - header bersih, baris diklik langsung. */
  #formAddListItem tbody tr.pick-row {
    cursor: pointer;
    transition: background-color .12s;
  }

  #formAddListItem tbody tr.pick-row:hover td {
    background-color: #eef2ff;
  }

  #formAddListItem #input_search_barang_all {
    width: 260px;
    max-width: 100%;
    font-size: 13px;
    padding: 7px 10px 7px 32px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    outline: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: 10px center;
  }

  #formAddListItem #input_search_barang_all:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #e8edff;
  }

  #formAddListItem thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee !important;
    border-top: none !important;
  }

  #formAddListItem tbody td {
    border-top: none !important;
    border-bottom: 1px solid #f1f3f5 !important;
    font-size: 13px;
    vertical-align: middle;
  }

  /* DataTables (autoWidth) menulis lebar hasil ukurnya sebagai inline style pada <table>;
     kalau lebih sempit dari kotaknya, `table.dataTable { margin: 0 auto }` memusatkan tabel
     sehingga muncul ruang kosong kiri-kanan. Dipakai min-width (bukan width) supaya tabel
     yang memang lebih lebar tetap bisa digeser mendatar di dalam kotaknya. Sengaja di-scope
     lewat ID, bukan class - DataTables meng-clone tabel tanpa id saat mengukur kolom. */
  #tabel, #tabel2 {
    min-width: 100%;
  }

  
/* ---------- Perataan kotak centang untuk semua tabel di halaman ini ----------
   Dikumpulkan di satu tempat supaya tabel utama maupun tabel di dalam modal
   berperilaku sama. Dua hal yang dibereskan:
   1. .form-check bawaan Bootstrap memberi padding-left pada wadahnya dan
      float:left pada inputnya, jadi text-center saja tidak membuat kotaknya
      benar-benar di tengah kolom.
   2. vertical-align:middle mengacu ke titik tengah x-height teks, sedangkan isi
      kolom sebelahnya huruf kapital/angka. Tanpa koreksi -2px kotak centangnya
      terlihat turun sedikit dibanding teks di sebelahnya. */
td .form-check {
  padding-left: 0;
  margin: 0;
  min-height: 0;
}
td input[type="checkbox"] {
  float: none;
  margin: 0;
  vertical-align: middle;
  position: relative;
  top: -2px;
  /* Ukuran diseragamkan lewat scale, bukan width/height, supaya bentuk kotaknya
     tetap bawaan browser. width/height dikembalikan ke auto supaya ukuran khusus
     dari class lain (mis. .pba-chk-lg di invoicepembelian) tidak membuat sebagian
     kotak jadi lebih besar dari yang lain. */
  width: auto;
  height: auto;
  transform: scale(1.5);
}
</style>
{{-- end tampilan tampilan baru --}}
@endsection

@section('content')

<div id="printContainer" style="display:none">

</div>

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>


<div id="tempPrintContainer" style="display:none">

</div>
<div id="page1" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />


  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
           aria-controls="nav-home" aria-selected="true">
          Outstanding PO
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
           aria-controls="nav-profile" aria-selected="false">
          Penerimaan Barang
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
                    <input type="date" class="po-filter-inp" id="npoTglAwal1" value="{!! $npoTglAwal !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="npoTglAkhir1" value="{!! $npoTglAkhir !!}">
                  </div>
                  <input type="search" id="npoSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="npoLen1">Tampilkan</label>
                    <select id="npoLen1" class="po-len-inp">
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
                     (npoPindahBar) saat tab berganti - lihat npoInitReportTableSekali(). --}}
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
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" class="po-filter-inp" id="npoTglAwal2" value="{!! $npoTglAwal !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="npoTglAkhir2" value="{!! $npoTglAkhir !!}">
                  </div>
                  <input type="search" id="npoSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="npoLen2">Tampilkan</label>
                    <select id="npoLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
                {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat npoPindahBar(). --}}
                <table id="tabel2" class="data-table po-aksi-hover">
                  <thead id="tabel2_header" class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
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



<!-- <div> -->



<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
        <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" />

        <div class="form-card">
          <div class="form-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="form-field">
              <label for="input_add_nobukti">No Bukti</label>
              <input type="text" class="form-control bg-light" id="input_add_nobukti" disabled>
            </div>
            <div class="form-field">
              <label for="input_add_tanggal">Tanggal</label>
              <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
            </div>
            <div class="form-field">
              <label for="input_add_nomorpo">Nomor PO</label>
              <input type="text" class="form-control bg-light" id="input_add_nomorpo" disabled>
            </div>
            <div class="form-field">
              <label for="input_add_gudang">Gudang</label>
              <select id="input_add_gudang" class="form-control">
                @foreach ($gudang as $g)
                  <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} {{ $g->NAMA }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-field">
              <label for="input_add_suratjalansupp">Surat Jln Supp</label>
              <input type="text" autocomplete="off" class="form-control" id="input_add_suratjalansupp" required>
            </div>
            <div class="form-field">
              <label for="input_add_nokend">No. Kend / Sopir</label>
              <input type="text" autocomplete="off" class="form-control" id="input_add_nokend" required>
            </div>
          </div>
        </div>

        <div class="container-fluid p-0" style="overflow-x: auto;">
              <table id="addTable" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Qty LPB</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Terima</th>
                    <th scope="col">No. PO Cust</th>
                    <th scope="col">Customer</th>
                  </tr>
                </thead>
                <tbody id="addTableData" class="text-left" >
                  <tr>
                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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
      </div>{{-- /#formBsGrid --}}

      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- End modal add-->

<!-- start modal detail -->
  <div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="formBsGrid">
        <div class="modal-body">
          <div class="form-card">
            <div class="form-grid">
              <div class="form-field">
                <label>Tanggal</label>
                <input type="date" class="form-control" id="detailDate" value="{!! date('Y-m-d') !!}" disabled>
              </div>
              <div class="form-field">
                <label>Supplier</label>
                <input type="text" class="form-control" id="detailSupp" placeholder="" disabled>
              </div>
              <div class="form-field">
                <label>Kode Supp</label>
                <input type="text" class="form-control" id="detailKodeSupp" placeholder="" disabled>
              </div>
            </div>
          </div>
          <div class="container-fluid p-0" style="overflow-x: auto;">
                <table id="detailTable" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Nama Barang</th>
                      <th scope="col">Qty PO</th>
                      <th scope="col">Qty LPB</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>
                      <th scope="col">No. PO Cust</th>
                      <th scope="col">Customer</th>
                    </tr>
                  </thead>
                  <tbody id="detailTableData" class="text-left" >
                    <tr >
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
        </div>{{-- /#formBsGrid --}}
    </div>
  </div>
  </div>
<!-- End modal detail pembelian-->



<!-- start modal detail -->
  <div class="modal fade" id="detailPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailPembelianModalLabel">Detail kolom2</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="formBsGrid">
        <div class="modal-body">
          <div class="form-card">
            <div class="form-grid">
              <div class="form-field">
                <label>No PO</label>
                <input type="text" class="form-control" id="detailPembelianNoPO" disabled>
              </div>
              <div class="form-field">
                <label>Supplier</label>
                <input type="text" class="form-control" id="detailPembelianSupp" disabled>
              </div>
              <div class="form-field">
                <label>Tanggal</label>
                <input type="date" class="form-control" id="detailPembelianDate" value="{!! date('Y-m-d') !!}" disabled>
              </div>
              <div class="form-field">
                <label>Surat Jln Supp</label>
                <input type="text" autocomplete="off" class="form-control" id="detailPembelianFakturSupp" disabled>
              </div>
              <div class="form-field">
                <label>No. Kend / Sopir</label>
                <input type="text" autocomplete="off" class="form-control" id="detailPembelianKeterangan" disabled>
              </div>
            </div>
          </div>
          <div class="container-fluid p-0" style="overflow: auto">
                <table id="detailPembelianTable" class="data-table">
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Barang</th>
                      <th scope="col">Nama Barang</th>
                      <th scope="col">Qty LPB</th>
                      <th scope="col">Qty PO</th>
                      <th scope="col">Satuan</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">No. PO Customer</th>
                      <th scope="col">Customer</th>
                    </tr>
                  </thead>


                  <tbody id="detailPembelianTableData" class="text-left" >
                    <tr >
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
        </div>{{-- /#formBsGrid --}}
    </div>
  </div>
  </div>
<!-- End modal detail-->

<!-- start modal editpembelian  -->
<div class="modal fade" id="editPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <!-- tes max width -->
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPembelianModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <div class="form-card">
          <div class="form-grid">
            <div class="form-field">
              <label for="editPembelianNoPO">No PO</label>
              <input type="text" class="form-control" id="editPembelianNoPO" disabled>
            </div>
            <div class="form-field">
              <label for="editPembelianSupp">Supplier</label>
              <input type="text" class="form-control" id="editPembelianSupp" disabled>
            </div>
            <div class="form-field">
              <label for="editPembelianDate">Tanggal</label>
              <input type="date" class="form-control" id="editPembelianDate" value="{!! date('Y-m-d') !!}" disabled>
            </div>
            <div class="form-field">
              <label for="editPembelianFakturSupp">Surat Jln Supp</label>
              <input type="text" autocomplete="off" class="form-control" id="editPembelianFakturSupp" disabled>
            </div>
            <div class="form-field">
              <label for="editPembelianKeterangan">No. Kend / Sopir</label>
              <input type="text" autocomplete="off" class="form-control" id="editPembelianKeterangan" disabled>
            </div>
          </div>
        </div>
        <div class="mt-3 text-right">
          <button type="button" class="btn btn-sm btn-chip-biru" onclick="showPembelianAdd()">Add Item</button>
          <!-- <button type="button" class="btn btn-primary" onclick="resetDetailPembelian('SML/LPB/00197/0323','SML/PO/00302/0223')" class="btn btn-secondary"  >reset</button> -->
          <button type="button" class="btn btn-sm btn-chip-biru" onclick="saveKetFaktur()">Save Faktur & Ket</button>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}

      <div class="container-fluid" style="overflow:auto;">
      <div id="formBsGrid">

              <table id="editPembelianTable" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty LPB</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">No. PO Customer</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="editPembelianTableData" class="text-left" >
                  <tr >
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
        </div>{{-- /#formBsGrid --}}
        </div>

        <div id="formPembelianAdd" class="container-fluid showhide mt-3">
        <div id="formBsGrid">
          <div class="row mb-3">
            <div class="col-12">
              <h4>Add Item</h4>
            </div>
          </div>

          <div class="container-fluid">
            <!-- Row 1 -->
            <div class="row mb-3">
              <!-- Pilih Barang -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianAddSelect" style="margin-top:10px;" class="col-sm-4 fw-bold">Pilih Barang</label>
                  <div class="col-sm-8">
                    {{-- <select onchange="changeSelectBarang()" id="editPembelianAddSelect" class="form-control form-select"></select> --}}
                    <div class="input-group">
                      <input id="editPembelianAddSelect" type="text" class="form-control text-center" placeholder="Kode Barang" onkeypress="onKeyPressBarang(event)" disabled>
                      <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary btn-sm rounded-end shadow-sm" style="height:32px;">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Kode Barang -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddKode" style="margin-top:10px;" class="col-sm-4 fw-bold">Kode Barang</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputAddKode" type="text" class="form-control" disabled>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 2 -->
            <div class="row mb-3">
              <!-- Nama Barang -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddNamaBarang" style="margin-top:10px;" class="col-sm-4 fw-bold">Nama Barang</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputAddNamaBarang" type="text" class="form-control" disabled>
                  </div>
                </div>
              </div>

              <!-- Qty -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddQty" style="margin-top:10px;" class="col-sm-4 fw-bold">Qty</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputAddQty" type="text" inputmode="decimal" value="0.00" class="form-control" oninput="formatRibuanLive(this)" onblur="formatRibuan(this)">
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 3 -->
            <div class="row mb-3">
              <!-- Satuan -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddSatuan" style="margin-top:10px;" class="col-sm-4 fw-bold">Satuan</label>
                  <div class="col-sm-8">
                    <input type="text" id="editPembelianInputAddSatuan" value="PCS" class="form-control" disabled>
                  </div>
                </div>
              </div>

              <!-- Qty OS -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddQtyOS" style="margin-top:10px;" class="col-sm-4 fw-bold">Qty OS</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputAddQtyOS" type="text" value="0.00" class="form-control" disabled>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 4 -->
            <div class="row mb-3">
              <!-- Qty PO -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddQtyPO" style="margin-top:10px;" class="col-sm-4 fw-bold">Qty PO</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputAddQtyPO" type="text" value="0.00" class="form-control" disabled>
                  </div>
                </div>
              </div>
            </div>

              <!-- Tombol -->
              <div class="mt-4 text-right">
                <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitPembelianAdd()">Add Item</button>
                <button type="button" class="btn btn-sm btn-batal-add me-2" onclick="buttonBatalShowHide()">Batal</button>
              </div>

            </div>
          </div>{{-- /#formBsGrid --}}
          </div>

        <div id="formPembelianEdit" class="container-fluid showhide mt-3">
        <div id="formBsGrid">
          <div class="row mb-3">
            <div class="col-12">
              <h4>Edit Item</h4>
            </div>
          </div>

          <div class="container-fluid">
            <!-- Row 1 -->
            <div class="row mb-3">
              <!-- Kode Barang -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddKode" style="margin-top:10px;" class="col-sm-4 fw-bold">Kode Barang</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputEditKode" type="text" class="form-control" disabled>
                  </div>
                </div>
              </div>
              <!-- Nama Barang -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddNamaBarang" style="margin-top:10px;" class="col-sm-4 fw-bold">Nama Barang</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputEditNamaBarang" type="text" class="form-control" disabled>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 2 -->
            <div class="row mb-3">
              <!-- Qty -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddQty" style="margin-top:10px;" class="col-sm-4 fw-bold">Qty</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputEditQty" type="text" inputmode="decimal" value="0.00" class="form-control" oninput="formatRibuanLive(this)" onblur="formatRibuan(this)">
                  </div>
                </div>
              </div>
              <!-- Satuan -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddSatuan" style="margin-top:10px;" class="col-sm-4 fw-bold">Satuan</label>
                  <div class="col-sm-8">
                    <input type="text" id="editPembelianInputEditSatuan" value="PCS" class="form-control" disabled>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 3 -->
            <div class="row mb-3">
              <!-- Qty OS -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddQtyOS" style="margin-top:10px;" class="col-sm-4 fw-bold">Qty OS</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputEditQtyOS" type="text" value="0.00" class="form-control" disabled>
                  </div>
                </div>
              </div>
              <!-- Qty PO -->
              <div class="col-md-6 mb-2">
                <div class="row align-items-center">
                  <label for="editPembelianInputAddQtyPO" style="margin-top:10px;" class="col-sm-4 fw-bold">Qty PO</label>
                  <div class="col-sm-8">
                    <input id="editPembelianInputEditQtyPO" type="text" value="0.00" class="form-control" disabled>
                  </div>
                </div>
              </div>
              </div>
              <!-- Tombol -->
                <div class="mt-4 text-right">
                  <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitPembelianEdit()">Edit Item</button>
                  <button type="button" class="btn btn-sm btn-batal-add" onclick="buttonBatalShowHide()">Batal</button>
                </div>
              </div>
            </div>{{-- /#formBsGrid --}}
            </div>
          </div>
        </div>


          </div>
          <!-- <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="">Submit</button>
          </div>

          <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
          </div> -->
          <!-- <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="saveKetFaktur()">Save1</button>
          </div> -->
        </div>
    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->

<!-- start modal editpembelian  -->
<div class="modal fade" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <!-- tes max width -->
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="printPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-card">
          <div class="form-grid">
            <div class="form-field">
              <label>No PO</label>
              <input type="text" class="form-control" id="printPembelianNoPO" disabled>
            </div>
            <div class="form-field">
              <label>Supplier</label>
              <input type="text" class="form-control" id="printPembelianSupp" disabled>
            </div>
            <div class="form-field">
              <label>Tanggal</label>
              <input type="date" class="form-control" id="printPembelianDate" value="{!! date('Y-m-d') !!}" disabled>
            </div>
            <div class="form-field">
              <label>Surat Jln Supp</label>
              <input type="text" class="form-control" id="printPembelianKeterangan" autocomplete="off" disabled>
            </div>
            <div class="form-field">
              <label>No. Kend / Sopir</label>
              <input type="text" class="form-control" id="printPembelianFakturSupp" autocomplete="off" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid p-0" style="overflow:auto;">

              <table id="printPembelianTable" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th scope="col">v</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                  </tr>
                </thead>


                <tbody id="printPembelianTableData" class="text-right" >
                  <tr >
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                </tr>

                </tbody>


              </table>
        </div>





        <div class="container-fluid">
          <div class="row">
            <div class="col-12 text-right">
              <label>Jumlah Print</label>
              <input type="number" style="max-width: 50px" value=1 class="text-right" id="input_print_jumlahprint" >
            </div>
            <!-- <div class="col-2">


            </div> -->
            <!-- <div class="col-1">
                <input type="number" value=1 class="form-control text-right" id="input_print_jumlahprint" >
            </div> -->

          </div>

        </div>
          </div>

          <!-- <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="">Submit</button>
          </div>

          <div class="modal-footer showhide">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
          </div> -->
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitPrint()">Print</button>
          </div>
        </div>
    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->

<!-- start modal editpembelian  -->
<div class="modal fade" id="tes1234" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tes1234Label">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <!-- <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" /> -->
        <h1>tes</h1>

    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->

<!-- start modal list item add -->
  {{-- Disamakan persis dengan modal pemilih menu Kas (picker-kas): dropdown "Tampilkan" +
       kotak Search bawaan DataTables (kotak cari lama #input_search_barang_all + Enter dilepas -
       getDetailPO di server tidak memakai parameter search, jadi daftarnya sudah lengkap),
       tombol Batal gaya pil di footer. --}}
  <div class="modal fade picker-kas"  id="formAddListItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style=""  role="document" >
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pilih Barang</h5>
          <button type="button" class="close" onclick="closeFormList()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid mt-4">
            <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px; ">

              <table id="tabel_add_list_item">
                <thead>
                  <tr>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                  </tr>
                </thead>

                <tbody id="tabel_data_add_list_item" class="text-left" >
                </tbody>

              </table>
            </div>
            </div>
          </div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" onclick="closeFormList()">Batal</button>
      </div>
    </div>
  </div>
  </div>
  </div>
<!-- End modal list item add-->
@endsection

@section('js')
{{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + tombol
     "Reset kolom"), disamakan dengan purchaseOrder.blade.php / uangmukabeli.blade.php. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
  <script type="text/javascript">

// Input Qty diketik dengan separator ribuan (koma) - pola sama dengan newpobeliacc.blade.php.
// Saat nilainya dibaca untuk validasi/simpan, koma dibuang dulu dengan .replace(/,/g, '').
function formatRibuan (el) {
  let raw = String(el.value).replace(/,/g, '')
  if (raw === '' || isNaN(Number(raw))) return
  let parts = Number(raw).toFixed(2).split('.')
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  el.value = parts.join('.')
}

function formatRibuanLive (el) {
  let cursorPos = el.selectionStart
  let digitsBeforeCursor = el.value.slice(0, cursorPos).replace(/[^0-9]/g, '').length

  let raw = el.value.replace(/,/g, '')
  if (raw !== '' && isNaN(Number(raw))) {
    raw = raw.replace(/[^0-9.]/g, '')
  }
  if (raw === '') { el.value = ''; return }

  let parts = raw.split('.')
  if (parts.length > 1) parts[1] = parts[1].slice(0, 2)
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  el.value = parts.length > 1 ? parts[0] + '.' + parts[1] : parts[0]

  let pos = 0, digitCount = 0
  while (pos < el.value.length && digitCount < digitsBeforeCursor) {
    if (/[0-9]/.test(el.value[pos])) digitCount++
    pos++
  }
  el.setSelectionRange(pos, pos)
}

// format angka ribuan (separator koma, 2 desimal) - dipakai untuk tampilan Qty
function formatAngka (angkaString) {
  if (!Number(angkaString)) {
    return '0.00';
  }

  angkaString = parseFloat(angkaString).toFixed(2);

  let tempAngka = angkaString.split('.');

  if (tempAngka[0][0] == '-') {
    let temp2 = '';
    let tempAngka1 = tempAngka[0].split('-');
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2;
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2;
    }
    temp2 += '.' + tempAngka[1];
    return '-' + temp2;
  }

  let temp1 = '';
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1;
    }
    temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1;
  }
  temp1 += '.' + tempAngka[1];
  return temp1;
}

    let row_id = "";
    let action = "";
    let row_data = {};


    // untuk edit pembelian
    let dataEditPembelianAddIndex = ""
    let dataEditPembelianAdd = [];
    let dataEditPembelianEdit = [];
    let indexEditPembelianEdit = 0;
    let dataLPB = {};
    //end untuk pembelian

    // loadall
    let dataRefreshPO = []
    let dataRefreshPembelian = []

    let dataPrint = []

    function detailPembelianAwal(noBukti) {

    $.ajax({
        url: "{{ url('getDetailPembelian') }}",
        type: "POST",
        data: {
            NoBukti: noBukti,
            _token: "{{ csrf_token() }}"
        },
        success: function(res) {
            detailPembelian('x', res);
        }
    });

}

    function detailPembelian1(index) {
      let tempDataDetailPembelian = dataRefreshPembelian[index]
      detailPembelian('x', tempDataDetailPembelian)
    }

    function detailPembelian(detail_pembelian_row_id, detail_pembelian_row_data) {

      console.log("ID :", detail_pembelian_row_id);
    console.log("DATA :", detail_pembelian_row_data);

      if (!detail_pembelian_row_data || !detail_pembelian_row_data.length) {
        alertify.warning('Detail penerimaan tidak ditemukan');
        return
      }

      let date = new Date(detail_pembelian_row_data[0].TANGGAL);
      // let date = new Date(detail_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      let table_pembelian_row_detail = ""

      detail_pembelian_row_data.forEach((detail_pembelian_row, i) => {
        let satuan = ""
        if (detail_pembelian_row.Satuan) {
          satuan = detail_pembelian_row.Satuan
        }
        table_pembelian_row_detail += `<tr><td>${detail_pembelian_row.KodeBrg}</td><td>${detail_pembelian_row.namabrgx}</td><td class="text-right">${formatAngka(detail_pembelian_row.QntTerima)}</td><td class="text-right">${formatAngka(detail_pembelian_row.QNTPO)}</td><td>${satuan}</td><td class="text-right">${formatAngka(detail_pembelian_row.QNTOUT)}</td><td>-</td><td>-</td></tr>`
      });
      let fakturSupp = ""
      let keterangan = ""
      if (detail_pembelian_row_data[0].FAKTURSUPP) {
        fakturSupp = detail_pembelian_row_data[0].FAKTURSUPP
      }
      if (detail_pembelian_row_data[0].KETERANGAN) {
        keterangan = detail_pembelian_row_data[0].KETERANGAN
      }
      document.getElementById("detailPembelianModalLabel").innerHTML = detail_pembelian_row_data[0].NoBukti;
      document.getElementById("detailPembelianTableData").innerHTML = table_pembelian_row_detail
      document.getElementById("detailPembelianSupp").value = detail_pembelian_row_data[0].NamaSupplier
      document.getElementById("detailPembelianFakturSupp").value = fakturSupp
      document.getElementById("detailPembelianKeterangan").value =  keterangan
      document.getElementById("detailPembelianNoPO").value = detail_pembelian_row_data[0].NoPO
      // document.getElementById("detailPembelianDate").innerHTML = "Tanggal : " +  date.getFullYear() + "/" + (date.getMonth()+1) + "/" + date.getDate()
      $("#detailPembelianDate").val(date1);
      $("#detailPembelian").modal('toggle');

    }

    function tesModal() {
      //
      // $("#tes1234").modal('toggle');
      $('#formPembelianAdd').show();
    }

    function showPembelianAdd () {
      let akses = $("#akses_istambah").val();

      if (!Number(akses)) {
        alertify.warning('No access');
        return;
      }

      $('.showhide').hide();

      dataEditPembelianAddIndex = null;

      // reset input
      $("#editPembelianAddSelect").val("");
      $("#editPembelianInputAddKode").val("");
      $("#editPembelianInputAddNamaBarang").val("");
      $("#editPembelianInputAddQtyPO").val("0.00");
      $("#editPembelianInputAddSatuan").val("");
      $("#editPembelianInputAddQty").val("0.00");

      let select = $("#editPembelianAddSelect");
      select.html(""); 

      select.append(`<option value="">-- Pilih Barang --</option>`);

      if (Array.isArray(dataEditPembelianAdd) && dataEditPembelianAdd.length > 0) {
        $.each(dataEditPembelianAdd, function (i, d) {
          select.append(`
            <option value="${i}">
              ${d.KodeBrg} - ${d.namaBrg}
            </option>
          `);
        });
      }

      $('#formPembelianAdd').show();
    }

    function showPembelianEdit (indexBarang) {
      let akses = $("#akses_iskoreksi").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log(indexBarang,'zxc')
      $('.showhide').hide();
      indexEditPembelianEdit = indexBarang
      console.log(dataEditPembelianEdit[indexBarang])
      console.log(dataEditPembelianEdit[indexBarang].IsOtorisasi1)
      if (Number(dataEditPembelianEdit[indexBarang].IsOtorisasi1)) {
        alertify.warning("data sudah di otorisasi");
        return
      }
      document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
      document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].namabrgx
      document.getElementById("editPembelianInputEditQtyPO").value = formatAngka(dataEditPembelianEdit[indexBarang].QNTPO)
      document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
      document.getElementById("editPembelianInputEditQty").value = formatAngka(dataEditPembelianEdit[indexBarang].Qnt)
      $('#formPembelianEdit').show();
      document.getElementById('formPembelianEdit').scrollIntoView();
    }

    function buttonBatalShowHide() {
      $('.showhide').hide();
    }

    function loadAll1 () {
      console.log('tes123')
      $.ajax({
        url: "{!! url('tesnewpassword') !!}",
        type: "get",
        async: false,
        data: {password: 'asdtes'},
        success: function(res) {
          console.log(res)
          // console.log(res , "RESPOND PO");
          // console.log(res[0])

        }
      })
    }

    /* ============ Header tabel interaktif (window.ReportTable) ============
     * Sama pola dengan purchaseOrder.blade.php: DUA tabel (urut 1 = Outstanding PO,
     * urut 2 = Penerimaan Barang), masing-masing punya konfigurasi kolomnya sendiri
     * tersimpan di DBHEADERTABLE lewat endpoint saveheadertable/getheadertable yang
     * sudah ada (href = 'newpo'). npoCart[urut] menyimpan array itu, window.gcart_header
     * selalu diarahkan ke cart tabel yang sedang aktif. */
    const NPO_HREF = 'newpo'
    let npoCart = { 1: [], 2: [] }
    let npoActiveUrut = 1
    let npoPanjangHalaman = { 1: 10, 2: 10 }
    let npoRtSudahInit = false
    const NPO_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
    // Tabel di tab yang TIDAK aktif tidak digambar saat loadAll() - cukup ditandai di sini,
    // lalu digambar sungguhan saat tabnya dibuka (lihat handler shown.bs.tab). Sama pola
    // dengan poPerluGambar di purchaseOrder.blade.php.
    let npoPerluGambar = { 1: false, 2: false }

    // Field dari vwOUtPOWMS/fnc_masterbeli tidak ramah dibaca (NoBukti, NAMACUSTSUPP, dst) -
    // label yang tampil ke user dipasang di sini, bukan lewat DBHEADERTABLEALIAS (kosong
    // untuk href ini).
    const NPO_LABEL_1 = { NoBukti: 'No. PO', TANGGAL: 'Tanggal', NAMACUSTSUPP: 'Nama Supplier', NAMAGDG: 'Gudang', NAMAEXP: 'Ekspedisi' }
    const NPO_LABEL_2 = { NoBukti: 'No. Bukti', TANGGAL: 'Tanggal', NAMACUSTSUPP: 'Nama Supplier', NoPO: 'No. PO', NAMAGUDANG: 'Gudang', FAKTURSUPP: 'FakturSupp' }

    window.g_href = NPO_HREF
    window.g_modeReport = 1
    window.gcart_header = []

    // Tanggal dari SQL Server ditampilkan Y/m/d, sama seperti tampilan lama.
    function npoFormatTanggal (v) {
      if (!v) { return '' }
      let d = new Date(v)
      if (isNaN(d.getTime())) { return v }
      let day = ('0' + d.getDate()).slice(-2)
      let month = ('0' + (d.getMonth() + 1)).slice(-2)
      return d.getFullYear() + '/' + month + '/' + day
    }

    function npoBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered, labelMap) {
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

    // Kolom yang tampil. WAJIB hasil filter() dari cart, bukan map/salinan - lihat
    // catatan yang sama di uangmukabeli.blade.php / purchaseOrder.blade.php.
    function npoKolomTampil (urut) {
      return (npoCart[urut] || []).filter(c => Number(c[2]) === 1)
    }

    function npoKolomRender (c) {
      return { field: c[0], label: c[1], tipe: Number(c[8]), desimal: Number(c[5]) }
    }

    function npoRenderNilai (col, item) {
      let nilai = item ? item[col.field] : undefined
      if (col.tipe === 2) {
        return nilai ? npoFormatTanggal(nilai) : ''
      }
      return (nilai === null || nilai === undefined) ? '' : nilai
    }

    // Kalau public/js/report-table.js belum ikut terunggah, halaman tetap tampil dengan
    // <th> biasa, hanya tanpa drag & roda gigi.
    function npoHeadHtml (cols) {
      if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
        return ReportTable.headHtml(cols)
      }
      // report-table.js tidak termuat - header turun ke versi polos: tidak bisa digeser
      // maupun disembunyikan. Diberi peringatan supaya tidak gagal secara senyap.
      console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
      let html = '<tr>'
      cols.forEach((c) => { html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>` });
      return html + '</tr>'
    }

    function npoUrutTabAktif () {
      return $('#nav-profile-tab').hasClass('active') ? 2 : 1
    }

    function npoAktifkanTabel (urut) {
      npoActiveUrut = urut
      window.g_modeReport = urut
      window.gcart_header = npoCart[urut]
    }

    function npoOnChangeAktif () {
      if (npoActiveUrut === 2) {
        renderTabelPembelian()
      } else {
        renderTabelOut()
      }
    }

    // Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman - sama
    // alasannya dengan purchaseOrder.blade.php / uangmukabeli.blade.php.
    function npoInitReportTableSekali () {
      if (npoRtSudahInit || typeof ReportTable === 'undefined') { return }
      npoRtSudahInit = true

      let urutAktif = npoUrutTabAktif()
      let idTabel = { 1: '#tabel', 2: '#tabel2' }
      Object.keys(idTabel).forEach((u) => {
        if (Number(u) === urutAktif) { return }
        ReportTable.init({ table: idTabel[u], onChange: npoOnChangeAktif })
      });

      ReportTable.init({
        table: NPO_SELEKTOR_TABEL_AKTIF,
        bar: '#rtBar',
        onChange: npoOnChangeAktif
      })

      // DataTables memasang handler sort langsung di tiap <th>, sedangkan roda gigi/drag
      // milik report-table.js didelegasikan di <thead> - tanpa penanganan khusus, klik roda
      // gigi juga memicu sort DataTables. Sama solusinya dengan purchaseOrder.blade.php /
      // uangmukabeli.blade.php: hentikan event aslinya di fase capture, tembakkan ulang satu
      // event click baru langsung ke <thead>.
      let npoGuardUlangKlik = false
      let idThead = ['tabel_header', 'tabel2_header']
      idThead.forEach((id) => {
        let thead = document.getElementById(id)
        if (!thead) { return }
        thead.addEventListener('click', function (e) {
          if (npoGuardUlangKlik) { return }
          let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
          if (!interaktif) { return }

          e.stopPropagation()
          e.preventDefault()

          npoGuardUlangKlik = true
          let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
          Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
          thead.dispatchEvent(ulang)
          npoGuardUlangKlik = false
        }, true)
      });
    }

    // Pindahkan elemen #rtBar supaya duduk tepat sebelum tabel yang sedang aktif - sama
    // catatan/bug-fix dengan poPindahBar()/umbPindahBar().
    function npoPindahBar (urut) {
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

    // Kotak scroll tabel dibuat setinggi sisa ruang di #content supaya halaman TIDAK perlu
    // scrollbar sendiri - yang discroll hanya isi tabel. Diukur dari DOM, bukan angka mati
    // seperti 65vh, karena tinggi bagian di atas/bawah kotak (kartu tab, toolbar, #rtBar,
    // pagination, catatan kaki) berbeda antar tab dan bisa berubah. Sama pola dengan
    // poAturTinggiTabel() di purchaseOrder.blade.php.
    function npoAturTinggiTabel () {
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
    // dataset.rtBound karena renderTabelOut()/renderTabelPembelian() destroy+init tiap
    // kali kolom digeser/disembunyikan.
    function npoIkatSearch (urut) {
      let id = urut === 2 ? 'npoSearch2' : 'npoSearch1'
      let tabelId = urut === 2 ? '#tabel2' : '#tabel'
      let input = document.getElementById(id)
      if (!input || input.dataset.rtBound) { return }
      input.dataset.rtBound = '1'
      input.addEventListener('input', function () {
        $(tabelId).DataTable().search(input.value).draw()
      })
    }

    function npoIkatPanjangHalaman (urut) {
      let id = urut === 2 ? 'npoLen2' : 'npoLen1'
      let tabelId = urut === 2 ? '#tabel2' : '#tabel'
      let sel = document.getElementById(id)
      if (!sel || sel.dataset.rtBound) { return }
      sel.dataset.rtBound = '1'
      sel.value = String(npoPanjangHalaman[urut])
      sel.addEventListener('change', function () {
        let n = Number(sel.value)
        npoPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
        $(tabelId).DataTable().page.len(npoPanjangHalaman[urut]).draw()
      })
    }

    // Ubah salah satu tanggal periode -> muat ulang HANYA tab yang bersangkutan, supaya
    // ganti tanggal di satu tab tidak ikut memuat ulang tab yang lain.
    function npoIkatPeriode (urut) {
      let awal  = document.getElementById('npoTglAwal' + urut)
      let akhir = document.getElementById('npoTglAkhir' + urut)
      if (!awal || !akhir || awal.dataset.rtBound) { return }
      awal.dataset.rtBound = '1'

      let onUbah = function () {
        if (!awal.value || !akhir.value) { return }
        if (awal.value > akhir.value) {
          alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
          return
        }
        if (urut === 2) { npoMuatPembelian() } else { npoMuatOut() }
      }

      awal.addEventListener('change', onUbah)
      akhir.addEventListener('change', onUbah)
    }

    /* ---- Jembatan ke mesin penyimpan milik report-table.js ----
     * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
     * didefinisikan - report-table.js sudah punya fallback yang memutasi gcart_header
     * sendiri lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke
     * doSimpanHeader di bawah. */
    function npoUrutSah (mode) {
      return Number(mode) === 2 ? 2 : 1
    }

    window.doSimpanHeader = function (href, mode) {
      let urut = npoUrutSah(mode)
      let cart = npoCart[urut] || []

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
          href: NPO_HREF,
          urut: urut
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal menyimpan pengaturan kolom')
        }
      })
    }

    // Dipakai tombol "Reset kolom" di bar. Harus async:false karena report-table.js
    // langsung menggambar ulang setelahnya.
    window.doSetHeader = function (mode, reset) {
      if (!reset) { return }
      let urut = npoUrutSah(mode)

      $.ajax({
        url: "{!! url('getheadertable') !!}",
        type: "post",
        async: false,
        data: {
          _token: $("#_token").val(),
          href: NPO_HREF,
          urut: urut,
          reset: 1
        },
        success: function (res) {
          if (urut === 2) {
            npoCart[2] = npoBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, NPO_LABEL_2)
          } else {
            npoCart[1] = npoBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, NPO_LABEL_1)
          }
          window.gcart_header = npoCart[urut]
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
        }
      })
    }

    // Konfigurasi kolom (urut 1 & urut 2 sekaligus, satu panggilan) - lihat cabang 'newpo'
    // di HeaderTableController::getHeaderTable().
    function npoMuatKonfigurasi () {
      $.ajax({
        url: "{!! url('getheadertable') !!}",
        type: "post",
        async: false,
        data: { _token: $("#_token").val(), href: NPO_HREF },
        success: function (res) {
          npoCart[1] = npoBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, NPO_LABEL_1)
          npoCart[2] = npoBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, NPO_LABEL_2)
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal memuat konfigurasi kolom')
        }
      })
    }

    // Tab "Outstanding PO". Baris digambar dari dataRefreshPO (diurutkan terbaru dulu oleh
    // loadAll()) menurut kolom yang sedang tampil (npoKolomTampil) - susunan kolom hasil
    // geser/sembunyi selalu konsisten dengan hasil render ulang.
    function renderTabelOut () {
      npoAktifkanTabel(1)

      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().destroy()
      }

      let cols = npoKolomTampil(1)
      let kolomRender = cols.map(npoKolomRender)

      let thead = document.getElementById('tabel_header')
      thead.innerHTML = npoHeadHtml(cols)
      let baris = thead.querySelector('tr')
      if (baris) {
        baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
      }

      let rowTable = ''
      dataRefreshPO.forEach((item, i) => {
        let header = (item && item[0]) ? item[0] : {}
        let tombolAksi = `
          <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail1(${i})"><i class="bi bi-info-lg"></i></button>
          <button class="btn btn-primary btn-sm" type="button" title="Terima" onclick="buttonAdd1(${i})"><i class="bi bi-plus"></i></button>
        `
        rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
        kolomRender.forEach((c) => {
          rowTable += `<td>${npoRenderNilai(c, header)}</td>`
        });
        rowTable += `</tr>`
      });

      // Baris "Tidak ada data" TIDAK ditulis manual di sini - baris manual hanya berisi 1 sel
      // sedangkan header punya banyak kolom, dan DataTables mencoba mengindeks sel-sel yang
      // tidak ada di situ lalu crash (_DT_CellIndex). Biarkan <tbody> kosong dan serahkan ke
      // opsi language.emptyTable di bawah - DataTables sendiri yang menggambar baris kosongnya
      // dengan colspan yang benar.
      document.getElementById('tabel_data').innerHTML = rowTable

      $('#tabel').DataTable({
        lengthChange: false,
        pageLength: npoPanjangHalaman[1],
        // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom
        // Actions). Data sudah diurutkan terbaru dulu oleh loadAll().
        order: [],
        dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
          emptyTable: 'Tidak ada data',
          zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        },
        drawCallback: function () {
          // ditunda 0ms supaya DOM pagination sudah terpasang saat diukur
          setTimeout(npoAturTinggiTabel, 0)
        }
      })

      npoPindahBar(1)
      npoIkatSearch(1)
      npoIkatPanjangHalaman(1)
      npoIkatPeriode(1)
      let inputSearch = document.getElementById('npoSearch1')
      if (inputSearch && inputSearch.value) {
        $('#tabel').DataTable().search(inputSearch.value).draw()
      }
      npoAturTinggiTabel()
    }

    // Tab "Penerimaan Barang". dataRefreshPembelian sudah baris datar (bukan dikelompokkan
    // per NoBukti seperti dataRefreshPO), jadi kolomnya langsung dibaca dari item itu sendiri.
    function renderTabelPembelian () {
      npoAktifkanTabel(2)

      if ($.fn.DataTable.isDataTable('#tabel2')) {
        $('#tabel2').DataTable().destroy()
      }

      let cols = npoKolomTampil(2)
      let kolomRender = cols.map(npoKolomRender)

      let thead = document.getElementById('tabel2_header')
      thead.innerHTML = npoHeadHtml(cols)
      let baris = thead.querySelector('tr')
      if (baris) {
        baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
      }

      let rowTable = ''
      dataRefreshPembelian.forEach((item) => {
        let noBukti = item.NoBukti || ''
        let tombolAksi = `
          <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="detailPembelianAwal('${noBukti}')"><i class="bi bi-info-lg"></i></button>
          <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEditPembelian1('${noBukti}')"><i class="bi bi-pen"></i></button>
          <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${noBukti}')"><i class="bi bi-printer"></i></button>
        `
        rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
        kolomRender.forEach((c) => {
          rowTable += `<td>${npoRenderNilai(c, item)}</td>`
        });
        rowTable += `</tr>`
      });

      // Baris "Tidak ada data" TIDAK ditulis manual - lihat catatan yang sama di
      // renderTabelOut(). Diserahkan ke language.emptyTable di bawah.
      document.getElementById('tabel2_data').innerHTML = rowTable

      $('#tabel2').DataTable({
        lengthChange: false,
        pageLength: npoPanjangHalaman[2],
        order: [],
        dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
          emptyTable: 'Tidak ada data',
          zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        },
        drawCallback: function () {
          // ditunda 0ms supaya DOM pagination sudah terpasang saat diukur
          setTimeout(npoAturTinggiTabel, 0)
        }
      })

      npoPindahBar(2)
      npoIkatSearch(2)
      npoIkatPanjangHalaman(2)
      npoIkatPeriode(2)
      let inputSearch = document.getElementById('npoSearch2')
      if (inputSearch && inputSearch.value) {
        $('#tabel2').DataTable().search(inputSearch.value).draw()
      }
      npoAturTinggiTabel()
    }

    // Ambil data tab "Outstanding PO" (tglawal/tglakhir dari input tab 1) dan gambar
    // ulang tab itu saja - dipakai baik oleh loadAll() (autoRender = false, penggambaran
    // diatur di sana) maupun oleh npoIkatPeriode(1) (autoRender default = true).
    function npoMuatOut (autoRender) {
      $.ajax({
        url: "{!! url('getAllPO') !!}",
        type: "get",
        async: false,
        data: {
          tglawal: $('#npoTglAwal1').val(),
          tglakhir: $('#npoTglAkhir1').val()
        },
        success: function(res) {
          console.log(res , "RESPOND PO");
          // Terbaru dulu: urutkan sekali di sini supaya buttonDetail1(i)/buttonAdd1(i) -
          // yang mengambil dataRefreshPO[i] - tetap mengacu ke PO yang benar.
          dataRefreshPO = (res || []).slice().sort(function (a, b) {
            let da = (a && a[0] && a[0].TANGGAL) ? new Date(a[0].TANGGAL) : 0
            let db = (b && b[0] && b[0].TANGGAL) ? new Date(b[0].TANGGAL) : 0
            if (db - da !== 0) { return db - da }
            let na = (a && a[0] && a[0].NoBukti) || ''
            let nb = (b && b[0] && b[0].NoBukti) || ''
            return nb.localeCompare(na)
          })
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal memuat data Outstanding PO')
        }
      })
      if (autoRender !== false) { renderTabelOut() }
    }

    // Ambil data tab "Penerimaan Barang" (tglawal/tglakhir dari input tab 2) dan gambar
    // ulang tab itu saja - sama seperti npoMuatOut().
    function npoMuatPembelian (autoRender) {
      $.ajax({
        url: "{!! url('getAllPembelian') !!}",
        type: "get",
        async: false,
        data: {
          tglawal: $('#npoTglAwal2').val(),
          tglakhir: $('#npoTglAkhir2').val()
        },
        success: function(res) {
          console.log(res , "RESPOND PEM");
          dataRefreshPembelian = (res || []).slice().sort(function (a, b) {
            let da = a.TANGGAL ? new Date(a.TANGGAL) : 0
            let db = b.TANGGAL ? new Date(b.TANGGAL) : 0
            if (db - da !== 0) { return db - da }
            return String(b.NoBukti || '').localeCompare(String(a.NoBukti || ''))
          })
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal memuat data Penerimaan Barang')
        }
      })
      if (autoRender !== false) { renderTabelPembelian() }
    }

    function loadAll () {
      // Idempotent - hanya benar-benar mengikat sekali seumur halaman.
      npoInitReportTableSekali()

      // Hanya tabel di tab yang SEDANG AKTIF yang digambar. Kalau keduanya digambar di sini,
      // render terakhir (tab 2) meninggalkan #rtBar + window.gcart_header + npoActiveUrut
      // menunjuk ke tab 2 padahal yang tampil tab 1 - itu yang bikin tombol Reset kolom
      // hilang dan geser/sembunyikan kolom tidak berefek sampai user pindah tab.
      let urutAktif = npoUrutTabAktif()
      npoPerluGambar[1] = (urutAktif !== 1)
      npoPerluGambar[2] = (urutAktif !== 2)

      npoMuatOut(false)
      npoMuatPembelian(false)

      if (urutAktif === 2) {
        renderTabelPembelian()
      } else {
        renderTabelOut()
      }
    }

    $('#nav-home-tab').on('shown.bs.tab', function () {
      npoAktifkanTabel(1)
      npoPindahBar(1)

      if (npoPerluGambar[1]) {
        npoPerluGambar[1] = false
        renderTabelOut()
        return
      }

      if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
      npoAturTinggiTabel()
    })

    $('#nav-profile-tab').on('shown.bs.tab', function () {
      npoAktifkanTabel(2)
      npoPindahBar(2)

      if (npoPerluGambar[2]) {
        npoPerluGambar[2] = false
        renderTabelPembelian()
        return
      }

      if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
      npoAturTinggiTabel()
    })

    // Layar diubah ukurannya (mis. resize jendela) - tinggi kotak tabel diukur ulang supaya
    // tetap pas, didebounce supaya tidak menghitung ulang di setiap event resize.
    let npoTimerResize = null
    $(window).on('resize', function () {
      if (npoTimerResize) { clearTimeout(npoTimerResize) }
      npoTimerResize = setTimeout(npoAturTinggiTabel, 150)
    })

    $(document).ready(function () {
      npoMuatKonfigurasi()
      loadAll()
    })

    function resetDetailPembelian (noBukti, noPO) {
      // SML/LPB/00197/0323
      // SML/PO/00302/0223
      let _token = $("#_token").val();
      console.log(noBukti, noPO)
      $('.showhide').hide();

      $.ajax({
        url: "{!! url('detailPembelian') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: noBukti
        },
        success: function(res) {
          console.log("RES =", res);
          console.log("isArray(res) =", Array.isArray(res));
          console.log("isArray(res[0]) =", Array.isArray(res[0]));

          console.log('=================1111===============!!!!!!')
          console.log(dataLPB, "breset")
          console.log(dataEditPembelianEdit)
          // console.log(res)
          // console.log(res[1])
          // console.log(res[0] ,'i0')

          console.log('=================22222===============!!!!!!')
          dataEditPembelianEdit = res;
          dataLPB = res[0];
          console.log(dataLPB,'areset')
          console.log(dataEditPembelianEdit)
          console.log('=================33333===============!!!!!!')
        }
      })

      // if (!dataEditPembelianEdit) {
      //   console.log('=================44444===============!!!!!!')
      //   // window.location.href = "newpo";
      //   // editPembelian
      //   $("#editPembelian").modal('toggle');

      if (!dataEditPembelianEdit || dataEditPembelianEdit.length === 0) {
        $("#editPembelian").modal('toggle');
        return;

      } else {
        let date = new Date(dataLPB.TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let table_row_edit_pembelian = ""
        dataEditPembelianEdit.forEach((r, i) => {
          console.log(r)
          if (r.Satuan) {
            satuan = r.Satuan
          }
          table_row_edit_pembelian += `
          <tr>
            <td>${r.KodeBrg}</td>
            <td>${r.namabrgx}</td>
            <td class="text-right">${formatAngka(r.Qnt)}</td>
            <td class="text-right">${formatAngka(r.QNTPO)}</td>
            <td>${satuan}</td>
            <td class="text-right">${formatAngka(r.QNTOUT)}</td>
            <td>-</td>
            <td>-</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})">
                <i class="bi bi-pen"></i>
              </button>
              <button class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>`
          });
        document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  dataLPB.NoBukti;
        document.getElementById("editPembelianNoPO").value = dataLPB.NoPO
        document.getElementById("editPembelianSupp").value = dataLPB.NamaSupplier
        document.getElementById("editPembelianFakturSupp").value = dataLPB.FAKTURSUPP
        document.getElementById("editPembelianKeterangan").value = dataLPB.KETERANGAN
        document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

        $.ajax({
          url: "{!! url('detailPO') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoPO:  noPO,
            NoBukti: noBukti,
          },
          success: function(res) {
            console.log('======detailPO success====')
            console.log(res,'1')
            dataEditPembelianAdd = res
            console.log(dataEditPembelianAdd,'2')
          }
        })
        editPembelianSelect = ""
        console.log(dataEditPembelianAdd,'3')
        if (dataEditPembelianAdd.length) {
          editPembelianSelect += `<option value="" selected disabled>-- Pilih Barang --</option>`
          dataEditPembelianAdd.forEach((data, i) => {
            editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namabrgx}</option>`
          });
        } else {
          editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
        }

        document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect

        $('#editPembelianDate').val(date1)
      }

    }

    // function resetDetailPembelian (noBukti, noPO) {

    //     let _token = $("#_token").val();
    //     console.log(noBukti, noPO);

    //     $('.showhide').hide();

    //     $.ajax({
    //       url: "{!! url('detailPembelian') !!}",
    //       type: "post",
    //       async: false,
    //       data: {
    //         _token : _token,
    //         NoBukti: noBukti
    //       },
    //       success: function(res) {

    //         dataEditPembelianEdit = res[0] || [];
    //         dataLPB = (res[0] && res[0][0]) ? res[0][0] : null;

    //         console.log("LPB:", dataLPB);
    //         console.log("DETAIL PEMBELIAN:", dataEditPembelianEdit);
    //       }
    //     });

    //     if (!dataLPB) {
    //       $("#editPembelian").modal('toggle');
    //       return;
    //     }

    //     let date = new Date(dataLPB.TANGGAL);
    //     let day   = ("0" + date.getDate()).slice(-2);
    //     let month = ("0" + (date.getMonth() + 1)).slice(-2);
    //     let date1 = date.getFullYear()+"-"+month+"-"+day;

    //     let table_row_edit_pembelian = "";
    //     dataEditPembelianEdit.forEach((r, i) => {

    //       let satuan = r.Satuan || "";

    //       table_row_edit_pembelian += `
    //         <tr>
    //           <td>${r.KodeBrg}</td>
    //           <td>${r.Namabrgx}</td>
    //           <td class="text-right">${r.Qnt}</td>
    //           <td class="text-right">${r.QNTPO}</td>
    //           <td>${satuan}</td>
    //           <td class="text-right">${r.QNTOUT}</td>
    //           <td>-</td>
    //           <td>-</td>
    //           <td class="text-center">
    //             <button class="btn btn-success btn-sm" type="button"
    //               onclick="showPembelianEdit(${i})">
    //               <i class="bi bi-pen"></i>
    //             </button>
    //             <button class="btn btn-danger btn-sm" type="button"
    //               onclick="submitPembelianDelete(${i})">
    //               <i class="bi bi-trash"></i>
    //             </button>
    //           </td>
    //         </tr>`;
    //     });

    //     document.getElementById("editPembelianModalLabel").innerHTML =
    //       "Edit " + dataLPB.NoBukti;

    //     document.getElementById("editPembelianNoPO").value = dataLPB.NoPO;
    //     document.getElementById("editPembelianSupp").value = dataLPB.NamaSupplier;
    //     document.getElementById("editPembelianFakturSupp").value = dataLPB.FAKTURSUPP;
    //     document.getElementById("editPembelianKeterangan").value = dataLPB.KETERANGAN;
    //     document.getElementById("editPembelianTableData").innerHTML =
    //       table_row_edit_pembelian;

    //     $('#editPembelianDate').val(date1);

    //     $.ajax({
    //       url: "{!! url('detailPO') !!}",
    //       type: "post",
    //       async: false,
    //       data: {
    //         _token : _token,
    //         NoPO:  noPO,
    //         NoBukti: noBukti
    //       },
    //       success: function(res) {

    //         console.log("detailPO success:", res);

    //         dataEditPembelianAdd = res || [];

    //         let select = document.getElementById("editPembelianAddSelect");
    //         select.innerHTML = "";

    //         if (!dataEditPembelianAdd.length) {
    //           select.innerHTML =
    //             `<option value="">-- Tidak ada barang outstanding --</option>`;
    //           return;
    //         }

    //         select.innerHTML =
    //           `<option value="">-- Pilih Barang --</option>`;

    //         dataEditPembelianAdd.forEach((d, i) => {
    //           select.innerHTML +=
    //             `<option value="${i}">
    //               ${d.KodeBrg} - ${d.namabrgx}
    //             </option>`;
    //         });

    //         console.log("OUTSTANDING:", dataEditPembelianAdd);
    //       }
    //     });
    //   }

    
function submitPrint (nobukti) {

  let _token = $('#_token').val()
  

    $.ajax({
      url: "{!! url('newpoCetak') !!}",
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
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">LAPORAN PENERIMAAN GUDANG</h2>
                    </div>
                    <div style="display: flex; width: 100%; font-size: 11px;">
                      <div class="pb-1" style="width: 20%">No. SPP</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOBUKTI}</div>
                    </div>
                    <div style="display: flex; width: 100%; font-size: 11px;">
                      <div class="pb-1" style="width: 20%">No. PO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoPO}</div>
                    </div>
                    <div style="display: flex; width: 100%; font-size: 11px;">
                      <div class="pb-1" style="width: 20%">NO. PR / SO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOSO || ''}</div>
                    </div>
                    <div style="display: flex; width: 100%; font-size: 11px;">
                      <div class="pb-1" style="width: 20%">Gudang</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].KodeGdg}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td colspan=6>Telah Diterima Barang sebagai berikut :</td>
                  </tr>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 5%">Kode Barang</td>
                    <td class="text-center" style="width: 30%">Nama Barang</td>
                    <td class="text-center" style="width: 5%">Sat</td>
                    <td class="text-center" style="width: 5%">PO</td>
                    <td class="text-center" style="width: 5%">Terima</td>
                    <td class="text-center" style="width: 5%">Sisa</td>
                    <td class="text-center" style="width: 5%">Keterangan</td>
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
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 2%;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.KODEBRG}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 30%;">${itemSub.NAMABRG}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: center;">${itemSub.SATUAN}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: right;">${itemSub.QNTPO ? formatAngka(itemSub.QNTPO) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: right;">${itemSub.QntTerima ? formatAngka(itemSub.QntTerima) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; width: 5%; text-align: right;">${itemSub.SISA ? parseFloat(itemSub.SISA).toFixed(2) : ''}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.KETERANGAN}</td>
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

  <div style="width: 40%">
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 100%">${dataPrint[0].NAMACUSTSUPP}</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 20%">${dataPrint[0].NOPOLL || ''}</div>
      </div>
  </div>

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Diterima oleh,</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui oleh,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Nama</p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid black; font-size:12px;">Tgl</p>
        </td>
      </tr>
    </table>
  </div>

</div>

    <div class="footer-print-date" style='margin-bottom:-100px;'>
            <table class="m-0" style="width: 100% ; font-family: sans-serif;
            font-size: 10px ">
              <tr>
                <td class="no-border" white-space:pre;>${i+1}/${arrayDataPrint.length}            `+tanggalOnly+`      `+jamCetak+`</td>
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


    function submitPembelianDelete (index) {
      let akses = $("#akses_ishapus").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }


      alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + '?',
          function() {
            console.log(dataEditPembelianEdit[index])
            // console.log('1')
            // return
            let _token = $("#_token").val();
            let choice = "D"
            let dataLPBDelete = dataEditPembelianEdit[index]
            let isOtorisasi = Number(dataLPBDelete.IsOtorisasi1);

            console.log(dataLPBDelete.IsOtorisasi1)
            if (isOtorisasi === 1) {
              alertify.warning("data sudah di otorisasi");
              return;
            }
            let reqNoBukti = dataLPBDelete.NoBukti
            let reqNoUrut = dataLPBDelete.NoUrut
            let reqTANGGAL = dataLPBDelete.TANGGAL
            let reqKodeSupp = dataLPBDelete.KODESUPP
            let reqKodeGudang = dataLPBDelete.KODEGDG
            let reqNoPO = dataLPBDelete.NoPO
            let reqKeterangan = dataLPBDelete.KETERANGAN
            let reqFakturSupp = dataLPBDelete.FAKTURSUPP
            //
            let reqUrut = dataLPBDelete.Urut
            let reqKodeBarang = dataLPBDelete.KodeBrg
            let reqUrutPO = dataLPBDelete.UrutPO
            let reqQtyTerima = dataLPBDelete.Qnt
            let reqNoSat = dataLPBDelete.NoSat
            let reqSatuan = dataLPBDelete.Satuan
            let reqIsi = dataLPBDelete.Isi
            let reqQtyTerima1 = 0
            let reqQtyTerima2 = 0
            if (dataLPBDelete.NoSat == 1) {
              reqQtyTerima1 = reqQtyTerima;
              reqQtyTerima2 = reqQtyTerima / dataLPBDelete.ISI2;
            } else if (dataLPBDelete.NoSat == 2) {
              reqQtyTerima1 = reqQtyTerima * dataLPBDelete.ISI2;
              reqQtyTerima2 = reqQtyTerima;
            }
            let reqNamaBarang = dataLPBDelete.namabrgx
            let reqNoBatch = ""
            let reqQtyReject = 0
            let reqQtyReject1 = 0
            let reqQtyReject2 = 0
            let reqPBeliJasa = 0
            let reqEd = null

            $.ajax({
              url: "{!! url('sp_beligudang') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                // data: data,
                // choice: choice,
                // qtyTerima: qtyTerima,
                // dataLPB: dataLPB
                choice,
                reqNoBukti,
                reqNoUrut,
                reqTANGGAL,
                reqKodeSupp,
                reqKodeGudang,
                reqNoPO,
                reqKeterangan,
                reqFakturSupp,
                reqUrut,
                reqKodeBarang,
                reqUrutPO,
                reqQtyTerima ,
                reqNoSat,
                reqSatuan,
                reqIsi,
                reqQtyTerima1,
                reqQtyTerima2,
                reqNamaBarang,
                reqNoBatch,
                reqQtyReject,
                reqQtyReject1,
                reqQtyReject2,
                reqPBeliJasa,
                reqEd,
              },
              success: function(res) {
                // console.log(res , "RESPOND");
                console.log(res)
                console.log("del successsssssssssssssssssssss")
                resetDetailPembelian(reqNoBukti,reqNoPO)
                loadAll()
                alertify.success('Item telah dihapus');
              }
            })

          }
        ,function(){
          console.log('no')
        });
    }

    function saveKetFaktur () {
      let akses = $("#akses_iskoreksi").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      let _token = $("#_token").val();
      let choice = "F"
      console.log(dataEditPembelianEdit[0])
      // console.log(dataLPBEdit)
      dataLPBEdit = dataEditPembelianEdit[0]
      // editPembelianFakturSupp
      // editPembelianKeterangan
      let reqNoBukti = dataLPBEdit.NoBukti
      let reqNoUrut = dataLPBEdit.NoUrut
      let reqTANGGAL = dataLPBEdit.TANGGAL
      let reqKodeSupp = dataLPBEdit.KODESUPP
      let reqKodeGudang = dataLPBEdit.KODEGDG
      let reqNoPO = dataLPBEdit.NoPO
      let reqKeterangan = $("#editPembelianKeterangan").val()
      let reqFakturSupp = $("#editPembelianFakturSupp").val()
      //
      let reqUrut = dataLPBEdit.Urut
      let reqKodeBarang = dataLPBEdit.KodeBrg
      let reqUrutPO = dataLPBEdit.UrutPO
      let reqQtyTerima = dataLPBEdit.Qnt
      let reqNoSat = dataLPBEdit.NoSat
      let reqSatuan = dataLPBEdit.Satuan
      let reqIsi = dataLPBEdit.Isi
      let reqQtyTerima1 = 0
      let reqQtyTerima2 = 0
      if (dataLPBEdit.NoSat == 1) {
        reqQtyTerima1 = reqQtyTerima;
        reqQtyTerima2 = reqQtyTerima / dataLPBEdit.ISI2;
      } else if (dataLPBEdit.NoSat == 2) {
        reqQtyTerima1 = reqQtyTerima * dataLPBEdit.ISI2;
        reqQtyTerima2 = reqQtyTerima;
      }
      let reqNamaBarang = dataLPBEdit.namabrgx
      let reqNoBatch = ""
      let reqQtyReject = 0
      let reqQtyReject1 = 0
      let reqQtyReject2 = 0
      let reqPBeliJasa = 0
      let reqEd = null

      $.ajax({
        url: "{!! url('sp_beligudang') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          // data: data,
          // choice: choice,
          // qtyTerima: qtyTerima,
          // dataLPB: dataLPB
          choice,
          reqNoBukti,
          reqNoUrut,
          reqTANGGAL,
          reqKodeSupp,
          reqKodeGudang,
          reqNoPO,
          reqKeterangan,
          reqFakturSupp,
          reqUrut,
          reqKodeBarang,
          reqUrutPO,
          reqQtyTerima ,
          reqNoSat,
          reqSatuan,
          reqIsi,
          reqQtyTerima1,
          reqQtyTerima2,
          reqNamaBarang,
          reqNoBatch,
          reqQtyReject,
          reqQtyReject1,
          reqQtyReject2,
          reqPBeliJasa,
          reqEd,
        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          console.log("successsssssssssssssssssssss")
          loadAll()
          resetDetailPembelian(reqNoBukti,reqNoPO)
          alertify.success('Faktur dan keterangan telah diupdate');
        }
      })

      console.log(reqFakturSupp,reqKeterangan)

      // console.log(choice,
      // reqNoBukti,
      // reqNoUrut,
      // reqTANGGAL,
      // reqKodeSupp,
      // reqKodeGudang,
      // reqNoPO,
      // reqKeterangan,
      // reqFakturSupp,
      // reqUrut,
      // reqKodeBarang,
      // reqUrutPO,
      // reqQtyTerima ,
      // reqNoSat,
      // reqSatuan,
      // reqIsi,
      // reqQtyTerima1,
      // reqQtyTerima2,
      // reqNamaBarang,
      // reqNoBatch,
      // reqQtyReject,
      // reqQtyReject1,
      // reqQtyReject2,
      // reqPBeliJasa,
      // reqEd)

      // document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
      // document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].NamaBrg
      // document.getElementById("editPembelianInputEditQtyPO").value = dataEditPembelianEdit[indexBarang].QNTPO
      // document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
      // document.getElementById("editPembelianInputEditQty").value = dataEditPembelianEdit[indexBarang].Qnt
    }

    function submitPembelianEdit () {
      console.log(indexEditPembelianEdit, dataEditPembelianEdit[indexEditPembelianEdit])

      let _token = $("#_token").val();
      let choice = "U"
      let dataLPBEdit = dataEditPembelianEdit[indexEditPembelianEdit]
      let reqQtyTerima = parseInt($("#editPembelianInputEditQty").val().replace(/,/g, ''), 10) || 0;

      // console.log(dataLPBEdit.QNTOUT, dataLPBEdit.Qnt , reqQtyTerima)
      // console.log(Number(reqQtyTerima) , Number(dataLPBEdit.QNTOUT) , Number(dataLPBEdit.Qnt))
      // console.log(Number(reqQtyTerima) , (Number(dataLPBEdit.QNTOUT) + Number(dataLPBEdit.Qnt)))
      if (Number(reqQtyTerima) > (Number(dataLPBEdit.QNTOUT) + Number(dataLPBEdit.Qnt))) {
        alertify.warning("Qty melebihi Qty OUT");
        console.log('error')
        return
      }
      if (Number(reqQtyTerima) < 0) {
        alertify.warning("Qty tidak boleh negatif");
        return
      }
      // console.log(dataLPBEdit.QNTOUT, dataLPBEdit.Qnt , reqQtyTerima)
      // return
      let reqNoBukti = dataLPBEdit.NoBukti
      let reqNoUrut = dataLPBEdit.NoUrut
      let reqTANGGAL = dataLPBEdit.TANGGAL
      let reqKodeSupp = dataLPBEdit.KODESUPP
      let reqKodeGudang = dataLPBEdit.KODEGDG
      let reqNoPO = dataLPBEdit.NoPO
      let reqKeterangan = dataLPBEdit.KETERANGAN
      let reqFakturSupp = dataLPBEdit.FAKTURSUPP
      //
      let reqUrut = dataLPBEdit.Urut
      let reqKodeBarang = dataLPBEdit.KodeBrg
      let reqUrutPO = dataLPBEdit.UrutPO

      let reqNoSat = dataLPBEdit.NoSat
      let reqSatuan = dataLPBEdit.Satuan
      let reqIsi = dataLPBEdit.Isi
      let reqQtyTerima1 = 0
      let reqQtyTerima2 = 0
      if (dataLPBEdit.NoSat == 1) {
        reqQtyTerima1 = reqQtyTerima;
        reqQtyTerima2 = reqQtyTerima / dataLPBEdit.ISI2;
      } else if (dataLPBEdit.NoSat == 2) {
        reqQtyTerima1 = reqQtyTerima * dataLPBEdit.ISI2;
        reqQtyTerima2 = reqQtyTerima;
      }

      reqQtyTerima1 = parseInt(reqQtyTerima1, 10) || 0;
      reqQtyTerima2 = parseInt(reqQtyTerima2, 10) || 0;

      let reqNamaBarang = dataLPBEdit.namabrgx
      let reqNoBatch = ""
      let reqQtyReject = 0
      let reqQtyReject1 = 0
      let reqQtyReject2 = 0
      let reqPBeliJasa = 0
      let reqEd = null

      $.ajax({
        url: "{!! url('sp_beligudang') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          // data: data,
          // choice: choice,
          // qtyTerima: qtyTerima,
          // dataLPB: dataLPB
          choice,
          reqNoBukti,
          reqNoUrut,
          reqTANGGAL,
          reqKodeSupp,
          reqKodeGudang,
          reqNoPO,
          reqKeterangan,
          reqFakturSupp,
          reqUrut,
          reqKodeBarang,
          reqUrutPO,
          reqQtyTerima ,
          reqNoSat,
          reqSatuan,
          reqIsi,
          reqQtyTerima1,
          reqQtyTerima2,
          reqNamaBarang,
          reqNoBatch,
          reqQtyReject,
          reqQtyReject1,
          reqQtyReject2,
          reqPBeliJasa,
          reqEd,
        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          console.log("successsssssssssssssssssssss")
          loadAll()
          resetDetailPembelian(reqNoBukti,reqNoPO)
          alertify.success('Item telah diedit');
        }
      })
    }

    function submitPembelianAdd() {

      if (dataEditPembelianAddIndex === null) {
        alertify.warning("Tidak ada item dipilih");
        return;
      }

      let _token = $("#_token").val();
      
      let data = dataEditPembelianAdd[dataEditPembelianAddIndex];
      console.log(data)
      if (!data) {
        alertify.warning("Data barang tidak ditemukan");
        return;
      }

      let reqQtyTerima = $("#editPembelianInputAddQty").val().replace(/,/g, '') || 0;

      if (Number(reqQtyTerima) > Number(data.OSPO)) {
        alertify.warning("Qty melebihi Qty OS");
        return;
      }

      if (Number(reqQtyTerima) < 0) {
        alertify.warning("Qty tidak boleh negatif");
        return;
      }

      let choice = "I";
      let reqNoBukti = dataLPB.NoBukti;
      let reqNoUrut = dataLPB.NoUrut;
      let reqTANGGAL = dataLPB.TANGGAL;
      let reqKodeSupp = dataLPB.KODESUPP;
      let reqKodeGudang = dataLPB.KODEGDG;
      let reqNoPO = dataLPB.NoPO;
      let reqKeterangan = dataLPB.KETERANGAN;
      let reqFakturSupp = dataLPB.FAKTURSUPP;
      let reqUrut = 0;
      let reqKodeBarang = data.KodeBrg;
      let reqUrutPO = data.Urut;

      let reqNoSat = data.NoSat;
      let reqSatuan = data.Satuan;
      let reqIsi = data.Isi;

      let reqQtyTerima1 = 0;
      let reqQtyTerima2 = 0;

      if (data.NoSat == 1) {
        reqQtyTerima1 = reqQtyTerima;
        reqQtyTerima2 = reqQtyTerima / data.ISI2;
      } else if (data.NoSat == 2) {
        reqQtyTerima1 = reqQtyTerima * data.ISI2;
        reqQtyTerima2 = reqQtyTerima;
      }

      let reqNamaBarang = data.namaBrg;
      let reqNoBatch = "";
      let reqQtyReject = 0;
      let reqQtyReject1 = 0;
      let reqQtyReject2 = 0;
      let reqPBeliJasa = 0;
      let reqEd = null;
      console.log('coba1')
      console.log(reqIsi, reqNoSat)
      $.ajax({
        url: "{!! url('sp_beligudang') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          choice,
          reqNoBukti,
          reqNoUrut,
          reqTANGGAL,
          reqKodeSupp,
          reqKodeGudang,
          reqNoPO,
          reqKeterangan,
          reqFakturSupp,
          reqUrut,
          reqKodeBarang,
          reqUrutPO,
          reqQtyTerima,
          reqNoSat,
          reqSatuan,
          reqIsi,
          reqQtyTerima1,
          reqQtyTerima2,
          reqNamaBarang,
          reqNoBatch,
          reqQtyReject,
          reqQtyReject1,
          reqQtyReject2,
          reqPBeliJasa,
          reqEd,
        },
        success: function(res) {
          loadAll();
          resetDetailPembelian(reqNoBukti, reqNoPO);
          alertify.success('Item telah diadd');
        }
      });
    }

    // function submitPembelianAdd() {
    //   if (dataEditPembelianAddIndex === "") {
    //     console.log("no item")
    //     alertify.warning("Tidak ada item dipilih");
    //   } else {

    //     let _token = $("#_token").val();


    //     // console.log(dataEditPembelianAddIndex)
    //     let data = dataEditPembelianAdd[dataEditPembelianAddIndex]
    //     console.log(data)
    //     let reqQtyTerima = $("#editPembelianInputAddQty").val()
    //     if (Number(reqQtyTerima) > Number(data.OSPO)) {
    //       alertify.warning("Qty melebihi Qty OS");
    //       console.log('error')
    //       return
    //     }
    //     if (Number(reqQtyTerima) < 0) {
    //       alertify.warning("Qty tidak boleh negatif");
    //       return
    //     }

    //     let choice = "I"
    //     let reqNoBukti = dataLPB.NoBukti
    //     let reqNoUrut = dataLPB.NoUrut
    //     let reqTANGGAL = dataLPB.TANGGAL
    //     let reqKodeSupp = dataLPB.KODESUPP
    //     let reqKodeGudang = dataLPB.KODEGDG
    //     let reqNoPO = dataLPB.NoPO
    //     let reqKeterangan = dataLPB.KETERANGAN
    //     let reqFakturSupp = dataLPB.FAKTURSUPP
    //     let reqUrut = 0
    //     let reqKodeBarang = data.KodeBrg
    //     let reqUrutPO = data.Urut

    //     if (Number(reqQtyTerima) < 0) {
    //       alertify.warning("Qty tidak boleh negatif");
    //       return
    //     }
    //     let reqNoSat = data.NOSAT
    //     let reqSatuan = data.Satuan
    //     let reqIsi = data.ISI
    //     let reqQtyTerima1 = 0
    //     let reqQtyTerima2 = 0
    //     if (data.NOSAT == 1) {
    //       reqQtyTerima1 = reqQtyTerima;
    //       reqQtyTerima2 = reqQtyTerima / data.ISI2;
    //     } else if (data.NOSAT == 2) {
    //       reqQtyTerima1 = reqQtyTerima * data.ISI2;
    //       reqQtyTerima2 = reqQtyTerima;
    //     }
    //     let reqNamaBarang = data.namabrgx
    //     let reqNoBatch = ""
    //     let reqQtyReject = 0
    //     let reqQtyReject1 = 0
    //     let reqQtyReject2 = 0
    //     let reqPBeliJasa = 0
    //     let reqEd = null


    //       // console.log(editPembelianInputAddQtyPO)
    //       $.ajax({
    //         url: "{!! url('sp_beligudang') !!}",
    //         type: "post",
    //         async: false,
    //         data: {
    //           _token : _token,
    //           // data: data,
    //           // choice: choice,
    //           // qtyTerima: qtyTerima,
    //           // dataLPB: dataLPB
    //           choice,
    //           reqNoBukti,
    //           reqNoUrut,
    //           reqTANGGAL,
    //           reqKodeSupp,
    //           reqKodeGudang,
    //           reqNoPO,
    //           reqKeterangan,
    //           reqFakturSupp,
    //           reqUrut,
    //           reqKodeBarang,
    //           reqUrutPO,
    //           reqQtyTerima ,
    //           reqNoSat,
    //           reqSatuan,
    //           reqIsi,
    //           reqQtyTerima1,
    //           reqQtyTerima2,
    //           reqNamaBarang,
    //           reqNoBatch,
    //           reqQtyReject,
    //           reqQtyReject1,
    //           reqQtyReject2,
    //           reqPBeliJasa,
    //           reqEd,
    //         },
    //         success: function(res) {
    //           // console.log(res , "RESPOND");
    //           console.log(res)
    //           console.log("successsssssssssssssssssssss")
    //           loadAll()
    //           resetDetailPembelian(reqNoBukti,reqNoPO)
    //           alertify.success('Item telah diadd');
    //         }
    //       })

    //   }

    // }

    function changeSelectBarang () {
      let indexBarang = $("#editPembelianAddSelect").val();
      if (indexBarang === "") return;

      dataEditPembelianAddIndex = indexBarang;

      let d = dataEditPembelianAdd[indexBarang];
      if (!d) return;

      $("#editPembelianInputAddKode").val(d.KodeBrg);
      $("#editPembelianInputAddNamaBarang").val(d.namaBrg);
      $("#editPembelianInputAddQtyOS").val(formatAngka(d.OSPO));
      $("#editPembelianInputAddQtyPO").val(formatAngka(d.QNT));
      $("#editPembelianInputAddSatuan").val(d.Satuan);
    }

    function buttonAddListBarang () {
      $("#input_search_barang_all").val('');

      // buka modal
      $('#editPembelian').modal('hide');
      $('#formAddListItem').modal('show');

      loadListBarang('');
    }

    function loadListBarang(search = '') {

      if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
        $('#tabel_add_list_item').DataTable().destroy();
      }

      $.ajax({
        url: "{!! url('detailPO') !!}",
        type: "post",
        data: { _token: $("#_token").val(),
        search,
        NoBukti: dataLPB.NoBukti,
        NoPO   : dataLPB.NoPO    
        },
        success: function(res) {
          console.log('test99')
          console.log(res)

          listBarang = res || [];

          let rowTable = "";
          listBarang.forEach((item, i) => {
            rowTable += `
              <tr class="pick-row" onclick="buttonAddAddInsertItem(${i})">
                <td>${item.KodeBrg}</td>
                <td>${item.namaBrg}</td>
              </tr>
            `;
          });

          $("#tabel_data_add_list_item").html(rowTable);

          pickerKasInit('tabel_add_list_item')
        }
      });
    }

    function closeFormList () {
      $('#formAddListItem').modal('toggle');
      $('#editPembelian').modal('toggle');

    }

    function closeListItemAdd () {

      $('#formAddListItem').modal('toggle');
      $('#editPembelian').modal('toggle');
    }

    function searchBarangAll (e) {
      if (e.which !== 13) return;

      let search = $("#input_search_barang_all").val().trim();
      loadListBarang(search);
    }

    function buttonAddAddInsertItem (index) {
      let brg = listBarang[index];
      if (!brg) return;

      let kode = brg.KodeBrg;

      let idx = dataEditPembelianAdd.findIndex(
        d => d.KodeBrg === kode
      );

      if (idx === -1) {
        alertify.warning("Barang tidak ada di PO");
        return;
      }

      dataEditPembelianAddIndex = idx;
      let d = dataEditPembelianAdd[idx];

      $("#editPembelianAddSelect").val(d.KodeBrg);
      $("#editPembelianInputAddKode").val(d.KodeBrg);
      $("#editPembelianInputAddNamaBarang").val(d.namaBrg);
      $("#editPembelianInputAddQtyOS").val(formatAngka(d.OSPO));
      $("#editPembelianInputAddQtyPO").val(formatAngka(d.QNT));
      $("#editPembelianInputAddSatuan").val(d.Satuan);

      closeListItemAdd();

      setTimeout(() => {
        $("#editPembelianInputAddQty").focus().select();
      }, 300);
    }

    function buttonEditPembelian1 (nobukti) {
      // tempDataEditPembelian = dataRefreshPembelian[index]
      // buttonEditPembelian('x', tempDataEditPembelian)
      buttonEditPembelian(nobukti)
    }


  function buttonEditPembelian (nobukti) {
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
          $('.showhide').hide();
          console.log("qwert")
          console.log(nobukti)

          let _token = $("#_token").val();

          let edit_pembelian_row_data = []

          $.ajax({
            url: "{!! url('detailPembelian') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoBukti: nobukti
            },
            success: function(res) {
              console.log(res)
              console.log(res)

              edit_pembelian_row_data = res
              console.log(res[0])
              console.log(res[0][0])
              // console.log(res[0][0].IsOtorisasi1)

            }
          })
            console.log(edit_pembelian_row_data)
          if(!edit_pembelian_row_data.length) {
            alertify.warning('silahkan refresh browser')
            return
          }

          dataLPB =  edit_pembelian_row_data[0]
          console.log(dataLPB)
           // detailPembelianDate
          // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
          dataEditPembelianEdit = edit_pembelian_row_data
          // return
          console.log(dataEditPembelianEdit)

          // console.log(edit_pembelian_row_id, edit_pembelian_row_data , "<<<< edit pembelian")
          let date = new Date(edit_pembelian_row_data[0].TANGGAL);
          var day = ("0" + date.getDate()).slice(-2);
          var month = ("0" + (date.getMonth() + 1)).slice(-2);
          var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

          if (Number(edit_pembelian_row_data[0].IsOtorisasi1)) {
            alertify.warning('Data Sudah di Otorisasi')
            return
          }

          let table_row_edit_pembelian = ""
          console.log("SSS")
          console.log(edit_pembelian_row_data)
          edit_pembelian_row_data.forEach((r, i) => {
            console.log('r===' ,r ,'ASD')
            console.log('SDW')
            console.log(r)
            console.log(i)
            // let QntPO_format = r.QntPO.toLocaleString();
            // console.log(r)
            // console.log(r.QntPO)
            // console.log(r.Satuan)
            let satuan = ""
            if (r.Satuan) {
              satuan = r.Satuan
            }
            table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td class="text-right">${formatAngka(r.Qnt)}</td><td class="text-right">${formatAngka(r.QNTPO)}</td><td>${satuan}</td><td class="text-right">${formatAngka(r.QNTOUT)}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
            });
          document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;
          document.getElementById("editPembelianNoPO").value = edit_pembelian_row_data[0].NoPO
          document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
          document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
          document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
          document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

          console.log(edit_pembelian_row_data[0].NoPO)
          console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")

          $.ajax({
            url: "{!! url('detailPO') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoPO: edit_pembelian_row_data[0].NoPO,
              NoBukti: edit_pembelian_row_data[0].NoBukti
            },
            success: function(res) {
              console.log(res)
              dataEditPembelianAdd = res
              console.log(dataEditPembelianAdd)
            }
          })

          editPembelianSelect = ""
          if (dataEditPembelianAdd.length) {
            editPembelianSelect += `<option value="" selected disabled>-- Pilih Barang --</option>`
            dataEditPembelianAdd.forEach((data, i) => {
              editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namaBrg}</option>`
            });
          } else {
            editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
          }

          // document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect

          console.log('date1', date1)
          $('#editPembelianDate').val(date1)
          $("#editPembelian").modal('toggle');
        }

    function buttonEditPembelianTemp (edit_pembelian_row_id, edit_pembelian_row_data) {
      $('.showhide').hide();
      console.log("qwert")
      dataLPB =  edit_pembelian_row_data[0]
       // detailPembelianDate
      // document.getElementById("editPembelianDate").value = edit_pembelian_row_data[0].TANGGAL
      dataEditPembelianEdit = edit_pembelian_row_data

      console.log(edit_pembelian_row_id, edit_pembelian_row_data , "<<<< edit pembelian")
      let date = new Date(edit_pembelian_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

      if (edit_pembelian_row_data[0].IsOtorisasi1) {
        alertify.warning('Data sudah di Otorisasi')
        return
      }

      let table_row_edit_pembelian = ""
      edit_pembelian_row_data.forEach((r, i) => {
        console.log('r===' ,r ,'ASD')
        console.log('SDW')
        // let QntPO_format = r.QntPO.toLocaleString();
        // console.log(r)
        // console.log(r.QntPO)
        // console.log(r.Satuan)
        let satuan = ""
        if (r.Satuan) {
          satuan = r.Satuan
        }
        table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td>${formatAngka(r.Qnt)}</td><td>${formatAngka(r.QNTPO)}</td><td>${satuan}</td><td>${formatAngka(r.QNTOUT)}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
        });
      document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;
      document.getElementById("editPembelianNoPO").value = edit_pembelian_row_data[0].NoPO
      document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
      // let fakturSupp = ""
      // let keterangan = ""
      // if (edit_pembelian_row_data[0].FAKTURSUPP) {
      //   fakturSupp = edit_pembelian_row_data[0].FAKTURSUPP
      // }
      // if (edit_pembelian_row_data[0].KETERANGAN) {
      //   keterangan = edit_pembelian_row_data[0].KETERANGAN
      // }
      // document.getElementById("editPembelianFakturSupp").value = fakturSupp
      // document.getElementById("editPembelianKeterangan").value = keterangan
      document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
      document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
      document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian

      console.log(edit_pembelian_row_data[0].NoPO)
      console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailPO') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoPO: edit_pembelian_row_data[0].NoPO,
          NoBukti: edit_pembelian_row_data[0].NoBukti
        },
        success: function(res) {
          // console.log(res , "RESPOND");
          console.log(res)
          dataEditPembelianAdd = res
          console.log(dataEditPembelianAdd)
        }
      })
      // console.log(dataEditPembelianAdd , "+")
      // editPembelianAddKode
      editPembelianSelect = ""
      if (dataEditPembelianAdd.length) {
        editPembelianSelect += `<option value="" selected disabled>-- Pilih Barang --</option>`
        dataEditPembelianAdd.forEach((data, i) => {
          editPembelianSelect += `<option value="${i}">${data.KodeBrg} - ${data.namaBrg}</option>`
        });
      } else {
        editPembelianSelect += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
      }

      document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect

      // $.ajax({
      //   url: "{!! url('deletenewmenu') !!}",
      //   type: "POST",
      //   async: false,
      //   data: {
      //     _token : _token,
      //     KODEMENU: kodemenu
      //   },
      //   success: function(res) {
      //     alertify.success('Menu telah dihapus.');
      //     window.location.href = "newmenu";
      //   }
      // })

      $('#editPembelianDate').val(date1)
      $("#editPembelian").modal('toggle');
    }
    function buttonDetail1(index) {
      let tempDataDetail = dataRefreshPO[index]
      buttonDetail('x' , tempDataDetail)
    }

    function buttonDetail(detail_row_id, detail_row_data ) {
      console.log(detail_row_id, detail_row_data ,  "<<< detail")
      console.log('================')
      // let date = new Date(detail_row_data[0].TANGGAL);
      let date = new Date(detail_row_data[0].TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

      let table_row_detail = ""
      detail_row_data.forEach((detail_row, i) => {
        // let QntPO_format = detail_row.QntPO.toLocaleString();
        // console.log(detail_row)
        // console.log(detail_row.QntPO)
        // console.log(detail_row.Satuan)
        table_row_detail += `<tr><td>${detail_row.namaBrg}</td><td class="text-right">${formatAngka(detail_row.QNT)}</td><td class="text-right">${formatAngka(detail_row.QntBeli)}</td><td class="text-right">${formatAngka(detail_row.OSPO)}</td><td>${detail_row.Satuan}</td><td>-</td><td>-</td></tr>`
      });

      document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;
      document.getElementById("detailSupp").value = detail_row_data[0].NAMACUSTSUPP;
      document.getElementById("detailKodeSupp").value = detail_row_data[0].KODESUPP;
      // document.getElementById("detailDate").value = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate()

      // let date1= date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate()
      console.log(date1)
      $('#detailDate').val(date1)
      document.getElementById("detailTableData").innerHTML = table_row_detail


      $("#detail").modal('toggle');
    }

    function buttonAdd1(index) {
      let tempDataAdd = dataRefreshPO[index]
      buttonAdd('x' , tempDataAdd)
      // console.log(tempDataAdd)

    }

    function setNewNoBukti () {
      $.ajax({
        url: "{!! url('getNoBukti') !!}",
        type: "get",
        async: false,
        success: function(res) {
          console.log(res , "RESPOND");
          console.log(res[0])
          document.getElementById("input_add_nobukti").value = res[0].Nobukti
          document.getElementById("input_add_noUrut").value = res[0].Nourut
        }
      })
    }

  function buttonAdd(add_row_id, add_row_data) {
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

    document.getElementById("input_add_suratjalansupp").value = '';
    document.getElementById("input_add_nokend").value = '';

    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access');
      return;
    }

    console.log('tes123');
    console.log(add_row_data);
    setNewNoBukti();

    console.log(add_row_id, add_row_data, "<<< add");
    console.log('================');
    row_data = add_row_data;
    let table_row_add = "";

    add_row_data.forEach((add_row, i) => {
      table_row_add += `
        <tr>
          <td class="text-center">
            <input id="add_checkbox${i}" type="checkbox">
          </td>
          <td>${add_row.namaBrg}</td>
          <td class="text-right">${formatAngka(add_row.QNT)}</td>
          <td class="text-right">${formatAngka(add_row.QntBeli)}</td>
          <td class="text-right">${formatAngka(add_row.OSPO)}</td>
          <td>${add_row.Satuan}</td>
          <td>
            <input id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="text" inputmode="decimal" value="0.00" oninput="formatRibuanLive(this)" onblur="formatRibuan(this)">
          </td>
          <td>-</td>
          <td>-</td>
        </tr>`;
    });

    document.getElementById("addTableData").innerHTML = table_row_add;

    document.getElementById("input_add_nomorpo").value = add_row_data[0].NoBukti;
    document.getElementById("exampleModalLabel").innerHTML = "Add " + add_row_data[0].NoBukti;
    document.getElementById("input_add_gudang").value = add_row_data[0].KODEGDG;

    add_row_data.forEach((add_row, i) => {
      const checkbox = document.getElementById(`add_checkbox${i}`);
      const inputQnt = document.getElementById(`input_add_qntTerima${i}`);

      checkbox.addEventListener('change', function () {
        if (this.checked) {
          inputQnt.value = formatAngka(add_row.OSPO);
        } else {
          inputQnt.value = '0.00';
        }
      });
    });

    $("#form").modal('toggle');
  }

    // function buttonAdd() {
    //   console.log(row_id)
    //
    //   if (row_id === "") {
    //     console.log('tes')
    //     alertify.warning("Tidak ada baris dipilih");
    //     return
    //   }
    //   console.log('masok')
    //
    //   let table_row_add = ""
      // row_data.forEach((add_row, i) => {
      //   table_row_add += `<tr><td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td><td>${add_row.namaBrg}</td><td>${add_row.QntPO}</td><td>${add_row.QntBeli}</td><td>${add_row.QNTOS}</td><td>${add_row.Satuan}</td><td>${add_row.OS}</td><td>-</td><td>-</td></tr>`
      // });
    //
      // document.getElementById("addTableData").innerHTML = table_row_add
      // document.getElementById("input_add_nomorpo").value = row_data[0].NoBukti;
      // document.getElementById("exampleModalLabel").innerHTML = "Add " +  row_data[0].NoBukti;
      //
      // $("#form").modal('toggle');
    // }

    function buttonEdit() {
      console.log(row_id)

      if (row_id === "") {
        console.log('tes')
        alertify.warning("Tidak ada baris dipilih");
        return
      }
      console.log('tes1')
      document.getElementById("exampleModalLabel").innerHTML = "Edit Menu";

      document.getElementById("input_kodemenu").value = row_data.KODEMENU;
      document.getElementById("input_access").value = row_data.ACCESS;
      document.getElementById("input_l0").value = row_data.L0;
      document.getElementById("input_keterangan").value = row_data.Keterangan;
      document.getElementById("input_href").value = row_data.href;
      action = "Edit";
      $("#form").modal('toggle');
      console.log('masok' , row_data);
    }

    function buttonDelete() {
      if (row_id === "") {
        console.log('tes')
        alertify.warning("Tidak ada baris dipilih");
        return
      }
      alertify.confirm('Hapus Menu', 'Apakah yakin ingin menghapus menu ' + row_data.KODEMENU + row_data.Keterangan + ' ?',
      function(){
        let _token = $("#_token").val();
        let kodemenu = row_data.KODEMENU;

        $.ajax({
          url: "{!! url('deletenewmenu') !!}",
          type: "POST",
          async: false,
          data: {
            _token : _token,
            KODEMENU: kodemenu
          },
          success: function(res) {
            alertify.success('Menu telah dihapus.');
            window.location.href = "newmenu";
          }
        })

    }
      ,function(){});
    }

    function submitAdd() {
      console.log('submitAdd123')
        let tempData = []
        // console.log('tes')
        for (let i = 0; i < row_data.length; i++) {
          // console.log('masok')
          // let tes = document.getElementById(`add_checkbox${i}`)
          // console.log(tes)
          // let isChecked = document.getElementById(`add_checkbox${i}`)
          // console.log(isChecked)
          // console.log(document.getElementById(`add_checkbox${i}`).checked)
          if (document.getElementById(`add_checkbox${i}`).checked) {
            // console.log(row_data[i])
          // let id = "#add_checkbox" + i
          // console.log(id)
          // let tes = $(`#add_checkbox${i}`).val();
          // console.log(tes)
          row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val().replace(/,/g, '');
          tempData.push(row_data[i])
        }
      }

      if(!tempData.length) {
        alertify.warning("Tidak ada item dipilih");
        return
      }




      console.log('else')
      let flag = false
      tempData.forEach((item, i) => {
        console.log(Number(item.inputQntTerima), Number(item.OSPO))
        if (Number(item.inputQntTerima) > Number(item.OSPO)){
          flag = true
        }
        if (Number(item.inputQntTerima) < 0 ) {
          flag = true
        }
      });
      if (flag) {
        alertify.warning("Qty Terima lebih besar dari Qty OS ataupun negatif");
        return
      }

      // let gudang = $("input_add_gudang").val();
      let gudang = document.getElementById("input_add_gudang").value;
      let suratJalan = $("#input_add_suratjalansupp").val();
      let noKend = $("#input_add_nokend").val();
      let noPO = $("#input_add_nomorpo").val();
      let noBukti = $("#input_add_nobukti").val();
      let _token = $("#_token").val();
      let noUrut = $("#input_add_noUrut").val();
      let inputTanggal = $("#input_add_tanggal").val();


      let checkDate = new Date(inputTanggal)

      let periode_bulan = document.getElementById("periode_bulan").value
      let periode_tahun = document.getElementById("periode_tahun").value

      if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

          alertify.warning("Tanggal tidak sesuai periode");
          return
      }


      console.log(tempData)
      // console.log(suratJalan , noKend , noPO, gudang , noUrut, noBukti, inputTanggal)
      // console.log(tempData , "=======")
      // ajax
      console.log('====== starting create =======')
      // return
      $.ajax({
                url : "{!! url('addDBBeli') !!}",
                type : "POST",
                async : false,
                data : {
                  _token : _token,
                  data : tempData,
                  suratJalan : suratJalan,
                  noKend : noKend,
                  noPO : noPO,
                  gudang: gudang,
                  noBukti: noBukti,
                  noUrut: noUrut,
                  inputTanggal: inputTanggal
                },
                success: function (res) {
                  console.log(res , "RESPOND");
                  if (res == 1) {

                    console.log('create success')
                    alertify.success('Pembelian telah ditambah');
                    // window.location.href = "newpo";
                    loadAll()
                    $("#form").modal('toggle');
                  }
                  if (res == 2) {
                    setNewNoBukti()
                    alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
                  }
                },
                error: function (err) {
                  console.log(err)
                  alertify.warning('Terjadi kesalahan silahkan refresh browser')
                }
              })

  }



    function submitForm() {
      if (action == "Add") {
        console.log("Add Menu Submit")
        console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )


        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let href = $("#input_href").val();

        console.log(_token, kodemenu, keterangan, l0, access)

        if (kodemenu && keterangan && access) {
          // alertify.alert('Gagal menambahkan menu jadi!', 'Semua kolom harus terisi.', function(){ });
          console.log('starting create')
          $.ajax({
            url : "{!! url('addnewmenu') !!}",
            type : "POST",
            async : false,
            data : {
              _token : _token,
              KODEMENU : kodemenu,
              Keterangan : keterangan,
              L0 : l0,
              ACCESS : access,
              href: href
            },
            success: function (res) {
              if (res == 1) {
                console.log('create success')

                window.location.href = "newmenu";
              } else {
                console.log('create fail')
              }
            }
          })
          $("#form").modal('toggle');
        } else {
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });

        }

      } else {
        console.log("Edit Menu Submit")
        console.log("Code lama :", row_data.KODEMENU)
        console.log(  $("#input_kodemenu").val() , $("#input_access").val() ,$("#input_l0").val() ,$("#input_keterangan").val()  )


        let _token = $("#_token").val();
        let kodemenu = $("#input_kodemenu").val();
        let keterangan = $("#input_keterangan").val();
        let l0 = $("#input_l0").val();
        let access = $("#input_access").val();
        let kodelama = row_data.KODEMENU
        let href = $("#input_href").val();


        if (kodemenu && keterangan && access) {
          // alertify.alert('Gagal menambahkan menu jadi!', 'Semua kolom harus terisi.', function(){ });
          console.log('starting edit')
          $.ajax({
            url : "{!! url('editnewmenu') !!}",
            type : "POST",
            async : false,
            data : {
              _token : _token,
              KODEMENU : kodemenu,
              Keterangan : keterangan,
              L0 : l0,
              ACCESS : access,
              kodelama: kodelama,
              href: href
            },
            success: function (res) {
              if (res == 1) {
                console.log('edit success')
                window.location.href = "newmenu";
              } else {
                console.log('edit fail')
              }
            }
          })
          $("#form").modal('toggle');
        } else {
          alertify.alert('Tes', 'Semua kolom harus terisi.', function(){ });

        }

      }
    }

    function tesclick(tesprint, tesprint1) {
      console.log('asd')
      row_id = tesprint;
      row_data = tesprint1;
      $('#tabel_data > tr').each(function() {
        $(this).css('background-color', '');
      });
      $("#tr"+tesprint).css('background-color', '#FFF59E');
      console.log(tesprint, tesprint1 ,row_id , action)
    }

  </script>

{{-- setActiveTab() (mengecat warna tab lewat inline style) DIMATIKAN - dibiarkan sebagai
     arsip di bawah, jangan dihidupkan lagi. Sama seperti purchaseOrder.blade.php: inline
     style selalu menang melawan stylesheet, jadi aturan .custom-tabs (warna tab aktif/
     tidak aktif) tidak pernah benar-benar berlaku selama skrip ini masih hidup. Warna tab
     aktif/tidak aktif sekarang sepenuhnya diserahkan ke class `active` yang dikelola
     Bootstrap Tab (bs.tab) + CSS .custom-tabs.

  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(homeActive) {
      if (homeActive) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';
      } else {
        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';
      }
    }

    // Default warna tab
    setActiveTab(true);

    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(true);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(false);
    });
  </script>
--}}

@endsection
