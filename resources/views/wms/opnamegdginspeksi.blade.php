@extends('newmasterTest')

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/wms-tampilan-baru.css') !!}?v={{ @filemtime(public_path('css/wms-tampilan-baru.css')) ?: '1' }}">
@endsection
@section('page-title', 'Inspeksi Opname Harian')
@section('buttons')


@endsection
@section('content')
<div class="container-fluid">
  <div class="row">


  <div class="col-6 text-left">
    {{-- <h1>Inspeksi Opname Harian</h1> - judul pindah ke header (page-title) --}}
  </div>
  <div class="col-6 text-right">
    <!-- <button type="button" class="btn btn-primary btn-sm " style="height: 60px; " onclick="buttonAddScan()"  >Opname Gudang</button> -->
  </div>
  </div>

  <!-- <button onclick="loadAll()">tes</button> -->

<div id="printContainer" style="display:none">

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
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Inspeksi Opname Harian</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"> Opname Teraudit </a>
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
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Nama Brg</th>
                          <th scope="col">Lokasi</th>
                          <th scope="col">Qty</th>
                          <th scope="col">Qty Saldo</th>
                          <th scope="col">Qty Selisih</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">Tanggal</th>
                        </tr>
                      </thead>


                      <tbody id="tabel_data" class="text-left" >
                        @for ($i = 0; $i < count($outstandingArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{ $outstandingArray[$i]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                            <button data-toggle="tooltip" data-placement="top" title="Verifikasi Audit" class="btn btn-primary btn-sm" type="button" onclick="buttonVerifikasi('{{ $outstandingArray[$i]->NOBUKTI }}')"><i class="bi bi-file-earmark-check"></i></button>
                            <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('{{ $outstandingArray[$i]->NOBUKTI }}')"><i class="bi bi-pen"></i></button>
                            <!-- <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('{{ $outstandingArray[$i]->NOBUKTI }}' , {{ $outstandingArray[$i]->URUT }} , {{ $outstandingArray[$i]->pVerifikasiAudit }})" ><i class="bi bi-trash"></i></button> -->

                          </td>
                          <td>{{ $outstandingArray[$i]->NOBUKTI }}</td>
                          <td>{{ $outstandingArray[$i]->NAMABRG }}</td>
                          <td>{{ $outstandingArray[$i]->BARCODELOC }}</td>
                          <td>{{ $outstandingArray[$i]->QntOpname }}</td>

                          <td>{{ $outstandingArray[$i]->SaldoComp }}</td>

                          <td>{{ $outstandingArray[$i]->Selisih }}</td>
                          <td>{{ $outstandingArray[$i]->SATUAN }}</td>
                          <td>{!! date("Y/m/d", strtotime($outstandingArray[$i]->tanggal)) !!}</td>
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

                    <table id="tabelKoreksi" class="data-table"  >
                      <thead class="text-center">
                        <tr>
                          <th scope="col">Actions</th>
                          <th scope="col">No. Bukti</th>
                          <th scope="col">Nama Brg</th>
                          <th scope="col">Lokasi</th>
                          <th scope="col">Qty</th>
                          <th scope="col">Qty Saldo</th>
                          <th scope="col">Selisih</th>
                          <th scope="col">Satuan</th>
                          <th scope="col">Tanggal</th>
                        </tr>
                      </thead>


                      <tbody id="tabelKoreksi_data" class="text-left" >
                        @for ($i = 0; $i < count($penerimaanArray); $i++)
                        <tr>
                          <td class="text-center">
                            <button style="" class="btn btn-warning btn-sm" type="button"   onclick="buttonDetail('{{ $penerimaanArray[$i]->NOBUKTI }}')" ><i class="bi bi-info-lg"></i></button>
                          </td>
                          <td>{{ $penerimaanArray[$i]->NOBUKTI }}</td>
                          <td>{{ $penerimaanArray[$i]->NAMABRG }}</td>
                          <td>{{ $penerimaanArray[$i]->BARCODELOC }}</td>
                          <td>{{ $penerimaanArray[$i]->QntOpname }}</td>
                          <td>{{ $penerimaanArray[$i]->SaldoComp }}</td>
                          <td>{{ $penerimaanArray[$i]->Selisih }}</td>
                          <td>{{ $penerimaanArray[$i]->SATUAN }}</td>
                          <td>{!! date("Y/m/d", strtotime($penerimaanArray[$i]->tanggal)) !!}</td>
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
<div class="modal fade" id="formDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
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
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
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

          </div>



        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <label>Barcode Barang</label>
            </div>
          </div>

          <!-- <div class="col-2">
          </div> -->


        </div>
        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_detail_kode1" placeholder="Barcode Barang" disabled>

            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_detail_kode1nama" placeholder="Nama Brg" disabled>

            </div>
          </div>
          <!-- <div class="col-2">
          </div> -->

        </div>

        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <label>Lokasi</label>
            </div>
          </div>



        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_detail_kode2" placeholder="Lokasi Tujuan" disabled>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>Qnt</label>
            </div>
          </div>


        </div>
        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-7">
            <div class="form-group">
              <input type="number" id="input_detail_qnt" style="font-size: 15px" class="form-control form-control-lg col-lg text-right"  value="0.00" disabled>
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_detail_nosat" placeholder="satuan" disabled>
            </div>
          </div>
          <!-- <div class="col-2">
          </div> -->

        </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>





      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-primary" onclick="submitAddScan()">Submit</button> -->
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade" id="formKoreksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Koreksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_koreksi_noUrut" value="" />
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_koreksi_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

          </div>



        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <label>Barcode Barang</label>
            </div>
          </div>

          <!-- <div class="col-2">
          </div> -->


        </div>
        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_koreksi_kode1" placeholder="Barcode Barang" disabled>

            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_koreksi_kode1nama" placeholder="Nama Brg" disabled>

            </div>
          </div>
          <!-- <div class="col-2">
          </div> -->

        </div>

        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <label>Lokasi</label>
            </div>
          </div>



        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_koreksi_kode2" placeholder="Lokasi Tujuan" disabled>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>Qnt</label>
            </div>
          </div>


        </div>
        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-7">
            <div class="form-group">
              <input type="number" id="input_koreksi_qnt" style="font-size: 15px" class="form-control form-control-lg col-lg text-right"  value="0.00">
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_koreksi_nosat" placeholder="satuan" disabled>
            </div>
          </div>
          <!-- <div class="col-2">
          </div> -->

        </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>





      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="submitEdit()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->


<!-- start modal add -->
<div class="modal fade" id="formVerifikasi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style=" max-width:1200px;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Verifikasi Audit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
          <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_verifikasi_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>

          </div>



        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <label>Barcode Barang</label>
            </div>
          </div>

          <!-- <div class="col-2">
          </div> -->


        </div>
        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_verifikasi_kode1" placeholder="Barcode Barang" disabled>

            </div>
          </div>
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_verifikasi_kode1nama" placeholder="Nama Brg" disabled>

            </div>
          </div>
          <!-- <div class="col-2">
          </div> -->

        </div>

        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-12">
            <div class="form-group">
              <label>Lokasi</label>
            </div>
          </div>



        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_verifikasi_kode2" placeholder="Lokasi Tujuan" disabled>
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>Qnt</label>
            </div>
          </div>


        </div>
        <div class="row">
          <!-- <div class="col-2">
          </div> -->
          <div class="col-7">
            <div class="form-group">
              <input type="number" id="input_verifikasi_qnt" style="font-size: 15px" class="form-control form-control-lg col-lg text-right"  value="0.00" disabled>
            </div>
          </div>
          <div class="col-5">
            <div class="form-group">
              <input type="text" style="font-size: 15px" class="form-control form-control-lg col-lg" id="input_verifikasi_nosat" placeholder="satuan" disabled>
            </div>
          </div>
          <!-- <div class="col-2">
          </div> -->

        </div>
          <div class="row">


        </div>
        <div class="row">


        </div>

        </div>



      </div>





      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="submitVerifikasi()">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->



@endsection

@section('js')
<script src="{!! URL::asset('js/wms-tampilan-baru.js') !!}?v={{ @filemtime(public_path('js/wms-tampilan-baru.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefreshOutstanding = []
let dataRefreshPenerimaan = []

let tempBarangScan = {}
let tempEditData = {}
let tempDataVerifikasi = {}

$(document).ready(function(){
      $("#tabel").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        // Dinonaktifkan 2026-10-22: kolom 6 adalah Selisih (angka), kolom Tanggal ada di indeks 8.
        // { "type": "date", "targets": [6] },
        { "type": "date", "targets": [8] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
        {  "className": "text-right", "targets": [4], }
      ]});
      $("#tabelKoreksi").DataTable({
        'order': [[1, 'asc']],
        "lengthChange": false,
          "paging": false ,
           "columnDefs": [
        // Dinonaktifkan 2026-10-22: kolom 6 adalah Selisih (angka), kolom Tanggal ada di indeks 8.
        // { "type": "date", "targets": [6] },
        { "type": "date", "targets": [8] },
        {  "className": "text-center", "targets": [0], "orderable" :false },
        {  "className": "text-right", "targets": [4], }
      ]});


});





function buttonDetail (NOBUKTI) {

  let _token = $("#_token").val();
  console.log(NOBUKTI)

  $.ajax({
    url: "{!! url('opnamegdginspeksidetailoutstanding') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti: NOBUKTI

    },
    success: function(res) {
      console.log(res ,'ress =============================')
      // console.log(1)
      // return
      if (!res.length) {
        // console.log(2)
        alertify.warning("Data tidak ditemukan");

      } else {
        document.getElementById("input_detail_nobukti").value = res[0].NOBUKTI
        document.getElementById("input_detail_kode1").value = res[0].KODEBRG
        document.getElementById("input_detail_kode1nama").value = res[0].NAMABRG
        document.getElementById("input_detail_kode2").value = res[0].BARCODELOC
        document.getElementById("input_detail_qnt").value = res[0].Qnt2Opname


        document.getElementById("input_detail_nosat").value = res[0].SATUAN

        $("#formDetail").modal('toggle')
      }

    }
  })


}

