@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Uang Muka Beli')

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
/* Halaman ini dirancang mengisi tinggi layar (lihat umbAturTinggiTabel()), jadi padding
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
   pembelianpermintaanagen.blade.php supaya tampilannya seragam. ---------- */
/* Baris SUPPLIER bisa melipat jadi beberapa baris (nama panjang), sehingga tinggi baris
   tabel jadi lebih dari satu baris teks. Kalau td Actions SENDIRI dijadikan display:flex,
   tingginya cuma mengikuti isi tombol (30px) - bukan tinggi baris - sehingga tombol
   "naik" ke atas dan bagian bawah selnya terlihat terpotong dibanding sel lain. Flex-nya
   dipindah ke pembungkus .po-aksi-wrap di DALAM td (lihat renderTabelUMB()), sedangkan
   td-nya sendiri tetap table-cell biasa supaya browser menengahkannya secara vertikal
   mengikuti tinggi baris seperti sel lain. */
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

/* Tombol di kolom Action baru muncul saat barisnya di-hover - opt-in lewat kelas
   po-aksi-hover supaya tabel lain tidak ikut terpengaruh. visibility (bukan display)
   supaya lebar kolomnya tetap dipesan. Sengaja TIDAK memakai :focus-within: klik mouse membuat tombol tetap fokus sehingga tidak ikut hilang saat kursor sudah pindah. */
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

/* ---------- Form Add/Detail (#page2 / #page3) - kartu ber-section, menggantikan
   grid col-md-4/col-md-8 lama yang dirapatkan pakai margin-top negatif. ---------- */
.form-card {
  background: var(--rt-card, #fff);
  border: 1px solid var(--rt-border, #e7e9ee);
  border-radius: 10px;
  padding: 16px 18px;
  margin-bottom: 14px;
}

.form-card-title {
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .04em;
  color: #6b7280;
  margin: 0 0 12px;
  padding-bottom: 8px;
  border-bottom: 1px solid #eef0f3;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px 16px;
}

.form-field label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #495057;
  margin-bottom: 4px;
}

.form-field .input-group {
  flex-wrap: nowrap;
}

/* ---------- Tombol chip (latar tint muda + teks berwarna) untuk tombol Submit dan Batal -
   disalin dari purchaseOrder.blade.php / pembelianpermintaanagen.blade.php supaya seragam. ---------- */
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

/* ---------- Modal pilih PO (#formAddListItem) - baris diklik langsung untuk memilih
   (tidak ada lagi tombol "+" di kolom Actions), disalin dari
   pembelianpermintaanagen.blade.php supaya seragam dengan modal cari barang di sana. ---------- */
#formAddListItem tbody tr.pick-row {
  cursor: pointer;
  transition: background-color .12s;
}

#formAddListItem tbody tr.pick-row:hover td {
  background-color: #eef2ff;
}

#formAddListItem thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee !important;
  border-top: none !important;
}

#formAddListItem tbody td {
  border-top: none !important;
  border-bottom: 1px solid #f1f3f5 !important;
  font-size: 13px;
  vertical-align: middle;
}

/* ---------- Kotak pencarian modal Pilih PO - disamakan dengan modal pencarian di menu
   Purchase Order (lihat purchasing/modals/modalPOAdd.blade.php). Tanpa ini yang tampil
   adalah widget bawaan DataTables: teks "Search:" kelihatan, kotak polos, tanpa ikon. ---------- */
#formAddListItem .dataTables_wrapper > .row:first-child > div {
  flex: 0 0 100%;
  max-width: 100%;
}

#formAddListItem .dataTables_filter {
  display: block;
  float: none;
  width: 100%;
  text-align: right;
  margin-bottom: 8px;
}

#formAddListItem .dataTables_filter label {
  font-size: 0; /* menyembunyikan teks "Search:" tanpa mengubah markup DataTables */
  margin: 0;
  display: inline-block;
}

#formAddListItem .dataTables_filter input {
  font-size: 13px;
  margin-left: 0;
  width: 240px;
  max-width: 100%;
  padding: 7px 10px 7px 32px;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  outline: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 10px center;
}

#formAddListItem .dataTables_filter input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}
</style>
@endsection

@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- <h2>Uang Muka Beli</h2> -->
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
          <input type="date" class="po-filter-inp" id="umbTglAwal" value="{!! $umbTglAwal !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="umbTglAkhir" value="{!! $umbTglAkhir !!}">
        </div>
        <input type="search" id="umbSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat umbIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="umbLen">Tampilkan</label>
          <select id="umbLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="umbBtnFilter" onclick="$('#modalFilterUMB').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat umbInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabel2" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">No Bukti</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
            <th style="padding: 4px 12px;" scope="col">No PO</th>
            <th style="padding: 4px 12px;" scope="col">Supplier</th>
            <th style="padding: 4px 12px;" scope="col">Valas</th>
            <th style="padding: 4px 12px;" scope="col">DPP</th>
            <th style="padding: 4px 12px;" scope="col">PPN</th>
            <th style="padding: 4px 12px;" scope="col">Persen</th>
            <th style="padding: 4px 12px;" scope="col">Tgl Est</th>
            <th style="padding: 4px 12px;" scope="col">Bayar</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelUMB() lewat JS, sama seperti pembelianpermintaanagen.blade.php,
               supaya susunan kolom hasil geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
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
      <!-- <h2>Uang Muka Beli</h2> -->
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

  <div id="formAdd">

    <input type="hidden" class="form-control" id="input_add_nourut" disabled>

    <div class="form-card">
      <div class="form-card-title">Informasi UMB</div>
      <div class="form-grid">
        <div class="form-field">
          <label>No Bukti</label>
          <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
        </div>
        <div class="form-field">
          <label>Tanggal</label>
          <input type="date" class="form-control text-left" id="input_add_tanggal" disabled>
        </div>
        <div class="form-field">
          <label>No PO</label>
          <div class="input-group">
            <input id="input_add_nopo" type="text" class="form-control" disabled>
            <button id="buttonAddListPO" type="button" onclick="buttonAddListPO()" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" disabled>
              <i class="bi bi-search"></i>
            </button>
          </div>
        </div>
        <div class="form-field">
          <label>Supplier</label>
          <input id="input_add_namasupplier" type="text" class="form-control" disabled>
          <input id="input_add_kodesupplier" type="hidden" disabled>
        </div>
      </div>
    </div>

    <div class="form-card">
      <div class="form-card-title">Nilai PO</div>
      <div class="form-grid">
        <div class="form-field">
          <label>Tipe PPN</label>
          <select id="input_add_tipeppn" class="form-control" disabled>
            <option value=0 selected>None</option>
            <option value=1>Exclude</option>
            <option value=2>Include</option>
          </select>
        </div>
        <div class="form-field">
          <label>Valas</label>
          <select id="input_add_valas" class="form-control" disabled></select>
        </div>
        <div class="form-field">
          <label>Kurs</label>
          <input id="input_add_kurs" type="number" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>DPP</label>
          <input id="input_add_dpp" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>PPN</label>
          <input id="input_add_ppn" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>Total</label>
          <input id="input_add_total" type="text" class="form-control text-right" disabled>
        </div>
      </div>
    </div>

    <div class="form-card">
      <div class="form-card-title">Uang Muka</div>
      <div class="form-grid">
        <div class="form-field">
          <label>Bayar</label>
          <select id="input_add_bayar" class="form-control">
            <option value=0 selected disabled>-</option>
            <option value=1>Tunai</option>
            <option value=2>Transfer</option>
          </select>
        </div>
        <div class="form-field">
          <label>DPP</label>
          <input id="input_add_totaldpp" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>PPN</label>
          <input id="input_add_totalppn" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>Subtotal</label>
          <input type="text"
            id="input_add_totalsubtotal"
            onchange="onChangeSubtotal()"
            data-a-sign=""
            data-a-dec="."
            data-a-sep=","
            class="form-control text-right input-partial-number">
        </div>
        <div class="form-field">
          <label>Tgl Est</label>
          <input id="input_add_tanggalest" type="date" class="form-control text-left">
        </div>
      </div>
    </div>

    <div class="col-md-12 mt-2 text-right">
      <button id="buttonSubmitAdd" type="button" class="btn btn-lg btn-chip-biru" onclick="submitAdd()" style="
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

