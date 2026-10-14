@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Perintah Retur Jual (-)')
@section('title', 'SML - Perintah Retur Jual (-)')

@endsection

@section('css')
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">

{{-- Modal pemilih (Customer/No Invoice/No Beli/Barang) disamakan persis dengan modal
     pemilih menu Kas - lihat public/css/picker-kas.css, pola yang sama seperti
     so.blade.php / retursuratjalan.blade.php. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">

<style>
.toolbar {
  display: flex;
  align-items: center;
  gap: 10px;
}

.page-title {
  font-size: 19px;
  font-weight: 800;
  color: #1f2430;
}
.po-toolbar-search {
  display: flex;
  align-items: center;
  gap: 10px;
}
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

#content { padding-top: 12px; }

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

/* {{-- Kolom Aksi tabel/tabel2 -- pastel round-button treatment, copied and
     rescoped to this page's own #tabel/#tabel2 from so.blade.php's @section('css'). --}} */
#tabel td:first-child,
#tabel2 td:first-child {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel td:first-child .btn,
#tabel2 td:first-child .btn,
#addTable td:last-child .btn {
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
#tabel2 td:first-child .btn:hover,
#addTable td:last-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel td:first-child .btn-success,
#tabel2 td:first-child .btn-success,
#addTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}

#tabel td:first-child .btn-warning,
#tabel2 td:first-child .btn-warning,
#addTable td:last-child .btn-warning {
  color: #b45309; border-color: #fbe3bd; background: #fef3e0;
}

#tabel td:first-child .btn-primary,
#tabel2 td:first-child .btn-primary,
#addTable td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}

#tabel td:first-child .btn-danger,
#tabel2 td:first-child .btn-danger,
#addTable td:last-child .btn-danger {
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
  animation: prjMunculLoading .34s ease-out both;
}

@keyframes prjMunculLoading {
  0%, 45% { opacity: 0; }
  100% { opacity: 1; }
}

/* Hide action buttons until the row is hovered, port 1:1 dari pola
   .action-buttons-wrap milik master (public/css/tableMaster2.css) --
   scoped ke #tabel (satu-satunya tabel di halaman ini yang punya Actions). */
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

.btn-chip-biru {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
}

.btn-chip-biru:hover,
.btn-chip-biru:focus {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}

.btn-chip-biru:active {
  background-color: #cfdcff !important;
  border-color: #a8bdff !important;
  color: #1d4ed8 !important;
}

.data-table th,
.data-table td {
  white-space: nowrap;
}

/* ---- Form Add/Edit/Detail (bsgrid seperti purchasing) ----
   Disalin dari purchaseOrder.blade.php: textarea tetap setinggi atribut rows, tombol
   browse (kaca pembesar) setinggi input dan menempel dengan sudut membulat. */
#formBsGrid textarea.form-control,
#formBsGrid table .form-control {
  height: auto;
}
#formBsGrid .btn:has(> .bi-search) {
  height: 38px !important;
}
#formBsGrid .input-group .btn:has(> .bi-search) {
  height: auto !important;
  align-self: stretch;
  padding-top: 0;
  padding-bottom: 0;
}
.btn-chip-biru:has(> .bi-search) {
  border-radius: 6px !important;
}
.form-control:has(+ .btn > .bi-search) {
  border-top-right-radius: 6px !important;
  border-bottom-right-radius: 6px !important;
}
#formBsGrid .input-group { flex-wrap: nowrap; }

/* Header PRJ: tiga pasang label | isian per baris (grid .bs-form newmaster.css).
   Nama & alamat customer menempati kolom label+isian pertama tanpa label. Jarak antar
   baris diatur gap grid - tidak ada lagi margin-top negatif yang membuat Catatan menempel. */
#formBsGrid .bs-form.prj-bs-header {
  grid-template-columns: 90px minmax(0, 1fr) 100px minmax(0, 1fr) 80px minmax(0, 1fr);
  gap: 12px 14px;
  max-width: 1250px;
}
#formBsGrid .bs-form.prj-bs-header .prj-span-awal { grid-column: 1 / 3; }
#formBsGrid .bs-form .prj-label-atas { align-self: start; padding-top: 11px; }
#formBsGrid .bs-form .prj-isian-atas { align-self: start; }

/* Form Tambah/Edit Item: Kode Brg | No Beli, Nama Brg (membentang), Qty | Satuan,
   Retur Supp | Keterangan - kolom label cukup lebar supaya "Retur Supp" tetap sebaris. */
#formBsGrid .bs-form.prj-bs-item {
  grid-template-columns: 100px minmax(0, 1fr) 100px minmax(0, 1fr);
  gap: 12px 14px;
  max-width: 860px;
}
#formBsGrid .bs-form.prj-bs-item .bs-full { grid-column: 2 / -1; }
.prj-item-form h4 { margin: 4px 0 14px; }
.prj-table-wrap { overflow-x: auto; margin-top: 14px; }

/* Lapisan "Memuat data..." di modal pemilih - chip & spinner yang sama dengan tabel
   purchasing / menu marketing lain (.po-loading-chip dari partial tampilan-purchasing-css).
   Selama memuat, isi tabel lama disembunyikan dan badan modal diberi tinggi minimum. */
#form .modal-body { position: relative; }
#form .modal-body.prj-sedang-memuat { min-height: 220px; }
#form .modal-body.prj-sedang-memuat .dataTables_wrapper,
#form .modal-body.prj-sedang-memuat table { visibility: hidden; }
.prj-memuat-awal {
  position: relative;
  min-height: 180px;
}
.prj-picker-memuat {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 40;
  background: rgba(255, 255, 255, .62);
  animation: prjMunculLoading .34s ease-out both;
}

@media (max-width: 991.98px) {
  #formBsGrid .bs-form.prj-bs-header {
    grid-template-columns: 90px minmax(0, 1fr);
  }
  #formBsGrid .bs-form.prj-bs-header .prj-span-awal { grid-column: 1 / -1; }
}
@media (max-width: 575.98px) {
  #formBsGrid .bs-form.prj-bs-header,
  #formBsGrid .bs-form.prj-bs-item { grid-template-columns: 1fr; }
  #formBsGrid .bs-form.prj-bs-item .bs-full { grid-column: 1 / -1; }
  #formBsGrid .bs-form .prj-label-atas { padding-top: 0; }
}
</style>
@include('marketing.partials.tampilan-purchasing-css')
@endsection


@section('content')

<div id="page1" class="mainpage">

<div id="printContainer" style="display:none">



</div>
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

  {{-- Filter modal: so.blade.php's exact modalFilterPO markup/classes, adapted to
       PRJ's own single Status dropdown (otorisasi + proses state combined -- PRJ
       has no Tipe Bayar concept so that field is dropped). --}}
  <div class="modal fade rt-filter" id="modalFilter">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-funnel"></i>
            Filter Perintah Retur Jual (-)
            <span class="rt-active-badge" id="prjFilterBadge">0 aktif</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilter').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="rt-section">
            <div class="rt-group-label">Penyaringan Data</div>
            <div class="rt-grid-2">
              <div>
                <label class="rt-field-label" for="input_filterstatus">Status</label>
                <select class="rt-native" id="input_filterstatus">
                  <option value=0 selected>Semua</option>
                  <option value=1>Belum Diproses</option>
                  <option value=2>Proses Sebagian</option>
                  <option value=3>Selesai</option>
                </select>
              </div>
              <div>
                <label class="rt-field-label" for="input_filteroto">Otorisasi</label>
                <select class="rt-native" id="input_filteroto">
                  <option value=0 selected>Semua</option>
                  <option value=2>Sudah</option>
                  <option value=1>Belum</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="rt-reset-link" onclick="prjResetFilterFields()">Reset semua</button>
          <div class="rt-footer-buttons">
            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
              onclick="$('#modalFilter').modal('hide')">Batal</button>
            <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterPRJ(); $('#modalFilter').modal('hide');">Terapkan</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding: 0">
      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" onchange="onChangePeriodePRJ()" class="po-filter-inp" id="input_tanggalawal" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" onchange="onChangePeriodePRJ()" class="po-filter-inp" id="input_tanggalakhir" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
        </div>
        <input type="search" id="prjSearch1" class="po-search-inp" placeholder="Cari data">
        <div class="po-len-wrap">
          <label for="prjLen1">Tampilkan</label>
          <select id="prjLen1" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" onclick="$('#modalFilter').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button type="button" class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
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

