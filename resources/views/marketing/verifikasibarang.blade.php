@extends('newmasterTest')
@section('buttons')

@endsection

@section('page-title', 'Verifikasi Barang')
@section('title', 'SML - Verifikasi Barang')

{{-- Verifikasi Barang: dibuat dari programajcSML/marketing/verifikasibarang (menu yang sudah
     dipakai user), tampilan disamakan dengan menu marketing newSML lain (pola purchaseOrder /
     closingso). Fungsi yang dibawa dari programajcSML:
       - daftar item penawaran ber-KODEBRG '-' yang sudah diverifikasi (kolom sama persis);
       - tombol Update Kode Barang -> isi Kode Barang (ketik + Enter atau browse) -> Simpan;
       - tombol "+ Master" (di sini "Tambah Master") untuk membuat master barang baru.
     Form Invoice (page2) di file lama tidak pernah ditampilkan (sisa salinan menu invoice),
     jadi tidak dibawa. --}}
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

/* Kolom Aksi - tombol pastel bulat, sama dengan closingso/so. */
#tabel td:first-child {
  display: flex; gap: 4px; justify-content: center; align-items: center;
}
#tabel td:first-child .btn {
  width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center;
  justify-content: center; border-radius: 7px; font-size: 13px; border: 1px solid transparent;
  box-shadow: none; transition: all .12s ease;
}
#tabel td:first-child .btn:hover { filter: brightness(0.97); transform: translateY(-1px); }
#tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel td:first-child .btn-danger { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

#tabel thead th {
  background: #f8f9fb !important; color: #6b7280 !important; font-size: 12px; text-transform: uppercase;
  letter-spacing: .04em; font-weight: 600; border-bottom: 1px solid #e7e9ee; border-top: none;
}
#tabel tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#tabel tbody tr:hover { background-color: #f5f3ff; }

#tabel tbody .action-buttons-wrap {
  opacity: 0;
  visibility: hidden;
  transform: translateX(-6px);
  transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
}
#tabel tbody tr:hover .action-buttons-wrap,
#tabel tbody tr:focus-within .action-buttons-wrap {
  opacity: 1;
  visibility: visible;
  transform: translateX(0);
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

/* Varian grid form (sama dengan master-list.css / masterbarang.blade.php) - dipakai modal
   Update Kode Barang dan Tambah Master. */
