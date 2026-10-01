@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Set Lokasi Barang')
@section('content')

{{-- Dimigrasikan dari layout newmaster ke newmasterTest, mengikuti master/masterhutang.blade.php
     (kolom data lebih dari 5 -> bisa digeser & disembunyikan lewat MasterList.kolom()). --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  <div class="card">
    <div class="card-body" style="padding:0;">

      {{-- Di halaman lama tombol Add disembunyikan (AddVisibility hidden), jadi toolbar tanpa Tambah. --}}
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

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

          <div class="bs-form bs-form-1">
            <label for="input_add_kode">Kode Lokasi</label>
            <input type="text" class="form-control" id="input_add_kode" placeholder="Kode Lokasi">

            <label for="input_add_nama">Keterangan</label>
            <input type="text" class="form-control" id="input_add_nama" placeholder="Keterangan">
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
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
            <label for="input_edit_lokasiBarang">Lokasi Barang</label>
            <div class="input-group">
              <input type="text" class="form-control" id="input_edit_lokasiBarang" placeholder="Lokasi Barang" disabled>
              <div class="input-group-append">
                <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonLokasiBarang()" title="Cari"><i class="bi bi-search"></i></button>
              </div>
            </div>
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

{{-- Modal pemilih/riwayat (#formModalOpen) bersama menu master, gaya picker purchasing. --}}
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
const MSL_KOLOM = [
  ['KODEBRG',   'Kode Barang', 1, 'varchar', 0, 0],
  ['NAMABRG',   'Nama Barang', 1, 'varchar', 0, 0],
  ['namaMerk',  'Merk',        1, 'varchar', 0, 0],
  ['Mlokasi',   'Kode Lokasi', 1, 'varchar', 0, 0],
  ['KetLokasi', 'Nama Lokasi', 1, 'varchar', 0, 0],
  ['SAT1',      'Sat 1',       1, 'varchar', 0, 0],
  ['SAT2',      'Sat 2',       1, 'varchar', 0, 0],
  ['ISI2',      'Isi 2',       1, 'varchar', 0, 0],
  ['SAT3',      'Sat 3',       1, 'varchar', 0, 0],
  ['ISI3',      'Isi 3',       1, 'varchar', 0, 0],
]

// Data tabel utama disimpan terpisah dari dataRefresh - pemilih lokasi & riwayat memakai
// dataRefresh, dan renderTabel() dipanggil ulang saat kolom digeser.
let dataTabel = []

function renderTabel () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  // Rata kanan seperti tabel lama (Sat 1, Isi 2, Isi 3).
  let kanan = function (field) { return function (item) { return '<td class="text-right">' + MasterList.nolDepan(item[field]) + '</td>' } }
  let khusus = { SAT1: kanan('SAT1'), ISI2: kanan('ISI2'), ISI3: kanan('ISI3') }

  let rowTable = ""
  dataTabel.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
          <button data-toggle="tooltip" data-placement="top" title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.KODEBRG}')"><i class="bi bi-pen"></i></button>
          <button data-toggle="tooltip" data-placement="top" title="History Lokasi Barang" class="btn-action-sm btn-action-primary" type="button" onclick="buttonHistory('${item.KODEBRG}')"><i class="bi bi-clock-history"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi, khusus)
  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')
}

function loadAll () {
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('mastersetlokasibarangloadall') !!}",
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

  $("#form").modal('toggle')

}

let kodeBarangTemp = ''

function buttonEdit (kode) {

  kodeBarangTemp = kode
  console.log(kodeBarangTemp)

  $("#formEdit").modal('toggle')
}

function buttonDelete (kode) {
  console.log(kode)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Lokasi Barang', 'Apakah yakin ingin menghapus Kode Lokasi ' + kode + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterlokasibarangspdelete') !!}",
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
              alertify.success("Kode Lokasi Barang telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  let lokasiBarang = $("#input_edit_lokasiBarang").val();

  if (!lokasiBarang) {
    alertify.warning("Lokasi Barang harus diisi");
    return
  }

  $.ajax({
    url: "{!! url('masterSetLokasiBarangSubmitEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      lokasiBarang,
      kodeBarang : kodeBarangTemp
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Lokasi berhasil disimpan.");
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
    url: "{!! url('masterlokasibarangspadd') !!}",
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
        alertify.success("Data Lokasi Barang telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function(){
  MasterList.kolom({ href: 'mastersetlokasibarang', kolom: MSL_KOLOM, onChange: renderTabel })
  loadAll();
}

function buttonHistory (kodeBarang) {
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSetLokasiBarangLoadHistory') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeBarang
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr>
      <td>${item.lokasi}</td>
      <td>${item.tgl}</td>
      <td>${item.iduser}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Lokasi</th>
    <th scope="col">Tanggal</th>
    <th scope="col">User</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'History Barang'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonLokasiBarang () {
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterSetLokasiBarangLoadLokasiBarang') !!}",
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

    rowTable += `<tr class="pick-row" onclick="buttonSelectLokasi('${item.KODELOKASI}')">
      <td>${item.KODELOKASI}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Lokasi</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Lokasi Barang'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectLokasi(kodeLokasi){

  document.getElementById('input_edit_lokasiBarang').value = kodeLokasi
  
  $("#formModalOpen").modal('toggle')

}

</script>

@endsection
