@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Retur Jual')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
{{-- <h1>Retur Jual</h1> - judul pindah ke header (page-title) --}}
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <svg id="barcode"></svg> -->
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <button onclick="loadAll1()">tes1</button>
<button onclick="loadAll2()">tes2</button> -->
</div>




<!-- <div class="container"> -->
<div id="loadingContainer" style="display: none" class="container-fluid mt-6 justify-content-middle align-middle">
  <div class="text-center">
    <div class="spinner-border" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Retur Jual</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Transaksi Retur Jual</a>
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
                          <th scope="col">No. PRJ</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Kode Customer</th>
                          <th scope="col">Nama Customer</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center"><button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{$outstandingArray[$i][0]->NOBUKTI}}')" ><i class="bi bi-info-lg"></i></button>
                            <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('{{$outstandingArray[$i][0]->NOBUKTI}}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button></td>
                            <td>{{ $outstandingArray[$i][0]->NOBUKTI }}</td>
                            <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->Tanggal)) !!}</td>
                            <td>{{ $outstandingArray[$i][0]->KodeCustSupp }}</td>
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
                          <th scope="col">No. SPR</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Supp</th>
                          <th scope="col">Gudang</th>
                        </tr>
                      </thead>


                      <tbody id="tabelRetur_data" class="text-left" >
                        @for ($i = 0; $i < count($returGudangArray); $i++)
                        <tr>
                          <td class="text-center"><button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailReturGudang('{{$returGudangArray[$i][0]->NOBUKTI}}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{$returGudangArray[$i][0]->NOBUKTI}}')"><i class="bi bi-pen"></i></button>
                            <button style="" class="btn btn-primary btn-sm" type="button"   onclick="buttonPrintPenerimaan('{{$returGudangArray[$i][0]->NOBUKTI}}')" ><i class="bi bi-printer"></i></button></td>
                            <td>{{ $returGudangArray[$i][0]->NOBUKTI}}</td>
                            <td>{!! date("Y/m/d", strtotime($returGudangArray[$i][0]->Tanggal)) !!}</td>
                            <td>{{ $returGudangArray[$i][0]->NAMACUSTSUPP}}</td>
                            <td>{{ $returGudangArray[$i][0]->NamaGDG}}</td>
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



<!-- <div> -->

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

        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" /> -->
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No SPR</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nospr" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No PRJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_noprj" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_kodecust" placeholder="Kode Cust" required disabled>
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
                <input type="text" class="form-control" id="input_add_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">

            <!-- <div class="col-2">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
              <select id="input_add_gudang" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                @foreach ($gudang as $g)
                <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} {{ $g->NAMA }}</option>
                @endforeach
              </select>
            </div>
          </div> -->
        </div>
        <div class="row">


        </div>
        <!-- <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Surat Jln Supp</label>
            </div>
          </div>
          <div class="col-10">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_suratjalansupp" placeholder="Surat Jln" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>No. Kend / Sopir</label>
            </div>
          </div>
          <div class="col-10">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_nokend" placeholder="No Kend / Sopir" required>
            </div>
          </div>
        </div> -->
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

<!-- start modal add -->
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

        <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="{!! csrf_token() !!}" /> -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="detail_kodecust" placeholder="Kode Cust" required disabled>
              </div>
            </div>


            <div class="col-12">
              <div class="form-group">
                <label>No PRJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="detail_noprj" placeholder="No Bukti" disabled>
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
                <input type="text" class="form-control" id="detail_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>




          </div>
          <div class="row">

            <!-- <div class="col-2">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
              <select id="input_add_gudang" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                @foreach ($gudang as $g)
                <option value="{{ $g->KODEGDG }}">{{ $g->KODEGDG }} {{ $g->NAMA }}</option>
                @endforeach
              </select>
            </div>
          </div> -->
        </div>
        <div class="row">


        </div>
        <!-- <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>Surat Jln Supp</label>
            </div>
          </div>
          <div class="col-10">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_suratjalansupp" placeholder="Surat Jln" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-2">
            <div class="form-group">
              <label>No. Kend / Sopir</label>
            </div>
          </div>
          <div class="col-10">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_nokend" placeholder="No Kend / Sopir" required>
            </div>
          </div>
        </div> -->
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
<!-- End modal detail out-->

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
                <label>No SPR</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nospr" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No PRJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noprj" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_kodecust" placeholder="Kode Cust" required disabled>
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
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Nama Cust" disabled>
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
              <!-- <div class="col-1">
                <div class="form-group">
                <label>Qty PO</label>
              </div>
              </div>
              <div class="col-3">

                  <input id="koreksiEditQtyPO" type="number" value=0.00 class="form-control" disabled>
              </div> -->
            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
                <button type="button" onclick="submitEditKoreksi()" class="btn btn-primary" >Edit Item</button>
              </div>

            </div>
            <div class="line"></div>
          </div>
        </div>

          <div class="container-fluid mt-4" style="overflow:auto;">

            <!-- koreksi add -->



                <table id="koreksiTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>
                      <th scope="col">Actionss</th>
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





      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="">Submit</button>
      </div> -->
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi-->

