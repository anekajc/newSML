@extends('newmasterTest')
@section('buttons')

@endsection

@section('page-title', 'Penawaran SO')
@section('title', 'SML - Penawaran SO')

{{-- Tampilan visual disamakan 1:1 dengan purchasing/purchaseOrder.blade.php: seluruh blok
     CSS di bawah disalin apa adanya dari sana (tabel bersih tanpa garis kolom, tombol aksi
     muncul saat baris di-hover, scrollbar auto-hide, modal pemilih gaya Kas, kotak centang
     diperbesar, tombol soft). Logika bisnis penawaran SO tidak diubah. --}}
{{-- tab bar baru--}}
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
    {{-- Header tabel interaktif (drag kolom + roda gigi + bar kolom tersembunyi).
       File khusus halaman ini: aturannya disalin dari public/css/report-table.css
       dengan scope diganti ke #tabel/#tabel2/#rtBar (satu bar dipakai bersama,
       dipindah lewat JS - lihat poPindahBar()), supaya reset ganas
       `.tb-report * { margin:0; padding:0 }` di file aslinya tidak ikut terbawa. --}}
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
{{-- js/canvas/functions.js (dimuat layout) mencari plugin di path relatif "js/" - dari URL
     /purchaseorder itu jadi 404 (plugins.easing.js). scwJsPath adalah variabel resmi template
     Canvas untuk menunjuk folder plugin-nya; didefinisikan di sini supaya layout tidak diubah. --}}
<script>var scwJsPath = "{!! URL::asset('js/canvas') !!}";</script>
  <style>
  .btn .bi-plus {
    font-size: 1.5rem;
    line-height: 0;
    vertical-align: middle;
  }
  
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

  /* Halaman ini dirancang mengisi tinggi layar (lihat poAturTinggiTabel()), jadi padding
     atas #content layout dikecilkan supaya tab tidak menggantung jauh dari header. Aman
     ditimpa dari sini karena blok <style> ini hanya ikut ter-render di halaman ini. */
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

  /* ---------- Kolom Aksi tabel (#tabel2/#tabel3) - tombol bulat kecil,
     warna pastel, sama seperti .btn-action-* di gudang/permintaanpemakaian ----------
     #tabel (Outstanding PR) SENGAJA tidak ikut lagi di grup ini: kolom Actions-nya
     sudah dimatikan (lihat PO_OUT[1].aksi), jadi sel pertamanya sekarang kolom data
     biasa - kalau masih dipaksa display:flex + justify-content:center, isinya jadi
     rata tengah dan perataan barisnya rusak.
     JANGAN pakai display:flex pada <td>: sel yang di-flex berhenti jadi sel tabel, tingginya
     cuma setinggi tombol dan tidak ikut vertical-align:middle baris - tombolnya jadi kelihatan
     naik dibanding teks kolom lain. Cukup text-align + vertical-align; font-size:0 membuang
     spasi antar tombol (tombolnya sendiri punya font-size 13px) supaya jaraknya pas 4px.
     :not(.dataTables_empty) - baris "No data available" jangan ikut font-size:0. */
  #tabel2 tbody td:first-child:not(.dataTables_empty),
  #tabel3 tbody td:first-child:not(.dataTables_empty) {
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    font-size: 0;
  }

  #tabel2 td:first-child .btn + .btn,
  #tabel3 td:first-child .btn + .btn {
    margin-left: 4px;
  }

  /* Di #tabel_add kolom Actions ada di paling kanan, bukan paling kiri. */
  #tabel td:first-child .btn,
  #tabel2 td:first-child .btn,
  #tabel3 td:first-child .btn,
  #tabel_add td:last-child .btn {
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    vertical-align: middle;
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
  #tabel3 td:first-child .btn:hover,
  #tabel_add td:last-child .btn:hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  #tabel td:first-child .btn-success,
  #tabel2 td:first-child .btn-success,
  #tabel3 td:first-child .btn-success,
  #tabel_add td:last-child .btn-success {
    color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
  }

  #tabel td:first-child .btn-warning,
  #tabel2 td:first-child .btn-warning,
  #tabel3 td:first-child .btn-warning,
  #tabel_add td:last-child .btn-warning {
    color: #b45309; border-color: #fbe3bd; background: #fef3e0;
  }

  #tabel td:first-child .btn-primary,
  #tabel2 td:first-child .btn-primary,
  #tabel3 td:first-child .btn-primary,
  #tabel_add td:last-child .btn-primary {
    color: #2563eb; border-color: #cfdcff; background: #e8edff;
  }

  #tabel td:first-child .btn-danger,
  #tabel2 td:first-child .btn-danger,
  #tabel3 td:first-child .btn-danger,
  #tabel_add td:last-child .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
  }

  #tabel td:first-child .btn-info,
  #tabel2 td:first-child .btn-info,
  #tabel3 td:first-child .btn-info,
  #tabel_add td:last-child .btn-info {
    color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
  }

  /* ---------- Header & baris tabel - bersih, uppercase abu-abu ---------- */
  #tabel thead th,
  #tabel2 thead th,
  #tabel3 thead th,
  #tabelso thead th,
  #tabel_refpr thead th,
  #tabel_data_header th {
    background: #f8f9fb !important;
    color: #6b7280 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #e7e9ee;
    border-top: none;
  }

  /* Tabel Harga Terakhir & Stock Proyeksi: struktur header sama persis dengan
     #tabel_data_header di atas (uppercase, 12px, garis bawah tipis), hanya warnanya
     yang mengikuti tombol pemicunya - hijau untuk Histori Harga, merah untuk Stock
     Proyeksi. Ditulis terpisah karena warnanya beda, bukan digabung ke selektor di atas. */
  #tabel_add_harga_terakhir thead th {
    background: #e7f7ed !important;
    color: #16a34a !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #cdebd7;
    border-top: none;
  }

  #tabel_add_stock_proyeksi thead th {
    background: #fdeaea !important;
    color: #dc2626 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 600;
    border-bottom: 1px solid #f7cfcf;
    border-top: none;
  }

  #tabel tbody tr:nth-of-type(odd),
  #tabel2 tbody tr:nth-of-type(odd),
  #tabel3 tbody tr:nth-of-type(odd),
  #tabelso tbody tr:nth-of-type(odd),
  #tabel_add tbody tr:nth-of-type(odd),
  #tabel_refpr tbody tr:nth-of-type(odd),
  #tabel_add_harga_terakhir tbody tr:nth-of-type(odd),
  #tabel_add_stock_proyeksi tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #tabel tbody tr:hover,
  #tabel2 tbody tr:hover,
  #tabel3 tbody tr:hover,
  #tabelso tbody tr:hover,
  #tabel_add tbody tr:hover,
  #tabel_refpr tbody tr:hover {
    background-color: #f5f3ff;
  }

  /* Tabel Referensi PR (form Tambah PR): isi satu baris, tidak dipatah - tabelnya digeser mendatar. */
  #tabel_refpr th,
  #tabel_refpr td {
    white-space: nowrap;
  }

  /* Hover baris disesuaikan warna tabelnya masing-masing, bukan ungu seperti tabel lain. */
  #tabel_add_harga_terakhir tbody tr:hover { background-color: #f2fbf5; }
  #tabel_add_stock_proyeksi tbody tr:hover { background-color: #fef6f6; }

  #tabel3.table-bordered th,
  #tabel3.table-bordered td {
    border-color: #e7e9ee !important;
  }

  /* ---------- Chip biru: Show/Hide Header (ikon truk), + Tambah Item, Simpan Data ----------
     Satu kelas dipakai bertiga supaya warnanya tidak lagi jalan sendiri-sendiri. Tint-nya
     sederet dengan chip hijau/merah/abu di bawah, memakai palet biru yang sudah ada di file
     ini (dipakai juga oleh tombol aksi di dalam tabel). */
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

  /* ---------- Tombol Histori Harga / Stock Proyeksi / Batal ----------
     Gaya chip: latar tint muda + teks berwarna, bukan latar solid menyala. Warna tint-nya
     sengaja sama persis dengan header tabel yang dibuka tombol tersebut
     (#tabel_add_harga_terakhir hijau, #tabel_add_stock_proyeksi merah), sehingga tombol dan
     tabelnya terbaca sebagai satu pasangan. Sederet dengan Batal yang juga chip abu-abu. */
  .btn-histori-harga {
    background-color: #e7f7ed;
    border-color: #cdebd7;
    color: #16a34a;
  }

  .btn-histori-harga:hover,
  .btn-histori-harga:focus {
    background-color: #d8f0e2;
    border-color: #b6e0c6;
    color: #15803d;
  }

  .btn-histori-harga:active {
    background-color: #c8e9d5 !important;
    border-color: #a5d8b8 !important;
    color: #15803d !important;
  }

  .btn-stock-proyeksi {
    background-color: #fdeaea;
    border-color: #f7cfcf;
    color: #dc2626;
  }

  .btn-stock-proyeksi:hover,
  .btn-stock-proyeksi:focus {
    background-color: #fbdcdc;
    border-color: #f2bcbc;
    color: #b91c1c;
  }

  .btn-stock-proyeksi:active {
    background-color: #f8cfcf !important;
    border-color: #eda9a9 !important;
    color: #b91c1c !important;
  }

  /* Batal = aksi sekunder, jadi abu-abu muda dengan teks gelap (bukan solid gelap). */
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

  /* Rule .card global di layout newmasterx sebenarnya untuk kartu menu dashboard
     (flex + align-items:center + efek melayang saat hover). Kalau dipakai untuk card berisi
     tabel, card-body tidak melar mengikuti halaman melainkan mengikuti lebar tabel,
     sehingga card ikut tertarik melebihi batas halaman. */
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

  /* Tombol di kolom Action baru muncul saat barisnya di-hover. Opt-in lewat kelas
     po-aksi-hover supaya tabel lain (mis. modalPOAdd) tidak ikut terpengaruh.
     visibility (bukan display) supaya lebar kolomnya tetap dipesan - tabel tidak
     melompat saat tombol muncul/hilang. Sengaja TIDAK memakai :focus-within: klik mouse membuat tombol tetap fokus sehingga tidak ikut hilang saat kursor sudah pindah. */
  table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transition: opacity .12s ease;
  }
  table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* Dropdown "Tampilkan" (jumlah baris per halaman) di toolbar tab Outstanding PR.
     Bentuknya sengaja meniru .po-filter-wrap / .po-filter-inp milik
     public/css/po-table-header.css supaya seragam dengan kotak periode di tab
     Purchase Order, tapi ditulis di sini - dengan begitu perubahan ini cukup
     mengunggah file blade-nya saja, file CSS bersama tidak perlu ikut diganti. */
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
    /* Panah bawaan <select> disembunyikan lalu digambar ulang lewat background-image,
       supaya warnanya bisa disamakan dengan kotak filter yang lain. padding-right
       menyediakan ruang untuk panah itu. */
    padding: 2px 20px 2px 0;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231D2130' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
  }

  /* ---- Lapisan "sedang memuat" tabel Outstanding PR & Outstanding SO ----
     Elemennya adalah .dataTables_processing bawaan DataTables (dihidupkan lewat opsi
     processing + token "r" pada dom - lihat initTabelOutstanding()), tapi seluruh
     tampilannya ditulis ulang di sini: dari teks polos menjadi lapisan yang menutup
     #tabel_wrapper dengan chip + spinner di tengahnya.

     Aturan bawaan `.dataTables_wrapper .dataTables_processing` dari jquery.dataTables.css
     kalah dua kali: selector di bawah memakai id (specificity lebih tinggi) dan blok ini
     dimuat belakangan - di newmasterx, tempat section css disisipkan ada di bawah <link>
     DataTables. (Nama directive-nya sengaja tidak ditulis lengkap di sini: Blade tetap
     mengompilasi directive walau berada di dalam komentar CSS.) */
  #tabel_wrapper,
  #tabelso_wrapper {
    /* Acuan position:absolute lapisan di bawah. DataTables sendiri sudah menetapkannya,
       ditulis ulang di sini supaya tidak bergantung pada file CSS-nya ikut termuat. */
    position: relative;
  }

  #tabel_wrapper > .dataTables_processing,
  #tabelso_wrapper > .dataTables_processing {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    /* Bawaannya width:200px + margin negatif supaya memusatkan diri di top/left 50%.
       Semuanya harus dinolkan, kalau tidak lapisan ini tidak menutup penuh. */
    width: auto;
    margin: 0;
    padding: 0;
    border: 0;
    background: rgba(255, 255, 255, .62);
    z-index: 40;
    /* 45% pertama masih transparan, jadi respons yang cepat (mis. pindah halaman saat
       datanya sedikit) selesai sebelum lapisan ini sempat terlihat - tidak berkedip. */
    animation: poMunculLoading .34s ease-out both;
  }

  @keyframes poMunculLoading {
    0%, 45% { opacity: 0; }
    100% { opacity: 1; }
  }

  /* Chip dipusatkan memakai posisi absolut, BUKAN flexbox pada lapisan di atas.
     Alasannya: DataTables menampilkan/menyembunyikan lapisan itu dengan menulis
     `display:block` / `display:none` sebagai INLINE style. Jadi `display:flex` di
     stylesheet akan selalu kalah, dan kalau dipaksa dengan !important justru perintah
     `display:none` untuk MENYEMBUNYIKANNYA ikut terblokir - lapisannya tidak pernah
     mau hilang. Menghindari properti display sama sekali membuat keduanya tidak
     pernah berebut. */
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
    animation: poPutarLoading .6s linear infinite;
  }

  @keyframes poPutarLoading {
    to { transform: rotate(360deg); }
  }

  #tabel td.dataTables_empty,
  #tabel2 td.dataTables_empty,
  #tabelso td.dataTables_empty { text-align: center !important; }

  .po-empty-msg {
    text-align: center;
    padding: 26px 12px;
    color: #6b7280;
    font-size: 13px;
  }

  .po-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 16px;
  }
  @media (max-width: 900px) {
    .po-kpi-strip { grid-template-columns: 1fr; }
  }
  .po-kpi-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    display: flex;
    align-items: flex-start;
    gap: 14px;
  }
  .po-kpi-ic {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }
  .po-kpi-label { font-size: 13px; color: #64748b; margin-bottom: 4px; }
  .po-kpi-val { font-size: 22px; font-weight: 700; color: #1e293b; }
  
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
{{-- tab bar baru--}}

{{-- tampilan search bar 1 --}}

  <style>
  .rodokNdukurTitik{
    margin-top:-12px;
  }
  </style>

  <style>
  .dataTables_scrollBody {
      border: none !important;
  }

  /* Remove conflicting borders */
  .table-responsive {
      border: none !important;
  }

  /* Dinonaktifkan: garis vertikal/atas per sel di tab Outstanding PR (#tabel) & Outstanding SO
     (#tabelso). Tabelnya kini bersih tanpa garis kolom, sama seperti tab Purchase Order (#tabel2)
     yang hanya memakai garis bawah tipis per baris dari .data-table.
  #tabel td, #tabel th,
  #tabelso td, #tabelso th {
      border-left: 1px solid #dee2e6;
      border-top: 1px solid #dee2e6;
  }

  #tabel td:first-child, #tabel th:first-child,
  #tabelso td:first-child, #tabelso th:first-child {
      border-left: none;
  }

  #tabel thead tr:first-child th,
  #tabelso thead tr:first-child th {
      border-top: none;
  }
  */

  /* DataTables (autoWidth bawaan = true) selalu menulis hasil pengukurannya sebagai inline
     style pada <table>, mis. style="width: 1640px" - lihat `b.style.width = v(e)` di
     datatables.min.js. Inline style itu mengalahkan `.data-table { width: 100% }`, dan kalau
     hasil ukurnya lebih kecil dari kotaknya, `table.dataTable { margin: 0 auto }` milik
     jquery.dataTables.css memusatkan tabel sehingga muncul ruang kosong di kiri dan kanan.
     Terjadi saat kolomnya sedikit - entah karena kolom Actions dimatikan, atau karena user
     menyembunyikan kolom lewat roda gigi.

     Dipakai min-width, BUKAN width, supaya inline style DataTables tidak perlu ditimpa:
     tabel yang lebih sempit dari kotak ikut melar penuh, sedangkan tabel yang memang lebih
     lebar tetap memakai lebar hasil ukurannya dan tetap bisa digeser mendatar.

     Sengaja di-scope lewat ID, bukan class. Saat mengukur kolom, DataTables meng-clone
     tabelnya dengan `.clone().css("visibility","hidden").removeAttr("id")` - class ikut
     terbawa tapi id dibuang. Aturan ber-ID otomatis tidak menyentuh klon pengukuran itu,
     sedangkan aturan ber-class (.data-table / table.dataTable) akan ikut terpasang di klon
     dan bisa merusak hasil ukur lebar kolomnya. */
  #tabel, #tabel2, #tabelso {
    min-width: 100%;
  }
  </style>

{{-- end tampilan search bar 1 --}}

{{-- end tampilan search bar 2 --}}

{{-- tampilan search bar 3 --}}
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

    #tabel_add_list_noSo_filter {
        display: flex;
        align-items: flex-end;
        margin-top: 8px;
        margin-right: 10px;
        margin-bottom: -10px;
      }

    #tabel_add_list_noSo_filter label input {
        width: 150px;
        padding: 5px 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
        box-shadow: none;
        font-size: 0.65rem;
      }

    #tabel_add_list_noSo_filter label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
      }

    #tabel_add_list_noSo_filter input:focus {
        border-color: #007bff;
        outline: none;
      }
  </style>
{{-- end tampilan search bar 3 --}}

{{-- tampilan search bar modal add pelanggan --}}
  <style>
    #tabel_add_list_pelanggan_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;

    }
    #tabel_add_list_pelanggan_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search bar modal add pelanggan --}}

{{-- tampilan search sales --}}
  <style>
    #tabel_add_list_sales_filter{
      display: flex;
      align-items: flex-end;
      margin-bottom: -10px;
    }
    #tabel_add_list_sales_filter label input {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
    }
  </style>
{{-- end tampilan search sales --}}

{{-- tampilan search modal barang all --}}
  <style>

  .card-header {
    text-align: left !important;
  }
    #input_search_barang_all {
      width: 150px;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-shadow: none;
      font-size: 0.65rem;
      display: flex;
      align-items: flex-end;
      margin-left: 95px;
    }

    .search-label {
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: 155px;
    margin-top : -45px;
    display: inline-block;
    vertical-align: middle;
    }

    /* .data-table thead th default-nya text-align:left dengan spesifisitas lebih
       tinggi dari .text-right Bootstrap - kolom angka di modal Otorisasi perlu
       aturan lokal supaya rata kanan. */
    #modalOtorisasi .data-table thead th.num { text-align: right; }
    #modalOtorisasi .data-table tbody tr.total-row td {
      font-weight: 600;
      border-top: 1px solid var(--border);
    }

    /* Tombol Otorisasi di footer modal: warna soft (latar biru muda, teks biru),
       mengikuti pola .modal-footer .btn-primary di bonsementara.blade.php. */
    #modalOtorisasi .modal-footer .btn-primary {
      background-color: #e8edff;
      border-color: #cfdcff;
      color: #2563eb;
      border-radius: 8px !important;
      text-transform: none !important;
      box-shadow: none;
    }
    #modalOtorisasi .modal-footer .btn-primary:hover {
      background-color: #dce6ff;
      border-color: #b9c9ff;
      color: #1d4ed8;
    }
  </style>
{{-- end tampilan search modal barang all --}}
@endsection
@section('content')

<div id="imagecontainer" class="d-none" style="">
  <img src="img/sml1.png" style="height: 50px; width: 280px" alt="">
</div>

<div id="imagecontainerTtd" class="d-none">
  <img src="" style="height: 95px; width: 200px" alt="">
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
    <input type="hidden" id="level" value="{!! $level !!}" />

    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true">Penawaran SO</a>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent">

          <!-- <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12" style='overflow:auto;'>
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <table id="tabel" class="table table-bordered table-hover table-striped">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Actions</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">No. Bukti</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Kode Barang</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Nama Barang</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Sat</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Qty</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Qty PO</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Sisa PR</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Keterangan</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Out. SO</th>
                        <th style="padding: 4px 12px; white-space:nowrap;" scope="col">Qty Stock</th>

                      </tr>
                    </thead>
                    <tbody id="tabel_data" class="text-left">
                      {{-- @foreach ($tempOutstanding1 as $OutPR)
                      <tr>

                         <td class="text-center">
                            <button class="btn btn-primary btn-sm" type="button" title="Details" onclick="buttonAdd('{{ $OutPR->Nobukti }}')">
                              <i class="bi bi-plus"></i>
                            </button>
                        </td>
                        <td style='white-space:nowrap;'>{{ $OutPR->Nobukti }}</td>
                        <td style='white-space:nowrap;'>{!! date("Y/m/d", strtotime($OutPR->Tanggal)) !!}</td>
                        <td style='white-space:nowrap;'>{{ $OutPR->kodebrg }}</td>
                        <td style='white-space:nowrap;'>{{ $OutPR->NamaBrg }}</td>
                        <td style='white-space:nowrap;' class='text-center'>{{ $OutPR->sat }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->Qnt, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->QNTPO, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->SisaPPL, 2) }}</td>
                        <td style='white-space:nowrap;'>{{ $OutPR->Keterangan }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->QntoutSO, 2) }}</td>
                        <td style='white-space:nowrap;' class='text-right'>{{ number_format($OutPR->QntStock, 2) }}</td>
                      </tr>
                      @endforeach --}}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> -->

          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" onchange="onChangePeriodePSO1()" class="po-filter-inp" id="input_tanggalawal_pso1" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d') !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" onchange="onChangePeriodePSO1()" class="po-filter-inp" id="input_tanggalakhir_pso1" value="{!! \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d') !!}">
                    </div>
                    <input type="search" id="psoSearch1" class="po-search-inp" placeholder="Cari data">
                    <div class="po-len-wrap"><label for="psoLen1">Tampilkan</label>
                      <select id="psoLen1" class="po-len-inp"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="-1">Semua</option></select>
                    </div>
                    <button class="po-btn-filter" type="button" onclick="$('#modalFilterPSO').modal('show')">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="po-toolbar-act">
                      <button id='AddVisibility' class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
                    </div>
                  </div>

                  {{-- Filter modal: so.blade.php's exact modalFilter markup/classes (rt-filter,
                       rt-section, rt-active-badge, rt-footer-buttons, rt-btn), just one Status
                       Otorisasi field instead of SO's multi-field filter. Tab Otorisasi lama sudah
                       digabung ke tab ini; data sudah/belum otorisasi dipilih lewat filter ini. --}}
                  <div class="modal fade rt-filter" id="modalFilterPSO">
                    <div class="modal-dialog modal-md">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">
                            <i class="bi bi-funnel"></i>
                            Filter Penawaran SO
                            <span class="rt-active-badge" id="psoFilterBadge">0 aktif</span>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPSO').modal('hide')">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>

                        <div class="modal-body">
                          <div class="rt-section">
                            <div class="rt-group-label">Penyaringan Data</div>
                            <div class="rt-grid-2">
                              <div>
                                <label class="rt-field-label" for="psoStatusOtorisasi">Otorisasi</label>
                                <select class="rt-native" id="psoStatusOtorisasi">
                                  <option value="0" selected>Semua</option>
                                  <option value="2">Sudah</option>
                                  <option value="1">Belum</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="rt-reset-link" onclick="psoResetFilterFieldsPSO()">Reset semua</button>
                          <div class="rt-footer-buttons">
                            <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
                              onclick="$('#modalFilterPSO').modal('hide')">Batal</button>
                            <button type="button" class="rt-btn rt-btn-primary" onclick="buttonFilterPSO(); $('#modalFilterPSO').modal('hide');">Terapkan</button>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>

                  <div id="rtBarTabel2"></div>
                  <table id="tabel2" class="data-table po-aksi-hover">
                    <thead class="text-center " style="white-space:nowrap;"></thead>
                    <tbody id="tabel2_data" class="text-left"></tbody>
                  </table>
                  <div class="po-rt-hint"><i class="bi bi-info-circle"></i> Seret judul kolom untuk mengubah urutannya. Klik <i class="bi bi-gear"></i> pada judul kolom untuk menyembunyikan kolom atau mengatur jumlah desimal.</div>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid">
                      <table id="tabelRetur" class="table table-bordered table-striped"  >
                        <thead class="text-center">
                          <tr>
                            <th scope="col">Profile 2</th>
                            <th scope="col">No. SSP</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">No. Out</th>
                            <th scope="col">Gudang</th>
                          </tr>
                        </thead>

                        <tbody id="tabelRetur_data" class="text-left" >

                        </tbody>
                      </table>
                </div>
              </div>
            </div>
          </div>

          <!-- <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid">

                      <table id="tabelRetur" class="table table-bordered table-striped"  >
                        <thead class="text-center">
                          <tr>
                            <th scope="col">Profile 3</th>
                            <th scope="col">No. SSP</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">No. Out</th>
                            <th scope="col">Gudang</th>
                          </tr>
                        </thead>

                        <tbody id="tabelRetur_data" class="text-left" >

                        </tbody>
                      </table>
                </div>
              </div>
            </div>
          </div> -->

        </div>
      </div>

    </div>
  </div>

