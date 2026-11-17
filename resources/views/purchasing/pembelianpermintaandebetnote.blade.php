@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Debet Note')

@endsection

@section('css')
{{-- js/canvas/functions.js (dimuat layout) mencari plugin di path relatif "js/" - dari URL
     /pembelianpermintaandebetnote itu jadi 404 (plugins.easing.js). scwJsPath adalah variabel resmi template
     Canvas untuk menunjuk folder plugin-nya; didefinisikan di sini supaya layout tidak diubah. --}}
<script>var scwJsPath = "{!! URL::asset('js/canvas') !!}";</script>
<style>
  /* Form Add/Edit/Detail/Otorisasi dibungkus div#formBsGrid supaya input memakai gaya
     #formBsGrid di public/css/newmaster.css (tinggi 38px, sudut 8px, border halus, abu-abu saat
     disabled). Dua pengecualian tinggi:
     - textarea tetap setinggi aslinya (atribut rows), supaya isi beberapa baris tetap terbaca;
     - input di dalam tabel item ikut gaya baru tapi tingginya kembali ke ukuran aslinya
       (termasuk form-control-sm), supaya baris tabel tidak ikut lebih tinggi. */
  #formBsGrid textarea.form-control,
  #formBsGrid table .form-control {
    height: auto;
  }

  /* Tombol browse (kaca pembesar) di dalam #formBsGrid disamakan tingginya dengan input.
     Banyak tombol masih membawa inline style="height:32px" dari tata letak lama, sedangkan
     input di sini 38px - karena itu !important. Tombol yang berada di .input-group dilepas
     tingginya supaya meregang (align-items: stretch) mengikuti input di sebelahnya: 38px di
     form, dan tetap setinggi input di dalam sel tabel. Tombol yang berdiri sendiri (bukan di
     .input-group) dipaku 38px. */
  #formBsGrid .btn:has(> .bi-search) {
    height: 38px !important;
  }

  /* Padding atas-bawah dinolkan: dengan padding 8px tombol setinggi 35,5px, lebih tinggi dari
     input (34,25px) - stretch hanya bisa memanjangkan, jadi tombol menonjol ke bawah. Tanpa
     padding vertikal tinggi tombol sepenuhnya mengikuti input di sebelahnya. */
  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
    padding-top: 0;
    padding-bottom: 0;
  }
</style>
  {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi + modal
     filter) --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
<style>
  /* Isi tabel di semua tab dibuat sebaris (tidak turun ke bawah). Kolom yang panjang
     cukup digeser lewat scroll horizontal .po-table-wrap (overflow:auto). */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }
</style>
<style>
  /* Tombol browse (kaca pembesar) tetap menempel ke input, tapi sudutnya membulat
     (bukan kotak). !important untuk menimpa inline
     style="border-radius:0" dan aturan .input-group bawaan Bootstrap. */
  .btn-chip-biru:has(> .bi-search),
  .btn-browsing:has(> .bi-search) {
    border-radius: 6px !important;
  }
  /* Input di kiri tombol browse ikut membulat di sisi kanannya (lewati input hidden). */
  .form-control:has(+ .btn > .bi-search),
  .form-control:has(+ :is([type=hidden], [hidden]) + .btn > .bi-search),
  .form-control:has(+ .input-group-append > .btn > .bi-search) {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
  }
</style>
<style>
  /* Dropdown "Tampilkan" (jumlah data) di modal browsing - kontrol length bawaan DataTables
     (lengthChange + lengthMenu 10/25/50/100/Semua), digaya seperti .po-len-wrap di newpo.
     Letaknya kiri atas, sejajar tepi kiri tabel; kotak pencarian tetap di kanan pada baris
     yang sama. Hanya baris toolbar yang memang berisi dropdown ini yang diatur (:has), jadi
     browsing barang yang lengthChange-nya false tidak ikut berubah. !important di flex/max-width
     untuk menimpa aturan "#form .dataTables_wrapper > .row:first-child > div { flex: 0 0 100% }"
     yang memaksa tiap kolom toolbar selebar tabel. */
  .modal .dataTables_wrapper > .row:first-child:has(.dataTables_length) {
    align-items: center;
    margin-bottom: 8px;
  }

  .modal .dataTables_wrapper > .row:first-child:has(.dataTables_length) > div {
    flex: 1 1 auto !important;
    max-width: none !important;
    width: auto;
  }

  .modal .dataTables_wrapper > .row:first-child:has(.dataTables_length) .dataTables_filter {
    margin-bottom: 0 !important;
  }

  .modal .dataTables_wrapper .dataTables_length label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    background: var(--rt-card, #FFFFFF);
    border: 1.5px solid var(--rt-border, #E7E8F0);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rt-ink-soft, #6B7180);
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
  }

  .modal .dataTables_wrapper .dataTables_length select {
    width: auto;
    height: auto;
    margin: 0;
    border: none !important;
    border-radius: 0;
    box-shadow: none !important;
    font-size: 13px;
    font-weight: 700;
    color: var(--rt-ink, #1D2130);
    text-transform: none;
    letter-spacing: normal;
    outline: none;
    cursor: pointer;
    padding: 2px 20px 2px 0 !important;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: transparent url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>") no-repeat right center !important;
  }
</style>
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di area yang bisa di-scroll --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
{{-- Modal pemilih (Perkiraan/Barang/Supplier/Customer/dst.) disamakan persis dengan modal pemilih menu Kas - lihat public/css/picker-kas.css. --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
<style>
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }
/* Halaman ini dirancang mengisi tinggi layar (lihat dnAturTinggiTabel()), jadi padding
   atas. */
#content { padding-top: 12px; }

/* Rule .card global di layout newmasterx (flex + align-items:center + efek melayang saat
   hover) untuk kartu menu dashboard, bukan kartu berisi tabel. */
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

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
   style pada <table>, yang mengalahkan `.data-table { width: 100% }`. Dipakai min-width,
   BUKAN width, dan di-scope lewat ID (bukan class) karena DataTables meng-clone tabel
   sambil membuang id saat mengukur kolom. */
#tabel2 {
  min-width: 100%;
}

/* ---------- Kolom Aksi tabel (#tabel2) - tombol bulat kecil, warna pastel, disalin dari
   uangmukabeli.blade.php supaya tampilannya seragam dengan menu purchasing lain. ---------- */
#tabel2 td:first-child:not([colspan]) {
  vertical-align: middle;
}

#tabel2 td:first-child .po-aksi-wrap {
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

#tabel2 td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel2 td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel2 td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel2 td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel2 td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

/* Tombol di kolom Action baru muncul saat barisnya di-hover. */
table.data-table.po-aksi-hover tbody td:first-child .btn {
  visibility: hidden;
  opacity: 0;
  transition: opacity .12s ease;
}
table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
  visibility: visible;
  opacity: 1;
}

/* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar - tidak ada di
   po-table-header.css, ditulis di sini supaya perubahan cukup mengunggah file blade-nya saja. */
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

