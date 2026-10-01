@extends('newmasterTest')
@section('page-title', 'Pembelian Closing PR')

@section('css')
  {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi), disamakan
     dengan resources/views/purchasing/purchaseOrder.blade.php. Aturannya di-scope ke
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
/* Halaman ini dirancang mengisi tinggi layar (lihat clAturTinggiTabel()), jadi padding
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

/* Tab pil biru, disamakan persis dengan purchaseOrder.blade.php. */
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
   width, di-scope lewat ID (bukan class) - lihat catatan yang sama di purchaseOrder.blade.php. */
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

/* Baris "No data available in table" bawaan DataTables ikut align kiri karena td-nya
   juga kena rule ":first-child { display:flex; justify-content:flex-start... }" milik
   kolom Action. Dikembalikan jadi table-cell biasa supaya colspan bawaan DataTables tetap
   berlaku - kalau dipaksa display:block selnya lepas dari colspan dan menyempit di kiri. */
#tabel td.dataTables_empty,
#tabel2 td.dataTables_empty {
  display: table-cell !important;
  width: auto !important;
  text-align: center !important;
  vertical-align: middle !important;
}

/* ---- Lapisan "sedang memuat" kedua tabel ----
   Elemennya adalah .dataTables_processing bawaan DataTables, tampilannya ditulis ulang
   di sini - lihat catatan yang sama di purchaseOrder.blade.php. */
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
  animation: clMunculLoading .34s ease-out both;
}

@keyframes clMunculLoading {
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
  animation: clPutarLoading .6s linear infinite;
}

@keyframes clPutarLoading {
  to { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')

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
              Outstanding PR
          </a>

          <a class="nav-item nav-link"
            id="nav-profile-tab"
            data-toggle="tab"
            href="#profile"
            role="tab"
            aria-controls="profile"
            aria-selected="false">
              Closing PR
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
                      <input type="date" class="po-filter-inp" id="clTglAwal1" value="{!! $clTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="clTglAkhir1" value="{!! $clTglAkhir !!}">
                    </div>
                    <input type="search" id="clSearch1" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Nilai -1 = tampilkan semua data - lihat
                         PembelianClosingPRController@dataOutstanding. --}}
                    <div class="po-len-wrap">
                      <label for="clLen1">Tampilkan</label>
                      <select id="clLen1" class="po-len-inp">
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
                       (clPindahBar) saat tab berganti. --}}
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
                      <input type="date" class="po-filter-inp" id="clTglAwal2" value="{!! $clTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="clTglAkhir2" value="{!! $clTglAkhir !!}">
                    </div>
                    <input type="search" id="clSearch2" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap">
                      <label for="clLen2">Tampilkan</label>
                      <select id="clLen2" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat clPindahBar(). --}}
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
            <p class="mb-2 text-muted" id="lock_info" style="font-size: 0.85rem;"></p>
            <div class="form-group">
              <label for="lock_reason">Masukkan Alasan:</label>
              <textarea class="form-control" id="lock_reason" rows="3" placeholder="" autocomplete="off" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="clCloseForm()" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" onclick="clSubmitLock()">Kunci</button>
          </div>
        </div>
      </div>
    </div>
    {{-- End Modal closing sample --}}
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
 * Diporting dari purchaseOrder.blade.php, disederhanakan untuk 2 tabel (bukan 3) dan
 * tanpa client-side table (kedua tab di sini server-side paging, tidak ada yang seperti
 * tab "Purchase Order" yang ditarik sekaligus). Lihat dokumentasi lengkap pola ini di
 * purchaseOrder.blade.php - komentar di sini hanya menandai bagian yang berbeda.
 */

const CL_HREF = 'pembelianclosingpr'
const CL_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date' }

const CL_TAB = {
  1 : {
    tabel  : 'tabel',
    thead  : 'tabel_header',
    tbody  : 'tabel_data',
    search : 'clSearch1',
    len    : 'clLen1',
    url    : "{!! url('pembelianclosingprdataoutstanding') !!}",
    nama   : 'Outstanding PR',
    aksi   : 'lock'
  },
  2 : {
    tabel  : 'tabel2',
    thead  : 'tabel2_header',
    tbody  : 'tabel2_data',
    search : 'clSearch2',
    len    : 'clLen2',
    url    : "{!! url('pembelianclosingprdataclosing') !!}",
    nama   : 'Closing PR',
    aksi   : 'unlock'
  }
}

