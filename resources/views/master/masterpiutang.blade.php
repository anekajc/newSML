@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Master Piutang')
@section('content')



  {{-- <div class="sp-breadcrumb">
    <span>Beranda</span>
    <span class="sp-sep">›</span>
    <span>Master</span>
    <span class="sp-sep">›</span>
    <span class="sp-crumb-active">Piutang</span>
  </div> --}}

  {{-- <div class="sp-page-head">
    <div>
      <h1>Master Piutang</h1>
    </div>
    <button class="btn btn-primary" onclick="buttonAdd()">+ Add Hutang</button>
  </div> --}}

<div id="contentContainer" class="container-fluid po-list-page">

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />


  {{-- Filter perkiraan (mis. Hutang Usaha / Piutang Usaha) ada di dalam tombol Filter toolbar,
       seperti modal filter purchasing/purchaseOrder. Id & nilai option sama seperti sebelumnya. --}}
  @php
    $filterPerkiraan = '<div><label class="rt-field-label" for="perkiraanCustomer">Perkiraan</label>'
      . '<select id="perkiraanCustomer" class="rt-native">';
    foreach ($listDataCustomer as $customer) {
      $nilai = e($customer->keterangan . ' (' . $customer->Perkiraan . ')');
      $filterPerkiraan .= '<option value="' . $nilai . '">' . $nilai . '</option>';
    }
    $filterPerkiraan .= '</select></div>';
  @endphp

  <div class="card">
    <div class="card-body" style="padding:0;">

      {{-- Tambah Piutang dilakukan per baris (tombol di kolom Actions), jadi toolbar tanpa tombol Tambah. --}}
      @include('master.partials.toolbarMaster', ['tanpaTambah' => true, 'filterIsi' => $filterPerkiraan, 'filterJudul' => 'Filter Piutang'])

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

<!-- Modal Add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Piutang Awal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <input type="hidden" name="noUrut" id="input_add_noUrut">

        <div class="container-fluid">
          <!-- Supplier -->
          <div class="bs-form">
          <label for="kodeSupplier">Supplier</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control" id="kodeSupplier" placeholder="Supplier" disabled>
            <input type="text" class="form-control" id="namaSupplier" placeholder="Nama Supplier" disabled></div></div>
          <label for="namaPerkiraan">Perkiraan</label>
          <div class="bs-full"><input type="text" class="form-control" id="namaPerkiraan" placeholder="Perkiraan" disabled></div>
          <label for="input_add_noFaktur">No. Faktur</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_noFaktur" placeholder="No. Faktur"></div>
          <label for="input_add_tanggalFaktur">Tanggal Faktur</label>
          <input type="date" class="form-control" id="input_add_tanggalFaktur">
          <label for="input_add_jatuhTempo">Jatuh Tempo</label>
          <input type="date" class="form-control" id="input_add_jatuhTempo">


          
          <label for="input_add_valas">Valas</label>
          <select class="form-control" id="input_add_valas" onchange="onChangeValas('add')"></select>
          <label for="input_add_kurs">Kurs</label>
          <input type="text" class="form-control text-right format-number" id="input_add_kurs" value='1.00' disabled>
          <label for="input_add_jumlah">Jumlah</label>
          <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_jumlah" value="0">
          <label for="input_add_jumlahRp">Jumlah (Rp)</label>
          <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_jumlahRp" value="0" disabled>
          <div hidden>
          <label for="input_add_kredit">Kredit</label>
          <input type="number" class="form-control text-right" id="input_add_kredit" value=0>
          <label for="input_add_kreditRp">Kredit (Rp)</label>
          <input type="text" class="form-control" id="input_add_kreditRp" value=0 disabled>
          </div>
          <label for="input_add_noPO">No. PO</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_add_noPO" placeholder="No. PO"></div>


          
          
          <label for="input_add_lokasiPenerima">Lok. Penerima</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><div class="input-group">
                  <input type="text" class="form-control" id="input_add_lokasiPenerima" disabled>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonLokasiPenerima()" title="Cari"><i class="bi bi-search"></i></button>
                  </div>
              </div>
            <input type="text" class="form-control" id="input_add_namaLokasi" disabled></div></div>
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
<!-- End Modal Add -->

