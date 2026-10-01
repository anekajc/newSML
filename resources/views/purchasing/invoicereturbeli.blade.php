@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Invoice Retur Beli')

@endsection

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
{{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + modal
     filter) --}}
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

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
   style pada <table>, yang mengalahkan `.data-table { width: 100% }`. Dipakai min-width,
   BUKAN width, dan di-scope lewat ID (bukan class) - sama seperti uangmukabeli.blade.php. */
#tabel2 {
  min-width: 100%;
}

/* Tabel item Add/Edit/Detail - header abu-abu uppercase, zebra, tombol pastel - sama
   seperti #tabel_add di perintahreturbeli.blade.php. */
#addTable td:last-child:not([colspan]),
#detailTable td:first-child:not([colspan]) {
  vertical-align: middle;
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#addTable td:last-child .btn,
#detailTable td:first-child .btn {
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

#addTable td:last-child .btn:hover,
#detailTable td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#addTable td:last-child .btn-success  { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#addTable td:last-child .btn-danger   { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

#addTable thead th,
#detailTable thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#addTable tbody tr:nth-of-type(odd), #detailTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#addTable tbody tr:hover, #detailTable tbody tr:hover { background-color: #f5f3ff; }

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

#tabel2 td:first-child:not([colspan]) {
  vertical-align: middle;
}

#tabel2 td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

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

#tabel2 td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel2 td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel2 td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

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

#detailTable, #detailTable td, #detailTable th,
#addTable, #addTable td, #addTable th { border-left: none !important; border-right: none !important; }
#detailTable tbody td,
#addTable tbody td { border-top: none !important; border-bottom: 1px solid #f1f3f5 !important; font-size: 13px; vertical-align: middle; }
</style>

<style>

#tabel_add_list_customer_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_customer_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_noinvoice_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_noinvoice_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_barang_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_barang_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_nobeli_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_nobeli_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

</style>
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- <h2>Invoice Retur Beli</h2> -->
    </div>
  </div>
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" class="po-filter-inp" id="irbTglAwal" value="{!! $irbTglAwal !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="irbTglAkhir" value="{!! $irbTglAkhir !!}">
        </div>
        <input type="search" id="irbSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat irbIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="irbLen">Tampilkan</label>
          <select id="irbLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="irbBtnFilter" onclick="$('#modalFilterIRB').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act"></div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat irbInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabel2" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelIRB() lewat JS, sama seperti uangmukabeli.blade.php,
               supaya susunan kolom hasil geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
        </tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>

    </div>
  </div>
</div>
</div>

<div id="page2" style="display: none" class="mainpage container-fluid">
<div id="formBsGrid">

  <div class="row" style="margin-bottom: 14px;">
    <div class="col-8 text-left">
    </div>
    <div class="col-4 text-right">
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

  <div id= "modalAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <!-- Kolom 1: Supplier -->
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Supplier</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea style="width: 100%; resize: none" rows=1 class="form-control" id="input_add_namacustomer" disabled></textarea>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea style="width: 100%; resize: none" rows=3 class="form-control" id="input_add_alamatcustomer" disabled></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom 2: No Bukti / No Beli / Catatan -->
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>No Beli</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group input-group">
                <input type="hidden" class="form-control" id="input_add_flagtipe" value="">
                <input type="hidden" class="form-control" id="input_add_ppn" value="">
                <input type="text" class="form-control" id="input_add_noinvoice" value="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>Catatan</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group">
                <textarea style="width: 100%; resize: none" rows=3 class="form-control" id="input_add_catatan"></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom 3: Tgl / PPN / Bayar / Hari -->
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tgl</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_noso" value="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>Bayar</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_bayar" value="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>Hari</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_hari" value="" disabled>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom 4: Valas / Kurs / Gudang -->
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Valas</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_add_valas" value="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>Kurs</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group input-group">
                <input type="text" class="form-control text-right" id="input_add_kurs" value="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <div class="col-md-8" style="margin-top:-10px">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_add_gudang" value="" disabled>
              </div>
            </div>
          </div>
        </div>

      </div>



      </div>








  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="table data-table"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Kode Barang</th>
              <th scope="col">Nama Barang</th>
              <th scope="col">Qty</th>
              <th scope="col">Sat</th>
              <th scope="col">Harga</th>
              <th scope="col">Diskon</th>
              <th scope="col">Sub Total</th>
              <th scope="col">No. PR.</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=9 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>

  <div class="col-md-12 mt-2 text-right">
  {{-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah Item</button> --}}
</div>


  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4>Add Item</h4>
      </div>
    </div>
    <div class="row">

      <div class="col-md-12">

      <div class="row">

      <div class="col-md-3">




    <div class="row">






      <div class="col-md-4">
        <div class="form-group">
        <label>Kode Brg</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>

        </div>
      </div>

    </div>

  </div>
  <div class="col-md-3">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>No Beli</label>
        </div>
      </div>
      <!-- <div class="col-3 text-right">
        <div class="form-group">
      </div>
    </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddUrutBeli" type="hidden" class="form-control" >
          <input id="AddAddNoBeli" type="text" class="form-control" disabled>
          <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-plus"></i></button>

        </div>
        <!-- <input id="AddAddKodeGudang" type="hidden" class="form-control" disabled> -->
      </div>


    </div>

  </div>
</div>
</div>

</div>
</div>







  <div class="col-md-6">
    <div class="row">
      <div class="col-md-12" style="margin-top: -10px">

      <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Nama Brg</label>
      </div>
      </div>
      <div class="col-md-6">
        <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
      </div>
      <div class="col-md-4">

      </div>
    </div>
  </div>


  <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty</label>
      </div>
      </div>

      <div class="col-md-4">
        <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQty()">
      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Satuan</label>
      </div>
      </div>

      <div class="col-md-4">
        <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
        <select id="AddAddInputNosat" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangeQty()" disabled>
          <option value=0 selected></option>
        </select>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="margin-top: -10px">

  <div class="row" hidden>

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty 1</label>
      </div>
      </div>

      <div class="input-group col-md-4">
        <input id="AddAddInputQty1" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">

        <input id="AddAddInputSat1" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty 2</label>
      </div>
      </div>

      <div class="input-group col-md-4">
        <input id="AddAddInputQty2" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">

        <input id="AddAddInputSat2" type="text" value='BOX' class="form-control text-center" disabled style="width: 25%">



      </div>
</div>
</div>




      <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
  <div class="input-group-append">
    <span class="input-group-text">@example.com</span>
  </div> -->
  <div class="col-md-12" style="margin-top: -10px">
    <div class="row">






      <div class="col-md-6">

      </div>
    </div>
  </div>
  <div class="col-md-12" style="margin-top: -10px">
    <div class="row">


      <div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-6">
        <input id="AddAddKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>
    </div>
  </div>
    </div>
  </div>


  <!-- <div class="col-6 ">
    <div class="row">



    </div> -->
  <!-- </div> -->




  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit Add</button>
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
    </div>

  </div>

</div>

    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>
</div>
<!-- </div> -->


<!-- ADD EDIT -->

<div id="formAddEdit" class="container-fluid showhideitem">

  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
    </div>
  </div>

  <div class="row">

    <!-- Kolom 1: Kode Brg / Nama Brg / Disc -->
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Kode Brg</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
          </div>
        </div>

        <div class="col-md-4" style="margin-top:-10px">
          <div class="form-group">
            <label>Nama Brg</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-10px">
          <div class="form-group">
            <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
          </div>
        </div>

        <div class="col-md-4" style="margin-top:-10px">
          <div class="form-group">
            <label>Disc(%)</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-10px">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value="0.00" tabindex="8">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen2" value="0.00" tabindex="9">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen3" value="0.00" tabindex="10">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kolom 2: No Beli + Satuan satu baris, Qty + Harga satu baris -->
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <label>No Beli</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="input-group form-group">
            <input id="AddEditUrutBeli" type="hidden" class="form-control">
            <input id="AddEditNoBeli" type="text" class="form-control" disabled>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            <label>Satuan</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input id="AddEditInputIsi" type="hidden" class="form-control" disabled>
            <select id="AddEditInputNosat" class="form-control" aria-label="Satuan" onchange="onChangeQtyEdit()">
              <option value=0 selected></option>
            </select>
          </div>
        </div>

        <div class="col-md-2" style="margin-top:-10px">
          <div class="form-group">
            <label>Qty</label>
          </div>
        </div>
        <div class="col-md-4" style="margin-top:-10px">
          <div class="form-group">
            <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQtyEdit()">
          </div>
        </div>
        <div class="col-md-2" style="margin-top:-10px">
          <div class="form-group">
            <label>Harga</label>
          </div>
        </div>
        <div class="col-md-4" style="margin-top:-10px">
          <div class="form-group">
            <input id="AddEditHarga" type="text" value='0.00' class="form-control text-right">
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- Qty per satuan - disembunyikan, hanya dipakai perhitungan konversi satuan. --}}
  <div class="row" hidden>
    <div class="col-md-2">
      <div class="form-group">
        <label>Qty 1</label>
      </div>
    </div>
    <div class="input-group col-md-4">
      <input id="AddEditInputQty1" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">
      <input id="AddEditInputSat1" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label>Qty 2</label>
      </div>
    </div>
    <div class="input-group col-md-4">
      <input id="AddEditInputQty2" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">
      <input id="AddEditInputSat2" type="text" value='BOX' class="form-control text-center" disabled style="width: 25%">
    </div>
  </div>

  <div class="row mt-2">
    <div class="col-md-12 text-right">
      <button type="button" class="btn btn-lg btn-batal-add" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-lg btn-chip-biru" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Simpan</button>
    </div>
  </div>

