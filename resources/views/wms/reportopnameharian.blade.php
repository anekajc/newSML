@extends('newmasterTest')
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Report Opname Harian')
@section('buttons')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row">

 <div class="col-12 text-left">
    <div class="row">

    {{-- <h3>Report Opname Harian</h3> - judul pindah ke header (page-title); tombol Filter pindah ke bar tab --}}
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
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Report Opname Harian</a>
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

                        <!-- nobukti, tanggal, kodebrg, namabrg, nosat, satuan, saldocomp, qntopname, selisih, qntdb, qntcr , kodegdg ,lokasi, rak, pverifikasi , pverifikasi -->
                        <tr>
                          <th scope="col">Nobukti</th>
                          <th scope="col">Tgl</th>
                          <th scope="col">Kode Brg</th>
                          <th scope="col">Nama Brg</th>
                          <th scope="col">Nosat</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">SaldoComp</th>

                          <th scope="col">QntOpname</th>
                          <th scope="col">Selisih</th>
                          <th scope="col">QntDB</th>
                          <th scope="col">QntCR</th>

                          <th scope="col">Kode Gdg</th>
                          <th scope="col">Lokasi</th>
                          <th scope="col">Rak</th>
                          <th scope="col">Verifikasi</th>

                          <th scope="col">Verif Audit</th>

                        </tr>
                      </thead>
                      <!-- nobukti , tanggal, nama cust, nama barang, pocust, qntso, qty, stock , sat, catatan, lokasipenerima, alamat kirim, duedate, pr, part number, merk, userid -->

                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($listData); $i++)
                        <tr>
                          <td>{{ $listData[$i]->NOBUKTI }}</td>
                          <td>{!! date("Y/m/d", strtotime($listData[$i]->tanggal)) !!}</td>
                          <td>{{ $listData[$i]->KODEBRG }}</td>
                          <td>{{ $listData[$i]->NAMABRG }}</td>

                          <td>{{ $listData[$i]->NOSAT }}</td>
                          <td>{{ $listData[$i]->SATUAN }}</td>
                          {{-- Dinonaktifkan 2026-10-23: (int) membuat qty desimal antara -1 dan 1 (mis. selisih 0.50) tampil 0.00.
                          <td>{{ (int)$listData[$i]->SaldoComp ? $listData[$i]->SaldoComp : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->QntOpname ? $listData[$i]->QntOpname : '0.00' }}</td>

                          <td>{{ (int)$listData[$i]->Selisih ? $listData[$i]->Selisih : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->QNTDB ? $listData[$i]->QNTDB : '0.00' }}</td>
                          <td>{{ (int)$listData[$i]->QNTCR ? $listData[$i]->QNTCR : '0.00' }}</td>
                          --}}
                          <td>{{ (float)$listData[$i]->SaldoComp ? $listData[$i]->SaldoComp : '0.00' }}</td>
                          <td>{{ (float)$listData[$i]->QntOpname ? $listData[$i]->QntOpname : '0.00' }}</td>
                          <td>{{ (float)$listData[$i]->Selisih ? $listData[$i]->Selisih : '0.00' }}</td>
                          <td>{{ (float)$listData[$i]->QNTDB ? $listData[$i]->QNTDB : '0.00' }}</td>
                          <td>{{ (float)$listData[$i]->QNTCR ? $listData[$i]->QNTCR : '0.00' }}</td>
                          <td>{{ $listData[$i]->KODEGDG }}</td>
                          <td>{{ $listData[$i]->LOKASI }}</td>
                          <td>{{ $listData[$i]->RAK }}</td>
                          @if ((int)$listData[$i]->pVerifikasi)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif

                          @if ((int)$listData[$i]->pVerifikasiAudit)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
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
       "lengthChange": false,
         "paging": false ,
           "columnDefs": [
        // { "type": "date", "targets": [2] },
        {  "className": "text-right", "targets": [6,7,8,9,10],  },
      ]});
});

function buttonOpenFilter () {

  let bulan = $("#input_date_month").val();
  console.log(bulan)
  if (!bulan) {

    document.getElementById("input_date_month").value = $("#periode_bulan").val();
  }

  $("#form").modal('toggle')


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

// Dinonaktifkan 2026-10-23: nilai kosong tertulis "null"; layar membeku tanpa tanda memuat;
// kalau server gagal tidak ada pesan.
// function submitFilter () {
//   // console.log(tanggal)
//   let _token = $("#_token").val();
//
//
//   let bln = $("#input_date_month").val();
//   let thn = $("#input_date_year").val();
//
//   $.ajax({
//     url: "{!! url('reportopnameharianspfilter') !!}",
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
//         <td>${item.NOBUKTI}</td>
//         <td>${formatDate(item.tanggal , '/')}</td>
//         <td>${item.KODEBRG}</td>
//         <td>${item.NAMABRG}</td>
//
//         <td>${item.NOSAT}</td>
//          <td>${item.SATUAN}</td>
//          <td>${Number(item.SaldoComp) ? item.SaldoComp : '0.00'}</td>
//          <td>${Number(item.QntOpname) ? item.QntOpname : '0.00'}</td>
//          <td>${Number(item.Selisih) ? item.Selisih : '0.00'}</td>
//          <td>${Number(item.QNTDB) ? item.QNTDB : '0.00'}</td>
//          <td>${Number(item.QNTCR) ? item.QNTCR : '0.00'}</td>
//          <td>${item.KODEGDG}</td>
//          <td>${item.LOKASI}</td>
//          <td>${item.RAK}</td>
//          ${Number(item.pVerifikasi) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}
//          ${Number(item.pVerifikasiAudit) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}
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
//      "lengthChange": false,
//        "paging": false ,
//          "columnDefs": [
//       // { "type": "date", "targets": [2] },
//       {  "className": "text-right", "targets": [6,7,8,9,10],  },
//     ]});
//   }});
//
//
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
    url: "{!! url('reportopnameharianspfilter') !!}",
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
        <td>${wmsTeks(item.NOBUKTI)}</td>
        <td>${formatDate(item.tanggal , '/')}</td>
        <td>${wmsTeks(item.KODEBRG)}</td>
        <td>${wmsTeks(item.NAMABRG)}</td>
        <td>${wmsTeks(item.NOSAT)}</td>
        <td>${wmsTeks(item.SATUAN)}</td>
        <td>${Number(item.SaldoComp) ? item.SaldoComp : '0.00'}</td>
        <td>${Number(item.QntOpname) ? item.QntOpname : '0.00'}</td>
        <td>${Number(item.Selisih) ? item.Selisih : '0.00'}</td>
        <td>${Number(item.QNTDB) ? item.QNTDB : '0.00'}</td>
        <td>${Number(item.QNTCR) ? item.QNTCR : '0.00'}</td>
        <td>${wmsTeks(item.KODEGDG)}</td>
        <td>${wmsTeks(item.LOKASI)}</td>
        <td>${wmsTeks(item.RAK)}</td>
        ${Number(item.pVerifikasi) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}
        ${Number(item.pVerifikasiAudit) ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'}
      </tr>`
    });

    document.getElementById("tabel_data").innerHTML = rowTable
    $("#tabel").DataTable({
     "lengthChange": false,
       "paging": false ,
         "columnDefs": [
      {  "className": "text-right", "targets": [6,7,8,9,10],  },
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
