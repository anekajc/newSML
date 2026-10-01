@extends('gudang.newmaster')
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
  #tabel2_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel2_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel2_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabel2_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 2 --}}

{{-- tampilan search sales --}}
  <style>
    #tabel_add_list_sales_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sales_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sales --}}

{{-- tampilan search serahsample --}}
  <style>
    #tabel_add_list_serahsample_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_serahsample_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search serahsample --}}

{{-- tampilan search modal barang all --}}
  <style>
    #input_search_barang_all {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
    }
    .search-label {
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: -155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }
  </style>
{{-- end tampilan search modal barang all --}}
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id='page1' class="container-fluid">
  <!-- <div id="qrcode"></div> -->
  <div class="" style="margin-top:-80px;">
  <div class="row">
    <div class="col-6 text-left">
      <h1>Retur Penyerahan Konsinyasi</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="
      height: 30px; 
      margin-top: 20px; 
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
      onclick="buttonAdd()">Add Retur Konsinyasi</button>
    </div>

<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">
  {{-- Hidden Inputs --}}
  <input type="hidden" id="periode_tahun" value="{{ $periode->tahun }}">
  <input type="hidden" id="periode_bulan" value="{{ $periode->bulan }}">
  <input type="hidden" id="akses_istambah" value="{{ $akses->ISTAMBAH }}">
  <input type="hidden" id="akses_ishapus" value="{{ $akses->ISHAPUS }}">
  <input type="hidden" id="akses_iskoreksi" value="{{ $akses->ISKOREKSI }}">
  <input type="hidden" id="akses_iscetak" value="{{ $akses->ISCETAK }}">
  <input type="hidden" id="akses_isotorisasi1" value="{{ $akses->IsOtorisasi1 }}">
  <input type="hidden" id="akses_isbatal" value="{{ $akses->IsBatal }}">
  <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">

  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
          <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
             aria-controls="nav-home" aria-selected="true"
             style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem;">
            RPK Belum Otorisasi
          </a>
          <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
             aria-controls="nav-profile" aria-selected="false"
             style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
            RPK Sudah Otorisai
          </a>
        </div>
      </div>
    </div>

    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">
        {{-- Utama --}}
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">
                <table id="tabel" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Actions</th>
                      <th>No Bukti</th>
                      <th>Tanggal</th>
                      <th>User</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data" class="text-left">
                    @foreach ($listData as $item)
                    <tr>
                    <td class="text-center">
                      <div class="d-flex justify-content-center align-items-center gap-2">
                          <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('{{ $item->NoBukti }}')">
                              <i class="bi bi-info"></i>
                          </button>
                          <button class="btn btn-success btn-sm p-1 px-2" title="Edit" onclick="buttonEdit('{{ $item->NoBukti }}')">
                              <i class="bi bi-pen"></i>
                          </button>
                          @if($item->IsOtorisasi1 == 0)
                          <button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('{{ $item->NoBukti }}')">
                            <i class="bi bi-key"></i>
                          </button>
                          @else
                          <button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $item->NoBukti }}')">
                            <i class="bi bi-key"></i>
                          </button>
                          @endif
                      </div>
                    </td>
                    <td>{{ $item->NoBukti}}</td>
                    <td>{{ date("Y/m/d", strtotime($item->Tanggal)) }}</td>
                    <td>{{ $item->IDUser}}</td>
                    </tr>
                @endforeach
                </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        {{-- sudah otorisasi --}}
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">
                <table id="tabel2" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">User</th>
                      <th style="padding: 4px 12px;" scope="col">User Oto</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>   
                    </tr>
                  </thead>
                  <tbody id="tabel2_data" class="text-left">
                    @foreach ($listData2 as $item)
                      <tr>
                        <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('{{ $item->NoBukti }}')">
                                <i class="bi bi-info"></i>
                            </button>
                            @if($item->IsOtorisasi1 == 0)
                            <button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('{{ $item->NoBukti }}')">
                              <i class="bi bi-key"></i>
                            </button>
                            @else
                            <button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $item->NoBukti }}')">
                              <i class="bi bi-key"></i>
                            </button>
                            @endif
			    <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{ $item->NoBukti }}')" ><i class="bi bi-printer"></i>
                            </button>
                        </div>
                      </td>
                      <td>{{ $item->NoBukti}}</td>
                      <td>{{ date("Y/m/d", strtotime($item->Tanggal)) }}</td>
                      <td>{{ $item->IDUser}}</td>
                      <td>{{ $item->OtoUser1 }}</td>
                      <td>{{ $item->TglOto1 ? date("Y/m/d", strtotime($item->TglOto1)) : '' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div> {{-- End Sudah Otorisasi --}}
      </div>
    </div>
  </div>
</div>
</div>
</div>

<div id="page2" class="container-fluid" style="display: none; margin-top:-80px;" >
  <div class="row">
    <div class="col-6 text-left">
      <h1>Form Retur Penyerahan Konsinyasi</h1>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
        height: 30px; 
            margin-top: 20px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
        onclick="buttonCloseForm()">Close</button>
    </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
        <div class="row">
        <input type="hidden" class="form-control" id="input_add_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_add_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-3 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_sales" type="hidden">
                        <button type="button" id="btn_sales" onclick="buttonAddListSales()" class="btn btn-primary btn-sm rounded-end shadow-sm">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
                </div>
            </div>
          </div>
        </div>
        </div>
        </div>
    </div>
    <div class="row ">
      <div class="col-md-12 text-right">
      <button type="button" class="btn btn-primary" style="
      height: 30px;  
      padding: 4px 12px; 
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
      onclick="buttonAddAddItem()" class="btn btn-secondary">Add Item</button>
      </div>
    </div>

    <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="3">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                  <th colspan="1"></th>
                </tr>
                <tr> 
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr >
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-success btn-sm" type="button" title="Edit"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    <button class="btn btn-primary btn-sm" type="button" title="Details"><i class="bi bi-list"></i></button>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              </tbody>
            </table>          
          </div>
          {{-- <div class="text-right">
            <button type="button" class="btn btn-primary" style="
            height: 30px; 
            margin-top: 20px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAdd()">Submit</button>
        </div> --}}
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
    </div>
    <!-- ADD SUBGROUP -->
    <div id="addAddItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-15px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-15px;">Edit Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>No. Serah Sample</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                    <input id="input_add_add_serahsample" type="text" class="form-control text-center" placeholder="No. Serah Sample" readonly>
                    <button type="button" id="buttonAddListSerahSample" onclick="buttonAddListSerahSample()" class="btn btn-primary btn-sm rounded-end shadow-sm">
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                  <input type="hidden" id="input_urut_ref">
                </div>
              </div>
            <div class="row" style="margin-top:-15px;">
                <div class="col-md-3" style="margin-top:5px;">
                  <div class="form-group">
                    <label>Gudang</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <input id="input_add_add_gudang" type="text" class="form-control text-center" readonly>
                  <input type="hidden" id="input_add_add_namagudang">
                </div>
              </div>
              <div class="row" style="margin-top:-15px;">
                  <div class="col-md-3" style="margin-top:5px;">
                    <div class="form-group">
                      <label>Kode Barang</label>
                    </div>
                  </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                  <input id="input_add_add_kodebarang" type="text" class="form-control text-center" placeholder="Kode Barang" disabled>
                  {{-- <button type="button" id="buttonAddListKodeBarang" onclick="buttonAddListKodeBarang()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button> --}}
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top:-15px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-md-8">
                <input id="input_add_add_keterangannama" type="text" class="form-control text-center" disabled>
              </div>
            </div>
            <div class="row" style="margin-top:-15px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-md-3">
                <input id="input_add_add_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-md-2" style="margin-top:5px;">
                <label for="input_add_add_satuan">Satuan</label>
              </div>
              <div class="col-md-3">
                <select id="input_add_add_satuan" class="form-control">
                  <option value="" disabled selected>Pilih Satuan</option>
                </select>                
              </div>
            </div>
            </div>
          </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeShowHideAdd()" >Batal</button>

                <button type="button" id="submitAddAdd" class="btn btn-primary" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddAdd()">Submit Add</button>

                <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
                height: 30px; 
                padding: 4px 12px; 
                border-radius: 20px; 
                font-size: 0.75rem; 
                font-weight: 600; 
                text-transform: uppercase; 
                transition: background-color 0.3s, box-shadow 0.3s; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddEdit()" style="display: none;">Submit Edit</button>
              </div>
            </div>
          </div>

    <!-- END ADD ADD -->

    <!-- ADD EDIT -->

    <div id="addEditItem" class="container-fluid showhide">
            <!-- <div class="line"></div> -->
            <div class="row">
              <div class="col-4">
                <h4>Edit Item Kedua</h4>
              </div>
            </div>

            {{-- <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ref SO</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_refso" type="text" class="form-control" value="-" disabled>
              </div>
              <div class="col-1 text-right">

                <button type="button" disabled onclick="" disabled class="btn btn-primary" >+</button>
              </div>
              <div class="col-2">
                <div class="form-group">
                <label>No PO Cust</label>
              </div>
              </div>
              <div class="col-4">

                <input id="input_add_edit_nopocust" type="text" class="form-control" disabled>
              </div>
            </div> --}}
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-3">
                <input id="input_add_edit_kodebarang" type="text" class="form-control" disabled>
              </div>
              <div class="col-1 text-right">
                <button type="button" disabled onclick="" class="btn btn-primary">+</button>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_keterangannama" type="text" class="form-control" disabled>
              </div>

            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Quantity</label>
              </div>
              </div>
              <div class="col-4">
                <input id="input_add_edit_qnt" type="number" value=0.00 class="form-control text-right">
              </div>
              <div class="col-md-2">
                <label for="input_add_edit_satuan">Satuan</label>
              </div>
              <div class="col-md-4">
                <select id="input_add_edit_satuan" class="form-control" name="satuan">
                  <option value="" selected disabled>Pilih Satuan</option>
                </select>
              </div>
            </div>
            <div class="row">
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <div class="col-10">
                <input id="input_add_edit_keterangan" type="text" class="form-control">
              </div>

            </div>

            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()">Batal</button>
                {{-- <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> --}}
              </div>
            </div>
          </div>

    <!-- END ADD EDIT -->
  </div>
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
</div>

</div> {{-- end page 2 --}}


<!-- End modal add-->

{{-- Start Modal List Serah Sample --}}
  <div class="modal fade" id="modalAddListSerahSample" role="dialog" aria-labelledby="labelSerahSample" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width:95%;">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelSerahSample">Pilih No Penyerahan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" style="margin-top:-30px;">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive" style="overflow-x:auto;">
                <table id="tabel_add_list_serahsample" class="table table-bordered table-striped" style="width:100%;">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="width:50px;">Actions</th>
                      <th style="width:160px;">No Penyerahan</th>
                      <th style="width:90px;">Tanggal</th>
                      <th style="width:50px;">Kode Customer</th>
                      <th style="width:160px;">Nama Customer</th>
                      <th style="width:110px;">Kode Barang</th>
                      <th style="width:320px;">Nama Barang</th>
                      <th style="width:50px;">Qty</th>
                      <th style="width:40px;">Gudang</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_serahsample" class="text-left">
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
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
            </div>

            {{-- <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div> --}}
          </div>
        </div>

      </div>
    </div>
  </div>
{{-- End Modal List serah sample --}}

{{-- Start Modal List Sales --}}
  <div class="modal fade" id="modalAddListSales" role="dialog" aria-labelledby="labelSales" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelSales">Pilih Sales</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_sales" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Actions</th>
                      <th>NIK</th>
                      <th>Nama</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_sales" class="text-left">
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
                      <td>-</td>
                      <td>-</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
                onclick="buttonAddListBatal()">Batal</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End Modal List Sales --}}

<!-- start modal list item add -->
  <div class="modal fade" id="formAddListItem" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"></h5>
          <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto" 
            data-dismiss="modal" aria-label="Close"
            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
            <span aria-hidden="true" style="font-size: 1.2rem; font-weight: bold;">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid mt-4">

            <div class="row mb-2" style="margin-top:-30px;">
              <div class="col-12 d-flex justify-content-end" style="padding-right: 0px;">
                <div class="d-flex align-items-center">
                  <label for="input_search_barang_all" class="me-2 mb-0">Search:</label>
                  <input id="input_search_barang_all" type="text" class="form-control"
                    style="max-width: 250px;" onkeypress="searchBarangAll(event)">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="table-responsive">
              <table id="tabel_add_list_item" class="table table-bordered table-striped">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Actions</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Qty</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_item" class="text-left">
                  <!-- Diisi lewat JS -->
                </tbody>
              </table>
            </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-danger btn-lg"
                style="height: 30px; padding: 4px 12px; border-radius: 20px;
                font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="closeListItemAdd()">Close</button>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
<!-- End modal list item add-->

<!-- start modal detail -->
<div id="page3" class="container-fluid" style="display: none; margin-top:-80px;">
        <div class="row">
          <div class="col-6 text-left">
            <h2>Detail Retur Penyerahan Konsinyasi</h2>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px; 
            margin-top: 20px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
            onclick="buttonCloseForm()">Close</button>
          </div>
        </div>  
        <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">

          <div class="row">
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detail_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_detail_sales" type="hidden">
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"disabled>
                </div>
            </div>
          </div>
        </div>
        </div>
        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_detail" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="3">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Qty</th>
                </tr>
              </thead>
              <tbody id="tabel_data_detail" class="text-left" >
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
              </tr>
              </tbody>
            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
    </div>
  </div>
  <div class="modal-footer">
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
    <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
  </div>
</div>
</div>
</div>
<!-- End modal detail-->


@endsection

@section('js')
<script type="text/javascript">
let dataAddListItem = []
let dataRefresh = []

let dataTableAdd = []
let dataTableEdit = []

let dataEditListItem = []
let excludeSerahSamples = [];

let tempAdd = {} /// kalau di so tempAddAdd
let tempEdit = {} //// kalau di so tempAddEdit
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tipeform = ''
let tipeformitem = ''

$(document).ready(function(){
  $("#tabel").DataTable({
    "lengthChange": false,
    "paging": false,
  });

  $("#tabel2").DataTable({
    "lengthChange": false,
    "paging": false,
  });
  // loadAll();
});

function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}

function loadAll () {
  const _token = document.getElementById("_token").value;

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasiloadall') !!}",
    type: "GET",
    data: { _token },
    success: function (res) {
      console.log('res.outstanding', res.outstanding);

      $('#tabel').DataTable().destroy();
      let rowTable = '';

      res.outstanding.forEach((item) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <div class="d-flex justify-content-center align-items-center gap-2">
                <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('${item.NoBukti}')">
                  <i class="bi bi-info"></i>
                </button>
                <button class="btn btn-success btn-sm p-1 px-2" title="Edit" onclick="buttonEdit('${item.NoBukti}', ${item.IsOtorisasi1})">
                  <i class="bi bi-pen"></i>
                </button>
                ${
                  parseInt(item.IsOtorisasi1) === 0
                  ? `<button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}', 0)">
                        <i class="bi bi-key"></i>
                     </button>`
                  : `<button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}')">
                        <i class="bi bi-key"></i>
                     </button>`
                }
              </div>
            </td>
            <td>${item.NoBukti}</td>
            <td>${formatDate(item.Tanggal)}</td>
            <td>${item.IDUser}</td>
          </tr>`;
      });

      document.getElementById("tabel_data").innerHTML = rowTable;

      $("#tabel").DataTable({
        lengthChange: false,
        paging: false
      });

      $('#tabel2').DataTable().destroy();
      let rowTable2 = '';

      res.outstanding2.forEach((item) => {
        rowTable2 += `
          <tr>
            <td class="text-center">
              <div class="d-flex justify-content-center align-items-center gap-2">
                <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('${item.NoBukti}')">
                  <i class="bi bi-info"></i>
                </button>
                ${
                  parseInt(item.IsOtorisasi1) === 0
                  ? `<button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}', 0)">
                        <i class="bi bi-key"></i>
                     </button>`
                  : `<button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}')">
                        <i class="bi bi-key"></i>
                     </button>`
                }
		<button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.NoBukti}')">
                  <i class="bi bi-printer"></i>
                </button>
              </div>
            </td>
            <td>${item.NoBukti}</td>
            <td>${formatDate(item.Tanggal)}</td>
            <td>${item.IDUser}</td>
            <td>${item.OtoUser1 || ''}</td>
            <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
          </tr>`;
      });

      document.getElementById("tabel2_data").innerHTML = rowTable2;

      $("#tabel2").DataTable({
        lengthChange: false,
        paging: false
      });
    },

    error: function (xhr, status, error) {
      alertify.error("Gagal load data: " + error);
      console.error('LoadAll error:', error);
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('returpenyerahankonsinyasidetailCetak') !!}",
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
                    <p class="m-0">
                      JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                      RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                    </p>
                    <p class="m-0">Telp (0541) 4104142 , Fax (0541) 4104195</p>
                    <p class="m-0">E-mail : sml@indo.net.id</p>
                  </div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">SURAT PENYERAHAN BARANG</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Nomor</div>
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
                    <td class="text-center" style="width: 30%">MERK</td>
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                    <td class="text-center" style="width: 20%">QUANTITY</td>
                    <td class="text-center" style="width: 20%">GUDANG</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.QNTSAT) {
          grandTotal += parseFloat(item.QNTSAT) || 0;
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
               style="width: 30%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: left"
               style="width: 30%;">${itemSub.KODEBRG}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.SATuan}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNTSAT ? parseFloat(itemSub.QNTSAT).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.namaGDG}</td>
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
          </div>
         
         </div>

           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%"></td>
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

function buttonOtorisasi (nobukti, isOtorisasi) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  if (Number(isOtorisasi) > 0) {
    alertify.warning('Sudah diotorisasi');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasiupdateotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      otorisasi: 1
    },
    success: function (res) {
      if (res > 0) {
        alertify.success('Berhasil otorisasi');
        loadAll();
      } else {
        alertify.warning('Gagal otorisasi');
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
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
        url: "{!! url('returpenyerahankonsinyasiupdatebatalotorisasi') !!}",
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

function buttonAddListSerahSample () {
  console.log('buttonAddListSerahSample');

  const sales = $('#input_sales').val(); 
  const nobukti = $('#input_add_add_serahsample').val().trim();
  const urut = $('#input_urut_ref').val().trim();

  if (!sales) {
    alertify.warning("Silakan pilih Sales terlebih dahulu");
    return;
  }

  // Kosongkan field barang
  $('#input_add_add_kodebarang').val('');
  $('#input_add_add_keterangannama').val('');
  $('#input_add_add_satuan').html(''); 
  $('#input_urut_ref').val(''); 
  $('#tabel_data_add_list_serahsample').html('');

  if ($.fn.DataTable.isDataTable('#tabel_add_list_serahsample')) {
    $('#tabel_add_list_serahsample').DataTable().clear().destroy();
  }

  // exclude 
  let excludeToSend = excludeSerahSamples.filter(e => !(e.nobukti === nobukti && e.urut === urut));

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasinokonsinyasi') }}",
    type: "get",
    data: {
      sales: sales,
      nobukti: nobukti,
      exclude: JSON.stringify(excludeToSend) 
    },
    success: function(res) {
      console.log('Data Sample:', res);

      let rowTable = "";
      if (res && res.length > 0) {
        res.forEach((item) => {
          rowTable += `
            <tr>
              <td class="text-center">
                <button class="btn btn-primary btn-sm" type="button"
                  onclick="buttonPickSerahSample('${item.NOBUKTI}', '${item.Urut}', '${item.Kodebrg}', '${item.GdgAsal}', '${item.NamaGgdAsal ?? ''}')">
                  <i class="bi bi-plus"></i>
                </button>
              </td>
              <td>${item.NOBUKTI}</td>
              <td>${item.TANGGAL ? item.TANGGAL.substring(0, 10) : '-'}</td>
              <td>${item.KODECUSTSUPP ?? ''}</td>
              <td>${item.NAMACUSTSUPP ?? ''}</td>
              <td>${item.Kodebrg ?? ''}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.QntSisa}</td>
              <td>${item.GdgAsal}</td>
            </tr>`;
        });
      }

      $('#tabel_data_add_list_serahsample').html(rowTable);

      $('#tabel_add_list_serahsample').DataTable({
        "lengthChange": false,
        "paging": false,
      });

      $('#modalAddListSerahSample').modal('show');
    },
    error: function(err) {
      console.error("AJAX Error:", err);
      alertify.warning('Terjadi kesalahan saat mengambil data.');
    }
  });
}

function buttonPickSerahSample (nobukti, urut, kodebrg, gdgasal, namagudang) {
  console.log('Data dipilih:', nobukti, urut, kodebrg, gdgasal, namagudang);

  // Isi data header
  $('#input_add_add_serahsample').val(nobukti);
  $('#input_urut_ref').val(urut);
  $('#input_add_add_gudang').val(gdgasal);
  $('#input_add_add_namagudang').val(namagudang);
  $('#input_add_add_qnt').val('0.00');
  $('#input_add_add_kodebarang').val('');
  $('#input_add_add_keterangannama').val('');
  $('#input_add_add_satuan').html('');

  $('#modalAddListSerahSample').modal('hide');

  loadBarangSerahSample(nobukti, urut, kodebrg);
}

function loadBarangSerahSample (nobukti, urut, kodebrg) {
  console.log("Ambil barang untuk:", nobukti, urut, kodebrg);

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasilistbarang') }}",
    type: "get",
    data: { nobukti: nobukti, urut: urut, kodebrg: kodebrg },
    success: function(res) {
      console.log("Data Barang:", res);

      dataAddListItem = res;

      if (res && res.length > 0) {
        const item = res[0]; 

        $("#input_add_add_kodebarang").val(item.Kodebrg);
        $("#input_add_add_keterangannama").val(item.NamaBrg);

        // Satuan
        let satuanOptions = "";
        if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1}</option>`;
        if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2}</option>`;
        if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3}</option>`;
        $("#input_add_add_satuan").html(satuanOptions);

        // isi QNT otomatis dengan sisa
        const qntSisa = item.QntSisa ?? item.Qnt ?? 0;
        $("#input_add_add_qnt").val(parseFloat(qntSisa).toFixed(2))
          .attr("max", qntSisa)
          .attr("min", 0);

        setTimeout(() => {
          const qntInput = document.getElementById("input_add_add_qnt");
          qntInput.focus();
          qntInput.select();
        }, 300);
      } else {
        alertify.warning("Barang tidak ditemukan untuk No Serah Sample ini");
      }
    },
    error: function(err) {
      console.error("AJAX Error:", err);
      alertify.warning("Terjadi kesalahan saat mengambil data barang.");
    }
  });
}

