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

@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="" style="margin-top:-85px;">

  <!-- <div id="qrcode"></div> -->
  <div class="row">
    <div class="col-6 text-left" >
      <h2>Penyerahan Sample</h2>
    </div>
    <div class="col-6 text-right">
      <!-- <button type="button" class="btn btn-primary btn-lg" style="
          height: 30px;
          margin-top: -150px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonAdd()">
        Add SO
      </button> -->
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
</div>

<div id="printContainer" style="display:none">


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
<div class="card-header">
<div class="row">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
         style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">
        Permintaan Sample
      </a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        SSP Belum Otorisasi
      </a>
      <a class="nav-item nav-link" id="nav-home2-tab" data-toggle="tab" href="#home2" role="tab"
          aria-controls="nav-home2" aria-selected="false"
          style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
        SSP Sudah Otorisasi
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-md-12">
        <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
          <table id="tabel" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;"  scope="col">Actions</th>
                <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col" style="min-width: 150px">Sales</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal Kirim</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left" >
              @for ($i = 0; $i < count($tempOutstanding); $i++)
            <tr>
              <td class='text-center'>
                 <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailAdd('{{ $tempOutstanding[$i]->NoBukti }}' )"><i class="bi bi-info"></i></button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('{{ $tempOutstanding[$i]->NoBukti }}')"><i class="bi bi-plus"></i></button>
              </td>
              <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
              <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
              <td>{{ $tempOutstanding[$i]->namasls }}</td>
              <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TglKirim)) !!}</td>
            </tr>
              @endfor
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  {{-- Tab belum oto --}}
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto;">
        <div class="container-fluid" style="padding:0; margin:0; width:100%;">
          <table id="tabel2" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;"  scope="col">Actions</th>
                <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                <th style="padding: 4px 12px;"  scope="col">Sales</th>
                <th style="padding: 4px 12px;"  scope="col">User</th>
                <th style="padding: 4px 12px;"  scope="col">No.Ref</th>
                {{-- <th style="padding: 4px 12px;"  scope="col">Oto</th> --}}
              </tr>
            </thead>


            <tbody id="tabel2_data" class="text-left" >
              @for ($i = 0; $i < count($tempPenerimaan); $i++)
            <tr>
              <td class='text-center'>
                <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $tempPenerimaan[$i]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempPenerimaan[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-pen"></i></button>
                @if ($tempPenerimaan[$i]->IsOtorisasi1 == 1)
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempPenerimaan[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-key"></i></button>
                @else
                <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempPenerimaan[$i]->NOBUKTI }}' , 'add')"><i class="bi bi-key"></i></button>
                @endif
              </td>
              <td>{{ $tempPenerimaan[$i]->NOBUKTI}}</td>
              <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i]->TANGGAL)) !!}</td>
              <td>{{ $tempPenerimaan[$i]->NAMASLS}}</td>
              <td>{{ $tempPenerimaan[$i]->IDUSER}}</td>
              <td>{{ $tempPenerimaan[$i]->RefPR ?? ''}}</td>
              {{-- @if ($tempPenerimaan[$i]->IsOtorisasi1)
                  <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                  @else
                  <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                  @endif --}}
            </tr>
              @endfor
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  {{-- Tab sudah oto --}}
  <div class="tab-pane fade" id="home2" role="tabpanel" aria-labelledby="home2-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
              <div class="container-fluid">
                <table id="tabel_oto" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;"  scope="col">Actions</th>
                      <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                      <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;"  scope="col">Sales</th>
                      <th style="padding: 4px 12px;"  scope="col">User</th>
                      <th style="padding: 4px 12px;"  scope="col">No.Ref</th>
                      <th style="padding: 4px 12px;"  scope="col">User Oto</th>
                      <th style="padding: 4px 12px;"  scope="col">Tgl Oto</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_oto_data" class="text-left">
                     @for ($i = 0; $i < count($tempPenerimaan2); $i++)
                    <tr>
                      <td class='text-center'>
                        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' )"><i class="bi bi-info"></i></button>
                        @if ($tempPenerimaan2[$i]->IsOtorisasi1 == 1)
                        <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' , 'edit')"><i class="bi bi-key"></i></button>
                        @else
                        <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempPenerimaan2[$i]->NOBUKTI }}' , 'add')"><i class="bi bi-key"></i></button>
                        @endif
			<button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{ $tempPenerimaan2[$i]->NOBUKTI }}')" ><i class="bi bi-printer"></i>
                        </button>
                      </td>
                      <td>{{ $tempPenerimaan2[$i]->NOBUKTI}}</td>
                      <td>{!! date("Y/m/d", strtotime($tempPenerimaan2[$i]->TANGGAL)) !!}</td>
                      <td>{{ $tempPenerimaan2[$i]->NAMASLS}}</td>
                      <td>{{ $tempPenerimaan2[$i]->IDUSER}}</td>
                      <td>{{ $tempPenerimaan2[$i]->RefPR ?? ''}}</td>
                      <td>{{ $tempPenerimaan2[$i]->OtoUser1 }}</td>
                      <td>{!! $tempPenerimaan2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempPenerimaan2[$i]->TglOto1)) : '' !!}</td>
                    </tr>
                @endfor
                </tbody> 
                </table>
              </div>
            </div>
          </div>
        </div>
      {{-- end tab sudah oto --}}
  
