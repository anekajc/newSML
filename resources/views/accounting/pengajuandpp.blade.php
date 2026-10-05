@extends('newmasterTest')
@section('buttons')
@section('page-title', 'Pengajuan DPP')

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

{{-- ==========================================================================
     Tampilan baru daftar DPP - menyalin pola menu purchasing
     (pembelianpermintaandebetnote.blade.php).

     Layout accounting.newmaster tidak mendefinisikan .data-table seperti
     newmasterTest, jadi gaya dasar tabelnya ditulis di sini supaya halaman ini
     tidak bergantung pada layout. Semua var CSS diberi nilai cadangan.
     ========================================================================== --}}
<style>
/* Rule .card global di sebagian layout (flex + align-items:center + efek melayang
   saat hover) diperuntukkan kartu menu dashboard, bukan kartu berisi tabel. */
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

/* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai
   inline style pada <table>, yang mengalahkan `.data-table { width: 100% }`.
   Dipakai min-width, BUKAN width. */
#tabelDpp { min-width: 100%; }

/* ---------- Kolom Aksi - tombol bulat kecil warna pastel ---------- */
#tabelDpp td:first-child:not([colspan]) { vertical-align: middle; }

#tabelDpp td:first-child .po-aksi-wrap {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

#tabelDpp td:first-child .btn {
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

#tabelDpp td:first-child .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}

#tabelDpp td:first-child .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#tabelDpp td:first-child .btn-warning { color: #b45309; border-color: #fbe3bd; background: #fef3e0; }
#tabelDpp td:first-child .btn-primary { color: #2563eb; border-color: #cfdcff; background: #e8edff; }
#tabelDpp td:first-child .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }
#tabelDpp td:first-child .btn-info    { color: #0891b2; border-color: #a5f3fc; background: #ecfeff; }

/* Tombol hapus di tabel item form (#addTable) - ukuran & warna soft seperti menu Kas/Bank. */
#addTable td .btn {
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
#addTable td .btn:hover {
  filter: brightness(0.97);
  transform: translateY(-1px);
}
#addTable td .btn-success { color: #16a34a; border-color: #cdebd7; background: #e7f7ed; }
#addTable td .btn-danger  { color: #dc2626; border-color: #f7cfcf; background: #fdeaea; }

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

/* ---------- Tombol utama halaman (Tambah DPP / Close / Otorisasi) ---------- */
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

/* Tombol Close di page2/page3. Warnanya mengikuti menu purchasing
   (pembelianpermintaanagen / pembelianpermintaannonagen), yang tombol Closenya memakai
   .btn-danger: saat diam merah muda dari layout (#fef2f2 / #fecaca / #b91c1c), saat
   hover jadi merah pekat khas btn-danger Bootstrap (#bb2d3b, teks putih). */
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

/* Tombol chip (latar tint muda + teks berwarna) untuk Submit / Batal di form. */
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

/* ---------- #addTable (form Add) dan #detailTable (form Detail) ---------- */
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

/* ---------- Modal lookup DPP (#form) - baris diklik langsung ---------- */
#tabel_add_list_modal thead th {
  background: #f8f9fb !important;
  color: #6b7280 !important;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .04em;
  font-weight: 600;
  border-bottom: 1px solid #e7e9ee !important;
  border-top: none !important;
}

#tabel_add_list_modal tbody td {
  border-top: none !important;
  border-bottom: 1px solid #f1f3f5 !important;
  font-size: 13px;
  vertical-align: middle;
}

#tabel_add_list_modal tbody tr.pick-row {
  cursor: pointer;
  transition: background-color .12s;
}
#tabel_add_list_modal tbody tr.pick-row:hover td { background-color: #eef2ff; }
#tabel_add_list_modal tbody tr.pick-row.row-terpilih td { background-color: #e8edff; }

/* Kolom "v" (pilih baris): header dan kotak centang sama-sama rata tengah.
   .form-check bawaan Bootstrap 5 memberi padding-left pada wadah dan float:left
   pada inputnya, jadi text-center saja tidak cukup - floatnya harus dimatikan. */
#tabel_add_list_modal th.kolom-pilih,
#tabel_add_list_modal tbody td.kolom-pilih {
  text-align: center;
}
#tabel_add_list_modal tbody td.kolom-pilih .form-check-input {
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

#input_search_pengajuan_dpp {
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
#input_search_pengajuan_dpp:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 3px #e8edff;
}

/* Modal filter (.rt-filter) memakai tombol close Bootstrap bawaan - kembalikan
   gayanya di dalam .rt-filter saja supaya tidak tertimpa aturan halaman. */
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

/* Tombol browsing berdiri sendiri di samping input, sudutnya membulat semua. */
#page2 .input-group .btn-browsing,
#page3 .input-group .btn-browsing,
.modal-body .input-group .btn-browsing {
  border-radius: 6px !important;
}
</style>
@endsection


@section('content')

{{-- Logo untuk cetakan. Dulu diletakkan di @section('css') sehingga ikut tercetak di
     dalam <head>; sebuah <div> di dalam <head> memaksa browser menutup <head> lebih
     awal dan memulai <body> di situ. Ditaruh di @section('content') seperti halaman
     Penerimaan DPP dan purchasing (lihat newpo.blade.php). --}}
<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1" class="container-fluid mainpage">
<div class="container-fluid" >

  <!-- <div id="qrcode"></div> -->
  {{-- Tombol "+ Tambah DPP" dipindah ke po-toolbar di bawah, mengikuti pola menu
       purchasing. Baris judul lama (dengan margin-top:-30px yang membuat kartu
       menempel ke bar atas) sudah tidak dipakai: judulnya pindah ke bar atas lewat
       @section('page-title'), dan jarak ke bar atas diatur #content di blok <style>
       - sama seperti halaman purchasing dan Penerimaan DPP. --}}
<!-- <button onclick="loadAll()">tes</button> -->

<!-- <button onclick="setNewNoBukti()">tes</button> -->
<!-- <button onclick="buttonAdd('nobukti')">Add Tes</button> -->
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
  <div class="card">
    <div class="card-body" style="padding:0;">

      <div class="po-toolbar">
        <div class="po-filter-wrap">
          <label>Periode</label>
          <input type="date" class="po-filter-inp" id="dppTglAwal" value="{!! $dppTglAwal !!}">
          <span class="po-filter-sep">s/d</span>
          <input type="date" class="po-filter-inp" id="dppTglAkhir" value="{!! $dppTglAkhir !!}">
        </div>
        <input type="search" id="dppSearch" class="po-search-inp" placeholder="Cari data">
        {{-- Jumlah baris per halaman - lihat dppIkatPanjangHalaman(). --}}
        <div class="po-len-wrap">
          <label for="dppLen">Tampilkan</label>
          <select id="dppLen" class="po-len-inp">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="-1">Semua</option>
          </select>
        </div>
        <button class="po-btn-filter" type="button" id="dppBtnFilter" onclick="$('#modalFilterDpp').modal('show')">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <div class="po-toolbar-act">
          <button type="button" class="btn btn-dpp-utama" onclick="buttonAdd()">Tambah</button>
        </div>
      </div>

      {{-- #rtBar diisi lewat JS oleh ReportTable.init() - lihat dppInitReportTableSekali(). --}}
      <div id="rtBar"></div>

      <table id="tabelDpp" class="data-table po-aksi-hover">
        <thead id="tabel_header" class="text-center">
          <tr>
            <th style="padding: 4px 12px;" scope="col">Actions</th>
            <th style="padding: 4px 12px;" scope="col">No Bukti</th>
            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
            <th style="padding: 4px 12px;" scope="col">Valas</th>
            <th style="padding: 4px 12px;" scope="col">Penagih</th>
            <th style="padding: 4px 12px;" scope="col">Debet</th>
          </tr>
        </thead>
        <tbody id="tabel_data" class="text-left">
          {{-- Baris digambar renderTabelDpp() lewat JS, supaya susunan kolom hasil
               geser/sembunyi selalu konsisten dengan hasil render ulang. --}}
        </tbody>
      </table>

    </div>
  </div>


</div>
</div>

<!-- modal filter otorisasi -->
<div class="modal fade rt-filter" id="modalFilterDpp">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Pengajuan DPP
          <span class="rt-active-badge" id="dppFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterDpp').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          {{-- Hanya Otorisasi. Dropdown Penagih dihapus atas permintaan: di menu ini
               Penagih cukup jadi kolom tabel, tidak dipakai sebagai penyaring. --}}
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="dppModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="dppModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="dppResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterDpp').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="dppTerapkanFilter()">Terapkan</button>
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

      <div class="row" style="margin-top: -10px">
        <div class="col-md-3">
          <div class="row">
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
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
              <label>Penagih</label>
            </div>
            </div>
            <!-- <div class="col-4 text-right">

              </div> -->
            <div class="col-md-8">
              <div class="input-group form-group">
                <input id="input_add_penagih" type="text" class="form-control" disabled>

                <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" disabled >+</button> -->

              </div>
            </div>
          </div>

        </div>

      </div>


      </div>

