@extends('newmaster')
@section('buttons')

@endsection

@section('css')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="tempPrintContainer1" style="display:none"></div>

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

  #tabelListInvoice_filter {
      display: flex;
      align-items: flex-end;
      margin-top: 8px;
      margin-right: 10px;
      margin-bottom: -10px;
    }

  #tabelListInvoice_filter label input {
      width: 150px;
      padding: 5px 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }

  #tabelListInvoice_filter label {
      font-weight: 600;
      font-size: 0.9rem;
      color: #333;
    }

  #tabelListInvoice_filter input:focus {
      border-color: #007bff;
      outline: none;
    }
</style>

<style>
#tabel3_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel3_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel3_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel3_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>
{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar modal add pelanggan --}}
<style>
  #tabel_add_list_pelanggan_filter{
    display: flex;
    align-items: flex-end;
    margin-bottom: -10px;

  }
  #tabel_add_list_pelanggan_filter label input {
    width: 150px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }
</style>
{{-- end tampilan search bar modal add pelanggan --}}

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
@endsection

@section('content')
<div id="page1" class="container-fluid mainpage">

  <div class="container-fluid">

    <!-- <div id="qrcode"></div> -->
    <div class="row" >
      <div class="col-6 text-left">
        <h2>Invoice Penjualan</h2>
      </div>
      <div class="col-6 text-right">
        <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="printAll()">
          Checklist Print Invoice
        </button>
      </div>
    </div>
    <!-- <button onclick="getDateDiff()">tes</button> -->
  <!-- <button onclick="loadAll()">tes</button> -->
  </div>

  <div id="printContainer" style="display:none">


  </div>


  <div id="contentContainer" class="container-fluid" >
    <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
    <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

    <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
    <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
    <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
    <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
    <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
    <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
    <input type="hidden" id="userid" value="{{ session('userid') }}">
    <div class="card" style="margin-top: -30px">
  <div class="card-header">
  <div class="row">
    <nav style="width: 100%;">
      <div class="nav nav-tabs col-12" id="nav-tab" role="tablist" style="border-bottom: 0;">

        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
           style="color: #fff; background-color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; text-align: left; border: 2px solid #007bff;">
          Surat Pengiriman Barang
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
           style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
          Invoice Belum Diotorisasi
        </a>
        <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="nav-profile1" aria-selected="false"
           style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
          Invoice Sudah Diotorisasi
        </a>
      </div>
    </nav>
  </div>
  </div>
  <div class="card-body" style="padding: 0">
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">

                <table id="tabel" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">No. Bukti</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col" style="min-width: 150px">Customer</th>
                      <th style="padding: 4px 12px;" scope="col">No So</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl So</th>


                    </tr>
                  </thead>


                  <tbody id="tabel_data" class="text-left" >
                    @for ($i = 0; $i < count($tempOutstanding); $i++)
                  <tr>
                    <td class="text-center">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonAddDetail('{{ $tempOutstanding[$i]->NoBukti }}')">
                        <i class="bi bi-info"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('{{ $tempOutstanding[$i]->Noso }}' , '{{ $tempOutstanding[$i]->NamaCustSupp }}' , '{{ $tempOutstanding[$i]->KodeCustSupp }}' , '{{ $tempOutstanding[$i]->TglSO }}' , '{{ $tempOutstanding[$i]->PPNCUST }}')">
                        <i class="bi bi-plus">
                      </i></button>
                    </td>
                    <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
                    <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->Tanggal)) !!}</td>
                    <td>{{ $tempOutstanding[$i]->NamaCustSupp }}</td>
                    <td>{{ $tempOutstanding[$i]->Noso }}</td>
                    <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TglSO)) !!}</td>

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
        <div class="col-12">
          <div class="table-responsive">

                <table id="tabel2" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">No Invoice</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">Customer</th>
                      <th style="padding: 4px 12px;" scope="col">No SPB</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl SPB</th>
                      <th style="padding: 4px 12px;" scope="col">No SO</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl SO</th>
                      <th style="padding: 4px 12px;" scope="col">No Pajak</th>
                      <th style="padding: 4px 12px;" scope="col">PO Cust</th>

                      <th style="padding: 4px 12px;" scope="col">DPP</th>
                      <th style="padding: 4px 12px;" scope="col">PPN</th>
                      <th style="padding: 4px 12px;" scope="col">Total</th>



                      <th style="padding: 4px 12px;" scope="col">Batal</th>
                      <th style="padding: 4px 12px;" scope="col">User Batal</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl Batal</th>


                    </tr>
                  </thead>


                  <tbody id="tabel2_data" class="text-left">

                    @for ($i = 0; $i < count($tempOutstanding2); $i++)
                    <tr>
                      <td class='text-center' style="vertical-align:middle">
                          <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $tempOutstanding2[$i]->NoBukti }}')">
                          <i class="bi bi-info"></i>
                          </button>
                          <button style="" class="btn btn-primary btn-sm" type="button"   onclick="openPrintModal('{{ $tempOutstanding2[$i]->NoBukti }}')" ><i class="bi bi-printer"></i>
                          </button>
                          <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding2[$i]->NoBukti }}' , '{{ $tempOutstanding2[$i]->IsOtorisasi1 }}')"><i class="bi bi-pen"></i></button>
                          @if ($tempOutstanding2[$i]->IsOtorisasi1 == 1)
                          <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}')"><i class="bi bi-key"></i></button>
                          @else
                          <button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}')"><i class="bi bi-key"></i></button>
                          <!-- <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}')"><i class="bi bi-key"></i></button> -->

                          @endif

                        </td>
                        <td>{{ $tempOutstanding2[$i]->NoBukti }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->Tanggal)) !!}</td>
                        <td>{{ $tempOutstanding2[$i]->NamaCustSupp }}</td>
                        <td>{{ $tempOutstanding2[$i]->NoSPB }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->TglSPB)) !!}</td>
                        <td>{{ $tempOutstanding2[$i]->NOSO }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->TGLSO)) !!}</td>


                        <td>{{ $tempOutstanding2[$i]->NoPajak }}</td>
                        <td>{{ $tempOutstanding2[$i]->NoPesanan }}</td>

                        <!-- <td>{{ number_format($tempOutstanding2[$i]->totdpp , 2 ,'.' , ',') }}</td> -->
                        <td class='text-right' >{{ number_format($tempOutstanding2[$i]->totdpp , 2 ,'.' , ',') }}</td>

                        <td class='text-right'>{{ number_format($tempOutstanding2[$i]->totppn , 2 ,'.' , ',') }}</td>

                        <td class='text-right'>{{ number_format($tempOutstanding2[$i]->totnet , 2 ,'.' , ',') }}</td>





                        @if ($tempOutstanding2[$i]->Isbatal)
                                  <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                                @else
                                <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                                @endif


                        <td>{{ $tempOutstanding2[$i]->userBatal }}</td>
                        <td>{!! $tempOutstanding2[$i]->tglBatal ? date("Y/m/d", strtotime($tempOutstanding2[$i]->tglBatal)) : '' !!}</td>

                    </tr>
                    @endfor

                  </tbody>
                </table>
          </div>
        </div>
      </div>
    </div>


    <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile1-tab">
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">

                <table id="tabel3" class="table table-bordered table-striped"  >
                  <thead class="text-center bg-primary text-white">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>
                      <th style="padding: 4px 12px;" scope="col">No Invoice</th>
                      <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                      <th style="padding: 4px 12px;" scope="col">Customer</th>
                      <th style="padding: 4px 12px;" scope="col">No SPB</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl SPB</th>
                      <th style="padding: 4px 12px;" scope="col">No SO</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl SO</th>
                      <th style="padding: 4px 12px;" scope="col">No Pajak</th>
                      <th style="padding: 4px 12px;" scope="col">PO Cust</th>

                      <th style="padding: 4px 12px;" scope="col">DPP</th>
                      <th style="padding: 4px 12px;" scope="col">PPN</th>
                      <th style="padding: 4px 12px;" scope="col">Total</th>


                      <th style="padding: 4px 12px;" scope="col">User Oto1</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl Oto1</th>

                      <th style="padding: 4px 12px;" scope="col">Batal</th>
                      <th style="padding: 4px 12px;" scope="col">User Batal</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl Batal</th>


                    </tr>
                  </thead>


                  <tbody id="tabel3_data" class="text-left">

                    @for ($i = 0; $i < count($tempOutstanding3); $i++)
                    <tr>
                      <td class='text-center' style="vertical-align:middle">
                          <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $tempOutstanding3[$i]->NoBukti }}')">
                          <i class="bi bi-info"></i>
                          </button>
                          @if ($tempOutstanding3[$i]->IsOtorisasi1 == 1)
                          <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding3[$i]->NoBukti }}')"><i class="bi bi-key"></i></button>
                          @else
                          <button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('{{ $tempOutstanding3[$i]->NoBukti }}')"><i class="bi bi-key"></i></button>

                          @endif

			                    <button style="" class="btn btn-primary btn-sm" type="button"   onclick="openPrintModal('{{ $tempOutstanding3[$i]->NoBukti }}')" ><i class="bi bi-printer"></i>
                          </button>

                        </td>
                        <td>{{ $tempOutstanding3[$i]->NoBukti }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding3[$i]->Tanggal)) !!}</td>
                        <td>{{ $tempOutstanding3[$i]->NamaCustSupp }}</td>
                        <td>{{ $tempOutstanding3[$i]->NoSPB }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding3[$i]->TglSPB)) !!}</td>
                        <td>{{ $tempOutstanding3[$i]->NOSO }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding3[$i]->TGLSO)) !!}</td>

                        <td>{{ $tempOutstanding3[$i]->NoPajak }}</td>
                        <td>{{ $tempOutstanding3[$i]->NoPesanan }}</td>
                        <td class='text-right'>{{ number_format($tempOutstanding3[$i]->totdpp , 2 ,'.' , ',') }}</td>
                        <td class='text-right'>{{ number_format($tempOutstanding3[$i]->totppn , 2 ,'.' , ',') }}</td>
                        <td class='text-right'>{{ number_format($tempOutstanding3[$i]->totnet , 2 ,'.' , ',') }}</td>

                        <td>{{ $tempOutstanding3[$i]->OtoUser1 }}</td>
                        <td>{!! $tempOutstanding3[$i]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding3[$i]->TglOto1)) : '' !!}</td>


                        @if ($tempOutstanding3[$i]->Isbatal)
                                  <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                                @else
                                <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                                @endif


                        <td>{{ $tempOutstanding3[$i]->userBatal }}</td>
                        <td>{!! $tempOutstanding3[$i]->tglBatal ? date("Y/m/d", strtotime($tempOutstanding3[$i]->tglBatal)) : '' !!}</td>

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


<div id="page2" class="container-fluid mainpage" style="display: none">
  <!-- <div id="" class="modal-content "> -->
    <div id= "modalAdd" class="showhideform">

      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Form Add</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
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

      </div>
      <!-- <h5 class="modal-title" id="modalTitleDetail">Detail</h5> -->




    <div id="" class="mt-4">
    <!-- <div class="modal-body"> -->
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-md-3 text-right">

          </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_customer" placeholder="No SJ" disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">



            <div class="col-md-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_noso" placeholder="No SO" disabled>
              </div>
            </div>
            </div>
          </div>
          <!-- <div class="col-md-3">
            <div class="row">

            </div>
          </div> -->
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl SO</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggalso" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>


              </div>
              <div class="row" style="margin-top: -10px">

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

          <!-- <div class="col-md-8">

          </div> -->
          <div class="col-md-3">
            <div class="row">

            </div>

          </div>


        </div>


        </div>



      <!-- </div> -->



    <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

          <table id="addTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">v</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Tgl SPB</th>
                <th style="padding: 4px 12px;" scope="col">No PO</th>

              </tr>
            </thead>


            <tbody id="addTableData" class="" >
              <tr >

                  <td colspan=4 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>

    <div class="container-fluid mt-4">

      <div id="" class="row">
        <div class="col-12 text-right">

          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
          <button id="" type="button" onclick="submitAdd()" class="btn btn-primary" style="height: 30px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;">Submit Add</button>
        </div>
      </div>
    </div>
    </div>



    <div id= "modalKoreksi" class="showhideform">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Form Koreksi</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
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
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>
      <!-- <h5 class="modal-title" id="modalTitleDetail">Detail</h5> -->




    <div id="" class="mt-4">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_koreksi_customer" placeholder="" disabled>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">



            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_koreksi_alamat"  disabled></textarea>
              </div>

            </div>
          </div>

            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_koreksi_alamatx"  disabled></textarea>
              </div>

            </div>




            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->

                <div class="col-md-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_koreksi_nopo" placeholder="" disabled>
                  </div>
                </div>

                <!-- </div>

              </div> -->



            </div>

            <!-- <div class="row"> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-md-8">

                    <select id="input_koreksi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>Tunai</option>
                      <option value=1 >Kredit</option>
                    </select>
                  </div>
                </div>

              <!-- </div> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_koreksi_hari" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              <!-- </div> -->



            <!-- </div> -->






          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>



            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_koreksi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                      <!-- <input type="text" class="form-control" id="input_koreksi_tipeppn" placeholder="" disabled> -->
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->





            </div>


            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4" >
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_koreksi_uangmuka" placeholder="" onblur="onChangeUangMuka('nuangmuka' , 'input_koreksi_uangmuka' , 'uang muka')">
                    </div>
                  </div>
                <!-- </div>

              </div> -->





            </div>



          </div>

          <div class="col-md-3">
            <div class="row">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_koreksi_valas" placeholder="" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_koreksi_kurs" placeholder="" onblur="onChangeKurs('kurs' , 'input_koreksi_kurs')">
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">



              <div class="col-md-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_koreksi_catatan"  onblur="onChangeHeader('footnote' , 'input_koreksi_catatan', 'catatan')"></textarea>
                </div>
              </div>

            </div>

          </div>

        </div>



        </div>

      </div>



    <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

          <table id="koreksiTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="koreksiTableData" class="" >
              <tr>
                <td colspan=11 class="text-center">Belum ada data</td>
              </tr>

            </tbody>

          </table>
    </div>

    </div>

    <hr/>

    <div class="container-fluid" style="margin-left: 10px">
    <div class="row" >

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-15px;">Disc %</label>
          </div>
          <div class="col-9" style="margin-left:-35px;">
            <input type="number" class="form-control text-right" id="input_addx_disc"  value="0.00" disabled>
          </div>
        </div>
      </div>


      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px;margin-left:-10px;">DiscRp</label>
          </div>
          <div class="col-9" style="margin-left:-35px;">
            <input type="number" class="form-control text-right" id="input_addx_discrp"  value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-15px;">DPP</label>
          </div>
          <div class="col-9" style="margin-left:-65px;">
            <input type="text" class="form-control text-right" id="input_addx_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-45px;">PPN</label>
          </div>
          <div class="col-9" style="margin-left:-90px;">
            <input type="text" class="form-control text-right" id="input_addx_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-70px;">GrandTotal</label>
          </div>
          <div class="col-9" style="margin-left:-50px;">
            <input type="text" class="form-control text-right" id="input_addx_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>

    </div>

    </div>

    <div class="container-fluid mt-4">
      <div id="" class="row">
        <div class="col-12 text-right">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->

          <!-- <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
        </div>
      </div>
    </div>

    </div>















    <!-- </div> -->




</div>

<!-- end page 4 -->
<div id= "page4" class="container-fluid mainpage" style="display: none">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Form Detail</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonCloseFormDetail()">Close</button>
          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>
      <!-- <h5 class="modal-title" id="modalTitleDetail">Detail</h5> -->

    <div id="" class="mt-4">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_detail_customer" placeholder="" disabled>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamat"  disabled></textarea>
              </div>
            </div>
          </div>

            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_alamatx"  disabled></textarea>
              </div>

            </div>

            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->

                <div class="col-md-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_detail_nopo" placeholder="" disabled>
                  </div>
                </div>

                <!-- </div>

              </div> -->

            </div>

            <!-- <div class="row"> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-md-8">

                    <select id="input_detail_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>Tunai</option>
                      <option value=1 >Kredit</option>
                    </select>
                  </div>
                </div>

              <!-- </div> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_detail_hari" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              <!-- </div> -->

            <!-- </div> -->

          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_detail_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>
                        <option value=2 >Include</option>
                      </select>
                      <!-- <input type="text" class="form-control" id="input_detail_tipeppn" placeholder="" disabled> -->
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->

            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_detail_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4" >
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_detail_uangmuka" placeholder="" onblur="onChangeUangMuka('nuangmuka' , 'input_detail_uangmuka' , 'uang muka')" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->

            </div>

          </div>

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_detail_valas" placeholder="" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_detail_kurs" placeholder="" onblur="onChangeKurs('kurs' , 'input_detail_kurs')" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_detail_catatan"  onblur="onChangeHeader('footnote' , 'input_detail_catatan', 'catatan')" disabled></textarea>
                </div>
              </div>

            </div>

          </div>

        </div>

        </div>

      </div>

    <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

          <table id="detailTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              </tr>
            </thead>
            <tbody id="detailTableData" class="" >
              <tr>
                <td colspan=10 class="text-center">Belum ada data</td>
              </tr>

            </tbody>

          </table>
    </div>

    </div>

    <hr/>

    <div class="container-fluid" style="margin-left: 10px">
    <div class="row" >

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-15px;">Disc %</label>
          </div>
          <div class="col-9" style="margin-left:-35px;">
            <input type="number" class="form-control text-right" id="input_detailx_disc"  value="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px;margin-left:-10px;">DiscRp</label>
          </div>
          <div class="col-9" style="margin-left:-35px;">
            <input type="number" class="form-control text-right" id="input_detailx_discrp"  value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-15px;">DPP</label>
          </div>
          <div class="col-9" style="margin-left:-65px;">
            <input type="text" class="form-control text-right" id="input_detailx_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-45px;">PPN</label>
          </div>
          <div class="col-9" style="margin-left:-90px;">
            <input type="text" class="form-control text-right" id="input_detailx_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-70px;">GrandTotal</label>
          </div>
          <div class="col-9" style="margin-left:-50px;">
            <input type="text" class="form-control text-right" id="input_detailx_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>

    </div>

    </div>

    <div class="container-fluid mt-4">
      <div id="" class="row">
        <div class="col-12 text-right">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->

          <!-- <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
        </div>
      </div>
    </div>

    </div>
