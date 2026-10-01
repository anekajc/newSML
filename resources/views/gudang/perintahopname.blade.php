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
{{-- end tampilan search bar 2 --}}
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
      <h2>Perintah Opname</h2>
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
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonAdd()">Add Perintah Opname</button>
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
      <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left;">POP Belum Otorisasi</a>
      <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
      aria-controls="nav-profile" aria-selected="false"
      style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
      POP Sudah Otorisasi
      </a>
    </div>
  </nav>
</div>
</div>
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  {{-- Utama --}}
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
                    <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                    <th style="padding: 4px 12px;"  scope="col">Gudang</th>
                    <th style="padding: 4px 12px;"  scope="col">Kode Hdgrp</th>
                    <th style="padding: 4px 12px;"  scope="col">Nama Hdgrp</th>
                    <th style="padding: 4px 12px;"  scope="col">Kode Subgrp</th>
                    <th style="padding: 4px 12px;"  scope="col">Nama Subgrp</th>
                    <th style="padding: 4px 12px;"  scope="col">Kode Merk</th>
                    <th style="padding: 4px 12px;"  scope="col">Merk</th>
                  </tr>
                </thead>

                <tbody id="tabel_data" class="text-left" >
                  @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'detail')"><i class="bi bi-info"></i></button>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'edit')"><i class="bi bi-pen"></i></button>
                    @if ($tempOutstanding[$i][0]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('{{ $tempOutstanding[$i][0]->NoBukti }}' , 'otorisasi')"><i class="bi bi-key"></i></button>

                    @endif
                    <!-- <button class="btn btn-info btn-sm" type="button" onclick="buttonPrint('{{ $tempOutstanding[$i][0]->NoBukti }}' )"><i class="bi bi-printer"></i></button> -->
                  </td>
                  <td>{{ $tempOutstanding[$i][0]->NoBukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i][0]->Tanggal)) !!}</td>
                  <td>{{ $tempOutstanding[$i][0]->Keterangan }}</td>
                  <td>{{ $tempOutstanding[$i][0]->KodeGdg }}</td>
                  <td>{{ $tempOutstanding[$i][0]->KodeHdGrp }}</td>
                  <td>{{ $tempOutstanding[$i][0]->NAMAHDGRP }}</td>
                  <td>{{ $tempOutstanding[$i][0]->KodeSubGrp }}</td>
                  <td>{{ $tempOutstanding[$i][0]->NamaSubGrp }}</td>
                  <td>{{ $tempOutstanding[$i][0]->KodeMerk }}</td>
                  <td>{{ $tempOutstanding[$i][0]->NAMAMERK }}</td>
                </tr>
                  @endfor
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
                      <th style="padding: 4px 12px;"  scope="col">Actions</th>
                      <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                      <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;"  scope="col">Keterangan</th>
                      <th style="padding: 4px 12px;"  scope="col">Gudang</th>
                      <th style="padding: 4px 12px;"  scope="col">Kode Hdgrp</th>
                      <th style="padding: 4px 12px;"  scope="col">Nama Hdgrp</th>
                      <th style="padding: 4px 12px;"  scope="col">Kode Subgrp</th>
                      <th style="padding: 4px 12px;"  scope="col">Nama Subgrp</th>
                      <th style="padding: 4px 12px;"  scope="col">Kode Merk</th>
                      <th style="padding: 4px 12px;"  scope="col">Merk</th>
                      <th style="padding: 4px 12px;"  scope="col">OtoUser</th>
                      <th style="padding: 4px 12px;"  scope="col">TglOto</th>
                    </tr>
                  </thead>
                  <tbody id="tabel2_data" class="text-left">
                    @for ($i = 0; $i < count($tempPenerimaan); $i++)
                      <tr>
                        <td class='text-center'>
                          <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempPenerimaan[$i][0]->NoBukti }}' , 'detail')"><i class="bi bi-info"></i></button>
                          @if ($tempPenerimaan[$i][0]->IsOtorisasi1 == 1)
                          <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempPenerimaan[$i][0]->NoBukti }}' , 'edit')"><i class="bi bi-key"></i></button>
                          @else
                          <button class="btn btn-primary btn-sm" type="button" onclick="buttonDetail('{{ $tempPenerimaan[$i][0]->NoBukti }}' , 'otorisasi')"><i class="bi bi-key"></i></button>

                          @endif
                          <!-- <button class="btn btn-info btn-sm" type="button" onclick="buttonPrint('{{ $tempPenerimaan[$i][0]->NoBukti }}' )"><i class="bi bi-printer"></i></button> -->
			  <button style="" class="btn btn-primary btn-sm" type="button"   onclick="submitPrint('{{$tempPenerimaan[$i][0]->NoBukti}}')" ><i class="bi bi-printer"></i></button>
                        </td>
                        <td>{{ $tempPenerimaan[$i][0]->NoBukti }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempPenerimaan[$i][0]->Tanggal)) !!}</td>
                        <td>{{ $tempPenerimaan[$i][0]->Keterangan }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->KodeGdg }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->KodeHdGrp }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->NAMAHDGRP }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->KodeSubGrp }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->NamaSubGrp }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->KodeMerk }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->NAMAMERK }}</td>
                        <td>{{ $tempPenerimaan[$i][0]->OtoUser1 }}</td>
                        <td>{!! $tempPenerimaan[$i][0]->TglOto1 ? date("Y/m/d", strtotime($tempPenerimaan[$i][0]->TglOto1)) : '' !!}</td>
                      </tr>
                    @endfor
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

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row" style="margin-top: -80px">
    <div class="col-8 text-left">
      <h2>Perintah Opname</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="
      height: 30px; 
      margin-top: 20px; 
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
            <label>Tgl Plk.</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="date" class="form-control text-left" id="input_add_tanggalpelaksanaan" placeholder="" disabled>
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
                <input id="input_add_keterangan" type="text" class="form-control" disabled>

                <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

              </div>
            </div>
          </div>

        </div>

        <div class="col-md-3">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>Tgl Cutoff</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="date" class="form-control text-left" id="input_add_tanggalcutoff" placeholder="" disabled>
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

                <button id="buttonAddListHeadGroup" type="button" onclick="buttonAddListHeadGroup()" class="btn btn-primary"  >+</button>

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

                <button id="buttonAddListKategori" type="button" onclick="buttonAddListKategori()" class="btn btn-primary"  >+</button>

              </div>
            </div>
          </div>

        </div>

        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>SubKategori</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_kodesubkategori" type="text" class="form-control" disabled>

                <button id="buttonAddListSubKategori" type="button" onclick="buttonAddListSubKategori()" class="btn btn-primary">+</button>

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

                <button id="buttonAddListMerk" type="button" onclick="buttonAddListMerk()" class="btn btn-primary"  >+</button>

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
              <th style="padding: 4px 12px;" scope="col">Urut</th>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Satuan</th>
              <th style="padding: 4px 12px;" scope="col">Qty Saldo</th>

              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=6 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddItem" type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;">+ Tambah</button>