/* ---------- #addTable (form Add) dan #detailTable (form Detail) - header abu-abu
   uppercase + zebra + hover, disalin dari pembelianpermintaanagen.blade.php. ---------- */
#addTable thead th,
#detailTable thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee;
  border-top: none;
}

#addTable tbody tr:nth-of-type(odd),
#detailTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#addTable tbody tr:hover,
#detailTable tbody tr:hover { background-color: #f5f3ff; }

#addTable thead th:nth-child(n+3),
#addTable tbody td:nth-child(n+3):not([colspan]) { text-align: center; }
#detailTable thead th:nth-child(n+3),
#detailTable tbody td:nth-child(n+3):not([colspan]) { text-align: center; }

#addTable td:last-child:not([colspan]) {
  white-space: nowrap;
}
#addTable td:last-child .btn {
  border-radius: 6px;
  font-size: 12px;
  padding: 4px 8px;
  border: 1px solid transparent;
  box-shadow: none;
}
#addTable td:last-child .btn-success {
  color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
}
#addTable td:last-child .btn-primary {
  color: #2563eb; border-color: #cfdcff; background: #e8edff;
}
#addTable td:last-child .btn-danger {
  color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
}

/* ---------- Tombol chip (latar tint muda + teks berwarna) untuk tombol Submit dan Batal -
   disalin dari uangmukabeli.blade.php / pembelianpermintaanagen.blade.php supaya seragam. ---------- */
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

.btn-batal-add {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
}

.btn-batal-add:hover,
.btn-batal-add:focus {
  background-color: #e9ecef;
  border-color: #ced4da;
  color: #343a40;
}

.btn-batal-add:active {
  background-color: #dee2e6 !important;
  border-color: #ced4da !important;
  color: #343a40 !important;
}

/* ---------- Modal lookup Customer/Invoice (#form) - baris tabel modal tetap
   table-bordered/table-striped (pola baku semua modal lookup di purchasing). ---------- */
#form .modal-header .btn-danger.rounded-circle {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ---------- Modal Invoice dilebarkan (hampir selebar layar) supaya kolom Keterangan langsung
   kelihatan tanpa scroll ke kanan. Selector dibuat lebih spesifik dari picker-kas.css
   (yang mengunci modal-dialog ke 1140px). ---------- */
.modal.picker-kas.modal-invoice-lebar:is(.picker-kas, #pk#pk#pk) .modal-dialog {
  max-width: min(1320px, 96vw) !important;
  width: 96vw !important;
  margin-left: auto !important;
  margin-right: auto !important;
}
#tabel_add_list_invoice th { white-space: normal; padding-left: 6px !important; padding-right: 6px !important; }
#tabel_add_list_invoice td { white-space: nowrap; padding-left: 6px !important; padding-right: 6px !important; }

/* ---------- Modal lookup Supplier/Invoice (#form) - baris supplier diklik langsung
   (tidak ada tombol Actions), disalin dari #formAddListItem di pembelianpermintaanagen. ---------- */
#tabel_add_list_customer tbody tr.pick-row {
  cursor: pointer;
  transition: background-color .12s;
}
#tabel_add_list_customer tbody tr.pick-row:hover td {
  background-color: #eef2ff;
}
#tabel_add_list_customer thead th,
#tabel_add_list_invoice thead th,
#editItemTable thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee !important;
  border-top: none !important;
}
#tabel_add_list_customer tbody td,
#tabel_add_list_invoice tbody td,
#editItemTable tbody td {
  border-top: none !important;
  border-bottom: 1px solid #f1f3f5 !important;
  font-size: 13px;
  vertical-align: middle;
}

#input_search_customer_debetnote,
#input_search_invoice_debetnote {
  width: 260px;
  max-width: 100%;
  font-size: 13px;
  padding: 7px 10px 7px 32px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  outline: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 10px center;
}
#input_search_customer_debetnote:focus,
#input_search_invoice_debetnote:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* ---------- Perataan kotak centang untuk semua tabel di halaman ini ----------
   Dikumpulkan di satu tempat supaya tabel utama maupun tabel di dalam modal
   berperilaku sama. Dua hal yang dibereskan:
   1. .form-check bawaan Bootstrap memberi padding-left pada wadahnya dan
      float:left pada inputnya, jadi text-center saja tidak membuat kotaknya
      benar-benar di tengah kolom.
   2. vertical-align:middle mengacu ke titik tengah x-height teks, sedangkan isi
      kolom sebelahnya huruf kapital/angka. Tanpa koreksi -2px kotak centangnya
      terlihat turun sedikit dibanding teks di sebelahnya. */
td .form-check {
  padding-left: 0;
  margin: 0;
  min-height: 0;
}
td input[type="checkbox"] {
  float: none;
  margin: 0;
  vertical-align: middle;
  position: relative;
  top: -2px;
  /* Ukuran diseragamkan lewat scale, bukan width/height, supaya bentuk kotaknya
     tetap bawaan browser. width/height dikembalikan ke auto supaya ukuran khusus
     dari class lain (mis. .pba-chk-lg di invoicepembelian) tidak membuat sebagian
     kotak jadi lebih besar dari yang lain. */
  width: auto;
  height: auto;
  transform: scale(1.5);
}
</style>
@endsection

@section('content')

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- <h2>Debet Note</h2> -->
    </div>
  </div>
</div>

<div id="printContainer" style="display:none"></div>
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

  <div class="card">
    <div class="card-body" style="padding:0;">

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" class="po-filter-inp" id="dnTglAwal" value="{!! $dnTglAwal !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="dnTglAkhir" value="{!! $dnTglAkhir !!}">
        </div>
        <input type="search" id="dnSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat dnIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="dnLen">Tampilkan</label>
          <select id="dnLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="dnBtnFilter" onclick="$('#modalFilterDN').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat dnInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabel2" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">No Bukti</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
            <th style="padding: 4px 12px;" scope="col">Kode Supp</th>
            <th style="padding: 4px 12px;" scope="col">Supplier</th>
            <th style="padding: 4px 12px;" scope="col">Debet</th>
            <th style="padding: 4px 12px;" scope="col">Kredit</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelDN() lewat JS, supaya susunan kolom hasil geser/
               sembunyi selalu konsisten dengan hasil render ulang. --}}
        </tbody>
      </table>

      <div class="po-rt-hint">
        <i class="bi bi-info-circle"></i>
        Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom.
      </div>

    </div>
  </div>
</div>
</div>

