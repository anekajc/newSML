@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Permintaan Pemakaian Internal')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">



  <div class="col-6 text-left">
    {{-- <h2>Permintaan Pemakaian Internal</h2> - judul pindah ke header (page-title) --}}
  </div>
  <div class="col-6 text-right">
    {{-- tombol dipindah ke bar tab / toolbar tabel --}}
    <!-- <button type="button" class="btn btn-primary btn-sm " style="height: 60px; " onclick="buttonShowModal()"  >Add Transokasi</button> -->
    <!-- <button type="button" class="btn btn-primary btn-sm " style="height: 60px; " onclick="buttonAdd()"  >Add Perkiraan</button> -->
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Permintaan Pemakaian Internal</a>
      </div>
      <div class="wms-tab-aksi">
        <button type="button" class="btn btn-primary btn-sm " onclick="buttonAdd()"  >Add Pemakaian Internal</button>
      </div>
    </div>
  </div>
  <div class="card wms-kartu">
    <div class="card-body">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabel" class="data-table po-aksi-hover"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Tanggal</th>
                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center"><button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $penerimaanArray[$i][0]->NOBUKTI }}', '{{ $penerimaanArray[$i][0]->NoUrut }}')"><i class="bi bi-pen"></i></button>
                            <!-- <button class="btn btn-primary btn-sm" type="button" onclick="buttonPrintPenerimaan('{{ $penerimaanArray[$i][0]->NOBUKTI }}', '{{ $penerimaanArray[$i][0]->NoUrut }}')"><i class="bi bi-printer"></i></button> -->
                          </td>
                          <td>{{ $penerimaanArray[$i][0]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i][0]->TANGGAL)) !!}</td>
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



<!-- <div> -->


