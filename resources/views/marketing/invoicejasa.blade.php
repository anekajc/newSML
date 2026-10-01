@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Invoice Jasa')
@section('title', 'SML - Invoice Jasa')

@endsection

{{-- Rerouted to newmasterTest to match Purchase Order's UI 1:1, same as
     so.blade.php/invoicepenjualan.blade.php/suratjalan.blade.php before it.
     Only layout/tab-bar/toolbar/column-header interactivity changed -- all
     business logic (loadAll, buttonAdd/buttonKoreksi/buttonOtorisasi/
     buttonBatalOtorisasi, the Add-invoice page2 flow) is untouched. --}}
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
.toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
}

.page-title {
  font-size: 19px;
  font-weight: 800;
  color: #1f2430;
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

#content { padding-top: 12px; }

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

#page1 .tab-content .col-md-12 {
  min-width: 0;
  max-width: 100%;
}
/* 
{{-- "Tampilkan" (page-length) dropdown -- copied verbatim from so.blade.php's own
     @section('css'), itself copied from purchaseOrder.blade.php. Not part of
     po-table-header.css, so has to be page-local like the other two pages. --}} */
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
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right center;
}

/* {{-- Kolom Aksi tabel -- small pastel round-button treatment for #tabel/#tabel2's
     Actions cell, copied verbatim (rescoped to this page's own #tabel/#tabel2)
     from so.blade.php's own @section('css'), itself copied from purchaseOrder.blade.php.
     Not part of po-table-header.css. --}} */
#tabel td:first-child,
#tabel2 td:first-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn,
#tabel2 td:first-child .btn,
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

#tabel td:first-child .btn:hover,
#tabel2 td:first-child .btn:hover,
#addTable td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success,
#tabel2 td:first-child .btn-success,
#addTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel td:first-child .btn-warning,
#tabel2 td:first-child .btn-warning,
#addTable td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel td:first-child .btn-chip-biru,
#tabel2 td:first-child .btn-chip-biru,
#addTable td:last-child .btn-chip-biru {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-danger,
#tabel2 td:first-child .btn-danger,
#addTable td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel td:first-child .btn-info,
#tabel2 td:first-child .btn-info,
#addTable td:last-child .btn-info {
  color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
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
</style>

<style>
  #tabel_add_list_lokasi_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_lokasi_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
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
</style>

<style>
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

<style>
  /* Search-icon button appended flush to an input, ported from so.blade.php. */
  .btn-icon-search {
    height: 31px;
    border-radius: 0% 20% 20% 0%;
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

  /* #formBsGrid di public/css/newmaster.css memaku input 38px (dipakai ~20 halaman lain).
     Halaman ini memakai tombol cari 31px (.btn-icon-search di atas), jadi tinggi input
     dikembalikan ke ukuran bawaan tema di sini saja; textarea tetap mengikuti atribut rows. */
  #formBsGrid .form-control {
    height: calc(1.5em + 0.75rem + 2px);
  }

  #formBsGrid textarea.form-control {
    height: auto;
  }

</style>
{{-- end tampilan search modal barang all --}}
@endsection

@section('content')

<div id="page1" class="mainpage container-fluid">

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
       1:1 dari pola kreditnote.blade.php (2 tab, tanpa tab "Outstanding" terpisah,
       sehingga hasil gabungannya langsung satu card tanpa tab bar lagi). --}}
  <div class="modal fade rt-filter" id="modalFilterIJ">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-funnel"></i>
            Filter Data
            <span class="rt-active-badge" id="ijFilterBadge">0 aktif</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterIJ').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-group-label">Status</div>
            <div>
              <label class="rt-field-label" for="input_filterij">Status Otorisasi</label>
              <select class="rt-native" id="input_filterij">
                <option value=0 selected>Semua</option>
                <option value=1>Belum Otorisasi</option>
                <option value=2>Sudah Otorisasi</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="rt-reset-link" onclick="ijResetFilterFields()">Reset semua</button>
          <div class="rt-footer-buttons">
            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
              onclick="$('#modalFilterIJ').modal('hide')">Batal</button>
            <button type="button" class="rt-btn rt-btn-chip-biru" onclick="buttonFilterIJ(); $('#modalFilterIJ').modal('hide');">Terapkan</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding: 0">
      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" onchange="onChangePeriodeIJ()" class="po-filter-inp" id="input_tanggalawal_ij" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodeIJ()" class="po-filter-inp" id="input_tanggalakhir_ij" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="ijSearch1" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap">
          <label for="ijLen1">Tampilkan</label>
          <select id="ijLen1" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" onclick="$('#modalFilterIJ').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button type="button" class="btn btn-primary" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>
      <div id="rtBarTabel"></div>
      <table id="tabel" class="data-table">
        <thead style="white-space:nowrap;"></thead>
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




