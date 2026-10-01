@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Posting Hutang')
@section('content')
@include('master/partials/sidebarPosting')



<style>
  .sp-page-wrap {
    margin-right: 280px; /* clears the fixed posting-sidebar so content doesn't run under it */
  }

  .sp-page-wrap .sp-page-head,
  .sp-page-wrap #contentContainer {
    max-width: 900px;   /* stops the table from stretching edge-to-edge */
    margin-left: auto;
    margin-right: auto;
  }

  @media (max-width: 768px) {
    .sp-page-wrap {
      margin-right: 0; /* posting-sidebar slides off-screen on mobile, no need to reserve space */
    }
  }
</style>

<div class="sp-page-wrap">

  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Satuan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Posting Hutang</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Posting Hutang</button>
  </div> --}}

  <div id="contentContainer" class="container-fluid po-list-page">

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')
  <link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

    <table id="tabel" class="data-table po-aksi-hover">
          <thead>
            <tr>
              <th style="padding: 4px 12px;" scope="col">Actions</th>
              <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
              <th style="padding: 4px 12px;" scope="col">Keterangan</th>
              <th style="padding: 4px 12px;" scope="col">Kas dan Bank?</th>
            </tr>
          </thead>
          <tbody id="tabel_data" class="text-left">
          </tbody>
        </table>

    </div>
  </div>

  </div>

</div>



<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
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
          <label for="input_add_kode">Perkiraan</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_add_kode" disabled>
            <div class="input-group-append">
              <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonSelectPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
            </div>
          </div>

          <label for="input_add_nama">Kas dan Bank?</label>
          <div class="bs-check"><input type="checkbox" id="input_add_nama" value=""></div>

          <label for="input_add_uangMuka">Uang Muka?</label>
          <div class="bs-check"><input type="checkbox" id="input_add_uangMuka" value=""></div>
        </div>
      </div>
  <div class="modal-footer">
    {{--   --}}
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAdd()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal edit -->
<div class="modal fade"  id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

        <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Perkiraan</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_edit_kode" disabled>
            <div class="input-group-append">
              <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonEditSelectPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
            </div>
          </div>

          <label for="input_edit_nama">Kas dan Bank?</label>
          <div class="bs-check"><input type="checkbox" id="input_edit_nama" value=""></div>

          <label for="input_edit_uangMuka">Uang Muka?</label>
          <div class="bs-check"><input type="checkbox" id="input_edit_uangMuka" value=""></div>
        </div>
      </div>
  <div class="modal-footer">
    {{--   --}}
    <button type="button" class="btn btn-sm btn-batal-add" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEdit()">Simpan</button>
  </div>
</div>
</div>
</div>
<!-- End modal edit-->

<!-- start modal aktiva select perkiraan -->
<div class="modal fade picker-kas" id="formSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelAktivaSelectPerkiraan">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Perkiraan</th>
                    <th scope="col">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tabel_dataAktivaSelectPerkiraan" class="text-left"></tbody>
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
<!-- End modal aktiva select perkiraan-->

<!-- start modal edit aktiva select perkiraan -->
<div class="modal fade picker-kas" id="formEditSelectPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Perkiraan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelEditAktivaSelectPerkiraan">
                <thead class="text-center">
                  <tr>
                    <th scope="col">Perkiraan</th>
                    <th scope="col">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tabel_dataEditAktivaSelectPerkiraan" class="text-left"></tbody>
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
<!-- End modal edit aktiva select perkiraan-->



@endsection

@section('js')
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

function loadAll () {
  console.log('asd')
  let _token = $("#_token").val();

  // document.getElementById('breadcrumb').innerHTML = "Master Posting Hutang"; // dimatikan: judul sekarang di bar atas (page-title)
  
  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostinghutangloadall') !!}",
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
    let statusCell = '';
    if (item.IsLokalorExim == 0) {
      statusCell = `<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>`;
    } else {
      statusCell = `<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>`;
    }
    let temp = ""

    rowTable += `<tr>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="Menu" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.Perkiraan}</td>
    <td>${item.keterangan}</td>
    ${statusCell}
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
//
function buttonAdd () {
  document.getElementById('input_add_kode').value = ''
  document.getElementById('input_add_nama').checked = false
  document.getElementById('input_add_uangMuka').checked = false

  $("#form").modal('toggle')

}

let perkiraanTemp = ''

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersetpostinghutangspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].Perkiraan
      if (Number(res[0].IsLokalorExim)) {
        document.getElementById("input_edit_nama").checked = true
      } else {
        document.getElementById("input_edit_nama").checked = false
      }

      
      if (Number(res[0].IsUM)) {
        document.getElementById("input_edit_uangMuka").checked = true
      } else {
        document.getElementById("input_edit_uangMuka").checked = false
      }

      perkiraanTemp = res[0].Perkiraan

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Posting Hutang', 'Apakah yakin ingin menghapus Perkiraan Posting Hutang ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersetpostinghutangspdelete') !!}",
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
              alertify.success("Perkiraan Posting Hutang telah dihapus");

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
  
    let nama = 0
  if (document.getElementById("input_edit_nama").checked) {
    nama = 1
  }

  let uangMuka = 0
  if (document.getElementById("input_edit_uangMuka").checked) {
    uangMuka = 1
  }

  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostinghutangspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      uangMuka,
      kodeLama : perkiraanTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Posting Hutang telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}



function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = 0
  if (document.getElementById("input_add_nama").checked) {
    nama = 1
  }

  let uangMuka = 0
  if (document.getElementById("input_add_uangMuka").checked) {
    uangMuka = 1
  }
  

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastersetpostinghutangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      uangMuka
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Posting Hutang telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

  // console.log(kodearea, namaarea)
}

function backToSetPosting() {
  window.location.href = "{{ ('mastersetposting') }}";
}

function buttonSelectPerkiraan () {
  loadSelectPerkiraan()
  $("#formSelectPerkiraan").modal('toggle')
}

function loadSelectPerkiraan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostinghutangloadperkiraan') !!}",
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
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonPilihPerkiraan('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataAktivaSelectPerkiraan").innerHTML = rowTable;
  pickerKasInit('tabelAktivaSelectPerkiraan')
}

function buttonPilihPerkiraan(selectedPerkiraan) {
  $("#input_add_kode").val(selectedPerkiraan);
  $("#formSelectPerkiraan").modal("hide");

}

function buttonEditSelectPerkiraan () {
  loadEditSelectPerkiraan()
  $("#formEditSelectPerkiraan").modal('toggle')
}

function loadEditSelectPerkiraan() {
  console.log('asd');
  let _token = $("#_token").val();

  $('#tabelEditAktivaSelectPerkiraan').DataTable().destroy();

  $.ajax({
    url: "{!! url('mastersetpostinghutangloadperkiraan') !!}",
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
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonEditPilihPerkiraan('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataEditAktivaSelectPerkiraan").innerHTML = rowTable;

  pickerKasInit('tabelEditAktivaSelectPerkiraan')
  
}

function buttonEditPilihPerkiraan(selectedPerkiraan) {
  $("#input_edit_kode").val(selectedPerkiraan);
  $("#formEditSelectPerkiraan").modal("hide");

}

window.onload = function(){
  loadAll();
};

</script>




@endsection