<!-- start modal koreksi -->
<div class="modal fade" id="formDetailKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <label>No SPR</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="detailKoreksi_nospr" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No PRJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="detailKoreksi_noprj" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="detailKoreksi_kodecust" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="detailKoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="detailKoreksi_namacust" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



          <div class="container-fluid mt-4" style="overflow:auto;">

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


      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="">Submit</button>
      </div> -->
    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi-->


<!-- start modal koreksi -->
<div class="modal fade" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
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
                <label>No SPR</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_nospr" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No PRJ</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_noprj" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_kodecust" placeholder="Kode Cust" required disabled>
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
                <label>Nama Cust</label>
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



          <div class="container-fluid mt-4" style="overflow:auto;">

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


            </div>

          </div>



        <div class="modal-footer mt-4">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
          <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
        </div>

      </div>


      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="">Submit</button>
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
// loadAll
let dataRefreshOutstanding = []
let dataRefreshReturGudang = []

// data add
let addDataArray = []

//data koreksi
let dataSPR = {}
let koreksiDataArray = []
let koreksiDataAddList = []
let koreksiDataEdit = {}

$(document).ready(function(){
      $("#tabel").DataTable({'order': [[1, 'asc']], "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] , "orderable" : false},
      ]});

    $("#tabelRetur").DataTable({'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], "orderable" : false },
    ]});
});

function loadAll () {

  $.ajax({
    url: "{!! url('loadallreturjual') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res)
      console.log(res.outstandingArray)
      console.log(res.returGudangArray)
      dataRefreshOutstanding = res.outstandingArray
      dataRefreshReturGudang = res.returGudangArray
      document.getElementById("akses_istambah").value = res.akses.ISTAMBAH
      document.getElementById("akses_iskoreksi").value = res.akses.ISKOREKSI
      document.getElementById("akses_iscetak").value = res.akses.ISCETAK
      document.getElementById("akses_ishapus").value = res.akses.ISHAPUS
    }})

    //loadall outstanding
    $('#tabel').DataTable().destroy();
    let rowTable = ""
    dataRefreshOutstanding.forEach((item, i) => {
      let date1 = ""
      if (item[0].Tanggal) {
        let date = new Date(item[0].Tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }
      rowTable += `<tr>
      <td class="text-center"><button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
      <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item[0].NOBUKTI}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button></td>
      <td>${item[0].NOBUKTI}</td>
      <td>${date1}</td>
      <td>${item[0].KodeCustSupp}</td>
      <td>${item[0].NAMACUSTSUPP}</td>
      </tr>`
    });
    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({ 'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], "orderable" : false },
    ]});

    //loadall returgudang
    $('#tabelRetur').DataTable().destroy();
    let rowTableRetur = ""

    dataRefreshReturGudang.forEach((item, i) => {
      let date1 = ""
      if (item[0].Tanggal) {
        let date = new Date(item[0].Tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }

      rowTableRetur += `<tr>
      <td class="text-center"><button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailReturGudang('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
      <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NOBUKTI}')"><i class="bi bi-pen"></i></button>
      <button class="btn btn-primary btn-sm" type="button" onclick="buttonPrintPenerimaan('${item[0].NOBUKTI}')"><i class="bi bi-printer"></i></button></td>
      <td>${item[0].NOBUKTI}</td>
      <td>${date1}</td>
      <td>${item[0].NAMACUSTSUPP}</td>
      <td>${item[0].NamaGDG}</td>
      </tr>`
    });

    document.getElementById("tabelRetur_data").innerHTML = rowTableRetur



    $("#tabelRetur").DataTable({'order': [[1, 'asc']], "lengthChange": false, "paging": false , "columnDefs": [
      { "type": "date", "targets": [2] },
      {  "className": "text-center", "targets": [0], "orderable" : false },
    ]});


}