</div>

<div id="page2" class="container-fluid" style="display: none" >
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left">
      {{-- <h2>Add</h2> --}}
    </div>
    <div class="col-6 text-right">
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

  <div id="modalBodyAddMain" class="">
    <div class="modal-body" style="">
      <div class="row">
        <input type="hidden" class="form-control" id="input_add_nourut">
        <div class="col-md-3">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>Customer</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" placeholder="Kode Customer" id="input_add_kodesupplier" onkeyup="checkKodeSupplier()">
                <button class="btn btn-chip-biru btn-sm" id="buttonAddListPelanggan" onclick="performSearchSupplier()">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>No Bukti</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-12px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_add_nobukti" placeholder="" disabled>
              </div>
            </div>

            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>

            <div class="col-md-8" style="margin-top:-10px;">
              <div class="form-group">
                <input type="date" class="form-control text-left" id="input_add_tanggal" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>



          </div>
        </div>

        <div class="col-md-3">
          <div class="row">

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control text-left" placeholder="Nama Customer" id="input_add_namasupplier" disabled> 
              </div>
            </div>


            <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>perihal</label>
              </div>
            </div>

            
            <div class="col-md-12" style="margin-top:-10px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=2 placeholder="perihal" class="form-control text-left align-items-left" onblur="onChangePerihal()" id="input_add_alamatsupplier"  ></textarea>
              </div>
            </div>


          </div>
        </div>


        <div class="col-md-3">
          <div class="row">

            <div class="col-md-6">
              <div class="row">
                <div class="col-9">
                  <div class="form-group">
                    <label>Valas</label>
                  </div>
                  </div>
                  <div class="col-3 text-right">
                    <div class="form-group">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <div class="input-group form-group">
                    <select class="form-control" id="input_add_valas" onchange="onChangeValasDropdown()"></select>
                    {{-- Tombol browse lama disembunyikan; status disabled/hidden-nya tetap dipakai kode lama untuk mengunci dropdown valas (lihat mktSinkronKunciValas). --}}
                    <button onclick="buttonAddListValas()" id="buttonAddListValas" class="btn btn-chip-biru btn-sm d-none" type="button"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12" style="margin-top:-12px;">
              <div class="row">

                <div class="col-6">
                  <div class="form-group">
                    <label>Kurs</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control text-right" id="input_add_kurs"  disabled>
                  </div>
                </div>




                <div class="col-6" style="margin-top:-10px;">
              <div class="form-group">
                <label>freight</label>
              </div>
            </div>


            

                <div class="col-md-6" style="margin-top:-10px;">
              <div class="form-group">
                <input type="text" inputmode="decimal" class="form-control text-right pso-angka" onchange="onChangeFreight()" id="input_add_freight" placeholder="0.00">
              </div>
            </div>

            
       



              </div>
            </div>

          </div>
        </div>

      <div class="col-md-3">
        <div class="row">

          <div class="col-md-12">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>PPN</label>
                </div>
              </div>
              <div class="col-md-6">
                <select onchange="onChangeTipePPN()" id="input_add_tipeppn" class="form-control text-left form-select-lg mb-3" aria-label=".form-select-lg example">
                  <option value=0 selected>None</option>
                  <option value=1 >Exclude</option>
                  <option value=2 >Include</option>
                </select>
              </div>
            </div>
          </div>

          <div class="col-md-12 rodokNdukurTitik">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Pembayaran</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <select id="input_add_pembayaran" onchange="onChangeInputAddPembayaran()" class="form-control form-select-lg mb-3 text-left" aria-label=".form-select-lg example">

                    <!-- <option value=0 selected >Tunai</option>
                    <option value=1 >Kredit</option> -->
                    <option value=1 selected>30 Hari Setelah Barang Dikirim</option>
                    <option value=2 >60 Hari Setelah Barang Dikirim</option>
                    <option value=3 >45 Hari Setelah Barang Dikirim</option>
                    <option value=4 >Cash Before Delivery</option>
                    <option value=5 >30 Hari Setelah Pekerjaan Selesai</option>
                    <option value=6 >BG 30 hari setelah barang diterima</option>
    `



                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- budi sementara --}}
          <div class="col-md-12" style="margin-top:-12px;">
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label>Hari</label>
                </div>
              </div>

              <div class="col-md-6">
                <input type="number" class="form-control text-right" id="input_add_hari" onblur="onChangeHari()" value=0 min=0 >
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

          <hr/>
          <div class="row" style='margin-top:5px'>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-lg btn-show-hide-header btn-chip-biru" style="
                height: 38px;
                width: 38px;
                margin-top: -35px;
                padding: 0;
                border-radius: 8px;
                font-size: 1.15rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s, box-shadow 0.3s;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                onclick="buttonShowHideHeader()" title="Show/Hide Header"><i class="bi bi-truck"></i></button>
            </div>
          </div>
            <div class="showhidemodalbodyaddmain mt-4" id="modalBodyAddMainHeader" style="display: none;">
              <div class="row" style='margin-top:-30px'>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Franco</label>
                      </div>
                    </div>

                    <!-- <div class="form-group row">
                      <input class="form-control col-8" id="input_add_kodealamatkirim" readonly value='GMPL' >
                      <button onclick="buttonAddListGudang()" id="buttonAddListGudang"  style="height:32px;" class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
                    </div> -->

                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_alamatkirim"  onblur="onChangeAlamatkirim()"></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Delivery</label>
                      </div>
                    </div>

                    


                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_ekspedisi"  onblur="onChangeDelivery()" ></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Validitas</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_keterangan" onblur="onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-3">
                  <div class="row">

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>TTD</label>
                          </div>
                        </div>
                        <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">
                            <input type="text" class="form-control" id="input_add_noso" value='-' readonly>
                            <button onclick="buttonAddListNoSO()" id="buttonAddListNoSo" class="btn btn-chip-biru btn-sm">
                              <i class="bi bi-search"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Nama Ttd</label>
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-group">
                            <input type="text" class="form-control" id="input_add_nopocust" value ='-' readonly>
                          </div>
                        </div>
                      </div>
                    </div>

                    


                  </div>
                </div>



               <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Up</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_up"  onblur="onChangeUp()"></textarea>
                      </div>
                    </div>
                  </div>
                </div>



              <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Up2</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_up2"  onblur="onChangeUp2()"></textarea>
                      </div>
                    </div>
                  </div>
              </div>  


              <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>note</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_note" onblur="onChangeNote()"></textarea>
                      </div>
                    </div>
                  </div>
              </div>


              <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Ket Revisi</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=2  class="form-control" id="input_add_ketrevisi" onblur="onChangeKetrevisi()"></textarea>
                      </div>
                    </div>
                  </div>
              </div>





              
              <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Tgl revisi</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="input-group form-group">
                         <input type="date" class="form-control text-left" id="input_add_tglrevisi" value="{!! date('Y-m-d') !!}" onblur="onChangeTglrevisi()">
                      </div>
                    </div>
                  </div>
                </div>



            <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Tgl pr. cust</label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                         <input type="date" class="form-control text-left" id="input_add_tglprcust" value="{!! date('Y-m-d') !!}" onblur="onChangeTglprcust()">
                      </div>
                    </div>
                  </div>
              </div>  



            <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Lokasi Penerima</label>
                      </div>
                    </div>

                    <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">

                            <input type="text" class="form-control" id="input_add_lokasipenerima" value='-' readonly>
                             <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
                 

                          </div>
                        </div>
                  </div>
              </div>  


             <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>nama lokasi</label>
                      </div>
                    </div>

                    <div class="col-8" style="margin-top:-5px">
                          <div class="input-group form-group">

                            <input type="text" class="form-control"  onblur="onChangeLokasipenerima()" id="input_add_namalokasipenerima" value='-' readonly>
                            


                          </div>
                        </div>
                  </div>
              </div>   








              
              








            <div class="col-md-3" hidden>
              <div class="row">

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Back Office</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-12">
                          <div class="input-group form-group">
                            <input type="hidden" class="form-control" id="input_add_kodebackoffice" >
                            <input type="text" class="form-control" id="input_add_namabackoffice"  disabled>
                            <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {{-- budi sementara 2 --}}
            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>PIC</label>
                      </div>
                    </div>
                    <div class="col-3 text-right">
                      <div class="form-group">
                    <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input type="hidden" class="form-control" id="input_add_kodepic"  >
                        <input type="text" class="form-control" id="input_add_namapic"  disabled>
                        <button onclick="buttonAddListPIC()" id="buttonAddListPIC" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-9">
                      <div class="form-group">
                        <label>Sales</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group form-group">
                    <input type="hidden" class="form-control" id="input_add_kodesales" >
                    <input type="text" class="form-control" id="input_add_namasales"  disabled>
                    <button onclick="buttonAddListSales()" id="buttonAddListSales" class="btn btn-chip-biru btn-sm"><i class="bi bi-search"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3" hidden>
              <div class="row">
                <div class="col-6" style="margin-top:-40px">
                  <div class="form-group">
                    <label>Draft PO</label>
                  </div>
                </div>

                <div class="col-md-6" style="margin-top:-40px">
                  <select onchange="onChangeDraftPO()" id="input_add_draftpo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value=0 selected>Tidak</option>
                    <option value=1 >Ya</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

        </div>
            <hr/>
      </div>

    </div>

      <div class="showhidemodalbodyaddmain container-fluid" id="modalBodyAddMainItems">
        <div class="container-fluid" style="overflow:auto; margin-top:-35px;">
          <!-- <input type="hidden" name="noUrut" id="input_add_noUrut" value="" /> -->
          <div class="row">
            <table id="tabel_add" class="data-table">
              <thead id='tabel_data_header' class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="tabel_data_add" class="text-left" >
                <tr>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td>1</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="">
                        <i class="bi bi-info"></i>
                      </button>
                      <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="">
                        <i class="bi bi-key-fill"></i>
                      </button>
                      <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id='buttonTambahItem' class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              transition: background-color 0.3s, box-shadow 0.3s;"
              onclick="buttonAddAddItem()">Tambah Item</button>
          </div>
        </div>

        <!-- ADD add -->
        <div id="addAddItem" class="container-fluid showhide">
          <div>
          <hr/>

            <div class="row">
              <div class="col-4">
                <h4 id="h4AddAddItem" style="margin-left:-35px;">Add Item</h4>
                <h4 id="h4AddEditItem" style="margin-left:-35px;">Edit Item</h4>
              </div>
            </div>

          <div class="row" style='margin-top:-30px'>
            <div class="col-md-12">
                <div class="row">
              <!-- No Penyerahan -->

              <div class="col-md-3">
                <div class="row">
                  <!-- No Penyerahan -->
                  <div class="col-md-12">
                    <div class="row" hidden>
                      <div class="col-6">
                        <div class="form-group">
                          <label>Jasa</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group form-group">
                          <select id="input_add_add_jasa" class="form-control form-select-lg mb-3" disabled>
                            <option value=0 selected>Tidak</option>
                            <option value=1>Iya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="row">
                  <!-- No Penyerahan -->
                  <div class="col-md-12">
                    <div class="row" hidden>
                      <div class="col-4">
                        <div class="form-group">
                          <label>FOC</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group form-group"> {{-- nama lama : nopenyerahan --}}
                          <select id="input_add_add_foc" onChange="LockFreeOfCharge()" class="form-control form-select-lg mb-3">
                            <option value=0>Tidak</option>
                            <option value=1>Iya</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3" style="margin-left:-50px;">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>QTY</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" inputmode="decimal" class="form-control text-right pso-angka" id="input_add_add_qty" onblur="cekQntStock()" value="0.00" >
                    </div>
                  </div>
                </div>
              </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Satuan</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <select id="input_add_add_nosat" class="form-control form-select-lg mb-3">
                        <option value=0 selected>Tidak</option>
                      </select>

                        <input type="text" class="form-control" value="-" id="input_add_add_satminus" >

                    </div>
                  </div>
                </div>

              </div>

                <div class="row">
                  <!-- Barang dan Nama Produk -->
                  <div class="col-md-6">
                    <!-- <div class="row">
                      
                      <div class="col-3" style="margin-top:-25px;">
                        <div class="form-group" hidden>
                          <label>No. PNW PO</label>
                        </div>
                      </div>

                      <div class="col-md-8" style="margin-top:-25px;">
                        <div class="input-group form-group" hidden>
                          <input type="hidden" class="form-control" value="-" id="input_add_add_nopnwpo" readonly>
                          <button onclick="buttonAddAddListPWO()" id="buttonAddAddListBarang" class="btn btn-primary btn-sm text-right" tabindex="1">
                            <i class="bi bi-plus"></i>
                          </button>
                        </div>
                      </div>

                    </div> -->

                    <!-- Kode Barang and Nama Barang in same row, halved -->
                    <div class="row">
                      <!-- Kode Barang - Left Half -->
                      <div class="col-md-6" style="margin-top:-12px;">
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Kode Barang</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="input-group form-group">
                              <input type="text" class="form-control" id="input_add_add_kodebarang">
                              <button onclick="performSearch()" id="buttonAddAddListBarang" class="btn btn-chip-biru btn-sm">
                                  <i class="bi bi-search"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Nama Barang - Right Half -->
                      <div class="col-md-5" style="margin-top:-12px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_namabarangasli" readonly>
                        </div>
                      </div>

                    </div>

                    <!-- Second Nama Barang (kept separate as requested) -->
                    <div class="row">
                      <div class="col-3" style="margin-top:-12px;">
                        <div class="form-group">
                          <label>Nama Barang</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-12px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_namabarang">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-3" style="margin-top:-12px;">
                        <div class="form-group">
                          <label>Note</label>
                        </div>
                      </div>
                      <div class="col-md-8" style="margin-top:-12px;">
                        <div class="form-group">
                          <input type="text" class="form-control" id="input_add_add_keteranganbarang">
                        </div>
                      </div>
                    </div>
                  </div>



                  

                <div class="col-md-6" style="margin-left:-50px;">
                  <div class="row">
                    <!-- Harga - Left Half -->
                    <div class="col-md-6" style="margin-top:-12px;">
                      <div class="row">

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Harga</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" inputmode="decimal" class="form-control text-right pso-angka" oninput="onChangeInputAddAddHarga()" onchange="onChangeInputAddAddHarga()" id="input_add_add_harga" value="0.00" >
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Harga - Left Half -->
                    <div class="col-md-6" style="margin-top:-12px;">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label   id="label_add_add_isi" >isi</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="text" inputmode="decimal" class="form-control text-right pso-angka" id="input_add_add_isi" value="0.00" >
                           <!-- <input type="number" class="form-control text-right" id="input_add_add_discrp" onchange="reverseCalculateDiscPercent()" tabindex="7"> -->
                          </div>
                        </div>
                      </div>

                    </div>
                    <!-- Disc RP - Right Half -->





                  </div>






                  <div class="row">
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <!-- <label>Merk SO</label> -->
                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <!-- <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value=0 onChange='calculateDiscRp()' tabindex="8"> -->
                        
                      </div>
                    </div>

                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <input type="hidden" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen2" value=0 onChange='calculateDiscRp()' >
                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                        <input type="hidden" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen3" value=0 onChange='calculateDiscRp()'>
                      </div>
                    </div>

                  </div>


                  <div class="row">
                    <div class="col-md-3" style="margin-top:-17px;">
                      <div class="form-group">
                         <label>Disc(%)</label> 

                      </div>
                    </div>


                    <div class="col-md-3" style="margin-top:-17px;">
                      <div class="form-group">
                       <input type="text" inputmode="decimal" class="form-control text-right pso-angka" id="input_add_add_discpersen1" value="0.00" oninput="calculateDiscRp()" onchange="calculateDiscRp()" >

                      </div>
                    </div>


                     <div class="col-md-6" style="margin-top:-12px;">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Disc Rp</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <input type="hidden" class="form-control text-right" id="input_add_add_hargaAwal" value="0.00">
                           <input type="text" inputmode="decimal" class="form-control text-right pso-angka" id="input_add_add_discrp" oninput="reverseCalculateDiscPercent()" onchange="reverseCalculateDiscPercent()" value="0.00" >
                          </div>
                        </div>
                      </div>

                    </div>



                  </div>


                  <div class="row">
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                         <label>tipe so</label> 

                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                       <input type="text" class="form-control" id="input_add_add_tipeso">

                      </div>
                    </div>


                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                         <label>merk so</label> 

                      </div>
                    </div>
                    <div class="col-md-3" style="margin-top:-12px;">
                      <div class="form-group">
                       <input type="text" class="form-control" id="input_add_add_merkso">

                      </div>
                    </div>





                  </div>


                  <!-- <div class="row">
                    
                  </div> -->

                  






                </div>

                <div class="col-md-1" style="margin-top:-10px;" hidden>
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_noPPL" > {{-- nama lama : satuanproduk --}}
                  </div>
                </div>
                <div class="col-md-1" style="margin-top:-10px;" hidden>
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_urutPPL" > {{-- nama lama : satuanproduk --}}
                  </div>
                </div>

              </div>
            </div>

            <div class="col-md-12">
              <div id="divhargaterakhir">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Beli Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_harga_terakhir" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Supplier</th>
                            <th style="padding: 4px 12px;" scope="col">Tgl LPB</th>
                            <th style="padding: 4px 12px;" scope="col">No LPB</th>
                            <th style="padding: 4px 12px;" scope="col">Harga LPB</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_harga_terakhir" class="text-left" >
                          <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div id="divStockProyeksi">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Jual Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_stock_proyeksi" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Tgl INVC</th>
                            <th style="padding: 4px 12px;" scope="col">No INVC</th>
                            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Harga INVC</th>
                            <th style="padding: 4px 12px;" scope="col">Customer</th>
                            <th style="padding: 4px 12px;" scope="col">Kode Kebun</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Kebun</th>
                          </tr>
                        </thead>
                        <tbody id="tabel_data_add_stock_proyeksi" class="text-left" >
                          <tr>
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
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>

          <div class="row mt-2">
            <div class="col-md-12 text-right">

              <button type="button" class="btn btn-lg btn-histori-harga" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableHargaTerakhir()">Harga Beli Terakhir</button>

              <button type="button" class="btn btn-lg btn-stock-proyeksi" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableStockProyeksi()">Harga Jual Terakhir</button>

              <button type="button" class="btn btn-lg btn-batal-add" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="closeShowHideAdd()">Batal</button>

              <button type="button" id="submitAddAdd" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd('I')">Simpan</button>

              <button type="button" id="submitAddEdit" class="btn btn-lg btn-chip-biru" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddAdd('U')">Simpan</button>
            </div>

          </div>

        </div>

        <!-- END ADD ADD -->

        <!-- ADD EDIT -->

        <div  id="addEditItem" class="container-fluid showhide">
            <div class="row">
              <div class="col-4">
                <h4>Edit Item</h4>
              </div>
            </div>
            <div class="row">

              <div class="col-md-12">

              <div class="row">

            <div class="col-md-4">

            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Ref PR</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="" class="btn btn-chip-biru btn-sm" disabled><i class="bi bi-search"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_refpr" value=""  disabled>
              </div>
            </div>

            </div>

            </div>


            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>No Penyerahan</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="" class="btn btn-chip-biru btn-sm" disabled><i class="bi bi-search"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_nopenyerahan"  disabled>
              </div>
            </div>

            </div>

            </div>
            </div>
            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-9">
                <div class="form-group">
                  <label>Barang</label>
                </div>
              </div>
              <div class="col-3 text-right">
                <div class="form-group">
              <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang" class="btn btn-chip-biru btn-sm" disabled><i class="bi bi-search"></i></button>
              </div>

            </div>

            <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" class="form-control" id="input_add_edit_kodebarang" >
                <input type="text" class="form-control" id="input_add_edit_namabarang"  disabled>
              </div>
            </div>

            </div>

            </div>

            <div class="col-md-4">


            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label>Nama Produk</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control" id="input_add_edit_namaproduk" >
              </div>
            </div>

            </div>

            </div>

<div class="col-md-12">
  <div class="row">

    <div class="col-12">
      <div class="form-group">
        <label>Harga Terakhir</label>
      </div>
    </div>

    <div class="col-md-12 mb-4">
      <div class="form-group">
        <table id="tabel_edit_harga_terakhir" class="table table-bordered table-striped"  >
          <thead class="text-center">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Qty</th>
              <th scope="col">Satuan</th>
              <th scope="col">Valas</th>
              <th scope="col">Kurs</th>
              <th scope="col">Harga</th>
              <th scope="col">Disc Rp</th>
              <th scope="col">Total Diskon</th>
            </tr>
          </thead>
          <tbody id="tabel_data_edit_harga_terakhir" class="text-left" >
            <tr>
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
      </div>
    </div>

  </div>
</div>


            <div class="col-md-12">
              <div class="row">


              <div class="col-md-2">
                <div class="row">

              <div class="col-md-12">
                <div class="form-group">
                  <label>Qty</label>
                </div>
              </div>


            <div class="col-md-12">
              <div class="form-group">
                <input type="number" class="form-control text-right" id="input_add_edit_qty" value ="0.00" >
              </div>
            </div>

            </div>
          </div>

            <div class="col-md-2">
              <div class="row">


            <div class="col-12">
              <div class="form-group">
                <label>Satuan</label>
              </div>
            </div>


            <div class="col-md-12">
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" >
                <option value=0 selected>Pilih Satuan</option>
              </select>
            </div>

          </div>
        </div>

        <div class="col-md-2 row">
          <div class="col-12">
            <div class="form-group">
              <label>Satuan Produk</label>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="input_add_edit_satuanproduk" >
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Harga</label>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
              <input type="number" class="form-control text-right" onchange="onChangeInputAddAddHarga()" id="input_add_edit_harga" value ="0.00" >
              </div>
            </div>
          </div>
        </div>


    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_disc" onChange="onChangeInputAddAddDisc()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-2">
      <div class="row">

        <div class="col-12">
          <div class="form-group">
            <label>Disc Rp</label>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_add_edit_discrp" onChange="onChangeInputAddAddDiscRp()" value ="0.00" >
          </div>
        </div>

      </div>
    </div>

        </div>
        </div>
        <div class="col-md-12">
        <div class="row">


        </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Tambah ke PO</label>
              </div>
            </div>
            <div class="col-md-12">
              <select onchange="" id="input_add_edit_tambahkepo" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Pilih</option>
                <option value=1 >Tidak</option>
                <option value=2 >Ya</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Booking</label>
              </div>
            </div>
            <div class="col-md-12">
            <select onchange="" id="input_add_edit_booking" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
              <option value=0 selected>Tidak</option>
              <option value=1 >Ya</option>
            </select>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
              <label>Urgent</label>
              </div>
            </div>
            <div class="col-md-12">
                <select onchange="" id="input_add_edit_urgent" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value=0 selected>Tidak</option>
                <option value=1 >Ya</option>
              </select>
            </div>
          </div>
        </div>
      </div>



          <div class="row mt-2">
            <div class="col-md-12 text-right">
              <button type="button" class="btn btn-batal-add" onclick="closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div></div>

  <div class="container-fluid" style="margin-top: -10px;">
  <div class="row">

    <!-- Total = jumlah Subtotal seluruh item -->
    <div class="col">
      <div class="form-group">
        <label>Total</label>
        <input type="text" class="form-control text-right" id="input_add_total" value="0.00" disabled>
      </div>
    </div>

    <!-- Disc % -->
    <div class="col">
      <div class="form-group">
        <label>Disc %</label>
        <input type="text" inputmode="decimal" class="form-control text-right pso-angka" id="input_add_disc" oninput="hitungDiscHeaderLokal('disc')" onchange="onChangeInputAddDisc()" value="0.00">
      </div>
    </div>

    <!-- DiscRp -->
    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="text" inputmode="decimal" class="form-control text-right pso-angka" id="input_add_discrp" oninput="hitungDiscHeaderLokal('discrp')" onchange="onChangeInputAddDiscRp()" value="0.00" >
      </div>
    </div>

    <!-- DPP -->
    <div class="col">
      <div class="form-group">
        <label>DPP</label>
        <input type="text" class="form-control text-right" id="input_add_dpp" value="0.00" disabled>
      </div>
    </div>

    <!-- PPN -->
    <div class="col">
      <div class="form-group">
        <label>PPN</label>
        <input type="text" class="form-control text-right" id="input_add_ppn" value="0.00" disabled>
      </div>
    </div>

    <!-- Grand Total -->
    <div class="col">
      <div class="form-group">
        <label>Grand Total</label>
        <input type="text" class="form-control text-right" id="input_add_grandtotal" value="0.00" disabled>
      </div>
    </div>

  </div>
</div>

</div>{{-- /#formBsGrid --}}
</div>

<!-- page3 -->

<div id="page3" class="container-fluid" style="display: none" >
<div id="formBsGrid">
      <div class="row">
        <div class="col-6 text-left">
          <h2>Detail SO</h2>
        </div>
        <div class="col-6 text-right">
          <button type="button" class="btn btn-danger btn-lg" style="
          height: 30px;
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          text-transform: uppercase;
          transition: background-color 0.3s, box-shadow 0.3s;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
          onclick="buttonCloseForm()">Close</button>
        </div>
      </div>

<div id="" class="">
  <div class="modal-body" >
<!-- <div class="container-fluid"> -->
  <div class="row">

    <input type="hidden" class="form-control" id="input_detail_nourut" >
    <div class="col-md-3">

      <div class="row">

        <div class="col-md-4" style="margin-top:-40px;">
          <div class="form-group">
            <label>No Bukti</label>
          </div>
        </div>
        <div class="col-md-8" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_nobukti" placeholder="" disabled>
          </div>
        </div>

      <div class="col-md-4" style="margin-top:-12px;">
        <div class="form-group">
          <label>Tanggal</label>
        </div>
      </div>
      <div class="col-md-8" style="margin-top:-12px;">
        <div class="form-group">
          <input type="date" class="form-control text-center" id="input_detail_tanggal" value="{!! date('Y-m-d') !!}" disabled>
        </div>
      </div>


      <div class="col-md-4" style="margin-top:-10px;">
        <div class="form-group">
          <label>Pelanggan</label>
        </div>
      </div>


    <div class="col-md-8" style="margin-top:-10px;">
      <div class="input-group form-group">
        <input type="text" class="form-control text-center" id="input_detail_kodepelanggan" disabled>
      </div>
    </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="row">
        <!-- <div class="col-md-6">
          <div class="row">


        <div class="col-9">
          <div class="form-group">
            <label>Pelanggan</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button class="btn btn-chip-biru btn-sm" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-search"></i></button>
        </div>

      </div>
      </div>
    </div>
    <div class="col-md-6">
    </div> -->


      <!-- <div class="col-md-6">
        <div class="row"> -->



        <div class="col-md-12" style="margin-top:-40px;">
          <div class="form-group">
            <input type="text" class="form-control text-center" id="input_detail_namapelanggan"  disabled>
          </div>
        </div>
        <!-- </div>
      </div> -->
      <!-- <div class="col-md-6">
        <div class="row"> -->


        <div class="col-md-12" style="margin-top:-10px;">
          <div class="form-group">
            <textarea  style="width: 100%; resize: none" rows=3  class="form-control text-center" id="input_detail_alamatpelanggan" disabled></textarea>
          </div>
        </div>
        <!-- </div>
      </div> -->

      </div>
    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-6">

        <div class="row">
          <div class="col-md-4" style="margin-top:-40px;">
            <div class="form-group">
              <label>Valas</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>


      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-40px;">
          <div class="input-group form-group">
            <input type="text" class="form-control text-center" id="input_detail_valas"  disabled>
            <!-- <button onclick="buttonAddListValas()" id="buttonAddListValas"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>
    <div class="col-md-12" style="margin-top:-20px;">
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label>Kurs</label>
          </div>
        </div>
          <div class="col-md-6">
            <div class="form-group">
              <input type="text" class="form-control text-center" id="input_detail_kurs"  disabled>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-12" style="margin-top:-12px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>TOP</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_hari" disabled value=0 min=0 >
          </div>
        </div>
        </div>
    </div>

      </div>

    </div>



    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12" style="margin-top:-40px;">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label>Pembayaran</label>
            </div>
          </div>

        <div class="col-md-6">
          <div class="form-group">
          <select  id="input_detail_pembayaran" disabled class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example">
            <option value=0 selected >Tunai</option>
            <option value=1 >Kredit</option>
          </select>
        </div>
        </div>
        </div>
        </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>TGL KIRIM</label>
              </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control text-left" id="input_detail_tanggalkirim" value="{!! date('Y-m-d') !!}" disabled>
              </div>
            </div>
          </div>

        <div class="col-md-12" style="margin-top:-12px;">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>PPN</label>
              </div>
            </div>
            <div class="col-md-6">
              <select id="input_detail_tipeppn" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
                <option value=0 selected>None</option>
                <option value=1 >Exclude</option>
                <option value=2 >Include</option>
              </select>
            </div>
          </div>
        </div>


      </div>

    </div>
  </div>

  <!-- </div> -->
  <!-- <hr/> -->
  <!-- <div class="row ">
    <div class="col-md-12 text-left">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>
    <button type="button" class="btn btn-chip-biru" onclick="buttonAddMainHeader()"  >Header</button>
    <button type="button" class="btn btn-chip-biru" onclick="buttonAddMainItems()"  >Items</button>
</div>
</div> -->
<hr/>
<div class="row ">
<div class="col-md-12 mt-2 text-left">
  <button type="button" class="btn btn-lg btn-show-hide-header btn-chip-biru" style="
  height: 38px;
  width: 38px;
  margin-top: -40px;
  padding: 0;
  border-radius: 8px;
  font-size: 1.15rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s, box-shadow 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
  onclick="buttonShowHideHeaderDetail()" title="Show/Hide Header"><i class="bi bi-truck"></i></button>
</div>
</div>
  <div class="mt-4" id="modalBodyDetailMainHeader">

  <div class="row">
    <div class="col-md-3">
      <div class="row">

        <div class="col-md-6" style="margin-top:-20px;">
          <div class="form-group">
            <label>Alamat Kirim</label>
          </div>
        </div>

        <div class="col-md-12" style="margin-top:-15px;">
          <div class="form-group">
            <input type="hidden" class="form-control" id="input_detail_kodealamatkirim" >
            <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatkirim"  disabled></textarea>
          </div>
        </div>

      </div>

    </div>

    <div class="col-md-3">
      <div class="row">
        <div class="col-md-8" style="margin-top:-20px;">
          <div class="form-group">
            <label>Ekspedisi</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <!-- <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
        </div>

      </div>

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->
      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group">
          <input type="hidden" class="form-control" id="input_detail_kodelokasipenerima" >
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_alamatlokasipenerima"  value ='-'disabled></textarea>
        </div>
      </div>

      </div>

      <!-- <div class="row">
        <div class="col-9">
          <div class="form-group">
            <label>PIC</label>
          </div>
        </div>
        <div class="col-3 text-right">
          <div class="form-group">
        <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button>
        </div>

      </div>
      </div> -->
      <div class="row">

      <!-- <div class="col-md-12">
        <div class="form-group">

        </div>
      </div> -->

      </div>

    </div>

    <div class="col-md-3">


      <div class="row">

        <div class="col-md-10" style="margin-top:-20px;">
          <label>Keterangan</label>
        </div>

      <div class="col-md-12" style="margin-top:-15px;">
        <div class="form-group" style="margin-top: 14px">
          <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_detail_catatan" disabled></textarea>
        </div>
      </div>

      <!-- <div class="col-md-12">

      </div> -->

      </div>

      <div class="row">

      <!-- <div class="row"> -->

      </div>

      <div class="row ">

  </div>

    </div>

    <div class="col-md-3">
      <div class="row">

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6" style="margin-top:-20px;">
              <div class="form-group">
                <label>DP</label>
              </div>
            </div>

          <div class="col-md-6" style="margin-top:-20px;">
            <div class="form-group">
              <input type="number" class="form-control text-center" id="input_detail_dp" value='0.00' disabled>
            </div>
          </div>
          </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">

            <div class="form-group">
              <label>No PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input  type="text" class="form-control text-center" id="input_detail_nopo"  disabled>
          </div>
        </div>
        </div>

        <div class="row">
          <div class="col-md-6" style="margin-top:-10px;">
            <div class="form-group">
              <label>Tgl PO</label>
            </div>
          </div>

        <div class="col-md-6" style="margin-top:-10px;">
          <div class="form-group">
            <input type="date" class="form-control text-center" id="input_detail_tanggalpo" value="{!! date('Y-m-d') !!}" disabled>
          </div>
        </div>
        </div>
        </div>

      </div>

      <div class="row">

      <!-- <div class="col-md-12">

      </div> -->

      </div>

    </div>
    <!-- <div class="col-md-12 mt-2 text-right" style="margin-bottom: 20px">
    <button type="button" class="btn btn-primary" id="buttonSubmitSaveHeader" onclick="submitSaveHeader()" class="btn btn-secondary"  >Save Header</button>
</div> -->

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-6" style="margin-top:-10px;">
        <div class="form-group">
          <label>PIC</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodepic"  >
            <input type="text" class="form-control" id="input_detail_namapic"  disabled>
            <!-- <button onclick="buttonAddListPIC()" id="buttonAddListPIC"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6">
      <div class="row">

      <div class="col-md-10" style="margin-top:-10px;">
        <div class="form-group">
          <label>Back Office</label>
        </div>
      </div>
      <div class="col-3 text-right">
        <div class="form-group">
      <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
      </div>

    </div>
    </div>
    </div>

    <div class="col-md-6">
      <div class="row">
        <div class="col-md-12">
          <div class="row">

          <!-- <div class="col-4">

          <div class="form-group">

          </div>

          </div> -->
          <div class="col-md-12" style="margin-top:-10px;">
          <div class="input-group form-group">
            <input type="hidden" class="form-control" id="input_detail_kodebackoffice" >
            <input type="text" class="form-control" id="input_detail_namabackoffice"  disabled>
            <!-- <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

          </div>

          </div>
          </div>
        <!-- </div> -->
        </div>
      </div>

    </div>

    <!-- <div class="row"> -->

    <!-- </div> -->

  </div>

</div>

<div class="col-md-3">
  <div class="row">

  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-8" style="margin-top:-10px;">
            <div class="form-group">
              <label>Sales</label>
            </div>
          </div>
          <div class="col-3 text-right">
            <div class="form-group">
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->
          </div>

        </div>

        </div>
      </div>
      <div class="col-md-6" style="margin-top:-10px;">
        <div class="input-group form-group">
          <input type="hidden" class="form-control" id="input_detail_kodesales" >
          <input type="text" class="form-control" id="input_detail_namasales"  disabled>
          <!-- <button onclick="buttonAddListSales()" id="buttonAddListSales"  class="btn btn-primary btn-sm text-right"><i class="bi bi-plus"></i></button> -->

        </div>
      </div>

    </div>

  </div>
  <!-- <div class="col-md-12">
    <div class="form-group">
      <input type="hidden" class="form-control" id="input_detail_kodesales" >
      <input type="text" class="form-control" id="input_detail_namasales"  disabled>
    </div>
  </div> -->
  </div>

</div>

<div class="col-md-3">
  <div class="row">
    <div class="col-md-6" style="margin-top:-25px;">
      <div class="form-group">
        <label>Draft PO</label>
      </div>
    </div>

  <div class="col-md-6" style="margin-top:-25px;">
    <select  id="input_detail_draftpo" class="form-control text-center form-select-lg mb-3" aria-label=".form-select-lg example" disabled>
      <option value=0 selected>Tidak</option>
      <option value=1 >Ya</option>
    </select>
  </div>
  </div>

</div>

  </div>
  <hr/>

</div>

</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="tabel_detail" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Qty</th>
          <th style="padding: 4px 12px;" scope="col">Sat</th>
          <th style="padding: 4px 12px;" scope="col">Harga</th>
          <th style="padding: 4px 12px;" scope="col">Diskon</th>
          <th style="padding: 4px 12px;" scope="col">DPP</th>
          <!-- <th scope="col">Actions</th> -->

        </tr>
      </thead>

      <tbody id="tabel_data_detail" class="text-left" >

        <tr >

          <td></td>
          <td></td>

            <td class="text-center">
              <!-- <button class="btn btn-warning btn-sm" type="button" onclick="" ><i class="bi bi-info-lg"></i></button> -->
              <button class="btn btn-success btn-sm" type="button" ><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger btn-sm" type="button" ><i class="bi bi-trash"></i></button>
              <button class="btn btn-primary btn-sm" type="button" ><i class="bi bi-list"></i></button>
            </td>
      </tr>
      </tbody>

    </table>
  </div>
    <!-- <button onclick="buttonSubKategori()">tes</button> -->
</div>

<div class="row ">
<div class="col-md-12 mt-2 text-right">
<!-- <button type="button" class="btn btn-primary" onclick="buttonAddAddItem()" class="btn btn-secondary"  ><b>+ Tambah Item</b></button> -->
</div>
</div>

<hr/>
</div>

  <div class="container-fluid" style="margin-top: -10px;">
    <div class="row" >

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label>Disc %</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-top:-50px; margin-left:60px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_disc" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DiscRp</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-25px;">
          <div class="form-group">
            <input type="number" class="form-control text-right" id="input_detail_discrp" disabled value ="0.00" >
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-10px;">DPP</label>
          </div>
        </div>
        <div class="col-md-9" style="margin-left:-50px;">
          <div class="form-group">
            <input type="text" class="form-control text-right" id="input_detail_dpp" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label style="margin-left:-40px;">PPN</label>
          </div>
        </div>

        <div class="col-md-9" style="margin-left:-80px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_ppn" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="col" style="width:20%">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group">
            <label style="margin-left:-75px;">Grand Total</label>
          </div>
        </div>

        <div class="col-md-10" style="margin-left:45px; margin-top:-50px;">
          <div class="form-group">
          <input type="text" class="form-control text-right" id="input_detail_grandtotal" value ="0.00" disabled>
          </div>
        </div>
      </div>
    </div>

    </div>

  </div>
</div>
</div>{{-- /#formBsGrid --}}
</div>

<!-- Add this modal HTML once in your Blade template (outside the function) -->
<div class="modal fade" id="modalOtorisasi" tabindex="-1" role="dialog" aria-labelledby="modalOtorisasiLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalOtorisasiLabel">Detail Otorisasi PO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="formBsGrid">
      <div class="modal-body" style="overflow-x: auto;">
        <div class="data-table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th class="num">Qty</th>
                <th class="num">Harga</th>
                <th class="num">Diskon</th>
                <th class="num">Sub Total</th>
                <th class="num">Stock</th>
                <th class="num">Nilai Stock RP</th>
              </tr>
            </thead>
            <tbody id="otorisasi-table-body">
              <tr><td colspan="8" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      </div>{{-- /#formBsGrid --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-confirm-otorisasi">
          <i class="fa fa-check"></i> Otorisasi
        </button>
      </div>
    </div>
  </div>
</div>

<!-- page3 end input_add -->

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
            Cetak PO IDR
          </button>

          <button class="btn btn-primary w-100 mb-2" onclick="choosePrint('design3')">
            Cetak PO Non IDR
          </button>
        </div>

      </div>
    </div>
  </div>
<!-- end modal print-->

@include('purchasing/modals/modalPOAdd')

@endsection

@section('js')
<script>window.RT_LABEL_PAJAK = true</script>
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih bersama (purchasing/modals/modalPOAdd, #form) disamakan persis dengan modal
     pemilih menu Kas, sama seperti di purchaseOrder. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">
$purut=0
let dataTableAdd = []
let dataTableEdit = []
let dataCekHarga = []
let dataAddAddListItem = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

let dataRefreshPenerimaan = []

let listAlamatKirim = []

let selectedNoBukti = ''

let tempAddAdd = {}
let tempAddEdit = {}
let tempIndexEdit = 0
let tempEditAdd = {}
let tempEditEdit = {}

let dataPrintPenerimaan = []

let noBuktiUntukAdd = 0


let tipeform = ''
let tipeformitem = ''
$("#input_add_add_satminus").hide();
$("#input_add_add_isi").hide();
$("#label_add_add_isi").hide();
buttonShowHideHeader ();
console.log('==========cccc=========');



  jQuery(function($) {
    $('.input-partial-number').autoNumeric('init',
      {
        minimumValue : '0',
        // negativeSignCharacter: 'z'
      }
    );
  });


  // Nilai input berpemisah ribuan ("1,250,000.00") -> angka. Sama seperti formatAngkaVal di purchaseOrder.
  function formatAngkaVal (angka) {
    return Number(String(angka == null ? '' : angka).split(',').join('')) || 0
  }

  // Notifikasi gagal AJAX berisi penyebabnya (pesan server / sesi habis), bukan pesan umum saja.
  function pesanGagalServer (err) {
    console.log(err)
    let status = err && err.status
    if (status == 419 || status == 401) {
      alertify.warning('Sesi login habis, silahkan refresh browser lalu login ulang')
      return
    }
    let pesan = err && err.responseJSON && err.responseJSON.message ? err.responseJSON.message : ''
    alertify.warning('Terjadi kesalahan' + (status ? ' (' + status + ')' : '') + (pesan ? ': ' + pesan : ', silahkan refresh browser'))
  }

  // Input angka (qty, harga, isi, freight, Disc %/DiscRp item & header) memakai kelas pso-angka:
  // pemisah ribuan langsung muncul SAAT MENGETIK ("1250000" -> "1,250,000"), titik = desimal
  // (maks 2 digit), posisi kursor dijaga. Saat ditinggalkan dibulatkan ke format "1,250,000.00".
  // Pembacaan nilainya selalu lewat formatAngkaVal() supaya koma pemisah tidak ikut terkirim.
  function formatAngkaKetik (el) {
    let teks = el.value
    let kursor = el.selectionStart == null ? teks.length : el.selectionStart
    // jumlah karakter bermakna (digit / titik) di kiri kursor - koma diabaikan
    let posisi = teks.substring(0, kursor).replace(/[^0-9.]/g, '').length

    let bersih = teks.replace(/[^0-9.]/g, '')
    let titik = bersih.indexOf('.')
    let bulat = titik >= 0 ? bersih.substring(0, titik) : bersih
    let desimal = titik >= 0 ? bersih.substring(titik + 1).replace(/\./g, '').substring(0, 2) : ''
    bulat = bulat.replace(/^0+(?=\d)/, '')
    if (bulat === '' && titik >= 0) { bulat = '0' }
    let hasil = bulat.replace(/\B(?=(\d{3})+(?!\d))/g, ',') + (titik >= 0 ? '.' + desimal : '')
    if (hasil === el.value) { return }
    el.value = hasil

    let i = 0
    let n = 0
    while (i < hasil.length && n < posisi) {
      if (hasil[i] !== ',') { n++ }
      i++
    }
    try { el.setSelectionRange(i, i) } catch (e) {}
  }
  $(document).on('input', '.pso-angka', function () {
    formatAngkaKetik(this)
  })
  $(document).on('focus', '.pso-angka', function () {
    let el = this
    setTimeout(function () { try { el.select() } catch (e) {} }, 0)
  })
  $(document).on('focusout', '.pso-angka', function () {
    this.value = formatAngka(formatAngkaVal(this.value))
  })

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

// function buttonOtorisasi (nobukti) {

//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access');
//     return;
//   }

//   // Update modal title and reset table
//   $('#modalOtorisasiLabel').text('Detail Otorisasi PO: ' + nobukti);
//   $('#otorisasi-table-body').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

//   // Show modal
//   $('#modalOtorisasi').modal('show');

//   // Fetch detail data
//   let _token = $("#_token").val();

//   $.ajax({
//     url: "{!! url('penawaransogetdetail') !!}",
//     type: "POST",
//     data: { _token, nobukti },
//     success: function(res) {
//       let dataTableAdd = res.list;
//       dataCekHarga = res.list
//       console.log('dataTableAdd ' , dataTableAdd)
//       let rows = "";
//       let totalTotal = 0;
//       let saldoTotal = 0;

//       dataTableAdd.forEach((item) => {
//         rows += `<tr>
//           <td>${item.KodeBrg}</td>
//           <td>${item.NamaBrg}</td>
//           <td class="text-right">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
//           <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.SaldoQnt ? formatAngka(parseFloat(item.SaldoQnt).toFixed(2)) : '0.00'}</td>
//           <td class="text-right">${item.SaldoRP ? formatAngka(parseFloat(item.SaldoRP).toFixed(2)) : '0.00'}</td>
//         </tr>`;