</div>
</div>
</div>
</div>
</div>





<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Form Penyerahan Sample</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Sales</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_add_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                <input id="input_add_sales" type="hidden">
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="input_add_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                <input id="input_add_customer" type="hidden">
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Serahkan</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">Stock</th>
                  </tr>
                </thead>
                <tbody id="addTableData" class="" >
                  <tr>
                    <td colspan=6 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
    </div>
    <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div>
  </div>
</div>


<div id="page3" style="display: none" class="mainpage container-fluid" >
  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Koreksi Penyerahan Sample</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    {{-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> --}}
    <div class="row">
        <input type="hidden" class="form-control" id="input_koreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_koreksi_sales" type="hidden">
                    </div>
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
                        <input id="input_koreksi_customer" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                    <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_koreksi_keterangan"  onblur="onChangeHeader('NOTE' , 'input_koreksi_keterangan')"></textarea>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
          </div>
        </div>

        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="koreksiTable" class="table table-bordered table-striped"  >
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
                <tbody id="koreksiTableData" class="" >
                  <tr>
                    <td colspan=7 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
    </div>

    <div id="formKoreksiEdit" class="container-fluid showhideitem">
    <div class="row">
      <div class="col-4">
        <h4 id="h4KoreksiEditItem" style="margin-left:-15px;">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Kode Barang</label>
          </div>
          <div class="col-md-4">
            <input id="KoreksiEditKodeBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Nama Barang</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditNamaBrg" type="text" class="form-control text-center" disabled>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Quantity</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputQty" type="number" step="0.01" class="form-control text-right" value="0.00">
          </div>
          <div class="col-md-2" style="margin-top:5px;">
            <label class="form-label fw-bold">Satuan</label>
          </div>
          <div class="col-md-3">
            <input id="KoreksiEditInputSat" type="text" class="form-control text-center" disabled>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Gudang Asal</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangAsal" type="text" class="form-control text-center" disabled>
            <input type="hidden" id="input_gudang_asal">
          </div>
        </div>

        <div class="row">
          <div class="col-md-3" style="margin-top:5px;">
            <label class="form-label fw-bold">Gudang Tujuan</label>
          </div>
          <div class="col-md-8">
            <input id="KoreksiEditGudangTujuan" type="text" class="form-control text-center" disabled>
            <input type="hidden" id="input_gudang_tujuan">
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-12 text-right">
        <button type="button" class="btn btn-secondary" onclick="buttonKoreksiItemBatal()" style="
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Batal</button>

        <button id="buttonSubmitKoreksiEdit" type="button" onclick="submitKoreksiEdit()" class="btn btn-primary" style="
          height: 30px; 
          padding: 4px 12px; 
          border-radius: 20px; 
          font-size: 0.75rem; 
          font-weight: 600; 
          text-transform: uppercase; 
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Submit Edit</button>
      </div>
    </div>
  </div>
    <hr/>

  </div>
</div>
{{-- Start Modal List gudang asal --}}
  <div class="modal fade" id="modalAddListGudangAsal" role="dialog" aria-labelledby="labelGudangAsal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelGudangAsal">Pilih Gudang Asal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_gudangasal" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Kode</th>
                      <th>Nama Gudang</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudangasal" class="text-left">
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
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
{{-- End Modal List gudang asal --}}

{{-- Start Modal List gudang tujuan --}}
  <div class="modal fade" id="modalAddListGudangTujuan" role="dialog" aria-labelledby="labelGudangTujuan" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="labelGudangTujuan">Pilih Gudang Tujuan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="container-fluid px-3 mt-4">
            <div class="row">
              <div class="table-responsive">
                <table id="tabel_add_list_gudangtujuan" class="table table-bordered table-striped">
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th>Kode</th>
                      <th>Nama Gudang</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="tabel_data_add_list_gudangtujuan" class="text-left">
                    <tr>
                      <td>-</td>
                      <td>-</td>
                      <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button"><i class="bi bi-plus"></i></button>
                      </td>
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
{{-- End Modal List gudang tujuan --}}

