@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Sales')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Sales</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Sales</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Sales</button>
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
          <label for="input_edit_Sales">Sales</label>
          <input type="text" class="form-control" id="input_edit_Sales" placeholder="Sales" disabled>

          <label for="input_edit_kodeGudang">Kode Gudang</label>
          <select class="form-control" id="input_edit_kodeGudang"></select>

          <label for="input_edit_kodeCost">Kode Cost</label>
          <div class="input-group"> <input type="text" class="form-control" id="input_edit_kodeCost" placeholder="Kode Cost" disabled> <div class="input-group-append"> <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonCosting()" title="Cari"><i class="bi bi-search"></i></button> </div> </div>
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

<!-- start modal sales -->
<div class="modal fade" id="formSales" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Sales Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row align-items-center">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group mb-0">
                <label class="mb-0">Kode Sales</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group mb-0">
                <input type="text" class="form-control" id="input_harga_kodesales" placeholder="Kode Sales" disabled>
              </div>
            </div>
          
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonAddSales()">Tambah</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addHarga" class="container-fluid showhide">
        <div id="formBsGrid" class="form-card mt-3">
          <div class="form-card-title">Tambah Sales/Cust</div>
          <div class="bs-form bs-form-1">
            <label for="input_custSupp_add_kodeCustSupp">Kode Cust Supp</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_custSupp_add_kodeCustSupp" placeholder="Kode Cust Supp" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonCustSupp()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>

            <label for="input_custSupp_add_mingguKe">Minggu Ke</label>
            <select id="input_custSupp_add_mingguKe" class="form-control">
              <option value ='0'>-</option>
              <option value ='1'>1</option>
              <option value ='2'>2</option>
              <option value ='3'>3</option>
              <option value ='4'>4</option>
            </select>
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddSales()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editHarga" class="container-fluid showhide">
        <div id="formBsGrid" class="form-card mt-3">
          <div class="form-card-title">Edit Sales/Cust</div>
          <div class="bs-form bs-form-1">
            <input type="hidden" id="input_custSupp_edit_kodeCustSupp_old">
            <label for="input_custSupp_edit_kodeCustSupp">Kode Cust Supp</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_custSupp_edit_kodeCustSupp" placeholder="Kode Cust Supp" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonCustSupp()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>

            <label for="input_custSupp_edit_mingguKe">Minggu Ke</label>
            <select id="input_custSupp_edit_mingguKe" class="form-control">
              <option value ='0'>-</option>
              <option value ='1'>1</option>
              <option value ='2'>2</option>
              <option value ='3'>3</option>
              <option value ='4'>4</option>
            </select>
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditSales()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_harga" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kode Customer</th>
                  <th scope="col">Nama Customer</th>
                  <th scope="col">Minggu Ke</th>

                </tr>
              </thead>

              <tbody id="tabel_data_harga" class="text-left" >

                <tr>
                  <td colspan=3></td>

                    <td class="text-center">
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>

</div>
</div>
</div>
<!-- End modal sales-->

