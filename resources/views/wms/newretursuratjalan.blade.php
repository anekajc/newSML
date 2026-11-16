@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Retur Surat Jalan')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>Retur Surat Jalan</h1> - judul pindah ke header (page-title) --}}
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

</div>

<div id="tempPrintContainer" style="display:none">

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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Surat Jalan</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Retur Surat Jalan</a>
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
                          <th scope="col">No. SJ</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Gdg</th>
                          <th scope="col">Nama Cust Supp</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{$outstandingArray[$i][0]->NOBUKTI}}', '{{$outstandingArray[$i][0]->KodeGdg}}')" ><i class="bi bi-info-lg"></i></button>
                            <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('{{$outstandingArray[$i][0]->NOBUKTI}}', '{{$outstandingArray[$i][0]->KodeGdg}}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
                          </td>
                          <td>{{ $outstandingArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->Tanggal)) !!}</td>
                          <td>{{ $outstandingArray[$i][0]->NamaGdg }}</td>
                          <td>{{ $outstandingArray[$i][0]->NAMACUSTSUPP }}</td>

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
                          <th scope="col">No. RSPB</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">No. SJ</th>
                          <th scope="col">Gudang</th>
                          <th scope="col">Nama Cust</th>
                        </tr>
                      </thead>


                      <tbody id="tabelRetur_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')"><i class="bi bi-pen"></i></button>
                            <button class="btn btn-primary btn-sm" type="button" onclick="buttonPrintPenerimaan('{{ $penerimaanArray[$i][0]->NOBUKTI }}')"><i class="bi bi-printer"></i></button>
                          </td>
                          <td>{{ $penerimaanArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->TANGGAL)) !!}</td>
                          <td>{{ $penerimaanArray[$i][0]->NoSPB }}</td>
                          <td>{{ $penerimaanArray[$i][0]->Namagdg }}</td>
                          <td>{{ $penerimaanArray[$i][0]->NAMACUSTSUPP }}</td>
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
                <label>No RSPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nosj" placeholder="" disabled>
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
                <input type="text" class="form-control" id="input_add_namacust" placeholder="Gdg Tujuan" disabled>
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
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Terima</th>

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
                <label>No SJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_detail_namacust" placeholder="Gdg Tujuan" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
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
                <label>No RSPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nosj" placeholder="No TRF" disabled>
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

<!-- start modal koreksi detail-->
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


            <div class="col-12">
              <div class="form-group">
                <label>No RSPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_koreksidetail_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

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
<!-- End modal koreksi detail-->


<!-- start modal koreksi detail-->
<div class="modal fade" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Print</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No RSPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_print_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="printTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">v</th>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>

                    </tr>
                  </thead>


                  <tbody id="printTableData" class="text-right" >
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

          <div class="container-fluid">
            <div class="row">
              <div class="col-12 text-right">
                <label>Jumlah Print</label>

                <input type="number" value=1 style="max-width: 50px" class="text-right" id="input_print_jumlahprint" >
              </div>
              <!-- <div class="col-1">
              </div> -->

            </div>

          </div>



        <div class="modal-footer mt-4">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
        </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi detail-->


