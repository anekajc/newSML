@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Closing Penyerahan Sample')
@section('title', 'SML - Closing Penyerahan Sample')

@endsection

@section('css')

<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Port 1:1 ke UI SO -- @extends newmasterTest (bukan lagi gudang.newmaster), custom-tabs
     pill bar, po-toolbar, dan report-table.js column engine untuk #tabel (Penyerahan Sample
     -- antrian outstanding yang bisa dikunci) dan #tabel2 (Closing Penyerahan Sample --
     item yang sudah dikunci), sama seperti gudangpenyerahansample.blade.php. --}}
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

.custom-tabs .nav-link:hover {
  background: transparent;
  color: #007bff;
}

.custom-tabs .nav-link.active {
  background: #007bff;
  border-color: #007bff;
  color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}

.tab-card {
  display: block !important;
  align-items: flex-start !important;
  padding: 0 !important;
  border: none !important;
  margin-bottom: 6px !important;
}

.tab-card .card-body {
  padding: 5px 10px !important;
}

#page1 .card {
  display: block !important;
  align-items: stretch !important;
  padding: 0 !important;
  text-align: left !important;
  cursor: default !important;
}

#page1 .card:hover {
  transform: none !important;
  box-shadow: none !important;
  border-color: var(--border) !important;
}

#page1 .tab-content .col-md-12 {
  min-width: 0;
  max-width: 100%;
}

{{-- #tabel sudah dapat width/min-width/warna tombol Aksi dari CSS terpusat
     (:where(.po-list-page) ...) sejak #page1 diberi class "po-list-page" -- lihat
     po-table-header.css. #tabel2 tidak disentuh aturan #tabel di sana (id berbeda), jadi
     tetap butuh aturan manual di sini. --}}
#tabel2 { min-width: 100%; }

#tabel2 td:first-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel2 td:first-child .btn {
  width: 30px;
  height: 30px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 7px;
  font-size: 13px;
  border: 1px solid transparent;
  box-shadow: none;
  transition: all .12s ease;
}

#tabel2 td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel2 td:first-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel2 td:first-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel2 td:first-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

#tabel2 td:first-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel thead th,
#tabel2 thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#tabel tbody tr:hover,
#tabel2 tbody tr:hover {
  background-color: #f5f3ff;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

#tabel tbody .action-buttons-wrap,
#tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}
</style>
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id='page1' class="container-fluid mainpage po-list-page">
<div class="container-fluid" >



</div>

<div id="printContainer" style="display:none">



</div>
<div id="contentContainer" class="container-fluid">
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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Penyerahan Sample</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Closing Penyerahan Sample</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="csSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="csLen1">Tampilkan</label>
                    <select id="csLen1" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
                <div id="rtBarTabel"></div>
                <table id="tabel" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel_data" class="text-left"></tbody>
                </table>
                <div class="po-rt-hint">
                  <i class="bi bi-info-circle"></i>
                  Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                  untuk menyembunyikan kolom atau mengatur jumlah desimal.
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="csSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="csLen2">Tampilkan</label>
                    <select id="csLen2" class="po-len-inp">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="-1">Semua</option>
                    </select>
                  </div>
                </div>
                <div id="rtBarTabel2"></div>
                <table id="tabel2" class="data-table">
                  <thead style="white-space:nowrap;"></thead>
                  <tbody id="tabel2_data" class="text-left"></tbody>
                </table>
                <div class="po-rt-hint">
                  <i class="bi bi-info-circle"></i>
                  Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom
                  untuk menyembunyikan kolom atau mengatur jumlah desimal.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

{{-- Start Modal closing sample --}}
<div class="modal fade" id="modalLockSample" tabindex="-1" role="dialog" aria-labelledby="modalLockSampleLabel">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alasan Penguncian Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="lock_nobukti">
        <input type="hidden" id="lock_mode">
        <input type="hidden" id="lock_urut">
        <div class="form-group">
          <label for="lock_reason">Masukkan Alasan:</label>
          <textarea class="form-control" id="lock_reason" rows="3" placeholder="" autocomplete="off" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="buttonCloseForm()" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" onclick="submitLock()">Kunci</button>
      </div>
    </div>
  </div>
</div>
{{-- End Modal closing sample --}}

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola opCart/opAktifkanTabel milik perintahopname.blade.php, untuk
 * kedua tabel di halaman ini (#tabel = Penyerahan Sample outstanding, #tabel2 =
 * Closing Penyerahan Sample / item terkunci). renderTabelRows()/renderTabel2Rows()
 * mem-force aktivasi urut-nya sendiri sebelum membaca gcart_header, supaya tidak
 * ada drift seperti bug yang ditemukan di perintahopname/opnamebarang. */
