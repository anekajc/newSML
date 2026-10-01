@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Retur Pembelian Gudang')

@endsection

{{-- Tampilan baru: tab custom, tabel .data-table, header interaktif - disamakan dengan
     resources/views/purchasing/newpo.blade.php / uangmukabeli.blade.php. --}}
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
       "Reset kolom"), sama dengan newpo.blade.php / uangmukabeli.blade.php. --}}
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
  <style>
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

  /* ---------- Kolom Aksi tabel - tombol bulat kecil, warna pastel, disalin dari
     newpo.blade.php / uangmukabeli.blade.php supaya seragam. ---------- */
  #tabel td:first-child:not([colspan]),
  #tabelRetur td:first-child:not([colspan]) {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #tabel td:first-child .btn,
  #tabelRetur td:first-child .btn {
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
  #tabelRetur td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success,  #tabelRetur td:first-child .btn-success  { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel td:first-child .btn-warning,  #tabelRetur td:first-child .btn-warning  { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel td:first-child .btn-primary,  #tabelRetur td:first-child .btn-primary  { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel td:first-child .btn-danger,   #tabelRetur td:first-child .btn-danger   { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel td:first-child .btn-info,     #tabelRetur td:first-child .btn-info     { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

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
     blade-nya saja. Sama seperti newpo.blade.php / uangmukabeli.blade.php. */
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

  #tabel, #tabelRetur {
    min-width: 100%;
  }

  /* Tabel item pada modal Add/Detail/Koreksi - header abu-abu uppercase, zebra, tombol
     pastel - sama seperti #tabel_add di perintahreturbeli.blade.php. */
  #addTable td:first-child:not([colspan]),
  #detailTable td:first-child:not([colspan]),
  #koreksiDetailTable td:first-child:not([colspan]),
  #koreksiTable td:first-child:not([colspan]) {
    vertical-align: middle;
  }

  #koreksiTable td:first-child .btn {
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
    margin: 0 2px;
  }

  #koreksiTable td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #koreksiTable td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #koreksiTable td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

  #addTable thead th,
  #detailTable thead th,
  #koreksiDetailTable thead th,
  #koreksiTable thead th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
  }

  #addTable tbody tr:nth-of-type(odd),
  #detailTable tbody tr:nth-of-type(odd),
  #koreksiDetailTable tbody tr:nth-of-type(odd),
  #koreksiTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }

  #addTable tbody tr:hover,
  #detailTable tbody tr:hover,
  #koreksiDetailTable tbody tr:hover,
  #koreksiTable tbody tr:hover { background-color: #f5f3ff; }

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

  #addTable, #addTable td, #addTable th,
  #detailTable, #detailTable td, #detailTable th,
  #koreksiDetailTable, #koreksiDetailTable td, #koreksiDetailTable th,
  #koreksiTable, #koreksiTable td, #koreksiTable th { border-left: none !important; border-right: none !important; }
  #addTable tbody td,
  #detailTable tbody td,
  #koreksiDetailTable tbody td,
  #koreksiTable tbody td { border-top: none !important; border-bottom: 1px solid #f1f3f5 !important; font-size: 13px; vertical-align: middle; }

  #formKoreksiDetail .modal-body > .container-fluid + .container-fluid { margin-top: 16px; }
  #formKoreksiDetail .modal-body .row .col-2 label { margin-bottom: 2px; }
  #formKoreksiDetail .modal-body .row { row-gap: 10px; margin-left: -5px; margin-right: -5px; }
  #formKoreksiDetail .modal-body .row .col-2 { padding-left: 5px; padding-right: 5px; }

  /* Perbesar glyph + pada tombol Add tanpa mengubah ukuran bingkai tombol.
     line-height: 0 membuat ikon tidak menambah tinggi baris, jadi tinggi
     tombol tetap sama dengan tombol Detail di sebelahnya. */
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
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

  
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="printContainer" style="display:none">

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
          Outstanding Perintah Retur
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
           aria-controls="nav-profile" aria-selected="false">
          Retur Beli
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
                    <input type="date" class="po-filter-inp" id="rpgTglAwal1" value="{!! $rpgTglAwal !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="rpgTglAkhir1" value="{!! $rpgTglAkhir !!}">
                  </div>
                  <input type="search" id="rpgSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="rpgLen1">Tampilkan</label>
                    <select id="rpgLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                  <div class="po-toolbar-act"></div>
                </div>
                {{-- Bar kolom tersembunyi + tombol "Reset kolom" (diisi report-table.js).
                     Satu elemen dipakai bersama #tabel dan #tabelRetur, dipindah lewat JS
                     (rpgPindahBar) saat tab berganti - lihat rpgInitReportTableSekali(). --}}
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
                    <input type="date" class="po-filter-inp" id="rpgTglAwal2" value="{!! $rpgTglAwal !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="rpgTglAkhir2" value="{!! $rpgTglAkhir !!}">
                  </div>
                  <input type="search" id="rpgSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="rpgLen2">Tampilkan</label>
                    <select id="rpgLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
                {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat rpgPindahBar(). --}}
                <table id="tabelRetur" class="data-table po-aksi-hover">
                  <thead id="tabelRetur_header" class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabelRetur_data" class="text-left"></tbody>
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



<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No Bukti Out</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_noout" placeholder="No Out" disabled>
            </div>

            <div class="col-2">
              <label>No RPB</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_norpb" placeholder="No PBG" disabled>
            </div>

            <div class="col-1">
              <label>Gudang</label>
            </div>
            <div class="col-3">
              <input type="text" class="form-control" id="input_add_gdg" placeholder="Gdg Asal" required disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
            </div>

            <div class="col-2">
              <label>Nama Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_namacust" placeholder="cust supp" disabled>
            </div>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}


        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered data-table">
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">No PRB</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Kirim</th>
                  </tr>
                </thead>


                <tbody id="addTableData" class="text-right" >
                  <tr >
                      <td class="text-center" colspan="8">Belum ada data</td>
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-batal-add" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal" >Batal</button>

        <button type="button" class="btn btn-lg btn-chip-biru" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAdd()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal detail -->
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>Gudang</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_detail_gdg" placeholder="Gdg Asal" required disabled>
            </div>

            <div class="col-2">
              <label>No Out</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_detail_noout" placeholder="No PBG" disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Nama Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_detail_namacust" placeholder="Nama Supp" disabled>
            </div>

            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}


        <div class="container-fluid" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">No RPB</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                  </tr>
                </thead>


                <tbody id="detailTableData" class="text-right" >
                  <tr >
                      <td class="text-center" colspan="6">Belum ada data</td>
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-batal-add" data-dismiss="modal" >Batal</button>

      </div>
    </div>
  </div>
</div>
<!-- End modal detail-->


