@extends('newmasterTest')
@section('page-title', 'Closing Purchase Order')

@section('css')
  {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi), disamakan
     dengan resources/views/purchasing/pembelianclosingpr.blade.php. Aturannya di-scope ke
     #tabel/#tabel2/#rtBar - id tabel di halaman ini sudah cocok apa adanya. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
  /* Isi tabel di semua tab dibuat sebaris (tidak turun ke bawah). Kolom yang panjang
     cukup digeser lewat scroll horizontal .po-table-wrap (overflow:auto). */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }
</style>
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
<style>
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }
/* Halaman ini dirancang mengisi tinggi layar (lihat cpoAturTinggiTabel()), jadi padding
   atas #content layout dikecilkan supaya tab tidak menggantung jauh dari header. */
#content { padding-top: 12px; }

/* layout newmasterx punya rule .card global (align-items:center) yang override ini */
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

/* Tab pil biru, disamakan persis dengan pembelianclosingpr.blade.php. */
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

/* Rule .card global di layout newmasterx sebenarnya untuk kartu menu dashboard
   (flex + align-items:center + efek melayang saat hover). Kalau dipakai untuk card berisi
   tabel, card-body tidak melar mengikuti halaman melainkan mengikuti lebar tabel. */
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

/* Tabel di dalam tab bisa punya kolom sangat banyak. Kolom Bootstrap adalah flex item
   yang min-width bawaannya "auto", jadi tabel lebar malah memaksa card dan halaman ikut
   melebar. min-width:0 membuat tabel discroll di dalam card, tidak melewati batas card. */
#page1 .tab-content .col-md-12 {
  min-width: 0;
  max-width: 100%;
}

/* ---------- Kolom Action tabel (#tabel/#tabel2) - tombol bulat kecil ---------- */
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

#tabel td:first-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel2 td:first-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel2 td:first-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

/* Tombol Print - tab Closing PO punya satu tombol lebih banyak dibanding Closing PR. */
#tabel2 td:first-child .btn-info {
  color: #0369a1; border-color: #c5e4f3; background: #e6f4fb;
}

/* ---------- Header & baris tabel - bersih, uppercase abu-abu ---------- */
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

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
   style pada <table>, mengalahkan `.data-table { width: 100% }`. Dipakai min-width, BUKAN
   width, di-scope lewat ID (bukan class). */
#tabel, #tabel2 {
  min-width: 100%;
}

/* Tombol di kolom Action baru muncul saat barisnya di-hover. Opt-in lewat kelas
   po-aksi-hover supaya tabel lain tidak ikut terpengaruh. visibility (bukan display)
   supaya lebar kolomnya tetap dipesan - tabel tidak melompat saat tombol muncul/hilang. Sengaja TIDAK memakai :focus-within: klik mouse membuat tombol tetap fokus sehingga tidak ikut hilang saat kursor sudah pindah. */
table.data-table.po-aksi-hover tbody td:first-child .btn {
  visibility: hidden;
  opacity: 0;
  transition: opacity .12s ease;
}
table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
  visibility: visible;
  opacity: 1;
}

/* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar kedua tab. Bentuknya meniru
   .po-filter-wrap milik public/css/po-table-header.css tapi ditulis di sini supaya
   perubahan ini cukup mengunggah file blade-nya saja. */
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
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
  background-repeat: no-repeat;
  background-position: right center;
}

/* ---- Lapisan "sedang memuat" kedua tabel ----
   Elemennya adalah .dataTables_processing bawaan DataTables, tampilannya ditulis ulang
   di sini. */
#tabel_wrapper,
#tabel2_wrapper {
  position: relative;
}

#tabel_wrapper > .dataTables_processing,
#tabel2_wrapper > .dataTables_processing {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: auto;
  margin: 0;
  padding: 0;
  border: 0;
  background: rgba(255, 255, 255, .62);
  z-index: 40;
  animation: cpoMunculLoading .34s ease-out both;
}

@keyframes cpoMunculLoading {
  0%, 45% { opacity: 0; }
  100% { opacity: 1; }
}

.po-loading-chip {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: inline-flex;
  align-items: center;
  gap: 9px;
  white-space: nowrap;
  padding: 9px 18px;
  border-radius: 999px;
  background: rgba(31, 36, 48, .92);
  color: #fff;
  font-size: 12.5px;
  font-weight: 600;
  box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
}

.po-loading-spin {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, .35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: cpoPutarLoading .6s linear infinite;
}

