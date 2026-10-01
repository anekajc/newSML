@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Nomor Polisi')
@section('content')


<?php
function getStatus($data) {
    return $data == 1 ? "Aktif" : "Non-Aktif";
}
?>



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">No. Pol.</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master No. Pol.</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add No. Pol.</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

          <table id="tabel" class="data-table po-aksi-hover">
                <thead>
                  <tr>
                    <th style="padding: 4px 12px;" scope="col">Actions</th>
                    <th style="padding: 4px 12px;" scope="col">No. Pol.</th>
                    <th style="padding: 4px 12px;" scope="col">Aktif / Non-Aktif</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Cost</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-left">
              </tbody>
              </table>

    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_add_kode">Kode Kendaraan</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Kendaraan" maxlength="30">

          <label for="input_add_nochasis">No. Chasis</label>
          <input type="text" class="form-control" id="input_add_nochasis" placeholder="No. Chasis" maxlength="50">

          <label for="input_add_nopol">No. Pol.</label>
          <input type="text" class="form-control" id="input_add_nopol" placeholder="No. Pol." maxlength="50">

          <label for="input_add_merk">Nama Kendaraan</label>
          <input type="text" class="form-control" id="input_add_merk" placeholder="Nama Kendaraan" maxlength="50">

          <label for="input_add_nama">Status Aktif</label>
          <select class="form-control" id="input_add_nama">
                    <option value=1>Aktif</option>
                    <option value=0>Non-Aktif</option>
                    </select>

          <label for="input_add_perkiraan">Kode Cost</label>
          <div class="input-group"> <input type="text" class="form-control" id="input_add_perkiraan" placeholder="Kode Cost" disabled> <div class="input-group-append"> <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectPerkiraan()" title="Cari"><i class="bi bi-search"></i></button> </div> </div>
        </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Kode Kendaraan</label>
          <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode Kendaraan" disabled>

          <label for="input_edit_nochasis">No. Chasis</label>
          <input type="text" class="form-control" id="input_edit_nochasis" placeholder="No. Chasis" disabled>

          <label for="input_edit_nopol">No. Pol.</label>
          <input type="text" class="form-control" id="input_edit_nopol" placeholder="No. Pol." maxlength="50">

          <label for="input_edit_merk">Nama Kendaraan</label>
          <input type="text" class="form-control" id="input_edit_merk" placeholder="Nama Kendaraan" maxlength="50">

          <label for="input_edit_nama">Status Aktif</label>
          <select class="form-control" id="input_edit_nama">
                    <option value=1>Aktif</option>
                    <option value=0>Non-Aktif</option>
                    </select>

          <label for="input_edit_perkiraan">Kode Cost</label>
          <div class="input-group"> <input type="text" class="form-control" id="input_edit_perkiraan" placeholder="Kode Cost" disabled> <div class="input-group-append"> <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectPerkiraan()" title="Cari"><i class="bi bi-search"></i></button> </div> </div>
        </div>


    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->


<!-- start modal perkiraan add -->
{{-- Modal pemilih - tampilan & DataTables mengikuti picker purchasing (picker-kas.css / pickerKasInit()). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
<div class="modal fade picker-kas" id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelPerkiraan">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Kode Cost</th>
                    <th scope="col">Nama Cost</th>
                  </tr>
                </thead>
                <tbody id="tabelData_perkiraan" class="text-left"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal perkiraan add -->

@endsection

@section('js')

<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>

<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();
  
  // document.getElementById('breadcrumb').innerHTML = "Master Nomor Polisi" // dimatikan: judul sekarang di bar atas (page-title)

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masternopolloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      console.log(res)
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    let temp = ""

    rowTable += `<tr>
      <td style="white-space:nowrap;" class='text-center'>
    <div class="action-buttons-wrap">
        <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEKEND}')"><i class="bi bi-pen"></i></button>
        <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEKEND}')"><i class="bi bi-trash"></i></button>
    </div>
        </td>
    <td>${item.KODEKEND}</td>
    <td>${item.IsAktif == 1 ? '<span class="sp-badge is-admin">Aktif</span>' : '<span class="sp-badge is-supervisor">Tidak Aktif</span>'}</td>
    <td>${item.KodeCost}</td>
    </tr>`
  });


    let currentLength = $("#tabel_length_visual").val() ? Number($("#tabel_length_visual").val()) : 10;
      document.getElementById("tabel_data").innerHTML = rowTable
      $("#tabel").DataTable({
        "lengthChange": false,
        "paging": true,
        "searching": true,
        "dom": MasterList.dom, "order": [], "language": MasterList.bahasa,
        "pageLength": currentLength
      });
      MasterList.selesai('#tabel')

}

function buttonAdd () {
  document.getElementById('input_add_kode').value = ''
  document.getElementById('input_add_nochasis').value = ''
  document.getElementById('input_add_nopol').value = ''
  document.getElementById('input_add_merk').value = ''
  document.getElementById('input_add_nama').value = '1'
  document.getElementById('input_add_perkiraan').value = ''
  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternopolspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODEKEND
      document.getElementById("input_edit_nochasis").value = res[0].NOCHASIS ? res[0].NOCHASIS : ''
      document.getElementById("input_edit_nopol").value = res[0].NAMAKEND ? res[0].NAMAKEND : ''
      document.getElementById("input_edit_merk").value = res[0].MERKKEND ? res[0].MERKKEND : ''
      document.getElementById("input_edit_nama").value = res[0].IsAktif
      document.getElementById("input_edit_perkiraan").value = res[0].KodeCost

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Data ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masternopolspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kode
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data Biaya telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}
//
function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let nopol = $("#input_edit_nopol").val();
  let merk = $("#input_edit_merk").val();
  let nama = $("#input_edit_nama").val();
  let kodecost = $("#input_edit_perkiraan").val();

  console.log(kode,nama,kodecost)
  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nopol) {
    alertify.warning("No. Pol. harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!kodecost) {
    alertify.warning("Perkiraan harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternopolspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nopol,
      merk,
      nama,
      kodecost
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Biaya telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nochasis = $("#input_add_nochasis").val();
  let nopol = $("#input_add_nopol").val();
  let merk = $("#input_add_merk").val();
  let nama = $("#input_add_nama").val();
  let kodecost = $("#input_add_perkiraan").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nopol) {
    alertify.warning("No. Pol. harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!kodecost) {
    alertify.warning("Kode Cost harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternopolspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nochasis,
      nopol,
      merk,
      nama,
      kodecost
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Kendaraan telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

function buttonSelectPerkiraan(kode) {
  console.log(kode);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternopolselectperkiraan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kode: kode // Make sure 'kode' is passed correctly
    },
    success: function (dataRefresh) {
      let rowTable = "";

      dataRefresh.forEach((item, i) => {
        rowTable += `<tr class="pick-row" onclick="buttonPilihPerkiraan('${item.kodecost}')">
          <td>${item.kodecost}</td>
          <td>${item.namacost}</td>
        </tr>`;
      });

      $("#tabelData_perkiraan").html(rowTable); // Update the table content

      pickerKasInit('tabelPerkiraan')

      // Close the modal if needed
      $("#formPerkiraan").modal("toggle");
    }
  });
}

function buttonPilihPerkiraan(selectedPerkiraan) {
  // Get the input element by ID
  document.getElementById("input_add_perkiraan").value = selectedPerkiraan
  document.getElementById("input_edit_perkiraan").value = selectedPerkiraan

  $("#formPerkiraan").modal("hide");
}

window.onload = function(){
  loadAll();
};

</script>


@endsection
