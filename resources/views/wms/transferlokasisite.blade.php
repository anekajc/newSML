@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Transfer Lokasi')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">





  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left">
      {{-- <h1>Transfer Lokasi</h1> - judul pindah ke header (page-title) --}}
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
                    <label class="text-left">Lokasi Awal</label>
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
                    <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode1" placeholder="Lokasi Awal" oninput="enterScannerKode1(event)" autofocus>
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
                  <label>Barcode Barang</label>
                </div>
              </div>
              <div class="col-5 text-right">

                <div class="form-group">
                  <!-- <button onclick="tesplus2()" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
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
                  <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode2" placeholder="Barcode Barang" oninput="enterScannerKode2(event)">

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
                  <label>Lokasi Tujuan</label>
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
                  <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode3" placeholder="Lokasi Tujuan" oninput="enterScannerKode3(event)">
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
                  <label>Qnt</label>
                </div>
              </div>
              <!-- <div class="col-5 text-right">
                <div class="form-group">
              <button onclick="resetScannerKode4()"  class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
              </div> -->
              <!-- <div class="col-2">
              </div> -->


            </div>
            <div class="row">
              <!-- <div class="col-2">
              </div> -->
              <div class="col-7">
                <div class="form-group">
                  <input type="number" id="input_add_qnt" style="font-size: 15px" class="form-control form-control-lg col-lg text-right"  value="0.00">
                </div>
              </div>
              <div class="col-5">
                <div class="form-group">
                  <select class="form-control" id="input_add_satuan" id aria-label="Default select example">
                    <option selected value=0>Satuan</option>
                  </select>
                </div>
              </div>
              <!-- <div class="col-2">
              </div> -->

            </div>
            <div class="row">
              <!-- <div class="col-2">
              </div> -->
              <div class="col-12 text-right">
                <button type="button" class="btn btn-primary mt-4" onclick="submitAdd()">Submit</button>
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
        <h5 class="modal-title" id="exampleModalLabel">Transfer Lokasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <input type="hidden" name="noUrut" id="input_detailKoreksi_noUrut" value="" />

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
                <label>Lokasi Awal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input style="font-size: 15px" type="text" class="form-control" id="input_detailKoreksi_kodelokasi"  disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Lokasi Tujuan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input style="font-size: 15px" type="text" class="form-control" id="input_detailKoreksi_kodelokasitujuan"  disabled>
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
        <button type="button" class="btn btn-primary" onclick="submitAddAdd()">Submit</button>
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
  let tempListBarang = []
  let tempListLokasi = []
  let tempListLokasiTujuan = []

  let detailBarang = {}
  let detailLokasi = {}


  $(document).ready(function(){

        $('#input_add_kode1').trigger('focus')
        // $("#formDetailKoreksi").modal('toggle')

  });

  function buttonOpenBrowserItem () {
    $('#input_add_kode2').trigger('focus')
    console.log('tesplus2')
        document.getElementById("input_add_kode2").value = 'asdasdasdsad'

  }













  function enterScannerKode1(e) {
    // if (e.which === 13) {
    console.log('enterScannerKode1')

      let kode1 = document.getElementById("input_add_kode1").value
      if (kode1.length < 3) {
          alertify.warning("Kode Lokasi tidak dikenali");
          document.getElementById("input_add_kode1").value = ''
          $('#input_add_kode1').focus();
          return
      }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('transferlokasisiteceklokasiawal') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          kodelokasi: kode1

        },
        success: function(res) {
          console.log(res ,'ress =============================')
          // console.log(1)
          // return
          if (res == 2) {
            // console.log(2)
            alertify.warning("Kode Lokasi tidak dikenali");
            document.getElementById("input_add_kode1").value = ''
            $('#input_add_kode1').focus();
            // return
          } else {


            // document.getElementById("listLokasi").innerHTML = lokasiListRow
            tempListBarang = res
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



  }

  function enterScannerKode3(e) {
    // if (e.which === 13) {
    console.log('enterscannerkode3')
      let kode3 = document.getElementById("input_add_kode3").value



      let kode1 = document.getElementById("input_add_kode1").value
      console.log(kode1)
      console.log(kode3)
      console.log(tempListLokasiTujuan)
      if (kode1 == kode3) {
        alertify.warning("Kode lokasi awal dan tujuan tidak boleh sama")
        resetScannerKode3()
        return
      }

      let lokasiTujuan = tempListLokasiTujuan.filter(function (el){
        console.log('el', el)
        return el.BarcodeLoc == kode3
      })
      // console.log('sebelum reset scanner 1')

      // let _token = $("#_token").val();
      if (!lokasiTujuan.length) {
        alertify.warning('Lokasi barang tidak terdaftar')
        resetScannerKode3()
        return
      }
      document.getElementById("input_add_kode3").readOnly = true;
      console.log('done')
      return

  }

  function enterScannerKode2 (e) {
    // if (e.which === 13) {
    let kode2 = document.getElementById("input_add_kode2").value
    // let kode1 = document.getElementById("input_add_kode1").value

    if (kode2.length < 3) {
        alertify.warning("Kode Barang tidak dikenali");
        document.getElementById("input_add_kode2").value = ''
        $('#input_add_kode2').focus();
        return
    }

    // if(!kode1) {
    //
    //   // document.getElementById("input_add_kode2").value = ''
    //   // $('#input_add_kode2').focus();
    //   resetScannerKode2()
    //   alertify.warning("isi kode gudang");
    //   return
    // }
    detailBarang = {}
    // Dinonaktifkan 2026-10-22: barang dulu dicari di daftar ±19 ribu barang hasil scan lokasi awal (lambat).
    // Sekarang dicek per kode ke server (syarat sama: barang aktif dan bukan grup JS).
    let barang = []
    // Seperti dulu: sebelum lokasi awal valid (input terkunci), barang belum bisa diterima.
    if (!document.getElementById("input_add_kode1").readOnly) {
      alertify.warning('Barang tidak terdaftar di lokasi')
      resetScannerKode2()
      return
    }
    $.ajax({
      url: "{!! url('transferlokasisitecekbarang') !!}",
      type: "post",
      async: false,
      data: {
        _token : $("#_token").val(),
        kodebarang: kode2
      },
      success: function(res) {
        barang = res
      },
      error: function (err) {
        console.log(err)
      }
    })
    /* let barang = tempListBarang.filter(function (el){
      // console.log(el)
      // console.log(el.KODEBRG , kode2)
      // if( el.KODEBRG == kode2 ) {
      //   console.log('tes')
      //   // break;
      //   return
      // }
      return el.KODEBRG == kode2
    }) */
    console.log(barang , kode2)
    console.log(barang.length)
    if (!barang.length) {
      alertify.warning('Barang tidak terdaftar di lokasi')
      resetScannerKode2()
      return
    }
    // console.log(Number(tempLokasi[0].SisaKapasitas))
    // if (Number(tempLokasi[0].SisaKapasitas) <= 0) {
    //   alertify.warning('Kapasitas Limit')
    //   return
    // }
    detailBarang = barang[0]
    let listSelect = ''
    if (barang[0].SAT1){
      listSelect += `<option   value=1>${barang[0].SAT1}</option>`
    }
    if (barang[0].SAT2){
      listSelect += `<option   value=2>${barang[0].SAT2}</option>`
    }
    if (barang[0].SAT3){
      listSelect += `<option   value=3>${barang[0].SAT3}</option>`
    }
    document.getElementById("input_add_satuan").innerHTML = listSelect
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('transferlokasisiteceklokasitujuan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        kodebarang: kode2

      },
      success: function(res) {
        console.log(res ,'ress =============================')
        // console.log(1)
        // return
        // if (res == 2) {
        //   // console.log(2)
        //   alertify.warning("Kode Lokasi tidak dikenali");
        //   document.getElementById("input_add_kode1").value = ''
        //   $('#input_add_kode1').focus();
        //   // return
        // } else {


          // document.getElementById("listLokasi").innerHTML = lokasiListRow
          tempListLokasiTujuan = res
          document.getElementById("input_add_kode2").readOnly = true;
          document.getElementById("input_add_kode3").value = ''
          $('#input_add_kode3').focus();
        // }
        // console.log(res)
        // item = res[0]
        // document.getElementById("input_add_kode1").value = ''
        // document.getElementById("input_add_kode2").value = ''
        // $('#input_add_kode1').focus();
      }
    })

    // document.getElementById("input_add_kode2").readOnly = true;
    // document.getElementById("input_add_kode3").value = ''
    // $('#input_add_kode3').focus();

  // }
  }

  function submitAddAdd () {
    console.log(detailLokasi)
    console.log(detailBarang)
    let nobukti = $("#input_detailKoreksi_nobukti").val();
    let nourut = $("#input_detailKoreksi_noUrut").val();
    let _token = $("#_token").val();
    let qnt2 = Number(detailLokasi.qnt)
    let qnt1= 0
    if (detailLokasi.satuan ==1) {
      qnt1 = Number(detailLokasi.qnt)
    } else if (detailLokasi.satuan == 2) {
      qnt1 = Number(detailLokasi.qnt) * detailBarang.ISI2
    } else if (detailLokasi.satuan == 3) {
      qnt1 = Number(detailLokasi.qnt) * detailBarang.ISI3
    }

    let isi = 0
    if(detailLokasi.satuan ==1) {
      isi = detailBarang.ISI1
    } else if (detailLokasi.satuan ==2) {
      isi = detailBarang.ISI2
    } else if (detailLokasi.satuan ==3)  {
      isi = detailBarang.ISI3
    }
    let sat1 = detailBarang.SAT1
    let sat2 = ''
    if(detailLokasi.satuan ==1) {
      sat2 = detailBarang.SAT1
    } else if (detailLokasi.satuan ==2) {
      sat2 = detailBarang.SAT2
    } else if (detailLokasi.satuan ==3)  {
      sat2 = detailBarang.SAT3
    }

    console.log({
      choice: 'I',
      nobukti: nobukti,
      nourut: nourut,
      tanggal: new Date(),
      urut: 0,
      kodebarang: detailBarang.KODEBRG,
      qnt1,
      qnt2,
      isi,
      sat1,
      sat2,
      lokasiasal: detailLokasi.kode1,
      lokasitujuan: detailLokasi.kode3,
      kodegdgasal: detailLokasi.lokasiawal.KodeGdg,
      kodegdgtujuan: detailLokasi.lokasitujuan.KodeGdg,
      pTerima:1,
      note: '',
      nosat: detailLokasi.satuan

    })

    $.ajax({
      url: "{!! url('transferlokasisitespadd') !!}",
      type: "post",
      async: false,
      data: {
        _token: _token,
        choice: 'I',
        nobukti: nobukti,
        nourut: nourut,
        tanggal: new Date(),
        urut: 0,
        kodebarang: detailBarang.KODEBRG,
        qnt1,
        qnt2,
        isi,
        sat1,
        sat2,
        lokasiasal: detailLokasi.kode1,
        lokasitujuan: detailLokasi.kode3,
        kodegdgasal: detailLokasi.lokasiawal.KodeGdg,
        kodegdgtujuan: detailLokasi.lokasitujuan.KodeGdg,
        pTerima:1,
        note: '',
        nosat: detailLokasi.satuan

      },
      success: function(res) {
        console.log(res)
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }
        if(res == 1) {

          $("#formDetailKoreksi").modal('toggle')
          alertify.success('RLB telah ditambah');
        }


        if (res == 3 ) {
          alertify.warning('Stok gudang tidak mencukupi');
        }

        if (res == 4 ) {
          alertify.warning('Gudang harus sama');
        }
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
    })

  }

  function setNewNoBukti () {
    $.ajax({
      url: "{!! url('getnobuktitransferlokasisite') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res, 'Nobukti')
        console.log(res[0].Nobukti , res[0].Nourut)
        console.log('===============')
        document.getElementById("input_detailKoreksi_nobukti").value = res[0].Nobukti
        document.getElementById("input_detailKoreksi_noUrut").value = res[0].Nourut

      }
    })
  }


  function submitAdd () {

    // let akses = $("#akses_istambah").val();
    //
    // if (!Number(akses)) {
    //   alertify.warning('No access')
    //   return
    // }


    let kode1 = $("#input_add_kode1").val();
    let kode2 = $("#input_add_kode2").val();
    let kode3 = $("#input_add_kode3").val();
    let satuan = $("#input_add_satuan").val();
    let qnt = $("#input_add_qnt").val();
    let _token = $("#_token").val();

    console.log(kode1)
    console.log(kode2)
    console.log(kode3)
    console.log(satuan)
    console.log(qnt)
    console.log(detailBarang)

    if (!kode1 || !kode2 || !kode3 || !satuan ) {
      alertify.warning("Lengkapi semua input")
      return
    }
    if ( qnt <= 0) {
      alertify.warning("Qnt <= 0")
      return
    }

    setNewNoBukti()

    detailLokasi = {}
    // console.log(Object.keys(detailLokasi).length)
    // if (Object.keys(detailLokasi).length) {
    //   console.log('true before')
    // } else {
    //   console.log('false before')
    // }

    $.ajax({
      url: "{!! url('transferlokasisitecekkodegdg') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        kodelokasiawal: kode1,
        kodelokasitujuan: kode3


      },
      success: function(res) {
        detailLokasi= {
          kode1,
          kode2,
          kode3,
          satuan,
          qnt,
          lokasiawal: res.lokasiawal,
          lokasitujuan: res.lokasitujuan
        }
      },
      error: function (err) {
        alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
      }
    })
    console.log(detailLokasi)
    // console.log(Object.keys(detailLokasi).length)

    if (!Object.keys(detailLokasi).length) {
      // console.log('true before
      alertify.warning("Terjadi kesalahan pada server")
      return
    }






    document.getElementById("input_detailKoreksi_qty").value = qnt
    // document.getElementById("input_detailKoreksi_satuan").value = satuan
    document.getElementById("input_detailKoreksi_kodelokasi").value = kode1
    document.getElementById("input_detailKoreksi_kodelokasitujuan").value = kode3
    document.getElementById("input_detailKoreksi_kodebarang").value = detailBarang.KODEBRG
    document.getElementById("input_detailKoreksi_namabarang").value = detailBarang.NAMABRG

    let namaSatuan = ''
    if (detailLokasi.satuan == 1) {
      namaSatuan = detailBarang.SAT1
    }
    if (detailLokasi.satuan == 2) {
      namaSatuan = detailBarang.SAT2
    }
    if (detailLokasi.satuan == 3) {
      namaSatuan = detailBarang.SAT3
    }
    document.getElementById("input_detailKoreksi_satuan").value = namaSatuan

    resetScannerKode1()
    $("#formDetailKoreksi").modal('toggle')


  }

  function resetScannerKode1 () {
    document.getElementById("input_add_kode1").value = ''
    document.getElementById("listLokasi").innerHTML = ''
    tempListLokasi = []
    tempListBarang = []
    tempListLokasiTujuan = []
    document.getElementById("input_add_kode1").readOnly = false;
    document.getElementById("input_add_kode2").readOnly = false;
    document.getElementById("input_add_kode3").readOnly = false;
    document.getElementById("input_add_kode2").value = ''
    document.getElementById("input_add_kode3").value = ''
    document.getElementById("input_add_qnt").value = '0.00'
    resetInputSatuan()
    $('#input_add_kode1').focus();
  }


  function resetScannerKode2 () {
    document.getElementById("input_add_kode2").value = ''
    document.getElementById("input_add_kode2").readOnly = false;
    document.getElementById("input_add_kode3").value = ''
    document.getElementById("input_add_kode3").readOnly = false;
    tempListLokasiTujuan = []
    resetInputSatuan()
    $('#input_add_kode2').focus();
  }

  function resetScannerKode3 () {
    document.getElementById("input_add_kode3").value = ''
    document.getElementById("input_add_kode3").readOnly = false;
    $('#input_add_kode3').focus();
  }

  function resetInputSatuan () {
    document.getElementById("input_add_satuan").innerHTML = '<option selected value=0>Satuan</option>'
  }
</script>




@endsection
