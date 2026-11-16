@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Picking UK')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>Picking UK</h1> - judul pindah ke header (page-title) --}}
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Picking UK</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Picking UK</a>
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

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Out</th>
                          <th scope="col">Tanggal</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{ $outstandingArray[$i][0]->NoBukti }}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('{{ $outstandingArray[$i][0]->NoBukti }}')"><i class="bi bi-upc-scan"></i></button>
                          </td>
                          <td>{{ $outstandingArray[$i][0]->NoBukti }}</td>
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

                    <table id="tabelRetur" class="data-table"  >
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
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NoBukti }}')" ><i class="bi bi-info-lg"></i></button>
                          </td>
                          <td>{{ $penerimaanArray[$i][0]->NoBukti }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->tanggal)) !!}</td>
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
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
            <!-- <div class="col-12">
              <div class="form-group">
                <label>No KMBJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nossp" placeholder="" disabled>
              </div>
            </div> -->
          </div>

          <div class="row">
            <!-- <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_gdg" placeholder="Gdg Asal" required disabled>
              </div>
            </div> -->
            <!-- <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div> -->




          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
      <div class="container-fluid">
        <div id="divAddScan" class="container-fluid showhide">
          <div class="line"></div>
          <div class="row">
            <div class="col-12">
              <h4>Scan Item</h4>
            </div>
          </div>
          <div class="row">

          <div class="col-12">
            <input id="addScanDetail" type="text" class="form-control" disabled>
          </div>
        </div>
          <div class="row" style="margin-top:10px">
            <div class="col-7">
              <div class="form-group">
                <label>barcode Barang</label>
              </div>
            </div>
            <div class="col-5 text-right">
              <div class="form-group">
            <button onclick="resetScannerKode1()" class="btn btn-success btn-sm text-right"><i class="bi bi-arrow-clockwise"></i></button>
            </div>
            </div>
            <div class="col-12">
              <input id="addScanBarcodeBarang" oninput="enterScannerKode1()" type="text" class="form-control " >
            </div>
          </div>
          <div class="row" style="margin-top:10px">
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
            <div class="col-12" >
              <div class="form-group form-group-lg">
              <input id="addScanBarcodeLokasi" oninput="enterScannerKode2()" type="text" class="form-control " >
            </div>
            </div>
          </div>
          <div class="container-lg mt-4">
            <div class="row" id="listLokasi" >

            </div>
          </div>
          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
              <button type="button" onclick="submitAddScan()" class="btn btn-primary" >Add Lokasi</button>
            </div>

          </div>
          <div class="line"></div>
        </div>

      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <!-- <th scope="col">No Bukti</th> -->
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">BarcodeLokasi</th>

                  </tr>
                </thead>


                <tbody id="addTableData" class="text-right" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                      <td>-</td>
                      <!-- <td>-</td> -->
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
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
            <!-- <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
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
                    <!-- <th scope="col">No Bukti</th> -->
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty DB</th>
                    <th scope="col">Qty CR</th>
                    <th scope="col">Satuan</th>

                  </tr>
                </thead>


                <tbody id="detailTableData" class="text-right" >
                  <tr >


                      <!-- <td>-</td> -->
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
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
                <label>No KMBJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nossp" placeholder="No KMBJ" disabled>
              </div>
            </div>
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
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_gdg" placeholder="Kode Cust" required disabled>
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
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <!-- <div class="col-12">
              <div class="form-group">
                <label>No KMBJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nossp" placeholder="No KMBJ" disabled>
              </div>
            </div> -->
            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_noout" placeholder="" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div> -->
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
                      <th scope="col">Satuan</th>
                    </tr>
                  </thead>


                  <tbody id="koreksiDetailTableData" class="text-right" >
                    <tr >

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

let tempScanData = {}
let tempScanIndex = 0
let tempScanLokasi = []

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


function buttonBatalShowHide() {
  $('.showhide').hide();
}