function buttonAddListSales () {
  console.log('buttonAddListSales');
  $('#tabel_add_list_sales').DataTable().destroy();

  $.ajax({
    url: "{{ url('returpenyerahankonsinyasilistsales') }}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res);

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-primary btn-sm" type="button"
                onclick="buttonAddPickSales('${item.namaSls}', '${item.KodeSls}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.NIK}</td>
            <td>${item.namaSls}</td>
          </tr>`;
      });

      if (!res.length) {
        rowTable = `<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>`;
      }

      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable;
      $("#tabel_add_list_sales").DataTable({
        "lengthChange": false,
        "paging": false,
        "order": [[2, "asc"]],
        "columnDefs": [{ targets: [0], orderable: false }],
      });

      $('#modalAddListSales').modal('show');
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}

function buttonAddPickSales (nama, kode) {
  $('#input_sales_nama').val(nama);
  $('#input_sales').val(kode);     
  $('#modalAddListSales').modal('hide');

  // Reset 
  $('#input_add_add_serahsample').val('');   
  $('#input_add_add_kodebarang').val('');     
  $('#input_add_add_keterangannama').val('');
  $('#input_add_add_satuan').html('');
  $('#input_urut_ref').val('');
  $('#input_add_add_gudang').val('');
  $('#input_add_add_namagudang').val('');
  $('#input_add_add_qnt').val('0.00');

  $('#tabel_data_add_list_serahsample').html('');
  if ($.fn.DataTable.isDataTable('#tabel_add_list_serahsample')) {
    $('#tabel_add_list_serahsample').DataTable().clear().destroy();
  }

  console.log("Sales dipilih/ganti, semua data serah sample & barang direset.");
}

function buttonAddListGudang () {
  console.log('buttonAddListGudang');
  $('#tabel_add_list_gudang').DataTable().destroy();

  $.ajax({
    url: "{{ url('permintaankonsinyasilistgudang') }}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res);

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-primary btn-sm" type="button"
                onclick="buttonAddPickGudang('${item.NamaGdg}', '${item.KodeGdg}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.KodeGdg}</td>
            <td>${item.NamaGdg}</td>
          </tr>`;
      });

      if (!res.length) {
        rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
      }

      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable;
      $("#tabel_add_list_gudang").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      $('#modalAddListGudang').modal('show');
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
    }
  });
}