//         totalTotal += item.Total;
//         saldoTotal += item.SaldoRP;
//       });

//       rows += `<tr class="border-0">
//         <td colspan="3"></td>
//         <td class="text-right">Total:</td>
//         <td class="text-right">${formatAngka(parseFloat(totalTotal).toFixed(2))}</td>
//         <td class="text-right"></td>
//         <td class="text-right">${formatAngka(parseFloat(saldoTotal).toFixed(2))}</td>
//       </tr>`;

//       $('#otorisasi-table-body').html(rows);
//     },
//     error: function(err) {
//       $('#otorisasi-table-body').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
//     }
//   });

//   // Handle confirm/otorisasi button � unbind first to avoid duplicate handlers
//   $('#btn-confirm-otorisasi').off('click').on('click', function() {

//       let _token = $("#_token").val();
//       // let nobukti = $("#input")
//     console.log("SubmitOtorisasi ")
//     console.log(dataCekHarga)
//     let mssg = ''
//     $.ajax({
//       url: "{!! url('purchaseordercekhargaoto') !!}",
//       type: "POST",
//       data: { _token, tempData: dataCekHarga },
//       success: function(res) {
//         console.log('rescekharga' ,res)

//         for (var i = 0; i < res.length; i++) {
//           console.log('1',i, mssg)

//           console.log('a',i)
//           let xtempx = 1;
//           if (mssg) {
//             mssg += ' , '
//           }
//           // if ()
//           // res.forEach((item, i) => {

//           if (res[i][0].Ket != 'lanjut') {
//             mssg += `
//               Barang ${res[i][0].kodebrg} - ${res[i][0].Ket}
//             `
//           }

//           // });
//           console.log(i, mssg)




//           // alertify.confirm('Konfirmasi Otorisasi', 'Apakah yakin ingin menghapus item ' + String(i) + ' ?',
//           //     function() {
//           //       console.log('yes')
//           //       let xtempx = 1;
//           //     }
//           //   ,function(){
//           //     console.log('no')
//           //       let xtempx = 0;
//           //   };
//           //   if (xtempx == 0) {
//           //     break;
//           //   })

//         }


//         console.log('mssg sini' , mssg)
//         if (mssg) {
//           console.log('mssg yes')
//           alertify.confirm('Konfirmasi Otorisasi', mssg + '. Lanjut otorisasi ?',
//               function() {
//                 console.log('yes')
//                 // return
//                 $.ajax({
//                   url: "{!! url('poupdateotorisasi') !!}",
//                   type: "POST",
//                   data: { _token, nobukti },
//                   success: function(res) {
//                     console.log('Tesresmaxol' , res)
//                     $('#modalOtorisasi').modal('hide');
//                     alertify.success('Berhasil update otorisasi');
//                     loadAll();
//                   },
//                   error: function(err) {
//                     console.log(err);
//                     alertify.warning('Terjadi kesalahan silahkan refresh browser');
//                   }
//                 });
//               }
//             ,function(){
//               console.log('no')
//             });
//             // if (xtempx == 0) {
//             //   break;
//             // })




//         } else {
//           console.log('else')
//           // return
//           console.log({ _token, nobukti })
//           $.ajax({
//             url: "{!! url('poupdateotorisasi') !!}",
//             type: "POST",
//             data: { _token, nobukti },
//             success: function(res) {
//               console.log('resoto',res)
//               $('#modalOtorisasi').modal('hide');
//               alertify.success('Berhasil update otorisasi');
//               loadAll();
//             },
//             error: function(err) {
//               console.log(err);
//               alertify.warning('Terjadi kesalahan silahkan refresh browser');
//             }
//           });
//         }

