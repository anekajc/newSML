@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Putaway')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">


  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      {{-- <h1>Putaway</h1> - judul pindah ke header (page-title) --}}
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Put Away Gudang</button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>


<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid" style="max-width: 800px">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
          <div class="row mt-4">
              <!-- <div class="col-12 text-right">
                  <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Koreksi Stock Gudang</button>
              </div> -->
          </div>
          <!-- <div class="container-fluid mt-4"> -->
            <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

              <div class="row">
                <!-- <div class="col-2">
                </div> -->
                <div class="col-7 text-left">
                  <div class="form-group form-group-lg text-left">
                    <label class="text-left">Barcode</label>
                  </div>
                </div>
                <!-- <div class="col-6">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_kode1" placeholder="Barcode Barang" oninput="enterScannerKode1(event)">
                  </div>
                </div> -->
                <div class="col-5 text-right">
                  <div class="form-group">
                <button onclick="resetScannerKode1()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
                </div>
                <!-- <div class="col-2">
                </div> -->


              </div>
              <div class="row">
                <!-- <div class="col-2">
                </div> -->
                <div class="col-12 form-group form-group-lg">
                  <div class="form-group form-group-lg">
                    <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode1" placeholder="Barcode" oninput="enterScannerKode1(event)" autofocus>
                    <!-- <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg" aria-label=".form-control-lg example"> -->

                  </div>
                </div>
                <!-- <div class="col-2">
                </div> -->
              </div>

            <div class="row">
              <!-- <div class="col-2">
              </div> -->
              <div class="col-7">
                <div class="form-group">
                  <label>barcode Lokasi</label>
                </div>
              </div>
              <div class="col-5 text-right">
                <div class="form-group">
              <button onclick="resetScannerKode2()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
              </div>
              <!-- <div class="col-2">
              </div> -->


            </div>
            <div class="row">
              <!-- <div class="col-2">
              </div> -->
              <div class="col-12">
                <div class="form-group">
                  <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode2" placeholder="Barcode Lokasi" oninput="enterScannerKode2(event)">
                </div>
              </div>
              <!-- <div class="col-2">
              </div> -->

            </div>

            <div class="row">
              <!-- <div class="col-2">
              </div> -->
              <div class="col-7">
                <div class="form-group">
                  <label>barcode barang</label>
                </div>
              </div>
              <div class="col-5 text-right">
                <div class="form-group">
              <button onclick="resetScannerKode3()"  class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
              </div>
              <!-- <div class="col-2">
              </div> -->


            </div>
            <div class="row">
              <!-- <div class="col-2">
              </div> -->
              <div class="col-12">
                <div class="form-group">
                  <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode3" placeholder="Barcode Barang" oninput="enterScannerKode3(event)">
                </div>
              </div>
              <!-- <div class="col-2">
              </div> -->

            </div>


          <!-- <div class="container-fluid">
            <div class="row ">
              <div class="col-md-12 text-right">
              <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
          </div>

          </div>



          </div> -->

      <!-- </div> -->

      <div class="container-lg mt-4">
        <div class="row" id="listLokasi" >

        </div>
      </div>


</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 600px"  role="document">
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
              <div class="col-4 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Barcode Barang TES 1</label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kode1" placeholder="Barcode Barang" oninput="enterScannerKode1(event)">
                </div>
              </div>

              <div class="col-2 text-right">
                <div class="form-group">
              <button onclick="resetScannerKode1()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
              </div>

            </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>barcode Lokasi</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kode2" placeholder="Barcode Lokasi" onkeypress="enterScannerKode2(event)">
              </div>
            </div>

            <div class="col-2 text-right">
              <div class="form-group">
            <button onclick="resetScannerKode2()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
            </div>







          </div>


        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->

    </div>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal detail koreksi -->
