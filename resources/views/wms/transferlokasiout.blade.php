@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Transfer Lokasi Out')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>Transfer Lokasi Out</h1> - judul pindah ke header (page-title) --}}
<!-- <button onclick="loadAll()">tes</button> -->
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


  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding TL Out</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">TL Out</a>
      </div>
    </div>
  </div>
  <div class="card wms-kartu">
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabel" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{ $outstandingArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('{{ $outstandingArray[$i][0]->NOBUKTI }}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
                          </td>
                          <td>{{ $outstandingArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->TANGGAL)) !!}</td>
                        </tr>
                        @endfor
                      </tbody>


                    </table>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabelRetur" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                        </tr>
                      </thead>


                      <tbody id="tabelRetur_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')"><i class="bi bi-pen"></i></button>
                          </td>
                          <td>{{ $penerimaanArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->TANGGAL)) !!}</td>
                        </tr>
                        @endfor
                      </tbody>
                    </table>
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti Out</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_noout" placeholder="No Out" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>

          </div>

          <div class="row">

            <div class="col-12">
              <div class="form-group">
                <label>No FLK</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nossp" placeholder="" disabled>
              </div>
            </div>






          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">No Bukti</th>
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

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal detail -->
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="row">
            <!-- <div class="col-2">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_gdg" placeholder="Gdg Asal" required disabled>
              </div>
            </div> -->


            <div class="col-12">
              <div class="form-group">
                <label>No Out</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_noout" placeholder="" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-2">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_namacust" placeholder="namacust" disabled>
              </div>
            </div> -->
            <!-- <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div> -->




          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">No Bukti</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>

                  </tr>
                </thead>


                <tbody id="detailTableData" class="text-right" >
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



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>

      </div>
    </div>
  </div>
</div>
<!-- End modal detail-->


<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No FLK</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nossp" placeholder="No FLK" disabled>
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

          </div>

          <div class="row">

            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noout" placeholder="" disabled>
              </div>
            </div>




            <!-- <div class="col-2">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div> -->


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">

        </div>
        <div class="container-fluid">
          <!-- koreksi add -->
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
                <button type="button" class="btn btn-secondary" onclick="" >Batal</button>
                <button type="button" onclick="submitAddKoreksi()" class="btn btn-primary" >Add Item</button>
              </div>

            </div>
            <div class="line"></div>
          </div>

          <!-- koreksi edit -->

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

          <!-- end  -->
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
  </div>
</div>
</div>
</div>
<!-- End modal koreksi-->

<!-- start modal detail koreksi -->
<div class="modal fade" id="formKoreksiDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Penerimaan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No FLK</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nossp" placeholder="No FLK" disabled>
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

          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_noout" placeholder="" disabled>
              </div>
            </div>



            <!-- <div class="col-2">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div> -->


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <!-- <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary"  >Add Item</button> -->
        </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="koreksiDetailTable" class="table table-bordered table-striped"  >
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
  </div>
</div>
</div>
</div>
<!-- End modal detail koreksi-->




@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefreshOutstanding = []
let dataRefreshPenerimaan = []


let addDataArray = []

let koreksiPenerimaanArray = []
let koreksiDataEdit = {}
let koreksiDataAddList = []

$(document).ready(function(){
      $("#tabel").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});
      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});


});


function buttonAdd (NoBukti) {
  console.log('button add')
  console.log(NoBukti)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }




  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingtransferlokasiout') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NoBukti: NoBukti
    },
    success: function(res) {
      console.log(res)
      addDataArray = res
      console.log('===========================')
    }
  })
  //
  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  console.log('NB' , addDataArray[0].NOBUKTI)
  document.getElementById("input_add_noout").value = addDataArray[0].NOBUKTI
  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP

  $.ajax({
    url: "{!! url('getnobuktitransferlokasiout') !!}",
    type: "get",
    async: false,
    data: {
      nobukti: addDataArray[0].NOBUKTI
    },
    success: function(res) {
      console.log(res, 'NoBUkti')
      console.log(res[0].Nobukti , res[0].Nourut)
      console.log('===============')
      document.getElementById("input_add_nossp").value = res[0].Nobukti
      document.getElementById("input_add_noUrut").value = res[0].Nourut
    },
    error: function(err) {
      console.log(err)
    }
  })
  //
  let rowTable = ""
  addDataArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.QNT) {
      qnt = parseFloat(item.QNT).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
    <td>${item.NOBUKTI}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-right">${qntos}</td>
    <td>${item.Satuan}</td>
    <td class="text-center"><input onchange="" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${qntos}></td>

    </tr>`
  });
  document.getElementById("addTableData").innerHTML = rowTable
  addDataArray.forEach((item, i) => {
  //  JsBarcode(`#barcodeAdd${i}`, item.KODEBRG ,{width: 2, height: 25,});
  });
  $("#form").modal('toggle')


}

