@extends('gudang.newmaster')
@section('buttons')

@endsection

@section('css')
@endsection

@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="pageHome" class="container-fluid">
    	<div class="">
    	  <div class="row">
    	    <div class="col-6 text-left">
    	      <h2 style="margin-top:-85px;">Pembebanan Sample</h2>
    	    </div>
    	    <div class="col-6 text-right">
    	      <button type="button" class="btn btn-primary btn-lg button-action" style="margin-top: -150px;" onclick="buttonAdd()">Add BBS</button>
    	    </div>
    	  </div>
    	</div>

    	<div id="contentContainer" class="" >
    		<input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
        <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

        <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
        <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
        <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
        <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
        <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
        <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

        <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

        <div class="card">
          <div class="card-header" style="margin-top:-55px;">
            <div class="row">
              <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
                <a class="nav-item nav-link active buttonnav-active" id="nav-home-tab" href="#tabhome" role="tab" aria-controls="nav-home" aria-selected="true" onclick="doSetActiveNavTab('home')">
                  Pembebanan Sample
                </a>
                <a class="nav-item nav-link buttonnav-inactive" id="nav-listsdhoto-tab" href="#tablistsdhoto" role="tab" aria-controls="nav-listsdhoto" aria-selected="false" onclick="doSetActiveNavTab('listsdhoto')">
                  Sudah Otorisasi
                </a>
              </div>
            </div>
          </div>

          <div class="card-body" style="padding:0;">
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="tabhome" role="tabpanel" aria-labelledby="home-tab">
                <div class="row">
                  <div class="col-md-12">
                    <div class="container-fluid col-sm-12 customTable" style="padding:0; margin:0; width:100%;">
                      <table id="tabelhome" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Actions</th>
                            <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Customer</th>
                            <th style="padding: 4px 12px;" scope="col">User</th>
                            {{-- <th style="padding: 4px 12px;" scope="col">Oto</th> --}}
                          </tr>
                        </thead>
                        <tbody id="tabelhome_data" class="text-left">
                          @if (count($listBBS) > 0)
                            @for ($i = 0; $i < count($listBBS); $i++)
                              <tr>
                                <td class="text-center">
                                  <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $listBBS[$i]->NoBukti }}')">
                                    <i class="bi bi-info"></i>
                                  </button>
                                  <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('{{ $listBBS[$i]->NoBukti }}')">
                                    <i class="bi bi-pencil-fill"></i>
                                  </button>
                                  <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('{{ $listBBS[$i]->NoBukti }}')">
                                    <i class="bi bi-key-fill"></i>
                                  </button>
                                </td>

                                <td>{{ $listBBS[$i]->NoBukti }}</td>
                                <td>{!! date("Y/m/d", strtotime($listBBS[$i]->Tanggal)) !!}</td>
                                <td>{{ $listBBS[$i]->KodeCustSupp }}</td>
                                <td>{{ $listBBS[$i]->NAMACUSTSUPP }}</td>
                                <td>{{ $listBBS[$i]->IDUSER }}</td>

                                {{-- <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td> --}}
                              </tr>
                            @endfor
                          @else
                            <tr>
                              <td colspan="6" class="text-center text-muted">Tidak ada transaksi</td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              {{-- <td style="display: none;"></td> --}}
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="tablistsdhoto" role="tabpanel" aria-labelledby="listsdhoto-tab">
                <div class="row">
                  <div class="col-md-12">
                    <div class="container-fluid col-sm-12 customTable" style="padding:0; margin:0; width:100%;">
                      <table id="tabellistsdhoto" class="table table-bordered table-hover table-striped table-responsive-lg">
                        <thead class="text-center bg-primary text-white">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Actions</th>
                            <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Customer</th>
                            <th style="padding: 4px 12px;" scope="col">User</th>
                            {{-- <th style="padding: 4px 12px;" scope="col">Oto</th> --}}
                            <th style="padding: 4px 12px;" scope="col">User Oto</th>
                            <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
                          </tr>
                        </thead>
                        <tbody id="tabellistsdhoto_data" class="text-left">
                          @if (count($listSdhOto) > 0)
                            @for ($i = 0; $i < count($listSdhOto); $i++)
                              <tr>
                                <td class="text-center">
                                  <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $listSdhOto[$i]->NoBukti }}')">
                                    <i class="bi bi-info"></i>
                                  </button>
                                  <button class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonBatalOtorisasi('{{ $listSdhOto[$i]->NoBukti }}')">
                                    <i class="bi bi-key-fill"></i>
                                  </button>
				  <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{ $listSdhOto[$i]->NoBukti }}')" ><i class="bi bi-printer"></i>
                                  </button>
                                </td>

                                <td>{{ $listSdhOto[$i]->NoBukti }}</td>
                                <td>{!! date("Y/m/d", strtotime($listSdhOto[$i]->Tanggal)) !!}</td>
                                <td>{{ $listSdhOto[$i]->KodeCustSupp }}</td>
                                <td>{{ $listSdhOto[$i]->NAMACUSTSUPP }}</td>
                                <td>{{ $listSdhOto[$i]->IDUSER }}</td>

                                {{-- <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td> --}}
                                <td>{{ $listSdhOto[$i]->OtoUser1 }}</td>
                                <td>{!! date("Y/m/d", strtotime($listSdhOto[$i]->TglOto1)) !!}</td>
                              </tr>
                            @endfor
                          @else
                            <tr>
                              <td colspan="8" class="text-center text-muted">Tidak ada transaksi</td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              <td style="display: none;"></td>
                              {{-- <td style="display: none;"></td> --}}
                            </tr>
                          @endif
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
</div>

