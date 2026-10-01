@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Closing SO')
@section('title', 'SML - Closing SO')

@endsection

{{-- Rerouted to match Purchase Order's UI 1:1 via so.blade.php's own pattern,
     same as invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual/
     returpenjualangudang/kreditnote/notareturpenjualan before it. Only
     layout/toolbar/column-header interactivity changed -- business logic
     (buttonAdd/buttonAddAll/buttonOpenSO/buttonOpenAllSO, submitAdd/
     submitAddAll/submitOpenSO/submitOpenAllSO) is untouched.
     Periode filter added later (tanggalawal/tanggalakhir, same widget as
     so.blade.php's own): shown once on the "Outstanding SO" toolbar, same as
     so.blade.php only shows it on its "SO" tab, but drives both #tabel and
     #tabel2 together via loadAll() -- ClosingSOController's Outstanding SO
     and Closing SO queries both got a new `and A.Tanggal between :tglawal
     and :tglakhir` clause (additive, alongside their existing outstanding
     qty > 0 / isbatal=1 filters), defaulting to the current periode's month
     range when no range is sent. --}}
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

/* {{-- Kolom Aksi tabel/tabel2 -- pastel round-button treatment, copied verbatim
     from so.blade.php's @section('css'). Both tabel dan tabel2 punya Actions
     di kolom pertama (sesuai markup lama). --}} */
#tabel td:first-child, #tabel2 td:first-child {
  display: flex; gap: 4px; justify-content: center; align-items: center;
}
#tabel td:first-child .btn, #tabel2 td:first-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
  box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover, #tabel2 td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-success, #tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-warning, #tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel td:first-child .btn-primary, #tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel td:first-child .btn-danger, #tabel2 td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

#tabel thead th, #tabel2 thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd), #tabel2 tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover, #tabel2 tbody tr:hover { background-color: #f5f3ff; }

/* Hide action buttons until the row is hovered/focused, port 1:1 dari pola
   .action-buttons-wrap milik master (public/css/tableMaster2.css). */
#tabel tbody .action-buttons-wrap, #tabel2 tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap, #tabel2 tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap, #tabel2 tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

.data-table th,
.data-table td {
  white-space: nowrap;
}
</style>
@endsection

@section('content')
<div id="page1" class="container-fluid mainpage">

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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Outstanding SO</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">Closing SO</a>
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
                    <input type="date" onchange="onChangePeriodeCSO()" class="po-filter-inp" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeCSO()" class="po-filter-inp" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
                  </div>
                  <input type="search" id="csoSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="csoLen1">Tampilkan</label>
                    <select id="csoLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                  </div>
                </div>
                <div id="rtBarTabel"></div>
                <table id="tabel" class="data-table">
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
                    <input type="date" onchange="onChangePeriodeCSO2()" class="po-filter-inp" id="input_tanggalawal2" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
                    <span class="po-filter-sep">s/d</span>
                    <input type="date" onchange="onChangePeriodeCSO2()" class="po-filter-inp" id="input_tanggalakhir2" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
                  </div>
                  <input type="search" id="csoSearch2" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="csoLen2">Tampilkan</label>
                    <select id="csoLen2" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
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

      </div>
    </div>
  </div>

</div>
</div>

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Close Outstanding SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <input type="hidden" name="noUrut" id="input_add_noUrut" value="" />

            <div class="row">
              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_add_nobukti" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">KODE BRG</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_add_kodebrg" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_add_keterangan" placeholder="">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitAdd()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal add -->
<div class="modal fade" id="formAll" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Close Outstanding SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">

            <div class="row">
              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_addall_nobukti" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_addall_keterangan" placeholder="">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitAddAll(1)">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal add -->
<div class="modal fade" id="formOpen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Open Outstanding SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">

            <div class="row">
              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_open_nobukti" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">KODE BRG</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_open_kodebrg" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_open_keterangan" placeholder="">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitOpenSO()">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

