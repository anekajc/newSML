@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Terima PO Customer')
@section('title', 'SML - Terima PO Customer')

@endsection

{{-- Terima PO Customer: fungsi diport dari programajcSML (menu yang sudah dipakai user),
     tampilan mengikuti menu marketing newSML lain (closingso/so -> purchaseOrder).
     Tab, kolom, form tambah/koreksi (Tgl, No PO, Customer cari-ketik, Total PO), tombol
     Close + keterangan, dan "setelah tambah form tetap terbuka & dikosongkan" sama seperti
     programajcSML. Tambahan: filter Periode (default bulan periode aktif), kolom bisa
     diatur lewat ikon gear, validasi input, dan perbaikan bug (lihat komentar di JS). --}}
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

#tabel thead th, #tabel2 thead th, #tabel3 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd), #tabel3 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover, #tabel3 tbody tr:hover { background-color: #f5f3ff; }

.data-table th,
.data-table td {
  white-space: nowrap;
}

/* Daftar hasil cari customer (cara programajcSML: ketik kode/nama, pilih dari daftar). */
.tpc-cust-wrap { position: relative; }
.tpc-cust-list {
  display: none; position: absolute; left: 0; right: 0; top: 100%; z-index: 1060;
  max-height: 240px; overflow-y: auto; margin-top: 2px;
  background: #fff; border: 1px solid #e3e6ec; border-radius: 8px;
  box-shadow: 0 8px 20px rgba(17, 24, 39, .12);
}
.tpc-cust-list .tpc-cust-item {
  display: block; padding: 7px 12px; font-size: 13px; color: #1d2130; cursor: pointer;
  white-space: normal; word-break: break-word;
}
.tpc-cust-list .tpc-cust-item:hover,
.tpc-cust-list .tpc-cust-item.aktif { background: #e8edff; color: #2563eb; }
.tpc-cust-list .tpc-cust-kosong { display: block; padding: 7px 12px; font-size: 13px; color: #9ca3af; }
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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding PO Terima</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Monitoring PO Customer - SO</a>
        <a class="nav-item nav-link" id="nav-profile2-tab" data-toggle="tab" href="#profile2" role="tab" aria-controls="nav-profile2" aria-selected="false">PO Customer - Close</a>
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
                    <input type="date" onchange="onChangePeriodeTPC(1)" class="po-filter-inp" id="input_tanggalawal1" value="{{ $tglawal }}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeTPC(1)" class="po-filter-inp" id="input_tanggalakhir1" value="{{ $tglakhir }}">
                  </div>
                  <input type="search" id="tpcSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="tpcLen1">Tampilkan</label>
                    <select id="tpcLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                  </div>
                  <div class="po-toolbar-act">
                    <button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
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
                    <input type="date" onchange="onChangePeriodeTPC(2)" class="po-filter-inp" id="input_tanggalawal2" value="{{ $tglawal }}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeTPC(2)" class="po-filter-inp" id="input_tanggalakhir2" value="{{ $tglakhir }}">
                  </div>
                  <input type="search" id="tpcSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="tpcLen2">Tampilkan</label>
                    <select id="tpcLen2" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
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

        <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile2-tab">
          <div class="row">
            <div class="col-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" onchange="onChangePeriodeTPC(3)" class="po-filter-inp" id="input_tanggalawal3" value="{{ $tglawal }}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeTPC(3)" class="po-filter-inp" id="input_tanggalakhir3" value="{{ $tglakhir }}">
                  </div>
                  <input type="search" id="tpcSearch3" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="tpcLen3">Tampilkan</label>
                    <select id="tpcLen3" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
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

<!-- start modal tambah / koreksi PO Customer -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="formTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formTitle">Tambah PO Customer</h5>
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
                <label>Tgl Terima</label>
                <input id="input_add_tanggal" type="date" class="form-control">
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>No PO Customer</label>
                <input id="input_add_nopo" type="text" class="form-control" maxlength="100" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group tpc-cust-wrap">
                <label>Customer</label>
                <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="Cari kode / nama customer..."
                  autocomplete="off" oninput="onInputCustomer(this.value)" onkeydown="onKeyDownCustomer(event)">
                <div id="dropdown_pelanggan" class="tpc-cust-list"></div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Nama Customer</label>
                <input type="text" class="form-control" id="input_add_namacustomer" placeholder="Nama Customer" disabled>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Total PO</label>
                <input type="text" id="input_add_totalpo" data-a-sign="" data-a-dec="." data-a-sep=","
                  class="form-control text-right input-partial-number" autocomplete="off">
              </div>
            </div>
          </div>

        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" onclick="submitAdd()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal tambah / koreksi -->

<!-- start modal Closing -->
<div class="modal fade" id="formclosing" tabindex="-1" role="dialog" aria-labelledby="formClosingTitle" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formClosingTitle">Close PO Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>PO Customer</label>
                <input type="text" class="form-control" id="input_close_nopo" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Customer</label>
                <input type="text" class="form-control" id="input_close_customer" disabled>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Keterangan</label>
                <input type="text" class="form-control" id="input_close_keterangan" maxlength="200" autocomplete="off">
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" onclick="submitClosing()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal Closing -->

@endsection

@section('js')
@include('marketing.partials.tampilan-purchasing-js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let xtipe = 'add'
let koreksiid = 0
let tempClose = {}

jQuery(function($) {
  $('.input-partial-number').autoNumeric('init',
    {
      vMin : '0',   // autoNumeric 1.9: opsi minimum bernama vMin (minimumValue diabaikan -> bisa negatif)
    }
  );
});

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Pola sama dengan closingso.blade.php, untuk tiga tabel:
 * tabel (urut 1) = Outstanding PO Terima (ada kolom Actions),
 * tabel2 (urut 2) = Monitoring PO Customer - SO, tabel3 (urut 3) = PO Customer - Close.
 * Semua kolom tampil saat pertama dibuka dan setelah "Reset kolom" (kolom programajcSML + tambahan).
 */
let tpcCart = { 1 : [], 2 : [], 3 : [] }
let tpcActiveUrut = 0
const TPC_HREF = 'terimapocustomer'
const TPC_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const TPC_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
const TPC_ID_TABEL = { 1 : 'tabel', 2 : 'tabel2', 3 : 'tabel3' }
const TPC_ID_BAR = { 1 : 'rtBarTabel', 2 : 'rtBarTabel2', 3 : 'rtBarTabel3' }
const TPC_ID_TAB = { 1 : 'nav-home-tab', 2 : 'nav-profile-tab', 3 : 'nav-profile2-tab' }
// Kolom angka yang kosong (bukan 0) kalau datanya NULL -- sama seperti programajcSML.
const TPC_KOSONG_JIKA_NULL = ['LeadTime']
let tpcPerluGambar = { 1 : false, 2 : false, 3 : false }
let tpcRows = { 1 : [], 2 : [], 3 : [] }
let tpcPanjangHalaman = { 1 : 10, 2 : 10, 3 : 10 }

function tpcPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function tpcEsc (v) {
  return String(v === undefined || v === null ? '' : v)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;')
}

function tpcDefaultCart (urut) {
  if (urut === 1) {
    return [
      ['TglInput',     'Tgl Create',     1, 'date',    0, 0],
      ['TglTerima',    'Tgl Terima',     1, 'date',    0, 0],
      ['POCustomer',   'No PO Customer', 1, 'varchar', 0, 0],
      ['KodeCustSupp', 'Kode Customer',  1, 'varchar', 0, 0],
      ['NAMACUSTSUPP', 'Nama Customer',  1, 'varchar', 0, 0],
      ['rppo',         'Total PO',       1, 'float',   0, 0],
      ['ALAMAT',       'Alamat',         1, 'varchar', 0, 0],
    ]
  }
  if (urut === 2) {
    return [
      ['TglInput',     'Tgl Create',     1, 'date',    0, 0],
      ['TglTerima',    'Tgl Terima',     1, 'date',    0, 0],
      ['POCustomer',   'No PO Cust',     1, 'varchar', 0, 0],
      ['TglSO',        'Tgl SO',         1, 'date',    0, 0],
      ['NAMACUSTSUPP', 'Customer',       1, 'varchar', 0, 0],
      ['NOSO',         'Nobukti SO',     1, 'varchar', 0, 0],
      ['TotDPP',       'Nilai DPP',      1, 'float',   0, 0],
      ['TglOto1',      'Tgl Oto1',       1, 'date',    0, 0],
      ['TglOto2',      'Tgl Oto2',       1, 'date',    0, 0],
      ['LeadTime',     'Lead Time',      1, 'float',   0, 0],
      ['KodeCustSupp', 'Kode Customer',  1, 'varchar', 0, 0],
      ['rppo',         'Total PO',       1, 'float',   0, 0],
    ]
  }
  return [
    ['TglInput',     'Tgl Create',     1, 'date',    0, 0],
    ['TglTerima',    'Tgl Terima',     1, 'date',    0, 0],
    ['POCustomer',   'No PO Customer', 1, 'varchar', 0, 0],
    ['KodeCustSupp', 'Kode Customer',  1, 'varchar', 0, 0],
    ['NAMACUSTSUPP', 'Nama Customer',  1, 'varchar', 0, 0],
    ['rppo',         'Total PO',       1, 'float',   0, 0],
    ['userclose',    'User Close',     1, 'varchar', 0, 0],
    ['tglclose',     'Tgl Close',      1, 'date',    0, 0],
    ['ketclose',     'Ket. Close',     1, 'varchar', 0, 0],
    ['ALAMAT',       'Alamat',         1, 'varchar', 0, 0],
  ]
}

function tpcBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, TPC_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function tpcAktifkanTabel (urut) {
  tpcActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = tpcCart[urut]
}

function tpcOnChangeAktif () {
  reinitTabelTPC(tpcActiveUrut || 1)
}

window.g_href = TPC_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = tpcCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(TPC_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: TPC_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: TPC_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        tpcCart[urut] = tpcBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        tpcCart[urut] = tpcDefaultCart(urut)
        window.gcart_header = tpcCart[urut]
        window.doSimpanHeader(TPC_HREF, urut)
      }
      window.gcart_header = tpcCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      tpcCart[urut] = tpcDefaultCart(urut)
      window.gcart_header = tpcCart[urut]
    }
  })
}