#formBsGrid .bs-form.bs-form-1 { grid-template-columns: 120px minmax(0, 1fr); }
#formBsGrid .bs-form.bs-form-1 .bs-full { grid-column: 2 / -1; }
#formBsGrid .bs-form.bs-form-3 { grid-template-columns: 120px minmax(0, 1fr) 100px minmax(0, 1fr) 100px minmax(0, 1fr); }
#formBsGrid .bs-form.bs-form-3 .bs-full-3 { grid-column: 4 / -1; }
#formBsGrid .bs-form .bs-check { display: flex; align-items: center; gap: 8px; min-height: 38px; }
#formBsGrid .bs-form .bs-check input[type="checkbox"] { margin: 0; transform: scale(1.3); }
#formBsGrid .bs-judul {
  grid-column: 1 / -1;
  margin: 6px 0 -4px;
  padding-top: 10px;
  border-top: 1px solid var(--rt-border, #E7E8F0);
  font-size: 12px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
  color: var(--rt-ink-soft, #6B7180);
}
@media (max-width: 575.98px) {
  #formBsGrid .bs-form.bs-form-1,
  #formBsGrid .bs-form.bs-form-3 { grid-template-columns: 1fr; }
  #formBsGrid .bs-form.bs-form-1 .bs-full,
  #formBsGrid .bs-form.bs-form-3 .bs-full-3 { grid-column: 1 / -1; }
}
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

  {{-- Satu tab saja ("Penawaran SO" di programajcSML), jadi pill tab tidak ditampilkan -
       sama seperti verifikasipenawaran newSML. --}}
  <div class="card">
    <div class="card-body" style="padding:0;">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row">
            <div class="col-12">
              <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                <div class="po-toolbar">
                  <input type="search" id="vbSearch1" class="po-search-inp" placeholder="Cari data">
                  <div class="po-len-wrap"><label for="vbLen1">Tampilkan</label>
                    <select id="vbLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                  </div>
                  <div class="po-toolbar-act">
                    <button class="btn btn-chip-biru" type="button" onclick="buttonAdd()">Tambah Master</button>
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
      </div>
    </div>
  </div>

</div>
</div>


<!-- start modal update kode barang -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="formLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formLabel">Update Kode Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="container-fluid">
          {{-- Data item penawaran (dulu tabel Detail: Kode Brg, Nama Brg, Qty, PIC) - hanya tampil. --}}
          <div class="bs-form">
            <label for="vb_nobukti">No. Bukti</label>
            <input type="text" class="form-control" id="vb_nobukti" disabled>
            <label for="vb_tanggal">Tanggal</label>
            <input type="text" class="form-control" id="vb_tanggal" disabled>

            <label for="vb_customer">Customer</label>
            <div class="bs-full"><input type="text" class="form-control" id="vb_customer" disabled></div>

            <label for="vb_pic">PIC</label>
            <input type="text" class="form-control" id="vb_pic" disabled>
            <label for="vb_qty">Qty</label>
            <input type="text" class="form-control text-right" id="vb_qty" disabled>

            <label for="vb_kodebrglama">Kode Brg</label>
            <input type="text" class="form-control" id="vb_kodebrglama" disabled>
            <label></label>
            <div></div>

            <label for="vb_namabrglama">Nama Brg</label>
            <div class="bs-full"><input type="text" class="form-control" id="vb_namabrglama" disabled></div>

            <div class="bs-judul">Update Kode Barang</div>

            <label for="AddAddKodeBrg">Barang</label>
            <div class="input-group">
              <input id="AddAddKodeBrg" type="text" class="form-control" placeholder="Ketik kode / nama, Enter" onkeypress="onKeyPressBarang(event)" oninput="onInputKodeBrg()">
              <button type="button" class="btn btn-chip-biru" title="Cari" onclick="buttonAddListBarang()"><i class="bi bi-search"></i></button>
            </div>
            <label for="AddAddNamaBrg">Nama</label>
            <input id="AddAddNamaBrg" type="text" class="form-control" disabled>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" id="buttonSubmitAddAdd" onclick="submitAddAdd()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal update kode barang -->


<!-- start modal pemilih barang -->
<div class="modal fade" id="formListBarang" tabindex="-1" role="dialog" aria-labelledby="formListBarangLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formListBarangLabel">Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabel_add_list_barang" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kode</th>
              <th style="padding: 4px 12px;" scope="col">Nama</th>
              <th style="padding: 4px 12px;" scope="col">Merk</th>
              <th style="padding: 4px 12px;" scope="col">Isi</th>
              <th style="padding: 4px 12px;" scope="col">Sat</th>
            </tr>
          </thead>
          <tbody id="tabel_data_add_list_barang" class="text-left"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal pemilih barang -->


<!-- start modal tambah master barang (tombol "+ Master" di programajcSML) -->
<div class="modal fade" id="formBarang" tabindex="-1" role="dialog" aria-labelledby="formBarangLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formBarangLabel">Tambah Master Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="formBsGrid">
        <div class="container-fluid">
          <div class="bs-form">
            <label for="input_add_kodegroup">Group</label>
            <div class="d-flex align-items-center" style="gap:8px">
              <input type="text" class="form-control" id="input_add_kodegroup" value='BJ' disabled style="flex:0 0 70px">
              <input type="text" class="form-control" value='Barang Jadi' disabled>
            </div>
            <label for="input_add_isagen">Keagenan</label>
            <div class="bs-check"><input type="checkbox" id="input_add_isagen" name="" value=""></div>

            <label for="input_add_kodeheadgroup">HeadGroup</label>
            <select id="input_add_kodeheadgroup" onchange="changeInputHeadGroup()" class="form-control">
              <option selected value="" disabled>Pilih HeadGroup</option>
            </select>
            <label for="input_add_kodesubgroup">SubGroup</label>
            <select id="input_add_kodesubgroup" onchange="changeInputSubGroup()" class="form-control">
              <option selected disabled value="">Pilih SubGroup</option>
            </select>

            <label for="input_add_kodesubkategori">SubKategori</label>
            <select id="input_add_kodesubkategori" onchange="changeInputSubKategori()" class="form-control">
              <option selected disabled value="">Pilih SubKategori</option>
            </select>
            <label for="input_add_kodemerk">Merk</label>
            <select id="input_add_kodemerk" onchange="changeInputMerk()" class="form-control">
              <option selected disabled value="">Pilih Merk</option>
            </select>

            <label for="input_add_kodebarang">Kode Barang</label>
            <input type="text" class="form-control" id="input_add_kodebarang" disabled>
            <label for="input_add_kodesku">Kode SKU</label>
            <div class="d-flex align-items-center" style="gap:8px">
              <input type="text" class="form-control" id="input_add_kodesku">
              <div class="bs-check"><input type="checkbox" id="input_add_iskontrak" name="" value=""><span>Kontrak</span></div>
            </div>

            <label for="input_add_namabarang">Nama Barang</label>
            <div class="bs-full"><input type="text" class="form-control" id="input_add_namabarang"></div>

            <label for="input_add_namabarang2">Nama Barang 2</label>
            <div class="bs-full"><input type="text" class="form-control" id="input_add_namabarang2"></div>
          </div>

          <br/>

          <div class="bs-form bs-form-1">
            <label></label>
            <div class="d-flex" style="gap:8px"><b class="form-control border-0 bg-transparent px-0">Satuan</b><b class="form-control border-0 bg-transparent px-0">Isi</b><b class="form-control border-0 bg-transparent px-0">Harga</b></div>

            <label for="input_add_satuan">Satuan</label>
            <div class="d-flex align-items-center" style="gap:8px">
              <select id="input_add_satuan" class="form-control"><option selected value="0">Pilih Satuan</option></select>
              <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_isi">
              <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_harga">
            </div>

            <label for="input_add_satuan2">Satuan 2</label>
            <div class="d-flex align-items-center" style="gap:8px">
              <select id="input_add_satuan2" class="form-control"><option selected value="0">Pilih Satuan</option></select>
              <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_isi2">
              <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_harga2">
            </div>

            <label for="input_add_satuan3">Satuan 3</label>
            <div class="d-flex align-items-center" style="gap:8px">
              <select id="input_add_satuan3" class="form-control"><option selected value="0">Pilih Satuan</option></select>
              <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_isi3">
              <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_harga3">
            </div>
          </div>

          <br/>

          <div class="bs-form bs-form-3">
            <label for="input_add_qtymin">Qty Min</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_qtymin">
            <label for="input_add_qtymax">Qty Max</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_qtymax">
            <label for="input_add_toleransi">Toleransi</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_toleransi">

            <label for="input_add_isaktif">Status</label>
            <select id="input_add_isaktif" class="form-control">
              <option value=1>Aktif</option>
              <option value=0>NonAktif</option>
            </select>
            <label for="input_add_isberat">Berat/Volume</label>
            <select id="input_add_isberat" class="form-control">
              <option value=0>Volume</option>
              <option value=1>Berat</option>
            </select>
            <label for="input_add_beratvolume">Berat/Volume</label>
            <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_add_beratvolume">

            <label for="input_add_partnumber">Part Number</label>
            <input type="text" class="form-control" id="input_add_partnumber">
            <label for="input_add_lokasi">Lokasi</label>
            <div class="bs-full-3"><input type="text" class="form-control" id="input_add_lokasi"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" id="buttonSubmitMaster" onclick="submitAdd()">Simpan</button>
      </div>
    </div>
  </div>
</div>
<!-- End modal tambah master barang -->

@endsection

@section('js')
@include('marketing.partials.tampilan-purchasing-js')
<script>window.RT_LABEL_PAJAK = true</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">

let xidrow = ''
let xnobukti = ''
let xurut = 0
let dataBarangx = []

let listSelectHeadGroup = []
let listSelectSubGroup = []
let listSelectSubKategori = []

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Pola sama dengan closingso.blade.php (satu tabel). Kolom default = kolom tabel
 * programajcSML: No. Bukti, Tanggal, Customer, PIC, Nama Brg, Tipe, Merk, Ket.
 */
let vbCart = { 1 : [] }
const VB_HREF = 'verifikasibarang'
const VB_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const VB_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function vbPickCI (row, key) {
  if (!row) { return undefined }
  if (row[key] !== undefined) { return row[key] }
  let lower = key.toLowerCase()
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k] } }
  return undefined
}