<!-- start modal target -->
<div class="modal fade"  id="formTarget" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Target Sales</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->
        <div class="container-fluid">
          <div class="row align-items-center">

            <input type="hidden" id="input_harga_kodegroup" value="" />
            <div class="col-2">
              <div class="form-group mb-0">
                <label class="mb-0">Kode Sales</label>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group mb-0">
                <input type="text" class="form-control" id="input_target_kodesales" placeholder="Kode Sales" disabled>
              </div>
            </div>
          
            <div class="col-2 ml-auto text-right">
              <button type="button" class="btn btn-sm btn-chip-biru" onclick="buttonAddTarget()">Tambah</button>
            </div>

          </div>
    </div>

    <!-- ADD SUBGROUP -->

    <div id="addTarget" class="container-fluid showhide">
        <div id="formBsGrid" class="form-card mt-3">
          <div class="form-card-title">Tambah Target</div>
          <div class="bs-form bs-form-1">
            <label for="input_target_add_tahun">Tahun</label>
            <input type="text" class="form-control text-right" id="input_target_add_tahun" placeholder="Tahun">

            <label for="input_target_add_merk">Merk</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_target_add_merk" placeholder="Merk" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonMerk()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>

            <label for="input_target_add_target">Target</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_target_add_target" placeholder="Target">
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitAddTarget()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END ADD SUBGROUP -->

    <!-- EDIT SUBGROUP -->

    <div id="editTarget" class="container-fluid showhide">
        <div id="formBsGrid" class="form-card mt-3">
          <div class="form-card-title">Edit Target</div>
          <div class="bs-form bs-form-1">
            <label for="input_target_edit_tahun">Tahun</label>
            <input type="text" class="form-control text-right" id="input_target_edit_tahun" placeholder="Tahun" disabled>

            <label for="input_target_edit_merk">Merk</label>
            <input type="text" class="form-control" id="input_target_edit_merk" placeholder="Merk" disabled>

            <label for="input_target_edit_target">Target</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_target_edit_target" placeholder="Target">
          </div>
          <div class="text-right mt-3">
            <button type="button" class="btn btn-sm btn-batal-add" onclick="closeShowSales()">Batal</button>
            <button type="button" class="btn btn-sm btn-chip-biru" onclick="submitEditTarget()">Simpan</button>
          </div>
        </div>
      </div>

    <!-- END EDIT SUBGROUP -->

        <div class="container-fluid mt-4">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_targetSales" class="table table-bordered table-striped"  >
              <thead id='theadCustom' class="text-center">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Tahun</th>
                  <th scope="col">Kode Merk</th>
                  <th scope="col">Nama Merk</th>
                  <th scope="col">Target</th>

                </tr>
              </thead>

              <tbody id="tabel_data_targetSales" class="text-left" >

                <tr>
                  <td colspan=3></td>

                    <td class="text-center">
                      <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                      <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                    </td>
              </tr>
              </tbody>


            </table>
          </div>
            <!-- <button onclick="buttonSubKategori()">tes</button> -->


    </div>
  </div>

</div>
</div>
</div>
<!-- End modal target-->

@endsection