function refreshKoreksi (NOBUKTI) {
  $('.showhide').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanubahkemasan') !!}",
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
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
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
    if(item.QNTCR) {
      qnt = parseFloat(item.QNTCR).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td>${qnt}</td>
    <td>${qntos}</td>
    <td>${item.Satuan}</td>

    <td class="text-center">
    <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable


  koreksiPenerimaanArray.forEach((item, i) => {
    //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
  });
  console.log('!!!!!!!!!!!!!!!')
  console.log(koreksiPenerimaanArray[0].NOBUKTI, koreksiPenerimaanArray[0].NooutBRg,'============')








}

function buttonKoreksi(NOBUKTI) {
  console.log('button koreksi')
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanubahkemasan') !!}",
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
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
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
    if(item.QNTCR) {
      qnt = parseFloat(item.QNTCR).toFixed(2)
    }
    if(item.QntOS) {
      qntos = parseFloat(item.QntOS).toFixed(2)
    }
    rowTable += `<tr>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td>${qnt}</td>
    <td>${qntos}</td>
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
  if (koreksiDataEdit.QNTCR) {

    qnt = parseFloat(koreksiDataEdit.QNTCR).toFixed(2)
  }
  document.getElementById("koreksiEditKodeBrg").value = koreksiDataEdit.KODEBRG
  document.getElementById("koreksiEditNamaBrg").value = koreksiDataEdit.NAMABRG
  document.getElementById("koreksiEditQtyOS").value = Number(koreksiDataEdit.QntOS) ? koreksiDataEdit.QntOS : '0.00'
  document.getElementById("koreksiEditInputQty").value = qnt
  document.getElementById("koreksiEditSatuan").value = koreksiDataEdit.Satuan
  $('#formKoreksiEdit').show();
  document.getElementById('formKoreksiEdit').scrollIntoView();
}

function changeSelectKoreksiAdd() {
  let indexBarang = document.getElementById("koreksiAddSelect").value;
  console.log(indexBarang)
  console.log(koreksiDataAddList[indexBarang])
  let qnt = 0.00
  let qntos = 0.00
  if(koreksiDataAddList[indexBarang].QNT) {
    qnt = parseFloat(koreksiDataAddList[indexBarang].QNT).toFixed(2)
  }
  if(koreksiDataAddList[indexBarang].QntOS) {
    qntos = parseFloat(koreksiDataAddList[indexBarang].QntOS).toFixed(2)
  }
  document.getElementById("koreksiAddKodeBrg").value = koreksiDataAddList[indexBarang].KODEBRG
  document.getElementById("koreksiAddNamaBrg").value = koreksiDataAddList[indexBarang].NAMABRG
  document.getElementById("koreksiAddQtyOS").value = qntos
  document.getElementById("koreksiAddQtyPO").value = qnt
  document.getElementById("koreksiAddSatuan").value = koreksiDataAddList[indexBarang].Satuan

}

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



function loadAll () {
  $.ajax({
    url: "{!! url('loadallubahkemasanpicking') !!}",
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
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('${item[0].NoBukti}')" ><i class="bi bi-info-lg"></i></button>
        <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('${item[0].NoBukti}')"><i class="bi bi-upc-scan"></i></button></td>
        <td>${item[0].NoBukti}</td>
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
        if (item[0].tanggal) {
          let date = new Date(item[0].tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }

        rowTableRetur += `<tr>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].NoBukti}')" ><i class="bi bi-info-lg"></i></button>
        </td>
        <td>${item[0].NoBukti}</td>
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


function buttonAdd (NOBUKTI) {
  console.log(NOBUKTI)
  $('.showhide').hide();
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingubahkemasanpicking') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI,
    },
    success: function(res) {
      console.log(res)
      addDataArray = res
      console.log('===========================')
      document.getElementById("input_add_noout").value = res[0].NoBukti
      // document.getElementById("input_detail_gdg").value = res[0].NamaGudangAsal
      // document.getElementById("input_detail_namacust").value = res[0].NAMACUSTSUPP
      let rowTable = ""
      res.forEach((item, i) => {
        // if (item.QNTCR > 0) {

          rowTable += `<tr>
          <td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="buttonAddScan(${i})"><i class="bi bi-upc-scan"></i></button></td>
          <td class="text-left">${item.Kodebrg}</td>
          <td class="text-left">${item.NAMABRG}</td>
          <td class="text-right">${parseFloat(item.Qnt).toFixed(2)}</td>
          <td class="text-left">${item.Satuan}</td>
          <td class="text-center"><input  id="input_add_scan${i}" style="width: 100px;" class="text-right" type="text" disabled></td>
          </tr>`
        // }
      });

      // <td class="text-center"><input id="input_add_qty${i}" style="width: 100px;" class="text-right" type="number" min=0 value="${parseFloat(item.Qnt).toFixed(2)}" disabled></td>


      document.getElementById("addTableData").innerHTML = rowTable

      $("#form").modal('toggle')
    },
    error: function(err) {
      console.log(err)
    }
  })





}

function resetScannerKode1() {
  // document.getElementById("addScanBarcodeBarang").value = ''
  // document.getElementById("addScanBarcodeBarang").disabled = false


  tempScanLokasi = []
  // console.log(tempScanIndex, tempScanData)
  document.getElementById(`addScanBarcodeLokasi`).value = ''
  document.getElementById(`addScanBarcodeBarang`).value = ''
  document.getElementById(`addScanBarcodeLokasi`).disabled = false
  document.getElementById(`addScanBarcodeBarang`).disabled = false
  document.getElementById("listLokasi").innerHTML = ''

  $('#addScanBarcodeBarang').focus();
}