@keyframes cpoPutarLoading {
  to { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')

{{-- Logo kop surat untuk cetak PO. Dibaca lewat innerHTML oleh submitPrintcopy(), jadi
     harus ada di body (bukan di @section('css') yang di-yield di dalam <head>). --}}
<div id="imagecontainer" class="d-none">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1">
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
          <a class="nav-item nav-link active"
            id="nav-home-tab"
            data-toggle="tab"
            href="#home"
            role="tab"
            aria-controls="home"
            aria-selected="true">
              Outstanding PO
          </a>

          <a class="nav-item nav-link"
            id="nav-profile-tab"
            data-toggle="tab"
            href="#profile"
            role="tab"
            aria-controls="profile"
            aria-selected="false">
              Closing PO
          </a>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">

          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" class="po-filter-inp" id="cpoTglAwal1" value="{!! $cpoTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="cpoTglAkhir1" value="{!! $cpoTglAkhir !!}">
                    </div>
                    <input type="search" id="cpoSearch1" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Nilai -1 = tampilkan semua data - lihat
                         ClosingPOController@dataOutstanding. --}}
                    <div class="po-len-wrap">
                      <label for="cpoLen1">Tampilkan</label>
                      <select id="cpoLen1" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  {{-- Bar kolom tersembunyi + tombol "Reset kolom" (diisi report-table.js).
                       Satu elemen dipakai bersama #tabel dan #tabel2, dipindah lewat JS
                       (cpoPindahBar) saat tab berganti. --}}
                  <div id="rtBar"></div>
                  <table id="tabel" class="data-table po-aksi-hover">
                    <thead id="tabel_header" class="text-center"></thead>
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

          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" class="po-filter-inp" id="cpoTglAwal2" value="{!! $cpoTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="cpoTglAkhir2" value="{!! $cpoTglAkhir !!}">
                    </div>
                    <input type="search" id="cpoSearch2" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="cpoLen2">Tampilkan</label>
                      <select id="cpoLen2" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat cpoPindahBar(). --}}
                  <table id="tabel2" class="data-table po-aksi-hover">
                    <thead id="tabel2_header" class="text-center"></thead>
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

    {{-- Start Modal closing PO --}}
    <div class="modal fade" id="modalLockPO" tabindex="-1" role="dialog" aria-labelledby="modalLockPOLabel">
      <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLockPOLabel">Alasan Penguncian Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="lock_nobukti">
            <input type="hidden" id="lock_mode">
            <input type="hidden" id="lock_urut">
            <p class="mb-2 text-muted" id="lock_info" style="font-size: 0.85rem;"></p>
            <div class="form-group">
              <label for="lock_reason">Masukkan Alasan:</label>
              <textarea class="form-control" id="lock_reason" rows="3" placeholder="" autocomplete="off" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="cpoCloseForm()" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" onclick="cpoSubmitLock()">Kunci</button>
          </div>
        </div>
      </div>
    </div>
    {{-- End Modal closing PO --}}
  </div>
</div>

@endsection

@section('js')
{{-- window.ReportTable: header tabel interaktif (drag kolom, roda gigi, bar kolom
     tersembunyi, tombol "Reset kolom"). File-nya berupa IIFE ber-guard, aman meski
     dimuat lebih dari sekali. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>

<script type="text/javascript">

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Diporting dari pembelianclosingpr.blade.php - dokumentasi lengkap pola ini ada di
 * purchaseOrder.blade.php, komentar di sini hanya menandai bagian yang berbeda.
 */

const CPO_HREF = 'closingpurchaseorder'
const CPO_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date' }

const CPO_TAB = {
  1 : {
    tabel  : 'tabel',
    thead  : 'tabel_header',
    tbody  : 'tabel_data',
    search : 'cpoSearch1',
    len    : 'cpoLen1',
    url    : "{!! url('closingpodataoutstanding') !!}",
    nama   : 'Outstanding PO'
  },
  2 : {
    tabel  : 'tabel2',
    thead  : 'tabel2_header',
    tbody  : 'tabel2_data',
    search : 'cpoSearch2',
    len    : 'cpoLen2',
    url    : "{!! url('closingpodataclosing') !!}",
    nama   : 'Closing PO'
  }
}

let cpoCart = { 1 : [], 2 : [] }
let cpoActiveUrut = 0
let cpoPerluGambar = { 1 : false, 2 : false }
let cpoCacheOut = { 1 : null, 2 : null }
let cpoPakaiCacheOut = { 1 : false, 2 : false }
let cpoPanjangHalaman = { 1 : 10, 2 : 10 }

function cpoUrutTabAktif () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

const CPO_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'

function cpoBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      CPO_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function cpoKolomTampil (urut) {
  return (cpoCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function cpoKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function cpoAktifkanTabel (urut) {
  cpoActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = cpoCart[urut]
}

function cpoOnChangeAktif () {
  initTabelClosingPO(cpoActiveUrut, true)
}

function cpoHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let cpoRtSudahInit = false

function cpoInitReportTableSekali () {
  if (cpoRtSudahInit || typeof ReportTable === 'undefined') { return }
  cpoRtSudahInit = true

  let urutAktif = cpoUrutTabAktif()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({
      table    : idTabel[u],
      onChange : cpoOnChangeAktif
    })
  });

  ReportTable.init({
    table    : CPO_SELEKTOR_TABEL_AKTIF,
    bar      : '#rtBar',
    onChange : cpoOnChangeAktif
  })

  // Guard klik roda gigi vs sort DataTables - lihat catatan lengkap di
  // purchaseOrder.blade.php (poInitReportTableSekali).
  let cpoGuardUlangKlik = false
  let idThead = ['tabel_header', 'tabel2_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (cpoGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      cpoGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      cpoGuardUlangKlik = false
    }, true)
  });
}

