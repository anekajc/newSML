@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Picking List')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">





  <div class="col-6 text-left">
    {{-- <h1>Picking List</h1> - judul pindah ke header (page-title) --}}
  </div>
  <div class="col-6 text-right">
    {{-- tombol dipindah ke bar tab / toolbar tabel --}}
  </div>
  </div>
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <svg id="barcode"></svg> -->
<!-- <button onclick="loadAll()">tes</button>
<button onclick="loadAll1()">tes1</button>
<button onclick="loadAll2()">tes2</button> -->
</div>

<!-- <div class="container"> -->
<div id="loadingContainer" style="display: none" class="container-fluid mt-6 justify-content-middle align-middle">
  <div class="text-center">
    <div class="spinner-border" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
</div>

<div id="printContainer" style="display:none">

</div>

<div id="tempPrintContainer" style="display:none">

</div>

<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body wms-tab-bar">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding SO Reguler</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile3" role="tab" aria-controls="nav-profile3" aria-selected="false">Outstanding SO Booking</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Picking List</a>
            <a class="nav-item nav-link" id="nav-profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="nav-profile2" aria-selected="false">Transaksi Picking List Per periode</a>
      </div>
      <div class="wms-tab-aksi">
        <button type="button" class="btn btn-primary btn-sm " onclick="buttonAdd()"  >Add PL</button>
      </div>
    </div>
  </div>
  <div class="card wms-kartu">
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-14" style="overflow:auto; max-height: 650px">
              <div class="container-fluid">

                    <table id="tabelReguler" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th class="text-center">v</th>
                          <th scope="col">No. Transaksi</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Cust</th>
                          <th scope="col">Nama Barang</th>
                          <th scope="col">PO Cust</th>
                          <th scope="col">No SSP/SPK</th>
                          <th scope="col">Gudang</th>

                          <th scope="col">Qty SO</th>
                          <th scope="col">Qty</th>
                          <th scope="col">Stock</th>
                          <th scope="col">OutPL-SPB</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">Catatan</th>
                          <th scope="col">Lokasi Penerima</th>
                          <th scope="col">Alamat Kirim</th>
                          <th scope="col">Due Date</th>
                          <th scope="col">PR</th>
                          <th scope="col">Part Number</th>
                          <th scope="col">Merk</th>
                          <th scope="col">User ID</th>
                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_dataReguler" class="text-left" >

                      </tbody>


                    </table>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-14" style="overflow:auto; max-height: 650px">
              <div class="container-fluid">

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th class="text-center">v</th>
                          <th scope="col">No. Transaksi</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Cust</th>
                          <th scope="col">Nama Barang</th>
                          <th scope="col">PO Cust</th>
                          <th scope="col">No SSP/SPK</th>
                          <th scope="col">Gudang</th>

                          <th scope="col">Qty SO</th>
                          <th scope="col">Qty</th>
                          <th scope="col">Stock</th>
                          <th scope="col">OutPL-SPB</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">Catatan</th>
                          <th scope="col">Lokasi Penerima</th>
                          <th scope="col">Alamat Kirim</th>
                          <th scope="col">Due Date</th>
                          <th scope="col">PR</th>
                          <th scope="col">Part Number</th>
                          <th scope="col">Merk</th>
                          <th scope="col">User ID</th>
                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >

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

                    <table id="tabelPenerimaan" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Cust Supp</th>
                          <th scope="col">Nomor SO</th>
                          <th scope="col">PO Customer</th>
                          <th scope="col">No SSP/SPK</th>
                          <th scope="col">Alamat Kirim</th>
                          <th scope="col">Lokasi Penerima</th>
                          <th scope="col">Keterangan</th>
                        </tr>
                      </thead>


                      <tbody id="tabelPenerimaan_data" class="text-left" >

                      </tbody>
                    </table>
              </div>
            </div>
          </div>
        </div>

        <!-- sheet transaksi per periode -->
        <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile2-tab">
          <div class="row">
            <div class="col-14" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabelPenerimaanperiod" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Nama Cust Supp</th>
                          <th scope="col">Nomor SO</th>
                          <th scope="col">PO Customer</th>
                           <th scope="col">No SSP/SPK</th>
                          <th scope="col">Alamat Kirim</th>
                          <th scope="col">Lokasi Penerima</th>
                          <th scope="col">Keterangan</th>
                        </tr>
                      </thead>


                      <tbody id="tabelpenerimaanperiod_data" class="text-left" >

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



<!-- <div> -->


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1200px;" role="document">
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
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
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
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_keterangan" placeholder="Keterangan" >
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

              <table id="addTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">No. Transaksi</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Gudang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
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
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                </tr>

                </tbody>


              </table>
        </div>



      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" id="submitAdd" onclick="submitAdd()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_edit_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" disabled class="form-control" id="input_edit_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" disabled class="form-control" id="input_edit_keterangan" >
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

              <table id="editTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">No. Trans</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Gudang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Isi</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>


                <tbody id="editTableData" class="text-right" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal add -->
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" disabled class="form-control" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" disabled class="form-control" id="input_detail_keterangan" >
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
                    <th scope="col">No. Trans</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Gudang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Isi</th>
                  </tr>
                </thead>


                <tbody id="detailTableData" class="text-right" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->





<!-- start modal add period -->
<div class="modal fade" id="formDetailperiod" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detailperiod_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" disabled class="form-control" id="input_detailperiod_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" disabled class="form-control" id="input_detailperiod_keterangan" >
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

              <table id="detailTableperiod" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">No. Trans</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Gudang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Isi</th>
                  </tr>
                </thead>


                <tbody id="detailTableDataperiod" class="text-right" >
                  <tr >

                      <td class="text-center"><input class="" type="checkbox" value="" id="flexCheckDefault"></td>
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
        <!-- <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add period-->









