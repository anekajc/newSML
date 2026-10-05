@extends('purchasing.newmaster')
@section('buttons')

@endsection

@section('css')

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
@endsection


@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" style='margin-top:-80px;'>

  <!-- <div id="qrcode"></div> -->
  <div class="row" style="margin-top: -30px">
    <div class="col-6 text-left">
      <h2>Invoice Retur Beli</h2>
    </div>
    {{-- <div class="col-6 text-right">
      <button type="button" class="btn btn-primary btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600;  " onclick="buttonAdd()"  >ADD PRJ</button>
    </div> --}}
  </div>
{{-- <button onclick="loadAll()">tes</button> --}}

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
  <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">
    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab"
        aria-controls="nav-home" aria-selected="true"
        style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
      Invoice Retur Beli
    </a>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab"
        aria-controls="nav-profile" aria-selected="false"
        style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff;">
      Sudah Otorisasi
    </a>
  </div>
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
                    <th style="padding: 4px 12px;"  scope="col">Kode Supp</th>
                    <th style="padding: 4px 12px;"  scope="col" style="min-width: 150px">Nama Supp</th>
                    <th style="padding: 4px 12px;"  scope="col">No Beli</th>
                    <th style="padding: 4px 12px;"  scope="col">DPPRP</th>
                    <th style="padding: 4px 12px;"  scope="col">PPNRP</th>
                    <th style="padding: 4px 12px;"  scope="col">Grand TotalRP</th>
                  </tr>
                </thead>


                <tbody id="tabel_data" class="text-left" >
                  {{-- @for ($i = 0; $i < count($tempOutstanding); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding[$i]->NoBukti }}' , 'detail')"><i class="bi bi-info"></i></button>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('{{ $tempOutstanding[$i]->NoBukti }}' , 'edit')"><i class="bi bi-pen"></i></button>
                    @if ($tempOutstanding[$i]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding[$i]->NoBukti }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempOutstanding[$i]->NoBukti }}' , 'add')"><i class="bi bi-key"></i></button>

                    @endif

                  </td>
                  <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
                  <td>{{ $tempOutstanding[$i]->KodeSupp }}</td>
                  <td>{{ $tempOutstanding[$i]->NamaCustSupp }}</td>

                  <td>{{ $tempOutstanding[$i]->NoBeli }}</td>
                  <td>{{ $tempOutstanding[$i]->TotDPPRp }}</td>
                  <td>{{ $tempOutstanding[$i]->TotPPNRp }}</td>
                  <td>{{ $tempOutstanding[$i]->TotNetRp }}</td>


                </tr>
                  @endfor --}}
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
                          <th style="padding: 4px 12px;"  scope="col">Actions</th>
                          <th style="padding: 4px 12px;"  scope="col">No. Bukti</th>
                          <th style="padding: 4px 12px;"  scope="col">Tanggal</th>
                          <th style="padding: 4px 12px;"  scope="col">Kode Supp</th>
                          <th style="padding: 4px 12px;"  scope="col" style="min-width: 150px">Nama Supp</th>
                          <th style="padding: 4px 12px;"  scope="col">No Beli</th>
                          <th style="padding: 4px 12px;"  scope="col">DPPRP</th>
                          <th style="padding: 4px 12px;"  scope="col">PPNRP</th>
                          <th style="padding: 4px 12px;"  scope="col">Grand TotalRP</th>
                          <th style="padding: 4px 12px;"  scope="col">Oto1</th>
                          <th style="padding: 4px 12px;"  scope="col">User Oto1</th>
                          <th style="padding: 4px 12px;"  scope="col">Tgl Oto1</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_dataRetur" class="text-left" >
                        {{-- @for ($i = 0; $i < count($tempOutstanding2); $i++)
                <tr>
                  <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('{{ $tempOutstanding2[$i]->NoBukti }}' , 'detail')"><i class="bi bi-info"></i></button>
                    @if ($tempOutstanding2[$i]->IsOtorisasi1 == 1)
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}' , 'edit')"><i class="bi bi-key"></i></button>
                    @else
                    <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}' , 'add')"><i class="bi bi-key"></i></button>

                    @endif

                  </td>
                  <td>{{ $tempOutstanding2[$i]->NoBukti }}</td>
                  <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->Tanggal)) !!}</td>
                  <td>{{ $tempOutstanding2[$i]->KodeSupp }}</td>
                  <td>{{ $tempOutstanding2[$i]->NamaCustSupp }}</td>

                  <td>{{ $tempOutstanding2[$i]->NoBeli }}</td>
                  <td>{{ $tempOutstanding2[$i]->TotDPPRp }}</td>
                  <td>{{ $tempOutstanding2[$i]->TotPPNRp }}</td>
                  <td>{{ $tempOutstanding2[$i]->TotNetRp }}</td>


                  @if ($tempOutstanding2[$i]->IsOtorisasi1 == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                  <td>{{ $tempOutstanding2[$i]->OtoUser1 }}</td>
                  <td>{!! $tempOutstanding2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding2[$i]->TglOto1)) : '' !!}</td>





                </tr>
                  @endfor --}}
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

