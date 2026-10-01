@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Perintah Uang Muka')
@section('title', 'SML - Perintah Uang Muka')

@endsection

{{-- Rerouted to match so.blade.php's UI 1:1 per marketing-full-menu-guide.md, same as
     kreditnote/perintahreturjual/invoicejasa before it. Tab "SO Sudah Otorisasi"
     (OtoPerf=0, DP>0) dan "Perintah Uang Muka" (OtoPerf=1) digabung jadi satu tabel
     dengan filter Status (Semua/Belum/Sudah), sama seperti kreditnote.blade.php --
     keduanya sama-sama cuma status split dari satu bentuk data, bukan tab beda jenis
     data seperti SO/Penawaran milik so.blade.php sendiri. Business logic (buttonAdd,
     submitAdd, submitBatalOto, submitPrint, getDetail/detailCetak) tidak diubah. --}}
@section('css')
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
.custom-tabs {
  display: inline-flex; justify-content: flex-start; align-items: center; gap: 2px;
  background-color: #f1f3f5; border-radius: 20px; padding: 3px;
}
.custom-tabs .nav-link {
  display: inline-block !important; padding: 5px 16px !important; font-size: 0.75rem !important;
  border: none; border-radius: 17px; color: #495057; background: transparent; font-weight: 600;
  transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}