<div id="page2" style="display: none" class="mainpage container-fluid" >
<div id="formBsGrid">

  <div class="row">
    <div class="col-8 text-left">
      {{-- <h2>Form PRJ</h2> --}}
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()"  >
        Close
      </button>
    </div>
  </div>

  <div id="modalAdd" class="">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />
    <input type="hidden" id="input_add_flagtipe" value="">
    <input type="hidden" id="input_add_ppn" value="">

    {{-- Header PRJ: grid .bs-form tiga pasang label | isian per baris (lihat .prj-bs-header di
         @section css). Nama & alamat customer ditaruh di bawah kode customer tanpa label,
         sama seperti tampilan sebelumnya. --}}
    <div class="bs-form prj-bs-header">
      <label>Customer</label>
      <div class="input-group">
        <input type="text" class="form-control" id="input_add_kodecustomer" placeholder="" disabled>
        <button type="button" class="btn btn-chip-biru btn-sm" id="buttonAddListCustomer" title="Cari" onclick="buttonAddListCustomer()"><i class="bi bi-search"></i></button>
      </div>

      <label>No Bukti</label>
      <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>

      <label>Tanggal</label>
      <input type="date" class="form-control text-center" id="input_add_tanggal" value="" disabled>

      <input type="text" class="form-control prj-span-awal" id="input_add_namacustomer" placeholder="Nama Customer" disabled>

      {{-- PRJ Minus tidak memakai invoice: No Invoice & No SO selalu '-' (programajcSML). --}}
      <label>No Invoice</label>
      <input type="text" class="form-control" id="input_add_noinvoice" value="-" disabled>

      <label>No SO</label>
      <input type="text" class="form-control text-left" id="input_add_noso" value="-" disabled>

      <textarea style="resize: none" rows=3 class="form-control prj-span-awal" id="input_add_alamatcustomer" placeholder="Alamat Customer" disabled></textarea>

      <label class="prj-label-atas">Catatan</label>
      <textarea style="resize: none" rows=3 class="form-control" id="input_add_catatan"></textarea>

      {{-- Gudang dipilih sendiri lewat dropdown (aturan menu marketing: Gudang = select).
           Terkunci setelah item pertama tersimpan - satu PRJ Minus satu gudang. --}}
      <label class="prj-label-atas">Gudang</label>
      <select class="form-control prj-isian-atas" id="input_add_gudang" onchange="onChangeGudangDropdown()">
        <option value=""></option>
        @foreach ($listGudang as $gdg)
        <option value="{{ $gdg->KODEGDG }}">{{ $gdg->KODEGDG }} - {{ $gdg->NAMA }}</option>
        @endforeach
      </select>
    </div>

    <hr/>

    <div class="prj-table-wrap">
      <table id="addTable" class="data-table">
        <thead class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
            <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
            <th style="padding: 4px 12px;" scope="col">Sat Produk</th>
            <th style="padding: 4px 12px;" scope="col">Qty</th>
            <th style="padding: 4px 12px;" scope="col">Sat</th>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
          </tr>
        </thead>
        <tbody id="addTableData" class="" >
          <tr>
            <td colspan=7 class="text-center">Belum ada data</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="text-right mt-2">
      <button type="button" class="btn btn-lg btn-chip-biru" onclick="buttonAddAddItem()" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              transition: background-color 0.3s, box-shadow 0.3s;" >Tambah Item</button>
    </div>

    {{-- Form Tambah Item --}}
    <div id="formAddAdd" class="showhideitem prj-item-form">
      <hr/>
      <h4>Tambah Item</h4>

      <div class="bs-form prj-bs-item">
        {{-- Kode barang bisa diketik lalu Enter (langsung terpilih bila hasilnya satu) atau
             dicari lewat tombol kaca pembesar, sama seperti programajcSML. --}}
        <label>Kode Brg</label>
        <div class="input-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" placeholder="Kode Barang" autocomplete="off" onkeypress="onKeyPressBarang(event)">
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-chip-biru btn-sm" id="buttonAddListBarang" title="Cari"><i class="bi bi-search"></i></button>
        </div>

        {{-- PRJ Minus: No Beli selalu '-' tanpa pilihan (programajcSML). --}}
        <label>No Beli</label>
        <div>
          <input id="AddAddUrutBeli" type="hidden" class="form-control" value="0">
          <input id="AddAddNoBeli" type="text" class="form-control" value="-" disabled>
        </div>

        <label>Nama Brg</label>
        <input id="AddAddNamaBrg" type="text" class="form-control bs-full" disabled>

        <label>Qty</label>
        <input id="AddAddInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQty()">

        <label>Satuan</label>
        <div>
          <input id="AddAddInputIsi" type="hidden" class="form-control" disabled>
          <select id="AddAddInputNosat" class="form-control" onchange="onChangeQty()">
            <option value=0 selected></option>
          </select>
        </div>

        <label>Retur Supp</label>
        <select id="AddAddReturSupp" class="form-control">
          <option value=0 selected>Tidak</option>
          <option value=1 >Ya</option>
        </select>

        <label>Keterangan</label>
        <input id="AddAddKeterangan" type="text" class="form-control" maxlength="100">
      </div>

      <input id="AddAddInputQty1" type="hidden" value='0.00'>
      <input id="AddAddInputSat1" type="hidden" value='PCS'>
      <input id="AddAddInputQty2" type="hidden" value='0.00'>
      <input id="AddAddInputSat2" type="hidden" value='BOX'>

      <div class="text-right mt-4">
        <button type="button" class="btn btn-lg btn-batal-add" onclick="buttonAddBatal()" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Batal</button>

        <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-lg btn-chip-biru" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Simpan</button>
      </div>
    </div>

    {{-- Form Edit Item --}}
    <div id="formAddEdit" class="showhideitem prj-item-form">
      <hr/>
      <h4>Edit Item</h4>

      <div class="bs-form prj-bs-item">
        <label>Kode Brg</label>
        <input id="AddEditKodeBrg" type="text" class="form-control" disabled>

        <label>No Beli</label>
        <div>
          <input id="AddEditUrutBeli" type="hidden" class="form-control" >
          <input id="AddEditNoBeli" type="text" class="form-control" disabled>
        </div>

        <label>Nama Brg</label>
        <input id="AddEditNamaBrg" type="text" class="form-control bs-full" disabled>

        <label>Qty</label>
        <input id="AddEditInputQty" type="number" value='0.00' class="form-control text-right" onchange="onChangeQtyEdit()">

        <label>Satuan</label>
        <div>
          <input id="AddEditInputIsi" type="hidden" class="form-control" disabled>
          <select id="AddEditInputNosat" class="form-control" onchange="onChangeQtyEdit()">
            <option value=0 selected></option>
          </select>
        </div>

        <label>Retur Supp</label>
        <select id="AddEditReturSupp" class="form-control">
          <option value=0 selected>Tidak</option>
          <option value=1 >Ya</option>
        </select>

        <label>Keterangan</label>
        <input id="AddEditKeterangan" type="text" class="form-control" maxlength="100">
      </div>

      <input id="AddEditInputQty1" type="hidden" value='0.00'>
      <input id="AddEditInputSat1" type="hidden" value='PCS'>
      <input id="AddEditInputQty2" type="hidden" value='0.00'>
      <input id="AddEditInputSat2" type="hidden" value='BOX'>

      <div class="text-right mt-4">
        <button type="button" class="btn btn-lg btn-batal-add" onclick="buttonAddBatal()" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Batal</button>

        <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-lg btn-chip-biru" style="
                height: 30px;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Simpan</button>
      </div>
    </div>

  </div>