<!-- end page 4 -->


<!-- page 5 -->
<div id= "page5" class="container-fluid mainpage" style="display: none">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Form Detail</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="buttonCloseFormAddDetail()">Close</button>
          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>
      <!-- <h5 class="modal-title" id="modalTitleDetail">Detail</h5> -->

    <div id="" class="mt-4">
    <div class="">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <input type="hidden" name="noUrut" id="input_add_detail_nourut" value="" />
        <div class="row">

          <div class="col-md-3">
            <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_add_detail_customer" placeholder="" disabled>
              </div>
            </div>
          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_detail_alamat"  disabled></textarea>
              </div>
            </div>
          </div>

            <div class="row" style="margin-top: -10px">

            <!-- <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_add_detail_alamatx"  disabled></textarea>
              </div>
            </div> -->

            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_detail_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->

                <div class="col-md-4">
                  <div class="form-group">
                    <label>No SO</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_add_detail_nopo" placeholder="" disabled>
                  </div>
                </div>

                <!-- </div>

              </div> -->

            </div>

            <!-- <div class="row"> -->
              <!-- <div class="col-6"> -->
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tanggal</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="date" class="form-control text-center" id="input_add_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                    </div>
                  </div>

                </div>

              <!-- </div> -->
              <!-- <div class="col-6"> -->

                <!-- <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_add_detail_sales" placeholder="" disabled>
                    </div>
                  </div>
                </div> -->

              <!-- </div> -->

            <!-- </div> -->

          </div>

        </div>

        </div>

      </div>

    <div class="container-fluid mt-4" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

          <table id="detailAddTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">Gudang</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Qty Retur</th>

              </tr>
            </thead>
            <tbody id="detailAddTableData" class="" >
              <tr>
                <td colspan=7 class="text-center">Belum ada data</td>
              </tr>

            </tbody>

          </table>
    </div>

    </div>

    <hr/>

    <div class="container-fluid mt-4">
      <div id="" class="row">
        <div class="col-12 text-right">
          <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->

          <!-- <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
        </div>
      </div>
    </div>

    </div>
<!-- end page 5 -->



<div id="page3" class="container-fluid mainpage" style="display: none">

  <!-- <div id="" class="modal-content "> -->
    <div id= "" class="">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row" style="margin-top: -60px">
          <div class="col-6 text-left">
            <h1>Form Otorisasi</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg" style="
            height: 30px;
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
      <!-- <button onclick="loadAll()">tes</button> -->
      </div>


    <div id="" class="">
    <div class="modal-body">
      <!-- <h1>Tes Modal</h1> -->

      <div class="container-fluid">
        <!-- <input type="hidden" name="noUrut" id="input_koreksi_nourut" value="" /> -->
        <div class="row">

          <div class="col-md-3">
            <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-md-3 text-right">

          </div> -->
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi_customer" placeholder="" disabled>
              </div>
            </div>

          </div>
            <div class="row" style="margin-top: -10px">



            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_otorisasi_alamat"  disabled></textarea>
              </div>

            </div>

          </div>
            <div class="row" style="margin-top: -10px">

            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_alamatx"  disabled></textarea>
              </div>

            </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>No PO</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nopo" placeholder="" disabled>
                </div>
              </div>
            </div>

            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->



            </div>

            <div class="row" style="margin-top: -10px">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_otorisasi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                        <option value=0 selected>Tunai</option>
                        <option value=1 >Kredit</option>
                      </select>

                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->
              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_otorisasi_hari" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->



            </div>






          </div>
          <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>
              <div class="row" style="margin-top: -10px">

              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <select id="input_otorisasi_tipeppn" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled >
                        <option value=0 selected>None</option>
                        <option value=1 >Exclude</option>

                        <option value=2 >Include</option>
                      </select>
                      <!-- <input type="text" class="form-control" id="input_otorisasi_tipeppn" placeholder="" disabled> -->
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

                <!-- </div>

              </div> -->





            </div>
              <div class="row" style="margin-top: -10px">

              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->

              </div>
                <div class="row" style="margin-top: -10px">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_otorisasi_uangmuka" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->





            </div>



          </div>

          <div class="col-md-3">
            <div class="row">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_valas" placeholder="" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_otorisasi_kurs" placeholder="" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">

              <div class="col-md-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_catatan"  disabled></textarea>
                </div>
              </div>

            </div>

          </div>

          <!-- <div class="col-md-8">

          </div>
          <div class="col-md-4">
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}"  >
                </div>
              </div>
            </div>

          </div> -->






        </div>










        </div>



      </div>



    <div class="container-fluid" style="overflow-x: auto;padding:0; margin:0; width:100%;" >

          <table id="otorisasiTable" class="table table-bordered table-striped"  >
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Kode brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
                <th style="padding: 4px 12px;" scope="col">No SPB</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Sat</th>
                <th style="padding: 4px 12px;" scope="col">Harga</th>
                <th style="padding: 4px 12px;" scope="col">Diskon</th>
                <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                <th style="padding: 4px 12px;" scope="col">Keterangan</th>

                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>


            <tbody id="otorisasiTableData" class="" >
              <tr >

                  <td colspan=10 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    </div>

    <hr/>

    <div class="container-fluid" style="margin-left: 10px">
    <div class="row" >

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-15px;">Disc %</label>
          </div>
          <div class="col-9" style="margin-left:-35px;">
            <input type="number" class="form-control text-right" id="input_otorisasix_disc"  value="0.00" disabled>
          </div>
        </div>
      </div>


      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px;margin-left:-10px;">DiscRp</label>
          </div>
          <div class="col-9" style="margin-left:-35px;">
            <input type="number" class="form-control text-right" id="input_otorisasix_discrp"  value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-15px;">DPP</label>
          </div>
          <div class="col-9" style="margin-left:-65px;">
            <input type="text" class="form-control text-right" id="input_otorisasix_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-45px;">PPN</label>
          </div>
          <div class="col-9" style="margin-left:-90px;">
            <input type="text" class="form-control text-right" id="input_otorisasix_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="row">
          <div class="col-4 d-flex align-items-center">
            <label style="margin-top:6px; margin-left:-70px;">GrandTotal</label>
          </div>
          <div class="col-9" style="margin-left:-50px;">
            <input type="text" class="form-control text-right" id="input_otorisasix_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>

    </div>

    </div>


    <div class="container-fluid">
      <div id="" class="row mt-4">
        <div class="col-12 text-right">

          <!-- <button id="" type="button" class="btn btn-primary" onclick="submitOtorisasi()">Otorisasi</button> -->
          <button id="" type="button" onclick="submitOtorisasi()" class="btn btn-primary" style="height: 30px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;">Otorisasi</button>
        </div>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
      </div>
    </div>


    </div>















    <!-- </div> -->


</div>





<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">







    </div>
  </div>

<!-- End modal add-->




<!-- start modal oto -->
<div class="modal fade" id="formOtorisasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">








    </div>
  </div>

<!-- End modal oto-->
<div class="modal fade" id="modalListChecklistInvoice" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Pilih Invoice</h5>
  <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div id="" class="row">
          <div class="col-12 text-right">

            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button> -->
            <button id="" type="button" onclick="openPrintModalAll()" class="btn btn-primary" style="height: 30px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;">Print</button>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="table-responsive">

                  <table id="tabelListInvoice" class="table table-bordered table-striped"  >
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px;" class="text-center" scope="col">v</th>
                        <th style="padding: 4px 12px;" scope="col">No Invoice</th>
                        <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px;" scope="col">Customer</th>
                        <th style="padding: 4px 12px;" scope="col">No SPB</th>

                        <th style="padding: 4px 12px;" scope="col">Tgl SPB</th>
                        <th style="padding: 4px 12px;" scope="col">No SO</th>
                        <th style="padding: 4px 12px;" scope="col">Tgl SO</th>


                      </tr>
                    </thead>


                    <tbody id="tabelListInvoice_data" class="text-left">
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>



                    </tbody>
                  </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- start modal print-->
  <div class="modal fade" id="modalPrint" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Pilih Design Cetak</h5>
	  <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('default')" hidden>
            Invoice Penjualan Adaro
          </button>

          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('design3')">
            Invoice
          </button>

          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('jbg')">
            Invoice A4
          </button>

          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('kwitansi')">
            Kwitansi
          </button>

          <button class="btn btn-primary w-100 " onclick="choosePrint('spb')">
            Cetak Ulang SPB
          </button>
        </div>

      </div>
    </div>
  </div>
<!-- end modal print-->

<div class="modal fade" id="modalPrintAll" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Pilih Design Cetak</h5>
  <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <button class="btn btn-primary w-100 mb-2" onclick="submitPrintAll('default')" hidden>
          Invoice Penjualan Adaro
        </button>

        <button class="btn btn-primary w-100 mb-2" onclick="submitPrint3All('default')">
          Invoice
        </button>

        <button class="btn btn-primary w-100 mb-2" onclick="submitPrintJBGAll('default')">
          Invoice A4
        </button>

        <button class="btn btn-primary w-100 mb-2" onclick="submitPrintKwitansiAll('default')">
          Kwitansi
        </button>

        <!-- <button class="btn btn-primary w-100 " onclick="submitPrintSPBAll('spb')">
          Cetak Ulang SPB
        </button> -->
      </div>

    </div>
  </div>
</div>


<!-- start modal print-->
  <div class="modal fade" id="modalPilihSPB" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Pilih Nomor SPB</h5>
	    <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div id='tempatPilihSPB' class="modal-body">
          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('default')" hidden>
            bro
          </button>
        </div>

      </div>
    </div>
  </div>
<!-- end modal print-->


<!-- start modal koreksi detail-->
<div class="modal fade" id="formPrintLPB" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <label>No SPB</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_nosj" placeholder="No TRF" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_gdg" placeholder="Kode Cust" required disabled>
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
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="tablePrint" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Merk</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Satuan</th>

                    </tr>
                  </thead>


                  <tbody id="detailTablePrint" class="text-right" >
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
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="submitPrintSPBFinal()"  >Print</button>

          </div>




      </div>



    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi detail-->



@endsection

@section('js')
<script type="text/javascript">

let arrayListInvoice = []
let dataTableAdd = []
let dataTableKoreksi = []
let selectedNoBukti = ''
let dataChecklistInvoice = []
let dataPrint = []

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]

        ]
        });

        $("#tabelListInvoice").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
            // { "type": "date", "targets": [3] },
            // {  "className": "text-right", "targets": [9,10,11,12] },
            // "columns" : [{"width" : "20px"}]

          ]
          });

        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
            // { "type": "date", "targets": [3] },
            // {  "className": "text-right", "targets": [9,10,11,12] },
            // "columns" : [{"width" : "20px"}]

          ]
          });

          $("#tabel3").DataTable({
            "lengthChange": false,
              "paging": false ,
              "order": [[1, 'asc']],
              "columnDefs": [
                {"targets" :[0] , 'orderable' : false},
              // { "type": "date", "targets": [3] },
              // {  "className": "text-right", "targets": [9,10,11,12] },
              // "columns" : [{"width" : "20px"}]

            ]
            });



  //   formAddListItem
});

function buttonClickInvoice (i) {
  console.log("buttonClickInvoice")
  console.log(i)

  console.log(dataChecklistInvoice[i])
  if (document.getElementById(`invoiceChecklist${i}`).checked) {

    // row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
    // tempData.push(dataTableAdd[i])
    console.log('checked')

    arrayListInvoice.push(dataChecklistInvoice[i].NoBukti)
  } else {
    console.log('unchecked')
    const index = arrayListInvoice.findIndex(nobukti => nobukti == dataChecklistInvoice[i].NoBukti)
    arrayListInvoice.splice(index,1)
  }
  console.log('res')
  console.log(arrayListInvoice)

}


function printAll () {
  arrayListInvoice = []
  $.ajax({
    url: "{!! url('invoicepenjualangetlistinvoicecetak') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {

      console.log(res)

      dataChecklistInvoice = res
      $('#tabelListInvoice').DataTable().destroy();
      rowTable = ``

      dataChecklistInvoice.forEach((item, i) => {
          rowTable += `
            <tr>
            <td>
            <div class="form-check text-center">
              <input id="invoiceChecklist${i}" class="form-check-input" type="checkbox" value="" onchange="buttonClickInvoice(${i})">
            </div>
            </td>

            <td>${item.NoBukti }</td>
            <td>${formatDate(item.Tanggal , '/')}</td>
            <td>${item.NamaCustSupp }</td>
            <td>${item.NoSPB }</td>
            <td>${formatDate(item.TglSPB , '/')}</td>
            <td>${item.NOSO }</td>
            <td>${formatDate(item.TGLSO , '/')}</td>

            </tr>
          `
      });

      document.getElementById("tabelListInvoice_data").innerHTML = rowTable
      // document.getElementById("input_add_tanggal").value = formatDate(new Date())
      $("#tabelListInvoice").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
            {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]

        ]
        });

      // $('.showhideform').hide();
      $("#modalListChecklistInvoice").modal('toggle')
      // $('#modalListChecklistInvoice').toggle();


      // $("#form").modal('toggle')

      // $('#page2').show();
      // $('#page1').hide();



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function getDateDiff (xdate1 , xdate2) {
  const date1 = new Date(xdate1);
  const date2 = new Date(xdate2);

// Get total difference in milliseconds
  const diffInMs = date2 - date1

// Convert milliseconds into other units
  const diffInDays = diffInMs / (1000 * 60 * 60 * 24);

  console.log(diffInDays);
  return diffInDays

}

function openPrintModal (nobukti) {
  selectedNoBukti = nobukti
  $('#modalPrint').modal('show')
}
function openPrintModalAll(nobukti) {
  if (!arrayListInvoice.length) {


    alertify.warning('Tidak ada invoice dipilih')
    return
  }
  
  $('#modalListChecklistInvoice').modal('toggle')
  $('#modalPrintAll').modal('toggle')
}


function openPilihSPB(nobukti) {
  let _token = $("#_token").val();
  selectedNoBukti = nobukti;
  $('#modalPilihSPB').modal('show');

  let res = [];

  $.ajax({
    url: "{!! url('ambilNomorSPB') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: nobukti
    },
    success: function(data) {
      console.log(data);
      res = data;
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan silahkan refresh browser');
    }
  });

  let buttonsHTML = '';

  if (res && res.length > 0) {
    res.forEach(function(item) {
      buttonsHTML += `
        <button class="btn btn-primary w-100 mb-2" onclick="submitPrintSPB('${item.NoSPB}')">
          ${item.NoSPB}
        </button>
      `;
    });
  } else {
    buttonsHTML = `<p class="text-center text-muted">Tidak ada data SPB</p>`;
  }

  document.getElementById('tempatPilihSPB').innerHTML = buttonsHTML;
}

function choosePrint (type) {
  $('#modalPrint').modal('hide')

  if (type === 'default') {
    submitPrint(selectedNoBukti)
  }
  else if (type === 'design3') {
    submitPrint3(selectedNoBukti)
  }
  else if (type === 'jbg') {
    submitPrintJBG(selectedNoBukti)
  }
  else if (type === 'kwitansi') {
    submitPrintKwitansi(selectedNoBukti)
  }
  else if (type === 'spb') {
    openPilihSPB(selectedNoBukti)
  }
}


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}

function closeShowHideItem () {
  $('.showhide').hide();

}