<!-- Modal Edit -->
<div class="modal fade" id="formEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 700px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Piutang Awal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <input type="hidden" name="noUrut" id="input_add_noUrut">

        <div class="container-fluid">
          <!-- Supplier -->
          <div class="bs-form">
          <label for="kodeSupplierEdit">Supplier</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><input type="text" class="form-control" id="kodeSupplierEdit" placeholder="Supplier" disabled>
            <input type="text" class="form-control" id="namaSupplierEdit" placeholder="Supplier" disabled></div></div>
          <label for="namaPerkiraanEdit">Perkiraan</label>
          <div class="bs-full"><input type="text" class="form-control" id="namaPerkiraanEdit" placeholder="Perkiraan" disabled></div>
          <label for="input_edit_noFaktur">No. Faktur</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_noFaktur" placeholder="No. Faktur" disabled></div>
          <label for="input_edit_tanggalFaktur">Tanggal Faktur</label>
          <input type="date" class="form-control" id="input_edit_tanggalFaktur">
          <label for="input_edit_jatuhTempo">Jatuh Tempo</label>
          <input type="date" class="form-control" id="input_edit_jatuhTempo">


          
          <label for="input_edit_valas">Valas</label>
          <select class="form-control" id="input_edit_valas" onchange="onChangeValas('edit')"></select>
          <label for="input_edit_kurs">Kurs</label>
          <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_edit_kurs" placeholder="Kurs" disabled>
          <label for="input_edit_jumlah">Jumlah</label>
          <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_edit_jumlah" value="0">
          <label for="input_edit_jumlahRp">Jumlah (Rp)</label>
          <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_edit_jumlahRp" value="0" disabled>
          <div hidden>
          <label for="input_edit_kredit">Kredit</label>
          <input type="number" class="form-control text-right" id="input_edit_kredit">
          <label for="input_edit_kreditRp">Kredit (Rp)</label>
          <input type="number" class="form-control text-right" id="input_edit_kreditRp" disabled>
          </div>
          <label for="input_edit_noPO">No. PO</label>
          <div class="bs-full"><input type="text" class="form-control" id="input_edit_noPO" placeholder="No. PO"></div>


          
          
          <label for="input_edit_lokasiPenerima">Lok. Penerima</label>
          <div class="bs-full"><div class="d-flex align-items-center" style="gap:8px"><div class="input-group">
                  <input type="text" class="form-control" id="input_edit_lokasiPenerima" disabled>
                  <div class="input-group-append">
                      <button type="button" class="btn btn-chip-biru btn-select" onclick="buttonLokasiPenerima()" title="Cari"><i class="bi bi-search"></i></button>
                  </div>
              </div>
            <input type="text" class="form-control" id="input_edit_namaLokasi" disabled></div></div>
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
<!-- End Modal Edit -->





@include('master.partials.modalOpenMaster')

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
const MPT_KOLOM = [
  ['KodeCustSupp', 'Kode Customer',  1, 'varchar', 0, 0],
  ['NAMACUST',     'Nama Customer',  1, 'varchar', 0, 0],
  ['NoFaktur',     'No Faktur',     1, 'varchar', 0, 0],
  ['Tanggal',      'Tanggal',       1, 'varchar', 0, 0],
  ['JatuhTempo',   'Jatuh Tempo',   1, 'varchar', 0, 0],
  ['Valas',        'Valas',         1, 'varchar', 0, 0],
  ['Kurs',         'Kurs',          1, 'varchar', 0, 0],
  ['Debet',        'Debet(Rp)',     1, 'varchar', 0, 0],
  ['DebetD',       'Debet Valas',   1, 'varchar', 0, 0],
  ['Kredit',       'Kredit(Rp)',    1, 'varchar', 0, 0],
  ['KreditD',      'Kredit Valas',  1, 'varchar', 0, 0],
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

  // Tampilan sel sama seperti sebelumnya: tanggal yyyy-mm-dd, angka lewat formatNumberDisplay().
  let formatTanggal = function (date) {
    return date == null ? '' : (new Date(date)).toLocaleDateString('en-CA');
  }
  let angka = function (field) { return function (item) { return '<td class="text-right">' + formatNumberDisplay(MasterList.nolDepan(item[field])) + '</td>' } }
  let khusus = {
    NoFaktur: function (item) { return '<td>' + (item.NoFaktur == null ? '' : item.NoFaktur) + '</td>' },
    Tanggal: function (item) { return '<td>' + formatTanggal(item.Tanggal) + '</td>' },
    JatuhTempo: function (item) { return '<td>' + formatTanggal(item.JatuhTempo) + '</td>' },
    Valas: function (item) { return '<td>' + (item.Valas == null ? '' : item.Valas) + '</td>' },
    Kurs: angka('Kurs'), Debet: angka('Debet'), DebetD: angka('DebetD'), Kredit: angka('Kredit'), KreditD: angka('KreditD')
  }

  let rowTable = ""
  dataTabel.forEach((item, i) => {
    let aksi = `
      <div class="action-buttons-wrap">
      <button title="Add Piutang" class="btn-action-sm btn-action-primary" type="button" onclick="buttonAdd('${item.KodeCustSupp}', '${item.Perkiraan}', '${item.NAMACUST}')"><i class="bi bi-file-earmark-plus"></i></button>
      ${item.NoFaktur != null ? `
        <button title="Edit" class="btn-action-sm btn-action-success" type="button" onclick="buttonEdit('${item.NoFaktur}', '${item.KodeCustSupp}', '${item.Perkiraan}')"><i class="bi bi-pen"></i></button>
        <button title="Delete" class="btn-action-sm btn-action-danger" type="button" onclick="buttonDelete('${item.NoFaktur}', '${item.KodeCustSupp}')"><i class="bi bi-trash"></i></button>
      ` : ''}
      </div>`
    rowTable += MasterList.baris(item, cols, aksi, khusus)
  });

  document.getElementById("tabel_data").innerHTML = rowTable
  // Urutan data dari server dipertahankan (dulu juga ordering: false).
  $("#tabel").DataTable(MasterList.opsi({ ordering: false }))
  MasterList.selesai('#tabel')
}

