@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Invoice Pembelian')

@endsection
{{-- tampilan tampilan baru: tab custom, tabel .data-table, header interaktif - disamakan
     dengan resources/views/purchasing/newpo.blade.php. --}}
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

  /* ---------- Kolom Aksi tabel (#tabel/#tabel_pembelian) - tombol bulat kecil, warna
     pastel, disalin dari newpo.blade.php supaya seragam. ---------- */
  #tabel td:first-child:not([colspan]),
  #tabel_pembelian td:first-child:not([colspan]) {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #tabel td:first-child .btn,
  #tabel_pembelian td:first-child .btn {
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
  #tabel_pembelian td:first-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success,  #tabel_pembelian td:first-child .btn-success  { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
  #tabel td:first-child .btn-warning,  #tabel_pembelian td:first-child .btn-warning  { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
  #tabel td:first-child .btn-primary,  #tabel_pembelian td:first-child .btn-primary  { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
  #tabel td:first-child .btn-danger,   #tabel_pembelian td:first-child .btn-danger   { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
  #tabel td:first-child .btn-info,     #tabel_pembelian td:first-child .btn-info     { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

  /* Tombol Aksi baru muncul saat baris di-hover, sama seperti newpo.blade.php. */
  table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transition: opacity .12s ease;
  }
  table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* Dropdown "Tampilkan" (jumlah baris per halaman) & dropdown status otorisasi -
     tidak ada di po-table-header.css, ditulis di sini supaya perubahan cukup
     mengunggah file blade-nya saja. */
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

  /* ---------- Grid field modal Add/Detail (tab Outstanding Pembelian) - disalin dari
     newpobeliacc.blade.php supaya gaya modalnya seragam. ---------- */
  .pba-fgrid{display:flex;gap:0 20px;margin-bottom:16px;align-items:flex-start;}
  .pba-fcol{flex:1;min-width:0;display:flex;flex-direction:column;gap:10px;}
  .pba-fcol-wide{flex:2;}
  .pba-f{min-width:0;margin-bottom:0;}
  .pba-f label{display:block;font-size:11px;font-weight:700;letter-spacing:.03em;color:#6b7280;text-transform:uppercase;margin-bottom:4px;white-space:nowrap;}
  .pba-f .form-control,.pba-f select{width:100%;}
  .pba-f textarea.form-control{resize:none;}
  .pba-tgrid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px 16px;margin-top:14px;}

  /* Checkbox "Terima"/"Buat Invoice" di tabel modal Add - dibesarkan supaya lebih mudah diklik. */
  .pba-chk-lg{width:18px;height:18px;cursor:pointer;}

  /* ---------- Form Add/Detail/Edit (modal) - tombol chip, disalin dari newpo.blade.php. ---------- */
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

  .btn-chip-merah {
    background-color: #fde8e8;
    border-color: #f8c9c9;
    color: #dc2626;
  }

  .btn-chip-merah:hover,
  .btn-chip-merah:focus {
    background-color: #fbd5d5;
    border-color: #f5b5b5;
    color: #b91c1c;
  }

  .btn-chip-merah:active {
    background-color: #f8c9c9 !important;
    border-color: #f0a3a3 !important;
    color: #b91c1c !important;
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

  /* Modal filter (.rt-filter, mis. #modalFilterIpb) meniru purchaseOrder.blade.php
     apa adanya - termasuk tombol "x"-nya yang polos, TANPA kotak seperti modal-modal
     lain di halaman ini. Kembalikan ke gaya Bootstrap default di dalam .rt-filter saja. */
  .rt-filter .modal-header .close {
    border: none;
    border-radius: 0;
    background: transparent;
    color: #000;
    opacity: .5;
    width: auto;
    height: auto;
  }

  .rt-filter .modal-header .close:hover {
    background: transparent;
    border-color: transparent;
    opacity: .75;
  }

  /* DataTables (autoWidth) menulis lebar hasil ukurnya sebagai inline style pada <table>;
     kalau lebih sempit dari kotaknya, tabel jadi punya ruang kosong kiri-kanan. min-width
     (bukan width) supaya tabel yang memang lebih lebar tetap bisa digeser mendatar. */
  #tabel, #tabel_pembelian {
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
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />


  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
           aria-controls="nav-home" aria-selected="true">
          Outstanding Pembelian
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
           aria-controls="nav-profile" aria-selected="false">
          Kelengkapan Dokumen
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
                    <input type="date" class="po-filter-inp" id="ipbTglAwal1" value="{!! $ipbTglAwal !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="ipbTglAkhir1" value="{!! $ipbTglAkhir !!}">
                  </div>
                  <input type="search" id="ipbSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="ipbLen1">Tampilkan</label>
                    <select id="ipbLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
                {{-- Bar kolom tersembunyi + tombol "Reset kolom" (diisi report-table.js).
                     Satu elemen dipakai bersama #tabel dan #tabel_pembelian, dipindah lewat
                     JS (ipbPindahBar) saat tab berganti - lihat ipbInitReportTableSekali(). --}}
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
                    <input type="date" class="po-filter-inp" id="ipbTglAwal2" value="{!! $ipbTglAwal !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="ipbTglAkhir2" value="{!! $ipbTglAkhir !!}">
                  </div>
                  <input type="search" id="ipbSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="ipbLen2">Tampilkan</label>
                    <select id="ipbLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                  <button class="po-btn-filter" type="button" id="ipbBtnFilter" onclick="$('#modalFilterIpb').modal('show')">
                    <i class="bi bi-funnel"></i> Filter
                  </button>
                </div>
                {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat ipbPindahBar(). --}}
                <table id="tabel_pembelian" class="data-table po-aksi-hover">
                  <thead id="tabel_pembelian_header" class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_pembelian_data" class="text-left"></tbody>
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

<!-- start page detail tab kiri (out beli) -->
<div id="page3" class="container-fluid" style="display: none; background: #fff; padding-top: 15px; padding-bottom: 15px;" >
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left">
      <h2 id="detailModalLabelout" style="display:none;"></h2>
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

      <div class="modal-body">
        <div class="row">
          <input type="hidden" class="form-control" id="input_add_nourut">

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>No Bukti</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" id="detailPembelianNobuktiout" placeholder="" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Tanggal</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="date" class="form-control text-left" id="detailDateout" value="{!! date('Y-m-d') !!}" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Supplier</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Kode Pelanggan" id="detailPembelianKodeSuppout" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>No PO</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" placeholder="No PO" id="detailNoPOout" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Surat Jln</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Surat Jalan Supplier" id="detailFakturSuppout" disabled>
              </div></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Nama Supp</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Nama Pelanggan" id="detailPembelianSuppout" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Alamat</label></div>
              <div class="col-md-8"><div class="form-group">
                <textarea rows=2 placeholder="Alamat Pelanggan" class="form-control text-left" id="detailPembelianAlamatSuppout" disabled></textarea>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Gudang</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Gudang" id="detailgudangout" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>SO Cust</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" id="detailSoCustomerout" disabled>
              </div></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Valas</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control" id="detailPembelianvalasout" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Kurs</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control" id="detailPembeliankursout" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>TOP</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="number" class="form-control text-left" id="detailPembelianhariout" value=0 min=0 disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>No/Sopir</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" id="detailNoSopirout" disabled>
              </div></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Bayar</label></div>
              <div class="col-md-8"><div class="form-group">
                <select onchange="onChangeInputAddPembayaran()" id="detailPembeliantipebayarout" class="form-control form-select-lg text-center" aria-label=".form-select-lg example" disabled>
                  <option value=0 selected>Tunai</option>
                  <option value=1>Kredit</option>
                </select>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Jth Tempo</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="date" class="form-control text-center" id="detailPembelianJthTempoout" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()" disabled>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>PPN</label></div>
              <div class="col-md-8"><div class="form-group">
                <select onchange="onChangeTipePPN()" id="detailPembeliantipeppnout" class="form-control text-center form-select-lg" aria-label=".form-select-lg example" disabled>
                  <option value=0 selected>None</option>
                  <option value=1>Exclude</option>
                  <option value=2>Include</option>
                </select>
              </div></div>
            </div>
            <div class="row">
              <div class="col-md-4" style="margin-top:5px;"><label>Uang Muka</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="number" class="form-control text-left" id="detailNuangmukaout" disabled>
              </div></div>
            </div>
            <div class="row" hidden>
              <div class="col-md-4" style="margin-top:5px;"><label>No Uang Muka</label></div>
              <div class="col-md-8"><div class="form-group">
                <input type="text" class="form-control text-left" id="detailNoUangMukaout" disabled>
              </div></div>
            </div>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-12">
            <table id="detailTableout" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Qty</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Qty PO</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Harga</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Disc</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Subtotal</th>
                </tr>
              </thead>
              <tbody id="detailTableDataout" class="text-left">
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

        <div class="row mt-4">
          <div class="col-md-2 col-4">
            <div class="form-group">
              <label>Disc %</label>
              <input type="number" class="form-control text-right" id="input_det_discout" onblur="onChangeInputAddDisc()" value="0.00" disabled>
            </div>
          </div>
          <div class="col-md-2 col-4">
            <div class="form-group">
              <label>DiscRp</label>
              <input type="number" class="form-control text-right" id="input_det_discrpout" onblur="onChangeInputAddDiscRp()" value="0.00" disabled>
            </div>
          </div>
          <div class="col-md-2 col-4">
            <div class="form-group">
              <label>DPP</label>
              <input type="text" class="form-control text-right" id="input_det_dppout" value="0.00" disabled>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="form-group">
              <label>PPN</label>
              <input type="text" class="form-control text-right" id="input_det_ppnout" value="0.00" disabled>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="form-group">
              <label>Grand Total</label>
              <input type="text" class="form-control text-right" id="input_det_grandtotalout" value="0.00" disabled>
            </div>
          </div>
        </div>
      </div>

      <!-- Commented out footer button
      <div class="modal-footer">
        <button type="button" id="btnotokiri" class="btn btn-primary" style="height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitOtorisasi1()">Approve</button>
      </div>
      -->
