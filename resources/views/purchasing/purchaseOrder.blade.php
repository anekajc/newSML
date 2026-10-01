@extends('newmasterTest')
@section('buttons')

@section('page-title', 'Purchase Order')

@endsection
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
  #tabel_add_harga_terakhir tbody tr:nth-of-type(odd),
  #tabel_add_stock_proyeksi tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #tabel tbody tr:hover,
  #tabel2 tbody tr:hover,
  #tabel3 tbody tr:hover,
  #tabelso tbody tr:hover,
  #tabel_add tbody tr:hover {
    background-color: #f5f3ff;
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
  <img src="img/sml.png" style="height: 50px; width: 80px" alt="">
</div>

<div id="page1">
    <!-- <div id="qrcode"></div> -->
    <!-- <div class="toolbar" style="margin-bottom: 20px">
      <div class="page-title">Purchase Order</div>
    </div> -->

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

    <div class="po-kpi-strip" id="poKpiStrip"></div>

    <!-- UNTUK TAB -->
    <div class="card mb-3 tab-card">
      <div class="card-body">
        <div class="nav nav-tabs border-0 custom-tabs" id="nav-tab" role="tablist">

            <a class="nav-item nav-link active"
              id="nav-profile-tab"
              data-toggle="tab"
              href="#profile"
              role="tab"
              aria-controls="profile"
              aria-selected="true">
                Purchase Order
            </a>

            <a class="nav-item nav-link"
              id="nav-home-tab"
              data-toggle="tab"
              href="#home"
              role="tab"
              aria-controls="home"
              aria-selected="false">
                Outstanding PR
            </a>

            <a class="nav-item nav-link"
              id="nav-outso-tab"
              data-toggle="tab"
              href="#outso"
              role="tab"
              aria-controls="outso"
              aria-selected="false">
                Outstanding SO
            </a>

        </div>
      </div>
    </div>

    <div class="card">

    <!-- <div class="card-header">
    <div class="d-flex justify-content-start">
        <div class="nav nav-tabs" id="nav-tab" role="tablist"> -->
     <!-- <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true" style="color: black;">Outstanding Packing - SPB</a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false" style="color: black;">Transaksi Spb</a> -->

            <!-- <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
              style="color: #007bff;  border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              OutStanding PR
            </a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
              style="color: #007bff;  border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              Purchase Order
            </a>
          </div>
      </div>

    </div> -->


      <!-- <div class="card-header" style="">
        <div class="row">
          <div class="nav nav-tabs col-12 justify-content-start" id="nav-tab" role="tablist" style="border-bottom: 0;">
            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
              style="color: #007bff;  border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              OutStanding PR
            </a>
            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
              style="color: #007bff;  border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              Purchase Order
            </a>
            <a class="nav-item nav-link" id="nav-profile1-tab" data-toggle="tab" href="#profile1" role="tab" aria-controls="nav-profile1" aria-selected="false"
              style="color: #007bff; background-color: #f8f9fa; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
              PO Otorisasi
            </a> -->
          <!-- </div>
        </div> -->
      <!-- </div>  -->

      <!-- <div class="card-header" style="padding-left: 0;">
    <div class="row no-gutters">
      <div class="nav col-12 justify-content-start" id="nav-tab" role="tablist" style="border-bottom: 0;">


        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="nav-home" aria-selected="true"
          style="color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px 0 0; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
          OutStanding PR
        </a>

        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"
          style="color: #007bff; border-radius: 20px; padding: 4px 12px; margin: 0 10px; font-weight: 600; font-size: 0.75rem; border: 2px solid #007bff; text-align: left;">
          Purchase Order
        </a>

      </div>
    </div>
  </div> -->

      <div class="card-body" style="padding:0;">
        <div class="tab-content" id="myTabContent" >

          <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <input type="search" id="poSearch1" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Nilai -1 = tampilkan semua data; angka itu
                         dikirim apa adanya ke podataoutstandingpr, yang memperlakukannya
                         sebagai "tanpa batas" - lihat POController@dataOutstandingPR. --}}
                    <div class="po-len-wrap">
                      <label for="poLen1">Tampilkan</label>
                      <select id="poLen1" class="po-len-inp">
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
                       (poPindahBar) saat tab berganti - lihat poInitReportTableSekali().
                       Sengaja di luar #tabel_wrapper supaya tidak ikut terhapus saat
                       DataTables menulis ulang toolbar-nya. --}}
                  <div id="rtBar"></div>
                  <table id="tabel" class="data-table">
                    <thead id="tabel_header" class="text-center ">
                      <tr>
                        {{-- Kolom Actions dimatikan - lihat PO_OUT[1].aksi di JS. initTabelOutstanding()
                             menulis ulang seluruh innerHTML thead ini, jadi markup di bawah hanya
                             placeholder sebelum JS jalan; dibiarkan sebagai arsip. --}}
                        {{-- <th style="padding: 4px 12px; " scope="col">Actions</th> --}}
                        <th style="padding: 4px 12px; " scope="col">No. Bukti</th>
                        <th style="padding: 4px 12px; " scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; " scope="col">Kode Barang</th>
                        <th style="padding: 4px 12px; " scope="col">Nama Barang</th>
                        <th style="padding: 4px 12px; " scope="col">Sat</th>
                        <th style="padding: 4px 12px; " scope="col">Qty</th>
                        <th style="padding: 4px 12px; " scope="col">Qty PO</th>
                        <th style="padding: 4px 12px; " scope="col">Sisa PR</th>
                        <th style="padding: 4px 12px; " scope="col">Keterangan</th>
                        <th style="padding: 4px 12px; " scope="col">Out. SO</th>
                        <th style="padding: 4px 12px; " scope="col">Qty Stock</th>

                      </tr>
                    </thead>
                    <tbody id="tabel_data" class="text-left">
                      {{-- @foreach ($tempOutstanding1 as $OutPR)
                      <tr>

                         <td class="text-center">
                            <button class="btn btn-success btn-sm" type="button" title="Details" onclick="buttonAdd('{{ $OutPR->Nobukti }}')">
                              <i class="bi bi-plus-lg"></i>
                            </button>
                        </td>
                        <td style=''>{{ $OutPR->Nobukti }}</td>
                        <td style=''>{!! date("d/m/Y", strtotime($OutPR->Tanggal)) !!}</td>
                        <td style=''>{{ $OutPR->kodebrg }}</td>
                        <td style=''>{{ $OutPR->NamaBrg }}</td>
                        <td style='' class='text-center'>{{ $OutPR->sat }}</td>
                        <td style='' class='text-right'>{{ number_format($OutPR->Qnt, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($OutPR->QNTPO, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($OutPR->SisaPPL, 2) }}</td>
                        <td style=''>{{ $OutPR->Keterangan }}</td>
                        <td style='' class='text-right'>{{ number_format($OutPR->QntoutSO, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($OutPR->QntStock, 2) }}</td>
                      </tr>
                      @endforeach --}}
                    </tbody>
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

          {{-- Tab "Outstanding SO". Isinya dibangun JS yang sama dengan tab Outstanding PR
               (lihat PO_OUT + initTabelOutstanding), jadi <thead>/<tbody> di sini memang
               sengaja dibiarkan kosong - keduanya diisi saat tabel digambar. --}}
          <div class="tab-pane fade" id="outso" role="tabpanel" aria-labelledby="outso-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <input type="search" id="poSearch3" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Nilai -1 = tampilkan semua data; angka itu
                         dikirim apa adanya ke podataoutstandingso, yang memperlakukannya
                         sebagai "tanpa batas" - lihat POController@dataOutstandingSO. --}}
                    <div class="po-len-wrap">
                      <label for="poLen3">Tampilkan</label>
                      <select id="poLen3" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                  </div>
                  {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat poPindahBar(). --}}
                  <table id="tabelso" class="data-table">
                    <thead id="tabelso_header" class="text-center "></thead>
                    <tbody id="tabelso_data" class="text-left"></tbody>
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

          <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab" >

            <div class="row" >
              <div class="col-md-12">
                <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                  <div class="po-toolbar">
                    <div class="po-filter-wrap">
                      <label>Periode</label>
                      <input type="date" class="po-filter-inp" id="poTglAwal" value="{!! $poTglAwal !!}">
                      <span class="po-filter-sep">s/d</span>
                      <input type="date" class="po-filter-inp" id="poTglAkhir" value="{!! $poTglAkhir !!}">
                    </div>
                    <input type="search" id="poSearch2" class="po-search-inp" placeholder="Cari data">
                    {{-- Jumlah baris per halaman. Sama seperti #poLen1/#poLen3 milik tab
                         Outstanding - lihat poIkatPanjangHalaman(). --}}
                    <div class="po-len-wrap">
                      <label for="poLen2">Tampilkan</label>
                      <select id="poLen2" class="po-len-inp">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="-1">Semua</option>
                      </select>
                    </div>
                    <button class="po-btn-filter" type="button" id="poBtnFilter" onclick="$('#modalFilterPO').modal('show')">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                    <div class="po-toolbar-act">
                      <button class="btn btn-chip-biru" onclick="buttonAdd()">Tambah</button>
                      <button class="btn btn-chip-biru" onclick="buttonAddPr()">Tambah PR</button>
                    </div>
                  </div>
                  {{-- #rtBar dipindahkan ke sini lewat JS saat tab ini aktif - lihat poPindahBar(). --}}
                  <table id="tabel2" class="data-table po-aksi-hover">
                    <thead id="tabel2_header" class="text-center ">
                        <tr>
                        <th style="padding: 4px 12px; " scope="col">Actions</th>
                        <th style="padding: 4px 12px; " scope="col">No Bukti</th>
                        <th style="padding: 4px 12px; " scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; " scope="col">Supplier</th>
                        <th style="padding: 4px 12px; " scope="col">Tanggal Kirim</th>
                        <th style="padding: 4px 12px; " scope="col">No. SO</th>
                        <th style="padding: 4px 12px; " scope="col">PO. Cust</th>
                        <th style="padding: 4px 12px; " scope="col">DPP Rp</th>
                        <th style="padding: 4px 12px; " scope="col">PPN Rp</th>
                        <th style="padding: 4px 12px; " scope="col">Grand Total Rp</th>
                        <th style="padding: 4px 12px; " scope="col">Oto1</th>
                        <th style="padding: 4px 12px; " scope="col">User Oto1</th>
                        <th style="padding: 4px 12px; " scope="col">Tgl Oto1</th>
                        @if ($level > 1)

                        <th style="padding: 4px 12px; " scope="col">Oto2</th>
                        <th style="padding: 4px 12px; " scope="col">User Oto2</th>
                        <th style="padding: 4px 12px; " scope="col">Tgl Oto2</th>
                        @if ($level > 2)

                        <th style="padding: 4px 12px; " scope="col">Oto3</th>
                        <th style="padding: 4px 12px; " scope="col">User Oto3</th>
                        <th style="padding: 4px 12px; " scope="col">Tgl Oto3</th>

                          @if ($level > 3)

                          <th style="padding: 4px 12px; " scope="col">Oto4</th>
                          <th style="padding: 4px 12px; " scope="col">User Oto4</th>
                          <th style="padding: 4px 12px; " scope="col">Tgl Oto4</th>

                            @if ($level > 4)

                            <th style="padding: 4px 12px; " scope="col">Oto5</th>
                            <th style="padding: 4px 12px; " scope="col">User Oto5</th>
                            <th style="padding: 4px 12px; " scope="col">Tgl Oto5</th>
                            @endif
                          @endif
                        @endif
                        @endif
                        <th style="padding: 4px 12px; " scope="col">Batal</th>
                        <th style="padding: 4px 12px; " scope="col">User Batal</th>
                        <th style="padding: 4px 12px; " scope="col">Tanggal Batal</th>
                        <th style="padding: 4px 12px; " scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody id="tabel2_data" class="text-left">
                      {{-- @foreach( $tempOutstanding3 as $PurchaseOrderData)
                      <tr>
                        <td class="text-center"style=''>
                            <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $PurchaseOrderData->NoBukti }}')">
                              <i class="bi bi-info"></i>
                            </button>
                            <button class="btn btn-success btn-sm" type="button" title="Edit" onclick="buttonEdit('{{ $PurchaseOrderData->NoBukti }}')">
                              <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" type="button" title="Otorisasi" onclick="buttonOtorisasi('{{ $PurchaseOrderData->NoBukti }}' , {{ $PurchaseOrderData->IsOtorisasi1 }})">
                              <i class="bi bi-key-fill"></i>
                            </button>
                        </td>
                        <td style=''>{{ $PurchaseOrderData->NoBukti }}</td>
                        <td style=''>{!! date("d/m/Y", strtotime($PurchaseOrderData->Tanggal)) !!}</td>
                        <td style=''>{{ $PurchaseOrderData->NamaCustSupp }}</td>
                        <td style=''>{!! date("d/m/Y", strtotime($PurchaseOrderData->tglKirim)) !!}</td>
                        <td style=''>{{ $PurchaseOrderData->NOSO }}</td>
                        <td style=''>{{ $PurchaseOrderData->NOPOCUST }}</td>
                        <td style='' class='text-right'>{{ number_format($PurchaseOrderData->TotDPPRp, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($PurchaseOrderData->TotSubTotalRp, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($PurchaseOrderData->TotPPNRp, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($PurchaseOrderData->TotNetRp, 2) }}</td>
                          <!-- @if($PurchaseOrderData->IsOtorisasi1 == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style=''>{{ $PurchaseOrderData->OtoUser1 }}</td>
                        <td style=''>
                          @if($PurchaseOrderData->TglOto1 === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($PurchaseOrderData->TglOto1)->format("d/m/Y H:i:s") }}
                          @endif
                        </td> -->
                        @if ($PurchaseOrderData->IsOtorisasi1 )
                          <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif
                        <td>{!! $PurchaseOrderData->TglOto1 ?  date("d/m/Y H:i:s", strtotime($PurchaseOrderData->TglOto1)) : '' !!}</td>

                        <td>{{ $PurchaseOrderData->OtoUser1 }}</td>
                        <td>{{ $PurchaseOrderData->OtoUser1 }}</td>
                        <td>{{ $PurchaseOrderData->OtoUser1 }}</td>
                        @if ($level > 1)
                        @if ($PurchaseOrderData->IsOtorisasi2 )
                        <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                        @else
                        <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif
                        <td>{!! $PurchaseOrderData->TglOto2 ? date("d/m/Y H:i:s", strtotime($PurchaseOrderData->TglOto2)) : '' !!}</td>

                        <td>{{ $PurchaseOrderData->OtoUser2 }}</td>
                        @if ($level > 2)
                        @if ($PurchaseOrderData->IsOtorisasi3 )
                        <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                        @else
                        <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif
                        <td>{!! $PurchaseOrderData->TglOto3 ? date("d/m/Y H:i:s", strtotime($PurchaseOrderData->TglOto3)) : '' !!}</td>

                        <td>{{ $PurchaseOrderData->OtoUser3 }}</td>
                        @if ($level > 3)
                        @if ($PurchaseOrderData->IsOtorisasi4 )
                        <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                        @else
                        <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif
                        <td>{!! $PurchaseOrderData->TglOto4 ? date("d/m/Y H:i:s", strtotime($PurchaseOrderData->TglOto4)) : '' !!}</td>

                        <td>{{ $PurchaseOrderData->OtoUser4 }}</td>
                        @if ($level > 4)
                        @if ($PurchaseOrderData->IsOtorisasi5 )
                        <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                        @else
                        <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif
                        <td>{!! $PurchaseOrderData->TglOto5 ? date("d/m/Y H:i:s", strtotime($PurchaseOrderData->TglOto5)) : '' !!}</td>

                        <td>{{ $PurchaseOrderData->OtoUser5 }}</td>

                        @endif
                        @endif
                        @endif
                        @endif

                          @if($PurchaseOrderData->Isbatal== 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style=''>{{ $PurchaseOrderData->UserBatal }}</td>
                        <td style=''>
                          @if($PurchaseOrderData->TglBatal === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($PurchaseOrderData->TglBatal)->format("d/m/Y - H:i:s") }}
                          @endif
                        </td>
                      </tr>
                      @endforeach --}}
                    </tbody>
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

          <div class="tab-pane fade" id="profile1" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
              <div class="col-12" style="overflow:auto;">
                <div class="container-fluid" style="padding:0; margin:0; width:100%;">

                  <table id="tabel3" class="table table-bordered table-hover table-striped table-responsive-lg">
                    <thead class="text-center bg-primary text-white">
                      <tr>
                        <th style="padding: 4px 12px; " scope="col">Actions</th>
                        <th style="padding: 4px 12px; " scope="col">No Bukti</th>
                        <th style="padding: 4px 12px; " scope="col">Tanggal</th>
                        <th style="padding: 4px 12px; " scope="col">Supplier</th>
                        <th style="padding: 4px 12px; " scope="col">Tanggal Kirim</th>
                        <th style="padding: 4px 12px; " scope="col">No. SO</th>
                        <th style="padding: 4px 12px; " scope="col">PO. Cust</th>
                        <th style="padding: 4px 12px; " scope="col">DPP Rp</th>
                        <th style="padding: 4px 12px; " scope="col">PPN Rp</th>
                        <th style="padding: 4px 12px; " scope="col">Grand Total Rp</th>
                        <th style="padding: 4px 12px; " scope="col">Authorized 1</th>
                        <th style="padding: 4px 12px; " scope="col">Authorized User</th>
                        <th style="padding: 4px 12px; " scope="col">Authorized Date 1</th>
                      </tr>
                    </thead>
                    <tbody id="tabel3_data" class="text-left">
                      {{-- @foreach ($tempOutstanding5 as $POOtorisasi)
                      <tr>
                        <td class="text-center">
                            <button class="btn btn-warning btn-sm" type="button" title="Details" onclick="buttonDetail('{{ $POOtorisasi->NoBukti }}')">
                              <i class="bi bi-info"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" type="button" title="Otorisasi" onclick="buttonBatalOtorisasi('{{ $POOtorisasi->NoBukti }}')">
                              <i class="bi bi-key-fill"></i>
                            </button>
                        </td>
                        <td style=''>{{ $POOtorisasi->NoBukti }}</td>
                        <td style=''>{!! date("d/m/Y", strtotime($POOtorisasi->Tanggal)) !!}</td>
                        <td style=''>{{ $POOtorisasi->NamaCustSupp }}</td>
                        <td style=''>{!! date("d/m/Y", strtotime($POOtorisasi->TglKirim)) !!}</td>
                        <td style=''>{{ $POOtorisasi->NOSO }}</td>
                        <td style=''>{{ $POOtorisasi->NOPOCUST }}</td>
                        <td style='' class='text-right'>{{ number_format($POOtorisasi->TotDPPRp, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($POOtorisasi->TotSubTotalRp, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($POOtorisasi->TotPPNRp, 2) }}</td>
                        <td style='' class='text-right'>{{ number_format($POOtorisasi->TotNetRp, 2) }}</td>
                        @if($POOtorisasi->IsOtorisasi1 == 1)
                          <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                        @else
                          <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                        @endif
                        <td style=''>{{ $POOtorisasi->OtoUser1 }}</td>
                        <td style=''>
                          @if($POOtorisasi->TglOto1 === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($POOtorisasi->TglOto1)->format('d/m/Y - H:i:s') }}
                          @endif
                        </td>
                          @if($POOtorisasi->Isbatal == 1)
                            <td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>
                          @else
                            <td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>
                          @endif
                        <td style=''>{{ $POOtorisasi->UserBatal }}</td>
                        <td style=''>
                          @if($POOtorisasi->TglBatal === null)
                            -
                          @else
                            {{ \Carbon\Carbon::parse($POOtorisasi->TglBatal)->format('d/m/Y - H:i:s') }}
                          @endif
                        </td>
                      </tr>
                      @endforeach --}}
                    </tbody>
                  </table>

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

          <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab">
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
          </div>

        </div>
      </div>

    </div>
  </div>

</div>

<div id="page2" class="container-fluid" style="display: none" >
<div id="formBsGrid">
  <div class="row">
    <div class="col-6 text-left">
      <!-- <h2 style="">Form Purchase Order</h2> -->
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
                <label>Supplier</label>
              </div>
            </div>

            <div class="col-md-8">
              <div class="input-group mb-3">
                <input type="text" class="form-control text-left" placeholder="Kode Supplier" id="input_add_kodesupplier">
                <button type="button" class="btn btn-chip-biru btn-sm" id="buttonAddListPelanggan" style="height:32px; border-radius:0;" onclick="performSearchSupplier()">
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
                <input type="text" class="form-control text-left" placeholder="Nama Supplier" id="input_add_namasupplier"  disabled>
              </div>
            </div>
            <div class="col-md-12" style="margin-top:-10px;">
              <div class="form-group">
                <textarea style="width: 100%; resize: none;" rows=3 placeholder="Alamat Supplier" class="form-control text-left align-items-left" id="input_add_alamatsupplier"  disabled></textarea>
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
                    <select class="form-control" id="input_add_valas" onchange="onChangeValas()"></select>
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
                    <option value=0 selected >Tunai</option>
                    <option value=1 >Kredit</option>
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
                        <label>Dikirim Ke</label>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <select class="form-control" id="input_add_kodealamatkirim" onchange="onChangeKodeAlamatKirim()">
                          <option value="GMPL">GMPL</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_alamatkirim"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Ekspedisi</label>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="input-group form-group">
                        <input class="form-control" id="input_add_kodeekspedisi" value ='-' readonly>
                        <button onclick="buttonAddListLokasiPenerima()" id="buttonAddListLokasiPenerima" value = '-' class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_ekspedisi"  disabled></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <label>Keterangan</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <textarea type="text" style="width: 100%; resize: none" rows=4  class="form-control" id="input_add_keterangan" onblur="onChangeCatatan()"></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="row">

                    {{-- Field No. SO dan No. PO Cust dipindah ke form Add Item
                         (lihat #addAddItem), id-nya tetap sama supaya semua
                         script lama tidak berubah. --}}

                    <div class="col-md-12">
                      <label>Tgl Kirim</label>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group" style="margin-top: 14px">
                        <input type="date" class="form-control text-left" id="input_add_tanggalkirim" value="{!! date('Y-m-d') !!}" onblur="onChangeTgglKirim()">
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
                            <button onclick="buttonAddListBackOffice()" id="buttonAddListBackOffice" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
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
                        <button onclick="buttonAddListPIC()" id="buttonAddListPIC" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
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
                    <button onclick="buttonAddListSales()" id="buttonAddListSales" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
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
            {{-- Pakai .data-table (didefinisikan di layout newmasterx) supaya tampilannya
                 persis sama dengan tabel di page1 (#tabel/#tabel2): tanpa garis kotak
                 per sel, hanya garis bawah tipis per baris. --}}
            <table id="tabel_add" class="data-table">
              <thead id='tabel_data_header' class="text-center">
                <tr>
                  <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                  <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Qty</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sat</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Harga</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Actions</th>
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
          <!-- <div class="col-md-11 mt-2 text-right">
            <button type="button" id='buttonTambahSOAll' class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="buttonTambahSOAll()" class="btn btn-secondary"><b>+ Tambah dari SO</b></button>
          </div> -->
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
          <hr/>

            <div class="row">
              <div class="col-12">
                <h4 id="h4AddAddItem">Tambah Item</h4>
                <h4 id="h4AddEditItem">Edit Item</h4>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12">

              {{-- Field tersembunyi (jasa, FOC, No. PNW PO, No/Urut PPL).
                   Urutannya sengaja tetap di atas: ada script yang memakai
                   document.getElementById("buttonAddAddListBarang") dan yang
                   pertama ketemu di DOM harus tetap tombol No. PNW PO ini. --}}
              <div class="row" hidden>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Jasa</label>
                    <select id="input_add_add_jasa" class="form-control" disabled>
                      <option value=0 selected>Tidak</option>
                      <option value=1>Iya</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group"> {{-- nama lama : nopenyerahan --}}
                    <label>FOC</label>
                    <select id="input_add_add_foc" onChange="LockFreeOfCharge()" class="form-control">
                      <option value=0>Tidak</option>
                      <option value=1>Iya</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>No. PNW PO</label>
                    <div class="input-group">
                      <input type="text" class="form-control" value="-" id="input_add_add_nopnwpo" readonly>
                      <div class="input-group-append">
                        <button onclick="buttonAddAddListPWO()" id="buttonAddAddListBarang" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_noPPL" tabindex="1"> {{-- nama lama : satuanproduk --}}
                    <input type="text" min="1" max="100" class="form-control" id="input_add_add_urutPPL" tabindex="1"> {{-- nama lama : satuanproduk --}}
                  </div>
                </div>
              </div>

              <div class="row">

                {{-- Kolom kiri : identitas barang --}}
                <div class="col-md-6">

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">+ Dari</label>
                    <div class="col-md-4">
                      <select id="input_add_add_outstanding" class="form-control" onchange="onChangeOutstanding()">
                        <option value="PR" selected>PR</option>
                        <option value="SO">SO</option>
                        <option value="FOC">FOC</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Kode Barang</label>
                    <div class="col-md-4">
                      <div class="input-group">
                        <input type="text" class="form-control text-left" id="input_add_add_kodebarang">
                        <button type="button" class="btn btn-chip-biru btn-sm" id="buttonBrowseBarangItem" style="height:32px; border-radius:0;" onclick="performSearch()" tabindex="1">
                          <i class="bi bi-search"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <input type="text" class="form-control text-left" id="input_add_add_namabarangasli" placeholder="Nama Barang" readonly>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Nama Barang</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" id="input_add_add_namabarang">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Note</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" id="input_add_add_keteranganbarang">
                    </div>
                  </div>

                  {{-- Label No. SO / No. PR ikut pilihan Outstanding di atas,
                       lihat onChangeOutstanding(). Field ini diisi otomatis
                       jadi dikunci (readonly + disabled). --}}
                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0" id="labelAddAddNoso">No. PR</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" id="input_add_noso" value="-" readonly disabled>
                    </div>
                  </div>

                </div>

                {{-- Kolom kanan : qty, harga, diskon --}}
                <div class="col-md-6">

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">QTY</label>
                    <div class="col-md-3">
                      <input type="text" id="input_add_add_qty" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onblur="cekQntStock()" tabindex="5">
                    </div>
                    <label class="col-md-3 mb-0">Satuan</label>
                    <div class="col-md-3">
                      <select id="input_add_add_nosat" class="form-control">
                        <option value=0 selected>Tidak</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Harga</label>
                    <div class="col-md-3">
                      <input type="text" id="input_add_add_harga" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onchange="onChangeInputAddAddHarga()" tabindex="6">
                    </div>
                    <label class="col-md-3 mb-0">Harga Awal</label>
                    <div class="col-md-3">
                      <input type="text" id="input_add_add_hargaAwal" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" tabindex="6">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Disc(%)</label>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen1" value=0 onChange='calculateDiscRp()' tabindex="8">
                    </div>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen2" value=0 onChange='calculateDiscRp()' tabindex="9">
                    </div>
                    <div class="col-md-3">
                      <input type="number" min="1" max="100" class="form-control text-right" id="input_add_add_discpersen3" value=0 onChange='calculateDiscRp()' tabindex="10">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">Disc RP</label>
                    <div class="col-md-3">
                      <input type="text" id="input_add_add_discrp" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" onchange="reverseCalculateDiscPercent()" tabindex="7">
                    </div>
                  </div>

                  <div class="form-row align-items-center mb-2">
                    <label class="col-md-3 mb-0">No. PO Cust</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" id="input_add_nopocust" value="-" readonly disabled>
                    </div>
                  </div>

                </div>

              </div>
            </div>

            <div class="col-md-12">
              <div id="divhargaterakhir">
                <div class="row">

                  <div class="col-12">
                    <div class="form-group">
                      <label>Harga Terakhir</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_harga_terakhir" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Supplier</th>
                            <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                            <th style="padding: 4px 12px;" scope="col">Qty</th>
                            <th style="padding: 4px 12px;" scope="col">Satuan</th>
                            <th style="padding: 4px 12px;" scope="col">Valas</th>
                            <th style="padding: 4px 12px;" scope="col">Kurs</th>
                            <th style="padding: 4px 12px;" scope="col">Harga</th>
                            <th style="padding: 4px 12px;" scope="col">Disc Rp</th>
                            <th style="padding: 4px 12px;" scope="col">Hrg. Nett</th>
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
                      <label>Stock Proyeksi</label>
                    </div>
                  </div>

                  <div class="col-md-12 mb-4" style="overflow:auto;">
                    <div class="container-fluid col-sm-12" style="padding:0; margin:0; width:100%;">
                      <table id="tabel_add_stock_proyeksi" class="data-table">
                        <thead class="text-center">
                          <tr>
                            <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
                            <th style="padding: 4px 12px;" scope="col">Stock</th>
                            <th style="padding: 4px 12px;" scope="col">Out PO</th>
                            <th style="padding: 4px 12px;" scope="col">Out SO</th>
                            <th style="padding: 4px 12px;" scope="col">S Marketing</th>
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
              onclick="showTableHargaTerakhir()">Histori Harga</button>

              <button type="button" class="btn btn-lg btn-stock-proyeksi" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="showTableStockProyeksi()">Stock Proyeksi</button>

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
              onclick="submitAddAdd()">Simpan</button>

              <button type="button" id="submitAddEdit" class="btn btn-primary btn-lg" style="
              height: 30px;
              padding: 4px 12px;
              border-radius: 20px;
              font-size: 0.75rem;
              font-weight: 600;
              text-transform: uppercase;
              transition: background-color 0.3s, box-shadow 0.3s;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
              onclick="submitAddEdit()" class="btn btn-secondary">Simpan</button>
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
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus" ></i></button>
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
              <button onclick=""  class="btn btn-primary btn-sm text-right" disabled><i class="bi bi-plus"></i></button>
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
              <button onclick="buttonAddEditListBarang()" id="buttonAddEditListBarang" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" disabled><i class="bi bi-search"></i></button>
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
              <select id="input_add_edit_nosat" onchange="onChangeInputAddAddNosat()" class="form-control form-select-lg mb-3" aria-label=".form-select-lg example" tabindex='2'>
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
              <button type="button" class="btn btn-secondary" onclick="closeShowHideAdd()" >Batal</button>
            </div>
          </div>


          <hr/>

          </div>


        <hr/>
    </div>

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
        <input type="number" class="form-control text-right" id="input_add_disc" onblur="onChangeInputAddDisc()" value="0.00">
      </div>
    </div>

    <!-- DiscRp -->
    <div class="col">
      <div class="form-group">
        <label>DiscRp</label>
        <input type="text" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number" id="input_add_discrp" onblur="onChangeInputAddDiscRp()" value="0.00">
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
        <button class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;" id="buttonAddListPelanggan" onclick="buttonAddListPelanggan()"><i class="bi bi-search"></i></button>
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
    <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
    <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
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
        <button onclick="buttonAddListPIC()" id="buttonAddListPIC" class="btn btn-chip-biru btn-sm" style="height:32px; border-radius:0;"><i class="bi bi-search"></i></button>
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
          <th style="padding: 4px 12px;" scope="col">NDPP</th>
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

<div id="page4" class="container-fluid" style="display: none" >
      <div class="row">
        <div class="col-6 text-left" style=''>
          <h2>Referensi PR</h2>
        </div>
        <div class="col-6 text-right" style=''>
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


  <!-- </div> -->
  <!-- <hr/> -->
  <div class="row ">
    <div class="col-md-12 text-right">
      <div class="row">
        <div class="col-md-12">

        </div>
      </div>

    <button type="button" class="btn btn-primary" onclick="submitAddPr()" class="btn btn-secondary"  >Save PR</button>
</div>
</div>
<hr/>


</div>
<div class=" container-fluid" id="" style="margin-top:-40px;">

  <!-- sinia -->

<!-- END ADD EDIT -->

<div class="container-fluid mt-4" style="overflow:auto;">
  <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
  <div class="row" style="overflow:auto;">
    <table id="tabel_refpr" class="table table-bordered table-hover table-striped table-responsive-lg">
      <thead class="text-center bg-primary text-white">
        <tr>
          <th style="padding: 4px 12px;" class="text-center" scope="col">v</th>
          <th style="padding: 4px 12px;" scope="col">Kode Barang</th>
          <th style="padding: 4px 12px;" scope="col">Nama Barang</th>
          <th style="padding: 4px 12px;" scope="col">Saldo Qty</th>
          <th style="padding: 4px 12px;" scope="col">Qty Out PO</th>
          <th style="padding: 4px 12px;" scope="col">Qty Out SO</th>
          <th style="padding: 4px 12px;" scope="col">Qty Min</th>
          <th style="padding: 4px 12px;" scope="col">Referensi PR</th>
          <th style="padding: 4px 12px;" scope="col">Final Qty PR</th>
          <th style="padding: 4px 12px;" scope="col">Merk</th>
          <th style="padding: 4px 12px;" scope="col">PartNumber</th>
          <!-- <th scope="col">Actions</th> -->

        </tr>
      </thead>

      <tbody id="tabel_data_refpr" class="text-left" >


      </tbody>

    </table>
  </div>
    <!-- <button onclick="buttonSubKategori()">tes</button> -->
</div>



</div>

<div class="row ">
  <div class="col-md-12 text-right">
    <div class="row">
      <div class="col-md-12">

      </div>
    </div>

  <button type="button" class="btn btn-primary" onclick="submitAddPr()" class="btn btn-secondary"  >Save PR</button>
</div>
</div>


</div>
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

<div class="modal fade"  id="formTambahSo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered"  role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Tambah Penawaran</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


        <!-- <h1>Tes Modal</h1> -->
        <div class="modal-body" >

      <!-- <div class="container-fluid"> -->
        <div class="row">

          <!-- <input type="hidden" class="form-control" id="input_detail_nourut" > -->
          <div class="col-md-6">

            <div class="row">


            <!-- <div class="col-md-12">
              <div class="form-group">
                <label></label>
              </div>
            </div> -->
            <!-- <div class="col-md-4" style="">
              <div class="form-group">
                <label>Kode Cust</label>
              </div>
            </div>
            <div class="col-md-2" style="">
              <div class="input-group mb-3 position-relative">
                <input
                type="text"
                class="form-control text-left"
                placeholder="Cari Pelanggan..."
                id="input_tambahsoall_kodepelanggan"
                onkeyup="searchPelangganTambahSOAll(this.value)"
                autocomplete="off">
                <div id="dropdown_pelanggantambahso"
                    class="dropdown-menu w-100">
                </div>
              </div>
            </div> -->
            <!-- <div class="col-md-6" style="margin-top:-40px;">
              <div class="form-group">
                <input type="text" class="form-control text-left" id="input_tambahsoall_namapelanggan" placeholder="" disabled> -->
                <input type="hidden" class="form-control text-left" id="input_tambahsoall_ppn" placeholder="" disabled>
              <!-- </div>
            </div> -->

            <!-- <div class="col-md-12">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div> -->
            <!-- <div class="col-md-4" style="margin-top:-12px;">
              <div class="form-group">
                <label>Tanggal</label>
              </div>
            </div>
            <div class="col-md-8 text-center" style="margin-top:-12px;">
              <div class="form-group">
                <input type="date" class="form-control text-center" id="input_tambahsoall_tanggal" value="{!! date('Y-m-d') !!}" >
              </div>
            </div> -->


            <!-- <div class="col-md-4" style="margin-top:-10px;">
              <div class="form-group">
                <label>No PO</label>
              </div>
            </div> -->


          <!-- <div class="col-md-8" style="margin-top:-10px;">
            <div class="input-group form-group">
              <input type="text" class="form-control text-left" id="input_tambahsoall_nopo" onkeyup="searchNoPOTambahSO(this.value)" >
              <input type="hidden" class="form-control text-left" id="input_tambahsoall_idpo"  >
            </div>
            <div id="dropdown_nopotambahsoall" class="dropdown-menu" style="width:100%"></div>
          </div> -->

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
          <button type="button" class="btn btn-primary" onclick="buttonAddMainHeader()" class="btn btn-secondary"  >Header</button>
          <button type="button" class="btn btn-primary" onclick="buttonAddMainItems()" class="btn btn-secondary"  >Items</button>
      </div>
      </div> -->
      <!-- <hr/> -->

      <div class=" container-fluid" id="" style="margin-top:-40px;">

        <!-- sinia -->

      <!-- END ADD EDIT -->

      <div class="container-fluid mt-4" style="overflow:auto;">
        <!-- <input type="hidden" name="noUrut" id="input_detail_noUrut" value="" /> -->
        <div class="row" style="overflow:auto; margin-top: 10px">
          <!-- <div class="row "> -->
          <div class="col-md-12 mt-2 text-right">
            <button type="button" id="submitAddTambahSOAll" class="btn btn-primary btn-lg" style="
            height: 30px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
            onclick="submitAddTambahSOAll()" class="btn btn-secondary">Submit Add</button>
          </div>
          <!-- </div> -->
          <div class="row" style="overflow:auto;">
          <table id="tabel_tambahsoall" class="table table-bordered table-hover table-striped table-responsive-lg">
            <thead class="text-center bg-primary text-white">
              <tr>
                <th style="padding: 4px 12px; " class="text-center" scope="col">v</th>
                <th style="padding: 4px 12px;" scope="col">No SO</th>
                <th style="padding: 4px 12px;" scope="col">Tanggal</th>
                <th style="padding: 4px 12px;" scope="col">No Po Cust</th>
                <th style="padding: 4px 12px;" scope="col">Kode Brg</th>
                <th style="padding: 4px 12px;" scope="col">Nama Brg</th>
                <th style="padding: 4px 12px;" scope="col">Qty</th>
                <th style="padding: 4px 12px;" scope="col">Satuan</th>
                <!-- <th scope="col">Actions</th> -->

              </tr>
            </thead>

            <tbody id="tabel_data_tambahsoall" class="text-left" >

              <tr >
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>

          </table>
        </div>
        </div>
          <!-- <button onclick="buttonSubKategori()">tes</button> -->
      </div>

      <div class="row ">
      <div class="col-md-12 mt-2 text-right">
        <button type="button" id="submitAddTambahSOAll" class="btn btn-primary btn-lg" style="
        height: 30px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        transition: background-color 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
        onclick="submitAddTambahSOAll()" class="btn btn-secondary">Submit Add</button>
      </div>
      </div>

      <hr/>
      </div>


      </div>



  </div>


</div>
</div>

<!-- page3 end input_add -->

<!-- modal filter status Purchase Order -->
<div class="modal fade rt-filter" id="modalFilterPO">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-funnel"></i>
          Filter Purchase Order
          <span class="rt-active-badge" id="poFilterBadge">0 aktif</span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#modalFilterPO').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="rt-section">
          <div class="rt-group-label">Penyaringan Data</div>
          <div class="rt-grid-2">
            <div>
              <label class="rt-field-label" for="poModalStatus">Status</label>
              <select class="rt-native" id="poModalStatus">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
                <option value="Sebagian">Sebagian</option>
                <option value="Batal">Batal</option>
              </select>
            </div>
            <div>
              <label class="rt-field-label" for="poModalOtorisasi">Otorisasi</label>
              <select class="rt-native" id="poModalOtorisasi">
                <option value="SEMUA">Semua</option>
                <option value="Sudah">Sudah</option>
                <option value="Belum">Belum</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="rt-reset-link" onclick="poResetFilter()">Reset semua</button>
        <div class="rt-footer-buttons">
          <button type="button" class="rt-btn rt-btn-ghost" data-dismiss="modal"
            onclick="$('#modalFilterPO').modal('hide')">Batal</button>
          <button type="button" class="rt-btn rt-btn-primary" onclick="poTerapkanFilter()">Terapkan</button>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- end modal filter status Purchase Order -->

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

{{--
  BACKUP - modal "Table Setting" lama (tombol panah naik/turun + checkbox "Tampil").
  Digantikan header tabel interaktif: seret judul kolom untuk mengurutkan, roda gigi
  untuk sembunyikan kolom / atur desimal, dan bar #rtBar1 / #rtBar2 di atas tabel
  untuk memunculkan kembali kolom dan me-reset susunannya.
  Disimpan sebagai comment untuk referensi, tidak dipakai lagi.

<div class="modal fade" id="formHeaderTable" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Table Setting</h5>
        <button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm ms-auto"
          data-dismiss="modal" aria-label="Close"
          style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
          <span aria-hidden="true" style="font-size: 1.2rem; font-weight: bold;">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="container-fluid mt-4">



          <div class="row">
            <div class="table-responsive">
            <table id="tabel_headertable" class="table table-bordered table-striped">
              <thead id="tabel_header_headertable" class="text-center bg-primary text-white">
                <tr>
                  <th scope="col">Actions</th>
                  <th scope="col">Kolom</th>
                  <th scope="col">Tampil</th>
                </tr>
              </thead>
              <tbody id="tabel_data_headertable" class="text-left">
                <tr>
                  <td class="text-center" colspan="3">Silakan ketik pencarian</td>
                </tr>
              </tbody>
            </table>
          </div>
          </div>



        </div>

        <div class="row ">
          <div class="col-md-12 text-right">
            <div class="row">
              <div class="col-md-12">

              </div>
            </div>

          <button type="button" class="btn btn-primary" onclick="saveHeaderTable()" class="btn btn-secondary"  >Save</button>
      </div>
      </div>
      </div>



    </div>
  </div>
</div>
--}}

@include('purchasing/modals/modalPOAdd')

@endsection

@section('js')
{{-- window.ReportTable: header tabel interaktif (drag kolom, menu roda gigi, bar kolom
     tersembunyi, tombol "Reset kolom"). Dimuat di halaman ini saja, bukan di layout
     purchasing/newmasterx, supaya halaman purchasing yang lain tidak ikut terpengaruh.
     File-nya berupa IIFE ber-guard, jadi aman meski dimuat lebih dari sekali. --}}
<script src="{!! URL::asset('js/report-table.js') !!}?v={{ @filemtime(base_path('public/js/report-table.js')) ?: '1' }}"></script>
<script src="{!! URL::asset('js/picker-kas.js') !!}?v={{ @filemtime(base_path('public/js/picker-kas.js')) ?: '1' }}"></script>
{{-- Modal pemilih bersama (purchasing/modals/modalPOAdd, #form) disamakan persis dengan modal
     pemilih menu Kas. Class picker-kas dipasang dari halaman ini (bukan di file modalnya) karena
     modalPOAdd juga di-include marketing/penawaranso, yang tidak ikut diubah. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  jQuery('#form').addClass('picker-kas')
  jQuery('#form .modal-footer .btn').filter(function () {
    return jQuery.trim(jQuery(this).text()) === 'Batal'
  }).addClass('picker-kas-batal')
})
</script>
<script type="text/javascript">

let urutHeaderTable = 0
let isoto1oto = 0
let dataTableAdd = []
let dataTableAddPr = []
let dataTableEdit = []
let dataCekHarga = []
let dataAddAddListItem = []
let tempDataTableTambahSO = []
let dataTambahSO = []

let dataRefreshOutstanding = []
let dataRefreshOutstanding2 = []

// Nilai Noso/NOPOCUST yang SEDANG BERLAKU di header dbpo untuk PO yang sedang dibuka di
// form. Header dbpo hanya punya satu kolom Noso/NOPOCUST untuk seluruh PO (bukan per
// baris dbpodet), padahal sp_PO menerima Noso/NOPOCUST sebagai parameter dan menulis
// ulang header itu SETIAP kali satu item disimpan. Tanpa variabel ini, menyimpan item PR
// atau FOC setelah item SO akan mengirim '-' dan menghapus No. SO yang sudah tersimpan.
//
// Jadi: item bersumber SO mengisi kedua variabel ini dari barang yang dipilih (lihat
// buttonAddAddPickBarangNonFOCPlus). Item PR/FOC TIDAK mengubahnya - submitAddAdd() dan
// submitAddEdit() mengirim balik nilai yang tersimpan di sini apa adanya, sehingga sp_PO
// menulis nilai yang sama persis dan header tidak berubah.
//
// Sengaja terpisah dari dataHeaderAdd (yang dipakai field header lain): dataHeaderAdd
// hanya diisi tiga fungsi refresh (buka PO lama) dan TIDAK PERNAH direset saat membuat PO
// baru, jadi membacanya langsung berisiko memakai sisa data PO lain yang sempat dibuka.
let poNosoHeader = '-'
let poNoPoCustHeader = '-'

let dataRefreshPenerimaan = []

let listAlamatKirim = []

let listValas = []

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


  jQuery(function($) {
    $('.input-partial-number').autoNumeric('init',
      {
        minimumValue : '0',
        // negativeSignCharacter: 'z'
      }
    );
  });

  /* BACKUP - penyimpan & pengurut kolom milik modal "Table Setting" lama.
     Digantikan window.doSimpanHeader + fallback bawaan report-table.js.
  function saveHeaderTable () {
    let href = window.location.pathname.split('/').filter(Boolean)[1];
    let _token = $("#_token").val();
    // console.log()

    console.log(JSON.stringify(xisshown))
    console.log(JSON.stringify(xheadertableheader))
    console.log(JSON.stringify(xheadertablevalue))
    console.log(JSON.stringify(xisnumeric))
    console.log(href)
    $.ajax({
      url: "{!! url('saveheadertable') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        header : JSON.stringify(xheadertableheader) ,
         isnumber : JSON.stringify(xisnumeric) ,
         tipe : '',
           value : JSON.stringify(xheadertablevalue) ,
            isshown : JSON.stringify(xisshown) ,

          href : href,
          urut: urutHeaderTable
      },
      success: function(res) {
        loadAll()
          $("#formHeaderTable").modal('toggle')

      }})


  }
  function buttonChangeOrder (type = 0, index =0) {
    console.log("buttonChangeOrder")
    console.log(type , index)

    // let xisshown = []
    // let xheadertableheader = []
    // let xheadertablevalue = []
    // let xisnumeric = []
    if (type == 0) {
      //naikkin posisi -1
      let tempisshown =  xisshown[index]
      let tempheadertableheader =  xheadertableheader[index]
      let tempheadertablevalue =  xheadertablevalue[index]
      let tempisnumeric =  xisnumeric[index]

      xisshown[index] = xisshown[index - 1]
      xheadertableheader[index] = xheadertableheader[index - 1]
      xheadertablevalue[index] = xheadertablevalue[index - 1]
      xisnumeric[index] = xisnumeric[index - 1]

      xisshown[index - 1] = tempisshown
      xheadertableheader[index - 1] = tempheadertableheader
      xheadertablevalue[index - 1] = tempheadertablevalue
      xisnumeric[index - 1] = tempisnumeric
      refreshHeaderTable()
    } else {
      // nurunin posisi +1
      let tempisshown =  xisshown[index]
      let tempheadertableheader =  xheadertableheader[index]
      let tempheadertablevalue =  xheadertablevalue[index]
      let tempisnumeric =  xisnumeric[index]

      xisshown[index] = xisshown[index + 1]
      xheadertableheader[index] = xheadertableheader[index + 1]
      xheadertablevalue[index] = xheadertablevalue[index + 1]
      xisnumeric[index] = xisnumeric[index + 1]

      xisshown[index + 1] = tempisshown
      xheadertableheader[index + 1] = tempheadertableheader
      xheadertablevalue[index + 1] = tempheadertablevalue
      xisnumeric[index + 1] = tempisnumeric
      refreshHeaderTable()

    }
  }
  END BACKUP */

  function onchangecheckboxtambahso (i) {
    console.log("onchangecheckboxtambahso" , i)
    if (document.getElementById(`add_checkboxAll${i}`).checked) {
      // tempDataTableTambahSO
      tempDataTableTambahSO.push(dataTambahSO[i])
    } else {
      // tempDataTableTambahSO

      const index = tempDataTableTambahSO.findIndex(item => item.NOBUKTI == dataTambahSO[i].NOBUKTI && item.URUT == dataTambahSO[i].URUT)
      tempDataTableTambahSO.splice(index,1)
    }
    console.log(tempDataTableTambahSO)

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

function buttonAddPr () {
  console.log('buttonAddPr')
  let _token = $("#_token").val();
  $.ajax({
    url: "{!! url('purchaseorderlistrefpr') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      console.log('resbuttonAddPr' , res)
      dataTableAddPr = res
      console.log('===========================')



      let rowTable = ""
      res.forEach((item, i) => {

        rowTable += `<tr>
        <td class="text-center"><input class="" type="checkbox" value="" id="add_checkbox${i}"></td>

        <td>${item.Kodebrg}</td>
        <td>${item.NamaBrg}</td>
        <td class="text-right">${formatAngkaX(item.SaldoQnt)}</td>
        <td class="text-right">${formatAngkaX(item.Qnt1OutPO)}</td>
        <td class="text-right">${formatAngkaX(item.Qnt1OutSo)}</td>
        <td class="text-right">${formatAngkaX(item.QntMin)}</td>
        <td class="text-right">${formatAngkaX(item.Qnt1Fiktif)}</td>
        <td class="text-right">${formatAngkaX(item.SaldoQnt)}</td>

        <td>${item.NamaMerk}</td>

        <td>${item.PartNumber}</td>
        </tr>`
      });
      document.getElementById("tabel_data_refpr").innerHTML = rowTable


      $('#page1').hide();
      $('#page4').show();
    }
  })

}

function submitAddPr () {
  let tempAddPr = []
  let _token = $("#_token").val();
  dataTableAddPr.forEach((item, i) => {
    if (document.getElementById(`add_checkbox${i}`).checked) {
      tempAddPr.push(dataTableAddPr[i])
    }
  });
  if (!tempAddPr.length) {
    alertify.warning("Tidak ada item dipilih");
    return
  }
  $.ajax({
    url: "{!! url('purchaseorderspaddpr') !!}",
    type: "post",
    async: false,
    data: {
      _token : _token,
      tempData: tempAddPr
    },
    success: function(res) {
      console.log('!' , res )

      buttonCloseForm()
      alertify.success(`PR dengan nobukti ${res} berhasil ditambah`);
      console.log('before ===========')
      loadAll()
      console.log("after ===========")
    }})



}

function buttonOtorisasi (nobukti , isoto1) {
  console.log(nobukti , isoto1)
  let akses = $("#akses_isotorisasi1").val();
  if (!Number(akses)) {
    alertify.warning('No access');
    return;
  }
  isoto1oto = isoto1

  // Update modal title and reset table
  $('#modalOtorisasiLabel').text('Detail Otorisasi PO: ' + nobukti);
  $('#otorisasi-table-body').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');

  // Show modal
  $('#modalOtorisasi').modal('show');

  // Fetch detail data
  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('pogetdetail') !!}",
    type: "POST",
    data: { _token, nobukti },
    success: function(res) {
      let dataTableAdd = res.list;
      dataCekHarga = res.list
      console.log('dataTableAdd ' , dataTableAdd)
      let rows = "";
      let totalTotal = 0;
      let saldoTotal = 0;

      dataTableAdd.forEach((item) => {
        rows += `<tr>
          <td>${item.KodeBrg}</td>
          <td>${item.NamaBrg}</td>
          <td class="text-right">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
          <td class="text-right">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
          <td class="text-right">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
          <td class="text-right">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
          <td class="text-right">${item.SaldoQnt ? formatAngka(parseFloat(item.SaldoQnt).toFixed(2)) : '0.00'}</td>
          <td class="text-right">${item.SaldoRP ? formatAngka(parseFloat(item.SaldoRP).toFixed(2)) : '0.00'}</td>
        </tr>`;

        totalTotal += item.Total;
        saldoTotal += item.SaldoRP;
      });

      rows += `<tr class="total-row">
        <td colspan="4"></td>
        <td class="text-right">Total:</td>
        <td class="text-right">${formatAngka(parseFloat(totalTotal).toFixed(2))}</td>
        <td class="text-right"></td>
        <td class="text-right">${formatAngka(parseFloat(saldoTotal).toFixed(2))}</td>
      </tr>`;

      $('#otorisasi-table-body').html(rows);
    },
    error: function(err) {
      $('#otorisasi-table-body').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
    }
  });

  // Handle confirm/otorisasi button   unbind first to avoid duplicate handlers
  $('#btn-confirm-otorisasi').off('click').on('click', function() {

      let _token = $("#_token").val();
      // nobukti = $("#input")
      console.log(nobukti)
      console.log(isoto1oto)
    console.log("SubmitOtorisasi ")
    console.log(dataCekHarga)
    let mssg = ''
    $.ajax({
      url: "{!! url('purchaseordercekhargaoto') !!}",
      type: "POST",
      data: { _token, tempData: dataCekHarga  },
      success: function(res) {
        console.log('rescekharga' ,res)

        for (var i = 0; i < res.length; i++) {
          console.log('1',i, mssg)

          console.log('a',i)
          let xtempx = 1;
          if (mssg) {
            mssg += ' , '
          }
          // if ()
          // res.forEach((item, i) => {

          if (res[i][0].Ket != 'lanjut') {
            mssg += `
              Barang ${res[i][0].kodebrg} - ${res[i][0].Ket}
            `
          }

          // });
          console.log(i, mssg)




          // alertify.confirm('Konfirmasi Otorisasi', 'Apakah yakin ingin menghapus item ' + String(i) + ' ?',
          //     function() {
          //       console.log('yes')
          //       let xtempx = 1;
          //     }
          //   ,function(){
          //     console.log('no')
          //       let xtempx = 0;
          //   };
          //   if (xtempx == 0) {
          //     break;
          //   })

        }


        console.log('mssg sini' , mssg)
        if (mssg) {
          console.log('mssg yes')
          alertify.confirm('Konfirmasi Otorisasi', mssg + '. Lanjut otorisasi ?',
              function() {
                console.log('yes')
                // return
                $.ajax({
                  url: "{!! url('poupdateotorisasi') !!}",
                  type: "POST",
                  data: { _token, nobukti , isoto1oto},
                  success: function(res) {
                    console.log('resupdoto')
                    if(res == 2) {
                      alertify.warning("Melebihi plafon")
                      return
                    }
                    if(res == 3) {
                      alertify.warning("Diperlukan otorisasi 1 terlebih dahulu")
                      return
                    }
                    console.log('Tesresmaxol' , res)
                    $('#modalOtorisasi').modal('hide');
                    alertify.success('Berhasil update otorisasi');
                    loadAll();
                  },
                  error: function(err) {
                    console.log(err);
                    alertify.warning('Terjadi kesalahan silahkan refresh browser');
                  }
                });
              }
            ,function(){
              console.log('no')
            });
            // if (xtempx == 0) {
            //   break;
            // })




        } else {
          console.log('else')
          // return
          console.log({ _token, nobukti })
          $.ajax({
            url: "{!! url('poupdateotorisasi') !!}",
            type: "POST",
            data: { _token, nobukti  , isoto1oto},
            success: function(res) {
              console.log('resupdoto')
              console.log(res)
              if(res == 2) {
                alertify.warning("Melebihi plafon")
                return
              }
              if(res == 3) {
                alertify.warning("Diperlukan otorisasi 1 terlebih dahulu")
                return
              }
              console.log('resoto',res)
              $('#modalOtorisasi').modal('hide');
              alertify.success('Berhasil update otorisasi');
              loadAll();
            },
            error: function(err) {
              console.log(err);
              alertify.warning('Terjadi kesalahan silahkan refresh browser');
            }
          });
        }

      },
      error: function(err) {
        console.log(err);
        alertify.warning('Terjadi kesalahan silahkan refresh browser');
      }
    });


    // return



  });
}

// function buttonOtorisasi (nobukti) {
//   console.log(nobukti)

//   let akses = $("#akses_isotorisasi1").val();
//   if (!Number(akses)) {
//     alertify.warning('No access')
//     return
//   }

//     let _token = $("#_token").val();

//   alertify.confirm('Otorisasi Otorisasi', 'Batal Otorisasi SO ' + nobukti + ' ?',
//       function() {
//         let _token = $("#_token").val();

//         $.ajax({
//           url: "{!! url('poupdateotorisasi') !!}",
//           type: "post",
//           async: false,
//           data: {
//             _token,
//             nobukti

//           },
//           success: function(res) {
//             alertify.success('Berhasil update otorisasi')
//             loadAll()

//           },
//           error: function (err) {
//             console.log(err)
//             alertify.warning('Terjadi kesalahan silahkan refresh browser')
//           }

//         })
//       }
//     ,function(){
//       console.log('no')
//     });

//   }


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
          $.abort();
        }
        let _token = $("#_token").val();

        $.ajax({
          url: "{!! url('poupdatebatalotorisasi') !!}",
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

function onChangeCatatan () {

  if (tipeform == 'edit') {
    let value  = $("#input_add_keterangan").val()
    onChangeHeader('Keterangan' , value)

  }

}
function onChangeNoPO () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_noso").val()
    onChangeHeader('NoPesanan' , value)
  }
}

function onChangeTgglKirim () {
  if (tipeform == 'edit') {
    let value  = $("#input_add_tanggalkirim").val()
    onChangeHeader('TglKirim' , value)
  }
}

function onChangeTipePPN () {
  console.log('onChangeTipePPN')
  if (tipeform == 'edit') {
    let value = $("#input_add_tipeppn").val()
    console.log(value)
    onChangeHeader('TipePPn' , value)
    onChangeHeader('PPN' , value)
    refreshUpdateHeader()
    let nobukti = $("#input_add_nobukti").val()
    refreshDataTableAdd(nobukti)
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

function onChangeInputAddDisc () {
    // document.getElementById("input_add_discrp").value = '0.00'
    console.log('onChangeDisc')
    if (tipeform == 'edit') {
      let value = $("#input_add_disc").val()
      console.log(value)
      onChangeHeader('DISC' , value)
      refreshUpdateHeader()
      let nobukti = $("#input_add_nobukti").val()
      refreshDataTableAdd(nobukti)
    }
}

function onChangeInputAddDiscRp () {
    // document.getElementById("input_add_disc").value = '0.00'
    console.log('onChangeDiscRp')
      if (tipeform == 'edit') {
        let value = formatAngkaVal($("#input_add_discrp").val())
        console.log(dataHeaderAdd)
        let x = Number(value) / Number(dataHeaderAdd.TOtSubtotalRP) * 100
        console.log(x)
        console.log(value)
        onChangeHeader('DISC' , x)
        refreshUpdateHeader()
        let nobukti = $("#input_add_nobukti").val()
        refreshDataTableAdd(nobukti)
      }
}

function refreshUpdateHeader ()
{
  let _token  = $("#_token").val()
  let nobukti = $("#input_add_nobukti").val()
  $.ajax({
    url: "{!! url('pospupdatepo') !!}",
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
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
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
    url: "{!! url('poonchangeheader') !!}",
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



function submitAddTambahSOAll () {

    console.log('submitAddTambahSO')
    console.log(tempDataTableTambahSO)
    if (!tempDataTableTambahSO.length) {
      alertify.warning("Tidak ada data dipilih")
      return

    }
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

    let Jmlrecord = 0
    if (dataTableAdd.length) {
      Jmlrecord = 1
    }
    TglJatuhTempo = formatDate(TglJatuhTempo)

    let _token  = $("#_token").val()
    let Choice = "I"
    let NoBukti = $("#input_add_nobukti").val()
    let NoUrut = $("#input_add_nourut").val()
    let Tanggal = $("#input_add_tanggal").val()
    let KodeSupp = $("#input_add_kodesupplier").val()
    //handling kosong
    let KodeExp = $("#input_add_kodeekspedisi").val()
    let Keterangan = $("#input_add_keterangan").val()
    //faktursupp kosong
    let KodeVls = $("#input_add_valas").val()
    let Kurs = $("#input_add_kurs").val()
    let PPn = $("#input_add_tipeppn").val()
    let TipeBayar = $("#input_add_pembayaran").val()
    let Hari = $("#input_add_hari").val()
    //TipeDisc kosong
    //Disc = 0
    //discrp
    let Urut = 0
    let KodeBrg =  $("#input_add_add_kodebarang").val()
    let NoSat =  $("#input_add_add_nosat").val()
    //satuan
    //isi teko dbbarang

    let Harga = (Number(($("#input_add_add_harga").val() || '0').replace(/,/g, '')))
    let DiscTot =(Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, '')))
    let HrgAwal = (Number(($("#input_add_add_hargaAwal").val() || '0').replace(/,/g, '')))
    let Qnt =  (Number(($("#input_add_add_qty").val() || '0').replace(/,/g, '')))
    let DiscP = $("#input_add_add_discpersen1").val()
    let NoPPL = $("#input_add_add_noPPL").val()
    //isclose kosong
    //isCloseD kosong
    //catatan kosong
    //IsExp = false
    //Tolerate kosong
    let UrutPPL = $("#input_add_add_urutPPL").val()
    let Kodegdg = $("#input_add_kodealamatkirim").val()
    let Discpdet2 = $("#input_add_add_discpersen2").val()
    let Discpdet3 = $("#input_add_add_discpersen3").val()
    //discpdet4 kosong
    //discpdet5 kosong
    //flagtipe 1
    let NamaBrg =  $("#input_add_add_namabarang").val()
    //isjasa = 0
    //pFirst = 0
    let pFOC = $("#input_add_add_foc").val()
    // let Noso = $("#input_add_noso").val()
    //jmlrecord no bukti duplikat
    // let NOPOCUST = $("#input_add_nopocust").val()
    //iduser = $user->name
    //pJasa = 0
    //npph23 0
    //perkiraan
    //satX
    //cost
    //subcost
    let TglKirim = $("#input_add_tanggalkirim").val()
    //pph21
    let NOPNw = $("#input_add_add_nopnwpo").val()
    let UrutPNW = 0
    let KeteranganBarang = $("#input_add_add_keteranganbarang").val()
    if (Number(Hari) < 0 )  {
      alertify.warning("Angka negatif")
      return
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
      //discrp,
      // Urut,
      KodeBrg,
      Qnt,
      NoSat,
      // Isi,
      // Harga,
      // DiscP,
      // DiscTot,
      // NoPPL,
      // IsClose,
      // IsCloseD,
      // Catatan,
      // IsExp,
      // Tolerate,
      // UrutPPL,
      Kodegdg,
      // Discpdet2,
      // Discpdet3,
      // Discpdet4,
      // Discpdet5,
      // FlagTipe,
      NamaBrg,
      // IsJasa,
      // pFirst,
      Jmlrecord,
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
      // HrgAwal,
      // KeteranganBarang

    })
    console.log( Kodegdg , NoBukti , KodeSupp, Keterangan)
    if ( !Kodegdg || !NoBukti || !KodeSupp || !Keterangan ) {
      alertify.warning("Data belum lengkap")
      return
    }


    $.ajax({
              url: "{!! url('pospaddtambahso') !!}",
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
                //discrp,
                // Urut,
                KodeBrg,
                Qnt,
                NoSat,
                // Isi,
                // Harga,
                // DiscP,
                // DiscTot,
                // NoPPL,
                // IsClose,
                // IsCloseD,
                // Catatan,
                // IsExp,
                // Tolerate,
                // UrutPPL,
                Kodegdg,
                // Discpdet2,
                // Discpdet3,
                // Discpdet4,
                // Discpdet5,
                // FlagTipe,
                NamaBrg,
                // IsJasa,
                // pFirst,
                Jmlrecord,
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
                // HrgAwal,
                // KeteranganBarang
                tempData: tempDataTableTambahSO
              },
              success: function(res) {
                console.log("resss")
                console.log(res)
                if (res == 1) {

                  loadAll()
                  tipeform = 'edit'
                  document.getElementById("buttonAddListPelanggan").disabled = true
                  document.getElementById("input_add_kodesupplier").disabled = true
                  $('#divhargaterakhir').hide();
                  $('#divStockProyeksi').hide();
                  cleanFormAddAdd()

                  refreshDataTableAdd(NoBukti)

                  $("#form").modal('toggle')
                  alertify.success('Berhasil menambah item')
                  poSegarkanOutstanding()
                }
                if(res == 2) {
                  setNewNoBukti()
                  alertify.warning('Nobukti telah direfresh silahkan submit ulang')
                }

              },
              error: function (err) {
                console.log(err)
                alertify.warning('Terjadi kesalahan silahkan refresh browser')
              }

            })

}




function submitAddAdd () {

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

    let Jmlrecord = 0
    if (dataTableAdd.length) {
      Jmlrecord = 1
    }

    let _token  = $("#_token").val()
    let Choice = "I"
    let NoBukti = $("#input_add_nobukti").val()
    let NoUrut = $("#input_add_nourut").val()
    let Tanggal = $("#input_add_tanggal").val()
    let KodeSupp = $("#input_add_kodesupplier").val()
    //handling kosong
    let KodeExp = $("#input_add_kodeekspedisi").val()
    let Keterangan = $("#input_add_keterangan").val()
    //faktursupp kosong
    let KodeVls = $("#input_add_valas").val()
    let Kurs = $("#input_add_kurs").val()
    let PPn = $("#input_add_tipeppn").val()
    let TipeBayar = $("#input_add_pembayaran").val()
    let Hari = $("#input_add_hari").val()
    //TipeDisc kosong
    //Disc = 0
    //discrp
    let Urut = 0
    let KodeBrg =  $("#input_add_add_kodebarang").val()
    let NoSat =  $("#input_add_add_nosat").val()
    //satuan
    //isi teko dbbarang

    let Harga = (Number(($("#input_add_add_harga").val() || '0').replace(/,/g, '')))
    let DiscTot =(Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, '')))
    let HrgAwal = (Number(($("#input_add_add_hargaAwal").val() || '0').replace(/,/g, '')))
    let Qnt =  (Number(($("#input_add_add_qty").val() || '0').replace(/,/g, '')))
    let DiscP = $("#input_add_add_discpersen1").val()
    let NoPPL = $("#input_add_add_noPPL").val()
    //isclose kosong
    //isCloseD kosong
    //catatan kosong
    //IsExp = false
    //Tolerate kosong
    let UrutPPL = $("#input_add_add_urutPPL").val()
    let Kodegdg = $("#input_add_kodealamatkirim").val()
    let Discpdet2 = $("#input_add_add_discpersen2").val()
    let Discpdet3 = $("#input_add_add_discpersen3").val()
    //discpdet4 kosong
    //discpdet5 kosong
    //flagtipe 1
    let NamaBrg =  $("#input_add_add_namabarang").val()
    //isjasa = 0
    //pFirst = 0
    let pFOC = $("#input_add_add_foc").val()
    // Noso/NOPOCUST di dbpo adalah kolom HEADER (satu untuk seluruh PO), sedangkan asal
    // barang per baris cukup NoPPL + UrutPPL. Item SO mengisi header dari field form;
    // item PR/FOC mengirim balik poNosoHeader/poNoPoCustHeader apa adanya (nilai header
    // yang sedang berlaku) supaya sp_PO menulis nilai yang sama dan header tidak berubah -
    // lihat catatan lengkap di deklarasi poNosoHeader.
    let Noso = poSumberBarang() === 'SO' ? $("#input_add_noso").val() : poNosoHeader
    //jmlrecord no bukti duplikat
    let NOPOCUST = poSumberBarang() === 'SO' ? $("#input_add_nopocust").val() : poNoPoCustHeader
    //iduser = $user->name
    //pJasa = 0
    //npph23 0
    //perkiraan
    //satX
    //cost
    //subcost
    let TglKirim = $("#input_add_tanggalkirim").val()
    //pph21
    let NOPNw = $("#input_add_add_nopnwpo").val()
    let UrutPNW = 0
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

    console.log(tempAddAdd)

    let Satuan = ''
    let qnt1 = 0
    let Isi = 0


    // Barang dari PR memakai satuan bawaan baris PR-nya (lihat pilihan satuan tunggal di
    // buttonAddAddPickBarangNonFOC), sedangkan SO dan FOC memakai satuan master barang.
    if (poSumberBarang() === 'PR') {
      console.log(tempAddAdd)
      Isi = tempAddAdd.Isi
      console.log(tempAddAdd.Isi)
      Satuan = tempAddAdd.Sat

    } else {
      if (NoSat == 1) {
        qnt1 = Qnt * tempSatuanBarang[0].ISI1
        Satuan = tempSatuanBarang[0].SAT1
        Isi = tempSatuanBarang[0].ISI1
      }
      if (NoSat == 2) {
        qnt1 = Qnt * tempSatuanBarang[0].ISI2
        Satuan = tempSatuanBarang[0].SAT2
        Isi = tempSatuanBarang[0].ISI2
      }
      if (NoSat == 3) {
        qnt1 = Qnt * tempSatuanBarang[0].ISI3
        Satuan = tempSatuanBarang[0].SAT3
        Isi = tempSatuanBarang[0].ISI3
      }
    }




    // let pppn = 0
    // if (tempAddAdd.pPPN) {
    //   pppn = 1
    // }

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
      //discrp
      // Urut, // UNDEFINED
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
    if (Number(Hari) < 0 || Number(Qnt) < 0 || Number(Harga) < 0 || Number(DiscTot) < 0)  {
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
        console.log('spadd a')
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
                      //discrp,
                      // Urut,
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

                      if (res == 1) {

                        loadAll()
                        tipeform = 'edit'
                        document.getElementById("buttonAddListPelanggan").disabled = true
                        document.getElementById("input_add_kodesupplier").disabled = true
                        $('#divhargaterakhir').hide();
                        $('#divStockProyeksi').hide();
                        cleanFormAddAdd()

                        refreshDataTableAdd(NoBukti)

                        alertify.success('Berhasil menambah item')
                        poSegarkanOutstanding()
                      }
                      if(res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
                      }

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




          }




          // sesuai range
          else{
            console.log('sp add b')

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
                      //discrp,
                      // Urut,
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
                      console.log("resss")
                      console.log(res)
                      if (res == 1) {

                        loadAll()
                        tipeform = 'edit'
                        document.getElementById("buttonAddListPelanggan").disabled = true
                        document.getElementById("input_add_kodesupplier").disabled = true
                        $('#divhargaterakhir').hide();
                        $('#divStockProyeksi').hide();
                        cleanFormAddAdd()

                        refreshDataTableAdd(NoBukti)

                        alertify.success('Berhasil menambah item')
                        poSegarkanOutstanding()
                      }
                      if(res == 2) {
                        setNewNoBukti()
                        alertify.warning('Nobukti telah direfresh silahkan submit ulang')
                      }

                    },
                    error: function (err) {
                      console.log(err)
                      alertify.warning('Terjadi kesalahan silahkan refresh browser')
                    }

                  })



              }




        }

  }

 );













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
  //handling kosong
  let KodeExp = $("#input_add_kodeekspedisi").val()
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
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang

  let Harga = (Number(($("#input_add_add_harga").val() || '0').replace(/,/g, '')))
  let DiscTot = (Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, '')))
  let HrgAwal = (Number(($("#input_add_add_hargaAwal").val() || '0').replace(/,/g, '')))
  let Qnt = (Number(($("#input_add_add_qty").val() || '0').replace(/,/g, '')))

  let DiscP = $("#input_add_add_discpersen1").val()
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  let UrutPPL = $("#input_add_add_urutPPL").val()
  let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = $("#input_add_add_discpersen2").val()
  let Discpdet3 = $("#input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  // Lihat catatan yang sama di submitAddAdd() soal poNosoHeader/poNoPoCustHeader.
  let Noso = poSumberBarang() === 'SO' ? $("#input_add_noso").val() : poNosoHeader
  //jmlrecord no bukti duplikat
  let NOPOCUST = poSumberBarang() === 'SO' ? $("#input_add_nopocust").val() : poNoPoCustHeader
  //iduser = $user->name
  //pJasa = 0
  //npph23 0
  //perkiraan
  //satX
  //cost
  //subcost
  let TglKirim = $("#input_add_tanggalkirim").val()
  //pph21
  let NOPNw = $("#input_add_add_nopnwpo").val()
  let UrutPNW = 0
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
    Satuan = tempAddEdit.SAT1
    Isi = tempAddEdit.ISI1
  }
  if (NoSat == 2) {
    Isi = tempAddEdit.ISI2
    Satuan = tempAddEdit.SAT2
  }
  if (NoSat == 3) {
    Isi = tempAddEdit.ISI3
    Satuan = tempAddEdit.SAT3
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
                                                              poSegarkanOutstanding()

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
                                                              poSegarkanOutstanding()

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

    onChangeHeader('TIPEBAYAR' , check)
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

    document.getElementById("input_add_add_discrp").value = '0.00'
    return
  }

  let disc = $("#input_add_add_disc").val();
  let discRp = Number(harga) * Number(disc) / 100
  document.getElementById("input_add_add_discrp").value = formatAngka(parseFloat(discRp).toFixed(2))

}