<div class="container-fluid">
  <hr/>
</div>


  <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

        <table id="addTable" class="data-table"  >
          <thead class="text-center">
            <tr>
              <th style="padding: 4px 12px;" scope="col">Customer</th>
              <th style="padding: 4px 12px;" scope="col">NoFaktur</th>
              <th style="padding: 4px 12px;" scope="col">Jth Tempo</th>
              <th style="padding: 4px 12px;" scope="col">Nilai Faktur</th>
              <th style="padding: 4px 12px;" scope="col">Sudah dibayar</th>


              <th style="padding: 4px 12px;" scope="col">Actions</th>

            </tr>
          </thead>


          <tbody id="addTableData" class="" >
            <tr >

                <td colspan=6 class="text-center">Belum ada data</td>

          </tr>

          </tbody>


        </table>
  </div>


  <div class="col-md-12 mt-2 text-right">
  <button id="buttonAddItem" type="button" class="btn btn-chip-biru" onclick="buttonAddItem()">Tambah</button>
</div>


  <div id="formAddAdd" class="container-fluid showhideitem">
    <!-- <div class="line"></div> -->
    <!-- <div class="row"> -->

    <div class="col-12">


    <hr/>
    <div class="row">
      <div class="col-md-12">
        <h4 id="labelAddAddItem">Add Item</h4>
        <h4 id="labelAddEditItem">Edit Item</h4>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="row">






          <div class="col-md-2">
            <div class="form-group">
            <label>Devisi</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKodeDevisi" type="text" class="form-control" disabled>

              <button id="buttonAddListDevisi" type="button" onclick="buttonAddListDevisi()" class="btn btn-browsing" title="Cari Devisi"><i class="bi bi-search"></i></button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input  id="AddAddNamaDevisi" type="text" class="form-control" disabled>

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
            <label>Valas</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <!-- <input id="AddAddValas" type="text" class="form-control" value="IDR" disabled> -->
              <!-- <button id="buttonAddListValas" type="button" onclick="buttonAddListValas()" class="btn btn-primary" >+</button> -->

            </div>
          </div>

          <div class="col-md-1">
            <div class="form-group">
            <label>Kurs</label>
          </div>
          </div>

          <div class="col-md-2">
            <div class="input-group form-group">
              <input id="AddAddKurs" type="text" inputmode="decimal" value="1.00" class="text-right form-control" disabled>

            </div>
          </div>

        </div>
      </div>

    </div>




    <div class="row" style="margin-top: -10px">

      <div class="col-md-12">

      <div class="row">

      <div class="col-md-6">


        <div class="row">






          <div class="col-md-2">
            <div class="form-group">
            <label>Lawan</label>
          </div>
          </div>
          <!-- <div class="col-4 text-right">

            </div> -->
          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddLawan" type="text" class="form-control" disabled>
              <input id="AddAddKodeLawan" type="hidden" class="form-control" disabled>
              <button id="buttonAddListLawan" type="button" onclick="buttonAddListLawan()" class="btn btn-browsing" title="Cari Lawan"><i class="bi bi-search"></i></button>

            </div>
          </div>

          <div class="col-md-3">
            <div class="input-group form-group">
              <input id="AddAddKeteranganLawan" type="text" class="form-control" disabled>

            </div>
          </div>

        </div>
      </div>



        <!-- <div class="col-md-3">


    <div class="row">






      <div class="col-md-4">
        <div class="form-group">
        <label>Kode Brg</label>
      </div>
      </div>
      <div class="col-md-8">
        <div class="input-group form-group">
          <input id="AddAddKodeBrg" type="text" class="form-control" disabled>
          <button type="button" onclick="buttonAddListBarang()" class="btn btn-browsing" title="Cari Barang"><i class="bi bi-search"></i></button>

        </div>
      </div>

    </div>

  </div> -->

</div>
</div>

</div>

<div class="row" style="margin-top: -10px">
  <div class="col-md-6">


    <div class="row">






      <div class="col-md-2">
        <div class="form-group">
        <label>Jumlah</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-3">
        <div class="input-group form-group">
          <input id="AddAddJumlah" type="text" inputmode="decimal" value="0.00" class="text-right form-control format-number" >

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
        <label>Keterangan</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddKeterangan" type="text" value="" class="form-control" >

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
        <label>Ket. Det</label>
      </div>
      </div>
      <!-- <div class="col-4 text-right">

        </div> -->
      <div class="col-md-6">
        <div class="input-group form-group">
          <input id="AddAddKeteranganDetail" type="text" value="" class="form-control" >

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
    <label>Departemen</label>
  </div>
  </div>
  <!-- <div class="col-4 text-right">

    </div> -->
  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddKodeDepartemen" type="text" class="form-control" disabled>
      <button id="buttonAddListDepartemen" type="button" onclick="buttonAddListDepartemen()" class="btn btn-browsing" title="Cari Departemen"><i class="bi bi-search"></i></button>

    </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddNamaDepartemen" type="text" class="form-control" disabled>

    </div>
  </div>

</div>


<div class="row" id="rowCustsupp" style="margin-top: -10px">






  <div class="col-md-2">
    <div class="form-group">
    <label>Custsupp</label>
  </div>
  </div>
  <!-- <div class="col-4 text-right">

    </div> -->
  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddKodeCustsupp" type="text" class="form-control" disabled>
      <button id="buttonAddListCustsupp" type="button" onclick="buttonAddListCustsupp()" class="btn btn-browsing" title="Cari Custsupp"><i class="bi bi-search"></i></button>

    </div>
  </div>

  <div class="col-md-3">
    <div class="input-group form-group">
      <input id="AddAddNamaCustsupp" type="text" class="form-control" disabled>

    </div>
  </div>

</div>


</div>
</div>


</div>










  <!-- <div class="col-6 ">
    <div class="row">



    </div> -->
  <!-- </div> -->




  <div class="row mt-2" style="margin-top: 0">
    <div class="col-md-12 text-right mt-4">
      <button type="button" class="btn btn-batal-add" onclick="buttonAddBatal()">Batal</button>

      <button id="buttonSubmitAddAdd" type="button" onclick="submitAddAdd()" class="btn btn-chip-biru">Submit Add</button>

      <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-chip-biru">Submit</button>


      <!-- <button id="buttonSubmitAddEdit" type="button" onclick="submitAddEdit()" class="btn btn-primary" >Edit</button> -->
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



    </div>

    <!-- <div class="row "> -->