let csCart = { 1 : [], 2 : [] }
let csActiveUrut = 0
const CS_HREF = 'gudangclosingpenyerahansample'
const CS_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const CS_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function csPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function csDefaultCart (urut) {
  if (urut === 2) {
    return [
      ['NOBUKTI',   'No. Bukti',        1, 'varchar', 0, 0],
      ['KODEBRG',   'Kode Barang',      1, 'varchar', 0, 0],
      ['NAMABRG',   'Nama Barang',      1, 'varchar', 0, 0],
      ['Satuan',    'Satuan',           1, 'varchar', 0, 0],
      ['QntCLose',  'Qnt Close',        1, 'float',   0, 2],
      ['UserBatal', 'User Batal',       1, 'varchar', 0, 0],
      ['tglbatal',  'Tgl Batal',        1, 'date',    0, 0],
      ['ketBatal',  'Keterangan Batal', 1, 'varchar', 0, 0],
    ]
  }
  return [
    ['nobukti',      'No. Bukti',  1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',    1, 'date',    0, 0],
    ['Keterangan',   'Keterangan', 1, 'varchar', 0, 0],
    ['NAMACUSTSUPP', 'Customer',   1, 'varchar', 0, 0],
    ['KODEBRG',      'Kode Brg',   1, 'varchar', 0, 0],
    ['NAMABRG',      'Nama Barang',1, 'varchar', 0, 0],
    ['NOTE',         'Note',       1, 'varchar', 0, 0],
    ['sisa',         'Sisa',       1, 'float',   0, 2],
    ['QntCLose',     'Close',      1, 'float',   0, 2],
  ]
}

function csBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)
    cart.push([
      values[i],
      h,
      Number(isshowns[i]) === 1 ? 1 : 0,
      CS_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function csAktifkanTabel (urut) {
  csActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = csCart[urut]
}

function csOnChangeAktif () {
  if (csActiveUrut === 2) { reinitTabel2(); }
  else { reinitTabel(); }
}

window.g_href = CS_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = (mode === 2) ? mode : 1
  let cart = csCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(CS_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : CS_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = (mode === 2) ? mode : 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : CS_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        csCart[urut] = csBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        csCart[urut] = csDefaultCart(urut)
        window.gcart_header = csCart[urut]
        window.doSimpanHeader(CS_HREF, urut)
      }
      window.gcart_header = csCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      csCart[urut] = csDefaultCart(urut)
      window.gcart_header = csCart[urut]
    }
  })
}