function submitAdd () {

    // $('.formA').hide();
    // $('#modalA').show();
    // return

    let _token = $("#_token").val();
    let tempData = []
    for (let i = 0; i < dataTableAdd.length; i++) {

      if (document.getElementById(`addChecklist${i}`).checked) {

        // row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
        tempData.push(dataTableAdd[i])
      }
    }

    if(!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }
    let inputDate = $("#input_add_tanggal").val()
    let checkDate = new Date($("#input_add_tanggal").val())

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }
    console.log(checkDate.getDate())
    console.log(new Date().getDate())

    let xcheckdate = getDateDiff( new Date(), checkDate)
    console.log(new Date(), checkDate)
    console.log(xcheckdate)
    console.log("===========")
    if (xcheckdate > 0 || xcheckdate < -6 ) {
      alertify.warning("Tanggal invoice maks 5 hari sebelum")
      return
    }

    // return
    // return
    console.log(checkDate)
    console.log(tempData)


    $.ajax({
      url: "{!! url('invoicepenjualanspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData,
        inputDate: inputDate

      },
      success: function(res) {
        console.log(res)

        if (res.status == 1) {


        // if (res.)


        loadAll()
        alertify.success("Berhasil menambah invoice penjualan")
        // $("#form").modal('toggle')
        // buttonCloseForm()

        buttonKoreksifirstadd(res.nobuktix , 0)


        }

         if (res == 46) {
          console.log('error kode :',res)
          alertify.warning('Periode tidak sesuai')
        return
       


        }


      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })



}


function buttonAdd (noso , namacust, kodecust, tglso , ppncust) {



  console.log('buttonAdd' , noso , namacust, kodecust, tglso)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('invoicepenjualanlistso') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noso

    },
    success: function(res) {

      console.log(res)

      document.getElementById("input_add_customer").value = kodecust + ' - ' + namacust
      document.getElementById("input_add_noso").value = noso
      document.getElementById("input_add_tanggalso").value = formatDate(tglso)
      document.getElementById("input_add_tanggal").value = formatDate(new Date())

      dataTableAdd = res.list

      rowTable = ``

      dataTableAdd.forEach((item, i) => {
          rowTable += `
            <tr>
              <td>
                <div class="form-check text-center">
                  <input id="addChecklist${i}" class="form-check-input" type="checkbox" value="" >
                </div>
              </td>
              <td>${item.Nobukti}</td>
              <td>${formatDate(item.Tanggal , '/')}</td>
              <td>${item.NoPesanan ? item.NoPesanan : ''}</td>
            </tr>
          `
      });

      document.getElementById("addTableData").innerHTML = rowTable
      document.getElementById("input_add_tanggal").value = formatDate(new Date())


      $('.showhideform').hide();
      $('#modalAdd').show();


      // $("#form").modal('toggle')

      $('#page2').show();
      $('#page1').hide();



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function buttonBatalOtorisasi (nobukti) {
  console.log('buttonBatalOtorisasi' , nobukti)
  let akses = $("#akses_isotorisasi1").val();

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
          url: "{!! url('invoicepenjualanspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            console.log('!', res)
            loadAll()

            // lockFormAdd()

            alertify.success('Berhasil Batal Otorisasi Invoice')

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
  let _token = $("#_token").val();
  // let nobukti = $("#input_otorisasi_nobukti").val();
  //  let nobukti = $tempOutstanding2[$i]->NoBukti ;
   console.log(nobukti)
  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  $.ajax({
    url: "{!! url('invoicepenjualanspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log('!', res)
      loadAll()

      // lockFormAdd()
      // $("#formOtorisasi").modal('toggle')
      buttonCloseForm()
      alertify.success('Berhasil Otorisasi')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
	}
    });

}

function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log('buttonOtorisasi' , nobukti)
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('invoicepenjualanspdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      if ( !res.length) {
        alertify.warning("Data tidak ditemukkan")

      }
      if (res.length) {

        rowTable = ``
        res.forEach((item, i) => {
            rowTable += `
              <tr>
                <td>${item.KodeBrg}</td>
                <td>${item.NAMABRG}</td>
                <td>${item.NamabrgKom}</td>
                <td>${item.NoSPB}</td>
                <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                <td>${item.Satuan}</td>
                <td class="text-right">${item.HARGA ? formatAngka(parseFloat(item.HARGA).toFixed(2)) : '0.00'}</td>
                <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
                <td class="text-right">${item.SubTotal ? formatAngka(parseFloat(item.SubTotal).toFixed(2)) : '0.00'}</td>
                <td>${item.KetDetail ? item.KetDetail : ''}</td>

              </tr>

            `
        });

        document.getElementById("otorisasiTableData").innerHTML = rowTable
        document.getElementById("input_otorisasi_customer").value = res[0].NamaCustSupp

        document.getElementById("input_otorisasi_alamat").value = res[0].Alamat
        document.getElementById("input_otorisasi_alamatx").value = res[0].AlamatX
        document.getElementById("input_otorisasi_catatan").value = res[0].FootNote
        document.getElementById("input_otorisasi_nobukti").value = res[0].NoBukti
        document.getElementById("input_otorisasi_tanggal").value = formatDate(res[0].Tanggal)
        document.getElementById("input_otorisasi_valas").value = res[0].Valas
        document.getElementById("input_otorisasi_kurs").value = res[0].Kurs
        document.getElementById("input_otorisasi_tipeppn").value = res[0].PPN
        document.getElementById("input_otorisasi_nopo").value = res[0].NoPesanan
        document.getElementById("input_otorisasi_pembayaran").value = res[0].TIPEBAYAR
        document.getElementById("input_otorisasi_hari").value = res[0].HARI
        document.getElementById("input_otorisasi_sales").value = res[0].NamaSls
        document.getElementById("input_otorisasi_uangmuka").value = parseFloat(res[0].nUangMuka).toFixed(2)


        document.getElementById("input_otorisasix_disc").value = formatAngkaX(res[0].DISC)
        document.getElementById("input_otorisasix_discrp").value = formatAngkaX(res[0].DiscRp)
        document.getElementById("input_otorisasix_ppn").value = formatAngkaX(res[0].TotalPPn)
        document.getElementById("input_otorisasix_dpp").value = formatAngkaX(res[0].TotalDPP)
        document.getElementById("input_otorisasix_grandtotal").value = formatAngkaX(res[0].TotalNetto)

        // $("#formOtorisasi").modal('toggle')

        $('#page1').hide();
        $('#page3').show();
      }




    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function refreshDataTableKoreksi (nobukti , tipeRefresh = '') {
  console.log('refreshDataTableKoreksi')
  let _token = $("#_token").val();

  let resRefresh = 0
  $.ajax({
    url: "{!! url('invoicepenjualanspdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      if (tipeRefresh && !res.length) {
        alertify.warning("Data habis")
        // $("#form").modal('toggle')
        buttonCloseForm()
      }
      if (res.length) {

        dataTableKoreksi = res
        rowTable = ``

        dataTableKoreksi.forEach((item,i) => {
          dataTableKoreksi[i].NAMABRG = dataTableKoreksi[i].NAMABRG ? dataTableKoreksi[i].NAMABRG.replaceAll('"', "''") : ''
          dataTableKoreksi[i].NamabrgKom = dataTableKoreksi[i].NamabrgKom ? dataTableKoreksi[i].NamabrgKom.replaceAll('"', "''") : ''

        })

        dataTableKoreksi.forEach((item, i) => {
            rowTable += `
              <tr>
                <td>${item.KodeBrg}</td>
                <td>${item.NAMABRG}</td>
                <td>${item.NamabrgKom ? item.NamabrgKom : ''}</td>
                <td>${item.NoSPB ? item.NoSPB : ''}</td>
                <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                <td>${item.Satuan}</td>
                <td class="text-right">${item.HARGA  ? formatAngka(parseFloat(item.HARGA).toFixed(2))  : '0.00'}</td>
                <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2))  : '0.00'}</td>
                <td class="text-right">${item.SubTotal ? formatAngka(parseFloat(item.SubTotal).toFixed(2))  : '0.00'}</td>
                <td>${item.KetDetail ? item.KetDetail : ''}</td>
                <td class="text-center">
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i} ,'${item.NoBukti}' , '${item.Urut}'  )"><i class="bi bi-trash"></i></button>
                </td>
              </tr>

            `
        });
        document.getElementById("koreksiTableData").innerHTML = rowTable
        document.getElementById("input_koreksi_customer").value = dataTableKoreksi[0].NamaCustSupp

        document.getElementById("input_koreksi_alamat").value = dataTableKoreksi[0].Alamat
        document.getElementById("input_koreksi_alamatx").value = dataTableKoreksi[0].AlamatX
        document.getElementById("input_koreksi_catatan").value = dataTableKoreksi[0].FootNote
        document.getElementById("input_koreksi_nobukti").value = dataTableKoreksi[0].NoBukti
        document.getElementById("input_koreksi_tanggal").value = formatDate(dataTableKoreksi[0].Tanggal)
        document.getElementById("input_koreksi_valas").value = dataTableKoreksi[0].Valas
        document.getElementById("input_koreksi_kurs").value = dataTableKoreksi[0].Kurs
        document.getElementById("input_koreksi_tipeppn").value = dataTableKoreksi[0].PPN
        document.getElementById("input_koreksi_nopo").value = dataTableKoreksi[0].NoPesanan
        document.getElementById("input_koreksi_pembayaran").value = dataTableKoreksi[0].TIPEBAYAR
        document.getElementById("input_koreksi_hari").value = dataTableKoreksi[0].HARI
        document.getElementById("input_koreksi_sales").value = dataTableKoreksi[0].NamaSls
        document.getElementById("input_koreksi_uangmuka").value = parseFloat(dataTableKoreksi[0].nUangMuka).toFixed(2)

        document.getElementById("input_addx_disc").value = formatAngkaX(dataTableKoreksi[0].DISC)
        document.getElementById("input_addx_discrp").value = formatAngkaX(dataTableKoreksi[0].DiscRp)
        document.getElementById("input_addx_ppn").value = formatAngkaX(dataTableKoreksi[0].TotalPPn)
        document.getElementById("input_addx_dpp").value = formatAngkaX(dataTableKoreksi[0].TotalDPP)
        document.getElementById("input_addx_grandtotal").value = formatAngkaX(dataTableKoreksi[0].TotalNetto)



        resRefresh =  1;


      }



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
  return resRefresh

}

function buttonKoreksiDelete (index, nobukti, urut) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log('buttonKoreksiDelete' , index, nobukti, urut )


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"





        $.ajax({
          url: "{!! url('invoicepenjualanspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti,
            urut

          },
          success: function(res) {
            console.log('res', res)
            loadAll()
            refreshDataTableKoreksi(nobukti , 'D')

            // lockFormAdd()
            // $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

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



function onChangeKurs (field, idValue , alias = field) {
  // if (tipeform == 'add') {
  //   return
  // }
  let _token  = $("#_token").val()
  let nobukti = $("#input_koreksi_nobukti").val()
  let value = $(`#${idValue}`).val()
  console.log(value)
  if (Number(value) < 0) {
    value = '1.00'
    document.getElementById("input_koreksi_kurs").value = value
    // onChangeHeader(field, value , alias )
    // onChangeDetail(field, value , alias )


    $.ajax({
      url: "{!! url('invoicepenjualanonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        alertify.success(`update ${alias} berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    $.ajax({
      url: "{!! url('invoicepenjualanonchangedetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        // console.log()
        alertify.success(`update ${alias} detail berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    refreshDataTableKoreksi(nobukti)
    // tes4

    console.log('kurs A')

    alertify.warning('Kurs tidak bisa kurang dari 0')
  } else {
    console.log('kurs B')
    onChangeHeader(field, idValue , alias )
    onChangeDetail(field, idValue , alias )
    refreshDataTableKoreksi(nobukti)
  }

}

function onChangeUangMuka (field, idValue , alias = field) {
  // if (tipeform == 'add') {
  //   return
  // }
  let _token  = $("#_token").val()
  let nobukti = $("#input_koreksi_nobukti").val()
  let value = $(`#${idValue}`).val()
  console.log(value)
  if (Number(value) < 0) {
    value = '0.00'
    document.getElementById("input_koreksi_uangmuka").value = value
    // onChangeHeader(field, value , alias )
    // onChangeDetail(field, value , alias )


    $.ajax({
      url: "{!! url('invoicepenjualanonchangeheader') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        field,
        value,
        nobukti
      },
      success: function(res) {
        alertify.success(`update ${alias} berhasil`)

      },error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })






    alertify.warning('Uang Muka tidak bisa kurang dari 0')
  } else {
    onChangeHeader(field, idValue , alias )
  }

}

function onChangeHeader (field, idValue , alias = field) {
  // if(tipeform == 'add' ) {
  //
  //   return
  // }
  let _token  = $("#_token").val()
  let nobukti = $("#input_koreksi_nobukti").val()
  let value = $(`#${idValue}`).val()
  $.ajax({
    url: "{!! url('invoicepenjualanonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${alias} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeDetail (field, idValue , alias = field) {
  // if (tipeform == 'add') {
  //   return
  // }
  let _token  = $("#_token").val()
  let nobukti = $("#input_koreksi_nobukti").val()
  let value = $(`#${idValue}`).val()
  $.ajax({
    url: "{!! url('invoicepenjualanonchangedetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      // console.log()
      alertify.success(`update ${alias} detail berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}


function buttonKoreksi (nobukti , isoto) {
  console.log(' buttonKoreksi' , nobukti, isoto)
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  if (Number(isoto)) {
    alertify.warning('Nobukti sudah diotorisasi')
    return
  }

  console.log('buttonKoreksi' , nobukti)
  let _token = $("#_token").val();
  dataTableKoreksi = []

  let resRefresh = refreshDataTableKoreksi(nobukti)
  console.log('resRefresh' , resRefresh)

  if (resRefresh) {
    $('.showhideform').hide();
    $('#modalKoreksi').show();

    $('#page2').show();
    $('#page1').hide();
  }

}


function buttonKoreksifirstadd (nobukti , isoto) {
  console.log(' buttonKoreksi' , nobukti, isoto)
  let akses = $("#akses_iskoreksi").val();

  // if (!Number(akses)) {
  //   alertify.warning('No access')
  //   return
  // }

  // if (Number(isoto)) {
  //   alertify.warning('Nobukti sudah diotorisasi')
  //   return
  // }

  console.log('buttonKoreksi' , nobukti)
  let _token = $("#_token").val();
  dataTableKoreksi = []

  let resRefresh = refreshDataTableKoreksifirstadd(nobukti)
  console.log('resRefresh' , resRefresh)

  if (resRefresh) {
    $('.showhideform').hide();
    $('#modalKoreksi').show();

    $('#page2').show();
    $('#page1').hide();
  }

}



function buttonKoreksiDeletefirstadd (index, nobukti, urut) {
  let akses = $("#akses_ishapus").val();

  
  console.log('buttonKoreksiDelete' , index, nobukti, urut )


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"





        $.ajax({
          url: "{!! url('invoicepenjualanspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nobukti,
            urut

          },
          success: function(res) {
            console.log('res', res)
            loadAll()
            refreshDataTableKoreksifirstadd(nobukti , 'D')

            // lockFormAdd()
            // $('.showhide').hide();
            // refreshDataTableAdd(nobukti)

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



function refreshDataTableKoreksifirstadd (nobukti , tipeRefresh = '') {
  console.log('refreshDataTableKoreksi')
  let _token = $("#_token").val();

  let resRefresh = 0
  $.ajax({
    url: "{!! url('invoicepenjualanspdetailkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {

      console.log(res)
      if (tipeRefresh && !res.length) {
        alertify.warning("Data habis")
        // $("#form").modal('toggle')
        buttonCloseForm()
      }
      if (res.length) {

        dataTableKoreksi = res
        rowTable = ``

        dataTableKoreksi.forEach((item,i) => {
          dataTableKoreksi[i].NAMABRG = dataTableKoreksi[i].NAMABRG ? dataTableKoreksi[i].NAMABRG.replaceAll('"', "''") : ''
          dataTableKoreksi[i].NamabrgKom = dataTableKoreksi[i].NamabrgKom ? dataTableKoreksi[i].NamabrgKom.replaceAll('"', "''") : ''

        })

        dataTableKoreksi.forEach((item, i) => {
            rowTable += `
              <tr>
                <td>${item.KodeBrg}</td>
                <td>${item.NAMABRG}</td>
                <td>${item.NamabrgKom ? item.NamabrgKom : ''}</td>
                <td>${item.NoSPB ? item.NoSPB : ''}</td>
                <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                <td>${item.Satuan}</td>
                <td class="text-right">${item.HARGA  ? formatAngka(parseFloat(item.HARGA).toFixed(2))  : '0.00'}</td>
                <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2))  : '0.00'}</td>
                <td class="text-right">${item.SubTotal ? formatAngka(parseFloat(item.SubTotal).toFixed(2))  : '0.00'}</td>
                <td>${item.KetDetail ? item.KetDetail : ''}</td>
                <td class="text-center">
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDeletefirstadd(${i} ,'${item.NoBukti}' , '${item.Urut}'  )"><i class="bi bi-trash"></i></button>
                </td>
              </tr>

            `
        });
        document.getElementById("koreksiTableData").innerHTML = rowTable
        document.getElementById("input_koreksi_customer").value = dataTableKoreksi[0].NamaCustSupp

        document.getElementById("input_koreksi_alamat").value = dataTableKoreksi[0].Alamat
        document.getElementById("input_koreksi_alamatx").value = dataTableKoreksi[0].AlamatX
        document.getElementById("input_koreksi_catatan").value = dataTableKoreksi[0].FootNote
        document.getElementById("input_koreksi_nobukti").value = dataTableKoreksi[0].NoBukti
        document.getElementById("input_koreksi_tanggal").value = formatDate(dataTableKoreksi[0].Tanggal)
        document.getElementById("input_koreksi_valas").value = dataTableKoreksi[0].Valas
        document.getElementById("input_koreksi_kurs").value = dataTableKoreksi[0].Kurs
        document.getElementById("input_koreksi_tipeppn").value = dataTableKoreksi[0].PPN
        document.getElementById("input_koreksi_nopo").value = dataTableKoreksi[0].NoPesanan
        document.getElementById("input_koreksi_pembayaran").value = dataTableKoreksi[0].TIPEBAYAR
        document.getElementById("input_koreksi_hari").value = dataTableKoreksi[0].HARI
        document.getElementById("input_koreksi_sales").value = dataTableKoreksi[0].NamaSls
        document.getElementById("input_koreksi_uangmuka").value = parseFloat(dataTableKoreksi[0].nUangMuka).toFixed(2)

        document.getElementById("input_addx_disc").value = formatAngkaX(dataTableKoreksi[0].DISC)
        document.getElementById("input_addx_discrp").value = formatAngkaX(dataTableKoreksi[0].DiscRp)
        document.getElementById("input_addx_ppn").value = formatAngkaX(dataTableKoreksi[0].TotalPPn)
        document.getElementById("input_addx_dpp").value = formatAngkaX(dataTableKoreksi[0].TotalDPP)
        document.getElementById("input_addx_grandtotal").value = formatAngkaX(dataTableKoreksi[0].TotalNetto)



        resRefresh =  1;


      }



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
  return resRefresh

}





function buttonDetail (nobukti) {
  console.log('buttonDetail', nobukti)

  let _token = $("#_token").val()

  $('#detailTableData').html(`
    <tr>
      <td colspan="10" class="text-center">Loading...</td>
    </tr>
  `)

  $.ajax({
    url: "{!! url('invoicepenjualangetdetail') !!}",
    type: 'post',
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      console.log('detail res:', res)

      if (!res.length) {
        alertify.warning('Data tidak ditemukan')
        return
      }

      let header = res[0]

      $('#input_detail_customer').val(header.NamaCustSupp)
      $('#input_detail_alamat').val(header.Alamat)
      $('#input_detail_alamatx').val(header.AlamatX || '')

      $('#input_detail_nobukti').val(header.NoBukti)
      $('#input_detail_nopo').val(header.NoPesanan || '')

      $('#input_detail_pembayaran').val(header.TIPEBAYAR)
      $('#input_detail_hari').val(header.HARI)

      $('#input_detail_tanggal').val(formatDate(header.Tanggal, '-'))

      $('#input_detail_sales').val(header.NamaSls)

      $('#input_detail_valas').val(header.NamaVls)
      $('#input_detail_kurs').val(header.Kurs)

      $('#input_detail_uangmuka').val(formatAngka(header.nUangMuka || 0))

      $('#input_detail_catatan').val(header.FootNote || '')

      let html = ''

      res.forEach(item => {
        html += `
          <tr>
            <td>${item.KodeBrg}</td>
            <td>${item.NAMABRG}</td>
            <td>${item.NamabrgKom || ''}</td>
            <td>${item.NoSPB}</td>

            <td class="text-right">${formatAngka(item.Qnt)}</td>
            <td>${item.Satuan}</td>

            <td class="text-right">${formatAngka(item.HARGA || 0)}</td>
            <td class="text-right">${formatAngka(item.DISCTOT || 0)}</td>
            <td class="text-right">${formatAngka(item.Total || 0)}</td>

            <td>${item.KetDetail || ''}</td>
          </tr>
        `
      })

      $('#detailTableData').html(html)

      $('#input_detailx_disc').val(formatAngka(header.Diskon))
      $('#input_detailx_discrp').val(formatAngka(header.Diskon))
      $('#input_detailx_dpp').val(formatAngka(header.TotalDPP))
      $('#input_detailx_ppn').val(formatAngka(header.TotalPPn))
      $('#input_detailx_grandtotal').val(formatAngka(header.TotalNetto))

      $('.mainpage').hide()
      $('#page4').show()

    },
    error: function(err) {
      console.log(err)
      alertify.error('Gagal load detail')
    }
  })
}

function buttonAddDetail (nobukti) {
  console.log('buttonAddDetail', nobukti)

  let _token = $("#_token").val()
  let userid = $("#userid").val()

  $('#detailAddTableData').html(`
    <tr>
      <td colspan="7" class="text-center">Loading...</td>
    </tr>
  `)

  $.ajax({
    url: "{!! url('invoicepenjualangetdetailadd') !!}",
    type: 'post',
    data: {
      _token,
      nobukti,
      userid
    },
    success: function(res) {
      console.log('detail res:', res)

      if (!res.length) {
        alertify.warning('Data tidak ditemukan')
        return
      }

      let header = res[0]

      $('#input_add_detail_customer').val(header.NAMACUSTSUPP)
      $('#input_add_detail_alamat').val(header.Alamat)
      // $('#input_add_detail_alamatx').val(header.AlamatX || '')

      $('#input_add_detail_nobukti').val(header.NOBUKTI)
      $('#input_add_detail_nopo').val(header.NOso || '')

      $('#input_add_detail_tanggal').val(formatDate(header.TANGGAL, '-'))

      // $('#input_add_detail_sales').val(header.NamaSls)

      let html = ''

      res.forEach(item => {
        html += `
          <tr>
            <td>${item.KODEBRG}</td>
            <td>${item.NamaBrg}</td>
            <td>${item.NAMAPRODUK || ''}</td>
            <td>${item.Kodegdg || ''}</td>
            <td class="text-center">${item.Satuan}</td>
            <td class="text-right">${formatAngka(item.QNT)}</td>
            <td class="text-right">${formatAngka(item.QNTRSPB)}</td>

          </tr>
        `
      })

      $('#detailAddTableData').html(html)

      $('.mainpage').hide()
      $('#page5').show()

    },
    error: function(err) {
      console.log(err)
      alertify.error('Gagal load detail')
    }
  })
}

function buttonCloseFormDetail () {
  $('#page4').hide();
  $('#page1').show();
}

function buttonCloseFormAddDetail () {
  $('#page5').hide();
  $('#page1').show();
}


function loadAll () {
  console.log('loadAll')
          $.ajax({
            url: "{!! url('invoicepenjualanloadall') !!}",
            type: "get",
            async: false,
            data: {
            },
            success: function(res) {

              $('#tabel').DataTable().destroy();

              let rowTable = ''


              res.tempOutstanding.forEach((item, i) => {
                rowTable += `<tr>
                <td class="text-center">
                   <button class="btn btn-warning btn-sm"
                    type="button"
                    title="Details"
                    onclick="buttonAddDetail('${item.NoBukti}')">
                    <i class="bi bi-info"></i>
                   </button>
                   <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('${item.Noso }' , '${item.NamaCustSupp }' , '${item.KodeCustSupp }' , '${item.TglSO }' , ${item.PPNCUST })"><i class="bi bi-plus"></i></button>
                </td>
                  <td>${item.NoBukti}</td>
                  <td>${ formatDate(item.Tanggal , '/')}</td>
                  <td>${item.NamaCustSupp ? item.NamaCustSupp : ''}</td>
                  <td>${item.Noso}</td>
                  <td>${ formatDate(item.TglSO , '/')}</td>
                </tr>`
              });


              document.getElementById("tabel_data").innerHTML = rowTable



              $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]

        ]
        });

                $('#tabel2').DataTable().destroy();

                let rowTable2 = ''

                res.tempOutstanding2.forEach((item, i) => {

                  rowTable2 += `
                  <tr>
                  <td class='text-center' style="vertical-align:middle">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NoBukti }' , '${item.IsOtorisasi1}')"><i class="bi bi-pen"></i></button>`

                    if (Number(item.IsOtorisasi1)) {
                      rowTable2 += `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${ item.NoBukti }')"><i class="bi bi-key"></i></button>`

                    } else {
                      rowTable2 += `
                      <button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('${ item.NoBukti }')"><i class="bi bi-key"></i></button>`

                    }

                    rowTable2 +=  `<button class="btn btn-primary btn-sm" title="Print"         onclick="openPrintModal('${item.NoBukti}')">
                      <i class="bi bi-printer"></i>
                      </button>
                      <button class="btn btn-warning btn-sm"
                        type="button"
                        title="Details"
                        onclick="buttonDetail('${item.NoBukti}')">
                        <i class="bi bi-info"></i>
                      </button>
                      </td>
                      <td>${item.NoBukti }</td>
                      <td>${formatDate(item.Tanggal , '/')}</td>
                      <td>${item.NamaCustSupp }</td>
                      <td>${item.NoSPB }</td>
                      <td>${formatDate(item.TglSPB , '/')}</td>
                      <td>${item.NOSO }</td>
                      <td>${formatDate(item.TGLSO , '/')}</td>

                      <td>${item.NoPajak ? item.NoPajak : '' }</td>
                      <td>${item.NoPesanan ? item.NoPesanan : '' }</td>
                      <td class='text-right'>${formatAngka(parseFloat(item.totdpp).toFixed(2))}</td>
                      <td class='text-right'>${formatAngka(parseFloat(item.totppn).toFixed(2))}</td>
                      <td class='text-right'>${formatAngka(parseFloat(item.totnet).toFixed(2))}</td>

                      ${Number(item.Isbatal) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}

                      <td>${item.userBatal ? item.userBatal : '' }</td>
                      <td>${item.tglBatal ? formatDate(item.tglBatal , '/') : '' }</td>

                  </tr>`
                });


                document.getElementById("tabel2_data").innerHTML = rowTable2

                $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "order": [[1, 'asc']],
            "columnDefs": [
              {"targets" :[0] , 'orderable' : false},
            // { "type": "date", "targets": [3] },
            // {  "className": "text-right", "targets": [9,10,11,12] },
            // "columns" : [{"width" : "20px"}]

          ]
          });


          $('#tabel3').DataTable().destroy();


          let rowTable3 = ''

          res.tempOutstanding3.forEach((item, i) => {

            rowTable3 += `
            <tr>
            <td class='text-center' style="vertical-align:middle">`
              if (Number(item.IsOtorisasi1)) {
                rowTable3 += `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${ item.NoBukti }')"><i class="bi bi-key"></i></button>`

              } else {
                rowTable3 += `
                <button class="btn btn-primary btn-sm" type="button" onclick="submitOtorisasi('${ item.NoBukti }')"><i class="bi bi-key"></i></button>`

              }

            rowTable3 += `<button class="btn btn-primary btn-sm" title="Print" onclick="openPrintModal('${item.NoBukti}')">
                <i class="bi bi-printer"></i>
              </button>
              <button class="btn btn-warning btn-sm"
                  type="button"
                  title="Details"
                  onclick="buttonDetail('${item.NoBukti}')">
                  <i class="bi bi-info"></i>
                </button>
		            </td>

                <td>${item.NoBukti }</td>
                <td>${formatDate(item.Tanggal , '/')}</td>
                <td>${item.NamaCustSupp }</td>
                <td>${item.NoSPB }</td>
                <td>${formatDate(item.TglSPB , '/')}</td>
                <td>${item.NOSO }</td>
                <td>${formatDate(item.TGLSO , '/')}</td>

                <td>${item.NoPajak ? item.NoPajak : '' }</td>
                <td>${item.NoPesanan ? item.NoPesanan : '' }</td>

                 <td class='text-right'>${formatAngka(parseFloat(item.totdpp).toFixed(2))}</td>
                      <td class='text-right'>${formatAngka(parseFloat(item.totppn).toFixed(2))}</td>
                      <td class='text-right'>${formatAngka(parseFloat(item.totnet).toFixed(2))}</td>

                <td>${item.OtoUser1 ? item.OtoUser1 : '' }</td>
                <td>${item.TglOto1 ? formatDate(item.TglOto1 , '/') : '' }</td>

                ${Number(item.Isbatal) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}




                <td>${item.userBatal ? item.userBatal : '' }</td>
                <td>${item.tglBatal ? formatDate(item.tglBatal , '/') : '' }</td>

            </tr>
            `
          });






          document.getElementById("tabel3_data").innerHTML = rowTable3











          $("#tabel3").DataTable({
    "lengthChange": false,
      "paging": false ,
      "order": [[1, 'asc']],
      "columnDefs": [
        {"targets" :[0] , 'orderable' : false},
      // { "type": "date", "targets": [3] },
      // {  "className": "text-right", "targets": [9,10,11,12] },
      // "columns" : [{"width" : "20px"}]

    ]
    });






            },
            error: function (err) {
              console.log(err)
              console.log(err.status)
              console.log(err.statusText)
              alertify.warning('Terjadi kesalahan silahkan refresh browser')
            }

          })
}