//       },
//       error: function(err) {
//         console.log(err);
//         alertify.warning('Terjadi kesalahan silahkan refresh browser');
//       }
//     });


//     // return



//   });
// }

function buttonOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

    let _token = $("#_token").val();

  alertify.confirm('Otorisasi Otorisasi', ' Otorisasi penawaran SO ' + nobukti + ' ?',
      function() {
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('penawaransoupdateotorisasi') !!}",
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
            pesanGagalServer(err)
          }

        })
      }
    ,function(){
      console.log('no')
    });

  }


function buttonBatalOtorisasi (nobukti) {
  console.log(nobukti)

  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  alertify.prompt('Batal Otorisasi',"Masukkan keterangan batal otorisasi nomor  " + nobukti, "",
  function(evt, value) {
    // alertify.success("You entered: " + value);
    let xpket = value;

     if (xpket==''){
          alertify.warning('Keterangan harus diisi.');
          return;
        }
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('penawaransoupdatebatalotorisasi') !!}",
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
            pesanGagalServer(err)
          }

        })
      }

    ,function(){
      console.log('no')
      alertify.error("Action cancelled");
    });

}

function onChangeCatatan () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_keterangan").val()
    onChangeHeader('validitas' , value)
  }
}

function onChangePerihal () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_alamatsupplier").val()
    onChangeHeader('keterangan' , value)
  }
}


function onChangeAlamatkirim () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_alamatkirim").val()
    onChangeHeader('franco' , value)
  }
}

function onChangeDelivery () {

  if (tipeform == 'edit') {
    let value  = $("#input_add_ekspedisi").val()
    onChangeHeader('delivery' , value)

  }

}



function onChangeUp () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_up").val()
    onChangeHeader('namapic' , value)
  }
}

function onChangeUp2 () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_up2").val()
    onChangeHeader('namapic2' , value)
  }
}


function onChangeNote () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_note").val()
    onChangeHeader('catatan' , value)
  }
}

function onChangeKetrevisi () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_ketrevisi").val()
    onChangeHeader('ketrevisi' , value)
  }
}

function onChangeFreight () {
  if (tipeform == 'edit') {
    let value  = formatAngkaVal($("#input_add_freight").val())
    onChangeHeader('freight' , value)
  }
}

function onChangeLokasipenerima () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_lokasipenerima").val()
    onChangeHeader('kodekebunh' , value)
  }
}





function onChangeNoPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_noso").val()
    onChangeHeader('ttd' , value)
  }
}

function onChangeTglprcust () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tglprcust").val()
    onChangeHeader('Tglprcust' , value)
  }
}

function onChangeTglrevisi () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tglrevisi").val()
    onChangeHeader('Tglrevisi' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    // onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  } else {
    hitungTotalHeader()
  }


}

function onChangeDP () {
  console.log('onChangeDP')
  if (tipeform == 'edit') {
    let value = $("#input_add_nopocust").val()
    console.log(value)
    onChangeHeader('DP' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeDraftPO () {
  console.log('onChangeDraftPO')
  if (tipeform == 'edit') {
    let value = $("#input_add_draftpo").val()
    console.log(value)
    onChangeHeader('PPO' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }


}

function onChangeHari () {
  console.log('onChangeHari')
  if (tipeform == 'edit')
  {
    let value = $("#input_add_hari").val()
    console.log(value)
    onChangeHeader('HARI' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }
}

// ---- Diskon header (DBPENAWARANSO.DISC = persen, DISCRP = rupiah) ----
// Rumus (sama dengan computed column DBPENAWARANSODET & Purchase Order):
//   Total       = jumlah Sub Total item (Qty x (Harga - Disc item)) x Kurs
//   DiscRp      = Total x Disc % / 100            (Disc % = DiscRp / Total x 100)
//   Non PPN (0) : DPP = Total - DiscRp,                   PPN = 0
//   Exclude (1) : DPP = Total - DiscRp,                   PPN = DPP x tarif PPN
//   Include (2) : DPP = (Total - DiscRp) / (1 + tarif),   PPN = DPP x tarif
//   Grand Total = DPP + PPN
// Tarif PPN diambil dari DBNILAIPPN sesuai tanggal dokumen (getDetail -> NilaiPPNHeader).
// Nilai di form dihitung di sini supaya langsung benar & ikut berubah saat mengetik; DB
// menyamakan DBPENAWARANSODET lewat Sp_UpdatePenawaranSO (refreshUpdateHeader).
function totalItemPenawaran () {
  if (!dataTableAdd || !dataTableAdd.length) { return 0 }
  return dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0)
}

function bulat2 (angka) {
  return Math.round((Number(angka) || 0) * 100) / 100
}

function hitungDiscRpHeader (disc) {
  return bulat2(totalItemPenawaran() * disc / 100)
}

function tarifPPNPenawaran () {
  let tarif = parseFloat(dataHeaderAdd && dataHeaderAdd.NilaiPPNHeader)
  return isNaN(tarif) ? 11 : tarif
}

// DPP / PPN / Grand Total dari Total, DiscRp header, dan tipe PPN yang sedang dipilih.
function hitungTotalHeader (discrp) {
  let total = totalItemPenawaran()
  if (discrp === undefined) { discrp = hitungDiscRpHeader(parseFloat(formatAngkaVal($("#input_add_disc").val())) || 0) }
  let setelahDisc = total - discrp
  // Tipe PPN: pilihan di select; kalau select tidak punya opsi itu (opsinya dibentuk ulang saat
  // pilih customer), pakai PPN header dari DB.
  let pilihanPPN = $("#input_add_tipeppn").val()
  let tipePPN = Number(pilihanPPN !== null && pilihanPPN !== undefined ? pilihanPPN : (dataHeaderAdd && dataHeaderAdd.PPN)) || 0
  let tarif = tarifPPNPenawaran() / 100
  let dpp = tipePPN == 2 ? setelahDisc / (1 + tarif) : setelahDisc
  let ppn = tipePPN == 0 ? 0 : dpp * tarif
  dpp = bulat2(dpp)
  ppn = bulat2(ppn)

  document.getElementById("input_add_total").value = formatAngka(bulat2(total))
  document.getElementById("input_add_dpp").value = formatAngka(dpp)
  document.getElementById("input_add_ppn").value = formatAngka(ppn)
  document.getElementById("input_add_grandtotal").value = formatAngka(bulat2(dpp + ppn))
}

// Ambil Disc % & DiscRp header dari input (sumber = field yang sedang diisi), saling
// mengisi satu sama lain. Nilai di luar batas dipotong diam-diam (tanpa notifikasi).
function bacaDiscHeader (sumber) {
  let total = totalItemPenawaran()
  let disc = parseFloat(formatAngkaVal($("#input_add_disc").val())) || 0
  let discrp = parseFloat(formatAngkaVal($("#input_add_discrp").val())) || 0
  if (sumber == 'discrp') {
    if (discrp < 0) { discrp = 0 }
    if (discrp > total) { discrp = total }
    disc = total ? discrp / total * 100 : 0
  } else {
    if (disc < 0) { disc = 0 }
    if (disc > 100) { disc = 100 }
    discrp = hitungDiscRpHeader(disc)
  }
  return { disc, discrp: bulat2(discrp) }
}

// Saat mengetik: isi pasangan field-nya + DPP/PPN/Grand Total, belum simpan ke DB.
function hitungDiscHeaderLokal (sumber) {
  let d = bacaDiscHeader(sumber)
  if (sumber == 'discrp') {
    document.getElementById("input_add_disc").value = formatAngka(d.disc)
  } else {
    document.getElementById("input_add_discrp").value = formatAngka(d.discrp)
  }
  hitungTotalHeader(d.discrp)
}

function simpanDiscHeader (disc, discrp) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  let berhasil = true
  let daftar = [['DISC', disc], ['DISCRP', discrp]]
  daftar.forEach((f) => {
    $.ajax({
      url: "{!! url('penawaransoonchangeheader') !!}",
      type: "post",
      async: false,
      data: { _token, field: f[0], value: f[1], nobukti },
      error: function (err) {
        console.log(err)
        berhasil = false
      }
    })
  });
  return berhasil
}

// Dipanggil refreshDataTableAdd/Edit/Detail: tampilkan diskon header dari DB + hitung ulang
// DPP/PPN/Grand Total. Kalau DiscRp tersimpan sudah tidak cocok dengan Total sekarang (item
// ditambah/diubah/dihapus), samakan lagi di DB - hanya di mode edit.
// Sama seperti Purchase Order (refreshDataTableAdd di purchaseOrder): DiscRp, DPP, PPN, dan
// Grand Total yang tampil = jumlah per item dari DB (TotDiskon/TotDPP/TotPPN/TotNet = sum
// NDISKON/NDPP/NPPN/NNET, rumus computed column identik DBPODET). Hitungan lokal hanya
// dipakai kalau nilai DB belum ada.
function tampilDiscHeader () {
  let disc = parseFloat(dataHeaderAdd.Disc) || 0
  let discrp = hitungDiscRpHeader(disc)
  document.getElementById("input_add_disc").value = formatAngka(disc)

  let adaTotalDB = dataTableAdd && dataTableAdd.length && dataHeaderAdd.TotNet !== null && dataHeaderAdd.TotNet !== undefined
  if (adaTotalDB) {
    document.getElementById("input_add_total").value = formatAngka(parseFloat(totalItemPenawaran()).toFixed(2))
    document.getElementById("input_add_discrp").value = formatAngka(parseFloat(dataHeaderAdd.TotDiskon || 0).toFixed(2))
    document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP || 0).toFixed(2))
    document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN || 0).toFixed(2))
    document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet || 0).toFixed(2))
  } else {
    document.getElementById("input_add_discrp").value = formatAngka(discrp)
    hitungTotalHeader(discrp)
  }

  if (tipeform == 'edit' && Math.abs((parseFloat(dataHeaderAdd.DiscRpHeader) || 0) - discrp) >= 0.005) {
    simpanDiscHeader(disc, discrp)
  }
}

function simpanDiscHeaderDariInput (sumber) {
  let d = bacaDiscHeader(sumber)
  document.getElementById("input_add_disc").value = formatAngka(d.disc)
  document.getElementById("input_add_discrp").value = formatAngka(d.discrp)
  hitungTotalHeader(d.discrp)

  // Mode add sebelum item pertama tersimpan: header belum ada di DB, nilai disimpan
  // sesudah item pertama berhasil disimpan (lihat submitAddAdd).
  if (tipeform == 'edit') {
    simpanDiscHeader(d.disc, d.discrp)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
  }
}

function onChangeInputAddDisc () {
  simpanDiscHeaderDariInput('disc')
}

function onChangeInputAddDiscRp () {
  simpanDiscHeaderDariInput('discrp')
}

// Salin PPN/Disc %/Kurs header ke tiap item (Sp_UpdatePenawaranSO) supaya DPP, PPN, dan
// Grand Total dihitung ulang DB - sama seperti Sp_UpdatePO di Purchase Order. Sebelumnya
// memanggil pospupdatepo (Sp_UpdatePO) yang hanya bekerja di DBPO, jadi tidak berpengaruh.
function refreshUpdateHeader ()
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('penawaransospupdate') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti
    },
    success: function(res) {
      // alertify.success('update header berhasil')
      // return
      console.log('check')

    },error: function (err) {
      // Penyelarasan di belakang layar - tidak perlu notifikasi ke user.
      console.log(err)
    }
  })
}

function onChangeHeaderSP (field, value , field1 = null , value2 = null)
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('soonchangeheadersp') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success('update header berhasil')
      return
      console.log('check')

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function onChangeHeader (field, value) {
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('penawaransoonchangeheader') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      field,
      value,
      nobukti
    },
    success: function(res) {
      alertify.success(`update ${field} berhasil`)

    },error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}






function submitAddAdd (pchoice) {

    console.log('submitAddAdd')

    let checkDate = new Date($("#input_add_tanggal").val())

    let periode_bulan = document.getElementById("periode_bulan").value
    let periode_tahun = document.getElementById("periode_tahun").value

    if (checkDate.getFullYear() !== Number(periode_tahun) || (checkDate.getMonth() +1) !== Number(periode_bulan)) {
        alertify.warning("Tanggal tidak sesuai periode");
        return
    }

    let TglJatuhTempo = new Date($("#input_add_tanggal").val())

    let hari = $("#input_add_hari").val()

    TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
    console.log(TglJatuhTempo)

    let jmlrecord = 0
    if (dataTableAdd.length) {
      jmlrecord = 1
    }

//    kodegdg, kodeexp, 
// userid, tglinput, nopesanan, tgkirim, masaberlaku , tglbatal , urutmaster , pembayaran , franco , delivery , validitas , namapic  , namapic2 , 
// freight , ketrevisi   , kodekebunh 

// qnt2,isi,idmaster,isVerf,nmsat,tipeso,namamerkso,TglVerf,UserVerf,jmlrecord
    

    let _token  = $("#_token").val()
    let choice = pchoice
    
    let nobukti = $("#input_add_nobukti").val()

    let tanggal = $("#input_add_tanggal").val()
    let tglrevisi = $("#input_add_tglrevisi").val()
    let tglprcust = $("#input_add_tglprcust").val()
    let kodesupp = $("#input_add_kodesupplier").val()
    let tipedisc =0

    let catatan = $("#input_add_note").val()
    let keterangan = $("#input_add_alamatsupplier").val()


    let kodevls = $("#input_add_valas").val()
    let kurs = $("#input_add_kurs").val()
    let ppn = $("#input_add_tipeppn").val()
    let tipebayar = $("#input_add_pembayaran").val()
    let urut = 0


  if (choice != 'I'){
     urut = $purut }


console.log(choice,nobukti,urut,$purut)



    let kodebrg =  $("#input_add_add_kodebarang").val()
    let qnt =  formatAngkaVal($("#input_add_add_qty").val())
    let nosat =  $("#input_add_add_nosat").val()

    let harga = formatAngkaVal($("#input_add_add_harga").val())
    let discp1 = formatAngkaVal($("#input_add_add_discpersen1").val())
    let disctot = formatAngkaVal($("#input_add_add_discrp").val())
    let noppl = $("#input_add_add_noPPL").val()
    let urutppl = $("#input_add_add_urutPPL").val()
    

    let discpdet2 = formatAngkaVal($("#input_add_add_discpersen2").val())
    let discpdet3 = formatAngkaVal($("#input_add_add_discpersen3").val())

    let namabrg =  $("#input_add_add_namabarang").val()


    let ttd = $("#input_add_noso").val()

    // let NOPOCUST = $("#input_add_nopocust").val()

    let ketdet = $("#input_add_add_keteranganbarang").val()

    let pembayaran = $("#input_add_pembayaran").val()
    let franco = $("#input_add_alamatkirim").val()
    let delivery = $("#input_add_ekspedisi").val()  
    let validitas = $("#input_add_keterangan").val()

    let namapic =$("#input_add_up").val()

    let namapic2 =$("#input_add_up2").val()

    let freight =formatAngkaVal($("#input_add_freight").val())
    let ketrevisi =$("#input_add_ketrevisi").val()
    let namacustomer =$("#input_add_namasupplier").val()
    let idmaster=0
    let norpr=''
    let urutrpr =0

    // let nmsat=satuan
    let tipeso=$("#input_add_add_tipeso").val()
    let namamerkso=$("#input_add_add_merkso").val()
    let kodekebunh=$("#input_add_lokasipenerima").val()
    let satminus=$("#input_add_add_satminus").val()
    





    // let date1 = ""
    // if (TglJatuhTempo) {
    //     let date = new Date(TglJatuhTempo);
    //     let day = ("0" + date.getDate()).slice(-2);
    //     let month = ("0" + (date.getMonth() + 1)).slice(-2);
    //     date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    //   }

    // TglJatuhTempo  = date1



    console.log(tempAddAdd)



    let satuan = ''
    let qnt1 = 0
    let isi = 0

    if (kodebrg== '-')
    {nosat=1 
     isi= formatAngkaVal($("#input_add_add_isi").val())

    }
    else {
          cekSatuanBarang(kodebrg)    
          if (nosat == 1) {
            qnt1 = qnt * tempSatuanBarang[0].ISI1
            satuan = tempSatuanBarang[0].SAT1
            isi = tempSatuanBarang[0].ISI1
          }
          if (nosat == 2) {
            qnt1 = qnt * tempSatuanBarang[0].ISI2
            satuan = tempSatuanBarang[0].SAT2
            isi = tempSatuanBarang[0].ISI2
          }
          if (nosat == 3) {
            qnt1 = qnt * tempSatuanBarang[0].ISI3
            satuan = tempSatuanBarang[0].SAT3
            Isi = tempSatuanBarang[0].ISI3
          }
    }


    if (!catatan) {
      catatan = '-'
    }

    console.log({
      _token,
        nobukti,
tanggal ,kodesupp, kodevls, kurs, ppn, tipebayar, hari,keterangan ,catatan, tipedisc,
  pembayaran , franco , delivery , validitas , namapic , ttd , namapic2 , 
freight , ketrevisi , tglrevisi , tglprcust , kodekebunh , urut

,kodebrg,namabrg,qnt,nosat,isi,harga,discp1,disctot,idmaster,norpr,urutrpr,ketdet,satuan,tipeso,namamerkso,jmlrecord,satminus,namacustomer
    })

    console.log('==========' , Number(nosat))

    if (Number(hari) < 0 || Number(qnt) < 0 || Number(harga) < 0 || Number(disctot) < 0)  {
      alertify.warning("Angka negatif")
      return
    }


  let xppn=0
  let xharga=0
  if  ( $("#input_add_tipeppn").val()==2) {
      xppn= harga * 0.1
  } 

 xharga= harga -  formatAngkaVal($("#input_add_discrp").val()) - xppn



console.log('==========start ============================================') 
$.ajax({
  url: "{!! url('penawaransospadd') !!}",
  type: "post",
  async: false,
  data: {
    _token,
    choice,
    nobukti,
tanggal ,kodesupp, kodevls, kurs, ppn, tipebayar, hari,keterangan ,catatan, tipedisc, 
 pembayaran , franco , delivery , validitas , namapic , ttd , namapic2 , 
freight , ketrevisi , tglrevisi , tglprcust , kodekebunh , urut

,kodebrg,namabrg,qnt,nosat,isi,harga,discp1,disctot,idmaster,norpr,urutrpr,ketdet,satuan,tipeso,namamerkso,jmlrecord,satminus,namacustomer
    


  },
  success: function(res) {
    console.log(res)
    if (res == 1) {

      // Disc % header yang diisi sebelum item pertama tersimpan (header baru masuk DB
      // bersama item pertama) disimpan sekarang; DiscRp-nya disamakan oleh tampilDiscHeader().
      let discHeaderBaru = tipeform == 'add' ? (parseFloat(formatAngkaVal($("#input_add_disc").val())) || 0) : 0

      loadAll()
      tipeform = 'edit'
      document.getElementById("input_add_kodesupplier").disabled = true
      document.getElementById("buttonAddListPelanggan").disabled = true
      document.getElementById("buttonAddListValas").disabled = true
      $('#divhargaterakhir').hide();
      $('#divStockProyeksi').hide();
      cleanFormAddAdd()

      if (discHeaderBaru) { simpanDiscHeader(discHeaderBaru, 0); refreshUpdateHeader() }
      refreshDataTableAdd(nobukti)
      if (choice == 'U'){
      $('.showhide').hide();
      alertify.success('Berhasil koreksi item')
      }
      else {
      alertify.success('Berhasil menambah item')
      }
    }
    if(res == 2) {
      setNewNoBukti(1)
      alertify.warning('Nobukti telah direfresh silahkan submit ulang')
    }

  },
  error: function (err) {
    pesanGagalServer(err)
  }

})



              
      
}




