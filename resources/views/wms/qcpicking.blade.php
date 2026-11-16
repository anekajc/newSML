@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'QC Picking')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>QC Picking</h1> - judul pindah ke header (page-title) --}}
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding QC Picking</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">QC Picking</a>
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
                          <th scope="col">No. PL</th>
                          <th scope="col">Kode Brg</th>
                          <th scope="col">Nama Brg</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">No SO</th>
                          <th scope="col">Nama Cust</th>
                          <th scope="col">PO Cust</th>
                          <th scope="col">Qty</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">searchDetail</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{ $outstandingArray[$i]->NObukti }}')" ><i class="bi bi-info-lg"></i></button>
                            <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('{{ $outstandingArray[$i]->NObukti }}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
                          </td>
                          <td>{{ $outstandingArray[$i]->NObukti }}</td>
                          <td>{{ $outstandingArray[$i]->KOdebrg }}</td>
                          <td>{{ $outstandingArray[$i]->NamaBrg }}</td>
                          <td>{{  date("Y/m/d", strtotime($outstandingArray[$i]->tanggal)) }}</td>
                          <td>{{ $outstandingArray[$i]->NOSO}}</td>
                          <td>{{ $outstandingArray[$i]->NAMACUSTSUPP}}</td>
                          <td>{{ $outstandingArray[$i]->NOPESANAN}}</td>
                          <td class="text-right">{{ $outstandingArray[$i]->Qnt }}</td>
                          <td>{{ $outstandingArray[$i]->SATUAN }}</td>
                          <td>{{ $outstandingArray[$i]->NoPickingList . '.' . $outstandingArray[$i]->UrutPickingLIST . '.' . $outstandingArray[$i]->KOdebrg }}</td>
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
                          <th scope="col">No. SO</th>
                          <th scope="col">Nama Cust</th>
                          <th scope="col">PO Cust</th>
                        </tr>
                      </thead>


                      <tbody id="tabelRetur_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NObukti }}')" ><i class="bi bi-info-lg"></i></button>
                            <!-- <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->NObukti }}')"><i class="bi bi-pen"></i></button> -->
                          </td>
                          <td>{{ $penerimaanArray[$i][0]->NObukti }}</td>
                          <td>{{ $penerimaanArray[$i][0]->NOSO }}</td>
                          <td>{{ $penerimaanArray[$i][0]->NAMACUSTSUPP }}</td>
                          <td>{{ $penerimaanArray[$i][0]->NOPESANAN }}</td>
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
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_noout" placeholder="No Out" disabled>
              </div>
            </div>
            <!-- <div class="col-2">
              <div class="form-group">
                <label>No SSP</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nossp" placeholder="" disabled>
              </div>
            </div> -->
          </div>

          <!-- <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_gdg" placeholder="Gdg Asal" required disabled>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>

            <div class="col-2">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_namacust" placeholder="namacust" disabled>
              </div>
            </div>


          </div> -->
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
                    <th scope="col">v</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Barcode Loc</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Reject</th>
                    <th scope="col"><i class="bi bi-unlock"></i></th>

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

          <!-- <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_namacust" placeholder="namacust" disabled>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>




          </div> -->
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
                    <th scope="col">Tanggal</th>
                    <th scope="col">Barcode Loc</th>
                    <th scope="col">Qty</th>
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

            <div class="col-2">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-4">
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

          <!-- koreksi add -->
          <div id="formKoreksiAdd" class="container-fluid showhide">
            <div class="line"></div>
            <div class="row">
              <div class="col-4">
                <h4>Add Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <label>Pilih Barang</label>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <select onchange="changeSelectKoreksiAdd()" id="koreksiAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-6">
                <input id="koreksiAddKodeBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-6">
                <input id="koreksiAddNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Qty</label>
              </div>
              </div>
              <div class="col-3">
                <input id="koreksiAddInputQty" type="number" value=0.00 class="form-control">
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Satuan</label>
              </div>
              </div>
              <div class="col-3">

                  <input type="text" id="koreksiAddSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Qty OS</label>
              </div>
              </div>
              <div class="col-3">
                <input id="koreksiAddQtyOS" type="number" value=0.00 class="form-control" disabled>
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Qty PO</label>
              </div>
              </div>
              <div class="col-3">

                  <input id="koreksiAddQtyPO" type="number" value=0.00 class="form-control" disabled>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="" >Batal</button>
                <button type="button" onclick="submitAddKoreksi()" class="btn btn-primary" >Add Item</button>
              </div>

            </div>
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
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Qty OS</label>
              </div>
              </div>
              <div class="col-3">
                <input id="koreksiEditQtyOS" type="number" value=0.00 class="form-control" disabled>
              </div>

            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="" >Batal</button>
                <button type="button" onclick="submitEditKoreksi()" class="btn btn-primary" >Edit Item</button>
              </div>

            </div>
          </div>

          <!-- end  -->


      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi-->

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


            <!-- <div class="col-2">
              <div class="form-group">
                <label>No SSP</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nossp" placeholder="No TRT" disabled>
              </div>
            </div> -->
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

          <!-- <div class="row">
            <div class="col-2">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-2">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksidetail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-2">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div> -->
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
                      <th scope="col">Tanggal</th>
                      <th scope="col">Lokasi</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty Reject</th>
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
        // { "type": "date", "targets": [1] },
        {  "className": "text-center", "targets": [0], 'orderable' : false },
        {
            "targets": [10],
            "visible": false,
            "searchable": true
        }
      ]});
      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [1] },
        {  "className": "text-center", "targets": [0], 'orderable' : false },
      ]});
      $('#tabel_filter label input').focus();


});