<!-- </div> -->









  </div>{{-- /#formBsGrid --}}
  </div>




  <div id="page3" style="display: none" class="mainpage container-fluid" >
  <div id="formBsGrid">

    <div class="row">
      <div class="col-8 text-left">
        <h2></h2>
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

        <div class="row" style="margin-top: -10px">
          <div class="col-md-3">
            <div class="row">
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
              <!-- <div class="col-4 text-right">

                </div> -->
              <div class="col-md-8">
                <div class="input-group form-group">
                  <input id="input_detail_penagih" type="text" class="form-control" disabled>

                </div>
              </div>
            </div>

          </div>



        </div>
















        </div>



  <div class="container-fluid">
    <hr/>

  </div>



    <div class="container-fluid mt-4" style="overflow-x: auto; padding:0; margin:0;">

          <table id="detailTable" class="data-table"  >
            <thead class="text-center">
              <tr>
                <th style="padding: 4px 12px;" scope="col">Customer</th>
                <th style="padding: 4px 12px;" scope="col">NoFaktur</th>
                <th style="padding: 4px 12px;" scope="col">Jth Tempo</th>
                <th style="padding: 4px 12px;" scope="col">Nilai Faktur</th>
                <th style="padding: 4px 12px;" scope="col">Sudah dibayar</th>



              </tr>
            </thead>


            <tbody id="detailTableData" class="" >
              <tr >

                  <td colspan=5 class="text-center">Belum ada data</td>

            </tr>

            </tbody>


          </table>
    </div>

    <div class="container-fluid">


    <div class="row" style="">
      <div class="col-6 text-left">
      </div>
      <div class="col-6 text-right">
        <button type="button" class="page3showhide otorisasishowhide btn btn-dpp-utama" onclick="submitOtorisasi()">Otorisasi</button>
      </div>
    </div>
    </div>









    </div>
  </div>


      </div>








    </div>{{-- /#formBsGrid --}}
    </div>






<!--  -->

<!-- start modal add -->
<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialo g-centered"  role="document" style="min-width: 1400px">
    <div id="" class="modal-content ">

      <div id= "" class="">
      <div class="modal-header">


          <h5 class="modal-title" id="">DPP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div id="" class="">
      <div class="modal-body">

        <div class="container-fluid" >
          <div class="row">
            <div class="col-12">
            </div>


          </div>
          <div class="row showhidelistpengajuandph">
            <div class="col-md-4">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                  <input type="hidden" class="form-control" id="input_modal_nourut" placeholder="" disabled>
                <input type="text" class="form-control" id="input_modal_nobukti" placeholder="No Bukti" disabled>
              </div>
            </div>
          </div>

            </div>
            <div class="col-md-4">
              <div class="row">


            <div class="col-md-4">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_modal_tanggal" placeholder="">
              </div>
            </div>
          </div>

            </div>

            <div class="col-md-4 ">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Penagih</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <select id="input_modal_penagih" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" >
                            <option selected value='' ></option>

                            @for ($i = 0; $i < count($penagih); $i++)
                              <option value='{{ $penagih[$i]->Penagih }}' >{{ $penagih[$i]->Penagih }}</option>

                              @endfor

                          </select>

                          <input id="input_modal_penagihedit" type="text" class="form-control" disabled>



                  </div>
                </div>
              </div>

            </div>



          </div>

          <div class="row" style="margin-top: -10px">
            <div class="col-md-4 showhidelistpengajuandph">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>Valas</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    {{-- Valas TIDAK boleh diubah user - mengikuti alur lama (lihat
                         pengajuandphtunaia.blade.php: input teks disabled berisi 'IDR').
                         Nilainya diisi program: 'IDR' saat tambah (cleanModalAdd/buttonAdd),
                         dan saat koreksi diambil dari valas header DPP yang dibuka
                         (listData[0].Valas -> #input_add_valas). Tetap dipakai sebagai
                         parameter sp_CariHutangJT dan saat simpan, karena elemen disabled
                         masih terbaca lewat .val(). Dropdown dbValas dipertahankan hanya
                         supaya nama valasnya ikut tampil. --}}
                    <select id="input_modal_valas" class="form-control" disabled>
                      @for ($i = 0; $i < count($listValas); $i++)
                        <option value="{{ trim($listValas[$i]->KodeVls) }}">{{ trim($listValas[$i]->KodeVls) }} - {{ trim($listValas[$i]->NamaVls) }}</option>
                      @endfor
                    </select>

                  </div>
                </div>
              </div>

            </div>

            <div class="col-md-4">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                  <label>JTHTempo</label>
                </div>
                </div>
                <!-- <div class="col-4 text-right">

                  </div> -->
                <div class="col-md-8">
                  <div class="input-group form-group">
                    <input id="input_modal_tanggaljatuhtempo" type="date" class="form-control text-center" >


                  </div>
                </div>
              </div>

            </div>


            <div class="col-md-4">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group form-group">
                  <button id="buttonRefreshListPengajuan" type="button" onclick="buttonRefreshListPengajuan()" class="btn btn-chip-biru" >Proses</button>
                </div>
                </div>

              </div>

            </div>


          </div>
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->

          {{-- Kotak pencarian tabel modal - diikat ke DataTables lewat
               dppIkatSearchModal(). --}}
          <div class="row mb-2">
            <div class="col-12 d-flex justify-content-end" style="padding-right: 0px;">
              <input id="input_search_pengajuan_dpp" type="search" class="form-control" placeholder="Cari data">
            </div>
          </div>

          <div class="row">
            <div class="col-12" style="overflow:auto;  max-height: 400px">
            <!-- <div class="container-fluid"> -->


            {{-- Tidak ada kolom Actions: barisnya diklik langsung untuk memilih /
                 membatalkan pilihan (lihat pengajuanKlikBaris()). --}}
            <table id="tabel_add_list_modal" class="data-table" style="overflow:auto; " >
              <thead class="text-center" style="position: sticky;
            top: 0;
            z-index: 1;">
                <tr>
                  <th class="kolom-pilih" style="padding: 4px 12px;" scope="col">v</th>
                  <th style="padding: 4px 12px;" scope="col">Customer</th>
                  <th style="padding: 4px 12px;" scope="col">JTHTempo</th>
                  <th style="padding: 4px 12px;" scope="col">Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">N. Faktur</th>
                  <th style="padding: 4px 12px;" scope="col">Sdh Dibayar</th>

                </tr>
              </thead>


              {{-- Sengaja dikosongkan. Baris placeholder dengan <td colspan> bikin
                   DataTables error (_DT_CellIndex undefined) karena jumlah sel baris
                   tidak sama dengan jumlah kolom di <thead>. Teks saat data kosong
                   ditangani opsi language.emptyTable. --}}
              <tbody id="tabel_data_add_list_modal" class="text-left" >
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
        <button type="button" class="btn btn-batal-add" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-chip-biru" onclick="submitAdd()">Submit</button>
      </div>
      </div>
















































      </div>







    </div>
  </div>

<!-- End modal add-->








@endsection

@section('js')
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script type="text/javascript">
let listInvoice = []
// let tempNoBukti = ''
let listData = []
let listPengajuan = []
let listCheckListPengajuan = []


let listPerkiraan = []
let listLawan = []
let listValas = []
let listDepartemen = []
let listDevisi = []

let listDPH = []
let listDPP = []

let listUMB = []

let tempDPPDPH = {}



let listBarang = []
let tempBarangAddAdd = {}
let tempBarangAddEdit = {}
let dataBarang = []
let tipeform = ''

$(document).ready(function(){
      // Tabel daftar DPP digambar renderTabelDpp() setelah loadAll() selesai.
      dppInitReportTableSekali()
      loadAll()

      $('.format-number').autoNumeric('init', { mDec: '2', vMin: '-9999999999999.99' })

        // searching dibiarkan aktif (dulu false) supaya kotak cari
        // #input_search_pengajuan_dpp bisa memfilter lewat API DataTables. Kotak cari
        // bawaan DataTables disembunyikan lewat opsi dom.
        $("#tabel_add_list_modal").DataTable({
          "lengthChange": false,
            "paging": false ,'order': [[1, 'asc']],
            "dom": "t",
            "language": { "emptyTable": "Belum ada data" },
            "columnDefs": [
          {"targets" :[0] , 'orderable' : false}
         // {  "className": "text-center", "targets": [4] },
       ]
      });
        dppIkatSearchModal()







        $("#tabel_add_list_custsupp").DataTable({
          "lengthChange": false,
            "paging": false ,
      });
        // const urlString = window.location.href
        // console.log(urlString)
        // const url = new URL(urlString);
        // console.log(url)
        // const searchParams = new URLSearchParams(url.search);
        // console.log(searchParams)
        //
        // const query = searchParams.get('nobukti');
        // console.log(query)


        // var xyz = jQuery.url.param("nobukti");
        // console.log(xyz)


  //   formAddListItem
});

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
  let kode  = 'DPP'
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
      document.getElementById("input_modal_nobukti").value = res[0].Nobukti
      document.getElementById("input_modal_nourut").value = res[0].Nourut

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


  // let date = new Date(), y = date.getFullYear(), m = date.getMonth();
  // console.log(date)
  // let firstDay = new Date(y, m, 1);
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value
  // console.log(periode_bulan)
  // let lastDay = new Date(y, Number(periode_bulan), 1);
  // console.log(lastDay)

  var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
  console.log(lastDayOfMonth)

  document.getElementById("input_modal_nobukti").value = ''
  document.getElementById("input_modal_nourut").value = ''
  document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  document.getElementById("input_modal_valas").value = 'IDR'
  document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)




}


function submitAdd () {
  console.log("submitAdd")
  console.log(listCheckListPengajuan)
  let _token  = $("#_token").val()
  let choice = "I"
  let nobukti  = $("#input_modal_nobukti").val()
  let nourut  = $("#input_modal_nourut").val()
  let valas  = $("#input_modal_valas").val()
  let penagih  = $("#input_modal_penagih").val()
  let tipe = 'DPP'
  let checkDate = new Date($("#input_modal_tanggal").val())
  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {
    console.log(checkDate.getFullYear())
    console.log(Number(periode_tahun))
    console.log((checkDate.getMonth() +1))
    console.log(Number(periode_bulan))
      alertify.warning("Tanggal tidak sesuai periode");
      return
  }


  let tanggal  = $("#input_modal_tanggal").val()

  if(!listCheckListPengajuan.length) {
    alertify.warning("Tidak ada item dipilih")

  }

  let jmlrecord = tipeform == "add" ? 0 : 1

  console.log({
    tempData : listCheckListPengajuan ,
    choice,
    valas,
    nobukti,
    nourut,
    tipe,
    tanggal,
    penagih
  })

  // listCheckListPengajuan

  $.ajax({
      url: "{!! url('pengajuandppspadd') !!}",
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
        jmlrecord,
        penagih
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('DPP telah ditambah');

          // $('.showhideitem').hide();
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true

          // refreshDataTable(nobukti)

          $("#form").modal('toggle')
          buttonKoreksi(nobukti)

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
  let kurs  = toNum($("#AddAddKurs").val())
  let lawan  = $("#AddAddLawan").val()
  // let kodelawan  = $("#AddAddKodeLawan").val()
  let jumlah  = toNum($("#AddAddJumlah").val())
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

function submitAddEdit () {



  let checkDate = new Date($("#input_add_tanggal").val())

  let periode_bulan = document.getElementById("periode_bulan").value
  let periode_tahun = document.getElementById("periode_tahun").value

  if ( checkDate.getFullYear()  !== Number(periode_tahun)  || (checkDate.getMonth() +1) !== Number(periode_bulan) ) {

      alertify.warning("Tanggal tidak sesuai periode");
      return
  }
  let choice = "U"
  let _token  = $("#_token").val()
  let nobukti  = $("#input_add_nobukti").val()
  let nourut  = $("#input_add_nourut").val()
  let transaksi  = $("#input_add_transaksi").val()
  let note  = $("#input_add_kepadaterima").val()
  let kodeperkiraan  = $("#input_add_kodeperkiraan").val()
  let tanggal = $("#input_add_tanggal").val()

  let lampiran = 0
  let keterangan2 = ''


  let kodedevisi  = $("#AddAddKodeDevisi").val()
  let valas  = $("#AddAddValas").val()
  let kurs  = toNum($("#AddAddKurs").val())
  let lawan  = $("#AddAddLawan").val()
  let jumlah  = toNum($("#AddAddJumlah").val())
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


  let urut = tempBarangAddEdit.Urut

  let custsuppP = tempBarangAddEdit.CustSuppP
  let custsuppL = tempBarangAddEdit.CustSuppL
  let noaktivaP = tempBarangAddEdit.NoAktivaP
  let noaktivaL = tempBarangAddEdit.NoAktivaL
  let statusaktivaP = tempBarangAddEdit.StatusAktivaP
  let statusaktivaL = tempBarangAddEdit.StatusAktivaL

  let nobon = $("#input_add_bon").val()
  let kodebag = '-'

  let kodeP = tempBarangAddEdit.KodeP
  let kodeL = tempBarangAddEdit.KodeL
  let statusgiro = tempBarangAddEdit.StatusGiro
  let simbol = $("#input_add_simbol").val()
  let flagsimbol = ''
  let kodecost = ''
  let kodesubcost = ''
  let nodph = tempBarangAddEdit.NODPH
  let urutdph = tempBarangAddEdit.urutDPH
  let dppdph = ''
  let tp = ''
  let ppklx = ''
  let nofaktur = ''
  let plok = 0
  let nobons = ''
  let jmlrecord = tipeform == 'add' ? 0 : 1
  let notitipan = tempBarangAddEdit.notitipan
  let uruttitipan = tempBarangAddEdit.URUTTITIPAN
  let pSKB = 0
  let perkiraanx = transaksi == 'BBK' ? lawan : kodeperkiraan
  let lawanx = transaksi == 'BBK' ? kodeperkiraan : lawan







  console.log({
    tipeform,
    _token,
    nobukti ,
    nourut,
    transaksi,
    note,
    kodeperkiraan ,
    tanggal,
    lampiran ,
    keterangan2 ,
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
    pSKB
  })



  $.ajax({
      url: "{!! url('bankspadd') !!}",
      type: "post",
      async: false,
      data: {
        choice,
        tipeform,
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
        pSKB
      },
      success: function(res) {
        console.log(res ,'!')

        if (res == 1) {
          // $("#form").modal('toggle')
          alertify.success('Bank telah ditambah');
          loadAll()
          // buttonCloseForm()
          tipeform = 'edit'
          // document.getElementById("buttonAddListCustomer").disabled = true
          // document.getElementById("input_add_tanggal").disabled = true
          $('.showhideitem').hide();
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



function buttonAddDelete (index) {


    let barangDelete = listData[index]



    console.log(barangDelete)

    // return


    alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus faktur '+ barangDelete.NoFaktur +' ?',
        function() {
          let _token = $("#_token").val()
          let choice = "D"


          let nobukti = barangDelete.Nobukti
          let nourut = ''
          let valas = ''
          let urut = barangDelete.Urut
          let kodecustsupp = ''
          let tanggal = ''
          let tipe = ''
          let nofaktur = ''
          let dibayar = 0
          let perkiraan = ''
          let kl = 0
          let lb = 0
          let noinvoice = ''
          let tglinvoice = ''
          let pcopy = 0



          $.ajax({
              url: "{!! url('pengajuandppspkoreksi') !!}",
              type: "post",
              async: false,
              data: {
                _token,
                choice,
                valas,
                nobukti,
                nourut,
                tipe,
                tanggal,
                urut,
                kodecustsupp,
                nofaktur,
                dibayar,
                perkiraan,
                kl,
                lb,
                noinvoice,
                tglinvoice,
                pcopy
              },
              success: function(res) {
                console.log(res ,'!')

                if (res == 1) {
                  // $("#form").modal('toggle')
                  alertify.success('DPP telah dihapus');

                  // $('.showhideitem').hide();
                  // loadAll()
                  // buttonCloseForm()
                  tipeform = 'edit'
                  loadAll()
                  refreshDataTable(nobukti)

                }
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
  console.log(nourut)
  let _token = $("#_token").val();
  console.log("buttonAddPickInvoice")
  let tempData = []
  // let checkQnt = 0
  let checkMinus = 0
  console.log(listInvoice)
    listInvoice.forEach((item, i) => {
      console.log(document.getElementById(`add_checkbox${i}`).checked)
      if (document.getElementById(`add_checkbox${i}`).checked) {

        let checkNilai = $(`#add_inputQnt${i}`).val();
        let checkKurs = $(`#add_inputKurs${i}`).val();
        let checkNilaiRp = $(`#add_inputQntRp${i}`).val();
        // add_inputKeterangan
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
    console.log(tempData)

    if (!tempData.length) {
      alertify.warning("Tidak ada item dipilih");
      return
    }

    if (checkMinus) {
      alertify.warning("Qnt <= 0");
      return
    }

    $.ajax({
        url: "{!! url('kreditnotespadd') !!}",
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
          console.log(res ,'!')

          if (res == 1) {
            // $("#form").modal('toggle')
            alertify.success('KN telah ditambah');
            loadAll()
            // buttonCloseForm()
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

function buttonAddListLawan () {
  listLawan = []

  console.log('buttonAddListLawan')


  let _token = $("#_token").val();
  let perkiraan = $("#input_add_kodeperkiraan").val();
  let transaksi = $("#input_add_transaksi").val();
  if(!perkiraan) {
    alertify.warning("Pilih perkiraan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('banklistlawan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      perkiraan,
      transaksi
    },
    success: function(res) {
      console.log(res)
      listLawan  = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Perkiraan}</td>
        <td>${item.Keterangan}</td>
        <td>${item.Simbol}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickLawan(${i},'${item.Perkiraan}' , '${item.Keterangan}' , '${item.Simbol}', '${item.Kode}' )" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_lawan").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListLawan').show();
        $("#form").modal('toggle')
      } else {
        alertify.warning("Perkiraan tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPP (dataLawan) {
  listDPP = []

  console.log('modalDPP')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('banklistdpp') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      valas
    },
    success: function(res) {
      console.log(res)
      listDPP = res
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.Nobukti}</td>
        <td>${item.KODECUSTSUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-right">${item.DIBAYAR ? formatAngka(parseFloat(item.DIBAYAR).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.KL ? formatAngka(parseFloat(item.KL).toFixed(2)) : '0.00'}</td>
        <td class="text-right">${item.LB ? formatAngka(parseFloat(item.LB).toFixed(2)) : '0.00'}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickDPP(${i} , '${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-plus"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dpp").innerHTML = rowTable

      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPP').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("DPP tidak ditemukkan")
      }


    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function modalDPHUHTBBM (dataLawan) {


  console.log('modalDPHUHTBBM')

  console.log(dataLawan)


  let _token = $("#_token").val();
  let valas = $("#AddAddValas").val();

  $.ajax({
    url: "{!! url('banklistcustsuppumb') !!}",
    type: "get",
    async: false,
    data: {
      _token,
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.KODESUPP}</td>
        <td>${item.NAMACUSTSUPP}</td>
        <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickCustDPHUHTBBM( '${item.KODESUPP}', '${item.NAMACUSTSUPP}','${dataLawan.Perkiraan}', '${dataLawan.Kode}' , '${dataLawan.Keterangan}')" type="button" ><i class="bi bi-arrow-right"></i></button></td>

        </tr>`
      });






      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=5>Tidak ada data</td></tr>`
      // }
      document.getElementById("tabel_data_add_list_dphuhtbbm_custsupp").innerHTML = rowTable
      document.getElementById("input_dphuhtbbm_namacustsupp").value = ''
      document.getElementById("input_dphuhtbbm_kodecustsupp").value = ''

      document.getElementById("tabel_data_add_list_dphuhtbbm").innerHTML = `
        <tr>
          <td colspan=10 class="text-center">Data tidak ditemukkan</td>
        </tr>
      `


      if (res.length) {

        $('.showhidemodalbodyadd').hide();
        $('#modalAddListDPHUHTBBM').show();
        // $("#form").modal('toggle')
      } else {
        alertify.warning("C tidak ditemukkan")
      }


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

  $("#form").modal('toggle')
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

function lockFormAdd (value = true) {
  console.log('lockFormAdd' , value)
  // document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_modal_tanggal").disabled = value
  document.getElementById("input_modal_penagih").disabled = value
  // Valas ikut nilai `value`: terbuka saat tambah (buttonAdd memanggil dengan false)
  // supaya user bisa pilih sendiri, tetap terkunci saat koreksi (default true) karena
  // valasnya diambil dari header DPP yang dibuka.
  document.getElementById("input_modal_valas").disabled = value
  //
  //
  // document.getElementById("buttonAddListCustomer").disabled = false
  // document.getElementById("buttonAddListNoInvoice").disabled = false

}

// function lockFormAdd () {
//   document.getElementById("input_add_tanggal").disabled = true
//   document.getElementById("input_add_bon").disabled = true
//   document.getElementById("input_add_kepadaterima").disabled = true
//   document.getElementById("buttonAddListPerkiraan").disabled = true
//   document.getElementById("input_add_transaksi").disabled = true
//
// }

function lockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = true
  document.getElementById("buttonAddListLawan").disabled = true
  document.getElementById("input_modal_valas").disabled = true
  document.getElementById("buttonAddListDevisi").disabled = true

}

function unlockFormAddAdd () {
  document.getElementById("buttonAddListDepartemen").disabled = false
  document.getElementById("buttonAddListLawan").disabled = false
  // #input_modal_valas sengaja tidak ikut dibuka - valas tidak boleh diubah user.
  document.getElementById("buttonAddListDevisi").disabled = false
}


function refreshDataTableDet (nobukti) {
  console.log('refreshDataTableDet' , nobukti)
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

      // $('#formAddAdd').hide();
      if (!res.length) {
          alertify.success('Data Habis')
          // $("#form").modal('toggle')
          $('.mainpage').hide();
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



                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_nobukti").value = listData[0].NoBukti

        // document.getElementById("input_detail_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_detail_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_detail_nobukti").value = listData[0].NoBukti
        document.getElementById("input_detail_tanggal").valueAsDate = new Date(listData[0].Tanggal)
        document.getElementById("input_detail_valas").value = listData[0].Valas










    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
      resRefresh = 0;
    }

  })
}




// SP_TempKoreksiDPP mengisi dbTempHutPiutJT dengan DUA macam baris untuk user yang sama:
//   1. baris milik DPP yang dibuka  -> Nobukti terisi (mis. SML/DPP/00001/0926), IsTerima 1
//   2. baris faktur outstanding yang BELUM masuk DPP, sebagai kandidat -> Nobukti = '0'
// Query getDetail mengembalikan keduanya tanpa ORDER BY, jadi urutannya tidak pasti dan
// baris pertama bisa saja baris kandidat. Kalau itu yang terjadi, No Bukti terbaca '0' dan
// Tanggal jadi 01/01/1970 (Nobukti '0' tidak ketemu di DBDPH, jadi Tanggal null).
// Karena itu ambil hanya baris milik nobukti yang sedang dibuka.
function dppBarisMilikNobukti (res, nobukti) {
  let kunci = String(nobukti || '').trim()
  return (res || []).filter(function (item) {
    return String(item.Nobukti || '').trim() === kunci
  })
}

function refreshDataTable (nobukti) {
  console.log('refreshDataTable' , nobukti)
  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('pengajuandppspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      listData = dppBarisMilikNobukti(res, nobukti)
      // console.log(res)

      $('#formAddAdd').hide();
      if (!listData.length) {
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
                  <td>${item.JatuhTempo ? formatDate(item.JatuhTempo) : '' }</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.Kredit)).toFixed(2)) }</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.diBayar)).toFixed(2)) }</td>




                  <td class="text-center">

                    <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDelete(${i} )"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("addTableData").innerHTML = rowTable


        document.getElementById("input_add_nobukti").value = listData[0].Nobukti

        // document.getElementById("input_add_transaksi").value = listData[0].NamaCustSupp
        // document.getElementById("input_add_alamatcustomer").value = listData[0].Alamat1
        // document.getElementById("input_add_nobukti").value = listData[0].NoBukti
        // Pakai .value + formatDate(), bukan valueAsDate: valueAsDate membaca objek Date
        // sebagai UTC, sedangkan "2026-09-09 00:00:00" diparse sebagai jam 00:00 WIB,
        // sehingga tanggalnya mundur sehari (tampil 08). formatDate() memakai getDate()
        // lokal, jadi tanggalnya sama persis dengan yang tersimpan di DBDPH.
        document.getElementById("input_add_tanggal").value = formatDate(listData[0].Tanggal)
        document.getElementById("input_add_valas").value = listData[0].Valas
        document.getElementById("input_add_penagih").value = listData[0].Penagih









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
    url: "{!! url('pengajuandppspotorisasi') !!}",
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





function buttonDetail (nobukti, form ) {
  console.log('buttonDetail' , nobukti )


  let _token = $("#_token").val();
  listData = []
  $.ajax({
    url: "{!! url('pengajuandppspdetail') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti

    },
    success: function(res) {
      console.log(res)
      // Sama seperti refreshDataTable(): buang baris kandidat ber-Nobukti '0' supaya
      // No Bukti dan Tanggal di page3 tidak terbaca dari baris yang bukan milik DPP ini.
      let listDataDetail = dppBarisMilikNobukti(res, nobukti)

      if (!listDataDetail.length) {
          alertify.warning('Data tidak ditemukkan')
          // $("#form").modal('toggle')
          $('.mainpage').hide();
          $('#page1').show();
          return
      }
      // dataTableAdd = res

      let rowTable = ``
      listDataDetail.forEach((item, i) => {

        // <td>${item.TipeTrans == 'BBK' ? item.Lawan : item.Perkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.NamaLawan : item.NamaPerkiraan}</td>
        // <td>${item.TipeTrans == 'BBK' ? item.Perkiraan : item.Lawan }</td>
        // <td>${item.TipeTrans == 'BBK' ?  item.NamaPerkiraan : item.NamaLawan }</td>

              rowTable += `
                <tr>
                  <td>${item.NamaCustSupp}</td>

                  <td>${item.NoFaktur}</td>
                  <td>${item.JatuhTempo ? formatDate(item.JatuhTempo ) : '' }</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.Kredit)).toFixed(2)) }</td>
                  <td class="text-right">${formatAngka(parseFloat(Number(item.diBayar)).toFixed(2)) }</td>




                </tr>

              `

              // <button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
      });

      document.getElementById("detailTableData").innerHTML = rowTable


        document.getElementById("input_detail_nobukti").value = listDataDetail[0].Nobukti

        // document.getElementById("input_detail_transaksi").value = listDataDetail[0].NamaCustSupp
        // document.getElementById("input_detail_alamatcustomer").value = listDataDetail[0].Alamat1
        // document.getElementById("input_detail_nobukti").value = listDataDetail[0].NoBukti
        // Sama seperti di page2: valueAsDate menggeser tanggal mundur sehari karena
        // dibaca sebagai UTC, jadi diisi lewat .value dengan formatDate().
        document.getElementById("input_detail_tanggal").value = formatDate(listDataDetail[0].Tanggal)
        document.getElementById("input_detail_valas").value = listDataDetail[0].Valas
        document.getElementById("input_detail_penagih").value = listDataDetail[0].Penagih


        $('.page3showhide').hide();
        if (form == 'otorisasi') {

          $('.otorisasishowhide').show();
        }
        $('.mainpage').hide();
        $('#page3').show();





    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')

    }

  })


}





// function buttonKoreksi (nobukti ) {
//   console.log('buttonKoreksi' , nobukti )
//
//
//
//   refreshDataTable(nobukti)
//   if (listData.length) {
//
//     $('#page1').hide();
//     $('#page3').show();
//   }
// }

function buttonKoreksi (nobukti ) {
  console.log('buttonKoreksi' , nobukti )

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'edit'
  // cleanFormAdd()
  refreshDataTable(nobukti)

  if (listData.length) {
    if(listData[0].IsOtorisasi1 == 1) {
      alertify.warning("Sudah diotorisasi")
    } else {

      $('.mainpage').hide();
      $('#page2').show();
    }
  }

  return

    $.ajax({
      url: "{!! url('pengajuandphspdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        tglawal,
        tglakhir,
        valas,
        tipe,
        tipelist,
        kodecustsupp
      },
      success: function(res) {
        console.log(res)
        dataTable = res
        // listPengajuan = res
        // listCheckListPengajuan = []

        rowTable = ''

        $('#tabel_add_list').DataTable().destroy();
        res.forEach((item, i) => {
          rowTable += `
        <tr>
        <td><div class="form-check text-center">
            <input id="pengajuanCheckList${i}" onchange="pengajuanCheckList(${i},this.id); dppTandaiBarisTerpilih(${i})" class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
            </div></td>
        <td>${item.NamaCustSupp}</td>
        <td>${formatDate(item.JatuhTempo)}</td>
        <td>${item.NoFaktur}</td>
        <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.JmlDibayar).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.diBayar).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.KL).toFixed(2))}</td>
        <td class="text-right">${formatAngka(parseFloat(item.LB).toFixed(2))}</td>
        <td>${ item.Perkiraan ? item.Perkiraan: ''}</td>
        <td>${ item.NOInvoice ? item.NOInvoice: ''}</td>
        <td>${ item.TglInvoice ? formatDate(item.TglInvoice) : ''}</td>
        </tr>`
        });


        document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable
        // document.getElementById("tabel_data_add_list_modal").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`

        $('#page1').hide();
        $('#page2').show();
        $('#formAddAdd').show();
      //   $("#tabel_add_list_modal").DataTable({
      //     "lengthChange": false,
      //       "paging": false ,'order': [[1, 'asc']],
      //       "searching" : false,
      //       "columnDefs": [
      //     {"targets" :[0] , 'orderable' : false}
      //    // {  "className": "text-center", "targets": [4] },
      //  ]
      // });

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })





  $('.mainpage').hide();
  $('#page2').show();
}

/* Klik di mana saja pada baris = memilih / membatalkan pilihan faktur.
   Klik yang mengenai kotak centang dibiarkan lewat supaya tidak terhitung dua kali
   (checkbox akan memicu onchange -> pengajuanCheckList sendiri). */
function pengajuanKlikBaris (e, index) {
  if (e && e.target && e.target.classList && e.target.classList.contains('form-check-input')) {
    return
  }

  let kotak = document.getElementById(`pengajuanCheckList${index}`)
  if (!kotak) { return }

  kotak.checked = !kotak.checked
  pengajuanCheckList(index, kotak.id)
  dppTandaiBarisTerpilih(index)
}

// Sorot baris yang sedang terpilih.
function dppTandaiBarisTerpilih (index) {
  let kotak = document.getElementById(`pengajuanCheckList${index}`)
  if (!kotak) { return }
  let baris = kotak.closest('tr')
  if (!baris) { return }

  if (kotak.checked) {
    baris.classList.add('row-terpilih')
  } else {
    baris.classList.remove('row-terpilih')
  }
}

function pengajuanCheckList( index ,id)  {
  let data = listPengajuan[index]
  console.log(data)
  if (document.getElementById(`pengajuanCheckList${index}`).checked) {
    console.log('add baru')
    // kalo tidak ada data di adddataarray langsung add , kalo ada data cek namacustsupp

    if (!listCheckListPengajuan.length) {

      listCheckListPengajuan.push(data)
      console.log(listCheckListPengajuan)
      return
    }


    // if (listCheckListPengajuan[0].KodeCustSupp != data.KodeCustSupp) {
    //   alertify.warning('Cust Supp berbeda')
    //   document.getElementById(`pengajuanCheckList${index}`).checked = !document.getElementById(`pengajuanCheckList${index}`).checked
    //   console.log(listCheckListPengajuan)
    //   return
    // }
    listCheckListPengajuan.push(data)


    // console.log(document.getElementById(`outstandingCheckList${index}`).checked)
    // console.log(!document.getElementById(`outstandingCheckList${index}`).checked)
    // document.getElementById(`outstandingCheckList${index}`).checked = !document.getElementById(`outstandingCheckList${index}`).checked
  } else {
    let check = listCheckListPengajuan.findIndex(el => el.NoFaktur === data.NoFaktur );
    console.log('hapus')
    listCheckListPengajuan.splice(check, 1)
  }

}

function buttonRefreshListPengajuan ( tipelist = 0 , kodecustsupp = '') {
  let tglawal = formatDate(new Date())

  let tglakhir = $("#input_modal_tanggaljatuhtempo").val();
  let valas = $("#input_modal_valas").val();


  let tipe = 'PT'

  if ( tipeform == 'edit') {
    tipelist = 1
    kodecustsupp = listData[0].KodeCustSupp
  }

  let _token = $("#_token").val();
  console.log("buttonRefreshListPengajuan")
  console.log(
    {
      tglawal,
      tglakhir,
      valas,
      tipe,
      tipelist,
      kodecustsupp
    }
  )
  $.ajax({
    url: "{!! url('pengajuandppsplistpengajuan') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      tglawal,
      tglakhir,
      valas,
      tipe,
      tipelist,
      kodecustsupp
    },
    success: function(res) {
      console.log(res)

      listPengajuan = res
      listCheckListPengajuan = []

      rowTable = ''

      $('#tabel_add_list_modal').DataTable().destroy();
      // Baris diklik langsung untuk memilih (tidak ada tombol Actions). Kotak centang
      // tetap ditampilkan sebagai penanda terpilih, tapi tidak perlu dikenai kursor.
      res.forEach((item, i) => {
        rowTable += `
      <tr class="pick-row" onclick="pengajuanKlikBaris(event, ${i})">
      <td class="kolom-pilih">
          <input id="pengajuanCheckList${i}" onchange="pengajuanCheckList(${i},this.id); dppTandaiBarisTerpilih(${i})" class="form-check-input" type="checkbox" value="">
          </td>
      <td>${item.NamaCustSupp}</td>
      <td>${formatDate(item.JatuhTempo)}</td>
      <td>${item.NoFaktur}</td>
      <td class="text-right">${formatAngka(parseFloat(item.Kredit).toFixed(2))}</td>
      <td class="text-right">${formatAngka(parseFloat(item.JmlDibayar).toFixed(2))}</td>

      </tr>`
      });

      // Saat data kosong tbody dibiarkan kosong (bukan diisi baris <td colspan>):
      // DataTables mengambil jumlah kolom dari <thead>, jadi baris bersel tunggal
      // memicu _DT_CellIndex undefined saat init. Pesan kosong dari language.emptyTable.
      document.getElementById("tabel_data_add_list_modal").innerHTML = rowTable

      $("#tabel_add_list_modal").DataTable({
        "lengthChange": false,
          "paging": false ,'order': [[1, 'asc']],
          "dom": "t",
          "language": { "emptyTable": "Tidak ada data" },
          "columnDefs": [
        {"targets" :[0] , 'orderable' : false}
       // {  "className": "text-center", "targets": [4] },
     ]
    });

    let inputCari = document.getElementById('input_search_pengajuan_dpp')
    if (inputCari) {
      inputCari.value = ''
      $('#tabel_add_list_modal').DataTable().search('').draw()
    }

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddItem () {
  console.log('buttonAdd' )

  let akses = $("#akses_istambah").val();
  let _token = $("#_token").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'edit'
  cleanModalAdd()
  lockFormAdd()
  // setNewNoBukti()
  document.getElementById("input_modal_nobukti").value = $("#input_add_nobukti").val()
  // Valas koreksi = valas header DPP yang dibuka. Di-trim karena KodeVls dari SQL
  // Server bertipe char (berspasi), sedangkan value <option> sudah di-trim.
  document.getElementById("input_modal_valas").value = ($("#input_add_valas").val() || '').trim()
  document.getElementById("input_modal_tanggal").valueAsDate = new Date($("#input_add_tanggal").val())
  document.getElementById("input_modal_penagihedit").value = $("#input_add_penagih").val()
  document.getElementById("input_modal_tanggaljatuhtempo").valueAsDate = new Date()

  $("#input_modal_penagihedit").show()
  $("#input_modal_penagih").hide()

  $(".showhidelistpengajuandph").show();
  buttonRefreshListPengajuan()

  // $('.showhideitem').hide();
  // document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  // document.getElementById("input_modal_valas").value = 'IDR'
  // document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)




  $("#form").modal('toggle')
  // return
}

// function buttonAddItem () {
//
//   let kodecustsuppx = listData[0].KodeCustSupp
//   let tipelist = 1
//
//   let periode_bulan = document.getElementById("periode_bulan").value
//   let periode_tahun = document.getElementById("periode_tahun").value
//   // console.log(periode_bulan)
//   // let lastDay = new Date(y, Number(periode_bulan), 1);
//   // console.log(lastDay)
//
//   var lastDayOfMonth = new Date(periode_tahun, periode_bulan, 0);
//   console.log(lastDayOfMonth)
//
//
//   document.getElementById("input_modal_valas").value = 'IDR'
//   document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)
//   document.getElementById("input_modal_nobukti").value = $("#input_add_nobukti").val();
//   document.getElementById("input_modal_tanggal").value = $("#input_add_tanggal").val();
//
//   console.log('buttonAddItem' , tipelist, kodecustsuppx)
//   $(".showhidelistpengajuandph").hide();
//   buttonRefreshListPengajuan(tipelist , kodecustsuppx)
//
//
//   $("#form").modal('toggle')
// }

// function buttonKoreksi (nobukti) {
//
//   let akses = $("#akses_iskoreksi").val();
//   let _token = $("#_token").val();
//   if (!Number(akses)) {
//     alertify.warning('No access')
//     return
//   }
//   lockFormAdd()
//   tipeform = 'edit'
//
//
// }

function buttonAdd () {
  console.log('buttonAdd' )

  let akses = $("#akses_istambah").val();
  let _token = $("#_token").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }


  tipeform = 'add'
  cleanModalAdd()
  lockFormAdd(false)
  setNewNoBukti()

  $(".showhidelistpengajuandph").show();
  buttonRefreshListPengajuan()

  // document.getElementById("input_modal_nobukti").value = $("#input_add_nobukti").val()
  document.getElementById("input_modal_valas").value = 'IDR'
  document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  document.getElementById("input_modal_penagihedit").value = ''
  document.getElementById("input_modal_tanggaljatuhtempo").valueAsDate = new Date()

  $("#input_modal_penagihedit").hide()
  $("#input_modal_penagih").show()

  // $('.showhideitem').hide();
  // document.getElementById("input_modal_tanggal").valueAsDate = new Date()
  // document.getElementById("input_modal_valas").value = 'IDR'
  // document.getElementById("input_modal_tanggaljatuhtempo").value = formatDate(lastDayOfMonth)




  $("#form").modal('toggle')
  return


  document.getElementById("input_add_tanggal").disabled = false
  document.getElementById("input_add_bon").disabled = false
  document.getElementById("input_add_kepadaterima").disabled = false
  document.getElementById("buttonAddListPerkiraan").disabled = false
  document.getElementById("input_add_transaksi").disabled = false




  document.getElementById("addTableData").innerHTML = `<td colspan=12 class="text-center">Belum ada data</td>`

  // unlockFormAdd()
  $('.showhideitem').hide();
  // $('.showhideform').hide();
  $('#formAdd').show();
  // $("#form").modal('toggle')

  // input_add_nobukti
  // document.getElementById("input_add_nobukti").value = nobukti

  cleanFormAdd()
  // setNewNoBukti()
  document.getElementById("input_add_transaksi").value = 'BBK'
  onChangeTransaksi()

  $('.mainpage').hide();
  $('#page2').show();

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
  setNum("AddAddKurs", tempBarangAddEdit.Kurs)

  document.getElementById("AddAddLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.Perkiraan : tempBarangAddEdit.Lawan
  document.getElementById("AddAddKeteranganLawan").value = tempBarangAddEdit.TipeTrans == 'BBK' ? tempBarangAddEdit.NamaPerkiraan : tempBarangAddEdit.NamaLawan

  console.log(tempBarangAddEdit.Debet)
  setNum("AddAddJumlah", tempBarangAddEdit.Debet)
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

/* ==========================================================================
   Tabel daftar DPP - pola ReportTable (geser kolom + sembunyikan kolom + bar
   kolom tersembunyi), disalin dari pembelianpermintaandebetnote.blade.php.

   Dipatok, bukan diambil dari window.location - harus sama persis dengan
   PengajuanDPPController::HREF.
   ========================================================================== */
const DPP_HREF = 'pengajuandpp'

let dppCart = []
let dataDpp = []

function dppBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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

function dppKolomTampil () {
  return (dppCart || []).filter(c => Number(c[2]) === 1)
}

function dppKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

function dppFormatAngkaDes (nilai, des) {
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

function dppRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return dppFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

function dppHeadHtml (cols) {
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

let dppRtSudahInit = false

function dppInitReportTableSekali () {
  if (dppRtSudahInit || typeof ReportTable === 'undefined') { return }
  dppRtSudahInit = true

  ReportTable.init({
    table    : '#tabelDpp',
    bar      : '#rtBar',
    onChange : renderTabelDpp
  })

  // Sebagian layout memasang penangan klik sendiri di <thead>; teruskan klik pada
  // roda gigi / pegangan geser ke penangan milik ReportTable.
  let dppGuardUlangKlik = false
  let thead = document.getElementById('tabel_header')
  if (thead) {
    thead.addEventListener('click', function (e) {
      if (dppGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      dppGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      dppGuardUlangKlik = false
    }, true)
  }
}

function dppPindahBar () {
  let bar = document.getElementById('rtBar')
  let tabel = document.getElementById('tabelDpp')
  if (!bar || !tabel) { return }

  let acuan = tabel
  if ($.fn.DataTable.isDataTable('#tabelDpp')) {
    acuan = document.getElementById('tabelDpp_wrapper') || tabel
  }

  if (acuan.previousElementSibling !== bar) {
    acuan.parentNode.insertBefore(bar, acuan)
  }
}

function dppIkatSearch () {
  let input = document.getElementById('dppSearch')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    $('#tabelDpp').DataTable().search(input.value).draw()
  })
}

let dppPanjangHalaman = 10
function dppIkatPanjangHalaman () {
  let sel = document.getElementById('dppLen')
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(dppPanjangHalaman)

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    dppPanjangHalaman = (n === -1 || n > 0) ? n : 10
    $('#tabelDpp').DataTable().page.len(dppPanjangHalaman).draw()
  })
}

function dppIkatPeriode () {
  let awal  = document.getElementById('dppTglAwal')
  let akhir = document.getElementById('dppTglAkhir')
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

// Kotak cari di modal DPP - memfilter #tabel_add_list_modal lewat API DataTables.
function dppIkatSearchModal () {
  let input = document.getElementById('input_search_pengajuan_dpp')
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    if ($.fn.DataTable.isDataTable('#tabel_add_list_modal')) {
      $('#tabel_add_list_modal').DataTable().search(input.value).draw()
    }
  })
}

/* ---------- Filter (menggantikan tab otorisasi) ---------- */
let dppFilterOtorisasi = 'SEMUA'

function dppOtorisasi (item) {
  return Number(item.IsOtorisasi1) === 1 ? 'Sudah' : 'Belum'
}

function dppUpdateFilterBadge () {
  let jml = (dppFilterOtorisasi !== 'SEMUA' ? 1 : 0)
  let badge = document.getElementById('dppFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function dppTerapkanFilter () {
  dppFilterOtorisasi = $('#dppModalOtorisasi').val() || 'SEMUA'
  dppUpdateFilterBadge()
  $('#modalFilterDpp').modal('hide')
  renderTabelDpp()
}

function dppResetFilter () {
  dppFilterOtorisasi = 'SEMUA'
  $('#dppModalOtorisasi').val('SEMUA')
  dppUpdateFilterBadge()
  $('#modalFilterDpp').modal('hide')
  renderTabelDpp()
}

/* ---------- Simpan / muat susunan kolom ---------- */
window.g_href = DPP_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  dppCart.forEach((c) => {
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
      href     : DPP_HREF
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal menyimpan pengaturan kolom')
    }
  })
}

// Tombol "Reset kolom". Memakai endpoint milik menu ini sendiri karena
// HeaderTableController belum punya cabang untuk href 'pengajuandpp'.
window.doSetHeader = function (mode, reset) {
  if (!reset) { return }

  $.ajax({
    url   : "{!! url('pengajuandppresetheader') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val()
    },
    success : function (res) {
      dppCart = dppBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = dppCart
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

function loadAll () {
  document.getElementById('tabel_data').innerHTML =
    '<tr><td colspan="20" class="text-center">' + loadingHtml('Memuat data...') + '</td></tr>'

  $.ajax({
    url: "{!! url('pengajuandpploadall') !!}",
    type: "get",
    async: true,
    data: {
      tglawal: $('#dppTglAwal').val(),
      tglakhir: $('#dppTglAkhir').val()
    },
    success: function(res) {
      dppCart = dppBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      window.gcart_header = dppCart
      dataDpp = res.tempOutstanding || []
      renderTabelDpp()
    },
    error: function (err) {
      console.error("Load failed:", err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function renderTabelDpp () {
  window.g_modeReport = 1
  window.gcart_header = dppCart

  if ($.fn.DataTable.isDataTable('#tabelDpp')) {
    $('#tabelDpp').DataTable().destroy()
  }

  let cols = dppKolomTampil()
  let kolomRender = cols.map(dppKolomRender)

  let thead = document.getElementById('tabel_header')
  thead.innerHTML = dppHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
    baris.insertAdjacentHTML('beforeend', `
      <th style="padding: 4px 12px;" scope="col">Oto</th>
      <th style="padding: 4px 12px;" scope="col">User Oto</th>
      <th style="padding: 4px 12px;" scope="col">Tgl Oto</th>
    `)
  }

  let dataTampil = dataDpp || []
  if (dppFilterOtorisasi !== 'SEMUA') {
    dataTampil = dataTampil.filter(function (item) { return dppOtorisasi(item) === dppFilterOtorisasi })
  }

  let rowTable = ''
  dataTampil.forEach((item) => {
    let isOtorisasi = Number(item.IsOtorisasi1) || 0

    let tombolAksi = `<button class="btn btn-warning btn-sm" type="button" title="Detail" onclick="buttonDetail('${item.NoBukti}' , 'detail')"><i class="bi bi-info"></i></button>`
    if (isOtorisasi === 1) {
      tombolAksi += `
        <button class="btn btn-danger btn-sm" type="button" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}' , 'edit')"><i class="bi bi-key"></i></button>
        <button class="btn btn-primary btn-sm" type="button" title="Cetak" onclick="submitPrint('${item.NoBukti}')"><i class="bi bi-printer"></i></button>
      `
    } else {
      tombolAksi += `
        <button class="btn btn-success btn-sm" type="button" title="Koreksi" onclick="buttonKoreksi('${item.NoBukti}' , 'edit')"><i class="bi bi-pen"></i></button>
        <button class="btn btn-info btn-sm" type="button" title="Otorisasi" onclick="buttonDetail('${item.NoBukti}' , 'otorisasi')"><i class="bi bi-key"></i></button>
      `
    }

    rowTable += `<tr><td class="text-center"><div class="po-aksi-wrap">${tombolAksi}</div></td>`
    kolomRender.forEach((c) => {
      if (c.tipe === 1) {
        rowTable += `<td style="text-align: right;">${dppRenderNilai(c, item)}</td>`
      } else {
        rowTable += `<td>${dppRenderNilai(c, item)}</td>`
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

  $('#tabelDpp').DataTable({
    lengthChange: false,
    pageLength: dppPanjangHalaman,
    order: [],
    dom: "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    language: {
      emptyTable: 'Tidak ada data',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  });

  dppPindahBar()
  dppIkatSearch()
  dppIkatPanjangHalaman()
  dppIkatPeriode()
  let inputSearch = document.getElementById('dppSearch')
  if (inputSearch && inputSearch.value) {
    $('#tabelDpp').DataTable().search(inputSearch.value).draw()
  }
  dppAturTinggiTabel()
}

// Tinggi tabel mengikuti sisa ruang layar. Aman bila layout tidak punya #content -
// fungsinya berhenti diam-diam.
function dppAturTinggiTabel () {
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

function submitPrint (nobukti) {
    // for (var i = 0; i < 30; i++) {
    //   dataPrint.push(dataPrint[0])
    // }
    let _token = $('#_token').val()
    $.ajax({
      url: "{!! url('pengajuandppdetailCetak') !!}",
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
    for (let i = 0; i < dataPrint.length; i+=8) {
      let tempArray = dataPrint.slice(i,i+8)
      arrayDataPrint.push(tempArray)
    }

    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].Tanggal.split(' ')[0];

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
            <tr>
              <td colspan="8" style="text-align:center; font-weight:bold; font-size:16px; border:none;">
                PENGAJUAN DPP
              </td>
            </tr>
            <tr>
              <td colspan="8" style="text-align:center; font-weight:bold; font-size:16px; border:none;">
                Perihal Perincian Invoice
              </td>
            </tr>
            <tr>
              <td colspan="8" style="border:none; padding-top:10px;">
                  <div>No DPP: ${dataPrint[0].NoBukti ?? '-'}</div>
                  <div>Kepada Yth : ${dataPrint[0].NOMINTA ?? '-'}</div>
                  <div>${dataPrint[0].NAMACUSTSUPP ?? '-'}</div>
              </td>
          </tr>
                  <tr>
                    <td rowspan="2" class="text-center" style="width: 1%">No.</td>
                    <td rowspan="2" class="text-center" style="width: 10%">TANGGAL</td>
                    <td rowspan="2" class="text-center" style="width: 15%">NO INVOICE</td>
                    <td rowspan="2" class="text-center" style="width: 10%">JATUH TEMPO</td>
                    <td rowspan="2" class="text-center" style="width: 20%">NO PO</td>
                    <td rowspan="2" class="text-center" style="width: 20%">INVOICE + PPN</td>
                    <td rowspan="2" class="text-center" style="width: 15%">JUMLAH RETUR</td>
                    <td rowspan="2" class="text-center" style="width: 20%">JUMLAH DI BAYAR</td>
                  </tr>
                </thead> `;

    let z = 0
    let maxRow = 8;
    let tempPrintStr = ``
    // buat hitung grandtotal
    let grandTotalJumlah = 0;

    dataPrint.forEach(item => {

      if (item.Kredit) {
        grandTotalJumlah += Number(item.Kredit) || 0;
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
               style="width: 10%;  ">${itemSub.Tanggal ? itemSub.Tanggal.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 15%;  ">${itemSub.NoFaktur ?? ''}</td>
         <td class="text-align: left"
               style="width: 10%;  ">${itemSub.JatuhTempo ? itemSub.JatuhTempo.split(' ')[0] : ''}</td>
         <td class="text-align: left"
               style="width: 20%;">${itemSub.NOORDER ?? ''}</td>
         <td style="width: 20%; text-align: right;">
            ${itemSub.Kredit 
              ? Number(itemSub.Kredit).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
          <td class="text-align: left"
               style="width: 15%;">${itemSub.RETUR
                ? Number(itemSub.RETUR).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  })
                : ''}</td>
          <td style="width: 20%; text-align: right;">
            ${itemSub.Kredit 
              ? Number(itemSub.Kredit).toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }) 
              : ''}
          </td>
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
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
            <td style="border-top:none; border-bottom:none;"></td>
          </tr>`;
        }

        tempPrintStr += `
        <tr>
          <td colspan="6" style="border:1px solid; padding:5px; font-weight:bold;">
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


         <div style="display:flex; justify-content:space-between; width:100%; font-family:sans-serif; font-size:10px;">
	  <!-- KIRI -->
          <div style="width:50%; font-size:20px;">
            <p class="m-0"></p>
            <p class="m-0"></p>
            <p class="m-0"></p>
          </div>

          <!-- KANAN -->
          <div style="width:50%;">
          <table
             class="detail-spb-table mb-2"
             style="width: 100%; margin-top: 20px; font-family: sans-serif;
             font-size: 10px ">
             <tr>
               <td class="no-border text-center" style="width: 20%"></td>
               <td class="no-border text-center" style="width: 20%">Mengetahui</td>
             </tr>
             <tr style="height: 2.5rem">
               <td class="no-border">&nbsp;</td>
             </tr>

             <tr>
               <td class="no-border px-2">
               </td>
               <td class="no-border px-2">
               <p class="m-0" style="border-bottom: 1px solid">Nama</p>
               </td>
             </tr>
           </table>
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

function buttonBatalOtorisasi (nobukti) {

  console.log(nobukti)



  let akses = $("#akses_isbatal").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }





  alertify.confirm('Batal Otorisasi', 'Batal Otorisasi DPH ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('pengajuandppspbatalotorisasi') !!}",
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
  // console.log('formatAngka' , angkaString);
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