</div>{{-- /#formBsGrid --}}
</div>
    <!-- End page detail Informasi-->





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

        <div class="pba-fgrid">
          <div class="pba-fcol">
            <div class="pba-f">
              <label>No Bukti</label>
              <input type="text" class="form-control" id="input_add_nobukti" placeholder="Surat Jln" disabled>
            </div>
            <div class="pba-f">
              <label>Tanggal</label>
              <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Nomor PO</label>
              <input type="text" class="form-control" id="input_add_nomorpo" placeholder="Nomor PO" required disabled>
            </div>
            <div class="pba-f">
              <label>Gudang</label>
              <select id="input_add_gudang" class="form-control" aria-label=".form-select-lg example">
                @foreach ($gudang as $g)
                <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} {{ $g->NAMA }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Surat Jln Supp</label>
              <input type="text" autocomplete="off" class="form-control" id="input_add_suratjalansupp" placeholder="Surat Jln" required>
            </div>
            <div class="pba-f">
              <label>No. Kend / Sopir</label>
              <input type="text" autocomplete="off" class="form-control" id="input_add_nokend" placeholder="No Kend / Sopir" required>
            </div>
          </div>
        </div>

        <div class="container-fluid p-0" style="overflow-x: auto;">
          <table id="addTable" class="table table-bordered table-striped">
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

            <tbody id="addTableData" class="text-left">
              <tr>
                <td class="text-center"><input class="pba-chk-lg" type="checkbox" value="" id="flexCheckDefault"></td>
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
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
<!-- End modal add-->




<!-- start modal editpembelian  ADD -->
<div class="modal fade" id="editPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="editPembelianModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" name="noUrut" id="editnoUrut" value="{!! csrf_token() !!}" />

        <div class="pba-fgrid">
          <div class="pba-fcol">
            <div class="pba-f">
              <label>No Bukti</label>
              <input type="text" class="form-control" id="editnobukti" placeholder="Nomor Bukti" disabled>
            </div>
            <div class="pba-f">
              <label>No. Invoice</label>
              <input type="text" class="form-control" id="editnoinvoice" placeholder="No. Invoice">
            </div>
            <div class="pba-f">
              <label>Tgl Invoice</label>
              <input type="date" class="form-control text-center" id="edittglinvoice" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Supplier</label>
              <input type="text" class="form-control" id="editPembelianSupp" placeholder="Supplier" disabled>
            </div>
            <div class="pba-f">
              <label>No. Pajak</label>
              <input type="text" class="form-control" id="editnopajak" placeholder="No. Pajak">
            </div>
            <div class="pba-f">
              <label>Tgl Pajak</label>
              <input type="date" class="form-control text-center" id="edittglfpajak" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Tanggal</label>
              <input type="date" class="form-control text-center" id="editPembelianDate" value="{!! date('Y-m-d') !!}">
            </div>
            <div class="pba-f">
              <label>No B.Potong</label>
              <input type="text" class="form-control" id="editnobpotong" placeholder="No. Bukti Potong">
            </div>
            <div class="pba-f">
              <label>Tgl B.Potong</label>
              <input type="date" class="form-control text-center" id="edittglbpotong" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>PPN</label>
              <input type="text" class="form-control" id="editPembeliantipeppn" placeholder="Tipe PPN" disabled>
            </div>
          </div>
        </div>


        <!-- ADD ITEM FORM -->
        <div id="formPembelianAdd" class="container-fluid showhide">
          <div class="line"></div>
          <div class="row">
            <div class="col-12">
              <h4>Add Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Pilih Barang</label>
                <select onchange="changeSelectBarang()" id="editPembelianAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                </select>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Kode Barang</label>
                <input id="editPembelianInputAddKode" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Nama Barang</label>
                <input id="editPembelianInputAddNamaBarang" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty</label>
                <input id="editPembelianInputAddQty" type="number" value="0.00" class="form-control">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Satuan</label>
                <input type="text" id="editPembelianInputAddSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty POx</label>
                <input id="editPembelianInputAddQtyPO" type="number" value="0.00" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty OS</label>
                <input id="editPembelianInputAddQtyOS" type="number" value="0.00" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()">Batal</button>
                <button type="button" onclick="submitPembelianAdd()" class="btn btn-chip-biru">Add Item</button>
              </div>
            </div>
          </div>
          <div class="line"></div>
        </div>

        <!-- EDIT ITEM FORM -->
        <div id="formPembelianEdit" class="container-fluid showhide">
          <div class="line"></div>
          <div class="row">
            <div class="col-12">
              <h4>Edit Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Kode Barang</label>
                <input id="editPembelianInputEditKode" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Nama Barang</label>
                <input id="editPembelianInputEditNamaBarang" type="text" class="form-control" disabled>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty</label>
                <input id="editPembelianInputEditQty" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Satuan</label>
                <input type="text" id="editPembelianInputEditSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Harga</label>
                <input id="editPembelianInputEditHarga" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>DISC %</label>
                <input id="editPembelianInputEditDisc" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Disc Rp</label>
                <input id="editPembelianInputEditDiscRp" type="number" value="0.00" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Qty POz</label>
                <input id="editPembelianInputEditQtyPO" type="number" value="0.00" class="form-control" disabled>
              </div>
            </div>
          </div>

          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitPembelianEdit()">Edit Item</button>
              </div>
            </div>
          </div>
          <div class="line"></div>
        </div>

        <!-- TABLE -->
        <div class="container-fluid mt-3" style="overflow:auto;">
          <table id="editPembelianTable" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Buat Invoice</th>
                <th style="padding: 4px 12px;" scope="col">No Pembelian</th>
                <th style="padding: 4px 12px;" scope="col" class="text-center">Tgl Pembelian</th>
                <th style="padding: 4px 12px;" scope="col">NOPO</th>
                <th style="padding: 4px 12px;" scope="col" class="text-center">Tgl PO</th>
              </tr>
            </thead>
            <tbody id="editPembelianTableData" class="text-left">
              <tr>
                <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="container-fluid mt-2">
          <div class="row">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-chip-biru" onclick="saveKetFaktur()">Submit</button>
            </div>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal editpembelian-->




<!-- //TAB KIRI -->


<!-- start modal detail INFORMASI-->
<div class="modal fade" id="detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document"  style="min-width: 1500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>




      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">




          <div class="col-4">
            <div class="form-group">
              <!-- <label>Tanggal</label> -->
              <input type="date" class="form-control text-center" id="detailDate" value="{!! date('Y-m-d') !!}"  >
              <input type="text" class="form-control" id="detailSupp" placeholder="Supplier" disabled>
            </div>
          </div>

<!-- DARI SINI -->

          <div class="col-md-3">
          <div class="row">

                  <div class="col-4">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>

                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="detailvalas" placeholder="valas" disabled>
                    </div>
                  </div>


                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="detailtipeppn" placeholder="tipe ppn" disabled>
                    </div>
                  </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group">
                        <label>Kurs</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailkurs" placeholder="kurs" disabled>
                      </div>
                    </div>

                    <div class="col-4">
                      <div class="form-group">
                        <label>Tipe Bayar</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                          <input type="text" class="form-control" id="detailtipebayar" placeholder="tipe bayar" disabled>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-3">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group">
                        <label>NO SO</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailSoCustomer" placeholder="NO SO" disabled>
                      </div>
                    </div>

                    <div class="col-4">
                      <div class="form-group">
                        <label>UMB</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <!-- <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  > -->
                          <input type="text" class="form-control" id="detailNoUangMuka" placeholder="No Uang Muka" disabled>
                      </div>
                    </div>
                  </div>
                </div>



                <div class="col-md-3">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group">
                        <label>NUM</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <!-- <input type="date" class="form-control text-center" id="input_add_tanggalso" value="{!! date('yyyy-mm-dd') !!}" disabled > -->
                        <input type="text" class="form-control" id="detailNuangmuka" placeholder="N UM" disabled>
                      </div>
                    </div>

                    <div class="col-4">
                      <div class="form-group">
                        <label>PPN %</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <!-- <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  > -->
                          <input type="text" class="form-control" id="detailNilaiPPN" placeholder="11/12" disabled>
                      </div>
                    </div>
                  </div>
                </div>




                  <!-- SAMPAI SINI -->

                  <div class="col-md-3">
                  <div class="row">

                    <div class="col-4">
                      <div class="form-group">
                        <label>NO PO</label>
                      </div>
                    </div>

                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailNoPO" placeholder="NOPO" disabled>
                      </div>
                    </div>


                    <div class="col-4">
                      <div class="form-group">
                        <label>Gudang</label>
                      </div>
                    </div>
                    <div class="col-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="detailgudang" placeholder="Gudang" disabled>
                      </div>
                    </div>

                </div>
                </div>


                <div class="col-md-3">
                <div class="row">

                  <div class="col-4">
                    <div class="form-group">
                      <label>Sopir</label>
                    </div>
                  </div>

                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="detailNoSopir" placeholder="Nopol/Sopir" disabled>
                    </div>
                  </div>
              </div>
              </div>



              <div class="row">
                <div class="col-12">
                  <h5 id="">Keterangan</h5>
                </div>
                <div class="col-12">
                  <input type="text" autocomplete="off" class="form-control" id="detailKeterangan" >
                </div>
                <div class="col-12">
                  <h5 id="">FakturSupp</h5>
                </div>
                <div class="col-12">
                  <input type="text" autocomplete="off" class="form-control" id="detailFakturSupp" >
                </div>
              </div>



          </div>
          </div>
        </div>
      </div>{{-- /#formBsGrid --}}






        <div class="container-fluid" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>

                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty PO</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Disc</th>
                    <th scope="col">Subtotal</th>f


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
        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="submitOtorisasi1()"  >Otorisasi</button>

                  </div>
    </div>
  </div>
</div>
</div>
<!-- End modal detail pembelian-->

<!-- //TAB KANAN -->

<!-- start modal detail -->
<div class="modal fade" id="detailPembelian" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPembelianModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid">
      <div class="modal-body">
        <div class="pba-fgrid">
          <div class="pba-fcol">
            <div class="pba-f">
              <label>No Bukti</label>
              <input type="text" class="form-control" id="editnobukti2" placeholder="Nomor Bukti" disabled>
            </div>
            <div class="pba-f">
              <label>No. Invoice</label>
              <input type="text" class="form-control" id="editnoinvoice2" placeholder="No. Invoice" disabled>
            </div>
            <div class="pba-f">
              <label>Tgl Invoice</label>
              <input type="date" class="form-control text-center" id="edittglinvoice2" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Supplier</label>
              <input type="text" class="form-control" id="editPembelianSupp2" placeholder="Supplier" disabled>
            </div>
            <div class="pba-f">
              <label>No. Pajak</label>
              <input type="text" class="form-control" id="editnopajak2" placeholder="No. Pajak" disabled>
            </div>
            <div class="pba-f">
              <label>Tgl Pajak</label>
              <input type="date" class="form-control text-center" id="edittglfpajak2" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Tanggal</label>
              <input type="date" class="form-control text-center" id="editPembelianDate2" value="{!! date('Y-m-d') !!}" disabled>
            </div>
            <div class="pba-f">
              <label>No B.Potong</label>
              <input type="text" class="form-control" id="editnobpotong2" placeholder="No. Bukti Potong" disabled>
            </div>
            <div class="pba-f">
              <label>Tgl B.Potong</label>
              <input type="date" class="form-control text-center" id="edittglbpotong2" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>PPN</label>
              <input type="text" class="form-control" id="editPembeliantipeppn2" placeholder="Tipe PPN" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-3" style="overflow:auto;">
          <table id="editPembelianTable2" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">No Pembelian</th>
                <th style="padding: 4px 12px;" scope="col">Gudang</th>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Total</th>
              </tr>
            </thead>
            <tbody id="editPembelianTableData2" class="text-left">
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
      </div>{{-- /#formBsGrid --}}
    </div>
  </div>
</div>
<!-- End modal detail-->


<!-- start modal edit kelengkapan dokumen -->
<div class="modal fade" id="editKelengkapan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl modal-dialog-centered" role="document" style="max-width: 95%;">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="editKelengkapanModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="formBsGrid">
      <div class="modal-body">
        <div class="pba-fgrid">
          <div class="pba-fcol">
            <div class="pba-f">
              <label>No Bukti</label>
              <input type="text" class="form-control" id="editnobukti3" placeholder="Nomor Bukti" disabled>
            </div>
            <div class="pba-f">
              <label>No. Invoice</label>
              <input type="text" class="form-control" id="editnoinvoice3" placeholder="No. Invoice">
            </div>
            <div class="pba-f">
              <label>Tgl Invoice</label>
              <input type="date" class="form-control text-center" id="edittglinvoice3" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Supplier</label>
              <input type="text" class="form-control" id="editPembelianSupp3" placeholder="Supplier" disabled>
            </div>
            <div class="pba-f">
              <label>No. Pajak</label>
              <input type="text" class="form-control" id="editnopajak3" placeholder="No. Pajak">
            </div>
            <div class="pba-f">
              <label>Tgl Pajak</label>
              <input type="date" class="form-control text-center" id="edittglfpajak3" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>Tanggal</label>
              <input type="date" class="form-control text-center" id="editPembelianDate3" value="{!! date('Y-m-d') !!}" disabled>
            </div>
            <div class="pba-f">
              <label>No B.Potong</label>
              <input type="text" class="form-control" id="editnobpotong3" placeholder="No. Bukti Potong">
            </div>
            <div class="pba-f">
              <label>Tgl B.Potong</label>
              <input type="date" class="form-control text-center" id="edittglbpotong3" value="{!! date('Y-m-d') !!}">
            </div>
          </div>

          <div class="pba-fcol">
            <div class="pba-f">
              <label>PPN</label>
              <input type="text" class="form-control" id="editPembeliantipeppn3" placeholder="Tipe PPN" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-3" style="overflow:auto;">
          <table id="editKelengkapanTable3" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Action</th>
                <th style="padding: 4px 12px;" scope="col">No Pembelian</th>
                <th style="padding: 4px 12px;" scope="col">Gudang</th>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Total</th>
              </tr>
            </thead>
            <tbody id="editKelengkapanTableData3" class="text-left">
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
      </div>{{-- /#formBsGrid --}}

      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" onclick="edittransaksi()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit kelengkapan dokumen-->
<!-- End modal detail-->


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
        <!-- <h1>Tes Modal</h1> -->
        <!-- <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" /> -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

                <h5 id="">No PO</h5>
            </div>
            <div class="col-12">

                <input type="text" class="form-control" id="printPembelianNoPO" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h5 id="">Supplier</h5>
            </div>
            <div class="col-12">
              <input type="text" class="form-control" id="printPembelianSupp" disabled>
            </div>
            <div class="col-12">

              <h5 id="">Tanggal</h5>
            </div>
            <div class="col-12">

                <input type="date" class="form-control" id="printPembelianDate" value="{!! date('Y-m-d') !!}" disabled >
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h5 id="">Keterangan</h5>
            </div>
            <div class="col-12">
              <input type="text" class="form-control" id="printPembelianFakturSupp" autocomplete="off" disabled>
            </div>
            <div class="col-12">
              <h5 id="">FakturSupp</h5>
            </div>
            <div class="col-12">
              <input type="text" class="form-control" id="printPembelianKeterangan" autocomplete="off" disabled>
            </div>

          </div>
        </div>
        <div class="container-fluid mt-2">

      </div>

        <div class="container-fluid" style="overflow:auto;">

              <table id="printPembelianTable" class="table table-bordered table-striped mt-5"  >
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
            <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
          </div>
        </div>
    </div>
  </div>
</div>
</div>
<!-- End modal editpembelian-->

<!-- modal filter status Transaksi Kelengkapan Dokumen -->
<div class="modal fade rt-filter" id="modalFilterIpb">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter
          <span class="rt-active-badge" id="ipbFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterIpb').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="ipbModalStatus">Status</label>
              <select class="rt-native" id="ipbModalStatus">
                <option value="">Semua</option>
                <option value="0">Belum Otorisasi</option>
                <option value="1">Sudah Otorisasi</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="ipbResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterIpb').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="ipbTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter status Transaksi Kelengkapan Dokumen -->

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


  <!-- start page tab kiri detail INFORMASI-->
 <!-- detail informasi tab kiri/out beli -->
  <!-- start modal tab kiri detail INFORMASI-->




@endsection

@section('js')
{{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + tombol
     "Reset kolom"), disamakan dengan newpo.blade.php. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
  <script type="text/javascript">

    let row_id = "";
    let action = "";
    let row_data = {};
    let DataNobukti = "";
    let listDataEdit = [];

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

    // Tanggal dari beberapa endpoint punya nama kolom berbeda (Tanggal vs TANGGAL)
    // tergantung view SQL Server sumbernya - beberapa titik pembacaan tanggal di file ini
    // sebelumnya salah membaca casing kolomnya sehingga modal menampilkan "Invalid Date".
    // Guard ini mencoba kedua casing supaya aman dipakai di mana pun sumber datanya.
    function ipbAmbilTanggal (row) {
      if (!row) { return null }
      let v = row.Tanggal || row.TANGGAL
      if (!v) { return null }
      let d = new Date(v)
      return isNaN(d.getTime()) ? null : d
    }

    function ipbTanggalYMD (d) {
      if (!d) { return '' }
      let day = ('0' + d.getDate()).slice(-2)
      let month = ('0' + (d.getMonth() + 1)).slice(-2)
      return d.getFullYear() + '-' + month + '-' + day
    }

    // Kolom "Tgl Pembelian"/"Tgl PO" di tabel Buat Invoice mengambil tanggal apa adanya
    // dari API (mis. "2026-07-17 00:00:00.000") - potong jamnya, tampilkan tanggal saja.
    function ipbTglSaja (v) {
      if (!v) { return '' }
      let d = new Date(v)
      return isNaN(d.getTime()) ? v : ipbTanggalYMD(d)
    }

    function detailPembelian1(index) {
      let tempDataDetailPembelian = dataRefreshPembelian[index]
      if (tempDataDetailPembelian) { detailPembelian(tempDataDetailPembelian.NoBukti) }
    }

// informasi pembelian KANAN

      function detailPembelian (nobukti) {


      let _token = $("#_token").val();
      let table_pembelian_row_detail =[]



      $.ajax({
        url: "{!! url('detailInvoiceBeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {

          console.log(res,'res')

          table_pembelian_row_detail = res
          console.log(res[0])
          // console.log(res[0][0])
          // console.log(res[0][0].pQC)

        }
      })

      dataLPB =  table_pembelian_row_detail[0]
      dataEditPembelianEdit = table_pembelian_row_detail


       let date = new Date(dataLPB.Tanggal);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
       var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;


       let dateinvoice = new Date(dataLPB.TglInvoice);
      var dayinvoice = ("0" + dateinvoice.getDate()).slice(-2);
      var monthinvoice = ("0" + (dateinvoice.getMonth() + 1)).slice(-2);
       var date1invoice = dateinvoice.getFullYear()+"-"+(monthinvoice)+"-"+(dayinvoice) ;

       let datefpajak = new Date(dataLPB.TglFakturPajak);
      var dayfpajak = ("0" + datefpajak.getDate()).slice(-2);
      var monthfpajak = ("0" + (datefpajak.getMonth() + 1)).slice(-2);
       var date1fpajak = datefpajak.getFullYear()+"-"+(monthfpajak)+"-"+(dayfpajak) ;



       let datebpotong = new Date(dataLPB.TglBuktiPotong);
      var daybpotong = ("0" + datebpotong.getDate()).slice(-2);
      var monthbpotong = ("0" + (datebpotong.getMonth() + 1)).slice(-2);
       var date1bpotong = datefpajak.getFullYear()+"-"+(monthbpotong)+"-"+(daybpotong) ;



       let table_row_detail_inf = ""


console.log(table_pembelian_row_detail)

       table_pembelian_row_detail.forEach((detail_row, i) => {
         console.log('DATADETAIL',detail_row)
   console.log(detail_row.KODEBARANG,'xb',i)
         table_row_detail_inf += `<tr>
         <td>${detail_row.NoBeli}</td>
         <td>${detail_row.KodeGdg}</td>
         <td>${detail_row.KODEBARANG}</td>
         <td>${detail_row.NAMABARANG}</td>
         <td class="text-right">${detail_row.QNT ? formatAngkaX(detail_row.QNT) : '0.00' }</td>

         <td>${detail_row.SATUAN}</td>
         <td class="text-right">${detail_row.HARGA ? formatAngkaX(detail_row.HARGA) : '0.00' }</td>
          <td class="text-right">${detail_row.NNET ? formatAngkaX(detail_row.NNET) : '0.00' }</td>


         </tr>`


      });





      let fakturSupp = ""
      let keterangan = ""
      if (dataLPB.NoPO) {
        fakturSupp = dataLPB.NoPO
      }
      if (dataLPB.KETERANGAN) {
        keterangan = dataLPB.KETERANGAN
      }
      document.getElementById("detailPembelianModalLabel").innerHTML = dataLPB.NoBukti;




      document.getElementById("editnobukti2").value = dataLPB.NoBukti;
      document.getElementById("editPembelianSupp2").value = dataLPB.NamaCustSupp;
      document.getElementById("editPembelianDate2").value = date1;

      document.getElementById("editPembeliantipeppn2").value = dataLPB.myppn;
      document.getElementById("edittglinvoice2").value = date1invoice;
      document.getElementById("edittglfpajak2").value = date1fpajak;
      document.getElementById("edittglbpotong2").value = date1bpotong;
      document.getElementById("editnoinvoice2").value = dataLPB.NoInvoice;
      document.getElementById("editnopajak2").value = dataLPB.NoFakturPajak;
      document.getElementById("editnobpotong2").value = dataLPB.NoBuktiPotong;




      $("#detailPembelianDate").val(date1);
      $("#detailPembelian").modal('toggle');
console.log('vvvvvvvvvvvvvvvvvv')
      document.getElementById("editPembelianTableData2").innerHTML = table_row_detail_inf


    }




    function editKelengkapan (nobukti) {

      let _token = $("#_token").val();
      let table_pembelian_row_detail = []

      $.ajax({
        url: "{!! url('detailInvoiceBeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {
          table_pembelian_row_detail = res
        }
      })

      let dataEdit = table_pembelian_row_detail[0]

      let date = new Date(dataEdit.Tanggal);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

      let dateinvoice = new Date(dataEdit.TglInvoice);
      var dayinvoice = ("0" + dateinvoice.getDate()).slice(-2);
      var monthinvoice = ("0" + (dateinvoice.getMonth() + 1)).slice(-2);
      var date1invoice = dateinvoice.getFullYear()+"-"+(monthinvoice)+"-"+(dayinvoice) ;

      let datefpajak = new Date(dataEdit.TglFakturPajak);
      var dayfpajak = ("0" + datefpajak.getDate()).slice(-2);
      var monthfpajak = ("0" + (datefpajak.getMonth() + 1)).slice(-2);
      var date1fpajak = datefpajak.getFullYear()+"-"+(monthfpajak)+"-"+(dayfpajak) ;

      let datebpotong = new Date(dataEdit.TglBuktiPotong);
      var daybpotong = ("0" + datebpotong.getDate()).slice(-2);
      var monthbpotong = ("0" + (datebpotong.getMonth() + 1)).slice(-2);
      var date1bpotong = datebpotong.getFullYear()+"-"+(monthbpotong)+"-"+(daybpotong) ;

      let table_row_detail_inf = ""
      table_pembelian_row_detail.forEach((detail_row) => {
        table_row_detail_inf += `<tr>
        <td class="text-center">
        <button class="btn btn-chip-merah btn-sm" type="button" onclick="hapustransaksi(${detail_row.KODEBARANG})"><i class="bi bi-trash"></i></button>
        </td>
        <td>${detail_row.NoBeli}</td>
        <td>${detail_row.KodeGdg}</td>
        <td>${detail_row.KODEBARANG}</td>
        <td>${detail_row.NAMABARANG}</td>
        <td class="text-right">${detail_row.QNT ? formatAngkaX(detail_row.QNT) : '0.00' }</td>
        <td>${detail_row.SATUAN}</td>
        <td class="text-right">${detail_row.HARGA ? formatAngkaX(detail_row.HARGA) : '0.00' }</td>
        <td class="text-right">${detail_row.NNET ? formatAngkaX(detail_row.NNET) : '0.00' }</td>
        </tr>`
      });

      document.getElementById("editKelengkapanModalLabel").innerHTML = dataEdit.NoBukti;
      document.getElementById("editnobukti3").value = dataEdit.NoBukti;
      document.getElementById("editPembelianSupp3").value = dataEdit.NamaCustSupp;
      document.getElementById("editPembelianDate3").value = date1;
      document.getElementById("editPembeliantipeppn3").value = dataEdit.myppn;
      document.getElementById("edittglinvoice3").value = date1invoice;
      document.getElementById("edittglfpajak3").value = date1fpajak;
      document.getElementById("edittglbpotong3").value = date1bpotong;
      document.getElementById("editnoinvoice3").value = dataEdit.NoInvoice;
      document.getElementById("editnopajak3").value = dataEdit.NoFakturPajak;
      document.getElementById("editnobpotong3").value = dataEdit.NoBuktiPotong;
      document.getElementById("editKelengkapanTableData3").innerHTML = table_row_detail_inf

      $("#editKelengkapan").modal('toggle');
    }

    function edittransaksi () {

      let nobukti = document.getElementById("editnobukti3").value ;
      let tglfpajak=document.getElementById("edittglfpajak3").value ;
      let tglinvoice=document.getElementById("edittglinvoice3").value ;
      let tglbpotong=document.getElementById("edittglbpotong3").value ;
      let nopajak=document.getElementById("editnopajak3").value ;
      let noinvoice=document.getElementById("editnoinvoice3").value ;
      let nobuktipotong=document.getElementById("editnobpotong3").value ;

      let _token = $("#_token").val();


      $.ajax({
        url: "{!! url('sp_edittransaksi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          tglfpajak,
          tglbpotong,
          tglinvoice,
          nopajak,
          nobuktipotong,
          noinvoice



        },
        success: function(res) {
          if (res > 0) {
            loadAll()

            alertify.success('Berhasil update kelengkapan dokumen')
            $("#editKelengkapan").modal('toggle')
          } else {
            alertify.warning('Dokumen sudah diotorisasi, tidak bisa diubah')
          }


      },
      error: function (err) {
        alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
      }
    })
    }


    function hapustransaksi () {




        alertify.confirm('Hapus Menu', 'Apakah yakin ingin menghapus data ?',
      function(){


                     let nobukti = document.getElementById("editnobukti2").value ;;
                    console.log(nobukti)
                    let _token = $("#_token").val();

                    $.ajax({
                      url: "{!! url('sp_hapusinvoice') !!}",
                      type: "post",
                      async: false,
                      data: {
                        _token,
                        nobukti

                      },
                      success: function(res) {
                        if (res > 0) {
                          console.log(nobukti)
                          loadAll()

                          alertify.success('Berhasil Hapus Data')
                          $("#detailPembelian").modal('toggle')
                        }


                    },
                    error: function (err) {
                      alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
                    }
                    })

         }
      ,function(){});

    }


    function tesModal() {
      //
      // $("#tes1234").modal('toggle');
      $('#formPembelianAdd').show();
    }

      function submitOtorisasi1 (nobukti) {
  console.log('=============================')
        // let nobukti = DataNobukti;
        let otorisasi = 1
        console.log(nobukti, otorisasi,'----t')
        let _token = $("#_token").val();


        $.ajax({
          url: "{!! url('spotorisasiInvoiceBeli') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
            otorisasi
          },
          success: function(res) {
            if (res > 0) {
              console.log(nobukti, otorisasi)
              loadAll()

              alertify.success('Berhasil update otorisasi')
              // $("#formOtorisasi1").modal('toggle')
            }


        },
        error: function (err) {
          alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
        }
      })
      }


      function submitUnOtorisasi1 (nobukti, isOtorisasi) {
        // Sementara dinonaktifkan sampai izin ISBATAL untuk menu Invoice Pembelian
        // diaktifkan lewat Berkas > Set Pemakai. Aktifkan kembali blok ini setelah itu.
        // let akses = $("#akses_isbatal").val();
        // if (!Number(akses)) {
        //   alertify.warning('No access');
        //   return;
        // }

        if (Number(isOtorisasi) === 0) {
          alertify.warning('Belum diotorisasi');
          return;
        }

        alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
          function (evt, value) {
            let xpket = value;

            if (xpket == '') {
              alertify.warning('Keterangan harus diisi.');
              return;
            }

            let _token = $("#_token").val();

            $.ajax({
              url: "{!! url('spUnotorisasiInvoiceBeli') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                nobukti,
                otorisasi: 0,
                pket: value
              },
              success: function (res) {
                if (res > 0) {
                  loadAll()
                  alertify.success('Berhasil batal otorisasi');
                } else {
                  alertify.warning('Gagal batal otorisasi');
                }
              },
              error: function (err) {
                alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
              }
            })
          },
          function () {
            alertify.error("Action cancelled");
          }
        );
      }

      function buttonUnOtoPembelian(nobukti) {
        console.log(nobukti,  "<<< detail")
        console.log('================')
        console.log(nobukti)


        let _token = $("#_token").val();
        let table_pembelian_row_detail =[]
        DataNobukti = nobukti


        $.ajax({
          url: "{!! url('detailPembelianACC') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoBukti: nobukti
          },
          success: function(res) {
              console.log('======xxxxx==========')
            console.log(res)

            table_pembelian_row_detail = res[0]
            console.log(res[0])
            // console.log(res[0][0])
            // console.log(res[0][0].pQC)

          }
        })


        dataLPB =  table_pembelian_row_detail[0]
        dataEditPembelianEdit = table_pembelian_row_detail

         let date = ipbAmbilTanggal(table_pembelian_row_detail[0]);
         let date1 = ipbTanggalYMD(date);

         let table_row_detail_inf = ""

        table_pembelian_row_detail.forEach((detail_row, i) => {
    console.log(detail_row.KodeBrg)
           table_row_detail_inf += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${formatAngka(detail_row.Qnt)}</td><td class="text-right">${formatAngka(detail_row.QNTPO)}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td></tr>`

        });


        let fakturSupp = ""
        let keterangan = ""
        if (table_pembelian_row_detail[0].FAKTURSUPP) {
          fakturSupp = table_pembelian_row_detail[0].FAKTURSUPP
        }
        if (table_pembelian_row_detail[0].KETERANGAN) {
          keterangan = table_pembelian_row_detail[0].KETERANGAN
        }
        document.getElementById("detailPembelianModalLabel").innerHTML = table_pembelian_row_detail[0].NoBukti;


        // document.getElementById("detailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier
        // document.getElementById("detailPembelianFakturSupp").value = fakturSupp
        // document.getElementById("detailPembelianKeterangan").value =  keterangan
        // document.getElementById("detailPembelianNoPO").value = table_pembelian_row_detail[0].NoPO
        // document.getElementById("detailPembeliangudang").value = table_pembelian_row_detail[0].NAMAGUDANG



        document.getElementById("detailPembelianSupp").value = table_pembelian_row_detail[0].NamaSupplier;

        document.getElementById("detailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS;


        document.getElementById("detailPembelianNoPO").value = table_pembelian_row_detail[0].NoPO
        document.getElementById("detailPembelianFakturSupp").value = table_pembelian_row_detail[0].FAKTURSUPP
        document.getElementById("detailPembelianKeterangan").value = table_pembelian_row_detail[0].KETERANGAN
        ///document.getElementById("detailTableData").innerHTML = table_row_detail



        document.getElementById("detailPembelianvalas").value = table_pembelian_row_detail[0].KODEVLS
        document.getElementById("detailPembeliankurs").value = table_pembelian_row_detail[0].KURS

        document.getElementById("detailPembeliantipeppn").value = table_pembelian_row_detail[0].MyPPN
        document.getElementById("detailPembeliantipebayar").value = table_pembelian_row_detail[0].MyTipeBayar

        document.getElementById("detailPembelianSoCustomer").value = table_pembelian_row_detail[0].NOSO
        document.getElementById("detailPembelianNoUangMuka").value = table_pembelian_row_detail[0].NOUMK

        document.getElementById("detailPembelianNuangmuka").value = table_pembelian_row_detail[0].NuangMuka
        document.getElementById("detailPembelianNilaiPPN").value = table_pembelian_row_detail[0].NilaiPPN

        document.getElementById("detailPembeliangudang").value = table_pembelian_row_detail[0].NAMAGUDANG
        // document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP

        document.getElementById("detailPembelianNoSopir").value = table_pembelian_row_detail[0].SOPIR







        $("#detailPembelianDate").val(date1);
        $("#detailPembelian").modal('toggle');
    console.log('vvvvvvvvvvvvvvvvvv')
        document.getElementById("detailPembelianTableData").innerHTML = table_row_detail_inf


      }

    function showPembelianAdd() {
      let akses = $("#akses_istambah").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      $('.showhide').hide();
      console.log(dataEditPembelianAddIndex , "?")
      dataEditPembelianAddIndex = ""
      console.log(dataEditPembelianAddIndex , "!")

      document.getElementById("editPembelianAddSelect").value = ""
      document.getElementById("editPembelianInputAddKode").value = ""
      document.getElementById("editPembelianInputAddNamaBarang").value = ""
      document.getElementById("editPembelianInputAddQtyPO").value = "0.00"
      document.getElementById("editPembelianInputAddSatuan").value = ""
      document.getElementById("editPembelianInputAddQty").value = "0.00"
      $('#formPembelianAdd').show();
    }
    function showPembelianEdit(indexBarang) {
       console.log('koreksi')
      let akses = $("#akses_iskoreksi").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log(indexBarang,'zxc')
      $('.showhide').hide();
      indexEditPembelianEdit = indexBarang
      console.log(dataEditPembelianEdit[indexBarang])
      console.log(dataEditPembelianEdit[indexBarang].pQC)

      document.getElementById("editPembelianInputEditKode").value = dataEditPembelianEdit[indexBarang].KodeBrg
      document.getElementById("editPembelianInputEditNamaBarang").value = dataEditPembelianEdit[indexBarang].namabrgx
      document.getElementById("editPembelianInputEditQtyPO").value = dataEditPembelianEdit[indexBarang].QNTPO
      document.getElementById("editPembelianInputEditSatuan").value = dataEditPembelianEdit[indexBarang].Satuan
      document.getElementById("editPembelianInputEditQty").value = dataEditPembelianEdit[indexBarang].Qnt
      document.getElementById("editPembelianInputEditDisc").value = dataEditPembelianEdit[indexBarang].DiscP1
      document.getElementById("editPembelianInputEditDiscRp").value = dataEditPembelianEdit[indexBarang].DiscTot
      document.getElementById("editPembelianInputEditHarga").value = dataEditPembelianEdit[indexBarang].Harga

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
     * Sama pola dengan newpo.blade.php: DUA tabel (urut 1 = Outstanding Pembelian, urut 2 =
     * Transaksi Kelengkapan Dokumen), masing-masing punya konfigurasi kolomnya sendiri
     * tersimpan di DBHEADERTABLE lewat endpoint saveheadertable/getheadertable (href =
     * 'invoicepembelian' - lihat cabang barunya di HeaderTableController::getHeaderTable()).
     * ipbCart[urut] menyimpan array itu, window.gcart_header selalu diarahkan ke cart
     * tabel yang sedang aktif. */
    const IPB_HREF = 'invoicepembelian'
    let ipbCart = { 1: [], 2: [] }
    let ipbActiveUrut = 1
    let ipbPanjangHalaman = { 1: 10, 2: 10 }
    let ipbRtSudahInit = false
    const IPB_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
    // Tabel di tab yang TIDAK aktif tidak digambar saat loadAll() - cukup ditandai di sini,
    // lalu digambar sungguhan saat tabnya dibuka (lihat handler shown.bs.tab).
    let ipbPerluGambar = { 1: false, 2: false }

    // Field dari vwBrowsOutBeli/vwTransInvoice tidak semuanya ramah dibaca - label yang
    // tampil ke user dipasang di sini, bukan lewat DBHEADERTABLEALIAS (kosong untuk href ini).
    const IPB_LABEL_1 = { NoBukti: 'No. Bukti', TANGGAL: 'Tanggal', KODECUSTSUPP: 'Kode Supp', NAMACUSTSUPP: 'Nama Supplier', NoPO: 'No. PO', TNDPP: 'DPP', TNPPN: 'PPN', TSUBTOTAL: 'Total' }
    const IPB_LABEL_2 = { NoBukti: 'No. Bukti', Tanggal: 'Tanggal', KODECUSTSUPP: 'Kode Supp', NamaCustSupp: 'Nama Supplier', NoPO: 'No. PO', TotDPPRp: 'DPP', TotPPNRp: 'PPN', TotNetRp: 'Total', OtoUser1: 'User Oto 1', TglOto1: 'Tgl Oto 1' }

    window.g_href = IPB_HREF
    window.g_modeReport = 1
    window.gcart_header = []

    // Tanggal dari SQL Server ditampilkan Y/m/d, sama seperti tampilan lama.
    function ipbFormatTanggal (v) {
      if (!v) { return '' }
      let d = new Date(v)
      if (isNaN(d.getTime())) { return v }
      let day = ('0' + d.getDate()).slice(-2)
      let month = ('0' + (d.getMonth() + 1)).slice(-2)
      return d.getFullYear() + '/' + month + '/' + day
    }

    function ipbBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered, labelMap) {
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

    // Kolom yang tampil. WAJIB hasil filter() dari cart, bukan map/salinan.
    function ipbKolomTampil (urut) {
      return (ipbCart[urut] || []).filter(c => Number(c[2]) === 1)
    }

    function ipbKolomRender (c) {
      return { field: c[0], label: c[1], tipe: Number(c[8]), desimal: Number(c[5]) }
    }

    // Kolom angka (DPP/PPN/Total dst.) dan tanggal harus rata kanan/tengah supaya
    // sejajar lurus dengan header-nya, bukan menyisa rata kiri seperti kolom teks.
    function ipbKelasKolom (col) {
      if (col.tipe === 1) { return ' class="text-right"' }
      if (col.tipe === 2) { return ' class="text-center"' }
      return ''
    }

    function ipbRenderNilai (col, item) {
      let nilai = item ? item[col.field] : undefined
      if (col.tipe === 2) {
        return nilai ? ipbFormatTanggal(nilai) : ''
      }
      if (col.tipe === 1) {
        return (nilai === null || nilai === undefined || nilai === '') ? '0.00' : formatAngkaX(nilai)
      }
      return (nilai === null || nilai === undefined) ? '' : nilai
    }

    // Kalau public/js/report-table.js belum ikut terunggah, halaman tetap tampil dengan
    // <th> biasa, hanya tanpa drag & roda gigi.
    function ipbHeadHtml (cols) {
      if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
        return ReportTable.headHtml(cols)
      }
      console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
      let html = '<tr>'
      cols.forEach((c) => { html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>` });
      return html + '</tr>'
    }

    function ipbUrutTabAktif () {
      return $('#nav-profile-tab').hasClass('active') ? 2 : 1
    }

    function ipbAktifkanTabel (urut) {
      ipbActiveUrut = urut
      window.g_modeReport = urut
      window.gcart_header = ipbCart[urut]
    }

    function ipbOnChangeAktif () {
      if (ipbActiveUrut === 2) {
        renderTabelPembelian()
      } else {
        renderTabelOut()
      }
    }

    // Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman.
    function ipbInitReportTableSekali () {
      if (ipbRtSudahInit || typeof ReportTable === 'undefined') { return }
      ipbRtSudahInit = true

      let urutAktif = ipbUrutTabAktif()
      let idTabel = { 1: '#tabel', 2: '#tabel_pembelian' }
      Object.keys(idTabel).forEach((u) => {
        if (Number(u) === urutAktif) { return }
        ReportTable.init({ table: idTabel[u], onChange: ipbOnChangeAktif })
      });

      ReportTable.init({
        table: IPB_SELEKTOR_TABEL_AKTIF,
        bar: '#rtBar',
        onChange: ipbOnChangeAktif
      })

      // DataTables memasang handler sort langsung di tiap <th>, sedangkan roda gigi/drag
      // milik report-table.js didelegasikan di <thead> - tanpa penanganan khusus, klik roda
      // gigi juga memicu sort DataTables. Hentikan event aslinya di fase capture, tembakkan
      // ulang satu event click baru langsung ke <thead>.
      let ipbGuardUlangKlik = false
      let idThead = ['tabel_header', 'tabel_pembelian_header']
      idThead.forEach((id) => {
        let thead = document.getElementById(id)
        if (!thead) { return }
        thead.addEventListener('click', function (e) {
          if (ipbGuardUlangKlik) { return }
          let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
          if (!interaktif) { return }

          e.stopPropagation()
          e.preventDefault()

          ipbGuardUlangKlik = true
          let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
          Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
          thead.dispatchEvent(ulang)
          ipbGuardUlangKlik = false
        }, true)
      });
    }

    // Pindahkan elemen #rtBar supaya duduk tepat sebelum tabel yang sedang aktif.
    function ipbPindahBar (urut) {
      let bar = document.getElementById('rtBar')
      let id = urut === 2 ? 'tabel_pembelian' : 'tabel'
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
    // scrollbar sendiri - yang discroll hanya isi tabel.
    function ipbAturTinggiTabel () {
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
    function ipbIkatSearch (urut) {
      let id = urut === 2 ? 'ipbSearch2' : 'ipbSearch1'
      let tabelId = urut === 2 ? '#tabel_pembelian' : '#tabel'
      let input = document.getElementById(id)
      if (!input || input.dataset.rtBound) { return }
      input.dataset.rtBound = '1'
      input.addEventListener('input', function () {
        $(tabelId).DataTable().search(input.value).draw()
      })
    }

    function ipbIkatPanjangHalaman (urut) {
      let id = urut === 2 ? 'ipbLen2' : 'ipbLen1'
      let tabelId = urut === 2 ? '#tabel_pembelian' : '#tabel'
      let sel = document.getElementById(id)
      if (!sel || sel.dataset.rtBound) { return }
      sel.dataset.rtBound = '1'
      sel.value = String(ipbPanjangHalaman[urut])
      sel.addEventListener('change', function () {
        let n = Number(sel.value)
        ipbPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
        $(tabelId).DataTable().page.len(ipbPanjangHalaman[urut]).draw()
      })
    }

    // Ubah salah satu tanggal periode -> muat ulang HANYA tab yang bersangkutan.
    function ipbIkatPeriode (urut) {
      let awal  = document.getElementById('ipbTglAwal' + urut)
      let akhir = document.getElementById('ipbTglAkhir' + urut)
      if (!awal || !akhir || awal.dataset.rtBound) { return }
      awal.dataset.rtBound = '1'

      let onUbah = function () {
        if (!awal.value || !akhir.value) { return }
        if (awal.value > akhir.value) {
          alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
          return
        }
        if (urut === 2) { ipbMuatPembelian() } else { ipbMuatOut() }
      }

      awal.addEventListener('change', onUbah)
      akhir.addEventListener('change', onUbah)
    }

    // Filter status otorisasi (tab "Transaksi Kelengkapan Dokumen") lewat modal
    // #modalFilterIpb - sama pola dengan modal filter Purchase Order
    // (poFilterStatus/poTerapkanFilter/poResetFilter di purchaseOrder.blade.php).
    // Hanya menyaring array yang sudah dimuat, tidak memuat ulang AJAX-nya.
    let ipbFilterStatus = ''

    function ipbUpdateFilterBadge () {
      let jml = ipbFilterStatus !== '' ? 1 : 0
      let badge = document.getElementById('ipbFilterBadge')
      if (badge) { badge.textContent = jml + ' aktif' }
    }

    function ipbTerapkanFilter () {
      ipbFilterStatus = $('#ipbModalStatus').val() || ''
      ipbUpdateFilterBadge()
      $('#modalFilterIpb').modal('hide')
      renderTabelPembelian()
    }

    function ipbResetFilter () {
      ipbFilterStatus = ''
      $('#ipbModalStatus').val('')
      ipbUpdateFilterBadge()
      $('#modalFilterIpb').modal('hide')
      renderTabelPembelian()
    }

    function ipbIkatOtoStatus () {
      let modal = document.getElementById('modalFilterIpb')
      if (!modal || modal.dataset.rtBound) { return }
      modal.dataset.rtBound = '1'
      $('#modalFilterIpb').on('show.bs.modal', function () {
        $('#ipbModalStatus').val(ipbFilterStatus)
        ipbUpdateFilterBadge()
      })
    }

    /* ---- Jembatan ke mesin penyimpan milik report-table.js ---- */
    function ipbUrutSah (mode) {
      return Number(mode) === 2 ? 2 : 1
    }

    window.doSimpanHeader = function (href, mode) {
      let urut = ipbUrutSah(mode)
      let cart = ipbCart[urut] || []

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
          href: IPB_HREF,
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
      let urut = ipbUrutSah(mode)

      $.ajax({
        url: "{!! url('getheadertable') !!}",
        type: "post",
        async: false,
        data: {
          _token: $("#_token").val(),
          href: IPB_HREF,
          urut: urut,
          reset: 1
        },
        success: function (res) {
          if (urut === 2) {
            ipbCart[2] = ipbBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, IPB_LABEL_2)
          } else {
            ipbCart[1] = ipbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, IPB_LABEL_1)
          }
          window.gcart_header = ipbCart[urut]
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
        }
      })
    }

    // Konfigurasi kolom (urut 1 & urut 2 sekaligus, satu panggilan) - lihat cabang
    // 'invoicepembelian' di HeaderTableController::getHeaderTable().
    function ipbMuatKonfigurasi () {
      $.ajax({
        url: "{!! url('getheadertable') !!}",
        type: "post",
        async: false,
        data: { _token: $("#_token").val(), href: IPB_HREF },
        success: function (res) {
          ipbCart[1] = ipbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered, IPB_LABEL_1)
          ipbCart[2] = ipbBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2, IPB_LABEL_2)
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal memuat konfigurasi kolom')
        }
      })
    }

    // Tab "Outstanding Pembelian". Baris digambar dari dataRefreshPO (diurutkan terbaru
    // dulu oleh loadAll()) menurut kolom yang sedang tampil (ipbKolomTampil).
    function renderTabelOut () {
      ipbAktifkanTabel(1)

      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().destroy()
      }

      let cols = ipbKolomTampil(1)
      let kolomRender = cols.map(ipbKolomRender)

      let thead = document.getElementById('tabel_header')
      thead.innerHTML = ipbHeadHtml(cols)
      let baris = thead.querySelector('tr')
      if (baris) {
        baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
      }

      let rowTable = ''
      dataRefreshPO.forEach((item) => {
        let tombolAksi = `
          <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetailout('${item.NoBukti}')"><i class="bi bi-info"></i></button>
          <button class="btn btn-primary btn-sm" type="button" title="Buat Invoice" onclick="buttonEditPembelian('${item.NoPO}')"><i class="bi bi-plus"></i></button>
        `
        rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
        kolomRender.forEach((c) => {
          rowTable += `<td${ipbKelasKolom(c)}>${ipbRenderNilai(c, item)}</td>`
        });
        rowTable += `</tr>`
      });

      // Baris "Tidak ada data" TIDAK ditulis manual - diserahkan ke language.emptyTable
      // di bawah, DataTables sendiri yang menggambar baris kosongnya dengan colspan benar.
      document.getElementById('tabel_data').innerHTML = rowTable

      $('#tabel').DataTable({
        lengthChange: false,
        pageLength: ipbPanjangHalaman[1],
        // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom
        // Actions). Data sudah diurutkan terbaru dulu oleh ipbMuatOut().
        order: [],
        dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
          emptyTable: 'Tidak ada data',
          zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        },
        drawCallback: function () {
          setTimeout(ipbAturTinggiTabel, 0)
        }
      })

      ipbPindahBar(1)
      ipbIkatSearch(1)
      ipbIkatPanjangHalaman(1)
      ipbIkatPeriode(1)
      let inputSearch = document.getElementById('ipbSearch1')
      if (inputSearch && inputSearch.value) {
        $('#tabel').DataTable().search(inputSearch.value).draw()
      }
      ipbAturTinggiTabel()
    }

    // Tab "Transaksi Kelengkapan Dokumen" - gabungan bekas tab Belum Oto & Oto, status
    // otorisasi disaring di klien lewat modal #modalFilterIpb (bukan AJAX ulang).
    function renderTabelPembelian () {
      ipbAktifkanTabel(2)

      if ($.fn.DataTable.isDataTable('#tabel_pembelian')) {
        $('#tabel_pembelian').DataTable().destroy()
      }

      let cols = ipbKolomTampil(2)
      let kolomRender = cols.map(ipbKolomRender)

      let thead = document.getElementById('tabel_pembelian_header')
      thead.innerHTML = ipbHeadHtml(cols)
      let baris = thead.querySelector('tr')
      if (baris) {
        baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
      }

      let statusFilter = ipbFilterStatus
      let dataTampil = dataRefreshPembelian
      if (statusFilter === '0' || statusFilter === '1') {
        dataTampil = dataRefreshPembelian.filter(function (item) {
          return String(Number(item.IsOtorisasi1) === 1 ? 1 : 0) === statusFilter
        })
      }

      let rowTable = ''
      dataTampil.forEach((item) => {
        let noBukti = item.NoBukti || ''
        let tombolAksi = ''
        if (Number(item.IsOtorisasi1) === 1) {
          tombolAksi = `
            <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="detailPembelian('${noBukti}')"><i class="bi bi-info"></i></button>
            <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="submitUnOtorisasi1('${noBukti}', '${item.IsOtorisasi1}')"><i class="bi bi-key"></i></button>
          `
        } else {
          tombolAksi = `
            <button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="detailPembelian('${noBukti}')"><i class="bi bi-info"></i></button>
            <button class="btn btn-info btn-sm" type="button" title="Edit" onclick="editKelengkapan('${noBukti}')"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="submitOtorisasi1('${noBukti}')"><i class="bi bi-key"></i></button>
          `
        }
        rowTable += `<tr><td class="text-center">${tombolAksi}</td>`
        kolomRender.forEach((c) => {
          rowTable += `<td${ipbKelasKolom(c)}>${ipbRenderNilai(c, item)}</td>`
        });
        rowTable += `</tr>`
      });

      document.getElementById('tabel_pembelian_data').innerHTML = rowTable

      $('#tabel_pembelian').DataTable({
        lengthChange: false,
        pageLength: ipbPanjangHalaman[2],
        order: [],
        dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
          emptyTable: 'Tidak ada data',
          zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
        },
        drawCallback: function () {
          setTimeout(ipbAturTinggiTabel, 0)
        }
      })

      ipbPindahBar(2)
      ipbIkatSearch(2)
      ipbIkatPanjangHalaman(2)
      ipbIkatPeriode(2)
      ipbIkatOtoStatus()
      let inputSearch = document.getElementById('ipbSearch2')
      if (inputSearch && inputSearch.value) {
        $('#tabel_pembelian').DataTable().search(inputSearch.value).draw()
      }
      ipbAturTinggiTabel()
    }

    // Ambil data tab "Outstanding Pembelian" (tglawal/tglakhir dari input tab 1) dan
    // gambar ulang tab itu saja.
    function ipbMuatOut (autoRender) {
      $.ajax({
        url: "{!! url('getAlloutbeliinv') !!}",
        type: "get",
        async: false,
        data: {
          tglawal: $('#ipbTglAwal1').val(),
          tglakhir: $('#ipbTglAkhir1').val()
        },
        success: function(res) {
          // Terbaru dulu: field TANGGAL di data ini memang berasal dari vwBrowsOutBeli
          // (huruf besar) - JANGAN diubah ke .Tanggal, beda kolom dengan tab 2.
          dataRefreshPO = (res || []).slice().sort(function (a, b) {
            let da = a && a.TANGGAL ? new Date(a.TANGGAL) : 0
            let db = b && b.TANGGAL ? new Date(b.TANGGAL) : 0
            if (db - da !== 0) { return db - da }
            return String((b && b.NoBukti) || '').localeCompare(String((a && a.NoBukti) || ''))
          })
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal memuat data Outstanding Pembelian')
        }
      })
      if (autoRender !== false) { renderTabelOut() }
    }

    // Ambil data tab "Transaksi Kelengkapan Dokumen" (tglawal/tglakhir dari input tab 2,
    // SELURUH status otorisasi) dan gambar ulang tab itu saja.
    function ipbMuatPembelian (autoRender) {
      $.ajax({
        url: "{!! url('getAllInvoiceBeli') !!}",
        type: "get",
        async: false,
        data: {
          tglawal: $('#ipbTglAwal2').val(),
          tglakhir: $('#ipbTglAkhir2').val()
        },
        success: function(res) {
          dataRefreshPembelian = (res || []).slice().sort(function (a, b) {
            let da = a && a.Tanggal ? new Date(a.Tanggal) : 0
            let db = b && b.Tanggal ? new Date(b.Tanggal) : 0
            if (db - da !== 0) { return db - da }
            return String((b && b.NoBukti) || '').localeCompare(String((a && a.NoBukti) || ''))
          })
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Gagal memuat data Transaksi Kelengkapan Dokumen')
        }
      })
      if (autoRender !== false) { renderTabelPembelian() }
    }

    function loadAll () {
      $.ajax({
        url: "{!! url('getAksesInvoiceBeli') !!}",
        type: "get",
        async: false,
        success: function(res) {
          console.log(res)
          document.getElementById("akses_istambah").value = res.ISTAMBAH
          document.getElementById("akses_iskoreksi").value = res.ISKOREKSI
          document.getElementById("akses_iscetak").value = res.ISCETAK
          document.getElementById("akses_ishapus").value = res.ISHAPUS
        }
      })

      // Idempotent - hanya benar-benar mengikat sekali seumur halaman.
      ipbInitReportTableSekali()

      // Hanya tabel di tab yang SEDANG AKTIF yang digambar (lihat catatan sama di
      // newpo.blade.php::loadAll()).
      let urutAktif = ipbUrutTabAktif()
      ipbPerluGambar[1] = (urutAktif !== 1)
      ipbPerluGambar[2] = (urutAktif !== 2)

      ipbMuatOut(false)
      ipbMuatPembelian(false)

      if (urutAktif === 2) {
        renderTabelPembelian()
      } else {
        renderTabelOut()
      }
    }

    $('#nav-home-tab').on('shown.bs.tab', function () {
      ipbAktifkanTabel(1)
      ipbPindahBar(1)

      if (ipbPerluGambar[1]) {
        ipbPerluGambar[1] = false
        renderTabelOut()
        return
      }

      if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
      ipbAturTinggiTabel()
    })

    $('#nav-profile-tab').on('shown.bs.tab', function () {
      ipbAktifkanTabel(2)
      ipbPindahBar(2)

      if (ipbPerluGambar[2]) {
        ipbPerluGambar[2] = false
        renderTabelPembelian()
        return
      }

      if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }
      ipbAturTinggiTabel()
    })

    let ipbTimerResize = null
    $(window).on('resize', function () {
      if (ipbTimerResize) { clearTimeout(ipbTimerResize) }
      ipbTimerResize = setTimeout(ipbAturTinggiTabel, 150)
    })

    $(document).ready(function () {
      ipbMuatKonfigurasi()
      loadAll()
    })

    function resetDetailPembelian (noBukti, noPO) {
      // SML/LPB/00197/0323
      // SML/PO/00302/0223
      let _token = $("#_token").val();
      console.log(noBukti, noPO)
      $('.showhide').hide();

      $.ajax({
        url: "{!! url('detailInvoiceBeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: noBukti
        },
        success: function(res) {
          console.log('=================1111===============!!!!!!')
          console.log(dataLPB, "breset")
          console.log(dataEditPembelianEdit)
          // console.log(res)
          // console.log(res[1])
          // console.log(res[0] ,'i0')

          console.log('=================22222===============!!!!!!')
          dataEditPembelianEdit = res[0]
          dataLPB = res[0][0]
          console.log(dataLPB,'areset')
          console.log(dataEditPembelianEdit)
          console.log('=================33333===============!!!!!!')
        }
      })

      if (!dataEditPembelianEdit) {
        console.log('=================44444===============!!!!!!')
        // window.location.href = "newpo";
        // editPembelian
        $("#editPembelian").modal('toggle');


      } else {
        let date = new Date(dataLPB.Tanggal);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let table_row_edit_pembelian = ""
        dataEditPembelianEdit.forEach((r, i) => {
          console.log(r)
          if (r.Satuan) {
            satuan = r.Satuan
          }
          table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td class="text-right">${formatAngka(r.Qnt)}</td><td class="text-right">${formatAngka(r.QNTPO)}</td><td>${satuan}</td><td class="text-right">${formatAngka(r.QNTOUT)}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
          });
        document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  dataLPB.NoBukti;
        // document.getElementById("editPembelianNoPO").value = dataLPB.NoPO
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

    function submitPrint () {
      console.log('submitPrint')


        let printContent1 = ""
        let jumlahPrint = $(`#input_print_jumlahprint`).val();
        let z= 0
        dataPrint.forEach((item, i) => {
          console.log('item print', item)

          let date = new Date(item.TANGGAL);
          var day = ("0" + date.getDate()).slice(-2);
          var month = ("0" + (date.getMonth() + 1)).slice(-2);
          var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

          if (document.getElementById(`printChecklist${i}`).checked) {
            // qrcodeprint
            let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;
            let printContent2 = document.getElementById(`tempqrcodeprintkodebrg${i}`).innerHTML;
            printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`

            for (let i=0; i < jumlahPrint ; i++ ) {

              if (z == 0) {
                printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; padding-right: 3px">`
              } else {
                printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; page-break-before: always; margin-top: 10px; padding-right: 3px">`
              }

              printContent1 += `<div  style="display: flex; flex-direction: column; margin-top: -25px" >`
              printContent1 += `<div class="" style="display: flex; flex-direction: row; text-align: center,">`
              printContent1 += `<div style="width:70%; padding-left: 5px; text-align: left"><h6 style=""> ${item.NoBukti}</h6> </div>`
              printContent1 += `<div style=" width:30%; padding-right: 8px; margin-right: 10px" > <h6>${item.KodeBrg} </h6> </div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="display: flex; flex-direction: row;margin-top: -20px">`
              printContent1 += `<div  style="width:70%; padding-left: 5px">` + printContent +`</div>`
              printContent1 += `<div style=" width:30%; padding-right: 18px">`+ printContent2 +`</div>`
              printContent1 += `</div>`

              printContent1 += `</div>`
              printContent1 += `<div style="width: 7.5cm; text-align: center ; margin-top: -15px">`
              printContent1 += `<h6 style="font-weight: bold; font-size: 10px;padding-right: 5px">${item.namabrgx} ${ item.NAMAMERK ? ' / ' + item.NAMAMERK : '' }</h6>`
              printContent1 += `</div>`



              printContent1 += `<div style="margin-top: -25px; text-align: center">`
              printContent1 += `<h6>${date1} / ${formatAngka(item.QntTerima)} ${item.Satuan}</h6>`
              printContent1 += `</div>`
              printContent1 += `</div>`
              z++
            }
            printContent1 += '</div>'
          }
        });

        document.getElementById("printContainer").innerHTML = printContent1
        w=window.open(' ');

        w.document.write($(`#printContainer`).html());
        w.print();
        w.close();

    }

    function buttonPrintPembelian (nobukti) {
      let akses = $("#akses_iscetak").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log('buttonPrintPembelian')
      console.log(nobukti)

      let _token = $("#_token").val();

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

          dataPrint = res[0]
          console.log(res[0])
          console.log(res[0][0])
          console.log(res[0][0].pQC)

        }
      })

      console.log(dataPrint)
      console.log(dataPrint[0])
      if (dataPrint[0].pQC === '1') {

        let date = new Date(dataPrint[0].TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;

        let rowTable = ""
        let rowTableTemp = ""
        dataPrint.forEach((r, i) => {
          console.log(r)
          let satuan = ""
          if (r.Satuan) {
            satuan = r.Satuan
          }
          rowTable += `<tr><td>
          <div class="form-check text-center">
              <input id="printChecklist${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
              </div></td><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td>${formatAngka(r.QntTerima)}</td><td>${formatAngka(r.QNTPO)}</td><td>${satuan}</td></tr>`
          rowTableTemp += `
          <div id="tempqrcodeprint${i}" style="width: 10px; height: 10px"></div>
          <div id="tempqrcodeprintkodebrg${i}" style="width: 10px; height: 10px"></div>
          `

          });
        document.getElementById("printPembelianModalLabel").innerHTML = "Print " +  dataPrint[0].NoBukti;
        document.getElementById("printPembelianNoPO").value = dataPrint[0].NoPO
        document.getElementById("printPembelianSupp").value = dataPrint[0].NamaSupplier
        document.getElementById("printPembelianFakturSupp").value = dataPrint[0].FAKTURSUPP
        document.getElementById("printPembelianKeterangan").value = dataPrint[0].KETERANGAN
        document.getElementById("printPembelianTableData").innerHTML = rowTable
        document.getElementById("tempPrintContainer").innerHTML = rowTableTemp
        dataPrint.forEach((item, i) => {
          new QRCode(document.getElementById(`tempqrcodeprint${i}`), {text: `${item.NoBukti}.${item.Urut}.${item.KodeBrg}` , width: 90, height: 90});
          new QRCode(document.getElementById(`tempqrcodeprintkodebrg${i}`), {text: `${item.KodeBrg}` , width: 90, height: 90});
        });
        $("#formPrint").modal('toggle')

      } else {
        alertify.warning('Data belum di QC')
      }

    }

    function submitPembelianDelete(index) {
      let akses = $("#akses_ishapus").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }


      alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + '?',
          function() {
            console.log(dataEditPembelianEdit[index])
            let _token = $("#_token").val();
            let choice = "D"
            let dataLPBDelete = dataEditPembelianEdit[index]

            let reqNoBukti = dataLPBDelete.NoBukti
            let reqNoUrut = dataLPBDelete.NoUrut
            let reqTANGGAL = dataLPBDelete.TANGGAL
            let reqKodeSupp = dataLPBDelete.KODESUPP
            let reqKodeGudang = dataLPBDelete.KODEGDG
            let reqNoPO = dataLPBDelete.NoPO
            let reqKeterangan = dataLPBDelete.KETERANGAN
            let reqFakturSupp = dataLPBDelete.FAKTURSUPP

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
                console.log(res)
                console.log("del successsssssssssssssssssssss")
                loadAll()
                resetDetailPembelian(reqNoBukti,reqNoPO)
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

      dataLPBEdit = dataEditPembelianEdit[0]




      let reqNobukti = $("#editnobukti").val()
      let reqNourut =  $("#editnoUrut").val()
      let reqTanggal = $("#editPembelianDate").val()
      let reqNopo = dataLPBEdit.NoPO

      let reqTglpajak = $("#edittglfpajak").val()
      let reqTglbpotong = $("#edittglbpotong").val()
      let reqTglinvoice = $("#edittglinvoice").val()
      let reqNopajak = $("#editnopajak").val()
      let reqNobpotong = $("#editnobpotong").val()
      let reqNoinvoice = $("#editnoinvoice").val()
      let tempData = []
      for (let i = 0; i < listDataEdit.length; i++) {
          if (document.getElementById(`add_checkbox${i}`).checked) {
          listDataEdit[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
          tempData.push(listDataEdit[i])
        }
      }

      if(!tempData.length) {
        alertify.warning("Tidak ada item dipilih");
        return
      }


  console.log(reqNobukti)
  console.log(reqNourut)
  console.log(tempData)


      $.ajax({
        url: "{!! url('sp_beligudangInvoiceBeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          reqNobukti,
          reqNourut,
          reqTanggal,
          reqNopo,
          reqTglpajak,
          reqTglbpotong,
          reqTglinvoice,
          reqNopajak,
          reqNobpotong,
          reqNoinvoice,
          tempData

        },
        success: function(res) {
          console.log(res)
          console.log("successsssssssssssssssssssss")
          loadAll()
            $("#editPembelian").modal('toggle');
          alertify.success('Faktur dan keterangan telah diupdate');
        }
      })

      console.log(reqNourut,reqNourut)
    }

    function submitPembelianEdit() {
      console.log(indexEditPembelianEdit, dataEditPembelianEdit[indexEditPembelianEdit])

      let _token = $("#_token").val();
      let choice = "U"
      let dataLPBEdit = dataEditPembelianEdit[indexEditPembelianEdit]
      let reqQtyTerima = $("#editPembelianInputEditQty").val();
      let reqDisc = $("#editPembelianInputEditDisc").val();
      let reqHarga = $("#editPembelianInputEditHarga").val();
      let reqDiscRp = $("#editPembelianInputEditDiscRp").val();

      if (Number(reqQtyTerima) > (Number(dataLPBEdit.QNTOUT) + Number(dataLPBEdit.Qnt))) {
        alertify.warning("Qty melebihi Qty OUT");
        console.log('error')
        return
      }
      if (Number(reqQtyTerima) < 0) {
        alertify.warning("Qty tidak boleh negatif");
        return
      }
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
      let reqNamaBarang = dataLPBEdit.namabrgx
      let reqNoBatch = ""
      let reqQtyReject = 0
      let reqQtyReject1 = 0
      let reqQtyReject2 = 0
      let reqPBeliJasa = 0
      let reqEd = null

      console.log("===========================")

      $.ajax({
        url: "{!! url('sp_beligudangACC') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
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
          reqDisc,
          reqDiscRp,
          reqHarga,
        },
        success: function(res) {
          console.log(res)
          console.log("successsssssssssssssssssssssx")
          loadAll()
          resetDetailPembelian(reqNoBukti,reqNoPO)
          alertify.success('Item telah diedit');
        }
      })


    }

    function submitPembelianAdd() {
      if (dataEditPembelianAddIndex === "") {
        console.log("no item")
        alertify.warning("Tidak ada item dipilih");
      } else {

        let _token = $("#_token").val();


        let data = dataEditPembelianAdd[dataEditPembelianAddIndex]
        console.log(data)
        let reqQtyTerima = $("#editPembelianInputAddQty").val()
        if (Number(reqQtyTerima) > Number(data.OSPO)) {
          alertify.warning("Qty melebihi Qty OS");
          console.log('error')
          return
        }
        if (Number(reqQtyTerima) < 0) {
          alertify.warning("Qty tidak boleh negatif");
          return
        }

        let choice = "I"
        let reqNoBukti = dataLPB.NoBukti
        let reqNoUrut = dataLPB.NoUrut
        let reqTANGGAL = dataLPB.TANGGAL
        let reqKodeSupp = dataLPB.KODESUPP
        let reqKodeGudang = dataLPB.KODEGDG
        let reqNoPO = dataLPB.NoPO
        let reqKeterangan = dataLPB.KETERANGAN
        let reqFakturSupp = dataLPB.FAKTURSUPP
        let reqUrut = 0
        let reqKodeBarang = data.KodeBrg
        let reqUrutPO = data.Urut

        if (Number(reqQtyTerima) < 0) {
          alertify.warning("Qty tidak boleh negatif");
          return
        }
        let reqNoSat = data.NOSAT
        let reqSatuan = data.Satuan
        let reqIsi = data.ISI
        let reqQtyTerima1 = 0
        let reqQtyTerima2 = 0
        if (data.NOSAT == 1) {
          reqQtyTerima1 = reqQtyTerima;
          reqQtyTerima2 = reqQtyTerima / data.ISI2;
        } else if (data.NOSAT == 2) {
          reqQtyTerima1 = reqQtyTerima * data.ISI2;
          reqQtyTerima2 = reqQtyTerima;
        }
        let reqNamaBarang = data.namabrgx
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
              reqDiscRp,
              reqHarga
            },
            success: function(res) {
              console.log(res)
              console.log("successsssssssssssssssssssss")
              loadAll()
              resetDetailPembelian(reqNoBukti,reqNoPO)
              alertify.success('Item telah diadd');
            }
          })

      }

    }

    function changeSelectBarang () {
      // sp_BeliGudang
      let indexBarang = document.getElementById("editPembelianAddSelect").value;
      dataEditPembelianAddIndex = indexBarang
      console.log(dataEditPembelianAdd[indexBarang])
      document.getElementById("editPembelianInputAddKode").value = dataEditPembelianAdd[indexBarang].KodeBrg
      document.getElementById("editPembelianInputAddNamaBarang").value = dataEditPembelianAdd[indexBarang].namaBrg

      document.getElementById("editPembelianInputAddQtyOS").value =  parseFloat(dataEditPembelianAdd[indexBarang].OSPO).toFixed(2)
      document.getElementById("editPembelianInputAddQtyPO").value =  parseFloat(dataEditPembelianAdd[indexBarang].QNT).toFixed(2)
      document.getElementById("editPembelianInputAddSatuan").value = dataEditPembelianAdd[indexBarang].Satuan
    }

    function buttonEditPembelian1 (nobukti) {
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
      console.log("qwerthhh")
      console.log(nobukti)
     setNewNoBukti()
      let _token = $("#_token").val();

      let edit_pembelian_row_data = []

      $.ajax({
        url: "{!! url('detailbeli') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {
            console.log("xxxxcc")
          console.log(res)
          listDataEdit = res
          edit_pembelian_row_data = res

        }
      })

          if(!edit_pembelian_row_data.length) {
            console.log('------------')
            alertify.warning('silahkan refresh browser')
            return
          }




          dataLPB =  edit_pembelian_row_data
          dataEditPembelianEdit = edit_pembelian_row_data

          // Tanggal di sini berasal dari vwBrowsOutBeli (endpoint 'detailbeli') - kolomnya
          // Tanggal (huruf kecil). Guard tetap dipakai untuk berjaga-jaga.
          let date = ipbAmbilTanggal(edit_pembelian_row_data[0]);
          let date1 = ipbTanggalYMD(date);

          let table_row_edit_pembelian = ""
          edit_pembelian_row_data.forEach((r, i) => {
            console.log('r===' ,r ,'ASD')
            console.log('SDW')

            let satuan = ""
            if (r.Satuan) {
              satuan = r.Satuan
            }
            table_row_edit_pembelian += `<tr><td class="text-center"><input id="add_checkbox${i}" class="pba-chk-lg" type="checkbox" ></td><td>${r.NoBukti}</td><td class="text-center">${ipbTglSaja(r.Tanggal)}</td><td>${r.NoPO}</td><td class="text-center">${ipbTglSaja(r.TglPO)}</td></tr>`
            });
          document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NAMACUSTSUPP
          document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian
          document.getElementById("editPembeliantipeppn").value = edit_pembelian_row_data[0].MyPPN




          console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")

          $.ajax({
            url: "{!! url('detailPOBeli') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,

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

          document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect


          console.log('date1', date1)
          $("#editPembelian").modal('toggle');
        }

        function buttonOtoPembelian (nobukti) {
          console.log(nobukti,  "<<< detail")
          console.log('================')
          console.log(nobukti)
          pNObukti = nobukti
          DataNobukti = nobukti

          let _token = $("#_token").val();

          let detail_row_data = []

          $.ajax({
            url: "{!! url('detailPembelianACC') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              NoBukti: nobukti
            },
            success: function(res) {
                console.log('======xxxxx==========')
              console.log(res)

              detail_row_data = res[0]
              console.log(res[0])

            }
          })

           dataLPB =  detail_row_data[0]
           dataEditPembelianEdit = detail_row_data

          let date = ipbAmbilTanggal(detail_row_data[0]);
          let date1 = ipbTanggalYMD(date);

           let table_row_detail = ""

               detail_row_data.forEach((detail_row, i) => {
             console.log(detail_row.KodeBrg)

          table_row_detail += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${formatAngka(detail_row.Qnt)}</td><td class="text-right">${formatAngka(detail_row.QNTPO)}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td></tr>`

          });

          document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;
          document.getElementById("detailSupp").value = detail_row_data[0].NamaSupplier;

          document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS;


          document.getElementById("detailNoPO").value = detail_row_data[0].NoPO
          document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP
          document.getElementById("detailKeterangan").value = detail_row_data[0].KETERANGAN

          document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS
          document.getElementById("detailkurs").value = detail_row_data[0].KURS

          document.getElementById("detailtipeppn").value = detail_row_data[0].MyPPN
          document.getElementById("detailtipebayar").value = detail_row_data[0].MyTipeBayar

          document.getElementById("detailSoCustomer").value = detail_row_data[0].NOSO
          document.getElementById("detailNoUangMuka").value = detail_row_data[0].NOUMK

          document.getElementById("detailNuangmuka").value = detail_row_data[0].NuangMuka
          document.getElementById("detailNilaiPPN").value = detail_row_data[0].NilaiPPN

          document.getElementById("detailgudang").value = detail_row_data[0].NAMAGUDANG

          document.getElementById("detailNoSopir").value = detail_row_data[0].SOPIR

         console.log(date1)
          $('#detailDate').val(date1)


          document.getElementById("detailTableData").innerHTML = table_row_detail


          $("#detail").modal('toggle');
        }

        function buttonAdd1(index) {
          let tempDataAdd = dataRefreshPO[index]
          buttonAdd('x' , tempDataAdd)

        }


    function buttonEditPembelianTemp (edit_pembelian_row_id, edit_pembelian_row_data) {
      $('.showhide').hide();
      console.log("qwert")
      dataLPB =  edit_pembelian_row_data[0]
      dataEditPembelianEdit = edit_pembelian_row_data

      console.log(edit_pembelian_row_id, edit_pembelian_row_data , "<<<< edit pembelian")
      let date = ipbAmbilTanggal(edit_pembelian_row_data[0]);
      let date1 = ipbTanggalYMD(date);

      let table_row_edit_pembelian = ""
      edit_pembelian_row_data.forEach((r, i) => {
        console.log('r===' ,r ,'ASD')
        console.log('SDW')
         console.log(r.Harga)
        let satuan = ""
        if (r.Satuan) {
          satuan = r.Satuan
        }
        table_row_edit_pembelian += `<tr><td>${r.KodeBrg}</td><td>${r.namabrgx}</td><td>${formatAngka(r.Qnt)}</td><td>${formatAngka(r.QNTPO)}</td><td>${satuan}</td><td>${formatAngka(r.Qnt)}</td><td>-</td><td>-</td><td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="showPembelianEdit(${i})"><i class="bi bi-pen"></i></button><button style="" class="btn btn-danger btn-sm" type="button" onclick="submitPembelianDelete(${i})" ><i class="bi bi-trash"></i></button></td></tr>`
        });
      document.getElementById("editPembelianModalLabel").innerHTML = "Edit " +  edit_pembelian_row_data[0].NoBukti;

      document.getElementById("editPembelianSupp").value = edit_pembelian_row_data[0].NamaSupplier
      document.getElementById("editPembelianFakturSupp").value = edit_pembelian_row_data[0].FAKTURSUPP
      document.getElementById("editPembelianKeterangan").value = edit_pembelian_row_data[0].KETERANGAN
      document.getElementById("editPembelianTableData").innerHTML = table_row_edit_pembelian


      console.log(edit_pembelian_row_data[0].NoBukti , "<<<<<<<<<<")
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailPO') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoPO: edit_pembelian_row_data[0].NoBukti,
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

      document.getElementById("editPembelianAddSelect").innerHTML = editPembelianSelect

      $('#editPembelianDate').val(date1)
      $("#editPembelian").modal('toggle');
    }
    function buttonDetail1(index) {
      let tempDataDetail = dataRefreshPO[index]
      buttonDetail('x' , tempDataDetail)
    }

    function buttonDetail (nobukti) {
      console.log(nobukti,  "<<< detail")
      console.log('================')
      console.log(nobukti)

      let _token = $("#_token").val();

      let detail_row_data = []

console.log(nobukti,"sebelum url")
      $.ajax({
     url: "{!! url('detailBeliDet') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NoBukti: nobukti
        },
        success: function(res) {

            console.log('======xxxxx==========')
          console.log(res)

          // getBeliDet() sekarang menyaring di SQL (VWtampilbeli::where()->get()) - hasilnya
          // array baris DATAR, bukan array-berisi-satu-grup seperti sebelumnya, jadi dibaca
          // langsung dari res (bukan res[0]).
          detail_row_data = res
          console.log(res)

        }
      })

      if (!detail_row_data || detail_row_data.length === 0) {
        alertify.warning('Detail dokumen tidak ditemukan')
        return
      }

       dataLPB =  detail_row_data[0]
       dataEditPembelianEdit = detail_row_data

      let date = ipbAmbilTanggal(detail_row_data[0]);
      let date1 = ipbTanggalYMD(date);

       let table_row_detail = ""

           detail_row_data.forEach((detail_row, i) => {
         console.log(detail_row.KodeBrg)

      table_row_detail += `<tr><td>${detail_row.KodeBrg}</td><td>${detail_row.namabrgx}</td><td class="text-right">${formatAngka(detail_row.Qnt)}</td><td class="text-right">${formatAngka(detail_row.QNTPO)}</td><td>${detail_row.Satuan}</td><td class="text-right">${Number(detail_row.Harga) ? detail_row.Harga : "0.00"}</td><td class="text-right">${detail_row.DiscRp1}</td><td class="text-right">${detail_row.NNET}</td></tr>`

      });

      document.getElementById("detailModalLabel").innerHTML = "Detail " +  detail_row_data[0].NoBukti;
      document.getElementById("detailSupp").value = detail_row_data[0].NamaSupplier;

      document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS;


      document.getElementById("detailNoPO").value = detail_row_data[0].NoPO
      document.getElementById("detailFakturSupp").value = detail_row_data[0].FAKTURSUPP
      document.getElementById("detailKeterangan").value = detail_row_data[0].KETERANGAN

      document.getElementById("detailvalas").value = detail_row_data[0].KODEVLS
      document.getElementById("detailkurs").value = detail_row_data[0].KURS

      document.getElementById("detailtipeppn").value = detail_row_data[0].MyPPN
      document.getElementById("detailtipebayar").value = detail_row_data[0].MyTipeBayar

      document.getElementById("detailSoCustomer").value = detail_row_data[0].NOSO
      document.getElementById("detailNoUangMuka").value = detail_row_data[0].NOUMK

      document.getElementById("detailNuangmuka").value = detail_row_data[0].NuangMuka
      document.getElementById("detailNilaiPPN").value = detail_row_data[0].NilaiPPN

      document.getElementById("detailgudang").value = detail_row_data[0].NAMAGUDANG

      document.getElementById("detailNoSopir").value = detail_row_data[0].SOPIR

     console.log(date1)
      $('#detailDate').val(date1)


      document.getElementById("detailTableData").innerHTML = table_row_detail


      $("#detail").modal('toggle');
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
      document.getElementById("input_add_suratjalansupp").value ='';
      document.getElementById("input_add_nokend").value ='';

      let akses = $("#akses_istambah").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      console.log('tes123')
      console.log(add_row_data)
      setNewNoBukti()



      console.log(add_row_id, add_row_data ,  "<<< add")
      console.log('================')
      row_data = add_row_data
      let table_row_add = ""
      add_row_data.forEach((add_row, i) => {
        table_row_add += `<tr><td class="text-center"><input id="add_checkbox${i}" class="pba-chk-lg" type="checkbox" ></td><td>${add_row.namaBrg}</td><td class="text-right">${formatAngka(add_row.QNT)}</td><td class="text-right">${formatAngka(add_row.QntBeli)}</td><td class="text-right">${parseFloat(add_row.OSPO).toFixed(2)}</td><td>${add_row.Satuan}</td><td><input id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td><td>-</td><td>-</td></tr>`
      });

      document.getElementById("addTableData").innerHTML = table_row_add
      document.getElementById("input_add_nomorpo").value = add_row_data[0].NoBukti;
      document.getElementById("exampleModalLabel").innerHTML = "Add " +  add_row_data[0].NoBukti;
      document.getElementById("input_add_gudang").value = add_row_data[0].KODEGDG;
      $("#form").modal('toggle');
    }

    function buttonEdit() {
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

    function submitAdd () {
      console.log('submitAdd123')
        let tempData = []
        for (let i = 0; i < edit_pembelian_row_data.length; i++) {
          if (document.getElementById(`add_checkbox${i}`).checked) {
          edit_pembelian_row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
          tempData.push(edit_pembelian_row_data[i])
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
      console.log('====== starting create =======')
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

    function formatAngkaX (angka) {
      if (!Number(angka)) {
        return '0.00'
      } else {
        return formatAngka(parseFloat(angka).toFixed(2))

      }

    }


    function formatAngka (angkaString) {
      if (!Number(angkaString)) {
        return '0.00'
      }
      angkastring = parseFloat(angkaString).toFixed(2)

      let tempAngka = angkaString.split('.')

      if (tempAngka[0][0] == '-') {
        let temp2=''

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
    }


    function buttonDetailout (nobukti) {

        let _token = $("#_token").val();

        let detail_row_data = []

        $.ajax({
          url: "{!! url('detailBeliDet') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            NoBukti: nobukti
          },
          success: function(res) {
              console.log('======xxxxx==========')
            console.log(res)

            // getBeliDet() sekarang menyaring di SQL - hasilnya array baris DATAR, dibaca
            // langsung dari res (bukan res[0]) - lihat juga catatan yang sama di buttonDetail().
            detail_row_data = res
            console.log(res)

          }
        })

        if (!detail_row_data || detail_row_data.length === 0) {
          alertify.warning('Detail dokumen tidak ditemukan')
          return
        }

        dataLPB =  detail_row_data[0]
        dataEditPembelianEdit = detail_row_data

        let date = ipbAmbilTanggal(detail_row_data[0]);
        let date1 = ipbTanggalYMD(date);

        let table_row_detail = ""


         detail_row_data.forEach((detail_row, i) => {


        table_row_detail += `<tr><td>${detail_row.KodeBrg}</td>
        <td>${detail_row.namabrgx}</td>
        <td class="text-right">${detail_row.Qnt ? formatAngkaX(detail_row.Qnt) : '0.00' }</td>
        <td class="text-right">${detail_row.QNTPO ? formatAngkaX(detail_row.QNTPO) : '0.00' }</td>
        <td>${detail_row.Satuan}</td>
        <td class="text-right">${detail_row.Harga ? formatAngkaX(detail_row.Harga) : '0.00' }</td>
        <td class="text-right">${detail_row.DiscRp1 ? formatAngkaX(detail_row.DiscRp1) : '0.00' }</td>
        <td class="text-right">${detail_row.NNET ? formatAngkaX(detail_row.NNET) : '0.00' }</td></tr>`






         });

        document.getElementById("detailModalLabelout").innerHTML = "Detail " +  detail_row_data[0].NoBukti;

        document.getElementById("detailPembelianNobuktiout").value = detail_row_data[0].NoBukti ?? '';
        document.getElementById("detailPembelianKodeSuppout").value = detail_row_data[0].KODESUPP ?? '';
        document.getElementById("detailPembelianAlamatSuppout").value = detail_row_data[0].ALAMAT1 ?? '';
        document.getElementById("detailPembelianhariout").value = detail_row_data[0].HARI ?? 0;

        document.getElementById("detailPembelianSuppout").value = detail_row_data[0].NamaSupplier ?? '';

        document.getElementById("detailPembelianvalasout").value = detail_row_data[0].KODEVLS ?? '';


        document.getElementById("detailNoPOout").value = detail_row_data[0].NoPO ?? ''
        document.getElementById("detailFakturSuppout").value = detail_row_data[0].FAKTURSUPP ?? ''

        document.getElementById("detailPembeliankursout").value = detail_row_data[0].KURS ?? ''

        document.getElementById("detailPembeliantipeppnout").value = detail_row_data[0].PPN ?? 0
        document.getElementById("detailPembeliantipebayarout").value = detail_row_data[0].TIPEBAYAR ?? 0

        document.getElementById("detailSoCustomerout").value = detail_row_data[0].NOSO ?? ''
        document.getElementById("detailNoUangMukaout").value = detail_row_data[0].NOUMK ?? ''

        document.getElementById("detailNuangmukaout").value = detail_row_data[0].NuangMuka ?? 0


        document.getElementById("detailgudangout").value = detail_row_data[0].NAMAGUDANG ?? ''
        document.getElementById("detailFakturSuppout").value = detail_row_data[0].FAKTURSUPP ?? ''

        document.getElementById("detailNoSopirout").value = detail_row_data[0].SOPIR ?? ''

        // Jth Tempo sebelumnya tidak pernah diisi - selalu menampilkan tanggal hari ini.
        if (detail_row_data[0].TglJatuhTempo) {
          $('#detailPembelianJthTempoout').val(ipbTanggalYMD(new Date(detail_row_data[0].TglJatuhTempo)))
        }

        document.getElementById("input_det_discout").value = detail_row_data[0].disc ?? 0
        document.getElementById("input_det_discrpout").value = detail_row_data[0].DISCRP ?? 0
        document.getElementById("input_det_dppout").value = detail_row_data[0].TotDPP ?? 0
        document.getElementById("input_det_ppnout").value = detail_row_data[0].TotPPN ?? 0
        document.getElementById("input_det_grandtotalout").value = detail_row_data[0].TotNet ?? 0
        $('#detailDateout').val(date1)

        document.getElementById("detailTableDataout").innerHTML = table_row_detail


        $("#page1").hide();
        $("#page3").show();
        window.scrollTo(0, 0);
      }

      function buttonCloseForm () {
        $("#page3").hide();
        $("#page1").show();
        window.scrollTo(0, 0);
      }

      function setNewNoBukti () {
        $.ajax({
          url: "{!! url('getNoBuktiInvoiceBeli') !!}",
          type: "get",
          async: false,
          success: function(res) {
            console.log(res , "RESPONDxg");
            console.log(res[0].Nobukti)
            document.getElementById("editnobukti").value = res[0].Nobukti
            document.getElementById("editnoUrut").value = res[0].Nourut
          }
        })
      }




  </script>

@endsection