<!-- start modal -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" onclick="closeForm()" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input style="box-shadow: 0 0 5px rgba(81, 203, 238, 1);
  border: 1px solid rgba(81, 203, 238, 1);" type="text" class="form-control" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
              <label>Gudang</label>
            </div>
            </div>
            <div class="col-12">

                <select onchange="" id="input_add_gudang" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected disabled>-- Pilih Gudang --</option>
                </select>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_keterangan" placeholder="">
              </div>
            </div> -->




          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">
            <div class="col-md-12 text-right">
            <button type="button" class="btn btn-primary" onclick="buttonAddAdd()" class="btn btn-secondary"  >Add Item</button>
        </div>

        <!-- koreksi add -->
        <div id="formAddAdd" class="container-fluid showhide">
          <!-- <div class="line"></div> -->
          <hr/>
          <div class="row">
            <div class="col-12">
              <h4>Add Item</h4>
            </div>
          </div>

          <div class="row">
            <div class="col-8">
              <div class="form-group">
              <label>Kode Barang</label>
            </div>
            </div>
            <div class="col-4 text-right">

                {{-- Dinonaktifkan 2026-10-22: tombol browse teks "+" diganti ikon kaca pembesar (fungsi sama). --}}
                {{-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button> --}}
                {{-- Dinonaktifkan 2026-10-27: tombol kaca pembesar dipindah menempel di kanan kolom input (fungsi sama). --}}
                {{-- <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" title="Cari Barang"><i class="bi bi-search"></i></button> --}}
              </div>
            <div class="col-12">
              {{-- <input id="AddAddKodeBrg" type="text" class="form-control" disabled> --}}
              <div class="input-group wms-browse">
                <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
                <div class="input-group-append">
                  <button type="button" onclick="buttonAddListBarang()" class="btn btn-browse" title="Cari Barang"><i class="bi bi-search"></i></button>
                </div>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Nama Barang</label>
            </div>
            </div>
            <div class="col-12">
              <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Qty</label>
            </div>
            </div>
            <div class="col-12">
              <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right">
            </div>


          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Satuan</label>
            </div>
            </div>
            <div class="col-12">

                <select onchange="" id="AddAddSatuan" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected disabled>-- Pilih Satuan --</option>
                </select>
            </div>

          </div>


          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-secondary" onclick="buttonBatalAdd()" >Batal</button>
              <button type="button" onclick="submitAddAdd()" class="btn btn-primary" >Submit</button>
            </div>

          </div>
          <!-- <div class="line"></div> -->
          <hr/>
        </div>
        <!-- end -->



                <!-- koreksi edit -->
                <div id="formAddEdit" class="container-fluid showhide">
                  <!-- <div class="line"></div> -->
                  <hr/>
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
                      <input id="AddEditKodeBrg" type="text" class="form-control" disabled>
                    </div>
                    <!-- <div class="col-1 text-left">

                        <button type="button" onclick="buttonAddListBarang()" class="btn btn-primary" >+</button>
                      </div> -->
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                      <label>Nama Barang</label>
                    </div>
                    </div>
                    <div class="col-12">
                      <input id="AddEditNamaBrg" type="text" class="form-control" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                      <label>Qty</label>
                    </div>
                    </div>
                    <div class="col-12">
                      <input id="AddEditInputQty" type="number" value=0.00 class="form-control text-right">
                    </div>



                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                      <label>Satuan</label>
                    </div>
                    </div>
                    <div class="col-12">

                        <select onchange="" id="AddEditSatuan" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                          <option value=0 selected disabled>-- Pilih Satuan --</option>
                        </select>
                    </div>
                  </div>
                  <!-- <div class="row">
                    <div class="col-8">
                      <div class="form-group">
                      <label>Lokasi Tujuan</label>
                    </div>
                    </div>
                    <div class="col-4 text-right">

                        <button type="button" onclick="buttonAddListLokasi('AddEditLokasiTujuan')" class="btn btn-primary" >+</button>
                      </div>
                    <div class="col-12">
                      <input id="AddEditLokasiTujuan" type="text"  class="form-control" disabled>
                    </div>


                      <div class="col-8">
                        <div class="form-group">
                        <label>Lokasi Asal</label>
                      </div>

                      </div>
                      <div class="col-4 text-right">

                          <button type="button" onclick="buttonAddListLokasi('AddEditLokasiAsal')" class="btn btn-primary" >+</button>
                        </div>
                      <div class="col-12">
                        <input id="AddEditLokasiAsal" type="text"  class="form-control" disabled>
                      </div>
                  </div> -->

                  <div class="row mt-2">
                    <div class="col-md-12 text-right">
                      <button type="button" class="btn btn-secondary" onclick="buttonBatalAdd()" >Batal</button>
                      <button type="button" onclick="submitAddEdit()" class="btn btn-primary" >Submit</button>
                    </div>

                  </div>
                  <!-- <div class="line"></div> -->
                  <hr/>
                </div>
                <!-- end -->

          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="addTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>

                      <th scope="col">Satuan</th>
                      <!-- <th scope="col">Lokasi A</th>
                      <th scope="col">Lokasi T</th> -->

                      <th scope="col">Actions</th>
                    </tr>
                  </thead>


                  <tbody id="addTableData" class="text-right" >
                    <tr>

                  </tr>

                  </tbody>


                </table>
          </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal -->


<!-- start modal list item add -->
<div class="modal fade"  id="formAddListItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=""  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add List Item</h5>
        <button type="button" class="close" onclick="closeListItemAdd()" >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->







        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row" style="overflow:auto;" >
            <div class="container">


            <table id="tabel_add_list_item" class="table table-bordered table-striped"  >
              <thead class="text-center">
                <tr>
                  <th scope="col">Kode Barang</th>
                  <th scope="col">Nama Barang</th>
                  {{-- Dinonaktifkan 2026-10-22: pilih barang dengan klik baris, kolom Actions tidak dipakai. --}}
                  {{-- <th scope="col">Actions</th> --}}

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_item" class="text-left" >

              </tbody>


            </table>
            </div>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>
  <!-- <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="">Submit</button>
  </div> -->
</div>
</div>
</div>
<!-- End modal list item add-->



<!-- start modal detail-->
<div class="modal fade" id="formDetailKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <input type="hidden" name="noUrut" id="input_detail_koreksi_noUrut" value="" />


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input style="" type="text" class="form-control" id="input_detail_koreksi_nobukti" placeholder="" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="date" class="form-control" id="input_detail_koreksi_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_detail_koreksi_keterangan" placeholder="" disabled>
              </div>
            </div> -->




          </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>

        <div class="container-fluid">
          <div class="row ">




          <div class="container-fluid mt-4" style="overflow-x: auto;">

                <table id="detailKoreksiTable" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Brg</th>
                      <th scope="col">Nama Brg</th>
                      <th scope="col">Qty</th>

                      <th scope="col">Satuan</th>
                      <!-- <th scope="col">Lokasi A</th>
                      <th scope="col">Lokasi T</th> -->

                    </tr>
                  </thead>


                  <tbody id="detailKoreksiTableData" class="text-right" >
                    <tr>
                      <td colspan=4>Data tidak ditemukan</td>
                  </tr>

                  </tbody>


                </table>
          </div>




      </div>





    </div>
  </div>
</div>
</div>
</div>
<!-- End modal detail-->


<!-- start modal print -->
<div class="modal fade" id="formPrint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
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
                    <th scope="col">Qty</th>
                    <th scope="col">Satuan</th>
                    <th scope="col">Lokasi A</th>
                    <th scope="col">Lokasi T</th>
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

              <input type="number" value=1 style="max-width: 50px" class="text-right" id="input_print_jumlahprint" >
            </div>
            <!-- <div class="col-1">
                <input type="number" value=1 class="form-control text-right" id="input_print_jumlahprint" >
            </div> -->

          </div>

        </div>



      <div class="modal-footer mt-4">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitPrint()">Print</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal print-->



@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>


<script type="text/javascript">

let listBarang = []
let listBarangReq = null // Ditambahkan 2026-10-22: muat daftar barang di belakang layar
let listLokasi = []

let listItemForm = []
let itemAddObj = {}
let itemEditObj = {}
let dataPrintPenerimaan = []


$(document).ready(function(){

      $("#tabel").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        { "type": "date", "targets": [2] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
      ]});



      // Dinonaktifkan 2026-10-22: tabel pemilih barang sekarang dibuat saat modal dibuka
      // (buttonAddListBarang, klik baris, tanpa kolom Actions).
      // $("#tabel_add_list_item").DataTable({
      //   "lengthChange": false,
      //     "paging": false ,
      //      "columnDefs": [
      //   {  "className": "text-center", "targets": [2] },
      // ]});

      $("#tabel_add_list_lokasi").DataTable({
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        {  "className": "text-center", "targets": [2] },
      ]});

      // Ditambahkan 2026-10-22: daftar barang (±19 ribu, ±7 dtk) dimuat di belakang layar saat halaman
      // terbuka, supaya pemilih barang langsung tampil saat tombol kaca pembesar diklik.
      listBarangReq = $.ajax({
        url: "{!! url('permintaanpenyerahangudanglistbarang') !!}",
        type: "get",
        success: function(res) { if (!listBarang.length) listBarang = res },
        complete: function() { listBarangReq = null }
      })

});

