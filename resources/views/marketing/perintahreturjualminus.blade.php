@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Perintah Retur Jual (-)')
@section('title', 'SML - Perintah Retur Jual (-)')

@endsection

{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual/
     returpenjualangudang/kreditnote/notareturpenjualan before it -- this page
     mirrors perintahreturjual.blade.php almost exactly (same tabs/tables/CRUD
     flow), only difference being one extra Gudang picker and no print buttons
     on tabel2, so the already-ported structure/engine from perintahreturjual
     was reused directly, adapted for those two differences. Only layout/
     toolbar/column-header interactivity changed -- business logic untouched. --}}
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
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
.po-toolbar-search {
  display: flex;
  align-items: center;
  gap: 10px;
}
.search-label {
  font-weight: bold;
  font-size: 0.75rem;
  margin-right: 155px;
  margin-top : -45px;
  display: inline-block;
  vertical-align: middle;
}
.custom-tabs {
  display: inline-flex; justify-content: flex-start; align-items: center; gap: 2px;
  background-color: #f1f3f5; border-radius: 20px; padding: 3px;
}
.custom-tabs .nav-link {
  display: inline-block !important; padding: 5px 16px !important; font-size: 0.75rem !important;
  border: none; border-radius: 17px; color: #495057; background: transparent; font-weight: 600;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}
.custom-tabs .nav-link:hover { background: transparent; color: #007bff; }
.custom-tabs .nav-link.active {
  background: #007bff; border-color: #007bff; color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}
.tab-card {
  display: block !important; align-items: flex-start !important; padding: 0 !important;
  border: none !important; margin-bottom: 6px !important;
}
.tab-card .card-body { padding: 5px 10px !important; }
#page1 .card {
  display: block !important; align-items: stretch !important; padding: 0 !important;
  text-align: left !important; cursor: default !important;
}
#page1 .card:hover { transform: none !important; box-shadow: none !important; border-color: var(--border) !important; }
.po-len-wrap {
  display: flex; align-items: center; gap: 8px; background: var(--rt-card);
  border: 1.5px solid var(--rt-border); border-radius: 8px; padding: 5px 12px;
}
.po-len-wrap label {
  margin: 0; font-size: 11.5px; font-weight: 700; color: var(--rt-ink-soft);
  text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
}
.po-len-inp {
  border: none; background: transparent; font-size: 13px; font-weight: 700; color: var(--rt-ink);
  outline: none; cursor: pointer; padding: 2px 20px 2px 0; appearance: none;
  -webkit-appearance: none; -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right center;
}
#tabel td:first-child, #tabel2 td:first-child { display: flex; gap: 4px; justify-content: center; align-items: center; }
#tabel td:first-child .btn, #tabel2 td:first-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
  box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover, #tabel2 td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-success, #tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-warning, #tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger, #tabel2 td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel thead th, #tabel2 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover { background-color: #f5f3ff; }

/* Hide action buttons until the row is hovered/focused, port 1:1 dari pola
   .action-buttons-wrap milik master (public/css/tableMaster2.css). */
#tabel tbody .action-buttons-wrap,
#tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}

#tabel tbody tr:hover .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

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

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection


@section('content')

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

  {{-- Tab bar: PO's exact card.tab-card + custom-tabs anchor pattern, same as
       perintahreturjual.blade.php. --}}
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          PRJ Belum Otorisasi
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
          PRJ Sudah Otorisasi
        </a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding: 0">
      <div class="tab-content" id="myTabContent">

        {{-- Belum Otorisasi tab: PO's toolbar + #rtBarTabel + bare data-table + hint skeleton. --}}
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-toolbar-search">
                    <input type="search" id="prjmSearch1" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="prjmLen1">Tampilkan</label>
                      <select id="prjmLen1" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  <div class="po-toolbar-act">
                    <button type="button" class="btn btn-primary" onclick="buttonAdd()">+ PRJ</button>
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

        {{-- Sudah Otorisasi tab. --}}
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="prjmSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-toolbar">
                    <div class="po-toolbar-search">
                      <input type="search" id="prjmSearch2" class="po-search-inp" placeholder="Cari data">
                      <div class="po-len-wrap">
                        <label for="prjmLen2">Tampilkan</label>
                        <select id="prjmLen2" class="po-len-inp">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                          <option value="-1">Semua</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="rtBarTabel2"></div>
                <table id="tabel2" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
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


</div>
</div>

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -30px">
    <div class="col-8 text-left">
      <h2>Form PRJ</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
    </div>
  </div>

  <div id= "modalAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
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
              <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-search"></i></button>
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

            <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
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
                <input type="hidden" class="form-control" id="input_add_flagtipe" value=""  >
                <input type="hidden" class="form-control" id="input_add_ppn" value=""  >
                <input type="text" class="form-control" id="input_add_noinvoice" value="-" disabled >
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoInvoice" onclick="buttonAddListNoInvoice()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Catatan</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_catatan"  ></textarea>
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
                <input type="text" class="form-control text-left" id="input_add_noso" value="-"  disabled>
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
                <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListGudang" onclick="buttonAddListGudang()"><i class="bi bi-search"></i></button>
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
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
              <th style="padding: 4px 12px;" scope="col">Sat Produk</th>
              <th style="padding: 4px 12px;" scope="col">Qty</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>

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
  <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah Item</button>
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
          <input id="AddAddKodeBrg" type="text" class="form-control" onkeypress="onKeyPressBarang(event)">
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
          <input id="AddAddUrutBeli" type="hidden"  class="form-control" >
          <input id="AddAddNoBeli" type="text" class="form-control" disabled>
          <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-plus"></i></button> -->

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
  <!-- <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

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
</div>
</div> -->

<input id="AddAddInputQty1" type="hidden" value='0.00' class="form-control text-right" disabled style="width: 75%">

<input id="AddAddInputSat1" type="hidden" value='PCS' class="form-control text-center" disabled style="width: 25%">

<input id="AddAddInputQty2" type="hidden" value='0.00' class="form-control text-right" disabled style="width: 75%">

<input id="AddAddInputSat2" type="hidden" value='BOX' class="form-control text-center" disabled style="width: 25%">






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
      </div>


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


<!-- <div class="col-md-12" style="margin-top: -10px">
<div class="row">
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
  </div>
</div> -->
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
      text-transform: uppercase;">Submit Edit</button>
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

    <div class="row" style="margin-top: -30px">
      <div class="col-8 text-left">
        <h2>Detail PRJ</h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
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
          <div class="col-md-12" style="margin-top:-10px">
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
              <h3>Customer</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_customer" class="data-table" style="overflow:auto; ">
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
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
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
              <h3>No Invoice</h3>
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


      <div id= "modalAddListGudang" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Gudang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_gudang" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Alamat</th>
                </tr>
              </thead>


              <tbody id="tabel_data_add_list_gudang" class="text-left" >
                @for ($i = 0; $i < count($listGudang); $i++)
                <tr class="pick-row" onclick="buttonAddPickGudang('{{ $listGudang[$i]->KODEGDG }}' )">

                  <td>{{ $listGudang[$i]->KODEGDG }}</td>
                  <td>{{ $listGudang[$i]->NAMA }}</td>
                  <td>{{ $listGudang[$i]->ALAMAT }}</td>



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
              <h3>No Beli</h3>
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
            <div class="col-md-9">
              <h3>Barang</h3>
            </div>
            <div class="col-md-3 text-right form-group" style="">
              <input id="input_search_barang_all" type="text" name="" value="" class="form-control" onkeypress="searchBarangAll(event)">
              <label for="input_search_barang_all" class="search-label">SEARCH:</label>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-40px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>

                  <th style="padding: 4px 12px;" scope="col">Sat1</th>

                  <th style="padding: 4px 12px;" scope="col">Sat2</th>

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
 * purchaseOrder.blade.php, sama seperti so/invoicejasa/fakturpajak/
 * cetaktandaterima/perintahreturjual/returpenjualangudang/kreditnote/
 * notareturpenjualan. Halaman ini (PRJ minus) sama persis dengan
 * perintahreturjual.blade.php kecuali tabel2 di sini tidak punya tombol cetak
 * (submitPrint/submitPrintBA tidak ada di halaman ini).
 */
let prjmCart = { 1 : [], 2 : [] }
let prjmActiveUrut = 0
const PRJM_HREF = 'perintahreturjualminus'
const PRJM_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const PRJM_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
let prjmPerluGambar = { 1 : false, 2 : false }

function activeVisibleTabKeyPRJM () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

function prjmPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function prjmDefaultCart (urut) {
  let cart = [
    ['NOBUKTI',      'No. Bukti',   1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',     1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Cust',   1, 'varchar', 0, 0],
    ['NoRPJ',        'No Invoice',  1, 'varchar', 0, 0],
    ['NOSO',         'No SO',       1, 'varchar', 0, 0],
    ['IDUser',       'User',        1, 'varchar', 0, 0],
  ]
  if (urut === 2) {
    cart.push(['OtoUser1', 'User Oto1', 1, 'varchar', 0, 0])
    cart.push(['TglOto1',  'Tgl Oto1',  1, 'date',    0, 0])
  }
  return cart
}

function prjmBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, PRJM_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function prjmAktifkanTabel (urut) {
  prjmActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = prjmCart[urut]
}

function prjmOnChangeAktif () {
  if (prjmActiveUrut === 2) { reinitTabel2(); } else { reinitTabel(); }
}

window.g_href = PRJM_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode === 2 ? 2 : 1
  let cart = prjmCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(PRJM_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: PRJM_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode === 2 ? 2 : 1

  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: PRJM_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        prjmCart[urut] = prjmBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        prjmCart[urut] = prjmDefaultCart(urut)
        window.gcart_header = prjmCart[urut]
        window.doSimpanHeader(PRJM_HREF, urut)
      }
      window.gcart_header = prjmCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      prjmCart[urut] = prjmDefaultCart(urut)
      window.gcart_header = prjmCart[urut]
    }
  })
}

