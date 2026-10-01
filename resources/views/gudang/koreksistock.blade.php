@extends('gudang.newmaster')
@section('buttons')

@endsection

@section('css')


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
    margin-right: 155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }
  </style>
{{-- end tampilan search modal barang all --}}

{{-- tampilan search bar 1 --}}
  <style>
  #tabel_add_list_customer_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }

  #tabel_add_list_merk_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }

  #tabel_add_list_merk_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_modal_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }

  #tabel_add_list_modal_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }



  #tabel_add_list_customer_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_noinvoice_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_noinvoice_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_barang_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_barang_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

  #tabel_add_list_nobeli_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;
  }
  #tabel_add_list_nobeli_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

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
{{-- tampilan search bar 2 --}}
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >



  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top: -80px">
    <div class="col-6 text-left">
      <h2>Koreksi Stock</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >+ Koreksi Stock</button>
    </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->

<!-- <button onclick="setNewNoBukti()">tes</button> -->
<!-- <button onclick="buttonAdd('nobukti')">Add Tes</button> -->
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
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card">
<div class="card-header">
<div class="row">
  <nav style="width: 100%;">
    <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">KRS Belum Otorisasi</a>

      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" 
      style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
      KRS Sudah Otorisasi
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">

              <table id="tabel" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;"  scope="col">Actions</th>
                    <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                    <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding[$i]->Nobukti }}' , 'detail')"><i class="bi bi-info"></i></button>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding[$i]->Nobukti }}' , 'edit')"><i class="bi bi-pen"></i></button>
                    @if ($tempOutstanding[$i]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding[$i]->Nobukti }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi ('{{ $tempOutstanding[$i]->Nobukti }}' , 'otorisasi')"><i class="bi bi-key"></i></button>

                    @endif

                  </td>
                  <td>{{ $tempOutstanding[$i]->GroupNobukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
                </tr>
                  @endfor
                </tbody>


              </table>
        </div>
      </div>
    </div>
  </div>
  {{-- tab sudah otorisasi --}}
  <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row">
      <div class="col-12" style="overflow:auto; padding:0; margin:0; width:100%;">
        <div class="container-fluid">

              <table id="tabel2" class="table table-bordered table-striped"  >
                <thead class="text-center bg-primary text-white">
                  <tr>
                    <th style="padding: 4px 12px;"  scope="col">Actions</th>
                    <th style="padding: 4px 12px;"  scope="col">No.Bukti</th>
                    <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;"  scope="col">OtoUser</th>
                    <th style="padding: 4px 12px;"  scope="col">TglOto</th>
                  </tr>
                </thead>
                <tbody id="tabel2_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding2); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding2[$i]->Nobukti }}' , 'detail')"><i class="bi bi-info"></i></button>
                    @if ($tempOutstanding2[$i]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding2[$i]->Nobukti }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding2[$i]->Nobukti }}' , 'otorisasi')"><i class="bi bi-key"></i></button>
                    @endif
		    <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{$tempOutstanding2[$i]->Nobukti}}')" ><i class="bi bi-printer"></i></button>
                  </td>
                  <td>{{ $tempOutstanding2[$i]->GroupNobukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->Tanggal)) !!}</td>
                  <td>{{ $tempOutstanding2[$i]->OtoUser1 }}</td>
                  <td>{!! $tempOutstanding2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding2[$i]->TglOto1)) : '' !!}</td>
                </tr>
                  @endfor
                </tbody>


              </table>
        </div>
      </div>
    </div>
  </div>{{-- end sudah otorisasi --}}




</div>
</div>
</div>


</div>
</div>

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -80px">
    <div class="col-8 text-left">
      <h2>Koreksi Stock</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div id= "" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

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
              <input type="hidden" class="form-control" id="input_add_nourut" placeholder="" disabled>
            <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
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
            <input type="date" class="form-control text-left" id="input_add_tanggal" placeholder="" disabled>
          </div>
        </div>
      </div>

        </div>



      </div>




      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Gudang</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_gudang" type="text" class="form-control" disabled>
                <button id="buttonAddListGudang" type="button" onclick="buttonAddListGudang()" class="btn btn-primary"  >+</button>

                <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

              </div>
            </div>
          </div>

        </div>

      </div>

      <div class="row" style="margin-top: -10px">
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
              <label>Keterangan</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-10">
              <div class="input-group form-group">
                <input id="input_add_keterangan" type="text" class="form-control" onchange="onChangeHeader()">

                <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

              </div>
            </div>
          </div>

        </div>






      </div>
















      </div>



<div class="container-fluid">
  <hr/>