.custom-tabs .nav-link:hover { background: transparent; color: #007bff; }
.custom-tabs .nav-link.active {
  background: #007bff; border-color: #007bff; color: #fff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, .35);
}
.tab-card {
  display: block !important; align-items: flex-start !important; padding: 0 !important;
  border: none !important; margin-bottom: 6px !important;
}
.tab-card .card-body { padding: 5px 10px !important; }
#page1 .card {
  display: block !important; align-items: stretch !important; padding: 0 !important;
  text-align: left !important; cursor: default !important;
}
#page1 .card:hover { transform: none !important; box-shadow: none !important; border-color: var(--border) !important; }
.po-len-wrap {
  display: flex; align-items: center; gap: 8px; background: var(--rt-card);
  border: 1.5px solid var(--rt-border); border-radius: 8px; padding: 5px 12px;
}
.po-len-wrap label {
  margin: 0; font-size: 11.5px; font-weight: 700; color: var(--rt-ink-soft);
  text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
}
.po-len-inp {
  border: none; background: transparent; font-size: 13px; font-weight: 700; color: var(--rt-ink);
  outline: none; cursor: pointer; padding: 2px 20px 2px 0; appearance: none;
  -webkit-appearance: none; -moz-appearance: none;
  background-image: url("data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right center;
}
#tabel td:first-child { display: flex; gap: 4px; justify-content: center; align-items: center; }
#tabel td:first-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
  box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover { background-color: #f5f3ff; }
#tabel tbody .action-buttons-wrap {
  opacity: 0; visibility: hidden; transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap {
  opacity: 1; visibility: visible; transform: translateX(0);
}
#tabelDetailAdd thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection

@section('content')
<div id="page1" class="container-fluid">

<div id="printContainer" style="display:none"></div>
<div id="contentContainer" class="container-fluid" >
  <input type="hidden" id="periode_tahun" value="{!! $periode->tahun !!}" />
  <input type="hidden" id="periode_bulan" value="{!! $periode->bulan !!}" />

  <input type="hidden" id="akses_istambah" value="{!! $akses->ISTAMBAH !!}" />
  <input type="hidden" id="akses_ishapus" value="{!! $akses->ISHAPUS!!}" />
  <input type="hidden" id="akses_iskoreksi" value="{!! $akses->ISKOREKSI !!}" />
  <input type="hidden" id="akses_iscetak" value="{!! $akses->ISCETAK !!}" />
  <input type="hidden" id="akses_isotorisasi1" value="{!! $akses->IsOtorisasi1 !!}" />
  <input type="hidden" id="akses_isbatal" value="{!! $akses->IsBatal !!}" />

  <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}" />

  {{-- Filter modal: port 1:1 dari modalFilter milik kreditnote.blade.php. --}}
  <div class="modal fade rt-filter" id="modalFilterPerf">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-funnel"></i>
            Filter Data
            <span class="rt-active-badge" id="perfFilterBadge">0 aktif</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPerf').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-group-label">Status</div>
            <div>
              <label class="rt-field-label" for="input_filterperf">Status Otorisasi</label>
              <select class="rt-native" id="input_filterperf">
                <option value=0 selected>Semua</option>
                <option value=1>Belum Otorisasi</option>
                <option value=2>Sudah Otorisasi</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="rt-reset-link" onclick="perfResetFilterFields()">Reset semua</button>
          <div class="rt-footer-buttons">
            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
              onclick="$('#modalFilterPerf').modal('hide')">Batal</button>
            <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterPerf(); $('#modalFilterPerf').modal('hide');">Terapkan</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" onchange="onChangePeriodePerf()" class="po-filter-inp" id="input_tanggalawal_perf" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodePerf()" class="po-filter-inp" id="input_tanggalakhir_perf" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="perfSearch1" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap"><label for="perfLen1">Tampilkan</label>
          <select id="perfLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
        </div>
        <button class="po-btn-filter" type="button" onclick="$('#modalFilterPerf').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
      </div>
      <div id="rtBarTabel"></div>
      <div class="po-table-wrap">
        <table id="tabel" class="data-table">
          <thead style="white-space:nowrap;"></thead>
          <tbody id="tabel_data" class="text-left" ></tbody>
        </table>
      </div>
      <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
    </div>
  </div>

</div>

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div id="formBsGrid" class="modal-dialog modal-xl modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_add_nobukti" placeholder="" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Tanggal</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="date" class="form-control text-center" id="input_add_tanggal"  value="{!! date('Y-m-d') !!}" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Customer</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_customer" placeholder="" disabled>
                </div>
              </div>
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">User Oto 1</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control" id="input_add_useroto" placeholder="" disabled>
                </div>
              </div>






            </div>



            <div class="row" style="margin-top: -10px">
              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Subtotal</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_subtotal" value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">PPN</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
                </div>
              </div>

            </div>
              <div class="row" style="margin-top: -10px">


              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">DPP</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_dpp"  value="0.00" disabled>
                </div>
              </div>

              <div class="col-2 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Grand Total</label>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
                </div>
              </div>
            </div>










            <div class="container-fluid mt-4" style="overflow: auto; padding:0; margin:0; width:100%;">
              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
              <!-- <div class="row" > -->

                    <table id="tabelDetailAdd" class="data-table"  >
                      <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                      <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                      <th style="padding: 4px 12px;" scope="col">Part Number</th>

                      <th style="padding: 4px 12px;" scope="col">Merk</th>
                      <th style="padding: 4px 12px;" scope="col">Qty</th>
                      <th style="padding: 4px 12px;" scope="col">Sat</th>
                      <th style="padding: 4px 12px;" scope="col">Harga</th>
                      <th style="padding: 4px 12px;" scope="col">Diskon</th>
                      <th style="padding: 4px 12px;" scope="col">Subtotal</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl Beli Akhir</th>
                      <th style="padding: 4px 12px;" scope="col">Tambah PO ?</th>
                      <th style="padding: 4px 12px;" scope="col">No Serah Sample</th>

                    </tr>
                  </thead>


                  <tbody id="tabel_data_add" class="text-left" >

                    <tr >

                      <td>asd</td>
                      <td></td>


                        <td class="text-center">
                          <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
                          <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
                          <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
                          <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
                        </td>
                  </tr>
                  </tbody>


                </table>
              <!-- </div> -->
                <!-- <button onclick="buttonSubKategori()">tes</button> -->


        </div>


    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal" >Batal</button>
    <button id="buttonSubmitAdd" type="button" class="btn btn-chip-biru" onclick="submitAdd()">Otorisasi</button>
    <button id="buttonSubmitBatalOto" type="button" class="btn btn-danger" onclick="submitBatalOto()">Batal Otorisasi</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->





@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let tempNoBukti = ''

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari pola kreditnote.blade.php -- satu tabel (tab lama digabung
 * jadi filter Status Otorisasi di modalFilterPerf).
 */
let perfCart = []
const PERF_HREF = 'performance'
const PERF_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const PERF_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function perfPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function perfDefaultCart () {
  return [
    ['NoBukti',    'No. Bukti',      1, 'varchar', 0, 0],
    ['Tanggal',    'Tanggal',        1, 'date',    0, 0],
    ['NamaCust',   'Nama Pelanggan', 1, 'varchar', 0, 0],
    ['userOtoPerf','User Oto Perf',  1, 'varchar', 0, 0],
    ['tglOtoPerf', 'Tgl Oto Perf',   1, 'date',    0, 0],
    ['NAMASLS',    'Sales',          1, 'varchar', 0, 0],
    ['NAMAPIC',    'PIC',            1, 'varchar', 0, 0],
    ['NoPesanan',  'Po. Cust',       1, 'varchar', 0, 0],
    ['DP',         'DP',             1, 'float',   0, 2],
    ['TotDPP',     'DPP',            1, 'float',   0, 2],
    ['TotPPN',     'PPN',            1, 'float',   0, 2],
    ['TotNet',     'Grandtotal',     1, 'float',   0, 2],
    ['OtoUser1',   'User Oto1',      1, 'varchar', 0, 0],
    ['TglOto1',    'Tgl Oto1',       1, 'date',    0, 0],
    ['IsBatal',    'Batal',          1, 'bool',    0, 0],
    ['userbatal',  'User Batal',     1, 'varchar', 0, 0],
    ['Tglbatal',   'Tgl Batal',      1, 'date',    0, 0],
    ['catatan',    'Keterangan',     1, 'varchar', 0, 0],
  ]
}

function perfBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, PERF_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

window.g_href = PERF_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = perfCart || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(PERF_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: PERF_HREF, urut: 1
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: PERF_HREF, urut: 1, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        perfCart = perfBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        perfCart = perfDefaultCart()
        window.gcart_header = perfCart
        window.doSimpanHeader()
      }
      window.gcart_header = perfCart
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      perfCart = perfDefaultCart()
      window.gcart_header = perfCart
    }
  })
}