</div>


<div class="container-fluid mt-3">
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
          <input type="text" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
        </div>
      </div>

      <!-- DiscRp -->
      <div class="col">
        <div class="form-group">
          <label>DiscRp</label>
          <input type="text" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
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
          <input type="text" class="form-control text-right" id="input_add_ppnTotal" value="0.00" disabled>
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

    </div>

    <!-- <div class="row "> -->

<!-- </div> -->

  </div>{{-- /#formBsGrid --}}
  </div>


  <div id="page3" style="display: none" class="mainpage container-fluid" >
  <div id="formBsGrid">

    <div class="row" style="margin-bottom: 14px;">
      <div class="col-8 text-left">
      </div>
      <div class="col-4 text-right">
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

    <div id= "modalDetail" class="">



    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <input type="hidden" class="form-control" id="input_detail_flagtipe" value="">
        <input type="hidden" class="form-control" id="input_detail_ppn" value="">

        <div class="row">

          <!-- Kolom 1: Supplier -->
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Supplier</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
                </div>
              </div>

              <div class="col-md-12" style="margin-top:-10px">
                <div class="form-group">
                  <textarea style="width: 100%; resize: none" rows=1 class="form-control" id="input_detail_namacustomer" disabled></textarea>
                </div>
              </div>

              <div class="col-md-12" style="margin-top:-10px">
                <div class="form-group">
                  <textarea style="width: 100%; resize: none" rows=3 class="form-control" id="input_detail_alamatcustomer" disabled></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Kolom 2: No Bukti / No Beli / Catatan -->
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>No Beli</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_detail_noinvoice" value="" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group">
                  <textarea style="width: 100%; resize: none" rows=3 class="form-control" id="input_detail_catatan" disabled></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Kolom 3: Tgl / PPN / Bayar / Hari -->
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>PPN</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_noso" value="" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>Bayar</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_bayar" value="" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_hari" value="" disabled>
                </div>
              </div>
            </div>
          </div>

          <!-- Kolom 4: Valas / Kurs / Gudang -->
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_detail_valas" value="" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group input-group">
                  <input type="text" class="form-control text-right" id="input_detail_kurs" value="" disabled>
                </div>
              </div>

              <div class="col-md-4" style="margin-top:-10px">
                <div class="form-group">
                  <label>Gudang</label>
                </div>
              </div>
              <div class="col-md-8" style="margin-top:-10px">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_detail_gudang" value="" disabled>
                </div>
              </div>
            </div>
          </div>

        </div>










        </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="table"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode Barang</th>
                <th scope="col">Nama Barang</th>
                <th scope="col">Qty</th>
                <th scope="col">Sat</th>
                <th scope="col">Harga</th>
                <th scope="col">Diskon</th>
                <th scope="col">Sub Total</th>
                <th scope="col">No. PR</th>
              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=8 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>
<div class="container-fluid mt-3">
  <div class="row">

    <!-- Total = jumlah Subtotal seluruh item -->
    <div class="col">
      <div class="form-group">
        <label>Total</label>
        <input type="text" class="form-control text-right" id="input_detail_total" value="0.00" disabled>
      </div>
    </div>

    <div class="col">
      <div class="form-group">
        <label>Disc %</label>
        <input type="text" class="form-control text-right" id="input_detail_disc" value="0.00" disabled>
      </div>
    </div>

    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="text" class="form-control text-right" id="input_detail_discrp" value ="0.00" disabled>
      </div>
    </div>

    <div class="col">
      <div class="form-group">
        <label>DPP</label>
        <input type="text" class="form-control text-right" id="input_detail_dpp" value="0.00" disabled>
      </div>
    </div>

    <div class="col">
      <div class="form-group">
        <label>PPN</label>
        <input type="text" class="form-control text-right" id="input_detail_ppnTotal" value="0.00" disabled>
      </div>
    </div>

    <div class="col">
      <div class="form-group">
        <label>Grand Total</label>
        <input type="text" class="form-control text-right" id="input_detail_grandtotal" value="0.00" disabled>
      </div>
    </div>

  </div>









      <!-- <div class="line"></div> -->
      <!-- <hr/> -->
    </div>
  </div>
  <!-- </div> -->


  <!-- ADD EDIT -->


  <!-- </div> -->



      </div>

      <!-- <div class="row "> -->

  <!-- </div> -->









    </div>



  </div>{{-- /#formBsGrid --}}
  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade picker-kas" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div id="" class="modal-content ">


      <div id= "modalAddListCustomer" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_customer">
              <thead>
                <tr>
                  <th scope="col">Kode</th>
                  <th scope="col">Nama</th>
                  <th scope="col">Kota</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_customer" class="text-left" >
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn picker-kas-batal" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>

      <div id= "modalAddListNoInvoice" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">No Beli</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:0px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_noinvoice">
              <thead>
                <tr>
                  <th scope="col">No Bukti</th>
                  <th scope="col">Tanggal</th>
                  <th scope="col">No SO</th>
                  <th scope="col">Gudang</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_noinvoice" class="text-left" >
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn picker-kas-batal" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>

      <div id= "modalAddListNoBeli" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">No Beli</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:0px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_nobeli">
              <thead>
                <tr>
                  <th scope="col">No Bukti</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_nobeli" class="text-left" >
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn picker-kas-batal" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


      <div id= "modalAddListBarang" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang">
              <thead>
                <tr>
                  <th scope="col">Kode Brg</th>
                  <th scope="col">Nama Brg</th>

                  <th scope="col">Qty Sisa</th>

                  <th scope="col">Satuan</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_barang" class="text-left" >
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn picker-kas-batal" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>



      </div>







    </div>
  </div>

<!-- End modal add-->

<!-- modal filter status otorisasi Invoice Retur Beli -->
<div class="modal fade rt-filter" id="modalFilterIRB">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Invoice Retur Beli
          <span class="rt-active-badge" id="irbFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterIRB').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="irbModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="irbModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="irbResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterIRB').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="irbTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter status otorisasi Invoice Retur Beli -->

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

// let tempNoBukti = ''



let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''

let noPBLTemp = ''

// Dipatok, bukan diambil dari window.location - sama alasannya dengan UMB_HREF di
// uangmukabeli.blade.php: harus sama persis dengan $req->href yang dikirim
// InvoiceReturBeliController@loadAll ke HeaderTableController@getHeaderTable.
const IRB_HREF = 'invoicereturbeli'

let irbCart = []
let dataIRB = []

function irbBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let tipeNama = { 0 : 'varchar', 1 : 'float', 2 : 'date' }
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
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

// Kolom yang tampil. WAJIB hasil filter() dari cart, bukan map/salinan, karena
// ReportTable.headHtml() memakai indexOf() terhadap gcart_header untuk mendapat
// indeks global tiap kolom.
function irbKolomTampil () {
  return (irbCart || []).filter(c => Number(c[2]) === 1)
}

function irbKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

// formatAngka() selalu menempelkan '.' + bagian desimal, sehingga input tanpa titik jadi
// "123.undefined". Dipakai versi yang sadar jumlah desimal, sama seperti umbFormatAngkaDes()
// di uangmukabeli.blade.php.
function irbFormatAngkaDes (nilai, des) {
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

function irbRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return irbFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Kalau public/js/report-table.js belum ikut terunggah, halaman harus tetap tampil:
// judul kolomnya jatuh ke <th> biasa, hanya tanpa drag & roda gigi.
function irbHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

// Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman.
// report-table.js tidak punya teardown; render ulang selanjutnya hanya menulis ulang
// innerHTML-nya (lihat renderTabelIRB()) - sama seperti uangmukabeli.blade.php.
let irbRtSudahInit = false

function irbInitReportTableSekali () {
  if (irbRtSudahInit || typeof ReportTable === 'undefined') { return }
  irbRtSudahInit = true

  ReportTable.init({
    table    : '#tabel2',
    bar      : '#rtBar',
    onChange : renderTabelIRB
  })

  // DataTables memasang handler sort LANGSUNG di tiap <th>, sedangkan roda gigi/drag milik
  // report-table.js didelegasikan di <thead>. Tanpa penanganan khusus, klik roda gigi juga
  // memicu sort DataTables - hentikan event ASLINYA di fase capture, lalu tembakkan ulang
  // satu event click baru langsung ke <thead> dengan target di-override.
  let irbGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (irbGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      irbGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      irbGuardUlangKlik = false
    }, true)
  }
}

// Pastikan #rtBar duduk tepat sebelum tabel (sibling, bukan anak di dalam wrapper DataTables) -
// kalau di dalam, .DataTable().destroy() yang menulis ulang wrapper akan ikut menghapusnya.
function irbPindahBar () {
  let bar = document.getElementById('rtBar')
  let tabel = document.getElementById('tabel2')
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#tabel2')) {
    acuan = document.getElementById('tabel2_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

// Ikat kotak search custom (#irbSearch, statis di blade - di luar #tabel2_wrapper jadi
// tidak ikut terhapus saat .DataTable().destroy() menulis ulang wrapper). Diikat sekali
// lewat dataset.rtBound karena renderTabelIRB() memanggil ini tiap kali tabel di-destroy+init.
function irbIkatSearch () {
  let input = document.getElementById('irbSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

// Jumlah baris per halaman, dikendalikan dropdown #irbLen. Disimpan di variabel, bukan
// hanya dibaca dari elemen select-nya, karena renderTabelIRB() melakukan destroy+init tiap
// kali kolom digeser/disembunyikan - tanpa ini tabel selalu balik ke nilai awal walau
// dropdownnya masih menunjuk pilihan pengguna. Nilai -1 berarti "semua data".
let irbPanjangHalaman = 10
function irbIkatPanjangHalaman () {
  let sel = document.getElementById('irbLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(irbPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    irbPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(irbPanjangHalaman).draw()
  })
}

// Ubah salah satu tanggal periode -> muat ulang data dari server (loadAll() sudah aman
// dipanggil ulang, tidak ada penjaga "sudah dimuat").
function irbIkatPeriode () {
  let awal  = document.getElementById('irbTglAwal')
  let akhir = document.getElementById('irbTglAkhir')
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

// Kotak scroll tabel dibuat setinggi sisa ruang di #content, sama seperti
// umbAturTinggiTabel() di uangmukabeli.blade.php.
function irbAturTinggiTabel () {
  let area = document.getElementById('content')
  let wrap = document.querySelector('#page1 .po-table-wrap')
  if (!area || !wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let pageEl = document.getElementById('page1')
  let bawah = pageEl.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

// 'SEMUA' = tidak menyaring. Disimpan di luar renderTabelIRB() supaya tetap berlaku saat
// tabel digambar ulang (sehabis simpan, otorisasi, dst).
let irbFilterOtorisasi = 'SEMUA'

function irbOtorisasiIRB (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

function irbUpdateFilterBadge () {
  let jml = (irbFilterOtorisasi !== 'SEMUA') ? 1 : 0
  let badge = document.getElementById('irbFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function irbTerapkanFilter () {
  irbFilterOtorisasi = $('#irbModalOtorisasi').val() || 'SEMUA'
  irbUpdateFilterBadge()
  $('#modalFilterIRB').modal('hide')
  renderTabelIRB()
}

function irbResetFilter () {
  irbFilterOtorisasi = 'SEMUA'
  $('#irbModalOtorisasi').val('SEMUA')
  irbUpdateFilterBadge()
  $('#modalFilterIRB').modal('hide')
  renderTabelIRB()
}

/* ---- Jembatan ke mesin penyimpan milik report-table.js ----
 * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
 * didefinisikan: report-table.js sudah punya fallback yang memutasi gcart_header sendiri
 * lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke doSimpanHeader di bawah.
 */
window.g_href = IRB_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  irbCart.forEach((c) => {
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
      href     : IRB_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Dipakai tombol "Reset kolom" di bar. Tombol itu hanya muncul kalau fungsi ini ada.
// Harus async:false karena report-table.js langsung menggambar ulang setelahnya.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : IRB_HREF,
      reset  : 1
    },
    success : function (res) {
      irbCart = irbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = irbCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

$(document).ready(function(){
        irbInitReportTableSekali()
        pickerKasInit('tabel_add_list_customer')
        pickerKasInit('tabel_add_list_noinvoice')
        pickerKasInit('tabel_add_list_barang')

      pickerKasInit('tabel_add_list_nobeli')




  //   formAddListItem
});

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('invoicereturbelispnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function cleanFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddNoBeli").value = ''
  document.getElementById("AddAddUrutBeli").value = 0
  document.getElementById("AddAddInputQty").value = '0.00'
  document.getElementById("AddAddInputNosat").value = 0

  document.getElementById("AddAddInputIsi").value = 0



  document.getElementById("AddAddKeterangan").value = ''
  tempBarangAddAdd = {}


}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanFormAdd () {


  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_noinvoice").value = ''
  document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_gudang").value = ''




  // document.getElementById("input_add_tanggalpo").valueAsDate = new Date()
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_kodepelanggan").value = ''
  // document.getElementById("input_add_namapelanggan").value = ''
  // document.getElementById("input_add_alamatpelanggan").value = ''
  // document.getElementById("input_add_kodealamatkirim").value = ''
  // document.getElementById("input_add_alamatkirim").value = ''
  // document.getElementById("input_add_kodepic").value = ''
  // document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodelokasipenerima").value = ''
  // document.getElementById("input_add_alamatlokasipenerima").value = ''
  // document.getElementById("input_add_catatan").value = ''
  // document.getElementById("input_add_valas").value = ''
  // document.getElementById("input_add_kurs").value = ''
  // document.getElementById("input_add_dp").value = '0.00'
  // document.getElementById("input_add_nopo").value = ''
  // document.getElementById("input_add_kodebackoffice").value = ''
  // document.getElementById("input_add_namabackoffice").value = ''
  // document.getElementById("input_add_tipeppn").value = 0
  // document.getElementById("input_add_pembayaran").value = 0
  // document.getElementById("input_add_kodesales").value = ''
  // document.getElementById("input_add_namasales").value = ''
  // document.getElementById("input_add_hari").value = 0
  // document.getElementById("input_add_draftpo").value = 0
  //
  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_dp").disabled = false
  // document.getElementById("input_add_nopo").disabled = false
  // document.getElementById("input_add_catatan").disabled = false
  // document.getElementById("input_add_tanggalpo").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false
  //
  // document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListAlamatKirim").disabled = false
  // document.getElementById("buttonAddListSales").disabled = false
  // document.getElementById("buttonAddListValas").disabled = false
  // document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  // document.getElementById("buttonAddListBackOffice").disabled = false
  //
  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false
  //
  // document.getElementById("input_add_disc").value = '0.00'
  // document.getElementById("input_add_discrp").value = '0.00'
  // document.getElementById("input_add_ppn").value = '0.00'
  // document.getElementById("input_add_dpp").value = '0.00'
  // document.getElementById("input_add_grandtotal").value = '0.00'
}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()

  console.log(nobukti , tanggal , nopajak)

}


function submitAddEdit () {
  let barang = tempBarangAddEdit

  let _token  = $("#_token").val()

  let choice = "U"
  let urut = barang.Urut
  let jmlrecord = 1
  let nobukti  = $("#input_add_nobukti").val()

  let DISCP = $("#input_add_add_discpersen1").val();
  let DiscP2 = $("#input_add_add_discpersen2").val();
  let DiscP3 = $("#input_add_add_discpersen3").val();
  let harga = bersihkanAngka($("#AddEditHarga").val());

  // Catatan: field Qty/Nosat pada form ini disabled (lihat buttonAddEditItem) dan
  // InvoiceReturBeliController@spAdd hanya menyimpan harga + 3 diskon, jadi hanya
  // field itu yang dikirim - mengirim field qty/isi lain hanya menambah kebingungan
  // karena tidak pernah dipakai server.
  $.ajax({
    url: "{!! url('invoicereturbelispadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      urut,
      jmlrecord,
      harga,
      DISCP,
      DiscP2,
      DiscP3
    },
    success: function(res) {
      console.log('resspadd', res)
      if (res == 1) {
        $('.showhideitem').hide();
        loadAll()
        refreshDataTableAdd(nobukti)
        alertify.success('Berhasil menyimpan item')
      } else {
        alertify.warning('Gagal menyimpan item')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddDeleteItem (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let barang = dataBarang[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NamaBrg + ' ?',
      function() {
        let _token = $("#_token").val();

        let nobukti = barang.NoBukti
        let urut = barang.Urut
        console.log(nobukti, urut)
        $.ajax({
          url: "{!! url('invoicereturbelispdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
            urut,
            nourut: barang.NoUrut,
            tanggal: barang.Tanggal,
            kodesupp: barang.KodeSupp,
            kodegdg: barang.KodeGdg,
            noout: barang.NoPRB,
            keterangan: '',
            faktursupp: barang.FakturSupp,
            kodebrg: barang.KodeBrg,
            urutout: barang.UrutPRB,
            nosat: barang.NoSat,
            satuan: barang.Satuan,
            isi: barang.Isi,
            flagtipe: barang.FlagTipe,
            nobatch: '',
            nolpb: 0
          },
          success: function(res) {
            if (res == 1) {
              $('.showhideitem').hide();
              loadAll()
              refreshDataTableAdd(nobukti)
              alertify.success('Berhasil menghapus item')
            } else {
              alertify.warning('Gagal menghapus item - kemungkinan dokumen sudah diotorisasi')
            }
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

function submitAddAdd () {

  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let barang = tempBarangAddAdd

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()

  let choice = "I"
  let urut = 0

  let jmlrecord = 0

  if (tipeform == "edit") {
    jmlrecord = 1
  }

  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodecustsupp = $("#input_add_kodecustomer").val()
  let namacustsupp = $("#input_add_namacustomer").val()
  let alamatcustsupp = $("#input_add_alamatcustomer").val()
  let kodegdg = $("#input_add_gudang").val()
  let noinvoice = $("#input_add_noinvoice").val()
  let flagtipe = $("#input_add_flagtipe").val()
  let ppn = $("#input_add_ppn").val()
  let catatan = $("#input_add_catatan").val()

  let tanggal = $("#input_add_tanggal").val()
  let noso = $("#input_add_noso").val()
  let gudang = $("#input_add_gudang").val()

  let nobeli = $("#AddAddNoBeli").val()
  let urutbeli = $("#AddAddUrutBeli").val()
  let namabrg = $("#AddAddNamaBrg").val();
  let kodebrg = $("#AddAddKodeBrg").val();
  let qnt = $("#AddAddInputQty").val();

  let nosat = $("#AddAddInputNosat").val();
  let isi = 0
  if (nosat == 1) {
    isi = barang.Isi1
  } else if (nosat == 2) {
    isi = barang.Isi2
  }
  let urutinvoice = barang.Urut
  let qnt1 = $("#AddAddInputQty1").val();
  let sat1 = $("#AddAddInputSat1").val();
  let qnt2 = $("#AddAddInputQty2").val();
  let sat2 = $("#AddAddInputSat2").val();

  let ketdet = $("#AddAddKeterangan").val();
  console.log(kodebrg, kodecustsupp, noinvoice , nosat)
  if (!kodebrg || !kodecustsupp || !noinvoice || !nosat) {
    alertify.warning("Data tidak lengkap")
    return
  }
  if (Number(qnt) < 0) {
    alertify.warning("Qty kurang dari 0")
    return
  }

  if (Number(qnt) * Number(isi) > Number(barang.QntSisa) * Number(barang.Isi) ) {
    alertify.warning("Melebihi qty sisa")
    return
  }
  console.log('y')

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal,
    noinvoice,
    kodecustsupp,
    catatan,
    urut,
    urutinvoice,
    kodebrg,
    qnt,
    qnt1,
    qnt2,
    sat1,
    nosat,
    isi,
    jmlrecord,
    namabrg,
    kodegdg,
    flagtipe,
    ppn,
    noso,
    retursupp,
    sat2,
    ketdet,
    nobeli,
    urutbeli


  })

  $.ajax({
    url: "{!! url('invoicereturbelispadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      noinvoice,
      kodecustsupp,
      catatan,
      urut,
      urutinvoice,
      kodebrg,
      qnt,
      qnt1,
      qnt2,
      sat1,
      nosat,
      isi,
      jmlrecord,
      namabrg,
      kodegdg,
      flagtipe,
      ppn,
      noso,
      retursupp,
      sat2,
      ketdet,
      nobeli,
      urutbeli


    },
    success: function(res) {


      console.log('resspadd', res)
      // return
      if (res == 1) {



        tipeform = 'edit'
        lockFormAdd()
        $('.showhideitem').hide();
        // $('.showhideform').hide();
        // $('#modalAdd').show();
        loadAll()
        refreshDataTableAdd(nobukti)





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

function buttonAddListBarang () {

  console.log('buttonAddListBarang')


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();
  let noinvoice = $("#input_add_noinvoice").val();
  let noso = $("#input_add_noso").val();

  if (!kodecustsupp || !noinvoice ) {
    alertify.warning("Pilih supplier & invoice terlebih dahulu")
    return
  }

  $('#tabel_add_list_barang').DataTable().destroy();
  $.ajax({
    url: "{!! url('invoicereturbelilistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
      noinvoice
    },
    success: function(res) {

      listBarang = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickBarang(${i})">
        <td>${item.KodeBrg}</td>
        <td>${item.NamaBrg ? item.NamaBrg : item.NamaBrgx }</td>
        <td>${formatAngka(item.QntSisa)}</td>
        <td class="text-center">${item.Satuan}</td>
        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
      pickerKasInit('tabel_add_list_barang')
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListBarang').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListNoBeli () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();
  let noinvoice = $("#input_add_noinvoice").val();
  let noso = $("#input_add_noso").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if (!kodebrg ) {
    alertify.warning("Pilih barang terlebih dahulu")
    return
  }

  $('#tabel_add_list_nobeli').DataTable().destroy();
  $.ajax({
    url: "{!! url('invoicereturbelilistnobeli') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg,
      noso
    },
    success: function(res) {
      let rowTable = ``
      rowTable += `<tr class="pick-row" onclick="buttonAddPickNoBeli('-' , 0)">
      <td>-</td>
      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickNoBeli('${item.NOBUKTI}' ,${item.urut} )">
        <td>${item.NOBUKTI}</td>
        </tr>`
      });





      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_nobeli").innerHTML = rowTable
      pickerKasInit('tabel_add_list_nobeli')
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoBeli').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListNoInvoice () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp) {
    alertify.warning("Pilih supplier terlebih dahulu")
    return
  }

  $('#tabel_add_list_noinvoice').DataTable().destroy();
  $.ajax({
    url: "{!! url('invoicereturbelilistnoinvoice') !!}",
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
        <tr class="pick-row" onclick="buttonAddPickNoInvoice('${item.NOBUKTI}' , '${item.NoSO}' , '${item.KODEGDG}', ${item.flagtipe}, ${item.ppn})">
        <td>${item.NOBUKTI}</td>
        <td>${item.TANGGAL}</td>
        <td>${item.NoSO}</td>
        <td>${item.NAMAGDG}</td>
        </tr>`
      });




      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_noinvoice").innerHTML = rowTable
      pickerKasInit('tabel_add_list_noinvoice')
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoInvoice').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListCustomer () {
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('invoicereturbelilistcustomer') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}')">
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.NamaKota}</td>
        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      pickerKasInit('tabel_add_list_customer')
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListCustomer').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonAddPickNoBeli (nobeli, urutbeli) {
  console.log('buttonAddPickNoBeli')
  console.log(nobeli)
  document.getElementById("AddAddNoBeli").value = nobeli
  document.getElementById("AddAddUrutBeli").value = urutbeli
  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCustomer (kode, nama , alamat) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat
  document.getElementById("input_add_noinvoice").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_gudang").value = ''
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}
function onChangeQtyEdit () {

  console.log('onChangeQtyEdit')
  console.log('tempBarangAddEdit' , tempBarangAddEdit)

  let qty = $("#AddEditInputQty").val();
  let nosat = $("#AddEditInputNosat").val();
  if (jQuery.isEmptyObject(tempBarangAddEdit)) {
    console.log('g ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddEdit.ISI1 : tempBarangAddEdit.ISI2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddEditInputQty1").value = tempTotalQty / tempBarangAddEdit.ISI1
    document.getElementById("AddEditInputQty2").value = tempTotalQty / tempBarangAddEdit.ISI2


  }
}

function onChangeQty () {

  console.log('onChangeQty')
  console.log('tempBarangAddAdd' , tempBarangAddAdd)
  let qty = $("#AddAddInputQty").val();
  let nosat = $("#AddAddInputNosat").val();
  console.log('qty' , qty)
  console.log('nosat' , nosat)

  if (jQuery.isEmptyObject(tempBarangAddAdd)) {
    console.log('g ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddAdd.Isi1 : tempBarangAddAdd.Isi2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddAddInputQty1").value = tempTotalQty / tempBarangAddAdd.Isi1
    document.getElementById("AddAddInputQty2").value = tempTotalQty / tempBarangAddAdd.Isi2


  }

}

function buttonAddPickBarang (index) {
  console.log('buttonAddPickBarang')
  tempBarangAddAdd = listBarang[index]

  console.log('tempBarangAddAdd', tempBarangAddAdd)
  document.getElementById("AddAddKodeBrg").value = tempBarangAddAdd.KodeBrg
  document.getElementById("AddAddNamaBrg").value = tempBarangAddAdd.NamaBrg ? tempBarangAddAdd.NamaBrg : tempBarangAddAdd.NamaBrgx
  document.getElementById("AddAddInputQty").value = tempBarangAddAdd.QntSisa
  document.getElementById("AddAddInputQty1").value = tempBarangAddAdd.Qnt1Sisa
  document.getElementById("AddAddInputQty2").value = tempBarangAddAdd.Qnt2Sisa

  document.getElementById("AddAddInputSat1").value = tempBarangAddAdd.SAT1
  document.getElementById("AddAddInputSat2").value = tempBarangAddAdd.SAT2

  let selectOption = ''
  if (tempBarangAddAdd.SAT1) {
    selectOption += `<option value=1 ${tempBarangAddAdd.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddAdd.SAT1}</option>`
  }
  if (tempBarangAddAdd.SAT2) {
    selectOption += `<option value=2 ${tempBarangAddAdd.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddAdd.SAT2}</option>`
  }
  document.getElementById("AddAddInputNosat").innerHTML = selectOption






  buttonAddListBatal()
  // $("#form").modal('toggle')
}



function buttonAddPickNoInvoice (nobukti, noso , kodegdg, flagtipe, ppn) {
  console.log('buttonAddPickNoInvoice')
  document.getElementById("input_add_noinvoice").value = nobukti
  document.getElementById("input_add_noso").value = noso
  document.getElementById("input_add_gudang").value = kodegdg
  document.getElementById("input_add_flagtipe").value = flagtipe
  document.getElementById("input_add_ppn").value = ppn
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}


function buttonAddBatal () {

  $('.showhideitem').hide();
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

// function buttonAddListCustomer () {
//
//   $('.showhidemodalbodyadd').hide();
//   $('#modalBodyAddListValas').show();
//
//   $("#form").modal('toggle')
// }


function closeShowHideItem () {
  $('.showhideitem').hide();

}

function unlockFormAdd () {
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_tanggal").disabled = false



}

function lockFormAdd () {
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggal").disabled = true



}



function refreshDataTableAdd (NOBUKTI = "") {

    console.log('refreshDataTableAdd' , NOBUKTI)

    if (!NOBUKTI) {

    } else {
      let _token  = $("#_token").val()
      $.ajax({
        url: "{!! url('invoicereturbeligetdetail') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti: NOBUKTI
        },
        success: function(res) {
          dataHeaderAdd = res[0]
          console.log('aaa')
          console.log('res' , res)

          // res.header.forEach((item, i) => {
          //   console.log('a' , i)
          // });
          if (res.length == 0) {
            alertify.warning("Data habis")
            // $("#form").modal('toggle')
            buttonCloseForm()
          } else {
            dataBarang = res

            let rowTable = ""
            res.forEach((item, i) => {

              rowTable += `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>

              <td class="text-center">${item.Satuan ? item.Satuan : item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
              <td class="text-center">
                <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>
              </td>

              </tr>`


            });


            document.getElementById("addTableData").innerHTML = rowTable

            noPBLTemp = res[0].NoPBL

            document.getElementById("input_add_kodecustomer").value = res[0].KodeSupp
            document.getElementById("input_add_namacustomer").value = res[0].NamaCustSupp
            document.getElementById("input_add_alamatcustomer").value = res[0].Alamat
            document.getElementById("input_add_noinvoice").value = res[0].NoPBL
            document.getElementById("input_add_noso").value = res[0].MyPPN

            document.getElementById("input_add_bayar").value = res[0].MYBAYAR
            document.getElementById("input_add_valas").value = res[0].KodeVls
            document.getElementById("input_add_kurs").value = res[0].Kurs
            document.getElementById("input_add_hari").value = res[0].Hari
          
            document.getElementById("input_add_gudang").value = res[0].KodeGdg
            document.getElementById("input_add_catatan").value = res[0].Keterangan
            document.getElementById("input_add_flagtipe").value = res[0].FlagTipe
            document.getElementById("input_add_ppn").value = res[0].PPN

            document.getElementById("input_add_nobukti").value = res[0].NoBukti
            document.getElementById("input_add_nourut").value = res[0].NoUrut

            // Total = jumlah Subtotal (Total) seluruh item.
            document.getElementById('input_add_total').value = formatAngka(res.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2));
            document.getElementById('input_add_grandtotal').value = formatAngka(parseFloat(res[0].TotalNetto || 0).toFixed(2));
            document.getElementById('input_add_ppnTotal').value = formatAngka(parseFloat(res[0].TotalPPn || 0).toFixed(2));
            document.getElementById('input_add_dpp').value = formatAngka(parseFloat(res[0].TotalDPP || 0).toFixed(2));

            document.getElementById('input_add_disc').value = formatAngka(parseFloat(res[0].Disc || 0).toFixed(2));
            // DiscRp diambil dari kolom Diskon (total NDiskon dbRBeliDet lewat vwMasterRBeli).
            // Sebelumnya dihitung TotalDPP x Disc% - salah, karena DPP sudah nilai setelah diskon.
            document.getElementById('input_add_discrp').value = formatAngka(parseFloat(res[0].Diskon || 0).toFixed(2));
          }



          // res.list.forEach((item, i) => {
          //   console.log('b' , i)
          // });



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

function buttonDetail (nobukti) {
  console.log('buttonDetail' , nobukti)

    let _token  = $("#_token").val()

    let rowTable = ""
    $.ajax({
      url: "{!! url('invoicereturbeligetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: nobukti
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

          if(res.length) {
            let rowTable = ""
            res.forEach((item, i) => {

              rowTable += `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>

              <td class="text-center">${item.Satuan ? item.Satuan : item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>

              </tr>`
            });

            // if(!dataTableAdd.length) {
            //   rowTable = `<tr>
            //   <td class="text-center" colspan="8">Belum ada barang</td>
            //   </tr>`
            // }
            document.getElementById("detailTableData").innerHTML = rowTable

            document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
            document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
            document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat
            document.getElementById("input_detail_noinvoice").value = res[0].NoPBL
            document.getElementById("input_detail_noso").value = res[0].MyPPN
            document.getElementById("input_detail_bayar").value = res[0].MYBAYAR
            document.getElementById("input_detail_valas").value = res[0].KodeVls
            document.getElementById("input_detail_kurs").value = res[0].Kurs
            document.getElementById("input_detail_hari").value = res[0].Hari

            document.getElementById("input_detail_gudang").value = res[0].KodeGdg
            document.getElementById("input_detail_catatan").value = res[0].Keterangan
            document.getElementById("input_detail_flagtipe").value = res[0].FlagTipe
            document.getElementById("input_detail_ppn").value = res[0].PPN

            document.getElementById("input_detail_nobukti").value = res[0].NoBukti
            document.getElementById("input_detail_nourut").value = res[0].NoUrut

            // Total = jumlah Subtotal (Total) seluruh item.
            document.getElementById('input_detail_total').value = formatAngka(res.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
            document.getElementById('input_detail_grandtotal').value = formatAngka(parseFloat(res[0].TotalNetto || 0).toFixed(2))
            document.getElementById('input_detail_ppnTotal').value = formatAngka(parseFloat(res[0].TotalPPn || 0).toFixed(2))
            document.getElementById('input_detail_dpp').value = formatAngka(parseFloat(res[0].TotalDPP || 0).toFixed(2))

            // Ditulis ke field halaman Detail sendiri (sebelumnya salah menulis ke
            // #input_add_disc/#input_add_discrp milik halaman Edit yang mungkin sedang
            // menampilkan dokumen lain).
            document.getElementById('input_detail_disc').value = formatAngka(parseFloat(res[0].Disc || 0).toFixed(2))
            // DiscRp diambil dari kolom Diskon (total NDiskon dbRBeliDet lewat vwMasterRBeli) -
            // kolom header dbRBeli.DiscRp tidak pernah terisi (selalu 0).
            document.getElementById('input_detail_discrp').value = formatAngka(parseFloat(res[0].Diskon || 0).toFixed(2))

          } else {
            alertify.warning("Data tidak ditemukan")
          }



      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

  $('.mainpage').hide();
  $('#page3').show();
}

function buttonEdit (nobukti) {
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
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  // Cek status otorisasi lebih dulu, sebelum data ditarik ke form - supaya dokumen yang
  // sudah diotorisasi tidak sempat terisi ke layar Edit.
  let sudahOtorisasi = false
  $.ajax({
    url: "{!! url('invoicereturbeligetdetail') !!}",
    type: "post",
    async: false,
    data: { _token: $("#_token").val(), nobukti },
    success: function (res) {
      if (res.length && Number(res[0].IsOtorisasi1) === 1) {
        sudahOtorisasi = true
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (sudahOtorisasi) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  tipeform = 'edit'
  lockFormAdd()
  $('.showhideitem').hide();
  refreshDataTableAdd(nobukti)

  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAdd (nobukti) {
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
  console.log('buttonAdd' , nobukti)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  document.getElementById("addTableData").innerHTML = `<td colspan=9 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  unlockFormAdd()
  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  setNewNoBukti()

  $('.mainpage').hide();
  $('#page2').show();

}

function buttonAddAddItem () {

    $('.showhideitem').hide();
    cleanFormAddAdd()
    $('#formAddAdd').show();

}

function buttonAddEditItem (i) {
  tempBarangAddEdit = dataBarang[i]
  console.log(tempBarangAddEdit)

  document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.KodeBrg
  // document.getElementById("AddEditReturSupp").value = tempBarangAddEdit.FlagKembali
  document.getElementById("AddEditNoBeli").value = tempBarangAddEdit.NoPBL
  document.getElementById("AddEditUrutBeli").value = tempBarangAddEdit.UrutPBL
  document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NamaBrg
  document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.Qnt).toFixed(2)

  document.getElementById("AddEditInputQty1").value = parseFloat(tempBarangAddEdit.qnt1).toFixed(2)
  document.getElementById("AddEditInputQty2").value = parseFloat(tempBarangAddEdit.qnt2).toFixed(2)
  document.getElementById("AddEditInputSat1").value = tempBarangAddEdit.sat1
  document.getElementById("AddEditInputSat2").value = tempBarangAddEdit.sat2

  
  // parseFloat dulu - nilai dari SQL Server datang sebagai '.00' (tanpa angka 0 di depan),
  // sehingga kalau langsung dipasang, field-nya tampil '.00' bukan '0.00'.
  document.getElementById("input_add_add_discpersen1").value = parseFloat(tempBarangAddEdit.DiscP || 0).toFixed(2)
  document.getElementById("input_add_add_discpersen2").value = parseFloat(tempBarangAddEdit.Discp2 || 0).toFixed(2)
  document.getElementById("input_add_add_discpersen3").value = parseFloat(tempBarangAddEdit.Discp3 || 0).toFixed(2)

  document.getElementById("AddEditHarga").value = formatAngka(parseFloat(tempBarangAddEdit.Harga).toFixed(2))

  let selectOption = ''
  if (tempBarangAddEdit.sat1) {
    selectOption += `<option value=1 ${tempBarangAddEdit.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddEdit.sat1}</option>`
  }
  if (tempBarangAddEdit.sat2) {
    selectOption += `<option value=2 ${tempBarangAddEdit.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddEdit.sat2}</option>`
  }
  document.getElementById("AddEditInputNosat").innerHTML = selectOption

  document.getElementById("AddEditInputNosat").disabled = true
  document.getElementById("AddEditInputQty").disabled = true


// $.ajax({
//     url: "{!! url('invoicereturbeligetLPBdetail') !!}",
//     type: "get",
//     async: false,
//     data: {
//       nobukti : noPBLTemp

//     },
//     success: function(res) {
//       console.log(res)
//       // document.getElementById('AddEditHarga').value = res[0].HARGA
//       // document.getElementById('AddEditDiskon').value = res[0].DISC

//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Gagal mendapatkan data LPB')
//     }

//   })

  $('.showhideitem').hide();
  $('#formAddEdit').show();

}

function formatAngka (angkaString) {
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  if (tempAngka[1] !== undefined) {
    temp1 += '.' + tempAngka[1]
  }
  return temp1
};

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

const formatTanggal = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}-${month}-${year}`;
};

function loadAll () {
  // Indikator "sedang memuat" - pola umum di proyek ini untuk aksi yang bisa memakan
  // waktu, supaya pengguna tahu aplikasinya sedang bekerja dan tidak mengira layarnya
  // menggantung (lihat loadingHtml() di public/js/report-table.js).
  document.getElementById('tabel_data').innerHTML =
    '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>'

  $.ajax({
    url: "{!! url('invoicereturbeliloadall') !!}",
    type: "get",
    async: true,
    data: {
      tglawal: $('#irbTglAwal').val(),
      tglakhir: $('#irbTglAkhir').val()
    },
    success: function (res) {
      irbCart = irbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = irbCart
      dataIRB = res.listData1 || []
      renderTabelIRB()
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function renderTabelIRB () {
  window.g_modeReport = 1
  window.gcart_header = irbCart

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  // Satu daftar kolom untuk header DAN isi baris, supaya jumlah kolomnya selalu sama.
  let cols = irbKolomTampil()
  let kolomRender = cols.map(irbKolomRender)

  // <thead> HANYA ditulis ulang innerHTML-nya, elemennya sendiri tidak diganti -
  // sudah diikat sekali oleh irbInitReportTableSekali().
  let thead = document.getElementById('tabel_header')
  thead.innerHTML = irbHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataIRB || []
  if (irbFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return irbOtorisasiIRB(item) === irbFilterOtorisasi })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let isOtorisasi = Number(item.IsOtorisasi1) || 0

    // Tombol aksi ikut status otorisasi barisnya - menggantikan pemisahan lewat tab
    // "Invoice Retur Beli" / "Sudah Otorisasi" yang dipakai tampilan lama.
    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${item.NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${item.NoBukti}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('${item.NoBukti}' , 'edit')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${irbRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${irbRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${isOtorisasi ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${item.OtoUser1 || ''}</td>
      <td>${item.TglOto1 ? formatTanggal(item.TglOto1) : ''}</td>
    </tr>`
  });

  // Baris "Tidak ada data" TIDAK ditulis manual di sini - baris manual hanya berisi 1 sel
  // sedangkan header punya banyak kolom, dan DataTables mencoba mengindeks sel-sel yang
  // tidak ada di situ lalu crash (_DT_CellIndex). Biarkan <tbody> kosong dan serahkan ke
  // opsi language.emptyTable di bawah - DataTables sendiri yang menggambar baris kosongnya
  // dengan colspan yang benar.
  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: irbPanjangHalaman,
    // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom
    // Actions). Data sudah datang terurut dari server (Tanggal/NoBukti terbaru dulu).
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  irbPindahBar()
  irbIkatSearch()
  irbIkatPanjangHalaman()
  irbIkatPeriode()
  // Init DataTable di atas mereset filter pencarian - kotak #irbSearch sendiri statis
  // di blade dan nilainya tidak ikut hilang, jadi diterapkan ulang di sini.
  let inputSearch = document.getElementById('irbSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
  irbAturTinggiTabel()
}



function buttonBatalOtorisasi (nobukti) {

console.log( $("#akses_isbatal").val());
let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access batal');
    return;
  }

  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {},
    success: function(res) {
      if (res.length) { pcekglobal = 1 }
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

alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          return;
        }
    let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('invoicereturbelispbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          otorisasi: 0,
          pket :value
        },
        success: function (res) {
          if (res > 0) {
            alertify.success('Berhasil batal otorisasi');
            loadAll();
          } else {
            alertify.warning('Gagal batal otorisasi');
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
        }
      });
  },
  function() {
    alertify.error("Action cancelled");
  }
);
}


function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {},
    success: function(res) {
      if (res.length) { pcekglobal = 1 }
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

  // Otorisasi langsung memicu posting hutang & jurnal - perlu konfirmasi eksplisit,
  // bukan langsung jalan begitu tombol diklik.
  alertify.confirm('Otorisasi', 'Otorisasi dokumen ' + nobukti + '? Proses ini akan langsung memposting jurnal.',
    function() {
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('invoicereturbelispotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti
        },
        success: function(res) {
          console.log(res)
          if (res > 0) {
            alertify.success('Berhasil update otorisasi')
            loadAll()
          } else {
            alertify.warning('Gagal otorisasi - kemungkinan dokumen sudah diotorisasi')
          }
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }

      })
    },
    function () {
      console.log('batal otorisasi')
    }
  );
}

// Field Disc %, DiscRp, DPP, PPN dan Grand Total ditampilkan dengan pemisah ribuan
// (formatAngka), jadi nilainya harus dibersihkan dulu dari koma sebelum dihitung atau
// dikirim ke server - kalau tidak, parseFloat('50,000.00') hanya terbaca 50.
function bersihkanAngka (nilai) {
  return parseFloat(String(nilai == null ? '' : nilai).replace(/,/g, '')) || 0
}

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = bersihkanAngka($("#input_add_disc").val())
      console.log(value)
      onChangeHeader('DISC' , value)
      // refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
      loadAll()
    }
}

function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = bersihkanAngka($("#input_add_discrp").val())
        console.log(dataHeaderAdd)
        // Persentase = DiscRp / Total (jumlah Subtotal seluruh item, sebelum diskon).
        // Jangan dibagi DPP - DPP sudah nilai setelah diskon, hasil persennya jadi kebesaran.
        let total = bersihkanAngka($("#input_add_total").val())
        let x = total ? (value * 100) / total : 0
        console.log(x)
        console.log(value)
        onChangeHeader('DISC' , x)
        // refreshUpdateHeader()
        let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)
        loadAll()
      }
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('invoicereturbelionchangeheader') !!}",
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

function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}

function submitPrint (nobukti) {

    let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('invoiceReturBeliCetak') !!}",
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
    // let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];

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
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">`+dataPrint[0].NAMACUSTSUPP+`</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">NOTA RETUR</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NOMOR</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+dataPrint[0].NoBukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">PO. NO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOPOHD}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">INVOICE</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%"></div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">NO.</td>
                    <td class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td class="text-center" style="width: 5%">PART NUMBER</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">HARGA SATUAN</td>
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
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NamaBrg}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.PartNumber}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${itemSub.Qnt ? formatAngka(itemSub.Qnt) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${formatAngka(parseFloat(itemSub.Harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${formatAngka(parseFloat(itemSub.Total).toFixed(2))}</td>
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
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Terbilang:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100% font-size:12px;">`+dataPrint[0].Terbilang+`</div>
    </div>
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">DIBUAT OLEH,</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
        <td class="no-border text-center" style="width: 33%; font-size:13px;">MENGETAHUI,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0""></p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2 text-center">
          <p class="m-0" style='font-size:12px;'>FENNY SAGITHA</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2 text-center">
          <p class="m-0" style="font-size:12px;">EVY YUSIA</p>
        </td>
      </tr>
    </table>
  </div>

  <div style="width: 40%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TotalUSD).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> DPP </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NDPP).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> PPN </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NPPN).toFixed(2))}</div>
    </div>

  </div>

</div>

         <div class="footer-print-date" style='margin-bottom:-100px;'>
            <table class="m-0" style="width: 100% ; font-family: sans-serif;
            font-size: 10px ">
              <tr>
                <td class="no-border" white-space:pre;>-Permasalahan : Barang yang disupply tidak sesuai dengan permintaan user.</td>
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


window.onload = function(){
  loadAll();
};


</script>




@endsection
