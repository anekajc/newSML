@extends('newmasterTest')
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Report Kartu Stok')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">


  <div class="row">
    <div class="col-12 text-left">
      {{-- <h1>Report Kartu Stok</h1> - judul pindah ke header (page-title) --}}
    </div>
    <!-- <div class="col-6 text-right">

    </div> -->
  </div>

</div>

<div id="printContainer" style="display:none">

</div>
<div id="contentContainer" class="container-fluid">

          <div class="row">

          </div>
          <div class="row text-right justify-content-center">
            <div class="card wms-form-kartu" >
              <div class="card-body">
                <h5 class="card-title text-left">Report Kartu Stok</h5>
                <div class="container-fluid">
                <div style="background-color: #E8E8E8; padding: 10px; " class="rounded">
                <!-- <div class="row " >
                  <h6 class="col-3 text-left">Periode</h6>
                </div> -->
                <!-- <div class="row text-center">
                  <div class="col-5"><input id="inputDate1" style="display: block; width: 100%" class="text-center" type="date" value="{!! date('Y-m-d') !!}">
                  </div>
                  <div class="col-2">s/d
                  </div>
                  <div class="col-5"><input id="inputDate2" style="display: block; width: 100%" class="text-center" type="date" value="{!! date('Y-m-d') !!}">
                  </div>
                </div> -->
                <div class="row text-left mt-2">
                  <div class="col-12">
                    <h6>
                    Awal
                  </h6>
                  </div>
                  <div class="col-4">
                    <select id="input_awal_bulan" style="display: block; width: 100%"  class="form-select text-right" aria-label="Default select example">
                      <option value=1>01</option>
                      <option value=2>02</option>
                      <option value=3>03</option>
                      <option value=4>04</option>
                      <option value=5>05</option>
                      <option value=6>06</option>
                      <option value=7>07</option>
                      <option value=8>08</option>
                      <option value=9>09</option>
                      <option value=10>10</option>
                      <option value=11>11</option>
                      <option value=12>12</option>
                    </select>
                  </div>
                  <div class="col-2">-
                  </div>
                  <div class="col-6">
                    <input type="number" maxlength="4" class="form-control text-right bordered" id="input_awal_tahun" value="{!! date('Y') !!}" >
                  </div>
                </div>
                <div class="row text-left mt-1">
                  <div class="col-12">
                    <h6>
                    Akhir
                  </h6>
                  </div>
                  <div class="col-4">
                    <select id="input_akhir_bulan" style="display: block; width: 100%"  class="form-select text-right" aria-label="Default select example">
                      <option value=1>01</option>
                      <option value=2>02</option>
                      <option value=3>03</option>
                      <option value=4>04</option>
                      <option value=5>05</option>
                      <option value=6>06</option>
                      <option value=7>07</option>
                      <option value=8>08</option>
                      <option value=9>09</option>
                      <option value=10>10</option>
                      <option value=11>11</option>
                      <option value=12>12</option>
                    </select>
                  </div>
                  <div class="col-2">-
                  </div>
                  <div class="col-6">
                    <input type="number" maxlength="4" class="form-control text-right bordered" id="input_akhir_tahun" value="{!! date('Y') !!}" >
                  </div>
                </div>
              </div>

              <div class="row text-center mt-4">
                <div class="col-8">
                  <div class="form-group text-left">
                    <label>Lokasi</label>
                  </div>
                </div>
                <div class="col-4 text-right">
                  {{-- Dinonaktifkan 2026-10-23: tombol browse teks "+" diganti ikon kaca pembesar (fungsi sama). --}}
                  {{-- <button type="button" onclick="buttonAddLokasi()" class="btn btn-primary" >+</button> --}}
                  {{-- Dinonaktifkan 2026-10-27: tombol kaca pembesar dipindah menempel di kanan kolom input (fungsi sama). --}}
                  {{-- <button type="button" onclick="buttonAddLokasi()" class="btn btn-primary" title="Cari Lokasi"><i class="bi bi-search"></i></button> --}}
                </div>

                <div class="col-12">
                  {{-- <input type="text" class="form-control text-right bordered" id="input_lokasi" value="" readonly > --}}
                  <div class="input-group wms-browse">
                    <input type="text" class="form-control text-right bordered" id="input_lokasi" value="" readonly >
                    <div class="input-group-append">
                      <button type="button" onclick="buttonAddLokasi()" class="btn btn-browse" title="Cari Lokasi"><i class="bi bi-search"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row text-center">
                <div class="col-8">
                  <div class="form-group text-left">
                    <label>Barang</label>
                  </div>
                </div>
                <div class="col-4 text-right">
                  {{-- Dinonaktifkan 2026-10-23: tombol browse teks "+" diganti ikon kaca pembesar (fungsi sama). --}}
                  {{-- <button type="button" onclick="buttonAddBarang()" class="btn btn-primary" >+</button> --}}
                  {{-- Dinonaktifkan 2026-10-27: tombol kaca pembesar dipindah menempel di kanan kolom input (fungsi sama). --}}
                  {{-- <button type="button" onclick="buttonAddBarang()" class="btn btn-primary" title="Cari Barang"><i class="bi bi-search"></i></button> --}}
                </div>

                <div class="col-12">
                  {{-- <input type="text" class="form-control text-right bordered" id="input_barang" value="" readonly > --}}
                  <div class="input-group wms-browse">
                    <input type="text" class="form-control text-right bordered" id="input_barang" value="" readonly >
                    <div class="input-group-append">
                      <button type="button" onclick="buttonAddBarang()" class="btn btn-browse" title="Cari Barang"><i class="bi bi-search"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row text-center ">
                <div class="col-12">
                  <div class="form-group text-left">
                    <label>Satuan</label>
                  </div>
                </div>

                <div class="col-12">
                  <input type="text" class="form-control text-right bordered" id="input_satuan" maxlength="1" value="1">
                </div>
              </div>

                <!-- <div class="row text-center mt-4">
                  <div class="col-6">
                    <select id="inputReport1" style="display: block; width: 100%"  class="form-select text-right" aria-label="Default select example">
                      <option value=0>BC 2.6.2</option>
                      <option value=1>BC 2.3</option>
                      <option value=2>BC 4.0</option>
                      <option value=3>BC 2.7</option>
                    </select>
                  </div>

                  <div class="col-6">
                    <select id="inputReport2" style="display: block; width: 100%" class="form-select form-select-sm" aria-label=".form-select-sm example">
                      <option value=0>Urut Dok. Pabean</option>
                      <option value=1>Urut Bukti LPB</option>
                    </select>
                  </div>
                </div> -->

                <!-- <div class="row text-center mt-4">
                  <div class="col-6">
                    <select id="inputReport3" style="display: block; width: 100%"  class="form-select" aria-label="Default select example">
                      <option value="All">All</option>
                      <option value="PHI001">PEIHAI</option>
                      <option value="PHJ001">JANTI</option>

                    </select>
                  </div>

                  <div class="col-6">

                    </select>
                  </div>
                </div> -->

                  <a href="#" class="mt-4 btn btn-primary text-right justify-content-right" onclick="makeTable()">Submit</a>

              </div>
              </div>
            </div>
          </div>

        <div class="container-fluid mt-6">



          <div id="showTableReport" style=" background-color: white; display: none" class="row mt-4 rounded">

              <div class="col-12 text-right">
                <button type="button" class="btn btn-success" onclick="exportTableToExcel('tabel')">Export to Excel</button>
                <button type="button" class="btn btn-secondary" onclick="closeTable()">Close Table</button>
              </div>
            <div class="col-12 mt-4" style="overflow:auto;">
              <div class="">

                    <table id="tabel" class="data-table"   >

                      <thead class="text-left" >
                        <tr>
                          <th colspan="13"  style="text-align: left; font-weight: bold;">CV. SARANA PRIMA LESTARI</th>
                        </tr>

                        <tr id="periodeTable">
                          <th colspan="13"  style="text-align: left; ">Gudang: <br/> Barang:</th>
                        </tr>
                        <tr>
                          <th colspan="13"></th>
                        </tr>
                        <tr style="height: 45px; padding: 20px; " class="text-center bg-dark text-light">
                          <th scope="col" style="border: 1px solid black;">Tanggal</th>
                          <th scope="col" style="border: 1px solid black;">NoBukti</th>
                          <th scope="col" style="border: 1px solid black;">Tipe</th>
                          <th scope="col" style="border: 1px solid black;">Keterangan</th>
                          <th scope="col" style="border: 1px solid black;">Sat</th>
                          <th  scope="col" style="border: 1px solid black;">Masuk</th>
                          <th  scope="col" style="border: 1px solid black;">Keluar</th>
                          <th  scope="col" style="border: 1px solid black;">Saldo</th>

                        </tr>
                        <!-- <tr style="padding: 0px; margin: 0px; height: 18px; text-align: center;" class="text-center">

                          <th style="padding: 0px; margin: 0px; border: 1px solid black;"></th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;"></th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;"></th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;"></th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;"></th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;"></th>

                          <th style="padding: 0px; margin: 0px; border: 1px solid black;">Qty</th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;">Rupiah</th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;">Qty</th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;">Rupiah</th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;">Qty</th>
                          <th style="padding: 0px; margin: 0px; border: 1px solid black;">Rupiah</th>

                        </tr>
                      </thead> -->


                      <tbody id="tabel_data" class="text-center"  style="border: 1px solid black; text-align: center;">
                        <tr style="text-align: center">



                          <td colspan=13 style="border: 1px solid black;">Tidak ada data ditemukan</td>


                      </tr>
                      </tbody>


                    </table>
              </div>
            </div>
          </div>
        </div>



    <!-- start modal list item add -->
    <div class="modal fade"  id="formAddListLokasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">List Lokasi</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- <h1>Tes Modal</h1> -->







            <div class="container-fluid mt-4">
              <div class="row text-center" style="overflow:auto;">
                <div class="col-12">


                <table id="tabel_add_list_lokasi" class="table table-bordered table-striped"  >
                  <thead class="text-center">
                    <tr>
                      <th scope="col">Kode Lokasi</th>
                      <th scope="col">Gudang</th>
                      <th scope="col">Lokasi</th>
                      {{-- Dinonaktifkan 2026-10-23: pilih lokasi dengan klik baris, kolom Actions tidak dipakai. --}}
                      {{-- <th scope="col">Actions</th> --}}

                    </tr>
                  </thead>


                  {{-- Dinonaktifkan 2026-10-23: baris contoh "-" dengan tombol + (diisi saat modal dibuka).
                  <tbody id="tabel_data_add_list_lokasi" class="text-left" >

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
                  --}}
                  <tbody id="tabel_data_add_list_lokasi" class="text-left" ></tbody>


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



        <!-- start modal list barang -->
        <div class="modal fade"  id="formAddListBarang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">List Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- <h1>Tes Modal</h1> -->







                <div class="container-fluid mt-4">
                  <div class="row text-center" style="overflow:auto;">
                    <div class="col-12">


                    <table id="tabel_add_list_barang" class="table table-bordered table-striped"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Kode Barang</th>
                          <th scope="col">Nama Barang</th>
                          {{-- Dinonaktifkan 2026-10-23: pilih barang dengan klik baris, kolom Actions tidak dipakai. --}}
                          {{-- <th scope="col">Actions</th> --}}

                        </tr>
                      </thead>


                      {{-- Dinonaktifkan 2026-10-23: baris contoh "-" dengan tombol + (diisi saat modal dibuka).
                      <tbody id="tabel_data_add_list_barang" class="text-left" >

                        <tr >

                          <td>-</td>
                          <td>-</td>


                            <td class="text-center">
                              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                              <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-plus"></i></button>
                            </td>
                      </tr>
                      </tbody>
                      --}}
                      <tbody id="tabel_data_add_list_barang" class="text-left" ></tbody>


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
        <!-- End modal list barang-->