function submitPrint3All () {
  console.log("submitPrint3All")
  // for (var i = 0; i < 30; i++) {
  //   dataPrint.push(dataPrint[0])
  // }
  let _token = $('#_token').val()

  if (!arrayListInvoice.length) {


    alertify.warning('Tidak ada invoice dipilih')
    return
  }
  $.ajax({
    url: "{!! url('invoicepenjualandetailcetakall') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      tempData: arrayListInvoice
    },

    success: function(res) {
      console.log("====res====")
      console.log(res)

      dataPrintx = res
      console.log(res[0])
      // console.log(res[0][0])

      // console.log(res[0][0].IsOtorisasi1)
    }
  })
  
  // $('#modalPrintAll').modal('toggle')

  let css = `<style type="text/css">
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
      height: 18px;
      padding: 0px 4px;
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
      width: 95%;
      bottom: 30px;
    }

     .solid{
      border-left: 0px red solid;
      height: 225px;
      width: 0px;
      display: inline-block;
      padding-left: 0px;
      }

    </style>`;
    let tempPrintStr = ``
    // tempPrintStr += `<html>
    // <head>
    //   <title></title>
    // </head>

    // <body onload="window.print()">
    //   ` + css

  console.log("Dataprintxlength" , dataPrintx.length)
  dataPrintx.forEach((item , i) => {
  dataPrint = item
  
  let arrayDataPrint = []
  for (let i = 0; i < dataPrint.length; i+=8) {
    let tempArray = dataPrint.slice(i,i+8)
    arrayDataPrint.push(tempArray)
  }

  let printContent = ''
  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  // let css = ''
  let hdr = ''
  let str= ''
  let ftr= ''
  let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
  let tanggalJthTempo = dataPrint[0].JthTmpo ? dataPrint[0].JthTmpo.split(' ')[0].split('-').reverse().join('/') : '-';

   tempPrintStr += `<html>
  <head>
    <title></title>
  </head>

  <body onload="window.print()">
    ` + css
      hdr = `<div style="display: flex; justify-content: space-between; width: 100%">
            <div class="pe-1" style="width: 60%">
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 15%; margin-top: 15px">
                  `+ imageContent +`
                </div>
                <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                  <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                  <p class="m-0" style="font-size: 10px;">
                    JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                    RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                  </p>
                </div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%; font-size: 10px; margin-top:15px;">Kepada YTH :</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%; font-size: 10px;">${dataPrint[0].NamaCustSupp ?? '-'}</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 100%; font-size: 10px;">${dataPrint[0].Alamat ?? '-'}</div>
              </div>
            </div>


            <div style="width: 40%; margin-left: 90px; margin-top: 15px;">
              <div style="display: flex; width: 100%">
                <h2 class="m-0 pb-2">INVOICE</h2>
              </div>
              <div style="display: flex; width: 100%; font-size: 12px;">
                <div class="pb-1" style="width: 30%">Nomor</div>
                <div class="pb-1" style="width: 5%">:</div>
                <div class="pb-1" style="width: 65%; font-weight: bold;">`+dataPrint[0].NoBukti+`</div>
              </div>
              <div style="display: flex; width: 100%; font-size: 12px;">
                <div class="pb-1" style="width: 30%">Tanggal</div>
                <div class="pb-1" style="width: 5%">:</div>
                <div class="pb-1" style="width: 65%">`+tanggalOnly+`</div>
              </div>
              <div style="display: flex; width: 100%; font-size: 12px;">
                <div class="pb-1" style="width: 30%">No PO</div>
                <div class="pb-1" style="width: 5%">:</div>
                <div class="pb-1" style="width: 65%">${dataPrint[0].PONO ?? '-'}</div>
              </div>
              <div style="display: flex; width: 100%; font-size: 12px;">
                <div class="pb-1" style="width: 30%">Pembayaran</div>
                <div class="pb-1" style="width: 5%">:</div>
                <div class="pb-1" style="width: 65%">${dataPrint[0].HARI ? dataPrint[0].HARI + ' HARI' : '-'}</div>
              </div>
              <div style="display: flex; width: 100%; font-size: 12px;">
                <div class="pb-1" style="width: 30%">Jatuh Tempo</div>
                <div class="pb-1" style="width: 5%">:</div>
                <div class="pb-1" style="width: 65%">`+tanggalJthTempo+`</div>
              </div>
              <div style="display: flex; width: 100%">
                <div class="pb-1" style="width: 0%"></div>
              </div>
            </div>
            <div
              style="
                height: 80px;
                overflow: hidden;">`+printContent+`
            </div>
          </div>

      <table style="width:95%; margin-top: 10px; margin-left:-5px; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
          <thead>
            </tr>
                <tr>
                  <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                  <td rowspan="2" class="text-center" style="width: 15%">NO SPB</td>
                  <td rowspan="2" class="text-center" style="width: 40%">NAMA BARANG</td>
                  <td rowspan="2" class="text-center" style="width: 5%">SAT</td>
                  <td rowspan="2" class="text-center" style="width: 10%">SAT TAX</td>
                  <td rowspan="2" class="text-center" style="width: 5%">QTY</td>
                  <td rowspan="2" class="text-center" style="width: 10%">HARGA</td>
                  <td rowspan="2" class="text-center" style="width: 5%">DISKON</td>
                  <td rowspan="2" class="text-center" style="width: 15%">JUMLAH</td>
                </tr>
              </thead> `;

  let z = 0
  let maxRow = 8;
  // let tempPrintStr = ``
  // buat hitung grandtotal
  let grandTotalJumlah = 0;
  
  dataPrint.forEach(item => {

    if (item.SubTotal) {
      grandTotalJumlah += Number(item.SubTotal) || 0;
    }

  });
  // end
  console.log("AAAAAAAAAAAA")
  console.log(arrayDataPrint)
  

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
      console.log(tempPrintStr)
      console.log("SINIIII")
      console.log(tempPrintStr)
      item.forEach((itemSub, j) => {
        tempPrintStr += ``

       tempPrintStr += `
       <tr>
       <td class="text-center"
             style="width: 1%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${z+1}</td>
       <td class="text-align: left"
             style="width: 15%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.nospb ?? ''}</td>
       <td class="text-align: left"
             style="width: 40%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.NamaBrg ?? ''}</td>
       <td class="text-center"
             style="width: 5%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.satuan ?? ''}</td>
       <td class="text-center"
             style="width: 10%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.SATTAX ?? ''}</td>
       <td class="text-right"
             style="width: 5%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.Qty ? parseFloat(itemSub.Qty).toFixed(2) : ''}</td>
       <td style="width: 10%; text-align: right; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">
          ${itemSub.Harga
            ? Number(itemSub.Harga).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })
            : ''}
        </td>
        <td class="text-right"
             style="width: 5%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.DISC ? parseFloat(itemSub.DISC).toFixed(2) + '%' : ''}</td>
        <td style="width: 15%; text-align: right; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">
          ${itemSub.SubTotal
            ? Number(itemSub.SubTotal).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })
            : ''}
        </td>
       </tr>`;

         z++;

      });

      // TAMBAHAN
      let sisaRow = maxRow - item.length;

      for (let k = 0; k < sisaRow; k++) {
        tempPrintStr += `
        <tr>
          <td style="border-top:none; border-bottom:none;">&nbsp;</td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
          <td style="border-top:none; border-bottom:none;"></td>
        </tr>`;
      }

      if (i != arrayDataPrint.length - 1) {
          tempPrintStr += `
          <tr>
              <td colspan="9"
                  style="border-top:none; border-left:none; border-right:none; border-bottom:1px solid black; padding:0; height:0;">
              </td>
          </tr>`;
        }

      // total berada di paling bawah
      console.log(i, arrayDataPrint.length)
      if(i == arrayDataPrint.length - 1){

      tempPrintStr += `
      <tr>
        <td colspan="3" style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; padding:5px; font-weight:bold; padding-right:20px;">
          TERBILANG : ${(item && item.length > 0) ? item[0].TerBIlang : '-'} Rupiah
        </td>
        <td colspan="3" style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; padding:5px; font-weight:bold;">
        </td>
        <td style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; text-align:left; font-weight:bold;">
          JUMLAH
        </td>
        <td style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; text-align:left; font-weight:bold;">
          IDR
        </td>
        <td style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; text-align:right; font-weight:bold;">
          ${grandTotalJumlah.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          })}
        </td>
      </tr>
      <!-- DISKON -->
      <tr>
        <td colspan="6" style="border:none;"></td>
        <td style="border:none; text-align:left; font-weight:bold;">
          DISKON
        </td>
        <td style="border:none; text-align:left; font-weight:bold;">
          IDR
        </td>
        <td style="border:none; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].POtongan || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
        </td>
      </tr>

      <!-- UANG MUKA -->
      <tr>
        <td colspan="6" style="border:none;"></td>
        <td style="border-bottom:1px solid black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
          U.MUKA
        </td>
        <td style="border-bottom:1px solid black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
          IDR
        </td>
        <td style="border-bottom:1px solid black; border-left:none; border-top:none; border-right:none; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].TotalUM || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
        </td>
      </tr>

      <!-- DPP -->
      <tr>
        <td colspan="6" style="border:none;"></td>
        <td style="border:none; text-align:left; font-weight:bold;">
          DPP
        </td>
        <td style="border:none; text-align:left; font-weight:bold;">
          IDR
        </td>
        <td style="border:none; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].NdppRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
        </td>
      </tr>
      <!-- PPN -->
      <tr>
        <td colspan="6" style="border:none;"></td>
        <td style="border:none; text-align:left; font-weight:bold;">
          PPN
        </td>
        <td style="border:none; text-align:left; font-weight:bold;">
          IDR
        </td>
        <td style="border:none; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].NPpnRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
        </td>
      </tr>
      <!-- TOTAL -->
      <tr>
        <td colspan="6" style="border:none; border-left:none; border-top:none; border-right:none;"></td>
        <td style="border-bottom:3px double black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
          TOTAL
        </td>
        <td style="border-bottom:3px double black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
          IDR
        </td>
        <td style="border-bottom:3px double black; border-left:none; border-top:none; border-right:none; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].NNetRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
        </td>
      </tr>`};
      // end

       tempPrintStr += `</tbody>`;

       tempPrintStr += `</table>`

       if (i == arrayDataPrint.length - 1) {
        tempPrintStr += `
       <div class="footer-sign font-family: sans-serif;
         font-size: 11px ">

       <div class="row mt-3" style="text-align: left;font-family: sans-serif;
       font-size: 11px ">
       <span style="float: left; display: block; clear: left;">
       </span>

       <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

        </div>

       </div>

       <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">

       <!-- KIRI -->
        <div style="width:40%; font-size:11px; margin-top:-100px;">
          <p class="m-0">TRANSFER : </p>
          <p class="m-0">CV. SINAR MAHAKAM LESTARI</p>
          <p class="m-0">PT. BANK DANAMON INDONESIA Tbk Cabang Banjarmasin</p>
          <p class="m-0">AC NO : 003646465454</p>
        </div>

        <!-- KANAN -->
        <div style="width:60%;  margin-top:-160px; margin-left: 20px;">
        <table
           class="detail-spb-table mb-2"
           style="width: 100%; font-family: sans-serif;
           font-size: 10px ">
           <tr>
             <td class="no-border text-center" style="width: 20%">Hormat Kami,</td>
             <td class="no-border text-center" style="width: 20%"></td>
           </tr>
           <tr style="height: 6rem">
             <td class="no-border">&nbsp;</td>
           </tr>

           <tr>
             <td class="no-border px-2">
             <p class="m-0" style="text-align: center;">ISKANDAR</p>
             </td>
             <td class="no-border px-2">
             </td>
           </tr>
         </table>
        </div>
      </div>

       </div>`};

       tempPrintStr += `
       <div class="footer-print-date">
         <table class="m-0" style="width: 100% ; font-family: sans-serif;
         font-size: 11px ">
           <tr>
             <td class="no-border"></td>
             <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
           </tr>
         </table>
       </div>`


      tempPrintStr += `</div>`
    });

  })
  tempPrintStr +=  `</body></html>`
  console.log(tempPrintStr)
  w=window.open(' ')
  w.document.write(tempPrintStr)

  w.print()
  w.close()

}