function onChangePrint (index) {
  // input_add_qnt input_add_qntTerima
  let qtyTerima = $(`#input_add_qntTerima${index}`).val();
  // let qtyReject= $(`#input_add_qntTerima${index}`).val();
  // Dinonaktifkan 2026-10-20: kolom input_add_qntPrint sudah tidak ada di tabel tambah -> error JS tiap qty diubah.
  // document.getElementById(`input_add_qntPrint${index}`).value = qtyTerima
  let inputPrint = document.getElementById(`input_add_qntPrint${index}`)
  if (inputPrint) {
    inputPrint.value = qtyTerima
  }
}

function submitAddKoreksi () {



  let _token = $("#_token").val();
  let choice = "I"
  console.log(' koreksi submit add')
  let check = document.getElementById("koreksiAddSelect").value;
  if (check === "") {
    console.log('a')
    alertify.warning('Tidak ada barang dipilih')
    return
  }
  let nospr = $("#input_koreksi_nospr").val()
  // console.log(nospr, dataSPR.NOBUKTI)
  // console.log(dataSPR, 'dataSPR')
  let tanggal = $("#input_koreksi_tanggal").val()
  let noprj = $("#input_koreksi_noprj").val()
  let nourut = dataSPR.NoUrut
  // let kodebrg = $("#koreksiAddKodeBrg").val()
  // let namabrg = $("#koreksiAddNamaBrg").val()

  // console.log(nospr,noprj,kodebrg,namabrg,inputQty)
  // console.log(koreksiDataAddList[check])
  let dataSubmit = koreksiDataAddList[check]
  let kodecustsupp = koreksiDataAddList[check].KodeCustSupp
  let nopolkend = ""
  let container = ""
  let nocontainer = ""
  let noseal = ""
  let catatan = ""
  let urut = 0
  let urutrpj = koreksiDataAddList[check].URUT
  let kodebrg = koreksiDataAddList[check].KODEBRG
  let inputQty = $("#koreksiAddInputQty").val()
  let inputQty1 = 0
  let inputQty2 = 0
  if (koreksiDataAddList[check].nosat == 1) {
    inputQty1 = inputQty
    inputQty2 = inputQty / koreksiDataAddList[check].ISI2
  } else if (koreksiDataAddList[check].nosat == 2) {
    inputQty1 = inputQty * koreksiDataAddList[check].ISI2
    inputQty2 = inputQty
  }
  let sat_1 = koreksiDataAddList[check].tempSAT_1
  let nosat = koreksiDataAddList[check].nosat
  let isi = koreksiDataAddList[check].ISI
  let netw = 0
  let grossw = 0
  let isempty = 1
  let namabarang = koreksiDataAddList[check].NAMABRG
  let sopir = ""
  let flagmenu = 1
  let kodegdg = dataSPR.KodeGdg
  let flagtipe = 1
  let tipePPN = 0
  let noSO = 0
  let noBatch = 0
  let flagKembali = 0
  let qntReject = 0
  let qntReject1 = 0
  let qntReject2 = 0
  let sat_2 = koreksiDataAddList[check].SAT_2

  $.ajax({
    url: "{!! url('koreksiReturJual') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nospr,
      nourut,
      tanggal,
      noprj,
      kodecustsupp,
      nopolkend,
      container,
      nocontainer,
      noseal,
      catatan,
      urut,
      urutrpj,
      kodebrg,
      inputQty,
      inputQty1,
      inputQty2,
      sat_1,
      nosat,
      isi,
      netw,
      grossw,
      isempty,
      namabarang,
      sopir,
      flagmenu,
      kodegdg,
      flagtipe,
      tipePPN,
      noSO,
      noBatch,
      flagKembali,
      qntReject,
      qntReject1,
      qntReject2,
      sat_2
    },
    success: function(res) {
      console.log(res ,'!?!')
      alertify.success('Item telah ditambah');
      loadAll()
      refreshKoreksi(nospr)
    }})

  // console.log('choice' , choice)
  // console.log('nospr', nospr, dataSPR.NOBUKTI)
  // console.log('nourut', nourut)
  // console.log('tanggal', tanggal)
  console.log('noprj', noprj)
  // console.log('kodecustsupp', kodecustsupp)
  // console.log('nopolkend', nopolkend)
  // console.log('container', container)
  // console.log('nocontainer', nocontainer)
  // console.log('noseal', noseal)
  // console.log('catatan', catatan)
  // console.log('urut', urut , 'add=0')
  // console.log('urutrpj', urutrpj)
  // console.log('kodebrg', kodebrg)
  // console.log('inputQty', inputQty)
  // console.log('inputQty1', inputQty1)
  // console.log('inputQty2', inputQty2)
  // console.log('sat_1', sat_1)
  // console.log('nosat', nosat)
  // console.log('isi', isi)
  // console.log('netw', netw)
  // console.log('grossw', grossw)
  // console.log('isempty', isempty)
  // console.log('namabarang', namabarang)
  // console.log('sopir', sopir)
  // console.log('flagmenu', flagmenu)
  // console.log('kodegdg', kodegdg)
  // console.log('flagtipe', flagtipe)
  // console.log('tipePPN', tipePPN)
  // console.log('noSO', noSO)
  // console.log('noBatch', noBatch)
  // console.log('flagKembali', flagKembali)
  // console.log('qntReject', qntReject)
  // console.log('qntReject1', qntReject1)
  // console.log('qntReject2', qntReject2)
  // console.log('sat_2', sat_2)

}