function buttonAddPickGudang (nama, kode) {
  $('#input_gudang_nama').val(nama);
  $('#input_gudang').val(kode);
  $('#modalAddListGudang').modal('hide');
}


function buttonAddListBatal() {
  $('#modalAddListSerahSample').modal('hide');
  $('#modalAddListSales').modal('hide');
  $('#modalAddListGudang').modal('hide');
}

// function closeListItemAdd () {
//   $("#formAddListItem").modal('toggle')
//   // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
//   // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
//   var modal = document.getElementById("page2");
//   modal.style.display = "block";
// }

function submitAddEdit () {
  console.log('submitAddEdit');

  let checkDate = new Date($("#input_add_tanggal").val());
  let periode_bulan = document.getElementById("periode_bulan").value;
  let periode_tahun = document.getElementById("periode_tahun").value;

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
    alertify.warning("Tanggal tidak sesuai periode");
    return;
  }

  let jmlrecord = (tipeform === "edit") ? 1 : 0;

  let _token = $("#_token").val();
  let choice = "U";
  let nobukti = $("#input_add_nobukti").val();
  let tanggal = $("#input_add_tanggal").val();
  let sales = $("#input_sales").val();
  let namasls = $("#input_sales_nama").val();
  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let satuan = $("#input_add_add_satuan").val();
  let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
  let noserahsample = $("#input_add_add_serahsample").val();
  let urutserahsample = $("#input_urut_ref").val();
  let gudang = $("#input_add_add_gudang").val();
  let namagudang = $("#input_add_add_namagudang").val();
  let keterangan = $("#input_keterangan").val() || '';

  if (!kodebarang || !satuan || qnt <= 0) {
    alertify.warning("Lengkapi semua data wajib");
    return;
  }

  let barang = tempEdit;
  console.log("tempEdit:", tempEdit);

  // ================= QNT SISA ===================
  let qntSisaStored = parseFloat(barang.QntSisa ?? 0) || 0;
  let qntLama = parseFloat(barang.QNT ?? barang.Qnt ?? 0) || 0;

  // Total QNT max 
  let qntMax = qntLama + qntSisaStored;

  console.log(`Mode: ${tipeform} | Qty Lama: ${qntLama} | Qty Sisa: ${qntSisaStored} | Qty Maks: ${qntMax}`);

  if (qnt > qntMax) {
    alertify.warning(`Qty tidak boleh lebih besar dari Qty maksimal (${qntMax})`);
    return;
  }

  if (qnt <= 0) {
    alertify.warning("Qty tidak boleh nol atau negatif");
    return;
  }
  // ===============================================

  let isi = 0;
  let nosat = parseInt(satuan);
  let qnt1 = 0;
  let sat_1 = '';
  let sat_2 = '';

  barang.ISI1 = parseFloat(barang.ISI1) || 1;
  barang.ISI2 = parseFloat(barang.ISI2) || 1;
  barang.ISI3 = parseFloat(barang.ISI3) || 1;

  if (!barang.SAT1 || !barang.SAT2) {
    alertify.warning("Data satuan tidak lengkap, silakan refresh atau pilih ulang barang");
    return;
  }

  if (nosat === 1) {
    qnt1 = qnt * barang.ISI1;
    satuan = barang.SAT1;
    isi = barang.ISI1;
  } else if (nosat === 2) {
    qnt1 = qnt * barang.ISI2;
    satuan = barang.SAT2;
    isi = barang.ISI2;
  } else if (nosat === 3) {
    qnt1 = qnt * barang.ISI3;
    satuan = barang.SAT3;
    isi = barang.ISI3;
  } else {
    alertify.warning("Satuan tidak valid");
    return;
  }

  sat_1 = barang.SAT1 ?? satuan ?? 'PCS';
  sat_2 = barang.SAT2 ?? satuan ?? 'PCS';

  keterangannama = keterangannama.replace(/["']/g, '');
  keterangan = keterangan.replace(/["']/g, '');

  let nourut = parseInt(barang.NOURUT);
  let urut = parseInt(barang.URUT);

  console.log("URUT yg dikirim:", urut);
  console.log("QNT yg dikirim:", qnt);
  console.log("SAT1 dikirim:", sat_1);
  console.log("SAT2 dikirim:", sat_2);
  console.log("NOSERAH:", noserahsample);
  console.log("URUTSERAH:", urutserahsample);
  console.log("KODEGUDANG:", gudang);
  console.log("NAMAGUDANG:", namagudang);

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispadd') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      urut,
      kodebarang,
      gdgasal: gudang,
      namagdgasal: namagudang,
      gdgtujuan: '',
      sat_1,
      sat_2,
      qnt,
      qnt2: qnt,
      nosat,
      isi,
      kodesls: sales,
      namasls: namasls,
      pbonus: 0,
      maxol: 0,
      pkonsi: 0,
      noserahsample,
      urutserahsample,
      jmlrecord
    },
    success: function (res) {
      console.log('respoedit', res);
      loadAll();
      $('.showhide').hide();
      refreshDataTableAdd(nobukti);
      alertify.success('Berhasil edit item');
    },
    error: function (err) {
      console.log('Error saat submit:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonAddEditItem (index) {
  tipeformitem = 'edit';
  let _token = $("#_token").val();
  console.log('buttonAddEditItem');

  $('.showhide').hide();
  // document.getElementById("buttonAddListKodeBarang").disabled = true;
  document.getElementById("buttonAddListSerahSample").disabled = true;

  tempEdit = dataTableAdd[index];
  tempIndexEdit = index;

  tempEdit.SAT1 = tempEdit.SAT_1;
  tempEdit.SAT2 = tempEdit.SAT_2;
  tempEdit.SAT3 = tempEdit.SAT_3;
  tempEdit.ISI1 = tempEdit.ISI1;
  tempEdit.ISI2 = tempEdit.ISI2;
  tempEdit.ISI3 = tempEdit.ISI3;

  // Isi dropdown satuan
  let selectOption = '<option value=0 selected>Pilih Satuan</option>';
  if (tempEdit.SAT_1) {
    selectOption += `<option value=1>${tempEdit.SAT_1}</option>`;
  }
  if (tempEdit.SAT_2) {
    selectOption += `<option value=2>${tempEdit.SAT_2}</option>`;
  }
  if (tempEdit.SAT_3) {
    selectOption += `<option value=3>${tempEdit.SAT_3}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = selectOption;

  // Isi input
  document.getElementById("input_add_add_kodebarang").value = tempEdit.KODEBRG;
  document.getElementById("input_add_add_keterangannama").value = tempEdit.NamaBrg;
  document.getElementById("input_add_add_qnt").value = parseFloat(tempEdit.QNT).toFixed(2);
  document.getElementById("input_add_add_satuan").value = String(tempEdit.NoSat);
  document.getElementById("input_add_add_serahsample").value = tempEdit.Noserahsample;
  document.getElementById("input_urut_ref").value = tempEdit.UrutSerahSample;
  document.getElementById("input_add_add_gudang").value = tempEdit.gdgAsal;
  document.getElementById("input_add_add_namagudang").value = tempEdit.NamaGgdAsal;

  // Tampilkan mode edit
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_kodebarang").scrollIntoView();
}

function buttonEdit (nobukti, isOtorisasi1) {
  tipeform = 'edit';

  let akses = $("#akses_iskoreksi").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispdetail') !!}",
    type: "get",
    async: false,
    data: { nobukti },
    success: function (res) {
      if (!res || !res.length) {
        alertify.error("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (isOtorisasi1 == 1 || data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah Di Otorisasi");
        return;
      }

      $('.showhide').hide();
      cleanFormAdd();
      lockFormAdd();

      dataTableAdd = res;
      dataHeaderAdd = data;

      // Format tanggal
      let date = new Date(data.TANGGAL);
      let dateFormatted = data.TANGGAL?.substring(0, 10) ?? '';

      // Isi form header
      $('#input_add_tanggal').val(dateFormatted);
      $('#input_add_nobukti').val(data.NOBUKTI);
      $('#input_add_nourut').val(data.Nourut);
      $('#input_sales_nama').val(data.NAMASLS);
      $('#input_sales').val(data.KODESLS);

      // Isi tabel item
      let rowTable = "";
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal}</td>
            <td>${parseFloat(item.QNT).toLocaleString()}</td>
            <td>${item.SAT_1}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})" title="Edit"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})" title="Hapus"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`;
      });

      $("#tabel_data_add").html(rowTable);

      $('#page1').hide();
      $('#page3').hide();
      $('#page2').show();
    },
    error: function (err) {
      console.error("Error saat load detail:", err);
      alertify.error("Gagal load detail");
    }
  });
}