</div>{{-- /#formBsGrid --}}
</div>


<div id="page3" style="display: none" class="mainpage container-fluid" >
<div id="formBsGrid">

  <div class="row">
    <div class="col-8 text-left">
      {{-- <h2>Detail PRJ</h2> --}}
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-danger btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" onclick="buttonCloseForm()"  >
      Close
    </button>
    </div>
  </div>

  <div id="modalDetail" class="">
    <input type="hidden" name="noUrut" id="input_detail_nourut" value="" />
    <input type="hidden" id="input_detail_flagtipe" value="">
    <input type="hidden" id="input_detail_ppn" value="">

    <div class="bs-form prj-bs-header">
      <label>Customer</label>
      <input type="text" class="form-control" id="input_detail_kodecustomer" placeholder="" disabled>

      <label>No Bukti</label>
      <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>

      <label>Tanggal</label>
      <input type="date" class="form-control text-center" id="input_detail_tanggal" value="" disabled>

      <input type="text" class="form-control prj-span-awal" id="input_detail_namacustomer" placeholder="Nama Customer" disabled>

      <label>No Invoice</label>
      <input type="text" class="form-control" id="input_detail_noinvoice" value="" disabled>

      <label>No SO</label>
      <input type="text" class="form-control text-left" id="input_detail_noso" value="" disabled>

      <textarea style="resize: none" rows=3 class="form-control prj-span-awal" id="input_detail_alamatcustomer" placeholder="Alamat Customer" disabled></textarea>

      <label class="prj-label-atas">Catatan</label>
      <textarea style="resize: none" rows=3 class="form-control" id="input_detail_catatan" disabled></textarea>

      <label class="prj-label-atas">Gudang</label>
      <input type="text" class="form-control prj-isian-atas" id="input_detail_gudang" value="" disabled>
    </div>

    <hr/>

    <div class="prj-table-wrap">
      <table id="detailTable" class="data-table">
        <thead class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
            <th style="padding: 4px 12px;" scope="col">Nama Produk</th>
            <th style="padding: 4px 12px;" scope="col">Sat Produk</th>
            <th style="padding: 4px 12px;" scope="col">Qty</th>
            <th style="padding: 4px 12px;" scope="col">Sat</th>
          </tr>
        </thead>
        <tbody id="detailTableData" class="" >
          <tr>
            <td colspan=6 class="text-center">Belum ada data</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>{{-- /#formBsGrid --}}
</div>


<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document">
    <div id="" class="modal-content ">

      <div id= "modalAddListCustomer" class="showhidemodalbodyadd">
      <div class="modal-header">

          <h5 class="modal-title" id="">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px; ">
            <!-- <div class="container-fluid"> -->

            <table id="tabel_add_list_customer" class="data-table" style="overflow:auto; " >
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode</th>
                  <th style="padding: 4px 12px;" scope="col">Nama</th>
                  <th style="padding: 4px 12px;" scope="col">Kota</th>

                </tr>
              </thead>

              <tbody id="tabel_data_add_list_customer" class="text-left" ></tbody>

            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>
            </div>

        </div>

      </div>

      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-lg btn-batal-add" data-dismiss="modal" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">Batal</button>
        </div>
      </div>

      {{-- Pemilih barang PRJ Minus: barang dicari ke master barang (aktif, grup BJ/JS) lewat
           kotak cari + Enter, sama seperti programajcSML - daftarnya terlalu besar untuk
           dimuat sekaligus. Bagian pemilih No Invoice / No Beli tidak dipakai di menu ini. --}}
      <div id= "modalAddListBarang" class="showhidemodalbodyadd">
      <div class="modal-header">
          <h5 class="modal-title" id="">Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid mt-4" >
          <div class="row">
            <div class="col-12">
              <h3></h3>
            </div>
          </div>
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:-60px;">

            <table id="tabel_add_list_barang" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                  <th style="padding: 4px 12px;" scope="col">Sat1</th>
                  <th style="padding: 4px 12px;" scope="col">Sat2</th>
                </tr>
              </thead>

              <tbody id="tabel_data_add_list_barang" class="text-left" ></tbody>

            </table>
          </div>
            </div>
            </div>

        </div>

      </div>

      <div id="" class="modal-footer ">
        <button type="button" class="btn btn-batal-add" onclick="buttonAddListBatal()" >Batal</button>
      </div>
      </div>



      </div>







    </div>
  </div>

<!-- End modal add-->








@endsection

@section('js')
@include('marketing.partials.tampilan-purchasing-js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
{{-- picker-kas.js sudah dimuat oleh partial tampilan-purchasing-js di atas. --}}
{{-- Modal pemilih bersama (#form) disamakan persis dengan modal pemilih menu Kas.
     Class picker-kas dipasang dari sini (bukan di markup modalnya) - lihat pola yang sama
     di purchaseOrder.blade.php / marketing/so.blade.php / retursuratjalan.blade.php. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

// let tempNoBukti = ''



let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''
let xppn = 0

/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel/poInitReportTableSekali milik
 * purchaseOrder.blade.php, sama seperti so.blade.php/invoicejasa.blade.php/
 * cetaktandaterima.blade.php. Endpoint persistensinya saveheadertable/
 * getheadertable (HeaderTableController). Disederhanakan jadi satu tabel saja
 * (urut 1) setelah tab Belum/Sudah Otorisasi digabung jadi satu daftar dengan
 * filter Status PRJ (lihat modalFilter di section('content')), sama seperti
 * so.blade.php sudah tidak punya tab otorisasi terpisah untuk tab SO-nya.
 */
let prjCart = []
const PRJ_HREF = 'perintahreturjualminus'
const PRJ_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const PRJ_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }

function prjPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) {
    if (k.toLowerCase() === lower) { return row[k]; }
  }
  return undefined;
}

function prjDefaultCart () {
  return [
    ['NOBUKTI',      'No. Bukti',   1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',     1, 'date',    0, 0],
    ['NAMACUSTSUPP', 'Nama Cust',   1, 'varchar', 0, 0],
    ['NoRPJ',        'No Invoice',  1, 'varchar', 0, 0],
    ['NOSO',         'No SO',       1, 'varchar', 0, 0],
    ['IDUser',       'User',        1, 'varchar', 0, 0],
    ['OtoUser1',     'User Oto1',   1, 'varchar', 0, 0],
    ['TglOto1',      'Tgl Oto1',    1, 'date',    0, 0],
    ['xstatus',       'Status',      1, 'varchar', 0, 0]
  ]
}

function prjBuatCart (headers, values, isnumerics, isshowns, desimals) {
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
      PRJ_TIPE_NAMA[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
    ])
  });
  return cart
}

window.g_href = PRJ_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function () {
  let cart = prjCart || []

  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1])
    value.push(c[0])
    isnumber.push(PRJ_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0)
    desimal.push(Number(c[5]) || 0)
  });

  // Async: dulu async:false - browser terkunci total selama menunggu server, dan saat
  // server lambat Chrome memunculkan "Page Unresponsive". Simpan pengaturan kolom tidak
  // perlu ditunggu; tampilan sudah memakai prjCart yang baru.
  return $.ajax({
    url   : "{!! url('saveheadertable') !!}",
    type  : "post",
    data  : {
      _token   : $("#_token").val(),
      header   : JSON.stringify(header),
      isnumber : JSON.stringify(isnumber),
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : PRJ_HREF,
      urut     : 1
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Async (dulu async:false, penyebab "Page Unresponsive" saat halaman dibuka).
// - Muat biasa: pengaturan kolom tersimpan diambil, lalu onSelesai() dipanggil untuk
//   menggambar tabel.
// - Reset kolom (dipanggil ReportTable/buttonHeaderTable yang langsung menggambar ulang
//   setelahnya): kolom default dipasang SEKETIKA, penghapusan pengaturan lama + simpan
//   default berjalan di latar (berurutan: hapus dulu, baru simpan).
window.doSetHeader = function (mode, reset, onSelesai) {
  if (reset) {
    prjCart = prjDefaultCart()
    window.gcart_header = prjCart
  }

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    data  : {
      _token : $("#_token").val(),
      href   : PRJ_HREF,
      urut   : 1,
      reset  : reset ? 1 : 0
    },
    success : function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        prjCart = prjBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        prjCart = prjDefaultCart()
        window.gcart_header = prjCart
        window.doSimpanHeader()
      }
      window.gcart_header = prjCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      prjCart = prjDefaultCart()
      window.gcart_header = prjCart
    },
    complete : function () {
      if (typeof onSelesai === 'function') { onSelesai() }
    }
  })
}

let prjRtSudahInit = false
function prjInitReportTableSekali () {
  if (prjRtSudahInit || typeof ReportTable === 'undefined') { return }
  prjRtSudahInit = true

  ReportTable.init({ table : '#tabel', bar : '#rtBarTabel', onChange : reinitTabel })

  let prjGuardUlangKlik = false;
  let thead = document.querySelector('#tabel thead')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (prjGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      prjGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      prjGuardUlangKlik = false
    }, true)
  }
}

function tulisTheadHeaderPRJ (cols) {
  let thead = document.querySelector('#tabel thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
    .replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>');
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

// Belum=merah, Sebagian=kuning, Selesai=biru -- sama seperti badge status di
// master/newsetpemakai.blade.php, pakai .sp-badge yang sudah dimuat lewat
// po-table-header.css (lihat juga poBadgeStatus() di purchaseOrder.blade.php).
const PRJ_BADGE_STATUS = {
  'Belum':    'is-inactive',
  'Sebagian': 'is-supervisor',
  'Selesai':  'is-user',
}

function prjValueCell (row, col) {
  let raw = prjPickCI(row, col[0]);
  let type = col[3];

  if (col[0] === 'xstatus') {
    let kelas = PRJ_BADGE_STATUS[raw] || '';
    return { html: '<span class="sp-badge ' + kelas + '">' + prjEsc(raw || '') + '</span>', cls: '' };
  }

  if (type === 'date') {
    if (!raw) { return { html: '', cls: '' }; }
    return { html: formatDate(raw, '/').split('/').reverse().join('/'), cls: '' };
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return { html: n.toLocaleString('en-US', { minimumFractionDigits: dp, maximumFractionDigits: dp }), cls: 'text-right' };
  }
  if (type === 'bool') {
    return Number(raw)
      ? { html: '<i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i>', cls: 'text-success text-center' }
      : { html: '<i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i>', cls: 'text-danger text-center' };
  }
  return { html: prjEsc(raw !== undefined && raw !== null ? raw : ''), cls: '' };
}

// Actions gabungan Belum/Sudah Otorisasi (dulu dua tab terpisah). PRJ Minus tidak punya
// cetak (programajcSML), jadi tanpa tombol print. Mengembalikan isi sel saja.
function tabelActionsCell (row) {
  let nobukti = prjPickCI(row, 'NOBUKTI');
  let isOto = Number(prjPickCI(row, 'IsOtorisasi1'));
  let html = '<div class="action-buttons-wrap">';
  html += '<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail(\'' + nobukti + '\' , \'detail\')"><i class="bi bi-info"></i></button>';
  if (isOto) {
    html += '<button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-key"></i></button>';
  } else {
    html += '<button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit(\'' + nobukti + '\' , \'edit\')"><i class="bi bi-pen"></i></button>';
    html += '<button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\' , \'add\')"><i class="bi bi-key"></i></button>';
  }
  html += '</div>';
  return html;
}

// Kolom DataTables untuk tabel daftar. Baris dibuat dari data (deferRender): hanya baris di
// halaman yang tampil yang dibuat elemennya - dulu semua baris ditulis ke <tbody> sekaligus,
// sehingga rentang tanggal lebar / "Semua" membuat browser terkunci beberapa detik.
function prjKolomTabel (cols) {
  let kolom = [{
    data: null,
    orderable: false,
    className: 'text-center',
    render: function (v, type, row) { return type === 'display' ? tabelActionsCell(row) : '' }
  }]
  cols.forEach(function (col) {
    kolom.push({
      data: null,
      render: function (v, type, row) { return prjValueCell(row, col).html },
      createdCell: function (td, v, row) {
        let cls = prjValueCell(row, col).cls
        if (cls) { td.className = cls }
      }
    })
  })
  return kolom
}

// Tooltip tombol aksi (dipasang partial tampilan-purchasing-js di <body>) bisa nyangkut:
// tombol diklik -> #page1 disembunyikan / dialog terbuka, jadi mouseleave tidak pernah
// terjadi; atau baris digambar ulang dan tombolnya hilang tanpa tooltip-nya ditutup.
// Pola yang sama dengan invoicepenjualan / cetaktandaterima.
function prjTutupTooltip () {
  if ($.fn.tooltip) { $('#page1 [data-toggle="tooltip"]').tooltip('hide') }
  $('body > .tooltip').remove()
}
$(document).on('click', '#page1 .action-buttons-wrap .btn', function () {
  prjTutupTooltip()
  this.blur()
})
$(document).on('preDraw.dt', '#tabel', prjTutupTooltip)

function renderTabelRows (rows) {
  let cols = (prjCart.length ? prjCart : gcart_header).filter(function (c) { return c[2] === 1; });
  prjTutupTooltip();
  document.getElementById('tabel_data').innerHTML = '';
  tulisTheadHeaderPRJ(cols);
  return cols;
}

let lastTabelRows = []
let prjPanjangHalaman = 10

function prjIkatSearch () {
  let input = document.getElementById('prjSearch1')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#tabel')) {
        $('#tabel').DataTable().search(nilai).draw()
      }
    }, 400)
  })
}

