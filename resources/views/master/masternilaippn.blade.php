@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Nilai PPN')
@section('content')

{{-- Dimigrasikan dari layout master.newmaster ke newmasterTest, mengikuti master/mastermerk.blade.php. --}}

<div id="contentContainer" class="container-fluid po-list-page">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />
  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster')

      <table id="tabel" class="data-table po-aksi-hover">
        <thead>
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">PPN</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal Awal</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal Akhir</th>
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

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="bs-form bs-form-1">
            <label for="input_add_kode">Urut</label>
            <input type="text" class="form-control" id="input_add_kode" placeholder="No. Urut PPN" disabled>

            <label for="input_add_tglAwal">Tanggal Awal</label>
            <input type="date" class="form-control" id="input_add_tglAwal">

            <label for="input_add_tglAkhir">Tanggal Akhir</label>
            <input type="date" class="form-control" id="input_add_tglAkhir">

            <label for="input_add_PPN">PPN</label>
            <input type="text" class="form-control" id="input_add_PPN" placeholder="PPN">
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

        <div class="container-fluid">

          <div class="bs-form bs-form-1">
            <label for="input_edit_kode">Urut PPN</label>
            <input type="text" class="form-control" id="input_edit_kode" disabled>

            <label for="input_edit_tglAwal">Tanggal Awal</label>
            <input type="date" class="form-control" id="input_edit_tglAwal">

            <label for="input_edit_tglAkhir">Tanggal Akhir</label>
            <input type="date" class="form-control" id="input_edit_tglAkhir">

            <label for="input_edit_PPN">PPN</label>
            <input type="text" class="form-control" id="input_edit_PPN" placeholder="PPN">
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


  $('#tabel').DataTable().destroy();

  $.ajax({
    url: "{!! url('masternilaippnloadall') !!}",
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
    let dbDateString = item.TglAwal;
    let dbDateString2 = item.TglAkhir;

    // Convert the database date string to a JavaScript Date object
    let dateObject = new Date(dbDateString);
    let dateObject2 = new Date(dbDateString2);

    // Extract day, month, and year components
    let day = dateObject.getDate();
    let month = dateObject.getMonth() + 1; // Months are 0-based, so add 1
    let year = dateObject.getFullYear(); // Getting last 2 digits of year
    let day2 = dateObject2.getDate();
    let month2 = dateObject2.getMonth() + 1; // Months are 0-based, so add 1
    let year2 = dateObject2.getFullYear(); // Getting last 2 digits of year

    // Format the components
    let formattedDay = (day < 10) ? `0${day}` : day;
    let formattedMonth = (month < 10) ? `0${month}` : month;
    let formattedYear = (year < 10) ? `0${year}` : year;

    let formattedDay2 = (day2 < 10) ? `0${day2}` : day2;
    let formattedMonth2 = (month2 < 10) ? `0${month2}` : month2;
    let formattedYear2 = (year2 < 10) ? `0${year2}` : year2;
    // Assemble the formatted components into the desired format 'DD-MM-YY'
    let formattedDate = `${formattedDay}-${formattedMonth}-${formattedYear}`;
    let formattedDate2 = `${formattedDay2}-${formattedMonth2}-${formattedYear2}`;

    rowTable += `<tr>
    <td style="white-space:nowrap;" class='text-center'>
      <div class="action-buttons-wrap">
        <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Urut}')"><i class="bi bi-pen"></i></button>
        <button data-toggle="tooltip" data-placement="top" title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Urut}')"><i class="bi bi-trash"></i></button>
      </div>
    </td>
    <td>${item.NilaiPPN}</td>
    <td>${formattedDate}</td>
    <td>${formattedDate2}</td>
    </tr>`
  });





  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')

}
//
function buttonAdd () {


  $("#form").modal('toggle')

}

function buttonEdit(kode) {
  console.log(kode);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masternilaippnspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kode: kode
    },
    success: function (res) {
      console.log(res);
      document.getElementById("input_edit_kode").value = res[0].Urut;

      // Convert datetime values to Date objects
      const tglAwal = new Date(res[0].TglAwal + 'Z'); // Add 'Z' to indicate UTC time
      const tglAkhir = new Date(res[0].TglAkhir + 'Z');

      // Format the date for input elements
      const formattedTglAwal = tglAwal.toISOString().split('T')[0];
      const formattedTglAkhir = tglAkhir.toISOString().split('T')[0];

      document.getElementById("input_edit_tglAwal").value = formattedTglAwal;
      document.getElementById("input_edit_tglAkhir").value = formattedTglAkhir;
      document.getElementById("input_edit_PPN").value = MasterList.nolDepan(res[0].NilaiPPN);
    }
  });
  $("#formEdit").modal('toggle');
}



function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Nilai PPN', 'Apakah yakin ingin menghapus Nilai PPN urut ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masternilaippnspdelete') !!}",
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
              alertify.success("Nilai PPN telah dihapus");

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
  let tglAwal = $("#input_edit_tglAwal").val();
  let tglAkhir = $("#input_edit_tglAkhir").val();
  let PPN = $("#input_edit_PPN").val();
  console.log(kode)

  $.ajax({
    url: "{!! url('masternilaippnspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      tglAwal,
      tglAkhir,
      PPN
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let tglAwal = $("#input_add_tglAwal").val();
  let tglAkhir = $("#input_add_tglAkhir").val();
  let PPN = $("#input_add_PPN").val();

  console.log(kode)
  console.log(tglAwal)
  console.log(tglAkhir)
  console.log(PPN)

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!tglAwal) {
    alertify.warning("Tanggal harus diisi");
    return
  }

  if (!tglAkhir) {
    alertify.warning("Tanggal harus diisi");
    return
  }

  if (!PPN) {
    alertify.warning("PPN harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masternilaippnspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      tglAwal,
      tglAkhir,
      PPN
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Nilai PPN telah ditambah");
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
    url: 'masternilaippndefault',
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


window.onload = function (){
  loadAll();
};


</script>




@endsection
