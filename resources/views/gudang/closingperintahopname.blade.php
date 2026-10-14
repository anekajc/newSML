@extends('newmasterTest')
@section('buttons')

@endsection
@section('page-title', 'Closing Perintah Opname')

{{-- Rerouted to match so.blade.php's UI 1:1, marketing-full-menu-guide.md's
     standing pattern, same as so.blade.php/suratjalan.blade.php/
     cetaktandaterima.blade.php/invoicejasa.blade.php. po-table-header.css is
     already loaded globally by newmasterTest, but report-table.js is NOT --
     added at the very top of the js section below (missing it makes
     window.ReportTable undefined, which makes the thead-writing function
     silently no-op and crashes DataTables on a table with zero header columns,
     per the guide's own warning). The small page-local block (.custom-tabs/
     .tab-card/.po-len-wrap/.po-len-inp, pastel action-button styling, grey
     thead) is copied here verbatim from so.blade.php's own css section.
     Business logic (loadAll, lock/lockAll/submitLock, submitUnlock) is
     untouched -- only the layout/toolbar/column-header interactivity
     changed. --}}
@section('css')
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

.po-len-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--rt-card);
  border: 1.5px solid var(--rt-border);
  border-radius: 8px;
  padding: 5px 12px;
}

.po-len-wrap label {
  margin: 0;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--rt-ink-soft);
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
}

.po-len-inp {
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 700;
  color: var(--rt-ink);
  outline: none;
  cursor: pointer;
  padding: 2px 20px 2px 0;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right center;
}

/* Kolom Aksi -- pastel round-button treatment, copied and rescoped to this
   page's own #tabel/#tabel2 from so.blade.php's own css section. Actions is the
   FIRST column in both tables (tabelActionsCell()/tabel2ActionsCell() are
   prepended before the data cells), so scoped with td:first-child. */
#tabel td:first-child,
#tabel2 td:first-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn,
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

#tabel td:first-child .btn:hover,
#tabel2 td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-primary,
#tabel2 td:first-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-success,
#tabel2 td:first-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel td:first-child .btn-warning,
#tabel2 td:first-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel td:first-child .btn-danger,
#tabel2 td:first-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
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

#tabel tbody tr:nth-of-type(odd),
#tabel2 tbody tr:nth-of-type(odd) {
  background-color: #fbfbfc;
}

#tabel tbody tr:hover,
#tabel2 tbody tr:hover {
  background-color: #f5f3ff;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

/* Hide action buttons until the row is hovered */
#tabel tbody .action-buttons-wrap,
#tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap,
#tabel2 tbody tr:hover .action-buttons-wrap,
#tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}
</style>
@endsection
@section('content')

<div id="page1" class="container-fluid mainpage">
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

  {{-- Tab bar: card.tab-card + custom-tabs pill pattern, copied 1:1 from
       so.blade.php/cetaktandaterima.blade.php. --}}
  <div class="card mb-3 tab-card">
    <div class="card-body">
      <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          Permintaan Opname Barang
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
          Closing Permintaan Opname
        </a>
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
                  <input type="search" id="cxSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="cxLen1">Tampilkan</label>
                    <select id="cxLen1" class="po-len-inp">
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
                  <div class="po-filter-wrap">
                    <label>Periode</label>
                    <input type="date" class="po-filter-inp" id="cxInputDate1" value="{!! $date1 !!}"
                      onchange="cxReloadTabel2()">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" class="po-filter-inp" id="cxInputDate2" value="{!! $date2 !!}"
                      onchange="cxReloadTabel2()">
                  </div>
                  <input type="search" id="cxSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap">
                    <label for="cxLen2">Tampilkan</label>
                    <select id="cxLen2" class="po-len-inp">
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
          <button type="button" onclick="buttonCloseForm()" class="btn btn-danger" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-chip-biru" onclick="submitLock()">Kunci</button>
        </div>
      </div>
    </div>
  </div>
  {{-- End Modal closing sample --}}
</div>
</div>

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari ctCart/ctAktifkanTabel/ctInitReportTableSekali milik
 * cetaktandaterima.blade.php, sama seperti so.blade.php/invoicejasa.blade.php.
 * Endpoint persistensinya saveheadertable/getheadertable (HeaderTableController,
 * generik lintas halaman). Beda dari cetaktandaterima: baris tabel/tabel2 di
 * halaman ini adalah objek datar biasa (dari DB::select() langsung, bukan
 * groupBy), jadi cxPickCI() dipanggil langsung dengan row, bukan row[0]. */
