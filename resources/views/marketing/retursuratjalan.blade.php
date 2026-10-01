@extends('newmasterTest')
@section('buttons')


@section('page-title', 'Retur Surat Jalan')
@section('title', 'SML - Retur Surat Jalan')

@endsection

{{-- Rerouted from the shared report-table.css/rsj-table-header.css/headerEngine.js
     implementation to the same self-contained po-table-header.css + inline-engine
     pattern used by so.blade.php/invoicejasa/fakturpajak/cetaktandaterima/
     perintahreturjual/returpenjualangudang/kreditnote/notareturpenjualan/
     perintahreturjualminus, for source-level consistency across the marketing
     folder -- the resulting UI is unchanged (this page's tab bar/toolbar/table
     skeleton already matched the target 1:1), only the underlying mechanism. --}}
@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Modal pemilih (Barang/SJ/Customer) disamakan persis dengan modal pemilih menu Kas - lihat public/css/picker-kas.css. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

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
    display: inline-flex !important;
    align-items: center;
    padding: 5px 16px !important;
    font-size: 0.75rem !important;
    border: none;
    border-radius: 17px;
    color: #495057;
    background: transparent;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
  }

  .custom-tabs .nav-link:hover {
    background: transparent;
    color: #007bff;
    text-decoration: none;
  }

  .custom-tabs .nav-link.active {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
  }

  /* newmaster.css punya rule .card global (align-items:center, dibuat untuk kartu menu
     dashboard) yang menimpa ini kalau tidak ditulis ulang di sini. */
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

  /* Dropdown "Tampilkan" di toolbar, meniru .po-len-wrap milik so.blade.php/
     purchaseOrder.blade.php 1:1. */
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

  /* Form label look (page2 Add form, page3 Detail form) copied 1:1 from so.blade.php's
     own #page2/#page3 label rule -- bold uppercase Poppins, matching Form Sales Order's
     input labels exactly. margin-bottom:10px here loses to .form-label.mb-0's
     !important on the row/col fields that use align-items-center (same interaction
     so.blade.php's own page2 already relies on), so the tight row alignment isn't
     affected. */
  #page2 label,
  #page3 label {
    display: inline-block;
    font-size: 13px;
    font-weight: 700;
    font-family: "Poppins", sans-serif;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #555;
    margin-bottom: 10px;
    cursor: pointer;
  }
</style>

<style>
  /* {{-- Kolom Aksi tabel/tabel2 -- pastel round-button treatment, copied and
       rescoped to this page's own #tabel/#tabel2 from so.blade.php's @section('css'). --}} */
  #tabel td:first-child,
  #tabel2 td:first-child {
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

  #tabel td:first-child .btn-warning,
  #tabel2 td:first-child .btn-warning {
    color: #b45309; border-color: #fbe3bd; background: #fef3e0;
  }

  #tabel td:first-child .btn-success {
    color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
  }

  #tabel td:first-child .btn-primary,
  #tabel2 td:first-child .btn-primary {
    color: #2563eb; border-color: #cfdcff; background: #e8edff;
  }

  #tabel td:first-child .btn-danger,
  #tabel2 td:first-child .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
  }

  #tabel thead th,
  #tabel2 thead th {
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

  /* Kolom Aksi #addTable (Add Item cart di page2) -- pastel round-button treatment,
     sama seperti #tabel/#tabel2 di atas, cuma di sini tombolnya ada di td TERAKHIR
     (bukan first-child) karena kolom Actions memang ditaruh paling kanan. */
  #addTable td:last-child {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
  }

  #addTable td:last-child .btn {
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

  #addTable td:last-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #addTable td:last-child .btn-success {
    color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
  }

  #addTable td:last-child .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
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

{{-- tampilan search bar modal add pelanggan --}}
<style>

  #tabel_add_list_nosj_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_nosj_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>

<style>

  #tabel_add_list_custsupp_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_custsupp_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>
{{-- end tampilan search bar modal add pelanggan --}}
<style>
  /* Search-icon button appended flush to an input, ported from so.blade.php. */
  .btn-icon-search {
    height: 32px;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }

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
</style>
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

  /* Hide action buttons until the row is hovered */
  #tabel tbody .action-buttons-wrap,
  #tabel2 tbody .action-buttons-wrap {
    opacity: 0;
    visibility: hidden;
    transform: translateX(-6px);
    transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
  }
  /* Show them when hovering the table row */
  #tabel tbody tr:hover .action-buttons-wrap,
  #tabel2 tbody tr:hover .action-buttons-wrap,
  #tabel tbody tr:focus-within .action-buttons-wrap,
  #tabel2 tbody tr:focus-within .action-buttons-wrap {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
  }

  .data-table th,
  .data-table td {
    white-space: nowrap;
  }
</style>
{{-- end tampilan search modal barang all --}}

@endsection


@section('content')
<div id="page1" class="container-fluid">

<div id="printContainer" style="display:none">


</div>
<div id="contentContainer" class="container-fluid" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  {{-- Belum/Sudah Otorisasi digabung jadi satu tabel dengan periode+filter, port
       1:1 dari pola kreditnote.blade.php/invoicejasa.blade.php (2 tab, tanpa tab
       "Outstanding" terpisah, sehingga hasil gabungannya langsung satu card tanpa
       tab bar lagi). --}}
  <div class="modal fade rt-filter" id="modalFilterRSJ">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-funnel"></i>
            Filter Data
            <span class="rt-active-badge" id="rsjFilterBadge">0 aktif</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterRSJ').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-group-label">Status</div>
            <div>
              <label class="rt-field-label" for="input_filterrsj">Status Otorisasi</label>
              <select class="rt-native" id="input_filterrsj">
                <option value=0 selected>Semua</option>
                <option value=1>Belum Otorisasi</option>
                <option value=2>Sudah Otorisasi</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="rt-reset-link" onclick="rsjResetFilterFields()">Reset semua</button>
          <div class="rt-footer-buttons">
            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
              onclick="$('#modalFilterRSJ').modal('hide')">Batal</button>
            <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterRSJ(); $('#modalFilterRSJ').modal('hide');">Terapkan</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="po-toolbar">

        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" onchange="onChangePeriodeRSJ()" class="po-filter-inp" id="input_tanggalawal_rsj" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodeRSJ()" class="po-filter-inp" id="input_tanggalakhir_rsj" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>

        <input type="search" id="tabel_filter_visual" class="po-search-inp" placeholder="Cari data">

        <div class="po-len-wrap">
          <label for="tabel_length_visual">Tampilkan</label>
          <select id="tabel_length_visual" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>

        <button class="po-btn-filter" type="button" onclick="$('#modalFilterRSJ').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>

        <div class="po-toolbar-act">
          <button type="button" class="btn btn-primary" onclick="buttonAdd()">Tambah</button>
        </div>

      </div>
    </div>
  </div>

  <div class="card">
  <div class="card-body" style="padding:0;">
      <div id="rtBarTabel"></div>
      <div class="po-table-wrap">
        <table id="tabel" class="data-table">
          {{-- Header content is fully JS-owned: replaceTheadWithHeader() (called from
               renderTabelRows(), which runs on page load via reinitTabel()) replaces
               this <thead>'s contents based on gcart_header before the user ever sees
               it -- the tag itself is just a placeholder for that selector. --}}
          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel_data" class="text-left"></tbody>
        </table>
      </div>
      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
        untuk menyembunyikan kolom atau mengatur jumlah desimal.
      </div>
  </div>
  </div>