<!-- start modal detail koreksi -->
<div class="modal fade" id="formKoreksiDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No RPB</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_norpb" placeholder="No RPB" disabled>
            </div>

            <div class="col-2">
              <label>No OUT</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_noout" placeholder="No PBG" disabled>
            </div>

            <div class="col-2">
              <label>Gudang</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_koreksidetail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>

            <div class="col-2">
              <label>Cust Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_namacust" placeholder="Nama Cust" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid" style="overflow-x: auto;">

              <table id="koreksiDetailTable" class="table table-bordered">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                  </tr>
                </thead>


                <tbody id="koreksiDetailTableData" class="text-right" >
                  <tr >
                      <td class="text-center" colspan="5">Belum ada data</td>
                </tr>

                </tbody>


              </table>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}

      <div class="modal-footer">
        <button type="button" class="btn btn-lg btn-batal-add" data-dismiss="modal" >Batal</button>

      </div>

      <!-- <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No RPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_norpb" placeholder="No RPB" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_noout" placeholder="No PBG" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksidetail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <! -- <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary"  >Add Item</button> - ->
        </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="koreksiDetailTable" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>
                    </tr>
                  </thead>


                  <tbody id="koreksiDetailTableData" class="text-right" >
                    <tr >

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
  </div> -->
</div>
</div>
</div>
<!-- End modal detail koreksi-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No RPB</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_norpb" placeholder="No RPB" disabled>
            </div>

            <div class="col-2">
              <label>No OUT</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_noout" placeholder="No out" disabled>
            </div>

            <div class="col-2">
              <label>Gudang</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_gdg" placeholder="Kode Cust" required disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>

            <div class="col-2">
              <label>Cust Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Nama Cust" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-chip-biru" onclick="buttonKoreksiAdd()">Add Item</button>
            </div>
          </div>

          <div class="container-fluid">
            <!-- koreksi add -->
            <div id="formKoreksiAdd" class="container-fluid showhide">

                <div class="row">
                  <div class="col-12">
                    <h4>Add Item</h4>
                  </div>
                </div>

                <div class="row">
                  <div class="col-2">
                    <label>Pilih Barang</label>
                  </div>
                  <div class="col-4">
                    <div class="form-group">
                      <select onchange="changeSelectKoreksiAdd()" id="koreksiAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                      </select>
                    </div>
                  </div>
                </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Kode Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddKodeBrg" type="text" class="form-control" disabled>
                </div>
                
                <div class="col-2">
                  <div class="form-group">
                    <label>Nama Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddNamaBrg" type="text" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddInputQty" type="number" value="0.00" class="form-control">
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label>Satuan</label>
                  </div>
                </div>
                <div class="col-4">
                  <input type="text" id="koreksiAddSatuan" value="PCS" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty OS</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddQtyOS" type="number" value="0.00" class="form-control" disabled>
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty PO</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddQtyPO" type="number" value="0.00" class="form-control" disabled>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-lg btn-batal-add" onclick="buttonBatalShowHide()">Batal</button>
                  <button type="button" onclick="submitAddKoreksi()" class="btn btn-lg btn-chip-biru">Add Item</button>
                </div>
              </div>
            </div>

            <!-- koreksi edit -->
            <div id="formKoreksiEdit" class="container-fluid showhide">

              <div class="row">
                <div class="col-12">
                  <h4>Edit Item</h4>
                </div>
              </div>

              <div class="row">
                <div class="col-2">
                  <div class="form-group">
                    <label>Kode Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditKodeBrg" type="text" class="form-control" disabled>
                </div>
                
                <div class="col-2">
                  <div class="form-group">
                    <label>Nama Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditNamaBrg" type="text" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditInputQty" onblur='cekQntEdit()' type="number" value="0.00" class="form-control">
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label>Satuan</label>
                  </div>
                </div>
                <div class="col-4">
                  <input type="text" id="koreksiEditSatuan" value="PCS" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty OS</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditQtyOS" type="number" value="0.00" class="form-control" disabled>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-lg btn-batal-add" onclick="buttonBatalShowHide()">Batal</button>
                  <button type="button" onclick="submitEditKoreksi()" class="btn btn-lg btn-chip-biru">Edit Item</button>
                </div>
              </div>
            </div>
            <!-- end -->
          </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">
            <table id="koreksiTable" class="table table-bordered data-table">
              <thead class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">No PRB</th>
                  <th scope="col">Kode Brg</th>
                  <th scope="col">Nama Brg</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Qty OS</th>
                  <th scope="col">Satuan</th>
                </tr>
              </thead>
              <tbody id="koreksiTableData" class="text-right">
                <tr>
                  <td class="text-center" colspan="7">Belum ada data</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}


      <!-- <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No RPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_norpb" placeholder="No RPB" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noout" placeholder="No out" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-chip-biru" onclick="buttonKoreksiAdd()">Add Item</button>
        </div>

        <div class="container-fluid">
          <!- - koreksi add - ->
          <div id="formKoreksiAdd" class="container-fluid showhide">
            <div class="line"></div>
            <div class="row">
              <div class="col-12">
                <h4>Add Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <label>Pilih Barang</label>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <select onchange="changeSelectKoreksiAdd()" id="koreksiAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiAddKodeBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiAddNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Qty</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiAddInputQty" type="number" value=0.00 class="form-control">
              </div>
              <div class="col-12">
                <div class="form-group">
                <label>Satuan</label>
              </div>
              </div>
              <div class="col-12">

                  <input type="text" id="koreksiAddSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Qty OS</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiAddQtyOS" type="number" value=0.00 class="form-control" disabled>
              </div>
              <div class="col-12">
                <div class="form-group">
                <label>Qty PO</label>
              </div>
              </div>
              <div class="col-12">

                  <input id="koreksiAddQtyPO" type="number" value=0.00 class="form-control" disabled>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
                <button type="button" onclick="submitAddKoreksi()" class="btn btn-chip-biru">Add Item</button>
              </div>

            </div>
            <div class="line"></div>
          </div>

          <!- - koreksi edit - ->

          <div id="formKoreksiEdit" class="container-fluid showhide">
            <div class="line"></div>
            <div class="row">
              <div class="col-12">
                <h4>Edit Item</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditKodeBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Qty</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditInputQty" type="number" value=0.00 class="form-control">
              </div>
              <div class="col-12">
                <div class="form-group">
                <label>Satuan</label>
              </div>
              </div>
              <div class="col-12">

                  <input type="text" id="koreksiEditSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Qty OS</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditQtyOS" type="number" value=0.00 class="form-control" disabled>
              </div>

            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
                <button type="button" onclick="submitEditKoreksi()" class="btn btn-primary" >Edit Item</button>
              </div>

            </div>
            <div class="line"></div>
          </div>

          <!- - end  - ->
        </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="koreksiTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>


                  <tbody id="koreksiTableData" class="text-right" >
                    <tr >

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
  </div> -->
</div>
</div>
</div>
<!-- End modal koreksi-->




@endsection