function prjIkatPanjangHalaman () {
  let sel = document.getElementById('prjLen1')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(prjPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    prjPanjangHalaman = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#tabel')) {
      $('#tabel').DataTable().page.len(prjPanjangHalaman).draw()
    }
  })
}

const PRJ_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

function reinitTabel () {
  try {
    // Kata pencarian yang sedang aktif dipertahankan setelah tabel dibangun ulang.
    let cari = $('#prjSearch1').val() || ''
    if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().clear().destroy(); }
    let cols = renderTabelRows(lastTabelRows);
    $('#tabel').DataTable({
      dom: PRJ_DOM_STRING,
      data: lastTabelRows || [],
      columns: prjKolomTabel(cols),
      deferRender: true,
      lengthChange: false,
      pageLength: prjPanjangHalaman,
      paging: true,
      ordering: false,
      search: { search: cari },
    });
    prjIkatSearch();
    prjIkatPanjangHalaman();
  } catch (e) {
    console.error('reinitTabel failed:', e);
    alertify.error('Gagal memperbarui tabel: ' + e.message);
  }
}

function buttonHeaderTable () {
  alertify.confirm('Reset Kolom', 'Kembalikan kolom tabel ke tampilan default?', function () {
    window.doSetHeader(1, true)
    reinitTabel()
    alertify.success('Kolom telah direset ke tampilan default')
  }, function () {})
}

// Filter modal ("Status PRJ") + periode -- port 1:1 dari
// soResetFilterFields()/buttonFilterSO()/onChangePeriodeSO() milik so.blade.php,
// disederhanakan (PRJ tidak punya field Tipe Bayar).
function prjResetFilterFields () {
  $('#input_filterstatus').val('0');
  $('#input_filteroto').val('0');
}

function prjUpdateFilterBadge () {
  let aktif = 0
  if (Number($('#input_filterstatus').val()) !== 0) { aktif++ }
  if (Number($('#input_filteroto').val()) !== 0) { aktif++ }
  $('#prjFilterBadge').text(aktif + ' aktif')
}

function buttonFilterPRJ () {
  prjUpdateFilterBadge()
  // Endpoint & parameter sama; loadAll() async supaya layar tidak membeku saat memuat.
  loadAll()
}

function onChangePeriodePRJ () {
  let awal = $("#input_tanggalawal").val()
  let akhir = $("#input_tanggalakhir").val()
  if (!awal || !akhir) { return }
  if (awal > akhir) {
    alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
    return
  }
  buttonFilterPRJ()
}

$(document).ready(function(){

      lastTabelRows = @json($tempOutstanding);

      // Pengaturan kolom dimuat async; selama menunggu tampil "Memuat data..." (gaya
      // .po-loading-chip purchasing), tabel digambar begitu jawabannya tiba.
      $('#tabel').before('<div id="prjMemuatAwal" class="prj-memuat-awal"><span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span></div>')
      window.doSetHeader(1, false, function () {
        $('#prjMemuatAwal').remove()
        reinitTabel();
        prjInitReportTableSekali();
      });

      // Tabel modal pemilih (customer/no invoice/barang/no beli) tidak diinisialisasi di sini
      // lagi: diisi saat tombol browse ditekan lewat prjIsiPicker().
});



function formatAngkaX (angka) {
  if (Number(angka) == 0) {
    return '0.00'
  } else {

    return formatAngka(parseFloat(angka).toFixed(2))
  }

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
}

// Async: No Bukti dikosongkan dulu, submitAddAdd() menolak simpan selama masih kosong.
// prjUrutNoBukti memastikan hanya jawaban permintaan terakhir yang dipakai (customer
// diganti-ganti cepat).
let prjUrutNoBukti = 0
function setNewNoBukti (xval = 1) {
  let urutIni = ++prjUrutNoBukti
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_nourut").value = ''
  $.ajax({
    url: "{!! url('perintahreturjualminusspnobukti') !!}",
    type: "get",
    data: {
      ppn: xval
    },
    success: function(res) {
      if (urutIni !== prjUrutNoBukti || !res || !res[0]) { return }
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mengambil No Bukti, silahkan pilih customer lagi')
    }})
}


function cleanFormAddAdd () {
  document.getElementById("AddAddKodeBrg").value = ''
  document.getElementById("AddAddNamaBrg").value = ''
  // PRJ Minus: No Beli selalu '-' (programajcSML).
  document.getElementById("AddAddNoBeli").value = '-'
  document.getElementById("AddAddUrutBeli").value = 0
  document.getElementById("AddAddInputQty").value = '0.00'
  // Pilihan satuan dikosongkan lagi supaya satuan barang sebelumnya tidak ikut terkirim.
  document.getElementById("AddAddInputNosat").innerHTML = '<option value=0 selected></option>'
  document.getElementById("AddAddInputNosat").value = 0

  document.getElementById("AddAddInputIsi").value = 0

  document.getElementById("AddAddReturSupp").value = 0

  document.getElementById("AddAddKeterangan").value = ''
  tempBarangAddAdd = {}
  // AddAddReturSupp

}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanFormAdd () {


  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_nobukti").value = ''
  // PRJ Minus tanpa invoice: No Invoice & No SO '-' (programajcSML).
  document.getElementById("input_add_noinvoice").value = '-'
  document.getElementById("input_add_catatan").value = ''
  // Tanggal diisi dari server di buttonAdd() (prjIsiTanggalServer), bukan dari jam komputer user.
  document.getElementById("input_add_tanggal").value = ''
  document.getElementById("input_add_noso").value = '-'
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("input_add_flagtipe").value = ''
  document.getElementById("input_add_ppn").value = ''
  document.getElementById("input_add_nourut").value = ''
  dataBarang = []
  tempBarangAddEdit = {}
  cleanFormAddAdd()




  // document.getElementById("input_add_tanggalpo").valueAsDate = new Date()
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_kodepelanggan").value = ''
  // document.getElementById("input_add_namapelanggan").value = ''
  // document.getElementById("input_add_alamatpelanggan").value = ''
  // document.getElementById("input_add_kodealamatkirim").value = ''
  // document.getElementById("input_add_alamatkirim").value = ''
  // document.getElementById("input_add_kodepic").value = ''
  // document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodelokasipenerima").value = ''
  // document.getElementById("input_add_alamatlokasipenerima").value = ''
  // document.getElementById("input_add_catatan").value = ''
  // document.getElementById("input_add_valas").value = ''
  // document.getElementById("input_add_kurs").value = ''
  // document.getElementById("input_add_dp").value = '0.00'
  // document.getElementById("input_add_nopo").value = ''
  // document.getElementById("input_add_kodebackoffice").value = ''
  // document.getElementById("input_add_namabackoffice").value = ''
  // document.getElementById("input_add_tipeppn").value = 0
  // document.getElementById("input_add_pembayaran").value = 0
  // document.getElementById("input_add_kodesales").value = ''
  // document.getElementById("input_add_namasales").value = ''
  // document.getElementById("input_add_hari").value = 0
  // document.getElementById("input_add_draftpo").value = 0
  //
  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_dp").disabled = false
  // document.getElementById("input_add_nopo").disabled = false
  // document.getElementById("input_add_catatan").disabled = false
  // document.getElementById("input_add_tanggalpo").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false
  //
  // document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListAlamatKirim").disabled = false
  // document.getElementById("buttonAddListSales").disabled = false
  // document.getElementById("buttonAddListValas").disabled = false
  // document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  // document.getElementById("buttonAddListBackOffice").disabled = false
  //
  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false
  //
  // document.getElementById("input_add_disc").value = '0.00'
  // document.getElementById("input_add_discrp").value = '0.00'
  // document.getElementById("input_add_ppn").value = '0.00'
  // document.getElementById("input_add_dpp").value = '0.00'
  // document.getElementById("input_add_grandtotal").value = '0.00'
}


function submitAdd () {
  console.log("submitAdd")
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()

  let tanggal  = $("#input_add_tanggal").val()
  let nopajak  = $("#input_add_nopajak").val()

  console.log(nobukti , tanggal , nopajak)

}


// Penjaga klik ganda: selama satu simpan/hapus berjalan, klik berikutnya diabaikan (dulu
// klik dua kali pada Simpan bisa menyimpan item dua kali).
let prjSedangSimpan = false

// Daftar di halaman utama cukup dimuat ulang saat form ditutup (buttonCloseForm), tidak
// setiap kali item disimpan/dihapus - menghemat satu request per simpan.
let prjDaftarPerluMuat = false

