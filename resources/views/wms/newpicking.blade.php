@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Picking')
@section('buttons')


@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-6 text-left">
      {{-- <h1>Picking</h1> - judul pindah ke header (page-title) --}}
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add Picking</button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <button onclick="tes123()">tes123</button> -->
<!-- <button onclick="tes1234()">tes1234</button> -->
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
          <!-- <div class="row mt-3">
            <div class="col-12" style="overflow:auto;">
              <div class="">

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Gdg</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-right" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr >

                          <td>{{ $penerimaanArray[$i][0]->NOBUKTI}}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->tanggal)) !!}</td>
                          <td>{{ $penerimaanArray[$i][0]->NAMAGUDANG}}</td>
                            <td class="text-center">
                              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI}}')" ><i class="bi bi-info-lg"></i></button>
                              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI}}')"><i class="bi bi-pen"></i></button>
                            </td>
                      </tr>
                      @endfor
                      </tbody>


                    </table>
              </div>
            </div>
          </div> -->
          <!-- <input type="text" class="input-lg" id="input_add_kode1" placeholder="Barcode Barang" onkeypress="enterScannerKode1(event)">
          <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg" aria-label=".form-control-lg example"> -->

          <div class="container-lg mt-4">
            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

              <div class="row">
                <!-- <div class="col-2">
                </div> -->
                <div class="col-7 text-left">
                  <div class="form-group form-group-lg text-left">
                    <label class="text-left">Barcode Barang</label>
                  </div>
                </div>
                <!-- <div class="col-6">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_kode1" placeholder="Barcode Barang" onkeypress="enterScannerKode1(event)">
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
                    <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode1" placeholder="Barcode Barang" oninput="enterScannerKode1(event)" autofocus>
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
              </div>
              <div class="col-2">
              </div> -->
              <div class="col-12">
                <div class="form-group">
                  <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode2" placeholder="Barcode Lokasi" oninput="enterScannerKode2(event)">
                </div>
              </div>
              <!-- <div class="col-2">
              </div> -->




            </div>

            <div id="divBarcodeLPB" style="display:none">

              <div class="row">
                <!-- <div class="col-2">
                </div> -->
                <div class="col-7 text-left">
                  <div class="form-group form-group-lg text-left">
                    <label class="text-left">Barcode LPB</label>
                  </div>
                </div>
                <!-- <div class="col-6">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_kode1" placeholder="Barcode Barang" onkeypress="enterScannerKode1(event)">
                  </div>
                </div> -->
                <div class="col-5 text-right">
                  <div class="form-group">
                <button onclick="resetScannerKode3()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
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
                    <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_add_kode3" placeholder="Barcode LPB" oninput="enterScannerKode3(event)">
                    <!-- <input class="form-control form-control-lg" type="text" placeholder=".form-control-lg" aria-label=".form-control-lg example"> -->

                  </div>
                </div>
                <!-- <div class="col-2">
                </div> -->
              </div>

            </div>





            <div class="container-lg mt-4">
              <div class="row" id="listLokasi" >

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
              <div class="col-3 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Barcode</label>
                </div>
              </div>
              <div class="col-7">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_kodebarcode" placeholder="Kode Barcode" onkeypress="enterScannerKode1(event)">
                </div>
              </div>

              <div class="col-2 text-right">
                <div class="form-group">
              <button onclick="resetScannerKode1()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
              </div>
              </div>

            </div>

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Lokasi</label>
              </div>
            </div>
            <div class="col-7">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kodelokasi" placeholder="Kode Lokasi" onkeypress="enterScannerKode2(event)">
              </div>
            </div>

            <div class="col-2 text-right">
              <div class="form-group">
            <button onclick="resetScannerKode2()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
            </div>







          </div>

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>LPB</label>
              </div>
            </div>
            <div class="col-7">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kodelpb" placeholder="Kode LPB" oninput="enterScannerKode3(event)">
              </div>
            </div>

            <div class="col-2 text-right">
              <div class="form-group">
            <button onclick="resetScannerKode3()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
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
        <h5 class="modal-title" id="exampleModalLabel">Picking</h5>
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
                <input type="text" class="form-control" id="input_detailKoreksi_nobukti"  disabled>
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
                <input type="text" class="form-control" id="input_detailKoreksi_kodelokasi"  disabled>
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
                <input type="text" class="form-control" id="input_detailKoreksi_kodebarang"  disabled>
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
                <textarea style='font-size: 13px' class="form-control" id="input_detailKoreksi_namabarang" rows="3" disabled></textarea>
                <!-- <textarea>4.1 Inch P WELDED BUSH HEAVY DUTY CHAIN WITH FLIGHT ATT. EV 6TH PITCH</textarea> -->
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
                <input type="number" class="form-control text-right" id="input_detailKoreksi_qty" value='0.00' >
              </div>
            </div>
            <div class="col-4">
              <input type="text" class="form-control" id="input_detailKoreksi_satuan"   disabled>
            </div>

          </div>




        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->
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
                    <td colspan="">-</td>
                    <td colspan="">-</td>
                    <td colspan="">-</td>
                    <td colspan="">-</td>
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
<div class="modal fade" id="formDetailKoreksiKode3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Picking</h5>
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
                <input type="text" class="form-control" id="input_detailKoreksiKode3_nobukti"  disabled>
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
                <input type="date" class="form-control" id="input_detailKoreksiKode3_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div> -->
            <div class="col-12">
              <div class="form-group">
                <label>Kode Lokasi</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detailKoreksiKode3_kodelokasi"  disabled>
              </div>
            </div>


          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>No LPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detailKoreksiKode3_noLPB"  disabled>
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
                <input type="text" class="form-control" id="input_detailKoreksiKode3_kodebarang"  disabled>
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
                <textarea style='font-size: 13px' class="form-control" id="input_detailKoreksiKode3_namabarang" rows="3" disabled></textarea>
                <!-- <textarea>4.1 Inch P WELDED BUSH HEAVY DUTY CHAIN WITH FLIGHT ATT. EV 6TH PITCH</textarea> -->
                <!-- <input type="text" rows='5' class="form-control" id="input_detailKoreksiKode3_namabarang" value='4.1 Inch P WELDED BUSH HEAVY DUTY CHAIN WITH FLIGHT ATT. EV 6TH PITCH'   disabled> -->
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
                <input type="number" class="form-control text-right" id="input_detailKoreksiKode3_qty" value='0.00' >
              </div>
            </div>
            <div class="col-4">
              <input type="text" class="form-control" id="input_detailKoreksiKode3_satuan"   disabled>
            </div>

          </div>




        <!-- <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary"  >Add Item</button>
        </div>

        </div>



        </div> -->
        <!-- <div class="container-fluid mt-4" style="overflow-x: auto;">

              <table id="detailKoreksiTableKode3" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                  </tr>
                </thead>


                <tbody id="detailKoreksiTableDataKode3" class="text-right" >
                  <tr >
                    <td colspan="">-</td>
                    <td colspan="">-</td>
                    <td colspan="">-</td>
                    <td colspan="">-</td>
                </tr>

                </tbody>


              </table>
        </div> -->



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAddKode3()">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal detail koreksi-->