let clCart = { 1 : [], 2 : [] }
let clActiveUrut = 0
let clPerluGambar = { 1 : false, 2 : false }
let clCacheOut = { 1 : null, 2 : null }
let clPakaiCacheOut = { 1 : false, 2 : false }
let clPanjangHalaman = { 1 : 10, 2 : 10 }

function clUrutTabAktif () {
  return $('#nav-profile-tab').hasClass('active') ? 2 : 1
}

const CL_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'

function clBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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
      CL_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function clKolomTampil (urut) {
  return (clCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function clKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function clAktifkanTabel (urut) {
  clActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = clCart[urut]
}

function clOnChangeAktif () {
  initTabelClosing(clActiveUrut, true)
}

function clHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let clRtSudahInit = false

function clInitReportTableSekali () {
  if (clRtSudahInit || typeof ReportTable === 'undefined') { return }
  clRtSudahInit = true

  let urutAktif = clUrutTabAktif()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({
      table    : idTabel[u],
      onChange : clOnChangeAktif
    })
  });

  ReportTable.init({
    table    : CL_SELEKTOR_TABEL_AKTIF,
    bar      : '#rtBar',
    onChange : clOnChangeAktif
  })

  // Guard klik roda gigi vs sort DataTables - lihat catatan lengkap di
  // purchaseOrder.blade.php (poInitReportTableSekali).
  let clGuardUlangKlik = false
  let idThead = ['tabel_header', 'tabel2_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (clGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      clGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      clGuardUlangKlik = false
    }, true)
  });
}

function clPindahBar (urut) {
  let bar = document.getElementById('rtBar')
  let id = CL_TAB[urut].tabel
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

let clTimerSearch = { 1 : null, 2 : null }
function clIkatSearch (urut) {
  let input = document.getElementById(CL_TAB[urut].search)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let nilai = input.value
    if (clTimerSearch[urut]) { clearTimeout(clTimerSearch[urut]) }
    clTimerSearch[urut] = setTimeout(function () {
      $('#' + CL_TAB[urut].tabel).DataTable().search(nilai).draw()
    }, 400)
  })
}

function clIkatPanjangHalaman (urut) {
  let sel = document.getElementById(CL_TAB[urut].len)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(clPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    clPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    $('#' + CL_TAB[urut].tabel).DataTable().page.len(clPanjangHalaman[urut]).draw()
  })
}