let csRtSudahInit = false
function csInitReportTableSekali () {
  if (csRtSudahInit || typeof ReportTable === 'undefined') { return }
  csRtSudahInit = true

  ReportTable.init({ table : '#tabel',  bar : '#rtBarTabel',  onChange : csOnChangeAktif })
  ReportTable.init({ table : '#tabel2', bar : '#rtBarTabel2', onChange : csOnChangeAktif })

  let csGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (csGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      csGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      csGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderCS (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function csValueCell (row, col) {
  let raw = csPickCI(row, col[0]);
  let type = col[3];

  if (type === 'date') {
    if (!raw) { return '<td></td>'; }
    return '<td>' + formatDate(raw, '/') + '</td>';
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

function csTabelActionsCell (row) {
  let nobukti = csPickCI(row, 'nobukti');
  let urut = csPickCI(row, 'Urut');
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-primary btn-sm" type="button" title="Kunci Per Barang" onclick="lockItem(\'' + nobukti + '\', \'' + urut + '\')"><i class="bi bi-lock-fill"></i></button>';
  html += '<button class="btn btn-success btn-sm" type="button" title="Kunci Per No Bukti" onclick="lockAll(\'' + nobukti + '\')"><i class="bi bi-lock-fill"></i></button>';
  html += '</div></td>';
  return html;
}

function csTabel2ActionsCell (row) {
  let nobukti = csPickCI(row, 'NOBUKTI');
  let urut = csPickCI(row, 'URUT');
  let html = '<td class="text-center" style="white-space:nowrap;"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Buka Per Barang" onclick="submitUnlock(\'' + nobukti + '\', \'item\', \'' + urut + '\')"><i class="bi bi-unlock"></i></button>';
  html += '<button class="btn btn-danger btn-sm" type="button" title="Buka Per No Bukti" onclick="submitUnlock(\'' + nobukti + '\', \'all\')"><i class="bi bi-unlock-fill"></i></button>';
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  if (csActiveUrut !== 1) { csAktifkanTabel(1); }
  let cols = (csCart[1].length ? csCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr data-nobukti="' + csPickCI(row, 'nobukti') + '" data-urut="' + csPickCI(row, 'Urut') + '">' + csTabelActionsCell(row);
    cols.forEach(function (col) { html += csValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderCS('#tabel', cols);
}

function renderTabel2Rows (rows) {
  if (csActiveUrut !== 2) { csAktifkanTabel(2); }
  let cols = (csCart[2].length ? csCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr data-nobukti="' + csPickCI(row, 'NOBUKTI') + '" data-urut="' + csPickCI(row, 'URUT') + '">' + csTabel2ActionsCell(row);
    cols.forEach(function (col) { html += csValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderCS('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let csPanjangHalaman = { 1 : 10, 2 : 10 }

function csIkatSearch (urut) {
  let ids = { 1 : ['csSearch1', 'tabel'], 2 : ['csSearch2', 'tabel2'] }
  let input = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) {
        $('#' + idTabel).DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function csIkatPanjangHalaman (urut) {
  let ids = { 1 : ['csLen1', 'tabel'], 2 : ['csLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(csPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    csPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(csPanjangHalaman[urut]).draw()
    }
  })
}

const CS_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: CS_DOM_STRING, lengthChange: false, pageLength: csPanjangHalaman[1], paging: true, ordering: false });
    csIkatSearch(1);
    csIkatPanjangHalaman(1);
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: CS_DOM_STRING, lengthChange: false, pageLength: csPanjangHalaman[2], paging: true, ordering: false });
    csIkatSearch(2);
    csIkatPanjangHalaman(2);
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

$(document).ready(function() {
  csAktifkanTabel(1);
  window.doSetHeader(1, false);
  lastTabelRows = @json($tempOutstanding);
  reinitTabel();

  csAktifkanTabel(2);
  window.doSetHeader(2, false);
  lastTabel2Rows = @json($tempPenerimaan);
  reinitTabel2();

  csAktifkanTabel(1);

  csInitReportTableSekali();

  $('#nav-home-tab').on('shown.bs.tab', function () {
    csAktifkanTabel(1);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
  });
  $('#nav-profile-tab').on('shown.bs.tab', function () {
    csAktifkanTabel(2);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
  });

  $('#modalLockSample').on('shown.bs.modal', function () {
    $('#lock_reason').focus();
  });
});


function lockItem (nobukti, urut) {
  $('#lock_nobukti').val(nobukti);
  $('#lock_mode').val('item');
  $('#lock_urut').val(urut);
  $('#lock_reason').val('');
  $('#modalLockSample').modal('show');
}

function lockAll (nobukti) {
  $('#lock_nobukti').val(nobukti);
  $('#lock_mode').val('all');
  $('#lock_urut').val('');
  $('#lock_reason').val('');
  $('#modalLockSample').modal('show');
}

function submitLock () {
  const _token = $('#_token').val();
  const nobukti = $('#lock_nobukti').val();
  const mode = $('#lock_mode').val();
  const urut = $('#lock_urut').val();
  const reason = $('#lock_reason').val().trim();

  if (!reason) {
    alertify.warning('Silakan masukkan alasan penguncian.');
    return;
  }

  $.ajax({
    url: '{{ url("closingpenyerahansamplelock") }}',
    type: 'POST',
    data: {
      _token,
      nobukti,
      mode,
      urut,
      reason
    },
    success: function (res) {
      if (res.success) {
        $('#modalLockSample').modal('hide');
        alertify.success("Data berhasil dikunci");
        loadAll();
      } else {
        alertify.error(res.message || "Gagal mengunci data");
      }
    },
    error: function (xhr) {
      alertify.error("Terjadi kesalahan: " + (xhr.responseJSON?.message || 'Unknown error'));
    }
  });
}

function submitUnlock (nobukti, mode = 'all', urut = '') {
  alertify.confirm(
    'Buka Kunci',
    `Yakin ingin Membuka Kunci ${mode === 'item' ? 'Data ini' : 'No. Bukti ' + nobukti} ?`,
    function () {
      const _token = $('#_token').val();

      $.ajax({
        url: "{{ url('closingpenyerahansampleunlock') }}",
        type: "POST",
        data: {
          _token,
          nobukti,
          urut: mode === 'item' ? urut : '',
          mode
        },
        success: function (res) {
          if (res.success) {
            alertify.success("Data berhasil di-unlock");
            setTimeout(() => {
              loadAll();
            }, 300);
          } else {
            alertify.error(res.message || "Gagal membuka kunci");
          }
        },
        error: function (xhr) {
          alertify.error("Terjadi kesalahan: " + (xhr.responseJSON?.message || 'Unknown error'));
        }
      });
    },
    function () {
      console.log('Batal unlock');
    }
  );
}

function loadAll () {
  $.ajax({
    url: "{!! url('closingpenyerahansampleloadall') !!}",
    type: "get",
    success: function (res) {
      lastTabelRows = res.tempOutstanding
      lastTabel2Rows = res.tempPenerimaan
      reinitTabel()
      reinitTabel2()
    },
    error: function (xhr) {
      alertify.error("Gagal memuat data: " + (xhr.responseJSON?.message || 'Unknown error'));
    }
  });
}

function buttonCloseForm () {
  $('#modalLockSample').modal('hide');
  $('#page1').show();
  loadAll();
}

//format Date Khusus karena ada data di database format tidak beraturan
function formatDate(date, pemisah = '-') {
    if (!date) return '';

    const parsedDate = new Date(date);
    if (isNaN(parsedDate)) return '';

    let day = '' + parsedDate.getDate();
    let month = '' + (parsedDate.getMonth() + 1);
    const year = parsedDate.getFullYear();

    if (day.length < 2) day = '0' + day;
    if (month.length < 2) month = '0' + month;

    return [year, month, day].join(pemisah);
}

</script>

{{-- Script hover-warna tab lama dihapus -- custom-tabs CSS sudah menangani warna
     tab aktif/hover (lihat .custom-tabs .nav-link.active di @section('css')). --}}

@endsection