function buttonBatalShowHide() {
  $('.showhide').hide();
}

function refreshKoreksi (NOBUKTI) {
  $('.showhide').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaantransferlokasiout') !!}",
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
    $("#formKoreksi").modal('toggle')
    return
  }

  let nooutbrg = ''
  console.log(koreksiPenerimaanArray[0])
  if (koreksiPenerimaanArray[0].NooutBRg) {
    nooutbrg = koreksiPenerimaanArray[0].NooutBRg
  }

  document.getElementById("input_koreksi_noout").value = nooutbrg
  document.getElementById("input_koreksi_nossp").value = koreksiPenerimaanArray[0].NOBUKTI
  // document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  // document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

  let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_koreksi_tanggal').val(date1)


  let rowTable = ""
  koreksiPenerimaanArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.Qntx) {
      qnt = parseFloat(item.Qntx).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-right">${qntos}</td>
    <td>${item.Satuan}</td>

    <td class="text-center">
    <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable


  // koreksiPenerimaanArray.forEach((item, i) => {
  //   //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
  // });
  console.log('!!!!!!!!!!!!!!!')
  console.log(koreksiPenerimaanArray[0].NOBUKTI, koreksiPenerimaanArray[0].NooutBRg,'============')








}

function buttonKoreksi(NOBUKTI) {
  console.log('button koreksi')
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaantransferlokasiout') !!}",
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
  document.getElementById("input_koreksi_nossp").value = koreksiPenerimaanArray[0].NOBUKTI
  // document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  // document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

  let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_koreksi_tanggal').val(date1)


  let rowTable = ""
  koreksiPenerimaanArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.Qntx) {
      qnt = parseFloat(item.Qntx).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-right">${qntos}</td>
    <td>${item.Satuan}</td>

    <td class="text-center">

    <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable


  // koreksiPenerimaanArray.forEach((item, i) => {
  //   //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1,  height: 25,   });
  // });













  $("#formKoreksi").modal('toggle')
}

function buttonKoreksiAdd() {
  $('.showhide').hide();
  document.getElementById("koreksiAddKodeBrg").value = ""
  document.getElementById("koreksiAddNamaBrg").value = ""
  document.getElementById("koreksiAddQtyOS").value = 0.00
  document.getElementById("koreksiAddQtyPO").value = 0.00
  document.getElementById("koreksiAddInputQty").value = "0.00"
  $('#formKoreksiAdd').show();
}

function buttonKoreksiEdit(index) {

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
  if (koreksiDataEdit.Qntx) {

    qnt = parseFloat(koreksiDataEdit.Qntx).toFixed(2)
  }
  document.getElementById("koreksiEditKodeBrg").value = koreksiDataEdit.KODEBRG
  document.getElementById("koreksiEditNamaBrg").value = koreksiDataEdit.NAMABRG
  document.getElementById("koreksiEditQtyOS").value = Number(koreksiDataEdit.QntOS) ? koreksiDataEdit.QntOS : '0.00'
  document.getElementById("koreksiEditInputQty").value = qnt
  document.getElementById("koreksiEditSatuan").value = koreksiDataEdit.Satuan
  $('#formKoreksiEdit').show();
  document.getElementById('formKoreksiEdit').scrollIntoView();
}

