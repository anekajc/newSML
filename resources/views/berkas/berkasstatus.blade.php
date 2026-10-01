@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Online / Offline')
@section('content')

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Berkas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Online/Offline</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Online/Offline</h1>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Online/Offline</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster', ['tanpaTambah' => true])

      <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">User ID</th>
            <th style="padding: 4px 12px;" scope="col">Nama Lengkap</th>
            <th style="padding: 4px 12px;" scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
        </tbody>
      </table>

    </div>
  </div>

</div>

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="judulEditStatus" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulEditStatus">Edit Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">

        <div class="container-fluid">
          <div class="bs-form bs-form-1">
            <label for="input_edit_kode">User ID</label>
            <input type="text" class="form-control" id="input_edit_kode" readonly>

            <label for="input_edit_nama">Nama Lengkap</label>
            <input type="text" class="form-control" id="input_edit_nama" readonly>

            <label for="input_edit_status">Status</label>
            <select class="form-control" id="input_edit_status">
              <option value="0">Offline</option>
              <option value="1">Online</option>
            </select>
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

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Nilai dari database ditulis ke HTML lewat fungsi ini supaya nama yang mengandung kutip
// atau < > tidak merusak baris tabel.
function spEsc (v) {
  if (v === null || v === undefined) { return '' }
  return String(v)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')
}

function spGagal (err) {
  console.log(err)
  alertify.error('Gagal terhubung ke server, silakan coba lagi')
}

function loadAll () {
  let _token = $("#_token").val();
  let berhasil = false

  $.ajax({
    url: "{!! url('berkasstatusloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      if (!Array.isArray(res)) {
        alertify.error('Sesi login habis atau server tidak merespons dengan benar. Silakan muat ulang halaman.')
        return
      }
      dataRefresh = res
      berhasil = true
    },
    error: spGagal
  })
  if (!berhasil) { return }

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  let rowTable = ""
  dataRefresh.forEach((item, i) => {

    rowTable += `<tr>
      <td style="white-space:nowrap;" class='text-center'>
        <div class="action-buttons-wrap">
            <button title="Edit Status" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit(${i})"><i class="bi bi-pen"></i></button>
        </div>
      </td>
    <td>${spEsc(item.USERID)}</td>
    <td>${spEsc(item.FullName)}</td>
    <td>
      ${
          Number(item.STATUS) === 1
              ? '<span class="sp-badge is-active">Online</span>'
              : '<span class="sp-badge is-inactive">Offline</span>'
      }
    </td>
    </tr>`

  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable({
    "lengthChange": false,
    "paging": true,
    "searching": true,
    "ordering": false,
    "dom": MasterList.dom, "order": [], "language": MasterList.bahasa,
    "pageLength": MasterList.panjang()
  });
  MasterList.selesai('#tabel')

}

function buttonEdit (idx) {
  let kode = dataRefresh[idx] ? dataRefresh[idx].USERID : ''
  if (!kode) { return }
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('berkasstatusspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {
      if (!Array.isArray(res) || !res.length) {
        alertify.warning('User ' + spEsc(kode) + ' tidak ditemukan')
        return
      }

      document.getElementById("input_edit_kode").value = res[0].USERID
      document.getElementById("input_edit_nama").value = res[0].FullName ?? ''
      document.getElementById("input_edit_status").value = Number(res[0].STATUS) === 1 ? '1' : '0'

      // formatNumber(document.getElementById("input_edit_kurs")) // dimatikan: sisa salinan menu valas - elemen #input_edit_kurs tidak ada, error-nya membuat modal Edit tidak pernah terbuka

      $("#formEdit").modal('show')
    },
    error: spGagal
  })
}

function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let status = $("#input_edit_status").val();

  if (!kode) {
    alertify.warning("User ID kosong");
    return
  }

  $.ajax({
    url: "{!! url('berkasstatusspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      status
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success("Status telah diedit");
        $("#formEdit").modal('hide')
        loadAll()
      }

    },
    error: spGagal
  })

}

window.onload = function(){
  loadAll();
};

</script>



@endsection