<div id="page2" style="display: none" class="mainpage container-fluid">

  <div class="row"  style='margin-top:-80px;'>
    <div class="col-8 text-left">
      <h2>Form Invoice Retur Beli</h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg " style="height: 30px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >Tutup</button>
    </div>
  </div>

  <div id= "modalAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
      <div class="row">

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-12">
              <div class="row">


          <div class="col-md-4">
            <div class="form-group">
              <label>Customer</label>
            </div>
          </div>
          <!-- <div class="col-3 text-right">
            <div class="form-group">
          </div>
        </div> -->
          <div class="col-md-8">
            <div class="form-group input-group">
              <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
              <button class="btn btn-primary btn-sm text-right" id="buttonAddListCustomer" onclick="buttonAddListCustomer()"><i class="bi bi-plus"></i></button>
            </div>
          </div>
          </div>
          </div>


          <div class="col-md-12" style="margin-top:-10px">
            <div class="form-group">
              <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_add_namacustomer"  disabled></textarea>
            </div>
          </div>
          <div class="col-md-12 mb-3" style="margin-top:-10px">

            <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_alamatcustomer"  disabled></textarea>
          </div>

          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12" >
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No Beli</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">
              <div class="form-group">
            </div>
          </div> -->


            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="hidden" class="form-control" id="input_add_flagtipe" value=""  >
                <input type="hidden" class="form-control" id="input_add_ppn" value=""  >
                <input type="text" class="form-control" id="input_add_noinvoice" value="" disabled >
                <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoInvoice" onclick="buttonAddListNoInvoice()"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Catatan</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_catatan"  ></textarea>
              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">

            <div class="col-md-12" >
              <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Tgl</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_noso" value=""  disabled>
              </div>
            </div>
          </div>
        </div>


        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Bayar</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_bayar" value=""  disabled>
              </div>
            </div>
          </div>
        </div>


        <div class="col-md-12" style="margin-top:-10px">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Hari</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_hari" value=""  disabled>
              </div>
            </div>
          </div>
        </div>




          </div>
        </div>








        <div class="col-3">
          <div class="row">
            <div class="col-12">


              <div class="col-md-12" style="margin-top:-10px">
                <div class="row">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Valas</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="input_add_valas" value=""  disabled>
                    </div>
                  </div>
                </div>
              </div>



              <div class="col-md-12" style="margin-top:-10px">
                <div class="row">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Kurs</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-left" id="input_add_kurs" value=""  disabled>

                    </div>
                  </div>
                </div>
              </div>





              <div class="col-md-12" style="margin-top:-10px">
                <div class="row">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Gudang</label>
                    </div>
                  </div>

                  <div class="col-md-8">
                    <div class="form-group input-group">
                      <input type="text" class="form-control" id="input_add_gudang" value="" disabled >
                    </div>
                  </div>
                </div>
              </div>




            </div>
          </div>
        </div>




        <div class="col-3">
          <div class="row">

          </div>
        </div>

        <div class="col-4">
          <div class="row">
            <div class="col-12">
              <div class="row">

              </div>
            </div>



            <div class="col-12">
              <div class="row">



              </div>

            </div>




          </div>

        </div>

        <div class="col-4">
          <div class="row">
            <div class="col-12">
              <div class="row">

              </div>
            </div>


            <div class="col-12">
              <div class="row">


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
              <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
              <th style="padding: 4px 12px;" scope="col">Qnt</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
              <th style="padding: 4px 12px;" scope="col">Harga</th>
              <th style="padding: 4px 12px;" scope="col">Diskon</th>
              <th style="padding: 4px 12px;" scope="col">Sub Total</th>
              <th style="padding: 4px 12px;" scope="col">No. PR.</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=12 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>

  <div class="col-md-12 mt-2 text-right">
  {{-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary" style="height: 30px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;" >+ Tambah Item</button> --}}
</div>


  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4>Add Item</h4>
      </div>
    </div>
    <div class="row">

      <div class="col-md-12">

      <div class="row">

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
          <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>

        </div>
      </div>

    </div>

  </div>
  <div class="col-md-3">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group">
          <label>No Beli</label>
        </div>
      </div>
      <!-- <div class="col-3 text-right">
        <div class="form-group">
      </div>
    </div> -->
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddUrutBeli" type="hidden" class="form-control" >
          <input id="AddAddNoBeli" type="text" class="form-control" disabled>
          <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-plus"></i></button>

        </div>
        <!-- <input id="AddAddKodeGudang" type="hidden" class="form-control" disabled> -->
      </div>


    </div>

  </div>
</div>
</div>

</div>
</div>







  <div class="col-md-6">
    <div class="row">
      <div class="col-md-12" style="margin-top: -10px">

      <div class="row">
      <div class="col-md-2">
        <div class="form-group">
        <label>Nama Brg</label>
      </div>
      </div>
      <div class="col-md-6">
        <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
      </div>
      <div class="col-md-4">

      </div>
    </div>
  </div>


  <div class="col-md-12" style="margin-top: -10px">

  <div class="row">

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty</label>
      </div>
      </div>

      <div class="col-md-4">
        <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQty()">
      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Satuan</label>
      </div>
      </div>

      <div class="col-md-4">
        <input id="AddAddInputIsi" type="hidden"  class="form-control  " disabled>
        <select id="AddAddInputNosat" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangeQty()" disabled>
          <option value=0 selected></option>
        </select>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="margin-top: -10px">

  <div class="row" hidden>

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty 1</label>
      </div>
      </div>

      <div class="input-group col-md-4">
        <input id="AddAddInputQty1" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">

        <input id="AddAddInputSat1" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



      </div>

      <div class="col-md-2">
        <div class="form-group">
        <label>Qty 2</label>
      </div>
      </div>

      <div class="input-group col-md-4">
        <input id="AddAddInputQty2" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">

        <input id="AddAddInputSat2" type="text" value='BOX' class="form-control text-center" disabled style="width: 25%">



      </div>
</div>
</div>




      <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
  <div class="input-group-append">
    <span class="input-group-text">@example.com</span>
  </div> -->
  <div class="col-md-12" style="margin-top: -10px">
    <div class="row">






      <div class="col-md-6">

      </div>
    </div>
  </div>
  <div class="col-md-12" style="margin-top: -10px">
    <div class="row">


      <div class="col-md-2">
        <div class="form-group">
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

          <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
        </div> -->
      <div class="col-md-6">
        <input id="AddAddKeterangan" type="text" class="form-control" >
        <!-- <input id="AddAddKodeGdg" type="hidden" class="form-control" disabled> -->
      </div>
    </div>
  </div>
    </div>
  </div>


  <!-- <div class="col-6 ">
    <div class="row">



    </div> -->
  <!-- </div> -->




  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit Add</button>
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
    </div>

  </div>

</div>

    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>
</div>
<!-- </div> -->


<!-- ADD EDIT -->

<div id="formAddEdit" class="container-fluid showhideitem">
  <!-- <div class="line"></div> -->
  <!-- <div class="row"> -->

  <div class="col-12">


  <hr/>
  <div class="row">
    <div class="col-12">
      <h4>Edit Item</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="row">

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
        <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
        <!-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> -->

      </div>
    </div>

  </div>

</div>
<div class="col-md-3">
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label>No Beli</label>
      </div>
    </div>
    <!-- <div class="col-3 text-right">
      <div class="form-group">
    </div>
  </div> -->
    <div class="col-md-8">
      <div class="input-group form-group">
        <input id="AddEditUrutBeli" type="hidden" class="form-control" >
        <input id="AddEditNoBeli" type="text" class="form-control" disabled>
        <!-- <button class="btn btn-primary btn-sm text-right" id="buttonAddListNoBeli" onclick="buttonAddListNoBeli()"><i class="bi bi-plus"></i></button> -->

      </div>
      <!-- <input id="AddEditKodeGudang" type="hidden" class="form-control" disabled> -->
    </div>


  </div>

</div>

</div>
</div>







<div class="col-md-6">
  <div class="row">
    <div class="col-md-12" style="margin-top: -10px">
      <div class="row">



    <div class="col-md-2">
      <div class="form-group">
      <label>Nama Brg</label>
    </div>
    </div>
    <div class="col-md-10">
      <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
    </div>

  </div>
</div>


<div class="col-md-12" >
<div class="row">


    <div class="col-md-2">
      <div class="form-group" style="margin-top: -10px">
      <label>Qty</label>
    </div>
    </div>

    <div class="col-md-4" style="margin-top: -10px">
      <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQtyEdit()">
    </div>

    <div class="col-md-2" style="margin-top: -10px">
      <div class="form-group">
      <label>Satuan</label>
    </div>
    </div>

    <div class="col-md-4" style="margin-top: -10px">
      <input id="AddEditInputIsi" type="hidden"  class="form-control  " disabled>
      <select id="AddEditInputNosat" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" onchange="onChangeQtyEdit()">
        <option value=0 selected></option>
      </select>
    </div>
  </div>
</div>

<div class="col-md-12" style="margin-top: -10px">
<div class="row" hidden>



    <div class="col-md-2">
      <div class="form-group">
      <label>Qty 1</label>
    </div>
    </div>

    <div class="input-group col-md-4">
      <input id="AddEditInputQty1" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">

      <input id="AddEditInputSat1" type="text" value='PCS' class="form-control text-center" disabled style="width: 25%">



    </div>

    <div class="col-md-2">
      <div class="form-group">
      <label>Qty 2</label>
    </div>
    </div>

    <div class="input-group col-md-4">
      <input id="AddEditInputQty2" type="number" value='0.00' class="form-control text-right" disabled style="width: 75%">

      <input id="AddEditInputSat2" type="text" value='BOX' class="form-control text-center" disabled style="width: 25%">
    </div>



  </div>
</div>



    <!-- <input type="text" class="form-control" placeholder="Email" id="demo" name="email">
<div class="input-group-append">
  <span class="input-group-text">@example.com</span>
</div> -->

<div class="col-md-12" style="margin-top: -10px">
<div class="row">


  </div>
</div>
<div class="col-md-12" style="margin-top: -10px">
<div class="row">

    <div class="col-md-2">
      <div class="form-group">
      <label>Harga</label>
    </div>
    </div>
    <!-- <div class="col-4 text-right">

        <button type="button" onclick="buttonKoreksiListGudang()" class="btn btn-primary" >+</button>
      </div> -->
    <div class="col-md-4">
      <input id="AddEditHarga" type="number" value='0.00' class="form-control text-right" >
      <!-- <input id="AddEditHarga" type="text" class="form-control" > -->
      <!-- <input id="AddEditKodeGdg" type="hidden" class="form-control" disabled> -->
    </div>
  </div>
  <div class="row" style='margin-top:-10px;'>
    <div class="col-md-2">
      <div class="form-group">
        <label>Disc(%)</label>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value=0 tabindex="8">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen2" value=0 tabindex="9">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen3" value=0 tabindex="10">
      </div>
    </div>
  </div>
</div>
  </div>
</div>


<!-- <div class="col-6 ">
  <div class="row">



  </div> -->
<!-- </div> -->





<div class="col-6 mt-2">
  <div class="row">


  </div>
</div>
</div>



  <div class="row mt-2">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit Edit</button>
      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
    </div>

  </div>
  <!-- <div class="line"></div> -->
  <!-- <hr/> -->
</div>
</div>
<!-- </div> -->


<div class="container-fluid">
  <div class="row">
    
    <!-- Disc % -->
      <div class="col">
        <div class="form-group">
          <label>Disc %</label>
          <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
        </div>
      </div>

      <!-- DiscRp -->
      <div class="col">
        <div class="form-group">
          <label>DiscRp</label>
          <input type="number" class="form-control text-right" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
        </div>
      </div>

      <!-- DPP -->
      <div class="col">
        <div class="form-group">
          <label>DPP</label>
          <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
        </div>
      </div>

      <!-- PPN -->
      <div class="col">
        <div class="form-group">
          <label>PPN</label>
          <input type="text" class="form-control text-right" id="input_add_ppnTotal" value="0.00" disabled>
        </div>
      </div>

      <!-- Grand Total -->
      <div class="col">
        <div class="form-group">
          <label>Grand Total</label>
          <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
        </div>
      </div>

    </div>
  </div>

    </div>

    <!-- <div class="row "> -->

<!-- </div> -->

  </div>


  <div id="page3" style="display: none" class="mainpage container-fluid" >

    <div class="row" style="margin-top:-80px;">
      <div class="col-8 text-left">
        <h2>Detail Invoice Retur Beli</h2>
      </div>
      <div class="col-4 text-right">
        <button type="button" class="btn btn-primary btn-lg " style="height: 40px; border-radius: 20px; font-size: 0.75rem;font-weight: 600; text-transform: uppercase " onclick="buttonCloseForm()"  >CLOSE</button>
      </div>
    </div>

    <div id= "modalDetail" class="">



    <div id="" class="">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12">
                <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Supplier</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>
              </div>
            </div>
            </div>
            </div>


            <div class="col-md-12" style="margin-top:-10px">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=1  class="form-control" id="input_detail_namacustomer"  disabled></textarea>
              </div>
            </div>
            <div class="col-md-12 mb-3" style="margin-top:-10px">

              <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatcustomer"  disabled></textarea>
            </div>

            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-12" >
                <div class="row">


              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Beli</label>
                </div>
              </div>
              <!-- <div class="col-3 text-right">
                <div class="form-group">
              </div>
            </div> -->


              <div class="col-md-8">
                <div class="form-group input-group">
                  <input type="hidden" class="form-control" id="input_detail_flagtipe" value=""  >
                  <input type="hidden" class="form-control" id="input_detail_ppn" value=""  >
                  <input type="text" class="form-control" id="input_detail_noinvoice" value="" disabled >
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>

              <div class="col-md-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_catatan"  disabled></textarea>
                </div>
              </div>
            </div>
          </div>
            </div>
          </div>



          <div class="col-md-3">
            <div class="row">

              <div class="col-md-12" >
                <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-left" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}"  disabled>
                </div>
              </div>
            </div>
          </div>


          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>PPN</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_noso" value=""  disabled>
                </div>
              </div>
            </div>
          </div>


          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Bayar</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_bayar" value=""  disabled>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12" style="margin-top:-10px">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>


              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control text-left" id="input_detail_hari" value=""  disabled>
                </div>
              </div>
            </div>
          </div>




            </div>
          </div>







          <div class="col-3">
            <div class="row">
              <div class="col-12">

                <div class="col-md-12" style="margin-top:-10px">
                  <div class="row">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Valas</label>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="form-group input-group">
                        <input type="text" class="form-control" id="input_detail_valas" value="" disabled >
                      </div>
                    </div>
                  </div>
                </div>



                <div class="col-md-12" style="margin-top:-10px">
                  <div class="row">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Kurs</label>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="form-group input-group">
                        <input type="text" class="form-control" id="input_detail_kurs" value="" disabled >
                      </div>
                    </div>
                  </div>
                </div>





                <div class="col-md-12" style="margin-top:-10px">
                  <div class="row">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Gudang</label>
                      </div>
                    </div>

                    <div class="col-md-8">
                      <div class="form-group input-group">
                        <input type="text" class="form-control" id="input_detail_gudang" value="" disabled >
                      </div>
                    </div>
                  </div>
                </div>



              </div>
            </div>
          </div>






          <div class="col-3">
            <div class="row">

            </div>
          </div>

          <div class="col-4">
            <div class="row">
              <div class="col-12">
                <div class="row">

                </div>
              </div>



              <div class="col-12">
                <div class="row">



                </div>

              </div>




            </div>

          </div>

          <div class="col-4">
            <div class="row">
              <div class="col-12">
                <div class="row">

                </div>
              </div>


              <div class="col-12">
                <div class="row">


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
                <th style="padding: 4px 12px;" scope="col">Qnt</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">No. PR</th>



              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=11 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>
<div class="container-fluid">
  <div class="row">
    
    <!-- Disc % -->
      <div class="col">
        <div class="form-group">
          <label>Disc %</label>
          <input type="number" class="form-control text-right" id="input_detail_disc" onblur="onChangeInputAddDisc()" value="0.00">
        </div>
      </div>

      <!-- DiscRp -->
      <div class="col">
        <div class="form-group">
          <label>DiscRp</label>
          <input type="number" class="form-control text-right" id="input_detail_discrp" onblur="onChangeInputAddDiscRp()" value ="0.00" >
        </div>
      </div>

      <!-- DPP -->
      <div class="col">
        <div class="form-group">
          <label>DPP</label>
          <input type="text" class="form-control text-right" id="input_detail_dpp" value="0.00" disabled>
        </div>
      </div>

      <!-- PPN -->
      <div class="col">
        <div class="form-group">
          <label>PPN</label>
          <input type="text" class="form-control text-right" id="input_detail_ppnTotal" value="0.00" disabled>
        </div>
      </div>

      <!-- Grand Total -->
      <div class="col">
        <div class="form-group">
          <label>Grand Total</label>
          <input type="text" class="form-control text-right" id="input_detail_grandtotal" value="0.00" disabled>
        </div>
      </div>

    </div>
  </div>









      <!-- <div class="line"></div> -->
      <!-- <hr/> -->
    </div>
  </div>
  <!-- </div> -->


  <!-- ADD EDIT -->


  <!-- </div> -->



      </div>

      <!-- <div class="row "> -->

  <!-- </div> -->









    </div>



  </div>
</div>



<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">


      <div id= "modalAddListCustomer" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Customer</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_customer" class="table table-bordered table-striped" style="overflow:auto; " >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Kota</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_customer" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
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

      <div id= "modalAddListNoInvoice" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">No Beli</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>No Beli</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_noinvoice" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">No Bukti</th>
                  <th scope="col">Tanggal</th>
                  <th scope="col">No SO</th>
                  <th scope="col">Gudang</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_noinvoice" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
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

      <div id= "modalAddListNoBeli" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">No Beli</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>No Beli</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto;margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_nobeli" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">No Bukti</th>
                  <th scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_nobeli" class="text-left" >

                <tr >

                  <td>-</td>


                    <td class="text-center">
                      <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                      <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                    </td>
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


      <div id= "modalAddListBarang" class="showhidemodalbodyadd">
      <div class="modal-header">


          <h5 class="modal-title" id="">Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3>Barang</h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px;">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_barang" class="table table-bordered table-striped"  >
              <thead class="text-center bg-primary text-white">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>

                  <th style="padding: 4px 12px;" scope="col">Qnt Sisa</th>

                  <th style="padding: 4px 12px;" scope="col">Satuan</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_barang" class="text-left" >


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

// let tempNoBukti = ''



let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''

let noPBLTemp = ''

$(document).ready(function(){
      // $("#tabel").DataTable({
      //   "lengthChange": false,
      //     "paging": false ,
      //     // "searching": false,
      //     "columnDefs": [
      //     // { "type": "date", "targets": [3] },
      //     // {  "className": "text-right", "targets": [9,10,11,12] },
      //     // "columns" : [{"width" : "20px"}]


      //   ]
      //   });
      //   $("#tabelRetur").DataTable({
      //   "lengthChange": false,
      //     "paging": false ,
      //     // "searching": false,
      //     "columnDefs": [
      //     // { "type": "date", "targets": [3] },
      //     // {  "className": "text-right", "targets": [9,10,11,12] },
      //     // "columns" : [{"width" : "20px"}]


      //   ]
      //   });
        $("#tabel_add_list_customer").DataTable({
          "lengthChange": false,
            "paging": false ,
        });
        $("#tabel_add_list_noinvoice").DataTable({
          "lengthChange": false,
            "paging": false ,
        });
        $("#tabel_add_list_barang").DataTable({
          "lengthChange": false,
            "paging": false ,
      });

      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });




  //   formAddListItem
});

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('perintahreturjualspnobukti') !!}",
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


function cleanFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  document.getElementById("AddAddNoBeli").value = ''
  document.getElementById("AddAddUrutBeli").value = 0
  document.getElementById("AddAddInputQty").value = '0.00'
  document.getElementById("AddAddInputNosat").value = 0

  document.getElementById("AddAddInputIsi").value = 0



  document.getElementById("AddAddKeterangan").value = ''
  tempBarangAddAdd = {}


}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanFormAdd () {


  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_noinvoice").value = ''
  document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_gudang").value = ''




  // document.getElementById("input_add_tanggalpo").valueAsDate = new Date()
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_kodepelanggan").value = ''
  // document.getElementById("input_add_namapelanggan").value = ''
  // document.getElementById("input_add_alamatpelanggan").value = ''
  // document.getElementById("input_add_kodealamatkirim").value = ''
  // document.getElementById("input_add_alamatkirim").value = ''
  // document.getElementById("input_add_kodepic").value = ''
  // document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodelokasipenerima").value = ''
  // document.getElementById("input_add_alamatlokasipenerima").value = ''
  // document.getElementById("input_add_catatan").value = ''
  // document.getElementById("input_add_valas").value = ''
  // document.getElementById("input_add_kurs").value = ''
  // document.getElementById("input_add_dp").value = '0.00'
  // document.getElementById("input_add_nopo").value = ''
  // document.getElementById("input_add_kodebackoffice").value = ''
  // document.getElementById("input_add_namabackoffice").value = ''
  // document.getElementById("input_add_tipeppn").value = 0
  // document.getElementById("input_add_pembayaran").value = 0
  // document.getElementById("input_add_kodesales").value = ''
  // document.getElementById("input_add_namasales").value = ''
  // document.getElementById("input_add_hari").value = 0
  // document.getElementById("input_add_draftpo").value = 0
  //
  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_dp").disabled = false
  // document.getElementById("input_add_nopo").disabled = false
  // document.getElementById("input_add_catatan").disabled = false
  // document.getElementById("input_add_tanggalpo").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false
  //
  // document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListAlamatKirim").disabled = false
  // document.getElementById("buttonAddListSales").disabled = false
  // document.getElementById("buttonAddListValas").disabled = false
  // document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  // document.getElementById("buttonAddListBackOffice").disabled = false
  //
  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false
  //
  // document.getElementById("input_add_disc").value = '0.00'
  // document.getElementById("input_add_discrp").value = '0.00'
  // document.getElementById("input_add_ppn").value = '0.00'
  // document.getElementById("input_add_dpp").value = '0.00'
  // document.getElementById("input_add_grandtotal").value = '0.00'
}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()

  console.log(nobukti , tanggal , nopajak)

}