</div>

</div>

<div id="page2" class="container-fluid" style="display: none">

  <div class="container-fluid">

    <!-- <div id="qrcode"></div> -->
    <div class="row d-flex justify-content-between align-items-center">
      <div class="col-auto text-left">
        {{-- <h1>Form Retur SJ</h1> --}}
      </div>
      <div class="col-auto text-right">
        <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonCloseForm()">
          Close
        </button>
      </div>
    </div>
  <!-- <button onclick="loadAll()">tes</button> -->
  </div>
  <div id="modalBodyAddMain" class="">
  <div class="modal-body">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row g-3">

        <div class="col-3">

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Customer</label>
            </div>
            <div class="col-8">
              <div class="input-group">
                <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
                <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListCustSupp" onclick="buttonAddListCustSupp()"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

        </div>

        <div class="col-3">

          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">No SJ</label>
            </div>
            <div class="col-8">
              <div class="input-group">
                  <input type="text" class="form-control" id="input_add_nosj" placeholder="" disabled>
                  <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListNoSJ" onclick="buttonAddListNoSJ()"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>

        </div>

          <div class="col-3">
            <div class="row align-items-center mb-2">
              <div class="col-4">
                <label class="form-label mb-0">No Bukti</label>
              </div>
              <div class="col-8">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No SO" disabled>
              </div>
            </div>
          </div>

          <div class="col-3">
            <div class="row align-items-center mb-2">
              <div class="col-4">
                <label class="form-label mb-0">Tanggal</label>
              </div>
              <div class="col-8">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>
          </div>


        </div>






      </div>

      <hr/>

      <div class="container-fluid">


      <div class="row g-3">
        <div class="col-3">
            <div class="row mb-2">

            <div class="col-12">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_customer"  disabled></textarea>
            </div>
          </div>

        </div>
        <div class="col-3">
          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">No SO</label>
            </div>
            <div class="col-8">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->
                <input type="text" class="form-control" id="input_add_noso" placeholder="" disabled>
            </div>
          </div>
            <div class="row align-items-start mb-2">

            <div class="col-4">
                <label class="form-label mb-0">Catatan</label>
            </div>
            <div class="col-8">
                <textarea  style="width: 100%; resize: none; " rows=4  class="form-control" id="input_add_catatan"  ></textarea>
            </div>

          </div>
        </div>

        <div class="col-3">
          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Tgl SJ</label>
            </div>
            <div class="col-8">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->
              <input type="date" class="form-control" id="input_add_tanggalsj" value="{!! date('Y-m-d') !!}" disabled>
            </div>

          </div>
            <div class="row align-items-center mb-2">

              <div class="col-4">
                <label class="form-label mb-0">Gudang</label>
              </div>
              <div class="col-8">
                    <input type="text" class="form-control" id="input_add_gudang" placeholder="" disabled>
              </div>

          </div>
        </div>

        <div class="col-3">
          <div class="row align-items-center mb-2">
            <div class="col-4">
              <label class="form-label mb-0">Tgl SC</label>
            </div>

            <div class="col-8">
                <!-- <input type="hidden" class="form-control" id="input_add_kodegdg" placeholder="" > -->

              <input type="date" class="form-control" id="input_add_tanggalsc" value="{!! date('Y-m-d') !!}" disabled>
            </div>

          </div>
            <div class="row align-items-center mb-2">

            <div class="col-4">
              <label class="form-label mb-0">No Pol Kend</label>
            </div>
            <div class="col-8">
                  <input type="text" class="form-control" id="input_add_nopol" placeholder="" >
            </div>

          </div>
        </div>


      </div>

      <div class="row">
        <div class="col-3">

        </div>

        <div class="col-8">
          <div class="row">
            <div class="col-6">

            </div>

            <div class="col-6">
              <div class="row">


              </div>
            </div>

            <div class="col-12">
              <div class="row">


              </div>

            </div>



          </div>

        </div>

      </div>

      </div>



    </div>
    <div class="container-fluid">
      <hr/>

    </div>

  <div class="container-fluid mt-4" style="overflow-x: auto;">

        <table id="addTable" class="data-table">
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
              <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="text-right" >
            <tr >

                <td colspan=5 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>



  <div class="col-12">

  <div class="row">
    <div class="col-12  text-right">

      <button type="button" class="btn btn-chip-biru btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddAdd()" ><b>Tambah Item</b></button>
      </div>
    </div>

  </div>
</div>
<div id="formAddAdd" class="container-fluid showhide mb-2">
  <!-- <div class="line"></div> -->
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Add Item</h4>
    </div>
  </div>
  <div class="row g-3">

    <div class="col-3">

  <div class="row align-items-center mb-2">
    <div class="col-4">
      <label class="form-label mb-0">Kode Barang</label>
    </div>
    <div class="col-8">
      <div class="input-group">

        <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
        <button type="button" id="buttonAddListBarang" onclick="buttonAddListBarang()" class="btn btn-chip-biru btn-sm btn-icon-search"><i class="bi bi-search"></i></button>
      </div>
    </div>

  </div>

</div>

<div class="col-6">
  <div class="row align-items-center mb-2">
    <div class="col-2">
      <label class="form-label mb-0">Nama Barang</label>
    </div>
    <div class="col-6">
      <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
    </div>
  </div>
</div>

<div class="col-3">

</div>

<div class="col-3 ">
  <div class="row align-items-center mb-2">
    <div class="col-4">
      <label class="form-label mb-0">Retur Supp</label>
    </div>
    <div class="col-8">
      <select  id="AddAddReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddAddNamaGdg" type="text" class="form-control" disabled>
      <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>

  </div>
</div>
<div class="col-6 ">
  <div class="row align-items-center mb-2">
    <div class="col-2">
      <label class="form-label mb-0">Qty</label>
    </div>
    <div class="col-4">
      <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
    </div>
    <div class="col-2">
      <input id="AddAddInputSatuan" type="text"  class="form-control  " disabled>
      <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
      <input id="AddAddInputSatuan1" type="hidden"  class="form-control  " disabled>
    </div>


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-danger btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonBatalAdd()" class="btn btn-danger">Batal</button>

      <button type="button" id="buttonSubmitAddAdd" class="btn btn-chip-biru btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="submitAddAdd()" class="btn btn-secondary">Simpan</button>
