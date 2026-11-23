@extends('newmasterTest')
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Report Outstanding Dispatch')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">


 <div class="col-12 text-left">
    <div class="row">

    {{-- <h3>Report Outstanding Dispatch</h3> - judul pindah ke header (page-title); tombol Filter pindah ke bar tab --}}
     <!-- <button  class="btn btn-primary" style="height: 40px; margin-left: 10px" onclick="buttonOpenFilter()">Filter</button> -->
  </div>
  </div>


  </div>
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <svg id="barcode"></svg> -->
<!-- <button onclick="loadAll()">tes</button>
<button onclick="loadAll1()">tes1</button>
<button onclick="loadAll2()">tes2</button> -->
</div>

<!-- <div class="container"> -->


<div id="printContainer" style="display:none">

</div>

<div id="tempPrintContainer" style="display:none">

</div>

<div id="contentContainer" class="container-fluid">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />


  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />


  <div class="card mb-3 tab-card">
    <div class="card-body wms-tab-bar">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Report Stok Gudang</a>
      </div>
      <div class="wms-tab-aksi">
        <button data-placement="top" title="Filter Date" class="btn btn-secondary btn-sm" type="button" onclick="buttonOpenFilter()"  ><i class="bi bi-funnel"></i></button>
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

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">N0. Bukti</th>
                          <th scope="col">Trans.</th>
                          <th scope="col">Tanggal</th>
                          <th scope="col">Kode Barang</th>
                          <th scope="col">Nama Barang</th>
                          <th scope="col">Qnt </th>
                          <th scope="col">Qnt Terima</th>
                          <th scope="col">Qnt Out</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">Gudang</th>
                          <th scope="col">Customer</th>
                          <th scope="col">User QC</th>
                          <th scope="col">Tgl QC</th>
                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr>
                          <td>{{ $listData[$i]->NOBUKTI }}</td>
                          <td>{{ $listData[$i]->JENISTRANSAKSI }}</td>
                          <td>{{ $listData[$i]->TANGGAL }}</td>
                          <td>{{ $listData[$i]->KODEBRG }}</td>
                          <td>{{ $listData[$i]->NAMABARANG }}</td>
                          {{-- Dinonaktifkan 2026-10-23: (int) membuat qty desimal < 1 tampil 0.00.
                          <td>{{ (int)$listData[$i]->QNT ? $listData[$i]->QNT : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->QNTTERIMA ? $listData[$i]->QNTTERIMA : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->QNTOUT ? $listData[$i]->QNTOUT : '0.00' }}</td>
                          --}}
                          <td>{{ (float)$listData[$i]->QNT ? $listData[$i]->QNT : '0.00' }}</td>
                          <td>{{ (float)$listData[$i]->QNTTERIMA ? $listData[$i]->QNTTERIMA : '0.00' }}</td>
                          <td>{{ (float)$listData[$i]->QNTOUT ? $listData[$i]->QNTOUT : '0.00' }}</td>
                          <td>{{ $listData[$i]->SATUAN }}</td>
                          <td>{{ $listData[$i]->NAMAGDG }}</td>
                          <td>{{ $listData[$i]->NAMACUSTSUPP }}</td>
                          <td>{{ $listData[$i]->USERQC }}</td>
                          <td>{{ $listData[$i]->TGLQC }}</td>
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


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" style=" " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter Date</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <div class="row">


                <div class="container-fluid">
                <div style="background-color: #E8E8E8; " class="rounded">
                  <input type="date" class="form-control" id="input_date" value="{!! date('Y-m-d') !!}">



              </div>








              </div>



          </div>

        </div>


      </div>




      <div class="modal-footer">
       <button type="button" class="btn btn-primary" onclick="submitFilter()">Submit</button>
      </div>


    </div>
  </div>
</div>
<!-- End modal add-->




@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>


<script type="text/javascript">



$(document).ready(function(){

      $("#tabel").DataTable({
        // 'order': [[0, 'asc']],
        "lengthChange": false,
         "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [2] },
        // Dinonaktifkan 2026-10-23: hanya kolom 5; semua kolom qty dibuat rata kanan.
        // {  "className": "text-right", "targets": [5],  },
        {  "className": "text-right", "targets": [5, 6, 7],  },
      ]});
});