function loadAll () {
  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('loadallpermintaanpenyerahangudang') !!}",
    type: "get",
    async: false,
    success: function(res) {
      document.getElementById("akses_istambah").value = res.akses.ISTAMBAH
      document.getElementById("akses_iskoreksi").value = res.akses.ISKOREKSI
      document.getElementById("akses_iscetak").value = res.akses.ISCETAK
      document.getElementById("akses_ishapus").value = res.akses.ISHAPUS
      console.log(res)
      let rowTable = ''
      res.penerimaan.forEach((item, i) => {
        let date = new Date(item[0].TANGGAL);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        rowTable += `
        <tr>
        <td class="text-center"><button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetailKoreksi('${item[0].NOBUKTI}')" ><i class="bi bi-info-lg"></i></button>
        <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item[0].NOBUKTI}', '${item[0].NoUrut}')"><i class="bi bi-pen"></i></button>

        </td>
          <td>${item[0].NOBUKTI}</td>
          <td>${date1}</td>
        </tr>
        `
      });
      // <button class="btn btn-primary btn-sm" type="button" onclick="buttonPrintPenerimaan('${item[0].NOBUKTI}', '${item[0].NoUrut}')"><i class="bi bi-printer"></i></button>
      document.getElementById("tabel_data").innerHTML = rowTable




    }
  })


  $("#tabel").DataTable({
    'order': [[1, 'asc']],
    "lengthChange": false,
      "paging": false ,
       "columnDefs": [
    { "type": "date", "targets": [2] },
    {  "className": "text-center", "targets": [0], "orderable" :false },
  ]});
}


function closeListItemAdd () {
  console.log('closeListItemAdd')
  $("#formAddListItem").hide()
  // document.getElementById("input_add_add_kodebarang").value = dataAddListItem[i].KODEBRG
  // document.getElementById("input_add_add_keterangannama").value = dataAddListItem[i].NAMABRG
  var modal = document.getElementById("form");
  modal.style.display = "block";

}



function buttonShowModal () {
    var modal2 = document.getElementById("formAddListItem");
    modal2.style.display == "block"
}

function closeForm () {
  console.log('closeForm')
  $('.modal').modal('hide');

}


