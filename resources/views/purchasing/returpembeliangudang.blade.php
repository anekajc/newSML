@extends('purchasing.newmaster')
@section('buttons')

@endsection

{{-- tampilan search bar 1 --}}
  @section('css')
  <style>
  #tabel_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;  
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }
  </style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
  <style>
  #tabelRetur_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabelRetur_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabelRetur_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabelRetur_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 2 --}}

@section('content')

  
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div class="container-fluid" style="margin-top:-80px;">
<h1>Retur Pembelian Gudang</h1>
{{-- <button onclick="loadAll()">tes load all</button>  --}}
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
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
          <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
             aria-controls="nav-home" aria-selected="true"
             style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
            Outstanding Retur Beli
          </a>
          <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
             aria-controls="nav-profile" aria-selected="false"
             style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
            Retur Beli
          </a>
        </div>
      </div>
    </div>
    <div class="card-body" style="margin-top:-20px;">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
           <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">

                  <table id="tabel" class="table table-bordered table-striped">
                    <thead class="text-center bg-primary text-white">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Out</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Supplier</th>
                        </tr>
                      </thead>

                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $outstandingArray[$i][0]->KODESUPP }}')" ><i class="bi bi-info-lg"></i></button>
                            <!-- <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('{{ $outstandingArray[$i][0]->KODESUPP }}')"><i class="bi bi-bag-plus-fill"></i></button> -->
                          </td>
                          <td>{{ $outstandingArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->TANGGAL)) !!}</td>
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

                    <table id="tabelRetur" class="table table-bordered table-striped"  >
                      <thead class="text-center bg-primary text-white">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Supplier</th>

                        </tr>
                      </thead>


                      <tbody id="tabelRetur_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i]->NoBukti }}')" ><i class="bi bi-info-lg"></i></button>
                            <button style="" class="btn btn-primary btn-sm" type="button" onclick="submitPrint('{{ $penerimaanArray[$i]->NoBukti }}')" ><i class="bi bi-printer"></i></button>
                            <!-- <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i]->NoBukti }}')"><i class="bi bi-pen"></i></button> -->
                          </td>
                          <td>{{ $penerimaanArray[$i]->NoBukti }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i]->Tanggal)) !!}</td>
                          <td>{{ $penerimaanArray[$i]->NamaCustSupp}}</td>

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
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No Bukti Out</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_noout" placeholder="No Out" disabled>
            </div>

            <div class="col-2">
              <label>No RPB</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_norpb" placeholder="No PBG" disabled>
            </div>

            <div class="col-1">
              <label>Gudang</label>
            </div>
            <div class="col-3">
              <input type="text" class="form-control" id="input_add_gdg" placeholder="Gdg Asal" required disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
            </div>

            <div class="col-2">
              <label>Nama Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_add_namacust" placeholder="cust supp" disabled>
            </div>
          </div>
        </div>
      </div>


        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">No PRB</th>
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
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" style="
            height: 30px;  
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal" >Batal</button>

        <button type="button" class="btn btn-primary" style="
            height: 30px;  
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal detail -->
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>Gudang</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_detail_gdg" placeholder="Gdg Asal" required disabled>
            </div>

            <div class="col-2">
              <label>No Out</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_detail_noout" placeholder="No PBG" disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Nama Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_detail_namacust" placeholder="Nama Supp" disabled>
            </div>

            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>
          </div>
        </div>
      </div>


        <div class="container-fluid" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th scope="col">No RPB</th>
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