let cxCart = { 1 : [], 2 : [] }
let cxActiveUrut = 0
const CX_HREF = 'closingtransfer'
const CX_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const CX_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
let cxPerluGambar = { 1 : false, 2 : false }

function activeVisibleTabKeyCX () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

function cxPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function cxDefaultCart (urut) {
  if (urut === 2) {
    return [
      ['NOBUKTI',   'No. Bukti',         1, 'varchar', 0, 0],
      ['KODEBRG',   'Kode Barang',       1, 'varchar', 0, 0],
      ['NAMABRG',   'Nama Barang',       1, 'varchar', 0, 0],
      ['GdgAsal',   'Gudang Asal',       1, 'varchar', 0, 0],
      ['GdgTujuan', 'Gudang Tujuan',     1, 'varchar', 0, 0],
      ['Satx',      'Satuan',            1, 'varchar', 0, 0],
      ['QNT',       'Qty',               1, 'float',   0, 2],
      ['QntBatal',  'Qty Batal',         1, 'float',   0, 2],
      ['TglBatal',  'Tgl Batal',         1, 'date',    0, 0],
      ['UserBatal', 'User Batal',        1, 'varchar', 0, 0],
      ['KetBatal',  'Keterangan Batal',  1, 'varchar', 0, 0]
    ]
  }
  return [
    ['NOBUKTI',   'No. Bukti',     1, 'varchar', 0, 0],
    ['KODEBRG',   'Kode Barang',   1, 'varchar', 0, 0],
    ['NAMABRG',   'Nama Barang',   1, 'varchar', 0, 0],
    ['Satuan',    'Sat',           1, 'varchar', 0, 0],
    ['QntComp',   'Qty',           1, 'float',   0, 2]
  ]
}

function cxBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      CX_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

function cxAktifkanTabel (urut) {
  cxActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = cxCart[urut]
}

function cxOnChangeAktif () {
  if (cxActiveUrut === 2) { reinitTabel2(); } else { reinitTabel(); }
}

window.g_href = CX_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode === 2 ? 2 : 1
  let cart = cxCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(CX_TIPE_KODE[c[3]] ?? 0)
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
      href     : CX_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode === 2 ? 2 : 1

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : CX_HREF,
      urut   : urut,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        let header = res.headertableheader
        let value = res.headertablevalue
        let isnumeric = res.isnumeric
        let isshown = res.isshown
        let tipe = res.desimal || []
        cxCart[urut] = cxBuatCart(header, value, isnumeric, isshown, tipe)
      } else {
        cxCart[urut] = cxDefaultCart(urut)
        window.gcart_header = cxCart[urut]
        window.doSimpanHeader(CX_HREF, urut)
      }
      window.gcart_header = cxCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      cxCart[urut] = cxDefaultCart(urut)
      window.gcart_header = cxCart[urut]
    }
  })
}

const CX_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const CX_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let cxRtSudahInit = false
function cxInitReportTableSekali () {
  if (cxRtSudahInit || typeof ReportTable === 'undefined') { return }
  cxRtSudahInit = true

  let urutAktif = activeVisibleTabKeyCX()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  let idBar = { 1 : '#rtBarTabel', 2 : '#rtBarTabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table : idTabel[u], bar : idBar[u], onChange : cxOnChangeAktif })
  });

  ReportTable.init({
    table    : CX_SELEKTOR_TABEL_AKTIF,
    bar      : CX_SELEKTOR_BAR_AKTIF,
    onChange : cxOnChangeAktif
  })
}