<div id="page4" style="display: none" class="mainpage container-fluid" >
  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Detail Penyerahan Sample</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
        <input type="hidden" class="form-control" id="input_detailkoreksi_nourut" placeholder="No Urut" disabled>
        <!-- Kiri -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">No Bukti</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control text-center" id="input_detailkoreksi_nobukti" placeholder="No Bukti" disabled>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Sales</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detailkoreksi_salesnama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                        <input id="input_detailkoreksi_sales" type="hidden">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tengah -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Customer</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="input_detailkoreksi_customernama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                        <input id="input_detailkoreksi_customer" type="hidden">
                    </div>
                </div>
            </div>
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Keterangan</label>
                <div class="col-sm-8">
                  <textarea  style="width: 100%; resize: none" rows=3 placeholder="" class="form-control" id="input_detailkoreksi_keterangan" disabled></textarea>
                </div>
            </div>
        </div>
        <!-- Kanan -->
        <div class="col-md-4">
            <div class="mb-2 row">
                <label class="col-sm-4 col-form-label">Tanggal</label>
                <div class="col-sm-8">
                    <input type="date" class="form-control text-center" id="input_detailkoreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
                </div>
            </div>
          </div>
        </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">
              <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                <tr>
                  <th colspan="4">Deskripsi Barang</th>
                  <th colspan="2">Satuan</th>
                </tr>
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  <th scope="col">Gudang Asal</th>
                  <th scope="col">Gudang Tujuan</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Sat</th>
                </tr>
                </thead>
                <tbody id="detailKoreksiTableData" class="" >
                  <tr>
                    <td colspan=6 class="text-center">Belum ada data</td>
                </tr>
                </tbody>
              </table>
    </div>
  </div>
</div>

<div id="page5" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top:-80px">
    <div class="col-8 text-left">
      <h2>Form Detail Permintaan Sample</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; margin-top:5px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div class="container-fluid">
    <input type="hidden" name="noUrut" id="detail_nourut" value="" />
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="detail_nobukti" placeholder="" disabled>
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Sales</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="detail_sales_nama" type="text" class="form-control text-center" placeholder="Sales" disabled>
                <input id="detail_sales" type="hidden">
              </div>
            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="detail_customer_nama" type="text" class="form-control text-center" placeholder="Customer" disabled>
                <input id="detail_customer" type="hidden">
              </div>
            </div>
            </div>
          </div>
          {{-- <div class="col-md-3">
            <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="detail_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
          </div> --}}
        </div>
      </div>
    </div>
    <hr/>
        <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

              <table id="DetailAddTable" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                    <th style="padding: 4px 12px;" scope="col">Qty</th>
                    <th style="padding: 4px 12px;" scope="col">Satuan</th>
                    <th style="padding: 4px 12px;" scope="col">Stock</th>
                  </tr>
                </thead>
                <tbody id="DetailAddTableData" class="" >
                  <tr>
                    <td colspan=5 class="text-center">Belum ada data</td>
                  </tr>
                </tbody>
              </table>
    </div>
    {{-- <div class="row mt-2" style="margin-top: 0">
      <div class="col-md-12 text-right mt-4">
        <button id="buttonSubmitAdd" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;">Submit</button>
        <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
      </div>
    </div> --}}

  </div>
</div>





@endsection

@section('js')
<script type="text/javascript">



let dataTableAdd = []
let dataTableKoreksi = []
let barangKoreksiEdit = {}


$(document).ready(function(){
  $("#tabel").DataTable({
    "lengthChange": false,
      "paging": false ,
    });

  $("#tabel2").DataTable({
    "lengthChange": false,
      "paging": false ,
      "autoWidth": false,
    });

  $("#tabel_oto").DataTable({
      "lengthChange": false,
        "paging": false ,
        "autoWidth": false,
      });

  //   formAddListItem
});

// function buttonKoreksiListGudangAsal () {
//   console.log('buttonKoreksiListGudangAsal');
//   $('#tabel_add_list_gudangasal').DataTable().destroy();

//   $.ajax({
//     url: "{{ url('penyerahansamplelistgudangasal') }}",
//     type: "get",
//     async: false,
//     success: function(res) {
//       console.log(res);

//       let rowTable = ``;
//       res.forEach((item, i) => {
//         rowTable += `
//           <tr>
//             <td>${item.KodeGdg}</td>
//             <td>${item.NamaGdg}</td>
//             <td class="text-center">
//               <button class="btn btn-primary btn-sm" type="button"
//                 onclick="buttonAddPickGudangAsal('${item.NamaGdg}', '${item.KodeGdg}')">
//                 <i class="bi bi-plus"></i>
//               </button>
//             </td>
//           </tr>`;
//       });

//       if (!res.length) {
//         rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
//       }