<!-- start modal detail koreksi -->
<div class="modal fade" id="formKoreksiDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No RPB</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_norpb" placeholder="No RPB" disabled>
            </div>

            <div class="col-2">
              <label>No OUT</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_noout" placeholder="No PBG" disabled>
            </div>

            <div class="col-2">
              <label>Gdg</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_koreksidetail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>

            <div class="col-2">
              <label>Cust Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksidetail_namacust" placeholder="Nama Cust" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 text-right">
              <!-- <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary">Add Item</button> -->
            </div>
          </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">
            <table id="koreksiDetailTable" class="table table-bordered table-striped">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Kode Brg</th>
                  <th scope="col">Nama Brg</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Qty OS</th>
                  <th scope="col">Satuan</th>
                </tr>
              </thead>
              <tbody id="koreksiDetailTableData" class="text-right">
                <tr>
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

      <!-- <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No RPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_norpb" placeholder="No RPB" disabled>
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
            <! -- <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary"  >Add Item</button> - ->
        </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="koreksiDetailTable" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
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
  </div> -->
</div>
</div>
</div>
<!-- End modal detail koreksi-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid">
          <!-- Row 1 -->
          <div class="row">
            <div class="col-2">
              <label>No RPB</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_norpb" placeholder="No RPB" disabled>
            </div>

            <div class="col-2">
              <label>No OUT</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_noout" placeholder="No out" disabled>
            </div>

            <div class="col-2">
              <label>Gdg</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_gdg" placeholder="Kode Cust" required disabled>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="row mt-2">
            <div class="col-2">
              <label>Tanggal</label>
            </div>
            <div class="col-2">
              <input type="date" class="form-control" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
            </div>

            <div class="col-2">
              <label>Cust Supp</label>
            </div>
            <div class="col-2">
              <input type="text" class="form-control" id="input_koreksi_namacust" placeholder="Nama Cust" disabled>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()">Add Item</button>
            </div>
          </div>

          <div class="container-fluid">
            <!-- koreksi add -->
            <div id="formKoreksiAdd" class="container-fluid showhide">

                <div class="row">
                  <div class="col-12">
                    <h4>Add Item</h4>
                  </div>
                </div>

                <div class="row">
                  <div class="col-2">
                    <label>Pilih Barang</label>
                  </div>
                  <div class="col-4">
                    <div class="form-group">
                      <select onchange="changeSelectKoreksiAdd()" id="koreksiAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                      </select>
                    </div>
                  </div>
                </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Kode Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddKodeBrg" type="text" class="form-control" disabled>
                </div>
                
                <div class="col-2">
                  <div class="form-group">
                    <label>Nama Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddNamaBrg" type="text" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddInputQty" type="number" value="0.00" class="form-control">
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label>Satuan</label>
                  </div>
                </div>
                <div class="col-4">
                  <input type="text" id="koreksiAddSatuan" value="PCS" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty OS</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddQtyOS" type="number" value="0.00" class="form-control" disabled>
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty PO</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiAddQtyPO" type="number" value="0.00" class="form-control" disabled>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()">Batal</button>
                  <button type="button" onclick="submitAddKoreksi()" class="btn btn-primary">Add Item</button>
                </div>
              </div>
            </div>

            <!-- koreksi edit -->
            <div id="formKoreksiEdit" class="container-fluid showhide">

              <div class="row">
                <div class="col-12">
                  <h4>Edit Item</h4>
                </div>
              </div>

              <div class="row">
                <div class="col-2">
                  <div class="form-group">
                    <label>Kode Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditKodeBrg" type="text" class="form-control" disabled>
                </div>
                
                <div class="col-2">
                  <div class="form-group">
                    <label>Nama Barang</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditNamaBrg" type="text" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditInputQty" onblur='cekQntEdit()' type="number" value="0.00" class="form-control">
                </div>
                <div class="col-2">
                  <div class="form-group">
                    <label>Satuan</label>
                  </div>
                </div>
                <div class="col-4">
                  <input type="text" id="koreksiEditSatuan" value="PCS" class="form-control" disabled>
                </div>
              </div>

              <div class="row" style='margin-top:-8px;'>
                <div class="col-2">
                  <div class="form-group">
                    <label>Qty OS</label>
                  </div>
                </div>
                <div class="col-4">
                  <input id="koreksiEditQtyOS" type="number" value="0.00" class="form-control" disabled>
                </div>
              </div>

              <div class="row mt-2">
                <div class="col-md-12 text-right">
                  <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()">Batal</button>
                  <button type="button" onclick="submitEditKoreksi()" class="btn btn-primary">Edit Item</button>
                </div>
              </div>
            </div>
            <!-- end -->
          </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">
            <table id="koreksiTable" class="table table-bordered table-striped">
              <thead class="text-center text-white bg-primary">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">No PRB</th>
                  <th scope="col">Kode Brg</th>
                  <th scope="col">Nama Brg</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Qty OS</th>
                  <th scope="col">Satuan</th>
                </tr>
              </thead>
              <tbody id="koreksiTableData" class="text-right">
                <tr>
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


      <!-- <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No RPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_norpb" placeholder="No RPB" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No OUT</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_noout" placeholder="No out" disabled>
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
          <!- - koreksi add - ->
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

          <!- - koreksi edit - ->

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

          <!- - end  - ->
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
  </div> -->