function prjKunciSimpan (kunci) {
  prjSedangSimpan = kunci
  $('#buttonSubmitAddAdd, #buttonSubmitAddEdit').prop('disabled', kunci)
}

// Jawaban server {status: 0, pesan} = ditolak (mis. PRJ sudah diotorisasi user lain).
function prjDitolak (res) {
  return !!res && typeof res === 'object' && Number(res.status) === 0
}

function submitAddEdit () {
  if (prjSedangSimpan) { return }
  let barang = tempBarangAddEdit

  if (jQuery.isEmptyObject(barang)) {
    alertify.warning("Pilih item yang akan diedit")
    return
  }

  let _token  = $("#_token").val()

  let choice = "U"
  let urut = barang.URUT

  let jmlrecord = 1

  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodecustsupp = $("#input_add_kodecustomer").val()
  let kodegdg = $("#input_add_gudang").val()
  let noinvoice = $("#input_add_noinvoice").val()
  let flagtipe = $("#input_add_flagtipe").val()
  let ppn = $("#input_add_ppn").val()
  let catatan = $("#input_add_catatan").val()
  let retursupp = $("#AddEditReturSupp").val()
  let tanggal = $("#input_add_tanggal").val()
  let noso = $("#input_add_noso").val()

  let nobeli = $("#AddEditNoBeli").val()
  let urutbeli = $("#AddEditUrutBeli").val()
  let namabrg = $("#AddEditNamaBrg").val();
  let kodebrg = $("#AddEditKodeBrg").val();
  let qnt = $("#AddEditInputQty").val();

  let nosat = $("#AddEditInputNosat").val();
  let isi = 0
  if (nosat == 1) {
    isi = barang.ISI1
  } else if (nosat == 2) {
    isi = barang.ISI2
  }
  let urutinvoice = 0
  let qnt1 = $("#AddEditInputQty1").val();
  let sat1 = $("#AddEditInputSat1").val();
  let qnt2 = $("#AddEditInputQty2").val();
  let sat2 = $("#AddEditInputSat2").val();
  let ketdet = $("#AddEditKeterangan").val();

  if (!Number(nosat)) {
    alertify.warning("Satuan belum dipilih")
    return
  }
  if (Number(qnt) <= 0) {
    alertify.warning("qnt <= 0")
    return
  }
  // PRJ Minus tidak terikat invoice, jadi tidak ada cek qty sisa (programajcSML).

  prjKunciSimpan(true)
  $.ajax({
    url: "{!! url('perintahreturjualminusspadd') !!}",
    type: "post",
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      noinvoice,
      kodecustsupp,
      catatan,
      urut,
      urutinvoice,
      kodebrg,
      qnt,
      qnt1,
      qnt2,
      sat1,
      nosat,
      isi,
      jmlrecord,
      namabrg,
      kodegdg,
      flagtipe,
      ppn,
      noso,
      retursupp,
      sat2,
      ketdet,
      nobeli,
      urutbeli
    },
    success: function(res) {
      if (prjDitolak(res)) {
        alertify.warning(res.pesan)
        prjDaftarPerluMuat = true
        refreshDataTableAdd(nobukti)
        return
      }
      if (res == 1) {
        $('.showhideitem').hide();
        prjDaftarPerluMuat = true
        refreshDataTableAdd(nobukti)
        alertify.success('Berhasil mengubah item')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    },
    complete: function () { prjKunciSimpan(false) }
  })
}

function buttonAddDeleteItem (i) {

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let barang = dataBarang[i]
  if (!barang) { return }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + prjEsc(barang.NAMABRG) + ' ?',
      function() {
        if (prjSedangSimpan) { return }
        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = barang.NOBUKTI
        let urut = barang.URUT
        prjKunciSimpan(true)
        $.ajax({
          url: "{!! url('perintahreturjualminusspadd') !!}",
          type: "post",
          data: {
            _token,
            choice,
            nobukti,
            nourut : '',
            tanggal : '',
            noinvoice : '-',
            kodecustsupp : '',
            catatan :'',
            urut,
            urutinvoice :0,
            kodebrg:'',
            qnt:0,
            qnt1:0,
            qnt2:0,
            sat1:'',
            nosat:0,
            isi:0,
            jmlrecord:1,
            namabrg:'',
            kodegdg:'',
            flagtipe:0,
            ppn:0,
            noso:'',
            retursupp:0,
            sat2:'',
            ketdet:'',
            nobeli:'',
            urutbeli:0

          },
          success: function(res) {
            prjDaftarPerluMuat = true
            if (prjDitolak(res)) {
              alertify.warning(res.pesan)
              refreshDataTableAdd(nobukti)
              return
            }
            $('.showhideitem').hide();
            // Item terakhir terhapus = PRJ ikut terhapus (sp_TransPRRJUAL); refreshDataTableAdd
            // akan menutup form dan kembali ke daftar.
            refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          },
          complete: function () { prjKunciSimpan(false) }

        })
      }
    ,function(){
    });
}

function submitAddAdd () {
  if (prjSedangSimpan) { return }

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  let barang = tempBarangAddAdd

  let jmlrecord = 0

  if (tipeform == "edit") {
    jmlrecord = 1
  }

  // Cek periode hanya untuk PRJ baru (tanggal = tanggal server). Menambah item ke PRJ yang
  // sudah ada tidak mengubah tanggalnya, dan aksi Edit tidak dibatasi periode.
  if (jmlrecord == 0) {
    let tglInput = $("#input_add_tanggal").val()
    if (!tglInput) {
      alertify.warning("Tanggal server belum terambil, silahkan tutup form lalu tekan Tambah lagi");
      return
    }
    let checkDate = new Date(tglInput)
    if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
        alertify.warning("Tanggal tidak sesuai periode");
        return
    }
  }

  let _token  = $("#_token").val()

  let choice = "I"
  let urut = 0

  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let kodecustsupp = $("#input_add_kodecustomer").val()
  let kodegdg = $("#input_add_gudang").val()
  let noinvoice = $("#input_add_noinvoice").val()
  let flagtipe = $("#input_add_flagtipe").val()
  let ppn = $("#input_add_ppn").val()
  let catatan = $("#input_add_catatan").val()
  let retursupp = $("#AddAddReturSupp").val()
  let tanggal = $("#input_add_tanggal").val()
  let noso = $("#input_add_noso").val()

  let nobeli = $("#AddAddNoBeli").val()
  let urutbeli = $("#AddAddUrutBeli").val()
  let namabrg = $("#AddAddNamaBrg").val();
  let kodebrg = $("#AddAddKodeBrg").val();
  let qnt = $("#AddAddInputQty").val();

  let nosat = $("#AddAddInputNosat").val();
  let isi = 0
  if (nosat == 1) {
    isi = barang.Isi1
  } else if (nosat == 2) {
    isi = barang.Isi2
  }
  let urutinvoice = 0
  let qnt1 = $("#AddAddInputQty1").val();
  let sat1 = $("#AddAddInputSat1").val();
  let qnt2 = $("#AddAddInputQty2").val();
  let sat2 = $("#AddAddInputSat2").val();

  let ketdet = $("#AddAddKeterangan").val();
  if (!kodegdg) {
    alertify.warning("Pilih gudang terlebih dahulu")
    return
  }
  if (!kodebrg || !kodecustsupp || !noinvoice || !Number(nosat)) {
    alertify.warning("Data tidak lengkap")
    return
  }
  // Kode barang bisa diketik ulang setelah memilih - harus tetap barang yang dipilih
  // (programajcSML).
  if (tempBarangAddAdd.KodeBrg != kodebrg) {
    alertify.warning("Barang tidak sesuai dengan pilihan")
    return
  }
  if (!nobukti) {
    alertify.warning("No Bukti belum terisi, silahkan pilih customer lagi")
    return
  }
  if (Number(qnt) <= 0) {
    alertify.warning("Qty kurang dari 0")
    return
  }
  // PRJ Minus tidak terikat invoice, jadi tidak ada cek qty sisa (programajcSML).

  prjKunciSimpan(true)
  $.ajax({
    url: "{!! url('perintahreturjualminusspadd') !!}",
    type: "post",
    data: {
      _token,
      choice,
      nobukti,
      nourut,
      tanggal,
      noinvoice,
      kodecustsupp,
      catatan,
      urut,
      urutinvoice,
      kodebrg,
      qnt,
      qnt1,
      qnt2,
      sat1,
      nosat,
      isi,
      jmlrecord,
      namabrg,
      kodegdg,
      flagtipe,
      ppn,
      noso,
      retursupp,
      sat2,
      ketdet,
      nobeli,
      urutbeli
    },
    success: function(res) {
      if (prjDitolak(res)) {
        alertify.warning(res.pesan)
        if (jmlrecord == 1) {
          prjDaftarPerluMuat = true
          refreshDataTableAdd(nobukti)
        }
        return
      }
      if (res == 1) {
        tipeform = 'edit'
        lockFormAdd()
        $('.showhideitem').hide();
        prjDaftarPerluMuat = true
        refreshDataTableAdd(nobukti)

        alertify.success('Berhasil menambah item')
      }
      if(res == 2) {
        setNewNoBukti(xppn)
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
      }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    },
    complete: function () { prjKunciSimpan(false) }
  })


}

