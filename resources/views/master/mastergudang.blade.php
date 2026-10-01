@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Gudang')
@section('content')




  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Gudang</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Gudang</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Gudang</button>
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
              <th style="padding: 4px 12px;" scope="col">Kode Gudang</th>
              <th style="padding: 4px 12px;" scope="col">Nama Gudang</th>
              <th style="padding: 4px 12px;" scope="col">Sample</th>
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
        <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />
        <div class="bs-form bs-form-1">
          <label for="input_add_kode">Kode Gdg</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Gdg">

          <label for="input_add_nama">Nama Gdg</label>
          <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Gdg">

          <label for="input_add_alamat">Alamat</label>
          <input type="text" class="form-control" id="input_add_alamat" placeholder="Alamat">

          <label>Opsi</label>
          <div class="bs-check">
            <span class="bs-cek"><input type="checkbox" id="input_add_issample" name="" value=""> <span>Sample</span></span>
            <span class="bs-cek"><input type="checkbox" id="input_add_ispusat" name="" value=""> <span>Pusat</span></span>
            <span class="bs-cek"><input type="checkbox" id="input_add_issampit" name="" value=""> <span>Sampit</span></span>
            <span class="bs-cek"><input type="checkbox" id="input_add_isaktif" name="" value=""> <span>Aktif</span></span>
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


<!-- start modal add -->
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
        <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Kode Gdg</label>
          <input type="text" class="form-control" disabled id="input_edit_kode" placeholder="Kode Gdg">

          <label for="input_edit_nama">Nama Gdg</label>
          <input type="text" class="form-control" id="input_edit_nama" placeholder="Nama Gdg">

          <label for="input_edit_alamat">Alamat</label>
          <input type="text" class="form-control" id="input_edit_alamat" placeholder="Alamat">

          <label>Opsi</label>
          <div class="bs-check">
            <span class="bs-cek"><input type="checkbox" id="input_edit_issample" name="" value=""> <span>Sample</span></span>
            <span class="bs-cek"><input type="checkbox" id="input_edit_ispusat" name="" value=""> <span>Pusat</span></span>
            <span class="bs-cek"><input type="checkbox" id="input_edit_issampit" name="" value=""> <span>Sampit</span></span>
            <span class="bs-cek"><input type="checkbox" id="input_edit_isaktif" name="" value=""> <span>Aktif</span></span>
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
<!-- End modal add-->










@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();
  
  // document.getElementById('breadcrumb').innerHTML = "Master Gudang" // dimatikan: judul sekarang di bar atas (page-title)

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastergudangloadall') !!}",
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
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEGDG}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODEGDG}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.KODEGDG}</td>
    <td>${item.NAMA}</td>`

    if (item.istakeinout == 0 ) {
      rowTable += `<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>`
    } else {
      rowTable += `<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>`
    }
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
  document.getElementById('input_add_alamat').value = ''
  document.getElementById('input_add_issample').checked = false
  document.getElementById('input_add_ispusat').checked = false
  document.getElementById('input_add_issampit').checked = false
  document.getElementById('input_add_isaktif').checked = false
}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastergudangspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KODEGDG
      document.getElementById("input_edit_nama").value = res[0].NAMA
      document.getElementById("input_edit_alamat").value = res[0].Alamat


      if (Number(res[0].istakeinout)) {
        document.getElementById("input_edit_issample").checked = true
      } else {
        document.getElementById("input_edit_issample").checked = false
      }
      if (Number(res[0].pSampit)) {
        document.getElementById("input_edit_issampit").checked = true
      } else {
        document.getElementById("input_edit_issampit").checked = false
      }
      if (Number(res[0].pPusat)) {
        document.getElementById("input_edit_ispusat").checked = true
      } else {
        document.getElementById("input_edit_ispusat").checked = false
      }
      
      if (Number(res[0].IsAktif)) {
        document.getElementById("input_edit_isaktif").checked = true
      } else {
        document.getElementById("input_edit_isaktif").checked = false
      }

    }})


    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Gudang', 'Apakah yakin ingin menghapus Gudang ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastergudangspdelete') !!}",
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
              alertify.success("Gudang telah dihapus");

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
  let alamat = $("#input_edit_alamat").val();
  console.log(kode,nama)
  let issample = 0
  let issampit = 0
  let ispusat = 0
  let isAktif = 0

  if (document.getElementById("input_edit_issample").checked) {
    issample= 1
  }
  if (document.getElementById("input_edit_issampit").checked) {
    issampit= 1
  }
  if (document.getElementById("input_edit_ispusat").checked) {
    ispusat= 1
  }
  if (document.getElementById("input_edit_isaktif").checked) {
    isAktif= 1
  }

  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama  harus diisi");
    return
  }
  if (!alamat) {
    alertify.warning("Alamat  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergudangspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      alamat,
      issample,
      ispusat,
      issampit,
      isAktif
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Gudang telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();
  let alamat = $("#input_add_alamat").val();
  let issample = 0
  let issampit = 0
  let ispusat = 0
  let isAktif = 0

  if (document.getElementById("input_add_issample").checked) {
    issample= 1
  }
  if (document.getElementById("input_add_issampit").checked) {
    issampit= 1
  }
  if (document.getElementById("input_add_ispusat").checked) {
    ispusat= 1
  }
  
  if (document.getElementById("input_add_isaktif").checked) {
    isAktif= 1
  }

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }
  if (!alamat) {
    alertify.warning("Alamat harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastergudangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      alamat,
      issample,
      ispusat,
      issampit,
      isAktif
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Gudang telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function(){
  loadAll();
}

</script>




@endsection
