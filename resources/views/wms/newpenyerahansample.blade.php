@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Penyerahan Sample')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>Penyerahan Sample</h1> - judul pindah ke header (page-title) --}}
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="tempPrintContainer1" style="display:none"></div>

<div id="tempPrintContainer" style="display:none"></div>




<div id="printContainer" style="display:none"></div>

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Serah Sample</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Transaksi Serah Sample</a>
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
                          <th scope="col">No. Out</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Gdg</th>
                          <th scope="col">No. Permintaan</th>
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
                          <td>{{ $outstandingArray[$i][0]->NamaGudangAsal }}</td>
                          <td>{{ $outstandingArray[$i][0]->NoBeli }}</td>
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
                          <th scope="col">No. SSP</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">No. Out</th>
                          <th scope="col">Gudang</th>
                        </tr>
                      </thead>


                      <tbody id="tabelRetur_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')"><i class="bi bi-pen"></i></button>
                            <button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintSPB('{{ $penerimaanArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-printer"></i></button>
                          </td>
                          <td>{{ $penerimaanArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->TANGGAL)) !!}</td>
                          <td>{{ $penerimaanArray[$i][0]->NooutBRg}}</td>
                          <td>{{ $penerimaanArray[$i][0]->Namagdg }}</td>
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
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
                <label>No SSP</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nossp" placeholder="" disabled>
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
                <input type="text" class="form-control" id="input_add_gdg" placeholder="Gdg Asal" required disabled>
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

            <div class="col-12">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_namacust" placeholder="namacust" disabled>
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
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
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
            <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_gdg" placeholder="Gdg Asal" required disabled>
              </div>
            </div>


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
            <div class="col-12">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_namacust" placeholder="namacust" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <!-- <div class="form-group">
                <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div> -->
            </div>




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
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
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
                <label>No SSP</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nossp" placeholder="No TRT" disabled>
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

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary"  >Add Item</button>
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
              <div class="col-4">
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






<!-- start modal detail print -->
<div class="modal fade" id="formPrintSample" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Print</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No SSP</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_nossp" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_noout" placeholder="" disabled>
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
                <input type="text" class="form-control" id="input_print_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_print_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


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

                <table id="printDetailTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>
                    </tr>
                  </thead>


                  <tbody id="printDetailTableData" class="text-right" >
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

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="submitPrint()"  >Print</button>

          </div>


      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal detail print-->




















<!-- start modal detail koreksi -->
<div class="modal fade" id="formKoreksiDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
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


            <div class="col-12">
              <div class="form-group">
                <label>No SSP</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nossp" placeholder="No TRT" disabled>
              </div>
            </div>
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
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
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

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


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
        {  "className": "text-center", "targets": [0], "orderable" : false },
      ]});
      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" : false },
      ]});


});

function setNewNoBukti() {

    $.ajax({
      url: "{!! url('getnobuktipenyerahansample') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res, 'NoBUkti')
        console.log(res[0].Nobukti , res[0].Nourut)
        console.log('===============')
        document.getElementById("input_add_nossp").value = res[0].Nobukti
        document.getElementById("input_add_noUrut").value = res[0].Nourut
      }
    })
}


function buttonAdd (NOBUKTI) {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('button add')
  console.log(NOBUKTI)

  setNewNoBukti()


  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingpenyerahansample') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      addDataArray = res
      console.log('===========================')
    }
  })
  //
  document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  document.getElementById("input_add_noout").value = addDataArray[0].NOBUKTI
  document.getElementById("input_add_gdg").value = addDataArray[0].NamaGudangAsal
  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
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

function refreshKoreksi (NOBUKTI) {
  $('.showhide').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanpenyerahansample') !!}",
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
  document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

  let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_koreksi_tanggal').val(date1)


  let rowTable = ""
  koreksiPenerimaanArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.QNT) {
      qnt = parseFloat(item.QNT).toFixed(2)
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


  koreksiPenerimaanArray.forEach((item, i) => {
    //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
  });
  console.log('!!!!!!!!!!!!!!!')
  console.log(koreksiPenerimaanArray[0].NOBUKTI, koreksiPenerimaanArray[0].NooutBRg,'============')
  $.ajax({
    url: "{!! url('penyerahansamplekoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nossp: koreksiPenerimaanArray[0].NOBUKTI,
      noout: koreksiPenerimaanArray[0].NooutBRg
    },
    success: function(res) {
      console.log(res , "addlistkoreksi +++++++++")
      koreksiDataAddList = res
    }
  })


  let tempKoreksiAddList = ""
  if(koreksiDataAddList.length) {
    tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
    koreksiDataAddList.forEach((item, i) => {
      tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
    });
  } else {
    tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
  }



  document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList







}