function submitAddEdit () {
  let barang = tempBarangAddEdit

  let _token  = $("#_token").val()

  let choice = "U"
  let urut = barang.Urut

  let jmlrecord = 1

  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodecustsupp = $("#input_add_kodecustomer").val()
  let namacustsupp = $("#input_add_namacustomer").val()
  let alamatcustsupp = $("#input_add_alamatcustomer").val()
  let kodegdg = $("#input_add_gudang").val()
  let noinvoice = $("#input_add_noinvoice").val()
  let flagtipe = $("#input_add_flagtipe").val()
  let ppn = $("#input_add_ppn").val()
  let catatan = $("#input_add_catatan").val()
  let retursupp = $("#AddEditReturSupp").val()
  let tanggal = $("#input_add_tanggal").val()
  let noso = $("#input_add_noso").val()
  let gudang = $("#input_add_gudang").val()

  let nobeli = $("#AddEditNoBeli").val()
  let urutbeli = $("#AddEditUrutBeli").val()
  let namabrg = $("#AddEditNamaBrg").val();
  let kodebrg = $("#AddEditKodeBrg").val();
  let qnt = $("#AddEditInputQty").val();

  let DISCP = $("#input_add_add_discpersen1").val();
  let DiscP2 = $("#input_add_add_discpersen2").val();
  let DiscP3 = $("#input_add_add_discpersen3").val();

  let nosat = $("#AddEditInputNosat").val();
  let isi = 0
  if (nosat == 1) {
    isi = barang.ISI1
  } else if (nosat == 2) {
    isi = barang.ISI2
  }
  let urutinvoice = barang.UrutSC
  let qnt1 = $("#AddEditInputQty1").val();
  let sat1 = $("#AddEditInputSat1").val();
  let qnt2 = $("#AddEditInputQty2").val();
  let sat2 = $("#AddEditInputSat2").val();
  let harga = $("#AddEditHarga").val();

  console.log(namabrg, kodebrg, qnt,harga,nobukti,urut)

  if (Number(qnt) * Number(isi) > Number(barang.IsiSisaView) * (Number(barang.QntSisaView) + Number(barang.QNT)) ) {
    alertify.warning("Melebihi qnt sisa")
    return
  }

  // return
  $.ajax({
    url: "{!! url('invoicereturbelispadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      noinvoice,
      kodecustsupp,
      catatan,
      urut,
      urutinvoice,
      kodebrg,
      qnt,
      qnt1,
      qnt2,
      sat1,
      nosat,
      isi,
      jmlrecord,
      namabrg,
      kodegdg,
      flagtipe,
      ppn,
      noso,
      retursupp,
      sat2,
      harga,
      nobeli,
      urutbeli,
      DISCP,
      DiscP2,
      DiscP3


    },
    success: function(res) {


      console.log('resspadd', res)
      // return
      if (res == 1) {



        // tipeform = 'edit'
        // lockFormAdd()
        $('.showhideitem').hide();
        // $('.showhideform').hide();
        // $('#modalAdd').show();
        loadAll()
        refreshDataTableAdd(nobukti)



        // lockFormAdd()
        // $('.showhide').hide();
        // $('#buttonSubmitSaveHeader').show();
        // unlockFormAdd()
        // tipeform = 'edit'
        // document.getElementById("buttonAddListCustomer").disabled = true
        // document.getElementById("buttonAddListNoInvoice").disabled = true
        // $('.showhideitem').hide();
        // // $('#divhargaterakhir').hide();
        // cleanFormAddAdd()
        //
        // refreshDataTableAdd(nobukti)

        alertify.success('Berhasil menambah item')
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddDeleteItem (i) {
  console.log(i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let barang = dataBarang[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + barang.NAMABRG + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = barang.NOBUKTI
        let urut = barang.URUT
        console.log(nobukti, urut)
        $.ajax({
          url: "{!! url('perintahreturjualspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            nourut : '',
            tanggal : '',
            noinvoice : '',
            kodecustsupp : '',
            catatan :'',
            urut,
            urutinvoice :'',
            kodebrg:'',
            qnt:0,
            qnt1:0,
            qnt2:0,
            sat1:'',
            nosat:0,
            isi:0,
            jmlrecord:1,
            namabrg:'',
            kodegdg:'',
            flagtipe:0,
            ppn:0,
            noso:'',
            retursupp:0,
            sat2:'',
            ketdet:'',
            nobeli:'',
            urutbeli:0

          },
          success: function(res) {
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();
            loadAll()
            refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

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

function submitAddAdd () {

  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let barang = tempBarangAddAdd

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()

  let choice = "I"
  let urut = 0

  let jmlrecord = 0

  if (tipeform == "edit") {
    jmlrecord = 1
  }

  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodecustsupp = $("#input_add_kodecustomer").val()
  let namacustsupp = $("#input_add_namacustomer").val()
  let alamatcustsupp = $("#input_add_alamatcustomer").val()
  let kodegdg = $("#input_add_gudang").val()
  let noinvoice = $("#input_add_noinvoice").val()
  let flagtipe = $("#input_add_flagtipe").val()
  let ppn = $("#input_add_ppn").val()
  let catatan = $("#input_add_catatan").val()

  let tanggal = $("#input_add_tanggal").val()
  let noso = $("#input_add_noso").val()
  let gudang = $("#input_add_gudang").val()

  let nobeli = $("#AddAddNoBeli").val()
  let urutbeli = $("#AddAddUrutBeli").val()
  let namabrg = $("#AddAddNamaBrg").val();
  let kodebrg = $("#AddAddKodeBrg").val();
  let qnt = $("#AddAddInputQty").val();

  let nosat = $("#AddAddInputNosat").val();
  let isi = 0
  if (nosat == 1) {
    isi = barang.Isi1
  } else if (nosat == 2) {
    isi = barang.Isi2
  }
  let urutinvoice = barang.Urut
  let qnt1 = $("#AddAddInputQty1").val();
  let sat1 = $("#AddAddInputSat1").val();
  let qnt2 = $("#AddAddInputQty2").val();
  let sat2 = $("#AddAddInputSat2").val();

  let ketdet = $("#AddAddKeterangan").val();
  console.log(kodebrg, kodecustsupp, noinvoice , nosat)
  if (!kodebrg || !kodecustsupp || !noinvoice || !nosat) {
    alertify.warning("Data tidak lengkap")
    return
  }
  if (Number(qnt) < 0) {
    alertify.warning("Qty kurang dari 0")
    return
  }

  if (Number(qnt) * Number(isi) > Number(barang.QntSisa) * Number(barang.Isi) ) {
    alertify.warning("Melebihi qnt sisa")
    return
  }
  console.log('y')

  console.log({
    choice,
    nobukti,
    nourut,
    tanggal,
    noinvoice,
    kodecustsupp,
    catatan,
    urut,
    urutinvoice,
    kodebrg,
    qnt,
    qnt1,
    qnt2,
    sat1,
    nosat,
    isi,
    jmlrecord,
    namabrg,
    kodegdg,
    flagtipe,
    ppn,
    noso,
    retursupp,
    sat2,
    ketdet,
    nobeli,
    urutbeli


  })

  $.ajax({
    url: "{!! url('perintahreturjualspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      noinvoice,
      kodecustsupp,
      catatan,
      urut,
      urutinvoice,
      kodebrg,
      qnt,
      qnt1,
      qnt2,
      sat1,
      nosat,
      isi,
      jmlrecord,
      namabrg,
      kodegdg,
      flagtipe,
      ppn,
      noso,
      retursupp,
      sat2,
      ketdet,
      nobeli,
      urutbeli


    },
    success: function(res) {


      console.log('resspadd', res)
      // return
      if (res == 1) {



        tipeform = 'edit'
        lockFormAdd()
        $('.showhideitem').hide();
        // $('.showhideform').hide();
        // $('#modalAdd').show();
        loadAll()
        refreshDataTableAdd(nobukti)





        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListBarang () {

  console.log('buttonAddListBarang')


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();
  let noinvoice = $("#input_add_noinvoice").val();
  let noso = $("#input_add_noso").val();

  if (!kodecustsupp || !noinvoice ) {
    alertify.warning("Pilih customer & invoice terlebih dahulu")
    return
  }

  $('#tabel_add_list_barang').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
      noinvoice
    },
    success: function(res) {

      listBarang = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KodeBrg}</td>
        <td>${item.NamaBrg ? item.NamaBrg : item.NamaBrgx }</td>
        <td>${item.QntSisa}</td>
        <td class="text-center">${item.Satuan}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickBarang(${i})" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
      $("#tabel_add_list_barang").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListBarang').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAddListNoBeli () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();
  let noinvoice = $("#input_add_noinvoice").val();
  let noso = $("#input_add_noso").val();
  let kodebrg = $("#AddAddKodeBrg").val();

  if (!kodebrg ) {
    alertify.warning("Pilih barang terlebih dahulu")
    return
  }

  $('#tabel_add_list_nobeli').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistnobeli') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg,
      noso
    },
    success: function(res) {
      let rowTable = ``
      rowTable += `<tr>
      <td>-</td>
      <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('-' , 0)" type="button" ><i class="bi bi-plus"></i></button></td>

      </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NOBUKTI}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoBeli('${item.NOBUKTI}' ,${item.urut} )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });





      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_nobeli").innerHTML = rowTable
      $("#tabel_add_list_nobeli").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoBeli').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListNoInvoice () {


  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp) {
    alertify.warning("Pilih customer terlebih dahulu")
    return
  }

  $('#tabel_add_list_noinvoice').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistnoinvoice') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.NOBUKTI}</td>
        <td>${item.TANGGAL}</td>
        <td>${item.NoSO}</td>
        <td>${item.NAMAGDG}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickNoInvoice('${item.NOBUKTI}' , '${item.NoSO}' , '${item.KODEGDG}', ${item.flagtipe}, ${item.ppn})" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_noinvoice").innerHTML = rowTable
      $("#tabel_add_list_noinvoice").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListNoInvoice').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListCustomer () {
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('perintahreturjuallistcustomer') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.NamaKota}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });




      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      $("#tabel_add_list_customer").DataTable({
        "lengthChange": false,
          "paging": false ,
    });
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListCustomer').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonAddPickNoBeli (nobeli, urutbeli) {
  console.log('buttonAddPickNoBeli')
  console.log(nobeli)
  document.getElementById("AddAddNoBeli").value = nobeli
  document.getElementById("AddAddUrutBeli").value = urutbeli
  // $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickCustomer (kode, nama , alamat) {
  console.log('buttonAddPickCustomer')
  console.log(kode,nama,alamat)
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat
  document.getElementById("input_add_noinvoice").value = ''
  document.getElementById("input_add_noso").value = ''
  document.getElementById("input_add_gudang").value = ''
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}
function onChangeQtyEdit () {

  console.log('onChangeQtyEdit')
  console.log('tempBarangAddEdit' , tempBarangAddEdit)

  let qty = $("#AddEditInputQty").val();
  let nosat = $("#AddEditInputNosat").val();
  if (jQuery.isEmptyObject(tempBarangAddEdit)) {
    console.log('g ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddEdit.ISI1 : tempBarangAddEdit.ISI2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddEditInputQty1").value = tempTotalQty / tempBarangAddEdit.ISI1
    document.getElementById("AddEditInputQty2").value = tempTotalQty / tempBarangAddEdit.ISI2


  }
}

function onChangeQty () {

  console.log('onChangeQty')
  console.log('tempBarangAddAdd' , tempBarangAddAdd)
  let qty = $("#AddAddInputQty").val();
  let nosat = $("#AddAddInputNosat").val();
  console.log('qty' , qty)
  console.log('nosat' , nosat)

  if (jQuery.isEmptyObject(tempBarangAddAdd)) {
    console.log('g ada barang')
  } else {

    console.log('ada barang')
    let tempIsi = nosat == 1 ? tempBarangAddAdd.Isi1 : tempBarangAddAdd.Isi2
    console.log(tempIsi)
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddAddInputQty1").value = tempTotalQty / tempBarangAddAdd.Isi1
    document.getElementById("AddAddInputQty2").value = tempTotalQty / tempBarangAddAdd.Isi2


  }

}

function buttonAddPickBarang (index) {
  console.log('buttonAddPickBarang')
  tempBarangAddAdd = listBarang[index]

  console.log('tempBarangAddAdd', tempBarangAddAdd)
  document.getElementById("AddAddKodeBrg").value = tempBarangAddAdd.KodeBrg
  document.getElementById("AddAddNamaBrg").value = tempBarangAddAdd.NamaBrg ? tempBarangAddAdd.NamaBrg : tempBarangAddAdd.NamaBrgx
  document.getElementById("AddAddInputQty").value = tempBarangAddAdd.QntSisa
  document.getElementById("AddAddInputQty1").value = tempBarangAddAdd.Qnt1Sisa
  document.getElementById("AddAddInputQty2").value = tempBarangAddAdd.Qnt2Sisa

  document.getElementById("AddAddInputSat1").value = tempBarangAddAdd.SAT1
  document.getElementById("AddAddInputSat2").value = tempBarangAddAdd.SAT2

  let selectOption = ''
  if (tempBarangAddAdd.SAT1) {
    selectOption += `<option value=1 ${tempBarangAddAdd.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddAdd.SAT1}</option>`
  }
  if (tempBarangAddAdd.SAT2) {
    selectOption += `<option value=2 ${tempBarangAddAdd.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddAdd.SAT2}</option>`
  }
  document.getElementById("AddAddInputNosat").innerHTML = selectOption






  buttonAddListBatal()
  // $("#form").modal('toggle')
}



function buttonAddPickNoInvoice (nobukti, noso , kodegdg, flagtipe, ppn) {
  console.log('buttonAddPickNoInvoice')
  document.getElementById("input_add_noinvoice").value = nobukti
  document.getElementById("input_add_noso").value = noso
  document.getElementById("input_add_gudang").value = kodegdg
  document.getElementById("input_add_flagtipe").value = flagtipe
  document.getElementById("input_add_ppn").value = ppn
  $('.showhideitem').hide();
  buttonAddListBatal()
  // $("#form").modal('toggle')
}


function buttonAddBatal () {

  $('.showhideitem').hide();
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

// function buttonAddListCustomer () {
//
//   $('.showhidemodalbodyadd').hide();
//   $('#modalBodyAddListValas').show();
//
//   $("#form").modal('toggle')
// }


function closeShowHideItem () {
  $('.showhideitem').hide();

}

function unlockFormAdd () {
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_tanggal").disabled = false


  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("buttonAddListNoInvoice").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_tanggal").disabled = true


  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("buttonAddListNoInvoice").disabled = true

}



function refreshDataTableAdd (NOBUKTI = "") {

    console.log('refreshDataTableAdd' , NOBUKTI)

    if (!NOBUKTI) {

    } else {
      let _token  = $("#_token").val()
      $.ajax({
        url: "{!! url('invoicereturbeligetdetail') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti: NOBUKTI
        },
        success: function(res) {
          dataHeaderAdd = res[0]
          console.log('aaa')
          console.log('res' , res)

          // res.header.forEach((item, i) => {
          //   console.log('a' , i)
          // });
          if (res.length == 0) {
            alertify.warning("Data habis")
            // $("#form").modal('toggle')
            buttonCloseForm()
          } else {
            dataBarang = res

            let rowTable = ""
            res.forEach((item, i) => {

              rowTable += `<tr>
              <td class="text-center">
                <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>
              </td>

              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>

              <td class="text-center">${item.Satuan ? item.Satuan : item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>

              </tr>`


            });


            document.getElementById("addTableData").innerHTML = rowTable

            noPBLTemp = res[0].NoPBL

            document.getElementById("input_add_kodecustomer").value = res[0].KodeSupp
            document.getElementById("input_add_namacustomer").value = res[0].NamaCustSupp
            document.getElementById("input_add_alamatcustomer").value = res[0].Alamat
            document.getElementById("input_add_noinvoice").value = res[0].NoPBL
            document.getElementById("input_add_noso").value = res[0].MyPPN

            document.getElementById("input_add_bayar").value = res[0].MYBAYAR
            document.getElementById("input_add_valas").value = res[0].KodeVls
            document.getElementById("input_add_kurs").value = res[0].Kurs
            document.getElementById("input_add_hari").value = res[0].Hari
          
            document.getElementById("input_add_gudang").value = res[0].KodeGdg
            document.getElementById("input_add_catatan").value = res[0].Keterangan
            document.getElementById("input_add_flagtipe").value = res[0].FlagTipe
            document.getElementById("input_add_ppn").value = res[0].FlagTipe

            document.getElementById("input_add_nobukti").value = res[0].NoBukti
            document.getElementById("input_add_nourut").value = res[0].NoUrut

            document.getElementById('input_add_grandtotal').value = parseFloat(res[0].TotalNetto || 0).toFixed(2);
            document.getElementById('input_add_ppnTotal').value = parseFloat(res[0].TotalPPn || 0).toFixed(2);
            document.getElementById('input_add_dpp').value = parseFloat(res[0].TotalDPP || 0).toFixed(2);

            document.getElementById('input_add_disc').value = parseFloat(res[0].Disc || 0).toFixed(2);
            document.getElementById('input_add_discrp').value = (parseFloat(res[0].Harga || 0) * (parseFloat(res[0].Disc || 0) / 100)).toFixed(2);
          }



          // res.list.forEach((item, i) => {
          //   console.log('b' , i)
          // });



        },
        error: function (err) {
          console.log(err)
          console.log(err.status)
          console.log(err.statusText)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }

      })
    }
}

function buttonDetail (nobukti) {
  console.log('buttonDetail' , nobukti)

    let _token  = $("#_token").val()

    let rowTable = ""
    $.ajax({
      url: "{!! url('invoicereturbeligetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: nobukti
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

          if(res.length) {
            let rowTable = ""
            res.forEach((item, i) => {

              rowTable += `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>

              <td class="text-center">${item.Satuan ? item.Satuan : item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>

              </tr>`
            });

            // if(!dataTableAdd.length) {
            //   rowTable = `<tr>
            //   <td class="text-center" colspan="8">Belum ada barang</td>
            //   </tr>`
            // }
            document.getElementById("detailTableData").innerHTML = rowTable

            document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
            document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
            document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat
            document.getElementById("input_detail_noinvoice").value = res[0].NoPBL
            document.getElementById("input_detail_noso").value = res[0].MyPPN
            document.getElementById("input_detail_bayar").value = res[0].MYBAYAR
            document.getElementById("input_detail_valas").value = res[0].KodeVls
            document.getElementById("input_detail_kurs").value = res[0].Kurs
            document.getElementById("input_detail_hari").value = res[0].Hari

            document.getElementById("input_detail_gudang").value = res[0].KodeGdg
            document.getElementById("input_detail_catatan").value = res[0].Keterangan
            document.getElementById("input_detail_flagtipe").value = res[0].FlagTipe
            document.getElementById("input_detail_ppn").value = res[0].FlagTipe

            document.getElementById("input_detail_nobukti").value = res[0].NoBukti
            document.getElementById("input_detail_nourut").value = res[0].NoUrut

            document.getElementById('input_detail_grandtotal').value = res[0].TotalNetto
            document.getElementById('input_detail_ppnTotal').value = res[0].TotalPPn
            document.getElementById('input_detail_dpp').value = res[0].TotalDPP
            
            document.getElementById('input_add_disc').value = res[0].Disc
            document.getElementById('input_add_discrp').value = res[0].DiscRp

          } else {
            alertify.warning("Data tidak ditemukan")
          }



      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

  $('.mainpage').hide();
  $('#page3').show();
}

function buttonEdit (nobukti) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  tipeform = 'edit'
  lockFormAdd()
  $('.showhideitem').hide();
  // $('.showhideform').hide();
  // $('#modalAdd').show();
  refreshDataTableAdd(nobukti)

  if (dataBarang[0].IsOtorisasi1 == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAdd (nobukti) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  console.log('buttonAdd' , nobukti)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  document.getElementById("addTableData").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`
  tipeform = 'add'
  unlockFormAdd()
  $('.showhideitem').hide();
  $('.showhideform').hide();
  $('#modalAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  setNewNoBukti()

  $('.mainpage').hide();
  $('#page2').show();

}

function buttonAddAddItem () {

    $('.showhideitem').hide();
    cleanFormAddAdd()
    $('#formAddAdd').show();

}

function buttonAddEditItem (i) {
  tempBarangAddEdit = dataBarang[i]
  console.log(tempBarangAddEdit)

  document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.KodeBrg
  // document.getElementById("AddEditReturSupp").value = tempBarangAddEdit.FlagKembali
  document.getElementById("AddEditNoBeli").value = tempBarangAddEdit.NoPBL
  document.getElementById("AddEditUrutBeli").value = tempBarangAddEdit.UrutPBL
  document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NamaBrg
  document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.Qnt).toFixed(2)

  document.getElementById("AddEditInputQty1").value = parseFloat(tempBarangAddEdit.qnt1).toFixed(2)
  document.getElementById("AddEditInputQty2").value = parseFloat(tempBarangAddEdit.qnt2).toFixed(2)
  document.getElementById("AddEditInputSat1").value = tempBarangAddEdit.sat1
  document.getElementById("AddEditInputSat2").value = tempBarangAddEdit.sat2

  
  document.getElementById("input_add_add_discpersen1").value = tempBarangAddEdit.DiscP
  document.getElementById("input_add_add_discpersen2").value = tempBarangAddEdit.Discp2
  document.getElementById("input_add_add_discpersen3").value = tempBarangAddEdit.Discp3

  document.getElementById("AddEditHarga").value = parseFloat(tempBarangAddEdit.Harga).toFixed(2)

  let selectOption = ''
  if (tempBarangAddEdit.sat1) {
    selectOption += `<option value=1 ${tempBarangAddEdit.NoSat == 1 ? 'selected' : ''}>SAT1 - ${tempBarangAddEdit.sat1}</option>`
  }
  if (tempBarangAddEdit.sat2) {
    selectOption += `<option value=2 ${tempBarangAddEdit.NoSat == 2 ? 'selected' : ''}>SAT2 - ${tempBarangAddEdit.sat2}</option>`
  }
  document.getElementById("AddEditInputNosat").innerHTML = selectOption

  document.getElementById("AddEditInputNosat").disabled = true
  document.getElementById("AddEditInputQty").disabled = true


// $.ajax({
//     url: "{!! url('invoicereturbeligetLPBdetail') !!}",
//     type: "get",
//     async: false,
//     data: {
//       nobukti : noPBLTemp

//     },
//     success: function(res) {
//       console.log(res)
//       // document.getElementById('AddEditHarga').value = res[0].HARGA
//       // document.getElementById('AddEditDiskon').value = res[0].DISC

//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Gagal mendapatkan data LPB')
//     }

//   })

  $('.showhideitem').hide();
  $('#formAddEdit').show();

}

function formatAngka (angkaString) {
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

const formatTanggal = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}-${month}-${year}`;
};

function loadAll () {

  console.log('loadall')
  let _token = $("#_token").val();

  $('#tabel').DataTable().destroy();
  $('#tabelRetur').DataTable().destroy();

  $.ajax({
    url: "{!! url('invoicereturbeliloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log(res)
      let rowTable = ""
      let rowTable2 = ""
      res.tempOutstanding.forEach((item, i) => {


        rowTable += 
       ` <tr>
        <td class='text-center'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit('${item.NoBukti}' , 'edit')"><i class="bi bi-pen"></i></button>
                    
      ${item.IsOtorisasi1 == 1 ? 
      `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.NoBukti}' , 'edit')"><i class="bi bi-key"></i></button>
                    ` 
      : 
      `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.NoBukti}' , 'add')"><i class="bi bi-key"></i></button>
`}
                  <td>${item.NoBukti}</td>
                  <td>${formatTanggal(item.Tanggal)}</td>
                  <td>${item.KodeSupp}</td>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoBeli || ''}</td>
                  <td class="text-right">${item.TotDPPRp ? formatAngka(parseFloat(item.TotDPPRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.TotPPNRp ? formatAngka(parseFloat(item.TotPPNRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.TotNetRp ? formatAngka(parseFloat(item.TotNetRp).toFixed(2)) : '0.00'}</td>
                  

                  </td>
                </tr>`

      });

      res.tempOutstanding2.forEach((item, i) => {


        rowTable2 += 
       ` <tr>
        <td class='text-center' style='white-space:nowrap;'>
                    <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>
                    
      ${item.IsOtorisasi1 == 1 ? 
      `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${item.NoBukti}' , 'edit')"><i class="bi bi-key"></i></button>
                    ` 
      : 
      `<button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item.NoBukti}' , 'add')"><i class="bi bi-key"></i></button>
`}
      <button class="btn btn-primary btn-sm" type="button" onclick="submitPrint('${item.NoBukti}' , 'detail')"><i class="bi bi-printer"></i></button>
      </td>
                  <td>${item.NoBukti}</td>
                  <td>${formatTanggal(item.Tanggal)}</td>
                  <td>${item.KodeSupp}</td>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoBeli || ''}</td>
                  <td class="text-right">${item.TotDPPRp ? formatAngka(parseFloat(item.TotDPPRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.TotPPNRp ? formatAngka(parseFloat(item.TotPPNRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.TotNetRp ? formatAngka(parseFloat(item.TotNetRp).toFixed(2)) : '0.00'}</td>
                  
                  ${item.IsOtorisasi1 == 1 ? 
                  ` <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                         ` 
                  : 
                  `<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          `}
                 
                  <td>${item.OtoUser1}</td>
                  <td>${item.TglOto1 ? item.TglOto1 : '' }</td>

                  </td>
                </tr>`

      });

      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          // "searching": false,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]


        ]
        });

        document.getElementById("tabel_dataRetur").innerHTML = rowTable2
      $("#tabelRetur").DataTable({
        "lengthChange": false,
          "paging": false ,
          // "searching": false,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]


        ]
        });

        



    }})

}



function buttonBatalOtorisasi (nobukti) {

console.log( $("#akses_isbatal").val());
let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access batal');
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
        url: "{!! url('invoicereturbelispbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          otorisasi: 0,
          pket :value
        },
        success: function (res) {
          if (res > 0) {
            alertify.success('Berhasil batal otorisasi');
            loadAll();
          } else {
            alertify.warning('Gagal batal otorisasi');
          }
        },
        error: function (err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
        }
      });
  },
  function() {
    alertify.error("Action cancelled");
  }
);
}


