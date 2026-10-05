@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Pelunasan Piutang DPP')

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

  #formBsGrid .input-group .btn:has(> .bi-search) {
    height: auto !important;
    align-self: stretch;
  }
</style>

{{-- Header tabel interaktif (geser kolom + roda gigi sembunyikan kolom + bar kolom
     tersembunyi + modal filter) - sama seperti menu purchasing / penerimaandpp. --}}
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
/* Jarak kartu ke bar atas. Layout memberi .content padding 28px; halaman purchasing
   memakai 12px, dan halaman ini mengikutinya supaya seragam. */
#content { padding-top: 12px; }

/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}

/* Desain tab disamakan dengan menu purchasing (purchaseOrder.blade.php): pill group
   di latar abu, tab aktif biru solid dengan shadow, murni lewat class - bukan inline style. */
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
</style>

<style>
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
  border-color: var(--border, #e5e7eb) !important;
}

/* ---------- Gaya dasar tabel daftar ---------- */
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.data-table thead th {
  background: #f9fafb;
  padding: 11px 16px;
  text-align: left;
  font-weight: 600;
  font-size: 12px;
  color: var(--text-muted, #6b7280);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid var(--border, #e5e7eb);
}

.data-table tbody td {
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  color: var(--text-main, #1f2937);
}

.data-table tbody tr:last-child td { border-bottom: none; }
.data-table tbody tr:hover td { background: #f9fafb; }

/* DataTables selalu menulis hasil pengukurannya sebagai inline style pada <table>,
   yang mengalahkan `.data-table { width: 100% }`. Dipakai min-width, BUKAN width. */
#tabel2, #tabel { min-width: 100%; }

/* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
#tabel2 td:first-child:not([colspan]),
#tabel td:first-child:not([colspan]) { vertical-align: middle; }

#tabel2 td:first-child .po-aksi-wrap,
#tabel td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabel2 td:first-child .btn,
#tabel td:first-child .btn {
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

#tabel2 td:first-child .btn:hover,
#tabel td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabel2 td:first-child .btn-success, #tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabel2 td:first-child .btn-warning, #tabel td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabel2 td:first-child .btn-primary, #tabel td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabel2 td:first-child .btn-danger,  #tabel td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabel2 td:first-child .btn-info,    #tabel td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

#addInvoiceTable td .btn, #addTable td .btn {
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
#addInvoiceTable td .btn:hover, #addTable td .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}
#addInvoiceTable td .btn-success, #addTable td .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#addInvoiceTable td .btn-danger, #addTable td .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

/* Tombol di kolom Aksi baru muncul saat barisnya di-hover. */
table.data-table.po-aksi-hover tbody td:first-child .btn {
  visibility: hidden;
  opacity: 0;
  transition: opacity .12s ease;
}
table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
  visibility: visible;
  opacity: 1;
}

/* ---------- Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar ---------- */
.po-len-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--rt-card, #fff);
  border: 1.5px solid var(--rt-border, #E7E8F0);
  border-radius: 8px;
  padding: 5px 12px;
}

.po-len-wrap label {
  margin: 0;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--rt-ink-soft, #6B7180);
  text-transform: uppercase;
  letter-spacing: .05em;
  white-space: nowrap;
}

.po-len-inp {
  border: none;
  background: transparent;
  font-size: 13px;
  font-weight: 700;
  color: var(--rt-ink, #1D2130);
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

/* ---------- Tombol utama halaman ---------- */
.btn-dpp-utama {
  height: 36px;
  border-radius: 8px;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0 16px;
  background-color: #e8edff;
  border: 1px solid #cfdcff;
  color: #2563eb;
  box-shadow: none;
}
.btn-dpp-utama:hover,
.btn-dpp-utama:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-dpp-utama:active { background-color: #cfdcff !important; border-color: #a8bdff !important; color: #1d4ed8 !important; }

.btn-dpp-tutup {
  height: 30px;
  border-radius: 20px !important;
  font-size: 0.75rem !important;
  font-weight: 600;
  padding: 4px 12px !important;
  text-transform: uppercase;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.btn-dpp-tutup:hover,
.btn-dpp-tutup:focus { background-color: #bb2d3b; border-color: #b02a37; color: #fff; }
.btn-dpp-tutup:active { background-color: #b02a37 !important; border-color: #a52834 !important; color: #fff !important; }

.btn-chip-biru {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
}
.btn-chip-biru:hover,
.btn-chip-biru:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-chip-biru:active { background-color: #cfdcff !important; border-color: #a8bdff !important; color: #1d4ed8 !important; }

.btn-batal-add {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
}
.btn-batal-add:hover,
.btn-batal-add:focus { background-color: #e9ecef; border-color: #ced4da; color: #343a40; }
.btn-batal-add:active { background-color: #dee2e6 !important; border-color: #ced4da !important; color: #343a40 !important; }

/* ---------- Tombol browsing (kaca pembesar) di sebelah input ---------- */
.btn-browsing {
  height: 38px;
  width: 40px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 0 6px 6px 0;
  background-color: #e8edff;
  border: 1px solid #cfdcff;
  color: #2563eb;
  font-size: 15px;
  box-shadow: none;
}
.btn-browsing:hover,
.btn-browsing:focus { background-color: #dce6ff; border-color: #b9c9ff; color: #1d4ed8; }
.btn-browsing:disabled { background-color: #f1f3f5; border-color: #dee2e6; color: #adb5bd; }

/* Varian kecil untuk tombol browsing di dalam baris tabel (input tinggi 30px). */
.btn-browsing-sm { height: 30px; width: 32px; font-size: 13px; border-radius: 0 !important; }

/* Kotak cari di modal Proses Terima DPP - sama dengan #input_search_pengajuan_dpp (pengajuandpp). */
#input_search_proses_dpp {
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
#input_search_proses_dpp:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* Tombol Save di modal Change Invoice - hijau lembut, bukan hijau bootstrap terang. */
#buttonSaveLB {
  background-color: #e7f7ed;
  border-color: #cdebd7;
  color: #16a34a;
  box-shadow: none;
}
#buttonSaveLB:hover,
#buttonSaveLB:focus { background-color: #d7f0e1; border-color: #b6e0c6; color: #15803d; }

/* ---------- Tabel Invoice/Giro/Rekap di page2, dan #addTable (mengikuti #addTable pengajuandpp) ---------- */
#addInvoiceTable thead th,
#addGiroTable thead th,
#addRekapTable thead th,
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

#addInvoiceTable tbody tr:nth-of-type(odd),
#addGiroTable tbody tr:nth-of-type(odd),
#addRekapTable tbody tr:nth-of-type(odd),
#addTable tbody tr:nth-of-type(odd),
#detailTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#addInvoiceTable tbody tr:hover,
#addGiroTable tbody tr:hover,
#addRekapTable tbody tr:hover,
#addTable tbody tr:hover,
#detailTable tbody tr:hover { background-color: #f5f3ff; }

/* ---------- Tabel di dalam modal - baris diklik langsung ---------- */
.tabel-modal-pdpp thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee !important;
  border-top: none !important;
}

.tabel-modal-pdpp tbody td {
  border-top: none !important;
  border-bottom: 1px solid #f1f3f5 !important;
  font-size: 13px;
  vertical-align: middle;
}

.tabel-modal-pdpp tbody tr.pick-row {
  cursor: pointer;
  transition: background-color .12s;
}
.tabel-modal-pdpp tbody tr.pick-row:hover td { background-color: #eef2ff; }

/* Kolom checkbox di modal Proses / Terima DPP: .form-check bawaan Bootstrap 5
   memberi padding-left pada wadah dan float:left pada inputnya, jadi text-center
   saja tidak cukup - floatnya harus dimatikan. */
#prosesModalTable tbody td .form-check,
#dppModalTable tbody td .form-check {
  padding-left: 0;
  margin: 0;
}
#prosesModalTable tbody td .form-check-input,
#dppModalTable tbody td .form-check-input {
  float: none;
  margin: 0;
  vertical-align: middle;
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

/* ---------- Kotak cari di dalam modal ---------- */
.cari-modal-pdpp {
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
.cari-modal-pdpp:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* Modal filter (.rt-filter) memakai tombol close Bootstrap bawaan. */
.rt-filter .modal-header .close {
  opacity: .5;
  text-shadow: 0 1px 0 #fff;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #000;
  background: transparent;
  border: 0;
  padding: 1rem;
  margin: -1rem -1rem -1rem auto;
}
.rt-filter .modal-header .close:hover { opacity: .75; }

/* ---------- Tombol aksi di dalam form (page2/page3) dan modal ----------
   Sebagian tombol lama membawa inline style yang hanya mengatur UKURAN
   (height/border-radius/font). Warnanya tidak inline, jadi cukup ditimpa di sini;
   border-radius perlu !important karena inline style menang atas CSS biasa.
   Tombol ikon di kolom Aksi tabel daftar ada di #page1 sehingga tidak terkena. */
#page2 .btn-primary,
#page3 .btn-primary,
.modal-body .btn-primary,
.modal-footer .btn-primary {
  background-color: #e8edff;
  border-color: #cfdcff;
  color: #2563eb;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

#page2 .btn-primary:hover,
#page3 .btn-primary:hover,
.modal-body .btn-primary:hover,
.modal-footer .btn-primary:hover {
  background-color: #dce6ff;
  border-color: #b9c9ff;
  color: #1d4ed8;
}

#page2 .btn-secondary,
#page3 .btn-secondary,
.modal-body .btn-secondary,
.modal-footer .btn-secondary {
  background-color: #f1f3f5;
  border-color: #dee2e6;
  color: #495057;
  border-radius: 8px !important;
  text-transform: none !important;
  box-shadow: none;
}

#page2 .btn-secondary:hover,
#page3 .btn-secondary:hover,
.modal-body .btn-secondary:hover,
.modal-footer .btn-secondary:hover {
  background-color: #e9ecef;
  border-color: #ced4da;
  color: #343a40;
}

/* Tombol browsing tetap menyatu dengan input di sebelah kirinya - tinggi disamakan
   secara eksplisit (bukan mengandalkan flex stretch) supaya tidak lebih besar dari
   input di sampingnya. */
#input_modalx_perkiraanlebihbayar,
#input_modalx_namaperkiraanlebihbayar,
#input_modalx_perkiraankurangbayar,
#input_modalx_namaperkiraankurangbayar,
#buttonAddListPerkiraanLebihBayar,
#buttonAddListPerkiraanKurangBayar {
  height: 36px;
}
#buttonAddListPerkiraanLebihBayar,
#buttonAddListPerkiraanKurangBayar {
  border-radius: 0 6px 6px 0 !important;
  width: 36px;
  padding: 0;
  font-size: 14px;
}

/* ---------- Tumpukan modal: hanya modal teratas yang terlihat ---------- */
.modal.pld-modal-tertimbun { display: none !important; }
.modal-backdrop.pld-backdrop-tertimbun { display: none !important; }

/* ---------- Indikator loading - disalin dari closingPurchaseOrder.blade.php ---------- */
.pld-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, .62);
  z-index: 40;
  animation: pldMunculLoading .34s ease-out both;
}

@keyframes pldMunculLoading {
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
  animation: pldPutarLoading .6s linear infinite;
}

@keyframes pldPutarLoading {
  to { transform: rotate(360deg); }
}

/* Form koreksi customer - layout label/kolom sejajar seperti modal di Bon Sementara. */
#formKoreksiCustomer .kc-form {
  display: grid;
  grid-template-columns: 120px minmax(0, 1fr);
  gap: 14px 12px;
  align-items: center;
}

#formKoreksiCustomer .kc-form label {
  margin-bottom: 0;
  text-align: left;
  font-size: 11.5px;
  letter-spacing: .04em;
  color: #6b7280;
  white-space: nowrap;
  cursor: default;
}

#formKoreksiCustomer .kc-form .form-control {
  height: 38px;
  border-radius: 8px;
  font-size: 13.5px;
  box-shadow: none;
}

/* Kolom nama customer + tombol kaca pembesar di sampingnya (pola halaman purchasing). */
#formKoreksiCustomer .kc-form .input-group { margin-bottom: 0; }
#formKoreksiCustomer .kc-form .input-group .form-control {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

/* Modal daftar customer dibuka di atas modal koreksi - dinaikkan supaya tidak tertutup. */
#formKoreksiCustomerList { z-index: 1061; }
.modal-backdrop.kc-backdrop-atas { z-index: 1060; }
</style>
@endsection


@section('content')

{{-- Logo untuk cetakan - dipindah ke @section('content'), sama seperti penerimaandpp. --}}
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >
  {{-- Judul pindah ke bar atas lewat @section('page-title') - sama seperti penerimaandpp. --}}
</div>

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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">
          Outstanding Pembayaran
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
          Pelunasan Piutang
        </a>
      </div>
    </div>
  </div>
<div class="card">
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    {{-- Tab Outstanding: hanya periode + cari + Tampilkan, tanpa tombol Filter (tidak
         ada kolom otorisasi di tab ini). --}}
    <div class="po-toolbar">
      <div class="po-filter-wrap">
        <label>Sampai Tanggal</label>
        <input type="date" class="po-filter-inp" id="outTglAkhir" value="{!! $outTglAkhir !!}">
      </div>
      <input type="search" id="outSearch" class="po-search-inp" placeholder="Cari data">
      <div class="po-len-wrap">
        <label for="outLen">Tampilkan</label>
        <select id="outLen" class="po-len-inp">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Semua</option>
        </select>
      </div>
    </div>

    <div id="rtBarOut"></div>

    <div id="outTableWrap" style="position: relative;">
    <div id="outLoading" class="pld-loading" hidden>
      <span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>
    </div>
    <table id="tabel" class="data-table po-aksi-hover">
      <thead id="tabel_header_out" class="text-center">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Actions</th>
          <th style="padding: 4px 12px;" scope="col">No Bukti</th>
          <th style="padding: 4px 12px;" scope="col">Customer</th>
          <th style="padding: 4px 12px;" scope="col">Tanggal</th>
          <th style="padding: 4px 12px;" scope="col">Penagih</th>
          <th style="padding: 4px 12px;" scope="col">Jumlah$</th>
          <th style="padding: 4px 12px;" scope="col">JumlahRp</th>
          <th style="padding: 4px 12px;" scope="col">LB</th>
          <th style="padding: 4px 12px;" scope="col">Dibayar</th>
          <th style="padding: 4px 12px;" scope="col">Sisa</th>
          <th style="padding: 4px 12px;" scope="col">Keterangan</th>
        </tr>
      </thead>

      <tbody id="tabel_data" class="text-left">
        {{-- Baris + judul kolom digambar renderTabelOutstanding() lewat JS dari data
             dan konfigurasi kolom yang dikirim loadAll(). --}}
      </tbody>
    </table>
    </div>
  </div>

  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

    <div class="po-toolbar">
      <div class="po-filter-wrap">
        <label>Periode</label>
        <input type="date" class="po-filter-inp" id="pldTglAwal" value="{!! $pldTglAwal !!}">
        <span class="po-filter-sep">s/d</span>
        <input type="date" class="po-filter-inp" id="pldTglAkhir" value="{!! $pldTglAkhir !!}">
      </div>
      <input type="search" id="pldSearch" class="po-search-inp" placeholder="Cari data">
      <div class="po-len-wrap">
        <label for="pldLen">Tampilkan</label>
        <select id="pldLen" class="po-len-inp">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Semua</option>
        </select>
      </div>
      <button class="po-btn-filter" type="button" id="pldBtnFilter" onclick="$('#modalFilterPld').modal('show')">
        <i class="bi bi-funnel"></i> Filter
      </button>
    </div>

    <div id="rtBar"></div>

    <div id="pldTableWrap" style="position: relative;">
    <div id="pldLoading" class="pld-loading" hidden>
      <span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>
    </div>
    <table id="tabel2" class="data-table po-aksi-hover">
      <thead id="tabel_header_pld" class="text-center">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Actions</th>
          <th style="padding: 4px 12px;" scope="col">No Bukti</th>
          <th style="padding: 4px 12px;" scope="col">Tanggal</th>
          <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
          <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
          <th style="padding: 4px 12px;" scope="col">No DPP</th>
          <th style="padding: 4px 12px;" scope="col">Dibayar</th>
          <th style="padding: 4px 12px;" scope="col">Lebih Bayar</th>
          <th style="padding: 4px 12px;" scope="col">Kurang Bayar</th>
        </tr>
      </thead>
      <tbody id="tabel2_data" class="text-left">
        {{-- Baris digambar renderTabelPld() lewat JS. --}}
      </tbody>
    </table>
    </div>

  </div>

</div>
</div>
</div>


</div>
</div>

<!-- modal filter otorisasi tab Pelunasan Piutang -->
<div class="modal fade rt-filter" id="modalFilterPld">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Pelunasan Piutang
          <span class="rt-active-badge" id="pldFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPld').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="pldModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="pldModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="pldResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterPld').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="pldTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter otorisasi -->

<div id="page2" style="display: none" class="mainpage container-fluid" >
<div id="formBsGrid">

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-dpp-tutup" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div id= "formAdd" class="">



  <div id="" class="">
  <div class="">
    <!-- <h1>Tes Modal</h1> -->

    <div class="container-fluid">
      <input type="hidden" name="noUrut" id="input_add_nourut" value="" />

      <div class="row">
        <div class="col-md-3">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>No Bukti</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
              <input type="hidden" class="form-control" id="input_add_nourut" placeholder="" disabled>
            <input type="text" class="form-control" id="input_add_nobukti" placeholder="No Bukti" disabled>
          </div>
        </div>
      </div>

        </div>
        <div class="col-md-3">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>Tanggal</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_add_tanggal" placeholder="" disabled>
          </div>
        </div>
      </div>

        </div>

      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="row" style="margin-top: -10px">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>BKM/BBM</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_add_nobkmbbm" type="text" class="form-control" disabled>


                  </div>
                </div>

              </div>
                <div class="row" style="margin-top: -10px">



                <div class="col-md-4">
                  <div class="form-group">
                  <label>Valas</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_add_valas" type="text" class="form-control" disabled>

                    <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                  </div>
                </div>
                </div>

            </div>

            <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                      <label>Customer</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_add_kodecust" type="text" class="form-control" disabled>


                      </div>
                    </div>

            </div>

            <div class="row" style="margin-top: -10px">
              <div class="col-md-4">
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_add_namacust" type="text" class="form-control" disabled>


                </div>
              </div>

      </div>



          </div>


        </div>

      </div>


      </div>

      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Jumlah</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_add_jumlah" type="text" inputmode="decimal" class="form-control text-right" disabled>


                  </div>
                </div>

        </div>
      </div>

      <div class="col-md-3">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                <label>Dibayar</label>
              </div>
              </div>
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_add_dibayar" type="text" inputmode="decimal" class="form-control text-right" disabled>


                </div>
              </div>

      </div>
    </div>



    <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Sisa</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_sisa" type="text" inputmode="decimal" class="form-control text-right" disabled>


              </div>
            </div>

    </div>
  </div>
</div>




<div class="container-fluid">
  <hr/>

</div>

<div class="col-md-12 mt-2 text-right">
<button id="buttonAddItem" type="button" class="btn btn-chip-biru" onclick="buttonAddItem()">Tambah</button>
</div>


  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="data-table"  >
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kas/Bank</th>
              <th style="padding: 4px 12px;" scope="col">Faktur</th>
              <th style="padding: 4px 12px;" scope="col">diBayar</th>
              <th style="padding: 4px 12px;" scope="col">lebihBayar</th>
              <th style="padding: 4px 12px;" scope="col">kurangBayar</th>
              <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
              <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
              <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=9 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>





  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4 id="labelAddEditItem">Edit Item</h4>
      </div>
    </div>


    <div class="row">
      <div class="col-md-3">
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
          <label>Faktur</label>
        </div>
        </div>
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddFaktur" type="text" class="form-control" disabled>
          </div>
        </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
          <label>Dibayar</label>
        </div>
        </div>
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddDibayar" type="text" inputmode="decimal" class="form-control text-right format-number" disabled>
          </div>
        </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
          <label>Lebih Bayar</label>
        </div>
        </div>
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddLebihBayar" type="text" inputmode="decimal" class="form-control text-right format-number" disabled>
          </div>
        </div>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: -10px">
      <div class="col-md-3">
        <div class="row">
        <div class="col-md-4">
          <div class="form-group">
          <label>Kurang Bayar</label>
        </div>
        </div>
        <div class="col-md-8">
          <div class="input-group form-group">
            <input id="AddAddKurangBayar" type="text" inputmode="decimal" class="form-control text-right format-number" disabled>
          </div>
        </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="row">
        <div class="col-md-2">
          <div class="form-group">
          <label>Perkiraan</label>
        </div>
        </div>
        <div class="col-md-4">
          <div class="input-group form-group">
            <input id="AddAddKodePerkiraan" type="text" class="form-control" disabled>
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group form-group">
            <input type="text" class="form-control" id="AddAddNamaPerkiraan" disabled>
          </div>
        </div>
        </div>
      </div>
    </div>



</div>

<div class="row mt-2" style="margin-top: 0">
  <div class="col-md-12 text-right mt-4">
    <button type="button" class="btn btn-batal-add" onclick="buttonAddBatal()">Batal</button>

    <button id="buttonSubmitEdit" type="button" onclick="submitEdit()" class="btn btn-chip-biru">Submit Edit</button>

  </div>

</div>




</div>



</div>




</div>








    <!-- <div class="line"></div> -->
    <!-- <hr/> -->
  </div>
</div>
<!-- </div> -->


<!-- ADD EDIT -->


<!-- </div> -->



    </div>{{-- /#formBsGrid --}}
    </div>

    <!-- <div class="row "> -->

<!-- </div> -->









  <!-- </div> -->

  <!-- <h2 class="page3showhide detailshowhide"> Detail Pengajuan DPH</h2>
  <h2 class="page3showhide otorisasishowhide"> Otorisasi Pengajuan DPH</h2> -->



    <!-- <div class="col-6 text-right">
      <button type="button" class="page3showhide otorisasishowhide btn btn-dpp-utama" onclick="submitOtorisasi()">Otorisasi</button>
    </div> -->


    <div id="page3" style="display: none" class="mainpage container-fluid" >
    <div id="formBsGrid">

      <div class="row">
        <div class="col-8 text-left">
          <h2 class="page3showhide detailshowhide"></h2>
          <h2 class="page3showhide otorisasishowhide"></h2>
        </div>
        <div class="col-4 text-right">
          <button type="button" class="btn btn-dpp-tutup" onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

      <div id= "" class="">



      <div id="" class="">
      <div class="">
        <!-- <h1>Tes Modal</h1> -->

        <div class="container-fluid">

          <div class="row">
            <div class="col-md-3">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                  <input type="hidden" class="form-control" id="input_detail_nourut" placeholder="" disabled>
                <input type="text" class="form-control" id="input_detail_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

            </div>
            <div class="col-md-3">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_detail_tanggal" placeholder="" disabled>
              </div>
            </div>
          </div>

            </div>

          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="row" style="margin-top: -10px">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                      <label>BKM/BBM</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_detail_nobkmbbm" type="text" class="form-control" disabled>


                      </div>
                    </div>

                  </div>
                    <div class="row" style="margin-top: -10px">



                    <div class="col-md-4">
                      <div class="form-group">
                      <label>Valas</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_detail_valas" type="text" class="form-control" disabled>

                        <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

                      </div>
                    </div>
                    </div>

                </div>

                <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                          <label>Customer</label>
                        </div>
                        </div>
                        <!-- <div class="col-4 text-right">

                          </div> -->
                        <div class="col-md-8">
                          <div class="input-group form-group">
                            <input id="input_detail_kodecust" type="text" class="form-control" disabled>


                          </div>
                        </div>

                </div>

                <div class="row" style="margin-top: -10px">
                  <div class="col-md-4">
                  </div>
                  <!-- <div class="col-4 text-right">

                    </div> -->
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="input_detail_namacust" type="text" class="form-control" disabled>


                    </div>
                  </div>

          </div>



              </div>


            </div>

          </div>


          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                      <label>Jumlah</label>
                    </div>
                    </div>
                    <!-- <div class="col-4 text-right">

                      </div> -->
                    <div class="col-md-8">
                      <div class="input-group form-group">
                        <input id="input_detail_jumlah" type="text" inputmode="decimal" class="form-control text-right" disabled>


                      </div>
                    </div>

            </div>
          </div>

          <div class="col-md-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                    <label>Dibayar</label>
                  </div>
                  </div>
                  <!-- <div class="col-4 text-right">

                    </div> -->
                  <div class="col-md-8">
                    <div class="input-group form-group">
                      <input id="input_detail_dibayar" type="text" inputmode="decimal" class="form-control text-right" disabled>


                    </div>
                  </div>

          </div>
        </div>



        <div class="col-md-3">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Sisa</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_detail_sisa" type="text" inputmode="decimal" class="form-control text-right" disabled>


                  </div>
                </div>

        </div>
      </div>
    </div>




    <div class="container-fluid">
      <hr/>

    </div>



      <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

            <table id="detailTable" class="data-table">
              <thead class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kas/Bank</th>
                  <th style="padding: 4px 12px;" scope="col">Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">diBayar</th>
                  <th style="padding: 4px 12px;" scope="col">lebihBayar</th>
                  <th style="padding: 4px 12px;" scope="col">kurangBayar</th>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Cust</th>

                </tr>
              </thead>


              <tbody id="detailTableData" class="" >
                <tr >

                    <td colspan=8 class="text-center">Belum ada data</td>

              </tr>

              </tbody>


            </table>
      </div>

      <div class="container-fluid">
        <div class="row">

                <div class="col-12 text-right">
                  <button type="button" class="page3showhide otorisasishowhide btn btn-dpp-utama" onclick="submitOtorisasi()">Otorisasi</button>
                </div>
        </div>

      </div>










    </div>




    </div>








        <!-- <div class="line"></div> -->
        <!-- <hr/> -->
      </div>
    </div>
    <!-- </div> -->


    <!-- ADD EDIT -->


    <!-- </div> -->



        </div>{{-- /#formBsGrid --}}
        </div>






<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Proses Terima DPP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >

          <div class="row">
            <div class="col-md-4">
              <div class="row" >
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Jumlah</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" inputmode="decimal" class="form-control text-right" id="input_modal_jumlah" disabled>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: -10px">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Dibayar</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" inputmode="decimal" class="form-control text-right" id="input_modal_dibayar" disabled>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top: -10px">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sisa</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" inputmode="decimal" class="form-control text-right" id="input_modal_sisa" disabled>
                  </div>
                </div>
              </div>

            </div>


            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>No Bukti</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_modal_nobukti" placeholder="" disabled>
                  </div>
                </div>
              </div>


              <div class="row" style="margin-top: -10px">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Nama Cust</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" id="input_modal_namacust" placeholder="" disabled>
                  </div>
                </div>

              </div>

            </div>

          </div>




          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

          {{-- Kotak pencarian tabel Proses Terima DPP - diikat lewat pldIkatSearchProsesDpp().
               Tampilan disamakan dengan #input_search_pengajuan_dpp di pengajuandpp. --}}
          <div class="row mb-2">
            <div class="col-12 d-flex justify-content-end" style="padding-right: 0px;">
              <input id="input_search_proses_dpp" type="search" class="form-control" placeholder="Cari data">
            </div>
          </div>

          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modal" class="data-table" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">v</th>
                  <th style="padding: 4px 12px;" scope="col">No Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Customer</th>
                  <th style="padding: 4px 12px;" scope="col">N. Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">SdhBayar</th>
                  <th style="padding: 4px 12px;" scope="col">Dibayar</th>
                  <th style="padding: 4px 12px;" scope="col">L.Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">K.Bayar</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modal" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>



              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
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
  </div>

<!-- End modal add-->





<div class="modal fade" id="formX" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Change Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >

          <div class="row">
            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Nilai Nota</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" inputmode="decimal" class="form-control text-right" id="input_modalx_nilainotadibayar" disabled>
                  </div>
                </div>

              </div>

            </div>

            <div class="col-md-4">
              <div class="row">


              <div class="col-md-4">
                <div class="form-group">
                  <label>Sisa Nota</label>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <input type="text" inputmode="decimal" class="form-control text-right" id="input_modalx_sisanotadibayar" disabled>
                </div>
              </div>

            </div>
            </div>

          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-4">
          <div class="row" >
            <div class="col-md-4">
              <div class="form-group">
                <label>Dibayar</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_modalx_dibayar" >
              </div>
            </div>
          </div>
          <div class="row" style="margin-top: -10px">
            <div class="col-md-4">
              <div class="form-group">
                <label>Lebih Bayar</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="text" inputmode="decimal" class="form-control text-right format-number" id="input_modalx_lebihbayar" >
              </div>
            </div>
          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-4">
              <div class="form-group">
                <label>Perk LB</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group input-group">
                <input type="text" class="form-control text-right" id="input_modalx_perkiraanlebihbayar" disabled>

                <input type="text" class="form-control" id="input_modalx_namaperkiraanlebihbayar" disabled>
                <button id="buttonAddListPerkiraanLebihBayar" type="button" onclick="buttonAddListPerkiraanLebihBayar('lebihbayar')" class="btn btn-browsing" title="Cari Perkiraan Lebih Bayar"><i class="bi bi-search"></i></button>

              </div>
            </div>
          </div>


            </div>

          </div>


          <div class="row mt-2" style="margin-top: 0">
            <div class="col-md-12 text-right mt-4">

              <button id="buttonSaveLB" type="button" onclick="buttonSaveLB()" class="btn btn-success" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">Save</button>
              <button type="button" id="buttonAddKL" class="btn btn-chip-biru" onclick="buttonAddKL()">KL</button>





              <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
            </div>

          </div>



          </div>
          <div id="formAddKL" class="container-fluid showhideitemKL">
            <!-- <div class="line"></div> -->
            <!-- <div class="row"> -->

            <div class="col-12">


            <hr/>
            <div class="row">
              <div class="col-md-12">
                <h4 id="">Add KL</h4>
              </div>
            </div>


            <div class="row" >
              <div class="col-md-6">


                <div class="row">






                  <div class="col-md-2">
                    <div class="form-group">
                    <label>Jumlah</label>
                  </div>
                  </div>
                  <!-- <div class="col-4 text-right">

                    </div> -->
                  <div class="col-md-4">
                    <div class="input-group form-group">
                      <input id="input_modalx_kurangbayar" type="text" inputmode="decimal" value="0.00" class="text-right form-control format-number" >

                    </div>
                  </div>



                </div>
              </div>

            </div>

            <div class="row" style="margin-top: -10px">
              <div class="col-md-6">


                <div class="row">






                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Perk KL</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group input-group">
                      <input type="text" class="form-control" id="input_modalx_perkiraankurangbayar" disabled>
                      <input type="text" class="form-control" id="input_modalx_namaperkiraankurangbayar" disabled>
                      <button id="buttonAddListPerkiraanKurangBayar" type="button" onclick="buttonAddListPerkiraanLebihBayar('kurangbayar')" class="btn btn-browsing" title="Cari Perkiraan Kurang Bayar"><i class="bi bi-search"></i></button>

                    </div>
                  </div>





                </div>
              </div>

            </div>















        </div>



          <div class="row mt-2" style="margin-top: 0">
            <div class="col-md-12 text-right mt-4">
              <button type="button" class="btn btn-secondary" onclick="buttonAddBatalKL()" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">Batal</button>

              <button id="buttonSubmitAddKL" type="button" onclick="submitAddKL()" class="btn btn-primary" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;">Submit Add</button>




              <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
            </div>

          </div>

        </div>



          <div class="row" style="margin-top:20px">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            <table id="tabel_add_list_modalx" class="data-table" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kurang Bayar</th>
                  <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                  <th style="padding: 4px 12px;" scope="col">Nama perkiraan</th>

                </tr>
              </thead>


              <tbody id="tabel_data_add_list_modalx" class="text-left" >

                <tr >

                  <td>-</td>
                  <td>-</td>
                  <td>-</td>



              </tr>
              </tbody>


            </table>
          <!-- </div> -->
            <!-- <button onclick="buttonSubKategori()">tes</button> -->
          </div>
            </div>








          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            </div>




        </div>





      </div>


      <div class="modal-footer">



        <button type="button" class="btn btn-secondary" data-dismiss="modal"
        >Batal</button>
        <!-- <button type="button" class="btn btn-primary" onclick="submitAddModalX()">Submit</button> -->
      </div>
      </div>

      </div>

    </div>




    {{-- Disamakan persis dengan modal Perkiraan menu Kas (picker-kas): modal-xl, dropdown
         "Tampilkan" + kotak Search bawaan DataTables (kotak cari lama #input_search_perkiraanmodal
         dilepas), tombol Batal gaya pil. --}}
    <div class="modal fade picker-kas" id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="">
        <div id="" class="modal-content ">

          <div id= "" class="">
          <div class="modal-header">


              <h5 class="modal-title" id="">Perkiraan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div id="" class="">
          <div class="modal-body">

            <div class="container-fluid mt-4" >
              <div class="row">
                <div class="col-12" style="overflow:auto; margin-top:0px; ">
                <!-- <div class="container-fluid"> -->


                <table id="tabel_add_list_perkiraan">
                  <thead>
                    <tr>
                      <th scope="col">Perkiraan</th>
                      <th scope="col">Nama</th>
                    </tr>
                  </thead>


                  <tbody id="tabel_data_add_list_perkiraan" class="text-left" >

                    @for ($i = 0; $i < count($tempListPerkiraan); $i++)
                    <tr class="pick-row" onclick="buttonAddPickPerkiraanLebihBayar('{{ $tempListPerkiraan[$i]->Perkiraan }}' , '{{ $tempListPerkiraan[$i]->Keterangan }}')">
                      <td>{{ $tempListPerkiraan[$i]->Perkiraan }}</td>
                      <td>{{ $tempListPerkiraan[$i]->Keterangan }}</td>
                  </tr>
                  @endfor
                  </tbody>


                </table>
              <!-- </div> -->
                <!-- <button onclick="buttonSubKategori()">tes</button> -->
              </div>
                </div>
                </div>

            </div>

          </div>


          <div class="modal-footer">
            <button type="button" class="btn picker-kas-batal" data-dismiss="modal" >Batal</button>
            <button type="button" id="buttonSubmitAdd" class="btn btn-chip-biru d-none" onclick="submitAdd()">Submit</button>
          </div>
          </div>




          </div>







        </div>
      </div>


<!-- Modal koreksi customer baris outstanding (dari NO NAME ke customer lain). -->
<div class="modal fade" id="formKoreksiCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Koreksi Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body">
        <div class="kc-form">
          <label for="input_koreksicust_nobukti">No Bukti</label>
          <input type="text" class="form-control" id="input_koreksicust_nobukti" disabled>

          <label for="input_koreksicust_customerlama">Customer Lama</label>
          <input type="text" class="form-control" id="input_koreksicust_customerlama" disabled>

          <label for="input_koreksicust_kode">Kode Customer</label>
          <input type="text" class="form-control" id="input_koreksicust_kode" disabled>

          <label for="input_koreksicust_nama">Nama Customer</label>
          <div class="input-group">
            <input type="text" class="form-control" id="input_koreksicust_nama" disabled>
            <button type="button" id="buttonAddListKoreksiCustomer" onclick="buttonAddListKoreksiCustomer()" class="btn btn-chip-biru btn-sm" style="height:38px; border-radius:0 8px 8px 0;"><i class="bi bi-search"></i></button>
          </div>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" id="buttonSubmitKoreksiCustomer" class="btn btn-primary" onclick="submitKoreksiCustomer()">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal daftar customer untuk browse dari modal Koreksi Customer. -->
{{-- Disamakan persis dengan modal pemilih menu Kas (picker-kas); kotak cari lama
     (#input_search_koreksicustomer) diganti kotak Search bawaan DataTables. --}}
<div class="modal fade picker-kas" id="formKoreksiCustomerList" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid mt-4">
          <div class="row">
            <div class="col-12" style="overflow:auto; margin-top:0px; ">
              <table id="tabel_add_list_koreksicustomer">
                <thead>
                  <tr>
                    <th scope="col">Kode</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Alamat</th>
                  </tr>
                </thead>
                <tbody id="tabel_data_add_list_koreksicustomer" class="text-left"></tbody>
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





  </div>









@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">
let listData = []
let listOutstanding = []
let tipeform = ''
let listProsesTerimaDPP = []
let penerimaanHeader = {}
let listPenerimaan = []
let listTambah = []
let listTambahLB = []
let listTambahKL = []
let saveHeaderInvoice = {}
let saveHeaderIndex = 0
let toId = ''
let urutTrans = 0
let dataEdit = {}

// ---------- Tumpukan modal ----------
// Hanya satu modal yang terlihat pada satu waktu. Saat modal anak dibuka, modal
// induk disembunyikan lewat class (bukan .modal('hide'), supaya isian form dan
// handler hidden.bs.modal milik induk tidak ikut terpicu). Saat anak ditutup -
// lewat Batal, tombol x, Esc, maupun klik backdrop - induk muncul lagi.
// Yang disimpan di tumpukan adalah elemennya, bukan id-nya, supaya tetap aman
// seandainya suatu saat ada id modal yang kembar.
var pldTumpukanModal = []

function pldSisakanSatuBackdrop () {
  var backdrop = $('.modal-backdrop')
  backdrop.addClass('pld-backdrop-tertimbun')
  backdrop.last().removeClass('pld-backdrop-tertimbun')
}

$(document).on('show.bs.modal', '.modal', function () {
  var induk = $('.modal.show').not(this).not('.pld-modal-tertimbun').last()
  if (induk.length) {
    pldTumpukanModal.push(induk)
    induk.addClass('pld-modal-tertimbun')
  }
})

$(document).on('shown.bs.modal', '.modal', function () {
  pldSisakanSatuBackdrop()
})

$(document).on('hidden.bs.modal', '.modal', function () {
  var induk = pldTumpukanModal.pop()
  if (induk) induk.removeClass('pld-modal-tertimbun')
  // BS4 melepas .modal-open dari <body> begitu satu modal tertutup, padahal masih
  // ada modal lain yang terbuka - pasang lagi supaya scroll body tetap terkunci.
  if ($('.modal.show').length) $('body').addClass('modal-open')
  pldSisakanSatuBackdrop()
})

$(document).ready(function(){
      $('.format-number').autoNumeric('init', { mDec: '2', vMin: '-9999999999999.99' })

      // Kedua tabel (Outstanding Pembayaran & Pelunasan Piutang) memakai pola
      // ReportTable (geser + sembunyikan kolom), sama seperti penerimaandpp.
      pldInitReportTableSekali()
      // Modal Perkiraan (#formPerkiraan): barisnya digambar blade, jadi DataTables-nya cukup
      // dibuat sekali - tampilan disamakan dengan modal Perkiraan menu Kas (picker-kas). Kotak
      // cari buatan sendiri (pldIkatCariPerkiraanModal) diganti kotak Search bawaan DataTables.
      // pldIkatCariPerkiraanModal()
      pickerKasInit('tabel_add_list_perkiraan')
      pldIkatSearchProsesDpp()
      $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().columns.adjust() }
        if ($.fn.DataTable.isDataTable('#tabel')) { $('#tabel').DataTable().columns.adjust() }
      })
      loadAll()

      //   $("#tabel_add_list_modal").DataTable({
      //     "lengthChange": false,
      //       "paging": false ,'order': [[1, 'asc']],
      //       "searching" : false,
      //       "columnDefs": [
      //     {"targets" :[0] , 'orderable' : false}
      //  ]
      // });

      // $('#page1').hide()
      // $('#page2').show()

        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });

});
/* ==========================================================================
   Kedua tabel halaman ini memakai pola ReportTable (geser kolom + sembunyikan
   kolom + bar kolom tersembunyi), disalin dari pembelianpermintaandebetnote.blade.php.

   href dipatok, bukan diambil dari window.location - harus sama persis dengan
   PenerimaanDPPController::HREF dan ::HREF_OUT.
   ========================================================================== */
const PLD_HREF = 'pelunasanpiutangdpp'
const OUT_HREF  = 'pelunasanpiutangdppoutstanding'

let pldCart = []
let dataPld = []

let outCart = []

/* ReportTable membaca susunan kolom dari window.gcart_header dan menyimpan lewat
   window.g_href, jadi keduanya harus ditukar setiap kali tabel yang dipegang
   berganti. Penukarannya dipasang sebagai onActivate di ReportTable.init() dan
   dipanggil ReportTable.use() di awal tiap fungsi render. */
let rtTabelAktif = 'pld'

function pldPakaiKolomPld () {
  rtTabelAktif = 'pld'
  window.g_href = PLD_HREF
  window.gcart_header = pldCart
}

function pldPakaiKolomOutstanding () {
  rtTabelAktif = 'out'
  window.g_href = OUT_HREF
  window.gcart_header = outCart
}

function pldBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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

function pldKolomTampil () {
  return (pldCart || []).filter(c => Number(c[2]) === 1)
}

function pldKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function pldFormatAngkaDes (nilai, des) {
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

function pldRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return pldFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

function pldHeadHtml (cols) {
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

let pldRtSudahInit = false

function pldInitReportTableSekali () {
  if (pldRtSudahInit || typeof ReportTable === 'undefined') { return }
  pldRtSudahInit = true

  ReportTable.init({
    table      : '#tabel2',
    bar        : '#rtBar',
    onChange   : renderTabelPld,
    onActivate : pldPakaiKolomPld
  })

  // Tabel kedua: Outstanding DPP. ReportTable menyimpan satu instance per selektor
  // tabel dan berpindah sendiri mengikuti tabel yang disentuh user.
  ReportTable.init({
    table      : '#tabel',
    bar        : '#rtBarOut',
    onChange   : renderTabelOutstanding,
    onActivate : pldPakaiKolomOutstanding
  })

  // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
  // roda gigi / pegangan geser ke penangan milik ReportTable.
  ;['tabel_header_pld', 'tabel_header_out'].forEach(function (idThead) {
    let guard = false
    let thead = document.getElementById(idThead)
    if (!thead) { return }

    thead.addEventListener('click', function (e) {
      if (guard) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      guard = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      guard = false
    }, true)
  })
}

/* Bar kolom tersembunyi harus berada tepat di atas tabelnya. DataTables membungkus
   tabel dengan #<id>_wrapper saat init, jadi acuannya ikut berpindah. */
function rtPindahBar (idBar, idTabel) {
  let bar = document.getElementById(idBar)
  let tabel = document.getElementById(idTabel)
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#' + idTabel)) {
    acuan = document.getElementById(idTabel + '_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

function pldPindahBar () {
  rtPindahBar('rtBar', 'tabel2')
}

function pldIkatSearch () {
  let input = document.getElementById('pldSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel2').DataTable().search(input.value).draw()
  })
}

let pldPanjangHalaman = 10
function pldIkatPanjangHalaman () {
  let sel = document.getElementById('pldLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(pldPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    pldPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel2').DataTable().page.len(pldPanjangHalaman).draw()
  })
}

function pldIkatPeriode () {
  let awal  = document.getElementById('pldTglAwal')
  let akhir = document.getElementById('pldTglAkhir')
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

// Kotak cari modal Perkiraan (#formPerkiraan) - barisnya digambar blade, bukan DataTable,
// jadi penyaringannya menyembunyikan baris secara langsung.
function pldIkatCariPerkiraanModal () {
  let input = document.getElementById('input_search_perkiraanmodal')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_add_list_perkiraan tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}

// Kotak cari modal Proses Terima DPP (#form). Tabelnya bukan DataTable dan tiap baris membawa
// checkbox/input ber-id yang dibaca saat submit, jadi baris cukup disembunyikan - tidak dibuang.
function pldIkatSearchProsesDpp () {
  let input = document.getElementById('input_search_proses_dpp')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_add_list_modal tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}

/* Mengisi dropdown valas (#input_add_valas / #input_giro_valas).

   Dropdown-nya HANYA pengganti tampilan input teks yang lama - keduanya tetap
   `disabled`, jadi user tidak bisa memilih valas sendiri dan alur datanya tidak
   berubah sama sekali.

   Kode valas dari database bisa membawa spasi di belakang (kolom char) dan bisa saja
   tidak ada di dbValas. Kalau kodenya tidak ketemu di daftar opsi, opsinya ditambahkan
   di sini supaya nilai yang tampil - dan yang ikut terkirim lewat $(...).val() saat
   submit giro - tetap sama persis dengan data aslinya, seperti waktu masih input teks. */
function pldSetValas (id, kode) {
  let sel = document.getElementById(id)
  if (!sel) { return }

  let nilai = (kode === null || kode === undefined) ? '' : String(kode).trim()

  let ada = Array.prototype.some.call(sel.options, function (o) { return o.value === nilai })
  if (!ada) {
    let opt = document.createElement('option')
    opt.value = nilai
    // Nilai kosong tampil sebagai pilihan kosong, bukan tulisan "undefined".
    opt.text = nilai
    sel.appendChild(opt)
  }

  sel.value = nilai
}

/* ---------- Filter (menggantikan tab "Penerimaan DPP Sudah Otorisasi") ---------- */
let pldFilterOtorisasi = 'SEMUA'

function pldOtorisasi (item) {
  return Number(item.IsOtorisasi1) !== 0 ? 'Sudah' : 'Belum'
}

function pldUpdateFilterBadge () {
  let jml = (pldFilterOtorisasi !== 'SEMUA' ? 1 : 0)
  let badge = document.getElementById('pldFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function pldTerapkanFilter () {
  pldFilterOtorisasi = $('#pldModalOtorisasi').val() || 'SEMUA'
  pldUpdateFilterBadge()
  $('#modalFilterPld').modal('hide')
  renderTabelPld()
}

function pldResetFilter () {
  pldFilterOtorisasi = 'SEMUA'
  $('#pldModalOtorisasi').val('SEMUA')
  pldUpdateFilterBadge()
  $('#modalFilterPld').modal('hide')
  renderTabelPld()
}

/* ---------- Simpan / muat susunan kolom ---------- */
window.g_href = PLD_HREF
window.g_modeReport = 1
window.gcart_header = []

// Keduanya dipanggil ReportTable untuk tabel yang sedang aktif, jadi cart dan href
// yang dipakai mengikuti rtTabelAktif (lihat pldPakaiKolom*()).
window.doSimpanHeader = function (href, mode) {
  let outAktif = (rtTabelAktif === 'out')
  let cart     = outAktif ? outCart : pldCart

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
      href     : outAktif ? OUT_HREF : PLD_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Tombol "Reset kolom". Memakai endpoint milik menu ini sendiri karena
// HeaderTableController belum punya cabang untuk href halaman ini.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  let outAktif = (rtTabelAktif === 'out')

  $.ajax({
    url   : "{!! url('pelunasanpiutangdppresetheader') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      tabel  : outAktif ? 'outstanding' : 'pelunasan'
    },
    success : function (res) {
      let cart = pldBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      if (outAktif) { outCart = cart } else { pldCart = cart }
      window.gcart_header = cart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function renderTabelPld () {
  window.g_modeReport = 1
  // Arahkan ReportTable ke tabel ini sebelum headHtml()/renderBar() dipanggil.
  if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use('#tabel2') }
  pldPakaiKolomPld()

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  let cols = pldKolomTampil()
  let kolomRender = cols.map(pldKolomRender)

  let thead = document.getElementById('tabel_header_pld')
  thead.innerHTML = pldHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataPld || []
  if (pldFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return pldOtorisasi(item) === pldFilterOtorisasi })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let sudahOtorisasi = Number(item.IsOtorisasi1) !== 0

    let tombolAksi = ''
    if (sudahOtorisasi) {
      tombolAksi = `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${item.NoBukti}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      tombolAksi = `
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NoBukti}', '${item.NoDPP}')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonDetail('${item.NoBukti}', 1, '${item.NoDPP}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${pldRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${pldRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `
      ${sudahOtorisasi ?
          '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
        :
          '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>'
      }
      <td>${item.OtoUser1 || ''}</td>
      <td>${item.TglOto1 ? formatDate(item.TglOto1) : ''}</td>
    </tr>`
  });

  document.getElementById("tabel2_data").innerHTML = rowTable

  $('#tabel2').DataTable({
    lengthChange: false,
    pageLength: pldPanjangHalaman,
    order: [],
    columnDefs: [{ targets: [0], orderable: false }],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  pldPindahBar()
  pldIkatSearch()
  pldIkatPanjangHalaman()
  pldIkatPeriode()
  let inputSearch = document.getElementById('pldSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel2').DataTable().search(inputSearch.value).draw()
  }
}

/* ==========================================================================
   Tabel Outstanding DPP (#tabel) - toolbar + penyaringan yang sama dengan tab
   Penerimaan DPP. Barisnya digambar JS (bukan perulangan blade) supaya filter
   Penagih dan gaya tombol aksi konsisten dengan tab sebelah.

   Roda gigi / geser kolom sengaja TIDAK dipasang di tabel ini: report-table.js
   hanya menyimpan satu konfigurasi (window.gcart_header) per halaman, dan itu
   sudah dipakai tabel Penerimaan DPP.
   ========================================================================== */
let dataOutstanding = []
let outPanjangHalaman = 10

function outIkatSearch () {
  let input = document.getElementById('outSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabel').DataTable().search(input.value).draw()
  })
}

function outIkatPanjangHalaman () {
  let sel = document.getElementById('outLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(outPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    outPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabel').DataTable().page.len(outPanjangHalaman).draw()
  })
}

function outKolomTampil () {
  return (outCart || []).filter(c => Number(c[2]) === 1)
}

// Customer belum teridentifikasi: kode CZ999, atau nama kosong/null/"NO NAME".
function outCustNoName (item) {
  let kode = String(item.CustSuppL || '').trim().toUpperCase()
  let nama = String(item.Customer || '').trim().toUpperCase()
  return kode === 'CZ999' || nama === '' || nama === 'NO NAME'
}

// Outstanding hanya punya satu batas tanggal (sampai tanggal akhir).
function outIkatPeriode () {
  let akhir = document.getElementById('outTglAkhir')
  if (!akhir || akhir.dataset.rtBound) { return }
  akhir.dataset.rtBound = '1'

  akhir.addEventListener('change', function () {
    if (!akhir.value) { return }
    loadAll()
  })
}

function renderTabelOutstanding () {
  window.g_modeReport = 1
  // Arahkan ReportTable ke tabel ini sebelum headHtml()/renderBar() dipanggil.
  if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use('#tabel') }
  pldPakaiKolomOutstanding()

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = outKolomTampil()
  let kolomRender = cols.map(pldKolomRender)

  // Judul kolom digambar ReportTable (pegangan geser + roda gigi); kolom Actions
  // ditambahkan di depan karena bukan kolom data dan tidak bisa digeser/disembunyikan.
  let thead = document.getElementById('tabel_header_out')
  thead.innerHTML = pldHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }

  let dataTampil = dataOutstanding || []

  let rowTable = ''
  dataTampil.forEach((item) => {
    let noName = outCustNoName(item)
    let btnAdd = noName
      ? `<button class="btn btn-primary btn-sm" type="button" title="Customer masih NO NAME" onclick="alertify.warning('Customer masih NO NAME - koreksi customer dulu sebelum diproses')"><i class="bi bi-plus"></i></button>`
      : `<button class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item.NOBUKTI}')"><i class="bi bi-plus"></i></button>`
    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">
        ${btnAdd}
        <button class="btn btn-success btn-sm" type="button" title="Koreksi Customer" onclick="buttonKoreksiCustomer('${item.NOBUKTI}','${item.urutTrans}')"><i class="bi bi-pen"></i></button>
      </div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${pldRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${pldRenderNilai(c, item)}</td>`
      }
    });
    rowTable += `</tr>`
  })

  document.getElementById('tabel_data').innerHTML = rowTable

  $('#tabel').DataTable({
    lengthChange: false,
    pageLength: outPanjangHalaman,
    order: [],
    columnDefs: [{ targets: [0], orderable: false }],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  })

  rtPindahBar('rtBarOut', 'tabel')
  outIkatSearch()
  outIkatPanjangHalaman()
  outIkatPeriode()
  let inputSearch = document.getElementById('outSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabel').DataTable().search(inputSearch.value).draw()
  }
}
      function outTampilLoading (tampil) {
        let outEl = document.getElementById('outLoading')
        let pldEl = document.getElementById('pldLoading')
        if (outEl) { outEl.hidden = !tampil }
        if (pldEl) { pldEl.hidden = !tampil }
      }

      function loadAll () {

        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('pelunasanpiutangdpploadall') !!}",
          type: "get",
          data: {
            tglawal    : $('#pldTglAwal').val(),
            tglakhir   : $('#pldTglAkhir').val(),
            // Tab Outstanding DPP hanya punya satu batas: sampai tanggal akhir.
            outtglakhir: $('#outTglAkhir').val()
          },
          beforeSend: function () {
            outTampilLoading(true)
          },
          complete: function () {
            outTampilLoading(false)
          },
          success: function(res) {
            // Tabel Outstanding DPP digambar renderTabelOutstanding() dari
            // res.tempOutstanding + konfigurasi kolomnya sendiri (awalan "out").
            outCart = pldBuatCart(res.outheadertableheader, res.outheadertablevalue, res.outisnumeric, res.outisshown, res.outdesimal, res.outaliasordered)
            dataOutstanding = res.tempOutstanding || []

            // Tabel Penerimaan DPP digambar renderTabelPld() dari data + konfigurasi
            // kolom yang ikut dikirim loadAll(). Tidak ada lagi tempPenerimaan2 -
            // sudah & belum otorisasi kini satu kumpulan data yang disaring di browser.
            pldCart = pldBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
            dataPld = res.tempPelunasan || []

            renderTabelOutstanding()
            renderTabelPld()

          },
          error : function (err) {
            console.log(err)
            alertify.warning('Gagal memuat data - koneksi ke server terputus, silakan coba lagi')
          }})

      }

// function testes () {
//   $("#formX").modal('toggle')
// }

// ---------- Koreksi customer NO NAME (tab Outstanding Pembayaran) ----------
let koreksiCustCtx = null
let koreksiCustDaftar = null

function buttonKoreksiCustomer (nobukti, urut) {
  let akses = $("#akses_iskoreksi").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let item = (dataOutstanding || []).find(i => i.NOBUKTI === nobukti && String(i.urutTrans) === String(urut))
  koreksiCustCtx = { nobukti, urut, customerLama: item ? item.Customer : '', kode: '', nama: '' }

  document.getElementById('input_koreksicust_nobukti').value = nobukti
  document.getElementById('input_koreksicust_customerlama').value = koreksiCustCtx.customerLama || '(kosong)'
  document.getElementById('input_koreksicust_kode').value = ''
  document.getElementById('input_koreksicust_nama').value = ''

  $("#formKoreksiCustomer").modal('show')
}

// Browse customer dari modal koreksi - daftar dimuat sekali lalu dipakai ulang.
function buttonAddListKoreksiCustomer () {
  let render = function () {
    koreksiCustRenderDaftar(koreksiCustDaftar || [])
    koreksiCustIkatCari()
    $("#formKoreksiCustomerList").modal('show')
  }

  if (koreksiCustDaftar) { render(); return }

  $.ajax({
    url: "{!! url('pelunasanpiutangdpplistcustomer') !!}",
    type: "get",
    success: function (res) {
      koreksiCustDaftar = res || []
      render()
    },
    error: function () {
      alertify.warning('Gagal memuat daftar customer')
    }
  })
}

// Modal daftar dibuka di atas modal koreksi. Bootstrap melepas body.modal-open
// begitu modal daftar ditutup, jadi kelasnya dipasang lagi supaya modal koreksi
// di belakangnya tetap bisa di-scroll seperti biasa.
$(function () {
  $("#formKoreksiCustomerList").on('show.bs.modal', function () {
    setTimeout(function () {
      $('.modal-backdrop').not('.kc-backdrop-atas').last().addClass('kc-backdrop-atas')
    }, 0)
  })
  $("#formKoreksiCustomerList").on('hidden.bs.modal', function () {
    if ($("#formKoreksiCustomer").hasClass('show')) {
      $('body').addClass('modal-open')
    }
  })
})

function koreksiCustRenderDaftar (list) {
  let rowTable = ''
  list.forEach((c) => {
    rowTable += `<tr class="pick-row" onclick="buttonPickKoreksiCustomer('${c.KODECUSTSUPP}','${(c.NAMACUSTSUPP || '').replace(/'/g, "\\'")}')">
        <td>${c.KODECUSTSUPP}</td>
        <td>${c.NAMACUSTSUPP || ''}</td>
        <td>${c.ALAMAT1 || ''}</td>
      </tr>`
  })
  // Tampilan disamakan dengan modal pemilih menu Kas (pickerKasInit, public/js/picker-kas.js):
  // dropdown "Tampilkan" + kotak Search bawaan DataTables. Tabel dihancurkan dulu sebelum
  // isinya ditulis ulang, lalu dibuat lagi. order: [] supaya urutan baris tetap urutan dari server.
  if ($.fn.DataTable.isDataTable('#tabel_add_list_koreksicustomer')) { $('#tabel_add_list_koreksicustomer').DataTable().destroy() }
  document.getElementById('tabel_data_add_list_koreksicustomer').innerHTML = rowTable
  pickerKasInit('tabel_add_list_koreksicustomer', { "order": [] })
}

function koreksiCustIkatCari () {
  let input = document.getElementById('input_search_koreksicustomer')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    // Dulu menyembunyikan baris langsung; sekarang lewat DataTables supaya pencarian
    // menjangkau semua halaman, bukan hanya baris yang sedang tampil.
    // let cari = input.value.toLowerCase()
    // let baris = document.querySelectorAll('#tabel_data_add_list_koreksicustomer tr')
    // baris.forEach(function (tr) {
    //   tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    // })
    if ($.fn.DataTable.isDataTable('#tabel_add_list_koreksicustomer')) { $('#tabel_add_list_koreksicustomer').DataTable().search(input.value).draw() }
  })
}

// Pilih customer dari daftar - hanya mengisi kolom, simpan lewat tombol Simpan.
function buttonPickKoreksiCustomer (kode, nama) {
  if (!koreksiCustCtx) { return }

  koreksiCustCtx.kode = kode
  koreksiCustCtx.nama = nama
  document.getElementById('input_koreksicust_kode').value = kode
  document.getElementById('input_koreksicust_nama').value = nama
  $("#formKoreksiCustomerList").modal('hide')
}

function submitKoreksiCustomer () {
  if (!koreksiCustCtx) { return }
  let ctx = koreksiCustCtx

  if (!ctx.kode) {
    alertify.warning('Pilih customer')
    return
  }

  alertify.confirm(
    'Koreksi Customer',
    `Ubah customer No Bukti ${ctx.nobukti} dari "${ctx.customerLama || '(kosong)'}" menjadi "${ctx.nama}" ?`,
    function () {
      let _token = $("#_token").val();
      $.ajax({
        url: "{!! url('pelunasanpiutangdppkoreksicustomer') !!}",
        type: "post",
        data: { _token, nobukti: ctx.nobukti, urut: ctx.urut, kodecustsupp: ctx.kode },
        success: function (res) {
          if (Number(res) === 1) {
            alertify.success('Customer berhasil dikoreksi')
            $("#formKoreksiCustomer").modal('hide')
            loadAll()
          } else {
            alertify.warning(res || 'Gagal mengoreksi customer')
          }
        },
        error: function () {
          alertify.warning('Gagal mengoreksi customer - koneksi ke server terputus')
        }
      })
    },
    function () {}
  )
}

function buttonAddListPerkiraanLebihBayar (id) {
  toId = id
  $("#formPerkiraan").modal('show')
}

function buttonAddPickPerkiraanLebihBayar (perkiraan , nama) {
  document.getElementById(`input_modalx_perkiraan${toId}`).value = perkiraan
  document.getElementById(`input_modalx_namaperkiraan${toId}`).value = nama
  $("#formPerkiraan").modal('hide')

}

function buttonSaveLB () {
    let xnilainota = toNum($("#input_modalx_nilainotadibayar").val())
    let xdibayar = toNum($("#input_modalx_dibayar").val())
    let xlebihbayar = toNum($("#input_modalx_lebihbayar").val())
    let xperkiraanlebihbayar = $("#input_modalx_perkiraanlebihbayar").val()
    let xnamaperkiraanlebihbayar = $("#input_modalx_namaperkiraanlebihbayar").val()
    let xsisa = toNum($("#input_modalx_sisanotadibayar").val())

    let checksisadibayar = Number(listProsesTerimaDPP[saveHeaderIndex].DIBAYAR)
    let checksisalb = Number(listTambahLB[listProsesTerimaDPP[saveHeaderIndex].NOFAKTUR]) ? Number(listTambahLB[listProsesTerimaDPP[saveHeaderIndex].NOFAKTUR]) : 0
    let checksisa = Number(checksisadibayar) + Number(checksisalb)
    console.log(xdibayar)
    console.log(xlebihbayar)
    console.log(xperkiraanlebihbayar)
    console.log(xnamaperkiraanlebihbayar)
    console.log(xsisa)
    console.log(checksisadibayar)
    console.log(checksisalb)
    console.log(checksisa)
    console.log("<3")
    let xlist = listTambahKL[saveHeaderInvoice.NOFAKTUR]
    let xTempTotalKL = 0
    if (!xlist) {
      xlist = []
    }
    xlist.forEach((item, i) => {
      xTempTotalKL += Number(item.inputKL)
    })
    console.log(Number(checksisalb) , Number(xlebihbayar) , Number(xsisa))
    if (Number(xdibayar) + Number(xTempTotalKL) > Number(xnilainota)) {
      alertify.warning("Dibayar + KL melebihi nilai nota")
      return
    }

    if (  Number(xlebihbayar) + Number(xdibayar) > Number(checksisa) + Number(xsisa) ) {
      alertify.warning("Melebihi sisa nota")
      return
    }
    // totfaktur
    if ( Number(xdibayar) <= 0 && Number(xlebihbayar) <= 0) {
      alertify.warning("Jumlah <= 0")
      return
    }
    if (Number(xnilainota) < Number(xdibayar)) {
      alertify.warning("Dibayar > Nilai Nota")
      return
    }

    // if ()

    if (Number(xlebihbayar) > 0 && !xperkiraanlebihbayar) {
      alertify.warning("Perk lebih bayar belum diisi")
      return
    }
    if ( Number(xlebihbayar) <= 0 && xperkiraanlebihbayar) {
      alertify.warning("Lebih bayar belum diisi")
      return
    }

    if (Number(xlebihbayar) + Number(xdibayar) > Number(xsisa) + Number(checksisa)) {
      alertify.warning("Jumlah melebihi sisa")
      return
    }

    if (Number(xlebihbayar) > 0) {


      let x = { ...saveHeaderInvoice }
      x.inputLB = xlebihbayar
      x.inputPerkiraanLB = xperkiraanlebihbayar
      x.inputNamaPerkiraanLB = xnamaperkiraanlebihbayar
      // x.DIBAYAR = 0
      // x.indexHeader = saveHeaderIndex
      listTambahLB[saveHeaderInvoice.NOFAKTUR] = x

    }
    console.log(listTambahLB)

    listProsesTerimaDPP[saveHeaderIndex].DIBAYAR = xdibayar

    // listTambahLB
    // listTambah
    refreshSisa()


}

function prosesCheckbox (index) {

 // id="list_proses_checkbox${i}"

 let xdata = listProsesTerimaDPP[index]
 console.log(xdata)
 // console.log(listProsesTerimaDPP[index])
 let xcheck = document.getElementById(`list_proses_checkbox${index}`).checked
 let xsisa = toNum($("#input_modal_sisa").val())
 let xdibayar = toNum($(`#list_proses_dibayar${index}`).val())
 console.log(xcheck)


 if (xcheck && xsisa <= 0) {
   document.getElementById(`list_proses_checkbox${index}`).checked = false
   alertify.warning("Sisa nota habis")
   return
 }

 if (Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR) <= 0) {
   console.log("bcd")

   return
 }

 if (xcheck) {
   console.log('1' , Number(xsisa))
   if(Number(xsisa) > 0) {

       console.log('2' , Number(xdibayar))
     if (Number(xdibayar) == 0) {
       if (Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR) < Number(xsisa)) {
          setNum(`list_proses_dibayar${index}`, Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR))
         listProsesTerimaDPP[index].DIBAYAR = Number(xdata.TOTFAKTUR) - Number(xdata.SDHBAYAR)
       } else {
          setNum(`list_proses_dibayar${index}`, xsisa)
         listProsesTerimaDPP[index].DIBAYAR = Number(xsisa)
       }
       refreshSisa()
     }


   }
 } else {
  setNum(`list_proses_dibayar${index}`, 0)
  setNum(`list_proses_LB${index}`, 0)
  setNum(`list_proses_KL${index}`, 0)
  listProsesTerimaDPP[index].DIBAYAR = 0
  delete listTambahLB[xdata.NOFAKTUR];
  delete listTambahKL[xdata.NOFAKTUR];
  refreshSisa()

 }
 console.log("^^^^^^^^^^^^^^^^^^^^^^^")
 console.log(listTambahLB)
 console.log(listProsesTerimaDPP)
 console.log("^^^^^^^^^^^^^^^^^^^^^^^")


}

function refreshSisa () {
  console.log('refreshSisa')
  let totdibayar = toNum($("#input_add_dibayar").val())
  let totlb = 0
  listProsesTerimaDPP.forEach((item, i) => {
    console.log('wwwwwwwwwwwww')
    console.log('listProsesTerimaDPP' , listTambahLB[item.NOFAKTUR])
    totdibayar += Number(item.DIBAYAR)

    if (listTambahLB[item.NOFAKTUR]) {
      totlb += Number(listTambahLB[item.NOFAKTUR].inputLB)
      setNum(`list_proses_LB${i}`, listTambahLB[item.NOFAKTUR].inputLB)
    }

    setNum(`list_proses_dibayar${i}`, item.DIBAYAR)
    console.log(totdibayar)
    console.log(totlb)

  });
  let xtot = Number(totdibayar) + Number(totlb)
  let xjumlah = toNum($("#input_modal_jumlah").val())

  console.log(parseFloat(xtot).toFixed(2))
  console.log(parseFloat(Number(xjumlah) - xtot).toFixed(2))
  setNum(`input_modal_dibayar`, xtot)
  setNum(`input_modal_sisa`, Number(xjumlah) - xtot)
  setNum(`input_modalx_sisanotadibayar`, Number(xjumlah) - xtot)



}

function buttonAddKL () {

  $('.showhideitemKL').show()

  setNum("input_modalx_kurangbayar", 0)
  document.getElementById("input_modalx_perkiraankurangbayar").value = ''
  document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
}


function submitAddKL () {
    let kl = toNum($("#input_modalx_kurangbayar").val())
    let perkkl = $("#input_modalx_perkiraankurangbayar").val()
    let namaperkkl = $("#input_modalx_namaperkiraankurangbayar").val()
    // perkkl = '444'
    // namaperkkl = 'TESTESwiu'

    if (Number(kl) <= 0 || !perkkl) {

      alertify.warning("Data tidak lengkap")
      return
    }
    let xnilainotadibayar = toNum($("#input_modalx_nilainotadibayar").val())
    let xdibayar = toNum($("#input_modalx_dibayar").val())
    if (Number(xnilainotadibayar) + Number(xdibayar) < Number(kl)) {
      alertify.warning('KL melebihi nilai nota + dibayar')

      return
    }


    let xsisa = toNum($("#input_modalx_sisanotadibayar").val())
    if (Number(kl) > Number(xsisa)) {
      alertify.warning('Melebihi sisa nota')
      return
    }
    alertify.success("KL berhasil ditambah")
    let x = { ...saveHeaderInvoice }
    x.inputKL = kl
    x.inputPerkiraanKL = perkkl
    x.inputNamaPerkiraanKL = namaperkkl
    console.log(x)
    console.log(listTambahKL)
    console.log('!!!!')
    console.log(saveHeaderInvoice.NOFAKTUR)
    if (!listTambahKL[saveHeaderInvoice.NOFAKTUR]) {
      listTambahKL[saveHeaderInvoice.NOFAKTUR] = []
    }
    console.log(listTambahKL)
    listTambahKL[saveHeaderInvoice.NOFAKTUR].push(x)
    console.log(listTambahKL)
    refreshTableKL()
    $('.showhideitemKL').hide()

}

function buttonAddBatalKL () {
  $('.showhideitemKL').hide()
}

function onChangeTransaksi () {
  document.getElementById("input_add_kodeperkiraan").value = ''
  document.getElementById("input_add_keteranganperkiraan").value = ''
  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggal").valueAsDate = new Date()
  document.getElementById("input_add_kepadaterima").value = ''

  document.getElementById("input_add_bon").value = ''
  document.getElementById("input_add_nilaibon").value = '0.00'

    console.log("onChangeTransaksi")
    $('.showhideitem').hide();
    $('.showhidePart').hide();
    let value = $("#input_add_transaksi").val()
    console.log(value)
    $(`.part${value}`).show();


}

function setNewNoBukti () {
  console.log('setNewNoBukti')
  let _token  = $("#_token").val()
  let kode  = 'PLD'
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


function cleanFormAddAdd () {
  document.getElementById("AddAddKodeDevisi").value = ''
  document.getElementById("AddAddNamaDevisi").value = ''
  document.getElementById("AddAddValas").value = 'IDR'
  document.getElementById("AddAddKurs").value = '1.00'
  document.getElementById("AddAddLawan").value = ''
  document.getElementById("AddAddKeteranganLawan").value = ''
  document.getElementById("AddAddJumlah").value = '0.00'
  document.getElementById("AddAddKeterangan").value = ''
  document.getElementById("AddAddKeteranganDetail").value = ''
  document.getElementById("AddAddKodeDepartemen").value = ''
  document.getElementById("AddAddNamaDepartemen").value = ''

}


function closeShowHideAdd () {
  $('.showhide').hide();

}

function cleanModalAdd () {


  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value


  var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
  console.log(lastDayOfMonth)

  document.getElementById("input_modal_nobukti").value = ''
  document.getElementById("input_modal_nourut").value = ''
  document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  document.getElementById("input_modal_valas").value = 'IDR'
  document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)

}

function tesConcat () {
  let x = []
  let y = ['a' , 'b']
  let z = [1,2,3]

  let a = x.concat(y)
  console.log(a)
  a = a.concat(z)
  console.log(a)
}


function submitAdd () {

  let _token  = $("#_token").val()
  let choice = "I"
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let valas  = $("#input_add_valas").val()
  let tipe = 'DPP'

  let kodecustsupp  = $("#input_add_kodecust").val()
  let checkDate = new Date($("#input_add_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
let nobkmbbm = $("#input_add_nobkmbbm").val();
  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let tanggal  = $("#input_add_tanggal").val()
  let jmlrecord = tipeform == "add" ? 0 : 1

  let xlisttambah = []
  let xlisttambahlb = []
  let xlisttambahkl = []

  console.log('[][][][][][][][]')
  console.log(listTambahLB)
  console.log(listTambahKL)
  console.log(listTambah)




  console.log('[][][][][][][][]')

  listProsesTerimaDPP.forEach((item, i) => {
    if (document.getElementById(`list_proses_checkbox${i}`).checked) {
      xlisttambah.push(listProsesTerimaDPP[i])
      if (listTambahLB[listProsesTerimaDPP[i].NOFAKTUR]) {
        console.log(listTambahLB[listProsesTerimaDPP[i].NOFAKTUR])
        xlisttambahlb.push(listTambahLB[listProsesTerimaDPP[i].NOFAKTUR])
      }
      if (listTambahKL[listProsesTerimaDPP[i].NOFAKTUR]) {
        console.log(xlisttambahkl)
        console.log(listTambahKL[listProsesTerimaDPP[i].NOFAKTUR])
        let tempkl = listTambahKL[listProsesTerimaDPP[i].NOFAKTUR]
        tempkl.forEach((item, i) => {
          xlisttambahkl.push(item)
        });

        // xlisttambahkl.concat(tempkl)
        console.log("11111111")
        console.log(xlisttambahkl)
      }

    }
  });
  console.log('tambah')
  console.log(xlisttambah)
  console.log('tambahkl')
  console.log(xlisttambahkl)
  console.log('tambahlb')
  console.log(xlisttambahlb)

  if (!xlisttambah.length ) {
    alertify.warning("Tidak ada data dipilih")
    return
  }


  $.ajax({
      url: "{!! url('pelunasanpiutangdppspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        choice,
        tempData : xlisttambah ,
        tempDataKL : xlisttambahkl ,
        tempDataLB : xlisttambahlb ,
        nobukti,
        nourut,
        tipe,
        tanggal,
        jmlrecord,
        kodecustsupp,
        urutTrans
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          alertify.success('DPH telah ditambah');
          document.getElementById("input_add_tanggal").disabled = true

          tipeform = 'edit'
          $("#form").modal('hide')
          refreshTableKoreksi(nobukti ,nobkmbbm)
          loadAll()

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





  return


  // let _token  = $("#_token").val()
  // let choice = "I"
  // let nobukti  = $("#input_modal_nobukti").val()
  // let nourut  = $("#input_modal_nourut").val()
  // let valas  = $("#input_modal_valas").val()
  // let tipe = 'DPP'
  // let checkDate = new Date($("#input_modal_tanggal").val())
  // let periode_bulan = document.getElementById("periode_bulan").value
  // let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
    console.log(checkDate.getFullYear())
    console.log(Number(periode_tahun))
    console.log((checkDate.getMonth() +1))
    console.log(Number(periode_bulan))
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }


  // let tanggal  = $("#input_modal_tanggal").val()

  if(!listCheckListPengajuan.length) {
    alertify.warning("Tidak ada item dipilih")
  }

  // let jmlrecord = tipeform == "add" ? 0 : 1

  console.log({
    tempData : listCheckListPengajuan ,
    choice,
    valas,
    nobukti,
    nourut,
    tipe,
    tanggal
  })


  $.ajax({
      url: "{!! url('pengajuandphspadd') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tempData : listCheckListPengajuan ,
        choice,
        valas,
        nobukti,
        nourut,
        tipe,
        tanggal,
        jmlrecord
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          alertify.success('DPH telah ditambah');

          tipeform = 'edit'
          $("#form").modal('hide')
          buttonKoreksi(nobukti)

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


function submitAddAdd () {

  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }

  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_kepadaterima").val()
  let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
  let tanggal = $("#input_add_tanggal").val()

  let lampiran = 0
  let keterangan2 = ''
  let choice = "I"


  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = $("#AddAddKurs").val()
  let lawan  = $("#AddAddLawan").val()
  // let kodelawan  = $("#AddAddKodeLawan").val()
  let jumlah  = $("#AddAddJumlah").val()
  let keterangan  = $("#AddAddKeterangan").val()
  let keterangandetail  = $("#AddAddKeteranganDetail").val()
  let kodedepartemen  = $("#AddAddKodeDepartemen").val()

  let kredit = 0
  let kreditrp = 0

  let tphc = 'C'

  if (!kodedevisi || !valas || !lawan || !kodedepartemen || !keterangan) {
    alertify.warning("Data tidak lengkap")
    return
  }

  if (jumlah < 0) {
    alertify.warning("Jumlah < 0")
    return
  }

  let jumlahrp = Number(jumlah) * Number(kurs)


  let urut = 0

  let custsuppP = ''
  let custsuppL = ''
  let noaktivaP = ''
  let noaktivaL = ''
  let statusaktivaP = ''
  let statusaktivaL = ''

  let nobon = $("#input_add_bon").val()
  let kodebag = '-'

  let kodeP = ''
  let kodeL = ''
  let statusgiro = ''
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = ''
  let kodesubcost = ''
  let nodph = ''
  let urutdph = 0
  let dppdph = ''
  let tp = ''
  let ppklx = ''
  let nofaktur = ''
  let plok = 0
  let nobons = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = ''
  let uruttitipan = 0
  let pSKB = 0
  let perkiraanx = transaksi == 'BBK' ? lawan : kodeperkiraan
  let lawanx = transaksi == 'BBK' ? kodeperkiraan : lawan

  let kodeFlag = $("#AddAddKodeLawan").val()
  let custsupp = ''






  if (kodeFlag == "HT" || kodeFlag == 'UHT') {
    if (transaksi == 'BBK') {
      kodeP = kodeFlag
      statusaktivaP = "HT-"
      custsuppP = tempDPPDPH.KODECUSTSUPP
      custsupp = tempDPPDPH.KODECUSTSUPP
      dppdph = 'DPH'
      nodph = tempDPPDPH.Nobukti

    } else if (transaksi == 'BBM' && kodeFlag == 'HT') {
      kodeL = kodeFlag
      statusaktivaL = "HT-"
      custsuppL = tempDPPDPH.KODECUSTSUPP
      custsupp = tempDPPDPH.KODECUSTSUPP
      dppdph = 'DPP'
      nodph = tempDPPDPH.Nobukti

    }



  }


  if ( kodeFlag == "UHT" && transaksi == "BBM") {


    custsuppL =  $("#input_dphuhtbbm_kodecustsupp").val();
    custsupp = $("#input_dphuhtbbm_kodecustsupp").val();
    statusAktivaL = 'UHT-'
    kodeL = kodeFlag







  }




  if (lawan == '113400') {

    custsupp = $("#AddAddKodeCustsupp").val()
    if (!custsupp) {
      alertify.warning("Pilih customer")
      return
    }
    custsuppP = custsupp
    custsuppL = custsupp




  }


  if (transaksi == 'BBK') {
    kodeP = $("#AddAddKodeLawan").val()
    if (kodeP == "HT") {
      statusaktivaP = "HT-"
    }
  } else {
    kodeL = $("#AddAddKodeLawan").val()
    if (kodeL == "HT") {
      statusaktivaL = "HT-"
    }

  }



  if (transaksi == 'BBM' && kodeFlag == 'UHT') {
    $.ajax({
        url: "{!! url('bankspadd') !!}",
        type: "post",
        async: false,
        data: {
          tipeform,
          choice,
          _token,
          nobukti ,
          nourut,
          transaksi,
          note,
          kodeperkiraan ,
          tanggal,

          lampiran ,
          keterangan2 ,
          perkiraanx,
          lawanx,

          kodedevisi ,
          valas ,
          kurs  ,
          lawan ,
          jumlah,
          keterangan  ,
          keterangandetail ,
          kodedepartemen  ,

          kredit,
          kreditrp,

          tphc,
          jumlahrp ,


          urut ,

          custsuppP ,
          custsuppL,
          noaktivaP ,
          noaktivaL ,
          statusaktivaP ,
          statusaktivaL ,

          nobon ,
          kodebag ,

          kodeP ,
          kodeL ,
          statusgiro ,
          simbol,
          flagsimbol ,
          kodecost ,
          kodesubcost ,
          nodph ,
          urutdph,
          dppdph,
          tp,
          ppklx ,
          nofaktur,
          plok ,
          nobons,
          jmlrecord,
          notitipan ,
          uruttitipan,
          pSKB,
          custsupp
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('Bank telah ditambah');

            $('.showhideitem').hide();
            loadAll()
            // buttonCloseForm()
            tipeform = 'edit'
            // document.getElementById("buttonAddListCustomer").disabled = true
            // document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            // $("#form").modal('toggle')

          }
          if (res == 2) {
            setNewNoBukti()
            alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
          }
          //
          // if (res == 3 ) {
          //   alertify.warning('Stok gudang tidak mencukupi');
          // }

        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })

  } else if (kodeFlag == 'HT' || kodeFlag == 'UHT') {
    $.ajax({
        url: "{!! url('bankspadddppdph') !!}",
        type: "post",
        async: false,
        data: {
          tipeform,
          choice,
          _token,
          nobukti ,
          nourut,
          transaksi,
          note,
          kodeperkiraan ,
          tanggal,

          lampiran ,
          keterangan2 ,
          perkiraanx,
          lawanx,

          kodedevisi ,
          valas ,
          kurs  ,
          lawan ,
          jumlah,
          keterangan  ,
          keterangandetail ,
          kodedepartemen  ,

          kredit,
          kreditrp,

          tphc,
          jumlahrp ,


          urut ,

          custsuppP ,
          custsuppL,
          noaktivaP ,
          noaktivaL ,
          statusaktivaP ,
          statusaktivaL ,

          nobon ,
          kodebag ,

          kodeP ,
          kodeL ,
          statusgiro ,
          simbol,
          flagsimbol ,
          kodecost ,
          kodesubcost ,
          nodph ,
          urutdph,
          dppdph,
          tp,
          ppklx ,
          nofaktur,
          plok ,
          nobons,
          jmlrecord,
          notitipan ,
          uruttitipan,
          pSKB,
          custsupp
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('Bank telah ditambah');
            loadAll()
            // buttonCloseForm()
            $('.showhideitem').hide();
            tipeform = 'edit'
            lockFormAdd()
            // document.getElementById("buttonAddListCustomer").disabled = true
            // document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            // $("#form").modal('toggle')

          }
          if (res == 2) {
            setNewNoBukti()
            alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
          }
          //
          // if (res == 3 ) {
          //   alertify.warning('Stok gudang tidak mencukupi');
          // }

        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })
  } else {
    $.ajax({
        url: "{!! url('bankspadd') !!}",
        type: "post",
        async: false,
        data: {
          tipeform,
          choice,
          _token,
          nobukti ,
          nourut,
          transaksi,
          note,
          kodeperkiraan ,
          tanggal,

          lampiran ,
          keterangan2 ,
          perkiraanx,
          lawanx,

          kodedevisi ,
          valas ,
          kurs  ,
          lawan ,
          jumlah,
          keterangan  ,
          keterangandetail ,
          kodedepartemen  ,

          kredit,
          kreditrp,

          tphc,
          jumlahrp ,


          urut ,

          custsuppP ,
          custsuppL,
          noaktivaP ,
          noaktivaL ,
          statusaktivaP ,
          statusaktivaL ,

          nobon ,
          kodebag ,

          kodeP ,
          kodeL ,
          statusgiro ,
          simbol,
          flagsimbol ,
          kodecost ,
          kodesubcost ,
          nodph ,
          urutdph,
          dppdph,
          tp,
          ppklx ,
          nofaktur,
          plok ,
          nobons,
          jmlrecord,
          notitipan ,
          uruttitipan,
          pSKB,
          custsupp
        },
        success: function(res) {
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('Bank telah ditambah');

            $('.showhideitem').hide();
            loadAll()
            // buttonCloseForm()
            tipeform = 'edit'
            // document.getElementById("buttonAddListCustomer").disabled = true
            // document.getElementById("input_add_tanggal").disabled = true

            refreshDataTable(nobukti)

            // $("#form").modal('toggle')

          }
          if (res == 2) {
            setNewNoBukti()
            alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
          }
          //
          // if (res == 3 ) {
          //   alertify.warning('Stok gudang tidak mencukupi');
          // }

        },
        error: function (err) {
          console.log(err)
          alertify.warning('Terjadi kesalahan silahkan refresh browser')
        }
      })
  }












}

function submitEdit () {






  let choice = "U"
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let tanggal  = $("#input_add_tanggal").val()

  let nobkmbbm = $("#input_add_nobkmbbm").val()
  let dibayar  = toNum($("#AddAddDibayar").val())
  let kl  = toNum($("#AddAddKurangBayar").val())
  let lb = toNum($("#AddAddLebihBayar").val())
  let perkiraan = $("#AddAddKodePerkiraan").val()

  if (Number(dibayar) > 0 || Number(lb) > 0 || Number(kl) >0 ) {

  } else {
    alertify.warning("Nilai <= 0")
    return
  }
  let urut = dataEdit.URUT
  console.log({

    choice,
    _token,
    nobukti ,
    nourut,
    dibayar,
    perkiraan,
    kl,
    lb,
    tanggal,
    urut
  })
// return



  $.ajax({
      url: "{!! url('pelunasanpiutangdppspkoreksi') !!}",
      type: "post",
      async: false,
      data: {

        choice,
        _token,
        nobukti ,
        nourut,
        dibayar,
        perkiraan,
        kl,
        lb,
        tanggal,
        urut
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('DPP telah diedit');
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          $('.showhideitem').hide();
          refreshTableKoreksi(nobukti, nobkmbbm)

          // $("#form").modal('toggle')

        }
        if (res == 2) {
          setNewNoBukti()
          alertify.warning('Nobukti telah di refresh, silahkan submit ulang');
        }
        //
        // if (res == 3 ) {
        //   alertify.warning('Stok gudang tidak mencukupi');
        // }

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })









}
























function buttonAddBatal () {

  $('.showhideitem').hide();
}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddMain').show();

  $("#form").modal('hide')
}

// function buttonAddListCustomer () {
//
//   $('.showhidemodalbodyadd').hide();
//   $('#modalBodyAddListValas').show();
//
//   $("#form").modal('toggle')
// }


function closeShowHideItem () {
  $('.showhideitem').hide();

}

function unlockFormAdd () {
  document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_tanggal").disabled = false


  document.getElementById("buttonAddListCustomer").disabled = false
  document.getElementById("buttonAddListNoInvoice").disabled = false

}

function lockFormAdd () {
  document.getElementById("input_add_tanggal").disabled = true
  document.getElementById("input_add_bon").disabled = true
  document.getElementById("input_add_kepadaterima").disabled = true
  document.getElementById("buttonAddListPerkiraan").disabled = true
  document.getElementById("input_add_transaksi").disabled = true

}

function lockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = true
  document.getElementById("buttonAddListLawan").disabled = true
  document.getElementById("buttonAddListValas").disabled = true
  document.getElementById("buttonAddListDevisi").disabled = true

}

function unlockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = false
  document.getElementById("buttonAddListLawan").disabled = false
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListDevisi").disabled = false
}




function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('pengajuandphspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listData = res
      // console.log(res)

      $('#formAddAdd').hide();
      if (!res.length) {
          alertify.success('Data Habis')
          // $("#form").modal('toggle')
          $('#page2').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listData.forEach((item, i) => {

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

              rowTable += `
                <tr>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoFaktur}</td>
                  <td class="text-right">${formatAngka(parseFloat(item.dibayar).toFixed(2))}</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.Nilai) - Number(item.dibayar)).toFixed(2)) }</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.dibayar) - Number(item.Nilai)).toFixed(2)) }</td>


                  <td>${item.Perkiraan ? item.Perkiraan : '' }</td>

                  <td>${item.Noinvoice ? item.Noinvoice : '' }</td>
                  <td>${item.TglInv ? formatDate(item.TglInv) : '' }</td>


                  <td class="text-center">

                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonDelete(${i}  )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("addTableData").innerHTML = rowTable


        document.getElementById("input_add_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_add_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        document.getElementById("input_add_tanggal").valueAsDate = new Date(listData[0].Tanggal)
        document.getElementById("input_add_valas").value = listData[0].Valas










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}






function submitOtorisasi () {

  let _token = $("#_token").val();
  let nobukti = $("#input_detail_nobukti").val();
  $.ajax({
    url: "{!! url('pelunasanpiutangdppspotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      if (res == 1) {
        alertify.success('Berhasil update otorisasi')
        loadAll()
        $('.mainpage').hide();
        $('#page1').show();
      }





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

















function buttonAddItem () {
  let _token = $("#_token").val();

  let kodecust = $("#input_add_kodecust").val();
  let nobkmbbm = $("#input_add_nobkmbbm").val();
  listTambah = []
  listTambahKL = []
  $.ajax({
    url: "{!! url('pelunasanpiutangdppgetlistterimadpp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nodpp : nobkmbbm,
      kodecust

    },
    success: function(res) {
      console.log(res)

      listProsesTerimaDPP = res

      let rowTable = ``

      listProsesTerimaDPP.forEach((item, i) => {
        console.log(i)
        rowTable += `
          <tr>
            <td class="text-center"><input class="" type="checkbox" onchange="prosesCheckbox(${i})" value="" id="list_proses_checkbox${i}"></td>
            <td>${item.NOFAKTUR}</td>
            <td>${item.namacust}</td>
            <td class="text-right">${formatAngka(parseFloat(item.TOTFAKTUR).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(item.SDHBAYAR).toFixed(2))}</td>

            <td class="text-center">
            <div class="input-group form-group">
              <input style="height:30px; width:160px" id="list_proses_dibayar${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.DIBAYAR).toFixed(2))}' class="form-control text-right" disabled>

              <button id="buttonChangeDibayar${i}" type="button" onclick="buttonChangeDibayar(${i})" class="btn btn-browsing btn-browsing-sm" title="Ubah Dibayar"><i class="bi bi-search"></i></button>

            </div></td>
            <td class="text-center">
            <input style="height:30px; width:160px" id="list_proses_LB${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.LB).toFixed(2))}' class="form-control text-right" disabled>
            </td>
            <td class="text-center">
            <input style="height:30px; width:160px" id="list_proses_KL${i}" type="text" inputmode="decimal" value='0.00' class="form-control text-right" disabled>
            </td>
          </tr>
        `
      });


      // <td class="text-right">${formatAngka(parseFloat(item.LB).toFixed(2))}</td>
      // <td class="text-right">${formatAngka(parseFloat(item.KL).toFixed(2))}</td>


      if (!listProsesTerimaDPP.length) {
        rowTable = `
          <tr><td colspan=8 class="text-center">Data tidak ditemukkan</td></tr>
        `

      }
      document.getElementById("input_modal_nobukti").value = nobkmbbm
      document.getElementById("input_modal_namacust").value = $("#input_add_namacust").val();
      setNum("input_modal_jumlah", $("#input_add_jumlah").val());
      setNum("input_modal_dibayar", $("#input_add_dibayar").val());
      setNum("input_modal_sisa", $("#input_add_sisa").val());
      document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
      document.getElementById("input_search_proses_dpp").value = ''


        $("#form").modal('show')
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


  // buttonRefreshListPengajuan()


}


function refreshTableKL () {

  console.log(saveHeaderInvoice)
  console.log('refreshTableKL')
  console.log(saveHeaderInvoice.NOFAKTUR)
  console.log(listTambahKL[saveHeaderInvoice.NOFAKTUR])
  let xlist = listTambahKL[saveHeaderInvoice.NOFAKTUR]
  console.log(xlist)
  if (!xlist) {
    xlist = []
  }
  console.log(xlist)
  console.log('weeewoooo')
  if (!xlist.length) {
    document.getElementById("tabel_data_add_list_modalx").innerHTML = `
    <tr>
      <td class="text-center" colspan=3>Belum ada data</td>
    </tr>
    `

  } else {
    rowTablex = ''
    let xTempTotalKL = 0
    xlist.forEach((item, i) => {
      xTempTotalKL += Number(item.inputKL)
      rowTablex += `
        <tr>
          <td class="text-right">${formatAngka(parseFloat(item.inputKL).toFixed(2))}</td>
          <td>${item.inputPerkiraanKL}</td>
          <td>${item.inputNamaPerkiraanKL}</td>
        </tr>
      `

    });

    document.getElementById("tabel_data_add_list_modalx").innerHTML = rowTablex
    setNum(`list_proses_KL${saveHeaderIndex}`, xTempTotalKL)


  }

}


function buttonChangeDibayar (index) {
  // sp_TempTerimaDPP

  let xcheck = document.getElementById(`list_proses_checkbox${index}`).checked
  if(!xcheck) {
    alertify.warning("Pilih invoice terlebih dahulu")
    return
  }
  let x = listProsesTerimaDPP[index]
  console.log(x)
  // listProsesTerimaDPP[saveHeaderIndex]
  saveHeaderInvoice = listProsesTerimaDPP[index]
  saveHeaderIndex = index
  let xdibayar = toNum($(`#list_proses_dibayar${index}`).val());
  let xLB = toNum($(`#list_proses_LB${index}`).val());
  let sisa = toNum($(`#input_modal_sisa`).val());
  console.log(saveHeaderInvoice.NoBukti)
  console.log(listTambahKL[saveHeaderInvoice.NoBukti])

  console.log(x.TOTFAKTUR)
  console.log(formatAngka(parseFloat(x.TOTFAKTUR).toFixed(2)))
  console.log('==')
  console.log(xdibayar, xLB , sisa)
  setNum("input_modalx_nilainotadibayar", Number(x.TOTFAKTUR) - Number(x.SDHBAYAR))
  setNum("input_modalx_dibayar", xdibayar)

  setNum("input_modalx_lebihbayar", xLB)
  setNum("input_modalx_sisanotadibayar", sisa)
  if (xLB > 0) {
    document.getElementById("input_modalx_perkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputPerkiraanLB
    document.getElementById("input_modalx_namaperkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputNamaPerkiraanLB

  } else {
    document.getElementById("input_modalx_perkiraanlebihbayar").value = ''
    document.getElementById("input_modalx_namaperkiraanlebihbayar").value = ''

  }

  refreshTableKL()


  $('.showhideitemKL').hide()


  $("#formX").modal('show')


}


function buttonDeleteItem (index) {
  let akses = $("#akses_ishapus").val();
  tipeform = 'edit'
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  let dataEdit = listPenerimaan[index]


  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus faktur '+ dataEdit.NOFAKTUR +' ?',
      function() {

        let choice = "D"
        let _token  = $("#_token").val()
        let nobukti  = $("#input_add_nobukti").val()
        let nourut  = $("#input_add_nourut").val()
        let tanggal  = $("#input_add_tanggal").val()
        let nobkmbbm = $("#input_add_nobkmbbm").val()

        let dibayar  = 0
        let kl  = 0
        let lb = 0
        let perkiraan = ''
        let urut = dataEdit.URUT

          $.ajax({
              url: "{!! url('pelunasanpiutangdppspkoreksi') !!}",
              type: "post",
              async: false,
              data: {

                choice,
                _token,
                nobukti ,
                nourut,
                dibayar,
                perkiraan,
                kl,
                lb,
                tanggal,
                urut
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('Faktur telah dihapus');
                  loadAll()
                  // buttonCloseForm()
                  tipeform = 'edit'
                  // document.getElementById("buttonAddListCustomer").disabled = true
                  // document.getElementById("input_add_tanggal").disabled = true
                  $('.showhideitem').hide();
                  refreshTableKoreksi(nobukti , nobkmbbm)

                  // $("#form").modal('toggle')

                }
                //
                // if (res == 3 ) {
                //   alertify.warning('Stok gudang tidak mencukupi');
                // }

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

function buttonEditItem (index) {

  console.log(listPenerimaan[index])
  dataEdit = listPenerimaan[index]
  let editDibayar = dataEdit.DIBAYAR
  let editLB = dataEdit.LB
  let editKL = dataEdit.KL
  document.getElementById("AddAddFaktur").value = dataEdit.NOFAKTUR
  document.getElementById("AddAddKodePerkiraan").value = dataEdit.perkiraan
  document.getElementById("AddAddNamaPerkiraan").value = dataEdit.NamaPerkiraan
  document.getElementById("AddAddDibayar").value = '0.00'
  document.getElementById("AddAddKurangBayar").value = '0.00'
  document.getElementById("AddAddLebihBayar").value = '0.00'
  document.getElementById("AddAddDibayar").disabled = true
  document.getElementById("AddAddKurangBayar").disabled = true
  document.getElementById("AddAddLebihBayar").disabled = true
  if (Number(editDibayar) > 0) {

    document.getElementById("AddAddDibayar").disabled = false
    setNum("AddAddDibayar", editDibayar)

  } else if (Number(editKL) > 0) {
    document.getElementById("AddAddKurangBayar").disabled = false
    setNum("AddAddKurangBayar", editKL)

  } else {
    document.getElementById("AddAddLebihBayar").disabled = false
    setNum("AddAddLebihBayar", editLB)

  }
  $('.showhideitem').show()



}


function refreshTableKoreksi ( nobukti , nodpp) {

let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('pelunasanpiutangdppspdetailpenerimaan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti,
      nodpp

    },
    success: function(res) {
      console.log(res)
      penerimaanHeader = res.header[0]
      listPenerimaan = res.detail
      if (!listPenerimaan.length) {
        resRefresh = 0

        $(".showhideitem").hide()
        $(".mainpage").hide()
        $("#page1").show()

        alertify.warning("Data habis")
        return
      }

      let xxx = 0
      if ( Number(res.X[0].Dibayar) ) {
        xxx += Number(res.X[0].Dibayar)
      }
      if ( Number(res.X[0].LB)) {
        xxx += Number(res.X[0].LB)
      }

      document.getElementById("input_add_nobkmbbm").value = res.header[0].NoDPP
      document.getElementById("input_add_nobukti").value = res.header[0].NoBukti

      document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal , '-')
      document.getElementById("input_add_kodecust").value = res.header[0].KODECUSTSUPP
      document.getElementById("input_add_namacust").value = res.header[0].NamaCustSupp
      document.getElementById("input_add_valas").value = res.detail[0].Valas
      let dibayarx = parseFloat(xxx).toFixed(2)
      let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
      console.log(dibayarx , jumlahx)
      setNum("input_add_dibayar", xxx)
      setNum("input_add_jumlah", res.header[0].Debet ? res.header[0].Debet : 0)
      setNum("input_add_sisa", Number(jumlahx) - Number(xxx))
      let totaldibayarx = 0
      let totallbx = 0
      let totalklx = 0
      let rowTable = ``
      res.detail.forEach((item, i) => {
         totaldibayarx += Number(item.DIBAYAR)
         totallbx += Number(item.LB)
         totalklx += Number(item.KL)

        rowTable += `
          <tr>
            <td>${item.NamaKasBank}</td>
            <td>${item.NOFAKTUR}</td>
            <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00' }</td>
            <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00' }</td>
            <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00' }</td>
            <td>${item.perkiraan}</td>
            <td>${item.KodeCustSuppD}</td>
            <td>${item.NamaCustSuppD ? item.NamaCustSuppD : ''}</td>
            <td class="text-center">
              <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>
            </td>
          </tr>

        `
      });

      rowTable += `
        <tr>
          <td colspan=2 class="text-right">Total:</td>
          <td class="text-right">${formatAngka(parseFloat(totaldibayarx).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(totallbx).toFixed(2))}</td>
          <td class="text-right">${formatAngka(parseFloat(totalklx).toFixed(2))}</td>
          <td colspan=4 ></td>
        </tr>
      `


      document.getElementById("addTableData").innerHTML = rowTable
      urutTrans = res.detail[0].UrutDPP





      console.log('a')
      resRefresh = 1



    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })
}


function buttonDetail (nobukti , tipe = 0 , nodpp = '') {
  console.log('buttonDetail')
  console.log(nobukti , tipe , nodpp )
  resRefresh = 0
  // let akses = $("#akses_iskoreksi").val();
  // tipeform = 'edit'
  // if (!Number(akses)) {
  //   alertify.warning('No access')
  //   return
  // }


  // document.getElementById("input_add_tanggal").disabled = true

  console.log("buttonDetail")





  let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('pelunasanpiutangdppspdetailpenerimaan') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        nodpp

      },
      success: function(res) {
        console.log(res)
        // penerimaanHeader = res.header[0]
        // listPenerimaan = res.detail
        document.getElementById("input_detail_nobkmbbm").value = res.header[0].NoDPP
        document.getElementById("input_detail_nobukti").value = res.header[0].NoBukti
        let xxx = 0
        if ( Number(res.X[0].Dibayar) ) {
          xxx += Number(res.X[0].Dibayar)
        }
        if ( Number(res.X[0].LB)) {
          xxx += Number(res.X[0].LB)
        }
        document.getElementById("input_detail_tanggal").value = formatDate(res.header[0].Tanggal , '-')
        document.getElementById("input_detail_kodecust").value = res.header[0].KODECUSTSUPP
        document.getElementById("input_detail_namacust").value = res.header[0].NamaCustSupp
        document.getElementById("input_detail_valas").value = res.detail[0].Valas
        let dibayarx = parseFloat(xxx).toFixed(2)
        let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
        console.log(dibayarx , jumlahx)
        setNum("input_detail_dibayar", xxx)
        setNum("input_detail_jumlah", res.header[0].Debet ? res.header[0].Debet : 0)
        setNum("input_detail_sisa", Number(jumlahx) - Number(xxx))

        let rowTable = ``
        res.detail.forEach((item, i) => {
          rowTable += `
            <tr>
              <td>${item.NamaKasBank}</td>
              <td>${item.NOFAKTUR}</td>
              <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00' }</td>
              <td>${item.perkiraan}</td>
              <td>${item.KodeCustSuppD}</td>
              <td>${item.NamaCustSuppD ? item.NamaCustSuppD : ''}</td>

            </tr>

          `
        });

        document.getElementById("detailTableData").innerHTML = rowTable
        // urutTrans = res.detail[0].UrutDPP




        $(".page3showhide").hide()

        if (tipe) {
          $(".otorisasishowhide").show()
        } else {
          $(".detailshowhide").show()
        }



        $(".mainpage").hide()
        $("#page3").show()
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}


function buttonKoreksi (nobukti , nodpp) {
  resRefresh = 0
  let akses = $("#akses_iskoreksi").val();
  tipeform = 'edit'
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  document.getElementById("input_add_tanggal").disabled = true







  let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('pelunasanpiutangdppspdetailpenerimaan') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti,
        nodpp

      },
      success: function(res) {

        console.log(res)
        penerimaanHeader = res.header[0]
        listPenerimaan = res.detail

        if ( res.detail[0].IsOtorisasi1 == 1) {
          alertify.warning("Data sudah diotorisasi")
          return
        }
        document.getElementById("input_add_nobkmbbm").value = res.header[0].NoDPP
        document.getElementById("input_add_nobukti").value = res.header[0].NoBukti
        let xxx = 0
        if ( Number(res.X[0].Dibayar) ) {
          xxx += Number(res.X[0].Dibayar)
        }
        if ( Number(res.X[0].LB)) {
          xxx += Number(res.X[0].LB)
        }
        document.getElementById("input_add_tanggal").value = formatDate(res.header[0].Tanggal , '-')
        document.getElementById("input_add_kodecust").value = res.header[0].KODECUSTSUPP
        document.getElementById("input_add_namacust").value = res.header[0].NamaCustSupp
        document.getElementById("input_add_valas").value = res.detail[0].Valas
        let dibayarx = parseFloat(xxx).toFixed(2)
        let jumlahx = res.header[0].Debet ? parseFloat(res.header[0].Debet ).toFixed(2) : '0.00'
        console.log(dibayarx , jumlahx)
        setNum("input_add_dibayar", xxx)
        setNum("input_add_jumlah", res.header[0].Debet ? res.header[0].Debet : 0)
        setNum("input_add_sisa", Number(jumlahx) - Number(xxx))
        let totaldibayarx = 0
        let totallbx = 0
        let totalklx = 0
        let rowTable = ``
        res.detail.forEach((item, i) => {
          totaldibayarx += Number(item.DIBAYAR)
          totallbx += Number(item.LB)
          totalklx += Number(item.KL)
          rowTable += `
            <tr>
              <td>${item.NamaKasBank}</td>
              <td>${item.NOFAKTUR}</td>
              <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00' }</td>
              <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00' }</td>
              <td>${item.perkiraan}</td>
              <td>${item.KodeCustSuppD}</td>
              <td>${item.NamaCustSuppD ? item.NamaCustSuppD : ''}</td>
              <td class="text-center">
                <button class="btn btn-success btn-sm" type="button" onclick="buttonEditItem('${i}' )"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteItem('${i}' )"><i class="bi bi-trash"></i></button>
              </td>
            </tr>

          `
        });

        rowTable += `
          <tr>
            <td colspan=2 class="text-right">Total:</td>
            <td class="text-right">${formatAngka(parseFloat(totaldibayarx).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(totallbx).toFixed(2))}</td>
            <td class="text-right">${formatAngka(parseFloat(totalklx).toFixed(2))}</td>
            <td colspan=4 ></td>
          </tr>
        `

        document.getElementById("addTableData").innerHTML = rowTable
        urutTrans = res.detail[0].UrutDPP









        $(".showhideitem").hide()
        $(".mainpage").hide()
        $("#page2").show()
      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

}


function buttonAdd (nobukti) {
  let akses = $("#akses_istambah").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let itemOut = (dataOutstanding || []).find(i => i.NOBUKTI === nobukti)
  if (itemOut && outCustNoName(itemOut)) {
    alertify.warning('Customer masih NO NAME - koreksi customer dulu sebelum diproses')
    return
  }
  tipeform = 'add'
  setNewNoBukti()
  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_add_tanggal").valueAsDate = new Date()

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pelunasanpiutangdppspdetailoutstanding') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res , '!!!')

      listOutstanding = res
      document.getElementById("input_add_nobkmbbm").value = listOutstanding[0].NOBUKTI
      document.getElementById("input_add_valas").value = listOutstanding[0].Valas
      // document.getElementById("input_add_dibayar").value = listOutstanding[0].Dibayar ? parseFloat(listOutstanding[0].Dibayar).toFixed(2) : '0.00'

      let xxx = 0
      if ( Number(listOutstanding[0].Dibayar) ) {
        xxx += Number(listOutstanding[0].Dibayar)
      }
      if ( Number(listOutstanding[0].LB)) {
        xxx += Number(listOutstanding[0].LB)
      }
      setNum("input_add_dibayar", xxx)

      setNum("input_add_jumlah", listOutstanding[0].JumlahRp ? listOutstanding[0].JumlahRp : 0)
      setNum("input_add_sisa", listOutstanding[0].Sisa ? listOutstanding[0].Sisa : 0)
      document.getElementById("input_add_kodecust").value = listOutstanding[0].CustSuppL
      document.getElementById("input_add_namacust").value = listOutstanding[0].namaCustSupp
      urutTrans = listOutstanding[0].urutTrans
      document.getElementById("addTableData").innerHTML = `<tr >

          <td colspan=9 class="text-center">Belum ada data</td>

    </tr>`







      $(".showhideitem").hide()
      $(".mainpage").hide()
      $("#page2").show()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })





  return

}

function buttonAddAddItem () {
    let value = $("#input_add_kodeperkiraan").val();
    if(!value) {
      alertify.warning("Pilih perkiraan terlebih dahulu")
      return
    }

    $('#buttonSubmitAddAdd').show();
    $('#buttonSubmitAddEdit').hide();

    $('#labelAddAddItem').show();
    $('#labelAddEditItem').hide();
    $('#rowCustsupp').hide();
    // $('#rowCustsupp').show();
    // document.getElementById("buttonSubmitAddAdd").style.display = "block";
    // // document.getElementById("buttonSubmitAddEdit").style.display = "none";
    // document.getElementById("labelAddAddItem").style.display = "block";
    // document.getElementById("labelAddEditItem").style.display = "none";
    unlockFormAddAdd()
    $('.showhideitem').hide();
    cleanFormAddAdd()
    $('#formAddAdd').show();

}

function buttonAddEditItem (i) {
  tempBarangAddEdit = listData[i]
  console.log(tempBarangAddEdit)
  lockFormAddAdd()
  cleanFormAddAdd()
  let value = $("#input_add_transaksi").val();

  console.log(value, tempBarangAddEdit.KodeL )
  if (value == 'BBM' && tempBarangAddEdit.KodeL == 'UHT' ) {
    document.getElementById("AddAddJumlah").disabled = true
  } else {
    document.getElementById("AddAddJumlah").disabled = false
  }

  document.getElementById("AddAddKodeDevisi").value = tempBarangAddEdit.Devisi
  document.getElementById("AddAddNamaDevisi").value = tempBarangAddEdit.NamaDevisi

  document.getElementById("AddAddValas").value = tempBarangAddEdit.Valas
  document.getElementById("AddAddKurs").value = parseFloat(tempBarangAddEdit.Kurs).toFixed(2)

  document.getElementById("AddAddLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.Perkiraan : tempBarangAddEdit.Lawan
  document.getElementById("AddAddKeteranganLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.NamaPerkiraan : tempBarangAddEdit.NamaLawan

  console.log(tempBarangAddEdit.Debet)
  document.getElementById("AddAddJumlah").value = parseFloat(tempBarangAddEdit.Debet).toFixed(2)
  document.getElementById("AddAddKeterangan").value = tempBarangAddEdit.Keterangan
  document.getElementById("AddAddKeteranganDetail").value = tempBarangAddEdit.KetDetail

  document.getElementById("AddAddKodeDepartemen").value = tempBarangAddEdit.KodeBag
  document.getElementById("AddAddNamaDepartemen").value = tempBarangAddEdit.NMDEP


  $('#buttonSubmitAddAdd').hide();
  $('#buttonSubmitAddEdit').show();

  $('#labelAddAddItem').hide();
  $('#labelAddEditItem').show();



  $('.showhideitem').hide();
  $('#formAddAdd').show();

}


function buttonCloseForm () {
  $('.mainpage').hide();
  // $('#page2').hide();
  $('#page1').show();

}


function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('pelunasanpiutangdppdetailCetak') !!}",
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
    for (let i = 0; i < dataPrint.length; i+=10) {
      let tempArray = dataPrint.slice(i,i+10)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].TANGGAL.split(' ')[0];

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
        hdr = `<table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:10px;">
            <thead>
            <!-- JUDUL -->
              <tr>
                <td colspan="4" rowspan="2" style="border:1px solid; text-align:center; font-weight:bold; font-size:18px;">
                  BUKTI PENERIMAAN DPP
                </td>
                <td style="border:1px solid; width:15%;">No. Bukti</td>
                <td style="border:1px solid; width:25%;">${dataPrint[0].NoBukti}</td>
              </tr>

              <!-- TANGGAL -->
              <tr>
                <td style="border:1px solid;">Tanggal</td>
                <td style="border:1px solid;">${tanggalOnly}</td>
              </tr>

              <!-- TERIMA DARI -->
              <tr>
                <td colspan="4" style="border:1px solid;">Terima Dari : ${dataPrint[0].NAMACUSTSUPP ? dataPrint[0].NAMACUSTSUPP : '-'}</td>
                <td colspan="2" style="border:1px solid;">
                  ${dataPrint[0].KETATAS}
                </td>
              </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 10%">KODE</td>
                    <td rowspan="2" class="text-center" style="width: 20%">NAMA PERKIRAAN</td>
                    <td rowspan="2" class="text-center" style="width: 40%">KETERANGAN</td>
                    <td rowspan="2" class="text-center" style="width: 10%">JUMLAH</td>
                    <td rowspan="2" class="text-center" style="width: 5%">LNS</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.DIBAYAR) {
        grandTotalJumlah += Number(item.DIBAYAR) || 0;
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
               style="width: 1%; ">${z+1}</td>
         <td class="text-align: left"
               style="width: 10%;  ">${itemSub.perkiraan}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.keterangan}</td>
         <td class="text-align: left"
               style="width: 40%;">${itemSub.NOFAKTUR} ${itemSub.TGLTITIP} No.TT: ${itemSub.Nott}</td>
         <td style="width: 10%; text-align: right;">
            ${itemSub.DIBAYAR 
              ? Number(itemSub.DIBAYAR).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
          <td class="text-align: left"
               style="width: 5%;"></td>
         </tr>`;

           z++;

        });

        // TAMBAHAN
        let sisaRow = maxRow - item.length;

        for (let k = 0; k < sisaRow; k++) {
          tempPrintStr += `
          <tr>
            <td style="border-top:none; border-bottom:none;">&nbsp;</td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
          </tr>`;
        }

        tempPrintStr += `
        <tr>
          <td colspan="4" style="border:1px solid; padding:5px; font-weight:bold;">
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            Total :
          </td>
          <td style="border:1px solid; text-align:right; font-weight:bold;">
            ${grandTotalJumlah.toLocaleString('id-ID', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            })}
          </td>
        </tr>`;

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


         <table
            class="detail-spb-table mb-2"
            style="
              width: 100%;
              margin-top: 20px;
              font-family: sans-serif;
              font-size: 10px;
              border-collapse: collapse;">
            <tr>
              <td style="width: 20%; border: 1px solid; text-align: center;">Menyetujui</td>
              <td style="width: 20%; border: 1px solid; text-align: center;">Mengetahui</td>
              <td style="width: 20%; border: 1px solid; text-align: center;">Kasir</td>
              <td style="width: 20%; border: 1px solid; text-align: center;">Penerima</td>
            </tr>

            <tr style="height: 60px;">
              <td style="border: 1px solid;"></td>
              <td style="border: 1px solid;"></td>
              <td style="border: 1px solid;"></td>
              <td style="border: 1px solid;"></td>
            </tr>
            
          </table>
         </div>


         <div class="footer-print-date">
           <table class="m-0" style="width: 100% ; font-family: sans-serif;
           font-size: 10px ">
             <tr>
               <td class="no-border"></td>
               <td class="no-border text-right">Page ${i+1} of ${arrayDataPrint.length}</td>
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

function buttonBatalOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi DPP ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('pelunasanpiutangdppspbatalotorisasi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti

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


function formatAngkaX (angka) {
  if (!angka) {
    return '0.00'
  } else {
    return formatAngka(parseFloat(angka).toFixed(2))
  }

}

function toNum (v) {
  let n = parseFloat(String(v == null ? '' : v).replace(/,/g, ''))
  return isNaN(n) ? 0 : n
}
function setNum (id, v) {
  let el = document.getElementById(id)
  if (!el) return
  let n = toNum(v)
  if ($(el).data('autoNumeric')) {
    $(el).autoNumeric('set', n)
  } else {
    el.value = formatNumberDisplay(n.toFixed(2))
  }
}
function formatAngka (angkaString) {

  let tempAngka = angkaString.split('.')

  if (tempAngka[0][0] == '-') {
    let temp2=''

    let tempAngka1 = tempAngka[0].split('-')
    for (let i = 0; i < tempAngka1[1].length; i++) {
      if (i != 0 && i % 3 == 0) {
        temp2 = ',' + temp2
      }
      temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2
      // console.log(i, temp2)
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
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
    // console.log(i, temp1)
  }
  temp1 += '.' + tempAngka[1]
  return temp1
}


</script>

@endsection