</div>{{-- /#formBsGrid --}}
</div>

  <div id="page3" style="display: none" class="mainpage container-fluid" >
  <div id="formBsGrid">

    <div class="row" style="margin-bottom: 14px;">
      <div class="col-8 text-left">
        <!-- <h2>Uang Muka Beli</h2> -->
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

    <div class="form-card">
      <div class="form-card-title">Informasi UMB</div>
      <div class="form-grid">
        <div class="form-field">
          <label>No Bukti</label>
          <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
        </div>
        <div class="form-field">
          <label>Tanggal</label>
          <input type="date" class="form-control text-center" id="input_detail_tanggal" disabled>
        </div>
        <div class="form-field">
          <label>No PO</label>
          <input id="input_detail_nopo" type="text" class="form-control" disabled>
        </div>
        <div class="form-field">
          <label>Supplier</label>
          <input id="input_detail_namasupplier" type="text" class="form-control" disabled>
          <input id="input_detail_kodesupplier" type="hidden" disabled>
        </div>
      </div>
    </div>

    <div class="form-card">
      <div class="form-card-title">Nilai PO</div>
      <div class="form-grid">
        <div class="form-field">
          <label>Tipe PPN</label>
          <select id="input_detail_tipeppn" class="form-control" disabled>
            <option value=0 selected>None</option>
            <option value=1>Exclude</option>
            <option value=2>Include</option>
          </select>
        </div>
        <div class="form-field">
          <label>Valas</label>
          <input id="input_detail_valas" type="text" class="form-control" disabled>
        </div>
        <div class="form-field">
          <label>Kurs</label>
          <input id="input_detail_kurs" type="number" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>DPP</label>
          <input id="input_detail_dpp" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>PPN</label>
          <input id="input_detail_ppn" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>Total</label>
          <input id="input_detail_total" type="text" class="form-control text-right" disabled>
        </div>
      </div>
    </div>

    <div class="form-card">
      <div class="form-card-title">Uang Muka</div>
      <div class="form-grid">
        <div class="form-field">
          <label>Bayar</label>
          <select id="input_detail_bayar" class="form-control" disabled>
            <option value=0 selected disabled>-</option>
            <option value=1>Tunai</option>
            <option value=2>Transfer</option>
          </select>
        </div>
        <div class="form-field">
          <label>DPP</label>
          <input id="input_detail_totaldpp" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>PPN</label>
          <input id="input_detail_totalppn" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>Subtotal</label>
          <input id="input_detail_totalsubtotal" type="text" class="form-control text-right" disabled>
        </div>
        <div class="form-field">
          <label>Tgl Est</label>
          <input id="input_detail_tanggalest" type="date" class="form-control text-center" disabled>
        </div>
      </div>
    </div>

  </div>{{-- /#formBsGrid --}}
  </div>




<!-- start modal list item add: pilih PO -->
{{-- Disamakan persis dengan modal pemilih menu Kas (picker-kas): modal-xl, dropdown
     "Tampilkan" + kotak Search bawaan DataTables, tombol x dan Batal gaya Kas. --}}
<div class="modal fade picker-kas" id="formAddListItem" tabindex="-1" role="dialog" aria-labelledby="formAddListItemLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content" id="modalAddListPO">
      <div class="modal-header">
        <h5 class="modal-title" id="formAddListItemLabel">Pilih PO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
          <div class="col-12" style="overflow:auto; margin-top:0px; ">
          <table id="tabel_add_list_po">
            <thead>
              <tr>
                <th scope="col">No Bukti</th>
                <th scope="col">Customer</th>
                <th scope="col">DPP</th>
                <th scope="col">PPN</th>
                <th scope="col">Subtotal</th>
              </tr>
            </thead>
            <tbody id="tabel_data_add_list_po" class="text-left">
            </tbody>
          </table>
          </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
<!-- end modal list item add -->
<!-- start modal print-->
<div class="modal fade" id="modalPrint" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title">Pilih Design Cetak</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('default')">
            Cetak IDR
          </button>

          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('design3')">
            Cetak Non IDR
          </button>
        </div>

      </div>
    </div>
  </div>
<!-- end modal print-->

<!-- modal filter status otorisasi Uang Muka Beli -->
<div class="modal fade rt-filter" id="modalFilterUMB">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Uang Muka Beli
          <span class="rt-active-badge" id="umbFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterUMB').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="umbModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="umbModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="umbResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterUMB').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="umbTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter status otorisasi Uang Muka Beli -->







@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">

let tipeform = ''
let listData = []
let listPO = []
let dataPO = {}

// Dipatok, bukan diambil dari window.location - sama alasannya dengan PR_HREF di
// pembelianpermintaanagen.blade.php: harus sama persis dengan $req->href yang dikirim
// UangMukaBeliController@loadAll ke HeaderTableController@getHeaderTable.
const UMB_HREF = 'uangmukabeli'

let umbCart = []
let dataUMB = []

function umbBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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
      tipe === 0 ? h : values[i],        // 0 nama field di data
      label,                             // 1 judul kolom
      Number(isshowns[i]) === 1 ? 1 : 0, // 2 tampil
      tipeNama[tipe] || 'varchar',       // 3 tipe data
      0,                                 // 4 total (tidak dipakai halaman ini)
      isNaN(des) ? 0 : des,              // 5 jumlah desimal
      h,                                 // 6 header asli
      values[i],                         // 7 value asli
      tipe                               // 8 isnumeric asli
    ])
  });
  return cart
}

// Kolom yang tampil. WAJIB hasil filter() dari cart, bukan map/salinan, karena
// ReportTable.headHtml() memakai indexOf() terhadap gcart_header untuk mendapat
// indeks global tiap kolom.
function umbKolomTampil () {
  return (umbCart || []).filter(c => Number(c[2]) === 1)
}

function umbKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

