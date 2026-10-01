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

{{-- tampilan search bar 3 --}}
  <style>
  #tabel_oto_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabel_oto_filter label input {
      width: 150px;
      padding: 5px 10px; 
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem; 
    }

  #tabel_oto_filter label {
      font-weight: 600; 
      font-size: 0.9rem; 
      color: #333;
    }

  #tabel_oto_filter input:focus {
      border-color: #007bff; 
      outline: none; 
    }
  </style>
{{-- end tampilan search bar 3 --}}

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

{{-- tampilan search customer --}}
  <style>
    #tabel_add_list_customer_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_customer_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search customer --}}

{{-- tampilan search gudang --}}
  <style>
    #tabel_add_list_gudang_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_gudang_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search gudang --}}
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id='page1' class="container-fluid">
  <!-- <div id="qrcode"></div> -->
  <div class="">
  <div class="row" style="margin-top:-85px;">
    <div class="col-6 text-left">
      <h2>Permintaan Sample</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="
      height: 30px; 
      margin-top: 10px; 
      padding: 4px 12px;
      border-radius: 20px; 
      font-size: 0.75rem; 
      font-weight: 600; 
      text-transform: uppercase; 
      transition: background-color 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
      onclick="buttonAdd()">Add Permintaan</button>
    </div>

{{-- <button onclick="loadAll()">tes</button> --}}
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
            PRS Belum Otorisasi
          </a>
          <a class="nav-item nav-link" id="nav-home2-tab" data-toggle="tab" href="#home2" role="tab"
             aria-controls="nav-home2" aria-selected="false"
             style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
            PRS Sudah Otorisasi
          </a>
          <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
             aria-controls="nav-profile" aria-selected="false"
             style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
            OutStanding
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
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                      <th style="padding: 4px 12px;" scope="col">Customer</th>
                      <th style="padding: 4px 12px;" scope="col">Sales</th>
                      <th style="padding: 4px 12px;" scope="col">Ref PR</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data" class="text-left">
                    @foreach ($listData as $group)
                    @php $item = $group->first(); @endphp
                    <tr>
                      <td class="text-center">
                      <div class="d-flex justify-content-center align-items-center gap-2">
                          <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('{{ $item->NOBUKTI }}')">
                              <i class="bi bi-info"></i>
                          </button>
                          <button class="btn btn-success btn-sm p-1 px-2" title="Edit" onclick="buttonEdit('{{ $item->NOBUKTI }}')">
                              <i class="bi bi-pen"></i>
                          </button> 
                          @if($item->IsOtorisasi1 == 0)
                          <button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('{{ $item->NOBUKTI }}')">
                            <i class="bi bi-key"></i>
                          </button>
                          @else
                          <button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $item->NOBUKTI }}')">
                            <i class="bi bi-key"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                    <td>{{ $item->NOBUKTI }}</td>
                    <td>{{ date("Y/m/d", strtotime($item->TANGGAL)) }}</td>
                    <td>{{ $item->Keterangan}}</td>
                    <td>{{ $item->NamaCustSupp }}</td>
                    <td>{{ $item->NAMASLS }}</td>
                    <td>{{ $item->RefPR ?? ''}}</td>
                    </td>
                    </tr>
                @endforeach
                </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        {{-- Tab Sudah Oto --}}
        <div class="tab-pane fade" id="home2" role="tabpanel" aria-labelledby="home2-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">
                <table id="tabel_oto" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                      <th style="padding: 4px 12px;" scope="col">Customer</th>
                      <th style="padding: 4px 12px;" scope="col">Sales</th>
                      <th style="padding: 4px 12px;" scope="col">Ref PR</th>
                      <th style="padding: 4px 12px;" scope="col">User Oto</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal Oto</th> 
                    </tr>
                  </thead>
                  <tbody id="tabel_oto_data" class="text-left">
                    @foreach ($listData3 as $group)
                    @php $item = $group->first(); @endphp
                    <tr>
                      <td class="text-center">
                      <div class="d-flex justify-content-center align-items-center gap-2">
                          <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('{{ $item->NOBUKTI }}')">
                              <i class="bi bi-info"></i>
                          </button>
                          @if($item->IsOtorisasi1 == 0)
                          <button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('{{ $item->NOBUKTI }}')">
                            <i class="bi bi-key"></i>
                          </button>
                          @else
                          <button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('{{ $item->NOBUKTI }}')">
                            <i class="bi bi-key"></i>
                          </button>
                        @endif
			<button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{ $item->NOBUKTI }}')" ><i class="bi bi-printer"></i>
                        </button>
                      </div>
                    </td>
                    <td>{{ $item->NOBUKTI }}</td>
                    <td>{{ date("Y/m/d", strtotime($item->TANGGAL)) }}</td>
                    <td>{{ $item->Keterangan}}</td>
                    <td>{{ $item->NamaCustSupp }}</td>
                    <td>{{ $item->NAMASLS }}</td>
                    <td>{{ $item->RefPR ?? ''}}</td>
                    {{-- <td class="text-center">
                        @if ($item->IsOtorisasi1 == 0)
                        <span class="text-danger"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></span>
                        @else
                        <span class="text-success"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></span>
                        @endif
                    </td> --}}
                    <td>{{ $item->OtoUser1 }}</td>
                    <td>{{ $item->TglOto1 ? date("Y/m/d", strtotime($item->TglOto1)) : '' }}</td>
                    </td>
                    </tr>
                @endforeach
                </tbody> 
                </table>
              </div>
            </div>
          </div>
        </div>
        {{-- OutStanding --}}
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <table id="tabel2" class="table table-bordered table-striped" style="margin:0;">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">Kode CustSupp</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Customer</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Sales</th>
                    <th style="padding: 4px 12px;" scope="col">Qnt</th>
                    <th style="padding: 4px 12px;" scope="col">Sat</th>
                    <th style="padding: 4px 12px;" scope="col">QNTSSKONSI</th>
                    <th style="padding: 4px 12px;" scope="col">QNT SISA</th>
                  </tr>
                </thead>
                <tbody id="tabel2_data" class="text-left">
                  @foreach ($listData2 as $item)
                      <tr>
                      <td>{{ $item->NoBukti}}</td>
                      <td>{{ date("Y/m/d", strtotime($item->Tanggal)) }}</td>
                      <td>{{ $item->KodeCustSupp }}</td>
                      <td>{{ $item->NAMACUSTSUPP }}</td>
                      <td>{{ $item->kodebrg }}</td>
                      <td>{{ $item->namabrg }}</td>
                      <td>{{ $item->namasls }}</td>
                      <td class="text-right">{{ number_format($item->QNT, 2) }}</td>
                      <td>{{ $item->SAT }}</td>
                      <td class="text-right">{{ number_format($item->QNTSSKONSI, 2) }}</td>
                      <td class="text-right">{{ number_format($item->QNTSISA, 2) }}</td>
                      </tr>
                  @endforeach
                  </tbody>
              </table>
            </div>
        </div> {{-- End OutStanding 2 --}}
      </div>
    </div>
  </div>