function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('invoicereturbelispotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            console.log(res)
            alertify.success('Berhasil update otorisasi')
            loadAll()



          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
}

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      // refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
      loadAll()
    }
}

function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = $("#input_add_discrp").val()
        console.log(dataHeaderAdd)
        let x = (parseFloat(value) * 100) / parseFloat(dataHeaderAdd.Harga)
        console.log(x)
        console.log(value)
        onChangeHeader('DISC' , x)
        // refreshUpdateHeader()
        let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)
        loadAll()
      }
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('invoicereturbelionchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
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

    $.ajax({
      url: "{!! url('invoiceReturBeliCetak') !!}",
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
    // let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];

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
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%">`+dataPrint[0].NAMACUSTSUPP+`</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">NOTA RETUR</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NOMOR</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+dataPrint[0].NoBukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">PO. NO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOPOHD}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">INVOICE</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%"></div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 100%; height: 225px; max-height: 225px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%">NO.</td>
                    <td class="text-center" style="width: 30%">NAMA BARANG</td>
                    <td class="text-center" style="width: 5%">PART NUMBER</td>
                    <td class="text-center" style="width: 5%">QTY</td>
                    <td class="text-center" style="width: 5%">HARGA SATUAN</td>
                    <td class="text-center" style="width: 5%">JUMLAH</td>
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
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 30%;">${itemSub.NamaBrg}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 5%;">${itemSub.PartNumber}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${formatAngka(parseFloat(itemSub.Harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; text-align: right; width: 5%;">${formatAngka(parseFloat(itemSub.Total).toFixed(2))}</td>
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
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 10px;">

  <div style="width: 60%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Terbilang:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100% font-size:12px;">`+dataPrint[0].Terbilang+`</div>
    </div>
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
      <tr>
        <td class="no-border text-center" style="width: 34%; font-size:13px;">DIBUAT OLEH,</td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
        <td class="no-border text-center" style="width: 34%; font-size:13px;"></td>
        <td class="no-border text-center" style="width: 33%; font-size:13px;">MENGETAHUI,</td>
      </tr>
      <tr style="height: 2.5rem;">
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
        <td class="no-border" colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0""></p>
        </td>
      </tr>
      <tr>
        <td class="no-border px-2 text-center">
          <p class="m-0" style='font-size:12px;'>FENNY SAGITHA</p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2">
          <p class="m-0"></p>
        </td>
        <td class="no-border px-2 text-center">
          <p class="m-0" style="font-size:12px;">EVY YUSIA</p>
        </td>
      </tr>
    </table>
  </div>

  <div style="width: 40%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TotalUSD).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> DPP </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NDPP).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:13px; justify-content: flex-end; width: 100%; padding-bottom: 2px;">
      <div style="width: 60% margin-left:auto"> PPN </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NPPN).toFixed(2))}</div>
    </div>

  </div>

</div>

         <div class="footer-print-date" style='margin-bottom:-100px;'>
            <table class="m-0" style="width: 100% ; font-family: sans-serif;
            font-size: 10px ">
              <tr>
                <td class="no-border" white-space:pre;>-Permasalahan : Barang yang disupply tidak sesuai dengan permintaan user.</td>
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


window.onload = function(){
  loadAll();
};


</script>




@endsection