</div>

  </div>
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>

<div id="formAddEdit" class="container-fluid showhide mb-2">
  <!-- <div class="line"></div> -->
  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
    </div>
  </div>
  <div class="row g-3">

    <div class="col-3">

  <div class="row align-items-center mb-2">
    <div class="col-4">
      <label class="form-label mb-0">Kode Barang</label>
    </div>
    <div class="col-8">
      <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
    </div>

  </div>

</div>

<div class="col-6">
  <div class="row align-items-center mb-2">
    <div class="col-2">
      <label class="form-label mb-0">Nama Barang</label>
    </div>
    <div class="col-6">
      <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
    </div>
  </div>
</div>

<div class="col-3">

</div>

<div class="col-3 ">
  <div class="row align-items-center mb-2">
    <div class="col-4">
      <label class="form-label mb-0">Retur Supp</label>
    </div>
    <div class="col-8">
      <select  id="AddEditReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddEditNamaGdg" type="text" class="form-control" disabled>
      <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>

  </div>
</div>
<div class="col-6">
  <div class="row align-items-center mb-2">
    <div class="col-2">
      <label class="form-label mb-0">Qty</label>
    </div>
    <div class="col-4">
      <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
    </div>
    <div class="col-2">
      <input id="AddEditInputSatuan" type="text"  class="form-control  " disabled>
      <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
      <input id="AddEditInputSatuan1" type="hidden"  class="form-control  " disabled>
    </div>


  </div>
</div>
</div>


  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <!-- <button type="button" class="btn btn-secondary" onclick="buttonBatalAdd()" >Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->

      <button type="button" class="btn btn-danger btn-lg" style="
      height: 30px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonBatalAdd()" class="btn btn-danger">Batal</button>

      <button type="button" id="buttonSubmitAddEdit" class="btn btn-chip-biru btn-lg" style="
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
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>

  </div>

  </div>

  <div id="page3" class="container-fluid" style="display: none">

    <div class="container-fluid">

      <!-- <div id="qrcode"></div> -->
      <div class="row">
        <div class="col-6 text-left">
          <h1 class="" id="modalTitleDetail">
            {{-- Detail --}}
          </h1>

          <h1 class="" id="modalTitleOto">
            {{-- Otorisasi --}}
          </h1>
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
    <!-- <button onclick="loadAll()">tes</button> -->
    </div>
    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div> -->
            <div class="col-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_nosj" placeholder="No SJ" disabled>
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoSJ" onclick="buttonAddListNoSJ()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>
          </div>
            </div>

            <div class="col-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No SO" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-3">
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <hr/>

        <div class="container-fluid">


        <div class="row">
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Customer</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
                  </div>
              </div>




              </div>
              <div class="row" style="margin-top: -10px">
              <!-- <div class="col-2">
                <div class="form-group">
                div

                </div>

              </div> -->
              <div class="col-12">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_customer"  disabled></textarea>
                </div>
              </div>
            </div>

          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->
                  <input type="text" class="form-control" id="input_detail_noso" placeholder="" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4">
                <div class="form-group">

                  <label>Catatan</label>
                </div>

              </div>
              <div class="col-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none; " rows=4  class="form-control" id="input_detail_catatan"  disabled></textarea>
                </div>
              </div>

            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SJ</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->
                <input type="date" class="form-control" id="input_detail_tanggalsj" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

              </div>
              <div class="row" style="margin-top: -10px">

              <!-- <div class="row"> -->
                <div class="col-4">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detail_gudang" placeholder="" disabled>
                    </div>
                </div>

              <!-- </div> -->

            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SC</label>
                </div>
              </div>

              <div class="col-8">
                <div class="form-group">
                  <!-- <input type="hidden" class="form-control" id="input_detail_kodegdg" placeholder="" > -->

                <input type="date" class="form-control" id="input_detail_tanggalsc" value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-4">
                <div class="form-group">
                  <label>No Pol Kend</label>
                </div>
              </div>
              <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nopol" placeholder="" disabled>
                  </div>
              </div>

            </div>
          </div>


        </div>

        <div class="row">
          <div class="col-3">

          </div>

          <div class="col-8">
            <div class="row">
              <div class="col-6">

              </div>

              <div class="col-6">
                <div class="row">


                </div>
              </div>

              <div class="col-12">
                <div class="row">


                </div>

              </div>



            </div>

          </div>

        </div>

        </div>



      </div>


    <div class="container-fluid" style="overflow-x: auto;">

          <table id="detailTable" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>


            <tbody id="detailTableData" class="text-right" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    <div id="" class="container-fluid ">
      <div class="row">
        <div class="col-12 text-right">
          <button type="button" id="buttonSubmitOto" class="btn btn-chip-biru btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="submitOto()" class="btn btn-secondary">Otorisasi</button>

        </div>

      </div>
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    </div>

    </div>

    </div>


</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalBodyAddListBarang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_barang" class="text-left" >

                <tr class="pick-row">

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>


              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>

    </div>
    <div id="modalBodyAddListNoSJ" class="showhidemodalbodyadd">
      <div class="modal-body" >

      <div class="container-fluid" >
        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
        <div class="row">
          <div class="col-12" style="overflow:auto;">
          <!-- <div class="container-fluid"> -->


          <table id="tabel_add_list_nosj" class="data-table">
            <thead class="text-center">
              <tr>

                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Tgl</th>

                <th style="padding: 4px 12px;" scope="col">Tipe</th>
                <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                <th style="padding: 4px 12px;" scope="col">Cust</th>

              </tr>
            </thead>


            <tbody id="tabel_data_add_list_nosj" class="text-left" >

              <tr class="pick-row">
                <td>-</td>
                <td>-</td>

                <td>-</td>
                <td>-</td>
                <td>-</td>



            </tr>
            </tbody>


          </table>
        <!-- </div> -->
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
        </div>
          </div>
          </div>
    </div>

  </div>


  <div id="modalBodyAddListCust" class="showhidemodalbodyadd">
    <div class="modal-body" >

    <div class="container-fluid" >
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <div class="col-12" style="overflow:auto;">
        <!-- <div class="container-fluid"> -->


        <table id="tabel_add_list_custsupp" class="data-table">
          <thead class="text-center">
            <tr>

              <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
              <th style="padding: 4px 12px;" scope="col">Cust</th>

            </tr>
          </thead>


          <tbody id="tabel_data_add_list_custsupp" class="text-left" >

            <tr class="pick-row">
              <td>-</td>
              <td>-</td>



          </tr>
          </tbody>


        </table>
      <!-- </div> -->
        <!-- <button onclick="buttonSubKategori()">tes</button> -->
      </div>
        </div>
        </div>
  </div>