<div id="page2" style="display: none" class="mainpage container-fluid" >
<div id="formBsGrid">

  <div class="row" style="margin-bottom: 14px;">
    <div class="col-8 text-left">
      <!-- <h2>Form Debet Note</h2> -->
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
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div id="modalAdd">
    <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group"><label>No Bukti</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group"><label>Tanggal</label></div>
            </div>
            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}">
              </div>
            </div>
            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group"><label>Supplier</label></div>
            </div>
            <div class="col-md-8" style="margin-top:-10px;">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" id="input_add_kodecustomer" placeholder="Ketik kode / nama, Enter">
                <button type="button" id="buttonAddListCustomer" onclick="performSearchSupplier()" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows="3" placeholder="Nama Supplier" class="form-control text-left" id="input_add_namacustomer" disabled></textarea>
              </div>
            </div>
            <div class="col-md-12" style="margin-top:-10px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows="3" placeholder="Alamat Supplier" class="form-control text-left" id="input_add_alamatcustomer" disabled></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4">
      <div class="row">
        <div class="col-md-12 text-right">
          <button id="buttonAddListInvoice" type="button" class="btn btn-lg btn-chip-biru" style="
            height: 30px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem;
            font-weight: 600; transition: background-color 0.3s, box-shadow 0.3s;"
            onclick="buttonAddListInvoice()">Tambah Invoice</button>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4">
      <div class="row">
        <table id="addTable" class="data-table">
          <thead class="text-center">
            <tr>
              <th scope="col">No Inv</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Nilai</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Debet</th>
              <th scope="col">Kredit</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody id="addTableData">
            <tr>
              <td colspan="8" class="text-center">Belum ada data</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div id="formAddEdit" class="container-fluid showhideitem" style="display:none;">
      <div class="row">
        <div class="col-4">
          <h4 style="margin-left:-35px;">Edit Item</h4>
        </div>
      </div>
      {{-- Sebaris seperti baris di modal Tambah Invoice (tanpa centang): label di atas, input
           di bawah. Debet dan Kredit saling mengunci (dnKunciDK()); Kredit disimpan ke field
           Nilai yang sama, bertanda minus. Nilai = nilai invoice sebelum baris ini (item.Sisa),
           dihitung ulang saat Debet/Kredit diketik (dnHitungSisaEdit()). --}}
      <div class="row">
        <div class="col-12 table-responsive">
          <table id="editItemTable" class="data-table">
            <thead>
              <tr>
                <th scope="col">No Faktur</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Jatuh Tempo</th>
                <th scope="col">Nilai</th>
                <th scope="col">Valas</th>
                <th scope="col">Kurs</th>
                <th scope="col">Debet</th>
                <th scope="col">Kredit</th>
                <th scope="col">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input id="AddEditNoInv" style="height:30px; min-width: 170px" type="text" class="form-control" disabled></td>
                <td><input id="AddEditTanggal" style="height:30px; width: 110px; min-width: 110px" type="text" class="form-control" disabled></td>
                <td><input id="AddEditJatuhTempo" style="height:30px; width: 110px; min-width: 110px" type="text" class="form-control" disabled></td>
                <td><input id="AddEditSisa" style="height:30px; width: 140px; min-width: 140px" type="text" value="0.00" class="form-control text-right" disabled></td>
                <td><input id="AddEditValas" style="height:30px; width: 70px; min-width: 70px" type="text" class="form-control" disabled></td>
                {{-- Terkunci di 1.00 kalau valasnya IDR - lihat buttonAddEditItem(). --}}
                <td><input id="AddEditKurs" style="height:30px; width: 100px; min-width: 100px" type="text" inputmode="decimal" value="1.00" class="form-control text-right" oninput="formatRibuanLive(this)" onblur="formatRibuan(this)"></td>
                <td><input id="AddEditNilai" style="height:30px; width: 140px; min-width: 140px" type="text" inputmode="decimal" value="0.00" class="form-control text-right" oninput="formatRibuanLive(this); dnKunciDK('AddEditNilai', 'AddEditKredit'); dnHitungSisaEdit()" onblur="formatRibuan(this); dnKunciDK('AddEditNilai', 'AddEditKredit'); dnHitungSisaEdit()"></td>
                <td><input id="AddEditKredit" style="height:30px; width: 140px; min-width: 140px" type="text" inputmode="decimal" value="0.00" class="form-control text-right" oninput="formatRibuanLive(this); dnKunciDK('AddEditNilai', 'AddEditKredit'); dnHitungSisaEdit()" onblur="formatRibuan(this); dnKunciDK('AddEditNilai', 'AddEditKredit'); dnHitungSisaEdit()"></td>
                <td><input id="AddEditKeterangan" style="height:30px; min-width: 200px" type="text" class="form-control text-left"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-right">
          <button type="button" class="btn btn-batal-add" onclick="buttonAddBatal()">Batal</button>
          <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru">Simpan</button>
        </div>
      </div>
    </div>

  </div>