<div class="container-fluid mainpage" id="page2" style="display: none">

  <div id="" class="">
    <!-- <div class="modal-header">

    <h5 class="modal-title" id="">Add</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div> -->

    <div class="container-fluid">

      <!-- <div id="qrcode"></div> -->
      <div class="row">
        <div class="col-6 text-left">
          {{-- <h1>Form Invoice</h1> --}}
        </div>
        <div class="col-6 text-right">
          <!-- <button type="button" class="btn btn-chip-biru btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button> -->
          <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>

        </div>
      </div>
      <!-- <button onclick="loadAll()">tes</button> -->
    </div>

    <div id="formBsGrid" class="">
      <div class="">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid mt-4">
          <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
          <div class="row">

            <div class="col-md-3">
              <div class="row">

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Customer</label>
                  </div>
                </div>
                <!-- <div class="col-md-3 text-right">
                <div class="form-group">
                </div>
                </div> -->
                <div class="col-md-8">
                  <div class="form-group input-group">
                    <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
                    <button class="btn-chip-biru btn-icon-search" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
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
              </div>
            </div>
            <div class="col-md-3">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tgl</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="row">

              </div>
            </div>

          </div>
        </div>
        <div class="container-fluid">

          <hr/ style="margin-top: -8px">
        </div>
        <div class="container-fluid mt-4">
          <div class="row">

            <div class="col-md-3">
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_namacustomer"  disabled></textarea>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: -10px">

                <div class="col-12">

                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="row">

                <!-- <div class="col-md-6">
                <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_nopo" value="" onblur="onChangeHeader('pono' , 'input_add_nopo' , 'No PO')"  >
                  </div>
                </div>

              </div>
              <div class="row" style="margin-top: -10px">

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sales</label>
                  </div>
                </div>
                <!-- <div class="col-md-3">
                <div class="row">

                <div class="form-group">
                </div>
                </div>

                </div> -->
                <div class="col-md-8">
                  <div class="form-group input-group">
                    <input type="text" class="form-control" id="input_add_sales" value="" disabled >
                    <input type="hidden" class="form-control text-center" id="input_add_kodesales" value="" disabled >
                    <button class=" btn-chip-biru btn-icon-search" id="buttonAddListSales" onclick="buttonAddListSales()"><i class="bi bi-search"></i></button>
                  </div>
                </div>

              </div>
              <div class="row" style="margin-top: -10px">

                <!-- </div>

                </div> -->

                <!-- <div class="col-md-6">
                <div class="row"> -->

                <div class="col-12">
                  <div class="form-group">
                    <label>Lokasi Penerima</label>
                  </div>
                </div>

              </div>
              <div class="row">

                <div class="col-12" style="margin-top: -10px">
                  <div class="form-group input-group">
                    <input type="hidden" class="form-control text-center" id="input_add_kodelokasipenerima" value="" disabled >
                    <input type="text" class="form-control" id="input_add_lokasipenerima" value="" disabled >
                    <button class="btn-chip-biru btn-icon-search" id="buttonAddListLokasiPenerima" onclick="buttonAddListLokasiPenerima()"><i class="bi bi-search"></i></button>
                  </div>
                </div>
                <!-- </div>
                </div> -->

                <!-- </div>

                </div> -->

              </div>

            </div>

            <div class="col-md-3">
              <div class="row">
                <!-- <div class="col-md-6">
                <div class="row"> -->

                <!-- </div>
                </div> -->

                <!-- <div class="col-md-6">
                <div class="row"> INI BUAT AKU -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_valas" value="IDR" disabled >
                  </div>
                </div>
                <!-- </div>
                </div> -->

              </div>
              <div class="row" style="margin-top: -10px">

                <!-- <div class="col-md-6">
                <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_kurs" value="1.00"  onblur="onChangeKurs('kurs' , 'input_add_kurs')">
                  </div>
                </div>

              </div>
              <div class="row" style="margin-top: -10px">

                <div class="col-12">
                  <div class="form-group">
                    <label>Catatan</label>
                  </div>
                </div>

              </div>
              <div class="row" style="margin-top: -10px">

                <div class="col-12">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_catatan" value="" onblur="onChangeHeader('footnote' , 'input_add_catatan', 'catatan')" >
                  </div>
                </div>
                <!-- </div>
                </div> -->

              </div>

            </div>

            <div class="col-md-3">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Pembayaran</label>
                  </div>
                </div>

                <div class="col-md-8">
                  <div class="form-group">
                    <select id="input_add_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePembayaran()">
                      <option value=0 selected>Tunai</option>
                      <option value=1 >Kredit</option>
                    </select>
                  </div>
                </div>
                <!-- </div>
                </div> -->

              </div>
              <div class="row" style="margin-top: -10px">

                <!-- <div class="col-md-6">
                <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Hari</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control  text-right" id="input_add_hari" value="0"  onblur="onChangeHeader('hari' , 'input_add_hari')">
                    <input type="hidden" class="form-control  text-right" id="input_add_harikredit" value="0"  >
                  </div>
                </div>

              </div>
              <div class="row" style="margin-top: -10px">

                <!-- <div class="col-md-6">
                <div class="row"> -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tipe PPN</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <select id="input_add_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePPN('ppn' , 'input_add_tipeppn')">
                      <option value=0 selected>None</option>
                      <option value=1>Exclude</option>
                      <option value=2>Include</option>
                    </select>
                  </div>
                </div>
                <!-- </div>
                </div> -->

              </div>
              <div class="row" style="margin-top: -10px">

                <!-- <div class="col-md-6">
                <div class="row"> INI BUAT BUDI -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Uang Muka</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="number" class="form-control text-right" id="input_add_uangmuka" value="0.00"  onblur="onChangeUangMuka('nuangmuka' , 'input_add_uangmuka' , 'uang muka')">
                  </div>
                </div>

              </div>
            </div>

            <div class="col-md-3 mt-2">
              <div class="row">

              </div>
            </div>

            <div class="col-md-3 mt-2">
              <div class="row">

              </div>
            </div>

          </div>

        </div>

      </div>

      <!-- <div class="row "> -->

      <!-- </div> -->
      <div class="container-fluid">
        <hr/>

      </div>

      <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

        <table id="addTable" class="data-table">
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Sub Total</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>

              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>

          <tbody id="addTableData" class="" >
            <tr >

              <td colspan=7 class="text-center">Belum ada data</td>

            </tr>

          </tbody>

        </table>
      </div>

    </div>

    <div id="" class="container-fluid">
      <div class="row">
        <div class="col-md-12 mt-2 text-right">
          <!-- <button type="button" class="btn btn-chip-biru" onclick="buttonAddAddItem()" class="btn btn-secondary"  >+ Tambah Item</button> -->
          <button type="button" id='buttonTambahItem' class="btn btn-lg btn-chip-biru" style="
        height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
        text-transform: uppercase; transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddAddItem()"><b>Tambah Item</b></button>
        </div>

      </div>
      <!-- <button id="" type="button" class="btn btn-chip-biru" onclick="submitAdd()">Submit</button> -->
    </div>

    <div id="formAddAdd" class="container-fluid showhideitem">
      <!-- <div class="line"></div> -->
      <hr/>
      <div class="row">
        <div class="col-12">
          <h4>Add Item</h4>
        </div>
      </div>
      <div class="row">

        <div class="col-md-6">

          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Barang</label>
              </div>
            </div>
            <!-- <div class="col-md-4 text-right">

            </div> -->
            <div class="col-md-3">
              <div class="input-group form-group">

                <input id="AddAddKodeBrg" type="text" class="form-control" onkeypress="onKeyPressBarang(event)">
                <button type="button" onclick="buttonAddListBarang()" class="btn-chip-biru btn-icon-search"><i class="bi bi-search"></i></button>
              </div>
            </div>

            <div class="col-md-5">
              <div class="input-group form-group">
                <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>

          </div>

        </div>

      </div>
      <div class="row" style="margin-top: -10px">

        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Qty</label>
              </div>
            </div>

            <div class="col-md-3">
              <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>

            <div class="col-md-3">
              <input id="AddAddInputHarga" type="text" data-a-sign="" data-a-dec="." data-a-sep="," value='0.00' class="form-control text-right input-partial-number">
              <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
              <input id="AddAddInputSatuan" type="hidden"  class="form-control  " disabled>
            </div>

          </div>
        </div>

        <div class="col-md-4">

        </div>

      </div>
      <div class="row" style="margin-top: -10px">

        <div class="col-md-6 ">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <!-- <div class="col-md-4 text-right">

            <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-chip-biru" >+</button>
            </div> -->
            <div class="col-md-8">
              <input id="AddAddKeterangan" type="text" class="form-control" >
              <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
            </div>

          </div>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-md-12 text-right mt-4">
          <!-- <button type="button" class="btn btn-secondary" onclick="closeShowHideItem()" >Batal</button> -->
          <button id="" type="button" onclick="closeShowHideItem()" class="btn btn-danger" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
          <!-- <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-chip-biru" >Add</button> -->
          <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-chip-biru" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Simpan</button>

          <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru" >Edit</button> -->
        </div>

      </div>
      <!-- <div class="line"></div> -->
      <!-- <hr/> -->
    </div>

    <div id="formAddEdit" class="container-fluid showhideitem">
      <!-- <div class="line"></div> -->
      <hr/>
      <div class="row">
        <div class="col-12">
          <h4>Edit Item</h4>
        </div>
      </div>
      <div class="row">

        <div class="col-md-4">

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Kode Barang</label>
              </div>
            </div>
            <!-- <div class="col-md-4 text-right"> -->

            <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-chip-biru" >+</button> -->
            <!-- </div> -->
            <div class="col-md-8">
              <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
            </div>

          </div>

        </div>

        <div class="col-md-4">
          <!-- <div class="row"> -->
          <div class="row">
            <div class="col-md-4 ">
              <div class="form-group">
                <label>Nama Barang</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">

                <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>
          </div>

          <!-- </div> -->

        </div>

        <div class="col-md-4">

        </div>

      </div>
      <div class="row" style="margin-top: -10px">

        <div class="col-md-4">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Qty</label>
              </div>
            </div>

            <div class="col-md-8">
              <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right">
            </div>

          </div>
        </div>

        <div class="col-md-4">

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-8">
              <input id="AddEditInputUrutItem" type="hidden"  class="form-control  " disabled>
              <input id="AddEditInputHarga" type="text" data-a-sign="" data-a-dec="." data-a-sep="," value='0.00' class="form-control text-right input-partial-number">
              <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
              <input id="AddEditInputSatuan" type="hidden"  class="form-control  " disabled>
            </div>
          </div>

        </div>
        <!-- <div class="col-md-6">

        </div> -->

        <div class="col-md-4">

        </div>

      </div>
      <div class="row" style="margin-top: -10px">

        <div class="col-md-8">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <!-- <div class="col-md-4 text-right">

            <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-chip-biru" >+</button>
            </div> -->
            <div class="col-md-4">
              <input id="AddEditKeterangan" type="text" class="form-control" >
              <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
            </div>

          </div>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-md-12 text-right mt-4">
          <!-- <button type="button" class="btn btn-secondary" onclick="closeShowHideItem()" >Batal</button>

          <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru" >Edit</button> -->

          <button id="" type="button" onclick="closeShowHideItem()" class="btn btn-danger" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
          <!-- <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-chip-biru" >Add</button> -->
          <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Simpan</button>

          <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru" >Edit</button> -->
        </div>

      </div>
      <!-- <div class="line"></div> -->
      <!-- <hr/> -->
    </div>

  </div>

</div>