</div>


      <div id="modalBodyFooterList" class="modal-footer showhidemodalfooteradd">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <!-- <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()">Batal</button> -->
      </div>

      <div id="modalBodyFooterMain" class="modal-footer showhidemodalfooteradd">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->









@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih bersama (#form) disamakan persis dengan modal pemilih menu Kas.
     Class picker-kas dipasang dari sini (bukan di markup modalnya) - lihat pola yang sama
     di purchaseOrder.blade.php / marketing/so.blade.php / public/css/picker-kas.css. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

let listSJ = []
let listBarang = []
let listCust = []

let dataTableAddHeader = {}
let dataTableAdd = []
let dataForm = {}

let dataTableDetailHeader = {}
let dataTableDetail = []
let dataFormDetail = {}

let dataBarangAdd = {}
let dataBarangEdit = {}

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Rerouted from the shared HeaderEngine.js (loadHeader/simpanHeader, its own
 * dedicated retursuratjalanloadheader/simpanheader endpoints) to the same
 * self-contained saveheadertable/getheadertable (HeaderTableController)
 * inline pattern used by so/invoicejasa/fakturpajak/cetaktandaterima/
 * perintahreturjual/returpenjualangudang/kreditnote/notareturpenjualan/
 * perintahreturjualminus. tabelActionsCell/tabel2ActionsCell/tabelValueCell/
 * replaceTheadWithHeader/renderTabelRows/renderTabel2Rows/reinitTabel/
 * reinitTabel2/rsjAturTinggiTabel below are otherwise untouched -- only the
 * HeaderEngine.* calls they made are rerouted to the inline equivalents here.
 */
// Disederhanakan jadi satu tabel setelah tab Belum Otorisasi/Sudah Otorisasi
// digabung jadi satu daftar dengan filter Status Otorisasi (lihat modalFilterRSJ
// di section('content')), sama seperti kreditnote.blade.php/invoicejasa.blade.php.
let rsjCart = []
const RSJ_HREF = 'retursuratjalan'
const RSJ_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const RSJ_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

var lastTabelRows = [];

function rsjPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function rsjDefaultCart () {
  return [
    ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
    ['NOSPB',        'No SPB',    1, 'varchar', 0, 0],
    ['TglSPB',       'Tgl SPB',   1, 'date',    0, 0],
    ['NamaCustSupp', 'Customer',  1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto1', 1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto1',  1, 'date',    0, 0],
  ]
}

function rsjBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, RSJ_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

window.g_href = RSJ_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = rsjCart || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(RSJ_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: RSJ_HREF, urut: 1
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: RSJ_HREF, urut: 1, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        rsjCart = rsjBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        rsjCart = rsjDefaultCart()
        window.gcart_header = rsjCart
        window.doSimpanHeader()
      }
      window.gcart_header = rsjCart
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      rsjCart = rsjDefaultCart()
      window.gcart_header = rsjCart
    }
  })
}

// Digabung dari tabelActionsCell (Belum Otorisasi: Koreksi/Otorisasi) +
// tabel2ActionsCell (Sudah Otorisasi: tanpa Koreksi) sejak keduanya digabung
// jadi satu tabel dengan filter Semua/Belum/Sudah Otorisasi.
function tabelActionsCell(row) {
  var nobukti = rsjPickCI(row, 'NOBUKTI');
  var isOto1 = rsjPickCI(row, 'IsOtorisasi1');
  var html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button> ';
  if (Number(isOto1) === 1) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOto(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'' + isOto1 + '\')"><i class="bi bi-pen"></i></button> ';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOto(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

// col: [field, label, visible, type, hasTotal, decimals]. Date formatting matches this
// page's own formatDate(date, '/') convention (yyyy/mm/dd), not so.blade.php's dd/mm/yyyy
// -- preserves the exact display this page already had, only the mechanism changed.
function retursuratjalanFormatTanggal(raw) {
  if (!raw) { return ''; }
  var d = new Date(raw);
  if (isNaN(d)) { return ''; }
  var dd = ('0' + d.getDate()).slice(-2);
  var mm = ('0' + (d.getMonth() + 1)).slice(-2);
  return d.getFullYear() + '/' + mm + '/' + dd;
}

function tabelValueCell(row, col) {
  var raw = rsjPickCI(row, col[0]);
  var type = col[3];

  if (type === 'date') {
    return '<td>' + retursuratjalanFormatTanggal(raw) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// ReportTable.init()'s bindHead(thead) attaches drag/gear listeners with no matching
// removeEventListener -- calling init() again (which reinitTabel()/reinitTabel2() do, on
// purpose, to re-bind after DataTables rebuilds) is only safe against a genuinely NEW
// <thead> node each time, so the whole element is replaced here rather than just its
// innerHTML.
function replaceTheadWithHeader(tableSel, cols) {
  var oldThead = document.querySelector(tableSel + ' thead');
  if (!oldThead || !window.ReportTable) { return; }
  // ReportTable.headHtml() only knows about gcart_header entries, so it never accounts
  // for the fixed Actions column every body row starts with. Splice a plain,
  // non-draggable Actions <th> in as the first cell so thead/tbody column counts
  // actually match.
  var headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  var newThead = document.createElement('thead');
  newThead.setAttribute('style', 'white-space:nowrap;');
  newThead.innerHTML = headRowHtml;
  oldThead.parentNode.replaceChild(newThead, oldThead);
}

// Kotak scroll tabel dibuat setinggi sisa ruang di #content supaya halaman TIDAK perlu
// scrollbar sendiri - port 1:1 dari soAturTinggiTabel()/poAturTinggiTabel() milik
// so.blade.php/purchaseOrder.blade.php.
function rsjAturTinggiTabel() {
  var area = document.getElementById('content');
  var wrap = document.querySelector('.po-table-wrap');
  if (!area || !wrap) { return; }

  wrap.style.maxHeight = 'none';

  var padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0;
  var batasBawah = area.getBoundingClientRect().bottom - padBawah;
  var kotak = wrap.getBoundingClientRect();

  var sisa = batasBawah - kotak.top - 4;
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px';
}

// dom string disamakan 1:1 dengan so.blade.php: 'po-table-wrap't membungkus HANYA isi
// tabelnya dalam kotak scroll, sedangkan info+pagination ('i'/'p') ada DI LUAR kotak itu
// supaya tidak ikut tergulung -- menggantikan moveDataTablePagination()/
// .tb-pagination-outside yang dipakai sebelumnya untuk menyelesaikan masalah yang sama.
const RSJ_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function renderTabelRows(rows) {
  var cols = gcart_header.filter(function (c) { return c[2] === 1; }); // same refs -- never .map()
  var html = '';
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += tabelValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  replaceTheadWithHeader('#tabel', cols);
}

function reinitTabel() {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: RSJ_DOM_STRING, lengthChange: false, paging: true, order: [[1, 'asc']], ordering: false, drawCallback: function () { setTimeout(rsjAturTinggiTabel, 0); } });
    ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel });
    rsjAturTinggiTabel();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function rsjResetFilterFields () {
  $('#input_filterrsj').val('0')
}

function rsjUpdateFilterBadge () {
  let n = Number($('#input_filterrsj').val()) || 0
  $('#rsjFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterRSJ () {
  let tglawal = $('#input_tanggalawal_rsj').val()
  let tglakhir = $('#input_tanggalakhir_rsj').val()
  let filterrsj = $('#input_filterrsj').val()
  $.ajax({
    url: "{!! url('retursuratjalanloadall') !!}",
    type: "get", async: false,
    data: { tglawal, tglakhir, filterrsj },
    success: function (res) {
      lastTabelRows = res.tempOutstanding
      reinitTabel()
      rsjUpdateFilterBadge()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeRSJ () {
  let tglawal = $('#input_tanggalawal_rsj').val()
  let tglakhir = $('#input_tanggalakhir_rsj').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterRSJ()
}

function buttonHeaderTable(key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    window.doSetHeader(1, true);
    reinitTabel();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

$(document).ready(function(){

      document.getElementById('breadcrumb').innerHTML = "Retur Surat Jalan"

      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      // Shared toolbar controls (search box + Tampilkan dropdown for the single
      // merged table), matching so.blade.php's shared-toolbar pattern.
      var tabelFilterVisualTimeout;
      $('#tabel_filter_visual').on('keyup', function () {
        var value = this.value;
        clearTimeout(tabelFilterVisualTimeout);
        tabelFilterVisualTimeout = setTimeout(function () {
          if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().search(value).draw(); }
        }, 400);
      });
      $('#tabel_length_visual').on('change', function () {
        var len = Number(this.value);
        if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().page.len(len).draw(); }
      });

        $("#tabel_add_list_nosj").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[0, 'asc']],
        });

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[0, 'asc']],
        });

});