function submitAddEdit () {

  console.log('submitAddEdits')

  let checkDate = new Date($("#input_add_tanggal").val())
  let TglJatuhTempo = new Date($("#input_add_tanggal").val())

  let hari = $("#input_add_hari").val()

  TglJatuhTempo.setDate(TglJatuhTempo.getDate() + Number(hari))
  console.log(TglJatuhTempo)

  let Jmlrecord = 0
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  let _token  = $("#_token").val()
  let Choice = "U"
  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_kodesupplier").val()

  let Keterangan = $("#input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#input_add_valas").val()
  let Kurs = $("#input_add_kurs").val()
  let PPn = $("#input_add_tipeppn").val()
  let TipeBayar = $("#input_add_pembayaran").val()
  let Hari = $("#input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //DiscRp
  let Urut = tempAddEdit.Urut
  let KodeBrg =  $("#input_add_add_kodebarang").val()
  let Qnt =  formatAngkaVal($("#input_add_add_qty").val())
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang
  let Harga = formatAngkaVal($("#input_add_add_harga").val())
  let DiscP = formatAngkaVal($("#input_add_add_discpersen1").val())
  let DiscTot = formatAngkaVal($("#input_add_add_discrp").val())
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  let UrutPPL = $("#input_add_add_urutPPL").val()
  // let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = formatAngkaVal($("#input_add_add_discpersen2").val())
  let Discpdet3 = formatAngkaVal($("#input_add_add_discpersen3").val())
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  let Noso = $("#input_add_noso").val()
  //jmlrecord no bukti duplikat
  let NOPOCUST = $("#input_add_nopocust").val()
  //iduser = $user->name
  //pJasa = 0
  //npph23 0
  //perkiraan
  //satX
  //cost
  //subcost
  // let TglKirim = $("#input_add_tanggalkirim").val()
  //pph21
  // let NOPNw = $("#input_add_add_nopnwpo").val()
  let UrutPNW = 0
  let HrgAwal = formatAngkaVal($("#input_add_add_hargaAwal").val())
  let KeteranganBarang = $("#input_add_add_keteranganbarang").val()

  // console.log(kodesupplier,'*')
  // if (!kodesupplier || !kodebackoffice || !nobukti || !valas || !kodealamatkirim || !kodelokasipenerima) {
  //   alertify.warning("Data tidak lengkap")
  //   return
  //}

  if (!NoPPL){
    NoPPL = ''
  };

  let date1 = ""
  if (TglJatuhTempo) {
      let date = new Date(TglJatuhTempo);
      let day = ("0" + date.getDate()).slice(-2);
      let month = ("0" + (date.getMonth() + 1)).slice(-2);
      date1 = date.getFullYear()+"-"+(month)+"-"+(day) ;
    }

  TglJatuhTempo  = date1

  // let tipediskon = 0
  // if (disc) {
  //   tipediskon = 1
  // }
  // if (discrp) {
  //   tipediskon = 1
  // }

  console.log(tempAddEdit)

  let Satuan = ''
  let qnt1 = 0
  let Isi = 0
  if (NoSat == 1) {
    qnt1 = Qnt * tempAddEdit.Isi
    Satuan = tempAddEdit.Satuan
    Isi = tempAddEdit.Isi
  }

  if (NOPNw == '-') {
    UrutPNW = 0
  }

  if (!Keterangan) {
    Keterangan = '-'
  }

  console.log({
    _token,
    Choice,
    NoBukti,
    NoUrut,
    Tanggal,
    TglJatuhTempo,
    KodeSupp,
    // Handling,
    KodeExp,
    Keterangan,
    // FakturSupp,
    KodeVls,
    Kurs,
    PPn,
    TipeBayar,
    Hari,
    // TipeDisc,
    // Disc,
    // DiscRp,
    Urut,
    KodeBrg,
    Qnt,
    NoSat,
    Satuan,
    Isi,
    Harga,
    DiscP,
    DiscTot,
    NoPPL,
    // IsClose,
    // IsCloseD,
    // Catatan,
    // IsExp,
    // Tolerate,
    UrutPPL,
    Kodegdg,
    Discpdet2,
    Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,
    NamaBrg,
    // IsJasa,
    // pFirst,
    pFOC,
    Noso,
    Jmlrecord,
    NOPOCUST,
    // IdUser,
    // pJasa,
    // NPPH23,
    // PERKIRAAN,
    // SatX,
    // COST,
    // SUBCOST,
    TglKirim,
    // PPH21,
    NOPNw,
    UrutPNW,
    HrgAwal,
    KeteranganBarang
  })

  console.log('==========' , Number(NoSat))
  if (!KodeBrg || !Kodegdg) {
    alertify.warning("Data belum lengkap")
    return
  }
  if (Number(Hari) < 0 || Number(Qnt) <= 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
    alertify.warning("Angka negatif")
    return
  }





  let xppn=0
  let xharga=0
  if  ( $("#input_add_tipeppn").val()==2) {
      xppn= Harga * 0.1
  } 
 xharga= Harga -  formatAngkaVal($("#input_add_discrp").val()) - xppn
  // console.log(kodebarang,tanggal,xharga,nosat,choice)
   console.log(KodeBrg,Noso,xharga,NoSat)


   $.ajax({
    url: "{!! url('checkhargaddd') !!}",
    type: "get",
    async: false,
    data: { Noso,KodeBrg,xharga,NoSat
    },
    success: function(res) {
    console.log ('=============================>',res)
    flagharga = res
    console.log ('=============================>',flagharga)
    if (flagharga !='lanjut'){
         alertify.confirm('' + flagharga + ' ?',
          function() { 

          


                                                          $.ajax({
                                                            url: "{!! url('pospadd') !!}",
                                                            type: "post",
                                                            async: false,
                                                            data: {
                                                              _token,
                                                              Choice,
                                                              NoBukti,
                                                              NoUrut,
                                                              Tanggal,
                                                              TglJatuhTempo,
                                                              KodeSupp,
                                                              // Handling,
                                                              KodeExp,
                                                              Keterangan,
                                                              // FakturSupp,
                                                              KodeVls,
                                                              Kurs,
                                                              PPn,
                                                              TipeBayar,
                                                              Hari,
                                                              // TipeDisc,
                                                              // Disc,
                                                              // DiscRp,
                                                              Urut,
                                                              KodeBrg,
                                                              Qnt,
                                                              NoSat,
                                                              Satuan,
                                                              Isi,
                                                              Harga,
                                                              DiscP,
                                                              DiscTot,
                                                              NoPPL,
                                                              // IsClose,
                                                              // IsCloseD,
                                                              // Catatan,
                                                              // IsExp,
                                                              // Tolerate,
                                                              UrutPPL,
                                                              Kodegdg,
                                                              Discpdet2,
                                                              Discpdet3,
                                                              // Discpdet4,
                                                              // Discpdet5,
                                                              // FlagTipe,
                                                              NamaBrg,
                                                              // IsJasa,
                                                              // pFirst,
                                                              pFOC,
                                                              Noso,
                                                              Jmlrecord,
                                                              NOPOCUST,
                                                              // IdUser,
                                                              // pJasa,
                                                              // NPPH23,
                                                              // PERKIRAAN,
                                                              // SatX,
                                                              // COST,
                                                              // SUBCOST,
                                                              TglKirim,
                                                              // PPH21,
                                                              NOPNw,
                                                              UrutPNW,
                                                              HrgAwal,
                                                              KeteranganBarang

                                                            },
                                                            success: function(res) {
                                                              console.log('resspsoaddedit', res)
                                                              loadAll()

                                                              // lockFormAdd()
                                                              $('.showhide').hide();
                                                              refreshDataTableAdd(NoBukti)

                                                              alertify.success('Berhasil edit item')

                                                            },
                                                            error: function (err) {
                                                              console.log(err)
                                                              alertify.warning('Terjadi kesalahan silahkan refresh browser')
                                                            }

                                                          })

                              }

                              
                  ,function(){
                console.log(' cancel harga minimal')
        
                  return
                });


              }else{

                 $.ajax({
                                                            url: "{!! url('pospadd') !!}",
                                                            type: "post",
                                                            async: false,
                                                            data: {
                                                              _token,
                                                              Choice,
                                                              NoBukti,
                                                              NoUrut,
                                                              Tanggal,
                                                              TglJatuhTempo,
                                                              KodeSupp,
                                                              // Handling,
                                                              KodeExp,
                                                              Keterangan,
                                                              // FakturSupp,
                                                              KodeVls,
                                                              Kurs,
                                                              PPn,
                                                              TipeBayar,
                                                              Hari,
                                                              // TipeDisc,
                                                              // Disc,
                                                              // DiscRp,
                                                              Urut,
                                                              KodeBrg,
                                                              Qnt,
                                                              NoSat,
                                                              Satuan,
                                                              Isi,
                                                              Harga,
                                                              DiscP,
                                                              DiscTot,
                                                              NoPPL,
                                                              // IsClose,
                                                              // IsCloseD,
                                                              // Catatan,
                                                              // IsExp,
                                                              // Tolerate,
                                                              UrutPPL,
                                                              Kodegdg,
                                                              Discpdet2,
                                                              Discpdet3,
                                                              // Discpdet4,
                                                              // Discpdet5,
                                                              // FlagTipe,
                                                              NamaBrg,
                                                              // IsJasa,
                                                              // pFirst,
                                                              pFOC,
                                                              Noso,
                                                              Jmlrecord,
                                                              NOPOCUST,
                                                              // IdUser,
                                                              // pJasa,
                                                              // NPPH23,
                                                              // PERKIRAAN,
                                                              // SatX,
                                                              // COST,
                                                              // SUBCOST,
                                                              TglKirim,
                                                              // PPH21,
                                                              NOPNw,
                                                              UrutPNW,
                                                              HrgAwal,
                                                              KeteranganBarang

                                                            },
                                                            success: function(res) {
                                                              console.log('resspsoaddedit', res)
                                                              loadAll()

                                                              // lockFormAdd()
                                                              $('.showhide').hide();
                                                              refreshDataTableAdd(NoBukti)

                                                              alertify.success('Berhasil edit item')

                                                            },
                                                            error: function (err) {
                                                              console.log(err)
                                                              alertify.warning('Terjadi kesalahan silahkan refresh browser')
                                                            }

                        })
              }

    }
})
}










function onChangeInputAddPembayaran () {
  console.log("onChangeInputAddPembayaran")
  let check = Number($("#input_add_pembayaran").val())
  console.log(typeof check)
  console.log(check)

  if (dataTableAdd.length) {

    onChangeHeader('PEMBAYARAN' , check)
  }
  let nobukti = $("#input_add_nobukti").val()
  console.log('len',dataTableAdd.length)
  if (check) {
    let _token = $("#_token").val();
    let kodesupplier = $("#input_add_kodesupplier").val();

    $.ajax({
      url: "{!! url('socekkredithari') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        kodesupplier
      },
      success: function(res) {
        console.log(res)
        if(res.length && res[0].harihutpiut) {
          document.getElementById("input_add_hari").value = res[0].harihutpiut

          if (dataTableAdd.length) {
            console.log('masokk')
            onChangeHeader('HARI' , res[0].harihutpiut)
            refreshUpdateHeader()
            // let nobukti = $("#input_add_nobukti").val()
            refreshDataTableAdd(nobukti)

          }
        }

      }})

  } else {
    document.getElementById("input_add_hari").value = 0
    // console.log('onChangeHari')
    if (tipeform == 'edit') {
      console.log('len', dataTableAdd.length)
      console.log(value)
      // onChangeHeader('TIPEBAYAR' , check)
      if (dataTableAdd.length) {
        console.log('masokk 2')
        onChangeHeader('HARI' , 0)
        refreshUpdateHeader()
        // let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)

      }
    }
  }
}




function onChangeInputAddAddDisc () {
  console.log("onChangeInputAddAddDisc")
  let harga = formatAngkaVal($("#input_add_add_harga").val());

  if (!Number(harga)) {

    document.getElementById("input_add_add_discrp").value = formatAngka('0.00')
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = formatAngka(parseFloat(discRp).toFixed(2))

}

// Harga berubah: DiscRp item dihitung ulang dari Disc % yang sudah diisi (tidak lagi direset ke 0).
function onChangeInputAddAddHarga () {
  document.getElementById("input_add_add_hargaAwal").value = formatAngkaVal(document.getElementById("input_add_add_harga").value)
  calculateDiscRp()
}

function onChangeInputAddEditHarga () {
  document.getElementById("input_add_edit_discrp").value = '0.00'
  document.getElementById("input_add_edit_disc").value = '0.00'
}

function onChangeInputAddAddDiscRp () {
  console.log("onChangeInputAddAddDiscRp")
  let harga = formatAngkaVal($("#input_add_add_harga").val());

  if (!Number(harga)) {

    document.getElementById("input_add_add_disc").value = '0.00'
    return
  }

  let discRp = formatAngkaVal($("#input_add_add_discrp").val());
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = parseFloat(disc).toFixed(2)
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();

  cleanFormAddAdd()
  document.getElementById("input_add_add_kodebarang").disabled = false
  document.getElementById("buttonAddAddListBarang").disabled = false
  $('#h4AddAddItem').show();
  $('#h4AddEditItem').hide();
  $('#submitAddAdd').show();
  $('#submitAddEdit').hide();
  $('#addAddItem').show();
  document.getElementById("input_add_add_namabarang").scrollIntoView();
}

function showTableHargaTerakhir () {
  if ( $("#divStockProyeksi").is(':visible'))
  {
    $('#divStockProyeksi').hide();
  }

  if (!$("#divhargaterakhir").is(':visible'))
  {
    $('#divhargaterakhir').show();
  } else
  {
    $('#divhargaterakhir').hide();
  }
}

function showTableStockProyeksi () {
  if ($("#divhargaterakhir").is(':visible'))
  {
    $('#divhargaterakhir').hide();
  }

  if (!$("#divStockProyeksi").is(':visible'))
  {
    $('#divStockProyeksi').show();
  } else
  {
    $('#divStockProyeksi').hide();
  }
}

function buttonAddEditItem (i) {
  tipeformitem = 'edit'
  let _token = $("#_token").val();
  $('.showhide').hide();
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]

  console.log(typeof tempAddEdit.Harga);
  console.log(tempAddEdit.Harga + " ini harga")

  let selectOption = ''
  if (tempAddEdit.Satuan) {
    selectOption += `<option value=1 selected>${tempAddEdit.Satuan}</option>`
  }

  if (tempAddEdit.NoPNW == ''){
    tempAddEdit.NoPNW = '-'
  }

  if (tempAddEdit.KodeBrg=='-'){
  // {document.getElementById("input_add_add_isi").show()
  //  document.getElementById("label_add_add_isi").show()
  //  document.getElementById("input_add_add_nosatminus").show()
  $("#input_add_add_satminus").show();
  $("#input_add_add_isi").show();
  $("#label_add_add_isi").show();
  $("#input_add_add_nosat").hide();

  }
  else{
 $("#input_add_add_satminus").hide();
  $("#input_add_add_isi").hide();
  $("#label_add_add_isi").hide();
  $("#input_add_add_nosat").show();

  }

   $purut  = tempAddEdit.Urut
  document.getElementById("input_add_add_jasa").value = tempAddEdit.Isjasa
  document.getElementById("input_add_add_foc").value = tempAddEdit.PFOC
  document.getElementById("input_add_add_isi").value = formatAngka(tempAddEdit.Isi)
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KodeBrg
  // Kode barang item yang sudah tersimpan tidak boleh diganti saat edit item; hapus item lalu
  // Tambah Item kalau barangnya salah. Dibuka lagi di buttonAddAddItem().
  document.getElementById("input_add_add_kodebarang").disabled = true
  document.getElementById("buttonAddAddListBarang").disabled = true
  document.getElementById("input_add_add_namabarangasli").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_discpersen1").value = formatAngka(Number(tempAddEdit.DiscP1) ?  tempAddEdit.DiscP1 : '0.00')
  document.getElementById("input_add_add_discpersen2").value = formatAngka(Number(tempAddEdit.Discp2) ?  tempAddEdit.Discp2 : '0.00')
  document.getElementById("input_add_add_discpersen3").value = formatAngka(Number(tempAddEdit.Discp3) ?  tempAddEdit.Discp3 : '0.00')
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddEdit.Qnt).toFixed(2))
  document.getElementById("input_add_add_nosat").innerHTML = selectOption
  document.getElementById("input_add_add_harga").value = formatAngka(Number(tempAddEdit.Harga) ? parseFloat(tempAddEdit.Harga).toFixed(2) : '0.00')
  document.getElementById("input_add_add_discrp").value = formatAngka(Number(tempAddEdit.DISCTOT) ? parseFloat(tempAddEdit.DISCTOT).toFixed(2) : '0.00')
  document.getElementById("input_add_add_noPPL").value = tempAddEdit.NoPPL
  document.getElementById("input_add_add_urutPPL").value = tempAddEdit.UrutPPL
  document.getElementById("input_add_add_keteranganbarang").value = tempAddEdit.KeteranganBarang
  document.getElementById("input_add_add_hargaAwal").value = tempAddEdit.Hrgawal
  document.getElementById("input_add_add_tipeso").value = tempAddEdit.tipeso
  document.getElementById("input_add_add_merkso").value = tempAddEdit.namamerkso
  document.getElementById("input_add_add_satminus").value = tempAddEdit.nmsat
