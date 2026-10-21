@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Ubah PO Customer')
@section('title', 'SML - Ubah PO Customer')

@endsection

{{-- Ubah PO Customer: fungsi diport dari programajcSML, tampilan mengikuti Terima PO
     Customer / menu marketing newSML lain: tab SO - Draft PO, Revisi PO Cust, History.
     Modal Ubah PO sekarang bisa mengganti No PO (DBSO.NoPesanan + IDPOCust) dengan PO
     Customer dari DBPOCUSTSUPP, mengikuti cara aplikasi desktop (PO lama pGanti = 1,
     riwayat ke DBHISPOCUST per barang, harga tidak diubah). Tombol "+" dan form Add/Edit
     Harga di modal lama (fungsinya tidak pernah ada) tetap tidak dipasang. --}}
@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
.custom-tabs {
  display: inline-flex;
  justify-content: flex-start;
  align-items: center;
  gap: 2px;
  background-color: #f1f3f5;
  border-radius: 20px;
  padding: 3px;
}
.custom-tabs .nav-link {
  display: inline-block !important;
  padding: 5px 16px !important;
  font-size: 0.75rem !important;
  border: none;
  border-radius: 17px;
  color: #495057;
  background: transparent;
  font-weight: 600;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}
.custom-tabs .nav-link:hover { background: transparent; color: #007bff; }
.custom-tabs .nav-link.active {
  background: #007bff; border-color: #007bff; color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}
.tab-card {
  display: block !important;
  align-items: flex-start !important;
  padding: 0 !important;
  border: none !important;
  margin-bottom: 6px !important;
}
.tab-card .card-body { padding: 5px 10px !important; }
#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}
#page1 .card:hover { transform: none !important; box-shadow: none !important; border-color: var(--border) !important; }
.po-len-wrap {
  display: flex; align-items: center; gap: 8px;
  background: var(--rt-card); border: 1.5px solid var(--rt-border);
  border-radius: 8px; padding: 5px 12px;
}
.po-len-wrap label {
  margin: 0; font-size: 11.5px; font-weight: 700; color: var(--rt-ink-soft);
  text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
}
.po-len-inp {
  border: none; background: transparent; font-size: 13px; font-weight: 700;
  color: var(--rt-ink); outline: none; cursor: pointer; padding: 2px 20px 2px 0;
  appearance: none; -webkit-appearance: none; -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right center;
}

#tabel thead th, #tabel2 thead th, #tabel3 thead th, #tabel_ubahPO thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd), #tabel3 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover, #tabel3 tbody tr:hover { background-color: #f5f3ff; }

.data-table th,
.data-table td {
  white-space: nowrap;
}

/* Tabel barang di modal Ubah PO */
.upc-detail-wrap { max-height: 45vh; overflow: auto; border: 1px solid #e7e9ee; border-radius: 8px; }
#tabel_ubahPO { width: 100%; margin: 0; font-size: 13px; }
#tabel_ubahPO th, #tabel_ubahPO td { padding: 6px 10px; border-bottom: 1px solid #eef0f4; white-space: nowrap; }
#tabel_ubahPO thead th { position: sticky; top: 0; z-index: 1; }
#tabel_ubahPO tbody tr.upc-baris-pilih { background: #e8edff; }

/* Modal tidak bertumpuk (pola menu DPP): modal induk disembunyikan selama modal anak terbuka. */
.modal.upc-modal-tertimbun { display: none !important; }
.modal-backdrop.upc-backdrop-tertimbun { display: none !important; }
</style>
@include('marketing.partials.tampilan-purchasing-css')
@endsection

@section('content')
<div id="page1" class="mainpage">

<div id="contentContainer" class="">
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">SO - Draft PO</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Revisi PO Cust</a>
        <a class="nav-item nav-link" id="nav-profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="nav-profile2" aria-selected="false">History</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" onchange="onChangePeriodeUPC(1)" class="po-filter-inp" id="input_tanggalawal1" value="{{ $tglawal }}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeUPC(1)" class="po-filter-inp" id="input_tanggalakhir1" value="{{ $tglakhir }}">
                  </div>
                  <input type="search" id="upcSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="upcLen1">Tampilkan</label>
                    <select id="upcLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                  </div>
                </div>
                <div id="rtBarTabel"></div>
                <table id="tabel" class="data-table po-aksi-hover">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel_data" class="text-left"></tbody>
                </table>
                <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" onchange="onChangePeriodeUPC(2)" class="po-filter-inp" id="input_tanggalawal2" value="{{ $tglawal }}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeUPC(2)" class="po-filter-inp" id="input_tanggalakhir2" value="{{ $tglakhir }}">
                  </div>
                  <input type="search" id="upcSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="upcLen2">Tampilkan</label>
                    <select id="upcLen2" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                  </div>
                </div>
                <div id="rtBarTabel2"></div>
                <table id="tabel2" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel2_data" class="text-left"></tbody>
                </table>
                <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
              </div>
            </div>
          </div>
        </div>

        {{-- History: seluruh DBHISPOCUST tanpa filter periode, sama seperti programajcSML. --}}
        <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile2-tab">
          <div class="row">
            <div class="col-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="upcSearch3" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="upcLen3">Tampilkan</label>
                    <select id="upcLen3" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                  </div>
                </div>
                <div id="rtBarTabel3"></div>
                <table id="tabel3" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel3_data" class="text-left"></tbody>
                </table>
                <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>
</div>

<!-- start modal Ubah PO -->
<div class="modal fade" id="formUbahPO" tabindex="-1" role="dialog" aria-labelledby="formUbahPOTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formUbahPOTitle">Ubah PO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>No. Bukti SO</label>
                <input type="text" class="form-control" id="input_harga_noBuktiUbahPO" disabled>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Customer</label>
                <input type="text" class="form-control" id="input_harga_customerUbahPO" disabled>
              </div>
            </div>
          </div>
          <div class="row">
            {{-- No. PO: berisi No PO SO saat ini; ganti lewat tombol cari (modal pemilih PO Customer). --}}
            <div class="col-md-4">
              <div class="form-group">
                <label>No. PO</label>
                <input type="hidden" id="input_ubah_idpocust" value="">
                <div class="input-group">
                  <input type="text" class="form-control" id="input_harga_nopoUbahPO" disabled>
                  <button type="button" class="btn btn-chip-biru btn-sm" title="Cari" onclick="buttonBrowsePO()"><i class="bi bi-search"></i></button>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Barang</label>
                <input type="text" class="form-control" id="input_harga_barangUbahPO" disabled>
              </div>
            </div>
          </div>

          <div class="upc-detail-wrap mt-2">
            <table id="tabel_ubahPO">
              <thead>
                <tr>
                  <th>Kode Brg</th>
                  <th>Nama Brg</th>
                  <th class="text-right">Qty</th>
                  <th>Sat</th>
                  <th class="text-right">Harga</th>
                </tr>
              </thead>
              <tbody id="tabel_data_ubahPO" class="text-left"></tbody>
            </table>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" onclick="submitUbahPO()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal Ubah PO -->

<!-- start modal pemilih PO Customer (klik baris untuk memilih) -->
<div class="modal fade" id="formListPO" tabindex="-1" role="dialog" aria-labelledby="formListPOTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formListPOTitle">PO Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabel_list_pocust" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Tgl Create</th>
              <th>Tgl Terima</th>
              <th>No PO Customer</th>
              <th class="text-right">Total PO</th>
            </tr>
          </thead>
          <tbody id="tabel_data_list_pocust" class="text-left"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal pemilih PO Customer -->

@endsection

@section('js')
@include('marketing.partials.tampilan-purchasing-js')
<script>window.RT_LABEL_PAJAK = true</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Pola sama dengan terimapocustomer/closingso, untuk tiga tabel:
 * tabel (urut 1) = SO - Draft PO (ada kolom Actions), tabel2 (urut 2) = Revisi PO Cust,
 * tabel3 (urut 3) = History. Semua kolom (kolom programajcSML + tambahan dari query
 * yang sama) tampil saat pertama dibuka dan setelah "Reset kolom".
 */
let upcCart = { 1 : [], 2 : [], 3 : [] }
let upcActiveUrut = 0
const UPC_HREF = 'ubahpocust'
const UPC_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const UPC_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
const UPC_ID_TABEL = { 1 : 'tabel', 2 : 'tabel2', 3 : 'tabel3' }
const UPC_ID_BAR = { 1 : 'rtBarTabel', 2 : 'rtBarTabel2', 3 : 'rtBarTabel3' }
const UPC_ID_TAB = { 1 : 'nav-home-tab', 2 : 'nav-profile-tab', 3 : 'nav-profile2-tab' }
let upcPerluGambar = { 1 : false, 2 : false, 3 : false }
let upcRows = { 1 : [], 2 : [], 3 : [] }
let upcPanjangHalaman = { 1 : 10, 2 : 10, 3 : 10 }

function upcPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function upcEsc (v) {
  return String(v === undefined || v === null ? '' : v)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;')
}

function upcKolomSO (denganIdPO) {
  let kolom = [
    ['NoBukti',      'No Bukti',       1, 'varchar', 0, 0],
    ['tanggal',      'Tanggal',        1, 'date',    0, 0],
    ['KodeCUst',     'Kode Customer',  1, 'varchar', 0, 0],
    ['namaCustSUpp', 'Nama Customer',  1, 'varchar', 0, 0],
    ['NoPesanan',    'NoPO Customer',  1, 'varchar', 0, 0],
    ['KodeBrg',      'Kode Barang',    1, 'varchar', 0, 0],
    ['NamaBrg',      'Nama Barang',    1, 'varchar', 0, 0],
    ['Urut',         'Urut',           1, 'float',   0, 0],
    ['PartNumber',   'Part Number',    1, 'varchar', 0, 0],
    ['NAMAMERK',     'Merk',           1, 'varchar', 0, 0],
    ['Qnt',          'Qty',            1, 'float',   0, 2],
    ['Satuan',       'Sat',            1, 'varchar', 0, 0],
    ['Harga',        'Harga',          1, 'float',   0, 2],
    ['NDPP',         'DPP',            1, 'float',   0, 2],
    ['Total',        'Total',          1, 'float',   0, 2],
  ]
  if (denganIdPO) { kolom.push(['IDPOCust', 'ID PO Cust', 1, 'varchar', 0, 0]) }
  return kolom
}

function upcDefaultCart (urut) {
  if (urut === 1) { return upcKolomSO(true) }
  if (urut === 2) { return upcKolomSO(false) }
  return [
    ['NOSO',     'No. SO',      1, 'varchar', 0, 0],
    ['Kodebrg',  'Kode Barang', 1, 'varchar', 0, 0],
    ['Ket',      'Keterangan',  1, 'varchar', 0, 0],
    ['Tanggal',  'Tanggal',     1, 'date',    0, 0],
    ['IdUser',   'Id User',     1, 'varchar', 0, 0],
    ['UrutSO',   'Urut SO',     1, 'float',   0, 0],
  ]
}

function upcBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, UPC_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function upcAktifkanTabel (urut) {
  upcActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = upcCart[urut]
}

function upcOnChangeAktif () {
  reinitTabelUPC(upcActiveUrut || 1)
}

window.g_href = UPC_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = upcCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(UPC_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: UPC_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: UPC_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        upcCart[urut] = upcBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        upcCart[urut] = upcDefaultCart(urut)
        window.gcart_header = upcCart[urut]
        window.doSimpanHeader(UPC_HREF, urut)
      }
      window.gcart_header = upcCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      upcCart[urut] = upcDefaultCart(urut)
      window.gcart_header = upcCart[urut]
    }
  })
}