function buttonAddListBatal () {


  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

}


function buttonBatalOto (nobukti) {
  console.log('buttonBatalOtorisasi' , nobukti)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.prompt("Batal Otorisasi","Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    let xpket = value;

    if (xpket==''){
      alertify.warning('Keterangan harus diisi.');
      return
    }
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('retursuratjalanspbataloto') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        pket: value

      },
      success: function(res) {
        console.log('!', res)
        loadAll()

        // lockFormAdd()

        alertify.success('Berhasil Batal Otorisasi RSPB')

      },
      error: function (err) {
        console.log(err)
        loadAll()
        alertify.error('Batal otorisasi kemungkinan sudah tersimpan, namun terjadi kesalahan pada proses lanjutan. Data telah dimuat ulang -- silahkan periksa status terbaru pada tabel.')
      }

    })
  }
  ,function(){
    console.log('no')
    alertify.error("Action cancelled");
  });
}

function submitOto () {

  console.log(dataTableDetailHeader.NOBUKTI)


  let _token = $("#_token").val();



  $.ajax({
    url: "{!! url('retursuratjalanspoto') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti : dataTableDetailHeader.NOBUKTI

    },
    success: function(res) {
      console.log('!', res)
      loadAll()

      // lockFormAdd()
      // $("#formDetail").modal('toggle')
      $('#page3').hide();
      $('#page1').show();

      alertify.success('Berhasil Otorisasi RSPB')


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function loadAll () {
  console.log('loadall')
  let _token = $("#_token").val();
  let tglawal = $('#input_tanggalawal_rsj').val()
  let tglakhir = $('#input_tanggalakhir_rsj').val()
  let filterrsj = $('#input_filterrsj').val()

  $.ajax({
    url: "{!! url('retursuratjalanloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal, tglakhir, filterrsj
    },
    success: function(res) {
      console.log(res)

      lastTabelRows = res.tempOutstanding
      reinitTabel()
    }})


}

function buttonAddPickBarang (index) {
  dataBarangAdd = listBarang[index]
  console.log(dataBarangAdd)

  document.getElementById("AddAddKodeBrg").value = dataBarangAdd.kodebrg
  document.getElementById("AddAddNamaBrg").value = dataBarangAdd.Namabrg
  document.getElementById("AddAddInputSatuan").value = dataBarangAdd.Satuan
  document.getElementById("AddAddInputQty").value = parseFloat(dataBarangAdd.Qty).toFixed(2)


  buttonAddListBatal()
  $("#form").modal('toggle')
}

function buttonAddPickCust (index) {
  dataCust = listCust[index]
  console.log(dataCust)

  // document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_customer").value = dataCust.NAMACUSTSUPP + '\n' + dataCust.Alamat
  document.getElementById("input_add_kodecustomer").value = dataCust.KodeCustSupp
  document.getElementById("input_add_nosj").value = ''

  // document.getElementById("input_add_nopol").value = ''
  //
  // document.getElementById("input_add_tanggal").value = formatDate(new Date())
    // document.getElementById("input_add_catatan").value = ''
  $('#buttonAddListNoSJ').show();
  // $('#buttonAddListCustSupp').hide();
  buttonAddListBatal()

  $('.showhide').hide();
  $("#form").modal('toggle')
}

function buttonAddPickNoSJ (index) {
  dataForm = listSJ[index]
  console.log(dataForm)

  // document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nosj").value = dataForm.NoSPB
  document.getElementById("input_add_noso").value = dataForm.NoSC
  // document.getElementById("input_add_customer").value = dataForm.NAMACUSTSUPP + '\n' + dataForm.Alamat
  // document.getElementById("input_add_kodecustomer").value = dataForm.KodeCustSupp
  document.getElementById("input_add_gudang").value = dataForm.Nama
  // document.getElementById("input_add_nopol").value = ''
  //
  // document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tanggalsj").value = formatDate(dataForm.TglSPB)
  document.getElementById("input_add_tanggalsc").value = formatDate(dataForm.TglSC)
  // document.getElementById("input_add_catatan").value = ''
  setNewNoBukti(dataForm.PPNCUST)
  // $('#buttonAddListNoSJ').hide();
  buttonAddListBatal()

  $('.showhide').hide();
  $("#form").modal('toggle')
}

function buttonOto (nobukti) {


  console.log('buttonOto' , nobukti)

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  let _token = $("#_token").val();
  $('#modalTitleDetail').hide();
  $('#modalTitleOto').show();

  $('#buttonSubmitOto').show();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')
          return
      }
      dataTableDetail = res.detail
      dataTableDetailHeader = res.header[0]
      dataFormDetail = res.dataForm[0]
      console.log(formatDate(dataTableDetailHeader.TANGGAL))
      document.getElementById("input_detail_tanggal").value = formatDate(dataTableDetailHeader.TANGGAL)
      document.getElementById("input_detail_nopol").value = dataTableDetailHeader.NoPolKend
      document.getElementById("input_detail_catatan").value = dataTableDetailHeader.Catatan

      document.getElementById("input_detail_nobukti").value = nobukti
      document.getElementById("input_detail_nourut").value = dataTableDetailHeader.NOURUT

      document.getElementById("input_detail_nosj").value = dataFormDetail.NoSPB
      document.getElementById("input_detail_noso").value = dataFormDetail.NoSC
      document.getElementById("input_detail_customer").value = dataFormDetail.NAMACUSTSUPP + '\n' + dataFormDetail.Alamat
      document.getElementById("input_detail_kodecustomer").value = dataFormDetail.KodeCustSupp
      document.getElementById("input_detail_gudang").value = dataFormDetail.Nama
      // document.getElementById("input_detail_nopol").value = ''
      //
      // document.getElementById("input_detail_tanggal").value = formatDate(new Date())
      document.getElementById("input_detail_tanggalsj").value = formatDate(dataFormDetail.TglSPB)
      document.getElementById("input_detail_tanggalsc").value = formatDate(dataFormDetail.TglSC)


      let rowTable = ''

      dataTableDetail.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>

          </tr>
        `
      });


      document.getElementById("detailTableData").innerHTML = rowTable
       // $("#formDetail").modal('toggle')
       $('#page1').hide();
       $('#page3').show();

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })




  $("#formDetail").modal('toggle')
}

function buttonDetail (nobukti) {
  console.log('buttonDetail' , nobukti)
  let _token = $("#_token").val();
  $('#modalTitleDetail').show();

  $('#buttonSubmitOto').hide();
  $('#modalTitleOto').hide();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')
          return
      }
      dataTableDetail = res.detail
      dataTableDetailHeader = res.header[0]
      dataFormDetail = res.dataForm[0]
      console.log(formatDate(dataTableDetailHeader.TANGGAL))
      document.getElementById("input_detail_tanggal").value = formatDate(dataTableDetailHeader.TANGGAL)
      document.getElementById("input_detail_nopol").value = dataTableDetailHeader.NoPolKend
      document.getElementById("input_detail_catatan").value = dataTableDetailHeader.Catatan

      document.getElementById("input_detail_nobukti").value = nobukti
      document.getElementById("input_detail_nourut").value = dataTableDetailHeader.NOURUT

      document.getElementById("input_detail_nosj").value = dataFormDetail.NoSPB
      document.getElementById("input_detail_noso").value = dataFormDetail.NoSC
      document.getElementById("input_detail_customer").value = dataFormDetail.NAMACUSTSUPP + '\n' + dataFormDetail.Alamat
      document.getElementById("input_detail_kodecustomer").value = dataFormDetail.KodeCustSupp
      document.getElementById("input_detail_gudang").value = dataFormDetail.Nama
      // document.getElementById("input_detail_nopol").value = ''
      //
      // document.getElementById("input_detail_tanggal").value = formatDate(new Date())
      document.getElementById("input_detail_tanggalsj").value = formatDate(dataFormDetail.TglSPB)
      document.getElementById("input_detail_tanggalsc").value = formatDate(dataFormDetail.TglSC)


      let rowTable = ''

      dataTableDetail.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>


          </tr>
        `
      });


      document.getElementById("detailTableData").innerHTML = rowTable
       // $("#formDetail").modal('toggle')
       $('#page1').hide();
       $('#page3').show();


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })




  // $("#formDetail").modal('toggle')
}