function submitEditKoreksi () {
  let _token = $("#_token").val();
  let choice = "U"
  console.log(' koreksi submit edit')
  let nospr = koreksiDataEdit.NOBUKTI
  let nourut = koreksiDataEdit.NoUrut
  let tanggal = koreksiDataEdit.Tanggal
  let noprj = koreksiDataEdit.NoPRRJUAL
  let kodecustsupp = koreksiDataEdit.KODECUSTSUPP
  let nopolkend = ""
  let container = ""
  let nocontainer = ""
  let noseal = ""
  let catatan = ""
  let urut = koreksiDataEdit.URUT
  let urutrpj = 0
  let kodebrg = koreksiDataEdit.KODEBRG
  let inputQty = $("#koreksiEditInputQty").val()
  // console.log(Number(inputQty) , Number(koreksiDataEdit.QntOS) , Number(koreksiDataEdit.QNT))
  if (Number(inputQty) > Number(koreksiDataEdit.QntOS) + Number(koreksiDataEdit.QNT)) {
    alertify.warning("Qty melebihi Qty OS");
    return
  }
  if(Number(inputQty) < 0 ) {
    alertify.warning("Qty tidak boleh negatif");
    return
  }
  console.log('lolos')
  let inputQty1 = 0
  let inputQty2 = 0
  if (koreksiDataEdit.NOSAT == 1) {
    inputQty1 = inputQty
    inputQty2 = inputQty / koreksiDataEdit.ISI2
  } else if (koreksiDataEdit.NOSAT == 2) {
    inputQty1 = inputQty * koreksiDataEdit.ISI2
    inputQty2 = inputQty
  }
  console.log(koreksiDataEdit)
  console.log(koreksiDataEdit.NOSAT , koreksiDataEdit.ISI2)
  console.log(inputQty , inputQty1 , inputQty2)
  // return
  let sat_1 = koreksiDataEdit.SAT_1
  let nosat = koreksiDataEdit.NOSAT
  let isi = koreksiDataEdit.ISI
  let netw = 0
  let grossw = 0
  let isempty = 0
  let namabarang = koreksiDataEdit.NAMABRG
  let sopir = ""
  let flagmenu = 1
  let kodegdg = koreksiDataEdit.KodeGdg
  let flagtipe = 1
  let tipePPN = 0
  let noSO = 0
  let noBatch = 0
  let flagKembali = 0
  let qntReject = 0
  let qntReject1 = 0
  let qntReject2 = 0
  let sat_2 = koreksiDataEdit.SAT_2
  // let qtyOS = $("#koreksiEditQtyOS").val()

  // console.log(nospr,noprj,kodebrg,namabarang,inputQty,urut)
  // console.log('a')
  $.ajax({
    url: "{!! url('koreksiReturJual') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nospr,
      nourut,
      tanggal,
      noprj,
      kodecustsupp,
      nopolkend,
      container,
      nocontainer,
      noseal,
      catatan,
      urut,
      urutrpj,
      kodebrg,
      inputQty,
      inputQty1,
      inputQty2,
      sat_1,
      nosat,
      isi,
      netw,
      grossw,
      isempty,
      namabarang,
      sopir,
      flagmenu,
      kodegdg,
      flagtipe,
      tipePPN,
      noSO,
      noBatch,
      flagKembali,
      qntReject,
      qntReject1,
      qntReject2,
      sat_2
    },
    success: function(res) {
      console.log(res ,'!?! edit')
      alertify.success('Item telah diedit');
      loadAll()
      refreshKoreksi(nospr)
    }})

}