@section('js')
<script src="{!! URL::asset('js/ajc-func-core.js') !!}"></script>
{{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + tombol
     "Reset kolom"), sama dengan newpo.blade.php / uangmukabeli.blade.php. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

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

let dataRefreshOutstanding = []
let dataRefreshPenerimaan = []
let xkodesupp=''
let nopbl=''
let urutpbl=0

let addDataArray = []

let koreksiPenerimaanArray = []
let koreksiDataEdit = {}
let koreksiDataAddList = []

function buttonBatalShowHide() {
  $('.showhide').hide();
}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('getnobuktireturbeli') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res, 'NoBUkti')
      console.log(res[0].Nobukti , res[0].Nourut)
      console.log('===============')
      document.getElementById("input_add_norpb").value = res[0].Nobukti
      document.getElementById("input_add_noUrut").value = res[0].Nourut
    }
  })
}


function buttonAdd (NOBUKTI) {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log('button add')
  console.log(NOBUKTI)

  setNewNoBukti()


  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      addDataArray = res
      console.log('===========================')
    }
  })
  //
  document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  document.getElementById("input_add_noout").value = addDataArray[0].NOBUKTI
  document.getElementById("input_add_gdg").value = addDataArray[0].Kodegdg
  xkodesupp = addDataArray[0].KODESUPP
  nopbl = addDataArray[0].noPBL
  urutpbl = addDataArray[0].UrutPBL

  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  //
  let rowTable = ""
  addDataArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    // Qty = jumlah yang sudah diterima sebelumnya (B.Qnt2), Qty OS = sisa outstanding
    // (QntOUt) - sebelumnya kolom ini membaca Qnt1 padahal syaratnya Qnt2, sehingga kalau
    // belum ada penerimaan sama sekali kolom Qty diam-diam tampil 0.00. Disamakan dengan
    // buttonDetail() yang konsisten memakai Qnt2.
    if(item.Qnt2) {
      qnt = parseFloat(item.Qnt2).toFixed(2)
    }
    if(item.QntOUt) {
      qntos = parseFloat(item.QntOUt).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
    <td>${item.NOBUKTI}</td>
    <td>${item.Kodebrg}</td>
    <td>${item.namaBrg}</td>
    <td class="text-right">${formatAngka(qnt)}</td>
    <td class="text-right">${formatAngka(qntos)}</td>
    <td class="text-center" >${item.SATUAN}</td>
    <td class="text-center"><input onblur="cekQnt(${qntos}, ${i})" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td>
    </tr>`
  });
  document.getElementById("addTableData").innerHTML = rowTable

  $("#form").modal('show')


}

  let stateQnt = 0
function cekQnt (qntos, urut){


  let qntTerima = document.getElementById('input_add_qntTerima'+urut).value

  if (parseFloat(qntos) < parseFloat(qntTerima)){
    stateQnt = 1
  } else {
    stateQnt = 0
  }

  if (stateQnt == 1 ){
    alertify.warning('Qty Kirim tidak boleh melebihi Qty OS')
    document.getElementById('input_add_qntTerima'+urut).value = '0.00'
  }

}

function cekQntEdit (){

  let stateQnt = 0

  let qntTerima = document.getElementById('koreksiEditInputQty').value
  let qntos = document.getElementById('koreksiEditQtyOS').value

  if (parseFloat(qntos) < parseFloat(qntTerima)){
    stateQnt = 1
  }

  if (stateQnt == 1 ){
    alertify.warning('Qty Kirim tidak boleh melebihi Qty OS')
    document.getElementById('koreksiEditInputQty').value = qntEditTemp
  }

}

function buttonKoreksiAdd() {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  $('.showhide').hide();
  document.getElementById("koreksiAddKodeBrg").value = ""
  document.getElementById("koreksiAddNamaBrg").value = ""
  document.getElementById("koreksiAddQtyOS").value = 0.00
  document.getElementById("koreksiAddQtyPO").value = 0.00
  document.getElementById("koreksiAddInputQty").value = "0.00"
  $('#formKoreksiAdd').show();
}


function changeSelectKoreksiAdd() {
  let indexBarang = document.getElementById("koreksiAddSelect").value;
  console.log(indexBarang)
  console.log(koreksiDataAddList[indexBarang])
  let qnt = 0.00
  let qntos = 0.00
  if(koreksiDataAddList[indexBarang].QNT) {
    qnt = parseFloat(koreksiDataAddList[indexBarang].QNT).toFixed(2)
  }
  if(koreksiDataAddList[indexBarang].QntOS) {
    qntos = parseFloat(koreksiDataAddList[indexBarang].QntOS).toFixed(2)
  }
  document.getElementById("koreksiAddKodeBrg").value = koreksiDataAddList[indexBarang].KODEBRG
  document.getElementById("koreksiAddNamaBrg").value = koreksiDataAddList[indexBarang].NAMABRG
  document.getElementById("koreksiAddQtyOS").value = qntos
  document.getElementById("koreksiAddQtyPO").value = qnt
  document.getElementById("koreksiAddSatuan").value = koreksiDataAddList[indexBarang].Satuan

}

function refreshKoreksi (NOBUKTI) {
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      koreksiPenerimaanArray = res
      koreksiDataEdit = res[0]
    }
  })

  if (!koreksiPenerimaanArray.length) {
    console.log('item habis')
    $("#formKoreksi").modal('hide')
    return
  }

  let nooutbrg = ''
  console.log(koreksiPenerimaanArray[0])
  if (koreksiPenerimaanArray[0].NooutBRg) {
    nooutbrg = koreksiPenerimaanArray[0].NooutBRg
  }

  document.getElementById("input_koreksi_noout").value = nooutbrg
  document.getElementById("input_koreksi_norpb").value = koreksiPenerimaanArray[0].NOBUKTI
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  document.getElementById("input_koreksi_namacust").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

  let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_koreksi_tanggal').val(date1)


  let rowTable = ""
  koreksiPenerimaanArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.QNT) {
      qnt = parseFloat(item.QNT).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td class="text-center">
      <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    <td>${item.NOBUKTI}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${formatAngka(qnt)}</td>
    <td class="text-right">${formatAngka(qntos)}</td>
    <td class="text-center">${item.Satuan}</td>
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable



  console.log('===========____________++++++++++++++')
  console.log(koreksiPenerimaanArray[0])
  $.ajax({
    url: "{!! url('returbelikoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      norpb: koreksiPenerimaanArray[0].NOBUKTI,
      noout: koreksiPenerimaanArray[0].NooutBRg
    },
    success: function(res) {
      console.log(res , "addlistkoreksi !!!!!!")
      koreksiDataAddList = res
    }
  })


  let tempKoreksiAddList = ""
  if(koreksiDataAddList.length) {
    tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
    koreksiDataAddList.forEach((item, i) => {
      tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
    });
  } else {
    tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
  }



  document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList


}

function buttonKoreksi (NOBUKTI) {
  console.log('button koreksi')
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      koreksiPenerimaanArray = res
      koreksiDataEdit = res[0]
    }
  })

  let nooutbrg = ''
  console.log(koreksiPenerimaanArray[0])
  if (koreksiPenerimaanArray[0].NooutBRg) {
    nooutbrg = koreksiPenerimaanArray[0].NooutBRg
  }

  document.getElementById("input_koreksi_noout").value = nooutbrg
  document.getElementById("input_koreksi_norpb").value = koreksiPenerimaanArray[0].NOBUKTI
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  document.getElementById("input_koreksi_namacust").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

  let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_koreksi_tanggal').val(date1)


  let rowTable = ""
  koreksiPenerimaanArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.QNT) {
      qnt = parseFloat(item.QNT).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td class="text-center">

    <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    <td>${item.NOBUKTI}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${formatAngka(qnt)}</td>
    <td class="text-right">${formatAngka(qntos)}</td>
    <td class="text-center">${item.Satuan}</td> 
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable



  console.log('===========____________++++++++++++++')
  console.log(koreksiPenerimaanArray[0])
  $.ajax({
    url: "{!! url('returbelikoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      norpb: koreksiPenerimaanArray[0].NOBUKTI,
      noout: koreksiPenerimaanArray[0].NooutBRg
    },
    success: function(res) {
      console.log(res , "addlistkoreksi !!!!!!")
      koreksiDataAddList = res
    }
  })


  let tempKoreksiAddList = ""
  if(koreksiDataAddList.length) {
    tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
    koreksiDataAddList.forEach((item, i) => {
      tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
    });
  } else {
    tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
  }



  document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList






  $("#formKoreksi").modal('show')
}



function buttonDetailKoreksi (NOBUKTI) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning('Detail tidak ditemukan')
        document.getElementById("input_koreksidetail_noout").value = ''
        document.getElementById("input_koreksidetail_norpb").value = ''
        document.getElementById("input_koreksidetail_gdg").value = ''
        document.getElementById("input_koreksidetail_namacust").value = ''
        document.getElementById("koreksiDetailTableData").innerHTML = ''
        $("#formKoreksiDetail").modal('show')
        return
      }

      let nooutbrg = res[0].NooutBRg || ''

      document.getElementById("input_koreksidetail_noout").value = nooutbrg
      document.getElementById("input_koreksidetail_norpb").value = res[0].NOBUKTI
      document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg
      document.getElementById("input_koreksidetail_namacust").value = res[0].NAMACUSTSUPP
      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_koreksidetail_tanggal').val(date1)



      let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.QNT) {
          qnt = parseFloat(item.QNT).toFixed(2)
        }
        if(item.QntOS) {
          qntos = parseFloat(item.QntOS).toFixed(2)
        }
        rowTable += `<tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${formatAngka(qnt)}</td>
        <td class="text-right">${formatAngka(qntos)}</td>
        <td class="text-center">${item.Satuan}</td>

        </tr>`
      });
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable



    }
  })

  $("#formKoreksiDetail").modal('show')

}


let qntEditTemp = 0
function buttonKoreksiEdit (index) {
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  $('.showhide').hide();
  // console.log(koreksiPenerimaanArray[index])
  // console.log(index)
  koreksiDataEdit = koreksiPenerimaanArray[index]
  console.log('edit data',koreksiDataEdit)
  let qnt = 0.00
  if (koreksiDataEdit.QNT) {

    qnt = parseFloat(koreksiDataEdit.QNT).toFixed(2)
  }
  document.getElementById("koreksiEditKodeBrg").value = koreksiDataEdit.KODEBRG
  document.getElementById("koreksiEditNamaBrg").value = koreksiDataEdit.NAMABRG
  document.getElementById("koreksiEditQtyOS").value = parseFloat(koreksiDataEdit.QntOS) + parseFloat(qnt)
  document.getElementById("koreksiEditInputQty").value = qnt
  qntEditTemp = qnt
  document.getElementById("koreksiEditSatuan").value = koreksiDataEdit.Satuan
  $('#formKoreksiEdit').show();
  document.getElementById('formKoreksiEdit').scrollIntoView();
}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      // addDataArray = res
      console.log('===========================')
      document.getElementById("input_detail_namacust").value = res[0].NAMACUSTSUPP
      document.getElementById("input_detail_noout").value = res[0].NOBUKTI
      document.getElementById("input_detail_gdg").value = nullToStrip(res[0].Kodegdg)

      let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.Qnt2) {
          qnt = parseFloat(item.Qnt2).toFixed(2)
        }
        if(item.QntOUt) {
          qntos = parseFloat(item.QntOUt).toFixed(2)
        }
        rowTable += `<tr class="text-left">
        <td>${item.NOBUKTI}</td>
        <td>${item.Kodebrg}</td>
        <td>${item.namaBrg}</td>
        <td class="text-right">${formatAngka(qnt)}</td>
        <td class="text-right">${formatAngka(qntos)}</td>
        <td class="text-center">${item.SATUAN}</td>
        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable

    }
  })



  $("#formDetail").modal('show')


}