function refreshDataTableAdd (nobukti) {
  console.log('refreshDataTableAdd')
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('retursuratjalanspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti,


    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res.detail.length == 0) {
          // addTableData

          alertify.warning('Data habis/ tidak ditemukan')
          // $("#form").modal('toggle')

          $('#page2').hide();
          $('#page1').show();

          return
      }
      dataTableAdd = res.detail
      dataTableAddHeader = res.header[0]
      dataForm = res.dataForm[0]
      console.log(formatDate(dataTableAddHeader.TANGGAL))
      document.getElementById("input_add_tanggal").value = formatDate(dataTableAddHeader.TANGGAL)
      document.getElementById("input_add_nopol").value = dataTableAddHeader.NoPolKend
      document.getElementById("input_add_catatan").value = dataTableAddHeader.Catatan
      document.getElementById("input_add_nobukti").value = nobukti
      document.getElementById("input_add_nourut").value = dataTableAddHeader.NOURUT
      document.getElementById("input_add_nosj").value = dataForm.NoSPB
      document.getElementById("input_add_noso").value = dataForm.NoSC
      document.getElementById("input_add_customer").value = dataForm.NAMACUSTSUPP + '\n' + dataForm.Alamat
      document.getElementById("input_add_kodecustomer").value = dataForm.KodeCustSupp
      document.getElementById("input_add_gudang").value = dataForm.Nama
      // document.getElementById("input_add_nopol").value = ''
      // document.getElementById("input_add_tanggal").value = formatDate(new Date())
      document.getElementById("input_add_tanggalsj").value = formatDate(dataForm.TglSPB)
      document.getElementById("input_add_tanggalsc").value = formatDate(dataForm.TglSC)
      document.getElementById("input_add_catatan").disabled = true;
      document.getElementById("input_add_nopol").disabled = true;
      document.getElementById("input_add_tanggal").disabled = true;

      let rowTable = ''

      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr class="text-left">
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class='text-right'>${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT_1}</td>
            <td class='text-center'>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEdit(${i})"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete('${item.NOBUKTI}' ,  ${item.URUT})"><i class="bi bi-trash"></i></button>


            </td>
          </tr>
        `
      });

        // <td class='text-right'>${parseFloat(item.QNT2).toFixed(2)}</td>
        // <td>${item.SAT_2}</td>
        // placeholder kalo qty & sat 2 dibutuhkan lagi

      document.getElementById("addTableData").innerHTML = rowTable

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function submitAddEdit () {
  let tempKodeBarang = $("#AddEditKodeBrg").val();

  let tempNamaBarang = $("#AddEditNamaBrg").val();
  let choice = 'U'
  let _token = $("#_token").val();

  console.log(dataForm)
  console.log(dataTableAddHeader)
  console.log(dataBarangEdit)

  let tempQnt = $("#AddEditInputQty").val();
  console.log(tempQnt)

  if (Number(tempQnt) <= 0) {
    alertify.warning("Qty <= 0")
    return
  }




  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let nopol = $("#input_add_nopol").val();
  let catatan = $("#input_add_catatan").val();
  let retursupp = $("#AddEditReturSupp").val();

  let flagtipe = 0
  if (Number(dataBarangEdit.FlagTipe)) {
    flagtipe = 1
  }

  let tempQnt1 = tempQnt * dataBarangEdit.ISI

  console.log({
    _token : _token,
    choice,
    nobukti,
    nourut,
    tanggal,
    nosj : dataBarangEdit.NoSC,
    kodecustsupp: dataForm.KodeCustSupp,
    nopol,
    container: '',
    nocontainer : '',
    noseal : '',
    catatan,
    urut : dataBarangEdit.URUT,
    urutsj : dataBarangEdit.UrutSC,
    kodebrg : tempKodeBarang,
    qnt: tempQnt1,
    qnt2: tempQnt,
    sat1: dataBarangEdit.SAT_1,
    sat2:  dataBarangEdit.SAT_2,
    nosat : dataBarangEdit.NOSAT,
    isi :  dataBarangEdit.ISI,
    netw : 0,
    grossw: 0,
    isempty: dataTableAdd.length,
    namabrg: tempNamaBarang,
    flagmenu: 0,
    flagtipe,
    retursupp

  })
  // return

  $.ajax({
    url: "{!! url('retursuratjalanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      nosj : dataBarangEdit.NoSC,
      kodecustsupp: dataForm.KodeCustSupp,
      nopol,
      container: '',
      nocontainer : '',
      noseal : '',
      catatan,
      urut : dataBarangEdit.URUT,
      urutsj : dataBarangEdit.UrutSC,
      kodebrg : tempKodeBarang,
      qnt: tempQnt1,
      qnt2: tempQnt,
      sat1: dataBarangEdit.SAT_1,
      sat2:  dataBarangEdit.SAT_2,
      nosat : dataBarangEdit.NOSAT,
      isi :  dataBarangEdit.ISI,
      netw : 0,
      grossw: 0,
      isempty: dataTableAdd.length,
      namabrg: tempNamaBarang,
      flagmenu: 0,
      flagtipe,
      retursupp

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {
        // loadAll()
        // refreshDataTableKoreksi(nobukti)
        refreshDataTableAdd(nobukti)
        $(".showhide").hide()
        // $("#form").modal('toggle')
        alertify.success('Item telah diedit');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })



}


function submitAddAdd () {
  let tempKodeBarang = $("#AddAddKodeBrg").val();

  let tempNamaBarang = $("#AddAddNamaBrg").val();
  let choice = 'I'
  let _token = $("#_token").val();

  if (!tempKodeBarang) {
    alertify.warning("Pilih barang terlebih dahulu")

    return
  }



  console.log(dataForm)
  console.log(dataBarangAdd)

  let tempQnt = $("#AddAddInputQty").val();
  console.log(tempQnt)

  if (Number(tempQnt) <= 0) {
    alertify.warning("Qty <= 0")
    return
  }




  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let nopol = $("#input_add_nopol").val();
  let catatan = $("#input_add_catatan").val();
  let retursupp = $("#AddAddReturSupp").val();

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let checkDate = new Date(tanggal)

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let flagtipe = 0
  if (Number(dataForm.FlagTipe)) {
    flagtipe = 1
  }

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal,
    nosj : dataBarangAdd.nobukti,
    kodecustsupp: dataForm.KodeCustSupp,
    nopol,
    container: '',
    nocontainer : '',
    noseal : '',
    catatan,
    urut : 0,
    urutsj : dataBarangAdd.urut,
    kodebrg : tempKodeBarang,
    qnt: dataBarangAdd.qnt,
    qnt2: dataBarangAdd.qnt2,
    sat1: dataBarangAdd.Sat_1,
    sat2:  dataBarangAdd.sat_2,
    nosat : dataBarangAdd.nosat,
    isi :  dataBarangAdd.isi,
    netw : 0,
    grossw: 0,
    isempty: dataTableAdd.length,
    namabrg: tempNamaBarang,
    flagmenu: 0,
    flagtipe,
    retursupp
  })

  let tempQnt1 = tempQnt * dataBarangAdd.isi

  $.ajax({
    url: "{!! url('retursuratjalanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      nosj : dataBarangAdd.nobukti,
      kodecustsupp: dataForm.KodeCustSupp,
      nopol,
      container: '',
      nocontainer : '',
      noseal : '',
      catatan,
      urut : 0,
      urutsj : dataBarangAdd.urut,
      kodebrg : tempKodeBarang,
      qnt: tempQnt1,
      qnt2: tempQnt,
      sat1: dataBarangAdd.Sat_1,
      sat2:  dataBarangAdd.sat_2,
      nosat : dataBarangAdd.nosat,
      isi :  dataBarangAdd.isi,
      netw : 0,
      grossw: 0,
      isempty: dataTableAdd.length,
      namabrg: tempNamaBarang,
      flagmenu: 0,
      flagtipe,
      retursupp

    },
    success: function(res) {
      console.log(res ,'!')
      // loadAll()
      if(res == 1) {
        loadAll()
        // refreshDataTableKoreksi(nobukti)
        document.getElementById("input_add_catatan").disabled = true;
        document.getElementById("input_add_nopol").disabled = true;

        document.getElementById("buttonAddListNoSJ").disabled = true;
        document.getElementById("buttonAddListCustSupp").disabled = true;
        refreshDataTableAdd(nobukti)
        $(".showhide").hide()
        // $("#form").modal('toggle')
        alertify.success('Item telah ditambah');
        // console.log('before ===========')
        // loadAll()
        // console.log("after ===========")
      }

      if (res == 2) {
        setNewNoBukti(dataForm.PPNCUST)
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


function buttonAddDelete (nobukti , urut) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonAddDelete')
  console.log(nobukti, urut)


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        $.ajax({
          url: "{!! url('retursuratjalanspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti,
            nourut : '',
            tanggal: '',
            nosj : '',
            kodecustsupp: '',
            nopol: '' ,
            container: '',
            nocontainer : '',
            noseal : '',
            catatan : '',
            urut : urut,
            urutsj : 0,
            kodebrg : '',
            qnt: 0,
            qnt2: 0,
            sat1: '',
            sat2:  '',
            nosat : 1,
            isi :  1,
            netw : 0,
            grossw: 0,
            isempty: 1,
            namabrg: '',
            flagmenu: 0,
            flagtipe: 0,
            retursupp : 0

          },
          success: function(res) {
            console.log('res delete', res)
            loadAll()
            refreshDataTableAdd(nobukti)
            // lockFormAdd()
            $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            console.log('err delete')
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
    });
}

function buttonAddListBarang () {

  let tempNoSJ = $("#input_add_nosj").val();
  if (!tempNoSJ) {
    alertify.warning("Pilih SJ terlebih dahulu")

    return
  }


  // $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListBarang').show();
  // $('.showhidemodalfooteradd').hide();
  // $('#modalBodyFooterList').show();

  listBarang = []
  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_barang')) { $('#tabel_add_list_barang').DataTable().destroy() }

  $.ajax({
    url: "{!! url('retursuratjalanlistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti : tempNoSJ,
    },
    success: function(res) {
      console.log(res)
      listBarang = res

      if (!res.length) {
        alertify.warning('Tidak ada barang untuk ditambah')
        return
      }
      let rowTable = ``
      listBarang.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickBarang(${i})">

        <td>${item.kodebrg}</td>
        <td>${item.Namabrg}</td>
        <td class="text-right">${parseFloat(item.Qty).toFixed(2)}</td>
        <td>${item.Satuan}</td>

        </tr>`
      });




      document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
      pickerKasInit('tabel_add_list_barang')
      loadAll()
      $('#exampleModalLabel').text('Barang');
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBarang').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}



