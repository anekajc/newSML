@extends('gudang.newmaster')
@section('buttons')

@endsection

@section('css')


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

{{-- tampilan search propname --}}
  <style>
    #tabel_add_list_propname_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_propname_filter label input {
      width: 150px;
      border-radius: 10px; 
      border: 1px solid #ccc; 
      box-shadow: none; 
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search opname --}}
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
      <h2>Opname Barang</h2>
    </div>
    <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="
            height: 30px; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonAddNonBAP()"  >+ Non BAP</button>
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
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">Berita Acara Opname</a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
         style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
        OPN Belum Otorisasi
      </a>
      <a class="nav-item nav-link" id="nav-home2-tab" data-toggle="tab" href="#home2" role="tab"
        aria-controls="nav-home2" aria-selected="false"
        style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
      OPN Sudah Otorisasi
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
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                    <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                    <th style="padding: 4px 12px;" scope="col">KodeGdg</th>
                    <th style="padding: 4px 12px;" scope="col">KodeHdGrp</th>
                    <th style="padding: 4px 12px;" scope="col">NamaHdGrp</th>
                    <th style="padding: 4px 12px;" scope="col">KodeSubGrp</th>
                    <th style="padding: 4px 12px;" scope="col">NamaSubGrp</th>
                    <th style="padding: 4px 12px;" scope="col">KodeMerk</th>
                    <th style="padding: 4px 12px;" scope="col">NamaMerk</th>
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                  <tr>
                    <td class="text-center">
                      <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('{{ $tempOutstanding[$i]->NoBukti }}')"><i class="bi bi-plus"></i></button>
                    </td>
                    <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
                    <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
                    <td>{{ $tempOutstanding[$i]->KodeGdg }}</td>
                    <td>{{ $tempOutstanding[$i]->KodeHdGrp }}</td>
                    <td>{{ $tempOutstanding[$i]->NAMAHDGRP }}</td>
                    <td>{{ $tempOutstanding[$i]->KodeSubGrp }}</td>
                    <td>{{ $tempOutstanding[$i]->NamaSubGrp }}</td>
                    <td>{{ $tempOutstanding[$i]->KodeMerk }}</td>
                    <td>{{ $tempOutstanding[$i]->NAMAMERK }}</td>

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
          <div class="container-fluid" style="padding:0; margin:0; width:100%;">

                <table id="tabel2" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">PR OPN</th>
                      <th style="padding: 4px 12px;" scope="col">Gudang</th>
                      <th style="padding: 4px 12px;" scope="col">SubGrp</th>
                      <th style="padding: 4px 12px;" scope="col">Merk</th>
                      <th style="padding: 4px 12px;" scope="col">HeadGrp</th>
                    </tr>
                  </thead>


                  <tbody id="tabel2_data" class="text-left" >

                    @for ($i = 0; $i < count($tempPenerimaan); $i++)
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempPenerimaan[$i]->Nobukti }}' , 'detail' )"><i class="bi bi-info"></i></button>

                        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempPenerimaan[$i]->Nobukti }}'  )"><i class="bi bi-pen"></i></button>
                        
                        <button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('{{ $tempPenerimaan[$i]->Nobukti }}')"><i class="bi bi-key"></i></button>
                      </td>
                      <td>{{ $tempPenerimaan[$i]->Nobukti }}</td>
                      <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i]->Tanggal)) !!}</td>
                      <td>{{ $tempPenerimaan[$i]->NoPerintahOP }}</td>
                      <td>{{ $tempPenerimaan[$i]->NamaGdg }}</td>
                      <td>{{ $tempPenerimaan[$i]->NamaSubGrp ? $tempPenerimaan[$i]->NamaSubGrp : '' }}</td>
                      <td>{{ $tempPenerimaan[$i]->NAMAMERK ? $tempPenerimaan[$i]->NAMAMERK : '' }}</td>
                      <td>{{ $tempPenerimaan[$i]->NAMAHDGRP }}</td>
                    </tr>
                    @endfor

                  </tbody>
                </table>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="home2" role="tabpanel" aria-labelledby="home2-tab">
      <div class="row">
        <div class="col-12" style="overflow:auto;">
          <div class="container-fluid" style="padding:0; margin:0; width:100%;">

                <table id="tabel_oto" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">Nobukti</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">PR OPN</th>
                      <th style="padding: 4px 12px;" scope="col">Gudang</th>
                      <th style="padding: 4px 12px;" scope="col">SubGrp</th>
                      <th style="padding: 4px 12px;" scope="col">Merk</th>
                      <th style="padding: 4px 12px;" scope="col">HeadGrp</th>
                      <th style="padding: 4px 12px;" scope="col">OtoUser</th>
                      <th style="padding: 4px 12px;" scope="col">TglOto</th> 
                    </tr>
                  </thead>


                  <tbody id="tabel_oto_data" class="text-left" >

                    @for ($i = 0; $i < count($tempPenerimaan1); $i++)
                    <tr>
                      <td class="text-center">
                        <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempPenerimaan1[$i]->Nobukti }}' , 'detail' )"><i class="bi bi-info"></i></button>
                        <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempPenerimaan1[$i]->Nobukti }}'  )"><i class="bi bi-key"></i></button>
			<button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{$tempPenerimaan1[$i]->Nobukti}}')" ><i class="bi bi-printer"></i></button>
                      </td>
                      <td>{{ $tempPenerimaan1[$i]->Nobukti }}</td>
                      <td>{!! date("Y/m/d", strtotime($tempPenerimaan1[$i]->Tanggal)) !!}</td>
                      <td>{{ $tempPenerimaan1[$i]->NoPerintahOP }}</td>
                      <td>{{ $tempPenerimaan1[$i]->NamaGdg }}</td>
                      <td>{{ $tempPenerimaan1[$i]->NamaSubGrp ? $tempPenerimaan1[$i]->NamaSubGrp : '' }}</td>
                      <td>{{ $tempPenerimaan1[$i]->NAMAMERK ? $tempPenerimaan1[$i]->NAMAMERK : '' }}</td>
                      <td>{{ $tempPenerimaan1[$i]->NAMAHDGRP }}</td>
                      <td>{{ $tempPenerimaan1[$i]->OtoUser1 }}</td>
                      <td>{!! $tempPenerimaan1[$i]->TglOto1 ? date("Y/m/d", strtotime($tempPenerimaan1[$i]->TglOto1)) : '' !!}</td>
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
</div>

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -80px">
    <div class="col-8 text-left">
      <h2>Opname Barang</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()">CLOSE</button>
    </div>
  </div>

  <div id= "formAdd" class="">



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

        <div class="col-md-3">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>No PROpname</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group input-group">
          <input type="text" class="form-control" id="input_add_noperintah" placeholder="" disabled>
          <button id="buttonAddListPROpname" type="button" onclick="buttonAddListPROpname()" class="btn btn-primary" >+</button>

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
              <label>Headgroup</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodehdgrp" type="text" class="form-control" disabled>


              </div>
            </div>
          </div>

        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Kategori</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodekategori" type="text" class="form-control" disabled>
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
              </div>
            </div>
          </div>

        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Merk</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodemerk" type="text" class="form-control" disabled>
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
            <div class="col-md-10">
              <div class="input-group form-group">
                <input id="input_add_keterangan" type="text" class="form-control" onblur="onChangeHeader()">


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
              <th style="padding: 4px 12px;" scope="col">KodeBrg</th>
              <th style="padding: 4px 12px;" scope="col">NamaBrg</th>
              <th style="padding: 4px 12px;" scope="col">Satuan</th>
              <th style="padding: 4px 12px;" scope="col">Saldo Stock</th>
              <th style="padding: 4px 12px;" scope="col">Saldo Fisik</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Selisih</th>

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
  <button id="buttonSubmitKoreksi" type="button" class="btn btn-primary" onclick="submitKoreksi()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >Save</button>