function onChangeInputAddAddHarga () {
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_discpersen1").value = '0.00'
  document.getElementById("input_add_add_discpersen2").value = '0.00'
  document.getElementById("input_add_add_discpersen3").value = '0.00'

  // Harga Awal hanya ikut Harga saat item BARU ditambahkan. Saat mengoreksi item yang
  // sudah tersimpan, Harga Awal adalah harga asli waktu item itu dibuat - kalau ikut
  // tertimpa, riwayat harga aslinya hilang.
  if (tipeformitem !== 'edit') {
    document.getElementById("input_add_add_hargaAwal").value = document.getElementById("input_add_add_harga").value
  }
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

  let discRp = $("#input_add_add_discrp").val();
  let disc = Number(discRp) / Number(harga) * 100
  document.getElementById("input_add_add_disc").value = formatAngka(parseFloat(disc).toFixed(2))
}

function buttonAddAddItem () {
  tipeformitem = 'add'
  $('.showhide').hide();

  $('#divhargaterakhir').hide();
  $('#divStockProyeksi').hide();

  cleanFormAddAdd()
  poKunciIdentitasBarang(false)
  poKunciHargaAwal()
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
  poKunciIdentitasBarang(true)
  let _token = $("#_token").val();
  $('.showhide').hide();
  // cleanFormAddAdd()
  console.log(dataTableAdd[i])
  tempAddEdit = dataTableAdd[i]
  console.log(tempAddEdit)

  console.log(typeof tempAddEdit.Harga);
  console.log(tempAddEdit.Harga + " ini harga")

  // let selectOption = ''
  // if (tempAddEdit.Satuan) {
  //   selectOption += `<option value=1 selected>${tempAddEdit.Satuan}</option>`
  // }

  let selectOption = ''
  if (tempAddEdit.SAT1) {
    selectOption += `<option value=1 selected>1-${tempAddEdit.SAT1}(${tempAddEdit.ISI1})</option>`
  }
  if (tempAddEdit.SAT2) {
    selectOption += `<option value=2>2-${tempAddEdit.SAT2}(${tempAddEdit.ISI2})</option>`
  }
  if (tempAddEdit.SAT3) {
    selectOption += `<option value=3>3-${tempAddEdit.SAT3}(${tempAddEdit.ISI3})</option>`
  }

  if (tempAddEdit.NoPNW == ''){
    tempAddEdit.NoPNW = '-'
  }

  document.getElementById("input_add_add_jasa").value = tempAddEdit.Isjasa
  document.getElementById("input_add_add_foc").value = tempAddEdit.PFOC
  syncOutstandingDariFOC(tempAddEdit)
  document.getElementById("input_add_add_nopnwpo").value = tempAddEdit.NoPNW
  document.getElementById("input_add_add_kodebarang").value = tempAddEdit.KodeBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_namabarang").value = tempAddEdit.NamaBrg
  document.getElementById("input_add_add_discpersen1").value = Number(tempAddEdit.DiscP1) ?  tempAddEdit.DiscP1 : '0.00'
  document.getElementById("input_add_add_discpersen2").value = Number(tempAddEdit.Discp2) ?  tempAddEdit.Discp2 : '0.00'
  document.getElementById("input_add_add_discpersen3").value = Number(tempAddEdit.Discp3) ?  tempAddEdit.Discp3 : '0.00'
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddEdit.Qnt).toFixed(2))
  document.getElementById("input_add_add_nosat").innerHTML = selectOption
  document.getElementById("input_add_add_nosat").value = tempAddEdit.nosat

  document.getElementById("input_add_add_harga").value = Number(tempAddEdit.Harga) ? formatAngka(parseFloat(tempAddEdit.Harga).toFixed(2)) : '0.00'
  document.getElementById("input_add_add_discrp").value = Number(tempAddEdit.DISCTOT) ? formatAngka(parseFloat(tempAddEdit.DISCTOT).toFixed(2)) : '0.00'
  document.getElementById("input_add_add_noPPL").value = tempAddEdit.NoPPL
  document.getElementById("input_add_add_urutPPL").value = tempAddEdit.UrutPPL
  document.getElementById("input_add_add_keteranganbarang").value = tempAddEdit.KeteranganBarang
  document.getElementById("input_add_add_hargaAwal").value = Number(tempAddEdit.Hrgawal) ? formatAngka(parseFloat(tempAddEdit.Hrgawal).toFixed(2)) : '0.00'
  poKunciHargaAwal()

  // No. PR/No. SO di layar = asal barang milik ITEM ini (NoPPL-nya sendiri), bukan lagi
  // nilai header. No. PO Cust ikut ditampilkan hanya kalau item ini memang yang mengisi
  // No. SO di header (poItemDariSO) - tempAddEdit.Nopesanan berasal dari join ke header,
  // jadi tidak berarti apa-apa untuk item yang bersumber dari PR.
  document.getElementById("input_add_noso").value = tempAddEdit.NoPPL ? tempAddEdit.NoPPL : '-'
  document.getElementById("input_add_nopocust").value = poItemDariSO(tempAddEdit)
    ? (tempAddEdit.Nopesanan ? tempAddEdit.Nopesanan : '-')
    : '-'

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
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      document.getElementById("input_add_add_kodebarang").scrollIntoView();

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
      kode:'PO',
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
      kode:'PON',
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

  if ($.fn.DataTable.isDataTable('#tabel_add_list_pic')) { $('#tabel_add_list_pic').DataTable().destroy() }

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
          <td>${formatAngka(item.qty)}</td>
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

