@extends('newmasterTest')
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Report Stok Lokasi')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">


  <div class="col-12 text-left">
    <div class="row">

    {{-- <h3>Report Stok Lokasi</h3> - judul pindah ke header (page-title); tombol Filter pindah ke bar tab --}}
     <!-- <button  class="btn btn-primary" style="height: 40px; margin-left: 10px" onclick="buttonOpenFilter()">Filter</button> -->
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
  {{-- tombol Export to Excel pindah ke bar tab --}}

  <div class="card mb-3 tab-card">
    <div class="card-body wms-tab-bar">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Report Stok Lokasi</a>
      </div>
      <div class="wms-tab-aksi">
        <button data-placement="top" title="Filter Date" class="btn btn-secondary btn-sm" type="button" onclick="buttonOpenFilter()"  ><i class="bi bi-funnel"></i></button>
        <button type="button" class="btn btn-success" onclick="exportTableToExcel('tabel')">Export to Excel</button>
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
                          <th scope="col">Kode Barang</th>
                          <th scope="col">Part Number</th>
                          <th scope="col">Nama Barang</th>
                          <th scope="col">Merk</th>
                          <th scope="col">Gudang</th>
                          <th scope="col">Lokasi</th>
                          <th scope="col">Qnt</th>
                          <th scope="col">Satuan</th>
                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr>
                          <td>{{$listData[$i]->KODEBRG }} </td>
                          <td>{{ $listData[$i]->PartNumber }}</td>
                          <td>{{ $listData[$i]->NAMABRG }}</td>
                          <td>{{ $listData[$i]->NAMAMERK }}</td>
                          <td>{{ $listData[$i]->NMgudang }}</td>
                          <td>{{ $listData[$i]->NamaLokasi }}</td>
                          <td>{{ number_format((float)$listData[$i]->SALDOQNT, 2, '.', '') }}</td>
                          <!-- <td>{{ (int)$listData[$i]->SALDOQNT ? $listData[$i]->SALDOQNT : '0.00' }}</td> -->
                          <td>{{ $listData[$i]->SAT1 }}</td>
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
        {  "className": "text-right", "targets": [6],  },
        {"type" : "string" , "targets" : [0]}
        // {"width" : '100px' , :"targets" : [0,1]}
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

// Dinonaktifkan 2026-10-23: nilai kosong tertulis "null"; layar membeku tanpa tanda memuat;
// kalau server gagal tidak ada pesan.
// function submitFilter () {
//   let tanggal = $("#input_date").val();
//   console.log(tanggal)
//   let _token = $("#_token").val();
//
//   $.ajax({
//     url: "{!! url('reportstoklokasispfilter') !!}",
//     type: "post",
//     async: false,
//     data: {
//       tanggal,
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
//         <td>${item.KODEBRG }</td>
//         <td>${item.PartNumber}</td>
//         <td>${item.NAMABRG}</td>
//         <td>${item.NAMAMERK}</td>
//         <td>${item.NMgudang}</td>
//         <td>${item.NamaLokasi}</td>
//         <td>${Number(item.SALDOQNT) ? Number(item.SALDOQNT).toFixed(2) : '0.00'}</td>
//         <td>${item.SAT1}</td>
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
//         "paging": false ,
//          "columnDefs": [
//       // { "type": "date", "targets": [2] },
//       {  "className": "text-right", "targets": [6],  },
//       {"type" : "string" , "targets" : [0]}
//     ]});
//   }});
//
//
//
//   $("#form").modal('toggle')
// }

function submitFilter () {
  let tanggal = $("#input_date").val();
  let _token = $("#_token").val();

  $("#form").modal('hide')
  wmsProses('Memuat data...', function () {
  $.ajax({
    url: "{!! url('reportstoklokasispfilter') !!}",
    type: "post",
    async: false,
    data: {
      tanggal,
      _token
    },
    success: function(res) {
    $('#tabel').DataTable().destroy();
    let rowTable = ""
    res.forEach((item, i) => {

      rowTable += `
      <tr>
        <td>${wmsTeks(item.KODEBRG)}</td>
        <td>${wmsTeks(item.PartNumber)}</td>
        <td>${wmsTeks(item.NAMABRG)}</td>
        <td>${wmsTeks(item.NAMAMERK)}</td>
        <td>${wmsTeks(item.NMgudang)}</td>
        <td>${wmsTeks(item.NamaLokasi)}</td>
        <td>${Number(item.SALDOQNT) ? Number(item.SALDOQNT).toFixed(2) : '0.00'}</td>
        <td>${wmsTeks(item.SAT1)}</td>
      </tr>`
    });

    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
      "lengthChange": false,
        "paging": false ,
         "columnDefs": [
      {  "className": "text-right", "targets": [6],  },
      {"type" : "string" , "targets" : [0]}
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