</div>
</div>
</div>

<!-- start modal add -->
{{-- <div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> --}}
<div id="page2" class="container-fluid" style="display:none; margin-top:-80px;" >
  <div class="row">
    <div class="col-6 text-left">
      <h1>Form Permintaan Sample</h1>
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
            <div class="mb-2 row">
            <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_sales" type="hidden">
                        <button type="button" onclick="buttonAddListSales()" class="btn btn-primary btn-sm rounded-end shadow-sm">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea class="form-control text-left" id="input_keterangan" rows="3" onblur="onChangeHeader('NOTE' , 'input_keterangan')" style="resize: none;"></textarea>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Customer</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                        <input id="input_customer" type="hidden">
                        <button id="btn_customer" type="button" onclick="buttonAddListCustomer()" class="btn btn-primary btn-sm rounded-end shadow-sm">
                          <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
           <div class="mb-2 row align-items-center">
              <label class="col-sm-4 col-form-label text-nowrap">Gudang Asal</label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="input_gudang_nama" type="text" class="form-control text-center" placeholder="Gudang Asal" disabled>
                  <input type="hidden" id="input_gudang">
                  <button type="button" id="btn_gudang" onclick="buttonAddListGudang()" class="btn btn-primary btn-sm rounded-end shadow-sm">
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Ref PR</label>
                <div class="col-sm-8">
                    <input id="input_refpr" type="text" class="form-control text-left">
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
            <div class="mb-2 row align-items-center">
                <label class="col-sm-4 col-form-label text-nowrap">Tanggal Kirim</label>
                <div class="col-sm-8">
                    <input type="date" 
                        class="form-control text-center" 
                        id="input_add_tanggalkirim" 
                        value="{!! date('Y-m-d') !!}" 
                        onchange="onChangeHeader('TglKirim', 'input_add_tanggalkirim')">
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

    <!-- ADD SUBGROUP -->
    <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="4">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                  <th colspan="1"></th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang Asal</th>
                  <th scope="col">Gudang Tujuan</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr > 
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td> 
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-success btn-sm" type="button" title="Edit"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    <button class="btn btn-primary btn-sm" type="button" title="Details"><i class="bi bi-list"></i></button>
                  </td>
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
                      <label>Kode Barang</label>
                    </div>
                  </div>
                <div class="col-md-4">
                  <div class="input-group mb-3">
                  <input id="input_add_add_kodebarang" type="text" class="form-control text-center" placeholder="Kode Barang">
                  <button type="button" id="buttonAddListKodeBarang" onclick="buttonAddListKodeBarang()" class="btn btn-primary btn-sm rounded-end shadow-sm"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            <div class="row" style="margin-top:-10px;">
              <div class="col-md-3" style="margin-top:5px;">
                <div class="form-group">
                <label>Ket. Barang</label>
              </div>
              </div>
              <div class="col-md-8">
                <input id="input_add_add_keterangannama" type="text" class="form-control text-center" disabled>
              </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="row">
              <div class="col-md-2" style="margin-top:5px;">
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

{{-- Start Modal List Customer --}}
<div class="modal fade" id="modalAddListCustomer" role="dialog" aria-labelledby="labelCustomer" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="margin-top:-30px;">
        <div class="container-fluid px-3 mt-4">
          <div class="row">
            <div class="table-responsive">
              <table id="tabel_add_list_customer" class="table table-bordered table-striped">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th>Actions</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_customer" class="text-left">
                  <tr>
                    <td class="text-center">
                      <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                    </td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
              onclick="buttonAddListBatal()">Batal</button> --}}
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
{{-- End Modal List Customer --}}

{{-- Start Modal List Sales --}}
<div class="modal fade" id="modalAddListSales" role="dialog" aria-labelledby="labelSales" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="margin-top:-30px;">
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
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
              onclick="buttonAddListBatal()">Batal</button> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Modal List Sales --}}