function buttonAddDeleteItem (index) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  const data = dataTableAdd[index];

  alertify.confirm('Hapus Item', `Apakah yakin ingin menghapus item ${data.KODEBRG}?`, function () {
    const _token = $("#_token").val();
    const choice = "D";
    const nobukti = $("#input_add_nobukti").val();
    const tanggal = $("#input_add_tanggal").val();
    const nourut = $("#input_add_nourut").val();
    const kodebarang = data.KODEBRG;
    const urut = data.URUT;
    const qnt = parseFloat(data.QNT);
    const qnt2 = parseFloat(data.QNT2 ?? data.QNT);
    const nosat = parseInt(data.NoSat);
    const isi = parseFloat(data.ISI);
    const sat_1 = data.SAT_1 ?? data.satuan;
    const sat_2 = data.SAT_2 ?? data.satuan;
    const note = data.NamaBrg;
    const gdgasal = data.gdgAsal ?? '';
    const gdgtujuan = data.gdgTujuan ?? '';
    const kodesls = $("#input_sales").val();
    const maxol = parseInt($("#input_maxol").val() || 0);
    const pbonus = parseInt(data.pbonus ?? 0);
    const noserahsample = data.NoserahSample ?? '';
    const urutserahsample = parseInt(data.UrutSerahSample ?? 0);
    const pkonsi = parseInt($("#input_pkonsi").val() || 0);
    const jmlrecord = 0;
    const kodecustsupp = data.KODECUSTSUPP ?? '';

    $.ajax({
      url: "{!! url('returpenyerahankonsinyasispdelete') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal,
        note,
        urut,
        kodebarang,
        gdgasal,
        gdgtujuan,
        sat_1,
        sat_2,
        qnt,
        qnt2,
        nosat,
        isi,
        kodecustsupp, 
        kodesls,
        pbonus,
        maxol,
        noserahsample,
        urutserahsample,
        pkonsi,
        jmlrecord
      },
      success: function (res) {
        alertify.success("Item sudah dihapus");

        refreshDataTableAdd(nobukti);

        if (!dataTableAdd || dataTableAdd.length === 0) {
          $("#btn_customer").prop("disabled", false);
          $("#btn_gudang").prop("disabled", false);
          tipeform = 'new';
        }

        loadAll();
      },
      error: function (err) {
        console.log(err);
        alertify.error("Gagal menghapus item");
      }
    });
  }, function () {
    console.log('Batal Menghapus data');
  });
}