function buttonAdd (NOBUKTI) {
  console.log('button add')
  console.log(NOBUKTI)
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  addDataArray = []

  // $.ajax({
  //   url: "{!! url('qcpickingdetailoutstanding') !!}",
  //   type: "get",
  //   async: false,
  //   success: function(res) {
  //     // console.log(res, 'NoBUkti')
  //     // console.log(res[0].Nobukti , res[0].Nourut)
  //     // console.log('===============')
  //     document.getElementById("input_add_nossp").value = res[0].NObukti
  //     // document.getElementById("input_add_noUrut").value = res[0].Nourut
  //   }
  // })


  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('qcpickingdetailoutstanding') !!}",
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
  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  document.getElementById("input_add_noout").value = addDataArray[0].NObukti
  // document.getElementById("input_add_gdg").value = addDataArray[0].NamaGudangAsal
  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  //
  let rowTable = ""
  addDataArray.forEach((item, i) => {
    let date1 = ''
    if (item.tanggal) {
    let date = new Date(item.tanggal);
    let day = ("0" + date.getDate()).slice(-2);
    let month = ("0" + (date.getMonth() + 1)).slice(-2);
    date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

    $('#input_koreksi_tanggal').val(date1)
    let qnt = 0.00
    // let qntos = 0.00
    if(item.Qnt) {
      // console.log(item.Qnt)
      qnt = parseFloat(item.Qnt).toFixed(2)
      // console.log(qnt)
    }
    // if(item.QntOS) {
    //   qntos = parseFloat(item.QntOS).toFixed(2)
    // }
    rowTable += `<tr class="text-left">
    <td class="text-center"><input id="add_checkbox${i}" class="" type="checkbox" ></td>
    <td>${item.KOdebrg}</td>
    <td>${item.NamaBrg}</td>
    <td>${date1}</td>
    <td>${item.BarcodeLoc}</td>
    <td class="text-center"><input id="input_add_qtyterima${i}" style="width: 100px;" class="text-right" type="number" min=0  value=${qnt}></td>
    <td>${item.SATUAN}</td>
    <td class="text-center "><input id="input_add_qtyreject${i}" style="width: 100px;" class="text-right" type="number" min=0 max=${qnt} value='0.00' disabled></td>
    <td class="text-center "><button class="btn btn-warning btn-sm" type="button" onclick="buttonQtyReject(${i})" ><i class="bi bi-unlock"></i></button></td>
    </tr>`
  });
  document.getElementById("addTableData").innerHTML = rowTable
  // addDataArray.forEach((item, i) => {
  // //  JsBarcode(`#barcodeAdd${i}`, item.KODEBRG ,{width: 2, height: 25,});
  // });
  $("#form").modal('toggle')


}

function buttonQtyReject (index) {
  document.getElementById(`input_add_qtyreject${index}`).disabled = false

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
  $('.showhide').hide();
  document.getElementById("koreksiAddKodeBrg").value = ""
  document.getElementById("koreksiAddNamaBrg").value = ""
  document.getElementById("koreksiAddQtyOS").value = 0.00
  document.getElementById("koreksiAddQtyPO").value = 0.00
  document.getElementById("koreksiAddInputQty").value = "0.00"
  $('#formKoreksiAdd').show();
}

