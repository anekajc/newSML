@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Perintah Retur Jual')
@section('title', 'SML - Perintah Retur Jual')

@endsection

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Modal pemilih (Customer/No Invoice/No Beli/Barang) disamakan persis dengan modal
     pemilih menu Kas - lihat public/css/picker-kas.css, pola yang sama seperti
     so.blade.php / retursuratjalan.blade.php. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

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
.po-toolbar-search {
  display: flex;
  align-items: center;
  gap: 10px;
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

#tabel td:first-child .btn-primary,
#tabel2 td:first-child .btn-primary,
#addTable td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-danger,
#tabel2 td:first-child .btn-danger,
#addTable td:last-child .btn-danger {
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
  animation: prjMunculLoading .34s ease-out both;
}

@keyframes prjMunculLoading {
  0%, 45% { opacity: 0; }
  100% { opacity: 1; }
}

/* Hide action buttons until the row is hovered, port 1:1 dari pola
   .action-buttons-wrap milik master (public/css/tableMaster2.css) --
   scoped ke #tabel (satu-satunya tabel di halaman ini yang punya Actions). */
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

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection


@section('content')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">

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

  {{-- Filter modal: so.blade.php's exact modalFilterPO markup/classes, adapted to
       PRJ's own single Status dropdown (otorisasi + proses state combined -- PRJ
       has no Tipe Bayar concept so that field is dropped). --}}
  <div class="modal fade rt-filter" id="modalFilter">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-funnel"></i>
            Filter Data
            <span class="rt-active-badge" id="prjFilterBadge">0 aktif</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilter').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-group-label">Status</div>
            <div>
              <label class="rt-field-label" for="input_filterstatus">Status PRJ</label>
              <select class="rt-native" id="input_filterstatus">
                <option value=0 selected>Semua Status</option>
                <option value=1>Belum Diproses</option>
                <option value=2>Proses Sebagian</option>
                <option value=3>Selesai</option>
              </select>
            </div>
          </div>

          <div class="rt-section">
            <div class="rt-group-label">Otorisasi</div>
            <div>
              <label class="rt-field-label" for="input_filteroto">Status Otorisasi</label>
              <select class="rt-native" id="input_filteroto">
                <option value=0 selected>Semua</option>
                <option value=1>Belum Otorisasi</option>
                <option value=2>Sudah Otorisasi</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="rt-reset-link" onclick="prjResetFilterFields()">Reset semua</button>
          <div class="rt-footer-buttons">
            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
              onclick="$('#modalFilter').modal('hide')">Batal</button>
            <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterPRJ(); $('#modalFilter').modal('hide');">Terapkan</button>
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
          <input type="date" onchange="onChangePeriodePRJ()" class="po-filter-inp" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodePRJ()" class="po-filter-inp" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="prjSearch1" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap">
          <label for="prjLen1">Tampilkan</label>
          <select id="prjLen1" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" onclick="$('#modalFilter').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
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

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      {{-- <h2>Form PRJ</h2> --}}
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>

  <div id= "modalAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div id="formBsGrid" class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-12">
              <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Customer</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">
          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
              <button class="btn btn-chip-biru btn-sm" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-search"></i></button>
            </div>
          </div>
          </div>
          </div>


          <div class="col-md-12" style="margin-top:-10px">
            <div class="form-group">
              <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_add_namacustomer"  disabled></textarea>
            </div>
          </div>
          <div class="col-md-12 mb-3" style="margin-top:-10px">

            <textarea  style="width: 100%; height: 75px !important; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
          </div>

          </div>
        </div>
        <div class="col-md-3">
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

        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Invoice</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div>
          </div> -->

            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="hidden" class="form-control" id="input_add_flagtipe" value="">
                <input type="hidden" class="form-control" id="input_add_ppn" value="">
                <input type="text" class="form-control" id="input_add_noinvoice" value="" disabled >
                <button class="btn btn-chip-biru btn-sm" id="buttonAddListNoInvoice" onclick="buttonAddListNoInvoice()"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12" style="margin-top:-19px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Catatan</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none; height: 75px !important;" rows=3  class="form-control" id="input_add_catatan"  ></textarea>
              </div>
            </div>

          </div>
        </div>
          </div>
        </div>
        <div class="col-md-3">
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
        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>


            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_noso" value=""  disabled>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <!-- <div class="col-md-3 text-right">
              <div class="form-group">
            </div>
          </div> -->
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_add_gudang" value="" disabled >
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListGudang" onclick="buttonAddListGudang()"><i class="bi bi-plus"></i></button> -->
                <!-- <input type="hidden" class="form-control text-center" id="input_add_kodesales" value="" disabled > -->
              </div>
            </div>
          </div>
        </div>


          </div>
        </div>

        <div class="col-3">
          <div class="row">
            <div class="col-12">

            </div>

          </div>


        </div>
        <div class="col-3">
          <div class="row">

          </div>
        </div>

        <div class="col-4">
          <div class="row">
            <div class="col-12">
              <div class="row">

              </div>
            </div>



            <div class="col-12">
              <div class="row">



              </div>

            </div>




          </div>

        </div>

        <div class="col-4">
          <div class="row">
            <div class="col-12">
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