// formatAngka() selalu menempelkan '.' + bagian desimal, sehingga input tanpa titik jadi
// "123.undefined". Dipakai versi yang sadar jumlah desimal, sama seperti poFormatAngkaDes()
// di purchaseOrder.blade.php / prFormatAngkaDes() di pembelianpermintaanagen.blade.php.
function umbFormatAngkaDes (nilai, des) {
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

function umbRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return umbFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Kalau public/js/report-table.js belum ikut terunggah, halaman harus tetap tampil:
// judul kolomnya jatuh ke <th> biasa, hanya tanpa drag & roda gigi.
function umbHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  // report-table.js tidak termuat - header turun ke versi polos: tidak bisa digeser
  // maupun disembunyikan. Diberi peringatan supaya tidak gagal secara senyap.
  console.warn('report-table.js tidak termuat - fitur geser & sembunyikan kolom dimatikan. Pastikan public/js/report-table.js ada di server.')
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

// Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman.
// report-table.js tidak punya teardown; render ulang selanjutnya hanya menulis ulang
// innerHTML-nya (lihat renderTabelUMB()) - sama seperti pembelianpermintaanagen.blade.php.
let umbRtSudahInit = false

function umbInitReportTableSekali () {
  if (umbRtSudahInit || typeof ReportTable === 'undefined') { return }
  umbRtSudahInit = true

  ReportTable.init({
    table    : '#tabel2',
    bar      : '#rtBar',
    onChange : renderTabelUMB
  })

  // DataTables memasang handler sort LANGSUNG di tiap <th>, sedangkan roda gigi/drag milik
  // report-table.js didelegasikan di <thead>. Tanpa penanganan khusus, klik roda gigi juga
  // memicu sort DataTables - hentikan event ASLINYA di fase capture, lalu tembakkan ulang
  // satu event click baru langsung ke <thead> dengan target di-override.
  let umbGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (umbGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      umbGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      umbGuardUlangKlik = false
    }, true)
  }
}