{{-- Modal pemilih mengikuti picker purchasing (picker-kas.css / pickerKasInit()). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

let listSelectHeadGroup = []
let listSelectSubGroup = []
let listSelectSubKategori = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MSL_KOLOM = [
  ['Nama',       'Nama Sales',  1, 'varchar', 0, 0],
  ['TeleponHP',  'Telepon HP',  1, 'varchar', 0, 0],
  ['KodeGdg',    'Kode Gudang', 1, 'varchar', 0, 0],
  ['NamaGdg',    'Nama Gudang', 1, 'varchar', 0, 0],
  ['KodeCost',   'Kode Cost',   1, 'varchar', 0, 0],
  ['NamaCost',   'Nama Cost',   1, 'varchar', 0, 0],
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
      <button class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KeyNIK}')" title="Koreksi Gudang/Cost">
        <i class="bi bi-pen"></i>
      </button>
      <button class="btn-action-sm btn-action-warning" type="button" onclick="buttonSales('${item.KeyNIK}')" title="Sales Cust">
        <i class="bi bi-receipt"></i>
      </button>
      <button class="btn-action-sm btn-action-primary" type="button" onclick="buttonTarget('${item.KeyNIK}')" title="Target">
        <i class="bi bi-bullseye"></i>
      </button>
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
    url: "{!! url('mastersalesloadall') !!}",
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

function buttonEdit (keynik) {
  console.log(keynik)

  keynikTemp = keynik;

  $.ajax({
    url: "{!! url('masterSalesSpDetail') !!}",
    type: "get",
    async: false,
    data: {
      keynik
    },
    success: function(res) {
      console.log(res)

      document.getElementById("input_edit_Sales").value = res[0].Nama
      pilihGudang(res[0].KodeGdg)
      document.getElementById("input_edit_kodeCost").value = res[0].KodeCost

    }})

    $("#formEdit").modal('toggle')
}

function buttonAddSales () {
  document.getElementById("input_custSupp_add_kodeCustSupp").value = ''
  document.getElementById("input_custSupp_add_mingguKe").value = '0'

  $('.showhide').hide();
  $('#addHarga').show()
}

function buttonAddTarget () {
  document.getElementById("input_target_add_tahun").value = ''
  document.getElementById("input_target_add_merk").value = ''
  setNum("input_target_add_target", '')

  $('.showhide').hide();
  $('#addTarget').show()
}

function buttonEditSales (kodeCustSupp, keynik) {

  $('.showhide').hide();
  $.ajax({
    url: "{!! url('masterSalesLoadDataSalesEdit') !!}",
    type: "get",
    async: false,
    data: {
      kodeCustSupp,
      keynik,
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_custSupp_edit_kodeCustSupp_old").value = res[0].kodeCustSupp
      document.getElementById("input_custSupp_edit_kodeCustSupp").value = res[0].kodeCustSupp
      document.getElementById("input_custSupp_edit_mingguKe").value = res[0].Mingguke

    }})

  $('#editHarga').show()
}

function closeShowSales () {
  $('.showhide').hide();
}

function submitEditSales () {

  let _token = $("#_token").val();

  let kodeCustSuppOld = $("#input_custSupp_edit_kodeCustSupp_old").val();
  let kodeCustSupp = $("#input_custSupp_edit_kodeCustSupp").val();
  let Mingguke = $("#input_custSupp_edit_mingguKe").val();

  if (!kodeCustSupp) {
    alertify.warning("Kode Cust Supp harus diisi");
    return
  }

$.ajax({
  url: "{!! url('masterSalesEditSalesCust') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    keynik: keynikTemp,
    kodeCustSupp,
    kodeCustSuppOld,
    Mingguke
  },
  success: function(res) {
    $('.showhide').hide();
    refreshTableSales(keynikTemp)
    alertify.success("Data Sales Cust telah dikoreksi");
  },
  error: function (err) {
    console.log(err)
    alertify.warning("Data Sales Cust gagal dikoreksi");
  }})

}

function submitAddSales () {

  let _token = $("#_token").val();
  let kodeCustSupp = $("#input_custSupp_add_kodeCustSupp").val();
  let Mingguke = $("#input_custSupp_add_mingguKe").val();

  // Dulu tanpa validasi dan tanpa pesan - customer kosong/ganda tetap dikirim.
  if (!kodeCustSupp) {
    alertify.warning("Kode Cust Supp harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterSalesAddSalesCust') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodeCustSupp,
      Mingguke,
      keynik: keynikTemp,
      NIK : nikTemp
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
        return
      }
      $('.showhide').hide();
      refreshTableSales(keynikTemp)
      alertify.success("Data Sales Cust telah ditambah");
    },
    error: function (err) {
      console.log(err)
      alertify.warning("Data Sales Cust gagal ditambah");
    }})

}

function submitAddTarget () {

  let _token = $("#_token").val();
  let Tahun = $("#input_target_add_tahun").val();
  let Merk = $("#input_target_add_merk").val();
  let TgtSales = $("#input_target_add_target").val().replace(/,/g, '');

  if (!Tahun) {
    alertify.warning("Tahun harus diisi");
    return
  }

  if (!Merk) {
    alertify.warning("Merk harus diisi");
    return
  }

  if (!TgtSales) {
    alertify.warning("Target harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterSalesAddTarget') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      Tahun,
      Merk,
      TgtSales,
      keynik: keynikTemp
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
        return
      }
      $('.showhide').hide();
      alertify.success("Data Target telah ditambah");
      refreshTableTarget(keynikTemp)
    },
    error: function (err) {
      console.log(err)
      alertify.warning("Data Target gagal ditambah");
    }})

}