</div>


  </div>
</div>

    </div>








  </div>


  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row" style="margin-top: -80px">
      <div class="col-8 text-left">
        <h2>Opname Barang</h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-danger btn-lg " style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()">CLOSE</button>
      </div>
    </div>

    <div id= "" class="">



    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />

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

          <div class="col-md-3">
            <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>No PROpname</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
            <input type="text" class="form-control" id="input_detail_noperintah" placeholder="" disabled>
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
                <label>Headgroup</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodehdgrp" type="text" class="form-control" disabled>


                </div>
              </div>
            </div>

          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Kategori</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodekategori" type="text" class="form-control" disabled>


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


                </div>
              </div>
            </div>

          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Merk</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodemerk" type="text" class="form-control" disabled>


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
              <div class="col-md-10">
                <div class="input-group form-group">
                  <input id="input_detail_keterangan" type="text" class="form-control" disabled>


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
                <th style="padding: 4px 12px;" scope="col">KodeBrg</th>
                <th style="padding: 4px 12px;" scope="col">NamaBrg</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <th style="padding: 4px 12px;" scope="col">Saldo Stock</th>
                <th style="padding: 4px 12px;" scope="col">Saldo Fisik</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Selisih</th>

              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr>

                  <td colspan=7 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>


    <div class="col-md-12 mt-2 text-right">
    <button id="buttonSubmitOtorisasi" type="button" class="btn btn-primary" onclick="submitOtorisasi()" class="btn btn-secondary" style="height: 30px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;">Otorisasi</button>
  </div>


    </div>
  </div>

      </div>


    </div>


  </div>
</div>


