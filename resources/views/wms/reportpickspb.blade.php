@extends('newmasterTest')
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Report Picking List - SPB')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">

 <div class="col-12 text-left">
    <div class="row">

    {{-- <h3>Report Picking List - SPB</h3> - judul pindah ke header (page-title); tombol Filter pindah ke bar tab --}}
     <!-- <button  class="btn btn-primary" style="height: 40px; margin-left: 10px" onclick="buttonOpenFilter()">Filter</button> -->
  </div>
  </div>


  </div>
<!-- <button onclick="loadAll()">tes</button> -->
<!-- <svg id="barcode"></svg> -->
<!--<button onclick="submitFilter()">tes</button>
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Report Pickinglist - SPB</a>
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
            {{-- Dinonaktifkan 2026-10-23: <div class="col-10" style="overflow:auto;"> - lebar wadah tabel disamakan dengan In Bound/Out Bound (col-12). --}}
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">No PL</th>
                          <th scope="col">Tgl Pickinglist</th>
                          <th scope="col">Kode Barang</th>
                          <th scope="col">Nama Barang</th>
                          <th scope="col">Qnt PList</th>
                          <th scope="col">Qnt Picking</th>
                          <th scope="col">Qnt Reject</th>
                          <th scope="col">No SPB/RSPB</th>
                          <th scope="col">Qnt SPB</th>
                          <th scope="col">Qnt RSPB</th>
                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >
                        {{-- Dinonaktifkan 2026-10-23: (int) membuat qty desimal < 1 (mis. 0.50) tampil 0.00.
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr>
                          <td>{{ $listData[$i]->nopicking }}</td>
                          <td>{{ $listData[$i]->tglpicking }}</td>
                          <td>{{ $listData[$i]->kodebrg }}</td>
                          <td>{{ $listData[$i]->namabrg }}</td>
                          <td>{{ (int)$listData[$i]->qntpickinglist ? $listData[$i]->qntpickinglist : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->qntpicking ? $listData[$i]->qntpicking : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->qntrejectpicking ? $listData[$i]->qntrejectpicking : '0.00' }}</td>
                          <td>{{ $listData[$i]->nospb }}</td>
                          <td>{{ (int)$listData[$i]->qntspb ? $listData[$i]->qntspb : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->qntrspb ? $listData[$i]->qntrspb : '0.00' }}</td>

                        </tr>
                        @endfor
                        --}}
                        @foreach ($listData as $d)
                        <tr>
                          <td>{{ $d->nopicking }}</td>
                          <td>{{ $d->tglpicking }}</td>
                          <td>{{ $d->kodebrg }}</td>
                          <td>{{ $d->namabrg }}</td>
                          <td>{{ (float)$d->qntpickinglist ? $d->qntpickinglist : '0.00' }}</td>
                          <td>{{ (float)$d->qntpicking ? $d->qntpicking : '0.00' }}</td>
                          <td>{{ (float)$d->qntrejectpicking ? $d->qntrejectpicking : '0.00' }}</td>
                          <td>{{ $d->nospb }}</td>
                          <td>{{ (float)$d->qntspb ? $d->qntspb : '0.00' }}</td>
                          <td>{{ (float)$d->qntrspb ? $d->qntrspb : '0.00' }}</td>
                        </tr>
                        @endforeach
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
       {{-- Dinonaktifkan 2026-10-23: label sisa uji "Submitxx". --}}
       {{-- <button type="button" class="btn btn-primary" onclick="submitFilter()">Submitxx</button> --}}
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
       // "lengthChange": false,
         // "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [2] },
        // Dinonaktifkan 2026-10-23: hanya kolom 5; semua kolom qty dibuat rata kanan.
        // {  "className": "text-right", "targets": [5],  },
        {  "className": "text-right", "targets": [4, 5, 6, 8, 9],  },
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

// Dinonaktifkan 2026-10-23: memanggil URL milik Report SO-SPB (reportsospbspfilter), jadi tabel terisi
// "undefined" setelah Submit. Nilai kosong tertulis "null"; layar membeku tanpa tanda memuat.
// function submitFilter () {
//   let tanggal = $("#input_date").val();
//   console.log(tanggal)
//   let _token = $("#_token").val();
//
//   $.ajax({
//     url: "{!! url('reportsospbspfilter') !!}",
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
//     res.forEach((item, i) => {
//
//       rowTable += `
//       <tr>
//
//         <td>${item.nopicking}</td>
//         <td>${item.tglpicking}</td>
//         <td>${item.kodebrg}</td>
//         <td>${item.namabrg}</td>
//         <td>${Number(item.qntpickinglist) ? item.qntpickinglist : '0.00'}</td>
//          <td>${Number(item.qntpicking) ? item.qntpicking : '0.00'}</td>
//          <td>${Number(item.qntrejectpicking) ? item.qntrejectpicking : '0.00'}</td>
//          <td>${item.nospb}</td>
//          <td>${Number(item.qntspb) ? item.qntspb : '0.00'}</td>
//          <td>${Number(item.qntrspb) ? item.qntrspb : '0.00'}</td>
//
//       </tr>`
//     });
//
//     document.getElementById("tabel_data").innerHTML = rowTable
//     $("#tabel").DataTable({
//          "columnDefs": [
//       {  "className": "text-right", "targets": [5],  },
//     ]});
//   }});
//
//   $("#form").modal('toggle')
// }

// Catatan: Sp_ReportPickSpb menampilkan SEMUA outstanding (filter periode di SP dikomentari sejak
// versi lama), jadi Submit = muat ulang data terbaru.
function submitFilter () {
  let tanggal = $("#input_date").val();
  let _token = $("#_token").val();

  $("#form").modal('hide')
  wmsProses('Memuat data...', function () {
  $.ajax({
    url: "{!! url('reportpickspbspfilter') !!}",
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
        <td>${wmsTeks(item.nopicking)}</td>
        <td>${wmsTeks(item.tglpicking)}</td>
        <td>${wmsTeks(item.kodebrg)}</td>
        <td>${wmsTeks(item.namabrg)}</td>
        <td>${Number(item.qntpickinglist) ? item.qntpickinglist : '0.00'}</td>
        <td>${Number(item.qntpicking) ? item.qntpicking : '0.00'}</td>
        <td>${Number(item.qntrejectpicking) ? item.qntrejectpicking : '0.00'}</td>
        <td>${wmsTeks(item.nospb)}</td>
        <td>${Number(item.qntspb) ? item.qntspb : '0.00'}</td>
        <td>${Number(item.qntrspb) ? item.qntrspb : '0.00'}</td>
      </tr>`
    });

    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
         "columnDefs": [
      {  "className": "text-right", "targets": [4, 5, 6, 8, 9],  },
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