</div>



  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="table table-bordered table-striped"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Debet</th>
              <th style="padding: 4px 12px;" scope="col">Kredit</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Total</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=8 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddItem" type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah</button>
</div>

<div id="formAddAdd" class="container-fluid showhideitem">
  <!-- <div class="line"></div> -->
  <!-- <div class="row"> -->

  <div class="col-12">


  <hr/>
  <div class="row">
    <div class="col-md-12">
      <h4 id="labelAddItem">Add Item</h4>
      <h4 id="labelEditItem">Edit Item</h4>
    </div>
  </div>










<div class="row" style="margin-top: -10px">
<div class="col-md-3">


  <div class="row">

    <div class="col-md-4">
        <div class="form-group">
        <label>Kode Brg</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" onkeypress="onKeyPressBarang(event)">
          <button id="buttonAddListBarang" type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>

        </div>
      </div>

  </div>
</div>

</div>

<div class="row" style="margin-top: -10px">
<div class="col-md-3">


  <div class="row">

    <div class="col-md-4">
        <div class="form-group">
        <label>Nama Brg</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddNamaBrg" type="text" class="form-control" disabled>

        </div>
      </div>

  </div>
</div>

</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-3">
    <div class="row">

      <div class="col-md-4">
          <div class="form-group">
          <label>Qty Db</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8" >
          <div class="row" >
            <div class="col-md-8" style="padding-right: 0px">
              <div class="input-group form-group">
                <input id="AddAddQntDb" type="number" class="form-control text-right" onblur="onchangeqntdb()">
                <!-- <input id="AddAddQntDbSat" type="text" class="form-control" style="width: 5px"  disabled> -->

              </div>
            </div>
            <div class="col-md-4" style="padding-left: 0px">
              <div class="input-group form-group">
                <!-- <input id="AddAddQntDb" type="number" class="form-control text-right" > -->
                <input id="AddAddQntDbSat" type="text" class="form-control text-center" style="width: 5px"  disabled>

              </div>
            </div>
          </div>

        </div>

    </div>

  </div>


<div class="col-md-3">
  <div class="row">

    <div class="col-md-4">
        <div class="form-group">
        <label>Qty Cr</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-8" >
        <div class="row" >
          <div class="col-md-8" style="padding-right: 0px">
            <div class="input-group form-group">
              <input id="AddAddQntCr" type="number" class="form-control text-right" onblur="onchangeqntcr()">
              <!-- <input id="AddAddQntCrSat" type="text" class="form-control" style="width: 5px"  disabled> -->

            </div>
          </div>
          <div class="col-md-4" style="padding-left: 0px">
            <div class="input-group form-group">
              <!-- <input id="AddAddQntCr" type="number" class="form-control text-right" > -->
              <input id="AddAddQntCrSat" type="text" class="form-control text-center" style="width: 5px"  disabled>

            </div>
          </div>
        </div>

      </div>

  </div>

</div>

</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-3">
    <div class="row">

      <div class="col-md-4">
          <div class="form-group">
          <label>Harga</label>
        </div>
        </div>
        <!-- <div class="col-4 text-right">

          </div> -->
        <div class="col-md-8" >

          <div class="input-group form-group">
            <input id="AddAddHarga" type="number" class="form-control text-right" >
            <!-- <input id="AddAddQntCrSat" type="text" class="form-control" style="width: 5px"  disabled> -->

          </div>



        </div>

    </div>

  </div>
</div>








</div>











<div class="row mt-2" style="margin-top: 0">
  <div class="col-md-12 text-right mt-4">
    <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Batal</button>

    <button id="buttonSubmitAddItem" type="button" onclick="submitAddItem()" class="btn btn-primary" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Submit Add</button>

    <button id="buttonSubmitEditItem" type="button" onclick="submitEditItem()" class="btn btn-primary" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Submit Edit</button>


    <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
  </div>

</div>

</div>


  </div>