function resetScannerKode2() {
  document.getElementById(`addScanBarcodeLokasi`).value = ''
  document.getElementById(`addScanBarcodeLokasi`).disabled = false
  $('#addScanBarcodeLokasi').focus();
}

function enterScannerKode2 (e) {
  let kodeLokasi = $("#addScanBarcodeLokasi").val();
  let check = tempScanLokasi.filter(function (el){
    console.log('el', el)
    return el.BarcodeLoc == kodeLokasi
  })
  // console.log('sebelum reset scanner 1')

  // let _token = $("#_token").val();
  if (!check.length) {
    alertify.warning('Lokasi barang tidak terdaftar')
    resetScannerKode2()
    return
  }

  // tempScanData
  console.log(tempScanData)
  console.log(check)

  console.log(Number(tempScanData.Qnt))
  console.log(Number(tempScanData.isi))
  console.log(Number(tempScanData.Qnt) * Number(tempScanData.isi))
  console.log(Number(check[0].StockLokasi))

  if ((Number(tempScanData.Qnt) * Number(tempScanData.isi))  > Number(check[0].StockLokasi)) {



    alertify.warning('Stock lokasi tidak mencukupi')
    resetScannerKode2()

    return
  }

  document.getElementById(`addScanBarcodeLokasi`).disabled = true
}


function enterScannerKode1(e) {
  // SML/OB/00002/032311209092300036 // 31
  // if (e.which === 13) {
    let _token = $("#_token").val();

    let kodebarcode = document.getElementById("addScanBarcodeBarang").value

    let temp1 = kodebarcode.split(".")
    console.log(temp1)
    console.log(temp1.length)

    if (temp1.length != 3) {
      alertify.warning("kode tidak dikenali");
      resetScannerKode1()
      return
    }

    let nobukti = temp1[0]
    let urut = temp1[1]
    let nobarang= temp1[2]


    console.log(tempScanData)
    // NoBukti
    // Urut
    // Kodebrg
    // document.getElementById("addScanBarcodeLokasi").disabled = false

    if (tempScanData.NoBukti != nobukti) {
      alertify.warning("kode tidak dikenali");
      resetScannerKode1()
      return
    }

    if (tempScanData.Urut != urut) {
      alertify.warning("kode tidak dikenali");
      resetScannerKode1()
      return
    }

    if (tempScanData.Kodebrg !=  nobarang) {
      alertify.warning("kode tidak dikenali");
      resetScannerKode1()
      return
    }

    $.ajax({
      url: "{!! url('ubahkemasanpickingspcekadd') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nobukti,
        urut,
        nobarang
      },
      success: function(res) {
        console.log(res)
        tempScanLokasi = res.lokasi
        lokasiListRow = ''

        tempScanLokasi.forEach((item, i) => {
          lokasiListRow += `<div class=" col-6 text-center">
            <h5>Lokasi: ${item.BarcodeLoc}</h5>
            <p>Stok: ${parseFloat(item.StockLokasi).toFixed(2)}</p>
          </div>`
        });

        document.getElementById("listLokasi").innerHTML = lokasiListRow
      },
      error: function(err) {
        console.log(err)
      }
    })


    document.getElementById("addScanBarcodeBarang").disabled = true
    $('#addScanBarcodeLokasi').focus();



  }


function buttonAddScan (i) {
  $('.showhide').hide();

  tempScanIndex = i
  tempScanData = addDataArray[i]
  tempScanLokasi = []
  console.log(tempScanIndex, tempScanData)
  document.getElementById(`addScanBarcodeLokasi`).value = ''
  document.getElementById(`addScanBarcodeBarang`).value = ''
  document.getElementById(`addScanBarcodeLokasi`).disabled = false
  document.getElementById(`addScanBarcodeBarang`).disabled = false
  document.getElementById("listLokasi").innerHTML = ''
  document.getElementById(`addScanDetail`).value = `${tempScanData.NoBukti} - ${tempScanData.Kodebrg}`
  $('#divAddScan').show();
}

function submitAddScan () {
  let barcodeLokasi = $("#addScanBarcodeLokasi").val();
  if(!barcodeLokasi) {
    alertify.warning("Isi barcode lokasi")
    return
  }
  document.getElementById(`input_add_scan${tempScanIndex}`).value = barcodeLokasi

  $('.showhide').hide();
}