<div class="container-fluid">
  <hr/>

</div>



  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="data-table">
          <thead class="text-center">
            <tr>
              <!-- <th style="padding: 4px 12px;" scope="col">Gudang</th> -->
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
              <th style="padding: 4px 12px;" scope="col">Sat Produk</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <!-- <th style="padding: 4px 12px;" scope="col">Qty1</th>
              <th style="padding: 4px 12px;" scope="col">Sat1</th>
              <th style="padding: 4px 12px;" scope="col">Qty2</th>
              <th style="padding: 4px 12px;" scope="col">Sat2</th> -->

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


  <div class="col-md-12 mt-2 text-right">
  <button type="button" class="btn btn-chip-biru" onclick="buttonAddAddItem()" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >Tambah Item</button>
</div>


  <div id="formAddAdd" class="container-fluid showhideitem">
    <div id="formBsGrid">
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
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>

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
          <button class="btn btn-chip-biru btn-sm" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-search"></i></button>

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
        <select id="AddAddInputNosat" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangeQty()">
          <option value=0 selected></option>
        </select>
      </div>
    </div>
  </div>
  <!-- <div class="col-md-12" style="margin-top: -10px"> -->
    <input id="AddAddInputQty1" type="hidden" value='0.00' class="form-control text-right" disabled style="width: 75%">

    <input id="AddAddInputSat1" type="hidden" value='PCS' class="form-control text-center" disabled style="width: 25%">


  <!-- <div class="row">

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty 1</label>
      </div>
      </div>

      <div class="input-group col-md-4">


      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty 2</label>
      </div>
      </div>

      <div class="input-group col-md-4">



      </div>
</div> -->
<input id="AddAddInputQty2" type="hidden" value='0.00' class="form-control text-right" disabled style="width: 75%">

<input id="AddAddInputSat2" type="hidden" value='BOX' class="form-control text-center" disabled style="width: 25%">

<!-- </div> -->




      <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
  <div class="input-group-append">
    <span class="input-group-text">@example.com</span>
  </div> -->
  <div class="col-md-12" style="margin-top: -10px">
    <div class="row">



      <div class="col-md-2">
        <div class="form-group">
        <label>Retur Supp</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-4">
        <select id="AddAddReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePPN('ppn' , 'input_add_tipeppn')">
          <option value=0 selected>Tidak</option>
          <option value=1 >Ya</option>
        </select>
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div><div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-4">
        <input id="AddAddKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>


      <!-- <div class="col-md-6">

      </div> -->
    </div>
  </div>
  <div class="col-md-12" style="margin-top: -10px">
    <div class="row">



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
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
    </div>

  </div>

</div>




    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
    </div>
  </div>
</div>
<!-- </div> -->


<!-- ADD EDIT -->

<div id="formAddEdit" class="container-fluid showhideitem">
  <!-- <div class="line"></div> -->
  <!-- <div class="row"> -->

  <div class="col-12">


  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
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
        <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
        <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->
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
        <input id="AddEditUrutBeli" type="hidden" class="form-control" >
        <input id="AddEditNoBeli" type="text" class="form-control" disabled>
        <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-plus"></i></button> -->

      </div>
      <!-- <input id="AddEditKodeGudang" type="hidden" class="form-control" disabled> -->
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
      <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
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
      <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQtyEdit()">
    </div>

    <div class="col-md-2">
      <div class="form-group">
      <label>Satuan</label>
    </div>
    </div>

    <div class="col-md-4">
      <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
      <select id="AddEditInputNosat" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangeQtyEdit()">
        <option value=0 selected></option>
      </select>
    </div>
  </div>
</div>

<input id="AddEditInputQty1" type="hidden" value='0.00' class="form-control text-right" disabled style="width: 75%">

<input id="AddEditInputSat1" type="hidden" value='PCS' class="form-control text-center" disabled style="width: 25%">



<!-- <div class="col-md-12" style="margin-top: -10px"> -->
<!-- <div class="row">
    <div class="col-md-2">
      <div class="form-group">
      <label>Qty 1</label>
    </div>
    </div>

    <div class="input-group col-md-4">


    </div>

    <div class="col-md-2">
      <div class="form-group">
      <label>Qty 2</label>
    </div>
    </div>

    <div class="input-group col-md-4">


    </div>
  </div> -->
<!-- </div> -->

<input id="AddEditInputQty2" type="hidden" value='0.00' class="form-control text-right" disabled style="width: 75%">

<input id="AddEditInputSat2" type="hidden" value='BOX' class="form-control text-center" disabled style="width: 25%">





    <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
<div class="input-group-append">
  <span class="input-group-text">@example.com</span>
</div> -->