// function changeSelectKoreksiAdd() {
//   let indexBarang = document.getElementById("koreksiAddSelect").value;
//   console.log(indexBarang)
//   console.log(koreksiDataAddList[indexBarang])
//   let qnt = 0.00
//   let qntos = 0.00
//   if(koreksiDataAddList[indexBarang].QNT) {
//     qnt = parseFloat(koreksiDataAddList[indexBarang].QNT).toFixed(2)
//   }
//   if(koreksiDataAddList[indexBarang].QntOS) {
//     qntos = parseFloat(koreksiDataAddList[indexBarang].QntOS).toFixed(2)
//   }
//   document.getElementById("koreksiAddKodeBrg").value = koreksiDataAddList[indexBarang].KODEBRG
//   document.getElementById("koreksiAddNamaBrg").value = koreksiDataAddList[indexBarang].NAMABRG
//   document.getElementById("koreksiAddQtyOS").value = qntos
//   document.getElementById("koreksiAddQtyPO").value = qnt
//   document.getElementById("koreksiAddSatuan").value = koreksiDataAddList[indexBarang].Satuan
//
// }

function printBarcode (idBarcode , idJumlah) {
  console.log(idBarcode , idJumlah)
  let printContent = document.getElementById(`${idBarcode}`).innerHTML;
  let jumlahPrint = $(`#${idJumlah}`).val();
  console.log(jumlahPrint)
  let printContent1 = ""
  for (let i=0; i < jumlahPrint ; i++ ) {
    printContent1 += `<div class="row">`
    printContent1 += printContent
    printContent1 += `</div>`
  }
  document.getElementById("printContainer").innerHTML = printContent1
  w=window.open(' ');

  w.document.write($(`#printContainer`).html());
  w.print();
  w.close();
}

function submitAdd() {
  console.log('Submit Add')
  let _token = $("#_token").val();
  let tempData = []

  // console.log('TES ==========')
  // return

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
    console.log(item , '==================')
    console.log(Number(item.inputQntTerima) ,Number(item.QntOS) )
    if (Number(item.inputQntTerima) > Number(item.QntOS)) {
      console.log('os')

      // return
      flag =true
    }
    if (Number(item.inputQntTerima) < 0) {
      console.log('negatif')

      // return
      flag =true
    }
  });
  if (flag) {
    alertify.warning("Qty tidak bisa negatif / melebihi OS");
    return
  }
  console.log('lolos')
  // return


  let inputDate = $("#input_add_tanggal").val();
  let noout = $(`#input_add_noout`).val();
  let nossp = $(`#input_add_nossp`).val();
  let nourut =  $(`#input_add_noUrut`).val();

  console.log('noout', noout)
  console.log('nossp', nossp)
  console.log('nourut', nourut)
  console.log('inputDate', inputDate)
  console.log(tempData)

  let checkDate = new Date(inputDate)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  // return
  // return
  // return
  // return

  // return
  $.ajax({
    url: "{!! url('addtransferlokasiout') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nossp,
      noout,
      nourut,
      tempData,
      inputDate
    },
    success: function(res) {
      // if (res == 2) {
      //   setNewNoBukti()
      //   alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      // }
      if (res == 3 ) {
        alertify.warning('Stok gudang tidak mencukupi');
      }

      if (res == 1) {
        console.log(res ,'!')
        $("#form").modal('toggle')
        alertify.success('Transfer Lokasi Out telah ditambah');
        loadAll()
      }
      if (res == 2) {
        console.log(res)
        // setNewNoBukti()
        $.ajax({
          url: "{!! url('getnobuktitransferlokasiout') !!}",
          type: "get",
          async: false,
          data: {
            nobukti: nossp
          },
          success: function(res) {
            console.log(res, 'NoBUkti')
            console.log(res[0].Nobukti , res[0].Nourut)
            console.log('===============')
            document.getElementById("input_add_nossp").value = res[0].Nobukti
            document.getElementById("input_add_noUrut").value = res[0].Nourut
          },
          error: function(err) {
            console.log(err)
          }
        })
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
    })



}