function cpoPindahBar (urut) {
  let bar = document.getElementById('rtBar')
  let id = CPO_TAB[urut].tabel
  let tabel = document.getElementById(id)
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#' + id)) {
    acuan = document.getElementById(id + '_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

let cpoTimerSearch = { 1 : null, 2 : null }
function cpoIkatSearch (urut) {
  let input = document.getElementById(CPO_TAB[urut].search)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let nilai = input.value
    if (cpoTimerSearch[urut]) { clearTimeout(cpoTimerSearch[urut]) }
    cpoTimerSearch[urut] = setTimeout(function () {
      $('#' + CPO_TAB[urut].tabel).DataTable().search(nilai).draw()
    }, 400)
  })
}

function cpoIkatPanjangHalaman (urut) {
  let sel = document.getElementById(CPO_TAB[urut].len)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(cpoPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    cpoPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    $('#' + CPO_TAB[urut].tabel).DataTable().page.len(cpoPanjangHalaman[urut]).draw()
  })
}

// Ubah salah satu tanggal periode -> kosongkan cache tab ini lalu reload, supaya
// halaman pertama tidak menampilkan hasil rentang lama (lihat cpoCacheOut/cpoPakaiCacheOut).
function cpoIkatPeriode (urut) {
  let awal  = document.getElementById('cpoTglAwal' + urut)
  let akhir = document.getElementById('cpoTglAkhir' + urut)
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    cpoCacheOut[urut] = null
    cpoPakaiCacheOut[urut] = false
    $('#' + CPO_TAB[urut].tabel).DataTable().ajax.reload()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function cpoAturTinggiTabel () {
  let area = document.getElementById('content')
  let pane = document.querySelector('#myTabContent .tab-pane.active')
  if (!area || !pane) { return }
  let wrap = pane.querySelector('.po-table-wrap')
  if (!wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

window.g_href = CPO_HREF
window.g_modeReport = 1
window.gcart_header = []

function cpoUrutSah (mode) {
  let urut = Number(mode)
  return urut === 2 ? 2 : 1
}

window.doSimpanHeader = function (href, mode) {
  let urut = cpoUrutSah(mode)
  let cart = cpoCart[urut] || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[6])
    value.push(c[7])
    isnumber.push(c[8])
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
      href     : CPO_HREF,
      urut     : urut
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  if (!reset) { return }
  let urut = cpoUrutSah(mode)

  $.ajax({
    url   : "{!! url('closingpoheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      urut   : urut,
      reset  : 1
    },
    success : function (res) {
      cpoCart[urut] = cpoBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = cpoCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function cpoFormatAngkaDes (nilai, des) {
  let d = Number(des)
  if (isNaN(d) || d < 0) { d = 0 }

  let mentah = (nilai === null || nilai === undefined || nilai === '') ? 0 : nilai
  let angka = Number(String(mentah).split(',').join(''))
  if (isNaN(angka)) {
    return (nilai === null || nilai === undefined) ? '' : nilai
  }

  let teks = angka.toFixed(d)
  let minus = teks.charAt(0) === '-'
  if (minus) { teks = teks.substring(1) }

  let bagian = teks.split('.')
  let bulat = bagian[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  return (minus ? '-' : '') + bulat + (bagian[1] ? '.' + bagian[1] : '')
}

function cpoRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return cpoFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Casing nama field berbeda antar tab karena sumber datanya berbeda: vwOutPOBatal memakai
// 'Nobukti'/'urut', sedangkan vwMasterPOOut memakai 'NoBukti'/'Urut'. Lihat CPO_KOLOM_1 dan
// CPO_KOLOM_2 di ClosingPOController.
function cpoAksiHtml (urut, row) {
  if (urut === 1) {
    return `<button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Close Per Barang" onclick="cpoLockItem('${row.Nobukti}', '${row.urut}')"><i class="bi bi-lock-fill"></i></button>
            <button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Close Per No. Bukti" onclick="cpoLockAll('${row.Nobukti}')"><i class="bi bi-lock-fill"></i></button>`
  }
  return `<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Buka Per Barang" onclick="cpoUnlock('${row.NoBukti}', 'item', '${row.Urut}')"><i class="bi bi-unlock"></i></button>
          <button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Buka Per No. Bukti" onclick="cpoUnlock('${row.NoBukti}', 'all')"><i class="bi bi-unlock-fill"></i></button>
          <button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Cetak PO" onclick="submitPrintcopy('${row.NoBukti}')"><i class="bi bi-printer"></i></button>`
}

// Menggambar salah satu tabel: urut 1 = Outstanding PO, urut 2 = Closing PO.
function initTabelClosingPO (urut, pakaiCache) {
  let cfg = CPO_TAB[urut]
  if (!cfg) { return }
  let selTabel = '#' + cfg.tabel
  cpoAktifkanTabel(urut)

  let posisi = null
  if ($.fn.DataTable.isDataTable(selTabel)) {
    let dtLama = $(selTabel).DataTable()
    posisi = {
      start  : dtLama.page.info().start,
      search : dtLama.search(),
      order  : dtLama.order()
    }
    dtLama.destroy()
  }
  document.getElementById(cfg.tbody).innerHTML = ""

  let cols = cpoKolomTampil(urut)
  let kolomRender = cols.map(cpoKolomRender)

  let thead = document.getElementById(cfg.thead)
  thead.innerHTML = cpoHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px; width: 120px;" scope="col">Action</th>')
  }

  let columns = [{
    data : null,
    orderable : false,
    className : 'text-center',
    render : function (data, type, row) {
      return cpoAksiHtml(urut, row)
    }
  }]

  kolomRender.forEach((c) => {
    columns.push({
      data : null,
      className : c.tipe === 1 ? 'text-right' : '',
      render : function (data, type, row) {
        return cpoRenderNilai(c, row)
      }
    })
  });

  cpoPakaiCacheOut[urut] = !!(pakaiCache && cpoCacheOut[urut])

  if (!kolomRender.length) {
    thead.innerHTML = '<tr><th style="padding: 4px 12px;" scope="col">' + cfg.nama + '</th></tr>'
    document.getElementById(cfg.tbody).innerHTML =
      '<tr><td class="text-center" style="padding: 14px;">Belum ada data untuk ditampilkan</td></tr>'
    return
  }

  let orderAman = posisi ? posisi.order.filter((o) => o[0] < columns.length) : []

  $(selTabel).DataTable({
    "processing" : true,
    "language" : {
      "processing" : '<span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>',
      "emptyTable" : 'Belum ada data untuk ditampilkan',
      "zeroRecords" : 'Tidak ada data yang cocok dengan pencarian'
    },
    "serverSide" : true,
    "lengthChange" : false,
    "pageLength" : cpoPanjangHalaman[urut],
    "searchDelay" : 400,
    "dom" : "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "order" : orderAman,
    "displayStart" : posisi ? posisi.start : 0,
    "search" : posisi ? { "search" : posisi.search } : { "search" : "" },
    "columns" : columns,
    "ajax" : function (data, callback, settings) {
      if (cpoPakaiCacheOut[urut] && cpoCacheOut[urut]) {
        cpoPakaiCacheOut[urut] = false
        callback(Object.assign({}, cpoCacheOut[urut], { draw : data.draw }))
        return
      }

      // Kolom pertama tabel adalah kolom Action yang tidak ada di kolomRender, jadi
      // indeks sort dari DataTables harus digeser satu.
      let kolom = null
      let arah = 'asc'
      if (data.order && data.order.length && data.order[0].column >= 1) {
        let c = kolomRender[data.order[0].column - 1]
        if (c) {
          kolom = c.field
          arah = data.order[0].dir
        }
      }

      $.ajax({
        url : cfg.url,
        type : "get",
        data : {
          draw : data.draw,
          start : data.start,
          length : data.length,
          search : data.search ? data.search.value : '',
          orderCol : kolom,
          orderDir : arah,
          tglawal : $('#cpoTglAwal' + urut).val(),
          tglakhir : $('#cpoTglAkhir' + urut).val()
        },
        success : function (res) {
          cpoCacheOut[urut] = res
          callback(res)
        },
        error : function (err) {
          console.log(cfg.url + ' gagal:', err.status, err.responseText)
          alertify.warning('Gagal memuat data ' + cfg.nama)
          callback({ draw : data.draw, data : [], recordsTotal : 0, recordsFiltered : 0 })
        }
      })
    },
    "drawCallback" : function () {
      setTimeout(cpoAturTinggiTabel, 0)
      $(selTabel).find('[data-toggle="tooltip"]').tooltip('dispose')
      $(selTabel).find('[data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
    }
  });

  let elMemuat = document.querySelector('#' + cfg.tabel + '_wrapper > .dataTables_processing')
  if (elMemuat) { elMemuat.classList.remove('card') }

  cpoIkatSearch(urut)
  cpoIkatPanjangHalaman(urut)
  cpoIkatPeriode(urut)
  let inputSearch = document.getElementById(cfg.search)
  if (inputSearch) { inputSearch.value = posisi ? posisi.search : '' }
  cpoAturTinggiTabel()
}

/* ============ Alur close (kunci) & open (buka kunci) ============ */

function cpoCloseForm () {
  $('#modalLockPO').modal('hide')
}

function getMSSQLDateTime () {
  let now = new Date()
  let year = now.getFullYear()
  let month = String(now.getMonth() + 1).padStart(2, '0')
  let day = String(now.getDate()).padStart(2, '0')
  let hours = String(now.getHours()).padStart(2, '0')
  let minutes = String(now.getMinutes()).padStart(2, '0')
  let seconds = String(now.getSeconds()).padStart(2, '0')

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

// Qty Sisa dicek dulu lewat server sebelum modal alasan dibuka - kalau sudah 0, tidak ada
// yang bisa diclose (Qty Batal tidak boleh diisi 0). Server tetap mengecek ulang saat
// submit, pengecekan di sini hanya supaya user tidak terlanjur mengetik alasan.
function cpoLockItem (nobukti, urut) {
  $.ajax({
    url : "{!! url('closingpoceksisa') !!}",
    type : "get",
    data : { nobukti, mode : 'item', urut },
    success : function (res) {
      if (!res.jmlSisa) {
        alertify.warning('Qty Sisa sudah 0, barang ini tidak bisa diclose.')
        return
      }
      $('#lock_nobukti').val(nobukti)
      $('#lock_mode').val('item')
      $('#lock_urut').val(urut)
      $('#lock_reason').val('')
      $('#modalLockPOLabel').text('Close PO Per Barang - ' + nobukti)
      $('#lock_info').text('Qty Sisa yang akan dibatalkan: ' + formatAngka(res.totalSisa))
      $('#modalLockPO').modal('show')
    },
    error : function (err) {
      alertify.error('Gagal mengecek Qty Sisa: ' + (err.responseJSON?.message || 'Unknown error'))
    }
  })
}

function cpoLockAll (nobukti) {
  $.ajax({
    url : "{!! url('closingpoceksisa') !!}",
    type : "get",
    data : { nobukti, mode : 'all' },
    success : function (res) {
      if (!res.jmlSisa) {
        alertify.warning('Semua barang pada No. Bukti ini Qty Sisa-nya sudah 0.')
        return
      }
      $('#lock_nobukti').val(nobukti)
      $('#lock_mode').val('all')
      $('#lock_urut').val('')
      $('#lock_reason').val('')
      $('#modalLockPOLabel').text('Close PO Per No. Bukti - ' + nobukti)
      let info = res.jmlSisa + ' dari ' + res.jml + ' barang bersisa, total Qty Sisa ' + formatAngka(res.totalSisa)
      if (res.jmlSisa < res.jml) {
        info += ' (' + (res.jml - res.jmlSisa) + ' barang Qty Sisa 0 akan dilewati)'
      }
      $('#lock_info').text(info)
      $('#modalLockPO').modal('show')
    },
    error : function (err) {
      alertify.error('Gagal mengecek Qty Sisa: ' + (err.responseJSON?.message || 'Unknown error'))
    }
  })
}

function cpoSubmitLock () {
  const _token = $('#_token').val()
  const nobukti = $('#lock_nobukti').val()
  const mode = $('#lock_mode').val()
  const urut = $('#lock_urut').val()
  const reason = $('#lock_reason').val().trim()

  if (!reason) {
    alertify.warning('Silakan masukkan alasan penguncian.')
    return
  }

  // Qty Batal tidak dikirim dari sini - server membacanya sendiri dari vwOutPOBatal, supaya
  // yang tersimpan pasti Qty Sisa baris itu apa adanya.
  $.ajax({
    url : mode === 'item'
      ? "{!! url('closingpospclosebarang') !!}"
      : "{!! url('closingpospcloseheader') !!}",
    type : 'POST',
    data : {
      _token,
      Nobukti : nobukti,
      Urut : mode === 'item' ? urut : '',
      KetBatal : reason,
      TglBatal : getMSSQLDateTime()
    },
    success : function (res) {
      if (res.success) {
        $('#modalLockPO').modal('hide')
        let pesan = mode === 'item'
          ? 'Barang berhasil diclose.'
          : res.diclose + ' barang diclose' + (res.dilewati > 0 ? ', ' + res.dilewati + ' barang dilewati (Qty Sisa 0)' : '') + '.'
        alertify.success(pesan)

        cpoCacheOut[1] = null
        cpoCacheOut[2] = null
        initTabelClosingPO(1)
        cpoPerluGambar[2] = true
      } else {
        alertify.error(res.message || 'Gagal mengunci data')
      }
    },
    error : function (xhr) {
      alertify.error('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'))
    }
  })
}

function cpoUnlock (nobukti, mode = 'all', urut = '') {
  alertify.confirm(
    'Buka Kunci',
    `Yakin ingin Membuka Kunci ${mode === 'item' ? 'Barang ini' : 'No. Bukti ' + nobukti} ?`,
    function () {
      const _token = $('#_token').val()

      $.ajax({
        url : mode === 'item'
          ? "{!! url('closingpospopenbarang') !!}"
          : "{!! url('closingpospopenheader') !!}",
        type : "POST",
        data : {
          _token,
          Nobukti : nobukti,
          Urut : mode === 'item' ? urut : ''
        },
        success : function (res) {
          if (res.success) {
            alertify.success('Data berhasil di-unlock')
            cpoCacheOut[1] = null
            cpoCacheOut[2] = null
            initTabelClosingPO(2)
            cpoPerluGambar[1] = true
          } else {
            alertify.error(res.message || 'Gagal membuka kunci')
          }
        },
        error : function (xhr) {
          alertify.error('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'))
        }
      })
    },
    function () {
      console.log('Batal unlock')
    }
  );
}

/* ============ Muat awal + pindah tab ============ */

function loadAll () {
  cpoInitReportTableSekali()

  let meta = null
  $.ajax({
    url: "{!! url('closingpurchaseorderloadall') !!}",
    type: "get",
    async: false,
    success: function (res) { meta = res },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat konfigurasi tabel')
    }
  })

  if (!meta) { return }

  cpoCart[1] = cpoBuatCart(meta.kolom1.headertableheader, meta.kolom1.headertablevalue, meta.kolom1.isnumeric, meta.kolom1.isshown, meta.kolom1.desimal, meta.kolom1.aliasordered)
  cpoCart[2] = cpoBuatCart(meta.kolom2.headertableheader, meta.kolom2.headertablevalue, meta.kolom2.isnumeric, meta.kolom2.isshown, meta.kolom2.desimal, meta.kolom2.aliasordered)

  let urutAktif = cpoUrutTabAktif()
  cpoPindahBar(urutAktif)

  ;[1, 2].forEach((u) => {
    cpoPerluGambar[u] = (u !== urutAktif)
  });

  initTabelClosingPO(urutAktif)
}

function formatAngka (angkaString) {
  if (!Number(angkaString)) {
    return '0.00';
  }

  angkaString = parseFloat(angkaString).toFixed(2);

  let tempAngka = angkaString.split('.');

  if (tempAngka[0][0] == '-') {
    let temp2 = '';
    let tempAngka1 = tempAngka[0].split('-');
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2;
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i - 1] + temp2;
    }
    temp2 += '.' + tempAngka[1];
    return '-' + temp2;
  }

  let temp1 = '';
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1;
    }
    temp1 = tempAngka[0][tempAngka[0].length - i - 1] + temp1;
  }
  temp1 += '.' + tempAngka[1];
  return temp1;
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

$(document).ready(function () {
  loadAll()

  $('#modalLockPO').on('shown.bs.modal', function () {
    $('#lock_reason').focus()
  })

  $('#nav-home-tab').on('shown.bs.tab', function () {
    cpoAktifkanTabel(1)
    cpoPindahBar(1)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (cpoPerluGambar[1]) {
      cpoPerluGambar[1] = false
      initTabelClosingPO(1)
    } else {
      cpoAturTinggiTabel()
    }
  })

  $('#nav-profile-tab').on('shown.bs.tab', function () {
    cpoAktifkanTabel(2)
    cpoPindahBar(2)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (cpoPerluGambar[2]) {
      cpoPerluGambar[2] = false
      initTabelClosingPO(2)
    } else {
      cpoAturTinggiTabel()
    }
  })

  let cpoTimerResize = null
  $(window).on('resize', function () {
    if (cpoTimerResize) { clearTimeout(cpoTimerResize) }
    cpoTimerResize = setTimeout(cpoAturTinggiTabel, 150)
  })
})

/* ============ Cetak PO ============
 * Dipertahankan apa adanya dari versi sebelumnya - dipakai tombol printer di tab Closing PO.
 * Sumber datanya endpoint purchaseorderprint (POController@spCetak / Sp_CetakPO), dan yang
 * dicetak adalah nilai-nilai batal (Qntbatal, HrgBatal, subtotalxbatal).
 */

  function submitPrintcopy (nobukti) {
  console.log('submitprint copy')
    let _token = $('#_token').val()
    let dataPrint = [];

    $.ajax({
      url: "{!! url('purchaseorderprint') !!}",
      type: "get",
      async: false,
      data: {
        _token : _token,
        NOBUKTI: nobukti
      },
      success: function(res) {

        dataPrint = res

        console.log(dataPrint)

      }
    })

    // Tanpa penjaga ini, No. Bukti yang tidak dikenali Sp_CetakPO membuat baris
    // `dataPrint[0].tanggal` di bawah melempar TypeError - jendela cetak terlanjur terbuka
    // kosong dan user tidak dapat pesan apa pun.
    if (!dataPrint || !dataPrint.length) {
      alertify.warning('Data cetak untuk No. Bukti ' + nobukti + ' tidak ditemukan.')
      return
    }

    const barisTotal = dataPrint.find(function (r) {
      return parseFloat(r.TSUBTOTALRpbatal) || parseFloat(r.nnetrpbatal)
    }) || dataPrint[0]

    let arrayDataPrint = []

    const isA4 = dataPrint.length > 7;

    const maxRowsPerPage = isA4 ? 7 : 7;

    for (let i = 0; i < dataPrint.length; i += maxRowsPerPage) {
        let tempArray = dataPrint.slice(i, i + maxRowsPerPage);
        arrayDataPrint.push(tempArray);
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''

    console.log('dataPrint:', dataPrint);
    console.log('dataPrint length:', dataPrint.length);
    console.log('dataPrint[0]:', dataPrint[0]);

    let tanggalOnly = dataPrint[0].tanggal.split(' ')[0];
    let tanggalKirimOnly = dataPrint[0].tglkirim.split(' ')[0];


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
        height: 18px;
        padding: 0px 4px;
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

      .border-bottom {
        border: bottom;
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

      /* Tinggi kotak sengaja dibiarkan mengikuti isi. Dulu jalur isA4 memaksa
         min-height:29.7cm, padahal halaman dicetak Landscape (tinggi fisik cuma
         ~21cm), sehingga tiap chunk tumpah ke halaman berikutnya dan tumpahan
         kosong itu tercetak sebagai halaman kosong selang-seling. */
      .body-main-prints{
      width:21cm;
      ${isA4 ? `
          padding:15px;
          box-sizing:border-box;
      ` : ``}
      position:relative;
      }

      .footer-sign {
        padding-top: 5px;
        position: absolute;
        width: 100%;
        bottom: 12px;
      }

      .footer-print-date {
        position: absolute;
        width: 100%;
        bottom: 5px;
      }

       .solid{
        border-left: 0px red solid;
        height: 225px;
        width: 0px;
        display: inline-block;
        padding-left: 0px;
        }

      </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 60%">
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 15%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                      <div class="pb-1 ps-3" style="width: 85%; margin-top: 10px;">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%; font-size: 11px;">Jl. Ampera Pergudangan Mangkupalas Bisnis Centre Blok D No.18 RT.022, Simpang Pasir Palaran, Samarinda - Kalimantan Timur</div>
                        <div class="pb-1" style="width: 100%; font-size: 10px;">TELP : 0541 - 4104142, | FAX : 0541 - 4104195</div>
                        <div class="pb-1" style="width: 100%; font-size: 11px;">E-Mail : sml@indo.net.id</div>
                      </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">Kepada Yth : </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">`+dataPrint[0].NAMA+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 100%;">`+dataPrint[0].ALAMAT1+`</div>
                    </div>
                  </div>

                  <div style="width: 40%; margin-left: 30px; margin-top: 15px;">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">PURCHASE ORDER</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">No</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].nobukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                    </div>
              <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Batas Tgl Kirim</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalKirimOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Pembayaran</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].HARI+` Hari</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Mata Uang</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].KODEVLS+`</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 95%; height: 100px; max-height: 100px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 35%">NAMA BARANG</td>
                    <td class="text-center" style="width: 15%">KODE BRG</td>
                    <td class="text-center" style="width: 10%">MERK</td>
                    <td class="text-center" style="width: 8%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
                    <td class="text-center" style="width: 15%">JUMLAH</td>
                  </tr>
                </thead> `;

    let z = 0
    let jumlahTotal = 0
    let diskonTotal = 0
    let subTotal = 0
    let ppnTotal = 0
    let totalTotal = 0

    let tempPrintStr = ``
    tempPrintStr += `<html>
    <head>
      <title></title>
    </head>
      ` + css

      arrayDataPrint.forEach((item, i) => {
        console.log('arrayDataPrint' , i)
        const isLastChunk = i == arrayDataPrint.length - 1
        // Break dikontrol per chunk di sini, bukan lewat CSS :last-of-type yang
        // tidak reliable - chunk terakhir tidak diberi break supaya tidak ada
        // halaman kosong di ujung.
        const breakStyle = isLastChunk ? '' : 'page-break-after: always;'
        const outerStyle = i == 0 ? 'margin-left: 7px; margin-top:5px;' : 'margin-left: 7px; padding-top:7px;'
        tempPrintStr +=  `<div class="body-main-prints" style="break-inside: avoid; ${outerStyle} ${breakStyle}">`
        tempPrintStr += hdr
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 2%; text-align: center;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NAMABRG}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.PartNumber}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 10%;">${itemSub.NAMAMERK ?? ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.QNT ? formatAngka(itemSub.Qntbatal) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATUAN}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${formatAngka(parseFloat(itemSub.HrgBatal).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${formatAngka(parseFloat(itemSub.subtotalxbatal).toFixed(2))}</td>
    </tr>`;
  z++;
});

// Fill remaining empty rows   table is 225px, each row ~24px, header ~24px = ~8 total slots
if (!isA4) {
const maxRows = 7;
const fillerCount = Math.max(0, maxRows - item.length);
for (let f = 0; f < fillerCount; f++) {
  tempPrintStr += `
    <tr style="height: 18px;">
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class='no-border'>&nbsp;</td>
    </tr>`;
  }
}

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 13px;">

  <div style="width: 38%; font-family: sans-serif; font-size: 10px;">
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Ke:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">CV. SINAR MAHAKAM LESTARI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+dataPrint[0].AlamatGudang+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Via:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${dataPrint[0].Expedisi ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${dataPrint[0].almkirim ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Semua Dokumen Asli Dikirim Ke:</h3>
    </div>
    <div style="display: flex; flex-direction: column; width: 100%">
      <div class="pb-1">CV. SINAR MAHAKAM LESTARI</div>
      <div class="pb-1">Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel. Simpang Pasir Mangkupalas, Samarinda Seberang.</div>
      <div class="pb-1">Telp: +62 541-4104142 | UP. IBU ALVI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div>User :</div>
      <div>`+dataPrint[0].Iduser+`</div>
    </div>
  </div>`

  tempPrintStr += `
  <div style="width: 62%; font-family: sans-serif; font-size: 10px;">
`

  if(i == arrayDataPrint.length - 1){

    tempPrintStr += `
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
      <div style="width: 5%; text-align:left;"> JUMLAH </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(barisTotal.TSUBTOTALRpbatal).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 4px; position: relative;">
      <div style="width: 5%; text-align:left;"> DISKON </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(barisTotal.Tdiscbatal).toFixed(2))}</div>

      <div style="
      position: absolute;
      right: 0;
      bottom: 0;
      width: 35%;
      border-bottom: 1px solid #000;"></div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
      <div style="width: 5%; text-align:left;"> DPP </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(barisTotal.TndpprpBatal).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 6px; position: relative;">
      <div style="width: 5%; text-align:left;"> PPN </div>
      <div style="
        position: absolute;
        right: 0;
        bottom: 3px;
        width: 35%;
        border-bottom: 1px solid #000;">
      </div>

      <!-- garis bawah 2 -->
      <div style="
        position: absolute;
        right: 0;
        bottom: 0;
        width: 35%;
        border-bottom: 1px solid #000;">
      </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(barisTotal.TnppnRpBatal).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 8px; font-weight: bold;">
      <div style="width: 5%; text-align:left;"> TOTAL </div>
      <div style="width: 30%; text-align: right">${formatAngka(parseFloat(barisTotal.nnetrpbatal).toFixed(2))}</div>
    </div>`
  };

     tempPrintStr += `
      <div style="width:50%; margin-top:15px; margin-left:-25px; float:left; text-align:center; font-size:10px;">
       <div>Disetujui Oleh</div>

       <div style="height:60px;"></div>

       <div style="font-size:10px;">
        `+dataPrint[0].otouser+`
       </div>
     <div style="font-size:10px;">
        ELECTRONICALLY APPROVED
     </div>
      </div>

        <div style="width:50%; float:left; font-size:10px; margin-top:-10px; margin-left:-15px;">
        <table style="width:100%; border-collapse: collapse;">

        <!-- HEADER -->
        <tr style="height:20px;">
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Konfirmasi Supplier
          </td>
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Estimasi Tgl. Kirim
          </td>
        </tr>

        <!-- ROW NAMA -->
        <tr style="height:50px;">
          <td style="border:1px solid black; width:50%; font-size:10px; vertical-align: bottom;">
            Nama
          </td>

          <!-- KOLOM KANAN -->
          <td rowspan="2" style="border:1px solid black; height:60px; position:relative;">

            <div style="position:absolute; bottom:5px; left:6px; font-style:italic; font-size:10px; vertical-align: bottom;">
              *wajib isi
            </div>

          </td>
        </tr>

        <!-- ROW TANGGAL -->
        <tr style="height:20px;">
          <td style="border:1px solid black; font-size:10px;">
            Tanggal
          </td>
        </tr>

      </table>
    </div>

    <div style="clear: both;"></div>
  </div>

</div>`


        tempPrintStr += `</div>`
      });


      tempPrintStr +=  `</body></html>`

    let w = window.open(' ');

    w.document.open();
    w.document.write(tempPrintStr);
    w.document.close();

    setTimeout(() => {
      w.focus();
      w.print();
      }, 300);
    }

</script>
@endsection