function vbDefaultCart () {
  return [
    ['NOBUKTI',      'No. Bukti', 1, 'varchar', 0, 0],
    ['TANGGAL',      'Tanggal',   1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Customer',  1, 'varchar', 0, 0],
    ['NAMAPIC',      'PIC',       1, 'varchar', 0, 0],
    ['NAMABRG',      'Nama Brg',  1, 'varchar', 0, 0],
    ['tipe',         'Tipe',      1, 'varchar', 0, 0],
    ['NamaMerk',     'Merk',      1, 'varchar', 0, 0],
    ['ketdet',       'Ket',       1, 'varchar', 0, 0],
  ]
}

function vbBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, VB_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  })
  return cart
}

window.g_href = VB_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let cart = vbCart[1] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(VB_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  })
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: VB_HREF, urut: 1
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: VB_HREF, urut: 1, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        vbCart[1] = vbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        vbCart[1] = vbDefaultCart()
        window.gcart_header = vbCart[1]
        window.doSimpanHeader(VB_HREF, 1)
      }
      window.gcart_header = vbCart[1]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      vbCart[1] = vbDefaultCart()
      window.gcart_header = vbCart[1]
    }
  })
}

let vbRtSudahInit = false
function vbInitReportTableSekali () {
  if (vbRtSudahInit || typeof ReportTable === 'undefined') { return }
  vbRtSudahInit = true
  ReportTable.init({ table: '#tabel', bar: '#rtBarTabel', onChange: reinitTabel })

  let guardUlangKlik = false
  let thead = document.querySelector('#tabel thead')
  if (!thead) { return }
  thead.addEventListener('click', function (e) {
    if (guardUlangKlik) { return }
    let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
    if (!interaktif) { return }
    e.stopPropagation()
    e.preventDefault()
    guardUlangKlik = true
    let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
    Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
    thead.dispatchEvent(ulang)
    guardUlangKlik = false
  }, true)
}