function loadAll () {
  $.ajax({
    url: "{!! url('loadalltransferlokasiout') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res)
      dataRefreshOutstanding = res.outstandingArray
      dataRefreshPenerimaan = res.penerimaanArray

      document.getElementById("akses_istambah").value = res.akses.ISTAMBAH
      document.getElementById("akses_iskoreksi").value = res.akses.ISKOREKSI
      document.getElementById("akses_iscetak").value = res.akses.ISCETAK
      document.getElementById("akses_ishapus").value = res.akses.ISHAPUS
    }
  })


      $('#tabel').DataTable().destroy();
      let rowTable = ""
      dataRefreshOutstanding.forEach((item, i) => {
        // console.log(item,'==================')
        let date1 = ""
        if (item[0].TANGGAL) {
          let date = new Date(item[0].TANGGAL);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }
        rowTable  += `<tr>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
        <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item[0].NOBUKTI}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button></td>
        <td>${item[0].NOBUKTI}</td>
        <td>${date1}</td>
        </tr>`
      })



      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});




      $('#tabelRetur').DataTable().destroy();
      let rowTableRetur = ""

      dataRefreshPenerimaan.forEach((item, i) => {
        let date1 = ""
        // console.log(item[0].TANGGAL)
        // return
        if (item[0].TANGGAL) {
          let date = new Date(item[0].TANGGAL);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }

        rowTableRetur += `<tr>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NOBUKTI}')"><i class="bi bi-pen"></i></button>
        </td>
        <td>${item[0].NOBUKTI}</td>
        <td>${date1}</td>
        </tr>`
      });



      document.getElementById("tabelRetur_data").innerHTML = rowTableRetur

      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});


}

function buttonDetail (NoBukti) {
  console.log(NoBukti)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingtransferlokasiout') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NoBukti,
    },
    success: function(res) {
      console.log(res)
      // addDataArray = res
      console.log('===========================')
      document.getElementById("input_detail_noout").value = res[0].NOBUKTI
      // document.getElementById("input_detail_gdg").value = res[0].NamaGudangAsal
      // document.getElementById("input_detail_namacust").value = res[0].NAMACUSTSUPP
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
        <td>${item.NOBUKTI}</td>
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntos}</td>
        <td>${item.Satuan}</td>
        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable

    }
  })




  $("#formDetail").modal('toggle')

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

  let dataPBG = koreksiDataEdit


  let choice = "I"
  let nossp = dataPBG.NOBUKTI
  let nourut = dataPBG.NOURUT
  let inputDate = $("#input_koreksi_tanggal").val()
  let note = ""
  let urut = 0
  let kodebrg = dataOut.KODEBRG
  let gdgasal = dataPBG.Kodegdg
  let gdgtujuan = "GSAMPLE"
  let sat_1 = dataOut.SAT1
  let sat_2 = dataOut.SAT2
  // let qntTerima = //
  // let qntTerima2 = //
  let nosat = dataOut.NOSAT
  let isi = dataOut.ISI
  // user
  let kodecustsupp = dataOut.KODECUSTSUPP
  let kodesls = dataOut.KODESLS
  let pbonus = 0
  let maxol = 0
  let nopr = ""
  let urutpr = 0
  let pkonsi = 0
  let nooutbrg = dataOut.NOBUKTI
  let urutoutbrg = dataOut.Urut

  console.log('choice' , choice)
  console.log('nossp' , nossp)
  console.log('note' , note)
  console.log('urut' , urut)
  console.log('kodebrg' , kodebrg)
  console.log('gdgasal' , gdgasal)
  console.log('sat_1' , sat_1)
  console.log('sat_2' , sat_2)
  console.log('qntTerima' , qntTerima)
  console.log('qntTerima2' , qntTerima2)
  console.log('nosat' , nosat)
  console.log('isi' , isi)
  console.log('kodecustsupp' , kodecustsupp)
  console.log('kodesls' , kodesls)
  console.log('pbonus' , pbonus)
  console.log('maxol' , maxol)
  console.log('nopr' , nopr)
  console.log('urutpr' , urutpr)
  console.log('pkonsi' , pkonsi)
  console.log('nooutbrg' , nooutbrg)
  console.log('urutoutbrg' , urutoutbrg)


  $.ajax({
    url: "{!! url('koreksipenyerahansample') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nossp,
      nourut,
      inputDate,
      note,
      urut,
      kodebrg,
      gdgasal,
      gdgtujuan,
      sat_1,
      sat_2,
      qntTerima,
      qntTerima2,
      nosat,
      isi,
      kodecustsupp,
      kodesls,
      pbonus,
      maxol,
      nopr,
      urutpr,
      pkonsi,
      nooutbrg,
      urutoutbrg,
    },
    success: function(res) {
      console.log(res ,'succes add koreksi')
      refreshKoreksi(nossp)
      alertify.success('Item telah ditambah');
      loadAll()
    }
  })
}