function submitAdd () {

  stateQnt = 0

  console.log('Submit Add')
  let _token = $("#_token").val();
  let tempData = []

  // Hanya baris yang dicentang yang divalidasi & dikirim - sebelumnya semua baris
  // outstanding ikut divalidasi (termasuk yang tidak dicentang dan defaultnya 0.00),
  // sehingga submit selalu ditolak begitu supplier punya lebih dari satu baris outstanding.
  addDataArray.forEach((item, i) => {
    if (document.getElementById(`add_checkbox${i}`).checked) {
      addDataArray[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
      tempData.push(addDataArray[i])
    }
  });

  if (!tempData.length) {
    alertify.warning("Tidak ada item dipilih");
    return
  }

  let flag = false
  tempData.forEach((item, i) => {
    let value = String(item.inputQntTerima).trim();
    if (value === '' || parseFloat(value) === 0 || isNaN(parseFloat(value))) {
      flag = true;
    }
    // Alias kolom sisa outstanding dari getDetailOutstanding() adalah QntOUt, bukan
    // QntOS - sebelumnya guard ini selalu Number(undefined) = NaN sehingga tidak pernah
    // menyala.
    if (Number(item.inputQntTerima) > Number(item.QntOUt)) {
      flag = true;
    }
    if (Number(item.inputQntTerima) < 0) {
      flag = true;
    }
  });
  if (flag) {
    alertify.warning("Qty tidak boleh kosong / negatif / melebihi Qty OS");
    return
  }


  let inputDate = $("#input_add_tanggal").val();
  let noout = $(`#input_add_noout`).val();
  let norpb = $(`#input_add_norpb`).val();
  let nourut =  $(`#input_add_noUrut`).val();
  let NOSAT = 1;
  let zkodesupp=xkodesupp;
  let znopbl=nopbl;
  let zurutpbl=urutpbl;

  console.log('noout', zkodesupp)
  console.log('noout', noout)
  console.log('norpb', norpb)
  console.log('nourut', nourut)
  console.log('inputDate', inputDate)
  console.log(tempData,'ttttttt')

  let checkDate = new Date(inputDate)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  $.ajax({
    url: "{!! url('addreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      norpb,
      noout,
      nourut,
      tempData,
      inputDate,
      NOSAT,
      zkodesupp,
      nopbl,
      urutpbl,

    },
    success: function(res) {
      console.log(res ,'!')

      if (res ==1) {

        $("#form").modal('hide')
        alertify.success('RPB telah ditambah');
        loadAll()
      }

      if (res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }


      if (res == 3 ) {
        alertify.warning('Stok gudang tidak mencukupi');
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}



function buttonKoreksiDelete(index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let dataRPB = koreksiPenerimaanArray[index]
  console.log(dataRPB)

    let _token = $("#_token").val();

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataRPB.KODEBRG + dataRPB.NAMABRG + ' ?',
      function() {
        console.log('yes')
        let choice = "D"
        let norpb = dataRPB.NOBUKTI
        let nourut = dataRPB.NOURUT
        let inputDate = $("#input_koreksi_tanggal").val()
        let kodesupp = dataRPB.KODESUPP
        let kodegdg = dataRPB.Kodegdg
        let noout = dataRPB.NooutBRg
        let keterangan = ""
        let faktursupp = ""
        let urut = dataRPB.URUT
        let kodebrg = dataRPB.KODEBRG
        let urutout = dataRPB.UrutOutBRg
        let nosat = dataRPB.NOSAT
        let satuan = dataRPB.Satuan
        let isi = dataRPB.ISI
        let flagtipe = 0
        let nobatch = ""
        let qntTerima = 0
        let qntTerima1 = 0
        let qntTerima2 = 0
        let nolpb = 0


          console.log("choice",choice)
          console.log("norpb",norpb)
          console.log("nourut",nourut)
          console.log("inputDate",inputDate)
          console.log("kodesupp",kodesupp)
          console.log("kodegdg",kodegdg)
          console.log("noout",noout)
          console.log("keterangan",keterangan)
          console.log("faktursupp",faktursupp)
          console.log("urut",urut)
          console.log("kodebrg",kodebrg)
          console.log("urutout",urutout)
          console.log("qntTerima", qntTerima)
          console.log("nosat",nosat)
          console.log("satuan",satuan)
          console.log("isi",isi)
          console.log("qntTerima1", qntTerima1)
          console.log("qntTerima2", qntTerima2)
          console.log("flagtipe",flagtipe)
          console.log("nobatch",nobatch)



        $.ajax({
          url: "{!! url('koreksireturbeli') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            norpb,
            nourut,
            inputDate,
            kodesupp,
            kodegdg,
            noout,
            keterangan,
            faktursupp,
            urut,
            kodebrg,
            urutout,
            qntTerima,
            nosat,
            satuan,
            isi,
            qntTerima1,
            qntTerima2,
            flagtipe,
            nobatch,
            nolpb
          },
          success: function(res) {
            console.log(res ,'succes delete koreksi')
            refreshKoreksi(norpb)
            alertify.success('Item telah didelete');
            loadAll()
          }
        })
      }
    ,function(){
      console.log('no')
    });
}


function submitAddKoreksi () {


  let _token = $("#_token").val();
  let check = document.getElementById("koreksiAddSelect").value;
  console.log(check)
  if (check === "") {
    // console.log('a')
    alertify.warning("Tidak ada item dipilih");
    return
  }
  let dataOut = koreksiDataAddList[check]
  console.log('=========================')

  console.log(dataOut)
  console.log('=========================')
  // console.log(dataOut)
  let qntTerima = $("#koreksiAddInputQty").val()
  if (Number(qntTerima) > Number(dataOut.QntOS)) {
    alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
    return
  }
  if (Number(qntTerima) <= 0) {
    alertify.warning("Qty tidak bisa 0 atau negatif");
    return
  }
  console.log('lolos')
  console.log(dataOut.NOSAT)
  let qntTerima1 = 0
  let qntTerima2 = 0
  if (dataOut.NOSAT == 1) {
    qntTerima1 = qntTerima
    qntTerima2 = qntTerima / dataOut.ISI2
  } else if (dataOut.NOSAT == 2) {
    qntTerima1 = qntTerima * dataOut.ISI2
    qntTerima2 = qntTerima
  }
  console.log(qntTerima, qntTerima1,qntTerima2)

  let dataRPB = koreksiDataEdit


  let choice = "I"
  let norpb = dataRPB.NOBUKTI
  let nourut = dataRPB.NOURUT
  let inputDate = $("#input_koreksi_tanggal").val()
  let kodesupp = dataRPB.KODESUPP
  let kodegdg = dataRPB.Kodegdg
  let noout = dataOut.NOBUKTI
  let keterangan = ""
  let faktursupp = ""
  let urut = 0
  let kodebrg = dataOut.KODEBRG
  let urutout = dataOut.Urut
  let nosat = dataOut.NOSAT
  let satuan = dataOut.Satuan
  let isi = dataOut.ISI
  let flagtipe = 0
  let nobatch = ""
  let nolpb = dataOut.UrutLPB


  console.log("choice",choice)
  console.log("norpb",norpb)
  console.log("nourut",nourut)
  console.log("inputDate",inputDate)
  console.log("kodesupp",kodesupp)
  console.log("kodegdg",kodegdg)
  console.log("noout",noout)
  console.log("keterangan",keterangan)
  console.log("faktursupp",faktursupp)
  console.log("urut",urut)
  console.log("kodebrg",kodebrg)
  console.log("urutout",urutout)
  console.log("qntTerima", qntTerima)
  console.log("nosat",nosat)
  console.log("satuan",satuan)
  console.log("isi",isi)
  console.log("qntTerima1", qntTerima1)
  console.log("qntTerima2", qntTerima2)
  console.log("flagtipe",flagtipe)
  console.log("nobatch",nobatch)

  $.ajax({
    url: "{!! url('koreksireturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      norpb,
      nourut,
      inputDate,
      kodesupp,
      kodegdg,
      noout,
      keterangan,
      faktursupp,
      urut,
      kodebrg,
      urutout,
      qntTerima,
      nosat,
      satuan,
      isi,
      qntTerima1,
      qntTerima2,
      flagtipe,
      nobatch,
      nolpb
    },
    success: function(res) {
      console.log(res ,'succes add koreksi')
      refreshKoreksi(norpb)
      alertify.success('Item telah ditambah');
      loadAll()
    }
  })
}



function submitEditKoreksi () {
  // console.log('submit edit koreksi')
  // console.log(koreksiDataEdit)
  let dataRPB = koreksiDataEdit

  let qntTerimaTemp = document.getElementById('koreksiEditInputQty').value

  if (qntTerimaTemp == 0){
    alertify.warning('QTY tidak boleh kosong')
    return;
  }

  let _token = $("#_token").val();

  let qntTerima = $("#koreksiEditInputQty").val()
  // console.log(dataRPB)
  // console.log(qntTerima)
  if (Number(qntTerima) > Number(dataRPB.QntOS) + Number(dataRPB.QNT)) {
    alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
    return
  }
  if (Number(qntTerima) <= 0) {
    alertify.warning("Qty tidak bisa 0 atau negatif");
    return
  }
  console.log('lolos')
  // console.log(dataSSK.NOSAT)
  // console.log(dataSSK.ISI2)
  let qntTerima1 = 0
  let qntTerima2 = 0
  if (dataRPB.NOSAT == 1) {
    qntTerima1 = qntTerima
    qntTerima2 = qntTerima / dataRPB.ISI2
  } else if (dataRPB.NOSAT == 2) {
    qntTerima1 = qntTerima * dataRPB.ISI2
    qntTerima2 = qntTerima
  }

    // let choice = "U"
    // let nossk = dataRPB.NOBUKTI
    // let nourut = dataRPB.NOURUT
    // let inputDate = $("#input_koreksi_tanggal").val()
    // let note = ""
    // let urut = dataRPB.URUT
    // let kodebrg = dataRPB.KODEBRG
    // let gdgasal = dataRPB.Kodegdg
    // let gdgtujuan = "GSAMPLE"
    // let sat_1 = dataRPB.SAT1
    // let sat_2 = dataRPB.SAT2
    // // let qntTerima = //
    // // let qntTerima2 = //
    // let nosat = dataRPB.NOSAT
    // let isi = dataRPB.ISI
    // // user
    // let kodecustsupp = dataRPB.KODECUSTSUPP
    // let kodesls = dataRPB.KODESLS
    // let pbonus = 0
    // let maxol = 0
    // let nopr = ""
    // let urutpr = 0
    // let pkonsi = 0
    // let nooutbrg = ''
    // let urutoutbrg = 0

    let choice = "U"
    let norpb = dataRPB.NOBUKTI
    let nourut = dataRPB.NOURUT
    let inputDate = $("#input_koreksi_tanggal").val()
    let kodesupp = dataRPB.KODESUPP
    let kodegdg = dataRPB.Kodegdg
    let noout = dataRPB.NooutBRg
    let keterangan = ""
    let faktursupp = ""
    let urut = dataRPB.URUT
    let kodebrg = dataRPB.KODEBRG
    let urutout = dataRPB.UrutOutBRg
    let nosat = dataRPB.NOSAT
    let satuan = dataRPB.Satuan
    let isi = dataRPB.ISI
    let flagtipe = 0
    let nobatch = ""
    let nolpb = 0


      console.log("choice",choice)
      console.log("norpb",norpb)
      console.log("nourut",nourut)
      console.log("inputDate",inputDate)
      console.log("kodesupp",kodesupp)
      console.log("kodegdg",kodegdg)
      console.log("noout",noout)
      console.log("keterangan",keterangan)
      console.log("faktursupp",faktursupp)
      console.log("urut",urut)
      console.log("kodebrg",kodebrg)
      console.log("urutout",urutout)
      console.log("qntTerima", qntTerima)
      console.log("nosat",nosat)
      console.log("satuan",satuan)
      console.log("isi",isi)
      console.log("qntTerima1", qntTerima1)
      console.log("qntTerima2", qntTerima2)
      console.log("flagtipe",flagtipe)
      console.log("nobatch",nobatch)



    $.ajax({
      url: "{!! url('koreksireturbeli') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        norpb,
        nourut,
        inputDate,
        kodesupp,
        kodegdg,
        noout,
        keterangan,
        faktursupp,
        urut,
        kodebrg,
        urutout,
        qntTerima,
        nosat,
        satuan,
        isi,
        qntTerima1,
        qntTerima2,
        flagtipe,
        nobatch,
        nolpb
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(norpb)
        alertify.success('Item telah diedt');
        loadAll()
      }
    })
}



