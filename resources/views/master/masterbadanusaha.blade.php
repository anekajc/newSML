@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Badan Usaha')
@section('content')

{{-- Master Badan Usaha (DBBADANUSAHA): PT, CV, UD, ... + setting PPh21 & PPh23.
     Dipakai dropdown Bentuk Usaha di Master Customer, Supplier, dan Ekspedisi.
     Pola halaman disalin dari masterjeniscustomer.blade.php. --}}
<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

  <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">Badan Usaha</th>
            <th style="padding: 4px 12px;" scope="col" class="text-right">PPh21 (%)</th>
            <th style="padding: 4px 12px;" scope="col" class="text-right">PPh23 (%)</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
      </tbody>
      </table>

    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="judulAdd" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulAdd">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="container-fluid">
            <div class="bs-form bs-form-1">
          <label for="input_add_kode">Badan Usaha</label>
          <input type="text" class="form-control" id="input_add_kode" maxlength="20" placeholder="mis. PT, CV, UD" style="text-transform:uppercase">

          <label for="input_add_pph21">PPh21 (%)</label>
          <input type="number" step="0.01" min="0" max="100" class="form-control text-right" id="input_add_pph21" placeholder="0.00">

          <label for="input_add_pph23">PPh23 (%)</label>
          <input type="number" step="0.01" min="0" max="100" class="form-control text-right" id="input_add_pph23" placeholder="0.00">
        </div>
        <small class="text-muted">PPh21 dan PPh23 minimal diisi salah satu.</small>
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
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="judulEdit" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulEdit">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="container-fluid">
            <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Badan Usaha</label>
          <input type="text" class="form-control" id="input_edit_kode" disabled>

          <label for="input_edit_pph21">PPh21 (%)</label>
          <input type="number" step="0.01" min="0" max="100" class="form-control text-right" id="input_edit_pph21" placeholder="0.00">

          <label for="input_edit_pph23">PPh23 (%)</label>
          <input type="number" step="0.01" min="0" max="100" class="form-control text-right" id="input_edit_pph23" placeholder="0.00">
        </div>
        <small class="text-muted">PPh21 dan PPh23 minimal diisi salah satu.</small>
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

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

function escHtml (s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))
}

function fmtPph (v) {
  return (parseFloat(v) || 0).toFixed(2)
}

function loadAll () {
  let _token = $("#_token").val();

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterbadanusahaloadall') !!}",
    type: "get",
    async: false,
    data: { _token },
    success: function(res) {
      dataRefresh = res
  }})

  let rowTable = ""
  dataRefresh.forEach((item) => {
    let kode = escHtml(item.KodeUsaha)
    let kodeJs = escHtml(JSON.stringify(item.KodeUsaha))
    rowTable += `<tr>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit(${kodeJs})"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete(${kodeJs})"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${kode}</td>
    <td class="text-right">${fmtPph(item.PPH21)}</td>
    <td class="text-right">${fmtPph(item.PPH23)}</td>
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

// Validasi sama dengan controller: minimal salah satu PPh > 0, rentang 0 - 100.
function cekPph (pph21, pph23) {
  let a = parseFloat(pph21) || 0
  let b = parseFloat(pph23) || 0
  if (a < 0 || a > 100 || b < 0 || b > 100) {
    alertify.warning("PPh harus di antara 0 - 100");
    return false
  }
  if (a <= 0 && b <= 0) {
    alertify.warning("PPh21 dan PPh23 tidak boleh kosong semua, isi minimal salah satu");
    return false
  }
  return true
}

function buttonAdd () {
  document.getElementById('input_add_kode').value = ''
  document.getElementById('input_add_pph21').value = ''
  document.getElementById('input_add_pph23').value = ''
  $("#form").modal('toggle')
}

function buttonEdit (kode) {
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterbadanusahaspdetail') !!}",
    type: "get",
    async: false,
    data: { _token, kode },
    success: function(res) {
      if (!res.length) {
        alertify.warning("Data tidak ditemukan");
        return
      }
      document.getElementById("input_edit_kode").value = res[0].KodeUsaha
      document.getElementById("input_edit_pph21").value = fmtPph(res[0].PPH21)
      document.getElementById("input_edit_pph23").value = fmtPph(res[0].PPH23)
      $("#formEdit").modal('toggle')
    }})
}

function buttonDelete (kode) {
  let _token = $("#_token").val();

  alertify.confirm('Hapus Badan Usaha', 'Apakah yakin ingin menghapus Badan Usaha ' + escHtml(kode) + ' ?',
      function() {
        $.ajax({
          url: "{!! url('masterbadanusahaspdelete') !!}",
          type: "post",
          async: false,
          data: { _token, kode },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              loadAll()
              alertify.success("Badan Usaha telah dihapus");
            }
          }})
      }
    ,function(){});
}

function submitEdit () {
  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let pph21 = $("#input_edit_pph21").val();
  let pph23 = $("#input_edit_pph23").val();

  if (!cekPph(pph21, pph23)) return

  $.ajax({
    url: "{!! url('masterbadanusahaspedit') !!}",
    type: "post",
    async: false,
    data: { _token, kode, pph21, pph23 },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
      } else {
        alertify.success("Badan Usaha telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }
    }})
}

function submitAdd () {
  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val().trim().toUpperCase();
  let pph21 = $("#input_add_pph21").val();
  let pph23 = $("#input_add_pph23").val();

  // Harus ada huruf/angka: kosong, '-', '.', '--' tidak boleh.
  if (!/[A-Z0-9]/.test(kode)) {
    alertify.warning("Badan Usaha harus diisi huruf/angka (tidak boleh kosong atau -)");
    return
  }
  if (!cekPph(pph21, pph23)) return

  $.ajax({
    url: "{!! url('masterbadanusahaspadd') !!}",
    type: "post",
    async: false,
    data: { _token, kode, pph21, pph23 },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
      } else {
        alertify.success("Badan Usaha telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }
    }})
}

window.onload = function(){
  loadAll();
};

</script>

@endsection
