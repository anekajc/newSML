@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Nomor Faktur Pajak')
@section('content')




  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Nomor Faktur Pajak</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Nomor Faktur Pajak</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Nomor Faktur Pajak</button>
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
                    <th style="padding: 4px 12px;" scope="col">No Seri</th>
                    <th style="padding: 4px 12px;" scope="col">No Awal</th>
                    <th style="padding: 4px 12px;" scope="col">No Akhir</th>
                    <th style="padding: 4px 12px;" scope="col">Is Penuh?</th>
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
          <label for="input_add_kode">Kode Faktur Pajak</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Faktur Pajak" disabled>

          <label for="seri1">No. Seri</label>
          <div class="d-flex" style="gap:8px">
            <input type="text" class="form-control" id="seri1" maxlength="3" placeholder="Seri 1" style="flex:0 0 90px">
            <input type="text" class="form-control" id="seri2" maxlength="2" placeholder="Seri 2" style="flex:0 0 80px">
            <input type="text" class="form-control" id="input_add_nama" placeholder="No. Seri" disabled>
          </div>

          <label for="input_add_noawal">No. Awal</label>
          <input type="number" class="form-control" id="input_add_noawal" placeholder="No Awal">

          <label for="input_add_noakhir">No. Akhir</label>
          <input type="number" class="form-control" id="input_add_noakhir" placeholder="No Akhir">

          <label for="input_add_penuh">Is Penuh?</label>
          <div class="bs-check"><input type="checkbox" id="input_add_penuh"></div>
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
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 550px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Kode Faktur Pajak</label>
          <input type="text" class="form-control" id="input_edit_kode" placeholder="Kode Faktur Pajak" disabled>

          <label for="seri1edit">No. Seri</label>
          <div class="d-flex" style="gap:8px">
            <input type="text" class="form-control" id="seri1edit" maxlength="3" placeholder="Seri 1" style="flex:0 0 90px">
            <input type="text" class="form-control" id="seri2edit" maxlength="2" placeholder="Seri 2" style="flex:0 0 80px">
            <input type="text" class="form-control" id="input_edit_nama" placeholder="No. Seri" disabled>
          </div>

          <label for="input_edit_noawal">No. Awal</label>
          <input type="number" class="form-control" id="input_edit_noawal" placeholder="No Awal">

          <label for="input_edit_noakhir">No. Akhir</label>
          <input type="number" class="form-control" id="input_edit_noakhir" placeholder="No Akhir">

          <label for="input_edit_penuh">Is Penuh?</label>
          <div class="bs-check"><input type="checkbox" id="input_edit_penuh"></div>
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

  // document.getElementById('breadcrumb').innerHTML = "Master Nomor Faktur Pajak" // dimatikan: judul sekarang di bar atas (page-title)

  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masternomorfakturpajakloadall') !!}",
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
            <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Kode}')"><i class="bi bi-pen"></i></button>
            <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Kode}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    <td>${item.Kode}</td>
    <td>${item.NoSeri}</td>
    <td>${item.NoAwal}</td>
    <td>${item.NoAkhir}</td>
    <td class="text-center">
    <input type="checkbox" class="is-penuh-checkbox" data-kode="${item.Kode}" ${item.IsPenuh == 1 ? 'checked' : ''}>
    </td>

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

  document.getElementById('seri1').value = '';
  document.getElementById('seri2').value = '';
  document.getElementById('input_add_nama').value = '';
  document.getElementById('input_add_noawal').value = '';
  document.getElementById('input_add_noakhir').value = '';
  document.getElementById('input_add_penuh').checked = false;

  $("#form").modal('toggle')

}

$(document).on('change', '.is-penuh-checkbox', function () {
  let _token = $("#_token").val();
  let kode = $(this).data('kode');
  let isPenuh = $(this).prop('checked') ? 1 : 0;

  $.ajax({
    url: "{!! url('masternomorfakturpajakupdatepenuh') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kode,
      isPenuh
    },
    success: function (res) {
      alertify.success("Is Penuh berhasil diperbarui");
    }
  })
})

function buttonEdit(kode) {
  console.log(kode);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternomorfakturpajakspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kode: kode
    },
    success: function (res) {
      console.log(res);
      document.getElementById("input_edit_kode").value = res[0].Kode;

      // Split the NoSeri into seri1 and seri2
      const noSeriParts = String(res[0].NoSeri || '').split('.');
      const seri1Value = (noSeriParts[0] || '').substring(0, 3);
      const seri2Value = (noSeriParts[1] || '').substring(0, 2);

      console.log(seri1Value);
      console.log(seri2Value);

      document.getElementById("seri1edit").value = seri1Value;
      document.getElementById("seri2edit").value = seri2Value;
      document.getElementById("input_edit_noawal").value = res[0].NoAwal;
      document.getElementById("input_edit_noakhir").value = res[0].NoAkhir;
      document.getElementById("input_edit_penuh").checked = res[0].IsPenuh == 1;
      // Dulu No. Seri gabungan hanya diperbarui saat Seri 1/2 diketik - kalau langsung Simpan,
      // yang terkirim nilai lama/".." dan menimpa NoSeri di database.
      updateConcatenatedSeriEdit();
    }
  });
  $("#formEdit").modal('toggle');
}


