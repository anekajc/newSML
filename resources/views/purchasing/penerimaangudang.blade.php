@extends('newmaster')
@section('buttons')

@endsection
@section('content')
<div id="page1" class="container-fluid mainpage">

  <div class="container-fluid">

    <!-- <div id="qrcode"></div> -->
    <div class="row">
      <div class="col-6 text-left">
        <h1>Penerimaan Gudang</h1>
      </div>
      <div class="col-6 text-right">
        <!-- <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonAdd()"  >Add SO</button> -->
      </div>
    </div>
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
    <div class="card">
  <div class="card-header">
  <div class="row">
    <nav style="width: 100%;">
      <div class="nav nav-tabs col-12" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: black;">Surat Pengiriman Barang</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="color: black;">Invoice</a>
      </div>
    </nav>
  </div>
  </div>
  <div class="card-body" >
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      <div class="row">
        <div class="col-12" style="overflow:auto;">
          <div class="container-fluid">

                <table id="tabel" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">No. PO</th>
                      <th scope="col">Tanggal</th>
                      <th scope="col">Kode Supp</th>
                      <th scope="col" style="min-width: 150px">Supplier</th>
                      <th scope="col">PO Customer</th>
                      <th scope="col">Nama Customer</th>
                      <th scope="col">Gudang</th>
                      <th scope="col">Ekspedisi</th>

                      <th scope="col">Actions</th>
                    </tr>
                  </thead>


                  <tbody id="tabel_data" class="text-left" >
                    @for ($i = 0; $i < count($tempOutstanding); $i++)
                  <tr>
                    <td>{{ $tempOutstanding[$i]->NoBukti }}</td>
                    <td>{!! date("Y/m/d", strtotime($tempOutstanding[$i]->TANGGAL)) !!}</td>
                    <td>{{ $tempOutstanding[$i]->KODESUPP }}</td>
                    <td>{{ $tempOutstanding[$i]->NAMASUPP }}</td>
                    <td>{{ $tempOutstanding[$i]->NOPOCUST }}</td>
                    <td>{{ $tempOutstanding[$i]->NAMACUST }}</td>
                    <td>{{ $tempOutstanding[$i]->NAMAGDG }}</td>
                    <td>{{ $tempOutstanding[$i]->NAMAEXP }}</td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('{{ $tempOutstanding[$i]->NoBukti }}' , '{{ $tempOutstanding[$i]->NAMASUPP }}' , '{{ $tempOutstanding[$i]->KODESUPP }}' , '{{ $tempOutstanding[$i]->TANGGAL }}')"><i class="bi bi-plus"></i></button>
                    </td>
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

                <table id="tabel2" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">No Invoice</th>
                      <th scope="col">Tanggal</th>
                      <th scope="col">Kode Cust</th>
                      <th scope="col">Customer</th>
                      <th scope="col">No SPB</th>
                      <th scope="col">Tgl SPB</th>
                      <th scope="col">No SO</th>
                      <th scope="col">Tgl SO</th>
                      <th scope="col">No Pajak</th>
                      <th scope="col">PO Cust</th>


                      <th scope="col">Oto1</th>
                      <th scope="col">User Oto1</th>
                      <th scope="col">Tgl Oto1</th>

                      <th scope="col">Batal</th>
                      <th scope="col">User Batal</th>
                      <th scope="col">Tgl Batal</th>

                      <th scope="col">Actions</th>
                    </tr>
                  </thead>


                  <tbody id="tabel2_data" class="text-left" >

                    @for ($i = 0; $i < count($tempOutstanding2); $i++)
                    <tr>
                        <td>{{ $tempOutstanding2[$i]->NoBukti }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->Tanggal)) !!}</td>
                        <td>{{ $tempOutstanding2[$i]->KodeCustSupp }}</td>
                        <td>{{ $tempOutstanding2[$i]->NamaCustSupp }}</td>
                        <td>{{ $tempOutstanding2[$i]->NoSPB }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->TglSPB)) !!}</td>
                        <td>{{ $tempOutstanding2[$i]->NOSO }}</td>
                        <td>{!! date("Y/m/d", strtotime($tempOutstanding2[$i]->TGLSO)) !!}</td>

                        <td>{{ $tempOutstanding2[$i]->NoPajak }}</td>
                        <td>{{ $tempOutstanding2[$i]->NoPesanan }}</td>



                        @if ($tempOutstanding2[$i]->IsOtorisasi1)
                                  <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                                @else
                                <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                                @endif


                        <td>{{ $tempOutstanding2[$i]->OtoUser1 }}</td>
                        <td>{!! $tempOutstanding2[$i]->TglOto1 ? date("Y/m/d", strtotime($tempOutstanding2[$i]->TglOto1)) : '' !!}</td>


                        @if ($tempOutstanding2[$i]->Isbatal)
                                  <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                                @else
                                <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                                @endif


                        <td>{{ $tempOutstanding2[$i]->userBatal }}</td>
                        <td>{!! $tempOutstanding2[$i]->tglBatal ? date("Y/m/d", strtotime($tempOutstanding2[$i]->tglBatal)) : '' !!}</td>
                        <td class='text-center' style="vertical-align:middle">
                          <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $tempOutstanding2[$i]->NoBukti }}' , '{{ $tempOutstanding2[$i]->IsOtorisasi1 }}')"><i class="bi bi-pen"></i></button>
                          @if ($tempOutstanding2[$i]->IsOtorisasi1 == 1)
                          <button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}')"><i class="bi bi-key"></i></button>
                          @else
                          <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $tempOutstanding2[$i]->NoBukti }}')"><i class="bi bi-key"></i></button>

                          @endif


                        </td>
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
        <div class="row">
          <div class="col-6 text-left">
            <h1>Form Add</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonCloseForm()"  >Close</button>
          </div>
        </div>
      <!-- <button onclick="loadAll()">tes</button> -->
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


            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">

          </div> -->
            <div class="col-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_customer" placeholder="No SJ" disabled>
              </div>
            </div>


            <div class="col-4">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-8">
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
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl SO</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggalso" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <label>Tanggal</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggal" value="{!! date('Y-m-d') !!}"  >
                </div>
              </div>
            </div>
          </div>

          <!-- <div class="col-8">

          </div> -->
          <div class="col-md-3">
            <div class="row">

            </div>

          </div>






        </div>










        </div>



      <!-- </div> -->



    <div class="container-fluid mt-4" style="overflow-x: auto;" >

          <table id="addTable" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">v</th>
                <th scope="col">No SPB</th>
                <th scope="col">Tgl SPB</th>
                <th scope="col">No PO</th>

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
          <button id="" type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
        </div>
      </div>
    </div>
    </div>



    <div id= "modalKoreksi" class="showhideform">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row">
          <div class="col-6 text-left">
            <h1>Form Koreksi</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonCloseForm()"  >Close</button>
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

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-8">
              <div class="form-group input-group">
                <input type="text" class="form-control" id="input_koreksi_customer" placeholder="" disabled>
              </div>
            </div>


            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_koreksi_alamat"  disabled></textarea>
              </div>

            </div>
            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_koreksi_alamatx"  disabled></textarea>
              </div>

            </div>




            </div>
          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>
            </div>

            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->

                <div class="col-4">
                  <div class="form-group">
                    <label>No PO</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_koreksi_nopo" placeholder="" disabled>
                  </div>
                </div>

                <!-- </div>

              </div> -->



            </div>

            <!-- <div class="row"> -->
              <!-- <div class="col-6"> -->
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-8">

                    <select id="input_koreksi_pembayaran" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                      <option value=0 selected>Tunai</option>
                      <option value=1 >Kredit</option>
                    </select>
                  </div>
                </div>

              <!-- </div> -->
              <!-- <div class="col-6"> -->
                <div class="row">
                  <div class="col-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_koreksi_hari" placeholder="" disabled>
                    </div>
                  </div>
                </div>

              <!-- </div> -->



            <!-- </div> -->






          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>



            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-8">
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


            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_koreksi_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_koreksi_uangmuka" placeholder="" onblur="onChangeUangMuka('nuangmuka' , 'input_koreksi_uangmuka' , 'uang muka')">
                    </div>
                  </div>
                <!-- </div>

              </div> -->





            </div>



          </div>

          <div class="col-3">
            <div class="row">


              <div class="col-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_koreksi_valas" placeholder="" disabled>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_koreksi_kurs" placeholder="" onblur="onChangeKurs('kurs' , 'input_koreksi_kurs')">
                </div>
              </div>


              <div class="col-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_koreksi_catatan"  onblur="onChangeHeader('footnote' , 'input_koreksi_catatan', 'catatan')"></textarea>
                </div>
              </div>

            </div>

          </div>

        </div>



        </div>

      </div>



    <div class="container-fluid mt-4" style="overflow-x: auto;">

          <table id="koreksiTable" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode brg</th>
                <th scope="col">Nama Brg</th>
                <th scope="col">Nama Produk</th>
                <th scope="col">No SPB</th>
                <th scope="col">Qty</th>
                <th scope="col">Sat</th>
                <th scope="col">Harga</th>
                <th scope="col">Diskon</th>
                <th scope="col">Sub Total</th>
                <th scope="col">Keterangan</th>

                <th scope="col">Actions</th>

              </tr>
            </thead>


            <tbody id="koreksiTableData" class="" >
              <tr >

                  <td colspan=11 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
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