<!-- start modal add -->
<div class="modal fade" id="formOpenAll" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"  role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Open Outstanding SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">

            <div class="row">
              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">NOBUKTI</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_openall_nobukti" placeholder="" disabled>
                </div>
              </div>

              <div class="col-12 text-left">
                <div class="form-group text-left">
                  <label class="text-left">Keterangan</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <input type="text" class="form-control " id="input_openall_keterangan" placeholder="">
                </div>
              </div>

            </div>

    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal" >Batal</button>
    <button type="button" class="btn btn-primary" onclick="submitOpenAllSO(1)">Submit</button>
  </div>
</div>
</div>
</div>
<!-- End modal add-->

@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let tempAdd = {}

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php, sama
 * seperti so/invoicejasa/fakturpajak/cetaktandaterima/perintahreturjual/
 * returpenjualangudang/kreditnote/notareturpenjualan. Dua tabel real (bukan
 * status gabungan) -- tabel (urut 1) = Outstanding SO, tabel2 (urut 2) =
 * Closing SO -- masing-masing dengan Actions kolom pertama, port 1:1 dari
 * markup lama.
 */
let csoCart = { 1 : [], 2 : [] }
let csoActiveUrut = 0
const CSO_HREF = 'closingso'
const CSO_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const CSO_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
let csoPerluGambar = { 1 : false, 2 : false }

function csoPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function csoDefaultCart (urut) {
  if (urut === 1) {
    return [
      ['NOBUKTI',    'No. Bukti',       1, 'varchar', 0, 0],
      ['Tanggal',    'Tanggal',         1, 'date',    0, 0],
      ['CXcust',     'Nama Pelanggan',  1, 'varchar', 0, 0],
      ['KODEBRG',    'Kode Barang',     1, 'varchar', 0, 0],
      ['PartNumber', 'Part Number',     1, 'varchar', 0, 0],
      ['NamaMerk',   'Merk',            1, 'varchar', 0, 0],
      ['NamaBrg',    'Nama Barang',     1, 'varchar', 0, 0],
      ['QntOut',     'Qty Out',         1, 'float',   0, 2],
      ['SATUAN',     'SAT',             1, 'varchar', 0, 0],
      ['DUEDATE',    'Due Date',        1, 'date',    0, 0],
      ['UserID',     'User ID',         1, 'varchar', 0, 0],
      ['xcKEBUN',    'Lokasi Penerima', 1, 'varchar', 0, 0],
    ]
  }
  // urut 2: Closing SO -- kolom User Close/Tgl Close/Ket. Close tambahan
  // dibanding Outstanding SO, port 1:1 dari markup lama.
  return [
    ['NOBUKTI',    'No. Bukti',       1, 'varchar', 0, 0],
    ['Tanggal',    'Tanggal',         1, 'date',    0, 0],
    ['UserBatal',  'User Close',      1, 'varchar', 0, 0],
    ['TglBatal',   'Tgl Close',       1, 'date',    0, 0],
    ['Ketbatal',   'Ket. Close',      1, 'varchar', 0, 0],
    ['CXcust',     'Nama Pelanggan',  1, 'varchar', 0, 0],
    ['KODEBRG',    'Kode Barang',     1, 'varchar', 0, 0],
    ['PartNumber', 'Part Number',     1, 'varchar', 0, 0],
    ['NamaMerk',   'Merk',            1, 'varchar', 0, 0],
    ['NamaBrg',    'Nama Barang',     1, 'varchar', 0, 0],
    ['Qntbatal',   'Qty',             1, 'float',   0, 2],
    ['SATUAN',     'SAT',             1, 'varchar', 0, 0],
    ['DUEDATE',    'Due Date',        1, 'date',    0, 0],
    ['UserID',     'User ID',         1, 'varchar', 0, 0],
    ['xcKEBUN',    'Lokasi Penerima', 1, 'varchar', 0, 0],
  ]
}

function csoBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, CSO_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function csoAktifkanTabel (urut) {
  csoActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = csoCart[urut]
}