// ---- Modal pemilih (browse) ----
// Data dimuat async (layar tidak membeku), tabel diisi lewat opsi data + deferRender
// sehingga hanya baris di halaman yang tampil yang dibuat (customer besar bisa ribuan
// invoice). Klik baris mengambil data barisnya langsung - tidak lewat onclick berisi teks,
// jadi nama/alamat yang mengandung tanda kutip tetap bisa dipilih. _pk: true supaya partial
// tampilan-purchasing-js tidak membangun ulang tabel ini.
function prjEsc (v) {
  return String(v === null || v === undefined ? '' : v)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;')
}

function prjKolomTeks (field, kelas) {
  return {
    data: field,
    defaultContent: '',
    className: kelas || '',
    render: function (v) { return prjEsc(v) }
  }
}

function prjIsiPicker (idTabel, rows, columns, onPilih, opsi) {
  let sel = '#' + idTabel
  if ($.fn.DataTable.isDataTable(sel)) { $(sel).DataTable().clear().destroy() }
  $(sel + ' tbody').empty()

  let dt = pickerKasInit(idTabel, $.extend({
    _pk: true,
    data: rows || [],
    columns: columns,
    deferRender: true,
    order: [[0, 'asc']],
    createdRow: function (tr) { tr.classList.add('pick-row') }
  }, opsi || {}))

  $(sel + ' tbody').off('click.prj').on('click.prj', 'tr.pick-row', function () {
    let data = dt.row(this).data()
    if (data) { onPilih(data) }
  })
  return dt
}

function prjBukaPicker (idBagian) {
  $('.showhidemodalbodyadd').hide()
  $('#' + idBagian).show()
  $('#form').modal('show')
}

// Modal pemilih dibuka lebih dulu, lalu lapisan "Memuat data..." (gaya .po-loading-chip
// purchasing / menu marketing lain) tampil di badan modal selama data diambil. Isi tabel
// lama disembunyikan supaya tidak terlihat data customer/invoice sebelumnya.
function prjModalMemuat (idBagian, tampil) {
  let $body = $('#' + idBagian + ' .modal-body')
  $body.children('.prj-picker-memuat').remove()
  $body.toggleClass('prj-sedang-memuat', !!tampil)
  if (!tampil) { return }
  $body.append(
    '<div class="prj-picker-memuat"><span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span></div>'
  )
}

// Penanda permintaan browse terakhir: jawaban dari browse sebelumnya (modal sudah ditutup
// atau sudah membuka browse lain) tidak dipakai.
let prjNomorBrowse = 0

function prjMulaiBrowse (idBagian) {
  let nomor = ++prjNomorBrowse
  prjBukaPicker(idBagian)
  prjModalMemuat(idBagian, true)
  return nomor
}

function prjBrowseMasihAktif (nomor) {
  return nomor === prjNomorBrowse
}

// Modal ditutup (Batal / X / klik luar) = browse yang masih memuat dibatalkan.
$(function () {
  $('#form').on('hidden.bs.modal', function (e) {
    if (e.target !== this) { return }
    prjNomorBrowse++
    $('#form .prj-picker-memuat').remove()
    $('#form .modal-body').removeClass('prj-sedang-memuat')
  })
})

function prjSelesaiBrowse (idBagian) {
  prjModalMemuat(idBagian, false)
}

// Browse gagal: tutup modal yang masih menampilkan "Memuat data...".
function prjGagalBrowse (idBagian, err) {
  console.log(err)
  prjModalMemuat(idBagian, false)
  if ($('#' + idBagian).is(':visible')) { buttonAddListBatal() }
  alertify.warning('Terjadi kesalahan silahkan refresh browser')
}

// ---- Barang PRJ Minus: dicari ke master barang (aktif, grup BJ/JS) ----
// Sama seperti programajcSML: kode/nama diketik di kolom Kode Brg lalu Enter (langsung terpilih
// bila hasilnya tepat satu), atau lewat tombol kaca pembesar yang membuka modal dengan kotak
// cari. Hasil pencarian diisi ke tabel lewat prjIsiPicker (deferRender, klik baris).

function prjSyaratBarang () {
  if (!$("#input_add_kodecustomer").val() || !$("#input_add_gudang").val()) {
    alertify.warning("Pilih customer & gudang terlebih dahulu")
    return false
  }
  return true
}

// Cari barang dari master barang - pola purchasing/pembelianpermintaanagen:
// - kolom Kode Barang + Enter: hasil satu langsung terpilih, lebih dari satu / tidak ada
//   membuka modal (kosong = "Tidak ada data"), kolom kosong = peringatan;
// - modal memakai kotak Search bawaan tabel pemilih: mengetik menyaring hasil yang tampil,
//   Enter mencari ke server, Enter dengan kotak kosong mengosongkan tabel.
let prjKataBarang = ''