function activeVisibleTabKeyUPC () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2; }
  if ($('#nav-profile2-tab').hasClass('active')) { return 3; }
  return 1;
}

const UPC_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const UPC_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let upcRtSudahInit = false
function upcInitReportTableSekali () {
  if (upcRtSudahInit || typeof ReportTable === 'undefined') { return }
  upcRtSudahInit = true
  let urutAktif = activeVisibleTabKeyUPC()
  Object.keys(UPC_ID_TABEL).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: '#' + UPC_ID_TABEL[u], bar: '#' + UPC_ID_BAR[u], onChange: upcOnChangeAktif })
  });
  ReportTable.init({ table: UPC_SELEKTOR_TABEL_AKTIF, bar: UPC_SELEKTOR_BAR_AKTIF, onChange: upcOnChangeAktif })

  let upcGuardUlangKlik = false;
  ['#tabel', '#tabel2', '#tabel3'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (upcGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      upcGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      upcGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderUPC (tableSel, cols, withActions) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
  if (withActions) { headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>'); }
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

// Tanggal dari SQL Server dibaca sebagai teks supaya tidak bergeser karena zona waktu browser.
function upcTanggalISO (raw) {
  if (!raw) { return '' }
  let s = String(raw)
  if (/^\d{4}-\d{2}-\d{2}/.test(s)) { return s.substring(0, 10) }
  return formatDate(raw).split('/').join('-')
}

function upcAngka (raw, dp) {
  let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0
  return n.toLocaleString('en-US', { minimumFractionDigits: dp, maximumFractionDigits: dp })
}

function upcValueCell (row, col) {
  let raw = upcPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') {
    let iso = upcTanggalISO(raw)
    return '<td>' + (iso ? iso.split('-').reverse().join('/') : '') + '</td>';
  }
  if (type === 'float') {
    return '<td class="text-right">' + upcAngka(raw, Number(col[5]) || 0) + '</td>';
  }
  return '<td>' + upcEsc(raw) + '</td>';
}

// Tombol Ubah PO per baris (programajcSML). Baris dirujuk lewat indeks data -- di programajcSML
// nama customer/barang yang mengandung tanda petik (') membuat tombol tidak bisa diklik.
function tabelActionsCell (row, i) {
  return '<td class="text-center"><button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Ubah PO" onclick="buttonKoreksi(' + i + ')"><i class="bi bi-pen"></i></button></td>';
}

function renderTabelUPC (urut) {
  let cols = (upcCart[urut].length ? upcCart[urut] : upcDefaultCart(urut)).filter(function (c) { return c[2] === 1; });
  let denganAksi = urut === 1
  let html = "";
  (upcRows[urut] || []).forEach(function (row, i) {
    html += '<tr>' + (denganAksi ? tabelActionsCell(row, i) : '');
    cols.forEach(function (col) { html += upcValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById(UPC_ID_TABEL[urut] + '_data').innerHTML = html;
  tulisTheadHeaderUPC('#' + UPC_ID_TABEL[urut], cols, denganAksi);
}

function upcIkatSearch (urut) {
  let input = document.getElementById('upcSearch' + urut)
  let idTabel = UPC_ID_TABEL[urut]
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().search(nilai).draw() }
    }, 400)
  })
}

function upcIkatPanjangHalaman (urut) {
  let sel = document.getElementById('upcLen' + urut)
  let idTabel = UPC_ID_TABEL[urut]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(upcPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    upcPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(upcPanjangHalaman[urut]).draw() }
  })
}

