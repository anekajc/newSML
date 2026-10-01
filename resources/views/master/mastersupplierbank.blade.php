@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Supplier Bank')
@section('content')




  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Supplier Bank Account</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Supplier Bank Account</h1>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Add Supplier Bank Account</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster', ['tanpaTambah' => true])

          <table id="tabel" class="data-table po-aksi-hover">
            <thead id="tabel_header" class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="tabel_data" class="text-left"></tbody>
          </table>

          <div class="po-rt-hint">
            <i class="bi bi-info-circle"></i>
            Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
          </div>

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
          <label for="input_add_kode">Kode Valas</label>
          <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Valas">

          <label for="input_add_nama">Nama Valas</label>
          <input type="text" class="form-control" id="input_add_nama" placeholder="Nama Valas">

          <label for="input_add_kurs">Kurs</label>
          <input type="text" class="form-control text-right" id="input_add_kurs" 
                    value="0.00" 
                    style="font-variant-numeric: tabular-nums;" 
                    oninput="formatNumber(this)">

          <label for="input_add_simbol">Simbol</label>
          <input type="text" class="form-control" id="input_add_simbol" placeholder="Simbol">
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
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_edit_kode">Kode Supplier</label>
          <input type="text" class="form-control" id="input_edit_kode"disabled>

          <label for="input_edit_bankTemp">Bank Temp</label>
          <input type="text" class="form-control" id="input_edit_bankTemp">

          <label for="input_edit_noAccTemp">No. ACC Temp</label>
          <input type="text" class="form-control" id="input_edit_noAccTemp">

          <label for="input_edit_atnTemp">ATN Temp</label>
          <input type="text" class="form-control" id="input_edit_atnTemp">
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
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MSB_KOLOM = [
  ['KODECUSTSUPP', 'Kode Supplier', 1, 'varchar', 0, 0],
  ['NAMACUSTSUPP', 'Nama Supplier', 1, 'varchar', 0, 0],
  ['bank',         'Bank',          1, 'varchar', 0, 0],
  ['NoAcc',        'No. Acc',       1, 'varchar', 0, 0],
  ['ATN',          'ATN',           1, 'varchar', 0, 0],
  ['bankTemp',     'Bank Temp',     1, 'varchar', 0, 0],
  ['NoAccTemp',    'No ACC Temp',   1, 'varchar', 0, 0],
  ['ATNTemp',      'AN Temp',       1, 'varchar', 0, 0],
]

// Data tabel utama disimpan terpisah dari dataRefresh - pemilih (valas, gudang, dsb) memakai
// dataRefresh untuk daftarnya sendiri, dan renderTabel() dipanggil ulang saat kolom digeser.
let dataTabel = []

function renderTabel () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  let rowTable = ""
  dataTabel.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
          <button title="Update Bank" class="btn-action-sm btn-action-primary" type="button" onclick="buttonUpdateBank('${item.KODECUSTSUPP}')"><i class="bi bi-send"></i></button>
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODECUSTSUPP}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.KODECUSTSUPP}')"><i class="bi bi-trash"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi)
  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')
}

function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('mastersupplierbankloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
    },
    success: function(res) {
      dataTabel = res
  }})

  renderTabel()
}

function buttonAdd () {
  document.getElementById("input_add_kode").value = ""
  document.getElementById("input_add_nama").value = ""
  document.getElementById("input_add_kurs").value = ""
  document.getElementById("input_add_simbol").value = ""

  $("#form").modal('toggle')

}

function buttonEdit (kode) {
  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersupplierbankspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_kode").value = res[0].KodeCustSupp
      document.getElementById("input_edit_bankTemp").value = res[0].BankTemp
      document.getElementById("input_edit_noAccTemp").value = res[0].NoaccTemp
      document.getElementById("input_edit_atnTemp").value = res[0].ATNTemp

    
    }})
    $("#formEdit").modal('toggle')
}


function buttonDelete (kode) {

  let _token = $("#_token").val();

  // Menghapus = mengosongkan Bank/No. Acc/ATN sementara (Temp) supplier ini. Dulu langsung jalan
  // tanpa konfirmasi dan pesannya "telah diedit".
  alertify.confirm('Hapus Bank Temp', 'Apakah yakin ingin menghapus data bank sementara ' + kode + ' ?',
    function () {
      $.ajax({
        url: "{!! url('mastersupplierbankspedit') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          kode
        },
        success: function(res) {
          if (res != 1) {
            alertify.warning(res);
          }  else {
            alertify.success("Data bank sementara telah dihapus");
            loadAll()
          }
        }})
    },
    function () {});

}

function buttonUpdateBank (kode) {
  console.log(kode)
  let _token = $("#_token").val();

  alertify.confirm('Update Bank Account', 'Update Bank Account ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersupplierbankspupdate') !!}",
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
              alertify.success("Data telah di-update");

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
  let bankTemp = $("#input_edit_bankTemp").val();
  let noAccTemp = $("#input_edit_noAccTemp").val();
  let atnTemp = $("#input_edit_atnTemp").val();

  $.ajax({
    url: "{!! url('mastersupplierbankspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      bankTemp,
      noAccTemp,
      atnTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Supplier Bank Acc. telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();
  let kode = $("#input_add_kode").val();
  let nama = $("#input_add_nama").val();
  let kurs = parseFloat(($("#input_add_kurs").val() || 0).toString().replace(/,/g, '')) || 0;
  let simbol = $("#input_add_simbol").val();

  if (!kode) {
    alertify.warning("Kode harus diisi");
    return
  }

  if (!nama) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Nama harus diisi");
    return
  }

  if (!simbol) {
    alertify.warning("Nama harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('mastervalasspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kode,
      nama,
      kurs,
      simbol
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Valas telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

}

window.onload = function(){
  MasterList.kolom({ href: 'mastersupplierbankacc', kolom: MSB_KOLOM, onChange: renderTabel })
  loadAll();
};

</script>



@endsection