// Sumber daftar barang saat browsing, ditentukan dropdown "+ Dari"
// (#input_add_add_outstanding):
//
//   PR  -> barang PR yang masih outstanding (vwOutPPL), seluruh PR sekaligus
//   SO  -> barang SO yang masih outstanding (DBSODET), seluruh SO sekaligus
//   FOC -> master barang (Dbbarang)
//
// Sebelumnya sumbernya ditebak dari kombinasi field FOC tersembunyi + apakah No. SO
// masih '-'. Itu sisa alur lama: No. SO dipilih lebih dulu di header lewat tombol browse
// tersendiri (buttonAddListNoSO), sehingga "No. SO sudah terisi" bisa dipakai sebagai
// penanda bahwa barangnya berasal dari SO. Tombol itu sudah tidak ada lagi di form dan
// sekarang dropdown inilah yang menentukan, jadi tebakan tersebut tidak dipakai lagi.
function poSumberBarang () {
  let el = document.getElementById('input_add_add_outstanding')
  return el ? el.value : 'PR'
}

// Satu baris hasil pogetdetail membawa NoPPL (asal barang, kolom dbpodet) sekaligus NOSO
// (nomor SO yang tersimpan di HEADER PO-nya, kolom dbpo). Keduanya sama berarti item ini
// memang yang mengisi No. SO di header; kalau beda (atau NoPPL kosong), item ini
// dianggap berasal dari PR/FOC - lihat catatan di deklarasi poNosoHeader soal kenapa
// header hanya bisa mengikuti satu item SO.
function poItemDariSO (item) {
  let noPPL = (item.NoPPL === null || item.NoPPL === undefined) ? '' : String(item.NoPPL).trim()
  let noSo = (item.NOSO === null || item.NOSO === undefined) ? '' : String(item.NOSO).trim()
  return noPPL !== '' && noPPL === noSo
}