function submitEditKoreksi () {
  // console.log('submit edit koreksi')
  // console.log(koreksiDataEdit)
  let dataPBG = koreksiDataEdit

  let _token = $("#_token").val();

  let qntTerima = $("#koreksiEditInputQty").val()
  // console.log(dataPBG)
  // console.log(qntTerima)
  if (Number(qntTerima) > Number(dataPBG.QntOS) + Number(dataPBG.Qntx)) {
    alertify.warning("Qty melebihi Qnt OS");
    return
  }
  if (Number(qntTerima) <= 0) {
    alertify.warning("Qty tidak bisa 0 atau negatif");
    return
  }
  console.log('lolos')
  // console.log(dataPBG.NOSAT)
  // console.log(dataPBG.ISI2)
  let qntTerima1 = 0
  let qntTerima2 = 0
  if (dataPBG.NOSAT == 1) {
    qntTerima1 = qntTerima
    qntTerima2 = qntTerima / dataPBG.ISI2
  } else if (dataPBG.NOSAT == 2) {
    qntTerima1 = qntTerima * dataPBG.ISI2
    qntTerima2 = qntTerima
  }

    let choice = "U"
    let nossp = dataPBG.NOBUKTI
    let nourut = dataPBG.NOURUT
    let inputDate = $("#input_koreksi_tanggal").val()
    let note = ""
    let urut = dataPBG.URUT
    let kodebrg = dataPBG.KODEBRG
    let gdgasal = dataPBG.GDGASAL
    let gdgtujuan = dataPBG.GDGTUJUAN
    let satuan = dataPBG.Satuan
    let sat_1 = dataPBG.SAT1
    let sat_2 = dataPBG.SAT2
    // let qntTerima = //
    // let qntTerima2 = //
    let nosat = dataPBG.NOSAT
    let isi = dataPBG.ISI
    // user
    let kodecustsupp = dataPBG.KODECUSTSUPP
    let kodesls = dataPBG.KODESLS
    let pbonus = 0
    let maxol = 0
    let nopr = ""
    let urutpr = 0
    let pkonsi = 0
    let nooutbrg = ''
    let urutoutbrg = 0
    let qntDB = 0


    // console.log('choice' , choice)
    // console.log('nossp' , nossp)
    // console.log('nourut' , nourut)
    // console.log('note' , note)
    // console.log('urut' , urut)
    // console.log('kodebrg' , kodebrg)
    // console.log('gdgasal' , gdgasal)
    // console.log('gdgtujuan' , gdgtujuan)
    // console.log('satuan' , satuan)
    // console.log('qntTerima' , qntTerima)
    // console.log('nosat' , nosat)
    // console.log('isi' , isi)
    // console.log('qntDB' , qntDB)
    // console.log('qntTerima' , qntTerima)

    console.log('choice' , choice)
    console.log('nossp' , nossp)
    console.log('nourut' , nourut)
    console.log('inputDate' , inputDate)
    console.log('note' , note)
    console.log('urut' , urut)
    console.log('kodebrg' , kodebrg)
    console.log('gdgasal' , gdgasal)
    console.log('gdgtujuan' , gdgtujuan)
    console.log('sat_1' , sat_1)
    console.log('sat_2' , sat_2)
    console.log('qntTerima' , qntTerima)
    console.log('qntTerima2' , qntTerima2)
    console.log('nosat' , nosat)
    console.log('isi' , isi)
    // console.log('nopenyerahan' , nopenyerahan)
    // console.log('nooutbrg' , nooutbrg)
    // console.log('urutoutbrg' , urutoutbrg)
    console.log('qntx' , qntTerima)

    // return
    $.ajax({
      url: "{!! url('koreksitransferlokasiout') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        nossp,
        nourut,
        inputDate,
        note,
        urut,
        kodebrg,
        gdgasal,
        gdgtujuan,
        sat_1,
        sat_2,
        qntTerima,
        qntTerima2,
        nosat,
        isi,
        nopenyerahan: '',
        nooutbrg: '',
        urutoutbrg: 0,
        qntx: qntTerima,
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(nossp)
        alertify.success('Item telah diedt');
        loadAll()
      },
      error: function (err) {
        console.log('masuk error')
        console.log(err)
        alertify.error('error')
      }
    })
}




