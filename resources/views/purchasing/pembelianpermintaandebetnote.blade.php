@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Debet Note')

@endsection

@section('css')
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
#tabel_add_list_invoice thead th {
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
#tabel_add_list_invoice tbody td {
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
            <th style="padding: 4px 12px;" scope="col">Nilai DN</th>
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
                <input type="text" class="form-control text-left" id="input_add_kodecustomer" placeholder="" disabled>
                <button type="button" id="buttonAddListCustomer" onclick="buttonAddListCustomer()" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
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
              <th scope="col">Nilai Debet Note</th>
              <th scope="col">Nilai Inv</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Nilai DN Rp</th>
              <th scope="col">Nilai Invoice Rp</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody id="addTableData">
            <tr>
              <td colspan="9" class="text-center">Belum ada data</td>
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
      <div class="row">
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>No Inv</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditNoInv" type="text" class="form-control" disabled>
              </div>
            </div>
          </div>
          <div class="row" style="margin-top:-15px;">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Ket.</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditKeterangan" type="text" class="form-control">
              </div>
            </div>
          </div>
          <div class="row" style="margin-top:-15px;">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Valas</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditValas" type="text" class="form-control" disabled>
              </div>
            </div>
          </div>
          <div class="row" style="margin-top:-15px;">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Kurs</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditKurs" type="number" value="1.00" class="form-control text-right" onBlur="onChangeNilaiKursItem()">
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Nilai</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditNilai" type="number" value="0.00" class="form-control text-right" onBlur="onChangeNilaiKursItem()">
              </div>
            </div>
          </div>
          <div class="row" style="margin-top:-15px;">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Nilai (Rp)</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditNilaiRp" type="number" value="0.00" class="form-control text-right" disabled>
              </div>
            </div>
          </div>
          <div class="row" style="margin-top:-15px;">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Nilai Inv</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditNilaiInv" type="number" value="0.00" class="form-control text-right" disabled>
              </div>
            </div>
          </div>
          <div class="row" style="margin-top:-15px;">
            <div class="col-md-3" style="margin-top:5px;">
              <div class="form-group"><label>Nilai Inv (Rp)</label></div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input id="AddEditNilaiInvRp" type="number" value="0.00" class="form-control text-right" disabled>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-2">
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
              <th scope="col">Nilai Debet Note</th>
              <th scope="col">Nilai Inv</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Nilai DN Rp</th>
              <th scope="col">Nilai Invoice Rp</th>
            </tr>
          </thead>
          <tbody id="detailTableData">
            <tr>
              <td colspan="8" class="text-center">Belum ada data</td>
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
          <div class="container-fluid mt-4">
            <div class="row mb-2" style="margin-top:-30px;">
              <div class="col-12 d-flex justify-content-end" style="padding-right: 0px;">
                <input id="input_search_invoice_debetnote" type="search" class="form-control" placeholder="Cari data" onkeyup="searchInvoiceDebetNote(event)">
              </div>
            </div>
            <div class="table-responsive">
              <table id="tabel_add_list_invoice" class="data-table">
                <thead class="text-center">
                  <tr>
                    <th class="text-center" scope="col">v</th>
                    <th scope="col">No Faktur</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Jatuh Tempo</th>
                    <th scope="col">Valas</th>
                    <th scope="col">Nilai Debet Note</th>
                    <th scope="col">Kurs</th>
                    <th scope="col">Nilai DN (Rp)</th>
                    <th scope="col">Hutang (Valas)</th>
                    <th scope="col">Hutang (Rp)</th>
                    <th scope="col">Keterangan</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_invoice" class="text-left">
                  <tr>
                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
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
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">
let listInvoice = []
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
    return nilai ? formatDate(nilai) : ""
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
      <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
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
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
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
  let nilai  = $("#AddEditNilai").val()
  let nilairp  = $("#AddEditNilaiRp").val()
  let kodevls  = $("#AddEditValas").val()
  let kurs  = $("#AddEditKurs").val()
  let keterangan  = $("#AddEditKeterangan").val()

  if (Number(nilai) < 0 || Number(kurs) < 0 ) {
    alertify.warning("Nilai < 0")
    return
  }

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
        let noinvoice  = $("#AddEditNoInv").val()
        let nilai  = $("#AddEditNilai").val()
        let nilairp  = $("#AddEditNilaiRp").val()
        let kodevls  = $("#AddEditValas").val()
        let kurs  = $("#AddEditKurs").val()
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
  listInvoice.forEach((item, i) => {
      if (document.getElementById(`add_checkbox${i}`).checked) {

        let checkNilai = $(`#add_inputQnt${i}`).val();
        let checkKurs = $(`#add_inputKurs${i}`).val();
        let checkNilaiRp = $(`#add_inputQntRp${i}`).val();
        listInvoice[i].Keterangan = $(`#add_inputKeterangan${i}`).val();
        listInvoice[i].inputNilai = checkNilai
        listInvoice[i].inputKurs = checkKurs
        listInvoice[i].inputNilaiRp = checkNilaiRp
        if (Number(checkNilai) < 0 || Number(checkKurs) < 0 ) {
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
      alertify.warning("Qty < 0");
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
          if (res == 1) {
            alertify.success('DN telah ditambah');
            loadAll()
            tipeform = 'edit'
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
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
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

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodecustomer").val();

  if (!kodecustsupp  ) {
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
        rowTable +=
        `<tr>
        <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>
        <td>${item.NoFaktur}</td>
        <td>${formatDate(item.Tanggal,'/')}</td>
        <td>${formatDate(item.JatuhTempo,'/')}</td>
        <td>${item.KodeVls}</td>
        <td><input id="add_inputQnt${i}" style="height:30px; min-width: 130px" type="number" value='0.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>

        <td><input id="add_inputKurs${i}" style="height:30px; min-width: 90px" type="number" value='1.00' class="form-control text-right" onBlur="onChangeNilaiKurs(${i})"></td>
        <td><input style="height:30px; min-width: 130px" id="add_inputQntRp${i}" type="number" value='0.00' class="form-control text-right"  disabled></td>

        <td class="text-right">${formatAngka(parseFloat(item.SaldoD).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Saldo).toFixed(2))}</td>

        <td><input style="height:30px; min-width: 200px" id="add_inputKeterangan${i}" type="text" value='' class="form-control text-left" ></td>

        </tr>`
      });

      document.getElementById("tabel_data_add_list_invoice").innerHTML = rowTable

      if (res.length) {
        $('.showhidemodalbodyadd').hide();
        $('#modalAddListInvoice').show();
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

function onChangeNilaiKursItem () {
  let onChangeQnt = $(`#AddEditNilai`).val();
  let onChangeKurs = $(`#AddEditKurs`).val();

  document.getElementById(`AddEditNilaiRp`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)
}

function onChangeNilaiKurs (index) {
  let onChangeQnt = $(`#add_inputQnt${index}`).val();
  let onChangeKurs = $(`#add_inputKurs${index}`).val();

  document.getElementById(`add_inputQntRp${index}`).value = parseFloat(Number(onChangeQnt) * Number(onChangeKurs)).toFixed(2)
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
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickCustomer('${item.KODECUSTSUPP}' , '${item.NAMACUSTSUPP}' , '${item.ALAMAT1}')">
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

function buttonAddPickCustomer (kode, nama , alamat) {
  document.getElementById("input_add_kodecustomer").value = kode
  document.getElementById("input_add_namacustomer").value = nama
  document.getElementById("input_add_alamatcustomer").value = alamat

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
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">0.00</td>

                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
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
        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].tanggal)
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
                  <td>${item.Keterangan}</td>
                  <td class="text-right">${item.Nilai ? formatAngka(parseFloat(item.Nilai).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
                  <td>${item.kodeVls}</td>
                  <td class="text-right">${item.Kurs ?  formatAngka(parseFloat(item.Kurs).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.NilaiRp ? formatAngka(parseFloat(item.NilaiRp).toFixed(2)) : '0.00'}</td>
                  <td class="text-right">${item.Saldo ?  formatAngka(parseFloat(item.Saldo).toFixed(2)) : '0.00'}</td>
                </tr>`
      });

      document.getElementById("detailTableData").innerHTML = rowTable
        document.getElementById("input_detail_kodecustomer").value = res[0].KodeSupp
        document.getElementById("input_detail_namacustomer").value = res[0].NamaCustSupp
        document.getElementById("input_detail_alamatcustomer").value = res[0].Alamat1
        document.getElementById("input_detail_nobukti").value = res[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(res[0].tanggal)

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

  alertify.confirm(
    'Konfirmasi Otorisasi',
    'Yakin Ingin Melakukan Otorisasi ' + nobukti + '?', function () {
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
        alertify.success('Berhasil update otorisasi');
        loadAll();
      },
      error: function (err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan, silakan refresh browser.');
      }
    });
  }, function () {
    alertify.message('Dibatalkan');
  });
}

function buttonKoreksi (nobukti ) {
  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  tipeform = 'edit'
  cleanFormAdd()
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
  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("addTableData").innerHTML = `<tr><td colspan=9 class="text-center">Belum ada data</td></tr>`
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
  document.getElementById("AddEditKurs").value = tempBarangAddEdit.Kurs
  document.getElementById("AddEditNilai").value = tempBarangAddEdit.Nilai

  document.getElementById("AddEditKeterangan").value = tempBarangAddEdit.Keterangan

  document.getElementById("AddEditNilaiRp").value = tempBarangAddEdit.NilaiRp
  document.getElementById("AddEditNilaiInv").value = tempBarangAddEdit.Saldo
  document.getElementById("AddEditNilaiInvRp").value = tempBarangAddEdit.Saldo

  $('.showhideitem').hide();
  $('#formAddEdit').show();
}

function buttonCloseForm () {
  $('.mainpage').hide();
  $('#page1').show();
}

function buttonBatalOtorisasi(nobukti) {
  alertify.confirm(
    'Konfirmasi Batal Otorisasi',
    'Yakin Ingin Membatalkan Otorisasi ' + nobukti + '?',
    function() {
      let _token = $("#_token").val();

      $.ajax({
        url: "{!! url('debetnotespbatalotorisasi') !!}",
        type: "post",
        async: false,
        data: {
          _token,
          nobukti
        },
        success: function(res) {
          alertify.success('Berhasil batal otorisasi');
          loadAll();
        },
        error: function(err) {
          console.log(err);
          alertify.warning('Terjadi kesalahan, silakan refresh browser.');
        }
      });
    },
    function() {
      console.log('Pembatalan otorisasi dibatalkan');
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

function formatAngka (angkaString) {
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
