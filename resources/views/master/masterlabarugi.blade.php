@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Laba Rugi')
@section('content')




  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Laba Rugi</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Laba Rugi</h1>
    </div>
    <button class="btn btn-action-primary" onclick="buttonAdd()">+ Add Laba Rugi</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  {{-- Filter Devisi & jenis Laporan ada di dalam tombol Filter toolbar, seperti modal filter
       purchasing/purchaseOrder. Id & nilai option sama seperti sebelumnya. --}}
  @php
    $filterLabaRugi = '<div class="rt-grid-2"><div><label class="rt-field-label" for="jenisDevisi">Devisi</label>'
      . '<select name="devisi" id="jenisDevisi" class="rt-native">';
    foreach ($listDataDevisi as $Devisi) {
      $filterLabaRugi .= '<option value="' . e($Devisi->Devisi) . '">' . e($Devisi->Devisi) . ' - ' . e($Devisi->NamaDevisi) . '</option>';
    }
    $filterLabaRugi .= '</select></div>'
      . '<div><label class="rt-field-label" for="jenisLaporan">Laporan</label>'
      . '<select name="perkiraanCustomer" id="jenisLaporan" class="rt-native">'
      . '<option value="0">Laba Rugi</option><option value="1">HPP</option>'
      . '</select></div></div>';
  @endphp

  <div class="card">
    <div class="card-body" style="padding:0;">

      @include('master.partials.toolbarMaster', ['filterIsi' => $filterLabaRugi, 'filterJudul' => 'Filter Laba Rugi'])

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


<style>
  /* Tiga pasang label + input per baris (Tipe | Jumlah | Group, Tanda | Persentasi | Tampil).
     Nomor & Perkiraan masing-masing satu baris selebar kolom input pertama (.bs-baris memaksa
     label mulai di baris baru), Keterangan (.bs-full) membentang sampai ujung kanan input Jumlah. */
  #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 110px minmax(0, 1fr) 90px minmax(0, 1fr) 90px minmax(0, 1fr); }
  #formBsGrid .bs-form.bs-form-3 .bs-baris { grid-column: 1; }
  /* Keterangan berhenti sejajar ujung kanan input Jumlah. */
  #formBsGrid .bs-form.bs-form-3 .bs-full { grid-column: 2 / 5; }
  @media (max-width: 575.98px) {
    #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 1fr; }
    #formBsGrid .bs-form.bs-form-3 .bs-full { grid-column: 1 / -1; }
  }
</style>