// Ubah salah satu tanggal periode -> kosongkan cache tab ini lalu reload, supaya
// halaman pertama tidak menampilkan hasil rentang lama (lihat clCacheOut/clPakaiCacheOut).
function clIkatPeriode (urut) {
  let awal  = document.getElementById('clTglAwal' + urut)
  let akhir = document.getElementById('clTglAkhir' + urut)
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    clCacheOut[urut] = null
    clPakaiCacheOut[urut] = false
    $('#' + CL_TAB[urut].tabel).DataTable().ajax.reload()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function clAturTinggiTabel () {
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

window.g_href = CL_HREF
window.g_modeReport = 1
window.gcart_header = []

function clUrutSah (mode) {
  let urut = Number(mode)
  return urut === 2 ? 2 : 1
}

window.doSimpanHeader = function (href, mode) {
  let urut = clUrutSah(mode)
  let cart = clCart[urut] || []

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
      href     : CL_HREF,
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
  let urut = clUrutSah(mode)

  $.ajax({
    url   : "{!! url('pembelianclosingprheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      urut   : urut,
      reset  : 1
    },
    success : function (res) {
      clCart[urut] = clBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = clCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function clFormatAngkaDes (nilai, des) {
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

function clRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return clFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

function clAksiHtml (urut, row) {
  if (urut === 1) {
    return `<button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Kunci Per Barang" onclick="clLockItem('${row.Nobukti}', '${row.urut}')"><i class="bi bi-lock-fill"></i></button>
            <button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Kunci Per No Bukti" onclick="clLockAll('${row.Nobukti}')"><i class="bi bi-lock-fill"></i></button>`
  }
  return `<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Buka Per Barang" onclick="clUnlock('${row.Nobukti}', 'item', '${row.Urut}')"><i class="bi bi-unlock"></i></button>
          <button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Buka Per No Bukti" onclick="clUnlock('${row.Nobukti}', 'all')"><i class="bi bi-unlock-fill"></i></button>`
}

// Menggambar salah satu tabel: urut 1 = Outstanding PR, urut 2 = Closing PR. Sejajar
// initTabelOutstanding() di purchaseOrder.blade.php.
function initTabelClosing (urut, pakaiCache) {
  let cfg = CL_TAB[urut]
  if (!cfg) { return }
  let selTabel = '#' + cfg.tabel
  clAktifkanTabel(urut)

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

  let cols = clKolomTampil(urut)
  let kolomRender = cols.map(clKolomRender)

  let thead = document.getElementById(cfg.thead)
  thead.innerHTML = clHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px; width: 100px;" scope="col">Action</th>')
  }

  let columns = [{
    data : null,
    orderable : false,
    className : 'text-center',
    render : function (data, type, row) {
      return clAksiHtml(urut, row)
    }
  }]

  kolomRender.forEach((c) => {
    columns.push({
      data : null,
      className : c.tipe === 1 ? 'text-right' : '',
      render : function (data, type, row) {
        return clRenderNilai(c, row)
      }
    })
  });

  clPakaiCacheOut[urut] = !!(pakaiCache && clCacheOut[urut])

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
      "processing" : '<span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>'
    },
    "serverSide" : true,
    "lengthChange" : false,
    "pageLength" : clPanjangHalaman[urut],
    "searchDelay" : 400,
    "dom" : "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "order" : orderAman,
    "displayStart" : posisi ? posisi.start : 0,
    "search" : posisi ? { "search" : posisi.search } : { "search" : "" },
    "columns" : columns,
    "ajax" : function (data, callback, settings) {
      if (clPakaiCacheOut[urut] && clCacheOut[urut]) {
        clPakaiCacheOut[urut] = false
        callback(Object.assign({}, clCacheOut[urut], { draw : data.draw }))
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
          tglawal : $('#clTglAwal' + urut).val(),
          tglakhir : $('#clTglAkhir' + urut).val()
        },
        success : function (res) {
          clCacheOut[urut] = res
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
      setTimeout(clAturTinggiTabel, 0)
      $(selTabel).find('[data-toggle="tooltip"]').tooltip('dispose')
      $(selTabel).find('[data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
    }
  });

  let elMemuat = document.querySelector('#' + cfg.tabel + '_wrapper > .dataTables_processing')
  if (elMemuat) { elMemuat.classList.remove('card') }

  clIkatSearch(urut)
  clIkatPanjangHalaman(urut)
  clIkatPeriode(urut)
  let inputSearch = document.getElementById(cfg.search)
  if (inputSearch) { inputSearch.value = posisi ? posisi.search : '' }
  clAturTinggiTabel()
}

/* ============ Alur kunci (close) & buka kunci ============ */

function clCloseForm () {
  $('#modalLockSample').modal('hide')
}

// Sisa PR dicek dulu lewat server sebelum modal alasan dibuka - kalau sudah 0, tidak
// mungkin ada yang di-close (Qty Batal tidak boleh diisi 0).
function clLockItem (nobukti, urut) {
  $.ajax({
    url : "{!! url('pembelianclosingprceksisa') !!}",
    type : "get",
    data : { nobukti, mode : 'item', urut },
    success : function (res) {
      if (!res.jmlSisa) {
        alertify.warning('Sisa PR sudah 0, item ini tidak bisa diclose.')
        return
      }
      $('#lock_nobukti').val(nobukti)
      $('#lock_mode').val('item')
      $('#lock_urut').val(urut)
      $('#lock_reason').val('')
      $('#lock_info').text('Sisa PR: ' + formatAngka(res.totalSisa))
      $('#modalLockSample').modal('show')
    },
    error : function (err) {
      alertify.error('Gagal mengecek sisa PR: ' + (err.responseJSON?.message || 'Unknown error'))
    }
  })
}

function clLockAll (nobukti) {
  $.ajax({
    url : "{!! url('pembelianclosingprceksisa') !!}",
    type : "get",
    data : { nobukti, mode : 'all' },
    success : function (res) {
      if (!res.jmlSisa) {
        alertify.warning('Semua item pada No. Bukti ini sisa PR-nya sudah 0.')
        return
      }
      $('#lock_nobukti').val(nobukti)
      $('#lock_mode').val('all')
      $('#lock_urut').val('')
      $('#lock_reason').val('')
      let info = res.jmlSisa + ' dari ' + res.jml + ' item bersisa, total sisa PR ' + formatAngka(res.totalSisa)
      if (res.jmlSisa < res.jml) {
        info += ' (' + (res.jml - res.jmlSisa) + ' item sisa 0 akan dilewati)'
      }
      $('#lock_info').text(info)
      $('#modalLockSample').modal('show')
    },
    error : function (err) {
      alertify.error('Gagal mengecek sisa PR: ' + (err.responseJSON?.message || 'Unknown error'))
    }
  })
}

function clSubmitLock () {
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
    url: '{{ url("pembelianclosingprlock") }}',
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
        $('#modalLockSample').modal('hide')
        let pesan = mode === 'item'
          ? 'Item berhasil diclose.'
          : res.diclose + ' item diclose' + (res.dilewati > 0 ? ', ' + res.dilewati + ' item dilewati (sisa PR 0)' : '') + '.'
        alertify.success(pesan)

        initTabelClosing(1)
        clPerluGambar[2] = true
      } else {
        alertify.error(res.message || "Gagal mengunci data")
      }
    },
    error: function (xhr) {
      alertify.error("Terjadi kesalahan: " + (xhr.responseJSON?.message || 'Unknown error'))
    }
  })
}