function submitAddEdit () {
  console.log('submitAddEdit')
  console.log(itemEditObj)
  let _token = $("#_token").val();
  let qntTerima = $("#AddEditInputQty").val()
  let satuan = $("#AddEditSatuan").val()
  console.log({
    _token : _token,
    choice: 'U',
    urut: itemEditObj.URUT,
    nobukti: itemEditObj.NOBUKTI,
    nourut: itemEditObj.NoUrut,
    qntTerima,
    kodebrg: itemEditObj.KODEBRG,
    namabrg: itemEditObj.NAMABRG,
    qnt1: 0,
    qnt2: 0,
    qntx: qntTerima,
    nosat: satuan,
    tanggal: itemEditObj.TANGGAL,
    jmlRecord: 1,
    kodegdg: itemEditObj.KodeGdg
  })
  // return

  if (Number(qntTerima) <= 0) {
    alertify.warning("Qnt <= 0")
    return
  }
  if (!satuan) {
    alertify.warning("satuan harus diisi")
    return
  }

  $.ajax({
    url: "{!! url('permintaanpenyerahangudangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice: 'U',
      urut: itemEditObj.URUT,
      nobukti: itemEditObj.NOBUKTI,
      nourut: itemEditObj.NoUrut,
      qntTerima,
      kodebrg: itemEditObj.KODEBRG,
      namabrg: itemEditObj.NAMABRG,
      qnt1: 0,
      qnt2: 0,
      qntx: qntTerima,
      nosat: satuan,
      tanggal: itemEditObj.TANGGAL,
      jmlRecord: 1,
      kodegdg: itemEditObj.KodeGdg
    },
    success: function(res) {
      console.log(res)
      console.log('masuk success')
      if (res == 1) {
        console.log(res ,'!')
        // $("#form").modal('toggle')

        refreshTableAdd()
        loadAll()
        $('.showhide').hide();
        alertify.success('Permintaan gudang telah diedit');
        // loadAll()




      }
    },
    error: function(err) {
      console.log('masuk error')
      console.log(err)
    }
  })


}

function submitAddAdd() {
  console.log('submitAddAdd')

  let _token = $("#_token").val();
  let nobukti = $('#input_add_nobukti').val()
  let nourut = $('#input_add_noUrut').val()
  let qntTerima = $("#AddAddInputQty").val()
  let kodebrg = $("#AddAddKodeBrg").val()
  let namabrg = $("#AddAddNamaBrg").val()
  let kodegdg = $("#input_add_gudang").val()
  // let lokasiasal = $("#AddAddLokasiAsal").val()
  // let lokasitujuan = $("#AddAddLokasiTujuan").val()
  let satuan = $("#AddAddSatuan").val()
  let tanggal = $("#input_add_tanggal").val()
  // let keterangan = $("#input_add_keterangan").val()

  let checkDate = new Date(tanggal)

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let jmlRecord = 0
  if (listItemForm.length) {
    jmlRecord =1
  }
  console.log(jmlRecord)
  // return

  if (!kodegdg) {
    alertify.warning("Gudang harus diisi")
    return
  }
  if (!kodebrg) {
    alertify.warning("kodebarang harus diisi")
    return
  }


  // if (!lokasiasal) {
  //   alertify.warning("lokasiasal harus diisi")
  //   return
  // }
  // if (!lokasitujuan) {
  //   alertify.warning("lokasitujuan harus diisi")
  //   return
  // }


  if (Number(qntTerima) <= 0) {
    alertify.warning("Qnt <= 0")
    return
  }
  if (!satuan) {
    alertify.warning("satuan harus diisi")
    return
  }

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  // kalo nosat 1 qnt , qnt2 langsung dr input
  //kalo nosat 2/3 , qnt dikali isi qnt 2 dr input
  let qnt2 = qntTerima
  let qnt1= 0
  if (satuan ==1) {
    qnt1 = qntTerima
  } else if (satuan == 2) {
    qnt1 = qntTerima * itemAddObj.ISI2
  } else if (satuan == 3) {
    qnt1 = qntTerima * itemAddObj.ISI3
  }
  let qntx = 0

  console.log(itemAddObj)
  console.log('nobukti', nobukti)
  console.log('nourut', nourut)
  console.log('qntTerima' , qntTerima)
  console.log('kodebrg' , kodebrg)
  console.log('namabrg' , namabrg)
  // console.log('lokasiasal' , lokasiasal)
  // console.log('lokasitujuan' , lokasitujuan)
  console.log('satuan' , satuan)
  console.log('tanggal' , tanggal)
  // console.log('keterangan' , keterangan)
  console.log('jmlRecord' , jmlRecord)
  console.log('kodegdg', kodegdg)
  console.log(qnt1,qnt2)


  // return

  $.ajax({
    url: "{!! url('permintaanpenyerahangudangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice: 'I',
      urut: 0,
      nobukti,
      nourut,
      qnt1,
      qnt2: qntTerima,
      kodebrg,
      kodegdg,
      namabrg,
      nosat: satuan,
      tanggal,
      jmlRecord,
      qntx
    },
    success: function(res) {
      console.log(res)
      console.log('masuk success')
      if (res == 1) {
        console.log(res ,'!')
        // $("#form").modal('toggle')

        refreshTableAdd()
        loadAll()
        $('.showhide').hide();
        alertify.success('Permintaan gudang telah ditambah');
        // loadAll()

      }
      if (res == 2) {
        setNewNoBukti()
        alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
      }
    },
    error: function(err) {
      console.log('masuk error')
      console.log(err)
    }
  })





}