<div class="col-md-12" style="margin-top: -10px">
<div class="row">

    <div class="col-md-2">
      <div class="form-group">
      <label>Retur Supp</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

        <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
      </div> -->
    <div class="col-md-4">
      <select id="AddEditReturSupp" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangePPN('ppn' , 'input_add_tipeppn')">
        <option value=0 selected>Tidak</option>
        <option value=1 >Ya</option>
      </select>
      <!-- <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled> -->
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
      <input id="AddEditKeterangan" type="text" class="form-control" >
      <!-- <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>
  </div>
</div>
  </div>
</div>


<!-- <div class="col-6 ">
  <div class="row">



  </div> -->
<!-- </div> -->





<div class="col-6 mt-2">
  <div class="row">


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Simpan</button>
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
    </div>

  </div>
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>
</div>
<!-- </div> -->



    </div>

    <!-- <div class="row "> -->

<!-- </div> -->









  </div>


  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row">
      <div class="col-8 text-left">
        {{-- <h2>Detail PRJ</h2> --}}
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>

    <div id= "modalDetail" class="">

    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12">
                <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div>
          </div> -->
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
              </div>
            </div>
            </div>
            </div>


            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_detail_namacustomer"  disabled></textarea>
              </div>
            </div>
            <div class="col-md-12 mb-3" style="margin-top:-10px">

              <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatcustomer"  disabled></textarea>
            </div>

            </div>
          </div>
          <div class="col-md-3">
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

          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Invoice</label>
                </div>
              </div>
              <!-- <div class="col-3 text-right">
                <div class="form-group">
              </div>
            </div> -->


              <div class="col-md-8">
                <div class="form-group input-group">
                  <input type="hidden" class="form-control" id="input_detail_flagtipe" value=""  >
                  <input type="hidden" class="form-control" id="input_detail_ppn" value=""  >
                  <input type="text" class="form-control" id="input_detail_noinvoice" value="" disabled >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12" style="margin-top:-19px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>

              <div class="col-md-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_catatan"  disabled></textarea>
                </div>
              </div>
            </div>
          </div>
            </div>
          </div>

          <div class="col-md-3">
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
          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>No SO</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_noso" value=""  disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Gudang</label>
                </div>
              </div>

              <div class="col-md-8">
                <div class="form-group input-group">
                  <input type="text" class="form-control" id="input_detail_gudang" value="" disabled >
                </div>
              </div>
            </div>
          </div>


            </div>
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col-12">

              </div>

            </div>


          </div>
          <div class="col-3">
            <div class="row">

            </div>
          </div>

          <div class="col-4">
            <div class="row">
              <div class="col-12">
                <div class="row">

                </div>
              </div>



              <div class="col-12">
                <div class="row">



                </div>

              </div>




            </div>

          </div>

          <div class="col-4">
            <div class="row">
              <div class="col-12">
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



  <div class="container-fluid">
    <hr/>

  </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="data-table">
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">Sat Produk</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>


              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=6 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
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



  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
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
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_customer" class="data-table" style="overflow:auto; " >
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Kota</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_customer" class="text-left" >

                <tr class="pick-row">

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
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
      </div>
      </div>

      <div id= "modalAddListNoInvoice" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">No Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_noinvoice" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                  <th style="padding: 4px 12px;" scope="col">No SO</th>
                  <th style="padding: 4px 12px;" scope="col">Gudang</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_noinvoice" class="text-left" >

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
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_nobeli" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">No Bukti</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_nobeli" class="text-left" >

                <tr class="pick-row">

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
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>

                  <th style="padding: 4px 12px;" scope="col">Qnt Sisa</th>

                  <th style="padding: 4px 12px;" scope="col">Satuan</th>

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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
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
     di purchaseOrder.blade.php / marketing/so.blade.php / retursuratjalan.blade.php. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

// let tempNoBukti = ''



let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''
let xppn = 0

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel/poInitReportTableSekali milik
 * purchaseOrder.blade.php, sama seperti so.blade.php/invoicejasa.blade.php/
 * cetaktandaterima.blade.php. Endpoint persistensinya saveheadertable/
 * getheadertable (HeaderTableController). Disederhanakan jadi satu tabel saja
 * (urut 1) setelah tab Belum/Sudah Otorisasi digabung jadi satu daftar dengan
 * filter Status PRJ (lihat modalFilter di section('content')), sama seperti
 * so.blade.php sudah tidak punya tab otorisasi terpisah untuk tab SO-nya.
 */
let prjCart = []
const PRJ_HREF = 'perintahreturjual'
const PRJ_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const PRJ_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function prjPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function prjDefaultCart () {
  return [
    ['NOBUKTI',      'No. Bukti',   1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',     1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Cust',   1, 'varchar', 0, 0],
    ['NoRPJ',        'No Invoice',  1, 'varchar', 0, 0],
    ['NOSO',         'No SO',       1, 'varchar', 0, 0],
    ['IDUser',       'User',        1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto1',   1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto1',    1, 'date',    0, 0],
    ['xstatus',       'Status',      1, 'varchar', 0, 0]
  ]
}

function prjBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      PRJ_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