const UPC_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabelUPC (urut) {
  let sel = '#' + UPC_ID_TABEL[urut]
  try {
    if ($.fn.DataTable.isDataTable(sel)) { $(sel).DataTable().destroy(); }
    renderTabelUPC(urut);
    $(sel).DataTable({ dom: UPC_DOM_STRING, lengthChange: false, pageLength: upcPanjangHalaman[urut], paging: true, ordering: false });
    let cari = document.getElementById('upcSearch' + urut)
    if (cari && cari.value) { $(sel).DataTable().search(cari.value).draw() }
    upcIkatSearch(urut); upcIkatPanjangHalaman(urut); upcPerluGambar[urut] = false;
  } catch (e) { console.error('reinitTabelUPC failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

$(document).ready(function(){
  upcAktifkanTabel(1); window.doSetHeader(1, false);
  upcRows[1] = @json($tempOutstanding);
  reinitTabelUPC(1);

  upcAktifkanTabel(2); window.doSetHeader(2, false);
  upcRows[2] = @json($tempOutstanding2);
  reinitTabelUPC(2);

  upcAktifkanTabel(3); window.doSetHeader(3, false);
  upcRows[3] = @json($tempOutstanding3);
  reinitTabelUPC(3);

  upcAktifkanTabel(1);
  upcInitReportTableSekali();

  [1, 2, 3].forEach(function (urut) {
    $('#' + UPC_ID_TAB[urut]).on('shown.bs.tab', function () {
      upcAktifkanTabel(urut)
      if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
      if (upcPerluGambar[urut]) { reinitTabelUPC(urut); }
    });
  });
});

/* ============ Modal Ubah PO ============ */
let upcSOAktif = null
let upcListPO = []

// ---------- Tumpukan modal (pola menu DPP) ----------
// Hanya satu modal yang terlihat: saat modal pemilih PO dibuka dari modal Ubah PO, modal
// Ubah PO disembunyikan lewat class (bukan .modal('hide')) supaya isiannya tetap; saat
// pemilih ditutup (pilih baris, Batal, x, Esc, backdrop) modal Ubah PO muncul lagi.
let upcTumpukanModal = []

function upcSisakanSatuBackdrop () {
  let backdrop = $('.modal-backdrop')
  backdrop.addClass('upc-backdrop-tertimbun')
  backdrop.last().removeClass('upc-backdrop-tertimbun')
}

$(document).on('show.bs.modal', '.modal', function (e) {
  if (e.target !== this) { return }
  let induk = $('.modal.show').not(this).not('.upc-modal-tertimbun').last()
  if (induk.length) {
    upcTumpukanModal.push(induk)
    induk.addClass('upc-modal-tertimbun')
  }
})

$(document).on('shown.bs.modal', '.modal', function (e) {
  if (e.target !== this) { return }
  upcSisakanSatuBackdrop()
})

$(document).on('hidden.bs.modal', '.modal', function (e) {
  if (e.target !== this) { return }
  let induk = upcTumpukanModal.pop()
  if (induk) { induk.removeClass('upc-modal-tertimbun') }
  // BS4 melepas .modal-open dari <body> begitu satu modal tertutup, padahal masih ada
  // modal lain yang terbuka - pasang lagi supaya scroll body tetap terkunci.
  if ($('.modal.show').length) { $('body').addClass('modal-open') }
  upcSisakanSatuBackdrop()
})

function buttonKoreksi (i) {
  if (!Number($("#akses_iskoreksi").val())) {
    alertify.warning('No access')
    return
  }
  let row = upcRows[1][i]
  if (!row) { return }
  upcSOAktif = row

  $("#input_ubah_idpocust").val('')
  document.getElementById('input_harga_noBuktiUbahPO').value = row.NoBukti || ''
  document.getElementById('input_harga_customerUbahPO').value = row.namaCustSUpp || ''
  document.getElementById('input_harga_barangUbahPO').value = row.NamaBrg || ''
  // No. PO berisi No PO SO saat ini; diganti lewat tombol cari.
  document.getElementById('input_harga_nopoUbahPO').value = row.NoPesanan || ''

  $.ajax({
    url: "{!! url('ubahPoCustomerListData') !!}",
    type: "get",
    async: false,
    data: {
      nobukti : row.NoBukti
    },
    success: function(res) {
      let rowTable = ''
      ;(res || []).forEach(function (item) {
        let pilih = String(item.Urut) === String(row.Urut) ? ' class="upc-baris-pilih"' : ''
        rowTable += '<tr' + pilih + '>'
          + '<td>' + upcEsc(item.KodeBrg) + '</td>'
          + '<td>' + upcEsc(item.NamaBrg) + '</td>'
          + '<td class="text-right">' + upcAngka(item.Qnt, 2) + '</td>'
          + '<td>' + upcEsc(item.Satuan) + '</td>'
          + '<td class="text-right">' + upcAngka(item.Harga, 2) + '</td>'
          + '</tr>'
      });

      if (!res || !res.length) {
        rowTable = '<tr><td colspan="5" class="text-center">Belum ada data harga</td></tr>'
      }

      document.getElementById("tabel_data_ubahPO").innerHTML = rowTable;
      $("#formUbahPO").modal('show');
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

// Tombol cari di samping No. PO (di programajcSML tombol "+" ini memanggil buttonAddHarga
// yang tidak pernah ada, sehingga error). Membuka modal pemilih PO Customer pengganti:
// customer SO yang sama, belum dipakai SO lain, belum diganti, belum di-close.
function buttonBrowsePO () {
  if (!upcSOAktif) { return }
  $.ajax({
    url: "{!! url('ubahpocustlistpo') !!}",
    type: "get",
    async: false,
    data: { nobukti: upcSOAktif.NoBukti },
    success: function (res) {
      upcListPO = res || []
      if (!upcListPO.length) {
        alertify.warning('Belum ada PO Customer untuk customer ini. Input dulu di menu Terima PO Customer.')
        return
      }
      if ($.fn.DataTable.isDataTable('#tabel_list_pocust')) { $('#tabel_list_pocust').DataTable().destroy() }
      let html = ''
      upcListPO.forEach(function (po, i) {
        html += '<tr class="pick-row" onclick="pilihPO(' + i + ')">'
          + '<td>' + upcTanggalISO(po.TglInput).split('-').reverse().join('/') + '</td>'
          + '<td>' + upcTanggalISO(po.TglTerima).split('-').reverse().join('/') + '</td>'
          + '<td>' + upcEsc(po.POCustomer) + (Number(po.PoSaatIni) === 1 ? ' <span class="badge badge-info">No PO saat ini</span>' : '') + '</td>'
          + '<td class="text-right">' + upcAngka(po.RpPO, 2) + '</td>'
          + '</tr>'
      });
      document.getElementById('tabel_data_list_pocust').innerHTML = html
      $("#formListPO").modal('show')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat daftar PO Customer')
    }
  })
}

function pilihPO (i) {
  let po = upcListPO[i]
  if (!po) { return }
  $("#input_ubah_idpocust").val(po.ID)
  $("#input_harga_nopoUbahPO").val(po.POCustomer)
  $("#formListPO").modal('hide')
}

function submitUbahPO () {
  if (!Number($("#akses_iskoreksi").val())) {
    alertify.warning('No access')
    return
  }
  if (!upcSOAktif) { return }
  let idpocust = $("#input_ubah_idpocust").val()
  let nopobaru = $("#input_harga_nopoUbahPO").val()
  // No PO sama dengan No PO SO saat ini = pengesahan (nomor tidak berubah), seperti desktop.
  let sahkan = String(nopobaru).trim() === String(upcSOAktif.NoPesanan || '').trim()
  if (!idpocust) {
    alertify.warning('Pilih No PO baru lewat tombol cari di samping No. PO')
    return
  }

  let dlg = alertify.confirm(
    sahkan ? 'Sahkan No PO' : 'Ubah No PO',
    sahkan
      ? 'Sahkan No PO "' + upcEsc(nopobaru) + '" untuk SO ' + upcEsc(upcSOAktif.NoBukti) + '? Nomor tidak berubah, SO pindah ke tab Revisi PO Cust.'
      : 'Ubah No PO SO ' + upcEsc(upcSOAktif.NoBukti) + ' dari "' + upcEsc(upcSOAktif.NoPesanan || '') + '" menjadi "' + upcEsc(nopobaru) + '"?',
    function () {
      $.ajax({
        url: "{!! url('ubahpocustsimpan') !!}",
        type: "post",
        async: false,
        data: {
          _token: $("#_token").val(),
          nobukti: upcSOAktif.NoBukti,
          idpocust,
        },
        success: function (res) {
          if (res != 1) {
            alertify.warning(res)
            return
          }
          $("#formUbahPO").modal('hide')
          alertify.success(sahkan ? 'No PO berhasil disahkan' : 'No PO berhasil diubah')
          loadAll()
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })
    },
    function () { }
  )
  if (dlg && dlg.elements && dlg.elements.root) { dlg.elements.root.classList.add('ajs-app-buttons') }
}

/* ============ Periode & muat ulang ============ */
function onChangePeriodeUPC (urut) {
  let awal = $("#input_tanggalawal" + urut).val()
  let akhir = $("#input_tanggalakhir" + urut).val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  // Tab SO - Draft PO dan Revisi PO Cust memakai satu periode yang sama.
  [1, 2].forEach(function (u) {
    $("#input_tanggalawal" + u).val(awal)
    $("#input_tanggalakhir" + u).val(akhir)
  });
  loadAll()
}

function loadAll () {
  let tglawal = $("#input_tanggalawal1").val()
  let tglakhir = $("#input_tanggalakhir1").val()

  $.ajax({
    url: "{!! url('ubahpocustloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal,
      tglakhir,
    },
    success: function(res) {
      upcRows[1] = res.tempOutstanding || [];
      upcRows[2] = res.tempOutstanding2 || [];
      upcRows[3] = res.tempOutstanding3 || [];
      let aktif = activeVisibleTabKeyUPC();
      [1, 2, 3].forEach(function (u) {
        if (u === aktif) { reinitTabelUPC(u) } else { upcPerluGambar[u] = true }
      });
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('/');
}

</script>

@endsection
