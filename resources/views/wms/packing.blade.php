@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Packing')
@section('buttons')

@endsection
@section('content')






<div class="container-fluid">
{{-- <h1>Packing</h1> - judul pindah ke header (page-title) --}}
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <button onclick="buttonPrintSPBTes()">TESCLICK</button> -->
</div>
<div id="tempPrintContainer1" style="display:none"></div>

<div id="printContainer" style="display:none">

</div>





<div id="tempPrintContainer" style="display:none">

</div>

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>



<div id="contentContainer" class="container-fluid">

  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />



  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding Dispatch</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Packing</a>
      </div>
    </div>
  </div>
  <div class="card wms-kartu">
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-20" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabel" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <!-- <th scope="col">Cetak Kolli</th> -->
                          <th scope="col">No. SO</th>
                          <th scope="col">No. PL</th>
                          <th scope="col">Gdg. PL</th>
                          <th scope="col">Tanggal</th>
                          <th style="min-width: 200px;">Nama Customer</th>
                          <th style="min-width: 200px;">Nama Barang</th>
                          <th scope="col">PO Customer</th>
                          <th scope="col">Qnt. Persiapan</th>
                          <th scope="col">Qnt OS</th>
                          <!-- <th scope="col">Stock</th>
                          <th scope="col">Stock G01</th>
                          <th scope="col">Stock G02</th>
                          <th scope="col">Stock G03</th> -->
                          <th scope="col">Sat</th>
                          <th scope="col">Catatan</th>

                          <th scope="col">Lokasi Penerima</th>
                          <th scope="col">Alamat Kirim</th>
                          <th scope="col">Due Date</th>
                          <th scope="col">Reff PR</th>

                          <th scope="col">Part Number</th>
                          <th scope="col">Merk</th>
                          <th scope="col">User ID</th>

                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{$outstandingArray[$i][0]->NOSO}}')" ><i class="bi bi-info-lg"></i></button>
                            <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('{{$outstandingArray[$i][0]->NOSO}}','{{$outstandingArray[$i][0]->gdgpl}}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
                          </td>
<!-- cetak kolli -->

                          <!-- <td class="text-center"><button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintKolli('{{ $outstandingArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-printer"></i></button></td> -->





                          <td>{{ $outstandingArray[$i][0]->NOSO }}</td>
                          <td>{{ $outstandingArray[$i][0]->NOBUKTI }}</td>
                          <td>{{ $outstandingArray[$i][0]->gdgpl }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->Tanggal)) !!}</td>
                          <td>{{ $outstandingArray[$i][0]->NamaCustSupp }}</td>
                          <td>{{ $outstandingArray[$i][0]->NamaBrgx }}</td>
                          <td>{{ $outstandingArray[$i][0]->Nopesanan }}</td>
                          <td>{{ $outstandingArray[$i][0]->Qnt }}</td>

                          <td>{{ $outstandingArray[$i][0]->QntOut }}</td>

                          <!-- <td>{{ $outstandingArray[$i][0]->SaldoQnt }}</td>
                          <td>{{ $outstandingArray[$i][0]->saldoqntg01 }}</td>
                          <td>{{ $outstandingArray[$i][0]->saldoqntg02 }}</td>
                          <td>{{ $outstandingArray[$i][0]->saldoqntg03 }}</td> -->

                          <td>{{ $outstandingArray[$i][0]->satuan }}</td>

                          <td>{{ $outstandingArray[$i][0]->catatan }}</td>
                          <td>{{ $outstandingArray[$i][0]->xckebun }}</td>


                          <td>{{ $outstandingArray[$i][0]->alamatlokasi }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i][0]->duedate)) !!}</td>

                          <td>{{ $outstandingArray[$i][0]->refpr }}</td>
                          <td>{{ $outstandingArray[$i][0]->partnumber }}</td>

                          <td>{{ $outstandingArray[$i][0]->namamerk ? $outstandingArray[$i][0]->namamerk : '' }}</td>
                          <td>{{ $outstandingArray[$i][0]->userid }}</td>

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
            <div class="col-14" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabelPersiapanspb" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">Cetak Kolli</th>
                          <th scope="col">No. Pack</th>
                          <th scope="col">No. SO</th>

                          <th scope="col">Tanggal</th>
                          <th scope="col">Kode Cust</th>
                          <th scope="col">Nama Cust</th>

                          <th scope="col">PO. Cust</th>
                          <th scope="col">Berat</th>
                          <!-- <th scope="col">User ID</th>

                          <th scope="col">Tgl. Kirim</th>
                          <th scope="col">Tgl. Terima</th>

                          <th scope="col">Oto 1</th>
                          <th scope="col">User oto1</th>
                          <th scope="col">Tgl oto1</th>

                          <th scope="col">Lokasi Terima</th> -->
                        </tr>
                      </thead>


                      <tbody id="tabelpersiapanspb_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <!-- <div class="container-fluid"> -->
                              <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->nobukti }}')" ><i class="bi bi-info-lg"></i></button>
                              <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->nobukti }}')"><i class="bi bi-pen"></i></button>
                              <!-- @if ($penerimaanArray[$i][0]->oto1 == 1) -->
                              <!-- <button disabled data-toggle="tooltip" data-placement="top" title="Otorisasi" class="btn btn-secondary btn-sm" type="button" onclick="buttonOtorisasi('{{ $penerimaanArray[$i][0]->nobukti }}' , '{{ $penerimaanArray[$i][0]->oto1 }}' )"><i class="bi bi-key"></i></button> -->
                              <!-- <button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintSPB('{{ $penerimaanArray[$i][0]->nobukti }}')" ><i class="bi bi-printer"></i></button> -->
                              <!-- @else -->
                              <!-- <button data-toggle="tooltip" data-placement="top" title="Otorisasi" class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('{{ $penerimaanArray[$i][0]->nobukti }}' , '{{ $penerimaanArray[$i][0]->oto1 }}' )"><i class="bi bi-key"></i></button> -->
                              <!-- <button disabled style="" class="btn btn-secondary btn-sm" type="button"   onclick="" ><i class="bi bi-printer"></i></button> -->
                              <!-- @endif -->
                            <!-- </div> -->
                          </td>
                          <td class="text-center"><button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintKolli('{{ $penerimaanArray[$i][0]->nobukti }}')" ><i class="bi bi-printer"></i></button></td>
                          <!-- <td class="text-center"><button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintKolli('{{ $penerimaanArray[$i][0]->nobukti }}')" ><i class="bi bi-printer"></i></button></td> -->
                          <td>{{ $penerimaanArray[$i][0]->nobukti }}</td>
                          <td>{{ $penerimaanArray[$i][0]->noso }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->tanggal)) !!}</td>
                          <td>{{ $penerimaanArray[$i][0]->kodecustsupp }}</td>
                          <td>{{ $penerimaanArray[$i][0]->namacustsupp }}</td>

                          <td>{{ $penerimaanArray[$i][0]->nopesanan }}</td>
                          <td>{{ $penerimaanArray[$i][0]->Berat }}</td>
                          <!-- <td>{{ $penerimaanArray[$i][0]->iduser }}</td>

                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->tglkirim)) !!}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->tglterima)) !!}</td>
                          @if ($penerimaanArray[$i][0]->oto1 == 0)
                          <td></td>
                          @else
                          <td>Approved</td>
                          @endif
                          <td>{{ $penerimaanArray[$i][0]->useroto1 }}</td>
                          <td>{!! $penerimaanArray[$i][0]->tgloto1 ? $penerimaanArray[$i][0]->tgloto1 : '' !!}</td>


                          <td>{{ $penerimaanArray[$i][0]->namakebun }}</td> -->
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


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Packing</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_norspb" placeholder="No TRT" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>




            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nosj" placeholder="No TRF" disabled>
              </div>
            </div>
          </div>

          <div class="row">

            <div class="col-12">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_namacust" placeholder="Gdg Tujuan" disabled>
              </div>
            </div>









            <div class="col-12">
              <div class="form-group">
                <label>Gudang</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_gdg" placeholder="Gdg Asal" required disabled>
              </div>
            </div>


            <div class="col-12">
              <div class="form-group">
                <label>Lokasi terima</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_lokasiterima" placeholder="Lokasi Terima" required disabled>
              </div>
            </div>


            <div class="col-12">
              <div class="form-group">
                <label>Alamat Kirim</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_alamatkirim" placeholder="Alamat kirim" required disabled>
              </div>
            </div>






            <!-- <div class="col-12">
              <div class="form-group">
                <label>No. Pol Kendaraan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nopolkendaraan" placeholder="No Pol Kendaraan" required enabled>
              </div>
            </div> -->

            <!-- <div class="col-12">
              <div class="form-group">
                <label>Expedisi</label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
              <select id="input_add_expedisi" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value="-" selected>-</option>
                @foreach ($expedisi as $exp)
                <option value="{{ $exp->KODECUSTSUPP }}">{{ $exp->KODECUSTSUPP }} {{ $exp->NAMACUSTSUPP }}</option>
                @endforeach
              </select>
            </div>
          </div> -->


          <!-- <div class="col-12">
            <div class="form-group">
              <label>Sopir</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_sopir" placeholder="Sopir" required enabled>
            </div>
          </div> -->


          <!-- <div class="col-12">
            <div class="form-group">
              <label>Ref UKM</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_refukm" placeholder="Ref UKM" required enabled>
            </div>
          </div> -->

          <!-- <div class="col-12">
            <div class="form-group">
              <label>Catatan SO</label>
            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_catatan" placeholder="Catatan" required enabled>
            </div>
          </div> -->



          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th style="" scope="col">Terima</th>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <!-- <th scope="col">Qty OS</th> -->
                    <th scope="col">Satuan</th>
                    <!-- <th scope="col">Qty SPB</th> -->

                  </tr>
                </thead>


                <tbody id="addTableData" class="text-right" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>

                </tr>

                </tbody>


              </table>
        </div>

        <div class="container-fluid">
          <div class="row">
            <div class="col-12 text-right">
              <label>Jumlah Kolli</label>
              <input type="number" value=1 style="max-width:50px" class="text-right" id="input_add_jumlahkolli" >
            </div>
            <!-- <div class="col-1">

            </div> -->

          </div>

        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal detail -->
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="row">



            <div class="col-12">
              <div class="form-group">
                <label>Nama Cust</label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_namacust" placeholder="Gdg Tujuan" disabled>
              </div>
            </div>





            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_detail_gdg" placeholder="Gdg Asal" required disabled>
              </div>
            </div>




            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>




          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto;">

              <table id="detailTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Brg</th>
                    <th scope="col">Nama Brg</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Qty OS</th>
                    <th scope="col">Satuan</th>

                  </tr>
                </thead>


                <tbody id="detailTableData" class="text-right" >
                  <tr >


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



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>

      </div>
    </div>
  </div>