window.g_href = PRJ_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = prjCart || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(PRJ_TIPE_KODE[c[3]] ?? 0)
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
      href     : PRJ_HREF,
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
      href   : PRJ_HREF,
      urut   : 1,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        prjCart = prjBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        prjCart = prjDefaultCart()
        window.gcart_header = prjCart
        window.doSimpanHeader()
      }
      window.gcart_header = prjCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      prjCart = prjDefaultCart()
      window.gcart_header = prjCart
    }
  })
}

let prjRtSudahInit = false
function prjInitReportTableSekali () {
  if (prjRtSudahInit || typeof ReportTable === 'undefined') { return }
  prjRtSudahInit = true

  ReportTable.init({ table : '#tabel', bar : '#rtBarTabel', onChange : reinitTabel })

  let prjGuardUlangKlik = false;
  let thead = document.querySelector('#tabel thead')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (prjGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      prjGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      prjGuardUlangKlik = false
    }, true)
  }
}

function tulisTheadHeaderPRJ (cols) {
  let thead = document.querySelector('#tabel thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

// Belum=merah, Sebagian=kuning, Selesai=biru -- sama seperti badge status di
// master/newsetpemakai.blade.php, pakai .sp-badge yang sudah dimuat lewat
// po-table-header.css (lihat juga poBadgeStatus() di purchaseOrder.blade.php).
const PRJ_BADGE_STATUS = {
  'Belum':    'is-inactive',
  'Sebagian': 'is-supervisor',
  'Selesai':  'is-user',
}

function prjValueCell (row, col) {
  let raw = prjPickCI(row, col[0]);
  let type = col[3];

  if (col[0] === 'xstatus') {
    let kelas = PRJ_BADGE_STATUS[raw] || '';
    return '<td><span class="sp-badge ' + kelas + '">' + (raw || '') + '</span></td>';
  }

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

// Actions gabungan Belum/Sudah Otorisasi (dulu tabelActionsCell vs
// tabel2ActionsCell terpisah per tab) -- cetak nota/berita acara cuma relevan
// setelah otorisasi, sama seperti tabel2ActionsCell dulu.
function tabelActionsCell (row) {
  let nobukti = prjPickCI(row, 'NOBUKTI');
  let isOto = Number(prjPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi " onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Cetak Nota" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Cetak Berita Acara" onclick="submitPrintBA(\'' + nobukti + '\')"><i class="bi bi-printer-fill"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (prjCart.length ? prjCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += prjValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderPRJ(cols);
}

let lastTabelRows = []
let prjPanjangHalaman = 10

function prjIkatSearch () {
  let input = document.getElementById('prjSearch1')
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

function prjIkatPanjangHalaman () {
  let sel = document.getElementById('prjLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(prjPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    prjPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().page.len(prjPanjangHalaman).draw()
    }
  })
}

const PRJ_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({
      dom: PRJ_DOM_STRING,
      lengthChange: false,
      pageLength: prjPanjangHalaman,
      paging: true,
      order: [[1, 'asc']],
      ordering: false,
    });
    prjIkatSearch();
    prjIkatPanjangHalaman();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable () {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    window.doSetHeader(1, true)
    reinitTabel()
    alertify.success('Kolom telah direset ke tampilan default')
  }, function () {})
}

// Filter modal ("Status PRJ") + periode -- port 1:1 dari
// soResetFilterFields()/buttonFilterSO()/onChangePeriodeSO() milik so.blade.php,
// disederhanakan (PRJ tidak punya field Tipe Bayar).
function prjResetFilterFields () {
  $('#input_filterstatus').val('0');
  $('#input_filteroto').val('0');
}

function prjUpdateFilterBadge () {
  let aktif = 0
  if (Number($('#input_filterstatus').val()) !== 0) { aktif++ }
  if (Number($('#input_filteroto').val()) !== 0) { aktif++ }
  $('#prjFilterBadge').text(aktif + ' aktif')
}

function buttonFilterPRJ () {
  let _token = $("#_token").val()
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  let filterstatus = $("#input_filterstatus").val()
  let filteroto = $("#input_filteroto").val()

  prjUpdateFilterBadge()

  $.ajax({
    url: "{!! url('perintahreturjualloadall') !!}",
    type: "get",
    async: false,
    data: { _token, tglawal, tglakhir, filterstatus, filteroto },
    success: function (res) {
      lastTabelRows = res.tempOutstanding;
      reinitTabel();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangePeriodePRJ () {
  let awal = $("#input_tanggalawal").val()
  let akhir = $("#input_tanggalakhir").val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  buttonFilterPRJ()
}

$(document).ready(function(){

      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      prjInitReportTableSekali();

        $("#tabel_add_list_customer").DataTable({
          "lengthChange": false,
            "paging": false ,
        });
        $("#tabel_add_list_noinvoice").DataTable({
          "lengthChange": false,
            "paging": false ,
        });
        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
      });

      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });




  //   formAddListItem
});



function formatAngkaX (angka) {
  if (Number(angka) == 0) {
    return '0.00'
  } else {

    return formatAngka(parseFloat(angka).toFixed(2))
  }

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

function setNewNoBukti (xval = 1) {
  $.ajax({
    url: "{!! url('perintahreturjualspnobukti') !!}",
    type: "get",
    async: false,
    data: {
      ppn: xval
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

  document.getElementById("AddAddReturSupp").value = 0

  document.getElementById("AddAddKeterangan").value = ''
  tempBarangAddAdd = {}
  // AddAddReturSupp

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
  let urut = barang.URUT

  let jmlrecord = 1

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
  let retursupp = $("#AddEditReturSupp").val()
  let tanggal = $("#input_add_tanggal").val()
  let noso = $("#input_add_noso").val()
  let gudang = $("#input_add_gudang").val()

  let nobeli = $("#AddEditNoBeli").val()
  let urutbeli = $("#AddEditUrutBeli").val()
  let namabrg = $("#AddEditNamaBrg").val();
  let kodebrg = $("#AddEditKodeBrg").val();
  let qnt = $("#AddEditInputQty").val();

  let nosat = $("#AddEditInputNosat").val();
  let isi = 0
  if (nosat == 1) {
    isi = barang.ISI1
  } else if (nosat == 2) {
    isi = barang.ISI2
  }
  let urutinvoice = barang.UrutSC
  let qnt1 = $("#AddEditInputQty1").val();
  let sat1 = $("#AddEditInputSat1").val();
  let qnt2 = $("#AddEditInputQty2").val();
  let sat2 = $("#AddEditInputSat2").val();
  let ketdet = $("#AddEditKeterangan").val();

  console.log(namabrg, kodebrg, qnt)
  if (Number(qnt) <= 0) {
    alertify.warning("qnt <= 0")
    return
  }
  if (Number(qnt) * Number(isi) > Number(barang.IsiSisaView) * (Number(barang.QntSisaView) + Number(barang.QNT)) ) {
    alertify.warning("Melebihi qnt sisa")
    return
  }

  // return
  $.ajax({
    url: "{!! url('perintahreturjualspadd') !!}",
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



        // tipeform = 'edit'
        // lockFormAdd()
        $('.showhideitem').hide();
        // $('.showhideform').hide();
        // $('#modalAdd').show();
        loadAll()
        refreshDataTableAdd(nobukti)



        // lockFormAdd()
        // $('.showhide').hide();
        // $('#buttonSubmitSaveHeader').show();
        // unlockFormAdd()
        // tipeform = 'edit'
        // document.getElementById("buttonAddListCustomer").disabled = true
        // document.getElementById("buttonAddListNoInvoice").disabled = true
        // $('.showhideitem').hide();
        // // $('#divhargaterakhir').hide();
        // cleanFormAddAdd()
        //
        // refreshDataTableAdd(nobukti)

        alertify.success('Berhasil menambah item')
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

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = barang.NOBUKTI
        let urut = barang.URUT
        console.log(nobukti, urut)
        $.ajax({
          url: "{!! url('perintahreturjualspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            nourut : '',
            tanggal : '',
            noinvoice : '',
            kodecustsupp : '',
            catatan :'',
            urut,
            urutinvoice :'',
            kodebrg:'',
            qnt:0,
            qnt1:0,
            qnt2:0,
            sat1:'',
            nosat:0,
            isi:0,
            jmlrecord:1,
            namabrg:'',
            kodegdg:'',
            flagtipe:0,
            ppn:0,
            noso:'',
            retursupp:0,
            sat2:'',
            ketdet:'',
            nobeli:'',
            urutbeli:0

          },
          success: function(res) {
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();
            loadAll()
            refreshDataTableAdd(nobukti)

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
  let retursupp = $("#AddAddReturSupp").val()
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
  if (Number(qnt) <= 0) {
    alertify.warning("Qty kurang dari 0")
    return
  }

  if (Number(qnt) * Number(isi) > Number(barang.QntSisa) * Number(barang.Isi) ) {
    alertify.warning("Melebihi qnt sisa")
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
    url: "{!! url('perintahreturjualspadd') !!}",
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
        setNewNoBukti(xppn)
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
    alertify.warning("Pilih customer & invoice terlebih dahulu")
    return
  }

  if ($.fn.DataTable.isDataTable('#tabel_add_list_barang')) { $('#tabel_add_list_barang').DataTable().destroy() }
  $.ajax({
    url: "{!! url('perintahreturjuallistbarang') !!}",
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
        <td class="text-right">${parseFloat(item.QntSisa).toFixed(2)}</td>
        <td>${item.Satuan ?  item.Satuan : ''}</td>

        </tr>`
      });






      document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
      pickerKasInit('tabel_add_list_barang', { order: [[0, 'asc']] })
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

  if ($.fn.DataTable.isDataTable('#tabel_add_list_nobeli')) { $('#tabel_add_list_nobeli').DataTable().destroy() }
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
      rowTable += `<tr class="pick-row" onclick="buttonAddPickNoBeli('-' , 0)">

      <td>-</td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickNoBeli('${item.NOBUKTI}' ,${item.urut} )">
        <td>${item.NOBUKTI}</td>

        </tr>`
      });





      document.getElementById("tabel_data_add_list_nobeli").innerHTML = rowTable
      pickerKasInit('tabel_add_list_nobeli', { order: [[0, 'asc']] })
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
    alertify.warning("Pilih customer terlebih dahulu")
    return
  }

  if ($.fn.DataTable.isDataTable('#tabel_add_list_noinvoice')) { $('#tabel_add_list_noinvoice').DataTable().destroy() }
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
        <tr class="pick-row" onclick="buttonAddPickNoInvoice('${item.NOBUKTI}' , '${item.NoSO}' , '${item.KODEGDG}', ${item.flagtipe}, ${item.ppn})">

        <td>${item.NOBUKTI}</td>
        <td>${formatDate(item.TANGGAL)}</td>
        <td>${item.NoSO}</td>
        <td>${item.NAMAGDG ? item.NAMAGDG : '' }</td>

        </tr>`
      });




      document.getElementById("tabel_data_add_list_noinvoice").innerHTML = rowTable
      pickerKasInit('tabel_add_list_noinvoice', { order: [[0, 'asc']] })
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
  if ($.fn.DataTable.isDataTable('#tabel_add_list_customer')) { $('#tabel_add_list_customer').DataTable().destroy() }
  $.ajax({
    url: "{!! url('perintahreturjuallistcustomer') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}' , ${item.PPN})">

        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.NamaKota}</td>

        </tr>`
      });




      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      pickerKasInit('tabel_add_list_customer', { order: [[0, 'asc']] })
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

function buttonAddPickCustomer (kode, nama , alamat,ppn) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat,ppn)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat
  document.getElementById("input_add_noinvoice").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_gudang").value = ''
  $('.showhideitem').hide();

  if (ppn && Number(ppn) == 1) {
    setNewNoBukti(1)
    xppn =1

  } else {
    setNewNoBukti(0)
    xppn =0

  }
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
  document.getElementById("input_add_gudang").value = kodegdg && kodegdg != 'null' ? kodegdg : ''
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
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggal").disabled = true


  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("buttonAddListNoInvoice").disabled = true

}

function refreshDataTableAdd (NOBUKTI = "") {

    console.log('refreshDataTableAdd' , NOBUKTI)

    if (!NOBUKTI) {

    } else {
      let _token  = $("#_token").val()
      $.ajax({
        url: "{!! url('perintahreturjualgetdetail') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti: NOBUKTI
        },
        success: function(res) {
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

              // <td>${item.KodeGdg}</td>
              rowTable += `<tr>
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>

              <td>${item.NAMAPRODUK}</td>
              <td>${item.SATX ? item.SATX : item.SAT}</td>
              <td class="text-right">${item.QNT ? formatAngkaX(item.QNT) : '0.00'}</td>
              <td>${item.SAT}</td>

              <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
               <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>
              </tr>`
            });

            // <td class="text-right">${item.QNT1 ? formatAngkaX(item.QNT1) : '0.00'}</td>
            // <td>${item.SAT_1}</td>
            //
            // <td class="text-right">${item.QNT2 ? formatAngkaX(item.QNT2) : '0.00'}</td>
            // <td>${item.SAT_2}</td>

            // if(!dataTableAdd.length) {
            //   rowTable = `<tr>
            //   <td class="text-center" colspan="8">Belum ada barang</td>
            //   </tr>`
            // }
            document.getElementById("addTableData").innerHTML = rowTable

            document.getElementById("input_add_kodecustomer").value = dataBarang[0].KodeCustSupp
            document.getElementById("input_add_namacustomer").value = dataBarang[0].NAMACUSTSUPP
            document.getElementById("input_add_alamatcustomer").value = dataBarang[0].ALAMAT1
            document.getElementById("input_add_noinvoice").value = dataBarang[0].NoRPJ
            document.getElementById("input_add_noso").value = dataBarang[0].NOSO
            document.getElementById("input_add_gudang").value = dataBarang[0].KodeGdg
            document.getElementById("input_add_catatan").value = dataBarang[0].Catatan
            document.getElementById("input_add_flagtipe").value = dataBarang[0].FlagTipe
            document.getElementById("input_add_ppn").value = dataBarang[0].TipePPN

            document.getElementById("input_add_nobukti").value = dataBarang[0].NOBUKTI
            document.getElementById("input_add_nourut").value = dataBarang[0].NOURUT
            //
            // document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
            // document.getElementById("input_add_namapelanggan").value = dataHeaderAdd.NamaCust
            // document.getElementById("input_add_kodepelanggan").value = dataHeaderAdd.KodeCUST
            // document.getElementById("input_add_alamatpelanggan").value = dataHeaderAdd.ALAMAT
            // document.getElementById("input_add_kodesales").value = dataHeaderAdd.kodesls
            // document.getElementById("input_add_namasales").value = dataHeaderAdd.NamaSls
            // document.getElementById("input_add_kodepic").value = dataHeaderAdd.kodePF
            // document.getElementById("input_add_namapic").value = dataHeaderAdd.NamaPF
            // document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
            // document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
            //
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
      url: "{!! url('perintahreturjualgetdetail') !!}",
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
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>

              <td>${item.NAMAPRODUK}</td>
              <td>${item.SATX ? item.SATX : item.SAT}</td>
              <td class="text-right">${item.QNT ? parseFloat(item.QNT).toFixed(2) : '0.00'}</td>
              <td>${item.SAT}</td>
              </tr>`
            });

            // if(!dataTableAdd.length) {
            //   rowTable = `<tr>
            //   <td class="text-center" colspan="8">Belum ada barang</td>
            //   </tr>`
            // }
            document.getElementById("detailTableData").innerHTML = rowTable

            document.getElementById("input_detail_kodecustomer").value = res[0].KodeCustSupp
            document.getElementById("input_detail_namacustomer").value = res[0].NAMACUSTSUPP
            document.getElementById("input_detail_alamatcustomer").value = res[0].ALAMAT1
            document.getElementById("input_detail_noinvoice").value = res[0].NoRPJ
            document.getElementById("input_detail_noso").value = res[0].NOSO
            document.getElementById("input_detail_gudang").value = res[0].KodeGdg
            document.getElementById("input_detail_catatan").value = res[0].Catatan
            document.getElementById("input_detail_flagtipe").value = res[0].FlagTipe
            document.getElementById("input_detail_ppn").value = res[0].TipePPN

            document.getElementById("input_detail_nobukti").value = res[0].NOBUKTI
            document.getElementById("input_detail_nourut").value = res[0].NOURUT

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

  tipeform = 'edit'
  lockFormAdd()
  $('.showhideitem').hide();
  // $('.showhideform').hide();
  // $('#modalAdd').show();
  refreshDataTableAdd(nobukti)

  if (dataBarang[0].IsOtorisasi1 == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

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

  document.getElementById("addTableData").innerHTML = `<td colspan=7 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  unlockFormAdd()
  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  // setNewNoBukti()

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
  document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.KODEBRG
  document.getElementById("AddEditReturSupp").value = tempBarangAddEdit.FlagKembali
  document.getElementById("AddEditNoBeli").value = tempBarangAddEdit.nobeli
  document.getElementById("AddEditUrutBeli").value = tempBarangAddEdit.urutbeli
  document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NAMABRG
  document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.QNT).toFixed(2)
  document.getElementById("AddEditInputQty1").value = parseFloat(tempBarangAddEdit.QNT1).toFixed(2)
  document.getElementById("AddEditInputQty2").value = parseFloat(tempBarangAddEdit.QNT2).toFixed(2)
  document.getElementById("AddEditInputSat1").value = tempBarangAddEdit.SAT_1
  document.getElementById("AddEditInputSat2").value = tempBarangAddEdit.SAT_2

  document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.KetDetail

  let selectOption = ''
  if (tempBarangAddEdit.SAT_1) {
    selectOption += `<option value=1 ${tempBarangAddEdit.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddEdit.SAT_1}</option>`
  }
  if (tempBarangAddEdit.SAT_2) {
    selectOption += `<option value=2 ${tempBarangAddEdit.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddEdit.SAT_2}</option>`
  }
  document.getElementById("AddEditInputNosat").innerHTML = selectOption

  // let retursupp = $("#AddEditReturSupp").val()
  // let nobeli = $("#AddEditNoBeli").val()
  // let urutbeli = $("#AddEditUrutBeli").val()
  // let namabrg = $("#AddEditNamaBrg").val();
  // let kodebrg = $("#AddEditKodeBrg").val();
  // let qnt = $("#AddEditInputQty").val();
  // // isi
  // let qnt1 = $("#AddEditInputQty1").val();
  // let sat1 = $("#AddEditInputSat1").val();
  // let qnt2 = $("#AddEditInputQty2").val();
  // let sat2 = $("#AddEditInputSat2").val();
  // let ketdet = $("#AddEditKeterangan").val();
  //
  // let nosat = $("#AddEditInputNosat").val();

  $('.showhideitem').hide();
  $('#formAddEdit').show();

}

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function loadAll () {

  let _token = $("#_token").val();
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  let filterstatus = $("#input_filterstatus").val()
  let filteroto = $("#input_filteroto").val()

  $.ajax({
    url: "{!! url('perintahreturjualloadall') !!}",
    type: "get",
    async: false,
    data: { _token, tglawal, tglakhir, filterstatus, filteroto },
    success: function(res) {

      lastTabelRows = res.tempOutstanding;
      reinitTabel();

    }})

}


function buttonBatalOtorisasi (nobukti) {
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
          url: "{!! url('perintahreturjualspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            console.log(res)
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

function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('perintahreturjualspotorisasi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {
        console.log(res)
        alertify.success('Berhasil update otorisasi')
        loadAll()



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  // alertify.confirm('Otorisasi', 'Otorisasi PRJ ' + nobukti + ' ?',
  //     function() {
  //     }
  //   ,function(){
  //     console.log('no')
  //   });
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


</script>



<script>
function submitPrint (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''


    $.ajax({
      url: "{!! url('perintahreturjualcetak') !!}",
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
    console.log(dataPrint[0].NOBUKTI)

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

                    <div style="display: flex; width: 100%">
               
                        <div class="pb-1" style="width: 100%"></div>
                       
                      </div>


                     <div style="display: flex; width: 100%">
                        <div class="pb-1" style="width: 10%">Dari</div>
                        <div class="pb-1" style="width: 2%">:</div>
                        <div class="pb-1" style="width: 88%">${dataPrint[0].namaCustSupp}</div>
                      </div>


                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">BERITA ACARA RETUR JUAL</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Nomor</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOBUKTI}</div>
                    </div>


                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${tanggalOnly}</div>
                    </div>
                    
                    
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    
                  </tr>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 5%">No. Nota</td>
                    <td class="text-center" style="width: 5%">Kode Barang</td>
                    <td class="text-center" style="width: 30%">Nama Barang</td>
                    <td class="text-center" style="width: 5%">Part Number</td>
                    <td class="text-center" style="width: 5%">Sat</td>
                    <td class="text-center" style="width: 5%">Qty</td>
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
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.NOBUKTI}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.KodeBrg}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 30%;">${itemSub.NAMABRG}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.PartNumber}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.SATUAN}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
      
    </tr>`;
  z++;
});

// Fill remaining empty rows ï¿½ table is 225px, each row ~24px, header ~24px = ~8 total slots
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
    </tr>`;
}

  tempPrintStr += `</tbody>`;
  tempPrintStr += `</table>`;

  tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 40%; font-family: sans-serif; font-size: 10px;">
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">SALES COUNTER</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;">(...............................)</p>
        </td>
      </tr>
      
    </table>
  </div>

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">W.H,</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">CUSTOMER</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;">(...............................)</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;">(...............................)</p>
        </td>
      </tr>
      <tr>
        
      </tr>
    </table>
  </div>

</div>
`


    tempPrintStr += `</div>`
  });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }




function submitPrintBA (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''


    $.ajax({
      url: "{!! url('perintahreturjualcetak') !!}",
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
    console.log(dataPrint[0].NOBUKTI)

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

                    <div style="display: flex; width: 100%">
               
                        <div class="pb-1" style="width: 100%"></div>
                       
                      </div>


                     <div style="display: flex; width: 100%">
                        <div class="pb-1" style="width: 10%">Reff No.</div>
                        <div class="pb-1" style="width: 2%">:</div>
                        <div class="pb-1" style="width: 88%">${dataPrint[0].NOBUKTI}</div>
                      </div>

                      <div style="display: flex; width: 100%">
                        <div class="pb-1" style="width: 10%">Tanggal</div>
                        <div class="pb-1" style="width: 2%">:</div>
                        <div class="pb-1" style="width: 88%">${dataPrint[0].TANGGAL}</div>
                      </div>

                      <div style="display: flex; width: 100%">
                        <div class="pb-1" style="width: 10%">No. Invoice</div>
                        <div class="pb-1" style="width: 2%">:</div>
                        <div class="pb-1" style="width: 88%">${dataPrint[0].NOINVOICE1}</div>
                      </div>


                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">BERITA ACARA RETUR </h2>
                    </div>



                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Customer</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].namaCustSupp}</div>
                    </div>


                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Po. Customer</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoPesanan}</div>
                    </div>
                    
                    
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    
                  </tr>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 30%">Nama Barang</td>
                    <td class="text-center" style="width: 5%">Qnt</td>
                    <td class="text-center" style="width: 5%">No. SPB</td>
                    <td class="text-center" style="width: 5%">Faktur Pajak</td>
                    <td class="text-center" style="width: 5%">Supplier</td>
                    <td class="text-center" style="width: 5%">PO Beli</td>
                    <td class="text-center" style="width: 5%">Nota Beli</td>
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
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 30%;">${itemSub.NAMABRG}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.NOSPB}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.NoPajak}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.NAMASUPPLIER}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.NoPO}</td>
      <td style='border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black; ' class="no-border" style="width: 5%;">${itemSub.nobeli}</td>
      
    </tr>`;
  z++;
});

// Fill remaining empty rows ï¿½ table is 225px, each row ~24px, header ~24px = ~8 total slots
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

  <div style="width: 40%; font-family: sans-serif; font-size: 10px;">
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Kronologi :</td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>


      <tr>
        <td class="no-border text-center" style="width: 100%; font-size:13px;">Rekomendasi barang yang diretur :</td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>
   
      

      <tr>
        <td class="no-border text-center" style="width: 35%; font-size:13px;">Dibuat oleh</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;">(...............................)</p>
        </td>
      </tr>
      
    </table>
  </div>

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">



   

      
   












    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">

    

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>


      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>

      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
      </tr>


      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Diketahui oleh</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">Disetujui oleh</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;">(...............................)</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style=" font-size:12px;">(...............................)</p>
        </td>
      </tr>
      <tr>
        
      </tr>
    </table>
  </div>

</div>
`


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