</div>

    </div>








  </div>



  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row" style="margin-top: -80px">
      <div class="col-8 text-left">
        <h2>Detail Koreksi Stock</h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-danger btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>

    <div id= "" class="">



    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">

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
                <input type="hidden" class="form-control" id="input_detail_nourut" placeholder="" disabled>
              <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
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
              <input type="date" class="form-control text-left" id="input_detail_tanggal" placeholder="" disabled>
            </div>
          </div>
        </div>

          </div>



        </div>




        <div class="row" style="margin-top: -10px">
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Gudang</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_gudang" type="text" class="form-control" disabled>

                  <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                </div>
              </div>
            </div>

          </div>

        </div>

        <div class="row" style="margin-top: -10px">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                <label>Keterangan</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-10">
                <div class="input-group form-group">
                  <input id="input_detail_keterangan" type="text" class="form-control" disabled>

                  <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                </div>
              </div>
            </div>

          </div>


        </div>


        </div>



  <div class="container-fluid">
    <hr/>

  </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Debet</th>
                <th style="padding: 4px 12px;" scope="col">Kredit</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Total</th>

              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=7 class="text-center">Belum ada data</td>

            </tr>

            </tbody>



            </table>
            </div>
            <div class="container-fluid">


            <div class="row" style="">
            <div class="col-6 text-left">
            </div>
            <div class="col-6 text-right">
            <button type="button" class="page3showhide otorisasishowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitOtorisasi()"  >Otorisasi</button>

            </div>
            </div>
            </div>







    </div>
  </div>

      </div>








    </div>


</div>



<!--  -->



<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="modalAddListBarang" class="showhidemodalbodyadd">
        <div class="modal-body" >

        <div class="container-fluid mt-4" >
          <div class="row">
            <div id="modalBodyAddAddListBarangAllTitle" class="col-md-9" style="">
              {{-- <h3>Barang All</h3> --}}
            </div>
            <div class="col-3 text-right form-group" style="margin-top:-30px;">
              <input id="input_search_barang_all" type="text" name="" value="" class="form-control" onkeypress="searchBarangAll(event)">
              <label for="input_search_barang_all" class="search-label">SEARCH:</label>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-40px;">
            <!-- <div class="container-fluid"> -->
            <table id="tabel_add_list_barangall" class="table table-bordered table-hover table-striped table-responsive-lg">
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add_list_barangall" class="text-left" >
                @for ($i = 0; $i < count($listBarangAll); $i++)
                <tr >
                   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBarang('{{ $listBarangAll[$i]->Kodebrg }}')" type="button" ><i class="bi bi-plus"></i></button></td>
                  <td>{{ $listBarangAll[$i]->Kodebrg }}</td>
                  <td>{{ $listBarangAll[$i]->NamaBrg }}</td>  
              </tr>
              @endfor
              </tbody>
            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
        {{-- <button type="button" class="btn btn-danger btn-lg"
        style="
        margin-top:-10px;
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="buttonAddListBatal()">Batal</button> --}}
      </div>
    </div>

      <div id= "modalAddListGudang" class="showhidemodalbodyadd">
      <div class="modal-header">
          <h5 class="modal-title" id="modalAddListGudangTitle">Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>

      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_gudang" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                </tr>
              </thead>


              <tbody id="tabel_data_add_list_gudang" class="text-left" >

                <tr >
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
                  <td>-</td>
                  <td>-</td>
              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        {{-- <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button> --}}
      </div>
      </div>


      <div id= "modalAddListMerk" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Merk</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Merk</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_merk" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_merk" class="text-left" >

                <tr >
                  <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
                  <td>-</td>
                  <td>-</td>
              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>




        </div>





      </div>


      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>


      </div>







    </div>
  </div>
<!-- End modal add-->














@endsection

@section('js')
<script type="text/javascript">

let tipeform = ''
let listData = []
let listDataAdd = []
let tempDataAdd = []
let itemEdit = {}
let listBarang = []
let barangx = {}

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          // "searching": false,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [5] },
          // "columns" : [{"width" : "20px"}]


        ]
        });

        $("#tabel2").DataTable({
        "lengthChange": false,
          "paging": false ,
          // "searching": false,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [5] },
          // "columns" : [{"width" : "20px"}]


        ]
        });
        //
        // $('.mainpage').hide()
        // $('#page2').show()
        // $('.showhide').show()
        //
        // $("#formAddItem").modal('toggle');

      $("#tabel_add_list_modal").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
     ]
    });







        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
});


function onKeyPressBarang (e) {
  if (e.which === 13) {
    let _token = $("#_token").val()
    let kodebrg = $('#AddAddKodeBrg').val();
    document.getElementById("input_search_barang_all").value = kodebrg
    let search = $("#input_search_barang_all").val();
    console.log(search)
    $('#tabel_add_list_barangall').DataTable().destroy();
    let gudang = $("#input_add_gudang").val();
    $('#tabel_add_list_barangall').DataTable().destroy();

    $.ajax({
      url: "{!! url('koreksistocklistbarang') !!}",
      type: "post",
      async: false,
      data: {
        search,
        _token,
        gudang
      },
      success: function(res) {

        console.log(res)
        listBarang = res
        let rowTable = ""

        if (res.length == 1) {
          buttonAddPickBarang( 0 , 1)
          $('#AddAddQntDb').focus();
          return
        }

        res.forEach((item, i) => {

          rowTable +=          `
          <tr >
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBarang('${i}')" type="button" ><i class="bi bi-plus"></i></button></td>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>      

        </tr>
        `
        });
        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable


      $("#tabel_add_list_barangall").DataTable({
        "lengthChange": false,
          "paging": false ,
          "searching" : false
      });

      $('.showhidemodalbodyadd').hide();
        $('#modalAddListBarang').show();

        $("#form").modal('toggle')

      }})

  }
}