console.log('zzzzzzzzzz',tempAddEdit.KodeBrg,tempAddEdit.Nosat,tempAddEdit.KodeSupp)
  $.ajax({
    url: "{!! url('penawaransocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang: tempAddEdit.KodeBrg,
      nosat: tempAddEdit.Nosat,
      kodecust: tempAddEdit.KodeSupp

    },
    success: function(res) {
      console.log(res.harga_lama);
      console.log(res.cari_beli_akhir);
      console.log(res.cari_beli_akhir_top1);

      let rowTable = ``
      res.cari_beli_akhir.forEach((item, i) => {
        // let date1 = ""
        // if (item.tanggal) {
        //     let date = new Date(item.tanggal);
        //     let day = ("0" + date.getDate()).slice(-2);
        //     let month = ("0" + (date.getMonth() + 1)).slice(-2);
        //     date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
        //   }
        rowTable += `
        <tr>
          <td>${item.KODEBRG}</td>
          <td>${item.namabrg}</td>
          <td>${item.namacustsupp}</td>
          <td>${item.tanggal}</td>
          <td>${item.nobukti}</td>
          <td class="text-right">${Number(item.HARGA) ? formatAngka(item.HARGA) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      document.getElementById("input_add_add_kodebarang").scrollIntoView();

      let rowTableJual = '';

      res.cari_beli_akhir_top1.forEach((item, i) => {

          rowTableJual += `
          <tr>
              <td>${item.tanggal}</td>
              <td>${item.nobukti}</td>
              <td>${item.kodebrg}</td>
              <td>${item.namabrg}</td>
              <td class="text-right">
                ${Number(item.hargabeli)
                    ? parseFloat(item.harga).toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                      })
                    : '0'}
              </td>
              <td>${item.namacustsupp}</td>
              <td>${item.kodekebun}</td>
              <td>${item.namakebun}</td>
          </tr>`;
      });

      document.getElementById("tabel_data_add_stock_proyeksi").innerHTML = rowTableJual;

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();
  $('#h4AddAddItem').hide();
  $('#h4AddEditItem').show();
  $('#submitAddAdd').hide();
  $('#submitAddEdit').show();
  $('#addAddItem').show();

  document.getElementById("input_add_add_namabarang").scrollIntoView();
}


function closeShowHideAdd () {
  $('.showhide').hide();

}


function setNewNoBukti (tipePpn) {
  let _token = $("#_token").val();

  if (tipePpn == 1){
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'PNW',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})
  } else if (tipePpn != 1){
  $.ajax({
    url: "{!! url('spnobukti') !!}",
    type: "post",
    async: false,
    data: {
      kode:'PNW',
      _token
    },
    success: function(res) {

      console.log(res)
      document.getElementById("input_add_nobukti").value = res[0].Nobukti
      document.getElementById("input_add_nourut").value = res[0].Nourut

    }})}

}


function buttonAddListPIC () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();

  if (!kodecustsupp) {
    alertify.warning("Isi pelanggan terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('solistpic') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodecustsupp
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPIC('${item.kodepic}' , '${item.nama}')">
        <td>${item.kodepic}</td>
        <td>${item.nama}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pic").innerHTML = rowTable
      pickerKasInit('tabel_add_list_pic')

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPIC').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddAddListPWO () {

  let _token = $("#_token").val();
  let noSo = $("#input_add_noso").val();

  if (!noSo) {
    alertify.warning("Isi Nomor SO terlebih dahulu")
    return
  }

  $.ajax({
    url: "{!! url('polistpwo') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      noSo
    },
    success: function(res) {
      let rowTable = `
        <tr class="pick-row" onclick="buttonAddPickPWO('-' , '-')">
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
          <td>-</td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPWO('${item.no_bukti}' , '${item.tanggal}')">
          <td>${item.no_bukti}</td>
          <td>${item.tanggal}</td>
          <td>${item.supplier}</td>
          <td>${item.kode}</td>
          <td>${item.NAMABRG}</td>
          <td>${item.qty}</td>
          <td>${item.satuan}</td>
          <td>${item.harga}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_pwo").innerHTML = rowTable

      document.getElementById("namaHeaderTable").textContent = 'Nomor Penawaran PO'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPWO').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

function buttonAddAddListBarang () {


  console.log('buttonAddAddListBarang')

  let _token = $("#_token").val();
  let foc = $("#input_add_add_foc").val();
  let noSo = $("#input_add_noso").val();
  let search = $("#input_add_add_kodebarang").val();

  if (search == '-') {
    $("#input_add_add_namabarangasli").val('');
    $("#input_add_add_namabarang").val('').focus();
    $("#input_add_add_nosat").hide();
    $("#input_add_add_satminus").show();
    $("#input_add_add_isi").show();
    $("#label_add_add_isi").show();


} else {

   $("#input_add_add_nosat").show();
    $("#input_add_add_satminus").hide();
    $("#input_add_add_isi").hide();
    $("#label_add_add_isi").hide();
                console.log(noSo)
                console.log('=======')
                console.log(noBuktiUntukAdd)
                if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_foc')) { $('#tabel_add_list_barang_foc').DataTable().destroy() }

                  $.ajax({
                    url: "{!! url('penawaransolistbarangfoc') !!}",
                    type: "get",
                    async: false,
                    data: {  search
                    },
                    success: function(res) {
                      console.log(res)
                      let rowTable = ``
                      dataAddAddListItem = res
                      dataAddAddListItem.forEach((item, i) => {
                        rowTable += `
                        <tr class="pick-row" onclick="buttonAddAddPickBarangFOCPlus(${i})">
                          <td style="white-space:nowrap;">${item.Kodebrg}</td>
                          <td style="white-space:nowrap;">${item.NamaBrg}</td>
                          <td style="white-space:nowrap;">${item.partNumber}</td>
                          <td style="white-space:nowrap;">${item.NamaMerk}</td>
                        </tr>`
                      });

                      document.getElementById("tabel_data_add_list_barang_foc").innerHTML = rowTable

                      pickerKasInit('tabel_add_list_barang_foc')
                      document.getElementById("namaHeaderTable").textContent = 'Barang (FOC)'
                      $('.showhidemodalbodyadd').hide();
                      $('#modalBodyAddAddListBarangFOC').show();

                      $("#form").modal('toggle')

                    },
                    error: function (err) {
                      console.log(err)
                      alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    }

                  })


 }

}









function buttonAddListNoSO () {

  let _token = $("#_token").val();
  console.log('list ttd===========')

  if ($.fn.DataTable.isDataTable('#tabel_add_list_Ttd')) { $('#tabel_add_list_Ttd').DataTable().destroy() }
  $.ajax({
    url: "{!! url('penawaransolistttd') !!}",
    type: "get",
    async: false,
    data: {
      _token
    },
    success: function(res) {
         console.log(res)
      let rowTable = `
        <tr class="pick-row" onclick="buttonAddPickNoSO('-' , '-')">
          <td>-</td>
          <td>-</td>
          </tr>`

      listNoSo = res

      listNoSo.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickNoSO('${item.boffice}' , '${item.Nama}')">
          <td>${item.boffice}</td>
          <td>${item.Nama}</td>
          </tr>`
      });

      document.getElementById("tabel_data_add_list_Ttd").innerHTML = rowTable
      pickerKasInit('tabel_add_list_Ttd')
      document.getElementById("namaHeaderTable").textContent = 'Ttd'
      $('.showhidemodalbodyadd').hide();
      console.log('hhhhhhhhhhhhhhhhhhhhhhhhh')
      $('#modalBodyAddListTtd').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}






// function buttonAddListGudang () {

//   let _token = $("#_token").val();

//   $('#tabel_add_list_alamatkirim').DataTable().destroy();
//   $.ajax({
//     url: "{!! url('polistgudang') !!}",
//     type: "post",
//     async: false,
//     data: {
//       _token
//     },
//     success: function(res) {
//       let rowTable = ``

//       listAlamatKirim = res

//       listAlamatKirim.forEach((item, i) => {
//         rowTable += `
//         <tr>
//         <td class="text-center"><button class="btn btn-primary btn-sm" style="margin-top:5px; margin-bottom:5px;" onclick="buttonAddPickAlamatKirim(${i} )" type="button" ><i class="bi bi-plus"></i></button></td>
//         <td>${item.KODEGDG}</td>
//         <td>${item.NAMA}</td>
//         <td>${item.Alamat}</td>

//         </tr>`
//       });

//       document.getElementById("tabel_data_add_list_alamatkirim").innerHTML = rowTable
//       $("#tabel_add_list_alamatkirim").DataTable({
//         "lengthChange": false,
//         "paging": true,
//       });

//       document.getElementById("namaHeaderTable").textContent = 'Dikirim Ke'

//       $('.showhidemodalbodyadd').hide();
//       $('#modalBodyAddListAlamatKirim').show();

//       $("#form").modal('toggle')

//     },
//     error: function (err) {
//       console.log(err)
//       alertify.warning('Terjadi kesalahan silahkan refresh browser')
//     }

//   })

// }


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();
  let kodecustsupp = $("#input_add_kodesupplier").val();
  if ($.fn.DataTable.isDataTable('#tabel_add_list_lokasipenerima')) { $('#tabel_add_list_lokasipenerima').DataTable().destroy() }

  $.ajax({
    url: "{!! url('penawaransolistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token,kodecustsupp
    },
    success: function(res) {
      let rowTable = `<tr class="pick-row" onclick="buttonAddPickLokasiPenerima('-' , '-' )">
        <td>-</td>
        <td>-</td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )">
        <td>${item.KodeCustsupp}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_Kebun").innerHTML = rowTable
      pickerKasInit('tabel_add_list_Kebun')

      document.getElementById("namaHeaderTable").textContent = 'Lokasi Penerima'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListKebun').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListValas () {

  if ($.fn.DataTable.isDataTable('#tabel_add_list_valas')) { $('#tabel_add_list_valas').DataTable().destroy() }
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickValas('${item.kodevls}' , '${item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00'}' )">
        <td>${item.kodevls}</td>
        <td>${item.namavls}</td>
        <td>${formatAngka(item.kurs ? parseFloat(item.kurs).toFixed(2) : '0.00')}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_valas").innerHTML = rowTable
      pickerKasInit('tabel_add_list_valas')

      document.getElementById("namaHeaderTable").textContent = 'Valas'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListValas').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListPelanggan ()
{
  if ($.fn.DataTable.isDataTable('#tabel_add_list_pelanggan')) { $('#tabel_add_list_pelanggan').DataTable().destroy() }

  $.ajax({
    url: "{!! url('penawaransolistpelanggan') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickPelanggan('${item.KodeCustSupp}' , '${item.NamaCustSupp}' , '${item.Alamat}','${item.HARIHUTPIUT}', '${item.PPN}')">
        <td>${item.KodeCustSupp}</td>
        <td>${item.NamaCustSupp}</td>
        <td>${item.Alamat}</td>
        </tr>`
      });


      document.getElementById("tabel_data_add_list_pelanggan").innerHTML = rowTable
      pickerKasInit('tabel_add_list_pelanggan')

      document.getElementById("namaHeaderTable").textContent = 'Supplier'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListPelanggan').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListBackOffice () {
  $.ajax({
    url: "{!! url('solistbackoffice') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickBackOffice('${item.keynik}' , '${item.fullname}')">
        <td>${item.keynik}</td>
        <td>${item.fullname}</td>
        </tr>`
      });


      document.getElementById("tabel_data_add_list_backoffice").innerHTML = rowTable
      pickerKasInit('tabel_add_list_backoffice')

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListBackOffice').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function buttonAddListSales () {
  if ($.fn.DataTable.isDataTable('#tabel_add_list_sales')) { $('#tabel_add_list_sales').DataTable().destroy() }
  $.ajax({
    url: "{!! url('solistsales') !!}",
    type: "get",
    async: false,
    data: {

    },
    success: function(res) {
      let rowTable = ``
      res.forEach((item, i) => {
        console.log(item.keynik)
        console.log(item.nama)
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickSales('${item.keynik}' , '${String(item.nama)}')">
        <td>${item.keynik}</td>
        <td>${item.nama}</td>
        </tr>`
      });


      document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable
      pickerKasInit('tabel_add_list_sales')
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListSales').show();
      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

function onChangeInputAddAddNosat () {
  console.log('onChangeInputAddAddNosat')
  let _token  = $("#_token").val()
  let nosat = $("#input_add_add_nosat").val()
  console.log(nosat)
  console.log(Number(nosat))
  let kodebarang = $("#input_add_add_kodebarang").val()

  if (!kodebarang) {
    return
  }

  $.ajax({
    url: "{!! url('socekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang ,
      nosat
    },
    success: function(res) {
      console.log(res)

      let rowTable = ``
      res.forEach((item, i) => {
        rowTable += `
        <tr>
        <td>${item.TANGGAL}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td>${item.Xharga}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      // let rowTable = ``
      // res.forEach((item, i) => {
      //   rowTable += `
      //   <tr>
      //   <td>${item.keynik}</td>
      //   <td>${item.nama}</td>
      //   <td class="text-center"><button class="btn btn-primary btn-sm" onclick="buttonAddPickSales('${item.keynik}' , '${item.nama}')" type="button" ><i class="bi bi-plus"></i></button></td>
      //
      //   </tr>`
      // });
      //
      //
      //
      //
      // if(!res.length) {
      //   rowTable= `<tr><td class="text-center" colspan=3>Tidak ada data</td></tr>`
      // }
      // document.getElementById("tabel_data_add_list_sales").innerHTML = rowTable

      if (tipeformitem == 'add') {
        console.log(tempAddAdd[`Hrg${nosat}_1`])
        if (res.length && res[0].Xharga) {
          console.log('if1')
          document.getElementById("input_add_add_harga").value = formatAngka(res[0].Xharga)
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = formatAngka(tempAddAdd[`Hrg${nosat}_1`])
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = formatAngka('0.00')
          }
        }
      } else {

      }

      // if (res.length && res[0].Xharga) {
      //   document.getElementById("input_add_add_harga").value = res[0].Xharga
      // } else {
      //   if ( nosat == 1) {
      //     if (tempAddAdd.Hrg1_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg1_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 2) {
      //     if (tempAddAdd.Hrg2_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg2_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      //   if ( nosat == 3) {
      //     if (tempAddAdd.Hrg3_1) {
      //       document.getElementById("input_add_add_harga").value = tempAddAdd.Hrg3_1
      //     } else {
      //       document.getElementById("input_add_add_harga").value = '0.00'
      //     }
      //   }
      //
      // }


    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}





/* ============ Header tabel interaktif (window.ReportTable) ============
 * Port 1:1 dari poCart/poAktifkanTabel milik purchaseOrder.blade.php, sama
 * seperti so/invoicejasa/.../closingso. Satu tabel -- tabel2 (urut 1) =
 * Penawaran SO; status otorisasi disaring lewat #modalFilterPSO (tab Otorisasi
 * lama/urut 2 sudah digabung ke sini). Kolom Oto2..Oto5/UserOto2..5/TglOto2..5 di tabel2 cuma
 * dimasukkan ke default cart kalau level otorisasi user (#level, dari
 * $akses->OL) cukup tinggi, sama seperti kondisi "level > N" di markup lama.
 */
let psoCart = { 1 : [], 2 : [] }
let psoActiveUrut = 0
const PSO_HREF = 'penawaranso'
const PSO_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date', 3 : 'bool' }
const PSO_TIPE_KODE = { varchar : 0, float : 1, date : 2, bool : 3 }
let psoPerluGambar = { 1 : false, 2 : false }

function psoPickCI (row, key) {
  if (!row) { return undefined; }
  if (row[key] !== undefined) { return row[key]; }
  let lower = key.toLowerCase();
  for (let k in row) { if (k.toLowerCase() === lower) { return row[k]; } }
  return undefined;
}

function psoDefaultCart (urut) {
  let level = Number($('#level').val()) || 0
  if (urut === 1) {
    let cols = [
      ['NoBukti',      'No Bukti',           1, 'varchar', 0, 0],
      ['Tanggal',      'Tanggal',            1, 'date',    0, 0],
      ['NamaCustSupp', 'Customer',           1, 'varchar', 0, 0],
      ['NOSO',         'No. SO',             1, 'varchar', 0, 0],
      ['TotDPPRp',     'DPP',             1, 'float',   0, 2],
      ['TotPPNRp',     'PPN',             1, 'float',   0, 2],
      ['TotNetRp',     'Grand Total',      1, 'float',   0, 2],
      ['IsOtorisasi1', 'Oto1',               1, 'bool',    0, 0],
      ['OtoUser1',     'User Oto1',          1, 'varchar', 0, 0],
      ['TglOto1',      'Tgl Oto1',           1, 'date',    0, 0],
    ]
    if (level > 1) {
      cols.push(['IsOtorisasi2', 'Oto2', 1, 'bool', 0, 0])
      cols.push(['OtoUser2',     'User Oto2', 1, 'varchar', 0, 0])
      cols.push(['TglOto2',      'Tgl Oto2', 1, 'date', 0, 0])
    }
    if (level > 2) {
      cols.push(['IsOtorisasi3', 'Oto3', 1, 'bool', 0, 0])
      cols.push(['TglOto3',      'Tgl Oto3', 1, 'date', 0, 0])
      cols.push(['OtoUser3',     'User Oto3', 1, 'varchar', 0, 0])
    }
    if (level > 3) {
      cols.push(['IsOtorisasi4', 'Oto4', 1, 'bool', 0, 0])
      cols.push(['TglOto4',      'Tgl Oto4', 1, 'date', 0, 0])
      cols.push(['OtoUser4',     'User Oto4', 1, 'varchar', 0, 0])
    }
    if (level > 4) {
      cols.push(['IsOtorisasi5', 'Oto5', 1, 'bool', 0, 0])
      cols.push(['TglOto5',      'Tgl Oto5', 1, 'date', 0, 0])
      cols.push(['OtoUser5',     'User Oto5', 1, 'varchar', 0, 0])
    }
    cols.push(['IsBatal',   'Batal',         1, 'bool', 0, 0])
    cols.push(['UserBatal', 'User Batal',    1, 'varchar', 0, 0])
    cols.push(['TglBatal',  'Tanggal Batal', 1, 'date', 0, 0])
    return cols
  }
  // urut 2: Otorisasi -- port 1:1 dari markup lama, tanpa level (cuma Oto1).
  return [
    ['NoBukti',      'No Bukti',            1, 'varchar', 0, 0],
    ['Tanggal',      'Tanggal',             1, 'date',    0, 0],
    ['NamaCustSupp', 'Customer',            1, 'varchar', 0, 0],
    ['NOSO',         'No. SO',              1, 'varchar', 0, 0],
    ['TotDPPRp',     'DPP',              1, 'float',   0, 2],
    ['TotPPNRp',     'PPN',              1, 'float',   0, 2],
    ['TotNetRp',     'Grand Total',       1, 'float',   0, 2],
    ['IsOtorisasi1', 'Authorized 1',        1, 'bool',    0, 0],
    ['OtoUser1',     'Authorized User',     1, 'varchar', 0, 0],
    ['TglOto1',      'Authorized Date 1',   1, 'date',    0, 0],
  ]
}

function psoBuatCart (headers, values, isnumerics, isshowns, desimals) {
  headers = headers || []
  let cart = []
  headers.forEach((h, i) => {
    let tipe = Number(isnumerics[i]) || 0
    let des = (desimals && desimals[i] !== undefined && desimals[i] !== null && desimals[i] !== '')
      ? Number(desimals[i]) : (tipe === 1 ? 2 : 0)
    cart.push([values[i], h, Number(isshowns[i]) === 1 ? 1 : 0, PSO_TIPE_NAMA[tipe] || 'varchar', 0, isNaN(des) ? 0 : des])
  });
  return cart
}

function psoAktifkanTabel (urut) {
  psoActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = psoCart[urut]
}

function psoOnChangeAktif () {
  reinitTabel2();
}

window.g_href = PSO_HREF
window.g_modeReport = 1
window.gcart_header = []

window.doSimpanHeader = function (href, mode) {
  let urut = mode || 1
  let cart = psoCart[urut] || []
  let header = [], value = [], isnumber = [], isshown = [], desimal = []
  cart.forEach((c) => {
    header.push(c[1]); value.push(c[0]); isnumber.push(PSO_TIPE_KODE[c[3]] ?? 0)
    isshown.push(Number(c[2]) === 1 ? 1 : 0); desimal.push(Number(c[5]) || 0)
  });
  $.ajax({
    url: "{!! url('saveheadertable') !!}", type: "post", async: false,
    data: {
      _token: $("#_token").val(), header: JSON.stringify(header), isnumber: JSON.stringify(isnumber),
      tipe: JSON.stringify(desimal), value: JSON.stringify(value), isshown: JSON.stringify(isshown),
      href: PSO_HREF, urut: urut
    },
    error: function (err) { console.log(err); alertify.warning('Gagal menyimpan pengaturan kolom') }
  })
}

window.doSetHeader = function (mode, reset) {
  let urut = mode || 1
  $.ajax({
    url: "{!! url('getheadertable') !!}", type: "post", async: false,
    data: { _token: $("#_token").val(), href: PSO_HREF, urut: urut, reset: reset ? 1 : 0 },
    success: function (res) {
      if (!reset && res && res.headertableheader && res.headertableheader.length) {
        psoCart[urut] = psoBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal || [])
      } else {
        psoCart[urut] = psoDefaultCart(urut)
        window.gcart_header = psoCart[urut]
        window.doSimpanHeader(PSO_HREF, urut)
      }
      window.gcart_header = psoCart[urut]
    },
    error: function (err) {
      console.log(err)
      alertify.warning(reset ? 'Gagal mengembalikan kolom ke tampilan default' : 'Gagal memuat pengaturan kolom')
      psoCart[urut] = psoDefaultCart(urut)
      window.gcart_header = psoCart[urut]
    }
  })
}

// Tab Otorisasi sudah digabung ke tab Penawaran SO, jadi hanya ada satu tabel (urut 1).
function activeVisibleTabKeyPSO () {
  return 1;
}

const PSO_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'
const PSO_SELEKTOR_BAR_AKTIF = '#myTabContent .tab-pane.active [id^="rtBarTabel"]'

let psoRtSudahInit = false
function psoInitReportTableSekali () {
  if (psoRtSudahInit || typeof ReportTable === 'undefined') { return }
  psoRtSudahInit = true
  ReportTable.init({ table: PSO_SELEKTOR_TABEL_AKTIF, bar: PSO_SELEKTOR_BAR_AKTIF, onChange: psoOnChangeAktif })

  let psoGuardUlangKlik = false;
  ['#tabel2'].forEach((sel) => {
    let thead = document.querySelector(sel + ' thead')
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (psoGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }
      e.stopPropagation()
      e.preventDefault()
      psoGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles: false, cancelable: true, view: window })
      Object.defineProperty(ulang, 'target', { value: interaktif, configurable: true })
      thead.dispatchEvent(ulang)
      psoGuardUlangKlik = false
    }, true)
  });
}

function tulisTheadHeaderPSO (tableSel, cols, withActions) {
  let thead = document.querySelector(tableSel + ' thead')
  if (!thead || !window.ReportTable) { return; }
  let headRowHtml = ReportTable.headHtml(cols)
  if (withActions) { headRowHtml = headRowHtml.replace('<tr>', '<tr><th style="padding: 4px 12px;">Actions</th>'); }
  thead.setAttribute('style', 'white-space:nowrap;');
  thead.innerHTML = headRowHtml;
}

function psoValueCell (row, col) {
  let raw = psoPickCI(row, col[0]);
  let type = col[3];
  if (type === 'date') { if (!raw) { return '<td></td>'; } return '<td>' + formatDate(raw).split('-').reverse().join('/') + '</td>'; }
  if (type === 'bool') {
    return Number(raw)
      ? '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"></i></td>'
      : '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"></i></td>';
  }
  if (type === 'float') {
    let dp = Number(col[5]) || 0;
    let n = (raw !== undefined && raw !== null && raw !== '') ? Number(raw) : 0;
    return '<td class="text-right">' + (isNaN(n) ? 0 : n).toLocaleString('en-US', { minimumFractionDigits: dp, maximumFractionDigits: dp }) + '</td>';
  }
  return '<td>' + (raw !== undefined && raw !== null ? raw : '') + '</td>';
}

// Tombol per baris mengikuti status otorisasinya, sama seperti Purchase Order:
// belum terotorisasi penuh (NeedOtorisasi = 1) -> Detail/Otorisasi/Edit/Print,
// sudah terotorisasi -> Detail/Batal Otorisasi/Print (tidak bisa diedit lagi).
// Tombolnya baru muncul saat baris di-hover lewat kelas po-aksi-hover pada <table>.
function tabel2ActionsCell (row) {
  let nobukti = psoPickCI(row, 'NoBukti');
  let html = '<td class="text-center">';
  html += '<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Detail" onclick="buttonDetail(\'' + nobukti + '\')"><i class="bi bi-info"></i></button>';
  if (Number(psoPickCI(row, 'NeedOtorisasi'))) {
    html += '<button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
    html += '<button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Edit" onclick="buttonEdit(\'' + nobukti + '\')"><i class="bi bi-pen"></i></button>';
  } else {
    html += '<button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi(\'' + nobukti + '\')"><i class="bi bi-key"></i></button>';
  }
  html += '<button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Print" onclick="submitPrint(\'' + nobukti + '\')"><i class="bi bi-printer"></i></button>';
  html += '</td>';
  return html;
}

function renderTabel2Rows (rows) {
  // window.gcart_header dipakai ReportTable.headHtml() untuk mencari index global
  // (data-gidx) tiap kolom lewat indexOf() -- kalau ini masih menunjuk cart tabel
  // LAIN (mis. karena psoAktifkanTabel(2) dipanggil belakangan di psoSetupSekali),
  // semua th tabel ini dapat data-gidx="-1" dan drag-reorder-nya diam saja.
  // Disamakan di sini supaya render tabel2 tidak bergantung urutan pemanggilan.
  window.gcart_header = psoCart[1]
  let cols = (psoCart[1].length ? psoCart[1] : gcart_header).filter(function (c) { return c[2] === 1; });
  let html = "";
  (rows || []).forEach(function (row) {
    html += '<tr>' + tabel2ActionsCell(row);
    cols.forEach(function (col) { html += psoValueCell(row, col); });
    html += '</tr>';
  });
  document.getElementById('tabel2_data').innerHTML = html;
  tulisTheadHeaderPSO('#tabel2', cols, true);
}

let lastTabel2Rows = []
let psoPanjangHalaman = { 1 : 10 }

function psoIkatSearch (urut) {
  let ids = { 1 : ['psoSearch1', 'tabel2'] }
  let input = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'
  let timer = null
  input.addEventListener('input', function () {
    let nilai = input.value
    if (timer) { clearTimeout(timer) }
    timer = setTimeout(function () {
      if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().search(nilai).draw() }
    }, 400)
  })
}

function psoIkatPanjangHalaman (urut) {
  let ids = { 1 : ['psoLen1', 'tabel2'] }
  let sel = document.getElementById(ids[urut][0])
  let idTabel = ids[urut][1]
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(psoPanjangHalaman[urut])
  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    psoPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    if ($.fn.DataTable.isDataTable('#' + idTabel)) { $('#' + idTabel).DataTable().page.len(psoPanjangHalaman[urut]).draw() }
  })
}

const PSO_DOM_STRING = "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"

// Kotak scroll tabel dibuat setinggi sisa ruang di #content supaya halaman TIDAK perlu
// scrollbar sendiri - yang discroll hanya isi tabel (scrollbar-nya auto-hide). Disalin
// dari poAturTinggiTabel() di purchaseOrder.
function psoAturTinggiTabel () {
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

// Pesan "Tidak ada data" ditampilkan sebagai blok biasa di dalam .po-table-wrap (bukan
// <td colspan>), sama seperti poRapikanBarisKosong() di purchaseOrder.
function psoRapikanBarisKosong (api) {
  let wrap = $(api.table().container()).find('.po-table-wrap')[0]
  if (!wrap) { return }

  let pesan = wrap.querySelector('.po-empty-msg')
  if (!pesan) {
    pesan = document.createElement('div')
    pesan.className = 'po-empty-msg'
    wrap.appendChild(pesan)
  }

  let sel = api.table().body() ? api.table().body().querySelector('td.dataTables_empty') : null
  if (sel) {
    pesan.textContent = sel.textContent
    pesan.hidden = false
    sel.parentNode.style.display = 'none'
  } else {
    pesan.hidden = true
  }
}

function psoOpsiTabel (urut, selTabel) {
  return {
    dom: PSO_DOM_STRING, lengthChange: false, pageLength: psoPanjangHalaman[urut], paging: true, order: [[1, 'asc']], ordering: false,
    language: { emptyTable: 'Tidak ada data', zeroRecords: 'Tidak ada data yang cocok dengan pencarian' },
    drawCallback: function () {
      psoRapikanBarisKosong(this.api())
      setTimeout(psoAturTinggiTabel, 0)
      $(selTabel).find('[data-toggle="tooltip"]').tooltip('dispose')
      $(selTabel).find('[data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
    }
  }
}

function reinitTabel2 () {
  try {
    if ($.fn.DataTable.isDataTable('#tabel2')) { $('#tabel2').DataTable().destroy(); }
    $('#tabel2_data').find('[data-toggle="tooltip"]').tooltip('dispose')
    renderTabel2Rows(lastTabel2Rows);
    $('#tabel2').DataTable(psoOpsiTabel(1, '#tabel2'));
    psoIkatSearch(1); psoIkatPanjangHalaman(1); psoPerluGambar[1] = false;
  } catch (e) { console.error('reinitTabel2 failed:', e); alertify.error('Gagal memperbarui tabel: ' + e.message); }
}

let psoTimerResize = null
$(window).on('resize', function () {
  if (psoTimerResize) { clearTimeout(psoTimerResize) }
  psoTimerResize = setTimeout(psoAturTinggiTabel, 150)
})

// Halaman ini punya banyak $(document).ready() lain yang sudah ada sebelum port
// ini (form Add/Edit, dsb) yang kadang butuh >1 detik untuk selesai jalan secara
// sinkron -- karena jQuery menjalankan semua callback ready() berurutan dalam
// satu antrian, ready() milik blok ini bisa baru jalan lama SETELAH event
// 'load' asli (yang memicu loadAll() di window.onload di bawah) sudah lewat.
// Akibatnya loadAll() sempat menggambar tabel dengan psoCart yang masih kosong
// sebelum doSetHeader() sempat mengisinya, dan `order` menunjuk kolom yang belum
// ada. psoSetupSekali() dibuat idempotent dan dipanggil dari DUA tempat (ready()
// di sini, dan loadAll() sendiri) supaya konfigurasi kolom selalu siap sebelum
// tabel pertama kali digambar, siapa pun yang jalan lebih dulu.
let psoSetupSudahJalan = false
function psoSetupSekali () {
  if (psoSetupSudahJalan) { return }
  psoSetupSudahJalan = true

  psoAktifkanTabel(1); window.doSetHeader(1, false);

  psoInitReportTableSekali();

  $('#nav-home-tab').on('shown.bs.tab', function () { psoAktifkanTabel(1); if (typeof ReportTable !== 'undefined') { ReportTable.refresh(); } if (psoPerluGambar[1]) { reinitTabel2(); } else { psoAturTinggiTabel(); } });
}

$(document).ready(function(){
  psoSetupSekali();
});

function onChangePeriodePSO1 () {
  loadAll();
}

function psoResetFilterFieldsPSO () {
  $('#psoStatusOtorisasi').val('0');
}

function psoUpdateFilterBadgePSO () {
  let n = Number($('#psoStatusOtorisasi').val());
  $('#psoFilterBadge').text(n === 0 ? '0 aktif' : '1 aktif');
}

function buttonFilterPSO () {
  loadAll();
  psoUpdateFilterBadgePSO();
}

function loadAll () {
  psoSetupSekali();

  $.ajax({
    url: "{!! url('penawaransoloadall') !!}",
    type: "get",
    async: false,
    data: {
      tglawal: $('#input_tanggalawal_pso1').val(),
      tglakhir: $('#input_tanggalakhir_pso1').val(),
      statusOtorisasi: $('#psoStatusOtorisasi').val(),
    },
    success: function(res) {
      lastTabel2Rows = res.tempOutstanding || []
      reinitTabel2()
      psoAktifkanTabel(activeVisibleTabKeyPSO());
      console.log('loadall selesai');
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}














function buttonAddAddPickBarangAll (kodebrg) {

  let _token  = $("#_token").val()

  $.ajax({
    url: "{!! url('sodetailbarangall') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebrg : kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)

      if (!res.barang.length) {

        alertify.warning("Terjadi kesalahan pada server")
        return
      }

      tempAddAdd = res.barang[0]
      document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
      document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
      document.getElementById("input_add_add_disc").value = '0.00'
      document.getElementById("input_add_add_discrp").value = formatAngka('0.00')
      let selectOption = ''

      if (tempAddAdd.Sat1) {
        selectOption += `<option value=1 selected>${tempAddAdd.Sat1}</option>`
      }
      if (tempAddAdd.Sat2) {
        selectOption += `<option value=2>${tempAddAdd.Sat2}</option>`
      }
      if (tempAddAdd.Sat3) {
        selectOption += `<option value=3>${tempAddAdd.Sat3}</option>`
      }

      document.getElementById("input_add_add_nosat").innerHTML = selectOption

      console.log(res.harga)
      let rowTable = ``
      res.harga.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
        <td>${date1}</td>
        <td>-</td>
        <td>${item.SATUAN}</td>
        <td>-</td>
        <td>-</td>
        <td class="text-right">${Number(item.Xharga) ? formatAngka(item.Xharga) : '0.00'}</td>
        <td>-</td>
        <td>-</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.harga.length && Number(res.harga[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res.harga[0].Xharga).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = formatAngka('0.00')
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refres.hargah browser')
    }

  })

}







function buttonAddAddPickBarangFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.Kodebrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.Kodebrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0
  // document.getElementById("input_add_add_disc").value = '0.00'
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.Kodebrg,
      nosat : 1
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${date1}</td>
          <td>-</td>
          <td>${item.SATUAN}</td>
          <td>-</td>
          <td>-</td>
          <td class="text-right">${Number(item.Xharga) ? formatAngka(item.Xharga) : '0.00'}</td>
          <td>-</td>
          <td>-</td>

        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].Xharga)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].Xharga).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = formatAngka('0.00')
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}


let cekQntPR = 0
let cekQntPO = 0
let cekQntSisa = 0

function cekQntStock () {

  // tempStockAdd = dataAddAddListItem[0]

  // let currentQntPO = 0

  // cekQntPR = tempStockAdd.Qnt

  // cekQntPO = tempStockAdd.QntPO
  // cekQntSisa = tempStockAdd.SisaPPL

  // currentQntPO = parseInt(document.getElementById("input_add_add_qty").value) || 0

  // console.log(currentQntPO + ' current qnt PO')

  // if (currentQntPO > cekQntSisa) {
  //   alertify.warning('Qnt PO Tidak boleh melebihi Qnt Sisa')
  //   document.getElementById("input_add_add_qty").value = '0.00'
  // }

}

function buttonAddAddPickBarangNonFOC (index , pEdit = 0) {
 console.log('buttonAddAddPickBarangNonFOC xxx')
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = formatAngka(tempAddAdd.SisaPPL)
  console.log(tempAddAdd.SisaPPL,'===================================')
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'
  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? formatAngka(item.HARGA) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? formatAngka(item.HrgNetto) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
        document.getElementById("input_add_add_hargaAwal").value = parseFloat(res[0].HARGA).toFixed(2)
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = formatAngka('0.00')
        }
      }


      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

function buttonAddAddPickBarangNonFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = formatAngka(tempAddAdd.Qnt)
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut
  // document.getElementById("input_add_add_discrp").value = '0.00'

  let selectOption = ''
  if (tempSatuanBarang[0].SAT1) {
    selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
  }
  if (tempSatuanBarang[0].SAT2) {
    selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
  }
  if (tempSatuanBarang[0].SAT3) {
    selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
  }
  document.getElementById("input_add_add_nosat").innerHTML = selectOption

  $.ajax({
    url: "{!! url('pocekharga') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      kodebarang : tempAddAdd.KodeBrg
    },
    success: function(res) {
      console.log(res)
      let rowTable = ``
      res.forEach((item, i) => {
        let date1 = ""
        if (item.TANGGAL) {
            let date = new Date(item.TANGGAL);
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            date1 = date.getFullYear()+"/"+(month)+"/"+(day) ;
          }
        rowTable += `
        <tr>
          <td>${item.NamaCustSupp}</td>
          <td>${date1}</td>
          <td>${item.QNT}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? formatAngka(item.HARGA) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? formatAngka(item.HrgNetto) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = formatAngka('0.00')
        }
      }

      buttonAddListBatal()
      document.getElementById("input_add_add_kodebarang").scrollIntoView();

    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

}

let tempSatuanBarang = []

function cekSatuanBarang (KodeBrg){
  let _token = $("#_token").val()

  $.ajax({
    url: "{!! url('ceksatuanbarang') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      KodeBrg : KodeBrg
    },
    success: function(res) {
      tempSatuanBarang = res
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonAddPickPelanggan (kode, nama , alamat, hari, ppn) {
  console.log('buttonAddPickPelanggan')

  setNewNoBukti(ppn)

  document.getElementById("input_add_kodesupplier").value = kode
  document.getElementById("input_add_kodesupplier").value = kode
  let namaSupplier = document.getElementById("input_add_namasupplier");
    namaSupplier.value = nama;
    namaSupplier.disabled = true;
  // document.getElementById("input_add_namasupplier").value = nama
  // document.getElementById("input_add_alamatsupplier").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = hari
  // document.getElementById("input_add_kodealamatkirim").value = ''
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'

    selectTipeBayar = `
    <option value=1 selected>30 Hari Setelah Barang Dikirim</option>
    <option value=2 >60 Hari Setelah Barang Dikirim</option>
    <option value=3 >45 Hari Setelah Barang Dikirim</option>
    <option value=4 >Cash Before Delivery</option>
    <option value=5 >30 Hari Setelah Pekerjaan Selesai</option>
    <option value=6 >BG 30 hari setelah barang diterima</option>
    `
  

  if (ppn == 1){
    selectTipePPN = `
    <option value=1>Exclude</option>
    <option value=2>Include</option>`
  } else if (ppn == 0) {
    selectTipePPN = `
    <option value=0>None</option>`
  }

  document.getElementById("input_add_pembayaran").innerHTML = selectTipeBayar
  document.getElementById("input_add_tipeppn").innerHTML = selectTipePPN

  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickAlamatKirim (index) {

  let itemX = listAlamatKirim[index]
  console.log(itemX)
  // console.log(kode,nama,alamat)
  if (tipeform == 'edit') {
    // onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    onChangeHeader('franco' , itemX.Alamat)
  }

  // document.getElementById("input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("input_add_alamatkirim").value = itemX.Alamat
  buttonAddListBatal()

}

function buttonAddPickNoSO (kode, nama) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('ttd' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

  buttonAddListBatal()
}



function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUNH' , kode)

  }
  
  document.getElementById("input_add_lokasipenerima").value = kode
  document.getElementById("input_add_namalokasipenerima").value = nama

  buttonAddListBatal()
  // document.getElementById("input_add_kodeekspedisi").scrollIntoView();
}

function buttonAddPickPIC (kode, nama ) {
  console.log('buttonAddPickPIC')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickPWO (kode, nama) {
  console.log('buttonAddPickPWO')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KodePF' , kode)
  }
  document.getElementById("input_add_kodepic").value = kode
  document.getElementById("input_add_namapic").value = nama
  buttonAddListBatal()
}

function buttonAddPickValas (kode, kurs) {
  console.log('buttonAddPickValas')
  console.log(kode,kurs)
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_valas").value = kode
  document.getElementById("input_add_kurs").value = kurs
  buttonAddListBatal()
}

// ---- Valas sebagai dropdown (sama seperti muatDropdownValas/onChangeValas di purchaseOrder) ----
// Daftar diambil dari endpoint yang sama dengan browse valas lama.
let listValasDropdown = []

function muatDropdownValas () {
  $.ajax({
    url: "{!! url('polistvalas') !!}",
    type: "get",
    async: false,
    data: {},
    success: function(res) {
      listValasDropdown = res || []
      let selectEl = document.getElementById("input_add_valas")
      if (!selectEl) { return }
      let kodeTerpilih = selectEl.value
      selectEl.innerHTML = ''
      listValasDropdown.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.kodevls
        opt.textContent = `${item.kodevls} - ${item.namavls}`
        selectEl.appendChild(opt)
      });
      if (listValasDropdown.some(item => item.kodevls === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })
}

// Isi kurs dari valas terpilih - sama dengan buttonAddPickValas(), tanpa menutup modal pemilih.
function onChangeValasDropdown () {
  let kode = document.getElementById("input_add_valas").value
  let itemX = listValasDropdown.find(item => item.kodevls === kode)
  let kurs = itemX && itemX.kurs ? parseFloat(itemX.kurs).toFixed(2) : '0.00'
  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }
  document.getElementById("input_add_kurs").value = kurs
}

// Kode lama mengunci valas dengan men-disable / menyembunyikan tombol #buttonAddListValas.
// Dropdown mengikuti status tombol itu supaya aturan kuncinya tetap sama.
function mktSinkronKunciValas () {
  let btn = document.getElementById("buttonAddListValas")
  let sel = document.getElementById("input_add_valas")
  if (!btn || !sel) { return }
  sel.disabled = btn.disabled || btn.hidden
}

$(function () {
  muatDropdownValas()
  mktSinkronKunciValas()
  let btn = document.getElementById("buttonAddListValas")
  if (btn && window.MutationObserver) {
    new MutationObserver(mktSinkronKunciValas).observe(btn, { attributes: true, attributeFilter: ['disabled', 'hidden'] })
  }
})

function buttonAddPickSales (kode, nama ) {
  console.log('buttonAddPickSales')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODESLS' , kode)

  }
  document.getElementById("input_add_kodesales").value = kode
  document.getElementById("input_add_namasales").value = nama
  buttonAddListBatal()
  // $("#form").modal('toggle')
}

function buttonAddPickBackOffice (kode, nama ) {
  console.log('buttonAddPickBackOffice')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('Boffice' , kode)

  }
  document.getElementById("input_add_kodebackoffice").value = kode
  document.getElementById("input_add_namabackoffice").value = nama
  buttonAddListBatal()

  document.getElementById("input_add_kodebackoffice").scrollIntoView();

}

function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('toggle')
}

function cleanFormAddAdd () {

  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_namabarang").value = ''
  document.getElementById("input_add_add_namabarangasli").value = ''
  document.getElementById("input_add_add_keteranganbarang").value = ''
  document.getElementById("input_add_add_tipeso").value = ''
  document.getElementById("input_add_add_merkso").value = ''
  document.getElementById("input_add_add_satminus").value = ''
  document.getElementById("input_add_add_qty").value = formatAngka('0.00')
  document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  // document.getElementById("input_add_add_satuanproduk").value = ''
  document.getElementById("input_add_add_harga").value = formatAngka('0.00')
  // document.getElementById("input_add_add_disc").value = '0.00'
  document.getElementById("input_add_add_discrp").value = formatAngka('0.00')
  document.getElementById("input_add_add_discpersen1").value = formatAngka('0.00')
  document.getElementById("input_add_add_discpersen2").value = formatAngka('0.00')
  document.getElementById("input_add_add_discpersen3").value = formatAngka('0.00')
  document.getElementById("input_add_add_hargaAwal").value = '0.00'
  // document.getElementById("input_add_add_tambahkepo").value = 0

}

function lockFormAdd () {
   document.getElementById("input_add_kodesupplier").disabled = true
    document.getElementById("input_add_alamatsupplier").disabled = true
    document.getElementById("input_add_freight").disabled = true
     document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_alamatkirim").disabled = true
   document.getElementById("input_add_ekspedisi").disabled = true
  document.getElementById("input_add_pembayaran").disabled = true
  document.getElementById("input_add_nopocust").disabled = true
  document.getElementById("input_add_noso").disabled = true
  document.getElementById("input_add_keterangan").disabled = true
  document.getElementById("input_add_tglrevisi").disabled = true
  document.getElementById("input_add_tglprcust").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_draftpo").disabled = true


  document.getElementById("input_add_up").disabled = true
  document.getElementById("input_add_up2").disabled = true
  document.getElementById("input_add_note").disabled = true
  // document.getElementById("input_add_catatan").disabled = true
  document.getElementById("input_add_ketrevisi").disabled = true
  document.getElementById("input_add_namalokasipenerima").disabled = true
  document.getElementById("input_add_lokasipenerima").disabled = true
  // document.getElementById("buttonaddlistpenerima").disabled = true


  document.getElementById("input_add_add_discpersen1").disabled = true
  document.getElementById("input_add_add_discpersen2").disabled = true
  document.getElementById("input_add_add_discpersen3").disabled = true
  document.getElementById("input_add_add_foc").disabled = true

  document.getElementById("buttonAddListPelanggan").hidden = true
  // document.getElementById("buttonAddListGudang").hidden = true
  document.getElementById("buttonAddListSales").hidden = true
  document.getElementById("buttonAddListValas").hidden = true
  document.getElementById("buttonAddListPIC").hidden = true
  document.getElementById("buttonAddListLokasiPenerima").disabled = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListNoSo").hidden = true
  document.getElementById("buttonTambahItem").hidden = true

  document.getElementById("input_add_disc").disabled = true
  document.getElementById("input_add_discrp").disabled = true
  document.getElementById("input_add_add_tipeso").disabled = true
  document.getElementById("input_add_add_merkso").disabled = true
  document.getElementById("input_add_add_satminus").disabled = true





}

function buttonShowHideHeader ()
{
  var modal = document.getElementById("modalBodyAddMainHeader");
  console.log($('#modalBodyAddMainHeader').css('display'))
  if($('#modalBodyAddMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function buttonShowHideHeaderDetail () {
  var modal = document.getElementById("modalBodyDetailMainHeader");
  console.log($('#modalBodyDetailMainHeader').css('display'))
  if($('#modalBodyDetailMainHeader').css('display') === 'block') {
    modal.style.display = "none";
  } else {
    modal.style.display = "block";
  }
}

function unlockFormAdd () {
  // document.getElementById("input_add_tipeppn").disabled = false
  // document.getElementById("input_add_pembayaran").disabled = false
  // document.getElementById("input_add_nopocust").disabled = false
  // document.getElementById("input_add_noso").disabled = false
  // document.getElementById("input_add_keterangan").disabled = false
  // document.getElementById("input_add_tglprcust").disabled = false
  //  document.getElementById("input_add_tglrevisi").disabled = false
  // document.getElementById("input_add_hari").disabled = false
  // document.getElementById("input_add_draftpo").disabled = false


  
  // document.getElementById("input_add_up").disabled = false
  // document.getElementById("input_add_up2").disabled = false
  // document.getElementById("input_add_note").disabled = false

  // document.getElementById("input_add_ketrevisi").disabled = false
  // document.getElementById("input_add_namalokasipenerima").disabled = false
  // document.getElementById("input_add_lokasipenerima").disabled = false

  // document.getElementById("input_add_add_discpersen1").disabled = false
  // document.getElementById("input_add_add_discpersen2").disabled = false
  // document.getElementById("input_add_add_discpersen3").disabled = false
  // document.getElementById("input_add_add_foc").disabled = false

  // document.getElementById("buttonAddListPelanggan").hidden = false

  // document.getElementById("buttonAddListSales").hidden = false
  // document.getElementById("buttonAddListValas").hidden = false
  // document.getElementById("buttonAddListPIC").hidden = false

  // document.getElementById("buttonAddListBackOffice").hidden = false
  // document.getElementById("buttonAddListBackOffice").hidden = false
  // document.getElementById("buttonAddListNoSo").hidden = false
  // document.getElementById("buttonTambahItem").hidden = false

  // document.getElementById("input_add_disc").disabled = false
  // document.getElementById("input_add_discrp").disabled = false


// ==========================
 document.getElementById("input_add_kodesupplier").disabled = false
    document.getElementById("input_add_alamatsupplier").disabled = false
    document.getElementById("input_add_freight").disabled = false
     document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_alamatkirim").disabled = false
   document.getElementById("input_add_ekspedisi").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_nopocust").disabled = false
  document.getElementById("input_add_noso").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  document.getElementById("input_add_tglrevisi").disabled = false
  document.getElementById("input_add_tglprcust").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false


  document.getElementById("input_add_up").disabled = false
  document.getElementById("input_add_up2").disabled = false
  document.getElementById("input_add_note").disabled = false
  // document.getElementById("input_add_catatan").disabled = false
  document.getElementById("input_add_ketrevisi").disabled = false
  document.getElementById("input_add_namalokasipenerima").disabled = false
  document.getElementById("input_add_lokasipenerima").disabled = false
  // document.getElementById("buttonaddlistpenerima").disabled = false


  document.getElementById("input_add_add_discpersen1").disabled = false
  document.getElementById("input_add_add_discpersen2").disabled = false
  document.getElementById("input_add_add_discpersen3").disabled = false
  document.getElementById("input_add_add_foc").disabled = false

  document.getElementById("buttonAddListPelanggan").hidden = false
  // document.getElementById("buttonAddListGudang").hidden = false
  document.getElementById("buttonAddListSales").hidden = false
  document.getElementById("buttonAddListValas").hidden = false
  document.getElementById("buttonAddListPIC").hidden = false
  document.getElementById("buttonAddListLokasiPenerima").disabled = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonAddListNoSo").hidden = false
  document.getElementById("buttonTambahItem").hidden = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false




}

function cleanFormAdd () {

  document.getElementById("input_add_nobukti").value = ''
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  // document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_kodesupplier").value = ''
  document.getElementById("input_add_namasupplier").value = ''
  document.getElementById("input_add_alamatsupplier").value = ''
  // document.getElementById("input_add_kodealamatkirim").value = 'GMPL'
  document.getElementById("input_add_alamatkirim").value = ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  // document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_valas").value = ''
  document.getElementById("input_add_kurs").value = ''
  document.getElementById("input_add_nopocust").value = '-'
  document.getElementById("input_add_noso").value = '-'
  document.getElementById("input_add_kodebackoffice").value = ''
  document.getElementById("input_add_namabackoffice").value = ''
  document.getElementById("input_add_tipeppn").value = 0
   document.getElementById("input_add_freight").value = formatAngka(0)
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_kodesales").value = ''
  document.getElementById("input_add_namasales").value = ''
  document.getElementById("input_add_hari").value = 0
  document.getElementById("input_add_draftpo").value = 0



  document.getElementById("input_add_up").value = ''
  document.getElementById("input_add_up2").value = ''
  document.getElementById("input_add_note").value = ''
  // document.getElementById("input_add_catatan").value = ''
  document.getElementById("input_add_ketrevisi").value = ''
  document.getElementById("input_add_namalokasipenerima").value = ''
  document.getElementById("input_add_lokasipenerima").value = ''
  


  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_nopocust").disabled = false
  document.getElementById("input_add_noso").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  // document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false

  document.getElementById("buttonAddListPelanggan").disabled = false
  // document.getElementById("buttonAddListGudang").disabled = false
  document.getElementById("buttonAddListSales").disabled = false
  document.getElementById("buttonAddListValas").disabled = false
  document.getElementById("buttonAddListPIC").disabled = false
  // document.getElementById("buttonAddListLokasiPenerima").disabled = false
  document.getElementById("buttonAddListBackOffice").disabled = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false

  document.getElementById("input_add_disc").value = formatAngka('0.00')
  document.getElementById("input_add_discrp").value = formatAngka('0.00')
  document.getElementById("input_add_total").value = '0.00'
  document.getElementById("input_add_ppn").value = '0.00'
  document.getElementById("input_add_dpp").value = '0.00'
  document.getElementById("input_add_grandtotal").value = '0.00'
}

function buttonEdit (NOBUKTI) {
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


  tipeform = 'edit'
  console.log('buttonEdit' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  unlockFormAdd()
  // Mode edit: customer dan valas tidak boleh diubah (dropdown valas ikut status
  // #buttonAddListValas lewat mktSinkronKunciValas).
  document.getElementById("input_add_kodesupplier").disabled = true
  document.getElementById("buttonAddListPelanggan").disabled = true
  document.getElementById("buttonAddListValas").disabled = true

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('penawaransocekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res.isOtorisasi
    },
    error: function (err) {
      console.log(err)
      console.log(err.status)
      console.log(err.statusText)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  if (oto == 1) {
    alertify.warning("Sudah diotorisasi")
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableEdit(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}



function buttonAdd (noBukti) {
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

  if (noBukti != null){
  noBuktiUntukAdd = noBukti
  }
  if ( noBukti == null){
    noBuktiUntukAdd = 0
  }

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').hide();
  let akses = $("#akses_istambah").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  dataTableAdd = []
  cleanFormAdd()
  unlockFormAdd()
    const now = new Date()
    const tanggalCetak = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }).replace(/\//g, '/')

    console.log(tanggalCetak, now)

  refreshDataTableAdd()
  document.getElementById("input_add_valas").value = 'IDR'
  document.getElementById("input_add_kurs").value = '1.00'

  // Tanggal diambil dari server (getdate) supaya tidak bisa diakali lewat jam komputer user
  // (sama seperti pembelianpermintaanagen). Input tanggal tetap disabled di semua mode.
  $.ajax({
    url: "{!! url('gettanggalserver') !!}",
    type: "get",
    async: false,
    success: function(res) {
      document.getElementById("input_add_tanggal").value = res[0].tanggal
      // Mode add: Tgl Revisi & Tgl PR.Cust default hari ini (masih bisa diubah user);
      // di mode edit/detail nilainya diambil dari database.
      document.getElementById("input_add_tglrevisi").value = res[0].tanggal
      document.getElementById("input_add_tglprcust").value = res[0].tanggal
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal mengambil tanggal server, silahkan refresh browser')
    }
  })

  $('#page1').hide();
  $('#page2').show();

}

function buttonCloseForm () {
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

  // Perubahan header di form (mis. PPN Exclude -> Include) langsung tersimpan ke DB
  // lewat onChangeHeader(), jadi tabel depan perlu dimuat ulang saat form ditutup.
  loadAll();
}

function buttonCloseFormDetail () {
  $('#page3').hide();
  $('#page1').show();

}

function submitAdd ()
{

  let alamatpelanggan = $("#input_add_alamatsupplier").val();
  console.log(alamatpelanggan)
  let catatan = $("#input_add_keterangan").val();
  console.log(catatan)

}

function buttonAddMainHeader() {
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainHeader').show();
  // $('#buttonAddListPelanggan').hide();
}

function buttonAddMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodyaddmain').hide();
  $('#modalBodyAddMainItems').show();
}

function buttonDetailMainHeader() {
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainHeader').show();
  // $('#buttonDetailListPelanggan').hide();
}

function buttonDetailMainItems() {
  $('.showhide').hide();
  $('.showhidemodalbodydetailmain').hide();
  $('#modalBodyDetailMainItems').show();
}

function cekPoDet () {

  let _token  = $("#_token").val()
  $.ajax({
    url: "{!! url('cekPoDet') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res){
      console.log(res)
    },
    error: function (err) {
      console.log(err)
    }
  })
}

function buttonDetail (NOBUKTI) {
  tipeform = 'detail'
  console.log('button Detail' , NOBUKTI)

  $('.showhide').hide();
  // $('.showhidemodalbodyaddmain').hide();
  $('#buttonSubmitSaveHeader').show();
  lockFormAdd()

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  $('.showhidemodalbodyadd').hide();
  // $('#modalBodyAddListPelanggan').show();
  $('#modalBodyAddMain').show();
  refreshDataTableDetail(NOBUKTI)
  // $("#form").modal('toggle')
  $('#page1').hide();
  $('#page2').show();
}

function refreshDataTableAdd (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="8">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('penawaransogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${formatAngka(item.Qnt)}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td class="text-center">
                ${tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="8">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

         


          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          console.log('======================= nnn1',dataHeaderAdd.NamaCustSupp)
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_freight").value = formatAngka(parseFloat(dataHeaderAdd.freight).toFixed(2))
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_note").value = dataHeaderAdd.catatan
          document.getElementById("input_add_up").value = dataHeaderAdd.namapic
          document.getElementById("input_add_up2").value = dataHeaderAdd.namapic2
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.franco
          document.getElementById("input_add_ketrevisi").value = dataHeaderAdd.ketrevisi
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.delivery
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.validitas
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_lokasipenerima").value = dataHeaderAdd.kodekebunh
          document.getElementById("input_add_namalokasipenerima").value = dataHeaderAdd.namakebun


          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tglprcust").value = dataHeaderAdd.tglprcust ? formatDate(dataHeaderAdd.tglprcust) : ""
          document.getElementById("input_add_tglrevisi").value = dataHeaderAdd.tglrevisi ? formatDate(dataHeaderAdd.tglrevisi) : ""
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          tampilDiscHeader()


        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    let rowHeader = ""
            rowHeader =
            `<tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function refreshDataTableEdit (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="8">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('penawaransogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-right">${formatAngka(item.Qnt)}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td class="text-center">
                ${tipeform == 'edit' ?
                `<button class="btn btn-success btn-sm" type="button" onclick="buttonAddEditItem(${i})"><i class="bi bi-pen"></i></button>
                <button class="btn btn-danger btn-sm" type="button" onclick="buttonAddDeleteItem(${i})"><i class="bi bi-trash"></i></button>`
                : `-`
                }
              </td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="8">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable
          $purut=dataHeaderAdd.Urut
          console.log('urut koreksi hapus', $purut)
         document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti

           console.log('======================= nnn2',dataHeaderAdd.NamaCustSupp)
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp


          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_freight").value = formatAngka(dataHeaderAdd.freight)
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_note").value = dataHeaderAdd.catatan
          document.getElementById("input_add_up").value = dataHeaderAdd.namapic
          document.getElementById("input_add_up2").value = dataHeaderAdd.namapic2
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.franco
          document.getElementById("input_add_ketrevisi").value = dataHeaderAdd.ketrevisi
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.delivery
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.validitas
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_lokasipenerima").value = dataHeaderAdd.kodekebunh
          document.getElementById("input_add_namalokasipenerima").value = dataHeaderAdd.namakebun


          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tglprcust").value = dataHeaderAdd.tglprcust ? formatDate(dataHeaderAdd.tglprcust) : ""
          document.getElementById("input_add_tglrevisi").value = dataHeaderAdd.tglrevisi ? formatDate(dataHeaderAdd.tglrevisi) : ""
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          tampilDiscHeader()





          

          noBuktiUntukAdd = dataHeaderAdd.NoPPL

        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

    let rowHeader = ""
            rowHeader =
            `<tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">Actions</th>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function refreshDataTableDetail (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="8">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('penawaransogetdetail') !!}",
      type: "post",
      async: false,
      data: {
        _token,
        nobukti: NOBUKTI
      },
      success: function(res) {
        console.log('aaa')
        console.log('res' , res)

        if (!res.list.length) {
          alertify.warning("Data habis")
          //  $("#form").modal('toggle')
          $('#page3').hide();
          $('#page2').hide();
          $('#page1').show();
        } else {
          dataHeaderAdd = res.list[0]
          dataTableAdd = res.list

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td>${item.KeteranganBarang || ''}</td>
              <td class="text-right">${formatAngka(item.Qnt)}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="8">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
           console.log('======================= nnn',dataHeaderAdd.NamaCustSupp)
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_freight").value = formatAngka(parseFloat(dataHeaderAdd.freight).toFixed(2))
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_note").value = dataHeaderAdd.catatan
          document.getElementById("input_add_up").value = dataHeaderAdd.namapic
          document.getElementById("input_add_up2").value = dataHeaderAdd.namapic2
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.franco
          document.getElementById("input_add_ketrevisi").value = dataHeaderAdd.ketrevisi
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.delivery
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.validitas
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_lokasipenerima").value = dataHeaderAdd.kodekebunh
          document.getElementById("input_add_namalokasipenerima").value = dataHeaderAdd.namakebun


          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tglprcust").value = dataHeaderAdd.tglprcust ? formatDate(dataHeaderAdd.tglprcust) : ""
          document.getElementById("input_add_tglrevisi").value = dataHeaderAdd.tglrevisi ? formatDate(dataHeaderAdd.tglrevisi) : ""
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          tampilDiscHeader()

        }

      },
      error: function (err) {
        console.log(err)
        console.log(err.status)
        console.log(err.statusText)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }
    })

          let rowHeader = ""

            rowHeader =
            `<tr>
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Keterangan</th>
                  <th style="padding: 4px 12px;" scope="col">Qty</th>
                  <th style="padding: 4px 12px;" scope="col">Sat</th>
                  <th style="padding: 4px 12px;" scope="col">Harga</th>
                  <th style="padding: 4px 12px;" scope="col">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col">Sub Total</th>
                </tr>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function submitPrint (nobukti) {

    let _token = $('#_token').val()

    $.ajax({
      url: "{!! url('penawaransoprint') !!}",
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
    for (let i = 0; i < dataPrint.length; i+=100)
    {
      let tempArray = dataPrint.slice(i,i+100)
      arrayDataPrint.push(tempArray)
    }

    document.getElementById('imagecontainerTtd').querySelector('img').src = `img/ttd/${dataPrint[0].ttd}.png`;
    let printContent = ''
    let imageContent = document.getElementById(`imagecontainer`).innerHTML;
    let imageContentTtd = document.getElementById(`imagecontainerTtd`).innerHTML;
    let css = ''
    let hdr = ''
    let str= ''
    let ftr= ''
    let tanggalOnly = dataPrint[0].tanggal.split(' ')[0].split('-').reverse().join('/');
    let tanggalKirimOnly = ''

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

    .no-border { border: none; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .m-0 { margin: 0; }
    .pb-1 { padding-bottom: 0.25rem; }
    .pb-2 { padding-bottom: 0.5rem; }
    .ps-3 { padding-left: 1rem; }
    .pe-1 { padding-right: 0.25rem; }
    .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mt-3 { margin-top: 1rem; }

    .body-main-prints {
      width: 21cm;
      height: 29.7cm;
      position: relative;
    }

    .footer-sign {
      padding-top: 5px;
      position: absolute;
      width: 100%;
      bottom: 30px;
    }

    .footer-print-date {
      position: absolute;
      width: 95%;
      bottom: 130px;
    }

    .detail-spb-table { margin: 0; }
  </style>`;
    hdr = `<div style="display: flex; justify-content: space-between; width: 100%">

                  <div class="pe-1" style="width: 100%">
                    <div style="width: 100%">
                      <div class="pb-1" style="width: 55%; margin-top: 15px">
                        `+ imageContent +`
                      </div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; margin-top: 10px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Ref.No</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%">`+dataPrint[0].nobukti+`</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Tanggal</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%">`+tanggalOnly+`</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Kepada Yth</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%">${dataPrint[0].NAMA ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Lokasi</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%">${dataPrint[0].namalokasi ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Up</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%">${dataPrint[0].namapic ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%">Dari</div>
                      <div class="pb-1" style="width: 3%">:</div>
                      <div class="pb-1" style="width: 100%">${dataPrint[0].nmprs ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%"></div>
                      <div class="pb-1" style="width: 3%"></div>
                      <div class="pb-1" style="width: 100%">${dataPrint[0].alamatprs ?? ''}</div>
                    </div>
                    
                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%"></div>
                      <div class="pb-1" style="width: 3%"></div>
                      <div class="pb-1" style="width: 100%">${dataPrint[0].kotaprs ?? ''}</div>
                    </div>

                    <div style="display: flex; width: 100%; font-size: 13px; padding: 2px;">
                      <div class="pb-1" style="width: 10%"></div>
                      <div class="pb-1" style="width: 3%"></div>
                      <div class="pb-1" style="width: 100%">Telp: (0541) 4104142 Email: sml@indo.net.id</div>
                    </div>

                  </div>

                </div>

              <div style="width:100%; text-align:center; margin:30px 0;">
                <span style="
                    font-size:15px;
                    text-decoration: underline;
                    text-decoration-thickness: 2px;">
                    PERIHAL : `+dataPrint[0].keterangan+`
                </span>
              </div>

              <div style="display: flex; width: 80%; font-size: 13px;">
                 <div class="pb-1" style="width: 30%;">Dengan hormat,</div>
              </div>

              <div style="display: flex; width: 100%; font-size: 13px;">
                 <div class="pb-1" style="width: 100%;">Bersama ini perkenankanlah kami menawarkan harga sebagai berikut:</div>
              </div>
    <table
    class="detail-spb-table"
    style="width: 98%; height: 100px; max-height: 100px; font-family: sans-serif; display: table; border: 1px solid #3c3c3c; font-size: 10px;">
                <thead>
                  <tr style="background-color:#609af7">
                    <td class="text-center" style="color:White; width: 2%" >No.</td>
                    <td class="text-center" style="color:White; width: 35%">Nama Barang</td>
                    <td class="text-center" style="color:White; width: 5%">Tipe</td>
                    <td class="text-center" style="color:White; width: 5%">Merk</td>
                    <td class="text-center" style="color:White; width: 6%">Qty</td>
                    <td class="text-center" style="color:White; width: 4%">Sat</td>
                    <td class="text-center" style="color:White; width: 10%">Harga</td>
                    <td class="text-center" style="color:White; width: 8%">Disc</td>
                    <td class="text-center" style="color:White; width: 10%">Total</td>
                    <td class="text-center" style="color:White; width: 15%">Keterangan</td>
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
            <td style="width: 2%; text-align: center; font-size: 10px;">${z+1}</td>

            <td style="width: 35%; text-align: left; font-size: 10px;">${itemSub.NAMABRG}</td>

            <td style="width: 5%; text-align: center; font-size: 10px;">${itemSub.tipeso}</td>

            <td style="width: 5%; text-align: left; font-size: 10px;">${itemSub.namamerkso ?? ''}</td>

            <td style="width: 6%; text-align: right; font-size: 10px;">${itemSub.QNT ? formatAngka(itemSub.QNT) : ''}</td>

            <td style="width: 4%; text-align: center; font-size: 10px;">${itemSub.SATUAN}</td>

            <td style="width: 10%; text-align: right; font-size: 10px;">${formatAngka(parseFloat(itemSub.hrg).toFixed(2))}</td>

            <td style="width: 8%; text-align: right; font-size: 10px;">${formatAngka(parseFloat(itemSub.DISCTOT).toFixed(2))}</td>

            <td style="width: 10%; text-align: right; font-size: 10px;">${formatAngka(parseFloat(itemSub.subtotalpenawaran).toFixed(2))}</td>

            <td style="width: 15%; text-align: left; font-size: 10px;">${itemSub.ketdet ?? ''}</td>
          </tr>`;
        z++;
      });

      tempPrintStr += `
        <tr>
          <td colspan="7" style="border:1px solid; padding:5px; font-size: 10px;">
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            Grand total
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            ${formatAngka(parseFloat(dataPrint[0].totalnnet || 0).toFixed(2))}
          </td>
          <td colspan="1" style="border:1px solid; padding:5px;">
          </td>
        </tr>
        <tr>
          <td colspan="7" style="border:1px solid; padding:5px;">
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            Freight
          </td>
          <td style="border:1px solid; text-align:right; font-size: 10px;">
            ${formatAngka(parseFloat(dataPrint[0].freight || 0).toFixed(2))}
          </td>
          <td colspan="1" style="border:1px solid; padding:5px;">
          </td>
        </tr>`;

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

         tempPrintStr += `<div style="width: 100%;">


  <div style="width: 95%; font-family: sans-serif; font-size: 10px;">
     <div style="display: flex; width: 100%; padding:10px 0;">
      <div>
        <span style="
          font-weight: bold;
          font-style: italic;
          font-size: 13px;
          border-bottom: 1px solid #000;
          line-height: 1.5;">
          “Mohon perhatikan spesifikasi barang yang kami tawarkan di atas, barang tidak dapat DIRETUR setelah PO kami proses. Mohon menjadi perhatian bersama. Terima kasih.”
        </span>
      </div>
    </div>


    <div style="display: flex; width: 100%; margin-top: 10px; padding: 2px 0; font-size: 13px;">
      <div>Adapun penawaran yang kami ajukan dengan kondisi sebagai berikut:</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Harga</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].myppn+`</div>
    </div>


    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Pembayaran</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].ketpemb+`</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Franco</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].franco+`</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Delivery</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].delivery+`</div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Validitas</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].validitas+`<span style="font-weight:bold; margin-left:1px;">
          (HARGA DAN STOK TIDAK TERIKAT)
        </span></div>
    </div>

    <div style="display: flex; width: 80%; padding: 3px 0; font-size: 13px;">
      <div class="pb-1" style="width: 15%;">Note</div>
      <div class="pb-1" style="width: 70%;">: `+dataPrint[0].catatan+`</div>
    </div>

    <div style="display: flex; width: 100%; padding: 15px 0; font-size: 13px;">
      <div class="pb-1" style="width: 100%;">Demikian penawaran harga dari kami dengan harapan mendapat kabar baik dari Bapak/Ibu.
        Atas perhatian dan kerjasama nya kami ucapkan terimakasih.
    </div>
    </div>


    <div style="display: flex; width: 80%; margin-top: 20px; font-size: 13px;">
      <div class="pb-1" style="width: 30%;">Hormat kami ,</div>
    </div>

    <div style="display: flex; width: 100%;">
      <div class="pb-1" style="width: 100%; margin-top: 15px">
        `+ imageContentTtd +`
      </div>
    </div>

    <div style="display: flex; width: 80%; margin-top: 10px; font-size: 13px;">
      <div class="pb-1" style="width: 100%;">`+dataPrint[0].namattd+`</div>
    </div>

    <div style="display: flex; width: 80%; font-size: 13px;">
      <div class="pb-1" style="width: 100%;">`+dataPrint[0].jabatan+`</div>
    </div>


    


  </div>`

     tempPrintStr += `
      <div style="width:50%; margin-top:15px; margin-left:-25px; float:left; text-align:center; font-size:15px;">
       
      </div>

        <div style="width:50%; float:left; font-size:15px; margin-top:-10px; margin-left:-15px;">
        <table style="width:100%; border-collapse: collapse;">

       
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


function submitPrint2 (nobukti) {

let _token = $('#_token').val()

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

  .body-main-prints {
    width: 21cm;
    height: 14cm;
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
                  <div class="pb-1" style="width: 45%">PO Number</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+dataPrint[0].nobukti+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Date</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Due Date</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+tanggalKirimOnly+`</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">TOP</div>
                  <div class="pb-1" style="width: 5%">:</div>
                  <div class="pb-1" style="width: 50%">`+dataPrint[0].HARI+` Hari</div>
                </div>
                <div style="display: flex; width: 100%">
                  <div class="pb-1" style="width: 45%">Currency</div>
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
                <td class="text-center" style="width: 35%">DESCRIPTION</td>
                <td class="text-center" style="width: 15%">PART NUMBER</td>
                <td class="text-center" style="width: 10%">BRAND</td>
                <td class="text-center" style="width: 8%">QTY</td>
                <td class="text-center" style="width: 5%">UOM</td>
                <td class="text-center" style="width: 10%">UNIT PRICE</td>
                <td class="text-center" style="width: 15%">AMOUNT</td>
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
  <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 35%;">${itemSub.NAMABRG}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: center;">${itemSub.PartNumber}</td>
  <td style='border-left:1px solid black; border-right:1px solid black;' class="no-border" style="width: 10%;">${itemSub.NAMAMERK ?? ''}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 8%; text-align: right;">${itemSub.QNT ? formatAngka(itemSub.QNT) : ''}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 5%; text-align: center;">${itemSub.SATUAN}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 10%; text-align: right;">${formatAngka(parseFloat(itemSub.harga).toFixed(2))}</td>
  <td class="no-border" style="border-left:1px solid black; border-right:1px solid black; width: 15%; text-align: right;">${formatAngka(parseFloat(itemSub.SUBTOTALRp).toFixed(2))}</td>
</tr>`;
z++;
});

// Fill remaining empty rows   table is 225px, each row ~24px, header ~24px = ~8 total slots
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

tempPrintStr += `</tbody>`;
tempPrintStr += `</table>`;

     tempPrintStr += `<div style="display: flex; width: 100%; margin-top: 13px;">

<div style="width: 38%; font-family: sans-serif; font-size: 10px;">
<div style="display: flex; width: 100%">
  <h3 class="m-0 pb-2">Ship To:</h3>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">CV. SINAR MAHAKAM LESTARI</div>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">`+dataPrint[0].AlamatGudang+`</div>
</div>
<div style="display: flex; width: 100%">
  <h3 class="m-0 pb-2">Appointed Forwarder:</h3>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">${dataPrint[0].Expedisi ?? ''}</div>
</div>
<div style="display: flex; width: 100%">
  <div class="pb-1" style="width: 100%">${dataPrint[0].almkirim ?? ''}</div>
</div>
<div style="display: flex; width: 100%">
  <h3 class="m-0 pb-2">Please send all original document to address below:</h3>
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

if(i == arrayDataPrint.length - 1){
      
tempPrintStr += `
<div style="width: 62%; font-family: sans-serif; font-size: 10px;">

<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
  <div style="width: 5%; text-align:left;"> JUMLAH </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].tsub).toFixed(2))}</div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 4px; position: relative;">
  <div style="width: 5%; text-align:left;"> DISKON </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].Tdisc).toFixed(2))}</div>

  <div style="
  position: absolute;
  right: 0;
  bottom: 0;
  width: 35%;
  border-bottom: 1px solid #000;"></div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 2px;">
  <div style="width: 5%; text-align:left;"> DPP </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TSUBTOTALRp).toFixed(2))}</div>
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
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnppnRp).toFixed(2))}</div>
</div>
<div style="display: flex; font-size:10px; justify-content: flex-end; width: 92%; padding-bottom: 8px; font-weight: bold;">
  <div style="width: 5%; text-align:left;"> TOTAL </div>
  <div style="width: 30%; text-align: right">${formatAngka(parseFloat(dataPrint[0].TnnetRp).toFixed(2))}</div>
</div>`};
  
 tempPrintStr += `
 <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-top: 6px;">
    <tr>
      <td class="no-border text-center" style="width: 40%; font-size:13px;">Approved By,</td>
      <td class="no-border text-center" style="width: 33%; font-size:13px;">Confirmed By,</td>
    </tr>
    <tr style="height: 2.5rem;">
      <td class="no-border" colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td class="no-border px-2">
        <p class="m-0"></p>
      </td>
      <td class="no-border px-2">
        <p class="m-0" style="border-bottom: 1px solid black; font-size:10px;">Name</p>
      </td>
    </tr>
    <tr>
      <td class="no-border px-2 text-center">
        <p class="m-0" style='font-size:10px;'>`+dataPrint[0].otouser+`</p>
        <p class="m-0" style='font-size:10px;'>
        ELECTRONICALLY APPROVED
     </p>
      </td>
      <td class="no-border px-2">
        <p class="m-0" style="border-bottom: 1px solid black; font-size:10px;">Date</p>
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




function buttonAddDeleteItem (i) {
  console.log('data hapus',i)

  let akses = $("#akses_ishapus").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }

  console.log(dataTableAdd[i])
  let dataDelete = dataTableAdd[i]

  alertify.confirm('Hapus Item', 'Apakah yakin ingin menghapus item ' + dataDelete.NamaBrg + ' ?',
      function() {

        let _token = $("#_token").val();
        let choice = "D"

        let nobukti = dataDelete.NoBukti
        let urut = dataDelete.Urut
        console.log('delete',nobukti,urut)
        $.ajax({
          url: "{!! url('penawaransospadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            choice,
            nobukti,
            NoUrut:0,
            Tanggal: '',
            TglJatuhTempo: '',
            KodeSupp: '',
            // Handling,
            KodeExp: '',
            Keterangan: '',
            // FakturSupp,
            KodeVls: '',
            Kurs: 0,
            PPn: 0,
            TipeBayar: 0,
            Hari: 0,
            // TipeDisc,
            // Disc,
            DiscRp: 0,
            urut,
            KodeBrg: 0,
            Qnt: 0,
            NoSat: 0,
            Satuan: '',
            Isi: 0,
            Harga: 0,
            DiscP: 0,
            // DiscTot,
            NoPPL: '',
            // IsClose,
            // IsCloseD,
            // Catatan,
            // IsExp,
            // Tolerate,
            UrutPPL: 0,
            Kodegdg: '',
            Discpdet2: 0,
            Discpdet3: 0,
            // Discpdet4,
            // Discpdet5,
            // FlagTipe,
            NamaBrg: '',
            // IsJasa,
            // pFirst,
            pFOC: 0,
            Noso: '',
            Jmlrecord: 0,
            NOPOCUST: '',
            // IdUser,
            // pJasa,
            // NPPH23,
            // PERKIRAAN,
            // SatX,
            // COST,
            // SUBCOST,
            TglKirim: '',
            // PPH21,
            NOPNw: '',
            UrutPNW: 0

          },
          success: function(res) {
            console.log('resspsoadd', res)
            loadAll()

            // lockFormAdd()
            $('.showhide').hide();

            refreshDataTableAdd(nobukti)

            alertify.success('Berhasil menghapus item')

          },
          error: function (err) {
            pesanGagalServer(err)
          }

        })
      }
    ,function(){
      console.log('no')
    });

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

    return [year, month, day].join('-');
}

function searchBarangAll (e) {
  if (e.which == 13) {
    console.log('enter')

    let search = $("#input_search_barang_all").val();

    $('#tabel_add_list_barangall').DataTable().destroy();

  }

}

// Angka -> teks 2 desimal berpemisah ribuan ("1,250,000.00"). Disamakan dengan formatAngka di
// purchaseOrder: menerima angka maupun teks, nilai kosong/bukan angka jadi '0.00', negatif tetap benar.
function formatAngka (angkaString) {
  if (angkaString === null || angkaString === undefined || angkaString === '') {
    return '0.00'
  }
  if (isNaN(Number(angkaString))) {
    return '0.00'
  }
  let teks = parseFloat(angkaString).toFixed(2)
  let minus = teks[0] === '-'
  if (minus) { teks = teks.substring(1) }
  let tempAngka = teks.split('.')
  let temp1 = ''
  for (let i = 0; i < tempAngka[0].length; i++) {
    if (i != 0 && i % 3 == 0) {
      temp1 = ',' + temp1
    }
    temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
  }
  temp1 += '.' + tempAngka[1]
  return (minus ? '-' : '') + temp1
};

// Diskon per item: DiscRp (per satuan) <-> Disc % terhadap Harga, saling mengisi saat diketik.
// Nilai di luar batas dipotong diam-diam (tanpa notifikasi): Disc % maks 100, DiscRp maks Harga.
function reverseCalculateDiscPercent() {
  let elDiscRp = document.getElementById('input_add_add_discrp')
  let harga = parseFloat(formatAngkaVal($('#input_add_add_harga').val())) || 0;
  let discRp = parseFloat(formatAngkaVal(elDiscRp.value)) || 0;

  document.getElementById('input_add_add_discpersen2').value = formatAngka(0)
  document.getElementById('input_add_add_discpersen3').value = formatAngka(0)

  if (harga <= 0) {
    document.getElementById('input_add_add_discpersen1').value = formatAngka(0)
    return;
  }

  if (discRp > harga) {
    discRp = harga
    elDiscRp.value = formatAngka(discRp)
  }

  let discPercent = (discRp / harga) * 100;
  document.getElementById('input_add_add_discpersen1').value = formatAngka(discPercent)
}

function calculateDiscRp() {
  let disc1 = formatAngkaVal(document.getElementById('input_add_add_discpersen1').value)
  let disc2 = formatAngkaVal(document.getElementById('input_add_add_discpersen2').value)
  let disc3 = formatAngkaVal(document.getElementById('input_add_add_discpersen3').value)

  let discRp = formatAngkaVal($('#input_add_add_harga').val())

  disc1 = Math.min(parseFloat(disc1) || 0, 100)
  disc2 = Math.min(parseFloat(disc2) || 0, 100)
  disc3 = Math.min(parseFloat(disc3) || 0, 100)
  discRp = parseFloat(discRp) || 0

  if ((parseFloat(formatAngkaVal(document.getElementById('input_add_add_discpersen1').value)) || 0) > 100) {
    document.getElementById('input_add_add_discpersen1').value = formatAngka(100)
  }

  let currentAmount = discRp
  let totalDiscount = 0

  if (disc1 > 0) {
    let afterDiskon1 = currentAmount * (disc1/100)
    currentAmount = currentAmount - afterDiskon1
    totalDiscount += afterDiskon1
  }

  if (disc2 > 0) {
    let afterDiskon2 = currentAmount * (disc2/100)
    currentAmount = currentAmount - afterDiskon2
    totalDiscount += afterDiskon2
  }

  if (disc3 > 0) {
    let afterDiskon3 = currentAmount * (disc3/100)
    currentAmount = currentAmount - afterDiskon3
    totalDiscount += afterDiskon3
  }

  document.getElementById('input_add_add_discrp').value = formatAngka(bulat2(totalDiscount))
}

function LockFreeOfCharge(){
  let focState = document.getElementById('input_add_add_foc').value

  if (focState == 1){
    document.getElementById('input_add_add_harga').disabled = true;
    document.getElementById('input_add_add_discrp').disabled = true;
    document.getElementById('input_add_add_discpersen1').disabled = true;
    document.getElementById('input_add_add_discpersen2').disabled = true;
    document.getElementById('input_add_add_discpersen3').disabled = true;

    document.getElementById('input_add_add_harga').value = formatAngka(0)
    document.getElementById('input_add_add_discrp').value = formatAngka(0)
    document.getElementById('input_add_add_discpersen1').value = formatAngka(0)
    document.getElementById('input_add_add_discpersen2').value = formatAngka(0)
    document.getElementById('input_add_add_discpersen3').value = formatAngka(0)
  } else {
    document.getElementById('input_add_add_harga').disabled = false;
    document.getElementById('input_add_add_discrp').disabled = false;
    document.getElementById('input_add_add_discpersen1').disabled = false;
    document.getElementById('input_add_add_discpersen2').disabled = false;
    document.getElementById('input_add_add_discpersen3').disabled = false;
  }

}

</script>
  <script>
    function performSearchSupplier () {
      const searchValue = document.getElementById('input_add_kodesupplier').value.trim();

      buttonAddListPelanggan();

      // Apply search to all DataTables
      $('#tabel_add_list_pelanggan').DataTable().search(searchValue).draw();
    }

    // Keyboard event
    document.getElementById('input_add_kodesupplier').addEventListener('keypress', function(event) {

      if (event.key === 'Enter') {
         let kode = document.getElementById("input_add_kodesupplier").value.trim();
          if (kode != "-") {
          event.preventDefault();
          performSearchSupplier();
        }
      }
    });

    function performSearch () {
      const searchValue = document.getElementById('input_add_add_kodebarang').value.trim();

      buttonAddAddListBarang();

      // Apply search to all DataTables
      $('#tabel_add_list_barang_nonfoc').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_nonfocplus').DataTable().search(searchValue).draw();
      $('#tabel_add_list_barang_foc').DataTable().search(searchValue).draw();
    }

    // Keyboard event
    document.getElementById('input_add_add_kodebarang').addEventListener('keypress', function(event) {
      if (event.key === 'Enter') {
          event.preventDefault();
          performSearch();
      }
    });

    window.onload = function(){
      loadAll();
    };

    function checkKodeSupplier() {
      console.log('cek supp');
      let kode = document.getElementById("input_add_kodesupplier").value.trim();
      let nama = document.getElementById("input_add_namasupplier");

      if (kode === "-") {
          nama.disabled = false;
          nama.value = "";
          setNewNoBukti(1);
          nama.focus();
      } else {
          nama.disabled = true;
      }
  }
  </script>

@endsection