</div>{{-- /#formBsGrid --}}
</div>

<div id="page3" style="display: none" class="mainpage container-fluid" >
<div id="formBsGrid">

  <div class="row" style="margin-bottom: 14px;">
    <div class="col-8 text-left">
      <!-- <h2>Detail Debet Note</h2> -->
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
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">
        Close
      </button>
    </div>
  </div>

  <div id="modalDetail">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group"><label>No Bukti</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_detail_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group"><label>Tanggal</label></div>
            </div>
            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group"><label>Supplier</label></div>
            </div>
            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_detail_kodecustomer" placeholder="" disabled>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows="3" placeholder="Nama Supplier" class="form-control text-left" id="input_detail_namacustomer" disabled></textarea>
              </div>
            </div>
            <div class="col-md-12" style="margin-top:-10px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows="3" placeholder="Alamat Supplier" class="form-control text-left" id="input_detail_alamatcustomer" disabled></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4">
      <div class="row">
        <table id="detailTable" class="data-table">
          <thead class="text-center">
            <tr>
              <th scope="col">No Inv</th>
              <th scope="col">Keterangan</th>
              <th scope="col">Nilai</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Debet</th>
              <th scope="col">Kredit</th>
            </tr>
          </thead>
          <tbody id="detailTableData">
            <tr>
              <td colspan="7" class="text-center">Belum ada data</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="row mt-2">
        <div class="col-md-12 text-right" id="divOto">
          <!-- <button id="submitOtorisasi" type="button" class="btn btn-chip-biru" onclick="submitOtorisasi()">Otorisasi</button> -->
        </div>
      </div>
    </div>
  </div>
</div>{{-- /#formBsGrid --}}
</div>

<!-- modal filter otorisasi -->
<div class="modal fade rt-filter" id="modalFilterDN">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Debet Note
          <span class="rt-active-badge" id="dnFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterDN').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="dnModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="dnModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="dnResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterDN').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="dnTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter otorisasi -->

<!-- start modal add (lookup Customer / Invoice) -->
<div class="modal fade picker-kas" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div id="modalAddListCustomer" class="showhidemodalbodyadd">
        <div class="modal-header">
          <h5 class="modal-title">Supplier</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{-- Disamakan persis dengan modal pemilih menu Kas (picker-kas): kotak cari lama
               (#input_search_customer_debetnote) diganti kotak Search bawaan DataTables. --}}
          <div class="container-fluid mt-4">
            <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px; ">
              <table id="tabel_add_list_customer">
                <thead>
                  <tr>
                    <th scope="col">Kode</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                    <th scope="col">Kota</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_customer" class="text-left">
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn picker-kas-batal" onclick="buttonAddListBatal()">Batal</button>
        </div>
      </div>

      <div id="modalAddListInvoice" class="showhidemodalbodyadd">
        <div class="modal-header">
          <h5 class="modal-title">Invoice</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            {{-- Dulu baris ini ditarik ke atas (margin-top:-30px) sehingga kotak cari terpotong header modal. --}}
            <div class="d-flex justify-content-end mb-3">
                <input id="input_search_invoice_debetnote" type="search" class="form-control" placeholder="Cari data" onkeyup="searchInvoiceDebetNote(event)">
            </div>
            <div class="table-responsive">
              <table id="tabel_add_list_invoice" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th class="text-center" scope="col">v</th>
                    <th scope="col">No Faktur</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Jatuh Tempo</th>
                    <th scope="col">Nilai</th>
                    <th scope="col">Valas</th>
                    <th scope="col">Kurs</th>
                    <th scope="col">Debet</th>
                    <th scope="col">Kredit</th>
                    <th scope="col">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_invoice" class="text-left">
                  <tr>
                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-batal-add" onclick="buttonAddListBatal()">Batal</button>
          <button type="button" class="btn btn-chip-biru" onclick="buttonAddPickInvoice()">Simpan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal add -->

@endsection

@section('js')
@include('partials.tabel-memuat')
<script>window.RT_LABEL_PAJAK = true</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">
let listInvoice = []
let listCustomer = []
let listData = []
let tempBarangAddEdit = {}
let tipeform = ''

// Dipatok, bukan diambil dari window.location - harus sama persis dengan $req->href yang
// dikirim PembelianPermintaanDebetNoteController@loadAll ke
// HeaderTableController@getHeaderTable.
const DN_HREF = 'pembelianpermintaandebetnote'

let dnCart = []
let dataDN = []

function dnBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
  let cart = []
  ;(headers || []).forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let tipeNama = { 0 : 'varchar', 1 : 'float', 2 : 'date' }
    let label = h
    if (aliasordered && aliasordered[i] && aliasordered[i].alias) {
      label = aliasordered[i].alias
    }
    // Field data tetap 'Nilai DN' / 'Kredit DN' (alias query loadAll), yang diganti hanya
    // judul kolomnya. Keduanya sudah positif dari server (kredit minus hanya di database).
    if (label === 'Nilai DN') { label = 'Debet' }
    if (label === 'Kredit DN') { label = 'Kredit' }
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null)
      ? Number(desimals[i])
      : (tipe === 1 ? 2 : 0)

    cart.push([
      tipe === 0 ? h : values[i],
      label,
      Number(isshowns[i]) === 1 ? 1 : 0,
      tipeNama[tipe] || 'varchar',
      0,
      isNaN(des) ? 0 : des,
      h,
      values[i],
      tipe
    ])
  });
  return cart
}

function dnKolomTampil () {
  return (dnCart || []).filter(c => Number(c[2]) === 1)
}

function dnKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function dnFormatAngkaDes (nilai, des) {
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

function dnRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return dnFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai).split('-').reverse().join('/') : ""
  }
  // Tanggal yang datang sebagai teks 'yyyy-mm-dd...' (mis. hasil CONVERT di SQL) juga tampil dd/mm/yyyy.
  if (typeof nilai === 'string' && /^\d{4}-\d{2}-\d{2}/.test(nilai)) {
    return nilai.substr(0, 10).split('-').reverse().join('/')
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

function dnHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

let dnRtSudahInit = false

function dnInitReportTableSekali () {
  if (dnRtSudahInit || typeof ReportTable === 'undefined') { return }
  dnRtSudahInit = true

  ReportTable.init({
    table    : '#tabel2',
    bar      : '#rtBar',
    onChange : renderTabelDN
  })

  let dnGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (dnGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      dnGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      dnGuardUlangKlik = false
    }, true)
  }
}