<div class="container-fluid mainpage" id="page3" style="display: none">

  <div id="" class="container-fluid ">


    <div id="" class="">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row">
          <div class="col-6 text-left">
            <h1></h1>
          </div>
          <div class="col-6 text-right">
            <!-- <button type="button" class="btn btn-chip-biru btn-lg " style="height: 60px; " onclick="buttonCloseForm()"  >Close</button> -->
            <button type="button" class="btn btn-danger" style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>

          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>


    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-md-3 text-right">
              <div class="form-group">
            <button class="btn btn-chip-biru btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button>
            </div>
          </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi_kodecustomer" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nobukti" placeholder="No Bukti" disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
      <div class="container-fluid" >
        <hr/ style="margin-top: -8px">

      </div>

      <div class="container-fluid mt-4">
      <div class="row">





          <div class="col-md-3">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_otorisasi_namacustomer"  disabled></textarea>
                </div>
              </div>
              <div class="col-12" style="margin-top: -10px">

                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_alamatcustomer"  disabled></textarea>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="row">



          <div class="col-md-6">
            <div class="row">
              <!-- <div class="col-md-6">
                <div class="row"> -->


              <!-- <div class="col-md-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_otorisasi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                    </div>
                  </div>


                <!-- </div>

              </div> -->

              <!-- <div class="col-md-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <!-- <div class="col-md-3">
                    <div class="row">

                    <div class="form-group">
                  <button class="btn btn-chip-biru btn-sm text-right" id="buttonAddListSales" onclick="buttonAddListSales()"><i class="bi bi-plus"></i></button>
                  </div>
                </div>

                  </div> -->
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_sales" value="" disabled >
                      <input type="hidden" class="form-control text-center" id="input_otorisasi_kodesales" value="" disabled >
                    </div>
                  </div>


                <!-- </div>

              </div> -->




            </div>

          </div>

          <div class="col-md-6">
            <div class="row" >



              <!-- <div class="col-md-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>No PO</label>
                    </div>
                  </div>


                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_nopo" value="" disabled  >
                    </div>
                  </div>
                <!-- </div>
              </div> -->

              <!-- <div class="col-md-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">


                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_otorisasi_uangmuka" value="0.00"  disabled>
                    </div>
                  </div>
                <!-- </div>
              </div> -->

            </div>

          </div>

          <div class="col-12">
            <div class="row" style="margin-top: -10px">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Lokasi Penerima</label>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" class="form-control text-center" id="input_otorisasi_kodelokasipenerima" value="" disabled >
                      <input type="text" class="form-control" id="input_otorisasi_lokasipenerima" value="" disabled >
                    </div>
                  </div>

                </div>
              </div>





              <div class="col-md-6">
                <div class="row">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Catatan</label>
                    </div>
                  </div>

                </div>

                <div class="row" style="margin-top: -10px">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_catatan" value="" disabled >
                    </div>
                  </div>

                </div>

              </div>

            </div>




          </div>
        </div>







      </div>

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_valas" value="IDR" disabled >
                </div>
              <!-- </div>
            </div> -->
          </div>



        </div>
          <div class="row" style="margin-top:-10px">


          <!-- <div class="col-md-6">
            <div class="row"> -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_otorisasi_kurs" value="1.00" disabled>
                </div>
              </div>
            <!-- </div>
          </div> -->

        </div>
          <div class="row" style="margin-top: -10px">




          <!-- <div class="col-md-6">
            <div class="row"> -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <select id="input_otorisasi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                    <option value=0 selected>Tunai</option>
                    <option value=1 >Kredit</option>
                  </select>
                </div>
              </div>
            <!-- </div>
          </div> -->

          <!-- <div class="col-md-6">
            <div class="row"> -->

          </div>
            <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control  text-right" id="input_otorisasi_hari" value="0" disabled>
                  <input type="hidden" class="form-control  text-right" id="input_otorisasi_harikredit" value="0" disabled >
                </div>
              </div>
            <!-- </div>
          </div> -->
            </div>

          </div>

          <div class="col-md-3">
            <div class="row">



            </div>
          </div>










        </div>










        </div>



      </div>

      <!-- <div class="row "> -->

  <!-- </div> -->




  <div class="container-fluid">
    <hr/>

  </div>


    <div class="container-fluid mt-4" style="overflow:auto; margin-top:-35px;">
      <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
      <div class="row">
        <table id="otorisasiTable" class="data-table">
          <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>


              </tr>
            </thead>


            <tbody id="otorisasiTableData" class="" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>


    <div id="" class="container-fluid mt-2 showhidepage3 page3otorisasi">
      <div class="row">
        <div class="text-right col-12">
          <button type="button" class="btn btn-chip-biru btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitOtorisasi()" class="btn btn-secondary"><b>Otorisasi</b></button>
        </div>

      </div>
      <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    </div>
    </div>

    </div>


</div></div>
<!-- </div> -->


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div id="" class="modal-content ">


      <div id="modalListCustomer" class="showhideform">
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
            <div class="col-12" style="padding:0; margin:0; width:100%; overflow:auto;">
              <table id="tabel_add_list_customer" class="data-table">
                <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Alamat</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_customer" class="text-left" >

                <tr>

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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
      </div>
      </div>

      <div id="modalListSales" class="showhideform">
      <div class="modal-header">


          <h5 class="modal-title" id="">Sales</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="padding:0; margin:0; width:100%; overflow:auto;">
              <table id="tabel_add_list_sales" class="data-table">
                <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_sales" class="text-left" >

                <tr>

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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
      </div>
      </div>


      <div id="modalListBarang" class="showhideform">
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
            <div class="col-12" style="padding:0; margin:0; width:100%; overflow:auto;">
              <table id="tabel_add_list_barang" class="data-table">
                <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_barang" class="text-left" >

                @for ($i = 0; $i < count($listBarangAll); $i++)
                <tr class="pick-row" onclick="buttonAddAddPickBarangAll('{{ $listBarangAll[$i]->Kodebrg }}' , '{{ $listBarangAll[$i]->Namabrg }}' , '{{ $listBarangAll[$i]->isi1 }}' , '{{ $listBarangAll[$i]->sat1 }}')">

                  <td>{{ $listBarangAll[$i]->Kodebrg }}</td>
                  <td>{{ $listBarangAll[$i]->Namabrg }}</td>




              </tr>
              @endfor
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
        <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
      </div>
      </div>





      <div id="modalListLokasiPenerima" class="showhideform">
      <div class="modal-header">


          <h5 class="modal-title" id="">Lokasi Penerima</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="padding:0; margin:0; width:100%; overflow:auto;">
              <table id="tabel_add_list_lokasi" class="data-table">
                <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_lokasi" class="text-left" >

                <tr>

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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
      </div>
      </div>







      </div>


    </div>
  </div>

<!-- End modal add-->






<div class="modal fade" id="formOtorisasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">








    </div>
  </div>

<!-- End modal oto-->





@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">
let xppncust = 0

let tipeform = 'add'

let dataTableAdd = []
let dataTableKoreksi = []
let dataEdit = {}

let tempKode = ''

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel/poInitReportTableSekali milik
 * purchaseOrder.blade.php, sama seperti so.blade.php/invoicepenjualan.blade.php.
 * Endpoint persistensinya saveheadertable/getheadertable (HeaderTableController)
 * -- halaman ini tidak punya endpoint loadHeader/simpanHeader sendiri sebelumnya,
 * jadi tidak ada kontrak lama yang perlu dipertahankan (beda dari suratjalan.blade.php).
 */