<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div id="" class="modal-content ">
      <div id= "modalAddListPROpname" class="showhidemodalbodyadd">
      <div class="modal-header">
          <h5 class="modal-title" id="modalAddListPROpname">PR Opname12</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
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


            <table id="tabel_add_list_propname" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                  <th style="padding: 4px 12px;" scope="col">No Bukti</th>
                  <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                  <th style="padding: 4px 12px;" scope="col">Gdg</th>
                  <th style="padding: 4px 12px;" scope="col">HdGroup</th>
                  <th style="padding: 4px 12px;" scope="col">SubGroup</th>
                  <th style="padding: 4px 12px;" scope="col">Merk</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_propname" class="text-left" >
                <tr>
                  <td class="text-center">
                    <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                    <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                  </td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
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


      {{-- <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div> --}}
      </div>


      <div id= "modalAddListGudang" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="modalAddListGudangTitle">Gudang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
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


      {{-- <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div> --}}
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
                <tr>
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


      {{-- <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div> --}}
      </div>


      </div>







    </div>
  </div>




<div class="modal fade" id="formAddItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Tambah Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>


          </div>
          <div class="row ">
            <div class="col-md-3">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                  <input type="hidden" class="form-control" id="input_modal_nourut" placeholder="" disabled>
                <input type="text" class="form-control" id="input_modal_nobukti" placeholder="No Bukti" disabled>
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
                <input type="date" class="form-control text-left" id="input_modal_tanggal" placeholder="" >
              </div>
            </div>
          </div>

            </div>



          </div>


          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modal" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">v</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modal" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
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


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" data-dismiss="modal">Batal</button>
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
  </div>









@endsection

@section('js')
<script type="text/javascript">

let tipeform = ''
let listData = []
let listDataDet = []
let listDataAdd = []
let tempDataAdd = []

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
        // $(".mainpage").hide()
        // $("#page2").show()
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

          $("#tabel_oto").DataTable({
          "lengthChange": false,
            "paging": false ,
             "columnDefs": [
            // { "type": "date", "targets": [3] },
            // {  "className": "text-right", "targets": [5] },
            // "columns" : [{"width" : "20px"}]
          ]
          });

          $("#tabel_add_list_propname").DataTable({
            "lengthChange": false,
              "paging": false ,
              // 'order': [[1, 'asc']],
              // "searching" : false,
              "columnDefs": [
            // {"targets" :[0] , 'orderable' : false}
           // {  "className": "text-center", "targets": [4] },
         ]
        });






        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
});


function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}