function tulisTheadHeaderCX (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px; width: 100px;">Action</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function cxValueCell (row, col) {
  let raw = cxPickCI(row, col[0]);
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

function tabelActionsCell (row) {
  let nobukti = cxPickCI(row, 'NOBUKTI');
  let urut = cxPickCI(row, 'URUT');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-primary btn-sm" title="Kunci Per Barang" type="button" onclick="lockItem(\'' + nobukti + '\', \'' + urut + '\')"><i class="bi bi-lock-fill"></i></button> ';
  html += '<button class="btn btn-success btn-sm" title="Kunci Per No Bukti" type="button" onclick="lockAll(\'' + nobukti + '\')"><i class="bi bi-lock-fill"></i></button>';
  html += '</div></td>';
  return html;
}

function tabel2ActionsCell (row) {
  let nobukti = cxPickCI(row, 'NOBUKTI');
  let urut = cxPickCI(row, 'URUT');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" title="Buka Per Barang" type="button" onclick="submitUnlock(\'' + nobukti + '\', \'item\', \'' + urut + '\')"><i class="bi bi-unlock"></i></button> ';
  html += '<button class="btn btn-danger btn-sm" title="Buka Per No Bukti" type="button" onclick="submitUnlock(\'' + nobukti + '\', \'all\')"><i class="bi bi-unlock-fill"></i></button>';
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (cxCart[1].length ? cxCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += cxValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderCX('#tabel', cols);
}

function renderTabel2Rows (rows) {
  let cols = (cxCart[2].length ? cxCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += cxValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderCX('#tabel2', cols);
}

let lastTabelRows = []
let lastTabel2Rows = []
let cxPanjangHalaman = { 1 : 10, 2 : 10 }

function cxIkatSearch (urut) {
  let ids = { 1 : ['cxSearch1', 'tabel'], 2 : ['cxSearch2', 'tabel2'] }
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

function cxIkatPanjangHalaman (urut) {
  let ids = { 1 : ['cxLen1', 'tabel'], 2 : ['cxLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(cxPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    cxPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) {
      $('#' + idTabel).DataTable().page.len(cxPanjangHalaman[urut]).draw()
    }
  })
}

const CX_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({
      dom: CX_DOM_STRING,
      lengthChange: false,
      pageLength: cxPanjangHalaman[1],
      paging: true,
      ordering: false,
    });
    cxIkatSearch(1);
    cxIkatPanjangHalaman(1);
    cxPerluGambar[1] = false;
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({
      dom: CX_DOM_STRING,
      lengthChange: false,
      pageLength: cxPanjangHalaman[2],
      paging: true,
      ordering: false,
    });
    cxIkatSearch(2);
    cxIkatPanjangHalaman(2);
    cxPerluGambar[2] = false;
  } catch (e) {
    console.error('reinitTabel2 failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable (key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    let urut = key === 'tabel2' ? 2 : 1
    cxAktifkanTabel(urut)
    window.doSetHeader(urut, true)
    ;(urut === 2 ? reinitTabel2 : reinitTabel)()
    alertify.success('Kolom telah direset ke tampilan default')
  }, function () {})
}

$(document).ready(function(){
  cxAktifkanTabel(1);
  window.doSetHeader(1, false);
  lastTabelRows = @json($tempOutstanding);
  reinitTabel();

  cxAktifkanTabel(2);
  window.doSetHeader(2, false);
  lastTabel2Rows = @json($tempPenerimaan);
  reinitTabel2();

  cxInitReportTableSekali();

  $('#nav-home-tab').on('shown.bs.tab', function () {
    cxAktifkanTabel(1);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
    if (cxPerluGambar[1]) { reinitTabel(); }
  });
  $('#nav-profile-tab').on('shown.bs.tab', function () {
    cxAktifkanTabel(2);
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); }
    if (cxPerluGambar[2]) { reinitTabel2(); }
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
    url: '{{ url("closingperintahopnamelock") }}',
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
        url: "{{ url('closingperintahopnameunlock') }}",
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
    url: "{!! url('closingtransferloadall') !!}",
    type: "get",
    data: {
      date1: $('#cxInputDate1').val(),
      date2: $('#cxInputDate2').val()
    },
    success: function (res) {
      cxAktifkanTabel(1);
      lastTabelRows = res.tempOutstanding || [];
      reinitTabel();

      cxAktifkanTabel(2);
      lastTabel2Rows = res.tempPenerimaan || [];
      reinitTabel2();

      cxAktifkanTabel(activeVisibleTabKeyCX());
    },
    error: function (xhr) {
      alertify.error("Gagal memuat data: " + (xhr.responseJSON?.message || 'Unknown error'));
    }
  });
}

// Periode range di tab "Closing Transfer Barang" -- cuma reload #tabel2 (tempPenerimaan),
// #tabel ("Transfer Barang") tidak difilter tanggal sama sekali jadi tidak disentuh.
function cxReloadTabel2 () {
  let date1 = $('#cxInputDate1').val();
  let date2 = $('#cxInputDate2').val();
  if (date1 && date2 && date1 > date2) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
    return;
  }
  $.ajax({
    url: "{!! url('closingtransferloadall') !!}",
    type: "get",
    data: { date1, date2 },
    success: function (res) {
      cxAktifkanTabel(2);
      lastTabel2Rows = res.tempPenerimaan || [];
      reinitTabel2();
      cxAktifkanTabel(activeVisibleTabKeyCX());
    },
    error: function (xhr) {
      alertify.error("Gagal memuat data: " + (xhr.responseJSON?.message || 'Unknown error'));
    }
  });
}

function buttonCloseForm () {
  $('#modalLockSample').modal('hide');
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
@endsection