//       document.getElementById("tabel_data_add_list_gudangasal").innerHTML = rowTable;
//       $("#tabel_add_list_gudangasal").DataTable({
//         "lengthChange": false,
//         "paging": false,
//       });

//       $('#modalAddListGudangAsal').modal('show');
//     },
//     error: function(err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
//     }
//   });
// }

// function buttonAddPickGudangAsal (nama, kode) {
//   $('#KoreksiEditGudangAsal').val(nama);
//   $('#input_gudang_asal').val(kode);
//   $('#modalAddListGudangAsal').modal('hide');
// }

// function buttonKoreksiListGudangTujuan () {
//   console.log('buttonKoreksiListGudangTujuan');
//   $('#tabel_add_list_gudangtujuan').DataTable().destroy();

//   $.ajax({
//     url: "{{ url('penyerahansamplelistgudangtujuan') }}",
//     type: "get",
//     async: false,
//     success: function(res) {
//       console.log(res);

//       let rowTable = ``;
//       res.forEach((item, i) => {
//         rowTable += `
//           <tr>
//             <td>${item.KodeGdg}</td>
//             <td>${item.NamaGdg}</td>
//             <td class="text-center">
//               <button class="btn btn-primary btn-sm" type="button"
//                 onclick="buttonAddPickGudangTujuan('${item.NamaGdg}', '${item.KodeGdg}')">
//                 <i class="bi bi-plus"></i>
//               </button>
//             </td>
//           </tr>`;
//       });

//       if (!res.length) {
//         rowTable = `<tr><td class="text-center" colspan="3">Tidak ada data</td></tr>`;
//       }

//       document.getElementById("tabel_data_add_list_gudangtujuan").innerHTML = rowTable;
//       $("#tabel_add_list_gudangtujuan").DataTable({
//         "lengthChange": false,
//         "paging": false,
//       });

//       $('#modalAddListGudangTujuan').modal('show');
//     },
//     error: function(err) {
//       console.log(err);
//       alertify.warning('Terjadi kesalahan saat mengambil data gudang.');
//     }
//   });
// }

// function buttonAddPickGudangTujuan (nama, kode) {
//   $('#KoreksiEditGudangAsal').val(nama);
//   $('#input_gudang_tujuan').val(kode);
//   $('#modalAddListGudangTujuan').modal('hide');
// }

// function buttonAddListBatal() {
//   $('#modalAddListGudangAsal').modal('hide');
//   $('#modalAddListGudangTujuan').modal('hide');
// }