function activeVisibleTabKeyTPC () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2; }
  if ($('#nav-profile2-tab').hasClass('active')) { return 3; }
  return 1;
}

const TPC_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const TPC_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let tpcRtSudahInit = false
function tpcInitReportTableSekali () {
  if (tpcRtSudahInit || typeof ReportTable === 'undefined') { return }
  tpcRtSudahInit = true
  let urutAktif = activeVisibleTabKeyTPC()
  Object.keys(TPC_ID_TABEL).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: '#' + TPC_ID_TABEL[u], bar: '#' + TPC_ID_BAR[u], onChange: tpcOnChangeAktif })
  });
  ReportTable.init({ table: TPC_SELEKTOR_TABEL_AKTIF, bar: TPC_SELEKTOR_BAR_AKTIF, onChange: tpcOnChangeAktif })

  let tpcGuardUlangKlik = false;
  ['#tabel', '#tabel2', '#tabel3'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (tpcGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      tpcGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      tpcGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderTPC (tableSel, cols, withActions) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
  if (withActions) { headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>'); }
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

// Tanggal dari SQL Server ("2026-10-01 00:00:00.000") dibaca sebagai teks supaya tidak
// bergeser karena zona waktu browser; tampil dd/mm/yyyy seperti menu marketing lain.
function tpcTanggalISO (raw) {
  if (!raw) { return '' }
  let s = String(raw)
  if (/^\d{4}-\d{2}-\d{2}/.test(s)) { return s.substring(0, 10) }
  return formatDate(raw).split('/').join('-')
}

function tpcValueCell (row, col) {
  let raw = tpcPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') {
    let iso = tpcTanggalISO(raw)
    return '<td>' + (iso ? iso.split('-').reverse().join('/') : '') + '</td>';
  }
  if (type === 'float') {
    if ((raw === undefined || raw === null || raw === '') && TPC_KOSONG_JIKA_NULL.indexOf(col[0]) !== -1) { return '<td class="text-right"></td>'; }
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('en-US', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + tpcEsc(raw) + '</td>';
}

// Tombol Edit & Close per baris (programajcSML). Baris dirujuk lewat indeks data, bukan
// teks di onclick -- di programajcSML nama customer/No PO yang mengandung tanda petik (')
// membuat tombol tidak bisa diklik.
function tabelActionsCell (row, i) {
  let html = '<td class="text-center">';
  html += '<button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Edit" onclick="buttonKoreksi(' + i + ')"><i class="bi bi-pen"></i></button>';
  html += '<button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Close" onclick="buttonclose(' + i + ')"><i class="bi bi-lock"></i></button>';
  html += '</td>';
  return html;
}

function renderTabelTPC (urut) {
  let cols = (tpcCart[urut].length ? tpcCart[urut] : tpcDefaultCart(urut)).filter(function (c) { return c[2] === 1; });
  let denganAksi = urut === 1
  let html = "";
  (tpcRows[urut] || []).forEach(function (row, i) {
    html += '<tr>' + (denganAksi ? tabelActionsCell(row, i) : '');
    cols.forEach(function (col) { html += tpcValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById(TPC_ID_TABEL[urut] + '_data').innerHTML = html;
  tulisTheadHeaderTPC('#' + TPC_ID_TABEL[urut], cols, denganAksi);
}

function tpcIkatSearch (urut) {
  let input = document.getElementById('tpcSearch' + urut)
  let idTabel = TPC_ID_TABEL[urut]
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

function tpcIkatPanjangHalaman (urut) {
  let sel = document.getElementById('tpcLen' + urut)
  let idTabel = TPC_ID_TABEL[urut]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(tpcPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    tpcPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(tpcPanjangHalaman[urut]).draw() }
  })
}

const TPC_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabelTPC (urut) {
  let sel = '#' + TPC_ID_TABEL[urut]
  try {
    if ($.fn.DataTable.isDataTable(sel)) { $(sel).DataTable().destroy(); }
    renderTabelTPC(urut);
    $(sel).DataTable({ dom: TPC_DOM_STRING, lengthChange: false, pageLength: tpcPanjangHalaman[urut], paging: true, ordering: false });
    let cari = document.getElementById('tpcSearch' + urut)
    if (cari && cari.value) { $(sel).DataTable().search(cari.value).draw() }
    tpcIkatSearch(urut); tpcIkatPanjangHalaman(urut); tpcPerluGambar[urut] = false;
  } catch (e) { console.error('reinitTabelTPC failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

$(document).ready(function(){
  tpcAktifkanTabel(1); window.doSetHeader(1, false);
  tpcRows[1] = @json($tempOutstanding);
  reinitTabelTPC(1);

  tpcAktifkanTabel(2); window.doSetHeader(2, false);
  tpcRows[2] = @json($tempOutstanding2);
  reinitTabelTPC(2);

  tpcAktifkanTabel(3); window.doSetHeader(3, false);
  tpcRows[3] = @json($tempOutstanding3);
  reinitTabelTPC(3);

  tpcAktifkanTabel(1);
  tpcInitReportTableSekali();

  [1, 2, 3].forEach(function (urut) {
    $('#' + TPC_ID_TAB[urut]).on('shown.bs.tab', function () {
      tpcAktifkanTabel(urut)
      if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
      if (tpcPerluGambar[urut]) { reinitTabelTPC(urut); }
    });
  });

  // Fokus ke No PO saat form dibuka supaya input berikutnya bisa langsung diketik.
  $('#form').on('shown.bs.modal', function () { $('#input_add_nopo').trigger('focus') })
  $('#form').on('hidden.bs.modal', function () { $('#dropdown_pelanggan').hide() })
  $('#formclosing').on('shown.bs.modal', function () { $('#input_close_keterangan').trigger('focus') })
});

/* ============ Cari customer (cara programajcSML: ketik kode/nama, pilih dari daftar) ============ */
let tpcTimerCust = null
let tpcHasilCust = []
let tpcIdxCust = -1

function onInputCustomer (keyword) {
  // Kode diketik ulang -> nama dikosongkan, customer harus dipilih lagi dari daftar.
  $("#input_add_namacustomer").val('')
  if (tpcTimerCust) { clearTimeout(tpcTimerCust) }
  if (!keyword || keyword.trim().length < 1) {
    $("#dropdown_pelanggan").hide();
    return;
  }
  tpcTimerCust = setTimeout(function () { searchPelanggan(keyword.trim()) }, 250)
}

function searchPelanggan (keyword) {
  $.ajax({
    url: "{!! url('terimapocustomerpelanggan') !!}",
    type: "get",
    data: { search: keyword },
    success: function (res) {
      // Abaikan hasil lama kalau isian sudah berubah selama menunggu.
      if ($("#input_add_kodecustomer").val().trim() !== keyword) { return }
      tpcHasilCust = res || []
      tpcIdxCust = -1
      let html = '';
      if (!tpcHasilCust.length) {
        html = '<span class="tpc-cust-kosong">Tidak ditemukan</span>';
      } else {
        tpcHasilCust.forEach(function (item, i) {
          html += '<a href="#" class="tpc-cust-item" data-idx="' + i + '" onmousedown="event.preventDefault(); pilihPelangganIdx(' + i + ')">'
            + tpcEsc(item.KODECUSTSUPP) + ' - ' + tpcEsc(item.NAMACUSTSUPP) + '</a>';
        });
      }
      $("#dropdown_pelanggan").html(html).show();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mencari customer')
    }
  });
}

function pilihPelangganIdx (i) {
  let item = tpcHasilCust[i]
  if (!item) { return }
  pilihPelanggan(item.KODECUSTSUPP, item.NAMACUSTSUPP)
}

function pilihPelanggan (kode, nama) {
  $("#input_add_kodecustomer").val(kode);
  $("#input_add_namacustomer").val(nama);
  $("#dropdown_pelanggan").hide();
}

// Panah atas/bawah + Enter untuk memilih dari daftar, Esc menutup daftar.
function onKeyDownCustomer (e) {
  let $list = $("#dropdown_pelanggan")
  if (!$list.is(':visible') || !tpcHasilCust.length) { return }
  if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
    e.preventDefault()
    tpcIdxCust += (e.key === 'ArrowDown' ? 1 : -1)
    if (tpcIdxCust < 0) { tpcIdxCust = tpcHasilCust.length - 1 }
    if (tpcIdxCust >= tpcHasilCust.length) { tpcIdxCust = 0 }
    let $items = $list.find('.tpc-cust-item').removeClass('aktif')
    let el = $items.eq(tpcIdxCust).addClass('aktif')[0]
    if (el) { el.scrollIntoView({ block: 'nearest' }) }
  } else if (e.key === 'Enter') {
    e.preventDefault()
    pilihPelangganIdx(tpcIdxCust >= 0 ? tpcIdxCust : 0)
  } else if (e.key === 'Escape') {
    e.stopPropagation()
    $list.hide()
  }
}

$(document).on('click', function (e) {
  if (!$(e.target).closest('#input_add_kodecustomer, #dropdown_pelanggan').length) {
    $("#dropdown_pelanggan").hide();
  }
});

/* ============ Tambah / Koreksi ============ */
function kosongkanFormPO () {
  document.getElementById("input_add_nopo").value = ''
  $("#input_add_totalpo").val('')
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_tanggal").value = formatDate(new Date()).split('/').join('-');
  $("#dropdown_pelanggan").hide()
}

// Tambah dikunci dengan hak Tambah, Edit dengan hak Koreksi (programajcSML tidak mengecek).
function buttonAdd () {
  if (!Number($("#akses_istambah").val())) {
    alertify.warning('No access')
    return
  }
  xtipe = 'add'
  koreksiid = 0
  $("#formTitle").text('Tambah PO Customer')
  kosongkanFormPO()
  $("#form").modal("show");
}

function buttonKoreksi (i) {
  if (!Number($("#akses_iskoreksi").val())) {
    alertify.warning('No access')
    return
  }
  let row = tpcRows[1][i]
  if (!row) { return }
  xtipe = 'edit'
  koreksiid = row.ID
  $("#formTitle").text('Koreksi PO Customer')
  // programajcSML memakai valueAsDate = new Date(TglTerima): di zona WIB tanggal mundur
  // satu hari dan ikut tersimpan mundur saat Simpan. Sekarang tanggal diambil apa adanya.
  document.getElementById("input_add_tanggal").value = tpcTanggalISO(row.TglTerima)
  document.getElementById("input_add_kodecustomer").value = row.KodeCustSupp || ''
  document.getElementById("input_add_namacustomer").value = row.NAMACUSTSUPP || ''
  document.getElementById("input_add_nopo").value = row.POCustomer || ''
  if (row.rppo) { $("#input_add_totalpo").autoNumeric('set', Number(row.rppo)) } else { $("#input_add_totalpo").val('') }
  $("#dropdown_pelanggan").hide()
  $("#form").modal("show");
}

function submitAdd () {
  let choice = (xtipe == 'edit') ? 'U' : 'I'
  let _token = $("#_token").val()
  let kodecust = $("#input_add_kodecustomer").val().trim()
  let namacust = $("#input_add_namacustomer").val()
  let pocust = $("#input_add_nopo").val().trim()
  let tanggal = $("#input_add_tanggal").val()
  let totalpo = Number($("#input_add_totalpo").autoNumeric('get')) || 0
  let id = (xtipe == 'edit') ? koreksiid : 0

  if (!tanggal) { alertify.warning('Tgl terima harus diisi'); return }
  if (!pocust) { alertify.warning('No PO Customer harus diisi'); $("#input_add_nopo").trigger('focus'); return }
  if (!kodecust || !namacust) { alertify.warning('Customer harus dipilih dari daftar'); $("#input_add_kodecustomer").trigger('focus'); return }

  $.ajax({
    url: "{!! url('terimapocustomerspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodecust,
      pocust,
      tanggal,
      id,
      choice,
      totalpo
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res);
        return
      }
      loadAll()
      if (choice == 'U') {
        alertify.success('PO Cust telah diupdate');
        $("#form").modal('hide')
      } else {
        // Sama seperti programajcSML: form tetap terbuka & dikosongkan untuk input PO berikutnya.
        alertify.success('PO Cust telah ditambah');
        kosongkanFormPO()
        $("#input_add_nopo").trigger('focus')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

/* ============ Close PO Customer ============ */
function buttonclose (i) {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let row = tpcRows[1][i]
  if (!row) { return }
  tempClose = { ID: row.ID, POCustomer: row.POCustomer }
  document.getElementById("input_close_nopo").value = row.POCustomer || ''
  document.getElementById("input_close_customer").value = row.NAMACUSTSUPP || ''
  document.getElementById("input_close_keterangan").value = row.ketclose || ''
  $("#formclosing").modal('show')
}

function submitClosing () {
  let keterangan = $("#input_close_keterangan").val().trim()
  let _token  = $("#_token").val()

  // Sama seperti Closing SO: keterangan closing wajib diisi.
  if (!keterangan) {
    alertify.warning("Keterangan closing harus diisi")
    $("#input_close_keterangan").trigger('focus')
    return
  }

  $.ajax({
    url: "{!! url('terimapocustomerspclose') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      ID: tempClose.ID,
      keterangan,
    },
    success: function(res) {
      if (res == 1) {
        loadAll()
        $("#formclosing").modal('hide')
        alertify.success("Berhasil closing PO Customer")
      } else {
        alertify.warning(res)
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

/* ============ Periode & muat ulang ============ */
function onChangePeriodeTPC (urut) {
  let awal = $("#input_tanggalawal" + urut).val()
  let akhir = $("#input_tanggalakhir" + urut).val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  // Ketiga tab memakai satu periode yang sama (loadAll memuat ketiganya sekaligus).
  [1, 2, 3].forEach(function (u) {
    $("#input_tanggalawal" + u).val(awal)
    $("#input_tanggalakhir" + u).val(akhir)
  });
  loadAll()
}

function loadAll () {
  let tglawal = $("#input_tanggalawal1").val()
  let tglakhir = $("#input_tanggalakhir1").val()

  $.ajax({
    url: "{!! url('terimapocustomerloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal,
      tglakhir,
    },
    success: function(res) {
      tpcRows[1] = res.tempOutstanding || [];
      tpcRows[2] = res.tempOutstanding2 || [];
      tpcRows[3] = res.tempOutstanding3 || [];
      let aktif = activeVisibleTabKeyTPC();
      [1, 2, 3].forEach(function (u) {
        if (u === aktif) { reinitTabelTPC(u) } else { tpcPerluGambar[u] = true }
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