<div class="modal fade" id="formDetailKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Putaway</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input style="font-size: 15px" type="text" class="form-control" id="input_detailKoreksi_nobukti"  disabled>
              </div>
            </div>
            <!-- <div class="col-">
            </div> -->
            <!-- <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detailKoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div> -->
            <div class="col-12">
              <div class="form-group">
                <label>Kode Lokasi</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input style="font-size: 15px" type="text" class="form-control" id="input_detailKoreksi_kodelokasi"  disabled>
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
              <div class="form-group">
                <input style="font-size: 15px" type="text" class="form-control" id="input_detailKoreksi_kodebarang"  disabled>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Nama Barang</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <textarea style='font-size: 15px' class="form-control" id="input_detailKoreksi_namabarang" rows="3" disabled></textarea>
                <!-- <textarea>RXP2/812/C2S/12.5/ECE-VE/ARB/UB/M1-AI UNIVERSAL PARALLEL SHAFT GEARBOX</textarea> -->
                <!-- <input type="text" rows='5' class="form-control" id="input_detailKoreksi_namabarang" value='4.1 Inch P WELDED BUSH HEAVY DUTY CHAIN WITH FLIGHT ATT. EV 6TH PITCH'   disabled> -->
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Qty</label>
              </div>
            </div>
            <div class="col-8">
              <div class="form-group">
                <input style="font-size: 15px" type="number" class="form-control text-right" id="input_detailKoreksi_qty" value='0.00'  disabled>
              </div>
            </div>
            <div class="col-4">
              <input style="font-size: 15px" type="text" class="form-control" id="input_detailKoreksi_satuan"  disabled>
            </div>

          </div>





        <!-- <div class="container-fluid mt-4" style="overflow-x: auto;">

              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                  </tr>
                </thead>


                <tbody id="detailKoreksiTableData" class="text-right" >
                  <tr >
                    <td colspan="">Tes</td>
                    <td colspan="">Tes</td>
                    <td colspan="">Tes</td>
                    <td colspan="">Tes</td>
                </tr>

                </tbody>


              </table>
        </div> -->



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal detail koreksi-->


