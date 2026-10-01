@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Giro Dibuka')
@section('title', 'SML - Giro Dibuka')

@endsection

{{-- Rerouted to newmasterTest to match Purchase Order's UI 1:1, same pattern applied
     to giroditerima.blade.php before it. Only layout/tab-bar/toolbar/column-header
     interactivity changed for #tabel (the main Giro list) and the "+"-button pickers
     inside the Add/Koreksi form; all business-logic functions are untouched.
     Old @extends('accounting.newmaster') pointed at a layout file that does not exist
     anywhere in resources/views -- this page has been a hard "view not found" error on
     every load regardless of anything else; switching to newmasterTest fixes that as a
     side effect of the port. --}}
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

/* {{-- "Tampilkan" (page-length) dropdown -- copied verbatim from so.blade.php's own
     @section('css'). Not part of po-table-header.css, so page-local like the other
     already-ported pages. --}} */
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

/* {{-- Kolom Aksi -- pastel round-button treatment, copied verbatim (rescoped to this
     page's own #tabel/#addTable/#detailTable/#giroModalTable/#giroBGTModalTable/
     #tabel_add_list_dphuhtbbm/#tabel_add_list_pencairangiroedit) from so.blade.php's
     own @section('css'). Unlike the other ids here, #tabel's Actions column is
     actually FIRST (tabelActionsCell() is prepended before the data cells in
     renderTabelRows()), so #tabel is additionally scoped with td:first-child; the
     other ids keep td:last-child, matching addTable's own existing convention in
     invoicejasa.blade.php/so.blade.php. The extra ids are the other data-entry grids
     in this file that also have a real (non-picker) Actions column -- add/koreksi
     line items, giro correction entries. --}} */

#tabel td:first-child,
#tabel td:last-child,
#addTable td:last-child,
#detailTable td:last-child,
#giroModalTable td:last-child,
#giroBGTModalTable td:last-child,
#tabel_add_list_dphuhtbbm td:last-child,
#tabel_add_list_pencairangiroedit td:last-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn,
#tabel td:last-child .btn,
#addTable td:last-child .btn,
#detailTable td:last-child .btn,
#giroModalTable td:last-child .btn,
#giroBGTModalTable td:last-child .btn,
#tabel_add_list_dphuhtbbm td:last-child .btn,
#tabel_add_list_pencairangiroedit td:last-child .btn {
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
#tabel td:last-child .btn:hover,
#addTable td:last-child .btn:hover,
#detailTable td:last-child .btn:hover,
#giroModalTable td:last-child .btn:hover,
#giroBGTModalTable td:last-child .btn:hover,
#tabel_add_list_dphuhtbbm td:last-child .btn:hover,
#tabel_add_list_pencairangiroedit td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success,
#tabel td:last-child .btn-success,
#addTable td:last-child .btn-success,
#detailTable td:last-child .btn-success,
#giroModalTable td:last-child .btn-success,
#giroBGTModalTable td:last-child .btn-success,
#tabel_add_list_dphuhtbbm td:last-child .btn-success,
#tabel_add_list_pencairangiroedit td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel td:first-child .btn-warning,
#tabel td:last-child .btn-warning,
#addTable td:last-child .btn-warning,
#detailTable td:last-child .btn-warning,
#giroModalTable td:last-child .btn-warning,
#giroBGTModalTable td:last-child .btn-warning,
#tabel_add_list_dphuhtbbm td:last-child .btn-warning,
#tabel_add_list_pencairangiroedit td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel td:first-child .btn-primary,
#tabel td:last-child .btn-primary,
#addTable td:last-child .btn-primary,
#detailTable td:last-child .btn-primary,
#giroModalTable td:last-child .btn-primary,
#giroBGTModalTable td:last-child .btn-primary,
#tabel_add_list_dphuhtbbm td:last-child .btn-primary,
#tabel_add_list_pencairangiroedit td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-danger,
#tabel td:last-child .btn-danger,
#addTable td:last-child .btn-danger,
#detailTable td:last-child .btn-danger,
#giroModalTable td:last-child .btn-danger,
#giroBGTModalTable td:last-child .btn-danger,
#tabel_add_list_dphuhtbbm td:last-child .btn-danger,
#tabel_add_list_pencairangiroedit td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel td:first-child .btn-info,
#tabel td:last-child .btn-info,
#addTable td:last-child .btn-info,
#detailTable td:last-child .btn-info,
#giroModalTable td:last-child .btn-info,
#giroBGTModalTable td:last-child .btn-info,
#tabel_add_list_dphuhtbbm td:last-child .btn-info,
#tabel_add_list_pencairangiroedit td:last-child .btn-info {
  color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
}

/* Hide action buttons until the row is hovered -- #tabel only (addTable/detailTable
   are small data-entry grids where the actions should stay visible). */
#tabel tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

/* {{-- "+"-button pickers converted to click-anywhere-on-row: cursor + hover
     feedback, same convention as so.blade.php's own picker modals. --}} */