function onChangeHeader (field , idvalue) {
  let _token  = $("#_token").val()
  console.log(field, idvalue)
  let onChangeValue  = $(`#${idvalue}`).val()
  let nobukti  = $(`#input_koreksi_nobukti`).val()
  console.log(onChangeValue , nobukti)


  console.log({
    _token : _token,
    field,
    nobukti,
    value: onChangeValue

  })

  $.ajax({
      url: "{!! url('penyerahansampleonchangeheader') !!}",
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
          alertify.warning(`${field} sudah diupdate`)
        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

}


function buttonKoreksiEditItem (i) {
  let barang = dataTableKoreksi[i];
  barangKoreksiEdit = barang;

  document.getElementById("KoreksiEditKodeBrg").value = barang.KODEBRG;
  document.getElementById("KoreksiEditNamaBrg").value = barang.NAMABRG;

  document.getElementById("KoreksiEditInputQty").value = barang.QNT ? parseFloat(barang.QNT).toFixed(2) : "0.00";
  document.getElementById("KoreksiEditInputSat").value = barang.NOSAT == 1 ? barang.SAT_1 : barang.SAT_2;

  document.getElementById("KoreksiEditGudangAsal").value = barang.NAMA_GDGASAL;
  document.getElementById("input_gudang_asal").value = barang.GDGASAL;

  document.getElementById("KoreksiEditGudangTujuan").value = barang.NAMA_GDGTUJUAN;
  document.getElementById("input_gudang_tujuan").value = barang.GDGTUJUAN;

  $('#formKoreksiEdit').show();
}

function refreshDataTableKoreksi (nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahansamplegetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        buttonCloseForm();
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableKoreksi = res;

      let rowTable = "";

      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GDGASAL}</td>
          <td>${item.GDGTUJUAN}</td>
          <td class="text-right">${item.QNT ? parseFloat(item.QNT).toLocaleString() : '0.00'}</td>
          <td class="text-center">${item.SAT_1}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})">
              <i class="bi bi-pen"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="buttonKoreksiDeleteItem(${i})">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>`;
      });

      document.getElementById("koreksiTableData").innerHTML = rowTable;

      let header = res[0];

      $("#input_koreksi_namacustomer").val(header.NAMACUSTSUPP);
      $("#input_koreksi_nobukti").val(header.NOBUKTI);
      $("#input_koreksi_catatan").val(header.KETERANGAN);
      $("#input_koreksi_sales_nama").val(header.NAMASLS);
      $("#input_koreksi_sales").val(header.KODESLS);
      $("#input_koreksi_customer").val(header.KODECUSTSUPP);
      $("#input_customer_nama").val(header.NAMACUSTSUPP);
      $("#input_koreksi_tanggal").val(header.TANGGAL);

      buttonKoreksiItemBatal();
    },
    error: function (err) {
      console.error(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonKoreksiDeleteItem (i) {
  console.log(i);
  let barang = dataTableKoreksi[i];

  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NAMABRG + ' ?',
    function () {
      let _token = $("#_token").val();
      let choice = "D";
      let qnt = 0;

      let nobukti = barang.NOBUKTI;
      let urut = barang.URUT;
      let kodebrg = barang.KODEBRG;
      let namabrg = barang.NAMABRG;
      let kodegdgasal = barang.GDGASAL;
      let kodegdgtujuan = barang.GDGTUJUAN;
      let nosat = barang.NOSAT;
      let isi = parseFloat(barang.ISI);
      let sat1 = barang.SAT_1;
      let sat2 = barang.SAT_2;
      let nopr = barang.NOPRSAMPLE;
      let urutpr = barang.URUTPRSAMPLE;

      let qnt2 = qnt;
      let qnt1 = qnt;

      let keterangan = $("#input_koreksi_keterangan").val();

      // log data
      console.log({
        choice,
        qnt,
        qnt1,
        qnt2,
        namabrg,
        nobukti,
        urut,
        nosat,
        isi,
        sat1,
        sat2,
        kodebrg,
        kodegdgasal,
        kodegdgtujuan,
        note: keterangan,
        nopr,
        urutpr
      });

      $.ajax({
        url: "{!! url('penyerahansamplespkoreksi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          choice,
          qnt,
          qnt1,
          qnt2,
          namabrg,
          nobukti,
          urut,
          nosat,
          isi,
          sat1,
          sat2,
          kodebrg,
          kodegdgasal,
          kodegdgtujuan,
          note: keterangan,
          nopr,
          urutpr
        },
        success: function (res) {
          if (res == 1) {
            refreshDataTableKoreksi(nobukti);
            loadAll();
            alertify.success('Item telah dihapus');
          } else {
            alertify.warning("Gagal menghapus item");
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser');
        }
      });
    },
    function () {
      console.log('Hapus dibatalkan');
    }
  );
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
    url: "{!! url('penyerahansamplespotorisasi') !!}",
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
        url: "{!! url('penyerahansamplespbatalotorisasi') !!}",
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


function submitKoreksiEdit () {
  let _token = $("#_token").val();
  let barang = barangKoreksiEdit;
  let choice = "U";

  let qnt = parseFloat($("#KoreksiEditInputQty").val());
  if (isNaN(qnt)) {
    alertify.warning("Qty tidak valid");
    return;
  }

  if (qnt < 0) {
    alertify.warning("Qty tidak boleh kurang dari 0");
    
    return;
  }

  // Data item
  let nobukti = barang.NOBUKTI;
  let urut = barang.URUT;
  let kodebrg = barang.KODEBRG;
  let namabrg = barang.NAMABRG;

  let kodegdgasal = $("#input_gudang_asal").val();
  let kodegdgtujuan = $("#input_gudang_tujuan").val();

  let nosat = barang.NoSat;
  let isi = parseFloat(barang.ISI); 

  let sat1 = barang.SAT_1;
  let sat2 = barang.SAT_2;

  let qnt2 = qnt;
  let qnt1 = qnt;

  let nopr = barang.NOPRSAMPLE;
  let urutpr = barang.URUTPRSAMPLE;

  let keterangan = $("#input_koreksi_keterangan").val();

  $.ajax({
    url: "{!! url('penyerahansamplespkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      qnt,
      qnt1,
      qnt2,
      namabrg,
      nobukti,
      urut,
      nosat,
      isi,
      sat1,
      sat2,
      kodebrg,
      kodegdgasal,
      kodegdgtujuan,
      note: keterangan,
      nopr,
      urutpr
    },
    success: function (res) {
      if (res == 1) {
        refreshDataTableKoreksi(nobukti);
        loadAll()
        alertify.success('Item telah dikoreksi');
      } else {
        alertify.warning("Koreksi gagal disimpan");
      }
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}


function buttonKoreksiItemBatal () {

  $('.showhideitem').hide();
}


function buttonKoreksi (nobukti) {
  console.log('buttonKoreksi', nobukti);

  let akses = $("#akses_iskoreksi").val();
  $('.showhideitem').hide();

  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahansamplegetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      const data = res[0];

      if (data.IsOtorisasi1 == 1) {
        alertify.warning("Data sudah diotorisasi");
        return;
      }

      dataTableKoreksi = res;

      // Isi Tabel Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr> 
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GDGASAL}</td>
          <td>${item.GDGTUJUAN}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
          <td class="text-center">
            <button class="btn btn-success btn-sm" onclick="buttonKoreksiEditItem(${i})"><i class="bi bi-pen"></i></button>
            <button class="btn btn-danger btn-sm" onclick="buttonKoreksiDeleteItem(${i})"><i class="bi bi-trash"></i></button>
          </td>
        </tr>`;
      });

      $("#koreksiTableData").html(rowTable);

      // Isi Form Header
      $("#input_koreksi_nobukti").val(data.NOBUKTI);
      $("#input_customer_nama").val(data.NAMACUSTSUPP);
      $("#input_koreksi_customer").val(data.KODECUSTSUPP);
      $("#input_sales_nama").val(data.NAMASLS);
      $("#input_koreksi_sales").val(data.KODESLS);
      $("#input_koreksi_keterangan").val(data.KETERANGAN);

      const tanggal = data.TANGGAL;
      $("#input_koreksi_tanggal").val(tanggal);

      $('.mainpage').hide();
      $('#page3').show();
    },
    error: function (err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailKoreksi (nobukti) {
  console.log('buttonDetailKoreksi', nobukti);

  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahansamplegetdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function (res) {
      console.log('res', res);

      if (!res || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      // Isi Tabel Detail Item
      let rowTable = "";
      res.forEach((item, i) => {
        rowTable += `<tr>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.GDGASAL}</td>
          <td>${item.GDGTUJUAN}</td>
          <td class="text-right">${parseFloat(item.QNT).toLocaleString()}</td>
          <td>${item.SAT_1}</td>
        </tr>`;
      });

      $("#detailKoreksiTableData").html(rowTable);

      // Isi Header Detail
      const data = res[0];

      $("#input_detailkoreksi_nobukti").val(data.NOBUKTI);
      $("#input_detailkoreksi_customernama").val(data.NAMACUSTSUPP);
      $("#input_detailkoreksi_customer").val(data.KODECUSTSUPP);
      $("#input_detailkoreksi_salesnama").val(data.NAMASLS);
      $("#input_detailkoreksi_sales").val(data.KODESLS);
      $("#input_detailkoreksi_keterangan").val(data.KETERANGAN);

      const tanggal = data.TANGGAL;
      $("#input_detailkoreksi_tanggal").val(tanggal);;

      $('.mainpage').hide();
      $('#page4').show();
    },
    error: function (err) {
      console.error('Error response:', err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser');
    }
  });
}

function buttonDetailAdd (nobukti) {
  console.log('buttonDetailAdd', nobukti);
  $('.showhideitem').hide();

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahansamplegetdetailpenerimaanadd') !!}",
    type: "POST",
    data: { _token, nobukti },
    success: function (res) {
      console.log('gudangpenyerahansample:3729 res', res);

      if (!Array.isArray(res) || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      let firstItem = res[0];
      if (firstItem) {
        $("#detail_nobukti").val(firstItem.NOBUKTI || "");
        $("#detail_customer_nama").val(firstItem.NAMACUSTSUPP || "");
        $("#detail_customer").val(firstItem.KodeCustSupp || "");
        $("#detail_sales_nama").val(firstItem.NamaSls || "");
        $("#detail_sales").val(firstItem.KodeSls || "");
        $("#detail_keterangan").val(firstItem.Note || "");
        $("#detail_tanggal").val(firstItem.TANGGAL || "");
      }

      let rowTable = "";

      res.forEach((item, i) => {
        let kodeBrg = item.KODEBRG || "";
        let namaBrg = item.NAMABRG || "";
        let satuan = item.SAT_1 || "";
        let qntStock =
          item.QNTSTOCK !== undefined
            ? parseFloat(item.QNTSTOCK).toFixed(2)
            : "0.00";
        let qnt =
          item.QNT !== undefined ? parseFloat(item.QNT).toFixed(2) : "0.00";

        // struktur tabel sama seperti buttonAdd tapi tanpa input/checkbox
        rowTable += `
          <tr>
            <td>${kodeBrg}</td>
            <td>${namaBrg}</td>
            <td class="text-right">${qnt}</td>
            <td>${satuan}</td>
            <td class="text-right">${qntStock}</td>
          </tr>`;
      });

      document.getElementById("DetailAddTableData").innerHTML = rowTable;

      $(".mainpage").hide();
      $("#page5").show();
    },
    error: function (err) {
      console.log("AJAX Error:", err);
      alertify.error("Terjadi kesalahan koneksi");
    }
  });
}