<!-- start modal add -->
<div class="modal fade" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="width: 90%; max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Print</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_print_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" disabled class="form-control" id="input_print_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" disabled class="form-control" id="input_print_keterangan" >
              </div>
            </div>








          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>
        <div class="container-fluid" style="overflow-x: auto; ">

              <table id="printTable" class="table table-bordered table-striped"  >
                <thead class="text-center">
                  <tr>
                    <th scope="col">v</th>
                    <th scope="col">Kode Barang</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Gudang</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <!-- <th scope="col">Barcode</th> -->
                  </tr>
                </thead>


                <tbody id="printTableData" class="text-right" >
                  <tr >

                </tr>

                </tbody>


              </table>
        </div>
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 text-right">
              <label>Jumlah Print</label>
              <input type="number" value=1 style="max-width:50px" class="text-right" id="input_print_jumlahprint" >
            </div>
            <!-- <div class="col-1">

            </div> -->

          </div>

        </div>



      <div class="modal-footer mt-4">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>

        <button type="button" class="btn btn-success" onclick="submitPreview()">Preview</button>
        <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->



@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">
// loadAll
let dataRefreshOutstanding = []
let dataRefreshOutstandingReguler = []
let dataRefreshPenerimaan = []
let dataRefreshPenerimaanperiod = []

let tipeso = ''

// data add
let addDataArray = []
let addDataArrayReguler = []
let timestamp = ''
//data koreksi
// let dataSPR = {}
// let koreksiDataArray = []
// let koreksiDataAddList = []
// let koreksiDataEdit = {}

let dataEditPenerimaan = []
let dataEditPenerimaanperiod = []

$(document).ready(function(){

      $("#tabel").DataTable({ "lengthChange": false,
      'order': [[1, 'asc']],
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] } , {"targets" :[0] , 'orderable' : false}
        // {  "className": "text-center", "targets": [4] },
      ]});

      $("#tabelReguler").DataTable({ "lengthChange": false,
      'order': [[1, 'asc']],
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] } , {"targets" :[0] , 'orderable' : false}
        // {  "className": "text-center", "targets": [4] },
      ]});


      $("#tabelPenerimaan").DataTable({ "lengthChange": false,
      'order': [[1, 'asc']],
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] ,"orderable": false },
      ]});


      $("#tabelPenerimaanperiod").DataTable({ "lengthChange": false,
      'order': [[1, 'asc']],
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0] ,"orderable": false },
      ]});



    //
    // $("#tabelRetur").DataTable({ "lengthChange": false, "paging": false , "columnDefs": [
    //   { "type": "date", "targets": [1] },
    //   {  "className": "text-center", "targets": [4] },
    // ]});
//     $('#tabel').on('xhr.dt', function ( e, settings, json, xhr ) {
//         //the new data is here json.data
//         console.log('1')
//         console.log(json.data);
// });

    console.log('ready function')
    loadAll()


});