// Merk ikut dikirim: target disimpan per tahun + merk, dan hapus/edit yang hanya memakai tahun
// dulu ikut mengenai target merk lain di tahun yang sama.
function buttonDeleteTarget (keynik, Tahun, Merk) {

  let _token = $("#_token").val();

  alertify.confirm('Hapus Target', 'Apakah yakin ingin menghapus Target Sales ' + Tahun + ' ' + (Merk || '') + ' ?',
      function() {

        $.ajax({
          url: "{!! url('masterSalesDeleteTarget') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            keynik,
            Tahun,
            Merk : Merk || ''
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              alertify.success("Target Sales telah dihapus");
              refreshTableTarget(keynik)
          }
          }})
      }
    ,function(){
    });
}

function submitEditTarget () {

  let _token = $("#_token").val();

  let TgtSales = $("#input_target_edit_target").val().replace(/,/g, '');
  let Merk = $("#input_target_edit_merk").val();
  let Tahun = $("#input_target_edit_tahun").val();

  if (!TgtSales) {
    alertify.warning("Target harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterSalesEditTarget') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      keynik: keynikTemp,
      TgtSales,
      Merk,
      Tahun
    },
    success: function(res) {
      $('.showhide').hide();
      refreshTableTarget(keynikTemp)
      alertify.success("Data Target telah dikoreksi");
    },
    error: function (err) {
      console.log(err)
      alertify.warning("Data Target gagal dikoreksi");
    }})

  }

function buttonEditTarget (keynik, Tahun, Merk) {

  $('.showhide').hide();
  let ketemu = false
  $.ajax({
    url: "{!! url('masterSalesLoadDataTargetEdit') !!}",
    type: "get",
    async: false,
    data: {
      Tahun,
      keynik,
      Merk : Merk || ''
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning("Data target tidak ditemukan");
        return
      }
      ketemu = true
      document.getElementById("input_target_edit_tahun").value = res[0].Tahun
      document.getElementById("input_target_edit_merk").value = res[0].Merk
      setNum("input_target_edit_target", res[0].TgtSales)

    }})

  if (ketemu) {
    $('#editTarget').show()
  }
}

function buttonDelete (kodebarang) {
  console.log('buttonDeleteSales')
  console.log(kodebarang)

  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Barang ' + kodebarang + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Barang telah dihapus");
          }
          }})
      }
    ,function(){
      console.log('no')
    });
}

function buttonDelete (kodebarang) {
  console.log('buttonDeleteSales')
  console.log(kodebarang)

  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Barang ' + kodebarang + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterbarangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            kodebarang,
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Barang telah dihapus");
          }
          }})
      }
    ,function(){
      console.log('no')
    });
}

function buttonDeleteSales (kodeCustSupp, keynik) {
  
  let _token = $("#_token").val();

  alertify.confirm('Hapus Harga', 'Apakah yakin ingin menghapus Sales ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterSalesDeleteSalesCust') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            keynik,
            kodeCustSupp
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              refreshTableSales(keynik)
              alertify.success("Data Sales Cust telah dihapus");

          }
          }})
      }
    ,function(){
      console.log('no')
    });

}

let nikTemp = 0

function buttonSales (keynik) {
  
  keynikTemp = keynik;

  $.ajax({
      url: "{!! url('masterSalesLoadKaryawan') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {

        nikTemp = res[0].NIK

      }})

  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterSalesLoadDataSales') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn-action-sm btn-action-success btn-sm hover-tooltip" onclick="buttonEditSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Edit Harga"><i class="bi bi-pen"></i></button>
            <button class="btn-action-sm btn-action-danger hover-tooltip" onclick="buttonDeleteSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Delete Harga" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.kodeCustSupp}</td>
          <td>${item.namaCustSupp}</td>
          <td>${item.Mingguke}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_harga").innerHTML = rowTable
        document.getElementById("input_harga_kodesales").value = keynik

      }})

  $("#formSales").modal('toggle')
}