function buttonKoreksi (NOBUKTI) {

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let _token = $("#_token").val();
  console.log(NOBUKTI)


  $.ajax({
    url: "{!! url('opnamegdginspeksidetailoutstanding') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti: NOBUKTI

    },
    success: function(res) {
      console.log(res ,'ress =============================')
      // console.log(1)
      // return
      if (!res.length) {
        // console.log(2)
        alertify.warning("Data tidak ditemukan");

      } else  if (Number(res[0].pVerifikasiAudit))  {
        alertify.warning("Data sudah diaudit");
      } else {
        tempEditData =  res[0]

        document.getElementById("input_koreksi_nobukti").value = res[0].NOBUKTI
        document.getElementById("input_koreksi_kode1").value = res[0].KODEBRG
        document.getElementById("input_koreksi_kode1nama").value = res[0].NAMABRG
        document.getElementById("input_koreksi_kode2").value = res[0].BARCODELOC
        document.getElementById("input_koreksi_qnt").value = res[0].Qnt2Opname


        document.getElementById("input_koreksi_nosat").value = res[0].SATUAN

        $("#formKoreksi").modal('toggle')
      }

    }
  })
}

function submitEdit () {
  console.log(tempEditData)


  let _token = $("#_token").val();
  let choice = 'U'
  let nobukti = tempEditData.NOBUKTI
  let nourut = tempEditData.nourut
  let kodebarang = tempEditData.KODEBRG
  let kodelokasi = tempEditData.BARCODELOC
  let qnt = $("#input_koreksi_qnt").val();
  let nosat = tempEditData.NOSAT
  let urut = tempEditData.URUT
  console.log('nobukti', nobukti)
  console.log('nourut', nourut)
  console.log('kodebarang', kodebarang)
  console.log('kodelokasi', kodelokasi)
  console.log('qnt', qnt)
  console.log('nosat', nosat)







  if ( Number(qnt) < 0) {
    alertify.warning("Qnt < 0");
    return
  }


  let satuan = tempEditData.SATUAN
  let qnt1 = 0
  let isi =0
  if (nosat == 1) {
    qnt1 = qnt * tempEditData.ISI1

    isi = tempEditData.ISI1
  }
  if (nosat == 2) {
    qnt1 = qnt * tempEditData.ISI2

    isi = tempEditData.ISI2
  }
  if (nosat == 3) {
    qnt1 = qnt * tempEditData.ISI3

    isi = tempEditData.ISI3
  }
  console.log('qnt1' , qnt1, satuan)


  $.ajax({
    url: "{!! url('opnamegdginspeksispkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      choice,
      nobukti,
      nourut,
      tempBarangScan :tempEditData,
      nosat,
      satuan,
      qnt,
      qnt1,
      kodelokasi,
      urut,
      isi
    },
    success: function(res) {
      console.log(res ,'ress =============================')
      // console.log(1)
      // return
      if (res == 1) {
        loadAll()
        $("#formKoreksi").modal('toggle')
        alertify.success('Berhasil koreksi opname')
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan pada server silahkan refresh browser')
    }
  })
}