function buttonDetail (nobukti) {
  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispdetail') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `<tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal}</td>
            <td>${parseFloat(item.QNT).toLocaleString()}</td>
            <td>${item.SAT_1}</td>
        </tr>`
      });

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NOBUKTI
      document.getElementById("input_detail_sales_nama").value  = res[0].NAMASLS


  }})
  $("#page3").show();
  $("#page1").hide();
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut
    }})
}

function buttonAdd () {
  tipeform = 'add'
  $('.showhide').hide();
  cleanFormAdd()
  unlockFormAdd();

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []
    cleanFormAdd()

  refreshDataTableAdd()
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function closeListItemAdd () {
  $("#formAddListItem").modal('toggle')
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("page2");
  modal.style.display = "block";
}

function buttonCloseForm () {
  $('#page2').hide();
  $('#page3').hide();
  $('#page1').show();
}

function buttonAddListKodeBarang () {
  console.log("buttonAddListKodeBarang");

  const serahsample = $("#input_add_add_serahsample").val().trim();

  if (!serahsample) {
    alertify.warning("Silakan pilih Serah Sample terlebih dahulu.");
    return;
  }

  // Kosongkan body tabel
  $("#tabel_data_add_list_item").html("");

  if ($.fn.DataTable.isDataTable("#tabel_add_list_item")) {
    $("#tabel_add_list_item").DataTable().clear().destroy();
  }

  $.ajax({
    url: "{{ url('returpenyerahansamplelistbarang') }}",
    type: "get",
    data: {
      nobukti: serahsample,
      search: "" 
    },
    success: function (res) {
      console.log("Data Barang:", res);

      let rowTable = "";

      if (res && res.length > 0) {
        res.forEach((item, i) => {
          rowTable += `
            <tr>
              <td class="text-center">
                <button class="btn btn-primary btn-sm" type="button"
                  onclick="buttonPickBarang(${i})">
                  <i class="bi bi-plus"></i>
                </button>
              </td>
              <td>${item.Kodebrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.QntSisa ?? ""}</td>
            </tr>`;
        });
      }

      $("#tabel_data_add_list_item").html(rowTable);

      // Inisialisasi DataTable
      $("#tabel_add_list_item").DataTable({
        "lengthChange": false,
        "paging": false,
        "searching": false,
      });

      dataAddListItem = res;

      $("#formAddListItem").modal("show");
      $("#input_search_barang_all").val("").focus();
    },
    error: function (err) {
      console.error("AJAX Error:", err);
      alertify.warning("Terjadi kesalahan saat mengambil data.");
    }
  });
}

function buttonPickBarang (index) {
  if (!dataAddListItem[index]) {
    alertify.warning("Data barang tidak valid");
    return;
  }

  const item = dataAddListItem[index];
  console.log("Barang dipilih:", item);

  $("#input_add_add_kodebarang").val(item.Kodebrg);
  $("#input_add_add_keterangannama").val(item.NamaBrg);

  // Reset Serah Sample & Gudang
  $("#input_add_add_serahsample").val(item.NOBUKTI);
  $("#input_urut_ref").val(item.Urut);
  $("#input_add_add_gudang").val(item.GdgAsal);
  $("#input_add_add_namagudang").val(item.NamaGdgAsal ?? "");

  // Satuan
  let satuanOptions = "";
  if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1}</option>`;
  if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2}</option>`;
  if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3}</option>`;
  $("#input_add_add_satuan").html(satuanOptions);

  // isi QNT otomatis dengan sisa
  const qntSisa = item.QntSisa ?? item.Qnt ?? 0;
  $("#input_add_add_qnt")
    .val(parseFloat(qntSisa).toFixed(2)) 
    .attr("max", qntSisa)   
    .attr("min", 0);     

  $("#formAddListItem").modal("hide");

  setTimeout(() => {
    const qntInput = document.getElementById("input_add_add_qnt");
    qntInput.focus();
    qntInput.select();
  }, 300);
}