function buttonTarget (keynik) {
  
  keynikTemp = keynik;

  $('.showhide').hide();

    $.ajax({
      url: "{!! url('masterSalesLoadDataTarget') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn-action-sm btn-action-success hover-tooltip" onclick="buttonEditTarget('${item.KeyNik}', '${item.Tahun}', '${item.Merk || ''}')" type="button" data-tooltip="Edit Target Sales"><i class="bi bi-pen"></i></button>
            <button class="btn-action-sm btn-action-danger hover-tooltip" onclick="buttonDeleteTarget('${item.KeyNik}', '${item.Tahun}', '${item.Merk || ''}')" type="button" data-tooltip="Delete Target Sales" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Tahun}</td>
          <td>${item.Merk}</td>
          <td>${item.namamerk}</td>
          <td>${formatNumberDisplay(MasterList.nolDepan(item.TgtSales))}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_targetSales").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = keynik

      }})


  $("#formTarget").modal('toggle')
}

function refreshTableSales(keynik){

  $.ajax({
      url: "{!! url('masterSalesLoadDataSales') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn-action-sm btn-action-success btn-sm hover-tooltip" onclick="buttonEditSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Edit Harga"><i class="bi bi-pen"></i></button>
            <button class="btn-action-sm btn-action-danger hover-tooltip" onclick="buttonDeleteSales('${item.kodeCustSupp}', '${item.Keynik}')" type="button" data-tooltip="Delete Harga" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.kodeCustSupp}</td>
          <td>${item.namaCustSupp}</td>
          <td>${item.Mingguke}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_harga").innerHTML = rowTable
        document.getElementById("input_harga_kodesales").value = keynik

      }})

}

function refreshTableTarget(keynik){

    $.ajax({
      url: "{!! url('masterSalesLoadDataTarget') !!}",
      type: "get",
      async: false,
      data: {
        keynik
      },
      success: function(res) {
        let rowTable = ''

        res.forEach((item, i) => {
          rowTable += `
          <tr>
          <td class="text-center">
            <button class="btn-action-sm btn-action-success btn-sm hover-tooltip" onclick="buttonEditTarget('${item.KeyNik}', '${item.Tahun}', '${item.Merk || ''}')" type="button" data-tooltip="Edit Target Sales"><i class="bi bi-pen"></i></button>
            <button class="btn-action-sm btn-action-danger btn-sm hover-tooltip" onclick="buttonDeleteTarget('${item.KeyNik}', '${item.Tahun}', '${item.Merk || ''}')" type="button" data-tooltip="Delete Target Sales" ><i class="bi bi-trash"></i></button>
          </td>
          <td>${item.Tahun}</td>
          <td>${item.Merk}</td>
          <td>${item.namamerk}</td>
          <td>${formatNumberDisplay(MasterList.nolDepan(item.TgtSales))}</td>
          </tr>
          `
        });
        
        document.getElementById("tabel_data_targetSales").innerHTML = rowTable
        document.getElementById("input_target_kodesales").value = keynik

      }})
}

let keynikTemp = 0

function submitEdit () {
  
  let _token = $("#_token").val();

  let kodeGdg = $("#input_edit_kodeGudang").val();
  let kodeCost = $("#input_edit_kodeCost").val();

  $.ajax({
    url: "{!! url('masterSalesSpEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      keynikTemp,
      kodeGdg,
      kodeCost
    },
    success: function(res) {

    console.log(res)
    if (res != 1) {
      alertify.warning(res);
    }  else {
      alertify.success('Gudang/Cost telah diedit');
      loadAll()
      $("#formEdit").modal('toggle')
    }
    }})

}

// Gudang dipilih lewat dropdown seperti purchasing/purchaseOrder (muatDropdownAlamatKirim).
// Isinya dari masterSalesListGudang, sumber yang dulu dipakai modal browse gudang.
let listGudang = []