let perfRtSudahInit = false
function perfInitReportTableSekali () {
  if (perfRtSudahInit || typeof ReportTable === 'undefined') { return }
  perfRtSudahInit = true
  ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel })
}

function tulisTheadHeaderPerf (tableSel, cols) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols).replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function perfValueCell (row, col) {
  let raw = perfPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw, '/') + '</td>'; }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? parseFloat(raw) : 0;
    if (isNaN(n)) { n = 0; }
    return '<td class="text-right">' + formatAngka(n.toFixed(dp)) + '</td>';
  }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// Digabung dari logika actions lama (tab OtoPerf=0: Detail+Otorisasi, tab
// OtoPerf=1: Detail+Batal Otorisasi+Print) -- persis sama, cuma sekarang satu
// tabel dengan filter Status yang menggantikan pembagian tab.
function tabelActionsCell (row) {
  let nobukti = perfPickCI(row, 'NoBukti');
  let otoPerf = Number(perfPickCI(row, 'OtoPerf'));
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" onclick="buttonAdd(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (otoPerf === 1) {
    html += '<button class="btn btn-danger btn-sm" type="button" onclick="buttonAdd(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-primary btn-sm" type="button" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  } else {
    html += '<button class="btn btn-primary btn-sm" type="button" onclick="submitAdd(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (perfCart.length ? perfCart : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += perfValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderPerf('#tabel', cols);
}

let lastTabelRows = []
let perfPanjangHalaman = 10

function perfIkatSearch () {
  let input = document.getElementById('perfSearch1')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().search(nilai).draw() }
    }, 400)
  })
}

function perfIkatPanjangHalaman () {
  let sel = document.getElementById('perfLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(perfPanjangHalaman)
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    perfPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().page.len(perfPanjangHalaman).draw() }
  })
}

const PERF_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: PERF_DOM_STRING, lengthChange: false, pageLength: perfPanjangHalaman, paging: true, order: [[1, 'asc']], ordering: false });
    perfIkatSearch(); perfIkatPanjangHalaman();
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function perfResetFilterFields () {
  $('#input_filterperf').val('0')
}

function perfUpdateFilterBadge () {
  let n = Number($('#input_filterperf').val()) || 0
  $('#perfFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif')
}

function buttonFilterPerf () {
  loadAll()
  perfUpdateFilterBadge()
}

function onChangePeriodePerf () {
  let tglawal = $('#input_tanggalawal_perf').val()
  let tglakhir = $('#input_tanggalakhir_perf').val()
  if (tglawal && tglakhir && tglawal > tglakhir) {
    alertify.warning('Tanggal awal tidak boleh lebih besar dari tanggal akhir')
    return
  }
  loadAll()
}

function buttonHeaderTable(key) {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    window.doSetHeader(1, true);
    reinitTabel();
    alertify.success('Kolom telah direset ke tampilan default');
  }, function () {});
}