<div id="page3" class="container-fluid mainpage" style="display: none">

  <!-- <div id="" class="modal-content "> -->




    <div id= "" class="">
      <div class="container-fluid">

        <!-- <div id="qrcode"></div> -->
        <div class="row">
          <div class="col-6 text-left">
            <h1>Form Koreksi</h1>
          </div>
          <div class="col-6 text-right">
            <button type="button" class="btn btn-primary btn-lg " style="height: 60px; " onclick="buttonCloseForm()"  >Close</button>
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

          <div class="col-3">
            <div class="row">


            <div class="col-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>
            <!-- <div class="col-3 text-right">

          </div> -->
            <div class="col-8">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi_customer" placeholder="" disabled>
              </div>
            </div>


            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=4  class="form-control" id="input_otorisasi_alamat"  disabled></textarea>
              </div>

            </div>
            <div class="col-12">
              <div class="form-group">
                <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_alamatx"  disabled></textarea>
              </div>

            </div>




            </div>
          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>No Bukti</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_nobukti" placeholder="No bukti" disabled>
                </div>
              </div>



              <div class="col-4">
                <div class="form-group">
                  <label>No PO</label>
                </div>
              </div>
              <div class="col-8">
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

            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Pembayaran</label>
                    </div>
                  </div>
                  <div class="col-8">
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
                  <div class="col-4">
                    <div class="form-group">
                      <label>Hari</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control text-right" id="input_otorisasi_hari" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->



            </div>






          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-4">
                <div class="form-group">
                  <label>Tgl</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_otorisasi_tanggal" value="{!! date('Y-m-d') !!}" disabled >
                </div>
              </div>
            </div>



            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Tipe PPN</label>
                    </div>
                  </div>
                  <div class="col-8">
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


            <div class="row">
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Sales</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="text" class="form-control" id="input_otorisasi_sales" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->
              <!-- <div class="col-6">
                <div class="row"> -->
                  <div class="col-4">
                    <div class="form-group">
                      <label>Uang Muka</label>
                    </div>
                  </div>
                  <div class="col-8">
                    <div class="form-group">
                      <input type="number" class="form-control text-right" id="input_otorisasi_uangmuka" placeholder="" disabled>
                    </div>
                  </div>
                <!-- </div>

              </div> -->





            </div>



          </div>

          <div class="col-3">
            <div class="row">

              <div class="col-4">
                <div class="form-group">
                  <label>Valas</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_otorisasi_valas" placeholder="" disabled>
                </div>
              </div>

              <div class="col-4">
                <div class="form-group">
                  <label>Kurs</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <input type="number" class="form-control text-right" id="input_otorisasi_kurs" placeholder="" disabled>
                </div>
              </div>


              <div class="col-4">
                <div class="form-group">
                  <label>Catatan</label>
                </div>
              </div>
              <div class="col-8">
                <div class="form-group">
                  <textarea  style="width: 100%; resize: none" rows=3  class="form-control" id="input_otorisasi_catatan"  disabled></textarea>
                </div>
              </div>

            </div>

          </div>

          <!-- <div class="col-8">

          </div>
          <div class="col-4">
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



    <div class="container-fluid" style="overflow-x: auto;">

          <table id="otorisasiTable" class="table table-bordered table-striped"  >
            <thead class="text-center">
              <tr>
                <th scope="col">Kode brg</th>
                <th scope="col">Nama Brg</th>
                <th scope="col">Nama Produk</th>
                <th scope="col">No SPB</th>
                <th scope="col">Qty</th>
                <th scope="col">Sat</th>
                <th scope="col">Harga</th>
                <th scope="col">Diskon</th>
                <th scope="col">Sub Total</th>
                <th scope="col">Keterangan</th>

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


    <div class="container-fluid">
      <div id="" class="row ">
        <div class="col-12 text-right">

          <button id="" type="button" class="btn btn-primary" onclick="submitOtorisasi()">Otorisasi</button>
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