function buttonBatalShowHide() {
  $('.showhide').hide();
}

function buttonKoreksi(NOBUKTI) {
  console.log('button koreksi')
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanpenyerahansample') !!}",
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
  document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

  let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_koreksi_tanggal').val(date1)


  let rowTable = ""
  koreksiPenerimaanArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.QNT) {
      qnt = parseFloat(item.QNT).toFixed(2)
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


  koreksiPenerimaanArray.forEach((item, i) => {
    //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1,  height: 25,   });
  });

  $.ajax({
    url: "{!! url('penyerahansamplekoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nossp: koreksiPenerimaanArray[0].NOBUKTI,
      noout: koreksiPenerimaanArray[0].NooutBRg
    },
    success: function(res) {
      console.log(res , "addlistkoreksi !!!!!!")
      koreksiDataAddList = res
    }
  })


  let tempKoreksiAddList = ""
  if(koreksiDataAddList.length) {
    tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
    koreksiDataAddList.forEach((item, i) => {
      tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
    });
  } else {
    tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
  }



  document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList








  $("#formKoreksi").modal('toggle')
}

function buttonKoreksiAdd() {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
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
  if (koreksiDataEdit.QNT) {

    qnt = parseFloat(koreksiDataEdit.QNT).toFixed(2)
  }
  document.getElementById("koreksiEditKodeBrg").value = koreksiDataEdit.KODEBRG
  document.getElementById("koreksiEditNamaBrg").value = koreksiDataEdit.NAMABRG
  document.getElementById("koreksiEditQtyOS").value = koreksiDataEdit.QntOS
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

  $.ajax({
    url: "{!! url('addpenyerahansample') !!}",
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
      console.log(res ,'!')

      if (res == 1) {
        $("#form").modal('toggle')
        alertify.success('SSP telah ditambah');
        loadAll()

      }
      if (res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }

      if (res == 3 ) {
        alertify.warning('Stok gudang tidak mencukupi');
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
    url: "{!! url('loadallpenyerahansample') !!}",
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
        <td>${item[0].NamaGudangAsal}</td>
        <td>${item[0].NoBeli}</td>
        </tr>`
      })



      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] , "orderable" : false },
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
        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NOBUKTI}')"><i class="bi bi-pen"></i></button></td>
        <td>${item[0].NOBUKTI}</td>
        <td>${date1}</td>
        <td>${item[0].NooutBRg}</td>
        <td>${item[0].Namagdg}</td>
        </td>
        </tr>`
      });



      document.getElementById("tabelRetur_data").innerHTML = rowTableRetur

      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] , "orderable" : false },
      ]});


}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingpenyerahansample') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      // addDataArray = res
      console.log('===========================')
      document.getElementById("input_detail_noout").value = res[0].NOBUKTI
      document.getElementById("input_detail_gdg").value = res[0].NamaGudangAsal
      document.getElementById("input_detail_namacust").value = res[0].NAMACUSTSUPP
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
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntos}</td>
        <td>${item.Satuan}</td>
        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable
      res.forEach((item, i) => {
        //JsBarcode(`#barcodeDetail${i}`, item.KODEBRG ,{width: 2, height: 25,});
      });
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
      if (res == 2) {
        // alertify.warning("Tidak bisa koreksi, barang sudah diproses")
      } else {
      console.log(res ,'succes add koreksi')
      refreshKoreksi(nossp)
      alertify.success('Item telah ditambah');
      loadAll()
    }
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
  if (Number(qntTerima) > Number(dataPBG.QntOS) + Number(dataPBG.QNT)) {
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
    let gdgasal = dataPBG.Kodegdg
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
        if (res == 2) {
          alertify.warning("Tidak bisa koreksi, barang sudah diproses")
        } else {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(nossp)
        alertify.success('Item telah diedit');
        loadAll()
      }
      }
    })
}




function buttonKoreksiDelete(index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let dataPBG = koreksiPenerimaanArray[index]
  console.log(dataPBG)

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
        let gdgasal = dataPBG.Kodegdg
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
            if (res == 2) {
              alertify.warning("Tidak bisa menghapus, barang sudah diproses")
            } else {
            console.log(res ,'succes delete koreksi')
            refreshKoreksi(nossp)
            alertify.success('Item telah didelete');
            loadAll()
          }
          }
        })


      }
    ,function(){
      console.log('no')
    });
}