</div>
</div>
</div>
<!-- End modal koreksi-->




@endsection

@section('js')
<script src="{!! URL::asset('public/js/ajc-func-core.js') !!}"></script>
<script type="text/javascript">

let dataRefreshOutstanding = []
let dataRefreshPenerimaan = []
let xkodesupp=''
let nopbl=''
let urutpbl=0

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
        {  "className": "text-center", "targets": [0] , "orderable" : false },
      ]});
      // $("#formKoreksi").modal('toggle')

});

function buttonBatalShowHide() {
  $('.showhide').hide();
}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('getnobuktireturbeli') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res, 'NoBUkti')
      console.log(res[0].Nobukti , res[0].Nourut)
      console.log('===============')
      document.getElementById("input_add_norpb").value = res[0].Nobukti
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
    url: "{!! url('detailoutstandingreturbeli') !!}",
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
  document.getElementById("input_add_gdg").value = addDataArray[0].Kodegdg
  xkodesupp = addDataArray[0].KODESUPP
  nopbl = addDataArray[0].noPBL
  urutpbl = addDataArray[0].UrutPBL

  // document.getElementById("input_add_namacust").value = addDataArray[0].NAMACUSTSUPP
  //
  let rowTable = ""
  addDataArray.forEach((item, i) => {
    let qnt = 0.00
    let qntos = 0.00
    if(item.Qnt2) {
      qnt = parseFloat(item.Qnt1).toFixed(2)
    }
    if(item.QntOUt) {
      qntos = parseFloat(item.QntOUt).toFixed(2)
    }
    rowTable += `<tr class="text-left">
    <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
    <td>${item.NOBUKTI}</td>
    <td>${item.Kodebrg}</td>
    <td>${item.namaBrg}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-right">${qntos}</td>
    <td class="text-center" >${item.SATUAN}</td>
    <td class="text-center"><input onblur="cekQnt(${qntos}, ${i})" id="input_add_qntTerima${i}" style="width: 100px;" class="text-right" type="number" min=0 value=0.00></td>
    </tr>`
  });
  document.getElementById("addTableData").innerHTML = rowTable

  $("#form").modal('toggle')


}

  let stateQnt = 0
function cekQnt (qntos, urut){


  let qntTerima = document.getElementById('input_add_qntTerima'+urut).value

  if ((parseFloat(qntos)+1) < parseFloat(qntTerima)){
    stateQnt = 1
  } else if ((parseFloat(qntos)+1) > parseFloat(qntTerima)){
    stateQnt = 0
  } 

  if (stateQnt == 1 ){
    alertify.warning('Qnt Kirim tidak boleh melebihi Qnt OS')
    document.getElementById('input_add_qntTerima'+urut).value = 0,00
  }

}