function submitDeleteKoreksi(index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + koreksiDataArray[index].NAMABRG + ' ?',
    function() {
      let _token = $("#_token").val();
      let choice = "D"
      console.log('koreksi submit delete')
      console.log(koreksiDataArray[index])
      let dataSPR = koreksiDataArray[index]
      let nospr = $("#input_koreksi_nospr").val()
      let tanggal = $("#input_koreksi_tanggal").val()
      let noprj = $("#input_koreksi_noprj").val()
      let nourut = dataSPR.NoUrut

      let kodecustsupp = 0
      let nopolkend = ""
      let container = ""
      let nocontainer = ""
      let noseal = ""
      let catatan = ""
      let urut = dataSPR.URUT
      let urutrpj = 0
      let kodebrg = dataSPR.KODEBRG
      let inputQty = 0
      let inputQty1 = 0
      let inputQty2 = 0
      let sat_1 = ''
      let nosat = 0
      let isi = 0
      let netw = 0
      let grossw = 0
      let isempty = 1
      let namabarang = ''
      let sopir = ""
      let flagmenu = 1
      let kodegdg = ''
      let flagtipe = 1
      let tipePPN = 0
      let noSO = 0
      let noBatch = 0
      let flagKembali = 0
      let qntReject = 0
      let qntReject1 = 0
      let qntReject2 = 0
      let sat_2 = ''

      $.ajax({
        url: "{!! url('koreksiReturJual') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          choice,
          nospr,
          nourut,
          tanggal,
          noprj,
          kodecustsupp,
          nopolkend,
          container,
          nocontainer,
          noseal,
          catatan,
          urut,
          urutrpj,
          kodebrg,
          inputQty,
          inputQty1,
          inputQty2,
          sat_1,
          nosat,
          isi,
          netw,
          grossw,
          isempty,
          namabarang,
          sopir,
          flagmenu,
          kodegdg,
          flagtipe,
          tipePPN,
          noSO,
          noBatch,
          flagKembali,
          qntReject,
          qntReject1,
          qntReject2,
          sat_2
        },
        success: function(res) {
          console.log(res ,'!?! delete')
          alertify.success('Item telah didelete');
          loadAll()
          refreshKoreksi(nospr)
        }
      })
    }
  ,function(){});

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

function changeSelectKoreksiAdd() {
  let indexBarang = document.getElementById("koreksiAddSelect").value;
  console.log(indexBarang)
  console.log(koreksiDataAddList[indexBarang])

  document.getElementById("koreksiAddKodeBrg").value = koreksiDataAddList[indexBarang].KODEBRG
  document.getElementById("koreksiAddNamaBrg").value = koreksiDataAddList[indexBarang].NAMABRG
  document.getElementById("koreksiAddQtyOS").value = koreksiDataAddList[indexBarang].QNTOS
  document.getElementById("koreksiAddQtyPO").value = koreksiDataAddList[indexBarang].QNT

}

function buttonBatalShowHide() {
  $('.showhide').hide();
}

function buttonKoreksiAdd() {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  $('.showhide').hide();
  // document.getElementById("koreksiAddSelect").value = ""
  document.getElementById("koreksiAddKodeBrg").value = ""
  document.getElementById("koreksiAddNamaBrg").value = ""
  document.getElementById("koreksiAddQtyOS").value = 0.00
  document.getElementById("koreksiAddQtyPO").value = 0.00
  $('#formKoreksiAdd').show();
}

function buttonKoreksiEdit(index) {
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  $('.showhide').hide();

  console.log(koreksiDataArray[index])
  koreksiDataEdit = koreksiDataArray[index]
  document.getElementById("koreksiEditKodeBrg").value = koreksiDataArray[index].KODEBRG
  document.getElementById("koreksiEditNamaBrg").value = koreksiDataArray[index].NAMABRG
  document.getElementById("koreksiEditQtyOS").value = Number(koreksiDataArray[index].QntOS) ? koreksiDataArray[index].QntOS : '0.00'
  document.getElementById("koreksiEditInputQty").value = koreksiDataArray[index].QNT
  $('#formKoreksiEdit').show();
  document.getElementById('formKoreksiEdit').scrollIntoView();
}

function buttonDetailReturGudang (NOBUKTI) {
  // formDetailKoreksi
  console.log(NOBUKTI)

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailreturgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)

      koreksiDataArray = res
      dataSPR = res[0]

      let rowTable = ""
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td class="text-left">${item.KODEBRG}</td>
        <td class="text-left">${item.NAMABRG}</td>
        <td>${item.QNT}</td>
        <td>${Number(item.QntOS) ? item.QntOS : '0.00'}</td>
        <td class="text-left">${item.Satuan}</td>

        </tr>`
      });
      let date = new Date(res[0].Tanggal);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#detailKoreksi_tanggal').val(date1)
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable
      res.forEach((item, i) => {
       // JsBarcode(`#barcodeDetailKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
      });
      document.getElementById("detailKoreksi_nospr").value = res[0].NOBUKTI
      document.getElementById("detailKoreksi_noprj").value = res[0].NoPRRJUAL
      document.getElementById("detailKoreksi_kodecust").value = res[0].KODECUSTSUPP
      document.getElementById("detailKoreksi_namacust").value = res[0].NAMACUSTSUPP
    }})


  $("#formDetailKoreksi").modal('toggle')
}