function loadAll () {

  console.log('loadall')
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('opnamebarangloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ""
      let rowTable2 = ""
      let rowTable3 = ""

      res.tempPenerimaan.forEach((item, i) => {
        rowTable2 += `
        <tr>
          <td class="text-center">
          <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.Nobukti}' , 'detail' )"><i class="bi bi-info"></i></button>
            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.Nobukti}'  )"><i class="bi bi-pen"></i></button>


            ${item.IsOtorisasi1 == 0 ?
              `<button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('${item.Nobukti}' , 'otorisasi' )"><i class="bi bi-key"></i></button>`
              :
              `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.Nobukti}'  )"><i class="bi bi-key"></i></button>`

            }

          </td>
          <td>${item.Nobukti}</td>
          <td>${formatDate(item.Tanggal)}</td>
          <td>${item.NoPerintahOP}</td>
          <td>${item.NamaGdg}</td>
          <td>${item.NamaSubGrp ? item.NamaSubGrp : ''}</td>
          <td>${item.NAMAMERK ? item.NAMAMERK : ''}</td>
          <td>${item.NAMAHDGRP ? item.NAMAHDGRP : '' }</td>

        </tr>
        `

      });

      res.tempPenerimaan1.forEach((item) => {
        rowTable3 += `
          <tr>
          <td class="text-center">
          <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.Nobukti}' , 'detail' )"><i class="bi bi-info"></i></button>
            ${item.IsOtorisasi1 == 0 ?
              `<button class="btn btn-primary btn-sm" type="button" onclick="buttonDetail('${item.Nobukti}' , 'otorisasi' )"><i class="bi bi-key"></i></button>`
              :
              `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.Nobukti}'  )"><i class="bi bi-key"></i></button>`
            }
	  <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item.Nobukti}')">
            <i class="bi bi-printer"></i>
	  </button>
          </td>
          <td>${item.Nobukti}</td>
          <td>${formatDate(item.Tanggal)}</td>
          <td>${item.NoPerintahOP}</td>
          <td>${item.NamaGdg}</td>
          <td>${item.NamaSubGrp ? item.NamaSubGrp : ''}</td>
          <td>${item.NAMAMERK ? item.NAMAMERK : ''}</td>
          <td>${item.NAMAHDGRP ? item.NAMAHDGRP : '' }</td>
          <td>${item.OtoUser1}</td>
          <td>${item.TglOto1 ? formatDate(item.TglOto1) : '' }</td>

        </tr>`
      });


      res.tempOutstanding.forEach((item, i) => {
          rowTable += `
          <tr>
	    <td class="text-center">
              <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('${item.NoBukti}')"><i class="bi bi-plus"></i></button>
            </td>
            <td>${item.NoBukti}</td>
            <td>${formatDate(item.Tanggal)}</td>
            <td>${item.KodeGdg}</td>
            <td>${item.KodeHdGrp}</td>
            <td>${item.NAMAHDGRP}</td>
            <td>${item.KodeSubGrp}</td>
            <td>${item.NamaSubGrp}</td>
            <td>${item.KodeMerk}</td>
            <td>${item.NAMAMERK}</td>
          </tr>`

      });
      console.log(1)
      $('#tabel').DataTable().destroy();
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
        ]
        });
        $('#tabel_oto').DataTable().destroy();
      document.getElementById("tabel_oto_data").innerHTML = rowTable3
      $("#tabel_oto").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
        ]
        });
console.log(2)
        $('#tabel2').DataTable().destroy();
        document.getElementById("tabel2_data").innerHTML = rowTable2
        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
          ]
          });
    }})

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('opnamebarangdetailCetak') !!}",
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
                  <div class="pb-1" style="width: 100%">Tanggal: `+tanggalOnly+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">No Bukti : `+dataPrint[0].Nobukti+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">BERITA ACARA STOK OPNAME</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Perintah OP</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NOPROPNAME+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Gudang</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].KodeGdg+` - `+dataPrint[0].NamaGDG+`</div>
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
                    <td rowspan="2" class="text-center" style="width: 2%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 15%">KODE</td>
                    <td rowspan="2" class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 10%">PART NUMBER</td>
                    <td rowspan="2" class="text-center" style="width: 10%">MERK</td>
                    <td rowspan="2" class="text-center" style="width: 10%">SAT</td>
                    <td colspan="2" class="text-center" style="width: 15%">SALDO FISIK</td>
                    <td colspan="2" class="text-center" style="width: 15%">SALDO SYSTEM</td>
                    <td colspan="2" class="text-center" style="width: 15%">SELISIH</td>
                  </tr>
                  <tr>
                    <td class="text-center">QTY</td>
                    <td class="text-center">RP</td>

                    <td class="text-center">QTY</td>
                    <td class="text-center">RP</td>

                    <td class="text-center">QTY</td>
                    <td class="text-center">RP</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalFisik = 0;
    let grandTotalFisik2 = 0;
    let grandTotalSistem = 0;
    let grandTotalSistem2 = 0;
    let grandTotalSelisih = 0;
    let grandTotalSelisih2 = 0;

    dataPrint.forEach(item => {

      if (item.QntOpname) {
        grandTotalFisik += Number(item.QntOpname) || 0;
      }

      if (item.SALDOOPNAME) {
        grandTotalFisik2 += Number(item.SALDOOPNAME) || 0;
      }

      if (item.SaldoComp) {
        grandTotalSistem += Number(item.SaldoComp) || 0;
      }

      if (item.TOTALSALDO) {
        grandTotalSistem2 += Number(item.TOTALSALDO) || 0;
      }

      if (item.QNTSELISIH) {
        grandTotalSelisih += Number(item.QNTSELISIH) || 0;
      }

      if (item.SALDOSELISIH) {
        grandTotalSelisih2 += Number(item.SALDOSELISIH) || 0;
      }
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
               style="width: 15%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: left"
               style="width: 30%;">${itemSub.namaBrg}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.PartNumber}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: text-center"
               style="width: 10%;">${itemSub.SAT1}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.QntOpname}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.SALDOOPNAME}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.SaldoComp ? parseFloat(itemSub.SaldoComp).toFixed(2) : ''}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.TOTALSALDO}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.QNTSELISIH}</td>
         <td class="text-align: text-right"
               style="width: 15%;">${itemSub.SALDOSELISIH}</td>
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

            <div style="width:5%; text-align:right;">
              ${grandTotalFisik.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalFisik2.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSistem.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSistem2.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSelisih.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

            <div style="width:5%; text-align:right;">
              ${grandTotalSelisih2.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </div>

          </div>

         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Kabag IT</td>
               <td class="no-border text-center" style="width: 20%">Direksi</td>
               <td class="no-border text-center" style="width: 20%">Supervisor Stok</td>
               <td class="no-border text-center" style="width: 20%">Kepala Gudang</td>
               <td class="no-border text-center" style="width: 20%">Admin Gudang</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
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
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
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
    tempDataAdd = []
    let _token = $("#_token").val();

    let akses = $("#akses_istambah").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }


    $.ajax({
      url: "{!! url('opnamebaranglistadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti
      },
      success: function(res) {
        console.log("RES !")
        console.log(res)
        // return
        // return
        listDataAdd  = res
        let rowTable = `
        `
        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}" onchange="onchangeChecklist(${i},this.id)"></td>

          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${item.QNT ? parseFloat(item.QNT).toFixed(2) : '0.00'}</td>

          </tr>`
        });

        document.getElementById("input_modal_tanggal").valueAsDate = new Date()

        document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
        setNewNoBukti()

      $("#formAddItem").modal('toggle');



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })





}