function muatDropdownGudang () {
  $.ajax({
    url: "{!! url('masterSalesListGudang') !!}",
    type: "get",
    async: false,
    data: {
      _token: $("#_token").val(),
    },
    success: function (res) {
      listGudang = res
    },
  });

  let selectEl = document.getElementById("input_edit_kodeGudang")
  selectEl.innerHTML = '<option value=""></option>'
  listGudang.forEach((item) => {
    let opt = document.createElement('option')
    opt.value = item.KODEGDG
    opt.textContent = `${item.KODEGDG} - ${item.NAMA}`
    selectEl.appendChild(opt)
  });
}

// Gudang tersimpan yang tidak ada di daftar tetap ditampilkan apa adanya.
function pilihGudang (kode) {
  let selectEl = document.getElementById("input_edit_kodeGudang")
  kode = kode == null ? '' : kode
  if (kode && !listGudang.some(item => item.KODEGDG === kode)) {
    let opt = document.createElement('option')
    opt.value = kode
    opt.textContent = kode
    selectEl.appendChild(opt)
  }
  selectEl.value = kode
}

function buttonCosting () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListCosting') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonSelectCosting('${item.KodeCost}')">
      <td>${item.KodeCost}</td>
      <td>${item.NamaCost}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Kode Costing</th>
    <th scope="col">Nama Costing</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Costing'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonCustSupp () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListCustSupp') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonSelectCustSupp('${item.KodeCust}')">
      <td>${item.KodeCust}</td>
      <td>${item.NamaCust}</td>
      <td>${item.Alamat}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Kode</th>
    <th scope="col">Nama Pelanggan</th>
    <th scope="col">Alamat</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Cust Supp'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonMerk () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSalesListMerk') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonSelectMerk('${item.KodeMerk}')">
      <td>${item.KodeMerk}</td>
      <td>${item.NamaMerk}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Kode Merk</th>
    <th scope="col">Nama Merk</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectCosting (kodecost){

  document.getElementById('input_edit_kodeCost').value = kodecost;

  $("#formModalOpen").modal("hide");
}

function buttonSelectCustSupp (kodesales){

  document.getElementById('input_custSupp_add_kodeCustSupp').value = kodesales;
  document.getElementById('input_custSupp_edit_kodeCustSupp').value = kodesales;

  $("#formModalOpen").modal("hide");
}

function buttonSelectMerk (merk){

  document.getElementById('input_target_add_merk').value = merk;
  // document.getElementById('input_target_edit_merk').value = merk;

  $("#formModalOpen").modal("hide");
}

// Separator ribuan pada input angka (.format-number) - pola accounting/pengajuandpp.blade.php:
// autoNumeric memformat tampilan, nilai yang diisi program lewat setNum(), dan pembacaan nilai
// membuang koma dengan .replace(/,/g, '') sebelum dikirim / dihitung.
function toNum (v) {
  let n = parseFloat(String(v == null ? '' : v).replace(/,/g, ''))
  return isNaN(n) ? 0 : n
}
function setNum (id, v) {
  let el = document.getElementById(id)
  if (!el) return
  // Nilai kosong tetap kosong (validasi "harus diisi" bergantung pada ini).
  if (v === '' || v == null) {
    el.value = ''
  } else if ($(el).data('autoNumeric')) {
    $(el).autoNumeric('set', toNum(v))
  } else {
    el.value = v
  }
}
$(function () {
  $('.format-number').autoNumeric('init', { mDec: '2', vMin: '-9999999999999.99' })
})

window.onload = function(){
  MasterList.kolom({ href: 'mastersales', kolom: MSL_KOLOM, onChange: renderTabel })
  muatDropdownGudang()
  loadAll();
};
</script>

<!-- start modal select add akumulasi penyusutan -->
<div class="modal fade picker-kas" id="formModalOpen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="namaModalOpen"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto;">
              <table id="tabelModalOpen">
                <thead id='theadOpen' class="text-center">
                  <tr></tr>
                </thead>
                <tbody id="tabel_dataModalOpen" class="text-left"></tbody>
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
<!-- End modal select add akumulasi penyusutan-->

@endsection
