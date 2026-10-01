@extends('newmasterTest')
@section('buttons')

@endsection


@section('page-title', 'Master Posting Aktiva')
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

  {{-- DINONAKTIFKAN - judul sudah di bar atas (page-title) dan tombol Tambah ada di toolbar.
  <div class="sp-page-head">
    <div>
      <h1>Master Posting Aktiva</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Posting Aktiva</button>
  </div> --}}

  <div id="contentContainer" class="container-fluid po-list-page">

    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

    <div class="card">
    <div class="card-body" style="padding:0;">

  @include('master.partials.toolbarMaster')

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

</div>

<!-- start modal add posting aktiva-->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Posting Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="bs-form">
          <label for="input_add_perkiraan">Perkiraan</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_add_perkiraan" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
          <label for="input_add_biayaPenyusutan1">Biaya Penyusutan 1</label>
          <div class="d-flex align-items-center" style="gap:8px"><div class="input-group">
                <input type="text" class="form-control" id="input_add_biayaPenyusutan1" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraanBP1()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
            <div class="input-group">
                <input type="text" class="form-control" id="input_add_persenBiaya1" placeholder='(%)'>
              </div></div>


          
          <label for="input_add_akm">Akumulasi Penyusutan</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_add_akm" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonAkumulasi()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
          <label for="input_add_biayaPenyusutan2">Biaya Penyusutan 2</label>
          <div class="d-flex align-items-center" style="gap:8px"><div class="input-group">
                <input type="text" class="form-control" id="input_add_biayaPenyusutan2" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraanBP2()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
            <div class="input-group">
                <input type="text" class="form-control" id="input_add_persenBiaya2" placeholder='(%)'>
              </div></div>


          
          <label for="input_add_persenSusut">Persen Susut</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_add_persenSusut" placeholder='(%)'>
              </div>
          <label for="input_add_metodePenyusutan">Metode Penyusutan</label>
          <div class="d-flex align-items-center" style="gap:8px"><select name="MedPenyu" class='form-control' id="input_add_metodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
            <input type="checkbox" id="input_add_uangMuka" value="">
              <label for="input_add_uangMuka" class="form-label mb-0">Uang Muka</label></div>
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
<!-- End modal add posting aktiva-->

<!-- start modal add posting aktiva-->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 1000px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Posting Aktiva</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="bs-form">
          <label for="input_edit_perkiraan">Perkiraan</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_edit_perkiraan" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
          <label for="input_edit_biayaPenyusutan1">Biaya Penyusutan 1</label>
          <div class="d-flex align-items-center" style="gap:8px"><div class="input-group">
                <input type="text" class="form-control" id="input_edit_biayaPenyusutan1" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraanBP1()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
            <div class="input-group">
                <input type="text" class="form-control" id="input_edit_persenBiaya1" placeholder='(%)'>
              </div></div>


          
          <label for="input_edit_akm">Akumulasi Penyusutan</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_edit_akm" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonAkumulasi()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
          <label for="input_edit_biayaPenyusutan2">Biaya Penyusutan 2</label>
          <div class="d-flex align-items-center" style="gap:8px"><div class="input-group">
                <input type="text" class="form-control" id="input_edit_biayaPenyusutan2" disabled>
                <div class="input-group-append">
                  <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraanBP2()" title="Cari"><i class="bi bi-search"></i></button>
                </div>
              </div>
            <div class="input-group">
                <input type="text" class="form-control" id="input_edit_persenBiaya2" placeholder='(%)'>
              </div></div>


          
          <label for="input_edit_persenSusut">Persen Susut</label>
          <div class="input-group">
                <input type="text" class="form-control" id="input_edit_persenSusut" placeholder='(%)'>
              </div>
          <label for="input_edit_metodePenyusutan">Metode Penyusutan</label>
          <div class="d-flex align-items-center" style="gap:8px"><select name="MedPenyu" class='form-control' id="input_edit_metodePenyusutan">
                <option value="L">[L]urus</option>
                <option value="M">[M]enurun</option>
                <option value="P">[P]ajak</option>
              </select>
            <input type="checkbox" id="input_edit_uangMuka" value="">
              <label for="input_edit_uangMuka" class="form-label mb-0">Uang Muka</label></div>
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
<!-- End modal add posting aktiva-->

@include('master.partials.modalOpenMaster')

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MSPA_KOLOM = [
  ['Perkiraan',    'Perkiraan',           1, 'varchar', 0, 0],
  ['Keterangan',   'Keterangan',          1, 'varchar', 0, 0],
  ['Persen',       'Persen',              1, 'varchar', 0, 0],
  ['Tipe',         'Metode',              1, 'varchar', 0, 0],
  ['Akumulasi',    'Perkiraan Akumulasi', 1, 'varchar', 0, 0],
  ['Biaya1',       'Perkiraan Biaya 1',   1, 'varchar', 0, 0],
  ['PersenBiaya1', 'Persen Biaya 1',      1, 'varchar', 0, 0],
  ['Biaya2',       'Perkiraan Biaya 2',   1, 'varchar', 0, 0],
  ['PersenBiaya2', 'Persen Biaya 2',      1, 'varchar', 0, 0],
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
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Perkiraan}')"><i class="bi bi-trash"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi)
  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')
}