</div>













@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">

let noUrutTable = 1
let nomor = ''

// Dinonaktifkan 2026-10-23: tabel pemilih sekarang dibuat saat modal dibuka (klik baris, tanpa kolom Actions).
// $(document).ready(function(){
//   $("#tabel_add_list_lokasi").DataTable({ "lengthChange": false, "paging": false , "columnDefs": [
//       {  "className": "text-center", "targets": [3] },
//   ]});
//
//   $("#tabel_add_list_barang").DataTable({ "lengthChange": false, "paging": false , "columnDefs": [
//       {  "className": "text-center", "targets": [2] },
//   ]});
//
//
// });

// Ditambahkan 2026-10-23: daftar barang (±19 ribu, ±3,5 dtk) dimuat di belakang layar saat halaman
// terbuka, supaya pemilih barang langsung tampil saat tombol kaca pembesar diklik.
let listBarang = []
let listBarangReq = null
let listLokasi = []
$(document).ready(function(){
  listBarangReq = $.ajax({
    url: "{!! url('reportkartustoklistbarang') !!}",
    type: "get",
    success: function(res) { if (!listBarang.length) listBarang = res },
    complete: function() { listBarangReq = null }
  })
});

function closeTable () {

  document.getElementById("showTableReport").style.display = "none"
}