function tulisTheadHeaderVB (cols) {
  let thead = document.querySelector('#tabel thead')
  if (!thead || !window.ReportTable) { return }
  let headRowHtml = ReportTable.headHtml(cols)
  headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>')
  thead.setAttribute('style', 'white-space:nowrap;')
  thead.innerHTML = headRowHtml
}

function vbEsc (v) {
  return String(v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
}

function vbValueCell (row, col) {
  let raw = vbPickCI(row, col[0])
  let type = col[3]
  if (type === 'date') { if (!raw) { return '<td></td>' } return '<td>' + formatDate(raw).split('/').reverse().join('/') + '</td>' }
  if (type === 'float') {
    let dp = Number(col[5]) || 0
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0
    return '<td class="text-right">' + n.toLocaleString('en-US', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>'
  }
  return '<td>' + (raw !== undefined && raw !== null ? vbEsc(raw) : '') + '</td>'
}

// Tombol Update Kodebarang milik programajcSML (btn-success bi-pen) + Batal Verifikasi
// (fungsi buttonBatalOtorisasi programajcSML yang dulu tidak punya tombol).
function tabelActionsCell (index) {
  let html = '<td class="text-center"><div class="action-buttons-wrap">'
  html += '<button class="btn btn-success btn-sm" type="button" title="Update Kode Barang" onclick="buttonEdit(' + index + ')"><i class="bi bi-pen"></i></button>'
  html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Verifikasi" onclick="buttonBatalVerifikasi(' + index + ')"><i class="bi bi-key"></i></button>'
  html += '</div></td>'
  return html
}

let lastTabelRows = []
let vbPanjangHalaman = 10

function renderTabelRows (rows) {
  let cols = (vbCart[1].length ? vbCart[1] : gcart_header).filter(function (c) { return c[2] === 1 })
  let html = ''
  ;(rows || []).forEach(function (row, i) {
    html += '<tr>' + tabelActionsCell(i)
    cols.forEach(function (col) { html += vbValueCell(row, col) })
    html += '</tr>'
  })
  document.getElementById('tabel_data').innerHTML = html
  tulisTheadHeaderVB(cols)
}

function vbIkatSearch () {
  let input = document.getElementById('vbSearch1')
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

function vbIkatPanjangHalaman () {
  let sel = document.getElementById('vbLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(vbPanjangHalaman)
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    vbPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().page.len(vbPanjangHalaman).draw() }
  })
}

const VB_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    let cari = $('#vbSearch1').val() || ''
    // Halaman yang sedang dibuka dipertahankan setelah data dimuat ulang (simpan / batal).
    let halaman = 0
    if ($.fn.DataTable.isDataTable('#tabel')) {
      halaman = $('#tabel').DataTable().page()
      $('#tabel').DataTable().destroy()
    }
    // Tooltip tombol aksi dari baris lama jangan tertinggal di layar.
    $('body > .tooltip').remove()
    renderTabelRows(lastTabelRows)
    let dt = $('#tabel').DataTable({ dom: VB_DOM_STRING, lengthChange: false, pageLength: vbPanjangHalaman, paging: true, ordering: false, search: { search: cari } })
    let jumlahHalaman = dt.page.info().pages
    if (halaman > 0 && jumlahHalaman > 0) {
      dt.page(Math.min(halaman, jumlahHalaman - 1)).draw(false)
    }
    vbIkatSearch(); vbIkatPanjangHalaman()
  } catch (e) { console.error('reinitTabel failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message) }
}

$(document).ready(function(){
  window.doSetHeader(1, false)
  lastTabelRows = @json($tempOutstanding);
  reinitTabel()
  vbInitReportTableSekali()

  $('.format-number').autoNumeric('init', { mDec: '2', vMin: '-9999999999999.99' })

  // Modal tidak bertumpuk: selama modal pemilih barang terbuka, modal Update Kode Barang
  // disembunyikan dulu, lalu muncul lagi (isiannya tetap) setelah pemilih ditutup.
  $('#formListBarang').on('hidden.bs.modal', function () {
    if (vbBalikKeForm) {
      vbBalikKeForm = false
      $('#form').modal('show')
    }
  })
  $('#formListBarang').on('shown.bs.modal', function () {
    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang')) {
      $('#tabel_add_list_barang').DataTable().columns.adjust()
    }
  })
  $('#form').on('show.bs.modal', function () {
    $('#buttonSubmitAddAdd').prop('disabled', false)
  })
  $('#formBarang').on('show.bs.modal', function () {
    $('#buttonSubmitMaster').prop('disabled', false)
  })
  $('#form').on('shown.bs.modal', function () {
    $('#AddAddKodeBrg').trigger('focus')
  })
})


