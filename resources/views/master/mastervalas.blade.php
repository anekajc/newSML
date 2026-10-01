@extends('newmasterTest')
@section('buttons')
@endsection
@section('page-title', 'Master Valas')

@section('content')

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster')

      <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">Kode Valas</th>
            <th style="padding: 4px 12px;" scope="col">Nama Valas</th>
            <th style="padding: 4px 12px;" scope="col">Kurs</th>
            <th style="padding: 4px 12px;" scope="col">Simbol</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left"></tbody>
      </table>

    </div>
  </div>

</div>


<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="labelFormAdd" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelFormAdd">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

        <div class="bs-form bs-form-1">
          <label for="input_add_kode">Kode Valas</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Valas">

          <label for="input_add_nama">Nama Valas</label>
          <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Valas">

          <label for="input_add_kurs">Kurs</label>
          <input type="text" class="form-control text-right" id="input_add_kurs"
            value="0.00"
            style="font-variant-numeric: tabular-nums;"
            oninput="formatNumber(this)">

          <label for="input_add_simbol">Simbol</label>
          <input type="text" class="form-control" id="input_add_simbol" placeholder="Simbol" maxlength="4">
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add-->

<!-- start modal edit -->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="labelFormEdit" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="labelFormEdit">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

        <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Kode Valas</label>
          <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode Valas" disabled>

          <label for="input_edit_nama">Nama Valas</label>
          <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama Valas">

          <label for="input_edit_kurs">Kurs</label>
          <input type="text" class="form-control text-right" id="input_edit_kurs"
            value="0.00"
            style="font-variant-numeric: tabular-nums;"
            oninput="formatNumber(this)">

          <label for="input_edit_simbol">Simbol</label>
          <input type="text" class="form-control" id="input_edit_simbol" placeholder="Simbol" maxlength="4">
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal edit-->

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {

  let _token = $("#_token").val();
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastervalasloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item, i) => {
    rowTable += `<tr>
      <td class="text-center">
        <div class="po-aksi-wrap">
          <button title="Edit" class="btn btn-success" type="button" onclick="buttonEdit('${item.KODEVLS}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn btn-danger" type="button" onclick="buttonDelete('${item.KODEVLS}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${item.KODEVLS}</td>
    <td>${item.NAMAVLS}</td>
    <td class='text-right'>${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(item.KURS))}</td>
    <td>${item.Simbol ?? ''}</td>
    </tr>`

  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')

}

function buttonAdd () {
  document.getElementById("input_add_kode").value = ""
  document.getElementById("input_add_nama").value = ""
  document.getElementById("input_add_kurs").value = "0.00"
  document.getElementById("input_add_simbol").value = ""

  $("#form").modal('show')

}

function buttonEdit (kode) {
  let _token = $("#_token").val();
  let ketemu = false
  $.ajax({
    url: "{!! url('mastervalasspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning("Data valas tidak ditemukan, silakan refresh halaman")
        return
      }
      ketemu = true
      document.getElementById("input_edit_kode").value = res[0].KODEVLS
      document.getElementById("input_edit_nama").value = res[0].NAMAVLS
      document.getElementById("input_edit_kurs").value = parseFloat(res[0].KURS).toFixed(2);
      document.getElementById("input_edit_simbol").value = res[0].Simbol ?? ''

      formatNumber(document.getElementById("input_edit_kurs"))

    }})
    if (ketemu) {
      $("#formEdit").modal('show')
    }
}

function buttonDelete (kode) {
  let _token = $("#_token").val();

  alertify.confirm('Hapus Valas', 'Apakah yakin ingin menghapus Kode Valas ' + kode + ' ?',
      function() {
        $.ajax({
          url: "{!! url('mastervalasspdelete') !!}",
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
              loadAll()
              alertify.success("Kode Valas telah dihapus");
            }
          }})
      }
    ,function(){
    });

}
//
function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let nama = $("#input_edit_nama").val();
  let kurs = parseFloat(($("#input_edit_kurs").val() || 0).toString().replace(/,/g, '')) || 0;
  let simbol = $("#input_edit_simbol").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Kurs harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Simbol harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kurs,
      simbol
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success("Data Valas telah diedit");
        loadAll()
        $("#formEdit").modal('hide')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = ($("#input_add_kode").val() || '').trim();
  let nama = $("#input_add_nama").val();
  let kurs = parseFloat(($("#input_add_kurs").val() || 0).toString().replace(/,/g, '')) || 0;
  let simbol = $("#input_add_simbol").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Kurs harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Simbol harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kurs,
      simbol
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success("Data Valas telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

}

window.onload = function(){
  loadAll();
};

</script>

@endsection