.pick-row { cursor: pointer; }
.pick-row:hover { background-color: #f5f3ff !important; }

/* {{-- Chip buttons, copied verbatim from so.blade.php's own @section('css'). --}} */
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

/* Search-icon button appended flush to an input (e.g. Perkiraan picker). */
.btn-icon-search {
  height: 32px;
  border-radius: 0;
  display: flex;
  align-items: center;
  justify-content: center;
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
@endsection


@section('content')


<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >



  <!-- <div id="qrcode"></div> -->
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
  {{-- <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Giro</a>
      </div>
    </div>
  </div> --}}
  <div class="card">
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">

              <div class="po-toolbar">
                <div class="po-filter-wrap">
                  <label>Periode</label>
                  <input type="date" onchange="onChangePeriodeGiro()" class="po-filter-inp" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
                  <span class="po-filter-sep">s/d</span>
                  <input type="date" onchange="onChangePeriodeGiro()" class="po-filter-inp" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
                </div>
                <input type="search" id="giroSearch1" class="po-search-inp" placeholder="Cari data">
                <div class="po-len-wrap"><label for="giroLen1">Tampilkan</label>
                  <select id="giroLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                </div>
                <button class="po-btn-filter" type="button" onclick="$('#modalFilterGiro').modal('show')"><i class="bi bi-funnel"></i> Filter</button>
                <div class="po-toolbar-act">
                  <button id="AddVisibility" class="btn btn-primary" onclick="buttonAdd()">Tambah</button>
                </div>
              </div>

              {{-- Filter Transaksi (BBG/BCG), pola modal rt-filter sama seperti
                   modalFilterSPB milik suratjalan.blade.php -- klien-side saja,
                   memfilter lastTabelRows sebelum di-render, karena tabel ini
                   sudah full-load (bukan server-paginated). --}}
              <div class="modal fade rt-filter" id="modalFilterGiro">
                <div class="modal-dialog modal-md">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title"><i class="bi bi-funnel"></i> Filter Data</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterGiro').modal('hide')"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                      <div class="rt-section">
                        <div class="rt-group-label">Status</div>
                        <div>
                          <label class="rt-field-label" for="input_filtertransgiro">Transaksi</label>
                          <select class="rt-native" id="input_filtertransgiro">
                            <option value="" selected>Semua</option>
                            <option value="BBG">BBG</option>
                            <option value="BCG">BCG</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="rt-reset-link" onclick="$('#input_filtertransgiro').val('')">Reset semua</button>
                      <div class="rt-footer-buttons">
                        <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal" onclick="$('#modalFilterGiro').modal('hide')">Batal</button>
                        <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterGiro(); $('#modalFilterGiro').modal('hide');">Terapkan</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="rtBarTabel"></div>
              <table id="tabel" class="data-table">
                <thead style="white-space:nowrap;"></thead>
                <tbody id="tabel_data" class="text-left"></tbody>
              </table>
              <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
        </div>
      </div>
    </div>
  </div>




</div>
</div>
</div>


</div>
</div>

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      {{-- <h2>Giro Dibuka</h2> --}}
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div id= "formAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">
        <div class="col-md-2">
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
              <label>Transaksi</label>
            </div>
            </div>

            <div class="col-md-7">
              <select id="input_add_transaksi" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onChange="onChangeTransaksi()">
                <option value='BBG' selected>BBG</option>
                <option value='BCG' >BCG</option>
              </select>
            </div>

          </div>

          <div class="row" style="margin-top: -10px">



          <div class="col-md-5">
            <div class="form-group">
              <label>G.Terima</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">
          </div>
        </div> -->
          <div class="col-md-7">
            <div class="form-group input-group">
              <input type="hidden" class="form-control" id="input_add_simbol" placeholder="" disabled>
              <input type="text" class="form-control" id="input_add_kodeperkiraan" placeholder="" disabled>
              <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListPerkiraan" onclick="buttonAddListPerkiraan()"><i class="bi bi-search"></i></button>
            </div>
          </div>


          <div class="col-md-12" style="margin-top:-10px">
            <div class="form-group">
              <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_add_keteranganperkiraan"  disabled></textarea>
            </div>
          </div>


          </div>

        </div>

        <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12" >
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



              </div>
            </div>

            <div class="col-md-6">
              <div class="row">

                <div class="col-md-12" >
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





              </div>
            </div>
          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-12">
              <div class="row">


            <div class="col-md-2">
              <div class="form-group">
                <label class="">Terima</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kepadaterima" placeholder="" >
              </div>
            </div>
          </div>

            </div>

          </div>

          <!-- <div class="row" style="margin-top: -10px" class="" >
            <div class="col-md-12">
              <div class="row">


            <div class="col-md-2">
              <div class="form-group">
                <label class="">Bon</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input type="text" class="form-control " id="input_add_bon" placeholder="" >
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label class="">Nilai Bon</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input type="number" class="form-control text-right " id="input_add_nilaibon" value="0.00" disabled>
              </div>
            </div>
          </div>

            </div>

          </div> -->

        </div>








      </div>














      </div>



<div class="container-fluid">
  <hr/>

</div>



  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="data-table"  >
          <thead>
            <tr>
              <th style="padding: 4px 12px;" scope="col">Devisi</th>
              <th style="padding: 4px 12px;" scope="col">Perk.</th>
              <th style="padding: 4px 12px;" scope="col">Ket. Perk</th>
              <th style="padding: 4px 12px;" scope="col">Lawan</th>
              <th style="padding: 4px 12px;" scope="col">Ket. Lawan</th>
              <th style="padding: 4px 12px;" scope="col">Sumber</th>
              <th style="padding: 4px 12px;" scope="col">Jumlah</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              <th style="padding: 4px 12px;" scope="col">Giro Rp</th>
              <th style="padding: 4px 12px;" scope="col">Costing</th>
              <th style="padding: 4px 12px;" scope="col">Sub Cost</th>


              <th style="padding: 4px 12px;" scope="col">Actions</th>

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
  <button id="buttonAddAddItem" type="button" class="btn btn-chip-biru" onclick="buttonAddAddItem()" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah</button>
</div>


  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4 id="labelAddAddItem">Add Item</h4>
        <h4 id="labelAddEditItem">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row">






          <div class="col-md-2">
            <div class="form-group">
            <label>Devisi</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKodeDevisi" type="text" class="form-control" disabled>

              <button id="buttonAddListDevisi" type="button" onclick="buttonAddListDevisi()" class="btn btn-chip-biru btn-sm btn-icon-search"><i class="bi bi-search"></i></button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input  id="AddAddNamaDevisi" type="text" class="form-control" disabled>

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
            <label>DPH</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-6">
            <div class="input-group form-group">
              <input id="AddAddNoDPH" type="text" class="form-control" value="" disabled>
              <button id="buttonAddListDPH" type="button" onclick="buttonAddListDPH()" class="btn btn-chip-biru btn-sm btn-icon-search"><i class="bi bi-search"></i></button>

            </div>
          </div>





        </div>


        <div class="row" style="margin-top: -10px">






          <div class="col-md-2">
            <div class="form-group">
            <label>Supplier</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKodeCustomer" type="text" class="form-control" value="" disabled>
              <!-- <button id="buttonAddListCustomer" type="button" onclick="buttonAddListCustSupp()" class="btn btn-primary" >+</button> -->

            </div>
          </div>

          <div class="col-md-7">
            <div class="input-group form-group">
              <input id="AddAddNamaCustomer" type="text" class="form-control" value="" disabled>


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
            <label>Valas</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddValas" type="text" class="form-control" value="IDR" disabled>
              <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" >+</button> -->

            </div>
          </div>

          <div class="col-md-1">
            <div class="form-group">
            <label>Kurs</label>
          </div>
          </div>

          <div class="col-md-2">
            <div class="input-group form-group">
              <input id="AddAddKurs" type="number"  value="1.00" class="text-right form-control" disabled>

            </div>
          </div>

        </div>
      </div>

    </div>




    <div class="row" style="margin-top: -10px">

      <div class="col-md-12">

      <div class="row">

      <div class="col-md-6">


        <div class="row">

          <div class="col-md-2">
            <div class="form-group">
            <label>Lawan</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddLawan" type="text" class="form-control" disabled>
              <input id="AddAddKodeLawan" type="hidden" class="form-control" disabled>
              <button id="buttonAddListLawan" type="button" onclick="buttonAddListLawan()" class="btn btn-chip-biru btn-sm btn-icon-search"><i class="bi bi-search"></i></button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKeteranganLawan" type="text" class="form-control" disabled>

            </div>
          </div>

        </div>
      </div>



        <!-- <div class="col-md-3">


    <div class="row">

      <div class="col-md-4">
        <div class="form-group">
        <label>Kode Brg</label>
      </div>
      </div>
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>

        </div>
      </div>

    </div>

  </div> -->

</div>
</div>

</div>


<div class="row" style="margin-top: -10px">
  <div class="col-md-6">


    <div class="row">






      <div class="col-md-2">
        <div class="form-group">
        <label>Jumlah</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddJumlah" type="number" value="0.00" class="text-right form-control" disabled>

        </div>
      </div>

       <div class="col-md-1">
        <div class="form-group">
        <label>Sumber</label>
      </div>
      </div>
      <div class="col-md-3">
        <div class="input-group form-group">
          <select id="AddAddSumber" class="form-control form-select-lg" aria-label=".form-select-lg example" onChange="onChangeTransaksi()">
            <option value=1 selected>[H]Hutang Giro</option>
          </select>


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
        <label>Jml Giro</label>
      </div>
      </div>

      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddJumlahGiro" type="number"  value="0.00" class="text-right form-control" disabled>
          <!-- <button id="buttonAddListLawan" type="button" onclick="buttonAddListLawan()" class="btn btn-primary" >+</button> -->

        </div>
      </div>

      <div class="col-md-2">
        <div class="input-group form-group text-left">
          <button id="buttonFormGiro" type="button" onclick="buttonFormGiro()" class="btn btn-chip-biru" >+ Giro</button>
          <button id="buttonFormGiroBGT" type="button" onclick="buttonFormGiroBGT()" class="btn btn-chip-biru" >+ Giro</button>


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
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddKeterangan" type="text" value="" class="form-control" >

        </div>
      </div>



    </div>
  </div>

</div>

<!-- <div class="row" style="margin-top: -10px">
  <div class="col-md-6">


    <div class="row">






      <div class="col-md-2">
        <div class="form-group">
        <label>Ket. Det</label>
      </div>
      </div>
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddKeteranganDetail" type="text" value="" class="form-control" >

        </div>
      </div>



    </div>
  </div>

</div> -->
<div class="row" style="margin-top: -10px">
  <div class="col-md-6">

<div class="row">






  <div class="col-md-2">
    <div class="form-group">
    <label>Departemen</label>
  </div>
  </div>
  <!-- <div class="col-4 text-right">

    </div> -->
  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddKodeDepartemen" type="text" class="form-control" disabled>
      <button id="buttonAddListDepartemen" type="button" onclick="buttonAddListDepartemen()" class="btn btn-chip-biru btn-sm btn-icon-search"><i class="bi bi-search"></i></button>

    </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddNamaDepartemen" type="text" class="form-control" disabled>

    </div>
  </div>

</div>


<!-- <div class="row" id="rowCustsupp" style="margin-top: -10px">






  <div class="col-md-2">
    <div class="form-group">
    <label>Custsupp</label>
  </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddKodeCustsupp" type="text" class="form-control" disabled>
      <button id="buttonAddListCustsupp" type="button" onclick="buttonAddListCustsupp()" class="btn btn-primary" >+</button>

    </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddNamaCustsupp" type="text" class="form-control" disabled>

    </div>
  </div>

</div> -->


</div>
</div>


</div>










  <!-- <div class="col-6 ">
    <div class="row">



    </div> -->
  <!-- </div> -->




  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-danger" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-chip-biru" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Simpan</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Simpan</button>


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


<!-- </div> -->



    </div>

    <!-- <div class="row "> -->

<!-- </div> -->









  </div>




  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row" style="margin-top: -30px">
      <div class="col-8 text-left">
        <h2 class="showhidepage3 page3detail">Detail Bank</h2>
        <h2 class="showhidepage3 page3otorisasi">Otorisasi Bank</h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>

    <div id= "" class="">



    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">

        <div class="row">
          <div class="col-md-2">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                <label>Transaksi</label>
              </div>
              </div>

              <div class="col-md-7">
                <select id="input_detail_transaksi" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                  <option value='BBG' selected>BBG</option>
                  <option value='BCG' >BCG</option>
                </select>
              </div>

            </div>

            <div class="row" style="margin-top: -10px">



            <div class="col-md-5">
              <div class="form-group">
                <label>Bank</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div>
          </div> -->
            <div class="col-md-7">
              <div class="form-group input-group">
                <input type="hidden" class="form-control" id="input_detail_simbol" placeholder="" disabled>
                <input type="text" class="form-control" id="input_detail_kodeperkiraan" placeholder="" disabled>
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListPerkiraan" onclick="buttonAddListPerkiraan()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>


            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_detail_keteranganperkiraan"  disabled></textarea>
              </div>
            </div>


            </div>

          </div>

          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12" >
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
                </div>
              </div>



                </div>
              </div>

              <div class="col-md-6">
                <div class="row">

                  <div class="col-md-12" >
                    <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tgl</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                    </div>
                  </div>
                </div>
              </div>





                </div>
              </div>
            </div>

            <div class="row" style="margin-top: -10px">
              <div class="col-md-12">
                <div class="row">


              <div class="col-md-2">
                <div class="form-group">
                  <label class="">Terima</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_kepadaterima" placeholder="" disabled>
                </div>
              </div>
            </div>

              </div>

            </div>

            <!-- <div class="row" style="margin-top: -10px" class="" >
              <div class="col-md-12">
                <div class="row">


              <div class="col-md-2">
                <div class="form-group">
                  <label class="">Bon</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_detail_bon" placeholder="" disabled>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label class="">Nilai Bon</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <input type="number" class="form-control text-right " id="input_detail_nilaibon" value="0.00" disabled>
                </div>
              </div>
            </div>

              </div>

            </div> -->

          </div>








        </div>














        </div>



  <div class="container-fluid">
    <hr/>

  </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="data-table"  >
            <thead>
              <tr>
                <th style="padding: 4px 12px;" scope="col">Devisi</th>
                <th style="padding: 4px 12px;" scope="col">Perk.</th>
                <th style="padding: 4px 12px;" scope="col">Ket. Perk</th>
                <th style="padding: 4px 12px;" scope="col">Lawan</th>
                <th style="padding: 4px 12px;" scope="col">Ket. Lawan</th>
                <th style="padding: 4px 12px;" scope="col">Sumber</th>
                <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">Giro Rp</th>
                <th style="padding: 4px 12px;" scope="col">Costing</th>
                <th style="padding: 4px 12px;" scope="col">Sub Cost</th>


              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=8 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>


    <div class="col-md-12 mt-2 text-right">

  </div>

  <div class="col-md-12 mt-2 text-right showhidepage3 page3otorisasi">
  <button id="buttonOtorisasi" type="button" class="btn btn-primary" onclick="submitOtorisasi()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >Otorisasi</button>
</div>



  <!-- <h2 class="showhidepage3 page3otorisasi">Otorisasi Kas</h2> -->








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





  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">

      <div id= "modalAddListValas" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Valas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Valas</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_valas" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Kurs</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_valas" class="text-left" >

                <tr >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


      <div id= "modalAddListCustsupp" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">CustSupp</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>CustSupp</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_custsupp" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Kota</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_custsupp" class="text-left" >

                <tr >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>

      <div id= "modalAddListDPHBBG" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">DPH</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>DPH</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_dphbbg" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">DPP</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                  <th style="padding: 4px 12px;" scope="col">Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">KL</th>
                  <th style="padding: 4px 12px;" scope="col">LB</th>
                  <th style="padding: 4px 12px;" scope="col">Perk</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_dphbbg" class="text-left" >

                <tr class="pick-row">

                  <td>-</td>
                  <td>-</td>
                  <td>-</td><td>-</td>
                  <td>-</td>
                  <td>-</td><td>-</td>
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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


      <div id= "modalAddListDPP" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">DPP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>DPP</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_dpp" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No DPP</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nominal</th>
                  <th style="padding: 4px 12px;" scope="col">K. Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">L. Bayar</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_dpp" class="text-left" >

                <tr >

                  <td>-</td>
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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>



      <div id= "modalAddListDPH" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">DPH</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>DPH</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_dph" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No DPH</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nominal</th>
                  <th style="padding: 4px 12px;" scope="col">K. Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">L. Bayar</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_dph" class="text-left" >

                <tr >

                  <td>-</td>
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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


      <div id= "modalAddListDPHUHT" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">DPH</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>DPH</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_dphuht" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No DPH</th>
                  <th style="padding: 4px 12px;" scope="col">No Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Supp</th>
                  <th style="padding: 4px 12px;" scope="col">Nominal</th>
                  <th style="padding: 4px 12px;" scope="col">K. Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">L. Bayar</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_dphuht" class="text-left" >

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
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>




      <div id= "modalAddListDPHUHTBBM" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Proses - Retur Uang Muka</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >

          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 300px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_dphuhtbbm_custsupp" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">KODE</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_dphuhtbbm_custsupp" class="text-left" >

                <tr >
                  <td>-</td>
                  <td>-</td>
              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            <hr>

            <div class="row" style="margin-top: 20px">
              <div class="col-md-2">
                <div class="form-group">
                  <label>CustSupp</label>
                </div>
              </div>
              <!-- <div class="col-3 text-right">
                <div class="form-group">
              </div>
            </div> -->
            <div class="col-md-2">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_dphuhtbbm_kodecustsupp" placeholder="" disabled>

              </div>
            </div>

              <div class="col-md-4">
                <div class="form-group input-group">

                  <input type="text" class="form-control" id="input_dphuhtbbm_namacustsupp" placeholder="" disabled>
                </div>
              </div>

            </div>



            <div class="row">
              <div class="col-12" style="overflow:auto; ">
              <!-- <div class="container-fluid"> -->


              <table id="tabel_add_list_dphuhtbbm" class="data-table" style="overflow:auto; " >
                <thead>
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                    <th style="padding: 4px 12px;" scope="col">NoRetur</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">PO</th>
                    <th style="padding: 4px 12px;" scope="col">Valas</th>

                    <th style="padding: 4px 12px;" scope="col">Kurs</th>
                    <th style="padding: 4px 12px;" scope="col">DPP</th>

                    <th style="padding: 4px 12px;" scope="col">PPN</th>

                    <th style="padding: 4px 12px;" scope="col">Subtotal</th>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>

                  </tr>
                </thead>


                <tbody id="tabel_data_add_list_dphuhtbbm" class="text-left" >

                  <tr>
                    <td colspan=10 class="text-center">Belum ada data</td>
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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>



      <div id= "modalAddListDevisi" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Devisi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Devisi</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_devisi" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_devisi" class="text-left" >

                <tr >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>




      <div id= "modalAddListPerkiraan" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Perkiraan</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_perkiraan" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Simbol</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_perkiraan" class="text-left" >

                <tr >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>

      <div id= "modalAddListDepartemen" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Departemen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Departemen</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_departemen" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_departemen" class="text-left" >

                <tr >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>

      <div id= "modalAddListLawan" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Lawan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Lawan</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_lawan" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Simbol</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_lawan" class="text-left" >

                <tr >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


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
          <div class="row">
            <div class="col-12">
              <h3>Customer</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_customer" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Alamat</th>
                  <th style="padding: 4px 12px;" scope="col">Kota</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_customer" class="text-left" >

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


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>



      <div id= "modalAddListInvoice" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Invoice</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_invoice" class="data-table" style="overflow:auto; " >
              <thead>
                <tr>
                  <th class="text-center" style="padding: 4px 12px;" scope="col">v</th>
                  <th style="padding: 4px 12px;" scope="col">No Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                  <th style="padding: 4px 12px;" scope="col">Jatuh Tempo</th>
                  <th style="padding: 4px 12px;" scope="col">Valas</th>
                  <th style="padding: 4px 12px;" scope="col">Nilai Kredit Note</th>
                  <th style="padding: 4px 12px;" scope="col">Kurs</th>
                  <th style="padding: 4px 12px;" scope="col">Nilai KN (Rp)</th>
                  <th style="padding: 4px 12px;" scope="col">Piutang (Valas)</th>
                  <th style="padding: 4px 12px;" scope="col">Piutang (Rp)</th>
                  <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_invoice" class="text-left" >

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="buttonAddPickInvoice()" >Submit</button>
      </div>
      </div>










      </div>







    </div>
  </div>


  <div class="modal fade" id="formGiro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="">
      <div id="" class="modal-content ">

        <div id= "" class="">
        <div class="modal-header">


            <h5 class="modal-title" id="">Giro</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>


        <div id="" class="">


        <div class="modal-body">

          <div class="container-fluid" >
            <div class="row">
                <div class="col-md-12" style="overflow-x: auto; padding:0; margin:0;">
                  <table id="giroModalTable" class="data-table"  >
                    <thead>
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Bank</th>
                        <th style="padding: 4px 12px;" scope="col">No Giro</th>
                        <th style="padding: 4px 12px;" scope="col">Jth Tempo</th>
                        <th style="padding: 4px 12px;" scope="col">Debet</th>
                        <th style="padding: 4px 12px;" scope="col">Kredit</th>
                        <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                        <th style="padding: 4px 12px;" scope="col">Valas</th>
                        <th style="padding: 4px 12px;" scope="col">Kurs</th>
                        <th style="padding: 4px 12px;" scope="col">Debet Rp</th>
                        <th style="padding: 4px 12px;" scope="col">Kredit Rp</th>
                        <th style="padding: 4px 12px;" scope="col">Jumlah Rp</th>
                        <th style="padding: 4px 12px;" scope="col">Actions</th>

                      </tr>
                    </thead>


                    <tbody id="giroModalTableData" class="" >
                      <tr >


                          <td colspan=12 class="text-center">Belum ada data</td>
                    </tr>

                    </tbody>


                  </table>
                </div>

                <div class="col-xl-12 mt-2 text-right">
                <button id="buttonAddGiroAdd" type="button" class="btn btn-chip-biru" onclick="buttonAddGiroAdd()" style="height: 30px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;" >+ Tambah</button>
              </div>

            </div>









            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

              </div>


              <div id="formGiroAdd" class="container-fluid showhideitemgiro" style="">
                <div class="row">

                <div class="col-12">


                <hr/>
                <div class="row">
                  <div class="col-md-12">
                    <h4 id="labelAddGiro" class="labelGiro">Add Giro</h4>
                    <h4 id="labelEditGiro" class="labelGiro">Edit Giro</h4>
                  </div>
                </div>

                <div class="row" >
                  <div class="col-xl-2">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                        <label>Bank</label>
                      </div>
                      </div>



                    </div>

                    <div class="row" style="margin-top: -15px">
                      <div class="col-md-12">
                        <div class="input-group form-group">
                          <input id="input_giro_bank" type="text" class="form-control" disabled>
                          <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListPerkiraanBank" onclick="buttonAddListPerkiraanBank()"><i class="bi bi-search"></i></button>

                        </div>
                      </div>
                    </div>

                  </div>



                  <div class="col-md-2">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                        <label>No Giro</label>
                      </div>
                      </div>







                    </div>

                    <div class="row" style="margin-top: -15px">
                      <div class="col-md-12">
                        <div class="input-group form-group">
                          <input id="input_giro_nogiro" type="text" class="form-control" >

                        </div>
                      </div>
                    </div>

                  </div>


                  <div class="col-md-2">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                        <label>Tanggal</label>
                      </div>
                      </div>

                    </div>

                    <div class="row" style="margin-top: -15px">
                      <div class="col-md-12">
                        <div class="input-group form-group">
                          <input id="input_giro_tanggal" type="date" class="form-control" >

                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-2">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                        <label>Valas</label>
                      </div>
                      </div>







                    </div>
                    <div class="row" style="margin-top: -15px">
                      <div class="col-md-12">
                        <div class="input-group form-group">
                          <input id="input_giro_valas" type="text" class="form-control" disabled>

                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-2">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                        <label>Kurs</label>
                      </div>
                      </div>







                    </div>

                    <div class="row" style="margin-top: -15px">
                      <div class="col-md-12">
                        <div class="input-group form-group">
                          <input id="input_giro_kurs" type="number" class="form-control text-right" disabled>

                        </div>
                      </div>
                    </div>

                  </div>


                  <div class="col-md-2">
                    <div class="row">


                      <div class="col-md-12">
                        <div class="form-group">
                        <label>Nilai Giro</label>
                      </div>
                      </div>







                    </div>

                    <div class="row" style="margin-top: -15px">
                      <div class="col-md-12">
                        <div class="input-group form-group">
                          <input id="input_giro_nilaigiro" type="number" class="form-control text-right" disabled>

                        </div>
                      </div>
                    </div>

                  </div>

                </div>

                <!-- <div class="row" style="margin-top: -10px">
                  <div class="col-xl-4">
                    <div class="row">
                      <div class="col-xl-12">
                        <div class="form-group">
                          <label>Keterangan</label>
                        </div>

                      </div>

                    </div>
                    <div class="row" style="margin-top: -15px">
                      <div class="col-xl-12">


                      <div class="input-group form-group">
                        <input id="input_giro_keterangan" type="text" class="form-control" >

                      </div>
                      </div>
                    </div>

                  </div>

                </div> -->














            </div></div>




              <div class="row mt-2" style="margin-top: 0">
                <div class="col-md-12 text-right mt-4">
                  <button type="button" class="btn btn-secondary" onclick="buttonGiroBatal()" style="height: 30px;
                  border-radius: 20px;
                  font-size: 0.75rem;
                  font-weight: 600;
                  text-transform: uppercase;">Batal</button>

                  <button id="buttono" type="button" onclick="submitAddGiro()" class="btn btn-primary" style="height: 30px;
                  border-radius: 20px;
                  font-size: 0.75rem;
                  font-weight: 600;
                  text-transform: uppercase;">Submit Add</button>

                  <button id="buttonSubmitEditGiro" type="button" onclick="submitEditGiro()" class="btn btn-primary" style="height: 30px;
                  border-radius: 20px;
                  font-size: 0.75rem;
                  font-weight: 600;
                  text-transform: uppercase;">Submit Edit</button>

              </div>

            </div>

              </div>




          </div>





        </div>


        <!-- <div class="modal-footer"> -->
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          <button type="button" id="buttonSubmitAdd" class="btn btn-primary" onclick="submitAdd()">Submit</button>
          <button type="button" id="buttonSubmitEdit" class="btn btn-primary" onclick="submitEdit()">SubmitE</button> -->
        <!-- </div> -->
        </div>


        </div>

      </div>
    </div>



    <div class="modal fade" id="formGiroBGT" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="">
        <div id="" class="modal-content ">

          <div id= "" class="">
          <div class="modal-header">


              <h5 class="modal-title" id="">Giro</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div id="" class="">


          <div class="modal-body">

            <div class="container-fluid" >
              <div class="row">
                  <div class="col-md-12" style="overflow-x: auto; padding:0; margin:0;">
                    <table id="giroBGTModalTable" class="data-table"  >
                      <thead>
                        <tr>
                          <th style="padding: 4px 12px;" scope="col">Bank</th>
                          <th style="padding: 4px 12px;" scope="col">No Giro</th>
                          <th style="padding: 4px 12px;" scope="col">Jth Tempo</th>
                          <th style="padding: 4px 12px;" scope="col">Debet</th>
                          <th style="padding: 4px 12px;" scope="col">Kredit</th>
                          <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                          <th style="padding: 4px 12px;" scope="col">Valas</th>
                          <th style="padding: 4px 12px;" scope="col">Kurs</th>
                          <th style="padding: 4px 12px;" scope="col">Debet Rp</th>
                          <th style="padding: 4px 12px;" scope="col">Kredit Rp</th>
                          <th style="padding: 4px 12px;" scope="col">Jumlah Rp</th>
                          <th style="padding: 4px 12px;" scope="col">Actions</th>

                        </tr>
                      </thead>


                      <tbody id="giroBGTModalTableData" class="" >
                        <tr >


                            <td colspan=12 class="text-center">Belum ada data</td>
                      </tr>

                      </tbody>


                    </table>
                  </div>

                  <div class="col-xl-12 mt-2 text-right">
                  <button id="buttonAddGiroBGTAdd" type="button" class="btn btn-chip-biru" onclick="buttonAddGiroBGTAdd()" style="height: 30px;
                  border-radius: 20px;
                  font-size: 0.75rem;
                  font-weight: 600;
                  text-transform: uppercase;" >+ Tambah</button>
                </div>

              </div>









              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

                </div>


                <div id="formGiroBGTAdd" class="container-fluid showhideitemgiro" style="">
                  <div class="row">

                  <div class="col-12">


                  <hr/>
                  <div class="row">
                    <div class="col-md-12">
                      <h4 id="" class="">Add Giro</h4>
                    </div>
                  </div>

                  <div class="row" >
                    <div class="col-xl-2">
                      <div class="row">


                        <div class="col-md-12">
                          <div class="form-group">
                          <label>Bank</label>
                        </div>
                        </div>



                      </div>

                      <div class="row" style="margin-top: -15px">
                        <div class="col-md-12">
                          <div class="input-group form-group">
                            <input id="input_girobgt_bank" type="text" class="form-control" disabled>
                            <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListPerkiraanBankKoreksi" onclick="buttonAddListPerkiraanBankKoreksi()"><i class="bi bi-search"></i></button>
                          </div>
                        </div>
                      </div>

                    </div>



                    <div class="col-md-2">
                      <div class="row">


                        <div class="col-md-12">
                          <div class="form-group">
                          <label>No Giro</label>
                        </div>
                        </div>







                      </div>

                      <div class="row" style="margin-top: -15px">
                        <div class="col-md-12">
                          <div class="input-group form-group">
                            <input id="input_girobgt_nogiro" type="text" class="form-control" >

                          </div>
                        </div>
                      </div>

                    </div>


                    <div class="col-md-2">
                      <div class="row">


                        <div class="col-md-12">
                          <div class="form-group">
                          <label>Tanggal</label>
                        </div>
                        </div>

                      </div>

                      <div class="row" style="margin-top: -15px">
                        <div class="col-md-12">
                          <div class="input-group form-group">
                            <input id="input_girobgt_tanggal" type="date" class="form-control" >

                          </div>
                        </div>
                      </div>

                    </div>

                    <div class="col-md-2">
                      <div class="row">


                        <div class="col-md-12">
                          <div class="form-group">
                          <label>Valas</label>
                        </div>
                        </div>







                      </div>
                      <div class="row" style="margin-top: -15px">
                        <div class="col-md-12">
                          <div class="input-group form-group">
                            <input id="input_girobgt_valas" type="text" class="form-control" disabled>

                          </div>
                        </div>
                      </div>

                    </div>

                    <div class="col-md-2">
                      <div class="row">


                        <div class="col-md-12">
                          <div class="form-group">
                          <label>Kurs</label>
                        </div>
                        </div>







                      </div>

                      <div class="row" style="margin-top: -15px">
                        <div class="col-md-12">
                          <div class="input-group form-group">
                            <input id="input_girobgt_kurs" type="number" class="form-control text-right" disabled>

                          </div>
                        </div>
                      </div>

                    </div>


                    <div class="col-md-2">
                      <div class="row">


                        <div class="col-md-12">
                          <div class="form-group">
                          <label>Nilai Giro</label>
                        </div>
                        </div>







                      </div>

                      <div class="row" style="margin-top: -15px">
                        <div class="col-md-12">
                          <div class="input-group form-group">
                            <input id="input_girobgt_nilaigiro" type="number" class="form-control text-right" disabled>

                          </div>
                        </div>
                      </div>

                    </div>

                  </div>















              </div></div>




                <div class="row mt-2" style="margin-top: 0">
                  <div class="col-md-12 text-right mt-4">
                    <button type="button" class="btn btn-secondary" onclick="buttonGiroBatalBGTt()" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">Batal</button>

                    <button id="" type="button" onclick="submitAddGiroBGT()" class="btn btn-primary" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">Submit Add</button>



                </div>

              </div>

                </div>




            </div>





          </div>


          <!-- <div class="modal-footer"> -->
            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" id="buttonSubmitAdd" class="btn btn-primary" onclick="submitAdd()">Submit</button>
            <button type="button" id="buttonSubmitEdit" class="btn btn-primary" onclick="submitEdit()">SubmitE</button> -->
          <!-- </div> -->
          </div>


          </div>

        </div>
      </div>

    <div class="modal fade" id="modalListBGCEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
        <div id="" class="modal-content ">

          <div id= "" class="">
          <div class="modal-header">


              <h5 class="modal-title" id="">Pencairan Giro</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div id="" class="">
          <div class="modal-body">

            <div class="container-fluid" >
              <div class="row">
                <div class="col-12">
                  <h3>Pencairan Giro Koreksi</h3>
                </div>


              </div>



              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto;  max-height: 400px">
                <!-- <div class="container-fluid"> -->


                <table id="tabel_add_list_pencairangiroedit" class="data-table" style="overflow:auto; " >
                  <thead style="position: sticky;
                top: 0;
                z-index: 1;">
                    <tr>


                      <th style="padding: 4px 12px;" scope="col">Bank</th>
                      <th style="padding: 4px 12px;" scope="col">No Giro</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>

                      <th style="padding: 4px 12px;" scope="col">Debet</th>
                      <th style="padding: 4px 12px;" scope="col">Kredit</th>
                      <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                      <th style="padding: 4px 12px;" scope="col">Valas</th>
                      <th style="padding: 4px 12px;" scope="col">Kurs</th>
                      <th style="padding: 4px 12px;" scope="col">DebetRp</th>
                      <th style="padding: 4px 12px;" scope="col">KreditRp</th>
                      <th style="padding: 4px 12px;" scope="col">JumlahRp</th>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>

                    </tr>
                  </thead>


                  <tbody id="tabel_data_add_list_pencairangiroedit" class="text-left" >


                  </tbody>


                </table>
              <!-- </div> -->
                <!-- <button onclick="buttonSubKategori()">tes</button> -->
              </div>
                </div>
                </div>

            </div>

          </div>


          <div class="modal-footer">

            <button type="button" class="btn btn-chip-biru" onclick="buttonAddGiroKoreksi()">+ Tambah</button>
          </div>
          </div>




          </div>







        </div>
      </div>


    <div class="modal fade" id="modalPerkiraanBGC" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
        <div id="" class="modal-content ">

          <div id= "" class="">
          <div class="modal-header">


              <h5 class="modal-title" id="">Perkiraan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div id="" class="">
          <div class="modal-body">

            <div class="container-fluid" >
              <div class="row">
                <div class="col-12">
                  <h3>Perkiraan</h3>
                </div>


              </div>



              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <div class="row">
                <div class="col-12" style="overflow:auto;  max-height: 400px">
                <!-- <div class="container-fluid"> -->


                <table id="tabel_add_list_perkiraanbgc" class="data-table" style="overflow:auto; " >
                  <thead style="position: sticky;
                top: 0;
                z-index: 1;">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                      <th style="padding: 4px 12px;" scope="col">Nama</th>
                      <th style="padding: 4px 12px;" scope="col">Simbol</th>

                    </tr>
                  </thead>


                  <tbody id="tabel_data_add_list_perkiraan" class="text-left" >

                    @for ($i = 0; $i < count($tempListPerkiraanBGC); $i++)
                    <tr class="pick-row" onclick="buttonAddPickPerkiraanBGC('{{ $tempListPerkiraanBGC[$i]->Perkiraan }}' , '{{ $tempListPerkiraanBGC[$i]->Keterangan }}')">
                      <td>{{ $tempListPerkiraanBGC[$i]->Perkiraan }}</td>
                      <td>{{ $tempListPerkiraanBGC[$i]->Keterangan }}</td>
                      <td>{{ $tempListPerkiraanBGC[$i]->Simbol }}</td>

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


          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
          </div>
          </div>




          </div>







        </div>
      </div>


      <div class="modal fade" id="modalListBGC" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
          <div id="" class="modal-content ">

            <div id= "" class="">
            <div class="modal-header">


                <h5 class="modal-title" id="">Pencairan Giro</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>


            <div id="" class="">
            <div class="modal-body">

              <div class="container-fluid" >
                <div class="row">
                  <div class="col-12">
                    <h3>Pencairan Giro</h3>
                  </div>


                </div>



                <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                <div class="row">
                  <div class="col-12" style="overflow:auto;  max-height: 400px">
                  <!-- <div class="container-fluid"> -->


                  <table id="tabel_add_list_pencairangiro" class="data-table" style="overflow:auto; " >
                    <thead style="position: sticky;
                  top: 0;
                  z-index: 1;">
                      <tr>

                          <th style="padding: 4px 12px;" scope="col">Cair</th>
                        <th style="padding: 4px 12px;" scope="col">Bank</th>
                        <th style="padding: 4px 12px;" scope="col">No Giro</th>
                        <th style="padding: 4px 12px;" scope="col">Tgl Giro</th>
                        <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                        <th style="padding: 4px 12px;" scope="col">Valas</th>
                        <th style="padding: 4px 12px;" scope="col">Kurs</th>
                        <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                      </tr>
                    </thead>


                    <tbody id="tabel_data_add_list_pencairangiro" class="text-left" >


                    </tbody>


                  </table>
                <!-- </div> -->
                  <!-- <button onclick="buttonSubKategori()">tes</button> -->
                </div>
                  </div>
                  </div>

              </div>

            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
              <button type="button" class="btn btn-primary" onclick="submitAddBGC()">Submit</button>
            </div>
            </div>




            </div>







          </div>
        </div>


        <div class="modal fade" id="modalListBGCAddKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
            <div id="" class="modal-content ">

              <div id= "" class="">
              <div class="modal-header">


                  <h5 class="modal-title" id="">Pencairan Giro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>


              <div id="" class="">
              <div class="modal-body">

                <div class="container-fluid" >
                  <div class="row">
                    <div class="col-12">
                      <h3>Pencairan Giro</h3>
                    </div>


                  </div>



                  <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                  <div class="row">
                    <div class="col-12" style="overflow:auto;  max-height: 400px">
                    <!-- <div class="container-fluid"> -->


                    <table id="tabel_add_list_pencairangiroaddkoreksi" class="data-table" style="overflow:auto; " >
                      <thead style="position: sticky;
                    top: 0;
                    z-index: 1;">
                        <tr>

                          <th style="padding: 4px 12px;" scope="col">Bank</th>
                          <th style="padding: 4px 12px;" scope="col">No Giro</th>
                          <th style="padding: 4px 12px;" scope="col">Tgl Giro</th>
                          <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                          <th style="padding: 4px 12px;" scope="col">Valas</th>
                          <th style="padding: 4px 12px;" scope="col">Kurs</th>
                          <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data_add_list_pencairangiroaddkoreksi" class="text-left" >


                      </tbody>


                    </table>
                  <!-- </div> -->
                    <!-- <button onclick="buttonSubKategori()">tes</button> -->
                  </div>
                    </div>
                    </div>

                </div>

              </div>


              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitAddBGC()">Submit</button>
              </div>
              </div>




              </div>







            </div>
          </div>

          <div class="modal fade" id="modalBank" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
              <div id="" class="modal-content ">

                <div id= "" class="">
                <div class="modal-header">


                    <h5 class="modal-title" id="">Perkiraan Bank</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>


                <div id="" class="">
                <div class="modal-body">

                  <div class="container-fluid" >
                    <div class="row">
                      <div class="col-12">
                        <h3>Perkiraan Bank</h3>
                      </div>


                    </div>



                    <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
                    <div class="row">
                      <div class="col-12" style="overflow:auto;  max-height: 400px">
                      <!-- <div class="container-fluid"> -->


                      <table id="tabel_add_list_perkiraanbank" class="data-table" style="overflow:auto; " >
                        <thead style="position: sticky;
                      top: 0;
                      z-index: 1;">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                            <th style="padding: 4px 12px;" scope="col">Nama</th>
                            <th style="padding: 4px 12px;" scope="col">Simbol</th>

                          </tr>
                        </thead>


                        <tbody id="tabel_data_add_list_perkiraanbank" class="text-left" >

                          @for ($i = 0; $i < count($tempListPerkiraanBGC); $i++)
                          <tr class="pick-row" onclick="buttonAddPickPerkiraanBank('{{ $tempListPerkiraanBGC[$i]->Perkiraan }}' , '{{ $tempListPerkiraanBGC[$i]->Keterangan }}')">
                            <td>{{ $tempListPerkiraanBGC[$i]->Perkiraan }}</td>
                            <td>{{ $tempListPerkiraanBGC[$i]->Keterangan }}</td>
                            <td>{{ $tempListPerkiraanBGC[$i]->Simbol }}</td>

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


                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
                </div>
                </div>




                </div>







              </div>
            </div>

<!-- End modal add-->








@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">
let listInvoice = []
// let tempNoBukti = ''
let listData = []

let xxtemp = 0

let listPencairanGiro = []
let listPencairanGiroKoreksi = []
let listPencairanGiroBGT = []
let idTujuanBank = ''
let listPerkiraan = []
let listLawan = []
let listValas = []
let listDepartemen = []
let listDevisi = []
let tipeformgiro = 'add'
let listDPH = []
let listDPP = []
let tipeform = 'add'

let tipeformbgc = 'add'

let perkiraanBGC = ''

let listUMB = []

let tempDPPDPH = {}
let listGiroAdd = []

let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
// let tipeform = ''

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola invoicejasa.blade.php/giroditerima.blade.php (tabel tunggal,
 * tanpa dimensi urut berganda karena halaman ini cuma punya satu tab "Giro").
 */
let giroCart = []
const GIRO_HREF = 'girodibuka'
const GIRO_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const GIRO_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function giroPickCI (row, key) {
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function giroDefaultCart () {
  return [
    ['NoBukti',     'No. Bukti', 1, 'varchar', 0, 0],
    ['Tanggal',     'Tanggal',   1, 'date',    0, 0],
    ['TipeTransHd', 'Trans',     1, 'varchar', 0, 0],
    ['Perkiraan',   'Perk.',     1, 'varchar', 0, 0],
    ['Note',        'Ket.',      1, 'varchar', 0, 0],
    ['TotalRp',     'Jumlah Rp', 1, 'float',   0, 2],
  ]
}

function giroBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      GIRO_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

window.g_href = GIRO_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = giroCart || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(GIRO_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: GIRO_HREF, urut: 1
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: GIRO_HREF, urut: 1, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        giroCart = giroBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        giroCart = giroDefaultCart()
        window.gcart_header = giroCart
        window.doSimpanHeader()
      }
      window.gcart_header = giroCart
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      giroCart = giroDefaultCart()
      window.gcart_header = giroCart
    }
  })
}