@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">


  // loadall
  let dataRefreshOutstanding = []
  let dataRefreshPenerimaan = []
  // add
  let addDataArray = []

  // koreksi
  let koreksiPenerimaanArray = []
  let koreksiDataEdit = {}
  let koreksiDataAddlist = []


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
    let inputDate = $("#input_add_tanggal").val();
    let nosj = $(`#input_add_nosj`).val();
    let norspb = $(`#input_add_norspb`).val();
    let nourut =  $(`#input_add_noUrut`).val();

    let checkDate = new Date(inputDate)

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    console.log('nosj', nosj)
    console.log('norspb', norspb)
    console.log('nourut', nourut)
    console.log('inputDate', inputDate)
    console.log(tempData)

    $.ajax({
      url: "{!! url('addSuratJalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nosj,
        norspb,
        nourut,
        tempData,
        inputDate
      },
      success: function(res) {
        console.log(res ,'!')
        // loadAll()
        if(res==1) {
          $("#form").modal('toggle')

          alertify.success('RSPB telah ditambah');
          console.log('before ===========')
          loadAll()
          console.log("after ===========")
        }

        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })
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
    document.getElementById("koreksiEditQtyOS").value = koreksiDataEdit.QntOS
    document.getElementById("koreksiEditInputQty").value = qnt
    document.getElementById("koreksiEditSatuan").value = koreksiDataEdit.SATUAN
    $('#formKoreksiEdit').show();
    document.getElementById('formKoreksiEdit').scrollIntoView();
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
    document.getElementById("koreksiAddSatuan").value = "-"
    $('#formKoreksiAdd').show();
  }

  function buttonDetailKoreksi(NOBUKTI ) {
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpenerimaanretursuratjalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,
      },
      success: function(res) {
        console.log(res)
        // koreksiPenerimaanArray = res
        // koreksiDataEdit = res[0]
        console.log('=============!!==============')
        document.getElementById("input_koreksidetail_norspb").value = res[0].NOBUKTI
        document.getElementById("input_koreksidetail_nosj").value = res[0].NoSPB
        document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg
        document.getElementById("input_koreksidetail_custsupp").value = res[0].NAMACUSTSUPP

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
          rowTable += `<tr style="text-align: left">
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td style="text-align: left">${qnt}</td>
          <td style="text-align: left">${qntos}</td>
          <td>${item.SATUAN}</td>


          </td>
          </tr>`
        });
        document.getElementById("koreksiDetailTableData").innerHTML = rowTable

        res.forEach((item, i) => {
         // JsBarcode(`#barcodeKoreksiDetail${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
        });

      }
    })


      $("#formKoreksiDetail").modal('toggle')
  }

  function submitPrint () {

    let printContent1 = ""
    let jumlahPrint = $(`#input_print_jumlahprint`).val();
    let z = 0
    koreksiPenerimaanArray.forEach((item, i) => {
      if (document.getElementById(`printChecklist${i}`).checked) {
        // qrcodeprint
        let date = new Date(item.TANGGAL);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;

        let printContent2 = document.getElementById(`tempqrcodeprintkodebrg${i}`).innerHTML;
        printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`
        for (let i=0; i < jumlahPrint ; i++ ) {
          // console.log(z, i, )


          // printContent1 += `<div style="break-inside: avoid; ">`
          // printContent1 += `<div  style="display: flex; flex-direction: column;">`
          // printContent1 += `<div class="" style="display: flex; flex-direction: row; text-align: center">`
          // printContent1 += `<div style=""><h6> ${item.NoBukti}</h5> </div>`
          // printContent1 += `<div ></div>`
          // printContent1 += `<div style=" margin-left: 95px"> <h6>${item.KodeBrg} </h5> </div>`
          // printContent1 += `</div>`
          //
          // printContent1 += `<div class="" style="display: flex; flex-direction: row">`
          // printContent1 += `<div >` + printContent +`</div>`
          // printContent1 += `<div style="width: 20px">` + `</div>`
          // printContent1 += `<div style=" margin-left: 80px">`+ printContent2 +`</div>`
          // printContent1 += `</div>`
          //
          // printContent1 += `</div>`
          // printContent1 += `<div style="width: 330px; text-align: center">`
          // printContent1 += `<h6>${item.NamaBrg} @ ${item.Satuan}</h6>`
          // printContent1 += `</div>`
          //
          // printContent1 += `<div style="margin-top: -20px;width: 330px; text-align: center">`
          // printContent1 += `<h6>${item.NamaSupplier}</h6>`
          // printContent1 += `</div>`
          //
          // printContent1 += `<div style="margin-top: -20px;margin-bottom: 40px;width: 330px; text-align: center">`
          // printContent1 += `<h6>${date1} / ${item.Qnt} ${item.Satuan}</h6>`
          // printContent1 += `</div>`
          // printContent1 += `</div>`
          // printContent1 += `<div style=' page-break-before: always'></div>`

          if (z == 0) {
            printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; padding-right: 3px">`
          } else {
            printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; page-break-before: always; margin-top: 10px; padding-right: 3px">`
          }

          // printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; page-break-after: always">`
          printContent1 += `<div  style="display: flex; flex-direction: column; margin-top: -25px" >`
          printContent1 += `<div class="" style="display: flex; flex-direction: row; text-align: center,">`
          printContent1 += `<div style="width:70%; padding-left: 5px; text-align: left"><h6 style=""> ${item.NOBUKTI}</h6> </div>`
          // printContent1 += `<div style="width:40%" ></div>`
          printContent1 += `<div style=" width:30%; padding-right: 8px; margin-right: 10px" > <h6>${item.KODEBRG} </h6> </div>`
          printContent1 += `</div>`

          printContent1 += `<div class="" style="display: flex; flex-direction: row;margin-top: -20px">`
          printContent1 += `<div  style="width:70%; padding-left: 5px">` + printContent +`</div>`
          // printContent1 += `<div style="width:10%">` + `</div>`
          printContent1 += `<div style=" width:30%; padding-right: 18px">`+ printContent2 +`</div>`
          printContent1 += `</div>`

          printContent1 += `</div>`
          printContent1 += `<div style="width: 7.5cm; text-align: center ; margin-top: -15px">`
          printContent1 += `<h6 style="font-weight: bold; font-size: 10px;padding-right: 5px">${item.NAMABRG} ${ item.NAMAMERK ? ' / ' + item.NAMAMERK : '' }</h6>`
          printContent1 += `</div>`



          printContent1 += `<div style="margin-top: -25px; text-align: center">`
          printContent1 += `<h6>${date1} / ${item.QNT} ${item.SATUAN}</h6>`
          printContent1 += `</div>`
          printContent1 += `</div>`
          // printContent1 += `<div style=' page-break-before: always'></div>`
          z++
        }
        printContent1 += '</div>'
      }
    });

    document.getElementById("printContainer").innerHTML = printContent1
    w=window.open(' ');
    // w.opener = null;
    // w.refferer = null;
    // w.rel="noopener , noreferrer"

    w.document.write($(`#printContainer`).html());
    console.log('a')
    w.print();
    console.log('b')
    w.close();
    console.log('c')


  }



  function buttonBatalShowHide() {
    $('.showhide').hide();
  }

    function buttonPrintPenerimaan(NOBUKTI) {
      let akses = $("#akses_iscetak").val();

      if (!Number(akses)) {
        alertify.warning('No access')
        return
      }
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('detailpenerimaanretursuratjalan') !!}",
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
          console.log('=============!!==============')
          document.getElementById("input_print_norspb").value = res[0].NOBUKTI
          document.getElementById("input_print_nosj").value = res[0].NoSPB
          document.getElementById("input_print_gdg").value = res[0].Namagdg
          document.getElementById("input_print_custsupp").value = res[0].NAMACUSTSUPP

          let date = new Date(res[0].TANGGAL);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
          $('#input_koreksidetail_tanggal').val(date1)

          let rowTable = ""
          let rowTableTemp = ''
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
            <td><div class="form-check text-center">
                <input id="printChecklist${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                </div></td>
            <td>${item.KODEBRG}</td>
            <td>${item.NAMABRG}</td>
            <td class="text-right">${qnt}</td>
            <td class="text-right">${qntos}</td>
            <td>${item.SATUAN}</td>


            </td>
            </tr>`

            rowTableTemp += `
            <div id="tempqrcodeprint${i}" style="width: 10px; height: 10px"></div>

            <div id="tempqrcodeprintkodebrg${i}" style="width: 10px; height: 10px"></div>
            `
          });
          document.getElementById("printTableData").innerHTML = rowTable

          document.getElementById("tempPrintContainer").innerHTML = rowTableTemp

          res.forEach((item, i) => {
           // JsBarcode(`#barcodeKoreksiDetail${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
           new QRCode(document.getElementById(`tempqrcodeprint${i}`), {text: `${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}` , width: 90, height: 90});

           new QRCode(document.getElementById(`tempqrcodeprintkodebrg${i}`), {text: `${item.KODEBRG}` , width: 90, height: 90});
          });

        }
      })


        $("#formPrint").modal('toggle')
    }

  function buttonKoreksi (NOBUKTI) {
    console.log('button koreksi')
    console.log(NOBUKTI)
    $('.showhide').hide();


    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpenerimaanretursuratjalan') !!}",
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
        console.log('=============!!==============')
      }
    })

    if(!koreksiPenerimaanArray.length) {
      alertify.warning('Data tidak ditemukkan silahkan refresh browser')
      return
    }

    if (Number(koreksiPenerimaanArray[0].IsOtorisasi1) ) {
      alertify.warning('Data sudah di otorisasi')
      return
    }

    let checkLockPeriode = [{
      BULAN: 0,
      TAHUN: 0
    }]
    let checkDateLockPeriode = new Date(koreksiPenerimaanArray[0].TANGGAL)

    $.ajax({
      url: "{!! url('lockperiodecheck') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        tahun: checkDateLockPeriode.getFullYear(),
        bulan: checkDateLockPeriode.getMonth() + 1
      },
      success: function(res) {
        console.log(res)
        checkLockPeriode = res
      }
    })

    if ( checkLockPeriode.length ) {

        alertify.warning("Periode sudah di kunci");
        return
    }


    document.getElementById("input_koreksi_norspb").value = koreksiPenerimaanArray[0].NOBUKTI
    document.getElementById("input_koreksi_nosj").value = koreksiPenerimaanArray[0].NoSPB
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
      <td>${item.SATUAN}</td>

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

    $.ajax({
      url: "{!! url('suratjalankoreksiaddlist') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        norspb: koreksiPenerimaanArray[0].NOBUKTI,
        nosj: koreksiPenerimaanArray[0].NoSPB,
        kodegdg: koreksiPenerimaanArray[0].KodeGdg
      },
      success: function(res) {
        console.log(res , "addlistkoreksi !!!!!!")
        koreksiDataAddList = res
      }
    })
    // console.log(koreksiDataAddList, "asdadasd")
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
    document.getElementById("koreksiAddSatuan").value = koreksiDataAddList[indexBarang].SATUAN
    document.getElementById("koreksiAddQtyOS").value = qntos
    document.getElementById("koreksiAddQtyPO").value = qnt

  }

  function buttonKoreksiDelete (index) {
    console.log(koreksiPenerimaanArray[index])
    let dataRSPB = koreksiPenerimaanArray[index]
    console.log(dataRSPB)
    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataRSPB.KODEBRG + dataRSPB.NAMABRG + ' ?',
      function() {
        let choice = 'D'
        let _token = $("#_token").val();
        let norspb = dataRSPB.NOBUKTI
        let nourut = dataRSPB.NoUrut
        let inputDate = $("#input_koreksi_tanggal").val()
        let nosj = dataRSPB.NoSPB
        let kodecustsupp = dataRSPB.KodeCustSupp
        let nopolkend = ""
        let container = ""
        let nocontainer = ""
        let noseal = ""
        let catatan = ""
        let urut = dataRSPB.URUT
        let urutsj = 0
        let kodebrg = dataRSPB.KODEBRG
        let sat_1 = dataRSPB.SAT_1
        let sat_2 = dataRSPB.SAT_2
        let nosat = dataRSPB.NOSAT
        let isi = 0
        let netw = 0
        let grossw = 0
        let isempty = 1
        let namabrg = dataRSPB.NAMABRG
        let flagmenu = 1
        let flagtipe = 1
        let retursupp = 0
        let qntTerima = 0
        let qntTerima1 = 0
        let qntTerima2 = 0


        console.log('choice' , choice)
        console.log('norspb' , norspb)
        console.log('nourut', nourut)
        console.log('inputDate' , inputDate)
        console.log('nosj' , nosj)
        console.log('kodecustsupp' , kodecustsupp)
        console.log('nopolkend' , nopolkend)
        console.log('container' , container)
        console.log('nocontainer' , nocontainer)
        console.log('noseal' , noseal)
        console.log('catatan' , catatan)
        console.log('urut' , urut)
        console.log('urutsj' , urutsj)
        console.log('kodebrg' , kodebrg)
        console.log('qntTerima' , qntTerima)
        console.log('qntTerima2' , qntTerima2)
        console.log('sat_1' , sat_1)
        console.log('sat_2' , sat_2)
        console.log('nosat' , nosat)
        console.log('isi' , isi)
        console.log('netw' , netw)
        console.log('grossw' , grossw)
        console.log('isempty' , isempty)
        console.log('namabrg' , namabrg)
        console.log('flagmenu' , flagmenu)
        console.log('flagtipe' , flagtipe)
        console.log('retursupp' , retursupp)
        $.ajax({
          url: "{!! url('koreksiSuratJalan') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            norspb,
            nourut,
            inputDate,
            nosj,
            kodecustsupp,
            nopolkend,
            container,
            nocontainer,
            noseal,
            catatan,
            urut,
            urutsj,
            kodebrg,
            qntTerima,
            qntTerima2,
            sat_1,
            sat_2,
            nosat,
            isi,
            netw,
            grossw,
            isempty,
            namabrg,
            flagmenu,
            flagtipe,
            retursupp,
          },
          success: function(res) {
            console.log(res ,'succes delete koreksi')
            refreshKoreksi(norspb)
            alertify.success('Item telah didelete');
            loadAll()
          }
        })
      }
    ,function(){
      console.log('no')
    });
  }



  function submitAddKoreksi () {
    // console.log('submit add koreksi')
    // console.log(koreksiDataTrt)
    let _token = $("#_token").val();
    let check = document.getElementById("koreksiAddSelect").value;
    // console.log(check,'check')
    if (check === "") {
      // console.log('a')
      alertify.warning("Tidak ada item dipilih");
      return
    }
    let dataSJ = koreksiDataAddList[check]
    let qntTerima = $("#koreksiAddInputQty").val()
    if (Number(qntTerima) > Number(dataSJ.QntOS)) {
      alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
      return
    }
    if (Number(qntTerima) <= 0) {
      alertify.warning("Qty tidak bisa 0 atau negatif");
      return
    }

    let qntTerima1 = 0
    let qntTerima2 = 0
    if (dataSJ.NOSAT == 1) {
      qntTerima1 = qntTerima
      qntTerima2 = qntTerima / dataSJ.ISI2
    } else if (dataSJ.NOSAT == 2) {
      qntTerima1 = qntTerima * dataSJ.ISI2
      qntTerima2 = qntTerima
    }
    console.log(dataSJ , qntTerima)
    console.log(koreksiDataEdit)
    let dataRSPB = koreksiDataEdit
    let choice = "I"
    let norspb = dataRSPB.NOBUKTI
    let nourut = dataRSPB.NoUrut
    let inputDate = $("#input_koreksi_tanggal").val()
    let nosj = dataSJ.NOBUKTI
    let kodecustsupp = dataSJ.KODECUSTSUPP
    let nopolkend = ""
    let container = ""
    let nocontainer = ""
    let noseal = ""
    let catatan = ""
    let urut = 0
    let urutsj = dataSJ.URUT
    let kodebrg = dataSJ.KODEBRG
    let sat_1 = dataSJ.SAT_1
    let sat_2 = dataSJ.SAT_2
    let nosat = dataSJ.NOSAT
    let isi = dataSJ.ISI
    let netw = 0
    let grossw = 0
    let isempty = 1
    let namabrg = dataSJ.NAMABRG
    let flagmenu = 1
    let flagtipe = 1
    let retursupp = 0


    console.log('choice' , choice)
    console.log('norspb' , norspb)
    console.log('nourut', nourut)
    console.log('inputDate' , inputDate)
    console.log('nosj' , nosj)
    console.log('kodecustsupp' , kodecustsupp)
    console.log('nopolkend' , nopolkend)
    console.log('container' , container)
    console.log('nocontainer' , nocontainer)
    console.log('noseal' , noseal)
    console.log('catatan' , catatan)
    console.log('urut' , urut)
    console.log('urutsj' , urutsj)
    console.log('kodebrg' , kodebrg)
    console.log('qntTerima' , qntTerima)
    console.log('qntTerima2' , qntTerima2)
    console.log('sat_1' , sat_1)
    console.log('sat_2' , sat_2)
    console.log('nosat' , nosat)
    console.log('isi' , isi)
    console.log('netw' , netw)
    console.log('grossw' , grossw)
    console.log('isempty' , isempty)
    console.log('namabrg' , namabrg)
    console.log('flagmenu' , flagmenu)
    console.log('flagtipe' , flagtipe)
    console.log('retursupp' , retursupp)

    $.ajax({
      url: "{!! url('koreksiSuratJalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        norspb,
        nourut,
        inputDate,
        nosj,
        kodecustsupp,
        nopolkend,
        container,
        nocontainer,
        noseal,
        catatan,
        urut,
        urutsj,
        kodebrg,
        qntTerima,
        qntTerima2,
        sat_1,
        sat_2,
        nosat,
        isi,
        netw,
        grossw,
        isempty,
        namabrg,
        flagmenu,
        flagtipe,
        retursupp,
      },
      success: function(res) {
        console.log(res ,'succes add koreksi')
        refreshKoreksi(norspb)
        alertify.success('Item telah ditambah');
        loadAll()
      }
    })


  }

  function submitEditKoreksi () {
    console.log('submit edit koreksi')
    console.log(koreksiDataEdit)
    let dataRSPB = koreksiDataEdit
    let _token = $("#_token").val();
    let choice = "U"
    let qntTerima = $("#koreksiEditInputQty").val()
    if (Number(qntTerima) > Number(koreksiDataEdit.QntOS) + Number(koreksiDataEdit.QNT)) {
      alertify.warning("Qty melebihi Qty OS");
      return
    }
    if(Number(qntTerima) < 0 ) {
      alertify.warning("Qty tidak boleh negatif");
      return
    }
    let qntTerima1 = 0
    let qntTerima2 = 0
    if (dataRSPB.NOSAT == 1) {
      qntTerima1 = qntTerima
      qntTerima2 = qntTerima / dataRSPB.ISI2
    } else if (dataRSPB.NOSAT == 2) {
      qntTerima1 = qntTerima * dataRSPB.ISI2
      qntTerima2 = qntTerima
    }
    let norspb = dataRSPB.NOBUKTI
    let nourut = dataRSPB.NoUrut
    let inputDate = $("#input_koreksi_tanggal").val()
    let nosj = dataRSPB.NoSPB
    let kodecustsupp = dataRSPB.KodeCustSupp
    let nopolkend = ""
    let container = ""
    let nocontainer = ""
    let noseal = ""
    let catatan = ""
    let urut = dataRSPB.URUT
    let urutsj = 0
    let kodebrg = dataRSPB.KODEBRG
    let sat_1 = dataRSPB.SAT_1
    let sat_2 = dataRSPB.SAT_2
    let nosat = dataRSPB.NOSAT
    let isi = dataRSPB.ISI
    let netw = 0
    let grossw = 0
    let isempty = 1
    let namabrg = dataRSPB.NAMABRG
    let flagmenu = 1
    let flagtipe = 1
    let retursupp = 0

    console.log('choice' , choice)
    console.log('norspb' , norspb)
    console.log('nourut', nourut)
    console.log('inputDate' , inputDate)
    console.log('nosj' , nosj)
    console.log('kodecustsupp' , kodecustsupp)
    console.log('nopolkend' , nopolkend)
    console.log('container' , container)
    console.log('nocontainer' , nocontainer)
    console.log('noseal' , noseal)
    console.log('catatan' , catatan)
    console.log('urut' , urut)
    console.log('urutsj' , urutsj)
    console.log('kodebrg' , kodebrg)
    console.log('qntTerima' , qntTerima)
    console.log('qntTerima2' , qntTerima2)
    console.log('sat_1' , sat_1)
    console.log('sat_2' , sat_2)
    console.log('nosat' , nosat)
    console.log('isi' , isi)
    console.log('netw' , netw)
    console.log('grossw' , grossw)
    console.log('isempty' , isempty)
    console.log('namabrg' , namabrg)
    console.log('flagmenu' , flagmenu)
    console.log('flagtipe' , flagtipe)
    console.log('retursupp' , retursupp)

    $.ajax({
      url: "{!! url('koreksiSuratJalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        norspb,
        nourut,
        inputDate,
        nosj,
        kodecustsupp,
        nopolkend,
        container,
        nocontainer,
        noseal,
        catatan,
        urut,
        urutsj,
        kodebrg,
        qntTerima,
        qntTerima2,
        sat_1,
        sat_2,
        nosat,
        isi,
        netw,
        grossw,
        isempty,
        namabrg,
        flagmenu,
        flagtipe,
        retursupp,
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(norspb)
        alertify.success('Item telah diedit');
        loadAll()
      }
    })




  }

  function buttonDetail (NOBUKTI, pKodeGdg) {
    console.log(NOBUKTI,pKodeGdg)

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailoutstandingretursuratjalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,
        kodegdg: pKodeGdg
      },
      success: function(res) {
        console.log(res)
        addDataArray = res
        console.log('===========================')
        document.getElementById("input_detail_nosj").value = res[0].NOBUKTI
        document.getElementById("input_detail_gdg").value = res[0].NamaGdg
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
          rowTable += `<tr style="text-align: left">
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td style="text-align: right">${qnt}</td>
          <td style="text-align: right">${qntos}</td>
          <td>${item.SATUAN}</td>
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
  function setNewNoBukti () {
    $.ajax({
      url: "{!! url('getnobuktiretursuratjalan') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res, 'NoBUkti')
        console.log(res[0].Nobukti , res[0].Nourut)
        console.log('===============')
        document.getElementById("input_add_norspb").value = res[0].Nobukti
        document.getElementById("input_add_noUrut").value = res[0].Nourut
      }
    })
  }


  function buttonAdd (NOBUKTI, pKodeGdg) {
    console.log(NOBUKTI, pKodeGdg)
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
      url: "{!! url('detailoutstandingretursuratjalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,
        kodegdg: pKodeGdg
      },
      success: function(res) {
        console.log(res)
        addDataArray = res
        console.log('===========================')
      }
    })

    document.getElementById("input_add_nosj").value = addDataArray[0].NOBUKTI
    document.getElementById("input_add_gdg").value = addDataArray[0].NamaGdg
    document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP

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
      rowTable += `<tr class="text-right">
      <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
      <td class="text-left">${item.KODEBRG}</td>
      <td class="text-left">${item.NAMABRG}</td>

      <td>${qntos}</td>
      <td class="text-left">${item.SATUAN}</td>
      <td><input onchange="" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td>
      </tr>`
    });
    document.getElementById("addTableData").innerHTML = rowTable
    addDataArray.forEach((item, i) => {
     // JsBarcode(`#barcodeAdd${i}`, item.KODEBRG ,{width: 2, height: 25,});
    });
    $("#form").modal('toggle')
  }

  function refreshKoreksi (NOBUKTI) {
    console.log('REFRESH START')
    console.log(NOBUKTI)
    $('.showhide').hide();

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpenerimaanretursuratjalan') !!}",
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
        console.log('=============!!==============')
      }
    })

    if (!koreksiPenerimaanArray.length) {
      console.log('item habis')
      $("#formKoreksi").modal('toggle')
      return
    }

    document.getElementById("input_koreksi_norspb").value = koreksiPenerimaanArray[0].NOBUKTI
    document.getElementById("input_koreksi_nosj").value = koreksiPenerimaanArray[0].NoSPB
    document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
    document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

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
      <td>${item.SATUAN}</td>

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

    $.ajax({
      url: "{!! url('suratjalankoreksiaddlist') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        norspb: koreksiPenerimaanArray[0].NOBUKTI,
        nosj: koreksiPenerimaanArray[0].NoSPB,
        kodegdg: koreksiPenerimaanArray[0].KodeGdg
      },
      success: function(res) {
        console.log(res , "addlistkoreksi !!!!!!")
        koreksiDataAddList = res
      }
    })
    // console.log(koreksiDataAddList, "asdadasd")
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

  function loadAll() {
    $.ajax({
      url: "{!! url('loadallsuratjalan') !!}",
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
    // console.log(dataRefreshOutstanding, 'out')
    // console.log(dataRefreshPenerimaan, 'pen')

    $('#tabel').DataTable().destroy();
    let rowTable = ""
    dataRefreshOutstanding.forEach((item, i) => {
      // console.log(item,'==================')
      let date1 = ""
      if (item[0].Tanggal) {
        let date = new Date(item[0].Tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }
      rowTable  += `<tr>
      <td class="text-center">
      <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('${item[0].NOBUKTI}' , '${item[0].KodeGdg}')" ><i class="bi bi-info-lg"></i></button>
      <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item[0].NOBUKTI}', '${item[0].KodeGdg}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button></td>
      <td>${item[0].NOBUKTI}</td>
      <td>${date1}</td>
      <td>${item[0].NamaGdg}</td>
      <td>${item[0].NAMACUSTSUPP}</td>
      </tr>`
    })


    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({'order': [[1, 'asc']],
      "lengthChange": false,
        "paging": false ,
         "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], "orderable" : false },
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
      <button class="btn btn-primary btn-sm" type="button" onclick="buttonPrintPenerimaan('${item[0].NOBUKTI}')"><i class="bi bi-printer"></i></button>
      </td>
      <td>${item[0].NOBUKTI}</td>
      <td>${date1}</td>
      <td>${item[0].NoSPB}</td>
      <td>${item[0].Namagdg}</td>
      <td>${item[0].NAMACUSTSUPP}</td>
      </tr>`
    });

    document.getElementById("tabelRetur_data").innerHTML = rowTableRetur

    $("#tabelRetur").DataTable({
      'order': [[1, 'asc']],
      "lengthChange": false,
        "paging": false ,
         "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], "orderable" : false },
    ]});
  }


</script>




@endsection