{{-- Start Modal List gudang --}}
<div class="modal fade" id="modalAddListGudang" role="dialog" aria-labelledby="labelGudang" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="margin-top:-30px;">
        <div class="container-fluid px-3 mt-4">
          <div class="row">
            <div class="table-responsive">
              <table id="tabel_add_list_gudang" class="table table-bordered table-striped">
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="width: 1px;">Actions</th>
                    <th>Kode</th>
                    <th>Nama Gudang</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_gudang" class="text-left">
                  <tr>
                    <td class="text-center" style="width: 1px;">
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
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"
              onclick="buttonAddListBatal()">Batal</button> --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Modal List gudang --}}

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
                <label for="input_search_barang_all" class="me-2 mb-0" style="margin-right:-90px;">Search:</label>
                <input id="input_search_barang_all" type="text" class="form-control"
                  style="max-width: 250px;" onkeypress="searchBarangAll(event)">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="table-responsive">
            <table id="tabel_add_list_item" class="table table-bordered table-striped">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Merk</th>
                  <th scope="col">Part Number</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_item" class="text-left">
                <!-- Diisi lewat JS -->
              </tbody>
            </table>
          </div>
          </div>

          <div class="d-flex justify-content-end mt-3">
            {{-- <button type="button" class="btn btn-danger btn-lg"
              style="height: 30px; padding: 4px 12px; border-radius: 20px;
              font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeListItemAdd()">Close</button> --}}
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<!-- End modal list item add-->

<!-- start modal detail -->
<div id="page3" class="container-fluid" style="display: none">
        <div class="row">
          <div class="col-6 text-left">
            <h2>Detail Permintaan Sample</h2>
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
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detail_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_detail_sales" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea class="form-control text-left" id="input_detail_keterangan" rows="3" style="resize: none;" disabled></textarea>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Customer</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detail_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                        <input id="input_detail_customer" type="hidden">
                    </div>
                </div>
            </div>
           <div class="mb-2 row align-items-center">
              <label class="col-sm-4 col-form-label text-nowrap">Gudang Asal</label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="input_detail_gudang_nama" type="text" class="form-control text-center" placeholder="Gudang Asal" disabled>
                  <input type="hidden" id="input_detail_gudang">
                </div>
              </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Ref PR</label>
                <div class="col-sm-8">
                    <input id="input_detail_refpr" type="text" class="form-control text-center" disabled>
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
            <div class="mb-2 row align-items-center">
                <label class="col-sm-4 col-form-label text-nowrap">Tanggal Kirim</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
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
                  <th colspan="4">Deskripsi Barang</th>
                  <th colspan="2">Satuan </th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang Asal</th>
                  <th scope="col">Gudang Tujuan</th>
                  <th scope="col">Satuan</th>
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

let tempAdd = {} /// kalau di so tempAddAdd
let tempEdit = {} //// kalau di so tempAddEdit
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}
let tipeform = ''
let tipeformitem = ''

$(document).ready(function () {
  $("#tabel").DataTable({
    "lengthChange": false,
    "paging": false,
    "order": [[1, "asc"]],
    "autoWidth": false,
    "columnDefs": [{ targets: [0], orderable: false, width: "1px" }],
  });

  $("#tabel2").DataTable({
    "lengthChange": false,
    "paging": false,
    "order": [[1, "asc"]],
    "autoWidth": false,
    "columnDefs": [{ targets: [0], orderable: false, width: "1px" }],
  });

  $("#tabel_oto").DataTable({
    "lengthChange": false,
    "paging": false,
    "order": [[1, "asc"]],
    "autoWidth": false,
    "columnDefs": [{ targets: [0], orderable: false, width: "1px" }],
  });

  // === SEARCH BARANG via ENTER ===
  document.getElementById("input_add_add_kodebarang").addEventListener("keypress", function (e) {
    if (e.which == 13) {
      let search = this.value.trim();

      const sales = document.getElementById("input_sales_nama").value.trim();
      const customer = document.getElementById("input_customer_nama").value.trim();
      const gudang = document.getElementById("input_gudang_nama").value.trim();

      if (!sales || !customer || !gudang) {
        alertify.warning("Silakan isi terlebih dahulu Customer, Sales, dan Gudang Asal");
        return;
      }

      if (!search) {
        alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.");
        return;
      }

      if ($.fn.DataTable.isDataTable("#tabel_add_list_item")) {
        $("#tabel_add_list_item").DataTable().clear().destroy();
      }

      $("#tabel_data_add_list_item")
        .empty()
        .append(`<tr><td class="text-center" colspan="5">Mencari data...</td></tr>`);

      $.ajax({
        url: "{!! url('permintaansamplelistbarang') !!}",
        type: "get",
        async: false,
        data: { search: search },
        success: function (res) {
          dataAddListItem = res;

          if (!res.length) {
            $("#formAddListItem").modal("show");
            $("#tabel_data_add_list_item")
              .empty()
              .append(`<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>`);
            return;
          }

          if (res.length === 1) {
            buttonAddAddInsertItem(0);
            return;
          }

          $("#formAddListItem").modal("show");

          let rowTable = "";
          res.forEach((item, i) => {
            rowTable += `
              <tr>
                <td class="text-center">
                  <button class="btn btn-primary btn-sm" onclick="buttonAddAddInsertItem(${i})" type="button">
                    <i class="bi bi-plus"></i>
                  </button>
                </td>
                <td>${item.Kodebrg}</td>
                <td>${item.NamaBrg}</td>
                <td>${item.NamaMerk ?? ""}</td>
                <td>${item.partNumber ?? ""}</td>
              </tr>`;
          });

          $("#tabel_data_add_list_item").empty().append(rowTable);
          $("#formAddListItem").modal("show");

          $("#tabel_add_list_item").DataTable({
            lengthChange: false,
            paging: false,
            searching: false,
          });
        },
        error: function (err) {
          console.log(err);
          alertify.warning("Terjadi kesalahan, silakan refresh browser");
        },
      });
    }
  });

  // customer
  const elCustomer = document.getElementById("input_customer_nama");
  if (elCustomer) {
    elCustomer.addEventListener("keypress", function (e) {
      if (e.which == 13) {
        const search = this.value.trim();
        if (!search) {
          alertify.warning("Silakan ketik kode atau nama customer terlebih dahulu.");
          return;
        }

        if ($.fn.DataTable.isDataTable("#tabel_add_list_customer")) {
          $("#tabel_add_list_customer").DataTable().clear().destroy();
        }

        $("#tabel_data_add_list_customer").html(
          `<tr><td class="text-center" colspan="5">Mencari data...</td></tr>`
        );

        $.ajax({
          url: "{{ url('permintaansamplelistcustomer') }}",
          type: "get",
          async: false,
          data: { search: search },
          success: function (res) {
            if (!res.length) {
              $("#modalAddListCustomer").modal("show");
              $("#tabel_data_add_list_customer").html(
                `<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>`
              );
              return;
            }

            if (res.length === 1) {
              const c = res[0];
              buttonAddPickCustomer(c.KodeCustSupp, c.NamaCustSupp, c.Alamat);
              return;
            }

            let rows = "";
            res.forEach((item) => {
              rows += `
                <tr>
                  <td class="text-center">
                    <button class="btn btn-primary btn-sm" type="button"
                      data-kode="${item.KodeCustSupp ?? ""}"
                      data-nama="${item.NamaCustSupp ?? ""}"
                      data-alamat="${item.Alamat ?? ""}"
                      onclick="buttonAddPickCustomer(this.dataset.kode, this.dataset.nama, this.dataset.alamat)">
                      <i class="bi bi-plus"></i>
                    </button>
                  </td>
                  <td>${item.KodeCustSupp ?? ""}</td>
                  <td>${item.NamaCustSupp ?? ""}</td>
                  <td>${item.Alamat ?? ""}</td>
                  <td>${item.NamaKota ?? ""}</td>
                </tr>`;
            });

            $("#tabel_data_add_list_customer").html(rows);
            $("#modalAddListCustomer").modal("show");
            $("#tabel_add_list_customer").DataTable({
              lengthChange: false,
              paging: false,
            });
          },
          error: function (err) {
            console.log(err);
            alertify.warning("Terjadi kesalahan, silakan refresh browser.");
          },
        });
      }
    });
  }

  // sales
  const elSales = document.getElementById("input_sales_nama");
  if (elSales) {
    elSales.addEventListener("keypress", function (e) {
      if (e.which == 13) {
        const search = this.value.trim();
        if (!search) {
          alertify.warning("Silakan ketik kode/NIK/nama sales terlebih dahulu.");
          return;
        }

        if ($.fn.DataTable.isDataTable("#tabel_add_list_sales")) {
          $("#tabel_add_list_sales").DataTable().clear().destroy();
        }

        $("#tabel_data_add_list_sales").html(
          `<tr><td class="text-center" colspan="4">Mencari data...</td></tr>`
        );

        $.ajax({
          url: "{{ url('permintaansamplelistsales') }}",
          type: "get",
          async: false,
          data: { search: search },
          success: function (res) {
            if (!res.length) {
              $("#modalAddListSales").modal("show");
              $("#tabel_data_add_list_sales").html(
                `<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>`
              );
              return;
            }

            if (res.length === 1) {
              const s = res[0];
              buttonAddPickSales(s.namaSls, s.KodeSls);
              return;
            }

            let rows = "";
            res.forEach((item) => {
              rows += `
                <tr>
                  <td class="text-center">
                    <button class="btn btn-primary btn-sm" type="button"
                      data-nama="${item.namaSls ?? ""}"
                      data-kode="${item.KodeSls ?? ""}"
                      onclick="buttonAddPickSales(this.dataset.nama, this.dataset.kode)">
                      <i class="bi bi-plus"></i>
                    </button>
                  </td>
                  <td>${item.NIK ?? ""}</td>
                  <td>${item.namaSls ?? ""}</td>
                </tr>`;
            });

            $("#tabel_data_add_list_sales").html(rows);
            $("#modalAddListSales").modal("show");
            $("#tabel_add_list_sales").DataTable({
              lengthChange: false,
              paging: false,
            });
          },
          error: function (err) {
            console.log(err);
            alertify.warning("Terjadi kesalahan, silakan refresh browser.");
          },
        });
      }
    });
  }

  // gudang
  const elGdg = document.getElementById("input_gudang_nama");
  if (elGdg) {
    elGdg.addEventListener("keypress", function (e) {
      if (e.which == 13) {
        const search = this.value.trim();
        if (!search) {
          alertify.warning("Silakan ketik kode/nama gudang terlebih dahulu.");
          return;
        }

        if ($.fn.DataTable.isDataTable("#tabel_add_list_gudang")) {
          $("#tabel_add_list_gudang").DataTable().clear().destroy();
        }

        $("#tabel_data_add_list_gudang").html(
          `<tr><td class="text-center" colspan="3">Mencari data...</td></tr>`
        );

        $.ajax({
          url: "{{ url('permintaansamplelistgudang') }}",
          type: "get",
          async: false,
          data: { search: search },
          success: function (res) {
            if (!res.length) {
              $("#modalAddListGudang").modal("show");
              $("#tabel_data_add_list_gudang").html(
                `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`
              );
              return;
            }

            if (res.length === 1) {
              const g = res[0];
              buttonAddPickGudang(g.NamaGdg, g.KodeGdg);
              return;
            }

            let rows = "";
            res.forEach((item) => {
              rows += `
                <tr>
                  <td class="text-center" style="width:1px;">
                    <button class="btn btn-primary btn-sm" type="button"
                      data-nama="${item.NamaGdg ?? ""}"
                      data-kode="${item.KodeGdg ?? ""}"
                      onclick="buttonAddPickGudang(this.dataset.nama, this.dataset.kode)">
                      <i class="bi bi-plus"></i>
                    </button>
                  </td>
                  <td>${item.KodeGdg ?? ""}</td>
                  <td>${item.NamaGdg ?? ""}</td>
                </tr>`;
            });

            $("#tabel_data_add_list_gudang").html(rows);
            $("#modalAddListGudang").modal("show");
            $("#tabel_add_list_gudang").DataTable({
              lengthChange: false,
              paging: false,
            });
          },
          error: function (err) {
            console.log(err);
            alertify.warning("Terjadi kesalahan saat mengambil data gudang.");
          },
        });
      }
    });
  }

  // loadAll();
});



function onChangeHeader (field , idvalue) {
  let _token  = $("#_token").val()
  console.log(field, idvalue)
  let onChangeValue  = $(`#${idvalue}`).val()
  let nobukti  = $(`#input_add_nobukti`).val()
  console.log(onChangeValue , nobukti)


  console.log({
    _token : _token,
    field,
    nobukti,
    value: onChangeValue

  })

  $.ajax({
      url: "{!! url('permintaansampleonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        field,
        nobukti,
        value: onChangeValue

      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
        const fieldLabels = {
            KODESLS: 'Sales',
            TGLKIRIM: 'Tanggal Kirim',
            NOTE: 'Keterangan'
        };

        const label = fieldLabels[field] || field;
        alertify.success(`${label} berhasil diupdate`);
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

}

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
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('permintaansampleloadall') !!}",
    type: "get",
    async: false,
    data: {},
    success: function (res) {
      console.log('belum otorisasi:', res.belum_otorisasi);
      console.log('sudah otorisasi:', res.sudah_otorisasi);
      console.log('outstanding:', res.outstanding);

      // ========== TAB 1: Belum Otorisasi ==========
      $('#tabel').DataTable().destroy();
      let rowTable = '';

      res.belum_otorisasi.forEach((Group) => {
        const item = Group[0];
        rowTable += `
          <tr>
            <td class="text-center">
              <div class="d-flex justify-content-center align-items-center gap-2">
                <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('${item.NOBUKTI}')">
                  <i class="bi bi-info"></i>
                </button>
                <button class="btn btn-success btn-sm p-1 px-2" title="Edit" onclick="buttonEdit('${item.NOBUKTI}')">
                  <i class="bi bi-pen"></i>
                </button>
                <button class="btn btn-info btn-sm p-1 px-2" title="Otorisasi" onclick="buttonOtorisasi('${item.NOBUKTI}', ${item.IsOtorisasi1})">
                  <i class="bi bi-key"></i>
                </button>
              </div>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${formatDate(item.TANGGAL)}</td>
            <td>${item.Keterangan}</td>
            <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">${item.NamaCustSupp}</td>
            <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">${item.NAMASLS}</td>
            <td>${item.RefPR || ''}</td>
          </tr>`;
      });

      $("#tabel_data").html(rowTable);
      $("#tabel").DataTable({
        lengthChange: false,
        paging: false,
        order: [[1, "asc"]]
      });

      // ========== TAB 2: Outstanding ==========
      $('#tabel2').DataTable().destroy();
      let rowTable2 = '';

      res.outstanding.forEach(item => {
        rowTable2 += `
          <tr>
            <td>${item.NoBukti}</td>
            <td>${formatDate(item.Tanggal)}</td>
            <td>${item.KodeCustSupp}</td>
            <td>${item.NAMACUSTSUPP}</td>
            <td>${item.kodebrg}</td>
            <td>${item.namabrg}</td>
            <td>${item.namasls ?? ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toFixed(2)}</td>
            <td>${item.SAT}</td>
            <td class="text-right">${isNaN(parseFloat(item.QNTSSKONSI)) ? '' : parseFloat(item.QNTSSKONSI).toFixed(2)}</td>
            <td class="text-end">${parseFloat(item.QNTSISA).toFixed(2)}</td>
          </tr>`;
      });

      $("#tabel2_data").html(rowTable2);
      $("#tabel2").DataTable({
        lengthChange: false,
        paging: false,
        order: [[1, "asc"]]
      });

      // ========== TAB 3: Sudah Otorisasi ==========
      if ($("#tabel_oto").length) {
        $('#tabel_oto').DataTable().destroy();
        let rowTable3 = '';

        res.sudah_otorisasi.forEach((Group) => {
          const item = Group[0];
          rowTable3 += `
            <tr>
              <td class="text-center">
                <div class="d-flex justify-content-center align-items-center gap-2">
                  <button class="btn btn-warning btn-sm p-1 px-2" title="Details" onclick="buttonDetail('${item.NOBUKTI}')">
                    <i class="bi bi-info"></i>
                  </button>
                  <button class="btn btn-danger btn-sm p-1 px-2" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NOBUKTI}')">
                    <i class="bi bi-key"></i>
                  </button>
		  <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.NOBUKTI}')">
                    <i class="bi bi-printer"></i>
                  </button>
                </div>
              </td>
              <td>${item.NOBUKTI}</td>
              <td>${formatDate(item.TANGGAL)}</td>
              <td>${item.Keterangan}</td>
              <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">${item.NamaCustSupp}</td>
              <td style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">${item.NAMASLS}</td>
              <td>${item.RefPR || ''}</td>
              <td>${item.OtoUser1 || ''}</td>
              <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
            </tr>`;
        });

        $("#tabel_oto_data").html(rowTable3);
        $("#tabel_oto").DataTable({
        lengthChange: false,
        paging: false,
        order: [[1, "asc"]]
      });
      }
    },
    error: function (xhr, status, error) {
      alert("Gagal load data: " + error);
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('permintaansampledetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">Dari : ${dataPrint[0].NAMACUSTSUPP ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">PERMINTAAN SAMPLE</h2>
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
                    <td class="text-center" style="width: 20%">QTY</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.Total) {
          grandTotal += parseFloat(item.Total) || 0;
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
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNTCETAK ? parseFloat(itemSub.QNTCETAK).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.SATX}</td>
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

         <span style="float: left; display: block; clear: left;">

         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Diajukan Oleh</td>
               <td class="no-border text-center" style="width: 10%"></td>
               <td class="no-border text-center" style="width: 35%">Menyetujui</td>
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
    url: "{!! url('permintaansampleupdateotorisasi') !!}",
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
        url: "{!! url('permintaansampleupdatebatalotorisasi') !!}",
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


function buttonAddListCustomer () {
  console.log('buttonAddListCustomer');

  if ($.fn.DataTable.isDataTable('#tabel_add_list_customer')) {
    $('#tabel_add_list_customer').DataTable().clear().destroy();
  }

  $.ajax({
    url: "{{ url('permintaansamplelistcustomer') }}",
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
                onclick="buttonAddPickCustomer('${item.KodeCustSupp}', '${item.NamaCustSupp}', '${item.Alamat}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.KodeCustSupp}</td>
            <td>${item.NamaCustSupp}</td>
            <td>${item.Alamat}</td>
            <td>${item.NamaKota}</td>
          </tr>`;
      });

      if (!res.length) {
        rowTable = `<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>`;
      }

      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable;

      $("#tabel_add_list_customer").DataTable({
        lengthChange: false,
        paging: false
      });

      $('#modalAddListCustomer').modal('show');
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}

function buttonAddPickCustomer (kode, nama, alamat) {
  $('#input_customer_nama').val(nama);  
  $('#input_customer').val(kode);
  $('#modalAddListCustomer').modal('hide');
}

function buttonAddListSales () {
  console.log('buttonAddListSales');

  if ($.fn.DataTable.isDataTable('#tabel_add_list_sales')) {
    $('#tabel_add_list_sales').DataTable().clear().destroy();
  }

  $.ajax({
    url: "{{ url('permintaansamplelistsales') }}",
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
  onChangeHeader('KODESLS', 'input_sales');
}

function buttonAddListGudang () {
  console.log('buttonAddListGudang');
  if ($.fn.DataTable.isDataTable('#tabel_add_list_gudang')) {
    $('#tabel_add_list_gudang').DataTable().clear().destroy();
  }

  $.ajax({
    url: "{{ url('permintaansamplelistgudang') }}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res);

      let rowTable = ``;
      res.forEach((item, i) => {
        rowTable += `
          <tr>
            <td class="text-center" style="width: 1px;">
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
        "autoWidth": false,
        "columnDefs": [
          { targets: 0, width: "1px", orderable: false, searchable: false }
        ]
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
  $('#modalAddListCustomer').modal('hide');
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
    let tanggalkirim = $("#input_add_tanggalkirim").val();
    let sales = $("#input_sales").val();
    let kodebarang = $("#input_add_add_kodebarang").val();
    let keterangannama = $("#input_add_add_keterangannama").val();
    let satuan = $("#input_add_add_satuan").val();
    let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
    let keterangan = $("#input_keterangan").val() || '';

    if (!kodebarang || !satuan || qnt <= 0) {
        alertify.warning("Lengkapi semua data wajib");
        return;
    }

    let barang = tempEdit;
    console.log("tempEdit:", tempEdit);
    let isi = 0;
    let nosat = parseInt(satuan);
    let qnt1 = 0;
    let sat1 = '';
    let sat2 = '';

    barang.ISI1 = barang.ISI1 || 1;
    barang.ISI2 = barang.ISI2 || 1;
    barang.ISI3 = barang.ISI3 || 1;

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

    sat1 = barang.SAT1 ?? satuan ?? 'PCS';
    sat2 = barang.SAT2 ?? satuan ?? 'PCS';

    keterangannama = keterangannama.replace(/["']/g, '');
    keterangan = keterangan.replace(/["']/g, '');

    let nourut = parseInt(barang.NOURUT); 
    let urut = parseInt(barang.URUT);

    console.log("URUT yg dikirim:", urut);
    console.log("QNT yg dikirim:", qnt);
    console.log("SAT1 dikirim:", sat1);
    console.log("SAT2 dikirim:", sat2);

    $.ajax({
        url: "{!! url('permintaansamplespadd') !!}",
        type: "POST",
        async: false,
        data: {
            _token,
            choice,
            nobukti,
            nourut,
            tanggal,
            note: keterangan,
            urut: urut,              
            kodebarang,
            gdgasal: barang.gdgAsal,
            gdgtujuan: barang.gdgTujuan || '',
            satuan,                      
            sat_1: sat1,
            sat_2: sat2,
            qnt,
            qnt2: qnt,
            nosat,
            isi,
            kodecustsupp: barang.KODECUSTSUPP,
            kodesls: sales,
            pbonus: barang.pbonus || 0,
            maxol: 0,
            tglkirim: tanggalkirim,
            refpr: barang.RefPR,
            pkonsi: 0,
            lokasi: barang.Lokasi || '',
            keterangannama,
            jmlrecord
        },
        success: function(res) {
            console.log('respoedit', res);
            loadAll();
            $('.showhide').hide();
            refreshDataTableAdd(nobukti);
            alertify.success('Berhasil edit item');
        },
        error: function(err) {
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
  document.getElementById("buttonAddListKodeBarang").disabled = true;
  document.getElementById("input_add_add_kodebarang").disabled = true;

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

  // Tampilkan mode edit
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_kodebarang").scrollIntoView();
}

function buttonEdit (nobukti) {
  tipeform = 'edit';

  let akses = $("#akses_iskoreksi").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  $.ajax({
    url: "{!! url('permintaansamplespdetail') !!}",
    type: "get",
    async: false,
    data: { nobukti },
    success: function (res) {
      if (!res || !res.length) {
        alertify.error("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi dan tidak dapat diedit");
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


      let dateKirim = new Date(data.TglKirim);
      let tglKirimFormatted = data.TglKirim?.substring(0, 10) ?? '';

      // Isi form header
      $('#input_add_tanggal').val(dateFormatted);
      $('#input_add_tanggalkirim').val(tglKirimFormatted);
      $('#input_add_nobukti').val(data.NOBUKTI);
      $('#input_add_nourut').val(data.Nourut);
      $('#input_refpr').val(data.RefPR);
      $('#input_keterangan').val(data.Keterangan);
      $('#input_sales_nama').val(data.NAMASLS);
      $('#input_sales').val(data.KODESLS);
      $('#input_customer_nama').val(data.NamaCustSupp);
      $('#input_customer').val(data.KODECUSTSUPP);
      $('#input_gudang_nama').val(data.NamaGgdAsal);
      $('#input_gudang').val(data.gdgAsal);

      // Isi tabel item
      let rowTable = "";
      dataTableAdd.forEach((item, i) => {
        rowTable += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.gdgAsal} - ${item.NamaGgdAsal}</td>
            <td>${item.gdgTujuan} - ${item.NamaGgdTujuan}</td>
            <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
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

  let data = dataTableAdd[index];

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + data.KODEBRG + ' ?',
    function () {
      let _token = $("#_token").val();
      let choice = "D";
      let nobukti = $("#input_add_nobukti").val();
      let tanggal = $("#input_add_tanggal").val();
      let kodebarang = data.KODEBRG;
      let qnt = data.QNT;
      let nosat = data.NoSat;
      let satuan = data.SAT_1;
      let isi = data.ISI;
      let urut = data.URUT;
      let keterangan = data.NamaBrg;
      let refpr = data.RefPR || '';
      let gudang = data.GDGAsal || '';
      let sales = $("#input_sales").val();
      let customer = $("#input_customer").val();
      let tglkirim = data.TglKirim || tanggal;
      let jmlrecord = 0;

      $.ajax({
        url: "{!! url('permintaansamplespdelete') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          choice,
          nobukti,
          tanggal,
          kodebarang,
          qnt,
          nosat,
          satuan,
          isi,
          urut,
          note: keterangan,
          refpr,
          gdgasal: gudang,
          kodesls: sales,
          kodecustsupp: customer,
          tglkirim,
          jmlrecord
        },
        success: function (res) {
          alertify.success("Item sudah dihapus");
          loadAll();
          refreshDataTableAdd(nobukti);

          if (!dataTableAdd || dataTableAdd.length === 0) {
            document.getElementById("btn_customer").disabled = false;
            document.getElementById("input_customer_nama").disabled = false;
            document.getElementById("btn_gudang").disabled = false;
            document.getElementById("input_gudang_nama").disabled = false;
            unlockFormAdd();
            tipeform = 'new';
          }
        },
        error: function (err) {
          console.log(err);
          alertify.error("Gagal menghapus item");
        }
      });
    },
    function () {
      console.log('User cancelled delete');
    });
}


function buttonDetail (nobukti) {
  $.ajax({
    url: "{!! url('permintaansamplespdetail') !!}",
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
        <td>${item.gdgAsal} - ${item.NamaGgdAsal}</td>
        <td>${item.gdgTujuan} - ${item.NamaGgdTujuan}</td>
        <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
        <td>${item.SAT_1}</td>
        </tr>`
      });

      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_detail_tanggal').val(date1)

      let dateKirim = new Date(res[0].TglKirim);
      let dayKirim = ("0" + dateKirim.getDate()).slice(-2);
      let monthKirim = ("0" + (dateKirim.getMonth() + 1)).slice(-2);
      let tglKirimFormatted = dateKirim.getFullYear() + "-" + monthKirim + "-" + dayKirim;
      $('#input_detail_tanggalkirim').val(tglKirimFormatted);

      document.getElementById("tabel_data_detail").innerHTML  = rowTable
      document.getElementById("input_detail_nobukti").value  = res[0].NOBUKTI
      document.getElementById("input_detail_customer_nama").value  = res[0].NamaCustSupp
      document.getElementById("input_detail_sales_nama").value  = res[0].NAMASLS
      document.getElementById("input_detail_gudang_nama").value  = res[0].NamaGgdAsal
      document.getElementById("input_detail_refpr").value  = res[0].RefPR
      document.getElementById("input_detail_keterangan").value  = res[0].Keterangan

  }})
  $("#page3").show();
  $("#page1").hide();
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('permintaansamplespnobukti') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log("RESPON NOBUKTI", res);
      if (res && res.length > 0) {
        const nobukti = res[0].NoBukti || res[0].Nobukti;
        const nourut = res[0].NoUrut || res[0].Nourut;

        document.getElementById("input_add_nobukti").value = nobukti;
        document.getElementById("input_add_nourut").value = nourut;
      } else {
        alertify.error("Gagal mendapatkan No Bukti dari server.");
      }
    },
    error: function(err) {
      console.error("Gagal ambil nobukti", err);
      alertify.error("Gagal mengambil data dari server.");
    }
  });
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
    url: "{!! url('permintaansamplespnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
    dataTableAdd = []

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
  // loadAll();
}

function buttonAddListKodeBarang () {
  const sales = document.getElementById("input_sales_nama").value.trim();
  const customer = document.getElementById("input_customer_nama").value.trim();
  const gudang = document.getElementById("input_gudang_nama").value.trim();

  if (!sales || !customer || !gudang) {
    alertify.warning("Silakan isi terlebih dahulu Customer, Sales, dan Gudang Asal");
    return;
  }

  // Kosongkan isi tbody
  if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
    $('#tabel_add_list_item').DataTable().clear().destroy();
  }

  document.getElementById("tabel_data_add_list_item").innerHTML = `
    <tr><td class="text-center" colspan="5">Silakan ketik pencarian</td></tr>`;

  // Tampilkan modal
  $('#formAddListItem').modal('show');
}


function searchBarangAll (e) {
  if (e.which === 13) {
    console.log('Enter ditekan');

    const search = $("#input_search_barang_all").val().trim();

    if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
      $('#tabel_add_list_item').DataTable().clear().destroy();
    }

    if (!search) {
      document.getElementById("tabel_data_add_list_item").innerHTML = `
        <tr><td class="text-center" colspan="5">Silakan ketik pencarian</td></tr>
      `;
      return;
    }

    // AJAX untuk cari barang
    $.ajax({
      url: "{!! url('permintaansamplelistbarang') !!}",
      type: "get",
      async: false,
      data: {
        search,
        isagen: 0
      },
      success: function (res) {
        console.log(res);
        dataAddListItem = res;
        let rowTable = "";

        if (res.length === 0) {
          document.getElementById("tabel_data_add_list_item").innerHTML = `
            <tr><td class="text-center" colspan="5">Tidak ada data</td></tr>
          `;
          return;
        }

        res.forEach((item, i) => {
          rowTable += `
            <tr>
              <td class="text-center">
                <button class="btn btn-primary btn-sm" onclick="buttonAddAddInsertItem(${i})" type="button">
                  <i class="bi bi-plus"></i>
                </button>
              </td>
              <td>${item.Kodebrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.NamaMerk ?? ''}</td>
              <td>${item.partNumber ?? ''}</td>
            </tr>`;
        });

        document.getElementById("tabel_data_add_list_item").innerHTML = rowTable;

        $('#tabel_add_list_item').DataTable({
          "lengthChange": false,
          "paging": false,
          "searching": false,
        });
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser');
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

  let item = dataAddListItem[i];
  
  $('#input_add_add_kodebarang').val(item.Kodebrg);
  $('#input_add_add_keterangannama').val(item.NamaBrg);

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
  let customer = $("#input_customer").val();
  let gudang = $("#input_gudang").val();
  let refpr = $("#input_refpr").val();
  let tanggalkirim = $("#input_add_tanggalkirim").val();
  let tanggal = $("#input_add_tanggal").val();
  let kodebarang = $("#input_add_add_kodebarang").val();
  let keterangannama = $("#input_add_add_keterangannama").val();
  let satuan = $("#input_add_add_satuan").val();
  let qnt = parseFloat($("#input_add_add_qnt").val()) || 0;
  let keterangan = $("#input_keterangan").val() || '';

  console.log({
  sales,
  customer,
  gudang,
  refpr,
  tanggal,
  tanggalkirim,
  kodebarang,
  satuan,
  qnt
});

  if (!sales || !customer || !gudang || !tanggal || !tanggalkirim || !kodebarang || !satuan || qnt <= 0) {
    alertify.warning("Lengkapi semua data wajib");
    return;
  }

  let barang = dataAddListItem.find(item => item.Kodebrg === kodebarang);
  if (!barang) {
    alertify.warning("Barang tidak ditemukan di daftar");
    return;
  }

  let refpr_valid = true;
  $.ajax({
    url: "{!! url('permintaansamplecekrefpr') !!}",
    type: "get",
    data: { refpr, nobukti },
    async: false,
    success: function (res) {
      if (res.exists) {
        alertify.warning("Ref PR sudah ada");
        refpr_valid = false;
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning("Gagal mengecek Ref PR");
      refpr_valid = false;
    }
  });
  if (!refpr_valid) return;

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
    url: "{!! url('permintaansamplespadd') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      note: keterangan,
      urut: 0,
      kodebarang,
      gdgasal: gudang,
      gdgtujuan: '',
      satuan,
      sat_1,
      sat_2,
      qnt,
      qnt2,
      nosat,
      isi,
      kodecustsupp: customer,
      kodesls: sales,
      pbonus: 0,
      maxol: 0,
      tglkirim: tanggalkirim,
      refpr,
      pkonsi: 0,
      lokasi: '',
      keterangannama,
      jmlrecord
    },
    success: function (res) {
      console.log('respoadd', res);
      if (res == 1) {
        loadAll();
        tipeform = 'edit';
        cleanFormAddAdd();
        refreshDataTableAdd(nobukti);

        document.getElementById("btn_customer").disabled = true;
        document.getElementById("input_customer_nama").disabled = true;
        document.getElementById("btn_gudang").disabled = true;
        document.getElementById("input_gudang_nama").disabled = true;
        document.getElementById("input_add_tanggal").disabled = true;

        alertify.success('Berhasil menambah item');
      } else if (res == 2) {
        setNewNoBukti();
        alertify.warning('Nobukti telah direfresh silahkan submit ulang');
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan silakan refresh browser');
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
  document.getElementById("buttonAddListKodeBarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").disabled = false;
  document.getElementById("input_add_add_kodebarang").value = ""
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
    
    document.getElementById("btn_customer").disabled = false;
    document.getElementById("btn_gudang").disabled = false;
    tipeform = "new";
    return;
  }

  $.ajax({
    url: "{!! url('permintaansamplespdetail') !!}",
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
        document.getElementById("btn_customer").disabled = false;
        document.getElementById("btn_gudang").disabled = false;
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
            <td>${item.gdgAsal} - ${item.NamaGgdAsal || ''}</td>
            <td>${item.gdgTujuan} - ${item.NamaGgdTujuan || ''}</td>
            <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
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
  document.getElementById("input_add_add_qnt").value = '0.00'
  document.getElementById("input_add_add_satuan").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
}

function cleanFormAdd (){
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_customer").value = ''
  document.getElementById("input_customer_nama").value = ''
  document.getElementById("input_sales").value = ''
  document.getElementById("input_sales_nama").value = ''
  document.getElementById("input_gudang").value = ''
  document.getElementById("input_gudang_nama").value = ''
  document.getElementById("input_refpr").value = ''
  document.getElementById("input_keterangan").value = ''
}

function lockFormAdd (){
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("btn_customer").disabled = true
  document.getElementById("input_customer_nama").disabled = true
  document.getElementById("btn_gudang").disabled = true
  document.getElementById("input_gudang_nama").disabled = true
  document.getElementById("input_refpr").disabled = true
}

function unlockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_refpr").disabled = false
  // document.getElementById("input_gudang_nama").disabled = false
  // document.getElementById("input_customer_nama").disabled = false
}
</script>

{{-- script buat hover belum otorisasi dan sudah otorisasi --}}
  <script>
    const tabHome = document.getElementById('nav-home-tab');
    const tabHome2 = document.getElementById('nav-home2-tab');
    const tabProfile = document.getElementById('nav-profile-tab');

    function setActiveTab(activeTab) {
      // reset semua tab ke warna default
      [tabHome, tabHome2, tabProfile].forEach(tab => {
        tab.style.backgroundColor = '#f8f9fa';
        tab.style.color = '#007bff';
      });

      // aktifkan tab yang dipilih
      activeTab.style.backgroundColor = '#007bff';
      activeTab.style.color = '#fff';
    }

    // Default warna tab pertama
    setActiveTab(tabHome);

    tabHome.addEventListener('click', function () {
      setActiveTab(tabHome);
    });

    tabHome2.addEventListener('click', function () {
      setActiveTab(tabHome2);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(tabProfile);
    });
  </script>
{{-- script buat hover belum otorisasi dan sudah otorisasi --}}


@endsection
