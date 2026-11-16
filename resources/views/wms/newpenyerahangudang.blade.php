@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Pemakaian Internal')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>Pemakaian Internal</h1> - judul pindah ke header (page-title) --}}
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Pemakaian Internal</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Pemakaian Internal</a>
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
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{ $outstandingArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            {{-- Dinonaktifkan 2026-10-22: tombol keranjang diganti tombol + (sama dengan In Bound / Out Bound). --}}
                            {{-- <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('{{ $outstandingArray[$i][0]->NOBUKTI }}')"><i class="bi bi-bag-plus-fill"></i></button> --}}
                            <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('{{ $outstandingArray[$i][0]->NOBUKTI }}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
                          </td>
                          <td>{{ $outstandingArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->TANGGAL)) !!}</td>
                          <td>{{ $outstandingArray[$i][0]->NamaGudangAsal }}</td>
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
                <label>No PBG</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nopbg" placeholder="No PBG" disabled>
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

            <!-- <div class="col-2">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_namacust" placeholder="Gdg Tujuan" disabled>
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

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Qty Kirim </th>
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
            <div class="col-2">
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
                <input type="text" class="form-control" id="input_detail_noout" placeholder="No PBG" disabled>
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
                <input type="text" class="form-control" id="input_detail_namacust" placeholder="Gdg Tujuan" disabled>
              </div>
            </div> -->
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
                <label>No PBG</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nopbg" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noout" placeholder="No PBG" disabled>
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
                <label>No PBG</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nopbg" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_noout" placeholder="No PBG" disabled>
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
        {  "className": "text-center", "targets": [0] , "orderable" :false },
      ]});
      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] , "orderable" :false },
      ]});


});