let giroRtSudahInit = false
function giroInitReportTableSekali () {
  if (giroRtSudahInit || typeof ReportTable === 'undefined') { return }
  giroRtSudahInit = true

  ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel })

  let giroGuardUlangKlik = false;
  ['#tabel'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (giroGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      giroGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      giroGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderGiro (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function giroValueCell (row, col) {
  let raw = giroPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') {
    if (!raw) { return '<td></td>'; }
    return '<td>' + formatDate(raw, '/') + '</td>';
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + formatAngka(n.toFixed(dp)) + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function tabelActionsCell (row) {
  let nobukti = giroPickCI(row, 'NoBukti');
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
  html += '<button class="btn btn-primary btn-sm" type="button" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (giroCart.length ? giroCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += giroValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderGiro('#tabel', cols);
}

let lastTabelRows = []
let giroPanjangHalaman = 10

function giroIkatSearch () {
  let input = document.getElementById('giroSearch1')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().search(nilai).draw() }
    }, 400)
  })
}

function giroIkatPanjangHalaman () {
  let sel = document.getElementById('giroLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(giroPanjangHalaman)
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    giroPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().page.len(giroPanjangHalaman).draw() }
  })
}

const GIRO_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

let giroFilterTrans = ''
function buttonFilterGiro () {
  giroFilterTrans = $('#input_filtertransgiro').val()
  reinitTabel()
}
function giroFilterRows (rows) {
  if (!giroFilterTrans) { return rows }
  return (rows || []).filter(function (r) { return giroPickCI(r, 'TipeTransHd') === giroFilterTrans })
}
function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(giroFilterRows(lastTabelRows));
    $('#tabel').DataTable({ dom: GIRO_DOM_STRING, lengthChange: false, pageLength: giroPanjangHalaman, paging: true });
    giroIkatSearch(); giroIkatPanjangHalaman();
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function onChangePeriodeGiro () {
  let awal = $("#input_tanggalawal").val()
  let akhir = $("#input_tanggalakhir").val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  loadAll()
}