function buttonAddListBarang () {
  var modal = document.getElementById("form");
  // $('#form').css('opacity', .8);
  modal.style.display = "none";

  // Dinonaktifkan 2026-10-22: daftar barang (±2,9 MB) dimuat ulang tiap klik lalu semua baris
  // dirender sekaligus dengan tombol + per baris (lambat). Diganti: dimuat sekali per halaman,
  // DataTables deferRender + paging, pilih barang dengan klik baris (sama dengan Permintaan UK).
  // $('#tabel_add_list_item').DataTable().destroy();
  //
  // $.ajax({
  //   url: "{!! url('permintaanpenyerahangudanglistbarang') !!}",
  //   type: "get",
  //   async: false,
  //   success: function(res) {
  //     console.log(res)
  //     listBarang = res
  //   }
  // })
  //
  // let rowTable = ""
  // listBarang.forEach((item, i) => {
  //   rowTable+= `
  //     <tr>
  //     <td>${item.KODEBRG}</td>
  //     <td>${item.NAMABRG}</td>
  //     <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddAddInsertItem(${i})" type="button" ><i class="bi bi-plus"></i></button></td>
  //     </tr>
  //   `
  // });
  // document.getElementById("tabel_data_add_list_item").innerHTML = rowTable
  //
  // $("#tabel_add_list_item").DataTable({
  //   "lengthChange": false,
  //     "paging": false ,
  //      "columnDefs": [
  //   {  "className": "text-center", "targets": [2] },
  // ]});
  if (!listBarang.length) {
    // Belum selesai dimuat di belakang layar: batalkan, muat langsung dengan animasi memuat.
    if (listBarangReq) { listBarangReq.abort(); listBarangReq = null }
    wmsProses('Memuat daftar barang...', function () {
    $.ajax({
      url: "{!! url('permintaanpenyerahangudanglistbarang') !!}",
      type: "get",
      async: false,
      success: function(res) {
        listBarang = res
      }
    })
    })
  }
  if ($.fn.DataTable.isDataTable('#tabel_add_list_item')) {
    $('#tabel_add_list_item').DataTable().search('').draw()
  } else {
    $("#tabel_add_list_item").DataTable({
      data: listBarang.map((item, i) => ({ i: i, KODEBRG: item.KODEBRG || '', NAMABRG: item.NAMABRG || '' })),
      deferRender: true,
      autoWidth: false, // tabel dibuat saat modal masih tersembunyi; tanpa ini lebar kolom jadi sempit
      columns: [{ data: 'KODEBRG' }, { data: 'NAMABRG' }],
      lengthChange: true,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 10,
      createdRow: function (row) { row.classList.add('pick-row') },
      language: { lengthMenu: 'Tampilkan _MENU_', emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' }
    })
    $('#tabel_add_list_item tbody').on('click', 'tr.pick-row', function () {
      const data = $('#tabel_add_list_item').DataTable().row(this).data()
      if (data) buttonAddAddInsertItem(data.i)
    })
  }




  var modal2 = document.getElementById("formAddListItem");
  console.log(modal2.style.display)
  if (modal2.style.display == 'none') {
    console.log('masuk none')
    modal2.style.display = "block";
    console.log($('.modal').hasClass('in'))
    // if (! $('#formAddListItem').hasClass('in')) {
    //   console.log('masok not in')
    //   $("#formAddListItem").modal('toggle')
    // }
  } else {
    console.log('masuk toggle')
    modal2.style.display = "block";
    $("#formAddListItem").modal('toggle')

  }


  // $("#formAddListItem").modal('toggle')
}

function buttonBatalAdd() {
  $('.showhide').hide();
}



function buttonAddAddInsertItem (index) {
  console.log('buttonAddAddInsertItem')
  console.log(listBarang[index])

  if(listBarang[index].SAT3) {
    console.log('sat3 masok')
    console.log(listBarang[index].SAT3)
  }

  document.getElementById("AddAddKodeBrg").value = listBarang[index].KODEBRG
  document.getElementById("AddAddNamaBrg").value = listBarang[index].NAMABRG
  itemAddObj = listBarang[index]
  console.log(listBarang[index])

  // <select onchange="" id="AddAddSatuan" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
  //   <option value=0 selected disabled>-- Pilih Satuan --</option>
  // </select>

  let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'
  if (listBarang[index].SAT1) {
    rowSelect += `<option value=1>Sat 1 - ${listBarang[index].SAT1}</option>`
  }
  if (listBarang[index].SAT2) {
    rowSelect += `<option value=2>Sat 2 - ${listBarang[index].SAT2}</option>`
  }
  if (listBarang[index].SAT3) {
    rowSelect += `<option value=3>Sat 3 - ${listBarang[index].SAT3}</option>`
  }
  document.getElementById("AddAddSatuan").innerHTML = rowSelect

  // console.log(kodebrg)
  // console.log(namabrg)
  // console.log(sat1)
  // console.log(sat2)
  // console.log(sat3)
  closeListItemAdd()

}


function setNewNoBukti () {
  $.ajax({
    url: "{!! url('getnobuktipermintaanpenyerahangudang') !!}",
    type: "get",
    async: false,
    success: function(res) {
      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_noUrut").value = res[0].Nourut
    }})
}

function buttonPrintPenerimaan (nobukti, nourut) {
  let akses = $("#akses_iscetak").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log('buttonPrintPenerimaan')
  console.log(nobukti, nourut)
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('transferlokasidetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
    _token,
      nobukti
    },
    success: function(res) {
      console.log(res)
      dataPrintPenerimaan = res
    }
  })
  document.getElementById("input_print_nobukti").value = dataPrintPenerimaan[0].NOBUKTI
  document.getElementById("input_print_keterangan").value = dataPrintPenerimaan[0].KETERANGAN

  let date = new Date(dataPrintPenerimaan[0].TANGGAL);
  let day = ("0" + date.getDate()).slice(-2);
  let month = ("0" + (date.getMonth() + 1)).slice(-2);
  date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
  $('#input_print_tanggal').val(date1)


  let rowTable = ``


  let rowTableTemp = ``
  dataPrintPenerimaan.forEach((item, i) => {
    rowTable += `
    <tr>
    <td>
    <div class="form-check text-center">
        <input id="printChecklist${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        </div></td>
    <td>${item.KODEBRG}</td>
    <td>${item.NamaBrg}</td>
    <td>${item.QNT}</td>
    <td>${item.SATUAN}</td>
    <td>${item.KodeLokasi}</td>
    <td>${item.KodeLokTujuan}</td>

    </tr>
    `
    rowTableTemp += `
    <div id="tempqrcodeprint${i}" style="width: 10px; height: 10px"></div>
    `
  });

  // <td class="d-flex justify-content-center"><div id="qrcodeprint${i}"  style="width: 60px; height: 60px"></div></td>

  document.getElementById("printTableData").innerHTML = rowTable
  document.getElementById("tempPrintContainer").innerHTML = rowTableTemp

  dataPrintPenerimaan.forEach((item, i) => {
    // new QRCode(document.getElementById(`qrcodeprint${i}`), `${item.NOBUKTI}${item.KODEBRG}`);
    new QRCode(document.getElementById(`tempqrcodeprint${i}`), {text: `${item.NOBUKTI}.${item.URUT}.${item.KODEBRG}` , width: 60, height: 60});
  });




  $("#formPrint").modal('toggle')
}