function buttonBatalShowHide() {
  $('.showhide').hide();
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
  let nopbg = dataPBG.NOBUKTI
  let nourut = dataPBG.NOURUT
  let inputDate = $("#input_koreksi_tanggal").val()
  let kodegdg = dataPBG.Kodegdg
  let urut = 0
  let kodebrg = dataOut.KODEBRG
  let nosat = dataOut.NOSAT
  let sat = dataOut.Satuan
  let isi = dataOut.ISI
  let nobppb = ""
  let urutspk = 0
  let nosatspk = 0
  let issample = 0
  let isbarang = 1
  let keterangan = ""
  let kddep = ""
  let nopr = dataOut.NOBUKTI
  let urutpr = dataOut.Urut

  console.log('choice' , choice)
  console.log('nopbg' , nopbg)
  console.log('nourut' , nourut)
  console.log('inputDate' , inputDate)
  console.log('kodegdg' , kodegdg)
  console.log('urut' , urut)
  console.log('kodebrg' , kodebrg)
  console.log('qntTerima' , qntTerima)
  console.log('nosat' , nosat)
  console.log('sat', sat)
  console.log('isi' , isi)
  console.log('nobppb' , nobppb)
  console.log('qntTerima2', qntTerima2)
  console.log('urutspk' , urutspk)
  console.log('nosatspk' , nosatspk)
  console.log('issample' , issample)
  console.log('isbarang' , isbarang)
  console.log('keterangan' , keterangan)
  console.log('kddep' , kddep)
  console.log('nopr' , nopr)
  console.log('urutpr' , urutpr)


  $.ajax({
    url: "{!! url('koreksipenyerahangudang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nopbg,
      nourut,
      inputDate,
      kodegdg,
      urut,
      kodebrg,
      qntTerima,
      nosat,
      sat,
      isi,
      nobppb,
      qntTerima2,
      urutspk,
      nosatspk,
      issample,
      isbarang,
      keterangan,
      kddep,
      nopr,
      urutpr,
    },
    success: function(res) {
      console.log(res ,'succes add koreksi')
      refreshKoreksi(nopbg)
      // Dinonaktifkan 2026-10-22: pesan sukses selalu muncul walau server menolak (3 = stok kurang).
      // alertify.success('Item telah ditambah');
      if (res == 1) {
        alertify.success('Item telah ditambah');
      } else if (res == 3) {
        alertify.warning('Stok gudang tidak mencukupi');
      }
      loadAll()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan pada server, silahkan refresh browser')
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



    // let dataTrf = koreksiDataEdit
    let choice = "U"
    let nopbg = dataPBG.NOBUKTI
    let nourut = dataPBG.NOURUT
    let inputDate = $("#input_koreksi_tanggal").val()
    let kodegdg = dataPBG.Kodegdg
    let urut = dataPBG.URUT
    let kodebrg = dataPBG.KODEBRG
    let nosat = dataPBG.NOSAT
    let sat = dataPBG.Satuan
    let isi = dataPBG.ISI
    let nobppb = ""
    let urutspk = 0
    let nosatspk = 0
    let issample = 0
    let isbarang = 1
    let keterangan = ""
    let kddep = ""
    let nopr = ''
    let urutpr = 0

    console.log('choice' , choice)
    console.log('nopbg' , nopbg)
    console.log('nourut' , nourut)
    console.log('inputDate' , inputDate)
    console.log('kodegdg' , kodegdg)
    console.log('urut' , urut)
    console.log('kodebrg' , kodebrg)
    console.log('qntTerima' , qntTerima)
    console.log('nosat' , nosat)
    console.log('sat', sat)
    console.log('isi' , isi)
    console.log('nobppb' , nobppb)
    console.log('qntTerima2', qntTerima2)
    console.log('urutspk' , urutspk)
    console.log('nosatspk' , nosatspk)
    console.log('issample' , issample)
    console.log('isbarang' , isbarang)
    console.log('keterangan' , keterangan)
    console.log('kddep' , kddep)
    console.log('nopr' , nopr)
    console.log('urutpr' , urutpr)

    $.ajax({
      url: "{!! url('koreksipenyerahangudang') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        nopbg,
        nourut,
        inputDate,
        kodegdg,
        urut,
        kodebrg,
        qntTerima,
        nosat,
        sat,
        isi,
        nobppb,
        qntTerima2,
        urutspk,
        nosatspk,
        issample,
        isbarang,
        keterangan,
        kddep,
        nopr,
        urutpr,
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(nopbg)
        // Dinonaktifkan 2026-10-22: controller mengembalikan 1 saat berhasil, jadi pesan sukses tidak pernah muncul.
        // if (res == 5 ) {
        if (res == 1 || res == 5 ) {
        alertify.success('Item telah diedit');
      }
        if (res == 3 ) {
          alertify.warning('Stok gudang tidak mencukupi');
        }
        loadAll()
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan pada server, silahkan refresh browser')
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

function buttonKoreksiAdd() {
  $('.showhide').hide();
  document.getElementById("koreksiAddKodeBrg").value = ""
  document.getElementById("koreksiAddNamaBrg").value = ""
  document.getElementById("koreksiAddQtyOS").value = 0.00
  document.getElementById("koreksiAddQtyPO").value = 0.00
  document.getElementById("koreksiAddInputQty").value = "0.00"
  $('#formKoreksiAdd').show();
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
  let nopbg = $(`#input_add_nopbg`).val();
  let nourut =  $(`#input_add_noUrut`).val();

  console.log('noout', noout)
  console.log('nopbg', nopbg)
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

  $.ajax({
    url: "{!! url('addpenyerahangudang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nopbg,
      noout,
      nourut,
      tempData,
      inputDate
    },
    success: function(res) {
      console.log(res ,'!')

      if (res == 1) {
        $("#form").modal('toggle')
        alertify.success('PBG telah ditambah');
        loadAll()
      }


      if (res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang')
      }

      if (res == 3 ) {
        alertify.warning('Stok gudang tidak mencukupi');
      }

      // Ditambahkan 2026-10-22: tidak ada baris yang tersimpan (data dokumen Out tidak lengkap).
      if (res == 4 ) {
        alertify.error('Gagal disimpan: data dokumen Out ' + noout + ' tidak lengkap (header tidak ditemukan). Hubungi kantor.');
      }

    },
  error:function (err) {
    console.log(err)
    alertify.warning('Terjadi kesalahan pada server, silahkan refresh browser')
  }})



}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('getnobuktipenyerahangudang') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res, 'NoBUkti')
      console.log(res[0].Nobukti , res[0].Nourut)
      console.log('===============')
      document.getElementById("input_add_nopbg").value = res[0].Nobukti
      document.getElementById("input_add_noUrut").value = res[0].Nourut
    }
  })
}