function submitPrint () {
  let printContent1 = ""
  let jumlahPrint = $(`#input_print_jumlahprint`).val();
  let z = 0
  koreksiDataArray.forEach((item, i) => {
    if (document.getElementById(`printChecklist${i}`).checked) {
      // qrcodeprint
      let date = new Date(item.Tanggal);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;
      let printContent2 = document.getElementById(`tempqrcodeprintkodebrg${i}`).innerHTML;

      // item.NOBUKTI item.KODEBRG ${item.NAMABRG} @ ${item.Satuan} ${date1} / ${item.QNT} ${item.Satuan}
      printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`
      for (let i=0; i < jumlahPrint ; i++ ) {

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
        printContent1 += `<h6>${date1} / ${item.QNT} ${item.Satuan}</h6>`
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

  // setTimeout(function () { w.print(); }, 500);
  // console.log('bb')
  //  w.onfocus = function () { setTimeout(function () { w.close(); }, 500); }
  //  console.log('cc')
  // setTimeout(function () { w.close(); }, 100)


  // console.log(idBarcode , idJumlah)
  // let printContent = document.getElementById(`${idBarcode}`).innerHTML;
  // let jumlahPrint = $(`#${idJumlah}`).val();
  // console.log(jumlahPrint)
  // let printContent1 = ""
  // for (let i=0; i < jumlahPrint ; i++ ) {
  //   printContent1 += `<div class="row">`
  //   printContent1 += printContent
  //   printContent1 += `</div>`
  // }
  // document.getElementById("printContainer").innerHTML = printContent1
  // w=window.open(' ');
  //
  // w.document.write($(`#printContainer`).html());
  // w.print();
  // w.close();
}

function buttonPrintPenerimaan (NOBUKTI) {

  let akses = $("#akses_iscetak").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log(NOBUKTI)

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailreturgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)

      koreksiDataArray = res
      dataSPR = res[0]

      let rowTable = ""
      let rowTableTemp = ""
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td>
        <div class="form-check text-center">
            <input id="printChecklist${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            </div></td>
        <td class="text-left">${item.KODEBRG}</td>
        <td class="text-left">${item.NAMABRG}</td>
        <td>${item.QNT}</td>
        <td>${Number(item.QntOS) ? item.QntOS : '0.00'}</td>
        <td class="text-left">${item.Satuan}</td>

        </tr>`

        rowTableTemp += `
        <div id="tempqrcodeprint${i}" style="width: 10px; height: 10px"></div>
        <div id="tempqrcodeprintkodebrg${i}" style="width: 10px; height: 10px"></div>
        `
      });
      let date = new Date(res[0].Tanggal);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_print_tanggal').val(date1)
      document.getElementById("printTableData").innerHTML = rowTable
      document.getElementById("tempPrintContainer").innerHTML = rowTableTemp
      res.forEach((item, i) => {
       // JsBarcode(`#barcodeDetailKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
       new QRCode(document.getElementById(`tempqrcodeprint${i}`), {text: `${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}` , width: 90, height: 90});
       new QRCode(document.getElementById(`tempqrcodeprintkodebrg${i}`), {text: `${item.KODEBRG}` , width: 90, height: 90});
      });
      document.getElementById("input_print_nospr").value = res[0].NOBUKTI
      document.getElementById("input_print_noprj").value = res[0].NoPRRJUAL
      document.getElementById("input_print_kodecust").value = res[0].KODECUSTSUPP
      document.getElementById("input_print_namacust").value = res[0].NAMACUSTSUPP
    }})


  $("#formPrint").modal('toggle')
}