// Pastikan #rtBar duduk tepat sebelum tabel (sibling, bukan anak di dalam wrapper DataTables) -
// kalau di dalam, .DataTable().destroy() yang menulis ulang wrapper akan ikut menghapusnya.
function umbPindahBar () {
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

// Ikat kotak search custom (#umbSearch, statis di blade - di luar #tabel2_wrapper jadi
// tidak ikut terhapus saat .DataTable().destroy() menulis ulang wrapper). Diikat sekali
// lewat dataset.rtBound karena renderTabelUMB() memanggil ini tiap kali tabel di-destroy+init.
function umbIkatSearch () {
  let input = document.getElementById('umbSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

// Jumlah baris per halaman, dikendalikan dropdown #umbLen. Disimpan di variabel, bukan
// hanya dibaca dari elemen select-nya, karena renderTabelUMB() melakukan destroy+init tiap
// kali kolom digeser/disembunyikan - tanpa ini tabel selalu balik ke nilai awal walau
// dropdownnya masih menunjuk pilihan pengguna. Nilai -1 berarti "semua data".
let umbPanjangHalaman = 10
function umbIkatPanjangHalaman () {
  let sel = document.getElementById('umbLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(umbPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    umbPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(umbPanjangHalaman).draw()
  })
}

// Ubah salah satu tanggal periode -> muat ulang data dari server (loadAll() sudah aman
// dipanggil ulang, tidak ada penjaga "sudah dimuat").
function umbIkatPeriode () {
  let awal  = document.getElementById('umbTglAwal')
  let akhir = document.getElementById('umbTglAkhir')
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

// Kotak scroll tabel dibuat setinggi sisa ruang di #content, sama seperti
// poAturTinggiTabel() di purchaseOrder.blade.php.
function umbAturTinggiTabel () {
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

// 'SEMUA' = tidak menyaring. Disimpan di luar renderTabelUMB() supaya tetap berlaku saat
// tabel digambar ulang (sehabis simpan, otorisasi, dst).
let umbFilterOtorisasi = 'SEMUA'

function umbOtorisasiUMB (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

function umbUpdateFilterBadge () {
  let jml = (umbFilterOtorisasi !== 'SEMUA') ? 1 : 0
  let badge = document.getElementById('umbFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function umbTerapkanFilter () {
  umbFilterOtorisasi = $('#umbModalOtorisasi').val() || 'SEMUA'
  umbUpdateFilterBadge()
  $('#modalFilterUMB').modal('hide')
  renderTabelUMB()
}

function umbResetFilter () {
  umbFilterOtorisasi = 'SEMUA'
  $('#umbModalOtorisasi').val('SEMUA')
  umbUpdateFilterBadge()
  $('#modalFilterUMB').modal('hide')
  renderTabelUMB()
}

/* ---- Jembatan ke mesin penyimpan milik report-table.js ----
 * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
 * didefinisikan: report-table.js sudah punya fallback yang memutasi gcart_header sendiri
 * lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke doSimpanHeader di bawah.
 */
window.g_href = UMB_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  umbCart.forEach((c) => {
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
      href     : UMB_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Dipakai tombol "Reset kolom" di bar. Tombol itu hanya muncul kalau fungsi ini ada.
// Harus async:false karena report-table.js langsung menggambar ulang setelahnya.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : UMB_HREF,
      reset  : 1
    },
    success : function (res) {
      umbCart = umbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = umbCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

jQuery(function($) {
  $('.input-partial-number').autoNumeric('init',
    {
      minimumValue : '0',
      // negativeSignCharacter: 'z'
     }
  );
});

$(document).ready(function(){
  umbInitReportTableSekali()
  muatDropdownValas()
  loadAll()
});

// Valas dipilih lewat dropdown (pola sama dengan halaman Purchase Order), bukan tombol
// browse. Nilainya tetap mengikuti PO yang dipilih, jadi select-nya tetap disabled.
let listValas = []

function muatDropdownValas () {

  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      listValas = res

      let selectEl = document.getElementById("input_add_valas")
      let kodeTerpilih = selectEl.value

      selectEl.innerHTML = ''
      listValas.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.kodevls
        opt.textContent = `${item.kodevls} - ${item.namavls}`
        selectEl.appendChild(opt)
      })

      if (kodeTerpilih) {
        setValasValue(kodeTerpilih)
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

// Kode valas dari PO belum tentu ada di daftar (misal data lama), jadi optionnya
// ditambahkan dulu supaya nilainya tetap kelihatan.
function setValasValue (kode) {
  let selectEl = document.getElementById("input_add_valas")
  kode = kode ? String(kode).trim() : ''

  if (kode && !Array.from(selectEl.options).some(opt => opt.value === kode)) {
    let opt = document.createElement('option')
    opt.value = kode
    opt.textContent = kode
    selectEl.appendChild(opt)
  }

  selectEl.value = kode
}

function openPrintModal(nobukti) {
  selectedNoBukti = nobukti
  $('#modalPrint').modal('show')
}

function choosePrint(type) {
  $('#modalPrint').modal('hide')

  if (type === 'default') {
    submitPrint(selectedNoBukti)
  } 
  else if (type === 'design3') {
    submitPrint2(selectedNoBukti)
  } 
}

function buttonDelete (NOBUKTI) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  console.log('buttonEdit')
  console.log(NOBUKTI )

  let akses = $("#akses_ishapus").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus UMB '+ NOBUKTI +' ?',
      function() {
        let _token  = $("#_token").val()


        let choice = 'D'
        let nobukti = NOBUKTI
        let nourut = ''
        let noso = ''
        let valas = ''
        let kurs = 0
        let dppx = 0
        let presentase = 0
        let ppnx = 0
        let subtotal = 0
        let tanggal = null
        let bayar = 0
        let flagtipe = 0
        let tglest = null
        let maxol = 1
        let jmlrecord = 1
        let pbeli = 1

        console.log({
          choice,
          nobukti ,
          nourut ,
          noso ,
          valas ,
          kurs ,
          dppx ,
          presentase ,
          ppnx ,
          subtotal ,
          tanggal ,
          bayar ,
          flagtipe ,
          tglest ,
          maxol ,
          jmlrecord,
          pbeli
        })

        $.ajax({
          url: "{!! url('uangmukabelispadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti ,
            nourut ,
            noso ,
            valas ,
            kurs ,
            dppx ,
            presentase ,
            ppnx ,
            subtotal ,
            tanggal ,
            bayar ,
            flagtipe ,
            tglest ,
            maxol ,
            jmlrecord,
            pbeli
          },
          success: function(res) {
	console.log(res)	
            if (res == 1) {
              
              alertify.success('Berhasil hapus UMB')
              loadAll()
              buttonCloseForm()

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
    ,function(){
      console.log('no')
    });







}


// function onChangeSubtotal () {
//     let xsubtotal = $("#input_add_totalsubtotal").val()
// console.log(listData)
// 	console.log(dataPO.NilaiPPN) 
// console.log("asd")

//     if (tipeform == 'add') {
//       if (dataPO.pPPN == 0 || !dataPO.pPPN) {
//         document.getElementById("input_add_totaldpp").value = formatAngkaY(xsubtotal)
//         document.getElementById("input_add_totalppn").value = '0.00'

//       } else {
//         let xdpp = formatAngkaY(Number(xsubtotal) / (100 + Number(dataPO.NilaiPPN)) * 100)
//         document.getElementById("input_add_totalppn").value = formatAngkaY(Number(xsubtotal) - xdpp)
//         document.getElementById("input_add_totaldpp").value = formatAngkaY(xdpp)

//       }
//     } else {
//       if (listData[0].TipePPN == 0 || !listData[0].TipePPN) {
//         document.getElementById("input_add_totaldpp").value = formatAngkaY(xsubtotal)
//         document.getElementById("input_add_totalppn").value = '0.00'

//       } else {
//         let xdpp  =formatAngkaY(( Number(xsubtotal) / (100 + Number(listData[0].NilaiPPN))  * 100))
//         document.getElementById("input_add_totalppn").value = formatAngkaY(Number(xsubtotal) - xdpp)
//         document.getElementById("input_add_totaldpp").value = formatAngkaY(xdpp)

//       }

//     }

// }

function formatAngkaY (angka) {

if (!angka) {
  return '0.00'
} else {
  return parseFloat(angka).toFixed(2)
}

}

function formatAngkaX (angka) {

if (!angka) {
  return '0.00'
} else {
  return formatAngka(parseFloat(angka).toFixed(2))
}

}

function formatAngka (angkaString) {

angkaString = String(angkaString)

let tempAngka = angkaString.split('.')

if (tempAngka.length == 1) {
  tempAngka.push('00')
}

if (tempAngka[0][0] == '-') {

  let temp2=''

  let tempAngka1 = tempAngka[0].split('-')

  for (let i = 0; i < tempAngka1[1].length; i++) {

    if (i != 0 && i % 3 == 0) {
      temp2 = ',' + temp2
    }

    temp2 =
      tempAngka1[1][tempAngka1[1].length - i -1] + temp2

  }

  temp2 += '.' + tempAngka[1]
  temp2 = '-' + temp2

  return temp2
}

let temp1 = ''

for (let i = 0; i < tempAngka[0].length; i++) {

  if (i != 0 && i % 3 == 0) {
    temp1 = ',' + temp1
  }

  temp1 =
    tempAngka[0][tempAngka[0].length - i -1] + temp1

}

temp1 += '.' + tempAngka[1]

return temp1
}

function onChangeSubtotal () {

  // ambil value display
  let xsubtotal =
    $("#input_add_totalsubtotal").val()

  // hilangkan koma supaya aman dihitung
  xsubtotal =
    xsubtotal.replace(/,/g, '')

  // convert ke number
  xsubtotal =
    Number(xsubtotal)

  // tampilkan kembali format ribuan ke input user
  $("#input_add_totalsubtotal").val(
    formatAngkaX(xsubtotal)
  )

  if (tipeform == 'add') {

    if (dataPO.pPPN == 0 || !dataPO.pPPN) {

      // tetap numeric-safe
      document.getElementById("input_add_totaldpp").value =
        formatAngkaX(xsubtotal)

      document.getElementById("input_add_totalppn").value =
        '0.00'

    } else {

      let xdpp =
        (xsubtotal / (100 + Number(dataPO.NilaiPPN))) * 100

      let xppn =
        xsubtotal - xdpp

      // tetap numeric-safe
      document.getElementById("input_add_totaldpp").value =
        formatAngkaX(xdpp)

      document.getElementById("input_add_totalppn").value =
        formatAngkaX(xppn)

    }

  } else {

    if (listData[0].TipePPN == 0 || !listData[0].TipePPN) {

      // tetap numeric-safe
      document.getElementById("input_add_totaldpp").value =
        formatAngkaX(xsubtotal)

      document.getElementById("input_add_totalppn").value =
        '0.00'

    } else {

      let xdpp =
        (xsubtotal / (100 + Number(listData[0].NilaiPPN))) * 100

      let xppn =
        xsubtotal - xdpp

      // tetap numeric-safe
      document.getElementById("input_add_totaldpp").value =
        formatAngkaX(xdpp)

      document.getElementById("input_add_totalppn").value =
        formatAngkaX(xppn)

    }

  }
}

function cleanForm () {
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate =  new Date()
  document.getElementById("input_add_nopo").value =  ''
  document.getElementById("input_add_kodesupplier").value =  ''
  document.getElementById("input_add_namasupplier").value =  ''
  document.getElementById("input_add_tipeppn").value =  0
  setValasValue('IDR')
  document.getElementById("input_add_kurs").value =  '1.00'
  document.getElementById("input_add_bayar").value =  0
  document.getElementById("input_add_dpp").value =  '0.00'
  document.getElementById("input_add_ppn").value =  '0.00'
  document.getElementById("input_add_total").value =  '0.00'


  document.getElementById("input_add_totaldpp").value =  '0.00'
  document.getElementById("input_add_totalppn").value =  '0.00'
  document.getElementById("input_add_totalsubtotal").value =  '0.00'
  document.getElementById("input_add_tanggalest").value =  ''

}



function lockForm (value = true) {

  document.getElementById("input_add_tanggal").disabled = value
  // document.getElementById("input_add_tipeppn").disabled = value
  document.getElementById("buttonAddListPO").disabled = value
  // Valas kini dropdown (diisi muatDropdownValas), tidak ada lagi tombol browse
  // document.getElementById("buttonAddListValas").disabled = true
  document.getElementById("input_add_kurs").disabled = value
  //document.getElementById("input_add_totalsubtotal").disabled = value
  // document.getElementById("input_add_tanggalest").disabled = value

}

function lockFormAdd (value = true) {

//document.getElementById("input_add_tanggal").disabled = value
document.getElementById("input_add_tipeppn").disabled = value
//document.getElementById("buttonAddListPO").disabled = value
// Valas kini dropdown (diisi muatDropdownValas), tidak ada lagi tombol browse
// document.getElementById("buttonAddListValas").disabled = true
//document.getElementById("input_add_kurs").disabled = value
//document.getElementById("input_add_totalsubtotal").disabled = value
// document.getElementById("input_add_tanggalest").disabled = value

}

function buttonCloseForm () {
  $('.mainpage').hide();
  $('#page1').show();
}

function buttonAdd () {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  tipeform = 'add'
  cleanForm()
  lockForm(false)
  setNewNoBukti()
  $(".mainpage").hide()
  $("#page2").show()
}

function buttonAddPickPO (index) {
  console.log(listPO[index])
  dataPO = listPO[index]

  document.getElementById("input_add_nopo").value = dataPO.NoBukti
  document.getElementById("input_add_namasupplier").value = dataPO.NamaCustSupp
  document.getElementById("input_add_kodesupplier").value = dataPO.KodeSupp
  document.getElementById("input_add_dpp").value = formatAngkaX(dataPO.DPP)
  document.getElementById("input_add_ppn").value = formatAngkaX(dataPO.PPN)
  document.getElementById("input_add_total").value = formatAngkaX(dataPO.Nnet)
  setValasValue(dataPO.Valas)
  document.getElementById("input_add_kurs").value = formatAngkaX(dataPO.Kurs)
  document.getElementById("input_add_tipeppn").value = dataPO.pPPN
  document.getElementById("input_add_bayar").value = dataPO.tipebayar
  document.getElementById("input_add_totaldpp").value = '0.00'
  document.getElementById("input_add_totalppn").value = '0.00'
  document.getElementById("input_add_totalsubtotal").value = '0.00'


  $("#formAddListItem").modal("toggle")
}

function buttonAddListPO () {

  $.ajax({
    url: "{!! url('uangmukabelilistpo') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {

      console.log(res)
      listPO = res

      let rowTable = ''
      // NoBukti, NamaCustSupp , DPP, PPN , Nnet

      listPO.forEach((item, i) => {
        rowTable += `
          <tr class="pick-row" onclick="buttonAddPickPO(${i})">
            <td>${item.NoBukti}</td>
            <td>${item.NamaCustSupp}</td>
            <td class="text-right">${formatAngkaX(item.DPP)}</td>
            <td class="text-right">${formatAngkaX(item.PPN)}</td>
            <td class="text-right">${formatAngkaX(item.Nnet)}</td>
          </tr>
        `
      });

      if (!listPO.length) {
        rowTable = '<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>'
      }

      if ($.fn.DataTable.isDataTable('#tabel_add_list_po')) {
        $('#tabel_add_list_po').DataTable().destroy();
      }

      document.getElementById("tabel_data_add_list_po").innerHTML = rowTable
      pickerKasInit('tabel_add_list_po')
      // Placeholder "Cari Data" dilepas - kotak Search disamakan persis dengan modal pemilih menu Kas.
      // $('#formAddListItem .dataTables_filter input').attr('placeholder', 'Cari Data')
      $("#formAddListItem").modal("show")

    }})
}

function refreshDataTable (nobukti) {
  listData = []
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('uangmukabelidetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
	console.log("Refresh")
      console.log(res)
      listData = res


      if (!listData.length) {
        alertify.success("Data habis")
        $(".mainpage").hide()
        $("#page1").show()
        return
      }


      /// convert timezone
      setDateInputValue("input_add_tanggal", listData[0].TANGGAL)
      setDateInputValue("input_add_tanggalest", listData[0].TglEst)

      document.getElementById("input_add_nopo").value =  listData[0].NOSO
      document.getElementById("input_add_kodesupplier").value =  listData[0].KodeSupp
      document.getElementById("input_add_namasupplier").value =  listData[0].NamaCustSupp
      document.getElementById("input_add_tipeppn").value = listData[0].TipePPN
      setValasValue(listData[0].VALAS)
      document.getElementById("input_add_kurs").value =  listData[0].KURS
      document.getElementById("input_add_bayar").value =  listData[0].Bayar
      document.getElementById("input_add_dpp").value =  formatAngkaX(listData[0].DPPSO)
      document.getElementById("input_add_ppn").value =  formatAngkaX(listData[0].SOPPN)
      document.getElementById("input_add_total").value =  formatAngkaX(listData[0].SUBTOTALSO)
      document.getElementById("input_add_nobukti").value =  nobukti


      document.getElementById("input_add_totaldpp").value =  formatAngkaX(listData[0].DPP)
      document.getElementById("input_add_totalppn").value =  formatAngkaX(listData[0].PPN)
      document.getElementById("input_add_totalsubtotal").value = formatAngkaX(listData[0].SUBTOTAL)
      // console.log(listData[0].TglEst)
      

    }})
}

//buat convert tanggal

function setDateInputValue(id, dateString) {
  if (!dateString) {
    document.getElementById(id).value = ''
    return
  }

  // ambil YYYY-MM-DD saja
  let cleanDate = dateString.split(' ')[0]
  document.getElementById(id).value = cleanDate
}

//end buat convert tanggal

function loadAll () {
  // Indikator "sedang memuat" - pola umum di proyek ini untuk aksi yang bisa memakan
  // waktu, supaya pengguna tahu aplikasinya sedang bekerja dan tidak mengira layarnya
  // menggantung (lihat loadingHtml() di public/js/report-table.js).
  document.getElementById('tabel_data').innerHTML =
    '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>'

  $.ajax({
    url: "{!! url('uangmukabeliloadall') !!}",
    type: "get",
    async: true,
    data: {
      tglawal: $('#umbTglAwal').val(),
      tglakhir: $('#umbTglAkhir').val()
    },
    success: function (res) {
      umbCart = umbBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = umbCart
      dataUMB = res.listData1 || []
      renderTabelUMB()
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function renderTabelUMB () {
  window.g_modeReport = 1
  window.gcart_header = umbCart

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  // Satu daftar kolom untuk header DAN isi baris, supaya jumlah kolomnya selalu sama.
  let cols = umbKolomTampil()
  let kolomRender = cols.map(umbKolomRender)

  // <thead> HANYA ditulis ulang innerHTML-nya, elemennya sendiri tidak diganti -
  // sudah diikat sekali oleh umbInitReportTableSekali().
  let thead = document.getElementById('tabel_header')
  thead.innerHTML = umbHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataUMB || []
  if (umbFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return umbOtorisasiUMB(item) === umbFilterOtorisasi })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let isOtorisasi = Number(item.IsOtorisasi1) || 0

    // Tombol aksi ikut status otorisasi barisnya - menggantikan pemisahan lewat tab
    // "UMB" / "UMB Sudah Diotorisasi" yang dipakai tampilan lama.
    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${item.NOBUKTI}')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NOBUKTI}')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="openPrintModal('${item.NOBUKTI}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NOBUKTI}')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-danger btn-sm" type="button" title="Hapus" onclick="buttonDelete('${item.NOBUKTI}')"><i class="bi bi-trash"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${item.NOBUKTI}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${umbRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${umbRenderNilai(c, item)}</td>`
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

  // Baris "Tidak ada data" TIDAK ditulis manual di sini - baris manual hanya berisi 1 sel
  // sedangkan header punya banyak kolom, dan DataTables mencoba mengindeks sel-sel yang
  // tidak ada di situ lalu crash (_DT_CellIndex). Biarkan <tbody> kosong dan serahkan ke
  // opsi language.emptyTable di bawah - DataTables sendiri yang menggambar baris kosongnya
  // dengan colspan yang benar.
  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: umbPanjangHalaman,
    // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom
    // Actions). Data sudah datang terurut dari server (Tanggal/NoBukti terbaru dulu).
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  umbPindahBar()
  umbIkatSearch()
  umbIkatPanjangHalaman()
  umbIkatPeriode()
  // Init DataTable di atas mereset filter pencarian - kotak #umbSearch sendiri statis
  // di blade dan nilainya tidak ikut hilang, jadi diterapkan ulang di sini.
  let inputSearch = document.getElementById('umbSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
  umbAturTinggiTabel()
}


function buttonOtorisasi (nobukti) {
  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('uangmukabelispotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

          },
          success: function(res) {
            alertify.success('Berhasil update otorisasi')
            loadAll()



          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })




}


function buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)



  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.prompt("Masukkan keterangan batal otorisasi nomor   " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          $.abort();
        }        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('uangmukabelispbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti,
          pket :value

          },
          success: function(res) {
            alertify.success('Berhasil batal otorisasi')
            loadAll()



          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })
      }
    ,function(){
      console.log('no')
	alertify.error("Action cancelled");
    });



}

function submitAdd () {
  let choice = 'I'
  if (tipeform == 'edit') {
    choice = 'U'
  }
  let _token =  $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let nourut = $("#input_add_nourut").val()
  let nopo = $("#input_add_nopo").val()
  let kodesupplier = $("#input_add_kodesupplier").val()
  let namasupplier = $("#input_add_namasupplier").val()
  let tipeppn = $("#input_add_tipeppn").val()
  let valas = $("#input_add_valas").val()
  let kurs = $("#input_add_kurs").val()
  let bayar = $("#input_add_bayar").val()
  let tanggal = $("#input_add_tanggal").val()

  // let ppn = Math.floor(Number(($("#input_add_ppn").val() || '0').replace(/,/g, '')))
  // let totaldpp = Math.floor(Number(($("#input_add_totaldpp").val() || '0').replace(/,/g, '')))
  // let dpp = Math.floor(Number(($("#input_add_dpp").val() || '0').replace(/,/g, '')))
  // let totalppn = Math.floor(Number(($("#input_add_totalppn").val() || '0').replace(/,/g, '')))
  // let totalsubtotal = Math.floor(Number(($("#input_add_totalsubtotal").val() || '0').replace(/,/g, '')))



  let ppn = parseFloat(Number(($("#input_add_ppn").val() || '0').replace(/,/g, '')))
  let totaldpp =  parseFloat(Number(($("#input_add_totaldpp").val() || '0').replace(/,/g, '')))
  let dpp =  parseFloat(Number(($("#input_add_dpp").val() || '0').replace(/,/g, '')))
  let totalppn =  parseFloat(Number(($("#input_add_totalppn").val() || '0').replace(/,/g, '')))
  let totalsubtotal =  parseFloat(Number(($("#input_add_totalsubtotal").val() || '0').replace(/,/g, '')))

  let pbeli = 1
  let jmlrecord = 0
  let presentase = 100 / (Number(dpp) / Number(totaldpp))
  let dppdetail = 0
  if (choice == 'U') {
    dppdetail = listData[0].DPP
    console.log ('ffffffffffffffff',listData[0].DPP)
    }
  let tanggalest = $("#input_add_tanggalest").val()
  let maxol = 1

  if (choice == 'I' ) {
    if( !nopo ) {

      alertify.warning("Isi no po")
      return
    }
    if( !bayar ) {

      alertify.warning("Pilih tipe bayar")
      return
    }
    if( Number(totalsubtotal) < 0 ) {

      alertify.warning("Subtotal < 0")
      return
    }

  }
  console.log({
    _token,
    choice,
    nobukti ,
    nourut ,
    noso: nopo ,
    valas ,
    kurs ,
    dppx : totaldpp ,
    presentase ,
    ppnx :totalppn ,
    subtotal : totalsubtotal ,
    tanggal ,
    bayar: bayar ,
    flagtipe : tipeppn,
    tglest: tanggalest ,
    maxol ,
    jmlrecord,
    dppdetail
  })
  console.log('NO PO:', nopo)
  $.ajax({
    url: "{!! url('uangmukabelispadd') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      choice,
      nobukti ,
      nourut ,
      noso: nopo ,
      valas ,
      kurs ,
      dppx : totaldpp ,
      presentase ,
      ppnx :totalppn ,
      subtotal : totalsubtotal ,
      tanggal ,
      bayar: bayar ,
      flagtipe : tipeppn,
      tglest: tanggalest ,
      maxol ,
      jmlrecord,
      pbeli,
      dpp,
	    dppdetail
    },
    success: function(res) {
      if (res == 1) {
        console.log(res)
        if (choice == 'I') {
          alertify.success('Berhasil tambah UMB')
          loadAll()
          lockFormAdd(true)
          cleanForm()
          setNewNoBukti()
        } else {
          alertify.success('Berhasil edit UMB')
          $('#page2').hide()
          $('#page1').show()
          loadAll()
        }
        
        // $("#form").modal('toggle')
        // buttonCloseForm()

      }
      if (res == 2) {
        alertify.warning('Nobukti telah direfresh, silahkan submit ulang')
        setNewNoBukti()
      }
      if (res == 3) {
        alertify.warning('Total Uang Muka melebihi PO')

      }
      if (res == 4) {
        alertify.warning('PO close tidak boleh ada uang muka')

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

function buttonKoreksi (nobukti) {
  let pcekglobal = 0
  $.ajax({
    url: "{!! url('ceklockperiode') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      if (res.length ) {
        pcekglobal = 1
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

if (pcekglobal) {
  alertify.warning("Periode sudah dikunci")
  return
}
  console.log(nobukti)
  tipeform = 'edit'
  refreshDataTable(nobukti)

  if (!listData.length) {
    alertify.warning("Data tidak ditemukkan")
    return
  }

  if (listData[0].IsOtorisasi1 == 1) {
    alertify.warning("Data sudah diotorisasi")
    return
  } else {
    $('.mainpage').hide()
    $('#page2').show()

    lockForm(true)
  }



}


function buttonDetail (nobukti) {
  console.log(nobukti)

  listData = []
  let _token = $("#_token").val()
  $.ajax({
    url: "{!! url('uangmukabelidetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {

      console.log(res)
      listData = res


      if (!listData.length) {
        alertify.success("Data tidak ditemukkan")
        $(".mainpage").hide()
        $("#page1").show()
        return
      }

      //// convert timezone
      setDateInputValue("input_detail_tanggal", listData[0].TANGGAL)
      setDateInputValue("input_detail_tanggalest", listData[0].TglEst)
      
      document.getElementById("input_detail_nopo").value =  listData[0].NOSO
      document.getElementById("input_detail_kodesupplier").value =  listData[0].KodeSupp
      document.getElementById("input_detail_namasupplier").value =  listData[0].NamaCustSupp
      document.getElementById("input_detail_tipeppn").value = listData[0].TipePPN
      document.getElementById("input_detail_valas").value =  listData[0].VALAS
      document.getElementById("input_detail_kurs").value =  listData[0].KURS
      document.getElementById("input_detail_bayar").value =  listData[0].Bayar
      document.getElementById("input_detail_dpp").value =  formatAngkaX(listData[0].DPPSO)
      document.getElementById("input_detail_ppn").value =  formatAngkaX(listData[0].SOPPN)
      document.getElementById("input_detail_total").value =  formatAngkaX(listData[0].SUBTOTALSO)
      document.getElementById("input_detail_nobukti").value =  nobukti


      document.getElementById("input_detail_totaldpp").value =  formatAngkaX(listData[0].DPP)
      document.getElementById("input_detail_totalppn").value =  formatAngkaX(listData[0].PPN)
      document.getElementById("input_detail_totalsubtotal").value = formatAngkaX(listData[0].SUBTOTAL)
      // console.log(listData[0].TglEst)


        $('.mainpage').hide()
        $('#page3').show()
    }})
}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'UMB'
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kode
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut
    }})
}


function formatDate (date , pemisah = '-') {
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

// function formatAngkaY (angka) {
//   if (!angka) {
//     return '0.00'
//   } else {
//     return parseFloat(angka).toFixed(2)
//   }

// }

// function formatAngkaX (angka) {
//   if (!angka) {
//     return '0.00'
//   } else {
//     return formatAngka(parseFloat(angka).toFixed(2))
//   }

// }

// function formatAngka (angkaString) {
//   let tempAngka = angkaString.split('.')

//   if (tempAngka[0][0] == '-') {
//     let temp2=''

//     let tempAngka1 = tempAngka[0].split('-')
//     for (let i = 0; i < tempAngka1[1].length; i++) {
//       if (i != 0 && i % 3 == 0) {
//         temp2 = ',' + temp2
//       }
//       temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2

//     }
//     temp2 += '.' + tempAngka[1]
//     temp2 = '-' + temp2

//     return temp2
//   }
//   let temp1 = ''
//   for (let i = 0; i < tempAngka[0].length; i++) {
//     if (i != 0 && i % 3 == 0) {
//       temp1 = ',' + temp1
//     }
//     temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1

//   }
//   temp1 += '.' + tempAngka[1]
//   return temp1
// }

function submitPrint (nobukti) {

  let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('uangmukabeliprint') !!}",
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
  
    let arrayDataPrint = []
    for (let i = 0; i < dataPrint.length; i+=7) 
    {
      let tempArray = dataPrint.slice(i,i+7)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];
    let tglCetak = dataPrint[0].tanggalcetak.split(' ')[0];
    let tglOto = dataPrint[0].TGLOTO1.split(' ')[0];
    let userLogin = "{{ auth()->user()->username }}"


    const now = new Date()
    const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    
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

      .body-main-prints {
        width: 21cm;
        height: 14cm;
        position: relative;
        display: flex;
        flex-direction: column;
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
                      <div class="pb-1 ps-3" style="width: 85%">
                        <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                        <div class="pb-1" style="width: 100%">JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18 RT.022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR</div>
                        <div class="pb-1" style="width: 100%">TELP : 0541 - 4104142 | FAX : 0541 - 4104195</div>
                        <div class="pb-1" style="width: 100%">E-Mail : sml@indo.net.id</div>
                      </div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">`+tanggalOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Supplier</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NamaCustSupp}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tipe Bayar</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].TipeBayarUM}</div>
                    </div>
                  </div>

                  <div style="width: 40%">
                    <div style="display: flex; width: 100%">
                      <h2 class="m-0 pb-2">INVOICE UANG MUKA PEMBELIAN</h2>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. UMB</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOBUKTI}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. PO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].nopo}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">NO. PR/SO</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].noso}</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">No. PO Cust</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].NOPOCUST}</div>
                    </div>
                  </div>

                </div>
   <table
    class="detail-spb-table"
    style="width: 95%; height: 50px; max-height: 100px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
                <thead>
                  <tr>
                    <td class="text-center" style="width: 2%" >No.</td>
                    <td class="text-center" style="width: 35%">NAMA BARANG</td>
                    <td class="text-center" style="width: 15%">KODE BRG</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 8%">QTY</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
                    <td class="text-center" style="width: 5%">DISKON</td>
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
        tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
  tempPrintStr += `
    <tr>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 2%; text-align: center;">${z+1}</td>
      <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NamaBrg}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.KodeBrg}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.Satuan}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.Qnt ? formatAngka(itemSub.Qnt) : ''}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${formatAngka(parseFloat(itemSub.Harga).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${formatAngka(parseFloat(itemSub.DISC).toFixed(2))}</td>
      <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${formatAngka(parseFloat(itemSub.Subtotal).toFixed(2))}</td>
    </tr>`;
  z++;
});

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 20px;">

  <div style="width: 50%; font-family: sans-serif; font-size: 10px;" class='text-right'>
    <table
      class="detail-spb-table mb-2"
      style="width: 100%; margin-top: -15px ; font-family: sans-serif;
      font-size: 10px ">
      <tr>
        <td class="no-border text-center" style="width: 10%"></td>
        <td class="no-border text-center" style="width: 35%">Disetujui Oleh</td>
        <td class="no-border text-center" style="width: 10%"></td>
        <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
        <td class="no-border text-center" style="width: 10%"></td>
      </tr>
      <tr style="height: 2.5rem">
        <td class="no-border">&nbsp;</td>
      </tr>

      <tr>
      <td class="no-border px-2" style="margin-top: -20px;">
        </td>
        <td class="no-border px-2">
        <p class="m-0" style="border-bottom: 1px solid">${dataPrint[0].FullName}</p>
        <p class="m-0">${tglOto}</p>
        </td>
        <td class="no-border px-2">
        </td>
        <td class="no-border px-2">
        <p class="m-0" style="border-bottom: 1px solid">${userLogin}</p>
        <p class="m-0">${tglCetak}</p>
        </td>
        <td class="no-border px-2">
        </td>
      </tr>
    </table>
  </div>`

  if(i == arrayDataPrint.length - 1){        
   tempPrintStr += `
  <div style="width: 62%; font-family: sans-serif; font-size: 10px;">

    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
      <div style="width: 30%; text-align:left;"> SUB TOTAL </div>
      <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Brutto).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
      <div style="width: 30%; text-align:left;"> UANG MUKA ${parseInt(dataPrint[0].persen)}%</div>
      <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NuangMuka).toFixed(2))}</div>
    </div>
    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
      <div style="width: 30%; text-align:left;">PPN ${dataPrint[0].nilaippn}</div>
      <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].PPN).toFixed(2))}</div>
    </div>

    <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 6px; position: relative;">
      <div style="width: 30%; text-align:left;"> TOTAL </div>
      <div style="
        position: absolute;
        right: 0;
        bottom: 3px;
        width: 50%;
        border-bottom: 1px solid #000;">
      </div>

      <!-- garis bawah 2 -->
      <div style="
        position: absolute;
        right: 0;
        bottom: 0;
        width: 50%;
        border-bottom: 1px solid #000;">
      </div>
      <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].subtotalum).toFixed(2))}</div>
    </div>

  </div>`};

  tempPrintStr += `
    </div>

      <div class="footer-print-date">
        <table class="m-0" style="width: 100% ; font-family: sans-serif;
        font-size: 10px">
          <tr>
            <td class="no-border">${i+1}/${arrayDataPrint.length}        `+userLogin+`          `+tanggalOnly+`      `+jamCetak+`</td>
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