// Dinonaktifkan 2026-10-23: daftar dengan tombol + per baris. Diganti: dimuat sekali, pilih dengan klik baris.
// function buttonAddLokasi () {
//   // formAddListLokasi
//   $('#tabel_add_list_lokasi').DataTable().destroy();
//
//   $.ajax({
//     url: "{!! url('reportkartustoklistlokasi') !!}",
//     type: "get",
//     async: false,
//     success: function(res) {
//     console.log(res)
//     let rowTable = ''
//     res.forEach((item, i) => {
//       // KodeGdg, NamaLokasi , BarcodeLoc
//       rowTable += `<tr>
//       <td>${item.BarcodeLoc}</td>
//       <td>${item.KodeGdg}</td>
//       <td>${item.NamaLokasi}</td>
//       <td class="text-center">
//         <button class="btn btn-primary btn-sm" type="button" onclick="buttonAddAddLokasi('${item.BarcodeLoc}')" ><i class="bi bi-plus"></i></button>
//       </td>
//       </tr>`
//
//     });
//
//
//     document.getElementById("tabel_data_add_list_lokasi").innerHTML = rowTable
//
//     $("#tabel_add_list_lokasi").DataTable({ "lengthChange": false, "paging": false , "columnDefs": [
//         {  "className": "text-center", "targets": [3] },
//     ]})
//
//
//     // tabel_data_add_list_lokasi
//
//
//   }})
//
//   $("#formAddListLokasi").modal('toggle');
// }