function buttonKoreksiEdit(index) {
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
  let nobukti = $("#input_add_noout").val();

  // console.log('TES ==========')
  // return

  console.log(addDataArray)
  console.log('=============')
  let check = false
  let check1 = false
  let check2 = false
  let tempAddDataArray = []
  addDataArray.forEach((item, i) => {
    // if (document.getElementById(`input_add_qtyreject${i}`).val) {
    if (document.getElementById(`add_checkbox${i}`).checked) {
      addDataArray[i].inputQtyReject = $(`#input_add_qtyreject${i}`).val();
      addDataArray[i].inputQtyTerima = $(`#input_add_qtyterima${i}`).val();
      tempAddDataArray.push(addDataArray[i])

      //
      let a =$(`#input_add_qtyreject${i}`).val();
      let b= $(`#input_add_qtyterima${i}`).val();
      //
      console.log('a' , a)
      console.log('b' , b)


      console.log(addDataArray[i])
      console.log(addDataArray[i].inputQtyReject , addDataArray[i].inputQtyTerima)
      console.log("+++++++++++++++++++++++")
      console.log(addDataArray[i].inputQtyReject.includes(",") )
      console.log(addDataArray[i].inputQtyReject)
      console.log(addDataArray[i].inputQtyTerima.includes(","))
      console.log(addDataArray[i].inputQtyTerima)
      if( !a && a !== 0) {
        // console.log('masok a ' , a)
        check1 = true
      }
      if( !b && b !== 0) {
        // console.log('masok b ' , b)
        check1 = true
      }
      if(addDataArray[i].inputQtyReject.includes(",") || addDataArray[i].inputQtyTerima.includes(",")) {

        check1 = true
      }

      // console.log('1' , addDataArray[i].inputQtyTerima , addDataArray[i].Qnt)
      if (Number(addDataArray[i].inputQtyTerima) > Number(addDataArray[i].Qnt) ) {
        check2 = true
      }
      if (Number(addDataArray[i].inputQtyReject) < 0) {
        check = true
      }
      if (Number(addDataArray[i].inputQtyTerima) < 0) {
        check = true
      }
      if ( Number(addDataArray[i].inputQtyReject) > Number(addDataArray[i].Qnt)) {
        check = true
      }
    }




      // tempData.push(addDataArray[i])
    // }
  });

  if(check1) {
    alertify.warning('Gunakan "." untuk angka dibelakang koma')
    return
  }

  if (check2) {
    alertify.warning("Qnt Terima melebihi Qnt")
    return
  }

  if(check) {
    alertify.warning('Qnt Reject tidak bisa melebihi Qnt / kurang dari 0')
    return
  }

  if (!addDataArray.length) {
    alertify.warning('Terjadi kesalahan, silahkan refresh ulang')
    $("#form").modal('toggle')
    return
  }

  if(!tempAddDataArray.length) {
    alertify.warning("Tidak ada item dipilih");
    return
  }

  console.log(nobukti)
  console.log(tempAddDataArray)
  // return


  $.ajax({
    url: "{!! url('qcpickingspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      tempData: tempAddDataArray,
      nobukti: nobukti,
    },
    success: function(res) {
      console.log(res ,'!')
      $("#form").modal('toggle')
      alertify.success('QC Picking telah ditambah');
      loadAll()
    }})



  // return





}


