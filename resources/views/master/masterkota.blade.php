@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Kota')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Kota</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Kota</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Kota</button>
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
                    <th style="padding: 4px 12px;" scope="col">Kode Kota</th>
                    <th style="padding: 4px 12px;" scope="col">Nama Kota</th>
                    <th style="padding: 4px 12px;" scope="col">Kode Area</th>
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
          <label for="input_add_kodekota">Kode Kota</label>
          <input type="text" class="form-control" id="input_add_kodekota" placeholder="Kode Kota">

          <label for="input_add_namakota">Nama Kota</label>
          <input type="text" class="form-control" id="input_add_namakota" placeholder="Nama Kota">

          <label for="input_add_kodearea">Kode Area</label>
          <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_add_kodearea" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Area</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                  </select>
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
          <label for="input_edit_kodekota">Kode kota</label>
          <input type="text" class="form-control" id="input_edit_kodekota" placeholder="Kode Kota" disabled>

          <label for="input_edit_namakota">Nama Kota</label>
          <input type="text" class="form-control" id="input_edit_namakota" placeholder="Nama Kota">

          <label for="input_edit_kodearea">Kode Area</label>
          <!-- <input type="text" class="form-control" id="input_add_namaarea" placeholder="Nama Area"> -->
                  <select id="input_edit_kodearea" class="form-control" aria-label="Default select example">
                    <option selected value="0">Pilih Area</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
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

function loadAll () {
  let _token = $("#_token").val();

  // document.getElementById('breadcrumb').innerHTML = "Master Kota" // dimatikan: judul sekarang di bar atas (page-title)

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masterkotaloadall') !!}",
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
            <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KodeKota}')"><i class="bi bi-pen"></i></button>
            <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KodeKota}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${item.KodeKota}</td>
    <td>${item.NamaKota}</td>
    <td>${item.KodeArea}</td>
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
  
  document.getElementById('input_add_kodekota').value = ''
  document.getElementById('input_add_namakota').value = ''

  $.ajax({
    url: "{!! url('masterkotalistarea') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      let rowTable = `<option selected value=0>Pilih Area</option>`
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.KODEAREA}">${item.NAMAAREA}</option>
        `
      });

      document.getElementById("input_add_kodearea").innerHTML = rowTable
    }})



  $("#form").modal('toggle')

}


function buttonEdit (kodekota) {
  console.log(kodekota)
  let _token = $("#_token").val();
  let tempkodearea = ""

  // Dropdown Area di form Edit dulu tidak pernah diisi (masih opsi contoh One/Two/Three),
  // jadi area tidak bisa terpilih dan edit selalu tertahan validasi. Diisi sama seperti Add.
  $.ajax({
    url: "{!! url('masterkotalistarea') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = `<option selected value=0>Pilih Area</option>`
      res.forEach((item, i) => {
        rowTable += `
          <option value="${item.KODEAREA}">${item.NAMAAREA}</option>
        `
      });

      document.getElementById("input_edit_kodearea").innerHTML = rowTable
    }})

  $.ajax({
    url: "{!! url('masterkotaspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodekota
    },
    success: function(res) {

      console.log(res)
      // document.getElementById("input_edit_kodearea").value = res[0].KodeArea
      document.getElementById("input_edit_namakota").value = res[0].NamaKota
      document.getElementById("input_edit_kodekota").value = res[0].KodeKota
      document.getElementById("input_edit_kodearea").value = res[0].KodeArea

    }})

    $("#formEdit").modal('toggle')
}

function buttonDelete (kodekota) {
  console.log(kodekota)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Kota', 'Apakah yakin ingin menghapus Kota ' + kodekota + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterkotaspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodekota
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Kota telah dihapus");



            
          }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let kodearea = $("#input_edit_kodearea").val();
  let namakota = $("#input_edit_namakota").val();
  let kodekota = $("#input_edit_kodekota").val();

  console.log(kodearea,namakota, kodekota)
  if (!kodearea || kodearea == 0) {
    alertify.warning("Kode area harus diisi");
    return
  }

  if (!namakota) {
    alertify.warning("Nama kota harus diisi");
    return
  }

  if (!kodekota) {
    alertify.warning("Kode kota harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterkotaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodearea,
      namakota,
      kodekota
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Kota telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let kodearea = $("#input_add_kodearea").val();
  let namakota = $("#input_add_namakota").val();
  let kodekota = $("#input_add_kodekota").val();

  console.log(kodekota,namakota,kodearea)
  // return
  if (!kodekota) {
    alertify.warning("Kode kota harus diisi");
    return
  }
  if (!namakota) {
    alertify.warning("Nama kota harus diisi");
    return
  }
  if (kodearea == 0) {
    alertify.warning("Kode area harus diisi");
    return
  }





  $.ajax({
    url: "{!! url('masterkotaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodearea,
      namakota,
      kodekota
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Kota telah ditambah");
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