function buttonKoreksi (NOBUKTI) {
  $('.showhide').hide();
  let _token = $("#_token").val();
  console.log(NOBUKTI)
  $.ajax({
    url: "{!! url('detailreturgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)

      koreksiDataArray = res
      dataSPR = res[0]


    }})
    if(!koreksiDataArray.length) {
      alertify.warning('Data tidak ditemukkan silahkan refresh browser')
      return
    }

    if (Number(koreksiDataArray[0].IsOtorisasi1) ) {
      alertify.warning('Data sudah di otorisasi')
      return
    }

    let checkLockPeriode = [{
      BULAN: 0,
      TAHUN: 0
    }]
    let checkDateLockPeriode = new Date(koreksiDataArray[0].Tanggal)
    // console.log('2')
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

    console.log(koreksiDataArray)
    let rowTabel = ""
    koreksiDataArray.forEach((item, i) => {
      console.log(item.KODEBRG)
      rowTabel += `<tr>
      <td class="text-left">${item.KODEBRG}</td>
      <td class="text-left">${item.NAMABRG}</td>
      <td>${item.QNT}</td>
      <td>${Number(item.QntOS) ? item.QntOS : '0.00'}</td>
      <td class="text-left">${item.Satuan}</td>


      <td class="text-center">

      <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="submitDeleteKoreksi(${i})" ><i class="bi bi-trash"></i></button></td>
      </tr>`
    });
    let date = new Date(koreksiDataArray[0].Tanggal);
    var day = ("0" + date.getDate()).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    document.getElementById("koreksiTableData").innerHTML = rowTabel

    koreksiDataArray.forEach((item, i) => {
      //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
    });


    document.getElementById("input_koreksi_nospr").value = koreksiDataArray[0].NOBUKTI
    document.getElementById("input_koreksi_noprj").value = koreksiDataArray[0].NoPRRJUAL


    // koreksi add list
    $.ajax({
      url: "{!! url('returjualkoreksiaddlist') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nospr: koreksiDataArray[0].NOBUKTI,
        noprj: koreksiDataArray[0].NoPRRJUAL
      },
      success: function(res) {
        console.log(res , "addlistkoreksi !!!!!!")
        koreksiDataAddList = res
      }})
    document.getElementById("koreksiAddSelect").innerHTML = ""
    let tempKoreksiAddList = ""
    if (koreksiDataAddList.length) {
      tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
      koreksiDataAddList.forEach((item, i) => {
        tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
      });

    } else {
      tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
    }
    document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList


    document.getElementById("input_koreksi_kodecust").value = koreksiDataArray[0].KODECUSTSUPP
    document.getElementById("input_koreksi_namacust").value = koreksiDataArray[0].NAMACUSTSUPP
    $('#input_koreksi_tanggal').val(date1)


    $("#formKoreksi").modal('toggle')
}

function refreshKoreksi (NOBUKTI) {
  $('.showhide').hide();
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailreturgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)

      koreksiDataArray = res
      dataSPR = res[0]


    }})
    console.log(koreksiDataArray)
    let rowTabel = ""
    koreksiDataArray.forEach((item, i) => {
      console.log(item.KODEBRG)
      rowTabel += `<tr>
      <td class="text-left">${item.KODEBRG}</td>
      <td class="text-left">${item.NAMABRG}</td>
      <td>${item.QNT}</td>
      <td>${item.QntOS}</td>
      <td class="text-left">${item.Satuan}</td>

      <td class="text-center">
      <button class="btn btn-success btn-sm" type="button" onclick="printBarcode('containerbarcodeKoreksi${i}', 'input_koreksi_qntPrint${i}')"><i class="bi bi-printer"></i></button>
      <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="submitDeleteKoreksi(${i})" ><i class="bi bi-trash"></i></button></td>
      </tr>`
    });
    let date = new Date(koreksiDataArray[0].Tanggal);
    var day = ("0" + date.getDate()).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    document.getElementById("koreksiTableData").innerHTML = rowTabel

    koreksiDataArray.forEach((item, i) => {
      //JsBarcode(`#barcodeKoreksi${i}`, item.NOBUKTI + item.KODEBRG ,{width: 1, height: 25,});
    });


    document.getElementById("input_koreksi_nospr").value = koreksiDataArray[0].NOBUKTI
    document.getElementById("input_koreksi_noprj").value = koreksiDataArray[0].NoPRRJUAL


    // koreksi add list
    $.ajax({
      url: "{!! url('returjualkoreksiaddlist') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nospr: koreksiDataArray[0].NOBUKTI,
        noprj: koreksiDataArray[0].NoPRRJUAL
      },
      success: function(res) {
        console.log(res , "addlistkoreksi !!!!!!")
        koreksiDataAddList = res
      }})
    document.getElementById("koreksiAddSelect").innerHTML = ""
    let tempKoreksiAddList = ""
    if (koreksiDataAddList.length) {
      tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
      koreksiDataAddList.forEach((item, i) => {
        tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
      });

    } else {
      tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
    }
    document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList


    document.getElementById("input_koreksi_kodecust").value = koreksiDataArray[0].KODECUSTSUPP
    document.getElementById("input_koreksi_namacust").value = koreksiDataArray[0].NAMACUSTSUPP
    $('#input_koreksi_tanggal').val(date1)


    // $("#formKoreksi").modal('toggle')
}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  // formDetail
  let _token = $("#_token").val();
  console.log(NOBUKTI)
  $.ajax({
    url: "{!! url('detailoutstandingreturjual') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)

      document.getElementById("detail_noprj").value = res[0].NOBUKTI
      document.getElementById("detail_kodecust").value = res[0].KodeCustSupp
      document.getElementById("detail_namacust").value = res[0].NAMACUSTSUPP
      let rowTable = ""
      res.forEach((item, i) => {
        rowTable += `<tr>
        <td class="text-left">${item.KODEBRG}</td>
        <td class="text-left">${item.NAMABRG}</td>
        <td>${item.QNT}</td>
        <td>${item.QNTOS}</td>
        <td class="text-left">${item.SATUAN}</td>


        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable
      res.forEach((item, i) => {
        //JsBarcode(`#barcodeDetail${i}`, item.KODEBRG ,{width: 2, height: 25,});
      });
    }})




  $("#formDetail").modal('toggle')
}

