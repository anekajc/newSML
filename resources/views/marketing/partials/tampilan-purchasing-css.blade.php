{{-- Tampilan visual purchasing (purchasing/purchaseOrder.blade.php) untuk semua menu marketing.
     Isinya disalin dari blok CSS purchaseOrder, hanya selektornya dibuat generik (bukan per ID
     tabel) supaya satu file ini berlaku sama persis di setiap halaman marketing:
       - tabel daftar di #page1 : header abu-abu uppercase, baris selang-seling, tanpa garis kolom,
         tombol aksi pastel 30px yang baru muncul saat baris di-hover;
       - form Add/Edit/Detail (#formBsGrid) : input 38px, tombol browse membulat;
       - modal pemilih : gaya modal pemilih menu Kas (picker-kas), klik baris;
       - scrollbar auto-hide, kotak centang diperbesar, tombol soft.
     Di-include di AKHIR @section('css') tiap halaman supaya menimpa aturan lama halaman itu.
     Pasangan JS-nya: marketing/partials/tampilan-purchasing-js.blade.php. --}}
<link rel="stylesheet" href="{!! URL::asset('css/po-table-header.css') !!}?v={{ @filemtime(base_path('public/css/po-table-header.css')) ?: '1' }}">
{{-- Scrollbar auto-hide: tidak terlihat sampai kursor ada di batang scrollbar --}}
<link rel="stylesheet" href="{!! URL::asset('css/scrollbar-autohide.css') !!}?v={{ @filemtime(base_path('public/css/scrollbar-autohide.css')) ?: '1' }}">
{{-- Modal pemilih disamakan dengan modal pemilih menu Kas - lihat public/css/picker-kas.css --}}
<link rel="stylesheet" href="{!! URL::asset('css/picker-kas.css') !!}?v={{ @filemtime(base_path('public/css/picker-kas.css')) ?: '1' }}">
<script>var scwJsPath = "{!! URL::asset('js/canvas') !!}";</script>
<style>
  /* ---------- Form Add/Edit/Detail ---------- */
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

  /* Tombol browse (kaca pembesar) menempel ke input dengan sudut membulat. */
  .btn-chip-biru:has(> .bi-search),
  .btn-browsing:has(> .bi-search) {
    border-radius: 6px !important;
  }
  .form-control:has(+ .btn > .bi-search),
  .form-control:has(+ :is([type=hidden], [hidden]) + .btn > .bi-search),
  .form-control:has(+ .input-group-append > .btn > .bi-search) {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
  }

  /* ---------- Isi tabel sebaris; kolom panjang digeser lewat .po-table-wrap ---------- */
  .po-table-wrap table.dataTable thead th,
  .po-table-wrap table.dataTable tbody td {
    white-space: nowrap;
  }

  /* ---------- Dropdown "Tampilkan" di modal ---------- */
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

  /* ---------- Tab ---------- */
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

  /* Halaman mengisi tinggi layar (lihat mktAturTinggiTabel()), jadi padding atas #content
     dikecilkan supaya tab tidak menggantung jauh dari header. */
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

  /* ---------- Kolom Aksi tabel daftar - tombol bulat kecil warna pastel ----------
     JANGAN display:flex pada <td>: sel jadi tidak ikut vertical-align:middle baris.
     Banyak halaman lama memasang `#tabelX td:first-child { display:flex }` - dikembalikan
     ke table-cell di sini. */
  #page1 table.data-table tbody td:has(> .action-buttons-wrap),
  #page1 table.data-table.po-aksi-hover tbody td:first-child:not(.dataTables_empty) {
    display: table-cell !important;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    font-size: 0;
  }

  #page1 table.data-table tbody .action-buttons-wrap {
    display: inline-flex !important;
    gap: 4px;
    vertical-align: middle;
    justify-content: center;
    align-items: center;
  }

  #page1 table.data-table.po-aksi-hover tbody td:first-child .btn + .btn {
    margin-left: 4px;
  }

  #page1 table.data-table tbody .action-buttons-wrap .btn,
  #page1 table.data-table.po-aksi-hover tbody td:first-child .btn,
  #page2 table tbody td .btn.btn-sm:not(:has(> .bi-search)),
  #page3 table tbody td .btn.btn-sm:not(:has(> .bi-search)) {
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

  #page1 table.data-table tbody .action-buttons-wrap .btn:hover,
  #page1 table.data-table.po-aksi-hover tbody td:first-child .btn:hover,
  #page2 table tbody td .btn.btn-sm:not(:has(> .bi-search)):hover,
  #page3 table tbody td .btn.btn-sm:not(:has(> .bi-search)):hover {
    filter: brightness(0.97);
    transform: translateY(-1px);
  }

  :is(#page1 table.data-table tbody .action-buttons-wrap, #page1 table.data-table.po-aksi-hover tbody td:first-child, #page2 table tbody td, #page3 table tbody td) .btn-success {
    color: #16a34a; border-color: #cdebd7; background: #e7f7ed;
  }

  :is(#page1 table.data-table tbody .action-buttons-wrap, #page1 table.data-table.po-aksi-hover tbody td:first-child, #page2 table tbody td, #page3 table tbody td) .btn-warning {
    color: #b45309; border-color: #fbe3bd; background: #fef3e0;
  }

  :is(#page1 table.data-table tbody .action-buttons-wrap, #page1 table.data-table.po-aksi-hover tbody td:first-child, #page2 table tbody td, #page3 table tbody td) .btn-primary {
    color: #2563eb; border-color: #cfdcff; background: #e8edff;
  }

  :is(#page1 table.data-table tbody .action-buttons-wrap, #page1 table.data-table.po-aksi-hover tbody td:first-child, #page2 table tbody td, #page3 table tbody td) .btn-danger {
    color: #dc2626; border-color: #f7cfcf; background: #fdeaea;
  }

  :is(#page1 table.data-table tbody .action-buttons-wrap, #page1 table.data-table.po-aksi-hover tbody td:first-child, #page2 table tbody td, #page3 table tbody td) .btn-info {
    color: #0891b2; border-color: #a5f3fc; background: #ecfeff;
  }

  :is(#page1 table.data-table tbody .action-buttons-wrap, #page1 table.data-table.po-aksi-hover tbody td:first-child, #page2 table tbody td, #page3 table tbody td) .btn-secondary {
    color: #495057; border-color: #dee2e6; background: #f1f3f5;
  }

  /* Tombol aksi baru muncul saat barisnya di-hover. visibility (bukan display) supaya lebar
     kolomnya tetap dipesan. Sengaja TIDAK memakai :focus-within, sama seperti purchaseOrder. */
  #page1 table.data-table tbody .action-buttons-wrap,
  #page1 table.data-table.po-aksi-hover tbody td:first-child .btn {
    visibility: hidden;
    opacity: 0;
    transform: none !important;
    transition: opacity .12s ease;
  }
  #page1 table.data-table tbody tr:hover .action-buttons-wrap,
  #page1 table.data-table.po-aksi-hover tbody tr:hover td:first-child .btn {
    visibility: visible;
    opacity: 1;
  }

  /* ---------- Header & baris tabel - bersih, uppercase abu-abu, tanpa garis kolom ---------- */
  #page1 table.data-table thead th,
  #page2 table.data-table thead th,
  #page3 table.data-table thead th,
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

  #page1 table.data-table td,
  #page1 table.data-table th {
    border-left: none !important;
    border-right: none !important;
  }
  #page1 table.data-table th {
    border-top: none !important;
  }

  #page1 table.data-table tbody tr:nth-of-type(odd),
  #page2 table.data-table tbody tr:nth-of-type(odd),
  #page3 table.data-table tbody tr:nth-of-type(odd) {
    background-color: #fbfbfc;
  }

  #page1 table.data-table tbody tr:hover,
  #page2 table.data-table tbody tr:hover,
  #page3 table.data-table tbody tr:hover {
    background-color: #f5f3ff;
  }

  /* Tabel histori di form: header sewarna tombol pemicunya (hijau / merah / oranye). */
  #page2 table.data-table.tabel-histori-hijau thead th,
  #tabel_add_harga_terakhir thead th {
    background: #e7f7ed !important;
    color: #16a34a !important;
    border-bottom: 1px solid #cdebd7;
  }
  #page2 table.data-table.tabel-histori-merah thead th,
  #tabel_add_stock_proyeksi thead th {
    background: #fdeaea !important;
    color: #dc2626 !important;
    border-bottom: 1px solid #f7cfcf;
  }
  #tabel_add_harga_beli thead th {
    background: #fef3e0 !important;
    color: #b45309 !important;
    border-bottom: 1px solid #fbe3bd;
  }
  #tabel_add_harga_terakhir tbody tr:hover { background-color: #f2fbf5; }
  #tabel_add_stock_proyeksi tbody tr:hover { background-color: #fef6f6; }
  #tabel_add_harga_beli tbody tr:hover { background-color: #fef8ef; }

  /* ---------- Tombol soft ---------- */
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

  /* Merah soft untuk aksi pembatalan (mis. Batal Otorisasi) - palet sama dengan
     .btn-stock-proyeksi / tombol hapus di tabel. */
  .btn-chip-merah {
    background-color: #fdeaea;
    border-color: #f7cfcf;
    color: #dc2626;
  }

  .btn-chip-merah:hover,
  .btn-chip-merah:focus {
    background-color: #fbdcdc;
    border-color: #f2bcbc;
    color: #b91c1c;
  }

  .btn-chip-merah:active {
    background-color: #f8cfcf !important;
    border-color: #eda9a9 !important;
    color: #b91c1c !important;
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

  /* ---------- Card berisi tabel ---------- */
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

  #page1 .tab-content .col-md-12,
  #page1 .tab-content .col-12 {
    min-width: 0;
    max-width: 100%;
  }

  /* ---------- Dropdown "Tampilkan" di toolbar ---------- */
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

  /* ---------- Lapisan "sedang memuat" tabel server-side ---------- */
  #page1 .dataTables_wrapper {
    position: relative;
    overflow: visible !important;
  }

  #page1 .dataTables_wrapper > .dataTables_processing {
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
    animation: poMunculLoading .34s ease-out both;
  }

  @keyframes poMunculLoading {
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
    animation: poPutarLoading .6s linear infinite;
  }

  @keyframes poPutarLoading {
    to { transform: rotate(360deg); }
  }

  #page1 table.data-table td.dataTables_empty { text-align: center !important; }

  .po-empty-msg {
    text-align: center;
    padding: 26px 12px;
    color: #6b7280;
    font-size: 13px;
  }

  /* Kartu KPI (.po-kpi-*) sengaja TIDAK ditulis di sini: jumlah kolomnya beda per halaman
     (so 3 kartu, purchaseOrder 4), jadi tiap halaman yang punya kartu KPI menulis aturannya sendiri. */

  /* ---------- Kotak centang untuk semua tabel - diperbesar seperti purchasing ---------- */
  td .form-check {
    padding-left: 0;
    margin: 0;
    min-height: 0;
  }
  td input[type="checkbox"],
  th input[type="checkbox"] {
    float: none;
    margin: 0;
    vertical-align: middle;
    position: relative;
    top: -2px;
    width: auto;
    height: auto;
    transform: scale(1.5);
  }

  .dataTables_scrollBody {
    border: none !important;
  }

  .table-responsive {
    border: none !important;
  }

  /* Tabel lebih sempit dari kotaknya ikut melar penuh. [id] supaya klon pengukuran
     DataTables (id-nya dibuang) tidak ikut kena - lihat catatan di purchaseOrder. */
  #page1 table.data-table[id] {
    min-width: 100%;
  }

  .card-header {
    text-align: left !important;
  }

  /* Tombol konfirmasi di footer modal otorisasi: warna soft. */
  .modal .modal-footer .btn-primary#btn-confirm-otorisasi {
    background-color: #e8edff;
    border-color: #cfdcff;
    color: #2563eb;
    border-radius: 8px !important;
    text-transform: none !important;
    box-shadow: none;
  }
  .modal .modal-footer .btn-primary#btn-confirm-otorisasi:hover {
    background-color: #dce6ff;
    border-color: #b9c9ff;
    color: #1d4ed8;
  }
</style>
