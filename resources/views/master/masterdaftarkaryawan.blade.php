@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Karyawan')
@section('content')




  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Karyawan</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Karyawan</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Karyawan</button>
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

<!-- start modal add -->
<div class="modal fade"  id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
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
          <label for="input_add_NamaLengkap">Nama Lengkap</label>
          <input type="text" class="form-control" id="input_add_NamaLengkap">

          <label for="input_add_Alamat">Alamat</label>
          <input type="text" class="form-control" id="input_add_Alamat">
        </div>

            <div class="bs-form">
          <label for="input_add_Nik">NIK</label>
          <input type="text" class="form-control" id="input_add_Nik">
          <label for="input_add_HP">Telepon HP</label>
          <input type="text" class="form-control" id="input_add_HP">


            
          <label for="input_add_KodePos">Kode Pos</label>
          <input type="text" class="form-control" id="input_add_KodePos">
          <label for="input_add_Kelamin">Kelamin</label>
          <div class='form-group'>
                  <select name="formkelamin" class='form-control' id="input_add_Kelamin">
                      <option value="L">Laki-Laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                </div>


            
          <label for="input_add_KTP">Nomor KTP</label>
          <input type="text" class="form-control" id="input_add_KTP">
          <label for="input_add_Email">E-mail</label>
          <input type="text" class="form-control" id="input_add_Email">


            
          <label for="input_add_TempatLahir">Tempat Lahir</label>
          <input type="text" class="form-control" id="input_add_TempatLahir">
          <label for="input_add_TanggalLahir">Tanggal Lahir</label>
          <input type="date" class="form-control" id="input_add_TanggalLahir">


            
          <label for="input_add_Agama">Agama</label>
          <input type="text" class="form-control" id="input_add_Agama">
          <label for="input_add_PendidikanAkhir">Pendidikan Akhir</label>
          <input type="text" class="form-control" id="input_add_PendidikanAkhir">


            
          <label for="input_add_Tinggi">Tinggi</label>
          <input type="number" class="form-control" id="input_add_Tinggi">
          <label for="input_add_Berat">Berat</label>
          <input type="number" class="form-control" id="input_add_Berat">
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add_TglMasuk">Tgl Masuk</label>
          <input type="date" class="form-control" id="input_add_TglMasuk">
        </div>

            <div class="bs-form">
          <label for="input_add_NPWP">NPWP</label>
          <input type="text" class="form-control" id="input_add_NPWP">
          <label for="input_add_Status">Status</label>
          <select name="formstatus" class='form-control' id="input_add_Status">
                    <option value=1>Sales</option>
                    <option value=0>Non-Sales</option>
                  </select>


            
          <label for="input_add_Aktif">Aktif</label>
          <select name="formaktif" class='form-control' id="input_add_Aktif">
                    <option value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
          <label for="input_add_Produksi">Produksi</label>
          <select name="formproduksi" class='form-control' id="input_add_Produksi">
                    <option value="0">Produksi</option>
                    <option value="1">Non Produksi</option>
                    <option value="2">Finishing</option>
                  </select>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_add_Penagih">Penagih</label>
          <input type="text" class="form-control" id="input_add_Penagih">
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
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_edit_noUrut" value="" />

            <div class="bs-form bs-form-1">
          <label for="input_edit_NamaLengkap">Nama Lengkap</label>
          <input type="text" class="form-control" id="input_edit_NamaLengkap">

          <label for="input_edit_Alamat">Alamat</label>
          <input type="text" class="form-control" id="input_edit_Alamat">
        </div>

            <div class="bs-form">
          <label for="input_edit_Nik">NIK</label>
          <input type="text" class="form-control" id="input_edit_Nik" disabled>
          <label for="input_edit_HP">Telepon HP</label>
          <input type="text" class="form-control" id="input_edit_HP">


            
          <label for="input_edit_KodePos">Kode Pos</label>
          <input type="text" class="form-control" id="input_edit_KodePos">
          <label for="input_edit_Kelamin">Kelamin</label>
          <div class='form-group'>
                  <select name="formkelamin" class='form-control' id="input_edit_Kelamin">
                      <option value="L">Laki-Laki</option>
                      <option value="P">Perempuan</option>
                    </select>
                </div>


            
          <label for="input_edit_KTP">Nomor KTP</label>
          <input type="text" class="form-control" id="input_edit_KTP">
          <label for="input_edit_Email">E-mail</label>
          <input type="text" class="form-control" id="input_edit_Email">


            
          <label for="input_edit_TempatLahir">Tempat Lahir</label>
          <input type="text" class="form-control" id="input_edit_TempatLahir">
          <label for="input_edit_TanggalLahir">Tanggal Lahir</label>
          <input type="date" class="form-control" id="input_edit_TanggalLahir">


            
          <label for="input_edit_Agama">Agama</label>
          <input type="text" class="form-control" id="input_edit_Agama">
          <label for="input_edit_PendidikanAkhir">Pendidikan Akhir</label>
          <input type="text" class="form-control" id="input_edit_PendidikanAkhir">


            
          <label for="input_edit_Tinggi">Tinggi</label>
          <input type="number" class="form-control" id="input_edit_Tinggi">
          <label for="input_edit_Berat">Berat</label>
          <input type="number" class="form-control" id="input_edit_Berat">
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_edit_TglMasuk">Tgl Masuk</label>
          <input type="date" class="form-control" id="input_edit_TglMasuk">
        </div>

            <div class="bs-form">
          <label for="input_edit_NPWP">NPWP</label>
          <input type="text" class="form-control" id="input_edit_NPWP">
          <label for="input_edit_Status">Status</label>
          <select name="formstatus" class='form-control' id="input_edit_Status">
                    <option value=1>Sales</option>
                    <option value=0>Non-Sales</option>
                  </select>


            
          <label for="input_edit_Aktif">Aktif</label>
          <select name="formaktif" class='form-control' id="input_edit_Aktif">
                    <option value=1>Aktif</option>
                    <option value=0>Tidak Aktif</option>
                  </select>
          <label for="input_edit_Produksi">Produksi</label>
          <select name="formproduksi" class='form-control' id="input_edit_Produksi">
                    <option value="0">Produksi</option>
                    <option value="1">Non Produksi</option>
                    <option value="2">Finishing</option>
                  </select>
        </div>

            <div class="bs-form bs-form-1">
          <label for="input_edit_Penagih">Penagih</label>
          <input type="text" class="form-control" id="input_edit_Penagih">
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
const MDK_KOLOM = [
  ['NIK',       'ID',         1, 'varchar', 0, 0],
  ['Nama',      'Nama',       1, 'varchar', 0, 0],
  ['Kelamin',   'Kelamin',    1, 'varchar', 0, 0],
  ['TmpLahir',  'Tmp Lahir',  1, 'varchar', 0, 0],
  ['TglLahir',  'Tgl Lahir',  1, 'varchar', 0, 0],
  ['Agama',     'Agama',      1, 'varchar', 0, 0],
  ['Tinggi',    'Tinggi',     1, 'varchar', 0, 0],
  ['Berat',     'Berat',      1, 'varchar', 0, 0],
  ['NomorKTP',  'Nomor KTP',  1, 'varchar', 0, 0],
  ['TglMasuk',  'Tgl Masuk',  1, 'varchar', 0, 0],
  ['TeleponHP', 'Telepon HP', 1, 'varchar', 0, 0],
]