function submitPrintAll (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    console.log('submitPrintAll')


    let _token = $("#_token").val();
    let tempData = []
    // for (let i = 0; i < dataChecklistInvoice.length; i++) {
    //
    //   if (document.getElementById(`invoiceChecklist${i}`).checked) {
    //
    //     // row_data[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
    //     tempData.push(dataChecklistInvoice[i].NoBukti)
    //   }
    // }
    console.log(tempData)

    if (!arrayListInvoice.length) {


      alertify.warning('Tidak ada invoice dipilih')
      return
    }

    $.ajax({
      url: "{!! url('invoicepenjualandetailcetakall') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        tempData: arrayListInvoice
      },
      success: function(res) {
        console.log(res)

        dataPrintx = res
        console.log(res[0])
        console.log(res[0][0])

        // console.log(res[0][0].IsOtorisasi1)
      }
    })
    // return
    
  // $('#modalPrintAll').modal('toggle')
    let css = `<style type="text/css">
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
        height: 18px;
        padding: 0px 4px;
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
        height: 14cm;
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
    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

    dataPrintx.forEach((item, i) => {
      console.log("-----------------------")
      console.log(item)
      dataPrint = item
      let arrayDataPrint = []
      for (let i = 0; i < dataPrint.length; i+=8) {
        let tempArray = dataPrint.slice(i,i+8)
        arrayDataPrint.push(tempArray)
      }

      let printContent = ''
      let imageContent = document.getElementById(`imagecontainer`).innerHTML;
      // let css = ''
      let hdr = ''
      let str= ''
      let ftr= ''
      let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];


          hdr = `<div style="display: flex; justify-content: space-between; width: 100%">
                <div class="pe-1" style="width: 60%">
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 15%; margin-top: 15px">
                      `+ imageContent +`
                    </div>
                    <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                      <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                      <p class="m-0">
                        JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                        RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                      </p>
                    </div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 100%">Kepada YTH : ${dataPrint[0].NamaCustSupp ?? '-'}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 100%">${dataPrint[0].Alamat ?? '-'}</div>
                  </div>
                </div>


                <div style="width: 40%; margin-left: 30px; margin-top: 15px;">
                  <div style="display: flex; width: 100%">
                    <h2 class="m-0 pb-2">INVOICE</h2>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 45%">Nomor</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 50%">`+dataPrint[0].NoBukti+`</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 45%">Tanggal</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 45%">No PO</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 50%">${dataPrint[0].PONO ?? '-'}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 45%">Pembayaran</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 50%">${dataPrint[0].HARI ?? '-'}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 45%">Jatuh Tempo</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 50%">${dataPrint[0].JthTempo ?? '-'}</div>
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

          <table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
              <thead>
                </tr>
                    <tr>
                      <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                      <td rowspan="2" class="text-center" style="width: 15%">NO SPB</td>
                      <td rowspan="2" class="text-center" style="width: 40%">NAMA BARANG</td>
                      <td rowspan="2" class="text-center" style="width: 5%">SAT</td>
                      <td rowspan="2" class="text-center" style="width: 10%">QTY</td>
                      <td rowspan="2" class="text-center" style="width: 10%">HARGA</td>
                      <td rowspan="2" class="text-center" style="width: 5%">DISKON</td>
                      <td rowspan="2" class="text-center" style="width: 15%">JUMLAH</td>
                    </tr>
                  </thead> `;

      let z = 0
      let maxRow = 8;

      // buat hitung grandtotal
      let grandTotalJumlah = 0;

      dataPrint.forEach(item => {

        if (item.SubTotal) {
          grandTotalJumlah += Number(item.SubTotal) || 0;
        }

      });
      // end

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
          item.forEach((itemSub, j) => {
            tempPrintStr += ``



           tempPrintStr += `
           <tr>
           <td class="text-align: center"
                 style="width: 1%; ">${z+1}</td>
           <td class="text-align: left"
                 style="width: 15%;  ">${itemSub.nospb ?? ''}</td>
           <td class="text-align: left"
                 style="width: 40%;  ">${itemSub.NamaBrg ?? ''}</td>
           <td class="text-center"
                 style="width: 5%;  ">${itemSub.satuan ?? ''}</td>
           <td class="text-right"
                 style="width: 10%;  ">${itemSub.Qty ? parseFloat(itemSub.Qty).toFixed(2) : ''}</td>
           <td style="width: 10%; text-align: right;">
              ${itemSub.Harga
                ? Number(itemSub.Harga).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  })
                : ''}
            </td>
            <td class="text-right"
                 style="width: 5%;  ">${itemSub.DISC ? parseFloat(itemSub.DISC).toFixed(2) + '%' : ''}</td>
            <td style="width: 15%; text-align: right;">
              ${itemSub.SubTotal
                ? Number(itemSub.SubTotal).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  })
                : ''}
            </td>
           </tr>`;

             z++;

          });

          // TAMBAHAN
          let sisaRow = maxRow - item.length;

          for (let k = 0; k < sisaRow; k++) {
            tempPrintStr += `
            <tr>
              <td style="border-top:none; border-bottom:none;">&nbsp;</td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none;"></td>
              <td style="border-top:none; border-bottom:none; border-right:none;"></td>
            </tr>`;
          }

  	// total berada di paling bawah
          console.log(i, arrayDataPrint.length)
          if(i == arrayDataPrint.length - 1){

          tempPrintStr += `
          <tr>
            <td colspan="5" style="border:1px solid; padding:5px; font-weight:bold;">
              TERBILANG : ${(item && item.length > 0) ? item[0].TerBIlang : '-'} Rupiah
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold;">
              Jumlah
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold;">
              IDR
            </td>
            <td style="border:1px solid; text-align:right; font-weight:bold;">
              ${grandTotalJumlah.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              })}
            </td>
          </tr>`};
  	//end

           tempPrintStr += `</tbody>`;

           tempPrintStr += `</table>

           <div class="footer-sign font-family: sans-serif;
             font-size: 10px ">

           <div class="row mt-3" style="text-align: left;font-family: sans-serif;
           font-size: 12px ">
           <span style="float: left; display: block; clear: left;">
           </span>


           <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

            </div>

           </div>


           <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">

            <!-- KIRI -->
            <div style="width:50%; font-size:10px; margin-top: 50px;">
              <p class="m-0">TRANSFER : </p>
              <p class="m-0">CV. SINAR MAHAKAM LESTARI
                PT. BANK DANAMON INDONESIA Tbk. Cabang Banjarmasin
                AC NO : 003-646-465-454 (IDR)</p>
              <p class="m-0"></p>
            </div>


            <!-- KANAN -->
            <div style="width:50%;">
            <table
               class="detail-spb-table mb-2"
               style="width: 100%; margin-top: 20px; font-family: sans-serif;
               font-size: 10px ">
               <tr>
                 <td class="no-border text-center" style="width: 20%">Hormat Kami,</td>
                 <td class="no-border text-center" style="width: 20%"></td>
               </tr>
               <tr style="height: 2.5rem">
                 <td class="no-border">&nbsp;</td>
               </tr>

               <tr>
                 <td class="no-border px-2">
  		           <p class="m-0" style="text-align: center;">NOOR AIRINI</p>
                 </td>
                 <td class="no-border px-2">
                 </td>
               </tr>
             </table>
            </div>
          </div>

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


        


        // cek sini

    });
    tempPrintStr +=  `</body></html>`
    w=window.open(' ')
    w.document.write(tempPrintStr)

    w.print()
    w.close()


  }


  function submitPrintJBGAll (nobukti) {
    console.log('submitPrintJBGAll')
  let _token = $('#_token').val()

  if (!arrayListInvoice.length) {


      alertify.warning('Tidak ada invoice dipilih')
      return
    }
  $.ajax({
    url: "{!! url('invoicepenjualandetailcetakall') !!}",
    type: "post",
    async: false,
    data: {
      _token: _token,
      tempData: arrayListInvoice
    },
    success: function(res) {
      console.log(res)
      dataPrintx = res
      console.log(res[0])
      console.log(res[0][0])
    }
  })
  let tempPrintStr = ''
  
  // $('#modalPrintAll').modal('toggle')
  let css = `<style type="text/css">
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

    .no-border { border: none; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .m-0 { margin: 0; }
    .pb-1 { padding-bottom: 0.25rem; }
    .pb-2 { padding-bottom: 0.5rem; }
    .ps-3 { padding-left: 1rem; }
    .pe-1 { padding-right: 0.25rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mt-3 { margin-top: 1rem; }

    .body-main-prints {
      width: 21cm;
      height: 29.7cm;
      position: relative;
    }

    .footer-sign {
      padding-top: 5px;
      position: absolute;
      width: 100%;
      bottom: 30px;
    }

    .footer-print-date {
      position: absolute;
      width: 95%;
      bottom: 0px;
    }

    .detail-spb-table { margin: 0; }
  </style>`;
  tempPrintStr += `<html>
  <head><title></title></head>
  <body onload="window.print()">
  ` + css

  dataPrintx.forEach((item, i) => {

  dataPrint = item
  let arrayDataPrint = []
  for (let i = 0; i < dataPrint.length; i += 20) {
    let tempArray = dataPrint.slice(i, i + 20)
    arrayDataPrint.push(tempArray)
  }

  let printContent = ''
  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  // let css = ''
  let hdr = ''
  let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
  let tanggalJthTempo = dataPrint[0].JthTmpo ? dataPrint[0].JthTmpo.split(' ')[0].split('-').reverse().join('/') : '-';

  

  hdr = `<div style="display: flex; width: 100%">
    <div class="pe-1" style="width: 60%">
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 15%; margin-top: 15px">
          ` + imageContent + `
        </div>
        <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
          <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
          <p class="m-0" style="font-size: 10px; width:90%;">JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18 RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR</p>
        </div>
      </div>
      <div style="display: flex; width: 95%">
        <div class="pb-1" style="width: 95%; font-size: 10px; margin-top:15px;">Kepada YTH :</div>
      </div>
      <div style="display: flex; width: 95%">
        <div class="pb-1" style="width: 95%; font-size: 10px;">${dataPrint[0].NamaCustSupp ?? '-'}</div>
      </div>
      <div style="display: flex; width: 95%">
        <div class="pb-1" style="width: 95%; font-size: 10px;">${dataPrint[0].Alamat ?? '-'}</div>
      </div>
    </div>

    <div style="width: 40%; margin-top:10px;">
      <div style="display: flex; width: 100%">
        <h2 class="m-0 pb-2">INVOICE</h2>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Nomor</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%; font-weight: bold;">` + dataPrint[0].NoBukti + `</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Tanggal</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">` + tanggalOnly + `</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">No PO</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">${dataPrint[0].PONO ?? '-'}</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Pembayaran</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">${dataPrint[0].HARI ? dataPrint[0].HARI + ' HARI' : '-'}
        </div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Jatuh Tempo</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">`+tanggalJthTempo+`</div>
      </div>
    </div>

    <div style="height: 80px; overflow: hidden;">${printContent}</div>
  </div>

  <table style="width:95%; margin-left:-5px; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
    <thead>
      <tr>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 1%">No.</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 15%">NO SPB</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 40%">NAMA BARANG</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 5%">SAT</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 10%">SAT TAX</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 5%">QTY</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 10%">HARGA</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 5%">DISKON</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 15%">JUMLAH</td>
      </tr>
    </thead>`;

  let z = 0
  let maxRow = 31;
  // let tempPrintStr = ``
  let grandTotalJumlah = 0;

  dataPrint.forEach(item => {
    if (item.SubTotal) {
      grandTotalJumlah += Number(item.SubTotal) || 0;
    }
  });

  arrayDataPrint.forEach((item, i) => {
    console.log('arrayDataPrint', i)

    if (i == 0) {
      tempPrintStr += `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
    } else {
      tempPrintStr += `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:7px; page-break-before: always">`
    }

    tempPrintStr += hdr

    item.forEach((itemSub, j) => {
      tempPrintStr += `
      <tr>
        <td class="text-center no-border" style="width: 1%;">${z + 1}</td>
        <td class='no-border' style="width: 15%;">${itemSub.nospb ?? ''}</td>
        <td class='no-border' style="width: 40%;">${itemSub.NamaBrg ?? ''}</td>
        <td class="text-center no-border" style="width: 5%;">${itemSub.satuan ?? ''}</td>
        <td class="text-center no-border" style="width: 10%;">${itemSub.SATTAX ?? ''}</td>
        <td class="text-right no-border" style="width: 5%;">${itemSub.Qty ? parseFloat(itemSub.Qty).toFixed(2) : ''}</td>
        <td class='no-border' style="width: 10%; text-align: right;">
          ${itemSub.Harga ? Number(itemSub.Harga).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : ''}
        </td>
        <td class="text-right no-border" style="width: 5%;">${itemSub.DISC ? parseFloat(itemSub.DISC).toFixed(2) + '%' : ''}</td>
        <td class='no-border' style="width: 15%; text-align: right;">
          ${itemSub.SubTotal ? Number(itemSub.SubTotal).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : ''}
        </td>
      </tr>`;
      z++;
    });

    // Empty rows to fill the page
    let sisaRow = maxRow - item.length;
    for (let k = 0; k < sisaRow; k++) {
      tempPrintStr += `
      <tr>
        <td class='no-border' style="border-top:none; border-bottom:none;">&nbsp;</td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
      </tr>`;
    }

    // Totals only on last page
    if (i == arrayDataPrint.length - 1) {
      tempPrintStr += `
      <tr>
        <td class='no-border' colspan="3" style="border-top:1px solid; padding:5px; font-weight:bold;">
          TERBILANG : ${(item && item.length > 0) ? item[0].TerBIlang : '-'} Rupiah
        </td>
        <td class='no-border' colspan="3" style="border-top:1px solid; padding:5px; font-weight:bold;">
        </td>
        <td class='no-border' style="border-top:1px solid; text-align:left; font-weight:bold;">JUMLAH</td>
        <td class='no-border' style="border-top:1px solid; text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style="border-top:1px solid; text-align:right; font-weight:bold;">
          ${grandTotalJumlah.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </td>
      </tr>
      <!-- DISKON -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">DISKON</td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].POtongan || 0).toLocaleString('id-ID', { minimumFractionDigits: 2 })}
        </td>
      </tr>
      <!-- UANG MUKA -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style="border-bottom:1px solid; text-align:left; font-weight:bold;">U.MUKA</td>
        <td class='no-border' style="border-bottom:1px solid; text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style="border-bottom:1px solid; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].TotalUM || 0).toLocaleString('id-ID', { minimumFractionDigits: 2 })}
        </td>
      </tr>
      <!-- DPP -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">DPP</td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].NdppRp || 0).toLocaleString('id-ID', { minimumFractionDigits: 2 })}
        </td>
      </tr>
      <!-- PPN -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">PPN</td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].NPpnRp || 0).toLocaleString('id-ID', { minimumFractionDigits: 2 })}
        </td>
      </tr>
      <!-- TOTAL -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" border-bottom:1px solid; text-align:left; font-weight:bold;">TOTAL</td>
        <td class='no-border' style=" border-bottom:1px solid; text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" border-bottom:1px solid; text-align:right; font-weight:bold;">
          ${Number(dataPrint[0].NNetRp || 0).toLocaleString('id-ID', { minimumFractionDigits: 2 })}
        </td>
      </tr>`;
    }

    tempPrintStr += `</tbody></table>

    <div class="footer-sign" style="font-family: sans-serif; font-size: 10px;">
      <div style="display:flex; justify-content:space-between; width:100%;">

        <!-- KIRI -->
        <div style="width:40%; font-size:10px; margin-top: -20px;">
          <p class="m-0">TRANSFER : </p>
          <p class="m-0">CV. SINAR MAHAKAM LESTARI </p>
          <p class="m-0">PT. BANK DANAMON INDONESIA Tbk</p>
          <p class="m-0">Cabang Banjarmasin</p>
          <p class="m-0">AC NO : 003646465454</p>
        </div>

        <!-- KANAN -->
        <div style="width:60%; margin-top:-90px; margin-left: 20px;">
          <table class="detail-spb-table mb-2"
            style="width: 100%; margin-top: 20px; font-family: sans-serif; font-size: 10px;">
            <tr>
              <td class="no-border text-center" style="width: 20%">Hormat Kami,</td>
              <td class="no-border text-center" style="width: 20%"></td>
            </tr>
            <tr style="height: 6rem">
              <td class="no-border">&nbsp;</td>
            </tr>
            <tr>
              <td class="no-border px-2">
                <p class="m-0" style="text-align: center;">ISKANDAR</p>
              </td>
              <td class="no-border px-2"></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="footer-print-date">
      <table class="m-0" style="width: 100%; font-family: sans-serif; font-size: 10px;">
        <tr>
          <td class="no-border"></td>
          <td class="no-border text-right">Page ${i + 1} of ${arrayDataPrint.length}</td>
        </tr>
      </table>
    </div>`;

    tempPrintStr += `</div>`
  });

  

  
  } )
  tempPrintStr += `</body></html>`
  w = window.open(' ')
  w.document.write(tempPrintStr)
  w.print()
  w.close()
  
}

  function submitPrintJBG (nobukti) {
  let _token = $('#_token').val()
  $.ajax({
    url: "{!! url('invoicepenjualandetailCetakJBG') !!}",
    type: "post",
    async: false,
    data: {
      _token: _token,
      NOBUKTI: nobukti
    },
    success: function(res) {
      console.log(res)
      dataPrint = res
      console.log(res[0])
      console.log(res[0][0])
    }
  })
 
  let arrayDataPrint = []
  for (let i = 0; i < dataPrint.length; i += 20) {
    let tempArray = dataPrint.slice(i, i + 20)
    arrayDataPrint.push(tempArray)
  }

  let printContent = ''
  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  let css = ''
  let hdr = ''
  let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
  let tanggalJthTempo = dataPrint[0].JthTmpo ? dataPrint[0].JthTmpo.split(' ')[0].split('-').reverse().join('/') : '-';

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

    .no-border { border: none; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .m-0 { margin: 0; }
    .pb-1 { padding-bottom: 0.25rem; }
    .pb-2 { padding-bottom: 0.5rem; }
    .ps-3 { padding-left: 1rem; }
    .pe-1 { padding-right: 0.25rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mt-3 { margin-top: 1rem; }

    .body-main-prints {
      width: 21cm;
      height: 29.7cm;
      position: relative;
    }

    .footer-sign {
      padding-top: 5px;
      position: absolute;
      width: 100%;
      bottom: 30px;
    }

    .footer-print-date {
      position: absolute;
      width: 95%;
      bottom: 0px;
    }

    .detail-spb-table { margin: 0; }
  </style>`;

  hdr = `<div style="display: flex; width: 100%">
    <div class="pe-1" style="width: 60%">
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 15%; margin-top: 15px">
          ` + imageContent + `
        </div>
        <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
          <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
          <p class="m-0" style="font-size: 11px; width:90%;">JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18 RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR</p>
        </div>
      </div>
      <div style="display: flex; width: 95%">
        <div class="pb-1" style="width: 95%; font-size: 11px; margin-top:15px;">Kepada YTH :</div>
      </div>
      <div style="display: flex; width: 95%">
        <div class="pb-1" style="width: 95%; font-size: 11px;">${dataPrint[0].NamaCustSupp ?? '-'}</div>
      </div>
      <div style="display: flex; width: 95%">
        <div class="pb-1" style="width: 95%; font-size: 11px;">${dataPrint[0].Alamat ?? '-'}</div>
      </div>
    </div>

    <div style="width: 40%; margin-top:10px; margin-left:-40px;">
      <div style="display: flex; width: 100%">
        <h2 class="m-0 pb-2">INVOICE</h2>
      </div>
      <div style="display: flex; width: 100%;">
        <div class="pb-1" style="width: 25%">Nomor</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%; font-weight: bold;">` + dataPrint[0].NoBukti + `</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Tanggal</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">` + tanggalOnly + `</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">No PO</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">${dataPrint[0].PONO ?? '-'}</div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Pembayaran</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">${dataPrint[0].HARI ? dataPrint[0].HARI + ' HARI' : '-'}
        </div>
      </div>
      <div style="display: flex; width: 100%">
        <div class="pb-1" style="width: 25%">Jatuh Tempo</div>
        <div class="pb-1" style="width: 5%">:</div>
        <div class="pb-1" style="width: 65%">`+tanggalJthTempo+`</div>
      </div>
    </div>

    <div style="height: 80px; overflow: hidden;">${printContent}</div>
  </div>

  <table style="width:95%; margin-left:-5px; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
    <thead>
      <tr>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 1%">No.</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 15%">NO SPB</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 40%">NAMA BARANG</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 5%">SAT</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 10%">SAT TAX</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 5%">QTY</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 10%">HARGA</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 5%">DISKON</td>
        <td style='border-left:none; border-right:none;' rowspan="2" class="text-center" style="width: 15%">JUMLAH</td>
      </tr>
    </thead>`;

  let z = 0
  let maxRow = 31;
  let tempPrintStr = ``
  let grandTotalJumlah = 0;

  dataPrint.forEach(item => {
    if (item.SubTotal) {
      grandTotalJumlah += Number(item.SubTotal) || 0;
    }
  });

  tempPrintStr += `<html>
  <head><title></title></head>
  <body onload="window.print()">
  ` + css

  arrayDataPrint.forEach((item, i) => {
    console.log('arrayDataPrint', i)

    if (i == 0) {
      tempPrintStr += `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
    } else {
      tempPrintStr += `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:7px; page-break-before: always">`
    }

    tempPrintStr += hdr

    item.forEach((itemSub, j) => {
      tempPrintStr += `
      <tr>
        <td class="text-center no-border" style="width: 1%;">${z + 1}</td>
        <td class='no-border' style="width: 15%;">${itemSub.nospb ?? ''}</td>
        <td class='no-border' style="width: 40%;">${itemSub.NamaBrg ?? ''}</td>
        <td class="text-center no-border" style="width: 5%;">${itemSub.satuan ?? ''}</td>
        <td class="text-center no-border" style="width: 10%;">${itemSub.SATTAX ?? ''}</td>
        <td class="text-right no-border" style="width: 5%;">${itemSub.Qty ? parseFloat(itemSub.Qty).toFixed(2) : ''}</td>
        <td class='no-border' style="width: 10%; text-align: right;">
          ${itemSub.Harga ? Number(itemSub.Harga).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : ''}
        </td>
        <td class="text-right no-border" style="width: 5%;">${itemSub.DISC ? parseFloat(itemSub.DISC).toFixed(2) + '%' : ''}</td>
        <td class='no-border' style="width: 15%; text-align: right;">
          ${itemSub.SubTotal ? Number(itemSub.SubTotal).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : ''}
        </td>
      </tr>`;
      z++;
    });

    // Empty rows to fill the page
    let sisaRow = maxRow - item.length;
    for (let k = 0; k < sisaRow; k++) {
      tempPrintStr += `
      <tr>
        <td class='no-border' style="border-top:none; border-bottom:none;">&nbsp;</td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
        <td class='no-border' style="border-top:none; border-bottom:none;"></td>
      </tr>`;
    }

    // Totals only on last page
    if (i == arrayDataPrint.length - 1) {
      tempPrintStr += `
      <tr>
        <td class='no-border' colspan="3" style="border-top:1px solid; padding:5px; font-weight:bold;">
          TERBILANG : ${(item && item.length > 0) ? item[0].TerBIlang : '-'} Rupiah
        </td>
        <td class='no-border' colspan="3" style="border-top:1px solid; padding:5px; font-weight:bold;">
        </td>
        <td class='no-border' style="border-top:1px solid; text-align:left; font-weight:bold;">JUMLAH</td>
        <td class='no-border' style="border-top:1px solid; text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style="border-top:1px solid; text-align:right; font-weight:bold;">
          ${Math.round(grandTotalJumlah).toLocaleString('id-ID')}
        </td>
      </tr>
      <!-- DISKON -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">DISKON</td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" text-align:right; font-weight:bold;">
          ${Math.round(Number(dataPrint[0].POtongan || 0)).toLocaleString('id-ID')}
        </td>
      </tr>
      <!-- UANG MUKA -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style="border-bottom:1px solid; text-align:left; font-weight:bold;">U.MUKA</td>
        <td class='no-border' style="border-bottom:1px solid; text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style="border-bottom:1px solid; text-align:right; font-weight:bold;">
          ${Math.round(Number(dataPrint[0].TotalUM || 0)).toLocaleString('id-ID')}
        </td>
      </tr>
      <!-- DPP -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">DPP</td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" text-align:right; font-weight:bold;">
          ${Math.round(Number(dataPrint[0].NdppRp || 0)).toLocaleString('id-ID')}
        </td>
      </tr>
      <!-- PPN -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">PPN</td>
        <td class='no-border' style=" text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" text-align:right; font-weight:bold;">
          ${Math.round(Number(dataPrint[0].NPpnRp || 0)).toLocaleString('id-ID')}
        </td>
      </tr>
      <!-- TOTAL -->
      <tr>
        <td class='no-border' colspan="6" style="border:none;"></td>
        <td class='no-border' style=" border-bottom:1px solid; text-align:left; font-weight:bold;">TOTAL</td>
        <td class='no-border' style=" border-bottom:1px solid; text-align:left; font-weight:bold;">IDR</td>
        <td class='no-border' style=" border-bottom:1px solid; text-align:right; font-weight:bold;">
          ${Math.round(Number(dataPrint[0].NNetRp || 0)).toLocaleString('id-ID')}
        </td>
      </tr>`;
    }

    tempPrintStr += `</tbody></table>

    <div class="footer-sign" style="font-family: sans-serif; font-size: 11px;">
      <div style="display:flex; justify-content:space-between; width:100%;">

        <!-- KIRI -->
        <div style="width:40%; font-size:11px; margin-top: -20px;">
          <p class="m-0">TRANSFER : </p>
          <p class="m-0">CV. SINAR MAHAKAM LESTARI </p>
          <p class="m-0">PT. BANK DANAMON INDONESIA Tbk</p>
          <p class="m-0">Cabang Banjarmasin</p>
          <p class="m-0">AC NO : 003646465454</p>
        </div>

        <!-- KANAN -->
        <div style="width:60%; margin-top:-90px; margin-left: 40px;">
          <table class="detail-spb-table mb-2"
            style="width: 100%; margin-top: 20px; font-family: sans-serif; font-size: 11px;">
            <tr>
              <td class="no-border text-center" style="width: 20%">Hormat Kami,</td>
              <td class="no-border text-center" style="width: 20%"></td>
            </tr>
            <tr style="height: 6rem">
              <td class="no-border">&nbsp;</td>
            </tr>
            <tr>
              <td class="no-border px-2">
                <p class="m-0" style="text-align: center;">ISKANDAR</p>
              </td>
              <td class="no-border px-2"></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <div class="footer-print-date">
      <table class="m-0" style="width: 100%; font-family: sans-serif; font-size: 11px;">
        <tr>
          <td class="no-border"></td>
          <td class="no-border text-right">Page ${i + 1} of ${arrayDataPrint.length}</td>
        </tr>
      </table>
    </div>`;

    tempPrintStr += `</div>`
  });

  tempPrintStr += `</body></html>`

  w = window.open(' ')
  w.document.write(tempPrintStr)
  w.print()
  w.close()
}


  function submitPrint3 (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('invoicepenjualandetailCetak3') !!}",
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
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
    let tanggalJthTempo = dataPrint[0].JthTmpo ? dataPrint[0].JthTmpo.split(' ')[0].split('-').reverse().join('/') : '-';


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
        height: 18px;
        padding: 0px 4px;
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
        width: 95%;
        bottom: 10px;
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
                  <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                    <p class="m-0" style="font-size: 11px;">
                      JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                      RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                    </p>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%; font-size: 11px; margin-top:15px;">Kepada YTH :</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%; font-size: 11px;">${dataPrint[0].NamaCustSupp ?? '-'}</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%; font-size: 11px;">${dataPrint[0].Alamat ?? '-'}</div>
                </div>
              </div>

              <div style="width: 40%; margin-left: 90px; margin-top: 15px;">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">INVOICE</h2>
                </div>
                <div style="display: flex; width: 100%; font-size: 12px;">
                  <div class="pb-1" style="width: 30%">Nomor</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 65%; font-weight: bold;">`+dataPrint[0].NoBukti+`</div>
                </div>
                <div style="display: flex; width: 100%; font-size: 12px;">
                  <div class="pb-1" style="width: 30%">Tanggal</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 65%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%; font-size: 12px;">
                  <div class="pb-1" style="width: 30%">No PO</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 65%">${dataPrint[0].PONO ?? '-'}</div>
                </div>
                <div style="display: flex; width: 100%; font-size: 12px;">
                  <div class="pb-1" style="width: 30%">Pembayaran</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 65%">${dataPrint[0].HARI ? dataPrint[0].HARI + ' HARI' : '-'}</div>
                </div>
                <div style="display: flex; width: 100%; font-size: 12px;">
                  <div class="pb-1" style="width: 30%">Jatuh Tempo</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 65%">`+tanggalJthTempo+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>
              <div
                style="
                  height: 80px;
                  overflow: hidden;">`+printContent+`
              </div>
            </div>

        <table style="width:95%; margin-top: 10px; margin-left:-5px; border-collapse:collapse; font-family:sans-serif; font-size:11px;">
            <thead>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 15%">NO SPB</td>
                    <td rowspan="2" class="text-center" style="width: 40%">NAMA BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 5%">SAT</td>
                    <td rowspan="2" class="text-center" style="width: 10%">SAT TAX</td>
                    <td rowspan="2" class="text-center" style="width: 5%">QTY</td>
                    <td rowspan="2" class="text-center" style="width: 10%">HARGA</td>
                    <td rowspan="2" class="text-center" style="width: 5%">DISKON</td>
                    <td rowspan="2" class="text-center" style="width: 15%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.SubTotal) {
        grandTotalJumlah += Number(item.SubTotal) || 0;
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
        item.forEach((itemSub, j) => {
          tempPrintStr += ``

         tempPrintStr += `
         <tr>
         <td class="text-center"
               style="width: 1%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${z+1}</td>
         <td class="text-align: left"
               style="width: 15%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.nospb ?? ''}</td>
         <td class="text-align: left"
               style="width: 40%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.NamaBrg ?? ''}</td>
         <td class="text-center"
               style="width: 5%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.satuan ?? ''}</td>
         <td class="text-center"
               style="width: 10%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.SATTAX ?? ''}</td>
         <td class="text-right"
               style="width: 5%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.Qty ? parseFloat(itemSub.Qty).toFixed(2) : ''}</td>
         <td style="width: 10%; text-align: right; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">
            ${itemSub.Harga
              ? Number(itemSub.Harga).toLocaleString('id-ID', {
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0
                })
              : ''}
          </td>
          <td class="text-right"
               style="width: 5%; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">${itemSub.DISC ? parseFloat(itemSub.DISC).toFixed(2) + '%' : ''}</td>
          <td style="width: 15%; text-align: right; border-bottom: none; border-top: none; vertical-align: top; padding: 3px 4px;">
            ${itemSub.SubTotal
              ? Number(itemSub.SubTotal).toLocaleString('id-ID', {
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0
                })
              : ''}
          </td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
          </tr>`;
        }

        if (i != arrayDataPrint.length - 1) {
          tempPrintStr += `
          <tr>
              <td colspan="9"
                  style="border-top:none; border-left:none; border-right:none; border-bottom:1px solid black; padding:0; height:0;">
              </td>
          </tr>`;
        }

        // total berada di paling bawah
        console.log(i, arrayDataPrint.length)
        if(i == arrayDataPrint.length - 1){

        tempPrintStr += `
        <tr>
          <td colspan="3" style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; padding:5px; font-weight:bold; padding-right:20px;">
            TERBILANG : ${(item && item.length > 0) ? item[0].TerBIlang : '-'} Rupiah
          </td>
          <td colspan="3" style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; padding:5px; font-weight:bold;">
          </td>
          <td style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; text-align:left; font-weight:bold;">
            JUMLAH
          </td>
          <td style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; text-align:left; font-weight:bold;">
            IDR
          </td>
          <td style="border-top:1px solid black; border-left:none; border-bottom:none; border-right:none; text-align:right; font-weight:bold;">
            ${Math.round(grandTotalJumlah).toLocaleString('id-ID')}
          </td>
        </tr>
        <!-- DISKON -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:none; text-align:left; font-weight:bold;">
            DISKON
          </td>
          <td style="border:none; text-align:left; font-weight:bold;">
            IDR
          </td>
          <td style="border:none; text-align:right; font-weight:bold;">
            ${Math.round(Number(dataPrint[0].POtongan || 0)).toLocaleString('id-ID')}
          </td>
        </tr>

        <!-- UANG MUKA -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border-bottom:1px solid black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
            U.MUKA
          </td>
          <td style="border-bottom:1px solid black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
            IDR
          </td>
          <td style="border-bottom:1px solid black; border-left:none; border-top:none; border-right:none; text-align:right; font-weight:bold;">
            ${Math.round(Number(dataPrint[0].TotalUM || 0)).toLocaleString('id-ID')}
          </td>
        </tr>

        <!-- DPP -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:none; text-align:left; font-weight:bold;">
            DPP
          </td>
          <td style="border:none; text-align:left; font-weight:bold;">
            IDR
          </td>
          <td style="border:none; text-align:right; font-weight:bold;">
            ${Math.round(Number(dataPrint[0].NdppRp || 0)).toLocaleString('id-ID')}
          </td>
        </tr>
        <!-- PPN -->
        <tr>
          <td colspan="6" style="border:none;"></td>
          <td style="border:none; text-align:left; font-weight:bold;">
            PPN
          </td>
          <td style="border:none; text-align:left; font-weight:bold;">
            IDR
          </td>
          <td style="border:none; text-align:right; font-weight:bold;">
            ${Math.round(Number(dataPrint[0].NPpnRp || 0)).toLocaleString('id-ID')}
          </td>
        </tr>
        <!-- TOTAL -->
        <tr>
          <td colspan="6" style="border:none; border-left:none; border-top:none; border-right:none;"></td>
          <td style="border-bottom:3px double black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
            TOTAL
          </td>
          <td style="border-bottom:3px double black; border-left:none; border-top:none; border-right:none; text-align:left; font-weight:bold;">
            IDR
          </td>
          <td style="border-bottom:3px double black; border-left:none; border-top:none; border-right:none; text-align:right; font-weight:bold;">
            ${Math.round(Number(dataPrint[0].NNetRp || 0)).toLocaleString('id-ID')}
          </td>
        </tr>`};
        // end

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>`

         if (i == arrayDataPrint.length - 1) {
         tempPrintStr += `
         <div class="footer-sign font-family: sans-serif;
           font-size: 11px">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px">
         <span style="float: left; display: block; clear: left;">
         </span>

         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>

         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:11px;">

         <!-- KIRI -->
          <div style="width:40%; font-size:11px; margin-top:-100px;">
            <p class="m-0">TRANSFER : </p>
            <p class="m-0">CV. SINAR MAHAKAM LESTARI</p>
            <p class="m-0">PT. BANK DANAMON INDONESIA Tbk Cabang Banjarmasin</p>
            <p class="m-0">AC NO : 003646465454</p>
          </div>

          <!-- KANAN -->
          <div style="width:60%;  margin-top:-160px; margin-left: 40px;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; font-family: sans-serif;
             font-size: 11px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Hormat Kami,</td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 6rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               <p class="m-0" style="text-align: center;">ISKANDAR</p>
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

         </div>`};


         tempPrintStr += `
         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 11px ">
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


  function formatTanggalKwitansi (tanggal) {
    if (!tanggal) return '-';
    let tanggalOnly = String(tanggal).split(' ')[0].split('T')[0];
    let parts = tanggalOnly.indexOf('-') > -1 ? tanggalOnly.split('-') : tanggalOnly.split('/');
    if (parts.length !== 3) return tanggalOnly;
    // yyyy-mm-dd / yyyy/mm/dd -> dd/mm/yyyy, kalau sudah dd/mm/yyyy biarkan apa adanya
    if (parts[0].length === 4) return parts[2] + '/' + parts[1] + '/' + parts[0];
    return parts[0] + '/' + parts[1] + '/' + parts[2];
  }

  function submitPrintKwitansi (nobukti) {
  let _token = $('#_token').val()

  $.ajax({
    url: "{!! url('invoicepenjualandetailCetak3') !!}",
    type: "post",
    async: false,
    data: {
      _token: _token,
      NOBUKTI: nobukti
    },
    success: function(res) {
      console.log(res)
      dataPrint = res
      console.log(res[0])
    }
  })

  let tanggalOnly = formatTanggalKwitansi(dataPrint[0].Tanggal);

  const totalFormatted = Number(dataPrint[0].NNetRp || 0)
    .toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

  const html = `
  <html>
  <head>
    <title>Kwitansi</title>
    <style>
      * { box-sizing: border-box; margin: 0; padding: 0; }
      body {
        font-family: sans-serif;
        font-size: 12px;
        background: #fff;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 45px;
      }
      .kwitansi-title {
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 30px;
        letter-spacing: 2px;
      }
      .kwitansi-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 18px;
      }
      .kwitansi-label {
        width: 200px;
        font-weight: bold;
        font-size: 15px;
        flex-shrink: 0;
        text-align: left;
      }
      .kwitansi-colon {
        width: 20px;
        font-weight: bold;
        font-size: 15px;
        flex-shrink: 0;
        text-align: center;
      }
      .kwitansi-value {
        flex: 1;
        font-weight: bold;
        font-size: 15px;
      }
      .terbilang-value {
        flex: 1;
        font-weight: bold;
        font-size: 15px;
        font-style: italic;
        background: #e8e8e8;
        padding: 4px 0 4px 0;
      }
      .invoice-value {
        flex: 1;
        display: flex;
        font-size: 15px;
        gap: 10px;
        font-weight: bold;
      }
      .kwitansi-date {
        text-align: right;
        font-weight: bold;
        margin-top: 45px;
        margin-bottom: 15px;
        padding-right: 60px;
        font-size: 15px;
      }
      .kwitansi-amount-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1.5px solid #333;
        padding: 8px 20px;
        width: 250px;
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 20px;
        font-style: italic;
      }
      .kwitansi-amount-box .rp-label {
        margin-right: 30px;
      }
      .kwitansi-amount-box .amount {
        flex: 1;
        text-align: right;
      }
      .kwitansi-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: -20px;
        padding-right: 60px;
      }
      .kwitansi-sign {
        text-align: center;
        font-weight: bold;
        font-size: 15px;
        min-width: 150px;
      }
    </style>
  </head>
  <body onload="window.print()">
    <div class="kwitansi-wrapper">

      <div class="kwitansi-title">KWITANSI</div>

      <div class="kwitansi-row">
        <div class="kwitansi-label">Telah Terima Dari</div>
        <div class="kwitansi-colon">:</div>
        <div class="kwitansi-value">${dataPrint[0].NamaCustSupp ?? '-'}</div>
      </div>

      <div class="kwitansi-row">
        <div class="kwitansi-label">Terbilang Uang Sebesar</div>
        <div class="kwitansi-colon">:</div>
        <div class="terbilang-value">${dataPrint[0].TerBIlang ?? '-'} Rupiah</div>
      </div>

      <div class="kwitansi-row">
        <div class="kwitansi-label">Untuk Pembayaran</div>
        <div class="kwitansi-colon">:</div>
        <div class="invoice-value">
          <span>Invoice nomor</span>
          <span>${dataPrint[0].NoBukti ?? '-'}</span>
        </div>
      </div>

      <div class="kwitansi-date">Samarinda, ${tanggalOnly}</div>

      <div class="kwitansi-amount-box">
          <span class="rp-label">Rp.</span>
          <span class="amount">${totalFormatted}</span>
      </div>

      <div class="kwitansi-footer">
        <div class="kwitansi-sign">
          <div style="height: 100px;"></div>
          <div>ISKANDAR</div>
        </div>
      </div>

    </div>
  </body>
  </html>`;

  const w = window.open(' ');
  w.document.write(html);
  w.document.close();
  w.print();
  w.close();
}

function submitPrintKwitansiAll (nobukti) {
  console.log('submitPrintKwitansiAll')
  let _token = $('#_token').val()
  if (!arrayListInvoice.length) {


      alertify.warning('Tidak ada invoice dipilih')
      return
    }
  $.ajax({
    url: "{!! url('invoicepenjualandetailcetakall') !!}",
    type: "post",
    async: false,
    data: {
      _token: _token,
      tempData: arrayListInvoice
    },
    success: function(res) {
      console.log(res)
      dataPrintx = res
      console.log(res[0])
    }
  })
  
  $('#modalPrintAll').modal('toggle')
  let html = ''
  dataPrintx.forEach((item, i, arr) => {

  dataPrint = item
  const isLastKwitansi = i === arr.length - 1
  let tanggalOnly = formatTanggalKwitansi(dataPrint[0].Tanggal);

  const totalFormatted = Number(dataPrint[0].NNetRp || 0)
    .toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
     html += `<html>
  <head>
    <title>Kwitansi</title>
    <style>
      @page {
        size: 21.5cm 14cm;
        margin: 0;
      }
      * { box-sizing: border-box; margin: 0; padding: 0; }
      html, body {
        width: 21.5cm;
      }
      body {
        font-family: sans-serif;
        font-size: 12px;
        background: #fff;
        padding: 0 25px;
      }
      .kwitansi-wrapper {
        width: 100%;
        padding-top: 60px;
        page-break-after: always;
        break-after: page;
      }
      .kwitansi-title {
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 30px;
        letter-spacing: 2px;
      }
      .kwitansi-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 18px;
      }
      .kwitansi-label {
        width: 200px;
        font-weight: bold;
        font-size: 15px;
        flex-shrink: 0;
        text-align: left;
      }
      .kwitansi-colon {
        width: 20px;
        font-weight: bold;
        font-size: 15px;
        flex-shrink: 0;
        text-align: center;
      }
      .kwitansi-value {
        flex: 1;
        font-weight: bold;
        font-size: 15px;
      }
      .terbilang-value {
        flex: 1;
        font-weight: bold;
        font-size: 15px;
        font-style: italic;
        background: #e8e8e8;
        padding: 4px 0 4px 0;
      }
      .invoice-value {
        flex: 1;
        display: flex;
        font-size: 15px;
        gap: 10px;
        font-weight: bold;
      }
      .kwitansi-date {
        text-align: right;
        font-weight: bold;
        margin-top: 45px;
        margin-bottom: 15px;
        padding-right: 60px;
        font-size: 15px;
      }
      .kwitansi-amount-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1.5px solid #333;
        padding: 8px 20px;
        width: 250px;
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 20px;
        font-style: italic;
      }
      .kwitansi-amount-box .rp-label {
        margin-right: 30px;
      }
      .kwitansi-amount-box .amount {
        flex: 1;
        text-align: right;
      }
      .kwitansi-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: -20px;
        padding-right: 60px;
      }
      .kwitansi-sign {
        text-align: center;
        font-weight: bold;
        font-size: 15px;
        min-width: 150px;
      }
    </style>
  </head>
  <body onload="window.print()">`
  

   html += `

    <div class="kwitansi-wrapper"${isLastKwitansi ? ' style="page-break-after: auto; break-after: auto;"' : ''}>

      <div class="kwitansi-title">KWITANSI</div>

      <div class="kwitansi-row">
        <div class="kwitansi-label">Telah Terima Dari</div>
        <div class="kwitansi-colon">:</div>
        <div class="kwitansi-value">${dataPrint[0].NamaCustSupp ?? '-'}</div>
      </div>

      <div class="kwitansi-row">
        <div class="kwitansi-label">Terbilang Uang Sebesar</div>
        <div class="kwitansi-colon">:</div>
        <div class="terbilang-value">${dataPrint[0].TerBIlang ?? '-'} Rupiah</div>
      </div>

      <div class="kwitansi-row">
        <div class="kwitansi-label">Untuk Pembayaran</div>
        <div class="kwitansi-colon">:</div>
        <div class="invoice-value">
          <span>Invoice nomor</span>
          <span>${dataPrint[0].NoBukti ?? '-'}</span>
        </div>
      </div>

      <div class="kwitansi-date">Samarinda, ${tanggalOnly}</div>

      <div class="kwitansi-amount-box">
          <span class="rp-label">Rp.</span>
          <span class="amount">${totalFormatted}</span>
      </div>

      <div class="kwitansi-footer">
        <div class="kwitansi-sign">
          <div style="height: 100px;"></div>
          <div>ISKANDAR</div>
        </div>
      </div>

    </div>
  `;

})

  html += `</body>
  </html>`

  const w = window.open(' ');
  w.document.write(html);
  w.document.close();
  w.print();
  w.close();
}

  function submitPrintSPB (nobukti) {
    let akses = $("#akses_iscetak").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    // let printContent = "";
    // let css = "";
    // let hdr = "", nmcust="", alamatcust="", kdgd="", tgl="", noso="", nopo="", penerima="", alamatkirim="";
    // let str = "";
    // let ftr = "";
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('invoicePenjualanPrintSPB') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)
        dataPrint = []

        res.forEach((item, i) => {

          if (!dataPrint.length) {
            dataPrint.push(item)
          } else {
            let tempVar = dataPrint.findIndex((x) => x.kodebrg == item.kodebrg && x.NOSAT == item.NOSAT )
            // console.log(tempVar)
            if(tempVar >= 0) {
              dataPrint[tempVar].qntcetak = Number(dataPrint[tempVar].qntcetak) + Number(item.qntcetak)

            } else {
              dataPrint.push(item)
            }
          }

        });

        // console.log('2' , dataPrint)


        document.getElementById("input_print_norspb").value = res[0].nobukti
        document.getElementById("input_print_nosj").value = res[0].noso
        document.getElementById("input_print_gdg").value = res[0].namagdg
        document.getElementById("input_print_custsupp").value = res[0].namacustsupp
        nooutso = res[0].nooutso
        urutoutso = res[0].urutoutso

        let date = new Date(res[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#input_print_tanggal').val(date1)

        let rowTable = ""
        dataPrint.forEach((item, i) => {
          let qnt = 0.00
          let qntos = 0.00
          if(item.qntcetak) {
            qnt = parseFloat(item.qntcetak).toFixed(2)
          }
          if(item.qntos) {
            qntos = parseFloat(item.qntos).toFixed(2)
          }
          // console.log(item.qntos)
          // console.log(qntos)
          rowTable += `<tr class="text-left">
          <td>${item.kodebrg}</td>
          <td>${item.NamaBrgAL}</td>
          <td >${item.NAMAMERK}</td>
          <td class="text-right">${qnt}</td>
          <td>${item.SatuanAL}</td>

          </tr>`
        });
        document.getElementById("detailTablePrint").innerHTML = rowTable
        document.getElementById(`tempPrintContainer1`).innerHTML =''
        new QRCode(document.getElementById(`tempPrintContainer1`), {text: nobukti , width: 80, height: 80});
        // printContent = document.getElementById(`tempPrintContainer1`).innerHTML;
        // console.log(printContent)
        $("#formPrintLPB").modal('toggle')
      }
    })

    return

    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 10px !important;
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
        position: absolute;
        width: 100%;
        bottom: 15px;
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
                  <div class="pb-1" style="width: 20%">Kepada Yth:</div>
                  <div class="pb-1" style="width: 80%"></div>
                </div>
                <div class="" style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">`+nmcust+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div class="" style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">
                    `+alamatcust+`
                  </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Gudang : `+kdgd+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>



              </div>
              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">SURAT PENGANTAR BARANG</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Nomor</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+nobukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+tgl+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. SO</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+noso+`</div>
                </div>

                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. PO</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+nopo+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Penerima</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+penerima+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Alamat Kirim:</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+alamatkirim+`</div>
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
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif  display: inline-block ;
                font-size: 10px "


              >
                <thead>


                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 65%">NAMA BARANG</td>
                    <td class="text-center" style="width: 9%" >MERK</td>
                    <td class="text-center" style="width: 13%">KODE BARANG</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 12%">QTY</td>
                  </tr>
                </thead> `;
         str += `<tbody border="1">`;
         dataPrint.forEach((item, i) => {
        str += `

        <td class="text-align: center"
              style="width: 2%; ">${i+1}</td>
        <td class="text-align: left"
              style="width: 65%;  ">${item.NamaBrgAL}</td>
        <td class="text-align: left"
              style="width: 9%;">${item.NAMAMERK}</td>
        <td class="text-align: left"
              style="width: 13%;  ">${item.kodebrg}</td>
        <td class="text-align: center"
              style="width: 5%;  ">${item.SatuanAL}</td>
        <td class="text-align: text-right"
              style="width: 12%;  ">${item.qntcetak}</td>


          </tr>`;
           });
          str +=`
            <tr style>

            </tr>`;



             str += `</tbody>`;

    ftr = `</table>
     <hr />


    <div class="footer-sign font-family: sans-serif;
      font-size: 10px ">



    <div class="row mt-3" style="text-align: left;font-family: sans-serif;
    font-size: 10px ">
      <h5>
        *Barang diterima dengan baik dan sudah sesuai dengan yang tertera di
        Surat Pengantar Barang ini
      </h5>
    </div>


      <table
        class="detail-spb-table mb-2"
        style="width: 100%; margin-top: -15px ; font-family: sans-serif;
        font-size: 10px "
      >
        <tr>
          <td class="no-border text-center" style="width: 25%">
            Kepala Gudang
          </td>
          <td class="no-border text-center" style="width: 25%">Diperiksa</td>
          <td class="no-border text-center" style="width: 25%">Pembawa</td>
          <td class="no-border text-center" style="width: 25%">Penerima</td>
        </tr>
        <tr style="height: 2.5rem">
          <td class="no-border">&nbsp;</td>
        </tr>

        <tr>
          <td class="no-border px-2">
            <p class="m-0" style="border-bottom: 1px solid">
              Nama
            </p>
            <p class="m-0">Tanggal</p>
          </td>
          <td class="no-border px-2">
            <p class="m-0" style="border-bottom: 1px solid">Nama</p>
            <p class="m-0">Tanggal</p>
          </td>
          <td class="no-border px-2">
            <p class="m-0" style="border-bottom: 1px solid">Nama</p>
            <p class="m-0">Tanggal</p>
          </td>
          <td class="no-border px-2">
            <p class="m-0" style="border-bottom: 1px solid">Nama</p>
            <p class="m-0">Tanggal</p>
          </td>
        </tr>
      </table>
    </div>


    <div class="footer-print-date">
      <table class="m-0" style="width: 100% ; font-family: sans-serif;
      font-size: 10px ">
        <tr>
          <td class="no-border"></td>
          <td class="no-border text-right"></td>
        </tr>
      </table>
    </div>`;
    w=window.open(' ')

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title>Cetak SPB</title>
    </head>

    <body onload="window.print()">
      ` + css





      tempPrintStr +=  `</body></html>`

    w.document.write(`<html>
    <head>
      <title>Cetak SPB</title>
    </head>

    <body onload="window.print()">
      `+css+`
      <div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">
        `+hdr+`
        `+str+`
        `+ftr+`
      </div>
    </body>
    </html>`)
    w.print()
    w.close()
  }

function submitPrintSPBFinal () {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }


    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }
    let printContent = document.getElementById(`tempPrintContainer1`).innerHTML;
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 10px !important;
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
                  <div class="pb-1" style="width: 20%">Kepada Yth:</div>
                  <div class="pb-1" style="width: 80%"></div>
                </div>
                <div class="" style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">`+dataPrint[0].namacustsupp+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div class="" style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">
                    `+dataPrint[0].ALAMAT+`
                  </div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Gudang : `+dataPrint[0].KodeGdg+`</div>
                  <div class="pb-1" style="width: 0%"></div>
                </div>


              </div>
              <div style="width: 38%">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">SURAT PENGANTAR BARANG</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Nomor</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].nobukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].tgl+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. SO</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].noso+`</div>
                </div>

                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">No. PO</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].nopesanan+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Penerima</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].namakebun+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 20%">Alamat Kirim:</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 78%">`+dataPrint[0].alamatkirim+`</div>
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
                style="width: 100%; height: 225px; max-height: 225px;font-family: sans-serif  display: inline-block ;
                font-size: 10px "


              >
                <thead>


                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 65%">NAMA BARANG</td>
                    <td class="text-center" style="width: 9%" >MERK</td>
                    <td class="text-center" style="width: 13%">KODE BARANG</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 12%">QTY</td>
                  </tr>
                </thead> `;




    let z = 0
    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title>Cetak SPB</title>
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

         <td class="text-align: center"
               style="width: 2%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 65%;  ">${itemSub.NamaBrgAL}</td>
         <td class="text-align: left"
               style="width: 9%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: left"
               style="width: 13%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: center"
               style="width: 5%;  ">${itemSub.SatuanAL}</td>
         <td class="text-align: text-right"
               style="width: 12%;  ">${itemSub.qntcetak ? parseFloat(itemSub.qntcetak).toFixed(2) : ''}</td>


           </tr>`;

           z++




        });
        tempPrintStr +=`
          <tr style>

          </tr>`;



         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>
          <hr />


         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">






         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
          <span style="float: left">
           <h5>
             *Barang diterima dengan baik dan sudah sesuai dengan yang tertera di
             Surat Pengantar Barang ini
           </h5>
           </span>

           <span style="float: right">
            <h5>
              Total Koli : `+`${dataPrint[0].cetakkolli ? dataPrint[0].cetakkolli : ' ' }`+`
            </h5>
            </span>
         </div>


           <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -15px ; font-family: sans-serif;
             font-size: 10px "
           >
             <tr>
               <td class="no-border text-center" style="width: 25%">
                 Kepala Gudang
               </td>
               <td class="no-border text-center" style="width: 25%">Diperiksa</td>
               <td class="no-border text-center" style="width: 25%">Pembawa</td>
               <td class="no-border text-center" style="width: 25%">Penerima</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
                 <p class="m-0" style="border-bottom: 1px solid">
                   Nama
                 </p>
                 <p class="m-0">Tanggal</p>
               </td>
               <td class="no-border px-2">
                 <p class="m-0" style="border-bottom: 1px solid">Nama</p>
                 <p class="m-0">Tanggal</p>
               </td>
               <td class="no-border px-2">
                 <p class="m-0" style="border-bottom: 1px solid">Nama</p>
                 <p class="m-0">Tanggal</p>
               </td>
               <td class="no-border px-2">
                 <p class="m-0" style="border-bottom: 1px solid">Nama</p>
                 <p class="m-0">Tanggal</p>
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
    // w.document.write(`<html>
    // <head>
    //   <title>Cetak SPB</title>
    // </head>
    //
    // <body onload="window.print()">
    //   `+css+`
    //   <div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:7px">
    //     `+hdr+`
    //     `+str+`
    //     `+ftr+`
    //   </div>
    //   <div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:7px">
    //     `+hdr+`
    //     `+str+`
    //     `+ftr+`
    //   </div>
    // </body>
    // </html>`)
    w.print()
    w.close()

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