function cekQntEdit (){

  let stateQnt = 0

  let qntTerima = document.getElementById('koreksiEditInputQty').value
  let qntos = document.getElementById('koreksiEditQtyOS').value

  if (parseFloat(qntos) < parseFloat(qntTerima)){
    stateQnt = 1
  }

  if (stateQnt == 1 ){
    alertify.warning('Qnt Kirim tidak boleh melebihi Qnt OS')
    document.getElementById('koreksiEditInputQty').value = qntEditTemp
  }

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

function refreshKoreksi (NOBUKTI) {
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanreturbeli') !!}",
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
  document.getElementById("input_koreksi_norpb").value = koreksiPenerimaanArray[0].NOBUKTI
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  document.getElementById("input_koreksi_namacust").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

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
    <td class="text-center">
      <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    <td>${item.NOBUKTI}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-right">${qntos}</td>
    <td class="text-center">${item.Satuan}</td>
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable



  console.log('===========____________++++++++++++++')
  console.log(koreksiPenerimaanArray[0])
  $.ajax({
    url: "{!! url('returbelikoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      norpb: koreksiPenerimaanArray[0].NOBUKTI,
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


}

function buttonKoreksi (NOBUKTI) {
  console.log('button koreksi')
  console.log(NOBUKTI)

  $('.showhide').hide();
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanreturbeli') !!}",
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
  document.getElementById("input_koreksi_norpb").value = koreksiPenerimaanArray[0].NOBUKTI
  document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].Namagdg
  document.getElementById("input_koreksi_namacust").value = koreksiPenerimaanArray[0].NAMACUSTSUPP

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
    <td class="text-center">

    <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
    </td>
    <td>${item.NOBUKTI}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NAMABRG}</td>
    <td class="text-right">${qnt}</td>
    <td class="text-right">${qntos}</td>
    <td class="text-center">${item.Satuan}</td> 
    </tr>`
  });
  document.getElementById("koreksiTableData").innerHTML = rowTable



  console.log('===========____________++++++++++++++')
  console.log(koreksiPenerimaanArray[0])
  $.ajax({
    url: "{!! url('returbelikoreksiaddlist') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      norpb: koreksiPenerimaanArray[0].NOBUKTI,
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



function buttonDetailKoreksi (NOBUKTI) {
  console.log('button detail koreksi')
  console.log(NOBUKTI)

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('detailpenerimaanreturbeli') !!}",
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
      console.log(res.NOBUKTI,'90909')
      // if (res[0].NooutBRg) {
      //   nooutbrg = res[0].NooutBRg
      // }

      document.getElementById("input_koreksidetail_noout").value = nooutbrg
      document.getElementById("input_koreksidetail_norpb").value = res[0].NOBUKTI
      document.getElementById("input_koreksidetail_gdg").value = res[0].Namagdg
      document.getElementById("input_koreksidetail_namacust").value = res[0].NAMACUSTSUPP
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
        <td class="text-center">${item.Satuan}</td>

        </tr>`
      });
      document.getElementById("koreksiDetailTableData").innerHTML = rowTable



    }
  })

  $("#formKoreksiDetail").modal('toggle')

}


let qntEditTemp = 0
function buttonKoreksiEdit (index) {
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
  document.getElementById("koreksiEditQtyOS").value = parseFloat(koreksiDataEdit.QntOS) + parseFloat(qnt)
  document.getElementById("koreksiEditInputQty").value = qnt
  qntEditTemp = qnt
  document.getElementById("koreksiEditSatuan").value = koreksiDataEdit.Satuan
  $('#formKoreksiEdit').show();
  document.getElementById('formKoreksiEdit').scrollIntoView();
}

function buttonDetail (NOBUKTI) {
  console.log(NOBUKTI)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailoutstandingreturbeli') !!}",
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
      document.getElementById("input_detail_namacust").value = res[0].NAMACUSTSUPP
      document.getElementById("input_detail_noout").value = res[0].NOBUKTI
      document.getElementById("input_detail_gdg").value = nullToStrip(res[0].Kodegdg)

      let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.Qnt2) {
          qnt = parseFloat(item.Qnt2).toFixed(2)
        }
        if(item.QntOUt) {
          qntos = parseFloat(item.QntOUt).toFixed(2)
        }
        rowTable += `<tr class="text-left">
        <td>${item.NOBUKTI}</td>
        <td>${item.Kodebrg}</td>
        <td>${item.namaBrg}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntos}</td>
        <td class="text-center">${item.SATUAN}</td>
        </tr>`
      });
      document.getElementById("detailTableData").innerHTML = rowTable

    }
  })



  $("#formDetail").modal('toggle')


}