/* ============ Header tabel interaktif (window.ReportTable) ============
 * Sama pola dengan newpo.blade.php: DUA tabel (urut 1 = Outstanding Retur Beli, urut 2 =
 * Retur Beli), masing-masing punya konfigurasi kolomnya sendiri tersimpan di DBHEADERTABLE
 * lewat endpoint saveheadertable/getheadertable yang sudah ada (href = 'returpembeliangudang').
 * BEDA dengan newpo: satu endpoint rpgloadAll() SEKALIGUS mengembalikan data + konfigurasi
 * kolom kedua tabel (lihat ReturPembelianGudangController@loadAll), jadi tidak perlu
 * pemanggilan getheadertable terpisah saat halaman pertama dibuka. */
const RPG_HREF = 'returpembeliangudang'
let rpgCart = { 1: [], 2: [] }
let rpgActiveUrut = 1
let rpgPanjangHalaman = { 1: 10, 2: 10 }
let rpgRtSudahInit = false
const RPG_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
// Tabel di tab yang TIDAK aktif tidak digambar saat loadAll() - cukup ditandai di sini,
// lalu digambar sungguhan saat tabnya dibuka (lihat handler shown.bs.tab). Sama pola
// dengan npoPerluGambar di newpo.blade.php.
let rpgPerluGambar = { 1: false, 2: false }