function buttonAddLokasi () {
  // wmsProses menjalankan pekerjaannya setelah animasi memuat tergambar (tidak langsung), jadi
  // tabel dibuat sesudah daftar selesai dimuat.
  if (!listLokasi.length) {
    wmsProses('Memuat daftar lokasi...', function () {
    $.ajax({
      url: "{!! url('reportkartustoklistlokasi') !!}",
      type: "get",
      async: false,
      success: function(res) { listLokasi = res },
      error: function () { alertify.error('Gagal memuat daftar lokasi, silakan coba lagi') }
    })
    if (listLokasi.length) tampilListLokasi()
    })
  } else {
    tampilListLokasi()
  }
}

function tampilListLokasi () {
  if ($.fn.DataTable.isDataTable('#tabel_add_list_lokasi')) {
    $('#tabel_add_list_lokasi').DataTable().search('').draw()
  } else {
    $("#tabel_add_list_lokasi").DataTable({
      data: listLokasi.map((item) => ({ BarcodeLoc: item.BarcodeLoc || '', KodeGdg: item.KodeGdg || '', NamaLokasi: item.NamaLokasi || '' })),
      columns: [{ data: 'BarcodeLoc' }, { data: 'KodeGdg' }, { data: 'NamaLokasi' }],
      autoWidth: false, // tabel dibuat saat modal masih tersembunyi; tanpa ini lebar kolom jadi sempit
      lengthChange: true,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 10,
      createdRow: function (row) { row.classList.add('pick-row') },
      language: { lengthMenu: 'Tampilkan _MENU_', emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' }
    })
    $('#tabel_add_list_lokasi tbody').on('click', 'tr.pick-row', function () {
      const data = $('#tabel_add_list_lokasi').DataTable().row(this).data()
      if (data) buttonAddAddLokasi(data.BarcodeLoc)
    })
  }

  $("#formAddListLokasi").modal('toggle');
}

// Dinonaktifkan 2026-10-23: daftar ±19 ribu barang (±1,4 MB) dimuat ulang tiap klik lalu semua baris
// digambar sekaligus dengan tombol + per baris (lambat). Diganti: dimuat sekali di belakang layar,
// 10 baris per halaman dengan pencarian, pilih dengan klik baris (sama dengan menu Gudang 031/034).
// function buttonAddBarang () {
//   // formAddListLokasi
//   $('#tabel_add_list_barang').DataTable().destroy();
//
//   $.ajax({
//     url: "{!! url('reportkartustoklistbarang') !!}",
//     type: "get",
//     async: false,
//     success: function(res) {
//     console.log(res)
//     let rowTable = ''
//     res.forEach((item, i) => {
//       // KodeGdg, NamaLokasi , BarcodeLoc
//       rowTable += `<tr>
//       <td>${item.KODEBRG}</td>
//       <td>${item.NAMABRG}</td>
//       <td class="text-center">
//         <button class="btn btn-primary btn-sm" type="button" onclick="buttonAddAddBarang('${item.KODEBRG}')" ><i class="bi bi-plus"></i></button>
//       </td>
//       </tr>`
//
//     });
//
//
//     document.getElementById("tabel_data_add_list_barang").innerHTML = rowTable
//
//     $("#tabel_add_list_barang").DataTable({ "lengthChange": false, "paging": false , "columnDefs": [
//         {  "className": "text-center", "targets": [2] },
//     ]})
//
//
//     // tabel_data_add_list_lokasi
//
//
//   }})
//
//   $("#formAddListBarang").modal('toggle');
// }

function buttonAddBarang () {
  if (!listBarang.length) {
    // Belum selesai dimuat di belakang layar: batalkan, muat langsung dengan animasi memuat.
    if (listBarangReq) { listBarangReq.abort(); listBarangReq = null }
    wmsProses('Memuat daftar barang...', function () {
    $.ajax({
      url: "{!! url('reportkartustoklistbarang') !!}",
      type: "get",
      async: false,
      success: function(res) { listBarang = res },
      error: function () { alertify.error('Gagal memuat daftar barang, silakan coba lagi') }
    })
    if (listBarang.length) tampilListBarang()
    })
  } else {
    tampilListBarang()
  }
}

function tampilListBarang () {
  if ($.fn.DataTable.isDataTable('#tabel_add_list_barang')) {
    $('#tabel_add_list_barang').DataTable().search('').draw()
  } else {
    $("#tabel_add_list_barang").DataTable({
      data: listBarang.map((item) => ({ KODEBRG: item.KODEBRG || '', NAMABRG: item.NAMABRG || '' })),
      deferRender: true,
      autoWidth: false, // tabel dibuat saat modal masih tersembunyi; tanpa ini lebar kolom jadi sempit
      columns: [{ data: 'KODEBRG' }, { data: 'NAMABRG' }],
      lengthChange: true,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      pageLength: 10,
      createdRow: function (row) { row.classList.add('pick-row') },
      language: { lengthMenu: 'Tampilkan _MENU_', emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' }
    })
    $('#tabel_add_list_barang tbody').on('click', 'tr.pick-row', function () {
      const data = $('#tabel_add_list_barang').DataTable().row(this).data()
      if (data) buttonAddAddBarang(data.KODEBRG)
    })
  }

  $("#formAddListBarang").modal('toggle');
}

function buttonAddAddBarang (kodebrg) {
  console.log(kodebrg)
  document.getElementById("input_barang").value = kodebrg
  $("#formAddListBarang").modal('toggle');
}

function buttonAddAddLokasi (barcodeLoc) {
  console.log(barcodeLoc)
  document.getElementById("input_lokasi").value = barcodeLoc
  $("#formAddListLokasi").modal('toggle');
}



// Dinonaktifkan 2026-10-23: Keterangan kosong tertulis "null", qty 0 tertulis "0" (bukan 0.00), layar membeku
// tanpa tanda memuat, kalau server gagal tidak ada pesan. Bagian setelah "return" memang tidak pernah dijalankan.
// function makeTable () {
//   noUrutTable = 1
//   console.log('makeTable')
//   let awalBulan = $("#input_awal_bulan").val();
//   let awalTahun= $("#input_awal_tahun").val();
//   let akhirBulan = $("#input_akhir_bulan").val();
//   let akhirTahun = $("#input_akhir_tahun").val();
//   let kodebrg = $("#input_barang").val();
//   let kodelokasi = $("#input_lokasi").val();
//   // let kodelokasi = 'GSML'
//   let satuan = $("#input_satuan").val();
//
//
//
//
//   console.log('awalBulan' , awalBulan)
//   console.log('awalTahun' , awalTahun)
//   console.log('akhirBulan' , akhirBulan)
//   console.log('akhirTahun' , akhirTahun)
//   console.log('kodebrg' , kodebrg)
//   console.log('kodelokasi' , kodelokasi)
//   console.log('satuan' , satuan)
//
//   if (!kodebrg) {
//     alertify.warning('Isi kodebarang')
//     return
//   }
//
//   if (!kodelokasi) {
//     alertify.warning('Isi kodelokasi')
//     return
//   }
//
//   if (!satuan) {
//     alertify.warning('Isi satuan')
//     return
//   }
//
//   // return
//   // reportkartustokspreport
//
//   $.ajax({
//     url: "{!! url('reportkartustokspreport') !!}",
//     type: "get",
//     async: false,
//     data: {
//       awalBulan, awalTahun, akhirBulan, akhirTahun, kodebrg, kodelokasi, satuan
//     },
//     success: function(res) {
//     console.log(res)
//
//     // tabel_data_add_list_lokasi
//     let tempQntSaldo = 0
//     let rowTable = ''
//     res.forEach((item, i) => {
//       let date1 = ""
//       if (item.Tanggal) {
//         let date = new Date(item.Tanggal);
//         let day = ("0" + date.getDate()).slice(-2);
//         let month = ("0" + (date.getMonth() + 1)).slice(-2);
//         date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
//       }
//       tempQntSaldo += Number(item.QntSaldo)
//       console.log('====',tempQntSaldo)
//       rowTable += `
//       <tr>
//       <td>${date1}</td>
//       <td>${item.NoBukti}</td>
//       <td>${item.Tipe}</td>
//       <td>${item.Keterangan}</td>
//       <td>${item.SATUAN}</td>
//       <td>${item.QntDB ? parseFloat(item.QntDB).toFixed(2) : 0.00}</td>
//       <td>${item.QntCR ? parseFloat(item.QntCR).toFixed(2) : 0.00}</td>
//       <td>${tempQntSaldo ? parseFloat(tempQntSaldo).toFixed(2) : 0.00}</td>
//       </tr>`
//     });
//
//
//
//
//     document.getElementById("tabel_data").innerHTML = rowTable
//
//   }});
//
//   let rowPeriode = `<th colspan="13"  style="text-align: left; ">Gudang: ${kodelokasi} <br/> Barang: ${kodebrg} <br/> Periode: ${awalBulan} - ${awalTahun} s/d ${akhirBulan} - ${akhirTahun}</th>`
//   document.getElementById("periodeTable").innerHTML = rowPeriode
//   document.getElementById("showTableReport").style.display = "block"
//   return
//
//   // let _token = $("#_token").val();
//   // console.log(_token)
//
//
//   // let periodeTable = `<th colspan="13"  style="text-align: left; font-weight: bold;">PERIODE: ${date1} s/d ${date2}</th>`
//   //
//
//   $.ajax({
//     url: "{!! url('reportkartustokspreport') !!}",
//     type: "get",
//     async: false,
//     data: {
//       date1,
//       date2,
//       input1,
//       input2,
//       input3
//     },
//     success: function(res) {
//       console.log(res)
//       let rowTable = ""
//
//
//
//
//
//
//       res.forEach((item, i) => {
//         // console.log(item)
//         // console.log(item.BuktiLPB)
//         //
//         let dateDokumen = new Date(item.TanggalDokumen);
//         let dayDokumen = ("0" + dateDokumen.getDate()).slice(-2);
//         let monthDokumen = ("0" + (dateDokumen.getMonth() + 1)).slice(-2);
//         let dateShowDokumen = dateDokumen.getFullYear()+"-"+(monthDokumen)+"-"+(dayDokumen) ;
//         //
//         let dateLPB = new Date(item.TanggalLPB);
//         let dayLPB = ("0" + dateLPB.getDate()).slice(-2);
//         let monthLPB = ("0" + (dateLPB.getMonth() + 1)).slice(-2);
//         let dateShowLPB = dateLPB.getFullYear()+"-"+(monthLPB)+"-"+(dayLPB) ;
//         //
//         let dateInput = new Date(item.TglInput);
//         let dayInput = ("0" + dateInput.getDate()).slice(-2);
//         let monthInput = ("0" + (dateInput.getMonth() + 1)).slice(-2);
//         let dateShowInput = dateInput.getFullYear()+"-"+(monthInput)+"-"+(dayInput) ;
//
//         if (nomor == item.NomorDokumen) {
//           rowTable += `<tr style="text-align: center">
//
//             <td style="border: 1px solid black;"></td>
//             <td style="border: 1px solid black;"></td>
//
//             <td style="border: 1px solid black;">${item.NomorDokumen}</td>
//             <td style="border: 1px solid black;">${dateShowDokumen}</td>
//             <td style="border: 1px solid black;">${item.BuktiLPB}</td>
//             <td style="border: 1px solid black;">${dateShowLPB}</td>
//             <td style="border: 1px solid black;">${item.NamaSupp}</td>
//             <td style="border: 1px solid black;">${item.KodeBrg}</td>
//             <td style="border: 1px solid black;">${item.NamaBrg}</td>
//             <td style="border: 1px solid black;">${item.Satuan}</td>
//             <td style="border: 1px solid black;">${item.Jumlah}</td>
//             <td style="border: 1px solid black;">${item.NilaiBrg}</td>
//             <td style="border: 1px solid black;">${dateShowInput}</td>
//
//
//           </tr>`
//         } else {
//           if (noUrutTable !== 1) {
//             rowTable += `<tr style="text-align: center">
//
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//               <td style="border: 1px solid black;"></td>
//
//
//             </tr>`
//           }
//           rowTable += `<tr style="text-align: center">
//
//             <td style="border: 1px solid black;">${noUrutTable}</td>
//             <td style="border: 1px solid black;">${item.JenisDokumen}</td>
//
//             <td style="border: 1px solid black;">${item.NomorDokumen}</td>
//             <td style="border: 1px solid black;">${dateShowDokumen}</td>
//             <td style="border: 1px solid black;">${item.BuktiLPB}</td>
//             <td style="border: 1px solid black;">${dateShowLPB}</td>
//             <td style="border: 1px solid black;">${item.NamaSupp}</td>
//             <td style="border: 1px solid black;">${item.KodeBrg}</td>
//             <td style="border: 1px solid black;">${item.NamaBrg}</td>
//             <td style="border: 1px solid black;">${item.Satuan}</td>
//             <td style="border: 1px solid black;">${item.Jumlah}</td>
//             <td style="border: 1px solid black;">${item.NilaiBrg}</td>
//             <td style="border: 1px solid black;">${dateShowInput}</td>
//
//
//           </tr>`
//
//           noUrutTable ++
//         }
//         nomor = item.NomorDokumen
//
//
//       });
//
//       if(!res.length) {
//         rowTable = `<tr style="text-align: center">
//
//           <td colspan=13 style="border: 1px solid black;">Tidak ada data ditemukan</td>
//
//
//
//         </tr>`
//       }
//       document.getElementById("periodeTable").innerHTML = periodeTable
//
//       document.getElementById("tabel_data").innerHTML = rowTable
//   }})
// }

// Catatan: Sp_reportkartuStockLokasi di DBSMLBARU membaca vwKartuStock (per GUDANG); pembacaan per lokasi
// (vwKartuStockSIG) dikomentari di SP. Akibatnya kode lokasi (mis. GSML3LDIS) tidak menghasilkan data,
// kode gudang (mis. GSML3) menghasilkan data. SP tidak diubah dari sini.
function makeTable () {
  let awalBulan = $("#input_awal_bulan").val();
  let awalTahun= $("#input_awal_tahun").val();
  let akhirBulan = $("#input_akhir_bulan").val();
  let akhirTahun = $("#input_akhir_tahun").val();
  let kodebrg = $("#input_barang").val();
  let kodelokasi = $("#input_lokasi").val();
  let satuan = $("#input_satuan").val();

  if (!kodebrg) {
    alertify.warning('Isi kodebarang')
    return
  }

  if (!kodelokasi) {
    alertify.warning('Isi kodelokasi')
    return
  }

  if (!satuan) {
    alertify.warning('Isi satuan')
    return
  }

  wmsProses('Memuat data...', function () {
  let berhasil = false
  $.ajax({
    url: "{!! url('reportkartustokspreport') !!}",
    type: "get",
    async: false,
    data: {
      awalBulan, awalTahun, akhirBulan, akhirTahun, kodebrg, kodelokasi, satuan
    },
    success: function(res) {
    berhasil = true
    let tempQntSaldo = 0
    let rowTable = ''
    res.forEach((item, i) => {
      let date1 = ""
      if (item.Tanggal) {
        let date = new Date(item.Tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }
      tempQntSaldo += Number(item.QntSaldo)
      rowTable += `
      <tr>
      <td>${date1}</td>
      <td>${wmsTeks(item.NoBukti)}</td>
      <td>${wmsTeks(item.Tipe)}</td>
      <td>${wmsTeks(item.Keterangan)}</td>
      <td>${wmsTeks(item.SATUAN)}</td>
      <td>${item.QntDB ? parseFloat(item.QntDB).toFixed(2) : '0.00'}</td>
      <td>${item.QntCR ? parseFloat(item.QntCR).toFixed(2) : '0.00'}</td>
      <td>${tempQntSaldo ? parseFloat(tempQntSaldo).toFixed(2) : '0.00'}</td>
      </tr>`
    });

    if (!res.length) {
      rowTable = `<tr style="text-align: center"><td colspan=8 style="border: 1px solid black;">Tidak ada data ditemukan</td></tr>`
    }

    document.getElementById("tabel_data").innerHTML = rowTable
    },
    error: function () {
      alertify.error('Gagal memuat data, silakan coba lagi')
    }
  });
  if (!berhasil) return

  let rowPeriode = `<th colspan="13"  style="text-align: left; ">Gudang: ${wmsTeks(kodelokasi)} <br/> Barang: ${wmsTeks(kodebrg)} <br/> Periode: ${awalBulan} - ${awalTahun} s/d ${akhirBulan} - ${akhirTahun}</th>`
  document.getElementById("periodeTable").innerHTML = rowPeriode
  document.getElementById("showTableReport").style.display = "block"
  })
}

// Dinonaktifkan 2026-10-23: unduh lewat data-URI - karakter "#" memotong isi file. Diganti wmsUnduhXls (Blob).
// function exportTableToExcel(tableID, filename = '') {
//
//   // makeTable()
//   // return
//   var downloadLink;
//   var dataType = 'application/vnd.ms-excel';
//   var tableSelect = document.getElementById(tableID);
//   var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
//
//   // Specify file name
//   filename = filename?filename+'.xls':'excel_data.xls';
//
//   // Create download link element
//   downloadLink = document.createElement("a");
//
//   document.body.appendChild(downloadLink);
//
//   if(navigator.msSaveOrOpenBlob){
//       var blob = new Blob(['\ufeff', tableHTML], {
//           type: dataType
//       });
//       navigator.msSaveOrOpenBlob( blob, filename);
//   }else{
//       // Create a link to the file
//       downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
//
//       // Setting the file name
//       downloadLink.download = filename;
//
//       //triggering the function
//       downloadLink.click();
//   }
// }

function exportTableToExcel(tableID, filename = '') {
  var tableSelect = document.getElementById(tableID);
  filename = filename?filename+'.xls':'excel_data.xls';
  wmsUnduhXls(tableSelect.outerHTML, filename);
}

</script>




@endsection