const PRJM_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const PRJM_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let prjmRtSudahInit = false
function prjmInitReportTableSekali () {
  if (prjmRtSudahInit || typeof ReportTable === 'undefined') { return }
  prjmRtSudahInit = true

  let urutAktif = activeVisibleTabKeyPRJM()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  let idBar = { 1 : '#rtBarTabel', 2 : '#rtBarTabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], bar: idBar[u], onChange: prjmOnChangeAktif })
  });

  ReportTable.init({ table: PRJM_SELEKTOR_TABEL_AKTIF, bar: PRJM_SELEKTOR_BAR_AKTIF, onChange: prjmOnChangeAktif })

  let prjmGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (prjmGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      prjmGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      prjmGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderPRJM (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols).replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function prjmValueCell (row, col) {
  let raw = prjmPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw, '/') + '</td>'; }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function tabelActionsCell (row) {
  let nobukti = prjmPickCI(row, 'NOBUKTI');
  let isOto = Number(prjmPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonEdit(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function tabel2ActionsCell (row) {
  let nobukti = prjmPickCI(row, 'NOBUKTI');
  let isOto = Number(prjmPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  if (prjmActiveUrut !== 1 && prjmCart[1].length === 0) { prjmAktifkanTabel(1); }
  let cols = (prjmCart[1].length ? prjmCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += prjmValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderPRJM('#tabel', cols);
}

function renderTabel2Rows (rows) {
  let cols = (prjmCart[2].length ? prjmCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += prjmValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderPRJM('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let prjmPanjangHalaman = { 1 : 10, 2 : 10 }

function prjmIkatSearch (urut) {
  let ids = { 1 : ['prjmSearch1', 'tabel'], 2 : ['prjmSearch2', 'tabel2'] }
  let input = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().search(nilai).draw() }
    }, 400)
  })
}

function prjmIkatPanjangHalaman (urut) {
  let ids = { 1 : ['prjmLen1', 'tabel'], 2 : ['prjmLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(prjmPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    prjmPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(prjmPanjangHalaman[urut]).draw() }
  })
}

const PRJM_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: PRJM_DOM_STRING, lengthChange: false, pageLength: prjmPanjangHalaman[1], paging: true, order: [[1, 'asc']], ordering: false });
    prjmIkatSearch(1); prjmIkatPanjangHalaman(1); prjmPerluGambar[1] = false;
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: PRJM_DOM_STRING, lengthChange: false, pageLength: prjmPanjangHalaman[2], paging: true, order: [[1, 'asc']], ordering: false });
    prjmIkatSearch(2); prjmIkatPanjangHalaman(2); prjmPerluGambar[2] = false;
  } catch (e) { console.error('reinitTabel2 failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function buttonHeaderTable (key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    let urut = key === 'tabel2' ? 2 : 1
    prjmAktifkanTabel(urut)
    window.doSetHeader(urut, true)
    ;(urut === 2 ? reinitTabel2 : reinitTabel)()
    alertify.success('Kolom telah direset ke tampilan default')
  }, function () {})
}