function buttonKoreksiDelete(index) {

  console.log('buttonKoreksiDelete')
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let dataPBG = koreksiPenerimaanArray[index]
  console.log(dataPBG)
  // return

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataPBG.KODEBRG + dataPBG.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();

        let choice = "D"
        let nossp = dataPBG.NOBUKTI
        let nourut = dataPBG.NOURUT
        let inputDate = $("#input_koreksi_tanggal").val()
        let note = ""
        let urut = dataPBG.URUT
        let kodebrg = dataPBG.KODEBRG
        let gdgasal = dataPBG.GDGASAL
        let gdgtujuan = dataPBG.GDGTUJUAN
        let sat_1 = dataPBG.SAT1
        let sat_2 = dataPBG.SAT2
        // let qntTerima = //
        // let qntTerima2 = //
        let nosat = dataPBG.NOSAT
        let isi = dataPBG.ISI
        // user
        let kodecustsupp = dataPBG.KODECUSTSUPP
        let kodesls = dataPBG.KODESLS
        let pbonus = 0
        let maxol = 0
        let nopr = ""
        let urutpr = 0
        let pkonsi = 0
        let nooutbrg = ''
        let urutoutbrg = 0
        let qntTerima = 0
        let qntTerima2 = 0
        let nopenyerahan = ''




        console.log('choice' , choice)
        console.log('nossp' , nossp)
        console.log('nourut' , nourut)
        console.log('inputDate' , inputDate)
        console.log('note' , note)
        console.log('urut' , urut)
        console.log('kodebrg' , kodebrg)
        console.log('gdgasal' , gdgasal)
        console.log('gdgtujuan' , gdgtujuan)
        console.log('sat_1' , sat_1)
        console.log('sat_2' , sat_2)
        console.log('qntTerima' , qntTerima)
        console.log('qntTerima2' , qntTerima2)
        console.log('nosat' , nosat)
        console.log('isi' , isi)
        console.log('nopenyerahan' , nopenyerahan)
        console.log('nooutbrg' , nooutbrg)
        console.log('urutoutbrg' , urutoutbrg)
        console.log('qntx' , qntTerima)

        // return
        $.ajax({
          url: "{!! url('koreksitransferlokasiout') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nossp,
            nourut,
            inputDate,
            note :'',
            urut,
            kodebrg: '',
            gdgasal: '',
            gdgtujuan: '',
            sat_1: '',
            sat_2: '',
            qntTerima: 0,
            qntTerima2: 0,
            nosat: 0,
            isi: 0,
            nopenyerahan: '',
            nooutbrg: '',
            urutoutbrg: 0,
            qntx: qntTerima,
          },
          success: function(res) {
            console.log(res ,'succes delete koreksi')
            refreshKoreksi(nossp)
            alertify.success('Item telah didelete');
            loadAll()
          }
        })


      }
    ,function(){
      console.log('no')
    });
}


function buttonDetailKoreksi (NOBUKTI) {
  console.log('button detail koreksi')
  console.log(NOBUKTI)

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaantransferlokasiout') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      // koreksiPenerimaanArray = res
      // koreksiDataEdit = res[0]
      console.log(res[0])

      let nooutbrg = ''
      console.log(res[0])
      if (res[0].NooutBRg) {
        nooutbrg = res[0].NooutBRg
      }
      // document.getElementById("input_koreksidetail_namacust").value = res[0].NAMACUSTSUPP
      document.getElementById("input_koreksidetail_noout").value = nooutbrg
      document.getElementById("input_koreksidetail_nossp").value = res[0].NOBUKTI
      // document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_koreksidetail_tanggal').val(date1)



      let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.Qntx) {
          qnt = parseFloat(item.Qntx).toFixed(2)
        }
        if(item.QntOS) {
          qntos = parseFloat(item.QntOS).toFixed(2)
        }
        rowTable += `<tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntos}</td>
        <td>${item.Satuan}</td>

        </tr>`
      });
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable



    }
  })

  $("#formKoreksiDetail").modal('toggle')

}

</script>




@endsection