function buttonPrintSPB (nobukti) {
  let akses = $("#akses_iscetak").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  // let printContent = "";
  // let css = "";
  // let hdr = "", nmcust="", alamatcust="", kdgd="", tgl="", noso="", nopo="", penerima="", alamatkirim="";
  // let str = "";
  // let ftr = "";
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('PrintSample') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: nobukti
    },
    success: function(res) {
      console.log(res)
      dataPrint = res
      // nmcust=res[0].namacustsupp;
      // alamatcust=res[0].ALAMAT;
      // kdgd=res[0].KodeGdg;
      // tgl=res[0].tgl;
      // noso=res[0].noso;
      // nopo=res[0].nopesanan;
      // penerima=res[0].namakebun;
      // alamatkirim=res[0].alamatkirim;

      document.getElementById("input_print_nossp").value = res[0].NOBUKTI
      document.getElementById("input_print_noout").value = res[0].NooutBRg
      document.getElementById("input_print_gdg").value = res[0].Namagdg
      document.getElementById("input_print_namacust").value = res[0].NAMACUSTSUPP
      nooutso = res[0].nooutso
      urutoutso = res[0].urutoutso

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_print_tanggal').val(date1)

      let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.qnt) {
          qnt = parseFloat(item.Qntx).toFixed(2)
        }
        if(item.qntos) {
          qntos = parseFloat(item.QntOS).toFixed(2)
        }
        // console.log(item.qntos)
        // console.log(qntos)
        rowTable += `<tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td >${item.NAMAMERK}</td>
        <td class="text-right">${qnt}</td>
        <td>${item.Satuan}</td>

        </tr>`
      });
      document.getElementById("printDetailTableData").innerHTML = rowTable
      document.getElementById(`tempPrintContainer1`).innerHTML =''
      new QRCode(document.getElementById(`tempPrintContainer1`), {text: nobukti , width: 80, height: 80});
      // printContent = document.getElementById(`tempPrintContainer1`).innerHTML;
      // console.log(printContent)
      $("#formPrintSample").modal('toggle')
    }
  })

  return

  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  css = `<style type="text/css">
    body {
      font-family: sans-serif;
      font-size: 10px !important;
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
      position: absolute;
      width: 100%;
      bottom: 15px;
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
  hdr = `<div class="" style="display: flex; width: 100%">
            <div class="pe-1" style="width: 50%">
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 15%; margin-top: 15px">
                  `+ imageContent +`
                </div>
                <div class="pb-1 ps-3" style="width: 85%; ">
                  <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                  <p class="m-0">
                    JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                    RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                  </p>
                  <p class="m-0">Telp (0541) 4104142 , Fax (0541) 4104195</p>
                  <p class="m-0">E-mail : sml@indo.net.id</p>
                </div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Kepada Yth:</div>
                <div class="pb-1" style="width: 80%"></div>
              </div>
              <div class="" style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%">`+nmcust+`</div>
                <div class="pb-1" style="width: 0%"></div>
              </div>
              <div class="" style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%">
                  `+alamatcust+`
                </div>
                <div class="pb-1" style="width: 0%"></div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%">Gudang : `+kdgd+`</div>
                <div class="pb-1" style="width: 0%"></div>
              </div>



            </div>
            <div style="width: 38%">
              <div style="display: flex; width: 100%">
                <h2 class="m-0 pb-2">SURAT PENGANTAR BARANG</h2>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Nomor</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+nobukti+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Tanggal</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+tgl+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">No. SO</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+noso+`</div>
              </div>

              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">No. PO</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+nopo+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Penerima</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+penerima+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Alamat Kirim:</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+alamatkirim+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 0%"></div>
              </div>
            </div>
            <div
              style="
                width: 12%;
                height: 80px;
                overflow: hidden;
              "
              >
              `+printContent+`
            </div>
          </div>
    <table


              class="detail-spb-table"
              style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif  display: inline-block ;
              font-size: 10px "


            >
              <thead>


                <tr>
                  <td class="text-center" style="width: 2%" >No.</td>
                  <td class="text-center" style="width: 65%">NAMA BARANG</td>
                  <td class="text-center" style="width: 9%" >MERK</td>
                  <td class="text-center" style="width: 13%">KODE BARANG</td>
                  <td class="text-center" style="width: 5%">SAT</td>
                  <td class="text-center" style="width: 12%">QTY</td>
                </tr>
              </thead> `;
       str += `<tbody border="1">`;
       dataPrint.forEach((item, i) => {
      str += `

      <td class="text-align: center"
            style="width: 2%; ">${i+1}</td>
      <td class="text-align: left"
            style="width: 65%;  ">${item.namabrg}</td>
      <td class="text-align: left"
            style="width: 9%;">${item.NAMAMERK}</td>
      <td class="text-align: left"
            style="width: 13%;  ">${item.kodebrg}</td>
      <td class="text-align: center"
            style="width: 5%;  ">${item.satuan}</td>
      <td class="text-align: text-right"
            style="width: 12%;  ">${item.qntcetak}</td>


        </tr>`;
         });
        str +=`
          <tr style>

          </tr>`;



           str += `</tbody>`;

  ftr = `</table>
   <hr />


  <div class="footer-sign font-family: sans-serif;
    font-size: 10px ">



  <div class="row mt-3" style="text-align: left;font-family: sans-serif;
  font-size: 10px ">
    <h5>
      *Barang diterima dengan baik dan sudah sesuai dengan yang tertera di
      Surat Pengantar Barang ini
    </h5>
  </div>


    <table
      class="detail-spb-table mb-2"
      style="width: 100%; margin-top: -15px ; font-family: sans-serif;
      font-size: 10px "
    >
      <tr>
        <td class="no-border text-center" style="width: 25%">
          Kepala Gudang
        </td>
        <td class="no-border text-center" style="width: 25%">Diperiksa</td>
        <td class="no-border text-center" style="width: 25%">Pembawa</td>
        <td class="no-border text-center" style="width: 25%">Penerima</td>
      </tr>
      <tr style="height: 2.5rem">
        <td class="no-border">&nbsp;</td>
      </tr>

      <tr>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid">
            Nama
          </p>
          <p class="m-0">Tanggal</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid">Nama</p>
          <p class="m-0">Tanggal</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid">Nama</p>
          <p class="m-0">Tanggal</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0" style="border-bottom: 1px solid">Nama</p>
          <p class="m-0">Tanggal</p>
        </td>
      </tr>
    </table>
  </div>


  <div class="footer-print-date">
    <table class="m-0" style="width: 100% ; font-family: sans-serif;
    font-size: 10px ">
      <tr>
        <td class="no-border"></td>
        <td class="no-border text-right"></td>
      </tr>
    </table>
  </div>`;
  w=window.open(' ')

  let tempPrintStr = ``
  tempPrintStr += `<html>
  <head>
    <title>Cetak Serah Sample</title>
  </head>

  <body onload="window.print()">
    ` + css





    tempPrintStr +=  `</body></html>`

  w.document.write(`<html>
  <head>
    <title>Cetak Serah Sample</title>
  </head>

  <body onload="window.print()">
    `+css+`
    <div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">
      `+hdr+`
      `+str+`
      `+ftr+`
    </div>
  </body>
  </html>`)
  w.print()
  w.close()
}




function submitPrint () {
  // for (var i = 0; i < 30; i++) {
  //   dataPrint.push(dataPrint[0])
  // }


  let arrayDataPrint = []
  for (let i = 0; i < dataPrint.length; i+=7) {
    let tempArray = dataPrint.slice(i,i+7)
    arrayDataPrint.push(tempArray)
  }
  let printContent = document.getElementById(`tempPrintContainer1`).innerHTML;
  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  let css = ''
  let hdr = ''
  let str= ''
  let ftr= ''
  css = `<style type="text/css">
    body {
      font-family: sans-serif;
      font-size: 10px !important;
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
  hdr = `<div class="" style="display: flex; width: 100%">
            <div class="pe-1" style="width: 50%">
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 15%; margin-top: 15px">
                  `+ imageContent +`
                </div>
                <div class="pb-1 ps-3" style="width: 85%; ">
                  <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                  <p class="m-0">
                    JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                    RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                  </p>
                  <p class="m-0">Telp (0541) 4104142 , Fax (0541) 4104195</p>
                  <p class="m-0">E-mail : sml@indo.net.id</p>
                </div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Kepada Yth:</div>
                <div class="pb-1" style="width: 80%"></div>
              </div>
              <div class="" style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%">`+dataPrint[0].NAMACUSTSUPP+`</div>
                <div class="pb-1" style="width: 0%"></div>
              </div>
              <div class="" style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%">
                  `+dataPrint[0].Alamat+`
                </div>
                <div class="pb-1" style="width: 0%"></div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%">Gudang : `+dataPrint[0].Kodegdg+`</div>
                <div class="pb-1" style="width: 0%"></div>
              </div>


            </div>
            <div style="width: 38%">
              <div style="display: flex; width: 100%">
                <h2 class="m-0 pb-2">SURAT SERAH SAMPLE</h2>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Nomor</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+dataPrint[0].NOBUKTI+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">Tanggal</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+dataPrint[0].TANGGAL+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 20%">No. SO</div>
                <div class="pb-1" style="width: 2%">:</div>
                <div class="pb-1" style="width: 78%">`+dataPrint[0].NooutBRg+`</div>
              </div>


              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 0%"></div>
              </div>
            </div>
            <div
              style="
                width: 12%;
                height: 80px;
                overflow: hidden;
              "
              >
              `+printContent+`
            </div>
          </div>
    <table


              class="detail-spb-table"
              style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif  display: inline-block ;
              font-size: 10px "


            >
              <thead>


                <tr>
                  <td class="text-center" style="width: 2%" >No.</td>
                  <td class="text-center" style="width: 65%">NAMA BARANG</td>
                  <td class="text-center" style="width: 9%" >MERK</td>
                  <td class="text-center" style="width: 13%">KODE BARANG</td>
                  <td class="text-center" style="width: 5%">SAT</td>
                  <td class="text-center" style="width: 12%">QTY</td>
                </tr>
              </thead> `;




  let z = 0
  let tempPrintStr = ``
  tempPrintStr += `<html>
  <head>
    <title>Cetak Serah Sample</title>
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
        tempPrintStr += ``



       tempPrintStr += `

       <td class="text-align: center"
             style="width: 2%; ">${z+1}</td>
       <td class="text-align: left"
             style="width: 65%;  ">${itemSub.NAMABRG}</td>
       <td class="text-align: left"
             style="width: 9%;">${itemSub.NAMAMERK}</td>
       <td class="text-align: left"
             style="width: 13%;  ">${itemSub.KODEBRG}</td>
       <td class="text-align: center"
             style="width: 5%;  ">${itemSub.Satuan}</td>
       <td class="text-align: text-right"
             style="width: 12%;  ">${itemSub.qntcetak}</td>


         </tr>`;

         z++




      });
      tempPrintStr +=`
        <tr style>

        </tr>`;



       tempPrintStr += `</tbody>`;

       tempPrintStr += `</table>
        <hr />


       <div class="footer-sign font-family: sans-serif;
         font-size: 10px ">






       <div class="row mt-3" style="text-align: left;font-family: sans-serif;
       font-size: 12px ">
        <span style="float: left">
         <h5>
           *Barang diterima dengan baik dan sudah sesuai dengan yang tertera di
           Surat Pengantar Barang ini
         </h5>
         </span>

         <span style="float: right">
          <h5>

          </h5>
          </span>
       </div>


         <table
           class="detail-spb-table mb-2"
           style="width: 100%; margin-top: -15px ; font-family: sans-serif;
           font-size: 10px "
         >
           <tr>
             <td class="no-border text-center" style="width: 25%">
               Kepala Gudang
             </td>
             <td class="no-border text-center" style="width: 25%">Diperiksa</td>
             <td class="no-border text-center" style="width: 25%">Pembawa</td>
             <td class="no-border text-center" style="width: 25%">Penerima</td>
           </tr>
           <tr style="height: 2.5rem">
             <td class="no-border">&nbsp;</td>
           </tr>

           <tr>
             <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">
                 Nama
               </p>
               <p class="m-0">Tanggal</p>
             </td>
             <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
             </td>
             <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
             </td>
             <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               <p class="m-0">Tanggal</p>
             </td>
           </tr>
         </table>
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
  // w.document.write(`<html>
  // <head>
  //   <title>Cetak SPB</title>
  // </head>
  //
  // <body onload="window.print()">
  //   `+css+`
  //   <div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:7px">
  //     `+hdr+`
  //     `+str+`
  //     `+ftr+`
  //   </div>
  //   <div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:7px">
  //     `+hdr+`
  //     `+str+`
  //     `+ftr+`
  //   </div>
  // </body>
  // </html>`)
  w.print()
  w.close()

}











function buttonDetailKoreksi (NOBUKTI) {
  console.log('button detail koreksi')
  console.log(NOBUKTI)

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanpenyerahansample') !!}",
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
      document.getElementById("input_koreksidetail_namacust").value = res[0].NAMACUSTSUPP
      document.getElementById("input_koreksidetail_noout").value = nooutbrg
      document.getElementById("input_koreksidetail_nossp").value = res[0].NOBUKTI
      document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_koreksidetail_tanggal').val(date1)



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
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntos}</td>
        <td>${item.Satuan}</td>

        </tr>`
      });
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable


      res.forEach((item, i) => {
        //JsBarcode(`#barcodeKoreksiDetail${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1,  height: 25,});
      });
    }
  })

  $("#formKoreksiDetail").modal('toggle')

}

</script>




@endsection
