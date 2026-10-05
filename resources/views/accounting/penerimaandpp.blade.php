@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Penerimaan DPP')

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
     tersembunyi + modal filter) - sama seperti menu purchasing. --}}
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

#tabel_add_list_customer_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}

#tabel_add_list_customer_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_noinvoice_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_noinvoice_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_barang_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_barang_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_add_list_nobeli_filter{
  display: flex;
  align-items: flex-end;
  margin-bottom: -10px;
}
#tabel_add_list_nobeli_filter label input {
  width: 150px;
  border-radius: 10px;
  border: 1px solid #ccc;
  box-shadow: none;
  font-size: 0.65rem;
}

#tabel_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }


#tabel_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }
</style>
{{-- end tampilan search bar 1 --}}

{{-- tampilan search bar 2 --}}
<style>
#tabel2_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel2_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel2_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel2_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>

<style>
#tabel3_filter {
    display: flex;
    align-items: flex-end;
    margin-top: 8px;
    margin-right: 10px;
    margin-bottom: -10px;
  }

#tabel3_filter label input {
    width: 150px;
    padding: 5px 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: none;
    font-size: 0.65rem;
  }

#tabel3_filter label {
    font-weight: 600;
    font-size: 0.9rem;
    color: #333;
  }

#tabel3_filter input:focus {
    border-color: #007bff;
    outline: none;
  }
</style>

{{-- ==========================================================================
     Tampilan baru Penerimaan DPP - menyalin pola menu purchasing
     (pembelianpermintaandebetnote.blade.php).

     Layout accounting.newmaster tidak mendefinisikan .data-table seperti
     newmasterTest, jadi gaya dasar tabelnya ditulis di sini supaya halaman ini
     tidak bergantung pada layout. Semua var CSS diberi nilai cadangan.
     ========================================================================== --}}
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
#tabelPdpp, #tabel { min-width: 100%; }

/* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
#tabelPdpp td:first-child:not([colspan]),
#tabel td:first-child:not([colspan]) { vertical-align: middle; }

#tabelPdpp td:first-child .po-aksi-wrap,
#tabel td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabelPdpp td:first-child .btn,
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

#tabelPdpp td:first-child .btn:hover,
#tabel td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabelPdpp td:first-child .btn-success, #tabel td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabelPdpp td:first-child .btn-warning, #tabel td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabelPdpp td:first-child .btn-primary, #tabel td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabelPdpp td:first-child .btn-danger,  #tabel td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabelPdpp td:first-child .btn-info,    #tabel td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

#addInvoiceTable td .btn,
#giroModalTable td .btn,
#tabel_add_list_modalx td .btn {
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
#addInvoiceTable td .btn:hover,
#giroModalTable td .btn:hover,
#tabel_add_list_modalx td .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}
#addInvoiceTable td .btn-success, #giroModalTable td .btn-success, #tabel_add_list_modalx td .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#addInvoiceTable td .btn-danger,  #giroModalTable td .btn-danger,  #tabel_add_list_modalx td .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

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

/* Tombol Save di modal Change Invoice - hijau lembut, bukan hijau bootstrap terang. */
#buttonSaveLB {
  background-color: #e7f7ed;
  border-color: #cdebd7;
  color: #16a34a;
  box-shadow: none;
}
#buttonSaveLB:hover,
#buttonSaveLB:focus { background-color: #d7f0e1; border-color: #b6e0c6; color: #15803d; }

/* ---------- Tabel Invoice/Giro/Rekap di page2 (mengikuti #addTable pengajuandpp) ---------- */
#addInvoiceTable thead th,
#addGiroTable thead th,
#addRekapTable thead th {
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
#addRekapTable tbody tr:nth-of-type(odd) { background-color: #fbfbfc; }
#addInvoiceTable tbody tr:hover,
#addGiroTable tbody tr:hover,
#addRekapTable tbody tr:hover { background-color: #f5f3ff; }

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
</style>
@endsection


@section('content')