function submitAdd () {

  addDataArray.forEach((item,i) =>{
  let qntInputTemp = document.getElementById('input_add_qntTerima' + i);
  if (qntInputTemp) {
    let value = qntInputTemp.value.trim();
    console.log(value)
    // If any input is empty or 0, set state to 1
    if (value === '' || value === '0' || parseFloat(value) === 0) {
      stateQnt = 1;
      alertify.warning('Tidak bisa submit, data kosong');
      return; // Stop checking after first empty found
    }
  }
});


  // Check result
  if (stateQnt === 1) {
    // Cannot submit
    return false;
  }

  console.log('Submit Add')
  let _token = $("#_token").val();
  let tempData = []

  console.log('TES ==========')
  // return

  addDataArray.forEach((item, i) => {
    if (document.getElementById(`add_checkbox${i}`).checked) {
      addDataArray[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
      tempData.push(addDataArray[i])
    }
  });

        console.log('ne=====')

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
  let norpb = $(`#input_add_norpb`).val();
  let nourut =  $(`#input_add_noUrut`).val();
  let NOSAT = 1;
  let zkodesupp=xkodesupp;
  let znopbl=nopbl;
  let zurutpbl=urutpbl;

  console.log('noout', zkodesupp)
  console.log('noout', noout)
  console.log('norpb', norpb)
  console.log('nourut', nourut)
  console.log('inputDate', inputDate)
  console.log(tempData,'ttttttt')

  let checkDate = new Date(inputDate)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  $.ajax({
    url: "{!! url('addreturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      norpb,
      noout,
      nourut,
      tempData,
      inputDate,
      NOSAT,
      zkodesupp,
      nopbl,
      urutpbl,

    },
    success: function(res) {
      console.log(res ,'!')

      if (res ==1) {

        $("#form").modal('toggle')
        alertify.success('RPB telah ditambah');
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



function buttonKoreksiDelete(index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let dataRPB = koreksiPenerimaanArray[index]
  console.log(dataRPB)

    let _token = $("#_token").val();

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataRPB.KODEBRG + dataRPB.NAMABRG + ' ?',
      function() {
        console.log('yes')
        let choice = "D"
        let norpb = dataRPB.NOBUKTI
        let nourut = dataRPB.NOURUT
        let inputDate = $("#input_koreksi_tanggal").val()
        let kodesupp = dataRPB.KODESUPP
        let kodegdg = dataRPB.Kodegdg
        let noout = dataRPB.NooutBRg
        let keterangan = ""
        let faktursupp = ""
        let urut = dataRPB.URUT
        let kodebrg = dataRPB.KODEBRG
        let urutout = dataRPB.UrutOutBRg
        let nosat = dataRPB.NOSAT
        let satuan = dataRPB.Satuan
        let isi = dataRPB.ISI
        let flagtipe = 0
        let nobatch = ""
        let qntTerima = 0
        let qntTerima1 = 0
        let qntTerima2 = 0
        let nolpb = 0


          console.log("choice",choice)
          console.log("norpb",norpb)
          console.log("nourut",nourut)
          console.log("inputDate",inputDate)
          console.log("kodesupp",kodesupp)
          console.log("kodegdg",kodegdg)
          console.log("noout",noout)
          console.log("keterangan",keterangan)
          console.log("faktursupp",faktursupp)
          console.log("urut",urut)
          console.log("kodebrg",kodebrg)
          console.log("urutout",urutout)
          console.log("qntTerima", qntTerima)
          console.log("nosat",nosat)
          console.log("satuan",satuan)
          console.log("isi",isi)
          console.log("qntTerima1", qntTerima1)
          console.log("qntTerima2", qntTerima2)
          console.log("flagtipe",flagtipe)
          console.log("nobatch",nobatch)



        $.ajax({
          url: "{!! url('koreksireturbeli') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            norpb,
            nourut,
            inputDate,
            kodesupp,
            kodegdg,
            noout,
            keterangan,
            faktursupp,
            urut,
            kodebrg,
            urutout,
            qntTerima,
            nosat,
            satuan,
            isi,
            qntTerima1,
            qntTerima2,
            flagtipe,
            nobatch,
            nolpb
          },
          success: function(res) {
            console.log(res ,'succes delete koreksi')
            refreshKoreksi(norpb)
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

  let dataRPB = koreksiDataEdit


  let choice = "I"
  let norpb = dataRPB.NOBUKTI
  let nourut = dataRPB.NOURUT
  let inputDate = $("#input_koreksi_tanggal").val()
  let kodesupp = dataRPB.KODESUPP
  let kodegdg = dataRPB.Kodegdg
  let noout = dataOut.NOBUKTI
  let keterangan = ""
  let faktursupp = ""
  let urut = 0
  let kodebrg = dataOut.KODEBRG
  let urutout = dataOut.Urut
  let nosat = dataOut.NOSAT
  let satuan = dataOut.Satuan
  let isi = dataOut.ISI
  let flagtipe = 0
  let nobatch = ""
  let nolpb = dataOut.UrutLPB


  console.log("choice",choice)
  console.log("norpb",norpb)
  console.log("nourut",nourut)
  console.log("inputDate",inputDate)
  console.log("kodesupp",kodesupp)
  console.log("kodegdg",kodegdg)
  console.log("noout",noout)
  console.log("keterangan",keterangan)
  console.log("faktursupp",faktursupp)
  console.log("urut",urut)
  console.log("kodebrg",kodebrg)
  console.log("urutout",urutout)
  console.log("qntTerima", qntTerima)
  console.log("nosat",nosat)
  console.log("satuan",satuan)
  console.log("isi",isi)
  console.log("qntTerima1", qntTerima1)
  console.log("qntTerima2", qntTerima2)
  console.log("flagtipe",flagtipe)
  console.log("nobatch",nobatch)

  $.ajax({
    url: "{!! url('koreksireturbeli') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      norpb,
      nourut,
      inputDate,
      kodesupp,
      kodegdg,
      noout,
      keterangan,
      faktursupp,
      urut,
      kodebrg,
      urutout,
      qntTerima,
      nosat,
      satuan,
      isi,
      qntTerima1,
      qntTerima2,
      flagtipe,
      nobatch,
      nolpb
    },
    success: function(res) {
      console.log(res ,'succes add koreksi')
      refreshKoreksi(norpb)
      alertify.success('Item telah ditambah');
      loadAll()
    }
  })
}



function submitEditKoreksi () {
  // console.log('submit edit koreksi')
  // console.log(koreksiDataEdit)
  let dataRPB = koreksiDataEdit

  let qntTerimaTemp = document.getElementById('koreksiEditInputQty').value

  if (qntTerimaTemp == 0){
    alertify.warning('QNT tidak boleh kosong')
    return;
  }

  let _token = $("#_token").val();

  let qntTerima = $("#koreksiEditInputQty").val()
  // console.log(dataRPB)
  // console.log(qntTerima)
  if (Number(qntTerima) > Number(dataRPB.QntOS) + Number(dataRPB.QNT)) {
    alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
    return
  }
  if (Number(qntTerima) <= 0) {
    alertify.warning("Qty tidak bisa 0 atau negatif");
    return
  }
  console.log('lolos')
  // console.log(dataSSK.NOSAT)
  // console.log(dataSSK.ISI2)
  let qntTerima1 = 0
  let qntTerima2 = 0
  if (dataRPB.NOSAT == 1) {
    qntTerima1 = qntTerima
    qntTerima2 = qntTerima / dataRPB.ISI2
  } else if (dataRPB.NOSAT == 2) {
    qntTerima1 = qntTerima * dataRPB.ISI2
    qntTerima2 = qntTerima
  }

    // let choice = "U"
    // let nossk = dataRPB.NOBUKTI
    // let nourut = dataRPB.NOURUT
    // let inputDate = $("#input_koreksi_tanggal").val()
    // let note = ""
    // let urut = dataRPB.URUT
    // let kodebrg = dataRPB.KODEBRG
    // let gdgasal = dataRPB.Kodegdg
    // let gdgtujuan = "GSAMPLE"
    // let sat_1 = dataRPB.SAT1
    // let sat_2 = dataRPB.SAT2
    // // let qntTerima = //
    // // let qntTerima2 = //
    // let nosat = dataRPB.NOSAT
    // let isi = dataRPB.ISI
    // // user
    // let kodecustsupp = dataRPB.KODECUSTSUPP
    // let kodesls = dataRPB.KODESLS
    // let pbonus = 0
    // let maxol = 0
    // let nopr = ""
    // let urutpr = 0
    // let pkonsi = 0
    // let nooutbrg = ''
    // let urutoutbrg = 0

    let choice = "U"
    let norpb = dataRPB.NOBUKTI
    let nourut = dataRPB.NOURUT
    let inputDate = $("#input_koreksi_tanggal").val()
    let kodesupp = dataRPB.KODESUPP
    let kodegdg = dataRPB.Kodegdg
    let noout = dataRPB.NooutBRg
    let keterangan = ""
    let faktursupp = ""
    let urut = dataRPB.URUT
    let kodebrg = dataRPB.KODEBRG
    let urutout = dataRPB.UrutOutBRg
    let nosat = dataRPB.NOSAT
    let satuan = dataRPB.Satuan
    let isi = dataRPB.ISI
    let flagtipe = 0
    let nobatch = ""
    let nolpb = 0


      console.log("choice",choice)
      console.log("norpb",norpb)
      console.log("nourut",nourut)
      console.log("inputDate",inputDate)
      console.log("kodesupp",kodesupp)
      console.log("kodegdg",kodegdg)
      console.log("noout",noout)
      console.log("keterangan",keterangan)
      console.log("faktursupp",faktursupp)
      console.log("urut",urut)
      console.log("kodebrg",kodebrg)
      console.log("urutout",urutout)
      console.log("qntTerima", qntTerima)
      console.log("nosat",nosat)
      console.log("satuan",satuan)
      console.log("isi",isi)
      console.log("qntTerima1", qntTerima1)
      console.log("qntTerima2", qntTerima2)
      console.log("flagtipe",flagtipe)
      console.log("nobatch",nobatch)



    $.ajax({
      url: "{!! url('koreksireturbeli') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        norpb,
        nourut,
        inputDate,
        kodesupp,
        kodegdg,
        noout,
        keterangan,
        faktursupp,
        urut,
        kodebrg,
        urutout,
        qntTerima,
        nosat,
        satuan,
        isi,
        qntTerima1,
        qntTerima2,
        flagtipe,
        nobatch,
        nolpb
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(norpb)
        alertify.success('Item telah diedt');
        loadAll()
      }
    })
}



function loadAll () {
  $.ajax({
    url: "{!! url('rpgloadAll') !!}",
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
        <button style="" class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].KODESUPP}')" ><i class="bi bi-info-lg"></i></button>
        <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('${item[0].KODESUPP}')"><i class="bi bi-bag-plus-fill"></i></button></td>
        <td>${item[0].NOBUKTI}</td>
        <td>${date1}</td>
        <td>${item[0].NAMACUSTSUPP}</td>
        </tr>`
      })

      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        'order': [[1, 'asc']],
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
        if (item.Tanggal) {
          let date = new Date(item.Tanggal);
          let day = ("0" + date.getDate()).slice(-2);
          let month = ("0" + (date.getMonth() + 1)).slice(-2);
          date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        }

        rowTableRetur += `<tr>
        <td class="text-center">
        <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item.NoBukti}')" ><i class="bi bi-info-lg"></i></button>
        <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('${item.NoBukti}')" ><i class="bi bi-printer"></i></button>
        </td>
        <td>${item.NoBukti}</td>
        <td>${date1}</td>
        <td>${item.NamaCustSupp}</td>

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



    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');
  
    function setActiveTab(homeActive) {
      if (homeActive == 0) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';

        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';


      } else if (homeActive == 1){
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';

      }
    }
  
    // Default warna tab
    setActiveTab(0);
  
    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(0);
    });
  
    tabProfile.addEventListener('click', function () {
      setActiveTab(1);
    });



function submitPrint (nobukti) {

  let _token = $('#_token').val()

  let namaTtdCetak = ''

  const options = ['EVY YUSIA', 'JULIA', 'DESTI']

  const overlay = document.createElement('div')
  overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;'

  overlay.innerHTML = `
    <div style="background:#fff;padding:24px;border-radius:8px;min-width:320px;font-family:sans-serif;font-size:14px;">
      <h3 style="margin:0 0 16px;">Cetak Purchase Order</h3>
      <label style="display:block;margin-bottom:6px;">Ditandatangani oleh :</label>
      <select id="selectNamaTtd" style="width:100%;padding:6px;font-size:14px;border:1px solid #ccc;border-radius:4px;">
        <option value="">-- Pilih --</option>
        ${options.map(n => `<option value="${n}">${n}</option>`).join('')}
      </select>
      <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:20px;">
        <button id="btnBatalTtd" style="padding:6px 16px;border:1px solid #ccc;background:#fff;border-radius:4px;cursor:pointer;">Batal</button>
        <button id="btnLanjutTtd" style="padding:6px 16px;background:#333;color:#fff;border:none;border-radius:4px;cursor:pointer;">Cetak</button>
      </div>
    </div>
  `

  document.body.appendChild(overlay)

  document.getElementById('btnBatalTtd').onclick = () => document.body.removeChild(overlay)

  document.getElementById('btnLanjutTtd').onclick = () => {
    namaTtdCetak = document.getElementById('selectNamaTtd').value
    document.body.removeChild(overlay)
  
    $.ajax({
      url: "{!! url('returpembeliangudangprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)
        
      }
    })
  
    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) 
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''


    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    
    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
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

      .border-bottom {
        border: bottom;
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
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2" style='text-decoration:underline; color:blue;'>CV. SINAR MAHAKAM LESTARI</h2>
                      </div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">Surat Jalan Retur Gudang</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NOMOR</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NoBukti}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">PO. NO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Nopesanan}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">INVOICE</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].Noinvoice}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td class="text-center" style="width: 5%">KODE BRG</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0
    
    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
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
    tempPrintStr += `
      <tr>
        <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 2%;">${z+1}</td>
        <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NAMABRG  }</td>
        <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.KodeBrg}</td>
        <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
        <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: center; width: 5%;">${itemSub.SATUAN}</td>
      </tr>`;
    z++;
  });

// Fill remaining empty rows — table is 225px, each row ~24px, header ~24px = ~8 total slots
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 24px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>
         
          <hr style="margin-top: -6px" />

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>

         <span style="float: left; display: block; clear: left;">
         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Hormat Kami,</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Penerima,</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
                  <p class="m-0" style="white-space: pre;">                             (                                    )</p>
                </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
         </div>


         <div class="footer-print-date" style='margin-bottom:-100px;'>
        <table class="m-0" style="width: 100% ; font-family: sans-serif;
        font-size: 10px ">
          <tr>
            <td class="no-border text-bold">- Permasalahan : Barang yang disupply tidak sesuai dengan permintaan user</td>
          </tr>
        </table>
      </div>`


        tempPrintStr += `</div>`
  });


      tempPrintStr +=  `</body></html>`

    w=window.open(' ')
    w.document.write(tempPrintStr)
    w.print()
    w.close()
    }
  } 




</script>




@endsection