// Identitas barang (dropdown "+ Dari", kode barang, tombol browse, nama barang) hanya
// boleh dipilih/diganti saat MENAMBAH item baru. Saat MENGEDIT item yang sudah tersimpan,
// keempatnya dikunci - baris yang sudah ada tidak boleh diganti jadi barang lain.
//
// Ini juga menutup risiko yang lebih dalam: submitAddEdit() menentukan pengiriman
// Noso/NOPOCUST dari nilai dropdown ini (lihat catatan di deklarasi poNosoHeader), jadi
// dropdown yang bisa diubah saat edit berpotensi mengubah header PO hanya karena item
// lama dibuka lalu disimpan ulang tanpa qty/harga benar-benar berubah.
//
// input_add_add_namabarangasli tidak ikut disebut di sini karena sudah readonly sejak
// awal. Qty, harga, diskon, satuan, dan note SENGAJA tidak dikunci - itu memang bagian
// yang boleh dikoreksi lewat edit.
function poKunciIdentitasBarang (kunci) {
  document.getElementById('input_add_add_outstanding').disabled = kunci
  document.getElementById('input_add_add_kodebarang').disabled = kunci
  document.getElementById('buttonBrowseBarangItem').disabled = kunci
  document.getElementById('input_add_add_namabarang').disabled = kunci
}

// Harga Awal hanya boleh diisi saat menambah item baru; saat mengedit item yang sudah
// tersimpan field ini dikunci supaya harga asli item tidak bisa diubah sama sekali,
// baik otomatis (lihat onChangeInputAddAddHarga) maupun diketik manual.
function poKunciHargaAwal () {
  document.getElementById('input_add_add_hargaAwal').disabled = (tipeformitem === 'edit')
}

// Cari indeks baris di dataAddAddListItem yang cocok dengan keyword yang diketik user di
// kolom kode barang. Dua tahap: kode barang PERSIS dulu, baru substring kode/nama -
// supaya kode lengkap yang kebetulan jadi bagian nama barang lain tidak membatalkan
// auto-pick. Nama field dikirim dari pemanggil karena sumber FOC memakai 'Kodebrg'
// (huruf b kecil, dari master Dbbarang) sedangkan PR/SO memakai 'KodeBrg'.
function poCariIndeksBarang (keyword, fieldKode, fieldNama) {
  let k = String(keyword).trim().toLowerCase()
  if (!k) { return [] }

  let ambil = (item, f) => String((item[f] === null || item[f] === undefined) ? '' : item[f]).toLowerCase()
  let persis = []
  let mirip = []

  dataAddAddListItem.forEach((item, i) => {
    if (ambil(item, fieldKode) === k) { persis.push(i) }
    if (ambil(item, fieldKode).includes(k) || ambil(item, fieldNama).includes(k)) { mirip.push(i) }
  })

  return persis.length ? persis : mirip
}

// Dipakai setelah auto-pick (hasil pencarian persis satu barang): form item sudah terisi
// otomatis, jadi kursor langsung dilempar ke Qty - mengikuti pola buttonAddAddInsertItem
// di pembelianpermintaanagen/nonagen. Diberi jeda karena fungsi pick diakhiri
// scrollIntoView().
function poFokusQty () {
  setTimeout(() => {
    let el = document.getElementById("input_add_add_qty")
    if (el && !el.disabled) { el.focus(); el.select() }
  }, 300)
}

