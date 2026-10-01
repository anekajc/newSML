@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Kredit Note')
@section('title', 'SML - Kredit Note')

@endsection

{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual before it.
     Only layout/toolbar/column-header interactivity changed -- business logic
     untouched. --}}
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
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
  border: 1.5px solid var(--rt-border); border-radius: 8px;1 padding: 5px 12px;
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
#tabel td:first-child .btn-warning, #tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger, #tabel2 td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel thead th, #tabel2 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover { background-color: #f5f3ff; }

/* Hide action buttons until the row is hovered/focused, port 1:1 dari pola
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
  {{-- Filter modal: port 1:1 dari modalFilter milik perintahreturjual.blade.php.
       tabel (Belum Otorisasi) + tabel2 (Sudah Otorisasi) digabung jadi satu tabel
       di sini dengan Status dropdown, sama seperti PRJ/RPG/SJ/UMJ/NRP -- tidak ada
       tab lagi karena kreditnote cuma punya 2 tab, keduanya konsep otorisasi. --}}
  <div class="modal fade rt-filter" id="modalFilterKN">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-funnel"></i>
            Filter Data
            <span class="rt-active-badge" id="knFilterBadge">0 aktif</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterKN').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-group-label">Status</div>
            <div>
              <label class="rt-field-label" for="input_filterkn">Status Otorisasi</label>
              <select class="rt-native" id="input_filterkn">
                <option value=0 selected>Semua</option>
                <option value=1>Belum Otorisasi</option>
                <option value=2>Sudah Otorisasi</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="rt-reset-link" onclick="knResetFilterFields()">Reset semua</button>
          <div class="rt-footer-buttons">
            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
              onclick="$('#modalFilterKN').modal('hide')">Batal</button>
            <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterKN(); $('#modalFilterKN').modal('hide');">Terapkan</button>
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
          <input type="date" onchange="onChangePeriodeKN()" class="po-filter-inp" id="input_tanggalawal_kn" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodeKN()" class="po-filter-inp" id="input_tanggalakhir_kn" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="knSearch1" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap"><label for="knLen1">Tampilkan</label>
          <select id="knLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
        </div>
        <button class="po-btn-filter" type="button" onclick="$('#modalFilterKN').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
      </div>
      <div id="rtBarTabel"></div>
      <table id="tabel" class="data-table">
        <thead style="white-space:nowrap;"></thead>
        <tbody id="tabel_data" class="text-left" ></tbody>
      </table>
      <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
    </div>
  </div>

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -30px">
    <div class="col-8 text-left">
      <h2>Form Kredit Note</h2>
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





          </div>
        </div>














      </div>

      <div class="row" style="margin-top: -10px">
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


          </div>
        </div>

        <div class="col-md-3" style="margin-top: 10px">
          <div class="row">
            <div class="col-md-12" style="margin-top:-10px">

              <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
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
              <th style="padding: 4px 12px;" scope="col">No Inv</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Kredit Note</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Inv</th>
              <th style="padding: 4px 12px;" scope="col">Valas</th>
              <th style="padding: 4px 12px;" scope="col">Kurs</th>
              <th style="padding: 4px 12px;" scope="col">Nilai KN Rp</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Invoice Rp</th>


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
  <button id="buttonAddListInvoice" type="button" class="btn btn-primary" onclick="buttonAddListInvoice()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah Invoice</button>
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
          <button class="btn btn-chip-biru btn-sm btn-icon-search" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-search"></i></button>

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
  <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

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

    <div class="col-md-4">




  <div class="row">




    <div class="col-md-2">
      <div class="form-group">
      <label>No Inv</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

      </div> -->
    <div class="col-md-6">
      <div class="input-group form-group">
        <input id="AddEditNoInv" type="text" class="form-control" disabled>
        <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->

      </div>
    </div>

  </div>

  <div class="row">




    <div class="col-md-2">
      <div class="form-group">
      <label>Ket.</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

      </div> -->
    <div class="col-md-6">
      <div class="input-group form-group">
        <input id="AddEditKeterangan" type="text" class="form-control">
        <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->

      </div>
    </div>

  </div>


</div>

</div>
</div>


</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">



  <div class="col-md-4">
    <div class="form-group">
    <label>Valas</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditValas" type="text" class="form-control" disabled>
  </div>

</div>
</div>
</div>



<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">



  <div class="col-md-4">
    <div class="form-group">
    <label>Kurs</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditKurs" type="number" value="1.00" class="form-control text-right" onBlur="onChangeNilaiKursItem()">
  </div>

</div>
</div>
</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">



  <div class="col-md-4">
    <div class="form-group">
    <label>Nilai</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditNilai" type="number" value="0.00" class="form-control text-right" onBlur="onChangeNilaiKursItem()">
  </div>

</div>
</div>

<div class="col-md-2" >
  <div class="row">




<div class="col-md-8">
  <input id="AddEditNilaiRp" type="number" value="0.00" class="form-control text-right" disabled>
</div>

</div>
</div>
</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-2" >
    <div class="row">



  <div class="col-md-4">
    <div class="form-group">
    <label>Nilai Inv</label>
  </div>
  </div>
  <div class="col-md-8">
    <input id="AddEditNilaiInv" type="number" value="0.00" class="form-control text-right" disabled>
  </div>

</div>
</div>

<div class="col-md-2" >
  <div class="row">




<div class="col-md-8">
  <input id="AddEditNilaiInvRp" type="number" value="0.00" class="form-control text-right" disabled>
</div>

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
        <h2>Form Kredit Note</h2>
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
        <!-- <input type="hidden" name="noUrut" id="input_add_nourut" value="" /> -->
        <div class="row">


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





            </div>
          </div>














        </div>

        <div class="row" style="margin-top: -10px">
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
                <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button> -->
              </div>
            </div>
            </div>
            </div>


            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_detail_namacustomer"  disabled></textarea>
              </div>
            </div>


            </div>
          </div>

          <div class="col-md-3" style="margin-top: 10px">
            <div class="row">
              <div class="col-md-12" style="margin-top:-10px">

                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatcustomer"  disabled></textarea>
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
                <th style="padding: 4px 12px;" scope="col">No Inv</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Kredit Note</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Inv</th>
                <th style="padding: 4px 12px;" scope="col">Valas</th>
                <th style="padding: 4px 12px;" scope="col">Kurs</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Kredit Note</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Invoice Rp</th>



              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=9 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    <div class="col-md-12 mt-2 text-right" id="divOto">
      <button id="submitOtorisasi" type="button" class="btn btn-primary" onclick="submitOtorisasi()" class="btn btn-secondary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;" >Otorisasi</button>
    </div>


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


            <table id="tabel_add_list_customer" class="data-table" style="overflow:auto; " >
              <thead class="text-center">
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
        <button id="" type="button" onclick="buttonAddListBatal()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>
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


            <table id="tabel_add_list_invoice" class="data-table" style="overflow:auto; ">
              <thead class="text-center">
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

        <button id="" type="button" onclick="buttonAddListBatal()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Batal</button>


        <button id="" type="button" onclick="buttonAddPickInvoice()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>


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

let xppn = 0
let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php, sama
 * seperti so/invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual.
 */