function buttonAdd (NOBUKTI) {
  console.log('button add')
  console.log(NOBUKTI)
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  setNewNoBukti()


  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingpenyerahangudang') !!}",
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
    <td class="text-center"><input onchange="" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td></tr>`
  });
  document.getElementById("addTableData").innerHTML = rowTable

  $("#form").modal('toggle')


}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingpenyerahangudang') !!}",
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

    }
  })




  $("#formDetail").modal('toggle')

}

function loadAll () {
  $.ajax({
    url: "{!! url('loadallpenyerahangudang') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res)
      dataRefreshOutstanding = res.outstandingArray
      dataRefreshPenerimaan = res.penerimaanArray
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
        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NOBUKTI}')"><i class="bi bi-pen"></i></button>
        </td>
        <td>${item[0].NOBUKTI}</td>
        <td>${date1}</td>
        <td>${item[0].NooutBRg}</td>
        <td>${item[0].Namagdg}</td>
        </tr>`
      });



      document.getElementById("tabelRetur_data").innerHTML = rowTableRetur

      $("#tabelRetur").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] , "orderable" :false },
      ]});


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
        console.log('yes')
        let nopbg = dataPBG.NOBUKTI
        let nourut = dataPBG.NOURUT
        let inputDate = $("#input_koreksi_tanggal").val()
        let kodegdg = dataPBG.Kodegdg
        let urut = dataPBG.URUT
        let kodebrg = dataPBG.KODEBRG
        let nosat = dataPBG.NOSAT
        let sat = dataPBG.Satuan
        let isi = dataPBG.ISI
        let nobppb = ""
        let urutspk = 0
        let nosatspk = 0
        let issample = 0
        let isbarang = 1
        let keterangan = ""
        let kddep = ""
        let nopr = ''
        let urutpr = 0
        let qntTerima = 0
        let qntTerima2 = 0

        console.log('choice' , choice)
        console.log('nopbg' , nopbg)
        console.log('nourut' , nourut)
        console.log('inputDate' , inputDate)
        console.log('kodegdg' , kodegdg)
        console.log('urut' , urut)
        console.log('kodebrg' , kodebrg)
        console.log('qntTerima' , qntTerima)
        console.log('nosat' , nosat)
        console.log('sat', sat)
        console.log('isi' , isi)
        console.log('nobppb' , nobppb)
        console.log('qntTerima2', qntTerima2)
        console.log('urutspk' , urutspk)
        console.log('nosatspk' , nosatspk)
        console.log('issample' , issample)
        console.log('isbarang' , isbarang)
        console.log('keterangan' , keterangan)
        console.log('kddep' , kddep)
        console.log('nopr' , nopr)
        console.log('urutpr' , urutpr)

        $.ajax({
          url: "{!! url('koreksipenyerahangudang') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nopbg,
            nourut,
            inputDate,
            kodegdg,
            urut,
            kodebrg,
            qntTerima,
            nosat,
            sat,
            isi,
            nobppb,
            qntTerima2,
            urutspk,
            nosatspk,
            issample,
            isbarang,
            keterangan,
            kddep,
            nopr,
            urutpr,
          },
          success: function(res) {
            console.log(res ,'succes delete koreksi')
            refreshKoreksi(nopbg)
            // Dinonaktifkan 2026-10-22: pesan sukses selalu muncul apa pun hasil server.
            // alertify.success('Item telah didelete');
            if (res == 1) {
              alertify.success('Item telah didelete');
            } else {
              alertify.warning('Item gagal dihapus');
            }
            loadAll()
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan pada server, silahkan refresh browser')
          }
        })

      }
    ,function(){
      console.log('no')
    });
}