@endsection

@section('js')
<script type="text/javascript">


let dataTableAdd = []
let dataTableKoreksi = []

$(document).ready(function(){
      $("#tabel").DataTable({
        "lengthChange": false,
          "paging": false ,
          "columnDefs": [
          // { "type": "date", "targets": [3] },
          // {  "className": "text-right", "targets": [9,10,11,12] },
          // "columns" : [{"width" : "20px"}]

        ]
        });

        $("#tabel2").DataTable({
          "lengthChange": false,
            "paging": false ,
            "columnDefs": [
            // { "type": "date", "targets": [3] },
            // {  "className": "text-right", "targets": [9,10,11,12] },
            // "columns" : [{"width" : "20px"}]

          ]
          });



  //   formAddListItem
});





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
    if (checkDate.getDate() - new Date().getDate() > 5 || checkDate.getDate() - new Date().getDate() < -5 ) {
      alertify.warning("Tanggal invoice maks 5 hari")
      return
    }
    // return
    console.log(checkDate)
    console.log(tempData)


    $.ajax({
      url: "{!! url('penerimaangudangspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData,
        inputDate: inputDate

      },
      success: function(res) {

        console.log(res)

        // if (res.)


        loadAll()
        alertify.success("Berhasil menambah invoice penjualan")
        // $("#form").modal('toggle')
        buttonCloseForm()



      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })



}