<!-- start modal koreksi -->
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
<!-- End modal koreksi-->






@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">

  let koreksiDataArray = []
  let koreksiDataEdit = {}

  let dataRefreshPenerimaan = []

  let tempAddData = {}
  let tempKodeBarang = ""
  let tempKodeLokasi = ""
  let tempNoBukti = ""
  let tempUrut = 0
  let tempnosat = 0
  let tempAddDataLPB = {}
  let tempKodeBarangLPB = ""
  let tempNoBuktiLPB = ""
  let tempUrutLPB = 0
  let listLokasi = []

  let tempLokasiAdd =  []


  $(document).ready(function(){
        // $("#tabel").DataTable({
        //   "lengthChange": false,
        //     "paging": false ,
        //      "columnDefs": [
        //   { "type": "date", "targets": [1] },
        //   {  "className": "text-center", "targets": [3] },
        // ]});

        $('#input_add_kode1').trigger('focus')
        // $("#formDetailKoreksi").modal('toggle')
        // $("#formDetailKoreksiKode3").modal('toggle')


  });

  function tes123 () {
    document.getElementById("divBarcodeLPB").style.display = "block"
  }

  function tes1234 () {
    document.getElementById("divBarcodeLPB").style.display = "none"
  }


    function buttonDetailKoreksi(NOBUKTI) {
      console.log(NOBUKTI)
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailpenerimaanpicking') !!}",
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


          let date = new Date(res[0].TANGGAL);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
          $('#input_detailKoreksi_tanggal').val(date1)
          // document.getElementById("input_detailKoreksi_tanggal").value = res[0].TANGGAL
        }
      })


      $("#formDetailKoreksi").modal('toggle')
    }


    function loadAll () {
      // Ditambah 2026-10-21: halaman scan ini tidak punya tabel daftar (#tabel / #tabel_data) sejak versi
      // lama (ada di dalam komentar HTML), jadi loadAll() setelah edit/hapus koreksi akan error JS. Lewati saja.
      if (!document.getElementById("tabel_data")) {
        return
      }
      $.ajax({
        url: "{!! url('loadallpicking') !!}",
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
        let namaGdg = ""
        if (item[0].NamaGDG) {
          namaGdg = item[0].NamaGDG
        }
        let date1 = ""
        if (item[0].TANGGAL) {
          let date = new Date(item[0].TANGGAL);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }
        rowTable  += `<tr>
        <td>${item[0].NOBUKTI}</td>
        <td>${date1}</td>
        <td>${namaGdg}</td>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NOBUKTI}')"><i class="bi bi-pen"></i></button>
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
        url: "{!! url('detailpenerimaanpicking') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          NOBUKTI: NOBUKTI,

        },
        success: function(res) {
          console.log(res ,'ress =============================')
          koreksiDataArray = res

          // document.getElementById("koreksiEditInputQty").value = koreksiDataArray[index].QNT
        }
      })

      if (!koreksiDataArray.length) {
        alertify.success('Item habis');
        $("#formKoreksi").modal('toggle')
        return
      }


      let rowTable = ""
      koreksiDataArray.forEach((item, i) => {

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

      console.log('tes2')
      // console.log(rowTable)
      document.getElementById("koreksiTableData").innerHTML = rowTable


      document.getElementById("input_koreksi_nopen").value = res[0].NOBUKTI
      document.getElementById("input_koreksi_tanggal").value = res[0].TANGGAL

      // let rowTable = ""
      // koreksiDataArray.forEach((item, i) => {
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

    function buttonKoreksi(NOBUKTI) {
      $('.showhide').hide();
      console.log(NOBUKTI)
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailpenerimaanpicking') !!}",
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

          let date = new Date(res[0].TANGGAL);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
          $('#input_koreksi_tanggal').val(date1)
          // document.getElementById("input_koreksi_tanggal").value = res[0].TANGGAL
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

      // Ditambahkan 2026-10-27: tombol Batal di form Edit Item koreksi memanggil hideShowhide() yang
      // tidak pernah ada (sejak versi lama) -> error JS dan form tidak tertutup. Sama dengan Put Away.
      function hideShowhide () {
        $('.showhide').hide();
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
            url: "{!! url('koreksipicking') !!}",
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
            console.log(namaBrg, kodeBrg, urut, choice, noBukti)
            $.ajax({
              url: "{!! url('koreksipicking') !!}",
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



    function buttonAdd () {


          $("#form").modal('toggle')
          $('#form').on('shown.bs.modal', function () {
            $('#input_add_kodebarcode').trigger('focus')
          })
    }


    function enterScannerKode1(e) {
      // SML/OB/00002/032311209092300036 // 31
      // if (e.which === 13) {

        let kodebarcode = document.getElementById("input_add_kode1").value

        let result = kodebarcode.includes("PRU");
        if(result) {
          alertify.warning("Picking UK melalui menu Picking UK");
          resetScannerKode1()
          return
        }

        let temp1 = kodebarcode.split(".")
        console.log(temp1)
        console.log(temp1.length)

        if (temp1.length != 3) {
          alertify.warning("kode tidak dikenali");
          resetScannerKode1()
          return
        }
        // console.log(kodebarcode.length)
        // if (!(kodebarcode.length === 32 || kodebarcode.length === 31 || kodebarcode.length === 33)) {
        //
        //   alertify.warning("kode tidak dikenali");
        //   document.getElementById("input_add_kode1").value = ''
        //   $('#input_add_kode1').focus();
        //   return
        //
        // }
        // let kode1 = document.getElementById("input_add_kode1").value
        // let nobukti = ""
        // let kodebarang = ""
        //
        // if (kodebarcode.length === 32) {
        //   nobukti = kodebarcode.slice(0,18)
        //   kodebarang = kodebarcode.slice(18)
        // }
        // if (kodebarcode.length === 31) {
        //   nobukti = kodebarcode.slice(0,17)
        //   kodebarang = kodebarcode.slice(17)
        // }
        // if (kodebarcode.length === 33) {
        //   nobukti = kodebarcode.slice(0,19)
        //   kodebarang = kodebarcode.slice(19)
        // }

        let nobukti = temp1[0]
        let urut = temp1[1]
        let nobarang= temp1[2]

        console.log(nobukti, urut ,  nobarang)

        console.log(nobukti.length + nobarang.length)

        let _token = $("#_token").val();
        let item = {}
          $.ajax({
            url: "{!! url('checkaddpicking') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              nobukti: nobukti,
              nobarang,
              urut

            },
            success: function(res) {
              console.log(res ,'ress =============================')
              console.log(1)
              if (res.check.length == 0) {
                console.log(2)
                alertify.warning("periode salah / barang sudah diterima");
                resetScannerKode1()
                return
              }
              console.log(3)
              listLokasi = res.lokasi
              lokasiListRow = ``
              res.lokasi.forEach((item, i) => {
                lokasiListRow += `<div class=" col-6 text-center">
                  <h5>Lokasi: ${item.BarcodeLoc}</h5>
                </div>`
              });
              document.getElementById("listLokasi").innerHTML = lokasiListRow
              document.getElementById("input_add_kode1").readOnly = true;
              $('#input_add_kode2').focus();
              // console.log(res)
              // item = res[0]
              // document.getElementById("input_add_kode1").value = ''
              // document.getElementById("input_add_kode2").value = ''
              // $('#input_add_kode1').focus();
            }
          })

        // $('#input_add_kode2').focus();
        //
        // document.getElementById("input_koreksi_scanner").value = ''

      // }
    }

    function enterScannerKode2(e) {

      // if (e.which === 13) {
        let kodebarcode = document.getElementById("input_add_kode1").value
        console.log(kodebarcode.length)

        let temp1 = kodebarcode.split(".")
        console.log(temp1)
        console.log(temp1.length)

        if (temp1.length != 3) {
          alertify.warning("kode tidak dikenali");
          // document.getElementById("input_add_kode1").value = ''
          // $('#input_add_kode1').focus();
          resetScannerKode1()
          return
        }

        // return

        let nobukti = temp1[0]
        let urut = temp1[1]
        let nobarang= temp1[2]
        // if (!(kodebarcode.length === 32 || kodebarcode.length === 31 || kodebarcode.length === 33)) {
        //
        //   alertify.warning("kode tidak dikenali");
        //   document.getElementById("input_add_kode1").value = ''
        //   $('#input_add_kode1').focus();
        //   return
        //
        // }
        // SML/OB/00002/032311209092300036 // 31

        // let nobukti = ""
        // let kodebarang = ""

        let kodelokasi = document.getElementById("input_add_kode2").value

        // if (kodebarcode.length === 32) {
        //   nobukti = kodebarcode.slice(0,18)
        //   kodebarang = kodebarcode.slice(18)
        // }
        // if (kodebarcode.length === 31) {
        //   nobukti = kodebarcode.slice(0,17)
        //   kodebarang = kodebarcode.slice(17)
        // }
        // if (kodebarcode.length === 33) {
        //   nobukti = kodebarcode.slice(0,19)
        //   kodebarang = kodebarcode.slice(19)
        // }

        console.log(listLokasi)

        let tempLokasi = listLokasi.filter(function (el){
          return el.BarcodeLoc == kodelokasi
        })

        console.log(tempLokasi)

        if (!tempLokasi.length) {
          alertify.warning('Lokasi tidak ada di list')
          resetScannerKode2()
          return
        }
        if (kodelokasi.includes('REC')) {
          document.getElementById("divBarcodeLPB").style.display = "block"
          document.getElementById("input_add_kode2").readOnly = true;
          $('#input_add_kode3').focus();
          return
        }
        // return



        // let nobukti = kodebarcode.slice(0,17)
        // let kodebarang = kodebarcode.slice(17)

        // console.log(kode4,kodebarang,kodelokasi)
        console.log('nobukti', nobukti)
        console.log('nobarang' , nobarang)
        console.log('kodelokasi', kodelokasi)
        console.log('urut', urut)

        let _token = $("#_token").val();
        // let item = {}
          // $.ajax({
          //   url: "{!! url('addpicking') !!}",
          //   type: "post",
          //   async: false,
          //   data: {
          //     _token : _token,
          //     nobukti: nobukti,
          //     nobarang: kodebarang,
          //     barcodelokasi: kodelokasi
          //
          //   },
          //   success: function(res) {
          //     console.log(res ,'ress =============================')
          //     // console.log(res)
          //     // item = res[0]
          //     document.getElementById("input_add_kodebarcode").value = ''
          //     document.getElementById("input_add_kodelokasi").value = ''
          //     loadAll()
          //     $('#input_add_kodebarcode').focus();
          //   }
          // })

          $.ajax({
            url: "{!! url('checkaddpicking') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              nobukti: nobukti,
              urut: urut,
              nobarang,

            },
            success: function(res) {
              console.log(res ,'ress =============================')
              console.log(1)
              tempAddData = res.check
              tempNoBukti = nobukti
              tempKodeBarang= nobarang
              tempUrut= urut
              tempKodeLokasi = kodelokasi
              tempLokasiAdd = tempLokasi
              if (res.check.length == 0) {
                console.log(2)
                alertify.warning("periode salah / barang sudah diterima");
                resetScannerKode1()
                $('#input_add_kode1').focus();
                return
              }
              console.log(3)

              tempAddData = res.check
              tempNoBukti = nobukti
              tempKodeBarang= nobarang
              tempKodeLokasi = kodelokasi
              tempUrut= urut
              tempLokasiAdd = tempLokasi
              // $('#input_add_kode2').focus();
              // console.log(res)
              // item = res[0]
              // document.getElementById("input_add_kode1").value = ''
              // document.getElementById("input_add_kode2").value = ''
              // $('#input_add_kode1').focus();
            }
          })

          if (!tempAddData.length) {
            return
          }
          console.log(tempKodeLokasi)
          console.log('1')
          document.getElementById("input_detailKoreksi_nobukti").value = tempNoBukti
          document.getElementById("input_detailKoreksi_kodelokasi").value = tempKodeLokasi
          console.log('2')
          // document.getElementById("detailKoreksiTableData").innerHTML = `
          // <tr>
          // <td>${tempAddData[0].Kodebrg}</td>
          // <td>${tempAddData[0].NAMABRG}</td>
          // <td class="text-center"><input type="number" class="input-lg text-right" id="input_add_qntTerima"></td>
          // <td>${tempAddData[0].Satuan}</td>
          // </tr>
          // `
          console.log('3')
          document.getElementById("input_detailKoreksi_kodebarang").value = tempAddData[0].Kodebrg
          document.getElementById("input_detailKoreksi_namabarang").value = tempAddData[0].NAMABRG


          // console.log('asd')
          // document.getElementById("input_detailKoreksi_nobukti").value = nobukti
          // document.getElementById("input_detailKoreksi_kodelokasi").value = kode2
          // console.log(tempAddData[0])
          // document.getElementById("input_detailKoreksi_kodebarang").value = tempAddData[0].Kodebrg
          // document.getElementById("input_detailKoreksi_namabarang").value = tempAddData[0].NAMABRG
          // console.log('===')
          document.getElementById("input_detailKoreksi_qty").value = tempAddData[0].QNTSISA
          // console.log('asdads')
          document.getElementById("input_detailKoreksi_satuan").value = tempAddData[0].Satuan

          // document.getElementById("input_add_qntTerima").value = tempAddData[0].QNTSISA

          $("#formDetailKoreksi").modal('toggle')
      // }
    }

    function enterScannerKode3 (e) {
      // if (e.which === 13) {
          console.log('yes enter')

          let kodebarcode = document.getElementById("input_add_kode1").value
          let kodelokasi = document.getElementById("input_add_kode2").value
          let kodeLPB = document.getElementById("input_add_kode3").value
          console.log('kodebarcode', kodebarcode)
          console.log('kodelokasi', kodelokasi)
          console.log('kodeLPB', kodeLPB)



          let temp1 = kodebarcode.split(".")
          console.log(temp1)
          console.log(temp1.length)

          if (temp1.length != 3) {
            alertify.warning("kode tidak dikenali");
            // document.getElementById("input_add_kode1").value = ''
            // $('#input_add_kode1').focus();
            resetScannerKode1()
            return
          }

          // return

          let nobukti = temp1[0]
          let urut = temp1[1]
          let nobarang= temp1[2]

          let temp2 = kodeLPB.split(".")
          console.log(temp2)
          console.log(temp2.length)
          console.log('==temp2==')

          if (temp2.length != 3) {
            alertify.warning("kode tidak dikenali");
            // document.getElementById("input_add_kode1").value = ''
            // $('#input_add_kode1').focus();
            resetScannerKode3()
            return
          }

          // return

          let nobuktiLPB = temp2[0]
          let urutLPB = temp2[1]
          let nobarangLPB= temp2[2]

          console.log('==temp1==')

          if( nobarangLPB != nobarang) {
            alertify.warning("barang tidak sesuai");
            resetScannerKode3()
            return
          }
          let _token = $("#_token").val();

          console.log('====checkaddpickinglpb====')
          console.log(nobukti, urut, nobarang)
          console.log(nobuktiLPB, urutLPB, nobarangLPB)
          // return

          $.ajax({
            url: "{!! url('checkaddpickinglpb') !!}",
            type: "post",
            async: false,
            data: {
              _token : _token,
              nobukti: nobukti,
              urut: urut,
              nobarang,
              nobuktiLPB,
              urutLPB,
              nobarangLPB,

            },
            success: function(res) {
              console.log(res ,'ress =============================')
              console.log(res.check)
              console.log(res.checkLPB)
              console.log(1)
              tempAddData = res.check
              tempAddDataLPB = res.checkLPB
              tempNoBukti = nobukti
              tempKodeBarang= nobarang
              tempKodeLokasi = kodelokasi
              tempNoBuktiLPB = nobuktiLPB
              tempKodeBarangLPB= nobarangLPB
              tempUrut = urut
              tempUrutLPB = urutLPB
              if (res.check.length == 0) {
                console.log(2)
                alertify.warning("periode salah / barang sudah diterima");
                resetScannerKode1()
                $('#input_add_kode1').focus();
                return
              }
              if (res.checkLPB.length == 0) {
                console.log(2)
                alertify.warning("data LPB tidak ditemukan");
                resetScannerKode3()
                $('#input_add_kode3').focus();
                return
              }
              console.log(3)

              tempAddData = res.check
              tempAddDataLPB = res.checkLPB
              tempNoBukti = nobukti
              tempKodeBarang= nobarang
              tempKodeLokasi = kodelokasi
              tempNoBuktiLPB = nobuktiLPB
              tempKodeBarangLPB= nobarangLPB
              tempUrut = urut
              tempUrutLPB = urutLPB
              // $('#input_add_kode2').focus();
              // console.log(res)
              // item = res[0]
              // document.getElementById("input_add_kode1").value = ''
              // document.getElementById("input_add_kode2").value = ''
              // $('#input_add_kode1').focus();
            }
          })

          if (!tempAddData.length) {
            return
          }
          if (!tempAddDataLPB.length) {
            return
          }
          // console.log(tempKodeLokasi)
          //
          // document.getElementById("input_detailKoreksi_nobukti").value = tempNoBukti
          // document.getElementById("input_detailKoreksi_kodelokasi").value = tempKodeLokasi
          // document.getElementById("detailKoreksiTableDataKode3").innerHTML = `
          // <tr>
          // <td>${tempAddData[0].Kodebrg}</td>
          // <td>${tempAddData[0].NAMABRG}</td>
          // <td class="text-center"><input type="number" class="input-lg text-right" id="input_add_qntTerima" readonly></td>
          // <td>${tempAddData[0].Satuan}</td>
          // </tr>
          // `
          console.log('=====')
          console.log(tempAddData[0])
          console.log(tempAddData[0].Qnt)
          console.log(tempAddData[0].QNTSISA)
          // document.getElementById("input_add_qntTerima").value = tempAddData[0].Qnt

          document.getElementById("input_detailKoreksiKode3_nobukti").value = nobukti
          document.getElementById("input_detailKoreksiKode3_noLPB").value = nobuktiLPB
          document.getElementById("input_detailKoreksiKode3_kodelokasi").value = kodelokasi

          console.log(tempKodeLokasi)
          console.log('1')
          // document.getElementById("detailKoreksiTableData").innerHTML = `
          // <tr>
          // <td>${tempAddData[0].Kodebrg}</td>
          // <td>${tempAddData[0].NAMABRG}</td>
          // <td class="text-center"><input type="number" class="input-lg text-right" id="input_add_qntTerima"></td>
          // <td>${tempAddData[0].Satuan}</td>
          // </tr>
          // `
          console.log('3')
          document.getElementById("input_detailKoreksiKode3_kodebarang").value = tempAddData[0].Kodebrg
          document.getElementById("input_detailKoreksiKode3_namabarang").value = tempAddData[0].NAMABRG


          // console.log('asd')
          // document.getElementById("input_detailKoreksi_nobukti").value = nobukti
          // document.getElementById("input_detailKoreksi_kodelokasi").value = kode2
          // console.log(tempAddData[0])
          // document.getElementById("input_detailKoreksi_kodebarang").value = tempAddData[0].Kodebrg
          // document.getElementById("input_detailKoreksi_namabarang").value = tempAddData[0].NAMABRG
          // console.log('===')
          document.getElementById("input_detailKoreksiKode3_qty").value = tempAddData[0].QNTSISA
          // console.log('asdads')
          document.getElementById("input_detailKoreksiKode3_satuan").value = tempAddData[0].Satuan

          // SML/OB/00002/0323.6.10304051000009
          // SML/OB/00002/0323.7.10201054360001

          // SML/LPB/00225/0423.1.10201054360001


          // 1 cek kode barang kode 1 dan 3 sama tidak
          // 2 cek ajax VWOUTPENERIMAANBRG ada barangnya tidak sesuai lpb
          // bila 1 dan 2 oke buka form dengan qnt dari kode 1




          $("#formDetailKoreksiKode3").modal('toggle')

      // }
    }



      function enterScannerKode1temp(e) {
        // SML/OB/00002/032311209092300036 // 31
        if (e.which === 13) {


          let kodebarcode = document.getElementById("input_add_kode1").value
          console.log(kodebarcode.length)

          let temp1 = kodebarcode.split(".")
          console.log(temp1)
          console.log(temp1.length)

          if (temp1.length != 2) {
            alertify.warning("kode tidak dikenali");
            document.getElementById("input_add_kode1").value = ''
            $('#input_add_kode1').focus();
            return
          }

          let nobukti = temp1[0]
          // let urut = temp1[1]
          let kodebarang= temp1[2]



          // if (!(kodebarcode.length === 32 || kodebarcode.length === 31 || kodebarcode.length === 33)) {
          //
          //   alertify.warning("kode tidak dikenali");
          //   document.getElementById("input_add_kode1").value = ''
          //   $('#input_add_kode1').focus();
          //   return
          //
          // }
          // let kode1 = document.getElementById("input_add_kode1").value
          // let nobukti = ""
          // let kodebarang = ""
          //
          // if (kodebarcode.length === 32) {
          //   nobukti = kodebarcode.slice(0,18)
          //   kodebarang = kodebarcode.slice(18)
          // }
          // if (kodebarcode.length === 31) {
          //   nobukti = kodebarcode.slice(0,17)
          //   kodebarang = kodebarcode.slice(17)
          // }
          // if (kodebarcode.length === 33) {
          //   nobukti = kodebarcode.slice(0,19)
          //   kodebarang = kodebarcode.slice(19)
          // }

          console.log(nobukti, kodebarang)

          console.log(nobukti.length + kodebarang.length)

          let _token = $("#_token").val();
          let item = {}
            $.ajax({
              url: "{!! url('checkaddpicking') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                nobukti: nobukti,
                nobarang: kodebarang,

              },
              success: function(res) {
                console.log(res ,'ress =============================')
                console.log(1)
                if (res.length == 0) {
                  console.log(2)
                  alertify.warning("periode salah / barang sudah diterima");
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
          //
          // document.getElementById("input_koreksi_scanner").value = ''

        }
      }

        function enterScannerKode2temp(e) {

        if (e.which === 13) {
          let kodebarcode = document.getElementById("input_add_kode1").value
          console.log(kodebarcode.length)


          let temp1 = kodebarcode.split(".")
          console.log(temp1)
          console.log(temp1.length)

          if (temp1.length != 2) {
            alertify.warning("kode tidak dikenali");
            document.getElementById("input_add_kode1").value = ''
            $('#input_add_kode1').focus();
            return
          }

          // let nobukti = ""
          // let kodebarang = ""

          let nobukti = temp1[0]
          // let urut = temp1[1]
          let nobarang= temp1[1]

          let kodelokasi = document.getElementById("input_add_kode2").value

          // if (kodebarcode.length === 32) {
          //   nobukti = kodebarcode.slice(0,18)
          //   kodebarang = kodebarcode.slice(18)
          // }
          // if (kodebarcode.length === 31) {
          //   nobukti = kodebarcode.slice(0,17)
          //   kodebarang = kodebarcode.slice(17)
          // }
          // if (kodebarcode.length === 33) {
          //   nobukti = kodebarcode.slice(0,19)
          //   kodebarang = kodebarcode.slice(19)
          // }


          console.log('nobukti', nobukti)
          console.log('nobarang' , kodebarang)
          console.log('kodelokasi', kodelokasi)

          let _token = $("#_token").val();

            $.ajax({
              url: "{!! url('checkaddpicking') !!}",
              type: "post",
              async: false,
              data: {
                _token : _token,
                nobukti: nobukti,
                nobarang: kodebarang,

              },
              success: function(res) {
                console.log(res ,'ress =============================')
                console.log(1)
                tempAddData = res
                tempNoBukti = nobukti
                tempKodeBarang= kodebarang
                tempKodeLokasi = kodelokasi
                if (res.length == 0) {
                  console.log(2)
                  alertify.warning("periode salah / barang sudah diterima");
                  document.getElementById("input_add_kode1").value = ''
                  $('#input_add_kode1').focus();
                  return
                }
                console.log(3)

                tempAddData = res
                tempNoBukti = nobukti
                tempKodeBarang= kodebarang
                tempKodeLokasi = kodelokasi

              }
            })

            if (!tempAddData.length) {
              return
            }

            document.getElementById("input_detailKoreksi_nobukti").value = tempNoBukti
            document.getElementById("input_detailKoreksi_kodelokasi").value = tempKodeLokasi
            document.getElementById("detailKoreksiTableData").innerHTML = `
            <tr>
            <td>${tempAddData[0].Kodebrg}</td>
            <td>${tempAddData[0].NAMABRG}</td>
            <td>${tempAddData[0].Qnt}</td>
            <td>${tempAddData[0].Satuan}</td>
            </tr>
            `

            $("#formDetailKoreksi").modal('toggle')
        }
      }


      function submitAddKode3 () {
        console.log('submitAddKode3')
        let _token = $("#_token").val();

        let qntTerima = $("#input_detailKoreksiKode3_qty").val();

        if(Number(qntTerima) <= 0) {
          alertify.warning('Qnt terima < 0')
          return
        }

        if (Number(qntTerima) > Number(tempAddData[0].QNTSISA)) {
          alertify.warning('Qnt terima melebihi qnt sisa')
          return
        }

        if ((Number(qntTerima) *  Number(tempAddData[0].isi)) > Number(tempAddDataLPB[0].QNTSISA)) {
          alertify.warning('Qnt terima melebihi qnt sisa LPB')
          return
        }

        console.log(tempKodeLokasi)
        console.log(qntTerima)
        console.log(tempAddData)
        console.log(tempNoBukti, tempUrut, tempKodeBarang)
        console.log(tempAddDataLPB)
        console.log(tempNoBuktiLPB, tempUrutLPB, tempKodeBarangLPB)
        $.ajax({
          url: "{!! url('addpickinglpb') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti: tempNoBukti,
            urut: tempUrut,
            nobarang: tempKodeBarang,
            nobuktiLPB: tempNoBuktiLPB,
            urutLPB: tempUrutLPB,
            nobarangLPB: tempKodeBarangLPB,
            barcodelokasi: tempKodeLokasi,
            qntTerima: qntTerima,


          },
          success: function(res) {
            console.log(res ,'ress =============================')
            // console.log(res)
            // item = res[0]

            if (res == "1") {
              // resetScannerKode1()
              // loadAll()
              alertify.success('Add picking berhasil')
              resetScannerKode1()
              $("#formDetailKoreksiKode3").modal('toggle')
              $('#input_add_kode1').focus();

            } else if (res == 3 ) {
              alertify.warning('Stok Lokasi tidak mencukupi xxx');
            }
            else if (res == 4 ) {
              alertify.warning('Barang sudah di picking');
            } else {
              alertify.warning('Terjadi kesalahan input')
            }
          }
        })


      }

      function submitAdd () {
        console.log('subtmittara')
        let _token = $("#_token").val();

        let qntTerima = $("#input_detailKoreksi_qty").val();

        if(Number(qntTerima) <= 0) {
          alertify.warning('Qnt terima <= 0')
          return
        }
        console.log(tempAddData[0])
        if (Number(qntTerima) > Number(tempAddData[0].QNTSISA)) {
          alertify.warning('Qnt terima melebihi qnt sisa')
          return
        }
        console.log(Number(tempLokasiAdd[0].StockLokasi))
        // return
        if (Number(tempLokasiAdd[0].StockLokasi) < Number(qntTerima)) {
          alertify.warning('Qnt melebihi stock lokasi')
          return
        }

        console.log(tempNoBukti,tempKodeBarang, qntTerima)
        console.log(tempUrut, tempKodeLokasi)


        // return
        $.ajax({
          url: "{!! url('addpicking') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti: tempNoBukti,
            nobarang: tempKodeBarang,
            barcodelokasi: tempKodeLokasi,
            qntTerima: qntTerima,
            urut: tempUrut,
            nosat: tempnosat

          },
          success: function(res) {
            console.log(res ,'ress =============================')
            // console.log(res)
            // item = res[0]
            // if (res == 3 ) {
            //   alertify.warning('Stok Lokasi tidak mencukupi');
            // }
            // if (res == 4 ) {
            //   alertify.warning('Barang sudah di picking');
            // }
            if (res == "1") {
              resetScannerKode1()
              // loadAll()
              alertify.success('Add picking berhasil')
              resetScannerKode1()
              $("#formDetailKoreksi").modal('toggle')
              $('#input_add_kode1').focus();

            } else if (res == 3 ) {
              alertify.warning('Stok Lokasi tidak mencukupi');
            } else if (res == 4 ) {
              alertify.warning('Barang sudah di picking');
            } else {
              alertify.warning('Terjadi kesalahan input')
            }
          }
        })

      }

      function resetScannerKode1 () {
        console.log('resetScannerKode1')
        document.getElementById("input_add_kode1").value = ''
        document.getElementById("input_add_kode1").readOnly = false;
        document.getElementById("input_add_kode2").value = ''
        document.getElementById("input_add_kode2").readOnly = false;
        document.getElementById("input_add_kode3").value = ''
        document.getElementById("input_add_kode3").readOnly = false;

        document.getElementById("listLokasi").innerHTML = ''
        listLokasi = []
        tempAddData = []
        $('#input_add_kode1').focus();
      }

      function resetScannerKode2 () {
        console.log('resetScannerKode2')
        document.getElementById("divBarcodeLPB").style.display = "none"
        document.getElementById("input_add_kode2").value = ''
        document.getElementById("input_add_kode2").readOnly = false;
        document.getElementById("input_add_kode3").value = ''
        document.getElementById("input_add_kode3").readOnly = false;
        $('#input_add_kode2').focus();
      }

      function resetScannerKode3 () {
        console.log('resetScannerKode3')
        document.getElementById("input_add_kode3").value = ''
        document.getElementById("input_add_kode3").readOnly = false;
        $('#input_add_kode3').focus();
      }


</script>




@endsection