function refreshKoreksi (NOBUKTI) {

  $('.showhide').hide();

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailpenerimaanpenyerahangudang') !!}",
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
  document.getElementById("input_koreksi_nopbg").value = koreksiPenerimaanArray[0].NOBUKTI
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg

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




  $.ajax({
    url: "{!! url('penyerahangudangkoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nopbg: koreksiPenerimaanArray[0].NOBUKTI,
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








  // $("#formKoreksi").modal('toggle')

}

function buttonDetailKoreksi (NOBUKTI) {
  console.log('button detail koreksi')
  console.log(NOBUKTI)

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanpenyerahangudang') !!}",
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

      document.getElementById("input_koreksidetail_noout").value = nooutbrg
      document.getElementById("input_koreksidetail_nopbg").value = res[0].NOBUKTI
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



    }
  })
  //
  // let nooutbrg = ''
  // console.log(koreksiPenerimaanArray[0])
  // if (koreksiPenerimaanArray[0].NooutBRg) {
  //   nooutbrg = koreksiPenerimaanArray[0].NooutBRg
  // }
  //
  // document.getElementById("input_koreksi_noout").value = nooutbrg
  // document.getElementById("input_koreksi_nopbg").value = koreksiPenerimaanArray[0].NOBUKTI
  // document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  //
  // let date = new Date(koreksiPenerimaanArray[0].TANGGAL);
  // let day = ("0" + date.getDate()).slice(-2);
  // let month = ("0" + (date.getMonth() + 1)).slice(-2);
  // date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  // $('#input_koreksi_tanggal').val(date1)
  //
  //
  // let rowTable = ""
  // koreksiPenerimaanArray.forEach((item, i) => {
  //   let qnt = 0.00
  //   let qntos = 0.00
  //   if(item.QNT) {
  //     qnt = parseFloat(item.QNT).toFixed(2)
  //   }
  //   if(item.QntOS) {
  //     qntos = parseFloat(item.QntOS).toFixed(2)
  //   }
  //   rowTable += `<tr>
  //   <td>${item.KODEBRG}</td>
  //   <td>${item.NAMABRG}</td>
  //   <td>${qnt}</td>
  //   <td>${qntos}</td>
  //   <td>${item.Satuan}</td>
  //   <td class="text-center"><div id="containerbarcodeKoreksi${i}"><svg id="barcodeKoreksi${i}"></svg></td>
  //   <td><input id="input_Koreksi_qntPrint${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${qnt}></td>
  //   <td class="text-center">
  //   <button class="btn btn-success btn-sm" type="button" onclick="printBarcode('containerbarcodeKoreksi${i}', 'input_Koreksi_qntPrint${i}')"><i class="bi bi-printer"></i></button>
  //   <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
  //   <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
  //   </td>
  //   </tr>`
  // });
  // document.getElementById("koreksiTableData").innerHTML = rowTable
  //
  //
  // koreksiPenerimaanArray.forEach((item, i) => {
  //   JsBarcode(`#barcodeKoreksi${i}`, item.KODEBRG ,{width: 2, height: 25,
  //     // displayValue: false
  //   });
  // });

  $("#formKoreksiDetail").modal('toggle')

}

function buttonKoreksi(NOBUKTI) {
  console.log('button koreksi')
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanpenyerahangudang') !!}",
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
  document.getElementById("input_koreksi_nopbg").value = koreksiPenerimaanArray[0].NOBUKTI
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg

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




  $.ajax({
    url: "{!! url('penyerahangudangkoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nopbg: koreksiPenerimaanArray[0].NOBUKTI,
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


</script>




@endsection
