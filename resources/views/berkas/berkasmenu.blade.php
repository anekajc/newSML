@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Menu')
@section('content')

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Berkas</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Menu</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Menu</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Menu</button>
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
            <th style="padding: 4px 12px;" scope="col">Kode</th>
            <th style="padding: 4px 12px;" scope="col">Keterangan</th>
            <th style="padding: 4px 12px;" scope="col">L0</th>
            <th style="padding: 4px 12px;" scope="col">Kode Akses</th>
            <th style="padding: 4px 12px;" scope="col">Level Otorisasi</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
        </tbody>
      </table>

    </div>
  </div>

</div>

<!-- start modal add / edit -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="formTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 500px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formTitle">Tambah Menu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">

        <div class="container-fluid">
          <div class="bs-form bs-form-1">
            <label for="input_add_kodeMenu">Kode Menu</label>
            <input type="text" class="form-control" id="input_add_kodeMenu" placeholder="Kode Menu" maxlength="25" autocomplete="off">

            <label for="input_add_keterangan">Keterangan</label>
            <input type="text" class="form-control" id="input_add_keterangan" placeholder="Keterangan" maxlength="500">

            <label for="input_add_L0">L0</label>
            <input type="number" class="form-control text-right" id="input_add_L0" min="0" max="255" step="1">

            <label for="input_add_access">Kode Akses</label>
            <input type="number" class="form-control text-right" id="input_add_access" min="0" step="1">

            <label for="input_add_otorisasiLevel">Level Otorisasi</label>
            <input type="number" class="form-control text-right" id="input_add_otorisasiLevel" min="0" max="255" step="1">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" id="FooterOptionAdd" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
        <button type="button" id="FooterOptionEdit" class="btn btn-sm btn-chip-biru" onclick="submitEdit()" hidden>Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal add / edit-->

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Nilai dari database ditulis ke HTML lewat fungsi ini supaya keterangan yang mengandung
// kutip atau < > tidak merusak baris tabel.
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
    url: "{!! url('berkasmenuloadall') !!}",
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
            <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit(${i})"><i class="bi bi-pen"></i></button>
            <button title="Hapus" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete(${i})"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${spEsc(item.KODEMENU)}</td>
    <td>${spEsc(item.Keterangan)}</td>
    <td>${spEsc(item.L0)}</td>
    <td>${spEsc(item.ACCESS)}</td>
    <td>${spEsc(item.OL)}</td>
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

function kodeDari (idx) {
  return dataRefresh[idx] ? dataRefresh[idx].KODEMENU : ''
}

function buttonAdd () {
  document.getElementById("input_add_kodeMenu").value = "";
  document.getElementById("input_add_keterangan").value = "";
  document.getElementById("input_add_L0").value = 0;
  document.getElementById("input_add_access").value = 0;
  document.getElementById("input_add_otorisasiLevel").value = 0;

  document.getElementById("input_add_kodeMenu").readOnly = false;

  document.getElementById('formTitle').innerHTML = 'Tambah Menu'

  document.getElementById('FooterOptionAdd').hidden = false;
  document.getElementById('FooterOptionEdit').hidden = true;

  $("#form").modal('show')

}

function buttonEdit (idx) {
  let kode = kodeDari(idx)
  if (!kode) { return }
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('berkasmenuspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      KODEMENU : kode
    },
    success: function(res) {
      if (!Array.isArray(res) || !res.length) {
        alertify.warning('Menu ' + spEsc(kode) + ' tidak ditemukan')
        return
      }

      document.getElementById("input_add_kodeMenu").value = res[0].KODEMENU
      document.getElementById("input_add_keterangan").value = res[0].Keterangan ?? ''
      document.getElementById("input_add_L0").value = res[0].L0 ?? 0;
      document.getElementById("input_add_access").value = res[0].ACCESS ?? 0;
      document.getElementById("input_add_otorisasiLevel").value = res[0].OL ?? 0;

      document.getElementById("input_add_kodeMenu").readOnly = true;

      document.getElementById('formTitle').innerHTML = 'Edit Menu'

      document.getElementById('FooterOptionAdd').hidden = true;
      document.getElementById('FooterOptionEdit').hidden = false;

      $("#form").modal('show')
    },
    error: spGagal
  })
}

function buttonDelete (idx) {
  let kode = kodeDari(idx)
  if (!kode) { return }
  let _token = $("#_token").val();

  alertify.confirm('Hapus Menu', 'Apakah yakin ingin menghapus Kode Menu ' + spEsc(kode) + ' ?',
      function() {

        $.ajax({
          url: "{!! url('berkasmenuspdelete') !!}",
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
              alertify.success("Menu telah dihapus");
            }
          },
          error: spGagal
        })
      }
    ,function(){
    });

}

// Isi form dibaca & dicek sama untuk Tambah dan Edit.
function ambilForm () {
  return {
    KODEMENU: String($("#input_add_kodeMenu").val() ?? '').trim(),
    Keterangan: String($("#input_add_keterangan").val() ?? '').trim(),
    L0: String($("#input_add_L0").val() ?? '').trim(),
    ACCESS: String($("#input_add_access").val() ?? '').trim(),
    OL: String($("#input_add_otorisasiLevel").val() ?? '').trim()
  }
}

function cekForm (d) {
  if (!d.KODEMENU) { return "Kode harus diisi" }
  if (!d.Keterangan) { return "Keterangan harus diisi" }
  // Tipe kolom DBMENUWEB: L0 & OL tinyint (0-255), ACCESS int.
  if (!/^\d+$/.test(d.L0) || Number(d.L0) > 255) { return "L0 harus diisi angka 0-255" }
  if (!/^\d+$/.test(d.ACCESS) || Number(d.ACCESS) > 2147483647) { return "Kode Akses harus diisi angka" }
  if (!/^\d+$/.test(d.OL) || Number(d.OL) > 255) { return "Level Otorisasi harus diisi angka 0-255" }
  return ''
}

function submitEdit () {

  let _token = $("#_token").val();
  let data = ambilForm()
  let pesan = cekForm(data)
  if (pesan) {
    alertify.warning(pesan)
    return
  }

  $.ajax({
    url: "{!! url('berkasmenuspedit') !!}",
    type: "post",
    async: false,
    data: Object.assign({ _token: _token }, data),
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success("Data Kode Menu telah diedit");
        $("#form").modal('hide')
        loadAll()
      }

    },
    error: spGagal
  })

}

function submitAdd () {

  let _token = $("#_token").val();
  let data = ambilForm()
  let pesan = cekForm(data)
  if (pesan) {
    alertify.warning(pesan)
    return
  }

  $.ajax({
    url: "{!! url('berkasmenuspadd') !!}",
    type: "post",
    async: false,
    data: Object.assign({ _token: _token }, data),
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        alertify.success("Data Kode Menu telah ditambah");
        $("#form").modal('hide')
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