function prjIsiPickerBarang (rows, kata, pesanKosong) {
  prjKataBarang = kata || ''
  listBarang = rows || []
  listBarang.forEach(function (item, i) { item._i = i })
  prjIsiPicker('tabel_add_list_barang', listBarang, [
    prjKolomTeks('KodeBrg'),
    prjKolomTeks('NamaBrg'),
    prjKolomTeks('SAT1'),
    prjKolomTeks('SAT2')
  ], function (item) { buttonAddPickBarang(item._i) }, {
    order: [[0, 'asc']],
    search: { search: prjKataBarang },
    language: {
      lengthMenu: 'Tampilkan _MENU_',
      emptyTable: pesanKosong || 'Ketik kode / nama barang di kotak Search, lalu tekan Enter',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  })

  $('#tabel_add_list_barang_filter input')
    .off('keypress.prj')
    .on('keypress.prj', searchBarangAll)
}

function prjFokusCariBarang () {
  let inp = $('#tabel_add_list_barang_filter input')
  inp.trigger('focus')
  let n = inp.val() ? inp.val().length : 0
  if (inp[0] && inp[0].setSelectionRange) { inp[0].setSelectionRange(n, n) }
}

function prjAmbilBarang (search) {
  return $.ajax({
    url: "{!! url('perintahreturjualminuslistbarang') !!}",
    type: "get",
    data: { search: search }
  })
}

// Kolom Kode Barang: Enter = cari (pola pembelianpermintaanagen / programajcSML).
let prjSedangCariKode = false
function onKeyPressBarang (e) {
  if (e.which != 13) { return }
  e.preventDefault()
  if (prjSedangCariKode) { return }
  if (!prjSyaratBarang()) { return }

  let search = $('#AddAddKodeBrg').val().trim()
  if (!search) {
    alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.")
    return
  }

  prjSedangCariKode = true
  prjAmbilBarang(search).then(function (res) {
    res = res || []
    if (res.length === 1) {
      listBarang = res
      buttonAddPickBarang(0)
      return
    }
    prjNomorBrowse++
    prjIsiPickerBarang(res, search, res.length ? '' : 'Tidak ada data')
    prjModalMemuat('modalAddListBarang', false)
    prjBukaPicker('modalAddListBarang')
    $('#form').one('shown.bs.modal', prjFokusCariBarang)
  }, function (err) {
    console.log(err)
    alertify.warning('Terjadi kesalahan, silakan refresh browser')
  }).always(function () {
    prjSedangCariKode = false
  })
}

// Tombol kaca pembesar: buka modal dengan kotak Search kosong (pola pembelianpermintaanagen).
function buttonAddListBarang () {
  if (!prjSyaratBarang()) { return }

  prjNomorBrowse++
  prjIsiPickerBarang([], '')
  prjModalMemuat('modalAddListBarang', false)
  prjBukaPicker('modalAddListBarang')
  $('#form').one('shown.bs.modal', prjFokusCariBarang)
}

// Kotak Search di modal barang: Enter = cari ke master barang. Selama menunggu tampil
// lapisan "Memuat data..." di modal.
function searchBarangAll (e) {
  if (e.which != 13) { return }
  e.preventDefault()

  let search = $(e.target).val().trim()
  if (!search) {
    prjIsiPickerBarang([], '')
    prjFokusCariBarang()
    return
  }

  let nomor = ++prjNomorBrowse
  prjModalMemuat('modalAddListBarang', true)
  prjAmbilBarang(search).then(function (res) {
    if (!prjBrowseMasihAktif(nomor)) { return }
    res = res || []
    prjIsiPickerBarang(res, search, res.length ? '' : 'Tidak ada data')
    prjSelesaiBrowse('modalAddListBarang')
    prjFokusCariBarang()
  }, function (err) {
    if (!prjBrowseMasihAktif(nomor)) { return }
    prjGagalBrowse('modalAddListBarang', err)
  })
}

// Daftar customer jarang berubah: dimuat sekali per halaman (mulai dimuat di latar saat
// tombol Tambah ditekan) lalu dipakai ulang, jadi browse customer berikutnya langsung tampil.
let prjCacheCustomer = null
let prjAjaxCustomer = null

function prjMuatCustomer () {
  if (prjCacheCustomer) { return $.Deferred().resolve(prjCacheCustomer).promise() }
  if (prjAjaxCustomer) { return prjAjaxCustomer }
  prjAjaxCustomer = $.ajax({
    url: "{!! url('perintahreturjualminuslistcustomer') !!}",
    type: "get"
  }).then(function (res) {
    prjCacheCustomer = res || []
    return prjCacheCustomer
  }).always(function () {
    prjAjaxCustomer = null
  })
  return prjAjaxCustomer
}

function prjIsiPickerCustomer (rows) {
  prjIsiPicker('tabel_add_list_customer', rows, [
    prjKolomTeks('KODECUSTSUPP'),
    prjKolomTeks('NAMACUSTSUPP'),
    prjKolomTeks('NamaKota')
  ], function (item) {
    buttonAddPickCustomer(item.KODECUSTSUPP, item.NAMACUSTSUPP, item.ALAMAT1, item.PPN)
  })
}

function buttonAddListCustomer () {
  // Sudah ada di cache: modal langsung tampil berisi data, tanpa animasi memuat.
  if (prjCacheCustomer) {
    prjNomorBrowse++
    prjIsiPickerCustomer(prjCacheCustomer)
    prjModalMemuat('modalAddListCustomer', false)
    prjBukaPicker('modalAddListCustomer')
    return
  }

  let nomor = prjMulaiBrowse('modalAddListCustomer')
  prjMuatCustomer().then(function (rows) {
    if (!prjBrowseMasihAktif(nomor)) { return }
    prjIsiPickerCustomer(rows)
    prjSelesaiBrowse('modalAddListCustomer')
  }, function (err) {
    if (!prjBrowseMasihAktif(nomor)) { return }
    prjGagalBrowse('modalAddListCustomer', err)
  })
}


function buttonAddPickCustomer (kode, nama , alamat,ppn) {
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama || ''
  document.getElementById("input_add_alamatcustomer").value = alamat || ''
  // PRJ Minus: invoice & SO '-', gudang dipilih ulang setelah ganti customer (programajcSML).
  document.getElementById("input_add_noinvoice").value = '-'
  document.getElementById("input_add_noso").value = '-'
  document.getElementById("input_add_gudang").value = ''
  document.getElementById("input_add_flagtipe").value = ''
  document.getElementById("input_add_ppn").value = ''
  $('.showhideitem').hide();

  if (ppn && Number(ppn) == 1) {
    setNewNoBukti(1)
    xppn =1

  } else {
    setNewNoBukti(0)
    xppn =0

  }
  buttonAddListBatal()
}
function onChangeQtyEdit () {

  let qty = $("#AddEditInputQty").val();
  let nosat = $("#AddEditInputNosat").val();
  if (!jQuery.isEmptyObject(tempBarangAddEdit)) {
    let tempIsi = nosat == 1 ? tempBarangAddEdit.ISI1 : tempBarangAddEdit.ISI2
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddEditInputQty1").value = tempTotalQty / tempBarangAddEdit.ISI1
    document.getElementById("AddEditInputQty2").value = tempTotalQty / tempBarangAddEdit.ISI2
  }
}

function onChangeQty () {

  let qty = $("#AddAddInputQty").val();
  let nosat = $("#AddAddInputNosat").val();

  if (!jQuery.isEmptyObject(tempBarangAddAdd)) {
    let tempIsi = nosat == 1 ? tempBarangAddAdd.Isi1 : tempBarangAddAdd.Isi2
    let tempTotalQty = Number(tempIsi) * Number(qty)

    document.getElementById("AddAddInputQty1").value = tempTotalQty / tempBarangAddAdd.Isi1
    document.getElementById("AddAddInputQty2").value = tempTotalQty / tempBarangAddAdd.Isi2
  }

}

// Barang dari master barang: qty mulai 0, satuan default SAT1 (programajcSML).
function buttonAddPickBarang (index) {
  tempBarangAddAdd = listBarang[index]
  if (!tempBarangAddAdd) { return }

  document.getElementById("AddAddKodeBrg").value = tempBarangAddAdd.KodeBrg
  document.getElementById("AddAddNamaBrg").value = tempBarangAddAdd.NamaBrg || ''
  document.getElementById("AddAddInputQty").value = "0.00"
  document.getElementById("AddAddInputQty1").value = "0.00"
  document.getElementById("AddAddInputQty2").value = "0.00"

  document.getElementById("AddAddInputSat1").value = tempBarangAddAdd.SAT1
  document.getElementById("AddAddInputSat2").value = tempBarangAddAdd.SAT2

  let selectOption = ''
  if (tempBarangAddAdd.SAT1) {
    selectOption += `<option value=1 selected>SAT1 - ${prjEsc(tempBarangAddAdd.SAT1)}</option>`
  }
  if (tempBarangAddAdd.SAT2) {
    selectOption += `<option value=2 >SAT2 - ${prjEsc(tempBarangAddAdd.SAT2)}</option>`
  }
  document.getElementById("AddAddInputNosat").innerHTML = selectOption || '<option value=0 selected></option>'

  buttonAddListBatal()
  // Jeda seperti pembelianpermintaanagen: modal yang sedang menutup mengembalikan fokus ke
  // tombol pembukanya, jadi fokus ke Qty dipasang setelah itu.
  setTimeout(function () { $('#AddAddInputQty').trigger('focus').trigger('select') }, 350)
}

// Ganti gudang = form item ditutup (programajcSML buttonAddPickGudang).
function onChangeGudangDropdown () {
  $('.showhideitem').hide();
}


function buttonAddBatal () {

  $('.showhideitem').hide();
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // 'hide', bukan 'toggle': toggle bisa membuka modal lagi kalau modal sudah tertutup.
  // Modal yang masih dalam animasi membuka mengabaikan 'hide' (Bootstrap 4), mis. barang
  // langsung terpilih karena hasil cari cuma satu - tutup begitu animasinya selesai.
  let $m = $('#form')
  let modal = $m.data('bs.modal')
  if (modal && modal._isTransitioning && modal._isShown) {
    $m.one('shown.bs.modal', function () { $m.modal('hide') })
  } else {
    $m.modal('hide')
  }
}


function closeShowHideItem () {
  $('.showhideitem').hide();

}

// Tanggal selalu disabled (diisi dari server saat Tambah, dari data saat Edit). Gudang ikut
// dikunci setelah item pertama tersimpan - satu PRJ Minus satu gudang.
function unlockFormAdd () {
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_gudang").disabled = false

  document.getElementById("buttonAddListCustomer").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_gudang").disabled = true

  document.getElementById("buttonAddListCustomer").disabled = true

}

// Tanggal PRJ baru diambil dari jam server SQL (getdate), sama seperti purchasing
// (purchaseOrder / pembelianpermintaanagen), supaya tidak bisa diakali lewat jam komputer user.
function prjIsiTanggalServer () {
  document.getElementById("input_add_tanggal").value = ''
  $.ajax({
    url: "{!! url('gettanggalserver') !!}",
    type: "get",
    success: function(res) {
      if (tipeform === 'add' && res && res[0]) {
        document.getElementById("input_add_tanggal").value = res[0].tanggal
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mengambil tanggal server, silahkan refresh browser')
    }
  })
}

function prjIsiTabelItem (res) {
  let rowTable = ""
  res.forEach((item, i) => {
    rowTable += `<tr>
    <td>${prjEsc(item.KODEBRG)}</td>
    <td>${prjEsc(item.NAMABRG)}</td>
    <td>${prjEsc(item.NAMAPRODUK)}</td>
    <td>${prjEsc(item.SATX ? item.SATX : item.SAT)}</td>
    <td class="text-right">${item.QNT ? formatAngkaX(item.QNT) : '0.00'}</td>
    <td>${prjEsc(item.SAT)}</td>
    <td class="text-center">
    <button title="Edit" class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
     <button title="Hapus" class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button></td>
    </tr>`
  });
  document.getElementById("addTableData").innerHTML = rowTable
}

// Muat ulang item & header PRJ di form Add/Edit. Mengembalikan promise berisi data (array);
// array kosong = PRJ sudah tidak ada (item terakhir dihapus) -> form ditutup.
function refreshDataTableAdd (NOBUKTI = "") {

  if (!NOBUKTI) {
    return $.Deferred().resolve([]).promise()
  }

  let _token  = $("#_token").val()
  return $.ajax({
    url: "{!! url('perintahreturjualminusgetdetail') !!}",
    type: "post",
    data: {
      _token,
      nobukti: NOBUKTI
    }
  }).then(function(res) {
    res = res || []
    if (res.length == 0) {
      alertify.warning("Data habis")
      dataBarang = []
      prjDaftarPerluMuat = true
      buttonCloseForm()
      return res
    }

    dataBarang = res
    prjIsiTabelItem(res)

    document.getElementById("input_add_kodecustomer").value = dataBarang[0].KodeCustSupp
    document.getElementById("input_add_namacustomer").value = dataBarang[0].NAMACUSTSUPP || ''
    document.getElementById("input_add_alamatcustomer").value = dataBarang[0].ALAMAT1 || ''
    document.getElementById("input_add_noinvoice").value = dataBarang[0].NoRPJ
    document.getElementById("input_add_noso").value = dataBarang[0].NOSO
    document.getElementById("input_add_gudang").value = dataBarang[0].KodeGdg || ''
    document.getElementById("input_add_catatan").value = dataBarang[0].Catatan || ''
    document.getElementById("input_add_flagtipe").value = dataBarang[0].FlagTipe
    document.getElementById("input_add_ppn").value = dataBarang[0].TipePPN
    // Tanggal PRJ yang sedang diedit (dulu tidak diisi, sehingga form edit menampilkan
    // tanggal hari ini).
    document.getElementById("input_add_tanggal").value = dataBarang[0].Tanggal ? formatDate(dataBarang[0].Tanggal) : ''

    document.getElementById("input_add_nobukti").value = dataBarang[0].NOBUKTI
    document.getElementById("input_add_nourut").value = dataBarang[0].NOURUT
    return res
  }, function (err) {
    console.log(err)
    alertify.warning('Terjadi kesalahan silahkan refresh browser')
    return $.Deferred().reject(err).promise()
  })
}

function buttonDetail (nobukti) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('perintahreturjualminusgetdetail') !!}",
    type: "post",
    data: {
      _token,
      nobukti: nobukti
    },
    success: function(res) {
      if (!res || !res.length) {
        alertify.warning("Data tidak ditemukan")
        loadAll()
        return
      }

      let rowTable = ""
      res.forEach((item, i) => {

        rowTable += `<tr>
        <td>${prjEsc(item.KODEBRG)}</td>
        <td>${prjEsc(item.NAMABRG)}</td>
        <td>${prjEsc(item.NAMAPRODUK)}</td>
        <td>${prjEsc(item.SATX ? item.SATX : item.SAT)}</td>
        <td class="text-right">${item.QNT ? formatAngkaX(item.QNT) : '0.00'}</td>
        <td>${prjEsc(item.SAT)}</td>
        </tr>`
      });

      document.getElementById("detailTableData").innerHTML = rowTable

      document.getElementById("input_detail_kodecustomer").value = res[0].KodeCustSupp
      document.getElementById("input_detail_namacustomer").value = res[0].NAMACUSTSUPP || ''
      document.getElementById("input_detail_alamatcustomer").value = res[0].ALAMAT1 || ''
      document.getElementById("input_detail_noinvoice").value = res[0].NoRPJ
      document.getElementById("input_detail_noso").value = res[0].NOSO
      document.getElementById("input_detail_gudang").value = res[0].KodeGdg || ''
      document.getElementById("input_detail_catatan").value = res[0].Catatan || ''
      document.getElementById("input_detail_flagtipe").value = res[0].FlagTipe
      document.getElementById("input_detail_ppn").value = res[0].TipePPN
      // Dulu tanggal detail tidak diisi (selalu tampil tanggal hari ini).
      document.getElementById("input_detail_tanggal").value = res[0].Tanggal ? formatDate(res[0].Tanggal) : ''

      document.getElementById("input_detail_nobukti").value = res[0].NOBUKTI
      document.getElementById("input_detail_nourut").value = res[0].NOURUT

      $('.mainpage').hide();
      $('#page3').show();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

// Cek kunci periode (ceklockperiode) - dipakai Tambah & Edit, sama seperti sebelumnya.
function prjCekKunciPeriode () {
  return $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get"
  }).then(function (res) {
    if (res && res.length) {
      alertify.warning("Periode sudah dikunci")
      return $.Deferred().reject('kunci').promise()
    }
  }, function (err) {
    console.log(err)
    alertify.warning('Terjadi kesalahan silahkan refresh browser')
    return $.Deferred().reject(err).promise()
  })
}