function searchBarangAll (e) {
  if (e.which == 13) {
    console.log("Enter ditekan");

    const search = $("#input_search_barang_all").val().trim();
    const serahsample = $("#input_add_add_serahsample").val().trim();

    if (!serahsample) {
      alertify.warning("Silakan pilih Serah Sample terlebih dahulu.");
      return;
    }

    if ($.fn.DataTable.isDataTable("#tabel_add_list_item")) {
      $("#tabel_add_list_item").DataTable().clear().destroy();
    }

    $("#tabel_data_add_list_item").html(`
      <tr><td class="text-center" colspan="5">Loading data...</td></tr>`);

    $.ajax({
      url: "{{ url('returpenyerahankonsinyasilistbarang') }}",
      type: "get",
      data: {
        nobukti: serahsample,
        search: search
      },
      success: function (res) {
        console.log("Hasil search barang:", res);

        let rowTable = "";

        if (res && res.length > 0) {
          res.forEach((item, i) => {
            rowTable += `
              <tr>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" type="button"
                    onclick="buttonPickBarang(${i})">
                    <i class="bi bi-plus"></i>
                  </button>
                </td>
                <td>${item.Kodebrg}</td>
                <td>${item.NamaBrg}</td>
                <td>${item.QntSisa ?? ""}</td>
              </tr>`;
          });
        }

        $("#tabel_data_add_list_item").html(rowTable);

        $("#tabel_add_list_item").DataTable({
          "lengthChange": false,
          "paging": false,
          "searching": false,
        });

        dataAddListItem = res;
      },
      error: function (err) {
        console.error("AJAX Error:", err);
        alertify.warning("Terjadi kesalahan saat mengambil data.");
      }
    });
  }
}