$(document).ready(function(){

      prjmAktifkanTabel(1); window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      prjmAktifkanTabel(2); window.doSetHeader(2, false);
      lastTabel2Rows = @json($tempOutstanding2);
      reinitTabel2();

      prjmInitReportTableSekali();

      $('#nav-home-tab').on('shown.bs.tab', function () { prjmAktifkanTabel(1); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (prjmPerluGambar[1]) { reinitTabel(); } });
      $('#nav-profile-tab').on('shown.bs.tab', function () { prjmAktifkanTabel(2); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (prjmPerluGambar[2]) { reinitTabel2(); } });

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

            "searching" : false,
      });

      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });




  //   formAddListItem
});

function onKeyPressBarang (e) {
  // tempxKodeBrg = ''


  let xkodecustsupp = $("#input_add_kodecustomer").val();
  let xgudang = $("#input_add_gudang").val();
  if (!xkodecustsupp || !xgudang ) {
    alertify.warning("Pilih customer & gudang terlebih dahulu")
    return
  }

  if (e.which == 13) {
    let kodebrg = $('#AddAddKodeBrg').val();
    document.getElementById("input_search_barang_all").value = kodebrg
    let search = $("#input_search_barang_all").val();
    console.log(search)
    $('#tabel_add_list_barang').DataTable().destroy();

        $.ajax({
          url: "{!! url('perintahreturjualminuslistbarang') !!}",
          type: "get",
          async: false,
          data: {
            search
          },
          success: function(res) {
            if (res.length == 1) {
              tempBarangAddAdd = res[0]

              console.log('tempBarangAddAdd', tempBarangAddAdd)
              document.getElementById("AddAddKodeBrg").value = tempBarangAddAdd.KodeBrg
              document.getElementById("AddAddNamaBrg").value = tempBarangAddAdd.NamaBrg ? tempBarangAddAdd.NamaBrg : tempBarangAddAdd.NamaBrgx
              document.getElementById("AddAddInputQty").value = "0.00"
              document.getElementById("AddAddInputQty1").value = "0.00"
              document.getElementById("AddAddInputQty2").value = "0.00"

              document.getElementById("AddAddInputSat1").value = tempBarangAddAdd.SAT1
              document.getElementById("AddAddInputSat2").value = tempBarangAddAdd.SAT2

              let selectOption = ''
              if (tempBarangAddAdd.SAT1) {
                selectOption += `<option value=1 selected>SAT1 - ${tempBarangAddAdd.SAT1}</option>`
              }
              if (tempBarangAddAdd.SAT2) {
                selectOption += `<option value=2 >SAT2 - ${tempBarangAddAdd.SAT2}</option>`
              }
              document.getElementById("AddAddInputNosat").innerHTML = selectOption





            } else {
              console.log(res)
              listBarang = res
              let rowTable = ""
              res.forEach((item, i) => {

                rowTable +=          `
                <tr class="pick-row" onclick="buttonAddPickBarang(${i})">

                  <td>${item.KodeBrg}</td>
                  <td>${item.NamaBrg}</td>

                  <td>${item.SAT1}</td>
                  <td>${item.SAT2}</td>




              </tr>
              `
              });
              // $('#tabel_add_list_barangall').DataTable().destroy();

              document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable

            $("#tabel_add_list_barang").DataTable({
              "lengthChange": false,
                "paging": false ,
                "searching" : false,
                "order": [[0, 'asc']],
            });

                        $('.showhidemodalbodyadd').hide();
                        $('#modalAddListBarang').show();
                        $("#form").modal('toggle')

            }

          }




        })

  }


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
  document.getElementById("AddAddNoBeli").value = '-'
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
  document.getElementById("input_add_noinvoice").value = '-'
  document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_noso").value = '-'
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
  let urutinvoice = 0
  let qnt1 = $("#AddEditInputQty1").val();
  let sat1 = $("#AddEditInputSat1").val();
  let qnt2 = $("#AddEditInputQty2").val();
  let sat2 = $("#AddEditInputSat2").val();
  let ketdet = $("#AddEditKeterangan").val();

  console.log(namabrg, kodebrg, qnt)

  if (Number(qnt) <= 0 ) {
    alertify.warning("Qnt <= 0")
    return
  }

  // return
  $.ajax({
    url: "{!! url('perintahreturjualminusspadd') !!}",
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
          url: "{!! url('perintahreturjualminusspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            nourut : '',
            tanggal : '',
            noinvoice : '-',
            kodecustsupp : '',
            catatan :'',
            urut,
            urutinvoice :0,
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

            $('.showhideitem').hide();
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
  let urutinvoice = 0
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
  if (tempBarangAddAdd.KodeBrg != kodebrg) {
    alertify.warning("Barang tidak sesuai dengan pilihan")
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
    url: "{!! url('perintahreturjualminusspadd') !!}",
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
  let gudang = $("#input_add_gudang").val();
  if (!kodecustsupp || !gudang ) {
    alertify.warning("Pilih customer & gudang terlebih dahulu")
    return
  }

  $('#tabel_add_list_barang').DataTable().destroy();

  document.getElementById("tabel_data_add_list_barang").innerHTML = ''

  $("#tabel_add_list_barang").DataTable({
    "lengthChange": false,
      "paging": false ,
      "searching" : false,"order": [[0, 'asc']],

  });
  document.getElementById("input_search_barang_all").value = ''


  $('.showhidemodalbodyadd').hide();
  $('#modalAddListBarang').show();
  $("#form").modal('toggle')

  $('#form').on('shown.bs.modal', function () {
  $('#input_search_barang_all').trigger('focus')
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
      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[0, 'asc']],

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
        <tr class="pick-row" onclick="buttonAddPickNoInvoice('${item.NOBUKTI}' , '${item.NoSO}' , '${item.KODEGDG}', ${item.flagtipe}, ${item.ppn})">

        <td>${item.NOBUKTI}</td>
        <td>${item.TANGGAL}</td>
        <td>${item.NoSO}</td>
        <td>${item.NAMAGDG ? item.NAMAGDG : ''}</td>

        </tr>`
      });




      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_noinvoice").innerHTML = rowTable
      $("#tabel_add_list_noinvoice").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[0, 'asc']],

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


function buttonAddListGudang() {

  $('.showhidemodalbodyadd').hide();
  $('#modalAddListGudang').show();
  $("#form").modal('toggle')






}

function buttonAddListCustomer () {
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistcustomer') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}', ${item.PPN})">

        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.NamaKota}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      $("#tabel_add_list_customer").DataTable({
        "lengthChange": false,
          "paging": false ,"order": [[0, 'asc']],

    });
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

function buttonAddPickCustomer (kode, nama , alamat , ppn) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat,ppn)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat
  document.getElementById("input_add_noinvoice").value = '-'
  document.getElementById("input_add_noso").value = '-'
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
  document.getElementById("AddAddInputQty").value = "0.00"
  document.getElementById("AddAddInputQty1").value = "0.00"
  document.getElementById("AddAddInputQty2").value = "0.00"

  document.getElementById("AddAddInputSat1").value = tempBarangAddAdd.SAT1
  document.getElementById("AddAddInputSat2").value = tempBarangAddAdd.SAT2

  let selectOption = ''
  if (tempBarangAddAdd.SAT1) {
    selectOption += `<option value=1 selected>SAT1 - ${tempBarangAddAdd.SAT1}</option>`
  }
  if (tempBarangAddAdd.SAT2) {
    selectOption += `<option value=2 >SAT2 - ${tempBarangAddAdd.SAT2}</option>`
  }
  document.getElementById("AddAddInputNosat").innerHTML = selectOption






  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickGudang (kodegdg) {
  console.log('buttonAddPickGudang')
  document.getElementById("input_add_gudang").value = kodegdg
  $('.showhideitem').hide();
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
  // document.getElementById("buttonAddListNoInvoice").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggal").disabled = true


  document.getElementById("buttonAddListCustomer").disabled = true
  // document.getElementById("buttonAddListNoInvoice").disabled = true

}

function refreshDataTableAdd (NOBUKTI = "") {

    console.log('refreshDataTableAdd' , NOBUKTI)


    if (!NOBUKTI) {


    } else {
      let _token  = $("#_token").val()
      $.ajax({
        url: "{!! url('perintahreturjualminusgetdetail') !!}",
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

              rowTable += `<tr>
              <td>${item.KODEBRG}</td>
              <td>${item.NAMABRG}</td>

              <td>${item.NAMAPRODUK}</td>
              <td>${item.SATX ? item.SATX : ''}</td>
              <td class="text-right">${item.QNT ? formatAngkaX(item.QNT): '0.00'}</td>
              <td>${item.SAT}</td>
              <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
               <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>
              </tr>`
            });

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
              <td class="text-right">${item.QNT ? formatAngkaX(item.QNT): '0.00'}</td>
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
  let xkodecustsupp = $("#input_add_kodecustomer").val();


  let xgudang = $("#input_add_gudang").val();
  if (!xkodecustsupp || !xgudang ) {
    alertify.warning("Pilih customer & gudang terlebih dahulu")
    return
  }
    $('.showhideitem').hide();
    cleanFormAddAdd()
    $('#formAddAdd').show();

}

function searchBarangAll (e) {
  // console.log('searchBarangAll')
  // console.log(e)
  // console.log(e.which)
  // console.log(e.key)

  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    $('#tabel_add_list_barang').DataTable().destroy();

    $.ajax({
      url: "{!! url('perintahreturjualminuslistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search
      },
      success: function(res) {

        console.log(res)
        listBarang = res
        let rowTable = ""
        res.forEach((item, i) => {

          rowTable +=          `
          <tr class="pick-row" onclick="buttonAddPickBarang(${i})">

            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>

            <td>${item.SAT1}</td>
            <td>${item.SAT2}</td>




        </tr>
        `
        });
        // $('#tabel_add_list_barangall').DataTable().destroy();

        document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable






      $("#tabel_add_list_barang").DataTable({
        "lengthChange": false,
          "paging": false ,
          "searching" : false,
          "order": [[0, 'asc']],

      });



      }})

  }

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
  $.ajax({
    url: "{!! url('perintahreturjualminusloadall') !!}",
    type: "get", async: false, data: {},
    success: function(res) {
      lastTabelRows = res.tempOutstanding;
      lastTabel2Rows = res.tempOutstanding2;
      reinitTabel();
      reinitTabel2();
    }})
}

function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }



  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi PRJ ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('perintahreturjualminusspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

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
    });





}

function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Otorisasi', 'Otorisasi PRJ ' + nobukti + ' ?',
      function() {
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


</script>






@endsection