function clUnlock (nobukti, mode = 'all', urut = '') {
  alertify.confirm(
    'Buka Kunci',
    `Yakin ingin Membuka Kunci ${mode === 'item' ? 'Data ini' : 'No. Bukti ' + nobukti} ?`,
    function () {
      const _token = $('#_token').val();

      $.ajax({
        url: "{{ url('pembelianclosingprunlock') }}",
        type: "POST",
        data: {
          _token,
          nobukti,
          urut: mode === 'item' ? urut : '',
          mode
        },
        success: function (res) {
          if (res.success) {
            alertify.success("Data berhasil di-unlock")
            initTabelClosing(2)
            clPerluGambar[1] = true
          } else {
            alertify.error(res.message || "Gagal membuka kunci")
          }
        },
        error: function (xhr) {
          alertify.error("Terjadi kesalahan: " + (xhr.responseJSON?.message || 'Unknown error'))
        }
      })
    },
    function () {
      console.log('Batal unlock')
    }
  );
}

/* ============ Muat awal + pindah tab ============ */

function clLoadAll () {
  clInitReportTableSekali()

  let meta = null
  $.ajax({
    url: "{!! url('pembelianclosingprloadall') !!}",
    type: "get",
    async: false,
    success: function (res) { meta = res },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat konfigurasi tabel')
    }
  })

  if (!meta) { return }

  clCart[1] = clBuatCart(meta.kolom1.headertableheader, meta.kolom1.headertablevalue, meta.kolom1.isnumeric, meta.kolom1.isshown, meta.kolom1.desimal, meta.kolom1.aliasordered)
  clCart[2] = clBuatCart(meta.kolom2.headertableheader, meta.kolom2.headertablevalue, meta.kolom2.isnumeric, meta.kolom2.isshown, meta.kolom2.desimal, meta.kolom2.aliasordered)

  let urutAktif = clUrutTabAktif()
  clPindahBar(urutAktif)

  ;[1, 2].forEach((u) => {
    clPerluGambar[u] = (u !== urutAktif)
  });

  initTabelClosing(urutAktif)
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
  clLoadAll()

  $('#modalLockSample').on('shown.bs.modal', function () {
    $('#lock_reason').focus()
  })

  $('#nav-home-tab').on('shown.bs.tab', function () {
    clAktifkanTabel(1)
    clPindahBar(1)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (clPerluGambar[1]) {
      clPerluGambar[1] = false
      initTabelClosing(1)
    } else {
      clAturTinggiTabel()
    }
  })

  $('#nav-profile-tab').on('shown.bs.tab', function () {
    clAktifkanTabel(2)
    clPindahBar(2)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (clPerluGambar[2]) {
      clPerluGambar[2] = false
      initTabelClosing(2)
    } else {
      clAturTinggiTabel()
    }
  })

  let clTimerResize = null
  $(window).on('resize', function () {
    if (clTimerResize) { clearTimeout(clTimerResize) }
    clTimerResize = setTimeout(clAturTinggiTabel, 150)
  })
})

</script>
@endsection