function loadAll () {
  let _token = $("#_token").val();
  let filter = $("#perkiraanCustomer").val();

  $.ajax({
    url: "{!! url('masterpiutangloadall') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      filter
    },
    success: function(res) {
      dataTabel = res
  }})

  renderTabel()
}

function buttonAdd (kodeSupplier, Perkiraan, namaSupplier) {

  kodeCustSuppTemp = kodeSupplier

  document.getElementById("kodeSupplier").value = kodeSupplier
  document.getElementById("namaSupplier").value = namaSupplier
  document.getElementById("namaPerkiraan").value = Perkiraan
  document.getElementById("input_add_noFaktur").value = ''
  document.getElementById("input_add_tanggalFaktur").value = ''
  document.getElementById("input_add_jatuhTempo").value = ''
  setNum("input_add_jumlah", '')
  setNum("input_add_jumlahRp", '')
  document.getElementById("input_add_noPO").value = ''
  document.getElementById("input_add_lokasiPenerima").value = ''
  document.getElementById("input_add_namaLokasi").value = ''
  $("#form").modal('toggle')

}

function buttonEdit (kode, perkiraanCust) {
  kodeCustSuppTemp = perkiraanCust
  noFakturTemp = kode
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('masterpiutangspdetail') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kode,
      kodeCustSupp : perkiraanCust
    },
    success: function(res) {

      document.getElementById("kodeSupplierEdit").value = res[0].KodeCustSupp
      document.getElementById("namaSupplierEdit").value = res[0].NAMACUST
      document.getElementById("namaPerkiraanEdit").value = res[0].Perkiraan
      document.getElementById("input_edit_noFaktur").value = res[0].NoFaktur
      document.getElementById("input_edit_tanggalFaktur").value = new Date(res[0].Tanggal).toLocaleDateString('en-CA');
      document.getElementById("input_edit_jatuhTempo").value = new Date(res[0].JatuhTempo).toLocaleDateString('en-CA');
      pilihValas("input_edit_valas", res[0].Valas)
      setNum("input_edit_kurs", formatAngka(parseFloat(res[0].Kurs).toFixed(2)))
      setNum("input_edit_jumlah", res[0].DebetD)
      setNum("input_edit_jumlahRp", res[0].Debet)
      document.getElementById("input_edit_kredit").value = MasterList.nolDepan(res[0].Kredit)
      document.getElementById("input_edit_kreditRp").value = MasterList.nolDepan(res[0].KreditD)
      document.getElementById("input_edit_noPO").value = res[0].POcust

    }})

    $.ajax({
    url: "{!! url('masterpiutangspdetaillokasipenerima') !!}",
    type: "get",
    async: false,
    data: {
      _token : _token,
      kodeCustSupp : perkiraanCust,
      noFaktur : noFakturTemp
    },
    success: function(res) {
      document.getElementById("input_edit_lokasiPenerima").value = res[0].KodeKebun
      document.getElementById("input_edit_namaLokasi").value = res[0].NamaKebun

    }})

    $("#formEdit").modal('toggle')
}