</div>
<!-- End modal detail-->

<!-- start modal koreksi -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Packing</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nosj" placeholder="No TRF" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Gdg</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <!-- <button type="button" class="btn btn-primary" onclick="buttonKoreksiAdd()" class="btn btn-secondary"  >Add Item</button> -->
        </div>

        <div class="container-fluid">
          <!-- koreksi add -->
          <div id="formKoreksiAdd" class="container-fluid showhide">
            <div class="line"></div>
            <div class="row">
              <div class="col-4">
                <h4>Add Item</h4>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <label>Pilih Barang</label>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <select onchange="changeSelectKoreksiAdd()" id="koreksiAddSelect" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-6">
                <input id="koreksiAddKodeBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-2">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-6">
                <input id="koreksiAddNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Qty</label>
              </div>
              </div>
              <div class="col-3">
                <input id="koreksiAddInputQty" type="number" value=0.00 class="form-control">
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Satuan</label>
              </div>
              </div>
              <div class="col-3">

                  <input type="text" id="koreksiAddSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-1">
                <div class="form-group">
                <label>Qty OS</label>
              </div>
              </div>
              <div class="col-3">
                <input id="koreksiAddQtyOS" type="number" value=0.00 class="form-control" disabled>
              </div>
              <div class="col-1">
                <div class="form-group">
                <label>Qty PO</label>
              </div>
              </div>
              <div class="col-3">

                  <input id="koreksiAddQtyPO" type="number" value=0.00 class="form-control" disabled>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="" >Batal</button>
                <button type="button" onclick="submitAddKoreksi()" class="btn btn-primary" >Add Item</button>
              </div>

            </div>
          </div>

          <!-- koreksi edit -->

          <div id="formKoreksiEdit" class="container-fluid showhide">
            <div class="line"></div>
            <div class="row">
              <div class="col-12">
                <h4>Edit Item</h4>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Kode Barang</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditKodeBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Nama Barang</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditNamaBrg" type="text" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Qty</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditInputQty" type="number" value=0.00 class="form-control">
              </div>
              <div class="col-12">
                <div class="form-group">
                <label>Satuan</label>
              </div>
              </div>
              <div class="col-12">

                  <input type="text" id="koreksiEditSatuan" value="PCS" class="form-control" disabled>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                <label>Qty OS</label>
              </div>
              </div>
              <div class="col-12">
                <input id="koreksiEditQtyOS" type="number" value=0.00 class="form-control" disabled>
              </div>

            </div>
            <div class="row mt-2">
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-secondary" onclick="buttonBatalShowHide()" >Batal</button>
                <button type="button" onclick="submitEditKoreksi()" class="btn btn-primary" >Edit Item</button>
              </div>

            </div>
            <div class="line"></div>
          </div>

          <!-- end  -->
        </div>

          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="koreksiTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">No PL</th>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Satuan</th>

                      <th scope="col">Actions</th>
                    </tr>
                  </thead>


                  <tbody id="koreksiTableData" class="text-right" >
                    <tr >

                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                  </tr>

                  </tbody>


                </table>
          </div>
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 text-right">
                <label>Jumlah Kolli</label>
                <input type="number" value=1 style="max-width:50px" class="text-right" id="input_koreksi_jumlahkolli" disabled>
              </div>
              <!-- <div class="col-1">

              </div> -->

            </div>

          </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi-->



<!-- start modal koreksi detail-->
<div class="modal fade" id="formOtorisasi1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Otorisasi1</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Packing</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi1_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi1_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_otorisasi1_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_otorisasi1_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_otorisasi1_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="tableOtorisasi1" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <th scope="col">Qty OS</th>
                      <th scope="col">Satuan</th>

                    </tr>
                  </thead>


                  <tbody id="detailTableOtorisasi1" class="text-right" >
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
            <button type="button" class="btn btn-primary" onclick="submitOtorisasi1()"  >Approve</button>

          </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi detail-->

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
                <label>No Packing</label>
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
            <button type="button" class="btn btn-primary" onclick="submitPrint()"  >Print</button>

          </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi detail-->


<!-- start modal koreksi detail-->
<div class="modal fade" id="formPrintKolli" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Print Kolli</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Packing</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_printKolli_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_printKolli_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_printKolli_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_printKolli_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_printKolli_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="tablePrintKolli" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">No Picking</th>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <!-- <th scope="col">Merk</th> -->
                      <th scope="col">Qty</th>
                      <th scope="col">Satuan</th>

                    </tr>
                  </thead>


                  <tbody id="detailTablePrintKolli" class="text-right" >
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
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 text-right">
                <label>Jumlah Kolli</label>
                <input type="number" value=1 style="max-width:50px" class="text-right" id="input_printKolli_jumlahkolli" disabled>
              </div>
              <!-- <div class="col-1">

              </div> -->

            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="submitPrintKolli()"  >Print</button>

          </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal koreksi detail-->