function formatAngkaX (angka) {
  if (Number(angka) == 0) {
    return '0.00'
  } else {

    return formatAngka(parseFloat(angka).toFixed(2))
  }

}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('invoicepenjualandetailCetak') !!}",
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
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
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
        height: 18px;
        padding: 0px 4px;
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
        height: 14cm;
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
                  <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                    <p class="m-0">
                      JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18
                      RT. 022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR
                    </p>
                  </div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">Kepada YTH : ${dataPrint[0].NamaCustSupp ?? '-'}</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 100%">${dataPrint[0].Alamat ?? '-'}</div>
                </div>
              </div>


              <div style="width: 40%; margin-left: 30px; margin-top: 15px;">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">INVOICE</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Nomor</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+dataPrint[0].NoBukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Tanggal</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">No PO</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">${dataPrint[0].PONO ?? '-'}</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Pembayaran</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">${dataPrint[0].HARI ?? '-'}</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Jatuh Tempo</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">${dataPrint[0].JthTempo ?? '-'}</div>
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

        <table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 15%">NO SPB</td>
                    <td rowspan="2" class="text-center" style="width: 40%">NAMA BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 5%">SAT</td>
                    <td rowspan="2" class="text-center" style="width: 10%">QTY</td>
                    <td rowspan="2" class="text-center" style="width: 10%">HARGA</td>
                    <td rowspan="2" class="text-center" style="width: 5%">DISKON</td>
                    <td rowspan="2" class="text-center" style="width: 15%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.SubTotal) {
        grandTotalJumlah += Number(item.SubTotal) || 0;
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
        item.forEach((itemSub, j) => {
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 1%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 15%;  ">${itemSub.nospb ?? ''}</td>
         <td class="text-align: left"
               style="width: 40%;  ">${itemSub.NamaBrg ?? ''}</td>
         <td class="text-center"
               style="width: 5%;  ">${itemSub.satuan ?? ''}</td>
         <td class="text-right"
               style="width: 10%;  ">${itemSub.Qty ? parseFloat(itemSub.Qty).toFixed(2) : ''}</td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.Harga
              ? Number(itemSub.Harga).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
          </td>
          <td class="text-right"
               style="width: 5%;  ">${itemSub.DISC ? parseFloat(itemSub.DISC).toFixed(2) + '%' : ''}</td>
          <td style="width: 15%; text-align: right;">
            ${itemSub.SubTotal
              ? Number(itemSub.SubTotal).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                })
              : ''}
          </td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none; border-right:none;"></td>
          </tr>`;
        }

	// total berada di paling bawah
        console.log(i, arrayDataPrint.length)
        if(i == arrayDataPrint.length - 1){

        tempPrintStr += `
        <tr>
          <td colspan="5" style="border:1px solid; padding:5px; font-weight:bold;">
            TERBILANG : ${(item && item.length > 0) ? item[0].terbilangAdaro : '-'} Rupiah
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            Jumlah
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            IDR
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${grandTotalJumlah.toLocaleString('id-ID', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            })}
          </td>
        </tr>`};
	//end

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>


         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">

          <!-- KIRI -->
          <div style="width:50%; font-size:10px; margin-top: 50px;">
            <p class="m-0">TRANSFER : </p>
            <p class="m-0">CV. SINAR MAHAKAM LESTARI
              PT. BANK DANAMON INDONESIA Tbk. Cabang Banjarmasin
              AC NO : 003-646-465-454 (IDR)</p>
            <p class="m-0"></p>
          </div>


          <!-- KANAN -->
          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Hormat Kami,</td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
		           <p class="m-0" style="text-align: center;">NOOR AIRINI</p>
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

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

function formatAngka (angkaString) {
  console.log('formatAngka', angkaString);

  if (angkaString === null || angkaString === undefined || angkaString === '') {
    angkaString = '0.00'
  }

  angkaString = parseFloat(angkaString).toFixed(2)

  let tempAngka = angkaString.split('.')
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


<script>
  // const tabHome = document.getElementById('nav-home-tab');
  // const tabProfile = document.getElementById('nav-profile-tab');

  function setActiveTab(idNav) {
    $(".nav-item").css("background-color", "#f8f9fa");
    $(".nav-item").css("color", "#007bff");
    console.log(idNav)
    document.getElementById(idNav).style.backgroundColor = '#007bff';
    document.getElementById(idNav).style.color = '#fff';

    // if (homeActive) {
    //   tabHome.style.backgroundColor = '#007bff';
    //   tabHome.style.color = '#fff';
    //   tabProfile.style.backgroundColor = '#f8f9fa';
    //   tabProfile.style.color = '#007bff';
    // } else {
    //   tabProfile.style.backgroundColor = '#007bff';
    //   tabProfile.style.color = '#fff';
    //   tabHome.style.backgroundColor = '#f8f9fa';
    //   tabHome.style.color = '#007bff';
    // }
  }

  // Default warna tab
  setActiveTab("nav-home-tab");

  // buat ganti tab
  document.getElementById('nav-home-tab').addEventListener('click', function () {
    setActiveTab("nav-home-tab");
  });

  document.getElementById('nav-profile-tab').addEventListener('click', function () {
    setActiveTab("nav-profile-tab");
  });

  document.getElementById('nav-profile1-tab').addEventListener('click', function () {
    setActiveTab("nav-profile1-tab");
  });
</script>




@endsection