function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('penyerahansampleloadall') !!}",
    type: "get",
    async: false,
    data: {},
    success: function (res) {
      $('#tabel').DataTable().destroy();
      let rowTable = '';

      res.tempOutstanding.forEach((item) => {
        rowTable += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailAdd('${item.NoBukti}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonAdd('${item.NoBukti}')">
                <i class="bi bi-plus"></i>
              </button>
            </td>
            <td>${item.NoBukti}</td>
            <td>${item.Tanggal ? formatDate(item.Tanggal, '/') : ''}</td>
            <td>${item.NAMASLS || ''}</td>
            <td>${item.TglKirim ? formatDate(item.TglKirim, '/') : ''}</td>
          </tr>`;
      });

      document.getElementById("tabel_data").innerHTML = rowTable;
      $("#tabel").DataTable({
        lengthChange: false,
        paging: false
      });

      $('#tabel2').DataTable().destroy();
      let rowTable2 = '';

      res.tempPenerimaan.forEach((item) => {
        rowTable2 += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                <i class="bi bi-info"></i>
              </button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NOBUKTI}' , 'edit')"><i class="bi bi-pen"></i></button>
              ${
                item.IsOtorisasi1 == 1
                  ? `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.NOBUKTI}', 'edit')">
                      <i class="bi bi-key"></i>
                    </button>`
                  : `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.NOBUKTI}', 'add')">
                      <i class="bi bi-key"></i>
                    </button>`}
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.NAMASLS}</td>
            <td>${item.IDUSER}</td>
            <td>${item.RefPR || ''}</td>
          </tr>`;
      });

      document.getElementById("tabel2_data").innerHTML = rowTable2;
      $("#tabel2").DataTable({
        lengthChange: false,
        paging: false
      });

      $('#tabel_oto').DataTable().destroy();
      let rowTable3 = '';

      res.tempPenerimaan2.forEach((item) => {
        rowTable3 += `
          <tr>
            <td class="text-center">
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetailKoreksi('${item.NOBUKTI}')">
                <i class="bi bi-info"></i>
              </button>
              ${
                item.IsOtorisasi1 == 1
                  ? `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.NOBUKTI}', 'edit')">
                      <i class="bi bi-key"></i>
                    </button>`
                  : `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.NOBUKTI}', 'add')">
                      <i class="bi bi-key"></i>
                    </button>`}
		<button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.NOBUKTI}')">
                <i class="bi bi-printer"></i>
              </button>
            </td>
            <td>${item.NOBUKTI}</td>
            <td>${item.TANGGAL ? formatDate(item.TANGGAL, '/') : ''}</td>
            <td>${item.NAMASLS}</td>
            <td>${item.IDUSER}</td>
            <td>${item.RefPR || ''}</td>
            <td>${item.OtoUser1}</td>
            <td>${item.TglOto1 ? formatDate(item.TglOto1, '/') : ''}</td> 
          </tr>`;
      });

      document.getElementById("tabel_oto_data").innerHTML = rowTable3;
      $("#tabel_oto").DataTable({
        lengthChange: false,
        paging: false
      });
    }
  });
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('penyerahansampledetailCetak') !!}",
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
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Kepada : PT. ${dataPrint[0].NamaCustSupp ?? '-'}</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">SURAT PENYERAHAN BARANG</h2>
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
                  <div class="pb-1" style="width: 20%">Reff. PR</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">${dataPrint[0].refPR ?? '-'}</div>
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
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 20%">QUANTITY</td>
                    <td class="text-center" style="width: 20%">SATUAN</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotal = 0;
    arrayDataPrint.forEach(group => {
      group.forEach(item => {
        if (item.QNT) {
          grandTotal += parseFloat(item.QNT) || 0;
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
               style="width: 30%;">${itemSub.KODEBRG}</td>
         <td class="text-align: left"
               style="width: 50%;">${itemSub.NamaBrg}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.QNT ? parseFloat(itemSub.QNT).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 20%;  "> ${itemSub.SATUAN}</td>
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

            <div style="width:70%; text-align:right; padding-right:10px;">
              Total :
            </div>

            <div style="width:10%; text-align:right;">
              ${grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>
         
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px ">
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

function buttonAdd (nobukti) {
  console.log('buttonAdd', nobukti);

  let akses = $("#akses_istambah").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  setNewNoBukti();

  let _token = $("#_token").val();
  let tanggal = $("#input_add_tanggal").val(); 
  if (!tanggal) {
    alertify.warning("Tanggal harus diisi.");
    return;
  }

  $.ajax({
    url: "{!! url('penyerahansamplegetdetail') !!}",
    type: "POST",
    async: false,
    data: {
      _token,
      nobukti,
      tanggal 
    },
    success: function (res) {
      console.log('res', res);

      if (!Array.isArray(res) || res.length === 0) {
        alertify.warning("Data tidak ditemukan");
        return;
      }

      dataTableAdd = res;
      let rowTable = "";

      let firstItem = res[0];
      if (firstItem) {
        $("#input_add_sales_nama").val(firstItem.NamaSls || "");
        $("#input_add_sales").val(firstItem.KodeSls || "");

        $("#input_add_customer_nama").val(firstItem.NAMACUSTSUPP || "");
        $("#input_add_customer").val(firstItem.KodeCustSupp || "");
      }

      res.forEach((item, i) => {
        let kodeBrg = item.KODEBRG;
        let namaBrg = item.NAMABRG;
        let satuan = item.SAT_1;
        let qntStock = item.QNTSTOCK !== undefined ? parseFloat(item.QNTSTOCK).toFixed(2) : '0.00';
        let qnt = item.QNT !== undefined ? parseFloat(item.QNT).toFixed(2) : '0.00';

        rowTable += `
          <tr>
            <td class="text-center">
              <input type="checkbox" id="add_checkbox${i}" style="transform: scale(1.5); margin: 5px;">
            </td>
            <td>${kodeBrg}</td>
            <td>${namaBrg}</td>
            <td class="text-right">
              <input id="input_add_qnt${i}" style="width: 100px;" class="text-right" type="number" min="0" value="${qnt}">
            </td>
            <td>${satuan}</td>
            <td class="text-right">${qntStock}</td>
          </tr>`;
      });

      document.getElementById("addTableData").innerHTML = rowTable;

      $('.mainpage').hide();
      $('#page2').show();
    },
    error: function (err) {
      console.error("AJAX Error", err);
      alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
    }
  });
}

function submitAdd () {
  const checkDate = new Date($("#input_add_tanggal").val());
  const periode_bulan = parseInt(document.getElementById("periode_bulan").value);
  const periode_tahun = parseInt(document.getElementById("periode_tahun").value);

  if (checkDate.getFullYear() !== periode_tahun || (checkDate.getMonth() + 1) !== periode_bulan) {
    alertify.warning("Tanggal tidak sesuai periode");
    return;
  }

  const _token = $("#_token").val();
  const tempData = [];
  let checkMinus = false;

  const nobukti = $("#input_add_nobukti").val();
  const nourut = $("#input_add_nourut").val();
  const tanggal = $("#input_add_tanggal").val();
  const kodeSales = $("#input_add_sales").val();
  const namaSales = $("#input_add_sales_nama").val();
  const kodeCustomer = $("#input_add_customer").val();
  const namaCustomer = $("#input_add_customer_nama").val();

  dataTableAdd.forEach((item, i) => {
    const checkbox = document.getElementById(`add_checkbox${i}`);
    if (checkbox && checkbox.checked) {
      const inputQty = $(`#input_add_qnt${i}`).val();

      if (inputQty < 0) {
        checkMinus = true;
      }

      const itemData = {
        ...item,
        inputQnt: inputQty,
        KodeSls: kodeSales,
        NamaSls: namaSales,
        KodeCustSupp: kodeCustomer,
        NamaCustSupp: namaCustomer
      };

      tempData.push(itemData);
    }
  });

  if (checkMinus) {
    alertify.warning("Qty tidak boleh negatif");
    return;
  }

  if (!tempData.length) {
    alertify.warning("Tidak ada item yang dipilih");
    return;
  }

  console.log("submitAdd payload", tempData);

  $.ajax({
    url: "{!! url('penyerahansamplespadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tempData,
      tanggal,
      nobukti,
      nourut
    },
    success: function(res) {
    console.log('submitAdd response', res);
    // if (res == 1) {
    //     alertify.success('SSP telah ditambah');
    //     loadAll();
    //     buttonCloseForm();
    //   }
      if (res == 1) {
        alertify.success('SSP telah ditambah');
        loadAll();
        setTimeout(() => {
          $('.mainpage').hide();
          $('#page3').show();  
          buttonKoreksi(nobukti, 'edit');
        }, 500);
      } else if (res == 2) {
        setNewNoBukti();
        alertify.warning('Nobukti telah direfresh silahkan submit ulang');
      } else if (res == 3) {
        alertify.warning('Stok tidak mencukupi');
        return;
      } else {
        alertify.error('Respon tidak diketahui dari server');
      }
    }
  });
}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('penyerahansamplespnobukti') !!}",
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



function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();
  loadAll();
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