// Disederhanakan jadi satu tabel (urut 1) setelah tab Belum/Sudah Otorisasi
// digabung jadi satu daftar dengan filter Status Otorisasi (lihat modalFilterKN
// di section('content')), sama seperti perintahreturjual.blade.php sudah tidak
// punya tab otorisasi terpisah lagi.
let knCart = []
const KN_HREF = 'kreditnote'
const KN_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const KN_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function knPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function knDefaultCart () {
  return [
    ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Cust', 1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto',  1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto',   1, 'date',    0, 0],
  ]
}

function knBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, KN_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

window.g_href = KN_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = knCart || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(KN_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: KN_HREF, urut: 1
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: KN_HREF, urut: 1, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        knCart = knBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        knCart = knDefaultCart()
        window.gcart_header = knCart
        window.doSimpanHeader()
      }
      window.gcart_header = knCart
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      knCart = knDefaultCart()
      window.gcart_header = knCart
    }
  })
}

let knRtSudahInit = false
function knInitReportTableSekali () {
  if (knRtSudahInit || typeof ReportTable === 'undefined') { return }
  knRtSudahInit = true
  ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel })

  let knGuardUlangKlik = false;
  ['#tabel'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (knGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      knGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      knGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderKN (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols).replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function knValueCell (row, col) {
  let raw = knPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw, '/') + '</td>'; }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// Digabung dari tabelActionsCell (Belum Otorisasi: Detail/Koreksi/Otorisasi) +
// tabel2ActionsCell (Sudah Otorisasi: Detail/Batal Otorisasi) sejak keduanya
// digabung jadi satu tabel dengan filter Semua/Belum/Sudah Otorisasi.
function tabelActionsCell (row) {
  let nobukti = knPickCI(row, 'NOBUKTI');
  let isOto = Number(knPickCI(row, 'IsOtorisasi1'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (knCart.length ? knCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += knValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderKN('#tabel', cols);
}

let lastTabelRows = []
let knPanjangHalaman = 10

function knIkatSearch () {
  let input = document.getElementById('knSearch1')
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

function knIkatPanjangHalaman () {
  let sel = document.getElementById('knLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(knPanjangHalaman)
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    knPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().page.len(knPanjangHalaman).draw() }
  })
}

const KN_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: KN_DOM_STRING, lengthChange: false, pageLength: knPanjangHalaman, paging: true, order: [[1, 'asc']], ordering: false });
    knIkatSearch(); knIkatPanjangHalaman();
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function knResetFilterFields () {
  $('#input_filterkn').val('0')
}

function knUpdateFilterBadge () {
  let n = Number($('#input_filterkn').val()) || 0
  $('#knFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterKN () {
  let tglawal = $('#input_tanggalawal_kn').val()
  let tglakhir = $('#input_tanggalakhir_kn').val()
  let filterkn = $('#input_filterkn').val()
  $.ajax({
    url: "{!! url('kreditnoteloadall') !!}",
    type: "get", async: false,
    data: { tglawal, tglakhir, filterkn },
    success: function (res) {
      lastTabelRows = res.tempOutstanding
      reinitTabel()
      knUpdateFilterBadge()
    },
    error: function (err) { console.log(err); alertify.warning('Terjadi kesalahan silahkan refresh browser') }
  })
}

function onChangePeriodeKN () {
  let tglawal = $('#input_tanggalawal_kn').val()
  let tglakhir = $('#input_tanggalakhir_kn').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  buttonFilterKN()
}

$(document).ready(function(){
      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();

      knInitReportTableSekali();

        $("#tabel_add_list_customer").DataTable({
          "lengthChange": false,
            "paging": false ,
        });




  //   formAddListItem
});

function setNewNoBukti (xval = 1) {
  console.log(xval)
  $.ajax({
    url: "{!! url('kreditnotespnobukti') !!}",
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

  document.getElementById("input_add_nobukti").value = ''

  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()



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
  let kodecustsupp = barang.KodeSupp
  let nobukti  = $("#input_add_nobukti").val()
  let tanggal  = $("#input_add_tanggal").val()
  let noinvoice  = $("#AddEditNoInv").val()
  let nilai  = $("#AddEditNilai").val()
  let nilairp  = $("#AddEditNilaiRp").val()
  let kodevls  = $("#AddEditValas").val()
  let kurs  = $("#AddEditKurs").val()
  let keterangan  = $("#AddEditKeterangan").val()

  // return

  if (Number(nilai) < 0 || Number(kurs) < 0 ) {
    alertify.warning("Nilai < 0")
    return
  }

  console.log({
    choice,
    nobukti,
    tanggal,
    urut,
    noinvoice,
    kodecustsupp,
    nilai,
    kodevls,
    kurs,
    nilairp
  })


  $.ajax({
    url: "{!! url('kreditnotespkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      tanggal,
      urut,
      noinvoice,
      kodecustsupp,
      nilai,
      kodevls,
      kurs,
      nilairp,
      keterangan


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
        refreshDataTable(nobukti)


        alertify.success('Berhasil Edit Invoice')
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddDelete (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let barang = listData[i]



  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus Invoice ' + barang.NoInv + ' ?',
      function() {
        let _token  = $("#_token").val()

        let choice = "D"
        let urut = barang.Urut
        let kodecustsupp = barang.KodeSupp
        let nobukti  = $("#input_add_nobukti").val()
        let tanggal  = $("#input_add_tanggal").val()
        let noinvoice  = $("#AddEditNoInv").val()
        let nilai  = $("#AddEditNilai").val()
        let nilairp  = $("#AddEditNilaiRp").val()
        let kodevls  = $("#AddEditValas").val()
        let kurs  = $("#AddEditKurs").val()
        console.log(nobukti, urut)
        $.ajax({
          url: "{!! url('kreditnotespkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            tanggal,
            urut,
            noinvoice,
            kodecustsupp,
            nilai,
            kodevls,
            kurs,
            nilairp


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
              refreshDataTable(nobukti)


              alertify.success('Berhasil menghapus Invoice')
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

function buttonAddPickInvoice () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();

  let tanggal = $("#input_add_tanggal").val();
    let kodecustsupp = $("#input_add_kodecustomer").val();

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  console.log(nourut)
  let _token = $("#_token").val();
  console.log("buttonAddPickInvoice")
  let tempData = []
  // let checkQnt = 0
  let checkMinus = 0
  console.log(listInvoice)
    listInvoice.forEach((item, i) => {
      console.log(document.getElementById(`add_checkbox${i}`).checked)
      if (document.getElementById(`add_checkbox${i}`).checked) {

        let checkNilai = $(`#add_inputQnt${i}`).val();
        let checkKurs = $(`#add_inputKurs${i}`).val();
        let checkNilaiRp = $(`#add_inputQntRp${i}`).val();
        // add_inputKeterangan
        listInvoice[i].Keterangan = $(`#add_inputKeterangan${i}`).val();
        listInvoice[i].inputNilai = checkNilai
        listInvoice[i].inputKurs = checkKurs
        listInvoice[i].inputNilaiRp = checkNilaiRp
        if (Number(checkNilai) < 0 || Number(checkKurs) < 0 ) {
          checkMinus = 1
        }

        tempData.push(listInvoice[i])

      }


    });
    console.log(tempData)

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }

    if (checkMinus) {
      alertify.warning("Qnt < 0");
      return
    }

    $.ajax({
        url: "{!! url('kreditnotespadd') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          tempData,
          tanggal: tanggal,
          nobukti,
          nourut,
          kodecustsupp,
          tipeform,
          nourut
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('KN telah ditambah');
            loadAll()
            // buttonCloseForm()
            tipeform = 'edit'
            document.getElementById("buttonAddListCustomer").disabled = true
            document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            $("#form").modal('toggle')

          }
          if (res == 2) {
            setNewNoBukti(xppn)
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

function buttonAddListCustomer () {
  console.log('buttonAddListCustomer')
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('kreditnotelistcustomer') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}' , ${item.PPN ? item.PPN : 0})">

        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.ALAMAT}</td>
        <td>${item.NamaKota}</td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      $("#tabel_add_list_customer").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[0, 'asc']],
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


function buttonAddPickCustomer (kode, nama , alamat , ppn) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat

  if (ppn && Number(ppn) == 1) {
    setNewNoBukti(1)
    xppn =1

  } else {
    setNewNoBukti(0)
    xppn =0

  }


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
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggal").disabled = true


  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("buttonAddListNoInvoice").disabled = true

}





function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('kreditnotespdetail') !!}",
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
          $('#page2').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listData.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan ? item.Keterangan : ''}</td>
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">0.00</td>

                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>


                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `
      });

      document.getElementById("addTableData").innerHTML = rowTable


        document.getElementById("input_add_kodecustomer").value = listData[0].KodeSupp
        document.getElementById("input_add_namacustomer").value = listData[0].NamaCustSupp
        document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].tanggal)










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
    url: "{!! url('kreditnotespotorisasi') !!}",
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

function buttonDetail (nobukti) {
  document.getElementById("divOto").style.display = "none";

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('kreditnotespdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      // listData = res
      // console.log(res)
      if (!res.length) {
          alertify.success('Data tidak ditemukkan')
          // $("#form").modal('toggle')
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      res.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan ? item.Keterangan : ''}</td>
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>

                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>



                </tr>

              `
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
        document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
        document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
        document.getElementById("input_detail_nobukti").value = res[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)

        $('#modalDetail').show();
        $('.mainpage').hide();
        $('#page3').show();








    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })

}

function buttonOtorisasi (nobukti) {
  document.getElementById("divOto").style.display = "block";
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('kreditnotespdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      // listData = res
      // console.log(res)
      if (!res.length) {
          alertify.success('Data tidak ditemukkan')
          // $("#form").modal('toggle')
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      res.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan}</td>
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>

                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>



                </tr>

              `
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
        document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
        document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
        document.getElementById("input_detail_nobukti").value = res[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)

        $('#modalDetail').show();
        $('.mainpage').hide();
        $('#page3').show();








    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })



}

function buttonKoreksi (nobukti ) {

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

  console.log('buttonKoreksi' , nobukti )

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'edit'
  cleanFormAdd()
  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("input_add_tanggal").disabled = true

  refreshDataTable(nobukti)
  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  console.log(listData)
  console.log(listData[0].isOtorisasi1)
  if (listData[0].IsOtorisasi1 == 1) {
    alertify.warning("KN sudah diotorisasi")
    return
  }

  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
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
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("addTableData").innerHTML = `<td colspan=9 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  // unlockFormAdd()
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
  tempBarangAddEdit = listData[i]
  console.log(tempBarangAddEdit)
  document.getElementById("AddEditNoInv").value = tempBarangAddEdit.NoInv
  document.getElementById("AddEditValas").value = tempBarangAddEdit.kodeVls
  document.getElementById("AddEditKurs").value = tempBarangAddEdit.Kurs
  document.getElementById("AddEditNilai").value = tempBarangAddEdit.Nilai

  document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.Keterangan

  document.getElementById("AddEditNilaiRp").value = tempBarangAddEdit.NilaiRp
  document.getElementById("AddEditNilaiInv").value = tempBarangAddEdit.Saldo
  document.getElementById("AddEditNilaiInvRp").value = tempBarangAddEdit.Saldo

  // document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.KODEBRG
  // document.getElementById("AddEditReturSupp").value = tempBarangAddEdit.FlagKembali
  // document.getElementById("AddEditNoBeli").value = tempBarangAddEdit.nobeli
  // document.getElementById("AddEditUrutBeli").value = tempBarangAddEdit.urutbeli
  // document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NAMABRG
  // document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.QNT).toFixed(2)
  // document.getElementById("AddEditInputQty1").value = parseFloat(tempBarangAddEdit.QNT1).toFixed(2)
  // document.getElementById("AddEditInputQty2").value = parseFloat(tempBarangAddEdit.QNT2).toFixed(2)
  // document.getElementById("AddEditInputSat1").value = tempBarangAddEdit.SAT_1
  // document.getElementById("AddEditInputSat2").value = tempBarangAddEdit.SAT_2
  //
  // document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.KetDetail
  //
  // let selectOption = ''
  // if (tempBarangAddEdit.SAT_1) {
  //   selectOption += `<option value=1 ${tempBarangAddEdit.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddEdit.SAT_1}</option>`
  // }
  // if (tempBarangAddEdit.SAT_2) {
  //   selectOption += `<option value=2 ${tempBarangAddEdit.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddEdit.SAT_2}</option>`
  // }
  // document.getElementById("AddEditInputNosat").innerHTML = selectOption
  //
  //




  $('.showhideitem').hide();
  $('#formAddEdit').show();

}


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function loadAll () {
  let tglawal = $('#input_tanggalawal_kn').val()
  let tglakhir = $('#input_tanggalakhir_kn').val()
  let filterkn = $('#input_filterkn').val()
  $.ajax({
    url: "{!! url('kreditnoteloadall') !!}",
    type: "get", async: false, data: { tglawal, tglakhir, filterkn },
    success: function(res) {
      lastTabelRows = res.tempOutstanding;
      reinitTabel();
    }})
}

function buttonBatalOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi KN ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('kreditnotespbatalotorisasi') !!}",
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