<!-- start modal koreksi detail-->
<div class="modal fade" id="formKoreksiDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1500px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Packing</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_norspb" placeholder="No TRT" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>No SO</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_nosj" placeholder="No TRF" disabled>
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
                <input type="text" class="form-control" id="input_koreksidetail_gdg" placeholder="Kode Cust" required disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_koreksidetail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Cust Supp</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksidetail_custsupp" placeholder="Nama Cust" disabled>
              </div>
            </div>


          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="koreksiDetailTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">No PL</th>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>
                      <!-- <th scope="col">Qty OS</th> -->
                      <th scope="col">Satuan</th>

                    </tr>
                  </thead>


                  <tbody id="koreksiDetailTableData" class="text-right" >
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

          <div class="container-fluid">
            <div class="row">
              <div class="col-12 text-right">
                <label>Jumlah Kolli</label>
                <input type="number" value=1 style="max-width:50px" class="text-right" id="input_koreksidetail_jumlahkolli" disabled>
              </div>
              <!-- <div class="col-1">

              </div> -->

            </div>

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
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">

  // loadall
  let dataRefreshOutstanding = []
  let dataRefreshPenerimaan = []
  // add
  let addDataArray = []

  // koreksi
  let koreksiPenerimaanArray = []
  let koreksiDataEdit = {}
  let koreksiDataAddlist = []

  let dataPrint = []

  // new QRCode(document.getElementById(`tempPrintContainer1`), {text: 'test' , width: 80, height: 80});
  $(document).ready(function(){
      
        $("#tabel").DataTable({
          "lengthChange": false,
          "order": [[1, 'asc']],
            "paging": false ,
             "columnDefs": [
          { "type": "date", "targets": [4] },
          {  "className": "text-center", "targets": [0] },
          // "columns" : [{"width" : "20px"}]
        ]});





        $("#tabelPersiapanspb").DataTable({
          "lengthChange": false,
           "order": [[2, 'asc']],
            "paging": false ,
             "columnDefs": [
          { "type": "date", "targets": [4] },
          {  "className": "text-center", "targets": [0,1] },
        ]});



  });

  function buttonPrintSPBTes (nobukti) {

  }

  function buttonPrintKolli (nobukti) {
    let akses = $("#akses_iscetak").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    // document.getElementById("input_printKolli_jumlahprint").value = 1
    // let printContent = "";
    // let css = "";
    // let hdr = "", nmcust="", alamatcust="", kdgd="", tgl="", noso="", nopo="", penerima="", alamatkirim="";
    // let str = "";
    // let ftr = "";
    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpacking') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)
        dataPrint = res

        // document.getElementById("input_printKolli_norspb").value = res[0].nobukti
        // document.getElementById("input_printKolli_nosj").value = res[0].noso
        // document.getElementById("input_printKolli_gdg").value = res[0].namagdg
        // document.getElementById("input_printKolli_custsupp").value = res[0].namacustsupp
        document.getElementById("input_printKolli_norspb").value = res[0].nobukti
        document.getElementById("input_printKolli_nosj").value = res[0].noso
        document.getElementById("input_printKolli_gdg").value = res[0].namagdg
        document.getElementById("input_printKolli_custsupp").value = res[0].namacustsupp


        // document.getElementById("input_koreksi_norspb").value = koreksiPenerimaanArray[0].nobukti
        // document.getElementById("input_koreksi_nosj").value = koreksiPenerimaanArray[0].noso
        // document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].namagdg
        // document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].namacustsupp
        //
        document.getElementById("input_printKolli_jumlahkolli").value = res[0].cetakkolli


        // nooutso = res[0].nooutso
        // urutoutso = res[0].urutoutso

        let date = new Date(res[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#input_printKolli_tanggal').val(date1)

        let rowTable = ""
        res.forEach((item, i) => {
          let qnt = 0.00
          let qntos = 0.00
          if(item.qnt) {
            qnt = parseFloat(item.qnt).toFixed(2)
          }
          if(item.qntos) {
            qntos = parseFloat(item.qntos).toFixed(2)
          }
          // console.log(item.qntos)
          // console.log(qntos)
          rowTable += `<tr class="text-left">
          <td>${item.nopicking}</td>
          <td>${item.kodebrg}</td>
          <td>${item.namabrg}</td>
          <td class="text-right">${qnt}</td>
          <td>${item.satuan}</td>

          </tr>`
        });
        document.getElementById("detailTablePrintKolli").innerHTML = rowTable
        // document.getElementById(`tempPrintContainer1`).innerHTML =''
        // new QRCode(document.getElementById(`tempPrintContainer1`), {text: nobukti , width: 80, height: 80});
        // printContent = document.getElementById(`tempPrintContainer1`).innerHTML;
        // console.log(printContent)
        $("#formPrintKolli").modal('toggle')
      }
    })

    // return

  }

  function buttonPrintSPB (nobukti) {
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
      url: "{!! url('PrintSPB') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)
        dataPrint = res
        // nmcust=res[0].namacustsupp;
        // alamatcust=res[0].ALAMAT;
        // kdgd=res[0].KodeGdg;
        // tgl=res[0].tgl;
        // noso=res[0].noso;
        // nopo=res[0].nopesanan;
        // penerima=res[0].namakebun;
        // alamatkirim=res[0].alamatkirim;

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
        res.forEach((item, i) => {
          let qnt = 0.00
          let qntos = 0.00
          if(item.qnt) {
            qnt = parseFloat(item.qnt).toFixed(2)
          }
          if(item.qntos) {
            qntos = parseFloat(item.qntos).toFixed(2)
          }
          // console.log(item.qntos)
          // console.log(qntos)
          rowTable += `<tr class="text-left">
          <td>${item.kodebrg}</td>
          <td>${item.namabrg}</td>
          <td >${item.NAMAMERK}</td>
          <td class="text-right">${qnt}</td>
          <td>${item.satuan}</td>

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
              style="width: 65%;  ">${item.namabrg}</td>
        <td class="text-align: left"
              style="width: 9%;">${item.NAMAMERK}</td>
        <td class="text-align: left"
              style="width: 13%;  ">${item.kodebrg}</td>
        <td class="text-align: center"
              style="width: 5%;  ">${item.satuan}</td>
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

  function submitPrint () {
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
               style="width: 65%;  ">${itemSub.namabrg}</td>
         <td class="text-align: left"
               style="width: 9%;">${itemSub.NAMAMERK}</td>
         <td class="text-align: left"
               style="width: 13%;  ">${itemSub.kodebrg}</td>
         <td class="text-align: center"
               style="width: 5%;  ">${itemSub.satuan}</td>
         <td class="text-align: text-right"
               style="width: 12%;  ">${itemSub.qntcetak}</td>


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
              Total Koli : `+dataPrint[0].cetakkolli+`
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

  function submitOtorisasi1 () {

    let nobukti = $("#input_otorisasi1_norspb").val();
    let otorisasi = 1
    console.log(nobukti, otorisasi)
    let _token = $("#_token").val();


    $.ajax({
      url: "{!! url('persiapanspbspotorisasi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        otorisasi
      },
      success: function(res) {
        if (res > 0) {
          loadAll()
          alertify.success('Berhasil update otorisasi')
          $("#formOtorisasi1").modal('toggle')
        }


    },
    error: function (err) {
      alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
    }
  })
  }


  function buttonOtorisasi  ( nobukti , isOtorisasi ) {
    console.log(nobukti , isOtorisasi )
    // isOtorisasi = 1

  let akses = $("#akses_isotorisasi1").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('2')
  if(isOtorisasi > 0) {
    alertify.warning('Sudah diotorisasi')
    return
  }

  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('detailpersiapanspb') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      NOBUKTI: nobukti
    },
    success: function(res) {
      console.log(res,'==')
      // koreksiPenerimaanArray = res
      // koreksiDataEdit = res[0]
      console.log('=============!!==============')
      document.getElementById("input_otorisasi1_norspb").value = res[0].nobukti
      document.getElementById("input_otorisasi1_nosj").value = res[0].noso
      document.getElementById("input_otorisasi1_gdg").value = res[0].namagdg
      document.getElementById("input_otorisasi1_custsupp").value = res[0].namacustsupp
      nooutso = res[0].nooutso
      urutoutso = res[0].urutoutso

      let date = new Date(res[0].tanggal);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      $('#input_otorisasi1_tanggal').val(date1)

      let rowTable = ""
      res.forEach((item, i) => {
        let qnt = 0.00
        let qntos = 0.00
        if(item.qnt) {
          qnt = parseFloat(item.qnt).toFixed(2)
        }
        if(item.qntos) {
          qntos = parseFloat(item.qntos).toFixed(2)
        }
        // console.log(item.qntos)
        // console.log(qntos)
        rowTable += `<tr class="text-left">
        <td>${item.kodebrg}</td>
        <td>${item.namabrg}</td>
        <td class="text-right">${qnt}</td>
        <td class="text-right">${qntos}</td>
        <td>${item.satuan}</td>

        </tr>`
      });
      document.getElementById("detailTableOtorisasi1").innerHTML = rowTable

      res.forEach((item, i) => {
        //JsBarcode(`#barcodeKoreksiDetail${i}`, item.kodebrg ,{width: 2, height: 25,});
      });

    }
  })
  console.log('1')
  $("#formOtorisasi1").modal('toggle')

  // let _token = $("#_token").val();

  // pembelianpermintaannonagenupdateotorisasi
  return
  // $.ajax({
  //   url: "{!! url('pembelianpermintaannonagenupdateotorisasi') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token,
  //     nobukti,
  //     otorisasi :1
  //   },
  //   success: function(res) {
  //     if (res > 0) {
  //       loadAll()
  //       alertify.success('Berhasil update otorisasi')
  //     }
  //
  //
  // }})
  }

  function submitAdd () {
    console.log('Submit Add')
    let _token = $("#_token").val();
    let tempData = []

    // input_add_jumlahkolli

    let jumlahkolli = $("#input_add_jumlahkolli").val();

    // console.log('TES ==========')
    // return
    // let qtyCheck = false

    addDataArray.forEach((item, i) => {
      if (document.getElementById(`add_checkbox${i}`).checked) {
        // addDataArray[i].inputQntTerima = $(`#input_add_qntTerima${i}`).val();
        // if (Number(addDataArray[i].inputQntTerima) > Number(addDataArray[i].QntOut)) {
        //   qtyCheck = true
        // }
        tempData.push(addDataArray[i])
      }
    });


    // if (qtyCheck) {
    //   // console.log(qtyCheck, 'true')
    //   // qtyCheck = false
    //   alertify.warning("Qty SPB tidak bisa lebih besar dari Qty OS");
    //   return
    // }

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }
    let flag = false
    // tempData.forEach((item, i) => {
    //   console.log(item , '==================')
    //   // console.log(Number(item.inputQntTerima) ,Number(item.QntOut) )
    //   // if (Number(item.inputQntTerima) > Number(item.QntOut)) {
    //   //   console.log('os')
    //   //
    //   //   // return
    //   //   flag =true
    //   // }
    //
    //   if (Number(item.inputQntTerima) <= 0) {
    //     console.log('negatif')
    //
    //     // return
    //     flag =true
    //   }
    // });

    if (Number(jumlahkolli) < 0) {
      flag = true
    }



    if (flag) {
      alertify.warning("Qty tidak bisa negatif ");
      return
    }
    let inputDate = $("#input_add_tanggal").val();
    let nosj = $(`#input_add_nosj`).val();
    let norspb = $(`#input_add_norspb`).val();
    let nourut =  $(`#input_add_noUrut`).val();
    let nopolkendaraan =  ''
    let expedisi = ''
    let lokasiterima =  $(`#input_add_lokasiterima`).val();


    let checkDate = new Date(inputDate)

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value
    // console.log('asd')

    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

        alertify.warning("Tanggal tidak sesuai periode");
        return
    }




    console.log('nosj', nosj)
    console.log('norspb', norspb)
    console.log('nourut', nourut)
    console.log('inputDate', inputDate)
    console.log(tempData)
    console.log('nopolkendaraan', nopolkendaraan)
    console.log('expedisi', expedisi)
    // return

    $.ajax({
      url: "{!! url('addpacking') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        nosj,
        norspb,
        nourut,
        tempData,
        inputDate,
        nopolkendaraan,
        expedisi,
        jumlahkolli
      },
      success: function(res) {
        console.log(res ,'!')
        // loadAll()
        if(res == 1) {

          $("#form").modal('toggle')
          alertify.success('Packing telah ditambah');
          console.log('before ===========')
          loadAll()
          console.log("after ===========")
        }

        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }


        if (res == 8 ) {
          alertify.warning('Tanggal Tidak Sesuai Periode');
        }

        if (res == 3 ) {
          alertify.warning('Stok gudang tidak mencukupi');
        }

        if (res == 4 ) {
          alertify.warning('Item sudah dipacking');
        }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })
  }

  function buttonBatalShowHide() {
    $('.showhide').hide();
  }

  function buttonKoreksiEdit(index) {
    let akses = $("#akses_iskoreksi").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    $('.showhide').hide();
    // console.log(koreksiPenerimaanArray[index])
    // console.log(index)
    koreksiDataEdit = koreksiPenerimaanArray[index]
    console.log('edit data',koreksiDataEdit)
    let qnt = 0.00
    if (koreksiDataEdit.qnt) {

      qnt = parseFloat(koreksiDataEdit.qnt).toFixed(2)
    }
    document.getElementById("koreksiEditKodeBrg").value = koreksiDataEdit.kodebrg
    document.getElementById("koreksiEditNamaBrg").value = koreksiDataEdit.namabrg
    document.getElementById("koreksiEditQtyOS").value = koreksiDataEdit.qntos
    document.getElementById("koreksiEditInputQty").value = qnt
    $('#formKoreksiEdit').show();

    document.getElementById('formKoreksiEdit').scrollIntoView();
  }

  function buttonKoreksiAdd() {
    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    $('.showhide').hide();
    document.getElementById("koreksiAddKodeBrg").value = ""
    document.getElementById("koreksiAddNamaBrg").value = ""
    document.getElementById("koreksiAddQtyOS").value = 0.00
    document.getElementById("koreksiAddQtyPO").value = 0.00
    document.getElementById("koreksiAddInputQty").value = "0.00"
    $('#formKoreksiAdd').show();
  }

  function buttonDetailKoreksi(NOBUKTI) {
    let _token = $("#_token").val();

    console.log(NOBUKTI,'==')
    //return
    $.ajax({
      url: "{!! url('detailpacking') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res,'==')
        // koreksiPenerimaanArray = res
        // koreksiDataEdit = res[0]
        console.log('=============!!==============')
        document.getElementById("input_koreksidetail_norspb").value = res[0].nobukti
        document.getElementById("input_koreksidetail_nosj").value = res[0].noso
        document.getElementById("input_koreksidetail_gdg").value = res[0].namagdg
        document.getElementById("input_koreksidetail_custsupp").value = res[0].namacustsupp
        document.getElementById("input_koreksidetail_jumlahkolli").value = res[0].cetakkolli
        nooutso = res[0].nooutso
        urutoutso = res[0].urutoutso

        let date = new Date(res[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#input_print_tanggal').val(date1)

        let rowTable = ""
        res.forEach((item, i) => {
          let qnt = 0.00
          let qntos = 0.00
          if(item.qnt) {
            qnt = parseFloat(item.qnt).toFixed(2)
          }
          if(item.qntos) {
            qntos = parseFloat(item.qntos).toFixed(2)
          }
          // console.log(item.qntos)
          // console.log(qntos)
          rowTable += `<tr class="text-left">
          <td>${item.nopicking}</td>
          <td>${item.kodebrg}</td>
          <td>${item.namabrg}</td>
          <td class="text-right">${qnt}</td>

          <td>${item.satuan}</td>

          </tr>`
        });
        document.getElementById("koreksiDetailTableData").innerHTML = rowTable

        res.forEach((item, i) => {
          //JsBarcode(`#barcodeKoreksiDetail${i}`, item.kodebrg ,{width: 2, height: 25,});
        });

      }
    })


      $("#formKoreksiDetail").modal('toggle')
  }

  function buttonKoreksi (NOBUKTI) {
    console.log('button koreksi')
    console.log(NOBUKTI)
    $('.showhide').hide();

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpacking') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res)
        koreksiPenerimaanArray = res
        koreksiDataEdit = res[0]
        console.log('=============!!==============')
      }
    })

    document.getElementById("input_koreksi_norspb").value = koreksiPenerimaanArray[0].nobukti
    document.getElementById("input_koreksi_nosj").value = koreksiPenerimaanArray[0].noso
    document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].namagdg
    document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].namacustsupp

    document.getElementById("input_koreksi_jumlahkolli").value = koreksiPenerimaanArray[0].cetakkolli
    let date = new Date(koreksiPenerimaanArray[0].tanggal);
    let day = ("0" + date.getDate()).slice(-2);
    let month = ("0" + (date.getMonth() + 1)).slice(-2);
    date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#input_koreksi_tanggal').val(date1)
    let rowTable = ""
    koreksiPenerimaanArray.forEach((item, i) => {
      let qnt = 0.00
      let qntos = 0.00
      if(item.qnt) {
        qnt = parseFloat(item.qnt).toFixed(2)
      }
      if(item.qntos) {
        qntos = parseFloat(item.qntos).toFixed(2)
      }
      rowTable += `<tr class="text-left">
      <td>${item.nopicking}</td>
      <td>${item.kodebrg}</td>
      <td>${item.namabrg}</td>
      <td class="text-right">${qnt}</td>
      <td>${item.satuan}</td>

      <td class="text-center">

      <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
      </td>
      </tr>`
    });

    // <button class="btn btn-warning btn-sm" type="button" onclick="buttonKoreksiEdit(${i})"><i class="bi bi-pen"></i></button>
    document.getElementById("koreksiTableData").innerHTML = rowTable

    koreksiPenerimaanArray.forEach((item, i) => {
    //  JsBarcode(`#barcodeKoreksi${i}`, item.kodebrg ,{width: 2, height: 25,});
    });

    $.ajax({
      url: "{!! url('spbkoreksiaddlist') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        norspb: koreksiPenerimaanArray[0].NOBUKTI,
        nosj: koreksiPenerimaanArray[0].NoSPB
      },
      success: function(res) {
        console.log(res , "addlistkoreksi !!!!!!")
        koreksiDataAddList = res
      }
    })
    // console.log(koreksiDataAddList, "asdadasd")
    let tempKoreksiAddList = ""
    if(koreksiDataAddList.length) {
      tempKoreksiAddList += `<option value="" selected disabled>-- Pilih Barang --</option>`
      koreksiDataAddList.forEach((item, i) => {
        tempKoreksiAddList += `<option value="${i}">${item.KODEBRG} - ${item.NAMABRG}</option>`
      });
    } else {
      tempKoreksiAddList += `<option value="" selected disabled>Tidak ada barang untuk ditambah</option>`
    }



    document.getElementById("koreksiAddSelect").innerHTML = tempKoreksiAddList

    $("#formKoreksi").modal('toggle')

  }

  function changeSelectKoreksiAdd() {
    let indexBarang = document.getElementById("koreksiAddSelect").value;
    console.log(indexBarang)
    console.log(koreksiDataAddList[indexBarang])
    let qnt = 0.00
    let qntos = 0.00
    if(koreksiDataAddList[indexBarang].QNT) {
      qnt = parseFloat(koreksiDataAddList[indexBarang].QNT).toFixed(2)
    }
    if(koreksiDataAddList[indexBarang].QntOS) {
      qntos = parseFloat(koreksiDataAddList[indexBarang].QntOS).toFixed(2)
    }
    document.getElementById("koreksiAddKodeBrg").value = koreksiDataAddList[indexBarang].KODEBRG
    document.getElementById("koreksiAddNamaBrg").value = koreksiDataAddList[indexBarang].NAMABRG
    document.getElementById("koreksiAddQtyOS").value = qntos
    document.getElementById("koreksiAddQtyPO").value = qnt

  }

  function buttonKoreksiDelete (index) {
    let akses = $("#akses_ishapus").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }
    console.log(koreksiPenerimaanArray[index])
    let dataRSPB = koreksiPenerimaanArray[index]
    console.log(dataRSPB)
    // return
    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataRSPB.kodebrg + dataRSPB.namabrg + ' ?',
      function() {


        // choice
        // nobukti
        // nourut
        // tanggal
        // noso
        // jumlahkolli
        // iduser
        // urut
        // nopicking
        // urutpicking
        // qnt


        let choice = 'D'
        let _token = $("#_token").val();
        let nobukti = dataRSPB.nob
        let nourut = dataRSPB.NOURUT
        let inputDate = $("#input_koreksi_tanggal").val()
        let noso = dataRSPB.noso
        let jumlahkolli = dataRSPB.cetakkolli
        let urut = dataRSPB.urutb
        let nopicking = ''
        let urutpicking = 0
        let qnt = 0

        console.log('choice' , choice)
        console.log('nobukti' , nobukti)
        console.log('nourut' , nourut )
        console.log('inputDate' , inputDate)
        console.log('noso' , noso)
        console.log('jumlahkolli' , jumlahkolli)
        // console.log(iduser
        console.log('urut', urut)
        console.log('nopicking' , nopicking)
        console.log('urutpicking' , urutpicking)
        console.log('qnt' , qnt)



        // return

        // return
        $.ajax({
          url: "{!! url('koreksiPacking') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti,
            nourut ,
            inputDate,
            noso,
            jumlahkolli,

            urut,
            nopicking,
            urutpicking,
            qnt
          },
          success: function(res) {
            if(res == 1) {

              console.log(res ,'succes hapus')
              refreshKoreksi(nobukti)
              alertify.success('Item telah didelete');
              loadAll()
            } else if (res == 2) {
              alertify.warning('Item telah diproses SPB');

            }
          }
        })
      }
    ,function(){
      console.log('no')
    });
  }



  function submitAddKoreksi () {
    // console.log('submit add koreksi')
    // console.log(koreksiDataTrt)
    let _token = $("#_token").val();
    let check = document.getElementById("koreksiAddSelect").value;
    // console.log(check,'check')
    if (check === "") {
      // console.log('a')
      alertify.warning("Tidak ada item dipilih");
      return
    }
    let dataSJ = koreksiDataAddList[check]
    let qntTerima = $("#koreksiAddInputQty").val()
    if (Number(qntTerima) > Number(dataSJ.QntOS)) {
      alertify.warning("Qty tidak bisa lebih besar dari Qty OS");
      return
    }
    if (Number(qntTerima) <= 0) {
      alertify.warning("Qty tidak bisa 0 atau negatif");
      return
    }

    let qntTerima1 = 0
    let qntTerima2 = 0
    if (dataSJ.NOSAT == 1) {
      qntTerima1 = qntTerima
      qntTerima2 = qntTerima / dataSJ.ISI2
    } else if (dataSJ.NOSAT == 2) {
      qntTerima1 = qntTerima * dataSJ.ISI2
      qntTerima2 = qntTerima
    }
    console.log(dataSJ , qntTerima)
    console.log(koreksiDataEdit)
    let dataRSPB = koreksiDataEdit
    let choice = "I"
    let norspb = dataRSPB.NOBUKTI
    let nourut = dataRSPB.NoUrut
    let inputDate = $("#input_koreksi_tanggal").val()
    let nosj = dataSJ.NOBUKTI
    let kodecustsupp = dataSJ.KODECUSTSUPP
    let nopolkend = ""
    let container = ""
    let nocontainer = ""
    let noseal = ""
    let catatan = ""
    let urut = 0
    let urutsj = dataSJ.URUT
    let kodebrg = dataSJ.KODEBRG
    let sat_1 = dataSJ.SAT1
    let sat_2 = dataSJ.SAT2
    let nosat = dataSJ.NOSAT
    let isi = dataSJ.ISI
    let netw = 0
    let grossw = 0
    let isempty = 1
    let namabrg = dataSJ.NAMABRG
    let flagmenu = 1
    let flagtipe = 1
    let retursupp = 0


    console.log('choice' , choice)
    console.log('norspb' , norspb)
    console.log('nourut', nourut)
    console.log('inputDate' , inputDate)
    console.log('nosj' , nosj)
    console.log('kodecustsupp' , kodecustsupp)
    console.log('nopolkend' , nopolkend)
    console.log('container' , container)
    console.log('nocontainer' , nocontainer)
    console.log('noseal' , noseal)
    console.log('catatan' , catatan)
    console.log('urut' , urut)
    console.log('urutsj' , urutsj)
    console.log('kodebrg' , kodebrg)
    console.log('qntTerima' , qntTerima)
    console.log('qntTerima2' , qntTerima2)
    console.log('sat_1' , sat_1)
    console.log('sat_2' , sat_2)
    console.log('nosat' , nosat)
    console.log('isi' , isi)
    console.log('netw' , netw)
    console.log('grossw' , grossw)
    console.log('isempty' , isempty)
    console.log('namabrg' , namabrg)
    console.log('flagmenu' , flagmenu)
    console.log('flagtipe' , flagtipe)
    console.log('retursupp' , retursupp)


    return
    $.ajax({
      url: "{!! url('koreksiSuratJalan') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        norspb,
        nourut,
        inputDate,
        nosj,
        kodecustsupp,
        nopolkend,
        container,
        nocontainer,
        noseal,
        catatan,
        urut,
        urutsj,
        kodebrg,
        qntTerima,
        qntTerima2,
        sat_1,
        sat_2,
        nosat,
        isi,
        netw,
        grossw,
        isempty,
        namabrg,
        flagmenu,
        flagtipe,
        retursupp,
      },
      success: function(res) {
        console.log(res ,'succes add koreksi')
        refreshKoreksi(norspb)
        alertify.success('Item telah ditambah');
        loadAll()
      }
    })


  }



  function submitPrintKolli () {



    // let _token = $("#_token").val();

    let printContent1 = ""
    let jumlahPrint = $(`#input_printKolli_jumlahkolli`).val();
    let z = 0

    // $.ajax({
    //   url: "{!! url('persiapanspbupdatecetakkolli') !!}",
    //   type: "post",
    //   async: false,
    //   data: {
    //     _token : _token,
    //     nobukti: dataPrint[0].nobukti,
    //     jumlahPrint
    //
    //   },
    //   success: function(res) {
        console.log(dataPrint)
        console.log(jumlahPrint)
        // return
        let dataPrintx = []
        dataPrintx.push(dataPrint[0])

        dataPrintx.forEach((item, i) => {
          // if (document.getElementById(`printChecklist${i}`).checked) {
            // qrcodeprint
            let date = new Date(item.tanggal);
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
            // let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;
            // console.log(printContent , 'printConcent')
            printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`
            for (let i=0; i < jumlahPrint ; i++ ) {

              // if (z == 0) {
                printContent1 += `<div style="font-size: 18px;break-inside: avoid; width:20cm; height: 8.5cm; overflow: hidden; border-style: solid; margin-top: 20px">`
                printContent1 += `<div  style="display: flex; flex-direction: row;  ">`
              // } else {
              //   printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm;  overflow: hidden;">`
              //   printContent1 += `<div  style="display: flex; flex-direction: row; margin-top: 10px">`
              // }

              printContent1 += `<div style="display: flex; flex-direction: column; width: 20cm; text-align: center; border-bottom: solid;">`
              printContent1 += `<div style="font-weight: bold; background-color: #D3D3D3"><h2>PACKING LIST</h2></div>`
              printContent1 += `</div>`
              // printContent1 += `<div style="padding-right: 16px" >`
              // printContent1 += printContent
              // printContent1 += `</div>`
              printContent1 += `</div>`


              printContent1 += `<div class="" style="margin-top: 20px;text-align: left;width: 20cm;display: flex; flex-direction: row; ">`
              // printContent1 += `<div style='width: 20cm'>`
              // printContent1 += `<div  style="background-color: gold; width:20%; ">` + 'Nama Cust 2' +`</div>`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%;" >` + 'Nama Customer' +`</div>`
              // printContent1 += `<div style="width:10%">` + `</div>`
              printContent1 += `<div style=" width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${item.namacustsupp ? item.namacustsupp : ''}` +`</div>`
              printContent1 += `</div>`
              // printContent1 += `</div>`

              printContent1 += `<div class="" style="text-align: left;width: 20cm;display: flex; flex-direction: row; ">`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%;" >` + 'Lokasi Penerima' +`</div>`
              printContent1 += `<div style=" width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${item.namakebun ? item.namakebun : ''}` +`</div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="text-align: left;width: 20cm;display: flex; flex-direction: row; height: 40px; overflow: hidden">`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%;" >` + 'Alamat Kirim' +`</div>`
              printContent1 += `<div style=" width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${item.alamatkirim ? item.alamatkirim : ''}` +`</div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="text-align: left;width: 20cm;display: flex; flex-direction: row; ">`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%;" >` + 'No PO' +`</div>`
              printContent1 += `<div style=" width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${item.nopesanan ? item.nopesanan : ''}` +`</div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="text-align: left;width: 20cm;display: flex; flex-direction: row; ">`
              // printContent1 += `<div style='width: 20cm'>`
              // printContent1 += `<div  style="background-color: gold; width:20%; ">` + 'No SO 2' +`</div>`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%; ">` + 'No Packing' +`</div>`
              // printContent1 += `<div style="width:10%">` + `</div>`
              printContent1 += `<div style="width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${item.nob ? item.nob : ''}` +`</div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="text-align: left;width: 20cm;display: flex; flex-direction: row; ">`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%;" >` + 'Kolli' +`</div>`
              printContent1 += `<div style=" width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${z+1} / ${jumlahPrint}` +`</div>`
              printContent1 += `</div>`

              printContent1 += `<div class="" style="text-align: left;width: 20cm;display: flex; flex-direction: row; height: 60px; overflow:hidden">`
              printContent1 += `<div  style=" width:5%; ">` + '' +`</div>`
              printContent1 += `<div  style="width:20%;" >` + 'No PL' +`</div>`
              printContent1 += `<div style=" width:5%; ">`+ ':' +`</div>`
              printContent1 += `<div style="width:70%; ">`+ `${item.listnopicking ? item.listnopicking : ''}` +`</div>`
              printContent1 += `</div>`

              // printContent1 += `<div style="width: 20cm;  margin-top: -3px">`
              // printContent1 += `<hr >`
              // printContent1 += `</div>`


              // printContent1 += `<div class="" style="text-align: left; width: 25%" >`
              // printContent1 += `Penerima:`
              // printContent1 += `</div>`
              printContent1 += `</div>`
              printContent1 += `</div>`

              // printContent1 += `<div style="width: 20cm; margin-top: -5px">`
              // printContent1 += `<hr >`
              // printContent1 += `</div>`

              // printContent1 += `<div style="font-size: 10px; text-align: left; margin-top: -10px">Loc: </div>`

              printContent1 += `</div>`
              z++
            }


            printContent1 += `</div>`
          // }
        });

        document.getElementById("printContainer").innerHTML = printContent1
        w=window.open(' ');
        // w.opener = null;
        // w.refferer = null;
        // w.rel="noopener , noreferrer"

        w.document.write($(`#printContainer`).html());
        // console.log('a')
        w.print();
        // console.log('b')
        w.close();
        // console.log('c')
    //   }
    // })




  }


  function submitEditKoreksi () {
    console.log('submit edit koreksi')
    console.log(koreksiDataEdit)
    let dataRSPB = koreksiDataEdit
    let _token = $("#_token").val();
    let choice = "U"
    let qntTerima = $("#koreksiEditInputQty").val()
    if (Number(qntTerima) > Number(koreksiDataEdit.qntos) + Number(koreksiDataEdit.qnt)) {
      alertify.warning("Qty melebihi Qty OS");
      return
    }
    if(Number(qntTerima) < 0 ) {
      alertify.warning("Qty tidak boleh negatif");
      return
    }
    let qntTerima1 = 0
    let qntTerima2 = 0
    if (dataRSPB.NOSAT == 1) {
      qntTerima1 = qntTerima
      qntTerima2 = qntTerima / dataRSPB.ISI2
    } else if (dataRSPB.NOSAT == 2) {
      qntTerima1 = qntTerima * dataRSPB.ISI2
      qntTerima2 = qntTerima
    }
    let norspb = dataRSPB.nobukti
    let nourut = dataRSPB.NOURUT
    let inputDate = $("#input_koreksi_tanggal").val()
    let nosj = dataRSPB.NoSPB
    let kodecustsupp = dataRSPB.kodecustsupp
    let nopolkend = ""
    let container = ""
    let nocontainer = ""
    let noseal = ""
    let catatan = dataRSPB.Catatan
    let urut = dataRSPB.Urut
    let urutsj = 0
    let kodebrg = dataRSPB.kodebrg
    let sat_1 = dataRSPB.SAT1
    let sat_2 = dataRSPB.SAT2
    let nosat = dataRSPB.NOSAT
    let isi = dataRSPB.ISI
    let netw = 0
    let grossw = 0
    let isempty = 1
    let namabrg = dataRSPB.namabrg
    let flagmenu = 1
    let flagtipe = 1
    let retursupp = 0

    console.log('choice' , choice)
    console.log('norspb' , norspb)
    console.log('nourut', nourut)
    console.log('inputDate' , inputDate)
    console.log('nosj' , nosj)
    console.log('kodecustsupp' , kodecustsupp)
    console.log('nopolkend' , nopolkend)
    console.log('container' , container)
    console.log('nocontainer' , nocontainer)
    console.log('noseal' , noseal)
    console.log('catatan' , catatan)
    console.log('urut' , urut)
    console.log('urutsj' , urutsj)
    console.log('kodebrg' , kodebrg)
    console.log('qntTerima' , qntTerima)
    console.log('qntTerima2' , qntTerima2)
    console.log('sat_1' , sat_1)
    console.log('sat_2' , sat_2)
    console.log('nosat' , nosat)
    console.log('isi' , isi)
    console.log('netw' , netw)
    console.log('grossw' , grossw)
    console.log('isempty' , isempty)
    console.log('namabrg' , namabrg)
    console.log('flagmenu' , flagmenu)
    console.log('flagtipe' , flagtipe)
    console.log('retursupp' , retursupp)

    // return

    $.ajax({
      url: "{!! url('koreksiPersiapanSPB') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        choice,
        norspb,
        nourut,
        inputDate,
        nosj,
        kodecustsupp,
        nopolkend,
        container,
        nocontainer,
        noseal,
        catatan,
        urut,
        urutsj,
        kodebrg,
        qntTerima,
        qntTerima2,
        sat_1,
        sat_2,
        nosat,
        isi,
        netw,
        grossw,
        isempty,
        namabrg,
        flagmenu,
        flagtipe,
        retursupp,
      },
      success: function(res) {
        console.log(res ,'succes edit koreksi')
        refreshKoreksi(norspb)
        alertify.success('Item telah diedit');
        loadAll()
      }
    })




  }

  function buttonDetail (NOBUKTI) {

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailoutstandingpacking') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res)
        addDataArray = res
        console.log('===========================')
        document.getElementById("input_detail_nosj").value = res[0].NOSO
        document.getElementById("input_detail_gdg").value = res[0].kodegdg
        document.getElementById("input_detail_namacust").value = res[0].NamaCustSupp
        // document.getElementById("input_detail_tanggal").value = res[0].Tanggal
        let date = new Date(res[0].Tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#input_detail_tanggal').val(date1)


        let rowTable = ""
        res.forEach((item, i) => {
          let qnt = 0.00
          let qntos = 0.00
          if(item.Qnt) {
            qnt = parseFloat(item.Qnt).toFixed(2)
          }
          if(item.QntOut) {
            qntos = parseFloat(item.QntOut).toFixed(2)
          }
          rowTable += `<tr class="text-left">
          <td>${item.KODEBRG}</td>
          <td>${item.NamaBrgx}</td>
          <td class="text-right">${qnt}</td>
          <td class="text-right">${qntos}</td>
          <td>${item.satuan}</td>
          </tr>`
        });
        document.getElementById("detailTableData").innerHTML = rowTable
        res.forEach((item, i) => {
        //  JsBarcode(`#barcodeDetail${i}`, item.KODEBRG ,{width: 2, height: 25,});
        });
      }
    })




    $("#formDetail").modal('toggle')

  }


  function setNewNoBukti () {
    $.ajax({
      url: "{!! url('getnobuktipacking') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res, 'Nobukti')
        console.log(res[0].Nobukti , res[0].Nourut)
        console.log('===============')
        document.getElementById("input_add_norspb").value = res[0].Nobukti
        document.getElementById("input_add_noUrut").value = res[0].Nourut

      }
    })
  }


  function buttonAdd (NOBUKTI,gdgpl) {
    console.log('buttonadd' , NOBUKTI);
    let akses = $("#akses_istambah").val();

    if (!Number(akses)) {
      alertify.warning('No access')
      return
    }

    let _token = $("#_token").val();

    let checkAdd = 0

    $.ajax({
      url: "{!! url('packingcheckadd') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI,
        gdgpl
      },
      success: function(res) {
        console.log(res)
        checkAdd = res
        console.log(gdgpl)
      }
    })

    if (checkAdd == 1 ) {
      console.log(1)
    } else {
      console.log(0)
      alertify.warning("Gudang " +gdgpl  + " : SO belum lengkap ")
      return
    }





    console.log('button add')
    console.log(NOBUKTI)
    console.log('======!!=======')
    setNewNoBukti()



    $.ajax({
      url: "{!! url('detailoutstandingpacking') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res)
        addDataArray = res
        console.log('===========================')
      }
    })

    document.getElementById("input_add_nosj").value = addDataArray[0].NOSO
    document.getElementById("input_add_gdg").value = addDataArray[0].kodegdg
    document.getElementById("input_add_namacust").value = addDataArray[0].NamaCustSupp
    document.getElementById("input_add_lokasiterima").value = addDataArray[0].ketkebun
    document.getElementById("input_add_alamatkirim").value = addDataArray[0].NamaCustSupp

    let rowTable = ""
    addDataArray.forEach((item, i) => {
      let qnt = 0.00
      let qntos = 0.00
      if(item.Qnt) {
        qnt = parseFloat(item.Qnt).toFixed(2)
      }
      if(item.QntOut) {
        qntos = parseFloat(item.QntOut).toFixed(2)
      }
      rowTable += `<tr class="text-left">
      <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
      <td>${item.KODEBRG}</td>
      <td>${item.NamaBrgx}</td>
      <td class="text-right">${qnt}</td>
      <td>${item.satuan}</td>
      </tr>`
    });

    document.getElementById("addTableData").innerHTML = rowTable
    addDataArray.forEach((item, i) => {
    //  JsBarcode(`#barcodeAdd${i}`, item.KODEBRG ,{width: 2, height: 25,});
    });
    $("#form").modal('toggle')
  }

  function refreshKoreksi (NOBUKTI) {
    console.log('REFRESH START')
    console.log(NOBUKTI)
    $('.showhide').hide();

    let _token = $("#_token").val();
    $.ajax({
      url: "{!! url('detailpersiapanspb') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: NOBUKTI
      },
      success: function(res) {
        console.log(res)
        koreksiPenerimaanArray = res
        koreksiDataEdit = res[0]
        console.log('=============!!==============')
      }
    })

    if (!koreksiPenerimaanArray.length) {
      console.log('item habis')
      $("#formKoreksi").modal('toggle')
      return
    }

    document.getElementById("input_koreksi_norspb").value = koreksiPenerimaanArray[0].nobukti
    document.getElementById("input_koreksi_nosj").value = koreksiPenerimaanArray[0].noso
    document.getElementById("input_koreksi_gdg").value = koreksiPenerimaanArray[0].namagdg
    document.getElementById("input_koreksi_custsupp").value = koreksiPenerimaanArray[0].namacustsupp
    document.getElementById("input_koreksi_jumlahkolli").value = koreksiPenerimaanArray[0].cetakkolli
    let rowTable = ""
    koreksiPenerimaanArray.forEach((item, i) => {
      let qnt = 0.00
      let qntos = 0.00
      if(item.qnt) {
        qnt = parseFloat(item.qnt).toFixed(2)
      }
      if(item.qntos) {
        qntos = parseFloat(item.qntos).toFixed(2)
      }
      rowTable += `<tr class="text-left">

      <td>${item.nopicking}</td>
      <td>${item.kodebrg}</td>
      <td>${item.namabrg}</td>
      <td class="text-right">${qnt}</td>
      <td>${item.satuan}</td>

      <td class="text-center">
      <button class="btn btn-danger btn-sm" type="button" onclick="buttonKoreksiDelete(${i})" ><i class="bi bi-trash"></i></button></td>
      </td>
      </tr>`
    });
    document.getElementById("koreksiTableData").innerHTML = rowTable


  }

  function printBarcode (idBarcode , idJumlah) {
    console.log(idBarcode , idJumlah)
    let printContent = document.getElementById(`${idBarcode}`).innerHTML;
    let jumlahPrint = $(`#${idJumlah}`).val();
    console.log(jumlahPrint)
    let printContent1 = ""
    for (let i=0; i < jumlahPrint ; i++ ) {
      printContent1 += `<div class="row">`
      printContent1 += printContent
      printContent1 += `</div>`
    }
    document.getElementById("printContainer").innerHTML = printContent1
    w=window.open(' ');

    w.document.write($(`#printContainer`).html());
    w.print();
    w.close();
  }

  function loadAll() {
    $.ajax({
      url: "{!! url('loadallpacking') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res)
        dataRefreshOutstanding = res.outstandingArray
        dataRefreshPenerimaan = res.penerimaanArray
        document.getElementById("akses_istambah").value = res.akses.ISTAMBAH
        document.getElementById("akses_iskoreksi").value = res.akses.ISKOREKSI
        document.getElementById("akses_iscetak").value = res.akses.ISCETAK
        document.getElementById("akses_ishapus").value = res.akses.ISHAPUS
      }
    })
    // console.log(dataRefreshOutstanding, 'out')
    // console.log(dataRefreshPenerimaan, 'pen')
    console.log('check1')
    $('#tabel').DataTable().destroy();
    let rowTable = ""
    dataRefreshOutstanding.forEach((item, i) => {
      // console.log(item,'==================')
      let date1 = ""
      if (item[0].Tanggal) {
        let date = new Date(item[0].Tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }
      rowTable  += `<tr>
      <td class="text-center">
      <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('${item[0].NOSO}')" ><i class="bi bi-info-lg"></i></button>
      <button style="width: 30px;" class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item[0].NOSO}')"><i class="bi bi-plus-lg" style="font-size: 16px;"></i></button>
      </td>

      <td>${item[0].NOSO}</td>
      <td>${item[0].NOBUKTI}</td>
      <td>${item[0].gdgpl}</td>
      <td>${date1}</td>
      <td>${item[0].NamaCustSupp}</td>
      <td>${item[0].NamaBrgx}</td>
      <td>${item[0].Nopesanan}</td>
      <td>${item[0].Qnt}</td>
      <td>${item[0].QntOut}</td>

      <td>${item[0].satuan}</td>
      <td>${item[0].catatan}</td>

      <td>${item[0].ketkebun}</td>
      <td>${item[0].alamatlokasi}</td>
      <td>${item[0].duedate}</td>

      <td>${item[0].refpr}</td>
      <td>${item[0].partnumber}</td>
      <td>${item[0].namamerk ? item[0].namamerk : ''}</td>
      <td>${item[0].userid}</td>
      </tr>`


    })

    console.log('check12')
    document.getElementById("tabel_data").innerHTML = rowTable
    console.log('check123')
    $("#tabel").DataTable({
      "lengthChange": false,
      "order": [[1, 'asc']],
        "paging": false ,
         "columnDefs": [
      { "type": "date", "targets": [4] },
      {  "className": "text-center", "targets": [0] },
      // "columns" : [{"width" : "20px"}]
    ]});

    console.log('check2')


    $('#tabelPersiapanspb').DataTable().destroy();
    let rowTableper = ""

    dataRefreshPenerimaan.forEach((item, i) => {
      let date1 = ""
      // console.log(item[0].TANGGAL)
      // return
      if (item[0].tanggal) {
        let date = new Date(item[0].tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }

      rowTableper  += `<tr>
        <td class="text-center">
            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].nobukti}')" ><i class="bi bi-info-lg"></i></button>
            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].nobukti}')"><i class="bi bi-pen"></i></button>
            `
            // if ( Number(item[0].oto1) ) {
            //   rowTableper  += `<button disabled data-toggle="tooltip" data-placement="top" title="Otorisasi" class="btn btn-secondary btn-sm" type="button" onclick="buttonOtorisasi('${item[0].nobukti}'' , '${item[0].oto1}' )"><i class="bi bi-key"></i></button>`
              // rowTableper += `<button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintSPB('${item[0].nobukti}')" ><i class="bi bi-printer"></i></button>`
            // } else {
            //   rowTableper  +=  `<button data-toggle="tooltip" data-placement="top" title="Otorisasi" class="btn btn-primary btn-sm" type="button" onclick="buttonOtorisasi('${item[0].nobukti}' , '${item[0].oto1}' )"><i class="bi bi-key"></i></button>`
            //   rowTableper += `<button disabled style="" class="btn btn-primary btn-sm" type="button"   onclick="" ><i class="bi bi-printer"></i></button>`
            // }

        // console.log(item[0].tgloto1)
      rowTableper  +=  `</td>
        <td class="text-center"><button style="" class="btn btn-info btn-sm" type="button"   onclick="buttonPrintKolli('${item[0].nobukti}')" ><i class="bi bi-printer"></i></button></td>

        <td>${item[0].nobukti}</td>
        <td>${item[0].noso}</td>
        <td>${date1}</td>
        <td>${item[0].kodecustsupp}</td>
        <td>${item[0].namacustsupp}</td>
        <td>${item[0].nopesanan}</td>
        <td>${item[0].Berat}</td>

      </tr>`
    });


    // <td>${item[0].iduser}</td>
    // <td>${item[0].tglkirim ? convertDate(item[0].tglkirim) : '' }</td>
    // <td>${item[0].tglterima ? convertDate(item[0].tglterima) : ''}</td>
    // <td>${Number(item[0].oto1) ? 'Approved' : '' }</td>
    //
    // <td>${item[0].useroto1}</td>
    // <td>${item[0].tgloto1 ? item[0].tgloto1  : '' }</td>
    // <td>${item[0].namakebun}</td>

    document.getElementById("tabelpersiapanspb_data").innerHTML = rowTableper
    console.log('check3')
    $("#tabelPersiapanspb").DataTable({
      "lengthChange": false,
      "order": [[2, 'asc']],
        "paging": false ,
         "columnDefs": [
      { "type": "date", "targets": [4] },
      {  "className": "text-center", "targets": [0,1] },
    ]});
  }

  function convertDate (tanggal) {
    let date1 = ""
    if (tanggal) {
      let date = new Date(tanggal);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
    }
    return date1
  }




</script>




@endsection