function buttonVerifikasi (NOBUKTI) {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  console.log(NOBUKTI)

  let _token = $("#_token").val();
  console.log(NOBUKTI)

  $.ajax({
    url: "{!! url('opnamegdginspeksidetailoutstanding') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nobukti: NOBUKTI

    },
    success: function(res) {
      console.log(res ,'ress =============================')
      // console.log(1)
      // return
      if (!res.length) {
        // console.log(2)
        alertify.warning("Data tidak ditemukan");

      } else  if (Number(res[0].pVerifikasiAudit))  {
        alertify.warning("Data sudah diaudit");
      } else {
        tempDataVerifikasi = res[0]
        document.getElementById("input_verifikasi_nobukti").value = res[0].NOBUKTI
        document.getElementById("input_verifikasi_kode1").value = res[0].KODEBRG
        document.getElementById("input_verifikasi_kode1nama").value = res[0].NAMABRG
        document.getElementById("input_verifikasi_kode2").value = res[0].BARCODELOC
        document.getElementById("input_verifikasi_qnt").value = res[0].Qnt2Opname


        document.getElementById("input_verifikasi_nosat").value = res[0].SATUAN

        $("#formVerifikasi").modal('toggle')
      }

    }
  })
}

function submitVerifikasi () {
  console.log(tempDataVerifikasi)
  let nobukti = tempDataVerifikasi.NOBUKTI
  let urut = tempDataVerifikasi.URUT

  let _token = $("#_token").val();


  $.ajax({
    url: "{!! url('opnamegdginspeksispverifikasiaudit') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      urut
    },
    success: function(res) {
      if (res > 0) {
        loadAll()
        alertify.success('Berhasil verifikasi audit')
        $("#formVerifikasi").modal('toggle')
      }


  },
  error: function (err) {
    alertify.warning('Terjadi kesalahan pada server, silahkan refresh ulang')
  }
})
}