</div>


  </div>
</div>

    </div>








  </div>


  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row" style="margin-top: -80px">
      <div class="col-8 text-left">
        <h2>Perintah Opname</h2>
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
              <label>Tgl Plk.</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="date" class="form-control text-left" id="input_detail_tanggalpelaksanaan" placeholder="" disabled>
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

          <div class="col-md-3">
            <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Tgl Cutoff</label>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <input type="date" class="form-control text-left" id="input_detail_tanggalcutoff" placeholder="" disabled>
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

          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>SubKategori</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_kodesubkategori" type="text" class="form-control" disabled>


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
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <th style="padding: 4px 12px;" scope="col">Qty Saldo</th>


              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=5 class="text-center">Belum ada data</td>

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
        <button type="button" class="page3showhide printshowhide btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="submitPrint()"  >Print</button>
      </div>
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
      <div class="modal-body" style="margin-top:-30px;">

        <div class="container-fluid mt-4">
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


      {{-- <div id="" class="modal-footer ">
        <button type="button" class="btn btn-secondary" onclick="buttonAddListBatal()" >Batal</button>
      </div> --}}
      </div>


      </div>







    </div>
  </div>
<!-- End modal add-->



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
                  <!-- <input type="hidden" class="form-control" id="input_modal_nourut" placeholder="" disabled> -->
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
                <input type="date" class="form-control text-left" id="input_modal_tanggal" placeholder="" disabled>
              </div>
            </div>
          </div>

            </div>


            <div class="col-md-3 ">
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
                    <input id="input_modal_gudang" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>

          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Group</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_hdgrp" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-3 ">
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
                    <input id="input_modal_kategori" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>
            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>SubKategori</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_subkategori" type="text" class="form-control" disabled>


                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-3 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>merk</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_merk" type="text" class="form-control" disabled>


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
                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modal" class="text-left" >

                <tr >

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
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="submitAddItem()">Submit</button>
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

        // $('.mainpage').hide()
        // $('#page2').show()
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