// Tanggal ditampilkan DD-MM-YYYY seperti sebelumnya; kosong kalau datanya kosong (dulu tampil NaN-NaN-NaN).
function mdkTanggal (v) {
  if (!v) { return '' }
  let d = new Date(v)
  if (isNaN(d.getTime())) { return '' }
  return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear()
}

// Data tabel utama disimpan terpisah dari dataRefresh - pemilih (valas, gudang, dsb) memakai
// dataRefresh untuk daftarnya sendiri, dan renderTabel() dipanggil ulang saat kolom digeser.
let dataTabel = []

function renderTabel () {
  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy();
  }

  let cols = MasterList.kolomTampil()
  document.getElementById('tabel_header').innerHTML = MasterList.headHtml(cols)

  let khusus = {
    TglLahir: function (item) { return '<td>' + mdkTanggal(item.TglLahir) + '</td>' },
    TglMasuk: function (item) { return '<td>' + mdkTanggal(item.TglMasuk) + '</td>' }
  }

  let rowTable = ""
  dataTabel.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
          <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.NIK}')"><i class="bi bi-pen"></i></button>
          <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.NIK}')"><i class="bi bi-trash"></i></button>
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
    url: "{!! url('masterdaftarkaryawanloadall') !!}",
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

  document.getElementById("input_add_noUrut").value = ''
  $('#form .modal-body input[type="text"], #form .modal-body input[type="number"], #form .modal-body input[type="date"]').val('')
  $('#form .modal-body select').prop('selectedIndex', 0)

  $("#form").modal('toggle')

}