function buttonDelete (nobukti , urut , pVerifikasiAudit) {
  console.log(nobukti,urut)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }



  if (Number(pVerifikasiAudit)) {
    alertify.warning("Data sudah diaudit");
    return
  }

  alertify.confirm('Hapus Opname', 'Apakah yakin ingin menghapus opname ' + nobukti + ' ?',
    function() {
      let _token = $("#_token").val();
      let choice = "D"




      console.log()
      $.ajax({
        url: "{!! url('opnamegdginspeksispdelete') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          urut,
          choice

        },
        success: function(res) {

          loadAll()
          alertify.success("Berhasil hapus opname")
        }
      })
    }
  ,function(){});

}



  function loadAll () {
    $.ajax({
      url: "{!! url('opnamegdginspeksiloadall') !!}",
      type: "get",
      async: false,
      success: function(res) {
        console.log(res)
        dataRefreshOutstanding = res.outstandingArray
        dataRefreshPenerimaan = res.penerimaanArray
      }
    })

    $('#tabel').DataTable().destroy();
    console.log(dataRefreshOutstanding)
    console.log(dataRefreshPenerimaan)
    let rowTable = ""
    dataRefreshOutstanding.forEach((item, i) => {
      let date1 = ""
      if (item.tanggal) {
        let date = new Date(item.tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }

      rowTable += `<tr>
      <td class="text-center">
      <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NOBUKTI}')"><i class="bi bi-info-lg"></i></button>
      <button data-toggle="tooltip" data-placement="top" title="Verifikasi Audit" class="btn btn-primary btn-sm" type="button" onclick="buttonVerifikasi('${item.NOBUKTI}')"><i class="bi bi-file-earmark-check"></i></button>
      <button class="btn btn-success btn-sm" type="button" onclick="buttonKoreksi('${item.NOBUKTI}')"><i class="bi bi-pen"></i></button>

      </td>
      <td>${item.NOBUKTI}</td>
      <td>${item.NAMABRG}</td>
      <td>${item.BARCODELOC}</td>
      <td>${item.QntOpname}</td>

      <td>${item.SaldoComp}</td>

      <td>${item.Selisih}</td>
      <td>${item.SATUAN}</td>
      <td>${date1}</td>
      </tr>`
    });


    //
    // <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete('${item.NOBUKTI}' , ${item.URUT} ,${item.pVerifikasiAudit})" ><i class="bi bi-trash"></i></button>

    document.getElementById("tabel_data").innerHTML = rowTable



    $("#tabel").DataTable({
      'order': [[1, 'asc']],
      "lengthChange": false,
        "paging": false ,
         "columnDefs": [
      // Dinonaktifkan 2026-10-22: kolom 6 adalah Selisih (angka), kolom Tanggal ada di indeks 8.
      // { "type": "date", "targets": [6] },
      { "type": "date", "targets": [8] },
      {  "className": "text-center", "targets": [0], "orderable" :false },
      {  "className": "text-right", "targets": [4], },
    ]});



    $('#tabelKoreksi').DataTable().destroy();

    let rowTableKoreksi = ""
    dataRefreshPenerimaan.forEach((item, i) => {
      let date1 = ""
      if (item.tanggal) {
        let date = new Date(item.tanggal);
        let day = ("0" + date.getDate()).slice(-2);
        let month = ("0" + (date.getMonth() + 1)).slice(-2);
        date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
      }

      rowTableKoreksi += `<tr>
      <td class="text-center">
      <button class="btn btn-warning btn-sm" type="button" onclick="buttonDetail('${item.NOBUKTI}')"><i class="bi bi-info-lg"></i></button>

      </td>
      <td>${item.NOBUKTI}</td>
      <td>${item.NAMABRG}</td>
      <td>${item.BARCODELOC}</td>
      <td>${item.QntOpname}</td>
      <td>${item.SaldoComp}</td>

      <td>${item.Selisih}</td>
      <td>${item.SATUAN}</td>
      <td>${date1}</td>
      </tr>`
    });


    document.getElementById("tabelKoreksi_data").innerHTML = rowTableKoreksi




    $("#tabelKoreksi").DataTable({
      'order': [[1, 'asc']],
      "lengthChange": false,
        "paging": false ,
         "columnDefs": [
      // Dinonaktifkan 2026-10-22: kolom 6 adalah Selisih (angka), kolom Tanggal ada di indeks 8.
      // { "type": "date", "targets": [6] },
      { "type": "date", "targets": [8] },
      {  "className": "text-center", "targets": [0], "orderable" :false },
      {  "className": "text-right", "targets": [4], },
    ]});


  }



</script>




@endsection
