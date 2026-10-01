@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Group')
@section('content')
  


  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Group</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Group</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Group</button>
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
                    <th style="padding: 4px 12px;" scope="col">Kode Group</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Group</th>
                  </tr>
                </thead>
                <tbody id="tabel_data" class="text-left">
              </tbody>
              </table>

    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
          <label for="input_add_kode">Kode Group</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Group">

          <label for="input_add_nama">Nama Group</label>
          <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Group">
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
          <label for="input_edit_kode">Kode Group</label>
          <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode Group" disabled>

          <label for="input_edit_nama">Nama Group</label>
          <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama Group">
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

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  // document.getElementById('breadcrumb').innerHTML = "Master Group" // dimatikan: judul sekarang di bar atas (page-title)

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergrouploadall') !!}",
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
            <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEGRP}')"><i class="bi bi-pen"></i></button>
            <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEGRP}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${item.KODEGRP}</td>
    <td>${item.NAMA}</td>
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
  $("#form").modal('toggle')
  document.getElementById('input_add_kode').value = ''
  document.getElementById('input_add_nama').value = ''

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastergroupspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODEGRP
      document.getElementById("input_edit_nama").value = res[0].NAMA

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Group', 'Apakah yakin ingin menghapus Group ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastergroupspdelete') !!}",
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
              alertify.success("Group telah dihapus");

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
  let nama = $("#input_edit_nama").val();
  console.log(kode,nama)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergroupspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Group telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergroupspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Group telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function(){
  loadAll();
};

</script>




@endsection