function buttonDetailPenerimaan (nobukti) {
  console.log(nobukti)
  // newpermintaanpersiapandetailpenerimaan

  $.ajax({
    url: "{!! url('newpermintaanpersiapandetailpenerimaan') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataEditPenerimaan = res
    }
  })

  document.getElementById("input_detail_nobukti").value = dataEditPenerimaan[0].NOBUKTI
  document.getElementById("input_detail_keterangan").value = dataEditPenerimaan[0].KETERANGAN

  // document.getElementById("input_detail_noUrut").value = res[0].Nourut

  let date = new Date(dataEditPenerimaan[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_detail_tanggal').val(date1)


  let rowTable = ``
  dataEditPenerimaan.forEach((item, i) => {
    rowTable += `
    <tr class="text-left">
    <td>${item.NoBeli}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.KodeGdg}</td>
    <td class="text-right">${item.QNT}</td>
    <td>${item.SATUAN}</td>
    <td class="text-right">${parseFloat(item.ISI).toFixed(2)}</td>
    </tr>
    `
  });

  document.getElementById("detailTableData").innerHTML = rowTable

  $("#formDetail").modal('toggle')

}




function buttonDetailPenerimaanperiod (nobukti) {
  console.log(nobukti)
  // newpermintaanpersiapandetailpenerimaan

  $.ajax({
    url: "{!! url('newpermintaanpersiapandetailpenerimaanperiod') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataEditPenerimaanperiod = res
    }
  })

   document.getElementById("input_detailperiod_nobukti").value = dataEditPenerimaanperiod[0].NOBUKTI
   document.getElementById("input_detailperiod_keterangan").value = dataEditPenerimaanperiod[0].KETERANGAN

  // document.getElementById("input_detailperiod_noUrut").value = res[0].Nourut

  let date = new Date(dataEditPenerimaanperiod[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_detailperiod_tanggal').val(date1)


  let rowTableperiod = ``
  dataEditPenerimaanperiod.forEach((item, i) => {
    rowTableperiod += `
    <tr class="text-left">
    <td>${item.NoBeli}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.KodeGdg}</td>
    <td class="text-right">${item.QNT}</td>
    <td>${item.SATUAN}</td>
    <td class="text-right">${parseFloat(item.ISI).toFixed(2)}</td>
    </tr>
    `
  });

  document.getElementById("detailTableDataperiod").innerHTML = rowTableperiod

  $("#formDetailperiod").modal('toggle')

}






function refreshEditPenerimaan (nobukti) {
  $.ajax({
    url: "{!! url('newpermintaanpersiapandetailpenerimaan') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataEditPenerimaan = res
    }
  })


  if (!dataEditPenerimaan.length) {
    $("#formEdit").modal('toggle')
    return
  }

  document.getElementById("input_edit_nobukti").value = dataEditPenerimaan[0].NOBUKTI
  document.getElementById("input_edit_keterangan").value = dataEditPenerimaan[0].KETERANGAN

  // document.getElementById("input_edit_noUrut").value = res[0].Nourut

  let date = new Date(dataEditPenerimaan[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_edit_tanggal').val(date1)


  let rowTable = ``
  dataEditPenerimaan.forEach((item, i) => {
    rowTable += `
    <tr class="text-left">
    <td>${item.NoBeli}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.KodeGdg}</td>
    <td class="text-right">${item.QNT}</td>
    <td>${item.SATUAN}</td>
    <td class="text-right">${parseFloat(item.ISI).toFixed(2)}</td>
    <td class="text-center">
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonPenerimaanDelete('${item.NOBUKTI}' , '${item.URUT}')" ><i class="bi bi-trash"></i></button>
    </td>
    </tr>
    `
  });

  document.getElementById("editTableData").innerHTML = rowTable
}

function buttonEditPenerimaan (nobukti) {
  console.log(nobukti)
  // newpermintaanpersiapandetailpenerimaan

  $.ajax({
    url: "{!! url('newpermintaanpersiapandetailpenerimaan') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataEditPenerimaan = res
    }
  })
  document.getElementById("input_edit_nobukti").value = dataEditPenerimaan[0].NOBUKTI
  document.getElementById("input_edit_keterangan").value = dataEditPenerimaan[0].KETERANGAN

  // document.getElementById("input_edit_noUrut").value = res[0].Nourut

  let date = new Date(dataEditPenerimaan[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_edit_tanggal').val(date1)


  let rowTable = ``
  dataEditPenerimaan.forEach((item, i) => {
    rowTable += `
    <tr class="text-left">
    <td>${item.NoBeli}</td>
    <td>${item.KODEBRG}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.KodeGdg}</td>
    <td class="text-right">${item.QNT}</td>
    <td>${item.SATUAN}</td>
    <td class="text-right">${parseFloat(item.ISI).toFixed(2)}</td>
    <td class="text-center">
    <button class="btn btn-danger btn-sm" type="button" onclick="buttonPenerimaanDelete('${item.NOBUKTI}' , '${item.URUT}')" ><i class="bi bi-trash"></i></button>
    </td>
    </tr>
    `
  });

  document.getElementById("editTableData").innerHTML = rowTable

  $("#formEdit").modal('toggle')

}


function submitPreview () {

  // let tempPrintData = []
  //
  // for (let i = 0; i < tempDetailData.length; i += 7) {
  //   let tempArrayData = tempDetailData.slice(i,i+7)
  //   tempPrintData.push(tempArrayData)
  // }

  let printContent1 = ""
  let jumlahPrint = $(`#input_print_jumlahprint`).val();
  let z = 0
  dataEditPenerimaan.forEach((item, i) => {
    if (document.getElementById(`printChecklist${i}`).checked) {
      // qrcodeprint
      let date = new Date(item.TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      let printContent = document.getElementById(`tempqrcodeprintpreview${i}`).innerHTML;
      console.log(printContent , 'printConcent')
      printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`
      for (let i=0; i < jumlahPrint ; i++ ) {

        if (z == 0) {
          printContent1 += `<div style="break-inside: avoid; width:15.5cm; height: 8cm; overflow: hidden">`
          printContent1 += `<div  style="display: flex; flex-direction: row;  margin-top: 1px">`
        } else {
          printContent1 += `<div style="break-inside: avoid; width:15.5cm; height: 8cm;  overflow: hidden;">`
          printContent1 += `<div  style="display: flex; flex-direction: row; margin-top: 10px">`
        }

        printContent1 += `<div style="display: flex; flex-direction: column; width: 15.5cm; text-align: left">`
        printContent1 += `<div style="font-weight: bold;font-size: 22px">NoBukti: ${item.NOBUKTI}</div><div style="font-weight: bold;font-size: 24px">Tgl: ${date1} </div><div style="font-weight: bold;font-size: 28px">PO: ${item.NoPesanan ? item.NoPesanan : '' } </div><div style="font-weight: bold;font-size: 20px">Site/Kebun: ${item.KODEKEBUN ? item.KODEKEBUN : '' } </div>`
        printContent1 += `</div>`
        printContent1 += `<div style="padding-right: 16px" >`
        printContent1 += printContent
        printContent1 += `</div>`
        printContent1 += `</div>`

        printContent1 += `<div style="width: 15.5cm;  margin-top: -3px">`
        printContent1 += `<hr >`
        printContent1 += `</div>`

        printContent1 += `<div  style="display: flex; flex-direction: column; width: 15.5cm;font-size: 28px; margin-top: -10px ">`
        printContent1 += `<div  style="display: flex; flex-direction: row;">`
        printContent1 += `<div  style="display: flex; flex-direction: column; text-align: left; ">`
        printContent1 += `<div style="font-weight: bold;font-size: 19px;">Ship to: ${item.namacustsupp ? item.namacustsupp : ''}</div>`
        printContent1 += `</div>`
        // printContent1 += `<div class="" style="text-align: left; width: 25%" >`
        // printContent1 += `Penerima:`
        // printContent1 += `</div>`
        printContent1 += `</div>`
        printContent1 += `</div>`

        printContent1 += `<div style="width: 15.5cm; margin-top: -5px">`
        printContent1 += `<hr >`
        printContent1 += `</div>`

        // printContent1 += `<div style="font-size: 10px; text-align: left; margin-top: -10px">Loc: </div>`
        printContent1 += `<div style="font-weight: bold;font-size: 22px; text-align: left">KodeBrg: ${item.KODEBRG} (${item.PartNumber ? item.PartNumber : ''})</div>`
        printContent1 += `<div style="font-weight: bold;font-size: 22px; text-align: left; padding-right: 8px ">NamaBrg: ${item.NamaBrg} ${ item.NAMAMERK ? ' / ' + item.NAMAMERK : '' } </div>`
        printContent1 += `<div style="font-weight: bold;font-size: 22px; text-align: left">Qty: ${item.QNT} @ ${item.SATUAN}</div>`
        printContent1 += `<div style="font-weight: bold;font-size: 22px; text-align: left">Gudang: ${item.KodeGdg} </div>`
        // SHAFT MOUNTED HELICAL GEAR RATIO 25/1 C/W TORQUE ARM,BORE SIZE: 120 MM
        // printContent1 += `<div class="row" style="margin-bottom: 20px"><div>${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}</div></div>`

        printContent1 += `</div>`
        z++
      }


      printContent1 += `</div>`
    }
  });

  document.getElementById("printContainer").innerHTML = printContent1
  w=window.open(' ');
  // w.opener = null;
  // w.refferer = null;
  // w.rel="noopener , noreferrer"

  w.document.write($(`#printContainer`).html());


}

function submitPrint () {

  // let tempPrintData = []
  //
  // for (let i = 0; i < tempDetailData.length; i += 7) {
  //   let tempArrayData = tempDetailData.slice(i,i+7)
  //   tempPrintData.push(tempArrayData)
  // }

  let printContent1 = ""
  let jumlahPrint = $(`#input_print_jumlahprint`).val();
  let z = 0
  dataEditPenerimaan.forEach((item, i) => {
    if (document.getElementById(`printChecklist${i}`).checked) {
      // qrcodeprint
      let date = new Date(item.TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;
      console.log(printContent , 'printConcent')
      printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`
      for (let i=0; i < jumlahPrint ; i++ ) {

        if (z == 0) {
          printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm; overflow: hidden">`
          printContent1 += `<div  style="display: flex; flex-direction: row;  margin-top: 1px">`
        } else {
          printContent1 += `<div style="break-inside: avoid; width:7.5cm; height: 4.5cm;  overflow: hidden;">`
          printContent1 += `<div  style="display: flex; flex-direction: row; margin-top: 10px">`
        }

        printContent1 += `<div style="display: flex; flex-direction: column; width: 7.5cm; text-align: left">`
        printContent1 += `<div style="font-weight: bold;font-size: 12px">NoBukti: ${item.NOBUKTI}</div><div style="font-weight: bold;font-size: 12px">Tgl: ${date1} </div><div style="font-weight: bold;font-size: 14px">PO: ${item.NoPesanan ? item.NoPesanan : '' } </div><div style="font-weight: bold;font-size: 12px">Site/Kebun: ${item.KODEKEBUN ? item.KODEKEBUN : '' } </div>`
        printContent1 += `</div>`
        printContent1 += `<div style="padding-right: 16px" >`
        printContent1 += printContent
        printContent1 += `</div>`
        printContent1 += `</div>`

        printContent1 += `<div style="width: 7.5cm;  margin-top: -3px">`
        printContent1 += `<hr >`
        printContent1 += `</div>`

        printContent1 += `<div  style="display: flex; flex-direction: column; width: 7.5cm;font-size: 14px; margin-top: -10px ">`
        printContent1 += `<div  style="display: flex; flex-direction: row;">`
        printContent1 += `<div  style="display: flex; flex-direction: column; text-align: left; ">`
        printContent1 += `<div style="font-weight: bold;font-size: 11px;">Ship to: ${item.namacustsupp ? item.namacustsupp : ''}</div>`
        printContent1 += `</div>`
        // printContent1 += `<div class="" style="text-align: left; width: 25%" >`
        // printContent1 += `Penerima:`
        // printContent1 += `</div>`
        printContent1 += `</div>`
        printContent1 += `</div>`

        printContent1 += `<div style="width: 7.5cm; margin-top: -5px">`
        printContent1 += `<hr >`
        printContent1 += `</div>`

        // printContent1 += `<div style="font-size: 10px; text-align: left; margin-top: -10px">Loc: </div>`
        printContent1 += `<div style="font-weight: bold;font-size: 11px; text-align: left">KodeBrg: ${item.KODEBRG} (${item.PartNumber ? item.PartNumber : ''})</div>`
        printContent1 += `<div style="font-weight: bold;font-size: 11px; text-align: left; padding-right: 8px ">NamaBrg: ${item.NamaBrg} ${ item.NAMAMERK ? ' / ' + item.NAMAMERK : '' } </div>`
        printContent1 += `<div style="font-weight: bold;font-size: 11px; text-align: left">Qty: ${item.QNT} @ ${item.SATUAN}</div>`
        printContent1 += `<div style="font-weight: bold;font-size: 11px; text-align: left">Gudang: ${item.KodeGdg} </div>`
        // SHAFT MOUNTED HELICAL GEAR RATIO 25/1 C/W TORQUE ARM,BORE SIZE: 120 MM
        // printContent1 += `<div class="row" style="margin-bottom: 20px"><div>${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}</div></div>`

        printContent1 += `</div>`
        z++
      }


      printContent1 += `</div>`
    }
  });

  document.getElementById("printContainer").innerHTML = printContent1
  w=window.open(' ');
  // w.opener = null;
  // w.refferer = null;
  // w.rel="noopener , noreferrer"

  w.document.write($(`#printContainer`).html());
  w.print();
  w.close();


}

function buttonPrintPenerimaan (nobukti) {


  let akses = $("#akses_iscetak").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $.ajax({
    url: "{!! url('newpermintaanpersiapandetailpenerimaan') !!}",
    type: "get",
    async: false,
    data: {
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataEditPenerimaan = res
    }
  })
  document.getElementById("input_print_nobukti").value = dataEditPenerimaan[0].NOBUKTI
  document.getElementById("input_print_keterangan").value = dataEditPenerimaan[0].KETERANGAN


  let date = new Date(dataEditPenerimaan[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_print_tanggal').val(date1)


  let rowTable = ``


  let rowTableTemp = ``
  dataEditPenerimaan.forEach((item, i) => {
    rowTable += `
    <tr>
    <td>
    <div class="form-check text-center">
        <input id="printChecklist${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        </div></td>
    <td>${item.KODEBRG}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.KodeGdg}</td>
    <td>${item.QNT}</td>
    <td>${item.SATUAN}</td>

    </tr>
    `
    rowTableTemp += `
    <div id="tempqrcodeprint${i}" style="width: 10px; height: 10px"></div>
    <div id="tempqrcodeprintpreview${i}" style="width: 10px; height: 10px"></div>
    `
  });

  // <td class="d-flex justify-content-center"><div id="qrcodeprint${i}"  style="width: 60px; height: 60px"></div></td>

  document.getElementById("printTableData").innerHTML = rowTable
  document.getElementById("tempPrintContainer").innerHTML = rowTableTemp

  dataEditPenerimaan.forEach((item, i) => {
    // new QRCode(document.getElementById(`qrcodeprint${i}`), `${item.NOBUKTI}${item.KODEBRG}`);
    new QRCode(document.getElementById(`tempqrcodeprint${i}`), {text: `${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}` , width: 70, height: 70});

    new QRCode(document.getElementById(`tempqrcodeprintpreview${i}`), {text: `${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}` , width: 120, height: 120});

  });




  $("#formPrint").modal('toggle')
}

function buttonPenerimaanDelete (nobukti , urut) {
  console.log(nobukti , urut)
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item' + ' ?',
      function() {
        let _token = $("#_token").val();

        let choice = "D"



        $.ajax({
          url: "{!! url('newpermintaanpersiapanspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            choice,
            nobukti,
            urut
          },
          success: function(res) {

            loadAll()
            addDataArray = []
            refreshEditPenerimaan(nobukti)
            alertify.success('Berhasil delete');
          }
        })


      }
    ,function(){
      console.log('no')
    });
}


function outstandingCheckListReguler ( index ,id) {
  // console.log(index, data,id)
  let data = dataRefreshOutstandingReguler[index]
  console.log(data)

  if (addDataArray.length) {
    alertify.warning('Pilih Reguler / Booking')
    document.getElementById(`outstandingCheckListReguler${index}`).checked = !document.getElementById(`outstandingCheckListReguler${index}`).checked

    return
  }
  if (document.getElementById(`outstandingCheckListReguler${index}`).checked) {
    console.log('add baru')
    // kalo tidak ada data di adddataarray langsung add , kalo ada data cek namacustsupp
    if (!addDataArrayReguler.length) {

      addDataArrayReguler.push(data)
      console.log(addDataArrayReguler)
      tipeso = 'reguler'
      return
    }
    if (addDataArrayReguler[0].AlamatLokasi != data.AlamatLokasi) {
      alertify.warning('Alamat berbeda')
      document.getElementById(`outstandingCheckListReguler${index}`).checked = !document.getElementById(`outstandingCheckListReguler${index}`).checked
      console.log(addDataArrayReguler)
      return
    }
    if (addDataArrayReguler[0].NoBukti != data.NoBukti) {
      alertify.warning('No SO berbeda')
      document.getElementById(`outstandingCheckListReguler${index}`).checked = !document.getElementById(`outstandingCheckListReguler${index}`).checked
      console.log(addDataArrayReguler)
      return
    }
    if (addDataArrayReguler[0].namacustsupp != data.namacustsupp) {
      alertify.warning('Cust Supp berbeda')
      document.getElementById(`outstandingCheckListReguler${index}`).checked = !document.getElementById(`outstandingCheckListReguler${index}`).checked
      console.log(addDataArrayReguler)
      return
    }
    addDataArrayReguler.push(data)
    tipeso = 'reguler'


    // console.log(document.getElementById(`outstandingCheckList${index}`).checked)
    // console.log(!document.getElementById(`outstandingCheckList${index}`).checked)
    // document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
  } else {
    let check = addDataArrayReguler.findIndex(el => el.Kodebrg === data.Kodebrg && el.NoBukti === data.NoBukti && el.Urut === data.Urut);
    console.log('hapus')
    addDataArrayReguler.splice(check, 1)
  }
  tipeso = 'reguler'
  console.log(tipeso)
  console.log(addDataArrayReguler)

  // myArray.findIndex(x => x.id === '45');

  // console.log(document.getElementById(id).checked)
  //
  // addDataArray.push(data)
  // console.log(addDataArray)
}

function outstandingCheckList ( index ,id) {
  // console.log(index, data,id)
  let data = dataRefreshOutstanding[index]
  console.log(data)
  if (document.getElementById(`outstandingCheckList${index}`).checked) {
    console.log('add baru')
    // kalo tidak ada data di adddataarray langsung add , kalo ada data cek namacustsupp
    if (addDataArrayReguler.length) {
      alertify.warning('Pilih Reguler / Booking')
      document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked

      return
    }
    if (!addDataArray.length) {

      addDataArray.push(data)
      console.log(addDataArray)
      tipeso = 'booking'
      return
    }
    if (addDataArray[0].AlamatLokasi != data.AlamatLokasi) {
      alertify.warning('Alamat berbeda')
      document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
      console.log(addDataArray)
      return
    }
    if (addDataArray[0].NoBukti != data.NoBukti) {
      alertify.warning('Nobukti berbeda')
      document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
      console.log(addDataArray)
      return
    }
    if (addDataArray[0].namacustsupp != data.namacustsupp) {
      alertify.warning('Cust Supp berbeda')
      document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
      console.log(addDataArray)
      return
    }
    addDataArray.push(data)
    tipeso = 'booking'


    // console.log(document.getElementById(`outstandingCheckList${index}`).checked)
    // console.log(!document.getElementById(`outstandingCheckList${index}`).checked)
    // document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
  } else {
    let check = addDataArray.findIndex(el => el.Kodebrg === data.Kodebrg && el.NoBukti === data.NoBukti && el.Urut === data.Urut);
    console.log('hapus')
    addDataArray.splice(check, 1)
  }
  tipeso = 'booking'
  console.log(tipeso)
  console.log(addDataArray)

  // myArray.findIndex(x => x.id === '45');

  // console.log(document.getElementById(id).checked)
  //
  // addDataArray.push(data)
  // console.log(addDataArray)
}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('newpermintaanpersiapangetnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_noUrut").value = res[0].Nourut
    }
  })
}

function buttonAdd () {
  // $("#submitAdd").attr("disabled", false);

  timestamp = new Date().toLocaleString()
  console.log(timestamp)


  // Dinonaktifkan 2026-10-21: tombol Add PL memeriksa hak hapus, seharusnya hak tambah.
  // let akses = $("#akses_ishapus").val();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let dataTemp = []
  console.log(addDataArray)
  console.log(addDataArrayReguler)
  console.log(tipeso)
  if (tipeso == 'reguler') {
    dataTemp = addDataArrayReguler
  } else {
    dataTemp = addDataArray
  }
  console.log(dataTemp)
  if (!dataTemp.length) {
    alertify.warning('Pilih item terlebih dahulu');
    return
  }

  setNewNoBukti()


  let rowTable = ''
  dataTemp.forEach((item, i) => {
    // console.log(item)
    let date1 = ""
    if (item.TANGGAL) {
      let date = new Date(item.TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
    }
    rowTable += `
    <tr>
    <td>${item.NoBukti}</td>
    <td>${date1}</td>
    <td>${item.Kodebrg}</td>
    <td>${item.namabrg}</td>
    <td>${item.kodegdg}</td>
    <td>${Number(item.QntXz).toFixed(2)}</td>
    <td>${item.satuan}</td>
    </tr>
    `
  });



  document.getElementById(`addTableData`).innerHTML = rowTable



  $("#form").modal('toggle')
}

function submitAdd () {

  // $("#submitAdd").attr("disabled", true);

  console.log('Submit Add')
  let _token = $("#_token").val();
  let choice = "I"
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_noUrut").val();
  let keterangan = $("#input_add_keterangan").val();

  let tanggal = $("#input_add_tanggal").val();

  let date = new Date();
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  let tanggalInput = date.getFullYear()+"-"+(month)+"-"+(day);

  console.log(nobukti, nourut)
  console.log(keterangan , tanggal)

  console.log(addDataArray)
  console.log(tanggalInput)

  let checkDate = new Date(tanggal)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  // let arrayCheck = []
  //
  // if (tipeso == 'reguler') {
  //   arrayCheck = addDataArrayReguler
  // } else {
  //   arrayCheck = addDataArray
  // }
  //
  // let arrayCheckSum = []
  //
  // arrayCheck.forEach((item, i) => {
  //   // if (!dataPrint.length) {
  //   //   dataPrint.push(item)
  //   // } else {
  //   //   let tempVar = dataPrint.findIndex((x) => x.kodebrg == item.kodebrg && x.NOSAT == item.NOSAT )
  //   //   // console.log(tempVar)
  //   //   if(tempVar >= 0) {
  //   //     dataPrint[tempVar].qntcetak = Number(dataPrint[tempVar].qntcetak) + Number(item.qntcetak)
  //   //
  //   //   } else {
  //   //     dataPrint.push(item)
  //   //   }
  //   // }
  //
  //   if (!arrayCheckSum.length) {
  //
  //
  //   }
  //
  //
  // });




  console.log(tanggal, tanggalInput)
  // return

  if (tipeso == 'reguler') {

    $.ajax({
      url: "{!! url('newpermintaanpersiapanspcheckreguler') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        keterangan,
        tanggal,
        tanggalInput,
        tempData: addDataArrayReguler,
        idToken: timestamp
      },
      success: function(res) {
        console.log('res check reguler', res)


        if (res.check ==  1) {

          console.log('masuk 1' , nobukti, '-')
          console.log(addDataArrayReguler)
          $.ajax({
            url: "{!! url('newpermintaanpersiapanspadd') !!}",
            type: "post",
            async: false,
            data: {
              _token,
              choice,
              nobukti,
              nourut,
              keterangan,
              tanggal,
              tanggalInput,
              tempData: addDataArrayReguler,
              idToken: timestamp
            },
            success: function(res) {
              console.log(res)
              if (res == 1) {
                loadAll()
                alertify.success('Berhasil');
                addDataArray = []
                addDataArrayReguler = []
                $("#form").modal('toggle')

              }
              if (res == 2) {
                setNewNoBukti()
                alertify.warning('Nobukti telah di refresh, silahkan submit ulang')
              }
              if (res == 3) {
                // setNewNoBukti()
                alertify.warning('Picking List lebih besar dari sisa SO')
              }
              if (res == 4) {
                // setNewNoBukti()
                alertify.warning('Stok SPK/SSP tidak mencukupi')
              }
              if (res == 8) {
                // setNewNoBukti()
                alertify.warning('Stok Gudang tidak mencukupi')
              }
              if (res == 9) {
                // setNewNoBukti()
                alertify.warning('Melebihi jumlah SO')
              }
            },
            error: function (err) {
              console.log(err)
              alertify.warning('Terjadi kesalahan silahkan refresh browser')
            }
          })



        } else {
          alertify.confirm(`Item sudah terbooking di Nobukti: ${res.nobukti}`, 'Apakah tetap membuat Picking' + ' ?',
              function() {
                $.ajax({
                  url: "{!! url('newpermintaanpersiapanspadd') !!}",
                  type: "post",
                  async: false,
                  data: {
                    _token,
                    choice,
                    nobukti,
                    nourut,
                    keterangan,
                    tanggal,
                    tanggalInput,
                    tempData: addDataArrayReguler,
                    idToken: timestamp
                  },
                  success: function(res) {
                    console.log(res)
                    if (res == 1) {
                      loadAll()
                      alertify.success('Berhasil');
                      addDataArray = []
                      addDataArrayReguler = []
                      $("#form").modal('toggle')

                    }
                    if (res == 2) {
                      setNewNoBukti()
                      alertify.warning('Nobukti telah di refresh, silahkan submit ulang')
                    }
                    if (res == 3) {
                      // setNewNoBukti()
                      alertify.warning('Picking List lebih besar dari sisa SO')
                    }
                    if (res == 4) {
                      // setNewNoBukti()
                      alertify.warning('Stok SPK/SSP tidak mencukupi')
                    }
                    if (res == 8) {
                      // setNewNoBukti()
                      alertify.warning('Stok Gudang tidak mencukupi')
                    }
                    if (res == 9) {
                      // setNewNoBukti()
                      alertify.warning('Melebihi jumlah SO')
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

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

  } else {


    $.ajax({
      url: "{!! url('newpermintaanpersiapanspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        nobukti,
        nourut,
        keterangan,
        tanggal,
        tanggalInput,
        tempData: addDataArray,
        idToken: timestamp
      },
      success: function(res) {
        console.log(res)
        if (res == 1) {
          loadAll()
          alertify.success('Berhasil');
          addDataArray = []
          addDataArrayReguler = []
          $("#form").modal('toggle')

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang')
        }
        if (res == 3) {
          // setNewNoBukti()
          alertify.warning('Picking List lebih besar dari sisa SO')
        }
        if (res == 4) {
          // setNewNoBukti()
          alertify.warning('Stok SPK/SSP tidak mencukupi')
        }

        if (res == 8) {
          // setNewNoBukti()
          alertify.warning('Stok Gudang tidak mencukupi')
        }
        if (res == 9) {
          // setNewNoBukti()
          alertify.warning('Melebihi jumlah SO')
        }
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

  }

}




// Ditambah 2026-10-21: dulu satu permintaan sinkron (async:false) mengambil SP outstanding (±8 dtk),
// daftar Picking List (±3 dtk) dan transaksi per periode (±2 dtk) berurutan, layar membeku ±15 dtk.
// Sekarang ketiganya diambil bersamaan (tidak membekukan layar), lalu tabel digambar dengan kode lama.
let loadAllUrut = 0
// Diubah 2026-10-21: tanda memuat memakai animasi bersama gaya newSML (wmsMemuat di wms-tampilan-baru.js).
// function loadAllTandaMemuat (tampil) {
//   let el = document.getElementById('plMemuat')
//   if (!el) {
//     const kotak = document.querySelector('.wms-tab-aksi')
//     if (!kotak) return
//     el = document.createElement('span')
//     el.id = 'plMemuat'
//     el.className = 'text-muted mr-2'
//     el.style.fontSize = '13px'
//     el.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status"></span>Memuat data...'
//     kotak.prepend(el)
//   }
//   el.style.display = tampil ? '' : 'none'
// }
function loadAllTandaMemuat (tampil) {
  if (window.wmsMemuat) wmsMemuat(tampil)
}
function loadAll () {
  addDataArray = []
  const urut = ++loadAllUrut
  const ambil = bagian => $.ajax({ url: "{!! url('newpermintaanpersiapanloadall') !!}", type: "get", data: { bagian } })
  loadAllTandaMemuat(true)
  $.when(ambil('outstanding'), ambil('penerimaan'), ambil('periode')).done(function (r1, r2, r3) {
    if (urut !== loadAllUrut) return
    const res = {
      outstanding: r1[0].outstanding,
      outstandingReguler: r1[0].outstandingReguler,
      penerimaan: r2[0].penerimaan,
      penerimaanperiod: r3[0].penerimaanperiod,
      akses: r1[0].akses
    }
    dataRefreshOutstanding = res.outstanding
    dataRefreshOutstandingReguler = res.outstandingReguler
    dataRefreshPenerimaan = res.penerimaan
    dataRefreshPenerimaanperiod = res.penerimaanperiod
    document.getElementById("akses_istambah").value = res.akses.ISTAMBAH
    document.getElementById("akses_iskoreksi").value = res.akses.ISKOREKSI
    document.getElementById("akses_iscetak").value = res.akses.ISCETAK
    document.getElementById("akses_ishapus").value = res.akses.ISHAPUS
    loadAllTampil()
    loadAllTandaMemuat(false)
  }).fail(function () {
    if (urut !== loadAllUrut) return
    loadAllTandaMemuat(false)
    alertify.warning('Gagal memuat data, silahkan refresh browser')
  })
}

// Dinonaktifkan 2026-10-21: pengambilan data sinkron, diganti loadAll() paralel di atas.
// Isi tampilan tabel di bawah tidak diubah, hanya dipindah ke loadAllTampil().
// function loadAll () {
//   addDataArray = []
//   $.ajax({
//     url: "{!! url('newpermintaanpersiapanloadall') !!}",
//     type: "get",
//     async: false,
//     data: {
//     },
//     success: function(res) {
//       console.log(res , ' !!!!!!!!!!!!!!!!!!!!!!!!! ')
//       dataRefreshOutstanding = res.outstanding
//       dataRefreshOutstandingReguler = res.outstandingReguler
//       dataRefreshPenerimaan = res.penerimaan
//       dataRefreshPenerimaanperiod = res.penerimaanperiod
//       document.getElementById("akses_istambah").value = res.akses.ISTAMBAH
//       document.getElementById("akses_iskoreksi").value = res.akses.ISKOREKSI
//       document.getElementById("akses_iscetak").value = res.akses.ISCETAK
//       document.getElementById("akses_ishapus").value = res.akses.ISHAPUS
//     }
//   })
function loadAllTampil () {

  let rowTableReguler = ``
  dataRefreshOutstandingReguler.forEach((item, i) => {

    let date1 = ""
    if (item.TANGGAL) {
      let date = new Date(item.TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
    }

    let date2 = ""
    if (item.DUEDATE) {
      let date = new Date(item.DUEDATE);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date2 = date.getFullYear()+"/"+(month)+"/"+(day) ;
    }
    rowTableReguler += `
    <tr>
    <td><div class="form-check text-center">
        <input id="outstandingCheckListReguler${i}" onchange="outstandingCheckListReguler(${i},this.id)" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        </div></td>
    <td>${item.NoBukti}</td>
    <td>${date1}</td>
    <td>${item.namacustsupp}</td>
    <td>${item.namabrg}</td>
    <td>${item.Nopesanan}</td>
    <td>${item.Noserah}</td>
    <td>${item.kodegdg}</td>
    <td>${Number(item.Qnt).toFixed(2)}</td>
    <td>${Number(item.QntXz).toFixed(2)}</td>
    <td>${Number(item.Qntsaldo).toFixed(2)}</td>
    <td>${Number(item.QNTOUTPICKINGLIST).toFixed(2)}</td>
    <td>${item.satuan}</td>
    <td>${item.catatan}</td>
    <td>${item.namakebun}</td>
    <td>${item.AlamatLokasi}</td>
    <td>${date2}</td>
    <td>${item.RefPR}</td>
    <td>${item.PartNumber}</td>
    <td>${ item.NamaMerk ? item.NamaMerk: ''}</td>
    <td>${item.UserID}</td>
    </tr>`
  });

  // <tr>
  //   <th class="text-center">v</th>
  //   <th scope="col">No. Transaksi</th>
  //   <th scope="col">Tanggal</th>
  //   <th scope="col">Nama Cust</th>
  //   <th scope="col">Nama Barang</th>
  //   <th scope="col">PO Cust</th>
  //   <th scope="col">Qnt SO</th>
  //   <th scope="col">Qnt</th>
  //   <th scope="col">Stock</th>
  //   <th scope="col">Satuan</th>
  //   <th scope="col">Catatan</th>
  //   <th scope="col">Lokasi Penerima</th>
  //   <th scope="col">Alamat Kirim</th>
  //   <th scope="col">Due Date</th>
  //   <th scope="col">PR</th>
  //   <th scope="col">Part Number</th>
  //   <th scope="col">Merk</th>
  //   <th scope="col">User ID</th>
  // </tr>

  $('#tabelReguler').DataTable().destroy();
  //
  document.getElementById(`tabel_dataReguler`).innerHTML = rowTableReguler
  //
  //
  $("#tabelReguler").DataTable({ "lengthChange": false,
  'order': [[1, 'asc']],
      "paging": false ,
       "columnDefs": [
    { "type": "date", "targets": [2] } , {"targets" :[0] , 'orderable' : false}
    // {  "className": "text-center", "targets": [4] },
  ]});



  let rowTable = ``
  dataRefreshOutstanding.forEach((item, i) => {

    let date1 = ""
    if (item.TANGGAL) {
      let date = new Date(item.TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
    }

    let date2 = ""
    if (item.DUEDATE) {
      let date = new Date(item.DUEDATE);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date2 = date.getFullYear()+"/"+(month)+"/"+(day) ;
    }
    rowTable += `
    <tr>
    <td><div class="form-check text-center">
        <input id="outstandingCheckList${i}" onchange="outstandingCheckList(${i},this.id)" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        </div></td>
    <td>${item.NoBukti}</td>
    <td>${date1}</td>
    <td>${item.namacustsupp}</td>
    <td>${item.namabrg}</td>
    <td>${item.Nopesanan}</td>
    <td>${item.Noserah}</td>
    <td>${item.kodegdg}</td>
    <td>${Number(item.Qnt).toFixed(2)}</td>
    <td>${Number(item.QntXz).toFixed(2)}</td>
    <td>${Number(item.Qntsaldo).toFixed(2)}</td>
    <td>${Number(item.QNTOUTPICKINGLIST).toFixed(2)}</td>
    <td>${item.satuan}</td>
    <td>${item.catatan}</td>
    <td>${item.namakebun}</td>
    <td>${item.AlamatLokasi}</td>
    <td>${date2}</td>
    <td>${item.RefPR}</td>
    <td>${item.PartNumber}</td>
    <td>${ item.NamaMerk ? item.NamaMerk: ''}</td>
    <td>${item.UserID}</td>
    </tr>`
  });

  // <tr>
  //   <th class="text-center">v</th>
  //   <th scope="col">No. Transaksi</th>
  //   <th scope="col">Tanggal</th>
  //   <th scope="col">Nama Cust</th>
  //   <th scope="col">Nama Barang</th>
  //   <th scope="col">PO Cust</th>
  //   <th scope="col">Qnt SO</th>
  //   <th scope="col">Qnt</th>
  //   <th scope="col">Stock</th>
  //   <th scope="col">Satuan</th>
  //   <th scope="col">Catatan</th>
  //   <th scope="col">Lokasi Penerima</th>
  //   <th scope="col">Alamat Kirim</th>
  //   <th scope="col">Due Date</th>
  //   <th scope="col">PR</th>
  //   <th scope="col">Part Number</th>
  //   <th scope="col">Merk</th>
  //   <th scope="col">User ID</th>
  // </tr>

  $('#tabel').DataTable().destroy();
  //
  document.getElementById(`tabel_data`).innerHTML = rowTable
  //
  //
  $("#tabel").DataTable({ "lengthChange": false,
  'order': [[1, 'asc']],
      "paging": false ,
       "columnDefs": [
    { "type": "date", "targets": [2] } , {"targets" :[0] , 'orderable' : false}
    // {  "className": "text-center", "targets": [4] },
  ]});


  $('#tabelPenerimaan').DataTable().destroy();

  let rowTablePenerimaan = ``

  dataRefreshPenerimaan.forEach((item, i) => {
    rowTablePenerimaan += `
    <tr>
    <td class="text-center">
    <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailPenerimaan('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
    <button class="btn btn-success btn-sm" type="button" onclick="buttonEditPenerimaan('${item[0].NOBUKTI}')"><i class="bi bi-pen"></i></button>
    <button class="btn btn-primary btn-sm" type="button" onclick="buttonPrintPenerimaan('${item[0].NOBUKTI}')"><i class="bi bi-printer"></i></button>
    </td>
    <td>${item[0].NOBUKTI}</td>
    <td>${item[0].TANGGAL}</td>
    <td>${item[0].namacustsupp}</td>
    <td>${item[0].dataso}</td>
    <td>${item[0].datapocust}</td>
    <td>${item[0].NOserah}</td>
    <td>-</td>
    <td>-</td>
    <td>${item[0].KETERANGAN}</td>
    </tr>`
  });


  document.getElementById(`tabelPenerimaan_data`).innerHTML = rowTablePenerimaan
  $("#tabelPenerimaan").DataTable({ "lengthChange": false,
  'order': [[1, 'asc']],
      "paging": false ,
       "columnDefs": [
    { "type": "date", "targets": [2] },
    {  "className": "text-center", "targets": [0], "orderable" :false },
  ]});



// table pickinglist per periode
$('#tabelPenerimaanperiod').DataTable().destroy();

let rowTablePenerimaanperiod = ``

dataRefreshPenerimaanperiod.forEach((item, i) => {
  rowTablePenerimaanperiod += `
  <tr>
  <td class="text-center">
  <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailPenerimaanperiod('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
  </td>

  <td>${item[0].NOBUKTI}</td>
  <td>${item[0].TANGGAL}</td>
  <td>${item[0].namacustsupp}</td>
  <td>${item[0].dataso}</td>
  <td>${item[0].datapocust}</td>
  <td>${item[0].NOserah}</td>
  <td>-</td>
  <td>-</td>
  <td>${item[0].KETERANGAN}</td>
  </tr>`
});


document.getElementById(`tabelpenerimaanperiod_data`).innerHTML = rowTablePenerimaanperiod
$("#tabelPenerimaanperiod").DataTable({ "lengthChange": false,
'order': [[1, 'asc']],
    "paging": false ,
     "columnDefs": [
  { "type": "date", "targets": [2] },
  {  "className": "text-center", "targets": [0], "orderable" :false },
]});



}


</script>


{{-- } - karakter sisa di luar <script> (dulu tampil sebagai teks di halaman) --}}


@endsection