$(document).ready(function(){
      window.doSetHeader(1, false);
      lastTabelRows = @json($tempOutstanding);
      reinitTabel();
      perfInitReportTableSekali();
});

function submitAdd (nobukti) {
  console.log('submitAdd')

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('performancespotoperf') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: nobukti
    },
    success: function(res) {
      console.log(res)
      if (res ==1 ) {
        loadAll()
        alertify.success('Berhasil Oto Perf')
      }
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function submitBatalOto () {
  console.log('submitBatalOto')
  console.log(tempNoBukti)

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('performancespbatalotoperf') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: tempNoBukti
    },
    success: function(res) {
      console.log(res)
      if (res ==1 ) {
        loadAll()
        $("#form").modal('toggle')
        alertify.success('Berhasil Batal Oto Perf')
      }
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}



function buttonAdd (NOBUKTI , tipe) {
  tempNoBukti = ''
  console.log('buttonAdd')
  console.log(NOBUKTI)
  let _token  = $("#_token").val()

  if (tipe == 'add') {
    tempNoBukti = NOBUKTI
    $('#buttonSubmitAdd').show();
    $('#buttonSubmitBatalOto').hide();
  } else if ( tipe == 'edit') {
    tempNoBukti = NOBUKTI
    $('#buttonSubmitAdd').hide();
    $('#buttonSubmitBatalOto').show();

  } else {

    $('#buttonSubmitAdd').hide();
    $('#buttonSubmitBatalOto').hide();

  }

  $.ajax({
    url: "{!! url('performancegetdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      let rowTable = ''
      res.detail.forEach((item, i) => {
        rowTable += `
        <tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td>${item.PartNumber ? item.PartNumber : ''}</td>
          <td>${item.NAMAMERK}</td>
          <td class="text-right">${formatAngka(parseFloat(item.Qnt1x).toFixed(2))}</td>
          <td>${item.SAT1X ? item.SAT1X : ''}</td>
          <td class="text-right">${formatAngka(parseFloat(item.Harga).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(item.DiscTot).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(item.Total).toFixed(2))}</td>
          <td>-</td>
          <td>${item.KetPO}</td>
          <td>-</td>
        </tr>

        `
      });


      // <td class="text-right">${formatAngka(parseFloat(item.Qnt2x).toFixed(2))}</td>
      // <td>${item.SAT2X ? item.SAT1X : ''}</td>
      // <td class="text-right">${formatAngka(parseFloat(item.Qnt3x).toFixed(2))}</td>
      // <td>${item.SAT3X ? item.SAT1X : ''}</td>

      document.getElementById("input_add_nobukti").value = NOBUKTI
      document.getElementById("input_add_customer").value = res.header[0].NamaCust
      document.getElementById("input_add_useroto").value = res.header[0].OtoUser1
      document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal)
      document.getElementById("input_add_subtotal").value = formatAngka(parseFloat(res.header[0].TotSubTotal).toFixed(2))
      document.getElementById("input_add_dpp").value = formatAngka(parseFloat(res.header[0].TotDPP).toFixed(2))
      document.getElementById("input_add_ppn").value = formatAngka(parseFloat(res.header[0].TotPPN).toFixed(2))
      document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(res.header[0].TotNet).toFixed(2))


      document.getElementById("tabel_data_add").innerHTML = rowTable


      $("#form").modal('toggle')


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}


function loadAll () {
  let tglawal = $('#input_tanggalawal_perf').val()
  let tglakhir = $('#input_tanggalakhir_perf').val()
  let filterperf = $('#input_filterperf').val()
  $.ajax({
    url: "{!! url('performanceloadall') !!}",
    type: "get",
    async: false,
    data: { tglawal, tglakhir, filterperf },
    success: function(res) {
      lastTabelRows = res.tempOutstanding
      reinitTabel()
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('performancedetailCetak') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {
        console.log(res)

        dataPrint = res
        console.log(res[0])
        console.log(res[0][0])
        
        // console.log(res[0][0].IsOtorisasi1)
      }
    })
    
    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0].split('-').reverse().join('/');
    let userLogin = "{{ auth()->user()->username }}"
    let now = new Date();
    let tanggalCetak = String(now.getDate()).padStart(2, '0') + '/' + String(now.getMonth() + 1).padStart(2, '0') + '/' + now.getFullYear();

    let jamCetak = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');

    css = `<style type="text/css">
      body {
        font-family: sans-serif;
        font-size: 11px !important;
      }

      table {
        margin: 20px auto;
        border-collapse: collapse;
      }

      table th,
      table td {
        border: 1px solid #3c3c3c;
        height: 24px;
        padding: 1px 5px 0px;
        overflow: hidden;
      }

      a {
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
      }

      .ttd-place {
        height: 80px;
        text-align: center;
      }

      #ttd {
        width: 1000px;
        border: none;
      }

      .ttd-header {
        padding-top: 40px;
      }

      .body-main-print {
        padding: 1rem;
        padding-top: 1rem;

      }

      .header-ba {
        margin-bottom: 2rem;
        text-decoration: underline;
        margin-top: 2rem;
      }

      .detail-spb-table {
        margin: 0;
      }

      .no-border {
        border: none;
      }

      .detail-ba-div {
      }

      .vertical-align-baseline {
        vertical-align: baseline;
      }

      .mt-2rem {
        margin-top: 2rem;
      }

      .mb-3 {
        margin-bottom: 0.5rem;
      }

      .fw-bold {
        font-weight: bold;
      }

      .mb-1 {
        margin-bottom: 0.25rem;
      }

      .mb-2 {
        margin-bottom: 0.5rem;
      }

      .mb-3 {
        margin-bottom: 1rem;
      }

      .mb-4 {
        margin-bottom: 1.5rem;
      }

      .mb-5 {
        margin-bottom: 3rem;
      }

      .mt-1 {
        margin-top: 0.25rem;
      }

      .mt-2 {
        margin-top: 0.5rem;
      }

      .mt-3 {
        margin-top: 1rem;
      }

      .mt-4 {
        margin-top: 1.5rem;
      }

      .mt-5 {
        margin-top: 3rem;
      }

      .ms-1 {
        margin-left: 0.25rem;
      }

      .ms-2 {
        margin-left: 0.5rem;
      }

      .ms-3 {
        margin-left: 1rem;
      }

      .ms-4 {
        margin-left: 1.5rem;
      }

      .ms-5 {
        margin-left: 3rem;
      }

      .me-1 {
        margin-right: 0.25rem;
      }

      .me-2 {
        margin-right: 0.5rem;
      }

      .me-3 {
        margin-right: 1rem;
      }

      .me-4 {
        margin-right: 1.5rem;
      }

      .me-5 {
        margin-right: 3rem;
      }

      .my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
      }

      .my-2 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .my-3 {
        margin-top: 1rem;
        margin-bottom: 1rem;
      }

      .my-4 {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .my-5 {
        margin-top: 3rem;
        margin-bottom: 3rem;
      }

      .pb-1 {
        padding-bottom: 0.25rem;
      }

      .pb-2 {
        padding-bottom: 0.5rem;
      }

      .pb-3 {
        padding-bottom: 1rem;
      }

      .pb-4 {
        padding-bottom: 1.5rem;
      }

      .pb-5 {
        padding-bottom: 3rem;
      }

      .pt-1 {
        padding-top: 0.25rem;
      }

      .pt-2 {
        padding-top: 0.5rem;
      }

      .pt-3 {
        padding-top: 1rem;
      }

      .pt-4 {
        padding-top: 1.5rem;
      }

      .pt-5 {
        padding-top: 3rem;
      }

      .ps-0 {
        padding-left: 0;
      }

      .ps-1 {
        padding-left: 0.25rem;
      }

      .ps-2 {
        padding-left: 0.5rem;
      }

      .ps-3 {
        padding-left: 1rem;
      }

      .ps-4 {
        padding-left: 1.5rem;
      }

      .ps-5 {
        padding-left: 3rem;
      }

      .pe-1 {
        padding-right: 0.25rem;
      }

      .pe-2 {
        padding-right: 0.5rem;
      }

      .pe-3 {
        padding-right: 1rem;
      }

      .pe-4 {
        padding-right: 1.5rem;
      }

      .pe-5 {
        padding-right: 3rem;
      }

      .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }

      .py-1-5 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
      }

      .py-3 {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }

      .py-4 {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
      }

      .py-5 {
        padding-top: 3rem;
        padding-bottom: 3rem;
      }

      .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
      }

      .px-1-5 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
      }

      .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      .px-4 {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }

      .px-5 {
        padding-left: 3rem;
        padding-right: 3rem;
      }

      .text-left {
        text-align: left;
      }

      .text-center {
        text-align: center;
      }

      .text-right {
        text-align: right;
      }

      .text-decoration-underline {
        text-decoration: underline;
      }

      ul {
        margin: 0;
        padding-left: 10px;
      }

      .note {
        width: 75%;
      }

      .w-15 {
        width: 16%;
      }

      .w-25 {
        width: 30%;
      }

      .w-10 {
        width: 4%;
      }

      .w-1 {
        width: 1%;
      }

      .m-0 {
        margin: 0;
      }

      .body-main-prints {
        width: 21cm;
        height: 13.5cm;
        position: relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        width: 100%;
        bottom: 5px;
        margin-top: 10px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
        hdr = `<div class="" style="display: flex; width: 100%">
              <div class="pe-1" style="width: 50%">
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 15%; margin-top: 15px">
                    `+ imageContent +`
                  </div>
                  <div class="pb-1 ps-3" style="width: 95%; margin-left: 15px;">
                    <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                    <p class="m-0" style="line-height:1.4; white-space: nowrap;">
                      JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS<br>
                      CENTRE BLOK D NO.18 RT. 022 SIMPANG PASIR PALARAN<br>
                      KOTA SAMARINDA KALIMANTAN TIMUR
                    </p>
                  </div>
                </div>
                <div style="display:flex; margin-top: 15px;">
                  <div style="width:80px;">Customer</div>
                      <div style="flex:1;">
                        <div>${dataPrint[0].NamaCUST ?? '-'}</div>
                          <div style="margin-top:4px;">
                              ${dataPrint[0].Alamat ?? '-'}
                          </div>
                        </div>
                      </div>
                    </div>             


              <div style="width: 40%; margin-left: 90px;">
                <div style="display: flex; width: 100%">
                  <h2 class="m-0 pb-2">PERFORMA INVOICE</h2>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 30%">No Invoice</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 68%">`+dataPrint[0].NoBukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 30%">Tanggal</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 68%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 30%">No PO Customer</div>
                  <div class="pb-1" style="width: 2%">:</div>
                  <div class="pb-1" style="width: 68%">${dataPrint[0].NoPO ?? '-'}</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 0%"></div>
                </div>
              </div>
              <div
                style="
                  height: 80px;
                  overflow: hidden;">`+printContent+`
              </div>
            </div>

        <table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%; border-left: none; border-right: none;">No.</td>
                    <td rowspan="2" class="text-center" style="width: 15%; border-left: none; border-right: none;">KODE BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 40%; border-left: none; border-right: none;">NAMA BARANG</td>
                    <td rowspan="2" class="text-center" style="width: 10%; border-left: none; border-right: none;">QTY</td>
                    <td rowspan="2" class="text-center" style="width: 10%; border-left: none; border-right: none;">SAT</td>
                    <td rowspan="2" class="text-center" style="width: 10%; border-left: none; border-right: none;">HARGA JUAL</td>
                    <td rowspan="2" class="text-center" style="width: 15%; border-left: none; border-right: none;">TOTAL</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.Jumlah) {
        grandTotalJumlah += Number(item.Jumlah) || 0;
      }

    });
    // end
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>

    <body onload="window.print()">
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        if (i == 0) {

          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; margin-top:5px">`
        // } else if ( i < 1) {
        //   tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px; padding-top:15px; page-break-before: always">`
        } else {
          tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; margin-left: 7px;padding-top:7px; ">`
        }
        tempPrintStr += hdr
        item.forEach((itemSub, j) => {
          tempPrintStr += ``



         tempPrintStr += `
         <tr>
         <td class="text-align: center"
               style="width: 1%; border-bottom: none; border-top: none; border-left: none; border-right: none;">${z+1}</td>
         <td class="text-align: left"
               style="width: 15%; border-bottom: none; border-top: none; border-left: none; border-right: none;">${itemSub.KodeBrg ?? ''}</td>
         <td class="text-align: left"
               style="width: 40%; border-bottom: none; border-top: none; border-left: none; border-right: none;">${itemSub.namabrg ?? ''}</td>
         <td class="text-center"
               style="width: 10%; border-bottom: none; border-top: none; border-left: none; border-right: none;">${itemSub.Qnt ? parseFloat(itemSub.Qnt).toFixed(2) : ''}</td>
         <td class="text-center"
               style="width: 10%; border-bottom: none; border-top: none; border-left: none; border-right: none;">${itemSub.Satuan ?? ''}</td>
         <td style="width: 10%; text-align: right; border-bottom: none; border-top: none; border-left: none; border-right: none;">
            ${itemSub.harga
              ? Number(itemSub.harga).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
          <td style="width: 15%; text-align: right; border-bottom: none; border-top: none; border-left: none; border-right: none;">
            ${itemSub.Jumlah
              ? Number(itemSub.Jumlah).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;">&nbsp;</td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
            <td style="border-bottom: none; border-top: none; border-left: none; border-right: none;"></td>
          </tr>`;
        }

        // total berada di paling bawah
        console.log(i, arrayDataPrint.length)
        if(i == arrayDataPrint.length - 1){
          
        tempPrintStr += `
        <tr>
          <td colspan="5" style="border:1px solid; padding:5px; font-weight:bold; border-bottom: none; border-left: none; border-right: none;">
          </td>
          <td style="border:1px solid; text-align:left; font-weight:bold; border-bottom: none; border-left: none; border-right: none;">
            SUB TOTAL
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold; border-bottom: none; border-left: none; border-right: none;">
            ${Number(dataPrint[0].TJumlah || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>
        <!-- DISKON -->
        <tr>
          <td colspan="5" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold; border-top: none; border-left: none; border-right: none;">
            DISKON
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold; border-top: none; border-left: none; border-right: none;">
            ${Number(dataPrint[0].Tdiskon || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>

        <!-- DPP -->
        <tr>
          <td colspan="5" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold; border-top: none; border-left: none; border-right: none; border-bottom: none;">
            DPP
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold; border-top: none; border-left: none; border-right: none; border-bottom: none;">
            ${Number(dataPrint[0].TNDPPRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>
        <!-- PPN -->
        <tr>
          <td colspan="5" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold; border-top: none; border-left: none; border-right: none;">
            PPN
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold; border-top: none; border-left: none; border-right: none;">
            ${Number(dataPrint[0].TNPPNRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>
        <!-- TOTAL -->
        <tr>
          <td colspan="5" style="border:none;"></td>
          <td style="border:1px solid; text-align:left; font-weight:bold; border-top: none; border-left: none; border-right: none;">
            TOTAL
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold; border-top: none; border-left: none; border-right: none;">
            ${Number(dataPrint[0].TNNETRp || 0).toLocaleString('id-ID', {minimumFractionDigits:2})}
          </td>
        </tr>`};
        // end

         tempPrintStr += `</tbody>`;

         tempPrintStr += `</table>

         <div class="footer-sign font-family: sans-serif;
           font-size: 10px ">

         <div class="row mt-3" style="text-align: left;font-family: sans-serif;
         font-size: 12px ">
         <span style="float: left; display: block; clear: left;">
         </span>
          

         <div style="width:100%; display:flex; font-weight:bold; margin-top:5px;">

          </div>

         </div>


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">

         <!-- KIRI -->
          <div style="width:50%; font-size:10px; margin-top: -35px;">
            <p class="m-0">TRANSFER : </p>
            <p class="m-0">CV. SINAR MAHAKAM LESTARI <br>
              PT. BANK DANAMON INDONESIA Tbk. Cabang Banjarmasin <br>
              AC NO : 003646465454 <br></p>
            <p class="m-0"></p>
          </div>

          <!-- KANAN -->
          <div style="width:70%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: -40px; margin-left:20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%">Disetujui Oleh,</td>
               <td class="no-border text-center" style="width: 20%"></td>
             </tr>
             <tr style="height: 4.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2" style="text-align:center;">
                <p class="m-0" style="display:inline-block; width:120px;">(ISKANDAR)</p>     
               </td>
               <td class="no-border px-2">
               </td>
             </tr>
           </table>
          </div>
        </div>

         </div>


         <div class="footer-print-date">
          <table class="m-0" style="width: 100% ; font-family: sans-serif;
          font-size: 10px; margin-top:-40px;">
            <tr>
              <td class="no-border">${i+1}/${arrayDataPrint.length}        `+userLogin+`          `+tanggalCetak+`      `+jamCetak+`</td>
            </tr>
          </table>
        </div>`

        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`



    w=window.open(' ')
    w.document.write(tempPrintStr)

    w.print()
    w.close()

  }

function formatAngka (angkaString) {


  console.log('formatAngkaMaster' , angkaString);
  let tempAngka = angkaString.split('.')


  tempAngka[0] = tempAngka[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return tempAngka.join(".");

}


function formatDate(date , pemisah = '-') {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join(pemisah);
}




</script>

@endsection