function buttonAddAddInsertItem (i) {
  console.log('index:', i);
  console.log('dataAddListItem:', dataAddListItem);

  // Cek jika data valid
  if (!dataAddListItem[i]) {
    alertify.warning('Data tidak valid');
    return;
  }

  // Ambil item yang dipilih dari dataAddListItem
  let item = dataAddListItem[i];
  
  // Masukkan data ke dalam form
  $('#input_add_add_kodebarang').val(item.Kodebrg);
  $('#input_add_add_keterangannama').val(item.NamaBrg);

  // Menampilkan satuan 
  let satuanOptions = '';
  if (item.SAT1) satuanOptions += `<option value="${item.SAT1}">${item.SAT1}</option>`;
  if (item.SAT2) satuanOptions += `<option value="${item.SAT2}">${item.SAT2}</option>`;
  if (item.SAT3) satuanOptions += `<option value="${item.SAT3}">${item.SAT3}</option>`;
  $('#input_add_add_satuan').html(satuanOptions);

  $('#formAddListItem').modal('hide');

  setTimeout(() => {
    document.getElementById("input_add_add_qnt").focus();
    document.getElementById("input_add_add_qnt").select();
  }, 300);
  // (Opsional) Reset nilai lainnya jika perlu
  // $('#input_add_add_qnt').val(0.00);
  // $('#input_add_add_keterangan').val('');
}

function submitAddAdd () {
  console.log('submitAddAdd');

  let checkDate = new Date($("#input_add_tanggal").val());
  let periode_bulan = document.getElementById("periode_bulan").value;
  let periode_tahun = document.getElementById("periode_tahun").value;

  if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() + 1) !== Number(periode_bulan)) {
    alertify.warning("Tanggal tidak sesuai periode");
    return;
  }

  let jmlrecord = (tipeform === 'edit') ? 1 : 0;
  let _token = $("#_token").val();
  let choice = "I";
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let sales = $("#input_sales").val();
  let namasls = $("#input_sales_nama").val();
  let gudang = $("#input_add_add_gudang").val();
  let namagudang = $("#input_add_add_namagudang").val();
  let tanggal = $("#input_add_tanggal").val();
  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let satuan = $("#input_add_add_satuan").val();
  let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
  let noserahsample = $("#input_add_add_serahsample").val();
  let urutserahsample = $("#input_urut_ref").val();
  let keterangan = "";

  if (!sales || !kodebarang || !satuan || qnt <= 0 || !noserahsample) {
    alertify.warning("Lengkapi semua data wajib");
    return;
  }

  let barang = dataAddListItem.find(item => item.Kodebrg === kodebarang);
  if (!barang) {
    alertify.warning("Barang tidak ditemukan di daftar");
    return;
  }

  let qntSisa = barang.QntSisa ?? barang.Qnt ?? 0;
  if (qnt > qntSisa) {
    alertify.warning("Qty tidak boleh lebih besar dari Qty sisa (" + qntSisa + ")");
    return;
  }
  if (qnt <= 0) {
    alertify.warning("Qty tidak boleh nol atau negatif");
    return;
  }

  let nosat = 0;
  let isi = 0;
  if (satuan === barang.SAT1) {
    nosat = 1;
    isi = barang.ISI1;
  } else if (satuan === barang.SAT2) {
    nosat = 2;
    isi = barang.ISI2;
  } else if (satuan === barang.SAT3) {
    nosat = 3;
    isi = barang.ISI3;
  } else {
    alertify.warning("Satuan tidak valid");
    return;
  }

  let sat_1 = satuan;
  let sat_2 = satuan;
  let qnt2 = qnt;

  keterangannama = keterangannama.replace(/["']/g, '');
  keterangan = keterangan.replace(/["']/g, '');

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispadd') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      urut: 0,
      kodebarang,
      gdgasal: gudang,
      namagdgasal: namagudang,
      gdgtujuan: '',
      sat_1,
      sat_2,
      qnt,
      qnt2,
      nosat,
      isi,
      kodesls: sales,
      namasls: namasls,
      pbonus: 0,
      maxol: 0,
      pkonsi: 0,
      noserahsample,
      urutserahsample,
      jmlrecord
    },
    success: function (res) {
      console.log('respoadd', res);
      if (res == 1) {
        tipeform = 'edit';
        loadAll();
        cleanFormAddAdd();
        refreshDataTableAdd(nobukti);
        alertify.success('Berhasil menambah item');
        document.getElementById("input_add_tanggal").disabled = true;
        document.getElementById("btn_sales").disabled = true;

        const nobuktiSerah = $('#input_add_add_serahsample').val();
        const urutSerah = $('#input_urut_ref').val();

        if (nobuktiSerah && urutSerah) {
          excludeSerahSamples.push({ nobukti: nobuktiSerah, urut: urutSerah });
          console.log("Exclude updated:", excludeSerahSamples);

          const addedQnt = qnt;
          for (let i = 0; i < dataAddListItem.length; i++) {
            const it = dataAddListItem[i];
            if (
              String(it.NOBUKTI) === String(nobuktiSerah) &&
              String(it.Urut) === String(urutSerah) &&
              String(it.Kodebrg) === String(kodebarang)
            ) {
              const oldSisa = parseFloat(it.QntSisa ?? it.Qnt ?? 0) || 0;
              let newSisa = oldSisa - addedQnt;
              if (newSisa < 0) newSisa = 0;

              it.QntSisa = newSisa;
              it.Qnt = newSisa;

              // Update tampilan tabel modal kalau masih terbuka
              $("#tabel_data_add_list_item tr").each(function () {
                const kodeCell = $(this).find("td").eq(1).text().trim();
                if (kodeCell === String(kodebarang)) {
                  $(this).find("td").eq(3).text(newSisa.toFixed(2));
                }
              });
              break;
            }
          }
        }

      } else if (res == 2) {
        setNewNoBukti();
        alertify.warning('Nobukti telah direfresh silahkan submit ulang');
      }
    }
  });
}