function setNewNoBukti () {

  $.ajax({
    url: "{!! url('getNoBuktiReturJual') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res, 'NoBUkti')

      document.getElementById("input_add_nospr").value = res[0].Nobukti
      document.getElementById("input_add_noUrut").value = res[0].Nourut
    }
  })


}

function buttonAdd(NOBUKTI) {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  setNewNoBukti()
  let _token = $("#_token").val();
  console.log(NOBUKTI)
  $.ajax({
    url: "{!! url('detailoutstandingreturjual') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: NOBUKTI
    },
    success: function(res) {
      console.log(res)

      addDataArray = res
      // console.log(res[1])
      // console.log(res[0] ,'i0')
      document.getElementById("input_add_noprj").value = addDataArray[0].NOBUKTI
      document.getElementById("input_add_kodecust").value = addDataArray[0].KodeCustSupp
      document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
    }})

    let rowTabel = ""
    addDataArray.forEach((addData, i) => {
      rowTabel += `<tr><td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
      <td class="text-left">${addData.KODEBRG}</td>
      <td class="text-left">${addData.NAMABRG}</td>
      <td><input id="input_add_qnt${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${addData.QNT} disabled></td>
      <td><input id="input_add_qntOS${i}" style="width: 100px;" class="text-right" type="number" min=0 value=${addData.QNTOS} disabled></td>
      <td class="text-left">${addData.SATUAN}</td>
      <td><input onchange="onChangePrint(${i})" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td>


      </tr>`
    })
    document.getElementById("addTableData").innerHTML = rowTabel

    addDataArray.forEach((addData, i) => {
     // JsBarcode(`#barcodeAdd${i}`, addData.KODEBRG ,{width: 2, height: 25,});
    });

    document.getElementById("input_add_noprj").value = addDataArray[0].NOBUKTI
    document.getElementById("input_add_kodecust").value = addDataArray[0].KodeCustSupp
    document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
    $("#form").modal('toggle')


}

function submitAdd() {
  console.log('Submit Add')
  let _token = $("#_token").val();
  let tempData = []

  addDataArray.forEach((addData, i) => {
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
  console.log('1')
  tempData.forEach((item, i) => {
    console.log(Number(item.inputQntTerima) ,Number(item.QNTOS) )
    if(Number(item.inputQntTerima) > Number(item.QNTOS)) {
      flag = true
    }
    if (Number(item.inputQntTerima) < 0 ) {
      flag = true
    }
  });
  if (flag) {
    alertify.warning("QtyTerima lebih besar dari QNTOS ataupun negatif");
    return
  }
  console.log('2')

  let inputDate = $("#input_add_tanggal").val();


  let noprj = $(`#input_add_noprj`).val();
  let nospr = $(`#input_add_nospr`).val();
  let nourut =  $(`#input_add_noUrut`).val();

  let checkDate = new Date(inputDate)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }


  console.log(noprj, nospr, nourut, inputDate)
  console.log(tempData)
  $.ajax({
    url: "{!! url('addReturJual') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noprj,
      nospr,
      nourut,
      tempData,
      inputDate
    },
    success: function(res) {
      console.log(res ,'!')
      if (res == 1) {
        alertify.success('SPR telah ditambah');
        loadAll()
        $("#form").modal('toggle')

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



</script>


{{-- } - karakter sisa di luar <script> (dulu tampil sebagai teks di halaman) --}}


@endsection