function buttonEdit (Nik) {
  console.log(Nik);
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterdaftarkaryawanspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      Nik: Nik
    },
    success: function (res) {
      console.log(res);
      document.getElementById("input_edit_Nik").value = res[0].NIK;
      document.getElementById("input_edit_NamaLengkap").value = res[0].Nama;
      document.getElementById("input_edit_Alamat").value = res[0].AlamatKTP;
      document.getElementById("input_edit_HP").value = res[0].TeleponHP;
      document.getElementById("input_edit_KodePos").value = res[0].KodePosRmh;
      document.getElementById("input_edit_Kelamin").value = res[0].Kelamin;
      document.getElementById("input_edit_KTP").value = res[0].NomorKTP;
      document.getElementById("input_edit_TempatLahir").value = res[0].TmpLahir;
      document.getElementById("input_edit_Email").value = res[0].Email;

      // Format TanggalLahir
      // Tanggal kosong/tidak valid dulu membuat toISOString() error dan modal edit tidak terbuka.
      const tglLahir = new Date(res[0].TglLahir + 'Z');
      const formattedTglLahir = isNaN(tglLahir.getTime()) ? '' : tglLahir.toISOString().split('T')[0];
      document.getElementById("input_edit_TanggalLahir").value = formattedTglLahir;

      document.getElementById("input_edit_Agama").value = res[0].Agama;
      document.getElementById("input_edit_PendidikanAkhir").value = res[0].KetPendAkhir;
      document.getElementById("input_edit_Tinggi").value = MasterList.nolDepan(res[0].Tinggi);
      document.getElementById("input_edit_Berat").value = MasterList.nolDepan(res[0].Berat);

      // Format TglMasuk
      const tglMasuk = new Date(res[0].TglMasuk + 'Z');
      const formattedTglMasuk = isNaN(tglMasuk.getTime()) ? '' : tglMasuk.toISOString().split('T')[0];
      document.getElementById("input_edit_TglMasuk").value = formattedTglMasuk;

      document.getElementById("input_edit_NPWP").value = res[0].NPWP;
      document.getElementById("input_edit_Status").value = res[0].IsSales;
      document.getElementById("input_edit_Aktif").value = res[0].Aktif;
      document.getElementById("input_edit_Produksi").value = res[0].Produksi;
      document.getElementById("input_edit_Penagih").value = res[0].Penagih;
    }
  });
  $("#formEdit").modal('toggle');
}