{{-- Logo untuk cetakan. Dulu diletakkan di @section('css') sehingga ikut tercetak di
     dalam <head>; sebuah <div> di dalam <head> memaksa browser menutup <head> lebih
     awal dan memulai <body> di situ. Ditaruh di @section('content') seperti halaman
     purchasing (lihat newpo.blade.php). --}}
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >
  {{-- Baris judul lama (dengan margin-top:-30px yang membuat kartu menempel ke bar
       atas) sudah tidak dipakai: judulnya pindah ke bar atas lewat
       @section('page-title'), dan jarak ke bar atas diatur #content di blok <style>
       - sama seperti halaman purchasing. --}}
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
          Outstanding DPP
        </a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false">
          Penerimaan DPP
        </a>
        {{-- Tab "Penerimaan DPP Sudah Otorisasi" dihapus: sudah & belum otorisasi kini
             satu tabel, disaring lewat modal Filter (lihat #modalFilterPdpp). --}}
      </div>
    </div>
  </div>
<div class="card">
<div class="card-body" style="padding:0;">
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    {{-- Toolbar sama seperti tab Penerimaan DPP: periode (tanggal 1 s/d akhir bulan
         periode kerja), kotak cari, jumlah baris per halaman, dan tombol Filter
         (lihat #modalFilterOut). --}}
    <div class="po-toolbar">
      <div class="po-filter-wrap">
        <label>Periode</label>
        <input type="date" class="po-filter-inp" id="outTglAwal" value="{!! $outTglAwal !!}">
        <span class="po-filter-sep">s/d</span>
        <input type="date" class="po-filter-inp" id="outTglAkhir" value="{!! $outTglAkhir !!}">
      </div>
      <input type="search" id="outSearch" class="po-search-inp" placeholder="Cari data">
      {{-- Jumlah baris per halaman - lihat outIkatPanjangHalaman(). --}}
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
      <button class="po-btn-filter" type="button" id="outBtnFilter" onclick="$('#modalFilterOut').modal('show')">
        <i class="bi bi-funnel"></i> Filter
      </button>
    </div>

    {{-- #rtBarOut diisi lewat JS oleh ReportTable - lihat pdppInitReportTableSekali(). --}}
    <div id="rtBarOut"></div>

    <table id="tabel" class="data-table po-aksi-hover">
      <thead id="tabel_header_out" class="text-center">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Actions</th>
          <th style="padding: 4px 12px;" scope="col">No. Bon</th>
          <th style="padding: 4px 12px;" scope="col">Tanggal</th>
          <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
          <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
          <th style="padding: 4px 12px;" scope="col">Penagih</th>
        </tr>
      </thead>

      <tbody id="tabel_data" class="text-left">
        {{-- Baris + judul kolom digambar renderTabelOutstanding() lewat JS dari data
             dan konfigurasi kolom yang dikirim loadAll(). --}}
      </tbody>
    </table>
  </div>

  {{-- Satu tabel gabungan: belum maupun sudah diotorisasi. Penyaringan otorisasi
       dikerjakan di browser lewat modal Filter (dulu dua tab terpisah). --}}
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

    <div class="po-toolbar">
      <div class="po-filter-wrap">
        <label>Periode</label>
        <input type="date" class="po-filter-inp" id="pdppTglAwal" value="{!! $pdppTglAwal !!}">
        <span class="po-filter-sep">s/d</span>
        <input type="date" class="po-filter-inp" id="pdppTglAkhir" value="{!! $pdppTglAkhir !!}">
      </div>
      <input type="search" id="pdppSearch" class="po-search-inp" placeholder="Cari data">
      {{-- Jumlah baris per halaman - lihat pdppIkatPanjangHalaman(). --}}
      <div class="po-len-wrap">
        <label for="pdppLen">Tampilkan</label>
        <select id="pdppLen" class="po-len-inp">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="-1">Semua</option>
        </select>
      </div>
      <button class="po-btn-filter" type="button" id="pdppBtnFilter" onclick="$('#modalFilterPdpp').modal('show')">
        <i class="bi bi-funnel"></i> Filter
      </button>
    </div>

    {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat pdppInitReportTableSekali(). --}}
    <div id="rtBar"></div>

    <table id="tabelPdpp" class="data-table po-aksi-hover">
      <thead id="tabel_header_pdpp" class="text-center">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Actions</th>
          <th style="padding: 4px 12px;" scope="col">No Bukti</th>
          <th style="padding: 4px 12px;" scope="col">Tanggal</th>
          <th style="padding: 4px 12px;" scope="col">Kode Cust</th>
          <th style="padding: 4px 12px;" scope="col">Nama Cust</th>
          <th style="padding: 4px 12px;" scope="col">DPP</th>
          <th style="padding: 4px 12px;" scope="col">Dibayar</th>
          <th style="padding: 4px 12px;" scope="col">LB</th>
          <th style="padding: 4px 12px;" scope="col">KL</th>
        </tr>
      </thead>
      <tbody id="tabel2_data" class="text-left">
        {{-- Baris digambar renderTabelPdpp() lewat JS, supaya susunan kolom hasil
             geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
      </tbody>
    </table>

  </div>




</div>
</div>
</div>


</div>
</div>







<!-- modal filter otorisasi (menggantikan tab "Penerimaan DPP Sudah Otorisasi") -->
<div class="modal fade rt-filter" id="modalFilterPdpp">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Penerimaan DPP
          <span class="rt-active-badge" id="pdppFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPdpp').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="pdppModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="pdppModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
            <div>
              <label class="rt-field-label" for="pdppModalPenagih">Penagih</label>
              <select class="rt-native" id="pdppModalPenagih">
                <option value="SEMUA">Semua</option>
                @for ($i = 0; $i < count($penagih); $i++)
                  <option value="{{ $penagih[$i]->Penagih }}">{{ $penagih[$i]->Penagih }}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="pdppResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterPdpp').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="pdppTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter otorisasi -->

<!-- modal filter tab Outstanding DPP -->
<div class="modal fade rt-filter" id="modalFilterOut">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Outstanding DPP
          <span class="rt-active-badge" id="outFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterOut').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="outModalPenagih">Penagih</label>
              <select class="rt-native" id="outModalPenagih">
                <option value="SEMUA">Semua</option>
                @for ($i = 0; $i < count($penagih); $i++)
                  <option value="{{ $penagih[$i]->Penagih }}">{{ $penagih[$i]->Penagih }}</option>
                @endfor
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="outResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterOut').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="outTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter tab Outstanding DPP -->

<div id="page2" style="display: none" class="mainpage container-fluid" >

  <div class="row">
    <div class="col-8 text-left">
      <h2></h2>
    </div>
    <div class="col-4 text-right">
      <button type="button" class="btn btn-dpp-tutup" onclick="buttonCloseForm()">Close</button>
    </div>
  </div>

  <div id= "" class="">
  <div id="formBsGrid">



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

        <div class="col-md-3">
          <div class="row">


        <div class="col-md-4">
          <div class="form-group">
            <label>No DPP</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_nodpp" placeholder="" disabled>
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
            <label>Customer</label>
          </div>
        </div>
        <div class="col-md-8">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_kodecust" placeholder="" disabled>
          </div>
        </div>
      </div>
      <div class="row" style="margin-top: -10px">
        <div class="col-md-12">
          <div class="form-group">
            <input type="text" class="form-control" id="input_add_namacust" placeholder="" disabled>
          </div>

        </div>

      </div>

        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Valas</label>
            </div>
            </div>

            <div class="col-md-8">
              <div class="input-group form-group">
                {{-- Tampilannya dropdown, tapi tetap disabled: valas diturunkan dari data
                     DPP, bukan dipilih user. Perilakunya sama persis dengan input teks
                     yang lama - nilainya diisi lewat pdppSetValas(). --}}
                <select id="input_add_valas" class="form-control" disabled>
                  @for ($i = 0; $i < count($listValas); $i++)
                    <option value="{{ trim($listValas[$i]->KodeVls) }}">{{ trim($listValas[$i]->KodeVls) }} - {{ trim($listValas[$i]->NamaVls) }}</option>
                  @endfor
                </select>

              </div>
            </div>
          </div>

        </div>
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Penagih</label>
            </div>
            </div>
            <div class="col-md-8">
              <div class="input-group form-group">
                <select id="input_add_penagih" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                        <!-- <option value='BKK' selected>BKK</option>
                        <option value='BKM' >BKM</option> -->
                        <option selected value='' ></option>

                        @for ($i = 0; $i < count($penagih); $i++)
                          <option value='{{ $penagih[$i]->Penagih }}' >{{ $penagih[$i]->Penagih }}</option>

                          @endfor

                      </select>

              </div>
            </div>
          </div>

        </div>



      </div>


      <div class="row" style="margin-top: -10px">







      </div>


      </div>



<div class="container-fluid">
  <hr/>

</div>



  <div class="container-fluid mt-4" style=" padding:0; margin:0;">
    <div class="row">
      <div class="col-xl-7">
        <div class="col-xl-12 mt-2 text-right">
        <button id="buttonAddItem" type="button" class="btn btn-primary" onclick="buttonAddItem()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;" >+ Tambah</button>
      </div>
        <table id="addInvoiceTable" class="mt-4 data-table"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Kas/Bank/Giro</th>
              <th style="padding: 4px 12px;" scope="col">Faktur</th>
              <th style="padding: 4px 12px;" scope="col">diBayar</th>
              <th style="padding: 4px 12px;" scope="col">lebihBayar</th>
              <th style="padding: 4px 12px;" scope="col">kurangBayar</th>
              <th style="padding: 4px 12px;" scope="col">Perkiraan</th>


              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addInvoiceTableData" class="" >
            <tr >

                <td colspan=7 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
      </div>
      <div class="col-xl-5">
        <div class="col-xl-12 mt-2 text-right">
        <button id="buttonAddGiro" type="button" class="btn btn-primary" onclick="buttonAddGiro()" class="btn btn-secondary" style="height: 30px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;" >+ Giro</button>
      </div>
        <table id="addGiroTable" class="data-table mt-4"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th style="padding: 4px 12px;" scope="col">No Giro</th>
              <th style="padding: 4px 12px;" scope="col">Bank</th>
              <th style="padding: 4px 12px;" scope="col">Tgl Giro</th>
              <th style="padding: 4px 12px;" scope="col">Jumlah</th>


            </tr>
          </thead>


          <tbody id="addGiroTableData" class="" >
            <tr >

                <td colspan=4 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>

      </div>

    </div>
    <div class="row">
      <div class="col-md-7">
        <table id="addRekapTable" class="data-table"  >
          <thead class="text-center bg-primary text-white">
            <tr>
              <th colspan=4 style="padding: 4px 12px;" scope="col">Rekap Bayar</th>

            </tr>
          </thead>


          <tbody id="addRekapTableData" class="" >
            <tr >

                <td style="width: 500px">TES</td>
                <td class="text-right">3.000.000.000</td>
                <td class="text-right">2.000.000.00</td>
                <td class="text-right">888888888,88</td>
          </tr>
          <tr>
            <td class="text-right">Total:</td>
            <td class="text-right">3.000.000.000</td>
            <td class="text-right">2.000.000.00</td>
            <td class="text-right">888888888,88</td>
          </tr>

          </tbody>


        </table>
      </div>

    </div>

  </div>



  <div id="formAddEdit" class="container-fluid showhideitem">

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
              <input id="AddNoFaktur" type="text" class="form-control" disabled>
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
              <input id="AddDibayar" type="text" inputmode="decimal" class="form-control text-right format-number" value="">
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
            <label>LBayar</label>
          </div>
          </div>
          <div class="col-md-8">
            <div class="input-group form-group">
              <input id="AddLebihBayar" type="text" inputmode="decimal"  value="1.00" class="text-right form-control format-number">
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
            <label>KBayar</label>
          </div>
          </div>
          <div class="col-md-8">
            <div class="input-group form-group">
              <input id="AddKurangBayar" type="text" inputmode="decimal" class="form-control text-right" value="" disabled>
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
              <input id="AddPerkiraan" type="text" class="form-control" value="" disabled>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group form-group">
              <input id="AddNamaPerkiraan" type="text" class="form-control" value="" disabled>
            </div>
          </div>
        </div>
      </div>
    </div>












</div>


  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-secondary" onclick="buttonAddBatal()" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Batal</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" style="height: 30px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;">Submit</button>

  </div>

</div>

  </div>
</div>

    </div>

  </div>{{-- /#formBsGrid --}}
  </div>




<!-- start modal add -->
<div class="modal fade" id="formGiro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">Giro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">


      <div id="formBsGrid">
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">
              <div class="col-md-12" style="overflow-x: auto; padding:0; margin:0;">
                <table id="giroModalTable" class="data-table tabel-modal-pdpp"  >
                  <thead class="text-center">
                    <tr>
                      <th style="padding: 4px 12px;" scope="col">No Giro</th>
                      <th style="padding: 4px 12px;" scope="col">Bank</th>
                      <th style="padding: 4px 12px;" scope="col">Tgl Giro</th>
                      <th style="padding: 4px 12px;" scope="col">Valas</th>
                      <th style="padding: 4px 12px;" scope="col">Kurs</th>
                      <th style="padding: 4px 12px;" scope="col">Jumlah</th>
                      <th style="padding: 4px 12px;" scope="col">Actions</th>

                    </tr>
                  </thead>


                  <tbody id="giroModalTableData" class="" >
                    <tr >


                        <td colspan=7 class="text-center">Belum ada data</td>
                  </tr>

                  </tbody>


                </table>
              </div>

              <div class="col-xl-12 mt-2 text-right">
              <button id="buttonAddGiroAdd" type="button" class="btn btn-primary" onclick="buttonAddGiroAdd()" class="btn btn-secondary" style="height: 30px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;" >+ Tambah</button>
            </div>

          </div>









          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

            </div>


            <div id="formGiroAdd" class="container-fluid showhideitemgiro" style="">
              <div class="row">

              <div class="col-12">


              <hr/>
              <div class="row">
                <div class="col-md-12">
                  <h4 id="labelAddGiro" class="labelGiro">Add Giro</h4>
                  <h4 id="labelEditGiro" class="labelGiro">Edit Giro</h4>
                </div>
              </div>

              <div class="row" >
                <div class="col-xl-2">
                  <div class="row">


                    <div class="col-xl-12">
                      <div class="form-group">
                      <label>Bank</label>
                    </div>
                    </div>



                  </div>

                  <div class="row" style="margin-top: -15px">
                    <div class="col-xl-12">
                      <div class="input-group form-group">
                        <input id="input_giro_bank" type="text" class="form-control" >

                      </div>
                    </div>
                  </div>

                </div>



                <div class="col-md-2">
                  <div class="row">


                    <div class="col-md-12">
                      <div class="form-group">
                      <label>No Giro</label>
                    </div>
                    </div>







                  </div>

                  <div class="row" style="margin-top: -15px">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input id="input_giro_nogiro" type="text" class="form-control" >

                      </div>
                    </div>
                  </div>

                </div>


                <div class="col-md-2">
                  <div class="row">


                    <div class="col-md-12">
                      <div class="form-group">
                      <label>Tanggal</label>
                    </div>
                    </div>

                  </div>

                  <div class="row" style="margin-top: -15px">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input id="input_giro_tanggal" type="date" class="form-control" >

                      </div>
                    </div>
                  </div>

                </div>

                <div class="col-md-2">
                  <div class="row">


                    <div class="col-md-12">
                      <div class="form-group">
                      <label>Valas</label>
                    </div>
                    </div>







                  </div>
                  <div class="row" style="margin-top: -15px">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        {{-- Sama seperti #input_add_valas: dropdown hanya untuk tampilan,
                             tetap disabled karena valas giro ikut datanya. --}}
                        <select id="input_giro_valas" class="form-control" disabled>
                          @for ($i = 0; $i < count($listValas); $i++)
                            <option value="{{ trim($listValas[$i]->KodeVls) }}">{{ trim($listValas[$i]->KodeVls) }} - {{ trim($listValas[$i]->NamaVls) }}</option>
                          @endfor
                        </select>

                      </div>
                    </div>
                  </div>

                </div>

                <div class="col-md-2">
                  <div class="row">


                    <div class="col-md-12">
                      <div class="form-group">
                      <label>Kurs</label>
                    </div>
                    </div>







                  </div>

                  <div class="row" style="margin-top: -15px">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input id="input_giro_kurs" type="text" inputmode="decimal" class="form-control text-right format-number">

                      </div>
                    </div>
                  </div>

                </div>


                <div class="col-md-2">
                  <div class="row">


                    <div class="col-md-12">
                      <div class="form-group">
                      <label>Nilai Giro</label>
                    </div>
                    </div>







                  </div>

                  <div class="row" style="margin-top: -15px">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input id="input_giro_nilaigiro" type="text" inputmode="decimal" class="form-control text-right format-number" >

                      </div>
                    </div>
                  </div>

                </div>

              </div>

              <div class="row" style="margin-top: -10px">
                <div class="col-xl-4">
                  <div class="row">
                    <div class="col-xl-12">
                      <div class="form-group">
                        <label>Keterangan</label>
                      </div>

                    </div>

                  </div>
                  <div class="row" style="margin-top: -15px">
                    <div class="col-xl-12">


                    <div class="input-group form-group">
                      <input id="input_giro_keterangan" type="text" class="form-control" >

                    </div>
                    </div>
                  </div>

                </div>

              </div>














          </div></div>




            <div class="row mt-2" style="margin-top: 0">
              <div class="col-md-12 text-right mt-4">
                <button type="button" class="btn btn-secondary" onclick="buttonGiroBatal()" style="height: 30px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;">Batal</button>

                <button id="buttonSubmitAddGiro" type="button" onclick="submitAddGiro()" class="btn btn-primary" style="height: 30px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;">Submit Add</button>

                <button id="buttonSubmitEditGiro" type="button" onclick="submitEditGiro()" class="btn btn-primary" style="height: 30px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;">Submit</button>

            </div>

          </div>

            </div>




        </div>
      </div>{{-- /#formBsGrid --}}





      </div>


      <!-- <div class="modal-footer"> -->
        <!-- <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" id="buttonSubmitAdd" class="btn btn-chip-biru" onclick="submitAdd()">Submit</button>
        <button type="button" id="buttonSubmitEdit" class="btn btn-chip-biru" onclick="submitEdit()">SubmitE</button> -->
      <!-- </div> -->
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
                  {{-- Tidak ada kolom Actions: barisnya diklik langsung untuk memilih. --}}
                  <table id="perkiraanModalTable">
                    <thead>
                      <tr>
                        <th scope="col">Perkiraan</th>
                        <th scope="col">Nama</th>

                      </tr>
                    </thead>


                    <tbody id="perkiraanModalTableData" class="text-left" >
                    </tbody>


                  </table>
                </div>

            </div>









            <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

              </div>




          </div>





        </div>


        <div class="modal-footer">
          <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
          <button type="button" id="buttonSubmitAdd" class="btn btn-chip-biru d-none" onclick="submitAdd()">Submit</button>
          <button type="button" id="buttonSubmitEdit" class="btn btn-chip-biru d-none" onclick="submitEdit()">SubmitE</button>
        </div>
        </div>


        </div>

      </div>
    </div>



    <div class="modal fade" id="formProses" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <div class="col-xl-1">
                  <div class="form-group">
                    <label>Perkiraan</label>

                  </div>
                </div>

                  <div class="col-xl-2">
                    <div class="form-group input-group">
                      <input type="text" class="form-control" id="input_proses_perkiraan" placeholder="" disabled>


                    </div>
                  </div>
                  <div class="col-xl-2">
                    <div class="form-group input-group">
                      <input type="text" class="form-control" id="input_proses_namaperkiraan" placeholder="" disabled>


                    </div>
                  </div>

              </div>
              <div class="row">
                  <div class="col-xl-12" style="overflow-x: auto; padding:0; margin:0;">
                    <table id="prosesModalTable" class="data-table tabel-modal-pdpp"  >
                      <thead class="text-center">
                        <tr>
                          <th style="padding: 4px 12px;" scope="col">v</th>
                          <th style="padding: 4px 12px;" scope="col">Faktur</th>
                          <th style="padding: 4px 12px;" scope="col">N. Faktur</th>
                          <th style="padding: 4px 12px;" scope="col">Sudah Bayar</th>
                          <th style="padding: 4px 12px;" scope="col">Dibayar</th>
                          <th style="padding: 4px 12px;" scope="col">LB</th>
                          <th style="padding: 4px 12px;" scope="col">KL</th>

                        </tr>
                      </thead>


                      <tbody id="prosesModalTableData" class="" >
                        <tr >


                            <td colspan=7 class="text-center">Belum ada data</td>
                      </tr>

                      </tbody>


                    </table>
                  </div>

              </div>

                </div>




            </div>





          </div>


          <div class="modal-footer">
            <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
            <button type="button" id="buttonSubmitProses" class="btn btn-chip-biru" onclick="submitProses()">Submit</button>
          </div>
          </div>


          </div>

        </div>
      </div>


    <div class="modal fade" id="formPerkiraan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" style="">
        <div id="" class="modal-content ">

          <div id= "" class="">
          <div class="modal-header">


              <h5 class="modal-title" id="">Terima DPP</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>


          <div id="" class="">


          <div class="modal-body">

            <div class="container-fluid" >
              <div class="row">
                  <div class="col-md-12">
                    <table id="dppModalTable" class="data-table tabel-modal-pdpp"  >
                      <thead class="text-center">
                        <tr>
                          <th style="padding: 4px 12px;" scope="col">Terima</th>
                          <th style="padding: 4px 12px;" scope="col">Faktur</th>
                          <th style="padding: 4px 12px;" scope="col">Nilai Faktur</th>
                          <th style="padding: 4px 12px;" scope="col">Sudah Bayar</th>
                          <th style="padding: 4px 12px;" scope="col">Dibayar</th>
                          <th style="padding: 4px 12px;" scope="col">LB</th>

                          <th style="padding: 4px 12px;" scope="col">KL</th>

                        </tr>
                      </thead>


                      <tbody id="dppModalTableData" class="" >
                        <tr >


                            <td colspan=7 class="text-center">Belum ada data</td>
                      </tr>

                      </tbody>


                    </table>
                  </div>

              </div>









              <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

                </div>




            </div>





          </div>


          <div class="modal-footer">
            <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
            <button type="button" id="buttonSubmitAdd" class="btn btn-chip-biru" onclick="submitAdd()">Submit</button>
            <button type="button" id="buttonSubmitEdit" class="btn btn-chip-biru" onclick="submitEdit()">SubmitE</button>
          </div>
          </div>


          </div>

        </div>
      </div>



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
                    <button type="button" id="buttonAddKL" class="btn btn-primary" onclick="buttonAddKL()" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">+ KL</button>





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

                    <button id="buttonSubmitEditKL" type="button" onclick="submitEditKL()" class="btn btn-primary" style="height: 30px;
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    text-transform: uppercase;">Submit</button>


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


                  <table id="tabel_add_list_modalx" class="data-table tabel-modal-pdpp" style="overflow:auto; " >
                    <thead class="text-center" style="position: sticky;
                  top: 0;
                  z-index: 1;">
                      <tr>
                        <th style="padding: 4px 12px;" scope="col">Perkiraan</th>
                        <th style="padding: 4px 12px;" scope="col">Nama Perkiraan</th>
                        <th style="padding: 4px 12px;" scope="col">Kurang Bayar</th>
                        <th style="padding: 4px 12px;" scope="col">Actions</th>

                      </tr>
                    </thead>


                    <tbody id="tabel_data_add_list_modalx" class="text-left" >

                      <tr >

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
               "Tampilkan" + kotak Search bawaan DataTables (kotak cari lama
               #input_search_perkiraankllb dilepas), tombol Batal gaya pil. --}}
          <div class="modal fade picker-kas" id="formPerkiraanKLLB" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

                      {{-- Tidak ada kolom Actions: barisnya diklik langsung untuk memilih. --}}
                      <table id="tabel_add_list_perkiraankllb">
                        <thead>
                          <tr>
                            <th scope="col">Perkiraan</th>
                            <th scope="col">Nama</th>

                          </tr>
                        </thead>


                        <tbody id="tabel_data_add_list_perkiraankllb" class="text-left" >
                        {{-- Baris kosong = pilihan "tanpa perkiraan" (dulu tombol + dengan
                             argumen kosong). --}}
                        <tr class="pick-row" onclick="buttonAddPickPerkiraanLebihBayar('' , '')">
                          <td><em class="text-muted">(kosongkan)</em></td>
                          <td></td>
                      </tr>
                          @for ($i = 0; $i < count($tempListPerkiraanKLLB); $i++)
                          <tr class="pick-row" onclick="buttonAddPickPerkiraanLebihBayar('{{ $tempListPerkiraanKLLB[$i]->Perkiraan }}' , '{{ $tempListPerkiraanKLLB[$i]->Keterangan }}')">
                            <td>{{ $tempListPerkiraanKLLB[$i]->Perkiraan }}</td>
                            <td>{{ $tempListPerkiraanKLLB[$i]->Keterangan }}</td>


                        </tr>
                        @endfor
                        </tbody>


                      </table>
                    </div>
                      </div>
                      </div>

                  </div>

                </div>


                <div class="modal-footer">
                  <button type="button" class="btn picker-kas-batal" data-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-chip-biru d-none" onclick="submitAdd()">Submit</button>
                </div>
                </div>




                </div>







              </div>
            </div>

<!-- End modal add-->


  </div>


@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
<script type="text/javascript">


let toId = ''
let xinvoice = {}
let listKoreksiInvoice = []
let listKoreksiGiro = []
let listKoreksiRekap = []
let listProses = []
let tipeform = ''
let urutTrans = 0
let arrayKL = []
let indexEditKL = 0

let saveHeaderInvoice = {}
let saveHeaderIndex = 0

// ---------- Tumpukan modal ----------
// Hanya satu modal yang terlihat pada satu waktu. Saat modal anak dibuka, modal
// induk disembunyikan lewat class (bukan .modal('hide'), supaya isian form dan
// handler hidden.bs.modal milik induk tidak ikut terpicu). Saat anak ditutup -
// lewat Batal, tombol x, Esc, maupun klik backdrop - induk muncul lagi.
// Yang disimpan di tumpukan adalah elemennya, bukan id-nya, karena di halaman ini
// ada id modal yang kembar (#formPerkiraan).
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

        // Tabel Outstanding DPP (#tabel) digambar renderTabelOutstanding() setelah
        // loadAll() selesai, sama seperti tabel Penerimaan DPP di tab sebelah.

        // Tabel Penerimaan DPP (#tabelPdpp) digambar renderTabelPdpp() setelah loadAll()
        // selesai. #tabel3 sudah tidak ada - tabnya dilebur jadi filter otorisasi.
        pdppInitReportTableSekali()
        // Modal Perkiraan (#formPerkiraan & #formPerkiraanKLLB) disamakan dengan modal Perkiraan
        // menu Kas (picker-kas): kotak cari buatan sendiri diganti dropdown "Tampilkan" + kotak
        // Search bawaan DataTables. Tabel KL/LB digambar blade, jadi DataTables-nya dibuat sekali di
        // sini; tabel #perkiraanModalTable dibuat ulang tiap dibuka (lihat buttonAddItem()).
        // pdppIkatCariPerkiraanKLLB()
        // pdppIkatCariPerkiraanModal()
        pickerKasInit('tabel_add_list_perkiraankllb')

        // DataTables mengukur lebar kolom saat init. Tabel di tab yang awalnya
        // tersembunyi terukur 0, jadi lebarnya dihitung ulang begitu tabnya dibuka.
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
          if ($.fn.DataTable.isDataTable('#tabelPdpp')) {
            $('#tabelPdpp').DataTable().columns.adjust()
          }
          if ($.fn.DataTable.isDataTable('#tabel')) {
            $('#tabel').DataTable().columns.adjust()
          }
        })
        // Dulu isi tabel dirender server lewat perulangan blade; sekarang tabel Penerimaan
        // DPP butuh loadAll() saat halaman dibuka.
        loadAll()

      });

/* ==========================================================================
   Kedua tabel halaman ini memakai pola ReportTable (geser kolom + sembunyikan
   kolom + bar kolom tersembunyi), disalin dari pembelianpermintaandebetnote.blade.php.

   href dipatok, bukan diambil dari window.location - harus sama persis dengan
   PenerimaanDPPController::HREF dan ::HREF_OUT.
   ========================================================================== */
const PDPP_HREF = 'penerimaandpp'
const OUT_HREF  = 'penerimaandppoutstanding'

let pdppCart = []
let dataPdpp = []

let outCart = []

/* ReportTable membaca susunan kolom dari window.gcart_header dan menyimpan lewat
   window.g_href, jadi keduanya harus ditukar setiap kali tabel yang dipegang
   berganti. Penukarannya dipasang sebagai onActivate di ReportTable.init() dan
   dipanggil ReportTable.use() di awal tiap fungsi render. */
let rtTabelAktif = 'pdpp'

function pdppPakaiKolomPdpp () {
  rtTabelAktif = 'pdpp'
  window.g_href = PDPP_HREF
  window.gcart_header = pdppCart
}

function pdppPakaiKolomOutstanding () {
  rtTabelAktif = 'out'
  window.g_href = OUT_HREF
  window.gcart_header = outCart
}

function pdppBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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

function pdppKolomTampil () {
  return (pdppCart || []).filter(c => Number(c[2]) === 1)
}

function pdppKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function pdppFormatAngkaDes (nilai, des) {
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

function pdppRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return pdppFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

function pdppHeadHtml (cols) {
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

let pdppRtSudahInit = false

function pdppInitReportTableSekali () {
  if (pdppRtSudahInit || typeof ReportTable === 'undefined') { return }
  pdppRtSudahInit = true

  ReportTable.init({
    table      : '#tabelPdpp',
    bar        : '#rtBar',
    onChange   : renderTabelPdpp,
    onActivate : pdppPakaiKolomPdpp
  })

  // Tabel kedua: Outstanding DPP. ReportTable menyimpan satu instance per selektor
  // tabel dan berpindah sendiri mengikuti tabel yang disentuh user.
  ReportTable.init({
    table      : '#tabel',
    bar        : '#rtBarOut',
    onChange   : renderTabelOutstanding,
    onActivate : pdppPakaiKolomOutstanding
  })

  // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
  // roda gigi / pegangan geser ke penangan milik ReportTable.
  ;['tabel_header_pdpp', 'tabel_header_out'].forEach(function (idThead) {
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

function pdppPindahBar () {
  rtPindahBar('rtBar', 'tabelPdpp')
}

function pdppIkatSearch () {
  let input = document.getElementById('pdppSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabelPdpp').DataTable().search(input.value).draw()
  })
}

let pdppPanjangHalaman = 10
function pdppIkatPanjangHalaman () {
  let sel = document.getElementById('pdppLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(pdppPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    pdppPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabelPdpp').DataTable().page.len(pdppPanjangHalaman).draw()
  })
}

function pdppIkatPeriode () {
  let awal  = document.getElementById('pdppTglAwal')
  let akhir = document.getElementById('pdppTglAkhir')
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

/* Kotak cari modal Perkiraan (#formPerkiraanKLLB). Tabelnya digambar blade dan bukan
   DataTable, jadi penyaringannya menyembunyikan baris secara langsung. */
function pdppIkatCariPerkiraanKLLB () {
  let input = document.getElementById('input_search_perkiraankllb')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#tabel_data_add_list_perkiraankllb tr')
    baris.forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(cari) !== -1 ? '' : 'none'
    })
  })
}

// Kotak cari modal Perkiraan (#formPerkiraan) - barisnya digambar JS, bukan DataTable.
function pdppIkatCariPerkiraanModal () {
  let input = document.getElementById('input_search_perkiraanmodal')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let cari = input.value.toLowerCase()
    let baris = document.querySelectorAll('#perkiraanModalTableData tr')
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
function pdppSetValas (id, kode) {
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
let pdppFilterOtorisasi = 'SEMUA'
let pdppFilterPenagih = 'SEMUA'

function pdppOtorisasi (item) {
  return Number(item.IsOtorisasi1) !== 0 ? 'Sudah' : 'Belum'
}

function pdppUpdateFilterBadge () {
  let jml = (pdppFilterOtorisasi !== 'SEMUA' ? 1 : 0) + (pdppFilterPenagih !== 'SEMUA' ? 1 : 0)
  let badge = document.getElementById('pdppFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function pdppTerapkanFilter () {
  pdppFilterOtorisasi = $('#pdppModalOtorisasi').val() || 'SEMUA'
  pdppFilterPenagih   = $('#pdppModalPenagih').val() || 'SEMUA'
  pdppUpdateFilterBadge()
  $('#modalFilterPdpp').modal('hide')
  renderTabelPdpp()
}

function pdppResetFilter () {
  pdppFilterOtorisasi = 'SEMUA'
  pdppFilterPenagih = 'SEMUA'
  $('#pdppModalOtorisasi').val('SEMUA')
  $('#pdppModalPenagih').val('SEMUA')
  pdppUpdateFilterBadge()
  $('#modalFilterPdpp').modal('hide')
  renderTabelPdpp()
}

/* ---------- Simpan / muat susunan kolom ---------- */
window.g_href = PDPP_HREF
window.g_modeReport = 1
window.gcart_header = []

// Keduanya dipanggil ReportTable untuk tabel yang sedang aktif, jadi cart dan href
// yang dipakai mengikuti rtTabelAktif (lihat pdppPakaiKolom*()).
window.doSimpanHeader = function (href, mode) {
  let outAktif = (rtTabelAktif === 'out')
  let cart     = outAktif ? outCart : pdppCart

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
      href     : outAktif ? OUT_HREF : PDPP_HREF
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
    url   : "{!! url('penerimaandppresetheader') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      tabel  : outAktif ? 'outstanding' : 'penerimaan'
    },
    success : function (res) {
      let cart = pdppBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      if (outAktif) { outCart = cart } else { pdppCart = cart }
      window.gcart_header = cart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function renderTabelPdpp () {
  window.g_modeReport = 1
  // Arahkan ReportTable ke tabel ini sebelum headHtml()/renderBar() dipanggil.
  if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use('#tabelPdpp') }
  pdppPakaiKolomPdpp()

  if ($.fn.DataTable.isDataTable('#tabelPdpp')) {
    $('#tabelPdpp').DataTable().destroy()
  }

  let cols = pdppKolomTampil()
  let kolomRender = cols.map(pdppKolomRender)

  let thead = document.getElementById('tabel_header_pdpp')
  thead.innerHTML = pdppHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataPdpp || []
  if (pdppFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return pdppOtorisasi(item) === pdppFilterOtorisasi })
  }
  if (pdppFilterPenagih !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return (item.Penagih || '') === pdppFilterPenagih })
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
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NoBukti}')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${pdppRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${pdppRenderNilai(c, item)}</td>`
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

  $('#tabelPdpp').DataTable({
    lengthChange: false,
    pageLength: pdppPanjangHalaman,
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  pdppPindahBar()
  pdppIkatSearch()
  pdppIkatPanjangHalaman()
  pdppIkatPeriode()
  let inputSearch = document.getElementById('pdppSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabelPdpp').DataTable().search(inputSearch.value).draw()
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
let outFilterPenagih = 'SEMUA'
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

function outUpdateFilterBadge () {
  let jml = (outFilterPenagih !== 'SEMUA') ? 1 : 0
  let badge = document.getElementById('outFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function outTerapkanFilter () {
  outFilterPenagih = $('#outModalPenagih').val() || 'SEMUA'
  outUpdateFilterBadge()
  $('#modalFilterOut').modal('hide')
  renderTabelOutstanding()
}

function outResetFilter () {
  outFilterPenagih = 'SEMUA'
  $('#outModalPenagih').val('SEMUA')
  outUpdateFilterBadge()
  $('#modalFilterOut').modal('hide')
  renderTabelOutstanding()
}

function outKolomTampil () {
  return (outCart || []).filter(c => Number(c[2]) === 1)
}

// Sama persis dengan pdppIkatPeriode() milik tab Penerimaan DPP.
function outIkatPeriode () {
  let awal  = document.getElementById('outTglAwal')
  let akhir = document.getElementById('outTglAkhir')
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

function renderTabelOutstanding () {
  window.g_modeReport = 1
  // Arahkan ReportTable ke tabel ini sebelum headHtml()/renderBar() dipanggil.
  if (typeof ReportTable !== 'undefined' && ReportTable.use) { ReportTable.use('#tabel') }
  pdppPakaiKolomOutstanding()

  if ($.fn.DataTable.isDataTable('#tabel')) {
    $('#tabel').DataTable().destroy()
  }

  let cols = outKolomTampil()
  let kolomRender = cols.map(pdppKolomRender)

  // Judul kolom digambar ReportTable (pegangan geser + roda gigi); kolom Actions
  // ditambahkan di depan karena bukan kolom data dan tidak bisa digeser/disembunyikan.
  let thead = document.getElementById('tabel_header_out')
  thead.innerHTML = pdppHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }

  let dataTampil = dataOutstanding || []
  if (outFilterPenagih !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return (item.Penagih || '') === outFilterPenagih })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">
        <button class="btn btn-primary btn-sm" type="button" title="Tambah" onclick="buttonAdd('${item.NOBUKTI}' , ${item.Urut})"><i class="bi bi-plus"></i></button>
      </div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${pdppRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${pdppRenderNilai(c, item)}</td>`
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
      function loadAll () {

        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('penerimaandpploadall') !!}",
          type: "get",
          async: false,
          data: {
            tglawal    : $('#pdppTglAwal').val(),
            tglakhir   : $('#pdppTglAkhir').val(),
            // Tab Outstanding DPP punya kotak periodenya sendiri.
            outtglawal : $('#outTglAwal').val(),
            outtglakhir: $('#outTglAkhir').val()
          },
          success: function(res) {
            // Tabel Outstanding DPP digambar renderTabelOutstanding() dari
            // res.tempOutstanding + konfigurasi kolomnya sendiri (awalan "out").
            outCart = pdppBuatCart(res.outheadertableheader, res.outheadertablevalue, res.outisnumeric, res.outisshown, res.outdesimal, res.outaliasordered)
            dataOutstanding = res.tempOutstanding || []

            // Tabel Penerimaan DPP digambar renderTabelPdpp() dari data + konfigurasi
            // kolom yang ikut dikirim loadAll(). Tidak ada lagi tempPenerimaan2 -
            // sudah & belum otorisasi kini satu kumpulan data yang disaring di browser.
            pdppCart = pdppBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
            dataPdpp = res.tempPenerimaan || []

            renderTabelOutstanding()
            renderTabelPdpp()

          }})

      }

    function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('penerimaandppdetailCetak') !!}",
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
                <td style="border:1px solid; width:25%;">${dataPrint[0].NOBUKTI}</td>
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
    // let grandTotalJumlah = 0;

    // dataPrint.forEach(item => {

    //   if (item.JumlahRp) {
    //     grandTotalJumlah += Number(item.JumlahRp) || 0;
    //   }

    // });
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


      function buttonOtorisasi (nobukti) {

        let _token = $("#_token").val();
        // let nobukti = $("#input_detail_nobukti").val();
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
              // $('.mainpage').hide();
              // $('#page1').show();
            }





          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }

        })


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


      function submitProses () {

        let _token  = $("#_token").val()
        let choice = "I"
        let nobukti  = $("#input_add_nobukti").val()
        let nourut  = $("#input_add_nourut").val()
        let valas  = $("#input_add_valas").val()
        let tipe = 'DPP'
        // let choice = 'I'
        // let nobukti = $('#input_add_nobukti').val()
        // let nourut = $('#input_add_nourut').val()
        // let tanggal = $('#input_add_tanggal').val()
        //
        // let kodecustsupp = $('#input_add_kodecust').val()
        let penagih = $("#input_add_penagih").val()

        let kodecustsupp  = $("#input_add_kodecust").val()
        let checkDate = new Date($("#input_add_tanggal").val())
        let periode_bulan = document.getElementById("periode_bulan").value
        let periode_tahun = document.getElementById("periode_tahun").value

        if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
            alertify.warning("Tanggal tidak sesuai periode");
            return
        }
        let tanggal  = $("#input_add_tanggal").val()
        let jmlrecord = tipeform == "add" ? 0 : 1

        let xlisttambah = []
        let xlisttambahlb = []
        let xlisttambahkl = []

        // console.log('[][][][][][][][]')
        // console.log(listTambahLB)
        // console.log(listTambahKL)
        // console.log(listTambah)




        console.log('[][][][][][][][]')
        console.log(listProses)
        console.log(arrayKL)
        listProses.forEach((item, i) => {

          if (document.getElementById(`prosesCheckList${i}`).checked) {
            xlisttambah.push(listProses[i])
            // if (listTambahLB[listProses[i].NOFAKTUR]) {
            //   console.log(listTambahLB[listProses[i].NOFAKTUR])
            //   xlisttambahlb.push(listTambahLB[listProses[i].NOFAKTUR])
            // }
            console.log(arrayKL)
            console.log(arrayKL[i])
            console.log(xlisttambahkl)
            console.log(xlisttambahkl.length)
            xlisttambahkl.push(arrayKL[i])
            // xlisttambahkl.push(arrayKL[i])
            console.log(xlisttambahkl)
            console.log(xlisttambahkl.length)
            console.log("NNNNN")
            // if (listTambahKL[listProses[i].NOFAKTUR]) {
            //   console.log(xlisttambahkl)
            //   console.log(listTambahKL[listProses[i].NOFAKTUR])
            //   let tempkl = listTambahKL[listProses[i].NOFAKTUR]
            //   tempkl.forEach((item, i) => {
            //     xlisttambahkl.push(item)
            //   });

              // xlisttambahkl.concat(tempkl)
              console.log("11111111")
              console.log(xlisttambahkl)
            // }

          }
        });

        console.log(xlisttambahkl)

        console.log("AWEAOIEJAEWIJ")
        console.log('tambah')
        console.log(xlisttambah)
        console.log('tambahkl')
        console.log(xlisttambahkl)
        // console.log('tambahlb')
        // console.log(xlisttambahlb)

        if (!xlisttambah.length ) {
          alertify.warning("Tidak ada data dipilih")
          return
        }


        $.ajax({
            url: "{!! url('penerimaandppspproses') !!}",
            type: "post",
            async: false,
            data: {
              _token,
              choice,
              tempData : xlisttambah ,
              tempDataKL : xlisttambahkl ,
              // tempDataLB : xlisttambahlb ,
              nobukti,
              nourut,
              tipe,
              tanggal,
              jmlrecord,
              kodecustsupp,
              urutTrans,
              penagih
            },
            success: function(res) {
              console.log(res ,'!')

              if (res == 1) {
                alertify.success('DPP telah ditambah');
                document.getElementById("input_add_tanggal").disabled = true

                tipeform = 'edit'
                // $("#form").modal('toggle')
                refreshDataTable(nobukti)
                loadAll()

                $("#formProses").modal("hide")

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

      function buttonAddBatal () {
        $(".showhideitem").hide()
      }

      function buttonSaveLB () {


          let xnilainota = toNum($("#input_modalx_nilainotadibayar").val())
          let xdibayar = toNum($("#input_modalx_dibayar").val())
          let xlebihbayar = toNum($("#input_modalx_lebihbayar").val())

          if (Number(xlebihbayar) > 0 && arrayKL[saveHeaderIndex].length > 0) {
              alertify.warning("Sudah ada kurang bayar")
              return
          }
          let xperkiraanlebihbayar = $("#input_modalx_perkiraanlebihbayar").val()
          let xnamaperkiraanlebihbayar = $("#input_modalx_namaperkiraanlebihbayar").val()
          let xsisa = toNum($("#input_modalx_sisanotadibayar").val())

          let xxdibayar = toNum($("#input_modal_dibayar").val())
          if (Number(xsisa) + Number(xnilainota) < Number(xdibayar)) {
            alertify.warning('Dibayar melebihi sisa nota')
            return

          }
          let checksisadibayar = Number(listProses[saveHeaderIndex].DIBAYAR)
          let checksisalb = Number(listProses[saveHeaderIndex].inputLB)
          let checksisa = checksisadibayar + checksisalb
          console.log(xdibayar)
          console.log(xlebihbayar)
          console.log(xperkiraanlebihbayar)
          console.log(xnamaperkiraanlebihbayar)
          // console.log(xsisa)
          console.log(checksisadibayar)
          console.log(checksisalb)
          console.log(checksisa)

          if ( Number(xdibayar) <= 0 && Number(xlebihbayar) <= 0) {
            alertify.warning("Jumlah <= 0")
            return
          }
          if (Number(xnilainota) < Number(xdibayar)) {
            alertify.warning("Dibayar > Nilai Nota")
            return
          }

          if (Number(xlebihbayar) > 0 && !xperkiraanlebihbayar) {
            alertify.warning("Perk lebih bayar belum diisi")
            return
          }
          if ( Number(xlebihbayar) <= 0 && xperkiraanlebihbayar) {
            alertify.warning("Lebih bayar belum diisi")
            return
          }

          // if (Number(xlebihbayar) + Number(xdibayar) > Number(xsisa) + Number(checksisa)) {
          //   alertify.warning("Jumlah melebihi sisa")
          //   return
          // }

          if (Number(xlebihbayar) > 0) {


            // let x = { ...saveHeaderInvoice }
            listProses[saveHeaderIndex].inputLB = xlebihbayar
            listProses[saveHeaderIndex].inputPerkiraanLB = xperkiraanlebihbayar
            listProses[saveHeaderIndex].inputNamaPerkiraanLB = xnamaperkiraanlebihbayar
            // x.DIBAYAR = 0
            // x.indexHeader = saveHeaderIndex
            // listTambahLB[saveHeaderInvoice.NOFAKTUR] = x

          }

          listProses[saveHeaderIndex].DIBAYAR = xdibayar
          $(".showhideitemKL").hide()
          // listTambahLB
          // listTambah
          refreshSisa()
          // let xdibayar = $(`#list_proses_dibayar${saveHeaderIndex}`).val();
          let xtotfaktur = saveHeaderInvoice.TOTFAKTUR

          let xKL = toNum($(`#list_proses_KL${saveHeaderIndex}`).val());
          let xsdhbayar = saveHeaderInvoice.SDHBAYAR
          sisa =(((Number(xtotfaktur)- Number(xsdhbayar)) -Number(xdibayar))- Number(xKL))
          document.getElementById("input_modalx_sisanotadibayar").value = formatNumberDisplay(parseFloat(sisa).toFixed(2))



      }

      function buttonDeleteKL (index) {
        arrayKL[saveHeaderIndex].splice(index, 1)
        alertify.success("KL berhasil dihapus")




        refreshTableKL()
        refreshSisa()
        let xdibayar = toNum($(`#list_proses_dibayar${saveHeaderIndex}`).val());
        let xtotfaktur = saveHeaderInvoice.TOTFAKTUR

        let xKL = toNum($(`#list_proses_KL${saveHeaderIndex}`).val());

        let xsdhbayar = saveHeaderInvoice.SDHBAYAR
        sisa =(((Number(xtotfaktur)- Number(xsdhbayar)) -Number(xdibayar))- Number(xKL))
        document.getElementById("input_modalx_sisanotadibayar").value = formatNumberDisplay(parseFloat(sisa).toFixed(2))

        $('.showhideitemKL').hide()
      }

      function submitEditKL () {


          let kl = toNum($("#input_modalx_kurangbayar").val())
          let perkkl = $("#input_modalx_perkiraankurangbayar").val()
          let namaperkkl = $("#input_modalx_namaperkiraankurangbayar").val()
          let xsisanota = toNum($("#input_modalx_sisanotadibayar").val())
          // perkkl = '444'
          // namaperkkl = 'TESTESwiu'
          if (Number(kl) <= 0 || !perkkl) {

            alertify.warning("Data tidak lengkap")
            return
          }

          if(Number(xsisanota) - Number(kl) < 0) {
            alertify.warning("Melebihi sisa nota")
            return
          }

          arrayKL[saveHeaderIndex].push(
            {
              inputKL: kl,
              inputPerkiraanKL : perkkl,
              inputNamaPerkiraanKL: namaperkkl
            }
          )
          alertify.success("KL berhasil ditambah")
          // let x = { ...saveHeaderInvoice }
          // x.inputKL = kl
          // x.inputPerkiraanKL = perkkl
          // x.inputNamaPerkiraanKL = namaperkkl
          // console.log(x)
          // console.log(listTambahKL)
          // console.log('!!!!')
          // console.log(saveHeaderInvoice.NOFAKTUR)
          // if (!listTambahKL[saveHeaderInvoice.NOFAKTUR]) {
          //   listTambahKL[saveHeaderInvoice.NOFAKTUR] = []
          // }
          // console.log(listTambahKL)
          // listTambahKL[saveHeaderInvoice.NOFAKTUR].push(x)
          // console.log(listTambahKL)




          refreshTableKL()
          refreshSisa()
          let xdibayar = toNum($(`#list_proses_dibayar${saveHeaderIndex}`).val());
          let xtotfaktur = saveHeaderInvoice.TOTFAKTUR

          let xKL = toNum($(`#list_proses_KL${saveHeaderIndex}`).val());

          let xsdhbayar = saveHeaderInvoice.SDHBAYAR
          sisa =(((Number(xtotfaktur)- Number(xsdhbayar)) -Number(xdibayar))- Number(xKL))
          document.getElementById("input_modalx_sisanotadibayar").value = formatNumberDisplay(parseFloat(sisa).toFixed(2))

          $('.showhideitemKL').hide()

      }

      function submitAddKL () {
          let kl = toNum($("#input_modalx_kurangbayar").val())
          let perkkl = $("#input_modalx_perkiraankurangbayar").val()
          let namaperkkl = $("#input_modalx_namaperkiraankurangbayar").val()
          let xsisanota = toNum($("#input_modalx_sisanotadibayar").val())
          // perkkl = '444'
          // namaperkkl = 'TESTESwiu'
          if (Number(kl) <= 0 || !perkkl) {

            alertify.warning("Data tidak lengkap")
            return
          }

          if(Number(xsisanota) - Number(kl) < 0) {
            alertify.warning("Melebihi sisa nota")
            return
          }

          arrayKL[saveHeaderIndex].push(
            {
              inputKL: kl,
              inputPerkiraanKL : perkkl,
              inputNamaPerkiraanKL: namaperkkl
            }
          )
          alertify.success("KL berhasil ditambah")
          // let x = { ...saveHeaderInvoice }
          // x.inputKL = kl
          // x.inputPerkiraanKL = perkkl
          // x.inputNamaPerkiraanKL = namaperkkl
          // console.log(x)
          // console.log(listTambahKL)
          // console.log('!!!!')
          // console.log(saveHeaderInvoice.NOFAKTUR)
          // if (!listTambahKL[saveHeaderInvoice.NOFAKTUR]) {
          //   listTambahKL[saveHeaderInvoice.NOFAKTUR] = []
          // }
          // console.log(listTambahKL)
          // listTambahKL[saveHeaderInvoice.NOFAKTUR].push(x)
          // console.log(listTambahKL)




          refreshTableKL()
          refreshSisa()
          let xdibayar = toNum($(`#list_proses_dibayar${saveHeaderIndex}`).val());
          let xtotfaktur = saveHeaderInvoice.TOTFAKTUR

          let xKL = toNum($(`#list_proses_KL${saveHeaderIndex}`).val());
          let xsdhbayar = saveHeaderInvoice.SDHBAYAR
          let sisa =(((Number(xtotfaktur)- Number(xsdhbayar)) -Number(xdibayar))- Number(xKL))
          document.getElementById("input_modalx_sisanotadibayar").value = formatNumberDisplay(parseFloat(sisa).toFixed(2))

          $('.showhideitemKL').hide()

      }


      function submitAdd () {
        // let rowTableProses = ""

        let choice = 'I'
        let nobukti = $('#input_add_nobukti').val()
        let nourut = $('#input_add_nourut').val()
        let tanggal = $('#input_add_tanggal').val()

        let kodecustsupp = $('#input_add_kodecust').val()
        let penagih = $("#input_add_penagih").val()
        let _token = $('#_token').val();
        let tempSubmitProses = []
        listProses.forEach((item, i) => {
          if (document.getElementById(`prosesCheckList${i}`).checked) {
              tempSubmitProses.push(listProses[i])

          }
        });

        console.log(tempSubmitProses)
        console.log({_token,
        choice,
        nobukti ,
        nourut ,
        tanggal ,

        tempData: listProses,
        kodecustsupp ,
        penagih ,})

        $.ajax({
          url: "{!! url('penerimaandppspadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti ,
            nourut ,
            tanggal ,
            tipeform,
            tempData: listProses,
            kodecustsupp ,
            penagih ,
          },
          success: function(res) {
            console.log(res)

            if (res == 1) {
              tipeform = 'edit'
              refreshDataTable(nobukti)

              $("#formProses").modal("hide")
              alertify.success("Berhasil menambah invoice")
              // $(".showhideitemgiro").hide()
            }
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })



      }

      function buttonDeleteGiro (index) {


        let xgiro = listKoreksiGiro[index]
        console.log(xgiro)
        document.getElementById("input_giro_bank").value = xgiro.Bank
        document.getElementById("input_giro_nogiro").value = xgiro.NoGiro
        document.getElementById("input_giro_tanggal").valueAsDate = new Date(xgiro.TglGiro)
        pdppSetValas("input_giro_valas", xgiro.Kodevls)
        setNum("input_giro_kurs", xgiro.Kurs)
        document.getElementById("input_giro_nilaigiro").value = xgiro.Jumlah ? formatNumberDisplay(parseFloat(xgiro.Jumlah).toFixed(2)) : ""
        document.getElementById("input_giro_keterangan").value = xgiro.Keterangan


        let _token = $("#_token").val()
        let choice = "D"
        let nogiro = xgiro.NoGiro
        let bank = xgiro.Bank
        let tanggalgiro = xgiro.TglGiro
        let valas = xgiro.Kodevls
        let kurs = xgiro.Kurs
        let nilaigiro = xgiro.Jumlah ? parseFloat(xgiro.Jumlah).toFixed(2) : ''
        let keterangan = xgiro.Keterangan
        let nobukti = $("#input_add_nobukti").val()
        let tanggal = $("#input_add_tanggal").val()



            alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus giro '+ nogiro + ' ' + bank +' ?',
                function() {


                          $.ajax({
                            url: "{!! url('penerimaandppspgiro') !!}",
                            type: "post",
                            async: false,
                            data: {
                              _token,
                              choice,
                              nogiro,
                              bank,
                              tanggalgiro,
                              valas,
                              kurs,
                              nilaigiro,
                              keterangan,
                              nobukti,
                              tanggal
                            },
                            success: function(res) {
                              console.log(res)

                              if (res == 1) {

                                refreshDataTable(nobukti)
                                $(".showhideitemgiro").hide()
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

      function submitEditGiro () {
        let _token = $("#_token").val()
        let choice = "U"
        let nogiro = $("#input_giro_nogiro").val()
        let bank = $("#input_giro_bank").val()
        let tanggalgiro = $("#input_giro_tanggal").val()
        let valas = $("#input_giro_valas").val()
        let kurs = toNum($("#input_giro_kurs").val())
        let nilaigiro = toNum($("#input_giro_nilaigiro").val())
        let keterangan = $("#input_giro_keterangan").val()
        let nobukti = $("#input_add_nobukti").val()
        let tanggal = $("#input_add_tanggal").val()

        $.ajax({
          url: "{!! url('penerimaandppspgiro') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nogiro,
            bank,
            tanggalgiro,
            valas,
            kurs,
            nilaigiro,
            keterangan,
            nobukti,
            tanggal
          },
          success: function(res) {
            console.log(res)

            if (res == 1) {

              refreshDataTable(nobukti)
              $(".showhideitemgiro").hide()
            }
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })
      }

      function submitAddGiro () {
        let _token = $("#_token").val()
        let choice = "I"
        let nogiro = $("#input_giro_nogiro").val()
        let bank = $("#input_giro_bank").val()
        let tanggalgiro = $("#input_giro_tanggal").val()
        let valas = $("#input_giro_valas").val()
        let kurs = toNum($("#input_giro_kurs").val())
        let nilaigiro = toNum($("#input_giro_nilaigiro").val())
        let keterangan = $("#input_giro_keterangan").val()
        let nobukti = $("#input_add_nobukti").val()
        let tanggal = $("#input_add_tanggal").val()

        if (!nogiro || !bank) {

          alertify.warning("Giro dan bank wajib diisi")
          return
        }

        if (Number(kurs) <= 0 || Number(nilaigiro) <= 0) {
          alertify.warning("Kurs / nilai giro <= 0")
          return
        }

        $.ajax({
          url: "{!! url('penerimaandppspgiro') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nogiro,
            bank,
            tanggalgiro,
            valas,
            kurs,
            nilaigiro,
            keterangan,
            nobukti,
            tanggal
          },
          success: function(res) {
            console.log(res)
            if (res == 2) {
              alertify.warning("Nogiro dan bank sudah terdaftar")
              return
            }
            if (res == 1) {

              refreshDataTable(nobukti)
              $(".showhideitemgiro").hide()
            }
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })
      }

      function buttonDeleteInvoice (index) {
        xinvoice = listKoreksiInvoice[index]
        alertify.confirm('Hapus Invoice', 'Hapus Invoice ' + xinvoice.NOFAKTUR + ` ?`,
            function() {
        let _token = $("#_token").val()
        let choice = 'D'
        let dibayar = 0
        let kl = 0
        let lb = 0

        let perkiraan = ''
        let nofaktur = ''
        let namaperkiraan = ''
        let nobukti = xinvoice.NOBUKTI
        let urut = xinvoice.URUT

        $.ajax({
          url: "{!! url('penerimaandppspkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            dibayar,
            kl,
            lb,
            perkiraan,
            nofaktur,
            nobukti,
            urut
          },
          success: function(res) {
            console.log(res)
            if (res == 2) {
              setNewNoBukti()
              $(".showhideitem").hide()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
        }
            if (res == 1) {

              refreshDataTable(nobukti)
              loadAll()
              $(".showhideitem").hide()
              alertify.success("Berhasil menghapus invoice")
              // $(".showhideitemgiro").hide()
            }
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })
      },function(){
        console.log('no')
      });
      }


      function submitAddEdit () {
        let _token = $("#_token").val()
        let choice = 'U'
        let dibayar = toNum($("#AddDibayar").val())
        let kl = toNum($("#AddKurangBayar").val())
        let lb = toNum($("#AddLebihBayar").val())

        let perkiraan = $("#AddPerkiraan").val()
        let nofaktur = $("#AddNoFaktur").val()
        let namaperkiraan = $("#AddNamaPerkiraan").val()
        let nobukti = xinvoice.NOBUKTI
        let urut = xinvoice.URUT

        $.ajax({
          url: "{!! url('penerimaandppspkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            dibayar,
            kl,
            lb,
            perkiraan,
            nofaktur,
            nobukti,
            urut
          },
          success: function(res) {
            console.log(res)
            if (res == 2) {
              setNewNoBukti()
              $(".showhideitem").hide()
        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
        }
            if (res == 1) {

              refreshDataTable(nobukti)
              $(".showhideitem").hide()
              loadAll()
              // $(".showhideitemgiro").hide()
              alertify.success("Berhasil mengedit invoice")
            }
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })

      }


      function buttonEditInvoice (index) {
        xinvoice = listKoreksiInvoice[index]
        console.log(xinvoice)
        document.getElementById("AddNoFaktur").value = xinvoice.NOFAKTUR
        console.log(1)
        document.getElementById("AddPerkiraan").value = xinvoice.perkiraan
        console.log(2)
        document.getElementById("AddNamaPerkiraan").value = xinvoice.NamaPerkiraan
        console.log(22)
        document.getElementById("AddDibayar").disabled = true
        console.log(23)
        document.getElementById("AddKurangBayar").disabled = true
        document.getElementById("AddLebihBayar").disabled = true
        if ( Number(xinvoice.DIBAYAR) > 0) {
          document.getElementById("AddDibayar").disabled = false
        } else if (Number(xinvoice.LB) > 0)  {
          document.getElementById("AddLebihBayar").disabled = false
        } else {
          document.getElementById("AddKurangBayar").disabled = false
        }

        document.getElementById("AddDibayar").value = Number(xinvoice.DIBAYAR) ? formatNumberDisplay(parseFloat(xinvoice.DIBAYAR).toFixed(2)) : "0.00"
        document.getElementById("AddKurangBayar").value = Number(xinvoice.KL) ? formatNumberDisplay(parseFloat(xinvoice.KL).toFixed(2)) : "0.00"
        document.getElementById("AddLebihBayar").value = Number(xinvoice.LB) ? formatNumberDisplay(parseFloat(xinvoice.LB).toFixed(2)) : "0.00"

        $("#formAddEdit").show()
      }

      function buttonEditGiro (index) {
        console.log(listKoreksiInvoice)
        let xgiro = listKoreksiGiro[index]
        console.log(xgiro)
        lockFormGiro()
        document.getElementById("input_giro_bank").value = xgiro.Bank
        document.getElementById("input_giro_nogiro").value = xgiro.NoGiro
        document.getElementById("input_giro_tanggal").valueAsDate = new Date(xgiro.TglGiro)
        pdppSetValas("input_giro_valas", xgiro.Kodevls)
        setNum("input_giro_kurs", xgiro.Kurs)
        document.getElementById("input_giro_nilaigiro").value = xgiro.Jumlah ? formatNumberDisplay(parseFloat(xgiro.Jumlah).toFixed(2)) : ""
        document.getElementById("input_giro_keterangan").value = xgiro.Keterangan


        $("#labelAddGiro").hide()
        $("#labelEditGiro").show()
        $("#buttonSubmitAddGiro").hide()
        $("#buttonSubmitEditGiro").show()
        $("#formGiroAdd").show()
      }

      function buttonAddGiroAdd () {
        console.log("buttonAddGiroAdd")

        document.getElementById("input_giro_bank").value = ''
        document.getElementById("input_giro_nogiro").value = ''
        document.getElementById("input_giro_tanggal").valueAsDate = new Date()
        pdppSetValas("input_giro_valas", 'IDR')
        document.getElementById("input_giro_kurs").value = '1.00'
        document.getElementById("input_giro_nilaigiro").value = '0.00'
        document.getElementById("input_giro_keterangan").value = ''

        lockFormGiro(false)
        $("#labelAddGiro").show()
        $("#labelEditGiro").hide()
        $("#buttonSubmitAddGiro").show()
        $("#buttonSubmitEditGiro").hide()
        $("#formGiroAdd").show()
      }

      function lockFormGiro ( value = true) {
        document.getElementById("input_giro_nogiro").disabled = value
        document.getElementById("input_giro_bank").disabled = value
        document.getElementById("input_giro_kurs").disabled = value
      }


      function buttonKoreksi (nobukti) {
        console.log('buttonKoreksi' , nobukti)
        tipeform = 'edit'
        listKoreksiInvoice = []
        lockForm(true)
        refreshDataTable(nobukti)
        $(".showhideitem").hide()
        if (!listKoreksiInvoice.length) {
          alertify.warning("Data tidak ditemukkan")
          return
        } else {
          urutTrans = listKoreksiInvoice[0].UrutDPP
          $(".mainpage").hide()
          $("#page2").show()
        }


      }

      function resetDataTable () {
        console.log('resetDataTable')

        listKoreksiInvoice = []
        listKoreksiGiro = []
        listKoreksiRekap = []
        document.getElementById("input_add_nobukti").value = ''
        document.getElementById("input_add_kodecust").value = ''
        document.getElementById("input_add_namacust").value = ''
        document.getElementById("input_add_tanggal").valueAsDate = new Date()
        document.getElementById("input_add_nodpp").value = ''
        pdppSetValas("input_add_valas", 'IDR')
        document.getElementById("input_add_penagih").value = ''

        document.getElementById("addInvoiceTableData").innerHTML = `<tr>
          <td colspan=7 class='text-center'>Belum ada data</td>
        </tr>`
        document.getElementById("addGiroTableData").innerHTML = `<tr>
          <td colspan=5 class='text-center'>Belum ada data</td>
        </tr>`
        document.getElementById("addRekapTableData").innerHTML = `<tr>
          <td colspan=4 class='text-center'>Belum ada data</td>
        </tr>`



      }

      function refreshDataTable (nobukti) {
        console.log('refreshDataTable' , nobukti)
        // let nobukti = $("#input_add_nobukti").val()
        let _token = $("#_token").val()
        $.ajax({
          url: "{!! url('penerimaandppdetailkoreksi') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nobukti
          },
          success: function(res) {

            console.log(res)
            console.log(res.listInvoice)
            console.log(res.listGiro)
            console.log(res.listRekap)

            listKoreksiInvoice = res.listInvoice
            listKoreksiGiro = res.listGiro
            listKoreksiRekap = res.listRekap

            rowTableInvoice = ''
            rowTableGiro = ''
            rowTableGiroForm = ''
            rowTableRekap = ''

            if (!listKoreksiInvoice.length) {
              console.log("ASDSAD")
              if (tipeform == 'edit') {
                console.log("")
                alertify.success("Data Habis")
                $(".mainpage").hide()
                $("#page1").show()
                return
              }
              alertify.warning("Data tidak ditemukkan")
              return

            }

            document.getElementById("input_add_nobukti").value = nobukti
            document.getElementById("input_add_kodecust").value = listKoreksiInvoice[0].KODECUSTSUPP
            document.getElementById("input_add_namacust").value = listKoreksiInvoice[0].NAMACUSTSUPP
            document.getElementById("input_add_tanggal").valueAsDate = new Date(listKoreksiInvoice[0].TANGGAL)
            document.getElementById("input_add_nodpp").value = listKoreksiInvoice[0].NoDPP
            pdppSetValas("input_add_valas", listKoreksiInvoice[0].Valas)
            document.getElementById("input_add_namacust").value = listKoreksiInvoice[0].NAMACUSTSUPP
            document.getElementById("input_add_penagih").value = listKoreksiInvoice[0].Penagih

            listKoreksiInvoice.forEach((item, i) => {
              rowTableInvoice += `
                <tr>
                  <td>${item.TipeKasBank}</td>
                  <td>${item.NOFAKTUR}</td>
                  <td class="text-right">${formatAngkaX(item.DIBAYAR)}</td>
                  <td class="text-right">${formatAngkaX(item.LB)}</td>
                  <td class="text-right">${formatAngkaX(item.KL)} </td>
                  <td>${item.KasBank}</td>
                  <td class="text-center">
                    <button class="btn btn-success btn-sm" type="button" onclick="buttonEditInvoice('${i}')"><i class="bi bi-pen" title=""></i></button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteInvoice('${i}')"><i class="bi bi-trash" title=""></i></button>

                  </td>
                </tr>
              `
            });

            if (!listKoreksiInvoice.length) {
              rowTableInvoice = `<tr>
                <td colspan=7 class='text-center'>Belum ada data</td>
              </tr>`
            }

            listKoreksiGiro.forEach((item, i) => {

              rowTableGiroForm += `
              <tr>
                <td>${item.NoGiro}</td>
                <td>${item.Bank}</td>
                <td>${formatDate(item.TglGiro)}</td>

                <td>${item.Kodevls}</td>

                <td class="text-right">${formatAngkaX(item.Kurs)}</td>
                <td class="text-right">${formatAngkaX(item.Jumlah)}</td>
                <td class="text-center">
                  <button class="btn btn-success btn-sm" type="button" onclick="buttonEditGiro('${i}')"><i class="bi bi-pen" title=""></i></button>
                  <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteGiro('${i}')"><i class="bi bi-trash" title=""></i></button>

                </td>
              </tr>
              `
              rowTableGiro += `
                <tr>
                  <td>${item.NoGiro}</td>
                  <td>${item.Bank}</td>
                  <td>${formatDate(item.TglGiro)}</td>
                  <td class="text-right">${formatAngkaX(item.Jumlah)}</td>

                </tr>
              `
            });



            if (!listKoreksiGiro.length) {
              rowTableGiro = `<tr>
                <td colspan=4 class='text-center'>Belum ada data</td>
              </tr>`
              rowTableGiroForm = `<tr>
                <td colspan=7 class='text-center'>Belum ada data</td>
              </tr>`
            }
            xdibayar = 0
            xkl = 0
            xlb = 0

            listKoreksiRekap.forEach((item, i) => {
              xdibayar += item.Dibayar
              xkl += item.KL
              xlb += item.LB
              rowTableRekap += `
                <tr>
                  <td>${item.MyTipeKasBank}</td>
                  <td class="text-right">${formatAngkaX(item.Dibayar)}</td>
                  <td class="text-right">${formatAngkaX(item.LB)}</td>
                  <td class="text-right">${formatAngkaX(item.KL)}</td>

                </tr>
              `
            });

            if (!listKoreksiRekap.length) {
              rowTableRekap = `<tr>
                <td colspan=4 class='text-center'>Belum ada data</td>
              </tr>`
            } else {

              rowTableRekap += `
                  <tr>
                    <td class="text-right">Total:</td>
                    <td class="text-right">${formatAngkaX(xdibayar)}</td>
                    <td class="text-right">${formatAngkaX(xlb)}</td>
                    <td class="text-right">${formatAngkaX(xkl)}</td>
                  </tr>
              `
            }

            document.getElementById("addInvoiceTableData").innerHTML = rowTableInvoice
            document.getElementById("addGiroTableData").innerHTML = rowTableGiro
            document.getElementById("addRekapTableData").innerHTML = rowTableRekap
            document.getElementById("giroModalTableData").innerHTML = rowTableGiroForm





          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })

      }

      // function resetDataTable () {
      //   console.log('resetDataTable')
      // }

      function buttonAddGiro () {
        if (!listKoreksiInvoice.length) {
          alertify.warning("Tambah invoice terlebih dahulu")
          return
        }



        $(".showhideitemgiro").hide()
        $("#formGiro").modal("show")

      }

      function buttonGiroBatal () {
        $(".showhideitemgiro").hide()
      }

      function buttonCloseForm () {
        $('.mainpage').hide()
        $('#page1').show()
      }


            function refreshSisa () {
              console.log('refreshSisa')
              let rowTableProses = ``
              // onchange="pengajuanCheckList(${i},this.id)"


              listProses.forEach((item, i) => {
                let tempTotalKL = 0
                arrayKL[i].forEach((item, i) => {
                  tempTotalKL += Number(item.inputKL)
                });


                rowTableProses += `
                  <tr>
                    <td><div class="form-check text-center">
                        <input id="prosesCheckList${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        </div>
                    </td>
                    <td>${item.NOFAKTUR}</td>


                    <td class="text-right">${formatAngkaX(item.TOTFAKTUR)}</td>
                    <td class="text-right">${formatAngkaX(item.SDHBAYAR)}</td>
                    <td class="text-center">
                    <div class="input-group form-group">
                      <input style="height:30px; width:160px" id="list_proses_dibayar${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.DIBAYAR).toFixed(2))}' class="form-control text-right" disabled>

                      <button id="buttonChangeDibayar${i}" type="button" onclick="buttonChangeDibayar(${i})" class="btn btn-browsing btn-browsing-sm" title="Ubah Dibayar"><i class="bi bi-search"></i></button>

                    </div></td>

                    <td class="text-center">
                    <div class="input-group form-group">
                    <input style="height:30px; width:160px" id="list_proses_LB${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.inputLB).toFixed(2))}' class="form-control text-right" disabled>
                    </div>
                    </td>

                    <td class="text-center">
                    <div class="input-group form-group">
                    <input style="height:30px; width:160px" id="list_proses_KL${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(tempTotalKL).toFixed(2))}' class="form-control text-right" disabled>
                    </div>
                    </td>

                  </tr>
                `
              });

              document.getElementById("prosesModalTableData").innerHTML = rowTableProses



            }

      function buttonAddPickPerkiraanAdd (perkiraan , keterangan) {
        listProses = []
        let _token = $("#_token").val()
        let kodecust = $("#input_add_kodecust").val()
        let nodpp = $("#input_add_nodpp").val()


        $.ajax({
          url: "{!! url('penerimaandpplistproses') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nodpp,
            kodecust,
            perkiraan,
          },
          success: function(res) {

            // console.log(res)
            listProses = res
            arrayKL = []
            console.log('res !><',res)
            if (!listProses.length) {
              alertify.warning("DPP/Invoice tidak ditemukkan")
              return
            }
            let rowTableProses = ``
            // onchange="pengajuanCheckList(${i},this.id)"
            listProses.forEach((item, i) => {
              console.log("disini")
              console.log(arrayKL)
              arrayKL.push([])
              arrayKL.push([])
              console.log(arrayKL)
              console.log(arrayKL[0])
              listProses[i].inputLB = 0
              listProses[i].inputPerkiraanLB = ''
              listProses[i].inputNamaPerkiraanLB = ''
              listProses[i].inputPerkiraanDibayar = perkiraan
              rowTableProses += `
                <tr>
                  <td><div class="form-check text-center">
                      <input id="prosesCheckList${i}" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                      </div>
                  </td>
                  <td>${item.NOFAKTUR}</td>


                  <td class="text-right">${formatAngkaX(item.TOTFAKTUR)}</td>
                  <td class="text-right">${formatAngkaX(item.SDHBAYAR)}</td>
                  <td class="text-center">
                  <div class="input-group form-group">
                    <input style="height:30px; width:160px" id="list_proses_dibayar${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.DIBAYAR).toFixed(2))}' class="form-control text-right" disabled>

                    <button id="buttonChangeDibayar${i}" type="button" onclick="buttonChangeDibayar(${i})" class="btn btn-browsing btn-browsing-sm" title="Ubah Dibayar"><i class="bi bi-search"></i></button>

                  </div></td>

                  <td class="text-center">
                  <div class="input-group form-group">
                  <input style="height:30px; width:160px" id="list_proses_LB${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.LB).toFixed(2))}' class="form-control text-right" disabled>
                  </div>
                  </td>

                  <td class="text-center">
                  <div class="input-group form-group">
                  <input style="height:30px; width:160px" id="list_proses_KL${i}" type="text" inputmode="decimal" value='${formatNumberDisplay(parseFloat(item.KL).toFixed(2))}' class="form-control text-right" disabled>
                  </div>
                  </td>

                </tr>
              `
            });

            document.getElementById("prosesModalTableData").innerHTML = rowTableProses
            document.getElementById("input_proses_perkiraan").value = perkiraan
            document.getElementById("input_proses_namaperkiraan").value = keterangan ? keterangan : ''
            $("#formPerkiraan").modal("hide")
            $("#formProses").modal("show")
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })


      }

      function buttonChangeDibayar (index) {
        console.log(index)
        console.log(listProses[index])
        let x = listProses[index]
        saveHeaderInvoice = listProses[index]
        saveHeaderIndex = index
        console.log('23',saveHeaderInvoice)

        let xdibayar = toNum($(`#list_proses_dibayar${index}`).val());
        let xLB = toNum($(`#list_proses_LB${index}`).val());
        let xKL = toNum($(`#list_proses_KL${index}`).val());
        let sisa = $(`#input_modal_sisa`).val();
        console.log(saveHeaderInvoice.TOTFAKTUR)
        let xtotfaktur = saveHeaderInvoice.TOTFAKTUR

        let xsdhbayar = saveHeaderInvoice.SDHBAYAR
        sisa =(((Number(xtotfaktur)- Number(xsdhbayar)) -Number(xdibayar))- Number(xKL))
        // let xnilaifaktur = $(`#list_proses_dibayar${index}`).val();
        // sisa =
        console.log({xdibayar ,
        xLB,
        xKL ,
        sisa })
        document.getElementById("input_modalx_nilainotadibayar").value = formatNumberDisplay(parseFloat(Number(xtotfaktur)- Number(xsdhbayar)).toFixed(2))
        document.getElementById("input_modalx_dibayar").value = formatNumberDisplay(parseFloat(xdibayar).toFixed(2))


          document.getElementById("input_modalx_lebihbayar").value = formatNumberDisplay(parseFloat(xLB).toFixed(2))
          document.getElementById("input_modalx_sisanotadibayar").value = formatNumberDisplay(parseFloat(sisa).toFixed(2))
          if (xLB > 0) {
            // document.getElementById("input_modalx_perkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputPerkiraanLB
            // document.getElementById("input_modalx_namaperkiraanlebihbayar").value = listTambahLB[saveHeaderInvoice.NOFAKTUR].inputNamaPerkiraanLB

          } else {
            document.getElementById("input_modalx_perkiraanlebihbayar").value = ''
            document.getElementById("input_modalx_namaperkiraanlebihbayar").value = ''

          }
          refreshTableKL()


          $('.showhideitemKL').hide()


          $("#formX").modal('show')


      }

      function buttonAddListPerkiraanLebihBayar (id) {
        toId = id
        $("#formPerkiraanKLLB").modal('show')
      }

      function buttonAddPickPerkiraanLebihBayar (perkiraan , nama) {
        document.getElementById(`input_modalx_perkiraan${toId}`).value = perkiraan
        document.getElementById(`input_modalx_namaperkiraan${toId}`).value = nama
        $("#formPerkiraanKLLB").modal('hide')

      }


            function submitEditKL () {
                let kl = toNum($("#input_modalx_kurangbayar").val())
                let perkkl = $("#input_modalx_perkiraankurangbayar").val()
                let namaperkkl = $("#input_modalx_namaperkiraankurangbayar").val()
                let xsisanota = toNum($("#input_modalx_sisanotadibayar").val())
                // perkkl = '444'
                // namaperkkl = 'TESTESwiu'
                if (Number(kl) <= 0 || !perkkl) {

                  alertify.warning("Data tidak lengkap")
                  return
                }

                if((Number(xsisanota) + Number(arrayKL[saveHeaderIndex][indexEditKL].inputKL)) - Number(kl) < 0) {
                  alertify.warning("Melebihi sisa nota")
                  return
                }

                arrayKL[saveHeaderIndex][indexEditKL] =
                  {
                    inputKL: kl,
                    inputPerkiraanKL : perkkl,
                    inputNamaPerkiraanKL: namaperkkl
                  }

                alertify.success("KL berhasil diedit")
                // let x = { ...saveHeaderInvoice }
                // x.inputKL = kl
                // x.inputPerkiraanKL = perkkl
                // x.inputNamaPerkiraanKL = namaperkkl
                // console.log(x)
                // console.log(listTambahKL)
                // console.log('!!!!')
                // console.log(saveHeaderInvoice.NOFAKTUR)
                // if (!listTambahKL[saveHeaderInvoice.NOFAKTUR]) {
                //   listTambahKL[saveHeaderInvoice.NOFAKTUR] = []
                // }
                // console.log(listTambahKL)
                // listTambahKL[saveHeaderInvoice.NOFAKTUR].push(x)
                // console.log(listTambahKL)




                refreshTableKL()
                refreshSisa()
                let xdibayar = toNum($(`#list_proses_dibayar${saveHeaderIndex}`).val());
                let xtotfaktur = saveHeaderInvoice.TOTFAKTUR

                let xKL = toNum($(`#list_proses_KL${saveHeaderIndex}`).val());
                let xsdhbayar = saveHeaderInvoice.SDHBAYAR
                let sisa =(((Number(xtotfaktur)- Number(xsdhbayar)) - Number(xdibayar))- Number(xKL))
                document.getElementById("input_modalx_sisanotadibayar").value = formatNumberDisplay(parseFloat(sisa).toFixed(2))

                $('.showhideitemKL').hide()

            }


      function buttonEditKL (index) {
        // arrayKL[saveHeaderIndex]
        console.log(arrayKL[saveHeaderIndex][index])

        indexEditKL = index
        document.getElementById("buttonAddListPerkiraanKurangBayar").disabled = true
        setNum("input_modalx_kurangbayar", arrayKL[saveHeaderIndex][index].inputKL)
        document.getElementById("input_modalx_perkiraankurangbayar").value = arrayKL[saveHeaderIndex][index].inputPerkiraanKL
        document.getElementById("input_modalx_namaperkiraankurangbayar").value = arrayKL[saveHeaderIndex][index].inputNamaPerkiraanKL
        // buttonSubmitAddKL
        $("#buttonSubmitAddKL").hide()
        $("#buttonSubmitEditKL").show()
        $('.showhideitemKL').show()
        // arrayKL[saveHeaderIndex].inputKL =
        // arrayKL[saveHeaderIndex].inputPerkiraanKL =
        // arrayKL[saveHeaderIndex].inputNamaPerkiraanKL =

        // arrayKL[saveHeaderIndex].push(
        //   {
        //     inputKL: kl,
        //     inputPerkiraanKL : perkkl,
        //     inputNamaPerkiraanKL: namaperkkl
        //   }
        // )

        return

        // let xlb = $("#input_modalx_lebihbayar").val()
        // let xperklb = $("#input_modalx_perkiraanlebihbayar").val()
        //
        // if (Number(xlb) || xperklb) {
        //   alertify.warning("Lebih bayar sudah terisi")
        //   return
        // }



        // document.getElementById("input_modalx_kurangbayar").value = '0.00'
        // document.getElementById("input_modalx_perkiraankurangbayar").value = ''
        // document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
        $('.showhideitemKL').show()
      }

      function buttonAddKL () {

        let xlb = toNum($("#input_modalx_lebihbayar").val())
        let xperklb = $("#input_modalx_perkiraanlebihbayar").val()

        if (Number(xlb) || xperklb) {
          alertify.warning("Lebih bayar sudah terisi")
          return
        }




        document.getElementById("buttonAddListPerkiraanKurangBayar").disabled = false
        document.getElementById("input_modalx_kurangbayar").value = '0.00'
        document.getElementById("input_modalx_perkiraankurangbayar").value = ''
        document.getElementById("input_modalx_namaperkiraankurangbayar").value = ''
        $("#buttonSubmitAddKL").show()
        $("#buttonSubmitEditKL").hide()
        $('.showhideitemKL').show()

      }


      function refreshTableKL () {

        console.log(saveHeaderInvoice)
        console.log('refreshTableKL')
        let xlist = arrayKL[saveHeaderIndex]
        console.log(xlist)
        if (!xlist) {
          xlist = []
        }



        console.log(xlist)
        console.log('weeewoooo')
        if (!xlist.length) {
          document.getElementById("tabel_data_add_list_modalx").innerHTML = `
          <tr>
            <td class="text-center" colspan=4>Belum ada data</td>
          </tr>
          `

        } else {
          rowTablex = ''
          let xtotalKL = 0
          xlist.forEach((item, i) => {
            xtotalKL += Number(item.inputKL)
            rowTablex += `
              <tr>
                <td>${item.inputPerkiraanKL}</td>
                <td>${item.inputNamaPerkiraanKL}</td>
                <td class="text-right">${formatAngkaX(parseFloat(item.inputKL).toFixed(2))}</td>
                <td class="text-center"><button class="btn btn-success btn-sm" type="button" onclick="buttonEditKL(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonDeleteKL(${i})"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
            `

          });
          rowTablex += `
            <tr>
              <td class="text-right" colspan=2 >Total :</td>
              <td class="text-right">${formatAngkaX(parseFloat(xtotalKL).toFixed(2))}</td>
              <td></td>
            </tr>
          `

          document.getElementById("tabel_data_add_list_modalx").innerHTML = rowTablex


        }

      }




      function buttonAddItem () {
        $.ajax({
          url: "{!! url('penerimaandpplistperkiraanadd') !!}",
          type: "get",
          async: false,
          data: {

          },
          success: function(res) {

            console.log(res)
            rowTable = ``
            res.forEach((item, i) => {
              let ketx = item.Keterangan ? item.Keterangan : ''
              // Tanpa tombol Actions: barisnya diklik langsung untuk memilih.
              rowTable += `
                <tr class="pick-row" onclick="buttonAddPickPerkiraanAdd('${item.Perkiraan}' , '${ketx}' )">
                  <td>${item.Perkiraan}</td>
                  <td>${ketx}</td>
                </tr>
              `
            });

            document.getElementById("perkiraanModalTableData").innerHTML = rowTable
            pickerKasInit('perkiraanModalTable')

            $("#formPerkiraan").modal("show")
          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })


      }

      function lockForm (value = true) {
        document.getElementById("input_add_tanggal").disabled = value
      }

      function buttonAdd (nodpp, uruttransx) {
        tipeform = 'add'
        let _token = $("#_token").val()
        $.ajax({
          url: "{!! url('penerimaandppdetailoutstanding') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            nodpp
          },
          success: function(res) {
            urutTrans = uruttransx
            console.log(res)
            console.log('urutTrans' , urutTrans)
            let listKoreksiInvoice = []
            let listKoreksiGiro = []
            let listKoreksiRekap = []
            lockForm(false)
            resetDataTable()

            document.getElementById("input_add_kodecust").value = res[0].KODECUSTSUPP
            document.getElementById("input_add_namacust").value = res[0].NAMACUSTSUPP
            document.getElementById("input_add_nodpp").value = nodpp
            pdppSetValas("input_add_valas", res[0].Valas)
            document.getElementById("input_add_penagih").value = res[0].Penagih ? res[0].Penagih : ''

            $(".showhideitem").hide()
            setNewNoBukti()
            $(".mainpage").hide()
            $("#page2").show()

          },
          error: function (err) {
            console.log(err)
            alertify.warning('Terjadi kesalahan silahkan refresh browser')
          }
        })

      }


      function setNewNoBukti () {
        console.log('setNewNoBukti')
        let _token  = $("#_token").val()
        let kode  = 'TDP'
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
    function formatAngkaX (angka) {
      if (!Number(angka)) {
        return '0.00'
      } else {
        return formatAngka(parseFloat(angka).toFixed(2))

      }

    }

    function formatAngka (angkaString) {
      if (!Number(angkaString)) {
        return '0.00'
      }
      angkastring = parseFloat(angkaString).toFixed(2)

      let tempAngka = angkaString.split('.')

      if (tempAngka[0][0] == '-') {
        let temp2=''

        let tempAngka1 = tempAngka[0].split('-')
        for (let i = 0; i < tempAngka1[1].length; i++) {
          if (i != 0 && i % 3 == 0) {
            temp2 = ',' + temp2
          }
          temp2 = tempAngka1[1][tempAngka1[1].length - i -1] + temp2

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

      }
      temp1 += '.' + tempAngka[1]
      return temp1
    }

</script>

<script>
  /* Pewarnaan tab Outstanding DPP / Penerimaan DPP.

     PENTING: selektornya HARUS dibatasi ke #nav-tab. Dulu di sini dipakai
     $(".nav-item"), padahal menu di sidebar layout (accounting/newmaster.blade.php)
     juga memakai kelas .nav-item untuk tiap barisnya. Akibatnya setiap kali tab
     diganti, semua baris menu sidebar ikut diberi inline style
     background-color:#f8f9fa + color:#007bff - itulah sebabnya navbar samping
     berubah jadi putih. */
  function setActiveTab(idNav) {
    $("#nav-tab .nav-item").css("background-color", "#f8f9fa")
    $("#nav-tab .nav-item").css("color", "#007bff")

    let aktif = document.getElementById(idNav)
    if (!aktif) { return }
    aktif.style.backgroundColor = '#007bff'
    aktif.style.color = '#fff'
  }

  // Warna awal tab
  setActiveTab("nav-home-tab")

  /* Tab "Penerimaan DPP Sudah Otorisasi" (#nav-profile1-tab) sudah dihapus - dulu
     elemennya tetap dipasangi addEventListener sehingga melempar error di console. */
  ;['nav-home-tab', 'nav-profile-tab'].forEach(function (id) {
    let el = document.getElementById(id)
    if (el) {
      el.addEventListener('click', function () { setActiveTab(id) })
    }
  })
</script>




@endsection