// Disederhanakan jadi satu tabel setelah tab Belum Otorisasi/Sudah Diotorisasi
// digabung jadi satu daftar dengan filter Status Otorisasi (lihat modalFilterIJ
// di section('content')), sama seperti kreditnote.blade.php.
let ijCart = []
const IJ_HREF = 'invoicejasa'
const IJ_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const IJ_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function ijPickCI (row, key) {
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

// Kolom "No SPB"/"Tgl SPB"/"No So"/"Tgl So"/"No Pajak" tidak pernah punya field yang
// cocok di data aslinya (markup lama juga menulis <td></td> kosong untuk semuanya) --
// tetap didaftarkan sebagai kolom (bisa disembunyikan/diurutkan) tapi field-nya sengaja
// nama yang tidak akan pernah cocok dengan row manapun, supaya selalu tampil kosong
// lewat fallback ijValueCell(), sama seperti perlakuan kolom "LokasiPenerima" di
// suratjalan.blade.php.
function ijDefaultCart () {
  return [
    ['NoBukti',      'No. Bukti', 1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',   1, 'date',    0, 0],
    ['NamaCustSupp', 'Customer',  1, 'varchar', 0, 0],
    ['_NoSPB',       'No SPB',    1, 'varchar', 0, 0],
    ['_TglSPB',      'Tgl SPB',   1, 'date',    0, 0],
    ['_NoSo',        'No So',     1, 'varchar', 0, 0],
    ['_TglSo',       'Tgl So',    1, 'date',    0, 0],
    ['_NoPajak',     'No Pajak',  1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto1', 1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto1',  1, 'date',    0, 0],
    ['Isbatal',      'Batal',      1, 'bool',    0, 0],
    ['userBatal',    'User Batal', 1, 'varchar', 0, 0],
    ['tglBatal',     'Tgl Batal',  1, 'date',    0, 0],
  ]
}

function ijBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)
    cart.push([
      values[i],
      h,
      Number(isshowns[i]) === 1 ? 1 : 0,
      IJ_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

window.g_href = IJ_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = ijCart || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(IJ_TIPE_KODE[c[3]] ?? 0)
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
      href     : IJ_HREF,
      urut     : 1
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : IJ_HREF,
      urut   : 1,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        let header = res.headertableheader
        let value = res.headertablevalue
        let isnumeric = res.isnumeric
        let isshown = res.isshown
        let tipe = res.desimal || []
        ijCart = ijBuatCart(header, value, isnumeric, isshown, tipe)
      } else {
        ijCart = ijDefaultCart()
        window.gcart_header = ijCart
        window.doSimpanHeader()
      }
      window.gcart_header = ijCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      ijCart = ijDefaultCart()
      window.gcart_header = ijCart
    }
  })
}