<div id="pageForm" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left">
          <h2 style="margin-top: -80px;">Form Pembebanan Sample</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg button-action" style="margin-top: -120px;" onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

      <div id="modalBodyAddMain" class="">
        <div class="modal-body" style="margin-top:-60px;">
          <div class="row">

            <input type="hidden" class="form-control" id="input_nourut">

            <div class="col-md-12">
              <div class="row">
                <div class="col-md-2">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="text" class="form-control text-center" id="input_nobukti" placeholder="" disabled>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group">
                    <label>Tanggal</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input type="date" class="form-control text-center" id="input_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="row">
                <div class="col-2">
                  <div class="form-group">
                    <label>No. Serah Sample</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control lockableModeDetail" id="input_noserahsample" onkeypress="doBrowseDirectFilter('NoSerahSample', '{!! $noserahsample !!}', 'input_noserahsample')" disabled>
                    <button id="buttonbrowse_noserahsample" onclick="doBrowseMaster('NoSerahSample', '{!! $noserahsample !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                </div>

                <div class="col-1">
                  <div class="form-group">
                    <label>Sales</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_namasales" disabled>
                    <input type="hidden" id="input_sales">
                    {{-- <button id="buttonbrowse_sales" onclick="doBrowseMaster('Sales', '{!! $sales !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button> --}}
                  </div>
                </div>

                <div class="col-1">
                  <div class="form-group">
                    <label>Customer</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="input-group form-group">
                    <input type="text" class="form-control" id="input_namacustsupp" disabled>
                    <input type="hidden" id="input_customer">
                    {{-- <button id="buttonbrowse_customer" onclick="doBrowseMaster('Customer', '{!! $customer !!}')" class="btn btn-primary btn-sm text-right lockableHeader lockableModeDetail">
                      <i class="bi bi-plus"></i>
                    </button> --}}
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto;">
          <div class="row">
            <table id="tabelitem" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead id="tabelitem_header" class="text-center bg-primary text-white">
                <tr>
                  <th  style="padding: 4px 12px;" scope="col" colspan="3"></th>
                  <th  style="padding: 4px 12px;" scope="col" colspan="2">Satuan</th>
                  <th  style="padding: 4px 12px;" scope="col"></th>
                </tr>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Gudang</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabelitem_data" class="text-left lockableModeDetail" >
              </tbody>
            </table>
          </div>
        </div>

        <div class="row ">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" class="btn btn-primary btn-lg button-action hideableModeDetail" onclick="buttonItemAdd()"><b>+ Tambah Item</b></button>
          </div>
        </div>

        <div id="formItem" class="container-fluid showhide">
          <hr/>
          <div class="row">
            <div class="col-4">
              <h4 id="formItem_labelAdd" style="margin-left:-35px;">Add Item</h4>
              <h4 id="formItem_labelEdit" style="margin-left:-35px;">Edit Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="row">

                <!-- START OF ITEM Kode Barang, Nama Barang -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <input type="text" class="form-control" id="inputitem_urut" hidden>

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Kode Barang</label>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="inputitem_kodebrg" disabled>
                            <button onclick="doBrowseMaster('Barang', '{!! $barang !!}')" id="btnitem_kodebrg" class="btn btn-primary btn-sm text-right lockableItemModeEdit">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Nama Barang</label>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="inputitem_namabrg" disabled>
                            <input type="text" class="form-control" id="inputitem_urutserahsample" hidden>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Gudang, Kode Barang, Nama Barang -->


                <!-- START OF ITEM Gudang, Quantity, Satuan -->
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">

                      <div class="row">
                        <div class="col-3">
                          <div class="form-group">
                            <label>Gudang</label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="inputitem_gudang" disabled>
                            <button onclick="doBrowseMaster('Gudang', '{!! $gudang !!}')" class="btn btn-primary btn-sm text-right">
                              <i class="bi bi-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Quantity</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <input type="number" class="form-control" id="inputitem_quantity">
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <label>Satuan</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="input-group form-group">
                            <select id="inputitem_satuan" class="form-control form-select-lg mb-3 text-center" aria-label=".form-select-lg example" disabled>
                            </select>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <!-- END OF ITEM Quantity, Satuan, Keterangan -->

              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-danger btn-lg button-action" onclick="closeFormItem()" class="btn btn-secondary">Batal</button>
              <button type="button" id="buttonSubmitItem" class="btn btn-primary btn-lg button-action" onclick="submitItem()" class="btn btn-secondary">Submit</button>
            </div>

          </div>
        </div>
      </div>
</div>

@include('gudang.modalbrowsemaster')

@endsection

@section('js')
<script src="{!! URL::asset('js/ajc-func-core.js') !!}"></script>
<script src="{!! URL::asset('js/ajc-browsemaster.js') !!}"></script>
<script>
  const BASE_URL = "{{ url('/') }}";

  const g_tipeformNone = "", g_tipeformAdd = "add", g_tipeformEdit = "edit", g_tipeformDetail = "detail";
  var   gtipeform = g_tipeformNone;

  const g_tipeformitemNone = "", g_tipeformitemAdd = "add", g_tipeformitemEdit = "edit",
        g_tipeformitemDelete = "delete";
  var   gtipeformitem = g_tipeformitemNone;

  const g_modalNone = "";
  var   gmodemodal = g_modalNone;

  var dataItem = [];
  var dataBrowse = [];

  $(document).ready(function(){
    $("#tabelhome").DataTable({
      "lengthChange": false,
      "paging": false ,
    });

    $("#tabellistsdhoto").DataTable({
      "lengthChange": false,
      "paging": false ,
    });
  });

  function loadAll() {
    let listBBS = [], listSdhOto = [];
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('bbsloadall') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        listBBS = res.listBBS;
        listSdhOto = res.listSdhOto;
    }});

    // === listBBS === //
    loadBBS(listBBS, "tabelhome", true);
    loadBBS(listSdhOto, "tabellistsdhoto", false);
  }

  function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('bbsdetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])
        
        // console.log(res[0][0].IsOtorisasi1)

      }
    })
    
    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].TANGGAL.split(' ')[0];

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
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Dari : PT. ${dataPrint[0].namaCustSupp ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BEBAN SAMPLE</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No Bukti</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NOBUKTI+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>
              <div
                style="
                  width: 12%;
                  height: 80px;
                  overflow: hidden;">`+printContent+`
              </div>
            </div>
      <table

                class="detail-spb-table"
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 20%">QUANTITY</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                    <td class="text-center" style="width: 20%">HARGA</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.JumlahHRHPP) {
          grandTotal += parseFloat(item.JumlahHRHPP) || 0;
        }
      });
    });
    // end
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
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NAMABRG}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  ">${itemSub.Sat}</td>
         <td class="text-align: text-right"
               style="width: 20%;  ">${itemSub.JumlahHRHPP ? parseFloat(itemSub.JumlahHRHPP).toFixed(2) : ''}</td>
         </tr>`;

           z++;

        });
        tempPrintStr +=`
          <tr style>

          </tr>`;

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>
         
          <hr style="margin-top: -6px" />

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>
         
         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

            <div style="width:80%; text-align:right; padding-right:10px;">
              Total :
            </div>

            <div style="width:20%; text-align:right;">
              ${grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>
         
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Mengetahui</td>
               <td class="no-border text-center" style="width: 10%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
              <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
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

    w.print()
    w.close()

  }

  function loadBBS(_list, _table, _isBlmOto) {
    $('#' + _table).DataTable().destroy();

    let rowTable = "";
    if (_list.length > 0) {
      _list.forEach((item, i) => {
        let dateBBS = doSetFormatDate(item.Tanggal, "/");
        let dateOto1 = item.TglOto1 ? doSetFormatDate(item.TglOto1, "/") : "";

        rowTable += "<tr>";
        rowTable += _isBlmOto 
          ? doSetActionBlmOto(item.NoBukti) 
          : doSetActionSdhOto1(item.NoBukti);

        rowTable += "<td>" + item.NoBukti + "</td>";
        rowTable += "<td>" + dateBBS + "</td>";
        rowTable += "<td>" + item.KodeCustSupp + "</td>";
        rowTable += "<td>" + item.NAMACUSTSUPP + "</td>";
        rowTable += "<td>" + item.IDUSER + "</td>";

        if (!_isBlmOto) {
          rowTable += "<td>" + (item.OtoUser1 ?? "") + "</td>";
          rowTable += "<td>" + (dateOto1 ?? "") + "</td>";
        }

        rowTable += "</tr>";
      });
    } else {
      let cHeader = _isBlmOto ? 6 : 8;
      rowTable += doSetEmptyTable(cHeader, "Tidak ada transaksi");
    }

    $("#" + _table +"_data").html(rowTable);

    $("#" + _table).DataTable({
      "lengthChange": false,
      "paging": false,
    });
  }

  function doSetActionSdhOto1 (_nb) {
    console.log('aa,');
    let rowTable = "";
    rowTable += '<td class="text-center">';
    rowTable += '<button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail(\'' + _nb + '\')"><i class="bi bi-info"></i></button>';
    rowTable += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + _nb + '\')"><i class="bi bi-key-fill"></i></button>';
    rowTable += '<button class="btn btn-primary btn-sm" type="button" title="Submit Print" onclick="submitPrint(\'' + _nb + '\')"><i class="bi bi-printer"></i></button>';
    rowTable += "</td>";
    
    return rowTable;
  }

  function closeFormItem() {
    $('.showhide').hide();
    doUnlockHeader();
    doUnlockModeEdit();
    $("#buttonbrowse_noserahsample").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_sales").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_customer").prop("disabled", dataItem.length > 0);
  }

  function refreshForm(_nobukti = "") {
    let rowTable = "";
    dataItem = [];

    if (_nobukti == "") {
      let cHide = (gtipeform == g_tipeformDetail) ? 1 : 0;
      rowTable += doSetEmptyTable(10 - cHide, "Belum ada barang");
    } else {
      let _token  = $("#_token").val();
      $.ajax({
        url: "{!! url('bbsgetdetail') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti: _nobukti
        },
        success: function(res) {
          if (!res.list.length) {
            alertify.warning("Data habis");
            $('#pageForm').hide();
            $('#pageHome').show();
          } else {
            let dataHeader = res.header[0];
            dataItem = res.list;

            dataItem.forEach((item, i) => {
              rowTable += `<tr>
                <td>${nullToEmpty(item.KODEGDG)}</td>
                <td>${nullToEmpty(item.KODEBRG)}</td>
                <td>${nullToEmpty(item.NAMABRG)}</td>
                <td class="text-right">${item.Qnt ? formatCurrency(item.Qnt) : '0.00'}</td>
                <td>${nullToEmpty(item.Sat)}</td>

                ${gtipeform == g_tipeformDetail ? `` :
                `<td class="text-center">
                  <button class="btn btn-success btn-sm" type="button" onclick="buttonItemEdit(${i})"><i class="bi bi-pen"></i></button>
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonItemDelete(${i})"><i class="bi bi-trash"></i></button>
                </td>`}
              </tr>`;
            });
            

            $("#input_nobukti").val(dataHeader.NOBUKTI);
            $("#input_nourut").val(dataHeader.nOURUT);
            $("#input_tanggal").val(doSetFormatDate(dataHeader.TANGGAL, "-"));
            $("#input_noserahsample").val(dataHeader.NoRSerahSample);
            $("#input_sales").val(dataHeader.KodeSLS);
            $("#input_namasales").val(dataHeader.NamaSLS);
            $("#input_customer").val(dataHeader.KodeCustSupp);
            $("#input_namacustsupp").val(dataHeader.NamaCustSupp);

            dataBrowse['noserahsample']  = dataHeader.NoRSerahSample;
            dataBrowse['sales']  = dataHeader.KodeSLS;
            dataBrowse['customer']  = dataHeader.KodeCustSupp;
          }
        },
        error: function (err) {
          console.log(err)
          console.log(err.status)
          console.log(err.statusText)
          alertify.warning('Terjadi kesalahan, silahkan refresh browser')
        }
      });
    }

    $("#tabelitem_data").html(rowTable);

    let rowHeader = `
    <tr>
      <th  style="padding: 4px 12px;" scope="col" colspan="3"></th>
      <th  style="padding: 4px 12px;" scope="col" colspan="2">Satuan</th>
      ${gtipeform == g_tipeformDetail ? `` : `<th  style="padding: 4px 12px;" scope="col"></th>`}
    </tr>
    <tr>
      <th style="padding: 4px 12px;" scope="col">Gudang</th>
      <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
      <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
      <th style="padding: 4px 12px;" scope="col">Qty</th>
      <th style="padding: 4px 12px;" scope="col">Sat</th>
      ${gtipeform == g_tipeformDetail ? `` : `<th style="padding: 4px 12px;" scope="col">Actions</th>`}
    </tr>
    `;
    $("#tabelitem_header").html(rowHeader);
  }

  function buttonCloseForm() {
    gtipeform = g_tipeformNone;
    cleanFormItem();
    doUnlockModeDetail();
    $('#pageForm').hide();
    $('#pageHome').show();
  }

  function buttonAdd() {
    if (!doCekAkses("akses_istambah")) return;
    if (!doCekPeriode("periode_bulan", "periode_tahun", "input_tanggal")) return;

    gtipeform = g_tipeformAdd;
    $('.showhide').hide();

    cleanFormHeader();
    refreshForm();
    doUnlockHeader();
    
    let nb = doGenerateNoBukti("BBS");
    $("#input_nobukti").val(nb.Nobukti);
    $("#input_nourut").val(nb.Nourut);

    dataBrowse['noserahsample']  = "";
    dataBrowse['sales']  = "";
    dataBrowse['customer']  = "";

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonEdit(_nb) {
    if (!doCekAkses("akses_iskoreksi")) return;
    if (!doCekOtorisasi(_nb, "bbscekotorisasi")) return;

    gtipeform = g_tipeformEdit;
    $('.showhide').hide();

    refreshForm(_nb);
    doUnlockHeader();
    $("#buttonbrowse_noserahsample").prop("disabled", dataItem.length > 0);
    $("#input_noserahsample").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_sales").prop("disabled", dataItem.length > 0);
    $("#buttonbrowse_customer").prop("disabled", dataItem.length > 0);

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonDetail(_nb) {
    if (!doCekAkses("akses_iskoreksi")) return;
    gtipeform = g_tipeformDetail;
    $('.showhide').hide();

    refreshForm(_nb);
    doLockModeDetail();

    $('#pageHome').hide();
    $('#pageForm').show();
  }

  function buttonOtorisasi(_nb) {
    if (!doCekAkses("akses_isotorisasi1")) return;

    doOtorisasi("Pembebanan Sample", _nb, "bbsupdateotorisasi", function (res) {
      if (res.status === 1) {
        alertify.success(res.msg);
        loadAll();
      } else {
        alertify.warning(res.msg);
      }
    });
  }


  function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('bbsupdatebatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          pket :value
        },
        success: function (res) {
          alertify.success('Berhasil batal otorisasi');
          loadAll();
        },
        error: function (err) {
          console.error(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Batal otorisasi dibatalkan');
      alertify.error("Action cancelled");
    }
  );
}

  // function buttonBatalOtorisasi(_nb) {
  //   if (!doCekAkses("akses_isbatal")) return;

    
  //   doBatalOtorisasi("Pembebanan Sample", _nb, "bbsupdatebatalotorisasi", function (res) {
  //     if (res.status === 1) {
  //       alertify.success(res.msg);
  //       loadAll();
  //     } else {
  //       alertify.warning(res.msg);
  //     }
  //   });


  // }

  function cleanFormHeader() {
    $("#input_nobukti").val("");
    $("#input_nourut").val("");
    $("#input_noserahsample").val("");
    $("#input_sales").val("");
    $("#input_namasales").val("");
    $("#input_customer").val("");
    $("#input_namacustsupp").val("");
  }

  function buttonItemAdd() {
    if (!cekNotEmpty("input_noserahsample")) {
      return alertify.warning(messageRequired("No. Serah Sample"));
    }

    if (dataBrowse['noserahsample'] !== $("#input_noserahsample").val()) {
      return alertify.warning("Data No. Serah Sample tidak sesuai");
    }

    gtipeformitem = g_tipeformitemAdd;
    $('.showhide').hide();
    $('#formItem_labelAdd').show();
    $('#formItem_labelEdit').hide();

    doLockHeader();
    cleanFormItem();

    dataBrowse['barang']  = "";
    dataBrowse['gudang']  = "";

    $('#formItem').show();

    $("#btnitem_kodebrg").prop("disabled", false);
    $("#inputitem_satuan").prop("disabled", false);

    document.getElementById("btnitem_kodebrg").scrollIntoView();
}
  function buttonItemEdit(_urut) {
    gtipeformitem = g_tipeformitemEdit;
    $('.showhide').hide();
    $('#formItem_labelEdit').show();
    $('#formItem_labelAdd').hide();

    doLockHeader();
    doLockModeEdit();
    cleanFormItem();

    let item = dataItem[_urut];
    $("#inputitem_urut").val(item.URUT);
    $("#inputitem_gudang").val(item.KODEGDG);
    dataBrowse['gudang'] = item.KODEGDG;

    if (item.NamaBrg != "") {
      showSatuanBarang(item.Nosat, item.KODEBRG, item.NAMABRG,
        item.Qnt, item.UrutRSerahSample,
        item.brgSat1, item.brgSat2, item.brgSat3,
        item.brgIsi1, item.brgIsi2, item.brgIsi3);
    }

    $('#formItem').show();

    document.getElementById("inputitem_kodebrg").scrollIntoView();
  }

  function buttonItemDelete(_urut) {
    if (!doCekAkses("akses_ishapus")) return;

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + nullToEmpty(dataItem[_urut].NAMABRG) + ' ?',
      function() {
        gtipeformitem = g_tipeformitemDelete;
        $("#inputitem_urut").val(dataItem[_urut].URUT);
        submitItem();
      },
      function(){
        console.log('Penghapusan dibatalkan')
      }
    );
  }

  function cleanFormItem() {
    $("#inputitem_urut").val(0);
    $("#inputitem_kodebrg").val("");
    $("#inputitem_namabrg").val("");
    $("#inputitem_urutserahsample").val("");
    $("#inputitem_gudang").val("");
    $("#inputitem_quantity").val(0);
    $('#inputitem_satuan').empty();
    $('#inputitem_satuan').prop("disabled", true);
  }

  function buttonBrowsePickNoSerahSample(_nb, _kodeSls, _namaSls, _kodeCust, _namaCust) {
  $("#input_noserahsample").val(_nb);

  $("#input_namasales").val(_namaSls);
  $("#input_namacustsupp").val(_namaCust);

  $("#input_sales").val(_kodeSls);
  $("#input_customer").val(_kodeCust);

  dataBrowse['noserahsample'] = _nb;
  dataBrowse['sales'] = _kodeSls;
  dataBrowse['customer'] = _kodeCust;
}


  // function buttonBrowsePickNoSerahSample(_nb) {
  //   $("#input_noserahsample").val(_nb);
  //   dataBrowse['noserahsample'] = _nb;
  // }

  function buttonBrowsePickSales(_kode) {
    $("#input_sales").val(_kode);
    dataBrowse['sales'] = _kode;
  }

  function buttonBrowsePickCustomer(_kode) {
    $("#input_customer").val(_kode);
    dataBrowse['customer'] = _kode;
  }

  function doBrowseBarang() {
    bm_params['noserahsample'] = $("#input_noserahsample").val();
    return true;
  }

  function buttonBrowsePickBarang(_kode, _nama, _qnt, _urut, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3) {
    $("#inputitem_kodebrg").val(_kode);
    $("#inputitem_namabrg").val(_nama);
    $("#inputitem_quantity").val(_qnt);

    let satuanSelect = $("#inputitem_satuan");
    satuanSelect.empty(); // Clear previous options

    // Array of satuan objects with number and value
    const satuanList = [
      { nosat: 1, sat: _sat1, isi: _isi1, sat2: _sat2 },
      { nosat: 2, sat: _sat2, isi: _isi2, sat2: _sat1 },
      { nosat: 3, sat: _sat3, isi: _isi3, sat2: _sat1 }
    ];

    // Append valid satuan options
    let added = 0;
    satuanList.forEach(item => {
      if (item.sat && item.sat.trim() !== '') {
        satuanSelect.append(`<option value="${item.nosat}||${item.sat}||${item.isi}||${item.sat2}">${item.nosat} - ${item.sat}</option>`);
        added++;
      }
    });

    satuanSelect.prop("disabled", added === 0);

    $("#inputitem_urutserahsample").val(_urut);

    dataBrowse['barang'] = _kode;
  }

  function buttonBrowsePickGudang(_kode) {
    $("#inputitem_gudang").val(_kode);
    dataBrowse['gudang'] = _kode;
  }

  function showSatuanBarang(_nosat, _kode, _nama, _qnt, _urut, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3) {
    buttonBrowsePickBarang(_kode, _nama, _qnt, _urut, _sat1, _sat2, _sat3, _isi1, _isi2, _isi3)

    const satuanSelect = $("#inputitem_satuan");

    // Find and select the matching option
    satuanSelect.find("option").each(function () {
      const value = $(this).val(); // e.g., "2||Box||12"
      const parts = value.split("||");

      if (parseInt(parts[0]) === _nosat) {
        satuanSelect.val(value); // set this option as selected
        return false; // break loop
      }
    });
  }

  function getStockSerahSample(_noserahsample, _urutserahsample) {
    let stock = [];
    $.ajax({
      url: "{!! url('bbsgetstockserahsample') !!}",
      type: "get",
      async: false,
      data: {
        noserahsample    : _noserahsample,
        urutserahsample  : _urutserahsample
      },
      success: function(res) {
        stock = res.stock;
    }});

    return (stock.length > 0) ? stock[0].qntsample : 0;
  }

  function cekValidate(_choice) {
    /* Kolom yang wajib diisi harus dicek. Kolom yang tidak wajib di-set ke default value jika perlu.
       Return pesan jika ada yang tidak valid. Return object cart jika semuanya sudah valid. */

    let cart = {};

    // === untuk hapus item ===
    if (_choice === "D") {
      cart["choice"]  = _choice;
      cart["nobukti"] = $("#input_nobukti").val();
      cart["urut"]    = $("#inputitem_urut").val();
      return cart;
    }
    // === Akhir tambahan ===

    // CEK EMPTY
    if (!cekNotEmpty("inputitem_kodebrg")) {
      return messageRequired("Barang");
    }
    if (!cekNotEmpty("inputitem_gudang")) {
      return messageRequired("Gudang");
    }

    // CEK VALIDASI KODE / NO BUKTI
    if (dataBrowse['barang'] !== $("#inputitem_kodebrg").val()) {
      return "Data Barang tidak sesuai";
    }
    if (dataBrowse['gudang'] !== $("#inputitem_gudang").val()) {
      return "Data Gudang tidak sesuai";
    }

    cart["choice"]          = _choice;
    cart["nobukti"]         = $("#input_nobukti").val();

    cart["nourut"]          = $("#input_nourut").val();
    cart["tanggal"]         = $("#input_tanggal").val();
    cart["urut"]            = $("#inputitem_urut").val();
    cart["kodebrg"]         = $("#inputitem_kodebrg").val();

    let satuan = $('#inputitem_satuan').val();
    if (satuan && satuan.trim() !== "") {
      satuan = satuan.split('||').map((v, i) => i === 2 ? parseFloat(v) : v);
      cart["nosat"]         = satuan[0];
      cart["satuan"]        = satuan[1];
      cart["isi"]           = satuan[2];
      cart["satuan1"]       = satuan[1];
      cart["satuan2"]       = satuan[3];
    } else {
      cart["nosat"]         = 0;
      cart["satuan"]        = "";
      cart["isi"]           = 0.00;
      cart["satuan1"]       = "";
      cart["satuan2"]       = "";
    }

    let stock = getStockSerahSample($("#input_noserahsample").val(), $("#inputitem_urutserahsample").val());

    if (Number($("#inputitem_quantity").val()) <= Number(stock)) {
      cart["qnt"]           = $("#inputitem_quantity").val();
    } else {
      return "Qty Barang " + cart["kodebrg"] + " melebihi quantity sample";
    }

    cart["jmlrecord"]       = (dataItem.length) ? 1 : 0;
    cart["kodegdg"]         = $("#inputitem_gudang").val();
    cart["kodesls"]         = $("#input_sales").val();
    cart["kodecust"]        = $("#input_customer").val();
    cart["noserahsample"]   = $("#input_noserahsample").val();
    cart["urutserahsample"] = $("#inputitem_urutserahsample").val();

    return cart;
}

  function submitItem () {
    doSubmitItem("item", "bbsspadd", "BBS");
  }

  function successAdd(_nobukti) {
    loadAll();
    cleanFormItem();
    refreshForm(_nobukti);

    gtipeform = g_tipeformEdit;
  }

  function successEdit(_nobukti) {
    loadAll();
    $('.showhide').hide();
    cleanFormItem();
    refreshForm(_nobukti);
  }

  function successDelete(_nobukti) {
    loadAll();
    $('.showhide').hide();
    refreshForm(_nobukti);
  }

</script>

@endsection