function buttonAddListCustSupp () {

  listCust = []

  if ($.fn.DataTable.isDataTable('#tabel_add_list_custsupp')) { $('#tabel_add_list_custsupp').DataTable().destroy() }

  $.ajax({
    url: "{!! url('retursuratjalanlistcustsuppbaru') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)

      listCust = res
      let rowTable = ``
      listCust.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCust(${i})">

        <td>${item.KodeCustSupp}</td>
        <td>${item.NAMACUSTSUPP}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_custsupp").innerHTML = rowTable

      pickerKasInit('tabel_add_list_custsupp', { order: [[0, 'asc']] })

      $('#exampleModalLabel').text('Customer');
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListCust').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddListNoSJ () {
  let xkodecust = $("#input_add_kodecustomer").val()

  if (!xkodecust) {

    alertify.warning("Pilih Cust Terlebih dahulu")
    return
  }
  let _token = $("#_token").val()

  listSJ = []

  if ($.fn.DataTable.isDataTable('#tabel_add_list_nosj')) { $('#tabel_add_list_nosj').DataTable().destroy() }

  $.ajax({
    url: "{!! url('retursuratjalanlistsj') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp : xkodecust

    },
    success: function(res) {
      console.log(res)

      listSJ = res
      let rowTable = ``
      listSJ.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickNoSJ(${i})">

        <td>${item.NoSPB}</td>
        <td>${formatDate(item.TglSPB , '/')}</td>
        <td class="text-right">${item.PPNCUST}</td>
        <td>${item.KodeCustSupp}</td>
        <td>${item.NAMACUSTSUPP}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_nosj").innerHTML = rowTable

      pickerKasInit('tabel_add_list_nosj', { order: [[0, 'asc']] })

      $('#exampleModalLabel').text('SJ');
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoSJ').show();
      // showhidemodalfooteradd
      $('.showhidemodalfooteradd').hide();
      $('#modalBodyFooterList').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
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




function buttonKoreksi (nobukti , oto) {
  console.log('buttonKoreksi')
  console.log(nobukti)
  console.log(oto)

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (Number(oto)) {
    alertify.warning('RSPB sudah diotorisasi')
    return
  }
  dataTableAdd = []
  dataForm = {}

  resetForm()

  // $('#buttonAddListNoSJ').hide();
  $('.showhide').hide();
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  document.getElementById("input_add_catatan").disabled = true;
  document.getElementById("input_add_nopol").disabled = true;
  document.getElementById("input_add_tanggal").disabled = true;
  refreshDataTableAdd(nobukti)
  document.getElementById("buttonAddListNoSJ").disabled = true;
  document.getElementById("buttonAddListCustSupp").disabled = true;
  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();

}

function buttonAdd () {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  dataTableAdd = []
  resetForm()
  // setNewNoBukti()
  $('#buttonAddListNoSJ').show();
  $('.showhide').hide();
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();
  document.getElementById("input_add_nobukti").value = '';

  document.getElementById("input_add_catatan").disabled = false;
  document.getElementById("input_add_nopol").disabled = false;
  document.getElementById("input_add_tanggal").disabled = false;
  document.getElementById("buttonAddListNoSJ").disabled = false;
  document.getElementById("buttonAddListCustSupp").disabled = false;
  document.getElementById("addTableData").innerHTML = `<tr >

      <td colspan=7 class="text-center">Belum ada data</td>

</tr>`;

  $('.showhidemodalfooteradd').hide();
  $('#modalBodyFooterMain').show();
  // $("#form").modal('toggle')
  $('#page2').show();
  $('#page1').hide();

}

function resetFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddInputSatuan").value = ''
  document.getElementById("AddAddInputQty").value = '0.00'
  document.getElementById("AddAddReturSupp").value = 0
}

function resetForm () {
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nosj").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_customer").value = ''
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("input_add_nopol").value = ''

  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tanggalsj").value = ''
  document.getElementById("input_add_tanggalsc").value = ''
  document.getElementById("input_add_catatan").value = ''

}

function buttonAddEdit (i) {
  console.log('buttonAddEdit')
  console.log(dataTableAdd[i])
  dataBarangEdit = dataTableAdd[i]
  // return
  document.getElementById("AddEditReturSupp").value = Number(dataBarangEdit.FlagKembali)

  document.getElementById("AddEditKodeBrg").value = dataBarangEdit.KODEBRG
  document.getElementById("AddEditNamaBrg").value = dataBarangEdit.NAMABRG
  if (dataBarangEdit.NOSAT == 1) {

    document.getElementById("AddEditInputSatuan").value = dataBarangEdit.SAT_1
    document.getElementById("AddEditInputQty").value = parseFloat(dataBarangEdit.QNT).toFixed(2)
  } else {
    document.getElementById("AddEditInputSatuan").value = dataBarangEdit.SAT_2
    document.getElementById("AddEditInputQty").value = parseFloat(dataBarangEdit.QNT2).toFixed(2)
  }

  $('.showhide').hide();
  $('#formAddEdit').show();

  document.getElementById("AddEditKodeBrg").scrollIntoView();
}

function buttonAddAdd () {
  console.log('buttonAddAdd')
    // tipeformitem = 'Add'
    // document.getElementById("AddAddKodeBrg").value = ''
    // document.getElementById("AddAddNamaBrg").value = ''
    // document.getElementById("AddAddInputSatuan").value = ''
    // document.getElementById("AddAddInputQty").value = '0.00'
    // document.getElementById("AddAddReturSupp").value = 0

    if (!$("#input_add_nosj").val()) {
      alertify.warning("Pilih SJ terlebih dahulu")

      return
    }

    resetFormAddAdd()
    $('.showhide').hide();
    $('#formAddAdd').show();
    document.getElementById("AddAddKodeBrg").scrollIntoView();

}

function buttonBatalAdd () {

    $('.showhide').hide();
}



function setNewNoBukti (ppn) {
  $.ajax({
    url: "{!! url('retursuratjalanspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


</script>



@endsection