function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}


function loadAll () {
  console.log('loadall')
  $.ajax({
    url: "{!! url('perintahopnameloadall') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res)

      // TAB 1
      let rowTable1 = ""
      res.tempOutstanding.forEach((item) => {
        rowTable1 += `
          <tr>
            <td class='text-center'>
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>
              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NoBukti}' , 'edit')"><i class="bi bi-pen"></i></button>
              ${item[0].IsOtorisasi1 == 1 ?
                `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item[0].NoBukti}' , 'edit')"><i class="bi bi-key"></i></button>` :
                `<button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('${item[0].NoBukti}' , 'otorisasi')"><i class="bi bi-key"></i></button>`
              }
            </td>
            <td>${item[0].NoBukti}</td>
            <td>${formatDate(item[0].Tanggal)}</td>
            <td>${item[0].Keterangan}</td>
            <td>${item[0].KodeGdg}</td>
            <td>${item[0].KodeHdGrp}</td>
            <td>${item[0].NAMAHDGRP}</td>
            <td>${item[0].KodeSubGrp || '-'}</td>
            <td>${item[0].NamaSubGrp || '-'}</td>
            <td>${item[0].KodeMerk || '-'}</td>
            <td>${item[0].NAMAMERK || '-'}</td>
          </tr>
        `
      });

      $('#tabel').DataTable().destroy();
      document.getElementById("tabel_data").innerHTML = rowTable1
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": false
      });

      // TAB 2 
      let rowTable2 = ""
      res.tempPenerimaan.forEach((item) => {
        rowTable2 += `
          <tr>
            <td class='text-center'>
              <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>
              ${item[0].IsOtorisasi1 == 1 ?
                `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item[0].NoBukti}' , 'edit')"><i class="bi bi-key"></i></button>` :
                `<button class="btn btn-primary btn-sm" type="button" onclick="buttonDetail('${item[0].NoBukti}' , 'otorisasi')"><i class="bi bi-key"></i></button>`
              }
	      <button class="btn btn-primary btn-sm" title="Print" onclick="submitPrint('${item[0].NoBukti}')">
                <i class="bi bi-printer"></i>
              </button>
            </td>
            <td>${item[0].NoBukti}</td>
            <td>${formatDate(item[0].Tanggal)}</td>
            <td>${item[0].Keterangan}</td>
            <td>${item[0].KodeGdg}</td>
            <td>${item[0].KodeHdGrp}</td>
            <td>${item[0].NAMAHDGRP}</td>
            <td>${item[0].KodeSubGrp || '-'}</td>
            <td>${item[0].NamaSubGrp || '-'}</td>
            <td>${item[0].KodeMerk || '-'}</td>
            <td>${item[0].NAMAMERK || '-'}</td>
            <td>${item[0].OtoUser1 || ''}</td>
            <td>${item[0].TglOto1 ? formatDate(item[0].TglOto1) : ''}</td>
          </tr>
        `
      });

      $('#tabel2').DataTable().destroy();
      document.getElementById("tabel2_data").innerHTML = rowTable2
      $("#tabel2").DataTable({
        "lengthChange": false,
        "paging": false
      });
    }
  })
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('perintahopnamedetailCetak') !!}",
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
    let tanggalcutoff = dataPrint[0].tglcutoff.split(' ')[0];
    let tanggalpelaksanaan = dataPrint[0].TglPelaksanaan.split(' ')[0];

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
                  <div class="pb-1" style="width: 100%">Gudang : `+dataPrint[0].Kodegdg+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tgl Cut Off: `+tanggalcutoff+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Tgl Opname : `+tanggalpelaksanaan+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>


              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">PERINTAH OPNAME</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No Bukti</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].NoBukti+`</div>
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
                    <td class="text-center" style="width: 15%">KODE BARANG</td>
                    <td class="text-center" style="width: 30%">URAIAN BARANG</td>
                    <td class="text-center" style="width: 10%">PART NO</td>
                    <td class="text-center" style="width: 10%">MERK</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 10%">SALDO FISIK</td>
                    <td class="text-center" style="width: 10%">SALDO KARTU</td>
                    <td class="text-center" style="width: 10%">SELISIH</td>
                    <td class="text-center" style="width: 10%">KETERANGAN</td>
                  </tr>
                </thead> `;

    let z = 0
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalFisik = 0;
    let grandTotalKartu = 0;
    let grandTotalSelisih = 0;

    dataPrint.forEach(item => {

      if (item.saldofisik) {
        grandTotalFisik += Number(item.saldofisik) || 0;
      }

      if (item.saldokartu) {
        grandTotalKartu += Number(item.saldokartu) || 0;
      }

      if (item.selisih) {
        grandTotalSelisih += Number(item.selisih) || 0;
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
               style="width: 15%;  ">${itemSub.Kodebrg}</td>
         <td class="text-align: left"
               style="width: 30%;">${itemSub.NAMABRG}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.PartNumber}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: text-center"
               style="width: 5%;">${itemSub.Satuan}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.saldofisik}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.saldokartu}</td>
         <td class="text-align: text-right"
               style="width: 10%;">${itemSub.selisih}</td>
         <td class="text-align: left"
               style="width: 10%;">${itemSub.keterangan}</td>
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

         <div style="display:flex; justify-content:space-between; width:300px">
         <h5 style="margin:0">
          Jumlah : ${grandTotalFisik.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
         </h5>
         <h5 style="margin:0">
            ${grandTotalKartu.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
         </h5>
         <h5 style="margin:0">
            ${grandTotalSelisih.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
         </h5>
         </div>

         </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px ; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Adm.Gudang</td>
               <td class="no-border text-center" style="width: 20%">Supervisor Stock</td>
               <td class="no-border text-center" style="width: 20%">PIC Gudang</td>
               <td class="no-border text-center" style="width: 20%">Kepala Gudang</td>
               <td class="no-border text-center" style="width: 20%">Dibuat Oleh</td>
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

function buttonAddItem () {
    tempDataAdd = []
    let _token = $("#_token").val();
    let nobukti = $("#input_add_nobukti").val();
    let kodehdgrp = $("#input_add_kodehdgrp").val();
    let kodekategori = $("#input_add_kodekategori").val();
    let kodesubkategori = $("#input_add_kodesubkategori").val();
    let gudang = $("#input_add_gudang").val();
    let kodemerk = $("#input_add_kodemerk").val();
    let tanggal = $("#input_add_tanggal").val();

    if (!kodehdgrp || !gudang ||  gudang == '-' || kodehdgrp == '-') {
      alertify.warning("Kode gudang dan group harus diisi")
      return
    }

    let checkDate = new Date($("#input_add_tanggal").val())
    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value


    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    document.getElementById("input_modal_nobukti").value = nobukti
    document.getElementById("input_modal_hdgrp").value = kodehdgrp
    document.getElementById("input_modal_kategori").value = kodekategori
    document.getElementById("input_modal_subkategori").value = kodesubkategori
    document.getElementById("input_modal_gudang").value = gudang
    document.getElementById("input_modal_tanggal").value = tanggal
    document.getElementById("input_modal_merk").value = kodemerk


    $.ajax({
      url: "{!! url('perintahopnamelistbarang') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        gudang,
        kodehdgrp,
        kodekategori,
        kodesubkategori,
        kodemerk,
        tanggal
      },
      success: function(res) {
        console.log("RES !")
        console.log(res)
        // return
        listDataAdd  = res
        let rowTable = `
        `
        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}" onchange="onchangeChecklist(${i},this.id)"></td>

          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-center">${item.Satuan}</td>
          <td class="text-right">${item.Qnt1 ? parseFloat(item.Qnt1).toFixed(2) : ''}</td>
          </tr>`
        });


        $('#tabel_add_list_modal').DataTable().destroy();
        document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable

        $("#tabel_add_list_modal").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
       ]
      });

      $("#formAddItem").modal('toggle');



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })





}