function buttonDelete (noFaktur, kodeCustSupp) {
  console.log(noFaktur)
  let _token = $("#_token").val();


  alertify.confirm('Hapus Nomor Faktur', 'Apakah yakin ingin menghapus No. Faktur ' + noFaktur + ' ?',
      function() {
        console.log('yes')

        $.ajax({
          url: "{!! url('masterpiutangspdelete') !!}",
          type: "post",
          async: false,
          data: {
            _token : _token,
            noFaktur,
            kodeCustSupp
          },
          success: function(res) {
            if (res != 1) {
              alertify.warning(res);
            } else {
              console.log(res)
              loadAll()
              alertify.success("No. Faktur telah dihapus");

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
  let noFaktur = $("#input_edit_noFaktur").val();
  let tanggalFaktur = $("#input_edit_tanggalFaktur").val();
  let jatuhTempo = $("#input_edit_jatuhTempo").val();
  let valas = $("#input_edit_valas").val();
  let kurs = $("#input_edit_kurs").val().replace(/,/g, '');
  let jumlah = $("#input_edit_jumlah").val().replace(/,/g, '');
  let jumlahRp = $("#input_edit_jumlahRp").val().replace(/,/g, '');
  let noPo = $("#input_edit_noPO").val();
  let lokasiPenerima = $("#input_edit_lokasiPenerima").val();
  let namaLokasi = $("#input_edit_namaLokasi").val();

  $.ajax({
    url: "{!! url('masterpiutangspedit') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noFaktur,
      kodeCustSupp: $("#kodeSupplierEdit").val(),
      tanggalFaktur,
      jatuhTempo,
      valas,
      kurs,
      jumlah,
      jumlahRp,
      noPo,
      lokasiPenerima,
      namaLokasi
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Departemen telah diedit");
        loadAll()
        $("#formEdit").modal('toggle')
      }

    }})

}
//
function submitAdd () {

  let _token = $("#_token").val();
  let noFaktur = $("#input_add_noFaktur").val();
  let kodeSupplier = $("#kodeSupplier").val();
  let perkiraanSupplier = $("#namaPerkiraan").val();
  let tanggalFaktur = $("#input_add_tanggalFaktur").val();
  let jatuhTempo = $("#input_add_jatuhTempo").val();
  let valas = $("#input_add_valas").val();
  let kurs = $("#input_add_kurs").val().replace(/,/g, '');
  let jumlah = $("#input_add_jumlah").val().replace(/,/g, '');
  let jumlahRp = $("#input_add_jumlahRp").val().replace(/,/g, '');
  let kredit = $("#input_add_kredit").val();
  let kreditRp = $("#input_add_kreditRp").val();
  let noPo = $("#input_add_noPO").val();
  let lokasiPenerima = $("#input_add_lokasiPenerima").val();
  let namaLokasi = $("#input_add_namaLokasi").val();

  if (!noFaktur) {
    alertify.warning("No. Faktur harus diisi");
    return
  }

  if (!tanggalFaktur) {
    alertify.warning("Tanggal Faktur harus diisi");
    return
  }

  if (!jatuhTempo) {
    alertify.warning("Jatuh Tempo harus diisi");
    return
  }

  if (!valas) {
    alertify.warning("Valas harus diisi");
    return
  }

  if (!kurs) {
    alertify.warning("Kurs harus diisi");
    return
  }

  if (!jumlah) {
    alertify.warning("Jumlah harus diisi");
    return
  }

  if (!noPo) {
    alertify.warning("No. PO harus diisi");
    return
  }

  if (!lokasiPenerima) {
    lokasiPenerima = '-';
    return
  }

    if (!namaLokasi) {
    namaLokasi = '-';
    return
  }


  $.ajax({
    url: "{!! url('masterpiutangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      noFaktur,
      kodeSupplier,
      perkiraanSupplier,
      tanggalFaktur,
      jatuhTempo,
      valas,
      kurs,
      jumlah,
      jumlahRp,
      kredit,
      kreditRp,
      noPo,
      lokasiPenerima,
      namaLokasi
    },
    success: function(res) {

      if (res != 1) {
        alertify.warning(res);
      }  else {
        console.log(res ,'!')
        // $("#formEdit").modal('toggle')
        alertify.success("Data Piutang telah ditambah");
        loadAll()
        $("#form").modal('toggle')
      }

    }})

  // console.log(kodearea, namaarea)
}

// Valas dipilih lewat dropdown seperti purchasing/purchaseOrder (muatDropdownValas /
// onChangeValas). Isinya dari masterpiutangloadvalas, sumber yang dulu dipakai modal browse.
let listValas = []

function muatDropdownValas () {
  $.ajax({
    url: "{!! url('masterpiutangloadvalas') !!}",
    type: "get",
    async: false,
    data: {
      _token: $("#_token").val(),
    },
    success: function (res) {
      listValas = res
    },
  });

  ['input_add_valas', 'input_edit_valas'].forEach((id) => {
    let selectEl = document.getElementById(id)
    selectEl.innerHTML = ''
    listValas.forEach((item) => {
      let opt = document.createElement('option')
      opt.value = item.KODEVLS
      opt.textContent = `${item.KODEVLS} - ${item.NAMAVLS}`
      selectEl.appendChild(opt)
    });
  });

  $("#input_add_valas").val('IDR')
}

// Valas tersimpan yang tidak ada di daftar tetap ditampilkan apa adanya.
function pilihValas (id, kode) {
  let selectEl = document.getElementById(id)
  if (kode && !listValas.some(item => item.KODEVLS === kode)) {
    let opt = document.createElement('option')
    opt.value = kode
    opt.textContent = kode
    selectEl.appendChild(opt)
  }
  selectEl.value = kode
}

function onChangeValas (mode) {
  let kode = $("#input_" + mode + "_valas").val()
  let itemX = listValas.find(item => item.KODEVLS === kode)
  setNum("input_" + mode + "_kurs", itemX ? itemX.KURS : '')
}

const inputKredit = document.getElementById('input_add_jumlah');
const inputKreditRp = document.getElementById('input_add_jumlahRp');
const inputKurs = document.getElementById('input_add_kurs');

// Add event listener to the Kredit input field
inputKredit.addEventListener('input', function() {
    // Get the values from the input fields
    const kredit = toNum(inputKredit.value);
    const kurs = toNum(inputKurs.value);

    // Calculate the Kredit(Rp)
    const kreditRp = kredit * kurs;

    // Update the value of the Kredit(Rp) input field
    setNum(inputKreditRp.id, kreditRp); // Assuming you want to display two decimal places
});

const inputKreditEdit = document.getElementById('input_edit_jumlah');
const inputKreditRpEdit = document.getElementById('input_edit_jumlahRp');
const inputKursEdit = document.getElementById('input_edit_kurs');

// Add event listener to the Kredit input field
inputKreditEdit.addEventListener('input', function() {
    // Get the values from the input fields
    const kreditEdit = toNum(inputKreditEdit.value);
    const kursEdit = toNum(inputKursEdit.value);

    // Calculate the Kredit(Rp)
    const kreditRpEdit = kreditEdit * kursEdit;

    // Update the value of the Kredit(Rp) input field
    setNum(inputKreditRpEdit.id, kreditRpEdit); // Assuming you want to display two decimal places
});

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
    MasterList.kolom({ href: 'masterpiutang', kolom: MPT_KOLOM, onChange: renderTabel })
    muatDropdownValas();
    loadAll();
};

function buttonLokasiPenerima () {

  let _token = $("#_token").val();

   if ($.fn.DataTable.isDataTable('#tabelModalOpen')) {
    $('#tabelModalOpen').DataTable().destroy();
  }

  $.ajax({
    url: "{!! url('masterpiutangloadlokasipenerima') !!}",
    type: "get",
    async: false,
    data: {
      _token: _token,
      kodeCustSupp : kodeCustSuppTemp
    },
    success: function (res) {
      console.log(res);
      dataRefresh = res;
    },
  });

  let rowTable = "";
  dataRefresh.forEach((item, i) => {
    let temp = "";

    rowTable += `<tr class="pick-row" onclick="buttonSelectLokasiPenerima('${item.KodeKebun}', '${item.namaKebun}')">
      <td>${item.KodeKebun}</td>
      <td>${item.namaKebun}</td>
    </tr>`;
  });

  document.getElementById("tabel_dataModalOpen").innerHTML = rowTable;

  let headerTable = `
  <tr>
    <th scope="col">Kode Kebun</th>
    <th scope="col">Nama Kebun</th>
  </tr>
  `
  document.querySelector("#theadOpen").innerHTML = headerTable;
  document.getElementById("namaModalOpen").innerHTML = 'Merk'

  pickerKasInit('tabelModalOpen')
  
  $("#formModalOpen").modal('toggle')
}

function buttonSelectLokasiPenerima (kodeKebun, namaKebun){
  document.getElementById('input_add_lokasiPenerima').value = kodeKebun;
  document.getElementById('input_add_namaLokasi').value = namaKebun;

  document.getElementById('input_edit_lokasiPenerima').value = kodeKebun;
  document.getElementById('input_edit_namaLokasi').value = namaKebun;

  $("#formModalOpen").modal("hide");
}

function formatAngka (angkaString) {
  console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
};

</script>



@endsection