let ijRtSudahInit = false
function ijInitReportTableSekali () {
  if (ijRtSudahInit || typeof ReportTable === 'undefined') { return }
  ijRtSudahInit = true

  ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel })

  let ijGuardUlangKlik = false;
  ['#tabel'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (ijGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      ijGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      ijGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderIJ (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function ijValueCell (row, col) {
  let raw = ijPickCI(row, col[0]);
  let type = col[3];

  if (type === 'date') {
    if (!raw) { return '<td></td>'; }
    return '<td>' + formatDate(raw, '/') + '</td>';
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// Digabung dari tabelActionsCell (Belum Otorisasi: Koreksi/Otorisasi) +
// tabel2ActionsCell (Sudah Diotorisasi: Batal Otorisasi) sejak keduanya
// digabung jadi satu tabel dengan filter Semua/Belum/Sudah Otorisasi.
function tabelActionsCell (row) {
  let nobukti = ijPickCI(row, 'NoBukti');
  let isOto = Number(ijPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'' + ijPickCI(row, 'IsOtorisasi1') + '\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-chip-biru btn-sm" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (ijCart.length ? ijCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += ijValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderIJ('#tabel', cols);
}

let lastTabelRows = []
let ijPanjangHalaman = 10

function ijIkatSearch () {
  let input = document.getElementById('ijSearch1')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function ijIkatPanjangHalaman () {
  let sel = document.getElementById('ijLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(ijPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    ijPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().page.len(ijPanjangHalaman).draw()
    }
  })
}

const IJ_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({
      dom: IJ_DOM_STRING,
      lengthChange: false,
      pageLength: ijPanjangHalaman,
      paging: true,
      order: [[1, 'asc']],
      ordering: false,
    });
    ijIkatSearch();
    ijIkatPanjangHalaman();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function ijResetFilterFields () {
  $('#input_filterij').val('0')
}

function ijUpdateFilterBadge () {
  let n = Number($('#input_filterij').val()) || 0
  $('#ijFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterIJ () {
  let tglawal = $('#input_tanggalawal_ij').val()
  let tglakhir = $('#input_tanggalakhir_ij').val()
  let filterij = $('#input_filterij').val()
  $.ajax({
    url: "{!! url('invoicejasaloadall') !!}",
    type: "get", async: false,
    data: { tglawal, tglakhir, filterij },
    success: function (res) {
      lastTabelRows = res.tempOutstanding
      reinitTabel()
      ijUpdateFilterBadge()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeIJ () {
  let tglawal = $('#input_tanggalawal_ij').val()
  let tglakhir = $('#input_tanggalakhir_ij').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterIJ()
}

function buttonHeaderTable (key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    window.doSetHeader(1, true)
    reinitTabel()
    alertify.success('Kolom telah direset ke tampilan default')
  }, function () {})
}

$(document).ready(function(){
      $('.input-partial-number').autoNumeric('init',
        {
          minimumValue : '0',
        }
      );

      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      ijInitReportTableSekali();

          $("#tabel_add_list_pelanggan").DataTable({
            "lengthChange": false,
              "paging": false ,
              "order": [[1, 'asc']],
              "columnDefs": [
                   {"targets" :[0] , 'orderable' : false}
                ]
          });

          $("#tabel_add_list_sales").DataTable({
            "lengthChange": false,
              "paging": false ,
              "order": [[1, 'asc']],
              "columnDefs": [
                   {"targets" :[0] , 'orderable' : false}
                ]
        });

        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[0, 'asc']],
        });



  //   formAddListItem
});


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function getDateDiff (xdate1 , xdate2) {
  const date1 = new Date(xdate1);
  const date2 = new Date(xdate2);

// Get total difference in milliseconds
  const diffInMs = date2 - date1

// Convert milliseconds into other units
  const diffInDays = diffInMs / (1000 * 60 * 60 * 24);

  console.log(diffInDays);
  return diffInDays

}

function setNewNoBukti (ppn = 1) {
  $.ajax({
    url: "{!! url('invoicejasaspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}

function closeShowHideItem () {

    $('.showhideitem').hide();
}

function closeShowHideAdd () {
  $('.showhideform').hide();
  // if (tipeform = 'add') {
      $('#modalAdd').show();
      console.log('closeShowHideAdd')
      $("#form").modal('toggle');
  // } else {
  //
  // }
}


function buttonAdd () {

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

  // let akses = $("#akses_istambah").val();
  //
  // if (!Number(akses)) {
  //   alertify.warning('No access')
  //   return
  // }
  tipeform = 'add'
  dataTableAdd = []
  console.log('buttonAdd' )

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  resetFormAdd()
  let _token = $("#_token").val();

  // setNewNoBukti()

  $('#buttonAddListCustomer').show();
  $('#buttonAddListSales').show();
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_add_nobukti").value = ''

  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
  // $("#form").modal('toggle')
  $('.mainpage').hide();
  $('#page2').show();


}

function submitAddAdd () {

  console.log('submitAddAdd')
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  if (tipeform == 'add') {
    let xcheckdate = getDateDiff( new Date(), checkDate)
    console.log(new Date(), checkDate)
    console.log(xcheckdate)
    console.log("===========")
    if (xcheckdate > 0 || xcheckdate < -5 ) {
      alertify.warning("Tanggal invoice maks 5 hari sebelum")
      return
    }
  }


  let _token = $("#_token").val();

  let choice = 'I'

  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let kodecustomer = $("#input_add_kodecustomer").val();
  let namacustomer = $("#input_add_namacustomer").val();
  let alamatcustomer = $("#input_add_alamatcustomer").val();
  let tanggal = $("#input_add_tanggal").val();

  // let xcheckdate = getDateDiff( new Date(), tanggal)
  // console.log(new Date(), tanggal)
  // console.log(xcheckdate)
  // console.log("===========")
  // if (xcheckdate > 0 || xcheckdate < -5 ) {
  //   alertify.warning("Tanggal invoice maks 5 hari sebelum")
  //   return
  // }

  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val();
  let tipeppn = $("#input_add_tipeppn").val();
  let kodesales = $("#input_add_kodesales").val();
  let sales = $("#input_add_sales").val();
  let pembayaran = $("#input_add_pembayaran").val();
  let hari = $("#input_add_hari").val();
  let nopo = $("#input_add_nopo").val();
  let uangmuka = $("#input_add_uangmuka").val();
  let catatan = $("#input_add_catatan").val();
  let kodelokasipenerima = $("#input_add_kodelokasipenerima").val();
  let lokasipenerima = $("#input_add_lokasipenerima").val();

  let kodebarang = $("#AddAddKodeBrg").val();
  let namabarang = $("#AddAddNamaBrg").val();
  let qty = $("#AddAddInputQty").val();
  let harga = Number(($("#AddAddInputHarga").val() || '0').replace(/,/g, ''));
  let keterangan = $("#AddAddKeterangan").val();

  if (kodebarang != tempKode) {
    alertify.warning("Kode barang tidak sesuai")
    return
  }

  let urut = 0
  let nosat = 1
  let isi = $("#AddAddInputIsi").val();
  let satuan = $("#AddAddInputSatuan").val();

  let flagtipe = Number(tipeppn) ? 1 : 0
  let ppnbrg = 0

  let jmlrecord = Number(dataTableAdd.length) ? 1 : 0

  if (!kodecustomer || !kodebarang || Number(kurs) < 0 || Number(qty) < 0 || Number(uangmuka) < 0 || Number(hari) < 0 || Number(harga) < 0) {
    alertify.warning("Data tidak lengkap")
    return
  }
  // let jmlrecord = 0

  console.log(
    {
      choice ,
      nobukti ,
      nourut ,
      kodecustomer ,
      namacustomer ,
      alamatcustomer ,
      tanggal ,

      valas ,
      kurs ,
      tipeppn ,
      kodesales ,
      sales ,
      pembayaran ,
      hari ,
      nopo ,
      uangmuka ,
      catatan ,
      kodelokasipenerima ,
      lokasipenerima ,

      kodebarang ,
      namabarang ,
      qty ,
      harga ,
      keterangan ,

      urut,
      nosat,
      isi,
      satuan,
      flagtipe,
      ppnbrg,
      jmlrecord

    }
  )


  $.ajax({
    url: "{!! url('invoicejasaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice ,
      nobukti ,
      nourut ,
      kodecustomer ,
      namacustomer ,
      alamatcustomer ,
      tanggal ,

      valas ,
      kurs ,
      tipeppn ,
      kodesales ,
      sales ,
      pembayaran ,
      hari ,
      nopo ,
      uangmuka ,
      catatan ,
      kodelokasipenerima ,
      lokasipenerima ,

      kodebarang ,
      namabarang ,
      qty ,
      harga ,
      keterangan ,

      urut,
      nosat,
      isi,
      satuan,
      flagtipe,
      ppnbrg,
      jmlrecord

    },
    success: function(res) {
      console.log('res', res)


      if (res == 1 ) {
        tipeform = 'edit'
        loadAll()

        refreshDataTableAdd(nobukti)
        $('#buttonAddListCustomer').hide();
        $('#buttonAddListSales').hide();
        document.getElementById("input_add_tanggal").disabled = true
        $('.showhideitem').hide();
        alertify.success('Berhasil Add item')



      }

      if (res == 2) {
        setNewNoBukti(xppncust)
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })








}



function submitAddEdit () {

  console.log('submitAddEdit')
  // let akses = $("#akses_istambah").val();
  //
  // if (!Number(akses)) {
  //   alertify.warning('No access')
  //   return
  // }

  let _token = $("#_token").val();

  let choice = 'U'

  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let kodecustomer = $("#input_add_kodecustomer").val();
  let namacustomer = $("#input_add_namacustomer").val();
  let alamatcustomer = $("#input_add_alamatcustomer").val();
  let tanggal = $("#input_add_tanggal").val();

  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val();
  let tipeppn = $("#input_add_tipeppn").val();
  let kodesales = $("#input_add_kodesales").val();
  let sales = $("#input_add_sales").val();
  let pembayaran = $("#input_add_pembayaran").val();
  let hari = $("#input_add_hari").val();
  let nopo = $("#input_add_nopo").val();
  let uangmuka = $("#input_add_uangmuka").val();
  let catatan = $("#input_add_catatan").val();
  let kodelokasipenerima = $("#input_add_kodelokasipenerima").val();
  let lokasipenerima = $("#input_add_lokasipenerima").val();

  let kodebarang = $("#AddEditKodeBrg").val();
  let namabarang = $("#AddEditNamaBrg").val();
  let qty = $("#AddEditInputQty").val();
  let harga = Number(($("#AddEditInputHarga").val() || '0').replace(/,/g, ''));
  let keterangan = $("#AddEditKeterangan").val();

  let urut = $("#AddEditInputUrutItem").val();
  let nosat = dataEdit.NOSAT
  let isi = $("#AddEditInputIsi").val();
  let satuan = $("#AddEditInputSatuan").val();

  let flagtipe = Number(tipeppn) ? 1 : 0
  let ppnbrg = 0

  let jmlrecord = Number(dataTableAdd.length) ? 1 : 0
  // let jmlrecord = 0

  console.log(
    {
      choice ,
      nobukti ,
      nourut ,
      kodecustomer ,
      namacustomer ,
      alamatcustomer ,
      tanggal ,

      valas ,
      kurs ,
      tipeppn ,
      kodesales ,
      sales ,
      pembayaran ,
      hari ,
      nopo ,
      uangmuka ,
      catatan ,
      kodelokasipenerima ,
      lokasipenerima ,

      kodebarang ,
      namabarang ,
      qty ,
      harga ,
      keterangan ,

      urut,
      nosat,
      isi,
      satuan,
      flagtipe,
      ppnbrg,
      jmlrecord

    }
  )


  $.ajax({
    url: "{!! url('invoicejasaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice ,
      nobukti ,
      nourut ,
      kodecustomer ,
      namacustomer ,
      alamatcustomer ,
      tanggal ,

      valas ,
      kurs ,
      tipeppn ,
      kodesales ,
      sales ,
      pembayaran ,
      hari ,
      nopo ,
      uangmuka ,
      catatan ,
      kodelokasipenerima ,
      lokasipenerima ,

      kodebarang ,
      namabarang ,
      qty ,
      harga ,
      keterangan ,

      urut,
      nosat,
      isi,
      satuan,
      flagtipe,
      ppnbrg,
      jmlrecord

    },
    success: function(res) {
      console.log('res', res)
      if (res == 1) {
        $('.showhideitem').hide();
        refreshDataTableAdd(nobukti)
        alertify.success('Berhasil Edit item')
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })








}


function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  // if (tipeform == 'edit') {
  //   onChangeHeader('KODEKEBUN' , kode)
  //
  // }
  document.getElementById("input_add_kodelokasipenerima").value = kode
  document.getElementById("input_add_lokasipenerima").value = nama
  onChangeHeader('kodelokasi' , 'input_add_kodelokasipenerima' , 'lokasi penerima')

  closeShowHideAdd()
  // $("#form").modal('toggle')
  // document.getElementById("input_add_lokasipenerima").scrollIntoView();
}


function buttonAddAddPickBarangAll (kode, nama , isi , sat ) {
  console.log('buttonAddAddPickBarangAll')
  console.log(kode,nama, isi , sat)
  // if (tipeform == 'edit') {
  //   onChangeHeader('KODEKEBUN' , kode)
  //
  // }

  tempKode = kode

  document.getElementById("AddAddNamaBrg").value = nama
  document.getElementById("AddAddKodeBrg").value = kode

  document.getElementById("AddAddInputIsi").value = isi

  document.getElementById("AddAddInputSatuan").value = sat

  closeShowHideAdd()
  // $("#form").modal('toggle')
  // document.getElementById("input_add_lokasipenerima").scrollIntoView();
}


function buttonAddListBarang () {


  $('.showhideform').hide();
  $('#modalListBarang').show();


  // // document.getElementById("#tabel_add_list_barang_filter").value = '123'
  // let tempDiv = document.getElementById("tabel_add_list_barang_filter");
  // let tempLabel = tempDiv.querySelector('label');
  // let tempInput = tempDiv.querySelector('input');
  // tempInput.value = '123'
  // var table = $('#tabel_add_list_barang').DataTable();
  // // tempInput.focus()
  // table.search('123').draw()
  $("#form").modal('toggle')




}

function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp) {
    alertify.warning("Isi Customer terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('invoicejasalistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      if ($.fn.DataTable.isDataTable('#tabel_add_list_lokasi')) { $('#tabel_add_list_lokasi').DataTable().destroy(); }
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLokasiPenerima('${item.kodekebun}' , '${item.nama}')">
        <td>${item.kodekebun}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=2>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_lokasi").innerHTML = rowTable
      $("#tabel_add_list_lokasi").DataTable({
        lengthChange: false,
        paging: false,
        searching: true,
      });

      $('.showhideform').hide();
      $('#modalListLokasiPenerima').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function resetFormAdd () {
  console.log('resetFormAdd')
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_lokasipenerima").value = ''
  console.log('a')
  document.getElementById("input_add_tanggal").value = formatDate(new Date())
  document.getElementById("input_add_tipeppn").innerHTML = `
    <option value=0 selected>None</option>
    <option value=1 >Exclude</option>
    <option value=2 >Include</option>
  `
  document.getElementById("input_add_valas").value = 'IDR'
  document.getElementById("input_add_kurs").value = '1.00'
  document.getElementById("input_add_tipeppn").value = 0
  document.getElementById("input_add_sales").value = ''
  console.log('b')
  document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_nopo").value = ''
  document.getElementById("input_add_uangmuka").value = '0.00'
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = 0

  document.getElementById("addTableData").innerHTML = `
    <tr><td colspan=7 class='text-center'>Belum ada data</td></tr>
  `

}

function buttonAddListSales () {
  $('#tabel_add_list_sales').DataTable().destroy();
  $.ajax({
    url: "{!! url('invoicejasalistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')">
        <td>${item.keynik}</td>
        <td>${item.nama}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=2>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      $("#tabel_add_list_sales").DataTable({
        lengthChange: false,
        paging: false,
        searching: true,
    });
      $('.showhideform').hide();
      $('#modalListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onKeyPressBarang(e) {
  if (e.which == 13) {
    let kodebrg = $('#AddAddKodeBrg').val();

    // document.getElementById("#tabel_add_list_barang_filter").value = '123'
    let tempDiv = document.getElementById("tabel_add_list_barang_filter");
    let tempLabel = tempDiv.querySelector('label');
    let tempInput = tempDiv.querySelector('input');

    var table = $('#tabel_add_list_barang').DataTable();
    // tempInput.focus()
    table.search(kodebrg).draw()
    console.log(table.rows({ search: 'applied' }).count() == 1)
    if (table.rows({ search: 'applied' }).count() == 1) {
      // console.log('if yes')
      let tempxData = table.rows({ search: 'applied' }).data().toArray();
      // console.log(tempxData)
      // console.log()

      tempKode = tempxData[1]
      document.getElementById("AddAddKodeBrg").value = tempxData[0][0]
      document.getElementById("AddAddNamaBrg").value = tempxData[0][1]
      document.getElementById("AddAddInputIsi").value = 1
      document.getElementById("AddAddInputSatuan").value = ''

      // console.log(table.rows({ search: 'applied' }).indexes())
    } else {
      // console.log('if no')
      // console.log(table.rows({ search: 'applied' }).indexes())
        $('.showhideform').hide();
        $('#modalListBarang').show();
        $("#form").modal('toggle')

    }
  }

}

function buttonAddEditItem (index) {
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonAddEditItem')
  dataEdit = dataTableAdd[index]


  document.getElementById("AddEditInputIsi").value = dataEdit.ISI

  document.getElementById("AddEditInputSatuan").value = dataEdit.Satuan

  document.getElementById("AddEditKodeBrg").value = dataEdit.KodeBrg
  document.getElementById("AddEditNamaBrg").value = dataEdit.NAMABRG
  document.getElementById("AddEditKeterangan").value = dataEdit.KetDetail
  document.getElementById("AddEditInputQty").value = parseFloat(dataEdit.Qnt).toFixed(2)
  document.getElementById("AddEditInputHarga").value = parseFloat(dataEdit.HARGA).toFixed(2)

  document.getElementById("AddEditInputUrutItem").value = dataEdit.Urut


  $('.showhideitem').hide();
  $('#formAddEdit').show();

  document.getElementById("AddEditKodeBrg").scrollIntoView();
}

function buttonAddAddItem () {
  tempKode = ''
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonAddAddItem')
    // tipeformitem = 'Add'
    document.getElementById("AddAddKodeBrg").value = ''
    document.getElementById("AddAddNamaBrg").value = ''
    // document.getElementById("AddAddKodeGdg").value = ''
    // document.getElementById("AddAddNamaGdg").value = ''
    document.getElementById("AddAddKeterangan").value = ''
    document.getElementById("AddAddInputQty").value = '0.00'
    document.getElementById("AddAddInputHarga").value = '0.00'


    $('.showhideitem').hide();
    $('#formAddAdd').show();

    document.getElementById("AddAddKodeBrg").scrollIntoView();

}


function buttonAddListCustomer () {
  console.log('buttonAddListCustomer')
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('invoicejasalistcustomer') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log('res' , res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer('${item.kodecustsupp}' , '${item.namacustsupp}' , '${item.alamat1}', '${item.hari}' ,  '${item.PPNCUST}')">
        <td>${item.kodecustsupp}</td>
        <td>${item.namacustsupp}</td>
        <td>${item.alamat1}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      $("#tabel_add_list_customer").DataTable({
        lengthChange: false,
        paging: false,
        searching: true,
    });
      $('.showhideform').hide();
      console.log('a')
      $('#modalListCustomer').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  // if (tipeform == 'edit') {
  //   onChangeHeader('KODESLS' , kode)
  //
  // }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_sales").value = nama
  closeShowHideAdd()
  // $("#form").modal('toggle')
}

function buttonAddPickCustomer (kode, nama , alamat , harikredit , ppncust) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat , harikredit)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  if (Number(harikredit) > 0) {
    document.getElementById("input_add_pembayaran").value = 1
  }
  document.getElementById("input_add_hari").value = harikredit
  document.getElementById("input_add_harikredit").value = harikredit
  document.getElementById("input_add_lokasipenerima").value = ''

  if (Number(ppncust)) {
    document.getElementById("input_add_tipeppn").innerHTML = `
    <option value=1 selected >Exclude</option>
    <option value=2 >Include</option>
    `
    xppncust = 1
    setNewNoBukti(1)

  } else {
    document.getElementById("input_add_tipeppn").innerHTML = `
    <option value=0 selected>None</option>
    `
    xppncust = 0
    setNewNoBukti(0)
  }



  closeShowHideAdd()
  // $("#form").modal('toggle')
}


function onChangeKurs (field, idValue , alias = field) {
  if (tipeform == 'add') {
    return
  }
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let value = $(`#${idValue}`).val()
  console.log(value)
  if (Number(value) < 0) {
    value = '1.00'
    document.getElementById("input_add_kurs").value = value
    // onChangeHeader(field, value , alias )
    // onChangeDetail(field, value , alias )


    $.ajax({
      url: "{!! url('invoicejasaonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        alertify.success(`update ${alias} berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    $.ajax({
      url: "{!! url('invoicejasaonchangedetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        // console.log()
        alertify.success(`update ${alias} detail berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    refreshDataTableAdd(nobukti)




    alertify.warning('Kurs tidak bisa kurang dari 0')
  } else {
    onChangeHeader(field, idValue , alias )
    onChangeDetail(field, idValue , alias )
    refreshDataTableAdd(nobukti)
  }

}


function onChangePPN (field, idValue , alias = field) {
  if (tipeform == 'add') {
    return
  }
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let value = $(`#${idValue}`).val()
  // console.log(value)
  let flagtipe = 0
  if (Number(value)) {
    flagtipe = 1
  }

  onChangeHeader(field, idValue , alias = field)
  onChangeDetail(field, idValue , alias = field)

  $.ajax({
    url: "{!! url('invoicejasaonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field : 'flagtipe',
      value : flagtipe,
      nobukti
    },
    success: function(res) {
      alertify.success(`update flagtipe berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })



  refreshDataTableAdd(nobukti)

}





function onChangeUangMuka (field, idValue , alias = field) {
  if (tipeform == 'add') {
    return
  }
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let value = $(`#${idValue}`).val()
  console.log(value)
  if (Number(value) < 0) {
    value = '0.00'
    document.getElementById("input_add_uangmuka").value = value
    // onChangeHeader(field, value , alias )
    // onChangeDetail(field, value , alias )


    $.ajax({
      url: "{!! url('invoicejasaonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        alertify.success(`update ${alias} berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })






    alertify.warning('Uang Muka tidak bisa kurang dari 0')
  } else {
    onChangeHeader(field, idValue , alias )
  }

}


function onChangeHeader (field, idValue , alias = field) {
  if(tipeform == 'add' ) {

    return
  }
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let value = $(`#${idValue}`).val()
  $.ajax({
    url: "{!! url('invoicejasaonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${alias} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeDetail (field, idValue , alias = field) {
  if (tipeform == 'add') {
    return
  }
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let value = $(`#${idValue}`).val()
  $.ajax({
    url: "{!! url('invoicejasaonchangedetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      // console.log()
      alertify.success(`update ${alias} detail berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function onChangePembayaran () {


  let _token = $("#_token").val();
  let check = Number($("#input_add_pembayaran").val())
  let nobukti = $("#input_add_nobukti").val()
  let valueHari = 0
  // let field = 'tipebayar'

  if (check) {
    valueHari = $("#input_add_harikredit").val()
    document.getElementById("input_add_hari").value = valueHari




  } else {
    valueHari = 0
    document.getElementById("input_add_hari").value = valueHari

  }

  if (tipeform == 'add') {
    return
  }

  $.ajax({
    url: "{!! url('invoicejasaonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field : 'tipebayar',
      value : check,
      nobukti
    },
    success: function(res) {
      alertify.success(`update pembayaran berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  $.ajax({
    url: "{!! url('invoicejasaonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field : 'hari',
      value : valueHari,
      nobukti
    },
    success: function(res) {
      alertify.success(`update hari berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function refreshDataTableAdd (nobukti) {
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('invoicejasaspdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {

        console.log(res)
        if (!res.length) {
            alertify.success('Data Habis')
            // $("#form").modal('toggle')
            $('#page2').hide();
            $('#page1').show();
            return
        }
        dataTableAdd = res

        let rowTable = ``
        dataTableAdd.forEach((item, i) => {
                rowTable += `
                  <tr>
                    <td>${item.KodeBrg}</td>
                    <td>${item.NAMABRG}</td>
                    <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                    <td class="text-right">${item.HARGA ?  formatAngka(parseFloat(item.HARGA).toFixed(2)) : '0.00'}</td>
                    <td class="text-right">${item.SubTotalRp ? formatAngka(parseFloat(item.SubTotalRp).toFixed(2)) : '0.00'}</td>

                    <td>${item.KetDetail ? item.KetDetail : ''}</td>

                    <td class="text-center">
                      <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i}  )"><i class="bi bi-trash"></i></button>
                    </td>
                  </tr>

                `
        });

        document.getElementById("addTableData").innerHTML = rowTable

        document.getElementById("input_add_nobukti").value = dataTableAdd[0].NoBukti
        document.getElementById("input_add_nourut").value = dataTableAdd[0].NoUrut
        document.getElementById("input_add_namacustomer").value = dataTableAdd[0].NamaCustSupp
        document.getElementById("input_add_kodecustomer").value = dataTableAdd[0].KodeCustSupp
        document.getElementById("input_add_kodelokasipenerima").value = dataTableAdd[0].KodeKebun
        document.getElementById("input_add_lokasipenerima").value = dataTableAdd[0].NamaKebun
        document.getElementById("input_add_alamatcustomer").value = dataTableAdd[0].Alamat
        document.getElementById("input_add_catatan").value = dataTableAdd[0].FootNote

        document.getElementById("input_add_tanggal").value = formatDate(dataTableAdd[0].Tanggal)

        document.getElementById("input_add_valas").value = dataTableAdd[0].Valas
        document.getElementById("input_add_kurs").value = parseFloat(dataTableAdd[0].Kurs).toFixed(2)
        document.getElementById("input_add_tipeppn").value = dataTableAdd[0].PPN
        document.getElementById("input_add_kodesales").value = dataTableAdd[0].KODESLS
        document.getElementById("input_add_sales").value = dataTableAdd[0].NamaSls

        document.getElementById("input_add_pembayaran").value = dataTableAdd[0].TIPEBAYAR
        document.getElementById("input_add_hari").value = dataTableAdd[0].HARI
        document.getElementById("input_add_harikredit").value = dataTableAdd[0].harikredit

        document.getElementById("input_add_uangmuka").value = parseFloat(dataTableAdd[0].nUangMuka).toFixed(2)

        document.getElementById("input_add_nopo").value = dataTableAdd[0].PONo


        $('#buttonAddListCustomer').hide();
        $('#buttonAddListSales').hide();

        $('.showhideitem').hide();
        $('.showhideform').hide();
        $('#modalAdd').show();








      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
      }

    })
}

function buttonKoreksi (nobukti , isoto) {

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

  console.log('buttonKoreksi' , nobukti , isoto)

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (Number(isoto)) {
    alertify.warning('Nobukti sudah diotorisasi')
    return
  }


  tipeform = 'edit'
  dataTableAdd = []

  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('invoicejasaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      dataTableAdd = res

      // if (dataTableAdd.)

      let rowTable = ``
      dataTableAdd.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.KodeBrg}</td>
                  <td>${item.NAMABRG}</td>
                  <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) :  ''}</td>
                  <td class="text-right">${item.HARGA ? formatAngka(parseFloat(item.HARGA).toFixed(2)) : ''}</td>
                  <td class="text-right">${item.SubTotalRp ? formatAngka(parseFloat(item.SubTotalRp).toFixed(2)) : ''}</td>

                  <td>${item.KetDetail ? item.KetDetail : ''}</td>

                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i} ,'${item.NoBukti}' , '${item.NAMABRG}' , '${item.Urut}'  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `
      });

      document.getElementById("addTableData").innerHTML = rowTable

      document.getElementById("input_add_nobukti").value = dataTableAdd[0].NoBukti
      document.getElementById("input_add_nourut").value = dataTableAdd[0].NoUrut
      document.getElementById("input_add_namacustomer").value = dataTableAdd[0].NamaCustSupp
      document.getElementById("input_add_kodecustomer").value = dataTableAdd[0].KodeCustSupp
      document.getElementById("input_add_kodelokasipenerima").value = dataTableAdd[0].KodeKebun
      document.getElementById("input_add_lokasipenerima").value = dataTableAdd[0].NamaKebun
      document.getElementById("input_add_alamatcustomer").value = dataTableAdd[0].Alamat
      document.getElementById("input_add_catatan").value = dataTableAdd[0].FootNote

      document.getElementById("input_add_tanggal").value = formatDate(dataTableAdd[0].Tanggal)
      document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=0 selected>None</option>
        <option value=1 >Exclude</option>
        <option value=2 >Include</option>
      `
      document.getElementById("input_add_valas").value = dataTableAdd[0].Valas
      document.getElementById("input_add_kurs").value = parseFloat(dataTableAdd[0].Kurs).toFixed(2)

      if (Number(dataTableAdd[0].PPN)) {
        document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=1 >Exclude</option>
        <option value=2 >Include</option>
        `

      } else {
        document.getElementById("input_add_tipeppn").innerHTML = `
        <option value=0 selected>None</option>
        `
      }


      document.getElementById("input_add_tipeppn").value = dataTableAdd[0].PPN
      document.getElementById("input_add_kodesales").value = dataTableAdd[0].KODESLS
      document.getElementById("input_add_sales").value = dataTableAdd[0].NamaSls


      document.getElementById("input_add_pembayaran").value = dataTableAdd[0].TIPEBAYAR
      document.getElementById("input_add_hari").value = dataTableAdd[0].HARI
      document.getElementById("input_add_uangmuka").value = parseFloat(dataTableAdd[0].nUangMuka).toFixed(2)
      document.getElementById("input_add_harikredit").value = dataTableAdd[0].harikredit
      document.getElementById("input_add_nopo").value = dataTableAdd[0].PONo

      document.getElementById("input_add_tanggal").disabled = true
      $('#buttonAddListCustomer').hide();
      $('#buttonAddListSales').hide();

      $('.showhideitem').hide();
      $('.showhideform').hide();
      $('#modalAdd').show();
      // $("#form").modal('toggle')

      // $('.mainpage').hide();
      $('#page1').hide();
      $('#page2').show();






    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      // resRefresh = 0;
    }

  })



}

function buttonOtorisasi (nobukti) {
  console.log('buttonOtorisasi' , nobukti)
  // tipeform = 'edit'
  // dataTableAdd = []

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('invoicejasaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      // dataTableAdd = res

      let rowTable = ``
      res.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.KodeBrg}</td>
                  <td>${item.NAMABRG}</td>
                  <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.HARGA ? formatAngka(parseFloat(item.HARGA).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.SubTotalRp ? formatAngka(parseFloat(item.SubTotalRp).toFixed(2)) : '0.00'}</td>

                  <td>${item.KetDetail ? item.KetDetail : ''}</td>


                </tr>

              `
      });

      document.getElementById("otorisasiTableData").innerHTML = rowTable

      document.getElementById("input_otorisasi_nobukti").value = res[0].NoBukti
      // document.getElementById("input_otorisasi_nourut").value = res[0].NoUrut
      document.getElementById("input_otorisasi_namacustomer").value = res[0].NamaCustSupp
      document.getElementById("input_otorisasi_kodecustomer").value = res[0].KodeCustSupp
      document.getElementById("input_otorisasi_kodelokasipenerima").value = res[0].KodeKebun
      document.getElementById("input_otorisasi_lokasipenerima").value = res[0].NamaKebun
      document.getElementById("input_otorisasi_alamatcustomer").value = res[0].Alamat
      document.getElementById("input_otorisasi_catatan").value = res[0].FootNote

      document.getElementById("input_otorisasi_tanggal").value = formatDate(res[0].Tanggal)

      document.getElementById("input_otorisasi_valas").value = res[0].Valas
      document.getElementById("input_otorisasi_kurs").value = parseFloat(res[0].Kurs).toFixed(2)
      document.getElementById("input_otorisasi_tipeppn").value = res[0].PPN
      document.getElementById("input_otorisasi_kodesales").value = res[0].KODESLS
      document.getElementById("input_otorisasi_sales").value = res[0].NamaSls

      document.getElementById("input_otorisasi_pembayaran").value = res[0].TIPEBAYAR
      document.getElementById("input_otorisasi_hari").value = res[0].HARI
      document.getElementById("input_otorisasi_uangmuka").value = parseFloat(res[0].nUangMuka).toFixed(2)
      document.getElementById("input_otorisasi_harikredit").value = res[0].harikredit
      document.getElementById("input_otorisasi_nopo").value = res[0].PONo


      $('.showhidepage3').hide();
      $('.page3otorisasi').show();
      $('.mainpage').hide();
      // $('#page1').hide();
      $('#page3').show();





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      // resRefresh = 0;
    }

  })



}

function buttonDetail (nobukti) {
  console.log('buttonDetail' , nobukti)

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('invoicejasaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.KodeBrg}</td>
                  <td>${item.NAMABRG}</td>
                  <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.HARGA ? formatAngka(parseFloat(item.HARGA).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.SubTotalRp ? formatAngka(parseFloat(item.SubTotalRp).toFixed(2)) : '0.00'}</td>

                  <td>${item.KetDetail ? item.KetDetail : ''}</td>


                </tr>

              `
      });

      document.getElementById("otorisasiTableData").innerHTML = rowTable

      document.getElementById("input_otorisasi_nobukti").value = res[0].NoBukti
      document.getElementById("input_otorisasi_namacustomer").value = res[0].NamaCustSupp
      document.getElementById("input_otorisasi_kodecustomer").value = res[0].KodeCustSupp
      document.getElementById("input_otorisasi_kodelokasipenerima").value = res[0].KodeKebun
      document.getElementById("input_otorisasi_lokasipenerima").value = res[0].NamaKebun
      document.getElementById("input_otorisasi_alamatcustomer").value = res[0].Alamat
      document.getElementById("input_otorisasi_catatan").value = res[0].FootNote

      document.getElementById("input_otorisasi_tanggal").value = formatDate(res[0].Tanggal)

      document.getElementById("input_otorisasi_valas").value = res[0].Valas
      document.getElementById("input_otorisasi_kurs").value = parseFloat(res[0].Kurs).toFixed(2)
      document.getElementById("input_otorisasi_tipeppn").value = res[0].PPN
      document.getElementById("input_otorisasi_kodesales").value = res[0].KODESLS
      document.getElementById("input_otorisasi_sales").value = res[0].NamaSls

      document.getElementById("input_otorisasi_pembayaran").value = res[0].TIPEBAYAR
      document.getElementById("input_otorisasi_hari").value = res[0].HARI
      document.getElementById("input_otorisasi_uangmuka").value = parseFloat(res[0].nUangMuka).toFixed(2)
      document.getElementById("input_otorisasi_harikredit").value = res[0].harikredit
      document.getElementById("input_otorisasi_nopo").value = res[0].PONo


      $('.showhidepage3').hide();
      $('.mainpage').hide();
      $('#page3').show();

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonBatalOtorisasi (nobukti) {
  console.log('buttonBatalOtorisasi' , nobukti)
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
          url: "{!! url('invoicepenjualanspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Batal Otorisasi Invoice')

          },
          error: function (err) {
            console.log(err)
            // Update status batal-otorisasi-nya sendiri jalan duluan sebelum proses
            // posting hutang-piutang/jurnal susulan -- kalau proses susulan itu yang
            // gagal, statusnya sudah kepakai duluan di database walau request ini
            // balik sebagai error. loadAll() supaya tabel langsung menampilkan status
            // yang sebenarnya tanpa user harus refresh manual.
            loadAll()
            alertify.error('Batal otorisasi kemungkinan sudah tersimpan, namun proses lanjutan (posting hutang-piutang/jurnal) gagal. Data telah dimuat ulang -- silahkan periksa status terbaru pada tabel, dan hubungi admin jika perlu diproses ulang.')
          }

        })
      }
    ,function(){
      console.log('no')
       alertify.error("Action cancelled");
    });

}

function submitOtorisasi () {
  console.log('submitOtorisasi')
  let _token = $("#_token").val();
  let nobukti =  $("#input_otorisasi_nobukti").val();
  console.log(nobukti)
  $.ajax({
    url: "{!! url('invoicepenjualanspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log('!', res)
      loadAll()

      // lockFormAdd()
      // $("#formOtorisasi").modal('toggle')
      buttonCloseForm()
      alertify.success('Berhasil Otorisasi')

    },
    error: function (err) {
      console.log(err)
      // Sama seperti buttonBatalOtorisasi() -- update status otorisasi-nya sendiri
      // bisa saja sudah kepakai duluan di database sebelum proses posting susulan
      // gagal, jadi refresh otomatis + pesan yang lebih jelas daripada cuma bilang
      // "terjadi kesalahan" tanpa konteks.
      loadAll()
      buttonCloseForm()
      alertify.error('Otorisasi kemungkinan sudah tersimpan, namun proses lanjutan (posting hutang-piutang/jurnal) gagal. Data telah dimuat ulang -- silahkan periksa status terbaru pada tabel, dan hubungi admin jika perlu diproses ulang.')
    }

  })
}

function buttonAddDelete (index) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonAddDelete')
  let tempData = dataTableAdd[index]
  console.log(tempData)
  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + tempData.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"





        $.ajax({
          url: "{!! url('invoicejasaspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice ,
            nobukti : tempData.NoBukti ,
            nourut : '',
            kodecustomer: '' ,
            namacustomer: '' ,
            alamatcustomer: '' ,
            tanggal: '' ,

            valas : '',
            kurs: 0 ,
            tipeppn: 0 ,
            kodesales: '' ,
            sales : '' ,
            pembayaran : 0 ,
            hari : 0,
            nopo: '' ,
            uangmuka: 0 ,
            catatan : '' ,
            kodelokasipenerima: '' ,
            lokasipenerima : '',

            kodebarang : '',
            namabarang : '' ,
            qty: 0 ,
            harga : 0,
            keterangan : '' ,

            urut: tempData.Urut,
            nosat : 0,
            isi: 0,
            satuan : '',
            flagtipe : 0,
            ppnbrg: 0,
            jmlrecord :0

          },
          success: function(res) {
            console.log('res', res)


            if (res == 1 ) {
              // tipeform = 'edit'
              loadAll()

              $('.showhideitem').hide();
              refreshDataTableAdd(tempData.NoBukti)
              alertify.success('Berhasil Hapus item')



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




function loadAll () {
  console.log('loadAll')
  let tglawal = $('#input_tanggalawal_ij').val()
  let tglakhir = $('#input_tanggalakhir_ij').val()
  let filterij = $('#input_filterij').val()

          $.ajax({
            url: "{!! url('invoicejasaloadall') !!}",
            type: "get",
            async: false,
            data: {
              tglawal, tglakhir, filterij
            },
            success: function(res) {
              lastTabelRows = res.tempOutstanding;
              reinitTabel();
            },
            error: function (err) {
              console.log(err)
              console.log(err.status)
              console.log(err.statusText)
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
  temp1 += '.' + tempAngka[1]
  return temp1
}



</script>



@endsection