function loadAll () {
  let _token = $("#_token").val();

  document.getElementById('judulPosting').innerHTML = 'Master Posting Aktiva'

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadall') !!}",
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
  // Form Add dikosongkan - dulu sisa isian tambah sebelumnya ikut terbawa.
  $('#form .modal-body input[type="text"], #form .modal-body input[type="number"]').each(function () { this.value = this.defaultValue })
  $('#form .modal-body input[type="checkbox"]').prop('checked', false)
  $("#form").modal('toggle')
}

function buttonEdit (kode) {

  console.log(kode)
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('mastersetpostingaktivaspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_edit_perkiraan").value = res[0].Perkiraan
      document.getElementById("input_edit_akm").value = res[0].Akumulasi
      document.getElementById("input_edit_biayaPenyusutan1").value = res[0].Biaya1
      document.getElementById("input_edit_persenBiaya1").value = MasterList.nolDepan(res[0].PersenBiaya1)
      document.getElementById("input_edit_biayaPenyusutan2").value = res[0].Biaya2
      document.getElementById("input_edit_persenBiaya2").value = MasterList.nolDepan(res[0].PersenBiaya2)
      document.getElementById("input_edit_persenSusut").value = MasterList.nolDepan(res[0].Persen)
      document.getElementById("input_edit_metodePenyusutan").value = res[0].Tipe

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


  alertify.confirm('Hapus Perkiraan', 'Apakah yakin ingin menghapus Perkiraan Posting Aktiva ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('mastersetpostingaktivaspdelete') !!}",
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
              alertify.success("Perkiraan Posting Aktiva telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

let perkiraanTemp = ''

function submitEdit () {

  let _token = $("#_token").val();

  let perkiraan = $("#input_edit_perkiraan").val();
  let akm = $("#input_edit_akm").val();
  let bp1 = $("#input_edit_biayaPenyusutan1").val();
  let persenbp1 = $("#input_edit_persenBiaya1").val();
  let bp2 = $("#input_edit_biayaPenyusutan2").val();
  let persenbp2 = $("#input_edit_persenBiaya2").val();
  let persenSusut = $("#input_edit_persenSusut").val();
  let metodePenyusutan = $("#input_edit_metodePenyusutan").val();

  let uangMuka = 0
  if (document.getElementById("input_edit_uangMuka").checked) {
    uangMuka = 1
  }
  if (!perkiraan){
    alertify.warning('Perkiraan harus diisi.')
    return;
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      perkiraan, akm, bp1, persenbp1, bp2, persenbp2, persenSusut, metodePenyusutan, uangMuka,
      kodeLama: perkiraanTemp
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
      }  
      else 
      {
        console.log(res ,'!')
        alertify.success("Data Posting Aktiva telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }
    }})
}

function submitAdd () {

  let _token = $("#_token").val();
  let perkiraan = $("#input_add_perkiraan").val();
  let akm = $("#input_add_akm").val();
  let bp1 = $("#input_add_biayaPenyusutan1").val();
  let persenbp1 = $("#input_add_persenBiaya1").val();
  let bp2 = $("#input_add_biayaPenyusutan2").val();
  let persenbp2 = $("#input_add_persenBiaya2").val();
  let persenSusut = $("#input_add_persenSusut").val();
  let metodePenyusutan = $("#input_add_metodePenyusutan").val();

  let uangMuka = 0
  if (document.getElementById("input_add_uangMuka").checked) {
    uangMuka = 1
  }

  if (!perkiraan){
    alertify.warning('Perkiraan harus diisi.')
    return;
  }
  if (!persenSusut){
    alertify.warning('Persen Susut tidak boleh kosong.')
    return;
  }

  if (!bp2){
    bp2 = '-'
  }

  if (!persenbp2){
    persenbp2 = 0
  }
  
  $.ajax({
    url: "{!! url('mastersetpostingaktivaspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      perkiraan, akm, bp1, persenbp1, bp2, persenbp2, persenSusut, metodePenyusutan, uangMuka
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Posting Aktiva telah ditambah");
        loadAll()
        $("#form").modal('hide')
      }

    }})

}

window.onload = function(){
MasterList.kolom({ href: 'mastersetpostingaktiva', kolom: MSPA_KOLOM, onChange: renderTabel })
loadAll();
}

function buttonPerkiraan () {

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
    }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadperkiraan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectPerkiraan('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Select Perkiraan'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectPerkiraan(perkiraan){
  document.getElementById('input_add_perkiraan').value = perkiraan;
  document.getElementById('input_edit_perkiraan').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonAkumulasi () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadakumulasi') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectAkumulasi('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Akumulasi'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectAkumulasi(perkiraan){
  document.getElementById('input_add_akm').value = perkiraan;
  document.getElementById('input_edit_akm').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonPerkiraanBP1 () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadperkiraanpenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectBP1('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Perkiraan Biaya Penyusutan 1'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectBP1(perkiraan){
  document.getElementById('input_add_biayaPenyusutan1').value = perkiraan;
  document.getElementById('input_edit_biayaPenyusutan1').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

function buttonPerkiraanBP2 () {
  console.log('asd');

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('mastersetpostingaktivaloadperkiraanpenyusutan') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectBP2('${item.Perkiraan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Perkiraan Biaya Penyusutan 2'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectBP2(perkiraan){
  document.getElementById('input_add_biayaPenyusutan2').value = perkiraan;
  document.getElementById('input_edit_biayaPenyusutan2').value = perkiraan;

  $("#formModalOpen").modal("hide");
}

</script>




@endsection