function buttonDetail (nobukti, tipe = "detail") {
  let _token = $("#_token").val();
  listData = []
  if (tipe =="otorisasi") {
    let akses = $("#akses_isotorisasi1").val();
    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
  }

  $.ajax({
    url: "{!! url('opnamebarangdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listDataDet = res
      // return
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
      listDataDet.forEach((item, i) => {

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

              rowTable += `
                <tr>

                  <td>${item.kodebrg}</td>
                  <td>${item.namaBrg}</td>
                  <td class="text-center">${item.Satuan}</td>
                  <td class="text-right">${item.SaldoComp ? parseFloat(item.SaldoComp).toFixed(2) : '0.00'}</td>
                  <td class="text-right">${item.QntOpname ? parseFloat(item.QntOpname).toFixed(2) : '0.00'}</td>

                  <td class="text-right">${item.Harga ? parseFloat(item.Harga).toFixed(2) : '0.00'}</td>
                  <td class="text-right">${item.Selisih ? parseFloat(item.Selisih).toFixed(2) : '0.00'}</td>


                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable
      document.getElementById("input_detail_nobukti").value = listDataDet[0].Nobukti
      document.getElementById("input_detail_nourut").value = listDataDet[0].Nourut
      document.getElementById("input_detail_tanggal").value = formatDate(listDataDet[0].Tanggal)
      document.getElementById("input_detail_keterangan").value = listDataDet[0].note
      document.getElementById("input_detail_gudang").value = listDataDet[0].KodeGdg
      document.getElementById("input_detail_kodehdgrp").value = listDataDet[0].KodeHdGrp
      document.getElementById("input_detail_kodekategori").value = listDataDet[0].KodeSubGrp
      document.getElementById("input_detail_kodemerk").value = listDataDet[0].KodeMerk
      document.getElementById("input_detail_noperintah").value = listDataDet[0].NoPerintahOP

      if (tipe == 'otorisasi') {
        $("#buttonSubmitOtorisasi").show()

      } else {
        $("#buttonSubmitOtorisasi").hide()

      }
      $('.mainpage').hide()
      $('#page3').show()


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}




function onchangeChecklist ( index ,id) {
  // console.log(index, data,id)
  let data = listDataAdd[index]
  console.log(data)


  if (document.getElementById(`add_checkbox${index}`).checked) {

    tempDataAdd.push(data)

  } else {
    console.log(data)
    let check = tempDataAdd.findIndex(el => el.KodeBrg === data.KodeBrg );
    console.log('hapus')
    tempDataAdd.splice(check, 1)
  }
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
          url: "{!! url('opnamebarangspbatalotorisasi') !!}",
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

function submitOtorisasi (nobukti) {
  console.log(nobukti);

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('opnamebarangspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      alertify.success('Berhasil update otorisasi');
      loadAll();
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan silahkan refresh browser');
    }
  });
}

// function submitOtorisasi () {

//   let _token = $("#_token").val();
//   let nobukti = $("#input_detail_nobukti").val();
//   $.ajax({
//     url: "{!! url('opnamebarangspotorisasi') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       nobukti

//     },
//     success: function(res) {
//       alertify.success('Berhasil update otorisasi')
//       loadAll()
//       buttonCloseForm()




//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }

//   })


// }

function submitAdd () {
  let checkDate = new Date($("#input_modal_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  console.log(periode_bulan, periode_tahun)
  console.log(checkDate.getMonth() +1 ,checkDate.getFullYear() )
  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let tempData = []



  // console.log(listDataAdd)
  let checkQnt = 0
  listDataAdd.forEach((item, i) => {
    console.log(i)
    if (document.getElementById(`add_checkbox${i}`).checked) {
      // if (Number(x)  < 0) {
      //         checkQnt = 1
      //       }
      tempData.push({
        ...item,
      })
    }
  });
  console.log(tempData)
  // if (checkQnt) {
  //   alertify.warning("Qnt < 0")
  //   return
  // }
  if (!tempData.length) {
    alertify.warning("Tidak ada data dipilih")
    return
  }
  let _token  = $("#_token").val()
  let nob = tempData[0].NOBUKTI
  let tanggal = $("#input_modal_tanggal").val();
  let nobukti = $("#input_modal_nobukti").val();
  let nourut = $("#input_modal_nourut").val();
  let jmlrecord = 0
  $.ajax({
      url: "{!! url('opnamebarangspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : tempData ,
        nobukti,
        nourut,
        tanggal,
        jmlrecord,
        nob
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('OPN telah ditambah');

          tipeform = 'edit'
          loadAll()
          $("#formAddItem").modal('toggle');
          buttonKoreksi(nobukti)

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

function onChangeHeader () {

  if (tipeform == 'add') {
    return
  }

  let _token = $("#_token").val();
  let nobukti = $("#input_add_nobukti").val();
  let keterangan = $("#input_add_keterangan").val();
  console.log(keterangan, nobukti)
  $.ajax({
    url: "{!! url('opnamebarangspupdateheader') !!}",
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


function submitKoreksi () {
  console.log('submitedit')

  if (tipeform == 'add') {
    return
  }




  if (listData[0].nonbap == 1) {
    let tempData = []



    // console.log(listDataAdd)
    let checkQnt = 0
    listData.forEach((item, i) => {
      console.log(i)
      // if (document.getElementById(`add_checkbox${i}`).checked) {
        console.log($(`#koreksi_qnt${i}`).val())
        let x = $(`#koreksi_qnt${i}`).val()
        let inputqntopname = $(`#koreksi_qntopname${i}`).val()
        if (Number(inputqntopname)  < 0) {
                checkQnt = 1
              }
        let dataQntComp = item.QtyComp ? item.QtyComp : 0
        let inputselisih = Number(inputqntopname) - Number(dataQntComp)
        let inputqntdb = 0
        let inputqntcr = 0
        if (inputselisih < 0) {
          inputqntcr = Math.abs(inputselisih)
          inputselisih = Math.abs(inputselisih)

        } else {
          inputqntdb = Math.abs(inputselisih)
          inputselisih = Math.abs(inputselisih)

        }
        if (Number(x)  < 0) {
                checkQnt = 1
              }
        tempData.push({
          ...item,
          qntedit : x,
          inputqntopname,
          inputselisih,
          inputqntdb,
          inputqntcr


        })
        console.log(tempData , x)
      })
    console.log(tempData)
    if (checkQnt) {
      alertify.warning("Qnt < 0")
      return
    }
    let _token  = $("#_token").val()
    let tanggal = $("#input_add_tanggal").val();
    let nobukti = $("#input_add_nobukti").val();
    let nourut = $("#input_add_nourut").val();
    let jmlrecord = 1

    console.log({
      _token,
      tempData : tempData ,
      nobukti,
      nourut,
      tanggal,
      jmlrecord,
    })
    $.ajax({
        url: "{!! url('opnamebarangspkoreksinonbap') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          tempData : tempData ,
          nobukti,
          nourut,
          tanggal,
          jmlrecord,
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('BAP telah diedit');

            tipeform = 'edit'
            loadAll()
            // $("#formAddItem").modal('toggle');
            refreshDataTable(nobukti)

          }

        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })


  } else {
    let tempData = []



    // console.log(listDataAdd)
    let checkQnt = 0
    listData.forEach((item, i) => {
      console.log(i)
      // if (document.getElementById(`add_checkbox${i}`).checked) {
        console.log($(`#koreksi_qnt${i}`).val())
        let x = $(`#koreksi_qnt${i}`).val()
        if (Number(x)  < 0) {
                checkQnt = 1
              }
        tempData.push({
          ...item,
          qntedit : x
        })
        console.log(tempData , x)
      })
    console.log(tempData)
    if (checkQnt) {
      alertify.warning("Qnt < 0")
      return
    }
    let _token  = $("#_token").val()
    let tanggal = $("#input_add_tanggal").val();
    let nobukti = $("#input_add_nobukti").val();
    let nourut = $("#input_add_nourut").val();
    let jmlrecord = 1

    console.log({
      _token,
      tempData : tempData ,
      nobukti,
      nourut,
      tanggal,
      jmlrecord,
    })
    $.ajax({
        url: "{!! url('opnamebarangspkoreksi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          tempData : tempData ,
          nobukti,
          nourut,
          tanggal,
          jmlrecord,
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('BAP telah diedit');

            tipeform = 'edit'
            loadAll()
            // $("#formAddItem").modal('toggle');
            refreshDataTable(nobukti)

          }

        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })



  }

}