function dnPindahBar () {
  let bar = document.getElementById('rtBar')
  let tabel = document.getElementById('tabel2')
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#tabel2')) {
    acuan = document.getElementById('tabel2_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

function dnIkatSearch () {
  let input = document.getElementById('dnSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

let dnPanjangHalaman = 10
function dnIkatPanjangHalaman () {
  let sel = document.getElementById('dnLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(dnPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    dnPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(dnPanjangHalaman).draw()
  })
}

function dnIkatPeriode () {
  let awal  = document.getElementById('dnTglAwal')
  let akhir = document.getElementById('dnTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadAll()
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

function dnAturTinggiTabel () {
  let area = document.getElementById('content')
  let wrap = document.querySelector('#page1 .po-table-wrap')
  if (!area || !wrap) { return }

  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  let pageEl = document.getElementById('page1')
  let bawah = pageEl.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

let dnFilterOtorisasi = 'SEMUA'

function dnOtorisasiDN (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

function dnUpdateFilterBadge () {
  let jml = (dnFilterOtorisasi !== 'SEMUA') ? 1 : 0
  let badge = document.getElementById('dnFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function dnTerapkanFilter () {
  dnFilterOtorisasi = $('#dnModalOtorisasi').val() || 'SEMUA'
  dnUpdateFilterBadge()
  $('#modalFilterDN').modal('hide')
  renderTabelDN()
}

function dnResetFilter () {
  dnFilterOtorisasi = 'SEMUA'
  $('#dnModalOtorisasi').val('SEMUA')
  dnUpdateFilterBadge()
  $('#modalFilterDN').modal('hide')
  renderTabelDN()
}

window.g_href = DN_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  dnCart.forEach((c) => {
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
      href     : DN_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : DN_HREF,
      reset  : 1
    },
    success : function (res) {
      dnCart = dnBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = dnCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

$(document).ready(function() {
  dnInitReportTableSekali()
  loadAll()
});

function loadAll () {
  document.getElementById('tabel_data').innerHTML =
    '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>'

  $.ajax({
    url: "{!! url('debetnoteloadall') !!}",
    type: "get",
    async: true,
    data: {
      tglawal: $('#dnTglAwal').val(),
      tglakhir: $('#dnTglAkhir').val()
    },
    success: function (res) {
      dnCart = dnBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = dnCart
      dataDN = res.listData1 || []
      renderTabelDN()
    },
    error: function (err) {
      console.error("Load failed:", err);
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function renderTabelDN () {
  window.g_modeReport = 1
  window.gcart_header = dnCart

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  let cols = dnKolomTampil()
  let kolomRender = cols.map(dnKolomRender)

  let thead = document.getElementById('tabel_header')
  thead.innerHTML = dnHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataDN || []
  if (dnFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return dnOtorisasiDN(item) === dnFilterOtorisasi })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let isOtorisasi = Number(item.IsOtorisasi1) || 0

    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${item.NOBUKTI}')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NOBUKTI}')"><i class="bi bi-key"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NOBUKTI}')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${item.NOBUKTI}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${dnRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${dnRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${isOtorisasi ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${item.OtoUser1 || ''}</td>
      <td>${item.TglOto1 ? formatDate(item.TglOto1).split('-').reverse().join('/') : ''}</td>
    </tr>`
  });

  document.getElementById("tabel_data").innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: dnPanjangHalaman,
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  dnPindahBar()
  dnIkatSearch()
  dnIkatPanjangHalaman()
  dnIkatPeriode()
  let inputSearch = document.getElementById('dnSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
  dnAturTinggiTabel()
}

function setNewNoBukti () {
  $.ajax({
    url: "{!! url('debetnotespnobukti') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut
    }})
}

function cleanFormAdd () {
  document.getElementById("input_add_kodecustomer").value = ''
  document.getElementById("input_add_namacustomer").value = ''
  document.getElementById("input_add_alamatcustomer").value = ''
  document.getElementById("input_add_nobukti").value = ''
  // Tanggal awal diambil dari jam server SQL (sama seperti menu PR), bukan jam komputer user.
  $.ajax({
    url: "{!! url('gettanggalserver') !!}",
    type: "get",
    async: false,
    success: function(res) {
      document.getElementById("input_add_tanggal").value = res[0].tanggal
    }})
}

function submitAddEdit () {
  let barang = tempBarangAddEdit
  let _token  = $("#_token").val()
  let choice = "U"
  let urut = barang.Urut
  let kodecustsupp = barang.KodeSupp
  let nobukti  = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val();
  let tanggal  = $("#input_add_tanggal").val()
  let noinvoice  = $("#AddEditNoInv").val()
  let debet  = bersihkanAngka($("#AddEditNilai").val())
  let kredit  = bersihkanAngka($("#AddEditKredit").val())
  let kodevls  = $("#AddEditValas").val()
  let kurs  = dnValasIDR(kodevls) ? 1 : bersihkanAngka($("#AddEditKurs").val())
  let keterangan  = $("#AddEditKeterangan").val()

  if (debet < 0 || kredit < 0 || kurs < 0 ) {
    alertify.warning("Nilai < 0")
    return
  }
  if (dnHitungSisaEdit() < 0) {
    alertify.warning("Debet invoice " + noinvoice + " melebihi Nilai")
    return
  }
  // Kredit disimpan di field Nilai yang sama dengan Debet Note, bertanda minus.
  let nilai  = dnNilaiDK(debet, kredit)
  let nilairp  = dnNilaiRp(nilai, kurs)

  $.ajax({
    url: "{!! url('debetnotespkoreksi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti,
      tanggal,
      urut,
      noinvoice,
      kodecustsupp,
      nilai,
      kodevls,
      kurs,
      nilairp,
      keterangan,
      nourut
    },
    success: function(res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        return
      }
      if (res == 1) {
        $('.showhideitem').hide();
        loadAll()
        refreshDataTable(nobukti)
        alertify.success('Berhasil Edit Invoice')
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddDelete (i) {
  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let barang = listData[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus Invoice ' + barang.NoInv + ' ?',
      function() {
        let _token  = $("#_token").val()

        let choice = "D"
        let urut = barang.Urut
        let kodecustsupp = barang.KodeSupp
        let nobukti  = $("#input_add_nobukti").val()
        let tanggal  = $("#input_add_tanggal").val()
        // Hapus memakai data item itu sendiri (dulu dari form Edit, yang bisa berisi item lain
        // atau angka ber-separator).
        let noinvoice  = barang.NoInv
        let nilai  = barang.Nilai
        let nilairp  = barang.NilaiRp
        let kodevls  = barang.kodeVls
        let kurs  = barang.Kurs
        $.ajax({
          url: "{!! url('debetnotespkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            tanggal,
            urut,
            noinvoice,
            kodecustsupp,
            nilai,
            kodevls,
            kurs,
            nilairp
          },
          success: function(res) {
            if (res && res.pesan) {
              alertify.warning(res.pesan)
              return
            }
            if (res == 1) {
              $('.showhideitem').hide();
              loadAll()
              refreshDataTable(nobukti)
              alertify.success('Berhasil menghapus Invoice')
            }
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })
      }
    ,function(){
      console.log('no')
    });
}

function buttonAddPickInvoice () {
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  let nobukti = $("#input_add_nobukti").val();
  let nourut = $("#input_add_nourut").val();

  let tanggal = $("#input_add_tanggal").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let _token = $("#_token").val();
  let tempData = []
  let checkMinus = 0
  let lebihNilai = ''
  listInvoice.forEach((item, i) => {
      if (document.getElementById(`add_checkbox${i}`).checked) {
        // Debet tidak boleh melebihi Nilai (sisa invoice) - sisa tidak boleh minus.
        if (dnHitungSisa(i) < 0 && !lebihNilai) {
          lebihNilai = item.NoFaktur
        }

        // Input tampil dengan separator ribuan, yang dikirim angka bersih.
        let checkDebet = bersihkanAngka($(`#add_inputQnt${i}`).val());
        let checkKredit = bersihkanAngka($(`#add_inputKredit${i}`).val());
        let checkKurs = dnValasIDR(item.KodeVls) ? 1 : bersihkanAngka($(`#add_inputKurs${i}`).val());
        // Kredit disimpan di field Nilai yang sama dengan Debet Note, bertanda minus.
        let checkNilai = dnNilaiDK(checkDebet, checkKredit)
        listInvoice[i].Keterangan = $(`#add_inputKeterangan${i}`).val();
        listInvoice[i].inputNilai = checkNilai
        listInvoice[i].inputKurs = checkKurs
        listInvoice[i].inputNilaiRp = dnNilaiRp(checkNilai, checkKurs)
        if (checkDebet < 0 || checkKredit < 0 || checkKurs < 0 ) {
          checkMinus = 1
        }

        tempData.push(listInvoice[i])
      }

    });

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }

    if (checkMinus) {
      alertify.warning("Nilai < 0");
      return
    }

    if (lebihNilai) {
      alertify.warning("Debet invoice " + lebihNilai + " melebihi Nilai");
      return
    }

    $.ajax({
        url: "{!! url('debetnotespadd') !!}",
        type: "post",
        async: false,
        data: {
          _token : _token,
          tempData,
          tanggal: tanggal,
          nobukti,
          nourut,
          kodecustsupp,
          tipeform,
          nourut
        },
        success: function(res) {
          if (res && res.pesan) {
            alertify.warning(res.pesan)
            return
          }
          if (res == 1) {
            alertify.success('DN telah ditambah');
            loadAll()
            tipeform = 'edit'
            document.getElementById("input_add_kodecustomer").disabled = true
            document.getElementById("buttonAddListCustomer").disabled = true
            document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            $("#form").modal('toggle')
          }
          if (res == 2) {
            setNewNoBukti()
            alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
          }
        },
        error: function (err) {
          console.log(err)
          // spAdd menyimpan semua invoice dalam satu transaksi - kalau gagal, tidak ada yang tersimpan.
          let pesan = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : 'Terjadi kesalahan silahkan refresh browser'
          alertify.warning(pesan)
        }
      })
}

function searchInvoiceDebetNote (event) {
  let keyword = event.target.value.toLowerCase()
  $('#tabel_data_add_list_invoice tr').each(function () {
    let text = $(this).text().toLowerCase()
    $(this).toggle(text.indexOf(keyword) > -1)
  })
}

function buttonAddListInvoice () {
  listInvoice = []
  $('#input_search_invoice_debetnote').val('')

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  // Kode supplier bisa diketik; yang dianggap terpilih hanya yang diambil dari modal
  // pemilih (nama supplier ikut terisi).
  if (!kodecustsupp || !$("#input_add_namacustomer").val()) {
    alertify.warning("Pilih Supplier terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('debetnotelistinvoice') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp,
    },
    success: function(res) {
      listInvoice = res
      let rowTable = ``
      res.forEach((item, i) => {
        // Valas IDR: kurs terkunci di 1.00. Valas lain: kurs terbuka, diisi awal kurs invoice.
        let idr = dnValasIDR(item.KodeVls)
        rowTable +=
        `<tr>
        <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
        <td>${item.NoFaktur}</td>
        <td>${formatDate(item.Tanggal,'-').split('-').reverse().join('/')}</td>
        <td>${formatDate(item.JatuhTempo,'-').split('-').reverse().join('/')}</td>
        <td><input id="add_inputSisa${i}" style="height:30px; width: 140px; min-width: 140px" type="text" value='${angkaTampil(item.Sisa)}' class="form-control text-right" disabled></td>
        <td>${item.KodeVls}</td>
        <td><input id="add_inputKurs${i}" style="height:30px; width: 100px; min-width: 100px" type="text" inputmode="decimal" value='${idr ? '1.00' : angkaTampil(item.Kurs)}' class="form-control text-right" oninput="formatRibuanLive(this)" onblur="formatRibuan(this)" ${idr ? 'disabled' : ''}></td>
        <td><input id="add_inputQnt${i}" style="height:30px; width: 140px; min-width: 140px" type="text" inputmode="decimal" value='0.00' class="form-control text-right" oninput="formatRibuanLive(this); dnKunciDK('add_inputQnt${i}', 'add_inputKredit${i}'); dnHitungSisa(${i})" onblur="formatRibuan(this); dnKunciDK('add_inputQnt${i}', 'add_inputKredit${i}'); dnHitungSisa(${i})"></td>
        <td><input id="add_inputKredit${i}" style="height:30px; width: 140px; min-width: 140px" type="text" inputmode="decimal" value='0.00' class="form-control text-right" oninput="formatRibuanLive(this); dnKunciDK('add_inputQnt${i}', 'add_inputKredit${i}'); dnHitungSisa(${i})" onblur="formatRibuan(this); dnKunciDK('add_inputQnt${i}', 'add_inputKredit${i}'); dnHitungSisa(${i})"></td>

        <td><input style="height:30px; min-width: 200px" id="add_inputKeterangan${i}" type="text" value='' class="form-control text-left" ></td>

        </tr>`
      });

      document.getElementById("tabel_data_add_list_invoice").innerHTML = rowTable

      if (res.length) {
        $('.showhidemodalbodyadd').hide();
        $('#modalAddListInvoice').show();
        $('#form').addClass('modal-invoice-lebar')
        $("#form").modal('toggle')
      } else {
        alertify.warning("Tidak ada invoice untuk ditambah")
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

// Kolom Nilai di modal Invoice = sisa invoice (dari listInvoice: saldo dikurangi DN yang belum
// diotorisasi). Debet mengurangi, Kredit menambah. Hasilnya dikembalikan supaya
// buttonAddPickInvoice() bisa menolak Debet yang melebihi Nilai.
function dnHitungSisa (i) {
  let item = listInvoice[i] || {}
  let debet = bersihkanAngka($(`#add_inputQnt${i}`).val())
  let kredit = bersihkanAngka($(`#add_inputKredit${i}`).val())
  let sisa = Number(((Number(item.Sisa) || 0) - debet + kredit).toFixed(2))
  let el = document.getElementById(`add_inputSisa${i}`)
  if (el) {
    el.value = angkaTampil(sisa)
    el.classList.toggle('text-danger', sisa < 0)
  }
  return sisa
}

// Kolom Nilai di form Edit Item = nilai invoice sebelum baris ini (item.Sisa dari getDetail),
// dikurangi Debet / ditambah Kredit yang sedang diketik. Dikembalikan untuk validasi Simpan.
function dnHitungSisaEdit () {
  let item = tempBarangAddEdit || {}
  let debet = bersihkanAngka($('#AddEditNilai').val())
  let kredit = bersihkanAngka($('#AddEditKredit').val())
  let sisa = Number(((Number(item.Sisa) || 0) - debet + kredit).toFixed(2))
  let el = document.getElementById('AddEditSisa')
  if (el) {
    el.value = angkaTampil(sisa)
    el.classList.toggle('text-danger', sisa < 0)
  }
  return sisa
}

function dnValasIDR (kodeVls) {
  let vls = String(kodeVls || 'IDR').trim().toUpperCase()
  return vls === 'IDR' || vls === ''
}

// Debet Note dan Kredit saling mengunci: begitu salah satu berisi angka (bukan 0), yang
// satunya di-disable; dikosongkan / dinolkan lagi = keduanya terbuka kembali.
function dnKunciDK (idDebet, idKredit) {
  let elDebet = document.getElementById(idDebet)
  let elKredit = document.getElementById(idKredit)
  let debet = bersihkanAngka(elDebet.value)
  let kredit = bersihkanAngka(elKredit.value)

  elKredit.disabled = debet !== 0
  elDebet.disabled = debet === 0 && kredit !== 0
}

// Nilai yang disimpan ke field Nilai: Debet Note apa adanya, Kredit bertanda minus.
function dnNilaiDK (debet, kredit) {
  return debet !== 0 ? debet : (kredit !== 0 ? -kredit : 0)
}

// NilaiRp tidak lagi tampil di layar, tetap dihitung (Nilai x Kurs) untuk total DN dan
// posting hutang/jurnal.
function dnNilaiRp (nilai, kurs) {
  return Number((nilai * kurs).toFixed(2))
}

// Sel Nilai (nilai invoice sebelum baris ini, berjalan per baris - lihat getDetail), Valas, Kurs, Debet, Kredit untuk tabel form Add/Edit dan Detail.
// Nilai minus di database = kredit, ditampilkan positif di kolom Kredit.
function dnSelDK (item) {
  let nilai = Number(item.Nilai) || 0
  return `
                  <td class="text-right">${angkaTampil(item.Sisa)}</td>
                  <td>${item.kodeVls}</td>
                  <td class="text-right">${angkaTampil(item.Kurs)}</td>
                  <td class="text-right">${angkaTampil(nilai < 0 ? 0 : nilai)}</td>
                  <td class="text-right">${angkaTampil(nilai < 0 ? -nilai : 0)}</td>`
}

function buttonAddListCustomer () {
  $('#tabel_add_list_customer').DataTable().destroy();
  $.ajax({
    url: "{!! url('debetnotelistcustomer') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      // Baris dipilih lewat indeks ke listCustomer, bukan teks nama/alamat di onclick - nama
      // supplier bertanda petik (mis. BANDUNG LEVI'S) atau alamat berisi baris baru membuat
      // onclick error dan supplier tidak bisa dipilih.
      listCustomer = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer(${i})">
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td>${item.ALAMAT}</td>
        <td>${item.NamaKota}</td>
        </tr>`
      })
      if(!res.length) {
        rowTable= `<tr><td class="text-center" colspan=4>Tidak ada data</td></tr>`
      }
      document.getElementById("tabel_data_add_list_customer").innerHTML = rowTable
      // Tampilan disamakan dengan modal pemilih menu Kas (pickerKasInit, public/js/picker-kas.js):
      // dropdown "Tampilkan" + kotak Search bawaan DataTables (kotak cari lama
      // #input_search_customer_debetnote dilepas dari markup).
      pickerKasInit('tabel_add_list_customer')
      $('.showhidemodalbodyadd').hide();
      $('#modalAddListCustomer').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function buttonAddPickCustomer (i) {
  let item = listCustomer[i] || {}
  document.getElementById("input_add_kodecustomer").value = item.KODECUSTSUPP || ''
  document.getElementById("input_add_namacustomer").value = item.NAMACUSTSUPP || ''
  document.getElementById("input_add_alamatcustomer").value = item.ALAMAT1 || ''

  $('.showhideitem').hide();
  buttonAddListBatal()
}

function buttonAddBatal () {
  $('.showhideitem').hide();
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $("#form").modal('toggle')
}

function refreshDataTable (nobukti) {
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('debetnotespdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      listData = res
      if (!res.length) {
          alertify.success('Data Habis')
          $('#page2').hide();
          $('#page1').show();
          return
      }

      let rowTable = ``
      listData.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan ? item.Keterangan : ''}</td>
                  ${dnSelDK(item)}
                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>      `
      });

      document.getElementById("addTableData").innerHTML = rowTable
        document.getElementById("input_add_kodecustomer").value = listData[0].KodeSupp
        document.getElementById("input_add_namacustomer").value = listData[0].NamaCustSupp
        document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        // Diisi teks YYYY-MM-DD, bukan valueAsDate = new Date(...): tanggal dari SQL dibaca
        // sebagai jam lokal (WIB) lalu valueAsDate memakai UTC, jadi tanggalnya mundur 1 hari.
        document.getElementById("input_add_tanggal").value = String(listData[0].tanggal || '').substring(0, 10)
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}

function submitOtorisasi () {
  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();

  $.ajax({
    url: "{!! url('debetnotespotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      if (res && res.pesan) {
        alertify.warning(res.pesan)
        loadAll()
        return
      }
      alertify.success('Berhasil update otorisasi');
      loadAll();
      buttonCloseForm();
    },
    error: function(err) {
      console.log(err);
      alertify.warning('Terjadi kesalahan, silakan refresh browser.');
    }
  });
}

function buttonDetail (nobukti) {
  document.getElementById("divOto").style.display = "none";
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('debetnotespdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      if (!res.length) {
          alertify.success('Data tidak ditemukkan')
          return
      }

      let rowTable = ``
      res.forEach((item, i) => {
              rowTable += `
                <tr>
                  <td>${item.NoInv}</td>
                  <td>${item.Keterangan ? item.Keterangan : ''}</td>
                  ${dnSelDK(item)}
                </tr>`
      });

      document.getElementById("detailTableData").innerHTML = rowTable
        document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
        document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
        document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
        document.getElementById("input_detail_nobukti").value = res[0].NoBukti
        document.getElementById("input_detail_tanggal").value = String(res[0].tanggal || '').substring(0, 10)

        document.getElementById("divOto").style.display = (Number(res[0].IsOtorisasi1) ? "none" : "block")

        $('.mainpage').hide();
        $('#page3').show();
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonOtorisasi (nobukti) {
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  // Otorisasi langsung jalan tanpa dialog konfirmasi (permintaan user 13 Nov 2026).
  {
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('debetnotespotorisasi') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti
      },
      success: function (res) {
        if (res && res.pesan) {
          alertify.warning(res.pesan)
          loadAll()
          return
        }
        alertify.success('Berhasil update otorisasi');
        loadAll();
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser.');
      }
    });
  }
}

function buttonKoreksi (nobukti ) {
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  tipeform = 'edit'
  cleanFormAdd()
  document.getElementById("input_add_kodecustomer").disabled = true
  document.getElementById("buttonAddListCustomer").disabled = true
  document.getElementById("input_add_tanggal").disabled = true

  refreshDataTable(nobukti)
  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }
  if (listData[0].IsOtorisasi1 == 1) {
    alertify.warning("DN sudah diotorisasi")
    return
  }

  $('.showhideitem').hide();
  $('#modalAdd').show();
  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAdd (nobukti) {
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_add_kodecustomer").disabled = false
  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=8 class="text-center">Belum ada data</td></tr>`
  tipeform = 'add'
  $('.showhideitem').hide();
  $('#modalAdd').show();

  cleanFormAdd()
  setNewNoBukti()

  $('.mainpage').hide();
  $('#page2').show();
}

function buttonAddEditItem (i) {
  tempBarangAddEdit = listData[i]
  document.getElementById("AddEditNoInv").value = tempBarangAddEdit.NoInv
  document.getElementById("AddEditValas").value = tempBarangAddEdit.kodeVls
  // Valas IDR: kurs terkunci di 1.00.
  let idr = dnValasIDR(tempBarangAddEdit.kodeVls)
  document.getElementById("AddEditKurs").value = idr ? '1.00' : angkaTampil(tempBarangAddEdit.Kurs)
  document.getElementById("AddEditKurs").disabled = idr

  // Nilai minus di database = kredit.
  let nilai = Number(tempBarangAddEdit.Nilai) || 0
  document.getElementById("AddEditNilai").value = angkaTampil(nilai < 0 ? 0 : nilai)
  document.getElementById("AddEditKredit").value = angkaTampil(nilai < 0 ? -nilai : 0)
  dnKunciDK('AddEditNilai', 'AddEditKredit')

  document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.Keterangan || ''
  document.getElementById("AddEditTanggal").value = tempBarangAddEdit.TglInv ? formatDate(tempBarangAddEdit.TglInv, '-').split('-').reverse().join('/') : ''
  document.getElementById("AddEditJatuhTempo").value = tempBarangAddEdit.JatuhTempoInv ? formatDate(tempBarangAddEdit.JatuhTempoInv, '-').split('-').reverse().join('/') : ''
  dnHitungSisaEdit()

  $('.showhideitem').hide();
  $('#formAddEdit').show();
}

function buttonCloseForm () {
  $('.mainpage').hide();
  $('#page1').show();
}

// Pola sama dengan buttonBatalOtorisasi di pembelianpermintaanagen.blade.php: cek hak akses
// Batal, lalu alertify.prompt untuk alasan (wajib diisi, dicatat ke dbLogFile oleh controller).
function buttonBatalOtorisasi(nobukti) {
  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }

  alertify.prompt("Batal Otorisasi", "Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
    function (evt, value) {
      if (!String(value || '').trim()) {
        alertify.warning('Keterangan harus diisi.');
        return false;
      }
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('debetnotespbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti,
          pket: value
        },
        success: function (res) {
          if (res && res.pesan) {
            alertify.warning(res.pesan)
            loadAll()
            return
          }
          if (res > 0) {
            alertify.success('Berhasil batal otorisasi');
            loadAll();
          } else {
            alertify.warning('Gagal batal otorisasi');
          }
        },
        error: function (err) {
          console.log(err);
          if (err.status === 403) {
            alertify.warning('No access');
          } else if (err.status === 422) {
            alertify.warning('Keterangan harus diisi.');
          } else {
            alertify.warning('Terjadi kesalahan. Silakan refresh browser.');
          }
        }
      });
    },
    function () {
      alertify.error("Action cancelled");
    }
  );
}

function formatDate(date , pemisah = '/') {
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

// Separator ribuan (koma) - pola sama dengan invoicereturbeli.blade.php / newpo.blade.php.
// formatAngka lama menaruh koma sesudah tanda minus (-,360,000.00), sekarang lewat dnFormatAngkaDes.
function formatAngka (angkaString) {
  return dnFormatAngkaDes(angkaString, 2)
}

// Angka dari server -> teks ber-separator 2 desimal (null/kosong = 0.00).
function angkaTampil (nilai) {
  return dnFormatAngkaDes(nilai, 2)
}

// Nilai input ber-separator dibersihkan dulu sebelum dihitung / dikirim ke server -
// parseFloat('50,000.00') hanya terbaca 50.
function bersihkanAngka (nilai) {
  return parseFloat(String(nilai == null ? '' : nilai).replace(/,/g, '')) || 0
}

function formatRibuan (el) {
  let raw = String(el.value).replace(/,/g, '')
  if (raw === '' || isNaN(Number(raw))) return
  let parts = Number(raw).toFixed(2).split('.')
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  el.value = parts.join('.')
}

// Modal Supplier kembali ke ukuran normal; lebar penuh hanya untuk daftar Invoice.
$(function () {
  $('#form').on('hidden.bs.modal', function () { $(this).removeClass('modal-invoice-lebar') })
})

// Posisi kursor dihitung dari jumlah karakter bermakna (angka, titik desimal, minus) di
// depannya. Dulu hanya angka yang dihitung, sehingga setelah "147." diketik kursor kembali
// ke depan titik dan angka berikutnya masuk sebelum titik (147.5 jadi 1475.).
function formatRibuanLive (el) {
  let cursorPos = el.selectionStart
  let sebelum = el.value.slice(0, cursorPos).replace(/[^0-9.\-]/g, '')

  let raw = el.value.replace(/[^0-9.\-]/g, '')
  let negatif = raw.charAt(0) === '-'
  raw = raw.replace(/-/g, '')
  if (raw === '' && !negatif) { el.value = ''; return }

  let titik = raw.indexOf('.')
  let bulat = titik >= 0 ? raw.slice(0, titik) : raw
  let desimal = titik >= 0 ? raw.slice(titik + 1).replace(/\./g, '').slice(0, 2) : null
  bulat = bulat.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  el.value = (negatif ? '-' : '') + bulat + (desimal !== null ? '.' + desimal : '')

  // Titik / minus ganda yang dibuang tidak ikut dihitung
  let jumlahTitik = (sebelum.match(/\./g) || []).length
  let jumlahMinus = (sebelum.match(/-/g) || []).length
  let target = sebelum.length - Math.max(0, jumlahTitik - 1) - Math.max(0, jumlahMinus - (negatif ? 1 : 0))

  let pos = 0, hitung = 0
  while (pos < el.value.length && hitung < target) {
    if (el.value[pos] !== ',') hitung++
    pos++
  }
  el.setSelectionRange(pos, pos)
}

// Kode / nama supplier bisa diketik, polanya sama dengan input barang di Permintaan Pembelian
// Agen: Enter / tombol browse -> kalau hasilnya hanya 1 supplier (atau kodenya persis sama)
// langsung terpilih tanpa modal; kalau lebih dari 1 / tidak ketemu, modal pemilih dibuka dan
// langsung terfilter dengan teks ketikan.
function performSearchSupplier () {
  const searchValue = document.getElementById('input_add_kodecustomer').value.trim();

  if (searchValue) {
    let cocok = []
    $.ajax({
      url: "{!! url('debetnotelistcustomer') !!}",
      type: "get",
      async: false,
      success: function (res) {
        listCustomer = res || []
        let kata = searchValue.toLowerCase()
        let persis = listCustomer.findIndex(c => String(c.KODECUSTSUPP || '').trim().toLowerCase() === kata)
        if (persis >= 0) {
          cocok = [persis]
          return
        }
        listCustomer.forEach((c, i) => {
          let teks = (String(c.KODECUSTSUPP || '') + ' ' + String(c.NAMACUSTSUPP || '')).toLowerCase()
          if (teks.indexOf(kata) > -1) { cocok.push(i) }
        })
      },
      error: function (err) {
        console.log(err)
      }
    })

    if (cocok.length === 1) {
      let item = listCustomer[cocok[0]] || {}
      document.getElementById("input_add_kodecustomer").value = item.KODECUSTSUPP || ''
      document.getElementById("input_add_namacustomer").value = item.NAMACUSTSUPP || ''
      document.getElementById("input_add_alamatcustomer").value = item.ALAMAT1 || ''
      $('.showhideitem').hide();
      return
    }
  }

  buttonAddListCustomer();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_customer')) {
    $('#tabel_add_list_customer').DataTable().search(searchValue).draw();
  }
}

$(function () {
  document.getElementById('input_add_kodecustomer').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      performSearchSupplier();
    }
  });

  // Kode diubah dengan mengetik = supplier yang tadi dipilih tidak berlaku lagi.
  document.getElementById('input_add_kodecustomer').addEventListener('input', function() {
    document.getElementById('input_add_namacustomer').value = ''
    document.getElementById('input_add_alamatcustomer').value = ''
  });
})
</script>
@endsection