<!-- start modal detail koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nopen" value="TES/TES/00001"  disabled>
              </div>
            </div>
            <!-- <div class="col-">
            </div> -->
            <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>


          </div>




        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->



        <div class="container-fluid mt-4" style="overflow-x: auto;">

              <table id="koreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="koreksiTableData" class="text-right" >
                  <tr >
                    <td colspan="3">Text</td>
                </tr>

                </tbody>


              </table>
        </div>

        <!-- koreksi edit -->

        <div id="formKoreksiEdit" class="container-fluid showhide">
          <div class="line"></div>
          <div class="row">
            <div class="col-4">
              <h4>Edit Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-2">
              <div class="form-group">
              <label>Kode Barang</label>
            </div>
            </div>
            <div class="col-6">
              <input id="koreksiEditKodeBrg" type="text" class="form-control" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-2">
              <div class="form-group">
              <label>Nama Barang</label>
            </div>
            </div>
            <div class="col-6">
              <input id="koreksiEditNamaBrg" type="text" class="form-control" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-1">
              <div class="form-group">
              <label>Qty</label>
            </div>
            </div>
            <div class="col-3">
              <input id="koreksiEditInputQty" type="number" value=0.00 class="form-control">
            </div>
            <div class="col-1">
              <div class="form-group">
              <label>Satuan</label>
            </div>
            </div>
            <div class="col-3">

                <input type="text" id="koreksiEditSatuan" value="PCS" class="form-control" disabled>
            </div>
          </div>
          <!-- <div class="row">
            <div class="col-1">
              <div class="form-group">
              <label>Qty OS</label>
            </div>
            </div>
            <div class="col-3">
              <input id="koreksiEditQtyOS" type="number" value=0.00 class="form-control" disabled>
            </div>

          </div> -->
          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="hideShowhide()" >Batal</button>
              <button type="button" onclick="submitEditKoreksi()" class="btn btn-primary" >Edit Item</button>
            </div>

          </div>
        </div>

        <!-- end  -->

      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div> -->
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

  let koreksiDataArray = []
  let koreksiDataEdit = {}

  let dataRefreshPenerimaan = []

  let listLokasi = []

  let tempAddData = []


  $(document).ready(function(){
        // $("#tabel").DataTable({
        //   "lengthChange": false,
        //     "paging": false ,
        //      "columnDefs": [
        //   { "type": "date", "targets": [1] },
        //   {  "className": "text-center", "targets": [3] },
        // ]});
        // // new QRCode(document.getElementById("qrcode"), `asdasdadsadsaa`);

        $('#input_add_kode1').trigger('focus')
        // $("#formDetailKoreksi").modal('toggle')

  });

  function submitAdd () {
    console.log(tempAddData[0])
    let _token = $("#_token").val();
    let nobukti = tempAddData[0].NoBukti1
    let nobarang = tempAddData[0].Kodebrg
    let barcodelokasi = $("#input_detailKoreksi_kodelokasi").val();
    let urut = tempAddData[0].Urut
    let qntShow = tempAddData[0].qntShow

    console.log('nobukti' , nobukti)
    console.log('nobarang' , nobarang)
    console.log('barcodelokasi' , barcodelokasi)
    console.log('urut' , urut)
    console.log('qntShow' , qntShow)


    // return
    $.ajax({
      url: "{!! url('addputaway') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nobukti,
        nobarang,
        barcodelokasi,
        urut,
        qntShow

      },
      success: function(res) {
        console.log(res ,'ress =============================')
        // console.log(res)
        // item = res[0]
        if (res === '1') {
          $("#formDetailKoreksi").modal('toggle')
          resetScannerKode1()
          // document.getElementById("input_add_kode1").value = ''
          // document.getElementById("input_add_kode2").value = ''
          // loadAll()
          // $('#input_add_kode1').focus();
          alertify.success('Put Away telah ditambah')
        } else {

          alertify.warning('Terjadi kesalahan, silahkan input ulang')
        }

      }
    })
  }

  function buttonAdd () {


        $("#form").modal('toggle')
        $('#form').on('shown.bs.modal', function () {
          $('#input_add_kode1').trigger('focus')
        })
  }

  function loadAll () {
    // Ditambah 2026-10-20: halaman scan ini tidak punya tabel daftar (#tabel / #tabel_data) sejak versi
    // lama, jadi loadAll() setelah edit/hapus koreksi selalu error JS + memuat data sia-sia. Lewati saja.
    if (!document.getElementById("tabel_data")) {
      return
    }
    $.ajax({
      url: "{!! url('loadallputaway') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res)
        dataRefreshPenerimaan = res.penerimaanArray
      }
    })

    $('#tabel').DataTable().destroy();
    let rowTable = ""
    dataRefreshPenerimaan.forEach((item, i) => {
      // console.log(item,'==================')
      let date1 = ""
      if (item[0].tanngal) {
        let date = new Date(item[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }
      rowTable  += `<tr>
      <td>${item[0].NOBUKTI}</td>
      <td>${date1}</td>
      <td>${item[0].NAMAGUDANG}</td>
      <td class="text-center">
      <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
      <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item[0].NOBUKTI}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
      </tr>`
    })



    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
      "lengthChange": false,
        "paging": false ,
         "columnDefs": [
      { "type": "date", "targets": [1] },
      {  "className": "text-center", "targets": [3] },
    ]});
  }



  function refreshKoreksi(NOBUKTI) {
    $('.showhide').hide();
    console.log(NOBUKTI)
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpenerimaanputaway') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,

      },
      success: function(res) {
        console.log(res ,'ress =============================')
        koreksiDataArray = res
        // let rowTable = ""
        // res.forEach((item, i) => {
        //
        //   rowTable += `<tr>
        //   <td>${item.KODEBRG}</td>
        //   <td>${item.NamaBrg}</td>
        //   <td>${item.QNT}</td>
        //   <td>${item.SATUAN}</td>
        //   <td class="text-center">
        //   <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
        //   <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})"><i class="bi bi-trash"></i></button>
        //   </td>
        //   </tr>`
        // });
        // console.log('tes2')
        // console.log(rowTable)
        // document.getElementById("koreksiTableData").innerHTML = rowTable
      }
    })

    if (!koreksiDataArray.length) {
      alertify.success('Item habis');
      $("#formKoreksi").modal('toggle')
      return
    }
    let rowTable = ""
    koreksiDataArray.forEach((item, i) => {

      rowTable += `<tr>
      <td>${item.KODEBRG}</td>
      <td>${item.NamaBrg}</td>
      <td>${item.QNT}</td>
      <td>${item.SATUAN}</td>
      <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})"><i class="bi bi-trash"></i></button>
      </td>
      </tr>`
    });
    console.log('tes2')
    // console.log(rowTable)
    document.getElementById("koreksiTableData").innerHTML = rowTable

  }

  function buttonKoreksi(NOBUKTI) {
    $('.showhide').hide();
    console.log(NOBUKTI)
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpenerimaanputaway') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,

      },
      success: function(res) {
        console.log(res ,'ress =============================')
        koreksiDataArray = res
        let rowTable = ""
        res.forEach((item, i) => {

          // console.log(item.kodebrg)
          rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NamaBrg}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td class="text-center">
          <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
          <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})"><i class="bi bi-trash"></i></button>
          </td>
          </tr>`
        });
      //   <td>${item.KODEBRG}</td>
      //   <td>${item.NamaBrg}</td>
      //   <td>${item.QNT}</td>
      //   <td>${item.SATUAN}</td>
      //   <td><button class="btn btn-danger btn-sm" type="button" onclick=""><i class="bi bi-trash"></i></button></td>
      // </td>
        console.log('tes2')
        // console.log(rowTable)
        document.getElementById("koreksiTableData").innerHTML = rowTable


        document.getElementById("input_koreksi_nopen").value = res[0].NOBUKTI

        let date = new Date(res[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#input_koreksi_tanggal').val(date1)
        // document.getElementById("input_koreksi_tanggal").value = res[0].tanggal
        // document.getElementById("koreksiEditInputQty").value = koreksiDataArray[index].QNT
      }
    })
    $("#formKoreksi").modal('toggle')
  }

  function buttonKoreksiEdit (index) {
    console.log(index)
    console.log(koreksiDataArray[index])
    koreksiDataEdit = koreksiDataArray[index]

    document.getElementById("koreksiEditKodeBrg").value = koreksiDataArray[index].KODEBRG
    document.getElementById("koreksiEditNamaBrg").value = koreksiDataArray[index].NamaBrg
    document.getElementById("koreksiEditInputQty").value = koreksiDataArray[index].QNT

    $('#formKoreksiEdit').show();
  }



  function buttonKoreksiDelete (index) {
    let data = koreksiDataArray[index]
    console.log(data)

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + data.KODEBRG + data.NamaBrg + ' ?',
        function() {
          let _token = $("#_token").val();

          // let _token = $("#_token").val();
          // let inputQnt = $('#koreksiEditInputQty').val()
          let namaBrg = data.NamaBrg
          let kodeBrg = data.KODEBRG
          let urut = data.URUT
          let choice = "D"
          let noBukti = data.NOBUKTI

          $.ajax({
            url: "{!! url('koreksiputaway') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              choice,
              inputQnt: 0,
              nobukti: noBukti,
              urut,
              kodeBrg

            },
            success: function(res) {
              console.log(res ,'succes delete koreksi')
              refreshKoreksi(noBukti)
              alertify.success('Item telah didelete');
              loadAll()
            }
          })


        }
      ,function(){
        console.log('no')
      });
  }

  function submitEditKoreksi () {
    let _token = $("#_token").val();
    let inputQnt = $('#koreksiEditInputQty').val()
    let namaBrg = koreksiDataEdit.NamaBrg
    let kodeBrg = koreksiDataEdit.KODEBRG
    let urut = koreksiDataEdit.URUT
    let choice = "U"
    let noBukti = koreksiDataEdit.NOBUKTI

    console.log(namaBrg, kodeBrg, noBukti)
    console.log(inputQnt, urut)


    $.ajax({
      url: "{!! url('koreksiputaway') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        inputQnt,
        nobukti: noBukti,
        urut,
        kodeBrg

      },
      success: function(res) {
        console.log(res ,'ress =============================')
        refreshKoreksi(noBukti)
        alertify.success('Item telah diedit');
        loadAll()

      }
    })


  }

  function hideShowhide () {
    $('.showhide').hide();
  }

  function buttonDetailKoreksi(NOBUKTI) {
    console.log(NOBUKTI)
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpenerimaanputaway') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,

      },
      success: function(res) {
        console.log(res ,'ress =============================')
        let rowTable = ""
        res.forEach((item, i) => {

          // console.log(item.kodebrg)
          rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NamaBrg}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          </tr>`
        });
      //   <td>${item.KODEBRG}</td>
      //   <td>${item.NamaBrg}</td>
      //   <td>${item.QNT}</td>
      //   <td>${item.SATUAN}</td>
      //   <td><button class="btn btn-danger btn-sm" type="button" onclick=""><i class="bi bi-trash"></i></button></td>
      // </td>
        console.log('tes2')
        // console.log(rowTable)
        document.getElementById("detailKoreksiTableData").innerHTML = rowTable

        document.getElementById("input_detailKoreksi_nopen").value = res[0].NOBUKTI

        let date = new Date(res[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#input_detailKoreksi_tanggal').val(date1)
        // document.getElementById("input_detailKoreksi_tanggal").value = res[0].TANGGAL
      }
    })


    $("#formDetailKoreksi").modal('toggle')
  }

  function enterScannerKode1(e) {
    // if (e.which === 13) {
    console.log('enterScannerKode1')


      let kode1 = document.getElementById("input_add_kode1").value
      console.log('kode1', kode1)




      let temp1 = kode1.split(".")
      console.log(temp1)
      console.log(temp1.length)

      if (temp1.length != 3) {
        alertify.warning("kode tidak dikenali");
        document.getElementById("input_add_kode1").value = ''
        $('#input_add_kode1').focus();
        return
      }

      // return

      let nobukti = temp1[0]
      let urut = temp1[1]
      let nobarang= temp1[2]

      if (!(nobukti.length === 17 || nobukti.length === 18 || nobukti.length === 19)) {
        alertify.warning("kode tidak dikenali");
        document.getElementById("input_add_kode1").value = ''
        $('#input_add_kode1').focus();
        return
      }

      console.log(temp1)
      console.log(nobukti, urut, nobarang)
      // return


      let _token = $("#_token").val();
      // let item = {}
        $.ajax({
          url: "{!! url('checkaddputaway') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti,
            nobarang,
            urut

          },
          success: function(res) {
            console.log(res ,'ress =============================')
            console.log(1)
            if (res.check.length == 0) {
              console.log(2)
              alertify.warning("periode / barang sudah diterima");
              document.getElementById("input_add_kode1").value = ''
              $('#input_add_kode1').focus();
              return
            } else {
              tempAddData = res.check
              listLokasi = res.lokasi
              console.log(3)
              lokasiListRow = ``

              if (res.lokasi.length < 1) {
                lokasiListRow += `<div class=" col-12 text-center">
                  <h5>Tidak ada lokasi yang sesuai</h5>

                </div>`
              } else {
                res.lokasi.forEach((item, i) => {
                  lokasiListRow += `<div class=" col-6 text-center">
                    <h5>Lokasi: ${item.BarcodeLoc}</h5>
                    <p>Sisa Kapasitas: ${parseFloat(item.SisaKapasitas).toFixed(2)}</p>
                  </div>`
                });
              }


              document.getElementById("listLokasi").innerHTML = lokasiListRow
              document.getElementById("input_add_kode1").readOnly = true;
              document.getElementById("input_add_kode2").value = ''
              $('#input_add_kode2').focus();
            }
            // console.log(res)
            // item = res[0]
            // document.getElementById("input_add_kode1").value = ''
            // document.getElementById("input_add_kode2").value = ''
            // $('#input_add_kode1').focus();
          }
        })



      // $('#input_add_kode2').focus();

      // document.getElementById("input_koreksi_scanner").value = ''

    // }
  }

  function enterScannerKode3(e) {
    // if (e.which === 13) {
    console.log('enterscannerkode3')
      let kode3 = document.getElementById("input_add_kode3").value

      if (tempAddData.length && kode3 != tempAddData[0].Kodebrg) {
        document.getElementById("input_add_kode3").value = ''
        $('#input_add_kode3').focus();
        alertify.warning('barcode barang tidak sesuai')
        return
      }
      console.log('lolos')


      let kode1 = document.getElementById("input_add_kode1").value

      let temp1 = kode1.split(".")
      console.log(temp1)
      console.log(temp1.length)

      if (temp1.length != 3) {
        alertify.warning("kode tidak dikenali");
        resetScannerKode1()
        return
      }

      // return

      let nobukti = temp1[0]
      let urut = temp1[1]
      let nobarang= temp1[2]

      if (!(nobukti.length === 17 || nobukti.length === 18 || nobukti.length === 19)) {
        alertify.warning("kode tidak dikenali");
        // document.getElementById("input_add_kode1").value = ''
        // document.getElementById("input_add_kode2").value = ''
        // $('#input_add_kode1').focus();
        resetScannerKode1()
        return
      }

      console.log(temp1)
      console.log(nobukti, urut, nobarang)


      // let kode4 = kode1.slice(0,18)
      // let kode5 = kode1.slice(18)
      let kode2 = document.getElementById("input_add_kode2").value

      if(!kode2) {
        resetScannerKode2()
        // document.getElementById("input_add_kode2").value = ''
        // $('#input_add_kode2').focus();
        alertify.warning("isi kode gudang");
        return
      }
      // console.log(kode4,kode5,kode2)
      console.log('nobukti', nobukti)
      console.log('nobarang' , nobarang)
      console.log('kodelokasi', kode2)
      console.log('urut' , urut)
      console.log('sebelum reset scanner 0')

      let tempLokasi = listLokasi.filter(function (el){
        return el.BarcodeLoc == kode2
      })
      console.log('sebelum reset scanner 1')

      // let _token = $("#_token").val();
      if (!tempLokasi.length) {
        alertify.warning('Lokasi tidak ada di list')
        resetScannerKode2()
        return
      }
      console.log('sebelum reset scanner 3')

      console.log(Number(tempLokasi[0].SisaKapasitas))
      if (Number(tempLokasi[0].SisaKapasitas) <= 0) {
        alertify.warning('Kapasitas Limit')
        resetScannerKode2()
        return
      }

      let tempQnt = Number(tempLokasi[0].SisaKapasitas)
      //
      if ( Number(tempLokasi[0].SisaKapasitas) > Number(tempAddData[0].QNTSISA) ) {
        tempQnt = Number(tempAddData[0].QNTSISA)
      }

      tempAddData[0].qntShow = tempQnt

      console.log('asd')
      document.getElementById("input_detailKoreksi_nobukti").value = nobukti
      document.getElementById("input_detailKoreksi_kodelokasi").value = kode2
      console.log(tempAddData[0])
      document.getElementById("input_detailKoreksi_kodebarang").value = tempAddData[0].Kodebrg
      document.getElementById("input_detailKoreksi_namabarang").value = tempAddData[0].NAMABRG
      console.log('===')
      document.getElementById("input_detailKoreksi_qty").value = tempAddData[0].qntShow
      console.log('asdads')
      document.getElementById("input_detailKoreksi_satuan").value = tempAddData[0].Satuan
      // document.getElementById("detailKoreksiTableData").innerHTML = `
      // <tr>
      // <td>${tempAddData[0].Kodebrg}</td>
      // <td>${tempAddData[0].NAMABRG}</td>
      // <td>${tempAddData[0].qntShow}</td>
      // <td>${tempAddData[0].Satuan}</td>
      // </tr>
      // `
      $("#formDetailKoreksi").modal('toggle')



    // }
  }

  function enterScannerKode2 (e) {
    // if (e.which === 13) {
    let kode2 = document.getElementById("input_add_kode2").value



    if(!kode2) {

      // document.getElementById("input_add_kode2").value = ''
      // $('#input_add_kode2').focus();
      resetScannerKode2()
      alertify.warning("isi kode gudang");
      return
    }

    if (kode2.includes('REC')) {
      resetScannerKode2()
      alertify.warning("Tidak bisa putaway ke gudang receiving");
      return
    }

    let tempLokasi = listLokasi.filter(function (el){
      return el.BarcodeLoc == kode2
    })

    if (!tempLokasi.length) {
      alertify.warning('Lokasi tidak ada di list')
      resetScannerKode2()
      return
    }
    console.log(Number(tempLokasi[0].SisaKapasitas))
    if (Number(tempLokasi[0].SisaKapasitas) <= 0) {
      alertify.warning('Kapasitas Limit')
      return
    }

    document.getElementById("input_add_kode2").readOnly = true;
    document.getElementById("input_add_kode3").value = ''
    $('#input_add_kode3').focus();

  // }
  }


    function enterScannerKode2lama(e) {
      if (e.which === 13) {
        console.log(listLokasi)
        let kode1 = document.getElementById("input_add_kode1").value

        let temp1 = kode1.split(".")
        console.log(temp1)
        console.log(temp1.length)

        if (temp1.length != 3) {
          alertify.warning("kode tidak dikenali");
          document.getElementById("input_add_kode1").value = ''
          document.getElementById("input_add_kode2").value = ''
          $('#input_add_kode1').focus();
          return
        }

        // return

        let nobukti = temp1[0]
        let urut = temp1[1]
        let nobarang= temp1[2]

        if (!(nobukti.length === 17 || nobukti.length === 18 || nobukti.length === 19)) {
          alertify.warning("kode tidak dikenali");
          document.getElementById("input_add_kode1").value = ''
          document.getElementById("input_add_kode2").value = ''
          $('#input_add_kode1').focus();
          return
        }

        console.log(temp1)
        console.log(nobukti, urut, nobarang)


        // let kode4 = kode1.slice(0,18)
        // let kode5 = kode1.slice(18)
        let kode2 = document.getElementById("input_add_kode2").value

        if(!kode2) {

          document.getElementById("input_add_kode2").value = ''
          $('#input_add_kode2').focus();
          alertify.warning("isi kode gudang");
          return
        }
        // console.log(kode4,kode5,kode2)
        console.log('nobukti', nobukti)
        console.log('nobarang' , nobarang)
        console.log('kodelokasi', kode2)
        console.log('urut' , urut)

        let tempLokasi = listLokasi.filter(function (el){
          return el.BarcodeLoc == kode2
        })

        let _token = $("#_token").val();
        // let item = {}

        console.log(tempLokasi)

        if (!tempLokasi.length) {
          alertify.warning('Lokasi tidak ada di list')
          resetScannerKode2()
          return
        }
        console.log(Number(tempLokasi[0].SisaKapasitas))
        if (Number(tempLokasi[0].SisaKapasitas) <= 0) {
          alertify.warning('Kapasitas Limit')
          return
        }
        let tempQnt = Number(tempLokasi[0].SisaKapasitas)
        //
        if ( Number(tempLokasi[0].SisaKapasitas) > Number(tempAddData[0].QNTSISA) ) {
          tempQnt = Number(tempAddData[0].QNTSISA)
        }

        tempAddData[0].qntShow = tempQnt


        document.getElementById("input_detailKoreksi_nobukti").value = nobukti
        document.getElementById("input_detailKoreksi_kodelokasi").value = kode2
        console.log(tempAddData[0])
        document.getElementById("detailKoreksiTableData").innerHTML = `
        <tr>
        <td>${tempAddData[0].Kodebrg}</td>
        <td>${tempAddData[0].NAMABRG}</td>
        <td>${tempAddData[0].qntShow}</td>
        <td>${tempAddData[0].Satuan}</td>
        </tr>
        `
        $("#formDetailKoreksi").modal('toggle')

        console.log('mau ke sp')

      }
    }

  function enterScannerKode1temp(e) {
    if (e.which === 13) {

      let kode1 = document.getElementById("input_add_kode1").value

      if (!(kode1.length === 32 || kode1.length === 31 || kode1.length === 33)) {
        alertify.warning("kode tidak dikenali");
        document.getElementById("input_add_kode1").value = ''
        $('#input_add_kode1').focus();
        return
      }
      let kode4 = ""
      let kode5 = ""

      if (kode1.length === 32) {
        kode4 = kode1.slice(0,18)
        kode5 = kode1.slice(18)
      }
      if (kode1.length === 31) {
        kode4 = kode1.slice(0,17)
        kode5 = kode1.slice(17)
      }
      if (kode1.length === 33) {
        kode4 = kode1.slice(0,19)
        kode5 = kode1.slice(19)
      }


      let _token = $("#_token").val();
      // let item = {}
        $.ajax({
          url: "{!! url('checkaddputaway') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti: kode4,
            nobarang: kode5,

          },
          success: function(res) {
            console.log(res ,'ress =============================')
            console.log(1)
            if (res.length == 0) {
              console.log(2)
              alertify.warning("periode / barang sudah diterima");
              document.getElementById("input_add_kode1").value = ''
              $('#input_add_kode1').focus();
              return
            }
            console.log(3)
            $('#input_add_kode2').focus();
            // console.log(res)
            // item = res[0]
            // document.getElementById("input_add_kode1").value = ''
            // document.getElementById("input_add_kode2").value = ''
            // $('#input_add_kode1').focus();
          }
        })

      // $('#input_add_kode2').focus();

      // document.getElementById("input_koreksi_scanner").value = ''

    }
  }



  function enterScannerKode2temp(e) {
    if (e.which === 13) {
      let kode1 = document.getElementById("input_add_kode1").value
      if (!(kode1.length === 32 || kode1.length === 31 || kode1.length === 33)) {

        alertify.warning("kode tidak dikenali");
        document.getElementById("input_add_kode1").value = ''
        $('#input_add_kode1').focus();
        return

      }
      // SML/LPB/00035/101911209092300020

      let kode4 = ""
      let kode5 = ""

      if (kode1.length === 32) {
        kode4 = kode1.slice(0,18)
        kode5 = kode1.slice(18)
      }
      if (kode1.length === 31) {
        kode4 = kode1.slice(0,17)
        kode5 = kode1.slice(17)
      }
      if (kode1.length === 33) {
        kode4 = kode1.slice(0,19)
        kode5 = kode1.slice(19)
      }


      // let kode4 = kode1.slice(0,18)
      // let kode5 = kode1.slice(18)
      let kode2 = document.getElementById("input_add_kode2").value
      // console.log(kode4,kode5,kode2)
      console.log('nobukti', kode4)
      console.log('nobarang' , kode5)
      console.log('kodelokasi', kode2)

      let _token = $("#_token").val();
      // let item = {}
        $.ajax({
          url: "{!! url('addputaway') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti: kode4,
            nobarang: kode5,
            barcodelokasi: kode2

          },
          success: function(res) {
            console.log(res ,'ress =============================')
            // console.log(res)
            // item = res[0]
            if (res === '1') {
              document.getElementById("input_add_kode1").value = ''
              document.getElementById("input_add_kode2").value = ''
              // loadAll()
              $('#input_add_kode1').focus();
              alertify.success('Put Away telah ditambah')
            } else {
              alertify.warning('Terjadi kesalahan, silahkan input ulang')
            }

          }
        })
    }
  }

  function resetScannerKode1 () {
    document.getElementById("input_add_kode1").value = ''
    document.getElementById("listLokasi").innerHTML = ''
    listLokasi = []
    tempAddData = []
    document.getElementById("input_add_kode1").readOnly = false;
    document.getElementById("input_add_kode2").readOnly = false;
    document.getElementById("input_add_kode2").value = ''
    document.getElementById("input_add_kode3").value = ''
    $('#input_add_kode1').focus();
  }

  function resetScannerKode2 () {
    document.getElementById("input_add_kode2").value = ''
    document.getElementById("input_add_kode2").readOnly = false;
    document.getElementById("input_add_kode3").value = ''
    $('#input_add_kode2').focus();
  }

  function resetScannerKode3 () {
    document.getElementById("input_add_kode3").value = ''
    $('#input_add_kode3').focus();
  }
</script>




@endsection