function buttonDelete (Nik) {
  console.log(Nik)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Karyawan', 'Apakah yakin ingin menghapus Data ' + Nik + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterdaftarkaryawanspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            Nik
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data Daftar Karyawan telah dihapus");

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
  let Nik = $("#input_edit_Nik").val();
  let NamaLengkap = $("#input_edit_NamaLengkap").val();
  let Alamat = $("#input_edit_Alamat").val();
  let HP = $("#input_edit_HP").val();
  let KodePos = $("#input_edit_KodePos").val();
  let Kelamin = $("#input_edit_Kelamin").val();
  let KTP = $("#input_edit_KTP").val();
  let TempatLahir = $("#input_edit_TempatLahir").val();
  let Email = $("#input_edit_Email").val();
  let TanggalLahir = $("#input_edit_TanggalLahir").val();
  let Agama = $("#input_edit_Agama").val();
  let PendidikanAkhir = $("#input_edit_PendidikanAkhir").val();
  let Tinggi = $("#input_edit_Tinggi").val();
  let Berat = $("#input_edit_Berat").val();
  let TglMasuk = $("#input_edit_TglMasuk").val();
  let NPWP = $("#input_edit_NPWP").val();
  let Status = $("#input_edit_Status").val();
  let Aktif = $("#input_edit_Aktif").val();
  let Produksi = $("#input_edit_Produksi").val();
  let Penagih = $("#input_edit_Penagih").val();

  if (!Nik) {
  alertify.warning("Nik harus diisi");
  return;
  }

  if (!NamaLengkap) {
    alertify.warning("Nama Lengkap harus diisi");
    return;
  }

  if (!Alamat) {
    alertify.warning("Alamat harus diisi");
    return;
  }

  if (!HP) {
    alertify.warning("HP harus diisi");
    return;
  }

  if (!KodePos) {
    alertify.warning("Kode Pos harus diisi");
    return;
  }

  if (!Kelamin) {
    alertify.warning("Kelamin harus diisi");
    return;
  }

  if (!TempatLahir) {
    alertify.warning("Tempat Lahir harus diisi");
    return;
  }

  if (!Email) {
    alertify.warning("Email harus diisi");
    return;
  }

  if (!TanggalLahir) {
    alertify.warning("Tanggal Lahir harus diisi");
    return;
  }

  if (!Agama) {
    alertify.warning("Agama harus diisi");
    return;
  }

  if (!PendidikanAkhir) {
    alertify.warning("Pendidikan Akhir harus diisi");
    return;
  }

  if (!Tinggi) {
    alertify.warning("Tinggi harus diisi");
    return;
  }

  if (!Berat) {
    alertify.warning("Berat harus diisi");
    return;
  }

  if (!TglMasuk) {
    alertify.warning("Tanggal Masuk harus diisi");
    return;
  }

  if (!NPWP) {
    alertify.warning("NPWP harus diisi");
    return;
  }

  if (!Status) {
    alertify.warning("Status harus diisi");
    return;
  }

  if (!Aktif) {
    alertify.warning("Aktif harus diisi");
    return;
  }

  if (!Produksi) {
    alertify.warning("Produksi harus diisi");
    return;
  }

  if (!Penagih) {
    alertify.warning("Penagih harus diisi");
    return;
  }

  $.ajax({
    url: "{!! url('masterdaftarkaryawanspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      Nik,
      NamaLengkap,
      Alamat,
      HP,
      KodePos,
      Kelamin,
      KTP,
      TempatLahir,
      Email,
      TanggalLahir,
      Agama,
      PendidikanAkhir,
      Tinggi,
      Berat,
      TglMasuk,
      NPWP,
      Status,
      Aktif,
      Produksi,
      Penagih
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

function submitAdd () {

  let _token = $("#_token").val();

  let Nik = $("#input_add_Nik").val();
    if (Nik.length > 20) {
    alertify.warning("NIK maximal 20 angka.");
    return;
  }

  let NamaLengkap = $("#input_add_NamaLengkap").val();
  let Alamat = $("#input_add_Alamat").val();
  let HP = $("#input_add_HP").val();
  let KodePos = $("#input_add_KodePos").val();
  let Kelamin = $("#input_add_Kelamin").val();
  let KTP = $("#input_add_KTP").val();
  let TempatLahir = $("#input_add_TempatLahir").val();
  let Email = $("#input_add_Email").val();
  let TanggalLahir = $("#input_add_TanggalLahir").val();
  let Agama = $("#input_add_Agama").val();
  let PendidikanAkhir = $("#input_add_PendidikanAkhir").val();
  let Tinggi = $("#input_add_Tinggi").val();
  let Berat = $("#input_add_Berat").val();
  let TglMasuk = $("#input_add_TglMasuk").val();
  let NPWP = $("#input_add_NPWP").val();
  let Status = $("#input_add_Status").val();
  let Aktif = $("#input_add_Aktif").val();
  let Produksi = $("#input_add_Produksi").val();
  let Penagih = $("#input_add_Penagih").val();

  console.log(Status)

    if (!Nik) {
      Nik = "";
    }

    if (!NamaLengkap) {
      NamaLengkap = "";
    }

    if (!Alamat) {
      Alamat = "";
    }

    if (!HP) {
      HP = "";
    }

    if (!KodePos) {
      KodePos = "";
    }

    if (!Kelamin) {
      Kelamin = "";
    }

    if (!KTP) {
      KTP = "";
    }

    if (!TempatLahir) {
      TempatLahir = "";
    }

    if (!Email) {
      Email = "";
    }

    if (!TanggalLahir) {
      TanggalLahir = "";
    }

    if (!Agama) {
      Agama = "";
    }

    if (!PendidikanAkhir) {
      PendidikanAkhir = "";
    }

    if (!Tinggi) {
      Tinggi = "";
    }

    if (!Berat) {
      Berat = "";
    }

    if (!TglMasuk) {
      TglMasuk = "";
    }

    if (!NPWP) {
      NPWP = "";
    }

    if (!Status) {
      Status = "";
    }

    if (!Aktif) {
      Aktif = "";
    }

    if (!Produksi) {
      Produksi = "";
    }

    if (!Penagih) {
      Penagih = "";
    }

    let keyNIKTemp = 0

  $.ajax({
    url: "{!! url('masterKaryawanGetKeyNIK') !!}",
    type: "get",
    async: false,
    success: function(res){
      keyNIKTemp = parseInt(res[0].KeyNIK) + 1
    }})

  $.ajax({
    url: "{!! url('masterdaftarkaryawanspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      keyNIK : keyNIKTemp,
      Nik,
      NamaLengkap,
      Alamat,
      HP,
      KodePos,
      Kelamin,
      KTP,
      TempatLahir,
      Email,
      TanggalLahir,
      Agama,
      PendidikanAkhir,
      Tinggi,
      Berat,
      TglMasuk,
      NPWP,
      Status,
      Aktif,
      Produksi,
      Penagih
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Jenis telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

window.onload = function(){
  MasterList.kolom({ href: 'masterdaftarkaryawan', kolom: MDK_KOLOM, onChange: renderTabel })
  loadAll();
};

</script>




@endsection