function csoOnChangeAktif () {
  if (csoActiveUrut === 2) { reinitTabel2(); } else { reinitTabel(); }
}

window.g_href = CSO_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = csoCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(CSO_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: CSO_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: CSO_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        csoCart[urut] = csoBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        csoCart[urut] = csoDefaultCart(urut)
        window.gcart_header = csoCart[urut]
        window.doSimpanHeader(CSO_HREF, urut)
      }
      window.gcart_header = csoCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      csoCart[urut] = csoDefaultCart(urut)
      window.gcart_header = csoCart[urut]
    }
  })
}

function activeVisibleTabKeyCSO () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2; }
  return 1;
}

const CSO_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const CSO_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let csoRtSudahInit = false
function csoInitReportTableSekali () {
  if (csoRtSudahInit || typeof ReportTable === 'undefined') { return }
  csoRtSudahInit = true
  let urutAktif = activeVisibleTabKeyCSO()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  let idBar = { 1 : '#rtBarTabel', 2 : '#rtBarTabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({ table: idTabel[u], bar: idBar[u], onChange: csoOnChangeAktif })
  });
  ReportTable.init({ table: CSO_SELEKTOR_TABEL_AKTIF, bar: CSO_SELEKTOR_BAR_AKTIF, onChange: csoOnChangeAktif })

  let csoGuardUlangKlik = false;
  ['#tabel', '#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (csoGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      csoGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      csoGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderCSO (tableSel, cols, withActions) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
  if (withActions) { headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>'); }
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function csoValueCell (row, col) {
  let raw = csoPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw) + '</td>'; }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + n.toLocaleString('id-ID', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// Port 1:1 dari tombol Close Item/Close NoBukti milik markup lama.
function tabelActionsCell (row) {
  let nobukti = csoPickCI(row, 'NOBUKTI');
  let kodebrg = csoPickCI(row, 'KODEBRG');
  let urut = csoPickCI(row, 'URUT');
  let qntout = csoPickCI(row, 'QntOut');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-primary btn-sm" type="button" title="Close Item" onclick="buttonAdd(\'' + nobukti + '\' , \'' + kodebrg + '\' , \'' + urut + '\' , \'' + qntout + '\')"><i class="bi bi-lock-fill"></i></button>';
  html += '<button class="btn btn-success btn-sm" type="button" title="Close NoBukti" onclick="buttonAddAll(\'' + nobukti + '\' , \'' + kodebrg + '\' , \'' + urut + '\' , \'' + qntout + '\')"><i class="bi bi-lock-fill"></i></button>';
  html += '</div></td>';
  return html;
}

// Port 1:1 dari tombol Open Item/Open NoBukti milik markup lama.
function tabel2ActionsCell (row) {
  let nobukti = csoPickCI(row, 'NOBUKTI');
  let kodebrg = csoPickCI(row, 'KODEBRG');
  let urut = csoPickCI(row, 'URUT');
  let qntout = csoPickCI(row, 'QntOut');
  let html = '<td class="text-center"><div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Open Item" onclick="buttonOpenSO(\'' + nobukti + '\' , \'' + kodebrg + '\' , \'' + urut + '\' , \'' + qntout + '\')"><i class="bi bi-unlock"></i></button>';
  html += '<button class="btn btn-danger btn-sm" type="button" title="Open NoBukti" onclick="buttonOpenAllSO(\'' + nobukti + '\' , \'' + kodebrg + '\' , \'' + urut + '\' , \'' + qntout + '\')"><i class="bi bi-unlock-fill"></i></button>';
  html += '</div></td>';
  return html;
}

function renderTabelRows (rows) {
  let cols = (csoCart[1].length ? csoCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabelActionsCell(row);
    cols.forEach(function (col) { html += csoValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel_data').innerHTML = html;
  tulisTheadHeaderCSO('#tabel', cols, true);
}

function renderTabel2Rows (rows) {
  let cols = (csoCart[2].length ? csoCart[2] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += csoValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderCSO('#tabel2', cols, true);
}

let lastTabelRows = []
let lastTabel2Rows = []
let csoPanjangHalaman = { 1 : 10, 2 : 10 }

function csoIkatSearch (urut) {
  let ids = { 1 : ['csoSearch1', 'tabel'], 2 : ['csoSearch2', 'tabel2'] }
  let input = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
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

function csoIkatPanjangHalaman (urut) {
  let ids = { 1 : ['csoLen1', 'tabel'], 2 : ['csoLen2', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(csoPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    csoPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(csoPanjangHalaman[urut]).draw() }
  })
}

const CSO_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().destroy(); }
    renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({ dom: CSO_DOM_STRING, lengthChange: false, pageLength: csoPanjangHalaman[1], paging: true, order: [[1, 'asc']], ordering: false });
    csoIkatSearch(1); csoIkatPanjangHalaman(1); csoPerluGambar[1] = false;
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable({ dom: CSO_DOM_STRING, lengthChange: false, pageLength: csoPanjangHalaman[2], paging: true, order: [[1, 'asc']], ordering: false });
    csoIkatSearch(2); csoIkatPanjangHalaman(2); csoPerluGambar[2] = false;
  } catch (e) { console.error('reinitTabel2 failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

$(document).ready(function(){
  csoAktifkanTabel(1); window.doSetHeader(1, false);
  lastTabelRows = @json($tempOutstanding);
  reinitTabel();

  csoAktifkanTabel(2); window.doSetHeader(2, false);
  lastTabel2Rows = @json($tempOutstanding2);
  reinitTabel2();

  csoInitReportTableSekali();

  $('#nav-home-tab').on('shown.bs.tab', function () { csoAktifkanTabel(1); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (csoPerluGambar[1]) { reinitTabel(); } });
  $('#nav-profile-tab').on('shown.bs.tab', function () { csoAktifkanTabel(2); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (csoPerluGambar[2]) { reinitTabel2(); } });
});

function submitAddAll (all = 0) {

  console.log(tempAdd)
  let keterangan = $("#input_addall_keterangan").val()
  console.log(keterangan)

 if (!keterangan) {
    alertify.warning("Keterangan closing harus diisi")

  } else {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('closingsospclosingso') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: tempAdd.NOBUKTI,
      qntout: tempAdd.qntout,
      urut: tempAdd.urut,
      kodebrg: tempAdd.kodebrg,
      keterangan,
      all : 1

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {
        loadAll()
          $("#formAll").modal('toggle')
          alertify.success("Berhasil closing SO")
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

}

function submitAdd (all = 0) {

  console.log(tempAdd)
  let keterangan = $("#input_add_keterangan").val()
  console.log(keterangan)

 if (!keterangan) {
    alertify.warning("Keterangan closing harus diisi")

  } else {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('closingsospclosingso') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: tempAdd.NOBUKTI,
      qntout: tempAdd.qntout,
      urut: tempAdd.urut,
      kodebrg: tempAdd.kodebrg,
      keterangan,
      all: 0

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {
        loadAll()
          $("#form").modal('toggle')
          alertify.success("Berhasil closing SO")
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

}


function submitOpenAllSO (all = 0) {

  console.log(tempAdd)
  let keterangan = $("#input_openall_keterangan").val()
  console.log(keterangan)

 if (!keterangan) {
    alertify.warning("Keterangan closing harus diisi")

  } else {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('closingsospopenso') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: tempAdd.NOBUKTI,
      qntout: tempAdd.qntout,
      urut: tempAdd.urut,
      kodebrg: tempAdd.kodebrg,
      keterangan,
      all: 1

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {
        loadAll()
          $("#formOpenAll").modal('toggle')
          alertify.success("Berhasil batal closing SO")
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

}


function submitOpenSO (all = 0) {

  console.log(tempAdd)
  let keterangan = $("#input_open_keterangan").val()
  console.log(keterangan)
  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('closingsospopenso') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      NOBUKTI: tempAdd.NOBUKTI,
      qntout: tempAdd.qntout,
      urut: tempAdd.urut,
      kodebrg: tempAdd.kodebrg,
      keterangan,
      all : 0

    },
    success: function(res) {
      console.log(res)
      if (res == 1) {
        loadAll()
          $("#formOpen").modal('toggle')
          alertify.success("Berhasil batal closing SO")
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

function buttonAdd (NOBUKTI , kodebrg , urut , qntout) {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  tempAdd = {}
  console.log('buttonAdd')
  console.log(NOBUKTI)
  console.log(NOBUKTI , kodebrg , urut , qntout)

  tempAdd = {
    NOBUKTI,
    kodebrg,
    urut,
    qntout
  }
  document.getElementById("input_add_nobukti").value = NOBUKTI
  document.getElementById("input_add_kodebrg").value = kodebrg
  document.getElementById("input_add_keterangan").value = ''
  $("#form").modal('toggle')

  console.log(tempAdd)

}

function buttonAddAll (NOBUKTI , kodebrg , urut , qntout) {

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  tempAdd = {}
  console.log('buttonAdd')
  console.log(NOBUKTI)
  console.log(NOBUKTI , kodebrg , urut , qntout)

  tempAdd = {
    NOBUKTI,
    kodebrg,
    urut,
    qntout
  }
  document.getElementById("input_addall_nobukti").value = NOBUKTI
  document.getElementById("input_addall_keterangan").value = ''
  $("#formAll").modal('toggle')

  console.log(tempAdd)

}

function onChangePeriodeCSO () {
  let awal = $("#input_tanggalawal").val()
  let akhir = $("#input_tanggalakhir").val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  // Kedua tab pakai satu sumber tanggal yang sama (loadAll() memuat #tabel dan
  // #tabel2 sekaligus dari satu ajax) -- disamakan supaya widget periode di tab
  // manapun yang diubah tetap konsisten dilihat dari tab satunya.
  $("#input_tanggalawal2").val(awal)
  $("#input_tanggalakhir2").val(akhir)
  loadAll()
}

function onChangePeriodeCSO2 () {
  let awal = $("#input_tanggalawal2").val()
  let akhir = $("#input_tanggalakhir2").val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  $("#input_tanggalawal").val(awal)
  $("#input_tanggalakhir").val(akhir)
  loadAll()
}

function loadAll () {
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()

  $.ajax({
    url: "{!! url('closingsoloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal,
      tglakhir,
    },
    success: function(res) {
      console.log(res)
      lastTabelRows = res.tempOutstanding;
      lastTabel2Rows = res.tempOutstanding2;
      reinitTabel();
      reinitTabel2();
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}


function buttonOpenAllSO (NOBUKTI , kodebrg , urut , qntout) {

  let akses = $("#akses_isbatal").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  tempAdd = {}
  console.log('buttonAdd')
  console.log(NOBUKTI)
  console.log(NOBUKTI , kodebrg , urut , qntout)

  tempAdd = {
    NOBUKTI,
    kodebrg,
    urut,
    qntout
  }
  document.getElementById("input_openall_nobukti").value = NOBUKTI
  document.getElementById("input_openall_keterangan").value = ''
  $("#formOpenAll").modal('toggle')

}

function buttonOpenSO (NOBUKTI , kodebrg , urut , qntout) {

  let akses = $("#akses_isbatal").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  tempAdd = {}
  console.log('buttonAdd')
  console.log(NOBUKTI)
  console.log(NOBUKTI , kodebrg , urut , qntout)

  tempAdd = {
    NOBUKTI,
    kodebrg,
    urut,
    qntout
  }
  document.getElementById("input_open_nobukti").value = NOBUKTI
  document.getElementById("input_open_kodebrg").value = kodebrg
  document.getElementById("input_open_keterangan").value = ''
  $("#formOpen").modal('toggle')

  console.log(tempAdd)

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