function buttonAddBatal () {

  $('.showhideitem').hide();
}


function onchangeqntdb () {
    let qnt = $("#AddAddQntDb").val()
    if (Number(qnt) == 0) {
      document.getElementById("AddAddQntCr").disabled = false
    } else {
      document.getElementById("AddAddQntCr").disabled = true
    }
}

function onchangeqntcr () {

    let qnt = $("#AddAddQntCr").val()
    if (Number(qnt) == 0) {
      document.getElementById("AddAddQntDb").disabled = false;
      document.getElementById("AddAddHarga").disabled = false;
    } else {
      document.getElementById("AddAddQntDb").disabled = true;
      document.getElementById("AddAddHarga").disabled = true;
    }
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}


function loadAll () {
  console.log('loadall');
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('koreksistockloadall') !!}",
    type: "get",
    async: false,
    data: {},
    success: function(res) {
      console.log(res);
      // TAB 1 
      let rowTable1 = "";
      res.tempOutstanding.forEach((item, i) => {
        rowTable1 += `
          <tr>
            <td class='text-center'>
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.Nobukti}' , 'detail')"><i class="bi bi-info"></i></button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.Nobukti}' , 'edit')"><i class="bi bi-pen"></i></button>
              ${item.IsOtorisasi1 == 1 ?
                `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.Nobukti}' , 'edit')"><i class="bi bi-key"></i></button>`
                :
                `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.Nobukti}' , 'otorisasi')"><i class="bi bi-key"></i></button>`
              }
            </td>
            <td>${item.GroupNobukti}</td>
            <td>${formatDate(item.Tanggal)}</td>
          </tr>
        `;
      });

      $('#tabel').DataTable().destroy();
      document.getElementById("tabel_data").innerHTML = rowTable1;
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": false,
      });

      // TAB 2
      let rowTable2 = "";
      res.tempOutstanding2.forEach((item, i) => {
        rowTable2 += `
          <tr>
            <td class='text-center'>
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.Nobukti}' , 'detail')"><i class="bi bi-info"></i></button>
              ${item.IsOtorisasi1 == 1 ?
                `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.Nobukti}' , 'edit')"><i class="bi bi-key"></i></button>`
                :
                `<button class="btn btn-primary btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.Nobukti}' , 'otorisasi')"><i class="bi bi-key"></i></button>`
              }
	      <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.Nobukti}')">
                <i class="bi bi-printer"></i>
              </button>
            </td>
            <td>${item.GroupNobukti}</td>
            <td>${formatDate(item.Tanggal)}</td>
            <td>${item.OtoUser1 ?? ''}</td>
            <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
          </tr>
        `;
      });

      $('#tabel2').DataTable().destroy();
      document.getElementById("tabel2_data").innerHTML = rowTable2;
      $("#tabel2").DataTable({
        "lengthChange": false,
        "paging": false,
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
      url: "{!! url('koreksistockdetailCetak') !!}",
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
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];

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
                  <div class="pb-1" style="width: 100%">Nomor : `+dataPrint[0].Nobukti+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tanggal : `+tanggalOnly+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">KOREKSI STOCK</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].KodeGdg+`</div>
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
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif;  display: table;
                font-size: 10px">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">No.</td>
                    <td class="text-center" style="width: 30%">KODE BARANG</td>
                    <td class="text-center" style="width: 50%">NAMA BARANG</td>
                    <td class="text-center" style="width: 10%">SATUAN</td>
                    <td class="text-center" style="width: 10%">DEBET</td>
                    <td class="text-center" style="width: 10%">KREDIT</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
                  </tr>
                </thead> `;

    let z = 0
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
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 50%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: left"
               style="width: 50%;  ">${itemSub.namaBrg}</td>
         <td class="text-align: text-center"
               style="width: 10%;">${itemSub.Satuan}</td>
         <td class="text-align: text-right"
               style="width: 10%;  ">${itemSub.Qntdb ? parseFloat(itemSub.Qntdb).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 10%;  ">${itemSub.QntCr ? parseFloat(itemSub.QntCr).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 10%;  ">${itemSub.Harga 
               ? parseFloat(itemSub.Harga).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0,00'}</td>
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
               <td class="no-border text-center" style="width: 25%">Kepala Gudang</td>
               <td class="no-border text-center" style="width: 25%">Internal Audit</td>
               <td class="no-border text-center" style="width: 25%">Mengetahui</td>
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
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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

function buttonAddItem () {
  let gudang = $("#input_add_gudang").val()
  if(!gudang) {
    alertify.warning("Pilih gudang")
    return
  }

  $("#labelAddItem").show()
  $("#labelEditItem").hide()
  $("#buttonSubmitAddItem").show()
  $("#buttonSubmitEditItem").hide()
  lockFormAddItem(false)
  cleanFormAddItem()
  document.getElementById("AddAddKodeBrg").disabled = false

  document.getElementById("AddAddQntCr").disabled = false
  document.getElementById("AddAddQntDb").disabled = false

  $('.showhideitem').show()

}




function refreshDataTableDetail (nobukti) {

  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('koreksistockspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listData = res

      let rowTable = ``
      listData.forEach((item, i) => {


              rowTable += `
                <tr>
                  <td>${item.kodebrg}</td>
                  <td>${item.namaBrg}</td>
                  <td class="text-center">${item.Satuan}</td>
                  <td class="text-right">${formatAngkaX(item.Qntdb)}</td>
                  <td class="text-right">${formatAngkaX(item.QntCr)}</td>
                  <td class="text-right">${formatAngkaX(item.Harga)}</td>
                  <td class="text-right">${formatAngkaX(item.Total)}</td>
                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable
      document.getElementById("input_detail_nobukti").value = listData[0].Nobukti
      document.getElementById("input_detail_nourut").value = listData[0].NoUrut
      document.getElementById("input_detail_tanggal").value = formatDate(listData[0].Tanggal)
      document.getElementById("input_detail_keterangan").value = listData[0].note
      document.getElementById("input_detail_gudang").value = listData[0].KodeGdg



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      // resRefresh = 0;
    }

  })
}