// opsi.keyword  : kata kunci dari kolom kode barang (kode maupun nama barang)
// opsi.autoPick : kalau hasil pencarian PERSIS satu baris, barang itu langsung dipilih
//                 dan modal tidak usah dibuka sama sekali. Hanya diaktifkan oleh Enter,
//                 bukan oleh tombol browse - menekan tombol browse berarti user memang
//                 ingin melihat daftarnya.
// Default {} membuat pemanggil lama berperilaku persis seperti sebelumnya.
function buttonAddAddListBarang (opsi = {}) {

  let _token = $("#_token").val();
  let sumber = poSumberBarang()
  let keyword = opsi.keyword ? String(opsi.keyword).trim() : ''
  let autoPick = !!opsi.autoPick

  // Kotak pencarian FOC hanya relevan untuk sumber FOC - direset di sini supaya sumber
  // lain (PR/SO/Barang All) selalu mulai dari kondisi tersembunyi.
  $('#wrapSearchBarangFOC').hide();

  // PR : seluruh PR yang masih outstanding.
  //
  // Dulu di sini ada percabangan lagi - kalau form dibuka lewat tombol + di tab
  // Outstanding PR (noBuktiUntukAdd terisi), daftarnya disaring hanya untuk PR itu saja
  // lewat polistbarangnosominus. Percabangan itu dibuang: penambahan item sekarang
  // seluruhnya dilakukan dari form Purchase Order, sedangkan tab Outstanding PR hanya
  // menjadi tampilan informasi.
  if (sumber === 'PR') {

    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_nonfoc')) { $('#tabel_add_list_barang_nonfoc').DataTable().destroy() }

    $.ajax({
      url: "{!! url('polistbarangnosominusallso') !!}",
      type: "post",
      async: false,
      data: {
        _token
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res

        // Hasil pencarian persis satu baris PR -> langsung dipilih, modal tidak dibuka.
        // Kalau satu kode barang punya beberapa baris PR (beda No. PR / beda satuan),
        // cocok.length > 1 sehingga modal tetap dibuka supaya user memilih sendiri.
        if (autoPick && keyword) {
          let cocok = poCariIndeksBarang(keyword, 'KodeBrg', 'NamaBrg')
          if (cocok.length === 1) {
            buttonAddAddPickBarangNonFOC(cocok[0])
            poFokusQty()
            return
          }
        }

        dataAddAddListItem.forEach((item, i) => {
          // Dikosongkan (bukan 0) kalau nilainya null/undefined, supaya tidak terbaca
          // sebagai "tidak ada yang dibatalkan" padahal datanya memang tidak ada.
          let qntBatal = (item.QntBatalPO === undefined || item.QntBatalPO === null) ? '' : item.QntBatalPO
          rowTable += `
          <tr class="pick-row" onclick="buttonAddAddPickBarangNonFOC(${i})">
            <td style="">${item.KodeBrg}</td>
            <td style="">${item.NamaBrg}</td>
            <td style="">${item.PartNumber}</td>
            <td style="">${item.NAMAMERK ? item.NAMAMERK : ''}</td>
            <td style="">${item.Sat}</td>
            <td style="">${formatAngka(item.Qnt)}</td>
            <td style="">${formatAngka(item.QntPO)}</td>
            <td style="">${formatAngka(qntBatal)}</td>
            <td style="">${item.SisaPPL}</td>
            <td style="">${item.NoBukti}</td>
            <td style="">${item.NosoCust ? item.NosoCust : ''}</td>
          </tr>`
        });

        if(!res.length) {
          rowTable= ``
        }
        document.getElementById("tabel_data_add_list_barang_nonfoc").innerHTML = rowTable
        document.getElementById("namaHeaderTable").textContent = 'Barang dari PR'
        pickerKasInit('tabel_add_list_barang_nonfoc')

        // Kata kunci yang diketik user ikut terbawa ke kotak cari bawaan DataTables,
        // supaya jelas daftar yang tampil ini hasil pencarian apa dan bisa langsung diubah.
        if (keyword) { $("#tabel_add_list_barang_nonfoc").DataTable().search(keyword).draw() }

        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOC').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })

  } else if (sumber === 'FOC') {

    // Browse FOC diambil dari seluruh master barang - kalau langsung dimuat semua saat modal
    // dibuka, ini lemot. Jadi modal dibuka kosong dan baru cari ke server saat user tekan
    // Enter (searchBarangFOC), mengikuti pola di pembelianpermintaanagen/nonagen.
    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_foc')) { $('#tabel_add_list_barang_foc').DataTable().destroy() }

    if (keyword) {

      // Kata kunci dari kolom kode barang dibawa ke kotak cari modal, lalu dicarikan ke
      // server sekarang juga (poMuatBarangFOC async:false) supaya jumlah hasilnya bisa
      // dipakai memutuskan auto-pick sebelum modal sempat dibuka.
      // $('#input_search_barang_foc').val(keyword)  - kata kuncinya kini tampil di kotak Search DataTables

      let jml = poMuatBarangFOC(keyword)

      if (autoPick && jml === 1) {
        buttonAddAddPickBarangFOCPlus(0)
        poFokusQty()
        return
      }

      poRenderBarangFOC(keyword)

    } else {

      // $('#input_search_barang_foc').val('')
      // document.getElementById("tabel_data_add_list_barang_foc").innerHTML = '<tr><td class="text-center" colspan="4">Silakan ketik pencarian</td></tr>'
      poTabelBarangFOCInit('', '')

    }

    document.getElementById("namaHeaderTable").textContent = 'Master Barang'
    $('.showhidemodalbodyadd').hide();
    // Kotak cari lama (#wrapSearchBarangFOC) tidak dimunculkan lagi - diganti kotak Search
    // DataTables (picker-kas), lihat poTabelBarangFOCInit().
    // $('#wrapSearchBarangFOC').show();
    $('#modalBodyAddAddListBarangFOC').show();

    $("#form").modal('toggle')

  } else {


    // SO : seluruh SO yang masih outstanding sekaligus. Endpoint tersendiri, BUKAN
    // polistbarangnosoplus, karena yang itu menyaring per satu nomor SO (peninggalan alur
    // dua langkah) dan masih dipakai halaman lain - lihat POController@listBarangSOAll.
    if ($.fn.DataTable.isDataTable('#tabel_add_list_barang_nonfocplus')) { $('#tabel_add_list_barang_nonfocplus').DataTable().destroy() }

    $.ajax({
      url: "{!! url('polistbarangsoall') !!}",
      type: "get",
      async: false,
      data: {
      },
      success: function(res) {
        let rowTable = ``
        dataAddAddListItem = res

        // Sama seperti cabang PR: auto-pick hanya kalau hasilnya benar-benar satu baris SO.
        if (autoPick && keyword) {
          let cocok = poCariIndeksBarang(keyword, 'KodeBrg', 'NamaBrg')
          if (cocok.length === 1) {
            buttonAddAddPickBarangNonFOCPlus(cocok[0])
            poFokusQty()
            return
          }
        }

        dataAddAddListItem.forEach((item, i) => {
          rowTable += `
          <tr class="pick-row" onclick="buttonAddAddPickBarangNonFOCPlus(${i})">
            <td>${item.KodeBrg}</td>
            <td style="">${item.NamaBrg}</td>
            <td>${formatAngka(item.Qnt)}</td>
            <td>${formatAngka(item.Qnt2)}</td>
            <td>${item.Sat}</td>
            <td>${item.SisaPPL}</td>
            <td>${item.Sisa2PPL}</td>
            <td>${item.NoBukti}</td>
            <td>${item.PartNumber}</td>

          </tr>`
        });

        document.getElementById("tabel_data_add_list_barang_nonfocplus").innerHTML = rowTable

        pickerKasInit('tabel_add_list_barang_nonfocplus')

        if (keyword) { $("#tabel_add_list_barang_nonfocplus").DataTable().search(keyword).draw() }

        document.getElementById("namaHeaderTable").textContent = 'Barang dari SO'
        $('.showhidemodalbodyadd').hide();
        $('#modalBodyAddAddListBarangNonFOCPlus').show();

        $("#form").modal('toggle')

      },
      error: function (err) {
        console.log(err)
        alertify.warning('Terjadi kesalahan silahkan refresh browser')
      }

    })
  }
}



function buttonTambahSOAll () {
  tempDataTableTambahSO = []
  console.log("buttonTambahSOAll")
  let _token = $("#_token").val();
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

  let Jmlrecord = 0
  if (dataTableAdd.length) {
    Jmlrecord = 1
  }

  // let _token  = $("#_token").val()
  let Choice = "I"
  let NoBukti = $("#input_add_nobukti").val()
  let NoUrut = $("#input_add_nourut").val()
  let Tanggal = $("#input_add_tanggal").val()
  let KodeSupp = $("#input_add_kodesupplier").val()
  //handling kosong
  let KodeExp = $("#input_add_kodeekspedisi").val()
  let Keterangan = $("#input_add_keterangan").val()
  //faktursupp kosong
  let KodeVls = $("#input_add_valas").val()
  let Kurs = $("#input_add_kurs").val()
  let PPn = $("#input_add_tipeppn").val()
  let TipeBayar = $("#input_add_pembayaran").val()
  let Hari = $("#input_add_hari").val()
  //TipeDisc kosong
  //Disc = 0
  //discrp
  let Urut = 0
  let KodeBrg =  $("#input_add_add_kodebarang").val()
  let NoSat =  $("#input_add_add_nosat").val()
  //satuan
  //isi teko dbbarang

  let Harga = (Number(($("#input_add_add_harga").val() || '0').replace(/,/g, '')))
  let DiscTot =(Number(($("#input_add_add_discrp").val() || '0').replace(/,/g, '')))
  let HrgAwal = (Number(($("#input_add_add_hargaAwal").val() || '0').replace(/,/g, '')))
  let Qnt =  (Number(($("#input_add_add_qty").val() || '0').replace(/,/g, '')))
  let DiscP = $("#input_add_add_discpersen1").val()
  let NoPPL = $("#input_add_add_noPPL").val()
  //isclose kosong
  //isCloseD kosong
  //catatan kosong
  //IsExp = false
  //Tolerate kosong
  let UrutPPL = $("#input_add_add_urutPPL").val()
  let Kodegdg = $("#input_add_kodealamatkirim").val()
  let Discpdet2 = $("#input_add_add_discpersen2").val()
  let Discpdet3 = $("#input_add_add_discpersen3").val()
  //discpdet4 kosong
  //discpdet5 kosong
  //flagtipe 1
  let NamaBrg =  $("#input_add_add_namabarang").val()
  //isjasa = 0
  //pFirst = 0
  let pFOC = $("#input_add_add_foc").val()
  // let Noso = $("#input_add_noso").val()
  //jmlrecord no bukti duplikat
  // let NOPOCUST = $("#input_add_nopocust").val()
  //iduser = $user->name
  //pJasa = 0
  //npph23 0
  //perkiraan
  //satX
  //cost
  //subcost
  let TglKirim = $("#input_add_tanggalkirim").val()
  //pph21
  let NOPNw = $("#input_add_add_nopnwpo").val()
  let UrutPNW = 0
  let KeteranganBarang = $("#input_add_add_keteranganbarang").val()
  if (Number(Hari) < 0 )  {
    alertify.warning("Angka negatif")
    return
  }
  // checkpoint
  if ( !Kodegdg || !NoBukti || !KodeSupp || !Keterangan ) {
    alertify.warning("Data belum lengkap")
    return
  }
  console.log( Kodegdg , NoBukti , KodeSupp)
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
    //discrp,
    // Urut,
    KodeBrg,
    Qnt,
    NoSat,
    // Satuan,
    // Isi,
    // Harga,
    // DiscP,
    // DiscTot,
    // NoPPL,
    // IsClose,
    // IsCloseD,
    // Catatan,
    // IsExp,
    // Tolerate,
    // UrutPPL,
    Kodegdg,
    // Discpdet2,
    // Discpdet3,
    // Discpdet4,
    // Discpdet5,
    // FlagTipe,
    NamaBrg,
    // IsJasa,
    // pFirst,
    Jmlrecord,
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
    // HrgAwal,
    // KeteranganBarang

  })

  if ( !Kodegdg || !NoBukti || !KodeSupp) {
    alertify.warning("Data belum lengkap")
    return
  }
  // Tabelnya #tabel_tambahsoall (di modal #formTambahSo), BUKAN #tabel_add_list_noSo:
  // baris di bawah membangun 8 sel (checkbox + 7 kolom) dan hanya thead
  // #tabel_tambahsoall yang punya 8 kolom. Sebelumnya fungsi ini menulis ke
  // #tabel_data_add_list_noSo yang cuma 3 kolom, sehingga barisnya melebihi header.
  if ($.fn.DataTable.isDataTable('#tabel_tambahsoall')) {
    if ($.fn.DataTable.isDataTable('#tabel_tambahsoall')) { $('#tabel_tambahsoall').DataTable().destroy() }
  }
  $.ajax({
    url: "{!! url('polistnoso') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `
        `

      dataTambahSO = res

      dataTambahSO.forEach((item, i) => {
        rowTable += `
        <tr>
          <td class="text-center"><input class="" type="checkbox" value="" id="add_checkboxAll${i}" onchange="onchangecheckboxtambahso(${i})"></td>
          <td>${item.NOBUKTI}</td>
          <td>${formatDate(item.Tanggal)}</td>
          <td>${item.NoPesanan}</td>
          <td>${item.KODEBRG}</td>
          <td>${item.NAMABRG}</td>
          <td class="text-right">${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          </tr>`
      });

      document.getElementById("tabel_data_tambahsoall").innerHTML = rowTable
      $("#tabel_tambahsoall").DataTable({
        "lengthChange": false,
          "paging": false ,
          "order": [[1, 'asc']],
          "columnDefs": [
               {"targets" :[0] , 'orderable' : false}
            ]
        });
      $("#formTambahSo").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function buttonAddListNoSO () {

  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_noSo')) { $('#tabel_add_list_noSo').DataTable().destroy() }
  $.ajax({
    url: "{!! url('polistnoso') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `
        <tr class="pick-row" onclick="buttonAddPickNoSO('-' , '-')">
          <td>-</td>
          <td>-</td>
          <td>-</td>
          </tr>`

      listNoSo = res

      listNoSo.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickNoSO('${item.NOBUKTI}' , '${item.NoPesanan}')">
          <td>${item.NOBUKTI}</td>
          <td>${item.Tanggal}</td>
          <td>${item.NoPesanan}</td></tr>`
      });

      document.getElementById("tabel_data_add_list_noSo").innerHTML = rowTable
      pickerKasInit('tabel_add_list_noSo')
      document.getElementById("namaHeaderTable").textContent = 'Nomor SO'
      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListNoSo').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function muatDropdownAlamatKirim () {

  let _token = $("#_token").val();

  $.ajax({
    url: "{!! url('polistgudang') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      listAlamatKirim = res

      let selectEl = document.getElementById("input_add_kodealamatkirim")
      let kodeTerpilih = selectEl.value

      selectEl.innerHTML = ''
      listAlamatKirim.forEach((item) => {
        let opt = document.createElement('option')
        opt.value = item.KODEGDG
        opt.textContent = `${item.KODEGDG} - ${item.NAMA}`
        selectEl.appendChild(opt)
      });

      if (listAlamatKirim.some(item => item.KODEGDG === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function onChangeKodeAlamatKirim () {

  let kode = document.getElementById("input_add_kodealamatkirim").value
  let itemX = listAlamatKirim.find(item => item.KODEGDG === kode)
  let alamat = itemX ? itemX.Alamat : ''

  document.getElementById("input_add_alamatkirim").value = alamat

  if (tipeform == 'edit') {
    onChangeHeader('NoAlamatKirim' , kode)
    onChangeHeader('AlamatKirim' , alamat)
  }

}


function buttonAddListLokasiPenerima () {

  let _token = $("#_token").val();

  if ($.fn.DataTable.isDataTable('#tabel_add_list_lokasipenerima')) { $('#tabel_add_list_lokasipenerima').DataTable().destroy() }

  $.ajax({
    url: "{!! url('polistlokasipenerima') !!}",
    type: "post",
    async: false,
    data: {
      _token
    },
    success: function(res) {
      let rowTable = `<tr class="pick-row" onclick="buttonAddPickLokasiPenerima('-' , '-' )">
        <td>-</td>
        <td>-</td>
        </tr>`
      res.forEach((item, i) => {
        rowTable += `
        <tr class="pick-row" onclick="buttonAddPickLokasiPenerima('${item.KodeCustsupp}' , '${item.NamaCust}' )">
        <td>${item.Kota}</td>
        <td>${item.NamaCust}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_list_lokasipenerima").innerHTML = rowTable
      pickerKasInit('tabel_add_list_lokasipenerima')

      document.getElementById("namaHeaderTable").textContent = 'Ekspedisi'

      $('.showhidemodalbodyadd').hide();
      $('#modalBodyAddListLokasiPenerima').show();

      $("#form").modal('toggle')

    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })


}

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
      });

      if (listValas.some(item => item.kodevls === kodeTerpilih)) {
        selectEl.value = kodeTerpilih
      }
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }

  })

}

function onChangeValas () {

  let kode = document.getElementById("input_add_valas").value
  let itemX = listValas.find(item => item.kodevls === kode)
  let kurs = itemX && itemX.kurs ? parseFloat(itemX.kurs).toFixed(2) : '0.00'

  document.getElementById("input_add_kurs").value = kurs

  if (tipeform == 'edit') {
    onChangeHeader('KODEVLS' , kode)
    onChangeHeader('KURS' , kurs)
  }

}

function buttonAddListPelanggan ()
{
  if ($.fn.DataTable.isDataTable('#tabel_add_list_pelanggan')) { $('#tabel_add_list_pelanggan').DataTable().destroy() }

  $.ajax({
    url: "{!! url('polistpelanggan') !!}",
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
  if ($.fn.DataTable.isDataTable('#tabel_add_list_backoffice')) { $('#tabel_add_list_backoffice').DataTable().destroy() }
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
          document.getElementById("input_add_add_harga").value = res[0].Xharga
        } else {
          console.log('else1')
          if (tempAddAdd[`Hrg${nosat}_1`]) {
            console.log('if2')
            document.getElementById("input_add_add_harga").value = tempAddAdd[`Hrg${nosat}_1`]
          } else {
            console.log('else2')
            document.getElementById("input_add_add_harga").value = '0.00'
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

// ================= Outstanding PR & Purchase Order =================
// Tabel "Outstanding PR" memakai server-side paging: data diambil per halaman lewat
// podataoutstandingpr, bukan lagi menarik seluruh isi vwOutPPL sekaligus.
// Tabel "Purchase Order" di-load lazy, yaitu saat tabnya pertama kali dibuka.

let poTab2Sudahdimuat = false

// Konfigurasi kolom mentah dari poloadall. Hanya dipakai sebagai bibit untuk poCart;
// setelah itu yang jadi acuan render adalah poCart.
let laheadertable = []
let laheadertablevalue = []
let laisnumeric = []
let laisshown = []
let ladesimal = []
let laaliasordered = []
let laheadertable2 = []
let laheadertablevalue2 = []
let laisnumeric2 = []
let laisshown2 = []
let ladesimal2 = []
let laaliasordered2 = []

/* ============ Header tabel interaktif (window.ReportTable) ============
 *
 * report-table.js bekerja di atas satu array global `gcart_header` berisi kolom
 * dengan 6 elemen: [field, label, tampil, tipe, total, desimal].
 * Halaman ini punya DUA tabel dengan konfigurasinya masing-masing (urut 1 =
 * Outstanding PR, urut 2 = Purchase Order), tersimpan di DBHEADERTABLE lewat
 * endpoint saveheadertable/getheadertable yang sudah ada.
 *
 * poCart[urut] menyimpan array itu untuk tiap tabel, dan window.gcart_header
 * selalu diarahkan ke cart milik tabel yang sedang aktif.
 *
 * Indeks 6, 7, 8 dipakai untuk menitipkan header/value/isnumeric aslinya.
 * report-table.js tidak pernah menyentuh indeks di atas 5, dan karena drag &
 * sembunyikan memindahkan seluruh baris array, ketiga nilai titipan itu ikut
 * berpindah - jadi pemetaan balik ke payload simpan selalu tepat.
 */

let poCart = { 1 : [], 2 : [], 3 : [] }
let poActiveUrut = 0

// Tabel yang tabnya sedang tidak aktif tetap punya data lama tertinggal setelah
// loadAll() (mis. sehabis simpan/edit/otorisasi). Ditandai di sini, baru benar-benar
// digambar ulang saat tabnya dibuka - lihat handler shown.bs.tab di bawah.
let poPerluGambar = { 1 : false, 2 : false, 3 : false }

/* ---- Dua tabel "Outstanding" ----
 * Tab "Outstanding PR" (urut 1) dan "Outstanding SO" (urut 3) cara kerjanya sama
 * persis: server-side paging, judul kolom interaktif, kotak search + dropdown
 * "Tampilkan" sendiri, tanpa filter periode. Yang berbeda hanya id elemen dan
 * endpoint datanya, jadi keduanya memakai SATU fungsi yang sama
 * (initTabelOutstanding) dengan tabel di bawah ini sebagai pembedanya - dengan
 * begitu perbaikan pada salah satu tab otomatis berlaku untuk keduanya.
 *
 * urut 2 = tab "Purchase Order" tidak ikut di sini: datanya ditarik sekaligus lalu
 * digambar renderTabelPO(), bukan per halaman dari server.
 */
const PO_OUT = {
  1 : {
    tabel  : 'tabel',
    thead  : 'tabel_header',
    tbody  : 'tabel_data',
    search : 'poSearch1',
    len    : 'poLen1',
    url    : "{!! url('podataoutstandingpr') !!}",
    nama   : 'Outstanding PR',
    // Kolom "Actions" (tombol + / buttonAdd) sudah tidak dipakai - seluruh penambahan
    // item PO sekarang lewat form Purchase Order, lihat memory
    // tab-outstanding-pr-hanya-informasi. Kedua tab outstanding jadi tampilan
    // informasi saja, tanpa kolom aksi.
    aksi   : false
  },
  3 : {
    tabel  : 'tabelso',
    thead  : 'tabelso_header',
    tbody  : 'tabelso_data',
    search : 'poSearch3',
    len    : 'poLen3',
    url    : "{!! url('podataoutstandingso') !!}",
    nama   : 'Outstanding SO',
    aksi   : false
  }
}

// Cache respons terakhir tiap tabel outstanding, dipakai supaya menggeser/menyembunyikan
// kolom tidak perlu menembak server lagi - lihat initTabelOutstanding().
let poCacheOut = { 1 : null, 3 : null }
let poPakaiCacheOut = { 1 : false, 3 : false }

// Jumlah baris per halaman tiap tabel, dikendalikan dropdown #poLen1/#poLen2/#poLen3.
// Disimpan di variabel, bukan hanya dibaca dari elemen select-nya, karena
// initTabelOutstanding()/renderTabelPO() melakukan destroy+init tiap kali kolom
// digeser/disembunyikan - tanpa ini tabel selalu balik ke nilai awal walau
// dropdownnya masih menunjuk pilihan pengguna. Nilai -1 berarti "semua data"
// (dipahami DataTables maupun servernya). urut 2 (tab Purchase Order) paging-nya
// murni di client (data sudah ditarik sekaligus), bukan lewat server seperti 1/3.
let poPanjangHalaman = { 1 : 10, 2 : 10, 3 : 10 }

// Nomor urut tabel milik tab yang sedang aktif.
function poUrutTabAktif () {
  if ($('#nav-profile-tab').hasClass('active')) { return 2 }
  if ($('#nav-outso-tab').hasClass('active')) { return 3 }
  return 1
}

// href sengaja dipatok, bukan diambil dari window.location, karena POController@loadAll
// juga memakai string yang sama saat MEMBACA konfigurasi. Kalau keduanya beda,
// pengaturan yang disimpan tidak akan pernah terbaca lagi.
const PO_HREF = 'purchaseorder'
const PO_TIPE_NAMA = { 0 : 'varchar', 1 : 'float', 2 : 'date' }

// Selektor dinamis: report-table.js menjalankan document.querySelector(cfg.table) setiap
// kali dipakai (tidak menyimpan elemennya), jadi selektor ini otomatis menunjuk tabel di
// tab yang sedang aktif tanpa perlu init() ulang tiap kali tab berpindah.
const PO_SELEKTOR_TABEL_AKTIF = '#myTabContent .tab-pane.active table.data-table'

function poBuatCart (headers, values, isnumerics, isshowns, desimals, aliasordered) {
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
      tipe === 0 ? h : values[i],        // 0 nama field di data
      label,                             // 1 judul kolom
      Number(isshowns[i]) === 1 ? 1 : 0, // 2 tampil
      PO_TIPE_NAMA[tipe] || 'varchar',   // 3 tipe data
      0,                                 // 4 total (item roda giginya disembunyikan lewat CSS)
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
function poKolomTampil (urut) {
  return (poCart[urut] || []).filter(c => Number(c[2]) === 1)
}

function poKolomRender (c) {
  return { field : c[0], label : c[1], tipe : Number(c[8]), desimal : Number(c[5]) }
}

// Arahkan komponen ke tabel tertentu. report-table.js menyimpan konfigurasinya di
// satu variabel modul, jadi hanya satu tabel yang bisa dilayani pada satu waktu -
// dan itu cukup, karena kedua tabel ada di tab yang saling eksklusif.
function poAktifkanTabel (urut) {
  poActiveUrut = urut
  window.g_modeReport = urut
  window.gcart_header = poCart[urut]
}

// Dipanggil report-table.js sendiri (lewat cfg.onChange) tiap kali kolom digeser/
// disembunyikan/diubah desimalnya. poActiveUrut selalu benar karena tab switch
// memanggil poAktifkanTabel() sebelum tabel manapun bisa diinteraksi.
function poOnChangeAktif () {
  if (poActiveUrut === 2) {
    renderTabelPO()
  } else {
    // true = pakai cache respons terakhir tabel itu, jangan menembak server -
    // perubahan kolom tidak mengubah datanya, hanya cara menampilkannya.
    initTabelOutstanding(poActiveUrut, true)
  }
}

// Kalau public/js/report-table.js belum ikut terunggah, halaman harus tetap tampil:
// judul kolomnya jatuh ke <th> biasa, hanya tanpa drag & roda gigi.
function poHeadHtml (cols) {
  if (typeof ReportTable !== 'undefined' && ReportTable.headHtml) {
    return ReportTable.headHtml(cols)
  }
  let html = '<tr>'
  cols.forEach((c) => {
    html += `<th style="padding: 4px 12px;" scope="col">${c[1]}</th>`
  });
  return html + '</tr>'
}

// Ikat handler drag & roda gigi ke ELEMEN <thead> TEPAT SEKALI seumur halaman.
// report-table.js tidak punya teardown, dan handler roda giginya berbunyi
// "if (openGidx === g) { closeMenu(); return }" - kalau <thead> yang sama terikat
// dua kali, klik roda gigi membuka lalu langsung menutup menunya sendiri di klik yang
// sama. Makanya elemen <thead>-nya TIDAK BOLEH diganti lagi setelah ini; render ulang
// selanjutnya hanya menulis ulang innerHTML-nya (lihat initTabelOutstanding/renderTabelPO).
let poRtSudahInit = false

function poInitReportTableSekali () {
  if (poRtSudahInit || typeof ReportTable === 'undefined') { return }
  poRtSudahInit = true

  // Tabel yang tabnya SEDANG TIDAK aktif diikat lebih dulu, tanpa bar (supaya bar
  // tidak ikut terikat dua kali). Tabel yang aktif diikat belakangan lewat selektor
  // dinamis sekaligus mengikat bar - itulah cfg akhir yang dipakai report-table.js.
  let urutAktif = poUrutTabAktif()
  let idTabel = { 1 : '#tabel', 2 : '#tabel2', 3 : '#tabelso' }
  Object.keys(idTabel).forEach((u) => {
    if (Number(u) === urutAktif) { return }
    ReportTable.init({
      table    : idTabel[u],
      onChange : poOnChangeAktif
    })
  });

  ReportTable.init({
    table    : PO_SELEKTOR_TABEL_AKTIF,
    bar      : '#rtBar',
    onChange : poOnChangeAktif
  })

  // DataTables memasang handler sort LANGSUNG di tiap <th> (bukan didelegasikan), sedangkan
  // roda gigi/drag milik report-table.js didelegasikan di <thead> lewat listener fase bubble.
  // <th> ada DI ANTARA tombol roda gigi dan <thead>, jadi klik pada roda gigi selalu lewat
  // <th> dulu sebelum sampai <thead> - tanpa penanganan khusus, tiap klik roda gigi juga
  // memicu sort DataTables (ubah urutan + fetch ulang data yang tidak perlu).
  //
  // stopPropagation() TIDAK BISA dipakai untuk "memblokir <th> tapi tetap sampai ke <thead>":
  // begitu propagation dihentikan di titik manapun sebelum event mencapai targetnya, seluruh
  // sisa perjalanan event (termasuk fase bubble yang seharusnya sampai ke <thead>) ikut batal -
  // itu sebabnya percobaan pertama (stopPropagation di fase capture) malah membuat roda gigi
  // sama sekali tidak merespons klik, bukan cuma berhenti memicu sort.
  //
  // Solusinya: hentikan event ASLINYA sebelum sempat mencapai <th> (fase capture, di <thead>),
  // lalu tembakkan ULANG satu event click baru langsung ke <thead> dengan `target` di-override
  // supaya listener report-table.js tetap mengenali elemen roda gigi/pegangan yang sebenarnya
  // diklik. Event tembakan ulang ini tidak lewat <th> sama sekali, jadi DataTables tidak pernah
  // melihatnya. Flag poGuardUlangKlik mencegah listener fase capture ini memproses event
  // tembakan ulangnya sendiri (yang juga transit lewat <thead>, elemen yang sama).
  let poGuardUlangKlik = false
  let idThead = ['tabel_header', 'tabel2_header', 'tabelso_header']
  idThead.forEach((id) => {
    let thead = document.getElementById(id)
    if (!thead) { return }
    thead.addEventListener('click', function (e) {
      if (poGuardUlangKlik) { return }
      let interaktif = e.target && e.target.closest && e.target.closest('.th-gear, .th-grip')
      if (!interaktif) { return }

      e.stopPropagation()
      e.preventDefault()

      poGuardUlangKlik = true
      let ulang = new MouseEvent('click', { bubbles : false, cancelable : true, view : window })
      Object.defineProperty(ulang, 'target', { value : interaktif, configurable : true })
      thead.dispatchEvent(ulang)
      poGuardUlangKlik = false
    }, true)
  });
}

// Pindahkan elemen #rtBar supaya duduk tepat sebelum tabel yang sedang aktif.
// Memindahkan sebuah node di DOM tidak melepas event listener yang menempel padanya,
// jadi bar tidak perlu diikat ulang - report-table.js sudah mengikatnya sekali lewat
// poInitReportTableSekali().
//
// BUG YANG PERNAH TERJADI: dulu fungsi ini selalu memakai <table> itu sendiri sebagai
// acuan (tabel.parentNode.insertBefore(bar, tabel)). Itu cuma aman SEBELUM DataTables
// membungkus tabel jadi #tabel_wrapper/#tabel2_wrapper. Begitu tab yang dituju pernah
// digambar sekali (jadi tabelnya sudah dibungkus DataTables) dan user pindah tab lagi,
// poPindahBar() ikut dipanggil oleh handler shown.bs.tab - saat itu tabel.parentNode
// sudah jadi ISI wrapper, jadi #rtBar malah tersisip DI DALAM wrapper (bukan sebelum
// wrapper). Reset kolom & bar kolom tersembunyi jadi tampak normal saat itu, tapi begitu
// user menggeser/menyembunyikan kolom, renderTabelPO()/initTabelOutstanding() memanggil
// .DataTable().destroy() yang MENGHAPUS wrapper lama - dan #rtBar ikut terhapus karena
// posisinya di dalam wrapper itu. Refresh manual memulihkannya karena loadAll() memanggil
// poPindahBar() sebelum tabel sempat dibungkus DataTables lagi. Perbaikannya: kalau
// tabelnya sudah dibungkus DataTables, pakai elemen wrapper-nya sebagai acuan posisi,
// bukan <table>-nya - supaya #rtBar selalu jadi SIBLING dari wrapper, bukan anak di
// dalamnya.
function poPindahBar (urut) {
  let bar = document.getElementById('rtBar')
  let id = urut === 2 ? 'tabel2' : PO_OUT[urut].tabel
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

// Ikat kotak search custom (#poSearch1/#poSearch2/#poSearch3, statis di blade - lihat
// catatan #rtBar di atas soal kenapa elemen di luar wrapper aman dari destroy()) ke
// instance DataTables yang sedang aktif. Diikat sekali per input lewat dataset.rtBound
// karena initTabelOutstanding()/renderTabelPO() memanggil ini tiap kali tabel
// di-destroy+init ulang, sementara elemen inputnya sendiri tidak pernah diganti.
let poTimerSearch = { 1 : null, 3 : null }
function poIkatSearch (urut) {
  let input = document.getElementById(urut === 2 ? 'poSearch2' : PO_OUT[urut].search)
  if (!input || input.dataset.rtBound) { return }
  input.dataset.rtBound = '1'

  input.addEventListener('input', function () {
    let nilai = input.value
    if (urut === 2) {
      $('#tabel2').DataTable().search(nilai).draw()
      return
    }
    // Tabel outstanding server-side - jangan tembak server tiap ketukan, sama seperti
    // searchDelay bawaan DataTables yang dulu berlaku untuk kotak search default.
    if (poTimerSearch[urut]) { clearTimeout(poTimerSearch[urut]) }
    poTimerSearch[urut] = setTimeout(function () {
      $('#' + PO_OUT[urut].tabel).DataTable().search(nilai).draw()
    }, 400)
  })
}

// Ikat dropdown "Tampilkan" (#poLen1/#poLen2/#poLen3). Sama seperti kotak search di atas:
// elemennya statis di blade dan berada DI LUAR wrapper DataTables, jadi tidak ikut
// terhapus saat DataTables di-destroy - makanya cukup diikat sekali, ditandai lewat
// dataset.rtBound.
//
// page.len(n).draw() sengaja dipakai, BUKAN destroy+init lewat initTabelOutstanding():
// DataTables sendiri yang menghitung ulang halaman lalu menembak ajax dengan length
// yang baru, jadi susunan kolom, urutan sort, dan kata pencarian tetap utuh. Untuk urut 2
// (tab Purchase Order, paging murni di client) page.len().draw() juga aman karena
// datanya sudah ada semua di tabel - DataTables hanya menghitung ulang halaman tampilnya.
// .draw() tanpa argumen mengembalikan tampilan ke halaman pertama - memang yang
// diinginkan, karena nomor halaman lama tidak lagi berarti setelah jumlah baris berubah.
function poIkatPanjangHalaman (urut) {
  let sel = document.getElementById(urut === 2 ? 'poLen2' : PO_OUT[urut].len)
  if (!sel || sel.dataset.rtBound) { return }
  sel.dataset.rtBound = '1'
  sel.value = String(poPanjangHalaman[urut])

  sel.addEventListener('change', function () {
    let n = Number(sel.value)
    poPanjangHalaman[urut] = (n === -1 || n > 0) ? n : 10
    $('#' + (urut === 2 ? 'tabel2' : PO_OUT[urut].tabel)).DataTable().page.len(poPanjangHalaman[urut]).draw()
  })
}

// Ubah salah satu tanggal periode -> muat ulang data tab Purchase Order. Wajib pakai
// loadTabelPO(true): tanpa flag paksa, penjaga poTab2Sudahdimuat akan menolak
// permintaan kedua dan tabel tidak pernah berubah.
function poIkatPeriode () {
  let awal  = document.getElementById('poTglAwal')
  let akhir = document.getElementById('poTglAkhir')
  if (!awal || !akhir || awal.dataset.rtBound) { return }
  awal.dataset.rtBound = '1'

  let onUbah = function () {
    if (!awal.value || !akhir.value) { return }
    if (awal.value > akhir.value) {
      alertify.warning('Tanggal awal tidak boleh melebihi tanggal akhir')
      return
    }
    loadTabelPO(true)
  }

  awal.addEventListener('change', onUbah)
  akhir.addEventListener('change', onUbah)
}

// Status penerimaan PO dihitung di browser dari qnt & qntbeli - urutannya penting,
// yang cocok duluan menang (qnt 0 selalu Batal, walau qntbeli juga 0).
function poAngka (v) {
  let n = Number(v)
  return isNaN(n) ? 0 : n
}

function poStatusPO (item) {
  let qnt   = poAngka(item.qnt)
  let qbeli = poAngka(item.qntbeli)
  if (qnt === 0)    { return 'Batal' }
  if (qbeli === 0)  { return 'Belum' }
  if (qbeli < qnt)  { return 'Sebagian' }
  return 'Sudah'
}

// Kelas badge disamakan dengan reportpengadaanpopo: hijau=selesai, biru=menunggu,
// kuning=sebagian, merah=batal.
const PO_BADGE_STATUS = {
  'Sudah'    : 'is-active',
  'Belum'    : 'is-user',
  'Sebagian' : 'is-supervisor',
  'Batal'    : 'is-inactive'
}

function poBadgeStatus (item) {
  let status = poStatusPO(item)
  let kelas = PO_BADGE_STATUS[status] || ''
  return `<span class="sp-badge ${kelas}">${status}</span>`
}

// Otorisasi level 1 - artinya sama dengan kolom Oto1 di tabel: 1 = sudah, 0 = belum.
function poOtorisasiPO (item) {
  return Number(item.IsOtorisasi1) ? 'Sudah' : 'Belum'
}

// 'SEMUA' = tidak menyaring. Nilainya sengaja disimpan di luar renderTabelPO()
// supaya tetap berlaku saat tabel digambar ulang (ganti periode, sehabis simpan, dst).
let poFilterStatus = 'SEMUA'
let poFilterOtorisasi = 'SEMUA'

function poUpdateFilterBadge () {
  let jml = 0
  if (poFilterStatus !== 'SEMUA') { jml++ }
  if (poFilterOtorisasi !== 'SEMUA') { jml++ }
  let badge = document.getElementById('poFilterBadge')
  if (badge) { badge.textContent = jml + ' aktif' }
}

function poTerapkanFilter () {
  poFilterStatus = $('#poModalStatus').val() || 'SEMUA'
  poFilterOtorisasi = $('#poModalOtorisasi').val() || 'SEMUA'
  poUpdateFilterBadge()
  $('#modalFilterPO').modal('hide')
  renderTabelPO()
}

function poResetFilter () {
  poFilterStatus = 'SEMUA'
  poFilterOtorisasi = 'SEMUA'
  $('#poModalStatus').val('SEMUA')
  $('#poModalOtorisasi').val('SEMUA')
  poUpdateFilterBadge()
  $('#modalFilterPO').modal('hide')
  renderTabelPO()
}

// Kotak scroll tabel dibuat setinggi sisa ruang di #content supaya halaman TIDAK perlu
// scrollbar sendiri - yang discroll hanya isi tabel. Diukur dari DOM, bukan angka mati
// seperti 65vh, karena tinggi bagian di atas/bawah kotak (kartu tab, toolbar, #rtBar,
// pagination, catatan kaki) berbeda antar tab dan bisa berubah.
function poAturTinggiTabel () {
  let area = document.getElementById('content')
  let pane = document.querySelector('#myTabContent .tab-pane.active')
  if (!area || !pane) { return }
  let wrap = pane.querySelector('.po-table-wrap')
  if (!wrap) { return }

  // Batasnya dibuka dulu supaya pengukuran memakai tinggi asli isi tab, bukan tinggi
  // hasil pembatasan panggilan sebelumnya.
  wrap.style.maxHeight = 'none'

  let padBawah = parseFloat(getComputedStyle(area).paddingBottom) || 0
  let batasBawah = area.getBoundingClientRect().bottom - padBawah
  let kotak = wrap.getBoundingClientRect()
  // Bagian yang ada DI BAWAH kotak tabel (pagination + catatan kaki) harus ikut
  // disisihkan, kalau tidak keduanya terdorong keluar layar.
  let bawah = pane.getBoundingClientRect().bottom - kotak.bottom

  let sisa = batasBawah - kotak.top - bawah - 4
  wrap.style.maxHeight = Math.max(200, Math.floor(sisa)) + 'px'
}

/* ---- Jembatan ke mesin penyimpan milik report-table.js ----
 * doMoveHeader / doButtonVisibility / doSetDesimal / doButtonTotal SENGAJA tidak
 * didefinisikan: report-table.js sudah punya fallback yang memutasi gcart_header
 * sendiri lalu memanggil saveHeader(), dan saveHeader() itulah yang mampir ke
 * doSimpanHeader di bawah. Jadi yang perlu disediakan hanya dua fungsi ini.
 */
window.g_href = PO_HREF
window.g_modeReport = 1
window.gcart_header = []

// report-table.js meneruskan window.g_modeReport apa adanya; disaring di sini supaya
// nilai yang tidak dikenal tidak sampai tersimpan ke DBHEADERTABLE sebagai urut asing.
function poUrutSah (mode) {
  let urut = Number(mode)
  return (urut === 2 || urut === 3) ? urut : 1
}

window.doSimpanHeader = function (href, mode) {
  let urut = poUrutSah(mode)
  let cart = poCart[urut] || []

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
      // DBHEADERTABLE tidak punya kolom desimal; kolom `tipe` dipakai untuk itu
      // karena selama ini ditulis kosong dan tidak pernah dibaca balik.
      tipe     : JSON.stringify(desimal),
      value    : JSON.stringify(value),
      isshown  : JSON.stringify(isshown),
      href     : PO_HREF,
      urut     : urut
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
  let urut = poUrutSah(mode)

  $.ajax({
    url   : "{!! url('getheadertable') !!}",
    type  : "post",
    async : false,
    data  : {
      _token : $("#_token").val(),
      href   : PO_HREF,
      urut   : urut,
      reset  : 1
    },
    success : function (res) {
      if (urut === 2) {
        poCart[2] = poBuatCart(res.headertableheader2, res.headertablevalue2, res.isnumeric2, res.isshown2, res.desimal2, res.aliasordered2)
      } else if (urut === 3) {
        poCart[3] = poBuatCart(res.headertableheader3, res.headertablevalue3, res.isnumeric3, res.isshown3, res.desimal3, res.aliasordered3)
      } else {
        poCart[1] = poBuatCart(res.headertableheader, res.headertablevalue, res.isnumeric, res.isshown, res.desimal, res.aliasordered)
      }
      window.gcart_header = poCart[urut]
    },
    error : function (err) {
      console.log(err)
      alertify.warning('Gagal mengembalikan kolom ke pengaturan awal')
    }
  })
}

// formatAngka() selalu menempelkan '.' + bagian desimal, sehingga input tanpa titik
// (mis. hasil toFixed(0)) jadi "123.undefined". Fungsi itu dipakai di puluhan tempat
// lain jadi tidak diubah; khusus tabel ini dipakai versi yang sadar jumlah desimal.
function poFormatAngkaDes (nilai, des) {
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

function poRenderNilai (col, item) {
  let nilai = item[col.field]
  if (col.tipe === 1) {
    return poFormatAngkaDes(nilai, col.desimal)
  }
  if (col.tipe === 2) {
    return nilai ? formatDate(nilai) : ""
  }
  return (nilai === null || nilai === undefined) ? "" : nilai
}

// Baris "tidak ada data" bawaan DataTables memakai <td colspan=N> yang N-nya dihitung
// saat init. Di halaman ini <thead> ditulis ulang ReportTable tiap render dan tabelnya
// di-destroy+init berkali-kali, jadi N itu tidak bisa dipercaya - teksnya berakhir
// terjepit di kolom Actions. Karena itu pesannya dipindah keluar dari <table>: baris
// bawaannya disembunyikan, teksnya ditampilkan sebagai blok biasa selebar kotak tabel
// sehingga rata tengahnya tidak bergantung pada colspan sama sekali.
function poRapikanBarisKosong (api) {
  let wrap = $(api.table().container()).find('.po-table-wrap')[0]
  if (!wrap) { return }

  // Dipasang SEBAGAI ANAK wrap (bukan sibling setelahnya) supaya tekasnya nongol tepat
  // di bawah header tabel. wrap ini melar mengisi sisa tinggi tab (lihat poAturTinggiTabel),
  // jadi kalau pesannya taruh di luar/sesudah wrap, dia terdorong ke bawah kotak kosong
  // yang tinggi itu - persis keluhan user: teksnya nongol di bawah scrollbar, bukan di atas.
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

// Menggambar salah satu tabel outstanding: urut 1 = Outstanding PR, urut 3 =
// Outstanding SO. Bedanya cuma isi PO_OUT[urut] (id elemen, endpoint, ada/tidaknya
// kolom Actions), sisanya identik.
//
// pakaiCache = true dipanggil dari poOnChangeAktif() saat kolom digeser/disembunyikan/
// diubah desimalnya - datanya tidak berubah, jadi respons terakhir yang tersimpan di
// poCacheOut[urut] dipakai lagi, tanpa menembak server. pakaiCache diabaikan (selalu
// ambil data baru) kalau memang belum ada cache untuk dipakai.
function initTabelOutstanding (urut, pakaiCache) {
  let cfg = PO_OUT[urut]
  if (!cfg) { return }
  let selTabel = '#' + cfg.tabel
  poAktifkanTabel(urut)

  // Simpan posisi tampilan (halaman, urutan, kata pencarian) supaya destroy+init
  // di bawah tidak mengembalikan tabel ke keadaan awal - dirasakan terutama saat
  // pengguna sedang menggeser kolom di tengah pencarian/halaman tertentu.
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

  // Satu daftar kolom dipakai bersama oleh header, isi baris, dan pemetaan sort.
  let cols = poKolomTampil(urut)
  let kolomRender = cols.map(poKolomRender)

  // <thead> HANYA ditulis ulang innerHTML-nya, elemennya sendiri tidak diganti -
  // sudah diikat sekali oleh poInitReportTableSekali(). Lihat catatan di sana.
  let thead = document.getElementById(cfg.thead)
  thead.innerHTML = poHeadHtml(cols)
  let baris = thead.querySelector('tr')
  if (baris && cfg.aksi) {
    baris.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  }

  let columns = []
  if (cfg.aksi) {
    columns.push({
      data : null,
      orderable : false,
      className : 'text-center',
      render : function (data, type, row) {
        return `<button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Buat PO" onclick="buttonAdd('${row.Nobukti}')"><i class="bi bi-plus-lg"></i></button>`
      }
    })
  }

  kolomRender.forEach((c) => {
    columns.push({
      data : null,
      className : c.tipe === 1 ? 'text-right' : '',
      render : function (data, type, row) {
        return poRenderNilai(c, row)
      }
    })
  });

  poPakaiCacheOut[urut] = !!(pakaiCache && poCacheOut[urut])

  // Tanpa satu kolom pun DataTables melempar error saat init. Bisa terjadi kalau
  // konfigurasi kolomnya belum pernah terbentuk - lihat getHeaderTable(): kolom awal
  // diturunkan dari satu baris contoh, jadi kalau saat itu memang belum ada data
  // outstanding sama sekali, daftar kolomnya ikut kosong.
  if (!columns.length) {
    thead.innerHTML = '<tr><th style="padding: 4px 12px;" scope="col">' + cfg.nama + '</th></tr>'
    document.getElementById(cfg.tbody).innerHTML =
      '<tr><td class="text-center" style="padding: 14px;">Belum ada data untuk ditampilkan</td></tr>'
    return
  }

  // posisi.order diambil dari SEBELUM kolom berubah - kalau kolom yang tadi dipakai
  // mengurutkan sudah tidak ada / bergeser di luar jangkauan (mis. baru saja disembunyikan),
  // indeksnya bisa menunjuk ke kolom yang tidak ada lagi dan DataTables melempar error.
  let orderAman = posisi ? posisi.order.filter((o) => o[0] < columns.length) : []

  $(selTabel).DataTable({
    // Indikator "sedang memuat". DataTables menampilkannya sendiri di SETIAP siklus
    // ambil data server-side - muat awal, cari, sortir, pindah halaman, dan ganti isi
    // dropdown "Tampilkan" - lalu menyembunyikannya lagi setelah barisnya selesai
    // digambar. Jadi pada pilihan "Semua data" yang berat, lapisan ini tetap terlihat
    // selama browser sibuk merender, bukan cuma selama menunggu jawaban server.
    //
    // Sebelumnya dimatikan karena tampilan bawaannya (teks polos di top:50% wrapper,
    // tanpa latar) terlihat mengapung entah di mana. Sekarang dihidupkan lagi dengan
    // elemennya ditata ulang lewat CSS menjadi lapisan yang menutup SELURUH wrapper
    // tabelnya - lihat blok .po-loading-* di bagian atas file. Karena yang menggulung
    // hanyalah .po-table-wrap DI DALAM wrapper, bukan wrapper-nya sendiri, lapisan itu
    // diam di tempat berapa pun isi tabel discroll.
    "processing" : true,
    // Boleh berisi HTML - dipasang lewat innerHTML saat elemennya dibuat.
    "language" : {
      "processing" : '<span class="po-loading-chip"><span class="po-loading-spin"></span>Memuat data...</span>'
    },
    "serverSide" : true,
    // Pemilih jumlah baris bawaan DataTables tetap dimatikan; yang dipakai adalah
    // dropdown #poLen1/#poLen3 di toolbar, supaya tampilannya seragam dengan kotak search
    // dan periode yang juga dibuat sendiri - lihat poIkatPanjangHalaman().
    "lengthChange" : false,
    "pageLength" : poPanjangHalaman[urut],
    "searchDelay" : 400,
    // "r" di paling depan = elemen indikator memuat, sengaja DI LUAR .po-table-wrap
    // supaya jadi anak langsung wrapper tabel dan tidak ikut tergulung bersama isinya.
    "dom" : "r<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "language" : {
      "emptyTable" : "Tidak ada data",
      "zeroRecords" : "Tidak ada data yang cocok dengan pencarian"
    },
    "drawCallback" : function () { poRapikanBarisKosong(this.api()) },
    "order" : orderAman,
    "displayStart" : posisi ? posisi.start : 0,
    "search" : posisi ? { "search" : posisi.search } : { "search" : "" },
    "columns" : columns,
    // Bentuk fungsi (bukan objek) dipilih khusus supaya bisa disajikan dari cache tanpa
    // menembak server - lihat poPakaiCacheOut di atas. Konsekuensinya opsi ajax.data
    // (dulu dipakai memetakan orderCol/orderDir) sudah tidak berlaku pada bentuk fungsi,
    // jadi pemetaannya dipindah ke sini.
    "ajax" : function (data, callback, settings) {
      if (poPakaiCacheOut[urut] && poCacheOut[urut]) {
        poPakaiCacheOut[urut] = false
        // DataTables menolak respons yang nomor "draw"-nya tidak cocok dengan yang
        // baru saja dikirim (penjaga supaya respons XHR lama/kesasar tidak dipakai).
        // Instance yang baru saja destroy+init ulang mulai dari draw=1 lagi, sedangkan
        // draw pada cache masih milik instance sebelumnya - jadi harus ditimpa dulu.
        callback(Object.assign({}, poCacheOut[urut], { draw : data.draw }))
        return
      }

      // Kolom pertama tabel Outstanding PR adalah kolom Actions yang tidak ada di
      // kolomRender, jadi indeks sort dari DataTables harus digeser satu. Tabel
      // Outstanding SO tidak punya kolom itu, jadi indeksnya sudah pas.
      let geser = cfg.aksi ? 1 : 0
      let kolom = null
      let arah = 'asc'
      if (data.order && data.order.length && data.order[0].column >= geser) {
        let c = kolomRender[data.order[0].column - geser]
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
          orderDir : arah
        },
        success : function (res) {
          poCacheOut[urut] = res
          callback(res)
        },
        error : function (err) {
          // tampilkan pesan asli dari server supaya penyebabnya kelihatan di console
          console.log(cfg.url + ' gagal:', err.status, err.responseText)
          alertify.warning('Gagal memuat data ' + cfg.nama)
          // WAJIB memanggil callback walau gagal: DataTables baru memulihkan
          // settings.bAjaxDataGet di ekor siklus draw (setelah callback dipanggil).
          // Tanpa ini, sekali request gagal, tabel mengabaikan diam-diam setiap
          // interaksi berikutnya (scroll, sort, search, ganti halaman) - seolah membeku.
          callback({ draw : data.draw, data : [], recordsTotal : 0, recordsFiltered : 0 })
        }
      })
    },
    // Dijadwalkan lewat setTimeout, bukan dipanggil langsung: kalau poAturTinggiTabel()
    // melempar error di sini (di dalam siklus draw DataTables), DataTables tidak pernah
    // sampai ke baris yang memulihkan bAjaxDataGet - tabel jadi tidak merespons lagi.
    // Elemen pagination juga baru ada setelah draw selesai.
    "drawCallback" : function () {
      setTimeout(poAturTinggiTabel, 0)
      $(selTabel).find('[data-toggle="tooltip"]').tooltip('dispose')
      $(selTabel).find('[data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })
      poKpiOut[urut] = this.api().page.info().recordsTotal
      renderKpiPO()
    }
  });

  // Integrasi Bootstrap 4 yang ikut dibundel di public/js/datatables.min.js memberi kelas
  // "card" pada elemen indikator memuat (ext.classes.sProcessing = "dataTables_processing
  // card"). Di halaman ini kelas itu merusak: aturan `#page1 .card` di bagian atas file
  // memaksa `display:block !important`, sementara DataTables menyembunyikan indikatornya
  // dengan menulis inline `display:none` - dan inline style selalu kalah melawan
  // !important. Akibatnya lapisan memuat muncul lalu tidak pernah mau hilang.
  //
  // Dilepas di sini, bukan dengan mengubah aturan `#page1 .card`, supaya kartu-kartu lain
  // di halaman ini tidak ikut terpengaruh. Harus di dalam fungsi ini karena elemennya
  // dibuat ulang tiap kali tabel di-destroy+init.
  let elMemuat = document.querySelector('#' + cfg.tabel + '_wrapper > .dataTables_processing')
  if (elMemuat) { elMemuat.classList.remove('card') }

  poIkatSearch(urut)
  poIkatPanjangHalaman(urut)
  // Kotak search custom hidup di luar wrapper DataTables, jadi destroy+init di atas
  // tidak menghapus isinya - hanya perlu disamakan dengan kata pencarian yang
  // sedang dipulihkan (posisi.search), kalau ada.
  let inputSearch = document.getElementById(cfg.search)
  if (inputSearch) { inputSearch.value = posisi ? posisi.search : '' }
  poAturTinggiTabel()
}

// Ambil data tab "Purchase Order". Dipanggil saat tabnya dibuka, bukan saat halaman dimuat.
function loadTabelPO (paksa) {
  if (poTab2Sudahdimuat && !paksa) {
    return
  }
  $.ajax({
    url: "{!! url('poloadpurchaseorder') !!}",
    type: "get",
    data: {
      tglawal  : $('#poTglAwal').val(),
      tglakhir : $('#poTglAkhir').val()
    },
    success: function(res) {
      dataRefreshOutstanding2 = res.tempOutstanding3
      poTab2Sudahdimuat = true
      renderTabelPO()
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat data Purchase Order')
    }
  })
}

function renderTabelPO () {
  let level = $("#level").val()

  poAktifkanTabel(2)

  if ($.fn.DataTable.isDataTable('#tabel2')) {
    $('#tabel2').DataTable().destroy()
  }

  // Satu daftar kolom untuk header DAN isi baris. Sebelumnya header dibangun dari
  // laaliasordered2 yang berisi SEMUA kolom (mengabaikan isshown) sementara barisnya
  // menghormati isshown, sehingga jumlah kolomnya bisa tidak sama.
  let cols2 = poKolomTampil(2)
  let kolomRender2 = cols2.map(poKolomRender)

  // <thead> HANYA ditulis ulang innerHTML-nya, elemennya sendiri tidak diganti -
  // sudah diikat sekali oleh poInitReportTableSekali(). Lihat catatan di sana.
  let thead2 = document.getElementById('tabel2_header')

  // Kolom otorisasi & batal digerbangi $level dan bukan bagian dari konfigurasi kolom,
  // jadi tetap dirakit manual lalu ditempel di belakang judul hasil ReportTable.
  let headerTable2 = ''
headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized</th>
<th style="padding: 4px 12px;" scope="col">User Oto</th>
<th style="padding: 4px 12px;" scope="col">Tanggal Oto</th>
`

  if (level > 1) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized2</th>
    <th style="padding: 4px 12px;" scope="col">User Oto2</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto2</th>
  `
}
  if (level > 2) {
    headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized3</th>
    <th style="padding: 4px 12px;" scope="col">User Oto3</th>
    <th style="padding: 4px 12px;" scope="col">Tanggal Oto3</th>
  `
}
    if (level > 3) {
      headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized4</th>
      <th style="padding: 4px 12px;" scope="col">User Oto4</th>
      <th style="padding: 4px 12px;" scope="col">Tanggal Oto4</th>
    `
  }
      if (level > 4) {
        headerTable2 += `<th style="padding: 4px 12px;" scope="col">Authorized5</th>
        <th style="padding: 4px 12px;" scope="col">User Oto5</th>
        <th style="padding: 4px 12px;" scope="col">Tanggal Oto5</th>
      `


      }

headerTable2 += `<th style="padding: 4px 12px;" scope="col">Batal</th>
<th style="padding: 4px 12px;" scope="col">User Batal</th>
<th style="padding: 4px 12px;" scope="col">Status</th>`

// Judul kolom yang bisa diseret & punya roda gigi dibuat ReportTable, lalu kolom
// "Actions" disisipkan di depan dan kolom otorisasi/batal di belakangnya.
thead2.innerHTML = poHeadHtml(cols2)
let baris2 = thead2.querySelector('tr')
if (baris2) {
  baris2.insertAdjacentHTML('afterbegin', '<th style="padding: 4px 12px;" scope="col">Actions</th>')
  baris2.insertAdjacentHTML('beforeend', headerTable2)
}

  let rowTable2 = ""

  // Data mentahnya satu array datar di [0] - lihat catatan groupBy di controller.
  let dataTampil2 = (dataRefreshOutstanding2 && dataRefreshOutstanding2[0]) ? dataRefreshOutstanding2[0] : []
  if (poFilterStatus !== 'SEMUA') {
    dataTampil2 = dataTampil2.filter(function (r) { return poStatusPO(r) === poFilterStatus })
  }
  if (poFilterOtorisasi !== 'SEMUA') {
    dataTampil2 = dataTampil2.filter(function (r) { return poOtorisasiPO(r) === poFilterOtorisasi })
  }

  if (dataTampil2.length > 0) {
    dataTampil2.forEach((item, i) => {

        // PO yang sudah terotorisasi tidak boleh diedit lagi - tombol Edit/Otorisasi
        // diganti Batal Otorisasi + Print. Yang belum terotorisasi sebaliknya.
        // Warna & ikon disamakan dengan pembelianpermintaanagen.blade.php.
        let tombolAksiPO = `<button class="btn btn-warning btn-sm" type="button" data-toggle="tooltip" title="Detail" onclick="buttonDetail('${item.NoBukti}')"><i class="bi bi-info"></i></button>`
        if (Number(item.IsOtorisasi1)) {
          tombolAksiPO += `
            <button class="btn btn-danger btn-sm" type="button" data-toggle="tooltip" title="Batal Otorisasi" onclick="buttonBatalOtorisasi('${item.NoBukti}')"><i class="bi bi-key"></i></button>
            <button class="btn btn-primary btn-sm" type="button" data-toggle="tooltip" title="Print" onclick="openPrintModal('${item.NoBukti}')"><i class="bi bi-printer"></i></button>`
        } else {
          tombolAksiPO += `
            <button class="btn btn-info btn-sm" type="button" data-toggle="tooltip" title="Otorisasi" onclick="buttonOtorisasi('${item.NoBukti}' , ${item.IsOtorisasi1})"><i class="bi bi-key"></i></button>
            <button class="btn btn-success btn-sm" type="button" data-toggle="tooltip" title="Edit" onclick="buttonEdit('${item.NoBukti}')"><i class="bi bi-pen"></i></button>`
        }

        rowTable2 += `
        <tr>
          <td class="text-center" style=''>
            ${tombolAksiPO}
          </td>

          `
          // Daftar kolom yang sama persis dengan yang dipakai header di atas.
          kolomRender2.forEach((c) => {
            if (c.tipe === 1) {
              rowTable2 += `<td style="text-align: right;">${poRenderNilai(c, item)}</td>`
            } else {
              rowTable2 += `<td>${poRenderNilai(c, item)}</td>`
            }
          });

          rowTable2 += `

          ${Number(item.IsOtorisasi1) ?
              '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
            :
            '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
          }
          <td>${item.TglOto1 || ''}</td>

          <td>${item.OtoUser1 || ''}</td>
          `

          if (level > 1) {
            rowTable2 += `
            ${Number(item.IsOtorisasi2) ?
                '<td class="text-success text-center">2<i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
              :
              '<td class="text-danger text-center">2<i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
            }

            <td>${item.OtoUser2 || '' } </td>
            <td>${item.TglOto2 || '' } </td>


            `
            if (level > 2) {
              rowTable2 += `
              ${Number(item.IsOtorisasi3) ?
                  '<td class="text-success text-center">3<i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
                :
                '<td class="text-danger text-center">3<i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
              }
              <td>${item.TglOto3 || ''} </td>

              <td>${item.OtoUser3 || '' } </td>
              `
              if (level > 3) {
                rowTable2 += `
                ${Number(item.IsOtorisasi4) ?
                    '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
                  :
                  '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
                }
                <td>${item.TglOto4 || ''}</td>

                <td>${item.OtoUser4 || '' }</td>
                `
                if (level > 4) {
                  rowTable2 += `
                  ${Number(item.IsOtorisasi5) ?
                      '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>'
                    :
                    '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
                  }
                  <td>${item.TglOto5 || '' }</td>

                  <td>${item.OtoUser5 || '' }</td>
                  `


                }

              }

            }

          }

          rowTable2 += `  ${item.IsBatal == 1 ?
              '<td class="text-success text-center"><i class="bi bi-check2" style="-webkit-text-stroke-width: 2px;"><div style="display: none">1</div></i></td>' :
              '<td class="text-danger text-center"><i class="bi bi-x" style="-webkit-text-stroke-width: 2px;"><div style="display: none">0</div></i></td>'
            }
          <td>${item.UserBatal || ''}</td>
          <td class="text-center">${poBadgeStatus(item)}</td>

        </tr>
        `
    });
}

  // Buang tooltip lama sebelum tombolnya diganti - kalau tidak, elemen tooltip
  // Bootstrap yang sudah dibuat tertinggal di <body> dan bisa menutupi tombol baru.
  $('#tabel2_data').find('[data-toggle="tooltip"]').tooltip('dispose')
  document.getElementById("tabel2_data").innerHTML = rowTable2

  $("#tabel2").DataTable({
    "lengthChange": false,
    "pageLength": poPanjangHalaman[2],
    // "order": [] WAJIB - tanpa ini DataTables jatuh ke default [[0,'asc']] (kolom
    // Actions), yang selama ini kebetulan tidak terlihat karena isinya HTML yang
    // di-strip jadi kunci kosong semua. Data sudah datang terurut dari server
    // (Tanggal/NoBukti terbaru dulu - lihat POController@loadPurchaseOrder), jadi
    // di sini cukup dipertahankan urutan DOM apa adanya.
    "order": [],
    "dom": "<'po-table-wrap't><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    "language": {
      "emptyTable": "Tidak ada data",
      "zeroRecords": "Tidak ada data yang cocok dengan pencarian"
    },
    "drawCallback": function () { poRapikanBarisKosong(this.api()) },
  });

  poIkatSearch(2)
  poIkatPanjangHalaman(2)
  poIkatPeriode()
  // Init DataTable di atas mereset filter pencarian - kotak #poSearch2 sendiri statis
  // di blade dan nilainya tidak ikut hilang, jadi diterapkan ulang di sini supaya
  // ganti periode tanggal tidak diam-diam membuang kata pencarian yang sedang aktif.
  let inputSearch2 = document.getElementById('poSearch2')
  if (inputSearch2 && inputSearch2.value) {
    $('#tabel2').DataTable().search(inputSearch2.value).draw()
  }
  poAturTinggiTabel()
  // container:'body' supaya tooltip tidak terjepit di dalam <td> sempit,
  // boundary:'window' supaya Popper tidak menganggap .po-table-wrap yang pendek
  // sebagai batas dan menumpuk tooltip di atas tombol.
  $('#tabel2_data [data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' })

  poKpiDPP = dataTampil2
  renderKpiPO()
}

// Kartu ringkasan (Jumlah Supplier / Total DPP / Outstanding PR / Outstanding SO) di atas
// tab-content. Jumlah Supplier & Total DPP dihitung dari dataTampil2 (baris yang sedang
// tampil di tab Purchase Order, sudah kena filter periode + status/otorisasi).
// Outstanding PR/SO diambil dari recordsTotal endpoint podataoutstandingpr/so - satu
// baris di sana = satu kode barang, jadi 1 nobukti dengan 3 barang terhitung 3.
let poKpiDPP = []
let poKpiOut = { 1 : null, 3 : null }

function renderKpiPO () {
  let totalDPP = 0
  let suppSet = new Set()
  ;(poKpiDPP || []).forEach((r) => {
    totalDPP += Number(r.TotDPPRp) || 0
    // 1 supplier = 1 hitungan walau punya banyak PO. KodeSupp di-trim + upper supaya
    // kode yang sama tidak terhitung dobel hanya karena beda spasi/huruf.
    let kodeSupp = String(r.KodeSupp || '').trim().toUpperCase()
    if (kodeSupp) { suppSet.add(kodeSupp) }
  })

  let cards = [
    ['Jumlah Supplier', suppSet.size, '#dc2626', '#fee2e2', 'bi bi-people', false],
    ['Total DPP', totalDPP, '#4f46e5', '#ede9fe', 'bi bi-receipt', true],
    ['Outstanding PR', poKpiOut[1] === null ? '-' : poKpiOut[1], '#0891b2', '#cffafe', 'bi bi-clipboard-data', false],
    ['Outstanding SO', poKpiOut[3] === null ? '-' : poKpiOut[3], '#ca8a04', '#fef9c3', 'bi bi-cart-check', false]
  ]

  document.getElementById('poKpiStrip').innerHTML = cards.map((c) => `
    <div class="po-kpi-card">
      <div class="po-kpi-ic" style="background:${c[3]};color:${c[2]}">
        <i class="${c[4]}"></i>
      </div>
      <div>
        <div class="po-kpi-label">${c[0]}</div>
        <div class="po-kpi-val">${c[5] ? 'Rp ' + poFormatAngkaDes(c[1], 2) : c[1]}</div>
      </div>
    </div>
  `).join('')
}

function loadAll () {
  console.log('loadall')

  // Idempotent - hanya benar-benar mengikat sekali seumur halaman, lihat definisinya.
  poInitReportTableSekali()

  let meta = null
  $.ajax({
    url: "{!! url('poloadall') !!}",
    type: "get",
    async: false,
    data: {
    },
    success: function(res) {
      meta = res
    },
    error: function (err) {
      console.log(err)
      alertify.warning('Gagal memuat konfigurasi tabel')
    }})

  if (!meta) {
    return
  }

  laisshown = meta.isshown
  laheadertable = meta.headertableheader
  laheadertablevalue = meta.headertablevalue
  laisnumeric = meta.isnumeric
  ladesimal = meta.desimal
  laaliasordered = meta.aliasordered

  laisshown2 = meta.isshown2
  laheadertable2 = meta.headertableheader2
  laheadertablevalue2 = meta.headertablevalue2
  laisnumeric2 = meta.isnumeric2
  ladesimal2 = meta.desimal2
  laaliasordered2 = meta.aliasordered2

  poCart[1] = poBuatCart(laheadertable, laheadertablevalue, laisnumeric, laisshown, ladesimal, laaliasordered)
  poCart[2] = poBuatCart(laheadertable2, laheadertablevalue2, laisnumeric2, laisshown2, ladesimal2, laaliasordered2)
  poCart[3] = poBuatCart(meta.headertableheader3, meta.headertablevalue3, meta.isnumeric3, meta.isshown3, meta.desimal3, meta.aliasordered3)

  // Data tab Purchase Order dianggap belum dimuat lagi - kalau tab itu sedang tidak
  // aktif, ambilnya ditunda sampai tabnya benar-benar dibuka (lihat poPerluGambar di bawah).
  dataRefreshOutstanding2 = []
  poTab2Sudahdimuat = false

  // Hanya tabel di tab yang SEDANG AKTIF yang digambar (dan menembak server kalau
  // memang perlu). Tabel lainnya ditandai lewat poPerluGambar dan baru benar-benar
  // dikerjakan saat tabnya dibuka - lihat handler shown.bs.tab di bawah. Ini yang
  // menghilangkan lag: sebelumnya semua tabel selalu digambar ulang di sini.
  let urutAktif = poUrutTabAktif()
  poPindahBar(urutAktif)

  ;[1, 2, 3].forEach((u) => {
    poPerluGambar[u] = (u !== urutAktif)
  });

  if (urutAktif === 2) {
    loadTabelPO()
  } else {
    initTabelOutstanding(urutAktif)
  }
}

// Kartu KPI Outstanding PR/SO tampil di semua tab, tapi tabelnya sendiri baru
// digambar saat tabnya dibuka (lazy). Supaya kartunya tidak menunggu itu, total
// barisnya diambil sendiri di sini dengan payload minimal (length=1), terpisah
// dari initTabelOutstanding().
function poMuatKpiOutstanding () {
  ;[1, 3].forEach((urut) => {
    $.ajax({
      url : PO_OUT[urut].url,
      type : "get",
      cache : false,
      data : { draw : 1, start : 0, length : 1, search : '' },
      success : function (res) {
        poKpiOut[urut] = res.recordsTotal
        renderKpiPO()
      },
      error : function (err) {
        console.log(PO_OUT[urut].url + ' gagal memuat KPI:', err.status, err.responseText)
      }
    })
  })
}
poMuatKpiOutstanding()

// Dipanggil tiap kali PO berhasil disimpan (baru/edit/tambah dari SO) supaya barang
// yang baru saja diambil langsung hilang dari tab Outstanding PR/SO tanpa perlu F5.
// Cache-nya dikosongkan dulu (poCacheOut/poPakaiCacheOut) supaya draw berikutnya
// benar-benar menembak server, bukan memakai respons lama - lihat initTabelOutstanding().
function poSegarkanOutstanding () {
  ;[1, 3].forEach((urut) => {
    poCacheOut[urut] = null
    poPakaiCacheOut[urut] = false
    let selTabel = '#' + PO_OUT[urut].tabel
    if ($.fn.DataTable.isDataTable(selTabel)) {
      $(selTabel).DataTable().ajax.reload(null, false)
    }
  })
  poMuatKpiOutstanding()
}

$(function () {
  muatDropdownAlamatKirim()
  muatDropdownValas()

  $('#modalFilterPO').on('show.bs.modal', function () {
    $('#poModalStatus').val(poFilterStatus)
    $('#poModalOtorisasi').val(poFilterOtorisasi)
    poUpdateFilterBadge()
  })

  // Pindah tab TIDAK menggambar ulang tabelnya lagi (itu sebabnya pindah tab dulu
  // terasa lambat) - cukup arahkan komponen (poAktifkanTabel, poPindahBar,
  // ReportTable.refresh) ke tabel yang baru aktif. Redraw sungguhan hanya terjadi
  // kalau memang ditandai perlu oleh loadAll() sehabis simpan/edit/otorisasi, atau
  // kalau tab Purchase Order memang belum pernah dimuat sama sekali.
  $('#nav-profile-tab').on('shown.bs.tab', function () {
    poAktifkanTabel(2)
    poPindahBar(2)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (!poTab2Sudahdimuat) {
      poPerluGambar[2] = false
      loadTabelPO()
    } else if (poPerluGambar[2]) {
      poPerluGambar[2] = false
      loadTabelPO(true)
    } else {
      // Tab-nya sudah pernah digambar dan tidak perlu digambar ulang - tinggi bagian
      // atas/bawah kotak beda antar tab, jadi tetap perlu diukur ulang di sini.
      poAturTinggiTabel()
    }
  })

  // Kedua tab outstanding (PR & SO) ditangani handler yang sama - lihat PO_OUT.
  $('#nav-home-tab, #nav-outso-tab').on('shown.bs.tab', function () {
    let urut = this.id === 'nav-outso-tab' ? 3 : 1
    poAktifkanTabel(urut)
    poPindahBar(urut)
    if (typeof ReportTable !== 'undefined') { ReportTable.refresh() }

    if (poPerluGambar[urut]) {
      poPerluGambar[urut] = false
      initTabelOutstanding(urut)
    } else {
      poAturTinggiTabel()
    }
  })

  // Layar diubah ukurannya (mis. resize jendela) - tinggi kotak tabel diukur ulang supaya
  // tetap pas, didebounce supaya tidak menghitung ulang di setiap event resize.
  let poTimerResize = null
  $(window).on('resize', function () {
    if (poTimerResize) { clearTimeout(poTimerResize) }
    poTimerResize = setTimeout(poAturTinggiTabel, 150)
  })
})

/* BACKUP - isi modal "Table Setting" lama. refreshHeaderTable() memang terdefinisi
   dua kali (identik) di file aslinya, dan onclickcheckboxheadertable() yang dipanggil
   checkbox "Tampil" tidak pernah ada sehingga fitur sembunyikan kolom lewat modal ini
   selalu melempar ReferenceError. Semuanya digantikan roda gigi + bar #rtBar1/#rtBar2.

function refreshHeaderTable () {
  // let href = window.location.pathname.split('/').filter(Boolean)[1];
  // console.log(href)
  // let _token = $("#_token").val();
  //
  // $.ajax({
  //   url: "{!! url('getheadertable') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token : _token,
  //     href
  //   },
  //   success: function(res) {
  //       console.log('======xxxxx==========')
  //     console.log(res)
  //     console.log(JSON.parse(res.isshown))
  //     console.log(JSON.parse(res.headertableheader))
  //     console.log(JSON.parse(res.headertablevalue))
  //     console.log(JSON.parse(res.isnumeric))
  //     xisshown = JSON.parse(res.isshown)
  //     xheadertableheader = JSON.parse(res.headertableheader)
  //     xheadertablevalue = JSON.parse(res.headertablevalue)
  //     xisnumeric = JSON.parse(res.isnumeric)
      let rowTable = ''
      console.log('len' , xheadertableheader.length)
      xheadertableheader.forEach((item, i) => {
        console.log(i)
        rowTable +=  `<tr>`
        rowTable += `<td class="text-center">`
        if (i != 0) {
          console.log("button down")
          rowTable +=  `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(0 , ${i})"><i class="bi bi-arrow-up"></i></button>`
        } else {
            rowTable +=  `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-up" disabled></i></button>`
        }
        if (i != xheadertableheader.length - 1 ) {

            console.log("button up")
            rowTable += `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(1 , ${i})"><i class="bi bi-arrow-down"></i></button>`
        } else {
            rowTable += `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-down" disabled></i></button>`
        }

        rowTable += `</td>`
          rowTable+= `  <td>${item}</td>
            <td class="text-center"><input class="" type="checkbox" value="" onchange='onclickcheckboxheadertable(${i})' id="headertable_checkbox${i}"></td>





        `
        rowTable += `</tr>`
      });



      document.getElementById("tabel_data_headertable").innerHTML = rowTable
      console.log(xisshown)
      xheadertableheader.forEach((item, i) => {
        console.log(xisshown[i])
        console.log(Number(xisshown[i]))
        if (Number(xisshown[i])) {

          document.getElementById(`headertable_checkbox${i}`).checked = true
        }

      });
      // $("#formHeaderTable").modal('toggle')



  //   }
  // })

}

function refreshHeaderTable () {
  // let href = window.location.pathname.split('/').filter(Boolean)[1];
  // console.log(href)
  // let _token = $("#_token").val();
  //
  // $.ajax({
  //   url: "{!! url('getheadertable') !!}",
  //   type: "post",
  //   async: false,
  //   data: {
  //     _token : _token,
  //     href
  //   },
  //   success: function(res) {
  //       console.log('======xxxxx==========')
  //     console.log(res)
  //     console.log(JSON.parse(res.isshown))
  //     console.log(JSON.parse(res.headertableheader))
  //     console.log(JSON.parse(res.headertablevalue))
  //     console.log(JSON.parse(res.isnumeric))
  //     xisshown = JSON.parse(res.isshown)
  //     xheadertableheader = JSON.parse(res.headertableheader)
  //     xheadertablevalue = JSON.parse(res.headertablevalue)
  //     xisnumeric = JSON.parse(res.isnumeric)
      let rowTable = ''
      console.log('len' , xheadertableheader.length)
      xheadertableheader.forEach((item, i) => {
        console.log(i)
        rowTable +=  `<tr>`
        rowTable += `<td class="text-center">`
        if (i != 0) {
          console.log("button down")
          rowTable +=  `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(0 , ${i})"><i class="bi bi-arrow-up"></i></button>`
        } else {
            rowTable +=  `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-up" disabled></i></button>`
        }
        if (i != xheadertableheader.length - 1 ) {

            console.log("button up")
            rowTable += `<button class="btn btn-primary btn-sm" title="" onclick="buttonChangeOrder(1 , ${i})"><i class="bi bi-arrow-down"></i></button>`
        } else {
            rowTable += `<button class="btn btn-secondary btn-sm" title="" onclick=""><i class="bi bi-arrow-down" disabled></i></button>`
        }

        rowTable += `</td>`
          rowTable+= `  <td>${item}</td>
            <td class="text-center"><input class="" type="checkbox" value="" onchange='onclickcheckboxheadertable(${i})' id="headertable_checkbox${i}"></td>





        `
        rowTable += `</tr>`
      });



      document.getElementById("tabel_data_headertable").innerHTML = rowTable
      console.log(xisshown)
      xheadertableheader.forEach((item, i) => {
        console.log(xisshown[i])
        console.log(Number(xisshown[i]))
        if (Number(xisshown[i])) {

          document.getElementById(`headertable_checkbox${i}`).checked = true
        }

      });
      // $("#formHeaderTable").modal('toggle')



  //   }
  // })

}

function buttonHeaderTable (urut = 1) {
  urutHeaderTable = urut
  console.log(urut)
    let href = window.location.pathname.split('/').filter(Boolean)[1];
    console.log(href)
    let _token = $("#_token").val();

    $.ajax({
      url: "{!! url('getheadertable') !!}",
      type: "post",
      async: false,
      data: {
        _token : _token,
        href
      },
      success: function(res) {
          console.log('======xxxxx==========')
        console.log(res)

        // if (res.isparsed == 0) {
        //   xisshown = JSON.parse(res.isshown)
        //   xheadertableheader = JSON.parse(res.headertableheader)
        //   xheadertablevalue = JSON.parse(res.headertablevalue)
        //   xisnumeric = JSON.parse(res.isnumeric)
        //
        //
        // } else {
        if (urutHeaderTable == 1) {
          xisshown = res.isshown
         xheadertableheader = res.headertableheader
         xheadertablevalue = res.headertablevalue
         xisnumeric = res.isnumeric

        } else {
          xisshown = res.isshown2
          xheadertableheader = res.headertableheader2
          xheadertablevalue = res.headertablevalue2
          xisnumeric = res.isnumeric2

        }


        // }
        // console.log(JSON.parse(res.isshown))
        // console.log(JSON.parse(res.headertableheader))
        // console.log(JSON.parse(res.headertablevalue))
        // console.log(JSON.parse(res.isnumeric))

        refreshHeaderTable()
        $("#formHeaderTable").modal('toggle')

      }
    })



}
END BACKUP */


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
      document.getElementById("input_add_add_discrp").value = '0.00'
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
        <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
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
          document.getElementById("input_add_add_harga").value = '0.00'
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

  // FOC diambil dari master barang, jadi tidak punya asal PR maupun SO: keempat field
  // referensi ini dikosongkan supaya tidak ada sisa nilai dari pilihan sebelumnya.
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0
  document.getElementById("input_add_noso").value = '-'
  document.getElementById("input_add_nopocust").value = '-'

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
          <td class="text-right">${Number(item.Xharga) ? parseFloat(item.Xharga).toFixed(2) : '0.00'}</td>
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
          document.getElementById("input_add_add_harga").value = '0.00'
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
  tempStockAdd = tempAddAdd

  let currentQntPO = 0

  cekQntPR = tempStockAdd.Qnt

  cekQntPO = tempStockAdd.QntPO
  cekQntSisa = tempStockAdd.SisaPPL

  // Nilai input qty sudah berpemisah ribuan (autoNumeric), jadi koma harus dibuang
  // sebelum dibandingkan - sama seperti purchaseOrderNonStock.blade.php.
  currentQntPO = (document.getElementById("input_add_add_qty").value || '0').replace(/,/g, '')

  console.log(currentQntPO + ' current qnt PO')
  console.log(currentQntPO,'=============================PO')
  console.log(cekQntSisa,'=============================sisa')
  if (Number(currentQntPO) > Number(cekQntSisa)) {
    alertify.warning('Qty PO Tidak boleh melebihi Qty Sisa')
    document.getElementById("input_add_add_qty").value = '0.00'
  }

}

// Pilih barang dari daftar PR (dropdown "+ Dari" = PR).
function buttonAddAddPickBarangNonFOC (index , pEdit = 0) {
  let _token  = $("#_token").val()

  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)

  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddAdd.SisaPPL).toFixed(2))

  // Nomor + urut PR-nya disimpan di dua field tersembunyi ini; itulah yang dikirim
  // sebagai NoPPL/UrutPPL ke sp_PO dan berakhir di kolom noppl/urutppl dbpodet.
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut

  // Field "No. PR" di layar hanya menampilkan asal barangnya. Nilai ini TIDAK ikut
  // tersimpan sebagai Noso - submitAddAdd()/submitAddEdit() hanya mengirimkannya kalau
  // sumbernya SO. No. PO Cust juga khusus milik SO, jadi di sini dikosongkan.
  document.getElementById("input_add_noso").value = tempAddAdd.NoBukti
  document.getElementById("input_add_nopocust").value = '-'

  let selectOption = ''

  if (poSumberBarang() === 'PR') {

      selectOption += `<option value=${tempAddAdd.NoSat} selected>${tempAddAdd.NoSat}-${tempAddAdd.Sat}(${tempAddAdd.Isi})</option>`

  } else {
    if (tempSatuanBarang[0].SAT1) {
      selectOption += `<option value=1 selected>1-${tempSatuanBarang[0].SAT1}(${tempSatuanBarang[0].ISI1})</option>`
    }
    if (tempSatuanBarang[0].SAT2) {
      selectOption += `<option value=2>2-${tempSatuanBarang[0].SAT2}(${tempSatuanBarang[0].ISI2})</option>`
    }
    if (tempSatuanBarang[0].SAT3) {
      selectOption += `<option value=3>3-${tempSatuanBarang[0].SAT3}(${tempSatuanBarang[0].ISI3})</option>`
    }
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
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
        document.getElementById("input_add_add_hargaAwal").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
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

// Pilih barang dari daftar SO (dropdown "+ Dari" = SO).
function buttonAddAddPickBarangNonFOCPlus (index , pEdit = 0) {
  let _token  = $("#_token").val()
  console.log(dataAddAddListItem[index])
  tempAddAdd = dataAddAddListItem[index]

  cekSatuanBarang(tempAddAdd.KodeBrg)
  // return
  document.getElementById("input_add_add_kodebarang").value = tempAddAdd.KodeBrg
  document.getElementById("input_add_add_namabarang").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_namabarangasli").value = tempAddAdd.NamaBrg
  document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddAdd.Qnt).toFixed(2))

  // Sama seperti alur PR: nomor + urut SO-nya yang berakhir di kolom noppl/urutppl.
  document.getElementById("input_add_add_noPPL").value = tempAddAdd.NoBukti
  document.getElementById("input_add_add_urutPPL").value = tempAddAdd.Urut

  // Hanya pada alur SO kedua field ini benar-benar ikut tersimpan: No. SO ke kolom Noso
  // dan No. PO Cust ke kolom NOPOCUST. NoPesanan diambil dari header SO-nya - itu
  // sebabnya listBarangSOAll ikut men-join DBSO.
  document.getElementById("input_add_noso").value = tempAddAdd.NoBukti
  document.getElementById("input_add_nopocust").value = tempAddAdd.NoPesanan ? tempAddAdd.NoPesanan : '-'

  // poNosoHeader/poNoPoCustHeader ikut diperbarui di sini, bukan hanya field di layar:
  // itulah yang dikirim submitAddAdd()/submitAddEdit() saat item PR/FOC berikutnya
  // disimpan, supaya header dbpo tidak balik ke nilai lama.
  poNosoHeader = tempAddAdd.NoBukti
  poNoPoCustHeader = tempAddAdd.NoPesanan ? tempAddAdd.NoPesanan : '-'


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
  document.getElementById("input_add_add_nosat").value = tempAddAdd.NoSat
  console.log(tempAddAdd.Nosat)
  if (tempAddAdd.NoSat == 1) {
    document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddAdd.SisaPPL).toFixed(2))

  } else {
    document.getElementById("input_add_add_qty").value = formatAngka(parseFloat(tempAddAdd.Sisa2PPL).toFixed(2))

  }

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
          <td>${formatAngka(item.QNT)}</td>
          <td>${item.SATUAN}</td>
          <td>${item.KODEVLS}</td>
          <td>${item.KURS}</td>
          <td class="text-right">${Number(item.HARGA) ? parseFloat(item.HARGA).toFixed(2) : '0.00'}</td>
          <td>${item.DISCRP}</td>
          <td class="text-right">${Number(item.HrgNetto) ? parseFloat(item.HrgNetto).toFixed(2) : '0.00'}</td>
        </tr>`
      });

      document.getElementById("tabel_data_add_harga_terakhir").innerHTML = rowTable

      if (res.length && Number(res[0].HARGA)) {
        document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(res[0].HARGA).toFixed(2))
      } else {
        if (Number(tempAddAdd.Hrg1_1)) {
          document.getElementById("input_add_add_harga").value = formatAngka(parseFloat(tempAddAdd.Hrg1_1).toFixed(2))
        } else {
          document.getElementById("input_add_add_harga").value = '0.00'
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
  document.getElementById("input_add_namasupplier").value = nama
  document.getElementById("input_add_alamatsupplier").value = alamat
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_hari").value = hari
  document.getElementById("input_add_kodealamatkirim").value = 'GMPL'
  let itemGudangDefault = listAlamatKirim.find(item => item.KODEGDG === 'GMPL')
  document.getElementById("input_add_alamatkirim").value = itemGudangDefault ? itemGudangDefault.Alamat : ''
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'

  if (hari == 0 ){
    selectTipeBayar = `<option value=0 selected>Tunai</option>
    <option value=1>Kredit</option>`
  }
  else if (hari != 0){
    selectTipeBayar = `
    <option value=0 >Tunai</option>
    <option value=1 selected>Kredit</option>`
  }

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
    onChangeHeader('NoAlamatKirim' , itemX.KODEGDG)
    onChangeHeader('AlamatKirim' , itemX.Alamat)
  }

  document.getElementById("input_add_kodealamatkirim").value = itemX.KODEGDG
  document.getElementById("input_add_alamatkirim").value = itemX.Alamat
  buttonAddListBatal()

}

function buttonAddPickNoSO (kode, nama) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_noso").value = kode
  document.getElementById("input_add_nopocust").value = nama

  buttonAddListBatal()
}

function buttonAddPickLokasiPenerima (kode, nama ) {
  console.log('buttonAddPickLokasiPenerima')
  console.log(kode,nama)
  if (tipeform == 'edit') {
    onChangeHeader('KODEKEBUN' , kode)

  }
  document.getElementById("input_add_kodeekspedisi").value = kode
  document.getElementById("input_add_ekspedisi").value = nama

  buttonAddListBatal()
  document.getElementById("input_add_kodeekspedisi").scrollIntoView();
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

// 'hide', bukan 'toggle'. Fungsi ini selalu berniat MENUTUP modal browse, dan sekarang
// dipanggil juga dari jalur auto-pick (buttonAddAddPickBarang* dijalankan tanpa modal
// pernah dibuka) - 'toggle' pada kondisi itu justru akan membuka modalnya.
function buttonAddListBatal () {
  $('.showhidemodalbodyadd').hide();
  $('#modalBodyAddMain').show();

  $("#form").modal('hide')
}

function cleanFormAddAdd () {
  document.getElementById("input_add_add_kodebarang").value = ''
  document.getElementById("input_add_add_namabarang").value = ''
  document.getElementById("input_add_add_namabarangasli").value = ''
  document.getElementById("input_add_add_nopnwpo").value = '-'
  document.getElementById("input_add_add_qty").value = '0.00'
  document.getElementById("input_add_add_nosat").innerHTML = '<option value=0 selected>Pilih Satuan</option>'
  // document.getElementById("input_add_add_satuanproduk").value = ''
  document.getElementById("input_add_add_harga").value = '0.00'
  // document.getElementById("input_add_add_disc").value = '0.00'
  document.getElementById("input_add_add_discrp").value = '0.00'
  document.getElementById("input_add_add_discpersen1").value = '0.00'
  document.getElementById("input_add_add_discpersen2").value = '0.00'
  document.getElementById("input_add_add_discpersen3").value = '0.00'
  document.getElementById("input_add_add_hargaAwal").value = '0.00'
  // document.getElementById("input_add_add_tambahkepo").value = 0

  document.getElementById("input_add_add_keteranganbarang").value = ''

  // Field referensi milik item yang tadi dipilih - kosongkan supaya memulai entri item
  // baru tidak menampilkan sisa asal barang dari item sebelumnya sebelum user memilih
  // barang baru lewat modal browse.
  document.getElementById("input_add_add_noPPL").value = ''
  document.getElementById("input_add_add_urutPPL").value = 0
  document.getElementById("input_add_noso").value = '-'
  document.getElementById("input_add_nopocust").value = '-'

  // Sisa data barang yang tadi dipilih (dipakai submitAddAdd/buttonAddEditItem dan
  // pengisi dropdown satuan) - dikosongkan supaya tidak tertinggal dari item sebelumnya.
  // Aman: keduanya selalu ditulis ulang oleh fungsi pemilih barang (buttonAddAddPickBarangXXX)
  // sebelum dibaca lagi di tempat lain.
  tempAddAdd = {}
  tempSatuanBarang = []

  // Riwayat harga terakhir milik barang sebelumnya - kembalikan ke baris placeholder
  // bawaan (lihat markup <tbody id="tabel_data_add_harga_terakhir"> di atas).
  document.getElementById("tabel_data_add_harga_terakhir").innerHTML =
    '<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>'

  syncOutstandingDariFOC()

}

function lockFormAdd () {
  document.getElementById("input_add_tipeppn").disabled = true
  document.getElementById("input_add_pembayaran").disabled = true
  document.getElementById("input_add_nopocust").disabled = true
  document.getElementById("input_add_noso").disabled = true
  document.getElementById("input_add_keterangan").disabled = true
  document.getElementById("input_add_kodealamatkirim").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_tanggalkirim").disabled = true
  document.getElementById("input_add_hari").disabled = true
  document.getElementById("input_add_draftpo").disabled = true
  document.getElementById("input_add_add_discpersen1").disabled = true
  document.getElementById("input_add_add_discpersen2").disabled = true
  document.getElementById("input_add_add_discpersen3").disabled = true
  document.getElementById("input_add_add_foc").disabled = true

  document.getElementById("input_add_kodesupplier").disabled = true
  document.getElementById("buttonAddListPelanggan").disabled = true
  document.getElementById("input_add_valas").disabled = true

  document.getElementById("buttonAddListPelanggan").hidden = true
  document.getElementById("buttonAddListSales").hidden = true
  document.getElementById("buttonAddListPIC").hidden = true
  document.getElementById("buttonAddListLokasiPenerima").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  document.getElementById("buttonAddListBackOffice").hidden = true
  // document.getElementById("buttonAddListNoSo").hidden = true
  document.getElementById("buttonTambahItem").hidden = true

  document.getElementById("input_add_disc").disabled = true
  document.getElementById("input_add_discrp").disabled = true
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
  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_nopocust").disabled = false
  document.getElementById("input_add_noso").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  document.getElementById("input_add_kodealamatkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false
  document.getElementById("input_add_add_discpersen1").disabled = false
  document.getElementById("input_add_add_discpersen2").disabled = false
  document.getElementById("input_add_add_discpersen3").disabled = false
  document.getElementById("input_add_add_foc").disabled = false

  // Supplier adalah identitas PO yang sudah tersimpan - saat koreksi (mode edit) tidak
  // boleh diganti, jadi kode supplier dan tombol browse-nya tetap terkunci di sini.
  // Hanya mode add yang boleh memilih supplier.
  let bolehPilihSupplier = (tipeform !== 'edit')
  document.getElementById("input_add_kodesupplier").disabled = !bolehPilihSupplier
  document.getElementById("buttonAddListPelanggan").disabled = !bolehPilihSupplier
  document.getElementById("input_add_valas").disabled = false

  document.getElementById("buttonAddListPelanggan").hidden = false
  document.getElementById("buttonAddListSales").hidden = false
  document.getElementById("buttonAddListPIC").hidden = false
  document.getElementById("buttonAddListLokasiPenerima").hidden = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  document.getElementById("buttonAddListBackOffice").hidden = false
  // document.getElementById("buttonAddListNoSo").hidden = false
  document.getElementById("buttonTambahItem").hidden = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false
}

function cleanFormAdd () {

  document.getElementById("input_add_nobukti").value = ''
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_tanggalkirim").valueAsDate = new Date()
  document.getElementById("input_add_kodesupplier").value = ''
  document.getElementById("input_add_namasupplier").value = ''
  document.getElementById("input_add_alamatsupplier").value = ''
  document.getElementById("input_add_kodealamatkirim").value = 'GMPL'
  document.getElementById("input_add_alamatkirim").value = 'Pergudangan Mangkupalas Centre, Jl. Ampera RT.22 Kel.Simpang Pasir Mangkupalas, Samarinda Seberang. '
  document.getElementById("input_add_kodepic").value = ''
  document.getElementById("input_add_namapic").value = ''
  document.getElementById("input_add_kodeekspedisi").value = '-'
  document.getElementById("input_add_ekspedisi").value = '-'
  document.getElementById("input_add_keterangan").value = ''
  document.getElementById("input_add_valas").value = ''
  document.getElementById("input_add_kurs").value = ''
  document.getElementById("input_add_nopocust").value = '-'
  document.getElementById("input_add_noso").value = '-'
  // PO baru belum punya header tersimpan sama sekali - lihat catatan di deklarasi
  // poNosoHeader/poNoPoCustHeader soal kenapa keduanya wajib direset di sini.
  poNosoHeader = '-'
  poNoPoCustHeader = '-'
  document.getElementById("input_add_kodebackoffice").value = ''
  document.getElementById("input_add_namabackoffice").value = ''
  document.getElementById("input_add_tipeppn").value = 0
  document.getElementById("input_add_pembayaran").value = 0
  document.getElementById("input_add_kodesales").value = ''
  document.getElementById("input_add_namasales").value = ''
  document.getElementById("input_add_hari").value = 0
  document.getElementById("input_add_draftpo").value = 0

  document.getElementById("input_add_tipeppn").disabled = false
  document.getElementById("input_add_pembayaran").disabled = false
  document.getElementById("input_add_nopocust").disabled = false
  document.getElementById("input_add_noso").disabled = false
  document.getElementById("input_add_keterangan").disabled = false
  document.getElementById("input_add_kodealamatkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_tanggalkirim").disabled = false
  document.getElementById("input_add_hari").disabled = false
  document.getElementById("input_add_draftpo").disabled = false

  document.getElementById("buttonAddListPelanggan").disabled = false
  document.getElementById("buttonAddListSales").disabled = false
  document.getElementById("buttonAddListPIC").disabled = false
  document.getElementById("buttonAddListLokasiPenerima").disabled = false
  document.getElementById("buttonAddListBackOffice").disabled = false

  document.getElementById("input_add_disc").disabled = false
  document.getElementById("input_add_discrp").disabled = false

  document.getElementById("input_add_total").value = '0.00'
  document.getElementById("input_add_disc").value = '0.00'
  document.getElementById("input_add_discrp").value = '0.00'
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

  let akses = $("#akses_iskoreksi").val();

  if (!Number(akses)) {
    alertify.warning('No access')
    return
  }
  let _token  = $("#_token").val()
  let oto = 1

  $.ajax({
    url: "{!! url('pocekotorisasi') !!}",
    type: "post",
    async: false,
    data: {
      _token,
      nobukti: NOBUKTI
    },
    success: function(res) {
      console.log(res)
      oto = res[0].isOtorisasi
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

  document.getElementById("input_add_tanggal").value = formatDate(now)

  $('#page1').hide();
  $('#page2').show();
  $('#modalBodyAddMainHeader').show();

}

function buttonCloseForm () {
  $('#page4').hide();
  $('#page3').hide();
  $('#page2').hide();
  $('#page1').show();

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
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('pogetdetail') !!}",
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

          // Header dbpo hanya boleh dibaca di sini (sesudah pogetdetail), sebagai nilai
          // yang SEDANG BERLAKU - lihat catatan lengkap di deklarasi poNosoHeader.
          poNosoHeader = dataHeaderAdd.NOSO ? dataHeaderAdd.NOSO : '-'
          poNoPoCustHeader = dataHeaderAdd.Nopesanan ? dataHeaderAdd.Nopesanan : '-'

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-center">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-center">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
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
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Alamat1
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Catatan
          document.getElementById("input_add_kodealamatkirim").value = dataHeaderAdd.Kodegdg
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.ALamatGdg
          document.getElementById("input_add_kodeekspedisi").value = dataHeaderAdd.KodeExp
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.NamaExp
          // input_add_noso/input_add_nopocust TIDAK diisi dari header di sini - keduanya
          // sekarang menampilkan asal barang milik ITEM yang sedang dibuka di form add
          // item (diisi buttonAddAddPickBarangXXX / buttonAddEditItem / cleanFormAddAdd),
          // bukan lagi nilai header. Lihat catatan di deklarasi poNosoHeader.
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tanggalkirim").value = formatDate(dataHeaderAdd.TglKirim)
          // Total = jumlah Subtotal (Total) seluruh item.
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.Disc).toFixed(2)
          document.getElementById("input_add_discrp").value = formatAngka(parseFloat(dataHeaderAdd.TotDiskon).toFixed(2))
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))

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
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Qty</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sat</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Harga</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Actions</th>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function refreshDataTableEdit (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('pogetdetail') !!}",
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

          // Lihat catatan yang sama di refreshDataTableAdd() soal poNosoHeader/
          // poNoPoCustHeader dan kenapa input_add_noso/input_add_nopocust tidak lagi
          // diisi dari header di sini.
          poNosoHeader = dataHeaderAdd.NOSO ? dataHeaderAdd.NOSO : '-'
          poNoPoCustHeader = dataHeaderAdd.Nopesanan ? dataHeaderAdd.Nopesanan : '-'

          let rowTable = ""
          dataTableAdd.forEach((item, i) => {

            rowTable +=
            `<tr>
              <td>${item.KodeBrg}</td>
              <td>${item.NamaBrg}</td>
              <td class="text-center">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-center">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
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
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Alamat1
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Catatan
          document.getElementById("input_add_kodealamatkirim").value = dataHeaderAdd.Kodegdg
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.ALamatGdg
          document.getElementById("input_add_kodeekspedisi").value = dataHeaderAdd.KodeExp
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.NamaExp
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tanggalkirim").value = formatDate(dataHeaderAdd.TglKirim)
          // Total = jumlah Subtotal (Total) seluruh item.
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.Disc).toFixed(2)
          document.getElementById("input_add_discrp").value = formatAngka(parseFloat(dataHeaderAdd.TotDiskon).toFixed(2))
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))

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
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Qty</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sat</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Harga</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Actions</th>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function refreshDataTableDetail (NOBUKTI) {

  console.log('refreshDataTableAdd' , NOBUKTI)
  if (!NOBUKTI) {

    // if(!dataTableAdd.length) {
      let rowTable = `<tr>
      <td class="text-center" colspan="9">Belum ada barang</td>
      </tr>`
    // }
    document.getElementById("tabel_data_add").innerHTML = rowTable
  } else {

    let _token  = $("#_token").val()

    $.ajax({
      url: "{!! url('pogetdetail') !!}",
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
              <td class="text-center">${item.Qnt ? formatAngka(item.Qnt) : '0.00'}</td>
              <td class="text-center">${item.Satuan}</td>
              <td class="text-center">${item.Harga ? formatAngka(parseFloat(item.Harga).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.DISCTOT ? formatAngka(parseFloat(item.DISCTOT).toFixed(2)) : '0.00'}</td>
              <td class="text-center">${item.Total ? formatAngka(parseFloat(item.Total).toFixed(2)) : '0.00'}</td>
              <td>${item.NoPPL ? item.NoPPL : ''}</td>
            </tr>`
          });

          if(!dataTableAdd.length) {
            rowTable = `<tr>
            <td class="text-center" colspan="9">Belum ada barang</td>
            </tr>`
          }
          document.getElementById("tabel_data_add").innerHTML = rowTable

          document.getElementById("input_add_nobukti").value = dataHeaderAdd.NoBukti
          document.getElementById("input_add_namasupplier").value = dataHeaderAdd.NamaCustSupp
          document.getElementById("input_add_kodesupplier").value = dataHeaderAdd.KodeSupp
          document.getElementById("input_add_alamatsupplier").value = dataHeaderAdd.Alamat1
          document.getElementById("input_add_valas").value = dataHeaderAdd.KodeVls
          document.getElementById("input_add_kurs").value = dataHeaderAdd.Kurs
          document.getElementById("input_add_nopocust").value = dataHeaderAdd.Nopesanan
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Catatan
          document.getElementById("input_add_kodealamatkirim").value = dataHeaderAdd.Kodegdg
          document.getElementById("input_add_alamatkirim").value = dataHeaderAdd.ALamatGdg
          document.getElementById("input_add_kodeekspedisi").value = dataHeaderAdd.KodeExp
          document.getElementById("input_add_ekspedisi").value = dataHeaderAdd.NamaExp
          document.getElementById("input_add_noso").value = dataHeaderAdd.NOSO
          document.getElementById("input_add_hari").value = dataHeaderAdd.Hari
          document.getElementById("input_add_keterangan").value = dataHeaderAdd.Keterangan
          document.getElementById("input_add_pembayaran").value = dataHeaderAdd.TipeBayar
          document.getElementById("input_add_tipeppn").value = dataHeaderAdd.PPN
          document.getElementById("input_add_tanggal").value = formatDate(dataHeaderAdd.Tanggal)
          document.getElementById("input_add_tanggalkirim").value = formatDate(dataHeaderAdd.TglKirim)
          // Total = jumlah Subtotal (Total) seluruh item.
          document.getElementById("input_add_total").value = formatAngka(dataTableAdd.reduce((jml, item) => jml + (parseFloat(item.Total) || 0), 0).toFixed(2))
          document.getElementById("input_add_disc").value = parseFloat(dataHeaderAdd.Disc).toFixed(2)
          document.getElementById("input_add_discrp").value = formatAngka(parseFloat(dataHeaderAdd.TotDiskon).toFixed(2))
          document.getElementById("input_add_dpp").value = formatAngka(parseFloat(dataHeaderAdd.TotDPP).toFixed(2))
          document.getElementById("input_add_ppn").value = formatAngka(parseFloat(dataHeaderAdd.TotPPN).toFixed(2))
          document.getElementById("input_add_grandtotal").value = formatAngka(parseFloat(dataHeaderAdd.TotNet).toFixed(2))

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
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Qty</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sat</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Harga</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Diskon</th>
                  <th style="padding: 4px 12px;" scope="col" class="text-center">Sub Total</th>
                  <th style="padding: 4px 12px;" scope="col">No. PR</th>
                </tr>
            </tr>`

          document.getElementById("tabel_data_header").innerHTML = rowHeader
  }
}

function submitPrint (nobukti) {

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
                      <div class="pb-1" style="width: 45%">No</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].nobukti+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Tanggal</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalOnly+`</div>
                    </div>
              <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Batas Tgl Kirim</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+tanggalKirimOnly+`</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Pembayaran</div>
                      <div class="pb-1" style="width: 5%">:</div>
                      <div class="pb-1" style="width: 50%">`+dataPrint[0].HARI+` Hari</div>
                    </div>
                    <div style="display: flex; width: 100%">
                      <div class="pb-1" style="width: 45%">Mata Uang</div>
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
                    <td class="text-center" style="width: 35%">NAMA BARANG</td>
                    <td class="text-center" style="width: 15%">KODE BRG</td>
                    <td class="text-center" style="width: 10%">MERK</td>
                    <td class="text-center" style="width: 8%">QTY</td>
                    <td class="text-center" style="width: 5%">SAT</td>
                    <td class="text-center" style="width: 10%">HARGA</td>
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
      <h3 class="m-0 pb-2">Dikirim Ke:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">CV. SINAR MAHAKAM LESTARI</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">`+dataPrint[0].AlamatGudang+`</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Dikirim Via:</h3>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${dataPrint[0].Expedisi ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <div class="pb-1" style="width: 100%">${dataPrint[0].almkirim ?? ''}</div>
    </div>
    <div style="display: flex; width: 100%">
      <h3 class="m-0 pb-2">Semua Dokumen Asli Dikirim Ke:</h3>
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
      <div style="width:50%; margin-top:15px; margin-left:-25px; float:left; text-align:center; font-size:10px;">
       <div>Disetujui Oleh</div>

       <div style="height:60px;"></div>

       <div style="font-size:10px;">
        `+dataPrint[0].otouser+`
       </div>
     <div style="font-size:10px;">
        ELECTRONICALLY APPROVED
     </div>
      </div>

        <div style="width:50%; float:left; font-size:10px; margin-top:-10px; margin-left:-15px;">
        <table style="width:100%; border-collapse: collapse;">

        <!-- HEADER -->
        <tr style="height:20px;">
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Konfirmasi Supplier
          </td>
          <td style="border:1px solid black; text-align:center; font-size:10px;">
            Estimasi Tgl. Kirim
          </td>
        </tr>

        <!-- ROW NAMA -->
        <tr style="height:50px;">
          <td style="border:1px solid black; width:50%; font-size:10px; vertical-align: bottom;">
            Nama
          </td>

          <!-- KOLOM KANAN -->
          <td rowspan="2" style="border:1px solid black; height:60px; position:relative;">

            <div style="position:absolute; bottom:5px; left:6px; font-style:italic; font-size:10px; vertical-align: bottom;">
              *wajib isi
            </div>

          </td>
        </tr>

        <!-- ROW TANGGAL -->
        <tr style="height:20px;">
          <td style="border:1px solid black; font-size:10px;">
            Tanggal
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
  console.log(i)

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
        let Choice = "D"

        let NoBukti = dataDelete.NoBukti
        let Urut = dataDelete.Urut

        $.ajax({
          url: "{!! url('pospadd') !!}",
          type: "post",
          async: false,
          data: {
            _token,
            Choice,
            NoBukti,
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
            Urut,
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
            // Sama seperti submitAddAdd()/submitAddEdit(): Noso/NOPOCUST kolom header,
            // bukan per baris. Mengirim '' di sini akan menghapus No. SO di header
            // walaupun item yang dihapus bukan yang bersumber dari SO - lihat catatan di
            // deklarasi poNosoHeader.
            Noso: poNosoHeader,
            Jmlrecord: 0,
            NOPOCUST: poNoPoCustHeader,
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

            refreshDataTableAdd(NoBukti)

            alertify.success('Berhasil menghapus item')
            poSegarkanOutstanding()

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

    if ($.fn.DataTable.isDataTable('#tabel_add_list_barangall')) { $('#tabel_add_list_barangall').DataTable().destroy() }

  }

}

// Ambil hasil pencarian master barang (FOC) dari server ke dataAddAddListItem dan
// kembalikan jumlah barisnya. Dipisah dari perenderannya supaya buttonAddAddListBarang
// bisa memakai jumlah itu untuk memutuskan auto-pick SEBELUM modal dibuka.
// Tetap async:false, mengikuti seluruh AJAX di halaman ini.
function poMuatBarangFOC (search) {
  let _token = $("#_token").val()
  let jml = 0

  document.getElementById("tabel_data_add_list_barang_foc").innerHTML = '<tr><td class="text-center" colspan="4">Mencari data...</td></tr>'

  $.ajax({
    url: "{!! url('polistbarangfoc') !!}",
    type: "get",
    async: false,
    data: {
      _token,
      search
    },
    success: function (res) {
      dataAddAddListItem = res
      jml = res.length
    },
    error: function (err) {
      console.log(err)
      dataAddAddListItem = []
      jml = 0
      alertify.warning('Terjadi kesalahan silahkan refresh browser')
    }
  })

  return jml
}

// Render isi dataAddAddListItem ke tabel browse FOC.
function poRenderBarangFOC (cari) {
  if (!dataAddAddListItem.length) {
    poTabelBarangFOCInit('', cari, 'Tidak ada data')
    return
  }

  let rowTable = ``
  dataAddAddListItem.forEach((item, i) => {
    rowTable += `
    <tr class="pick-row" onclick="buttonAddAddPickBarangFOCPlus(${i})">
      <td style="">${item.Kodebrg}</td>
      <td style="">${item.NamaBrg}</td>
      <td style="">${item.partNumber}</td>
      <td style="">${item.NamaMerk}</td>
    </tr>`
  });

  poTabelBarangFOCInit(rowTable, cari)
}

/* Browse FOC (Master Barang) - tampilan disamakan dengan modal pemilih menu Kas (picker-kas):
   dropdown "Tampilkan" + kotak Search bawaan DataTables. Datanya tetap dicari ke server
   (seluruh master barang terlalu besar untuk dimuat sekaligus), jadi kotak Search DataTables
   itu sekaligus menjadi kotak cari server: tekan Enter -> searchBarangFOC(). Sambil mengetik,
   DataTables tetap menyaring hasil yang sedang tampil. */
function poTabelBarangFOCInit (rowTable, cari, pesanKosong) {
  document.getElementById("tabel_data_add_list_barang_foc").innerHTML = rowTable || ''
  pickerKasInit('tabel_add_list_barang_foc', {
    search: { search: cari || '' },
    language: {
      lengthMenu: 'Tampilkan _MENU_',
      emptyTable: pesanKosong || 'Ketik kode / nama barang di kotak Search, lalu tekan Enter',
      zeroRecords: 'Tidak ada data yang cocok dengan pencarian'
    }
  })
  $('#tabel_add_list_barang_foc_filter input')
    .off('keypress.poFOC')
    .on('keypress.poFOC', searchBarangFOC)
    .trigger('focus')
}

// Pencarian server-side untuk browse FOC (Master Barang) - mengikuti pola searchBarangAll
// di pembelianpermintaanagen/nonagen: hanya jalan saat Enter, cari lewat kode+nama barang.
// Di dalam modal tidak ada auto-pick: satu hasil tetap ditampilkan sebagai satu baris
// yang harus diklik, sama seperti searchBarangAll di pembelianpermintaanagen.
function searchBarangFOC (e) {
  if (e.which != 13) { return }

  // Dipanggil dari kotak Search DataTables (lihat poTabelBarangFOCInit), bukan lagi
  // #input_search_barang_foc.
  let search = $(e.target).val().trim()

  if (!search) {
    poTabelBarangFOCInit('', '')
    return
  }

  poMuatBarangFOC(search)
  poRenderBarangFOC(search)
}

function generateInputNumber (id , style, classes, onchange) {
    return `<input type="text" id="${id}" onchange="${onchange}" style="${style}" data-a-sign="" data-a-dec="." data-a-sep="," class="form-control text-right input-partial-number ${classes}">`
  }

  function formatAngkaX (angka) {
    if (!angka) {
      return '0.00'
    } else {
      return formatAngka(parseFloat(angka).toFixed(2))
    }

  }

  function formatAngkaParse (angka) {

    return parseFloat(angka).toFixed(2)
  }

  function formatAngkaVal (angka) {
    return Number(String(angka == null ? '' : angka).split(',').join('')) || 0
  }


  function formatAngka (angkaString) {
  // console.log('formatAngka' , angkaString);
  if (angkaString === null || angkaString === undefined || angkaString === '') {
    return '0.00'
  }
  if (isNaN(Number(angkaString))) {
    return '0.00'
  }
  angkaString = parseFloat(angkaString).toFixed(2).toString()
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

// function formatAngka (angkaString) {
//   // console.log('formatAngka' , angkaString);
//   let tempAngka = angkaString.split('.')
//   let temp1 = ''
//   for (let i = 0; i < tempAngka[0].length; i++) {
//     if (i != 0 && i % 3 == 0) {
//       temp1 = ',' + temp1
//     }
//     temp1 = tempAngka[0][tempAngka[0].length - i -1] + temp1
//     // console.log(i, temp1)
//   }
//   temp1 += '.' + tempAngka[1]
//   return temp1
// };

function reverseCalculateDiscPercent() {
  let harga = formatAngkaVal($('#input_add_add_harga').val()) || 0;
  let discRp = formatAngkaVal(document.getElementById('input_add_add_discrp').value) || 0;

  // Clear all discount percentage fields first
  document.getElementById('input_add_add_discpersen1').value = 0;
  document.getElementById('input_add_add_discpersen2').value = 0;
  document.getElementById('input_add_add_discpersen3').value = 0;

  // If harga is 0, we can't calculate percentage
  if (harga === 0) {
    return;
  }

  // Calculate the discount percentage
  let discPercent = (discRp / harga) * 100;

  // Validate that discount doesn't exceed 100%
  if (discPercent > 100) {
    alert("Diskon tidak boleh melebihi harga");
    document.getElementById('input_add_add_discrp').value = '0.00';
    return;
  }

  // Set the first discount percentage field
  document.getElementById('input_add_add_discpersen1').value = discPercent.toFixed(2);
}

function calculateDiscRp() {
  let disc1 = document.getElementById('input_add_add_discpersen1').value
  let disc2 = document.getElementById('input_add_add_discpersen2').value
  let disc3 = document.getElementById('input_add_add_discpersen3').value

  let discRp = formatAngkaVal($('#input_add_add_harga').val())

  disc1 = parseFloat(disc1) || 0
  disc2 = parseFloat(disc2) || 0
  disc3 = parseFloat(disc3) || 0
  discRp = parseFloat(discRp) || 0

  if (disc1 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen1').value = ""
    return
  }
  if (disc2 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen2').value = ""
    return
  }
  if (disc3 > 100) {
    alert("Diskon tidak boleh melebihi angka 100")
    document.getElementById('input_add_add_discpersen3').value = ""
    return
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

  document.getElementById('input_add_add_discrp').value = formatAngka(parseFloat(totalDiscount).toFixed(2))
}

// Dropdown "Outstanding" di form Add Item.
// PR  -> label field nomor jadi "No. PR", FOC dimatikan
// SO  -> label field nomor jadi "No. SO", FOC dimatikan
// FOC -> label dibiarkan apa adanya, flag FOC dinyalakan sehingga
//        Harga & Disc terkunci lewat LockFreeOfCharge().
function onChangeOutstanding () {
  let outstanding = document.getElementById('input_add_add_outstanding').value
  let labelNoso = document.getElementById('labelAddAddNoso')

  if (outstanding == 'PR') {
    labelNoso.textContent = 'No. PR'
    document.getElementById('input_add_add_foc').value = 0
  } else if (outstanding == 'SO') {
    labelNoso.textContent = 'No. SO'
    document.getElementById('input_add_add_foc').value = 0
  } else {
    labelNoso.textContent = 'No. PR'
    document.getElementById('input_add_add_foc').value = 1
  }

  // Seluruh isian item (kode/nama barang, qty, harga, diskon, note, referensi asal
  // barang, riwayat harga terakhir) melekat pada barang yang tadi dipilih dari sumber
  // SEBELUMNYA - begitu sumbernya berganti, semuanya sudah tidak berlaku lagi. Tanpa
  // direset, tampilan jadi tidak masuk akal: dropdown sudah PR tapi kode/nama barang,
  // qty, dan harga masih menunjukkan barang dari SO yang tadi dipilih.
  //
  // Dropdown ini terkunci di mode edit (lihat poKunciIdentitasBarang), jadi reset penuh
  // di sini aman - tidak mungkin terpicu saat sedang mengedit item yang sudah tersimpan.
  cleanFormAddAdd()

  LockFreeOfCharge()
}

// Balikan dari onChangeOutstanding(): dipakai waktu form Add Item dibuka
// atau waktu edit item, supaya dropdown ikut nilai FOC yang sudah ada.
// Kalau `item` diberikan (dipanggil dari buttonAddEditItem), sumbernya ditentukan dari
// data barangnya sendiri: satu baris hasil pogetdetail membawa NoPPL - asal barang, kolom
// dbpodet - sekaligus NOSO, yaitu nomor SO di header PO-nya. Keduanya sama berarti barang
// itu memang ditarik dari SO; selain itu dianggap berasal dari PR.
//
// Ini bukan sekadar soal label: submitAddEdit() memutuskan mengirim Noso/NOPOCUST atau
// tidak berdasarkan dropdown ini, jadi dropdown yang meleset bisa menghapus No. SO di
// header hanya karena sebuah item lama dibuka lalu disimpan ulang.
function syncOutstandingDariFOC (item) {
  let focState = document.getElementById('input_add_add_foc').value
  let outstanding = document.getElementById('input_add_add_outstanding')

  if (focState == 1) {
    outstanding.value = 'FOC'
  } else if (item) {
    outstanding.value = poItemDariSO(item) ? 'SO' : 'PR'
  } else if (outstanding.value == 'FOC') {
    // Tidak ada petunjuk asal barangnya - jatuh ke PR, sama dengan pilihan awal form.
    outstanding.value = 'PR'
  }

  let labelNoso = document.getElementById('labelAddAddNoso')
  labelNoso.textContent = outstanding.value === 'SO' ? 'No. SO' : 'No. PR'
}

function LockFreeOfCharge(){
  let focState = document.getElementById('input_add_add_foc').value

  if (focState == 1){
    document.getElementById('input_add_add_harga').disabled = true;
    document.getElementById('input_add_add_hargaAwal').disabled = true;
    document.getElementById('input_add_add_discrp').disabled = true;
    document.getElementById('input_add_add_discpersen1').disabled = true;
    document.getElementById('input_add_add_discpersen2').disabled = true;
    document.getElementById('input_add_add_discpersen3').disabled = true;

    document.getElementById('input_add_add_harga').value = '0.00' ;
    document.getElementById('input_add_add_hargaAwal').value = 0.00 ;
    document.getElementById('input_add_add_discrp').value = '0.00' ;
    document.getElementById('input_add_add_discpersen1').value = 0 ;
    document.getElementById('input_add_add_discpersen2').value = 0 ;
    document.getElementById('input_add_add_discpersen3').value = 0 ;
  } else {
    document.getElementById('input_add_add_harga').disabled = false;
    poKunciHargaAwal();
    document.getElementById('input_add_add_discrp').disabled = false;
    document.getElementById('input_add_add_discpersen1').disabled = false;
    document.getElementById('input_add_add_discpersen2').disabled = false;
    document.getElementById('input_add_add_discpersen3').disabled = false;
  }

}

</script>
{{-- script buat hover po belum otorisasi dan sudah otorisasi --}}
  <script>
    /* setActiveTab() (mengecat warna tab lewat inline style) DIMATIKAN - dibiarkan sebagai
       arsip di bawah, jangan dihidupkan lagi. Inline style selalu menang melawan stylesheet,
       jadi selama ini aturan .custom-tabs (warna tab aktif/tidak aktif) tidak pernah benar-benar
       berlaku. Skrip ini juga hanya mengenal dua tab (Home/Profile) - begitu tab ketiga
       (Outstanding SO) ditambahkan, warnanya tidak pernah dibersihkan saat pindah tab sehingga
       dua tab bisa tampil biru sekaligus. Sekarang warna tab aktif/tidak aktif sepenuhnya
       diserahkan ke class `active` yang sudah dikelola Bootstrap Tab (bs.tab) + CSS .custom-tabs.

    const tabHome = document.getElementById('nav-home-tab');
    const tabProfile = document.getElementById('nav-profile-tab');
    // const tabProfile1 = document.getElementById('nav-profile1-tab');

    function setActiveTab(homeActive) {
      if (homeActive == 0) {
        tabHome.style.backgroundColor = '#007bff';
        tabHome.style.color = '#fff';
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        // tabProfile1.style.backgroundColor = '#f8f9fa';
        // tabProfile1.style.color = '#007bff';

      } else if (homeActive == 1){
        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        tabProfile.style.backgroundColor = '#007bff';
        tabProfile.style.color = '#fff';

        // tabProfile1.style.backgroundColor = '#f8f9fa';
        // tabProfile1.style.color = '#007bff';
      }
      else if (homeActive == 2){
        tabProfile.style.backgroundColor = '#f8f9fa';
        tabProfile.style.color = '#007bff';

        tabHome.style.backgroundColor = '#f8f9fa';
        tabHome.style.color = '#007bff';

        // tabProfile1.style.backgroundColor = '#007bff';
        // tabProfile1.style.color = '#fff';
      }
    }

    // Default warna tab - Purchase Order aktif duluan saat halaman dibuka
    setActiveTab(1);

    // buat ganti tab
    tabHome.addEventListener('click', function () {
      setActiveTab(0);
    });

    tabProfile.addEventListener('click', function () {
      setActiveTab(1);
    });

    // tabProfile1.addEventListener('click', function () {
    //   setActiveTab(2);
    // });
    */


    function performSearchSupplier () {
      const searchValue = document.getElementById('input_add_kodesupplier').value.trim();

      buttonAddListPelanggan();

      // Apply search to all DataTables
      if ($.fn.DataTable.isDataTable('#tabel_add_list_pelanggan')) {
        $('#tabel_add_list_pelanggan').DataTable().search(searchValue).draw();
      }
    }

    // Keyboard event
    document.getElementById('input_add_kodesupplier').addEventListener('keypress', function(event) {
      if (event.key === 'Enter') {
          event.preventDefault();
          performSearchSupplier();
      }
    });

    // autoPick hanya true kalau dipanggil dari Enter. Menekan tombol browse berarti user
    // memang ingin melihat daftarnya, jadi modal selalu dibuka - tetap terfilter kalau
    // kolom kode barang sudah terisi.
    //
    // Penerapan filter ke DataTables tidak lagi dilakukan di sini: dulu fungsi ini
    // menembak ketiga id tabel picker sekaligus padahal hanya satu yang relevan.
    // Sekarang tiap cabang di buttonAddAddListBarang() yang memfilter tabel miliknya
    // sendiri, sesuai pilihan dropdown "+ Dari".
    function performSearch (autoPick = false) {
      const searchValue = document.getElementById('input_add_add_kodebarang').value.trim();

      if (autoPick && !searchValue) {
        alertify.warning("Silakan ketik kode atau nama barang terlebih dahulu.")
        return
      }

      buttonAddAddListBarang({ keyword: searchValue, autoPick: autoPick });
    }

    // Keyboard event
    document.getElementById('input_add_add_kodebarang').addEventListener('keypress', function(event) {
      if (event.key === 'Enter') {
          event.preventDefault();
          performSearch(true);
      }
    });

    window.onload = function(){
      loadAll();
    };

  </script>

@endsection