function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Area', 'Apakah yakin ingin menghapus Jenis ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masternomorfakturpajakspdelete') !!}",
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
              alertify.success("Jenis telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let kode = $("#input_edit_kode").val();
  let nama = $("#input_edit_nama").val();
  let noawal = $("#input_edit_noawal").val();
  let noakhir = $("#input_edit_noakhir").val();
  const isPenuh = $('#input_edit_penuh').prop('checked') ? 1 : 0;

  console.log(kode,nama)
  if (!kode) {
    alertify.warning("Kode  harus diisi");
    return
  }

  if (!nama || !nama.replace(/\./g, '')) {
    alertify.warning("No. Seri harus diisi");
    return
  }

  if (!noawal) {
    alertify.warning("No. Awal harus diisi");
    return
  }

  if (!noakhir) {
    alertify.warning("No. Akhir harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternomorfakturpajakspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      noawal,
      noakhir,
      isPenuh: isPenuh,

    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Jenis telah diedit");
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
  let noawal = $("#input_add_noawal").val();
  let noakhir = $("#input_add_noakhir").val();
  const isPenuh = $('#input_add_penuh').prop('checked') ? 1 : 0;

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama || !nama.replace(/\./g, '')) {
    alertify.warning("No. Seri harus diisi");
    return
  }

  if (!noawal) {
    alertify.warning("No. Awal harus diisi");
    return
  }

  if (!noakhir) {
    alertify.warning("No. Akhir harus diisi");
    return
  }

console.log(isPenuh);
  $.ajax({
    url: "{!! url('masternomorfakturpajakspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      noawal,
      noakhir,
      isPenuh: isPenuh,
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Jenis telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

    // Function to set the default Urut value
    function setDefaultUrut() {
      // Make an asynchronous request to get the highest Urut value from the database
      $.ajax({
        url: 'masternomorfakturpajakdefault',
        method: 'GET',
        success: function (data) {
          const highestUrut = Number(data.highestUrut) || 0;
          const newUrut = highestUrut + 1;

          console.log('Retrieved highestUrut:', highestUrut);
          console.log('New Urut:', newUrut);
          $("#input_add_kode").val(newUrut);
        },
        error: function (error) {
          console.error('Error fetching highest Urut value:', error);
        },
      });
    }

    // Call the setDefaultUrut function when the modal is opened
    $("#form").on("show.bs.modal", function () {
      setDefaultUrut();
    });

    const seri1Input = document.getElementById("seri1");
    const seri2Input = document.getElementById("seri2");
    const concatenatedSeriInput = document.getElementById("input_add_nama");

    // Add event listeners to both input fields
    seri1Input.addEventListener("input", updateConcatenatedSeri);
    seri2Input.addEventListener("input", updateConcatenatedSeri);

    // Function to update the concatenated value
    function updateConcatenatedSeri() {
      const seri1Value = seri1Input.value;
      const seri2Value = seri2Input.value;

      // Concatenate seri1, ".", and seri2
      const concatenatedSeri = `${seri1Value}.${seri2Value}.`;

      // Set the concatenated value to the input field
      concatenatedSeriInput.value = concatenatedSeri;
    }

    // Initialize the concatenated value when the page loads
    updateConcatenatedSeri();


    // Get references to the input fields
        const seri1InputEdit = document.getElementById("seri1edit");
        const seri2InputEdit = document.getElementById("seri2edit");
        const concatenatedSeriInputEdit = document.getElementById("input_edit_nama");

        // Add event listeners to both input fields
        seri1InputEdit.addEventListener("input", updateConcatenatedSeriEdit);
        seri2InputEdit.addEventListener("input", updateConcatenatedSeriEdit);

        // Function to update the concatenated value
        function updateConcatenatedSeriEdit() {
          const seri1Value = seri1InputEdit.value;
          const seri2Value = seri2InputEdit.value;

          // Concatenate seri1, ".", and seri2
          const concatenatedSeri = `${seri1Value}.${seri2Value}.`;

          // Set the concatenated value to the input field
          concatenatedSeriInputEdit.value = concatenatedSeri;
        }

        // Initialize the concatenated value when the page loads
        updateConcatenatedSeriEdit();

window.onload = function (){
  loadAll();
};

</script>



@endsection