function buttonOpenFilter () {
  // console.log(document.getElementById("filterContainer").style.display)
  // let display = document.getElementById("filterContainer").style.display
  // if (display == "block") {
  //   document.getElementById("filterContainer").style.display = "none";
  // } else {
  //
  //   document.getElementById("filterContainer").style.display = "block";
  // }
  $("#form").modal('toggle')


}

// Dinonaktifkan 2026-10-23: nilai kosong tertulis "null"; layar membeku tanpa tanda memuat;
// kalau server gagal tidak ada pesan.
// function submitFilter () {
//   // console.log(submitFilter)
//   let tanggal = new Date($("#input_date").val())
//   let year = tanggal.getFullYear();
//   let month = tanggal.getMonth() + 1
//
//   console.log(year,month)
//   console.log(tanggal)
//   let _token = $("#_token").val();
//
//   $.ajax({
//     url: "{!! url('reportoutdispatchspfilter') !!}",
//     type: "post",
//     async: false,
//     data: {
//       year,
//       month,
//       _token
//     },
//     success: function(res) {
//     console.log(res)
//     $('#tabel').DataTable().destroy();
//     let rowTable = ""
//     // tabel_data_add_list_lokasi
//     res.forEach((item, i) => {
//
//       rowTable += `
//       <tr>
//
//       <td>${item.NOBUKTI }</td>
//       <td>${item.JENISTRANSAKSI }</td>
//       <td>${item.TANGGAL }</td>
//       <td>${item.KODEBRG }</td>
//       <td>${item.NAMABARANG }</td>
//       <td>${Number(item.QNT )? item.QNT : '0.00' }</td>
//       <td>${Number(item.QNTTERIMA) ? item.QNTTERIMA : '0.00' }</td>
//       <td>${Number(item.QNTOUT) ? item.QNTOUT : '0.00' }</td>
//       <td>${item.SATUAN }</td>
//       <td>${item.NAMAGDG }</td>
//       <td>${item.NAMACUSTSUPP }</td>
//       <td>${item.USERQC }</td>
//       <td>${item.TGLQC }</td>
//
//
//
//       </tr>`
//     });
//
//
//
//
//     document.getElementById("tabel_data").innerHTML = rowTable
//     $("#tabel").DataTable({
//       // 'order': [[0, 'asc']],
//       "lengthChange": false,
//        "paging": false ,
//          "columnDefs": [
//       // { "type": "date", "targets": [2] },
//       {  "className": "text-right", "targets": [5],  },
//     ]});
//   }});
//
//
//
//   $("#form").modal('toggle')
// }

function submitFilter () {
  let tanggal = new Date($("#input_date").val())
  let year = tanggal.getFullYear();
  let month = tanggal.getMonth() + 1
  let _token = $("#_token").val();

  $("#form").modal('hide')
  wmsProses('Memuat data...', function () {
  $.ajax({
    url: "{!! url('reportoutdispatchspfilter') !!}",
    type: "post",
    async: false,
    data: {
      year,
      month,
      _token
    },
    success: function(res) {
    $('#tabel').DataTable().destroy();
    let rowTable = ""
    res.forEach((item, i) => {

      rowTable += `
      <tr>
      <td>${wmsTeks(item.NOBUKTI)}</td>
      <td>${wmsTeks(item.JENISTRANSAKSI)}</td>
      <td>${wmsTeks(item.TANGGAL)}</td>
      <td>${wmsTeks(item.KODEBRG)}</td>
      <td>${wmsTeks(item.NAMABARANG)}</td>
      <td>${Number(item.QNT) ? item.QNT : '0.00' }</td>
      <td>${Number(item.QNTTERIMA) ? item.QNTTERIMA : '0.00' }</td>
      <td>${Number(item.QNTOUT) ? item.QNTOUT : '0.00' }</td>
      <td>${wmsTeks(item.SATUAN)}</td>
      <td>${wmsTeks(item.NAMAGDG)}</td>
      <td>${wmsTeks(item.NAMACUSTSUPP)}</td>
      <td>${wmsTeks(item.USERQC)}</td>
      <td>${wmsTeks(item.TGLQC)}</td>
      </tr>`
    });

    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
      "lengthChange": false,
       "paging": false ,
         "columnDefs": [
      {  "className": "text-right", "targets": [5, 6, 7],  },
    ]});
    },
    error: function () {
      alertify.error('Gagal memuat data, silakan coba lagi')
    }
  });
  })
}


</script>


{{-- } - karakter sisa di luar <script> (dulu tampil sebagai teks di halaman) --}}


@endsection