$(document).ready(function(){
      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      giroInitReportTableSekali();

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
        // const urlString = window.location.href
        // console.log(urlString)
        // const url = new URL(urlString);
        // console.log(url)
        // const searchParams = new URLSearchParams(url.search);
        // console.log(searchParams)
        //
        // const query = searchParams.get('nobukti');
        // console.log(query)


        // var xyz = jQuery.url.param("nobukti");
        // console.log(xyz)


  //   formAddListItem
});

function tesDetailGiro () {
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('girodibukalistgiro') !!}",
    type: "post",
    async: false,
    data: {
      _token,

    },
    success: function(res) {

      console.log(res)
    }})
}

function buttonAddListCustomer () {
  $(".showhideitemgiro").hide()
  $("#formGiro").modal("toggle")


}



function buttonFormGiroBGT () {
  // tipeformgiro = tipe
  // if (tipeformgiro == 'add') {
    // listGiroAdd = []
    // $(".showhideitemgiro").hide()
    $(".showhideitemgiro").hide()

    let xvalas = $("#AddAddValas").val()
    let xkurs = $("#AddAddKurs").val()
    let xnilaigiro = xxtemp
    console.log(xvalas, xkurs)
    document.getElementById('input_girobgt_valas').value = xvalas
    document.getElementById('input_girobgt_kurs').value = xkurs
    document.getElementById('input_girobgt_nilaigiro').value = xnilaigiro

    if (xvalas == 'IDR') {
      document.getElementById('input_girobgt_kurs').disabled = true

    }
    // refreshDataTableGiroKoreksi
    refreshDataTableGiroBGT()
    $("#formGiroBGT").modal('toggle')
  // } else {
  //
  // }

}