function submitAdd () {
  let _token = $("#_token").val();
  console.log("submitAdd")
  let check = 1
  addDataArray.forEach((item, i) => {
    // if (item.QNTCR > 0) {
      let qtyCheck = $(`#input_add_scan${i}`).val();
      addDataArray[i].inputKodeLokasi = qtyCheck
      console.log(qtyCheck)
      if (!qtyCheck) {
        check = 0
        console.log(item)
      // }
    }
  });
  console.log(addDataArray)
  if (!check) {
    alertify.warning("Isi semua lokasi")

    return
  }


  $.ajax({
    url: "{!! url('ubahkemasanpickingspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      tempData: addDataArray,
    },
    success: function(res) {
      console.log(res)
      alertify.success("UK Picking berhasil ditambah")
      loadAll()
      $("#form").modal('toggle')
    },
    error: function(err) {
      console.log(err)
    }
  })


}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingubahkemasanpickinginfo') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI,
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
        rowTable += `<tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NamaBrg}</td>
        <td class="text-right">${Number(item.pDebet) ? parseFloat(item.QNT).toFixed(2) : '0.00'}</td>
        <td class="text-right">${Number(item.pDebet) ? '0.00' : parseFloat(item.QNT).toFixed(2)}</td>
        <td>${item.SATUAN}</td>
        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable
      res.forEach((item, i) => {
        //JsBarcode(`#barcodeDetail${i}`, item.KODEBRG ,{width: 2, height: 25,});
      });
    },
    error: function(err) {
      console.log(err)
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
  if (Number(qntTerima) > Number(dataPBG.QntOS) + Number(dataPBG.QNTCR)) {
    alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
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
    let gdgasal = dataPBG.KodeGdg
    let gdgtujuan = "GSAMPLE"
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


    console.log('choice' , choice)
    console.log('nossp' , nossp)
    console.log('note' , note)
    console.log('urut' , urut)
    console.log('kodebrg' , kodebrg)
    console.log('gdgasal' , gdgasal)
    console.log('satuan' , satuan)
    console.log('qntTerima' , qntTerima)
    console.log('nosat' , nosat)
    console.log('isi' , isi)
    console.log('qntDB' , qntDB)
    console.log('qntTerima' , qntTerima)

    // return
    $.ajax({
      url: "{!! url('koreksiubahkemasan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        nossp,
        note,
        urut,
        kodebrg,
        gdgasal,
        satuan,
        qntTerima,
        nosat,
        isi,
        qntDB,
        qntTerima,
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(nossp)
        alertify.success('Item telah diedt');
        loadAll()
      }
    })
}




function buttonKoreksiDelete(index) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonKoreksiDelete')
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
        let gdgasal = dataPBG.KodeGdg
        let gdgtujuan = "GSAMPLE"
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
        console.log('kodecustsupp' , kodecustsupp)
        console.log('kodesls' , kodesls)
        console.log('pbonus' , pbonus)
        console.log('maxol' , maxol)
        console.log('nopr' , nopr)
        console.log('urutpr' , urutpr)
        console.log('pkonsi' , pkonsi)
        console.log('nooutbrg' , nooutbrg)
        console.log('urutoutbrg' , urutoutbrg)

        // return
        $.ajax({
          url: "{!! url('koreksiubahkemasan') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nossp,
            note :'',
            urut,
            kodebrg: '',
            gdgasal: '',
            satuan: '',
            qntTerima: 0,
            nosat: 0,
            isi: 0,
            qntDB : 0,
            qntTerima : 0,
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
    url: "{!! url('detailpenerimaanubahkemasanpicking') !!}",
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

      // let nooutbrg = ''
      // console.log(res[0])
      // if (res[0].NooutBRg) {
      //   nooutbrg = res[0].NooutBRg
      // }
      // document.getElementById("input_koreksidetail_namacust").value = res[0].NAMACUSTSUPP
      // document.getElementById("input_koreksidetail_noout").value = nooutbrg
      document.getElementById("input_koreksidetail_noout").value = res[0].NoBukti
      // document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg

      let date = new Date(res[0].tanggal);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_koreksidetail_tanggal').val(date1)



      let rowTable = ""
      res.forEach((item, i) => {

        rowTable += `<tr>
        <td class="text-left">${item.Kodebrg}</td>
        <td class="text-left">${item.NamaBrg}</td>
        <td>${item.Qnt ? parseFloat(item.Qnt).toFixed(2) : '0.00' }</td>
        <td class="text-left">${item.SATUAN}</td>

        </tr>`
      });
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable



    }
  })

  $("#formKoreksiDetail").modal('toggle')

}

</script>




@endsection