function buttonDeleteItem (index) {
  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    let barangDelete = listData[index]



    console.log(barangDelete)

    // return


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item '+ barangDelete.kodebrg +' ?',
        function() {


            let nobukti = $("#input_add_nobukti").val();
            let urut = barangDelete.Urut


            let _token  = $("#_token").val()

            $.ajax({
                url: "{!! url('opnamebarangspdelete') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  nobukti,
                  urut
                },
                success: function(res) {
                  console.log(res ,'!')

                  if (res == 1) {
                    // $("#form").modal('toggle')
                    alertify.success('Item telah dihapus');

                    tipeform = 'edit'
                    loadAll()
                    refreshDataTable(nobukti)

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


function buttonAddPickGudang (index, kode,nama) {


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

function buttonAddListPROpname () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('opnamebaranglistpropname') !!}",
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickPROpname(${i}, '${item.NoBukti}' , '${item.KodeGdg}')" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.NoBukti}</td>
        <td>${item.Tanggal ? formatDate(item.Tanggal) : ''}</td>
        <td>${item.KodeGdg}</td>
        <td>${item.KodeHdGrp}</td>
        <td>${item.KodeSubGrp}</td>
        <td>${item.KodeMerk}</td>
        </tr>`
      });


      $('#tabel_add_list_propname').DataTable().destroy();
      document.getElementById("tabel_data_add_list_propname").innerHTML = rowTable

      $("#tabel_add_list_propname").DataTable({
        "lengthChange": false,
          "paging": false ,
          // 'order': [[1, 'asc']],
          // "searching" : false,
          "columnDefs": [
        // {"targets" :[0] , 'orderable' : false}
       // {  "className": "text-center", "targets": [4] },
     ]
    });

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListPROpname').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("PR Opname tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddPickPROpname (index,nopropname, kodegdg) {

  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }


  let _token = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()


  let tanggal = $("#input_add_tanggal").val()


  $.ajax({
    url: "{!! url('opnamebarangspaddpropname') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      kodegdg,
      nourut,
      tanggal,
      nopropname

    },
    success: function(res) {
      console.log(res)
      if(res == 2) {
        setNewNoBukti()
        alertify.warning("No bukti telah di refresh, silahkan pilih ulang")

        return
      }

      if (res == 3) {
        alertify.warning("Data tidak ditemukkan")
        return
      }

      if (res == 1) {
        alertify.success("Berhasil menanmbah OPN")
        buttonKoreksi(nobukti)
        loadAll()
        $("#form").modal('toggle')
        return
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListMerk () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistmerk') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickMerk('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODEMERK}</td>
        <td>${item.NAMAMERK}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickMerk(${i},'${item.KODEMERK}' , '${item.NAMAMERK}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      $('#tabel_add_list_merk').DataTable().destroy();
      document.getElementById("tabel_data_add_list_merk").innerHTML = rowTable

      $("#tabel_add_list_merk").DataTable({
        "lengthChange": false,
          "paging": false ,
          // 'order': [[1, 'asc']],
          // "searching" : false,
          "columnDefs": [
        // {"targets" :[0] , 'orderable' : false}
       // {  "className": "text-center", "targets": [4] },
     ]
    });

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListMerk').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListSubKategori () {
  let _token = $("#_token").val();
  let kode = $("#input_add_kodehdgrp").val();
  let kode1 = $("#input_add_kodekategori").val();
  if ( kode == '-' || !kode) {
    alertify.warning("Pilih headgroup")
    return


  }

  if ( kode1 == '-' || !kode1) {
    alertify.warning("Pilih kategori")
    return


  }

  $.ajax({
    url: "{!! url('perintahopnamelistsubkategori') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode,
      kode1
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubKategori('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Urut}</td>
        <td>${item.Keterangan}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubKategori(${i},'${item.Urut}' , '${item.Keterangan}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });








      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      document.getElementById("modalAddListGudangTitle").innerHTML= 'Sub Kategori'

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonAddListKategori () {
  let _token = $("#_token").val();
  let kode = $("#input_add_kodehdgrp").val();

  if ( kode == '-' || !kode) {
    alertify.warning("Pilih headgroup")
    return


  }

  $.ajax({
    url: "{!! url('perintahopnamelistkategori') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickKategori('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeSubGrp}</td>
        <td>${item.NamaSubGrp}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickKategori(${i},'${item.KodeSubGrp}' , '${item.NamaSubGrp}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });








      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      document.getElementById("modalAddListGudangTitle").innerHTML= 'Kategori'

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListHeadGroup () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistheadgroup') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickHeadGroup('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeHDGrp}</td>
        <td>${item.NamaHDGRP}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickHeadGroup(${i},'${item.KodeHDGrp}' , '${item.NamaHDGRP}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }

      document.getElementById("tabel_data_add_list_gudang").innerHTML = rowTable
      document.getElementById("modalAddListGudangTitle").innerHTML= 'Headgroup'

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListGudang').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Merk tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListGudang () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('perintahopnamelistgudang') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      // listLawan  = res
      let rowTable = `
      <tr class="text-center">
      <td >-</td>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeGdg}</td>
        <td>${item.Nama}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang(${i},'${item.KodeGdg}' , '${item.Nama}'  )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
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

// function lockForm (value = false) {
//   document.getElementById("input_add_tanggal").disabled = value
//   document.getElementById("input_add_tanggalpelaksanaan").disabled = value
//   document.getElementById("input_add_tanggalcutoff").disabled = value
//   document.getElementById("input_add_keterangan").disabled = value
//   document.getElementById("buttonAddListHeadGroup").disabled = value
//   document.getElementById("buttonAddListGudang").disabled = value
//   document.getElementById("buttonAddListMerk").disabled = value
//   document.getElementById("buttonAddListKategori").disabled = value
//   document.getElementById("buttonAddListSubKategori").disabled = value
//
// }

// function cleanFormAdd () {
//   document.getElementById("input_add_tanggal").valueAsDate = new Date()
//   document.getElementById("input_add_tanggalpelaksanaan").value = ''
//   document.getElementById("input_add_tanggalcutoff").value = ''
//   document.getElementById("input_add_keterangan").value = ''
//   document.getElementById("input_add_kodehdgrp").value = '-'
//   document.getElementById("input_add_gudang").value = '-'
//   document.getElementById("input_add_kodemerk").value = '-'
//   document.getElementById("input_add_kodesubkategori").value = '-'
//   document.getElementById("input_add_kodekategori").value = '-'
//   document.getElementById("addTableData").innerHTML = `<tr><td colspan=6 class="text-center">Belum ada data</td></tr>`
//
// }

function refreshDataTable (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('opnamebarangdetailkoreksi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti

      },
      success: function(res) {
        console.log(res)
        listData = res
        // return
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

          // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
          // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

                rowTable += `
                  <tr>

                    <td>${item.kodebrg}</td>
                    <td>${item.namaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">${item.SaldoComp ? parseFloat(item.SaldoComp).toFixed(2) : '0.00'}</td>
                    ${item.nonbap == 1 ?
                      `<td class="text-center"><input onchange="" id="koreksi_qntopname${i}"  class="text-right" type="number" min=0 value=${item.QntOpname ? parseFloat(item.QntOpname).toFixed(2) : '0.00'}></td>`
                      :
                      `<td class="text-right">${item.QntOpname ? parseFloat(item.QntOpname).toFixed(2) : '0.00'}</td>`

                    }

                    <td class="text-center"><input onchange="" id="koreksi_qnt${i}"  class="text-right" type="number" min=0 value=${item.Harga ? parseFloat(item.Harga).toFixed(2) : '0.00'}></td>
                    <td class="text-right">${item.Selisih ? parseFloat(item.Selisih).toFixed(2) : '0.00'}</td>

                    <td class='text-center'>
                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>


                    </td>


                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_nobukti").value = listData[0].Nobukti
        document.getElementById("input_add_nourut").value = listData[0].Nourut
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)
        document.getElementById("input_add_keterangan").value = listData[0].note
        document.getElementById("input_add_gudang").value = listData[0].KodeGdg
        document.getElementById("input_add_kodehdgrp").value = listData[0].KodeHdGrp
        document.getElementById("input_add_kodekategori").value = listData[0].KodeSubGrp
        document.getElementById("input_add_kodemerk").value = listData[0].KodeMerk
        document.getElementById("input_add_noperintah").value = listData[0].NoPerintahOP



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
      }

    })
}

function lockForm (value = false) {
  document.getElementById("input_add_tanggal").disabled = value
  if (value) {
    $("#buttonAddListPROpname").hide()
  } else {

    $("#buttonAddListPROpname").show()
  }
}

function cleanFormAdd () {


}

function buttonAddNonBAP () {

  let akses = $("#akses_istambah").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
    tipeform = 'add'
    lockForm(false)

    document.getElementById("input_add_tanggal").valueAsDate = new Date()
    document.getElementById("input_add_kodehdgrp").value = ''
    // console.log('12')
    document.getElementById("input_add_gudang").value = ''
    document.getElementById("input_add_kodekategori").value = ''
    // document.getElementById("input_add_kodesubkategori").value = ''
    // console.log('34')
    document.getElementById("input_add_noperintah").value = ''
    document.getElementById("input_add_kodemerk").value = ''
    document.getElementById("input_add_keterangan").value = ''

    document.getElementById("addTableData").innerHTML = '<tr><td colspan=8 class="text-center">Belum ada data</td></tr>'



    setNewNoBukti()

    $('.mainpage').hide()
    $('.showhideitem').hide()
    $('#page2').show()
}

function buttonKoreksi (nobukti) {
  tipeform = 'edit'
  listData = []
  lockForm(true)

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  // lockForm(true)
  refreshDataTable(nobukti)
  // return


  if (listData.length) {
    console.log(listData[0])
    console.log(listData[0].IsOtorisasi1)
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


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'OPN'
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
      document.getElementById("input_modal_nobukti").value = res[0].Nobukti
      document.getElementById("input_modal_nourut").value = res[0].Nourut
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