function loadAll () {
  $.ajax({
    url: "{!! url('verifikasibarangloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
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


/* ============ Update Kode Barang ============ */

// Hak akses dari master hak akses menu (DBFLMENUWEB), diisi controller ke input hidden akses_*.
// Server juga memeriksa ulang (VerifikasiBarangController).
function punyaAkses (idAkses) {
  if (!Number($("#" + idAkses).val())) {
    alertify.warning('No access')
    return false
  }
  return true
}

function buttonEdit (index) {
  if (!punyaAkses('akses_iskoreksi')) { return }
  let row = lastTabelRows[index]
  if (!row) { return }
  let nobukti = row.NOBUKTI
  let urut = row.Urut
  xnobukti = nobukti
  xurut = urut
  xidrow = row.ID
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('verifikasibarangdetailbarangx') !!}",
    type: "post",
    async: false,
    data: {
      nobukti,
      _token,
      urut
    },
    success: function(res) {
      dataBarangx = res
      // Perbaikan: programajcSML membaca dataBarangx[0].ID sebelum memeriksa data kosong,
      // jadi JS error kalau item sudah tidak ada.
      if (!res.length) {
        alertify.warning("Data tidak ditemukkan")
        return
      }
      let item = res[0]
      xidrow = item.ID

      document.getElementById("vb_nobukti").value = item.NOBUKTI || ''
      document.getElementById("vb_tanggal").value = item.TANGGAL ? formatDate(item.TANGGAL).split('/').reverse().join('/') : ''
      document.getElementById("vb_customer").value = item.NAMACUSTSUPP || ''
      document.getElementById("vb_pic").value = item.NAMAPIC || ''
      document.getElementById("vb_qty").value = item.QNT ? formatAngka(item.QNT) : ''
      document.getElementById("vb_kodebrglama").value = item.KODEBRG || ''
      document.getElementById("vb_namabrglama").value = item.NAMABRG || ''

      document.getElementById("AddAddKodeBrg").value = ''
      document.getElementById("AddAddNamaBrg").value = ''

      $("#form").modal("show")
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

// Pencarian barang dipakai tombol browse dan Enter (verifikasibaranglistbarang).
function cariBarang (search) {
  let hasil = null
  $.ajax({
    url: "{!! url('verifikasibaranglistbarang') !!}",
    type: "get",
    async: false,
    data: {
      search: search
    },
    success: function(res) {
      hasil = res
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
  return hasil
}

let vbBalikKeForm = false

function tampilkanPickerBarang (res) {
  $('#tabel_data_add_list_barang').empty()
  pickerKasInit('tabel_add_list_barang', {
    _pk: true,
    data: res,
    deferRender: true,
    order: [],
    columns: [
      { data: 'Kodebrg', defaultContent: '', render: $.fn.dataTable.render.text() },
      { data: 'Namabrg', defaultContent: '', render: $.fn.dataTable.render.text() },
      { data: 'namamerk', defaultContent: '', render: $.fn.dataTable.render.text() },
      { data: 'isi1', defaultContent: '', className: 'text-right', render: function (v) { return (v === null || v === undefined || v === '') ? '' : formatAngka(v) } },
      { data: 'sat1', defaultContent: '', render: $.fn.dataTable.render.text() },
    ],
    createdRow: function (tr, data) {
      tr.classList.add('pick-row')
      tr.addEventListener('click', function () { buttonAddPickBarang(data.Kodebrg, data.Namabrg) })
    }
  })

  // Modal Update Kode Barang disembunyikan selama pemilih terbuka.
  if ($('#form').hasClass('show')) {
    vbBalikKeForm = true
    $('#form').one('hidden.bs.modal', function () { $('#formListBarang').modal('show') })
    $('#form').modal('hide')
  } else {
    $('#formListBarang').modal('show')
  }
}

function buttonAddListBarang () {
  let search = $('#AddAddKodeBrg').val() || ''
  let res = cariBarang(search)
  if (res === null) { return }
  if (!res.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  tampilkanPickerBarang(res)
}

function onKeyPressBarang (event) {
  if (event.which !== 13) { return }
  event.preventDefault()
  let search = $('#AddAddKodeBrg').val() || ''
  let res = cariBarang(search)
  if (res === null) { return }
  if (!res.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  // Hasil tepat satu langsung dipilih (programajcSML). Perbaikan: dulu Nama terisi "1"
  // karena fungsi pemilih dipanggil dengan argumen yang salah.
  if (res.length == 1) {
    buttonAddPickBarang(res[0].Kodebrg, res[0].Namabrg)
    return
  }
  tampilkanPickerBarang(res)
}

// Kode diketik ulang setelah barang dipilih -> Nama dikosongkan, supaya yang tersimpan tidak
// berbeda dengan nama yang terlihat (Simpan menolak sampai barang dipilih lagi).
function onInputKodeBrg () {
  document.getElementById("AddAddNamaBrg").value = ''
}

function buttonAddPickBarang (kodebrg, namabrg) {
  document.getElementById("AddAddKodeBrg").value = kodebrg
  document.getElementById("AddAddNamaBrg").value = namabrg
  if ($('#formListBarang').hasClass('show')) {
    $('#formListBarang').modal('hide')
  }
}

function submitAddAdd () {
  let _token = $("#_token").val()
  let kodebrg = $("#AddAddKodeBrg").val()
  // Perbaikan: programajcSML tetap menyimpan walau Kode Barang kosong / tidak dipilih.
  if (!kodebrg || !$("#AddAddNamaBrg").val()) {
    alertify.warning('Pilih Kode Barang dulu (ketik lalu Enter, atau klik tombol cari)')
    return
  }
  // Tombol dikunci selama simpan: klik ganda tidak mengirim update dua kali.
  // Dibuka lagi saat modal dibuka berikutnya atau kalau simpan gagal.
  let $btn = $("#buttonSubmitAddAdd")
  if ($btn.prop('disabled')) { return }
  $btn.prop('disabled', true)
  let berhasil = false
  $.ajax({
    url: "{!! url('verifikasibarangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      idrow: xidrow,
      kodebrg
    },
    success: function(res) {
      if (res == 1) {
        berhasil = true
        loadAll()
        $("#form").modal('hide')
        alertify.success('Berhasil Update Kode Brg')
      } else if (res == 2) {
        // Item sudah diisi kodenya / dibatalkan user lain sejak layar ini dimuat.
        berhasil = true
        loadAll()
        $("#form").modal('hide')
        alertify.warning('Item ini sudah diubah atau dibatalkan oleh user lain. Data sudah dimuat ulang.')
      } else {
        alertify.warning(res)
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
  if (!berhasil) { $btn.prop('disabled', false) }
}

// Batal verifikasi: item dikembalikan ke status belum diverifikasi (IsVerf NULL), sehingga
// hilang dari daftar ini dan kembali ke menu Verifikasi Penawaran.
function buttonBatalVerifikasi (index) {
  if (!punyaAkses('akses_isbatal')) { return }
  let row = lastTabelRows[index]
  if (!row) { return }
  alertify.confirm('Batal Verifikasi',
    'Batalkan verifikasi item <b>' + vbEsc(row.NAMABRG || '') + '</b> pada ' + vbEsc(row.NOBUKTI) + '?',
    function () { buttonBatalOtorisasi(row.NOBUKTI, row.Urut) },
    function () {}
  )
}

// Fungsi batal verifikasi programajcSML (dulu tanpa tombol di tabel).
function buttonBatalOtorisasi (nobukti, urut) {
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('verifikasibarangbatalotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      urut
    },
    success: function(res) {
      if (res == 1) {
        loadAll()
        alertify.success('Berhasil Batal Verifikasi')
      } else if (res == 2) {
        loadAll()
        alertify.warning('Item ini sudah diubah atau dibatalkan oleh user lain. Data sudah dimuat ulang.')
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


/* ============ Tambah Master Barang ============
 * Tombol "+ Master" programajcSML. Perbaikan: di sana submitAdd() memakai variabel kodebrg
 * yang tidak pernah dibuat dan mengirim ke verifikasibarangspadd, sehingga master barang
 * tidak pernah tersimpan. Sekarang disimpan lewat masterbarangspadd dengan isian dan
 * validasi yang sama seperti menu Master Barang newSML.
 */

function toNum (v) {
  let n = parseFloat(String(v == null ? '' : v).replace(/,/g, ''))
  return isNaN(n) ? 0 : n
}
function setNum (id, v) {
  let el = document.getElementById(id)
  if (!el) return
  if (v === '' || v == null) {
    el.value = ''
  } else if ($(el).data('autoNumeric')) {
    $(el).autoNumeric('set', toNum(v))
  } else {
    el.value = v
  }
}

function changeInputHeadGroup () {
  let valueGroup = $("#input_add_kodegroup").val()
  let valueHeadGroup = $("#input_add_kodeheadgroup").val()

  let temp = listSelectSubGroup.filter (function (el) {
    return el.KodeGrp == valueGroup && el.KodeHDGrp == valueHeadGroup
  })

  let rowSelect = `<option selected disabled value="">Pilih SubGroup</option>`
  temp.forEach((item, i) => {
    rowSelect += `
    <option value="${item.KodeSubGrp}">${item.NamaSubGrp}</option>
    `
  })

  document.getElementById("input_add_kodesubgroup").innerHTML = rowSelect
  document.getElementById("input_add_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`
  document.getElementById("input_add_kodebarang").value = ''
}

function changeInputSubGroup () {
  let valueGroup = $("#input_add_kodegroup").val()
  let valueHeadGroup = $("#input_add_kodeheadgroup").val()
  let valueSubGroup = $("#input_add_kodesubgroup").val()

  let temp = listSelectSubKategori.filter(function (el){
    return el.KodeGrp == valueGroup && el.HDGROUP == valueHeadGroup && el.KodeSubGrp == valueSubGroup
  })
  let rowSelect = `<option selected disabled value="">Pilih SubKategori</option>`
  temp.forEach((item, i) => {
    rowSelect += `
    <option value="${item.Urut}">${item.Keterangan}</option>
    `
  })

  document.getElementById("input_add_kodesubkategori").innerHTML = rowSelect
  document.getElementById("input_add_kodebarang").value = ''
}

// Kode barang = '2' + HeadGroup + SubGroup + SubKategori + Merk + urut 4 digit
// (sama dengan programajcSML & Master Barang newSML).
function buatKodeBarang () {
  let valueHeadGroup = $("#input_add_kodeheadgroup").val()
  let valueSubGroup = $("#input_add_kodesubgroup").val()
  let valueSubKategori = $("#input_add_kodesubkategori").val()
  let valueMerk = $("#input_add_kodemerk").val()

  let kodebarang = `2` +valueHeadGroup +valueSubGroup +valueSubKategori + valueMerk

  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('masterbarangspcheckdbbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      kodebarang: kodebarang + '%'
    },
    success: function(res) {
      if(!res.length) {
        kodebarang += '0001'
      } else {
        let tes = res[0].KODEBRG
        let temp = tes.slice(kodebarang.length , kodebarang.length + 4)
        let tempUrut = '0000' + (Number(temp) + 1)
        kodebarang += tempUrut.slice(-4)
      }
    }})

  document.getElementById("input_add_kodebarang").value = kodebarang
}

function changeInputSubKategori () {
  if ($("#input_add_kodemerk").val()) {
    buatKodeBarang()
  }
}

function changeInputMerk () {
  if ($("#input_add_kodesubkategori").val()) {
    buatKodeBarang()
  }
}

function buttonAdd () {
  if (!punyaAkses('akses_istambah')) { return }
  document.getElementById("input_add_isagen").checked = false
  document.getElementById("input_add_iskontrak").checked = false
  document.getElementById("input_add_kodebarang").value= ''
  document.getElementById("input_add_namabarang").value= ''
  document.getElementById("input_add_namabarang2").value= ''
  setNum("input_add_isi", '1.00')
  setNum("input_add_isi2", '1.00')
  setNum("input_add_isi3", '0.00')
  setNum("input_add_harga", '0.00')
  setNum("input_add_harga2", '0.00')
  setNum("input_add_harga3", '0.00')
  setNum("input_add_qtymax", '0.00')
  setNum("input_add_qtymin", '0.00')
  setNum("input_add_toleransi", '0.00')
  document.getElementById("input_add_isberat").value= 0
  document.getElementById("input_add_isaktif").value= 1
  setNum("input_add_beratvolume", '0.00')
  document.getElementById("input_add_partnumber").value= ''
  document.getElementById("input_add_lokasi").value= ''
  document.getElementById("input_add_kodesku").value= ''

  $.ajax({
    url: "{!! url('masterbaranglistselect') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      listSelectHeadGroup = res.listHeadGroup
      listSelectSubGroup = res.listSubGroup
      listSelectSubKategori = res.listSubKategori
      let rowSelectMerk = `<option selected disabled value="">Pilih Merk</option>`
      res.listMerk.forEach((item, i) => {
        rowSelectMerk += `
          <option value="${item.KODEMERK}">${item.NAMAMERK}</option>
        `
      })
      document.getElementById("input_add_kodemerk").innerHTML = rowSelectMerk
    }})

  $.ajax({
    url: "{!! url('masterbaranglistsatuan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowSelect = `<option selected value=0>Pilih Satuan</option>`
      res.forEach((item, i) => {
        rowSelect += `
          <option value="${item.kodesatuan}">${item.kodesatuan}</option>
        `
      })
      document.getElementById("input_add_satuan").innerHTML = rowSelect
      document.getElementById("input_add_satuan2").innerHTML = rowSelect
      document.getElementById("input_add_satuan3").innerHTML = rowSelect
    }})

  let rowSelect = `<option selected disabled value="">Pilih HeadGroup</option>`
  listSelectHeadGroup.forEach((item, i) => {
    rowSelect += `
      <option value="${item.KODEHDGRP}">${item.NAMAHDGRP}</option>
    `
  })
  document.getElementById("input_add_kodeheadgroup").innerHTML = rowSelect
  document.getElementById("input_add_kodesubgroup").innerHTML =  `<option selected disabled value="">Pilih SubGroup</option>`
  document.getElementById("input_add_kodesubkategori").innerHTML =  `<option selected disabled value="">Pilih SubKategori</option>`
  $("#formBarang").modal('show')
}

function submitAdd () {
  let _token = $("#_token").val()
  let isagen = document.getElementById("input_add_isagen").checked ? 1 : 0
  let iskontrak = document.getElementById("input_add_iskontrak").checked ? 1 : 0
  let kodegroup = $("#input_add_kodegroup").val()
  let kodeheadgroup = $("#input_add_kodeheadgroup").val()
  let kodesubgroup = $("#input_add_kodesubgroup").val()
  let kodesubkategori = $("#input_add_kodesubkategori").val()
  let kodemerk = $("#input_add_kodemerk").val()
  let kodebarang = $("#input_add_kodebarang").val()
  let namabarang = $("#input_add_namabarang").val()
  let namabarang2 = $("#input_add_namabarang2").val()
  let satuan = $("#input_add_satuan").val()
  let satuan2 = $("#input_add_satuan2").val()
  let satuan3 = $("#input_add_satuan3").val()
  let isi = $("#input_add_isi").val().replace(/,/g, '')
  let isi2 = $("#input_add_isi2").val().replace(/,/g, '')
  let isi3 = $("#input_add_isi3").val().replace(/,/g, '')
  let harga = $("#input_add_harga").val().replace(/,/g, '')
  let harga2 = $("#input_add_harga2").val().replace(/,/g, '')
  let harga3 = $("#input_add_harga3").val().replace(/,/g, '')
  let qtymax = $("#input_add_qtymax").val().replace(/,/g, '')
  let qtymin = $("#input_add_qtymin").val().replace(/,/g, '')
  let toleransi = $("#input_add_toleransi").val().replace(/,/g, '')
  let isberat = $("#input_add_isberat").val()
  let isaktif = $("#input_add_isaktif").val()
  let beratvolume = $("#input_add_beratvolume").val().replace(/,/g, '')
  let partnumber = $("#input_add_partnumber").val()
  let lokasi = $("#input_add_lokasi").val()
  let kodesku = $("#input_add_kodesku").val()

  if (!kodegroup) {
    alertify.warning("KodeGroup harus diisi")
    return
  }
  if (!kodeheadgroup) {
    alertify.warning("KodeHeadGroup  harus diisi")
    return
  }
  if (!kodesubgroup) {
    alertify.warning("KodeSubGroup harus diisi")
    return
  }
  if (!kodesubkategori) {
    alertify.warning("KodeSubKategori harus diisi")
    return
  }
  if (!kodemerk) {
    alertify.warning("KodeMerk harus diisi")
    return
  }
  if (!kodebarang) {
    alertify.warning("KodeBarang harus diisi")
    return
  }
  if (!namabarang) {
    alertify.warning("NamaBarang harus diisi")
    return
  }
  if (!satuan || satuan == 0) {
    alertify.warning("Satuan 1 harus diisi")
    return
  }
  if (!isi) {
    alertify.warning("Isi 1 harus diisi")
    return
  }

  // Tombol dikunci selama simpan (klik ganda tidak membuat insert kedua).
  let $btn = $("#buttonSubmitMaster")
  if ($btn.prop('disabled')) { return }
  $btn.prop('disabled', true)
  let berhasil = false
  $.ajax({
    url: "{!! url('masterbarangspadd') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      isagen: Number(isagen),
      kodegroup,
      kodeheadgroup,
      kodesubgroup,
      kodesubkategori,
      kodemerk,
      kodebarang,
      namabarang,
      namabarang2,
      harga,
      harga2,
      harga3,
      satuan,
      satuan2,
      satuan3,
      isi: Number(isi),
      isi2: Number(isi2),
      isi3: Number(isi3),
      qtymax: Number(qtymax),
      qtymin: Number(qtymin),
      toleransi: Number(toleransi),
      isberat: Number(isberat),
      isaktif: Number(isaktif),
      beratvolume: Number(beratvolume),
      partnumber,
      lokasi,
      kodesku,
      proses: 0,
      istakein: 0,
      iskontrak
    },
    success: function(res) {
      if (res != 1) {
        alertify.warning(res)
        // Kode yang dibuat otomatis keburu dipakai user lain -> buat nomor urut berikutnya,
        // user cukup klik Simpan lagi.
        if (String(res).indexOf('sudah ada') >= 0) {
          buatKodeBarang()
          alertify.message('Kode Barang diperbarui menjadi ' + $("#input_add_kodebarang").val() + ', silakan Simpan lagi')
        }
      }  else {
        berhasil = true
        alertify.success('Barang telah ditambah')
        $("#formBarang").modal('hide')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
  if (!berhasil) { $btn.prop('disabled', false) }
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

function formatAngka (angkaString) {
  angkaString = parseFloat(angkaString).toFixed(2)
  let tempAngka = angkaString.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
  }
  temp1 += '.' + tempAngka[1]
  return temp1
}

</script>

@endsection
