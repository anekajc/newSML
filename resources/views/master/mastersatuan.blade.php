@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Satuan')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Satuan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Satuan</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Satuan</button>
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
            <th style="padding: 4px 12px;" scope="col">Kode Satuan</th>
            <th style="padding: 4px 12px;" scope="col">Kode Sat Tax</th>
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
          <label for="input_add_kodeSatuan">Kode Satuan</label>
          <input type="text" class="form-control" id="input_add_kodeSatuan">

          <label for="input_add_satuanTax">Kode Satuan Tax</label>
          <div class="input-group"> <input type="text" class="form-control" id="input_add_satuanTax" disabled> <div class="input-group-append"> <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonAmbilSatTax()" title="Cari"><i class="bi bi-search"></i></button> </div> </div>
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
          <label for="input_edit_kodeSatuan">Kode Satuan</label>
          <input type="text" class="form-control" id="input_edit_kodeSatuan" disabled>

          <label for="input_edit_satuanTax">Kode Satuan Tax</label>
          <div class="input-group"> <input type="text" class="form-control" id="input_edit_satuanTax" disabled> <div class="input-group-append"> <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonAmbilSatTax()" title="Cari"><i class="bi bi-search"></i></button> </div> </div>
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

@include('master.partials.modalOpenMaster')

@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  // document.getElementById('breadcrumb').innerHTML = "Master Satuan" // dimatikan: judul sekarang di bar atas (page-title)
  
  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersatuanloadall') !!}",
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
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KodeSatuan}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KodeSatuan}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.KodeSatuan}</td>
    <td>${item.KodeSatTax}</td>
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
  document.getElementById("input_add_kodeSatuan").value = ""
  document.getElementById("input_add_satuanTax").value = ""

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersatuanspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kodeSatuan").value = res[0].KodeSatuan
      document.getElementById("input_edit_satuanTax").value = res[0].KodeSatTax

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Satuan', 'Apakah yakin ingin menghapus Kode Satuan ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersatuanspdelete') !!}",
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
              alertify.success("Kode Satuan telah dihapus");

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
  let kode = $("#input_edit_kodeSatuan").val();
  let nama = $("#input_edit_satuanTax").val();
  
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersatuanspedit') !!}",
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
        alertify.success("Data Satuan telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kodeSatuan").val();
  let nama = $("#input_add_satuanTax").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersatuanspadd') !!}",
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
        alertify.success("Data Satuan telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}


function buttonAmbilSatTax () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSatuanAmbilSatTax') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {

    rowTable += `<tr class="pick-row" onclick="buttonSelectSatTax('${item.KODETAX}')">
      <td>${item.KODETAX}</td>
      <td>${item.NAMATAX}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Kode</th>
    <th scope="col">Nama</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Sat Tax'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectSatTax (perkiraan){

  document.getElementById('input_add_satuanTax').value = perkiraan;
  document.getElementById('input_edit_satuanTax').value = perkiraan;
  // document.getElementById('input_target_edit_merk').value = merk;

  $("#formModalOpen").modal("hide");
}

window.onload = function(){
  loadAll();
};

</script>


@endsection