function submitPrint () {
  let printContent1 = ""
  let jumlahPrint = $(`#input_print_jumlahprint`).val();
  let z = 0
  dataPrintPenerimaan.forEach((item, i) => {
    if (document.getElementById(`printChecklist${i}`).checked) {
      // qrcodeprint
      let date = new Date(item.TANGGAL);
      var day = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      let printContent = document.getElementById(`tempqrcodeprint${i}`).innerHTML;
      printContent1 += `<div style="margin: 0px; padding: 0px ; text-align: center">`
      for (let i=0; i < jumlahPrint ; i++ ) {

        if (z == 0) {
          printContent1 += `<div style="break-inside: avoid; width:8cm; height: 4.5cm; overflow: hidden">`
        } else {
          printContent1 += `<div style="break-inside: avoid; width:8cm; height: 4.5cm; page-break-before: always; overflow: hidden">`
        }
        printContent1 += `<div  style="display: flex; flex-direction: row;">`
        printContent1 += `<div style="display: flex; flex-direction: column; width: 6.3cm; text-align: left">`
        printContent1 += `<div style="font-size: 14px">Barcode: </div><div style="font-size: 14px">NoBukti: ${item.NOBUKTI}</div><div style="font-size: 14px">Tggl: ${date1} </div>`
        printContent1 += `</div>`
        printContent1 += `<div  >`
        printContent1 += printContent
        printContent1 += `</div>`
        printContent1 += `</div>`

        printContent1 += `<div style="width: 8cm;  margin-top: -3px">`
        printContent1 += `<hr >`
        printContent1 += `</div>`

        printContent1 += `<div  style="display: flex; flex-direction: column; width: 8cm;font-size: 14px; margin-top: -10px ">`
        printContent1 += `<div  style="display: flex; flex-direction: row;">`
        printContent1 += `<div  style="display: flex; flex-direction: column; text-align: left; width: 75%">`
        printContent1 += `<div >PO: </div><div>Ship to: </div>`
        printContent1 += `</div>`
        printContent1 += `<div class="" style="text-align: left; width: 25%" >`
        printContent1 += `Penerima:`
        printContent1 += `</div>`
        printContent1 += `</div>`
        printContent1 += `</div>`

        printContent1 += `<div style="width: 8cm; margin-top: -5px">`
        printContent1 += `<hr >`
        printContent1 += `</div>`

        printContent1 += `<div style="font-size: 12px; text-align: left; margin-top: -10px">Loc: </div>`
        printContent1 += `<div style="font-size: 12px; text-align: left">KodeBrg: ${item.KODEBRG}</div>`
        printContent1 += `<div style="font-size: 11px; text-align: left; ">NamaBrg: ${item.NamaBrg}</div>`
        printContent1 += `<div style="font-size: 12px; text-align: left">Qty: ${item.QNT} @ ${item.SATUAN}</div>`
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

  w.document.write($(`#printContainer`).html());
  console.log('a')
  w.print();
  console.log('b')
  w.close();
  console.log('c')

}

function buttonKoreksi (nobukti, nourut) {

  // $('#formAddListLokasi').css('display', '')
  $('#formAddListItem').css('display', '')

  $('.showhide').hide();
  console.log(nobukti, nourut)

  $('#input_add_nobukti').css('border', '')
  $('#input_add_nobukti').css('box-shadow', '')
  document.getElementById("input_add_nobukti").value = nobukti
  document.getElementById("input_add_noUrut").value = nourut


  refreshTableAdd()
  let rowSelect = `<option value='${listItemForm[0].KodeGdg}' selected>${listItemForm[0].KodeGdg} - ${listItemForm[0].Namagdg}</option>`

  // refreshTableDetailKoreksi
  console.log('tes44')




  document.getElementById("input_add_gudang").innerHTML = rowSelect


  $("#form").modal('toggle')

}


function buttonAdd () {
  console.log('buttonAdd')

  // var modal2 = document.getElementById("formAddListLokasi");
  // // console.log(modal2.style.display)
  // // if (modal2.style.display == 'none') {
  //   // console.log('masuk none')
  //
  //   modal2.style.display = "block";
  // console.log(document.getElementById("formAddListLokasi").style.display)
  // // $("#formAddListLokasi").removeAttr("display");
  // $('#formAddListLokasi').css('display', '')
  // $('#formAddListItem').css('display', '')
  // console.log(document.getElementById("formAddListLokasi").style.display)
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    $('.showhide').hide();
    console.log('buttonAdd')
    $('#input_add_nobukti').css('border', '')
    $('#input_add_nobukti').css('box-shadow', '')
    // document.getElementById("input_add_keterangan").value = ''

    $.ajax({
      url: "{!! url('permintaanpenyerahangudanglistgudang') !!}",
      type: "get",
      async: false,
      data: {
        // _token,
        // nobukti
      },
      success: function(res) {
        console.log(res)
        let rowSelect = '<option value=0 selected disabled>-- Pilih Gudang --</option>'

        res.forEach((item, i) => {

          rowSelect += `<option value='${item.KODEGDG}'>${item.KODEGDG} - ${item.NAMA}</option>`
        });




        document.getElementById("input_add_gudang").innerHTML = rowSelect

      }})




    setNewNoBukti()
    refreshTableAdd()
    console.log('1')



    $("#form").modal('toggle')
}

function buttonDetailKoreksi (nobukti) {
  console.log('buttonDetailKoreksi')
  console.log(nobukti)
  refreshTableDetailKoreksi(nobukti)

  $("#formDetailKoreksi").modal('toggle')

}

function refreshTableDetailKoreksi(nobukti) {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('permintaanpenyerahangudangdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      console.log(res)

      let tableRow = ''
      document.getElementById("input_detail_koreksi_nobukti").value = res[0].NOBUKTI
      // document.getElementById("input_detail_koreksi_keterangan").value = res[0].KETERANGAN
      let date = new Date(res[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      console.log(date1)
      // console.log($("#input_detail_koreksi_tanggal").val())
      $('#input_detail_koreksi_tanggal').val(date1)
      res.forEach((item, i) => {
        tableRow += `
        <tr class="text-left">
        <td>${item.KODEBRG}</td>
        <td>${item.NAMABRG}</td>
        <td class="text-right">${item.Qntx}</td>
        <td>${item.SATUAN}</td>
        </tr>
        `
      });
      console.log(res.length)
      if (!res.length) {
        tableRow = `<tr>
        <td colspan='7' class='text-center'>Data tidak ditemukan</td>
        </tr>`
      }


      document.getElementById("detailKoreksiTableData").innerHTML = tableRow
    }})
}

function buttonEdit (index) {
  console.log('buttonEdit')

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhide').hide();
  console.log(index)
  itemEditObj = listItemForm[index]
  console.log(itemEditObj)

  document.getElementById("AddEditKodeBrg").value = itemEditObj.KODEBRG
  document.getElementById("AddEditNamaBrg").value = itemEditObj.NAMABRG
  // document.getElementById("AddEditLokasiAsal").value = itemEditObj.KodeLokasi
  // document.getElementById("AddEditLokasiTujuan").value = itemEditObj.KodeLokTujuan
  document.getElementById("AddEditInputQty").value = itemEditObj.Qntx
  let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'

  if (itemEditObj.SAT1) {
    rowSelect += `<option value=1>Sat 1 - ${itemEditObj.SAT1}</option>`
  }
  if (itemEditObj.SAT2) {
    rowSelect += `<option value=2>Sat 2 - ${itemEditObj.SAT2}</option>`
  }
  if (itemEditObj.SAT3) {
    rowSelect += `<option value=3>Sat 3 - ${itemEditObj.SAT3}</option>`
  }
  document.getElementById("AddEditSatuan").innerHTML = rowSelect
  document.getElementById("AddEditSatuan").value = itemEditObj.NOSAT


  $('#formAddEdit').show();

}

function refreshTableAdd ( action = 'I') {
  console.log('refreshTableAdd', action)
  // $('#formAddListLokasi').css('display', '')
  // $('#formAddListItem').css('display', '')
  listItemForm = []
  console.log(action)
  let nobukti = $('#input_add_nobukti').val();
  let _token = $("#_token").val();
  console.log('nobukti', nobukti)
  console.log('token', _token)

  $.ajax({
    url: "{!! url('permintaanpenyerahangudangdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      console.log(res)
      listItemForm = res
    }})
    console.log(listItemForm)

    // addTableData
    let tableRow = ''
    listItemForm.forEach((item, i) => {
      tableRow += `
      <tr class="text-left">
      <td>${item.KODEBRG}</td>
      <td>${item.NAMABRG}</td>
      <td class="text-right">${item.Qntx}</td>
      <td>${item.SATUAN}</td>
      <td class="text-center"><button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('${item.NOBUKTI}', ${item.URUT})" ><i class="bi bi-trash"></i></button>
      <button class="btn btn-success btn-sm" type="button" onclick="buttonEdit(${i})" ><i class="bi bi-pen"></i></button>

      </td>
      </tr>
      `
    });
    console.log(listItemForm.length)
    // if (!listItemForm.length && action == 'D') {
    //   alertify.warning('Data item habis')
    // }
    if (!listItemForm.length) {
      tableRow = `<tr>
      <td colspan='5' class='text-center'>Belum ada data</td>
      </tr>`
      console.log('masok !')
      $('#input_add_tanggal').prop('disabled', false)
      // $('#input_add_keterangan').prop('disabled', false)
      $('#input_add_gudang').prop('disabled', false)
    }
    if(listItemForm.length && listItemForm[0].TANGGAL) {
      let date = new Date(listItemForm[0].TANGGAL);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
      console.log(date1)
      $('#input_add_tanggal').val(date1)
      // document.getElementById("input_add_keterangan").value = listItemForm[0].KETERANGAN
      $('#input_add_tanggal').prop('disabled', true)
      // $('#input_add_keterangan').prop('disabled', true)
      $('#input_add_gudang').prop('disabled', true)

    }

    document.getElementById("addTableData").innerHTML = tableRow
}

function buttonDelete (nobukti, urut ) {
  console.log('buttonDelete')
  console.log(nobukti, urut)

  let akses = $("#akses_ishapus").val();
  console.log(akses)

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token = $("#_token").val();

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + ' ?',
    function() {
      $.ajax({
        url: "{!! url('permintaanpenyerahangudangspadd') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          choice: 'D',
          urut: urut,
          nobukti: nobukti,
          nourut: '',
          qnt1: 0,
          qnt2: 0,
          qntx: 0,
          kodebrg: '',
          namabrg: '',
          kodegdg: '',
          nosat: 0,
          tanggal: '',
          jmlRecord: 0
        },
        success: function(res) {
          console.log(res)
          console.log('masuk success')
          if (res == 1) {
            console.log(res ,'!')
            // $("#form").modal('toggle')

            refreshTableAdd('D')
            loadAll()
            alertify.success('Permintaan gudang telah didelete');
            // $("#form").modal('toggle')

          }
        },
        error: function(err) {
          console.log('masuk error')
          console.log(err)
        }
      })


    }
  ,function(){
    console.log('no')
  });
}

function buttonAddAdd () {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  $('.showhide').hide();

  resetTableAddAdd()
  $('#formAddAdd').show();

}



function resetTableAddAdd() {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  // document.getElementById("AddAddLokasiAsal").value = ''
  // document.getElementById("AddAddLokasiTujuan").value = ''
  document.getElementById("AddAddInputQty").value = '0.00'
  let rowSelect = '<option value=0 selected disabled>-- Pilih Satuan --</option>'
  document.getElementById("AddAddSatuan").innerHTML = rowSelect
}



</script>


{{-- } - karakter sisa di luar <script> (dulu tampil sebagai teks di halaman) --}}


@endsection