function buttonDetail (nobukti , tipe = 'detail') {
  console.log(tipe)
  let _token = $("#_token").val();

  $('.page3showhide').hide()


  refreshDataTableDetail(nobukti)
  if(!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  } else {

  }

  if (tipe == 'otorisasi') {
    let akses = $("#akses_isotorisasi1").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    $('.otorisasishowhide').show()
  } else {

  }

  $('.mainpage').hide()
  $('#page3').show()


}


function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
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
          url: "{!! url('koreksistockspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()



          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
      alertify.error("Action cancelled");
    });
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
    url: "{!! url('koreksistockspotorisasi') !!}",
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


function submitOtorisasi () {

  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();
  $.ajax({
    url: "{!! url('koreksistockspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi')
      loadAll()
      buttonCloseForm()




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function onChangeHeader () {

  if (tipeform == 'add') {
    return
  }

  let _token = $("#_token").val();
  let nobukti = $("#input_add_nobukti").val();
  let keterangan = $("#input_add_keterangan").val();
  console.log(keterangan, nobukti)
  $.ajax({
    url: "{!! url('koreksistockspupdateheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      keterangan

    },
    success: function(res) {
      console.log(res)
      alertify.success('Berhasil update keterangan')
      // loadAll()




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function submitAddItem () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token = $("#_token").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if ( !kodebrg ) {
    alertify.warning("Kode barang harus diisi")
    return
  }

  if (kodebrg != barangx.KodeBrg) {
    alertify.warning("Barang tidak sesuai")
    return
  }
  let choice= 'I'
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let gudang = $("#input_add_gudang").val();
  let keterangan = $("#input_add_keterangan").val();

  let qntcr = $("#AddAddQntCr").val();
  let harga = $("#AddAddHarga").val();
  let qntdb = $("#AddAddQntDb").val();
  let satuan = $("#AddAddQntDbSat").val();
  let qntsaldo = Number(barangx.QntSaldo) ? barangx.QntSaldo : '0.00'
  let selisih = Number(qntcr) > 0 ? qntcr : qntdb
  let urut = 0
  let qntopname = 0
  let nosat = 1
  let isi = 1
  let jmlrecord = tipeform == 'add' ? 0 : 1
  if (Number(qntcr) <= 0  && Number(qntdb) <= 0 ) {
    alertify.warning("Qnt <= 0")
    return
  }

  if (Number(qntcr) > 0  && Number(qntdb) > 0 ) {
    alertify.warning("Qnt DB/CR")
    return
  }

  if (Number(qntcr) > 0){
    if (Number(qntcr) > Number(qntsaldo)){
      alertify.warning("Qnt CR Melebihi Stock")
      return
    }
  }
  // if (Number(qntcr) > Number(qntsaldo)) {
  //   alertify.warning("Qnt cr melebihi stock")
  //   return
  // }





  $.ajax({
      url: "{!! url('koreksistockspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal,
        keterangan,
        gudang,
        qntcr,
        qntdb,
        satuan,
        kodebrg,
        selisih,
        urut,
        qntsaldo,
        qntopname,
        jmlrecord,
        nosat,
        isi,
        harga
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Koreksi Stock telah ditambah');

          tipeform = 'edit'
          loadAll()
          $('.showhideitem').hide()
          lockForm()
          // $("#formAddItem").modal('toggle');
          buttonKoreksi(nobukti)

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }

        if (res == 3) {
          alertify.warning("Barang tidak ditemukkan")
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })



  return
  let tempData = []

  console.log(listDataAdd)
  listDataAdd.forEach((item, i) => {
    console.log(i)
    if (document.getElementById(`add_checkbox${i}`).checked) {

      tempData.push(listDataAdd[i])
    }
  });
  console.log(tempData)
  if (!tempData.length) {
    alertify.warning("Tidak ada data dipilih")
    return
  }


}



function buttonAddPickGudang (index, kode,nama) {

  $(".showhideitem").hide()
  document.getElementById("input_add_gudang").value = kode

  $("#form").modal('toggle')

}

function buttonAddPickKategori (index, kode,nama) {

  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = kode
          $("#form").modal('toggle')

}

function buttonAddPickSubKategori (index, kode,nama) {


  document.getElementById("input_add_kodesubkategori").value = kode
          $("#form").modal('toggle')

}

function buttonAddPickHeadGroup (index, kode,nama) {

  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = '-'
  document.getElementById("input_add_kodehdgrp").value = kode
          $("#form").modal('toggle')

}

function buttonAddPickMerk (index, kode,nama) {

  console.log()
  document.getElementById("input_add_kodemerk").value = kode
          $("#form").modal('toggle')

}



function buttonAddListGudang () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('koreksistocklistgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang(${i},'${item.KodeGdg}' , '${item.Nama}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeGdg}</td>
        <td>${item.Nama}</td>
        

        </tr>`
      });


      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable

      document.getElementById("modalAddListGudangTitle").innerHTML= 'Gudang'
      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Gudang tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function lockForm (value = false) {
  document.getElementById("input_add_tanggal").disabled = value
  // document.getElementById("input_add_keterangan").disabled = value
  document.getElementById("buttonAddListGudang").disabled = value

}



function searchBarangAll (e) {
  let _token = $("#_token").val()
  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();
    let gudang = $("#input_add_gudang").val();
    $('#tabel_add_list_barangall').DataTable().destroy();

    $.ajax({
      url: "{!! url('koreksistocklistbarang') !!}",
      type: "post",
      async: false,
      data: {
        search,
        _token,
        gudang
      },
      success: function(res) {

        console.log(res)
        listBarang = res
        let rowTable = ""
        res.forEach((item, i) => {

          rowTable +=          `
          <tr >
            <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBarang('${i}')" type="button" ><i class="bi bi-plus"></i></button></td>
            <td>${item.KodeBrg}</td>
            <td>${item.NamaBrg}</td>
        </tr>
        `
        });
        // $('#tabel_add_list_barangall').DataTable().destroy();

        document.getElementById("tabel_data_add_list_barangall").innerHTML = rowTable






      $("#tabel_add_list_barangall").DataTable({
        "lengthChange": false,
          "paging": false ,
          "searching" : false
      });



      }})

  }

}

function buttonAddListBarang () {
  let gudang = $("#input_add_gudang").val()
  if(!gudang) {
    alertify.warning("Pilih gudang")
    return
  }

  console.log("buttonAddListBarang")
  $('.showhidemodalbodyadd').hide();
    $('#modalAddListBarang').show();

    $('#tabel_add_list_barangall').DataTable().destroy();

    document.getElementById("tabel_data_add_list_barangall").innerHTML = ''

    $("#tabel_add_list_barangall").DataTable({
      "lengthChange": false,
        "paging": false ,
        "searching" : false
    });

    document.getElementById("input_search_barang_all").value = ''
    $("#form").modal('toggle')


    document.getElementById("modalBodyAddAddListBarangAllTitle").scrollIntoView();

    $('#form').on('shown.bs.modal', function () {
    $('#input_search_barang_all').trigger('focus')
    })
}


function buttonAddPickBarang (index , x = 0) {

  let _token  = $("#_token").val()
  barangx = listBarang[index]

  document.getElementById("AddAddKodeBrg").value = barangx.KodeBrg
  document.getElementById("AddAddNamaBrg").value = barangx.NamaBrg
  document.getElementById("AddAddQntCrSat").value = barangx.Sat1
  document.getElementById("AddAddQntDbSat").value = barangx.Sat1
  document.getElementById("AddAddQntDb").value = '0.00'
  document.getElementById("AddAddQntCr").value = '0.00'
  document.getElementById("AddAddHarga").value = '0.00'

  if (x == 1) {
    return
  }
  $("#form").modal('toggle')


}

function cleanFormAdd () {
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=11 class="text-center">Belum ada data</td></tr>`

}

function lockFormAddItem (value = false) {
  document.getElementById("buttonAddListBarang").disabled = value

}

function cleanFormAddItem () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddQntCr").value = '0.00'
  document.getElementById("AddAddQntDb").value = '0.00'
  document.getElementById("AddAddQntCrSat").value = ''
  document.getElementById("AddAddQntDbSat").value = ''
  document.getElementById("AddAddHarga").value = '0.00'


}



function refreshDataTable (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('koreksistockspdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {
        console.log(res)
        listData = res
        // console.log(res)
        // return
        // $('#formAddAdd').hide();
        if (!res.length) {
            alertify.success('Data Habis')
            // $("#form").modal('toggle')
            $('.mainpage').hide();
            $('#page1').show();
            return
        }
        // dataTableAdd = res

        let rowTable = ``
        listData.forEach((item, i) => {


                rowTable += `
                  <tr>
                    <td>${item.kodebrg}</td>
                    <td>${item.namaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">${formatAngkaX(item.Qntdb)}</td>
                    <td class="text-right">${formatAngkaX(item.QntCr)}</td>
                    <td class="text-right">${formatAngkaX(item.Harga)}</td>
                    <td class="text-right">${formatAngkaX(item.Total)}</td>
                    <td class='text-center'>
                      <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>

                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>


                    </td>


                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_nobukti").value = listData[0].Nobukti
        document.getElementById("input_add_nourut").value = listData[0].NoUrut
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)
        document.getElementById("input_add_keterangan").value = listData[0].note
        document.getElementById("input_add_gudang").value = listData[0].KodeGdg



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        // resRefresh = 0;
      }

    })
}


function buttonDeleteItem (index) {


  itemEdit = listData[index]
  let _token = $("#_token").val();
  let kodebrg = itemEdit.kodebrg

    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item '+ itemEdit.kodebrg +' ?',
        function() {
          let choice= 'D'
          let nobukti = $("#input_add_nobukti").val();
          let nourut = $("#input_add_nourut").val();
          let tanggal = $("#input_add_tanggal").val();
          let gudang = $("#input_add_gudang").val();
          let keterangan = $("#input_add_keterangan").val();

          let qntcr = 0
          let harga = 0
          let qntdb = 0
          let satuan = ''
          let qntsaldo = 0
          let selisih = 0
          let urut = itemEdit.Urut
          let qntopname = 0
          let nosat = 1
          let isi = 1
          let jmlrecord = tipeform == 'add' ? 0 : 1

          $.ajax({
              url: "{!! url('koreksistockspadd') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                choice,
                nobukti,
                nourut,
                tanggal,
                keterangan,
                gudang,
                qntcr,
                qntdb,
                satuan,
                kodebrg,
                selisih,
                urut,
                qntsaldo,
                qntopname,
                jmlrecord,
                nosat,
                isi,
                harga
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('Koreksi Stock telah diedit');

                  tipeform = 'edit'
                  loadAll()
                  $('.showhideitem').hide()
                  lockForm()
                  // $("#formAddItem").modal('toggle');
                  buttonKoreksi(nobukti)

                }

              },
              error: function (err) {
                console.log(err)
                alertify.warning('Terjadi kesalahan silahkan refresh browser')
              }
            })


        }
      ,function(){
        console.log('no')
      });

}

function submitEditItem () {
  let _token = $("#_token").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if ( !kodebrg ) {
    alertify.warning("Kode barang harus diisi")
    return
  }
  let choice= 'U'
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let tanggal = $("#input_add_tanggal").val();
  let gudang = $("#input_add_gudang").val();
  let keterangan = $("#input_add_keterangan").val();

  let qntcr = $("#AddAddQntCr").val();
  let harga = $("#AddAddHarga").val();
  let qntdb = $("#AddAddQntDb").val();
  let satuan = $("#AddAddQntDbSat").val();
  let qntsaldo = Number(itemEdit.SaldoComp) ? itemEdit.SaldoComp : '0.00'
  let selisih = Number(qntcr) > 0 ? qntcr : qntdb
  let urut = itemEdit.Urut
  let qntopname = 0
  let nosat = 1
  let isi = 1
  let jmlrecord = tipeform == 'add' ? 0 : 1
  if (Number(qntcr) <= 0  && Number(qntdb) <= 0 ) {
    alertify.warning("Qnt <= 0")
    return
  }

  if (Number(qntcr) > 0  && Number(qntdb) > 0 ) {
    alertify.warning("Qnt DB/CR")
    return
  }

  if (Number(qntcr) > 0){
    if (Number(qntcr) > Number(qntsaldo)){
      alertify.warning("Qnt CR Melebihi Stock")
      return
    }
  }
  // if (Number(qntcr) > Number(qntsaldo)) {
  //   alertify.warning("Qnt cr melebihi stock")
  //   return
  // }





  $.ajax({
      url: "{!! url('koreksistockspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        tanggal,
        keterangan,
        gudang,
        qntcr,
        qntdb,
        satuan,
        kodebrg,
        selisih,
        urut,
        qntsaldo,
        qntopname,
        jmlrecord,
        nosat,
        isi,
        harga
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Koreksi Stock telah diedit');

          tipeform = 'edit'
          loadAll()
          $('.showhideitem').hide()
          lockForm()
          // $("#formAddItem").modal('toggle');
          buttonKoreksi(nobukti)

        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })




}



function buttonEditItem (index) {
  itemEdit = {}

  itemEdit = listData[index]
  console.log(itemEdit)
  lockFormAddItem(true)
  // return
  $("#labelAddItem").hide()
  $("#labelEditItem").show()
  $("#buttonSubmitAddItem").hide()
  $("#buttonSubmitEditItem").show()
  document.getElementById("AddAddKodeBrg").value = itemEdit.kodebrg

  document.getElementById("AddAddNamaBrg").value = itemEdit.namaBrg
  document.getElementById("AddAddQntCr").value = parseFloat(itemEdit.QntCr).toFixed(2)
  document.getElementById("AddAddQntCrSat").value = itemEdit.Satuan
  document.getElementById("AddAddQntDb").value = parseFloat(itemEdit.Qntdb).toFixed(2)
  document.getElementById("AddAddQntDbSat").value = itemEdit.Satuan
  document.getElementById("AddAddHarga").value = parseFloat(itemEdit.Harga).toFixed(2)
  document.getElementById("AddAddKodeBrg").disabled = true

  if (Number(itemEdit.QntCr)) {
    document.getElementById("AddAddQntCr").disabled = false
    document.getElementById("AddAddQntDb").disabled = true

  } else {
    document.getElementById("AddAddQntCr").disabled = true
    document.getElementById("AddAddQntDb").disabled = false

  }

  $('.showhideitem').show()
}

function buttonKoreksi (nobukti) {
  tipeform = 'edit'
  listData = []

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  lockForm(true)
  refreshDataTable(nobukti)


  if (listData.length) {
    console.log("=")
    console.log(listData[0].IsOtorisasi1 )
    if (listData[0].IsOtorisasi1 == 1) {
      alertify.warning("Nobukti sudah diotorisasi")
      return
    } else {
      $('.mainpage').hide()
      $('.showhideitem').hide()
      $('#page2').show()

    }
  }



}



function buttonAdd () {
  listData = []
  tipeform = 'add'
  cleanFormAdd()
  lockForm()
  setNewNoBukti()
  $('.mainpage').hide()
  $('.showhideitem').hide()
  $('#page2').show()
}

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'KRS'
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
}


function formatDate (date , pemisah = '-') {
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

function formatAngkaX (angka) {
  if (!Number(angka)) {
    return '0.00'
  } else {
    return formatAngka(parseFloat(angka).toFixed(2))

  }

}
function formatAngka (angkaString) {
  let tempAngka = angkaString.split('.')

  if (tempAngka[0][0] == '-') {
    let temp2=''

    let tempAngka1 = tempAngka[0].split('-')
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2

    }
    temp2 += '.' + tempAngka[1]
    temp2 = '-' + temp2

    return temp2
  }
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1

  }
  temp1 += '.' + tempAngka[1]
  return temp1
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
{{-- script buat hover belum otorisasi dan sudah otorisasi --}}

@endsection