function refreshDataTableGiroBGT (nobukti = '' , urut = 0) {
  console.log('refreshDataTableGiroKoreksi')
  let _token = $('#_token').val()
  $.ajax({
    url: "{!! url('girodibukalistpencairangirobgt') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: tempBarangAddEdit.NoBukti,
      urut: tempBarangAddEdit.Urut

    },
    success: function(res) {
      console.log(res)
      // $('.showhideitemKLedit').hide()

      listPencairanGiroBGT = res
      let rowTable = ''

      if (listPencairanGiroBGT.length) {
        console.log('r1')
        listPencairanGiroBGT.forEach((item, i) => {
          rowTable += `
          <tr>

              <td>${item.Bank}</td>
              <td>${item.NoGiro}</td>

              <td>${formatDate(item.TglGiro)}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>

              <td class="text-right">${formatAngka(parseFloat(Number(item.Debet) -Number(item.Kredit)).toFixed(2))}</td>
              <td>${item.Kodevls}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.DebetRp).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.KreditRp).toFixed(2))}</td>

              <td class="text-right">${formatAngka(parseFloat(Number(item.DebetRp) -Number(item.KreditRp)).toFixed(2))}</td>

              <td><div class="form-check text-center">
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteGiroBGT(${i})"><i class="bi bi-trash"></i></button>
              </div></td>
          </tr>


          `
        });

        document.getElementById("giroBGTModalTableData").innerHTML = rowTable
        // $('#modalPerkiraanBGC').modal("toggle")
        // $('#modalListBGCEdit').modal("toggle")
      } else {
        console.log('r2')
        document.getElementById("giroBGTModalTableData").innerHTML =

        `<tr>
          <td colspan=12 class="text-center">Belum ada data</td>
        </tr>`
        // $('#modalListBGCEdit').modal("toggle")
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}


function submitAddGiroBGT () {
  console.log('submitAddGiroBGT')

  // tempBarangAddEdit
  // listPencairanGiro


  let xdebet = 0
  let xdebetrp = 0
  listPencairanGiroBGT.forEach((item, i) => {

    // if (document.getElementById(`giroChecklist${i}`).checked) {
      // tempDataPencairanGiro.push(item)
      xdebet += Number(item.Debet)
      xdebetrp += Number(item.DebetRp)
    // }

  });

  let _token = $("#_token").val()
  let nogiro = $("#input_girobgt_nogiro").val()
  let bank = $("#input_girobgt_bank").val()
  let tanggal = $("#input_girobgt_tanggal").val()
  let valas = $("#input_girobgt_valas").val()
  let kurs = $("#input_girobgt_kurs").val()
  let nilaigiro = $("#input_girobgt_nilaigiro").val()

  let girocheck = 0
  $.ajax({
    url: "{!! url('girodibukacekgiroexist') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nogiro,
      bank
    },
    success: function(res) {
      console.log(res)



      if (!res.length) {

        girocheck = 1
      } else {

      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

  if (girocheck == 0) {
    alertify.warning("Bank dan giro sudah terdaftar")
    return
  }

  if ( !nogiro || !bank || Number(kurs) <= 0 || Number(nilaigiro) <= 0) {
    alertify.warning("Data tidak lengkap")
    return
  }


  xdebet += Number(nilaigiro)
  xdebetrp += Number(nilaigiro) * Number(kurs)
  let kodeperkiraan = $("#input_add_kodeperkiraan").val()

  // let tanggal = tempBarangAddEdit.Tanggal
  let nobukti = tempBarangAddEdit.NoBukti
  let urutbuktibuka = tempBarangAddEdit.Urut
  let keterangancair = tempBarangAddEdit.Keterangan
  let kepadaterima = tempBarangAddEdit.Note
  // let nogiro = listPencairanGiroBGT[index].Nogiro
  // let bank = listPencairanGiroBGT[index].Bank
  // let kredit = listPencairanGiroBGT[index].jumlah
  // let kreditrp = Number(listPencairanGiroBGT[index].jumlah) * Number(listPencairanGiroBGT[index].Kurs)
  // let _token = $("#_token").val()

  // console.log(nobukti, urutbukticair, xdebet, xdebetrp)
  $.ajax({
    url: "{!! url('girodibukaspaddgirobgt') !!}",
    type: "post",
    async: false,
    data: {
      choice: 'I',
      _token,
      tanggal,
      nobukti,
      keterangancair,
      nogiro,
      bank,
      xdebet,
      xdebetrp,
      urutbuktibuka,
      nilaigiro,
      kepadaterima,
      kodeperkiraan,
      valas,
      kurs

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {
        document.getElementById("AddAddJumlah").value = parseFloat(xdebetrp).toFixed(2)
        document.getElementById("AddAddJumlahGiro").value = parseFloat(xdebetrp).toFixed(2)

        alertify.success("Berhasil menambah giro")
        refreshDataTableGiroBGT(nobukti)
        refreshDataTable(nobukti)
        $('#formGiroBGTAdd').hide()
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })


}



function buttonDeleteGiroBGT (index) {
  console.log('submitAddGiroBGT')

  // tempBarangAddEdit
  // listPencairanGiro


  let xdebet = 0
  let xdebetrp = 0
  listPencairanGiroBGT.forEach((item, i) => {

    // if (document.getElementById(`giroChecklist${i}`).checked) {
      // tempDataPencairanGiro.push(item)
      xdebet += Number(item.Debet)
      xdebetrp += Number(item.DebetRp)
    // }

  });

  let _token = $("#_token").val()
  let nogiro = listPencairanGiroBGT[index].NoGiro
  let bank = listPencairanGiroBGT[index].Bank
  let tanggal = ''
  let valas = ''
  let kurs = 0
  let nilaigiro = ''



  xdebet -= Number(listPencairanGiroBGT[index].Debet)
  xdebetrp -= Number(listPencairanGiroBGT[index].DebetRp)

  let kodeperkiraan = $("#input_add_kodeperkiraan").val()

  // let tanggal = tempBarangAddEdit.Tanggal
  let nobukti = tempBarangAddEdit.NoBukti
  let urutbuktibuka = tempBarangAddEdit.Urut
  let keterangancair = tempBarangAddEdit.Keterangan
  let kepadaterima = tempBarangAddEdit.Note
  // let nogiro = listPencairanGiroBGT[index].Nogiro
  // let bank = listPencairanGiroBGT[index].Bank
  // let kredit = listPencairanGiroBGT[index].jumlah
  // let kreditrp = Number(listPencairanGiroBGT[index].jumlah) * Number(listPencairanGiroBGT[index].Kurs)
  // let _token = $("#_token").val()

  // console.log(nobukti, urutbukticair, xdebet, xdebetrp)
  $.ajax({
    url: "{!! url('girodibukaspaddgirobgt') !!}",
    type: "post",
    async: false,
    data: {
      choice: 'D',
      _token,
      tanggal,
      nobukti,
      keterangancair,
      nogiro,
      bank,
      xdebet,
      xdebetrp,
      urutbuktibuka,
      nilaigiro,
      kepadaterima,
      kodeperkiraan,
      valas,
      kurs

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {
        document.getElementById("AddAddJumlah").value = parseFloat(xdebetrp).toFixed(2)
        document.getElementById("AddAddJumlahGiro").value = parseFloat(xdebetrp).toFixed(2)

        alertify.success("Berhasil menambah giro")
        refreshDataTableGiroBGT(nobukti)
        refreshDataTable(nobukti)
        $('#formGiroBGTAdd').hide()
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })


}

function buttonFormGiro () {
  // tipeformgiro = tipe

  let xnodph = $("#AddAddNoDPH").val()

  if (!xnodph) {

    alertify.warning("Pilih dph terlebih dahulu")
    return
  }

  if (tipeformgiro == 'add') {
    // listGiroAdd = []
    // $(".showhideitemgiro").hide()
    $(".showhideitemgiro").hide()

    let xvalas = $("#AddAddValas").val()
    let xkurs = $("#AddAddKurs").val()
    let xnilaigiro = $("#AddAddJumlah").val()
    let xjumlahgiro = $("#AddAddJumlahGiro").val()
    console.log(xvalas, xkurs)
    document.getElementById('input_giro_valas').value = xvalas
    document.getElementById('input_giro_kurs').value = xkurs
    document.getElementById('input_giro_nilaigiro').value = xnilaigiro
    if (xvalas == 'IDR') {
      document.getElementById('input_giro_kurs').disabled = true

    }
    refreshDataTableGiro()
    $("#formGiro").modal('toggle')

    document.getElementById('AddAddJumlah').value = xnilaigiro
    document.getElementById('AddAddJumlahGiro').value = xjumlahgiro

  } else {

  }

}

function lockFormGiro ( value = true) {
  // document.getElementById("input_giro_nogiro").disabled = value
  // document.getElementById("input_giro_bank").disabled = value
  // document.getElementById("input_giro_kurs").disabled = value
}

function lockFormGiroBGT ( value = true) {
  // document.getElementById("input_girobgt_nogiro").disabled = value
  // document.getElementById("input_girobgt_bank").disabled = value
  // document.getElementById("input_girobgt_kurs").disabled = value
}

      function buttonGiroBatal () {

        $("#formGiroAdd").hide()
      }
      function buttonGiroBatalBGT () {

        $("#formGiroBGTAdd").hide()
      }
      function buttonAddGiroAdd () {
        console.log("buttonAddGiroAdd")
        if (listGiroAdd.length) {
          alertify.warning("Sudah ada giro")

          return
        }
        document.getElementById("input_giro_bank").value = ''
        document.getElementById("input_giro_nogiro").value = ''
        document.getElementById("input_giro_tanggal").valueAsDate = new Date()
        // document.getElementById("input_giro_valas").value = 'IDR'
        // document.getElementById("input_giro_kurs").value = '1.00'
        // document.getElementById("input_giro_nilaigiro").value = '0.00'
        // document.getElementById("input_giro_keterangan").value = ''

        lockFormGiro(false)
        $("#labelAddGiro").show()
        $("#labelEditGiro").hide()
        $("#buttonSubmitAddGiro").show()
        $("#buttonSubmitEditGiro").hide()
        $("#formGiroAdd").show()
      }



      // function submitAddGiroBGT (index) {
      //
      //
      //   // tempBarangAddEdit
      //   // listPencairanGiro
      //
      //
      //   let xdebet = 0
      //   let xdebetrp = 0
      //   listPencairanGiroKoreksi.forEach((item, i) => {
      //
      //     // if (document.getElementById(`giroChecklist${i}`).checked) {
      //       // tempDataPencairanGiro.push(item)
      //       xdebet += Number(item.Debet)
      //       xdebetrp += Number(item.DebetRp)
      //     // }
      //
      //   });
      //
      //   xdebet += Number(listPencairanGiro[index].jumlah)
      //   xdebetrp += Number(listPencairanGiro[index].jumlah) * Number(listPencairanGiro[index].Kurs)
      //
      //   let tanggal = tempBarangAddEdit.Tanggal
      //   let nobukti = tempBarangAddEdit.NoBukti
      //   let urutbukticair = tempBarangAddEdit.Urut
      //   let keterangancair = listPencairanGiro[index].Keterangan
      //   let nogiro = listPencairanGiro[index].Nogiro
      //   let bank = listPencairanGiro[index].Bank
      //   let kredit = listPencairanGiro[index].jumlah
      //   let kreditrp = Number(listPencairanGiro[index].jumlah) * Number(listPencairanGiro[index].Kurs)
      //   let _token = $("#_token").val()
      //
      //   console.log(nobukti, urutbukticair, xdebet, xdebetrp)
      //   $.ajax({
      //     url: "{!! url('girodibukaspaddgirobgt') !!}",
      //     type: "post",
      //     async: false,
      //     data: {
      //       _token,
      //       tanggal,
      //       nobukti,
      //       keterangancair,
      //       nogiro,
      //       bank,
      //       xdebet,
      //       xdebetrp,
      //       urutbukticair,
      //       kredit,
      //       kreditrp
      //
      //     },
      //     success: function(res) {
      //       console.log(res)
      //       if (res == 1) {
      //
      //
      //         alertify.success("Berhasil menambah giro")
      //         refreshDataTableGiroBGT(nobukti)
      //         refreshDataTable(nobukti)
      //         $('.showhideitemgiro').modal("toggle")
      //       }
      //
      //
      //
      //
      //     },
      //     error: function (err) {
      //       console.log(err)
      //       alertify.warning('Terjadi kesalahan silahkan refresh browser')
      //       resRefresh = 0;
      //     }
      //
      //   })
      //
      //
      // }


      function buttonAddGiroBGTAdd () {
        console.log("buttonAddGiroAdd")
        if (listPencairanGiroBGT.length) {
          alertify.warning("Sudah ada giro")

          return
        }
        document.getElementById("input_girobgt_bank").value = ''
        document.getElementById("input_girobgt_nogiro").value = ''
        document.getElementById("input_girobgt_tanggal").valueAsDate = new Date()
        // document.getElementById("input_girobgt_valas").value = 'IDR'
        // document.getElementById("input_girobgt_kurs").value = '1.00'
        document.getElementById("input_girobgt_nilaigiro").value = '0.00'
        // document.getElementById("input_girobgt_keterangan").value = ''

        lockFormGiroBGT(false)
        $("#formGiroBGTAdd").show()
      }

      function submitAddGiro () {
        let _token = $("#_token").val()
        let nogiro = $("#input_giro_nogiro").val()
        let bank = $("#input_giro_bank").val()
        let tanggal = $("#input_giro_tanggal").val()
        let valas = $("#input_giro_valas").val()
        let kurs = $("#input_giro_kurs").val()
        let nilaigiro = $("#input_giro_nilaigiro").val()
        if ( !nogiro || !bank || Number(kurs) <= 0 || Number(nilaigiro) <= 0) {
          alertify.warning("Data tidak lengkap")
          return
        }

        let xcheck = listGiroAdd.filter(el => el.nogiro == nogiro && el.bank == bank)
        console.log(xcheck)
        if (xcheck.length) {
          // console.log("MASOK SINI")
          alertify.warning("Nogiro dan Bank sudah terdaftar")
          return
        }
        console.log(nogiro, bank)
          $.ajax({
            url: "{!! url('girodibukacekgiroexist') !!}",
            type: "post",
            async: false,
            data: {
              _token,
              nogiro,
              bank
            },
            success: function(res) {
              console.log(res)



              if (!res.length) {
                listGiroAdd.push({
                  nogiro ,
                  bank ,
                  tanggal ,
                  valas ,
                  kurs ,
                  nilaigiro,
                  tipe: 'HT'
                })

                refreshDataTableGiro()
                $(".showhideitemgiro").hide()
                alertify.success("Giro berhasil ditambah")

              } else {
                alertify.warning("Nogiro dan Bank sudah terdaftar")
              }


            },
            error: function (err) {
              console.log(err)
              alertify.warning('Terjadi kesalahan silahkan refresh browser')
            }

          })





      }

      function refreshDataTableGiro () {


        // if
        let rowTableGiro = ''

        let jmlGiro = 0
        if (listGiroAdd.length) {

          // document.getElementById('buttonAddListValas').disabled = true

          listGiroAdd.forEach((item, i) => {
            jmlGiro += Number(item.nilaigiro)
            rowTableGiro += `
            <tr>
              <td>${item.bank}</td>
              <td>${item.nogiro}</td>
              <td>${formatDate(item.tanggal)}</td>
              <td class="text-right">0.00</td>
              <td class="text-right">${formatAngka(parseFloat(item.nilaigiro).toFixed(2))}</td>

              <td class="text-right">${formatAngka(parseFloat(item.nilaigiro).toFixed(2))}</td>
              <td>${item.valas}</td>
              <td class="text-right">${formatAngka(parseFloat(item.kurs).toFixed(2))}</td>
              <td class="text-right">0.00</td>
              <td class="text-right">${formatAngka(parseFloat(item.nilaigiro * item.kurs).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.nilaigiro * item.kurs).toFixed(2))}</td>
              <td>

              <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteGiro(${i})"><i class="bi bi-trash"></i></button>
              </td>
            </tr>

            `
          });
          // <button class="btn btn-success btn-sm" type="button" onclick="buttonEditGiro(${i})"><i class="bi bi-pen"></i></button>
        } else {
          // document.getElementById('buttonAddListValas').disabled = false

          rowTableGiro = `<tr>
            <td colspan=12 class="text-center">Belum ada data</td>
          </tr>`
        }

        document.getElementById("giroModalTableData").innerHTML = rowTableGiro
        document.getElementById("AddAddJumlah").value = parseFloat(jmlGiro).toFixed(2)
        document.getElementById("AddAddJumlahGiro").value = parseFloat(jmlGiro).toFixed(2)

      }

      function buttonDeleteGiro (index) {

  listGiroAdd.splice(index,1)
  refreshDataTableGiro()

}

function onChangeTransaksi () {
  document.getElementById("input_add_kodeperkiraan").value = ''
  document.getElementById("input_add_keteranganperkiraan").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kepadaterima").value = ''

  // document.getElementById("input_add_bon").value = ''
  // document.getElementById("input_add_nilaibon").value = '0.00'

    console.log("onChangeTransaksi")
    $('.showhideitem').hide();
    // $('.showhidePart').hide();
    // let value = $("#input_add_transaksi").val()
    // console.log(value)
    // $(`.part${value}`).show();


}

function setNewNoBukti (ppn) {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = $("#input_add_transaksi").val()
  $.ajax({
    url: "{!! url('girodibukaspnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode,
      ppn

    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function cleanFormAddAdd () {
  document.getElementById("AddAddNoDPH").value = ''

  document.getElementById("AddAddKodeDevisi").value = ''
  document.getElementById("AddAddNamaDevisi").value = ''
  document.getElementById("AddAddValas").value = 'IDR'
  document.getElementById("AddAddKurs").value = '1.00'
  document.getElementById("AddAddLawan").value = ''
  document.getElementById("AddAddKodeCustomer").value = ''
  document.getElementById("AddAddNamaCustomer").value = ''
  document.getElementById("AddAddKeteranganLawan").value = ''
  document.getElementById("AddAddJumlah").value = '0.00'
  document.getElementById("AddAddJumlahGiro").value = '0.00'
  document.getElementById("AddAddKeterangan").value = ''
  // document.getElementById("AddAddKeteranganDetail").value = ''
  document.getElementById("AddAddKodeDepartemen").value = ''
  document.getElementById("AddAddNamaDepartemen").value = ''

}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanFormAdd (tipe = 0) {

  // if (tipe == 0) {
  //   document.getElementById("input_add_transaksi").value = 'BBK'
  //   onChangeTransaksi()
  // }


  document.getElementById("input_add_kodeperkiraan").value = ''
  document.getElementById("input_add_keteranganperkiraan").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kepadaterima").value = ''

  // document.getElementById("input_add_bon").value = ''
  // document.getElementById("input_add_nilaibon").value = '0.00'

}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()
  if (!listGiroAdd.length) {
    alertify.warning("Belum ada giro")
    return
  }
  console.log(nobukti , tanggal , nopajak)

}


function submitAddAdd () {
  let transaksi  = $("#input_add_transaksi").val()
  let choice = 'I'
  let _token = $("#_token").val()


  if (transaksi == 'BBG') {
    let tempData = listGiroAdd
    let nobukti = $("#input_add_nobukti").val()
    let nourut = $("#input_add_nourut").val()
    let kodeperkiraan = $("#input_add_kodeperkiraan").val()
    let tanggal = $("#input_add_tanggal").val()
    let kepadaterima = $("#input_add_kepadaterima").val()
    let urut = 0
    let kodedevisi = $("#AddAddKodeDevisi").val()
    let kodecustsupp = $("#AddAddKodeCustomer").val()
    let valas = $("#AddAddValas").val()
    let kurs = $("#AddAddKurs").val()
    let lawan = $("#AddAddLawan").val()
    let jumlah = $("#AddAddJumlah").val()
    let sumber = $("#AddAddSumber").val()
    let jumlahgiro = $("#AddAddJumlahGiro").val()
    let departemen = $("#AddAddKodeDepartemen").val()
    let keterangandet = $("#AddAddKeterangan").val()
    let nodph = $("#AddAddNoDPH").val()
    let jmlrecord = tipeform == 'add' ? 0 : 1


    if (!tempData.length) {
      alertify.warning("Tidak ada giro")
      return
    }

    if (!nodph || !kodedevisi || !kodecustsupp || !lawan || !departemen || !valas || Number(kurs) <= 0 || !keterangandet) {
      alertify.warning("Data tidak lengkap")
      return

    }


      $.ajax({
          url: "{!! url('girodibukaspadd') !!}",
          type: "post",
          async: false,
          data: {
            transaksi ,
            choice ,
            _token ,
            tempData ,
            nobukti ,
            nourut ,
            nodph,

            kodeperkiraan ,
            tanggal ,
            kepadaterima ,
            urut,
            kodedevisi ,
            kodecustsupp ,
            valas ,
            kurs ,
            lawan ,
            jumlah ,
            sumber ,
            jumlahgiro ,
            departemen ,
            keterangandet ,
            jmlrecord

          },
          success: function(res) {
            console.log(res ,'!')

            if (res == 1) {
              // $("#form").modal('toggle')
              alertify.success('Giro telah ditambah');
              loadAll()
              // buttonCloseForm()
              lockFormAdd()
              tipeform = 'edit'
              // document.getElementById("buttonAddListCustomer").disabled = true
              // document.getElementById("input_add_tanggal").disabled = true
              $('.showhideitem').hide();
              refreshDataTable(nobukti)

              document.getElementById("input_add_transaksi").disabled = true

              // $("#form").modal('toggle')

            }
            if (res == 2) {
              setNewNoBukti()
              alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
            }
            //
            // if (res == 3 ) {
            //   alertify.warning('Stok gudang tidak mencukupi');
            // }

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })



  } else {


  }
}

function submitAddEdit () {



  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let choice = "U"
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_kepadaterima").val()
  let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
  let tanggal = $("#input_add_tanggal").val()

  let lampiran = 0
  let keterangan2 = ''


  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = $("#AddAddKurs").val()
  let lawan  = $("#AddAddLawan").val()
  let jumlah  = $("#AddAddJumlah").val()
  let jumlahgiro  = $("#AddAddJumlahGiro").val()
  let keterangan  = $("#AddAddKeterangan").val()
  // let keterangandetail  = $("#AddAddKeteranganDetail").val()
  let keterangandetail  = ''
  let kodedepartemen  = $("#AddAddKodeDepartemen").val()

  let kredit = 0
  let kreditrp = 0

  let tphc = 'H'

  if (!kodedevisi || !valas || !lawan || !kodedepartemen || !keterangan) {
    alertify.warning("Data tidak lengkap")
    return
  }

  if (jumlah < 0) {
    alertify.warning("Jumlah < 0")
    return
  }

  let jumlahrp = Number(jumlah) * Number(kurs)


  let urut = tempBarangAddEdit.Urut

  let custsuppP = tempBarangAddEdit.CustSuppP
  let custsuppL = tempBarangAddEdit.CustSuppL
  let noaktivaP = tempBarangAddEdit.NoAktivaP
  let noaktivaL = tempBarangAddEdit.NoAktivaL
  let statusaktivaP = tempBarangAddEdit.StatusAktivaP
  let statusaktivaL = tempBarangAddEdit.StatusAktivaL

  let nobon = $("#input_add_bon").val()
  let kodebag = '-'

  let kodeP = tempBarangAddEdit.KodeP
  let kodeL = tempBarangAddEdit.KodeL
  let statusgiro = tempBarangAddEdit.StatusGiro
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = ''
  let kodesubcost = ''
  let nodph = tempBarangAddEdit.NODPH
  let urutdph = tempBarangAddEdit.urutDPH
  let dppdph = ''
  let tp = ''
  let ppklx = ''
  let nofaktur = ''
  let plok = 0
  let nobons = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = tempBarangAddEdit.notitipan
  let uruttitipan = tempBarangAddEdit.URUTTITIPAN
  let pSKB = 0
  let perkiraanx = transaksi == 'BBK' ? lawan : kodeperkiraan
  let lawanx = transaksi == 'BBK' ? kodeperkiraan : lawan







  console.log({
    tipeform,
    _token,
    nobukti ,
    nourut,
    transaksi,
    note,
    kodeperkiraan ,
    tanggal,
    lampiran ,
    keterangan2 ,
    kodedevisi ,
    valas ,
    kurs  ,
    lawan ,
    jumlah,
    keterangan  ,
    keterangandetail ,
    kodedepartemen  ,
    kredit,
    kreditrp,
    tphc,
    jumlahrp ,
    urut ,
    custsuppP ,
    custsuppL,
    noaktivaP ,
    noaktivaL ,
    statusaktivaP ,
    statusaktivaL ,
    nobon ,
    kodebag ,
    kodeP ,
    kodeL ,
    statusgiro ,
    simbol,
    flagsimbol ,
    kodecost ,
    kodesubcost ,
    nodph ,
    urutdph,
    dppdph,
    tp,
    ppklx ,
    nofaktur,
    plok ,
    nobons,
    jmlrecord,
    notitipan ,
    uruttitipan,
    pSKB
  })



  $.ajax({
      url: "{!! url('girodibukaspadd') !!}",
      type: "post",
      async: false,
      data: {
        choice,
        tipeform,
        _token,
        nobukti ,
        nourut,
        transaksi,
        note,
        kodeperkiraan ,
        tanggal,

        lampiran ,
        keterangan2 ,
        perkiraanx,
        lawanx,

        kodedevisi ,
        valas ,
        kurs  ,
        lawan ,
        jumlah,
        keterangan  ,
        keterangandetail ,
        kodedepartemen  ,

        kredit,
        kreditrp,

        tphc,
        jumlahrp ,


        urut ,

        custsuppP ,
        custsuppL,
        noaktivaP ,
        noaktivaL ,
        statusaktivaP ,
        statusaktivaL ,

        nobon ,
        kodebag ,

        kodeP ,
        kodeL ,
        statusgiro ,
        simbol,
        flagsimbol ,
        kodecost ,
        kodesubcost ,
        nodph ,
        urutdph,
        dppdph,
        tp,
        ppklx ,
        nofaktur,
        plok ,
        nobons,
        jmlrecord,
        notitipan ,
        uruttitipan,
        pSKB
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bank telah ditambah');
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          $('.showhideitem').hide();
          refreshDataTable(nobukti)

          // $("#form").modal('toggle')

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }
        //
        // if (res == 3 ) {
        //   alertify.warning('Stok gudang tidak mencukupi');
        // }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })









}



function buttonAddDelete (index) {


    tempBarangAddEdit = listData[index]


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus Item ?',
        function() {

          let choice = "D"
          let _token  = $("#_token").val()
          let nobukti  = $("#input_add_nobukti").val()
          let nourut  = $("#input_add_nourut").val()
          let transaksi  = $("#input_add_transaksi").val()
          let note  = $("#input_add_kepadaterima").val()
          let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
          let tanggal = $("#input_add_tanggal").val()

          let lampiran = 0
          let keterangan2 = ''


          let kodedevisi  = $("#AddAddKodeDevisi").val()
          let valas  = $("#AddAddValas").val()
          let kurs  = $("#AddAddKurs").val()
          let lawan  = $("#AddAddLawan").val()
          let jumlah  = $("#AddAddJumlah").val()
          let keterangan  = $("#AddAddKeterangan").val()
          // let keterangandetail  = $("#AddAddKeteranganDetail").val()

          let keterangandetail  = ''
          let kodedepartemen  = $("#AddAddKodeDepartemen").val()

          let kredit = 0
          let kreditrp = 0

          let tphc = 'H'


          let jumlahrp = 0


          let urut = tempBarangAddEdit.Urut

          let custsuppP = tempBarangAddEdit.CustSuppP
          let custsuppL = tempBarangAddEdit.CustSuppL
          let noaktivaP = tempBarangAddEdit.NoAktivaP
          let noaktivaL = tempBarangAddEdit.NoAktivaL
          let statusaktivaP = tempBarangAddEdit.StatusAktivaP
          let statusaktivaL = tempBarangAddEdit.StatusAktivaL

          let nobon = $("#input_add_bon").val()
          let kodebag = '-'

          let kodeP = tempBarangAddEdit.KodeP
          let kodeL = tempBarangAddEdit.KodeL
          let statusgiro = tempBarangAddEdit.StatusGiro
          let simbol = $("#input_add_simbol").val()
          let flagsimbol = ''
          let kodecost = ''
          let kodesubcost = ''
          let nodph = tempBarangAddEdit.NODPH
          let urutdph = tempBarangAddEdit.urutDPH
          let dppdph = ''
          let tp = ''
          let ppklx = ''
          let nofaktur = ''
          let plok = 0
          let nobons = ''
          let jmlrecord = tipeform == 'add' ? 0 : 1
          let notitipan = tempBarangAddEdit.notitipan
          let uruttitipan = tempBarangAddEdit.URUTTITIPAN
          let pSKB = 0
          let perkiraanx = transaksi == 'BBK' ? lawan : kodeperkiraan
          let lawanx = transaksi == 'BBK' ? kodeperkiraan : lawan


          console.log({
            tipeform,
            _token,
            nobukti ,
            nourut,
            transaksi,
            note,
            kodeperkiraan ,
            tanggal,
            lampiran ,
            keterangan2 ,
            kodedevisi ,
            valas ,
            kurs  ,
            lawan ,
            jumlah,
            keterangan  ,
            keterangandetail ,
            kodedepartemen  ,
            kredit,
            kreditrp,
            tphc,
            jumlahrp ,
            urut ,
            custsuppP ,
            custsuppL,
            noaktivaP ,
            noaktivaL ,
            statusaktivaP ,
            statusaktivaL ,
            nobon ,
            kodebag ,
            kodeP ,
            kodeL ,
            statusgiro ,
            simbol,
            flagsimbol ,
            kodecost ,
            kodesubcost ,
            nodph ,
            urutdph,
            dppdph,
            tp,
            ppklx ,
            nofaktur,
            plok ,
            nobons,
            jmlrecord,
            notitipan ,
            uruttitipan,
            pSKB
          })



          $.ajax({
              url: "{!! url('girodibukaspdelete') !!}",
              type: "post",
              async: false,
              data: {
                choice,
                tipeform,
                _token,
                nobukti ,
                nourut,
                transaksi,
                note,
                kodeperkiraan ,
                tanggal,

                lampiran ,
                keterangan2 ,
                perkiraanx,
                lawanx,

                kodedevisi ,
                valas ,
                kurs  ,
                lawan ,
                jumlah,
                keterangan  ,
                keterangandetail ,
                kodedepartemen  ,

                kredit,
                kreditrp,

                tphc,
                jumlahrp ,


                urut ,

                custsuppP ,
                custsuppL,
                noaktivaP ,
                noaktivaL ,
                statusaktivaP ,
                statusaktivaL ,

                nobon ,
                kodebag ,

                kodeP ,
                kodeL ,
                statusgiro ,
                simbol,
                flagsimbol ,
                kodecost ,
                kodesubcost ,
                nodph ,
                urutdph,
                dppdph,
                tp,
                ppklx ,
                nofaktur,
                plok ,
                nobons,
                jmlrecord,
                notitipan ,
                uruttitipan,
                pSKB
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('Giro telah dihapus');
                  loadAll()
                  // buttonCloseForm()
                  tipeform = 'edit'
                  // document.getElementById("buttonAddListCustomer").disabled = true
                  // document.getElementById("input_add_tanggal").disabled = true
                  $('.showhideitem').hide();
                  refreshDataTable(nobukti)

                  // $("#form").modal('toggle')

                }

                if (res == 9) {

                  alertify.warning("Terdapat giro yang sudah dicairkan")
                  return
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





function buttonAddListLawan () {
  listLawan = []

  console.log('buttonAddListLawan')


  let _token = $("#_token").val();
  let perkiraan = $("#input_add_kodeperkiraan").val();
  let transaksi = $("#input_add_transaksi").val();
  if(!perkiraan) {
    alertify.warning("Pilih perkiraan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('girodibukalistlawan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan,
      transaksi
    },
    success: function(res) {
      console.log(res)
      listLawan  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLawan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}', '${item.Kode}' )">
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Simbol}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_lawan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListLawan').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListLawanBGC () {
  listLawan = []

  console.log('buttonAddListLawan')


  let _token = $("#_token").val();
  let perkiraan = $("#input_add_kodeperkiraan").val();
  let transaksi = $("#input_add_transaksi").val();
  if(!perkiraan) {
    alertify.warning("Pilih perkiraan terlebih dahulu")
    return
  }

  if (transaksi == 'BCG') {


  } else {


  }

  $.ajax({
    url: "{!! url('girodibukalistlawanbgc') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan,
      transaksi
    },
    success: function(res) {
      console.log(res)
      listLawan  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLawanBGC(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}', '${item.Kode}' )">
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Simbol}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_lawan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListLawan').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPP (dataLawan) {
  listDPP = []

  console.log('modalDPP')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('girodibukalistdpp') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPP = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickDPP(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')">
        <td>${item.Nobukti}</td>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00'}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dpp").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPP').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPP tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPHUHTBBM (dataLawan) {


  console.log('modalDPHUHTBBM')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('girodibukalistcustsuppumb') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustDPHUHTBBM( '${item.KODESUPP}', '${item.NAMACUSTSUPP}','${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')">
        <td>${item.KODESUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm_custsupp").innerHTML = rowTable
      document.getElementById("input_dphuhtbbm_namacustsupp").value = ''
      document.getElementById("input_dphuhtbbm_kodecustsupp").value = ''

      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
        <tr>
          <td colspan=10 class="text-center">Data tidak ditemukkan</td>
        </tr>
      `


      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPHUHTBBM').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("C tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPHUHT (dataLawan) {
  listDPH = []

  console.log('modalDPHUHT')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('girodibukalistdphuht') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPH = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickDPH(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')">
        <td>${item.Nobukti}</td>
        <td>${item.NOUM}</td>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00'}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuht").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPHUHT').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPH tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function modalDPH (dataLawan) {
  listDPH = []

  console.log('modalDPH')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();




  $.ajax({
    url: "{!! url('girodibukalistdph') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPH = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickDPH(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')">
        <td>${item.Nobukti}</td>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00'}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dph").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPH').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPH tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonMinusUMB (index) {

  let _token = $("#_token").val();
  let umb = listUMB[index]
  let nobukti = $("#input_add_nobukti").val();
  $.ajax({
    url: "{!! url('girodibukaspdeletetemprumjual') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noumb: umb.NOBUKTI,
      noretur: umb.NORETUR ,
      tanggal: umb.TANGGAL ,
      noso: umb.NOSO ,
      valas: umb.VALAS ,
      kurs: umb.KURS ,
      dpp: umb.DPP ,
      ppn: umb.PPN ,
      kodesupp: umb.KODESUPP ,
      subtotal: umb.SUBTOTAL ,
      nobukti: nobukti,
      urut: 0
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>

          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-right">${parseFloat(item.PPN).toFixed(2)}</td>
          <td class="text-right">${parseFloat(item.SUBTOTAL).toFixed(2)}</td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>
          <td class="text-right">${parseFloat(item.PPN).toFixed(2)}</td>
          <td class="text-right">${parseFloat(item.SUBTOTAL).toFixed(2)}</td>

          <td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i}  )" type="button" ><i class="bi bi-trash"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable

      if (res.length) {
        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonPlusUMB (index) {

  let _token = $("#_token").val();
  let umb = listUMB[index]
  let nobukti = $("#input_add_nobukti").val();
  $.ajax({
    url: "{!! url('girodibukaspaddtemprumjual') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noumb: umb.NOBUKTI,
      noretur: "" ,
      tanggal: umb.TANGGAL ,
      noso: umb.NOSO ,
      valas: umb.VALAS ,
      kurs: umb.KURS ,
      dpp: umb.DPP ,
      ppn: umb.PPN ,
      kodesupp: umb.KODESUPP ,
      subtotal: umb.SUBTOTAL ,
      nobukti: nobukti,
      urut: 0
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i}  )" type="button" ><i class="bi bi-trash"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable

      if (res.length) {
        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function onChangeDPPUMB (index) {

  let qnt = $(`#add_inputDPPUMBQnt${index}`).val();

  if (Number(qnt) < 0) {
    document.getElementById(`add_inputDPPUMBQnt${index}`).value = '0.00'
    document.getElementById(`add_inputPPNUMBQnt${index}`).value = '0.00'
    document.getElementById(`add_inputSUBTOTALUMBQnt${index}`).value = '0.00'
    alertify.warning('Qnt < 0')
    return
  }

  let ppn = listUMB[index].ppnx

  let ppnx = Number(qnt) * Number(ppn)
  let subtotalx = Number(ppnx) + Number(qnt)





  let _token = $("#_token").val();
  let umb = listUMB[index]
  let nobukti = $("#input_add_nobukti").val();

  console.log(qnt, ppnx)
  $.ajax({
    url: "{!! url('girodibukaspupdatetemprumjual') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noumb: umb.NOBUKTI,
      noretur: umb.NORETUR ,
      tanggal: umb.TANGGAL ,
      noso: umb.NOSO ,
      valas: umb.VALAS ,
      kurs: umb.KURS ,
      dpp: qnt ,
      ppn: ppnx ,
      kodesupp: umb.KODESUPP ,
      subtotal: subtotalx ,
      nobukti: nobukti,
      urut: 0
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i}  )" type="button" ><i class="bi bi-trash"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable

      if (res.length) {
        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })





}


function buttonAddPickCustDPHUHTBBM (kode, nama,perkiraanlawan, kodelawan , keteranganlawan) {




  document.getElementById("input_dphuhtbbm_namacustsupp").value = nama
  document.getElementById("input_dphuhtbbm_kodecustsupp").value = kode

  document.getElementById("AddAddLawan").value = perkiraanlawan
  document.getElementById("AddAddKodeLawan").value = kodelawan
  document.getElementById("AddAddKeteranganLawan").value = keteranganlawan



  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('girodibukaprosesumb') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      custsupp: kode
    },
    success: function(res) {
      console.log(res)
      listUMB = res
      let rowTable = ``
      res.forEach((item, i) => {
        if (item.NORETUR == '') {
          rowTable += `
          <tr>
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>

          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonPlusUMB(${i}  )" type="button" ><i class="bi bi-plus"></i></button></td>

          </tr>`


        } else {
          rowTable += `
          <tr style="background-color: #FF746C">
          <td>${item.NOBUKTI}</td>
          <td>${item.NORETUR ? item.NORETUR : '' }</td>
          <td>${item.TANGGAL ? formatDate(item.TANGGAL) : '' }</td>
          <td>${item.NOSO}</td>
          <td>${item.VALAS}</td>
          <td>${item.KURS}</td>
          <td class="text-center"><input id="add_inputDPPUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.DPP).toFixed(2)}' class="form-control text-right" onBlur="onChangeDPPUMB(${i})"></td>

          <td class="text-center"><input id="add_inputPPNUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.PPN).toFixed(2)}' class="form-control text-right"  disabled></td>
          <td class="text-center"><input id="add_inputSUBTOTALUMBQnt${i}" style="height:30px; min-width: 130px" type="number" value='${parseFloat(item.SUBTOTAL).toFixed(2)}' class="form-control text-right"  disabled></td>
<td class="text-center"><button class="btn btn-danger btn-sm" onclick="buttonMinusUMB(${i})" type="button" ><i class="bi bi-dash-lg"></i></button></td>

          </tr>`

        }

      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = rowTable



      if (res.length) {

        document.getElementById("AddAddJumlah").value = res[0].totalqntx
      } else {
        document.getElementById("AddAddJumlah").value = '0.00'
        document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
          <tr>
            <td colspan=10 class="text-center">Data tidak ditemukkan</td>
          </tr>
        `
        alertify.warning("Data tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })



}


// function buttonAddPickDPH (indexDPH , perkiraanlawan, kodelawan , keteranganlawan) {
//
//
//   tempDPPDPH = listDPH[indexDPH]
//
//   document.getElementById("AddAddLawan").value = perkiraanlawan
//   document.getElementById("AddAddKodeLawan").value = kodelawan
//   document.getElementById("AddAddKeteranganLawan").value = keteranganlawan
//   document.getElementById("AddAddJumlah").value = parseFloat(tempDPPDPH.DIBAYAR).toFixed(2)
//   console.log(tempDPPDPH)
//
//   // $('.showhideitem').hide();
//   buttonAddListBatal()
//
// }


function buttonAddPickDPP (indexDPP , perkiraanlawan, kodelawan , keteranganlawan) {


  tempDPPDPH = listDPP[indexDPP]

  document.getElementById("AddAddLawan").value = perkiraanlawan
  document.getElementById("AddAddKodeLawan").value = kodelawan
  document.getElementById("AddAddKeteranganLawan").value = keteranganlawan
  document.getElementById("AddAddJumlah").value = parseFloat(tempDPPDPH.DIBAYAR).toFixed(2)
  console.log(tempDPPDPH)

  // $('.showhideitem').hide();
  buttonAddListBatal()

}


function buttonAddListDepartemen () {
  listDepartemen = []

  console.log('buttonAddListDepartemen')


  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('girodibukalistdepartemen') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listDepartemen  = res
      let rowTable = `<tr class="pick-row" onclick="buttonAddPickDepartemen(null,'-' , ''  )">
      <td>-</td>
      <td></td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickDepartemen(${i},'${item.KDDEP}' , '${item.NMDEP}'  )">
        <td>${item.KDDEP}</td>
        <td>${item.NMDEP}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_departemen").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDepartemen').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Departemen tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListValas () {
  listValas= []

  console.log('buttonAddListValas')


  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('girodibukalistvalas') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listValas = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickValas(${i},'${item.KODEVLS}' , '${item.NAMAVLS}' , '${item.KURS}' )">
        <td>${item.KODEVLS}</td>
        <td>${item.NAMAVLS}</td>
        <td class="text-right">${parseFloat(item.KURS).toFixed(2)}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListPerkiraan').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListDevisi () {
  listDevisi= []

  console.log('buttonAddListDevisi')


  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('girodibukalistdevisi') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listDevisi = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickDevisi(${i},'${item.Devisi}' , '${item.NamaDevisi}' )">
        <td>${item.Devisi}</td>
        <td>${item.NamaDevisi}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_devisi").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDevisi').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Devisi tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPerkiraan () {
  listPerkiraan= []

  console.log('buttonAddListPerkiraan')


  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('girodibukalistperkiraanheader') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      listPerkiraan = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPerkiraan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}' , '${item.IsPPN}' )">
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Simbol}</td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_perkiraan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListPerkiraan').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonAddListInvoice () {
  listInvoice = []

  console.log('buttonAddListInvoice')


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp  ) {
    alertify.warning("Pilih customer terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('kreditnotelistinvoice') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
    },
    success: function(res) {
      console.log(res)
      listInvoice = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
        <td>${item.NoFaktur}</td>
        <td>${formatDate(item.Tanggal,'/')}</td>
        <td>${formatDate(item.JatuhTempo,'/')}</td>
        <td>${item.KodeVls}</td>
        <td><input id="add_inputQnt${i}" style="height:30px; min-width: 130px" type="number" value='0.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>

        <td><input id="add_inputKurs${i}" style="height:30px; min-width: 90px" type="number" value='1.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>
        <td><input style="height:30px; min-width: 130px" id="add_inputQntRp${i}" type="number" value='0.00' class="form-control text-right"  disabled></td>

        <td class="text-right">${formatAngka(parseFloat(item.SaldoD).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Saldo).toFixed(2))}</td>

        <td><input style="height:30px; min-width: 200px" id="add_inputKeterangan${i}" type="text" value='' class="form-control text-left" ></td>


        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_invoice").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListInvoice').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Tidak ada invoice untuk ditambah")
      }


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
    url: "{!! url('perintahreturjuallistnobeli') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg,
      noso
    },
    success: function(res) {
      let rowTable = ``
      rowTable += `<tr>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('-' , 0)" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NOBUKTI}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('${item.NOBUKTI}' ,${item.urut} )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });





      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_nobeli").innerHTML = rowTable
      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
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

function onChangeNilaiKursItem () {
  console.log('onChangeNilaiKursItem' )
  let onChangeQnt = $(`#AddEditNilai`).val();
  let onChangeKurs = $(`#AddEditKurs`).val();

  document.getElementById(`AddEditNilaiRp`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)

}

function onChangeNilaiKurs (index) {
  console.log('onChangeNilaiKurs' , index)
  let onChangeQnt = $(`#add_inputQnt${index}`).val();
  let onChangeKurs = $(`#add_inputKurs${index}`).val();

  document.getElementById(`add_inputQntRp${index}`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)

}

function buttonAddListNoInvoice () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp) {
    alertify.warning("Pilih customer terlebih dahulu")
    return
  }

  $('#tabel_add_list_noinvoice').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistnoinvoice') !!}",
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
        <td>${item.NOBUKTI}</td>
        <td>${item.TANGGAL}</td>
        <td>${item.NoSO}</td>
        <td>${item.NAMAGDG}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoInvoice('${item.NOBUKTI}' , '${item.NoSO}' , '${item.KODEGDG}', ${item.flagtipe}, ${item.ppn})" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_noinvoice").innerHTML = rowTable
      $("#tabel_add_list_noinvoice").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
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

function buttonAddListDPH () {
  console.log('buttonAddListDPH')
  // $('#tabel_add_list_custsupp').DataTable().destroy();
  $.ajax({
    url: "{!! url('girodibukalistdphbbg') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickDPH('${item.NoBukti}' , '${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Dibayar}' , '${item.Valas}' , '${item.KURS}')">
        <td>${item.NoBukti}</td>
        <td>${formatDate(item.Tanggal)}</td>
        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>

        <td class="text-right">${formatAngka(parseFloat(item.Dibayar).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.KL).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.LB).toFixed(2))}</td>

        <td>${item.Perkiraan}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=8>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_dphbbg").innerHTML = rowTable
    //   $("#tabel_add_list_custsupp").DataTable({
    //     "lengthChange": false,
    //       "paging": false ,
    // });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListDPHBBG').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListCustSupp () {
  console.log('buttonAddListCustsupp')
  $('#tabel_add_list_custsupp').DataTable().destroy();
  $.ajax({
    url: "{!! url('girodibukalistcustsupp') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustsupp('${item.kodecustsupp}' , '${item.namacustsupp}' , '${item.alamat1}')">
        <td>${item.kodecustsupp}</td>
        <td>${item.namacustsupp}</td>
        <td>${item.alamat1}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_custsupp").innerHTML = rowTable
      $("#tabel_add_list_custsupp").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListCustsupp').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddPickDevisi (index, kode, nama) {
  console.log('buttonAddPickDevisi')
  document.getElementById("AddAddKodeDevisi").value = kode
  document.getElementById("AddAddNamaDevisi").value = nama
  // document.getElementById("input_add_nobukti").value = simbol

  // $('.showhideitem').hide();
  // setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCustsupp (kode, nama) {
  console.log('buttonAddPickCustsupp',kode, nama)
  document.getElementById("AddAddKodeCustomer").value = kode
  document.getElementById("AddAddNamaCustomer").value = nama
  // document.getElementById("input_add_nobukti").value = simbol

  // $('.showhideitem').hide();
  // setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}


function buttonAddPickDPH (nobukti, kodecust , namacust , dibayar, valas , kurs) {
  console.log('buttonAddPickDPH',nobukti, kodecust , namacust , dibayar, valas , kurs)
  document.getElementById("AddAddNoDPH").value = nobukti
  document.getElementById("AddAddKodeCustomer").value = kodecust
  document.getElementById("AddAddNamaCustomer").value = namacust
  document.getElementById("AddAddValas").value = valas
  document.getElementById("AddAddJumlah").value = parseFloat(dibayar).toFixed(2)
  document.getElementById("AddAddJumlahGiro").value = parseFloat(dibayar).toFixed(2)
  document.getElementById("AddAddKurs").value = parseFloat(kurs).toFixed(2)
  // document.getElementById("input_add_nobukti").value = simbol

  // $('.showhideitem').hide();
  // setNewNoBukti(simbol)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickValas (index, kode, nama , kurs) {
  console.log('buttonAddPickValas')

  // $('#rowCustsupp').hide();

  document.getElementById("AddAddValas").value = kode
  document.getElementById("AddAddKurs").value = parseFloat(kurs).toFixed(2)

  tempDPPDPH = {}

  document.getElementById("AddAddLawan").value = ''
  document.getElementById("AddAddKodeLawan").value = ''
  document.getElementById("AddAddKeteranganLawan").value = ''

  buttonAddListBatal()
  // $("#form").modal('toggle')
}


function buttonAddPickPerkiraan (index, perkiraan, keterangan , simbol, ppn) {
  console.log('buttonAddPickPerkiraan')
  document.getElementById("input_add_kodeperkiraan").value = perkiraan
  document.getElementById("input_add_keteranganperkiraan").value = keterangan
  document.getElementById("input_add_simbol").value = simbol
  // document.getElementById("input_add_nobukti").value = simbol

  $('.showhideitem').hide();
  setNewNoBukti(ppn)
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickLawan (index, perkiraan, keterangan , simbol, kode) {
  // let trans =  $("#input_add_transaksi").val();
  // console.log(trans)
  // if (kode == 'UHT' && trans == 'BBM') {
  //
  //   modalDPHUHTBBM(listLawan[index])
  //
  //
  //
  //   document.getElementById("AddAddJumlah").disabled = true
  //   document.getElementById("AddAddJumlah").value = '0.00'
  //
  //
  //
  //   return
  // }
  //
  // // document.getElementById("AddAddJumlah").disabled = false
  // // document.getElementById("AddAddJumlah").value = '0.00'
  //
  //
  //
  //
  // if ( kode == 'HT' ) {
  //   modalDPH(listLawan[index])
  //
  //   return
  // }
  //
  //
  //
  // if (kode == 'UHT' && trans == 'BBK') {
  //   modalDPHUHT(listLawan[index])
  //
  //   return
  // }
  //
  //
  //
  //
  //
  // if (perkiraan == '113400') {
  //
  //   document.getElementById("AddAddKodeCustsupp").value = ''
  //   document.getElementById("AddAddNamaCustsupp").value = ''
  //   document.getElementById("AddAddLawan").value = perkiraan
  //   document.getElementById("AddAddKodeLawan").value = kode
  //   document.getElementById("AddAddKeteranganLawan").value = keterangan
  //
  //   buttonAddListBatal()
  //   $('#rowCustsupp').show();
  //
  //   return
  // }
  //
  // $('#rowCustsupp').hide();
  //
  // if (kode == 'PT' ) {
  //   modalDPP(listLawan[index])
  //
  //   return
  // }
  console.log('buttonAddPickLawan')
  console.log(index, perkiraan, keterangan , simbol, kode)
  document.getElementById("AddAddLawan").value = perkiraan
  document.getElementById("AddAddKodeLawan").value = kode
  document.getElementById("AddAddKeteranganLawan").value = keterangan

  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}



function buttonAddPickDepartemen (index, kode, nama) {


  console.log('buttonAddPickDepartemen')
  document.getElementById("AddAddNamaDepartemen").value = nama
  document.getElementById("AddAddKodeDepartemen").value = kode

  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCustomer (kode, nama , alamat) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat)
  document.getElementById("AddAddKodeCustsupp").value = kode
  document.getElementById("AddAddNamaCustsupp").value = nama

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


  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("buttonAddListNoInvoice").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = true
  // document.getElementById("input_add_bon").disabled = true
  document.getElementById("input_add_kepadaterima").disabled = true
  document.getElementById("buttonAddListPerkiraan").disabled = true
  document.getElementById("input_add_transaksi").disabled = true

}

function lockFormAddAdd () {
  // document.getElementById("buttonAddListDepartemen").disabled = true
  document.getElementById("buttonAddListLawan").disabled = true
  // document.getElementById("buttonAddListValas").disabled = true
  document.getElementById("buttonAddListDevisi").disabled = true
  document.getElementById("buttonAddListDPH").disabled = true

}

function unlockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = false
  document.getElementById("buttonAddListLawan").disabled = false
  document.getElementById("buttonAddListDPH").disabled = false
  // document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListDevisi").disabled = false
}





function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('girodibukaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log("RES DET TES")
      console.log(res)
      listData = res
      // console.log(res)
      if (!res.length) {
          alertify.success('Data Habis')
          // $("#form").modal('toggle')
          $('#page2').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listData.forEach((item, i) => {

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

              rowTable += `
                <tr>
                  <td>${item.Devisi}</td>

                  <td>${item.Perkiraan}</td>
                  <td>${item.NamaPerkiraan}</td>
                  <td>${item.Lawan }</td>
                  <td>${item.NamaLawan }</td>




                  <td>${item.TPHC}</td>
                  <td class="text-right">${item.DebetRp ?  formatAngka(parseFloat(item.DebetRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.Keterangan }</td>
                  <td class="text-right">${item.JumlahGiroRp ? formatAngka(parseFloat(item.JumlahGiroRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.NamaCost ? item.NamaCost : '' }</td>
                  <td>${item.NamaSubCost ? item.NamaSubCost : ''}</td>
                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `
      });

      document.getElementById("addTableData").innerHTML = rowTable


        document.getElementById("input_add_transaksi").value = listData[0].TipeTransHD
        document.getElementById("input_add_kodeperkiraan").value = listData[0].PerkiraanHd
        document.getElementById("input_add_keteranganperkiraan").value = listData[0].NamaPerkiraanHd
        document.getElementById("input_add_kepadaterima").value = listData[0].Note
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_add_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].Tanggal)










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}


function refreshDataTableDetail (nobukti) {
  console.log('refreshDataDetail' , nobukti)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('girodibukaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listData = res
      // console.log(res)
      if (!res.length) {
          alertify.success('Data Habis')
          // $("#form").modal('toggle')
          $('#page3').hide();
          // $('#page3').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listData.forEach((item, i) => {

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

              rowTable += `
                <tr>
                  <td>${item.Devisi}</td>

                  <td>${item.Perkiraan}</td>
                  <td>${item.NamaPerkiraan}</td>
                  <td>${item.Lawan }</td>
                  <td>${item.NamaLawan }</td>




                  <td>${item.TPHC}</td>
                  <td class="text-right">${item.DebetRp ?  formatAngka(parseFloat(item.DebetRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.Keterangan }</td>
                  <td class="text-right">${item.JumlahGiroRp ? formatAngka(parseFloat(item.JumlahGiroRp).toFixed(2)) : '0.00'}</td>
                  <td>${item.NamaCost ? item.NamaCost : '' }</td>
                  <td>${item.NamaSubCost ? item.NamaSubCost : ''}</td>

                </tr>

              `
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_transaksi").value = listData[0].TipeTransHD
        document.getElementById("input_detail_kodeperkiraan").value = listData[0].PerkiraanHd
        document.getElementById("input_detail_keteranganperkiraan").value = listData[0].NamaPerkiraanHd
        document.getElementById("input_detail_kepadaterima").value = listData[0].Note
        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_detail_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_detail_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(listData[0].Tanggal)










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}



function submitOtorisasi () {

  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();
  $.ajax({
    url: "{!! url('girodibukaspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi')
      loadAll()
      buttonCloseForm()




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

// function buttonDetail (nobukti) {
//   document.getElementById("divOto").style.display = "none";
//
//   let _token = $("#_token").val();
//   $.ajax({
//     url: "{!! url('kreditnotespdetail') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti
//
//     },
//     success: function(res) {
//       console.log(res)
//       // listData = res
//       // console.log(res)
//       if (!res.length) {
//           alertify.success('Data tidak ditemukkan')
//           // $("#form").modal('toggle')
//           return
//       }
//       // dataTableAdd = res
//
//       let rowTable = ``
//       res.forEach((item, i) => {
//               rowTable += `
//                 <tr>
//                   <td>${item.NoInv}</td>
//                   <td>${item.Keterangan}</td>
//                   <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
//                   <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
//
//                   <td>${item.kodeVls}</td>
//                   <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
//                   <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
//                   <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
//
//
//
//                 </tr>
//
//               `
//       });
//
//       document.getElementById("detailTableData").innerHTML = rowTable
//
//
//         document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
//         document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
//         document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
//         document.getElementById("input_detail_nobukti").value = res[0].NoBukti
//         document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)
//
//         $('#modalDetail').show();
//         $('.mainpage').hide();
//         $('#page3').show();
//
//
//
//
//
//
//
//
//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//       resRefresh = 0;
//     }
//
//   })
//
// }



function buttonDetail (nobukti , tipe = 'detail') {
  console.log('buttonkoreksi' , nobukti )






  refreshDataTableDetail(nobukti)


  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  $('.showhidepage3').hide();

  if (tipe == 'otorisasi') {
    $('.page3otorisasi').show();
  } else {
    $('.page3detail').show();
  }


  // $('.showhideitem').hide();
  // $('.showhidePart').hide();
  // let value = $("#input_detail_transaksi").val()
  // $(`.part${value}`).show();


  // $('#formAdd').show();
  $('.mainpage').hide();
  $('#page3').show();
}



function buttonKoreksi (nobukti ) {


  tipeform = 'edit'
  console.log('buttonKoreksi' , nobukti )
  tipeformbgc = 'edit'
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'edit'
  cleanFormAdd()



  document.getElementById("input_add_tanggal").disabled = true
  // document.getElementById("input_add_bon").disabled = true
  document.getElementById("input_add_kepadaterima").disabled = true
  document.getElementById("buttonAddListPerkiraan").disabled = true
  document.getElementById("input_add_transaksi").disabled = true

  refreshDataTable(nobukti)


  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  console.log(listData)
  console.log(listData[0].isOtorisasi1)
  if (listData[0].IsOtorisasi1 == 1) {
    alertify.warning("Bank sudah diotorisasi")
    return
  }

  $('.showhideitem').hide();
  // $('.showhidePart').hide();
  // let value = $("#input_add_transaksi").val()
  // $(`.part${value}`).show();


  $('#formAdd').show();
  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAdd (nobukti) {
  console.log('buttonAdd' , nobukti)
  tipeform = 'add'
  tipeformbgc = 'add'
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  document.getElementById("input_add_tanggal").disabled = false
  // document.getElementById("input_add_bon").disabled = false
  document.getElementById("input_add_kepadaterima").disabled = false
  document.getElementById("buttonAddListPerkiraan").disabled = false
  document.getElementById("input_add_transaksi").disabled = false




  document.getElementById("addTableData").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  // unlockFormAdd()




  $('.showhideitem').hide();
  // $('.showhideform').hide();
  $('#formAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  // setNewNoBukti()
  document.getElementById("input_add_transaksi").value = 'BBG'
  onChangeTransaksi()

  $('.mainpage').hide();
  $('#page2').show();

}

function submitAddBGC () {
  console.log('submitAddBGC')
  let tempDataPencairanGiro = []

  let jmlrecord = 0
  let urutBGC = 0
  if (tipeform == 'edit') {
    jmlrecord = 1
    // urutBGC = dataEditBGC.Urut
  }

  let xdebet = 0
  let xdebetrp = 0
  listPencairanGiro.forEach((item, i) => {

    if (document.getElementById(`giroChecklist${i}`).checked) {
      tempDataPencairanGiro.push(item)
      xdebet += Number(item.jumlah)
      xdebetrp += Number(item.jumlah) * Number(item.Kurs)
    }





  });

  if (!tempDataPencairanGiro.length) {
    alertify.warning("Tidak ada giro dipilih")
    return
  }

  let transaksi  = $("#input_add_transaksi").val()
  let choice = 'I'

  let _token = $("#_token").val()

  // let nourut = $("#input_add_nourut").val()
  console.log(tempDataPencairanGiro)

  let nobukti = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodeperkiraan = perkiraanBGC
  let tanggal = $("#input_add_tanggal").val()
  let kepadaterima = $("#input_add_kepadaterima").val()
  let kodedevisi = '01'
  let kodecustsupp = ''
  let lawan = $("#input_add_kodeperkiraan").val()
  let departemen = ''
  let keterangandet = ''
  let valas = tempDataPencairanGiro[0].KodeVls
  let kurs = tempDataPencairanGiro[0].Kurs

  // let debet = tempDataPencairanGiro[0].jumlah
  // let kurs = Number(tempDataPencairanGiro[0].jumlah) * Number(tempDataPencairanGiro[0].Kurs)
  console.log(jmlrecord)
  $.ajax({
      url: "{!! url('girodibukaspaddbgc') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        transaksi,
        choice,
        nobukti ,
        nourut,
        kodeperkiraan ,
        tanggal ,
        kepadaterima ,
        kodedevisi ,
        kodecustsupp ,
        lawan ,
        departemen ,
        keterangandet ,
        tempDataPencairanGiro,
        valas,
        kurs,
        xdebet,
        xdebetrp,
        jmlrecord

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Giro telah ditambah');
          loadAll()
          // buttonCloseForm()
          lockFormAdd()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          $('.showhideitem').hide();
          refreshDataTable(nobukti)
          $('#modalListBGC').modal("toggle")
          // $("#form").modal('toggle')
          document.getElementById("input_add_transaksi").disabled = true
        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }
        //
        // if (res == 3 ) {
        //   alertify.warning('Stok gudang tidak mencukupi');
        // }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

}



function buttonDeleteGiroKoreksi (index) {


  // tempBarangAddEdit
  // listPencairanGiro


  let xdebet = 0
  let xdebetrp = 0
  listPencairanGiroKoreksi.forEach((item, i) => {

    // if (document.getElementById(`giroChecklist${i}`).checked) {
      // tempDataPencairanGiro.push(item)
      xdebet += Number(item.Debet)
      xdebetrp += Number(item.DebetRp)
    // }

  });

  xdebet -= Number(listPencairanGiroKoreksi[index].Debet)
  xdebetrp -= Number(listPencairanGiroKoreksi[index].DebetRp)

  let tanggal = tempBarangAddEdit.Tanggal
  let nobukti = tempBarangAddEdit.NoBukti
  let urutbukticair = tempBarangAddEdit.Urut
  // let keterangancair = listPencairanGiro[index].Keterangan
  let nogiro = listPencairanGiroKoreksi[index].NoGiro
  let bank = listPencairanGiroKoreksi[index].Bank
  let _token = $("#_token").val()

  console.log({
    _token,
    nobukti,
    nogiro,
    bank,
    xdebet,
    xdebetrp,
    urutbukticair,})
  $.ajax({
    url: "{!! url('girodibukaspdeletegirokoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      nogiro,
      bank,
      xdebet,
      xdebetrp,
      urutbukticair,

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {


        alertify.warning("Berhasil menghapus giro")
        refreshDataTableGiroKoreksi(nobukti)
        refreshDataTable(nobukti)
        // $('#modalListBGCAddKoreksi').modal("toggle")
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })


}


function submitAddGiroKoreksi (index) {


  // tempBarangAddEdit
  // listPencairanGiro


  let xdebet = 0
  let xdebetrp = 0
  listPencairanGiroKoreksi.forEach((item, i) => {

    // if (document.getElementById(`giroChecklist${i}`).checked) {
      // tempDataPencairanGiro.push(item)
      xdebet += Number(item.Debet)
      xdebetrp += Number(item.DebetRp)
    // }

  });

  xdebet += Number(listPencairanGiro[index].jumlah)
  xdebetrp += Number(listPencairanGiro[index].jumlah) * Number(listPencairanGiro[index].Kurs)

  let tanggal = tempBarangAddEdit.Tanggal
  let nobukti = tempBarangAddEdit.NoBukti
  let urutbukticair = tempBarangAddEdit.Urut
  let keterangancair = listPencairanGiro[index].Keterangan
  let nogiro = listPencairanGiro[index].Nogiro
  let bank = listPencairanGiro[index].Bank
  let kredit = listPencairanGiro[index].jumlah
  let kreditrp = Number(listPencairanGiro[index].jumlah) * Number(listPencairanGiro[index].Kurs)
  let _token = $("#_token").val()

  console.log(nobukti, urutbukticair, xdebet, xdebetrp)
  $.ajax({
    url: "{!! url('girodibukaspaddgirokoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tanggal,
      nobukti,
      keterangancair,
      nogiro,
      bank,
      xdebet,
      xdebetrp,
      urutbukticair,
      kredit,
      kreditrp

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {


        alertify.success("Berhasil menambah giro")
        refreshDataTableGiroKoreksi(nobukti)
        refreshDataTable(nobukti)
        $('#modalListBGCAddKoreksi').modal("toggle")
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })


}

function buttonAddGiroKoreksi () {


  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('girodibukalistpencairangiro') !!}",
    type: "post",
    async: false,
    data: {
      _token,

    },
    success: function(res) {
      console.log(res)
      // $('.showhideitemKLedit').hide()

      listPencairanGiro = res
      let rowTable = ''

      if (listPencairanGiro.length) {
        listPencairanGiro.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="submitAddGiroKoreksi(${i})">
              <td>${item.Bank}</td>
              <td>${item.Nogiro}</td>

              <td>${formatDate(item.TglGiro)}</td>
              <td class="text-right">${formatAngka(parseFloat(item.jumlah).toFixed(2))}</td>
              <td>${item.KodeVls}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>
              <td>${item.Keterangan}</td>
          </tr>


          `
        });

        document.getElementById("tabel_data_add_list_pencairangiroaddkoreksi").innerHTML = rowTable

        $('#modalListBGCAddKoreksi').modal("toggle")
      } else {
        alertify.warning("Tidak ada giro untuk ditambah")
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })








}

function buttonAddListPerkiraanBank ( ) {
  // document.getElementById(`${idTujuanBank}`).value = perkiraan

  idTujuanBank = 'input_giro_bank'



  $("#modalBank").modal("toggle")


}

function buttonAddListPerkiraanBankKoreksi ( ) {
  // document.getElementById(`${idTujuanBank}`).value = perkiraan

  idTujuanBank = 'input_girobgt_bank'



  $("#modalBank").modal("toggle")


}



function buttonAddPickPerkiraanBank (perkiraan , keterangan ) {
  document.getElementById(`${idTujuanBank}`).value = perkiraan



  $("#modalBank").modal("toggle")


}

function buttonAddPickPerkiraanBGC (perkiraan , keterangan) {
  perkiraanBGC = perkiraan

  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('girodibukalistpencairangiro') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      bank: perkiraan

    },
    success: function(res) {
      console.log(res)
      // $('.showhideitemKLedit').hide()

      listPencairanGiro = res
      let rowTable = ''

      if (listPencairanGiro.length) {
        listPencairanGiro.forEach((item, i) => {
          rowTable += `
          <tr>
              <td><div class="form-check text-center">
                <input id="giroChecklist${i}"  class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
              </div></td>
              <td>${item.Bank}</td>
              <td>${item.Nogiro}</td>

              <td>${formatDate(item.TglGiro)}</td>
              <td class="text-right">${formatAngka(parseFloat(item.jumlah).toFixed(2))}</td>
              <td>${item.KodeVls}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>
              <td>${item.Keterangan}</td>
          </tr>


          `
        });

        document.getElementById("tabel_data_add_list_pencairangiro").innerHTML = rowTable
        $('#modalPerkiraanBGC').modal("toggle")
        $('#modalListBGC').modal("toggle")
      } else {
        alertify.warning("Tidak ada giro untuk ditambah")
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })








}

function buttonAddAddItem () {
  tipeformgiro = 'add'
  listGiroAdd = []
  console.log("buttonAddAddItem")
    let value = $("#input_add_kodeperkiraan").val();
    if(!value) {
      alertify.warning("Pilih perkiraan terlebih dahulu")
      return
    }
    let xval = $("#input_add_transaksi").val()
    if (xval == 'BBG') {
      $('#buttonSubmitAddAdd').show();
      $('#buttonSubmitAddEdit').hide();

      $('#labelAddAddItem').show();
      $('#labelAddEditItem').hide();
      // $('#rowCustsupp').hide();
      // $('#rowCustsupp').show();
      // document.getElementById("buttonSubmitAddAdd").style.display = "block";
      // // document.getElementById("buttonSubmitAddEdit").style.display = "none";
      // document.getElementById("labelAddAddItem").style.display = "block";
      // document.getElementById("labelAddEditItem").style.display = "none";
      unlockFormAddAdd()
      $('#buttonFormGiro').show();
      $('#buttonFormGiroBGT').hide();
      $('.showhideitem').hide();
      cleanFormAddAdd()
      $('#formAddAdd').show();
      // document.getElementById("buttonAddListValas").disabled = false
    } else {
      $('#modalPerkiraanBGC').modal("toggle")

    }



}

function refreshDataTableGiroKoreksi (nobukti = '' , urut = 0) {
  console.log('refreshDataTableGiroKoreksi')
  let _token = $('#_token').val()
  $.ajax({
    url: "{!! url('girodibukalistpencairangirokoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: tempBarangAddEdit.NoBukti,
      urut: tempBarangAddEdit.Urut

    },
    success: function(res) {
      console.log(res)
      // $('.showhideitemKLedit').hide()

      listPencairanGiroKoreksi = res
      let rowTable = ''

      if (listPencairanGiroKoreksi.length) {
        console.log('r1')
        listPencairanGiroKoreksi.forEach((item, i) => {
          rowTable += `
          <tr>

              <td>${item.Bank}</td>
              <td>${item.NoGiro}</td>

              <td>${formatDate(item.TglGiro)}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>

              <td class="text-right">${formatAngka(parseFloat(Number(item.Debet) -Number(item.Kredit)).toFixed(2))}</td>
              <td>${item.Kodevls}</td>
              <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.DebetRp).toFixed(2))}</td>
              <td class="text-right">${formatAngka(parseFloat(item.KreditRp).toFixed(2))}</td>

              <td class="text-right">${formatAngka(parseFloat(Number(item.DebetRp) -Number(item.KreditRp)).toFixed(2))}</td>

              <td><div class="form-check text-center">
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteGiroKoreksi(${i})"><i class="bi bi-trash"></i></button>
              </div></td>
          </tr>


          `
        });

        document.getElementById("tabel_data_add_list_pencairangiroedit").innerHTML = rowTable
        // $('#modalPerkiraanBGC').modal("toggle")
        // $('#modalListBGCEdit').modal("toggle")
      } else {
        console.log('r2')
        document.getElementById("tabel_data_add_list_pencairangiroedit").innerHTML =

        `<tr>
          <td colspan=12 class="text-center">Belum ada data</td>
        </tr>`
        // $('#modalListBGCEdit').modal("toggle")
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}

function buttonAddEditItem (i) {
  console.log('buttonAddEditItem')
  let transaksi = $("#input_add_transaksi").val()
  let _token = $("#_token").val()
  tempBarangAddEdit = listData[i]
  if (transaksi =='BBG') {
    tipeformgiro = 'edit'
    // tempBarangAddEdit = listData[i]
    console.log(tempBarangAddEdit)
    lockFormAddAdd()
    cleanFormAddAdd()
    let value = $("#input_add_transaksi").val();

    console.log(value, tempBarangAddEdit.KodeL )
    // if (value == 'BBM' && tempBarangAddEdit.KodeL == 'UHT' ) {
    //   document.getElementById("AddAddJumlah").disabled = true
    // } else {
    //   document.getElementById("AddAddJumlah").disabled = false
    // }

    document.getElementById("AddAddKodeDevisi").value = tempBarangAddEdit.Devisi
    document.getElementById("AddAddNamaDevisi").value = tempBarangAddEdit.NamaDevisi
    document.getElementById("AddAddNoDPH").value = tempBarangAddEdit.NODPH

    document.getElementById("AddAddValas").value = tempBarangAddEdit.Valas
    document.getElementById("AddAddKurs").value = parseFloat(tempBarangAddEdit.Kurs).toFixed(2)

    document.getElementById("AddAddLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.Perkiraan : tempBarangAddEdit.Lawan
    document.getElementById("AddAddKeteranganLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.NamaPerkiraan : tempBarangAddEdit.NamaLawan

    console.log(tempBarangAddEdit.Debet)
    document.getElementById("AddAddJumlah").value = parseFloat(tempBarangAddEdit.DebetRp).toFixed(2)

    xxtemp = tempBarangAddEdit.JumlahGiroRp
    document.getElementById("AddAddJumlahGiro").value = parseFloat(tempBarangAddEdit.JumlahGiroRp).toFixed(2)
    document.getElementById("AddAddKeterangan").value = tempBarangAddEdit.Keterangan
    // document.getElementById("AddAddKeteranganDetail").value = tempBarangAddEdit.KetDetail
    document.getElementById("AddAddKodeCustomer").value = tempBarangAddEdit.CustSuppL
    document.getElementById("AddAddNamaCustomer").value = tempBarangAddEdit.NamaCustSuppL

    document.getElementById("AddAddKodeDepartemen").value = tempBarangAddEdit.KodeBag
    document.getElementById("AddAddNamaDepartemen").value = tempBarangAddEdit.NamaBag


    $('#buttonSubmitAddAdd').hide();
    $('#buttonSubmitAddEdit').show();

    $('#labelAddAddItem').hide();
    $('#labelAddEditItem').show();

    $('#buttonFormGiro').hide();
    $('#buttonFormGiroBGT').show();
    $('.showhideitem').hide();
    $('#formAddAdd').show();
  } else {
    console.log(tempBarangAddEdit.NoBukti)

      console.log(tempBarangAddEdit.Urut)

      // refreshDataTableGiro()

    $.ajax({
      url: "{!! url('girodibukalistpencairangirokoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: tempBarangAddEdit.NoBukti,
        urut: tempBarangAddEdit.Urut

      },
      success: function(res) {
        console.log(res)
        // $('.showhideitemKLedit').hide()

        listPencairanGiroKoreksi = res
        let rowTable = ''

        if (listPencairanGiroKoreksi.length) {
          console.log('e1')
          listPencairanGiroKoreksi.forEach((item, i) => {
            rowTable += `
            <tr>

                <td>${item.Bank}</td>
                <td>${item.NoGiro}</td>

                <td>${formatDate(item.TglGiro)}</td>
                <td class="text-right">${formatAngka(parseFloat(item.Debet).toFixed(2))}</td>
                <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>

                <td class="text-right">${formatAngka(parseFloat(Number(item.Debet) -Number(item.Kredit)).toFixed(2))}</td>
                <td>${item.Kodevls}</td>
                <td class="text-right">${formatAngka(parseFloat(item.Kurs).toFixed(2))}</td>
                <td class="text-right">${formatAngka(parseFloat(item.DebetRp).toFixed(2))}</td>
                <td class="text-right">${formatAngka(parseFloat(item.KreditRp).toFixed(2))}</td>

                <td class="text-right">${formatAngka(parseFloat(Number(item.DebetRp) -Number(item.KreditRp)).toFixed(2))}</td>

                <td><div class="form-check text-center">
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteGiroKoreksi(${i})"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>


            `
          });

          document.getElementById("tabel_data_add_list_pencairangiroedit").innerHTML = rowTable
          // $('#modalPerkiraanBGC').modal("toggle")
          $('#modalListBGCEdit').modal("toggle")
        } else {
          console.log('e2')
          document.getElementById("tabel_data_add_list_pencairangiroedit").innerHTML =

          `<tr>
            <td colspan=12 class="text-center">Belum ada data</td>
          </tr>`
          $('#modalListBGCEdit').modal("toggle")
        }





      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
      }

    })

  }



}


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function loadAll () {
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  $.ajax({
    url: "{!! url('girodibukaloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal,
      tglakhir,
    },
    success: function(res) {
      lastTabelRows = res.tempOutstanding || []
      reinitTabel()
    }})

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('girodibukadetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])
        
        // console.log(res[0][0].IsOtorisasi1)

      }
    })
    
    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=10) {
      let tempArray = dataPrint.slice(i,i+10)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];

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
        hdr = `<table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
            <!-- JUDUL -->
              <tr>
                <td colspan="4" rowspan="2" style="border:1px solid; text-align:center; font-weight:bold; font-size:18px;">
                  GIRO DITERIMA
                </td>
                <td style="border:1px solid; width:15%;">No. Bukti</td>
                <td style="border:1px solid; width:25%;">${dataPrint[0].NoBukti}</td>
              </tr>

              <!-- TANGGAL -->
              <tr>
                <td style="border:1px solid;">Tanggal</td>
                <td style="border:1px solid;">${tanggalOnly}</td>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 10%">KODE</td>
                    <td rowspan="2" class="text-center" style="width: 20%">BANK</td>
                    <td rowspan="2" class="text-center" style="width: 50%">KETERANGAN</td>
                    <td rowspan="2" class="text-center" style="width: 20%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalDebet = 0;
    let grandTotalKredit = 0;

    dataPrint.forEach(item => {

      if (item.DebetRp) {
        grandTotalDebet += Number(item.DebetRp) || 0;
      }

    });
    // end
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
        item.forEach((itemSub, j) => {
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 1%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 10%;  ">${itemSub.NoGiro}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Bank}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.Keterangan}</td>
         <td style="width: 20%; text-align: right;">
            ${itemSub.DebetRp 
              ? Number(itemSub.DebetRp).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
          </tr>`;
        }

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>
         

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>
          

         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>

         </div>


         <div class="footer-print-date">
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

function buttonBatalOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi Bank ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('girodibukaspbatalotorisasi') !!}",
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
}


</script>




@endsection