<!-- start modal add -->
<div class="modal fade" id="form"tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
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

            <div class="bs-form bs-form-3">
          <label for="input_add_nomor">Nomor</label>
          <input type="text" class="form-control" id="input_add_nomor" placeholder="Nomor">
          <label for="input_add_perkiraan" class="bs-baris">Perkiraan</label>
          <div class="input-group">
                  <input type="text" class="form-control" id="input_add_perkiraan" placeholder="Perkiraan" disabled>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
                  </div>
                </div>
          <label for="input_add_keterangan" class="bs-baris">Keterangan</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_keterangan" placeholder="Keterangan"></div>
          <label class="bs-baris">Tipe</label>
          <select class='form-control' id='input_add_tipe'>
                    <option value='' selected disabled></option>
                    <option value=1>1 - Mutasi Debet</option>
                    <option value=2>2 - Mutasi Kredit</option>
                    <option value=3>3 - Koreksi Debet</option>
                    <option value=4>4 - Koreksi Kredit</option>
                    <option value='A'>A - Saldo Awal</option>
                    <option value='K'>K - Saldo Akhir</option>
                    <option value='Z'>Z - Persediaan Akhir</option>
                    <option value='M'>M -Mutasi</option>
                    <option value='H'>H - HPP</option>
                  </select>
          <label>Jumlah</label>
          <select class='form-control' id='input_add_jumlah'>
                    <option value='' selected disabled></option>
                    <option value='T'>T - Total</option>
                    <option value='G'>G - Group</option>
                    <option value='S'>S - Sub Group</option>
                  </select>
          <label for="input_add_group">Group</label>
          <input type="text" class="form-control" id="input_add_group" placeholder="Group">
          <label class="bs-baris">Tanda</label>
          <select class='form-control' id='input_add_tanda'>
                    <option value='' selected disabled></option>
                    <option value='+'>+</option>
                    <option value='-'>-</option>
                  </select>
          <label for="input_add_persentasi">Persentasi</label>
          <input type="text" class="form-control" id="input_add_persentasi" placeholder="Persentasi">
          <label>Tampil</label>
          <select class='form-control' id='input_add_tampil'>
                    <option value='Y'>Y - Ya</option>
                    <option value='T'>T - Tidak</option>
                  </select>
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
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"  role="document" style="max-width: 900px">
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
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="bs-form bs-form-3">
          <label for="input_edit_nomor">Nomor</label>
          <input type="text" class="form-control" id="input_edit_nomor" placeholder="Nomor" disabled>
          <label for="input_edit_perkiraan" class="bs-baris">Perkiraan</label>
          <div class="input-group">
                  <input type="text" class="form-control" id="input_edit_perkiraan" placeholder="Perkiraan" disabled>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonPerkiraan()" title="Cari"><i class="bi bi-search"></i></button>
                  </div>
                </div>
          <label for="input_edit_keterangan" class="bs-baris">Keterangan</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_keterangan" placeholder="Keterangan"></div>
          <label class="bs-baris">Tipe</label>
          <select class='form-control' id='input_edit_tipe'>
                    <option value='' selected disabled></option>
                    <option value=1>1 - Mutasi Debet</option>
                    <option value=2>2 - Mutasi Kredit</option>
                    <option value=3>3 - Koreksi Debet</option>
                    <option value=4>4 - Koreksi Kredit</option>
                    <option value='A'>A - Saldo Awal</option>
                    <option value='K'>K - Saldo Akhir</option>
                    <option value='Z'>Z - Persediaan Akhir</option>
                    <option value='M'>M -Mutasi</option>
                    <option value='H'>H - HPP</option>
                  </select>
          <label>Jumlah</label>
          <select class='form-control' id='input_edit_jumlah'>
                    <option value='' selected disabled></option>
                    <option value='T'>T - Total</option>
                    <option value='G'>G - Group</option>
                    <option value='S'>S - Sub Group</option>
                  </select>
          <label for="input_edit_group">Group</label>
          <input type="text" class="form-control" id="input_edit_group" placeholder="Group">
          <label class="bs-baris">Tanda</label>
          <select class='form-control' id='input_edit_tanda'>
                    <option value='' selected disabled></option>
                    <option value='+'>+</option>
                    <option value='-'>-</option>
                  </select>
          <label for="input_edit_persentasi">Persentasi</label>
          <input type="text" class="form-control" id="input_edit_persentasi" placeholder="Persentasi">
          <label>Tampil</label>
          <select class='form-control' id='input_edit_tampil'>
                    <option value='Y'>Y - Ya</option>
                    <option value='T'>T - Tidak</option>
                  </select>
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

