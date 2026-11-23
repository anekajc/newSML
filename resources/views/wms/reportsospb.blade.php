@extends('newmasterTest')
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Report So - SPB')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">

 <div class="col-12 text-left">
    <div class="row">

    {{-- <h3>Report So - SPB</h3> - judul pindah ke header (page-title); tombol Filter pindah ke bar tab --}}
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Report SO - SPB</a>
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
            {{-- Dinonaktifkan 2026-10-23: <div class="col-13" style="overflow:auto;"> - lebar wadah tabel disamakan dengan In Bound/Out Bound (col-12). --}}
            <div class="col-12" style="overflow:auto;">
              <div class="container-fluid">

                    <table id="tabel" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">No So</th>
                          <th scope="col">Tgl So</th>
                          <th scope="col">Kode Barang</th>
                          <th scope="col">Nama Barang</th>
                          <th scope="col">Qnt SO</th>
                          <th scope="col">No PL</th>
                          <th scope="col">Qnt PList</th>
                          <th scope="col">SQnt Picking</th>
                          <th scope="col">Qnt Reject</th>

                          <th scope="col">No Spb</th>
                          <th scope="col">Qnt SPB</th>
                          <th scope="col">No Rspb</th>
                          <th scope="col">Qnt RSPB</th>

                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >
                        {{-- Dinonaktifkan 2026-10-23: (int) membuat qty desimal < 1 (mis. 0.50) tampil 0.00, dan
                             kolom Qnt RSPB memakai syarat qntspb (qty RSPB kosong tampil kosong, bukan 0.00).
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr>
                          <td>{{ $listData[$i]->noso }}</td>
                          <td>{{ $listData[$i]->tglso }}</td>
                          <td>{{ $listData[$i]->kodebrg }}</td>
                          <td>{{ $listData[$i]->namabrg }}</td>
                          <td>{{ (int)$listData[$i]->qntso ? $listData[$i]->qntso : '0.00' }}</td>
                          <td>{{ $listData[$i]->nopl }}</td>
                          <td>{{ (int)$listData[$i]->qntpckinglist ? $listData[$i]->qntpckinglist : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->qntp ? $listData[$i]->qntp : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->qntplreject ? $listData[$i]->qntplreject : '0.00' }}</td>
                          <td>{{ $listData[$i]->nospb }}</td>
                          <td>{{ (int)$listData[$i]->qntspb ? $listData[$i]->qntspb : '0.00' }}</td>

                          <td>{{ $listData[$i]->norspb }}</td>
                          <td>{{ (int)$listData[$i]->qntspb ? $listData[$i]->qntrspb : '0.00' }}</td>
                        </tr>
                        @endfor
                        --}}
                        @foreach ($listData as $d)
                        <tr>
                          <td>{{ $d->noso }}</td>
                          <td>{{ $d->tglso }}</td>
                          <td>{{ $d->kodebrg }}</td>
                          <td>{{ $d->namabrg }}</td>
                          <td>{{ (float)$d->qntso ? $d->qntso : '0.00' }}</td>
                          <td>{{ $d->nopl }}</td>
                          <td>{{ (float)$d->qntpckinglist ? $d->qntpckinglist : '0.00' }}</td>
                          <td>{{ (float)$d->qntp ? $d->qntp : '0.00' }}</td>
                          <td>{{ (float)$d->qntplreject ? $d->qntplreject : '0.00' }}</td>
                          <td>{{ $d->nospb }}</td>
                          <td>{{ (float)$d->qntspb ? $d->qntspb : '0.00' }}</td>
                          <td>{{ $d->norspb }}</td>
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
                <div style=" " class="rounded">
                  <h5>Bulan</h5>
                  <select class="form-control" aria-label="Default select example" id="input_date_month" >
                    <option value=0 selected disabled>Pilih Bulan</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                  </select>
                  <h5 style="margin-top:10px">Tahun</h5>
                  <input type="number" class="form-control" id="input_date_year" value="{!! $periode->tahun !!}">


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
       // "lengthChange": false,
         // "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [2] },
        // Dinonaktifkan 2026-10-23: kolom 5 adalah No PL (teks); yang rata kanan seharusnya kolom qty.
        // {  "className": "text-right", "targets": [5],  },
        {  "className": "text-right", "targets": [4, 6, 7, 8, 10, 12],  },
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
  let bulan = $("#input_date_month").val();
  console.log(bulan)
  if (!bulan) {

    document.getElementById("input_date_month").value = $("#periode_bulan").val();
  }

  $("#form").modal('toggle')


}

// Dinonaktifkan 2026-10-23: kolom Qnt RSPB menampilkan qntspb; nilai kosong tertulis "null";
// layar membeku tanpa tanda memuat; kalau server gagal, tidak ada pesan.
// function submitFilter () {
//   // console.log(tanggal)
//   let _token = $("#_token").val();
//
//
//   let bln = $("#input_date_month").val();
//   let thn = $("#input_date_year").val();
//
//   $.ajax({
//     url: "{!! url('reportsospbspfilter') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token,
//       bln,
//       thn
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
//         <td>${item.noso}</td>
//         <td>${item.tglso}</td>
//         <td>${item.kodebrg}</td>
//         <td>${item.namabrg}</td>
//         <td>${Number(item.qntso) ? item.qntso : '0.00'}</td>
//          <td>${item.nopl}</td>
//          <td>${Number(item.qntpckinglist) ? item.qntpckinglist : '0.00'}</td>
//          <td>${Number(item.qntp) ? item.qntp : '0.00'}</td>
//          <td>${Number(item.qntplreject) ? item.qntplreject : '0.00'}</td>
//          <td>${item.nospb}</td>
//          <td>${Number(item.qntspb) ? item.qntspb : '0.00'}</td>
//          <td>${item.norspb}</td>
//          <td>${Number(item.qntrspb) ? item.qntspb : '0.00'}</td>
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

function submitFilter () {
  let _token = $("#_token").val();
  let bln = $("#input_date_month").val();
  let thn = $("#input_date_year").val();

  $("#form").modal('hide')
  wmsProses('Memuat data...', function () {
  $.ajax({
    url: "{!! url('reportsospbspfilter') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      bln,
      thn
    },
    success: function(res) {
    $('#tabel').DataTable().destroy();
    let rowTable = ""
    res.forEach((item, i) => {

      rowTable += `
      <tr>
        <td>${wmsTeks(item.noso)}</td>
        <td>${wmsTeks(item.tglso)}</td>
        <td>${wmsTeks(item.kodebrg)}</td>
        <td>${wmsTeks(item.namabrg)}</td>
        <td>${Number(item.qntso) ? item.qntso : '0.00'}</td>
        <td>${wmsTeks(item.nopl)}</td>
        <td>${Number(item.qntpckinglist) ? item.qntpckinglist : '0.00'}</td>
        <td>${Number(item.qntp) ? item.qntp : '0.00'}</td>
        <td>${Number(item.qntplreject) ? item.qntplreject : '0.00'}</td>
        <td>${wmsTeks(item.nospb)}</td>
        <td>${Number(item.qntspb) ? item.qntspb : '0.00'}</td>
        <td>${wmsTeks(item.norspb)}</td>
        <td>${Number(item.qntrspb) ? item.qntrspb : '0.00'}</td>
      </tr>`
    });

    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
         "columnDefs": [
      {  "className": "text-right", "targets": [4, 6, 7, 8, 10, 12],  },
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