function buttonAdd (noso , namacust, kodecust, tglso) {



  console.log('buttonAdd' , noso , namacust, kodecust, tglso)

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('penerimaangudanglistso') !!}",
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
              <td>${item.NoPesanan}</td>
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
  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi Invoice ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();



        $.ajax({
          url: "{!! url('penerimaangudangspbatalotorisasi') !!}",
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
    });

}

function submitOtorisasi () {
  let _token = $("#_token").val();
  let nobukti = $("#input_otorisasi_nobukti").val();

  $.ajax({
    url: "{!! url('penerimaangudangspotorisasi') !!}",
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

  })

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
    url: "{!! url('penerimaangudangspdetailkoreksi') !!}",
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
                <td>${item.KetDetail}</td>

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
    url: "{!! url('penerimaangudangspdetailkoreksi') !!}",
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
        dataTableKoreksi.forEach((item, i) => {
            rowTable += `
              <tr>
                <td>${item.KodeBrg}</td>
                <td>${item.NAMABRG}</td>
                <td>${item.NamabrgKom}</td>
                <td>${item.NoSPB}</td>
                <td class="text-right">${item.Qnt ? formatAngka(parseFloat(item.Qnt).toFixed(2)) : '0.00'}</td>
                <td>${item.Satuan}</td>
                <td class="text-right">${item.HARGA  ? formatAngka(parseFloat(item.HARGA).toFixed(2))  : '0.00'}</td>
                <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2))  : '0.00'}</td>
                <td class="text-right">${item.SubTotal ? formatAngka(parseFloat(item.SubTotal).toFixed(2))  : '0.00'}</td>
                <td>${item.KetDetail}</td>
                <td class="text-center">
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i} ,'${item.NoBukti}' , '${item.NAMABRG}' , '${item.Urut}'  )"><i class="bi bi-trash"></i></button>
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

function buttonKoreksiDelete (index, nobukti, namabrg, urut) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log('buttonKoreksiDelete' , index, nobukti, namabrg, urut )


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + namabrg + ' ?',
      function() {
        let _token = $("#_token").val();
        let choice = "D"





        $.ajax({
          url: "{!! url('penerimaangudangspdelete') !!}",
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
      url: "{!! url('penerimaangudangonchangeheader') !!}",
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
      url: "{!! url('penerimaangudangonchangedetail') !!}",
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
      url: "{!! url('penerimaangudangonchangeheader') !!}",
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
    url: "{!! url('penerimaangudangonchangeheader') !!}",
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
    url: "{!! url('penerimaangudangonchangedetail') !!}",
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
    // $("#form").modal('toggle')

    $('#page2').show();
    $('#page1').hide();
  }


  // $.ajax({
  //   url: "{!! url('invoicepenjualanspdetailkoreksi') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token,
  //     nobukti
  //
  //   },
  //   success: function(res) {
  //
  //     console.log(res)
  //     $('.showhideform').hide();
  //     $('#modalKoreksi').show();
  //     $("#form").modal('toggle')
  //
  //
  //
  //   },
  //   error: function (err) {
  //     console.log(err)
  //     alertify.warning('Terjadi kesalahan silahkan refresh browser')
  //   }
  //
  // })


}


function loadAll () {
  console.log('loadAll')






          $.ajax({
            url: "{!! url('penerimaangudangloadall') !!}",
            type: "get",
            async: false,
            data: {
            },
            success: function(res) {

              $('#tabel').DataTable().destroy();

              let rowTable = ''




              res.tempOutstanding.forEach((item, i) => {
                rowTable += `<tr>
                  <td>${item.NoBukti}</td>
                  <td>${ formatDate(item.Tanggal , '/')}</td>
                  <td>${item.NamaCustSupp ? item.NamaCustSupp : ''}</td>
                  <td>${item.Noso}</td>
                  <td>${ formatDate(item.TglSO , '/')}</td>
                  <td class="text-center">
                     <button class="btn btn-primary btn-sm" type="button" onclick="buttonAdd('${item.Noso }' , '${item.NamaCustSupp }' , '${item.KodeCustSupp }' , '${item.TglSO }')"><i class="bi bi-plus"></i></button>
                  </td>
                </tr>`
              });








              document.getElementById("tabel_data").innerHTML = rowTable



              $("#tabel").DataTable({
                "lengthChange": false,
                  "paging": false ,
                  "columnDefs": [
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
                      <td>${item.NoBukti }</td>
                      <td>${formatDate(item.Tanggal , '/')}</td>
                      <td>${item.KodeCustSupp }</td>
                      <td>${item.NamaCustSupp }</td>
                      <td>${item.NoSPB }</td>
                      <td>${formatDate(item.TglSPB , '/')}</td>
                      <td>${item.NOSO }</td>
                      <td>${formatDate(item.TGLSO , '/')}</td>

                      <td>${item.NoPajak }</td>
                      <td>${item.NoPesanan }</td>


                      ${Number(item.IsOtorisasi1) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}



                      <td>${item.OtoUser1 ? item.OtoUser1 : '' }</td>
                      <td>${item.TglOto1 ? formatDate(item.TglOto1 , '/') : '' }</td>

                      ${Number(item.Isbatal) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}




                      <td>${item.userBatal ? item.userBatal : '' }</td>
                      <td>${item.tglBatal ? formatDate(item.tglBatal , '/') : '' }</td>
                      <td class='text-center' style="vertical-align:middle">
                        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NoBukti }' , '${item.IsOtorisasi1}')"><i class="bi bi-pen"></i></button>`

                        if (Number(item.IsOtorisasi1)) {
                          rowTable2 += `<button class="btn btn-danger btn-sm" type="button" onclick="buttonBatalOtorisasi('${ item.NoBukti }')"><i class="bi bi-key"></i></button>`

                        } else {
                          rowTable2 += `
                          <button class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${ item.NoBukti }')"><i class="bi bi-key"></i></button>

                          `

                        }

                  rowTable2 +=    `</td>
                  </tr>
                  `
                });






                document.getElementById("tabel2_data").innerHTML = rowTable2











                $("#tabel2").DataTable({
                  "lengthChange": false,
                    "paging": false ,
                    "columnDefs": [
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
}



</script>




@endsection