function loadAll () {
  $.ajax({
    url: "{!! url('qcpickingloadall') !!}",
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
      console.log(dataRefreshOutstanding)
      dataRefreshOutstanding.forEach((item, i) => {
        let date1 = ''
        if (item.tanggal) {
        let date = new Date(item.tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }
        // console.log(item,'==================')
        // let date1 = ""
        // if (item[0].TANGGAL) {
        //   let date = new Date(item[0].TANGGAL);
        //   let day = ("0" + date.getDate()).slice(-2);
        //   let month = ("0" + (date.getMonth() + 1)).slice(-2);
        //   date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        // }
        rowTable  += `<tr>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('${item.NObukti}')" ><i class="bi bi-info-lg"></i></button>
        <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item.NObukti}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button></td>
        <td>${item.NObukti}</td>
        <td>${ item.KOdebrg}</td>
        <td>${ item.NamaBrg}</td>
        <td>${ date1}</td>
        <td>${item.NOSO}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.NOPESANAN}</td>
        <td class="text-right">${ item.Qnt}</td>
        <td>${ item.SATUAN}</td>
        <td>${ item.NoPickingList + '.' + item.UrutPickingLIST + '.' + item.KOdebrg }</td>

        </tr>`
      })



      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [1] },
        {  "className": "text-center", "targets": [0], "orderable": false },
        {
            "targets": [10],
            "visible": false,
            "searchable": true
        }
      ]});




      $('#tabelRetur').DataTable().destroy();
      let rowTableRetur = ""

      dataRefreshPenerimaan.forEach((item, i) => {
        // let date1 = ""
        // console.log(item[0].TANGGAL)
        // return
        // if (item[0].TANGGAL) {
        //   let date = new Date(item[0].TANGGAL);
        //   let day = ("0" + date.getDate()).slice(-2);
        //   let month = ("0" + (date.getMonth() + 1)).slice(-2);
        //   date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        // }

        rowTableRetur += `<tr>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].NObukti}')" ><i class="bi bi-info-lg"></i></button>

        </td>
        <td>${item[0].NObukti}</td>
        <td>${item[0].NOSO}</td>
        <td>${item[0].NAMACUSTSUPP}</td>
        <td>${item[0].NOPESANAN}</td>
        </tr>`
      });
      //
      //   <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NObukti}')"><i class="bi bi-pen"></i></button>

      document.getElementById("tabelRetur_data").innerHTML = rowTableRetur

      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [1] },
        {  "className": "text-center", "targets": [0], "orderable" : false },
      ]});


}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('qcpickingdetailoutstanding') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      // addDataArray = res
      // console.log('===========================')
      document.getElementById("input_detail_noout").value = res[0].NObukti
      // document.getElementById("input_detail_gdg").value = res[0].NamaGudangAsal
      // document.getElementById("input_detail_namacust").value = res[0].NAMACUSTSUPP
      let rowTable = ""
      res.forEach((item, i) => {
        let date1 = ''
        if (item.tanggal) {
        let date = new Date(item.tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        }
        let qnt = 0.00
        // let qntos = 0.00
        if(item.Qnt) {
          qnt = parseFloat(item.Qnt).toFixed(2)
        }
        // if(item.QntOS) {
        //   qntos = parseFloat(item.QntOS).toFixed(2)
        // }
        rowTable += `<tr class="text-left">
        <td>${item.KOdebrg}</td>
        <td>${item.NamaBrg}</td>
        <td>${date1}</td>
        <td>${item.BarcodeLoc}</td>
        <td class="text-right">${qnt}</td>
        <td>${item.SATUAN}</td>
        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable
      // res.forEach((item, i) => {
      //   //JsBarcode(`#barcodeDetail${i}`, item.KODEBRG ,{width: 2, height: 25,});
      // });
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
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(nossp)
        alertify.success('Item telah diedt');
        loadAll()
      }
    })
}




function buttonKoreksiDelete(index) {

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

  // return

  $.ajax({
    url: "{!! url('qcpickingdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      console.log(res[0])
      document.getElementById("input_koreksidetail_noout").value = res[0].NObukti
      let rowTable = ''
      res.forEach((item, i) => {
        let date1 = ''
        if (item.tanggal) {
        let date = new Date(item.tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        }
        $('#input_koreksi_tanggal').val(date1)
        let qnt = 0.00
        // let qntos = 0.00
        if(item.Qnt) {
          qnt = parseFloat(item.Qnt).toFixed(2)
        }
        let qntReject= 0.00
        if (item.QntReject) {
          qntReject = parseFloat(item.QntReject).toFixed(2)
        }
        // if(item.QntOS) {
        //   qntos = parseFloat(item.QntOS).toFixed(2)
        // }
        rowTable += `<tr class="text-left">
        <td>${item.KOdebrg}</td>
        <td>${item.NamaBrg}</td>
        <td>${date1}</td>
        <td>${item.BarcodeLoc}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntReject}</td>
        <td>${item.SATUAN}</td>
        </tr>`
      });
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable



      return


      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_koreksidetail_tanggal').val(date1)



      // let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.QNT) {
          qnt = parseFloat(item.QNT).toFixed(2)
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