function buttonEditAddItem () {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhideedit').hide();

  tempEditAdd = {}
  document.getElementById("input_edit_add_refso").value = "-"
  document.getElementById("input_edit_add_nopocust").value = ""
  document.getElementById("input_edit_add_kodebarang").value = ""
  document.getElementById("input_edit_add_keterangannama").value = ""
  document.getElementById("input_edit_add_qnt").value = "0.00"
  document.getElementById("input_edit_add_keterangan").value = ""
  document.getElementById("input_edit_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'

  $('#editAddItem').show();
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();
  tempAdd = {}
  // document.getElementById("inlineRadio1").checked = false
  // document.getElementById("input_add_add_refso").value = "-"
  // document.getElementById("input_add_add_nopocust").value = ""
  // document.getElementById("buttonAddListKodeBarang").disabled = false;
  document.getElementById("buttonAddListSerahSample").disabled = false;
  document.getElementById("input_add_add_satuan").disabled = false;
  document.getElementById("input_add_add_kodebarang").value = ""
  document.getElementById("input_add_add_serahsample").value = ""
  document.getElementById("input_add_add_gudang").value = ""
  document.getElementById("input_add_add_namagudang").value = ""
  document.getElementById("input_urut_ref").value = ""
  document.getElementById("input_add_add_keterangannama").value = ""
  document.getElementById("input_add_add_qnt").value = "0.00"
  // Menentukan isi dropdown berdasarkan tempAdd
  let satuanOptions = `<option value="" selected disabled>Pilih Satuan</option>`;
  
  if (tempAdd.SAT1) {
    satuanOptions += `<option value="${tempAdd.SAT1}">[1] ${tempAdd.SAT1}</option>`;
  }
  if (tempAdd.SAT2) {
    satuanOptions += `<option value="${tempAdd.SAT2}">[2] ${tempAdd.SAT2}</option>`;
  }
  if (tempAdd.SAT3) {
    satuanOptions += `<option value="${tempAdd.SAT3}">[3] ${tempAdd.SAT3}</option>`;
  }
  document.getElementById("input_add_add_satuan").innerHTML = satuanOptions;


  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
}

function closeShowHideAdd () {
  $('.showhide').hide();
}

function closeShowHideEdit () {
  $('.showhideedit').hide();
}


function refreshDataTableAdd (NOBUKTI = "") {
  console.log('refreshDataTableAdd', NOBUKTI);

  let _token = $("#_token").val();

  if (!NOBUKTI) {
    document.getElementById("tabel_data_add").innerHTML = `
      <tr>
        <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`;
    
    tipeform = "new";
    return;
  }

  $.ajax({
    url: "{!! url('returpenyerahankonsinyasispdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function (res) {
      console.log('res', res);

      if (!res.length) {
        alertify.warning("Data habis");
        document.getElementById("tabel_data_add").innerHTML = `
          <tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
          </tr>`;

        $('#page3').hide();
        $('#page2').hide();
        $('#page1').show();

        tipeform = "new";
        return;
      }

      dataTableAdd = res;
      dataHeaderAdd = res[0];

      let rowTable = "";
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal}</td>
            <td>${parseFloat(item.QNT).toLocaleString()}</td>
            <td>${item.SAT_1}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})" title="Edit"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})" title="Hapus"><i class="bi bi-trash"></i></button>
            </td>
          </tr>`;
      });

      document.getElementById("tabel_data_add").innerHTML = rowTable;
    },
    error: function (err) {
      console.error("Error fetching detail:", err);
      alertify.error("Gagal memuat data");
    }
  });
}


function cleanFormAddAdd (){
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_keterangannama").value = ''
  document.getElementById("input_urut_ref").value = ''
  document.getElementById("input_add_add_gudang").value = ''
  document.getElementById("input_add_add_serahsample").value = ''
  document.getElementById("input_add_add_namagudang").value = ''
  document.getElementById("input_add_add_qnt").value = '0.00'
  document.getElementById("input_add_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
}

function cleanFormAdd (){
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_sales").value = ''
  document.getElementById("input_sales_nama").value = ''
}

function lockFormAdd (){
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("btn_sales").disabled = true
}

function unlockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("btn_sales").disabled = false
}
</script>

{{-- script buat hover belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(homeActive) {
      if (homeActive) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';
      } else {
        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';
      }
    }

    // Default warna tab
    setActiveTab(true);

    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(true);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(false);
    });
  </script>


@endsection