// Field query DBPRRBELIDET/dbRBeli tidak ramah dibaca - label yang tampil ke user dipasang
// di sini, bukan lewat DBHEADERTABLEALIAS (kosong untuk href ini).
const RPG_LABEL_1 = { NOBUKTI: 'No. Out', TANGGAL: 'Tanggal', NAMACUSTSUPP: 'Supplier' }
const RPG_LABEL_2 = { NoBukti: 'No. Bukti', Tanggal: 'Tanggal', NamaCustSupp: 'Supplier' }

window.g_href = RPG_HREF
window.g_modeReport = 1
window.gcart_header = []

// Tanggal dari SQL Server ditampilkan Y/m/d, sama seperti tampilan lama.
function rpgFormatTanggal (v) {
  if (!v) { return '' }
  let d = new Date(v)
  if (isNaN(d.getTime())) { return v }
  let day = ('0' + d.getDate()).slice(-2)
  let month = ('0' + (d.getMonth() + 1)).slice(-2)
  return d.getFullYear() + '/' + month + '/' + day
}

function rpgBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered, labelMap) {
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
// yang sama di newpo.blade.php / uangmukabeli.blade.php.
function rpgKolomTampil (urut) {
  return (rpgCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function rpgKolomRender (c) {
  return { field: c[0], label: c[1], tipe: Number(c[8]), desimal: Number(c[5]) }
}

function rpgRenderNilai (col, item) {
  let nilai = item ? item[col.field] : undefined
  if (col.tipe === 2) {
    return nilai ? rpgFormatTanggal(nilai) : ''
  }
  return (nilai === null || nilai === undefined) ? '' : nilai
}

// Kalau public/js/report-table.js belum ikut terunggah, halaman tetap tampil dengan
// <th> biasa, hanya tanpa drag & roda gigi.
function rpgHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => { html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>` });
  return html + '</tr>'
}

function rpgUrutTabAktif () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

function rpgAktifkanTabel (urut) {
  rpgActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = rpgCart[urut]
}

function rpgOnChangeAktif () {
  if (rpgActiveUrut === 2) {
    renderTabelRetur()
  } else {
    renderTabelOutstanding()
  }
}

// Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman - sama
// alasannya dengan newpo.blade.php / uangmukabeli.blade.php.
function rpgInitReportTableSekali () {
  if (rpgRtSudahInit || typeof ReportTable === 'undefined') { return }
  rpgRtSudahInit = true

  let urutAktif = rpgUrutTabAktif()
  let idTabel = { 1: '#tabel', 2: '#tabelRetur' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], onChange: rpgOnChangeAktif })
  });

  ReportTable.init({
    table: RPG_SELEKTOR_TABEL_AKTIF,
    bar: '#rtBar',
    onChange: rpgOnChangeAktif
  })

  // DataTables memasang handler sort langsung di tiap <th>, sedangkan roda gigi/drag milik
  // report-table.js didelegasikan di <thead> - tanpa penanganan khusus, klik roda gigi juga
  // memicu sort DataTables. Sama solusinya dengan newpo.blade.php / uangmukabeli.blade.php:
  // hentikan event aslinya di fase capture, tembakkan ulang satu event click baru langsung
  // ke <thead>.
  let rpgGuardUlangKlik = false
  let idThead = ['tabel_header', 'tabelRetur_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (rpgGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      rpgGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      rpgGuardUlangKlik = false
    }, true)
  });
}

// Pindahkan elemen #rtBar supaya duduk tepat sebelum tabel yang sedang aktif - sama
// catatan/bug-fix dengan npoPindahBar()/umbPindahBar().
function rpgPindahBar (urut) {
  let bar = document.getElementById('rtBar')
  let id = urut === 2 ? 'tabelRetur' : 'tabel'
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
// scrollbar sendiri - sama pola dengan npoAturTinggiTabel() di newpo.blade.php.
function rpgAturTinggiTabel () {
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
// dataset.rtBound karena renderTabelOutstanding()/renderTabelRetur() destroy+init tiap
// kali kolom digeser/disembunyikan.
function rpgIkatSearch (urut) {
  let id = urut === 2 ? 'rpgSearch2' : 'rpgSearch1'
  let tabelId = urut === 2 ? '#tabelRetur' : '#tabel'
  let input = document.getElementById(id)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  input.addEventListener('input', function () {
    $(tabelId).DataTable().search(input.value).draw()
  })
}

function rpgIkatPanjangHalaman (urut) {
  let id = urut === 2 ? 'rpgLen2' : 'rpgLen1'
  let tabelId = urut === 2 ? '#tabelRetur' : '#tabel'
  let sel = document.getElementById(id)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(rpgPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    rpgPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    $(tabelId).DataTable().page.len(rpgPanjangHalaman[urut]).draw()
  })
}

// Kedua tab punya filter tanggal sendiri (rpgTglAwal1/rpgTglAkhir1 untuk Outstanding,
// rpgTglAwal2/rpgTglAkhir2 untuk Retur Beli) - ganti salah satu tanggal periode -> muat
// ulang seluruh data (rpgloadAll() memang mengembalikan kedua tabel sekaligus) lalu
// gambar ulang tab yang sedang aktif saja.
function rpgIkatPeriodeUmum (idAwal, idAkhir) {
  let awal  = document.getElementById(idAwal)
  let akhir = document.getElementById(idAkhir)
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function rpgIkatPeriode1 () { rpgIkatPeriodeUmum('rpgTglAwal1', 'rpgTglAkhir1') }
function rpgIkatPeriode2 () { rpgIkatPeriodeUmum('rpgTglAwal2', 'rpgTglAkhir2') }

/* ---- Jembatan ke mesin penyimpan milik report-table.js ----
 * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
 * didefinisikan - report-table.js sudah punya fallback yang memutasi gcart_header sendiri
 * lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke doSimpanHeader
 * di bawah. */
function rpgUrutSah (mode) {
  return Number(mode) === 2 ? 2 : 1
}

window.doSimpanHeader = function (href, mode) {
  let urut = rpgUrutSah(mode)
  let cart = rpgCart[urut] || []

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
      href: RPG_HREF,
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
  let urut = rpgUrutSah(mode)

  $.ajax({
    url: "{!! url('getheadertable') !!}",
    type: "post",
    async: false,
    data: {
      _token: $("#_token").val(),
      href: RPG_HREF,
      urut: urut,
      reset: 1
    },
    success: function (res) {
      if (urut === 2) {
        rpgCart[2] = rpgBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, RPG_LABEL_2)
      } else {
        rpgCart[1] = rpgBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, RPG_LABEL_1)
      }
      window.gcart_header = rpgCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

// Tab "Outstanding Retur Beli". Baris digambar dari dataRefreshOutstanding (dikelompokkan
// per KODESUPP oleh controller) menurut kolom yang sedang tampil (rpgKolomTampil) - susunan
// kolom hasil geser/sembunyi selalu konsisten dengan hasil render ulang. Tombol Actions
// TETAP per-baris (buttonDetail/buttonAdd memakai KODESUPP, bukan tombol Add global).
function renderTabelOutstanding () {
  rpgAktifkanTabel(1)

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = rpgKolomTampil(1)
  let kolomRender = cols.map(rpgKolomRender)

  let thead = document.getElementById('tabel_header')
  thead.innerHTML = rpgHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }

  let rowTable = ''
  dataRefreshOutstanding.forEach((item) => {
    let header = (item && item[0]) ? item[0] : {}
    let kodesupp = header.KODESUPP || ''
    let tombolAksi = `
      <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${kodesupp}')"><i class="bi bi-info-lg"></i></button>
      <button class="btn btn-primary btn-sm" type="button" title="Add" onclick="buttonAdd('${kodesupp}')"><i class="bi bi-plus"></i></button>
    `
    rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
    kolomRender.forEach((c) => {
      rowTable += `<td>${rpgRenderNilai(c, header)}</td>`
    });
    rowTable += `</tr>`
  });

  // Baris "Tidak ada data" TIDAK ditulis manual di sini - baris manual hanya berisi 1 sel
  // sedangkan header punya banyak kolom, dan DataTables mencoba mengindeks sel-sel yang
  // tidak ada di situ lalu crash (_DT_CellIndex). Biarkan <tbody> kosong dan serahkan ke
  // opsi language.emptyTable di bawah.
  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel').DataTable({
    lengthChange: false,
    pageLength: rpgPanjangHalaman[1],
    // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom Actions).
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    },
    drawCallback: function () {
      setTimeout(rpgAturTinggiTabel, 0)
    }
  })

  rpgPindahBar(1)
  rpgIkatSearch(1)
  rpgIkatPanjangHalaman(1)
  rpgIkatPeriode1()
  let inputSearch = document.getElementById('rpgSearch1')
  if (inputSearch && inputSearch.value) {
    $('#tabel').DataTable().search(inputSearch.value).draw()
  }
  rpgAturTinggiTabel()
}

// Tab "Retur Beli". dataRefreshPenerimaan sudah baris datar (bukan dikelompokkan per
// NoBukti seperti dataRefreshOutstanding), jadi kolomnya langsung dibaca dari item itu
// sendiri.
function renderTabelRetur () {
  rpgAktifkanTabel(2)

  if ($.fn.DataTable.isDataTable('#tabelRetur')) {
    $('#tabelRetur').DataTable().destroy()
  }

  let cols = rpgKolomTampil(2)
  let kolomRender = cols.map(rpgKolomRender)

  let thead = document.getElementById('tabelRetur_header')
  thead.innerHTML = rpgHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }

  let rowTable = ''
  dataRefreshPenerimaan.forEach((item) => {
    let noBukti = item.NoBukti || ''
    let tombolAksi = `
      <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetailKoreksi('${noBukti}')"><i class="bi bi-info-lg"></i></button>
      <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${noBukti}')"><i class="bi bi-printer"></i></button>
    `
    rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
    kolomRender.forEach((c) => {
      rowTable += `<td>${rpgRenderNilai(c, item)}</td>`
    });
    rowTable += `</tr>`
  });

  // Baris "Tidak ada data" TIDAK ditulis manual - lihat catatan yang sama di
  // renderTabelOutstanding(). Diserahkan ke language.emptyTable di bawah.
  document.getElementById('tabelRetur_data').innerHTML = rowTable

  $('#tabelRetur').DataTable({
    lengthChange: false,
    pageLength: rpgPanjangHalaman[2],
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    },
    drawCallback: function () {
      setTimeout(rpgAturTinggiTabel, 0)
    }
  })

  rpgPindahBar(2)
  rpgIkatSearch(2)
  rpgIkatPanjangHalaman(2)
  rpgIkatPeriode2()
  let inputSearch = document.getElementById('rpgSearch2')
  if (inputSearch && inputSearch.value) {
    $('#tabelRetur').DataTable().search(inputSearch.value).draw()
  }
  rpgAturTinggiTabel()
}

// Satu endpoint mengembalikan data + konfigurasi kolom KEDUA tabel sekaligus (lihat
// ReturPembelianGudangController@loadAll) - tanggal dari kedua tab dikirim sekaligus,
// masing-masing dipakai memfilter tabelnya sendiri di server.
function loadAll () {
  // Idempotent - hanya benar-benar mengikat sekali seumur halaman.
  rpgInitReportTableSekali()

  let urutAktif = rpgUrutTabAktif()
  rpgPerluGambar[1] = (urutAktif !== 1)
  rpgPerluGambar[2] = (urutAktif !== 2)

  $.ajax({
    url: "{!! url('rpgloadAll') !!}",
    type: "get",
    async: false,
    data: {
      tglawal1: $('#rpgTglAwal1').val(),
      tglakhir1: $('#rpgTglAkhir1').val(),
      tglawal: $('#rpgTglAwal2').val(),
      tglakhir: $('#rpgTglAkhir2').val()
    },
    success: function (res) {
      console.log(res)
      dataRefreshOutstanding = res.outstandingArray || []
      dataRefreshPenerimaan = res.penerimaanArray || []
      rpgCart[1] = rpgBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, RPG_LABEL_1)
      rpgCart[2] = rpgBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, RPG_LABEL_2)
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data Retur Pembelian Gudang')
    }
  })

  if (urutAktif === 2) {
    renderTabelRetur()
  } else {
    renderTabelOutstanding()
  }
}

$('#nav-home-tab').on('shown.bs.tab', function () {
  rpgAktifkanTabel(1)
  rpgPindahBar(1)

  if (rpgPerluGambar[1]) {
    rpgPerluGambar[1] = false
    renderTabelOutstanding()
    return
  }

  if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
  rpgAturTinggiTabel()
})

$('#nav-profile-tab').on('shown.bs.tab', function () {
  rpgAktifkanTabel(2)
  rpgPindahBar(2)

  if (rpgPerluGambar[2]) {
    rpgPerluGambar[2] = false
    renderTabelRetur()
    return
  }

  if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
  rpgAturTinggiTabel()
})

// Layar diubah ukurannya (mis. resize jendela) - tinggi kotak tabel diukur ulang supaya
// tetap pas, didebounce supaya tidak menghitung ulang di setiap event resize.
let rpgTimerResize = null
$(window).on('resize', function () {
  if (rpgTimerResize) { clearTimeout(rpgTimerResize) }
  rpgTimerResize = setTimeout(rpgAturTinggiTabel, 150)
})

$(document).ready(function () {
  loadAll()
})


function submitPrint (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''

  const options = ['EVY YUSIA', 'JULIA', 'DESTI']

  const overlay = document.createElement('div')
  overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;'

  overlay.innerHTML = `
    <div style="background:#fff;padding:24px;border-radius:8px;min-width:320px;font-family:sans-serif;font-size:14px;">
      <h3 style="margin:0 0 16px;">Cetak Retur Pembelian Gudang</h3>
      <label style="display:block;margin-bottom:6px;">Ditandatangani oleh :</label>
      <select id="selectNamaTtd" style="width:100%;padding:6px;font-size:14px;border:1px solid #ccc;border-radius:4px;">
        <option value="">-- Pilih --</option>
        ${options.map(n => `<option value="${n}">${n}</option>`).join('')}
      </select>
      <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;">
        <button id="btnBatalTtd" style="padding:6px 16px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;">Batal</button>
        <button id="btnLanjutTtd" style="padding:6px 16px;background:#333;color:#fff;border:none;border-radius:4px;cursor:pointer;">Cetak</button>
      </div>
    </div>
  `

  document.body.appendChild(overlay)

  document.getElementById('btnBatalTtd').onclick = () => document.body.removeChild(overlay)

  document.getElementById('btnLanjutTtd').onclick = () => {
    namaTtdCetak = document.getElementById('selectNamaTtd').value
    document.body.removeChild(overlay)
  
    $.ajax({
      url: "{!! url('returpembeliangudangprint') !!}",
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

    if (!dataPrint || !dataPrint.length) {
      alertify.warning('Data cetak kosong')
      return
    }

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
                        <h2 class="m-0 pb-2" style='text-decoration:underline; color:blue;'>CV. SINAR MAHAKAM LESTARI</h2>
                      </div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">Surat Jalan Retur Gudang</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NOMOR</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoBukti}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">PO. NO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Nopesanan}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">INVOICE</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Noinvoice}</div>
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
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
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
        <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NAMABRG  }</td>
        <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.KodeBrg}</td>
        <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${itemSub.QNT ? formatAngka(itemSub.QNT) : ''}</td>
        <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 5%;">${itemSub.SATUAN}</td>
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
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>
         
          <hr style="margin-top: -6px" />

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>

         <span style="float: left; display: block; clear: left;">
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Hormat Kami,</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Penerima,</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
                  <p class="m-0" style="white-space: pre;">                             (                                    )</p>
                </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
         </div>


         <div class="footer-print-date" style='margin-bottom:-100px;'>
        <table class="m-0" style="width: 100% ; font-family: sans-serif;
        font-size: 10px ">
          <tr>
            <td class="no-border text-bold">- Permasalahan : Barang yang disupply tidak sesuai dengan permintaan user</td>
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
  } 




</script>




@endsection