function submitPrint2 (nobukti) {

let _token = $('#_token').val()

  $.ajax({
    url: "{!! url('uangmukabeliprint') !!}",
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

  let arrayDataPrint = []
  for (let i = 0; i < dataPrint.length; i+=7) 
  {
    let tempArray = dataPrint.slice(i,i+7)
    arrayDataPrint.push(tempArray)
  }

  let printContent = ''
  let imageContent = document.getElementById(`imagecontainer`).innerHTML;
  let css = ''
  let hdr = ''
  let str= ''
  let ftr= ''
  let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];
  let tglCetak = dataPrint[0].tanggalcetak.split(' ')[0];
  let tglOto = dataPrint[0].TGLOTO1.split(' ')[0];
  let userLogin = "{{ auth()->user()->username }}"


  const now = new Date()
  const jamCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
  
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

    .body-main-prints {
      width: 21cm;
      height: 14cm;
      position: relative;
      display: flex;
      flex-direction: column;
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
                    <div class="pb-1 ps-3" style="width: 85%">
                      <h2 class="m-0 pb-2">CV. SINAR MAHAKAM LESTARI</h2>
                      <div class="pb-1" style="width: 100%">JL. AMPERA PERGUDANGAN MANGKUPALAS BISNIS CENTRE BLOK D NO.18 RT.022 SIMPANG PASIR PALARAN SAMARINDA-KALIMANTAN TIMUR</div>
                      <div class="pb-1" style="width: 100%">TELP : 0541 - 4104142 | FAX : 0541 - 4104195</div>
                      <div class="pb-1" style="width: 100%">E-Mail : sml@indo.net.id</div>
                    </div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">Tanggal</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 75%">`+tanggalOnly+`</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">Supplier</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 75%">${dataPrint[0].NamaCustSupp}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 20%">Tipe Bayar</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 75%">${dataPrint[0].TipeBayarUM}</div>
                    </div>
                </div>

                <div style="width: 40%">
                  <div style="display: flex; width: 100%">
                    <h2 class="m-0 pb-2">INVOICE UANG MUKA PEMBELIAN</h2>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">No. UMB</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 75%">${dataPrint[0].NOBUKTI}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">No. PO</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 75%">${dataPrint[0].nopo}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">NO. PR/SO</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 75%">${dataPrint[0].noso}</div>
                  </div>
                  <div style="display: flex; width: 100%">
                    <div class="pb-1" style="width: 20%">No. PO Cust</div>
                    <div class="pb-1" style="width: 5%">:</div>
                    <div class="pb-1" style="width: 75%">${dataPrint[0].NOPOCUST}</div>
                  </div>
                </div>

              </div>
 <table
  class="detail-spb-table"
  style="width: 95%; height: 50px; max-height: 100px; font-family: sans-serif; display: table; font-size: 10px; border: 1px solid #3c3c3c;">
              <thead>
                <tr>
                  <td class="text-center" style="width: 2%" >No.</td>
                  <td class="text-center" style="width: 35%">NAMA BARANG</td>
                  <td class="text-center" style="width: 15%">KODE BRG</td>
                  <td class="text-center" style="width: 5%">SAT</td>
                  <td class="text-center" style="width: 8%">QTY</td>
                  <td class="text-center" style="width: 10%">HARGA</td>
                  <td class="text-center" style="width: 5%">DISKON</td>
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
      tempPrintStr += `<tbody border="1">`;
item.forEach((itemSub, j) => {
tempPrintStr += `
  <tr>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 2%; text-align: center;">${z+1}</td>
    <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NamaBrg}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.KodeBrg}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.Satuan}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.Qnt ? formatAngka(itemSub.Qnt) : ''}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${formatAngka(parseFloat(itemSub.Harga).toFixed(2))}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: right;">${formatAngka(parseFloat(itemSub.DISC).toFixed(2))}</td>
    <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${formatAngka(parseFloat(itemSub.Subtotal).toFixed(2))}</td>
  </tr>`;
z++;
});

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

       tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 20px;">

<div style="width: 50%; font-family: sans-serif; font-size: 10px;" class='text-right'>
  <table
    class="detail-spb-table mb-2"
    style="width: 100%; margin-top: -15px ; font-family: sans-serif;
    font-size: 10px ">
    <tr>
      <td class="no-border text-center" style="width: 10%"></td>
      <td class="no-border text-center" style="width: 35%">Disetujui Oleh</td>
      <td class="no-border text-center" style="width: 10%"></td>
      <td class="no-border text-center" style="width: 35%">Dibuat Oleh</td>
      <td class="no-border text-center" style="width: 10%"></td>
    </tr>
    <tr style="height: 2.5rem">
      <td class="no-border">&nbsp;</td>
    </tr>

    <tr>
    <td class="no-border px-2" style="margin-top: -20px;">
      </td>
      <td class="no-border px-2">
      <p class="m-0" style="border-bottom: 1px solid">${dataPrint[0].FullName}</p>
      <p class="m-0">${tglOto}</p>
      </td>
      <td class="no-border px-2">
      </td>
      <td class="no-border px-2">
      <p class="m-0" style="border-bottom: 1px solid">${userLogin}</p>
      <p class="m-0">${tglCetak}</p>
      </td>
      <td class="no-border px-2">
      </td>
    </tr>
  </table>
</div>`

if(i == arrayDataPrint.length - 1){        
 tempPrintStr += `
<div style="width: 62%; font-family: sans-serif; font-size: 10px;">

  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 30%; text-align:left;"> SUB TOTAL </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Brutto).toFixed(2))}</div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 30%; text-align:left;"> UANG MUKA ${parseInt(dataPrint[0].persen)}%</div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].NuangMuka).toFixed(2))}</div>
  </div>
  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 2px;">
    <div style="width: 30%; text-align:left;">PPN ${dataPrint[0].nilaippn}</div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].PPN).toFixed(2))}</div>
  </div>

  <div style="display: flex; font-size:10px; justify-content: flex-end; width: 91%; padding-bottom: 6px; position: relative;">
    <div style="width: 30%; text-align:left;"> TOTAL </div>
    <div style="
      position: absolute;
      right: 0;
      bottom: 3px;
      width: 50%;
      border-bottom: 1px solid #000;">
    </div>

    <!-- garis bawah 2 -->
    <div style="
      position: absolute;
      right: 0;
      bottom: 0;
      width: 50%;
      border-bottom: 1px solid #000;">
    </div>
    <div style="width: 20%; text-align: right">${formatAngka(parseFloat(dataPrint[0].subtotalum).toFixed(2))}</div>
  </div>

</div>`};

tempPrintStr += `
  </div>

  <div class="footer-print-date">
    <table class="m-0" style="width: 100% ; font-family: sans-serif;
    font-size: 10px">
      <tr>
        <td class="no-border">${i+1}/${arrayDataPrint.length}        `+userLogin+`          `+tanggalOnly+`      `+jamCetak+`</td>
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

  function formatAngkaCetak (angkaString) {
  angkaString = parseFloat(angkaString).toFixed(2)
  // console.log('formatAngka' , angkaString);
  let tempAngka = angkaString.split('.')
  let temp1 = ''

  if (Number(tempAngka[1]) > 50) {
    tempAngka[0] = Number(tempAngka[0]) + 1
  }


  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  return temp1
}



</script>

@endsection