function prjCariBaris (nobukti) {
  return (lastTabelRows || []).find(function (r) { return prjPickCI(r, 'NOBUKTI') === nobukti })
}

let prjSedangBuka = false

function buttonEdit (nobukti) {
  if (prjSedangBuka) { return }

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  prjSedangBuka = true
  prjCekKunciPeriode().then(function () {
    tipeform = 'edit'
    cleanFormAdd()
    lockFormAdd()
    $('.showhideitem').hide();
    return refreshDataTableAdd(nobukti)
  }).then(function (res) {
    if (!res || !res.length) { return }

    if (dataBarang[0].IsOtorisasi1 == 1) {
      alertify.warning("Sudah diotorisasi")
      loadAll()
      return
    }
    if (Number(dataBarang[0].IsProses) === 1) {
      alertify.warning("PRJ " + nobukti + " sudah diproses Retur Penjualan Gudang, tidak bisa diedit")
      return
    }

    $('.mainpage').hide();
    $('#page2').show();
  }).always(function () {
    prjSedangBuka = false
  })
}

function buttonAdd (nobukti) {
  if (prjSedangBuka) { return }

  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  prjSedangBuka = true
  prjCekKunciPeriode().then(function () {
    tipeform = 'add'
    cleanFormAdd()
    document.getElementById("addTableData").innerHTML = `<tr><td colspan=7 class="text-center">Belum ada data</td></tr>`
    unlockFormAdd()
    $('.showhideitem').hide();
    $('.showhideform').hide();
    $('#modalAdd').show();

    prjIsiTanggalServer()
    // Daftar customer mulai dimuat di latar supaya browse customer langsung tampil.
    prjMuatCustomer()

    $('.mainpage').hide();
    $('#page2').show();
  }).always(function () {
    prjSedangBuka = false
  })

}

function buttonAddAddItem () {

  if (!$("#input_add_kodecustomer").val() || !$("#input_add_gudang").val()) {
    alertify.warning("Pilih customer & gudang terlebih dahulu")
    return
  }
  $('.showhideitem').hide();
  cleanFormAddAdd()
  $('#formAddAdd').show();

}

function buttonAddEditItem (i) {
  tempBarangAddEdit = dataBarang[i]
  if (!tempBarangAddEdit) { return }
  document.getElementById("AddEditKodeBrg").value = tempBarangAddEdit.KODEBRG
  document.getElementById("AddEditReturSupp").value = Number(tempBarangAddEdit.FlagKembali) === 1 ? 1 : 0
  document.getElementById("AddEditNoBeli").value = tempBarangAddEdit.nobeli || ''
  document.getElementById("AddEditUrutBeli").value = tempBarangAddEdit.urutbeli || 0
  document.getElementById("AddEditNamaBrg").value = tempBarangAddEdit.NAMABRG || ''
  document.getElementById("AddEditInputQty").value = parseFloat(tempBarangAddEdit.QNT).toFixed(2)
  document.getElementById("AddEditInputQty1").value = parseFloat(tempBarangAddEdit.QNT1).toFixed(2)
  document.getElementById("AddEditInputQty2").value = parseFloat(tempBarangAddEdit.QNT2).toFixed(2)
  document.getElementById("AddEditInputSat1").value = tempBarangAddEdit.SAT_1
  document.getElementById("AddEditInputSat2").value = tempBarangAddEdit.SAT_2

  document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.KetDetail || ''

  let selectOption = ''
  if (tempBarangAddEdit.SAT_1) {
    selectOption += `<option value=1 ${tempBarangAddEdit.NoSat == 1 ? 'selected' : ''}>SAT1 - ${prjEsc(tempBarangAddEdit.SAT_1)}</option>`
  }
  if (tempBarangAddEdit.SAT_2) {
    selectOption += `<option value=2 ${tempBarangAddEdit.NoSat == 2 ? 'selected' : ''}>SAT2 - ${prjEsc(tempBarangAddEdit.SAT_2)}</option>`
  }
  document.getElementById("AddEditInputNosat").innerHTML = selectOption

  $('.showhideitem').hide();
  $('#formAddEdit').show();

}

function buttonCloseForm () {
  $('.mainpage').hide();
  $('#page1').show();

  if (prjDaftarPerluMuat) {
    prjDaftarPerluMuat = false
    loadAll()
  }
}

let prjAjaxLoadAll = null

function loadAll () {

  let _token = $("#_token").val();
  let tglawal = $("#input_tanggalawal").val()
  let tglakhir = $("#input_tanggalakhir").val()
  let filterstatus = $("#input_filterstatus").val()
  let filteroto = $("#input_filteroto").val()

  // Permintaan lama dibatalkan supaya hasil yang lebih lama tidak menimpa yang terbaru.
  if (prjAjaxLoadAll) { prjAjaxLoadAll.abort() }
  prjAjaxLoadAll = $.ajax({
    url: "{!! url('perintahreturjualminusloadall') !!}",
    type: "get",
    data: { _token, tglawal, tglakhir, filterstatus, filteroto },
    success: function(res) {
      lastTabelRows = res.tempOutstanding;
      reinitTabel();
    },
    error: function (err) {
      if (err && err.statusText === 'abort') { return }
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    },
    complete: function () { prjAjaxLoadAll = null }
  })

}

// Pola batal otorisasi yang disepakati (purchasing / SO): prompt berjudul "Batal Otorisasi",
// keterangan wajib diisi (prompt tetap terbuka bila kosong), dicatat ke LoggingData('btloto').
function buttonBatalOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let baris = prjCariBaris(nobukti)
  if (baris && prjPickCI(baris, 'xstatus') && prjPickCI(baris, 'xstatus') !== 'Belum') {
    alertify.warning("PRJ " + nobukti + " sudah diproses Retur Penjualan Gudang, tidak bisa batal otorisasi")
    return
  }

  alertify.prompt('Batal Otorisasi', "Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    let xpket = String(value || '').trim();

    if (!xpket) {
      alertify.warning('Keterangan harus diisi.');
      return false;
    }
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('perintahreturjualminusspbatalotorisasi') !!}",
      type: "post",
      data: {
        _token,
        nobukti,
        pket : xpket
      },
      success: function(res) {
        loadAll()
        if (prjDitolak(res)) {
          alertify.warning(res.pesan)
          return
        }
        alertify.success('Berhasil batal otorisasi')
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  }
  ,function(){
    alertify.error("Action cancelled");
  });

}

// Konfirmasi sebelum otorisasi, sama seperti programajcSML.
function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Otorisasi', 'Otorisasi PRJ ' + nobukti + ' ?',
    function() {
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('perintahreturjualminusspotorisasi') !!}",
        type: "post",
        data: {
          _token,
          nobukti
        },
        success: function(res) {
          loadAll()
          if (prjDitolak(res)) {
            alertify.warning(res.pesan)
            return
          }
          alertify.success('Berhasil update otorisasi')
        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }

      })
    }
    ,function(){
    });
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