{{-- Modal pemilih mengikuti picker purchasing (picker-kas.css / pickerKasInit()). --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/master-list.js') !!}?v={{ @filemtime(base_path('public/js/master-list.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let dataRefresh = []

// Kolom tabel daftar (lebih dari 5 kolom -> bisa digeser & disembunyikan, lihat MasterList.kolom()).
// [field, label, tampil, tipe, total, desimal]
const MLR_KOLOM = [
  ['Nomor',      'Nomor',      1, 'varchar', 0, 0],
  ['Perkiraan',  'Perkiraan',  1, 'varchar', 0, 0],
  ['Keterangan', 'Keterangan', 1, 'varchar', 0, 0],
  ['Grup',       'Grup',       1, 'varchar', 0, 0],
  ['Tipe',       'Tipe',       1, 'varchar', 0, 0],
  ['Tanda',      'Tanda',      1, 'varchar', 0, 0],
  ['Persen',     'Persen',     1, 'varchar', 0, 0],
  ['Jumlah',     'Jumlah',     1, 'varchar', 0, 0],
]

// Data tabel utama disimpan terpisah dari dataRefresh (dipakai juga oleh pemilih perkiraan).
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
        <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.Nomor}')"><i class="bi bi-pen"></i></button>
        <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.Nomor}')"><i class="bi bi-trash"></i></button>
      </div>`
    rowTable += MasterList.baris(item, cols, aksi)
  });

  document.getElementById("tabel_data").innerHTML = rowTable
  $("#tabel").DataTable(MasterList.opsi())
  MasterList.selesai('#tabel')
}

function loadAll () {
  let _token = $("#_token").val();

  let filterDevisi = $("#jenisDevisi").val();
  let filterLaporan = $("#jenisLaporan").val();

  $.ajax({
    url: "{!! url('masterlabarugiloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      filterDevisi,
      filterLaporan
    },
    success: function(res) {
      dataTabel = res
  }})

  renderTabel()
}

function buttonAdd () {

  $("#form").modal('toggle')

  resetFormAdd()

}

// Dipakai saat membuka form Add dan setelah Simpan berhasil, supaya isian sebelumnya tidak tertinggal.
function resetFormAdd () {

  document.getElementById('input_add_nomor').value = ''
  document.getElementById('input_add_perkiraan').value = ''
  document.getElementById('input_add_keterangan').value = ''
  document.getElementById('input_add_tipe').value = ''
  document.getElementById('input_add_tanda').value = ''
  document.getElementById('input_add_jumlah').value = ''
  document.getElementById('input_add_persentasi').value = ''
  document.getElementById('input_add_tampil').value = 'Y'
  document.getElementById('input_add_group').value = ''

}

function buttonEdit (nomor) {
  console.log(nomor)
  let _token = $("#_token").val();
  let filterDevisi = $("#jenisDevisi").val();
  let filterLaporan = $("#jenisLaporan").val();

  $.ajax({
    url: "{!! url('masterLabaRugiLoadEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nomor,
      filterDevisi,
      filterLaporan
    },
    success: function(res) {
      console.log(res)
      document.getElementById("input_edit_nomor").value = res[0].Nomor
      document.getElementById("input_edit_perkiraan").value = res[0].Perkiraan
      document.getElementById("input_edit_keterangan").value = res[0].Keterangan
      document.getElementById("input_edit_tipe").value = res[0].Tipe
      document.getElementById("input_edit_tanda").value = res[0].Tanda
      document.getElementById("input_edit_jumlah").value = MasterList.nolDepan(res[0].Jumlah)
      document.getElementById("input_edit_persentasi").value = MasterList.nolDepan(res[0].Persen)
      document.getElementById("input_edit_tampil").value = res[0].Tampil
      document.getElementById("input_edit_group").value = res[0].Grup

    }})
    $("#formEdit").modal('toggle')
}

function buttonDelete (nomor) {
  console.log(nomor)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Devisi', 'Apakah yakin ingin menghapus data ' + nomor + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterLabaRugiSubmitDelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            nomor
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("Data Laba Rugi telah dihapus");

            }
          }})
      }
    ,function(){
      console.log('no')
    });


}

function submitEdit () {

  let _token = $("#_token").val();
  
  let nomor = $("#input_edit_nomor").val();
  let perkiraan = $("#input_edit_perkiraan").val();
  let keterangan = $("#input_edit_keterangan").val();

  let tipe = $("#input_edit_tipe").val();
  let tanda = $("#input_edit_tanda").val();
  let jumlah = $("#input_edit_jumlah").val();

  let persentasi = $("#input_edit_persentasi").val();
  let tampil = $("#input_edit_tampil").val();
  let group = $("#input_edit_group").val();

  let devisi = $("#jenisDevisi").val();

  if (!nomor) {
    alertify.warning("Nomor harus diisi");
    return
  }

  if (!perkiraan) {
    perkiraan = '-';
  }

  if (!keterangan) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!tipe) {
    tipe = '-';
  }

  if (!tanda) {
    alertify.warning("Tanda harus diisi");
    return
  }

  if (!jumlah) {
    jumlah = '-';
  }

  if (!persentasi) {
     persentasi = '-'
  }

  if (!group) {
    group = '-';
  }

  if (group.length > 3) {
    alertify.warning("Group Hanya boleh 3 huruf");
    return;
  }

  if (!tampil) {
    alertify.warning("Tampil harus diisi");
    return
  }
  
  $.ajax({
    url: "{!! url('masterLabaRugiSubmitEdit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nomor,
      perkiraan,
      keterangan,
      tipe,
      tanda,
      jumlah,
      persentasi,
      tampil,
      group,
      devisi
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Laba Rugi telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}

function submitAdd () {

  let _token = $("#_token").val();

  let nomor = $("#input_add_nomor").val();
  let perkiraan = $("#input_add_perkiraan").val();
  let keterangan = $("#input_add_keterangan").val();

  let tipe = $("#input_add_tipe").val();
  let tanda = $("#input_add_tanda").val();
  let jumlah = $("#input_add_jumlah").val();

  let persentasi = $("#input_add_persentasi").val();
  let tampil = $("#input_add_tampil").val();
  let group = $("#input_add_group").val();

  let devisi = $("#jenisDevisi").val();

  if (!nomor) {
    alertify.warning("Nomor harus diisi");
    return
  }

  if (!perkiraan) {
    perkiraan = '-';
  }

  if (!keterangan) {
    alertify.warning("Keterangan harus diisi");
    return
  }

  if (!tipe) {
    tipe = '-';
  }

  if (!tanda) {
    alertify.warning("Tanda harus diisi");
    return
  }

  if (!jumlah) {
    jumlah = '-';
  }

  if (!persentasi) {
     persentasi = '-'
  }

  if (!group) {
    group = '-';
  }

  if (group.length > 3) {
    alertify.warning("Group Hanya boleh 3 huruf");
    return;
  }

  if (!tampil) {
    alertify.warning("Tampil harus diisi");
    return
  }
  
  $.ajax({
    url: "{!! url('masterLabaRugiSubmitAdd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      nomor,
      perkiraan,
      keterangan,
      tipe,
      tanda,
      jumlah,
      persentasi,
      tampil,
      group,
      devisi
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        alertify.success("Data Laba Rugi telah ditambah");
        resetFormAdd()
        loadAll()
      }
    }})
}

window.onload = function(){
  MasterList.kolom({ href: 'masterlabarugi', kolom: MLR_KOLOM, onChange: renderTabel })
  loadAll();
};

function buttonPerkiraan () {
  console.log('asd');
  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterLabaRugiLoadPerkiraan') !!}",
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
    let tipePerkiraan = '';
        if (item.Tipe == 0) {
          tipePerkiraan = `<td>General</td>`;
        } else {
          tipePerkiraan = `<td>Detail</td>`;
        }

    rowTable += `<tr class="pick-row" onclick="buttonSelectPerkiraan('${item.Perkiraan}', '${item.Keterangan}')">
      <td>${item.Perkiraan}</td>
      <td>${item.Keterangan}</td>
      ${tipePerkiraan}
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Perkiraan</th>
    <th scope="col">Keterangan</th>
    <th scope="col">Tipe</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectPerkiraan (perkiraan, keterangan){

  document.getElementById('input_add_perkiraan').value = perkiraan;
  document.getElementById('input_add_keterangan').value = keterangan;
  document.getElementById('input_edit_perkiraan').value = perkiraan;
  document.getElementById('input_edit_keterangan').value = keterangan;
  // document.getElementById('input_target_edit_merk').value = merk;

  $("#formModalOpen").modal("hide");
}

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