function refreshDataTableDetail (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('perintahopnamespdetail') !!}",
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

        // dataTableAdd = res

        let rowTable = ``
        listData.forEach((item, i) => {

          // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
          // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

                rowTable += `
                  <tr>
                    <td>${item.KodeBrg}</td>
                    <td>${item.NamaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">0</td>



                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("detailTableData").innerHTML = rowTable
        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_nourut").value = listData[0].NoUrut


        document.getElementById("input_detail_tanggal").value = formatDate(listData[0].Tanggal)

        if (listData[0].TglPelaksanaan && Number(new Date(listData[0].TglPelaksanaan).getFullYear()) > 1999 ) {
          document.getElementById("input_detail_tanggalpelaksanaan").value = formatDate(listData[0].TglPelaksanaan)

        } else {
          document.getElementById("input_detail_tanggalpelaksanaan").value = ''

        }

        if (listData[0].TglCutOFF && Number(new Date(listData[0].TglCutOFF).getFullYear()) > 1999 ) {
          document.getElementById("input_detail_tanggalcutoff").value = formatDate(listData[0].TglCutOFF)

        } else {
          document.getElementById("input_detail_tanggalcutoff").value = ''

        }

        // document.getElementById("input_detail_tanggalpelaksanaan").value = listData[0].TglPelaksanaan ? formatDate(listData[0].TglPelaksanaan) : ''
        // document.getElementById("input_detail_tanggalcutoff").value = listData[0].TglCutOFF ? formatDate(listData[0].TglCutOFF) : ''
        document.getElementById("input_detail_keterangan").value = listData[0].Keterangan
        document.getElementById("input_detail_gudang").value = listData[0].KodeGdg
        document.getElementById("input_detail_kodehdgrp").value = listData[0].KodeHdGrp
        document.getElementById("input_detail_kodekategori").value = listData[0].KodeSubGrp
        document.getElementById("input_detail_kodesubkategori").value = listData[0].Jenis
        document.getElementById("input_detail_kodemerk").value = listData[0].KodeMerk



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
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
          url: "{!! url('perintahopnamespbatalotorisasi') !!}",
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
    url: "{!! url('perintahopnamespotorisasi') !!}",
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
//     url: "{!! url('perintahopnamespotorisasi') !!}",
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

function submitAddItem () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let _token = $("#_token").val();

  console.log(tempDataAdd)

  if(!tempDataAdd.length) {
    alertify.warning("Tidak ada data dipilih")
    return
  }

  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();
  let kodehdgrp = $("#input_add_kodehdgrp").val();
  let kodekategori = $("#input_add_kodekategori").val();
  let kodesubkategori = $("#input_add_kodesubkategori").val();
  let gudang = $("#input_add_gudang").val();
  let kodemerk = $("#input_add_kodemerk").val();
  let keterangan = $("#input_add_keterangan").val();
  let tanggal = $("#input_add_tanggal").val();
  let tanggalpelaksanaan = $("#input_add_tanggalpelaksanaan").val();
  let tanggalcutoff = $("#input_add_tanggalcutoff").val();
  let jmlrecord = tipeform == 'add' ? 0 : 1


  console.log({
    _token,
    choice: 'I',
    tempData : tempDataAdd ,
    nobukti,
    kodehdgrp,
    kodekategori,
    kodesubkategori,
    gudang,
    kodemerk,
    tanggal,
    keterangan,
    tanggal,
    tanggalpelaksanaan,
    tanggalcutoff,
    jmlrecord
  })

  $.ajax({
      url: "{!! url('perintahopnamespadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : tempDataAdd ,
        choice: 'I',
        nobukti,
        nourut,
        kodehdgrp,
        kodekategori,
        kodesubkategori,
        gudang,
        kodemerk,
        tanggal,
        keterangan,
        tanggal,
        tanggalpelaksanaan,
        tanggalcutoff,
        jmlrecord
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('POP telah ditambah');

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



function buttonDeleteItem (index) {


    let barangDelete = listData[index]



    console.log(barangDelete)

    // return


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item '+ barangDelete.KodeBrg +' ?',
        function() {


            let nobukti = $("#input_add_nobukti").val();
            let nourut = $("#input_add_nourut").val();
            let kodehdgrp = $("#input_add_kodehdgrp").val();
            let kodekategori = $("#input_add_kodekategori").val();
            let kodesubkategori = $("#input_add_kodesubkategori").val();
            let gudang = $("#input_add_gudang").val();
            let kodemerk = $("#input_add_kodemerk").val();
            let keterangan = $("#input_add_keterangan").val();
            let tanggal = $("#input_add_tanggal").val();
            let tanggalpelaksanaan = $("#input_add_tanggalpelaksanaan").val();
            let tanggalcutoff = $("#input_add_tanggalcutoff").val();
            let jmlrecord = tipeform == 'add' ? 0 : 1
            let urut = barangDelete.Urut


            let _token  = $("#_token").val()

            $.ajax({
                url: "{!! url('perintahopnamespkoreksi') !!}",
                type: "post",
                async: false,
                data: {
                  _token,
                  tempData : tempDataAdd ,
                  choice: 'D',
                  nobukti,
                  nourut,
                  kodehdgrp,
                  kodekategori,
                  kodesubkategori,
                  gudang,
                  kodemerk,
                  tanggal,
                  keterangan,
                  tanggal,
                  tanggalpelaksanaan,
                  tanggalcutoff,
                  jmlrecord,
                  urut,
                  kodebrg: '',
                  namabrg: '',
                  satuan: ''
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickMerk('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>
      <td >-</td>
      <td>-</td>
      

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickMerk(${i},'${item.KODEMERK}' , '${item.NAMAMERK}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KODEMERK}</td>
        <td>${item.NAMAMERK}</td>
        

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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubKategori('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>
      <td >-</td>
      <td>-</td>
      

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSubKategori(${i},'${item.Urut}' , '${item.Keterangan}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.Urut}</td>
        <td>${item.Keterangan}</td>
        
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickKategori('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>
      <td >-</td>
      <td>-</td>
      

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickKategori(${i},'${item.KodeSubGrp}' , '${item.NamaSubGrp}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeSubGrp}</td>
        <td>${item.NamaSubGrp}</td>
        
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
         <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickHeadGroup('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>
      <td >-</td>
      <td>-</td>
     

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickHeadGroup(${i},'${item.KodeHDGrp}' , '${item.NamaHDGRP}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeHDGrp}</td>
        <td>${item.NamaHDGRP}</td>
        
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
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang('-','-' , '-'  )" type="button" ><i class="bi bi-plus"></i></button></td>
      <td >-</td>
      <td>-</td>
      

      </tr>
      `
      res.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickGudang(${i},'${item.KodeGdg}' , '${item.Nama}'  )" type="button" ><i class="bi bi-plus"></i></button></td>
        <td>${item.KodeGdg}</td>
        <td>${item.Nama}</td>
        

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

function lockForm (value = false) {
  document.getElementById("input_add_tanggal").disabled = value
  document.getElementById("input_add_tanggalpelaksanaan").disabled = value
  document.getElementById("input_add_tanggalcutoff").disabled = value
  document.getElementById("input_add_keterangan").disabled = value
  document.getElementById("buttonAddListHeadGroup").disabled = value
  document.getElementById("buttonAddListGudang").disabled = value
  document.getElementById("buttonAddListMerk").disabled = value
  document.getElementById("buttonAddListKategori").disabled = value
  document.getElementById("buttonAddListSubKategori").disabled = value

}

function cleanFormAdd () {
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_tanggalpelaksanaan").value = ''
  document.getElementById("input_add_tanggalcutoff").value = ''
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_kodehdgrp").value = '-'
  document.getElementById("input_add_gudang").value = '-'
  document.getElementById("input_add_kodemerk").value = '-'
  document.getElementById("input_add_kodesubkategori").value = '-'
  document.getElementById("input_add_kodekategori").value = '-'
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=6 class="text-center">Belum ada data</td></tr>`

}

function refreshDataTable (nobukti) {

    let _token = $("#_token").val();
    listData = []
    $.ajax({
      url: "{!! url('perintahopnamespdetail') !!}",
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

          // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
          // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
          // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

                rowTable += `
                  <tr>
                    <td class="text-right">${item.Urut}</td>

                    <td>${item.KodeBrg}</td>
                    <td>${item.NamaBrg}</td>
                    <td class="text-center">${item.Satuan}</td>
                    <td class="text-right">0</td>
                    <td class='text-center'>
                      <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>


                    </td>


                  </tr>

                `

                // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
        });

        document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_nourut").value = listData[0].NoUrut
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)

        if (listData[0].TglPelaksanaan && Number(new Date(listData[0].TglPelaksanaan).getFullYear()) > 1999 ) {
          document.getElementById("input_add_tanggalpelaksanaan").value = formatDate(listData[0].TglPelaksanaan)

        } else {
          document.getElementById("input_add_tanggalpelaksanaan").value = ''

        }

        if (listData[0].TglCutOFF && Number(new Date(listData[0].TglCutOFF).getFullYear()) > 1999 ) {
          document.getElementById("input_add_tanggalcutoff").value = formatDate(listData[0].TglCutOFF)

        } else {
          document.getElementById("input_add_tanggalcutoff").value = ''

        }

        // document.getElementById("input_add_tanggalpelaksanaan").value = listData[0].TglPelaksanaan ? formatDate(listData[0].TglPelaksanaan) : ''
        // document.getElementById("input_add_tanggalcutoff").value = listData[0].TglCutOFF ? formatDate(listData[0].TglCutOFF) : ''
        document.getElementById("input_add_keterangan").value = listData[0].Keterangan
        document.getElementById("input_add_gudang").value = listData[0].KodeGdg
        document.getElementById("input_add_kodehdgrp").value = listData[0].KodeHdGrp
        document.getElementById("input_add_kodekategori").value = listData[0].KodeSubGrp
        document.getElementById("input_add_kodesubkategori").value = listData[0].Jenis
        document.getElementById("input_add_kodemerk").value = listData[0].KodeMerk



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
        resRefresh = 0;
      }

    })
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
  let kode  = 'POP'
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


@endsection
